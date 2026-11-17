<?
	include "settings.inc.php";
	$TableName="form_fields";
	include "../layout/header.inc.php";
	include "../layout/body.inc.php";
?>
<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
<tr>
	<td valign="top" align="left"><?
		show_page_tools(filter);
		show_content_list();?>
	</td>
	<td width="10px">&nbsp;</td>
	<td valign="top" width="200"><? show_fast_tools();?></td>
</tr>
</table>
<? 	include "../layout/bottom.inc.php";?>