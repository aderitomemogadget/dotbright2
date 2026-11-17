<?php

include "settings.inc.php";
include "../layout/header.inc.php";
include "../layout/body.inc.php";

global $site_http_url,$site_shell_url;

$count=0;
?>
<link href="../../../layout/template_admin.css" rel="stylesheet" type="text/css">
<script src="../../../external_api/jquery-1.7.1.min.js"></script>
<script>
function verifica_file(file_imp, result_file, nome)
{
	$.ajax({
	  url: file_imp,
	  success: function(data)
	  {
		if(data=="|impon") $('#'+result_file).html("<img src='loading.gif' border='0' align='absmiddle' style='width:20px' /> <span style='font-size:10px;color:#0029dc;font-weight:normal'><b>A gerar "+nome+"...</b></span>");
		else if(data=="off") $('#'+result_file).html("<a class='preto' href='#' onclick=forca_file('executa.php?metodo="+nome+"')><img src='bt_conf.png' align='absmiddle' border='0' />&nbsp;Gerar Todas</a>");
	  }
	});
}

function forca_file(file)
{
	$.ajax({
	  url: file,
	  success: function(data){}
	});
}

var familias = setInterval(function(){verifica_file("../gerafiltros_verifica.php?files=familias", "res1","familias");},2500);
var marcas = setInterval(function(){verifica_file("../gerafiltros_verifica.php?files=marcas", "res2","marcas");},2500);
</script>

<?php
		for($f=0;$f<count($ficheiros_importar);$f++)
		{
			$target = $ficheiros_importar[$f];
			echo '<script></script>';
			echo '<span id="res'.$f.'">&nbsp;</span><br />';
		}
		?>
<table class="listagem" width="100%" border="0" cellspacing="0" cellpadding="0">
<?php
	if($count % 2 !=0) $cor="#E0E0E0";
	else $cor="#F6F6F6";
	$count++;
?>
<tr>
<td height="25" bgcolor="<?php echo $cor;?>">&nbsp;</td>
<td bgcolor="<?php echo $cor;?>"></td>
<td bgcolor="<?php echo $cor;?>" style="font-size:14px">Familias / Subfamilias</td>
<td bgcolor="<?php echo $cor;?>" align="right"><span id="res1">&nbsp;</span>&nbsp;&nbsp;&nbsp;</td>
<td bgcolor="<?php echo $cor;?>">&nbsp;</td>
</tr>
<?php
	if($count % 2 !=0) $cor="#E0E0E0";
	else $cor="#F6F6F6";
	$count++;
?>
<tr>
<td height="25" bgcolor="<?php echo $cor;?>">&nbsp;</td>
<td bgcolor="<?php echo $cor;?>"></td>
<td bgcolor="<?php echo $cor;?>" style="font-size:14px">Marcas</td>
<td bgcolor="<?php echo $cor;?>" align="right"><span id="res2">&nbsp;</span>&nbsp;&nbsp;&nbsp;</td>
<td bgcolor="<?php echo $cor;?>">&nbsp;</td>
</tr>
</table>
<script>
forca_file('<?=$site_http_url?>admin/filters/cache/executa.php?metodo=familias');
forca_file('executa.php?metodo=marcas');
</script>
<?php include "../layout/bottom.inc.php";?>