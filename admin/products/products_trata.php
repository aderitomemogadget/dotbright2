<?php
	$products_diag_log = dirname(dirname(__DIR__, 2)).'/private/php-error.log';
	register_shutdown_function(static function () use ($products_diag_log) {
		$error = error_get_last();
		if ($error && in_array($error['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR), true)) {
			@file_put_contents(
				$products_diag_log,
				'['.date('Y-m-d H:i:s').'] products_trata shutdown: '.json_encode($error, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).PHP_EOL,
				FILE_APPEND | LOCK_EX
			);
		}
	});
	set_exception_handler(static function (Throwable $exception) use ($products_diag_log) {
		@file_put_contents(
			$products_diag_log,
			'['.date('Y-m-d H:i:s').'] products_trata exception: '.$exception.PHP_EOL,
			FILE_APPEND | LOCK_EX
		);
		http_response_code(500);
		exit;
	});
	include "settings.inc.php";
	$private_error_log = dirname(dirname(__DIR__, 2)).'/private/php-error.log';
	if(is_dir(dirname($private_error_log)))
	{
		ini_set('log_errors', '1');
		ini_set('error_log', $private_error_log);
	}
	require_once 'filtros/funcoes.inc.php';
	if (defined('PROD_CONF')) $prod_conf=unserialize(PROD_CONF); else $prod_conf=array();

	$retorno='list.php';
	if(isset($_POST['slashes']))
	{
		$Img_peq=$_FILES['img_peq'];
		$Apagar_img=$_POST['Apagar_img'];
		$tem_foto=$_POST['tem_foto'];
		$img_path='../../files/products/';
		$Download_peq["name"]=$_FILES['download_peq']['name'];
		$Download_peq["temp"]=$_FILES['download_peq']['tmp_name'];
		$img_path_download="../../files/products_downl/";

		if($slashes=="'")
		{
			$cabecalho=addslashes($cabecalho);
			$texto1=addslashes($texto1);
			$texto2=addslashes($texto2);
			$texto3=addslashes($texto3);
			$texto4=addslashes($texto4);
			$cabecalho_texto5=addslashes($cabecalho_texto5);
			$texto5=addslashes($texto5);
			$cor=addslashes($cor);
			$strSearchWords=addslashes($strSearchWords);
			$ref=addslashes($ref);
			$cod_ean=addslashes($cod_ean);
			$marca=addslashes($marca);
			$stock_txt=addslashes($stock_txt);

			$bateria=addslashes($bateria);
			$estado=addslashes($estado);
			$localizacao_fisica=addslashes($localizacao_fisica);
		}

		//Validação de numeros inteiros
		$ID_ORG=intval($ID_ORG);
		$ordem=intval($ordem);
		$margem_activa=intval($margem_activa);
		$destaque=intval($destaque);
		$novidade=intval($novidade);
		$promocao=intval($promocao);
		$home=intval($home);
		$saldo=intval($saldo);
		$aproved=intval($aproved);
		$tipo=intval($tipo);
		$decumentario=intval($decumentario);
		$stock=intval($stock);
		$usado=intval($usado);
		$configurado=intval($configurado);
		$configurador=intval($configurador);
		$medidas=intval($medidas);
		$download=intval($download);
		$sync=intval($sync);

		//Validação de numeros float
		$tamanho=floatval($tamanho);
		$preco1=floatval($preco1);
		$tamanho=floatval($tamanho);
		$preco2=floatval($preco2);
		$precopromo1=floatval($precopromo1);
		$precopromo2=floatval($precopromo2);
		$margem=floatval($margem);
		$precocusto=floatval($precocusto);
		$preco_prec=floatval($preco_prec);
		$peso=floatval($peso);

		if(is_array($_POST['icn_chk'] ?? null))
		{
			$store_icons='';
			foreach ($_POST['icn_chk'] as $key => $value)
			{
				if($value==1)
					$store_icons.=$key.';';
			}
		}

        require_once __DIR__.'/../../core/repair_product_settings.php';
        $repair_settings_to_save = null;
        // Validate before modifying the product; ordinary products ignore these fields.
        if (in_array($task_form, array('insere', 'altera', 'duplicar'), true) && isset($_POST['repair_settings_present']) && dotbright_is_repair_family(isset($_POST['m_familia']) && is_scalar($_POST['m_familia']) ? $_POST['m_familia'] : 0)) {
            try {
                if (!dotbright_repair_settings_ready()) throw new InvalidArgumentException('Aplique primeiro a migração SQL da fase 2.');
                $repair_input = isset($_POST['repair_settings']) ? $_POST['repair_settings'] : array();
                if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc() && is_array($repair_input)) $repair_input = array_map('stripslashes', $repair_input);
                $repair_settings_to_save = dotbright_repair_settings_validate($repair_input);
            } catch (InvalidArgumentException $e) {
                exit('<p>'.htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8').'</p><p>Volte atrás para corrigir. A ficha não foi alterada.</p>');
            }
        }
        $save_repair_settings = function ($product_id) use ($repair_settings_to_save) {
            if ($repair_settings_to_save !== null && !dotbright_repair_settings_save($product_id, $_POST['m_familia'], $repair_settings_to_save)) {
                exit('<p>A ficha foi guardada, mas não foi possível guardar a configuração da reparação. Reabra a ficha e volte a guardar os dados no separador Reparação.</p>');
            }
        };

		if($task_form=="insere")
		{
			$retorno="products_families.php?ID=".$_POST['m_familia'];
			$result=InsertNewProd($_POST['m_familia'],$ordem,$cabecalho,$ref,$cod_ean,$tamanho,$cor,$texto1,$texto2,$texto3,$img_path,$Img_peq,$preco1,$preco2,$precopromo1,$precopromo2,$margem,$margem_activa,$destaque,$novidade,$promocao,$home,$saldo,$aproved,$tipo,$preco_prec,$strSearchWords,$marca,$peso,$usado,$stock_txt,$configurado,$configurador,$medidas,$precocusto,$stock,$download,$img_path_download,$Download_peq,$decumentario,$texto4,$cabecalho_texto5,$texto5,$sync,$preco_revenoff, $SEO_title, $SEO_description, $SEO_keywords, $SEO_alt, $SEO_h1,$tem_prod_destaque_homepage,$preco_custo_custom,$store_icons,$bateria,$estado,$localizacao_fisica);
			if(!is_array($result) || empty($result['res']) || empty($result['prod_id']))
			{
				show_box_ERROR(1,$retorno);
				exit;
			}
			$prod_id_new=(int)$result['prod_id'];
            $save_repair_settings($prod_id_new);
			if(isset($prod_conf) && $prod_conf['tem_filtros']==1)
			{
				if(isset($_POST['filtro']))
				{
					$filtros_arr=$_POST['filtro'];
					$valores_arr=$_POST['valor'];
					$opcoes_arr=$_POST['opcoes'];
					include 'products_filtros_save.php';
				}
				products_objs_filtros_save($prod_id_new);
			}
			if(isset($prod_conf) && $prod_conf['tem_acabamentos']==1) products_acabamentos_save($prod_id_new);
			$prod_id_on=$prod_id_new;

			//Atualiza a cache dos artigos
			get_produtc_info($prod_id_on,'',1,0,$admin_lang,1);

			if($result['res'])	show_box_OK(1,$retorno);
			else show_box_ERROR(1,$retorno);
		}
		elseif($task_form=="altera")
		{
			$retorno="products.php?ID=$ID&ID_ORG=".$_POST['m_familia'];
			$result=ChangeProd($ID,$_POST['m_familia'],$ordem,$cabecalho,$ref,$cod_ean,$tamanho,$cor,$texto1,$texto2,$texto3,$img_path,$Img_peq, $Apagar_img, $tem_foto,$preco1,$preco2,$precopromo1,$precopromo2,$margem,$margem_activa,$destaque,$novidade,$promocao,$home,$saldo,$aproved,$tipo,$preco_prec,$strSearchWords,$marca,$peso,$usado,$stock_txt,$configurado,$configurador,$medidas,$precocusto,$stock,$download,$img_path_download,$Download_peq,$Apagar_download,$tem_download,$decumentario,$texto4,$cabecalho_texto5,$texto5,$sync,$preco_revenoff, $SEO_title, $SEO_description, $SEO_keywords, $SEO_alt, $SEO_h1,$tem_prod_destaque_homepage,$preco_custo_custom,$store_icons,$bateria,$estado,$localizacao_fisica);
			if($result)
			{
                $save_repair_settings($ID);
				if(isset($prod_conf) && $prod_conf['tem_filtros']==1)
				{
					if(isset($_POST['filtro']))
					{
						$filtros_arr=$_POST['filtro'];
						$valores_arr=$_POST['valor'];
						$opcoes_arr=$_POST['opcoes'];
						include 'products_filtros_save.php';
					}
					products_objs_filtros_save($ID);
				}
				if(isset($prod_conf) && $prod_conf['tem_acabamentos']==1) products_acabamentos_save($ID);
				$prod_id_on=$ID;

				//Atualiza a cache dos artigos
				get_produtc_info($ID,'',1,0,$admin_lang,1);

				show_box_OK(2,$retorno);
			}
			else show_box_ERROR(2,$retorno);
		}
		elseif($task_form=="apaga")
		{
			$retorno="list.php";
			$result=DeleteProd($ID,$img_path);

			//Atualiza a cache dos artigos
			del_produtc_info($ID,'',1,0,$admin_lang,1);

			if($result)	show_box_OK(3,$retorno);
			else		show_box_ERROR(3,$retorno);

		}
		elseif($_GET['task']=="relacaooff" && $_GET['ID_ORG_REMOVE'] > 0 && $_GET['ID_REMOVE'] > 0)
		{
			global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
			$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
			$retorno="products_families.php?ID=".$_GET['ID_ORG_REMOVE'];
			$sql='DELETE FROM products_families_obj WHERE ID_ORG="'.$_GET['ID_ORG_REMOVE'].'" AND ID_PROD="'.$_GET['ID_REMOVE'].'"';
			$res=Runquery($db_link,$DB_name,$sql);
			if($res)	show_box_OK(3,$retorno);
			else		show_box_ERROR(3,$retorno);
		}
		elseif($task_form=="duplicar")
		{
			$retorno="products_families.php?ID=".$_POST['m_familia'];
			$result=InsertNewProd($_POST['m_familia'],$ordem,$cabecalho,$ref,$cod_ean,$tamanho,$cor,$texto1,$texto2,$texto3,$img_path,$Img_peq,$preco1,$preco2,$precopromo1,$precopromo2,$margem,$margem_activa,$destaque,$novidade,$promocao,$home,$saldo,$aproved,$tipo,$preco_prec,$strSearchWords,$marca,$peso,$usado,$stock_txt,$configurado,$configurador,$medidas,$precocusto,$stock,$download,$img_path_download,$Download_peq,$decumentario,$texto4,$cabecalho_texto5,$texto5,$sync,$preco_revenoff, $SEO_title, $SEO_description, $SEO_keywords, $SEO_alt, $SEO_h1,$store_icons,$bateria,$estado,$localizacao_fisica);
			if($result['res'])
			{
                $save_repair_settings((int)$result['prod_id']);
				show_box_OK(1,$retorno);

				if(isset($prod_conf) && $prod_conf['tem_filtros']==1)
				{
					if(isset($_POST['filtro']))
					{
						$filtros_arr=$_POST['filtro'];
						$valores_arr=$_POST['valor'];
						$opcoes_arr=$_POST['opcoes'];
						$prod_id_new=$result['prod_id'];						
						include 'products_filtros_save.php';
					}
				}
				$prod_id_on=$result['prod_id'];

				//Atualiza a cache dos artigos
				get_produtc_info($prod_id_on,'',1,0,$admin_lang,1);

			}
			else show_box_ERROR(1,$retorno);
		}
	}
	else show_box_ERROR(4, $retorno);
?>
