<?php

  $_GET ='';
  $_POST ='';
  $_REQUEST ='';

  unset($_GET);
  unset($_POST);
  unset($_REQUEST);


  ?><!doctype html>
<html amp lang="<?php echo $GLOBALS['site_lang']?>">
  <head>
    <meta charset="utf-8">
    <title><?php echo (isset($page_info) && $page_info['title'] !='' ? $page_info['title'] : $SEO_title); ?></title><?
    if($AMP_Canonical) $canonical=$AMP_Canonical;

	 $canonical=str_replace('amp','p',$https.$_SERVER['HTTP_HOST'].parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ));
	?>
    <link rel="canonical" href="<?php  echo $canonical; ?>" />
    <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
    <link rel="shortcut icon" href="files/icons/favicon_v_<?php echo filemtime('files/icons/favicon.ico')?>.ico" type="image/x-icon"/>
    <link rel="apple-touch-icon" sizes="57x57"   href="files/icons/apple-icon-57x57_v_<?php echo filemtime('files/icons/apple-icon-57x57.png')?>.png">
	<link rel="apple-touch-icon" sizes="60x60"   href="files/icons/apple-icon-60x60_v_<?php echo filemtime('files/icons/apple-icon-60x60.png')?>.png">
	<link rel="apple-touch-icon" sizes="72x72"   href="files/icons/apple-icon-72x72_v_<?php echo filemtime('files/icons/apple-icon-72x72.png')?>.png">
	<link rel="apple-touch-icon" sizes="76x76"   href="files/icons/apple-icon-76x76_v_<?php echo filemtime('files/icons/apple-icon-76x76.png')?>.png">
	<link rel="apple-touch-icon" sizes="114x114" href="files/icons/apple-icon-114x114_v_<?php echo filemtime('files/icons/apple-icon-114x114.png')?>.png">
	<link rel="apple-touch-icon" sizes="120x120" href="files/icons/apple-icon-120x120_v_<?php echo filemtime('files/icons/apple-icon-120x120.png')?>.png">
	<link rel="apple-touch-icon" sizes="144x144" href="files/icons/apple-icon-144x144_v_<?php echo filemtime('files/icons/apple-icon-144x144.png')?>.png">
	<link rel="apple-touch-icon" sizes="152x152" href="files/icons/apple-icon-152x152_v_<?php echo filemtime('files/icons/apple-icon-152x152.png')?>.png">
	<link rel="apple-touch-icon" sizes="180x180" href="files/icons/apple-icon-180x180_v_<?php echo filemtime('files/icons/apple-icon-180x180.png')?>.png">
	<link rel="icon" type="image/png" sizes="192x192" href="files/icons/favicon-192x192_v_<?php echo filemtime('files/icons/favicon-192x192.png')?>.png">
	<link rel="icon" type="image/png" sizes="160x160" href="files/icons/favicon-160x160_v_<?php echo filemtime('files/icons/favicon-160x160.png')?>.png">
	<link rel="icon" type="image/png" sizes="96x96"   href="files/icons/favicon-96x96_v_<?php echo filemtime('files/icons/favicon-96x96.png')?>.png">
	<link rel="icon" type="image/png" sizes="32x32"	  href="files/icons/favicon-32x32_v_<?php echo filemtime('files/icons/favicon-32x32.png')?>.png">
	<link rel="icon" type="image/png" sizes="16x16"   href="files/icons/favicon-16x16_v_<?php echo filemtime('files/icons/favicon-16x16.png')?>.png">
	<link rel="manifest" href="<?php echo SITE_HTTP_URL?>files/icons/manifest_v_<?php echo filemtime('files/icons/manifest.json')?>.json">
	<!-- Chrome, Firefox OS and Opera -->
	<meta name="theme-color" content="<?php echo $GLOBALS['theme_color']?>">
	<!-- Windows Phone -->
	<meta name="msapplication-navbutton-color" content="<?php echo $GLOBALS['theme_color']?>">
	<!-- iOS Safari -->
	<meta name="apple-mobile-web-app-status-bar-style" content="<?php echo $GLOBALS['theme_color']?>">
	<meta name="msapplication-TileColor" content="<?php echo $GLOBALS['theme_color']?>">
	<meta name="msapplication-TileImage" content="files/icons/ms-icon-144x144_v_<?php echo filemtime('files/icons/ms-icon-144x144.png')?>.png">

    <style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style><noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>
    <?

    if($AMP_header) echo $AMP_header;
    else
    	echo '
	<script async custom-element="amp-selector" src="https://cdn.ampproject.org/v0/amp-selector-0.1.js"></script>
    <script async custom-element="amp-font" src="https://cdn.ampproject.org/v0/amp-font-0.1.js"></script>
    <script async custom-element="amp-social-share" src="https://cdn.ampproject.org/v0/amp-social-share-0.1.js"></script>
    <script async src="https://cdn.ampproject.org/v0.js"></script> ';
    if($tem_video==1)echo '<script async custom-element="amp-youtube" src="https://cdn.ampproject.org/v0/amp-youtube-0.1.js"></script>';
    ?>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700,400italic" rel="stylesheet" type="text/css">
    <style amp-custom>
    #header-space{display:none}
    body {margin: 0;font-family:'Open Sans',Serif}
    .brand-logo {font-family:'Open Sans'}
    .ad-container {display:flex;justify-content: center}
    .content-container p {line-height: 24px}
    amp-sidebar{background-color: #2d343e}
    amp-sidebar a{color: rgba(255,255,255,0.7); text-decoration: none}
    /***********************************/
    amp-sidebar{width:250px;padding-right:10px}
    li{margin-bottom:20px;list-style:none}
    <?php
    include 'files/classes.css';?>
    /****************************************************/
    <style amp-custom>
    article#preview {background-color:#f5f5f5}
    amp-carousel {margin:0}
    .header {
      position: relative;
    }
    .header #sample-menu {
      position:absolute;
      top:0;
      left: 0;
      font-size: 18px;
      font-weight: 700;
      color: white;
      text-transform: uppercase;
      padding: 13px;
    }
    .header #sample-logo {
      position:absolute;
      top:0;
      left:36px;
      font-size: 18px;
      font-weight: 400;
      color: white;
      text-transform: uppercase;
      margin: 0 16px;
      height: 32px;
      text-decoration: none;
      line-height: 52px;
    }

    .header form {
      padding: 0;
    }
    .header input::-webkit-search-decoration,
    .header input::-webkit-search-cancel-button {
      display: none;
    }
    .header input[type=submit] {
      position:absolute;
      top:10px; right:8px;
      background-color: #eb407a;
      background-position: center;
      background-repeat: no-repeat;
      border: 0 none;
      height: 32px;
      width: 36px;
      -webkit-appearance: none;
      border-radius: 0;
      -moz-border-top-right-radius: 4px;
      -moz-border-bottom-right-radius: 4px;
      -webkit-border-top-right-radius: 4px;
      -webkit-border-bottom-right-radius: 4px;
      border-top-right-radius: 4px;
      border-bottom-right-radius: 4px;
    }
    .header input[type=search], .header input[type=text] {
      position: absolute;
      top: 10px;
      right: 24px;
      background: #ededed ;
      padding: 0 8px;
      width: 20vw;
      height: 32px;
      font-family: inherit;
      font-size: 100%;
      -moz-box-sizing: border-box;
      -webkit-box-sizing: border-box;
      box-sizing: border-box;
      -webkit-appearance: none;
      border: solid 0px #ccc;
      border-radius: 0;
      -moz-border-top-left-radius: 4px;
      -moz-border-bottom-left-radius: 4px;
      -webkit-border-top-left-radius: 4px;
      -webkit-border-bottom-left-radius: 4px;
      border-top-left-radius: 4px;
      border-bottom-left-radius: 4px;
      -webkit-transition: all .9s;
      -moz-transition: all .9s;
      transition: all .9s ease;
    }
    .header input[type=search]:focus, .header input[type=text]:focus {
      box-sizing: border-box;
      width: 80vw;
      max-width: 700px;
      height: 32px;
      background-color: #fff;
      border-color: #eb407a;
      -webkit-box-shadow: 0 0 5px rgba(109,207,246,.5);
      -moz-box-shadow: 0 0 5px rgba(109,207,246,.5);
      box-shadow: 0 0 5px rgba(109,207,246,.5);
      outline:none;
    }
    .header input:-moz-placeholder,
    .header input::-webkit-input-placeholder {
      color: #999;
    }
    .header input:focus + #sample-logo {
      visibility: hidden;
      opacity: 0;
      transition: visibility 0s 0.7s, opacity 0.7s ease;
    }
    .header input + #sample-logo {
      visibility: visible;
      opacity: 1;
      transition: opacity 1.5s ease;
    }
    @media (min-width: 600px) {
      .header input[type=search] {
        width: 100px;
      }
      .header input[type=search]:focus {
        width: 600px;
      }
    }

    .items,
    amp-list.items > div {
      display: flex;
      justify-content:space-around;
      flex-wrap: wrap;
    }
    .item {
      flex-grow: 1;
      flex-shrink: 1;
    }

    .tile {
      background-color: white;
      width: 120px;
      height: 200px;
      display: block;
      margin: 8px;
      -webkit-tap-highlight-color: #eee;
      max-width: 200px;
    }
    @media (max-width: 500px) {
      .items .tile {
        width: 150px;
        height: 200px;
      }
    }
    .tile:active {
      background-color: #eee;
    }
    .tile p {
      margin: 0;
      padding: 0 8px;
      font-size: 14px;
      line-height:20px;
    }
    .tile .name {
      margin-top: 8px;
    }
    .tile .price {
      margin-bottom: 8px;
    }
    .tile .price,
    .tile .star {
      color: black;
    }
    #product-description {
      clear: both;
    }
    amp-lightbox {
      background: rgba(0,0,0,0.8);
      width: 100%;
      height: 100%;
      position: absolute;
    }
    amp-accordion h4 {
      font-size: 18px;
    }
    amp-carousel .selected {
      border: solid 2px red;
    }
    .amp-youtube-container {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 100%;
      height: 100%;
    }
    .video {
      flex: 1;
    }
    .m1{text-align: center;}
    .price-description {
      color: grey;
      font-weight: 400;
      margin-top: 24px;
      margin-bottom: 16px;
      padding: 0 16px;
      text-align: center;
    }
    .product-gallery > ul {
      margin: 0;
      padding: 0;
      float: left;
      list-style: none;
      line-height: 0;
    }
    .product-gallery > ul > li {
      cursor: pointer;
      margin: 0 1em 1em 1em;
      opacity: 1;
      will-change: opacity;
      transition: all 0.3s cubic-bezier(.25,.8,.25,1);
    }
    .product-gallery > ul > li:active {
      opacity: 0.5;
    }
    .product-gallery > ul > li:focus {
      outline: none;
    }
    .product-gallery amp-img:focus {
      outline: none;
    }
    form#order label {
      display: inline;
      font-weight: 500;
      font-size: 16px;
      margin: 0;
    }
    form#order .button {
      width: 100%;
      max-width: 400px;
      align-self: center;
      margin: 16px 20%;
      box-sizing: border-box;
    }
    form#order.amp-form-submitting [name=add-to-cart] {
      color: #666;
      background-color: #aaa;
      cursor: default;
    }
    form#order amp-selector > ul {
      list-style: none;
      display: flex;
      align-items: flex-end;
      justify-content: end;
    }
    form#order amp-selector > ul li {
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 8px;
      background: white;
      height: 40px;
      min-width: 40px;
      width: auto;
    }
    form#order amp-selector [option] {
      outline: none;
    }
    form#order amp-selector amp-img[option][selected] {
      outline-width: 1px;
      outline-style: solid;
      outline-color: #eb407a;
      background: none;
    }
    form#order amp-selector:not([disabled]) [option][selected]:not([disabled]), amp-img.selected {
      will-change: box-shadow;
      box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);
      transition: box-shadow 0.3s cubic-bezier(.25,.8,.25,1);
      color: #fff;
    }

    form#order amp-selector:not([disabled]) [option][selected]:not([disabled]):not(.unavailable):not(.square) {
      background-color: #eb407a;
    }

    form#order amp-selector:not([disabled]) [option][selected]:not([disabled]):not(.unavailable).square {
      outline: 1px solid #eb407a;
    }

    form#order amp-selector:not([disabled]) [option]:not([selected]):not([disabled]), amp-img:not([.selected]) {
      will-change: box-shadow;
      box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
      transition: box-shadow 0.3s cubic-bezier(.25,.8,.25,1);
    }

    form#order amp-selector[name=color] div:not([selected]):not([disabled]) {
      opacity: 0.6;
    }
    @keyframes fadeIn {
      0% {opacity: 0;}
      100% {opacity: 1;}
    }
    .fadeIn {
      animation-duration: 1s;
      animation-fill-mode: both;
      animation-name: fadeIn;
    }
    .hide {
      display: none;
    }
    .show {
      display: block;
    }
    .unavailable {
      position: relative;
      background: gray;
      overflow: hidden;
      opacity: 0.6;
      pointer-events: none;
      user-select: none;
    }

    .unavailable:before, .unavailable:after {
      position: absolute;
      content: '';
      background: grey;
      display: block;
      width: auto;
      height: 1px;
      min-width: 56px;
      -webkit-transform: rotate(-45deg);
      transform: rotate(-45deg);
      left: -11px;
      right: 0;
      top: 0;
      bottom: 0;
      margin: auto;
      border-bottom: 2px solid;
    }

    .unavailable:after {
        -webkit-transform: rotate(45deg);
        transform: rotate(45deg);
    }

    div.square {
      width: 40px;
      height: 40px;
    }

    .green {
      background-color: #008000;
    }

    .red {
      background-color: #FF0000;
    }

    .golden {
      background-color: #DAA520;
    }

    @media (max-width: 1024px) {
      .product-gallery > ul {
        display: none;
      }
    }
    .info-button {
      background: -webkit-image-set(
        url(/img/ic_info_outline_black_24dp_1x.png) 1x,
        url(/img/ic_info_outline_black_24dp_2x.png) 2x
      );
      background-repeat: no-repeat;
      width: 24px;
      height: 24px;
      margin-top: 4px;
    }

    .info {
      margin: 0 auto;
      background: #fff;
    }

    .lightbox {
      width: 100%;
      height: 100%;
      display: flex;
      align-items: center;
    }

    .ampTabContainer {
        display: flex;
        flex-wrap: wrap;
    }

    .tabButton {
        list-style: none;
        text-align: center;
        cursor: pointer;
        outline: none;
    }

    amp-selector [option][selected] {
      outline: #fff;
    }

    .tabContent {
        line-height: 1.5rem;
        display: none;
        width: 100%;
        order: 1;
    }

    .tabButton[selected]::after {
      content: '';
      display: block;
      height: 4px;
      background: linear-gradient(90deg,#94103e 0,#db004d);
    }

    .tabButton[selected]+.tabContent {
        display: block;
    }

    .items, amp-list.items > div {
      justify-content: flex-start;
    }
    .close-gallery-button {
      z-index: 1;
      position: absolute;
      right: 0;
    }
    /****************************************************/
    .container{padding-right:15px;padding-left:15px;margin-right:auto;margin-left:auto}
    .container-fluid{padding-right:15px;padding-left:15px;margin-right:auto;margin-left:auto}
    .row{margin-right:-15px;margin-left:-15px}
    .amp{width:100%;text-align:center}
    .amp_btn{padding:20px;margin-top:20px;margin-bottom:20px;font-size:20px}
    .amp_small{font-size:10px}
    .amp_url{font-size:10px;color:#ffffff;text-decoration:none}
    .amp_bg_dark{background-color:#221f1f}
	</style>
	<script type="application/ld+json">
	{
		"@context" : "http://schema.org",
		"@type" : "Organization",
		"name" : "<?php echo $TAG_EMPRESA?>",
		"url" : "<?php echo $site_http_url?>",
		"contactPoint": {
		"@type": "ContactPoint",
		"telephone": "<?php echo $SEO_PHONE?>",
		"contactType": "Customer service"
		},
	  "logo": {
	    "@context": "http://schema.org",
	    "@type": "ImageObject",
	    "url": "<?php echo $site_http_url?>layout/<?php echo PROJETO?>/logos/logo_top_resp.png",
	    "name": "<?php echo $TAG_EMPRESA?> logo"
	  },
	  "sameAs" : [
	    <? if($SEO_FB) echo '"'.$SEO_FB.'"'?>
	  ]
	}
    </script>
    <script type="application/ld+json">
	{
	  "@context": "http://schema.org",
	  "@type": "WebPage",
	  "description": "<?php echo (isset($page_info) && $page_info['description'] !='' ? $page_info['description'] : $SEO_description); ?>",
	  "alternativeHeadline": "<?php echo (isset($page_info) && $page_info['description'] !='' ? $page_info['description'] : $SEO_description); ?>"
	}
	</script>
	<script type="application/ld+json">
	{
		"@context": "http://schema.org",
		"@type": "WebSite",
		"name": "<?php echo $TAG_EMPRESA?>",
  		"alternateName": "<?php echo $SEO_DESCRIPTION?>",
		"url": "<?php echo $site_http_url?>",
		"potentialAction": {
			"@type": "SearchAction",
			"target": "<?php echo $site_http_url?>p<?php echo $pesquisa_page_id?>-pesquisa-pt?pesquisa={search_term_string}",
			"query-input": "required name=search_term_string"
		}
	}
	</script>
	<script type="application/ld+json">
    {
        "@context": "http://schema.org/",
        "@type": "Product",
        "name": "<?php echo $SEO_title?>",
        "image": "<?php echo $imgfb?>",
        "description": "<?php echo $SEO_description?>",
        "brand": {
            "@type": "Thing",
            "name": "<?php echo $prods['prod_marca']['marca_nome']?>"
        },
        "offers": {
            "@type": "Offer",
            "priceCurrency": "<?php echo MOEDA_ISO?>",
            "price": "<?php

            echo $prods['prod_precos']['PVP'];


            ?>",
            "priceValidUntil": "<?php


			$date = strtotime("+7 day");
			echo date('Y-M-d', $date);


            ?>",
            "itemCondition": "http://schema.org/NewCondition",
            "availability": "http://schema.org/<?php

            $precos = $prods['prod_precos'];
            $PODE_ENCOMENDAR = $precos['PODE_ENCOMENDAR'];


            if($PODE_ENCOMENDAR || $precos['STOCK'] > 0)echo 'InStock';
            else echo 'Outofstock';



            	?>",
            "seller": {
                "@type": "Retail",
                "name": "<?php echo $TAG_EMPRESA?>"
            }
        }
    }
    </script>
    </head>
    <body>
    <amp-font layout="nodisplay"
  font-family="Open Sans"
  timeout="2000"
  on-error-remove-class="unavailable-font-loading"
  on-error-add-class="unavailable-font-missing"
  on-load-remove-class="unavailable-font-loading"
  on-load-add-class="unavailable-font-loaded">
	</amp-font><?


	/* CONTEUDO */
	if(isset($page_id))
	{
		if(!defined('is_home')) echo BREADCRUMBS();
		if (!$GLOBALS['ocultar_titulo'])  echo '<div class="titulo_d">' . $page_name . '</div>';

		show_content($page_id);
	}
?>