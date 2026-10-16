/*
 * The Content-Security-Policy (no 'unsafe-inline') stops style="" attributes in
 * HTML that is inserted at runtime from taking effect. Page content loaded into
 * the Trumbowyg editor is such HTML, so editors would see it without its
 * colours, sizes and indents (the attributes are kept and saved unchanged).
 *
 * This re-applies those styles through the CSSOM, which CSP allows, but only
 * inside .trumbowyg-box: the content being edited (written by signed-in editors,
 * shown with the same styles on the public site) and the editor's own colour and
 * font-size pickers. Nothing outside the editor is touched.
 */
(function () {
	'use strict';

	function restore(el) {
		var css = el.getAttribute('style');
		if (!css || el.style.length) {
			return;
		}
		var parsed = document.createElement('div').style;
		parsed.cssText = css;
		for (var i = 0; i < parsed.length; i++) {
			el.style.setProperty(parsed[i], parsed.getPropertyValue(parsed[i]), parsed.getPropertyPriority(parsed[i]));
		}
	}

	function restoreAll(root) {
		if (root.nodeType !== 1) {
			return;
		}
		if (root.hasAttribute('style')) {
			restore(root);
		}
		Array.prototype.forEach.call(root.querySelectorAll('[style]'), restore);
	}

	function watch(editor) {
		if (editor.getAttribute('data-zed-styles')) {
			return;
		}
		editor.setAttribute('data-zed-styles', '1');
		restoreAll(editor);
		new MutationObserver(function (mutations) {
			mutations.forEach(function (m) {
				if (m.type === 'attributes') {
					restore(m.target);
				}
				Array.prototype.forEach.call(m.addedNodes || [], restoreAll);
			});
		}).observe(editor, { subtree: true, childList: true, attributes: true, attributeFilter: ['style'] });
	}

	function scan() {
		Array.prototype.forEach.call(document.querySelectorAll('.trumbowyg-box'), watch);
	}

	// editors are created by page scripts (on load, or later in tabs/modals)
	document.addEventListener('DOMContentLoaded', scan);
	window.addEventListener('load', scan);
	new MutationObserver(scan).observe(document.documentElement, { childList: true, subtree: true });
})();
