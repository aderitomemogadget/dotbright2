<?
	include "settings.inc.php";
	if($task=="apagar"){$task_button="Apagar este Registo";$task_form="apaga";}
	elseif($task=="duplicar"){$task_button="Duplicar este Registo";$task_form="duplicar";}
	elseif($ID==0){$task_button="Gravar o Novo Registo";$task_form="insere";}
	else {$task_button="Gravar alterações";$task_form="altera";}

	if($ID!=0)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs=new DataObj;
		$sql="Select * from products_testemunhos WHERE ID=$ID";
		$db_res=$rs->db_open($db_link,$DB_name,$sql);
		if($rs)
		{
			$prods 		= get_produtc_info($rs->db_data['ID_PROD']);
			$nomeprod 	= $prods['prod_nome'];
			$Nome 		= $rs->db_data['Nome'];
			$Local 		= $rs->db_data['Local'];
			$Pais 		= $rs->db_data['Pais'];
			$Visivel 	= $rs->db_data["Visivel"];
			$Texto 		= $rs->db_data["Texto"];
			$Data 		= $rs->db_data["Data"];
		}
		$rs->db_close(1);
	}
	include "../layout/header.inc.php";
	include "../layout/body.inc.php";
?>
	<div class="center">
		<form name="form" method="post" action="pagina_trata.php?ID=<?=$ID?>&task_form=<?=$task_form?>&dump=<?=uniqID("")?>" enctype="multipart/form-data">
			<input type="hidden" name="slashes" value="'">
			<input type="hidden" name="ID" value="<?=$ID?>">
			<table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td valign="top"><?  include "../layout/menus.inc.php"; ?>
					<table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding="4" class="text-center">
					<tr>
						<td valign="top">
							<table width="100%" height="30px" border=0 cellspacing=0 cellpadding="5" class="titulo_janela">
							<tr>
								<td width="100">Tornar visivel:&nbsp;
						          <input type="checkbox" name="visivel" value="1" <? if($Visivel) echo "checked"; ?>></td>
                                <td align="right"><? if($ID){?>Criado:&nbsp;<?=$Data?><? }?></td>
							</tr>
							</table>
						</td>
					</tr>
                    <tr>
						<td align="left" valign="top" class="preto">

                            <table width="100%" cellpadding=0 cellspacing=0 border=0>
                            <tr>
								<td width="5" height="26"></td>
								<td width="200" class="new_preto">Nome:&nbsp;</td>
								<td><input name="nome" style="WIDTH: 100%" maxlength="255" value="<?=$Nome?>" class="form-control"></td>
								<td width="5" height="26"></td>
							</tr>
							</table>
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
								<td width="5" height="26"></td>
								<td width="200" class="new_preto">Local:</td>
								<td><input name="local" style="WIDTH: 100%" maxlength="255" value="<?=$Local?>" class="form-control"></td>
								<td width="5" height="26"></td>
							</tr>
							</table>
							<table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
								<td width="5" height="26"></td>
								<td width="200" class="new_preto">País:</td>
								<td><input name="pais" style="WIDTH: 100%" maxlength="255" value="<?=$Pais?>" class="form-control"></td>
								<td width="5" height="26"></td>
							</tr>
							</table>
							<table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
								<td width="5" height="26"></td>
								<td width="200" class="new_preto">Produto:</td>
								<td><a target="_blank" href="<?=$site_http_url?>admin/products/products.php?ID=<?=$prods['prod_id']?>"><?=$nomeprod?></a></td>
								<td width="5" height="26"></td>
							</tr>
							</table>
                            <table width="100%" cellpadding="10" cellspacing="0" border="0">
                            <tr>
                                <td class="preto"> Testemunho:</td>
                            </tr>
							</table>
                            <table width="100%" cellpadding="10" cellspacing="0" border="0">
                            <tr>
                                <td align="left" class="preto"><textarea id="texto" name="texto"><?=stripslashes($Texto);?></textarea></td>
                            </tr>
							</table>
						</td>
					</tr>

					</table>
                    <? show_page_buttons();?>
                </td>
				<td width="10px">&nbsp;</td>
				<td valign="top"></td>
            </tr>
            </table>
		</form>
	</div>
<? 	include "../layout/bottom.inc.php";?>