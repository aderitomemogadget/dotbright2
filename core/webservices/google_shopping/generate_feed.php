<?php
$includes_path = dirname(__FILE__) . '/../../includes/';
include $includes_path . 'includes.inc.php';


ini_set('display_errors', 0);
ini_set('display_startup_errors',0);
error_reporting(0);


global $erp_Server,$erp_UserName,$erp_db_password,$erp_DBName;
$hConn=OpenConnection($erp_Server,$erp_UserName,$erp_db_password);
$obj=new clsRecordset;
$result2=new clsRecordset;

$flag_save_file=0;


header ( "Expires:0");
header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
header ( "Pragma: no-cache" );
// header ( "Content-type: application/octet-stream; name=nomeDoArquivoXls".".xls");
// header ( "Content-Disposition: attachment; filename=nomeDoArquivoXls".".xls"); 
// header ( "Content-Description: MID Gera excel" );
// header ( "Content-type: text/xml" );
if($flag_save_file==1)
{
	header ( "Content-Disposition: attachment; filename=feed".".xml"); 
	header ( "Content-type: text/xml; name=feed".".xml" );
}
else
{
	// header('Content-Type: text/html');	
	header ( "Content-type: text/xml" );
}


ini_set("max_execution_time","0");
ini_set("max_input_time","60000");
ini_set("memory_limit","-1");


function get_family_tree($id_org)
{
	if($id_org==0) return "";
	$return='';
	global $erp_Server,$erp_UserName,$erp_db_password,$erp_DBName;
	$hConn=OpenConnection($erp_Server,$erp_UserName,$erp_db_password);
	$result2=new clsRecordset;
	while($id_org>0)
	{
		$sql2="SELECT Cabecalho, ID_ORG from produtos_1_familias inner join produtos_1_familias_linguas on produtos_1_familias_linguas.ID_PAI=produtos_1_familias.ID WHERE produtos_1_familias.ID=$id_org";
		// echo $sql2;
		$result2->open($hConn, $erp_DBName, $sql2);
	    // var_dump($result2);
	    if (!$result2->EOF()) 
		{
			$Cabecalho=$result2->aFields['Cabecalho'];
			// $Cabecalho=htmlentities($Cabecalho);
			$Cabecalho=utf8_encode($Cabecalho);
			$return=$Cabecalho.' &gt; '.$return;
			$id_org=$result2->aFields['ID_ORG'];
		}
		else
			$id_org=0;
		// if(strlen($return)>500) $id_org=0;
	}
	$return=substr($return, 0, (strlen($return)-6));
	return $return;
}


$beg_data='';


$beg_data.='<?xml version="1.0"?>
		<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">
			<channel>
				<title>Clickfiel - Loja Online test#1</title>
				<link>http://www.clickfiel.pt</link>
				<description>This is a sample feed...</description>
				';

echo $beg_data;

/*

*/

$sql="SELECT
			*,produtos_1.ID,produtos_1.ID_ORG
		FROM
			produtos_1
			INNER JOIN produtos_1_linguas ON produtos_1_linguas.ID_PAI = produtos_1.ID
			INNER JOIN produtos_1_tabelaspreco ON produtos_1_tabelaspreco.ID_ORG = produtos_1.ID
		WHERE produtos_1.ID=produtos_1_linguas.ID_PAI 
		AND Lang='$lang' 
		AND (Aproved=1 and mostrar_online=1) 
		
		ORDER BY produtos_1.ID DESC 
		";
// echo $sql;
		// AND produtos_1.ID NOT IN (7482) 
		// LIMIT 5000
$hRes=$obj->Open($hConn,$erp_DBName,$sql);

while (!$obj->EOF()) {
	# code...
	// var_dump($obj->aFields);
	if($flag_save_file==0)
	{
		// echo '<br>fields: <pre>'; print_r($obj->aFields); echo '</pre>';
	}

	$ID = $obj->aFields["ID"];
	$c_dest = $obj->aFields["Cabecalho"];
	$ext1 = $obj->aFields["img_ext"];
	$ext2 = $obj->aFields["img2_ext"];
	$ext3 = $obj->aFields["img3_ext"];
	$ext4 = $obj->aFields["img4_ext"];
	$id_org_dest = $obj->aFields["ID_ORG"];
	$ref = $obj->aFields["Ref"];
	$grelha = $obj->aFields["gestao_dimensoes"];
	$peso = $obj->aFields["Peso"];
	$id_marca = $obj->aFields["ID_Marca"];

	if($peso=='') $peso=0;

	if($obj->aFields["Texto3"]!='')
		$description = $obj->aFields["Texto3"];
	elseif($obj->aFields["Texto1"]!='')
		$description = $obj->aFields["Texto1"];
	else
		$description = $obj->aFields["Cabecalho"];
	// $description = '&nbsp:;';
	// $description = str_replace('<br> ', '<br>', $description);
	// $description = str_replace('&nbsp;', '*', $description);
	// $ar_search=array('<', '&', '>', '"', "'", '\l', '\n');
	// $ar_replace=array('&lt;', '&amp;', '&gt;', '&quot;', '&apos;', '<br>', '<br>');
	// str_replace($ar_search, $ar_replace, $description);
	// $description=htmlentities($description);
	// $description=htmlspecialchars($description);
	
	// preg_replace('/[^A-Za-z0-9\-]/', '', $description);
	$description=strip_tags($description);
	$description=utf8_encode($description);

	// $description = iconv("cp1252", "utf-8", $description);
	$description = iconv("utf-8", "ascii//translit", $description);
	$description = str_replace('?', ' ', $description);
	if(strlen($description)>5000) $description=substr($description, 0, 4985).'...';

	// $search_ar=array('’', '•');
	// $replace_ar=array("´", '-');
	// $description=str_replace($search_ar, $replace_ar, $description);

	$description = '<![CDATA['.$description.']]>';
	// $description = strlen($obj->aFields["Texto3"]).$description;
	// if(strlen($description)>5000)
	// 	$description='Too long!!!';

	// url for product page
	$link = caminho_correcto(2, $ID, $c_dest, 'produtos.php?ID='.$ID, $id_org_dest);

	// $c_dest=str_replace('“', '"', $c_dest);
	// $c_dest='--'.$c_dest;
	// $c_dest= htmlspecialchars($c_dest);
	// $c_dest= htmlentities($c_dest);
	$c_dest=utf8_encode($c_dest);
	if(strlen($c_dest)>150) $c_dest=substr($c_dest, 0, 135).'...';
	$c_dest= '<![CDATA['.$c_dest.']]>';

	// get (and process) product images
	$img_file_peq='';
	if($grelha && $ref)
	{
		$imagens = multiple_image_referencias($ref);
		$arr_img3 = $imagens[3][0];
	}
	else
	{
		$imagens = multiple_image($ID);
		$arr_img3 = $imagens[3][0];
	}
	if((!stream_resolve_include_path(ERP_SHELL_URL.'img_upload/produtos_1/'.$arr_img3)) || !$arr_img3) 
		$img_file_peq = SITE_HTTP_URL.'layout/'.PROJETO.'/img/170x170.png';
	else 
		$img_file_peq = ERP_HTTP_URL . 'img_upload/produtos_1/'.$arr_img3;

	// condition
	$condition = 'Novo';

	// get stock availability
	$stock = ''; $stock_titulo = ''; $stk = stock($ID);
	// if($stk == "brevemente")
	// {
	// 	$stock = 'out';
	// 	$stock_titulo = 'out of stock'; //= 'Brevemente Disponivel';
	// }
	// elseif($stk == "esgotado")
	// {
	// 	$stock = 'out';
	// 	$stock_titulo = 'out of stock'; //= 'Esgotado';
	// }
	// elseif(array_search('entrega', $stk)!== false || array_search('entrega2', $stk)!== false)
	// {
	// 	$stock = 'in24';
	// 	$stock_titulo = 'in stock'; //= 'Entrega em 24/48h';
	// }
	// elseif(array_search('stock', $stk)!== false || array_search('stock2', $stk)!== false)
	// {
	// 	$stock = 'in';
	// 	$stock_titulo = 'in stock'; //= 'Entrega imediata';
	// }

	if ($stk != "esgotado" && $stk != "brevemente")
	{
		if (array_search('stock', $stk) !== false || array_search('stock2', $stk) !== false)
		{
			$stock_titulo = 'in stock';
		}
		//elseif (array_search('entrega2', $stk) !== false)
		elseif (array_search('entrega', $stk) !== false)
		{
			// $stock_titulo = 'in stock';
			$stock_titulo = 'out of stock';
		}
	}
	else
	{
		if ($stk == "brevemente")
		{
			$stock_titulo = 'out of stock';
		}
		elseif ($stk == "esgotado")
		{
			$stock_titulo = 'out of stock';
		}
	}

	// APENAS INCLUI NO FEED OS ARTIGOS COM STOCK
	// if($stock_titulo == 'in stock')
	// {

		// get (and process) product price(s)
		$preco_cortado='';
		$ar_precos = devolve_preco($ID);
		$preco = $ar_precos[0];
		$promo = 0;
		if($ar_precos[1] != $ar_precos[0] && $ar_precos[1] > 0)
		{
			$promo = 1;
			$preco_cortado = $ar_precos[1];
			$desconto = ($preco_cortado-$preco);
			$desconto = number_format($desconto,0);
			$preco_cortado = number_format($preco_cortado,2, '.', '');
		}
		$preco = number_format($preco,2, '.', '');

		// get shipping data
		$country='PT';
		$local=5; // Portugal Continental
		$expedicao=3; // Transportadora NACEX
		$modo_pag=21; // Multibanco
		$service='Transportadora NACEX';
		$sql2 = "SELECT * FROM precos_ctt WHERE precos_ctt.PesoMin <= '" . $peso . "' AND precos_ctt.PesoMax >= '" . $peso . "' AND entregaID='" . $local . "' AND expedicaoID='" . $expedicao . "' AND pagamentoID='" . $modo_pag . "'";
		// echo $sql2;
		$result2->open($hConn, $erp_DBName, $sql2);
	    // var_dump($result2);
	    // echo db_error($hConn);
	    if (!$result2->EOF()) 
		{
	        $portes = $result2->aFields["PrecoSemIva"];
			// echo  $portes.'x';
	        $portes2 = $result2->aFields["Preco"];
	        $cobrar = $result2->aFields["PrecoContraEntrega"];
	        // if ($portes_taxas == 1) $cobrar_porte = $cobrar;
	    }
	    $result2->close();
	    if ($preco > 200) {
	        $portes_gratis = $portes;
	        $portes = 0;
	        $portes2 = 0;
	    }
	    $portes2=number_format($portes2,2, '.', '');

	    // get the brand/gtin/mpn
	    // 		<g:gtin>71919219405200</g:gtin>
		// 		<g:brand>LG</g:brand>
		// 		<g:mpn>22LB4510/US</g:mpn>
	    $marca='';
	    if($id_marca>0)
	    {
	    	$sql2="SELECT Descricao FROM outras_tabelas_linguas WHERE ID_PAI='$id_marca' AND Lang='$lang'";
	    	$result2->open($hConn, $erp_DBName, $sql2);
	    	if (!$result2->EOF()) 
	    		$marca_nome=$result2->aFields['Descricao'];
	    }
	    // else
	    // 	$marca=$id_marca;

	    // Product category
	    // <g:google_product_category>Electronics > Video > Televisions > Flat Panel Televisions</g:google_product_category>
	    // <g:product_type>Consumer Electronics &gt; TVs &gt; Flat Panel TVs</g:product_type>
	    $product_type='';
	    $product_type=get_family_tree($id_org_dest);
	    if(strlen($product_type)>750) $product_type=substr($product_type, 0, 735).'...';
	    // var_dump($test); die();
		
		$line='
			<item>
				<g:id>'.$ID.'</g:id>
				<g:title>'.$c_dest.'</g:title>
				<g:description>'.$description.'</g:description>
				<g:link><![CDATA['.$link.']]></g:link>
				<g:image_link><![CDATA['.$img_file_peq.']]></g:image_link>
				<g:condition>'.$condition.'</g:condition>
				<g:availability>'.$stock_titulo.'</g:availability>
				';
		if($preco_cortado!='')
		{
			$line.='<g:price>'.$preco_cortado.' EUR</g:price>
				<g:sale_price>'.$preco.' EUR</g:sale_price>';
		}
		else
		{
			$line.='<g:price>'.$preco.' EUR</g:price>';
		}
		$line.='
				<g:shipping>
					<g:country>'.$country.'</g:country>
					<g:service>'.$service.'</g:service>
					<g:price>'.$portes2.' EUR</g:price>
				</g:shipping>
				
				<!-- 2 of the following 3 attributes are required fot this item according to the Unique Product Identifier Rules -->
				<g:brand>'.$marca_nome.'</g:brand>
				<g:identifier_exists>no</g:identifier_exists>
				
				<!-- The following attributes are not required for this item, but supplying them is recommended -->
				<g:product_type><![CDATA['.$product_type.']]></g:product_type>
			</item>
			';
		echo $line;

	// }

	$obj->next();
}
$obj->close();


$end_data.='
		</channel>
	</rss>
	';


echo $end_data;
?>