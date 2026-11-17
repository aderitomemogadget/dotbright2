<?php
require_once __DIR__.'/core/product_images.php';
$site_http_url = rtrim(str_replace('\\', '/', dirname(isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '/original-product-image.php')), '/').'/';
// Compatibility for previously cached product-detail image URLs. No database writes.
$id=isset($_GET['image'])?(string)$_GET['image']:'';
$ext=isset($_GET['ext'])?strtolower((string)$_GET['ext']):'';
if(!preg_match('~^[0-9]+_[0-9]+$~D',$id)||!in_array($ext,array('jpg','jpeg','png','webp','gif','avif'),true)){http_response_code(404);exit;}
$extensions=array_unique(array_merge(array($ext),array('jpg','jpeg','png','webp','gif','avif','JPG','PNG')));
foreach($extensions as $candidate){
 $relative='files/products/'.$id.'.'.$candidate;
 $asset=dotbright_image_asset($relative);
 if($asset!==false&&($asset['file']===false||@getimagesize($asset['file']))){
  header('X-Content-Type-Options: nosniff');header('Cache-Control: public,max-age=3600');
  $url=dotbright_shared_images()?dotbright_image_url($relative):$site_http_url.$relative.'?dp='.$asset['mtime'];
  header('Location: '.$url,true,302);exit;
 }
}
http_response_code(404);
