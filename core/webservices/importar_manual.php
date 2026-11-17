<?php
// Replays must name a snapshot on the server; never accept web-triggered resets.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__.'/sage_sync.php';
sage_sync_endpoint('catalog');
