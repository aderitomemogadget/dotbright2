<?php
	include "settings.inc.php";
	if($task=="apagar"){$task_button = write_lang('key_apagar_este_registo','Apagar este Registo');$task_form="apaga";}
	elseif($task=="duplicar"){$task_button = write_lang('key_duplicar_este_registo','Duplicar este Registo');$task_form="duplicar";}
	elseif($ID==0){$task_button = write_lang('key_gravar_novo_registo','Gravar Novo Registo');$task_form="insere";}
	else {$task_button = write_lang('key_gravar_alteracoes','Gravar alterações');$task_form="altera";}
	$D=getdate();


	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;$rs2 = new DataObj;
	if($ID!=0)
	{



		$sql = 'SELECT * FROM portes WHERE ID='.$ID;
		$rs->db_open($db_link,$DB_name,$sql);
		if($rs)
		{
			$PesoMin 			= $rs->db_data["PesoMin"];
			$PesoMax 			= $rs->db_data["PesoMax"];
			$Preco 				= $rs->db_data["Preco"];
			$ID_Pais 			= $rs->db_data["ID_Pais"];
			$zone_type 			= $rs->db_data["zone_type"];
			$ID_MetodoExpedicao = $rs->db_data["ID_MetodoExpedicao"];
			$usar_iva 			= $rs->db_data["usar_iva"];
		}
		$rs->db_close();
	}
	include "../layout/header.inc.php";
	include "../layout/body.inc.php";
?>
<script>



</script>
	<div class="center">
		<form name="form" method="post" action="pagina_trata.php?ID=<?php echo $ID?>&task_form=<?php echo $task_form?>&dump=<?php echo uniqID("")?>" enctype="multipart/form-data">
			<input type="hidden" name="ID" value="<?php echo $ID;?>" />
			<input type="hidden" name="slashes" value="'" />
			<table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td valign="top" align="left"><? include "../layout/menus.inc.php"; ?>
                	<div class="panel panel-inverse overflow-hidden m-b-12">
				        <div class="panel-heading">
				            <h4 style="color:#fff">PORTES</h4>
				        </div>
				    </div>
						<table class="titulo_janela" width="100%" height="30px" cellspacing="0" cellpadding="5" border="0">
						<tr>
							<td valign="middle"><a href="list.php" class="branco"> &#60; Voltar para a lista de linhas de Portes</a></td>
						</tr>
						</table>

					<table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
                    <tr>
						<td align="left" valign="top" class="preto">
							<table class="border1" style="background-color: rgb(246, 246, 246);" width="100%" cellspacing="0" cellpadding="2" border="0" bgcolor="#F6F6F6">
							<tr>
								<td>
									<table width="100%" cellpadding=2 cellspacing=2 border=0>
			                        <tr>
										<td width="5" height="26"></td>
										<td width="200" class="new_preto">&nbsp;&nbsp;Peso Mínimo (Kg):&nbsp;</td>
										<td class="new_preto"><input  type="text" name="PesoMin" id="PesoMin" style="WIDTH: 200px"  value="<?php echo $PesoMin;?>" class="new_input" /></td>
										<td width="5" height="26"></td>
									</tr>
									</table>
									<table width="100%" cellpadding=2 cellspacing=0 border=0>
			                        <tr>
										<td width="5" height="26"></td>
										<td width="200" class="new_preto">&nbsp;&nbsp;Peso Máximo (Kg):&nbsp;</td>
										<td class="new_preto"><input  type="text" name="PesoMax" id="PesoMax" style="WIDTH: 200px"  value="<?php echo $PesoMax;?>" class="new_input" /></td>
										<td width="5" height="26"></td>
									</tr>
									</table>
									<table width="100%" cellpadding=2 cellspacing=0 border=0>
			                        <tr>
										<td width="5" height="26"></td>
										<td width="200" class="new_preto">&nbsp;&nbsp;Zona:&nbsp;</td>
										<td class="new_preto">
											<select name="zone_type" id="zone_type"><? for($a=0;$a<9;$a++){?><option value="<? echo $a;?>" <? if($zone_type==$a){?>selected="selected"<? }?>>Zone <? echo $a;?></option><? }?></select>
										</td>
										<td width="5" height="26"></td>
									</tr>
									</table>
									<table width="100%" cellpadding=2 cellspacing=0 border=0>
			                        <tr>
										<td width="5" height="26"></td>
										<td width="200" class="new_preto">&nbsp;&nbsp;Método de Expedição:&nbsp;</td>
										<td class="new_preto">
											<select name="ID_MetodoExpedicao" id="ID_MetodoExpedicao">
												<?php
												$sql='SELECT me.ID,me.Abreviatura,mel.Cabecalho FROM metodos_expedicao me INNER JOIN metodos_expedicao_lang mel ON mel.ID_PAI=me.ID WHERE mel.Lang="'.$admin_lang.'" order by mel.Cabecalho ASC';
												$rs2->db_open($db_link,$DB_name,$sql);
												while(!$rs2->EOF()){
													$nome_metodo 	= $rs2->db_data['Cabecalho'];
													$nome_metodo2 	= $rs2->db_data['Abreviatura'];
													$iddb 			= $rs2->db_data['ID'];

													if(!$nome_metodo2)$nome_metodo2=$nome_metodo;

													echo '<option value="'.$iddb.'" ';
													if($iddb==$ID_MetodoExpedicao) echo ' selected="selected" ';
													echo ' >'.$nome_metodo2.'</option>';
													$rs2->db_next();
												}
												$rs2->db_close();
												?>
											</select>
										</td>
										<td width="5" height="26"></td>
									</tr>
									</table>
									<table width="100%" cellpadding=2 cellspacing=0 border=0>
		                            <tr>
										<td width="5" height="26"></td>
										<td width="200" class="new_preto">&nbsp;&nbsp;Preço S/IVA:&nbsp;</td>
										<td style="font-weight:bold"><div style="width:100%"><input  type="text" name="Preco" id="Preco" style="WIDTH: 200px"  value="<?php echo $Preco;?>" class="new_input" /> <input id="usar_iva" name="usar_iva" value="1" type="checkbox" <?php if($usar_iva) echo 'checked="checked"'; ?> />Sujeitar a IVA</div></td>
										<td width="5" height="26"></td>
									</tr>
									</table>
								</td>
							</tr>
							</table>
						</td>
					</tr>

					</table>
                    <?php show_page_buttons();?>
                </td>
				<td width="10px">&nbsp;</td>
				<td valign="top" width="200"><?php show_fast_tools();?></td>
            </tr>
            </table>
		</form>
	</div>
<?php 	include "../layout/bottom.inc.php";?>