<?
function show_page_buttons()
{
	global $converter,$task_button,$ID,$ID_ORG,$Estado,$T;
	?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
    <tr>
    	<td align="center" valign="middle" class="t1"><?

				echo '<input class="btn btn-primary" type="submit" name="Submit" value="'.$task_button.'">';
				if($task_form=="insere" || $task_form=="apaga" || $task_form=="duplicar")
				{
					?><input class="btn btn-secondary" type="button" name="cancelar" value="<?php echo write_lang('key_cancelar','Cancelar')?>" onClick="window.open('list.php','_self')" /><?
				}
				if($task_form=="altera"){?><input class="btn btn-danger" type="button" name="Apagar" value="<?php echo write_lang('key_apagar','Apagar')?>" onClick="window.open('pagina_trata.php?ID=<?=$ID?>&task_form=apaga','_self')" /><? }
			?>
            <input class="btn btn-secondary" type="button" name="cancelar" value="<?php echo write_lang('key_cancelar','Cancelar')?>" onClick="window.open('list.php','_self')" />
		</td>
     </tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
	<br />
<?
}
function show_fast_tools()
{
	global $ID, $admin_id;
	?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
		<td class="titulo2" align="center">FAST TOOLS</td>
	</tr>
	</table>
	<?php
	$dados = getDadosAdministrador($admin_id);
	if($dados['Grupo_ID']==1){ ?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
	<tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="Novo Metodo Pagamento" onclick="javascript:window.open('pagina.php?id_metodo=0&Dump=<?=uniqid("");?>','_self')"></td>
	</tr>
	</table>
	<?php } ?>
	<img src="../images/bsh.png" width="100%" height="30px">
	<br />
<? }
function show_page_tools($filter=0)
{
	global $tipos; ?>
	<div class="panel panel-inverse overflow-hidden m-b-12">
        <div class="panel-heading">
            <h4 style="color:#fff">MÉTODOS DE PAGAMENTO</h4>
        </div>
    </div>
	<script>
	function checkFields(){
		return true;
	}
	</script>
	<table width="100%" height="50px" border=0 cellspacing=0 cellpadding=0 class="p2">
		<tr>
			<td width="3%" align="center"><input onClick='makeSelection("filter","lista_paginas")' type="checkbox" name="filter" id="filter" value="1"  class="cb"></td>
		    <td width="97%" valign="middle" style="font-weight:bold;color:#FFF">TODOS</td>
		</tr>
	</table>

<? }
function show_page_bottom_tools()
{?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td width="130px" class="t1">&nbsp;<? echo write_lang('key_com_os_seleccionados','Com os seleccionados')?>:</td>
		<td width="125px"><select name="flag" id="flag" class="select1">
				<option value="" selected="selected"></option>
				<option value="3"><? echo 'Remover';?></option>
				<option value="4"><? echo write_lang('key_tornar_visivel','Tornar visivel')?></option>
				<option value="5"><? echo write_lang('key_tornar_invisivel','Tornar invisivel')?></option>
			</select>
		</td>
        <td width="5px"></td>
		<td>&nbsp;<input type="submit" name="submeter" value="<? echo write_lang('key_aplicar_acao','Aplicar Ação')?>" class="btn btn-primary" style="width:120px" /></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
	<br />
<?php
}
function InsertNewRecord()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

	if(!$m_lang) $m_lang = $admin_lang;

	$Img_peq1["name"] = $_FILES['img_peq1']['name'];
	$Img_peq1["temp"] = $_FILES['img_peq1']['tmp_name'];
	$img_path="../../files/metodos_pagamento/";

	$tipo_metodo 	= $_POST["tipo_metodo"];
	$metodo_taxa	= $_POST["metodo_taxa"];
	$tem_foto1 		= $_POST['tem_foto1'];
	$Apagar_img1 	= $_POST['Apagar_img1'];

	$metodo_chave = '';
	$metodo_entidade = '';
	$metodo_subentidade = '';
	$sandbox =0;
	$user = '';
	$pass = '';
	$user_sand = '';
	$pass_sand = '';
	$metodo_nib='';
	$metodo_contractnumber='';
	$metodo_account_id = '';
	$metodo_account_id_sandbox = '';
	$metodo_merchant_site_id = '';
	$metodo_merchant_site_id_sandbox = '';
	$metodo_limite = '';
	$metodo_taxa_tipo = '';

	$ep_account_id_sandbox = '';
	$ep_api_key_sandbox = '';
	$ep_user_sandbox = '';
	$ep_cin_sandbox = '';
	$ep_entity_sandbox = '';
	$ep_url_sandbox = '';

	$ep_account_id_production = '';
	$ep_api_key_production = '';
	$ep_user_production = '';
	$ep_cin_production = '';
	$ep_entity_production = '';
	$ep_url_production = '';
	$ep_sandbox_production = '';

	$Ordem='';

	if(isset($_POST["metodo_chave"])) $metodo_chave = $_POST["metodo_chave"];
	if(isset($_POST["metodo_entidade"])) $metodo_entidade = $_POST["metodo_entidade"];
	if(isset($_POST["metodo_subentidade"])) $metodo_subentidade = $_POST["metodo_subentidade"];
	if(isset($_POST["metodo_sandbox"])) $sandbox = $_POST["metodo_sandbox"];
	if(isset($_POST["metodo_username"])) $user = $_POST["metodo_username"];
	if(isset($_POST["metodo_password"])) $pass = $_POST["metodo_password"];
	if(isset($_POST["metodo_username_sandbox"])) $user_sand = $_POST["metodo_username_sandbox"];
	if(isset($_POST["metodo_password_sandbox"])) $pass_sand = $_POST["metodo_password_sandbox"];
	if(isset($_POST["metodo_nib"])) $metodo_nib = $_POST["metodo_nib"];
	if(isset($_POST["metodo_contractnumber"])) $metodo_contractnumber = $_POST["metodo_contractnumber"];
	if(isset($_POST["metodo_account_id"])) $metodo_account_id = $_POST["metodo_account_id"];
	if(isset($_POST["metodo_account_id_sandbox"])) $metodo_account_id_sandbox = $_POST["metodo_account_id_sandbox"];
	if(isset($_POST["metodo_merchant_site_id"])) $metodo_merchant_site_id = $_POST["metodo_merchant_site_id"];
	if(isset($_POST["metodo_merchant_site_id_sandbox"])) $metodo_merchant_site_id_sandbox = $_POST["metodo_merchant_site_id_sandbox"];
	if(isset($_POST["metodo_swift"])) $metodo_swift = $_POST["metodo_swift"];
	if(isset($_POST["metodo_iban"])) $metodo_iban = $_POST["metodo_iban"];
	if(isset($_POST["limite"])) $metodo_limite = $_POST["limite"];
	if(isset($_POST["metodo_taxa_tipo"])) $metodo_taxa_tipo = $_POST["metodo_taxa_tipo"];

	if(isset($_POST["ep_account_id_sandbox"])) $ep_account_id_sandbox = $_POST["ep_account_id_sandbox"];
	if(isset($_POST["ep_api_key_sandbox"])) $ep_api_key_sandbox = $_POST["ep_api_key_sandbox"];
	if(isset($_POST["ep_user_sandbox"])) $ep_user_sandbox = $_POST["ep_user_sandbox"];
	if(isset($_POST["ep_cin_sandbox"])) $ep_cin_sandbox = $_POST["ep_cin_sandbox"];
	if(isset($_POST["ep_entity_sandbox"])) $ep_entity_sandbox = $_POST["ep_entity_sandbox"];
	if(isset($_POST["ep_url_sandbox"])) $ep_url_sandbox = $_POST["ep_url_sandbox"];

	if(isset($_POST["ep_account_id_production"])) $ep_account_id_production = $_POST["ep_account_id_production"];
	if(isset($_POST["ep_api_key_production"])) $ep_api_key_production = $_POST["ep_api_key_production"];
	if(isset($_POST["ep_user_production"])) $ep_user_production = $_POST["ep_user_production"];
	if(isset($_POST["ep_cin_production"])) $ep_cin_production = $_POST["ep_cin_production"];
	if(isset($_POST["ep_entity_production"])) $ep_entity_production = $_POST["ep_entity_production"];
	if(isset($_POST["ep_url_production"])) $ep_url_production = $_POST["ep_url_production"];
	if(isset($_POST["ep_sandbox_production"])) $ep_sandbox_production = $_POST["ep_sandbox_production"];

	if(isset($_POST["Ordem"])) $Ordem = $_POST["Ordem"];

	$Abreviatura = addslashes($_POST['Abreviatura']);

	$nome = $_POST['nome_metodo'];
	$metodo_descricao = addslashes($_POST['metodo_descricao']);


	if(isset($_POST['Visivel'])) $Visivel = $_POST['Visivel'];
	else $Visivel = 0;

	$sql = "INSERT INTO metodos_pagamento(Ordem,metodo_taxa_tipo,limite,Visivel,img_ext,Tipo,Sandbox,Username,Username_sandbox,Password,Password_sandbox,Taxa,chavebo,entidade,subentidade,nib,swift,iban,Contractnumber,Account_id,Account_id_sandbox,Merchant_site_id,Merchant_site_id_sandbox,ep_account_id_sandbox,ep_api_key_sandbox,ep_user_sandbox,ep_cin_sandbox,ep_entity_sandbox,ep_url_sandbox,ep_account_id_production,ep_api_key_production,ep_user_production,ep_cin_production,ep_entity_production,ep_url_production,ep_sandbox_production,Abreviatura)
	VALUES ('$Ordem','$metodo_taxa_tipo','$metodo_limite', '$Visivel','$img_ext','$tipo_metodo','$sandbox','$user','$user_sand','$pass','$pass_sand','$metodo_taxa','$metodo_chave','$metodo_entidade','$metodo_subentidade','$metodo_nib','$metodo_swift','$metodo_iban','$metodo_contractnumber','$metodo_account_id','$metodo_account_id_sandbox','$metodo_merchant_site_id','$metodo_merchant_site_id_sandbox','$ep_account_id_sandbox','$ep_api_key_sandbox','$ep_user_sandbox','$ep_cin_sandbox','$ep_entity_sandbox','$ep_url_sandbox','$ep_account_id_production','$ep_api_key_production','$ep_user_production','$ep_cin_production','$ep_entity_production','$ep_url_production','$ep_sandbox_production','$Abreviatura')";
	// echo $sql.'<br>';
	$res = Runquery($db_link,$DB_name,$sql);
	if($res){
		$new_id = db_get_last_insert_id($db_link);

		$altera_img=0;if($Img_peq1["name"]!=''){$altera_img=1;$pos=strrpos($Img_peq1["name"],".");$img_ext=substr($Img_peq1["name"],$pos+1,strlen($Img_peq1["name"])+1-$pos);}
		//IMAGEM NUMERO 1
		if($tem_foto1==1 && $Apagar_img1==1){$ficheiro1=$new_id."_1.".$img_ext;unlink($img_path.$ficheiro1);if(!$altera_img)$img_ext='';}
		if($altera_img)copia_segura($Img_peq1["temp"],$img_path.$new_id."_1.".$img_ext);

		$sql = "INSERT INTO metodos_pagamento_lang(ID_PAI,Cabecalho,Texto1,Lang) VALUES ('$new_id','$nome','$metodo_descricao','$m_lang')";
	// echo $sql.'<br>';
		$res = Runquery($db_link,$DB_name,$sql);
	}
	else echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
function DeleteRecord()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$id_metodo = $_POST['id_metodo'];

    $sql="delete from metodos_pagamento where ID=$id_metodo";
	$res=Runquery($db_link,$DB_name,$sql);

	$sql="delete from metodos_pagamento_lang where ID_PAI=$id_metodo";
	$res=Runquery($db_link,$DB_name,$sql);
	if(!$res) echo db_error($db_link);

	return($res);
}
function ChangeRecord()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;
	if(!$m_lang) $m_lang = $admin_lang;

	$Img_peq1["name"] = $_FILES['img_peq1']['name'];
	$Img_peq1["temp"] = $_FILES['img_peq1']['tmp_name'];
	$img_path="../../files/metodos_pagamento/";

	$tipo_metodo = $_POST["tipo_metodo"];
	$id_metodo = $_GET["id_metodo"];
	$metodo_taxa = $_POST["metodo_taxa"];
	$tem_foto1 = $_POST['tem_foto1'];
	$Apagar_img1 = $_POST['Apagar_img1'];


	$metodo_chave = '';
	$metodo_entidade = '';
	$metodo_subentidade = '';
	$sandbox =0;
	$user = '';
	$pass = '';
	$user_sand = '';
	$pass_sand = '';
	$metodo_nib='';
	$metodo_contractnumber='';
	$metodo_account_id = '';
	$metodo_account_id_sandbox = '';
	$metodo_merchant_site_id = '';
	$metodo_merchant_site_id_sandbox = '';
	$metodo_limite = '';
	$metodo_taxa_tipo = '';

	$ep_account_id_sandbox = '';
	$ep_api_key_sandbox = '';
	$ep_user_sandbox = '';
	$ep_cin_sandbox = '';
	$ep_entity_sandbox = '';
	$ep_url_sandbox = '';

	$ep_account_id_production = '';
	$ep_api_key_production = '';
	$ep_user_production = '';
	$ep_cin_production = '';
	$ep_entity_production = '';
	$ep_url_production = '';
	$ep_sandbox_production = '';
	$Ordem='';

	if(isset($_POST["metodo_chave"])) $metodo_chave = $_POST["metodo_chave"];
	if(isset($_POST["metodo_entidade"])) $metodo_entidade = $_POST["metodo_entidade"];
	if(isset($_POST["metodo_subentidade"])) $metodo_subentidade = $_POST["metodo_subentidade"];
	if(isset($_POST["metodo_sandbox"])) $sandbox = 1; else $sandbox = 0;
	if(isset($_POST["metodo_username"])) $user = $_POST["metodo_username"];
	if(isset($_POST["metodo_password"])) $pass = $_POST["metodo_password"];
	if(isset($_POST["metodo_username_sandbox"])) $user_sand = $_POST["metodo_username_sandbox"];
	if(isset($_POST["metodo_password_sandbox"])) $pass_sand = $_POST["metodo_password_sandbox"];
	if(isset($_POST["metodo_nib"])) $metodo_nib = $_POST["metodo_nib"];
	if(isset($_POST["metodo_contractnumber"])) $metodo_contractnumber = $_POST["metodo_contractnumber"];
	if(isset($_POST["metodo_account_id"])) $metodo_account_id = $_POST["metodo_account_id"];
	if(isset($_POST["metodo_account_id_sandbox"])) $metodo_account_id_sandbox = $_POST["metodo_account_id_sandbox"];
	if(isset($_POST["metodo_merchant_site_id"])) $metodo_merchant_site_id = $_POST["metodo_merchant_site_id"];
	if(isset($_POST["metodo_merchant_site_id_sandbox"])) $metodo_merchant_site_id_sandbox = $_POST["metodo_merchant_site_id_sandbox"];
	if(isset($_POST["metodo_swift"])) $metodo_swift = $_POST["metodo_swift"];
	if(isset($_POST["metodo_iban"])) $metodo_iban = $_POST["metodo_iban"];
	if(isset($_POST["limite"])) $metodo_limite = $_POST["limite"];
	if(isset($_POST["metodo_taxa_tipo"])) $metodo_taxa_tipo = $_POST["metodo_taxa_tipo"];

	if(isset($_POST["ep_account_id_sandbox"])) $ep_account_id_sandbox = $_POST["ep_account_id_sandbox"];
	if(isset($_POST["ep_api_key_sandbox"])) $ep_api_key_sandbox = $_POST["ep_api_key_sandbox"];
	if(isset($_POST["ep_user_sandbox"])) $ep_user_sandbox = $_POST["ep_user_sandbox"];
	if(isset($_POST["ep_cin_sandbox"])) $ep_cin_sandbox = $_POST["ep_cin_sandbox"];
	if(isset($_POST["ep_entity_sandbox"])) $ep_entity_sandbox = $_POST["ep_entity_sandbox"];
	if(isset($_POST["ep_url_sandbox"])) $ep_url_sandbox = $_POST["ep_url_sandbox"];

	if(isset($_POST["ep_account_id_production"])) $ep_account_id_production = $_POST["ep_account_id_production"];
	if(isset($_POST["ep_api_key_production"])) $ep_api_key_production = $_POST["ep_api_key_production"];
	if(isset($_POST["ep_user_production"])) $ep_user_production = $_POST["ep_user_production"];
	if(isset($_POST["ep_cin_production"])) $ep_cin_production = $_POST["ep_cin_production"];
	if(isset($_POST["ep_entity_production"])) $ep_entity_production = $_POST["ep_entity_production"];
	if(isset($_POST["ep_url_production"])) $ep_url_production = $_POST["ep_url_production"];
	if(isset($_POST["ep_sandbox_production"])) $ep_sandbox_production = $_POST["ep_sandbox_production"];
	
	if(isset($_POST["Ordem"])) $Ordem = $_POST["Ordem"];


	$Abreviatura = addslashes($_POST['Abreviatura']);

	$nome = $_POST['nome_metodo'];
	$metodo_descricao = addslashes($_POST['metodo_descricao']);

	if(isset($_POST['Visivel'])) $Visivel = $_POST['Visivel'];
	else $Visivel = 0;

	$altera=0;
	$rs->db_open($db_link,$DB_name,"Select * from metodos_pagamento WHERE ID='$id_metodo'");
	if(!$rs->EOF())
	{
		$altera = 1;
		$img_ext=$rs->db_data["img_ext"];
	}
	$rs->db_close();

	if(!$altera) return InsertNewRecord();
	else
	{
		$altera_img=0;if($Img_peq1["name"]!=''){$altera_img=1;$pos=strrpos($Img_peq1["name"],".");$img_ext=substr($Img_peq1["name"],$pos+1,strlen($Img_peq1["name"])+1-$pos);}

		if($tem_foto1==1 && $Apagar_img1==1){$ficheiro1=$id_metodo."_1.".$img_ext;unlink($img_path.$ficheiro1);if(!$altera_img)$img_ext='';}
		if($altera_img)copia_segura($Img_peq1["temp"],$img_path.$id_metodo."_1.".$img_ext);

		$sql="Update metodos_pagamento Set Ordem='$Ordem',metodo_taxa_tipo='$metodo_taxa_tipo', limite='$metodo_limite', Tipo='$tipo_metodo', Merchant_site_id='$metodo_merchant_site_id',Merchant_site_id_sandbox='$metodo_merchant_site_id_sandbox',Account_id='$metodo_account_id',Account_id_sandbox='$metodo_account_id_sandbox',Visivel='$Visivel',Contractnumber='$metodo_contractnumber',iban='$metodo_iban',swift='$metodo_swift',nib='$metodo_nib',chavebo='$metodo_chave',entidade='$metodo_entidade',subentidade='$metodo_subentidade',img_ext='$img_ext',Taxa='$metodo_taxa',Sandbox='$sandbox',Username='$user',Password='$pass',Username_sandbox='$user_sand',Password_sandbox='$pass_sand',ep_account_id_sandbox = '$ep_account_id_sandbox',
			ep_api_key_sandbox = '$ep_api_key_sandbox',
			ep_user_sandbox = '$ep_user_sandbox',
			ep_cin_sandbox = '$ep_cin_sandbox',
			ep_entity_sandbox = '$ep_entity_sandbox',
			ep_url_sandbox = '$ep_url_sandbox',
			ep_account_id_production = '$ep_account_id_production',
			ep_api_key_production = '$ep_api_key_production',
			ep_user_production = '$ep_user_production',
			ep_cin_production = '$ep_cin_production',
			ep_entity_production = '$ep_entity_production',
			ep_url_production = '$ep_url_production',
			ep_sandbox_production = '$ep_sandbox_production',
			Abreviatura='$Abreviatura'
			where ID=$id_metodo";

		$res = Runquery($db_link,$DB_name,$sql);

		$dados_tab = verificaExisteLingua("metodos_pagamento_lang",$m_lang,$id_metodo);
		if($dados_tab->EOF()) $sql = "INSERT INTO metodos_pagamento_lang(ID_PAI,Cabecalho,Texto1,Lang) VALUES ('$id_metodo','".$nome."','".$metodo_descricao."','$m_lang')";
		else $sql="update metodos_pagamento_lang set  Texto1='".$metodo_descricao."',Cabecalho='".$nome."' where ID_PAI='".$id_metodo."' and Lang='".$m_lang."'";
		$res = Runquery($db_link,$DB_name,$sql);



		db_closedb_conection($db_link);
		return($res);
	}
}

function verificaExisteLingua($tabela,$lang,$id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj_tab = new DataObj;
	$sql="Select * from ".$tabela." where ID_PAI='".$id."' and Lang = '".$lang."'";
	$obj_tab->db_open($db_link,$DB_name,$sql);
	return $obj_tab;

}



function show_content_list($pai=0,$contador=-5,$count=0,$md=0,$span=0)
{
	$path="../../core/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$admin_lang,$m_resumo,$tipos;

	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$contador+=5;
	$rs=new DataObj;$rs2=new DataObj;

	$sql='SELECT ID, Tipo, Visivel FROM metodos_pagamento ORDER BY Ordem ASC';

	$rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	if(!$rs->EOF()){

		while(!$rs->EOF())
		{
			$count++;
			$id_metodo 		= $rs->db_data["ID"];
			$Visivel 		= $rs->db_data["Visivel"];
			$tipo 			= $rs->db_data['Tipo'];

			$sql2='SELECT Cabecalho FROM metodos_pagamento_lang WHERE ID_PAI="'.$id_metodo.'" and Lang="'.$admin_lang.'"';
			$rs2->db_open($db_link, $DB_name,$sql2);
			if(!$rs2->EOF()) $nome_metodo = $rs2->db_data["Cabecalho"];
			else $nome_metodo 	= '';
			$rs2->db_close();

			if($Visivel==1)
			{
				if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
				else				{$cor="#FFFFFF";$class="r2";$n="2";}
			}
			else
			{
				if($count % 2 != 0)	{$cor="#dbdada";$class="r1";$n="1";}
				else				{$cor="#e8e8e8";$class="r2";$n="2";}
			}

			$i=0;
			?><table height="50px" border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
				<tr bgcolor="<?=$cor;?>" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
					<td width="3%" align="center"><input type="checkbox" name="chk_<?=$id_metodo?>" id="chk_<?=$id_metodo?>" value="<?=$id_metodo?>" class="cb"></td>
			        <td valign="middle" width="35"><?
					$imgvar="";
					$imgvar .='<img title="';
					if($Visivel)$imgvar .='Visível';
					else $imgvar .='Invisível';
					$imgvar .='" src="imgs/';
					if($Visivel) $imgvar .= "ok.png";
					else $imgvar .= "hidden.png";
					$imgvar .='" border=0 align="absmiddle" />';
					echo $imgvar;?>
			        </td>
        			<td valign="middle"><? while($i<$contador){echo "-";$i++;}?><a title="<?php echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?id_metodo=<?=$id_metodo;?>&Dump=<? echo uniqid("");?>"><? if($nome_metodo){echo htmlentities_b(stripslashes($nome_metodo));} else echo "[Edit]";?></a></td>
        			<td valign="middle" width="250"><?php if(isset($GLOBALS['pgm_metodos'][$tipo])) echo $GLOBALS['pgm_metodos'][$tipo]['nome']; ?></td>
				</tr>
			</table><?php
			$rs->db_next();
			$i++;
		}

	}else{?>
			<table height="50px" border=0 cellspacing=0 cellpadding=0 class="r1">
				<tr bgcolor="#e8e7e7" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='#e8e7e7'">
					<td colspan="8" align="center">Não Existem Métodos de Pagamento</td>
				</tr>
			</table>
	<?php }
	$rs->db_close();
}

?>