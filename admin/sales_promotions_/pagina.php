<?php
	require_once "settings.inc.php";
	if($task=="apagar"){$task_button = write_lang('key_apagar_este_registo','Apagar este Registo');$task_form="apaga";}
	elseif($task=="duplicar"){$task_button = write_lang('key_duplicar_este_registo','Duplicar este Registo');$task_form="duplicar";}
	elseif($_GET[TBID]==0){$task_button = write_lang('key_gravar_novo_registo','Gravar Novo Registo');$task_form="insere";}
	else {$task_button = write_lang('key_gravar_alteracoes','Gravar alterações');$task_form="altera";}
	$D = getdate();

	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;

    $fields=db_list_fields($DB_name,TBMODULO,$db_link);
    $columns=db_num_fields($fields);
    for ($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
    if(!in_array('QUATIDADEMAXIMA',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE '.TBMODULO.' ADD QUATIDADEMAXIMA INT(11) DEFAULT 0');  }


	 $fields=db_list_fields($DB_name,'sales_promotions_products',$db_link);
    $columns=db_num_fields($fields);
    for ($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
    if(!in_array('FLAG',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE sales_promotions_products ADD FLAG tinyint(1) DEFAULT 0');  }

	if(isset($_GET[TBID]) && $_GET[TBID]!=0)
	{
		$rs = new DataObj;
		$sql = 'SELECT * FROM '.TBMODULO.' WHERE '.TBID.'='.$_GET[TBID];
		$rs->db_open($db_link, $DB_name, $sql);
		if(!$rs->EOF())
		{
			$promo_id = $rs->db_data['promo_id'];
			$ATIVO = $rs->db_data['ATIVO'];
			$DATA_INICIO = $rs->db_data['DATA_INICIO'];
			$DATA_FIM = $rs->db_data['DATA_FIM'];
			$DESIGNACAO = $rs->db_data['DESIGNACAO'];
			$TIPO = $rs->db_data['TIPO'];
			$MINIMO = $rs->db_data['MINIMO'];
			$MAXIMO = $rs->db_data['MAXIMO'];
			$QUATIDADEMAXIMA = $rs->db_data['QUATIDADEMAXIMA'];
			$TIPOPROMOCAO = $rs->db_data['TIPOPROMOCAO'];
			$TIPOCAMPANHA = $rs->db_data['TIPOCAMPANHA'];
			$DATACRIACAO = $rs->db_data['DATACRIACAO'];
			$DATAALTERACAO = $rs->db_data['DATAALTERACAO'];
			$user_sp = $rs->db_data['user'];
			$IMAGEM_EXT = $rs->db_data['IMAGEM_EXT'];
			$DESCRICAO = $rs->db_data['DESCRICAO'];
			$TEMPO = $rs->db_data['TEMPO'];

			if($TIPOPROMOCAO==1 || $TIPOPROMOCAO==4) $MINIMO1 = $MINIMO; else $MINIMO1 = '';
			if($TIPOPROMOCAO==2) $MINIMO2 = $MINIMO; else $MINIMO2 = '';
			if($TIPOPROMOCAO==3) $MINIMO3 = $MINIMO; else $MINIMO3 = '';


		}
		$rs->db_close();
	}
	require_once "../layout/header.inc.php";
	require_once "../layout/body.inc.php";
?>
<link rel="stylesheet" type="text/css" href="sa.css" />
<script>var tabsel = '<?php echo (isset($_GET['tab']) && $_GET['tab'] > 0) ? $_GET['tab'] : '1';?>';</script>
<script>var tabsel2 = '<?php echo (isset($_GET['tab2']) && $_GET['tab2'] > 0) ? $_GET['tab2'] : '1';?>';</script>
<script>var tabsel3 = '<?php echo (isset($_GET['tab3']) && $_GET['tab3'] > 0) ? $_GET['tab3'] : '1';?>';</script>
<script>var SITE_HTTP_URL = '<?php echo $GLOBALS['site_http_url']?>';var SITE_SHELL_URL = '<?php echo $GLOBALS['site_shell_url']?>';</script>
<script>

$(document).ready(function()
{
	$('.fancybox_img').fancybox();
	$('.fancybox2').live('click', function(){ var href = $(this).attr('href'); $.fancybox({ 'showCloseButton' : false, 'titlePosition' : 'inside', title : true, autoSize : false, modal : false, fitToView : false, topRatio : 0, content : '<iframe width="100%" height="100%" src="'+href+'"></iframe>', width : 754, maxHeight : 600, scrolling : 'no', type : 'html', onComplete: function() {$("#fancybox-wrap").stop().animate({'top':'10px',}, 0); $("#fancybox-wrap").css({'z-index':'9999999999999999',}, 0); } }); return false;});
	$('.fancybox3').live('click', function(){ var href = $(this).attr('href'); $.fancybox({ 'beforeClose': function() {window.open('pagina.php?promo_id=<?php echo $_GET['promo_id'];?>&tab=3','_self'); },'closeClick': true, 'showCloseButton' : false, 'titlePosition' : 'inside', title : true, autoSize : false, modal : false, fitToView : false, topRatio : 0, content : '<iframe width="100%" height="100%" src="'+href+'"></iframe>', width : 1000, maxHeight : 600, scrolling : 'no', type : 'html', onComplete: function() {$("#fancybox-wrap").stop().animate({'top':'10px',}, 0); $("#fancybox-wrap").css({'z-index':'9999999999999999',}, 0); } }); return false;});

/* 	$('.fancybox3').fancybox({
		'beforeClose': function() { alert('test') },
		'closeClick': true
	}); */

	$("input[name=TIPOPROMOCAO]:radio").change(function()
	{
		if($(this).val()==1)
		{
			if($('#MINIMO1').val() > 0){ $('#MINIMO').val($('#MINIMO1').val());}
			else if($('#MINIMO2').val() > 0){ $('#MINIMO1').val($('#MINIMO2').val()); $('#MINIMO').val($('#MINIMO2').val()); $('#MINIMO2').val('');}
			else if($('#MINIMO3').val() > 0){ $('#MINIMO1').val($('#MINIMO3').val()); $('#MINIMO').val($('#MINIMO3').val()); $('#MINIMO3').val('');}
		}
		if($(this).val()==2)
		{
			if($('#MINIMO1').val() > 0){ $('#MINIMO2').val($('#MINIMO1').val()); $('#MINIMO').val($('#MINIMO1').val()); $('#MINIMO1').val('');}
			else if($('#MINIMO2').val() > 0){$('#MINIMO').val($('#MINIMO2').val());}
			else if($('#MINIMO3').val() > 0){ $('#MINIMO2').val($('#MINIMO3').val()); $('#MINIMO').val($('#MINIMO3').val()); $('#MINIMO3').val('');}
		}
		if($(this).val()==3)
		{
			if($('#MINIMO1').val() > 0){ $('#MINIMO3').val($('#MINIMO1').val()); $('#MINIMO').val($('#MINIMO1').val()); $('#MINIMO1').val('');}
			else if($('#MINIMO2').val() > 0){ $('#MINIMO3').val($('#MINIMO2').val()); $('#MINIMO').val($('#MINIMO2').val()); $('#MINIMO2').val('');}
			else if($('#MINIMO3').val() > 0){ $('#MINIMO').val($('#MINIMO3').val());}
		}
		if($(this).val()==4)
		{
			if($('#MINIMO1').val() > 0){ $('#MINIMO').val($('#MINIMO1').val());}
			else if($('#MINIMO2').val() > 0){ $('#MINIMO1').val($('#MINIMO2').val()); $('#MINIMO').val($('#MINIMO2').val()); $('#MINIMO2').val('');}
			else if($('#MINIMO3').val() > 0){ $('#MINIMO1').val($('#MINIMO3').val()); $('#MINIMO').val($('#MINIMO3').val()); $('#MINIMO3').val('');}
		}
	});

	$("input[name=MINIMO1]").focus(function ()
	{

		if($('#MINIMO1').val() > 0){ $('#MINIMO').val($('#MINIMO1').val());}
		else if($('#MINIMO2').val() > 0){ $('#MINIMO1').val($('#MINIMO2').val()); $('#MINIMO').val($('#MINIMO2').val()); $('#MINIMO2').val('');}
		else if($('#MINIMO3').val() > 0){ $('#MINIMO1').val($('#MINIMO3').val()); $('#MINIMO').val($('#MINIMO3').val()); $('#MINIMO3').val('');}

		if($('#INTERVALO_QUANTIDADES2').is(':checked')) $("#INTERVALO_QUANTIDADES2").attr('checked', 'checked');
		else $("#INTERVALO_QUANTIDADES").attr('checked', 'checked');

		setupLabel();
	});
	$("input[name=MINIMO1]").keyup(function (){ $('#MINIMO').val($('#MINIMO1').val()); });

	$("input[name=MINIMO2]").focus(function ()
	{
		if($('#MINIMO1').val() > 0){ $('#MINIMO2').val($('#MINIMO1').val()); $('#MINIMO').val($('#MINIMO1').val()); $('#MINIMO1').val('');}
		else if($('#MINIMO2').val() > 0){$('#MINIMO').val($('#MINIMO2').val());}
		else if($('#MINIMO3').val() > 0){ $('#MINIMO2').val($('#MINIMO3').val()); $('#MINIMO').val($('#MINIMO3').val()); $('#MINIMO3').val('');}

		$("#INTERVALO_VALORES").attr('checked', 'checked');	setupLabel();
	});
	$("input[name=MINIMO2]").keyup(function (){ $('#MINIMO').val($('#MINIMO2').val()); });

	$("input[name=MINIMO3]").focus(function ()
	{
		if($('#MINIMO1').val() > 0){ $('#MINIMO3').val($('#MINIMO1').val()); $('#MINIMO').val($('#MINIMO1').val()); $('#MINIMO1').val('');}
		else if($('#MINIMO2').val() > 0){ $('#MINIMO3').val($('#MINIMO2').val()); $('#MINIMO').val($('#MINIMO2').val()); $('#MINIMO2').val('');}
		else if($('#MINIMO3').val() > 0){ $('#MINIMO').val($('#MINIMO3').val());}

		$("#VALOR_ACUMULADO").attr('checked', 'checked'); setupLabel();
	});
	$("input[name=MINIMO3]").keyup(function (){ $('#MINIMO').val($('#MINIMO3').val()); });


});


</script>
<script language="JavaScript" src="sa.js"></script>
<div class="center" style="position:relative;" id="wp">

<table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
<td valign="top" align="left"><?php include "../layout/menus.inc.php"; ?>
<div class="panel panel-inverse overflow-hidden m-b-12">
        <div class="panel-heading">
            <h4 style="color:#fff">PROMOÇÕES</h4>
        </div>
    </div>
<table class="titulo_janela" width="100%" height="30px" cellspacing="0" cellpadding="5" border="0">
<tr>
	<td valign="middle"><a href="list.php" class="branco"> &#60; Voltar para a lista de <?php echo MODULO.'</a>'; if(isset($DESIGNACAO) && $DESIGNACAO) echo ' - '.$DESIGNACAO;?> </td>
</tr>
</table>
<table class="new_quadros" style="background:#FFF" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
<tr>
<td align="left" valign="top" class="preto" style="background:#FFF">
<div class="wraper" id="wraper">
<div class="bigtitletab" onclick="infinittab(1);return false;" id="menutab1">Geral</div>

<?php if(isset($_GET['promo_id']) && $_GET['promo_id'] > 0){?>
<div class="bigtitletaboff" onclick="infinittab(3);return false;" id="menutab3">Configurações Descontos</div>
<?php }?>

<div class="clearboth"></div>
<form name="formpromocoes" method="post" action="" enctype="multipart/form-data">
<input type="hidden" name="DATACRIACAO" value="<?php echo (isset($DATACRIACAO) && $DATACRIACAO!='0000-00-00') ? $DATACRIACAO : date('Y-m-d')?>" />
<input type="hidden" name="DATAALTERACAO" value="<?php echo (isset($DATACRIACAO) && $DATACRIACAO!='0000-00-00') ? date('Y-m-d') : '0000-00-00'?>" />
<input type="hidden" name="user" value="<?php echo $user_sp.'['.date('YmdHis').'/'.$GLOBALS['admin_user'].'/'.$GLOBALS['admin_id'].']'?>" />
<input type="hidden" name="m_lang" value="<?php echo $GLOBALS['admin_lang'];?>" />
<input type="hidden" name="<?php echo TBID?>" value="<?php echo $promo_id;?>" />
<input type="hidden" name="ctab" id="ctab" value="<?php echo (isset($_GET['tab']) && $_GET['tab'] > 0) ? $_GET['tab'] : '1';?>" />
<input type="hidden" name="ctab2" id="ctab2" value="<?php echo (isset($_GET['tab2']) && $_GET['tab2'] > 0) ? $_GET['tab2'] : '1';?>" />
<input type="hidden" name="ctab3" id="ctab3" value="<?php echo (isset($_GET['tab3']) && $_GET['tab3'] > 0) ? $_GET['tab3'] : '1';?>" />
<input type="hidden" name="slashes" value="'" />
<div class="tabtop-open3" id="tab1"><?php require_once 'tab_opcoes.php';?><?php require_once 'tab_dados.php';?></div>

</form>
<div class="tabtop-open3" id="tab3"><?php require_once 'tab_produtos.php';?></div>

</div>
<?php show_page_buttons();?>
</td>
</tr>
</table>
</td>
<td width="10px">&nbsp;</td>
<td valign="top" width="200"><?php show_fast_tools();?></td>
</tr>
</table>
<img src="../images/bsh.png" width="100%" height="30px">
</div>
<?php require_once "../layout/bottom.inc.php";?>