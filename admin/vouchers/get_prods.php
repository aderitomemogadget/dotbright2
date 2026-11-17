<?php
   	header("Content-Type: text/html; charset=utf-8",true);
	
	$path = "../../core/";
	include $path."core_files.php";
	include $path."admin_globals.php";
	
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	
	$rs = new DataObj; $rs2 = new DataObj;
	$sql = "SELECT pl.Cabecalho as NomeProd,pol.*,pol.Cabecalho AS Ref FROM products_lang pl INNER JOIN products_objs po ON pl.ID_PAI=po.obj_type_id INNER JOIN products_objs_lang pol ON pol.ID_PAI=po.obj_id WHERE pl.lang='".$admin_lang."' AND pol.lang='".$admin_lang."' GROUP BY pl.ID_PAI";
	if($_GET['pesquisa']) $sql .= ' and (pol.Cabecalho like "%'.$_GET['pesquisa'].'%" OR pl.Cabecalho like "%'.$_GET['pesquisa'].'%" )';
	$sql .= ' LIMIT 50';
	$db_res = $rs->db_open($db_link,$DB_name,$sql);
	$i=1;
	
	$content = '<div style="height:18px;background:#dcdcdc;color:#000;font-weight:normal;padding-top:3px;width:100%;border-bottom:#c0c0c0 1px solid;"><div style="margin-top:2px;font-size:12px;float:left;width:310px;font-weight:bold;text-align:right;cursor:pointer" onclick="empty()">&nbsp;'.write_lang('key_voucher_productclearbox','Clique aqui para limpar a caixa').'</div></div>';			
	$content .= '<div style="height:18px;background:#dcdcdc;color:#000;font-weight:normal;padding-top:3px;width:100%;border-bottom:#c0c0c0 1px solid;"><div style="float:left;width:260px;border:#000 0px solid"><div style="margin-top:2px;font-size:12px;float:left;width:70px;font-weight:bold">&nbsp;'.write_lang('key_voucher_productsearch','Pesquisar').':</div><div style="float:left;width:150px;border:#000 0px solid;margin-top:-3px"><input type="text" style="width:150px;" value="'.$_GET['pesquisa'].'" id="search_by" /></div><div style="float:right;width:29px;border:#000 0px solid;margin-top:-4px"><img src="find.png" border="0"  title="search" onclick="search()" style="cursor:pointer" /></div></div></div>';			
	while(!$rs->EOF())
	{
		$count++;
		$video_id = $rs->db_data["ID_PAI"];
		$Ref = $rs->db_data["Ref"];
		$designacao = stripslashes($rs->db_data["NomeProd"]);
		if($count % 2 != 0)	{$cor="#def2fa";$class="r1";$n="1";}
		else				{$cor="#F6F6F6";$class="r2";$n="2";}
		
		$content .= '<div style="height:18px;background:#dcdcdc;color:#000;font-weight:normal;padding-top:3px;width:100%;border-bottom:#c0c0c0 1px solid;"><div style="float:left;width:230px;border:#000 0px solid"><div style="margin-top:2px;font-size:11px">&nbsp;'.$Ref.' - '.$designacao.'</div></div><div style="float:right;width:50px;text-align:center;border:#000 0px solid;margin-top:2px"><a href="#" class="pg" onclick="totext(\'div'.$video_id.'\')">'.write_lang('key_voucher_productadd','Adicionar').'</a><input type="hidden" value="'.$designacao.'" id="div'.$video_id.'" /><input type="hidden" value="'.$video_id.'" id="_div'.$video_id.'" /></div></div>';			
							
		$rs->db_next(); 
		$i++;
	}
	$rs->db_close();
	
	echo $content;
?>