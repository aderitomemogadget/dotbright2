<?php
require_once "settings.inc.php";
ini_set('meemory_limit', '1000M');
ini_set('post_max_size', '1000M');
ini_set('upload_max_filesize', '1000M');

	$DESCONTOf = str_replace('.','_',$_GET['desconto']);
	$DESCONTOf2 = str_replace('_','.',$_GET['desconto']);
	
	
	?><table width="100%" border="0" align="left"><?php			

	$prods_list = show_produtosp($_GET['tpromo']);
	if($prods_list['list_prods'])
	{
		echo '<tr><td  style="border-bottom:solid 1px #000; " class=\'preto\' align=\'center\'><input type="button" onclick="submeter_produtos(\''.$DESCONTOf.'\',\''.$DESCONTOf2.'\',\''.$_GET['ID_PAI'].'\',\''.$_GET['tpromo'].'\');" value="Submeter Produtos" class="botao" /></td></tr>';
		echo "<tr bgcolor='".$cor."'><td colspan=\"2\" class='preto' align='left' height='25'>";
		echo "<div style='float:left;width:95%'><b> <span style='color:#008B45'>".$prods_list['list_prods']."</span></b></div>";
		echo "</td></tr>";
	}
	else
	{
		echo "<tr><td  colspan=\"2\" style='border-bottom:solid 1px #000; height:30px;' class='preto' align='center'><b>Não foram encontrados registos para a pesquisa efetuada!</b></td></tr>";
	}
	?>		
	</table><?
	

?>