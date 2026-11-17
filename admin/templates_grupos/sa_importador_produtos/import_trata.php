<?php
	ini_set('max_execution_time','0');
	ini_set('memory_limit','1024M');
    require_once('settings.inc.php');
    require_once('../layout/header.inc.php');
    require_once('../layout/body.inc.php');
?>
<style> .botao{ padding:0 5px} </style>
<table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
	<td valign="top" align="left"><?php require_once('../layout/menus.inc.php'); ?>
		<table class="p1" width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr><td style="color:#FFF" align="center"><h3>Importa&ccedil;&atilde;o de produtos</h3></td></tr>
		</table>
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td style="color:#000;padding:10px;font-family:monospace;font-size:15px;" align="left"  bgcolor="#FFF"><?
			$ficheirocsv['name'] = $_FILES['import_file']['name'];
			$ficheirocsv['temp'] = $_FILES['import_file']['tmp_name'];


			$ext = pathinfo($ficheirocsv['name'], PATHINFO_EXTENSION);
			if($ext!='csv' && $ext!='CSV')
			{
			   echo '<div style="color:#F00;text-align:center">ERRO: tipo de ficheiro errado. Deve ser csv<br><br><input class="botao" type="button" name="voltar" value="Voltar" onclick="window.open(\'import.php\',\'_self\');"></div>';
			}
			else
			{
				// $nome_ficheiro = limpa_special(str_replace('.'.$ext, '', $ficheirocsv['name']));
				// exit;
				$filename = 'importa_manual';
				$result = copia_segura($ficheirocsv['temp'],  dirname(__FILE__) .'/../../files/importar/'.$filename.'.csv');
				
				echo '<script>window.open("import.php","_self");</script>';
			}
			
			?>
			</td>
		</tr>
		</table>
		<table class="p1" width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr><td style="color:#FFF" align="center">&nbsp;</td></tr>
		</table>
	</td>
	<td width="10px">&nbsp;</td>
	<td valign="top"></td>
</tr>
</table>
	<? require_once('../layout/bottom.inc.php');?>