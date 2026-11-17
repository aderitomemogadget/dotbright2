<?php
	include "settings.inc.php";
	global $DB_host,$db_link,$DB_name,$DB_user,$DB_pass;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	include "../layout/header.inc.php";	
	include "../layout/body.inc.php";
?>
<table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
	<td valign="top" align="left">
		<?php 
			include "../layout/menus.inc.php";
			
			
		?>
		<form action="" name="lista_paginas" method="get">
		<?php
			show_page_tools_stocks(filter);
			show_content_list_stocks();
			show_page_bottom_tools_stocks();
		?>
		</form>
	</td>
	<td width="10px">&nbsp;</td>
	<td valign="top"><?php show_fast_tools();?></td>
</tr>
</table>
<?php include "../layout/bottom.inc.php";?>