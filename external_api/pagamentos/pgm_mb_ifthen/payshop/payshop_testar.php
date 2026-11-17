<?php
	/*CREATE TABLE `ifthen_payshop_logs` (
	  `ID` int(11) NOT NULL AUTO_INCREMENT,
	  `ID_Encomenda` int(11) NOT NULL,
	  `Data` datetime NOT NULL,
	  `Estado` varchar(100) NOT NULL,
	  `Erros` longblob,
	  PRIMARY KEY (`ID`)
	) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;*/

	$path =  dirname( __FILE__ ) . '/../../../../core/';
	include $path.'core_files.php';
	//require("encode_bars.php"); /* build-in encoders */


	$id_enc='73681';
	$GLOBALS['PAYSHOP_KEY'] = 'XSE-077653';
	$valor=302;

	function sendPayshopRequest($payshopkey='',$id_enc='',$valor='')
	{
		if (!is_null($payshopkey) && !is_null($id_enc) && !is_null($valor) )
		{
			$url='https://ifthenpay.com/api/payshop/get?payshopkey='.$payshopkey.'&id='.$id_enc.'&valor='.$valor.'&validade=';

			$response=file_get_contents($url);

		    return $response;

        }
        else return 'Informação incompleta para poder gerar a referencia payshop!';

	}

	$refgen = sendPayshopRequest($GLOBALS['PAYSHOP_KEY'],$id_enc,$valor);
	$refgen2=json_decode($refgen,true);


	if($refgen!='')
	{
		$msg_tmp.= '<table width="350" border=0 cellspacing=0 cellpadding=0 style="border:1px solid grey;margin:0 auto;border-collapse:inherit;border-spacing:10px;">';
		$msg_tmp.= '<tr><td  align="center"><img style="width:150px" src="' .SITE_HTTP_URL . $GLOBALS['pgm_metodos'][MP_TP_PAYSHOP_IFTHEN]['url']. 'payshop.png" border="0" /></td></tr>';
		$msg_tmp.= "<tr><td align='center' style='font-weight:bold;'>&euro;&nbsp;" . number_format($valor , 2, ',', ' ').'</td></tr>';
		$msg_tmp.= "<tr><td align='center' style='font-weight:bold'>".$refgen2['Reference'].'</td></tr>';
		$msg_tmp.= '<tr><td align="center" style="font-size:13px">Para pagamento em agentes Payshop.</td></tr>';
		$msg_tmp.= '</table>';


	}else{
		$msg_tmp.='Não foi possivel gerar referência.';
	}
	echo $msg_tmp;

	/*$encoding="ANY";
	$bars=barcode_encode($refgen2['Reference'],$encoding);
	barcode_outimage($bars['text'],$bars['bars'],2, 'png');*/






function barcode_outimage($text, $bars, $scale = 1, $mode = "png",
	    $total_y = 0, $space = ''){
    global $bar_color, $bg_color, $text_color;
    global $font_loc;
    /* set defaults */
    if ($scale<1) $scale=2;
    $total_y=(int)($total_y);
    if ($total_y<1) $total_y=(int)$scale * 60;
    if (!$space)
      $space=array('top'=>2*$scale,'bottom'=>2*$scale,'left'=>2*$scale,'right'=>2*$scale);

    /* count total width */
    $xpos=0;
    $width=true;
    for ($i=0;$i<strlen($bars);$i++){
	$val=strtolower($bars[$i]);
	if ($width){
	    $xpos+=$val*$scale;
	    $width=false;
	    continue;
	}
	if (ereg("[a-z]", $val)){
	    /* tall bar */
	    $val=ord($val)-ord('a')+1;
	}
	$xpos+=$val*$scale;
	$width=true;
    }

    /* allocate the image */
    $total_x=( $xpos )+$space['right']+$space['right'];
    $xpos=$space['left'];
    if (!function_exists("imagecreate")){
	print "You don't have the gd2 extension enabled<BR>\n";
	print "<BR>\n";
	print "<BR>\n";
	print "Short HOWTO<BR>\n";
	print "<BR>\n";
	print "Debian: # apt-get install php4-gd2<BR>\n";
	print "<BR>\n";
	print "SuSE: ask YaST<BR>\n";
	print "<BR>\n";
	print "OpenBSD: # pkg_add /path/php4-gd-4.X.X.tgz (read output, you have to enable it)<BR>\n";
	print "<BR>\n";
	print "Windows: Download the PHP zip package from <A href=\"http://www.php.net/downloads.php\">php.net</A>, NOT the windows-installer, unzip the php_gd2.dll to C:\PHP (this is the default install dir) and uncomment 'extension=php_gd2.dll' in C:\WINNT\php.ini (or where ever your os is installed)<BR>\n";
	print "<BR>\n";
	print "<BR>\n";
	print "The author of php-barcode will give not support on this topic!<BR>\n";
	print "<BR>\n";
	print "<BR>\n";
	print "<A HREF=\"http://www.ashberg.de/bar/\">Folke Ashberg's OpenSource PHP-Barcode</A><BR>\n";
	return "";
    }
    $im=imagecreate($total_x, $total_y);
    /* create two images */
    $col_bg=ImageColorAllocate($im,$bg_color[0],$bg_color[1],$bg_color[2]);
    $col_bar=ImageColorAllocate($im,$bar_color[0],$bar_color[1],$bar_color[2]);
    $col_text=ImageColorAllocate($im,$text_color[0],$text_color[1],$text_color[2]);
    $height=round($total_y-($scale*10));
    $height2=round($total_y-$space['bottom']);


    /* paint the bars */
    $width=true;
    for ($i=0;$i<strlen($bars);$i++){
	$val=strtolower($bars[$i]);
	if ($width){
	    $xpos+=$val*$scale;
	    $width=false;
	    continue;
	}
	if (ereg("[a-z]", $val)){
	    /* tall bar */
	    $val=ord($val)-ord('a')+1;
	    $h=$height2;
	} else $h=$height;
	imagefilledrectangle($im, $xpos, $space['top'], $xpos+($val*$scale)-1, $h, $col_bar);
	$xpos+=$val*$scale;
	$width=true;
    }
    /* write out the text */
    global $_SERVER;
    $chars=explode(" ", $text);
	reset($chars);


	/********/
	putenv('GDFONTPATH=' . realpath('fonts'));
	$font = 'arialbd';
	/********/
	while (list($n, $v)=each($chars)){
	if (trim($v)){
        $inf=explode(":", $v);
	    $fontsize=$scale*($inf[1]/1.8);
	    $fontheight=$total_y-($fontsize/2.7)+2;
		$black = imagecolorallocate($im,0,0,0);
		imagettftext($im, 8 , 0, $space['left']+($scale*$inf[0])+2, $fontheight, -$black, $font, $inf[2]);
		//@imagettftext($im, $fontsize, 0, $space['left']+($scale*$inf[0])+2, $fontheight, $col_text, $font_loc, $inf[2]);
	}
    }

    /* output the image */
    $mode=strtolower($mode);
    if ($mode=='jpg' || $mode=='jpeg'){
	header("Content-Type: image/jpeg; name=\"barcode.jpg\"");
	imagejpeg($im);
    } else if ($mode=='gif'){
	header("Content-Type: image/gif; name=\"barcode.gif\"");
	imagegif($im);
    } else {
	header("Content-Type: image/png; name=\"barcode.png\"");
	imagepng($im);
    }

}
?>