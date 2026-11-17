<?php
require_once "settings.inc.php";
ini_set('meemory_limit', '1000M');
ini_set('post_max_size', '1000M');
ini_set('upload_max_filesize', '1000M');

$DESCONTOf = str_replace('.','_',$_GET['Desconto']);

global $DB_host, $DB_user, $DB_pass, $DB_name;
$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);

if ($_POST && isset($_POST["adicionar"]))
{
	$array_prod = $_POST['produto'];
	$array_prod_hidden = $_POST['produto_hidden'];
	$array_fam = $_POST['familias'];
	$arr_hidden = $_POST['familias_hidden'];

	global $DB_host, $DB_user, $DB_pass, $DB_name;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	foreach ($arr_hidden as $fam)
	{
		$sql = 'DELETE FROM sales_promotions_products WHERE promo_id="'.$_GET['ID_PAI'].'" AND DESCONTO="'.$_GET['Desconto'].'" AND FAMILIA="'.$fam.'" AND MARCA=0';
		$res = Runquery($db_link, $DB_name, $sql);
	}
	db_closedb_conection($db_link);
	global $DB_host, $DB_user, $DB_pass, $DB_name;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	foreach ($array_fam as $fam)
	{
		$prods = get_produtos($fam);

		for($a=0;$a<count($prods); $a++)
		{
			$sql='INSERT INTO sales_promotions_products (promo_id, DESCONTO, PROD_ID, FAMILIA) VALUES ("'.$_GET['ID_PAI'].'","'.$_GET['Desconto'].'","'.$prods[$a].'","'.$fam.'")';
			$res = Runquery($db_link, $DB_name, $sql);
		}
	}
	db_closedb_conection($db_link);

	$fams_list = gera_lista_familias($_GET['ID_PAI'], 1, $_GET['Desconto']);
}

function getMarcas()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $obj=new DataObj;
    $sql="select brands.ID, brands_lang.Nome from brands inner join brands_lang on brands.ID=brands_lang.ID_PAI order by brands_lang.nome ASC";
    $obj->db_open($db_link, $DB_name, $sql);

	$data = array();
	while (!$obj->EOF())
	{
		$temp = array();
		$temp[] = $obj->db_data['ID'];
		$temp[] = $obj->db_data['Nome'];
		$data[] = $temp;

		$obj->db_next();
	}
	$obj->db_close();

	return $data;
}
function getFamilias($ID_ORG=0, &$temp)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $obj=new DataObj;
   	$sql2 =
			'
			SELECT
				pf.ID, pf.ID_ORG, pfl.Cabecalho
			FROM
				products_families pf
			INNER JOIN
				products_families_lang pfl ON ( pf.ID = pfl.ID_PAI )
			WHERE
				pfl.Lang="'.$GLOBALS['admin_lang'].'"
			ORDER BY
				 pf.ID_ORG, pfl.Cabecalho ASC
			';
	// echo $sql2.'<br />';
	$obj2=new DataObj;
	$obj2->db_open($db_link,$DB_name,$sql2);
	$data = array(); $temp = array();
	$valor_antigo = 0;
	while(!$obj2->EOF())
	{
		$ID_ORG = $obj2->db_data['ID_ORG'];

		if($ID_ORG > 0)
		{
			$temp[$obj2->db_data['ID_ORG']][$obj2->db_data['ID']] = array(
						'familia_id' => $obj2->db_data['ID'],
						'familia_pai' => $obj2->db_data['ID_ORG'],
						'familia_nome' => $obj2->db_data['Cabecalho']
					);
		}
		else
		{
			$temp[$obj2->db_data['ID']] = array(
							'familia_id' => $obj2->db_data['ID'],
							'familia_pai' => $obj2->db_data['ID_ORG'],
							'familia_nome' => $obj2->db_data['Cabecalho']
						);

		}
		$obj2->db_next();
	}
	$obj2->db_close();

	return $temp;
}
$array_Familias = getFamilias();
global $DB_host,$DB_user,$DB_pass,$DB_name,$Nome,$lang_crm,$ID,$ID_Fam, $txt_email14;
$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
$obj3 = new DataObj;

?><!DOCTYPE html>
<html>
<head>
<title>Title of the document</title>

<script src="../../external_api/datepicker/jquery-1.8.2.js"></script>
<link rel="stylesheet" type="text/css" href="../admin_estilos.css" />
<link rel="stylesheet" type="text/css" href="sa.css?d=1" />
<style>html,body{background:#FFF;font-size:12px}</style>

<script>
function reset_campos()
{
	document.formFiltro.bgn.value=0;
}
<?php
if ($_POST && isset($_POST["adicionar"]))
{

	?>
	$(document).ready(function()
	{
		parent.$('#categories<?php echo $DESCONTOf?>').html("<?php echo addslashes($fams_list)?>");
		parent.$.fancybox.close();
	});
	<?php
}
?>
</script>
</head>

<body>

<div class="center" id="laod" style="display:none;position:absolute;top:0px;left:0px;width:100%;height:1px;border:#FFF 0px solid;z-index:1000;background:#c0c0c0; height:100px;padding-top:50px; text-align:center; font-size:15px">Não saia desta página, o script está a ser executado</div>
<form name="formFiltro" method="post"><input type="hidden" name="bgn" value="<?php echo $bgn?>" /><input type="hidden" name="num" value="<?php echo $num?>" />	<input type="hidden" name="pesq" value="1" />
<table width="100%" bgcolor="#fafafa" border=0 cellspacing=0 cellpadding=1>
<tr>
<td bgcolor="#e3e5e3" colspan="10" height="25px" align="center" class='preto'><font class="red" style="font-size:20px"><b>SELEÇÃO DE FAMILIAS</b></font></td>
</tr>
<tr>
<td bgcolor="#e3e5e3" height="20px" width="210px" align="center" class='preto'><font class="red"><b>Marca</b></font></td>
<td bgcolor="#e3e5e3" class='preto' width="210px" align="center"><font class="red"><b>Família</b></font></td>
<td bgcolor="#e3e5e3" width="20px" class='preto'>&nbsp;</td>
<td bgcolor="#e3e5e3" class='preto'>&nbsp;</td>
</tr>
<tr bgcolor="#F1F1F1">
<td bgcolor="#e3e5e3" align="center" class='preto'>
<?php
	$array_Marcas = getMarcas();
	?>
	<select class="form-control" name="s_marca" style="WIDTH:200px;">
	<option value="">SEM VALOR</option>
	<?php
	foreach($array_Marcas as $marca)
	{
	?><option value="<?php echo $marca[0]?>" <? if ($s_marca==$marca[0]){ echo ' selected="selected" ';}?>><?php echo $marca[1]?></option> <?php
	}
	?>
	</select>
</td>
<td bgcolor="#e3e5e3" class='preto' align="center">
	<select class="form-control" id="s_familia" name="s_familia" style="WIDTH:350px;">
	<option value="">SEM VALOR</option>
	<?php
	foreach($array_Familias as $fam => $val)
	{
		?><option value="<?php echo $fam?>" disabled="disabled" <?php if($s_familia == $fam){ echo "selected"; } ?>><?php echo $val['familia_nome']?></option><?php
		foreach($array_Familias[$fam] as $fam2 => $val2)
		{
			if($fam2 > 0)
			{
				?><option value="<?php echo $fam2?>" <?php if($s_familia == $fam2){ echo "selected"; } ?>><?php echo '---'.$val2['familia_nome']?></option><?php
			}
		}
	}
	?>
	</select>
</td>
<td bgcolor="#e3e5e3"><input name="image" type="image" class="" src="../templates/bt_search.jpg" alt="Pesquisar" title="Pesquisar" align="absmiddle" style="border:none" /></td>
<td bgcolor="#e3e5e3" class='preto'>&nbsp;</td>
</tr>
</table>
</form>
<form action="seleccionarfamilias.php?ID_PAI=<?php echo $_GET['ID_PAI']; ?>&Desconto=<?php echo $_GET['Desconto']; ?>&Linha=<?php echo $_GET['Linha']; ?>"  method="post" name="formConfirmar" onSubmit="submit_loading();return true;">

	<input type="hidden" value="<?php echo $_POST['s_marca']?>" name="s_marca" />
	<input type="hidden" value="<?php echo $_POST['s_familia']?>" name="s_familia" />
	<input type="hidden" value="<?php echo $_POST['f_produto']?>" name="f_produto" />
	<input type="hidden" value="1" name="adicionar" />

	<table width="100%" border="0" align="left">
	<?php
				foreach($array_Familias as $fam => $val)
				{

					foreach($array_Familias[$fam] as $fam2 => $val2)
					{
						if($cor == '#FFFFFF') $cor = '#F0F0F0';
						else  $cor = '#FFFFFF';

						if(($fam2 > 0 && $_POST['s_familia']=='') || isset($_POST['s_familia']) && $_POST['s_familia']==$fam2)
						{
							$prods_list = show_produtos($fam2, $fam);
							if($prods_list['list_prods']!='' && !isset($arr_fam[$fam]))
							{
								echo "<tr><td colspan=\"2\" style='border-bottom:solid 1px #000; height:30px;' bgcolor=#E9F0B8 class='preto' align='center'><b>".$obj2->db_data["Cabecalho"]." (".$val['familia_nome'].")";
								echo ' &nbsp;&nbsp;&nbsp;&nbsp; Selecionar: <a href="javascript:;" onclick="SelectAll(\'fam_'. $fam.'\', true)" style="text-decoration:underline">Todos</a> &nbsp;/&nbsp; <a href="javascript:;" onclick="SelectAll(\'fam_'.$fam.'\', false)"  style="text-decoration:underline">Nenhum</a>';
								echo "</b></td></tr>";
								$arr_fam[$fam] = $fam;
							}



							$selected = '';
							if($prods_list['total_prd_prom'] > 0) $selected = "checked='checked'";

							if($prods_list['list_prods']!='')
							{
								echo "<tr bgcolor='".$cor."'><td style='border-bottom:solid 1px #000'  colspan=\"2\" class='preto' align='left'>";
								echo "<div style='float:left'><input type='checkbox' name='familias[]' ".$selected." onclick=\"SelectAll('', true, 'fam2_".$fam2."', this.checked)\" value='".$fam2."' class='fam_".$fam."' id='fam2_".$fam2."' /><input type='hidden' name='familias_hidden[]' value='".$fam2."' /></div>";
								echo "<div style='float:left'><b> <span style='color:#008B45'>&nbsp;&nbsp;".$val2['familia_nome']."</span></b> </div>";
								echo "</td></tr>";
							}
						}
					}
				}
			echo "<tr><td  colspan=\"2\" style='border-bottom:solid 1px #000; height:30px;' class='preto' align='center'><b></b></td></tr>";
		?>

	</table>
	<div style="position:fixed;bottom:0;background:#FFF;width:100%;height:30px;z-index:999999">
	<table width="100%" border="0" align="left">
	<tr>
	<td style="height:30px; width:50%; text-align:right;"><input type="submit" name="adicionar1" value="Submeter Familias" class="btn btn-primary" />&nbsp;&nbsp;&nbsp;</td>
	<td style="height:30px;  width:50%; text-align:left;">&nbsp;&nbsp;&nbsp;<input type="button" name="close" value="Fechar" onclick="parent.$.fancybox.close();" class="btn btn-primary" /></td>
	</tr>
	</table>
	</div>
</form>
<script>
	function SelectAll(id, status, id2, status2)
	{
		if(id)
		{
			$("."+id).each(function()
			{
				 $(this).attr("checked",status);

			});
		}
		if(id2)
		{
			$('div.'+id2+' > input[type=checkbox]').each(function()
			{
				 $(this).attr("checked", status2);
			});
		}
	}
	function open_div(id)
	{

		if ($('#'+id).is(':visible'))
		{
			$("#"+id).hide();
		}
		else
		{
			$("#"+id).show();
		}

	}
	function submit_loading(id)
	{
		if(document.getElementById('laod')) document.getElementById('laod').style.display='block';
	}
</script>
</body>
</html>
<?
function get_produtos($f_familia2="", $f_familia="")
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$Nome,$ID,$ID_Fam, $txt_email14;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$obj2=new DataObj;
	$obj3=new DataObj;

	if(isset($_POST['f_produto']) && ltrim(rtrim($_POST['f_produto']))!='')
	{
		$w .= '
				AND
					(p1.Ref Like "%'.ltrim(rtrim($_POST['f_produto'])).'%"
				OR
					p1l.Cabecalho Like "%'.ltrim(rtrim($_POST['f_produto'])).'%") ';
	}
	if(isset($_POST['s_marca']) && $_POST['s_marca'] > 0)
	{
		if($_POST['s_marca'] != "SM") $w .= ' AND p1.ID_Marca = "'.$_POST['s_marca'].'" ';
		else $w .= ' AND p1.ID_Marca = 0 ';
	}

	if($f_familia2) $w.= ' AND (fp.fam_id2 = "'.$f_familia2.'")';

	$aproved = "p1.Aproved = 1";

	$sql = '
			SELECT
				p1.* , p1l.Cabecalho
			FROM
				products p1
			INNER JOIN products_lang p1l ON (p1.ID=p1l.ID_PAI)
			INNER JOIN filters_products fp ON (fp.prod_id=p1.ID)
			WHERE
				'.$aproved.'
				'.$w.'

		';
	$obj->db_open($db_link, $DB_name, $sql);
	// echo $sql.'<br />';


	$id_org_old = "";
	$cor = "#FFFFFF";

	$total = $obj->db_rows;
	$total2 = 0;
	$return = array();
	if(!$obj->EOF())
	{
		while(!$obj->EOF())
		{
			$id_org_old = $obj->db_data["ID_ORG"];
			$selected = '';

			$return[] = $obj->db_data["ID"];
			$obj->db_next();
		}

	}
    $obj->db_close();

	return $return;
    db_closedb_conection($db_link);
}
$produtos_selecionados = array();
function show_produtos($f_familia2="", $f_familia="")
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$Nome,$ID,$ID_Fam, $txt_email14;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$obj2=new DataObj;
	$obj3=new DataObj;

	if(isset($_POST['f_produto']) && ltrim(rtrim($_POST['f_produto']))!='')
	{
		$w .= '
				AND
					(p1.Ref Like "%'.ltrim(rtrim($_POST['f_produto'])).'%"
				OR
					p1l.Cabecalho Like "%'.ltrim(rtrim($_POST['f_produto'])).'%") ';
	}
	if(isset($_POST['s_marca']) && $_POST['s_marca'] > 0)
	{
		if($_POST['s_marca'] != "SM") $w .= ' AND p1.ID_Marca = "'.$_POST['s_marca'].'" ';
		else $w .= ' AND p1.ID_Marca = 0 ';
	}

	if($f_familia2) $w.= ' AND (fp.fam_id2 = "'.$f_familia2.'")';

	$aproved = "p1.Aproved = 1";

	$sql = '
			SELECT
				p1.* , p1l.Cabecalho
			FROM
				products p1
			INNER JOIN products_lang p1l ON (p1.ID=p1l.ID_PAI)
			INNER JOIN filters_products fp ON (fp.prod_id=p1.ID)
			WHERE
				'.$aproved.'
				'.$w.'

		';
	$obj->db_open($db_link, $DB_name, $sql);
	// echo $sql.'<br />';
	$sql3 = '
			SELECT
				p1l.promo_prod_id, p1l.promo_id, p1l.PROD_ID, p1l.FAMILIA, p.Ref, pl.Cabecalho
			FROM
				sales_promotions_products p1l
			INNER JOIN
				products p ON (p.ID=p1l.PROD_ID)
			INNER JOIN
				products_lang pl ON (pl.ID_PAI=p.ID)
			WHERE
				p1l.promo_id="'.$_GET['ID_PAI'].'"
			AND
				p1l.DESCONTO="'.$_GET['Desconto'].'"
			AND
				p1l.FAMILIA > 0
			AND
				p1l.MARCA=0
			ORDER BY
				p.Ref
		';
	$obj3->db_open($db_link,$DB_name,$sql3);
	while(!$obj3->EOF())
	{
		$array_data[] = $obj3->db_data['PROD_ID'];
		$array_data2[] = $obj3->db_data['FAMILIA'];

		$obj3->db_next();
	}
	$obj3->db_close();

	$id_org_old = "";
	$cor = "#FFFFFF";

	// $total = $obj->db_rows;
	$total2 = 0;
	$return = '';
	if(!$obj->EOF())
	{
		$return = '<table width="100%">';
		while(!$obj->EOF())
		{
			$id_org_old = $obj->db_data["ID_ORG"];
			$selected = '';

			if(in_array($obj->db_data["ID"],$array_data) && in_array($f_familia2, $array_data2))
			{
				$selected = "checked='checked'";
				$total2++;
			}

			$return .=  "<tr><td style='border-bottom:solid 1px #000' colspan='3' class='preto' ><div class='fam2_".$f_familia2."'><input ".$selected."  type='checkbox' name='produto[]' onclick='$(\"#fam2_".$f_familia2."\").attr(\"checked\", true);' value='".$obj->db_data["ID"].";".$f_familia2."' class='fam_".$f_familia."'> - " . $obj->db_data["Cabecalho"]."</div></td></tr>";
			if(isset($_POST['f_produto']) && ltrim(rtrim($_POST['f_produto']))!='' || isset($_POST['s_marca']) && $_POST['s_marca'] > 0 || isset($_POST['s_familia']) && $_POST['s_familia']>0)
			{
				$return .= "<input type='hidden' name='produto_hidden[]' value='".$obj->db_data["ID"].";".$f_familia2."' />";
			}
			$obj->db_next();
		}
		$return .= '</table>';
	}
    $obj->db_close();

	return array('list_prods' => $return, 'total_prod' => $total, 'total_prd_prom' => $total2);
    db_closedb_conection($db_link);
}
?>
<script type="text/javascript">document.title="SELECIONAR FAMILIAS PARA DESCONTOS"; </script>
</body>
</html>