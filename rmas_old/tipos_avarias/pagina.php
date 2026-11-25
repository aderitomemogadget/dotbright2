<?
	include "settings.inc.php";
	if($task=='apagar'){$task_button=write_lang('key_apagar_este_registo','Apagar este Registo');$task_form='apaga';}
	elseif($task=='duplicar'){$task_button=write_lang('key_duplicar_este_registo','Duplicar este Registo');$task_form='duplicar';}
	elseif($zone_id==0){$task_button=write_lang('key_gravar_novo_registo','Gravar Novo Registo');$task_form='insere';}
	else {$task_button=write_lang('key_gravar_alteracoes','Gravar alterações');$task_form='altera';}
	$D=getdate();
	
	if($zone_id!=0)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs = new DataObj;
		$sql = "Select * from tipos_avarias WHERE zone_id=$zone_id order by zone_order Asc";
		$rs->db_open($db_link,$DB_name,$sql);
		if($rs)
		{			
			$zone_order=$rs->db_data["zone_order"];
			$zone_type=$rs->db_data["zone_type"];
			$zone_visible=$rs->db_data["zone_visible"];			
			
			$rs2 = new DataObj;
			$sql = "Select * from tipos_avarias_lang where zone_id=$zone_id and zone_lang='$admin_lang' order by zone_id Desc";
			$rs2->db_open($db_link,$DB_name,$sql);	
			$zone_name = stripslashes($rs2->db_data["zone_name"]);
			$rs2->db_close();
		}
		$rs->db_close(1);
	}
	include "../layout/header.inc.php";	
	include "../layout/body.inc.php";	
?>
	<div class="center">
		<form name="form" method="post" action="pagina_trata.php?zone_id=<?=$zone_id?>&task_form=<?=$task_form?>&dump=<?=uniqID("")?>" enctype="multipart/form-data">
			<input type="hidden" name="m_lang" value="<?=$admin_lang;?>" />
			<input type="hidden" name="zone_id" value="<?=$zone_id;?>" />
			<input type="hidden" name="slashes" value="'" />
			<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
            <tr>
                <td valign="top" align="left"><? include "../layout/menus.inc.php"; ?>
					<table class="new_quadros" width="880px" border=0 cellspacing=0 cellpadding="4" align="center">
					<tr> 
						<td valign="top">
						<table width="100%" border=0 cellspacing=0 cellpadding="5" class="subtitulo_janela">
							<tr>
								<td valign="middle"><a href="list.php" class="branco"> &#60; Voltar à lista de avarias</a></td>
							</tr>
							</table>
							<table width="100%" height="30px" border=0 cellspacing=0 cellpadding="5" class="titulo_janela">
							<tr>
								<td width="100">Visível:&nbsp;<input type="checkbox" name="zone_visible" value="1" <? if($zone_visible) echo "checked"; ?>></td>
                                <td align="right"></td>
							</tr>
							</table>
                        
					</tr>
                    <tr>
						<td align="left" valign="top" class="preto">
							<table width="100%" cellpadding=0 cellspacing=0 border=0 style="background:url(../images/pagpub_cx_fundo.jpg) repeat-x;">
							<tr>
								<td width="5" height="26"><img src="../images/pagpub_cx_esq.jpg"></td>
								<td width="200" class="new_preto"><? echo write_lang('key_ordem','Ordem')?>:&nbsp;</td>
                                <td><input name="zone_order" style="WIDTH: 58px; HEIGHT: 16px" maxlength="5" value="<?=$zone_order;?>" class="new_input"></td>
								<td width="5" height="26"><img src="../images/pagpub_cx_dir.jpg"></td>
							</tr>
                            </table><!-- 
							<table width="100%" cellpadding=0 cellspacing=0 border=0 style="background:url(../images/pagpub_cx_fundo.jpg) repeat-x;">
							<tr>
								<td width="5" height="26"><img src="../images/pagpub_cx_esq.jpg"></td>
								<td width="200" class="new_preto">Zone:&nbsp;</td>
                                <td><select name="zone_type"><? for($a=0;$a<9;$a++){?><option value="<? echo $a;?>" <? if($zone_type==$a){?>selected="selected"<? }?>>Zone <? echo $a;?></option><? }?></select></td>
								<td width="5" height="26"><img src="../images/pagpub_cx_dir.jpg"></td>
							</tr>
                            </table> -->
                            <table width="100%" cellpadding=0 cellspacing=0 border=0 style="background:url(../images/pagpub_cx_fundo.jpg) repeat-x;">
                            <tr>
								<td width="5" height="26"><img src="../images/pagpub_cx_esq.jpg"></td>
								<td width="200" class="new_preto"> País:&nbsp;</td>
								<td><input name="zone_name" style="WIDTH: 100%; HEIGHT: 16px" maxlength="255" value="<? echo $zone_name?>" class="new_input" /></td>
								<td width="5" height="26"><img src="../images/pagpub_cx_dir.jpg"></td>
							</tr>
							</table>
                           
						</td>
					</tr>
					
					</table>
                    <? show_page_buttons();?>
                </td>
				<td width="10px">&nbsp;</td>
				<td valign="top"><? show_fast_tools();?></td>
            </tr>
            </table>
		</form>
	</div>
<? 	include "../layout/bottom.inc.php";?>