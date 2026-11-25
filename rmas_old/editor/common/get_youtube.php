<?php
   	header("Content-Type: text/html; charset=utf-8",true);
	
	$path = "../../../core/";
	include $path."core_files.php";
	
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">	
	<link href="../scripts/style/editor.css" rel="stylesheet" type="text/css">
	<title>Insert Youtube Video</title>
	<script src="jquery-1.4.2.min.js" type="text/javascript"></script>
	<script src="common.js" type="text/javascript"></script>
	<script>
	function I_Insert(video_id)
	{
		var w = $('#WIDTH').val();
		var h = $('#HEIGHT').val();
		if(w=='' || w==0) w = 350;
		if(h=='' || h==0) h = 300;
		
		var sEmbedUrl = "http://www.youtube.com/embed/" + $('#_div'+video_id).val()+"?autoplay=1&rel=0&loop=1";       
		var sHTML = "<div style=\"width:"+w+"px;height:"+h+"px;float:left\"><iframe title=\"\" width=\""+w+"\" height=\""+h+"\" src=\"" + sEmbedUrl + "\" frameborder=\"0\" allowfullscreen></iframe></div>";
        I_InsertHTML(sHTML);
    }
	</script>
	</head>
	<body>
	<?php
	
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	
	$rs = new DataObj; $rs2 = new DataObj;
	$sql = "Select * from video_menager_link where video_lang='".$admin_lang."'";
	if($_GET['pesquisa']) $sql .= ' where video_title like "%'.$_GET['pesquisa'].'%"';
	$db_res = $rs->db_open($db_link,$DB_name,$sql);
	$i=1;
	
	 $content = '<div style="height:25px;background:#dcdcdc;color:#000;font-weight:normal;padding-top:3px;width:100%;border-bottom:#c0c0c0 1px solid;"><div style="margin-top:2px;font-size:12px;border:#000 0px solid;float:left;width:110px;font-weight:bold;text-align:right;cursor:pointer">Width: <input type="text" style="width:50px;" id="WIDTH" value="350" /></div><div style="margin-top:2px;font-size:12px;float:left;width:200px;font-weight:bold;text-align:left;cursor:pointer">Height: <input type="text" style="width:50px;" id="HEIGHT" value="300" /></div></div>';			
	$content .= '<div style="height:18px;background:#dcdcdc;color:#000;font-weight:normal;padding-top:3px;width:100%;border-bottom:#c0c0c0 1px solid;"><div style="float:left;width:260px;border:#000 0px solid"><div style="margin-top:2px;font-size:12px;float:left;width:70px;font-weight:bold">&nbsp;Search:</div><div style="float:left;width:150px;border:#000 0px solid;margin-top:-3px"><input type="text" style="width:150px;" value="'.$_GET['pesquisa'].'" id="search_by" /></div><div style="float:right;width:29px;border:#000 0px solid;margin-top:-4px"><img src="find.png" border="0"  title="search" onclick="search()" style="cursor:pointer" /></div></div></div>';			
	while(!$rs->EOF())
	{
		$count++;
		$video_id = $rs->db_data["video_id"];
		$designacao = $rs->db_data["video_title"];
		$video_code = $rs->db_data["video_code"];
		if($count % 2 != 0)	{$cor="#def2fa";$class="r1";$n="1";}
		else				{$cor="#F6F6F6";$class="r2";$n="2";}
		
		$content .= '<div style="height:18px;background:#dcdcdc;color:#000;font-weight:normal;padding-top:3px;width:100%;border-bottom:#c0c0c0 1px solid;"><div style="float:left;width:230px;border:#000 0px solid"><div style="margin-top:2px;font-size:11px">&nbsp;'.$i.' - '.$designacao.'</div></div><div style="float:right;width:50px;text-align:center;border:#000 0px solid;margin-top:2px"><a href="#" class="pg" onclick="I_Insert(\''.$video_id.'\')">Add this</a><input type="hidden" value="'.$video_code.'" id="_div'.$video_id.'" /></div></div>';			
							
		$rs->db_next(); 
		$i++;
	}
	$rs->db_close();
	
	echo $content;
?>
</body>
</html>