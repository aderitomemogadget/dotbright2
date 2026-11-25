<?php
	include "settings.inc.php";
	require_once 'filtros/funcoes.inc.php';
	
	if (defined('PROD_CONF')) $prod_conf = unserialize(PROD_CONF); else $prod_conf = array();
	
	// print_r($prod_conf);

	$retorno = 'list.php';
	if(isset($_POST['slashes']))
	{
		$Img_peq = $_FILES['img_peq'];
		$Apagar_img = $_POST['Apagar_img'];
		$tem_foto = $_POST['tem_foto'];
		$img_path = '../../files/products/';
		$Download_peq["name"] = $_FILES['download_peq']['name']; 
		$Download_peq["temp"] = $_FILES['download_peq']['tmp_name'];
		$img_path_download = "../../files/products_downl/";
		
		if($slashes == "'")
		{
			$cabecalho = addslashes($cabecalho);
			$texto1 = addslashes($texto1);
			$texto2 = addslashes($texto2);
			$texto3 = addslashes($texto3);
			$texto4 = addslashes($texto4);
			$cabecalho_texto5 = addslashes($cabecalho_texto5);
			$texto5 = addslashes($texto5);
			$cor = addslashes($cor);
			//$tamanho= addslashes($tamanho);
			$strSearchWords = addslashes($strSearchWords);
			$ref = addslashes($ref);
			$marca = addslashes($marca);
			$stock_txt = addslashes($stock_txt);
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

		if($task_form=="insere")
		{
			$retorno="products_families.php?ID=".$m_familia;
			$result = InsertNewProd($m_familia,$ordem,$cabecalho,$ref,$tamanho,$cor,$texto1,$texto2,$texto3,$img_path,$Img_peq,$preco1,$preco2,$precopromo1,$precopromo2,$margem,$margem_activa,$destaque,$novidade,$promocao,$home,$saldo,$aproved,$tipo,$preco_prec,$strSearchWords,$marca,$peso,$usado,$stock_txt,$configurado,$configurador,$medidas,$precocusto,$stock,$download,$img_path_download,$Download_peq,$decumentario,$texto4,$cabecalho_texto5,$texto5,$sync,$preco_revenoff);
			
			
			if(isset($prod_conf) && $prod_conf['tem_filtros'] == 1) 
			{
				if(isset($_POST['filtro']))
				{ 
					$filtros_arr = $_POST['filtro'];
					$valores_arr = $_POST['valor'];
					$opcoes_arr = $_POST['opcoes'];
					$prod_id_new = $result['prod_id'];
					include 'products_filtros_save.php';
				}
				
				products_objs_filtros_save($ID);
			}	
			
			if(isset($prod_conf) && $prod_conf['tem_acabamentos'] == 1) products_acabamentos_save($ID);
			$prod_id_on = $result['prod_id'];
			gera_produtos($result['prod_id']);
			
			if($result['res'])	show_box_OK(1,$retorno);
			else show_box_ERROR(1,$retorno);
		}
		elseif($task_form=="altera")
		{
			$retorno = "products.php?ID=$ID&ID_ORG=$m_familia";
			$result = ChangeProd($ID,$m_familia,$ordem,$cabecalho,$ref,$tamanho,$cor,$texto1,$texto2,$texto3,$img_path,$Img_peq, $Apagar_img, $tem_foto,$preco1,$preco2,$precopromo1,$precopromo2,$margem,$margem_activa,$destaque,$novidade,$promocao,$home,$saldo,$aproved,$tipo,$preco_prec,$strSearchWords,$marca,$peso,$usado,$stock_txt,$configurado,$configurador,$medidas,$precocusto,$stock,$download,$img_path_download,$Download_peq,$Apagar_download,$tem_download,$decumentario,$texto4,$cabecalho_texto5,$texto5,$sync,$preco_revenoff);
			if($result)
			{			
				if(isset($prod_conf) && $prod_conf['tem_filtros'] == 1) 
				{
					if(isset($_POST['filtro']))
					{ 
						$filtros_arr = $_POST['filtro'];
						$valores_arr = $_POST['valor'];
						$opcoes_arr = $_POST['opcoes'];					
						
						include 'products_filtros_save.php';
					}
					
					products_objs_filtros_save($ID);
				}
				if(isset($prod_conf) && $prod_conf['tem_acabamentos'] == 1) products_acabamentos_save($ID); 
				$prod_id_on = $ID;
				gera_produtos($ID);
				
				show_box_OK(2,$retorno);
			}
			else show_box_ERROR(2,$retorno);
				
		}
		elseif($task_form=="apaga")
		{
			$retorno="list.php";
			$result=DeleteProd($ID,$img_path);
			if($result)	show_box_OK(3,$retorno);
			else		show_box_ERROR(3,$retorno);
		}
		elseif($_GET['task']== "relacaooff" && $_GET['ID_ORG_REMOVE'] > 0 && $_GET['ID_REMOVE'] > 0)
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
			$retorno="products_families.php?ID=".$ID_ORG;
			$result=InsertNewProd($ID_ORG,$ordem,$cabecalho,$ref,$tamanho,$cor,$texto1,$texto2,$texto3,$img_path,$Img_peq,$preco1,$preco2,$precopromo1,$precopromo2,$margem,$margem_activa,$destaque,$novidade,$promocao,$home,$saldo,$aproved,$tipo,$preco_prec,$strSearchWords,$marca,$peso,$usado,$stock_txt,$configurado,$configurador,$medidas,$precocusto,$stock,$download,$img_path_download,$Download_peq,$decumentario,$texto4,$cabecalho_texto5,$texto5,$sync,$preco_revenoff);
			if($result['res'])
			{	
				show_box_OK(1,$retorno);
				
				if(isset($prod_conf) && $prod_conf['tem_filtros'] == 1) 
				{
					if(isset($_POST['filtro']))
					{ 
						$filtros_arr = $_POST['filtro'];
						$valores_arr = $_POST['valor'];
						$opcoes_arr = $_POST['opcoes'];
						$prod_id_new = $result['prod_id'];
						include 'products_filtros_save.php';
					}
				}
				$prod_id_on = $ID;
				gera_produtos($ID);				
			}
			else show_box_ERROR(1,$retorno);
		}
	}
	else show_box_ERROR(4, $retorno);
		
	if($prod_id_on > 0) gera_produtos($prod_id_on);
	
	global $DB_host, $DB_user, $DB_pass, $DB_name, $admin_lang;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new DataObj;
	$total_p = 0; $del = 0;
	foreach (glob($GLOBALS['site_shell_url']."files/filtros/*_p*.php") as $filename)
	{
		$total_p++;
		$fl = explode('_p',$filename);	
		$prod_id = str_replace('.php', '', $fl[1]);	

		$sql = 'SELECT ID FROM products WHERE ID="'.$prod_id.'" AND Aproved=1';
		$rs->db_open($db_link, $DB_name, $sql);
		if($rs->EOF())
		{
			@unlink($filename);	$del++;
		}
		$rs->db_close();
	}
?>