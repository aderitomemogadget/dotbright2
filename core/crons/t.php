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


atualiza_acelerador_pesquisa2();



function atualiza_acelerador_pesquisa2()
{
	echo "atualiza_catalogo_produtos";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$rs = new DataObj;
	$rs2 = new DataObj;

	$id_produtos = array();
	$sql = "SELECT * FROM acelerador_pesquisa WHERE ID_Prod = '96400'";
	echo "selecting products";
	$rs->db_open( $db_link,$DB_name,$sql);
	$total_produtos = $rs->db_rows;
	$linha = 1;
	$ll = '';
	$ll2 = '';
	echo "start work";
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

		echo "\n\n";

		echo $Texto;
		echo "\n\n";	


        $Texto = mb_convert_encoding($Texto, "UTF-8");
	    $Texto = preg_replace('/[^a-zA-Z0-9_\.\,\-áÁàÀãÃâÂéÉèÈêÊíÍìÌîÎóÓòÒõÕôÔúÚùÙçÇ& ]/', '',$Texto);
	    $Texto = filter_var($Texto, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH);
	    $Texto = str_replace(array('.',',','-','_',','),' ',$Texto);

		echo $Texto;
		echo "\n\n";	

		$palavras=explode(' ',$Texto);
		$i=0;
		$t=count($palavras);
		while($i<$t)
		{
			$palavra=trim(strtolower($palavras[$i]));
			if($palavra != '')
			{
				$key=$ID_Prod.'_'.base64_encode($palavra);
	            $sql = "INSERT INTO acelerador_pesquisa2 (`Key`,ID_Prod,Cabecalho,STR,Peso,Flag) values ('$key','$ID_Prod','$Cabecalho','$palavra','1','1')";

	            echo $sql."\n";
			}
			$i++;
		}

		$linha++;
		$rs->db_next();
	}
	$rs->db_close(1);
}
?>