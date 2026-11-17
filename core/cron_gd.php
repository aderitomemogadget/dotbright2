<?

if(isset($argv) && $argv[1] != '')
{
	$u=trim($argv[1]);
	$foto=trim($argv[2]);
	$foto2=trim($argv[3]);
	make_transparent_new($u,$foto,$foto2);
}


function make_transparent_new($u,$foto,$foto2)
{
	$x=0;
	$y=0;
	$color='#ff0000';
	$transparencia=2;
	$cor_rgb=hex2rgb($color);
	fload_fill($x,$y,$u,$foto,$foto2,$cor_rgb['r'],$cor_rgb['g'],$cor_rgb['b'],$transparencia,$color);
}


//------------------------
function fload_fill($x,$y,$u,$foto,$foto2,$r,$g,$b,$toler,$color)
{
	setMemoryForImage($u.$foto); // vamos alocar memoria para esta imagem
	set_time_limit(0);

	$quality=9;//0,1,2,...9  ( quanto maior o numero menor será a qualidade )

	
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
	
	//imagepng($img_destino,$u.$foto2, null, $quality);
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
	ini_set("memory_limit","500M");
	set_time_limit(0);


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
	
	$array[]=$x.'x'.$y;//ponto 0,0
	$array[]=($w-1).'x'.($h-1);//lado oposto

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
		
		if ($x < 0 || $x >= $w || $y < 0 || $y >= $h || (isset($array_history[$x][$y]) && $array_history[$x][$y]==$flag)) {}
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
function setMemoryForImage( $filename )
{
	echo $filename;
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
?>