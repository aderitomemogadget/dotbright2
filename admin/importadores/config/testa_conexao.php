<?php
	ini_set('max_execution_time','0');
	ini_set('memory_limit','-1');
	header('Content-Type: text/html; charset=utf-8');

	if(isset($argv) && $argv[1] > 0)$Supplier_ID=intval($argv[1]);
	if(isset($_GET['Supplier_ID']))$Supplier_ID=intval($_GET['Supplier_ID']);

	$path = dirname(__FILE__).'/../../../core/';
	include $path.'core_files.php';
	require_once('settings_crons.inc.php');
	require_once('funcoes.inc.php');

	$img_path = dirname( __FILE__ )."/../../../files/importar/";

	global $Supplier_ID;
	$rs = getImportadorConfiguracoes_2($Supplier_ID);
	$Marca = stripslashes($rs->db_data['Marca']);
	$MarcaDesc = stripslashes($rs->db_data['MarcaDesc']);
	$Categoria = stripslashes($rs->db_data['Categoria']);
	$CategoriaDesc = stripslashes($rs->db_data['CategoriaDesc']);
	$Sub_Categoria = stripslashes($rs->db_data['Sub_Categoria']);
	$Sub_CategoriaDesc = stripslashes($rs->db_data['Sub_CategoriaDesc']);
	$Sub_Categoria2 = stripslashes($rs->db_data['Sub_Categoria2']);
	$Sub_Categoria2Desc = stripslashes($rs->db_data['Sub_Categoria2Desc']);
	$Codigo_produto = stripslashes($rs->db_data['Codigo_produto']);
	$Produto = stripslashes($rs->db_data['Produto']);
	$Descricao = stripslashes($rs->db_data['Descricao']);
	$Preco_IVA = stripslashes($rs->db_data['Preco_IVA']);
	$EAN = stripslashes($rs->db_data['EAN']);
	$Stock = stripslashes($rs->db_data['Stock']);
	$Imagem1 = stripslashes($rs->db_data['Imagem1']);
	$Imagem2 = stripslashes($rs->db_data['Imagem2']);
	$Imagem3 = stripslashes($rs->db_data['Imagem3']);
	$Imagem4 = stripslashes($rs->db_data['Imagem4']);
	$Separador = stripslashes($rs->db_data['Separador']);
	$Separador2 = htmlentities(stripslashes($rs->db_data['Separador2']));
	$conf1 = $rs->db_data['conf1'];
	$conf2 = $rs->db_data['conf2'];
	$conf3 = $rs->db_data['conf3'];
	$conf4 = $rs->db_data['conf4'];
	$Preco_conf = $rs->db_data['Preco_conf'];
	$Preco_iva_valor = $rs->db_data['Preco_iva_valor'];
	$Imagem_conf = $rs->db_data['Imagem_conf'];
	$Imagem_pasta = $rs->db_data['Imagem_pasta'];
	$Peso = $rs->db_data['Peso'];
	$ftp_servidor = $rs->db_data['ftp_servidor'];
	$ftp_porta = $rs->db_data['ftp_porta'];
	$ftp_user = $rs->db_data['ftp_user'];
	$ftp_password = $rs->db_data['ftp_password'];
	$ftp_passivo = $rs->db_data['ftp_passivo'];
	$ftp_caminho = $rs->db_data['ftp_caminho'];
	$ficheiro_caminho = $rs->db_data['ficheiro_caminho'];
	$ficheiro_caminho_json = $rs->db_data['ficheiro_caminho_json'];
	$ficheiro_caminho_api = $rs->db_data['ficheiro_caminho_api'];
	$ficheiro_caminho_api_login = $rs->db_data['ficheiro_caminho_api_login'];
	$ficheiro_caminho_api_pass = $rs->db_data['ficheiro_caminho_api_pass'];
	$tipo_importacao = $rs->db_data['tipo_importacao'];
	$ficheiro_ftp = $rs->db_data['ficheiro_ftp'];
	$Descricao2 = $rs->db_data['Descricao2'];
	$Classe_energetica = $rs->db_data['Classe_energetica'];
	$Link_Classe_Energetica = $rs->db_data['Link_Classe_Energetica'];
	$Stock1 = $rs->db_data['Stock1'];
	$Stock2 = $rs->db_data['Stock2'];
	$Stock3 = $rs->db_data['Stock3'];
	$Stock4 = $rs->db_data['Stock4'];
	$codigo_importacao = $rs->db_data['codigo_importacao'];
	$Eliminar_conf = $rs->db_data['Eliminar_conf'];
	$fora_gama = $rs->db_data['fora_gama'];
	$fora_gama_codigo = $rs->db_data['fora_gama_codigo'];
	$log_activation = $rs->db_data['log_activation'];
	$email_address = $rs->db_data['email_address'];
	$cron_activation = $rs->db_data['cron_activation'];
	$cron = $rs->db_data['cron'];
	$price_conf = $rs->db_data['price_conf'];
	$codigo_fam_default = $rs->db_data['codigo_fam_default'];
	$marcas_atraves_nome = $rs->db_data['marcas_atraves_nome'];
	$cria_marcas_novas = $rs->db_data['cria_marcas_novas'];
	$key_field = $rs->db_data['key_field'];


	if($tipo_importacao == 0)
	{

		$ftp_servidor = $rs->db_data['ftp_servidor'];
		$ftp_porta = $rs->db_data['ftp_porta'];
		$ftp_user = $rs->db_data['ftp_user'];
		$ftp_password = $rs->db_data['ftp_password'];
		$ftp_passivo = $rs->db_data['ftp_passivo'];
		$ftp_caminho = $rs->db_data['ftp_caminho'];

		$rs->db_close();

		define('SERVER', $ftp_servidor); //IP o Nome do Servidor
		define('PORT', ($ftp_porta == 0 || $ftp_porta == '' ? 21 : $ftp_porta)); //Porto
		define('USER', $ftp_user); //Nome de Usuário
		define('PASSWORD', $ftp_password); //Senha de acesso
		define('PASV', ($ftp_passivo == 1 ? true : false)); //Ativa modo passivo

		ConectarFTP2();
	}

	if($tipo_importacao == 1)
	{
		if(!$ficheiro_caminho)echo "erro !! nao existe url configurada";
		else
		{
			$file = get_web_file($ficheiro_caminho);
			// VAMOS VERICAR SE O FICHEIRO NÃO VEM EM UTF-8 E VAMOS CONVERTER
//$encodedOutput = convToUtf8($file);
			// VAMOS GUARDAR O NOVO FICHEIRO
			$file_to_import = 'web_tmp'.$Supplier_ID.'.csv';
			file_put_contents($img_path.$file_to_import,$encodedOutput);
			if(!file_exists($img_path.$file_to_import)) echo "erro !! nao copiou o fx $img_path.$file_to_import";
			else
			{
				echo "ficheiro gravado com sucesso!";
				unlink($img_path.$file_to_import);
			}
		}
	}

	if($tipo_importacao == 2)
	{
		if(!$ficheiro_caminho_json)echo "erro !! nao existe url configurada";
		else
		{
			$file = get_web_file($ficheiro_caminho_json);
			// VAMOS VERICAR SE O FICHEIRO NÃO VEM EM UTF-8 E VAMOS CONVERTER
//$encodedOutput = convToUtf8($file);
			// VAMOS GUARDAR O NOVO FICHEIRO
			$file_to_import = 'web_tmp'.$Supplier_ID.'.json';
			file_put_contents($img_path.$file_to_import,$encodedOutput);
			if(!file_exists($img_path.$file_to_import)) echo "erro !! nao copiou o fx $img_path.$file_to_import";
			else
			{
				echo "ficheiro gravado com sucesso!";
				unlink($img_path.$file_to_import);
			}
		}
	}

	if($tipo_importacao == 3)
	{
		if(!$ficheiro_caminho_api_login)echo 'erro !! nao existe url configurada';
		else
		{
			$timeout = 5;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $ficheiro_caminho_api);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_USERPWD, "$ficheiro_caminho_api_login:$ficheiro_caminho_api_pass");
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			$data = curl_exec($ch);
			$info = curl_getinfo($ch);
			if (curl_errno($ch))
			{
		    	$error_msg = curl_error($ch);
		    	echo 'Erro !!! '.$error_msg;
			}
			else echo 'Ligação OK';
			curl_close($ch);
		}
	}

	function ConectarFTP2()
	{
		// PERMITE SE CONECTAR AO SERVIDOR FTP
		$id_ftp = ftp_connect(SERVER, PORT) or die("Não consegui ligação com ".SERVER);

		// TRY TO LOGIN
		if (@ftp_login($id_ftp,USER, PASSWORD))
		{
			echo SERVER." » Ligação OK";
			// get contents of the current directory
			$contents = ftp_nlist($id_ftp, ".");
			// output $contents
			var_dump($contents);
		}
		else
		{
			echo "Dados ftp incorretos";
		}

		if(PASV == true) ftp_pasv($id_ftp, MODO); //Estabelece o modo de conexão
		return $id_ftp; //Devolve o manejador à função
	}


?>