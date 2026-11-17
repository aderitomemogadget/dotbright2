<?php
	$pesquisa=strip_html_tags(clean_var($_GET['pesquisa']));


   	header("Content-Type: text/html; charset=utf-8",true);

	$path = "../../core/";
	include $path."core_files.php";
	include $path."admin_globals.php";

	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$rs = new DataObj;
	$rs2 = new DataObj;
	$sql = "Select * from clientes";


	if($pesquisa)
	{
		$sql .= ' Where 1=1 ';
		$vpesq = explode(' ', $pesquisa);
		for ($i=0; $i < count($vpesq); $i++) {
			$sql .= ' AND (Nome like "%'.$vpesq[$i].'%" OR Apelido like "%'.$vpesq[$i].'%") ';
		}
	}


	$db_res = $rs->db_open($db_link,$DB_name,$sql);
	$i=1;

	$content = '<div style="height:18px;background:#dcdcdc;color:#000;font-weight:normal;padding-top:3px;width:100%;border-bottom:#c0c0c0 1px solid;"><div style="margin-top:2px;font-size:12px;float:left;width:310px;font-weight:bold;text-align:right;cursor:pointer" onclick="empty2()">&nbsp;'.write_lang('key_voucher_productclearbox','Clique aqui para limpar a caixa').'</div></div>';
	$content .= '<div style="height:18px;background:#dcdcdc;color:#000;font-weight:normal;padding-top:3px;width:100%;border-bottom:#c0c0c0 1px solid;"><div style="float:left;width:260px;border:#000 0px solid"><div style="margin-top:2px;font-size:12px;float:left;width:70px;font-weight:bold">&nbsp;'.write_lang('key_voucher_productsearch','Pesquisar').':</div><div style="float:left;width:150px;border:#000 0px solid;margin-top:-3px"><input type="text" style="width:150px;" value="'.$pesquisa.'" id="search_by2" /></div><div style="float:right;width:29px;border:#000 0px solid;margin-top:-4px"><img src="find.png" border="0"  title="search" onclick="search2()" style="cursor:pointer" /></div></div></div>';
	while(!$rs->EOF())
	{
		$count++;
		$cliente_id = $rs->db_data["ID"];
		$designacao = stripslashes($rs->db_data["Nome"].' '.$rs->db_data["Apelido"]);
		if($count % 2 != 0)	{$cor="#def2fa";$class="r1";$n="1";}
		else				{$cor="#F6F6F6";$class="r2";$n="2";}

		$content .= '<div style="height:18px;background:#dcdcdc;color:#000;font-weight:normal;padding-top:3px;width:100%;border-bottom:#c0c0c0 1px solid;"><div style="float:left;width:230px;border:#000 0px solid"><div style="margin-top:2px;font-size:11px">&nbsp;'.$i.' - '.$designacao.'</div></div><div style="float:right;width:50px;text-align:center;border:#000 0px solid;margin-top:2px"><a href="#" class="pg" onclick="totext2(\'div'.$cliente_id.'\')">'.write_lang('key_voucher_productadd','Adicionar').'</a><input type="hidden" value="'.$designacao.'" id="div'.$cliente_id.'" /><input type="hidden" value="'.$cliente_id.'" id="_div'.$cliente_id.'" /></div></div>';

		$rs->db_next();
		$i++;
	}
	$rs->db_close();

	echo $content;
?>