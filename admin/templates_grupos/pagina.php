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
		$sql = 'SELECT '.implode(',', array_keys($CMPS_MODULO_DADOS)).' FROM '.TBMODULO.' WHERE '.TBID.'='.$_GET[TBID];
		$rs->db_open($db_link, $DB_name, $sql);
		if(!$rs->EOF())
		{
			$bloco_id = $rs->db_data[TBID];
			$Ordem = $rs->db_data['Ordem'];
			$Data = $rs->db_data['Data'];
			$Data2 = $rs->db_data['Data2'];
			$img_ext = $rs->db_data['img_ext'];
			$Video = $rs->db_data['Video'];
			$Visivel = $rs->db_data['Visivel'];
			$Visivel2 = $rs->db_data['Visivel2'];
			$Destaque = $rs->db_data['Destaque'];
			$margem_top = $rs->db_data['margem_top'];
			$bg_color = $rs->db_data['bg_color'];
			$efeito = $rs->db_data['efeito'];
			$URL = $rs->db_data['URL'];
			$site_width = $rs->db_data['site_width'];
			$estilo = $rs->db_data['estilo'];
			$estilo2 = $rs->db_data['estilo2'];
			$Protected = $rs->db_data['Protected'];
			$ID_ORG = $rs->db_data['ID_ORG'];

			$sql = 'SELECT '.implode(',',array_keys($CMPS_MODULO_DADOS_LANG)).' FROM '.TBMODULO_LANG.' WHERE '.TBID.'='.$_GET[TBID].' AND '.TBLANG.'="'.$GLOBALS['admin_lang'].'"';

			$rs2->db_open($db_link, $DB_name, $sql);
			if(!$rs2->EOF())
			{
				$Cabecalho = limpa_editor_lixo(stripslashes($rs2->db_data['Cabecalho']));
				$Texto1 = limpa_editor_lixo(stripslashes($rs2->db_data['Texto1']));
				$Texto2 = limpa_editor_lixo(stripslashes($rs2->db_data['Texto2']));
				$Texto3 = limpa_editor_lixo(stripslashes($rs2->db_data['Texto3']));
				$Texto4 = limpa_editor_lixo(stripslashes($rs2->db_data['Texto4']));
			}
			$rs2->db_close();
		}
		$rs->db_close();
	}

	if(!$caminho_bt_editor){$caminho_bt_editor1='../../../editor/';$caminho_bt_editor2='../editor/';}
	else{$caminho_bt_editor1=$caminho_bt_editor2=$caminho_bt_editor;}

	require_once "../layout/header.inc.php";
	require_once "../layout/body.inc.php";

	if(EDITOR == 1) // TEM EDITOR
	{
		echo '<script type="text/javascript" src="../editor/ckeditor/ckeditor.js"></script>';
		echo '<script type="text/javascript" src="../editor/ckeditor/ckfinder/ckfinder.js"></script>';
	}
?>
<link rel="stylesheet" type="text/css" href="../sa.css" />
<link rel="stylesheet" type="text/css" href="../../external_api/chosen/chosen.css?dp=<?php echo dumpacache?>" />
<link rel="stylesheet" href="../../external_api/codemirror/lib/codemirror.css" />
<link rel="stylesheet" href="../../external_api/codemirror/addon/dialog/dialog.css">
<link rel="stylesheet" href="../../external_api/codemirror/addon/search/matchesonscrollbar.css">
<script src="../../external_api/codemirror/lib/codemirror.js"></script>
<script src="../../external_api/codemirror/mode/markdown/markdown.js"></script>
<script src="../../external_api/codemirror/mode/xml/xml.js"></script>
<script src="../../external_api/codemirror/addon/selection/active-line.js"></script>
<script src="../../external_api/codemirror/addon/dialog/dialog.js"></script>
<script src="../../external_api/codemirror/addon/search/searchcursor.js"></script>
<script src="../../external_api/codemirror/addon/search/search.js"></script>
<script src="../../external_api/codemirror/addon/scroll/annotatescrollbar.js"></script>
<script src="../../external_api/codemirror/addon/search/matchesonscrollbar.js"></script>
<script>var tabsel = '<?php echo (isset($_GET['tab']) && $_GET['tab'] > 0) ? $_GET['tab'] : '1';?>';</script>
<script>var SITE_HTTP_URL = '<?php echo $GLOBALS['site_http_url']?>';var SITE_SHELL_URL = '<?php echo $GLOBALS['site_shell_url']?>';</script>
<script>$(document).ready(function()
{
	$('.fancybox_img').fancybox();
	$('.fancybox2').live('click', function(){ var href = $(this).attr('href'); $.fancybox({ 'showCloseButton' : false, 'titlePosition' : 'inside', title : true, autoSize : false, modal : false, fitToView : false, topRatio : 0, content : '<iframe width="100%" height="100%" src="'+href+'"></iframe>', width : 754, maxHeight : 600, scrolling : 'no', type : 'html', onComplete: function() {$("#fancybox-wrap").stop().animate({'top':'10px',}, 0); $("#fancybox-wrap").css({'z-index':'9999999999999999',}, 0); } }); return false;});
});
</script>
<script language="JavaScript" src="../sa.js"></script>
<div class="center" style="position:relative;" id="wp">
<form name="form" method="post" action="pagina_trata.php?<?php echo TBID?>=<?php echo $bloco_id?>&task_form=<?php echo $task_form?>&dump=<?php echo uniqID("")?>" enctype="multipart/form-data">

<input type="hidden" name="<?php echo TBLANG?>" value="<?php echo $GLOBALS['admin_lang'];?>" />
<input type="hidden" name="<?php echo TBID?>" value="<?php echo (isset($_GET['duplicar']) && $_GET['duplicar'] > 0) ? 0 : $bloco_id;?>" />
<input type="hidden" name="duplicar" value="<?php echo (isset($_GET['duplicar']) && $_GET['duplicar'] > 0) ? 1 : 0;?>" />
<input type="hidden" name="ctab" id="ctab" value="<?php echo (isset($_GET['tab']) && $_GET['tab'] > 0) ? $_GET['tab'] : '1';?>" />
<input type="hidden" name="slashes" value="'" />

<table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
<td valign="top" align="left"><?php include "../layout/menus.inc.php"; ?>
<table class="new_quadros" style="background:#FFF" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
<tr>
<td valign="top">
<table width="100%" border=0 cellspacing=0 cellpadding="5" class="subtitulo_janela">
<tr>
<td valign="middle"><a href="list.php" class="brancoh"> &#60; Voltar para a lista de <?php echo MODULO?></a> <?php echo show_menupath(((isset($_GET['bloco_id_pai']) && $_GET['bloco_id_pai'] > 0) ? $_GET['bloco_id_pai'] : $_GET['bloco_id']),'blocos');?></td>
</tr>
</table>
</tr>
<tr>
<td align="left" valign="top" class="preto" style="background:#FFF">
<div class="wraper" id="wraper">

<div class="bigtitletab" onclick="infinittab(1);return false;" id="menutab1"><?php echo MODULO?></a></div>
<div class="clearboth"></div>
<div class="tabtop-open3" id="tab1"><?php require_once 'tab_dados.php';?></div>
</div>
<?php show_page_buttons();?>
</td>
</tr>
</table>
</td>
<td width="10px">&nbsp;</td>
<td valign="top"><?php show_fast_tools();?></td>
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
<?php if(isset($_GET[TBID]) && $_GET[TBID] > 0){ $res = show_content_list($_GET[TBID]); if($res) show_page_bottom_tools();}?>
</td>
<td width="10px">&nbsp;</td>
<td width="200">&nbsp;</td>
</tr>
</table>
</form>
<?php }?>
</div>
<script src="../../external_api/chosen/chosen.jquery.min.js" type="text/javascript"></script>
<script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true}); </script>
<script>var editor = CodeMirror.fromTextArea(document.getElementById("Texto1"), { lineNumbers: true, styleActiveLine: true, lineNumbers: true, lineWrapping: true});</script>
<script>var editor2 = CodeMirror.fromTextArea(document.getElementById("Texto2"), { lineNumbers: true, styleActiveLine: true, lineNumbers: true, lineWrapping: true});</script>
<script>var editor3 = CodeMirror.fromTextArea(document.getElementById("Texto3"), { lineNumbers: true, styleActiveLine: true, lineNumbers: true, lineWrapping: true});</script>
<?php require_once "../layout/bottom.inc.php";?>