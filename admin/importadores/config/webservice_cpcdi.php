<?php
// phpinfo();
// die();
$Supplier_ID=5;
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
	require_once $path.'db_engine_pdo.php';
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
	require_once('settings_crons.inc.php');
	require_once('funcoes.inc.php');

	$path2 = dirname( __FILE__ ).'/../../../';
	include $path2.'admin/products/filtros/funcoes.inc.php';




//****************************************************    ALGUMAS REGRAS:
//
//
//

$familia_default=9448;
$campo_chave_ean=1;
$cria_filtros=1;

$cria_familias=0;
$cria_produtos=1;
$cria_marcas=0;
//$importar_imagens=1;
$importar_imagens=0;
$importar_precos=0;
$importar_relacionados=0;
$campo_chave_ref=0;
$muda_prod_familia=0;
$importar_etiquetas=1;
$importar_fichaeua=1;
$importar_desenhos=1;

//****************************************************


if(!isset($_GET['log'])) $log = 0;
error_reporting(E_ERROR);
// error_reporting(E_ALL);
ini_set('display_errors',-1);

header('Content-Type: text/html; charset=utf-8');
ini_set('max_execution_time','60000');
ini_set('max_input_time','60000');
ini_set('memory_limit','-1');






/*$myFile = dirname( __FILE__ )."/../../files/importar/atualizar_sync.txt";
$fh = fopen($myFile, 'a+');
fwrite($fh, $filename."\n");
chmod($myFile, 0755);*/

// $myFile = dirname( __FILE__ )."/../../../files/importar/ws_cpcdi_".date('YmdHis').".txt";
$myFile = "/var/www/vhosts/mhr.pt/httpdocs"."/files/importar/ws_cpcdi_".date('YmdHis').".txt";
// $myFile = $default_folder."/files/importar/ws_cpcdi_".date('YmdHis').".txt";
// if(file_exists($myFile))
// 	echo ('<br>file exists - '.$myFile);
// else
// 	echo ('<br>file does NOT exist - '.$myFile);
$fh = fopen($myFile, 'a+');
// echo ('<br>file handler - '.$fh);
$res=fwrite($fh, "a comecar analise...\n");
// var_dump('<br>fwrite result: '.$res);

// if(file_exists($myFile))
// 	echo ('<br>file created - '.$myFile);
// else
// 	echo ('<br>file NOT created - '.$myFile);


// send_email('aderito@memogadget.com','webservice ~macorlux','comecei a importar');
$json = str_replace('99999999999999999999', '', $json);
$json = json_decode($json, true);
$cont = 0; $cont2 = 0;
$rs = new DataObj; $rs2 = new DataObj; $rs3 = new DataObj;
global $DB_host, $DB_user, $DB_pass, $DB_name;
$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);


// get configuracoes
$sql='SELECT * FROM a_importador_configuracoes WHERE conf_id='.$Supplier_ID;
$rs->db_open($db_link, $DB_name, $sql);
if(!$rs->EOF())
{
	// var_dump($rs->db_data);

	$cfg_webservice = array(
		"ficheiro_caminho_api" => $rs->db_data["ficheiro_caminho_api"],
		"ficheiro_caminho_api_login" => $rs->db_data["ficheiro_caminho_api_login"],
		"ficheiro_caminho_api_pass" => $rs->db_data["ficheiro_caminho_api_pass"]
	);
}
// var_dump($cfg_webservice);
// die();






//****************************************************
// VAMOS VERIFICAR SE OS CAMPOS EXISTEM TODOS NA BASE DE DADOS
// echo $DB_name,'-products_images-',$db_link."...\n";
$fields = db_list_fields($DB_name,'products',$db_link);
$columns = db_num_fields($fields);
for($i=0; $i < $columns; $i++){$field_array[] = db_field_name($fields,$i);}
if(!in_array('Ref_secundaria', $field_array))$res = Runquery($db_link,$DB_name,'ALTER TABLE products ADD Ref_secundaria Varchar(255)');
if(!in_array('Ref_Ean', $field_array))$res = Runquery($db_link,$DB_name,'ALTER TABLE products ADD Ref_Ean Varchar(255)');
if(!in_array('u_coretiq', $field_array))$res = Runquery($db_link,$DB_name,'ALTER TABLE products ADD u_coretiq Varchar(255)');
if(!in_array('u_catetiq', $field_array))$res = Runquery($db_link,$DB_name,'ALTER TABLE products ADD u_catetiq Varchar(255)');
if(!in_array('u_imagetiq', $field_array))$res = Runquery($db_link,$DB_name,'ALTER TABLE products ADD u_imagetiq Varchar(255)');

$fields = db_list_fields($DB_name,'products_images',$db_link);
$columns = db_num_fields($fields);
for($i=0; $i < $columns; $i++){$field_array[] = db_field_name($fields,$i);}
if(!in_array('URL_Source', $field_array))$res = Runquery($db_link,$DB_name,'ALTER TABLE products_images ADD URL_Source Varchar(255)');

$fields = db_list_fields($DB_name,'menus_filtros',$db_link);
$columns = db_num_fields($fields);
for($i=0; $i < $columns; $i++){$field_array[] = db_field_name($fields,$i);}
if(!in_array('menu_data', $field_array))$res = Runquery($db_link,$DB_name,'ALTER TABLE menus_filtros ADD menu_data DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\'');
if(!in_array('ID_Integracao', $field_array))$res = Runquery($db_link,$DB_name,'ALTER TABLE menus_filtros ADD ID_Integracao INT(11)');

$fields = db_list_fields($DB_name,'products_families',$db_link);
$columns = db_num_fields($fields);
for($i=0; $i < $columns; $i++){$field_array[] = db_field_name($fields,$i);}
if(!in_array('FLAG', $field_array))$res = Runquery($db_link,$DB_name,'ALTER TABLE products_families ADD `FLAG` TINYINT(1) NOT NULL ;');
if(!in_array('data_atualizacao', $field_array))$res = Runquery($db_link,$DB_name,'ALTER TABLE products_families ADD data_atualizacao DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\'');

$fields = db_list_fields($DB_name,'brands',$db_link);
$columns = db_num_fields($fields);
for($i=0; $i < $columns; $i++){$field_array[] = db_field_name($fields,$i);}
if(!in_array('FLAG', $field_array))$res = Runquery($db_link,$DB_name,'ALTER TABLE brands ADD `FLAG` TINYINT(1) NOT NULL ;');
if(!in_array('data_atualizacao', $field_array))$res = Runquery($db_link,$DB_name,'ALTER TABLE brands ADD data_atualizacao DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\'');

if(!in_array('flag_cpc_ws', $field_array))$res = Runquery($db_link,$DB_name,'ALTER TABLE products ADD flag_cpc_ws TINYINT( 1 ) NOT NULL ;');
if(!in_array('tstamp_cpc_ws', $field_array))$res = Runquery($db_link,$DB_name,'ALTER TABLE products ADD tstamp_cpc_ws INT( 11 ) NOT NULL ;');

//****************************************************






$produtos=[];
//**********************************************************************************************************************/
// PARA TORNAR A IMPORTAÇÃO MAIS RAPIDA, VAMOS CARREGAR PARA UM ARRAY TODOS OS PRODUTOS QUE ESTAO EM BASE DE DADOS
$ct_rep=0;
$sql='
	SELECT
		products_objs_history_day.ID_ORG,
		products_objs_history_day.Ref_loja,
		products.ID as products_ID,
		products.Ref,
		products.Ref_Ean,
		products.sync
	FROM `products_objs_history_day`
	left join products_objs
		on products_objs.obj_id=products_objs_history_day.ID_ORG
	left join products
		on products.ID=products_objs.obj_type_id
	WHERE flag_cpc_ws=0
	AND products_objs_history_day.data_i > \'2020-11-01\'
	AND products.ID IS NOT NULL
	AND Ref_loja!=""
	AND `Supplier_ID` = '.$Supplier_ID.'
	GROUP BY products_objs_history_day.Ref_loja
	ORDER BY `products_objs_history_day`.`Ref_loja` ASC
	LIMIT 150
';

$rs->db_open($db_link, $DB_name, $sql);
if($rs->EOF())
{
	// if there are not products to process
	// set the flag=0 on all of them
	$sql1='UPDATE LOW_PRIORITY products SET flag_cpc_ws=0, tstamp_cpc_ws=0 WHERE 1';
	$res = Runquery($db_link, $DB_name, $sql1);
	// then get products again
	$rs->db_open($db_link, $DB_name, $sql);
	echo "<br>Nenhum registo encontrado para processar - RESET DA FLAG...";
	fwrite($fh, "Nenhum registo encontrado para processar - RESET DA FLAG...\n");

}
while(!$rs->EOF())
{
	if(in_array($rs->db_data['Ref_loja'], $produtos))
		$ct_rep++;
	else
		$produtos[] = array(
			'Ref_loja' => $rs->db_data['Ref'],
			'products_ID' => $rs->db_data['products_ID'],
			'sync' => $rs->db_data['sync']
		);

	$rs->db_next();
}
$rs->db_close();

echo "<br>1.3 Produtos carregados para a memoria<br>\n";

echo '<br>repetidos='.$ct_rep.'<br>\n';
echo '<br>total prods='.count($produtos).'<br>\n';
// var_dump($produtos);

// die();

//**********************************************************************************************************************/

fwrite($fh, "encontrados ".count($produtos)." produtos para processar...\n");




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

$alertas=0;
$nao_existe=0;
$a_sync=0;
$xx=0;

$l=0;
// foreach($json as $produtos)
// {
// 	$l++;
// 	if($l==1) //Quero que corra apenas 1 vez
// 	{
		$t=count($produtos);
		if($t>0)
		{


			foreach($produtos as $prod)
			{
				ob_flush();
				flush();


				//if($xx>10)die("FIM");


				if(!$db_link) $db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);

				$ll = intval(($linha * 100 ) / $t);
				if($ll != $ll2)
				{
					$ll2 = $ll;
				}
				// if(!file_exists($myFile)) die("parou - fx nao existe $myFile");

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


				$prod_Referencia=$prod['Ref_loja'];
				$prod_Ref_Ean=$prod['Ref_Ean'];
				$produto_id=$prod['products_ID'];
				$sync=$prod['sync'];



				echo '<br><hr><br> | t:'.$t;

				$t--;

				echo ' | ref:'.$prod_Referencia.' | id:'.$produto_id;


				fwrite($fh, "Faltam $t artigos para terminar - REF: $prod_Referencia - ID: $produto_id \n");
				//****************************************************



				if($produto_id > 0 ) // O PRODUTO JA EXISTE
				{

					// SE VIA BACKOFFICE TIRARMOS A SINCRONISAÇÃO ESTA FLAG FICA IGUAL A 1
					$udtime =  date("Y-m-d H:i:s");

					if($sync=='0')
					{

						// read stock data from webservice

						// test stock=3
						$wsurl=$cfg_webservice["ficheiro_caminho_api"];
						$wsurl=str_replace('{referencia}', urlencode($prod_Referencia), $wsurl);
						$wsurl=str_replace('{qtd}', '1', $wsurl);
						echo '<br>'.$wsurl;
						$response=get_data($wsurl, $cfg_webservice["ficheiro_caminho_api_login"], $cfg_webservice["ficheiro_caminho_api_pass"]);
						// var_dump($response);
						if($response->SeuPreco->PrecoPP=='' && $response->Message=='')
						{
							echo '<br>NO RESPONSE FROM WS - non-existent Ref: '.$prod_Referencia;
							fwrite($fh, "NO RESPONSE FROM WS - non-existent Ref: ".$prod_Referencia."\n");

							$nao_existe++;

							// update flag to 2
							$sql='UPDATE LOW_PRIORITY products SET flag_cpc_ws=2, tstamp_cpc_ws=UNIX_TIMESTAMP() WHERE ID='.$produto_id;
							$res = Runquery($db_link, $DB_name, $sql);

						}
						elseif($response->Message=="Excedeu número de pedidos autorizados na ultima hora!")
						{
							fwrite($fh, "-- Excedeu número de pedidos autorizados na ultima hora!\n");
							fclose($fh);
							die('limite ws!');
						}
						else
						{
							$stockA=$response->Stock;
							$preco=$response->SeuPreco->PrecoPP;
							$taxas1=$response->Taxas->EcoValor;
							$taxas2=$response->Taxas->EcoReee;
							$taxas3=$response->Taxas->DireitosAutor;

							$prod_preco=$preco+$taxas1+$taxas2+$taxas3;
							$prod_preco=($prod_preco*1.23);

							if($stockA=='0') $prod_stock='0';
							elseif($stockA=='1') $prod_stock='3';
							elseif($stockA=='2') $prod_stock='1';
							elseif($stockA=='3') $prod_stock='1';
							elseif($stockA=='4') $prod_stock='3';
							elseif($stockA=='5') $prod_stock='1';

							echo '<br>*** stockA='.$stockA.' -- store:'.$prod_stock;
							echo '<br>*** ('.$preco.'+'.$taxas1.'+'.$taxas2.'+'.$taxas3.') prod_preco='.$prod_preco;
							// RoturaDeStock						0
							// EmStock								1
							// DisponivelBrevemente					2
							// Disponivel4DiasUteisAposEncomenda	3
							// ParcialmenteDisponivel				4
							// ConsultarCPCDI						5

							// VERIFICAÇÃO
							$tipo_pvp='0';
							$sql='SELECT obj_id,obj_Supplier_ID FROM products_objs WHERE obj_type_id="'.$produto_id.'" AND obj_val2="'.$tipo_pvp.'"';
							$rs->db_open($db_link, $DB_name, $sql);
							if(!$rs->EOF())
							{
								$obj_id=$rs->db_data['obj_id'];
								$obj_Supplier_ID=$rs->db_data['obj_Supplier_ID'];

								$sql='SELECT ID,pvp,Stock FROM products_objs_history_day WHERE ID_ORG="'.$obj_id.'" AND Supplier_ID="'.$Supplier_ID.'" ORDER BY ID DESC';
								$rs2->db_open($db_link, $DB_name, $sql);
								if(!$rs2->EOF())
								{
									$old_pvp=$rs2->db_data['pvp'];
									$old_stock=$rs2->db_data['Stock'];
									echo '<br>[DB - pvp='.$old_pvp.' - stock='.$old_stock.']';
								}
							}
							$rs->db_close();
							$rs2->db_close();

							if(($old_pvp-$prod_preco)>($old_pvp*0.6))
							{
								echo '<br>ALERTA: ALTERACAO MAIOR QUE 60%!!!';
								fwrite($fh, "ALERTA: ALTERACAO MAIOR QUE 60%!!! \n");

								$alertas++;

								// update flag to 3
								$sql='UPDATE LOW_PRIORITY products SET flag_cpc_ws=3, tstamp_cpc_ws=UNIX_TIMESTAMP() WHERE ID='.$produto_id;
								$res = Runquery($db_link, $DB_name, $sql);
							}
							else
							{

								// update_products_precos(ID_PROD,PVP,0,STOCK,REF,EAN,0,0,5,0,'23',0);

								echo "<br>[new: update_products_precos(".$produto_id.",".$prod_preco.",0,".$prod_stock.",".$prod_Referencia.",".$prod_Ref_Ean.",0,0,".$Supplier_ID.",0,'23')";

								update_products_precos($produto_id,$prod_preco,0,$prod_stock,$prod_Referencia,$prod_Ref_Ean,0,0,$Supplier_ID,0,'23');

								$sql='UPDATE LOW_PRIORITY products SET flag_cpc_ws=1, tstamp_cpc_ws=UNIX_TIMESTAMP() WHERE ID='.$produto_id;
								$res = Runquery($db_link, $DB_name, $sql);

							}

						}

					}
					else
					{
						$a_sync++;
						echo '<br>sync=1 '.$prod_Referencia;
						fwrite($fh, "sync=1 ".$prod_Referencia."\n");
						// update flag to 2
						$sql='UPDATE LOW_PRIORITY products SET flag_cpc_ws=4, tstamp_cpc_ws=UNIX_TIMESTAMP() WHERE ID='.$produto_id;
						$res = Runquery($db_link, $DB_name, $sql);
					}

					// test stock=1
					// $wsurl=$cfg_webservice["ficheiro_caminho_api"];
					// $wsurl=str_replace('{referencia}', $prod_Referencia, $wsurl);
					// $wsurl=str_replace('{qtd}', '5', $wsurl);
					// echo '<br>'.$wsurl.'<br>';
					// $response=get_data($wsurl, $cfg_webservice["ficheiro_caminho_api_login"], $cfg_webservice["ficheiro_caminho_api_pass"]);
					// // var_dump($response);
					// $stockB=$response->Stock;
					// echo '<br>+++ stockB='.$stockB;


					// EmStock
					// RoturaDeStock

					// die();


					// ***********************************************


					if($log) echo ($cont+$cont2).': Update Produto: '.$prod_Referencia.'<br>';
					$cont++;
				}
				else
				{
					echo "+";

				}

				if($produto_id > 0 && $sub_fam_id > 0 && $prod_Marca_id > 0 && $cria_filtros==1)
				{
					// products_objs_filtros_save($ID);
					//gera_produtos($produto_id, $sub_fam_id, $prod_Marca_id, 1);
				}
				//die("...");
				$linha++;
			}


			// SÓ NO FIM GERAMOS OS FILTROS PARA MARCAS / FAMILIAS
			//gera_produtos_filtros('pt');

			$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);




			// AGORA QUE JÁ IMPORTOU TUDO VAMOS REMOVER OS FICHEIROS DE CACHE QUE NÃO SE ENCONTRAM ATIVOS
			$total_p = 0; $del = 0;

			echo '<hr>';
			// echo '<br>Total Prods: ' . $total_p.'<br />';
			echo 'Processou: ' . $cont.'<br />';
			echo 'Nao existentes: ' . $nao_existe.'<br />';
			echo 'Sync: ' . $a_sync.'<br />';
			echo 'ALERTA: ALTERACAO MAIOR QUE 60%: ' . $alertas.'<br />';

			fwrite($fh, 'Processou: ' . $cont." \n");
			fwrite($fh, 'Nao existentes: ' . $nao_existe."\n");
			fwrite($fh, 'Sync: ' . $a_sync."\n");
			fwrite($fh, 'ALERTA: ALTERACAO MAIOR QUE 60%: ' . $alertas." \n");
			// fclose($fh);


			// echo 'Removeu: ' . $del.'<br />';
			// echo 'Fez '.$cont2.' INSERTS e '.$cont.' UPDATES Total de '.($cont+$cont2)."<br>\n";




	 	}
	 	else echo "ERRO nao conseguio ler produtos";
// 	}
// }


fclose($fh);

function get_data($url_remaining, $uid, $pwd){

// $baseUrl = 'https://api.cpcdi.pt/';
// $uid = '5335';
// $pwd = 'MHR2015API';
$ch = curl_init();
$url = $baseUrl . $url_remaining;
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_USERPWD, "$uid:$pwd");
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Accept: application/json',
    'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.99 Safari/537.36',
    'Connection: keep-alive',
    'Host: api.cpcdi.pt',
));
$json = curl_exec($ch);
curl_close ($ch);

return json_decode($json);
}

/*
function filtros_produtos_atualiza_imp($cat_id=0, $value_id=0, $prod_id=0, $fams, $prod_Marca_id=0, $filtros_rel2='', $fam_ordem1, $fam_ordem2, $v_fam_ordem1, $v_fam_ordem2, $f_ativo)
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
			if($and)$and.=' OR ';
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

	if($res && $fams[0] > 0 && ($fam_ordem1 > 0 || $v_fam_ordem1 > 0)) filtros_menus($cat_id, $value_id, $fams[0], $fam_ordem1, $v_fam_ordem1, $f_ativo);
	if($res && $fams[1] > 0 && ($fam_ordem2 > 0 || $v_fam_ordem2 > 0)) filtros_menus($cat_id, $value_id, $fams[1], $fam_ordem2, $v_fam_ordem2, $f_ativo);
	if($res && $fams[2] > 0 && ($fam_ordem2 > 0 || $v_fam_ordem2 > 0)) filtros_menus($cat_id, $value_id, $fams[2], $fam_ordem2, $v_fam_ordem2, $f_ativo);
	if($res && $fams[3] > 0 && ($fam_ordem2 > 0 || $v_fam_ordem2 > 0)) filtros_menus($cat_id, $value_id, $fams[3], $fam_ordem2, $v_fam_ordem2, $f_ativo);
}

function filtros_menus($cat_id, $value_id, $fam_id, $fam_ordem, $v_fam_ordem, $f_ativo)
{
	global $DB_host, $DB_user, $DB_pass, $DB_name;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs=new DataObj;

	$sql = 'SELECT menu_fam_id FROM menus_filtros WHERE menu_fam_id="'.$fam_id.'" AND menu_cat_id="'.$cat_id.'" AND menu_valor_id=0';
	$rs->db_open($db_link, $DB_name, $sql);
	if(!$rs->EOF())
	{
		$sql ='UPDATE menus_filtros SET menu_data="'.date('Y-m-d H:i:s').'", ID_Integracao="'.$cat_id.'", menu_flag=1, menu_ordem="'.$fam_ordem.'", menu_cat_nome="'.buscacategoriafiltros($cat_id).'", menu_visivel="1" WHERE menu_fam_id="'.$fam_id.'" AND menu_cat_id="'.$cat_id.'" AND menu_valor_id=0';
		$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
	}
	else
	{
		$sql ='INSERT INTO menus_filtros(ID_Integracao, menu_fam_id, menu_ordem, menu_cat_id, menu_cat_nome, menu_visivel, menu_flag, menu_data) VALUES("'.$cat_id.'", "'.$fam_id.'", "'.$fam_ordem.'", "'.$cat_id.'", "'.buscacategoriafiltros($cat_id).'", "'.$f_ativo.'", 1, "'.date('Y-m-d H:i:s').'")';
		$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
	}
	$rs->db_close();


	$sql = 'SELECT menu_fam_id FROM menus_filtros WHERE menu_fam_id="'.$fam_id.'" AND menu_cat_id="'.$cat_id.'" AND menu_valor_id="'.$value_id.'"';
	$rs->db_open($db_link, $DB_name, $sql);
	if(!$rs->EOF())
	{
		$sql ='UPDATE menus_filtros SET menu_data="'.date('Y-m-d H:i:s').'", ID_Integracao="'.$value_id.'", menu_flag=1, menu_ordem="'.$v_fam_ordem.'", menu_cat_nome="'.buscacategoriafiltros($cat_id).'", menu_valor_nome="'.buscavalorfiltros($value_id).'", menu_visivel="'.$f_ativo.'" WHERE  menu_fam_id="'.$fam_id.'" AND menu_cat_id="'.$cat_id.'" AND menu_valor_id="'.$value_id.'"';
		$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
	}
	else
	{
		$sql ='INSERT INTO menus_filtros(ID_Integracao, menu_fam_id, menu_ordem, menu_cat_id, menu_cat_nome, menu_valor_id, menu_valor_nome, menu_visivel, menu_flag,menu_data) VALUES("'.$value_id.'", "'.$fam_id.'", "'.$v_fam_ordem.'", "'.$cat_id.'", "'.buscacategoriafiltros($cat_id).'", "'.$value_id.'", "'.buscavalorfiltros($value_id).'", "'.$f_ativo.'", 1, "'.date('Y-m-d H:i:s').'")';
		$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
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
	}
	for($a=(count($nivel)-1); $a>=0; $a--)
	{
		$nivel2[]=$nivel[$a];
	}
	return $nivel2;
}
function getBrandsimg($prod_Marca_id=0, $prod_Marca_id_macor=0)
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
			$res = Runquery($db_link, $DB_name, $sql);
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
*/
/**
 * Converte o PDF num ficheiro mais pequeno
 * @param  string $input caminho completo do ficheiro a ser convertido
 */
function convertePDF($input){
	/***** COVERTE O PDF NUM MAIS PEQUENO *****/
	$output = $input.'.temp';
	exec('gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/screen -dNOPAUSE -dQUIET -dBATCH -sOutputFile='.$output.' '.$input, $temp);
	unlink($input);
	rename($output, $input);
}

/**
 * Converts a PDF to a Jpeg
 * @param  string $input  path of the file to be converted
 * @param  string $output converted file path
 * @return bool           if it was converted with success;
 */
function createJPGfromPDF($input, $output){
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
	$db_link=\db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$objv_id=$_POST['c_obj_id'];
	$obj_iva=$_POST['c_obj_iva'];
	$obj_preco=$_POST['c_obj_preco'];
	$obj_stock=$_POST['c_obj_stock'];
	$obj_promo=$_POST['c_obj_promo'];
	$obj_combo=$_POST['c_obj_combo'];
	$obj_ref=$_POST['c_obj_ref'];
	$rs=new DataObj; $rs2=new DataObj;
	$sql="UPDATE products_objs_combo SET obj_val=0 WHERE obj_type='products'AND obj_type_id='".$ID_PROD."'";

	$res=Runquery($db_link, $DB_name, $sql);
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

			$res=Runquery($db_link, $DB_name, $sql);
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
			$res=Runquery($db_link, $DB_name, $sql);
			$obj_id=db_get_last_insert_id($db_link);
		}
		$rs->db_close();
	}
		$sql="
			DELETE FROM
				products_objs_combo
			WHERE
				obj_type='products'
				AND obj_type_id='".$ID_PROD."'
				AND obj_val=0
			";
			$res=Runquery($db_link, $DB_name, $sql);
}

	// FICHEIRO DE LOG
	$log_activation=1;
	if($log_activation==1) // if logs are active
	{
		if(copy($myFile, str_replace('importar/', 'importado/', str_replace('ws_cpcdi_', 'ws_cpcdi_ok_',$myFile))))
		{
			unlink($myFile);
		}
	}
	else
	{
		unlink($myFile);
	}
?>