<?php
	include "settings.inc.php";
	include "../layout/header.inc.php";
	include "../layout/body.inc.php";

	?>
	<link rel="stylesheet" type="text/css" href="sa.css" />
	<link rel="stylesheet" type="text/css" href="chosen.css?dp=<?php echo dumpacache?>" />

	<script language="JavaScript" src="sa.js"></script>
	<form action="list.php" method="post" name="lista_paginas" id="lista_paginas">

	<table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
	<tr>
	<td valign="top" align="left">
	<?php
	require_once '../layout/menus.inc.php';
	show_page_tools();
	show_content_list();
	show_page_bottom_tools();
	?>
	</td>
	</tr>
	</table>
	</form>
	<script src="chosen.jquery.min.js" type="text/javascript"></script>
<?php require_once '../layout/bottom.inc.php';?>