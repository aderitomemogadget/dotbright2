<?php
// Copiar no servidor, fora da raiz publica, para:
// /home/www/dotbright.pt/private/sequra_secrets.php
// Usar primeiro as credenciais e o endpoint de sandbox fornecidos pela seQura.
$GLOBALS['SEQURA_ENABLED'] = 0;
$GLOBALS['SEQURA_SANDBOX'] = 1;
$GLOBALS['SEQURA_MERCHANT_ID'] = '';
$GLOBALS['SEQURA_USERNAME'] = '';
$GLOBALS['SEQURA_PASSWORD'] = '';
$GLOBALS['SEQURA_ENDPOINT'] = '';
$GLOBALS['SEQURA_ASSETS_KEY'] = '';
$GLOBALS['SEQURA_PRODUCT'] = 'pp3';
$GLOBALS['SEQURA_CULTURE'] = 'pt-PT';
$GLOBALS['SEQURA_ORDER_PREFIX'] = 'DOTBRIGHT';
$GLOBALS['SEQURA_TOKEN_SECRET'] = ''; // minimo 32 bytes aleatorios
$GLOBALS['SEQURA_SIGNATURE_SECRET'] = ''; // segredo HMAC dos callbacks seQura
$GLOBALS['SEQURA_PAID_STATE'] = '13';
$GLOBALS['SEQURA_PENDING_STATE'] = '5';
$GLOBALS['SEQURA_FAILED_STATE'] = '14';
$GLOBALS['SEQURA_CANCELLED_STATE'] = '15';
