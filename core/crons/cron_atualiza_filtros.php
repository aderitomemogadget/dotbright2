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

/*$sql='ALTER TABLE `acelerador_pesquisa` ADD INDEX ( `ID_Prod` )';
if(!Runquery($db_link,$DB_name,$sql)) echo "Data Base ERROR: " . db_error($db_link)."\n";*/

atualiza_catalogo_produtos();

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

	$sql = "UPDATE acelerador_pesquisa set Flag=0";
	if(!Runquery($db_link,$DB_name,$sql)) echo "Data Base ERROR: " . db_error($db_link)."\n";
	$rs = new DataObj;	$rs2 = new DataObj;

	$id_produtos = array();

	$fornecedores_importacao = array();
	$myFile = dirname( __FILE__ )."/../../files/importar/atualizar_pesquisa.txt";
	$fh = fopen($myFile, 'a+');

	$sql = "
			SELECT
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
				products.ID
			ORDER BY
				products.ID

			";


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
			// fwrite($fh, $ll."%\n");
			$ll2 = $ll;

			echo $ll."% \n";
		}

		$id_produto = $rs->db_data["ID"];
		$texto = $cabecalho = addslashes($rs->db_data["Cabecalho"]);
		$id_fam = $rs->db_data["ID_ORG"];
		$preco_site = $rs->db_data["Preco1"];
		$ID_Marca = $rs->db_data["ID_Marca"];
		$ref = $rs->db_data["Ref"];
		$EAN = $rs->db_data["Ref_Ean"];
		$caract = addslashes($rs->db_data["Texto3"]);
		$fornecedor_importacao = $rs->db_data["fornecedor_importacao"];

		$fams = encontraFamiliasRecursiva_x($id_fam);
		if(isset($fams[0])) $fam_id1 = $fams[0]; else $fam_id1 = $id_org;
		if(isset($fams[1])) $fam_id2 = $fams[1]; else $fam_id2 = 0;
		if(isset($fams[2])) $fam_id3 = $fams[2]; else $fam_id3 = 0;
		if(isset($fams[3])) $fam_id4 = $fams[3]; else $fam_id4 = 0;

		$marcax = devolve_marcas($ID_Marca);
		if($marcax) $marcax = ", ".$marcax;
		else $marcax = "";

		$familia = Buscafamilianome($id_fam);
		if($familia) $familia = ", ".$familia;
		else $familia = "";
		/*
		if($caract) $caract=", ".$caract;
		else $caract="";
*/

		$texto.= $marcax.$familia;

		$sql="SELECT ID_Prod FROM acelerador_pesquisa WHERE ID_Prod='$id_produto'";
		$hRes=$rs2->db_open($db_link,$DB_name,$sql);
		if(!$rs2->EOF())
        {
            $sql = "UPDATE acelerador_pesquisa set Ref='$ref',EAN='$EAN', Texto='$texto', Flag=1, Cabecalho='$cabecalho', Preco_Site='$preco_site', fam_id1='$fam_id1', fam_id2='$fam_id2', fam_id3='$fam_id3', fam_id4='$fam_id4', marca_id='$ID_Marca' WHERE ID_Prod=".$id_produto;
            echo "U ";
        }
		else
        {
            $sql = "INSERT INTO acelerador_pesquisa (Ref,EAN,Texto,ID_Prod,Flag,Cabecalho,Preco_Site,fam_id1, fam_id2, fam_id3, fam_id4, marca_id) values ('$ref','$EAN','$texto','$id_produto',1,'$cabecalho','$preco_site','$fam_id1','$fam_id2','$fam_id3','$fam_id4','$ID_Marca')";
            echo "I ";
        }
		$rs2->db_close();

		fwrite($fh, $ll.'% - '.$ref.' - '.$fornecedor_importacao." \n");

		$fornecedores_importacao[$fornecedor_importacao][] = 1;

		//gera_produtos($id_produto, $id_fam, $ID_Marca, 1, 'pt');

		$id_produtos[] = $id_produto;

		if(!Runquery($db_link,$DB_name,$sql)) echo "Data Base ERROR: " . db_error($db_link).$sql."<br>";
		$linha++;
		$rs->db_next();
	}
	$rs->db_close();


	$sql = "delete from acelerador_pesquisa where Flag=0";
	if(!Runquery($db_link,$DB_name,$sql)) echo "Data Base ERROR: " . db_error($db_link)."\n";


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
	// foreach ($fornecedores_importacao as $fonrnecedor)
	// {
		// fwrite($fh, count($fonrnecedor)"100% - eliminou produtos que não estão disponiveis\n");
	// }


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
?>