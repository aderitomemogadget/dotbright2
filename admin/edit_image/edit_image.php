<?
	header('Content-Type: text/html; charset=utf-8');
	$path = '../../core/';
	require_once $path . 'core_files.php';
//  require_once $path . 'admin_globals.php';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<title>EDIT IMAGE</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?
	$src=$_GET["src"];
	$reset=$_GET["reset"];
	$crop=$_GET["crop"];
	$w=$_GET["w"];
	$h=$_GET["h"];
	$x1=$_GET["x1"];
	$x2=$_GET["x2"];
	$y1=$_GET["y1"];
	$y2=$_GET["y2"];
	$perspectiva=$_GET["perspectiva"];
	$duotone=$_GET["duotone"];
	$sepia=$_GET["sepia"];

	$original=str_replace(".jpg","_org.jpg",$src);
	$original=str_replace(".JPG","_org.jpg",$original);
	$original=str_replace(".JPEG","_org.jpg",$original);
	$original=str_replace(".png","_org.png",$original);
	$original=str_replace(".PNG","_org.png",$original);


	if(!file_exists($src)){echo 'Imagem não disponivel'; die();}
	else
	{
		//Faz backup
		if(!file_exists($original))
		{
			if(!copia_segura($src,$original))die("ERRO a FAZER BACKUP!! ");
		}
	}

	if($rotate)imagem_rotate($src,$rotate);
	//if($redim)imagem_redim($folder,$file,300,300,$file);
	if($perspectiva)imagem_perspective($src);
	if($duotone)imagem_duotone($src);
	if($sepia)imagem_sepia($src);
	if($reset && file_exists($original)) copia_segura($original,$src);


	if($crop)
	{
		if($x2==0 || $y2==0)
		{
			smart_resize_image($src,$w,$h,true,$src,false,false);
		}
		else
		{
			$w2=$x2-$x1;
			$h2=$y2-$y1;
			$source=$src;
			setMemoryForImage($source);
			if(!file_exists($source)) return '';

			if($size=@getimagesize($source))
			{
				//Ok
			}
			else return("ERRO: ".$source);
			switch ($size[2])
			{
				case 1: $simg = @imagecreatefromgif($source); break;
				case 2: $simg = @imagecreatefromjpeg($source);  break;
				case 3: $simg = @imagecreatefrompng($source); break;
				default: ''; break;
			}
			$img_destino = imagecreatetruecolor($w2,$h2);
			imagecopy($img_destino, $simg, 0, 0, $x1, $y1, $w2, $h2 );

			imagesavealpha($img_destino,true);
			@imagepng($img_destino,$src);
			//Depois de cortada a imagem vamos colocar no tamanho correcto
			smart_resize_image($src,$w,$h,true,$src,false,false);
		}
	}
	?>
<link rel="stylesheet" type="text/css" href="geral.css"/>
<link rel="stylesheet" type="text/css" href="jquery.imgareaselect-0.9.8/css/imgareaselect-default.css" />
<script type="text/javascript" src="jquery.imgareaselect-0.9.8/scripts/jquery.min.js"></script>
<script type="text/javascript" src="jquery.imgareaselect-0.9.8/scripts/jquery.imgareaselect.pack.js"></script>

<script type="text/javascript">
var x1=0;
var x2=0;
var y1=0;
var y2=0;

$(document).ready(function () {
	$('img#photo').imgAreaSelect({
		aspectRatio: '1:<?=($h*1/$w)?>',
		x1: 0, y1: 0, x2: <?=$w?>, y2: <?=$h?>,
		handles: true,
		fadeSpeed : 1,
		onSelectEnd: function (img, selection) {
			x1=selection.x1;
			x2=selection.x2;
			y1=selection.y1;
			y2=selection.y2;
		}
	});
});

function crop()
{
	var url="edit_image.php?w=<?=$w?>&h=<?=$h?>&src=<?=$src?>&x1="+x1+"&x2="+x2+"&y1="+y1+"&y2="+y2+"&crop=1&dump=<?=uniqid()?>";
	window.open(url,"_self");
}
function reset()
{
	var url="edit_image.php?w=<?=$w?>&h=<?=$h?>&src=<?=$src?>&x1="+x1+"&x2="+x2+"&y1="+y1+"&y2="+y2+"&reset=1&dump=<?=uniqid()?>";
	window.open(url,"_self");
}
function perspectiva()
{
	var url="edit_image.php?w=<?=$w?>&h=<?=$h?>&src=<?=$src?>&x1="+x1+"&x2="+x2+"&y1="+y1+"&y2="+y2+"&perspectiva=1&dump=<?=uniqid()?>";
	window.open(url,"_self");
}
function duotone()
{
	var url="edit_image.php?w=<?=$w?>&h=<?=$h?>&src=<?=$src?>&x1="+x1+"&x2="+x2+"&y1="+y1+"&y2="+y2+"&duotone=1&dump=<?=uniqid()?>";
	window.open(url,"_self");
}
function sepia()
{
	var url="edit_image.php?w=<?=$w?>&h=<?=$h?>&src=<?=$src?>&x1="+x1+"&x2="+x2+"&y1="+y1+"&y2="+y2+"&sepia=1&dump=<?=uniqid()?>";
	window.open(url,"_self");
}
</script>
</head><table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
	<tr>
		<td valign="top">

						<table width="925px" border=0 cellspacing=0 cellpadding="10" bgcolor="#FFFFFF">
						<tr>
							<td align="left" valign="top" class=preto>
								<table width="100%" border=0 cellspacing=0 cellpadding=0>
								<tr>
									<td><img id="photo" src="<?=$src;?>?dp=<?=uniqid()?>" /></td>
								</tr>
								</table>
							</td>
						</tr>
						</table>




				<table width="925px" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
				<table width="925px" class=p1 border=0 cellspacing=0 cellpadding=0>
				<tr>
					<td width="5px"></td>
					<td align="left" valign="middle" class="t1">
						<input class="btn btn-primary" type="button" name="Fechar" value=" Fechar " onMouseUp="window.close()">&nbsp;
						<input class="btn btn-primary" type="button" name="Cortar imagem" value=" Cortar imagem " onMouseUp="javascript:crop()">&nbsp;
						<input class="btn btn-primary" type="button" name="Perspetivar imagem" value=" Perspetivar imagem " onMouseUp="javascript:perspectiva()">&nbsp;
						<input class="btn btn-primary" type="button" name="Duotone" value=" Duotone " onMouseUp="javascript:duotone()">&nbsp;
						<input class="btn btn-primary" type="button" name="Sepia" value=" Sepia " onMouseUp="javascript:sepia()">&nbsp;
						<input class="btn btn-primary" type="button" name="RESET" value=" RESET " onMouseUp="javascript:reset()">
					</td>
				 </tr>
				</table>

	  </td>
	</tr>
	</table>
</body>
</html>
<?

function imagem_rotate($imagem,$degrees)
{
	if(file_exists($imagem))
	{
		$size = @getimagesize($imagem);
		switch ($size[2])
		{
			case 1: $simg = @imagecreatefromgif($imagem); break;
			case 2: $simg = @imagecreatefromjpeg($imagem);  break;
			case 3: $simg = @imagecreatefrompng($imagem); break;
			default: ""; break;
		}
		$rotate = imagerotate($simg, $degrees, 0);
		switch ($size[2])
		{

			case 1: @imagegif($rotate,$imagem); break;
			case 2: @imagejpeg($rotate,$imagem,100);  break;
			case 3: @imagepng($rotate,$imagem); break;
			default: "";  break;
		}
	}
}
function imagem_redim($path,$imagem,$largura,$altura,$novo_nome)
{
	$testGD=get_extension_funcs("gd");
	if(in_array ("imagegd2",$testGD)) $gd_version="<2";
	$conf_compressao=100;
	$ficheiro_origem=$path."/".$imagem;
	$ficheiro_destino=$ficheiro_origem;
	$nome_ficheiro_destino=$imagem;


	if(file_exists($ficheiro_origem))
	{
		$size = @getimagesize($ficheiro_origem);
		switch ($size[2])
		{
			case 1: $img_origem = @imagecreatefromgif($ficheiro_origem); break;
			case 2: $img_origem = @imagecreatefromjpeg($ficheiro_origem);  break;
			case 3: $img_origem = @imagecreatefrompng($ficheiro_origem); break;
			default: ""; break;
		}
	}

	$width=imagesx($img_origem);
	$height=imagesy($img_origem);
	if($largura>$width) $largura=$width;
	if($altura>$height) $altura=$height;

	if($width >=$height)
	{
			$nova_largura=$largura;
			$x=100*$nova_largura/imagesx($img_origem);
			$nova_altura=$x*imagesy($img_origem)/100;
			// E se a nova altura ainda � maior do que devia????
			if($nova_altura>$altura)
			{
				$nova_altura=$altura;
				$y=100*$nova_altura/imagesy($img_origem);
				$nova_largura=$y*imagesx($img_origem)/100;
			}
			if($gd_version=="<2")
			{
				$img_destino=imagecreatetruecolor($nova_largura,$nova_altura);
				imagecopyresampled($img_destino,$img_origem,0,0,0,0,$nova_largura,$nova_altura,imagesx($img_origem),imagesy($img_origem));
			}
			else
			{
				$img_destino=imagecreate($nova_largura,$nova_altura);
				imagecopyresized($img_destino,$img_origem,0,0,0,0,$nova_largura,$nova_altura,imagesx($img_origem),imagesy($img_origem));
			}
			$ficheiro_destino=$path."/".$novo_nome;
			$nome_ficheiro_destino=$novo_nome;
			imageJPEG($img_destino,$ficheiro_destino,$conf_compressao);
	}
	else
	{
			$nova_altura=$altura;
			$y=100*$nova_altura/imagesy($img_origem);
			$nova_largura=$y*imagesx($img_origem)/100;
			// E se a nova largura ainda � maior do que devia????
			if($nova_largura>$largura)
			{
				$nova_largura=$largura;
				$x=100*$nova_largura/imagesx($img_origem);
				$nova_altura=$x*imagesy($img_origem)/100;
			}
			if($gd_version=="<2")
			{
				$img_destino=imagecreatetruecolor($nova_largura,$nova_altura);
				imagecopyresampled($img_destino,$img_origem,0,0,0,0,$nova_largura,$nova_altura,imagesx($img_origem),imagesy($img_origem));
			}
			else
			{
				$img_destino=imagecreate($nova_largura,$nova_altura);
				imagecopyresized($img_destino,$img_origem,0,0,0,0,$nova_largura,$nova_altura,imagesx($img_origem),imagesy($img_origem));
			}
			$ficheiro_destino=$path."/".$novo_nome;
			$nome_ficheiro_destino=$novo_nome;
			imageJPEG($img_destino,$ficheiro_destino,$conf_compressao);
	}
	return $nome_ficheiro_destino;
}
function imagem_perspective($imagem,$degrees)
{
	if(file_exists($imagem))
	{
		$size = @getimagesize($imagem);
		switch ($size[2])
		{
			case 1: $simg = @imagecreatefromgif($imagem); break;
			case 2: $simg = @imagecreatefromjpeg($imagem);  break;
			case 3: $simg = @imagecreatefrompng($imagem); break;
			default: ""; break;
		}
		$rotate = perspectiva($simg);
		switch ($size[2])
		{

			case 1: @imagegif($rotate,$imagem); break;
			case 2: @imagejpeg($rotate,$imagem,100);  break;
			case 3: @imagepng($rotate,$imagem); break;
			default: "";  break;
		}
	}
}
function perspectiva($i,$gradient=0.85,$rightdown=true,$background=0xFFFFFF)
{
	$mult=5;
	$w=imagesx($i);
	$h=imagesy($i);
	$image=imagecreatetruecolor($w*$mult,$h*$mult);
	imagecopyresized($image,$i,0,0,0,0,$w*$mult,$h*$mult,$w,$h);
	imagedestroy($i);
	$w*=$mult;
	$h*=$mult;
	$im=imagecreatetruecolor($w,$h);
	$background=imagecolorallocate($im,($background>>16)&0xFF,($background>>8)&0xFF,$background&0xFF);
	imagefill($im,0,0,$background);
	$nh=$h-($h*$gradient);
	for ($x=0;$x<$w;$x++)
	{
	 $ni=(($rightdown) ? $x : $w-$x);
	 $p=intval($h-(($ni/$w)*$nh));
	 if(($p%2)<>0)
	  $p-=1;
	 $nx=intval(($p-$h)/2);
	 imagecopyresampled($im,$image,$x,0,$x,$nx,1,$p,1,$h-1);
	 imageline($im,$x,0,$x,-$nx-1,$background);
	 imageline($im,$x,$h-1,$x,$h+$nx,$background);
	}
	imagedestroy($image);
	imagefilter($im,IMG_FILTER_SMOOTH,10);
	$i=imagecreatetruecolor($w/$mult,$h/$mult);
	imagecopyresampled($i,$im,0,0,0,0,$w,$h,$w*$mult,$h*$mult);
	imagedestroy($im);
	return $i;
}
function imagem_make_transparent($caminho, $imagem, $imagem2)
{
	$image_file = $imagem;
	$img_file_peq = $imagem2;
	if(file_exists($caminho."/".$imagem))
	{
		$size = @getimagesize($caminho."/".$imagem);
		switch ($size[2])
		{
			case 1: $simg = @imagecreatefromgif($caminho."/".$imagem); break;
			case 2: $simg = @imagecreatefromjpeg($caminho."/".$imagem);  break;
			case 3: $simg = @imagecreatefrompng($caminho."/".$imagem); break;
			default: ""; break;
		}
		$imagex = imagesx($simg);
		$imagey = imagesy($simg);
		$img_destino = imagecreatetruecolor($imagex,$imagey);
		// Set a White & Transparent Background Color
		$bg = ImageColorAllocateAlpha($img_destino, 255, 255, 255, 127); // (PHP 4 >= 4.3.2, PHP 5)
		ImageFill($img_destino, 0, 0 , $bg);
		for ($x = 0;$x <$imagex; ++$x)
		{
			for ($y = 0;$y <$imagey; ++$y)
			{
				$rgb = imagecolorat($simg, $x, $y);
				$alpha=($rgb >> 24) & 0x7F;
				if($alpha < 100)
				{
					$red = ($rgb >> 16) & 0xFF;
					$green = ($rgb >> 8) & 0xFF;
					$blue = $rgb & 0xFF;

					if($red>=240 && $green>=240 && $blue>=240)// encontrou brancos
					{
						//branco
					}
					else
					{
						$newcol = imageColorAllocateAlpha($simg, $red, $green, $blue, $alpha);
						imagesetpixel ($img_destino, $x, $y, $newcol);
					}
				}
			}
		}
		imagesavealpha($img_destino,true);
		imagepng($img_destino,$caminho."/".$imagem2);
		if($imagem != $imagem2) unlink($caminho."/".$imagem); //remover original
	}
}
function imagem_duotone($imagem,$degrees)
{
	if(file_exists($imagem))
	{
		$size = @getimagesize($imagem);
		switch ($size[2])
		{
			case 1: $simg = @imagecreatefromgif($imagem); break;
			case 2: $simg = @imagecreatefromjpeg($imagem);  break;
			case 3: $simg = @imagecreatefrompng($imagem); break;
			default: ""; break;
		}
		duotone($simg, 50,50,50);
		switch ($size[2])
		{

			case 1: @imagegif($simg,$imagem); break;
			case 2: @imagejpeg($simg,$imagem,100);  break;
			case 3: @imagepng($simg,$imagem); break;
			default: "";  break;
		}
	}
}
function duotone(&$image,$rplus,$gplus,$bplus)
{
	$imagex=imagesx($image);
	$imagey=imagesy($image);
	$x=0;
	while($x<$imagex)
	{
		$y=0;
		while($y<$imagey)
		{
			$rgb=imagecolorat($image,$x,$y);
			$alpha=($rgb >> 24) & 0x7F;
			if($alpha<127) //127 corresponde a transparente
			{
				$red=($rgb >> 16) & 0xFF;
				$green=($rgb >> 8) & 0xFF;
				$blue=$rgb & 0xFF;
				$red=(int)(($red+$green+$blue)/3);
				//para escurecer somar ao red:
				$red-=100;
				$green=$red + $gplus;
				$blue=$red + $bplus;
				$red += $rplus;
				if($red>255) $red=255;
				if($green>255) $green=255;
				if($blue>255) $blue=255;
				if($red<0) $red=0;
				if($green<0) $green=0;
				if($blue<0) $blue=0;
				$newcol=imageColorAllocateAlpha($image,$red,$green,$blue,$alpha);
				imagesetpixel($image,$x,$y,$newcol);
			}
			$y++;
		 }
		 $x++;
	}
}
function grayscale_image(&$image)
{
	$imagex=imagesx($image);
	$imagey=imagesy($image);
	$image2 = imagecreate($imagex,$imagey);
	imagecopy ( $image2,$image,0,0,$imagex,$imagey,$imagex,$imagey);
	imagefilter($image,IMG_FILTER_EDGEDETECT);
	imagefilter($dest,IMG_FILTER_SMOOTH);
	imagefilter($image,IMG_FILTER_BRIGHTNESS,100);
	imagecopy ($image,$image2,0,0,0,0,$width,$height);
}
function hilight_image(&$image)
{
	$imagex=imagesx($image);
	$imagey=imagesy($image);
	$image2=imagecreate($imagex+10,$imagey+10);
	imagecopyresampled($image2,$image,0,0,0,0,$imagex,$imagey,$imagex,$imagey);
	imagecopyresampled($image,$image2,-5,-5,0,0,$imagex+5,$imagey+5,$imagex,$imagey);
	imagefilter($image,IMG_FILTER_COLORIZE,255,255,0,30);
	imagecopyresampled($image,$image2,0,0,0,0,$imagex,$imagey,$imagex,$imagey);
}
function html2rgb($color)
{
	if($color[0]=='#')$color=substr($color,1);
	if(strlen($color)==6)list($r,$g,$b)=array($color[0].$color[1],$color[2].$color[3],$color[4].$color[5]);
	elseif(strlen($color)==3)list($r,$g,$b)=array($color[0].$color[0],$color[1].$color[1],$color[2].$color[2]);
	else return false;
	$r=hexdec($r);$g=hexdec($g);$b=hexdec($b);
	return array($r,$g,$b);
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
		if($use_linux_commands)exec('rm '.$file);
		else @unlink($file);
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
function imagem_sepia($imagem)
{
	if(file_exists($imagem))
	{
		$size = @getimagesize($imagem);
		switch ($size[2])
		{
			case 1: $simg = @imagecreatefromgif($imagem); break;
			case 2: $simg = @imagecreatefromjpeg($imagem);  break;
			case 3: $simg = @imagecreatefrompng($imagem); break;
			default: ""; break;
		}
		duotone($simg, 100,50,0);
		switch ($size[2])
		{

			case 1: @imagegif($simg,$imagem); break;
			case 2: @imagejpeg($simg,$imagem,100);  break;
			case 3: @imagepng($simg,$imagem); break;
			default: "";  break;
		}
	}
}
?>