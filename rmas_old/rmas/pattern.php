<?php
if(isset($_GET['t']) && intval($_GET['t']))
{
	header("Content-type: image/png");
	$png_image = imagecreatefrompng("pattern2.png");
	
	
	function imagettfstroketext(&$image, $size, $angle, $x, $y, &$textcolor, &$strokecolor, $fontfile, $text, $px) 
	{
		for($c1 = ($x-abs($px)); $c1 <= ($x+abs($px)); $c1++)
			for($c2 = ($y-abs($px)); $c2 <= ($y+abs($px)); $c2++)
				$bg = imagettftext($image, $size, $angle, $c1, $c2, $strokecolor, $fontfile, $text);
	   return imagettftext($image, $size, $angle, $x, $y, $textcolor, $fontfile, $text);
	}
	
	$font_path = './arial.ttf';
	$white = imagecolorallocate($png_image, 255, 255, 255);
	$blck = imagecolorallocate($png_image, 0, 0, 0);
	
	// COORDENADAS
	$p[1] = array('w' => 23, 'h' => 23);	
	$p[2] = array('w' => 60, 'h' => 23);
	$p[3] = array('w' => 97, 'h' => 23);

	$p[4] = array('w' => 23, 'h' => 60);
	$p[5] = array('w' => 60, 'h' => 60);
	$p[6] = array('w' => 97, 'h' => 60);

	$p[7] = array('w' => 23, 'h' => 97);
	$p[8] = array('w' => 60, 'h' => 97);
	$p[9] = array('w' => 97, 'h' => 97);
	
	
	


	imagecolorallocate($png_image, 15, 142, 210);
	imagesetthickness($png_image, 5);
	$black = imagecolorallocate($png_image, 0, 0, 0);

	$array = str_split($_GET['t']);

	for($a=0; $a<count($array); $a++)
	{	
		if($a == (count($array)-1))
		{ 
			imageline($png_image, $p[$array[$a]]['w'], $p[$array[$a]]['h'], $p[$array[$a]]['w'], $p[$array[$a]]['h'], $black);			
			
		}
		else
		{
			imageline($png_image, $p[$array[$a]]['w'], $p[$array[$a]]['h'], $p[$array[$a+1]]['w'], $p[$array[$a+1]]['h'], $black);
		}
	}
	$text = $_GET['t'];
	
	// $text_box = imagettfbbox(10, 0, $font_path, $text);
	


	for($a=0; $a<count($array); $a++)
	{
		imagettfstroketext($png_image, 10, 0, ($p[$array[$a]]['w']+5), ($p[$array[$a]]['h']-5), $blck, $white, $font_path, $array[$a], 3);
	}

	imagettftext($png_image, 10, 0, 10, 126, $white, $font_path, $text);

	imagepng($png_image);
	imagedestroy($png_image);
}