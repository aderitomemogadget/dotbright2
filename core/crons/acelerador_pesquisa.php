<?php
//CREATE TABLE test ENGINE=MEMORY
$path = dirname( __FILE__ ) . '/../../core/';
require_once $path . 'core_files.php';
require_once dirname( __FILE__ ).'/../../admin/products/funcoesfiltros.inc.php';
require_once dirname( __FILE__ ).'/../../admin/products/filtros/funcoes.inc.php';

if(!isset($_GET['log'])) $log = 0;
error_reporting(E_ERROR);
ini_set('display_errors',0);

header('Content-Type: text/html; charset=utf-8');
ini_set('max_execution_time','60000');
ini_set('max_input_time','60000');
ini_set('memory_limit','-1');

global $DB_host,$DB_user,$DB_pass,$DB_name,$lang;
$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
$obj=new DataObj;
$rs=new DataObj;

/*
$sql = "CREATE TABLE IF NOT EXISTS `acelerador_pesquisa` (
    `ID` int(11) unsigned NOT NULL auto_increment,
    `fam_id1` int(11) unsigned NOT NULL,
    `fam_id2` int(11) unsigned NOT NULL,
    `fam_id3` int(11) unsigned NOT NULL,
    `fam_id4` int(11) unsigned NOT NULL,
    `marca_id` int(11) unsigned NOT NULL,
    `Cabecalho` varchar(255) NOT NULL default '',
    `Texto` varchar(255) NOT NULL default '',
    `Ref` varchar(30) NOT NULL default '',
    `EAN` varchar(30) NOT NULL default '',
    `ID_Prod` int(11) NOT NULL default '0',
    `Preco_Site` double unsigned NOT NULL default '1',
    `Flag` tinyint(1) unsigned NOT NULL default '1',
    PRIMARY KEY  (`ID`)
)ENGINE=MEMORY";
if(!Runquery($db_link,$DB_name,$sql)) echo "Data Base ERROR: " . db_error($db_link)."\n";
*/


echo "Verificar todos os produtos visiveis\n";
$rs->db_open($db_link, $DB_name, "SELECT ID FROM products WHERE Aproved = 1");
while(!$rs->EOF())
{
    $ID = $rs->db_data['ID'];
    $sql='UPDATE products_objs set Visivel = 1 WHERE obj_type_id = "'.$ID.'"';
    $res=Runquery($db_link, $DB_name, $sql);       

    $rs->db_next();
}
$rs->db_close();


echo "Verificar todas as familias invisiveis\n";
$rs->db_open($db_link, $DB_name, "SELECT products.ID
            FROM products
            INNER JOIN products_families_obj FPO on FPO.ID_PROD=products.ID
            INNER JOIN products_families on products_families.ID=FPO.ID_ORG
            WHERE products.Aproved=0 OR products_families.visivel=0 group by obj_type_id");
while(!$rs->EOF())
{
    $ID = $rs->db_data['ID'];
    $sql='UPDATE products_objs set Visivel = 0 WHERE obj_type_id = "'.$ID.'"';
    $res=Runquery($db_link, $DB_name, $sql);

    $rs->db_next();
}
$rs->db_close();

atualiza_catalogo_produtos();
atualiza_autocomplete_produtos();
//atualiza_acelerador_pesquisa2();
otimiza_filtros();

function otimiza_filtros()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
    $db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs = new DataObj;    
    $sql = "SELECT * FROM filters_products GROUP BY prod_id";

    $rs->db_open($db_link,$DB_name,$sql);
    $total_produtos=$rs->db_rows;

	$linha = 1;
	$ll = '';
	$ll2 = '';

    while(!$rs->EOF())
    {
    	$ll = intval(($linha * 100 ) / $total_produtos);
		if($ll != $ll2)
		{
			$ll2 = $ll;
			echo $ll."% \n";
		}


    	$FAM_ID1=$rs->db_data["fam_id1"];
    	$FAM_ID2=$rs->db_data["fam_id2"];
    	$FAM_ID3=$rs->db_data["fam_id3"];
    	$FAM_ID4=$rs->db_data["fam_id4"];
    	$CAT_ID =$rs->db_data["cat_id"];

    	$sql = "REPLACE INTO filters_categories_fast (Chave,FAM_ID,CAT_ID) VALUES ('".$FAM_ID1."_".$CAT_ID."','$FAM_ID1','$CAT_ID')"; Runquery($db_link,$GLOBALS['DB_name'],$sql);
    	$sql = "REPLACE INTO filters_categories_fast (Chave,FAM_ID,CAT_ID) VALUES ('".$FAM_ID2."_".$CAT_ID."','$FAM_ID2','$CAT_ID')"; Runquery($db_link,$GLOBALS['DB_name'],$sql);
    	$sql = "REPLACE INTO filters_categories_fast (Chave,FAM_ID,CAT_ID) VALUES ('".$FAM_ID3."_".$CAT_ID."','$FAM_ID3','$CAT_ID')"; Runquery($db_link,$GLOBALS['DB_name'],$sql);
    	$sql = "REPLACE INTO filters_categories_fast (Chave,FAM_ID,CAT_ID) VALUES ('".$FAM_ID4."_".$CAT_ID."','$FAM_ID4','$CAT_ID')"; Runquery($db_link,$GLOBALS['DB_name'],$sql);
    	
    	$prod_id =$rs->db_data["prod_id"];
    	$filtros_rel =$rs->db_data["filtros_rel2"];

    	$sql = "REPLACE INTO filters_products_fast (prod_id,filtros_rel) VALUES ('$prod_id','$filtros_rel')"; Runquery($db_link,$GLOBALS['DB_name'],$sql);
    	$rs->db_next();

    	//$numprods--;
    	$linha++;
    }
    $rs->db_close();
}
function devolve_marcas($ID_Marca)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;

	$sql="SELECT * FROM brands_lang WHERE Lang='$site_lang' and ID_PAI=$ID_Marca";
	$hRes=$rs->db_open( $db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$cabecalho=addslashes($rs->db_data["Nome"]);
	}
	$rs->db_close();

	return $cabecalho;

}
function Buscafamilianome($ID_PAI)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;

	$sql="SELECT * FROM products_families_lang WHERE Lang='$site_lang' and ID_PAI=$ID_PAI";
	$hRes=$rs->db_open( $db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$cabecalho=addslashes($rs->db_data["Cabecalho"]);
	}
	$rs->db_close();

	return $cabecalho;

}
function atualiza_catalogo_produtos()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$rs = new DataObj;
	$rs2 = new DataObj;

	$sql = "TRUNCATE acelerador_pesquisa";
	if(!Runquery($db_link,$DB_name,$sql)) echo "Data Base ERROR: " . db_error($db_link)."\n";

	$id_produtos = array();

	$fornecedores_importacao = array();
	$myFile = dirname( __FILE__ )."/../../files/importar/atualizar_pesquisa.txt";
	$fh = fopen($myFile, 'a+');


	echo "****************************************** acelerador_pesquisa";


	//produtos normais
	$sql = "SELECT
				products.ID,products.ID_ORG,products_objs.obj_pvp as Preco1,products.ID_Marca,
				products.Ref,products.Ref_Ean,products.fornecedor_importacao,products.estado,
				products.Variante,products.bateria,
				products_lang.Cabecalho,products_lang.Texto3,products_objs.obj_stock
			FROM
				products
				INNER JOIN products_lang ON products_lang.ID_PAI=products.ID
				INNER JOIN products_objs ON products_objs.obj_type_id=products.ID
				INNER JOIN products_families_obj pfo on pfo.ID_PROD=products.ID
				INNER JOIN products_families pf on pf.ID=pfo.ID_ORG
			WHERE
				products.Aproved=1
				AND products_lang.Lang='$site_lang'
				AND products_objs.obj_pvp > 0
				AND products_objs.obj_stock>0 
				AND pf.visivel=1
				AND pfo.ID_ORG <> 163
			GROUP BY
				products.ID
			ORDER BY
				products.ID";

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
			echo $ll."% \n";
		}

		$id_produto = $rs->db_data["ID"];
		$Variante = $rs->db_data["Variante"];
		$estado = $rs->db_data["estado"];
		$bateria = $rs->db_data["bateria"];
		$cabecalho = addslashes(stripslashes($rs->db_data["Cabecalho"]));
		$texto = $cabecalho;

		//if($estado != '')$cabecalho.=' GRAU: '.$estado;
		//if($bateria != '' && $bateria != '0')$cabecalho.=' BAT: '.$bateria.'%';

		$id_fam = $rs->db_data["ID_ORG"];
		$preco_site = $rs->db_data["Preco1"];
		$ID_Marca = $rs->db_data["ID_Marca"];
		$ref = $rs->db_data["Ref"];
		$EAN = $rs->db_data["Ref_Ean"];
		$Stock = $rs->db_data["obj_stock"];
		$caract = addslashes($rs->db_data["Texto3"]);
		$fornecedor_importacao = $rs->db_data["fornecedor_importacao"];


		//Se já está no mysql não preciso de adicionar slashes
		$marcax = devolve_marcas($ID_Marca);
		if($marcax) $marcax = ", ".$marcax;
		else $marcax = "";



			$fam   = '';
		    //$marca = devolve_marcas($ID_Marca);
		    $familia = Buscafamilianome($id_fam);
		    $fam   = addslashes(stripslashes($familia));


		
			$texto = addslashes(stripslashes($cabecalho.$marcax.$familia));

        	$sql = "INSERT INTO acelerador_pesquisa (Ref,EAN,Texto,ID_Prod,Flag,Fam,fam_id,Cabecalho,Preco_Site,marca_id,Stock,id_fam) values ('$ref','$EAN','$texto','$id_produto',1,'$fam','$id_fam','$cabecalho','$preco_site','$ID_Marca','$Stock','$id_fam')";
			echo "I ";

			fwrite($fh, $ll.'% - '.$ref.' - '.$fornecedor_importacao." \n");
			$fornecedores_importacao[$fornecedor_importacao][] = 1;
			$id_produtos[] = $id_produto;

			if(!Runquery($db_link,$DB_name,$sql)) echo "Data Base ERROR: " . db_error($db_link).$sql."<br>";


		$linha++;
		$rs->db_next();
	}
	$rs->db_close();



	echo "****************************************** AGORA SEMINOVOS";

	//------------------------ AGORA SEMINOVOS
	$sql = "SELECT
				products.ID,
				UPPER(TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(products.Ref, '_', 1), ',', -1))) AS uniq_ref,
				IF(products_objs.obj_promo_ativo=1 AND products_objs.obj_promo>0, products_objs.obj_promo, products_objs.obj_pvp) AS uniq_price,
				UPPER(TRIM(COALESCE(products.estado, ''))) AS uniq_grade,
				COALESCE(NULLIF(products.bateria, ''), 0) AS uniq_battery,
				CONCAT(products.EAN,products.estado) AS uniq_ref2,products.ID_ORG,products.Preco1,products.ID_Marca,
				products.Ref,products.Ref_Ean,products.fornecedor_importacao,products.estado,
				products.Variante,products.bateria,
				products_lang.Cabecalho,products_lang.Texto3,products_objs.obj_stock
			FROM
				products
				INNER JOIN products_lang ON products_lang.ID_PAI=products.ID
				INNER JOIN products_objs ON products_objs.obj_type_id=products.ID
				INNER JOIN products_families_obj pfo on pfo.ID_PROD=products.ID
				INNER JOIN products_families pf on pf.ID=pfo.ID_ORG
			WHERE
				products.Aproved=1
				AND products_lang.Lang='$site_lang'
				AND products_objs.obj_pvp > 0
				AND pfo.ID_ORG IN (163) 
				AND pf.visivel=1
				AND products_objs.obj_val2=0 
				AND products_objs.obj_stock>0 
				AND (obj_val2=0 OR obj_val2 IS NULL) 
			GROUP BY uniq_ref, uniq_price, uniq_grade, uniq_battery
			ORDER BY
				products.ID";

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
			echo $ll."% \n";
		}

		$id_produto = $rs->db_data["ID"];
		$Variante = $rs->db_data["Variante"];
		$estado = $rs->db_data["estado"];
		$bateria = $rs->db_data["bateria"];
		$cabecalho = addslashes($rs->db_data["Cabecalho"]);
		$texto = $cabecalho;

		if($estado != '')$cabecalho.=' GRAU: '.$estado;
		if($bateria != '' && $bateria != '0')$cabecalho.=' BAT: '.$bateria.'%';

		$id_fam = $rs->db_data["ID_ORG"];
		$preco_site = $rs->db_data["Preco1"];
		$ID_Marca = $rs->db_data["ID_Marca"];
		$ref = $rs->db_data["Ref"];
		$EAN = $rs->db_data["Ref_Ean"];
		$Stock = $rs->db_data["obj_stock"];
		$caract = addslashes($rs->db_data["Texto3"]);
		$fornecedor_importacao = $rs->db_data["fornecedor_importacao"];


		$marcax = devolve_marcas($ID_Marca);
		if($marcax!='') $marcax = ", ".$marcax;

		$familia = Buscafamilianome($id_fam);
		if($familia!='') $familia = ", ".$familia;

		$txt_familias=addslashes($marcax.$familia);

        $sql = "INSERT INTO acelerador_pesquisa (Ref,EAN,Texto,ID_Prod,Flag,Cabecalho,Preco_Site,marca_id,Stock,id_fam) values ('$ref','$EAN','$texto','$id_produto',1,'$cabecalho','$preco_site','$ID_Marca','$Stock','$id_fam')";
        //echo $sql."\n";
        echo "I ";

		fwrite($fh, $ll.'% - '.$ref.' - '.$fornecedor_importacao." \n");
		$fornecedores_importacao[$fornecedor_importacao][] = 1;
		$id_produtos[] = $id_produto;

		if(!Runquery($db_link,$DB_name,$sql)) echo "Data Base ERROR: " . db_error($db_link).$sql."<br>";



		$linha++;
		$rs->db_next();
	}
	$rs->db_close();

	//----------------------------------------


	if(count($id_produtos) > 0)
	{
		foreach (glob($GLOBALS['site_shell_url'] . "files/filtros/*_p*_" . $GLOBALS['site_lang'] . ".json") as $filename)
		{
			$fl = explode('_p',$filename);
			$prod_id = str_replace(array('_en.json', '_pt.json', '_es.json', '_de.json', '_fr.json'), '', $fl[1]);

			if(!in_array($prod_id, $id_produtos)) unlink($filename);
		}
	}

	fwrite($fh, "100% - eliminou produtos que não estão disponiveis\n\n");

	fclose($fh);

	if(copy($myFile, str_replace('importar/', 'importado/', str_replace('atualizar_pesquisa.txt', 'atualizar_pesquisa_OK_'.date('YmdHis').'.txt', $myFile))))
	{
		@unlink($myFile);
	}

	$now  = time();
	foreach (glob($GLOBALS['site_shell_url']."files/importado/atualizar_pesquisa_OK_*.txt") as $filename)
	{
		if (is_file($filename))
		{
			if ($now - filemtime($filename) >= 60 * 60 * 24 * 3)
			{
				if(substr_count($filename, '.txt')) @unlink($filename);
			}
		}
	}
}
function atualiza_acelerador_pesquisa2()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$sql = "UPDATE acelerador_pesquisa2 set Flag=0";
	if(!Runquery($db_link,$DB_name,$sql)) echo "Data Base ERROR: " . db_error($db_link)."\n";

	$rs = new DataObj;
	$rs2 = new DataObj;

	$id_produtos = array();
	$sql = "SELECT * FROM acelerador_pesquisa";
	echo "****************************************** acelerador_pesquisa2";
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
			echo $ll."% \n";
		}

		$ID_Prod = $rs->db_data["ID_Prod"];
		$Cabecalho = $rs->db_data["Cabecalho"];
		$Texto = $rs->db_data["Texto"];
		$id_fam = $rs->db_data["id_fam"];

		//************* EAN
		$palavra=trim(strtolower($rs->db_data["EAN"]));
		if($palavra != '')
		{
			$key=$ID_Prod.'_'.base64_encode($palavra);
            $sql = "INSERT INTO acelerador_pesquisa2 (`Key`,ID_Prod,Cabecalho,STR,Peso,Flag) values ('$key','$ID_Prod','$Cabecalho','$palavra','5','1')";
			Runquery($db_link,$DB_name,$sql);
		}
		//************* REF
		$palavra=trim(strtolower($rs->db_data["Ref"]));
		if($palavra != '')
		{
			$key=$ID_Prod.'_'.base64_encode($palavra);
            $sql = "INSERT INTO acelerador_pesquisa2 (`Key`,ID_Prod,Cabecalho,STR,Peso,Flag) values ('$key','$ID_Prod','$Cabecalho','$palavra','4','1')";
			Runquery($db_link,$DB_name,$sql);
		}

		$sql = "UPDATE acelerador_pesquisa2 SET Flag=1 WHERE ID_Prod = '$ID_Prod'";
		Runquery($db_link,$DB_name,$sql);

		//Vamos remover caracteres que não queremos

		//$Texto = iconv('UTF-8','ASCII//TRANSLIT',$Texto);
	    //$Texto = preg_replace('/[^a-zA-Z0-9_\.\,\-áÁàÀãÃâÂéÉèÈêÊíÍìÌîÎóÓòÒõÕôÔúÚùÙçÇ& ]/', '',$Texto);
	    //$Texto = filter_var($Texto, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH);
	    //$Texto = str_replace(array('.',',','-','_',','),' ',$Texto);


        $Texto = mb_convert_encoding($Texto, "UTF-8");
	    $Texto = preg_replace('/[^a-zA-Z0-9_\.\,\-áÁàÀãÃâÂéÉèÈêÊíÍìÌîÎóÓòÒõÕôÔúÚùÙçÇ& ]/', '',$Texto);
	    $Texto = filter_var($Texto, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH);
	    $Texto = str_replace(array('.',',','-','_',','),' ',$Texto);


	    $Peso=1;
	    if($id_fam=='3071')$Peso=2;//MOBILE SMARTPHONES  Smartphones Android 
	    if($id_fam=='3433')$Peso=2;//MOBILE SMARTPHONES  Smartphones iphones 
	    if($id_fam=='163')$Peso=3;//Seminovos 

	    //echo "id_fam = $id_fam | ";



		$palavras=explode(' ',$Texto);
		$i=0;
		$t=count($palavras);
		while($i<$t)
		{
			$palavra=trim(strtolower($palavras[$i]));
			if($palavra != '')
			{
				$key=$ID_Prod.'_'.base64_encode($palavra);
	            $sql = "INSERT INTO acelerador_pesquisa2 (`Key`,ID_Prod,Cabecalho,STR,Peso,Flag) values ('$key','$ID_Prod','$Cabecalho','$palavra','$Peso','1')";
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
}

function atualiza_autocomplete_produtos()
{
    echo "Atualizar autocomplete...\n";

    global $DB_host,$DB_user,$DB_pass,$DB_name;

    $db = db_opendb_conection($DB_host,$DB_user,$DB_pass);
    // Desativar tudo
    Runquery($db,$GLOBALS['DB_name'],"UPDATE autocomplete_produtos SET Flag = 0");

    $rs = new DataObj;
    $sql = "SELECT ID_Prod,Fam,fam_id,Cabecalho,EAN,Ref,Texto,Preco_Site FROM acelerador_pesquisa";
    $rs->db_open($db, $DB_name, $sql);

    $batch = [];
    $batchSize = 500;

    while(!$rs->EOF())
    {
        $ID_Prod   = (int)$rs->db_data['ID_Prod'];
        $fam_id    = (int)$rs->db_data['fam_id'];
        $Fam       = addslashes(trim($rs->db_data['Fam']));
        $cabecalho = addslashes(trim($rs->db_data['Cabecalho']));
        $PVP = $rs->db_data['Preco_Site'];

        /* ======================
         * 1️⃣ EAN (Peso 5)
         * ====================== */
        $ean = normaliza_texto($rs->db_data['EAN']);
        if ($ean !== '') {
            $palavras = [$ean];
            foreach ($palavras as $p) {
                $len = strlen($p);
                for ($i = 2; $i <= $len && $i <= 20; $i++) {
                    $prefix = substr($p, 0, $i);
                    $batch[] = "('$prefix','$cabecalho','$ID_Prod','$Fam','$fam_id','$PVP',5,1)";
                }
            }
        }

        /* ======================
         * REF (Peso 4)
         * ====================== */
        $ref = normaliza_texto($rs->db_data['Ref']);
        if ($ref !== '') {
            $palavras = [$ref];
            foreach ($palavras as $p) {
                $len = strlen($p);
                for ($i = 2; $i <= $len && $i <= 20; $i++) {
                    $prefix = substr($p, 0, $i);
                    $batch[] = "('$prefix','$cabecalho','$ID_Prod','$Fam','$fam_id','$PVP',4,1)";
                }
            }
        }

        /* ======================
         * NOME (Peso 3)
         * ====================== */
        $nome = normaliza_texto($rs->db_data['Cabecalho']);
        if ($nome !== '') {
            $palavras = array_unique(explode(' ', $nome));
            foreach ($palavras as $p) {
                $len = strlen($p);
                for ($i = 2; $i <= $len && $i <= 20; $i++) {
                    $prefix = substr($p, 0, $i);
                    $batch[] = "('$prefix','$cabecalho','$ID_Prod','$Fam','$fam_id','$PVP',3,1)";
                }
            }
        }

        /* ======================
         * FAMÍLIA (Peso 1)
         * ====================== */
        $fam_txt = normaliza_texto($Fam);
        $palavras = array_unique(explode(' ', $fam_txt));
        foreach ($palavras as $p) {
            if (strlen($p) < 2) continue;
            $len = strlen($p);
            for ($i = 2; $i <= $len && $i <= 20; $i++) {
                $prefix = substr($p, 0, $i);
                $batch[] = "('$prefix','$cabecalho','$ID_Prod','$Fam','$fam_id','$PVP',1,1)";
            }
        }

        /* ======================
         * TEXTO NORMAL (Peso 1)
         * ====================== */
        /*$texto = normaliza_texto($rs->db_data['Texto']);
        $palavras = array_unique(explode(' ', $texto));
        foreach ($palavras as $p)
        {
            if (strlen($p) < 2) continue;
            $len = strlen($p);
            for ($i = 2; $i <= $len && $i <= 20; $i++) {
                $prefix = substr($p, 0, $i);
                $batch[] = "('$prefix','$cabecalho','$ID_Prod','$Fam','$fam_id','$PVP',1,1)";
            }
        }*/

        // INSERT EM LOTE
        if (count($batch) >= $batchSize)
        {
            $sql = "
                INSERT INTO autocomplete_produtos
                (prefix,termo,ID_Prod,Fam,fam_id,PVP,Peso,Flag)
                VALUES ".implode(',', $batch)."
                ON DUPLICATE KEY UPDATE
                    Flag = 1,
                    PVP = GREATEST(PVP, VALUES(PVP)),
                    Peso = GREATEST(Peso, VALUES(Peso))
            ";
            Runquery($db,$GLOBALS['DB_name'],$sql);
            $batch = [];
        }

        $rs->db_next();
    }

    // Último batch
    if (!empty($batch))
    {
        $sql = "
            INSERT INTO autocomplete_produtos
            (prefix,termo,ID_Prod,Fam,fam_id,PVP,Peso,Flag)
            VALUES ".implode(',', $batch)."
            ON DUPLICATE KEY UPDATE
                Flag = 1,
                Peso = GREATEST(Peso, VALUES(Peso))
        ";
        Runquery($db,$GLOBALS['DB_name'],$sql);
    }

    // Limpeza
	Runquery($db,$GLOBALS['DB_name'],"DELETE FROM autocomplete_produtos WHERE Flag = 0");

    $rs->db_close();

    echo "Autocomplete atualizado com sucesso.\n";
}
?>
