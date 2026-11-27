<?php
	include "settings.inc.php";
	require_once 'filtros/funcoes.inc.php';

	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(0);

	$top_search=$_POST['top_search'];
	$search2=$_POST['pesquisa2'];

	if($_POST['pesquisa']){$top_search=$_POST['pesquisa'];$_POST['pesquisa']='';}
	if($_POST['pesquisa2']){$search2=$_POST['pesquisa2'];$_POST['pesquisa2']='';}
	if($_POST['pesquisa3']){$search3=$_POST['pesquisa3'];$_POST['pesquisa3']='';}

	$fam_por_produto=$_POST['fam_por_produto'];

	if($task=="apagar_draft"){$task_button=write_lang('key_apagar_este_draft','Apagar este Draft');$task_form='apaga';}
	elseif($task=='apagar'){$task_button=write_lang('key_apagar_este_registo','Apagar este Registo');$task_form='apaga';}
	elseif($task=='duplicar'){$task_button=write_lang('key_duplicar_este_registo','Duplicar este Registo');$task_form='duplicar';}
	elseif($ID==0){$task_button=write_lang('key_gravar_o_novo_registo','Gravar o Novo Registo');$task_form='insere';}
	elseif($draft_id) {$task_button=write_lang('key_gravar_draft','Gravar Draft');$task_form='altera';}
	else {$task_button=write_lang('key_gravar_alteracoes','Gravar alteraçőes');$task_form='altera';}

	// CONFIGURACAO EM SETTINGS
	if (defined('FAM_CONF')) $fam_conf = unserialize(FAM_CONF); else $fam_conf = array();

	if(trim($Nivel) == '') $Nivel = 0;
	if(trim($total_prods=='')) $total_prods=50;
	if(trim($from_prods=='')) $from_prods=0;
	if(trim($to_prods=='')) $to_prods=$total_prods;
	$limite_from = $from_prods;
	$limite_to = $to_prods;

	include "../layout/header.inc.php";
	include "../layout/body.inc.php";

	global $DB_host,$DB_user,$DB_pass,$DB_name,$n_imagens_familia;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$rs2=new DataObj;
	$rs3=new DataObj;

	if($GLOBALS['update_db']==1)
	{
		$fields = db_list_fields($DB_name,'products_families',$db_link);
		$columns = db_num_fields($fields);
		for ($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
		if(!in_array('preco_revenoff',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_families ADD preco_revenoff tinyint(1) DEFAULT 0');  }
		if(!in_array('Margem',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_families ADD Margem decimal(10,4) DEFAULT 0');  }
		if(!in_array('MargemTipo',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_families ADD MargemTipo tinyint(1) DEFAULT 0');}
		if(!in_array('Portes',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_families ADD Portes decimal(10,4) DEFAULT 0');}
		if(!in_array('points',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_families ADD points varchar(250)');}
		if(!in_array('Margem_formula',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_families ADD Margem_formula varchar(250)');}

		$fields = db_list_fields($DB_name,'products_families_lang',$db_link);
		$columns = db_num_fields($fields);
		for ($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
		if(!in_array('SEO_img',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_families_lang ADD SEO_img varchar (5)');  }
		if(!in_array('ICON_img',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_families_lang ADD ICON_img varchar (5)');  }
		if(!in_array('prod_txt1',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_families_lang ADD prod_txt1 varchar (255)');  }
	}


	// MOVER

	// ATUALIZADO PARA GERAR OS PRODUTOS QUANDO SAO MOVIDOS
	// ESTOU A MANDAR A LINGUA Á MÃO "PT"
	if(isset($_POST['flag2']) && $_POST['flag2'] == 3)
	{
		foreach($_POST as $varName=> $value)
		{
			$dv = $value;
			$str_id = substr($varName,strpos($varName,'_')+1);
			if($str_id > 0 && substr_count($varName,'chk_') > 0)
			{
				$sql = "UPDATE products SET ID_ORG='$m_familia2',Aproved=1 WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql.'<br>';

				$fams = encontraFamiliasRecursiva_x($m_familia2);
				if(isset($fams[0])) $fam_id1 = $fams[0]; else $fam_id1 = $m_familia2;
				if(isset($fams[1])) $fam_id2 = $fams[1]; else $fam_id2 = 0;
				if(isset($fams[2])) $fam_id3 = $fams[2]; else $fam_id3 = 0;
				if(isset($fams[3])) $fam_id4 = $fams[3]; else $fam_id4 = 0;

				//VAMOS BUSCAR A MARCA
				$ID_Marca = 0;
				$rs->db_open($db_link,$DB_name,"SELECT ID_Marca FROM products WHERE ID=$str_id");
				if(!$rs->EOF) $ID_Marca = $rs->db_data['ID_Marca'];
				$rs->db_close();

				$rs3->db_open($db_link,$DB_name,'Select prod_id from filters_products WHERE prod_id="'.$id.'"');
				if($rs3->EOF())
				{
					$sql = 'INSERT INTO filters_products(cat_id,valor_id,prod_id,fam_id1,fam_id2,fam_id3,fam_id4,marca_id,filtros_rel2,FLAG,filtro_active)
							VALUES("0","0","'.$id.'","'.$fam_id1.'","'.$fam_id2.'","'.$fam_id3.'","'.$fam_id4.'","'.$ID_Marca.'","",1,1)';
					$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
				}
				else
				{
					$sql = 'UPDATE filters_products SET fam_id1="'.$fam_id1.'",fam_id2="'.$fam_id2.'",fam_id3="'.$fam_id3.'",fam_id4="'.$fam_id4.'",marca_id="'.$ID_Marca.'" where prod_id = "'.$id.'"';
					$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
				}
				$rs3->db_close();

				$sql="DELETE FROM products_families_obj WHERE ID_PROD=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql.'<br>';

				$sql='INSERT INTO products_families_obj(ID_PROD,ID_ORG) values ("'.$str_id.'","'.$m_familia2.'")';
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql.'<br>';

				$prod_id_on = $str_id;
				//gera_produtos($str_id,$m_familia2,$ID_Marca,1,"pt");
			}
		}
		echo '<script>window.open("products_families.php?ID='.$m_familia2.'","_self")</script>';
		die("a redireccionar...");
	}
	if(isset($_POST['flag2']) && $_POST['flag2'] == 5)
	{
		foreach($_POST as $varName=> $value)
		{
			$dv = $value;
			$str_id = substr($varName,strpos($varName,'_')+1);
			if($str_id > 0 && substr_count($varName,'chk_') > 0)
			{
				$m_familia2=$_POST['m_familia'.$str_id];

				$sql = "UPDATE products SET ID_ORG='$m_familia2',Aproved=1 WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql.'<br>';

				$fams = encontraFamiliasRecursiva_x($m_familia2);
				if(isset($fams[0])) $fam_id1 = $fams[0]; else $fam_id1 = $m_familia2;
				if(isset($fams[1])) $fam_id2 = $fams[1]; else $fam_id2 = 0;
				if(isset($fams[2])) $fam_id3 = $fams[2]; else $fam_id3 = 0;
				if(isset($fams[3])) $fam_id4 = $fams[3]; else $fam_id4 = 0;

				//VAMOS BUSCAR A MARCA
				$ID_Marca = 0;
				$rs->db_open($db_link,$DB_name,"SELECT ID_Marca FROM products WHERE ID=$str_id");
				if(!$rs->EOF) $ID_Marca = $rs->db_data['ID_Marca'];
				$rs->db_close();

				$rs3->db_open($db_link,$DB_name,'Select prod_id from filters_products WHERE prod_id="'.$id.'"');
				if($rs3->EOF())
				{
					$sql = 'INSERT INTO filters_products(cat_id,valor_id,prod_id,fam_id1,fam_id2,fam_id3,fam_id4,marca_id,filtros_rel2,FLAG,filtro_active)
							VALUES("0","0","'.$id.'","'.$fam_id1.'","'.$fam_id2.'","'.$fam_id3.'","'.$fam_id4.'","'.$ID_Marca.'","",1,1)';
					$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
				}
				else
				{
					$sql = 'UPDATE filters_products SET fam_id1="'.$fam_id1.'",fam_id2="'.$fam_id2.'",fam_id3="'.$fam_id3.'",fam_id4="'.$fam_id4.'",marca_id="'.$ID_Marca.'" where prod_id = "'.$id.'"';
					$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
				}
				$rs->db_close();

				$sql="DELETE FROM products_families_obj WHERE ID_PROD=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql.'<br>';

				$sql='INSERT INTO products_families_obj(ID_PROD,ID_ORG) values ("'.$str_id.'","'.$m_familia2.'")';
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql.'<br>';
			}
		}
	}

	if(isset($fam_conf) && $fam_conf['tem_caract'] == 1){?>
	<script type="text/javascript" src="../editor/ckeditor/ckeditor.js"></script>
	<script type="text/javascript" src="../editor/ckeditor/ckfinder/ckfinder.js"></script>
	<?php }?>
	<link rel="stylesheet" type="text/css" href="avancada/chosen2.css" />
	<link rel="stylesheet" type="text/css" href="avancada/sa.css" />
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
	if(document.getElementById)
	{
		var el=document.getElementById(obj);
		var el2=document.getElementById(obj2);
		var ar=document.getElementById("masterdiv").getElementsByTagName("div");
		var ar2=document.getElementById("masterdiv").getElementsByTagName("td");

		if(el.style.display !="block")
		{
			for (var i=0; i<ar.length; i++)
			{
				v="sub"+(i+1);
				 if(ar[i].className=="submenu") ar[i].style.display="none";
			}

			for (var i=0; i<ar2.length; i++)
			{
				v="sub"+(i+1);
				if(ar2[i].className=="branco") ar2[i].style.background="#5694B4";
			}
			el.style.display="block";
			el2.style.background="#000000";
		}
	}
}
window.onload =function(){<? if($t1 && $t2){?>showmenu_prod('<?=$t1?>','<?=$t2?>');<? }else{?>showmenu_prod('sub1','pinta1');<? }?>}
</script>
<div class="center">
<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
<tr>
<td align="left" valign="top"><?php
	include "../layout/menus.inc.php"; 

	if(!$top_search && !$search2 && !$search3)
	{
		$C = '';$T = '';$I1 = '';$I2 = '';
		$ordem=0;
		if(isset($_GET['ID']) && $_GET['ID'] != 0)
		{
			$ID = $_GET['ID'];

			$rs->db_open($db_link,$DB_name,'SELECT * FROM products_families WHERE ID='.$ID.' ORDER BY Ordem DESC');
			if(!$rs->EOF())
			{
				$D=str2date($rs->db_data["Data"]);
				$I1=$rs->db_data["img_ext"];
				$I2=$rs->db_data["img2_ext"];
				$I3=$rs->db_data["img3_ext"];
				$ordem=$rs->db_data["Ordem"];
				$id_org=$rs->db_data["ID_ORG"];
				$cor=$rs->db_data["cor"];
				$visivel=$rs->db_data["visivel"];
				$destaque=$rs->db_data["Destaque"];
				$margemcli=$rs->db_data["MargemCli"];
				$margemrev=$rs->db_data["MargemRev"];
				$Margem_formula=$rs->db_data["Margem_formula"];
				$cat1=$rs->db_data["cat1"];
				$cat2=$rs->db_data["cat2"];
				$cat3=$rs->db_data["cat3"];
				$download=$rs->db_data["Download"];
				$ID_Media=$rs->db_data["ID_Media"];
				$Desconto=number_format($rs->db_data["Desconto"],2);
				$Margem=number_format($rs->db_data["Margem"],2);
				$DescontoTipo=$rs->db_data["DescontoTipo"];
				$MargemTipo=$rs->db_data["MargemTipo"];
				$ID_Integracao=$rs->db_data["ID_Integracao"];
				$preco_revenoff=$rs->db_data["preco_revenoff"];
				$Portes=number_format($rs->db_data["Portes"],2);
				$pontos=$rs->db_data["points"];
			}
			$rs->db_close();

			//********* linguas ***/
			$rs->db_open($db_link,$DB_name,"Select * from products_families_lang where ID_PAI=$ID and lang='$admin_lang'");
			$C=htmlentities_memo(stripslashes($rs->db_data["Cabecalho"]));
			$C2=htmlentities_memo(stripslashes($rs->db_data["Cabecalho2"]));
			$T=stripslashes($rs->db_data["Texto"]);
			$URL1=$rs->db_data["URL"];
			$SEO_img = $rs->db_data["SEO_img"];
			$ICON_img = $rs->db_data["ICON_img"];
			$prod_txt1 = $rs->db_data["prod_txt1"];
			$rs->db_close();
		}

		if($translate_to)
		{
			$lang1='pt';
			$lang2b = $translate_to;
			if($lang2=='ho')$lang2b='nl';

			if(!$C2) $C2 = $C;

			$arr_query = array(
			'key' => 'trnsl.1.1.20160826T172445Z.ad542005a80e547d.9ff6f4f7d599e4951974daafc13d5dd10bd89613',
			'text' => ($C2),
			'format' => 'html',
			'lang' => "$lang1-$lang2b");
			$new_str = file_get_contents('https://translate.yandex.net/api/v1.5/tr.json/translate?'.http_build_query($arr_query));
			$obj = json_decode($new_str);
			$array_obj = $obj->{'text'};
			$C2=$array_obj[0];
		}
		// SE EXISTIR id_org do query tem que ser passado para o select box
		if(isset($id_org) && $id_org > 0) $ID_ORG = $id_org;
		elseif($_GET['ID']==$_GET['ID_ORG']) $ID_ORG = $id_org;
		?>
		<form class="db-family-editor" name="form" method="post" action="products_families_trata.php?prev=0&ID=<?=$ID?>&ID_ORG=<?=$ID_ORG?>&task_form=<?=$task_form?>&dump=<?php echo uniqID("")?>" enctype="multipart/form-data" accept-charset="UTF-8">
		<input type="hidden" name="m_lang" value="<?=$admin_lang;?>" />
		<input type="hidden" name="slashes" value="'" />
		<input type="hidden" name="tab_a" value="0" />
		<input type="hidden" name="tab_b" value="0" />
		<input name="Nivel" type="hidden" id="Nivel" value="<?=$Nivel;?>" />
		<div class="panel panel-inverse overflow-hidden m-b-12">
	        <div class="panel-heading">
	            <h4 style="color:#fff">PRODUTOS</h4>
	        </div>
	    </div>
		<table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
		<tr>
			<td>
				<table width="100%" height="30px" border=0 cellspacing=0 cellpadding="5" class="titulo_janela">
				<tr>
					<td><a href="list.php" class="new_caminho"><? echo write_lang('key_inicio','Ínicio')?></a> - <? show_products_menu_path($ID,"products_families.php","products_families",$total_prods);?></td>
				</tr>
				</table>
				<div id="masterdiv">
				<br />
				<table height="20" border=0 cellspacing=0 cellpadding=0>
				<tr>
				<td width="120" bgcolor="#6699CC" align="center" class="branco" onClick="showmenu_prod('sub1','pinta1')" style="border-radius: 5px 5px 0px 0px;cursor:pointer" id="pinta1"><? echo write_lang('key_categoria','CATEGORIA')?></td>
				<?php
				// IMAGEM DA FAMILIA
				if(isset($fam_conf) && $fam_conf['tem_imagens']){?>
				<td><img src="cm_fill.gif" width="8" height="1"></td>
				<td width="220" bgcolor="#6699CC" align="center" class="branco" onClick="showmenu_prod('sub2','pinta2')" style="border-radius: 5px 5px 0px 0px;cursor:pointer" id="pinta2"><? echo write_lang('key_imagens','IMAGENS')?></td>
				<?php }

				// CARACTERISTICAS DA FAMILIA
				if(isset($fam_conf) && $fam_conf['tem_caract']){?>
				<td><img src="cm_fill.gif" width="8" height="1"></td>
				<td width="120" bgcolor="#6699CC" align="center" class="branco" onClick="showmenu_prod('sub3','pinta3')" style="border-radius: 5px 5px 0px 0px;cursor:pointer" id="pinta3"><? echo write_lang('key_caracteriticas','CARACTERÍSTICAS')?></td><td><img src="cm_fill.gif" width="8" height="1"></td>
				<?php }?>

				<td><img src="cm_fill.gif" width="8" height="1"></td>
				<td width="120" bgcolor="#6699CC" align="center" class="branco" onClick="showmenu_prod('sub4','pinta4')" style="border-radius: 5px 5px 0px 0px;cursor:pointer" id="pinta4">Margens</td><td><img src="cm_fill.gif" width="8" height="1"></td>
				</tr>

				</table>

				<div style="position:relative; width: 100%; display:none;" class="submenu" id="sub1">

				<table width="100%" border=0 cellpadding="2" cellspacing=0 bgcolor="#F6F6F6" class="border1 db-family-fields">
				<tr>
					<td>&nbsp;</td>
					<td></td>
					<td></td>
					<td>&nbsp;</td>
				</tr>

				<tr>
					<td>&nbsp;</td>
					<td class="preto">ATIVAR</td>
					<td align="left" class="preto"><label class="db-family-check"><input name="visivel" type="checkbox" value="1" <?php if($visivel) echo ' checked="checked"'; ?> /> <? echo write_lang('key_tornar_categoria_visivel','Tornar categoria visível')?></label></td>
					<td>&nbsp;</td>
				</tr>

				<?php  if($ID_ORG == 0 && $task_form == 'altera'){?>
				<tr><td style="border-bottom:#c0c0c0 1px solid;height:1px;font-size:1px" colspan="10"></td></tr>
				<tr><td colspan="10">&nbsp;</td></tr>
				<tr>
				<td>&nbsp;</td>
				<td>REPARAÇÕES</td>
				<td align="left" class="preto"><label class="db-family-check"><input name="download" type="checkbox" value="1" <?php if($download) echo ' checked="checked"'; ?>> Família das reparações</label></td>
				<td>&nbsp;</td>
				</tr>

				<?php 
				} 
				?>
				<tr><td style="border-bottom:#c0c0c0 1px solid;height:1px;font-size:1px" colspan="10"></td></tr>
				<tr><td colspan="10">&nbsp;</td></tr>
				<tr>
				<td width="10">&nbsp;</td>
				<td class="preto" width="100"><? echo write_lang('key_ordem','Ordem')?>:</td>
				<td align="left" class="preto"><input class="form-control" name="ordem" style="width:80px;" maxlength="5" value="<?=$ordem;?>" /></td>
				<td width="4">&nbsp;</td>
				</tr>

				<tr>
				<td>&nbsp;</td>
				<td class="preto">Familia:</td>
				<td align="left"><?php gera_lista_familias($ID_ORG,'products_families','products_families_lang');?></td>
				<td>&nbsp;</td>
				</tr>

				<?php
				// NA LISTAGEM DA FAMILA APARECE O MEDIA
				if(isset($fam_conf) && $fam_conf['tem_banner'] == 1 && $ID_ORG==0){?>
				<tr>
				<td>&nbsp;</td>
				<td class="preto">Banner:</td>
				<td align="left"><?php gera_lista_medias($ID_Media,"media_type");?></td>
				<td>&nbsp;</td>
				</tr>
				<?php } ?>

				<tr>
				<td>&nbsp;</td>
				<td class="preto"><? echo write_lang('key_nome','Nome')?>:</td>
				<td align="left"><? 

					if ($ID_Integracao > 0)
					{

						echo '<div class="db-family-name"><input class="form-control" type="text" name="cabecalho" readonly="readonly" maxlength="255" value="'.$C.'" /><span class="db-family-integration">SAGE ID = '.$ID_Integracao.'</span></div>';
					}
					else echo '<input class="form-control" type="text" name="cabecalho" style="width:100%" maxlength="255" value="'.$C.'" />';

					?>
				</td>
				<td>&nbsp;</td>
				</tr>

				<?php
				// NA IMPORTAÇÃO É BLOQUEADO O TITULO E ESTE DEVE SER ATIVADO
				if(isset($fam_conf) && $fam_conf['tem_n_alterna'] == 1){?>
				<tr>
				<td>&nbsp;</td>
				<td class="preto"><? echo write_lang('key_nome','Nome')?> Alternativo:</td>
				<td align="left"><input class="form-control" type="text" name="cabecalho2" style="width:100%;" maxlength="255" value="<?=$C2;?>" /></td>
				<td>&nbsp;</td>
				</tr>
				<?php }

				// NA IMPORTAÇÃO EM VEZ DE TRAZER O PREÇO DE REVENDA TRAZ O PRECO FINAL
				if(isset($fam_conf) && $fam_conf['tem_p_c_final'] == 1){?>
				<tr>
				<td>&nbsp;</td>
				<td></td>
				<td class="preto"><input type="checkbox" name="preco_revenoff" <?php if($preco_revenoff){?> checked="checked" <? }?> value="1" /> Buscar Preço Cliente Final</td>
				<td class="preto" align="left" valign="top"></td>
				</tr>
				<?php }

				// DESTAQUES POR FAMILIAS NA HOMEPAGE
				//if(isset($fam_conf) && $fam_conf['tem_fams_destaque'] == 1){?>

				<?php // } ?>

				<tr>
					<td>&nbsp;</td>
					<td class="preto">Texto ficha artigo:</td>
					<td align="left"><input class="form-control" type="text" name="prod_txt1" style="width:100%;" maxlength="255" value="<?=$prod_txt1;?>" /></td>
					<td>&nbsp;</td>
				</tr>

				<tr>
				<td>&nbsp;</td>
				<td></td>
				<td></td>
				<td>&nbsp;</td>
				</tr>

				<?php
				// BLOCO APRESENTADO APENAS SE FOR UPDATE
				if(isset($_GET['ID']) && $_GET['ID'] > 0)
				{
					if(isset($fam_conf) && $fam_conf['tem_bonus'] == 1)
					{
						if(strpos($pontos,"%") != false)
						{
							$select1 = 'selected="selected"';
							$select2 = '';
							$pontos = str_replace("%","",$pontos);
						}
						else
						{
							$select2 = 'selected="selected"';
							$select1 = '';
							$pontos = str_replace("€","",$pontos);
						}
					?>
					<tr><td style="border-bottom:#c0c0c0 1px solid;height:1px;font-size:1px" colspan="10"></td></tr>
					<tr bgcolor="#EAEAEA"><td colspan="10">&nbsp;</td></tr>

					<tr bgcolor="#EAEAEA">
					<td width="10">&nbsp;</td>
					<td class="preto" width="50">Bonus:</td>
					<td align="left" class="preto"><input class="form-control" name="pontos" style="width:80px" maxlength="5" value="<?=$pontos;?>" /></td>
					<td width="4">&nbsp;</td>
					</tr>

					<tr bgcolor="#EAEAEA">
					<td width="10">&nbsp;</td>
					<td class="preto" width="50">Ação:</td>
					<td align="left" class="preto">
					<select name="tpoints" id="tpoints" class="form-control4" style="width:80px">
					<option value="€" <? echo $select2; ?>>€</option>
					<option value="%" <? echo $select1; ?>>%</option>
					</select>
					</td>
					<td width="4">&nbsp;</td>
					</tr>

					<?php
					}

					// MARGEM POR FAMILIA
					if(isset($fam_conf) && $fam_conf['tem_desconto'] == 1)
					{
					?>
					<tr bgcolor="#EAEAEA"><td style="border-bottom:#c0c0c0 1px solid" colspan="10">&nbsp;</td></tr>
					<tr bgcolor="#EAEAEA"><td colspan="10">&nbsp;</td></tr>
					<tr bgcolor="#EAEAEA">
						<td width="10">&nbsp;</td>
						<td class="preto" width="50">Desconto(%):</td>
						<td align="left" class="preto"><input class="form-control" name="Desconto" style="width:80px" maxlength="5" value="<?=$Desconto;?>" /></td>
						<td width="4">&nbsp;</td>
					</tr>
					<tr bgcolor="#EAEAEA">
						<td width="10">&nbsp;</td>
						<td class="preto" width="50">Ação:</td>
						<td align="left" class="preto">
							<select name="DescontoTipo" id="DescontoTipo" class="form-control4">
								<option value="0" <?php if($DescontoTipo==0 || $Desconto == 0){?>selected="selected"<?php }?>>Não aplicar</option>
								<option value="1" <?php if($DescontoTipo==1 && $Desconto > 0){?>selected="selected"<?php }?>>Aplicar a subfamilias/produtos</option>
								<option value="2" <?php if($DescontoTipo==2 && $Desconto > 0){?>selected="selected"<?php }?>>Aplicar a subfamilias/produtos sem promoção</option>
								<option value="3" <?php if($DescontoTipo==3 && $Desconto > 0){?>selected="selected"<?php }?>>Aplicar a subfamilias/produtos com promoção</option>
							</select>
						</td>
						<td width="4">&nbsp;</td>
					</tr>
					<?php
					}
				}
				?>
				<tr bgcolor="#EAEAEA"><td colspan="10">&nbsp;</td></tr>
				</table>
				</div>

				<div style="position:relative; width: 100%; display:none;" class="submenu" id="sub4">
					<table width="100%" border=0 cellpadding="2" cellspacing=0 bgcolor="F6F6F6" class="border1">
					<tr>
						<td>&nbsp;</td>
						<td></td>
						<td></td>
						<td>&nbsp;</td>
					</tr>
					<?php
					// MARGEM POR FAMILIA
					if(isset($fam_conf) && $fam_conf['tem_margem'] == 1)
					{
					?>
					<tr bgcolor="#EAEAEA"><td style="border-bottom:#c0c0c0 1px solid" colspan="10">&nbsp;</td></tr>
					<tr bgcolor="#EAEAEA"><td colspan="10">&nbsp;</td></tr>
					<tr bgcolor="#EAEAEA">
						<td width="10">&nbsp;</td>
						<td class="preto" width="200">Margem:</td>
						<td class="preto" align="left"><input class="form-control" name="margemcli" style="width:80px" maxlength="5" value="<?=$margemcli;?>" /></td>
						<td width="150" colspan="9">
							<select name="MargemTipo" id="MargemTipo">
								<option value="0" <?php if($MargemTipo==0){?>selected="selected"<?php }?>>%</option>
								<option value="1" <?php if($MargemTipo==1){?>selected="selected"<?php }?>>valor</option>
							</select>
						</td>
					</tr>
					<tr bgcolor="#EAEAEA"><td style="border-bottom:#c0c0c0 1px solid" colspan="10">&nbsp;</td></tr>
					<tr bgcolor="#EAEAEA"><td colspan="10">&nbsp;</td></tr>
					<tr bgcolor="#EAEAEA">
						<td width="10">&nbsp;</td>
						<td class="preto" width="200">Ou Formula</td>
						<td class="preto" align="left"><textarea class="form-control" name="Margem_formula" style="width:400px"><?=$Margem_formula;?></textarea></td>
						<td class="preto" align="left" colspan="9" style="width:400px">
								<?php echo htmlentities("if(PVP > 0 && PVP < 1000) echo 10;")."<br>".htmlentities("if(PVP > 1000 && PVP < 2000) echo 20;");?>
						</td>

					</tr>
					<?php
					}
					// PORTES POR FAMILIA
					if(isset($fam_conf) && $fam_conf['tem_portes'] == 1)
					{
					?>
					<tr bgcolor="#EAEAEA"><td style="border-bottom:#c0c0c0 1px solid" colspan="10">&nbsp;</td></tr>
					<tr bgcolor="#EAEAEA"><td colspan="10">&nbsp;</td></tr>
					<tr bgcolor="#EAEAEA">
						<td width="10">&nbsp;</td>
						<td class="preto"  width="50">Portes(€):</td>
						<td align="left" class="preto"><input class="form-control" name="Portes" style="width:80px" maxlength="5" value="<?=$Portes;?>" ></td>
						<td width="4">&nbsp;</td>
					</tr>
					<?php
					}?>
					<tr bgcolor="#EAEAEA"><td colspan="10">&nbsp;</td></tr>
					</table>
				</div>
				<?php
				// IMAGEM DA FAMILIA
				if(isset($fam_conf) && $fam_conf['tem_imagens'])
				{
				?>
					<div style="position:relative; width: 100%; display:none;" class="submenu" id="sub2">
					<table width="100%" border=0 cellpadding="2" cellspacing=0 bgcolor="F6F6F6" class="border1">
					<tr>
						<td>&nbsp;</td>
						<td></td>
						<td></td>
						<td>&nbsp;</td>
					</tr>
					<?php
					if($ICON_img!='')
					{
					?>
					<tr>
						<td>&nbsp;</td>
						<td colspan="2">
						<div class="imagens_400"><img src="<? echo "../../files/products_families/".$ID."_ICON_".$admin_lang.".".$ICON_img;?>" alt="" border="0" /></div><br />
						<?php
						if($task != "apagar")
						{
							echo write_lang('key_apagar','Apagar');?>
							<input type="checkbox" name="Apagar_ICON_img" value="1">
							<?php echo write_lang('key_ou','ou'); echo write_lang('key_alterar','Alterar')?> <input type="file" name="ICON_img" /><input type="hidden" name="tem_ICON_img" value="1" /><?php
						}?>
						</td>
						<td>&nbsp;</td>
					</tr><?php
					}
					else
					{?>
					<tr>
						<td>&nbsp;</td>
						<td colspan="2"><?php
							echo write_lang('key_nova','Nova');?> ICON : <input type="file" name="ICON_img" /><input type="hidden" name="tem_ICON_img" value="1" />
						</td>
						<td>&nbsp;</td>
					</tr><?php
					}?>
					<?php
					if($SEO_img!='')
					{
					?>
					<tr>
						<td>&nbsp;</td>
						<td colspan="2">
						<div class="imagens_400"><img src="<? echo "../../files/products_families/".$ID."_seo_".$admin_lang.".".$SEO_img;?>" alt="" border="0" /></div><br />
						<?php
						if($task != "apagar")
						{
							echo write_lang('key_apagar','Apagar');?>
							<input type="checkbox" name="Apagar_SEO_img" value="1">
							<?php echo write_lang('key_ou','ou'); echo write_lang('key_alterar','Alterar')?> <input type="file" name="SEO_img" /><input type="hidden" name="tem_SEO_img" value="1" /><?php
						}?>
						</td>
						<td>&nbsp;</td>
					</tr><?php
					}
					else
					{?>
					<tr>
						<td>&nbsp;</td>
						<td colspan="2"><?php
							echo write_lang('key_nova','Nova');?> : <input type="file" name="SEO_img" /><input type="hidden" name="tem_SEO_img" value="1" />
						</td>
						<td>&nbsp;</td>
					</tr><?php
					}?>
					<tr>
				        <td width="5" height="26"></td>
				        <td width="100"  valign="top">Partilhar:</td>
				        <td><?
				        	$url=$GLOBALS['site_http_url'].seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$ID,$ID,1,$admin_lang);
				        	?>
							<a class="btn btn-primary" href="#" onclick="window.open('https://www.facebook.com/sharer/sharer.php?u=<?=$url?>','facebook-share-dialog','width=626,height=436');return false;">FACEBOOK</a>
							<a class="btn btn-primary" href="#" onclick="window.open('https://twitter.com/intent/tweet?text=Tweet%20this&url=<?=$url?>','facebook-share-dialog','width=626,height=436');return false;">TWITTER</a>
							<a class="btn btn-primary" href="#" onclick="window.open('https://www.linkedin.com/sharing/share-offsite?url=<?=$url?>','facebook-share-dialog','width=626,height=436');return false;">LINKEDIN</a>
							<a class="btn btn-primary" href="#" onclick="window.open('https://developers.facebook.com/tools/debug/?q=<?=$url?>','_blank');return false;">DEBUG FACEBOOK</a>
					    </td>
			    	</tr>

					<tr>
					<td width="10">&nbsp;</td>
					<td class="preto" align="left" width="163"><? echo write_lang('key_imagem','Imagem')?> 1:
					<table width="100%" border=0 cellspacing=0 cellpadding=0><tr><td class="preto">(jpg | png | gif) 72 dpis</td></tr></table>
					</td>
					<td class="preto3" align="left">
					<?php
					if($I1!='')
					{
					?>
						<div class="imagens_400"><img src="<? echo "../../files/products_families/".$ID."_1.".$I1;?>" alt="" border="0" /></div><br />
						<?php
						if($task != "apagar")
						{
							echo write_lang('key_apagar','Apagar');?>
							<input type="checkbox" name="Apagar_img1" value="1">
							<?php echo write_lang('key_ou','ou'); echo write_lang('key_alterar','Alterar')?> <input type="file" name="img_peq1" /><input type="hidden" name="tem_foto1" value="1" /><?php
						}
					}
					else
					{
						echo write_lang('key_nova','Nova')?> : <input type="file" name="img_peq1" /><input type="hidden" name="tem_foto1" value="1" />
					<?php }?>
					</td>
					<td width="4">&nbsp;</td>
					</tr>

					<tr>
					<td>&nbsp;</td>
					<td></td>
					<td></td>
					<td>&nbsp;</td>
					</tr>
					</table>
					</div>
				<?php }

				// CARACTERISTICAS DA FAMILIA
				if(isset($fam_conf) && $fam_conf['tem_caract'])
				{
				?>
					<div style="position:relative; width: 100%; display:none;" class="submenu" id="sub3">
					<table width="100%" border=0 cellpadding="2" cellspacing=0 bgcolor="F6F6F6" class="border1">
					<tr>
					<td width="10">&nbsp;</td>
					<td>
					<table width="100%" border=0 cellpadding="2" cellspacing="2">
					<tr>
						<td class="preto" width="200">Cor:</td>
						<td align="left">
							<table border=0 cellspacing=0 cellpadding=0>
							<tr>
								<td width="65%"><input type="text" disabled="disabled" style="height:17; width:15; background-color:<?=$cor?>">&nbsp;<input name="cor" type="text" style="height:17; width:80; font-family: Arial" value="<?=$cor?>">&nbsp;</td>
								<td width="35%"><input id="pick1" onClick="cp2.select(document.forms[0].cor,'pick1');return false;" name="pick1" type="button" style="height:17; width:52; border-color:#000000; background-color:#FFFFFF; font-size:10px; cursor:hand" value="escolher"></td>
							</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td class="preto" width="200">Texto:</td>
						<td></td>
					</tr>
					</table>
						<textarea name="texto" style="width:100%;height:50px"><?=$T;?></textarea><?php show_editor("oEdit1","texto","99%",150); ?>
					</td>
					<td width="4">&nbsp;</td>
					</tr>
					</table>
					</div>
				<?php }?>
			</div>
			</td>
			</tr>
			</table>
			<table width="100%" height="5px" border="0" cellspacing="0" cellpadding="0"><tr><td></td></tr></table>
			<table width="100%" class="p1 db-family-actions" border=0 cellspacing=0 cellpadding=0>
			<tr>
				<td class="t1" valign="middle" align="center"><input class="btn btn-primary" type="submit" name="Submit" value=" <?=$task_button;?> "> <input class="btn btn-secondary" type="button" name="cancelar" value="<? echo write_lang('key_cancelar','Cancelar')?>" onClick="window.open('list.php','_self');"></td>
			</tr>
			</table>
			<img src="../images/bsh.png" width="100%" height="30px">
			<br />
		</form>
		<?php

		if($task_form == "altera")
		{
			$rs->db_open($db_link,$DB_name,"SELECT ID,Ordem,visivel,Desconto,MargemCli  FROM products_families WHERE ID_ORG=$ID ORDER BY Ordem ASC");
			if(!$rs->EOF())
			{?>
			<form action="reordena2.php" method="post"><input type="hidden" name="url_return" value="products_families.php?ID=<?=$ID?>&ID_ORG=<?=$ID_ORG?>&total_prods=<?=$total_prods?>&checkshowimages=<?=$checkshowimages?>">
			<table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
			<tr>
				<td>
					<table class="titulo_janela" width="100%" height="40px" cellspacing="0" cellpadding="5" border="0">
					<tr>
						<td width="15" align="center"></td>
						<td width="20" align="center" >ID</td>
						<td class="branco"> <? echo 'SUB-';?><? echo write_lang('key_categorias','Categories')?></td>
					</tr>
					</table>
					<?php
					$count = 0;
					while(!$rs->EOF())
					{
						$codigo = $rs->db_data["ID"];
						$ordem = $rs->db_data["Ordem"];
						$visivel = $rs->db_data["visivel"];
						$desconto = number_format($rs->db_data["Desconto"],2);
						$margem = $rs->db_data["MargemCli"].'%';

						$rs2->db_open($db_link,$DB_name,"Select Cabecalho FROM products_families_lang WHERE ID_PAI=$codigo AND lang='$admin_lang'");
						$cabecalho = stripslashes($rs2->db_data["Cabecalho"]);
						$rs2->db_close();

						if($count % 2 !=0)	{$cor="#f0f3f5";$class="r1";$n="1";}
						else				{$cor="#FFFFFF";$class="r2";$n="2";}

						$cor_url='class="preto"';
						if($visivel==0)$cor_url='style="color:#ccc"';

 						?>
						<table width="100%" style="height: 32px;" border=0 cellspacing=0 cellpadding=0 class="grid <?=$class?>">
						<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
						<tr>
							<td width="15" align="center"></td>
							<td width="20" align="center" class="preto"><?=$codigo?></td>
							<td class="preto"><a <?=$cor_url?> href="products_families.php?ID=<?=$rs->db_data["ID"];?>&ID_ORG=<?=$ID;?>&ID_ORG_ANT=<?=$ID_ORG?>&Nivel=<?=$Nivel+1;?>">&nbsp;<? if($cabecalho) echo htmlentities_b($cabecalho); else echo "[Editar]"?></a></td>
							<?php if($desconto > 0){?> <td width="80" class="desconto<?=$n?>"><?=$desconto?> %</td> <? }else{?> <td width="20" class="desconto<?=$n?>">&nbsp;</td> <td width="4" align="right">&nbsp;</td><?php }?>
							<td width="30" align="center" bgcolor="<?=$cor;?>"><?=$margem;?></td>
							<td width="30" align="center" bgcolor="<?=$cor;?>"><img src="<?php echo ($visivel == 1 ? 'ok.png' : 'hidden.png')?>" border="0" /></td>
							<td width="30" align="center" bgcolor="<?=$cor;?>"><a class="italico" href="products_families.php?ID=<?=$rs->db_data["ID"]?>&ID_ORG=<?=$ID;?>&ID_ORG_ANT=<?=$ID_ORG?>&task=duplicar"><img title="<? echo write_lang('key_duplicar_pagina','Duplicar Página')?>" src="duplicate.png" border=0></a></td>
							<td width="30" align="center" bgcolor="<?=$cor;?>"><a class="italico" href="products_families.php?ID=<?=$rs->db_data["ID"]?>&ID_ORG=<?=$ID;?>&ID_ORG_ANT=<?=$ID_ORG?>&task=apagar"><img title="<? echo write_lang('key_apagar_pagina','Apagar Página')?>" src="del2.png" border=0></a></td>
							<td class="ordem<?=$n?>"><input class="ordem" type=text name="chk_<?=$codigo?>" value="<?=$ordem?>"></td>
						</tr>
						</table><?
						$rs->db_next();
						$count++;
					}
					$rs->db_close();?>
				</td>
			</tr>
			</table>
			<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
			<table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
			<tr>
				<td class="t1" valign="middle" align="center"><input class="btn btn-primary" type="submit" name="Reordenar" value="<? echo write_lang('key_reordenar','Reordenar')?> <? echo 'SUB-';?><? echo write_lang('key_categorias','Categories')?>"></td>
			</tr>
			</table>
			</form>
			<img src="../images/bsh.png" width="100%" height="30px"><?
			}
		}
	}


            if($task_form=="altera"  || $top_search || $search2 || $search3)
            {
            	$top_search=trim($top_search);
            	$search2=trim($search2);
            	$search3=intval(trim($search3));

                if($flag==1) // Apagar
                {
                    foreach($_POST as $key=> $valor)
                    {
                        if((strcmp(substr($key,0,4),"chk_")==0))
                        {
                            $id=$valor;
                            $img_path="../../files/products/";
                            DeleteProd($id,$img_path);
                        }
                    }
                }
                if($flag==2) // Reordenar
                {
                    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
                    $db_link =db_opendb_conection($DB_host,$DB_user,$DB_pass);
                    foreach($_POST as $key=> $valor)
                    {
                        if((strcmp(substr($key,0,6),"field_")==0))
                        {
                            $id=substr($key,strpos($key,"_")+1);
                            $sql="UPDATE products SET Ordem=$valor WHERE ID=$id";
                            $res =Runquery($db_link,$DB_name,$sql);
                            if(!$res)  echo db_error($db_link);
                        }
                    }
                    db_closedb_conection($db_link);
                }
                if($flag==3) // mudar de familia
                {
                    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
                    $db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
                    foreach($_POST as $key=> $valor)
                    {
                        if((strcmp(substr($key,0,4),"chk_")==0))
                        {
                            $id = $valor;
                            $sql = "UPDATE products SET ID_ORG=$m_familia WHERE ID=$id";
                            $res = Runquery($db_link,$DB_name,$sql);
                            $sql = "UPDATE products_families_obj SET ID_ORG=$m_familia WHERE ID_PROD=$id";
                            $res = Runquery($db_link,$DB_name,$sql);
                            if(!$res)  echo db_error($db_link);
                        }
                    }
                    db_closedb_conection($db_link);
                }
                if($flag==4) // actualiza preco
                {
                    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
                    $db_link =db_opendb_conection($DB_host,$DB_user,$DB_pass);
                    foreach($_POST as $key=> $valor)
                    {
                        if((strcmp(substr($key,0,6),"preco_")==0))
                        {
                            $prod_id=substr($key,strpos($key,"_")+1);
                            $preco=$valor;
                            $sql="UPDATE products SET Preco1=$preco WHERE ID=$prod_id";

                            $res =Runquery($db_link,$DB_name,$sql);
                            if(!$res)  echo db_error($db_link);
                        }
                    }
                    db_closedb_conection($db_link);
                }
                if($flag==5) // actualiza preco
                {
                    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
                    $db_link =db_opendb_conection($DB_host,$DB_user,$DB_pass);
                    foreach($_POST as $key=> $valor)
                    {
                        if((strcmp(substr($key,0,13),"configurador_")==0))
                        {
                            $prod_id=substr($key,strpos($key,"_")+1);
                            $conf=$valor;
                            $sql="UPDATE products SET Configurador='$conf' WHERE ID=$prod_id";

                            $res =Runquery($db_link,$DB_name,$sql);
                            if(!$res)  echo db_error($db_link);
                        }
                    }
                    db_closedb_conection($db_link);
                }
                if($m_familia) $ID = $m_familia;
                elseif($search2)
                {
                	$ID_Fam=$_POST['ID'];
                	$top_search= $search2;

					global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
                    $db_link =db_opendb_conection($DB_host,$DB_user,$DB_pass);

                    /*$sql="Select products.*,products_lang.Cabecalho,products_objs_lang.Cabecalho as Ref
                    FROM products
                    INNER JOIN products_lang ON products_lang.ID_PAI=products.ID
                    INNER JOIN products_objs ON products_objs.obj_type_id=products.ID
                    INNER JOIN products_objs_lang ON products_objs_lang.ID_PAI=products_objs.obj_id
                    WHERE
                    (
                    	lower(products_lang.Cabecalho) like '%".strtolower($top_search)."%'
                    	OR lower(products.Ref) like '%".strtolower($top_search)."%'
                    	OR lower(products.Ref_Ean) like '%".strtolower($top_search)."%'
                    )
                    AND products.ID_ORG=$ID_Fam
                    AND products_lang.Lang='$admin_lang'
                    AND products_objs_lang.Lang='$admin_lang'
                    GROUP by products.ID
                    ORDER BY products_objs_lang.Cabecalho Asc
                    GROUP BY products.ID
                    limit 0,100";*/


                    $sql="Select products.ID,ID_Marca,Ref
                        FROM products
                        INNER JOIN products_lang ON products_lang.ID_PAI=products.ID
                        WHERE
                        (
                        	lower(products.Ref) like '%".strtolower($top_search)."%'
                        	OR lower(products_lang.Cabecalho) like '%".strtolower($top_search)."%'
                        	OR lower(products.Ref_Ean) like '%".strtolower($top_search)."%'
                        )
                        AND products.ID_ORG=$ID_Fam
                        AND products_lang.Lang='$admin_lang'
                        group by products.ID
                        limit 0,100";

                    $hRes=$rs->db_open($db_link,$DB_name,$sql);

				}
				elseif($search3)
                {
					global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
                    $db_link =db_opendb_conection($DB_host,$DB_user,$DB_pass);

                    $sql="Select products.*,products_lang.Cabecalho,products_objs_lang.Cabecalho as Ref
                    FROM products
                    INNER JOIN products_lang ON products_lang.ID_PAI=products.ID
                    INNER JOIN products_objs ON products_objs.obj_type_id=products.ID
                    INNER JOIN products_objs_lang ON products_objs_lang.ID_PAI=products_objs.obj_id
                    WHERE products.ID = $search3 GROUP BY products.ID";
                    $hRes=$rs->db_open($db_link,$DB_name,$sql);

				}
                elseif($top_search)
                {
                    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$type_search;
                    $db_link =db_opendb_conection($DB_host,$DB_user,$DB_pass);
                    $rs=new DataObj;

                    if(ctype_digit(strval($top_search)))
					{
						$sql="Select products.ID,ID_Marca,Ref
                        FROM products
                        WHERE
                        (
                        	products.ID = '".$top_search."'
                        )";
                    	$hRes=$rs->db_open($db_link,$DB_name,$sql);
					}
					if($rs->EOF())
					{
                        $sql="Select products.ID,ID_Marca,Ref, Aproved, Sync
                        FROM products
                        INNER JOIN products_lang ON products_lang.ID_PAI=products.ID
                        WHERE
                        (
                        	lower(products.Ref) like '%".strtolower($top_search)."%'
                        	OR lower(products_lang.Cabecalho) like '%".strtolower($top_search)."%'
                        	OR lower(products.Ref_Ean) like '%".strtolower($top_search)."%'
                        )
                        AND products_lang.Lang='$admin_lang'
                        group by products.ID
                        limit 0,100";
					}


                    $hRes=$rs->db_open($db_link,$DB_name,$sql);

                    if(!$rs->EOF())
					{
						//Se apenas tiver 1 produto,entra ja na ficha
						if($rs->db_rows == 1)
						{
							$prod_id=$rs->db_data["ID"];
							$rs->db_close();
							echo '<script>window.open("products.php?ID='.$prod_id.'","_self")</script>';
						}
					}
                }
                elseif($novos)
                {
                    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
                    $db_link =db_opendb_conection($DB_host,$DB_user,$DB_pass);
                    $where=" WHERE Aproved=0 ";
                    $sql="Select * from products $where order by Ref Asc";
                    echo $sql;
                    die();
                    $rs=new DataObj;
                    $rs->db_open($db_link,$DB_name,$sql);
                }
                else
				{
					$info=GetRecords_Recursos_1_1(0,$ID,$Nivel+1,"*",$limite_from,$limite_to,$total_prods);
					$rs = $info['obj'];
					$paginacao = $info['return'];
				}
				if($rs && !$rs->EOF())
                {
                ?>
                    <form name="form2" method="post">
                        <input type="hidden" name="ID_Fam" value="<?=$ID?>">
                        <input type="hidden" name="ID" value="<?=$ID?>">
                        <input type="hidden" name="ID_ORG" value="<?=$ID_ORG?>">
                        <input type="hidden" name="total_prods" value="<?=$total_prods?>">
                        <input type="hidden" name="checkshowimages" value="<?=$checkshowimages?>">
                        <table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
                        <tr>
                            <td>
                                <table class="titulo_janela" width="100%" height="30px" cellspacing="0" cellpadding="5" border="0">
                                <tr>
                                    <td width="5px">&nbsp;</td>
                                    <td class="branco"><?php echo write_lang('key_produtos','Produtos')?></td>
                                    <td>
										<input class="form-control" name="pesquisa2" type="text" style="width: 200px;float: right;">
									</td>
									<td width="50px">
										<input value=" OK " class="btn btn-primary" type="submit">
									</td>
                                </tr>
                                </table>
                                <table id="lista_produtos" class="p1" width="100%" border=0 cellspacing=0 cellpadding=0>
                                <tr>
                                    <td width="130px" height="10px" align="left" class="branco">
										<input onClick='makeSelection("filter","form2")' type="checkbox" name="filter" id="filter" value="1"  class="cb"> Seleccionar todos
									</td>
									<td height="10px" align="left" class="branco">
										<input onClick='document.form2.submit()' type="checkbox" name="fam_por_produto" id="fam_por_produto" value="1"  class="cb" <?php if($fam_por_produto==1) echo 'checked';?> > Alterar familia por produto
									</td>
                                </tr>
                                </table>
                                <?
                                $count=0;

                                    while(!$rs->EOF())
                                    {

                                        $aproved 		= $rs->db_data["Aproved"];
                                        $sync 			= $rs->db_data["Sync"];
                                        $codigo 		= $rs->db_data["ID"];
                                        $img_ext 		= $rs->db_data["img_ext"];
                                        $ordem 			= $rs->db_data["Ordem"];
                                        $ref 			= $rs->db_data["Ref"];
                                        $cod_ean    	= $rs->db_data["Ref_Ean"];
                                        $preco1 		= $rs->db_data["Preco1"];
                                        $preco2 		= $rs->db_data["Preco2"];
                                        $precoCusto 	= $rs->db_data["Preco_prec"];
                                        $ID 			= $rs->db_data["ID_ORG"];
                                        $importado 		= $rs->db_data["Importado"];
                                        $id_fornecedor 	= $rs->db_data["id_fornecedor"];
                                        $tipo 			= $rs->db_data["Tipo"];
                                        $configurador 	= $rs->db_data["Configurador"];
										$points 		= $rs->db_data["points"];
										$id_marca 		= $rs->db_data["ID_Marca"];

										/*$prod_precos=get_produtc_price($codigo);
										$preco1 = $rs->db_data['obj_pvp'];
										$precoPVP = $prod_precos['PVP'];

										// VAMOS VERIFICAR SE AS FAMILIAS DO PRODUTO TEM ALGUMA MARGEM
										$margem_array=get_product_families($ID);
										// VAMOS COLOCAR MARGEM
										if($margem_array[1]==1)
										{
											$margem = $margem_array[1];
											if($margem != 0 && $precoPVP > 0) $precoPVP=$precoPVP + $precoPVP + $margem;
										}
										else
										{
											$margem = $margem_array[1];
											if($margem != 0 && $precoPVP > 0) $precoPVP=$precoPVP + ($precoPVP * ($margem / 100));
										}

										$lucro=$precoPVP-$precoCusto;*/

									   	// LINGUAS
                                        $rs2->db_open($db_link,$DB_name,'SELECT Cabecalho,Texto1 FROM products_lang WHERE ID_PAI='.$codigo.' AND lang="'.$admin_lang.'"');
                                        $C = stripslashes($rs2->db_data["Cabecalho"]);
                                        $T = $rs2->db_data["Texto1"];
                                        $rs2->db_close();

                                        // FAMILIA
                                        if($codigo)
                                        {
                                            $rs2->db_open($db_link,$DB_name,'SELECT ID_ORG FROM products_families_obj WHERE ID_PROD='.$codigo);
                                            $ID = $rs2->db_data["ID_ORG"];
                                            $rs2->db_close();


                                            $rs2->db_open($db_link,$DB_name,'SELECT Cabecalho FROM products_families_lang WHERE ID_PAI='.$ID.' AND lang="'.$admin_lang.'"');
                                            $C_familia = stripslashes($rs2->db_data["Cabecalho"]);
                                            $rs2->db_close();
                                    	}

                                    	// MARCA
                                    	$marca_nome=get_produtc_brand($id_marca);
                                    	$marca = $marca_nome['marca_nome'];

										$rs2->db_open($db_link,$DB_name,'SELECT obj_pvp,obj_promo,obj_iva,obj_promo_ativo,obj_ativo,obj_stock,obj_novo,obj_Supplier_ID FROM products_objs WHERE obj_type_id='.$codigo.' Order by obj_id ');
										$obj_pvp = $rs2->db_data["obj_pvp"];
										$obj_promo = $rs2->db_data["obj_promo"];
										$obj_promo_ativo = $rs2->db_data["obj_promo_ativo"];
										$obj_ativo = $rs2->db_data["obj_ativo"];
										$obj_novo = $rs2->db_data["obj_novo"];
										$iva = $rs2->db_data['obj_iva'];
										$obj_Supplier_ID = $rs2->db_data['obj_Supplier_ID'];
										$Stock = $rs2->db_data['obj_stock'];
										$obj_destaque = $rs2->db_data['obj_destaque'];
										$obj_travelsize = $rs2->db_data['obj_travelsize'];
										global $Suppliers_nomes;
										$fornecedor=$Suppliers_nomes[$rs2->db_data["obj_Supplier_ID"]];
                                        $rs2->db_close();

										$icon_bonus = 'images/icon_bonus_nao.png';
										$icon_promo = 'images/icon_promo_nao.png';
										$icon_novidade = 'images/icon_novidade_nao.png';

										$prod_activo=0;
										if($visivel) $prod_activo = 1;

										// SE TIVER PONTOS > 0 E PROMOCAO CHECKADO ENTAO MOSTRAR ICON "BONUS"
										if($points > 0 && $points != '0€') $icon_bonus = 'images/icon_bonus.png';
										// SE TIVER APENAS PROMOCAO CHECKADO  ENTAO MOSTRAR ICON "PROMO"

										if($obj_promo_ativo > 0) $icon_promo = 'images/icon_promo.png';

										// SE TIVER APENAS VALOR NO CAMPO DE PROMOCAO SEM A CHECKBOX ENTAO APENAS APARECE COM O PREÇO RISCADO E NA LISTAGEM DAS PROMOS SEM ICON
										if($obj_novo > 0) $icon_novidade = 'images/icon_novidade.png';

										if($obj_pvp!=$obj_promo && $obj_promo > 0 && $obj_promo_ativo==1)
										{
											$obj_p = $obj_promo;
										}
										else
										{
											$obj_p = $obj_pvp;
										}

                                        if($count % 2 !=0)  {$cor = "#def2fa"; $class = "r1"; $n = "1";}
                                        else                {$cor = "#F6F6F6"; $class = "r2"; $n = "2";}

                                        $cor_url='class="preto"';
										if($aproved==0)$cor_url='style="color:#ccc"';

										$imagem = 'cm_fill.gif';
										if(file_exists('../../files/products/'.$rs->db_data["ID"].'_1_small.png')) $imagem = '../../files/products/'.$rs->db_data["ID"].'_1_small.png';
										elseif(file_exists('../../files/products/'.$rs->db_data["ID"].'_1.jpg')) $imagem = '../../files/products/'.$rs->db_data["ID"].'_1.jpg';
										elseif(file_exists('../../files/products/'.$rs->db_data["ID"].'_1.png')) $imagem = '../../files/products/'.$rs->db_data["ID"].'_1.png';
										elseif(file_exists('../../files/products/'.$rs->db_data["ID"].'_0_small.jpg')) $imagem = '../../files/products/'.$rs->db_data["ID"].'_0_small.jpg';
										elseif(file_exists('../../files/products/'.$rs->db_data["ID"].'_0.jpg')) $imagem = '../../files/products/'.$rs->db_data["ID"].'_0.jpg';
										elseif(file_exists('../../files/products/'.$rs->db_data["ID"].'_0.png')) $imagem = '../../files/products/'.$rs->db_data["ID"].'_0.png';


										$state = '';
										if($rs->db_data["Carrinho"]==1) $state .= '<img src="avancada/carrinho.gif" width="16" title="Pode adicionar ao carrinho" border="0" align="absmiddle" />';
										if($obj_destaque==1) $state .= '<img src="avancada/destaque.png" border="0" title="Em destaque na homepage" align="absmiddle" />';
										if($obj_promo==1) $state .= '<img src="avancada/promo.png" border="0" title="Em promoção" align="absmiddle" />';
										if($obj_travelsize==1) $state .= '<img src="avancada/travel.png" border="0" title="Pode ser expedido para fora de Portugal" align="absmiddle" />';
										if($obj_novo==1) $state .= '<img src="avancada/novidade.png" border="0" title="Novidade" align="absmiddle" />';

                                        if($count==0){?><table height="50px" cellspacing=2 cellpadding=2 class="table table-bordered table-hover table-striped table-td-valign-middle grid" border=0>
                                        	<tr>
												<td	class="preto" width="29" align="center">&nbsp;</td>
												<td	class="preto" width="29" align="center" title="Visivel" ><img alt="produtos activos" src="ok.png" border="0" align="absmiddle" /></td>
												<td	class="preto" width="29" align="center" title="Listar produtos com foto" >Foto</td>
												<td	class="preto" width="50" align="center" >ID</td>
												<td	class="preto" width="80" align="center" >REF</td>
												<td	class="preto" width="100" align="center" >MARCA</td>
												<td	class="preto" width="200px" align="center" >FAMILIA</td>
												<td	class="preto" >DESIGNAÇÃO</td>
												<td	class="preto" width="80" align="center" >SUPPLIER</td>
												<td	class="preto" width="80" align="center" >PREÇO SITE</td>
												<td	class="preto" width="80" align="center" >STOCK</td>
												<td	class="preto" width="100" align="center" style="cursor:default">Estado</td>
												<td	class="preto" width="29" align="center">&nbsp;</td>
												<td	class="preto" width="30" align="center">&nbsp;</td>
												<td	class="preto" width="30" align="center">&nbsp;</td>
											</tr><? }?>
                                        	<tr style="background-color:'<?=$cor;?>'" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
	                                        	<td class="preto" width="29" align="center"><input type="checkbox" name="chk_<?=$codigo?>" id="chk_<?=$codigo?>" value="<?=$codigo?>" class="cb" /></td>
	                                        	<td class="preto" width="29" align="center"><?php if($aproved) echo "<img src='ok.png' border='0' align='absmiddle' title='Produto Activo' />"; else echo "-";?></td>
	                                        	<td class="preto" width="29" align="center"><img style="max-width:50px" src="<?=$imagem; ?>" border="0" alt=""></td>
	                                        	<td class="preto" width="50" align="center"><a <?=$cor_url?> href="products.php?ID=<?=$rs->db_data["ID"];?>&ID_ORG=<?=$ID;?>&ID_ORG_ANT=<?=$ID_ORG?>&Nivel=<?=$Nivel+1;?>"><?=$codigo;?></a></td>
	                                        	<td class="preto" width="80" align="center"><a <?=$cor_url?>><?=$ref;?></a></td>
	                                        	<td class="preto" width="100" align="center"><a <?=$cor_url?>><?=$marca;?></a></td>
	                                        	<td class="preto" width="200" align="left" ><a <?=$cor_url?>><?php if($fam_por_produto==1)gera_lista_familias_produto($ID,'products_families','products_families_lang'); else echo $C_familia;?></a></td>
                                        		<td class="preto"><a <?=$cor_url?> href="products.php?ID=<?=$codigo;?>&ID_ORG=<?=$ID;?>"><?php if($C) echo ($C); else echo "[Sem nome]"?></a></td>
												<td class="preto" width="80" align="center" ><?=(($sync == 1) ? strtoupper(PROJETO) : $GLOBALS['Suppliers_nomes'][$obj_Supplier_ID]);?></td>
												<td class="preto" width="80" align="center" ><?=$precoPVP?></td>
												<td class="preto" width="80" align="center" ><?=$Stock?></td>
												<td class="preto" width="100" align="center"><?=$state;?></td>
												<td class="preto" width="29" align="center" class="ordem<?=$n?>"><input class="ordem small" style="padding:0; width:30px" type="text" name="field_<?=$prod_id?>" value="<?=$ordem?>"></td>
                                            	<td class="preto" width="30px" align="center" bgcolor="<?=$cor;?>"><a href="products.php?ID=<?=$rs->db_data["ID"]?>&ID_ORG=<?=$ID;?>&ID_ORG_ANT=<?=$ID_ORG?>&task=duplicar"><img title="Duplicar" src="duplicate.png" border="0" /></a></td>
                                            	<td class="preto" width="30px" align="center" bgcolor="<?=$cor;?>"><a href="products.php?ID=<?=$rs->db_data["ID"]?>&ID_ORG=<?=$ID;?>&ID_ORG_ANT=<?=$ID_ORG?>&task=apagar"><img title="<?php echo write_lang('key_apagar','Apagar')?>" src="del2.png" border="0" /></a></td>
                                        	</tr><?php
                                        $rs->db_next();
                                        $count++;
                                    }
                                    echo '</table>';

                                $rs->db_close();
								echo $paginacao;
                                ?>
                            </td>
                        </tr>
                        </table>
                    <?php
                     show_page_bottom_tools2();
                    ?>
                    </form>
                    <?php
                }
            }
            ?>
         </td>
         <td width="10px">&nbsp;</td>
         <td width="200px" valign="top"><?php show_fast_tools();show_translate_tools();?></td>
    </tr>
    </table>
</div>
<script src="avancada/chosen.jquery.min2.js?dp=<?php echo dumpcache?>" type="text/javascript"></script>
<script type="text/javascript">
$(".form-control4").chosen({disable_search_threshold: 10, no_results_text: "Não foram encontrados resultados!",width: "300px",enable_new_filter:false});
$(".form-control6").chosen({disable_search_threshold: 10, no_results_text: "Não foram encontrados resultados!",enable_new_filter:false});
</script>
<?php include "../layout/bottom.inc.php";?>
