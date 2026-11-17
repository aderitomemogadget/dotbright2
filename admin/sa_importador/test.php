<?php

$Categoria = 'MICRO-ONDAS !"#$%&/()=?»«._çÇ';
$Categoria = preg_replace("/[^-!\"#?._=ç \w]+/", "", $Categoria);


echo $Categoria;

/* $file_path = 'https://www.worten.pt/i/2475d22bea7dccb1e16a492facb0a845e5d15479';
$file_path = 'http://192.168.1.202/lojas/neocasa/files/media/1_1.png?d=1498472787';


switch(@exif_imagetype($file_path))
{
	case IMAGETYPE_JPEG:
		$extensions = 'jpg';
		break;
	case IMAGETYPE_PNG:
		$extensions = 'png';
		break;
	case IMAGETYPE_GIF:
		$extensions = 'gif';
		break;
}
	
	
echo $extensions;  */