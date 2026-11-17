<?php
    require_once('settings.inc.php');
    require_once('../layout/header.inc.php');
    require_once('../layout/body.inc.php');

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(0);

	function limpa_special_import($s)
	{
		$s=strip_tags($s);
		$s=str_replace('<br>','',$s);
		$s=str_replace('<br/>','',$s);
		$s=str_replace('<br />','',$s);
		$s=trim($s,"\t\n\r\0\x0B");
		$v=array("(",")","³","°","²","½","%","«","»","?","ª","º","®",'"','”','*','’','‘',"!","#","=");
		$s=str_replace($v,'',$s);
		$v=array(" ","'","/","&",":",";","`","´","~","^","¨","_",",",".","|","\\","<",">","—","€","±","×","¿");
		$s=str_replace($v,'-',$s);
		$s=str_replace('-----','-',$s);
		$s=str_replace('----','-',$s);
		$s=str_replace('---','-',$s);
		$s=str_replace('--','-',$s);
		$v=array("á","à","ã","â","ä"); 	$s=str_replace($v,'a',$s);
		$v=array("é","è","ê","ë"); 		$s=str_replace($v,'e',$s);
		$v=array("í","ì","î","ï"); 		$s=str_replace($v,'i',$s);
		$v=array("ó","ò","õ","ô","ö"); 	$s=str_replace($v,'o',$s);
		$v=array("ú","ù","û","ü"); 		$s=str_replace($v,'u',$s);
		$v=array("ñ"); 					$s=str_replace($v,'n',$s);
		$v=array("ç"); 					$s=str_replace($v,'c',$s);
		$v=array("Á","À","Ã","Â","Ä"); 	$s=str_replace($v,'A',$s);
		$v=array("É","È","Ê","Ë"); 		$s=str_replace($v,'E',$s);
		$v=array("Í","Ì","Î","Ï"); 		$s=str_replace($v,'I',$s);
		$v=array("Ó","Ò","Õ","Ô","Ö"); 	$s=str_replace($v,'O',$s);
		$v=array("Ú","Ù","Û","Ü"); 		$s=str_replace($v,'U',$s);
		$v=array("Ñ"); 		$s=str_replace($v,'N',$s);
		$v=array("Ç"); 		$s=str_replace($v,'C',$s);
		return $s;
	}

	// VAMOS ATUALIZAR TODAS AS FAMILIAS COM UM CÓDIGO ESPECIAL
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

	// $field_array = array();
	// $fields=db_list_fields($DB_name,'products',$db_link);
	// $columns=db_num_fields($fields);
	// for ($i=0;$i < $columns;$i++){$field_array[]=db_field_name($fields,$i);}
	// if(!in_array('codigo_importacao', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD codigo_importacao varchar(255) DEFAULT ""');}

	$field_array = array();
	$fields=db_list_fields($DB_name,'products_families',$db_link);
	$columns=db_num_fields($fields);
	for ($i=0;$i < $columns;$i++){$field_array[]=db_field_name($fields,$i);}
	if(!in_array('codigo_importacao', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_families ADD codigo_importacao varchar(255) DEFAULT ""');}

	$field_array = array();
	$fields=db_list_fields($DB_name,'brands',$db_link);
	$columns=db_num_fields($fields);
	for ($i=0;$i < $columns;$i++){$field_array[]=db_field_name($fields,$i);}
	if(!in_array('codigo_importacao', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE brands ADD codigo_importacao varchar(255) DEFAULT ""');}



	$field_array = array();
	$fields=db_list_fields($DB_name,'a_importador_configuracoes',$db_link);
	$columns=db_num_fields($fields);
	for ($i=0;$i < $columns;$i++){$field_array[]=db_field_name($fields,$i);}
	if(!in_array('ftp_caminho', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE a_importador_configuracoes ADD ftp_caminho varchar(255) DEFAULT ""');}
	if(!in_array('ftp_servidor', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE a_importador_configuracoes ADD ftp_servidor varchar(255) DEFAULT ""');}
	if(!in_array('ftp_porta', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE a_importador_configuracoes ADD ftp_porta int(11) DEFAULT 0');}
	if(!in_array('ftp_user', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE a_importador_configuracoes ADD ftp_user varchar(255) DEFAULT ""');}
	if(!in_array('ftp_password', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE a_importador_configuracoes ADD ftp_password varchar(255) DEFAULT ""');}
	if(!in_array('ftp_passivo', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE a_importador_configuracoes ADD ftp_passivo tinyint(1) DEFAULT 0');}

	if(!in_array('fora_gama', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE a_importador_configuracoes ADD fora_gama varchar(255) DEFAULT ""');}
	if(!in_array('fora_gama_codigo', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE a_importador_configuracoes ADD fora_gama_codigo varchar(255) DEFAULT ""');}


	$obj = new DataObj;

	$conf1 = 0;
	$sql2 = '
			SELECT
				*
			FROM
				a_importador_configuracoes
			';
			// echo $sql2;
	$obj->db_open($db_link,$DB_name,$sql2);
	$upd2 = '';
	if(!$obj->EOF())
	{
		while(!$obj->EOF())
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
			$tipo_importacao = $obj->db_data['tipo_importacao'];
			$ficheiro_ftp = $obj->db_data['ficheiro_ftp'];

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

			$obj->db_next();
		}
	}
	$obj->db_close();


	if(!isset($codigo_importacao) || (isset($codigo_importacao) && $codigo_importacao == '')) $codigo_importacao = 'IMPNOVA';

?>
<style>
.botao{ padding:0 5px}
.container{ width: 100%; margin: 0 auto; }
.container label{ display:block;padding:5px 0 }
ul.tabs{ margin: 0px; padding: 0px; list-style: none; }
ul.tabs li{ background: none; color: #222; display: inline-block; padding: 10px 15px; cursor: pointer; font-weight:bold}
ul.tabs li.current{ background: #FFF; color: #222; }
.tab-content{ display: none; background: #FFF; padding: 15px; font-size:12px }
.tab-content.current{ display: inherit; }
</style>
<script>
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
	{
		?>
		var tab_id = '<?php echo $_GET['tab_sel'];?>';

		$('#tab_sel').val(tab_id);

		$('ul.tabs li').removeClass('current');
		$('.tab-content').removeClass('current');

		$("#"+tab_id+"-t").addClass('current');
		$("#"+tab_id).addClass('current');
		<?php
	}
	?>

	$('.fancybox_url').live('click', function(){ var href = $(this).attr('href'); $.fancybox({ 'showCloseButton' : false, 'titlePosition' : 'inside', title : true, autoSize : false, modal : false, fitToView : false, topRatio : 0, content : '<iframe width="100%" height="100%" src="'+href+'"></iframe>', width : 754, maxHeight : 600, scrolling : 'no', type : 'html', onComplete: function() {$("#fancybox-wrap").stop().animate({'top':'10px',}, 0); $("#fancybox-wrap").css({'z-index':'9999999999999999',}, 0); } }); return false;});

});
function test_conection()
{
	$.ajax({type: 'GET',url: "testa_conexao.php",dataType: 'html',cache: false, success: function(id){ alert(id); }});
}
</script>
<table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td valign="top" align="left"><?php require_once('../layout/menus.inc.php'); ?>
            <table class="p1" width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr><td style="color:#FFF" align="center"><h3>Importador de produtos</h3></td></tr>
            </table>
				<form name="form1" method="post" action="grava_trata.php" enctype="multipart/form-data">
				<input type="hidden" name="tab_sel" id="tab_sel" value="<?php echo (isset($_GET['tab_sel']) ? $_GET['tab_sel'] : 'tab-1');?>">
					<table width="100%" border="0" cellpadding="0" cellspacing="0"  style="background-color:#DDD">
					<tr><td style="background-color:#DDD" colspan="2" height="25" align="left">&nbsp;&nbsp;<b>CONFIGURAR CAMPOS DO FICHEIRO</b></td></tr>
					<tr>
					<td colspan="2" style="padding:0 10px;background-color:#DDD" align="left">
						<div class="container">
							<ul class="tabs">
								<li class="tab-link current" id="tab-1-t" data-tab="tab-1">Configurações</li>
								<li class="tab-link" id="tab-2-t" data-tab="tab-2">Configuração de Colunas</li>
								<li class="tab-link" id="tab-3-t" data-tab="tab-3">Configuração de Categorias</li>
								<li class="tab-link" id="tab-4-t" data-tab="tab-4">Configuração de Marcas</li>
								<li class="tab-link" id="tab-5-t" data-tab="tab-5">Configuração do Ficheiro</li>
								<li class="tab-link" id="tab-6-t" data-tab="tab-6">Logs Importação</li>
							</ul>

							<div id="tab-1" class="tab-content current">
								<div style="display:inline-block;padding:5px;vertical-align:top;border:#FFF 1px solid;background:#FFF;min-height:118px">
									<h2>Separadores Colunas</h2><br>
									<div style="width:145px;display:inline-block;padding:5px 0">Colunas separadas com</div><div style="width:40px;display:inline-block;padding:5px 0"><input type="text" value="<?php echo $Separador;?>" name="Separador" id="Separador" style="width:30px"> </div><br>
									<div style="clear:both"></div>
									<div style="width:145px;display:inline-block;padding:5px 0">Colunas fechadas com</div><div style="width:40px;display:inline-block;padding:5px 0"><input type="text" value="<?php echo $Separador2;?>" name="Separador2" id="Separador2" style="width:30px"> </div><br>
								</div>
								<div style="display:inline-block;padding:5px;vertical-align:top;border-left:#c0c0c0 1px solid;background:#FFF;min-height:118px">
									<h2>Ações Produtos</h2><br>
									<label for="conf1_0"><input type="radio" value="0" name="conf1" id="conf1_0" <?php echo ($conf1 == 0 ? 'checked="checked"' : '');?>> Inserir/Atualizar</label>
									<label for="conf1_1"><input type="radio" value="1" name="conf1" id="conf1_1" <?php echo ($conf1 == 1 ? 'checked="checked"' : '');?>> Apenas Inserir</label>
									<label for="conf1_2"><input type="radio" value="2" name="conf1" id="conf1_2" <?php echo ($conf1 == 2 ? 'checked="checked"' : '');?>> Apenas Atualizar</label>
									<label for="conf1_3"><input type="radio" value="3" name="conf1" id="conf1_3" <?php echo ($conf1 == 3 ? 'checked="checked"' : '');?>> Eliminar</label>
								</div>
								<div style="display:inline-block;padding:5px;vertical-align:top;border-left:#c0c0c0 1px solid;background:#FFF;min-height:118px">
									<h2>Ações Categorias</h2><br>
									<label for="conf2_1"><input type="radio" value="0" name="conf2" id="conf2_1" <?php echo ($conf2 == 0 ? 'checked="checked"' : '');?>> Desativado</label>
									<label for="conf2_2"><input type="radio" value="1" name="conf2" id="conf2_2" <?php echo ($conf2 == 1 ? 'checked="checked"' : '');?>> Criar/Atualizar</label>
									<label for="conf2_3"><input type="radio" value="2" name="conf2" id="conf2_3" <?php echo ($conf2 == 2 ? 'checked="checked"' : '');?>> Apenas Criar</label>
									<label for="conf2_4"><input type="radio" value="3" name="conf2" id="conf2_4" <?php echo ($conf2 == 3 ? 'checked="checked"' : '');?>> Apenas Atualizar</label>

								</div>
								<div style="display:inline-block;padding:5px;vertical-align:top;border-left:#c0c0c0 1px solid;background:#FFF;min-height:118px">

									<h2>Ações Marcas</h2><br>
									<label for="conf3_1"><input type="radio" value="0" name="conf3" id="conf3_1" <?php echo ($conf3 == 0 ? 'checked="checked"' : '');?>> Desativado</label>
									<label for="conf3_2"><input type="radio" value="1" name="conf3" id="conf3_2" <?php echo ($conf3 == 1 ? 'checked="checked"' : '');?>> Criar/Atualizar Marca</label>
									<label for="conf3_3"><input type="radio" value="2" name="conf3" id="conf3_3" <?php echo ($conf3 == 2 ? 'checked="checked"' : '');?>> Apenas Criar</label>
									<label for="conf3_4"><input type="radio" value="3" name="conf3" id="conf3_4" <?php echo ($conf3 == 3 ? 'checked="checked"' : '');?>> Apenas Atualizar</label>

								</div>

								<div style="display:inline-block;padding:5px;vertical-align:top;border-left:#c0c0c0 1px solid;background:#FFF;min-height:118px">
									<h2>Preços</h2><br>
									<label for="Preco_conf"><input type="radio" value="0" name="Preco_conf" id="Preco_conf" <?php echo ($Preco_conf == 0 ? 'checked="checked"' : '');?>> Preços com IVA INC.</label>
									<div style="width:100%;display:inline-block;padding:5px 0;border:#c0c0c0 1px solid">
										<label for="Preco_conf2"><input type="radio" value="1" name="Preco_conf" id="Preco_conf2" <?php echo ($Preco_conf == 1 ? 'checked="checked"' : '');?>> Preços sem IVA INC.</label>
										<div style="width:96px;display:inline-block;padding:5px 0">Acrescentar IVA</div><div style="width:40px;display:inline-block;padding:5px 0"><input type="text" value="<?php echo $Preco_iva_valor;?>" name="Preco_iva_valor" id="Preco_iva_valor" style="width:30px"> </div><br>
									</div>
								</div>
								<div style="display:inline-block;padding:5px;vertical-align:top;border-left:#c0c0c0 1px solid;background:#FFF;min-height:118px">
									<h2>Imagens</h2><br>
									<label for="Imagem_conf"><input type="radio" value="0" name="Imagem_conf" id="Imagem_conf" <?php echo ($Imagem_conf == 0 ? 'checked="checked"' : '');?>> Download Imagens de Url</label>
									<div style="width:100%;display:inline-block;padding:5px 0;border:#c0c0c0 1px solid">
										<label for="Imagem_conf21"><input type="radio" value="1" name="Imagem_conf" id="Imagem_conf21" <?php echo ($Imagem_conf == 1 ? 'checked="checked"' : '');?>> Download Imagens Pasta Servidor</label>
										<div style="width:135px;display:inline-block;padding:5px 0">/files/importar/imagens/</div><div style="width:80px;display:inline-block;padding:5px 0"><input type="text" value="<?php echo $Imagem_pasta;?>" name="Imagem_pasta" id="Imagem_pasta" style="width:70px"> </div><br>
									</div>
								</div>
								<div style="display:inline-block;padding:5px;vertical-align:top;background:#FFF;min-height:118px">
									<h2>Stock</h2><br>
									<div style="width:135px;display:inline-block;padding:5px 0">Stock Azul</div><div style="width:80px;display:inline-block;padding:5px 0"><input type="text" value="<?php echo $Stock1;?>" name="Stock1" id="Stock1" style="width:70px"> </div><br>
									<div style="width:135px;display:inline-block;padding:5px 0">Stock Vermelho</div><div style="width:80px;display:inline-block;padding:5px 0"><input type="text" value="<?php echo $Stock2;?>" name="Stock2" id="Stock2" style="width:70px"> </div><br>
									<div style="width:135px;display:inline-block;padding:5px 0">Stock Amarelo</div><div style="width:80px;display:inline-block;padding:5px 0"><input type="text" value="<?php echo $Stock3;?>" name="Stock3" id="Stock3" style="width:70px"> </div><br>
									<div style="width:135px;display:inline-block;padding:5px 0">Stock Verde</div><div style="width:80px;display:inline-block;padding:5px 0"><input type="text" value="<?php echo $Stock4;?>" name="Stock4" id="Stock4" style="width:70px"> </div><br>
								</div>
								<div style="display:inline-block;padding:5px;vertical-align:top;border-left:#c0c0c0 1px solid;background:#FFF;min-height:118px">
									<h2>Ações Ficheiro</h2><br>
									<div style="width:110px;display:inline-block;padding:5px 0">Código Importação</div><div style="width:120px;display:inline-block;padding:5px 0"><input type="text" value="<?php echo $codigo_importacao;?>" name="codigo_importacao" id="codigo_importacao" style="width:120px"> </div><br>
									<label for="Eliminar_conf"><input type="radio" value="0" name="Eliminar_conf" id="Eliminar_conf" <?php echo ($Eliminar_conf == 0 ? 'checked="checked"' : '');?>> Desativado</label>
									<label for="Eliminar_conf1"><input type="radio" value="1" name="Eliminar_conf" id="Eliminar_conf1" <?php echo ($Eliminar_conf == 1 ? 'checked="checked"' : '');?>> Eliminar Produtos que não vinham no ficheiro</label>
									<div style="width:100%;display:inline-block;padding:5px 0;border:#c0c0c0 1px solid">
									<label for="Eliminar_conf2"><input type="radio" value="2" name="Eliminar_conf" id="Eliminar_conf2" <?php echo ($Eliminar_conf == 2 ? 'checked="checked"' : '');?>> Eliminar Produtos que não vinham no ficheiro e que estejam fora de gama</label>
									<div style="width:110px;display:inline-block;padding:5px 0">Com Valor</div><div style="width:120px;display:inline-block;padding:5px 0"><input type="text" value="<?php echo $fora_gama_codigo;?>" name="fora_gama_codigo" id="fora_gama_codigo" style="width:120px"> </div><br>
									</div>
								</div>
							</div>

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
							</div>
							<div id="tab-6" class="tab-content">
								<?php include 'tabs/tab_logs.php';?>
							</div>
						</div>
					<br>

						<center><input type="submit" class="botao" value=" Gravar Configurações" /></center>
					<br>
					</td>
					</tr>
					</table>
				</form>
				<br>


				<?php


				if($Separador =='' ) $err[] ='Na Tab <b>Configurações</b>, O campo <b>Separador</b> - "<b>Colunas separadas com</b>", normalmente este campo é configurado com ponto e vírgula (;)';
				foreach($GLOBALS['arr_campos_importar'] as $campos)
				{
					if($campos['OBRIGATORIO'] == 1 && ${$campos['CAMPO']} == '') $err[] ='Na Tab <b>Configurações de Colunas</b> O campo <b>'.($campos['INFO'] != '' ? $campos['INFO'] : $campos['CAMPO']).'</b> não foi preenchido';
				}

				if(count($err) > 0)
				{
					?>
					<table width="100%" border="0" cellpadding="0" cellspacing="0"  style="background-color:#DDD">
						<tr>
							<td  align="center" style="padding:10px;text-align:center;font-size:18px;color:#FF0000">Erros de Configuração</td>
						</tr>
						<tr>
							<td align="left" valign="top" style="padding:10px;font-size:16px;">
							<?php
							for($a=0; $a<count($err);$a++)
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
				?>
				<form name="form1" method="post" action="import_trata.php" enctype="multipart/form-data">
					<table width="100%" border="0" cellpadding="0" cellspacing="0"  style="background-color:#DDD">
						<tr>
							<td  align="left" style="padding:10px;text-align:left;font-weight:bold">ENVIAR FICHEIRO <b>.CSV</b> MANUAL</td>
						</tr>
						<tr>
							<td align="left" style="padding:0 10px">
								<table border="0" cellpadding="0" cellspacing="0"  style="margin:0 auto">
								<tr>
								<td align="left" valign="middle" style="padding:10px">CSV</td>
								<td align="left" valign="top" style="padding:10px"><span id="tt2"><input type="file" name="import_file">&nbsp;<input type="submit" class="botao" value="Submeter e Importar..." /></span></td>
								</tr>
								</table>
							</td>
						</tr>
					</table>
				</form>
				<br>
				<table width="100%" border="0" cellpadding="0" cellspacing="0"  style="background-color:#DDD">
				<tr>
					<td  align="left" style="padding:10px;text-align:left;font-weight:bold">PROCESSOS A DECORRER</td>
				</tr>
				<tr>
					<td align="center" valign="top" style="padding:10px" colspan="2">

						<div id="carregar" style="display:none"><input class="botao" type="button" style="width:160px;height:20px" value="Forçar Importação" onclick="callfile();return false;"  /></div><br>
						<div id="carregar2" style="display:none;background:#FFF"></div>
						<div id="carregar3" style="display:none;background:#FFF"></div>

						<script>
							$( document).ready(function()
							{
								loadsexport();
								var tt = setInterval(function(){loadsexport();}, 3000);
							});
							function loadsexport()
							{
								$.ajax({
									type: 'GET',
									url: "importa/verifica.php",
									dataType: 'html',
									cache: false,
									success: function(id2)
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
								$.ajax({type: 'GET',url: "importa/corre.php",dataType: 'html',cache: false, success: function(id){}});
							}
							function remove2()
							{
								if(confirm('Tem a certeza que quer cancelar a importação, os produtos que já foram atualizados não serão repostos?'))
								{
									$.ajax({type: 'GET',url: "importa/remove.php",dataType: 'html',cache: false, success: function(id){}});
								}
							}
							</script>
					</td>
				</tr>
			</table>

				<?php }?>
        </td>
    </tr>
</table><?php require_once('../layout/bottom.inc.php');