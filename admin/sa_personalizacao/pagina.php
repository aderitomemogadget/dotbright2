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
	$bloco_id_pai = 0;
	if(isset($_GET[TBID]) && $_GET[TBID]!=0)
	{

		$rs = new DataObj; $rs2 = new DataObj;
		$sql = 'SELECT '.implode(',', $CMPS_MODULO_DADOS).' FROM '.TBMODULO.' WHERE '.TBID.'='.$_GET[TBID];
		$rs->db_open($db_link, $DB_name, $sql);
		if(!$rs->EOF())
		{
			$bloco_id = $rs->db_data['bloco_id'];
			$m_familia = $rs->db_data['m_familia'];
			$m_marca = $rs->db_data['m_marca'];
			$bloco_id_pai = $rs->db_data['bloco_id_pai'];
			$bloco_visivel = $rs->db_data['bloco_visivel'];
			$bloco_ordem = $rs->db_data['bloco_ordem'];
			$bloco_data = $rs->db_data['bloco_data'];
			$bloco_data2 = $rs->db_data['bloco_data2'];
			$bloco_ext = $rs->db_data['bloco_ext'];
			$bloco_pdf_ext = $rs->db_data['bloco_pdf_ext'];
			$bloco_preco = $rs->db_data['bloco_preco'];
			$bloco_tipo = $rs->db_data['bloco_tipo'];
			$bloco_obrigatorio = $rs->db_data['bloco_obrigatorio'];

			$width = $rs->db_data['width'];
			$height = $rs->db_data['height'];

			$sql = 'SELECT '.implode(',',$CMPS_MODULO_DADOS_LANG).' FROM '.TBMODULO_LANG.' WHERE '.TBID.'='.$_GET[TBID].' AND bloco_lang="'.$GLOBALS['admin_lang'].'"';

			$rs2->db_open($db_link, $DB_name, $sql);
			if(!$rs2->EOF())
			{
				$bloco_titulo = limpa_editor_lixo(stripslashes(htmlentities($rs2->db_data['bloco_titulo'])));
				$bloco_resumo = limpa_editor_lixo(stripslashes($rs2->db_data['bloco_resumo']));
				$bloco_texto = limpa_editor_lixo(stripslashes($rs2->db_data['bloco_texto']));
			}
			$rs2->db_close();
		}
		$rs->db_close();
	}

	if(!$caminho_bt_editor){$caminho_bt_editor1='../../../editor/';$caminho_bt_editor2='../editor/';}
	else{$caminho_bt_editor1=$caminho_bt_editor2=$caminho_bt_editor;}

	require_once "../layout/header.inc.php";
	require_once "../layout/body.inc.php";
?>
<script type="text/javascript" src="../editor/ckeditor_4_5_1/ckeditor.js"></script>
<script type="text/javascript" src="../editor/ckeditor_4_5_1/ckfinder/ckfinder.js"></script>
<link rel="stylesheet" href="<?php echo $GLOBALS['site_http_url']?>admin/layout/font-awesome-4.5.0/css/font-awesome.css" type="text/css" media="all" />
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
	$('.fancybox_url').live('click', function(){ var href = $(this).attr('href'); $.fancybox({ 'showCloseButton' : false, 'titlePosition' : 'inside', title : true, autoSize : false, modal : false, fitToView : false, topRatio : 0, content : '<iframe width="100%" height="100%" src="'+href+'"></iframe>', width : 754, maxHeight : 600, scrolling : 'no', type : 'html', onComplete: function() {$("#fancybox-wrap").stop().animate({'top':'10px',}, 0); $("#fancybox-wrap").css({'z-index':'9999999999999999',}, 0); } }); return false;});
	// $('.fancybox_url').live('click', function(){ var href = $(this).attr('href'); $.fancybox({ 'showCloseButton' : false, 'titlePosition' : 'inside', title : true, autoSize : false, modal : false, fitToView : false, topRatio : 0, content : '<iframe width="100%" height="100%" src="'+href+'"></iframe>', width : 1105, maxHeight : 600, scrolling : 'no', type : 'html', onComplete: function() {$("#fancybox-wrap").stop().animate({'top':'10px',}, 0); $("#fancybox-wrap").css({'z-index':'9999999999999999',}, 0); } }); return false;});
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
<form name="form" method="post" action="pagina_trata.php?<?php echo TBID?>=<?php echo $bloco_id?>&task_form=<?php echo $task_form?>&dump=<?php echo uniqID("")?>" enctype="multipart/form-data">
<input type="hidden" name="m_lang" value="<?php echo $GLOBALS['admin_lang'];?>" />
<input type="hidden" name="<?php echo TBID?>" value="<?php echo (isset($_GET['duplicar']) && $_GET['duplicar'] > 0) ? 0 : $bloco_id;?>" />
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
<td valign="middle"><a href="list.php" class="brancoh"> &#60; Voltar para a lista de <?php echo MODULO?></a> <?php echo show_menupath(((isset($_GET['bloco_id_pai']) && $_GET['bloco_id_pai'] > 0) ? $_GET['bloco_id_pai'] : $_GET['bloco_id']), 'personalizacao');?></td>
</tr>
</table>
</td>
</tr>
<tr>
<td align="left" valign="top" class="preto" style="background:#FFF">
<div class="wraper" id="wraper">

<div class="bigtitletab" onclick="infinittab(1);return false;" id="menutab1" style="float:none"></div>
<div class="clearboth"></div>
<div class="tabtop-open3" id="tab1"><?php require_once 'tab_dados.php';?></div>
<?php show_page_buttons($bloco_id_pai);?>
</td>
</tr>
</table>
</td>
<?php if($bloco_id_pai == 0){?>
<td width="10px">&nbsp;</td>
<td valign="top"><?php show_fast_tools();?><?php /* if($bloco_id_pai > 0){ show_drop_tools(); } */?></td><?php }?>
</tr>
</table>
</form>
<?php if(!isset($_GET['duplicar'])){?>
<br />
<form action="pagina.php" method="GET" name="lista_paginas" id="lista_paginas">
<input type="hidden" name="<?php echo TBID?>" value="<?php echo (isset($_GET['duplicar']) && $_GET['duplicar'] > 0) ? 0 : $bloco_id;?>" />
<table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
<td valign="top" align="left">
<?php if(isset($_GET[TBID]) && $_GET[TBID] > 0 && $bloco_id_pai == 0){ $res = show_content_list($_GET[TBID]); if($res) show_page_bottom_tools();}?>
</td>
<td width="10px">&nbsp;</td>
<td width="200">&nbsp;</td>
</tr>
</table>
</form>
<?php }?>
</div>
<script src="chosen.jquery.min.js" type="text/javascript"></script>

<script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true}); </script><br /><br />
<?php require_once "../layout/bottom.inc.php";?>