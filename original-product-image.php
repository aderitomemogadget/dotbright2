<?php
// Compatibility for previously cached product-detail image URLs. No database writes.
$id=isset($_GET['image'])?(string)$_GET['image']:'';
$ext=isset($_GET['ext'])?strtolower((string)$_GET['ext']):'';
if(!preg_match('~^[0-9]+_[0-9]+$~D',$id)||!in_array($ext,array('jpg','jpeg','png','webp','gif','avif'),true)){http_response_code(404);exit;}
$folder=realpath(__DIR__.'/files/products');
if($folder===false){http_response_code(404);exit;}
$extensions=array_unique(array_merge(array($ext),array('jpg','jpeg','png','webp','gif','avif','JPG','PNG')));
foreach($extensions as $candidate){
 $path=$folder.'/'.$id.'.'.$candidate;
 if(is_link($path))continue;
 $real=realpath($path);
 if($real!==false&&strpos($real,$folder.DIRECTORY_SEPARATOR)===0&&is_file($real)&&@getimagesize($real)){
  header('X-Content-Type-Options: nosniff');header('Cache-Control: public,max-age=3600');
  header('Location: /files/products/'.$id.'.'.$candidate.'?dp='.filemtime($real),true,302);exit;
 }
}
http_response_code(404);
