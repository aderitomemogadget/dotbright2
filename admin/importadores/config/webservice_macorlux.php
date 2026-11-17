<?php

if(isset($argv) && $argv[1] > 0)$Supplier_ID=intval($argv[1]);
if(isset($_GET['Supplier_ID']))$Supplier_ID=intval($_GET['Supplier_ID']);

$Supplier_ID=2;
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////// ATENÇÃO, NOVO SCRIPT, FAZER BACKUP ANTES DE MODIFICAR ///////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////

// $id_revendedor é configurado no conf.php


	$path = dirname(__FILE__).'/../../../core/';
	//Incluir apenas as librarias necessarias
	require_once $path.'../conf/conf.php';
	require_once $path.'seguranca.php';
	require_once $path.DB_ENGINE;
	require_once $path.'sessions_engine.php';
	require_once $path.'global.php';
	require_once $path.'admin_engine.php';
	require_once $path.'seo.php';
	require_once $path.'gd.php';
	require_once $path.'email.php';
	require_once $path.'produtos.php';
	require_once $path.'filtros.php';
	require_once $path.'return_filtros.php';

	//incluir as funcoes dos importadores
	$path2 = dirname( __FILE__ ).'/../../../';
	include $path2.'admin/importadores/config/settings_crons.inc.php';
	include $path2.'admin/importadores/config/funcoes.inc.php';
	include $path2.'admin/products/filtros/funcoes.inc.php';


$DEBUG=0;

$DEBUG_REF='091.L.2216FL';

//****************************************************    ALGUMAS REGRAS:
//
//
//
/*
$familia_default=0;
$campo_chave_ean=0;
$cria_filtros=1;

$cria_familias=0;
$cria_produtos=1;
$cria_marcas=1;
$importar_imagens=1;
$importar_precos=1;
$importar_relacionados=1;
$campo_chave_ref=1;
$muda_prod_familia=1;
$importar_etiquetas=1;
$importar_fichaeua=1;
$importar_desenhos=1;
$importar_pvp_usado=0;
$tirar_iva=1;
$atualiza_descricoes=1;*/

$familia_default=$GLOBALS['m_familia_default'];
$campo_chave_ean=$GLOBALS['m_campo_chave_ean'];
$cria_filtros=$GLOBALS['m_cria_filtros'];
$cria_familias=$GLOBALS['m_cria_familias'];
$cria_produtos=$GLOBALS['m_cria_produtos'];
$cria_marcas=$GLOBALS['m_cria_marcas'];
$importar_imagens=$GLOBALS['m_importar_imagens'];
$importar_precos=$GLOBALS['m_importar_precos'];
$importar_relacionados=$GLOBALS['m_importar_relacionados'];
$campo_chave_ref=$GLOBALS['m_campo_chave_ref'];
$muda_prod_familia=$GLOBALS['m_muda_prod_familia'];
$m_id_prod_sem_familia=$GLOBALS['m_id_prod_sem_familia'];
$importar_etiquetas=$GLOBALS['m_importar_etiquetas'];
$importar_fichaeua=$GLOBALS['m_importar_fichaeua'];
$importar_desenhos=$GLOBALS['m_importar_desenhos'];
$importar_pvp_usado=$GLOBALS['m_importar_pvp_usado'];
$tirar_iva=$GLOBALS['m_tirar_iva'];
$atualiza_descricoes=$GLOBALS['m_atualiza_descricoes'];
$ignorar_familias=$GLOBALS['m_ignorar_familias'];
$apenas_macorlux=$GLOBALS['m_apenas_macorlux'];


echo "*";
$obj = getImportadorConfiguracoes_2($Supplier_ID);
if(!$obj->EOF())
{
	$Margem = $obj->db_data['Margem'];
}
$obj->db_close();

//****************************************************


if(!isset($_GET['log'])) $log = 0;
error_reporting(E_ERROR);
ini_set('display_errors',-1);

header('Content-Type: text/html; charset=utf-8');
ini_set('max_execution_time','60000');
ini_set('max_input_time','60000');
ini_set('memory_limit','-1');

// LER DO SETTINGS O ID DE CLIENTE NO SITE DA MACORLUX
global $id_revendedor;
$familias_restritas = array();
if(count($GLOBALS['macorlux_familias_interditas'])) {
	for($i = 0; $i < count($GLOBALS['macorlux_familias_interditas']); $i++) {
		$familias_restritas[] = $GLOBALS['macorlux_familias_interditas'][$i];
		$temp = get_familia_recursiva($GLOBALS['macorlux_familias_interditas'][$i]);
		$familias_restritas = array_merge_custom($familias_restritas, $temp);
	}
}
//*****************************************************************************




$filename=$path2 . 'files/sync_'.date('Ymd').'_'.$id_revendedor.'.txt';
$filename_del=$path2 . 'files/sync_'.date('Ymd').'_'.$id_revendedor.'.txt';
$filename_importado=$path2 . 'files/importado/sync_'.date('Ymd').'_'.$id_revendedor.'.txt';
$fam_id_temp = 1;


$myFile = dirname( __FILE__ )."/../../../files/importar/analisar_csv".$Supplier_ID.".txt";

$fh = fopen($myFile, 'a+');

// if(file_exists($filename_importado)) exit; // CASO JÁ TENHA IMPORTADO HOJE MORRE AQUI







// SE AINDA NÃO EXISTIR, FAZ DOWNLOAD DO FICHEIRO DA MACORLUX
echo "FASE 1<br>\n";
if(!file_exists($filename))
{

	if($json=file_get_contents('http://www.macorlux.pt/img_upload/cache/json_'.date('Ymd').'_'.$id_revendedor.'.txt'))
	{
		echo "1.2 A Fazer download do json ".'json_'.date('Ymd').'_'.$id_revendedor.".txt<br>\n";

		fwrite($fh, "1.2 A Fazer download do json ".'json_'.date('Ymd').'_'.$id_revendedor.".txt\n");

		if(substr_count($json, '99999999999999999999'))
		{
			fwrite($fh, "1.3 O ficheiro esta completo, vamos coloca-lo na pasta\n");
			echo "1.3 O ficheiro esta completo, vamos coloca-lo na pasta<br>\n";
			file_put_contents($filename, str_replace('99999999999999999999', '',$json));
		}
		else
		{
			fwrite($fh, "1.3 O ficheiro: http://www.macorlux.pt/img_upload/cache/json_".date('Ymd')."_".$id_revendedor.".txt ainda não acabou de ser criado na Macorlux!\n");
			fwrite($fh,$json);
			var_dump($json);
			fclose($fh);
			unlink($myFile);
			die('1.3 O ficheiro ainda não acabou de ser criado na Macorlux!');
		}
	}
	else
	{
		fwrite($fh,"1.1 A Criar na macorlux json ".'http://www.macorlux.pt/img_upload/cache/json_'.date('Ymd').'_'.$id_revendedor.".txt\n");
		echo "1.1 A Criar na macorlux json ".'http://www.macorlux.pt/img_upload/cache/json_'.date('Ymd').'_'.$id_revendedor.".txt<br>\n";
		echo "http://www.macorlux.pt/webservice_freeplace_novo.php?small=1&user=memogadget&pass=domesticos&id_revendedor=$id_revendedor&exec=1<br>\n";
		echo file_get_contents('http://www.macorlux.pt/webservice_freeplace_novo.php?small=1&user=memogadget&pass=domesticos&id_revendedor='.$id_revendedor.'&exec=1');
		fclose($fh);
		unlink($myFile);
		exit;
	}
}
else
{
	fwrite($fh,"1.4 A importar o ficheiro $filename\n");

	echo "1.4 A importar o ficheiro $filename<br>\n";
	$json=file_get_contents($filename);
}

//****************************************************
fwrite($fh, "Fase 2, comecar analise...\n");
//****************************************************





/*if(!file_exists($filename))
 {
	// send_email('aderito@memogadget.com','webservice neocasa es','https://www.on4tech.pt/files/sync_'.date('Ymd').'_'.$id_revendedor.'.txt');
	if($json=file_get_contents('https://www.on4tech.pt/files/sync_'.date('Ymd').'_'.$id_revendedor.'.txt'))
	{
		ob_flush();flush(); echo "1.2 A Fazer download do json ".'sync_'.date('Ymd').'_'.$id_revendedor.".txt\n";
		if(substr_count($json, '99999999999999999999'))
		{
			ob_flush();flush(); echo "1.3 O ficheiro esta completo, vamos coloca-lo na pasta\n";
			file_put_contents($filename, str_replace('99999999999999999999', '',$json));
		}
		else die('1.3 O ficheiro não acabou de ser criado!');
	}
	else
	{
		ob_flush();flush(); echo "1.1 A Criar na neocasa sync json ".'https://www.on4tech.pt/files/sync_'.date('Ymd').'_'.$id_revendedor.".txt\n";
		ob_flush();flush(); echo "https://www.on4tech.pt/feeds/sync_loja.php?id_revendedor=$id_revendedor&exec=1\n";
		ob_flush();flush(); echo file_get_contents('https://www.on4tech.pt/feeds/sync_loja.php?id_revendedor='.$id_revendedor.'&exec=1');
		exit;
	}
}
else
{
	ob_flush();flush(); echo "1.4 A importar o ficheiro $filename\n";
	$json=file_get_contents($filename);

} */







// send_email('aderito@memogadget.com','webservice ~macorlux','comecei a importar');
$json = str_replace('99999999999999999999', '',$json);
$json = json_decode($json, true);
$cont = 0; $cont2 = 0;
$rs = new DataObj; $rs2 = new DataObj; $rs3 = new DataObj;
global $DB_host,$DB_user,$DB_pass,$DB_name;
$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);



$sql = 'INSERT INTO a_importador_stats (ID_ORG,DataStart) values ("'.$Supplier_ID.'","'.date('Y-m-d H:i:s').'")';
$res = Runquery($db_link,$DB_name,$sql);
$a_importador_stats_id=db_get_last_insert_id($db_link);




//****************************************************
// VAMOS VERIFICAR SE OS CAMPOS EXISTEM TODOS NA BASE DE DADOS
// echo $DB_name,'-products_images-',$db_link."...\n";
$fields = db_list_fields($DB_name,'products',$db_link);
$columns = db_num_fields($fields);
for($i=0; $i < $columns; $i++){$field_array[] = db_field_name($fields,$i);}
if(!in_array('Ref_secundaria',$field_array))$res = Runquery($db_link,$DB_name,'ALTER TABLE products ADD Ref_secundaria Varchar(255)');
if(!in_array('Ref_Ean',$field_array))$res = Runquery($db_link,$DB_name,'ALTER TABLE products ADD Ref_Ean Varchar(255)');
if(!in_array('u_coretiq',$field_array))$res = Runquery($db_link,$DB_name,'ALTER TABLE products ADD u_coretiq Varchar(255)');
if(!in_array('u_catetiq',$field_array))$res = Runquery($db_link,$DB_name,'ALTER TABLE products ADD u_catetiq Varchar(255)');
if(!in_array('u_imagetiq',$field_array))$res = Runquery($db_link,$DB_name,'ALTER TABLE products ADD u_imagetiq Varchar(255)');
if(!in_array('Supplier_ID',$field_array))$res = Runquery($db_link,$DB_name,'ALTER TABLE products ADD Supplier_ID INT(11)');



$fields = db_list_fields($DB_name,'products_images',$db_link);
$columns = db_num_fields($fields);
for($i=0; $i < $columns; $i++){$field_array[] = db_field_name($fields,$i);}
if(!in_array('URL_Source',$field_array))$res = Runquery($db_link,$DB_name,'ALTER TABLE products_images ADD URL_Source Varchar(255)');

$fields = db_list_fields($DB_name,'menus_filtros',$db_link);
$columns = db_num_fields($fields);
for($i=0; $i < $columns; $i++){$field_array[] = db_field_name($fields,$i);}
if(!in_array('menu_data',$field_array))$res = Runquery($db_link,$DB_name,'ALTER TABLE menus_filtros ADD menu_data DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\'');
if(!in_array('ID_Integracao',$field_array))$res = Runquery($db_link,$DB_name,'ALTER TABLE menus_filtros ADD ID_Integracao INT(11)');

$fields = db_list_fields($DB_name,'products_families',$db_link);
$columns = db_num_fields($fields);
for($i=0; $i < $columns; $i++){$field_array[] = db_field_name($fields,$i);}
if(!in_array('FLAG',$field_array))$res = Runquery($db_link,$DB_name,'ALTER TABLE products_families ADD `FLAG` TINYINT(1) NOT NULL ;');
if(!in_array('data_atualizacao',$field_array))$res = Runquery($db_link,$DB_name,'ALTER TABLE products_families ADD data_atualizacao DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\'');

$fields = db_list_fields($DB_name,'brands',$db_link);
$columns = db_num_fields($fields);
for($i=0; $i < $columns; $i++){$field_array[] = db_field_name($fields,$i);}
if(!in_array('FLAG',$field_array))$res = Runquery($db_link,$DB_name,'ALTER TABLE brands ADD `FLAG` TINYINT(1) NOT NULL ;');
if(!in_array('data_atualizacao',$field_array))$res = Runquery($db_link,$DB_name,'ALTER TABLE brands ADD data_atualizacao DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\'');
//****************************************************


$fields = db_list_fields($DB_name,'products_objs_history',$db_link);
$columns = db_num_fields($fields);
for($i=0; $i < $columns; $i++){$field_array[] = db_field_name($fields,$i);}
if(!in_array('Flag',$field_array))$res = Runquery($db_link,$DB_name,'ALTER TABLE products_objs_history ADD `Flag` TINYINT(1) NOT NULL ;');




//**********************************************************************************************************************/
// PARA TORNAR A IMPORTAÇÃO MAIS RAPIDA, VAMOS CARREGAR PARA UM ARRAY TODOS OS PRODUTOS QUE ESTAO EM BASE DE DADOS
$rs->db_open($db_link,$DB_name, 'SELECT ID,ID_ORG,ID_Integracao,Ref,Ref_Ean,preco_revenoff,Sync FROM products');
while(!$rs->EOF())
{

	if($campo_chave_ean==1)
	{
		//Caso o artigo nao tenha ean mas tenha referencia, vamos ler antes esse campo
		if($rs->db_data['Ref_Ean']!='')$prod_refs[$rs->db_data['Ref_Ean']] = $rs->db_data['ID'];
		else $prod_refs[$rs->db_data['Ref']] = $rs->db_data['ID'];
	}
	else 
	{
		if($rs->db_data['Ref']!='')$prod_refs[$rs->db_data['Ref']] = $rs->db_data['ID'];
		else $prod_refs[$rs->db_data['Ref_Ean']] = $rs->db_data['ID'];
	}


	$prod_dados[$rs->db_data['ID']][0] = $rs->db_data['ID_ORG'];
	$prod_dados[$rs->db_data['ID']][1] = $rs->db_data['preco_revenoff'];
	$prod_dados[$rs->db_data['ID']][2] = $rs->db_data['ID_Integracao'];
	$prod_dados[$rs->db_data['ID']][3] = $rs->db_data['Sync'];
	$rs->db_next();
}
$rs->db_close();

echo "1.3 Produtos carregados para a memoria<br>\n";

// PARA TORNAR A IMPORTAÇÃO MAIS RAPIDA, VAMOS CARREGAR PARA UM ARRAY TODOS OS PRODUTOS QUE ESTAO EM BASE DE DADOS.
$rs->db_open($db_link,$DB_name, 'SELECT ID,ID_Integracao FROM products_families');
while(!$rs->EOF())
{
	$fams_dados[$rs->db_data['ID_Integracao']] = $rs->db_data['ID'];
	$fams_dados_preco[$rs->db_data['ID']] = $rs->db_data['preco_revenoff'];
	$rs->db_next();
}
$rs->db_close();

echo "1.4 Familias carregadas para a memoria<br>\n";

// PARA TORNAR A IMPORTAÇÃO MAIS RAPIDA, VAMOS CARREGAR PARA UM ARRAY TODOS AS MARCAS QUE ESTAO EM BASE DE DADOS
$rs->db_open($db_link,$DB_name,'SELECT ID, ID_Integracao FROM brands');
while(!$rs->EOF())
{
	$prod_Marcas_dados[$rs->db_data['ID_Integracao']] = $rs->db_data['ID'];
	$prod_Marca_dados_preco[$rs->db_data['ID']] = $rs->db_data['preco_revenoff'];
	$rs->db_next();
}
$rs->db_close();

echo "1.5 Marcas carregadas para a memoria<br>\n";

echo "*** A verificar integridade das Imagens<br>\n";
// PARA TORNAR A IMPORTAÇÃO MAIS RAPIDA, VAMOS CARREGAR PARA UM ARRAY TODAS AS PRIMEIRAS IMAGENS QUE ESTAO EM BASE DE DADOS
$rs->db_open($db_link,$DB_name, 'SELECT ID_ORG,img_ext FROM products_images group by ID_ORG');
while(!$rs->EOF())
{
	$img = $site_shell_url.'files/products/'.$rs->db_data['ID_ORG'].'_1.'.$rs->db_data['img_ext'];
	//Tive de comentar o codigo porque demora muito tempo e depois a importacao dá timeout
	if(file_exists($img))
	{
		$imagens_dados[$rs->db_data['ID_ORG']] = 1;
		/*if(filesize($img) > 0 ) $imagens_dados[$rs->db_data['ID_ORG']] = 1;
		else unlink($img);*/
	}
	$rs->db_next();
}
$rs->db_close();

echo "1.6 Imagens carregadas para a memoria<br>\n";

// PRECOS
$rs->db_open($db_link,$DB_name,'SELECT obj_pvp,obj_type_id FROM products_objs');
while(!$rs->EOF())
{
	$precos_dados[$rs->db_data['obj_type_id']]=$rs->db_data['obj_pvp'];
	$rs->db_next();
}
$rs->db_close();

echo "1.7 Precos carregadas para a memoria<br>\n";

//**********************************************************************************************************************/
















//********************************************************************************************************
//**************************************************** GRAVAR LOG COM DATA DE INICIO DA IMPORTACAO
//********************************************************************************************************
$sql = 'INSERT INTO z_integracao_api(DATA_INI, DATA_FIM, FICHEIRO, IMPORTADO) VALUES("'.date('Y-m-d H:i:s').'", "0000-00-00 00:00:00", "'.$filename.'", 0)';
$res = Runquery($db_link,$DB_name,$sql);

// VAMOS COLOCAR A FLAG DE TODOS A ZERO E SÓ FINAL VAMOS COLOCAR INATIVOS AQUELES CUJO A FLAG CONTINUAR A 0, APENAS PRODUTOS DE INTEGRAÇÃO É QUE SERÃO AFETADOS

if($apenas_macorlux==1)$res = Runquery($db_link,$DB_name, 'UPDATE products SET Flag=0 WHERE Sync=0');

/*if($cria_produtos == 1)$res = Runquery($db_link,$DB_name, 'UPDATE products SET Flag=0 WHERE ID_Integracao > 0 AND	Sync=0');
if($cria_filtros == 1)$res = Runquery($db_link,$DB_name, 'UPDATE filters_categories SET category_flag=0 AND category_id_import > 0');
if($cria_filtros == 1)$res = Runquery($db_link,$DB_name, 'UPDATE filters_values SET value_flag=0 WHERE value_id_import > 0');
if($cria_filtros == 1)$res = Runquery($db_link,$DB_name, 'UPDATE filters_products SET FLAG=0 WHERE data_atualizacao != "0000-00-00 00:00:00"');
if($cria_familias == 1)$res = Runquery($db_link,$DB_name, 'UPDATE products_families SET FLAG=0 WHERE data_atualizacao != "0000-00-00 00:00:00" AND ID_Integracao > 0');
if($cria_marcas ==1)$res = Runquery($db_link,$DB_name, 'UPDATE brands SET FLAG=0 WHERE data_atualizacao != "0000-00-00 00:00:00" AND ID_Integracao > 0');
*/

//$res = Runquery($db_link,$DB_name, 'UPDATE menus_filtros SET menu_flag=0 WHERE menu_data != "0000-00-00 00:00:00" AND ID_Integracao > 0');





/*$myFile = dirname( __FILE__ )."/../../files/importar/atualizar_sync.txt";
$fh = fopen($myFile, 'a+');
fwrite($fh,$filename."\n");
chmod($myFile, 0755);*/

$email_txt = '';
global $iva_default;
$filtros_rel2 = '';
$xz = 0;
// echo '++++'.count($json);
// exit;
$small=1;
$linha = 1;



//********************************************************************************************************
//**************** PARA CADA ARTIGO
//********************************************************************************************************
//Flush (send) the output buffer and turn off output buffering
//ob_end_flush();
while (@ob_end_flush());
// Implicitly flush the buffer(s)
ini_set('implicit_flush', true);
ob_implicit_flush(true);


$xx=0;

$l=0;
foreach($json as $produtos)
{
	$l++;
	if($l==1) //Quero que corra apenas 1 vez
	{
		$t=count($produtos);
		if($t>0)
		{

			$sql = 'UPDATE a_importador_stats set TotalProds = "'.$t.'" WHERE ID = '.$a_importador_stats_id;
			$res = Runquery($db_link,$DB_name,$sql);

			foreach($produtos as $prod)
			{
				ob_flush();
				flush();


				//if($xx>10)die("FIM");


				//if(!$db_link) $db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

				$ll = intval(($linha * 100 ) / $t);
				if($ll != $ll2)
				{
					$ll2 = $ll;
				}
				if(!file_exists($myFile)) die("parou - fx nao existe $myFile");

				// INICIALIZA VARIAVEIS
				$preco_revenoff_marca = 0;
				$preco_revenoff_fam = 0;
				$preco_revenoff_prod = 0;
				$prod_id = 0;
				$outlet = 0;
				$val_p2 = 0;
				$news_l = 0;
				$val_promo = 0;
				$Ref_Ean = '';

				if($small==1)
				{
					$prod_ID=					$prod['1'];
			 		$prod_Referencia=			trim($prod['2']);
			 		$prod_Nome=					$prod['3'];
			 		$prod_Marca=				$prod['4'];
			 		$prod_Descricao=			$prod['5'];
			 		$prod_Sugestoes=			$prod['6'];
			 		$prod_Relacionados=			$prod['7'];
			 		$prod_Familia=				$prod['8'];
			 		$prod_SubFamilia=			$prod['9'];
			 		// $prod_Stock=				$prod['10'];
			 		$prod_Stock=				$prod['30'];
			 		$prod_Destaque=				$prod['11'];
			 		$prod_Novidade=				$prod['12'];
			 		$prod_Data=					$prod['13'];
			 		$prod_Imagem=				$prod['14'];
			 		$prod_ImagemTime=			$prod['15'];
			 		$prod_Preco=				$prod['16'];
			 		$prod_Preco_Promo=			$prod['17'];
			 		$prod_Preco_Original=		$prod['18'];
			 		$prod_Caracteristicas=		$prod['19'];
			 		$prod_ProdAss=				$prod['20'];
			 		$prod_QtdMinEnc=			$prod['21'];
			 		$prod_Ecotaxas=				$prod['22'];
			 		$prod_Peso=					$prod['23'];
			 		$prod_Video=				$prod['24'];
			 		$prod_EtiquetaEnergia=		$prod['25'];
			 		$prod_CorEtiquetaEnergia =	$prod['26'];
					$prod_FichaUE=				$prod['27'];
			 		$prod_DesenhosTecnicos=		$prod['28'];
			 		$prod_letra_energetica=		$prod['29'];
			 		$Ref_Ean = 					trim($prod['31']);

			 		$prod_Imagem2=				$prod['32'];$prod_ImagemTime2=			$prod['38'];
			 		$prod_Imagem3=				$prod['33'];$prod_ImagemTime3=			$prod['39'];
			 		$prod_Imagem4=				$prod['34'];$prod_ImagemTime4=			$prod['40'];
			 		$prod_Imagem5=				$prod['35'];$prod_ImagemTime5=			$prod['41'];
			 		$prod_Imagem6=				$prod['36'];$prod_ImagemTime6=			$prod['42'];
			 		$prod_Imagem7=				$prod['37'];$prod_ImagemTime7=			$prod['43'];

			 		$prod_EtiquetaNova=			$prod['44'];
			 	}
				else
				{
					$prod_ID=					$prod['ID'];
			 		$prod_Referencia=			trim($prod['Referencia']);
			 		$prod_Nome=					$prod['Nome'];
			 		$prod_Marca=				$prod['Marca'];
			 		$prod_Descricao=			$prod['Descricao'];
			 		$prod_Sugestoes=			$prod['Sugestoes'];
			 		$prod_Relacionados=			$prod['Relacionados'];
			 		$prod_Familia=				$prod['Familia'];
			 		$prod_SubFamilia=			$prod['SubFamilia'];
			 		// $prod_Stock=				$prod['Stock'];
			 		$prod_Stock=				$prod['Stock_Novo'];
			 		$prod_Destaque=				$prod['Destaque'];
			 		$prod_Novidade=				$prod['Novidade'];
			 		$prod_Data=					$prod['Data'];
			 		$prod_Imagem=				$prod['Imagem'];
			 		$prod_ImagemTime=			$prod['ImagemTime'];
			 		$prod_Preco=				$prod['Preco'];
			 		$prod_Preco_Promo=			$prod['Preco_Promo'];
			 		$prod_Preco_Original=		$prod['Preco_Original2'];
			 		$prod_Caracteristicas=		$prod['Caracteristicas'];
			 		$prod_ProdAss=				$prod['ProdAss'];
			 		$prod_QtdMinEnc=			$prod['QtdMinEnc'];
			 		$prod_Ecotaxas=				$prod['Ecotaxas'];
			 		$prod_Peso=					$prod['Peso'];
			 		$prod_Video=				$prod['Video'];
			 		$prod_EtiquetaEnergia=		$prod['EtiquetaEnergia'];
			 		$prod_CorEtiquetaEnergia =	$prod['CorEtiquetaEnergia'];
					$prod_FichaUE=				$prod['FichaUE'];
			 		$prod_DesenhosTecnicos=		$prod['DesenhosTecnicos'];
					$prod_letra_energetica=		$prod['letra_energetica'];
					$Ref_Ean = 					trim($prod['Ean']);

					$prod_Imagem2=				$prod['Imagem2'];$prod_ImagemTime2=			$prod['ImagemTime2'];
					$prod_Imagem3=				$prod['Imagem3'];$prod_ImagemTime3=			$prod['ImagemTime3'];
					$prod_Imagem4=				$prod['Imagem4'];$prod_ImagemTime4=			$prod['ImagemTime4'];
					$prod_Imagem5=				$prod['Imagem5'];$prod_ImagemTime5=			$prod['ImagemTime5'];
					$prod_Imagem6=				$prod['Imagem6'];$prod_ImagemTime6=			$prod['ImagemTime6'];
					$prod_Imagem7=				$prod['Imagem7'];$prod_ImagemTime7=			$prod['ImagemTime7'];

			 		$prod_EtiquetaNova=			$prod['EtiquetaNova'];
			 	}

			 	if($importar_pvp_usado == 1)
			 	{
			 		$prod_Preco =  $prod_Preco_Original;
			 		if($tirar_iva==1)$prod_Preco=$prod_Preco/1.23;
			 	}
			 	if($prod_Stock==0)$prod_Stock=1;//se vier 0 da macorlux quer dizer que eles ainda vendem o artigo

				echo ' | '.$t;
				$t--;

				//echo '|'.$prod_Referencia.' ';


				if($Margem != '')
				{
					$prod_Preco2=$prod_Preco;
					$prod_Preco = $prod_Preco * (1+($Margem / 100));
					fwrite($fh, "Faltam $t artigos para terminar - REF: $prod_Referencia - EAN: $Ref_Ean - PC: $prod_Preco2, Aplica margem e novo preço: $prod_Preco \n");
				}
				else fwrite($fh, "Faltam $t artigos para terminar - REF: $prod_Referencia - EAN: $Ref_Ean - PC: $prod_Preco \n");
				//****************************************************

				/*fwrite($fh,$prod_Referencia."|");
				if(!file_exists($myFile)) die("FIM");*/

		 		$prod_Nome=addslashes(trim($prod_Nome));



				//********************************************************************************************************
				// VERIFICA SE JÁ EXISTE A REFERÊNCIA NA BD, O PRODUTO
				//if($produto_id = $prod_refs[$prod_Referencia]) // O PRODUTO JA EXISTE

		 		$importar=1;
				if(array($familias_restritas) && count($familias_restritas)>0)
				{
					if($prod_Familia['ID'] > 0)
					{
						if(in_array($prod_Familia['ID'],$familias_restritas))$importar=0;
					}
				}

				//*********************** PARA DEBUG
				if($DEBUG==1)
				{
					if($DEBUG_REF != $prod_Referencia)$importar=0;
				}
				//*********************** PARA DEBUG

				if($importar==1)
				{
					$produto_id=0;
					$pode_criar=0;
					if($campo_chave_ean==1 && trim($Ref_Ean) != '') {$pode_criar=1;$produto_id = $prod_refs[$Ref_Ean];} // O PRODUTO JA EXISTE
					else
					{
						if($campo_chave_ean==1 && trim($Ref_Ean) == '' && trim($prod_Referencia) != '') $produto_id = $prod_refs[$prod_Referencia]; // O PRODUTO JA EXISTE
					}
					if($campo_chave_ref==1 && trim($prod_Referencia) != '') {$pode_criar=1;$produto_id = $prod_refs[$prod_Referencia];} // O PRODUTO JA EXISTE
					else
					{
						if($campo_chave_ref==1 && trim($prod_Referencia) == '' && trim($Ref_Ean) != '') $produto_id = $prod_refs[$Ref_Ean]; // O PRODUTO JA EXISTE
					}

					if($produto_id > 0 ) // O PRODUTO JA EXISTE
					{
						$sub_fam_id = '';

						//echo "<br>prod_Referencia = $prod_Referencia";
						//echo "Ref_Ean=$Ref_Ean ";

						$ID_ORG = $prod_dados[$produto_id][0];//Id da familia onde esta o artigo
						$preco_revenoff_prod = $prod_dados[$produto_id][1];
						$sync = $prod_dados[$produto_id][3];

						if($DEBUG==1) echo "O ARTIGO ESTA NA FAMILIA $ID_ORG \n";


						//Se for para colocar o produto na familia que vem na importacao:
						//
						//$prod_Familia['ID']

						$fam_id = $fams_dados[$prod_Familia['ID']];
						$sub_fam_id = $fams_dados[$prod_SubFamilia['ID']];

						if($DEBUG==1) var_dump($prod_Familia);
						if($DEBUG==1) var_dump($prod_SubFamilia);
						if($DEBUG==1) echo "O ARTIGO DEVERA MOVER PARA A FAMILIA $fam_id/$sub_fam_id \n";


						
						// VAMOS ATUALIZAR A FAMILIA OU CRIAR SE NAO EXISTIR
						if($DEBUG==1) echo 'VAMOS ATUALIZAR A FAMILIA OU CRIAR SE NAO EXISTIR\n';
						if($prod_Familia['ID'] > 0)
						{
							if($DEBUG==1) echo "OBTER O ID DA FAMILIA ".$prod_Familia['ID']."\n";
							if($fam_id = $fams_dados[$prod_Familia['ID']])
							{
								if( $cria_familias == 1)
								{
									// UPDATE FAMILIA
									$preco_revenoff_fam = $fams_dados_preco[$fam_id];

									$sql = 'UPDATE products_families SET visivel=1, FLAG=1, ID_Integracao="'.$prod_Familia['ID'].'", data_atualizacao="'.date('Y-m-d H:i:s').'" WHERE ID="'.$fam_id.'"';
									if($DEBUG==1) echo "$sql\n";
									$res = Runquery($db_link,$DB_name,$sql);
									$sql = 'UPDATE LOW_PRIORITY products_families_lang SET Cabecalho="'.$prod_Familia['Valor'].'" WHERE ID_PAI="'.$fam_id .'" and Lang="pt"';
									if($DEBUG==1) echo "$sql\n";
									$res = Runquery($db_link,$DB_name,$sql);
								}
							}
							else
							{
								if( $cria_familias == 1)
								{
									// INSERT FAMILIA
									$sql = 'INSERT INTO products_families (visivel, ID_integracao, data_atualizacao, FLAG) VALUES (1,"'.$prod_Familia['ID'].'","'.date('Y-m-d H:i:s').'", 1)';
									if($DEBUG==1) echo "$sql\n";
									$res = Runquery($db_link,$DB_name,$sql);
									$fam_id = db_get_last_insert_id($db_link);
									$sql = 'INSERT INTO products_families_lang (ID_PAI,Cabecalho,Lang) VALUES ("'.$fam_id.'","'.$prod_Familia['Valor'].'","pt")';
									if($DEBUG==1) echo "$sql\n";
									$res = Runquery($db_link,$DB_name,$sql);
									if(!in_array($fam_id,$fams_dados)) $fams_dados[$prod_Familia['ID']] = $fam_id;
								}
							}
						}

						
						
						if($muda_prod_familia==1)
						{
							//Vamos querer mover o artigo de familias
							if($DEBUG==1) echo "Vamos querer mover o artigo de familias\n";
						}
						else
						{
							//Mantem o artigo na familia onde esta
							if($DEBUG==1) echo "Mantem o artigo na familia onde esta que e $ID_ORG\n";
							$fam_id=$ID_ORG;
						}
					

						// VAMOS ATUALIZAR A SUB-FAMILIA OU CRIAR SE NAO EXISTIR
						if($DEBUG==1) echo "VAMOS ATUALIZAR A SUB-FAMILIA OU CRIAR SE NAO EXISTIR\n";

						// SENDO UMA SUB-FAMILIA E SE NAO FOR PARA CRIAR FAMILIAS ENTAO TB NAO SERA PARA CRIAR SUB-FAMILIAS
						if($prod_SubFamilia['ID'] > 0)
						{
							if($DEBUG==1) echo "OBTER O ID DA SUB-FAMILIA ".$prod_SubFamilia['ID']."\n";

							if($sub_fam_id = $fams_dados[$prod_SubFamilia['ID']])
							{
								if($cria_familias == 1)
								{
									// UPDATE SUB FAM
									$preco_revenoff_fam = $fams_dados_preco[$sub_fam_id];
									$sql = 'UPDATE products_families SET visivel=1, FLAG=1, ID_Integracao="'.$prod_SubFamilia['ID'].'", data_atualizacao="'.date('Y-m-d H:i:s').'" WHERE ID="'.$sub_fam_id.'"';
									if($DEBUG==1) echo "$sql\n";
									$res = Runquery($db_link,$DB_name,$sql);
									$sql = 'UPDATE LOW_PRIORITY products_families_lang SET Cabecalho="'.$prod_SubFamilia['Valor'].'" WHERE ID_PAI="'.$sub_fam_id .'" and Lang="pt"';
									if($DEBUG==1) echo "$sql\n";
									$res = Runquery($db_link,$DB_name,$sql);
								}
							}
							else
							{
								if($cria_familias == 1)
								{
									//INSERT SUB FAM
									$sql = 'INSERT INTO products_families (visivel, ID_ORG, ID_integracao, data_atualizacao, FLAG) VALUES (1, "'.$fam_id.'", "'.$prod_SubFamilia['ID'].'", "'.date('Y-m-d H:i:s').'", 1)';
									if($DEBUG==1) echo "$sql\n";
									$res = Runquery($db_link,$DB_name,$sql);
									$sub_fam_id = db_get_last_insert_id($db_link);
									$sql = 'INSERT INTO products_families_lang (ID_PAI, Cabecalho, Lang) VALUES ("'.$sub_fam_id.'", "'.$prod_SubFamilia['Valor'].'", "pt")';
									if($DEBUG==1) echo "$sql\n";
									$res = Runquery($db_link,$DB_name,$sql);
									if(!in_array($sub_fam_id,$fams_dados)) $fams_dados[$prod_SubFamilia['ID']] = $sub_fam_id;
								}

							}
							//$fams_gen[] = $sub_fam_id;
						}

						// VAMOS ATUALIZAR A MARCA OU CRIAR SE NAO EXISTIR
						if($prod_Marca['ID'] > 0)
						{
							if($prod_Marca_id = $prod_Marcas_dados[$prod_Marca['ID']])
							{
								// UPDATE Marca
								$preco_revenoff_marca = $prod_Marca_dados_preco[$prod_Marca_id];
								if($cria_marcas == 1)
								{
									$sql = 'UPDATE brands SET Visivel=1, FLAG=1, img_ext="'.$ext.'" WHERE ID="'.$prod_Marca_id .'"';
									if($DEBUG==1) echo "$sql\n";
									$res = Runquery($db_link,$DB_name,$sql);
									$sql = 'UPDATE LOW_PRIORITY brands_lang SET Nome="'.$prod_Marca['Valor'].'" WHERE ID_PAI="'.$prod_Marca_id .'" and Lang="pt"';
									if($DEBUG==1) echo "$sql\n";
									$res = Runquery($db_link,$DB_name,$sql);
									// IMAGEM DA MARCA
									getBrandsimg($prod_Marca_id,$prod_Marca['ID']);
								}
							}
							else
							{
								if( $cria_marcas == 1)
								{
									// INSERT MARCA
									$sql = 'INSERT INTO brands (Visivel,ID_integracao, data_atualizacao, FLAG, img_ext) VALUES (1,"'.$prod_Marca['ID'].'", "'.date('Y-m-d H:i:s').'", 1, "'.$ext.'")';
									if($DEBUG==1) echo "$sql\n";
									$res = Runquery($db_link,$DB_name,$sql);
									$prod_Marca_id = db_get_last_insert_id($db_link);
									$sql = 'INSERT INTO brands_lang (ID_PAI,Nome,Lang) VALUES ("'.$prod_Marca_id.'","'.$prod_Marca['Valor'].'","pt")';
									if($DEBUG==1) echo "$sql\n";
									$res = Runquery($db_link,$DB_name,$sql);
									// IMAGEM DA MARCA
									getBrandsimg($prod_Marca_id,$prod_Marca['ID']);
									if(!in_array($prod_Marca_id,$prod_Marcas_dados))  $prod_Marcas_dados[$prod_Marca['ID']] = $prod_Marca_id;
								}
							}
							//$prod_Marcas_gen[] = $prod_Marca_id;
						}

						if($DEBUG==1) echo "IDS das familias  $fam_id / $sub_fam_id / $m_id_prod_sem_familia\n";


						if($sub_fam_id == '') $sub_fam_id=$fam_id;
						// UPDATE PRODUTO
						if($ID_ORG!=$sub_fam_id) $ID_ORG=$sub_fam_id;

						//Se nao tem ID_ORG
						if(!$ID_ORG && $muda_prod_familia==1)$ID_ORG=$m_id_prod_sem_familia;



						if(substr($prod_Nome,0,1) === '+') { $outlet=1;  $prod_Nome = ltrim($prod_Nome, '+');}
						$products_description = addslashes(str_replace("|ln|", "<br/>",$prod_Descricao));



						// SE FOR PARA IMPORTAR OS PRODUTOS RELACIONADOS
						//if($prod_ProdAss == '')
						if($importar_precos==1)
						{
							// TEM PRODUTOS ASSOCIADOS
							/*$obj_pvp_old = 0;
							$obj_pvp_old = floatval($precos_dados[$produto_id]);*/

							// VOLTA A INSERIR AS LINHAS DE PRODUCTS_OBJ E LANG
							if($preco_revenoff_prod == 1) $val_p = floatval(aplicarIVA($prod_Preco_Original));
							elseif($preco_revenoff_fam==1) $val_p = floatval(aplicarIVA($prod_Preco_Original));
							elseif($preco_revenoff_marca==1) $val_p = floatval(aplicarIVA($prod_Preco_Original));
							else $val_p = floatval(aplicarMargem($prod_Preco));

							$Data_ultimo_desconto = '0000-00-00';
							$Desconto_percentagem = 0;
							//$perc_verfica = ((($obj_pvp_old - $val_p ) / ($obj_pvp_old)) * 100);

							if($id_revendedor > 0)
							{
								$news_l = 1;
								$val_promo = 0;
							}
							/*else
							{
								if($val_p < $obj_pvp_old && $perc_verfica > 60)
								{
									$val_p2 = $val_p;
									$val_p = $obj_pvp_old;
									$val_promo = 0;
								}
								elseif($val_p < $obj_pvp_old && $val_p > 0 && $perc_verfica <= 60) //  && $perc_verfica <= 20
								{
									$news_l = 1;
									$val_promo = $val_p;
									$val_p = $obj_pvp_old;
									$Data_ultimo_desconto = date('Y-m-d');
									$Desconto_percentagem = $perc_verfica;
								}
								else $news_l = 1;
							}
							if($val_promo != $val_p && $val_promo > 0) $promo = 1; else $promo = 0;*/

							$promo = 0;
						}

						// SE VIA BACKOFFICE TIRARMOS A SINCRONISAÇÃO ESTA FLAG FICA IGUAL A 1
						$udtime =  date("Y-m-d H:i:s");






						// VAMOS IGNORAR O PROXIMO PASSO CASO ALGUEM DESATIVOU A SYNC AUTOMATICA
						if($DEBUG==1 && $sync==1) echo "CASO ALGUEM DESATIVOU A SYNC AUTOMATICA PARA ESTE ARTIGO\n";

						if($sync == 0)
						{
							//Vamos atualizar os dados relativos ao produto
							if($cria_produtos==1 && $atualiza_descricoes==1)
							{
								// Quando existem várias importacoes em simultaneo, nao podemos mudar o artigo de familia nem o seu nome
								if($muda_prod_familia==1)
								{
									$sql = 'UPDATE products SET ID_ORG="'.$ID_ORG.'", Ref_Ean="'.$Ref_Ean.'",Peso="'.$prod_Peso.'",Ref_secundaria="'.pathinfo($prod_Referencia,PATHINFO_EXTENSION).'",Aproved=1,Aproved_ass=0,Flag=1,Preco1="'.$prod_Preco.'",MargemCli="'.aplicarMargem_ret($prod_Preco).'",Preco2="'.ValorPagarMargens($prod_Preco).'",Data_ultimo_desconto="'.$Data_ultimo_desconto.'",Desconto_percentagem="'.$Desconto_percentagem.'",Video="'.$prod_Video.'",CorEE="'.$prod_CorEtiquetaEnergia.'",u_coretiq="'.$prod_CorEtiquetaEnergia.'",u_catetiq="'.$prod_letra_energetica.'",udtime="'.$udtime.'" WHERE ID="'.$produto_id.'"';
									if($DEBUG==1) echo "$sql\n";
									$res = Runquery($db_link,$DB_name,$sql);
								}
								else
								{
									$sql = 'UPDATE products SET Ref_Ean="'.$Ref_Ean.'",Peso="'.$prod_Peso.'",Ref_secundaria="'.pathinfo($prod_Referencia,PATHINFO_EXTENSION).'",Aproved=1,Aproved_ass=0,Flag=1,Preco1="'.$prod_Preco.'",MargemCli="'.aplicarMargem_ret($prod_Preco).'",Preco2="'.ValorPagarMargens($prod_Preco).'",Data_ultimo_desconto="'.$Data_ultimo_desconto.'",Desconto_percentagem="'.$Desconto_percentagem.'",Video="'.$prod_Video.'",CorEE="'.$prod_CorEtiquetaEnergia.'",u_coretiq="'.$prod_CorEtiquetaEnergia.'",u_catetiq="'.$prod_letra_energetica.'",udtime="'.$udtime.'" WHERE ID="'.$produto_id.'"';
									if($DEBUG==1) echo "$sql\n";
									$res = Runquery($db_link,$DB_name,$sql);
								}
								
								$sql = 'UPDATE LOW_PRIORITY products_lang SET Cabecalho="'.$prod_Nome.'",Texto1="'.$products_description.'" WHERE ID_PAI="'.$produto_id.'" AND Lang="pt"';
								if($DEBUG==1) echo "$sql\n";
								$res = Runquery($db_link,$DB_name,$sql);

								/*$sql = 'UPDATE products SET ID_ORG="'.$sub_fam_id.'",fornecedor_importacao="macorlux",Ref_Ean="'.$Ref_Ean.'",Peso="'.$prod_Peso.'",Ref_secundaria="'.pathinfo($prod_Referencia,PATHINFO_EXTENSION).'",Aproved=1,Aproved_ass=0,Flag=1,Preco1="'.$prod_Preco.'",MargemCli="'.aplicarMargem_ret($prod_Preco).'",Preco2="'.ValorPagarMargens($prod_Preco).'",Data_ultimo_desconto="'.$Data_ultimo_desconto.'",Desconto_percentagem="'.$Desconto_percentagem.'",Video="'.$prod_Video.'",CorEE="'.$prod_CorEtiquetaEnergia.'",u_coretiq="'.$prod_CorEtiquetaEnergia.'",u_catetiq="'.$prod_letra_energetica.'",udtime="'.$udtime.'" WHERE ID="'.$produto_id.'"';
								$res = Runquery($db_link,$DB_name,$sql);
								$sql = 'UPDATE LOW_PRIORITY products_lang SET Cabecalho="'.$prod_Nome.'",Texto1="'.$products_description.'" WHERE ID_PAI="'.$produto_id.'" AND Lang="pt"';
								$res = Runquery($db_link,$DB_name,$sql);*/
							}

							//Vamos atualizar o id da sub-familia
							if($muda_prod_familia==1 && $ID_ORG > 0 )
							{
								$sql = 'UPDATE products_families_obj SET ID_ORG ="'.$ID_ORG.'" WHERE ID_PROD="'.$produto_id.'"';
								if($DEBUG==1) echo "$sql\n";
								$res = Runquery($db_link,$DB_name,$sql);
							}


							if($importar_imagens==1)
							{
								if($DEBUG==1) echo "IMPORTAR IMAGENS\n";

								if($prod_Imagem!='')
								{
									if($DEBUG==1) echo "IMPORTAR IMAGEM 1\n";
									$url = $prod_Imagem;
									$ext = explode('.',$url);

									$img = $site_shell_url.'files/products/'.$produto_id.'_1.'.end($ext);

									//Se a imagem já existe do nosso lado mas está desatualizada:
									if(file_exists($img) && $prod_ImagemTime>filemtime($img) )unlink($img);

									// VAMOS VERIFICAR SE A IMAGEM JÁ EXISTE DO NOSSO LADO
									$forcar=0;
									if(!file_exists($img) || $forcar==1)
									{
										echo "U";
										file_put_contents($img,file_get_contents($url));
										$res=Runquery($db_link,$DB_name,'UPDATE products SET img_ext="'.end($ext).'" WHERE ID="'.$produto_id.'"');
										$res=Runquery($db_link,$DB_name,'DELETE FROM products_images WHERE ID_ORG="'.$produto_id.'" AND Ordem=1');
										$res=Runquery($db_link,$DB_name,'INSERT INTO products_images(ID_ORG,img_ext,Ordem,URL_Source) VALUES("'.$produto_id.'","'.end($ext).'",1,"'.$url.'")');
									}
									else
									{
										if(!$imagens_dados[$produto_id])
										{
											// POR ALGUM MOTIVO GRAVOU A IMAGEM MAS NÃO GRAVOU EM BASE DE DADOS
											$res=Runquery($db_link,$DB_name,'INSERT INTO products_images(ID_ORG,img_ext,Ordem,URL_Source) VALUES("'.$produto_id.'","'.end($ext).'",1,"'.$url.'")');
										}
									}
								}
								if($prod_Imagem2!='')
								{
									if($DEBUG==1) echo "IMPORTAR IMAGEM 2\n";

									$url = $prod_Imagem2;
									$ext = explode('.',$url);
									$img = $site_shell_url.'files/products/'.$produto_id.'_2.'.end($ext);
									//Se a imagem já existe do nosso lado mas está desatualizada:
									if(file_exists($img) && $prod_ImagemTime2>filemtime($img) )unlink($img);

									// VAMOS VERIFICAR SE A IMAGEM JÁ EXISTE DO NOSSO LADO
									$forcar=0;
									if(!file_exists($img) || $forcar==1)
									{
										echo "U";
										file_put_contents($img,file_get_contents($url));
										$res=Runquery($db_link,$DB_name,'DELETE FROM products_images WHERE ID_ORG="'.$produto_id.'" AND Ordem=2');
										$res=Runquery($db_link,$DB_name,'INSERT INTO products_images (ID_ORG,img_ext,Ordem,URL_Source) VALUES ("'.$produto_id.'","'.end($ext).'",2,"'.$url.'")');
									}
									else
									{
										if(!$imagens_dados[$produto_id])
										{
											// POR ALGUM MOTIVO GRAVOU A IMAGEM MAS NÃO GRAVOU EM BASE DE DADOS
											$res=Runquery($db_link,$DB_name,'INSERT INTO products_images (ID_ORG,img_ext,Ordem,URL_Source) VALUES ("'.$produto_id.'","'.end($ext).'",2,"'.$url.'")');
										}
									}
								}
								if($prod_Imagem3!='')
								{
									if($DEBUG==1) echo "IMPORTAR IMAGEM 3\n";
									$url = $prod_Imagem3;
									$ext = explode('.',$url);

									$img = $site_shell_url.'files/products/'.$produto_id.'_3.'.end($ext);

									//Se a imagem já existe do nosso lado mas está desatualizada:
									if(file_exists($img) && $prod_ImagemTime3>filemtime($img) )unlink($img);

									// VAMOS VERIFICAR SE A IMAGEM JÁ EXISTE DO NOSSO LADO
									$forcar=0;
									if(!file_exists($img) || $forcar==1)
									{
										echo "U";
										file_put_contents($img,file_get_contents($url));
										$res=Runquery($db_link,$DB_name,'DELETE FROM products_images WHERE ID_ORG="'.$produto_id.'" AND Ordem=3');
										$res=Runquery($db_link,$DB_name,'INSERT INTO products_images (ID_ORG,img_ext,Ordem,URL_Source) VALUES ("'.$produto_id.'","'.end($ext).'",3,"'.$url.'")');
									}
									else
									{
										if(!$imagens_dados[$produto_id])
										{
											// POR ALGUM MOTIVO GRAVOU A IMAGEM MAS NÃO GRAVOU EM BASE DE DADOS
											$res=Runquery($db_link,$DB_name,'INSERT INTO products_images (ID_ORG,img_ext,Ordem,URL_Source) VALUES ("'.$produto_id.'","'.end($ext).'",3,"'.$url.'")');
										}
									}
								}
								if($prod_Imagem4!='')
								{
									if($DEBUG==1) echo "IMPORTAR IMAGEM 4\n";
									$url = $prod_Imagem4;
									$ext = explode('.',$url);

									$img = $site_shell_url.'files/products/'.$produto_id.'_4.'.end($ext);

									//Se a imagem já existe do nosso lado mas está desatualizada:
									if(file_exists($img) && $prod_ImagemTime4>filemtime($img) )unlink($img);

									// VAMOS VERIFICAR SE A IMAGEM JÁ EXISTE DO NOSSO LADO
									$forcar=0;
									if(!file_exists($img) || $forcar==1)
									{
										echo "U";
										file_put_contents($img,file_get_contents($url));
										$res=Runquery($db_link,$DB_name,'DELETE FROM products_images WHERE ID_ORG="'.$produto_id.'" AND Ordem=4');
										$res=Runquery($db_link,$DB_name,'INSERT INTO products_images (ID_ORG,img_ext,Ordem,URL_Source) VALUES ("'.$produto_id.'","'.end($ext).'",4,"'.$url.'")');
									}
									else
									{
										if(!$imagens_dados[$produto_id])
										{
											// POR ALGUM MOTIVO GRAVOU A IMAGEM MAS NÃO GRAVOU EM BASE DE DADOS
											$res=Runquery($db_link,$DB_name,'INSERT INTO products_images (ID_ORG,img_ext,Ordem,URL_Source) VALUES ("'.$produto_id.'","'.end($ext).'",4,"'.$url.'")');
										}
									}
								}
								if($prod_Imagem5!='')
								{
									if($DEBUG==1) echo "IMPORTAR IMAGEM 5\n";
									$url = $prod_Imagem5;
									$ext = explode('.',$url);

									$img = $site_shell_url.'files/products/'.$produto_id.'_5.'.end($ext);

									//Se a imagem já existe do nosso lado mas está desatualizada:
									if(file_exists($img) && $prod_ImagemTime5>filemtime($img) )unlink($img);

									// VAMOS VERIFICAR SE A IMAGEM JÁ EXISTE DO NOSSO LADO
									$forcar=0;
									if(!file_exists($img) || $forcar==1)
									{
										echo "U";
										file_put_contents($img,file_get_contents($url));
										$res=Runquery($db_link,$DB_name,'DELETE FROM products_images WHERE ID_ORG="'.$produto_id.'" AND Ordem=5');
										$res=Runquery($db_link,$DB_name,'INSERT INTO products_images (ID_ORG,img_ext,Ordem,URL_Source) VALUES ("'.$produto_id.'","'.end($ext).'",5,"'.$url.'")');
									}
									else
									{
										if(!$imagens_dados[$produto_id])
										{
											// POR ALGUM MOTIVO GRAVOU A IMAGEM MAS NÃO GRAVOU EM BASE DE DADOS
											$res=Runquery($db_link,$DB_name,'INSERT INTO products_images (ID_ORG,img_ext,Ordem,URL_Source) VALUES ("'.$produto_id.'","'.end($ext).'",5,"'.$url.'")');
										}
									}
								}
								if($prod_Imagem6!='')
								{
									if($DEBUG==1) echo "IMPORTAR IMAGEM 6\n";
									$url = $prod_Imagem6;
									$ext = explode('.',$url);

									$img = $site_shell_url.'files/products/'.$produto_id.'_6.'.end($ext);

									//Se a imagem já existe do nosso lado mas está desatualizada:
									if(file_exists($img) && $prod_ImagemTime6>filemtime($img) )unlink($img);

									// VAMOS VERIFICAR SE A IMAGEM JÁ EXISTE DO NOSSO LADO
									$forcar=0;
									if(!file_exists($img) || $forcar==1)
									{
										echo "U";
										file_put_contents($img,file_get_contents($url));
										$res=Runquery($db_link,$DB_name,'DELETE FROM products_images WHERE ID_ORG="'.$produto_id.'" AND Ordem=6');
										$res=Runquery($db_link,$DB_name,'INSERT INTO products_images (ID_ORG,img_ext,Ordem,URL_Source) VALUES ("'.$produto_id.'","'.end($ext).'",6,"'.$url.'")');
									}
									else
									{
										if(!$imagens_dados[$produto_id])
										{
											// POR ALGUM MOTIVO GRAVOU A IMAGEM MAS NÃO GRAVOU EM BASE DE DADOS
											$res=Runquery($db_link,$DB_name,'INSERT INTO products_images (ID_ORG,img_ext,Ordem,URL_Source) VALUES ("'.$produto_id.'","'.end($ext).'",6,"'.$url.'")');
										}
									}
								}
								if($prod_Imagem7!='')
								{
									if($DEBUG==1) echo "IMPORTAR IMAGEM 7\n";
									$url = $prod_Imagem7;
									$ext = explode('.',$url);

									$img = $site_shell_url.'files/products/'.$produto_id.'_7.'.end($ext);

									//Se a imagem já existe do nosso lado mas está desatualizada:
									if(file_exists($img) && $prod_ImagemTime7>filemtime($img) )unlink($img);

									// VAMOS VERIFICAR SE A IMAGEM JÁ EXISTE DO NOSSO LADO
									$forcar=0;
									if(!file_exists($img) || $forcar==1)
									{
										echo "U";
										file_put_contents($img,file_get_contents($url));
										$res=Runquery($db_link,$DB_name,'DELETE FROM products_images WHERE ID_ORG="'.$produto_id.'" AND Ordem=7');
										$res=Runquery($db_link,$DB_name,'INSERT INTO products_images (ID_ORG,img_ext,Ordem,URL_Source) VALUES ("'.$produto_id.'","'.end($ext).'",7,"'.$url.'")');
									}
									else
									{
										if(!$imagens_dados[$produto_id])
										{
											// POR ALGUM MOTIVO GRAVOU A IMAGEM MAS NÃO GRAVOU EM BASE DE DADOS
											$res=Runquery($db_link,$DB_name,'INSERT INTO products_images (ID_ORG,img_ext,Ordem,URL_Source) VALUES ("'.$produto_id.'","'.end($ext).'",7,"'.$url.'")');
										}
									}
								}
							}
							// ETIQUETA DE ENERGIA
							if($prod_EtiquetaEnergia != '' && $importar_etiquetas==1)
							{
								if($DEBUG==1) echo "IMPORTAR ETIQUETA DE ENERGIA\n";
								$url = $prod_EtiquetaEnergia;
								$ext = explode('.',$url);
								$ee = $site_shell_url.'files/products/EE/'.$produto_id.'_1.'.end($ext);
								// VAMOS VERIFICAR SE A IMAGEM JÁ EXISTE DO NOSSO LADO
								if(!file_exists($ee))
								{
									file_put_contents($ee, file_get_contents($url));
									$exts = end($ext);
									$res = Runquery($db_link,$DB_name, 'UPDATE products SET ee_ext="'.$exts.'", u_imagetiq="'.$produto_id.'_1.pdf" WHERE ID="'.$produto_id.'"');
								}
							} /*else $res = Runquery($db_link,$DB_name, 'UPDATE products SET ee_ext="", u_imagetiq="" WHERE ID="'.$produto_id.'"');*/

							// FICHA UNIÃO EUROPEIA
							if($prod_FichaUE != '' && $importar_fichaeua==1)
							{
								if($DEBUG==1) echo "IMPORTAR FICHA UNIÃO EUROPEIA\n";
								$url = $prod_FichaUE;
								$ext = explode('.',$url);
								$ue = $site_shell_url.'files/products/UE/'.$produto_id.'_1.'.end($ext);
								// VAMOS VERIFICAR SE A IMAGEM JÁ EXISTE DO NOSSO LADO
								if(!file_exists($ue))
								{
									file_put_contents($ue, file_get_contents($url));
									$exts = end($ext);
									$res = Runquery($db_link,$DB_name, 'UPDATE products SET ue_ext="'.$exts.'" WHERE ID="'.$produto_id.'"');
								}
							} /*else $res = Runquery($db_link,$DB_name, 'UPDATE products SET ue_ext="" WHERE ID="'.$produto_id.'"');*/

							// DESENHOS TÉCNICOS
							if($prod_DesenhosTecnicos != '' && $importar_desenhos==1)
							{
								if($DEBUG==1) echo "IMPORTAR DESENHOS TÉCNICOS\n";
								$url = $prod_DesenhosTecnicos;
								$ext = explode('.',$url);
								$dt = $site_shell_url.'files/products/DT/'.$produto_id.'_1.'.end($ext);
								// VAMOS VERIFICAR SE A IMAGEM JÁ EXISTE DO NOSSO LADO
								if(!file_exists($dt))
								{
									file_put_contents($dt, file_get_contents($url));
									$exts = end($ext);
									$res = Runquery($db_link,$DB_name, 'UPDATE products SET dt_ext="'.$exts.'" WHERE ID="'.$produto_id.'"');
								}
							} /*else $res = Runquery($db_link,$DB_name, 'UPDATE products SET dt_ext="" WHERE ID="'.$produto_id.'"');*/


							//if($prod_ProdAss == '' && $importar_precos==1)
							if($importar_precos==1)
							{
								if($DEBUG==1) echo "importar_precos invocando a funcao update_products_precos()\n";

								update_products_precos($produto_id,$val_p,$prod_QtdMinEnc,$prod_Stock,$prod_Referencia, 'EAN', 0, ($prod_Stock !='' ? 1 : 0),$Supplier_ID,0,$iva_default,$prod_Preco);

								//Vamos remover os produtos associados, se já houve
								/*$sql = 'DELETE FROM products_objs_lang WHERE ID_PAI IN (SELECT obj_id FROM products_objs WHERE obj_type_id="'.$produto_id.'")';
								$res = Runquery($db_link,$DB_name,$sql);
								$sql = 'DELETE FROM products_objs WHERE obj_type_id="'.$produto_id .'"';
								$res = Runquery($db_link,$DB_name,$sql);
								$sql = 'INSERT INTO products_objs (obj_type_id,obj_type,obj_stock,obj_iva,obj_pvp,obj_pvp2,obj_promo,obj_promo_ativo,obj_ativo,obj_val,obj_destaque,obj_novo,obj_novo2,obj_outlet,obj_qtdmin,obj_ecotaxas)
								VALUES ("'.$produto_id.'","products","'.$prod_Stock.'","'.$iva_default.'","'.$val_p.'","'.$val_p2.'","'.$val_promo.'","'.$promo.'",1,1,"'.$prod_Destaque.'","'.$prod_Novidade.'","'.$news_l.'","'.$outlet.'","'.$prod_QtdMinEnc.'","'.addslashes($prod_Ecotaxas).'")';
								$res = Runquery($db_link,$DB_name,$sql);
								$obj_id = db_get_last_insert_id($db_link);
								$sql = 'INSERT INTO products_objs_lang (ID_PAI,Cabecalho,Lang) VALUES ("'.$obj_id.'", "'.$prod_Referencia.'", "pt")';
								$res = Runquery($db_link,$DB_name,$sql);*/
							}


							//Outras sugestoes
							$Sugestoes = $prod_Sugestoes;
							if(is_array($Sugestoes) && !empty($Sugestoes) && $importar_relacionados==1)
							{
								if($DEBUG==1) echo "importar Outras sugestoes\n";
								for ($i=0; $i < count($Sugestoes); $i++)
								{
									$rs2->db_open($db_link,$DB_name, 'SELECT ID FROM products WHERE ID_Integracao="'.$Sugestoes[$i].'"');
									if(!$rs2->EOF())
									{
										$prod_id2 = $rs2->db_data['ID'];
										$rs3->db_open($db_link,$DB_name,'SELECT ID_Prod FROM produtos_sugestoes WHERE ID_Prod="'.$produto_id.'" AND ID_Prod2="'.$prod_id2.'"');
										if($rs3->EOF())
										{
											$res = Runquery($db_link,$DB_name,'INSERT INTO produtos_sugestoes (ID_Prod,ID_Prod2) VALUES ("'.$produto_id.'","'.$prod_id2.'")');
										}
										$rs3->db_close();
									}
									$rs2->db_close();
								}
							}

							// Produtos relacionados
							$Relacionados = $prod_Relacionados;
							if(is_array($Relacionados) && !empty($Relacionados) && $importar_relacionados==1)
							{
								if($DEBUG==1) echo "importar Produtos relacionados\n";
								for ($i=0; $i < count($Relacionados); $i++)
								{
									$rs2->db_open($db_link,$DB_name, 'SELECT ID FROM products WHERE ID_Integracao="'.$Relacionados[$i].'"');
									if(!$rs2->EOF())
									{
										$prod_id2 = $rs2->db_data['ID'];
										$rs3->db_open($db_link,$DB_name,'SELECT ID_Prod FROM produtos_opcoes WHERE ID_Prod="'.$produto_id.'" AND ID_Prod2="'.$prod_id2.'"');
										if($rs3->EOF())
										{
											$res = Runquery($db_link,$DB_name,'INSERT INTO produtos_opcoes (ID_Prod,ID_Prod2) VALUES ("'.$produto_id.'","'.$prod_id2.'")');
										}
										$rs3->db_close();
									}
									$rs2->db_close();
								}
							}

							unlink($site_shell_url.'files/cache/products/p'.$produto_id.'_pt.bin');//Remove a cache do artigo

						}
						else // EM CASO DE O PRODUTO ESTAR COM A SICRONIZAÇÃO INATIVA, VAMOS VERIFICAR SE NÃO TEM IMAGEM NA MESMA
						{
							if($prod_Imagem != '')
							{
								$url = $prod_Imagem;
								$ext = explode('.',$url);
								$img = $site_shell_url.'files/products/'.$produto_id.'_1.'.end($ext);

								//Se a imagem já existe do nosso lado mas está desatualizada:
								/*if(file_exists($img) && $prod_ImagemTime>filemtime($img))
								{
									unlink($img);
								}*/

								// VAMOS VERIFICAR SE A IMAGEM JÁ EXISTE DO NOSSO LADO
								if(!file_exists($img))
								{
									file_put_contents($img, file_get_contents($url));
									$res = Runquery($db_link,$DB_name, 'UPDATE products SET img_ext="'.end($ext).'" WHERE ID="'.$produto_id.'"');
									$res = Runquery($db_link,$DB_name, 'DELETE FROM products_images WHERE ID_ORG="'.$produto_id.'" AND Ordem=1');
									$res = Runquery($db_link,$DB_name, 'INSERT INTO products_images (ID_ORG, img_ext, Ordem, URL_Source) VALUES ("'.$produto_id.'", "'.end($ext).'", 1, "'.$url.'")');
								}
								else
								{
									if(!$imagens_dados[$produto_id])
									{
										// POR ALGUM MOTIVO GRAVOU A IMAGEM MAS NÃO GRAVOU EM BASE DE DADOS
										$res = Runquery($db_link,$DB_name, 'INSERT INTO products_images (ID_ORG,img_ext,Ordem,URL_Source) VALUES ("'.$produto_id.'", "'.end($ext).'", 1, "'.$url.'")');
									}
								}
							}
						}

						$filtros_rel2 = '';

						if($cria_filtros ==1 &&  (isset($prod_Caracteristicas) && !empty($prod_Caracteristicas)) || ($prod_Marca['ID'] > 0) )
						{
							if($DEBUG==1) echo "agora vamos atualizar os filtros\n";
							$fams=array();
							$fams = niveis_fams_f($ID_ORG);

							if(isset($prod_Caracteristicas) && !empty($prod_Caracteristicas))
							{
								foreach($prod_Caracteristicas as $filtros)
								{
									$IDCategoria = $filtros['IDCategoria'];
									$Categoria = $filtros['Categoria'];
									$IDValor = $filtros['IDValor'];
									$Valor = $filtros['Valor'];
									$f_od1 = $filtros['f_od1'];
									$f_od2 = $filtros['f_od2'];
									$v_f_od1 = $filtros['v_f_od1'];
									$v_f_od2 = $filtros['v_f_od2'];
									$f_ativo = $filtros['f_ativo'];

									if($DEBUG==1) echo "filtros_atualiza($produto_id,$IDCategoria,$Categoria,$IDValor,$Valor)\n";


									$filters_data = filtros_atualiza($produto_id,$IDCategoria,$Categoria,$IDValor,$Valor);
									$filtros_rel2 .= $filters_data['category_id'].'_'.$filters_data['value_id'].';';
									filtros_produtos_atualiza_imp($filters_data['category_id'],$filters_data['value_id'],$produto_id,$fams,$prod_Marca_id,$filtros_rel2,$f_od1,$f_od2,$v_f_od1,$v_f_od2,$f_ativo);
								}
							}
							/*
							// Não faz sentido ter a marca 2 vezes
							if($prod_Marca['ID'] > 0 && $cria_marcas == 1)
							{
								$filters_data = filtros_atualiza($produto_id,1,'Marca',$prod_Marca['ID'],$prod_Marca['Valor'],1);
								$filtros_rel2 .= $filters_data['category_id'].'_'.$filters_data['value_id'].';';
								filtros_produtos_atualiza_imp($filters_data['category_id'],$filters_data['value_id'],$produto_id,$fams,$prod_Marca_id,$filtros_rel2,0,0,0,0,1);
							}*/
						}
						if($cria_filtros == 1)
						{
							$sql = 'UPDATE filters_products SET filtros_rel2="'.$filtros_rel2.'" WHERE prod_id="'.$produto_id.'"';
							if($DEBUG==1) echo "$sql\n";
							$res = Runquery($db_link,$DB_name,$sql);

							$xx++;
						}
						// ***********************************************


						if($log) echo ($cont+$cont2).': Update Produto: '.$prod_Referencia.'<br>';
						$cont++;
					}
					else
					{
						if($DEBUG==1) echo "VAMOS IMPORTAR USANDO O EAN COMO CAMPO CHAVE\n";

						if(($campo_chave_ean==1 && trim($Ref_Ean) != '') || ($campo_chave_ref==1 && trim($prod_Referencia) != ''))
						{
							if($cria_produtos == 1 && $pode_criar == 1)
							{
								echo "+";
								$fam_id = $fams_dados[$prod_Familia['ID']];
								$sub_fam_id = $fams_dados[$prod_SubFamilia['ID']];

								//********************************************************************************************************
								// NÃO EXISTE, VAMOS INSERIR O PRODUTO
								if($prod_Familia['ID'] > 0 && $cria_familias == 1)
								{
									if($fam_id = $fams_dados[$prod_Familia['ID']])
									{
										$preco_revenoff_fam = $fams_dados_preco[$fam_id];
										$sql = 'UPDATE products_families SET visivel=1, FLAG=1, ID_Integracao="'.$prod_Familia['ID'].'", data_atualizacao="'.date('Y-m-d H:i:s').'" WHERE ID="'.$fam_id.'"';
										$res = Runquery($db_link,$DB_name,$sql);
										$sql = 'UPDATE products_families_lang SET Cabecalho="'.$prod_Familia['Valor'].'" WHERE ID_PAI="'.$fam_id .'" and Lang="pt"';
										$res = Runquery($db_link,$DB_name,$sql);
									}
									else
									{
										// INSERT FAMILIA
										$sql = 'INSERT INTO products_families (visivel, ID_integracao, data_atualizacao, ID_ORG, FLAG) VALUES (1, "'.$prod_Familia['ID'].'", "'.date('Y-m-d H:i:s').'", "'.$fam_id_temp.'", 1)';
										$res = Runquery($db_link,$DB_name,$sql);
										$fam_id = db_get_last_insert_id($db_link);
										$sql = 'INSERT INTO products_families_lang (ID_PAI, Cabecalho, Lang) VALUES ("'.$fam_id.'", "'.$prod_Familia['Valor'].'","pt")';
										$res = Runquery($db_link,$DB_name,$sql);
										if(!in_array($fam_id,$fams_dados)) $fams_dados[$prod_Familia['ID']] = $fam_id;
									}
									//$fams_gen[] = $fam_id;
								}
								if($prod_SubFamilia['ID'] > 0 && $cria_familias == 1)
								{
									if($sub_fam_id = $fams_dados[$prod_SubFamilia['ID']])
									{
										$preco_revenoff_fam = $fams_dados_preco[$sub_fam_id];

										$sql = 'UPDATE products_families SET  visivel=1, FLAG=1, ID_Integracao="'.$prod_SubFamilia['ID'].'", data_atualizacao="'.date('Y-m-d H:i:s').'" WHERE ID="'.$sub_fam_id.'"';
										$res = Runquery($db_link,$DB_name,$sql);
										$sql = 'UPDATE products_families_lang SET Cabecalho="'.$prod_SubFamilia['Valor'].'" WHERE ID_PAI="'.$sub_fam_id .'" and Lang="pt"';
										$res = Runquery($db_link,$DB_name,$sql);

									}
									else
									{
										// INSERT SUB FAM
										$sql = 'INSERT INTO products_families (visivel, ID_ORG, ID_integracao, data_atualizacao, FLAG) VALUES (1, "'.$fam_id.'", "'.$prod_SubFamilia['ID'].'", "'.date('Y-m-d H:i:s').'", 1)';
										$res = Runquery($db_link,$DB_name,$sql);
										$sub_fam_id = db_get_last_insert_id($db_link);
										$sql = 'INSERT INTO products_families_lang (ID_PAI, Cabecalho, Lang) VALUES ("'.$sub_fam_id.'", "'.$prod_SubFamilia['Valor'].'", "pt")';
										$res = Runquery($db_link,$DB_name,$sql);
										if(!in_array($sub_fam_id,$fams_dados)) $fams_dados[$prod_SubFamilia['ID']] = $sub_fam_id;
									}
									//$fams_gen[] = $sub_fam_id;
								}
								$rs2->db_close();

								if($prod_Marca['ID'] > 0 && $cria_marcas == 1)
								{
									$ext = '';
									if($prod_Marca['Imagem'] != '')
									{
										$url = $prod_Marca['Imagem'];
										$ext = pathinfo($url, PATHINFO_EXTENSION);
										$img = $site_shell_url.'files/brands/'.$prod_Marca['ID'].'_1.'.$ext;

										if(!file_exists($img)) file_put_contents($img, file_get_contents($url));
									}

									if($prod_Marca_id = $prod_Marcas_dados[$prod_Marca['ID']])
									{
										$sql = 'UPDATE brands SET Visivel=1, FLAG=1, img_ext="'.$ext.'" WHERE ID="'.$prod_Marca_id .'"';
										$res = Runquery($db_link,$DB_name,$sql);

										$preco_revenoff_marca = $prod_Marca_dados_preco[$prod_Marca_id];
										$res = Runquery($db_link,$DB_name, 'UPDATE brands_lang SET Nome="'.$prod_Marca['Valor'].'" WHERE ID_PAI="'.$prod_Marca_id .'" and Lang="pt"');

										// IMAGEM DA MARCA
										getBrandsimg($prod_Marca_id,$prod_Marca['ID']);
									}
									else
									{
										// INSERT MARCA
										$res = Runquery($db_link,$DB_name,'INSERT INTO brands (Visivel, ID_integracao, data_atualizacao, FLAG, img_ext) VALUES (1,"'.$prod_Marca['ID'].'", "'.date('Y-m-d H:i:s').'", 1, "'.$ext.'")');
										$prod_Marca_id = db_get_last_insert_id($db_link);
										$sql = 'INSERT INTO brands_lang (ID_PAI,Nome,Lang) VALUES ("'.$prod_Marca_id.'","'.$prod_Marca['Valor'].'","pt")';
										$res = Runquery($db_link,$DB_name,$sql);
										// IMAGEM DA MARCA
										getBrandsimg($prod_Marca_id,$prod_Marca['ID']);
										if(!in_array($prod_Marca_id,$prod_Marcas_dados))  $prod_Marcas_dados[$prod_Marca['ID']] = $prod_Marca_id;
									}
									$rs2->db_close();
									//$prod_Marcas_gen[] = $prod_Marca_id;
								}


								//Se definimos uma familia default, entao sera essa a familia onde o produto vai ser inserido
								if($familia_default) $fam_id=$familia_default;


								if($sub_fam_id == '') $sub_fam_id = $fam_id;
								$udtime =  date("Y-m-d H:i:s");

								//Se e para criar artigos
								if($cria_produtos == 1)
								{
									if($prod_ProdAss != '') $sql = 'INSERT INTO products (Ref_Ean, Peso, Flag,ID_ORG,Ref,Ref_Ass,Ref_secundaria,ID_Marca,Destaque,Aproved,Aproved_ass,ID_Integracao,Preco1,MargemCli,Preco2,Data_criacao,Data_ultimo_desconto,Desconto_percentagem,Importancia,TXPV,TXPV_Data,Carrinho,Video,CorEE,u_coretiq,u_catetiq,fornecedor_importacao,udtime) VALUES ("'.$Ref_Ean.'", "'.$prod_Peso.'",1,"'.$sub_fam_id.'","'.$prod_Referencia.'","'.$prod_ProdAss.'","'.pathinfo($prod_Referencia, PATHINFO_EXTENSION).'","'.$prod_Marca_id.'",1,0,1,"'.$prod_ID.'","'.$prod_Preco.'","'.aplicarMargem_ret($prod_Preco).'","'.ValorPagarMargens($prod_Preco).'","'.date("Y-m-d").'","0000-00-00",0,0,0,"0000-00-00",1,"'.$prod_Video.'","'.$prod_CorEtiquetaEnergia.'","'.$prod_CorEtiquetaEnergia.'","'.$prod_letra_energetica.'", "macorlux","'.$udtime.'")';
									else $sql = 'INSERT INTO products (Ref_Ean, Peso, Flag,ID_ORG,Ref,Ref_secundaria,ID_Marca,Destaque,Aproved,Aproved_ass,ID_Integracao,Preco1,MargemCli,Preco2,Data_criacao,Data_ultimo_desconto,Desconto_percentagem,Importancia,TXPV,TXPV_Data,Carrinho,Video,CorEE,u_coretiq,u_catetiq,fornecedor_importacao,udtime) VALUES ("'.$Ref_Ean.'", "'.$prod_Peso.'",1,"'.$sub_fam_id.'","'.$prod_Referencia.'","'.pathinfo($prod_Referencia, PATHINFO_EXTENSION).'","'.$prod_Marca_id.'",1,0,0,"'.$prod_ID.'","'.$prod_Preco.'","'.aplicarMargem_ret($prod_Preco).'","'.ValorPagarMargens($prod_Preco).'","'.date("Y-m-d").'","0000-00-00",0,0,0,"0000-00-00",1,"'.$prod_Video.'","'.$prod_CorEtiquetaEnergia.'","'.$prod_CorEtiquetaEnergia.'","'.$prod_letra_energetica.'", "macorlux","'.$udtime.'")';
									$res = Runquery($db_link,$DB_name,$sql);
									$produto_id = db_get_last_insert_id($db_link);

									// INSERE FAMILIA AO PRODUTO
									$res = Runquery($db_link,$DB_name, 'INSERT INTO products_families_obj (ID_PROD, ID_ORG) VALUES ("'.$produto_id.'", "'.$sub_fam_id.'")');
									$prod_Nome = addslashes(trim($prod_Nome));


									if(substr($prod_Nome,0,1) === '+'){ $outlet = 1; $prod_Nome = ltrim($prod_Nome, '+');}
									$products_description = addslashes(str_replace("|ln|", "<br/>",$prod_Descricao));
									$sql = 'INSERT INTO products_lang (ID_PAI, Cabecalho, Texto1, Lang) VALUES ("'.$produto_id.'", "'.$prod_Nome.'", "'.$products_description.'", "pt")';
									$res = Runquery($db_link,$DB_name,$sql);
								}


								//Se pode inserir imagens
								if($prod_Imagem != '' && $importar_imagens == 1)
								{
									$url = $prod_Imagem;
									$ext = explode('.',$url);
									$img = $site_shell_url.'files/products/'.$produto_id.'_1.'.end($ext);

									//Se a imagem já existe do nosso lado mas está desatualizada:
									if(file_exists($img) && ($prod_ImagemTime>filemtime($img)) || filesize($img)==0 )
									{
										unlink($img);
									}

									// VAMOS VERIFICAR SE A IMAGEM JÁ EXISTE DO NOSSO LADO
									if(!file_exists($img))
									{
										file_put_contents($img, file_get_contents($url));
										$res=Runquery($db_link,$DB_name, 'UPDATE products SET img_ext="'.end($ext).'" WHERE ID="'.$produto_id.'"');
										$res=Runquery($db_link,$DB_name, 'DELETE FROM products_images WHERE ID_ORG="'.$produto_id.'" AND Ordem=1');
										$res=Runquery($db_link,$DB_name, 'INSERT INTO products_images (ID_ORG,img_ext,Ordem,URL_Source) VALUES ("'.$produto_id.'","'.end($ext).'",1,"'.$url.'")');
									}
									else
									{
										if(!$imagens_dados[$produto_id])
										{
											// POR ALGUM MOTIVO GRAVOU A IMAGEM MAS NÃO GRAVOU EM BASE DE DADOS
											$res=Runquery($db_link,$DB_name,'INSERT INTO products_images (ID_ORG,img_ext,Ordem,URL_Source) VALUES ("'.$produto_id.'","'.end($ext).'",1,"'.$url.'")');
										}
									}
								}

								// ETIQUETA ENERGETICA
								if($prod_EtiquetaEnergia != '' && $importar_etiquetas == 1)
								{
									$url = $prod_EtiquetaEnergia;
									$ext = explode('.',$url);
									$ee = $site_shell_url.'files/products/EE/'.$produto_id.'_1.'.end($ext);
									// VAMOS VERIFICAR SE A IMAGEM JÁ EXISTE DO NOSSO LADO
									if(!file_exists($ee) && !file_exists($site_shell_url.'files/products/EE/'.$produto_id.'_1.lnk'))
									{
										file_put_contents($ee, file_get_contents($url));
										$exts = end($ext);
										$res = Runquery($db_link,$DB_name, 'UPDATE products SET ee_ext="'.$exts.'", u_imagetiq="'.$produto_id.'_1.pdf" WHERE ID="'.$produto_id.'"');

									}
								} else $res = Runquery($db_link,$DB_name, 'UPDATE products SET ee_ext="" WHERE ID="'.$produto_id.'"');

								// FICHA UNIAO EUROPEIA
								if($prod_FichaUE != '' && $importar_fichaeua == 1)
								{
									$url = $prod_FichaUE;
									$ext = explode('.',$url);
									$ue = $site_shell_url.'files/products/UE/'.$produto_id.'_1.'.end($ext);

									// VAMOS VERIFICAR SE A IMAGEM JÁ EXISTE DO NOSSO LADO
									if(!file_exists($ue) && !file_exists($site_shell_url.'files/products/UE/'.$produto_id.'_1.lnk'))
									{
										file_put_contents($ue, file_get_contents($url));
										$exts = end($ext);
										$res=Runquery($db_link,$DB_name, 'UPDATE products SET ue_ext="'.$exts.'" WHERE ID="'.$produto_id.'"');
									}
								} else $res=Runquery($db_link,$DB_name, 'UPDATE products SET ue_ext="" WHERE ID="'.$produto_id.'"');
								// DESENHOS TÉCNICOS

								if($prod_DesenhosTecnicos!='' && $importar_desenhos == 1)
								{
									$url = $prod_DesenhosTecnicos;
									$ext = explode('.',$url);
									$dt = $site_shell_url.'files/products/DT/'.$produto_id.'_1.'.end($ext);

									// VAMOS VERIFICAR SE A IMAGEM JÁ EXISTE DO NOSSO LADO
									if(!file_exists($dt) && !file_exists($site_shell_url.'files/products/DT/'.$produto_id.'_1.lnk'))
									{
										file_put_contents($dt, file_get_contents($url));
										$exts = end($ext);
										$res = Runquery($db_link,$DB_name, 'UPDATE products SET dt_ext="'.$exts.'" WHERE ID="'.$produto_id.'"');

									}
								} else $res = Runquery($db_link,$DB_name, 'UPDATE products SET dt_ext="" WHERE ID="'.$produto_id.'"');

								//if($prod_ProdAss == '' && $importar_precos == 1)
								if($importar_precos == 1)
								{
									/*$obj_pvp_old = 0;
									$rs3->db_open($db_link,$DB_name, 'SELECT obj_pvp FROM products_objs WHERE obj_type_id="'.$produto_id.'"');
									if(!$rs3->EOF()) $obj_pvp_old = floatval($rs3->db_data['obj_pvp']);
									$rs3->db_close();*/

									if($preco_revenoff_prod == 1) $val_p = floatval(aplicarIVA($prod_Preco_Original));
									elseif($preco_revenoff_fam == 1) $val_p = floatval(aplicarIVA($prod_Preco_Original));
									elseif($preco_revenoff_marca == 1) $val_p = floatval(aplicarIVA($prod_Preco_Original));
									else $val_p = floatval(aplicarMargem($prod_Preco));

									//$perc_verfica = ((($obj_pvp_old - $val_p ) / ($obj_pvp_old)) * 100);

									if($id_revendedor > 0)
									{
										$news_l = 1;
										$val_promo = 0;
									}
									else
									{
										/*if($val_p < $obj_pvp_old && $perc_verfica > 60)
										{
											$val_p2 = $val_p;
											$val_p = $obj_pvp_old;
											$val_promo = 0;
										}
										elseif($val_p < $obj_pvp_old && $perc_verfica <= 60 && $val_p > 0)
										{
											$news_l = 1;
											$val_promo = $val_p;
											$val_p = $obj_pvp_old;
											$Data_ultimo_desconto = date('Y-m-d');
											$Desconto_percentagem = $perc_verfica;
										}
										else
										{
											$news_l = 1;
										}

										if($val_promo != $val_p && $val_promo > 0) $promo = 1; else $promo = 0;*/

										$promo = 0;
									}

									/*$sql = 'INSERT INTO products_objs (obj_type_id,obj_type,obj_stock,obj_iva,obj_pvp,obj_pvp2,obj_promo,obj_promo_ativo,obj_ativo,obj_val,obj_destaque,obj_novo,obj_novo2,obj_outlet,obj_qtdmin,obj_ecotaxas) VALUES ("'.$produto_id.'","products","'.$prod_Stock.'","'.$iva_default.'","'.$val_p.'","'.$val_p2.'","'.$val_promo.'","'.$promo.'",1,1,"'.$prod_Destaque.'","'.$prod_Novidade.'","'.$news_l.'","'.$outlet.'","'.$prod_QtdMinEnc.'","'.addslashes($prod_Ecotaxas).'")';
									$res = Runquery($db_link,$DB_name,$sql);
									$obj_id = db_get_last_insert_id($db_link);
									$sql = 'INSERT INTO products_objs_lang (ID_PAI,Cabecalho,Lang) VALUES ("'.$obj_id.'","'.$prod_Referencia.'","pt")';
									$res = Runquery($db_link,$DB_name,$sql);*/

									update_products_precos($produto_id,$val_p,$prod_QtdMinEnc,$prod_Stock,$prod_Referencia, 'EAN', 0, ($prod_Stock !='' ? 1 : 0),$Supplier_ID,0,$iva_default,$prod_Preco);

									fwrite($fh,$ll.'% - ('.$linha.' de '.$t.') - NOVO PRODUTO: '.$produto_id.', Ref: '.$prod_Referencia.", Preço: $val_p, PC:$prod_Preco, Stock: $prod_Stock \n");

								}

								//Outras sugestoes
								$Sugestoes = $prod_Sugestoes;
								if(is_array($Sugestoes) && !empty($Sugestoes) && $importar_relacionados == 1)
								{
									for ($i=0; $i < count($Sugestoes); $i++)
									{
										$rs2->db_open($db_link,$DB_name, 'SELECT ID FROM products WHERE ID_Integracao="'.$Sugestoes[$i].'"');
										if(!$rs2->EOF())
										{
											$prod_id2 = $rs2->db_data['ID'];
											$rs3->db_open($db_link,$DB_name, 'SELECT ID_Prod FROM produtos_sugestoes WHERE ID_Prod="'.$produto_id.'" AND ID_Prod2="'.$prod_id2.'"');
											if($rs3->EOF())
											{
												$res = Runquery($db_link,$DB_name,'INSERT INTO produtos_sugestoes (ID_Prod, ID_Prod2) VALUES ("'.$produto_id.'", "'.$prod_id2.'")');
											}
											$rs3->db_close();
										}
										$rs2->db_close();
									}
								}

								//Filtros
								$filtros_rel2 = '';
								if(isset($prod_Caracteristicas) && !empty($prod_Caracteristicas) && $cria_filtros==1)
								{
									foreach($prod_Caracteristicas as $filtros)
									{
										$IDCategoria = $filtros['IDCategoria'];
										$Categoria = $filtros['Categoria'];
										$IDValor = $filtros['IDValor'];
										$Valor = $filtros['Valor'];
										$f_od1 = $filtros['f_od1'];
										$f_od2 = $filtros['f_od2'];
										$v_f_od1 = $filtros['v_f_od1'];
										$v_f_od2 = $filtros['v_f_od2'];
										$f_ativo = $filtros['f_ativo'];

										$filters_data = filtros_atualiza($produto_id,$IDCategoria,$Categoria,$IDValor,$Valor);

										$filtros_rel2 .= $filters_data['category_id'].'_'.$filters_data['value_id'].';';
										$fams = niveis_fams_f($sub_fam_id);

										filtros_produtos_atualiza_imp($filters_data['category_id'],$filters_data['value_id'],$produto_id,$fams,$prod_Marca_id,$filtros_rel2,$f_od1,$f_od2,$v_f_od1,$v_f_od2,$f_ativo);
									}
								}

								/*if($prod_Marca['ID'] > 0 && $cria_marcas == 1)
								{
									$filters_data = filtros_atualiza($produto_id, 1, 'Marca',$prod_Marca['ID'],$prod_Marca['Valor'], 1);
									$filtros_rel2 .= $filters_data['category_id'].'_'.$filters_data['value_id'].';';
									$fams = niveis_fams_f($sub_fam_id);
									filtros_produtos_atualiza_imp($filters_data['category_id'],$filters_data['value_id'],$produto_id,$fams,$prod_Marca_id,$filtros_rel2, 0, 0, 0, 0, 1);
								}*/

								if($cria_filtros == 1)$res = Runquery($db_link,$DB_name, 'UPDATE filters_products SET filtros_rel2="'.$filtros_rel2.'" WHERE prod_id="'.$produto_id.'"');

								$cont2++;
								if($log) echo ($cont+$cont2).': Inseriu Produto: '.$prod_Referencia.'<br>';
							}
						}
					}

					if($produto_id > 0 && $sub_fam_id > 0 && $prod_Marca_id > 0 && $cria_filtros==1)
					{
						if($DEBUG==1) echo "products_objs_filtros_save($ID)\n";

						products_objs_filtros_save($ID);
						//gera_produtos($produto_id,$sub_fam_id,$prod_Marca_id, 1);
					}
				}
				//die("...");
				$linha++;
			}


			// SÓ NO FIM GERAMOS OS FILTROS PARA MARCAS / FAMILIAS
			//gera_produtos_filtros('pt');

			//$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);




			// ESCONDE REGISTOS QUE NÃO ESTEJAM NESTE FICHEIRO
			//$res = Runquery($db_link,$DB_name, 'UPDATE filters_categories SET category_active = 0 WHERE category_flag = 0 AND category_id_import > 0');
			//$res = Runquery($db_link,$DB_name, 'UPDATE filters_values SET value_active = 0 WHERE value_flag = 0 AND value_id_import > 0');

			// O script nao pode esconder artigos

			if($apenas_macorlux==1)
			{
				$res = Runquery($db_link,$DB_name, 'UPDATE products SET Aproved=1 WHERE Flag=1 and Aproved_ass=0 AND Sync = 0');
				$res = Runquery($db_link,$DB_name, 'UPDATE products SET Aproved=0 WHERE Flag=0 AND Sync = 0');
			}
			/*$res = Runquery($db_link,$DB_name, 'UPDATE products SET Aproved=1 WHERE Flag=1 and Aproved_ass=0 AND ID_Integracao = '.$Supplier_ID.' AND Sync = 0');
			$res = Runquery($db_link,$DB_name, 'UPDATE products SET Aproved=0 WHERE Flag=0 AND ID_Integracao = '.$Supplier_ID.' AND Sync = 0');*/

			//			if($cria_produtos==1)$res = Runquery($db_link,$DB_name, 'UPDATE products SET Aproved=1 WHERE Flag=1 and Aproved_ass=0 AND ID_Integracao = '.$Supplier_ID.' AND Sync = 0');
			//			if($cria_produtos==1)$res = Runquery($db_link,$DB_name, 'UPDATE products SET Aproved=0 WHERE Flag=0 AND ID_Integracao > 0 AND Sync = 0');


			//$res = Runquery($db_link,$DB_name, 'UPDATE menus_filtros SET menu_visivel=0 WHERE menu_flag=0 AND ID_Integracao > 0 AND menu_data != "0000-00-00 00:00:00"');
			//$res = Runquery($db_link,$DB_name, 'DELETE FROM filters_products WHERE filtro_active=0 AND data_atualizacao != "0000-00-00 00:00:00"');

			//if($cria_filtros == 1)$res = Runquery($db_link,$DB_name, 'DELETE FROM filters_products WHERE FLAG=0 AND data_atualizacao != "0000-00-00 00:00:00"');


			/*if($cria_familias == 1)$res = Runquery($db_link,$DB_name, 'UPDATE products_families SET visivel=0 WHERE FLAG=0 AND ID_Integracao > 0');
			if($cria_marcas ==1 )$res = Runquery($db_link,$DB_name, 'UPDATE brands SET Visivel=0 WHERE FLAG=0 AND ID_Integracao > 0');
			if($cria_filtros == 1)$res = Runquery($db_link,$DB_name, 'UPDATE filters_products SET filtro_active=0 WHERE FLAG=0 AND data_atualizacao != "0000-00-00 00:00:00"');*/

			// AGORA QUE JÁ IMPORTOU TUDO VAMOS REMOVER OS FICHEIROS DE CACHE QUE NÃO SE ENCONTRAM ATIVOS
			$total_p = 0; $del = 0;



			/*
			if($cria_filtros == 1 || $cria_produtos==1)
			{
				global $DB_host,$DB_user,$DB_pass,$DB_name;
				$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
				$rs = new DataObj;
				$sql = 'SELECT ID FROM products WHERE Aproved=0';
				$rs->db_open($db_link,$DB_name,$sql);
				if(!$rs->EOF())
				{
					while(!$rs->EOF())
					{
						foreach (glob($GLOBALS['site_shell_url']."files/filtros/*_p".$rs->db_data['ID']."_pt.json") as $filename)
						{
							@unlink($filename);	$del++;
						}

						$rs->db_next();
					}
				}
				$rs->db_close();
			}
			*/



			echo '<br>Total Prods: ' . $total_p.'<br />';
			echo 'Removeu: ' . $del.'<br />';
			echo 'Fez '.$cont2.' INSERTS e '.$cont.' UPDATES Total de '.($cont+$cont2)."<br>\n";

			// VAMOS LIMPAR TODOS OS FICHEIRO NA PASTA IMPORTADO
			/*foreach (glob($GLOBALS['site_shell_url']."files/importado/*.txt") as $filename_imp)
			{
				@unlink($filename_imp);
			}*/

			// ATUALIZAR LOG DE INTEGRACAO
			$res = Runquery($db_link,$DB_name,'UPDATE z_integracao_api SET DATA_FIM = "'.date('Y-m-d H:i:s').'", IMPORTADO=1 WHERE FICHEIRO="'.$filename_del.'"');
			if(@copy($filename,$filename_importado)) @unlink($filename);

	 	}
	 	else echo "ERRO nao conseguio ler produtos";
	}
}

fclose($fh);
function filtros_produtos_atualiza_imp($cat_id=0,$value_id=0,$prod_id=0,$fams,$prod_Marca_id=0,$filtros_rel2='',$fam_ordem1,$fam_ordem2,$v_fam_ordem1,$v_fam_ordem2,$f_ativo)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;

	$c=count($fams);
	$i=0;
	$and='';

	if($i<$c)
	{
		$and=' AND (';
		while($i<$c)
		{
			if($and && $i>0)$and.=' OR ';
			$and.=' fam_id'.($i+1).'='.$fams[$i];
			$i++;
		}
		$and.=')';
	}

	$sql_ref='SELECT * FROM filters_products WHERE cat_id="'.$cat_id.'" AND valor_id="'.$value_id.'" AND prod_id="'.$prod_id.'"'.$and;

	$rs->db_open($db_link,$DB_name,$sql_ref);
	if(!$rs->EOF())
	{
		$sql = '
				UPDATE filters_products
				SET
					cat_id="'.$cat_id.'",
					valor_id="'.$value_id.'",
					fam_id1="'.$fams[0].'",
					fam_id2="'.$fams[1].'",
					fam_id3="'.$fams[2].'",
					fam_id4="'.$fams[3].'",
					marca_id="'.$prod_Marca_id.'",
					fam_ordem1="'.$fam_ordem1.'",
					fam_ordem2="'.$fam_ordem2.'",
					v_fam_ordem1="'.$v_fam_ordem1.'",
					v_fam_ordem2="'.$v_fam_ordem2.'",
					FLAG=1,
					filtro_active=1,
					data_atualizacao="'.date('Y-m-d H:i:s').'"
				WHERE cat_id="'.$cat_id.'" AND valor_id="'.$value_id.'" AND prod_id="'.$prod_id.'"'.$and;
		$res=Runquery($db_link,$DB_name,$sql);
	}
	else
	{
		$sql = 'INSERT INTO filters_products(cat_id,valor_id,prod_id,fam_id1,fam_id2,fam_id3,fam_id4,marca_id,FLAG,filtro_active,data_atualizacao,fam_ordem1,fam_ordem2,v_fam_ordem1,v_fam_ordem2)
				VALUES ("'.$cat_id.'","'.$value_id.'","'.$prod_id.'","'.$fams[0].'","'.$fams[1].'","'.$fams[2].'","'.$fams[3].'","'.$prod_Marca_id.'","1","1","'.date('Y-m-d H:i:s').'","'.$fam_ordem1.'","'.$fam_ordem2.'","'.$v_fam_ordem1.'","'.$v_fam_ordem2.'")';
		$res = Runquery($db_link,$DB_name,$sql);
	}
	$rs->db_close();

	if($res && $fams[0] > 0 && ($fam_ordem1 > 0 || $v_fam_ordem1 > 0)) filtros_menus($cat_id,$value_id,$fams[0],$fam_ordem1,$v_fam_ordem1,$f_ativo);
	if($res && $fams[1] > 0 && ($fam_ordem2 > 0 || $v_fam_ordem2 > 0)) filtros_menus($cat_id,$value_id,$fams[1],$fam_ordem2,$v_fam_ordem2,$f_ativo);
	if($res && $fams[2] > 0 && ($fam_ordem2 > 0 || $v_fam_ordem2 > 0)) filtros_menus($cat_id,$value_id,$fams[2],$fam_ordem2,$v_fam_ordem2,$f_ativo);
	if($res && $fams[3] > 0 && ($fam_ordem2 > 0 || $v_fam_ordem2 > 0)) filtros_menus($cat_id,$value_id,$fams[3],$fam_ordem2,$v_fam_ordem2,$f_ativo);
}

function filtros_menus($cat_id,$value_id,$fam_id,$fam_ordem,$v_fam_ordem,$f_ativo)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;

	$sql = 'SELECT menu_fam_id FROM menus_filtros WHERE menu_fam_id="'.$fam_id.'" AND menu_cat_id="'.$cat_id.'" AND menu_valor_id=0';
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$sql ='UPDATE menus_filtros SET menu_data="'.date('Y-m-d H:i:s').'", ID_Integracao="'.$cat_id.'", menu_flag=1, menu_ordem="'.$fam_ordem.'", menu_cat_nome="'.buscacategoriafiltros($cat_id).'", menu_visivel="1" WHERE menu_fam_id="'.$fam_id.'" AND menu_cat_id="'.$cat_id.'" AND menu_valor_id=0';
		$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
	}
	else
	{
		$sql ='INSERT INTO menus_filtros(ID_Integracao, menu_fam_id, menu_ordem, menu_cat_id, menu_cat_nome, menu_visivel, menu_flag, menu_data) VALUES("'.$cat_id.'", "'.$fam_id.'", "'.$fam_ordem.'", "'.$cat_id.'", "'.buscacategoriafiltros($cat_id).'", "'.$f_ativo.'", 1, "'.date('Y-m-d H:i:s').'")';
		$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
	}
	$rs->db_close();


	$sql = 'SELECT menu_fam_id FROM menus_filtros WHERE menu_fam_id="'.$fam_id.'" AND menu_cat_id="'.$cat_id.'" AND menu_valor_id="'.$value_id.'"';
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$sql ='UPDATE menus_filtros SET menu_data="'.date('Y-m-d H:i:s').'", ID_Integracao="'.$value_id.'", menu_flag=1, menu_ordem="'.$v_fam_ordem.'", menu_cat_nome="'.buscacategoriafiltros($cat_id).'", menu_valor_nome="'.buscavalorfiltros($value_id).'", menu_visivel="'.$f_ativo.'" WHERE  menu_fam_id="'.$fam_id.'" AND menu_cat_id="'.$cat_id.'" AND menu_valor_id="'.$value_id.'"';
		$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
	}
	else
	{
		$sql ='INSERT INTO menus_filtros(ID_Integracao, menu_fam_id, menu_ordem, menu_cat_id, menu_cat_nome, menu_valor_id, menu_valor_nome, menu_visivel, menu_flag,menu_data) VALUES("'.$value_id.'", "'.$fam_id.'", "'.$v_fam_ordem.'", "'.$cat_id.'", "'.buscacategoriafiltros($cat_id).'", "'.$value_id.'", "'.buscavalorfiltros($value_id).'", "'.$f_ativo.'", 1, "'.date('Y-m-d H:i:s').'")';
		$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
	}
	$rs->db_close();
}

function niveis_fams_f($id_org=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$pai=$id_org;
    while($pai > 0)
	{
		$sql='SELECT ID,ID_ORG FROM products_families WHERE ID="'.$pai.'"';
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			if($rs->db_data['ID_ORG']==$pai)$pai=0;
			else $pai=$rs->db_data['ID_ORG'];
			$pai2=$rs->db_data['ID'];
			$nivel[]=$pai2;
		}
		else $pai=0;
		$rs->db_close();
	}
	for($a=(count($nivel)-1); $a>=0; $a--)
	{
		$nivel2[]=$nivel[$a];
	}
	return $nivel2;
}
function getBrandsimg($prod_Marca_id=0,$prod_Marca_id_macor=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

	if($prod_Marca_id > 0 && $prod_Marca_id_macor > 0)
	{
		$ext = '';

		$url = 'http://www.macorlux.pt/img_upload/marcas/'.$prod_Marca_id_macor.'_2.jpg';
		$imageArray = @getimagesize($url);
		if(!$imageArray[0])
		{
			$url = 'http://www.macorlux.pt/img_upload/marcas/'.$prod_Marca_id_macor.'_2.png';
			$imageArray = @getimagesize($url);
			if(!$imageArray[0])
			{
				$url = 'http://www.macorlux.pt/img_upload/marcas/'.$prod_Marca_id_macor.'_2.gif';
				$imageArray = @getimagesize($url);
			}
		}

		if(!empty($imageArray[0]))
		{
			$ext = pathinfo($url, PATHINFO_EXTENSION);
			$img = $GLOBALS['site_shell_url'].'files/brands/'.$prod_Marca_id.'_1.'.$ext;

			if(!file_exists($img)) file_put_contents($img, file_get_contents($url));
			$sql = 'UPDATE brands SET img_ext="'.$ext.'" WHERE ID="'.$prod_Marca_id .'"';
			$res = Runquery($db_link,$DB_name,$sql);
		}
	}
}
function aplicarIVA($preco_revendedor)
{
	// global $iva_default;
	// $iva=1+(floatval($iva_default)/100);
	// $preco_revendedor *= $iva; //PRECO C/IVA
	return $preco_revendedor;
}
function aplica_margens_valor($valor=0)
{
	// +7% nos produtos de valor entre 198.01€ e 298€
	// +6% nos produtos entre 298.01€ e 498 €
	// +4% entre 498.01€ e 998€
	// +5%  de 998.01€ a 1498€
	// +4% e 3% acima de qualquer  produto  de valor superior a 1498.01€.

	if($valor >= 1498.01){$valor *= 1.03;}
	elseif($valor >= 998.01 && $valor < 1498) $valor *= 1.05;
	elseif($valor >= 498.01 && $valor < 998) $valor *= 1.04;
	elseif($valor >= 298.01 && $valor < 498) $valor *= 1.06;
	elseif($valor >= 198.01 && $valor < 298) $valor *= 1.07;
	else $valor *= 1.08;

	return $valor;
}
function aplicarMargem($preco_revendedor)
{
	global $iva_default;
	// if($preco_revendedor>=1000)
	// {
		// $preco_revendedor *= 1.01;
		// $preco_revendedor *= 1.005;
	// }
	// else
	// {
		// $preco_revendedor *= 1.02;
		// $preco_revendedor *= 1.01;
	// }
	$iva=1+(floatval($iva_default)/100);
	// $preco_revendedor *= 1.02; //Margem cliente
	// $preco_revendedor = ($preco_revendedor + (5*1.23)); //PRECO C/IVA
	$preco_revendedor *= $iva; //PRECO C/IVA
	return $preco_revendedor;
}

// Margem da memogadget
function aplicarMargem_ret($preco_revendedor)
{
	// if($preco_revendedor>=1000)
	// {
		// $preco_revendedor = 1.005;
	// }
	// else
	// {
		// $preco_revendedor = 1.01;
	// }
	return $preco_revendedor;
}
function ValorPagarMargens($valor)
{
	// 1 - valor + margem memogadget------------------
	$valor_memo=$valor * aplicarMargem_ret($valor);
	// 2 - valor + margem cliente + margem memogadget------------------
	// MARGEM CLIENTE
	// $valor *= 1.02;
	// MARGEM MEMOGADGET
	$valor *= aplicarMargem_ret($valor);
	// valor a pagar
	if($valor==$valor_memo) $valor_pagar=($valor_memo * (aplicarMargem_ret($valor)-1));
	else $valor_pagar=($valor - $valor_memo);
	return $valor_pagar;
}
/**
 * Converte o PDF num ficheiro mais pequeno
 * @param  string $input caminho completo do ficheiro a ser convertido
 */
function convertePDF($input){
	/***** COVERTE O PDF NUM MAIS PEQUENO *****/
	$output = $input.'.temp';
	exec('gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/screen -dNOPAUSE -dQUIET -dBATCH -sOutputFile='.$output.' '.$input,$temp);
	unlink($input);
	rename($output,$input);
}

/**
 * Converts a PDF to a Jpeg
 * @param  string $input  path of the file to be converted
 * @param  string $output converted file path
 * @return bool           if it was converted with success;
 */
function createJPGfromPDF($input,$output){
    $quality=80;
    $res='150x150';
    exec("'gs' '-dNOPAUSE' '-sDEVICE=jpeg' '-dUseCIEColor' '-dTextAlphaBits=4' '-dGraphicsAlphaBits=4' '-o$output' '-r$res' '-dJPEGQ=$quality' '$input'",$output);
    if(count($output)){
    	return true;
    }
    return false;
}

function products_objs_filtros_save($ID_PROD=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$objv_id=$_POST['c_obj_id'];
	$obj_iva=$_POST['c_obj_iva'];
	$obj_preco=$_POST['c_obj_preco'];
	$obj_stock=$_POST['c_obj_stock'];
	$obj_promo=$_POST['c_obj_promo'];
	$obj_combo=$_POST['c_obj_combo'];
	$obj_ref=$_POST['c_obj_ref'];
	$rs=new DataObj; $rs2=new DataObj;
	$sql="UPDATE products_objs_combo SET obj_val=0 WHERE obj_type='products'AND obj_type_id='".$ID_PROD."'";

	$res=Runquery($db_link,$DB_name,$sql);
	for($a=0; $a<count($objv_id); $a++)
	{
		$sql="SELECT obj_id FROM products_objs_combo WHERE obj_type='products' AND obj_type_id='".$ID_PROD."' AND obj_id='".$objv_id[$a]."' AND obj_combo='".$obj_combo[$a]."'";
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$obj_id=$rs->db_data['obj_id'];

			$sql="UPDATE products_objs_combo
					SET
						obj_iva='".$obj_iva[$a]."',
						obj_pvp='".$obj_preco[$a]."',
						obj_stock='".$obj_stock[$a]."',
						obj_promo='".$obj_promo[$a]."',
						obj_promo_ativo='".$obj_p_ativo[$a]."',
						obj_combo='".$obj_combo[$a]."',
						obj_ref='".$obj_ref[$a]."',
						obj_val=1
					WHERE
						obj_id='".$obj_id."'
						AND obj_type='products'
						AND obj_type_id='".$ID_PROD."'";

			$res=Runquery($db_link,$DB_name,$sql);
		}
		else
		{
			$sql="INSERT INTO
						products_objs_combo
						(
							obj_type,
							obj_type_id,
							obj_iva,
							obj_pvp,
							obj_stock,
							obj_promo,
							obj_combo,
							obj_ref,
							obj_val
						)
						VALUES
						(
							'products',
							'".$ID_PROD."',
							'".$obj_iva[$a]."',
							'".$obj_preco[$a]."',
							'".$obj_stock[$a]."',
							'".$obj_promo[$a]."',
							'".$obj_combo[$a]."',
							'".$obj_ref[$a]."',
							1
						)
					";
			$res=Runquery($db_link,$DB_name,$sql);
			$obj_id=db_get_last_insert_id($db_link);
		}
		$rs->db_close();
	}
	$sql="DELETE FROM products_objs_combo WHERE obj_type='products'AND obj_type_id='".$ID_PROD."'AND obj_val=0 ";
	$res=Runquery($db_link,$DB_name,$sql);
}

	// FICHEIRO DE LOG
	$log_activation=1;
	if($log_activation==1) // if logs are active
	{
		if(copy($myFile, str_replace('importar/', 'importado/', str_replace('analisar_csv'.$Supplier_ID.'.txt', 'analisar_csv'.$Supplier_ID.'_ok'.date('YmdHis').'.txt',$myFile))))
		{
			unlink($myFile);
		}
	}
	else
	{
		unlink($myFile);
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


	$sql = 'UPDATE a_importador_stats set DataEnd="'.date('Y-m-d H:i:s').'",TotalProdsAtivos = "'.$total_p.'",TotalProdsI = "'.$cont2.'",TotalProdsU = "'.$cont.'",TotalProdsD = "'.$del.'" WHERE ID = '.$a_importador_stats_id;
	$res = Runquery($db_link,$DB_name,$sql);

	//Limpa cache
	global $site_shell_url;
	exec('rm -f '.$site_shell_url.'files/cache/*.*');
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
	exec('rm -f '.$site_shell_url.'files/filtros/*1.*');
	exec('rm -f '.$site_shell_url.'files/filtros/*2.*');
	exec('rm -f '.$site_shell_url.'files/filtros/*3.*');
	exec('rm -f '.$site_shell_url.'files/filtros/*4.*');
	exec('rm -f '.$site_shell_url.'files/filtros/*5.*');
	exec('rm -f '.$site_shell_url.'files/filtros/*6.*');
	exec('rm -f '.$site_shell_url.'files/filtros/*7.*');
	exec('rm -f '.$site_shell_url.'files/filtros/*8.*');
	exec('rm -f '.$site_shell_url.'files/filtros/*9.*');
	exec('rm -f '.$site_shell_url.'files/filtros/*0.*');
	exec('rm -f '.$site_shell_url.'files/tmp/*.*');
?>