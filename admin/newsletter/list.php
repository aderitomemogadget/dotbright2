<?php
	require_once('settings.inc.php');
	if(!$ordenar) $ordenar = 'Data';
	require_once('../layout/header.inc.php');
	require_once('../layout/body.inc.php');
?>
<table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td valign="top" align="left">
		<?php
			show_content_list();
		?>
		</td>
		<td width="10px">&nbsp;</td>
		<td valign="top" width="200"><?php show_fast_tools();?><?php show_page_config();?></td>
	</tr>
</table>
<?php require_once('../layout/bottom.inc.php');?>