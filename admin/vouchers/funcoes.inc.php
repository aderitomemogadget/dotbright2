<?php
function show_page_buttons()
{
	global $converter,$task_button,$ID,$ID_ORG,$Estado,$T;
	?>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
    <tr>
    	<td width="5px"></td>
    	<td align="left" valign="middle" class="t1"><?

				echo '<input class="btn btn-primary" type="submit" name="Submit" value="'.$task_button.'">';
				if($task_form=="insere" || $task_form=="apaga" || $task_form=="duplicar")
				{
					?><input class="btn btn-primary" type="button" name="cancelar" value="<?php echo write_lang('key_cancelar','Cancelar')?>" onMouseUp="window.open('list.php','_self')" /><?
				}
				if($task_form=="altera"){?><input class="btn btn-primary" type="button" name="Apagar" value="<?php echo write_lang('key_apagar','Apagar')?>" onMouseUp="window.open('pagina_trata.php?ID=<?=$ID?>&task_form=apaga','_self')" /><? }
			?>
            <input class="btn btn-primary" type="button" name="cancelar" value="<?php echo write_lang('key_cancelar','Cancelar')?>" onMouseUp="window.open('list.php','_self')" />
		</td>
     </tr>
	</table>
	<img src="../images/bsh.png" width="890px">
<?
}
function show_fast_tools()
{
	global $ID;?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
		<td class="titulo2" align="center">FAST TOOLS</td>
	</tr>
	<tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<?php echo write_lang('key_voucher_create','Criar novo Voucher')?>" onclick="javascript:window.open('pagina.php?ID=0&ID_ORG=0&Dump=<?=uniqid("");?>','_self')"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<? }
function show_page_tools($filter=0)
{
	global $tipos; /*?>
	<table width="100%" border=0 cellspacing=0 cellpadding=0 class="p1">
	<tr>
		<td width=29 align="center"></td>
		<td width="10px" align="center">&nbsp;</td>
		<td width="91px" class="t1"><?php echo write_lang('key_pesquisar','Pesquisar')?>:</td>
		<td width="150px" class=pesq><input type="text" name="m_pesquisa" value="<?=$_POST["m_pesquisa"]?>" class="pesq" style="height:20px"></td>
		<td width="28px"><input type="submit" value="<?php echo write_lang('key_submeter_pesquisa','Submeter Pesquisa')?>" src="../images/btp.jpg" class="btn btn-primary" style="width:120px"></td>
        <td>&nbsp;</td>
	</tr>
	</table>*/?>
	<script>
	function checkFields(){
		return true;
	}
	function changeTipoCupao(){
		$('form[name^="lista_paginas"]').submit();
	}
	</script>
	<table width="100%" height="50px" border=0 cellspacing=0 cellpadding=0 class="p2">
		<tr>
			<td width="5%" align="center">&nbsp;</td>
		    <td width="5%" align="center">&nbsp;</td>
		    <td width="34%" valign="middle" style="font-weight:bold;color:#FFF"><?php echo write_lang('key_voucher_title','Título do cupão')?></td>
			<td width="10%" style="font-weight:bold;color:#FFF"><?php echo write_lang('key_voucher_codelist','Código')?></td>
			<td width="20%" style="font-weight:bold;color:#FFF"><?php echo write_lang('key_voucher_type','Tipo')?></td>
			<td width="20%" style="font-weight:bold;color:#FFF"><?php echo write_lang('key_voucher_state','Estado')?></td>
			<td width="4%" colspan="2" align="right" style="font-weight:bold;color:#FFF">&nbsp;</td>
		</tr>
	</table>
	<table width="100%" height="50px" border=0 cellspacing=0 cellpadding=0 class="p1">
		<tr>



			<td width="5%" align="center"><input onClick='makeSelection("filter","lista_paginas")' type="checkbox" name="filter" id="filter" value="1"  class="cb"></td>
		    <td width="5%" align="center">&nbsp;</td>
		    <td width="34%" valign="middle"><input type="text" name="p_nome" value="<?=$_POST["p_nome"]?>"  style="width:300px"></td>
			<td width="10%" style="font-weight:bold"><input type="text" name="p_codigo" value="<?=$_POST["p_codigo"]?>"  style="width:90px"></td>
			<td width="20%" style="font-weight:bold"></td>
			<td width="20%" style="font-weight:bold">
				<select id="p_tipo" name="p_tipo" onchange="changeTipoCupao();">
					<option value="-1" <?php if( !isset($_POST["p_tipo"]) ||  isset($_POST["p_tipo"]) && $_POST["p_tipo"]==-1) echo 'selected="selected"';  ?>>---</option>
					<option value="0" <?php if( isset($_POST["p_tipo"]) && $_POST["p_tipo"]==0) echo 'selected="selected"';  ?>>Desconto na Encomenda</option>
					<option value="1" <?php if( isset($_POST["p_tipo"]) && $_POST["p_tipo"]==1) echo 'selected="selected"';  ?>>Oferta de produto</option>
					<?/*<option value="2" <?php if( isset($_POST["p_tipo"]) && $_POST["p_tipo"]==2) echo 'selected="selected"';  ?>>Desconto Parceiro</option>*/?>
					<option value="2" <?php if( isset($_POST["p_tipo"]) && $_POST["p_tipo"]==3) echo 'selected="selected"';  ?>>Desconto Cliente</option>
				</select>
			</td>
			<td width="20%">&nbsp;</td>
			<td width="4%" colspan="2" align="right" class=preto>&nbsp;</td>

		</tr>
	</table>
<? }
function show_page_bottom_tools()
{?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td width="130px" class="t1">&nbsp;<?php echo write_lang('key_com_os_seleccionados','Com os seleccionados')?>:</td>
		<td width="125px"><select name="flag" id="flag" class="select1">
				<option value="" selected="selected"></option>
				<option value="1"><?php echo write_lang('key_apagar','Apagar')?></option>
				<?php /* <option value="2"><?php echo write_lang('key_reordenar','Reordenar')?></option>
				<option value="4"><?php echo write_lang('key_tornar_visivel','Tornar visivel')?></option>
				<option value="5"><?php echo write_lang('key_tornar_invisivel','Tornar invisivel')?></option> */?>
			</select>
		</td>
        <td width="5px"></td>

		<td>&nbsp;<input type="submit"  name="submeter" value="<?php echo write_lang('key_aplicar_acao','Aplicar Ação')?>" class="btn btn-primary" style="width:120px"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="890px">
<?
}
function SearchRecords($d)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$d=strtolower(ltrim(rtrim($d)));
	$sql="SELECT distinct ID,
	lower(Texto) as Descricao,length(lower(Texto)) AS len,
	((length(Texto)-length(replace(lower(Texto),'".$d."','')))/length('".$d."'))/(length(lower(Texto))+1-length(replace(lower(Texto),' ','')) ) AS occwds,
	((length(Texto)-length(replace(lower(Texto),'".$d."','')))/length('".$d."')) AS occ
	FROM menus_links_lang
	WHERE ((lower(Texto) LIKE '%".$d."%' OR URL_Nome like '%".$d."%') AND Lang='$admin_lang' ) ORDER BY occ DESC,occwds DESC  ";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	if(!$db_res) echo "ERRO";
	return $rs;
}
function show_options($id_org=0,$id_first=0,$Nomeb,$Nomeb_linguas)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql="Select * from $Nomeb where ID_ORG='$id_first' order by Ordem Asc";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
    echo '<select name="m_familia2" style="width:60%; height:16px">';
	if($id_org==0) echo '<option value="0" selected>- '.write_lang('key_nenhuma','Nenhuma').' -</option>';
	else echo '<option value="0">- '.write_lang('key_nenhuma','Nenhuma').' -</option>';
    while(!$rs->EOF())
    {
        echo '<option value="'. $rs->db_data["ID"] .'"';
		if($id_org==$rs->db_data["ID"]) echo ' selected ';
        $sql="Select * from $Nomeb_linguas where ID_PAI=".$rs->db_data["ID"]. " and Lang='$admin_lang'";
        $rs2=new DataObj;
	    $db_res=$rs2->db_open($db_link,$DB_name,$sql);
		echo '>';
		if(!$rs2->db_data["URL_Nome2"]) echo "N/A";else echo $rs2->db_data["URL_Nome2"];
		echo '</option>';
	    $rs2->db_close();
	    show_optionsb($id_org,$rs->db_data["ID"],$Nomeb,$Nomeb_linguas);
        $rs->db_next();
    }
    $rs->db_close();
    echo '</select>&nbsp;';
    db_closedb_conection($db_link);
}
function show_options_templates($template=0,$Nomeb,$Nomeb_linguas)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql='Select '.$Nomeb.'.ID, '.$Nomeb_linguas.'.Cabecalho from '.$Nomeb.' inner join '.$Nomeb_linguas.' on '.$Nomeb_linguas.'.ID_PAI = '.$Nomeb.'.ID where Visivel=1 and Lang="'.$admin_lang.'"';
	$rs->db_open($db_link,$DB_name,$sql);

	$template = explode(';', $template);
	while(!$rs->EOF())
    {
		echo '<input type="checkbox" value="1" name="template_id'.$rs->db_data["ID"].'"';
		 if(in_array($rs->db_data["ID"],$template)){ echo ' checked="checked" ';}
		echo' />&nbsp;'.$rs->db_data["Cabecalho"].'&nbsp;&nbsp;';

        $rs->db_next();
    }
    $rs->db_close();

    db_closedb_conection($db_link);
}
function show_optionsb($id_org=0,$id_first=0,$Nomeb,$Nomeb_linguas)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql="Select * from $Nomeb where ID_ORG='$id_first' order by Ordem Asc";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
    while(!$rs->EOF())
    {
        echo '<option value="'. $rs->db_data["ID"] .'"';
		if($id_org==$rs->db_data["ID"]) echo ' selected ';
        $sql="Select * from $Nomeb_linguas where ID_PAI=".$rs->db_data["ID"]. " and Lang='$admin_lang'";
        $rs2=new DataObj;
	    $db_res=$rs2->db_open($db_link,$DB_name,$sql);
		echo '>';
		if(!$rs2->db_data["URL_Nome2"]) echo "N/A";else echo $rs2->db_data["URL_Nome2"];
		echo '</option>';
	    $rs2->db_close();
	    show_optionsb($id_org,$rs->db_data["ID"],$Nomeb,$Nomeb_linguas);
        $rs->db_next();
    }
    $rs->db_close();
}
function createRandomPassword($num_chars=6)
{
	$chars = "abcdefghijkmnopqrstuvwxyz0123456789";
	srand((double)microtime()*1000000);
	$i = 0;
	$pass = '' ;

	while ($i < $num_chars)
	{
		$num = rand() % 33;
		$tmp = substr($chars, $num, 1);
		$pass = $pass . $tmp;
		$i++;
	}
	return $pass;
}
function InsertNewRecord()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$rs = new DataObj;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$voucher_title = $_POST["voucher_title"];
	$voucher_type = $_POST["voucher_type"];
	$voucher_dataini = $_POST["voucher_dataini"];
	$voucher_datafim = $_POST["voucher_datafim"];
	$voucher_usage = $_POST["voucher_usage"];
	$voucher_prod_id = $_POST["voucher_prod_id"];
	$voucher_cliente_id = $_POST["voucher_cliente_id"];
	$voucher_desconto_afeta = $_POST["voucher_desconto_afeta"];

	if(isset($_POST['voucher_acumulavel'])){
		$voucher_acumulavel = $_POST['voucher_acumulavel'];
	}else{
		$voucher_acumulavel = 0;
	}

	if($voucher_type==0){
		$voucher_type_discount = $_POST["voucher_type_discount"];
		if($voucher_type_discount==0) $voucher_discount = $_POST["voucher_discount_perc"];
		else $voucher_discount = $_POST["voucher_discount_value"];
		$voucher_partner_profit = 0;
	}
	elseif($voucher_type==4){
		$voucher_type_discount = $_POST["voucher_type_discount"];
		if($voucher_type_discount==0) $voucher_discount = $_POST["voucher_discount_perc"];
		else $voucher_discount = $_POST["voucher_discount_value"];
		$voucher_partner_profit = 0;
	}
	else if($voucher_type==2){
		$voucher_discount = $_POST["voucher_partner_discount"];
		$voucher_partner_profit = $_POST['voucher_partner_profit'];
	}else if($voucher_type==3){
		$voucher_type_discount = $_POST["voucher_type_discount3"];
		if($voucher_type_discount==0) $voucher_discount = $_POST["voucher_partner_discount_perc"];
		else $voucher_discount = $_POST["voucher_partner_discount_value"];
	}
	if($voucher_type==2){

		$rs->db_open($db_link,$DB_name,"Select Nome,Apelido from clientes WHERE ID='$voucher_cliente_id'");
		if(!$rs->EOF()){
			$nome 		= strtoupper(substr(trim($rs->db_data['Nome']), 0,1));
			$apelido 	= strtoupper(substr(trim($rs->db_data['Apelido']), 0,1));

			$voucher_code = $nome.$apelido.createRandomPassword(4);
		}else{
			$voucher_code = createRandomPassword();
		}
		$rs->db_close();
	}else{
		$voucher_code = createRandomPassword();
	}

	if($_POST['voucher_pre'])
	{
		$voucher_code = $_POST['voucher_pre'];
	}
	$sql = "INSERT INTO vouchers(voucher_desconto_afeta,voucher_title,voucher_type,voucher_dataini,voucher_datafim, voucher_usage, voucher_code,voucher_prod_id,voucher_cliente_id,voucher_discount,voucher_partner_profit,voucher_acumulavel,voucher_type_discount) VALUES ('$voucher_desconto_afeta','$voucher_title','$voucher_type','$voucher_dataini','$voucher_datafim','$voucher_usage','$voucher_code','$voucher_prod_id','$voucher_cliente_id','$voucher_discount','$voucher_partner_profit','$voucher_acumulavel','$voucher_type_discount')";
	$res = Runquery($db_link,$DB_name,$sql);
	if(!$res) echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
function DeleteRecord()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$voucher_id = $_POST['voucher_id'];

    $sql="delete from vouchers where voucher_id=$voucher_id";
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

	$voucher_id = $_POST["voucher_id"];
	$voucher_title = $_POST["voucher_title"];
	$voucher_type = $_POST["voucher_type"];
	$voucher_desconto_afeta = $_POST["voucher_desconto_afeta"];

	if($voucher_type==0)
	{
		$voucher_type_discount = $_POST["voucher_type_discount"];
		if($voucher_type_discount==0 || $voucher_type_discount=='') $voucher_discount = $_POST["voucher_discount_perc"];
		else $voucher_discount = $_POST["voucher_discount_value"];
		$voucher_partner_profit = 0;
	}
	else if($voucher_type==2)
	{
		$voucher_discount = $_POST["voucher_partner_discount"];
		$voucher_partner_profit = $_POST['voucher_partner_profit'];
	}
	else if($voucher_type==3)
	{
		$voucher_type_discount = $_POST["voucher_type_discount3"];
		if($voucher_type_discount==0) $voucher_discount = $_POST["voucher_partner_discount_perc"];
		else $voucher_discount = $_POST["voucher_partner_discount_value"];
	}
	else if($voucher_type==4)
	{
		$voucher_type_discount = $_POST["voucher_type_discount"];
		if($voucher_type_discount==0 || $voucher_type_discount=='') $voucher_discount = $_POST["voucher_discount_perc"];
		else $voucher_discount = $_POST["voucher_discount_value"];
		$voucher_partner_profit = 0;
	}
	if(isset($_POST['voucher_acumulavel']))
	{
		$voucher_acumulavel = $_POST['voucher_acumulavel'];
	}
	else
	{
		$voucher_acumulavel = 0;
	}
	$voucher_dataini = $_POST["voucher_dataini"];
	$voucher_datafim = $_POST["voucher_datafim"];
	$voucher_usage = $_POST["voucher_usage"];
	$voucher_code = $_POST["voucher_code"];
	$voucher_prod_id = $_POST["voucher_prod_id"];
	$voucher_cliente_id = $_POST["voucher_cliente_id"];

	$altera=0;
	$rs->db_open($db_link,$DB_name,"Select * from vouchers WHERE voucher_id='$voucher_id'");
	if(!$rs->EOF()) $altera=1;
	$rs->db_close();

	if(!$altera) return InsertNewRecord();
	else
	{
		$sql="Update vouchers Set voucher_desconto_afeta='$voucher_desconto_afeta', voucher_type_discount='$voucher_type_discount',voucher_acumulavel='$voucher_acumulavel',voucher_title='$voucher_title',voucher_type='$voucher_type',voucher_dataini='$voucher_dataini',voucher_datafim='$voucher_datafim',voucher_usage='$voucher_usage',voucher_prod_id='$voucher_prod_id',voucher_cliente_id='$voucher_cliente_id',voucher_discount='$voucher_discount',voucher_partner_profit='$voucher_partner_profit' where voucher_id=$voucher_id";
		$res = Runquery($db_link,$DB_name,$sql);
		db_closedb_conection($db_link);
		return($res);
	}
}
function GetRecords_Recursos($id=0,$id_org=0,$campos="*",$first=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	if($id!=0)
	{
		$cond="where ID=$id";
		if($id_org!=0 || $first!=0)	$cond="$cond and ID_ORG=$id_org";
	}
	else
	{
		$cond="where ID > 0";
		if($id_org!=0 || $first!=0)	$cond=$cond." and ID_ORG=$id_org";
	}
    $sql="Select $campos from menus_links $cond order by Ordem Asc";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	return $rs;
}
function show_menupath($id_org,$table="menus_links",$pagina="pagina.php")
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;  $rs1=new DataObj;
	if($id_org!=0)
	{
		$sql="SELECT * FROM $table WHERE ID=$id_org";
		$rs->db_open($db_link,$DB_name,$sql);
		$codigo=$rs->db_data["ID"];
		$id_org1=$rs->db_data["ID_ORG"];
		$rs->db_close();
		$sql="SELECT * FROM $table"."_lang WHERE ID_PAI=$codigo";
		$rs1->db_open($db_link,$DB_name,$sql);
		$C=stripslashes($rs1->db_data["URL_Nome2"]);
		$C1=$C!="" ? $C : "[EDIT]";
		$rs1->db_close();
	}
	if($codigo!=0)
	{
		$str.=show_menupath($id_org1);
		$str.=" - <a href=\"$pagina"."?ID=$codigo&ID_ORG=$id_org1&Dumpe=".uniqid('')."\" class=\"new_caminho\">$C1</a>";
	}
	else $str.=" - <a href=\"list.php?Dump=".uniqid('')."\" class=\"new_caminho\">Menus</a>";
	return $str;
}

function show_content_list($pai=0,$contador=-5,$count=0,$md=0,$span=0)
{
	$path="../../core/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$admin_lang,$m_resumo,$tipos;

	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$contador+=5;
	$rs=new DataObj;$rs2=new DataObj;

	$and=' Where 1=1';
	if(isset($_POST["p_nome"]) && $_POST["p_nome"]){
		$and.=' and voucher_title like "%'.$_POST['p_nome'].'%" ';
	}
	if(isset($_POST["p_tipo"]) && $_POST["p_tipo"]>=0){
		$and.=' and voucher_type='.$_POST['p_tipo'].' ';
	}
	if(isset($_POST["p_codigo"]) && $_POST["p_codigo"]){
		$and.=' and voucher_code like "%'.$_POST['p_codigo'].'%" ';
	}

	$sql="Select * from vouchers ".$and." order by voucher_dataini, voucher_datafim";;
	$rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	if(!$rs->EOF()){

		while(!$rs->EOF())
		{
			$count++;
			$voucher_id = $rs->db_data["voucher_id"];
			$voucher_title = $rs->db_data["voucher_title"];
			$voucher_type = $rs->db_data["voucher_type"];
			$voucher_dataini = $rs->db_data["voucher_dataini"];
			$voucher_datafim = $rs->db_data["voucher_datafim"];
			$voucher_usage = $rs->db_data["voucher_usage"];
			$voucher_code = $rs->db_data["voucher_code"];
			$voucher_prod_id = $rs->db_data["voucher_prod_id"];
			$voucher_discount = $rs->db_data["voucher_discount"];
			$voucher_partner_profit = $rs->db_data["voucher_partner_profit"];

			if(!$voucher_type || $voucher_type==0) {
				$dst = write_lang('key_voucher_orderdiscount','Desconto na encomenda');
			} else if($voucher_type==1) {
				$dst = write_lang('key_voucher_productoffer','Oferta de produto');
			}else if($voucher_type==2) {
				$dst = write_lang('key_voucher_partnerdiscount','Desconto Parceiro');
			}else if($voucher_type==3) {
				$dst = "Desconto Cliente";
			}
			$d_ini = strtotime($voucher_dataini);
			$d_fim = strtotime($voucher_datafim);
			if($d_ini<=strtotime("now") && $d_fim>=strtotime("now"))
			{
				$act = '<span style="color:#235ef4;font-weight:bold">'.write_lang('key_voucher_active','Activo').' - '.$voucher_dataini.'/'.$voucher_datafim.'</span>';
				$act_image = '<img title="" src="ok.png" border=0 align="absmiddle" />';
				if($cor=='#c8ecfa')	{$cor="#def2fa";$class="r1";$n="1";}
				else				{$cor="#c8ecfa";$class="r2";$n="2";}

				if($voucher_usage==1){
					$sql = "Select * from encomendas_produtos WHERE voucher_id='".$voucher_id."'";
					$rs2->db_open($db_link, $DB_name, $sql);
					if(!$rs2->EOF()) {
						$act = '<span style="color:#FF0000;font-weight:bold">'.write_lang('key_voucher_inactivealreadyused','Inactivo - Já foi utilizado').'</span>';
						$act_image = '<img title="" src="hidden.png" border=0 align="absmiddle" />';
						if($cor=='#F6F6F6')	{$cor="#e8e7e7";$class="r1";$n="1";}
						else				{$cor="#F6F6F6";$class="r2";$n="2";}
					}
				}
			}
			else
			{
				$act = '<span style="color:#FF0000;font-weight:bold">'.write_lang('key_voucher_inactive','Inactivo').' - '.$voucher_dataini.'/'.$voucher_datafim.'</span>';
				$act_image = '<img title="" src="hidden.png" border=0 align="absmiddle" />';
				if($cor=='#F6F6F6')	{$cor="#e8e7e7";$class="r1";$n="1";}
				else				{$cor="#F6F6F6";$class="r2";$n="2";}
			}
			$i=0;
			?><table height="50px" border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
				<tr bgcolor="<?=$cor;?>" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
					<td width="5%" align="center"><input type="checkbox" name="chk_<?=$voucher_id?>" id="chk_<?=$voucher_id?>" value="<?=$voucher_id?>" class="cb"></td>
		            <td width="5%" align="center"><?php echo $act_image?></td>
		            <td width="36%" valign="middle"><? while($i<$contador){echo "-";$i++;}?><a title="<?php echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?voucher_id=<?=$voucher_id;?>&Dump=<? echo uniqid("");?>"><? if($voucher_title){echo htmlentities_b(stripslashes($voucher_title));} else echo "[Edit]";?></a></td>
					<td width="10%" style="font-weight:bold"> <?php echo $voucher_code?></td>
					<td width="20%" style="font-weight:bold"> <?php echo $dst?></td>
					<td width="20%"><?php echo $act?></td>
					<td width="2%" align="right" class=preto><a class="italico" href="pagina.php?voucher_id=<?=$voucher_id?>&task=duplicar&Dump=<? echo uniqid("");?>"><img title="<?php echo write_lang('key_duplicar_pagina','Duplicar Página')?>" src="duplicate.png" border=0></a></td>
					<td width="2%" align="right" class=preto><a class="italico" href="pagina.php?voucher_id=<?=$voucher_id?>&task=apagar&Dump=<? echo uniqid("");?>"><img title="<?php echo write_lang('key_apagar_pagina','Apagar Página')?>" src="del2.png" border=0></a></td>
				</tr>
			</table><?php
			$rs->db_next();
			$i++;
		}

	}else{?>
			<table height="50px" border=0 cellspacing=0 cellpadding=0 class="r1">
				<tr bgcolor="#e8e7e7" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='#e8e7e7'">
					<td colspan="8" align="center"><?php echo write_lang('key_voucher_no_vouchers','Não existem cupões.')?></td>
				</tr>
			</table>
	<?php }
	$rs->db_close();
}?>