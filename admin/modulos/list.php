<?php
	include "settings.inc.php";
	include "../layout/header.inc.php";
	include "../layout/body.inc.php";
	acoes_selecionados();
	?>
	<link rel="stylesheet" type="text/css" href="chosen.css?dp=<?=dumpacache?>" />
	<script language="JavaScript" src="sa.js"></script>
	<form action="list.php" method="post" name="lista_paginas" id="lista_paginas">
	<table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td valign="top" align="left">
		<?php
			require_once '../layout/menus.inc.php';
			show_page_tools();
			show_content_list();
	 		if($GLOBALS['dados']['Grupo_ID']==1 || $GLOBALS['dados']['Grupo_ID']==4 || ($GLOBALS['dados']['Grupo_ID']==5 && substr_count($_SERVER['SCRIPT_NAME'], 'pagina.php'))) show_page_bottom_tools();
		?>
		</td>
		<?php if($GLOBALS['dados']['Grupo_ID']==1 || $GLOBALS['dados']['Grupo_ID']==4 || ($GLOBALS['dados']['Grupo_ID']==5 && substr_count($_SERVER['SCRIPT_NAME'], 'pagina.php')))
		{?>
			<td width="10px">&nbsp;</td>
			<td valign="top" width="200px"><?php show_fast_tools(); ?></td>
		<?php }?>
	</tr>
	</table>
	</form>
	<script src="chosen.jquery.min.js" type="text/javascript"></script>
	<script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true}); </script><?php
	require_once '../layout/bottom.inc.php';
?>