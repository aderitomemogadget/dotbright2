<?php
    require_once('settings.inc.php');
    require_once('../layout/header.inc.php');
    require_once('../layout/body.inc.php');

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(0);

?>
<link rel="stylesheet" type="text/css" href="chosen/chosen.css?dp=<?php echo dumpacache?>" />
<style>
.botao{ padding:0 5px}
.container{ width: 100%; margin: 0 auto; }
.container label{ display:block;padding:5px 0 }
ul.tabs{ margin: 0px; padding: 0px; list-style: none; }
ul.tabs li{ background: none; color: #222; display: inline-block; padding: 10px 15px; cursor: pointer; font-weight:bold}
ul.tabs li.current{ background: #FFF; color: #222; }
.tab-content{ display: none; background: #FFF; padding: 15px; font-size:12px }
.tab-content.current{ display: inherit; }
</style>
<script type="text/javascript" src="../editor/ckeditor_4_5_1/ckfinder/ckfinder.js"></script>
<script type="text/javascript">
function BrowseServer()
{
	var finder = new CKFinder();
    finder.basePath = '../';
    finder.selectActionFunction = SetFileField;
    finder.popup();
}
function SetFileField( fileUrl )
{
    document.getElementById( 'xFilePath' ).value = fileUrl;
}
</script>
<table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td valign="top" align="left">
        	<?php require_once('../layout/menus.inc.php'); ?>
            <table class="p1" width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr><td style="color:#FFF" align="center"><h3>Importador de Imagens</h3></td></tr>
            </table>

			
        </td>
    </tr>
    <tr height="40" style="background-color:#DDD;">
    	<td>
    		Passos: <br><br>
			

    		1 - Copiar todas as imagens para a pasta /files/importar_imagens/ <input type="button" value="Browse Server" onclick="BrowseServer();" /><br>
    		Extensões aceites: jpg,JPG,gif,GIF,png,PNG,jpeg,JPEG<br>
    		formato da imagem:  ref_999.png em que ref corresponde a referencia do artigo<br>
    		<br>


    		2 - Clicar no botão abaixo:<br><br>
    		<form name="form1" method="post" action="grava_trata.php" enctype="multipart/form-data">
				<input type="hidden" name="importar_imagens" id="importar_imagens" value="1">
				
				<input type="submit" class="botao" value="Clique aqui para importar" /><br><br>
			</form>
    	</td>
    </tr>
</table>
<script src="chosen/chosen.jquery.min.js" type="text/javascript"></script>
<script type="text/javascript"> $(".chzn-select").chosen(); </script>

<?php require_once('../layout/bottom.inc.php');