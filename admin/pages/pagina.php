<?php
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
			$script=$rs->db_data['script'];
			$menus=$rs->db_data['menus'];
			$interna=$rs->db_data['Interna'];
			$Datacriacao=$rs->db_data['Data'];
			$ID_FAM=$rs->db_data['ID_ORG'];
			$D=$rs->db_data['Data'];
			$ordem=$rs->db_data['Ordem'];
			$visible=$rs->db_data['Visible'];
			$I1=$rs->db_data['img_ext'];
			$I2=$rs->db_data['img_ext2'];
			$I3=$rs->db_data['img_ext3'];
			$I4=$rs->db_data['img_ext4'];
			$Img_estilo=$rs->db_data['Img_estilo'];
			$IDOC=$rs->db_data['img_extdoc'];
			$Estado=$rs->db_data['Estado'];
			$sim=$rs->db_data['sim'];
			$usa_css=$rs->db_data['usa_css'];
			$tipo=$rs->db_data['Tipo'];
			$template=$rs->db_data['Template'];
			$defaulthome=$rs->db_data['DefaultHome'];
			$DefaultHome2=$rs->db_data['DefaultHome2'];
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
			$bootstrap=$rs->db_data['bootstrap'];
			$bootstrap_p=$rs->db_data['bootstrap_p'];
			if($task_form=='altera' || $task_form=='duplicar') $ID_Familia = $rs->db_data['ID_ORG'];
			$rs2=new DataObj;
			if($draft_id) $sql='Select * from drafts_lang where ID_PAI='.$draft_id.' and lang="'.$admin_lang.'" order by ID Desc';
			elseif($recuperar)$sql='Select * from pages_lang where ID_PAI='.$ID.' and lang2="'.$admin_lang.'" order by ID Desc';
			else $sql='Select * from pages_lang where ID_PAI='.$ID.' and lang="'.$admin_lang.'" order by ID Desc';
			$db_res=$rs2->db_open($db_link,$DB_name,$sql);
			$C=stripslashes($rs2->db_data['Cabecalho']);
			$C2=stripslashes($rs2->db_data['Cabecalho2']);

			if($task=='duplicar') $C .= '(copia)';

			$T=limpa_editor_lixo(stripslashes($rs2->db_data['Texto']));
			$T2=limpa_editor_lixo(stripslashes($rs2->db_data['Texto2']));
			$T3=limpa_editor_lixo(stripslashes($rs2->db_data['Texto3']));

			$T10=limpa_editor_lixo(stripslashes($rs2->db_data['Texto10']));
			$T11=limpa_editor_lixo(stripslashes($rs2->db_data['Texto11']));
			$T12=limpa_editor_lixo(stripslashes($rs2->db_data['Texto12']));
			$T13=limpa_editor_lixo(stripslashes($rs2->db_data['Texto13']));
			$T14=limpa_editor_lixo(stripslashes($rs2->db_data['Texto14']));
			$T15=limpa_editor_lixo(stripslashes($rs2->db_data['Texto15']));
			$T16=limpa_editor_lixo(stripslashes($rs2->db_data['Texto16']));
			$T17=limpa_editor_lixo(stripslashes($rs2->db_data['Texto17']));
			$T18=limpa_editor_lixo(stripslashes($rs2->db_data['Texto18']));
			$T19=limpa_editor_lixo(stripslashes($rs2->db_data['Texto19']));
			$T20=limpa_editor_lixo(stripslashes($rs2->db_data['Texto20']));

			$SEO_title=stripslashes($rs2->db_data['SEO_title']);
			$SEO_description=stripslashes($rs2->db_data['SEO_description']);
			$SEO_keywords=stripslashes($rs2->db_data['SEO_keywords']);

			$SEO_img = $rs2->db_data["SEO_img"];

			$SEO_title_preview = $SEO_title != '' ? $SEO_title : $C;
			$SEO_description_preview = $SEO_description != '' ? $SEO_description : $C;

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

	if($translate_to)
	{
		$lang1='pt';
		$lang2b = $translate_to;
		if($lang2=='ho')$lang2b='nl';

		$arr_query = array(
		'key' => 'trnsl.1.1.20160826T172445Z.ad542005a80e547d.9ff6f4f7d599e4951974daafc13d5dd10bd89613',
		'text' => ($T),
		'format' => 'html',
		'lang' => "$lang1-$lang2b");
		$new_str = file_get_contents('https://translate.yandex.net/api/v1.5/tr.json/translate?'.http_build_query($arr_query));
		$obj = json_decode($new_str);
		$array_obj = $obj->{'text'};
		$T=$array_obj[0];


		$arr_query = array(
		'key' => 'trnsl.1.1.20160826T172445Z.ad542005a80e547d.9ff6f4f7d599e4951974daafc13d5dd10bd89613',
		'text' => ($C),
		'format' => 'html',
		'lang' => "$lang1-$lang2b");
		$new_str = file_get_contents('https://translate.yandex.net/api/v1.5/tr.json/translate?'.http_build_query($arr_query));
		$obj = json_decode($new_str);
		$array_obj = $obj->{'text'};
		$C=$array_obj[0];
	}


	$tipo = (isset($tipo) ? $tipo :(isset($_GET['tipo']) ? $_GET['tipo'] :0));

	if(!$data1 || $data1 == '0000-00-00 00:00:00') $data1 = date('Y-m-d H:i:s');
	if(!$data2 || $data2 == '0000-00-00 00:00:00') $data2 = date('Y-m-d H:i:s',strtotime('+72 hours'));
	if($task_form=='altera' || $task_form=='duplicar') $ID_ORG = $ID;
	if(!$caminho_bt_editor){$caminho_bt_editor1='../../../'.EDITOR.'/';$caminho_bt_editor2='../'.EDITOR.'/';}
	else{$caminho_bt_editor1=$caminho_bt_editor2=$caminho_bt_editor;}

	if($converter == 1) $tipo = 0;
	if($converter == 2) $tipo = 1;
	if($converter == 3) $tipo = 2;

	include '../layout/header.inc.php';
	include '../layout/body.inc.php';

?><script>
function show_hide()
{
	/* if(document.getElementById('conf2') && document.getElementById('conf2').style.display=='block')
	{
		if(document.getElementById('conf2')) document.getElementById('conf2').style.display='none';
	}
	else
	{
		if(document.getElementById('conf2')) document.getElementById('conf2').style.display='block';
	} */
}
$(function()
{
	$('#data1').datetimepicker({showOn:"button", buttonImage:"<?php echo $site_http_url?>external_api/datepicker/images/calendar.png", buttonImageOnly:true, dateFormat:'yy-mm-dd'});
	$('#data2').datetimepicker({showOn:"button", buttonImage:"<?php echo $site_http_url?>external_api/datepicker/images/calendar.png", buttonImageOnly:true, dateFormat:'yy-mm-dd'});
	<? if(($compare_lingua || $ID==0) && ($tipo==2 || $tipo==1)){?>show_hide();<?php }elseif($tipo!=2 && $tipo!=1){?>show_hide();<?php }?>
});

</script>
<?php if(!$script){?><script type="text/javascript" src="../editor/ckeditor_4_5_1/ckeditor.js"></script>
<script type="text/javascript" src="../editor/ckeditor_4_5_1/ckfinder/ckfinder.js"></script><?php }?>
<link rel="stylesheet" type="text/css" href="sa.css?dp=<?php echo dumpacache?>" />
<link rel="stylesheet" type="text/css" href="chosen.css?dp=<?php echo dumpacache?>" />

<script type="text/javascript">
	if(document.getElementById){document.write('<style type="text/css">\n');document.write('.subm{display:none;}\n');document.write('</style>\n');}
	function SwitchMenu(obj){if(document.getElementById){var el=document.getElementById(obj);var ar=document.getElementById("md").getElementsByTagName("span");if(el.style.display!="block"){for(var i=0;i<ar.length;i++){if(ar[i].className=="subm")ar[i].style.display="none";}el.style.display="block";}else el.style.display="none";}}
	function import2(url){url=url+'&de='+document.form.m_familia2.value+'&x_lang='+document.form.m_lingua.value;window.open(url,'_self');}
	function translate(url){url=url+"&_para="+document.form.m_lingua2.value;window.open(url,'_self');}
	function save_draft(){document.form.draft.value=1;document.form.submit();}
	function show_draft(id){window.open("pagina.php?ID=<?php echo $ID?>&ID_ORG=<?php echo $ID_ORG?>&draft_id="+id,'_self');}
	function aprove_draft(){document.form.draft.value=0;document.form.draft_id.value=0;document.form.submit();}
	function ShowSEO(){if(document.getElementById){var el=document.getElementById("SEO");if(el.style.display!="block")el.style.display="block";else el.style.display="none";}}
	function ShowEFECT(){if(document.getElementById){var el=document.getElementById("EFECTS");if(el.style.display!="block")el.style.display="block";else el.style.display="none";}}
</script>
	<div class="center">
		<form name="form" method="post" action="pagina_trata.php?prev=0&ID=<?php echo $ID?>&ID_ORG=<?php echo $ID_ORG?>&task_form=<?php echo $task_form?>&dump=<?php echo uniqID('')?>" enctype="multipart/form-data">
			<input type="hidden" name="m_lang" value="<?php echo $admin_lang;?>">
			<input type="hidden" name="slashes" value="'">
            <input type="hidden" name="draft" value="0">
            <input type="hidden" name="draft_id" value="<?php echo $draft_id?>">
            <input type="hidden" name="tipo" value="<?php echo $tipo?>">
			<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
            <tr>
                <td align="left" style="vertical-align:top">
                	<div class="panel panel-inverse overflow-hidden m-b-12">
				        <div class="panel-heading">
				            <h4 style="color:#fff">PÁGINAS</h4>
				            <?php if($ID){?><div style="float:right;color:#555"><?php echo write_lang('key_criado','Criado');?>:&nbsp;<?php echo $Datacriacao?></div><?php }?>
				        </div>
				    </div>

					<table class="<?php if($tipo==1) echo 'new_quadros2';elseif($tipo==2) echo 'new_quadros3';else echo 'new_quadros';?>" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
					<tr>
						<td>
							<table width="100%" height="30px" border=0 cellspacing=0 cellpadding="5" class="titulo_janela">
							<tr>
								<td width="80"><?php echo write_lang('key_ativa','Ativa');?>:&nbsp;<input type="checkbox" name="visible" value="1" <? if($visible) echo "checked";?>></td>
                                <td width="100"><?php echo write_lang('key_pesquisavel','Pesquisavel');?>:&nbsp;<input type="checkbox" name="pesquisavel" value="1" <? if($pesquisavel) echo "checked";?>></td>
								<td width="150"><?php echo write_lang('key_ocultar_titulo','Ocultar Titulo');?>:&nbsp;<input type="checkbox" name="sim" value="1" <? if($sim) echo "checked";?>></td>
								<!-- <td width="50">CSS:&nbsp;<input type="checkbox" name="usa_css" value="1" <? if($usa_css) echo "checked";?>></td> -->
                                <?php if($tipo==0){?><td width="200">
								<?php echo write_lang('key_homepage','Homepage');?>:&nbsp;<input type="checkbox" name="defaulthome" value="1" <? if($defaulthome) echo "checked";?>>
								<?php if($GLOBALS['tem_loja']==1){echo write_lang('key_homepage','Homepage');?>Loja:&nbsp;<input type="checkbox" name="DefaultHome2" value="1" <? if($DefaultHome2) echo "checked";?>><?php }?></td><?php }?>
                                <td width="50"><?
								if($template>0)echo '<a class=branco href="../templates/pagina.php?ID='.$template.'">';
								echo write_lang('key_template','Template');
								if($template>0) echo '</a>';

								?>:</td><td><?

								// $sql = 'SELECT ID_PAI, Cabecalho FROM templates INNER JOIN templates_lang ON templates_lang.ID_PAI = templates.ID WHERE Visivel=1 AND Lang="'.$admin_lang.'" ORDER BY Cabecalho';
								$sql = 'SELECT ID_PAI, Cabecalho FROM templates INNER JOIN templates_lang ON templates_lang.ID_PAI = templates.ID WHERE Visivel=1 AND Lang="pt" ORDER BY Cabecalho';
								$rs->db_open($db_link,$DB_name,$sql);
							    echo '<select name="template" class="form-control">';
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
							</tr>
							</table>
                            <table width="100%" border="0" cellspacing="0" cellpadding="5" class="subtitulo_janela">
							<tr>
								<td valign="middle"><h2><?php echo show_menupath($ID_ORG, 'pages','pagina.php');?></h2></td>
							</tr>
							</table>
                            <?
								$sql='Select * from pages_lang where ID_PAI='.$ID.' AND Lang <> "'.$admin_lang.'" and Lang <> ""';
								$db_res=$rs->db_open($db_link,$DB_name,$sql);
								if(!$rs->EOF())
								{
									echo '<table width="100%" border=0 cellspacing=0 cellpadding="5" class="subtitulo_janela"><tr><td valign="middle">'.write_lang('key_veja_tambem_esta_pagina_em','Veja também esta página em').':';
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

									echo '<input class="btn btn-primary" style="width:160px" value="Traduzir para '.$admin_lang.'" onclick="javascript:window.open(\'pagina.php?ID='.$ID.'&ID_ORG='.$ID_ORG.'&translate_to='.$admin_lang.'&d='.uniqid().'\',\'_self\')" type="button">';

									echo '</td></tr></table>';
								}
								$rs->db_close();

							?>
						</td>
					</tr>
                    <tr>
						<td align="left" class="preto">

							<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#FFF">
							<tr>
								<td>
									<table width="100%" cellpadding="5" cellspacing="0" border="0" style="background:#FFF">
									<tr>
									<td width="5" height="35">&nbsp;</td>
									<td width="50"><?php echo write_lang('key_ordem','Ordem');?>:</td>
									<td width="60"><input type="text" name="ordem" style="WIDTH:58px" maxlength="5" value="<?php echo $ordem;?>" class="form-control"></td>
									<td width="5">&nbsp;</td>
									<td width="50" style="text-align:center"> <?php echo write_lang('key_titulo','Título');?>:</td>
									<td><input type="text" name="descricao" style="WIDTH:100%" maxlength="255" value="" class="form-control"><script>document.form.descricao.value="<?php echo str_replace('"','\"',$C);?>";</script></td>
									<td width="5">&nbsp;</td>
									<td width="50" style="text-align:center"> <?php echo write_lang('key_subtitulo','Sub Título');?>:</td>
									<td><input type="text" name="descricao2" style="WIDTH:100%" maxlength="255" value="" class="form-control"><script>document.form.descricao2.value="<?php echo str_replace('"','\"',$C2);?>";</script></td>
									<?php
									if($protected==0)
									{?>
										<td width="5">&nbsp;</td>
										<td width="70"  align="left"> <?php echo write_lang('key_familia','Família');?>:</td>
										<td align="right" width="200px"><? show_familiax((isset($ID_Familia) ? $ID_Familia :(isset($_GET['ID_ORG']) ? $_GET['ID_ORG'] :0)), 'pages', 'pages_lang',$tipo);?></td><?
									}
									else
									{
										?><td width="5"><input type="hidden" value="<?php echo $ID_Familia?>" name="m_familia" /></td><?
									}
									if($tipo==1){
									?>
	                                <td  align="right"> Permissőes:</td>
									<td align="right"><? show_lista($ID_Grupo,"grupos_clientes","ID_Grupo");?></td>
	                                <?php }?>
									<td width="5" height="26">&nbsp;</td>
									</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<? if($tipo==0){?>
			                            <table width="100%" cellpadding="10" cellspacing=0 border=0 bgcolor="#FFF">
			                            	<tr>
			                                    <td class="new_preto">
													<?  if($task !="duplicar" || ($task !="duplicar" && $task_form=="altera") )
													{
														if($I1!="" && file_exists("../../files/pages/".$ID."_1.".$I1))
														{
															if(file_exists("../../files/pages/$ID"."_1.".$I1))
															{?><img src="<?="../../files/pages/".$ID."_1.".$I1;?>" style="max-width:200px"><br><a target="_blank" href="<?="../../files/pages/".$ID."_1.".$I1;?>">Ver Imagem real</a>&nbsp;&nbsp;<? }?><?
															if($task!="apagar"){?><br><br><?=write_lang('key_apagar','Apagar');?>&nbsp;<input type='checkbox' name='Apagar_img1' value='1'><br><br>
															<table cellpadding="0" cellspacing=0 border=0>
																<tr>
																<td><?=write_lang('key_ou','Ou');?> <?=write_lang('key_alterar','Alterar');?></td><td><input type='file' name='img_peq1'> <input type='hidden' name='tem_foto1' value='1'></td><td>Estilo:</td><td><select name="Img_estilo"><option value="0" <? if(!$Img_estilo) echo 'selected=""';?> >no topo</option><option value="1" <? if($Img_estilo==1) echo 'selected=""';?> >à esquerda</option> </select></td>
																</tr>
															</table>
														<? }
													}
													else{
														?>
														<table cellpadding="0" cellspacing=0 border=0>
														<tr>
															<td align="left"><? echo write_lang('key_nova','Nova');?>:&nbsp;</td><td><input type=file name='img_peq1'> <input type='hidden' name='tem_foto1' value='0'></td><td>Estilo:</td><td><select name="Img_estilo"><option value="0" <? if(!$Img_estilo) echo 'selected=""';?> >no topo</option><option value="1" <? if($Img_estilo==1) echo 'selected=""';?> >à esquerda</option> </select></td>
														</tr>
														</table>
													<? }?>
											<? }?>
											</td>
										</tr>
										</table>
									<? }?>
								</td>
							</tr>
							</table>
							<div style="<? if($mostra==1){?>display:none<?php }elseif($tipo==2 || $tipo==1){?>display:block<?php }?>" id="conf2">
                            <div id="SEO" style="display:none">
                            	<table width="100%" cellpadding="5" cellspacing=0 border=0 bgcolor="#CCCCCC">
                                <tr>
                                    <td width="5" height="26"></td>
                                    <td width="100" ><?php echo write_lang('key_meta_title','Meta Title');?>:</td>
                                    <td><input name="SEO_title" style="WIDTH:100%" maxlength="255" value="" class="form-control" onkeyup="change_seo_title(this.value)"><script>document.form.SEO_title.value="<?php echo str_replace('"','\"',$SEO_title);?>";</script><br />
                                    <?php echo write_lang('key_txt_title','');?>
									</td>
                                </tr>
                                <tr>
                                    <td width="5" height="26"></td>
                                    <td width="100" ><?php echo write_lang('key_meta_description','Meta Description');?>:</td>
                                    <td><input name="SEO_description" style="WIDTH:100%" maxlength="255" value="" class="form-control" onkeyup="change_seo_description(this.value)"><script>document.form.SEO_description.value="<?php echo str_replace('"','\"',$SEO_description);?>";</script><br />
                                    	<?php echo write_lang('key_txt_description','');?>
									</td>
                                </tr>
                                <tr>
                                    <td width="105" height="26" colspan="2"></td>
                                    <td>
                                    	<script>
											function change_seo_description(SEO_description)
											{
												$('#SEO_description_preview').text(SEO_description);
												if(SEO_description == '')
												{
													$('#SEO_description_preview').text('<?=addslashes($C)?>');
												}
											}
											function change_seo_title(SEO_title)
											{
												$('#SEO_title_preview_link').text(SEO_title);
												if(SEO_title == '')
												{
													$('#SEO_title_preview_link').text('<?=addslashes($C)?>');
												}
											}
										</script>
		                                <div class="SERP_preview" style="background:#fff;padding:20px;margin:0 auto">
									    	<div width="100" style="padding-bottom:10px;display:inline-block">
									    		<div style="float:left;width:auto;"><img src="../layout/logo-google-snippet.png" width="50"></div>
									    		<div style="float:left;width:auto;font-size:14px;font-weight:bold;padding-left:5px">Snippet</div>
									    	</div>
									    	<div style="border:solid 1px #CCC;padding:18px;">
										    	<div id="SEO_title_preview" style="text-align:left;width:100%;padding-bottom:5px"><a id="SEO_title_preview_link" style="color:#12c;font-size:16px;" href="<?php echo $site_http_url.seo($site_http_url.'pages.php?page_id=3&prod_id='.$ID, $ID,2, $admin_lang) ?>" target="_blank"><?= $SEO_title_preview ?></a></div>
										    	<div id="SEO_url_preview" style="text-align:left;width:100%;color:#093;padding-bottom:5px;"><?php echo $site_http_url.seo($site_http_url.'pages.php?page_id=3&prod_id='.$ID, $ID,2, $admin_lang) ?></div>
										    	<div id="SEO_description_preview" style="text-align:left;width:100%;padding-bottom:5px;"><?= $SEO_description_preview ?></div>
									    	</div>
									    </div>
									</td>
                                </tr>
                                <tr>
							        <td width="5" height="26"></td>
							        <td width="100"  valign="top">Imagem alternativa para partilhas:</td>
							        <td>
							        	<?php
										if($SEO_img!='')
										{
										?>
											<div class="imagens_400"><img src="<? echo "../../files/pages/".$ID."_seo_".$admin_lang.".".$SEO_img;?>" alt="" border="0" /></div><br />
											<?php
											if($task != "apagar")
											{
												echo write_lang('key_apagar','Apagar');?>
												<input type="checkbox" name="Apagar_SEO_img" value="1">
												<?php echo write_lang('key_ou','ou'); echo write_lang('key_alterar','Alterar')?> <input type="file" name="SEO_img" /><input type="hidden" name="tem_SEO_img" value="1" /><?php
											}
										}
										else
										{
											echo write_lang('key_nova','Nova')?> : <input type="file" name="SEO_img" /><input type="hidden" name="tem_SEO_img" value="1" />
										<?php }?>

								    </td>
							    </tr>
							    <tr>
							        <td width="5" height="26"></td>
							        <td width="100" valign="top">Partilhar:</td>
							        <td><?
							        	$url=$GLOBALS['site_http_url'].seo('pages.php?page_id='.$ID,$ID,0,$admin_lang);
							        	?>
										<a class="btn btn-primary" href="#" onclick="window.open('https://www.facebook.com/sharer/sharer.php?u=<?=$url?>','facebook-share-dialog','width=626,height=436');return false;">FACEBOOK</a>
										<a class="btn btn-primary" href="#" onclick="window.open('https://twitter.com/intent/tweet?text=Tweet%20this&url=<?=$url?>','facebook-share-dialog','width=626,height=436');return false;">TWITTER</a>
										<a class="btn btn-primary" href="#" onclick="window.open('https://www.linkedin.com/sharing/share-offsite?url=<?=$url?>','facebook-share-dialog','width=626,height=436');return false;">LINKEDIN</a>
										<a class="btn btn-primary" href="#" onclick="window.open('https://developers.facebook.com/tools/debug/?q=<?=$url?>','_blank');return false;">DEBUG FACEBOOK</a>
								    </td>
							    </tr>
                                <!-- <tr>
                                    <td width="5" height="26"></td>
                                    <td width="100" ><?php echo write_lang('key_pagina_keywords','Página Keywords');?>:</td>
                                    <td ><input name="SEO_keywords" style="WIDTH:100%" maxlength="255" value="" class="form-control"><script>document.form.SEO_keywords.value="<?php echo str_replace('"','\"',$SEO_keywords);?>";</script><br />
                                     <?php echo write_lang('key_txt_processos','');?></td>
                                </tr> -->
                                <tr>
                                    <td width="5" height="26"></td>
                                    <td width="100">Layout da página:</td>
                                    <td>
										<table width="100%" cellspacing="0" cellpadding="0" border="0">
										<tr>
											<td>
													<div style="width:100%;height:45px;background-image:url('img/0.png');background-repeat:no-repeat;">
														<input value="1" type="radio" name="bootstrap" <?php if(!$bootstrap || $bootstrap==1)echo' checked="checked"';?>>
													</div>
													<div style="width:100%;height:45px;background-image:url('img/1.png');background-repeat:no-repeat;">
														<input value="2" type="radio" name="bootstrap" <?php if($bootstrap==2)echo' checked="checked"';?>>
													</div>
													<div style="width:100%;height:45px;background-image:url('img/2.png');background-repeat:no-repeat;">
														<input value="3" type="radio" name="bootstrap" <?php if($bootstrap==3)echo' checked="checked"';?>>
													</div>
													<div style="width:100%;height:45px;background-image:url('img/3.png');background-repeat:no-repeat;">
														<input value="4" type="radio" name="bootstrap" <?php if($bootstrap==4)echo' checked="checked"';?>>
													</div>
													<div style="width:100%;height:45px;background-image:url('img/4.png');background-repeat:no-repeat;">
														<input value="5" type="radio" name="bootstrap" <?php if($bootstrap==5)echo' checked="checked"';?>>
													</div>
													<div style="width:100%;height:45px;background-image:url('img/5.png');background-repeat:no-repeat;">
														<input value="6" type="radio" name="bootstrap" <?php if($bootstrap==6)echo' checked="checked"';?>>
													</div>
													<div style="width:100%;height:45px;background-image:url('img/6.png');background-repeat:no-repeat;">
														<input value="7" type="radio" name="bootstrap" <?php if($bootstrap==7)echo' checked="checked"';?>>
													</div>


											</td>
										</tr>
										</table>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="5" height="26"></td>
                                    <td width="100">Personalizar medidas bootstrap, exemplo: ( 8;4 ) ou ( 1;3;3;6 ) </td>
                                    <td><input value="<?=$bootstrap_p?>" type="text" name="bootstrap_p"></td>
                                </tr>
                                <tr>
                                    <td width="5" height="26"></td>
                                    <td width="100">Página em formato SCRIPT:</td>
                                    <td><input type="checkbox" value="1" <? if($script==1) echo 'checked'; ?> name="script"> SIM</td>
                                </tr>
                                <tr>
                                    <td width="5" height="26"></td>
                                    <td width="100">Página da plataforma:</td>
                                    <td>
                                    	<select name="interna">
                                        	<option value="" <? if(!$interna) echo 'selected="selected"';?>>----</option>
                                            <option value="1" <? if($interna=="1") echo 'selected="selected"';?>>Módulo LOJA</option>
                                            <option value="2" <? if($interna=="2") echo 'selected="selected"';?>>Módulo Newsletter</option>
                                            <option value="3" <? if($interna=="3") echo 'selected="selected"';?>>Módulo Registo</option>
                                            <option value="4" <? if($interna=="4") echo 'selected="selected"';?>>Outra</option>
                                        </select>



                                    	<!-- <input type="checkbox" value="1" <? if($interna==1) echo 'checked'; ?> name="interna"> SIM --></td>
                                </tr>
                                <tr>
                                    <td width="5" height="26"></td>
                                    <td width="100">Página com menus laterais:</td>
                                    <td><input type="checkbox" value="1" <? if($menus==1) echo 'checked'; ?> name="menus"> SIM</td>
                                </tr>
                                </table>
                            </div>
                            <div id="EFECTS" style="display:none">
                            	<table width="100%" cellpadding="5" cellspacing=0 border=0 bgcolor="#CCCCCC">
                                <tr>
                                    <td width="5" height="26"></td>
                                    <td width="100" ><?php echo write_lang('key_page_enter','Page Enter');?>:</td>
                                    <td>
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
							<?php


							if($tipo==2){?>
                            <table width="100%" cellpadding="10" cellspacing=0 border=0 bgcolor="#CCCCCC">
                            <tr>
                                    <td >
									<br />
									<?php
									if($task !="duplicar" || ($task !="duplicar" && $task_form=="altera"))
									{


										?><table cellpadding="0" cellspacing="0" border="0">

										<tr><td ><?php

										echo write_lang('key_imagem','Imagem');echo ':';

										?></td><td ><?php

										if($I1!="" && file_exists("../../files/pages/".$ID."_1.".$I1))
										{
											if(file_exists("../../files/pages/$ID"."_1.".$I1))
											{
											?>
												<a target="_blank" href="<?php echo "../../files/pages/".$ID."_1.".$I1;?>"><img width="100" border=0 src="<?php echo "../../files/pages/".$ID."_1.".$I1;?>"></a>
												<?php if($task!="apagar"){ echo write_lang('key_apagar','Apagar');?> <input type='checkbox' name='Apagar_img1' value='1' /> <?php echo write_lang('key_ou','Ou');?> <?php echo write_lang('key_alterar','Alterar');?> <input type='file' name='img_peq1' /> <input type='hidden' name='tem_foto1' value='1'/><?php }
											}
										}
										else
										{
										?><input type="file" name='img_peq1' /> <input type='hidden' name='tem_foto1' value='0' /><?php }?>
										</td></tr>




										<?php /* echo write_lang('key_imagem','Imagem');?>:<?
										if($I2!="" && file_exists("../../files/pages/".$ID."_2.".$I2))
										{
											if(file_exists("../../files/pages/$ID"."_2.".$I2))
											{?>
												<a target="_blank" href="<?php echo "../../files/pages/".$ID."_2.".$I2;?>">Ver Imagem</a> | <?
												if($task!="apagar"){ echo write_lang('key_apagar','Apagar');?> <input type='checkbox' name='Apagar_img2' value='1'> <?php echo write_lang('key_ou','Ou');?> <?php echo write_lang('key_alterar','Alterar');?> <input type='file' name='img_peq2'> <input type='hidden' name='tem_foto2' value='1'><?php }
											}
										}
										else{?><input type=file name='img_peq2'> <input type='hidden' name='tem_foto2' value='0'><?php }?>
										<br />
										<?php echo write_lang('key_imagem','Imagem');?>:<?
										if($I3!="" && file_exists("../../files/pages/".$ID."_3.".$I3))
										{
											if(file_exists("../../files/pages/$ID"."_3.".$I3))
											{?>
												<a target="_blank" href="<?php echo "../../files/pages/".$ID."_3.".$I3;?>">Ver Imagem</a> | <?
												if($task!="apagar"){ echo write_lang('key_apagar','Apagar');?> <input type='checkbox' name='Apagar_img3' value='1'> <?php echo write_lang('key_ou','Ou');?> <?php echo write_lang('key_alterar','Alterar');?> <input type='file' name='img_peq3'> <input type='hidden' name='tem_foto3' value='1'><?php }
											}
										}
										else{?><input type=file name='img_peq3'> <input type='hidden' name='tem_foto3' value='0'><?php }?>
										<br />
										<?php echo write_lang('key_imagem','Imagem');?>:<?
										if($I4!="" && file_exists("../../files/pages/".$ID."_4.".$I4))
										{
											if(file_exists("../../files/pages/$ID"."_4.".$I4))
											{?>
												<a target="_blank" href="<?php echo "../../files/pages/".$ID."_4.".$I4;?>">Ver Imagem</a> | <?
												if($task!="apagar"){ echo write_lang('key_apagar','Apagar');?> <input type='checkbox' name='Apagar_img4' value='1'> <?php echo write_lang('key_ou','Ou');?> <?php echo write_lang('key_alterar','Alterar');?> <input type='file' name='img_peq4'> <input type='hidden' name='tem_foto4' value='1'><?php }
											}
										}
										else{?><input type=file name='img_peq4'> <input type='hidden' name='tem_foto4' value='0'><?php } */?>


										<tr><td >
										Documento:</td><td><?
										if($IDOC!="" && file_exists("../../files/pages/".$ID."_doc.".$IDOC))
										{
											if(file_exists("../../files/pages/$ID"."_doc.".$IDOC)){?><a target="_blank" href="<?php echo "../../files/pages/".$ID."_doc.".$IDOC;?>">Ver Documento</a> | <?php }?><?
											if($task!="apagar"){?><?php echo write_lang('key_apagar','Apagar');?> <input type='checkbox' name='Apagar_imgdoc' value='1'> <?php echo write_lang('key_ou','Ou');?> <?php echo write_lang('key_alterar','Alterar');?> <input type='file' name='img_peqdoc'> <input type='hidden' name='tem_fotodoc' value='1'><?php }
										}
										else{?><input type=file name='img_peqdoc'> <input type='hidden' name='tem_fotodoc' value='0'><?php }?>
										<br /><br />
										<td></tr>
										</table>

										<?php
									}?>
                                <?php echo write_lang('key_video','Vídeo');?> <?php echo write_lang('key_ou','ou');?> <?php echo write_lang('key_script','Script');?>:
                                <textarea id="texto3" name="texto3" style="width:100%;height:100px"><?php echo stripslashes($T3);?></textarea><br />
                                <?php echo write_lang('key_resumo','Resumo');?> (<?php echo write_lang('key_maximo','Máximo');?> 255 <?php echo write_lang('key_caracteres','Caracteres');?>):
								<textarea id="texto2" name="texto2" style="width:100%;height:100px"><?php echo stripslashes($T2);?></textarea><br />
                                <table border="0" cellpadding="5" cellspacing=0 width="100%">
								<?php
									/*  if(($tipo==1 || $tipo==2) && ($familia_noticias==1 || $ID==0)){?>
									<tr>
										<td class="preto" style="border-bottom:#000 1px solid" width="110" height="30" align="left"><b><?php echo write_lang('key_categoria','Categorias');?>:</b></td>
										<td class="preto" style="border-bottom:#000 1px solid" align="left" width="160"><input type="checkbox" name="familia_noticias" value="1" <? if($familia_noticias) echo "checked";?> /></td>
										<td class="preto" style="border-bottom:#000 1px solid" align="left"></td>
										<td valign=top  style="border-bottom:#000 1px solid"class="preto" width="200">&nbsp;</td>
									</tr>
									<? if(($tipo==1) && ($familia_noticias==1 || $ID==0)){?>
									<tr>
										<td class="preto" style="border-bottom:#000 1px solid" width="110" height="30" align="left"><b>Visible in the left menu:</b></td>
										<td class="preto" style="border-bottom:#000 1px solid" align="left" width="160"><input type="checkbox" name="is_menu" value="1" <? if($is_menu) echo "checked";?> /></td>
										<td class="preto" style="border-bottom:#000 1px solid" align="left"></td>
										<td valign=top  style="border-bottom:#000 1px solid"class="preto" width="200">&nbsp;</td>
									</tr>
									<tr>
										<td class="preto" style="border-bottom:#000 1px solid" width="110" height="30" align="left"><b>Only Visible Homepage and Footer:</b></td>
										<td class="preto" style="border-bottom:#000 1px solid" align="left" width="160"><input type="checkbox" name="is_menu2" value="1" <? if($is_menu2) echo "checked";?> /></td>
										<td class="preto" style="border-bottom:#000 1px solid" align="left"></td>
										<td valign=top  style="border-bottom:#000 1px solid"class="preto" width="200">&nbsp;</td>
									</tr>
									<?php }?>
								<?php } */

								if(!$familia_noticias)
								{ ?>
									<tr><td valign=top colspan="4">&nbsp;</td></tr>
									<tr>
										<td class="preto" width="110" align="left"><?php echo write_lang('key_valida','Válida');?> <?php echo write_lang('key_de','de');?>:</td>
										<td class="preto" align="left" style="width:250px"><input style="float:left;width:180px" class="form-control" name="data1" id="data1" type="text" value="<?php echo $data1;?>" /></td>
										<? if($tipo==1 || $tipo==2){?><td class="preto" align="left"></td><?php }?>
										<? if($tipo==1 || $tipo==2){?><td class="preto" align="left"></td><?php }?>
									</tr>
									<tr>
										<td class="preto" align="left"><?php echo write_lang('key_ate','até');?>:</td>
										<td class="preto" align="left"><input style="float:left;width:180px" class="form-control" name="data2" id="data2" type="text" value="<?php echo $data2;?>" /></td>
										<? if($tipo==2){?><td colspan="2" class="preto" align="right">&nbsp;</td>
                                        <?php }else{?><td colspan="2" class="preto" align="right"></td><?php }?>
									</tr>
									<tr>
										<td class="preto" width="110" align="left"><?php echo write_lang('key_ou','ou');?> <?php echo write_lang('key_permanente','Permanente');?></td>
										<td class="preto" align="left"><input type="checkbox" name="permanente" value="1" <? if($permanente) echo "checked";?> /></td>
										<? if($tipo==2){?><td colspan="2" valign=top align="right" class="preto"><input type="checkbox" name="destaque" id="destaque" onclick="document.getElementById('destaque_text').checked=0" value="1" <? if($destaque) echo "checked";?> />&nbsp;<?php echo write_lang('key_em','Em');?> <?php echo write_lang('key_destaque','Destaque');?></td><?php }else{?><td>&nbsp;</td><?php }?>
									</tr>
									<tr>
										<td class="preto" align="left"></td>
										<td class="preto" align="left"></td>
										<td class="preto" align="left"></td>
										<td class="preto" align="left"></td>
									</tr>
									<tr><td colspan="4">&nbsp;</td></tr>
									<?php }?>
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
						<td class="preto" align="left">




							<?php if(!$bootstrap || $bootstrap==1){?><textarea id="texto" name="texto"><?php echo stripslashes($T);?></textarea><? if(!$script)show_editor("oEdit1","texto",880,650)?><?php }?>
							<?php if($bootstrap==2){?>
								<div class="row">
									<div class="col-md-4 col-sm-4 col-xs-12"><textarea id="texto" name="texto"><?php echo stripslashes($T);?></textarea><? if(!$script)show_editor("oEdit1","texto",880,650)?></div>
									<div class="col-md-8 col-sm-8 col-xs-12"><textarea id="texto10" name="texto10"><?php echo stripslashes($T10);?></textarea><? if(!$script)show_editor("oEdit10","texto10",880,650)?></div>
								</div><?php
							}?>
							<?php if($bootstrap==3){?>
								<div class="row">
									<div class="col-md-6 col-sm-6 col-xs-12"><textarea id="texto" name="texto"><?php echo stripslashes($T);?></textarea><? if(!$script)show_editor("oEdit1","texto",880,650)?></div>
									<div class="col-md-6 col-sm-6 col-xs-12"><textarea id="texto10" name="texto10"><?php echo stripslashes($T10);?></textarea><? if(!$script)show_editor("oEdit10","texto10",880,650)?></div>
								</div><?php
							}?>
							<?php if($bootstrap==4){?>
								<div class="row">
									<div class="col-md-4 col-sm-4 col-xs-12"><textarea id="texto" name="texto"><?php echo stripslashes($T);?></textarea><? if(!$script)show_editor("oEdit1","texto",880,650)?></div>
									<div class="col-md-4 col-sm-4 col-xs-12"><textarea id="texto10" name="texto10"><?php echo stripslashes($T10);?></textarea><? if(!$script)show_editor("oEdit10","texto10",880,650)?></div>
									<div class="col-md-4 col-sm-4 col-xs-12"><textarea id="texto11" name="texto11"><?php echo stripslashes($T11);?></textarea><? if(!$script)show_editor("oEdit11","texto11",880,650)?></div>
								</div><?php
							}?>
							<?php if($bootstrap==5){?>
								<div class="row">
									<div class="col-md-3 col-sm-3 col-xs-12"><textarea id="texto" name="texto"><?php echo stripslashes($T);?></textarea><? if(!$script)show_editor("oEdit1","texto",880,650)?></div>
									<div class="col-md-3 col-sm-3 col-xs-12"><textarea id="texto10" name="texto10"><?php echo stripslashes($T10);?></textarea><? if(!$script)show_editor("oEdit10","texto10",880,650)?></div>
									<div class="col-md-3 col-sm-3 col-xs-12"><textarea id="texto11" name="texto11"><?php echo stripslashes($T11);?></textarea><? if(!$script)show_editor("oEdit11","texto11",880,650)?></div>
									<div class="col-md-3 col-sm-3 col-xs-12"><textarea id="texto12" name="texto12"><?php echo stripslashes($T12);?></textarea><? if(!$script)show_editor("oEdit12","texto12",880,650)?></div>
								</div><?php
							}?>
							<?php if($bootstrap==6){?>
								<div class="row">
									<div class="col-md-2 col-sm-2 col-xs-12"><textarea id="texto" name="texto"><?php echo stripslashes($T);?></textarea><? if(!$script)show_editor("oEdit1","texto",880,650)?></div>
									<div class="col-md-2 col-sm-2 col-xs-12"><textarea id="texto10" name="texto10"><?php echo stripslashes($T10);?></textarea><? if(!$script)show_editor("oEdit10","texto10",880,650)?></div>
									<div class="col-md-2 col-sm-2 col-xs-12"><textarea id="texto11" name="texto11"><?php echo stripslashes($T11);?></textarea><? if(!$script)show_editor("oEdit11","texto11",880,650)?></div>
									<div class="col-md-2 col-sm-2 col-xs-12"><textarea id="texto12" name="texto12"><?php echo stripslashes($T12);?></textarea><? if(!$script)show_editor("oEdit12","texto12",880,650)?></div>
									<div class="col-md-2 col-sm-2 col-xs-12"><textarea id="texto13" name="texto13"><?php echo stripslashes($T13);?></textarea><? if(!$script)show_editor("oEdit13","texto13",880,650)?></div>
									<div class="col-md-2 col-sm-2 col-xs-12"><textarea id="texto14" name="texto14"><?php echo stripslashes($T14);?></textarea><? if(!$script)show_editor("oEdit14","texto14",880,650)?></div>
								</div><?php
							}?>
							<?php if($bootstrap==7){?>
								<div class="row">
									<div class="col-md-1 col-sm-1 col-xs-12"><textarea id="texto" name="texto"><?php echo stripslashes($T);?></textarea><? if(!$script)show_editor("oEdit1","texto",880,650)?></div>
									<div class="col-md-1 col-sm-1 col-xs-12"><textarea id="texto10" name="texto10"><?php echo stripslashes($T10);?></textarea><? if(!$script)show_editor("oEdit10","texto10",880,650)?></div>
									<div class="col-md-1 col-sm-1 col-xs-12"><textarea id="texto11" name="texto11"><?php echo stripslashes($T11);?></textarea><? if(!$script)show_editor("oEdit11","texto11",880,650)?></div>
									<div class="col-md-1 col-sm-1 col-xs-12"><textarea id="texto12" name="texto12"><?php echo stripslashes($T12);?></textarea><? if(!$script)show_editor("oEdit12","texto12",880,650)?></div>
									<div class="col-md-1 col-sm-1 col-xs-12"><textarea id="texto13" name="texto13"><?php echo stripslashes($T13);?></textarea><? if(!$script)show_editor("oEdit13","texto13",880,650)?></div>
									<div class="col-md-1 col-sm-1 col-xs-12"><textarea id="texto14" name="texto14"><?php echo stripslashes($T14);?></textarea><? if(!$script)show_editor("oEdit14","texto14",880,650)?></div>
									<div class="col-md-1 col-sm-1 col-xs-12"><textarea id="texto15" name="texto15"><?php echo stripslashes($T15);?></textarea><? if(!$script)show_editor("oEdit15","texto15",880,650)?></div>
									<div class="col-md-1 col-sm-1 col-xs-12"><textarea id="texto16" name="texto16"><?php echo stripslashes($T16);?></textarea><? if(!$script)show_editor("oEdit16","texto16",880,650)?></div>
									<div class="col-md-1 col-sm-1 col-xs-12"><textarea id="texto17" name="texto17"><?php echo stripslashes($T17);?></textarea><? if(!$script)show_editor("oEdit17","texto17",880,650)?></div>
									<div class="col-md-1 col-sm-1 col-xs-12"><textarea id="texto18" name="texto18"><?php echo stripslashes($T18);?></textarea><? if(!$script)show_editor("oEdit18","texto18",880,650)?></div>
									<div class="col-md-1 col-sm-1 col-xs-12"><textarea id="texto19" name="texto19"><?php echo stripslashes($T19);?></textarea><? if(!$script)show_editor("oEdit19","texto19",880,650)?></div>
									<div class="col-md-1 col-sm-1 col-xs-12"><textarea id="texto20" name="texto20"><?php echo stripslashes($T20);?></textarea><? if(!$script)show_editor("oEdit20","texto20",880,650)?></div>
								</div><?php
							}?>


						</td>
					</tr>
                    <?
					if($compare_lingua)
					{?>
                    <tr><td class="preto" align="left"><textarea id="textob" name="textob"><?php echo stripslashes($textob);?></textarea><? if(!$script)show_editor("oEdit1b","textob",880,650)?></td></tr><?
					}
					?>
					</table>
                    <?
						show_page_buttons($protected);
						if(!$draft_id && !$converter && $ID)
						{

							$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
							$porpagina = 20;

							$sql="Select pages.*,Cabecalho,Cabecalho2 from pages inner join pages_lang on pages.ID=pages_lang.ID_PAI where Lang='$admin_lang' and ID_ORG=$ID Order by ordem";
							$rs->db_open($db_link,$DB_name,$sql);
							$total = $rs->db_rows;

							$n_pags = ceil($total / $porpagina);// nº de paginas
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
								for($pi = ($pagina - 6);$pi < ($pagina + 7);$pi++)
								{
									$pgn = 'onclick=window.open("pagina.php?ID='.$_GET['ID'].'&ID_ORG='.$_GET['ID_ORG'].'&pagina='.$pi.'","_self")';
									$return .= ($pi >= 1 && $pi <= $n_pags) ? $pagina == $pi ? '<td width="2px">&nbsp;</td><td width="20" height="20" bgcolor="#84c6fe" align="center" >'.sprintf(($pi)).'</td>' :'<td width="2px">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer">'.sprintf($pi).'</td>' :'';
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
							<td class="branco" style="font-weight:bold;font-size:15px;" align="right"><input class="btn btn-primary" type="button" style="width:160px" value="<?php echo write_lang('key_nova_noticia','Nova Notícia');?>" onClick="javascript:window.open('pagina.php?ID=0&ID_ORG=<?php echo $ID?>&tipo=<?php echo $tipo?>&Dump=<?php echo uniqid("");?>','_self')"></td>
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
							<td width="40" height="50" bgcolor="<?php echo $cor2;?>" align="right">&nbsp;</td>
							<td bgcolor="<?php echo $cor2;?>" width="8">&nbsp;</td>
							<td valign="middle" bgcolor="<?php echo $cor2;?>" style="font-size:15px"><b><?php echo write_lang('key_noticias','Notícias');?></b></td>
							<? if(($tipo==1 || $tipo==2)){?><td valign="middle" align="center" bgcolor="<?php echo $cor2;?>" style="font-size:15px"><b><?php echo write_lang('key_comentarios','Comentários');?></b></td><?php }?>
							<? if(($tipo==1 || $tipo==2)){?><td valign="middle" align="center"  bgcolor="<?php echo $cor2;?>" width="100"><? echo CountRcords($codigo)?></td><?php }?>
							<td valign="middle" bgcolor="<?php echo $cor2;?>" align="right" width="24"></td>
							<td valign="middle" bgcolor="<?php echo $cor2;?>" align="right" width="24"></td>
							<td bgcolor="<?php echo $cor2;?>" width="10">&nbsp;</td>
							</tr>
							<?
								while(!$rs->EOF())
								{
									$t++;
									$codigo=$rs->db_data["ID"];
									$ID_ORG2=$rs->db_data["ID_ORG"];
									$name=$rs->db_data["Cabecalho"];
									$name2=$rs->db_data["Cabecalho2"];
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
										<td width="30" bgcolor="<?php echo $cor2;?>" align="center"><a class="preto" href="pagina.php?ID=<?php echo $rs->db_data["ID"]?>"><?
										if($tipo==0) echo '<img border=0 src="../images/page.png" />';
										if($tipo==1) echo '<img border=0 src="../images/page_priv.png" />';
										if($tipo==2) echo '<img border=0 src="../images/page_news.png" />';
										?></a></td>
										<td bgcolor="<?php echo $cor2;?>" width="8">&nbsp;</td>
										<? if(($tipo==1 || $tipo==2)){?>
										<td valign="middle" bgcolor="<?php echo $cor2;?>"><a class="preto" href="pagina.php?ID=<?php echo $rs->db_data["ID"]?>"><b><?php if($name2 != '') {echo $name.': '.$name2;} else {echo $name;}?></b></a></td>
										<?php }else{?>
										<td valign="middle" bgcolor="<?php echo $cor2;?>"><a class="preto" href="pagina.php?ID=<?php echo $rs->db_data["ID"]?>"><?php if($name2 != '') {echo $name.': '.$name2;} else {echo $name;}?></a></td>
										<?php }?>
										<? if(($tipo==1 || $tipo==2)) {?><td valign="middle"  align="center"  bgcolor="<?php echo $cor2;?>" width="100"><? if(!$fam_noticias) echo CountRcords_Comments($codigo)?></td><?php }?>
										<? if(($tipo==1 || $tipo==2)) {?><td valign="middle" align="center" bgcolor="<?php echo $cor2;?>" width="100"><? if($fam_noticias) echo CountRcords($codigo)?></td><?php }?>
										<td valign="middle" bgcolor="<?php echo $cor2;?>" align="right" width="24"><a class="italico" href="pagina.php?ID=<?php echo $rs->db_data["ID"]?>&ID_ORG=<?php echo $ID_ORG2;?>&task=duplicar&Dump=<? echo uniqid("");?>"><img title="Duplicate page" src="images/duplicate.png" border=0></a></td>
										<td valign="middle" bgcolor="<?php echo $cor2;?>" align="right" width="24"><a class="italico" href="pagina.php?ID=<?php echo $rs->db_data["ID"]?>&task=apagar"><img alt="" src="images/del2.png" border=0 align="absmiddle" title="Delete page"></a></td>
										<td bgcolor="<?php echo $cor2;?>" width="10">&nbsp;</td>
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
				<td width="200" width="200" valign="top">
					<?
					global $noticias_por_catalogo;
					show_fast_tools($tipo);
					if(!$noticias_por_catalogo){show_conversoes();};
					show_seo();
					show_efeitos();
					?>
				</td>
            </tr>
            </table>
		</form>
 	</div><br>
<!--script src="chosen.jquery.min.js" type="text/javascript"></script>
<script type="text/javascript"> $(".form-control").chosen();$(".form-control-deselect").chosen({allow_single_deselect:true});</script-->
<?php 	include '../layout/bottom.inc.php';?>