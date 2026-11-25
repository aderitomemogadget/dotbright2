<?php
	include "settings.inc.php";

	$arr_cat = getFiltros1();
	?><option value="">Associar filtro</option><?php	
	foreach($arr_cat as $categoria)
	{
		?><option value="<?php echo $categoria[0]?>" <?php if(is_array($prd_fltrs) && $prd_fltrs[$a]['cat_id'] == $categoria[0]) echo "selected"; ?> ><?php echo $categoria[1]?></option><?php					
	}
?>