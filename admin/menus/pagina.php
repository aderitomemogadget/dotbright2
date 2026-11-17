<?php
	include "settings.inc.php";
	if($task=="apagar"){$task_button = write_lang('key_apagar_este_registo','Apagar este Registo');$task_form="apaga";}
	elseif($task=="duplicar"){$task_button = write_lang('key_duplicar_este_registo','Duplicar este Registo');$task_form="duplicar";}
	elseif($ID==0){$task_button = write_lang('key_gravar_novo_registo','Gravar Novo Registo');$task_form="insere";}
	else {$task_button = write_lang('key_gravar_alteracoes','Gravar alterações');$task_form="altera";}
	$D=getdate();

	if($ID!=0)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$tem_loja,$tem_area_reservada;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

		$fields = db_list_fields($DB_name, 'menus_links', $db_link);
		$columns = db_num_fields($fields);
		for ($i=0; $i < $columns; $i++) {$field_array[]=db_field_name($fields,$i);}
		if (!in_array('novajanela',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE menus_links ADD novajanela tinyint(1) DEFAULT 0');}
		if (!in_array('Indexar',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE menus_links ADD Indexar tinyint(1) DEFAULT 0');}
		if (!in_array('catalogo_id',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE menus_links ADD catalogo_id int(11)');}
		if (!in_array('TipoMenu',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE menus_links ADD TipoMenu varchar(255)');}
		if (!in_array('Logado',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE menus_links ADD Logado tinyint(1)');}
		if (!in_array('video',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE menus_links ADD video text');}
		if (!in_array('media',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE menus_links ADD media varchar(255)');}
		if (!in_array('img2_ext',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE menus_links ADD img2_ext varchar(4)');}
		if (!in_array('tipo_catalogo',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE menus_links ADD tipo_catalogo varchar(200)');}
		if (!in_array('class',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE menus_links ADD class varchar(255)');}


		$rs=new DataObj;
		$sql="Select * from menus_links WHERE ID=$ID order by Ordem Asc";
		$db_res=$rs->db_open($db_link,$DB_name,$sql);
		if($rs)
		{
			$datacriacao=$rs->db_data["Data"];
			$ordem=$rs->db_data["Ordem"];
			$visivel=$rs->db_data["Visivel"];
			$tipo=$rs->db_data["Tipo"];
			$ID_ORG=$rs->db_data["ID_ORG"];
			$url_link=$rs->db_data["URL_Link"];
			$url_nome=$rs->db_data["URL_Nome"];
			$I1=$rs->db_data["img_ext"];
			$I2=$rs->db_data["img2_ext"];
			$I3=$rs->db_data["img3_ext"];
			$largura=$rs->db_data["Largura"];
			$altura=$rs->db_data["Altura"];
			$template_id=$rs->db_data["template_id"];
			$novajanela=$rs->db_data["novajanela"];
			$catalogo_id=$rs->db_data["catalogo_id"];
			$TipoMenu=$rs->db_data["TipoMenu"];
			$Indexar=$rs->db_data["Indexar"];
			$Logado=$rs->db_data["Logado"];
			$video=$rs->db_data["video"];
			$media=$rs->db_data["media"];
			$tipo_catalogo=$rs->db_data["tipo_catalogo"];
			$class=$rs->db_data["class"];
			$menu_icon_fa=$rs->db_data["menu_icon_fa"];

			$rs2=new DataObj;
			$sql="Select * from menus_links_lang where ID_PAI=$ID and lang='$admin_lang' order by ID Desc";
			$db_res=$rs2->db_open($db_link,$DB_name,$sql);
			$url_nome2=stripslashes($rs2->db_data["URL_Nome2"]);
			$texto_icon_fa=stripslashes($rs2->db_data["texto_icon_fa"]);
			$rs2->db_close();

			if($catalogo_id > 0 && $tipo_catalogo == 'familias')
			{
				$rs3=new DataObj;
				$sql='Select Cabecalho from products_families_lang where ID_PAI="'.$catalogo_id.'" and lang="'.$admin_lang.'"';
				$rs3->db_open($db_link,$DB_name,$sql);
				$nome_fam = stripslashes($rs3->db_data["Cabecalho"]);
				$rs3->db_close();
			}
			elseif($catalogo_id > 0 && $tipo_catalogo == 'marcas')
			{
				$rs3=new DataObj;
				$sql='Select Nome from brands_lang where ID_PAI="'.$catalogo_id.'" and lang="'.$admin_lang.'"';
				$rs3->db_open($db_link,$DB_name,$sql);
				$nome_fam = stripslashes($rs3->db_data["Nome"]);
				$rs3->db_close();
			}
		}
		$rs->db_close(1);
	}



	if($translate_to)
	{
		$lang1='pt';
		$lang2b = $translate_to;
		if($lang2=='ho')$lang2b='nl';

		$arr_query = array(
		'key' => 'trnsl.1.1.20160826T172445Z.ad542005a80e547d.9ff6f4f7d599e4951974daafc13d5dd10bd89613',
		'text' => ($url_nome2),
		'format' => 'html',
		'lang' => "$lang1-$lang2b");
		$new_str = file_get_contents('https://translate.yandex.net/api/v1.5/tr.json/translate?'.http_build_query($arr_query));
		$obj = json_decode($new_str);
		$array_obj = $obj->{'text'};
		$url_nome2=$array_obj[0];

		$arr_query = array(
		'key' => 'trnsl.1.1.20160826T172445Z.ad542005a80e547d.9ff6f4f7d599e4951974daafc13d5dd10bd89613',
		'text' => ($nome_fam),
		'format' => 'html',
		'lang' => "$lang1-$lang2b");
		$new_str = file_get_contents('https://translate.yandex.net/api/v1.5/tr.json/translate?'.http_build_query($arr_query));
		$obj = json_decode($new_str);
		$array_obj = $obj->{'text'};
		$nome_fam=$array_obj[0];
	}


	include "../layout/header.inc.php";
	include "../layout/body.inc.php";
?>
<link rel="stylesheet" type="text/css" href="chosen/sa.css" />
<link rel="stylesheet" type="text/css" href="chosen/chosen.css?dp=<?php echo dumpacache?>" />

<script type="text/javascript">
$(document).ready(function()
{
	$('.fancybox_img').fancybox();
	$('.fancybox2').on('click', function(){ var href = $(this).attr('href'); $.fancybox({ 'showCloseButton' : false, 'titlePosition' : 'inside', title : true, autoSize : false, modal : false, fitToView : false, content : '<iframe width="100%" height="100%" src="'+href+'"></iframe>', width : 400, maxHeight : 600, scrolling : 'no', type : 'html', onComplete: function() {} }); return false;});
});

	if(document.getElementById){document.write('<style type="text/css">\n');document.write('.subm{display: none;}\n');document.write('</style>\n');}
	function SwitchMenu(obj){if(document.getElementById){var el=document.getElementById(obj);var ar=document.getElementById("md").getElementsByTagName("span");if(el.style.display!="block"){for(var i=0;i<ar.length;i++){if(ar[i].className=="subm")ar[i].style.display="none";}el.style.display="block";}else el.style.display="none";}}
	function select_page()
	{
		var editdoc=document.form.url_link;
		var newurl=showModalDialog("listcontent.php", "", "resizable: no; help: no; status: no; scroll: yes;");
		if(newurl) editdoc.value=newurl;
	}
	function select_page2()
	{
		var editdoc = document.form.catalogo_id;
		var editdoc2 = document.form.catalogo_nome;
		var newurl = showModalDialog("listcontent2.php", "", "resizable: no; help: no; status: no; scroll: yes;");
		var brokenstring = newurl.split(";");

		if(brokenstring[0]) editdoc.value=brokenstring[0];
		if(brokenstring[1]) editdoc2.value=brokenstring[1];
	}

</script>
	<div class="center">
		<form name="form" method="post" action="pagina_trata.php?prev=0&ID=<?php echo $ID?>&ID_ORG=<?php echo $ID_ORG?>&task_form=<?php echo $task_form?>&dump=<?php echo uniqID("")?>" enctype="multipart/form-data">
			<input type="hidden" name="m_lang" value="<?php echo $admin_lang;?>" />
			<input type="hidden" name="slashes" value="'" />
            <input type="hidden" name="tipo" value="<?php echo $tipo?>" />
			<table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td valign="top" align="left"><? include "../layout/menus.inc.php"; ?>
                	<div class="panel panel-inverse overflow-hidden m-b-12">
				        <div class="panel-heading">
				            <h4 style="color:#fff">MENUS</h4>
				        </div>
				    </div>
					<table class="new_quadros" width="100%" border="0" cellspacing="0" align="center" style="background:#ECF3FD">
					<tr>
						<td valign="top" style="padding:4px">
							<table width="100%" height="30px" border="0" cellspacing="0" cellpadding="5" class="titulo_janela">
							<tr>
								<td width="100"><?php echo write_lang('key_menu_visivel','Menu Visível')?>:&nbsp;<input type="checkbox" name="visivel" value="1" <? if($visivel) echo "checked"; ?>></td>
                                <td align="right"><?php if($ID){?><?php echo write_lang('key_criado','Criado')?>:&nbsp;<?php echo $datacriacao?><? }?></td>
							</tr>
							</table>
                            <table width="100%" border="0" cellspacing="0" cellpadding="5" class="subtitulo_janela">
							<tr>
								<td valign="middle"><h2 style="color:#FFF"><?php echo show_menupath($ID_ORG);?></h2></td>
							</tr>
							</table>
						</td>
					</tr>
                    <tr>
						<td style="padding:10px" align="left" valign="top" class="preto">

						<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td width="200" class="preto"><?php echo write_lang('key_menu_pai','Menu Pai')?>:</td>
							<td><?php show_options($ID_ORG,0,"menus_links","menus_links_lang");?></td>
						</tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
						<tr><td colspan="2">&nbsp;</td></tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td width="200" class="preto">Visivel para:</td>
							<td><?php show_options_visivel($Logado);?></td>
						</tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
						<tr><td colspan="2">&nbsp;</td></tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td width="200" class="preto"><?php echo write_lang('key_ordem','Ordem')?>:&nbsp;</td>
							<td><input type="text" name="ordem" style="WIDTH: 58px; " maxlength="5" value="<?php echo $ordem;?>" class="form-control" /></td>
						</tr>
						</table><table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
						<tr><td colspan="2">&nbsp;</td></tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td width="200" class="preto"> Class:&nbsp;</td>
							<td><input type="text" name="class" style="width:100%" maxlength="255" value="<?php echo $class;?>" class="form-control"></td>
						</tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
						<tr><td colspan="2">&nbsp;</td></tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td width="200" class="preto"> <?php echo write_lang('key_descreva_o_link','Descreva o link')?>:&nbsp;</td>
							<td><input type="text" name="url_nome" style="width:100%" maxlength="255" value="" class="form-control"><script>document.form.url_nome.value="<?php echo str_replace('"','\"',$url_nome);?>";</script></td>
						</tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
						<tr><td colspan="2">&nbsp;</td></tr>
						</table>

						<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td width="200" class="preto">Icon:</td>
							<td align="left" class="preto">
								<?php
								if($I3!="" && file_exists("../../files/menus/".$ID."_3.".$I3))
								{
									if(file_exists("../../files/menus/$ID"."_3.".$I3)){?><img style="max-width:50px;max-height:50px" alt="" src="<?php echo "../../files/menus/$ID"."_3.".$I3.'?d='.uniqid('');?>"><? }
									if($task!="apagar"){?><?php echo write_lang('key_apagar','Apagar')?> <input type='checkbox' name='Apagar_img3' value='1'> <?php echo write_lang('key_ou','ou')?> <?php echo write_lang('key_alterar','Alterar')?> <input type='file' name='img_peq3' /> <input type='hidden' name='tem_foto3' value='1'><? }
								}
								else{?><input type="file" name='img_peq3' /> <input type='hidden' name='tem_foto3' value='0'><? }?></td>
						</tr>
						</table>


						<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td width="200" class="preto"> <?php echo write_lang('key_nome_que_deve_aparecer_no_site','Nome que deve aparecer no site')?>:&nbsp;</td>
							<td><input type="text" name="url_nome2" style="width:100%" maxlength="255" value="" class="form-control"><script>document.form.url_nome2.value="<?php echo str_replace('"','\"',$url_nome2);?>";</script></td>
						</tr>
						</table>

						<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td width="200" class="preto"> OU <?php echo write_lang('key_usar_imagem','Usar Imagem')?>:</td>
							<td align="left" class="preto">
								<?php
								if($I1!="" && file_exists("../../files/menus/".$ID."_1.".$I1))
								{
									if(file_exists("../../files/menus/$ID"."_1.".$I1)){?><div style="overflow:auto;max-width:600px;max-height:300px"><img alt="" src="<?php echo "../../files/menus/$ID"."_1.".$I1.'?d='.uniqid('');?>"></div><? }
									if($task!="apagar"){?><?php echo write_lang('key_apagar','Apagar')?> <input type='checkbox' name='Apagar_img1' value='1'> <?php echo write_lang('key_ou','ou')?> <?php echo write_lang('key_alterar','Alterar')?> <input type='file' name='img_peq1' /> <input type='hidden' name='tem_foto1' value='1'><? }
								}
								else{?><input type="file" name='img_peq1' /> <input type='hidden' name='tem_foto1' value='0'><? }?></td>
						</tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td width="200" class="preto"> <?php echo write_lang('key_usar_imagem','Usar Imagem')?> Hover:</td>
							<td align="left" class="preto">
								<?php
								if($I2!='' && file_exists('../../files/menus/'.$ID.'_2.'.$I2))
								{
									if(file_exists("../../files/menus/".$ID."_2.".$I2)){?><div style="overflow:auto;max-width:600px;max-height:300px"><img alt="" src="<?php echo "../../files/menus/$ID"."_2.".$I2.'?d='.uniqid('');?>"></div><? }
									if($task!="apagar"){?><?php echo write_lang('key_apagar','Apagar')?> <input type='checkbox' name='Apagar_img2' value='1'> <?php echo write_lang('key_ou','ou')?> <?php echo write_lang('key_alterar','Alterar')?> <input type='file' name='img_peq2' /> <input type='hidden' name='tem_foto2' value='1'><? }
								}
								else{?><input type="file" name='img_peq2' /> <input type='hidden' name='tem_foto2' value='0'><? }?>
							</td>
						</tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td width="200" class="preto"> OU <?php echo write_lang('key_usar_icon','Usar Icon')?>:
								<br>
								<br>
								<a href="icons.php?field=menu_icon_fa&field2=fontaweimg" class="preto fancybox2" style="font-size:16px; ">  	&#128279;  <?php echo write_lang('key_procurar','Procurar')?></a>
							</td>
							<td align="left" class="preto"><input type="hidden" name="menu_icon_fa" id="menu_icon_fa" value="<?=$menu_icon_fa?>" >
								<?php if($menu_icon_fa!="")
								{
									echo '<div  id="fontaweimg" style="display:inline-block;">';
									echo '<i class="'.$menu_icon_fa.' fa-3x" ></i>';
									echo ' &nbsp;&nbsp;&nbsp; <input type="hidden" name="texto_icon_fa"  value="'.$texto_icon_fa.'" >';
									echo '</div>';
									echo '&nbsp;&nbsp;&nbsp;Apagar? <input type="checkbox" onclick="this.value=this.value==1?0:1" name="apagar_fa_icon">';
								}
								else
								{?><div id="fontaweimg" style="display:inline-block"><input type="hidden" name="texto_icon_fa" value=""></div>
								<?php }  ?>
						    </td>
						</tr>
						</table>


						<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
						<tr><td colspan="2">&nbsp;</td></tr>
						</table>

						<table width="91%" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td width="200" class="preto"> Link:&nbsp;</td>
							<td><input type="text" name="url_link" id="url_link" style="WIDTH: 99%; " maxlength="255" value="<?php echo $url_link?>" class="form-control"></td>
							<td width="55" height="26" align="center"><a href="listcontent.php?field=url_link" class="preto fancybox2"><?php echo write_lang('key_procurar','Procurar')?></a></td>
						</tr>
						</table>

						<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td width="200" class="preto"> Abrir link em nova janela:&nbsp;</td>
							<td><input type="checkbox" name="novajanela" value="1" <? if($novajanela) echo "checked"; ?>></td>
							<td width="5" height="26">&nbsp;</td>
						</tr>
						</table>

						<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td width="200" class="preto"> Permitir indexar o link:&nbsp;</td>
							<td><input type="checkbox" name="Indexar" value="1" <? if($Indexar) echo "checked"; ?>> (rel="nofollow")</td>
							<td width="5" height="26">&nbsp;</td>
						</tr>
						</table>

						<?php if($tem_loja) { ?>
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
						<tr><td colspan="2">&nbsp;</td></tr>
						</table>

						<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td width="200" class="preto"> Catálogo de Produtos:&nbsp;</td>
							<td>
								<table>
								<tr>
									<td>
										<input style="width: 50px;float:left" type="text" name="catalogo_id" id="catalogo_id" class="form-control" value="<?php echo $catalogo_id?>" />
										<input style="width: 350px;margin-right: 10px" type="text" name="catalogo_nome" readonly="" id="catalogo_nome" class="form-control" value="<?php echo $nome_fam?>" />
										<input type="hidden" name="tipo_catalogo" id="tipo_catalogo" class="form-control" value="<?php echo $tipo_catalogo?>" />
									</td>
									<td height="26">
										<a href="listcontent2.php?field=catalogo_nome;catalogo_id;tipo_catalogo" class="preto fancybox2"><?php echo write_lang('key_procurar','Procurar')?></a> |
										<a href="javascript:;" class="preto" onclick="$('#catalogo_id').val('');$('#catalogo_nome').val('');$('#tipo_catalogo').val('');">Limpar</a>
									</td>
								</tr>
								</table>
							</td>
							<td width="5" height="26">&nbsp;</td>
						</tr>
						</table>
						<?php } //tem_loja ?>
						<?php if(isset($GLOBALS['menus_conf']) && is_array($GLOBALS['menus_conf']) && count($GLOBALS['menus_conf']['codigo']) > 0){?>
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td width="200" class="preto">Estilo do Item de Menu:&nbsp;</td>
							<td>
								<table>
								<tr>
									<td>
										<select name="TipoMenu" id="TipoMenu" class="form-control">
										<?php for($a=0; $a<count($GLOBALS['menus_conf']['codigo']); $a++){?>
										<option value="<?php echo $GLOBALS['menus_conf']['codigo'][$a];?>" <?php if($GLOBALS['menus_conf']['codigo'][$a]==$TipoMenu){ echo 'selected="selected"';} ?>><?php echo $GLOBALS['menus_conf']['nome'][$a]; ?></option>
										<?php } ?>
										</select>
									</td>
									<td height="26">&nbsp;</td>
								</tr>
								</table>
							</td>
							<td width="5" height="26">&nbsp;</td>
						</tr>
						</table>
						<?php } ?>


						<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
						<tr><td colspan="2">&nbsp;</td></tr>
						</table>

						<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td width="200" class="preto"> Colunas p/linha:</td>
							<td align="left" class="preto" style="white-space:nowrap;line-height:30px"><input type="text" name="largura" style="width:100px;float:left" maxlength="255" value="<?php echo $largura?>" class="form-control"> (colspan)</td>
						</tr>
						</table>


								
							</td>
						</tr>
						</table>

						<?php show_page_buttons();?>

						<?php if(isset($_GET['ID']) && $_GET['ID'] > 0 && $_GET['task']!='duplicar')
						{
						?>
						<table class="p1" width="100%" cellpadding=0 cellspacing=0 border=0 style="background:#FFF;border-top:#a1a1a1 1px solid">
						<tr>
							<td width="5" height="26">&nbsp;</td>
							<td width="200" class="new_preto"><b>SUBMENU:</b></td>
							<td width="100" align="right" class="new_preto"><input class="btn btn-primary" type="button" onclick="javascript:window.open('pagina.php?ID=0&ID_ORG=<?php echo $_GET['ID']?>&Dump=<?php echo date('YmdHis');?>','_self')" value="Create New Sub-Menu link"></td>
							<td width="2" align="right" class="new_preto">&nbsp;</td>
						</tr>
						</table>
						<br />
						<?php
							show_content_list($_GET['ID'],-5,0,0,0,1);

						}
						?>
                </td>
				<td width="10px">&nbsp;</td>
				<td valign="top" width="200"><?php show_fast_tools();show_translate_tools();?></td>
            </tr>
            </table>
		</form>
	</div>
<script src="chosen/chosen.jquery.min.js" type="text/javascript"></script>
<script type="text/javascript"> $(".chzn-select").chosen({disable_search_threshold: 10, no_results_text: "Oops, nada encontrado!"}); $(".chzn-select-deselect").chosen({allow_single_deselect:true}); </script>
<?php include "../layout/bottom.inc.php";?>