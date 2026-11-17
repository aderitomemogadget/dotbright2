<?php
	ini_set('max_execution_time','0');
	ini_set('memory_limit','-1');

    $abs_path = dirname( __FILE__ ).'/';


    $path=$abs_path."../../../../core/";
    include $path.'core_files.php';

    include 'funcoes.inc.php';
    define('FILE_PATH',$abs_path.'../../../../files/importar/');
    define("IMPORT_POS_FAMILIA",0);
    define("IMPORT_POS_REF",1);
    define("IMPORT_POS_NOME",2);
    define("IMPORT_POS_MODELO",3);
    define("IMPORT_POS_PVP",4);
    define("IMPORT_POS_PVP_PROMO",5);
    define("IMPORT_POS_PROMO",6);
    define("IMPORT_POS_DESTAQUE",7);
    define("IMPORT_POS_STOCK",8);
    define("IMPORT_POS_PESO",9);
    define("IMPORT_POS_MEDIDAS",10);
    define("IMPORT_POS_SINONIMOS",11);
    define("IMPORT_POS_CARRINHO",12);
    define("IMPORT_POS_VISIVEL",13);
    define("IMPORT_POS_ORDEM",14);



    //$abs_path='/on4tech.pt/public_html/admin/importadores/jpsacouto/';
    //Vamos descarregar o ficheiro


    ini_set('display_errors',1);
    ini_set('display_startup_errors',1);
    error_reporting(0);

    //The username or email address of the account.
    define('USERNAME', 'comercial@memogadget.com');

    //The password of the account.
    define('PASSWORD', '14173');

    //Set a user agent. This basically tells the server that we are using Chrome ;)
    define('USER_AGENT', 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.2309.372 Safari/537.36');

    //Where our cookie information will be stored (needed for authentication).
    define('COOKIE_FILE', $abs_path.'cookie.txt');


    //Initiate cURL.
    $curl = curl_init();

    //Let's attempt to access page
    curl_setopt($curl, CURLOPT_URL, 'https://www.dotbright.pt/core/crons/gera_export_feed.php');

    //Use the same cookie file.
    curl_setopt($curl, CURLOPT_COOKIEJAR, COOKIE_FILE);

    //Use the same user agent, just in case it is used by the server for session validation.
    curl_setopt($curl, CURLOPT_USERAGENT, USER_AGENT);

    //We don't want any HTTPS / SSL errors.
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);


    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLINFO_HEADER_OUT, false);

    //Execute the GET request and print out the result.
    //
    $fileout =  $abs_path.'tmp/filecurl.csv';
    $fp = fopen ($fileout , 'w+');
    // write curl response to file
    curl_setopt($curl, CURLOPT_FILE, $fp);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

    $data = curl_exec($curl);
    curl_close($curl);
    fclose($fp);


    //Agora vamos copiar o ficheiro
    $filename = 'dotbright_feed_export.txt';
    $result=copy($abs_path.'tmp/filecurl.csv',FILE_PATH.$filename);

	if(!$result) echo '<div style="color:#F00;">ERRO: Não foi possível colocar o ficheiro no servidor na pasta '.FILE_PATH.$filename.'</div>';
    else
    {
		$f = FILE_PATH.$filename;

        $new_heading =  "Ref;Ref_Fabricante;Designacao;Preco_Final;Stock;Unidade_Negocio;Linha_Produto;Familia;Sub-Familia;Categoria;Marca;Data_modificacao;Descricao;Imagem;URL_Imagem;ean;Preco_Unitario;Taxa";
		$new_heading .= "\n";

		// read into array
		$arr = file($f);

		// edit first line
		$arr[0] = $new_heading;

		// write back to file
		if(file_put_contents($f, implode($arr)))
		{
			$path = dirname( __FILE__ ) . '/utils/insere_prods.php';
			if(file_exists($path)) echo 'file exists<br>';
			$exe_file = "php ".$path." > /dev/null &";

            echo '--> o ficheiro foi copiado com sucesso para o servidor, vai se iniciar a actualização dos dados';
            echo $exe_file;

            exec($exe_file);


		}
    }

?>