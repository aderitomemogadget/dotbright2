<?php
	require_once "settings.inc.php";
	if($task=="apagar"){$task_button = write_lang('key_apagar_este_registo','Apagar este Registo');$task_form="apaga";}
	elseif(isset($_GET['duplicar']) && $_GET['duplicar']==1){$task_button = write_lang('key_duplicar_este_registo','Duplicar este Registo');$task_form="duplicar";}
	elseif($_GET[TBID]==0){$task_button = write_lang('key_gravar_novo_registo','Gravar Novo Registo');$task_form="insere";}
	else {$task_button = write_lang('key_gravar_alteracoes','Gravar alterações');$task_form="altera";}
	$D = getdate();

	acoes_selecionados();

	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$id_pai = 0; $tem_voucher_prods = 0;
	if(isset($_GET[TBID]) && $_GET[TBID]!=0)
	{
		$rs = new DataObj; $rs2 = new DataObj;
		$sql = 'SELECT '.implode(',',$CMPS_MODULO_DADOS).' FROM '.TBMODULO.' WHERE '.TBID.'='.$_GET[TBID];

		$rs->db_open($db_link, $DB_name, $sql);
		if(!$rs->EOF())
		{
			$id = $rs->db_data['id'];
			$familia_id = $rs->db_data['familia_id'];
			$marca_id = $rs->db_data['marca_id'];
			$promo_ativo = $rs->db_data['promo_ativo'];
			$valor = $rs->db_data['valor'];
			$valor_promo = $rs->db_data['valor_promo'];
			$ini = $rs->db_data['ini'];
			$fim = $rs->db_data['fim'];
			$visivel = $rs->db_data['visivel'];
			$titulo = $rs->db_data['titulo'];
			$info = htmlentities($rs->db_data['info']);


			$rs3 = new DataObj;
			$sql = "SELECT count(promo_id) as TOTAL FROM products_sync_web_prods where promo_id='".$id."'";

			$rs3->db_open($db_link,$DB_name,$sql);
			if(!$rs3->EOF()) $tem_voucher_prods = $rs3->db_data['TOTAL'];
			$rs3->db_close();

		}
		$rs->db_close();
	}

	if(!$caminho_bt_editor){$caminho_bt_editor1='../../../editor/';$caminho_bt_editor2='../editor/';}
	else{$caminho_bt_editor1=$caminho_bt_editor2=$caminho_bt_editor;}

	require_once "../layout/header.inc.php";
	require_once "../layout/body.inc.php";
?>
<script type="text/javascript" src="../editor/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../editor/ckeditor/ckfinder/ckfinder.js"></script>
<link rel="stylesheet" type="text/css" href="sa.css" />
<link rel="stylesheet" type="text/css" href="chosen.css?dp=<?php echo dumpacache?>" />
<script>var tabsel = '<?php echo (isset($_GET['tab']) && $_GET['tab'] > 0) ? $_GET['tab'] : '1';?>';</script>
<script>var tabsel2 = '<?php echo (isset($_GET['tab2']) && $_GET['tab2'] > 0) ? $_GET['tab2'] : '1';?>';</script>
<script>var tabsel3 = '<?php echo (isset($_GET['tab3']) && $_GET['tab3'] > 0) ? $_GET['tab3'] : '1';?>';</script>
<script>var SITE_HTTP_URL = '<?php echo $GLOBALS['site_http_url']?>';var SITE_SHELL_URL = '<?php echo $GLOBALS['site_shell_url']?>';</script>
<script>$(document).ready(function()
{
	$('.fancybox_img').fancybox();
	$('.fancybox2').live('click', function(){ var href = $(this).attr('href'); $.fancybox({ 'showCloseButton' : false, 'titlePosition' : 'inside', title : true, autoSize : false, modal : false, fitToView : false, topRatio : 0, content : '<iframe width="100%" height="100%" src="'+href+'"></iframe>', width : 754, maxHeight : 600, scrolling : 'no', type : 'html', onComplete: function() {$("#fancybox-wrap").stop().animate({'top':'10px',}, 0); $("#fancybox-wrap").css({'z-index':'9999999999999999',}, 0); } }); return false;});
	$('.fancybox3').live('click', function(){ var href = $(this).attr('href'); $.fancybox({ 'showCloseButton' : false, 'titlePosition' : 'inside', title : true, autoSize : false, modal : false, fitToView : false, topRatio : 0, content : '<iframe width="100%" height="100%" src="'+href+'"></iframe>', width : 1000, maxHeight : 600, scrolling : 'no', type : 'html', onComplete: function() {$("#fancybox-wrap").stop().animate({'top':'10px',}, 0); $("#fancybox-wrap").css({'z-index':'9999999999999999',}, 0); } }); return false;});
	$('#familia_id').on('change', function(e)
	{
		var items = [];
		$('#familia_id option:selected').each(function(){ items.push($(this).val()); });
		var result = items.join(',');

		// console.log(result);
		$.get('ajax_marcas.php?dp='+new Date()+'&fam_id='+result, function(data)
		{
			$('#load_brads').html(data);
			$(".form-control2").chosen({ placeholder_text_single: "Todas"});
		});


	});

	var items = [];
	$('#familia_id option:selected').each(function(){ items.push($(this).val()); });
	var result = items.join(',');

	// console.log(result);
	$.get('ajax_marcas.php?dp='+new Date()+'&fam_id='+result+'&id_org=<?php echo $marca_id;?>', function(data)
	{
		$('#load_brads').html(data);
		$(".form-control2").chosen({ placeholder_text_single: "Todas"});
	});

});
function addToProduct(){
	$("#s_listafamiliasprod").append($("#listademarcas option:selected"));
}
function removeFromProduct(){
	$("#listademarcas").append($("#s_listafamiliasprod option:selected"));
}

</script>
<script language="JavaScript" src="sa.js"></script>
<div class="center" style="position:relative;" id="wp">
<form name="form" method="post" action="pagina_trata.php?<?php echo TBID?>=<?php echo $id?>&task_form=<?php echo $task_form?>&dump=<?php echo uniqID("")?>" enctype="multipart/form-data">
<input type="hidden" name="m_lang" value="<?php echo $GLOBALS['admin_lang'];?>" />
<input type="hidden" name="<?php echo TBID?>" value="<?php echo (isset($_GET['duplicar']) && $_GET['duplicar'] > 0) ? 0 : $id;?>" />
<input type="hidden" name="duplicar" value="<?php echo (isset($_GET['duplicar']) && $_GET['duplicar'] > 0) ? 1 : 0;?>" />
<input type="hidden" name="ctab" id="ctab" value="<?php echo (isset($_GET['tab']) && $_GET['tab'] > 0) ? $_GET['tab'] : '1';?>" />
<input type="hidden" name="ctab2" id="ctab2" value="<?php echo (isset($_GET['tab2']) && $_GET['tab2'] > 0) ? $_GET['tab2'] : '1';?>" />
<input type="hidden" name="ctab3" id="ctab3" value="<?php echo (isset($_GET['tab3']) && $_GET['tab3'] > 0) ? $_GET['tab3'] : '1';?>" />
<input type="hidden" name="slashes" value="'" />
<table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
<td valign="top" align="left"><?php include "../layout/menus.inc.php"; ?>
<table class="new_quadros" style="background:#FFF" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
<tr>
<td valign="top">
<table width="100%" border=0 cellspacing=0 cellpadding="5" class="subtitulo_janela">
<tr>
<td valign="middle"><a href="list.php" class="brancoh"> &#60; Voltar para a lista de <?php echo MODULO?></a> <?php echo show_menupath(((isset($_GET['id_pai']) && $_GET['id_pai'] > 0) ? $_GET['id_pai'] : $_GET['id']),'blocos');?></td>
</tr>
</table>
</tr>
<tr>
<td align="left" valign="top" class="preto" style="background:#FFF">
<div class="wraper" id="wraper">

<div class="bigtitletab" onclick="infinittab(1);return false;" id="menutab1"><?php echo MODULO?></div>
<div class="clearboth"></div>
<div class="tabtop-open3" id="tab1"><?php require_once 'tab_dados.php';?></div>
</div>
<?php show_page_buttons();?>
</td>
</tr>
</table>
<img src="../images/bsh.png" width="100%" height="30px">
</td>
<td width="10px">&nbsp;</td>
<td valign="top"><?php //show_fast_tools();?></td>
</tr>
</table>
</form>

</div>
<script src="chosen.jquery.min.js" type="text/javascript"></script>

<script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true}); $(".form-control2").chosen({ placeholder_text_single: "Todas"});</script><br /><br />
<?php require_once "../layout/bottom.inc.php";?>