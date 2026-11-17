<?php
	if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) exit();
	function show_flash($movie,$largura,$altura,$parametros,$return=0)
	{
		//descontinuado


		/*$str1=str_replace(';[',',',$parametros);
		$str1=str_replace(']=',',',$str1);
		$str1=str_replace('[','',$str1);
		$str2=str_replace(";['",'<param name="',$parametros);
		$str2=str_replace("']='",'" value="',$str2);
		$str2=str_replace("['",'<param name="',$str2);
		$str2=str_replace("'",'">',$str2);
		$str3=str_replace(";['"," ",$parametros);
		$str3=str_replace("']='","=",$str3);
		$str3=str_replace("['","",$str3);
		$str3=str_replace("'","",$str3);
		$str='<script type="text/javascript">';
		$str.="AC_FL_RunContent( 'codebase','https://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0',";
		$str.=$str1;
		$str.=",'src','$movie','width','$largura','height','$altura','pluginspage','http://www.macromedia.com/go/getflashplayer','movie','$movie' );";
		$str.='</script><noscript><object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="https://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="'.$largura.'" height="'.$altura.'">';
		$str.='<param name="movie" value="'.$movie.'.swf">';
		$str.=$str2;
        $str.='<embed src="'.$movie.'.swf" pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" width="'.$largura.'" height="'.$altura.'" '.$str3.'></embed>';
		$str.='</object></noscript>';
		if($return)return $str;
		else echo $str;*/
	}