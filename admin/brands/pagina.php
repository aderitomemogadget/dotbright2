<?
	include "settings.inc.php";
	if($task=='apagar'){$task_button=write_lang('key_apagar_este_registo','Apagar este Registo');$task_form='apaga';}
	elseif($task=='duplicar'){$task_button=write_lang('key_duplicar_este_registo','Duplicar este Registo');$task_form='duplicar';}
	elseif($ID == 0){$task_button=write_lang('key_gravar_novo_registo','Gravar Novo Registo');$task_form='insere';}
	else {$task_button=write_lang('key_gravar_alteracoes','Gravar alterações');$task_form='altera';}
	$D = getdate();

	if($ID!=0)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

		$fields=db_list_fields($DB_name,'brands',$db_link);
		$columns=db_num_fields($fields);
		for ($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
		if(!in_array('preco_revenoff', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE brands ADD preco_revenoff tinyint(1) DEFAULT 0');  }

		$rs=new DataObj;
		$sql = "Select * from brands WHERE ID=$ID";
		$rs->db_open($db_link,$DB_name,$sql);
		if($rs)
		{
			$visivel=$rs->db_data["Visivel"];
			$I1=$rs->db_data["img_ext"];
			$I2=$rs->db_data["img_ext2"];
			$ordem=$rs->db_data['Ordem'];
			$preco_revenoff=$rs->db_data['preco_revenoff'];
			$ref_integracao=$rs->db_data['ref_integracao'];

			$rs2=new DataObj;
			$sql="Select Nome, Texto1 from brands_lang where ID_PAI=$ID and Lang='$admin_lang' order by ID Desc";
			$rs2->db_open($db_link,$DB_name,$sql);
			$nome = stripslashes($rs2->db_data["Nome"]);
			$Texto1 = stripslashes($rs2->db_data["Texto1"]);
			$rs2->db_close();
		}
		$rs->db_close(1);
	}
	include "../layout/header.inc.php";
	include "../layout/body.inc.php";
?>
<script type="text/javascript">
	if(document.getElementById){document.write('<style type="text/css">\n');document.write('.subm{display: none;}\n');document.write('</style>\n');}
	function SwitchMenu(obj){if(document.getElementById){var el=document.getElementById(obj);var ar=document.getElementById("md").getElementsByTagName("span");if(el.style.display!="block"){for(var i=0;i<ar.length;i++){if(ar[i].className=="subm")ar[i].style.display="none";}el.style.display="block";}else el.style.display="none";}}
	function select_page() {
	var editdoc=document.form.url_link;
	var newurl=showModalDialog("listcontent.php", "", "resizable: no; help: no; status: no; scroll: yes;");
	if(newurl) editdoc.value=newurl;
	}
</script>
<link rel="stylesheet" type="text/css" href="sa.css" />
<link rel="stylesheet" type="text/css" href="chosen.css?dp=<?php echo dumpacache?>" />
<div class="center">
<form name="form" method="post" action="pagina_trata.php?task_form=<? echo $task_form?>&dump=<? echo uniqID("")?>" enctype="multipart/form-data">
<input type="hidden" name="ID" value="<? echo $ID;?>" />
<input type="hidden" name="m_lang" value="<? echo $GLOBALS['admin_lang'];?>" />
<input type="hidden" name="slashes" value="'" />

<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
<tr>
<td valign="top" align="left"><? include "../layout/menus.inc.php"; ?>
<div class="panel panel-inverse overflow-hidden m-b-12">
    <div class="panel-heading">
        <h4 style="color:#fff">MARCAS</h4>
    </div>
</div>
<table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
<tr>
<td valign="top">

<table width="100%" border=0 cellspacing=0 cellpadding="5" class="subtitulo_janela">
<tr>
<td valign="middle"><a href="list.php" class="branco">« Voltar à listagem de coleções</a></td>
</tr>
</table>
</td>
</tr>
<tr>
<td align="left" valign="top" class=preto>


<table width="100%" cellpadding=5 cellspacing=0 border=0 style="background:#FFF">
<tr >
<td width="5" height="30">&nbsp;</td>
<td width="200" class="new_preto">Visivel:&nbsp;</td>
<td><input type="checkbox" name="visivel" value="1" <? if($visivel==1){ echo 'checked="checked"';}?> /></td>
<td width="5" height="30">&nbsp;</td>
</tr>
<tr >
<td width="5" height="30">&nbsp;</td>
<td width="200" class="new_preto">Visivel no Menu:&nbsp;</td>
<td><input type="checkbox" name="preco_revenoff" value="1" <?php if($preco_revenoff==1){ echo 'checked="checked"';}?> /></td>
<td width="5" height="30">&nbsp;</td>
</tr>
<tr >
<td width="5" height="30">&nbsp;</td>
<td width="200" class="new_preto">Ordem:&nbsp;</td>
<td><input type="text" name="ordem" style="WIDTH: 50px" maxlength="50" value="<? echo $ordem;?>" class="form-control" /></td>
<td width="5" height="30">&nbsp;</td>
</tr>
<tr >
<td width="5" height="30">&nbsp;</td>
<td width="200" class="new_preto">Referência:&nbsp;</td>
<td><input type="text" name="ref_integracao" style="WIDTH: 150px" maxlength="50" value="<? echo $ref_integracao;?>" class="form-control" /></td>
<td width="5" height="30">&nbsp;</td>
</tr>
<tr >
<td width="5" height="30">&nbsp;</td>
<td width="200" class="new_preto">Nome Marca:&nbsp;</td>
<td><input type="text" name="nome" style="WIDTH: 300px" maxlength="50" value="<? echo $nome;?>" class="form-control" /></td>
<td width="5" height="30">&nbsp;</td>
</tr>
<tr>
<td width="5" height="30"></td>
<td width="200" class="new_preto"><?php echo write_lang('key_imagem','Imagem');?>:&nbsp;</td>
<td><div style="overflow-y:hidden;max-width:600px"><?
if($I1!="" && file_exists("../../files/brands/".$ID."_1.".$I1))
{
	if(file_exists("../../files/brands/$ID"."_1.".$I1)){?><a target="_new" href="<? echo "../../files/brands/".$ID."_1.".$I1;?>" style="color:#000"><img src="<? echo "../../files/brands/".$ID."_1.".$I1;?>"></a><br><? }?><?
	if($task!="apagar"){?><? echo write_lang('key_apagar','Apagar');?> <input type='checkbox' name='Apagar_img1' value='1'> <? echo write_lang('key_ou','Ou');?> <? echo write_lang('key_alterar','Alterar');?> <input type='file' name='img_peq1'> <input type='hidden' name='tem_foto1' value='1'><? }
}
else{ echo write_lang('key_nova','Nova');?>: <input type=file name='img_peq1'> <input type='hidden' name='tem_foto1' value='0'><?php }?></td>
<td width="5" height="30">&nbsp;</td>
</tr>
<tr>
<td width="5" height="30"></td>
<td width="200" class="new_preto">Imagem header:&nbsp;</td>
<td>
<div style="overflow-y:hidden;max-width:600px">
<?
if($I2!="" && file_exists("../../files/brands/".$ID."_2.".$I2))
{
	if(file_exists("../../files/brands/$ID"."_2.".$I2)){?><a target="_new" href="<? echo "../../files/brands/".$ID."_2.".$I2;?>" style="color:#000"><img width="300" src="<? echo "../../files/brands/".$ID."_2.".$I2;?>"></a><? }?><?
	if($task!="apagar"){?><? echo write_lang('key_apagar','Apagar');?> <input type='checkbox' name='Apagar_img2' value='1'> <? echo write_lang('key_ou','Ou');?> <? echo write_lang('key_alterar','Alterar');?> <input type='file' name='img_peq2'> <input type='hidden' name='tem_foto2' value='1'><? }
}
else{ echo write_lang('key_nova','Nova');?>: <input type=file name='img_peq2'> <input type='hidden' name='tem_foto2' value='0'><?php }?>
</div>
</td>
<td width="5" height="30"></td>
</tr>
<?php /* <tr><td style="background:#87ceee " height="10" colspan="10"><br></td></tr>
<tr style="background:#FFF">
<td width="5" height="30">&nbsp;</td>
<td width="200" class="new_preto">Texto:&nbsp;</td>
<td><textarea name="Texto1" class="form-control" style="width:100%; height:300px"><?php echo $Texto1?></textarea></td>
<td width="5" height="30">&nbsp;</td>
</tr> */?>
</table>

</td>
</tr>

</table>
<?php show_page_buttons();?>

<br>
<?php

$porpagina = 10;


global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
$db_link =db_opendb_conection($DB_host,$DB_user,$DB_pass);
$where = " WHERE products.ID_Marca=".$_GET['ID'];
$sql="Select products.*,products_lang.Cabecalho,products.Ref from products INNER JOIN products_lang ON products_lang.ID_PAI=products.ID inner join products_objs on products_objs.obj_type_id=products.ID inner join products_objs_lang on products_objs_lang.ID_PAI=products_objs.obj_id $where and products_lang.Lang='$admin_lang' and products_objs_lang.Lang='$admin_lang' group by products.ID order by products_objs_lang.Cabecalho Asc";
$rs=new DataObj;

$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
$total=$rs->db_rows;
$rs->db_close();

$n_pags = ceil($total / $porpagina); // n de paginas
if(!isset($_GET['pagina'])) $pagina = '1';
else $pagina = $_GET['pagina'];
$inicio = $pagina - 1;
$inicio = $inicio * $porpagina;
$sql2=$sql." LIMIT $inicio, $porpagina";
// echo $sql2;
$rs->db_open($db_link, $DB_name, $sql2);

if($n_pags > 1)
{
	$return = '<div class="clear" style="height:10px"></div><table class="new_quadros" border=0 cellspacing=0 cellpadding=0 style="margin-left:auto;margin-right:auto;"><tr>';

	$p_seo = 'pagina.php?ID='.$_GET['ID'];

	if($pagina > 6)
	{
		if(substr_count($p_seo, '?')) $ash = '&pagina=1';	else $ash = '?pagina=1';
		$pgn = 'onclick=window.open("'.$p_seo.$ash.'","_self")';
		$return .= '<td width="2px"></td><td width="20" height="15" bgcolor="#ffffff" align="center" '.$pgn.' style="font-size:10px;cursor:pointer;padding-top:2px;border:#939597 1px solid;color:#939597">1</td><td width="2px">...</td>';
	}
	for($pi = ($pagina - 6); $pi < ($pagina + 7); $pi++)
	{

		if(substr_count($p_seo, '?')) $ash = '&pagina='.$pi;	else $ash = '?pagina='.$pi;
		$pgn = 'onclick=window.open("'.$p_seo.$ash.'","_self")';
		$return .= ($pi >= 1 && $pi <= $n_pags) ? $pagina == $pi ? '<td width="2px"></td><td width="20" height="15" bgcolor="#006d9f" align="center" style="font-size:10px;color:#FFF;padding-top:2px;border:#939597 1px solid" >'.sprintf(($pi)).'</td>' : '<td width="2px"></td><td width="20" height="15" bgcolor="#edf4f9" onmouseover="this.style.background=\'#006d9f\';this.style.color=\'#FFFFFF\'" onmouseout="this.style.background=\'#edf4f9\';this.style.color=\'#939597\'" align="center" '.$pgn.' style="font-size:10px;cursor:pointer;padding-top:2px;border:#939597 1px solid;margin:2px;color:#939597" >'.sprintf($pi).'</td>' :'';
	}
	if($n_pags != $pagina)
	{

		if(substr_count($p_seo, '?')) $ash = '&pagina='.$n_pags;	else $ash = '?pagina='.$n_pags;
		$pgn = 'onclick=window.open("'.$p_seo.$ash.'","_self")';
		$return .= '<td width="2px"></td><td width="2px">...</td><td width="20" height="15" bgcolor="#ffffff" align="center" '.$pgn.' style="font-size:10px;cursor:pointer;padding-top:2px;border:#939597 1px solid;color:#939597" >'.sprintf($n_pags).'</td>';
	}
	$return .= '</tr></table>';
}

if(!$rs->EOF())
{
?>
	<form name="form2" method="post">
		<input type="hidden" name="ID_Fam" value="<?php echo $ID?>">
		<input type="hidden" name="ID" value="<?php echo $ID?>">
		<input type="hidden" name="ID_ORG" value="<?php echo $ID_ORG?>">
		<input type="hidden" name="total_prods" value="<?php echo $total_prods?>">
		<input type="hidden" name="checkshowimages" value="<?php echo $checkshowimages?>">
		<table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
		<tr>
			<td>

				<table width="100%" border=0 cellspacing=0 cellpadding=0 class="p1">
				<tr>
				<td width="5px">&nbsp;</td>
				<td class="branco"><?php echo write_lang('key_produtos','Produtos')?></td>
				<td>&nbsp;</td>
				</tr>
				</table>

				<table id="lista_produtos" class="p1" width="100%" border=0 cellspacing=0 cellpadding=0>
				<tr>
					<td width="29px" height="10px" align="center"><input onClick='makeSelection("filter","form2")' type="checkbox" name="filter" id="filter" value="1"  class="cb"></td>
					<td width="4px" height="10px">&nbsp;</td>
					<td width="29px" height="10px">&nbsp;</td>
					<td width="4px" height="10px">&nbsp;</td>
					<td width="10px" height="10px">&nbsp;</td>
					<td width="1px">&nbsp;</td>
					<td width="1px">&nbsp;</td>
					<td class="branco"><?php echo write_lang('key_nome','Nome')?></td>
					<td width="26px">&nbsp;</td>
					<td width="4px">&nbsp;</td>
					<td width="30px">&nbsp;</td>
				</tr>
				</table>
				<?
				$count=0;

					while(!$rs->EOF())
					{
						$visivel=$rs->db_data["Aproved"];
						$codigo=$rs->db_data["ID"];
						$img_ext=$rs->db_data["img_ext"];
						$ordem=$rs->db_data["Ordem"];
						$ref=$rs->db_data["Ref"];
						$preco1=$rs->db_data["Preco1"];
						$preco2=$rs->db_data["Preco2"];
						$precoCusto=$rs->db_data["Preco_prec"];
						$ID=$rs->db_data["ID_ORG"];
						$importado=$rs->db_data["Importado"];
						$id_fornecedor=$rs->db_data["id_fornecedor"];
						$tipo=$rs->db_data["Tipo"];
						$configurador=$rs->db_data["Configurador"];
						$points = $rs->db_data["points"];

					   // LINGUAS
						$rs2->db_open($db_link, $DB_name, 'SELECT Cabecalho, Texto1 FROM products_lang WHERE ID_PAI='.$codigo.' AND lang="'.$admin_lang.'"');
						$C = $rs2->db_data["Cabecalho"];
						$T = $rs2->db_data["Texto1"];
						$rs2->db_close();

						$rs2->db_open($db_link,$DB_name, 'SELECT obj_pvp, obj_promo, obj_promo_ativo, obj_ativo, obj_novo FROM products_objs WHERE obj_type_id='.$codigo);
						$obj_pvp = $rs2->db_data["obj_pvp"];
						$obj_promo = $rs2->db_data["obj_promo"];
						$obj_promo_ativo = $rs2->db_data["obj_promo_ativo"];
						$obj_ativo = $rs2->db_data["obj_ativo"];
						$obj_novo = $rs2->db_data["obj_novo"];
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

						?><table height="50px" border=0 cellspacing=0 cellpadding=0 class="<?php echo $class?>">
						<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?php echo $cor;?>'">
						<td width="29" align="center"><input type="checkbox" name="chk_<?php echo $codigo?>" id="chk_<?php echo $codigo?>" value="<?php echo $codigo?>" class="cb" /></td>
						<td width="4" align="right"><img width="2" src="../images/s6_<?php echo $n?>.jpg" /></td>
						<td width="29" align="center"><img src="../images/page.png" /></td>
						<td width="4" align="right"><img width="2" src="../images/s6_<?php echo $n?>.jpg" /></td>
						<td width=10 class="preto">&nbsp;</td>
						<td><a class="preto" href="../products/products.php?ID=<?php echo $rs->db_data["ID"];?>&ID_ORG=<?php echo $ID;?>&ID_ORG_ANT=<?php echo $ID_ORG?>&Nivel=<?php echo $Nivel+1;?>">[<?php echo $ref?>]&nbsp; - <? if($C) echo htmlentities_b($C); else echo "[Edit]"?> </a></td>


						<td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg" /></td>
						<td class="preto" width="24px" align="center"><? if($prod_activo){?><img src="images/icon_activo.png"><? }?></td>
						<td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg" /></td>


						<td width="20px" align="right" bgcolor="<?php echo $cor;?>"><a class="italico" href="../products/products.php?ID=<?php echo $rs->db_data["ID"]?>&ID_ORG=<?php echo $ID;?>&ID_ORG_ANT=<?php echo $ID_ORG?>&task=duplicar"><img title="Duplicar" src="duplicate.png" border="0" /></a></td>
						<td width="20px" align="right" bgcolor="<?php echo $cor;?>"><a class="italico" href="../products/products.php?ID=<?php echo $rs->db_data["ID"]?>&ID_ORG=<?php echo $ID;?>&ID_ORG_ANT=<?php echo $ID_ORG?>&task=apagar"><img title="<?php echo write_lang('key_apagar','Apagar')?>" src="del2.png" border="0" /></a></td>
						<td width="4px" align="right"><img width="2" src="../images/s6_<?php echo $n?>.jpg"></td>
						<td width="1px"></td>
						</tr>
						</table><?php
						$rs->db_next();
						$count++;
					}

				$rs->db_close();
				echo $return;
				?>
			</td>
		</tr>
		</table>
	<?php
	 // show_page_bottom_tools2();
	?>
	</form>
	<?php
}

?>
</td>
<td width="10px">&nbsp;</td>
<td valign="top" width="200"><? show_fast_tools();?></td>
</tr>
</table>
</form>
</div>
<script src="chosen.jquery.min.js" type="text/javascript"></script>

<script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true}); </script><br /><br />
<? include "../layout/bottom.inc.php";?>