<?
	require_once "settings.inc.php";
	if($task=="apagar"){$task_button=write_lang('key_apagar_este_registo','Apagar este Registo');$task_form="apaga";}
	elseif(isset($_GET['duplicar']) && $_GET['duplicar']==1){$task_button=write_lang('key_duplicar_este_registo','Duplicar este Registo');$task_form="duplicar";}
	elseif($_GET[TBID]==0){$task_button=write_lang('key_gravar_novo_registo','Gravar Novo Registo');$task_form="insere";}
	else {$task_button=write_lang('key_gravar_alteracoes','Gravar alterações');$task_form="altera";}
	$D=getdate();
	acoes_selecionados();
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$ID_ORG=0;
	$rs=new DataObj;
	$rs2=new DataObj;
	if(isset($_GET[TBID]) && $_GET[TBID]!=0)
	{
		$sql='SELECT
			ID,permissoes,ID_ORG,visivel,ocultar,destaque_home,destaque_lateral,destaque_topo,categorias,ordem,data,data2,img_ext,pdf_ext,permanente,open,users,Qtd,Posicao,titulo_visivel,
			titulo_cor,
			titulo_tamanho,
			titulo_font_weight,
			Texto1_cor,
			Texto1_tamanho,
			Texto1_font_weight,
			Texto1_line_height,
			Texto2_cor,
			Texto2_tamanho,
			Texto2_font_weight,
			Texto2_line_height,
			Texto3_cor,
			Texto3_tamanho,
			Texto3_font_weight,
			Texto3_line_height
			FROM '.TBMODULO.' WHERE '.TBID.'='.$_GET[TBID];
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$ID=$rs->db_data['ID'];
			$ID_ORG=$rs->db_data['ID_ORG'];
			$visivel=$rs->db_data['visivel'];
			$destaque_home=$rs->db_data['destaque_home'];
			$destaque_lateral=$rs->db_data['destaque_lateral'];
			$destaque_topo=$rs->db_data['destaque_topo'];
			$categorias=explode(';',$rs->db_data['categorias']);
			$ordem=$rs->db_data['ordem'];
			$Qtd=$rs->db_data['Qtd'];
			$Posicao=$rs->db_data['Posicao'];
			$titulo_visivel=$rs->db_data['titulo_visivel'];
			$titulo_cor=$rs->db_data['titulo_cor'];
			$titulo_tamanho=$rs->db_data['titulo_tamanho'];
			$titulo_font_weight=$rs->db_data['titulo_font_weight'];
			$Texto1_cor=$rs->db_data['Texto1_cor'];
			$Texto1_tamanho=$rs->db_data['Texto1_tamanho'];
			$Texto1_font_weight=$rs->db_data['Texto1_font_weight'];
			$Texto1_line_height=$rs->db_data['Texto1_line_height'];

			$Texto2_cor=$rs->db_data['Texto2_cor'];
			$Texto2_tamanho=$rs->db_data['Texto2_tamanho'];
			$Texto2_font_weight=$rs->db_data['Texto2_font_weight'];
			$Texto2_line_height=$rs->db_data['Texto2_line_height'];

			$Texto3_cor=$rs->db_data['Texto3_cor'];
			$Texto3_tamanho=$rs->db_data['Texto3_tamanho'];
			$Texto3_font_weight=$rs->db_data['Texto3_font_weight'];
			$Texto3_line_height=$rs->db_data['Texto3_line_height'];

			$data=$rs->db_data['data'];
			$data2=$rs->db_data['data2'];
			$img_ext=$rs->db_data['img_ext'];
			$pdf_ext=$rs->db_data['pdf_ext'];
			$permanente=$rs->db_data['permanente'];
			$open=$rs->db_data['open'];
			$users=$rs->db_data['users'];
			$permissoes=$rs->db_data['permissoes'];
			$users2=explode(';',$rs->db_data["users"]);
			$sql='SELECT titulo,titulo_2,titulo_3,resumo,texto,texto3 FROM '.TBMODULO_LANG.' WHERE ID_PAI='.$_GET[TBID].' AND lang="'.$GLOBALS['admin_lang'].'"';
			$rs2->db_open($db_link,$DB_name,$sql);
			if(!$rs2->EOF())
			{
				$titulo=limpa_editor_lixo(stripslashes($rs2->db_data['titulo']));
				$titulo_2=limpa_editor_lixo(stripslashes($rs2->db_data['titulo_2']));
				$titulo_3=limpa_editor_lixo(stripslashes($rs2->db_data['titulo_3']));
				$resumo=limpa_editor_lixo(stripslashes($rs2->db_data['resumo']));
				$texto=limpa_editor_lixo(stripslashes($rs2->db_data['texto']));
				$texto3=limpa_editor_lixo(stripslashes($rs2->db_data['texto3']));
			}
			$rs2->db_close();
		}
		$rs->db_close();
	}
	if(!$caminho_bt_editor){$caminho_bt_editor1='../../../editor/';$caminho_bt_editor2='../editor/';}
	else{$caminho_bt_editor1=$caminho_bt_editor2=$caminho_bt_editor;}
	require_once "../layout/header.inc.php";
	// VAMOS VER PERMISSOES
	if(isset($rs->db_data["users"]) && ($rs->db_data["users"]=='' || $rs->db_data["users"]=='0'))
	{
		$ID_principal=BLOCOS_PATH_PRINCIPAL($rs->db_data["ID_ORG"]);
		$users2=GET_BLOCO_USERS($ID_principal);
	}
	if($GLOBALS['dados']['Grupo_ID']==1 || $GLOBALS['dados']['Grupo_ID']==4 || ($GLOBALS['dados']['Grupo_ID']==5 && in_array($GLOBALS['admin_id'],$users2)) || $_GET['ID']==0)
	{
		?>
		<script type="text/javascript" src="../editor/ckeditor_4_5_1/ckeditor.js"></script>
		<script type="text/javascript" src="../editor/ckeditor_4_5_1/ckfinder/ckfinder.js"></script>
		<link rel="stylesheet" type="text/css" href="sa.css" />
		<link rel="stylesheet" type="text/css" href="chosen.css?dp=<? echo dumpacache?>" />
		<link rel="stylesheet" href="../../layout/fonts/awesome/css/font-awesome.css">
		<link rel="stylesheet" href="../../layout/fonts/NewsGothic/style.css">
		<style type="text/css">input,textarea{font-family:"NewsGothic-Light,sans-serif"}</style>
		<script>var SITE_HTTP_URL='<? echo $GLOBALS['site_http_url']?>';var SITE_SHELL_URL='<? echo $GLOBALS['site_shell_url']?>';</script>
		<script>$(document).ready(function()
		{
			$('.fancybox_img').fancybox();
			$('.fancybox2').live('click',function(){ var href=$(this).attr('href'); $.fancybox({ 'showCloseButton' : false,'titlePosition' : 'inside',title : true,autoSize : false,modal : false,fitToView : false,topRatio : 0,content : '<iframe width="100%" height="100%" src="'+href+'"></iframe>',width : 754,maxHeight : 600,scrolling : 'no',type : 'html',onComplete: function() {$("#fancybox-wrap").stop().animate({'top':'10px',},0); $("#fancybox-wrap").css({'z-index':'9999999999999999',},0); } }); return false;});
		});
		function addToProduct(){$("#s_listafamiliasprod").append($("#listademarcas option:selected"));}
		function removeFromProduct(){$("#listademarcas").append($("#s_listafamiliasprod option:selected"));}
		</script>
		<script language="JavaScript" src="sa.js"></script>
		<div class="center" style="position:relative;" id="wp">
			<form name="form" method="post" action="pagina_on_fly_trata.php?<?=TBID?>=<?=$ID?>&task_form=<?=$task_form?>&d=<?=uniqID("")?>" enctype="multipart/form-data">
				<input type="hidden" name="m_lang" value="<? echo $GLOBALS['admin_lang'];?>" />
				<input type="hidden" name="<? echo TBID?>" value="<? echo (isset($_GET['duplicar']) && $_GET['duplicar'] > 0) ? 0 : $ID;?>" />
				<input type="hidden" name="duplicar" value="<? echo (isset($_GET['duplicar']) && $_GET['duplicar'] > 0) ? 1 : 0;?>" />
				<input type="hidden" name="slashes" value="'" />
				<table  align="center" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td valign="top" align="left">
						<table class="new_quadros" style="background:#FFF" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
						<tr>
							<td align="left" valign="top" class="preto">
								<div>
									<div class="clearboth"></div>
									<input type="hidden" name="lang" id="lang" class="form-control" value="<? echo $GLOBALS['admin_lang']?>" style="width:50px"/>
									<table width="100%" cellpadding="0" cellspacing="0" border="0">
									<tr><td style="height:5px" colspan="2"></td></tr>
                                    <tr>
                                        <td width="130" align="left">Tipo de elemento:</td>
                                        <td align="left">
                                            <table border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td>
                                                    <?
                                                        $i=0;
                                                        $arr_familias[$i]['brand_id']=$i+1;$arr_familias[$i]['marca_nome']="GRUPO de MODULOS";$i++;//1
                                                        $arr_familias[$i]['brand_id']=$i+1;$arr_familias[$i]['marca_nome']="Banner";$i++;//2
                                                        $arr_familias[$i]['brand_id']=$i+1;$arr_familias[$i]['marca_nome']="Texto simples centrado";$i++;//3
                                                        $arr_familias[$i]['brand_id']=$i+1;$arr_familias[$i]['marca_nome']="Carrocell Imagens - Bolas";$i++;//4
                                                        $arr_familias[$i]['brand_id']=$i+1;$arr_familias[$i]['marca_nome']="Carrocell Imagens - Retangulos";$i++;//5
                                                        $arr_familias[$i]['brand_id']=$i+1;$arr_familias[$i]['marca_nome']="Conteudos centrados";$i++;//6
                                                        $arr_familias[$i]['brand_id']=$i+1;$arr_familias[$i]['marca_nome']="Texto Livre";$i++;//7
                                                        $arr_familias[$i]['brand_id']=$i+1;$arr_familias[$i]['marca_nome']="2 Textos lado a lado";$i++;//8
                                                        $arr_familias[$i]['brand_id']=$i+1;$arr_familias[$i]['marca_nome']="Destaques tipo Blogue";$i++;//9
                                                        $arr_familias[$i]['brand_id']=$i+1;$arr_familias[$i]['marca_nome']="Header estilo 1";$i++;//10
                                                        $arr_familias[$i]['brand_id']=$i+1;$arr_familias[$i]['marca_nome']="Galeria imagens";$i++;//11
                                                        $arr_familias[$i]['brand_id']=$i+1;$arr_familias[$i]['marca_nome']="3 Textos lado a lado";$i++;//12
                                                        $arr_familias[$i]['brand_id']=$i+1;$arr_familias[$i]['marca_nome']="Galeria2 imagens";$i++;//13
                                                        $arr_familias[$i]['brand_id']=$i+1;$arr_familias[$i]['marca_nome']="Galeria3";$i++;//13
                                                        $arr_familias[$i]['brand_id']=$i+1;$arr_familias[$i]['marca_nome']="Accordion";$i++;//13
                                                        $arr_familias[$i]['brand_id']=$i+1;$arr_familias[$i]['marca_nome']="Contactos";$i++;//13
                                                        gera_lista_brands_cat($arr_familias,$categorias);

                                                        //Opcoes de cada tipo de elemento:
                                                        $opcoes[1]['Titulo']=1; $opcoes[1]['titulo_cor']=0; $opcoes[1]['Texto1']=0; $opcoes[1]['Texto2']=0; $opcoes[1]['Texto3']=0; $opcoes[1]['Imagens']=0; $opcoes[1]['Imagem_bg']=0; $opcoes[1]['legen_local']=0; $opcoes[1]['gal_qtd']=0; $opcoes[1]['gal_tipoanim']=0;
                                                        $opcoes[2]['Titulo']=1; $opcoes[2]['titulo_cor']=0; $opcoes[2]['Texto1']=0; $opcoes[2]['Texto2']=0; $opcoes[2]['Texto3']=0; $opcoes[2]['Imagens']=1; $opcoes[2]['Imagem_bg']=0; $opcoes[2]['legen_local']=0; $opcoes[2]['gal_qtd']=0; $opcoes[2]['gal_tipoanim']=0;
                                                        $opcoes[3]['Titulo']=1; $opcoes[3]['titulo_cor']=1; $opcoes[3]['Texto1']=1; $opcoes[3]['Texto2']=1; $opcoes[3]['Texto3']=1; $opcoes[3]['Imagens']=1; $opcoes[3]['Imagem_bg']=1; $opcoes[3]['legen_local']=1; $opcoes[3]['gal_qtd']=1; $opcoes[3]['gal_tipoanim']=1;
                                                        $opcoes[4]['Titulo']=1; $opcoes[4]['titulo_cor']=1; $opcoes[4]['Texto1']=1; $opcoes[4]['Texto2']=0; $opcoes[4]['Texto3']=0; $opcoes[4]['Imagens']=1; $opcoes[4]['Imagem_bg']=1; $opcoes[4]['legen_local']=0; $opcoes[4]['gal_qtd']=1; $opcoes[4]['gal_tipoanim']=0;
                                                        $opcoes[5]['Titulo']=1; $opcoes[5]['titulo_cor']=1; $opcoes[5]['Texto1']=1; $opcoes[5]['Texto2']=0; $opcoes[5]['Texto3']=0; $opcoes[5]['Imagens']=1; $opcoes[5]['Imagem_bg']=1; $opcoes[5]['legen_local']=1; $opcoes[5]['gal_qtd']=1; $opcoes[5]['gal_tipoanim']=0;
                                                        $opcoes[6]['Titulo']=1; $opcoes[6]['titulo_cor']=1; $opcoes[6]['Texto1']=1; $opcoes[6]['Texto2']=1; $opcoes[6]['Texto3']=1; $opcoes[6]['Imagens']=1; $opcoes[6]['Imagem_bg']=1; $opcoes[6]['legen_local']=1; $opcoes[6]['gal_qtd']=1; $opcoes[6]['gal_tipoanim']=1;
                                                        $opcoes[7]['Titulo']=1; $opcoes[7]['titulo_cor']=1; $opcoes[7]['Texto1']=1; $opcoes[7]['Texto2']=1; $opcoes[7]['Texto3']=1; $opcoes[7]['Imagens']=1; $opcoes[7]['Imagem_bg']=1; $opcoes[7]['legen_local']=1; $opcoes[7]['gal_qtd']=1; $opcoes[7]['gal_tipoanim']=1;
                                                        $opcoes[8]['Titulo']=1; $opcoes[8]['titulo_cor']=1; $opcoes[8]['Texto1']=1; $opcoes[8]['Texto2']=1; $opcoes[8]['Texto3']=1; $opcoes[8]['Imagens']=1; $opcoes[8]['Imagem_bg']=1; $opcoes[8]['legen_local']=1; $opcoes[8]['gal_qtd']=1; $opcoes[8]['gal_tipoanim']=1;
                                                        $opcoes[9]['Titulo']=1; $opcoes[9]['titulo_cor']=1; $opcoes[9]['Texto1']=1; $opcoes[9]['Texto2']=1; $opcoes[9]['Texto3']=1; $opcoes[9]['Imagens']=1; $opcoes[9]['Imagem_bg']=1; $opcoes[9]['legen_local']=1; $opcoes[9]['gal_qtd']=1; $opcoes[9]['gal_tipoanim']=1;
                                                        $opcoes[10]['Titulo']=1;$opcoes[10]['titulo_cor']=1;$opcoes[10]['Texto1']=1;$opcoes[10]['Texto2']=0;$opcoes[10]['Texto3']=0;$opcoes[10]['Imagens']=1;$opcoes[10]['Imagem_bg']=0;$opcoes[10]['legen_local']=0;$opcoes[10]['gal_qtd']=0;$opcoes[10]['gal_tipoanim']=0;
                                                        $opcoes[11]['Titulo']=1;$opcoes[11]['titulo_cor']=1;$opcoes[11]['Texto1']=1;$opcoes[11]['Texto2']=1;$opcoes[11]['Texto3']=0;$opcoes[11]['Imagens']=1;$opcoes[11]['Imagem_bg']=1;$opcoes[11]['legen_local']=1;$opcoes[11]['gal_qtd']=1;$opcoes[11]['gal_tipoanim']=1;
                                                        $opcoes[12]['Titulo']=1;$opcoes[12]['titulo_cor']=1;$opcoes[12]['Texto1']=1;$opcoes[12]['Texto2']=1;$opcoes[12]['Texto3']=1;$opcoes[12]['Imagens']=1;$opcoes[12]['Imagem_bg']=1;$opcoes[12]['legen_local']=1;$opcoes[12]['gal_qtd']=1;$opcoes[12]['gal_tipoanim']=1;
                                                        $opcoes[13]['Titulo']=1;$opcoes[13]['titulo_cor']=1;$opcoes[13]['Texto1']=1;$opcoes[13]['Texto2']=1;$opcoes[13]['Texto3']=1;$opcoes[13]['Imagens']=1;$opcoes[13]['Imagem_bg']=1;$opcoes[13]['legen_local']=1;$opcoes[13]['gal_qtd']=1;$opcoes[13]['gal_tipoanim']=1;
                                                        $opcoes[14]['Titulo']=1;$opcoes[14]['titulo_cor']=1;$opcoes[14]['Texto1']=1;$opcoes[14]['Texto2']=1;$opcoes[14]['Texto3']=1;$opcoes[14]['Imagens']=1;$opcoes[14]['Imagem_bg']=1;$opcoes[14]['legen_local']=1;$opcoes[14]['gal_qtd']=1;$opcoes[14]['gal_tipoanim']=1;
                                                        $opcoes[15]['Titulo']=1;$opcoes[15]['titulo_cor']=1;$opcoes[15]['Texto1']=1;$opcoes[15]['Texto2']=0;$opcoes[15]['Texto3']=0;$opcoes[15]['Imagens']=0;$opcoes[15]['Imagem_bg']=1;$opcoes[15]['legen_local']=0;$opcoes[15]['gal_qtd']=0;$opcoes[15]['gal_tipoanim']=0;
                                                        $opcoes[16]['Titulo']=1;$opcoes[16]['titulo_cor']=1;$opcoes[16]['Texto1']=1;$opcoes[16]['Texto2']=0;$opcoes[16]['Texto3']=0;$opcoes[16]['Imagens']=0;$opcoes[16]['Imagem_bg']=1;$opcoes[16]['legen_local']=0;$opcoes[16]['gal_qtd']=0;$opcoes[16]['gal_tipoanim']=0;

                                                    ?>
                                                </td>
                                            </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr><td style="height:5px" colspan="2"></td></tr>
                                    <tr>
                                        <td width="130" align="left">Ativar:</td>
                                        <td align="left">
                                            <table border="0" cellspacing="0" cellpadding="0">
                                            <tr>
	                                            <td><label class="label_check" for="visivel"><input type="checkbox" name="visivel" id="visivel" value="1" <? if($visivel==1)echo 'checked="checked"'; ?> /></label></td>
                                            </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr><td style="height:5px" colspan="2"></td></tr>
                                    <tr>
                                        <td align="left">Data Início:</td>
                                        <td align="left">
                                            <table cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td align="left"><input type="text" name="data" id="data" class="box dropdate" style="width:80px !important" value="<? echo (isset($data) && $data!='0000-00-00') ? $data : date('Y-m-d'); ?>" /></td>
                                                <td align="center" width="80">Data Fim:</td>
                                                <td align="left"><input type="text" name="data2" id="data2" class="box dropdate" style="width:80px !important" value="<? echo (isset($data2) && $data2!='0000-00-00') ? $data2 : date('Y-m-d'); ?>" /></td>
                                                <td align="center" width="130"> OU ignorar datas:</td>
                                                <td width="45"><label class="label_check" for="permanente"><input type="checkbox" name="permanente" id="permanente" value="1" <? if($permanente==1)echo 'checked="checked"'; ?> /></label></td>
                                            </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr><td style="height:5px" colspan="2"></td></tr>
                                    <tr>
                                        <td align="left">Ordem:</td>
                                        <td align="left">
                                            <table border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                            <td>
                                                <input type="text" name="ordem" id="ordem" class="form-control" value="<? echo $ordem?>" style="width:50px"/>
                                            </td>
                                            </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr><td style="height:5px" colspan="2"></td></tr>
                                    <tr>
                                        <td align="left">Módulo Pai:</td>
                                        <td align="left">
                                            <table cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td align="left" width="100"><? echo gera_lista_path((isset($_GET['ID_ORG']) && $_GET['ID_ORG'] > 0) ? $_GET['ID_ORG'] : $ID_ORG); ?></td>
                                            </tr>
                                            </table>
                                        </td>
                                    </tr><?
                                    if(!$ID || $opcoes[$categorias[0]]['Titulo'])
									{?>
										<tr><td style="height:5px" colspan="2"></td></tr>
										<tr>
											<td align="left">Titulo:</td>
											<td align="left">

												<table cellpadding="0" cellspacing="0" border="0" width="100%">
												<tr>
													<td align="left" width="300"><?

														//vamos simular o estilo do titulo::::::::
														$style_titulo='';
														if($titulo_cor || $titulo_tamanho || $titulo_font_weight)
														{
															if($titulo_cor)$str_titulo_cor='color:'.$titulo_cor.';';
															if($titulo_tamanho)$str_titulo_tamanho='font-size:'.$titulo_tamanho.'px;height:'.$titulo_tamanho.'px;';
															if($titulo_font_weight)$str_titulo_font_weight='font-weight:'.$titulo_font_weight.';';
															$style_titulo=$str_titulo_cor.$str_titulo_tamanho.$str_titulo_font_weight;
														}
														$style_texto1='';
														if($Texto1_cor || $Texto1_tamanho || $Texto1_font_weight)
														{
															if($Texto1_cor)$str_texto1_cor='color:'.$Texto1_cor.';';
															if($Texto1_tamanho)$str_texto1_tamanho='font-size:'.$Texto1_tamanho.'px;';
															if($Texto1_font_weight)$str_texto1_font_weight='font-weight:'.$Texto1_font_weight.';';
															if($Texto1_line_height)$str_texto1_line_height='line-height:'.$Texto1_line_height.'px;';
															$style_texto1=$str_texto1_cor.$str_texto1_tamanho.$str_texto1_font_weight.$str_texto1_line_height;
														}

														if($Texto2_cor || $Texto2_tamanho || $Texto2_font_weight)
														{
															if($Texto2_cor)$str_texto2_cor='color:'.$Texto2_cor.';';
															if($Texto2_tamanho)$str_texto2_tamanho='font-size:'.$Texto2_tamanho.'px;';
															if($Texto2_font_weight)$str_texto2_font_weight='font-weight:'.$Texto2_font_weight.';';
															if($Texto2_line_height)$str_texto2_line_height='line-height:'.$Texto2_line_height.'px;';
															$style_texto2=$str_texto2_cor.$str_texto2_tamanho.$str_texto2_font_weight.$str_texto2_line_height;
														}

														if($Texto3_cor || $Texto3_tamanho || $Texto3_font_weight)
														{
															if($Texto3_cor)$str_texto3_cor='color:'.$Texto3_cor.';';
															if($Texto3_tamanho)$str_texto3_tamanho='font-size:'.$Texto3_tamanho.'px;';
															if($Texto3_font_weight)$str_texto3_font_weight='font-weight:'.$Texto3_font_weight.';';
															if($Texto3_line_height)$str_texto3_line_height='line-height:'.$Texto3_line_height.'px;';
															$style_texto3=$str_texto3_cor.$str_texto3_tamanho.$str_texto3_font_weight.$str_texto3_line_height;
														}
														?>
														<input class="form-control" type="text" name="titulo" id="titulo" value="<? echo $titulo;?>" style="width:95%;<?=$style_titulo?>"/>
													</td>
													<? if($opcoes[$categorias[0]]['titulo_cor']) { ?>
													<td align="left">Cor:</td>
													<td align="left">
														<select name="titulo_cor">
															<option value="" <? if(!$titulo_cor) echo 'selected'?>>---</option>
															<option value="#DD723C" <? if($titulo_cor=='#DD723C') echo 'selected'?>>Laranja</option>
															<option value="#868789" <? if($titulo_cor=='#868789') echo 'selected'?>>Cinza Claro</option>
															<option value="#2D343E" <? if($titulo_cor=='#2D343E') echo 'selected'?>>Cinza escuro</option>
														</select>
													</td>

													<td align="left">Tamanho (pixels):</td>
													<td align="left"><input class="form-control" type="text" name="titulo_tamanho" id="titulo_tamanho" value="<? echo $titulo_tamanho;?>" style="width:50px"/></td>

													<td align="left">Weight:</td>
													<td align="left">
														<select name="titulo_font_weight">
															<option value="" <? if(!$titulo_font_weight) echo 'selected'?>>normal</option>
															<option value="bold" <? if($titulo_font_weight=='bold') echo 'selected'?>>bold</option>
															<option value="bolder" <? if($titulo_font_weight=='bolder') echo 'selected'?>>bolder</option>
															<option value="lighter" <? if($titulo_font_weight=='lighter') echo 'selected'?>>lighter</option>
															<option value="100" <? if($titulo_font_weight=='100') echo 'selected'?>>100</option>
															<option value="200" <? if($titulo_font_weight=='200') echo 'selected'?>>200</option>
															<option value="300" <? if($titulo_font_weight=='300') echo 'selected'?>>300</option>
															<option value="400" <? if($titulo_font_weight=='400') echo 'selected'?>>400</option>
															<option value="500" <? if($titulo_font_weight=='500') echo 'selected'?>>500</option>
															<option value="600" <? if($titulo_font_weight=='600') echo 'selected'?>>600</option>
															<option value="700" <? if($titulo_font_weight=='700') echo 'selected'?>>700</option>
															<option value="800" <? if($titulo_font_weight=='800') echo 'selected'?>>800</option>
															<option value="900" <? if($titulo_font_weight=='900') echo 'selected'?>>900</option>
														</select>
													</td>

													<td align="left">Ocultar:</td>
													<td align="left"><label class="label_check" for="titulo_visivel"><input type="checkbox" name="titulo_visivel" id="titulo_visivel" value="1" <? if($titulo_visivel==1)echo 'checked="checked"'; ?> /></label></td>
													<? }?>

												</tr>
												</table>

											</td>
										</tr>
										<tr><td style="height:5px" colspan="2"></td></tr><?
									}?>
									</table>
                                    <table width="100%" cellpadding="0" cellspacing="0" border="0"><?
									if($opcoes[$categorias[0]]['Texto1'])
									{
									?>
										<tr>
											<td width="130" align="left">Texto 1:</td>
											<td align="left"><textarea class="form-control" type="text" name="resumo" id="resumo" style="width:99%;height:150px;<?=$style_texto1?>"><? echo $resumo;?></textarea></td>
										</tr>
										<tr>
											<td width="130" align="left"></td>
											<td align="left">
												<table cellpadding="0" cellspacing="0" border="0">
												<tr>
													<td align="left">Cor:</td>
													<td><select name="Texto1_cor">
                                                            <option value="" <? if(!$Texto1_cor) echo 'selected'?>>---</option>
                                                            <option value="#DD723C" <? if($Texto1_cor=='#DD723C') echo 'selected'?>>Laranja</option>
                                                            <option value="#868789" <? if($Texto1_cor=='#868789') echo 'selected'?>>Cinza Claro</option>
                                                            <option value="#2D343E" <? if($Texto1_cor=='#2D343E') echo 'selected'?>>Cinza escuro</option>
                                                        </select>
													</td>
													<td>Tamanho (pixels):</td>
													<td><input class="form-control" type="text" name="Texto1_tamanho" id="Texto1_tamanho" value="<? echo $Texto1_tamanho;?>" style="width:50px"/></td>
													<td>Line Height:</td>
													<td><input class="form-control" type="text" name="Texto1_line_height" id="Texto1_line_height" value="<? echo $Texto1_line_height;?>" style="width:50px"/></td>

													<td align="left">Weight:</td>
													<td align="left">
														<select name="Texto1_font_weight">
															<option value="" <? if(!$Texto1_font_weight) echo 'selected'?>>normal</option>
															<option value="bold" <? if($Texto1_font_weight=='bold') echo 'selected'?>>bold</option>
															<option value="bolder" <? if($Texto1_font_weight=='bolder') echo 'selected'?>>bolder</option>
															<option value="lighter" <? if($Texto1_font_weight=='lighter') echo 'selected'?>>lighter</option>
															<option value="100" <? if($Texto1_font_weight=='100') echo 'selected'?>>100</option>
															<option value="200" <? if($Texto1_font_weight=='200') echo 'selected'?>>200</option>
															<option value="300" <? if($Texto1_font_weight=='300') echo 'selected'?>>300</option>
															<option value="400" <? if($Texto1_font_weight=='400') echo 'selected'?>>400</option>
															<option value="500" <? if($Texto1_font_weight=='500') echo 'selected'?>>500</option>
															<option value="600" <? if($Texto1_font_weight=='600') echo 'selected'?>>600</option>
															<option value="700" <? if($Texto1_font_weight=='700') echo 'selected'?>>700</option>
															<option value="800" <? if($Texto1_font_weight=='800') echo 'selected'?>>800</option>
															<option value="900" <? if($Texto1_font_weight=='900') echo 'selected'?>>900</option>
														</select>
													</td>

												</tr>
												</table>
											</td>
										</tr>
										<tr><td colspan="2">&nbsp;</td></tr><?
                                    }
                                    if($opcoes[$categorias[0]]['Texto2'])
                                    {
                                    ?>
										<tr>
											<td width="130" align="left">Texto 2:</td>
											<td align="left"><textarea class="form-control" type="text" name="texto" id="texto" style="width:99%;height:150px;<?=$style_texto2?>"><? echo $texto;?></textarea></td>
										</tr>
										<tr>
											<td width="130" align="left"></td>
											<td align="left">
												<table cellpadding="0" cellspacing="0" border="0">
												<tr>
													<td align="left">Cor:</td>
													<td><select name="Texto2_cor">
                                                            <option value="" <? if(!$Texto2_cor) echo 'selected'?>>---</option>
                                                            <option value="#DD723C" <? if($Texto2_cor=='#DD723C') echo 'selected'?>>Laranja</option>
                                                            <option value="#868789" <? if($Texto2_cor=='#868789') echo 'selected'?>>Cinza Claro</option>
                                                            <option value="#2D343E" <? if($Texto2_cor=='#2D343E') echo 'selected'?>>Cinza escuro</option>
                                                        </select>
													</td>
													<td>Tamanho (pixels):</td>
													<td><input class="form-control" type="text" name="Texto2_tamanho" id="Texto2_tamanho" value="<? echo $Texto2_tamanho;?>" style="width:50px"/></td>
													<td>Line Height:</td>
													<td><input class="form-control" type="text" name="Texto2_line_height" id="Texto2_line_height" value="<? echo $Texto2_line_height;?>" style="width:50px"/></td>

													<td align="left">Weight:</td>
													<td align="left">
														<select name="Texto2_font_weight">
															<option value="" <? if(!$Texto2_font_weight) echo 'selected'?>>normal</option>
															<option value="bold" <? if($Texto2_font_weight=='bold') echo 'selected'?>>bold</option>
															<option value="bolder" <? if($Texto2_font_weight=='bolder') echo 'selected'?>>bolder</option>
															<option value="lighter" <? if($Texto2_font_weight=='lighter') echo 'selected'?>>lighter</option>
															<option value="100" <? if($Texto2_font_weight=='100') echo 'selected'?>>100</option>
															<option value="200" <? if($Texto2_font_weight=='200') echo 'selected'?>>200</option>
															<option value="300" <? if($Texto2_font_weight=='300') echo 'selected'?>>300</option>
															<option value="400" <? if($Texto2_font_weight=='400') echo 'selected'?>>400</option>
															<option value="500" <? if($Texto2_font_weight=='500') echo 'selected'?>>500</option>
															<option value="600" <? if($Texto2_font_weight=='600') echo 'selected'?>>600</option>
															<option value="700" <? if($Texto2_font_weight=='700') echo 'selected'?>>700</option>
															<option value="800" <? if($Texto2_font_weight=='800') echo 'selected'?>>800</option>
															<option value="900" <? if($Texto2_font_weight=='900') echo 'selected'?>>900</option>
														</select>
													</td>

												</tr>
												</table>
											</td>
										</tr>
										<tr><td style="height:5px" colspan="2"></td></tr><?
									}
									if($opcoes[$categorias[0]]['Texto3'])
									{
									?>
										<tr>
											<td width="130" align="left">Texto 3:</td>
											<td align="left"><textarea class="form-control" type="text" name="texto3" id="texto3" style="width:99%;height:150px;<?=$style_texto3?>"><? echo $texto3;?></textarea></td>
										</tr>
										<tr>
											<td width="130" align="left"></td>
											<td align="left">
												<table cellpadding="0" cellspacing="0" border="0">
												<tr>
													<td align="left">Cor:</td>
													<td>
														<select name="Texto3_cor">
															<option value="" <? if(!$Texto3_cor) echo 'selected'?>>---</option>
															<option value="#DD723C" <? if($Texto3_cor=='#DD723C') echo 'selected'?>>Laranja</option>
															<option value="#868789" <? if($Texto3_cor=='#868789') echo 'selected'?>>Cinza Claro</option>
															<option value="#2D343E" <? if($Texto3_cor=='#2D343E') echo 'selected'?>>Cinza escuro</option>
														</select>
													</td>
													<td>Tamanho (pixels):</td>
													<td><input class="form-control" type="text" name="Texto3_tamanho" id="Texto3_tamanho" value="<? echo $Texto3_tamanho;?>" style="width:50px"/></td>
													<td>Line Height:</td>
													<td><input class="form-control" type="text" name="Texto3_line_height" id="Texto3_line_height" value="<? echo $Texto3_line_height;?>" style="width:50px"/></td>
													<td align="left">Weight:</td>
													<td align="left">
														<select name="Texto3_font_weight">
															<option value="" 		<? if(!$Texto3_font_weight) 			echo 'selected'?>>normal</option>
															<option value="bold" 	<? if($Texto3_font_weight=='bold') 		echo 'selected'?>>bold</option>
															<option value="bolder" 	<? if($Texto3_font_weight=='bolder') 	echo 'selected'?>>bolder</option>
															<option value="lighter" <? if($Texto3_font_weight=='lighter') 	echo 'selected'?>>lighter</option>
															<option value="100" 	<? if($Texto3_font_weight=='100') 		echo 'selected'?>>100</option>
															<option value="200" 	<? if($Texto3_font_weight=='200') 		echo 'selected'?>>200</option>
															<option value="300" 	<? if($Texto3_font_weight=='300') 		echo 'selected'?>>300</option>
															<option value="400" 	<? if($Texto3_font_weight=='400') 		echo 'selected'?>>400</option>
															<option value="500" 	<? if($Texto3_font_weight=='500') 		echo 'selected'?>>500</option>
															<option value="600" 	<? if($Texto3_font_weight=='600') 		echo 'selected'?>>600</option>
															<option value="700" 	<? if($Texto3_font_weight=='700') 		echo 'selected'?>>700</option>
															<option value="800" 	<? if($Texto3_font_weight=='800') 		echo 'selected'?>>800</option>
															<option value="900" 	<? if($Texto3_font_weight=='900') 		echo 'selected'?>>900</option>
														</select>
													</td>
												</tr>
												</table>
											</td>
										</tr>
										<tr><td style="height:5px" colspan="2"></td></tr><?
									}?>
									</table>
									<? if($opcoes[$categorias[0]]['Imagem_bg'])
									{?>
                                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td width="130" align="left">Imagem de fundo do Modulo:</td>
                                        <td align="left"><?
                                            if($img_ext && file_exists('../../files/documentos/'.$img_ext))
                                            {
                                                echo '<a class="pretourl_small2 fancybox_img" href="../../files/documentos/'.$img_ext.'" target="_blank" class="url_reno"><img src="../../files/documentos/'.$img_ext.'" height=100 border=0></a> | ';
                                                echo '<a class="preto" href="../edit_image/edit_image.php?w=619&h=609&src=../../files/documentos/'.$img_ext.'&ext=_enc" target="_blank" style="font-weight:bold;font-size:12px">Editar imagem</a><br /><br />';
                                            }?><input type="file" name="img_ext" id="img_ext" />
                                        </td>
                                    </tr>
                                    </table><?
                                    }
									if($opcoes[$categorias[0]]['Imagens'])require_once 'tab_imagens.php';?>
								</div>




	<div class="clearboth"></div>
	<div id="slidebottom">
    <table width="100%" border=0 cellspacing=0 cellpadding=0>
    <tr>
    	<td width="5px">
    	<td align="center" valign="middle" class="t1"><?
				echo '<input class="botao" type="submit" name="Submit" value=" '.$task_button.' ">';
			?>
            <input class="botao" type="button" name="cancelar" value=" FECHAR " onMouseUp="window.close()" />
		</td>
		<td width="5px"></td>
     </tr>
	</table>
	</div>

							</td>
						</tr>
						</table>
					</td>
				</tr>
				</table>
			</form>
		</div>
		<script src="chosen.jquery.min.js" type="text/javascript"></script>
		<script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true}); </script><br /><br /><?
	}
	else
	{
		echo show_alerta('Não tem permissões para aceder a este móludo!','erro');
	}
	?>