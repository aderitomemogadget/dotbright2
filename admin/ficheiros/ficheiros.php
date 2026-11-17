<?php
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
global $DB_host,$DB_user,$DB_pass,$DB_name, $dev;
$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
$rs=new DataObj;$rs2=new DataObj;


$C="";$T="";
/*$I1="";$I2="";$I3="";$I4="";*/
$ordem=0;
if($ID!=0)
{

	$rs->db_open($db_link,$DB_name, "Select * from ficheiros where ID=$ID");
	if($rs)
	{
		$ID_Fam=$rs->db_data["ID_ORG"];
		$Ref=ltrim(rtrim($rs->db_data["Ref"]));
		$ordem=$rs->db_data["Ordem"];
		/*$I1=$rs->db_data["img_ext"];
		$I2=$rs->db_data["img2_ext"];
		$I3=$rs->db_data["img3_ext"];
		$I4=$rs->db_data["img4_ext"];*/
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
		$Grupo_ID = $rs->db_data["Grupo_ID"];
	}

	//Vamos buscar as areas
	$rs->db_open($db_link,$DB_name, "Select Areas from ficheiros_pasta_lang where ID_PAI=$ID_ORG AND lang='$admin_lang'");
	$Areas=$rs->db_data["Areas"];
	$rs->db_close();

	//********* linguas ***//
	$rs->db_open($db_link,$DB_name, "Select * from ficheiros_lang where ID_PAI=$ID and lang='$admin_lang'");
	$video_id=$rs->db_data["ID"];
	$areas1=$rs->db_data["Areas1"];
	$pais1=$rs->db_data["Pais1"];
	$ano1=$rs->db_data["Ano1"];
	$SEO_title=stripslashes($rs->db_data['SEO_title']);
	$SEO_description=stripslashes($rs->db_data['SEO_description']);

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

	function adicionarNovaImagem()
	{
		var next=parseInt($("#next_img_number").val());
		var str='<tr><td class="preto">Imagem '+next+'</td><td class="preto" align="left" valign="top"><input type="file" name="img_peq['+next+']"> <input type="hidden" name="tem_foto['+next+']" value="0" /></td></tr>';
		$("#list_images").append(str);
		$("#next_img_number").val(next+1);
	}

</script>
<form name="form" method="post" action="ficheiros_trata.php?ID=<? echo $ID?>&ID_ORG=<? echo $ID_Fam?>&task_form=<? echo $task_form?>&dump=<? echo uniqID("")?>" enctype="multipart/form-data">
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
				                    <td><a href="list.php" class="new_caminho"><? echo write_lang('key_inicio','Início')?></a> - <? show_ficheiros_menu_path($ID_Fam, "ficheiros_pasta.php", "ficheiros_pasta",$total_prods);?></td>
				                </tr>
				                </table>

								<div id="masterdiv"><br />
									<table height="20" border=0 cellspacing=0 cellpadding=0>
										<tr>
											<td width="120" bgcolor="#000000" align="center" class="branco" onClick="showmenu_prod('sub1','pinta1')" style="cursor:pointer;background:#000" id="pinta1">DADOS DE FICHEIRO</td><td><img src="cm_fill.gif" width="8" height="1"></td>
											<td><img src="cm_fill.gif" width="8" height="1"></td>
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
																		<td class="preto">Pasta: <? gera_lista_familias($Grupo_ID,"ficheiros_pasta","ficheiros_pasta_lang");?></td>
																		<td>&nbsp;&nbsp;</td>
																		<td>Visível para:</td>
                                                        <td style="padding: 0px 0px 0px 10px;min-width:30%;"><? show_lista($Grupo_ID,"grupos_clientes","Grupo_ID");?>&nbsp;</td>
                                                        <td></td>
																	</tr>
																</table>
															</td>
														</tr>
														<tr>
															<td class="preto"><? echo write_lang('key_nome','Nome')?>:</td>
															<td class="preto" align="left" valign="top"><input type="text" name="cabecalho" style="WIDTH:100%; HEIGHT:20px" maxlength="255" value="<? echo $C;?>"></td>
														</tr>
														<tr>
						                                    <td class="preto">Breve descrição:</td>
						                                    <td class="preto"><textarea style="height: 100px" name="texto1"><?=$T?></textarea><?php show_editor("oEdit3","texto1","100%",250)?></td>
						                                </tr>

						                                <tr>
						                                    <td class="preto"></td>
						                                    <td class="preto"></td>
						                                </tr>
						                                 <tr>
						                                    <td class="preto"></td>
						                                    <td class="preto">

						                                    	<table width="100%" border="0" cellpadding="2" cellspacing="0" id="list_images">
                                <tr><td class="preto" style="width:150px"></td><td class="preto"></td></tr>
                                <?php
                                	if($ID) $arr_imagens=getImagensProduto($ID);
                                	$tam=count($arr_imagens);

									if($tam>0)
									{

										$next_img_number=$arr_imagens[($tam-1)]['Ordem']+1;
	                                	$input=1;
	                                	for ($i=0; $i < $tam; $i++)
	                                	{
	                                		$ordem=$arr_imagens[$i]['Ordem'];

	                                		if($ordem==$input)
	                                		{
	                                			$n_ordem=$ordem;
	                                			if($ordem==1) $n_ordem=' ';
	                                			echo '<tr>';
		                                		echo '<td class="preto">Imagem/ficheiro '.$n_ordem;
		                                		echo '<a href="../../files/ficheiros/'.$ID.'_'.$ordem.'.'.$arr_imagens[$i]['img_ext'].'?dp='.date("YmdHis").'" download>';
		                                		echo '<p style="font-size:14px;">Download &#128279; ';
		                                		echo ' '.$arr_imagens[$i]['img_ext'].'</p>';
		                                		echo '</a>';
		                                		echo '</td>';
		                                		echo '<td class="preto" align="left" valign="top">';
		                                		echo '<div class="imagens_400">';
		                                		$displaytool = 0;
		                                		$path = '../../files/ficheiros/'.$ID.'_'.$ordem.'.'.$arr_imagens[$i]['img_ext'].'?dp='.date("YmdHis");
		                                		$r_img = $arr_imagens[$i]['img_ext'] == 'png' || $arr_imagens[$i]['img_ext'] == 'jpg' || $arr_imagens[$i]['img_ext'] == 'JPG';
		                                		if($r_img){
		                                		echo '<img style="width:auto;height:90%;"  src="'.$path.'">';
		                                		}else{
		                                			$displaytool = 1;
		                                		}

		                                		if($displaytool && $arr_imagens[$i]['img_ext'] == 'pdf'){

		                                				echo '<object style="width:582px;height:295px" data="'.$path.'#zoom=65?dp='.date("YmdHis").'" type="application/pdf"><embed src="your_url_to_pdf" type="application/pdf" /></object>';


		                                		}

		                                		echo '</div>';

		                                        if($task!="apagar"){
		                                        	echo 'Remove <input type="checkbox" name="Apagar_img['.$ordem.']" value="1" /> or Change <input type="file" name="img_peq['.$ordem.']"> <input type="hidden" name="tem_foto['.$ordem.']" value="1" />';
		                                        }
		                                        echo '<br></td>';
		                                		echo '</tr>';
		                                		$input++;
	                                		}
	                                		else
	                                		{
	                                			if($ordem > $input)
	                                			{
		                                			while ($ordem!=$input) {
		                                				echo '<tr>';
					                                	echo '<td class="preto">Imagem '.($input).'</td>';
					                                	echo '<td class="preto" align="left" valign="top">';
					                                	echo '<input type="file" name="img_peq['.($input).']"> <input type="hidden" name="tem_foto['.($input).']" value="0" />';
					                                    echo '</td>';
					                                	echo '</tr>';
					                                	$input++;
		                                			}
	                                			}
	                                			else $input++;
	                                			echo '<tr>';
		                                		echo '<td class="preto">Imagem '.$ordem.'</td>';
		                                		echo '<td class="preto" align="left" valign="top">';
		                                		echo '<div class="imagens_400"><img src="../../files/ficheiros/'.$ID.'_'.$ordem.'.'.$arr_imagens[$i]['img_ext'].'?dp='.date("YmdHis").'?>"></div>';

		                                        if($task!="apagar"){
		                                        	echo 'Remove <input type="checkbox" name="Apagar_img['.$ordem.']" value="1" /> or Change <input type="file" name="img_peq['.$ordem.']"> <input type="hidden" name="tem_foto['.$ordem.']" value="1" />';
		                                        }
		                                        echo '<br></td>';
		                                		echo '</tr>';
		                                		$input++;
	                                		}
	                                	}
	                                }
									else
									{
	                                	$next_img_number=$tam+2;
	                                	echo '<tr>';
	                                	echo '<td class="preto">Imagem/ficheiro</td>';
	                                	echo '<td class="preto" align="left" valign="top">';
	                                	echo '<input type="file" name="img_peq[1]"> <input type="hidden" name="tem_foto[1]" value="0" />';
	                                    echo '</td>';
	                                	echo '</tr>';
	                                }

                                ?>
                                </table>
						                                    </td>
						                                </tr>
						                                 <tr>
						                                    <td class="preto"></td>
						                                    <td class="preto"></td>
						                                </tr>
														<tr>
															<td class="preto">Activar:</td>
															<td class="preto"> <input type=checkbox name="aproved" value=1 <? if($aproved) echo "checked";?>>&nbsp;<? echo write_lang('key_sim','Sim')?></td>
														</tr>
														<tr>
															<td class="preto"></td>
															<td class="preto">
																&nbsp;
															</td>
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
                               </td>
                              </tr>
                              </table>
									</div>
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
							<td>&nbsp;<input class="btn btn-primary" type="submit" name="Submit" value=" <?=$task_button;?> "> <input class="btn btn-primary" type="button" name="cancelar" value=" Cancelar " onMouseUp="javascript:history.go(-1);"> <input class="btn btn-primary" type="button" name="cancelar" value=" Pré-visualizar " onclick="javascript:window.open('<?php echo $GLOBALS['site_http_url'].'pages.php?page_id='.$GLOBALS['products_page_id'].'&prod_id='.$ID.'&site_prev=1';?>','_blank');"></td>
						</tr>
					</table>
					<img src="../images/bsh.png" width="100%">
				</td>
				<td width="10px">&nbsp;</td>
				<td valign="top" width="220px"><? show_fast_tools();?></td>
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
		echo "currentfr.src = 'ficheiros_tecidos.php?ID=$ID'";
		?>
	}
	_body_onload();
</script>
</body>
</html>