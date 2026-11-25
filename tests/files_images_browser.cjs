// node tests/files_images_browser.cjs
// Exercise the shipped script with DOM/CSSOM events, without network or production writes.
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');
const assert = require('node:assert/strict');
const base = 'https://dev.example/dotbright2/';
const source = 'https://www.dotbright.pt/files/';
const endpoint = base + 'shared-file-image.php';
const fallback = base + 'layout/dotbright/images/no_photo.png';
let checks = 0;
function check(value, label) { assert.ok(value, label); checks++; console.log('PASS ' + label); }
function style(values) {
    const names = Object.keys(values);
    return Object.assign({length: names.length, getPropertyValue: key => values[key], getPropertyPriority: () => '',
        setProperty: (key, value) => { values[key] = value; }}, names);
}
function image(src, complete = false) {
    const attrs = {src};
    return {tagName: 'IMG', nodeType: 1, complete, naturalWidth: complete ? 1 : 0, parentElement: null,
        get src() { return new URL(attrs.src, base).href; },
        set src(value) { attrs.src = value; this.complete = false; },
        get currentSrc() { return this.src; },
        getAttribute: key => attrs[key] || null,
        setAttribute: (key, value) => { attrs[key] = value; },
        removeAttribute: key => { delete attrs[key]; }};
}
const events = {};
const documentRoot = {nodeType: 1, tagName: 'HTML', querySelectorAll: () => [localSuccess]};
const localSuccess = image('files/menus/local.png', true);
const css = {background: 'url(documentos/banner.png)', src: 'url(fonts/site.woff)'};
const sheet = {href: base + 'files/classes.css', cssRules: [{style: style(css)}]};
const foreignSheet = {href: 'https://foreign.example/theme.css', get cssRules() { throw new Error('SecurityError'); }};
let mutationCallback;
class MutationObserver { constructor(callback) { mutationCallback = callback; } observe() {} }
const attrs = {'data-files-root': base + 'files/', 'data-files-source': source,
    'data-image-endpoint': endpoint, 'data-image-fallback': fallback};
const document = {baseURI: base, currentScript: {getAttribute: key => attrs[key]}, readyState: 'complete',
    documentElement: documentRoot, styleSheets: [sheet, foreignSheet],
    addEventListener: (event, handler) => { events[event] = handler; }};
vm.runInNewContext(fs.readFileSync(path.join(__dirname, '../layout/dotbright/js/shared-files-images.js'), 'utf8'),
    {document, window: {URL, MutationObserver}, URL, MutationObserver, WeakSet});
function error(img) {
    let stopped = false;
    events.error({target: img, stopImmediatePropagation: () => { stopped = true; }});
    return stopped;
}
function routedName(url) { const value = new URL(url); return value.origin + value.pathname === endpoint ? value.searchParams.get('image') : null; }
check(localSuccess.src === base + 'files/menus/local.png', 'existing local images load without redirects');
check(routedName(css.background.match(/url\("([^"]+)"\)/)[1]) === 'files/documentos/banner.png', 'external CSS resolves paths relative to stylesheet');
check(css.src === 'url(fonts/site.woff)', 'font URLs unchanged');
const lazy = image('files/menus/icon.svg?dp=7#symbol');
check(error(lazy), 'capture missing image before old fallback handlers');
check(routedName(lazy.src) === 'files/menus/icon.svg?dp=7#symbol', 'dynamic SVG image and cache parameters');
check(error(lazy) && lazy.src === fallback, 'missing shared image uses no-photo');
error(lazy);
check(lazy.src === fallback, 'fallback cannot loop');
const publicRoot = image('/files/brands/logo.webp'); error(publicRoot);
check(routedName(publicRoot.src) === 'files/brands/logo.webp', 'root-relative image paths');
const legacyRelative = image('../../files/products_components/1_2.svg'); error(legacyRelative);
check(routedName(legacyRelative.src) === 'files/products_components/1_2.svg', 'legacy parent-relative paths');
const pdf = image('files/documentos/private.pdf');
check(!error(pdf) && pdf.src === base + 'files/documentos/private.pdf', 'PDFs are never routed');
const foreign = image('https://foreign.example/files/icon.png');
check(!error(foreign) && foreign.src === 'https://foreign.example/files/icon.png', 'foreign URLs unchanged');
const brokenPublic = image(source + 'menus/icon.png'); error(brokenPublic);
check(brokenPublic.src === fallback, 'broken previously resolved source has a fallback');
const picture = image('files/menus/fallback.png');
picture.setAttribute('srcset', 'files/menus/large.png 2x');
let removedSource = false;
picture.parentElement = {tagName: 'PICTURE', querySelectorAll: () => [{removeAttribute: () => { removedSource = true; }}]};
error(picture);
check(!picture.getAttribute('srcset') && removedSource, 'responsive sources cannot override image retry');
const dynamicStyle = {background: 'url(files/menus/icon.png)', cursor: 'url(files/documentos/private.pdf)'};
const dynamicNode = {nodeType: 1, tagName: 'DIV', style: style(dynamicStyle), querySelectorAll: () => []};
mutationCallback([{type: 'childList', addedNodes: [dynamicNode], target: documentRoot}]);
check(dynamicStyle.background.includes(endpoint), 'AJAX-inserted background images');
check(dynamicStyle.cursor === 'url(files/documentos/private.pdf)', 'non-image CSS resources unchanged');
const svgAttrs = {href: 'files/menus/sprite.svg#symbol'};
const svg = {nodeType: 1, tagName: 'image', getAttribute: key => svgAttrs[key] || null,
    setAttribute: (key, value) => { svgAttrs[key] = value; }, querySelectorAll: () => []};
mutationCallback([{type: 'childList', addedNodes: [svg], target: documentRoot}]);
check(routedName(svgAttrs.href) === 'files/menus/sprite.svg#symbol', 'dynamically inserted SVG image references');
console.log(checks + ' browser-script checks passed');
