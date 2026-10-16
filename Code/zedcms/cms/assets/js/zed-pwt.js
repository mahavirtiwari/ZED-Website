/*
 * Encrypts password fields before a form is posted, so passwords never travel
 * in clear text in the request body (see application/libraries/Password_transport.php).
 *
 * The page carries <meta name="zed-pwt-key"> (RSA public key, SPKI base64) and
 * <meta name="zed-pwt-nonce">. On submit, each filled password field is sent as
 * "pwt1:" + base64(RSA-OAEP-SHA1("nonce:password")) in a hidden field of the same
 * name; the visible field is not sent. Runs after the form's own validation
 * (listener on document), and does nothing if that validation cancelled the submit.
 */
(function () {
	'use strict';

	var PREFIX = 'pwt1:';
	var keyPromise = null;

	function meta(name) {
		var m = document.querySelector('meta[name="' + name + '"]');
		return m ? m.getAttribute('content') : '';
	}

	function toBuffer(b64) {
		var s = atob(b64), bytes = new Uint8Array(s.length);
		for (var i = 0; i < s.length; i++) {
			bytes[i] = s.charCodeAt(i);
		}
		return bytes.buffer;
	}

	function toBase64(buffer) {
		var bytes = new Uint8Array(buffer), s = '';
		for (var i = 0; i < bytes.length; i++) {
			s += String.fromCharCode(bytes[i]);
		}
		return btoa(s);
	}

	function publicKey() {
		if (!keyPromise) {
			keyPromise = window.crypto.subtle.importKey('spki', toBuffer(meta('zed-pwt-key')),
				{ name: 'RSA-OAEP', hash: 'SHA-1' }, false, ['encrypt']);
		}
		return keyPromise;
	}

	function hiddenInput(form, name, value) {
		var input = document.createElement('input');
		input.type = 'hidden';
		input.name = name;
		input.value = value;
		input.setAttribute('data-pwt-added', '1');
		form.appendChild(input);
	}

	document.addEventListener('submit', function (event) {
		var form = event.target;
		if (event.defaultPrevented || !form || form.getAttribute('data-pwt-done')) {
			return;
		}
		var fields = Array.prototype.filter.call(form.querySelectorAll('input[type="password"]'), function (f) {
			return f.name && f.value && !f.disabled;
		});
		// no WebCrypto (plain-HTTP page) or no key: post as before
		if (!fields.length || !window.crypto || !window.crypto.subtle || !meta('zed-pwt-key')) {
			return;
		}
		event.preventDefault();

		var nonce = meta('zed-pwt-nonce');
		var encoder = new TextEncoder();
		var submitter = event.submitter;

		publicKey().then(function (key) {
			return Promise.all(fields.map(function (f) {
				return window.crypto.subtle.encrypt({ name: 'RSA-OAEP' }, key, encoder.encode(nonce + ':' + f.value));
			}));
		}).then(function (encrypted) {
			fields.forEach(function (f, i) {
				hiddenInput(form, f.name, PREFIX + toBase64(encrypted[i]));
				f.setAttribute('data-pwt-name', f.name);
				f.removeAttribute('name');
			});
			// form.submit() does not send the clicked button, so send its name/value ourselves
			if (submitter && submitter.name) {
				hiddenInput(form, submitter.name, submitter.value);
			}
			form.setAttribute('data-pwt-done', '1');
			HTMLFormElement.prototype.submit.call(form);
		}).catch(function () {
			window.alert('The password could not be secured for sending. Please reload the page and try again.');
		});
	});

	// coming back with the Back button: restore the form as it was
	window.addEventListener('pageshow', function () {
		Array.prototype.forEach.call(document.querySelectorAll('[data-pwt-added]'), function (el) {
			el.parentNode.removeChild(el);
		});
		Array.prototype.forEach.call(document.querySelectorAll('[data-pwt-name]'), function (f) {
			f.name = f.getAttribute('data-pwt-name');
			f.removeAttribute('data-pwt-name');
			f.value = '';
		});
		Array.prototype.forEach.call(document.querySelectorAll('form[data-pwt-done]'), function (f) {
			f.removeAttribute('data-pwt-done');
		});
	});
})();
