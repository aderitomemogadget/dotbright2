<?php
	ini_set('max_execution_time','0');
	ini_set('memory_limit','1024M');

	if(isset($argv) && $argv[1] > 0)$Supplier_ID=intval($argv[1]);
	if(isset($_GET['Supplier_ID']))$Supplier_ID=intval($_GET['Supplier_ID']);


    require_once('settings.inc.php');
    require_once('../../layout/header.inc.php');
    require_once('../../layout/body.inc.php');?>
	<script>
	function run_conection()
	{
		$.ajax({type:'GET',url:"importa/corre.php?Supplier_ID=<?=$Supplier_ID?>",dataType:'html',cache:false,success:function(id){
		alert("A importação foi iniciada, acompanhe o seu estado acedendo aos logs");
		window.open("import.php?Supplier_ID=<?=$Supplier_ID?>","_self");
	}});
}
</script>
<style>.botao{padding:0 5px}</style>
<table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
	<td valign="top" align="left"><?php require_once('../../layout/menus.inc.php');?>
		<table class="p1" width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr><td style="color:#FFF" align="center"><h3>Importa&ccedil;&atilde;o de produtos</h3></td></tr>
		</table>
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td style="color:#000;padding:10px;font-family:monospace;font-size:15px" align="left" bgcolor="#FFF"><?
			$ficheirocsv['name'] = $_FILES['import_file']['name'];
			$ficheirocsv['temp'] = $_FILES['import_file']['tmp_name'];
			$ext=pathinfo($ficheirocsv['name'], PATHINFO_EXTENSION);
			if($ext!='csv' && $ext!='CSV' && $ext!='xlsx' && $ext!='XLSX')
			{
			   echo '<div style="color:#F00;text-align:center">ERRO: tipo de ficheiro errado. Deve ser CSV ou XLSX<br><br><input class="botao" type="button" name="voltar" value="Voltar" onclick="window.open(\'import.php?Supplier_ID='.$Supplier_ID.'\',\'_self\');"></div>';
			}
			else
			{
				$filename = 'importa_manual'.$Supplier_ID;
				$result = copia_segura($ficheirocsv['temp'],  dirname(__FILE__) .'/../../../files/importar/'.$filename.'.'.$ext);


				echo '<div style="color:#F00;text-align:center">';
				echo 'Ficheiro copiado com sucesso para o servidor<br><br>';
				echo '<input class="botao" type="button" name="voltar" value="Voltar" onclick="window.open(\'import.php?Supplier_ID='.$Supplier_ID.'\',\'_self\');"><br>';
				echo '<input class="btn btn-primary" value="Iniciar a importação" type="button" style="width:200px" onclick="run_conection();">';
				echo '</div>';

				//echo '<div style="width:145px;display:inline-block;padding:5px 0">Correr Importação</div><div style="width:250px;display:inline-block;padding:5px 0"><input class="btn btn-primary" value="Iniciar a importação" type="button" style="width:100px" onclick="run_conection();"></div><br>';
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
<? require_once('../../layout/bottom.inc.php');?>