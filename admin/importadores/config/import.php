<?php
	ini_set('max_execution_time','0');
	ini_set('memory_limit','1024M');

	if(isset($argv) && $argv[1] > 0)$Supplier_ID=intval($argv[1]);
	if(isset($_GET['Supplier_ID']))$Supplier_ID=intval($_GET['Supplier_ID']);

    require_once('settings.inc.php');
    require_once('../../layout/header.inc.php');
    require_once('../../layout/body.inc.php');

	// VAMOS ATUALIZAR TODAS AS FAMILIAS COM UM CÓDIGO ESPECIAL
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

	error_reporting(E_ALL);
		ini_set('display_errors',1);
		ini_set('display_startup_errors',1);
		error_reporting(0);

	$field_array = array();
	$fields=db_list_fields($DB_name,'a_importador_configuracoes',$db_link);
	$columns=db_num_fields($fields);
	for ($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
	if(!in_array('Preco2_IVA',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE a_importador_configuracoes ADD Preco2_IVA varchar(255) DEFAULT ""');}
	if(!in_array('Preco3_IVA',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE a_importador_configuracoes ADD Preco3_IVA varchar(255) DEFAULT ""');}
	if(!in_array('Margem',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE a_importador_configuracoes ADD Margem double DEFAULT "0"');}



	/*$field_array = array();
	$fields=db_list_fields($DB_name,'a_importador_configuracoes',$db_link);
	$columns=db_num_fields($fields);
	for ($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
	if(!in_array('ftp_caminho',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE a_importador_configuracoes ADD ftp_caminho varchar(255) DEFAULT ""');}
	if(!in_array('ftp_servidor',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE a_importador_configuracoes ADD ftp_servidor varchar(255) DEFAULT ""');}
	if(!in_array('ftp_porta',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE a_importador_configuracoes ADD ftp_porta int(11) DEFAULT 0');}
	if(!in_array('ftp_user',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE a_importador_configuracoes ADD ftp_user varchar(255) DEFAULT ""');}
	if(!in_array('ftp_password',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE a_importador_configuracoes ADD ftp_password varchar(255) DEFAULT ""');}
	if(!in_array('fora_gama',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE a_importador_configuracoes ADD fora_gama varchar(255) DEFAULT ""');}
	if(!in_array('fora_gama_codigo',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE a_importador_configuracoes ADD fora_gama_codigo varchar(255) DEFAULT ""');}
	if(!in_array('log_activation',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE a_importador_configuracoes ADD log_activation tinyint(1) DEFAULT 0');}
	if(!in_array('email_address',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE a_importador_configuracoes ADD email_address varchar(255) DEFAULT ""');}
	if(!in_array('cron_activation',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE a_importador_configuracoes ADD cron_activation tinyint(1) DEFAULT 0');}
	if(!in_array('cron',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE a_importador_configuracoes ADD cron varchar(255) DEFAULT ""');}
	if(!in_array('price_conf',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE a_importador_configuracoes ADD price_conf tinyint(1) DEFAULT 0');}
	if(!in_array('conf4',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE a_importador_configuracoes ADD conf4 tinyint(1) DEFAULT 0');}
	if(!in_array('Separador_decimais',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE a_importador_configuracoes ADD Separador_decimais char(1) DEFAULT ""');}
	if(!in_array('ficheiro_ftp1',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE a_importador_configuracoes ADD ficheiro_ftp1 varchar(255) DEFAULT ""');}
	if(!in_array('ficheiro_ftp2',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE a_importador_configuracoes ADD ficheiro_ftp2 varchar(255) DEFAULT ""');}
	if(!in_array('ficheiro_ftp3',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE a_importador_configuracoes ADD ficheiro_ftp3 varchar(255) DEFAULT ""');}

	$field_array = array();
	$fields=db_list_fields($DB_name,'products',$db_link);
	$columns=db_num_fields($fields);
	for ($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
	if(!in_array('codigo_importacao',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD codigo_importacao varchar(255) DEFAULT ""');}
	if(!in_array('ficheiro_caminho_json',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE a_importador_configuracoes ADD ficheiro_caminho_json varchar(255) DEFAULT ""');}
	if(!in_array('ficheiro_caminho_api',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE a_importador_configuracoes ADD ficheiro_caminho_api varchar(255) DEFAULT ""');}
	if(!in_array('ficheiro_caminho_api_login',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE a_importador_configuracoes ADD ficheiro_caminho_api_login varchar(255) DEFAULT ""');}
	if(!in_array('ficheiro_caminho_api_pass',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE a_importador_configuracoes ADD ficheiro_caminho_api_pass varchar(255) DEFAULT ""');}
	if(!in_array('codigo_fam_default',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE a_importador_configuracoes ADD codigo_fam_default int(11) DEFAULT 0');}
	if(!in_array('marcas_atraves_nome',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE a_importador_configuracoes ADD marcas_atraves_nome tinyint(1) DEFAULT 0');}
	if(!in_array('cria_marcas_novas',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE a_importador_configuracoes ADD cria_marcas_novas tinyint(1) DEFAULT 0');}
	if(!in_array('key_field',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE a_importador_configuracoes ADD key_field tinyint(1) DEFAULT 0');}

	$field_array = array();
	$fields=db_list_fields($DB_name,'filters_values_lang',$db_link);
	$columns=db_num_fields($fields);
	for ($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
	if(!in_array('values_name2',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE filters_values_lang ADD values_name2 varchar(255) DEFAULT ""');}

	$field_array = array();
	$fields=db_list_fields($DB_name,'filters_categories_lang',$db_link);
	$columns=db_num_fields($fields);
	for ($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
	if(!in_array('category_name2',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE filters_categories_lang ADD category_name2 varchar(255) DEFAULT ""');}

	$field_array = array();
	$fields=db_list_fields($DB_name,'products_objs_history',$db_link);
	$columns=db_num_fields($fields);
	for ($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
	if(!in_array('Ref_loja',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_objs_history ADD Ref_loja varchar(255) DEFAULT ""');}*/

	$obj = new DataObj;
	$conf1 = 0;
	$conf4 = 1;
	$sql2 = 'SELECT * FROM a_importador_configuracoes WHERE conf_id='.$Supplier_ID;
	$obj->db_open($db_link,$DB_name,$sql2);
	$upd2 = '';
	if(!$obj->EOF())
	{
		$Marca = stripslashes($obj->db_data['Marca']);
		$MarcaDesc = stripslashes($obj->db_data['MarcaDesc']);
		$Categoria = stripslashes($obj->db_data['Categoria']);
		$CategoriaDesc = stripslashes($obj->db_data['CategoriaDesc']);
		$Sub_Categoria = stripslashes($obj->db_data['Sub_Categoria']);
		$Sub_CategoriaDesc = stripslashes($obj->db_data['Sub_CategoriaDesc']);
		$Sub_Categoria2 = stripslashes($obj->db_data['Sub_Categoria2']);
		$Sub_Categoria2Desc = stripslashes($obj->db_data['Sub_Categoria2Desc']);
		$Codigo_produto = stripslashes($obj->db_data['Codigo_produto']);
		$Produto = stripslashes($obj->db_data['Produto']);
		$Descricao = stripslashes($obj->db_data['Descricao']);
		$Preco_IVA = stripslashes($obj->db_data['Preco_IVA']);
		$Preco2_IVA = stripslashes($obj->db_data['Preco2_IVA']);
		$Preco3_IVA = stripslashes($obj->db_data['Preco3_IVA']);
		$Margem = stripslashes($obj->db_data['Margem']);
		$EAN = stripslashes($obj->db_data['EAN']);
		$Stock = stripslashes($obj->db_data['Stock']);
		$Imagem1 = stripslashes($obj->db_data['Imagem1']);
		$Imagem2 = stripslashes($obj->db_data['Imagem2']);
		$Imagem3 = stripslashes($obj->db_data['Imagem3']);
		$Imagem4 = stripslashes($obj->db_data['Imagem4']);
		$Separador = stripslashes($obj->db_data['Separador']);
		$Separador2 = htmlentities(stripslashes($obj->db_data['Separador2']));
		$conf1 = $obj->db_data['conf1'];
		$conf2 = $obj->db_data['conf2'];
		$conf3 = $obj->db_data['conf3'];
		$conf4 = $obj->db_data['conf4'];
		$Separador_decimais = $obj->db_data['Separador_decimais'];
		$Preco_conf = $obj->db_data['Preco_conf'];
		$Preco_iva_valor = $obj->db_data['Preco_iva_valor'];
		$Imagem_conf = $obj->db_data['Imagem_conf'];
		$Imagem_pasta = $obj->db_data['Imagem_pasta'];
		$Peso = $obj->db_data['Peso'];
		$ftp_servidor = $obj->db_data['ftp_servidor'];
		$ftp_porta = $obj->db_data['ftp_porta'];
		$ftp_user = $obj->db_data['ftp_user'];
		$ftp_password = $obj->db_data['ftp_password'];
		$ftp_passivo = $obj->db_data['ftp_passivo'];
		$ftp_caminho = $obj->db_data['ftp_caminho'];
		$ficheiro_caminho = $obj->db_data['ficheiro_caminho'];
		$ficheiro_caminho_json = $obj->db_data['ficheiro_caminho_json'];
		$ficheiro_caminho_api = $obj->db_data['ficheiro_caminho_api'];
		$ficheiro_caminho_api_login = $obj->db_data['ficheiro_caminho_api_login'];
		$ficheiro_caminho_api_pass = $obj->db_data['ficheiro_caminho_api_pass'];
		$tipo_importacao = $obj->db_data['tipo_importacao'];
		$ficheiro_ftp = $obj->db_data['ficheiro_ftp'];
		$ficheiro_ftp1 = $obj->db_data['ficheiro_ftp1'];
		$ficheiro_ftp2 = $obj->db_data['ficheiro_ftp2'];
		$ficheiro_ftp3 = $obj->db_data['ficheiro_ftp3'];
		$Descricao2 = $obj->db_data['Descricao2'];
		$Classe_energetica = $obj->db_data['Classe_energetica'];
		$Link_Classe_Energetica = $obj->db_data['Link_Classe_Energetica'];
		$Stock1 = $obj->db_data['Stock1'];
		$Stock2 = $obj->db_data['Stock2'];
		$Stock3 = $obj->db_data['Stock3'];
		$Stock4 = $obj->db_data['Stock4'];
		$codigo_importacao = $obj->db_data['codigo_importacao'];
		$Eliminar_conf = $obj->db_data['Eliminar_conf'];
		$fora_gama = $obj->db_data['fora_gama'];
		$fora_gama_codigo = $obj->db_data['fora_gama_codigo'];
		$log_activation = $obj->db_data['log_activation'];
		$email_address = $obj->db_data['email_address'];
		$cron_activation = $obj->db_data['cron_activation'];
		$cron = $obj->db_data['cron'];
		$price_conf = $obj->db_data['price_conf'];
		$codigo_fam_default = $obj->db_data['codigo_fam_default'];
		$marcas_atraves_nome = $obj->db_data['marcas_atraves_nome'];
		$cria_marcas_novas = $obj->db_data['cria_marcas_novas'];
		$key_field = $obj->db_data['key_field'];
	}
	$obj->db_close();

	if(!isset($codigo_importacao) || (isset($codigo_importacao) && $codigo_importacao == '')) $codigo_importacao = 'IMPNOVA';
?>
<style>
	.botao{padding:0 5px}
	.container{width:100%;margin:0 auto}
	.container label{display:block;padding:5px 0}
	ul.tabs{margin:0px;padding:0px;list-style:none}
	ul.tabs li{background:none;color:#222;display:inline-block;padding:5px 10px;cursor:pointer;font-weight:bold;border-right:solid 1px;border-top:solid 1px;font-size:11px;border-top-left-radius:4px;border-top-right-radius:4px}
	ul.tabs li.current{background:#FFF;color:#222}
	.tab-content{display:none;background:#FFF;padding:15px;font-size:12px}
	.tab-content.current{display:inherit}
	/*input, select {height:20px}*/
</style>
<script>
function close_prod(e)
{
	$(e).css({"opacity":"0"});
	$(e).hide();
	$(e).html('');
	var e2 = e+'_x';
	$('html, body').animate({     scrollTop: ($(e2).offset().top-300) + 'px'   }, 1000, 'swing');
}
function load_prod(e,url)
{
	$(e).html('loading....');
	$(e).load(url,function() {
		$(e).css({"opacity":"0"});
		$(e).show();
		// smooth scroll to the anchor id
		$('html, body').animate({     scrollTop: ($(e).offset().top-150) + 'px'   }, 1000, 'swing');
		$(e).animate({"opacity":"1","transition":"opacity .5s ease-in-out"}, 'slow');
	});
}
$(document).ready(function(){
	$('ul.tabs li').click(function(){
		var tab_id = $(this).attr('data-tab');
		$('#tab_sel').val(tab_id);
		$('ul.tabs li').removeClass('current');
		$('.tab-content').removeClass('current');
		$(this).addClass('current');
		$("#"+tab_id).addClass('current');
	});
	<?php
	if(isset($_GET['tab_sel']) && $_GET['tab_sel'] !='')
	{?>
		var tab_id = '<?php echo $_GET['tab_sel'];?>';
		$('#tab_sel').val(tab_id);
		$('ul.tabs li').removeClass('current');
		$('.tab-content').removeClass('current');
		$("#"+tab_id+"-t").addClass('current');
		$("#"+tab_id).addClass('current');
		<?php
	}
	?>
	$('.fancybox_url').live('click', function(){ var href = $(this).attr('href');$.fancybox({ 'showCloseButton' :false, 'titlePosition' :'inside', title :true, autoSize :false, modal :false, fitToView :false, topRatio :0, content :'<iframe width="100%" height="100%" src="'+href+'"></iframe>', width :754, maxHeight :600, scrolling :'no', type :'html', onComplete:function() {$("#fancybox-wrap").stop().animate({'top':'10px',}, 0);$("#fancybox-wrap").css({'z-index':'9999999999999999',}, 0);} });return false;});
});
function test_conection()
{
	$.ajax({type:'GET',url:"testa_conexao.php?Supplier_ID=<?=$Supplier_ID?>",dataType:'html',cache:false, success:function(id){ alert(id);}});
}
function prerun_conection()
{
	window.open('import_file_testa.php?Supplier_ID=<?=$Supplier_ID?>','_new');
}
function run_conection()
{
	$.ajax({type:'GET',url:"importa/corre.php?Supplier_ID=<?=$Supplier_ID?>&d=<?=uniqid('')?>",dataType:'html',cache:false, success:function(id){ alert("iniciada a importacao");}});
}
</script>
<table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
	<td valign="top" align="left"><?php require_once('../../layout/menus.inc.php');?>
		<div class="panel panel-inverse overflow-hidden m-b-12">
			<div class="panel-heading">
				<h4 style="color:#fff">Importador de produtos - <?php

				$i=0;
				$t=count($GLOBALS['lista_importadores']);
				while($i<$t)
				{
					if($GLOBALS['lista_importadores'][$i]['id'] == $Supplier_ID) echo $GLOBALS['lista_importadores'][$i]['name'];
					$i++;
				}

				?></h4>

				<div style="padding:5px">CPU: <?php $load = sys_getloadavg(); echo $load[0] . "%"; ?></div>

				<div id="carregar" style="display:none;float:right;margin-top:-30px"><input class="btn btn-primary" type="button" value="Correr Importação" onclick="callfile();return false;" /></div><br>

				<div id="carregar2" style="display:none;background:#dde7d9;color:#000"></div>
				<div id="carregar3" style="display:none;background:#dde7d9;color:#000"></div>

			</div>
		</div><?php

		if($Supplier_ID!=2000)
		{?>

			<form name="form1" method="post" action="grava_trata.php?Supplier_ID=<?=$Supplier_ID?>" enctype="multipart/form-data">
				<input type="hidden" name="tab_sel" id="tab_sel" value="<?php echo (isset($_GET['tab_sel']) ? $_GET['tab_sel'] :'tab-1');?>">
				<table width="100%" border=0 cellspacing=0 cellpadding=0 class="p1">
				<tr><td colspan="2" height="5" align="left">&nbsp;&nbsp;</td></tr>
				<tr>
					<td colspan="2" style="padding:0 10px" align="left">
						<div style="padding:0;margin:0">
							<ul class="tabs">
								<li class="tab-link current" id="tab-1-t" data-tab="tab-1">CONFIGURAÇÕES GERAIS</li>
								<?php if($Supplier_ID!=2) {?>
								<li class="tab-link" id="tab-2-t" data-tab="tab-2">CAMPOS CSV</li>
								<li class="tab-link" id="tab-3-t" data-tab="tab-3">CATEGORIAS/FAMILIAS</li>
								<li class="tab-link" id="tab-4-t" data-tab="tab-4">MARCAS</li>
								<li class="tab-link" id="tab-5-t" data-tab="tab-5">TIPO DE IMPORTAÇÃO</li><? } ?>
								<li class="tab-link" id="tab-6-t" data-tab="tab-6">LOGS</li>
								<?php /*if($tipo_importacao!=4){?><li class="tab-link" id="tab-7-t" data-tab="tab-7">CRONS</li><?}*/ ?>
								<li class="tab-link" id="tab-8-t" data-tab="tab-8">INFO</li>
							</ul>


							<div id="tab-1" class="tab-content <?php if($_GET['info'] !=1) echo 'current'?>">
								<table width="100%" border="1" cellpadding=5 cellspacing=0>
									<tr>
										<td style="vertical-align:top">
									        <table width="100%" border="0" cellpadding="1" cellspacing="0">
										        <tr>
													<td colspan=5 align=left>
														<span style="line-height:25px; padding-left: 10px">
															<b style="color:#ff0c00">Escolha o campo CHAVE para ligar os produtos quem vem na importação com os produtos do site</b>
														</span>
													</td>
												</tr>
										        <tr>
													<td width="25" height="25" valign="middle">
														<input type="radio" value="0" name="key_field" id="key_field2" <?php echo ($key_field == 0 ? 'checked="checked"' :'');?>>
													</td>
													<td width="80" align=left>
														<span style="line-height:25px">
															<b style="color:#ff0c00">REFERÊNCIA</b>
														</span>
													</td>
													<td width="25" height="25" valign="middle">
														<input type="radio" value="1" name="key_field" id="key_field2" <?php echo ($key_field == 1 ? 'checked="checked"' :'');?>>
													</td>
													<td width="80" align=left>
														<span style="line-height:25px">
															<b style="color:#ff0c00">EAN</b>
														</span>
													</td>
													<td>&nbsp;</td>
												</tr>
									        </table>
									    </td>
								   </tr>
								</table>
								<br><br>
								<div style="display:inline-block;padding:5px;vertical-align:top;border:#FFF 1px solid;background:#FFF;min-height:118px">
									<h2>Separadores Colunas (CSV)</h2><br>
									<div style="width:145px;display:inline-block;padding:5px 0">Colunas separadas com</div><div style="width:40px;display:inline-block;padding:5px 0"><input class="form-control" type="text" value="<?php echo $Separador;?>" name="Separador" id="Separador" style="width:30px"> </div><br>
									<div style="clear:both"></div>
									<div style="width:145px;display:inline-block;padding:5px 0">Colunas fechadas com</div><div style="width:40px;display:inline-block;padding:5px 0"><input class="form-control" type="text" value="<?php echo $Separador2;?>" name="Separador2" id="Separador2" style="width:30px"> </div><br>
									<div style="clear:both"></div>
									<div style="width:145px;display:inline-block;padding:5px 0">Separador casas decimais</div><div style="width:40px;display:inline-block;padding:5px 0"><input class="form-control" type="text" value="<?php echo $Separador_decimais;?>" name="Separador_decimais" id="Separador_decimais" style="width:30px"> </div><br>
								</div>
								<div style="display:inline-block;padding:5px;vertical-align:top;border-left:#c0c0c0 1px solid;background:#FFF;min-height:118px">
									<h2>Ações Produtos</h2><br>
									<label for="conf1_1"><input type="radio" value="1" name="conf1" id="conf1_1" <?php echo ($conf1 == 1 ? 'checked="checked"' :'');?>> Apenas Inserir novos</label><br>
									<label for="conf1_2"><input type="radio" value="2" name="conf1" id="conf1_2" <?php echo ($conf1 == 2 ? 'checked="checked"' :'');?>> Atualizar TUDO  (novos nao faz nada)</label><br>
									<label for="conf1_4"><input type="radio" value="4" name="conf1" id="conf1_4" <?php echo ($conf1 == 4 ? 'checked="checked"' :'');?>> Atualizar apenas PVP e Stock (novos nao faz nada)</label><br>
									<label for="conf1_5"><input type="radio" value="5" name="conf1" id="conf1_5" <?php echo ($conf1 == 5 ? 'checked="checked"' :'');?>> Inserir novos e Atualizar apenas imagens</label><br>
									<label for="conf1_0"><input type="radio" value="0" name="conf1" id="conf1_0" <?php echo ($conf1 == 0 ? 'checked="checked"' :'');?>> Inserir ou Atualizar TUDO</label><br>
									<label for="conf1_3"><input type="radio" value="3" name="conf1" id="conf1_3" <?php echo ($conf1 == 3 ? 'checked="checked"' :'');?>> Eliminar</label>
								</div>
								<div style="display:inline-block;padding:5px;vertical-align:top;border-left:#c0c0c0 1px solid;background:#FFF;min-height:118px">
									<h2>Estado Produtos Novos</h2><br>
									<label for="conf4_0"><input type="radio" value="0" name="conf4" id="conf4_0" <?php echo ($conf4 == 0 ? 'checked="checked"' :'');?>> Produto Activo/Visível</label><br>
									<label for="conf4_1"><input type="radio" value="1" name="conf4" id="conf4_1" <?php echo ($conf4 == 1 ? 'checked="checked"' :'');?>> Produto Desactivado/Não Visível</label>
								</div>
								<div style="display:inline-block;padding:5px;vertical-align:top;border-left:#c0c0c0 1px solid;background:#FFF;min-height:118px;display:none">
									<h2>Ações Categorias</h2><br>
									<label for="conf2_1"><input type="radio" value="0" name="conf2" id="conf2_1" <?php echo ($conf2 == 0 ? 'checked="checked"' :'');?>> Desativado</label><br>
									<label for="conf2_2"><input type="radio" value="1" name="conf2" id="conf2_2" <?php echo ($conf2 == 1 ? 'checked="checked"' :'');?>> Criar/Atualizar</label><br>
									<label for="conf2_3"><input type="radio" value="2" name="conf2" id="conf2_3" <?php echo ($conf2 == 2 ? 'checked="checked"' :'');?>> Apenas Criar</label><br>
									<label for="conf2_4"><input type="radio" value="3" name="conf2" id="conf2_4" <?php echo ($conf2 == 3 ? 'checked="checked"' :'');?>> Apenas Atualizar</label>

								</div>
								<div style="display:inline-block;padding:5px;vertical-align:top;border-left:#c0c0c0 1px solid;background:#FFF;min-height:118px;display:none">

									<h2>Ações Marcas</h2><br>
									<label for="conf3_1"><input type="radio" value="0" name="conf3" id="conf3_1" <?php echo ($conf3 == 0 ? 'checked="checked"' :'');?>> Desativado</label><br>
									<label for="conf3_2"><input type="radio" value="1" name="conf3" id="conf3_2" <?php echo ($conf3 == 1 ? 'checked="checked"' :'');?>> Criar/Atualizar Marca</label><br>
									<label for="conf3_3"><input type="radio" value="2" name="conf3" id="conf3_3" <?php echo ($conf3 == 2 ? 'checked="checked"' :'');?>> Apenas Criar</label><br>
									<label for="conf3_4"><input type="radio" value="3" name="conf3" id="conf3_4" <?php echo ($conf3 == 3 ? 'checked="checked"' :'');?>> Apenas Atualizar</label>

								</div>

								<div style="display:inline-block;padding:5px;vertical-align:top;border-left:#c0c0c0 1px solid;background:#FFF;min-height:118px">
									<h2>Preços Importados</h2><br>
									<label for="Preco_conf"><input type="radio" value="0" name="Preco_conf" id="Preco_conf" <?php echo ($Preco_conf == 0 ? 'checked="checked"' :'');?>> Preços já vem com IVA incluído</label>
									<div style="width:100%;display:inline-block;border:#c0c0c0 0px solid">
										<label for="Preco_conf2"><input type="radio" value="1" name="Preco_conf" id="Preco_conf2" <?php echo ($Preco_conf == 1 ? 'checked="checked"' :'');?>> Preços vem sem IVA</label>
										<div style="width:100px;display:inline-block;padding:5px">Acrescentar IVA</div><div style="width:40px;display:inline-block;padding:5px 0"><input class="form-control" type="text" value="<?php echo $Preco_iva_valor;?>" name="Preco_iva_valor" id="Preco_iva_valor" style="width:80px"> </div><br>
									</div>

									<div style="width:100%;display:inline-block;border:#c0c0c0 0px solid">
										<div style="width:100px;display:inline-block;padding:5px">Aplicar margem</div><div style="width:40px;display:inline-block;padding:5px 0"><input class="form-control" type="text" value="<?php echo $Margem;?>" name="Margem" id="Margem" style="width:80px"> </div>%<br>
									</div>

								</div>
								<div style="display:inline-block;padding:5px;vertical-align:top;border-left:#c0c0c0 1px solid;background:#FFF;min-height:118px;width:630px">
									<h2>Imagens</h2><br>
									<label for="Imagem_conf"><input type="radio" value="0" name="Imagem_conf" id="Imagem_conf" <?php echo ($Imagem_conf == 0 ? 'checked="checked"' :'');?>> Download de imagens a partir de Url quem vem no CSV ( HTTP ) </label>
									<div style="width:100%;display:inline-block;padding:5px 0;border:#c0c0c0 0px solid">
										<label for="Imagem_conf21"><input type="radio" value="1" name="Imagem_conf" id="Imagem_conf21" <?php echo ($Imagem_conf == 1 ? 'checked="checked"' :'');?>> Importar a partir de pasta do Servidor</label>
										<div style="width:145px;display:inline-block;padding:5px 0">/files/importar/imagens/</div><div style="width:80px;display:inline-block;padding:5px 0"><input class="form-control" type="text" value="<?php echo $Imagem_pasta;?>" name="Imagem_pasta" id="Imagem_pasta" style="width:70px"> </div><br>
									</div>
									<label for="Imagem_conf22"><input type="radio" value="2" name="Imagem_conf" id="Imagem_conf22" <?php echo ($Imagem_conf == 2 ? 'checked="checked"' :'');?>> Não fazer nada </label>
								</div>
								<div style="display:inline-block;padding:5px;vertical-align:top;border-left:#c0c0c0 1px solid;background:#FFF;min-height:118px">
									<h2>Stock</h2>(Insira as tags correspondentes separadas por ; por exemplo:  1;2;3 )<br>
									<div style="width:135px;display:inline-block;padding:5px 0;color:#0000ff">Stock Azul</div><div style="width:80px;display:inline-block;padding:5px 0"><input class="form-control" type="text" value="<?php echo $Stock1;?>" name="Stock1" id="Stock1" style="width:70px"> </div>Por encomenda<br>
									<div style="width:135px;display:inline-block;padding:5px 0;color:#ff0000">Stock Vermelho</div><div style="width:80px;display:inline-block;padding:5px 0"><input class="form-control" type="text" value="<?php echo $Stock2;?>" name="Stock2" id="Stock2" style="width:70px"> </div>Esgotado<br>
									<div style="width:135px;display:inline-block;padding:5px 0">Stock Amarelo</div><div style="width:80px;display:inline-block;padding:5px 0"><input class="form-control" type="text" value="<?php echo $Stock3;?>" name="Stock3" id="Stock3" style="width:70px"> </div>Pouco stock<br>
									<div style="width:135px;display:inline-block;padding:5px 0;color:#00ff00">Stock Verde</div><div style="width:80px;display:inline-block;padding:5px 0"><input class="form-control" type="text" value="<?php echo $Stock4;?>" name="Stock4" id="Stock4" style="width:70px"> </div>Em stock<br>
								</div>
								<div style="display:inline-block;padding:5px;vertical-align:top;border-left:#c0c0c0 1px solid;background:#FFF;min-height:118px">
									<h2>Outras Ações</h2><br>
									<div style="width:110px;display:inline-block;padding:5px 0">Código Importação</div><div style="width:120px;display:inline-block;padding:5px 0"><input class="form-control" type="text" value="<?php echo $codigo_importacao;?>" name="codigo_importacao" id="codigo_importacao" style="width:120px"> </div><br>
									<label for="Eliminar_conf"><input type="radio" value="0" name="Eliminar_conf" id="Eliminar_conf" <?php echo ($Eliminar_conf == 0 ? 'checked="checked"' :'');?>> Desativado</label>
									<label for="Eliminar_conf1"><input type="radio" value="1" name="Eliminar_conf" id="Eliminar_conf1" <?php echo ($Eliminar_conf == 1 ? 'checked="checked"' :'');?>> Eliminar Produtos que não venham no ficheiro</label>
									<div style="width:100%;display:inline-block;padding:5px 0;border:#c0c0c0 1px solid">
									<label for="Eliminar_conf2"><input type="radio" value="2" name="Eliminar_conf" id="Eliminar_conf2" <?php echo ($Eliminar_conf == 2 ? 'checked="checked"' :'');?>> Eliminar Produtos que não venham no ficheiro e que estejam fora de gama</label>
									<div style="width:110px;display:inline-block;padding:5px 0">Com Valor</div><div style="width:120px;display:inline-block;padding:5px 0"><input class="form-control" type="text" value="<?php echo $fora_gama_codigo;?>" name="fora_gama_codigo" id="fora_gama_codigo" style="width:120px"> </div><br>
									</div>
									<div style="clear:both"></div>
								</div>
								<div style="padding:5px;vertical-align:top;border-left:#c0c0c0 1px solid;background:#FFF;min-height:80px">
									<h2>Os produtos devem assumir os preços desta importação se :</h2><br>
									<label for="preco_conf">
										<input type="radio" value="0" name="price_conf" id="price_conf" <?php echo ($price_conf == 0 ? 'checked="checked"' :'');?>>
										Tiver o Menor Preço
									</label>
									<label for="preco_conf1">
										<input type="radio" value="1" name="price_conf" id="price_conf2" <?php echo ($price_conf == 1 ? 'checked="checked"' :'');?>>
										Se tiver Mais Stock
									</label>
								</div>
							</div><?php

								if($Supplier_ID!=2)
								{?>
							<div id="tab-2" class="tab-content">
								<?php include 'tabs/tab_configuracoes.php';?>
							</div>

							<div id="tab-3" class="tab-content">
								<?php include 'tabs/tab_familias.php';?>
							</div>

							<div id="tab-4" class="tab-content">
								<?php include 'tabs/tab_marcas.php';?>
							</div>
							<div id="tab-5" class="tab-content">
								<?php include 'tabs/tab_buscar_ficheiro.php';?>
							</div><?php
							}?>
							<div id="tab-6" class="tab-content <?php if($Supplier_ID==2) echo 'current'?>">
								<?php include 'tabs/tab_logs.php';?>
							</div>
							<!-- <div id="tab-7" class="tab-content"> -->
								<?php //include 'tabs/tab_cron.php';?>
							<!-- </div> -->
							<div id="tab-8" class="tab-content">
								<?php include 'tabs/tab_info.php';?>
							</div>
						</div>
					<br><center><input type="submit" class="btn btn-primary" value=" Gravar Configurações" /></center><br>
					</td>
				</tr>
				</table>
			</form>
			<br><?php

			if($Supplier_ID!=2){

				if(($tipo_importacao==0 || $tipo_importacao==1) && $Separador =='' ) $err[] ='Na Tab <b>Configurações</b>, O campo <b>Separador</b> - "<b>Colunas separadas com</b>", normalmente este campo é configurado com ponto e vírgula (;)';
				foreach($GLOBALS['arr_campos_importar'] as $campos)
				{
					if($campos['OBRIGATORIO'] == 1 && ${$campos['CAMPO']} == '') $err[] ='Na Tab <b>Configurações de Colunas</b> O campo <b>'.($campos['INFO'] != '' ? $campos['INFO'] :$campos['CAMPO']).'</b> não foi preenchido';
				}
			}
		}

		if(isset($err) && count($err) > 0)
		{
			?>
			<table width="100%" height="50px" border=0 cellspacing=0 cellpadding=0 class="p1">
				<tr>
					<td  align="center" style="padding:10px;text-align:center;font-size:18px;color:#FF0000">Erros de Configuração</td>
				</tr>
				<tr>
					<td align="left" valign="top" style="padding:10px;font-size:16px;">
					<?php
					for($a=0;$a<count($err);$a++)
					{
						echo ($a+1).' - '.$err[$a].'<br>';
					}
					?>
					</td>
				</tr>
			</table>
			<?php
		}
		else
		{
			if($Supplier_ID!=2){
		?>
		<form name="form1" method="post" action="import_trata.php?Supplier_ID=<?=$Supplier_ID?>" enctype="multipart/form-data">
			<table width="100%" height="50px" border=0 cellspacing=0 cellpadding=0 class="p1">
			<tr>
				<td align="left" style="padding:10px;text-align:left;font-weight:bold">ENVIAR FICHEIRO <b>.CSV</b> MANUAL</td>
			</tr>
			<tr>
				<td align="left" style="padding:0 10px">
					<table border="0" cellpadding="0" cellspacing="0"  style="margin:0 auto">
					<tr>
						<td align="left" valign="middle" style="padding:10px">CSV</td>
						<td align="left" valign="top" style="padding:10px"><span id="tt2" style="display:inline-flex"><input type="file" name="import_file">&nbsp;<input type="submit" class="btn btn-primary" value="Submeter e Importar..." /></span></td>
					</tr>
					</table>
				</td>
			</tr>
			</table>
		</form><? }?>
		<br>

		<script>
			$( document).ready(function()
			{
				//loadsexport();
				var tt = setInterval(function(){loadsexport();},5000);
			});
			function loadsexport()
			{
				$.ajax({
					type:'GET',
					url:"importa/verifica.php?Supplier_ID=<?=$Supplier_ID?>",
					dataType:'html',
					cache:false,
					success:function(id2)
					{
						idx = id2.split('[split]');
						id = idx[0];

						if(id == '1')
						{
							document.getElementById('carregar').style.display="none";
							document.getElementById('carregar2').style.display="block";
							document.getElementById('carregar2').innerHTML = idx[1];
							document.getElementById('carregar3').style.display="none";
							document.getElementById('carregar3').innerHTML = '';
						}
						else if(id==0)
						{
							document.getElementById('carregar').style.display="block";
							document.getElementById('carregar2').style.display="none";
							document.getElementById('carregar2').innerHTML = '';
							document.getElementById('carregar3').style.display="block";
							document.getElementById('carregar3').innerHTML = idx[1];
						}
					}
				});
			}
			function callfile()
			{
				$.ajax({type:'GET',url:"importa/corre.php?Supplier_ID=<?=$Supplier_ID?>&d=<?=uniqid('')?>",dataType:'html',cache:false, success:function(id){ alert("iniciada a importacao");}});
			}
			function remove2()
			{
				if(confirm('Tem a certeza que quer cancelar a importação, os produtos que já foram atualizados não serão repostos?'))
				{
					$.ajax({type:'GET',url:"importa/remove.php?Supplier_ID=<?=$Supplier_ID?>&d=<?=uniqid('')?>",dataType:'html',cache:false, success:function(id){}});
				}
			}
		</script>

		<img src="../../images/bsh.png" width="100%" height="30px"><?php
		}?>
	</td>
</tr>
</table><?php require_once('../../layout/bottom.inc.php');