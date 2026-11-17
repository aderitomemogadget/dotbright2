<?php
// Installation metadata only. Use existing private credentials.
define('STORE_PRIVACY_ROOT',"/home/www/dotbright.pt/public_html");
define('STORE_PRIVACY_SITE_URL',"https://www.dotbright.pt/");
define('STORE_PRIVACY_BRAND',"Dotbright");
define('STORE_PRIVACY_ACCOUNT_PAGE',5);
define('STORE_PRIVACY_NEWSLETTER_TABLE',true);
define('STORE_PRIVACY_NEWSLETTER_GROUP',0);
define('STORE_PRIVACY_LEGACY_RGPD',false);
function store_privacy_account_url(){return STORE_PRIVACY_SITE_URL.'pages.php?page_id='.STORE_PRIVACY_ACCOUNT_PAGE;}
define('STORE_PRIVACY_MOBILE_BREAKPOINT',1024);
