<?php
	ini_set('max_execution_time','0');
	ini_set('memory_limit','-1');
	header('Content-Type: text/html; charset=utf-8');

	$path = dirname(__FILE__).'/../../core/';
	require_once $path.'../conf/conf.php';
	require_once $path.'seguranca.php';
	require_once $path.'db_engine_mysqli.php';
	require_once $path.'sessions_engine.php';
	require_once $path.'global.php';
	require_once $path.'admin_engine.php';
	require_once $path.'seo.php';
	require_once $path.'gd.php';
	require_once $path.'email.php';
	require_once $path.'produtos.php';
	require_once $path.'filtros.php';
	require_once $path.'return_filtros.php';
	require_once('settings_crons.inc.php');
	require_once('funcoes.inc.php');

	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(E_ALL ^ E_DEPRECATED);
	error_reporting(0);

	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);


	$id_produtos=array();

	$sql = 'UPDATE acelerador_pesquisa set Flag=0';
	$res = Runquery($db_link,$DB_name,$sql);

	$myFile = dirname( __FILE__ )."/logs/log.txt";
	$fh = fopen($myFile, 'w');
	fwrite($fh, "a comecar a limpar...\n");


	$sql = "SELECT
				products.*, Cabecalho, Texto3
			FROM
				products
				INNER JOIN products_lang ON products_lang.ID_PAI=products.ID
				INNER JOIN products_objs ON products_objs.obj_type_id=products.ID
				INNER JOIN products_families on products_families.ID=products.ID_ORG
			WHERE
				(products.Aproved=1 and apagado=0)
				AND products_families.visivel=1
				AND products_lang.Lang='$site_lang'
				AND products_objs.obj_pvp > 0
			GROUP BY
				products.Ref
			ORDER BY
				products.ID";

	$rs = new DataObj;
	$rs2 = new DataObj;
	$rs->db_open( $db_link,$DB_name,$sql);
	$total_produtos = $rs->db_rows;
	$linha = 1;
	$ll = '';
	$ll2 = '';

	while(!$rs->EOF())
	{

		$ll = intval(($linha * 100 ) / $total_produtos);
		if($ll != $ll2)
		{
			$ll2 = $ll;
			fwrite($fh, $ll."% \n");
		}

		$id_produto = $rs->db_data["ID"];
		$cabecalho = addslashes($rs->db_data["Cabecalho"]);
		$id_fam = $rs->db_data["ID_ORG"];
		$preco_site = $rs->db_data["Preco1"];
		$ID_Marca = $rs->db_data["ID_Marca"];
		$ref = $rs->db_data["Ref"];
		$EAN = $rs->db_data["Ref_Ean"];
		$caract = addslashes($rs->db_data["Texto3"]);
		$fornecedor_importacao = $rs->db_data["fornecedor_importacao"];



		if($GLOBALS['use_elastic_search']==1)
		{
			//Nao necessitamos de texto para pesquisa
		}
		else
		{
			$marcax = devolve_marcas($ID_Marca);
			if($marcax) $marcax = ", ".$marcax;
			else $marcax = "";

			$familia = Buscafamilianome($id_fam);
			if($familia) $familia = ", ".$familia;
			else $familia = "";		
		
			$texto = $cabecalho.$marcax.$familia;
		}

		$filtros='';
	

		$sql="SELECT ID_Prod FROM acelerador_pesquisa WHERE ID_Prod='$id_produto'";
		$hRes=$rs2->db_open($db_link,$DB_name,$sql);
		if(!$rs2->EOF())
        {
            $sql = "UPDATE acelerador_pesquisa set Ref='$ref',EAN='$EAN',Texto='$texto',Flag=1,Cabecalho='$cabecalho',Preco_Site='$preco_site',fam_id1='$fam_id1',fam_id2='$fam_id2',fam_id3='$fam_id3',fam_id4='$fam_id4',marca_id='$ID_Marca',Filtros='$filtros' WHERE ID_Prod=".$id_produto;
            echo "U ";
        }
		else
        {
            $sql = "INSERT INTO acelerador_pesquisa (Ref,EAN,Texto,ID_Prod,Flag,Cabecalho,Preco_Site,fam_id1, fam_id2, fam_id3, fam_id4, marca_id,Filtros) values ('$ref','$EAN','$texto','$id_produto',1,'$cabecalho','$preco_site','$fam_id1','$fam_id2','$fam_id3','$fam_id4','$ID_Marca','$filtros')";
            echo "I ";
        }
		$rs2->db_close();
		
		$id_produtos[] = $id_produto;
		if(!Runquery($db_link,$DB_name,$sql)) echo "Data Base ERROR: " . db_error($db_link).$sql."<br>";
		$linha++;
		$rs->db_next();
	}
	$rs->db_close(1);


	//Agora vamos remover os produtos nao atualizados do elastic search
	//if($GLOBALS['use_elastic_search']==1)atualiza_elastic_search();

	

	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$sql = "delete from acelerador_pesquisa where Flag=0";
	if(!Runquery($db_link,$DB_name,$sql)) echo "Data Base ERROR: " . db_error($db_link)."\n";






	//----------------
	echo "atualiza_catalogo_produtos";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$sql = "UPDATE acelerador_pesquisa2 set Flag=0";
	if(!Runquery($db_link,$DB_name,$sql)) echo "Data Base ERROR: " . db_error($db_link)."\n";

	$rs = new DataObj;
	$rs2 = new DataObj;

	$id_produtos = array();
	$sql = "SELECT * FROM acelerador_pesquisa    ";
    //where ID = 118663
	echo "selecting products";
	$rs->db_open( $db_link,$DB_name,$sql);
	$total_produtos = $rs->db_rows;
	$linha = 1;
	$ll = '';
	$ll2 = '';
	echo "start work";
	fwrite($fh, "agora o acelerador pesquisa 2...\n");
	while(!$rs->EOF())
	{

		$ll = intval(($linha * 100 ) / $total_produtos);
		if($ll != $ll2)
		{
			$ll2 = $ll;
			echo $ll."% \n";
			fwrite($fh, $ll."% \n");
		}

		$ID_Prod = $rs->db_data["ID_Prod"];
		$Cabecalho = $rs->db_data["Cabecalho"];
		$Texto = $rs->db_data["EAN"].' '.$rs->db_data["Ref"].' '.$rs->db_data["Texto"];


		$sql = "UPDATE acelerador_pesquisa2 SET Flag=1 WHERE ID_Prod = '$ID_Prod'";
		Runquery($db_link,$DB_name,$sql);


		$palavras=explode(' ',$Texto);
		$i=0;
		$t=count($palavras);
		while($i<$t)
		{
			$palavra=trim(strtolower($palavras[$i]));
			if($palavra != '')
			{
				$key=$ID_Prod.'_'.base64_encode($palavra);

	            $sql = "INSERT INTO acelerador_pesquisa2 (`Key`,ID_Prod,Cabecalho,STR,Flag) values ('$key','$ID_Prod','$Cabecalho','$palavra','1')";
	            //echo $sql."\n";
				Runquery($db_link,$DB_name,$sql);
			}
			$i++;
		}

		$linha++;
		$rs->db_next();
	}
	$rs->db_close(1);


	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$sql = "delete from acelerador_pesquisa2 where Flag=0";
	if(!Runquery($db_link,$DB_name,$sql)) echo "Data Base ERROR: " . db_error($db_link)."\n";
	//-----------------------------------------------









	global $site_shell_url;
	if(function_exists('exec'))
	{
		exec('rm -f '.$site_shell_url.'files/cache/products/p10*.*');
		exec('rm -f '.$site_shell_url.'files/cache/products/p11*.*');
		exec('rm -f '.$site_shell_url.'files/cache/products/p12*.*');
		exec('rm -f '.$site_shell_url.'files/cache/products/p13*.*');
		exec('rm -f '.$site_shell_url.'files/cache/products/p14*.*');
		exec('rm -f '.$site_shell_url.'files/cache/products/p15*.*');
		exec('rm -f '.$site_shell_url.'files/cache/products/p16*.*');
		exec('rm -f '.$site_shell_url.'files/cache/products/p17*.*');
		exec('rm -f '.$site_shell_url.'files/cache/products/p18*.*');
		exec('rm -f '.$site_shell_url.'files/cache/products/p19*.*');
		exec('rm -f '.$site_shell_url.'files/cache/products/p2*.*');
		exec('rm -f '.$site_shell_url.'files/cache/products/p3*.*');
		exec('rm -f '.$site_shell_url.'files/cache/products/p4*.*');
		exec('rm -f '.$site_shell_url.'files/cache/products/p5*.*');
		exec('rm -f '.$site_shell_url.'files/cache/products/*.*');
		exec('rm -f '.$site_shell_url.'files/cache/*.*');
		exec('rm -f '.$site_shell_url.'files/cache/*');
		exec('rm -f '.$site_shell_url.'files/filtros/*1*.*');
		exec('rm -f '.$site_shell_url.'files/filtros/*2*.*');
		exec('rm -f '.$site_shell_url.'files/filtros/*3*.*');
		exec('rm -f '.$site_shell_url.'files/filtros/*4*.*');
		exec('rm -f '.$site_shell_url.'files/filtros/*5*.*');
		exec('rm -f '.$site_shell_url.'files/filtros/*6*.*');
		exec('rm -f '.$site_shell_url.'files/filtros/*7*.*');
		exec('rm -f '.$site_shell_url.'files/filtros/*8*.*');
		exec('rm -f '.$site_shell_url.'files/filtros/*9*.*');
		exec('rm -f '.$site_shell_url.'files/filtros/*0*.*');
		exec('rm -f '.$site_shell_url.'files/filtros/*.*');
		exec('rm -f '.$site_shell_url.'files/tmp/*.*');
	}
	else
	{
		remove_files($site_shell_url.'files/cache/products');
		remove_files($site_shell_url.'files/cache');
		remove_files($site_shell_url.'files/filtros');
		remove_files($site_shell_url.'files/tmp');
	}

	function remove_files($path)
	{
		if ($handle = opendir($path))
	    {
	        while (false !== ($file = readdir($handle)))
	        {
	            if( is_file($file) )
	            {
	                unlink($file);
	            }
	        }
	        closedir($handle);
	    }
	}


	/*
	fwrite($fh, "A remover ficheiros de filtros de produtos \n");	
	if(count($id_produtos) > 0)
	{
		foreach (glob($GLOBALS['site_shell_url'] . "files/filtros/*_p*_" . $GLOBALS['site_lang'] . ".json") as $filename)
		{
			$fl = explode('_p',$filename);
			$prod_id = str_replace(array('_en.json', '_pt.json', '_es.json', '_de.json', '_fr.json'), '', $fl[1]);

			if(!in_array($prod_id, $id_produtos)) unlink($filename);
		}
	}

	fwrite($fh, "A remover ficheiros de filtros de familias de produtos \n");	
	if(count($id_produtos) > 0)
	{
		foreach (glob($GLOBALS['site_shell_url'] . "files/filtros/*_p*_" . $GLOBALS['site_lang'] . ".json") as $filename)
		{
			$fl = explode('_p',$filename);
			$prod_id = str_replace(array('_en.json', '_pt.json', '_es.json', '_de.json', '_fr.json'), '', $fl[1]);

			if(!in_array($prod_id, $id_produtos)) unlink($filename);
		}
	}*/

	fwrite($fh, "Fim \n");


	fclose($fh);








	function atualiza_elastic_search()
	{
		$sql = "SELECT * FROM acelerador_pesquisa group by ID_Prod";
		$rs = new DataObj;
		global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs->db_open( $db_link,$DB_name,$sql);

		$i=0;
		$dados=array();
		while(!$rs->EOF())
		{
			$ID= $rs->db_data["ID"];
			$fam_id1= $rs->db_data["fam_id1"];
			$fam_id2= $rs->db_data["fam_id2"];
			$fam_id3= $rs->db_data["fam_id3"];
			$fam_id4= $rs->db_data["fam_id4"];
			$marca_id= $rs->db_data["marca_id"];
			$Cabecalho= $rs->db_data["Cabecalho"];
			$Texto= $rs->db_data["Texto"];
			$Ref= $rs->db_data["Ref"];
			$EAN= $rs->db_data["EAN"];
			$ID_Prod= $rs->db_data["ID_Prod"];
			$Preco_Site= $rs->db_data["Preco_Site"];
			$Flag= $rs->db_data["Flag"];

			$jsonData = array(
	            "ID" => $ID,
			    "fam_id1" => $fam_id1,
			    "fam_id2" => $fam_id2,
			    "fam_id3" => $fam_id3,
			    "fam_id4" => $fam_id4,
			    "marca_id" => $marca_id,
			    "Cabecalho" => $Cabecalho,
			    "Texto" => $Texto,
			    "Ref" => $Ref,
			    "EAN" => $EAN,
			    "ID_Prod" => $ID_Prod,
			    "Preco_Site" => $Preco_Site,
			    "Flag" => $Flag,
	    	);

	    	$dados[$i][0]=$ID;
	    	$dados[$i][1]=$Flag;
	    	$dados[$i][2]=$jsonData;

	    	$i++;

			$rs->db_next();
		}
		$rs->db_close();

		$j=0;
		while($j<$i)
		{
			$ID = $dados[$j][0];
			$Flag = $dados[$j][1];
			$jsonData = $dados[$j][2];

			if($Flag==1)  	$res=elastic_search_insert(PROJETO,$jsonData,$ID);
	    	else  	$res=elastic_search_delete(PROJETO,$ID);

	    	//var_dump($res);
	    	echo "*";
			$j++;
		}
	}

	function elastic_search_insert($indice='',$jsonData='',$id=0)
	{
	    $jsonData = json_encode($jsonData);
	    $baseUrl = 'http://localhost:9200/'.$indice.'/listing/'.$id;
	    $curlInit = curl_init();
	    curl_setopt($curlInit, CURLOPT_URL, $baseUrl);
	    curl_setopt($curlInit, CURLOPT_PORT, '9200');
	    curl_setopt($curlInit, CURLOPT_TIMEOUT, 2000);
	    curl_setopt($curlInit, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($curlInit, CURLOPT_FORBID_REUSE, 0);
	    curl_setopt($curlInit, CURLOPT_CUSTOMREQUEST, 'PUT');
	    curl_setopt($curlInit, CURLOPT_POSTFIELDS, $jsonData);
	    curl_setopt($curlInit, CURLOPT_HTTPHEADER,
		    array(
		        'Content-Type:application/json'
		    )
		);
	    $response = curl_exec($curlInit);
	    curl_close($curlInit);
	    return $response;
	}
	function elastic_search_delete($indice='',$id)
	{
	  	//Third Step: Index data - Such as add data to your database table
	    
	    $baseUrl = 'http://localhost:9200/'.$indice.'/listing/'.$id;

	    $curlInit = curl_init();
	    curl_setopt($curlInit, CURLOPT_URL, $baseUrl);
	    curl_setopt($curlInit, CURLOPT_PORT, '9200');
	    curl_setopt($curlInit, CURLOPT_TIMEOUT, 2000);
	    curl_setopt($curlInit, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($curlInit, CURLOPT_FORBID_REUSE, 0);
	    curl_setopt($curlInit, CURLOPT_CUSTOMREQUEST, 'DELETE');
	    curl_setopt($curlInit, CURLOPT_HTTPHEADER,
		    array(
		        'Content-Type:application/json'
		    )
		);
	    $response = curl_exec($curlInit);
	    curl_close($curlInit);
	    return $response;
	}

	function elastic_delete_all($indice='',$id)
	{
	  	//Third Step: Index data - Such as add data to your database table
	    
	    $baseUrl = 'http://localhost:9200/'.$indice.'/_all/';

	    $curlInit = curl_init();
	    curl_setopt($curlInit, CURLOPT_URL, $baseUrl);
	    curl_setopt($curlInit, CURLOPT_PORT, '9200');
	    curl_setopt($curlInit, CURLOPT_TIMEOUT, 2000);
	    curl_setopt($curlInit, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($curlInit, CURLOPT_FORBID_REUSE, 0);
	    curl_setopt($curlInit, CURLOPT_CUSTOMREQUEST, 'DELETE');
	    curl_setopt($curlInit, CURLOPT_HTTPHEADER,
		    array(
		        'Content-Type:application/json'
		    )
		);
	    $response = curl_exec($curlInit);
	    curl_close($curlInit);
	    return $response;
	}

	function criar_filtros_familias()
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs=new DataObj;

		$sql="SELECT ID FROM products_families WHERE visivel=1";
		$hRes=$rs->db_open( $db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$IDb=addslashes($rs->db_data["ID"]);
			echo "a gerar familia $IDb";
			gera_json_categorias(1,$IDb,'pt');
		}
		$rs->db_close();

	}

	function devolve_marcas($ID_Marca)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs=new DataObj;

		$sql="SELECT Nome FROM brands_lang WHERE Lang='$site_lang' and ID_PAI=$ID_Marca";
		$hRes=$rs->db_open( $db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$cabecalho=$rs->db_data["Nome"];
		}
		$rs->db_close();

		return $cabecalho;
	}

	function Buscafamilianome($ID_PAI)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs=new DataObj;

		$sql="SELECT Cabecalho FROM products_families_lang WHERE Lang='$site_lang' and ID_PAI=$ID_PAI";
		$hRes=$rs->db_open( $db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$cabecalho=addslashes($rs->db_data["Cabecalho"]);
		}
		$rs->db_close();

		return $cabecalho;

	}
	function devolve_filtros($ID_Prod=0)
	{
		$sql = "SELECT * FROM filters_products WHERE prod_id = $ID_Prod limit 0,1";
		$rs = new DataObj;
		global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs->db_open( $db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$combo = $rs->db_data["filtros_rel2"];
			$rs->db_close();
			
			return $combo;
		}
	}
?>