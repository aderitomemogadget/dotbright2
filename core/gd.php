<?php
if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) exit();
require_once __DIR__.'/product_images.php';
function gera_image($src,$new,$w,$h) //funcao que vai gerar uma imagem nova se a data da original mudar
{
	global $site_shell_url;
	if(stream_resolve_include_path($site_shell_url.$src))$src_date=date("Y-m-d H:i:s",filemtime($site_shell_url.$src));
	if(stream_resolve_include_path($site_shell_url.$new))$new_date=date("Y-m-d H:i:s",filemtime($site_shell_url.$new));
	if((!stream_resolve_include_path($site_shell_url.$new) || $new_date<$src_date) && stream_resolve_include_path($site_shell_url.$src))
	{
		if(!stream_resolve_include_path($site_shell_url.$src))return '';

		setMemoryForImage($site_shell_url.$src); // vamos alocar memoria para esta imagem

		if($size=@getimagesize($site_shell_url.$src)){/*Ok*/}
		else return("ERRO: ".$site_shell_url.$src);
		switch($size[2])
		{
			case 2: return redimencionaJpegFill($site_shell_url,$src,$w,$h,$new,0);break;
			case 3: return redimencionaPNGFill($site_shell_url,$src,$w,$h,$new,0);break;
			default: ''; break;
		}
	}
}
function setMemoryForImage( $filename )
{
	$imageInfo=getimagesize($filename);
	$MB=1048576; // number of bytes in 1M
	$K64=65536; // number of bytes in 64K
	$TWEAKFACTOR=1.85; // Or whatever works for you
	$memoryNeeded=round( ( $imageInfo[0] * $imageInfo[1] * $imageInfo['bits']	* $imageInfo['channels'] / 8 + $K64) * $TWEAKFACTOR);

	//Default memory limit is 8MB so well stick with that.
	//To find out what yours is, view your php.ini file.
	$memoryLimit=8 * $MB;
	if (function_exists('memory_get_usage') && memory_get_usage() + $memoryNeeded > $memoryLimit)
	{
		$newLimit=$memoryLimit + ceil( ( memory_get_usage() + $memoryNeeded - $memoryLimit) / $MB);
		$newLimit=$newLimit + 3000000;
		ini_set('memory_limit',$newLimit . 'M' );
		return true;
	}
	else
	{
		return false;
	}
}
function geraimagens($img_original='',$img_nova='',$w=0,$h=0,$crop=array(),$transparent=0,$white=0,$marcaagua=array(),$nome='',$gerar=0,$fit=0)
{
	// Shared originals and derivatives are read only; regeneration stays in DEV.
	if (dotbright_shared_images() && dotbright_image_name($img_original) !== false
		&& dotbright_image_file_in(dotbright_image_root().'files/products', dotbright_image_name($img_original)) === false) return false;
	// _prod derivatives are obsolete; list/cart thumbnails remain unchanged.
	if(preg_match('~(?:^|/)files/products/[0-9]+_[0-9]+_prod(?:_v_[0-9]+)?\.(?:png|jpe?g|webp|gif|avif)$~i', (string)$img_nova)) return false;
	$no_image=0;
	if($img_nova=='')
	{
		$no_image=1;
		$ext=pathinfo($img_original, PATHINFO_EXTENSION);
		$img_nova=str_replace('.'.$ext, '_resize'.$nome.'.'.$ext,$img_original);
	}
	if(stream_resolve_include_path($GLOBALS['site_shell_url'].$img_original))$original=filemtime($GLOBALS['site_shell_url'].$img_original);
	if(stream_resolve_include_path($GLOBALS['site_shell_url'].$img_nova))$nova=filemtime($GLOBALS['site_shell_url'].$img_nova);
	if((!stream_resolve_include_path($GLOBALS['site_shell_url'].$img_nova) || $nova < $original) && stream_resolve_include_path($GLOBALS['site_shell_url'].$img_original))
	{
		if($fit==1)
		{
			jpegImgCrop($GLOBALS['site_shell_url'].$img_original,$GLOBALS['site_shell_url'].$img_nova,(isset($crop['w']) && $crop['w'] > 0 ? $crop['w'] : $w),(isset($crop['h']) && $crop['h'] > 0 ? $crop['h'] : $h));
		}
		else
		{
			//if(resize_image((($white == 1) ? $GLOBALS['site_shell_url'].$img_nova : $GLOBALS['site_shell_url'].$img_original),$w,(($crop == 1) ? 0 : $h),true,$GLOBALS['site_shell_url'].$img_nova))
			if(resize_image($GLOBALS['site_shell_url'].$img_original,$w,(($crop == 1) ? 0 : $h),true,$GLOBALS['site_shell_url'].$img_nova))
			{
				// if($transparent == 1 && $white == 0)make_transparent2($GLOBALS['site_shell_url'],$img_original,$img_nova);
				// if($transparent == 1 && $white == 0)make_transparent($GLOBALS['site_shell_url'],$img_original,$img_nova);
				// if($transparent == 1 && $white == 1)make_transparent($GLOBALS['site_shell_url'],$img_nova,$img_nova);


				//Vamos remover os brancos à volta depois de a reduzirmos
//if($transparent == 1)make_transparent3($GLOBALS['site_shell_url'],$img_nova,$img_nova);


				if(isset($crop['ativo']) && $crop['ativo'] == 1)cropImage_new($GLOBALS['site_shell_url'].$img_nova,$GLOBALS['site_shell_url'].$img_nova,(isset($crop['w']) && $crop['w'] > 0 ? $crop['w'] : $w),(isset($crop['h']) && $crop['h'] > 0 ? $crop['h'] : $h));
				if(isset($marcaagua) && !empty($marcaagua))
				{
					if($marcaagua['ativo'] == 1)
					{
						WatermarkImage($GLOBALS['site_shell_url'].$img_nova,$marcaagua['imagem']);
					}
				}
			}
		}
	}
	if($no_image == 1)
	{
		return $img_nova;
	}
}
function jpegImgCrop($source,$dest,$nw,$nh)
{
	if($info=@getimagesize($source))
	{
		//Ok
	}
	else return("ERRO: ".$source);

	setMemoryForImage($source); // vamos alocar memoria para esta imagem

	switch($info[2])
	{
		case IMAGETYPE_GIF:
			$image=imagecreatefromgif($source);
		break;
		case IMAGETYPE_JPEG:
			$image=imagecreatefromjpeg($source);
		break;
		case IMAGETYPE_PNG:
			$image=imagecreatefrompng($source);
		break;
		default:
			return false;
	}

  $filename=$dest;
  $width=imagesx($image);
  $height=imagesy($image);


	$thumb_width=$nw;
	$thumb_height=$nh;


  $original_aspect=$width / $height;
  $thumb_aspect=$thumb_width / $thumb_height;

  if ( $original_aspect >= $thumb_aspect ) {

     // If image is wider than thumbnail (in aspect ratio sense)
     $new_height=$thumb_height;
     $new_width=$width / ($height / $thumb_height);

  }
  else {
     // If the thumbnail is wider than the image
     $new_width=$thumb_width;
     $new_height=$height / ($width / $thumb_width);
  }

  $thumb=imagecreatetruecolor( $thumb_width,$thumb_height );

  // Resize and crop
  imagecopyresampled($thumb,
         $image,
         0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
         0 - ($new_height - $thumb_height) / 2, // Center the image vertically
         0, 0,
         $new_width,$new_height,
         $width,$height);


  switch ($info[2])
	{
		case 1: @imagegif($thumb,$dest); break;
		case 2: @imagejpeg($thumb,$dest,100);  break;
		case 3: @imagepng($thumb,$dest); break;
		default: "";  break;
	}
	return 1;

 }
function cropImage_new($source,$dest,$nw,$nh,$align=0,$color='#FFFFFF')
{
	if(!stream_resolve_include_path($source)) return "";

	if($size=@getimagesize($source))
	{
		//Ok
	}
	else return("ERRO: ".$source);

	setMemoryForImage($source); // vamos alocar memoria para esta imagem

	switch ($size[2])
	{
		case 1: $simg=@imagecreatefromgif($source); break;
		case 2: $simg=@imagecreatefromjpeg($source);  break;
		case 3: $simg=@imagecreatefrompng($source); break;
		default: ""; break;
	}

	$w=$size[0];
	$h=$size[1];

	$nw2=$nw;
	$nh2=$nh;

	$nw=$w;
	$nh=$h;

	$dimg=imagecreatetruecolor($nw2,$nh2);
	$white= imagecolorallocate($dimg,255,255,255);
	imagefill($dimg,0,0,$white);

	$xoffset=($nw2/2) - ($w/ 2);
	$yoffset=($nh2/2) - ($h/ 2);

	if($w > $h) imagecopyresampled($dimg,$simg,$xoffset,$yoffset,0,0,$nw,$nh,$w,$h);
	elseif(($w <$h) || ($w == $h)) imagecopyresampled($dimg,$simg,$xoffset,$yoffset,0,0,$nw,$nh,$w,$h);
	else imagecopyresampled($dimg,$simg,$xoffset,$yoffset,0,0,$nw,$nh,$w,$h);

	switch ($size[2])
	{
		case 1: @imagegif($dimg,$dest); break;
		case 2: @imagejpeg($dimg,$dest,100);  break;
		case 3: @imagepng($dimg,$dest); break;
		default: "";  break;
	}
	return 1;
}
function WatermarkImage($imagem,$aquaimage='')
{
	global $site_shell_url;
	$testGD=get_extension_funcs("gd"); // Grab function list
	if (in_array ("imagegd2",$testGD)) $gd_version="<2"; // Check

	if($info=@getimagesize($imagem))
	{
		//Ok
	}
	else return("ERRO: ".$imagem);

	setMemoryForImage($imagem); // vamos alocar memoria para esta imagem

	switch($info[2])
	{
		case IMAGETYPE_GIF:
			$img_origem=imagecreatefromgif($imagem);
		break;
		case IMAGETYPE_JPEG:
			$img_origem=imagecreatefromjpeg($imagem);
		break;
		case IMAGETYPE_PNG:
			$img_origem=imagecreatefrompng($imagem);
		break;
		default:
			return false;
	}


	$width=imagesx($img_origem);
	$height=imagesy($img_origem);
	$w=($width/2);
	$h=($height/2);

	/************************** REDIMENSIONA WATERMARK ****************/
	//die($site_shell_url.$aquaimage);
	$watermark=imagecreatefrompng($site_shell_url.$aquaimage);
	$w2=imagesx($watermark);
	$h2=imagesy($watermark);
	if($w2>$w || $h2>$h)
	{
		if($w2 - $w>$h2 - $h) $racio=(100*$w)/$w2;
		else $racio=(100*$h)/$h2;

		$w3=$w2*$racio/100;
		$h3=$h2*$racio/100;
	}
	else
	{
		$w3=$w2;
		$h3=$h2;
	}

	$new_watermark=imagecreatetruecolor($w3,$h3);
	imagealphablending($new_watermark,false);
	$color=imagecolortransparent($new_watermark,imagecolorallocatealpha($new_watermark,230,10,0,127));
	imagefill($new_watermark,0,0,$color);
	imagesavealpha($new_watermark,true);
	imagecopyresampled($new_watermark,$watermark,0,0,0,0,$w3,$h3,$w2,$h2);
	//******************** FIM DE REDIMENSIONAR A WATERMARK ******************************/
	$dest_x=($width/2)-($w3/2);//$width - $w3;
	$dest_y=($height/2) - ($h3/2); // para colocar a marca de agua no final da foto

	if($gd_version == "<2")
	{
		$img_destino=imagecreatetruecolor($width,$height);
		imagealphablending($img_destino,true);
		imagecopyresampled($img_destino,$img_origem,0,0,0,0,$width,$height,$width,$height);
		imagecopyresampled($img_destino,$new_watermark,$dest_x,$dest_y,0,0,$w3,$h3,$w3,$h3);
	}
	else
	{
		$img_destino=imagecreate($width,$height);
		imagecopyresized($img_destino,$img_origem,0,0,0,0,$width,$height,$width,$height);
		imagecopyresized($img_destino,$new_watermark,$dest_x,$dest_y,0,0,$watermark_width,$watermark_height,$w3,$h3);
	}
	imagesavealpha($new_image,true);

	switch($info[2]) {
	   case IMAGETYPE_GIF:
		 imagegif($img_destino,$imagem);
	   break;
	   case IMAGETYPE_JPEG:
		 imageJPEG($img_destino,$imagem,100);
	   break;
	   case IMAGETYPE_PNG:
		 imagepng($img_destino,$imagem);
	   break;
	   default:
		 return false;
	 }


	return $imagem;
}

function resize_image($file,$width=0,$height=0,$proportional=true,$output='file',$delete_original=false)
{
	if ($height <= 0 && $width <= 0 )return false;
	if($info=@getimagesize($file)){/*Ok*/}
	else return("ERRO: ".$file);

	setMemoryForImage($file); // vamos alocar memoria para esta imagem
	set_time_limit(0);

	$image='';
	$final_width=0;
	$final_height=0;
	list($width_old,$height_old)=$info;
	//O novo tamanho nao pode ser maior que o anterior
	if($width > $width_old)$width = $width_old;
	if($height > $height_old)$height = $height_old;

	if($proportional)
    {
		if($width == 0)$factor=$height/$height_old;
		elseif($height == 0)$factor=$width/$width_old;
		else $factor=min($width/$width_old,$height/$height_old);

		$final_width=round($width_old*$factor);
		$final_height=round($height_old*$factor);
   }
   else
   {
	 $final_width=($width <= 0 ) ? $width_old : $width;
	 $final_height=($height <= 0 ) ? $height_old : $height;
   }

   switch ($info[2]) {
	 case IMAGETYPE_GIF:
	   $image=imagecreatefromgif($file);
	 break;
	 case IMAGETYPE_JPEG:
	   $image=imagecreatefromjpeg($file);
	 break;
	 case IMAGETYPE_PNG:
	   $image=imagecreatefrompng($file);
	 break;
	 default:
	   return false;
   }

   $image_resized=imagecreatetruecolor( $final_width,$final_height );

   if(($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG))
   {
	 $trnprt_indx=imagecolortransparent($image);

	 // If we have a specific transparent color
	 if($trnprt_indx >= 0) {

	   // Get the original image's transparent color's RGB values
	   $trnprt_color   =imagecolorsforindex($image,$trnprt_indx);

	   // Allocate the same color in the new image resource
	   $trnprt_indx   =imagecolorallocate($image_resized,$trnprt_color['red'],$trnprt_color['green'],$trnprt_color['blue']);

	   // Completely fill the background of the new image with allocated color.
	   imagefill($image_resized, 0, 0,$trnprt_indx);

	   // Set the background color for new image to transparent
	   imagecolortransparent($image_resized,$trnprt_indx);


	 }
	 // Always make a transparent background color for PNGs that don't have one allocated already
	 elseif ($info[2] == IMAGETYPE_PNG) {

	   // Turn off transparency blending (temporarily)
	   imagealphablending($image_resized, false);

	   // Create a new transparent color for image
	   $color=imagecolorallocatealpha($image_resized, 0, 0, 0, 127);

	   // Completely fill the background of the new image with allocated color.
	   imagefill($image_resized, 0, 0,$color);

	   // Restore transparency blending
	   imagesavealpha($image_resized, true);
	 }
   }

   imagecopyresampled($image_resized,$image, 0, 0, 0, 0,$final_width,$final_height,$width_old,$height_old);

   switch (strtolower($output)) {
	 case 'browser':
	   $mime=image_type_to_mime_type($info[2]);
	   header("Content-type: $mime");
	   $output=NULL;
	 break;
	 case 'file':
	   $output=$file;
	 break;
	 case 'return':
	   return $image_resized;
	 break;
	 default:
	 break;
   }

   switch ($info[2]) {
	 case IMAGETYPE_GIF:
	   imagegif($image_resized,$output);
	 break;
	 case IMAGETYPE_JPEG:
	   imagejpeg($image_resized,$output, 100);
	 break;
	 case IMAGETYPE_PNG:
	   imagepng($image_resized,$output);
	 break;
	 default:
	   return false;
   }

   return true;
}
function imagem_brancos($u,$foto,$foto2)
{
	if($size=@getimagesize($u.$foto))
	{
		//Ok
	}
	else return("ERRO: ".$u.$foto);

	switch($size[2])
	{
		case 1: $simg=@imagecreatefromgif($u.$foto); break;
		case 2: $simg=@imagecreatefromjpeg($u.$foto); break;
		case 3: $simg=@imagecreatefrompng($u.$foto); break;
		default: ''; break;
	}

	$imgx=imagesx($simg);
	$imgy=imagesy($simg);

	for($x=0;$x <$imgx; ++$x)
	{
		for($y=0;$y <$imgy; ++$y)
		{
			$rgb=imagecolorat($simg,$x,$y);
			$colors=imagecolorsforindex($simg,$rgb);
			$alpha=$colors['alpha'];
			if($alpha < 100)
			{
				$red=($rgb >> 16) & 0xFF;
				$green=($rgb >> 8) & 0xFF;
				$blue=$rgb & 0xFF;
				if($red > 255) $red=255;
				if($green > 255) $green=255;
				if($blue > 255) $blue=255;
				if($red < 0) $red=0;
				if($green < 0) $green=0;
				if($blue < 0) $blue=0;

				//if($red>=250 && $green>=250 && $blue>=250){$red=239;$green=239;$blue=239;} // coloca a cinza
				if($red>=250 && $green>=250 && $blue>=250)
				{
					$red=255;
					$green=255;
					$blue=255;
					$alpha=127;
				}
				$new_color=imageColorAllocateAlpha($simg,$red,$green,$blue,$alpha);
				imagesetpixel($simg,$x,$y,$new_color);
			}
		}
	}


	switch($size[2])
	{
		case 1: @imagegif($simg,$u.$foto2); break;
		case 2: @imagejpeg($simg,$u.$foto2,100);  break;
		case 3: @imagepng($simg,$u.$foto2); break;
		default: '';  break;
	}
	if(stream_resolve_include_path($u.$foto2)) return $foto2;
	else return '';
}
function crop_white_space($u,$foto,$foto2)
{
	//load the image
	if($size=@getimagesize($u.$foto))
	{
		//Ok
	}
	else return("ERRO: ".$u.$foto);
	switch($size[2])
	{
		case 1: $img=@imagecreatefromgif($u.$foto); break;
		case 2: $img=@imagecreatefromjpeg($u.$foto); break;
		case 3: $img=@imagecreatefrompng($u.$foto); break;
		default: ''; break;
	}
	//find the size of the borders
	$b_top=0;
	$b_btm=0;
	$b_lft=0;
	$b_rt=0;
	//top
	for(; $b_top < imagesy($img); ++$b_top) {
	  for($x=0; $x < imagesx($img); ++$x) {
		if(imagecolorat($img,$x,$b_top) != 0xFFFFFF) {
		   break 2; //out of the 'top' loop
		}
	  }
	}
	//bottom
	for(; $b_btm < imagesy($img); ++$b_btm) {
	  for($x=0; $x < imagesx($img); ++$x) {
		if(imagecolorat($img,$x, imagesy($img) - $b_btm-1) != 0xFFFFFF) {
		   break 2; //out of the 'bottom' loop
		}
	  }
	}
	//left
	for(; $b_lft < imagesx($img); ++$b_lft) {
	  for($y=0; $y < imagesy($img); ++$y) {
		if(imagecolorat($img,$b_lft,$y) != 0xFFFFFF) {
		   break 2; //out of the 'left' loop
		}
	  }
	}
	//right
	for(; $b_rt < imagesx($img); ++$b_rt) {
	  for($y=0; $y < imagesy($img); ++$y) {
		if(imagecolorat($img, imagesx($img) - $b_rt-1,$y) != 0xFFFFFF) {
		   break 2; //out of the 'right' loop
		}
	  }
	}
	//copy the contents, excluding the border
	$newimg=imagecreatetruecolor(imagesx($img)-($b_lft+$b_rt), imagesy($img)-($b_top+$b_btm));
	$bg=ImageColorAllocateAlpha($newimg,255,255,255,127); //(PHP 4 >=4.3.2,PHP 5)
	ImageFill($newimg,0,0 ,$bg);

	imagecopy($newimg,$img, 0, 0,$b_lft,$b_top, imagesx($newimg), imagesy($newimg));
	imagesavealpha($newimg,true);
	imagepng($newimg,$u.$foto2);
	if(stream_resolve_include_path($u.$foto2)) return $foto2;
	else return '';
}
function make_transparent($u,$foto,$foto2)
{
	setMemoryForImage($u.$foto); // vamos alocar memoria para esta imagem

	if($size=@getimagesize($u.$foto))
	{
		//Ok
	}
	else return("ERRO: ".$u.$foto);

	switch($size[2])
	{
		case 1: $simg=@imagecreatefromgif($u.$foto); break;
		case 2: $simg=@imagecreatefromjpeg($u.$foto);  break;
		case 3: $simg=@imagecreatefrompng($u.$foto); break;
		default: ''; break;
	}
	$imagex=imagesx($simg);
	$imagey=imagesy($simg);

	// Create a new image
	$img_destino=imagecreatetruecolor($imagex,$imagey);
	$red = imagecolorallocate($img_destino, 255, 0, 0);

	// Set a White & Transparent Background Color
	//$bg=ImageColorAllocateAlpha($img_destino,255,255,255,127); //(PHP 4 >=4.3.2,PHP 5)
	$bg = imagecolorallocate($img_destino, 0, 0, 0);
	// Make the background transparent
	imagecolortransparent($img_destino, $black);
	//ImageFill($img_destino,0,0,$bg);

	// Draw a red rectangle
	//imagefilledrectangle($img_destino, 4, 4, 50, 25, $red);


	for($x=0;$x <$imagex; ++$x)
	{
		for($y=0;$y <$imagey; ++$y)
		{
			$rgb=imagecolorat($simg,$x,$y);
			$alpha=($rgb >> 24) & 0x7F;
			if($alpha < 100)
			{
				$red=($rgb >> 16) & 0xFF;
				$green=($rgb >> 8) & 0xFF;
				$blue=$rgb & 0xFF;

				if($red>=250 && $green>=250 && $blue>=250)// encontrou brancos
				{
					//branco
				}
				else
				{
					$newcol=imageColorAllocateAlpha($simg,$red,$green,$blue,$alpha);
					imagesetpixel($img_destino,$x,$y,$newcol);
				}
			}
		}
	}
	//imagesavealpha($img_destino,true);
	if(stream_resolve_include_path($u.$foto2)) unlink($u.$foto2);
	imagepng($img_destino,$u.$foto2);

	if(stream_resolve_include_path($u.$foto2)) return $foto2;
	else return '';
}
function FloodFill($im, $x, $y)
{
    $rgb = imagecolorat($im, $x, $y);
    $r = ($rgb >> 16) & 0xFF;
    $g = ($rgb >> 8) & 0xFF;
    $b = $rgb & 0xFF;
 
    $counter=0;
    $counter2=0;
    if($r >= 245){ $counter++;}
    if($g >= 245){ $counter++;}
    if($b >= 245){ $counter++;}
    if($r >= 240){ $counter2++;}
    if($g >= 240){ $counter2++;}
    if($b >= 240){ $counter2++;}

    if($counter >= 1 && $counter2 == 3){
        $background = imagecolorallocate($im, 180, 0, 255);
        imagesetpixel($im, $x, $y, $background);

        FloodFill ($im, $x, $y+1);
        FloodFill ($im, $x+1, $y);
        FloodFill ($im, $x, $y-1);
    }
}
function make_transparent2($u,$foto,$foto2)
{
	setMemoryForImage($u.$foto); // vamos alocar memoria para esta imagem

	if($size=@getimagesize($u.$foto))
	{
		//Ok
	}
	else return("ERRO: ".$u.$foto);

	switch($size[2])
	{
		case 1: $simg=@imagecreatefromgif($u.$foto); break;
		case 2: $simg=@imagecreatefromjpeg($u.$foto);  break;
		case 3: $simg=@imagecreatefrompng($u.$foto); break;
		default: ''; break;
	}
	$imagex=imagesx($simg);
	$imagey=imagesy($simg);
	$img_destino=imagecreatetruecolor($imagex,$imagey);
	imagesavealpha($img_destino,true);
	imagealphablending($img_destino,false);
	// Set a White & Transparent Background Color
	$bg=ImageColorAllocateAlpha($img_destino,0,0,0,127); //(PHP 4 >=4.3.2,PHP 5)
	ImageFill($img_destino,0,0 ,$bg);


	for($x=0;$x <$imagex; ++$x)
	{
		for($y=0;$y <$imagey; ++$y)
		{
			$rgb=imagecolorat($simg,$x,$y);
			$alpha=($rgb >> 24) & 0x7F;

			$red = ($rgb >> 16) & 0xFF;
            $green = ($rgb >> 8) & 0xFF;
            $blue = $rgb & 0xFF;

			if ($red < 250 || $green < 250 || $blue < 250) {
                imagesetpixel($img_destino, $x, $y, imagecolorallocatealpha($img_destino, $red, $green, $blue, 127));
            }
		}
	}
	imagesavealpha($img_destino,true);
	if(stream_resolve_include_path($u.$foto2)) unlink($u.$foto2);
	imagepng($img_destino,$u.$foto2);

	if(stream_resolve_include_path($u.$foto2)) return $foto2;
	else return '';
}

function make_transparent3($u,$foto,$foto2)
{
	//Passei o script para background e desta forma a página não crasha enquanto está a recortar as imagens
	$path = dirname( __FILE__ ) . '/cron_gd.php '.$u.' '.$foto.' '.$foto2.' > /dev/null &';
	$exe_file = "php ".$path;

	//die($exe_file);
	exec($exe_file,$output);
	return $foto;

	return make_transparent_new($u,$foto,$foto2);
}


function make_transparent_fromcinza($u,$foto,$foto2)
{
	if(stream_resolve_include_path($u.$foto)) $src_date=date('Y-m-d H:i:s',filemtime($u.$foto)); // data/hora em que foi modificada/criada
	if(stream_resolve_include_path($u.$foto2)) $miniatura=date('Y-m-d H:i:s',filemtime($u.$foto2)); //data/hora em que foi modificada/criada
	if(!stream_resolve_include_path($u.$foto2) || $miniatura<$src_date)
	{
		if($size=@getimagesize($u.$foto))
		{
			//Ok
		}
		else return("ERRO: ".$u.$foto);


		switch($size[2])
		{
			case 1: $simg=@imagecreatefromgif($u.$foto); break;
			case 2: $simg=@imagecreatefromjpeg($u.$foto); break;
			case 3: $simg=@imagecreatefrompng($u.$foto); break;
			default: ''; break;
		}
		$imagex=imagesx($simg);
		$imagey=imagesy($simg);
		$img_destino=imagecreatetruecolor($imagex,$imagey);
		// Set a White & Transparent Background Color
		$bg=ImageColorAllocateAlpha($img_destino,255,255,255,127); //(PHP 4 >=4.3.2,PHP 5)
		ImageFill($img_destino,0,0 ,$bg);


		for($x=0;$x <$imagex; ++$x)
		{
			for($y=0;$y <$imagey; ++$y)
			{
				$rgb=imagecolorat($simg,$x,$y);
				$alpha=($rgb >> 24) & 0x7F;
				if($alpha < 100)
				{
					$red=($rgb >> 16) & 0xFF;
					$green=($rgb >> 8) & 0xFF;
					$blue=$rgb & 0xFF;

					if($red>=240 && $green>=240 && $blue>=240)// encontrou brancos
					{
						//branco
					}
					else
					{
						$newcol=imageColorAllocateAlpha($simg,$red,$green,$blue,$alpha);
						imagesetpixel($img_destino,$x,$y,$newcol);
					}
				}
			}
		}

		imagesavealpha($img_destino,true);
		imagepng($img_destino,$u.$foto2);
	}
	if(stream_resolve_include_path($u.$foto2)) return $foto2;
	else return '';
}

// Versăo 3.0 já inclui perspectivas
function perspective($i,$gradient=0.85,$rightdown=true,$background=0xFFFFFF)
{
	$mult=5;
	$w=imagesx($i);
	$h=imagesy($i);
	$img=imagecreatetruecolor($w*$mult,$h*$mult);
	imagecopyresized($img,$i,0,0,0,0,$w*$mult,$h*$mult,$w,$h);
	imagedestroy($i);
	$w*=$mult;
	$h*=$mult;
	$im=imagecreatetruecolor($w,$h);
	$background=imagecolorallocate($im,($background>>16)&0xFF,($background>>8)&0xFF,$background&0xFF);
	imagefill($im,0,0,$background);
	imageantialias($im,true);
	$nh=$h-($h*$gradient);
	for($x=0;$x<$w;$x++)
	{
	 $ni=(($rightdown) ? $x : $w-$x);
	 $p=intval($h-(($ni/$w)*$nh));
	 if(($p%2)<>0)
	  $p-=1;
	 $nx=intval(($p-$h)/2);
	 imagecopyresampled($im,$img,$x,0,$x,$nx,1,$p,1,$h-1);
	 imageline($im,$x,0,$x,-$nx-1,$background);
	 imageline($im,$x,$h-1,$x,$h+$nx,$background);
	}
	imagedestroy($img);
	imagefilter($im,IMG_FILTER_SMOOTH,10);
	$i=imagecreatetruecolor($w/$mult,$h/$mult);
	imageantialias($i,true);
	imagecopyresampled($i,$im,0,0,0,0,$w,$h,$w*$mult,$h*$mult);
	imagedestroy($im);
	return $i;
}
function redimencionaImagem($path,$foto,$largura,$altura,$novo_nome,$tipo=0)
{
	$ficheiro_origem=$path.$foto;
	if(!stream_resolve_include_path($path.$foto)) return '';

	if($size=@getimagesize($path.$foto))
	{
		//Ok
	}
	else return("ERRO: ".$path.$foto);

	switch($size[2])
	{
		case 2: return smart_resize_image($path.$foto,$largura ,$altura ,true,$path.$novo_nome,false,false);break;
		case 3: return smart_resize_image($path.$foto,$largura ,$altura ,true,$path.$novo_nome,false,false);break;
		default: ''; break;
	}
}
function redimencionaJpegFill($path,$foto,$largura,$altura,$novo_nome,$tipo=0)
{
	$testGD=get_extension_funcs('gd'); // Grab function list
	if(in_array('imagegd2',$testGD)) $gd_version='<2'; // Check
	$conf_compressao=100;


	if($info=@getimagesize($path.$foto))
	{
		//Ok
	}
	else return("ERRO: ".$path.$foto);



	$ficheiro_origem=$path.$foto;
	$ficheiro_destino=$ficheiro_origem;
	$nome_ficheiro_destino=$foto;
	$img_origem=imagecreatefromjpeg($ficheiro_origem);
	$width=imagesx($img_origem);
	$height=imagesy($img_origem);
	if($largura>$width) $largura=$width;
	if($altura>$height) $altura=$height;

	if($width >=$height)
	{
		$nova_largura=$largura;
		$x=100*$nova_largura/imagesx($img_origem);
		$nova_altura=$x*imagesy($img_origem)/100;
		// E se a nova altura ainda é maior do que devia????
		if($nova_altura>$altura)
		{
			$nova_altura=$altura;
			$y=100*$nova_altura/imagesy($img_origem);
			$nova_largura=$y*imagesx($img_origem)/100;
		}
		//e se ficou mais pequena do que devia?
		//reduzimos a largura mas a altura pode ter ficado mais pequena do que devia
		if($nova_altura<$altura) // se a nova altura é menor do que a altura pretendida entao temos de reduzir menos
		{
			$nova_altura=$altura;
			$y=100*$nova_altura/imagesy($img_origem);
			$nova_largura=$y*imagesx($img_origem)/100;

		}
		if($nova_largura<$largura)
		{
			$nova_largura=$largura;
			$x=100*$nova_largura/imagesx($img_origem);
			$nova_altura=$x*imagesy($img_origem)/100;
		}
	}
	else
	{
		$nova_altura=$altura;
		$y=100*$nova_altura/imagesy($img_origem);
		$nova_largura=$y*imagesx($img_origem)/100;
		// E se a nova largura ainda é maior do que devia????

		if($nova_largura>$largura)
		{
			$nova_largura=$largura;
			$x=100*$nova_largura/imagesx($img_origem);
			$nova_altura=$x*imagesy($img_origem)/100;
		}
		if($nova_altura<$altura) // se a nova altura é menor do que a altura pretendida entao temos de reduzir menos
		{
			$nova_altura=$altura;
			$y=100*$nova_altura/imagesy($img_origem);
			$nova_largura=$y*imagesx($img_origem)/100;

		}
		if($nova_largura<$largura)
		{
			$nova_largura=$largura;
			$x=100*$nova_largura/imagesx($img_origem);
			$nova_altura=$x*imagesy($img_origem)/100;
		}
	}

	if($gd_version=='<2')
	{
		$img_destino=imagecreatetruecolor($nova_largura,$nova_altura);
		imagecopyresampled($img_destino,$img_origem,0,0,0,0,$nova_largura,$nova_altura,imagesx($img_origem),imagesy($img_origem));
	}
	else
	{
		$img_destino=imagecreate($nova_largura,$nova_altura);
		imagecopyresized($img_destino,$img_origem,0,0,0,0,$nova_largura,$nova_altura,imagesx($img_origem),imagesy($img_origem));
	}
	$ficheiro_destino=$path.$novo_nome;
	$nome_ficheiro_destino=$novo_nome;
	imagejpeg($img_destino,$ficheiro_destino,$conf_compressao);

	cropImage($path.$novo_nome,$path.$novo_nome,$largura,$altura);
	return $nome_ficheiro_destino;
}
function redimencionaPNGFill($path,$foto,$largura,$altura,$novo_nome,$tipo=0)
{
	$testGD=get_extension_funcs('gd');
	if(in_array('imagegd2',$testGD)) $gd_version='<2';
	$conf_compressao=100;

	if($info=@getimagesize($path.$foto))
	{
		//Ok
	}
	else return("ERRO: ".$path.$foto);

	$ficheiro_origem=$path.$foto;
	$ficheiro_destino=$ficheiro_origem;
	$nome_ficheiro_destino=$foto;
	$img_origem=imagecreatefrompng($ficheiro_origem);
	$width=imagesx($img_origem);
	$height=imagesy($img_origem);
	if($largura>$width) $largura=$width;
	if($altura>$height) $altura=$height;

	if($width >=$height)
	{
		$nova_largura=$largura;
		$x=100*$nova_largura/imagesx($img_origem);
		$nova_altura=$x*imagesy($img_origem)/100;
		// E se a nova altura ainda é maior do que devia????
		if($nova_altura>$altura)
		{
			$nova_altura=$altura;
			$y=100*$nova_altura/imagesy($img_origem);
			$nova_largura=$y*imagesx($img_origem)/100;
		}
		//e se ficou mais pequena do que devia?
		//reduzimos a largura mas a altura pode ter ficado mais pequena do que devia
		if($nova_altura<$altura) // se a nova altura é menor do que a altura pretendida entao temos de reduzir menos
		{
			$nova_altura=$altura;
			$y=100*$nova_altura/imagesy($img_origem);
			$nova_largura=$y*imagesx($img_origem)/100;

		}
		if($nova_largura<$largura)
		{
			$nova_largura=$largura;
			$x=100*$nova_largura/imagesx($img_origem);
			$nova_altura=$x*imagesy($img_origem)/100;
		}
	}
	else
	{
		$nova_altura=$altura;
		$y=100*$nova_altura/imagesy($img_origem);
		$nova_largura=$y*imagesx($img_origem)/100;
		// E se a nova largura ainda é maior do que devia????

		if($nova_largura>$largura)
		{
			$nova_largura=$largura;
			$x=100*$nova_largura/imagesx($img_origem);
			$nova_altura=$x*imagesy($img_origem)/100;
		}
		if($nova_altura<$altura) // se a nova altura é menor do que a altura pretendida entao temos de reduzir menos
		{
			$nova_altura=$altura;
			$y=100*$nova_altura/imagesy($img_origem);
			$nova_largura=$y*imagesx($img_origem)/100;

		}
		if($nova_largura<$largura)
		{
			$nova_largura=$largura;
			$x=100*$nova_largura/imagesx($img_origem);
			$nova_altura=$x*imagesy($img_origem)/100;
		}
	}

	if($gd_version=='<2')
	{
		$img_destino=imagecreatetruecolor($nova_largura,$nova_altura);
		imagecopyresampled($img_destino,$img_origem,0,0,0,0,$nova_largura,$nova_altura,imagesx($img_origem),imagesy($img_origem));
	}
	else
	{
		$img_destino=imagecreate($nova_largura,$nova_altura);
		imagecopyresized($img_destino,$img_origem,0,0,0,0,$nova_largura,$nova_altura,imagesx($img_origem),imagesy($img_origem));
	}
	$ficheiro_destino=$path.$novo_nome;
	$nome_ficheiro_destino=$novo_nome;

	imagesavealpha($img_destino,true);
	@imagepng($img_destino,$ficheiro_destino);
	cropImage($path.$novo_nome,$path.$novo_nome,$largura,$altura);
	return $nome_ficheiro_destino;
}
function redimencionaJpeg($path,$imagem,$w,$h,$novo_nome,$tipo=0)
{
	$imagem=$path.$imagem;
	$novo_nome=$path.$novo_nome;
	if(!stream_resolve_include_path($imagem)) return "";
	$conf_compressao=100;
	if (!extension_loaded('gd') && !extension_loaded('gd2')) return false;

	if($imgInfo=@getimagesize($imagem))
	{
		//Ok
	}
	else return("ERRO: ".$imagem);

	switch ($imgInfo[2])
	{
		case 1: $im=@imagecreatefromgif($imagem); break;
		case 2: $im=@imagecreatefromjpeg($imagem);  break;
		case 3: $im=@imagecreatefrompng($imagem); break;
		default: "";  break;
	}
	if ($imgInfo[0] <= $w && $imgInfo[1] <= $h)
	{
		$nHeight=$imgInfo[1];
		$nWidth=$imgInfo[0];
	}
	else
	{
		if ($imgInfo[0] > $imgInfo[1])
		{
			$nWidth=$w;
			$nHeight=$imgInfo[1]*($w/$imgInfo[0]);
		}
		else
		{
			$nWidth=$imgInfo[0]*($h/$imgInfo[1]);
			$nHeight=$h;
		}
	}

	$nWidth=@round($nWidth);
	$nHeight=@round($nHeight);
	$newImg=@imagecreatetruecolor($nWidth,$nHeight);

	if(($imgInfo[2] == 1) || ($imgInfo[2]==3))
	{
		@imagealphablending($newImg, false);
		@imagesavealpha($newImg,true);
		$transparent=@imagecolorallocatealpha($newImg, 255, 255, 255, 127);
		@imagefilledrectangle($newImg, 0, 0,$nWidth,$nHeight,$transparent);
	}
	@imagecopyresampled($newImg,$im, 0, 0, 0, 0,$nWidth,$nHeight,$imgInfo[0],$imgInfo[1]);

	switch ($imgInfo[2])
	{
		case 1: @imagegif($newImg,$novo_nome); break;
		case 2: @imagejpeg($newImg,$novo_nome,100);  break;
		case 3: @imagepng($newImg,$novo_nome); break;
		default: "";  break;
	}
	return $novo_nome;
}

function cropImage($source,$dest,$nw,$nh,$align=0,$color='#FFFFFF')
{
	if(!stream_resolve_include_path($source)) return "";

	if($size=@getimagesize($source))
	{
		//Ok
	}
	else return("ERRO: ".$source);

	switch ($size[2])
	{
		case 1: $simg=@imagecreatefromgif($source); break;
		case 2: $simg=@imagecreatefromjpeg($source);  break;
		case 3: $simg=@imagecreatefrompng($source); break;
		default: ""; break;
	}

	$w=$size[0];
	$h=$size[1];

	$nw2=$nw;
	$nh2=$nh;

	$nw=$w;
	$nh=$h;

	$dimg=imagecreatetruecolor($nw2,$nh2);
	$white= imagecolorallocate($dimg,255,255,255);
	imagefill($dimg,0,0,$white);

	$xoffset=($nw2/2) - ($w/ 2);
	$yoffset=($nh2/2) - ($h/ 2);

	if($w > $h) imagecopyresampled($dimg,$simg,$xoffset,$yoffset,0,0,$nw,$nh,$w,$h);
	elseif(($w <$h) || ($w == $h)) imagecopyresampled($dimg,$simg,$xoffset,$yoffset,0,0,$nw,$nh,$w,$h);
	else imagecopyresampled($dimg,$simg,$xoffset,$yoffset,0,0,$nw,$nh,$w,$h);

	switch ($size[2])
	{
		case 1: @imagegif($dimg,$dest); break;
		case 2: @imagejpeg($dimg,$dest,100);  break;
		case 3: @imagepng($dimg,$dest); break;
		default: "";  break;
	}
	return 1;
}
function html2rgb($color)
{
	if ($color[0] == '#') $color=substr($color, 1);
	if (strlen($color) == 6) list($r,$g,$b)=array($color[0].$color[1],$color[2].$color[3],$color[4].$color[5]);
	elseif (strlen($color) == 3) list($r,$g,$b)=array($color[0].$color[0],$color[1].$color[1],$color[2].$color[2]);
	else return false;
	$r=hexdec($r); $g=hexdec($g); $b=hexdec($b);
	return array($r,$g,$b);
}

function redimencionaPNG($path,$foto,$largura,$altura,$novo_nome,$tipo=0,$path2='')
{
	$conf_compressao=100;
	$ficheiro_origem=$path.$foto;
	if($path2)$ficheiro_destino=$path2.$foto;
	$nome_ficheiro_destino=$foto;

	if($size=@getimagesize($ficheiro_origem)){/*Ok*/}
	else return false;

	switch($size[2])
	{
		case 1: $img_origem=@imagecreatefromgif($ficheiro_origem);break;
		case 2: $img_origem=@imagecreatefromjpeg($ficheiro_origem);break;
		case 3: $img_origem=@imagecreatefrompng($ficheiro_origem);break;
		default: ''; break;
	}
	$testGD=get_extension_funcs('gd');
	if(in_array('imagegd2',$testGD)) $gd_version='<2';


	$width=imagesx($img_origem);
	$height=imagesy($img_origem);
	if($largura>$width) $largura=$width;
	if($altura>$height) $altura=$height;

	if($width >=$height)
	{
		$nova_largura=$largura;
		$x=100*$nova_largura/imagesx($img_origem);
		$nova_altura=$x*imagesy($img_origem)/100;
		// E se a nova altura ainda é maior do que devia????
		if($nova_altura>$altura)
		{
			$nova_altura=$altura;
			$y=100*$nova_altura/imagesy($img_origem);
			$nova_largura=$y*imagesx($img_origem)/100;
		}
		//e se ficou mais pequena do que devia?
		//reduzimos a largura mas a altura pode ter ficado mais pequena do que devia
		if($nova_altura<$altura) // se a nova altura é menor do que a altura pretendida entao temos de reduzir menos
		{
			$nova_altura=$altura;
			$y=100*$nova_altura/imagesy($img_origem);
			$nova_largura=$y*imagesx($img_origem)/100;
		}
	}
	else
	{
		$nova_altura=$altura;
		$y=100*$nova_altura/imagesy($img_origem);
		$nova_largura=$y*imagesx($img_origem)/100;
		// E se a nova largura ainda é maior do que devia????
		if($nova_largura>$largura)
		{
			$nova_largura=$largura;
			$x=100*$nova_largura/imagesx($img_origem);
			$nova_altura=$x*imagesy($img_origem)/100;
		}
		if($nova_largura<$largura)
		{
			$nova_largura=$largura;
			$x=100*$nova_largura/imagesx($img_origem);
			$nova_altura=$x*imagesy($img_origem)/100;
		}
	}
	if($gd_version=='<2')
	{
		$img_destino=imagecreatetruecolor($nova_largura,$nova_altura);

		$bg=ImageColorAllocateAlpha($img_destino,255,255,255,127); //(PHP 4 >=4.3.2,PHP 5)
		ImageFill($img_destino,0,0 ,$bg);
		//imagecopy($img_destino,$img_origem, 0, 0,$nova_largura,$nova_altura, imagesx($img_destino), imagesy($img_destino));
		imagecopyresampled($img_destino,$img_origem,0,0,0,0,$nova_largura,$nova_altura,imagesx($img_origem),imagesy($img_origem));
	}
	else
	{
		$img_destino=imagecreate($nova_largura,$nova_altura);

		$bg=ImageColorAllocateAlpha($img_destino,255,255,255,127); //(PHP 4 >=4.3.2,PHP 5)
		ImageFill($img_destino,0,0 ,$bg);
		//imagecopy($img_destino,$img_origem, 0, 0,$nova_largura,$nova_altura, imagesx($img_destino), imagesy($img_destino));
		imagecopyresized($img_destino,$img_origem,0,0,0,0,$nova_largura,$nova_altura,imagesx($img_origem),imagesy($img_origem));
	}
	if($path2)$ficheiro_destino=$path2.$novo_nome;
	else $ficheiro_destino=$path.$novo_nome;
	$nome_ficheiro_destino=$novo_nome;

	imagesavealpha($img_destino,true);
	imagepng($img_destino,$ficheiro_destino);

	return $nome_ficheiro_destino;
}
function smart_resize_image($file,$width=0,$height=0,$proportional=false,$output='file',$delete_original=true,$use_linux_commands=false)
{
	if($height <=0 && $width <=0)return false;

	if($info=@getimagesize($file))
	{
		//Ok
	}
	else return("ERRO: ".$file);

	$img='';

	$final_width=0;
	$final_height=0;
	list($width_old,$height_old)=$info;
	//Se os tamanhos forem os mesmos da original nao faz nada
	if($height == $height_old && $width ==$width_old)
	{
		copia_segura($file,$output);
		return true;
	}

	if($proportional)
	{
		if($width==0) $factor=$height/$height_old;
		elseif($height==0) $factor=$width/$width_old;
		else $factor=min($width / $width_old,$height / $height_old);
		$final_width=round($width_old * $factor);
		$final_height=round($height_old * $factor);
	}
	else
	{
		$final_width=($width <=0) ? $width_old : $width;
		$final_height=($height <=0) ? $height_old : $height;
	}

	switch($info[2])
	{
		case IMAGETYPE_GIF:
			$img=imagecreatefromgif($file);
		break;
		case IMAGETYPE_JPEG:
			$img=imagecreatefromjpeg($file);
		break;
		case IMAGETYPE_PNG:
			$img=imagecreatefrompng($file);
		break;
		default:
			return false;
	}

	$img_resized=imagecreatetruecolor($final_width,$final_height);

	if(($info[2]==IMAGETYPE_GIF) || ($info[2]==IMAGETYPE_PNG))
	{
	   $trnprt_indx=imagecolortransparent($img);
	   if($trnprt_indx >=0)// If we have a specific transparent color
	   {
		 $trnprt_color=imagecolorsforindex($img,$trnprt_indx);// Get the original image's transparent color's RGB values
		 $trnprt_indx  =imagecolorallocate($img_resized,$trnprt_color['red'],$trnprt_color['green'],$trnprt_color['blue']);// Allocate the same color in the new image resource
		 imagefill($img_resized,0,0,$trnprt_indx);// Completely fill the background of the new image with allocated color.
		 imagecolortransparent($img_resized,$trnprt_indx);// Set the background color for new image to transparent
	  }
	   elseif($info[2]==IMAGETYPE_PNG) // Always make a transparent background color for PNGs that don't have one allocated already
	   {
		 imagealphablending($img_resized,false);// Turn off transparency blending(temporarily)
		 $color=imagecolorallocatealpha($img_resized,0,0,0,127);// Create a new transparent color for image
		 imagefill($img_resized,0,0,$color);// Completely fill the background of the new image with allocated color.
		 imagesavealpha($img_resized,true);// Restore transparency blending
	   }
	}
	imagecopyresampled($img_resized,$img,0,0,0,0,$final_width,$final_height,$width_old,$height_old);
	if($delete_original)
	{
		/*if($use_linux_commands)exec('rm '.$file);
		else*/ @unlink($file);
	}
	switch(strtolower($output))
	{
	   case 'browser':
			$mime=image_type_to_mime_type($info[2]);
			header('Content-type: $mime');
			$output=NULL;
	   break;
	   case 'file':
			$output=$file;
	   break;
	   case 'return':
			return $img_resized;
	   break;
			default:
	   break;
	 }

	switch($info[2])
	{
	   case IMAGETYPE_GIF:
		 imagegif($img_resized,$output);
	   break;
	   case IMAGETYPE_JPEG:
		 imagejpeg($img_resized,$output,100);
	   break;
	   case IMAGETYPE_PNG:
		 imagepng($img_resized,$output);
	   break;
	   default:
		 return false;
	}
		return true;
}

function putWatermarkImage($path,$imagem)
{
		global $site_shell_url;
		$testGD=get_extension_funcs("gd"); // Grab function list
		if (in_array ("imagegd2",$testGD)) $gd_version="<2"; // Check

		if($info=@getimagesize($path.$imagem))
		{
			//Ok
		}
		else return("ERRO: ".$path.$imagem);

		switch($info[2])
		{
			case IMAGETYPE_GIF:
				$img_origem=imagecreatefromgif($path.$imagem);
			break;
			case IMAGETYPE_JPEG:
				$img_origem=imagecreatefromjpeg($path.$imagem);
			break;
			case IMAGETYPE_PNG:
				$img_origem=imagecreatefrompng($path.$imagem);
			break;
			default:
				return false;
		}


		$width=imagesx($img_origem);
		$height=imagesy($img_origem);
		$w=($width/2);
		$h=($height/2);

		/************************** REDIMENSIONA WATERMARK ****************/
		$watermark=imagecreatefrompng($site_shell_url.'layout/WATERMARK.png');
		$w2=imagesx($watermark);
		$h2=imagesy($watermark);
		if($w2>$w || $h2>$h)
		{
			if($w2 - $w>$h2 - $h) $racio=(100*$w)/$w2;
			else $racio=(100*$h)/$h2;

			$w3=$w2*$racio/100;
			$h3=$h2*$racio/100;
		}
		else
		{
			$w3=$w2;
			$h3=$h2;
		}

		$new_watermark=imagecreatetruecolor($w3,$h3);
		imagealphablending($new_watermark,false);
		$color=imagecolortransparent($new_watermark,imagecolorallocatealpha($new_watermark,230,10,0,127));
		imagefill($new_watermark,0,0,$color);
		imagesavealpha($new_watermark,true);
		imagecopyresampled($new_watermark,$watermark,0,0,0,0,$w3,$h3,$w2,$h2);
		//******************** FIM DE REDIMENSIONAR A WATERMARK ******************************/
		$dest_x=($width/2)-($w3/2);//$width - $w3;
		$dest_y=($height/2) - ($h3/2); // para colocar a marca de agua no final da foto

		if($gd_version == "<2")
		{
			$img_destino=imagecreatetruecolor($width,$height);
			imagealphablending($img_destino,true);
			imagecopyresampled($img_destino,$img_origem,0,0,0,0,$width,$height,$width,$height);
			imagecopyresampled($img_destino,$new_watermark,$dest_x,$dest_y,0,0,$w3,$h3,$w3,$h3);
		}
		else
		{
			$img_destino=imagecreate($width,$height);
			imagecopyresized($img_destino,$img_origem,0,0,0,0,$width,$height,$width,$height);
			imagecopyresized($img_destino,$new_watermark,$dest_x,$dest_y,0,0,$watermark_width,$watermark_height,$w3,$h3);
		}
		imagesavealpha($new_image,true);

		switch($info[2]) {
		   case IMAGETYPE_GIF:
			 imagegif($img_destino,$path.$imagem);
		   break;
		   case IMAGETYPE_JPEG:
			 imageJPEG($img_destino,$path.$imagem,100);
		   break;
		   case IMAGETYPE_PNG:
			 imagepng($img_destino,$path.$imagem);
		   break;
		   default:
			 return false;
		 }


		return $imagem;
	}

	function compressPNG($src,$dts)
{
	return true;
	if(!$dts)$dts=$sql;
	/**
	 * Image PNG
	 * Take a PNG image and create a thumbnail, maintaining alpha transparency for a png-24 image.
	 */
	$m=microtime(true);
	$img=$src;
	// Size
	$s=getimagesize( $img );
	$w=imagesx($img);
	$h=imagesy($img);
	// Source
	$i=imagecreatefrompng( $img );
	// Destination
	$d=imagecreatetruecolor($w,$h);

	// if this has no alpha transparency defined as an index
	// it could be a palette image??
	$palette=(imagecolortransparent($i)<0);

	// If this has transparency, or is defined
	if(!$palette||(ord(file_get_contents ($img, false, null, 25, 1)) & 4)){
		//print ("Is Alpha");
		// Has indexed transparent color
		if(($tc=imagecolorstotal($i))&&$tc<=256)
			imagetruecolortopalette($d, false,$tc);
		imagealphablending($d, false);
		$alpha=imagecolorallocatealpha($d, 0, 0, 0, 127);
		imagefill($d, 0, 0,$alpha);
		imagesavealpha($d, true);
		//var_dump(microtime(true)-$m);
	}

	// Resample Image
	//print ("Resampling Image");
	imagecopyresampled($d,$i, 0, 0, 0, 0,$w,$h,$s[0],$s[1]);
	//var_dump(microtime(true)-$m);

	// Did the original PNG supported Alpha?
	if((ord(file_get_contents ($img, false, null, 25, 1)) & 4)){
		//print ("Testing is there is Alpha transparency");

		// we dont have to check every pixel.
		// We take a sample of 2500 pixels (for images between 50X50 up to 500X500), then 1/100 pixels thereafter.
		$dx=min(max(floor($w/50),1),10);
		$dy=min(max(floor($h/50),1),10);

		$palette=true;
		for($x=0;$x<$w;$x=$x+$dx){
			for($y=0;$y<$h;$y=$y+$dy){
				$col=imagecolorsforindex($d, imagecolorat($d,$x,$y));
				// How transparent until it's actually visible
				// I reackon atleast 10% of 127 before its noticeable, e.g. ~13
				if($col['alpha']>13){
					//print_r($col);
					$palette=false;
					break 2;
				}
			}
		}
		//var_dump(microtime(true)-$m);
		//var_dump( !$palette );
	}

	if($palette){
		//print "Converting to indexed colors";
		imagetruecolortopalette($d, false, 256);
		//var_dump(microtime(true)-$m);
	}

	// Save file, quality=9, Add filters... although sometimes better without.
	//die($dts);
	imagepng( $d,$dts, 1, PNG_ALL_FILTERS);
}


function imagem_recize_png_transparent($src='',$new='',$w=0,$h=0,$cache=0)
{
	$path=$GLOBALS['site_shell_url'].'files/';
	if(!stream_resolve_include_path($path.$src))return false;
	$cache_date=filemtime($path.$src);
	$src_date=date("Y-m-d H:i:s",$cache_date);
	if(stream_resolve_include_path($path.$new))$new_date=date("Y-m-d H:i:s",filemtime($path.$new));
	if(!stream_resolve_include_path($path.$new) || $new_date<$src_date)
	{
		if($size=@getimagesize($path.$src)){/*Ok*/}
		else return false;
		$testGD=get_extension_funcs('gd');
		if(in_array('imagegd2',$testGD)) $gd_version='<2';
		$conf_compressao=100;
		switch($size[2])
		{
			case 1: $img=@imagecreatefromgif($path.$src);break;
			case 2: $img=@imagecreatefromjpeg($path.$src);break;
			case 3: $img=@imagecreatefrompng($path.$src);break;
			default: ''; break;
		}
		/********** FASE 1 : REMOVER O BRANCO excedente e cortar a imagem pelas extremidades *****/
		//find the size of the borders
		$b_top=0;
		$b_btm=0;
		$b_lft=0;
		$b_rt=0;
		//top
		for(; $b_top < imagesy($img); ++$b_top) {
		  for($x=0; $x < imagesx($img); ++$x) {
			if(imagecolorat($img,$x,$b_top) != 0xFFFFFF) {
			   break 2; //out of the 'top' loop
			}
		  }
		}
		//bottom
		for(; $b_btm < imagesy($img); ++$b_btm) {
		  for($x=0; $x < imagesx($img); ++$x) {
			if(imagecolorat($img,$x, imagesy($img) - $b_btm-1) != 0xFFFFFF) {
			   break 2; //out of the 'bottom' loop
			}
		  }
		}
		//left
		for(; $b_lft < imagesx($img); ++$b_lft) {
		  for($y=0; $y < imagesy($img); ++$y) {
			if(imagecolorat($img,$b_lft,$y) != 0xFFFFFF) {
			   break 2; //out of the 'left' loop
			}
		  }
		}
		//right
		for(; $b_rt < imagesx($img); ++$b_rt) {
		  for($y=0; $y < imagesy($img); ++$y) {
			if(imagecolorat($img, imagesx($img) - $b_rt-1,$y) != 0xFFFFFF) {
			   break 2; //out of the 'right' loop
			}
		  }
		}
		//copy the contents, excluding the border
		$newimg=imagecreatetruecolor(imagesx($img)-($b_lft+$b_rt), imagesy($img)-($b_top+$b_btm));
		$bg=ImageColorAllocateAlpha($newimg,255,255,255,127); //(PHP 4 >=4.3.2,PHP 5)
		ImageFill($newimg,0,0 ,$bg);
		imagecopy($newimg,$img, 0, 0,$b_lft,$b_top, imagesx($newimg), imagesy($newimg));
		imagesavealpha($newimg,true);
		$img=$newimg;
		/******************************************/
		/********** FASE 2 : REDIMENSIONAR A IMAGEM *****/

		$width_old=imagesx($img);
		$height_old=imagesy($img);
		if($w == 0)$factor=$h/$height_old;
		elseif($h == 0)$factor=$w/$width_old;
		else $factor=min($w/$width_old,$h / $height_old);
		$nova_largura=round($width_old*$factor);
		$nova_altura=round($height_old*$factor);

		if($gd_version=='<2')
		{
			$img_destino=imagecreatetruecolor($nova_largura,$nova_altura);
			imagecopyresampled($img_destino,$img,0,0,0,0,$nova_largura,$nova_altura,imagesx($img),imagesy($img));
		}
		else
		{
			$img_destino=imagecreate($nova_largura,$nova_altura);
			imagecopyresized($img_destino,$img,0,0,0,0,$nova_largura,$nova_altura,imagesx($img),imagesy($img));
		}
		imagesavealpha($img_destino,true);
		$img=$img_destino;

		//Fase 3 **** Cortar a imagem
		//Fase 4 **** Colocar transparente a imagem
		$imagex=imagesx($img);
		$imagey=imagesy($img);
		if($gd_version=='<2')$img_destino=imagecreatetruecolor($imagex,$imagey);
		else $img_destino=imagecreate($imagex,$imagey);
		// Set a White & Transparent Background Color
		$bg=ImageColorAllocateAlpha($img_destino,255,255,255,127); //(PHP 4 >=4.3.2,PHP 5)
		ImageFill($img_destino,0,0 ,$bg);
		for($x=0;$x <$imagex; ++$x)
		{
			for($y=0;$y <$imagey; ++$y)
			{
				$rgb=imagecolorat($img,$x,$y);
				$alpha=($rgb >> 24) & 0x7F;
				if($alpha < 100)
				{
					$red=($rgb >> 16) & 0xFF;
					$green=($rgb >> 8) & 0xFF;
					$blue=$rgb & 0xFF;
					if($red>=250 && $green>=250 && $blue>=250){/*branco*/}
					else
					{
						$newcol=imageColorAllocateAlpha($img,$red,$green,$blue,$alpha);
						imagesetpixel($img_destino,$x,$y,$newcol);
					}
				}
			}
		}
		imagesavealpha($img_destino,true);
		//Fase 5 Gravar a imagem final
		@imagepng($img_destino,$path.$new);
		//Fase 6 verificar se a imagem ficou bem gravada
		if($size_new=@getimagesize($path.$new))
		{
			/*Ok*/
			if($cache)$new.='?dp='.$cache_date;
			return $new;
		}
		else
		{
			if(stream_resolve_include_path($path.$new))unlink($path.$new);
			return false;
		}
	}
}




/*function convert_webp($image_src='')
{
	if($size=@getimagesize($image_src)){}
	else return false;
	$testGD=get_extension_funcs('gd');
	if(in_array('imagegd2',$testGD)) $gd_version='<2';
	switch($size[2])
	{
		case 1: $img=@imagecreatefromgif($GLOBALS['site_shell_url'].$image_src);break;
		case 2: $img=@imagecreatefromjpeg($GLOBALS['site_shell_url'].$image_src);break;
		case 3: $img=@imagecreatefrompng($GLOBALS['site_shell_url'].$image_src);break;
		default: ''; break;
	}
	$image_src=str_replace('.png','.webp',$image_src);
	imagewebp($img,$GLOBALS['site_shell_url'].$image_src);
	return $image_src;
}
*/

function convert_webp($image_src='')
{
	$src=$GLOBALS['site_shell_url'].$image_src;
	$image_src=str_replace('.png','.webp',$image_src);
	$dest=$GLOBALS['site_shell_url'].$image_src;
	exec('cwebp -q 100 '.$src.' -o '.$dest);
	return $image_src;
}


function redim_fill($path,$file,$new_width=0,$new_height=0,$file2)
{
	if($height <=0 && $new_width <=0)return false;
	if($info=@getimagesize($path.$file)){}else return("ERRO: ".$file);
	$myImage='';

	$output=$path.$file2;

	$final_width=0;
	$final_height=0;
	//getting the image dimensions
	list($width,$height)=$info;
	//Se os tamanhos forem os mesmos da original nao faz nada
	if($new_height == $height && $new_width ==$width)
	{
		copy($path.$file,$output);
		return true;
	}

	//saving the image into memory (for manipulation with GD Library)
	switch($info[2])
	{
		case IMAGETYPE_GIF:
			$myImage=imagecreatefromgif($path.$file);
		break;
		case IMAGETYPE_JPEG:
			$myImage=imagecreatefromjpeg($path.$file);
		break;
		case IMAGETYPE_PNG:
			$myImage=imagecreatefrompng($path.$file);
		break;
		default:
			return false;
	}

	// calculating the part of the image to use for thumbnail
	if ($width > $height) {
	  $y=0;
	  $x=($width - $height) / 2;
	  $smallestSide=$height;
	} else {
	  $x=0;
	  $y=($height - $width) / 2;
	  $smallestSide=$width;
	}

    $thumb=imagecreatetruecolor($new_width,$new_height);
	imagecopyresampled($thumb,$myImage, 0, 0,$x,$y,$new_width,$new_height,$smallestSide,$smallestSide);

	//final output
    switch($info[2])
	{
       case IMAGETYPE_GIF:
         imagegif($thumb,$output);
       break;
       case IMAGETYPE_JPEG:
         imagejpeg($thumb,$output,100);
       break;
       case IMAGETYPE_PNG:
         imagepng($thumb,$output);
       break;
       default:
         return false;
	}
	return true;
}


function imgtowepp($src='',$output=''){
	$file=$src;
	$image=imagecreatefromstring(file_get_contents($file));
	ob_start();
	imagejpeg($image,NULL,100);
	$cont=ob_get_contents();
	ob_end_clean();
	imagedestroy($image);
	$content=imagecreatefromstring($cont);
	imagewebp($content,$output);
	imagedestroy($content);
}














function make_transparent_new($u,$foto,$foto2)
{
	$x=0;
	$y=0;
	$color='#ff0000';
	$transparencia='5';
	$cor_rgb=hex2rgb($color);
	fload_fill($x,$y,$u,$foto,$foto2,$cor_rgb['r'],$cor_rgb['g'],$cor_rgb['b'],$transparencia,$color);
}


//------------------------
function fload_fill($x,$y,$u,$foto,$foto2,$r,$g,$b,$toler,$color)
{
	setMemoryForImage($u.$foto); // vamos alocar memoria para esta imagem
	set_time_limit(0);

	
	if($size=@getimagesize($u.$foto)){}else echo "ERRO: ".$u.$foto ;
	switch($size[2])
	{
		case 1: $img_origem=@imagecreatefromgif($u.$foto); break;
		case 2: $img_origem=@imagecreatefromjpeg($u.$foto); break;
		case 3: $img_origem=@imagecreatefrompng($u.$foto); break;
		default: ''; break;
	}
	$w=$size[0];
	$h=$size[1];


	
	$cor_original = imagecolorat($img_origem, $x, $y);	
	$nova_cor = imagecolorallocate($img_origem,$r,$g,$b);
	$img_destino=pinta_imagem($img_origem,$x,$y,$w,$h,$nova_cor,$cor_original,$toler,$nova,$color);
	
	//$img_destino=imagecreatetruecolor($w,$h);
	//imagecopy($img_destino, $img_origem, 0, 0, 0,0,$w,$h);
	imagepng($img_destino,$u.$foto2);
	if(file_exists($u.$foto2)) return $foto2;
	else return '';
}


function hex2rgb( $colour ) {
        if ( $colour[0] == '#' ) {
                $colour = substr( $colour, 1 );
        }
        if ( strlen( $colour ) == 6 ) {
                list( $r, $g, $b ) = array( $colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5] );
        } elseif ( strlen( $colour ) == 3 ) {
                list( $r, $g, $b ) = array( $colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2] );
        } else {
                return false;
        }
        $r = hexdec( $r );
        $g = hexdec( $g );
        $b = hexdec( $b );
        return array( 'r' => $r, 'g' => $g, 'b' => $b );
}
function pinta_imagem($img,$x,$y,$w,$h,$nova_cor,$corClicada,$toler=10,$nova=1,$color_passada)
{
	$x = intval($x);
	$y = intval($y);
	$w = intval($w);
	$h = intval($h);

	$new_image=imagecreatetruecolor($w,$h);//create Image.
	imagealphablending($new_image,false);//for transparent background.
	imagesavealpha($new_image,true);
	//$colortransparent=imagecolortransparent($new_image,imagecolorallocatealpha($new_image,230,10,0,127));
	$colortransparent=imagecolorallocatealpha($new_image,255,255,255,127);
	imagefill($new_image, 0, 0, $colortransparent);
	//imagesavealpha($new_image,true);

	imagecopyresized($new_image,$img,0,0,0,0,$w,$h,$w,$h);

	//$toler=5;
	$tolerancia=$toler;
	//Vamos criar uma matriz

	$i=1;
	while($i<=$w)
	{
		$j=1;
		while($j<=$h)
		{
  			$array_history[$i][$j]=0;
  			$j++;
		}
		$i++;
	}
	
	$array[]=$x.'x'.$y; 

	$c0 = imagecolorsforindex($img,$nova_cor);
	$red = $c0['red'];
	$green = $c0['green'];
	$blue = $c0['blue'];
	$nova_cor_red_hex = ($red); // Convert to decimal
	$nova_cor_green_hex = ($green); // Convert to decimal
	$nova_cor_blue_hex = ($blue); // Convert to decimal
	$c2 = imagecolorsforindex($img, $corClicada);	

	while(count($array)>0)
	{
		$x_y = array_pop($array);
		$x_y = explode('x',$x_y);
		$x=$x_y[0];
		$y=$x_y[1];
		//echo 'x:'.$x.' y:'.$y;
		//*****************************//
		$flag=1;
		
		if ($x < 0 || $x >= $w || $y < 0 || $y >= $h || $array_history[$x][$y]==$flag) {}
		else
		{
			$array_history[$x][$y]=1;

		   //Agora vamos verificar se a cor clicada é parecida com a do pixel que vamos pintar:
		   $corPixelPintar = imagecolorat($img, $x, $y);
		   $c1 = imagecolorsforindex($img, $corPixelPintar);
			
		   $diffRed   = abs($c1['red'] - $c2['red']);
			$diffGreen = abs($c1['green'] - $c2['green']);
			$diffBlue  = abs($c1['blue'] - $c2['blue']);
			
			$ajuste=($c1['red']+$c1['green']+$c1['blue'])-($c2['red']+$c2['green']+$c2['blue']);
			if($ajuste > 0 )$ajuste = 1;else $ajuste = -1;

			$pctDiffRed   = $diffRed   / 255;
			$pctDiffGreen = $diffGreen / 255;
			$pctDiffBlue  = $diffBlue  / 255;
			$perc = ($pctDiffRed + $pctDiffGreen + $pctDiffBlue) / 3 * 100;
			$perc_red=$diffRed/100;
			$perc_green=$diffGreen/100;
			$perc_blue=$diffBlue/100;
			$perc_compara=0;

			
		   if ( $perc > $tolerancia  )
			{
				if ( $perc - $tolerancia < 5 )
				{
					$PintaComEstaCor = imagecolorallocatealpha($new_image,255,255,255,100);
					//$PintaComEstaCor = imagecolorallocate($new_image,255,255,255);
					imagesetpixel($new_image,$x,$y,$PintaComEstaCor);

					if($x+1 < $w){$array[]=($x+1).'x'.($y);}
					if($y-1 > 0 ){$array[]=($x).'x'.($y-1);}
					if($x-1 > 0 ){$array[]=($x-1).'x'.$y;}
					if($y+1 < $h){$array[]=($x).'x'.($y+1);}

				}
				else
				{
					//nao faz nada
					$array_history[$x][$y]=0;
				}
			}
			else
	    	{
		    	//$PintaComEstaCor = imagecolorallocate($new_image,$red,$green,$blue);
		    	$PintaComEstaCor = imagecolorallocatealpha($new_image,255,255,255,127);
				imagesetpixel($new_image,$x,$y,$PintaComEstaCor);
				// Recur for north, east, south and west

				if($x+1 < $w){$array[]=($x+1).'x'.($y);}
				if($y-1 > 0 ){$array[]=($x).'x'.($y-1);}
				if($x-1 > 0 ){$array[]=($x-1).'x'.$y;}
				if($y+1 < $h){$array[]=($x).'x'.($y+1);}			

			}
		}
	}


   return $new_image;
}
?>
