<?php

if(isset($argv) && $argv[1] > 0)$Supplier_ID=intval($argv[1]);
if(isset($_GET['Supplier_ID']))$Supplier_ID=intval($_GET['Supplier_ID']);

$Supplier_ID=4;//apenas imagens

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
	if($json=file_get_contents('https://fd-distribution.pl/xml-porownywarki/hurt/csv-eur.php?h=2dbcfca2ead8ed1169290664e5c9c8'))
	{
		echo "1.2 A Fazer download do json ".'json_'.date('Ymd').'_'.$id_revendedor.".txt<br>\n";
		fwrite($fh, "1.2 A Fazer download do json ".'json_'.date('Ymd').'_'.$id_revendedor.".txt\n");

		file_put_contents($filename, $json);
	}
}
else
{
	fwrite($fh,"1.4 A importar o ficheiro $filename\n");

	echo "1.4 A importar o ficheiro $filename<br>\n";
	$json=file_get_contents($filename);
}

var_dump($json);


die();


//****************************************************
fwrite($fh, "Fase 2, comecar analise...\n");
//****************************************************


$json = json_decode($json, true);
$cont = 0; $cont2 = 0;
$rs = new DataObj; $rs2 = new DataObj; $rs3 = new DataObj;
global $DB_host,$DB_user,$DB_pass,$DB_name;
$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);



$sql = 'INSERT INTO a_importador_stats (ID_ORG,DataStart) values ("'.$Supplier_ID.'","'.date('Y-m-d H:i:s').'")';
$res = Runquery($db_link,$DB_name,$sql);
$a_importador_stats_id=db_get_last_insert_id($db_link);


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



//********************************************************************************************************
//**************************************************** GRAVAR LOG COM DATA DE INICIO DA IMPORTACAO
//********************************************************************************************************
$sql = 'INSERT INTO z_integracao_api(DATA_INI, DATA_FIM, FICHEIRO, IMPORTADO) VALUES("'.date('Y-m-d H:i:s').'", "0000-00-00 00:00:00", "'.$filename.'", 0)';
$res = Runquery($db_link,$DB_name,$sql);


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
						$udtime =  date("Y-m-d H:i:s");
						$importar_imagens=1;

						if($importar_imagens==1)
						{
							if($DEBUG==1) echo "IMPORTAR IMAGENS\n";

							if($prod_Imagem!='')
							{
								echo "IMPORTAR IMAGEM 1\n";

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

								die();
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

						if($log) echo ($cont+$cont2).': Update Produto: '.$prod_Referencia.'<br>';
						$cont++;
					}
					else
					{
						//nao faz nada
					}

				}
				//die("...");
				$linha++;
			}

			$total_p = 0; $del = 0;


			echo 'Fez '.$cont2.' INSERTS e '.$cont.' UPDATES Total de '.($cont+$cont2)."<br>\n";

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