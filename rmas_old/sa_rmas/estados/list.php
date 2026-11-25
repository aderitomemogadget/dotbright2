<?php	
	include "settings.inc.php";
	include "../../layout/header.inc.php";	
	include "../../layout/body.inc.php";
	
	acoes_selecionados();
?>
	<link rel="stylesheet" type="text/css" href="<?php echo CSS?>sa.css?dp=<?php echo dumpacache?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo CHOSEN?>chosen/chosen.css" />
	
	<script language="javascript" src="<?php echo JS?>sa.js"></script>
	<form action="list.php" method="post" name="lista_paginas" id="lista_paginas">
	<table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
	<tr>
	<td valign="top" align="left">
	<?php
	require_once '../../layout/menus.inc.php';
	show_page_tools();
	show_content_list();
	show_page_bottom_tools();
	?>
	</td>
	<td width="10px">&nbsp;</td>
	<td valign="top" width="160"><?php show_fast_tools();?></td>
	</tr>
	</table>
	</form>
	
<script src="<?php echo CHOSEN?>chosen.jquery.min.js" type="text/javascript"></script>
<script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true}); </script>
<?php require_once '../../layout/bottom.inc.php';?>