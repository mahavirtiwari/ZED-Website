<?php

namespace App\Support;

use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Log;

/**
 * Keeps passwords out of request bodies in clear text (security audit item 8,
 * CWE-319).
 *
 * Each session gets its own RSA-2048 key pair. The layout prints the public key
 * and a nonce as <meta> tags (see @pwtMeta / resources/views/cms/partials);
 * public/js/zed-pwt.js encrypts each password field as RSA-OAEP(SHA-1) of
 * "nonce:password" and posts "pwt1:<base64>" instead. The DecryptPasswords
 * middleware puts the clear value back into the request before validation.
 * The nonce is single use, so a captured value cannot be replayed.
 * Passwords are still stored as bcrypt hashes. HTTPS remains required.
 */
class PasswordTransport
{
    public const PREFIX = 'pwt1:';

    public function __construct(private Session $session) {}

    private function opensslOptions(): array
    {
        return [
            // PHP on Windows has no default openssl.cnf
            'config' => resource_path('openssl.cnf'),
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ];
    }

    /** Base64 SPKI public key for this session; '' if OpenSSL cannot make one. */
    public function publicKey(): string
    {
        $public = $this->session->get('pwt.public');
        if (! $public || ! $this->session->get('pwt.private')) {
            $key = openssl_pkey_new($this->opensslOptions());
            if ($key === false || ! openssl_pkey_export($key, $private, null, $this->opensslOptions())) {
                Log::error('PasswordTransport: RSA key generation failed: '.openssl_error_string());

                return '';
            }
            $public = openssl_pkey_get_details($key)['key'];
            $this->session->put('pwt.private', $private);
            $this->session->put('pwt.public', $public);
        }

        return preg_replace('/-----[^-]+-----|\s+/', '', $public);
    }

    public function nonce(): string
    {
        $nonce = $this->session->get('pwt.nonce');
        if (! $nonce) {
            $nonce = bin2hex(random_bytes(16));
            $this->session->put('pwt.nonce', $nonce);
        }

        return $nonce;
    }

    /**
     * Decrypt one posted value. Returns null when the value was not encrypted,
     * '' when it was but does not decrypt (wrong key, wrong or used nonce).
     */
    public function decrypt(mixed $value): ?string
    {
        if (! is_string($value) || ! str_starts_with($value, self::PREFIX)) {
            return null;
        }
        $private = $this->session->get('pwt.private');
        $nonce = $this->session->get('pwt.nonce');
        $cipher = base64_decode(substr($value, strlen(self::PREFIX)), true);
        if (! $private || ! $nonce || $cipher === false
            || ! openssl_private_decrypt($cipher, $plain, $private, OPENSSL_PKCS1_OAEP_PADDING)) {
            return '';
        }
        $sep = strpos($plain, ':');

        return ($sep !== false && hash_equals($nonce, substr($plain, 0, $sep))) ? substr($plain, $sep + 1) : '';
    }

    /** New nonce after it has been used. */
    public function rotateNonce(): void
    {
        $this->session->put('pwt.nonce', bin2hex(random_bytes(16)));
    }
}
