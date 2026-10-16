/*
 * Bhashini website translation plugin (language menu in the header, as on
 * zed.msme.gov.in). CMS > Settings > Header and footer turns it on or off.
 *
 * The plugin is loaded from translation-plugin.bhashini.co.in after the page
 * has loaded. It styles its menu and dialogs with a <style> element and with
 * style="" attributes, which the strict Content-Security-Policy blocks. So
 * before it loads, this file makes those styles go through allowed routes:
 * - <style> elements added by scripts get this page's nonce;
 * - setAttribute("style", ...) is applied through CSSOM (element.style);
 * - style="" in HTML assigned to innerHTML is taken out before the browser
 *   parses it and applied through CSSOM afterwards.
 * No 'unsafe-inline' is needed and the browser reports no CSP violations.
 */
(function () {
    'use strict';

    var me = document.currentScript;
    var src = me && me.getAttribute('data-src');
    if (!src) {
        return;
    }
    var nonce = me.nonce || '';

    function nonceStyle(node) {
        if (node && node.nodeName === 'STYLE' && !node.nonce && nonce) {
            node.nonce = nonce;
        }
    }

    ['appendChild', 'insertBefore'].forEach(function (method) {
        var original = Node.prototype[method];
        Node.prototype[method] = function (node) {
            nonceStyle(node);
            return original.apply(this, arguments);
        };
    });

    var setAttribute = Element.prototype.setAttribute;
    Element.prototype.setAttribute = function (name, value) {
        if (String(name).toLowerCase() === 'style' && this.style) {
            this.style.cssText = String(value);
            return;
        }
        return setAttribute.call(this, name, value);
    };

    // The browser checks style="" while parsing (in any document), so rename the
    // attribute in the HTML text first and apply the values through CSSOM.
    var styleAttr = /(<[a-z][^<>]*?\s)style(\s*=\s*(?:"[^"]*"|'[^']*'))/gi;
    var innerHTML = Object.getOwnPropertyDescriptor(Element.prototype, 'innerHTML');
    Object.defineProperty(Element.prototype, 'innerHTML', {
        configurable: true,
        enumerable: innerHTML.enumerable,
        get: innerHTML.get,
        set: function (html) {
            html = String(html);
            var renamed = html.replace(styleAttr, '$1data-zed-style$2');
            innerHTML.set.call(this, renamed);
            if (renamed === html) {
                return;
            }
            this.querySelectorAll('[data-zed-style]').forEach(function (el) {
                if (el.style) {
                    el.style.cssText = el.getAttribute('data-zed-style');
                }
                el.removeAttribute('data-zed-style');
            });
        }
    });

    window.addEventListener('load', function () {
        var script = document.createElement('script');
        script.src = src;
        script.async = true;
        if (nonce) {
            script.nonce = nonce;
        }
        document.body.appendChild(script);
    });
})();
