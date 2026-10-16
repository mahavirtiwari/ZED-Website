<?php

namespace Tests\Feature;

use App\Support\Captcha;
use App\Support\HtmlHardener;
use App\Support\PasswordTransport;
use Illuminate\Support\Facades\Vite;
use Tests\TestCase;

/**
 * Security audit fixes (Verification Report No. I). No database needed:
 * run with "php artisan test".
 */
class SecurityTest extends TestCase
{
    public function test_security_headers_and_strict_csp(): void
    {
        $response = $this->get('/up');

        $csp = (string) $response->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("script-src 'self' 'nonce-", $csp);
        $this->assertStringContainsString("style-src 'self' 'nonce-", $csp);
        $this->assertStringNotContainsString('unsafe-inline', $csp);
        $this->assertStringNotContainsString('unsafe-eval', $csp);
        $this->assertStringContainsString("frame-ancestors 'self'", $csp);
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->assertFalse($response->headers->has('X-Powered-By'));
    }

    public function test_html_hardener_moves_inline_code_out_of_attributes(): void
    {
        Vite::useCspNonce('test-nonce');
        $html = '<html><head><title>t</title><script>var a = 1;</script></head><body>'
            .'<p style="color:red" onclick="return go(this)">Write to zed.support@qcin.org</p>'
            .'<a href="javascript:void(0);">x</a><a href="mailto:a@b.in">a@b.in</a>'
            .'<form><input type="password" name="p"></form></body></html>';

        $out = (new HtmlHardener(obfuscateEmails: true, autocompleteOff: true))->harden($html);

        $this->assertStringContainsString('<script nonce="test-nonce">var a = 1;</script>', $out);
        $this->assertDoesNotMatchRegularExpression('/\s(style|onclick)=/', $out);
        $this->assertStringNotContainsString('javascript:', $out);
        $this->assertStringNotContainsString('mailto:', $out);
        $this->assertStringContainsString('zed[dot]support[at]qcin[dot]org', $out);
        $this->assertStringContainsString('<style nonce="test-nonce">', $out);
        $this->assertStringContainsString('<input type="password" name="p" autocomplete="off">', $out);
        $this->assertStringContainsString('return go(this)', $out); // moved into the nonced binder script
    }

    public function test_captcha_is_six_characters_single_use_and_case_insensitive(): void
    {
        $captcha = new Captcha($this->app['session.store']);

        $code = $captcha->generate('contact');
        $this->assertMatchesRegularExpression('/^['.Captcha::CHARS.']{6}$/', $code);
        $this->assertTrue($captcha->check('contact', strtolower($code)));
        $this->assertFalse($captcha->check('contact', $code), 'a code works only once');

        $captcha->generate('login');
        $this->assertFalse($captcha->check('login', 'WRONG1'));
        $this->assertFalse($captcha->check('login', 'WRONG1'), 'a wrong answer also discards the code');
        $this->assertStringStartsWith("\x89PNG", Captcha::png('ABC234'));
    }

    public function test_password_transport_decrypts_once_with_the_session_nonce(): void
    {
        $transport = new PasswordTransport($this->app['session.store']);
        $pem = "-----BEGIN PUBLIC KEY-----\n".chunk_split($transport->publicKey(), 64, "\n")."-----END PUBLIC KEY-----\n";
        openssl_public_encrypt($transport->nonce().':S3cret!pass', $cipher, $pem, OPENSSL_PKCS1_OAEP_PADDING);
        $posted = PasswordTransport::PREFIX.base64_encode($cipher);

        $this->assertNull($transport->decrypt('plain'), 'values without the prefix are left alone');
        $this->assertSame('S3cret!pass', $transport->decrypt($posted));
        $transport->rotateNonce();
        $this->assertSame('', $transport->decrypt($posted), 'a captured value cannot be replayed');
        $this->assertSame('', $transport->decrypt(PasswordTransport::PREFIX.'bm90LXJzYQ=='));
    }
}
