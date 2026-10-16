/*
 * Captcha refresh: a button with data-captcha-refresh="<img id>" loads a new
 * image (each image request makes a new code on the server) and clears the
 * answer field named in data-captcha-input. Used by the public site and the CMS.
 */
(function () {
	'use strict';
	document.addEventListener('click', function (event) {
		var button = event.target.closest ? event.target.closest('[data-captcha-refresh]') : null;
		if (!button) {
			return;
		}
		event.preventDefault();
		var img = document.getElementById(button.getAttribute('data-captcha-refresh'));
		if (img) {
			img.src = img.src.replace(/([?&])r=[^&]*/, '$1r=' + Date.now());
		}
		var input = document.getElementById(button.getAttribute('data-captcha-input'));
		if (input) {
			input.value = '';
			input.focus();
		}
	});
})();
