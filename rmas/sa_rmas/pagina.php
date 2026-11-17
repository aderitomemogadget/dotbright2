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
		$sql = 'SELECT '.implode(',',$CMPS_MODULO_DADOS).' FROM '.TBMODULO.' WHERE '.TBID.'='.$_GET[TBID];
		// echo $sql;
		$rs->db_open($db_link, $DB_name, $sql);
		if(!$rs->EOF())
		{
			$bloco_id = $rs->db_data['bloco_id'];
			$bloco_id_pai = $rs->db_data['bloco_id_pai'];
			$bloco_visivel = $rs->db_data['bloco_visivel'];
			$bloco_categorias = explode(';',$rs->db_data['bloco_categorias']);
			$bloco_ordem = $rs->db_data['bloco_ordem'];
			$bloco_data = $rs->db_data['bloco_data'];
			$bloco_data2 = $rs->db_data['bloco_data2'];
			$bloco_ext = $rs->db_data['bloco_ext'];
			$bloco_pdf_ext = $rs->db_data['bloco_pdf_ext'];
			$bloco_permanente = $rs->db_data['bloco_permanente'];
			$bloco_opcao_embalagem = $rs->db_data['bloco_opcao_embalagem'];
			$bloco_opcao_cor = $rs->db_data['bloco_opcao_cor'];
			$bloco_opcao_vinhos = $rs->db_data['bloco_opcao_vinhos'];
			$bloco_opcao_iguarias = $rs->db_data['bloco_opcao_iguarias'];
			$bloco_opcao_cartao = $rs->db_data['bloco_opcao_cartao'];
			$bloco_opcao_placafoto = $rs->db_data['bloco_opcao_placafoto'];
			$bloco_opcao_placafrase = $rs->db_data['bloco_opcao_placafrase'];
			$bloco_opcao_embrulho = $rs->db_data['bloco_opcao_embrulho'];
			$bloco_preco = $rs->db_data['bloco_preco'];
			$bloco_promo = $rs->db_data['bloco_promo'];
			$bloco_preco_promo = $rs->db_data['bloco_preco_promo'];
			
			$bloco_embalagens_multiple = $rs->db_data['bloco_embalagens_multiple'];
			$bloco_cores_multiple = $rs->db_data['bloco_cores_multiple'];
			$bloco_cores_multiple_imgs = $rs->db_data['bloco_cores_multiple_imgs'];
			// $bl_c_m = explode(',',$bloco_cores_multiple_imgs);
			// $bl_c_m2 = explode('-',$bl_c_m);
			
			$bloco_vinhos_multiple = $rs->db_data['bloco_vinhos_multiple'];
			$bloco_iguarias_multiple = $rs->db_data['bloco_iguarias_multiple'];			
			$bloco_maximo_vinhos = $rs->db_data['bloco_maximo_vinhos'];			
			$bloco_maximo_iguarias = $rs->db_data['bloco_maximo_iguarias'];			
			
			
			$sql = 'SELECT '.implode(',',$CMPS_MODULO_DADOS_LANG).' FROM '.TBMODULO_LANG.' WHERE '.TBID.'='.$_GET[TBID].' AND bloco_lang="'.$GLOBALS['admin_lang'].'"';
			
			$rs2->db_open($db_link, $DB_name, $sql);
			if(!$rs2->EOF())
			{
				$bloco_titulo = limpa_editor_lixo(stripslashes($rs2->db_data['bloco_titulo']));
				$bloco_titulo_2 = limpa_editor_lixo(stripslashes($rs2->db_data['bloco_titulo_2']));
				$bloco_titulo_3 = limpa_editor_lixo(stripslashes($rs2->db_data['bloco_titulo_3']));
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
<table class="new_quadros" style="background:#FFF" width="100%" border=0 cellspacing=0 cellpadding="4" align="center">
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

<div class="bigtitletab" onclick="infinittab(1);return false;" id="menutab1">Ficha do Pack</div>
<div class="bigtitletaboff" onclick="infinittab(2);return false;" id="menutab2" style="display:none">Escolha Embalagens</div>
<div class="bigtitletaboff" onclick="infinittab(3);return false;" id="menutab3" style="display:none">Escolha Cores</div>
<div class="bigtitletaboff" onclick="infinittab(4);return false;" id="menutab4" style="display:none">Escolha Vinhos</div>
<div class="bigtitletaboff" onclick="infinittab(5);return false;" id="menutab5" style="display:none">Escolha Iguarias</div>
<div class="bigtitletaboff" onclick="infinittab(6);return false;" id="menutab6">Imagens Animação</div>
<div class="bigtitletaboff" onclick="infinittab(7);return false;" id="menutab7">Caracteristicas</div>
<?php if(isset($_GET[TBID]) && $_GET[TBID] > 0){ ?><div class="bigtitletaboff" onclick="infinittab(8);return false;" id="menutab8">Combinações Embalagem/Cor Imagens</div><?php }?>

<div class="clearboth"></div>
<div class="tabtop-open3" id="tab1"><?php require_once 'tab_dados.php';?></div>
<div class="tabtop-open3" id="tab2"><?php require_once 'tab_embalagens.php';?></div>
<div class="tabtop-open3" id="tab3"><?php require_once 'tab_cores.php';?></div>
<div class="tabtop-open3" id="tab4"><?php require_once 'tab_vinhos.php';?></div>
<div class="tabtop-open3" id="tab5"><?php require_once 'tab_iguarias.php';?></div>
<div class="tabtop-open3" id="tab6"><?php require_once 'tab_imagens.php';?></div>
<div class="tabtop-open3" id="tab7"><?php require_once 'tab_caracteristicas.php';?></div>
<div class="tabtop-open3" id="tab8"><?php require_once 'tab_combinacoes.php';?></div>
</div>
<?php show_page_buttons();?>
</td>
</tr>
</table>
</td>
<td width="10px">&nbsp;</td>
<td valign="top"><?php //show_fast_tools();?></td>
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
<script src="chosen.jquery.min.js" type="text/javascript"></script>

<script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true}); </script><br /><br />
<?php require_once "../layout/bottom.inc.php";?>