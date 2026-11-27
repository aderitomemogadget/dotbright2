<?php
// Local UI fixture: no DB, external writes, checkout or account actions.
if (PHP_SAPI !== 'cli-server' || !in_array($_SERVER['REMOTE_ADDR'] ?? '',array('127.0.0.1','::1'),true)) { http_response_code(404); exit; }
require dirname(__DIR__).'/layout/dotbright/code/seminovo_detail.php';
$file=sys_get_temp_dir().'/dotbright-seminovo-original.html';
if (!is_file($file)) { http_response_code(404); exit('Local product snapshot required'); }
$html=file_get_contents($file);
$html=preg_replace('~<script\b[^>]*>.*?</script\s*>~is','',$html);
// Use local assets; preserve the captured header/footer HTML without redesigning them.
$GLOBALS['site_http_url']='http://127.0.0.1:8974/';
$p=array('fam_id'=>163,'prod_id'=>143608,'prod_nome'=>'(SEMINOVO) IPHONE 18 PRO MAX 2TB GLACIAR',
    'prod_estado'=>in_array($_GET['grade']??'',array('A+','A','B'),true)?$_GET['grade']:'A+',
    'prod_bateria'=>isset($_GET['battery'])?$_GET['battery']:'100',
    'prod_localizacao_fisica'=>isset($_GET['location'])?$_GET['location']:'Loja da Maia',
    'prod_precos'=>array('STOCK'=>isset($_GET['stock'])?(int)$_GET['stock']:1), 'seminovo_reference_image'=>true);
$d=new DOMDocument();@$d->loadHTML('<?xml encoding="UTF-8">'.$html);$x=new DOMXPath($d);
$main=$x->query('//*[@id="part1"]/../..')->item(0);
if(!$main)exit('Product markup missing');
if(isset($_GET['location'])){foreach($x->query('//*[contains(@class,"localizacao_fisica")]//strong') as $store){$store->textContent=$p['prod_localizacao_fisica'];}}
if(isset($_GET['stock']) && (int)$_GET['stock']===0){foreach($x->query('//button[contains(@onclick,"adicionarCarrinhoProdutoOpcoes")]') as $button){$button->removeAttribute('onclick');$button->textContent='Alerta de stock';$button->setAttribute('class','fp_comprar_esgotado');}}
foreach($x->query('//*[contains(@class,"swiper-slide")][@onclick]') as $i=>$slide){if(!preg_match('/p3-p-(\d+)/',$slide->getAttribute('onclick'),$match))continue;$img=$slide->getElementsByTagName('img')->item(0);$name=$slide->getElementsByTagName('h5')->item(0);if(!$img||!$name)continue;$price=$x->query('.//*[contains(@class,"product-price1")]',$slide)->item(0);$card=array('id'=>(int)$match[1],'title'=>$name->textContent,'image'=>$img->getAttribute('src'),'caminho'=>'#related-'.$match[1],'price'=>$price?(float)str_replace(',','.',str_replace('.', '', preg_replace('/[^0-9,.]/','',$price->textContent))):0,'stock'=>1,'carrinho'=>1);$slide->setAttribute('data-db-semi-card',html_entity_decode(dotbright_seminovo_related_data($card,$p),ENT_QUOTES,'UTF-8'));}
$mainHTML=$d->saveHTML($main);$html=$d->saveHTML();$html=str_replace($mainHTML,dotbright_seminovo_detail($p,$mainHTML),$html);
$globals='<script>window.$=function(selector){return {val:function(){return document.querySelector(selector).value;}}};var TEM_ENC_SEM_STOCK=0,SITE_HTTP_AJAX_URL="/test-no-network/";function numberformat(n){return Number(n).toLocaleString("pt-PT",{style:"currency",currency:"EUR"});}window.previewActions={cart:0,wish:0};function adicionarCarrinhoProdutoOpcoes(id,q){previewActions.cart++;document.documentElement.dataset.previewCart=previewActions.cart;}function addProductWishList(){previewActions.wish++;document.documentElement.dataset.previewWish=previewActions.wish;}function removeProductWishList(){previewActions.wish++;document.documentElement.dataset.previewWish=previewActions.wish;}</script><script src="/layout/dotbright/js/bloco_produto.js"></script>';
$globals=str_replace('src="/layout/','src="http://127.0.0.1:8974/layout/',$globals);
$html=str_replace('<head>','<head><base href="https://dev3.memogadget.com/dotbright2/">'.$globals,$html);
if(isset($_GET['missing']))$html=str_replace('"grade":"'.$p['prod_estado'].'"','"grade":""',str_replace('"battery":'.(int)$p['prod_bateria'],'"battery":null',$html));
echo $html;
