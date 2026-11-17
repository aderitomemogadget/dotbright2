<?php
	include "settings.inc.php";
	include "../layout/header.inc.php";
	include "../layout/body.inc.php";

	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang, $tem_loja, $tem_area_reservada;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$fields=db_list_fields($DB_name,'servicos_extra',$db_link);
	$columns=db_num_fields($fields);
	for($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
	if(!in_array('img_ext',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE servicos_extra ADD img_ext varchar(255) NOT NULL AFTER bloco_ext');

	acoes_selecionados();
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
	<td width="10px">&nbsp;</td>
	<td valign="top" width="200"><?php show_fast_tools();?></td>
	</tr>
	</table>
	</form>

<script src="chosen.jquery.min.js" type="text/javascript"></script>
<script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true}); </script>
<?php require_once '../layout/bottom.inc.php';?>