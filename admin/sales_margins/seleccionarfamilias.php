<?php
require_once "settings.inc.php";
ini_set('meemory_limit', '1000M');
ini_set('post_max_size', '1000M');
ini_set('upload_max_filesize', '1000M');

if ($_POST && isset($_POST["adicionar"])) //todos
{
	$families = $_POST['familias'];

	global $DB_host, $DB_user, $DB_pass, $DB_name;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$sql = 'DELETE FROM sales_margins_families WHERE margin_id="'.$_GET['ID_PAI'].'"';
	$res = Runquery($db_link, $DB_name, $sql);

	foreach ($families as $ID)
	{
		$sql='INSERT INTO sales_margins_families (margin_id, FAMILIA) VALUES ("'.$_GET['ID_PAI'].'","'.$ID.'")';
		$res = Runquery($db_link, $DB_name, $sql);
	}
	db_closedb_conection($db_link);

}

function getFamilies($ID_ORG=0,$ID='') //todoss
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$obj = new DataObj;
	if($ID != '')
	{
		 $sql='SELECT pf.ID,pf.ID_ORG,pfl.Cabecalho
			FROM
				products_families AS pf,
				products_families_lang AS pfl
			WHERE
				pf.ID = pfl.ID_PAI AND pfl.Lang="'.$GLOBALS['admin_lang'].'" AND pf.ID = "'.$ID.'"';
	}
	else $sql = 'SELECT pf.ID,pf.ID_ORG,pfl.Cabecalho
			FROM
				products_families AS pf,
				products_families_lang AS pfl
			WHERE
				pf.ID = pfl.ID_PAI AND pfl.Lang="'.$GLOBALS['admin_lang'].'" AND pf.ID_ORG = "'.$ID_ORG.'"
			ORDER BY
				pf.ID';

	$obj->db_open($db_link,$DB_name,$sql);
	return $obj;
}


function getSalesMarginsFamilies() //todoss
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$obj = new DataObj;
	$sql =
			'
			SELECT
				FAMILIA
			FROM
				sales_margins_families
			WHERE
				margin_id = "'.$_GET['ID_PAI'].'"
			';
	$obj->db_open($db_link,$DB_name,$sql);
	return $obj;
}


?><!DOCTYPE html>
<html>
<head>
<title>Title of the document</title>
<script src="../../external_api/datepicker/jquery-1.8.2.js"></script>
<link rel="stylesheet" type="text/css" href="<?=$GLOBALS['site_http_url']?>admin/layout/bootstrap-3.3.7/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="<?=$GLOBALS['site_http_url']?>admin/admin_estilos.css" />
<link rel="stylesheet" type="text/css" href="<?=$GLOBALS['site_http_url']?>admin/layout/reveal/jquery.fancybox.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?=$GLOBALS['site_http_url']?>admin/layout/fontawesome-free-5.7.2-web/css/all.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?=$GLOBALS['site_http_url']?>admin/layout/theme/plugins/bootstrap/bootstrap-4.1.1/css/bootstrap-grid.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?=$GLOBALS['site_http_url']?>admin/layout/theme/plugins/bootstrap-calendar/css/bootstrap_calendar.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?=$GLOBALS['site_http_url']?>admin/layout/theme/plugins/jquery-jvectormap/jquery-jvectormap-1.2.2.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?=$GLOBALS['site_http_url']?>admin/layout/jquery-ui-1.10.3.css" />
<link rel="stylesheet" type="text/css" href="<?=$GLOBALS['site_http_url']?>admin/layout/datepicker/jquery-ui-timepicker-addon.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?=$GLOBALS['site_http_url']?>admin/layout/theme/css/animate.min.css" />
<link rel="stylesheet" type="text/css" href="<?=$GLOBALS['site_http_url']?>admin/layout/theme/css/style.min.css" />
<link rel="stylesheet" type="text/css" href="<?=$GLOBALS['site_http_url']?>admin/layout/theme/css/style.css" />
<link rel="stylesheet" type="text/css" href="<?=$GLOBALS['site_http_url']?>admin/layout/theme/css/style-responsive.min.css" />
<link rel="stylesheet" type="text/css" href="sa.css" />
<style>html,body{background:#FFF;font-size:12px}input, select {height: auto;}</style>
<script>
<?php
if ($_POST && isset($_POST["adicionar"]))
{
	?>
	$(document).ready(function()
	{
		parent.location.reload();
		parent.$.fancybox.close();
	});
	<?php
}
?>
</script>

</head>
<body>
<div class="center" id="laod" style="display:none;position:absolute;top:0px;left:0px;width:100%;height:1px;border:#FFF 0px solid;z-index:1000;background:#c0c0c0; height:100px;padding-top:50px; text-align:center; font-size:15px">Não saia desta página, o script está a ser executado</div>
<table width="100%" bgcolor="#fafafa" border=0 cellspacing=0 cellpadding=1>
<tr>
<td bgcolor="#e3e5e3" colspan="10" height="25px" align="center" class='preto'><font class="red" style="font-size:20px"><b>SELEÇÃO DE FAMILIAS</b></font></td>
</tr>
</table>
<form action="seleccionarfamilias.php?ID_PAI=<?php echo $_GET['ID_PAI']; ?>&Desconto=<?php echo $_GET['Desconto']; ?>&Linha=<?php echo $_GET['Linha']; ?>"  method="post" name="formConfirmar" onSubmit="submit_loading();return true;">

	<input type="hidden" value="<?php echo $_POST['s_marca']?>" name="s_marca" />
	<input type="hidden" value="<?php echo $_POST['s_familia']?>" name="s_familia" />
	<input type="hidden" value="<?php echo $_POST['f_produto']?>" name="f_produto" />
	<input type="hidden" value="1" name="adicionar" />

	<table width="100%" border="0" align="left">
	<?php

	$obj_smf = getSalesMarginsFamilies();
	if(!$obj_smf->EOF())
	{
		while(!$obj_smf->EOF())
		{
			$IDsmf[] 	= $obj_smf->db_data['FAMILIA'];
			$obj_smf->db_next();
		}
	}
	$obj_smf->db_close();

	$obj_pf = getFamilies(0,$s_familia);
	$n=0;

	if(!$obj_pf->EOF())
	{
		while(!$obj_pf->EOF())
		{
			$IDpf 		= $obj_pf->db_data['ID'];
			$ID_ORGpf 	= $obj_pf->db_data['ID_ORG'];
			$namepf 	= $obj_pf->db_data['Cabecalho'];

			$obj_spf = getFamilies($IDpf);

			if(!$obj_spf->EOF())
			{
				echo "<tr><td colspan=\"2\" style='border-bottom:solid 1px #000; height:30px;' bgcolor=#E9F0B8 class='preto' align='center'><b> (".$namepf.")";
				echo ' &nbsp;&nbsp;&nbsp;&nbsp; Selecionar: <a href="javascript:;" onclick="SelectAll(\'fam_'. $IDpf.'\', true)" style="text-decoration:underline">Todos</a> &nbsp;/&nbsp; <a href="javascript:;" onclick="SelectAll(\'fam_'.$IDpf.'\', false)"  style="text-decoration:underline">Nenhum</a>';
				echo "</b></td></tr>";

				while(!$obj_spf->EOF())
				{
					if($cor == '#FFFFFF') $cor = '#F0F0F0';
					else  $cor = '#FFFFFF';

					$IDspf 		= $obj_spf->db_data['ID'];
					$namespf 	= $obj_spf->db_data['Cabecalho'];

					$selected = '';
					if(in_array($IDspf, $IDsmf)) $selected = "checked='checked'";

					echo "<tr bgcolor='".$cor."'><td style='border-bottom:solid 1px #000'  colspan=\"2\" class='preto' align='left'>";
					echo "<div style='float:left'><input type='checkbox' name='familias[]' ".$selected." onclick=\"SelectAll('', true, 'fam2_".$IDspf."', this.checked)\" value='".$IDspf."' class='fam_".$IDpf."' id='fam2_".$IDspf."' /></div>";
					echo "<div style='float:left'><b> <span style='color:#008B45'>&nbsp;&nbsp;".$namespf."</span></b> </div>";
					echo "</td></tr>";

					getsubFamilies($IDspf, $IDsmf);

					$obj_spf->db_next();
				}
			}

			$obj_spf->db_close();
			$obj_pf->db_next();
		}
	}
	$obj_pf->db_close();

	function getsubFamilies($IDspf, $IDsmf,$n=0)
	{
		$n++;
		$obj_pf = getFamilies($IDspf);
		if(!$obj_pf->EOF())
		{
			echo "<tr><td><table style='margin-top:5px;margin-bottom:5px;margin-left:".($n*15)."px'>";
			while(!$obj_pf->EOF())
			{
				$IDpf 		= $obj_pf->db_data['ID'];
				$ID_ORGpf 	= $obj_pf->db_data['ID_ORG'];
				$namepf 	= $obj_pf->db_data['Cabecalho'];

				$selected = '';
				if(in_array($IDpf, $IDsmf)) $selected = "checked='checked'";
				echo "<tr bgcolor='".$cor."'><td style='border-bottom:solid 1px #000'  colspan=\"2\" class='preto' align='left'>";
				echo "<div style='float:left'><input type='checkbox' name='familias[]' ".$selected." onclick=\"SelectAll('', true, 'fam2_".$IDpf."', this.checked)\" value='".$IDpf."' class='fam_".$IDpf."' id='fam2_".$IDpf."' /></div>";
				echo "<div style='float:left'><b> <span style='color:#008B45'>&nbsp;&nbsp;".$namepf."</span></b> </div>";
				echo "</td></tr>";

				getsubFamilies($IDpf, $IDsmf,$n);

				$obj_pf->db_next();
			}
			echo "</table></td></tr>";
		}
		$obj_pf->db_close();
	}

	echo "<tr><td  colspan=\"2\" style='border-bottom:solid 1px #000; height:30px;' class='preto' align='center'><b></b></td></tr>";
	?>

	</table>
	<div style="position:fixed;bottom:0;background:#FFF;width:100%;height:30px;z-index:999999">
	<table width="100%" border="0" align="left">
	<tr>
	<td style="height:30px; width:50%; text-align:right;"><input type="submit" name="adicionar1" value="GRAVAR" class="btn btn-primary" />&nbsp;&nbsp;&nbsp;</td>
	<td style="height:30px;  width:50%; text-align:left;">&nbsp;&nbsp;&nbsp;<input type="button" name="close" value="CANCELAR" onclick="parent.$.fancybox.close();" class="btn btn-secondary" /></td>
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
<script type="text/javascript">document.title="SELECIONAR FAMILIAS"; </script>
</body>
</html>