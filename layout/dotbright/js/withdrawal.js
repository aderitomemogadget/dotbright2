// Keep the declaration in place when crossing the legacy desktop/mobile breakpoint.
// The shared site.js otherwise redirects every page to index.php on resize.
(function () {
    'use strict';
    window.addEventListener('resize', function () {
        if (typeof window.is_mobile !== 'undefined') {
            window.is_mobile = window.innerWidth <= 1024 ? 1 : 0;
        }
    }, true);
}());
