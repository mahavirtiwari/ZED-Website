/*
 * Show / hide button (eye) on every password field of the CMS.
 *
 * Revealed fields keep data-password, so public/js/zed-pwt.js still encrypts
 * them; they are also switched back to type="password" when the form is sent.
 * No inline code or styles (strict Content-Security-Policy).
 */
(function () {
    'use strict';

    function setVisible(input, button, visible) {
        input.type = visible ? 'text' : 'password';
        button.setAttribute('aria-pressed', visible ? 'true' : 'false');
        button.setAttribute('aria-label', visible ? 'Hide password' : 'Show password');
        button.title = visible ? 'Hide password' : 'Show password';
        button.firstChild.className = visible ? 'bi bi-eye-slash' : 'bi bi-eye';
    }

    function enhance(input) {
        if (input.hasAttribute('data-password')) {
            return;
        }
        input.setAttribute('data-password', '');

        var group = document.createElement('div');
        group.className = 'input-group password-group';
        input.parentNode.insertBefore(group, input);
        group.appendChild(input);

        var button = document.createElement('button');
        button.type = 'button';
        button.className = 'btn btn-outline-secondary password-toggle';
        button.setAttribute('aria-controls', input.id || '');
        button.appendChild(document.createElement('i'));
        group.appendChild(button);
        setVisible(input, button, false);

        button.addEventListener('click', function () {
            setVisible(input, button, input.type === 'password');
            input.focus();
        });
        // never leave a password readable after sending the form
        if (input.form) {
            input.form.addEventListener('submit', function () {
                setVisible(input, button, false);
            }, true);
        }
    }

    document.querySelectorAll('input[type="password"]').forEach(enhance);
})();
