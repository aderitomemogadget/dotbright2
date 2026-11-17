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
<?
}
function show_fast_tools()
{
	global $ID;
	?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
		<td class="titulo2" align="center">FAST TOOLS</td>
	</tr>
	<tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="Nova Linha de Portes" onclick="javascript:window.open('pagina.php?ID=0&Dump=<?=uniqid("");?>','_self')"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<? }
function show_page_tools($filter=0)
{
	global $tipos; ?>
	<div class="panel panel-inverse overflow-hidden m-b-12">
        <div class="panel-heading">
            <h4 style="color:#fff">PORTES</h4>
        </div>
    </div>
	<script>
	function checkFields(){
		return true;
	}
	</script>
	<table width="100%"  height="50px" border=0 cellspacing=0 cellpadding=0 class="p2">
				<tr>
					<td width="10%" align="center" align="center" style="font-weight:bold;color:#FFF">Sujeitar a Iva</td>
		            <td width="15%" valign="middle" align="center" style="font-weight:bold;color:#FFF">Peso Mínimo</td>
		            <td width="15%" valign="middle" align="center" style="font-weight:bold;color:#FFF">Peso Máximo</td>
		            <td width="20%" valign="middle" align="center" style="font-weight:bold;color:#FFF">Preço</td>
		            <td width="20%" valign="middle" align="center" style="font-weight:bold;color:#FFF">Metodo Expedição</td>
		            <td width="15%" valign="middle" align="center" style="font-weight:bold;color:#FFF">Zona</td>
		            <td width="5%" valign="middle">&nbsp;</td>
				</tr>
			</table>
<? }
function show_page_bottom_tools()
{?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
	<tr>
        <td>&nbsp;</td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<?
}
function InsertNewRecord()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);


	$PesoMin 	= $_POST["PesoMin"];
	$PesoMax	= $_POST["PesoMax"];
	$Preco 		= $_POST['Preco'];
	$zone_type 	= $_POST['zone_type'];
	$ID_MetodoExpedicao 	= $_POST['ID_MetodoExpedicao'];
	$usar_iva 	= $_POST['usar_iva'];


	$sql = "INSERT INTO portes(PesoMin,PesoMax,Preco,zone_type,ID_MetodoExpedicao,usar_iva) VALUES ('$PesoMin','$PesoMax','$Preco','$zone_type','$ID_MetodoExpedicao','$usar_iva')";
	$res = Runquery($db_link,$DB_name,$sql);
	db_closedb_conection($db_link);
	return($res);
}
function DeleteRecord()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$id = $_POST['ID'];

    $sql="delete from portes where ID=$id";
	$res=Runquery($db_link,$DB_name,$sql);

	if(!$res) echo db_error($db_link);
	return($res);
}
function ChangeRecord()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;

	$ID 					= $_POST["ID"];
	$PesoMin 				= $_POST["PesoMin"];
	$PesoMax				= $_POST["PesoMax"];
	$Preco 					= $_POST['Preco'];
	$zone_type 				= $_POST['zone_type'];
	$ID_MetodoExpedicao 	= $_POST['ID_MetodoExpedicao'];
	$usar_iva 				= $_POST['usar_iva'];

	$altera=0;
	$rs->db_open($db_link,$DB_name,"Select * from portes WHERE ID='$ID'");
	if(!$rs->EOF()){
		$altera=1;
	}
	$rs->db_close();

	if(!$altera) return InsertNewRecord();
	else
	{
		$sql="UPDATE portes Set usar_iva='$usar_iva',PesoMin='$PesoMin',PesoMax='$PesoMax',Preco='$Preco',zone_type='$zone_type',ID_MetodoExpedicao='$ID_MetodoExpedicao'where ID=$ID";
		$res = Runquery($db_link,$DB_name,$sql);
		db_closedb_conection($db_link);
		return($res);
	}
}

function show_content_list($pai=0,$contador=-5,$count=0,$md=0,$span=0)
{
	$path="../../core/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$admin_lang,$m_resumo,$tipos;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$rs2=new DataObj;
	$sql='SELECT * from portes order by ID_MetodoExpedicao, zone_type ASC,PesoMin ASC,PesoMax ASC,Preco ASC';
	$rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	if(!$rs->EOF()){

		while(!$rs->EOF())
		{
			$count++;
			$id 	 			= $rs->db_data["ID"];
			$PesoMin 			= $rs->db_data["PesoMin"];
			$PesoMax 			= $rs->db_data["PesoMax"];
			$Preco 				= $rs->db_data["Preco"];
			$zone_type 			= $rs->db_data["zone_type"];
			$ID_MetodoExpedicao = $rs->db_data["ID_MetodoExpedicao"];
			$usar_iva 			= $rs->db_data["usar_iva"];

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

			$sql='SELECT Abreviatura,Cabecalho FROM metodos_expedicao me INNER JOIN metodos_expedicao_lang mel ON mel.ID_PAI=me.ID WHERE me.ID='.$ID_MetodoExpedicao.' AND mel.Lang="'.$admin_lang.'"';
			$rs2->db_open($db_link,$DB_name,$sql);
			if(!$rs2->EOF())
			{
				$Abreviatura = $rs2->db_data["Abreviatura"];
				$nome_metodo = $rs2->db_data['Cabecalho'];
			}
			$rs2->db_close();

			if($Abreviatura != '')$nome_metodo=$Abreviatura;

			$nome_pais = 'Zone '.$zone_type;

			$i=0;
			?><table height="50px" border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
				<tr bgcolor="<?=$cor;?>" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
					<td height="32px" width="10%" align="center" align="center"><a class="lista_links" href="pagina.php?ID=<?=$id;?>&Dump=<? echo uniqid("");?>"><?php if($usar_iva) echo 'Sim'; else echo 'Não';?></a></td>
		            <td height="32px" width="15%" valign="middle" align="center"><a class="lista_links" href="pagina.php?ID=<?=$id;?>&Dump=<? echo uniqid("");?>"><? echo $PesoMin;?></a></td>
		            <td height="32px" width="15%" valign="middle" align="center"><a class="lista_links" href="pagina.php?ID=<?=$id;?>&Dump=<? echo uniqid("");?>"><? echo $PesoMax;?></a></td>
		            <td height="32px" width="20%" valign="middle" align="center"><a class="lista_links" href="pagina.php?ID=<?=$id;?>&Dump=<? echo uniqid("");?>"><? echo numberformat($Preco);?></a></td>
		            <td height="32px" width="20%" valign="middle" align="center"><a class="lista_links" href="pagina.php?ID=<?=$id;?>&Dump=<? echo uniqid("");?>"><? if($nome_metodo){echo htmlentities_b(stripslashes($nome_metodo));} else echo "[Edit]";?></a></td>
		            <td height="32px" width="15%" valign="middle" align="center"><a class="lista_links" href="pagina.php?ID=<?=$id;?>&Dump=<? echo uniqid("");?>"><? if($nome_pais){echo htmlentities_b(stripslashes($nome_pais));} else echo "[Edit]";?></a></td>
		            <td height="32px" width="30px" align="center" class="preto"><a class="italico" href="pagina.php?ID=<?=$id?>&task=duplicar&Dump=<? echo uniqid("");?>"><img title="<?php echo write_lang('key_duplicar_pagina','Duplicar Página')?>" src="duplicate.png" border=0></a></td>
					<td height="32px" width="30px" align="center" class="preto"><a class="italico" href="pagina.php?ID=<?=$id?>&task=apagar&Dump=<? echo uniqid("");?>"><img title="<?php echo write_lang('key_apagar_pagina','Apagar Página')?>" src="del2.png" border=0></a></td>
				</tr>
			</table><?
			$rs->db_next();
			$i++;
		}

	}else{?>
			<table height="50px" border=0 cellspacing=0 cellpadding=0 class="r1">
				<tr bgcolor="#e8e7e7" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='#e8e7e7'">
					<td colspan="8" align="center">Não Existem Linhas de Portes</td>
				</tr>
			</table>
	<? }
	$rs->db_close();
}


?>