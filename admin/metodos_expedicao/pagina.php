<?php
	include "settings.inc.php";
	if($task=="apagar"){$task_button = write_lang('key_apagar_este_registo','Apagar este Registo');$task_form="apaga";}
	elseif($task=="duplicar"){$task_button = write_lang('key_duplicar_este_registo','Duplicar este Registo');$task_form="duplicar";}
	elseif($id_metodo==0){$task_button = write_lang('key_gravar_novo_registo','Gravar Novo Registo');$task_form="insere";}
	else {$task_button = write_lang('key_gravar_alteracoes','Gravar alterações');$task_form="altera";}
	$D=getdate();

	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;$rs2 = new DataObj;$rs_paises = new DataObj;


	$fields=db_list_fields($DB_name,'metodos_expedicao',$db_link);
	$columns=db_num_fields($fields);
	for ($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
	if(!in_array('m_pais',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE metodos_expedicao ADD m_pais INT(11) NOT NULL');
	if(!in_array('m_tipo',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE metodos_expedicao ADD m_tipo tinyint(1) NOT NULL');
	if(!in_array('Ordem',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE metodos_expedicao ADD Ordem int(11) NOT NULL');
	if(!in_array('Gratis',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE metodos_expedicao ADD Gratis tinyint(1) NOT NULL');
	if(!in_array('Loja',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE metodos_expedicao ADD Loja tinyint(1) NOT NULL');
	if(!in_array('Abreviatura',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE metodos_expedicao ADD Abreviatura varchar(150) NOT NULL');

	$fields=db_list_fields($DB_name,'metodos_expedicao_lang',$db_link);
	$columns=db_num_fields($fields);
	for ($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
	if(!in_array('Cabecalho2',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE metodos_expedicao_lang ADD Cabecalho2 varchar(255) NOT NULL');
	if(!in_array('Texto1',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE metodos_expedicao_lang ADD Texto1 Text NOT NULL');


	if($id_metodo!=0)
	{

		// $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		// $rs = new DataObj;$rs2 = new DataObj;
		$sql = 'SELECT * FROM metodos_expedicao WHERE ID='.$id_metodo;
		$rs->db_open($db_link,$DB_name,$sql);
		if($rs)
		{
			$id_metodo 					= $rs->db_data["ID"];
			$Visivel					= $rs->db_data['Visivel'];
			$img_ext					= $rs->db_data['img_ext'];
			$tipo 						= $rs->db_data['Tipo'];
			$m_pais						= $rs->db_data['m_pais'];
			$m_tipo						= $rs->db_data['m_tipo'];
			$gratis						= $rs->db_data['Gratis'];
			$loja 						= $rs->db_data['Loja'];
			$Abreviatura				= $rs->db_data['Abreviatura'];

			$sql2='SELECT Cabecalho, Cabecalho2 , Texto1 FROM metodos_expedicao_lang WHERE ID_PAI="'.$id_metodo.'" and Lang="'.$admin_lang.'"';
			$rs2->db_open($db_link,$DB_name,$sql2);
			if(!$rs2->EOF())
			{
				 $descricao = $rs2->db_data['Cabecalho'];
				 $Cabecalho2 = $rs2->db_data['Cabecalho2'];
				 $Texto1 = stripslashes($rs2->db_data['Texto1']);
			}
			$rs2->db_close();
		}
		$rs->db_close();
	}
	include "../layout/header.inc.php";
	include "../layout/body.inc.php";
?>
<script>

$(function(){ $('.result').hide();$('.result2').hide();});


</script>
	<div class="center">
		<form name="form" method="post" action="pagina_trata.php?id_metodo=<?php echo $id_metodo?>&task_form=<?php echo $task_form?>&dump=<?php echo uniqID("")?>" enctype="multipart/form-data">
			<input type="hidden" name="m_lang" value="<?php echo $admin_lang;?>" />
			<input type="hidden" name="id_metodo" value="<?php echo $id_metodo;?>" />
			<input type="hidden" name="slashes" value="'" />
			<table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td valign="top" align="left"><? include "../layout/menus.inc.php"; ?>
	                <div class="panel panel-inverse overflow-hidden m-b-12">
				        <div class="panel-heading">
				            <h4 style="color:#fff">MÉTODOS DE ENVIO</h4>
				        </div>
				    </div>
					<table style="background:#FFF" width="100%" cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td valign="top">
						<table width="100%" border=0 cellspacing=0 cellpadding="5" class="subtitulo_janela">
						<tr>
							<td valign="middle"><a href="list.php" class="branco"> &#60; Voltar para a lista de Métodos de Expedição</a></td>
						</tr>
						</table>
					</tr>
                    <tr>
						<td align="left" valign="top" class="preto">
							<table width="100%" cellpadding=0 cellspacing=0 border=0 style="background:#FFF">
                            <tr>
								<td width="5" height="26">&nbsp;</td>
								<td width="200" class="new_preto">&nbsp;&nbsp;Visível:&nbsp;</td>
								<td style="font-weight:bold;text-align: left;"><input type="checkbox" name="Visivel" id="Visivel" <? if($Visivel==1) echo 'checked="checked"';?> value="1" /></td>
								<td width="5" height="26">&nbsp;</td>
							</tr>
							</table>
							<table width="100%" cellpadding=0 cellspacing=0 border=0 style="background:#FFF">
                            <tr>
								<td width="5" height="26">&nbsp;</td>
								<td width="200" class="new_preto">&nbsp;&nbsp;Gratuito:&nbsp;</td>
								<td style="font-weight:bold;text-align: left;"><input type="checkbox" name="Gratis" id="Gratis" <? if($gratis==1) echo 'checked="checked"';?> value="1" /></td>
								<td width="5" height="26">&nbsp;</td>
							</tr>
							</table>
							<table width="100%" cellpadding=0 cellspacing=0 border=0 style="background:#FFF">
                            <tr>
								<td width="5" height="26">&nbsp;</td>
								<td width="200" class="new_preto">&nbsp;&nbsp;Ponto de recolha (LOJA):&nbsp;</td>
								<td style="font-weight:bold;text-align: left;"><input type="checkbox" name="Loja" id="Loja" <? if($loja==1) echo 'checked="checked"';?> value="1" /></td>
								<td width="5" height="26">&nbsp;</td>
							</tr>
							</table>
							<?php /* <table width="100%" cellpadding=0 cellspacing=0 border=0 style="background:#FFF">
	                        <tr>
								<td width="5" height="26">&nbsp;</td>
								<td width="200" class="new_preto">&nbsp;&nbsp;Nome Extra:&nbsp;</td>
								<td class="new_preto"><input type="text" name="Cabecalho2" id="Cabecalho2" style="WIDTH: 200px"  value="<?php echo $Cabecalho2;?>" class="form-control" /></td>
								<td width="5" height="26">&nbsp;</td>
							</tr>
							</table>  */?>
							<table width="100%" cellpadding=0 cellspacing=0 border=0 style="background:#FFF">
	                        <tr>
								<td width="5" height="26">&nbsp;</td>
								<td width="200" class="new_preto">&nbsp;&nbsp;Nome:&nbsp;</td>
								<td class="new_preto"><input type="text" name="nome_metodo" id="nome_metodo"  value="<?php echo $descricao;?>" class="form-control" /></td>
								<td width="5" height="26">&nbsp;</td>
							</tr>
							</table><br>
							<table width="100%" cellpadding=0 cellspacing=0 border=0 style="background:#FFF">
	                        <tr>
								<td width="5" height="26">&nbsp;</td>
								<td width="200" class="new_preto">&nbsp;&nbsp;Abreviatura:&nbsp;</td>
								<td class="new_preto"><input type="text" name="Abreviatura" id="Abreviatura"  value="<?php echo $Abreviatura;?>" class="form-control" /></td>
								<td width="5" height="26">&nbsp;</td>
							</tr>
							</table><br>

							<table width="100%" cellpadding=0 cellspacing=0 border=0 style="background:#FFF">
	                        <tr>
								<td width="5" height="26">&nbsp;</td>
								<td width="200" class="new_preto">&nbsp;&nbsp;Descrição:&nbsp;</td>
								<td class="new_preto"><textarea  name="Texto1" id="Texto1" style="WIDTH: 200px; HEIGHT:50px" class="form-control"><?php echo $Texto1;?></textarea></td>
								<td width="5" height="26">&nbsp;</td>
							</tr>
							</table><br>
							<table width="100%" cellpadding=0 cellspacing=0 border=0 style="background:#FFF">
	                        <tr>
								<td width="5" height="26">&nbsp;</td>
								<td width="200" class="new_preto">&nbsp;&nbsp;Tipo:&nbsp;</td>
								<td class="new_preto">
								<?php
									?>
										<select name="tipo_metodo" id="tipo_metodo">
											<option value="1" <?php if($tipo==1) echo ' selected="selected" ';?>>CARRINHO PRODUTOS</option>
											<?php /* <option value="2" <?php if($tipo==2) echo ' selected="selected" ';?>>CARRINHO REPARAÇÕES</option> */?>
										</select>
									 <?php
									?>
								</td>
								<td width="5" height="26">&nbsp;</td>
							</tr>
							</table><br>
							<table width="100%" cellpadding=0 cellspacing=0 border=0 style="background:#FFF">
	                        <tr>
								<td width="5" height="26"></td>
								<td width="200" class="new_preto">&nbsp;&nbsp;Imagem:&nbsp;</td>
								<td class="new_preto"><?
                                        if($img_ext!="")
                                        {?><div class="imagens_400" style="height:100px;"><? if(file_exists("../../files/metodos_expedicao/$id_metodo"."_1.".$img_ext)){?><img alt="" src="<?="../../files/metodos_expedicao/$id_metodo"."_1.".$img_ext;?>"><? }?></div><?
                                            if($task!="apagar"){?><?php echo write_lang('key_apagar','Apagar');?> <input type='checkbox' name='Apagar_img1' value='1'> <?php echo write_lang('key_ou','ou');?> <?php echo write_lang('key_alterar','alterar');?> <input type='file' name='img_peq1'> <input type='hidden' name='tem_foto1' value='1'><? }
                                        }
                                        else{?><?php echo write_lang('key_nova','Nova');?>: <input type=file name='img_peq1'> <input type='hidden' name='tem_foto1' value='0'><? }?></td>
								<td width="5" height="26"></td>
							</tr>
							</table><br>
							<table width="100%" cellpadding=0 cellspacing=0 border=0 style="background:#FFF">
	                        <tr>
								<td width="5" height="26"></td>
								<td width="200" class="new_preto">Disponivel Países</td>
								<td class="new_preto">
                              <select class="inputregisto HvMd" style="width:195px" name="m_pais" id="m_pais">
							 <?php /*  <option value="0">Todos</option> */?>
							<?php
								$sql = 'SELECT c.zone_id,czl.zone_name FROM country_zones as c inner join country_zones_lang as czl on c.zone_id=czl.zone_id where czl.zone_lang="'.$admin_lang.'" AND c.zone_visible=1 group by c.zone_id  order by czl.zone_name ASC';
								$rs_paises->db_open($db_link, $DB_name, $sql);
								while(!$rs_paises->EOF())
								{
									$zone_name = $rs_paises->db_data['zone_name'];
									$zone_id = $rs_paises->db_data['zone_id'];
									echo '<option value="'.$zone_id.'" ';

									if($m_pais==$zone_id){	echo 'selected="selected"';	}
									echo'>'.$zone_name.'</option>';

									$rs_paises->db_next();
								}
								$rs_paises->db_close();
							?>
						</select>
									</td>
								<td width="5" height="26"></td>
							</tr>
							</table>
							<br>
						</td>
					</tr>

					</table>
                    <?php show_page_buttons();?>
                </td>
				<td width="10px">&nbsp;</td>
				<td valign="top" width="200"><? show_fast_tools();?></td>
            </tr>
            </table>
		</form>
	</div>
<?php 	include "../layout/bottom.inc.php";?>