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


	/*$path1=dirname( __FILE__ ).'/conf/';
	$path2=dirname( __FILE__ ).'/core/';

	include $path1.'conf.php';

	require_once $path2.'db_engine.php';
	require_once $path2.'email.php';
	require_once $path2.'settings_modulos.php';
	require_once $path2.'sessions_engine.php';
	require_once $path2.'global.php';
	require_once $path2.'admin_engine.php';

	// MODULOS de LOJA
	require_once $path2.'garantia.php';
	require_once $path2.'servicos_extra.php';
	require_once $path2.'portes.php';
	require_once $path2.'portes_extra.php';

	require_once $path2.'media.php';
	require_once $path2.'seo.php';
	require_once $path2.'linguas.php';
	require_once $path2.'gd.php';
	require_once $path2.'produtos.php';
	require_once $path2.'promocoes.php';
	require_once $path2.'filtros.php';
	require_once $path2.'servicos_extra.php';

	$site_lang='pt';*/



	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_shell_url;
	$hConn=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new Dataobj;
	$rec=new Dataobj;
	$rec2=new Dataobj;
	$rec3=new Dataobj;

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
	$sql='select pf.ID, pf.ID_ORG, pfl.Cabecalho from products_families pf inner join products_families_lang pfl on (pf.ID=pfl.ID_PAI) where pf.visivel=1  and pfl.Lang="'.$GLOBALS['site_lang'].'" and pf.ID='.$id_pai;
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




$sql = '
SELECT DISTINCT
p.ID
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
AND p.Sync = 0
AND p.udtime < CURRENT_TIMESTAMP - INTERVAL "24" HOUR
';






$rec->db_open($hConn, $DB_name, $sql);
while(!$rec->EOF())
{
	$prod_id = $rec->db_data['ID'];
	$prods = get_produtc_info($prod_id);
	if($prods !== NULL)
	{
		$fam_id = $prods['fam_id'];
		$prod_nome = htmlspecialchars_decode($prods['prod_nome']);
		$prod_foto = $prods['prd_imgm'];
		$ref = $prods['prod_ref'];
		$EAN = $prods['Ref_Ean'];
		$prod_carrinho = $prods['prod_carrinho'];
		$marcax = $prods['prod_marca']['marca_nome'];
		$familia = rtrim(show_breadcrums_recursivo_on($prods['fam_id']), '-');
		$preco = $prods['prod_precos']['PVP'];
		$t_stock = $prods['prod_precos']['STOCK'];
		$prod_url = $site_http_url.seo('pages.php?page_id='.$GLOBALS['products_page_id'], $prod_id, 2);
		// $peso_total = $prods['prod_peso'];

		$prods_tz .= $ref.';'.$EAN.';'.$prod_nome.' '.$res[$a]['cat_nome'].' '.$res[$a]['valor_nome']."\n";

	}
	// $sql2 = 'UPDATE products SET Aproved = 0 WHERE ID='$prod_id;
	// echo $sql2;
	// $rec2->db_open($hConn, $DB_name, $sql2);
	// $rec2->db_close();
	$rec->db_next();
}
$rec->db_close();

if($prods_tz)
{
	$dt=date('Y-m-d');
	$dt_ontem=date('Y-m-d', strtotime($dt . "+1 days"));
	$myFile=$site_shell_url."files/artigos_desativados".$dt.".txt";
	if(stream_resolve_include_path($myFile)) unlink($myFile);
	$fh=fopen($myFile,'w') or die("can't open file");
	$stringData=$prods_tz;
	fwrite($fh, $stringData);
	fclose($fh);

	$attach[0]=$myFile;
	$attach[1]='artigos_desativados'.$dt.'.txt';
	send_email($GLOBALS['Default_reports_email'],'PRODUTOS DESATIVADOS - MHR','LISTA DE PRODUTOS DESATIVADOS EM '.$dt,'',1,$attach);

}

?>