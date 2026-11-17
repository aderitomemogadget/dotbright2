<?php
	require_once "settings.inc.php";
	if($task=="apagar"){$task_button=write_lang('key_apagar_este_registo','Apagar este Registo');$task_form="apaga";}
	elseif(isset($_GET['duplicar']) && $_GET['duplicar']==1){$task_button=write_lang('key_duplicar_este_registo','Duplicar este Registo');$task_form="duplicar";}
	elseif($_GET[TBID]==0){$task_button=write_lang('key_gravar_novo_registo','Gravar Novo Registo');$task_form="insere";}
	else {$task_button=write_lang('key_gravar_alteracoes','Gravar alterações');$task_form="altera";}
	$D=getdate();
	acoes_selecionados();
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	// vamos verificar se os campos existem todos na base de dados
    $fields=db_list_fields($DB_name,'modulos',$db_link);
    $columns=db_num_fields($fields);
    for($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
    if(!in_array('Tem_H1',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos ADD Tem_H1 tinyint(1) NOT NULL');
    if(!in_array('Tem_paginacao',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos ADD Tem_paginacao tinyint(1) NOT NULL');
	if(!in_array('Tem_setas',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos ADD Tem_setas tinyint(1) NOT NULL');
	if(!in_array('Tem_sombra',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos ADD Tem_sombra tinyint(1) NOT NULL');
	if(!in_array('Largura',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos ADD Largura tinyint(1) NOT NULL');
	if(!in_array('Altura',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos ADD Altura int(5) NOT NULL');
	if(!in_array('altura_max',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos ADD altura_max tinyint(1) NOT NULL');
	if(!in_array('Layout',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos ADD Layout tinyint(1) NOT NULL');
	if(!in_array('Estilo',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos ADD Estilo tinyint(1) NOT NULL');
	if(!in_array('Estilo_Back',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos ADD Estilo_Back tinyint(1) NOT NULL');
	if(!in_array('Modulo_animacao',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos ADD Modulo_animacao varchar(255) NOT NULL');
	if(!in_array('bc_hometype',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos ADD bc_hometype tinyint(4) NOT NULL');
	if(!in_array('bc_homename',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos ADD bc_homename varchar(20) NOT NULL');


	$fields=db_list_fields($DB_name,'modulos_imgs',$db_link);
    $columns=db_num_fields($fields);
    for($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
	if(!in_array('bloco_img_layout',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD bloco_img_layout tinyint(1) NOT NULL');
	if(!in_array('bloco_img_lightbox',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD bloco_img_lightbox tinyint(1) NOT NULL');
	if(!in_array('bloco_layout',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD bloco_layout tinyint(1) NOT NULL');
	if(!in_array('bloco_img_data',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD bloco_img_data date Default "0000-00-00"');
	if(!in_array('bloco_img_data_visivel',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD bloco_img_data_visivel tinyint(1) Default "0"');
	if(!in_array('bloco_img_url_default',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD bloco_img_url_default tinyint(1) Default "0"');
	if(!in_array('bloco_img_limite',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD bloco_img_limite INT(11) Default "0"');
	if(!in_array('bloco_pdf_ext',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD bloco_pdf_ext varchar(255) Default "0"');
	if(!in_array('bloco_img2_ext',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD bloco_img2_ext varchar(255) Default "0"');//imagem para mobile

	if(!in_array('a_data_x',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD a_data_x varchar(50) Default ""');
	if(!in_array('a_data_y',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD a_data_y varchar(50) Default ""');
	if(!in_array('a_data_voffset',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD a_data_voffset varchar(50) Default ""');
	if(!in_array('a_data_hoffset',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD a_data_hoffset varchar(50) Default ""');
	if(!in_array('a_data_speed',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD a_data_speed varchar(50) Default ""');
	if(!in_array('a_data_start',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD a_data_start varchar(50) Default ""');
	if(!in_array('a_data_easing',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD a_data_easing varchar(50) Default ""');
	if(!in_array('a_data_endspeed',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD a_data_endspeed varchar(50) Default ""');
	if(!in_array('a_data_text_align',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD a_data_text_align varchar(50) Default ""');

	if(!in_array('b_data_x',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD b_data_x varchar(50) Default ""');
	if(!in_array('b_data_y',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD b_data_y varchar(50) Default ""');
	if(!in_array('b_data_voffset',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD b_data_voffset varchar(50) Default ""');
	if(!in_array('b_data_hoffset',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD b_data_hoffset varchar(50) Default ""');
	if(!in_array('b_data_speed',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD b_data_speed varchar(50) Default ""');
	if(!in_array('b_data_start',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD b_data_start varchar(50) Default ""');
	if(!in_array('b_data_easing',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD b_data_easing varchar(50) Default ""');
	if(!in_array('b_data_endspeed',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD b_data_endspeed varchar(50) Default ""');
	if(!in_array('b_data_text_align',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD b_data_text_align varchar(50) Default ""');

	if(!in_array('c_data_x',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD c_data_x varchar(50) Default ""');
	if(!in_array('c_data_y',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD c_data_y varchar(50) Default ""');
	if(!in_array('c_data_voffset',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD c_data_voffset varchar(50) Default ""');
	if(!in_array('c_data_hoffset',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD c_data_hoffset varchar(50) Default ""');
	if(!in_array('c_data_speed',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD c_data_speed varchar(50) Default ""');
	if(!in_array('c_data_start',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD c_data_start varchar(50) Default ""');
	if(!in_array('c_data_easing',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD c_data_easing varchar(50) Default ""');
	if(!in_array('c_data_endspeed',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD c_data_endspeed varchar(50) Default ""');
	if(!in_array('c_data_text_align',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD c_data_text_align varchar(50) Default ""');

	if(!in_array('d_data_x',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD d_data_x varchar(50) Default ""');
	if(!in_array('d_data_y',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD d_data_y varchar(50) Default ""');
	if(!in_array('d_data_voffset',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD d_data_voffset varchar(50) Default ""');
	if(!in_array('d_data_hoffset',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD d_data_hoffset varchar(50) Default ""');
	if(!in_array('d_data_speed',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD d_data_speed varchar(50) Default ""');
	if(!in_array('d_data_start',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD d_data_start varchar(50) Default ""');
	if(!in_array('d_data_easing',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD d_data_easing varchar(50) Default ""');
	if(!in_array('d_data_endspeed',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD d_data_endspeed varchar(50) Default ""');
	if(!in_array('d_data_text_align',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs ADD d_data_text_align varchar(50) Default ""');



	$fields=db_list_fields($DB_name,'modulos_imgs_lang',$db_link);
    $columns=db_num_fields($fields);
    for($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
	if(!in_array('bloco_video_code',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs_lang ADD bloco_video_code varchar(255) NOT NULL');
	if(!in_array('bloco_video_yt',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs_lang ADD bloco_video_yt varchar(255) NOT NULL');
	if(!in_array('bloco_video_vimeo',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs_lang ADD bloco_video_vimeo varchar(255) NOT NULL');
	if(!in_array('bloco_img2_txt',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_imgs_lang ADD bloco_img2_txt text NOT NULL');



    //*************************************************/

	$ID_ORG=0;
	$rs=new DataObj;
	$rs2=new DataObj;
	if(isset($_GET[TBID]) && $_GET[TBID]!=0)
	{
		$sql='SELECT
			ID,permissoes,ID_ORG,visivel,ocultar,destaque_home,destaque_lateral,destaque_topo,categorias,ordem,data,data2,img_ext,pdf_ext,permanente,open,users,Qtd,Anchor,Posicao,titulo_visivel,
			titulo_cor,
			titulo_tamanho,
			titulo_font_weight,
			Texto1_cor,
			Texto1_tamanho,
			Texto1_font_weight,
			Texto1_line_height,
			Texto2_cor,
			Texto2_tamanho,
			Texto2_font_weight,
			Texto2_line_height,
			Texto3_cor,
			Texto3_tamanho,
			Texto3_font_weight,
			Texto3_line_height,
			Tem_H1,
			Tem_paginacao,
			Tem_setas,
			Tem_sombra,
			Layout,
			Estilo,
			Largura,
			Altura,
			altura_max,
			Estilo_Back,
			Modulo_animacao,
			bc_hometype,
			bc_homename
			FROM '.TBMODULO.' WHERE '.TBID.'='.$_GET[TBID];
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$ID=$rs->db_data['ID'];
			$ID_ORG=$rs->db_data['ID_ORG'];
			$visivel=$rs->db_data['visivel'];
			$destaque_home=$rs->db_data['destaque_home'];
			$destaque_lateral=$rs->db_data['destaque_lateral'];
			$destaque_topo=$rs->db_data['destaque_topo'];
			$categorias=explode(';',$rs->db_data['categorias']);
			$ordem=$rs->db_data['ordem'];
			$Qtd=$rs->db_data['Qtd'];
			$Anchor=$rs->db_data['Anchor'];
			$Posicao=$rs->db_data['Posicao'];
			$titulo_visivel=$rs->db_data['titulo_visivel'];
			$titulo_cor=$rs->db_data['titulo_cor'];
			$titulo_tamanho=$rs->db_data['titulo_tamanho'];
			$titulo_font_weight=$rs->db_data['titulo_font_weight'];
			$Texto1_cor=$rs->db_data['Texto1_cor'];
			$Texto1_tamanho=$rs->db_data['Texto1_tamanho'];
			$Texto1_font_weight=$rs->db_data['Texto1_font_weight'];
			$Texto1_line_height=$rs->db_data['Texto1_line_height'];
			$Texto2_cor=$rs->db_data['Texto2_cor'];
			$Texto2_tamanho=$rs->db_data['Texto2_tamanho'];
			$Texto2_font_weight=$rs->db_data['Texto2_font_weight'];
			$Texto2_line_height=$rs->db_data['Texto2_line_height'];
			$Texto3_cor=$rs->db_data['Texto3_cor'];
			$Texto3_tamanho=$rs->db_data['Texto3_tamanho'];
			$Texto3_font_weight=$rs->db_data['Texto3_font_weight'];
			$Texto3_line_height=$rs->db_data['Texto3_line_height'];
			$data=$rs->db_data['data'];
			$data2=$rs->db_data['data2'];
			$img_ext=$rs->db_data['img_ext'];
			$pdf_ext=$rs->db_data['pdf_ext'];
			$permanente=$rs->db_data['permanente'];
			$open=$rs->db_data['open'];
			$users=$rs->db_data['users'];
			$permissoes=$rs->db_data['permissoes'];
			$users2=explode(';',$rs->db_data["users"]);
			$Tem_H1=$rs->db_data['Tem_H1'];
			$Tem_paginacao=$rs->db_data['Tem_paginacao'];
			$Tem_setas=$rs->db_data['Tem_setas'];
			$Tem_sombra=$rs->db_data['Tem_sombra'];
			$Largura=$rs->db_data['Largura'];
			$Altura=$rs->db_data['Altura'];
			$altura_max=$rs->db_data['altura_max'];
			$Layout=$rs->db_data['Layout'];
			$Estilo=$rs->db_data['Estilo'];
			$Estilo_Back=$rs->db_data['Estilo_Back'];
			$Modulo_animacao=$rs->db_data['Modulo_animacao'];

			$bc_hometype=$rs->db_data['bc_hometype'];
			$bc_homename=$rs->db_data['bc_homename'];



			$sql='SELECT titulo,titulo_2,titulo_3,resumo,texto,texto3 FROM '.TBMODULO_LANG.' WHERE ID_PAI='.$_GET[TBID].' AND lang="'.$GLOBALS['admin_lang'].'"';
			$rs2->db_open($db_link,$DB_name,$sql);
			if(!$rs2->EOF())
			{
				/*$titulo=limpa_editor_lixo(htmlentities(stripslashes($rs2->db_data['titulo'])));
				$titulo_2=limpa_editor_lixo(htmlentities(stripslashes($rs2->db_data['titulo_2'])));
				$titulo_3=limpa_editor_lixo(htmlentities(stripslashes($rs2->db_data['titulo_3'])));
				$resumo=limpa_editor_lixo(htmlentities(stripslashes($rs2->db_data['resumo'])));
				$texto=limpa_editor_lixo(htmlentities(stripslashes($rs2->db_data['texto'])));
				$texto3=limpa_editor_lixo(htmlentities(stripslashes($rs2->db_data['texto3'])));*/


				$titulo=limpa_editor_lixo((stripslashes($rs2->db_data['titulo'])));
				$titulo_2=limpa_editor_lixo((stripslashes($rs2->db_data['titulo_2'])));
				$titulo_3=limpa_editor_lixo((stripslashes($rs2->db_data['titulo_3'])));
				$resumo=limpa_editor_lixo((stripslashes($rs2->db_data['resumo'])));
				$texto=limpa_editor_lixo((stripslashes($rs2->db_data['texto'])));
				$texto3=limpa_editor_lixo((stripslashes($rs2->db_data['texto3'])));

			}
			$rs2->db_close();
		}
		$rs->db_close();
	}

	if(!$caminho_bt_editor){$caminho_bt_editor1='../../../editor/';$caminho_bt_editor2='../editor/';}
	else{$caminho_bt_editor1=$caminho_bt_editor2=$caminho_bt_editor;}
	require_once "../layout/header.inc.php";
	require_once "../layout/body.inc.php";
	// VAMOS VER PERMISSOES
	if(isset($rs->db_data["users"]) && ($rs->db_data["users"]=='' || $rs->db_data["users"]=='0'))
	{
		$ID_principal=BLOCOS_PATH_PRINCIPAL_B($rs->db_data["ID_ORG"]);
		$users2=GET_BLOCO_USERS($ID_principal);
	}
	if($GLOBALS['dados']['Grupo_ID']==1 || $GLOBALS['dados']['Grupo_ID']==4 || ($GLOBALS['dados']['Grupo_ID']==5 && in_array($GLOBALS['admin_id'],$users2)) || $_GET['ID']==0)
	{
?>
	<script type="text/javascript" src="../editor/ckeditor_4_5_1/ckeditor.js"></script>
	<script type="text/javascript" src="../editor/ckeditor_4_5_1/ckfinder/ckfinder.js"></script>


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


	<!-- <link rel="stylesheet" type="text/css" href="sa.css" /> -->
	<link rel="stylesheet" type="text/css" href="chosen.css?dp=<?php echo dumpacache?>" />
	<link rel="stylesheet" href="../layout/font-awesome/css/font-awesome.css">
	<script>var SITE_HTTP_URL='<?php echo $GLOBALS['site_http_url']?>';var SITE_SHELL_URL='<?php echo $GLOBALS['site_shell_url']?>';</script>
	<script>$(document).ready(function()
	{
		$('.fancybox_img').fancybox();
		$('.fancybox2').live('click',function(){ var href=$(this).attr('href'); $.fancybox({ 'showCloseButton' : false,'titlePosition' : 'inside',title : true,autoSize : false,modal : false,fitToView : false,topRatio : 0,content : '<iframe width="100%" height="100%" src="'+href+'"></iframe>',width : 754,maxHeight : 600,scrolling : 'no',type : 'html',onComplete: function() {$("#fancybox-wrap").stop().animate({'top':'10px',},0); $("#fancybox-wrap").css({'z-index':'9999999999999999',},0); } }); return false;});
	});
	function addToProduct(){$("#s_listafamiliasprod").append($("#listademarcas option:selected"));}
	function removeFromProduct(){$("#listademarcas").append($("#s_listafamiliasprod option:selected"));}
	function reload_select(id)
	{
		$.get('atualizar_select.php?dp='+new Date(), function(data)
		{
			$('#'+id).children().remove();
			$('#'+id).html(data);
			$('#'+id).trigger("liszt:updated");

		});
	}
	function ver_estilo(id)
	{
		if($(id).val() !='') window.open('../modulos_config/list.php?p=1&Nome='+$(id).val(), '_blank');
		else alert('Por favor selecione um estilo!');
	}
	</script>
	<script language="JavaScript" src="sa.js"></script>
	<div class="center" style="position:relative;" id="wp">
	<form name="form" id="mod_form" method="post" action="pagina_trata.php?<?php echo TBID?>=<?php echo $ID?>&task_form=<?php echo $task_form?>&d=<?php echo uniqID("")?>" enctype="multipart/form-data">
		<input type="hidden" name="m_lang" value="<?php echo $GLOBALS['admin_lang'];?>" />
		<input type="hidden" name="<?php echo TBID?>" value="<?php echo (isset($_GET['duplicar']) && $_GET['duplicar'] > 0) ? 0 : $ID;?>" />
		<input type="hidden" name="duplicar" value="<?php echo (isset($_GET['duplicar']) && $_GET['duplicar'] > 0) ? 1 : 0;?>" />
		<input type="hidden" name="slashes" value="'" />
		<input type="hidden" name="lang" id="lang" class="form-control" value="<?php echo $GLOBALS['admin_lang']?>" style="width:50px"/>
		<table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
		<tr>
		<td valign="top" align="left">
		<?php include "../layout/menus.inc.php"; ?>
			<div class="panel panel-inverse overflow-hidden m-b-12">
		        <div class="panel-heading">
		            <h4 style="color:#fff">COMPONENTES</h4>
		        </div>
		    </div>
		<?php if($ID && $_GET['preview']==1) show_preview($ID);?>
		<table class="titulo_janela" width="100%" height="30px" cellspacing="0" cellpadding="5" border="0">
		<tr>
			<td valign="middle"><a href="list.php" class="branco"> &#60; Voltar para a listagem de <?php echo MODULO?></a> <?php echo show_menupath(((isset($_GET['ID_ORG']) && $_GET['ID_ORG'] > 0) ? $_GET['ID_ORG'] : $_GET['ID']), 'modulos');?></td>
		</tr>
		</table>
				<table class="new_quadros" style="background:#FFF" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
				<tr>
		<td valign="top">
		</td>
		</tr>
		<tr>
			<td align="left" valign="top" class="preto">
				<div>
					<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tr>
					<td align="left">
					<table border="0" cellspacing="0" cellpadding="0" width="100%">
					<tr>
					<td style="background:#fff">
					<?php
						//Como queremos que este modulo seja compativel com as versoes anteriores, é necessário manter o seguinte codigo:
						//Os proximos tipos de elementos vão ser substituidos por novos
						if($categorias[0]==3)$categorias[0]=6;
						if($categorias[0]==4)$categorias[0]=5;
						if($categorias[0]==7)$categorias[0]=6;
						if($categorias[0]==12)$categorias[0]=8;
						if($categorias[0]==13)$categorias[0]=11;
						if($categorias[0]==14)$categorias[0]=11;
						if($categorias[0]==15)$categorias[0]=17;
						//**********************************************
						$i=0;
						$arr_familias[$i]['brand_id']=$i+1;$arr_familias[$i]['marca_nome']="GRUPO de MODULOS";$i++;//1
						$arr_familias[$i]['brand_id']=$i+1;$arr_familias[$i]['marca_nome']="Banner";$i++;//2
						$arr_familias[$i]['brand_id']=$i+1;$arr_familias[$i]['marca_nome']="Texto simples centrado";$i++;//3*************
						$arr_familias[$i]['brand_id']=$i+1;$arr_familias[$i]['marca_nome']="Carrocell Imagens - Bolas";$i++;//4*************
						$arr_familias[$i]['brand_id']=$i+1;$arr_familias[$i]['marca_nome']="Carrocell";$i++;//5
						$arr_familias[$i]['brand_id']=$i+1;$arr_familias[$i]['marca_nome']="Conteudos livres";$i++;//6
						$arr_familias[$i]['brand_id']=$i+1;$arr_familias[$i]['marca_nome']="Texto Livre";$i++;//7****************
						$arr_familias[$i]['brand_id']=$i+1;$arr_familias[$i]['marca_nome']="Conteudos lado a lado";$i++;//8
						$arr_familias[$i]['brand_id']=$i+1;$arr_familias[$i]['marca_nome']="Destaques tipo Blogue";$i++;//9
						$arr_familias[$i]['brand_id']=$i+1;$arr_familias[$i]['marca_nome']="Caminho";$i++;//10
						$arr_familias[$i]['brand_id']=$i+1;$arr_familias[$i]['marca_nome']="Galeria";$i++;//11
						$arr_familias[$i]['brand_id']=$i+1;$arr_familias[$i]['marca_nome']="3 Textos lado a lado";$i++;//12***************
						$arr_familias[$i]['brand_id']=$i+1;$arr_familias[$i]['marca_nome']="Galeria2 imagens";$i++;//13**********
						$arr_familias[$i]['brand_id']=$i+1;$arr_familias[$i]['marca_nome']="Galeria3";$i++;//14***************
						$arr_familias[$i]['brand_id']=$i+1;$arr_familias[$i]['marca_nome']="Accordion Group";$i++;//15******************
						$arr_familias[$i]['brand_id']=$i+1;$arr_familias[$i]['marca_nome']="Google Maps";$i++;//16
						$arr_familias[$i]['brand_id']=$i+1;$arr_familias[$i]['marca_nome']="Acordião";$i++;//17
						$arr_familias[$i]['brand_id']=$i+1;$arr_familias[$i]['marca_nome']="";$i++;//18
						$arr_familias[$i]['brand_id']=$i+1;$arr_familias[$i]['marca_nome']="Apoios";$i++;//19
						$arr_familias[$i]['brand_id']=$i+1;$arr_familias[$i]['marca_nome']="";$i++;//20
						$arr_familias[$i]['brand_id']=$i+1;$arr_familias[$i]['marca_nome']="";$i++;//21
						$arr_familias[$i]['brand_id']=$i+1;$arr_familias[$i]['marca_nome']="Organização";$i++;//22
						$arr_familias[$i]['brand_id']=$i+1;$arr_familias[$i]['marca_nome']="";$i++;//23
						$arr_familias[$i]['brand_id']=$i+1;$arr_familias[$i]['marca_nome']="Multiscroll";$i++;//24

						gera_lista_brands_cat($arr_familias,$categorias,1);

						// OPCOES DE CADA TIPO DE ELEMENTO
						$opcoes[1]['Titulo']=1;  $opcoes[1]['titulo_cor']=0; $opcoes[1]['Texto1']=0; $opcoes[1]['Texto2']=0; $opcoes[1]['Texto3']=0; $opcoes[1]['Imagens']=0; $opcoes[1]['Imagem_bg']=0; $opcoes[1]['legen_local']=0; $opcoes[1]['gal_qtd']=0; $opcoes[1]['gal_tipoanim']=0;$opcoes[1]['Layout']=0;$opcoes[1]['Layout_Bloco']=0;$opcoes[1]['Estilo']=0;
						$opcoes[2]['Titulo']=1;  $opcoes[2]['titulo_cor']=1; $opcoes[2]['Texto1']=1; $opcoes[2]['Texto2']=0; $opcoes[2]['Texto3']=0; $opcoes[2]['Imagens']=1; $opcoes[2]['Imagem_bg']=0; $opcoes[2]['legen_local']=0; $opcoes[2]['gal_qtd']=1; $opcoes[2]['gal_tipoanim']=1;$opcoes[2]['Layout']=0;$opcoes[2]['Layout_Bloco']=0;$opcoes[2]['Estilo']=0;
						$opcoes[3]['Titulo']=1;  $opcoes[3]['titulo_cor']=1; $opcoes[3]['Texto1']=1; $opcoes[3]['Texto2']=1; $opcoes[3]['Texto3']=1; $opcoes[3]['Imagens']=1; $opcoes[3]['Imagem_bg']=1; $opcoes[3]['legen_local']=1; $opcoes[3]['gal_qtd']=1; $opcoes[3]['gal_tipoanim']=1;$opcoes[3]['Layout']=0;$opcoes[3]['Layout_Bloco']=0;$opcoes[3]['Estilo']=0;
						$opcoes[4]['Titulo']=1;  $opcoes[4]['titulo_cor']=1; $opcoes[4]['Texto1']=1; $opcoes[4]['Texto2']=0; $opcoes[4]['Texto3']=0; $opcoes[4]['Imagens']=1; $opcoes[4]['Imagem_bg']=1; $opcoes[4]['legen_local']=0; $opcoes[4]['gal_qtd']=1; $opcoes[4]['gal_tipoanim']=0;$opcoes[4]['Layout']=0;$opcoes[4]['Layout_Bloco']=0;$opcoes[4]['Estilo']=0;
						$opcoes[5]['Titulo']=1;  $opcoes[5]['titulo_cor']=1; $opcoes[5]['Texto1']=1; $opcoes[5]['Texto2']=0; $opcoes[5]['Texto3']=0; $opcoes[5]['Imagens']=1; $opcoes[5]['Imagem_bg']=1; $opcoes[5]['legen_local']=1; $opcoes[5]['gal_qtd']=1; $opcoes[5]['gal_tipoanim']=0;$opcoes[5]['Layout']=0;$opcoes[5]['Layout_Bloco']=0;$opcoes[5]['Estilo']=1;
						$opcoes[6]['Titulo']=1;  $opcoes[6]['titulo_cor']=1; $opcoes[6]['Texto1']=1; $opcoes[6]['Texto2']=1; $opcoes[6]['Texto3']=1; $opcoes[6]['Imagens']=1; $opcoes[6]['Imagem_bg']=1; $opcoes[6]['legen_local']=1; $opcoes[6]['gal_qtd']=1; $opcoes[6]['gal_tipoanim']=1;$opcoes[6]['Layout']=0;$opcoes[6]['Layout_Bloco']=0;$opcoes[6]['Estilo']=0;
						$opcoes[7]['Titulo']=1;  $opcoes[7]['titulo_cor']=1; $opcoes[7]['Texto1']=1; $opcoes[7]['Texto2']=1; $opcoes[7]['Texto3']=1; $opcoes[7]['Imagens']=1; $opcoes[7]['Imagem_bg']=1; $opcoes[7]['legen_local']=1; $opcoes[7]['gal_qtd']=1; $opcoes[7]['gal_tipoanim']=1;$opcoes[7]['Layout']=0;$opcoes[7]['Layout_Bloco']=0;$opcoes[7]['Estilo']=0;
						$opcoes[8]['Titulo']=1;  $opcoes[8]['titulo_cor']=1; $opcoes[8]['Texto1']=1; $opcoes[8]['Texto2']=1; $opcoes[8]['Texto3']=1; $opcoes[8]['Imagens']=1; $opcoes[8]['Imagem_bg']=1; $opcoes[8]['legen_local']=1; $opcoes[8]['gal_qtd']=1; $opcoes[8]['gal_tipoanim']=1;$opcoes[8]['Layout']=0;$opcoes[8]['Layout_Bloco']=0;$opcoes[8]['Estilo']=0;
						$opcoes[9]['Titulo']=1;  $opcoes[9]['titulo_cor']=1; $opcoes[9]['Texto1']=1; $opcoes[9]['Texto2']=1; $opcoes[9]['Texto3']=1; $opcoes[9]['Imagens']=1; $opcoes[9]['Imagem_bg']=1; $opcoes[9]['legen_local']=1; $opcoes[9]['gal_qtd']=1; $opcoes[9]['gal_tipoanim']=1;$opcoes[9]['Layout']=1;$opcoes[9]['Layout_Bloco']=0;$opcoes[9]['Estilo']=0;
						$opcoes[10]['Titulo']=1; $opcoes[10]['titulo_cor']=1;$opcoes[10]['Texto1']=1;$opcoes[10]['Texto2']=0;$opcoes[10]['Texto3']=0;$opcoes[10]['Imagens']=1;$opcoes[10]['Imagem_bg']=0;$opcoes[10]['legen_local']=0;$opcoes[10]['gal_qtd']=0;$opcoes[10]['gal_tipoanim']=0;$opcoes[10]['Layout']=0;$opcoes[10]['Layout_Bloco']=0;$opcoes[10]['Estilo']=0;
						$opcoes[11]['Titulo']=1; $opcoes[11]['titulo_cor']=1;$opcoes[11]['Texto1']=1;$opcoes[11]['Texto2']=1;$opcoes[11]['Texto3']=0;$opcoes[11]['Imagens']=1;$opcoes[11]['Imagem_bg']=1;$opcoes[11]['legen_local']=1;$opcoes[11]['gal_qtd']=1;$opcoes[11]['gal_tipoanim']=1;$opcoes[11]['Layout']=0;$opcoes[11]['Layout_Bloco']=0;$opcoes[11]['Estilo']=1;
						$opcoes[12]['Titulo']=1; $opcoes[12]['titulo_cor']=1;$opcoes[12]['Texto1']=1;$opcoes[12]['Texto2']=1;$opcoes[12]['Texto3']=1;$opcoes[12]['Imagens']=1;$opcoes[12]['Imagem_bg']=1;$opcoes[12]['legen_local']=1;$opcoes[12]['gal_qtd']=1;$opcoes[12]['gal_tipoanim']=1;$opcoes[12]['Layout']=0;$opcoes[12]['Layout_Bloco']=0;$opcoes[12]['Estilo']=0;
						$opcoes[13]['Titulo']=1; $opcoes[13]['titulo_cor']=1;$opcoes[13]['Texto1']=1;$opcoes[13]['Texto2']=1;$opcoes[13]['Texto3']=1;$opcoes[13]['Imagens']=1;$opcoes[13]['Imagem_bg']=1;$opcoes[13]['legen_local']=1;$opcoes[13]['gal_qtd']=1;$opcoes[13]['gal_tipoanim']=1;$opcoes[13]['Layout']=0;$opcoes[13]['Layout_Bloco']=0;$opcoes[13]['Estilo']=0;
						$opcoes[14]['Titulo']=1; $opcoes[14]['titulo_cor']=1;$opcoes[14]['Texto1']=1;$opcoes[14]['Texto2']=1;$opcoes[14]['Texto3']=1;$opcoes[14]['Imagens']=1;$opcoes[14]['Imagem_bg']=1;$opcoes[14]['legen_local']=1;$opcoes[14]['gal_qtd']=1;$opcoes[14]['gal_tipoanim']=1;$opcoes[14]['Layout']=0;$opcoes[14]['Layout_Bloco']=0;$opcoes[14]['Estilo']=0;
						$opcoes[15]['Titulo']=1; $opcoes[15]['titulo_cor']=1;$opcoes[15]['Texto1']=1;$opcoes[15]['Texto2']=0;$opcoes[15]['Texto3']=0;$opcoes[15]['Imagens']=0;$opcoes[15]['Imagem_bg']=1;$opcoes[15]['legen_local']=0;$opcoes[15]['gal_qtd']=0;$opcoes[15]['gal_tipoanim']=0;$opcoes[15]['Layout']=0;$opcoes[15]['Layout_Bloco']=0;$opcoes[15]['Estilo']=0;
						$opcoes[16]['Titulo']=1; $opcoes[16]['titulo_cor']=1;$opcoes[16]['Texto1']=1;$opcoes[16]['Texto2']=0;$opcoes[16]['Texto3']=0;$opcoes[16]['Imagens']=0;$opcoes[16]['Imagem_bg']=1;$opcoes[16]['legen_local']=0;$opcoes[16]['gal_qtd']=0;$opcoes[16]['gal_tipoanim']=0;$opcoes[16]['Layout']=0;$opcoes[16]['Layout_Bloco']=0;$opcoes[16]['Estilo']=0;
						$opcoes[17]['Titulo']=1; $opcoes[17]['titulo_cor']=1;$opcoes[17]['Texto1']=1;$opcoes[17]['Texto2']=0;$opcoes[17]['Texto3']=0;$opcoes[17]['Imagens']=1;$opcoes[17]['Imagem_bg']=0;$opcoes[17]['legen_local']=0;$opcoes[17]['gal_qtd']=0;$opcoes[17]['gal_tipoanim']=0;$opcoes[17]['Layout']=0;$opcoes[17]['Layout_Bloco']=0;$opcoes[17]['Estilo']=1;
						$opcoes[18]['Titulo']=1; $opcoes[18]['titulo_cor']=1;$opcoes[18]['Texto1']=1;$opcoes[18]['Texto2']=0;$opcoes[18]['Texto3']=0;$opcoes[18]['Imagens']=1;$opcoes[18]['Imagem_bg']=0;$opcoes[18]['legen_local']=0;$opcoes[18]['gal_qtd']=0;$opcoes[18]['gal_tipoanim']=0;$opcoes[18]['Layout']=0;$opcoes[18]['Layout_Bloco']=0;$opcoes[18]['Estilo']=0;
						$opcoes[19]['Titulo']=1; $opcoes[19]['titulo_cor']=1;$opcoes[19]['Texto1']=1;$opcoes[19]['Texto2']=0;$opcoes[19]['Texto3']=0;$opcoes[19]['Imagens']=1;$opcoes[19]['Imagem_bg']=1;$opcoes[19]['legen_local']=0;$opcoes[19]['gal_qtd']=1;$opcoes[19]['gal_tipoanim']=0;$opcoes[19]['Layout']=0;$opcoes[19]['Layout_Bloco']=0;$opcoes[19]['Estilo']=0;
						$opcoes[22]['Titulo']=1; $opcoes[22]['titulo_cor']=1;$opcoes[22]['Texto1']=1;$opcoes[22]['Texto2']=0;$opcoes[22]['Texto3']=0;$opcoes[22]['Imagens']=1;$opcoes[22]['Imagem_bg']=0;$opcoes[22]['legen_local']=0;$opcoes[22]['gal_qtd']=1;$opcoes[22]['gal_tipoanim']=1;$opcoes[22]['Layout']=0;$opcoes[22]['Layout_Bloco']=0;$opcoes[22]['Estilo']=0;
						$opcoes[24]['Titulo']=1; $opcoes[24]['titulo_cor']=1;$opcoes[24]['Texto1']=1;$opcoes[24]['Texto2']=0;$opcoes[24]['Texto3']=0;$opcoes[24]['Imagens']=1;$opcoes[24]['Imagem_bg']=0;$opcoes[24]['legen_local']=0;$opcoes[24]['gal_qtd']=0;$opcoes[24]['gal_tipoanim']=0;$opcoes[24]['Layout']=0;$opcoes[24]['Layout_Bloco']=1;$opcoes[24]['Estilo']=0;



						$breadcrumb = 0;
						if($arr_familias[$categorias[0]-1]['brand_id'] == 10){
							$breadcrumb = 1;
						}

					?>
					</td>
					</tr>
					</table>
					</td>
					</tr>
					<tr><td style="height:5x;border-top:#e2e7eb 1px solid" colspan="2">&nbsp;</td></tr>
					<tr>
						<td align="left">
							<table cellpadding="0" cellspacing="0" border="0" style="margin-bottom:5px">
							<tr>
								<td width="80" align="right">MÓDULO PAI:</td>
								<td align="left" width="100"><?php echo gera_lista_path((isset($_GET['ID_ORG']) && $_GET['ID_ORG'] > 0) ? $_GET['ID_ORG'] : $ID_ORG); ?></td>
								<td align="left">
									<table border="0" cellspacing="0" cellpadding="0">
									<tr>
										<td width="80" align="center">ORDEM:</td>
										<td><input type="text" name="ordem" id="ordem" class="form-control" value="<?php echo $ordem?>" style="width:50px"/></td>
									</tr>
									</table>
								</td>
							</tr>
							</table>
						</td>
					</tr>

					<tr><td style="height:5x;border-top:#e2e7eb 1px solid" colspan="2">&nbsp;</td></tr>

					<tr>
						<td align="left" style="margin-top:5x">
							<table cellpadding="0" cellspacing="0" border="0" style="margin-bottom:5px">
							<tr>
								<td width="80" align="right">ATIVAR:</td>
								<td width="20" align="center"><label class="label_check" for="visivel"><input type="checkbox" name="visivel" id="visivel" value="1" <?php if($visivel==1)echo 'checked="checked"'; ?> /></label></td>
								<td width="100" align="center">DATA INÍCIO:</td>
								<td width="90"><input type="text" name="data" id="data" class="box dropdate" style="width:130px !important" value="<?php echo (isset($data) && $data!='0000-00-00') ? $data : date('Y-m-d'); ?>" /></td>
								<td width="80" align="center">DATA FIM:</td>
								<td width="90"><input type="text" name="data2" id="data2" class="box dropdate" style="width:130px !important" value="<?php echo (isset($data2) && $data2!='0000-00-00') ? $data2 : date('Y-m-d'); ?>" /></td>
								<td width="130" align="center"> ou SEMPRE VISIVEL:</td>
								<td width="20"><label class="label_check" for="permanente"><input type="checkbox" name="permanente" id="permanente" value="1" <?php if($permanente == 1) echo 'checked="checked"'; ?> /></label></td>
							</tr>
							</table>
						</td>
					</tr>
					<tr><td style="height:5x;border-top:#e2e7eb 1px solid" colspan="2">&nbsp;</td></tr>

					<tr>
						<td align="left">
							<table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-bottom:5px">
							<tr>
								<td width="80" align="right">ÂNCORA:</td>
								<td align="left" width="100"><input class="form-control" type="text" name="Anchor" id="Anchor" value="<?php echo $Anchor;?>" style="width:150px" onkeyup="$('#anc').html($('#Anchor').val()+'_bloco');$('#anc2').attr('href', '../modulos_config/list.php?p=1&Nome='+$('#Anchor').val()+'_bloco');"/></td>
								<td width="100" align="center">WRAPPER:</td>
								<td align="left" width="150"><div id="anc"><?php echo ($Anchor!='' ? $Anchor.'_bloco' : 'sem wrapper');?></div></td>
								<td align="left"><div style="text-align:center"><?php echo '<a id="anc2" href="../modulos_config/list.php?p=1&Nome='.($Anchor!='' ? $Anchor.'_bloco' : '').'" class="preto" target="_blank" style="text-decoration:underline">Ver estilo em CONFIGURAÇÕES CSS</a>';?></div></td>
							</tr>
							</table>
						</td>
					</tr>
					<tr><td style="height:5x;border-top:#e2e7eb 1px solid" colspan="2">&nbsp;</td></tr>

					<tr>
						<td align="left">
							<table cellpadding="0" cellspacing="0" border="0" style="margin-bottom:5px">
							<tr>
								<td width="80" align="right"> LARGURA:</td>
								<td>
									<select name="Largura" id="Largura" class="form-control" style="width:150px">
										<option value="" <?php if(!$Largura) echo 'selected'?>>Largura fixa</option>
										<option value="1" <?php if($Largura) echo 'selected'?>>Largura 100%</option>
									</select>
								</td><?php 

								if($categorias[0]==2 || $categorias[0]==10 || $categorias[0]==24)
								{
									?>
									<td width="80" align="center"> ALTURA:</td>
									<td width="45"><input maxlength="5" style="width:80px" type="text" name="Altura" value="<?php echo $Altura?>" class="form-control"></td>
									<td width="80" align="left"> pixels</td>
									<td width="80" align="center"> altura maxima:</td>
									<td width="45"><label class="label_check" for="altura_max"><input type="checkbox" name="altura_max" id="altura_max" value="1" <?php if($altura_max==1)echo 'checked="checked"'; ?> /></label></td><?php 
								}?>
							</tr>
							</table>
						</td>
					</tr>
					<tr><td style="height:5x;border-top:#e2e7eb 1px solid" colspan="2">&nbsp;</td></tr>


					<?

					if(!$ID || $opcoes[$categorias[0]]['Titulo'])
					{?>
						<tr><td style="height:5px"></td></tr>
						<tr>
							<td align="left">
								<table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-bottom:5px">
								<tr>
									<td width="80" align="right">DESCRIÇÃO:</td>
									<td><?

										//vamos simular o estilo do titulo::::::::
										$style_titulo='';
										if($titulo_cor || $titulo_tamanho || $titulo_font_weight)
										{
											if($titulo_cor)$str_titulo_cor='color:'.$titulo_cor.';';
											if($titulo_tamanho)$str_titulo_tamanho='font-size:'.$titulo_tamanho.'px;height:'.$titulo_tamanho.'px;';
											if($titulo_font_weight)$str_titulo_font_weight='font-weight:'.$titulo_font_weight.';';
											$style_titulo=$str_titulo_cor.$str_titulo_tamanho.$str_titulo_font_weight;
										}
										$style_texto1='';
										if($Texto1_cor || $Texto1_tamanho || $Texto1_font_weight)
										{
											if($Texto1_cor)$str_texto1_cor='color:'.$Texto1_cor.';';
											if($Texto1_tamanho)$str_texto1_tamanho='font-size:'.$Texto1_tamanho.'px;';
											if($Texto1_font_weight)$str_texto1_font_weight='font-weight:'.$Texto1_font_weight.';';
											if($Texto1_line_height)$str_texto1_line_height='line-height:'.$Texto1_line_height.'px;';
											$style_texto1=$str_texto1_cor.$str_texto1_tamanho.$str_texto1_font_weight.$str_texto1_line_height;
										}

										if($Texto2_cor || $Texto2_tamanho || $Texto2_font_weight)
										{
											if($Texto2_cor)$str_texto2_cor='color:'.$Texto2_cor.';';
											if($Texto2_tamanho)$str_texto2_tamanho='font-size:'.$Texto2_tamanho.'px;';
											if($Texto2_font_weight)$str_texto2_font_weight='font-weight:'.$Texto2_font_weight.';';
											if($Texto2_line_height)$str_texto2_line_height='line-height:'.$Texto2_line_height.'px;';
											$style_texto2=$str_texto2_cor.$str_texto2_tamanho.$str_texto2_font_weight.$str_texto2_line_height;
										}

										if($Texto3_cor || $Texto3_tamanho || $Texto3_font_weight)
										{
											if($Texto3_cor)$str_texto3_cor='color:'.$Texto3_cor.';';
											if($Texto3_tamanho)$str_texto3_tamanho='font-size:'.$Texto3_tamanho.'px;';
											if($Texto3_font_weight)$str_texto3_font_weight='font-weight:'.$Texto3_font_weight.';';
											if($Texto3_line_height)$str_texto3_line_height='line-height:'.$Texto3_line_height.'px;';
											$style_texto3=$str_texto3_cor.$str_texto3_tamanho.$str_texto3_font_weight.$str_texto3_line_height;
										}
										?>
										<input class="form-control" type="text" name="titulo" id="titulo" value="<?php echo $titulo;?>" style="width:99%;<?php echo $style_titulo?>"/>
									</td>
								</tr>
								</table>
								<br>




								<table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-bottom:5px">
								<tr>

									<?php if($opcoes[$categorias[0]]['titulo_cor']){ ?>
									<td width="80" align="right">ESTILO:&nbsp;</td>
									<td align="left" width="450">
										<table border=0>
										<tr>
										<td align="left">
										<select name="titulo_cor" id="titulo_cor" class="form-control">
											<option value="" <?php if(!$titulo_cor) echo 'selected'?>>Sem Estilo</option><?
											mostra_estilos($titulo_cor);
											?>
										</select>
										</td>
										<td>
										<a href="#" class="preto" onclick="reload_select('titulo_cor');return false;" style="text-decoration:underline" title="Atualizar Select Estilo">Atualizar</a> |
										</td>
										<td>
										<?php if($bloco_img_txt_cor){?><a href="#" onclick="ver_estilo('#titulo_cor');return false;" class="preto" target="_blank" style="text-decoration:underline" title="Ver Estilo Selecionado">Ver Estilo</a> | <?php }?>
										</td>
										<td>
										<a href="../modulos_config/pagina.php?ID=0&dp=<?php echo date('YmdHis');?>&Tipo=1" class="preto" target="_blank" style="text-decoration:underline">Criar Estilo</a>
										</td>
										</tr>
										</table>

									</td>


									<td align="center" width="120" >Ocultar Descrição:</td>
									<td align="left" width="20"><label class="label_check" for="titulo_visivel"><input type="checkbox" name="titulo_visivel" id="titulo_visivel" value="1" <?php if($titulo_visivel==1)echo 'checked="checked"'; ?> /></label></td>
									<td align="center" width="80">Titulo H1:</td>
									<td align="left" width="120"><label class="label_check" for="Tem_H1"><input type="checkbox" name="Tem_H1" id="Tem_H1" value="1" <?php if($Tem_H1==1)echo 'checked="checked"'; ?> /></label></td>
									<td align="left">&nbsp;</td>
									<?php }?>

								</tr>
								</table>
								<?php /* if($categorias[0]==5){?><br>
								<table cellpadding="0" cellspacing="0" border="0" width="100%">
								<tr><td width="80" align="left">Animacão:&nbsp;</td>
								<td><select name="Modulo_animacao" id="Modulo_animacao" class="form-control">
									<option value="" <?php if(!$Modulo_animacao) echo 'selected'?>>---</option>
									<option value="slideup" <?php if($Modulo_animacao=='slideup') echo 'selected';?>>slideup</option>
									<option value="slidedown" <?php if($Modulo_animacao=='slidedown') echo 'selected';?>>slidedown</option>
									<option value="slideright" <?php if($Modulo_animacao=='slideright') echo 'selected';?>>slideright</option>
									<option value="slideleft" <?php if($Modulo_animacao=='slideleft') echo 'selected';?>>slideleft</option>
									<option value="slidehorizontal" <?php if($Modulo_animacao=='slidehorizontal') echo 'selected';?>>slidehorizontal</option>
									<option value="slidevertical" <?php if($Modulo_animacao=='slidevertical') echo 'selected';?>>slidevertical</option>
									<option value="boxslide" <?php if($Modulo_animacao=='boxslide') echo 'selected';?>>boxslide</option>
									<option value="slotslide-horizontal" <?php if($Modulo_animacao=='slotslide-horizontal') echo 'selected';?>>slotslide-horizontal</option>
									<option value="slotslide-vertical" <?php if($Modulo_animacao=='slotslide-vertical') echo 'selected';?>>slotslide-vertical</option>
									<option value="boxfade" <?php if($Modulo_animacao=='boxfade') echo 'selected';?>>boxfade</option>
									<option value="slotfade-horizontal" <?php if($Modulo_animacao=='slotfade-horizontal') echo 'selected';?>>slotfade-horizontal</option>
									<option value="slotfade-vertical" <?php if($Modulo_animacao=='slotfade-vertical') echo 'selected';?>>slotfade-vertical</option>
									<option value="fadefromright" <?php if($Modulo_animacao=='fadefromright') echo 'selected';?>>fadefromright</option>
									<option value="fadefromleft" <?php if($Modulo_animacao=='fadefromleft') echo 'selected';?>>fadefromleft</option>
									<option value="fadefromtop" <?php if($Modulo_animacao=='fadefromtop') echo 'selected';?>>fadefromtop</option>
									<option value="fadefrombottom" <?php if($Modulo_animacao=='fadefrombottom') echo 'selected';?>>fadefrombottom</option>
									<option value="fadetoleftfadefromright" <?php if($Modulo_animacao=='fadetoleftfadefromright') echo 'selected';?>>fadetoleftfadefromright</option>
									<option value="fadetorightfadefromleft" <?php if($Modulo_animacao=='fadetorightfadefromleft') echo 'selected';?>>fadetorightfadefromleft</option>
									<option value="fadetotopfadefrombottom" <?php if($Modulo_animacao=='fadetotopfadefrombottom') echo 'selected';?>>fadetotopfadefrombottom</option>
									<option value="fadetobottomfadefromtop" <?php if($Modulo_animacao=='fadetobottomfadefromtop') echo 'selected';?>>fadetobottomfadefromtop</option>
									<option value="parallaxtoright" <?php if($Modulo_animacao=='parallaxtoright') echo 'selected';?>>parallaxtoright</option>
									<option value="parallaxtoleft" <?php if($Modulo_animacao=='parallaxtoleft') echo 'selected';?>>parallaxtoleft</option>
									<option value="parallaxtotop" <?php if($Modulo_animacao=='parallaxtotop') echo 'selected';?>>parallaxtotop</option>
									<option value="parallaxtobottom" <?php if($Modulo_animacao=='parallaxtobottom') echo 'selected';?>>parallaxtobottom</option>
									<option value="scaledownfromright" <?php if($Modulo_animacao=='scaledownfromright') echo 'selected';?>>scaledownfromright</option>
									<option value="scaledownfromleft" <?php if($Modulo_animacao=='scaledownfromleft') echo 'selected';?>>scaledownfromleft</option>
									<option value="scaledownfromtop" <?php if($Modulo_animacao=='scaledownfromtop') echo 'selected';?>>scaledownfromtop</option>
									<option value="scaledownfrombottom" <?php if($Modulo_animacao=='scaledownfrombottom') echo 'selected';?>>scaledownfrombottom</option>
									<option value="zoomout" <?php if($Modulo_animacao=='zoomout') echo 'selected';?>>zoomout</option>
									<option value="zoomin" <?php if($Modulo_animacao=='zoomin') echo 'selected';?>>zoomin</option>
									<option value="slotzoom-horizontal" <?php if($Modulo_animacao=='slotzoom-horizontal') echo 'selected';?>>slotzoom-horizontal</option>
									<option value="slotzoom-vertical" <?php if($Modulo_animacao=='slotzoom-vertical') echo 'selected';?>>slotzoom-vertical</option>
									<option value="fade" <?php if($Modulo_animacao=='fade') echo 'selected';?>>fade</option>
									<option value="random-static" <?php if($Modulo_animacao=='random-static') echo 'selected';?>>random-static</option>
									<option value="random" <?php if($Modulo_animacao=='random') echo 'selected';?>>random</option>
									</select>
									</td>
								</tr>
								</table>
								<?php } */?>
							</td>

						</tr>
						<tr><td style="height:5x;border-top:#e2e7eb 1px solid" colspan="2">&nbsp;</td></tr>
					<?
					}

					?>
					</table>
					<table width="100%" cellpadding="0" cellspacing="0" border="0"><?
					if($opcoes[$categorias[0]]['Texto1']) // && ($resumo || $categorias[0]==16)  foi adicionada a validacao do resumo para ser compativel com antigos
					{
					?>
						<tr>
						<td align="right">COR:&nbsp;</td>
						<td><select name="Texto1_cor" id="Texto1_cor" class="form-control">
						<option value="" <?php if(!$Texto1_cor) echo 'selected'?>>---</option>
						<?php mostra_estilos($Texto1_cor);?>
						</select>
						</td>
						</tr>

						<tr>
							<td width="80" align="right">TEXTO 1:</td>
							<td align="left"><textarea class="form-control" type="text" name="resumo" id="resumo" style="width:100%;height:100px;<?php echo $style_texto1?>"><?php echo $resumo;?></textarea></td>
						</tr>

						<tr><td style="height:5x;border-top:#e2e7eb 1px solid" colspan="2">&nbsp;</td></tr>
						<?php
					}
					if($opcoes[$categorias[0]]['Texto2'] && $texto)
					{
					?>
						<tr>
							<td width="130" align="left">TEXTO 2:</td>
							<td align="left"><textarea class="form-control" type="text" name="texto" id="texto" style="width:100%;height:150px;<?php echo $style_texto2?>"><?php echo $texto;?></textarea></td>
						</tr>
						<tr>
							<td width="130" align="left"></td>
							<td align="left">
								<table cellpadding="0" cellspacing="0" border="0">
								<tr>
									<td align="left">Cor:&nbsp;</td>
									<td><select name="Texto2_cor"  class="form-control">
											<option value="" <?php if(!$Texto2_cor) echo 'selected'?>>---</option>
											<?php mostra_estilos($Texto2_cor);?>
										</select>
									</td>
								</tr>
								</table>
							</td>
						</tr>
					<tr><td style="height:5x;border-top:#e2e7eb 1px solid" colspan="2">&nbsp;</td></tr>
					<?php
					}

					if($breadcrumb)
					{
					?>
						<tr>
							<td width="130" align="left">Breadcrumb home icono ou texto:</td>
							<td align="left">
								<table>
									<tr>
										<td>
											<label>
												<input type="radio" name="bc_hometype" value="1" <?=$bc_hometype==1?'checked':''?>> Texto
										    </label>


										</td>

										<td>
											<input type="text" name="bc_homename" value="<?=$bc_homename==''?'HOME':$bc_homename?>">
										</td>

										<td>


										</td>

									</tr>

									<tr>

										<td>
											 <label>
												<input type="radio" name="bc_hometype" value="2" <?=$bc_hometype==2?'checked':''?>> Icon <i class="fas fa-home fa-2x" style="color:#000000;"></i>
											</label>
										</td>
									</tr>

								</table>

							</td>
						</tr>
						<tr>
							<td width="130" align="left"></td>
							<td align="left">

							</td>
						</tr>
					<tr><td style="height:5x;border-top:#e2e7eb 1px solid" colspan="2">&nbsp;</td></tr>
					<?php
					}

					if($opcoes[$categorias[0]]['Texto3'] && $texto3)
					{
					?>
						<tr>
							<td width="130" align="left">TEXTO 3:</td>
							<td align="left"><textarea class="form-control" type="text" name="texto3" id="texto3" style="width:100%;height:150px;<?php echo $style_texto3?>"><?php echo $texto3;?></textarea></td>
						</tr>
						<tr>
							<td width="130" align="left"></td>
							<td align="left">
								<table cellpadding="0" cellspacing="0" border="0">
								<tr>
									<td align="left">COR:&nbsp;</td>
									<td>
										<select name="Texto3_cor" class="form-control">
											<option value="" <?php if(!$Texto3_cor) echo 'selected'?>>---</option>
											<?php mostra_estilos($Texto3_cor);?>
										</select>
									</td>
								</tr>
								</table>
							</td>
						</tr>
					<tr><td style="height:5x;border-top:#e2e7eb 1px solid" colspan="2">&nbsp;</td></tr>
					<?
					}?>
					</table>
					<?php if($opcoes[$categorias[0]]['Imagem_bg'])
					{?>
					<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td width="130" align="left">Imagem de fundo:</td>
						<td align="left"><?
							if($img_ext && file_exists('../../files/documentos/'.$img_ext))
							{
								list($w,$h) = @getimagesize('../../files/documentos/'.$img_ext);
								if($w > $h) $st = 'style="height:100px;max-height:'.$h.'px"';
								elseif($w < $h) $st = 'style="width:100px;max-width:'.$w.'px"';
								else $st = 'style="max-width:100px"';

								echo '<a class="pretourl_small2 fancybox_img" href="../../files/documentos/'.$img_ext.'" target="_blank" class="url_reno"><img src="../../files/documentos/'.$img_ext.'" '.$st.' border=0></a> | ';
								echo '<a class="preto" href="../edit_image/edit_image.php?w=619&h=609&src=../../files/documentos/'.$img_ext.'&ext=_enc" target="_blank" style="font-weight:bold;font-size:12px">Editar imagem</a><br /><br />';

							   echo 'Estilo do background: ';?>
							   <select name="Estilo_Back" class="form-control" style="width: 200px">
								<option value="" <?php if(!$Estilo_Back) echo 'selected'?>>O background acompanha a página</option>
								<option value="1" <?php if($Estilo_Back=='1') echo 'selected'?>>Background fixo</option>
								<option value="2" <?php if($Estilo_Back=='2') echo 'selected'?>>Background Paralax</option>
								<option value="3" <?php if($Estilo_Back=='3') echo 'selected'?>>Cover</option>
								<option value="4" <?php if($Estilo_Back=='4') echo 'selected'?>>Fixo e Cover</option>
							</select><br><?
							}?><input type="file" name="img_ext" id="img_ext" />
						</td>
					</tr>
					<tr><td style="height:5x;border-top:#e2e7eb 1px solid" colspan="2">&nbsp;</td></tr>

					</table><?
					}
					if($opcoes[$categorias[0]]['Layout'])
					{?>
					<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td width="130" align="left">Tipo de Layout:</td>
						<td align="left">
							<select name="Layout" class="form-control">
								<option value="" <?php if(!$Layout) echo 'selected'?>>Imagem à esquerda e Texto à direita</option>
								<option value="1" <?php if($Layout=='1') echo 'selected'?>>Imagem à direita e Texto à esquerda</option>
								<option value="2" <?php if($Layout=='2') echo 'selected'?>>Alternar Imagem com texto</option>
							</select>
						</td>
					</tr>
					<tr><td style="height:5x;border-top:#e2e7eb 1px solid" colspan="2">&nbsp;</td></tr>

					</table><?
					}
					if($opcoes[$categorias[0]]['Estilo'])
					{
						if($categorias[0]==5)//Carrocel
						{
						?>
							<table width="100%" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td width="130" align="left">ESTILO:</td>
								<td align="left">
									<select name="Estilo" class="form-control">
										<option value="" <?php if(!$Estilo) echo 'selected'?>>Simples, apenas imagens</option>
										<option value="1" <?php if($Estilo=='1') echo 'selected'?>>Imagens com Links</option>
										<option value="2" <?php if($Estilo=='2') echo 'selected'?>>Imagens mais descritivos</option>
										<option value="3" <?php if($Estilo=='3') echo 'selected'?>>Tipo Testemunhos</option>
										<option value="4" <?php if($Estilo=='4') echo 'selected'?>>Tipo Notícias</option>
										<option value="5" <?php if($Estilo=='5') echo 'selected'?>>Tipo Stacked Cards</option>
									</select>
								</td>
							</tr>
							<tr><td style="height:5x;border-top:#e2e7eb 1px solid" colspan="2">&nbsp;</td></tr>

							</table><?
						}
						elseif($categorias[0]==11)
						{
						?>
							<table width="100%" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td width="130" align="left">Estilo:</td>
								<td align="left">
									<select name="Estilo" class="form-control" style="width:auto">
										<option value="" <?php if(!$Estilo) echo 'selected'?>>Standard</option>
										<option value="1" <?php if($Estilo=='1') echo 'selected'?>>snapGallery</option>
										<option value="2" <?php if($Estilo=='2') echo 'selected'?>>Sem zoom</option>
									</select>
								</td>
							</tr>
							<tr><td style="height:5x;border-top:#e2e7eb 1px solid" colspan="2">&nbsp;</td></tr>

							</table><?
						}
					   else
					   {?>
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td width="130" align="left">Estilo:</td>
							<td align="left">
								<select name="Estilo" class="form-control">
									<option value="" <?php if(!$Estilo) echo 'selected'?>>Em linha</option>
									<option value="1" <?php if($Estilo=='1') echo 'selected'?>>TABS</option>
									<option value="2" <?php if($Estilo=='2') echo 'selected'?>>En linha bottões</option>
								</select>
							</td>
						</tr>
						<tr><td style="height:5x;border-top:#e2e7eb 1px solid" colspan="2">&nbsp;</td></tr>

						</table><?
					   }
					}
					if($opcoes[$categorias[0]]['Imagens']) require_once 'tab_imagens.php';?>
				</div>
			</td>
		</tr>

		</table>
		<?php show_page_buttons();?>
	</td>
	<td width="10px">&nbsp;</td>
	<td valign="top" width="200px"><?php show_fast_tools();?>&nbsp;<?php show_easy_create()?></td>
	</tr>
	</table>
	</form>
	<?php
	if(!isset($_GET['duplicar']) && isset($_GET[TBID]) && $_GET[TBID] > 0)
	{?>
	<br />
	<form action="pagina.php?<?php echo TBID?>=<?php echo (isset($_GET['duplicar']) && $_GET['duplicar'] > 0) ? 0 : $ID;?>&d=<?php echo uniqID("")?>" method="POST" name="lista_paginas" id="lista_paginas" enctype="multipart/form-data">

		<table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
		<tr><?
				echo '<td valign="top" align="left">';
				// if(isset($_GET[TBID]) && $_GET[TBID] > 0)
				// {
					$res=show_content_list($_GET[TBID]);
					if($res) show_page_bottom_tools();
				// }
				echo '</td>';
				echo '<td width="10px">&nbsp;</td>';
				echo '<td width="200">&nbsp;</td>';
			?>
		</tr>
		</table>
	</form><?
	}?>
	</div>
	<script src="chosen.jquery.min.js" type="text/javascript"></script>
	<script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true}); </script><br /><br /><?
	}
	else
	{
		echo show_alerta('Não tem permissões para aceder a esta funcionalidade!','erro');
	}?>
	<script>var editor = CodeMirror.fromTextArea(document.getElementById("resumo"), { lineNumbers: true, styleActiveLine: true, lineNumbers: true, lineWrapping: true});</script>
	<?php require_once "../layout/bottom.inc.php";?>