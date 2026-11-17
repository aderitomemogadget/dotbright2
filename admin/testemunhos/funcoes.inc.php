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
					?><input class="btn btn-secondary" type="button" name="cancelar" value="Cancelar" onClick="window.open('list.php','_self')"><?
				}
				if($task_form=="altera"){?><input class="btn btn-danger" type="button" name="Apagar" value="Apagar" onClick="window.open('pagina_trata.php?ID=<?=$ID?>&task_form=apaga','_self')"><? }
			?>
            <input class="btn btn-secondary" type="button" name="cancelar" value="Cancelar" onClick="window.open('list.php','_self')">
		</td>
     </tr>
	</table>

<?
}
function show_fast_tools()
{
	global $ID;?>

	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>

		<td class="titulo2 text-center">FAST TOOLS</td>

	</tr>
	<tr>

		<td class="preto text-center" style="color:#FFF">Sem opções</td>

	</tr>
	</table>

<? }
function show_page_tools($filter=0)
{
	global $tipos; /*?>
	<table width="100%" border=0 cellspacing=0 cellpadding=0 class="p1">
	<tr>
		<td width=29 align="center"></td>
		<td width="10px" align="center">&nbsp;</td>
		<td width="91px" class="t1"><?php echo write_lang('key_pesquisar','Pesquisar')?>:</td>
		<td width="150px" class=pesq><input type="text" name="m_pesquisa" value="<?=$_POST["m_pesquisa"]?>" class="form-control"  ></td>
		<td width="28px"><input type="submit" value="<?php echo write_lang('key_submeter_pesquisa','Submeter Pesquisa')?>" class="btn btn-primary" ></td>
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
	<table width="100%" height="50px" border=0 cellspacing=0 cellpadding=0 class="p1">
		<tr>
			<td width="29" align="center"><input onClick='makeSelection("filter","lista_paginas")' type="checkbox" name="filter" id="filter" value="1"  class="cb"></td>
		    <td width="4" align="center">&nbsp;</td>
		    <td valign="middle" align="center" width="120" valign="middle" style="font-weight:bold;color:#FFF">Nome Produto</td>
		    <td valign="middle" align="center" width="120" valign="middle" style="font-weight:bold;color:#FFF">Nome</td>
			<td valign="middle" align="center" width="120" style="font-weight:bold;color:#FFF">Cidade</td>
			<td valign="middle" align="center" width="120" style="font-weight:bold;color:#FFF">País</td>
			<td align="center" width="120" style="font-weight:bold;color:#FFF">Data</td>
			<td align="center" width="50" style="font-weight:bold;color:#FFF">Estado</td>
			<td width="100" colspan="2" align="right" style="font-weight:bold;color:#FFF">&nbsp;</td>
		</tr>
	</table>
<? }
function show_page_bottom_tools()
{?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td width="130px" class="t1">&nbsp;Com os seleccionados:</td>
		<td width="125px"><select name="flag" id="flag" class="select1">
				<option value="" selected="selected"></option>
				<option value="1">Apagar</option>
				<option value="4">Tornar visivel</option>
				<option value="5">Tornar invisivel</option>
			</select>
		</td>
        <td width="5px"></td>
		<td>
		</td>
		<td>&nbsp;<input type="submit"  name="submeter" value="Aplicar Ação" class="btn btn-primary" style="width:120px"></td>
	</tr>
	</table>

<?
}

function DeleteRecord()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$id = $_GET['ID'];

    $sql="delete from products_testemunhos where ID=$id";
	$res=Runquery($db_link,$DB_name,$sql);
	return($res);
}
function ChangeRecord($id,$id_org,$ordem,$visivel,$name,$url_link,$tipo=0,$m_lang,$largura,$altura,$img_path,$Img_peq1,$Apagar_img1,$tem_foto1,$texto,$texto2)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;

	$id 		= $_POST['ID'];
	if(isset($_POST['visivel'])) 	$visivel 	= $_POST['visivel'];
	else 							$visivel 	= 0;
	$nome 		= addslashes($_POST['nome']);
	$local 		= addslashes($_POST['local']);
	$pais 		= addslashes($_POST['pais']);
	$texto 		= addslashes($_POST['texto']);

	$altera=0;
	$rs->db_open($db_link,$DB_name,"Select * from products_testemunhos WHERE ID=$id");
	if(!$rs->EOF())
	{
		$altera=1;
	}
	$rs->db_close();

	if($altera)	{

		$sql='UPDATE products_testemunhos SET Nome="'.$nome.'",Local="'.$local.'",Pais="'.$pais.'",Texto="'.$texto.'",Visivel="'.$visivel.'" where ID='.$id;
		$res=Runquery($db_link,$DB_name,$sql);
		db_closedb_conection($db_link);
		return($res);
	}
}

function show_content_list($pai=0,$contador=-5,$count=0,$md=0,$span=0)
{
	$path="../../core/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$admin_lang,$m_resumo,$tipos;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$contador+=5;
	$rs=new DataObj;$rs2=new DataObj;
	$sql="Select * from products_testemunhos order by Visivel,Data";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	while(!$rs->EOF())
	{
		$count++;
		$IDb 		= $rs->db_data['ID'];
		$prods 		= get_produtc_info($rs->db_data['ID_PROD']);
		$nomeprod 	= $prods['prod_nome'];
		$Nome 		= $rs->db_data['Nome'];
		$Local 		= $rs->db_data['Local'];
		$Pais 		= $rs->db_data['Pais'];
		$Visivel 	= $rs->db_data["Visivel"];
		$Data 		= $rs->db_data["Data"];

		if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
		else				{$cor="#FFFFFF";$class="r2";$n="2";}
		$i=0;
		?><table height="50px" border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
		<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
			<td width="29" class="text-center"><input type="checkbox" name="chk_<?=$IDb?>" id="chk_<?=$IDb?>" value="<?=$IDb?>" class="cb"></td>
			<td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg"></td>

            <td valign="middle" align="center" width="120"><a title="editar página" class="lista_links" href="pagina.php?ID=<?=$IDb;?>&Dump=<? echo uniqid("");?>"><? echo htmlentities_b(stripslashes($nomeprod)); ?></a></td>
			<td valign="middle" align="center" width="120"><a title="editar página" class="lista_links" href="pagina.php?ID=<?=$IDb;?>&Dump=<? echo uniqid("");?>"><? echo htmlentities_b(stripslashes($Nome)); ?></a></td>
			<td valign="middle" align="center" width="120"><a title="editar página" class="lista_links" href="pagina.php?ID=<?=$IDb;?>&Dump=<? echo uniqid("");?>"><? echo htmlentities_b(stripslashes($Local)); ?></a></td>
			<td valign="middle" align="center" width="120"><a title="editar página" class="lista_links" href="pagina.php?ID=<?=$IDb;?>&Dump=<? echo uniqid("");?>"><? echo htmlentities_b(stripslashes($Pais)); ?></a></td>
			<td valign="middle" align="center" width="120"><a title="editar página" class="lista_links" href="pagina.php?ID=<?=$IDb;?>&Dump=<? echo uniqid("");?>"><? echo htmlentities_b(stripslashes($Data)); ?></a></td>

            <td valign="middle" align="center" width="50"><img src="<? if($Visivel) echo "ok.png"; else echo "hidden.png";?>" border=0 align="absmiddle"></td>
            <td width="100" align="right" class=preto><a class="italico" href="pagina.php?ID=<?=$IDb?>&task=apagar&Dump=<? echo uniqid("");?>"><img title="apagar pagina" src="del.png" border=0></a></td>
		</tr>
		</table><?
		$rs->db_next();
		$i++;
	}
	$rs->db_close();
}
?>