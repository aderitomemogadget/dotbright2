<?php
    require_once('settings.inc.php');
    require_once('../../layout/header.inc.php');
    require_once('../../layout/body.inc.php');
?>
<table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td valign="top" align="left"><?php require_once('../../layout/menus.inc.php'); ?>
            <table class="p1" width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr><td align="center"><h3  style="color: #fff">Importa&ccedil;&atilde;o de produtos DOTBRIGHT</h3></td></tr>
            </table>
            <form name="form1" method="post" action="import_trata.php" enctype="multipart/form-data">
                <table width="100%" border="0" cellpadding="0" cellspacing="0"  style="background-color:#FFF">
				<tr>
				<td  align="left" width="120" style="padding:10px">Importar ficheiro <b>CSV</b>:</td>
				<td align="left" valign="top" style="padding:10px"><input type="file" name="import_file">&nbsp;<input type="submit" class="btn btn-primary" value="Importar"></td>
				</tr>
				<tr><td style="background-color:#FFF" colspan="2" height="25" align="center">
				<b><div id="carregar" style="padding:10px 0;">Existe um ficheiro na pasta: files/importar/dotbright_feed_export.txt<br><br><input type="button" name="atualizar" id="atualizar" value="Atualizar Base Dados" onclick="callfile();return false;" class="btn btn-primary" /></div>
				<div id="carregar2" style="display:none"><div></div><img src="loading.gif" border="0" style="vertical-align: middle;" /> a importar...<br><br><a href="#" onclick="cancelfile();return false;">Cancelar</a><br><br></div></b>
				</td></tr>


                </table>
            </form>
            <table class="p1" width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr><td style="color:#FFF" align="center">&nbsp;</td></tr>
            </table>
            <img src="../../images/bsh.png" width="100%" height="30px">
        </td>
    </tr>
</table>
<?php
?>
<script>
$( document).ready(function()
{
	// $.ajax({type: 'GET',url: "verifica.php",dataType: 'html',cache: false, success: function(id){if(id=='-1'){document.getElementById('carregar').style.display="block";document.getElementById('carregar2').style.display="none";}else if(id==0){document.getElementById('carregar').style.display="none";document.getElementById('carregar2').style.display="none";}else{ if(Number(id) > Number($('#mypercold').val(id))){ $('#mypercold').val(Number(id));};document.getElementById('carregar').style.display="none"; document.getElementById('carregar2').style.display="block";}}	});
	$.ajax({type: 'GET',url: "verifica.php",dataType: 'html',cache: false, success: function(id){if(id=='-1'){document.getElementById('carregar').style.display="block";document.getElementById('carregar2').style.display="none";}else if(id==0){document.getElementById('carregar').style.display="none";document.getElementById('carregar2').style.display="none";}else{ if(id == 'ON'){};document.getElementById('carregar').style.display="none"; document.getElementById('carregar2').style.display="block";}}	});
	var tt = setInterval(function()
	{
		// $.ajax({type: 'GET',url: "verifica.php",dataType: 'html',cache: false, success: function(id){if(id=='-1'){document.getElementById('carregar').style.display="block";document.getElementById('carregar2').style.display="none";}else if(id==0){document.getElementById('carregar').style.display="none";document.getElementById('carregar2').style.display="none";}else{ if(Number(id) > Number($('#mypercold').val(id))){ $('#mypercold').val(Number(id));};document.getElementById('carregar').style.display="none"; document.getElementById('carregar2').style.display="block";}}	});
		$.ajax({type: 'GET',url: "verifica.php",dataType: 'html',cache: false, success: function(id){if(id=='-1'){document.getElementById('carregar').style.display="block";document.getElementById('carregar2').style.display="none";}else if(id==0){document.getElementById('carregar').style.display="none";document.getElementById('carregar2').style.display="none";}else{if(id == 'ON'){};document.getElementById('carregar').style.display="none"; document.getElementById('carregar2').style.display="block";}}	});
	},
	2000);
});
function callfile()
{
	$.ajax({type: 'GET',url: "utils/run.php",dataType: 'html',cache: false, success: function(id){}});
	document.getElementById('carregar').style.display="none";
	document.getElementById('carregar2').style.display="block";
}
function cancelfile()
{
	$.ajax({type: 'GET',url: "utils/cancel.php",dataType: 'html',cache: false, success: function(id){}});
	// document.getElementById('carregar').style.display="none";
	// document.getElementById('carregar2').style.display="block";
}
</script>
<?php require_once('../../layout/bottom.inc.php');?>