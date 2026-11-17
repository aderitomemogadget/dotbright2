<?
include "settings.inc.php";
global $task,$draft_id,$total_prods;
if($task=="apagar_draft"){$task_button=write_lang('key_apagar_este_draft','Apagar este Draft');$task_form='apaga';}
elseif($task=='apagar'){$task_button=write_lang('key_apagar_este_registo','Apagar este Registo');$task_form='apaga';}
elseif($task=='duplicar'){$task_button=write_lang('key_duplicar_este_registo','Duplicar este Registo');$task_form='duplicar';}
elseif($ID==0){$task_button=write_lang('key_gravar_o_novo_registo','Gravar o Novo Registo');$task_form='insere';}
elseif($draft_id) {$task_button=write_lang('key_gravar_draft','Gravar Draft');$task_form='altera';}
else {$task_button=write_lang('key_gravar_alteracoes','Gravar alteraçőes');$task_form='altera';}

if(trim($Nivel)=="") $Nivel=0;
include "../layout/header.inc.php";
include "../layout/body.inc.php";
global $DB_host,$DB_user,$DB_pass,$DB_name;
$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
$rs=new DataObj;$rs2=new DataObj;

// if(!$caminho_bt_editor){$caminho_bt_editor1="../../../editor/";$caminho_bt_editor2="../editor/";}
// else{$caminho_bt_editor1=$caminho_bt_editor2=$caminho_bt_editor;}

$C="";$T="";$I1="";$I2="";$I3="";$I4="";
$ordem=0;
if($ID!=0)
{

	$rs->db_open($db_link,$DB_name, "Select * from portfolio3 where ID=$ID order by Ordem Desc");
	if($rs)
	{
		$ID_Fam=$rs->db_data["ID_ORG"];
		$Ref=ltrim(rtrim($rs->db_data["Ref"]));
		$ordem=$rs->db_data["Ordem"];
		$I1=$rs->db_data["img_ext"];
		$I2=$rs->db_data["img2_ext"];
		$I3=$rs->db_data["img3_ext"];
		$I4=$rs->db_data["img4_ext"];
		$ordem=$rs->db_data["Ordem"];
		$preco1=$rs->db_data["Preco1"];
		$PrecoCusto=$rs->db_data["PrecoCusto"];
		$preco2=$rs->db_data["Preco2"];
		$preco_prec=$rs->db_data["Preco_prec"];
		$precopromo1=$rs->db_data["PrecoPromo1"];
		$margem=$rs->db_data["Margem"];
		$margem_activa=$rs->db_data["MargemActiva"];
		$destaque=$rs->db_data["Destaque"];
		$decumentario=$rs->db_data["decumentario"];
		$novidade=$rs->db_data["Novidade"];
		$promocao=$rs->db_data["Promo"];
		$home=$rs->db_data["Home"];
		$m_marca=$rs->db_data["ID_Marca"];
		$saldo=$rs->db_data["Saldo"];
		$aproved=$rs->db_data["Aproved"];
		$tipo=$rs->db_data["Tipo"];
		$strHottags=$rs->db_data["Hottags"];
		$peso=$rs->db_data["Peso"];
		$medidas=$rs->db_data["Medidas"];
		$stock_txt=$rs->db_data["StockTxt"];
		$stock=$rs->db_data["Qtd"];
		$precopromo2=$rs->db_data["PrecoPromo2"];
		$usado=$rs->db_data["usado"];
		$disponivelencomenda=$rs->db_data["DisponivelEncomenda"];
		$alertastocks=$rs->db_data["AlertaStocks"];
		$download=$rs->db_data["Download"];
	}

	//Vamos buscar as areas
	$rs->db_open($db_link,$DB_name, "Select Areas from portfolio3_families_lang where ID_PAI=$ID_ORG AND lang='$admin_lang'");
	$Areas=$rs->db_data["Areas"];

	//********* linguas ***//
	$rs->db_open($db_link,$DB_name, "Select * from portfolio3_lang where ID_PAI=$ID and lang='$admin_lang'");
	$video_id=$rs->db_data["ID"];
	$areas1=$rs->db_data["Areas1"];
	$pais1=$rs->db_data["Pais1"];
	$ano1=$rs->db_data["Ano1"];

	require_once("Encoding.php");
	$C =  htmlentities_memo(stripslashes($rs->db_data["Cabecalho"]));
	$T = limpa_editor_lixo(stripslashes($rs->db_data["Texto1"]));
	$T2 = iconv("UTF-8", "UTF-8//IGNORE",limpa_editor_lixo(stripslashes($rs->db_data["Texto2"])));
	$T3 = iconv("UTF-8", "UTF-8//IGNORE",limpa_editor_lixo(stripslashes($rs->db_data["Texto3"])));
	$T4 = iconv("UTF-8", "UTF-8//IGNORE",limpa_editor_lixo(stripslashes($rs->db_data["Texto4"])));
	$T5 = iconv("UTF-8", "UTF-8//IGNORE",limpa_editor_lixo(stripslashes($rs->db_data["Tamanho"])));
	$T6 = iconv("UTF-8", "UTF-8//IGNORE",limpa_editor_lixo(stripslashes($rs->db_data["Cor"])));

	// if($cor > 0)
	// {
	// 	$rs3=new DataObj;
	// 	$sql = "Select video_title from video_menager_link where video_id=$cor";
	// 	$rs3->db_open($db_link,$DB_name,$sql);
	// 	$video_title = $rs3->db_data["video_title"];
	// 	$rs3->db_close();
	// }

	$rs->db_close();
}
else $ID_Fam=$ID_ORG;
?>

<script type="text/javascript" src="../editor/ckeditor_4_5_1/ckeditor.js"></script>
<script type="text/javascript" src="../editor/ckeditor_4_5_1/ckfinder/ckfinder.js"></script>
<!--script language=JavaScript src='../editor/scripts/innovaeditor.js'></script-->
<script language="JavaScript">
	if(document.getElementById)
	{
		document.write('<style type="text/css">\n')
		document.write('.submenu{display: none;}\n')
		document.write('</style>\n')
	}
	function showmenu_prod(obj,obj2){
		document.form.tab_a.value=obj;
		document.form.tab_b.value=obj2;
		if(document.getElementById){

			var el=document.getElementById(obj);
			var el2=document.getElementById(obj2);

			var ar=document.getElementById("masterdiv").getElementsByTagName("div");
			var ar2=document.getElementById("masterdiv").getElementsByTagName("td");

			if(el.style.display !="block"){
				for (var i=0; i<ar.length; i++){
					v="sub"+(i+1);
					if(ar[i].className=="submenu") ar[i].style.display="none";
				}

				for (var i=0; i<ar2.length; i++){
					v="sub"+(i+1);
					if(ar2[i].className=="branco") ar2[i].style.background="#5694B4";
				}
				el.style.display="block";
				el2.style.background="#000000";
			}
		}

	}
	window.onload = function(){<? if(isset($_GET['t1']) && isset($_GET['t2'])){?>showmenu_prod('<? echo $t1?>','<? echo $t2?>');<? }else{?>showmenu_prod('sub1','pinta1');<? }?>}

	$(function(){ $('.result').hide();});

	function get_cs_user()
	{
		var t = $('.result').is(':visible');

		if(t==false)
		{
			$('#choose').html('<img src="del2.png" border="0" title="close box" />');
			$.get('../pages/get_youtube.php?dp=<? echo date("YmdHis")?>', function(data) {  $('.result').show("slow"); $('.result').html(data);});
		}
		else
		{
			$('#choose').html('<img src="config.png" border="0"  title="choose a video" />');
			$('.result').hide("slow");
		}
	}
	function totext(div)
	{
		$('#m_cliente_url').val($('#'+div).val());
		$('#cor').val($('#_'+div).val());
		$('#choose').html('<img src="config.png" border="0"  title="choose a video" />');
		$('.result').hide("slow");
	}
	function search()
	{
		$.get('../pages/get_youtube.php?dp=<? echo date("YmdHis")?>&pesquisa='+$('#search_by').val(), function(data) { $('.result').html(data);});
	}
	function empty()
	{
		$('#m_cliente_url').val("");
		$('#cor').val(0);
		$('#choose').html('<img src="config.png" border="0"  title="choose a video" />');
		$('.result').hide("slow");
	}

	function mudaStock(valor){
		$("input[name='obj_stock[]']").val(valor);
	}
</script>
<form name="form" method="post" action="portfolio3_trata.php?ID=<? echo $ID?>&ID_ORG=<? echo $ID_Fam?>&task_form=<? echo $task_form?>&dump=<? echo uniqID("")?>" enctype="multipart/form-data">
	<input type="hidden" name="m_lang" value="<? echo $admin_lang;?>">
	<input type="hidden" name="slashes" value="'">
	<input type="hidden" name="tab_a" value="0">
	<input type="hidden" name="tab_b" value="0">

	<div class="center">
		<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
			<tr>
				<td valign="top" align="left"><? include "../layout/menus.inc.php"; ?>
					<table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
						<tr>
							<td valign="top">

								<table width="100%" height="30px" border=0 cellspacing=0 cellpadding="5" class="titulo_janela">
				                <tr>
				                    <td><a href="list.php" class="new_caminho"><? echo write_lang('key_inicio','Início')?></a> - <? show_portfolio3_menu_path($ID_Fam, "portfolio3_families.php", "portfolio3_families",$total_prods);?></td>
				                </tr>
				                </table>

								<div id="masterdiv"><br />
									<table height="20" border=0 cellspacing=0 cellpadding=0>
										<tr>
											<td width="120" bgcolor="#000000" align="center" class="branco" onClick="showmenu_prod('sub1','pinta1')" style="cursor:pointer;background:#000" id="pinta1">RESUMO</td><td><img src="cm_fill.gif" width="8" height="1"></td>
											<td width="220" bgcolor="#5694B4" align="center" class="branco" onClick="showmenu_prod('sub2','pinta2')" style="cursor:pointer" id="pinta2"><? echo write_lang('key_imagens_ficheiros','IMAGENS/FICHEIROS')?></td>
											<td><img src="cm_fill.gif" width="8" height="1"></td>
											<!-- <td width="120" bgcolor="#5694B4" align="center" class="branco" onClick="showmenu_prod('sub3','pinta3')" style="cursor:pointer" id="pinta3"><? echo write_lang('key_caracteristicas','CARACTERÍSTICAS')?></td><td><img src="cm_fill.gif" width="8" height="1"></td>
											<td><img src="cm_fill.gif" width="8" height="1"></td> -->
											<!-- <td width="120" bgcolor="#5694B4" align="center" class="branco" onClick="showmenu_prod('sub6','pinta6')" style="cursor:pointer" id="pinta5">GALERIA</td> -->
										</tr>
									</table>

									<div style="position:relative; width: 100%; display:block;" class="submenu" id="sub1">
										<table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="F6F6F6" class="border1">
											<tr>
												<td valign="top">

													<table width="100%" border=0 cellpadding="2" cellspacing=0>
														<tr>
															<td class="preto" width="120"><? echo write_lang('key_ordem','Ordem')?>:</td>
															<td class="preto" align="left" valign="top">
																<table border=0 cellspacing=0 cellpadding=0>
																	<tr>
																		<td width="100"><input name="ordem" style="WIDTH:50px; HEIGHT:20px" maxlength="5" value="<? echo $ordem;?>"></td>
																		<td class="preto"><? /*if($task_button=="Alterar"){*/?><? echo write_lang('key_categoria','Categoria')?>: <? gera_lista_familias($ID_Fam,"portfolio3_families","portfolio3_families_lang");//}?></td>
																	</tr>
																</table>
															</td>
														</tr>
														<tr>
															<td class="preto"><? echo write_lang('key_nome','Nome')?>:</td>
															<td class="preto" align="left" valign="top"><input class="form-control" type="text" name="cabecalho" style="WIDTH:100%" maxlength="255" value="<? echo $C;?>"></td>
														</tr>
						                                    <td class="preto">Breve descrição:</td>
						                                    <td class="preto"><textarea style="height: 100px" name="texto1"><?=$T?></textarea></td>
						                                </tr>
						                                <tr>
						                                    <td class="preto">Áreas de Negócio:</td>
						                                    <td class="preto"><?

						                                    $i=0;
						                                    $array_areas_geral=explode(';',$Areas);
						                                    $array_areas_produto=explode(';',$areas1);

						                                    $t=count($array_areas_geral);
						                                    while($i<$t)
						                                    {
						                                    	if (in_array($array_areas_geral[$i],$array_areas_produto))echo '<input name="areas1[]" checked type="checkbox" value="'.$array_areas_geral[$i].'">';
						                                    	else echo '<input name="areas1[]" type="checkbox" value="'.$array_areas_geral[$i].'">';

						                                    	echo '&nbsp'.$array_areas_geral[$i].'&nbsp&nbsp';
						                                    	$i++;
						                                    }

						                                    ?></td>
						                                </tr>
						                                <tr>
						                                    <td class="preto">País:</td>
						                                    <td class="preto"><input type="text" name="pais1" value='<?=$pais1?>'></td>
						                                </tr>
						                                <tr>
						                                    <td class="preto">Ano de conclusão:</td>
						                                    <td class="preto"><input type="text" name="ano1" value='<?=$ano1?>'></td>
						                                </tr>
														</tr>
														<tr>
						                                    <td class="preto"> <? echo write_lang('key_destaque','Destaque')?>:</td>
						                                    <td class="preto"> <input type="checkbox" name="destaque" value=1 <? if($destaque) echo "checked";?>>&nbsp;<? echo write_lang('key_sim','Sim')?></td>
						                                </tr>
														<tr>
															<td class="preto"><? echo write_lang('key_activar_produto','Activar Produto')?>:</td>
															<td class="preto"> <input type=checkbox name="aproved" value=1 <? if($aproved) echo "checked";?>>&nbsp;<? echo write_lang('key_sim','Sim')?></td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
									</div>
									<div style="position:relative; width: 100%; display:none;" class="submenu" id="sub2">
										<table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="F6F6F6" class="border1">
											<tr>
												<td valign="top">
													<table width="100%" border=0 cellpadding="2" cellspacing=0>
														<tr>
															<td class="preto" width="200">Imagem Principal</td>
															<td class="preto" align="left" valign="top"><?
																if($I1!="")
																{?><div><img style="max-width:200px" src="<? echo "../../files/portfolio3/$ID"."_1.".$I1;?>"></div><?
																	if($task!="apagar")
																	{
																		?>Remove <input type='checkbox' name='Apagar_img1' value='1' /> or Change <input type='file' name='img_peq1'> <input type='hidden' name='tem_foto1' value='1' /><?

																		echo '<a class="preto" href="../edit_image/edit_image.php?w=320&h=213&src=../../files/portfolio3/'.$ID.'_1.'.$I1.'&ext=_enc" target="_blank" style="font-weight:bold;font-size:12px">Editar imagem</a><br /><br />';

																	}

																}
																else{?><input type=file name='img_peq1'> <input type='hidden' name='tem_foto1' value='0' /><? }?><br></td>





														</tr>
														<tr>
															<td class="preto">Imagem 2</td>
															<td class="preto" align="left" valign="top"><?
																if($I2!="")
																{?><div><img style="max-width:200px" src="<? echo "../../files/portfolio3/".$ID."_2.".$I2;?>"></div><?
																	if($task!="apagar"){?>Remove <input type='checkbox' name='Apagar_img2' value='1' /> or Change <input type='file' name='img_peq2'> <input type='hidden' name='tem_foto2' value='1' /><? }
																}
																else{?><input type=file name='img_peq2' /> <input type='hidden' name='tem_foto2' value='0' /><? }?><br></td>
														</tr>
														<tr>
															<td class="preto">Imagem 3</td>
															<td class="preto" align="left" valign="top"><?
																if($I3!="")
																{?><div><img style="max-width:200px" src="<? echo "../../files/portfolio3/".$ID."_3.".$I3;?>"></div><?
																	if($task!="apagar"){?>Remove <input type='checkbox' name='Apagar_img3' value='1' /> or Change <input type='file' name='img_peq3'> <input type='hidden' name='tem_foto3' value='1' /><? }
																}
																else{?><input type=file name='img_peq3' /> <input type='hidden' name='tem_foto3' value='0' /><? }?><br></td>
														</tr>
														<tr>
															<td class="preto">Imagem 4</td>
															<td class="preto" align="left" valign="top"><?
																if($I4!="")
																{?><div><img style="max-width:200px" src="<? echo "../../files/portfolio3/".$ID."_4.".$I4;?>"></div><?
																	if($task!="apagar"){?>Remove <input type='checkbox' name='Apagar_img4' value='1' /> or Change <input type='file' name='img_peq4'> <input type='hidden' name='tem_foto4' value='1' /><? }
																}
																else{?><input type=file name='img_peq4' /> <input type='hidden' name='tem_foto4' value='0' /><? }?><br></td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
									</div><!--
									<div style="position:relative; width: 100%; display:none;" class="submenu" id="sub3">
										<table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="F6F6F6" class="border1">
											<tr>
												<td valign="top">

													<table width="100%" border=0 cellpadding="2" cellspacing=0>
														<tr>
															<td class="preto">Descrição:</td>
														</tr>
														<tr>
															<td class="preto"><textarea name="texto1" id="texto1" style="WIDTH:100%; HEIGHT:100px"><? echo $T;?></textarea><? //show_editor("oEdit1","texto1","100%",250)?></td>
														</tr>
														<tr>
															<td class="preto">Texto da popup:</td>
														</tr>
														<tr>
															<td class="preto"><textarea name="texto3" id="texto3" style="WIDTH:100%; HEIGHT:100px"><?php echo $T3;?></textarea><?php show_editor("oEdit3","texto3","100%",250)?></td>
														</tr>
														<tr>
															<td class="preto">Dados do Fabricante:</td>
														</tr>
														<tr>
															<td class="preto"><textarea name="texto4" id="texto4" style="WIDTH:100%; HEIGHT:100px"><? echo $T4;?></textarea><? show_editor("oEdit4","texto4","100%",250)?></td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
									</div> -->
								</div>
								<div style="position:relative; width: 100%; display:<? if($t1=="sub6") echo 'block'; else echo 'none';?>;" class="submenu" id="sub6">
									<table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
										<tr>
											<td valign="top">
												<table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="F6F6F6" class="border1">
													<tr>
														<td valign="top"><iframe id="ifrm_master" name="ifrm_master" scrolling="auto" marginwidth="0" marginheight="0" frameborder="0" vspace="0" hspace="0" style="overflow:visible; width:100%; height:600px; display:yes; z-index:1">[Content for browsers that don't support iframes goes here]</iframe><script>var currentfr=document.getElementById("ifrm_master");</script></td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</div>
							</td>
						</tr>
					</table>
					<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
					<table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
						<tr>
							<td align="center">
								<input class="btn btn-primary" type="submit" name="Submit" value=" <?=$task_button;?> ">
								<input class="btn btn-info" type="button" name="cancelar" value=" Pré-visualizar " onclick="javascript:window.open('<?php echo $GLOBALS['site_http_url'].'pages.php?page_id='.$GLOBALS['portfolio3_page_id'].'&prod_id='.$ID.'&site_prev=1';?>','_blank');">
								<input class="btn btn-secondary" type="button" name="cancelar" value=" Cancelar " onMouseUp="javascript:history.go(-1);">
							</td>
						</tr>
					</table>
					<img src="../images/bsh.png" width="100%">
				</td>
				<td width="10px">&nbsp;</td>
				<td valign="top" width="200px"><? show_fast_tools();?><? show_product_tools();?></td>
			</tr>
		</table>
	</div>
</form>
<script type="text/javascript">
	function _body_onload()
	{
		available_height=document.body.clientHeight-20;
		var currentfr=document.getElementById("ifrm_master");
		currentfr.height=available_height+24;<?
		echo "currentfr.src = 'portfolio3_tecidos.php?ID=$ID'";
		?>
	}
	_body_onload();
</script>
</body>
</html>