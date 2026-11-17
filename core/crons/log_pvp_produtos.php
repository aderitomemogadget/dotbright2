<?php
	$_GET ='';
	$_POST ='';
	$_REQUEST ='';

	unset($_GET);
	unset($_POST);
	unset($_REQUEST);

	$path=dirname( __FILE__ ) . '/../';
	require_once $path . "core_files.php";
	header('Content-Type: text/html; charset=utf-8');

	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_shell_url;
	$hConn=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new Dataobj;
	$rec=new Dataobj;
	$rec2=new Dataobj;
	$rec3=new Dataobj;

// $prods=\get_produtc_info(214384,1);
// var_dump($prods);
// die();

function unhtmlentities($string)
{
	$trans_tbl=get_html_translation_table (HTML_ENTITIES);
	$trans_tbl=array_flip ($trans_tbl);
	return strtr ($string, $trans_tbl);
}
function show_breadcrums_recursivo_on($id_pai)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$m_http_url_images,$site_http_url,$tema,$site_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$sql='select pf.ID_ORG,pfl.Cabecalho from products_families pf inner join products_families_lang pfl on (pf.ID=pfl.ID_PAI) where pf.visivel=1 and pfl.Lang="'.$GLOBALS['site_lang'].'" and pf.ID='.$id_pai;
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$str) $str='-';
	else $str='';
	if(!$rs->EOF())
	{
		if($rs->db_data['ID_ORG']==0)$str=htmlentities_memo(stripslashes(($rs->db_data['Cabecalho']))).$str;
		else
		{
			$str=htmlentities_memo(stripslashes($rs->db_data['Cabecalho'])).$str;
			$str=show_breadcrums_recursivo_on($rs->db_data['ID_ORG']).$str;
		}
	}
	$rs->db_close();
	return $str;
}

/* RESTRIÇÃO DE FAMILIAS */
/**
* Vai buscar todas as subfamilias da familia escolhida para um array
**/
function get_familia_recursiva($familia_id, $return = null)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$hConn=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new Dataobj;
	$obj->db_open($hConn, $DB_name, 'SELECT ID FROM products_families WHERE ID_ORG = '.$familia_id);
	while(!$obj->EOF())
	{
		$return[] = $obj->db_data['ID'];
		$temp = get_familia_recursiva($obj->db_data['ID'],$return);
		$obj->db_next();
	}
	$obj->db_close();

	return $return;
}

/* POR ALGUMA RAZÃO O ARRAY_MERGE DO PHP NÃO ESTÁ FUNCIONAR POR ISSO TIVE DE FAZER UM ARRAY_MERGE MANUAL */
function array_merge_custom()
{
	$return = null;
	$arg_list = func_get_args();
	for ($i = 0; $i < count($arg_list); $i++) {
		for($j = 0; $j < count($arg_list[$i]); $j++) {
			$return[] = $arg_list[$i][$j];
		}
	}
	return $return;
}

$familias_restritas = array();
if(count($GLOBALS['kuantokusta_familias_interditas'])) {
	for($i = 0; $i < count($GLOBALS['kuantokusta_familias_interditas']); $i++) {
		$familias_restritas[] = $GLOBALS['kuantokusta_familias_interditas'][$i];
		$temp = get_familia_recursiva($GLOBALS['kuantokusta_familias_interditas'][$i]);
		$familias_restritas = array_merge_custom($familias_restritas, $temp);
	}
}

$sql_restricao = null;
if(count($familias_restritas))$sql_restricao = " AND p.ID_ORG NOT IN (".implode(',', $familias_restritas).") ";



/* END RESTRIÇÃO FAMILIAS */
$sql = 'SELECT DISTINCT p.ID
		FROM
			products p
			INNER JOIN products_families_obj pfo on pfo.ID_PROD=p.ID
			INNER JOIN products_families pf on pf.ID=pfo.ID_ORG
			INNER JOIN products_lang pl ON pl.ID_PAI= p.ID
		WHERE
			p.Aproved=1
			AND pf.visivel=1
			AND pl.Cabecalho <> ""
			AND pl.Lang="pt"
			'.$sql_restricao.'';

$rec->db_open($hConn, $DB_name, $sql);
//$prods_tz = '';
$prods_tz = 'REFERENCIA;EAN;PRECO'."\n";


$myFiletmp=$site_shell_url."files/produtos_tmp_".$dt.".txt";
if(stream_resolve_include_path($myFile)) unlink($myFile);
$fh=fopen($myFiletmp,'w') or die("can't open file");
fwrite($fh,$prods_tz);



while(!$rec->EOF())
{
	$prod_id = $rec->db_data['ID'];

	$prods=\get_produtc_info($prod_id,1);


	if($prods !== NULL)
	{
		echo "*";
		$fam_id = $prods['fam_id'];
		$prod_nome = htmlspecialchars_decode($prods['prod_nome']);
		$prod_foto = $prods['prd_imgm'];
		$ref = $prods['prod_ref'];
		$EAN = $prods['Ref_Ean'];
		$prod_carrinho = $prods['prod_carrinho'];
		$marcax = $prods['prod_marca']['marca_nome'];
		$familia = htmlspecialchars_decode(rtrim(show_breadcrums_recursivo_on($prods['fam_id']), '-'));
		$preco = $prods['prod_precos']['PVP'];
		$t_stock = $prods['prod_precos']['STOCK'];
		$sync = $prods['prod_precos']['Sync'];
		$prod_url = $site_http_url.seo('pages.php?page_id='.$GLOBALS['products_page_id'], $prod_id, 2);


		$prods_tz = $ref.';'.$EAN.';'.$preco."\n";
		fwrite($fh,$prods_tz);
		
	
	}
	$rec->db_next();
}
$rec->db_close();

fclose($fh);

if($myFiletmp)
{
	$dt=date('Y-m-d');
	$dt_ontem=date('Y-m-d', strtotime($dt . "+1 days"));
	$myFile=$site_shell_url."files/log_preco_produtos/produtos_".$dt.".txt";
	if(stream_resolve_include_path($myFile)) unlink($myFile);

	copy($myFiletmp,$myFile);

	/*$myFilegz=$site_shell_url."files/kuantokusta.tar.gz";
	if(stream_resolve_include_path($myFilegz)) unlink($stream_resolve_include_path);*/

	$myFile_ontem=$site_shell_url."log_preco_produtos/produtos_".$dt_ontem.".txt";
	/*$fh=fopen($myFile,'w') or die("can't open file");
	$stringData=$prods_tz;
	fwrite($fh, $stringData);
	fclose($fh);*/
	if(stream_resolve_include_path($myFile)) exec('cd '.$site_shell_url.'files/; tar -cf kuantokusta.tar.gz kk'.$dt.'.txt');
	if(stream_resolve_include_path($myFile_ontem)) unlink($myFile_ontem);
}
echo "FIM";

?>