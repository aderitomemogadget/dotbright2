<?
	include 'settings.inc.php';
	if($task=='apagar_draft'){$task_button=write_lang('key_apagar_este_draft','Apagar este Draft');$task_form='apaga';}
	elseif($task=='apagar'){$task_button=write_lang('key_apagar_este_registo','Apagar este Registo');$task_form='apaga';}
	elseif($task=='duplicar'){$task_button=write_lang('key_duplicar_este_registo','Duplicar este Registo');$task_form='duplicar';}
	elseif($ID==0){$task_button=write_lang('key_gravar_o_novo_registo','Gravar o Novo Registo');$task_form='insere';}
	elseif($draft_id) {$task_button=write_lang('key_gravar_draft','Gravar Draft');$task_form='altera';}
	else {$task_button=write_lang('key_gravar_alteracoes','Gravar alteraçőes');$task_form='altera';}
	
	$D=getdate();

	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$site_http_url;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	if($ID!=0)
	{		
		if($draft_id)$sql='Select * from drafts WHERE ID='.$draft_id;
		else $sql='Select * from pages WHERE ID='.$ID.' order by Ordem Asc';
		$db_res=$rs->db_open($db_link,$DB_name,$sql);
		if($rs)
		{
			$Datacriacao=$rs->db_data['Data'];
			$ID_FAM=$rs->db_data['ID_ORG'];
			$D=$rs->db_data['Data'];
			$ordem=$rs->db_data['Ordem'];
			$visible=$rs->db_data['Visible'];
			$I1=$rs->db_data['img_ext'];
			$I2=$rs->db_data['img_ext2'];
			$Estado=$rs->db_data['Estado'];
			$sim=$rs->db_data['sim'];
			$usa_css=$rs->db_data['usa_css'];
			$tipo=$rs->db_data['Tipo'];
			$template=$rs->db_data['Template'];
			$defaulthome=$rs->db_data['DefaultHome'];
			$PageEnter=$rs->db_data['PageEnter'];
			$PageExit=$rs->db_data['PageExit'];
			$ID_Grupo=$rs->db_data['ID_Grupo'];
			$pesquisavel=$rs->db_data['Pesquisavel'];
			$destaque=$rs->db_data['Destaque'];
			$destaque_topo=$rs->db_data['Destaque_topo'];
			$destaque_text=$rs->db_data['Destaque_text'];
			$destaque_club=$rs->db_data['Destaque_club'];
			$permanente=$rs->db_data['Permanente'];
			$familia_noticias=$rs->db_data['Familia_noticias'];
			$is_menu=$rs->db_data['IS_MENU'];
			$is_menu2=$rs->db_data['IS_MENU2'];
			$ticker=$rs->db_data['Ticker'];
			$protected=$rs->db_data['Protected'];
			$data1=$rs->db_data['Data1'];
			$data2=$rs->db_data['Data2'];
			if($task_form=='altera')$ID_Familia=$rs->db_data['ID_ORG'];
			$rs2=new DataObj;
			if($draft_id)$sql='Select * from drafts_lang where ID_PAI='.$draft_id.' and lang="'.$admin_lang.'" order by ID Desc';
			elseif($recuperar)$sql='Select * from pages_lang where ID_PAI='.$ID.' and lang2="'.$admin_lang.'" order by ID Desc';
			else $sql='Select * from pages_lang where ID_PAI='.$ID.' and lang="'.$admin_lang.'" order by ID Desc';
			$db_res=$rs2->db_open($db_link,$DB_name,$sql);	
			$C=stripslashes($rs2->db_data['Cabecalho']);
			
			if($task=='duplicar')$C.='(copia)';
			
			$T=limpa_editor_lixo(stripslashes($rs2->db_data['Texto']));
			$T2=limpa_editor_lixo(stripslashes($rs2->db_data['Texto2']));
			$T3=limpa_editor_lixo(stripslashes($rs2->db_data['Texto3']));
			$SEO_title=stripslashes($rs2->db_data['SEO_title']);
			$SEO_description=stripslashes($rs2->db_data['SEO_description']);
			$SEO_keywords=stripslashes($rs2->db_data['SEO_keywords']);
			$video_id = $rs2->db_data['video_id'];
			if($video_id > 0)
			{
				$rs3=new DataObj;
				$sql = "Select video_title from video_menager_link where video_id=$video_id";
				$rs3->db_open($db_link,$DB_name,$sql);
				$video_title = $rs3->db_data['video_title'];
				$rs3->db_close();
			}			
			$rs2->db_close();
		}
		$rs->db_close();
	}
	else
	{
		//inicializa variaveis de data
		$D=str2date(date('Y-m-d'));
		$D2=str2date(date('Y-m-d',strtotime('+24 hours')));
	}
	
	if(!$data1 || $data1 == '0000-00-00 00:00:00') $data1 = date('Y-m-d H:i:s');
	if(!$data2 || $data2 == '0000-00-00 00:00:00') $data2 = date('Y-m-d H:i:s',strtotime('+72 hours'));
	if($task_form=='altera') $ID_ORG = $ID;
	if(!$caminho_bt_editor){$caminho_bt_editor1='../../../editor/';$caminho_bt_editor2='../editor/';}
	else{$caminho_bt_editor1=$caminho_bt_editor2=$caminho_bt_editor;}

	if($converter == 1) $tipo=0;
	if($converter == 2) $tipo = 1;
	if($converter == 3) $tipo = 2;

	include '../layout/header.inc.php';
	include '../layout/body.inc.php';
?><script>
function show_hide()
{
	if(document.getElementById('conf2') && document.getElementById('conf2').style.display=='block')
	{
		if(document.getElementById('conf2')) document.getElementById('conf2').style.display='none';
	} 
	else
	{
		if(document.getElementById('conf2')) document.getElementById('conf2').style.display='block';
	}
}
$(function()
{
	$('#data1').datetimepicker({showOn: "button", buttonImage: "<?=$site_http_url?>external_api/datepicker/images/calendar.png", buttonImageOnly: true, dateFormat: 'yy-mm-dd'});
	$('#data2').datetimepicker({showOn: "button", buttonImage: "<?=$site_http_url?>external_api/datepicker/images/calendar.png", buttonImageOnly: true, dateFormat: 'yy-mm-dd'});
	<? if(($compare_lingua || $ID==0) && ($tipo==2 || $tipo==1)){?>show_hide();<? }elseif($tipo!=2 && $tipo!=1){?>show_hide();<? }?>
});

</script>
<script type="text/javascript" src="../editor/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../editor/ckeditor/ckfinder/ckfinder.js"></script>
<script type="text/javascript">
	if(document.getElementById){document.write('<style type="text/css">\n');document.write('.subm{display: none;}\n');document.write('</style>\n');}
	function SwitchMenu(obj){if(document.getElementById){var el=document.getElementById(obj);var ar=document.getElementById("md").getElementsByTagName("span");if(el.style.display!="block"){for(var i=0;i<ar.length;i++){if(ar[i].className=="subm")ar[i].style.display="none";}el.style.display="block";}else el.style.display="none";}}
	function import2(url){url=url+'&de='+document.form.m_familia2.value+'&x_lang='+document.form.m_lingua.value;window.open(url,'_self');}
	function translate(url){url=url+"&_para="+document.form.m_lingua2.value;window.open(url,'_self');}
	function save_draft(){document.form.draft.value=1; document.form.submit();}
	function show_draft(id){window.open("pagina.php?ID=<?=$ID?>&ID_ORG=<?=$ID_ORG?>&draft_id="+id,'_self');}
	function aprove_draft(){document.form.draft.value=0; document.form.draft_id.value=0; document.form.submit();}
	function ShowSEO(){if(document.getElementById){var el=document.getElementById("SEO");if(el.style.display!="block")el.style.display="block";else el.style.display="none";}}
	function ShowEFECT(){if(document.getElementById){var el=document.getElementById("EFECTS");if(el.style.display!="block")el.style.display="block";else el.style.display="none";}}
</script>
	<div class="center">
		<form name="form" method="post" action="pagina_trata.php?prev=0&ID=<?=$ID?>&ID_ORG=<?=$ID_ORG?>&task_form=<?=$task_form?>&dump=<?=uniqID('')?>" enctype="multipart/form-data">
			<input type="hidden" name="m_lang" value="<?=$admin_lang;?>">
			<input type="hidden" name="slashes" value="'">
            <input type="hidden" name="draft" value="0">
            <input type="hidden" name="draft_id" value="<?=$draft_id?>">
            <input type="hidden" name="tipo" value="<?=$tipo?>">
			<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
            <tr>
                <td valign="top" align="left"><? include '../layout/menus.inc.php'; ?>
					<table class="<? if($tipo==1) echo 'new_quadros2';elseif($tipo==2) echo 'new_quadros3';else echo 'new_quadros';?>" width="880px" border=0 cellspacing=0 cellpadding="4" align="center">
					<tr> 
						<td valign="top">
							<table width="100%" height="30px" border=0 cellspacing=0 cellpadding="5" class="titulo_janela">
							<tr>
								<td width="80"><?=write_lang('key_ativa','Ativa');?>:&nbsp;<input type="checkbox" name="visible" value="1" <? if($visible) echo "checked"; ?>></td>
                                <td width="100"><?=write_lang('key_pesquisavel','Pesquisavel');?>:&nbsp;<input type="checkbox" name="pesquisavel" value="1" <? if($pesquisavel) echo "checked"; ?>></td>
								 <td width="100"><?=write_lang('key_ocultar_titulo','Ocultar Titulo');?>:&nbsp;<input type="checkbox" name="sim" value="1" <? if($sim) echo "checked"; ?>></td>
								<td width="50">CSS:&nbsp;<input type="checkbox" name="usa_css" value="1" <? if($usa_css) echo "checked"; ?>></td>
                                <? if($tipo==0){?><td width="100"><?=write_lang('key_homepage','Homepage');?>:&nbsp;<input type="checkbox" name="defaulthome" value="1" <? if($defaulthome) echo "checked"; ?>></td><? }?>
                                <td width="200"><? 
								if($template>0)echo '<a class=branco href="../templates/pagina.php?ID='.$template.'">';
								echo write_lang('key_template','Template');
								if($template>0) echo '</a>';		
								
								?>:&nbsp;<?
								
								$sql='Select * from templates inner join templates_lang on  templates_lang.ID_PAI = templates.ID where Visivel=1 and Lang="'.$admin_lang.'" order by Cabecalho';
								$db_res=$rs->db_open($db_link,$DB_name,$sql);
							    echo '<select name="template" style="width:60%; height:16px">';
								if($template==0) echo '<option value="0" selected>- default -</option>';
								else echo '<option value="0">- default -</option>';
								if($template==-1) echo '<option value="-1" selected>- that is active -</option>';
								else echo '<option value="-1">- that is active -</option>';
								while(!$rs->EOF())
								{
									echo '<option value="'. $rs->db_data['ID_PAI'] .'"';
									if($template==$rs->db_data['ID_PAI']) echo ' selected ';
									echo '>'.$rs->db_data['Cabecalho'].'</option>';
									$rs->db_next();
								}
								$rs->db_close();
								echo '</select>';
		
								?></td>
                                
                                <td align="right"><? if($ID){?><?=write_lang('key_criado','Criado');?>:&nbsp;<?=$Datacriacao?><? }?></td>
							</tr>
							</table>
                            <table width="100%" border="0" cellspacing="0" cellpadding="5" class="subtitulo_janela">
							<tr>
								<td valign="middle"><h2><?=show_menupath($ID_ORG,'pages','pagina.php',$tipo);?></h2></td>
							</tr>
							</table>
                            <?
								$sql='Select * from pages_lang where ID_PAI='.$ID.' AND Lang <> "'.$admin_lang.'" and Lang <> ""';
								$db_res=$rs->db_open($db_link,$DB_name,$sql);
								if(!$rs->EOF())
								{
									echo '<table width="100%" border=0 cellspacing=0 cellpadding="5" class="subtitulo_janela"><tr><td valign="middle">'.write_lang('key_veja_tambem_esta_pagina_em','Veja também esta página em').': ';
									while(!$rs->EOF())
									{
										if($compare_lingua==$rs->db_data['Lang'])$textob=limpa_editor_lixo(stripslashes($rs->db_data['Texto']));
										echo '[ <a class="preto" href="pagina.php?ID='.$ID.'&ID_ORG='.$ID_ORG.'&c='.uniqid('').'&admin_lingua='.$rs->db_data['Lang'].'">'.$rs->db_data['Lang'].'</a> ]  ';
										$rs->db_next();
									}
									
									$rs->db_close();
									$db_res=$rs->db_open($db_link,$DB_name,$sql);
									echo write_lang('key_ou_compare_esta_pagina_com_a_versao_em','Ou compare esta página com a versão em').':';
									while(!$rs->EOF())
									{
										echo '[ <a class="preto" href="pagina.php?ID='.$ID.'&ID_ORG='.$ID_ORG.'&c='.uniqid('').'&compare_lingua='.$rs->db_data['Lang'].'">'.$rs->db_data['Lang'].'</a> ]  ';
										$rs->db_next();
									}
									
									echo '</td></tr></table>';
								}
								$rs->db_close();
								
							?>
						</td>
					</tr>
                    <tr>
						<td align="left" valign="top" class="preto">
						<table width="100%" cellpadding=0 cellspacing=0 border=0 style="background:url(../images/pagpub_cx_fundo.jpg) repeat-x;">
							<tr>
								<td width="5" height="26"><img src="../images/pagpub_cx_esq.jpg"></td>
								<td width="100" class="new_preto"><?=write_lang('key_ordem','Ordem');?>:&nbsp;<input name="ordem" style="WIDTH: 58px; HEIGHT: 16px" maxlength="5" value="<?=$ordem;?>" class="new_input"></td>
								<td width="5"><img src="../images/pagpub_cx_sep.jpg"></td>
								<td width="100" class="new_preto"> <?=write_lang('key_titulo','Título');?>:&nbsp;</td>
								<td width="<? if($tipo==1)echo 200;else echo 400;?>"><input name="descricao" style="WIDTH: 100%; HEIGHT: 16px" maxlength="255" value="" class="new_input"><script>document.form.descricao.value="<?=str_replace('"','\"',$C);?>";</script></td>
								<?
								if($protected==0)
								{?>
									<td width="5"><img src="../images/pagpub_cx_sep.jpg"></td>
									<td width="70" class="new_preto" align="left"> <?=write_lang('key_familia','Família');?>:</td>
									<td align="right" width="200px"><? if(($tipo==1 || $tipo==2))show_familiax($ID_Familia, 'pages', 'pages_lang',$tipo); else show_familia($ID_Familia, 'pages', 'pages_lang',$tipo);?></td><?
								}
								else
								{
									?><td width="5"><input type="hidden" value="<?=$ID_Familia?>" name="m_familia" /></td><?
								}
								if($tipo==1){
								?>
                                <td class="new_preto" align="right"> Permissőes:</td>
								<td align="right"><? show_lista($ID_Grupo,"grupos_clientes","ID_Grupo");?></td>
                                <? }?>
								<td width="5" height="26"><img src="../images/pagpub_cx_dir.jpg"></td>
							</tr>
							</table>
							<div style="<? if($mostra==1){?>display:none<? }elseif($tipo==2 || $tipo==1){?>display:block<? }?>" id="conf2">
                            <div id="SEO" style="display:none">
                            	<table width="100%" cellpadding="5" cellspacing=0 border=0 bgcolor="#CCCCCC">
                                <tr>
                                    <td width="5" height="26"></td>
                                    <td width="100" class="new_preto" valign="top"><?=write_lang('key_meta_title','Meta Title');?>:</td>
                                    <td class="new_preto"><input name="SEO_title" style="WIDTH: 100%; HEIGHT: 16px" maxlength="255" value="" class="new_input"><script>document.form.SEO_title.value="<?=str_replace('"','\"',$SEO_title);?>";</script><br />
                                    <?=write_lang('key_txt_title','');?>
									</td>
                                </tr>
                                <tr>
                                    <td width="5" height="26"></td>
                                    <td width="100" class="new_preto" valign="top"><?=write_lang('key_meta_description','Meta Description');?>:</td>
                                    <td class="new_preto"><input name="SEO_description" style="WIDTH: 100%; HEIGHT: 16px" maxlength="255" value="" class="new_input"><script>document.form.SEO_description.value="<?=str_replace('"','\"',$SEO_description);?>";</script><br />
                                    <?=write_lang('key_txt_description','');?>
									</td>
                                </tr>
                                <tr>
                                    <td width="5" height="26"></td>
                                    <td width="100" class="new_preto" valign="top"><?=write_lang('key_pagina_keywords','Página Keywords');?>:</td>
                                    <td class="new_preto"><input name="SEO_keywords" style="WIDTH: 100%; HEIGHT: 16px" maxlength="255" value="" class="new_input"><script>document.form.SEO_keywords.value="<?=str_replace('"','\"',$SEO_keywords);?>";</script><br />
                                     <?=write_lang('key_txt_processos','');?></td>
                                </tr>
                                </table>
                            </div>
                            <div id="EFECTS" style="display:none">
                            	<table width="100%" cellpadding="5" cellspacing=0 border=0 bgcolor="#CCCCCC">
                                <tr>
                                    <td width="5" height="26"></td>
                                    <td width="100" class="new_preto" valign="top"><?=write_lang('key_page_enter','Page Enter');?>:</td>
                                    <td class="new_preto">
                                    	<select name="PageEnter">
                                        	<option value="0" <? if(!$PageEnter) echo 'selected="selected"';?>>----</option>
                                            <option value="1" <? if($PageEnter=="1") echo 'selected="selected"';?>>Appear</option>
                                            <option value="2" <? if($PageEnter=="2") echo 'selected="selected"';?>>Blind</option>
                                            <option value="3" <? if($PageEnter=="3") echo 'selected="selected"';?>>Slide</option>
                                        </select>
                                    </td>
                                </tr>
                                </table>
                            </div>
							<? if($tipo==2){?>
                            <table width="100%" cellpadding="10" cellspacing=0 border=0 bgcolor="#CCCCCC">
                            <tr>
                                    <td class="new_preto" valign="top">
									<br /><br />
									<?  if($task !="duplicar" || ($task !="duplicar" && $task_form=="altera") ){?>
									<?=write_lang('key_imagem','Imagem');?>: <?
									if($I1!="" && file_exists("../../files/pages/".$ID."_1.".$I1))
									{	
										if(file_exists("../../files/pages/$ID"."_1.".$I1)){?>&nbsp;&nbsp;<a target="_blank" href="<?="../../files/pages/".$ID."_1.".$I1;?>">View Image</a>&nbsp;&nbsp;<? }?><?
										if($task!="apagar"){?><?=write_lang('key_apagar','Apagar');?> <input type='checkbox' name='Apagar_img1' value='1'> <?=write_lang('key_ou','Ou');?> <?=write_lang('key_alterar','Alterar');?> <input type='file' name='img_peq1'> <input type='hidden' name='tem_foto1' value='1'><? }
									}
									else{ echo write_lang('key_nova','Nova');?>: <input type=file name='img_peq1'> <input type='hidden' name='tem_foto1' value='0'><? }?>
									<br /><br /><? }?>
                                <?=write_lang('key_video','Vídeo');?> <?=write_lang('key_ou','ou');?> <?=write_lang('key_script','Script');?>:
                                <textarea id="texto3" name="texto3" style="width:100%;height:100px"><?=stripslashes($T3);?></textarea><br />
                                <?=write_lang('key_resumo','Resumo');?> (<?=write_lang('key_maximo','Máximo');?> 255 <?=write_lang('key_caracteres','Caracteres');?>):
								<textarea id="texto2" name="texto2" style="width:100%;height:100px"><?=stripslashes($T2);?></textarea><br />
                                <table border="0" cellpadding="5" cellspacing=0 width="100%">
								<? if(($tipo==1 || $tipo==2) && ($familia_noticias==1 || $ID==0)){?>
									<tr>
										<td class="preto" style="border-bottom:#000 1px solid" width="110" height="30" align="left"><b><?=write_lang('key_categoria','Categorias');?>:</b></td>
										<td class="preto" style="border-bottom:#000 1px solid" align="left" width="160"><input type="checkbox" name="familia_noticias" value="1" <? if($familia_noticias) echo "checked"; ?> /></td>
										<td class="preto" style="border-bottom:#000 1px solid" align="left" valign="top"></td>
										<td valign=top  style="border-bottom:#000 1px solid"class="preto" width="200">&nbsp;</td>										
									</tr>
									<? if(($tipo==1) && ($familia_noticias==1 || $ID==0)){?>
									<tr>
										<td class="preto" style="border-bottom:#000 1px solid" width="110" height="30" align="left"><b>Visible in the left menu:</b></td>
										<td class="preto" style="border-bottom:#000 1px solid" align="left" width="160"><input type="checkbox" name="is_menu" value="1" <? if($is_menu) echo "checked"; ?> /></td>
										<td class="preto" style="border-bottom:#000 1px solid" align="left" valign="top"></td>
										<td valign=top  style="border-bottom:#000 1px solid"class="preto" width="200">&nbsp;</td>										
									</tr>
									<tr>
										<td class="preto" style="border-bottom:#000 1px solid" width="110" height="30" align="left"><b>Only Visible Homepage and Footer:</b></td>
										<td class="preto" style="border-bottom:#000 1px solid" align="left" width="160"><input type="checkbox" name="is_menu2" value="1" <? if($is_menu2) echo "checked"; ?> /></td>
										<td class="preto" style="border-bottom:#000 1px solid" align="left" valign="top"></td>
										<td valign=top  style="border-bottom:#000 1px solid"class="preto" width="200">&nbsp;</td>										
									</tr>
									<? }?>
								<? }
								
								if(!$familia_noticias)
								{ ?>
									<tr><td valign=top colspan="4">&nbsp;</td></tr>
									<tr>
										<td class="preto" width="110" align="left" valign="top"><?=write_lang('key_valida','Válida');?> <?=write_lang('key_de','de');?>:</td>
										<td class="preto" align="left" valign="top"  width="160"><input name="data1" id="data1" type="text" value="<?=$data1;?>" /></td>
										<? if($tipo==1 || $tipo==2){?><td class="preto" align="left" valign="top"></td><? }?>										
										<? if($tipo==1 || $tipo==2){?><td class="preto" align="left" valign="top"></td><? }?>
									</tr>									
									<tr>
										<td class="preto" align="left" valign="top"><?=write_lang('key_ate','até');?>:</td>
										<td class="preto" align="left" valign="top"><input name="data2" id="data2" type="text" value="<?=$data2;?>" /></td>
										<? if($tipo==2){?><td colspan="2" class="preto" align="right" valign="top">&nbsp;</td>
                                        <? }else{?><td colspan="2" class="preto" align="right" valign="top"></td><? }?>
									</tr> 
									<tr>
										<td class="preto" width="110" align="left" valign="top"><?=write_lang('key_ou','ou');?> <?=write_lang('key_permanente','Permanente');?></td>
										<td class="preto" align="left" valign="top"><input type="checkbox" name="permanente" value="1" <? if($permanente) echo "checked"; ?> /></td>      
										<? if($tipo==2){?><td colspan="2" valign=top align="right" class="preto"><input type="checkbox" name="destaque" id="destaque" onclick="document.getElementById('destaque_text').checked=0" value="1" <? if($destaque) echo "checked"; ?> />&nbsp;<?=write_lang('key_em','Em');?> <?=write_lang('key_destaque','Destaque');?></td><? }else{?><td>&nbsp;</td><? }?>
									</tr>
									<tr>
										<td class="preto" align="left" valign="top"></td>
										<td class="preto" align="left" valign="top"></td>    
										<td class="preto" align="left" valign="top"></td>    
										<td class="preto" align="left" valign="top"></td>																				
									</tr>							
									<tr><td valign="top" colspan="4">&nbsp;</td></tr>					
									<? }?>										
									</table>
                                    </td>
                                </tr>
                                </table>
								<? 
							}?>
						</div>
						</td>
					</tr>
					<tr>
						<td class="preto" align="left" valign="top"><textarea id="texto" name="texto"><?=stripslashes($T);?></textarea><? show_editor("oEdit1","texto",880,450)?></td>
					</tr>
                    <?
					if($compare_lingua)
					{?>
                    <tr><td class="preto" align="left" valign="top"><textarea id="textob" name="textob"><?=stripslashes($textob);?></textarea><? show_editor("oEdit1b","textob",880,450)?></td></tr><?
					}
					?>
					</table>
                    <? 
						show_page_buttons($protected);
						if(!$draft_id && !$converter && $ID)
						{						
							
							$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);							
							$porpagina = 20;							
							
							$sql="Select pages.*,Cabecalho from pages inner join pages_lang on pages.ID=pages_lang.ID_PAI where Lang='$admin_lang' and ID_ORG=$ID Order by ordem";							
							$rs->db_open($db_link,$DB_name,$sql);
							$total = $rs->db_rows;
	
							$n_pags = ceil($total / $porpagina); // nº de paginas
							if(!$pagina) $pagina = "1";
							
							$inicio = $pagina - 1;
							$inicio = $inicio * $porpagina;							
							
							$sql2 = $sql." Limit $inicio,$porpagina";
							$rs->db_open($db_link,$DB_name,$sql2);
							
							if($n_pags > 1)
							{       
								$return = '<table class="new_quadros" border=0 cellspacing=2 cellpadding=1><tr>';
								if($pagina > 6)
								{
									$pgn = 'onclick=window.open("pagina.php?ID='.$_GET['ID'].'&ID_ORG='.$_GET['ID_ORG'].'&pagina=1","_self")'; 
									$return .= '<td width="2px">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer;padding-top:2px;border:#000 0px solid" >1... </td>';
								}
								for($pi = ($pagina - 6); $pi < ($pagina + 7); $pi++)
								{
									$pgn = 'onclick=window.open("pagina.php?ID='.$_GET['ID'].'&ID_ORG='.$_GET['ID_ORG'].'&pagina='.$pi.'","_self")'; 
									$return .= ($pi >= 1 && $pi <= $n_pags) ? $pagina == $pi ? '<td width="2px">&nbsp;</td><td width="20" height="20" bgcolor="#84c6fe" align="center" >'.sprintf(($pi)).'</td>' : '<td width="2px">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer">'.sprintf($pi).'</td>' :'';
								}
								if($n_pags!=$pagina)
								{
									$pgn = 'onclick=window.open("pagina.php?ID='.$_GET['ID'].'&ID_ORG='.$_GET['ID_ORG'].'&pagina='.$n_pags.'","_self")'; 
									$return .= '<td width="2px" style="">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer;padding-top:2px;border:#000 0px solid" > ...'.sprintf($n_pags).'</td>';
								}
								$return .= '</tr></table>';
							}
							
							$count2=0;
							$t=0;
							
							/*
							if($familia_noticias){?>
							<table width="100%" border=0 cellspacing=0 cellpadding=0 class="p1">
							<tr> 
							<td width="5px"></td>
							<td class="branco" style="font-weight:bold;font-size:15px;" align="right"><input class="botao" type="button" style="width:160px;height:20px" value="<?=write_lang('key_nova_noticia','Nova Notícia');?>" onClick="javascript:window.open('pagina.php?ID=0&ID_ORG=<?=$ID?>&tipo=<?=$tipo?>&Dump=<?=uniqid("");?>','_self')"></td>                            
							<td width="5px"></td>
							</tr>
							</table>
							<? 
							}
							*/
							?>							
							<? if($familia_noticias || !$rs->EOF()){?>
                            <table class="new_quadros" width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr> 
							<td width="40" height="50" bgcolor="<?=$cor2;?>" align="right">&nbsp;</td>
							<td valign="top" bgcolor="<?=$cor2;?>" width="8">&nbsp;</td>
							<td valign="middle" bgcolor="<?=$cor2;?>" style="font-size:15px"><b><?=write_lang('key_noticias','Notícias');?></b></td>
							<? if(($tipo==1 || $tipo==2)){?><td valign="middle" align="center" bgcolor="<?=$cor2;?>" style="font-size:15px"><b><?=write_lang('key_comentarios','Comentários');?></b></td><? }?>
							<? if(($tipo==1 || $tipo==2)){?><td valign="middle" align="center"  bgcolor="<?=$cor2;?>" width="100"><? echo CountRcords($codigo)?></td><? }?>
							<td valign="middle" bgcolor="<?=$cor2;?>" align="right" width="24"></td>
							<td valign="middle" bgcolor="<?=$cor2;?>" align="right" width="24"></td>
							<td bgcolor="<?=$cor2;?>" width="10">&nbsp;</td>
							</tr>					
							<?
							
								while(!$rs->EOF())
								{
									$t++;
									$codigo=$rs->db_data["ID"];
									$ID_ORG2=$rs->db_data["ID_ORG"];
									$name=$rs->db_data["Cabecalho"];
									$tipo=$rs->db_data["Tipo"];
									$Visible=$rs->db_data["Visible"];
									$fam_noticias=$rs->db_data["Familia_noticias"];
									
									if($Visible==1)
									{									
										if($count2 % 2 != 0)	$cor2="#def2fa";
										else					$cor2="#F6F6F6";
									}
									else
									{
										if($count2 % 2 != 0)	$cor2="#E0E0E0";
										else					$cor2="#c0c0c0";
									}
									?>
									<tr> 
										<td width="30" bgcolor="<?=$cor2;?>" align="center"><a class="preto" href="pagina.php?ID=<?=$rs->db_data["ID"]?>"><?
										if($tipo==0) echo '<img border=0 src="../images/page.png" />';
										if($tipo==1) echo '<img border=0 src="../images/page_priv.png" />';
										if($tipo==2) echo '<img border=0 src="../images/page_news.png" />';
										?></a></td>
										<td valign="top" bgcolor="<?=$cor2;?>" width="8">&nbsp;</td>
										<? if(($tipo==1 || $tipo==2)){?>
										<td valign="middle" bgcolor="<?=$cor2;?>"><a class="preto" href="pagina.php?ID=<?=$rs->db_data["ID"]?>"><b><?=$name;?></b></a></td>
										<? }else{?>
										<td valign="middle" bgcolor="<?=$cor2;?>"><a class="preto" href="pagina.php?ID=<?=$rs->db_data["ID"]?>"><?=$name;?></a></td>
										<? }?>
										<? if(($tipo==1 || $tipo==2)) {?><td valign="middle"  align="center"  bgcolor="<?=$cor2;?>" width="100"><? if(!$fam_noticias) echo CountRcords_Comments($codigo)?></td><? }?>
										<? if(($tipo==1 || $tipo==2)) {?><td valign="middle" align="center" bgcolor="<?=$cor2;?>" width="100"><? if($fam_noticias) echo CountRcords($codigo)?></td><? }?>
										<td valign="middle" bgcolor="<?=$cor2;?>" align="right" width="24"><a class="italico" href="pagina.php?ID=<?=$rs->db_data["ID"]?>&ID_ORG=<?=$ID_ORG2;?>&task=duplicar&Dump=<? echo uniqid("");?>"><img title="Duplicate page" src="duplicate.png" border=0></a></td>
										<td valign="middle" bgcolor="<?=$cor2;?>" align="right" width="24"><a class="italico" href="pagina.php?ID=<?=$rs->db_data["ID"]?>&task=apagar"><img alt="" src="del2.png" border=0 align="absmiddle" title="Delete page"></a></td>
										<td bgcolor="<?=$cor2;?>" width="10">&nbsp;</td>
									</tr>
									<?
									$rs->db_next();	
									$count2++;
								}
								$rs->db_close(1);?>
								</table><?
							}	
								echo $return;
                         }?>
                </td>
				<td width="10px">&nbsp;</td>
				<td valign="top"><? show_fast_tools($tipo);?><? show_drafts()?><? show_drop_tools()?><? show_conversoes()?><? show_seo()?><? show_efeitos()?></td>
            </tr>
            </table>
		</form>
 	</div>
<? 	include '../layout/bottom.inc.php';?>