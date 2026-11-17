<?php
	ini_set('max_execution_time','0');
	ini_set('memory_limit','1024M');

	if(isset($argv) && $argv[1] > 0)$Supplier_ID=intval($argv[1]);
	if(isset($_GET['Supplier_ID']))$Supplier_ID=intval($_GET['Supplier_ID']);

    require_once('settings.inc.php');

	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$dbh = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$sql = '
			SELECT
				conf_id
			FROM
				a_importador_configuracoes
			WHERE
				conf_id='.$Supplier_ID.'
			';
	$result = new DataObj;
	$rs2 = new DataObj;
	$result->db_open($dbh,$DB_name,$sql);
	if(!$result->EOF())
	{
		$sql = '
			UPDATE
				a_importador_configuracoes
			SET
				Marca = "'.addslashes(trim($_POST['Marca'])).'",
				MarcaDesc = "'.addslashes(trim($_POST['MarcaDesc'])).'",
				Categoria = "'.addslashes(trim($_POST['Categoria'])).'",
				CategoriaDesc = "'.addslashes(trim($_POST['CategoriaDesc'])).'",
				Sub_Categoria = "'.addslashes(trim($_POST['Sub_Categoria'])).'",
				Sub_CategoriaDesc = "'.addslashes(trim($_POST['Sub_CategoriaDesc'])).'",
				Sub_Categoria2 = "'.addslashes(trim($_POST['Sub_Categoria2'])).'",
				Sub_Categoria2Desc = "'.addslashes(trim($_POST['Sub_Categoria2Desc'])).'",
				Codigo_produto = "'.addslashes(trim($_POST['Codigo_produto'])).'",
				Produto = "'.addslashes(trim($_POST['Produto'])).'",
				Descricao = "'.addslashes(trim($_POST['Descricao'])).'",
				Preco_IVA = "'.addslashes(trim($_POST['Preco_IVA'])).'",
				Preco2_IVA = "'.addslashes(trim($_POST['Preco2_IVA'])).'",
				Preco3_IVA = "'.addslashes(trim($_POST['Preco3_IVA'])).'",
				Margem = "'.addslashes(trim($_POST['Margem'])).'",
				EAN = "'.addslashes(trim($_POST['EAN'])).'",
				Stock = "'.addslashes(trim($_POST['Stock'])).'",
				Imagem1 = "'.addslashes(trim($_POST['Imagem1'])).'",
				Imagem2 = "'.addslashes(trim($_POST['Imagem2'])).'",
				Imagem3 = "'.addslashes(trim($_POST['Imagem3'])).'",
				Imagem4 = "'.addslashes(trim($_POST['Imagem4'])).'",
				Peso = "'.addslashes(trim($_POST['Peso'])).'",
				Separador = "'.addslashes(trim($_POST['Separador'])).'",
				Separador2 = "'.addslashes(trim($_POST['Separador2'])).'",
				conf1 = "'.addslashes(trim($_POST['conf1'])).'",
				conf2 = "'.addslashes(trim($_POST['conf2'])).'",
				conf3 = "'.addslashes(trim($_POST['conf3'])).'",
				conf4 = "'.addslashes(trim($_POST['conf4'])).'",
				Separador_decimais = "'.addslashes(trim($_POST['Separador_decimais'])).'",
				Preco_conf = "'.addslashes(trim($_POST['Preco_conf'])).'",
				Preco_iva_valor = "'.addslashes(trim($_POST['Preco_iva_valor'])).'",
				Imagem_conf = "'.addslashes(trim($_POST['Imagem_conf'])).'",
				Imagem_pasta = "'.addslashes(trim($_POST['Imagem_pasta'])).'",
				ftp_servidor = "'.addslashes(trim($_POST['ftp_servidor'])).'",
				ftp_porta = "'.addslashes(trim($_POST['ftp_porta'])).'",
				ftp_user = "'.addslashes(trim($_POST['ftp_user'])).'",
				ftp_password = "'.addslashes(trim($_POST['ftp_password'])).'",
				ftp_passivo = "'.addslashes(trim($_POST['ftp_passivo'])).'",
				ftp_caminho = "'.addslashes(trim($_POST['ftp_caminho'])).'",
				ficheiro_caminho = "'.addslashes(trim($_POST['ficheiro_caminho'])).'",
				ficheiro_caminho_json = "'.addslashes(trim($_POST['ficheiro_caminho_json'])).'",
				ficheiro_caminho_api = "'.addslashes(trim($_POST['ficheiro_caminho_api'])).'",
				ficheiro_caminho_api_login = "'.addslashes(trim($_POST['ficheiro_caminho_api_login'])).'",
				ficheiro_caminho_api_pass = "'.addslashes(trim($_POST['ficheiro_caminho_api_pass'])).'",
				tipo_importacao = "'.addslashes(trim($_POST['tipo_importacao'])).'",
				ficheiro_ftp = "'.addslashes(trim($_POST['ficheiro_ftp'])).'",
				ficheiro_ftp1 = "'.addslashes(trim($_POST['ficheiro_ftp1'])).'",
				ficheiro_ftp2 = "'.addslashes(trim($_POST['ficheiro_ftp2'])).'",
				ficheiro_ftp3 = "'.addslashes(trim($_POST['ficheiro_ftp3'])).'",
				Descricao2 = "'.addslashes(trim($_POST['Descricao2'])).'",
				Classe_energetica = "'.addslashes(trim($_POST['Classe_energetica'])).'",
				Link_Classe_Energetica = "'.addslashes(trim($_POST['Link_Classe_Energetica'])).'",
				Stock1 = "'.addslashes(trim($_POST['Stock1'])).'",
				Stock2 = "'.addslashes(trim($_POST['Stock2'])).'",
				Stock3 = "'.addslashes(trim($_POST['Stock3'])).'",
				Stock4 = "'.addslashes(trim($_POST['Stock4'])).'",
				codigo_importacao = "'.addslashes(trim($_POST['codigo_importacao'])).'",
				Eliminar_conf = "'.addslashes(trim($_POST['Eliminar_conf'])).'",
				fora_gama = "'.addslashes(trim($_POST['fora_gama'])).'",
				fora_gama_codigo = "'.addslashes(trim($_POST['fora_gama_codigo'])).'",
				log_activation = "'.addslashes(trim($_POST['log_activation'])).'",
				email_address = "'.addslashes(trim($_POST['email_address'])).'",
				cron_activation = "'.addslashes(trim($_POST['cron_activation'])).'",
				cron = "'.addslashes(trim($_POST['cron'])).'",
				price_conf = "'.addslashes(trim($_POST['price_conf'])).'",
				codigo_fam_default = "'.addslashes(trim($_POST['codigo_fam_default'])).'",
				marcas_atraves_nome = "'.addslashes(trim($_POST['marcas_atraves_nome'])).'",
				cria_marcas_novas = "'.addslashes(trim($_POST['cria_marcas_novas'])).'",
				key_field = "'.addslashes(trim($_POST['key_field'])).'"
			WHERE
				conf_id="'.$Supplier_ID.'"
		';
	}
	else
	{
		$sql = '
			INSERT INTO
				a_importador_configuracoes
				(
					conf_id,
					Marca,
					MarcaDesc,
					Categoria,
					CategoriaDesc,
					Sub_Categoria,
					Sub_CategoriaDesc,
					Sub_Categoria2,
					Sub_Categoria2Desc,
					Codigo_produto,
					Produto,
					Descricao,
					Preco_IVA,
					Preco2_IVA,
					Preco3_IVA,
					Margem,
					EAN,
					Stock,
					Imagem1,
					Imagem2,
					Imagem3,
					Imagem4,
					Peso,
					Separador,
					Separador2,
					conf1,
					conf2,
					conf3,
					conf4,
					Separador_decimais,
					Preco_conf,
					Preco_iva_valor,
					Imagem_conf,
					Imagem_pasta,
					ftp_servidor,
					ftp_porta,
					ftp_user,
					ftp_password,
					ftp_passivo,
					ftp_caminho,
					ficheiro_caminho,
					ficheiro_caminho_json,
					ficheiro_caminho_api,
					ficheiro_caminho_api_login,
					ficheiro_caminho_api_pass,
					tipo_importacao,
					ficheiro_ftp,
					ficheiro_ftp1,
					ficheiro_ftp2,
					ficheiro_ftp3,
					Descricao2,
					Classe_energetica,
					Link_Classe_Energetica,
					Stock1,
					Stock2,
					Stock3,
					Stock4,
					codigo_importacao,
					Eliminar_conf,
					fora_gama,
					fora_gama_codigo,
					log_activation,
					email_address,
					cron_activation,
					cron,
					price_conf,
					codigo_fam_default,
					marcas_atraves_nome,
					cria_marcas_novas,
					key_field
				)
				VALUES
				(
					"'.$Supplier_ID.'",
					"'.addslashes(trim($_POST['Marca'])).'",
					"'.addslashes(trim($_POST['MarcaDesc'])).'",
					"'.addslashes(trim($_POST['Categoria'])).'",
					"'.addslashes(trim($_POST['CategoriaDesc'])).'",
					"'.addslashes(trim($_POST['Sub_Categoria'])).'",
					"'.addslashes(trim($_POST['Sub_CategoriaDesc'])).'",
					"'.addslashes(trim($_POST['Sub_Categoria2'])).'",
					"'.addslashes(trim($_POST['Sub_Categoria2Desc'])).'",
					"'.addslashes(trim($_POST['Codigo_produto'])).'",
					"'.addslashes(trim($_POST['Produto'])).'",
					"'.addslashes(trim($_POST['Descricao'])).'",
					"'.addslashes(trim($_POST['Preco_IVA'])).'",
					"'.addslashes(trim($_POST['Preco2_IVA'])).'",
					"'.addslashes(trim($_POST['Preco3_IVA'])).'",
					"'.addslashes(trim($_POST['Margem'])).'",
					"'.addslashes(trim($_POST['EAN'])).'",
					"'.addslashes(trim($_POST['Stock'])).'",
					"'.addslashes(trim($_POST['Imagem1'])).'",
					"'.addslashes(trim($_POST['Imagem2'])).'",
					"'.addslashes(trim($_POST['Imagem3'])).'",
					"'.addslashes(trim($_POST['Imagem4'])).'",
					"'.addslashes(trim($_POST['Peso'])).'",
					"'.addslashes(trim($_POST['Separador'])).'",
					"'.addslashes(trim($_POST['Separador2'])).'",
					"'.addslashes(trim($_POST['conf1'])).'",
					"'.addslashes(trim($_POST['conf2'])).'",
					"'.addslashes(trim($_POST['conf3'])).'",
					"'.addslashes(trim($_POST['conf4'])).'",
					"'.addslashes(trim($_POST['Separador_decimais'])).'",
					"'.addslashes(trim($_POST['Preco_conf'])).'",
					"'.addslashes(trim($_POST['Preco_iva_valor'])).'",
					"'.addslashes(trim($_POST['Imagem_conf'])).'",
					"'.addslashes(trim($_POST['Imagem_pasta'])).'",
					"'.addslashes(trim($_POST['ftp_servidor'])).'",
					"'.addslashes(trim($_POST['ftp_porta'])).'",
					"'.addslashes(trim($_POST['ftp_user'])).'",
					"'.addslashes(trim($_POST['ftp_password'])).'",
					"'.addslashes(trim($_POST['ftp_passivo'])).'",
					"'.addslashes(trim($_POST['ftp_caminho'])).'",
					"'.addslashes(trim($_POST['ficheiro_caminho'])).'",
					"'.addslashes(trim($_POST['ficheiro_caminho_json'])).'",
					"'.addslashes(trim($_POST['ficheiro_caminho_api'])).'",
					"'.addslashes(trim($_POST['ficheiro_caminho_api_login'])).'",
					"'.addslashes(trim($_POST['ficheiro_caminho_api_pass'])).'",
					"'.addslashes(trim($_POST['tipo_importacao'])).'",
					"'.addslashes(trim($_POST['ficheiro_ftp'])).'",
					"'.addslashes(trim($_POST['ficheiro_ftp1'])).'",
					"'.addslashes(trim($_POST['ficheiro_ftp2'])).'",
					"'.addslashes(trim($_POST['ficheiro_ftp3'])).'",
					"'.addslashes(trim($_POST['Descricao2'])).'",
					"'.addslashes(trim($_POST['Classe_energetica'])).'",
					"'.addslashes(trim($_POST['Link_Classe_Energetica'])).'",
					"'.addslashes(trim($_POST['Stock1'])).'",
					"'.addslashes(trim($_POST['Stock2'])).'",
					"'.addslashes(trim($_POST['Stock3'])).'",
					"'.addslashes(trim($_POST['Stock4'])).'",
					"'.addslashes(trim($_POST['codigo_importacao'])).'",
					"'.addslashes(trim($_POST['Eliminar_conf'])).'",
					"'.addslashes(trim($_POST['fora_gama'])).'",
					"'.addslashes(trim($_POST['fora_gama_codigo'])).'",
					"'.addslashes(trim($_POST['log_activation'])).'",
					"'.addslashes(trim($_POST['email_address'])).'",
					"'.addslashes(trim($_POST['cron_activation'])).'",
					"'.addslashes(trim($_POST['cron'])).'",
					"'.addslashes(trim($_POST['price_conf'])).'",
					"'.addslashes(trim($_POST['codigo_fam_default'])).'",
					"'.addslashes(trim($_POST['marcas_atraves_nome'])).'",
					"'.addslashes(trim($_POST['cria_marcas_novas'])).'",
					"'.addslashes(trim($_POST['key_field'])).'"
				)
		';
	}

	//echo $sql;die();
	$res = Runquery($dbh, $DB_name, $sql);

	$c = count($_POST);
	$keys = array_keys($_POST);
	$fams = array();
	$marcas = array();
	$i=0;
	while($i < $c)
	{
		if(substr_count($keys[$i], 'fam_')) $fams[] = array('fam_id' => str_replace('fam_', '', $keys[$i]), 'fam_codigo' => $_POST[$keys[$i]]);
		elseif(substr_count($keys[$i], 'marca_')) $marcas[] = array('marca_id' => str_replace('marca_', '', $keys[$i]), 'marca_codigo' => $_POST[$keys[$i]]);
		$i++;
	}

	if(count($fams) > 0)
	{
		for($a=0; $a<count($fams); $a++)
		{
			//$sql = 'UPDATE products_families SET codigo_importacao=";'.$fams[$a]['fam_codigo'].';" WHERE ID="'.$fams[$a]['fam_id'].'"';

			$sql = 'SELECT * from a_importador_familias WHERE ID_FAM = "'.$fams[$a]['fam_id'].'" AND conf_id = "'.$Supplier_ID.'"';
			$rs2->db_open($dbh,$DB_name,$sql);
			if(!$rs2->EOF())
			{
				$IDb = $rs2->db_data["ID"];
				$sql = 'UPDATE a_importador_familias SET Codigo = ";'.$fams[$a]['fam_codigo'].';" WHERE ID = '.$IDb;
			}
			else
			{
				if($fams[$a]['fam_codigo'] != '')$sql = 'INSERT INTO a_importador_familias (conf_id,ID_FAM,Codigo) values ("'.$Supplier_ID.'","'.$fams[$a]['fam_id'].'",";'.$fams[$a]['fam_codigo'].';")';
			}
			$rs2->db_close();
			$res = Runquery($dbh, $DB_name, $sql);
		}
	}

	if(count($marcas) > 0)
	{
		for($a=0; $a<count($marcas); $a++)
		{
			//$sql = 'UPDATE brands SET codigo_importacao=";'.$marcas[$a]['marca_codigo'].';" WHERE ID="'.$marcas[$a]['marca_id'].'"';
			$sql = 'SELECT * from a_importador_brands WHERE ID_FAM = "'.$marcas[$a]['marca_id'].'" AND conf_id = "'.$Supplier_ID.'"';
			$rs2->db_open($dbh,$DB_name,$sql);
			if(!$rs2->EOF())
			{
				$IDb = $rs2->db_data["ID"];
				$sql = 'UPDATE a_importador_brands SET Codigo = ";'.$marcas[$a]['marca_codigo'].';" WHERE ID = '.$IDb;
			}
			else
			{
				if($marcas[$a]['marca_codigo'] != '')$sql = 'INSERT INTO a_importador_brands (conf_id,ID_FAM,Codigo) values ("'.$Supplier_ID.'","'.$marcas[$a]['marca_id'].'",";'.$marcas[$a]['marca_codigo'].';")';
			}
			$rs2->db_close();


			$res = Runquery($dbh, $DB_name, $sql);
		}
	}

	if(isset($_POST['tab_sel'])) echo '<script>window.open("import.php?Supplier_ID='.$Supplier_ID.'&tab_sel='.$_POST['tab_sel'].'","_self");</script>';
	else echo '<script>window.open("import.php?Supplier_ID='.$Supplier_ID.'","_self");</script>';
?>