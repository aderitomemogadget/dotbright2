(function () {
    'use strict';
    var script = document.currentScript;
    if (!script || !window.URL || !window.MutationObserver) return;
    var filesRoot = new URL(script.getAttribute('data-files-root'), document.baseURI);
    var sourceRoot = new URL(script.getAttribute('data-files-source'), document.baseURI);
    var endpoint = new URL(script.getAttribute('data-image-endpoint'), document.baseURI);
    var fallback = new URL(script.getAttribute('data-image-fallback'), document.baseURI).href;
    var attempted = new WeakSet();
    var stylesheets = new WeakSet();
    var imageExtension = /\.(?:jpe?g|png|webp|gif|avif|svg|ico|bmp)$/i;

    function imageRoute(raw, base) {
        if (!raw || /^(?:data|blob|javascript):/i.test(raw)) return null;
        var url;
        try { url = new URL(raw, base || document.baseURI); } catch (error) { return null; }
        if (url.href.indexOf(endpoint.href + '?') === 0) return null;
        var name;
        if (url.origin === filesRoot.origin && url.pathname.indexOf(filesRoot.pathname) === 0) {
            name = url.pathname.slice(filesRoot.pathname.length);
        } else if (url.origin === filesRoot.origin && url.pathname.indexOf('/files/') === 0) {
            name = url.pathname.slice(7);
        } else if (url.origin === sourceRoot.origin && url.pathname.indexOf(sourceRoot.pathname) === 0) {
            // URLs already resolved by PHP need no extra redirect.
            return null;
        } else if (/^(?:\.\.?\/)*files\//i.test(raw)) {
            name = raw.replace(/^(?:\.\.?\/)*files\//i, '').split(/[?#]/)[0];
        } else return null;
        try { name = decodeURIComponent(name); } catch (error) { return null; }
        if (!imageExtension.test(name) || /[\x00-\x1f\x7f%\\:]/.test(name)
            || name.split('/').some(function (part) { return !part || part === '.' || part === '..'; })) return null;
        return endpoint.href + '?image=' + encodeURIComponent('files/' + name + url.search + url.hash);
    }

    function cssImages(value, base) {
        return value.replace(/url\(\s*(?:"([^"]*)"|'([^']*)'|([^\s)]*))\s*\)/gi, function (whole, doubleQuoted, singleQuoted, bare) {
            var mapped = imageRoute(doubleQuoted || singleQuoted || bare, base);
            return mapped ? 'url("' + mapped + '")' : whole;
        });
    }

    function fixStyle(style, base) {
        if (!style) return;
        for (var i = 0; i < style.length; i++) {
            var property = style[i];
            var value = style.getPropertyValue(property);
            if (value.indexOf('url(') < 0) continue;
            var resolved = cssImages(value, base);
            if (resolved !== value) style.setProperty(property, resolved, style.getPropertyPriority(property));
        }
    }

    function fixRules(rules, base) {
        for (var i = 0; i < rules.length; i++) {
            var rule = rules[i];
            fixStyle(rule.style, base);
            if (rule.cssRules) fixRules(rule.cssRules, base);
            if (rule.styleSheet) fixSheet(rule.styleSheet);
        }
    }

    function fixSheet(sheet) {
        if (!sheet || stylesheets.has(sheet)) return;
        stylesheets.add(sheet);
        try { fixRules(sheet.cssRules, sheet.href || document.baseURI); } catch (error) {
            // Cross-origin stylesheets are not readable; their foreign assets stay unchanged.
        }
    }

    function retryImage(img) {
        if (attempted.has(img)) {
            if (img.src !== fallback) { img.removeAttribute('srcset'); img.src = fallback; }
            return true;
        }
        var route = imageRoute(img.currentSrc || img.getAttribute('src'));
        if (!route) {
            var current;
            try { current = new URL(img.currentSrc || img.getAttribute('src'), document.baseURI); } catch (error) { return false; }
            if (current.origin !== sourceRoot.origin || current.pathname.indexOf(sourceRoot.pathname) !== 0
                || !imageExtension.test(current.pathname)) return false;
            route = fallback;
        }
        attempted.add(img);
        img.removeAttribute('srcset');
        if (img.parentElement && img.parentElement.tagName === 'PICTURE') {
            img.parentElement.querySelectorAll('source').forEach(function (source) { source.removeAttribute('srcset'); });
        }
        img.src = route;
        return true;
    }

    // Local images load normally. Retry only failures through the read-only resolver.
    document.addEventListener('error', function (event) {
        if (event.target && event.target.tagName === 'IMG' && retryImage(event.target)) {
            // Existing product error handlers must not overwrite an in-progress retry.
            event.stopImmediatePropagation();
        }
    }, true);
    document.addEventListener('load', function (event) {
        if (event.target && (event.target.tagName === 'LINK' || event.target.tagName === 'STYLE')) fixSheet(event.target.sheet);
    }, true);

    function inspect(element) {
        if (!element || element.nodeType !== 1) return;
        fixStyle(element.style, document.baseURI);
        if (element.tagName === 'STYLE' || element.tagName === 'LINK') fixSheet(element.sheet);
        if (element.tagName === 'IMG' && element.complete && element.naturalWidth === 0) retryImage(element);
        // SVG <image> and video posters do not emit an IMG error event.
        if (element.tagName.toLowerCase() === 'image' || element.tagName === 'VIDEO') {
            ['href', 'xlink:href', 'poster'].forEach(function (attr) {
                var value = element.getAttribute(attr);
                var mapped = imageRoute(value);
                if (mapped) element.setAttribute(attr, mapped);
            });
        }
    }

    function inspectTree(root) {
        inspect(root);
        if (root.querySelectorAll) root.querySelectorAll('img, image, video, [style], style, link[rel="stylesheet"]').forEach(inspect);
    }

    function start() {
        inspectTree(document.documentElement);
        Array.prototype.forEach.call(document.styleSheets, fixSheet);
        new MutationObserver(function (records) {
            records.forEach(function (record) {
                if (record.type === 'attributes') {
                    if (record.target.tagName === 'IMG' && (record.attributeName === 'src' || record.attributeName === 'srcset')
                        && imageRoute(record.target.getAttribute('src'))) attempted.delete(record.target);
                    inspect(record.target);
                } else {
                    record.addedNodes.forEach(inspectTree);
                    if (record.target.nodeType === 1 && record.target.tagName === 'STYLE') {
                        stylesheets.delete(record.target.sheet); fixSheet(record.target.sheet);
                    }
                }
            });
        }).observe(document.documentElement, {subtree: true, childList: true, attributes: true,
            attributeFilter: ['src', 'srcset', 'style', 'href', 'xlink:href', 'poster']});
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start);
    else start();
}());
