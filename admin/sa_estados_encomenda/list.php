<?php
	include "settings.inc.php";
	include "../layout/header.inc.php";
	include "../layout/body.inc.php";

	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$field_array = array();
	$fields=db_list_fields($DB_name,'encomendas_estados',$db_link);
	$columns=db_num_fields($fields);
	for ($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
	if(!in_array('Cor', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE encomendas_estados ADD Cor varchar(7) DEFAULT ""');  }
	if(!in_array('Email', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE encomendas_estados ADD Email tinyint(1) DEFAULT 0');  }
	if(!in_array('Pagamento_recebido', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE encomendas_estados ADD Pagamento_recebido tinyint(1) DEFAULT 0');  }
	if(!in_array('Obs', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE encomendas_estados ADD Obs text DEFAULT ""');  }


	$fields=db_list_fields($DB_name,'encomendas_estados_lang',$db_link);
	$columns=db_num_fields($fields);
	for ($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
	if(!in_array('Cabecalho2', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE encomendas_estados_lang ADD Cabecalho2 varchar(255) DEFAULT ""');  }
	if(!in_array('Texto', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE encomendas_estados_lang ADD Texto text DEFAULT ""');  }




	acoes_selecionados();
	?>
	<link rel="stylesheet" type="text/css" href="sa.css" />
	<link rel="stylesheet" type="text/css" href="chosen.css?dp=<?php echo dumpacache?>" />
	<script language="JavaScript" src="sa.js"></script>
	<form action="list.php" method="post" name="lista_paginas" id="lista_paginas">
	<script>
	$(document).ready(function()
	{
		$('.fancybox_img').fancybox();
		$('.fancybox2').live('click',function(){ var href = $(this).attr('href'); $.fancybox({ 'showCloseButton' : false, 'titlePosition' : 'inside', title : true, autoSize : false, modal : false, fitToView : false, topRatio : 0, content : '<iframe width="100%" height="100%" src="'+href+'"></iframe>', width : 754, maxHeight : 600, scrolling : 'no', type : 'html', onComplete: function() {$("#fancybox-wrap").stop().animate({'top':'10px',}, 0); $("#fancybox-wrap").css({'z-index':'9999999999999999',}, 0); } }); return false;});
	});
	function addToProduct(){
		$("#s_listafamiliasprod").append($("#listademarcas option:selected"));
	}
	function removeFromProduct(){
		$("#listademarcas").append($("#s_listafamiliasprod option:selected"));
	}
	</script>
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