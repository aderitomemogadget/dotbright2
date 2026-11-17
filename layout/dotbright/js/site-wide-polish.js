(function () {
  'use strict';

  var fallbackImage = 'layout/dotbright/images/no_photo_prd.png';

  function applyProductImageFallbacks(root) {
    var scope = root && root.querySelectorAll ? root : document;
    var images = scope.querySelectorAll('.prd_d .prd_img img');

    Array.prototype.forEach.call(images, function (image) {
      if (image.dataset.dbFallbackReady === '1') return;
      image.dataset.dbFallbackReady = '1';

      function useFallback() {
        if (image.dataset.dbFallbackApplied === '1') return;
        image.dataset.dbFallbackApplied = '1';
        image.src = fallbackImage;
        image.setAttribute('data-original', fallbackImage);
        image.alt = 'Imagem do produto indisponível';
        image.classList.add('db-image-placeholder');
      }

      if (!image.getAttribute('src') && !image.getAttribute('data-original')) {
        useFallback();
      } else {
        image.addEventListener('error', useFallback, { once: true });
      }
    });
  }

  function initialise() {
    applyProductImageFallbacks(document);

    if ('MutationObserver' in window) {
      var observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutation) {
          Array.prototype.forEach.call(mutation.addedNodes, function (node) {
            if (node.nodeType !== 1) return;
            if (node.matches && node.matches('.prd_d .prd_img img')) {
              applyProductImageFallbacks(node.parentNode || document);
            } else {
              applyProductImageFallbacks(node);
            }
          });
        });
      });
      observer.observe(document.body, { childList: true, subtree: true });
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initialise);
  } else {
    initialise();
  }
}());
