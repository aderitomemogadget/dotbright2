<?php
// Additional public images under files/. Product settings remain independent.
if (!defined('SHARED_FILES_IMAGES')) define('SHARED_FILES_IMAGES', true);
// Optional verified production files/ directory. Empty uses read-only HTTPS.
if (!defined('FILES_IMAGES_SOURCE_PATH')) define('FILES_IMAGES_SOURCE_PATH', '');
if (!defined('FILES_IMAGES_SOURCE_URL')) define('FILES_IMAGES_SOURCE_URL', 'https://www.dotbright.pt/files/');
