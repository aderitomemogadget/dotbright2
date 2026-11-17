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


	echo "*";
	$obj = getImportadorConfiguracoes_2($Supplier_ID);
	if(!$obj->EOF())
	{
		$Margem = $obj->db_data['Margem'];
	}
	$obj->db_close();

	//****************************************************

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
	$importar=1;


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

$filename=$path2 . 'files/sync_stock_'.date('Ymd').'_'.$id_revendedor.'.txt';
$filename_del=$path2 . 'files/sync_'.date('Ymd').'_'.$id_revendedor.'.txt';
$filename_importado=$path2 . 'files/importado/sync_'.date('Ymd').'_'.$id_revendedor.'.txt';


$remote_url = "http://www.macorlux.pt/webservice_freeplace_stock.php?small=1&user=memogadget&pass=domesticos&id_revendedor=$id_revendedor";


// SE AINDA NÃO EXISTIR, FAZ DOWNLOAD DO FICHEIRO DA MACORLUX
echo "FASE 1<br>\n";
echo "1.2 A Fazer download do json\n";
if($json=file_get_contents($remote_url))
{
	if(substr_count($json, '99999999999999999999'))
	{
		fwrite($fh, "1.3 O ficheiro esta completo, vamos coloca-lo na pasta\n");
		echo "1.3 O ficheiro esta completo, vamos coloca-lo na pasta<br>\n";
		file_put_contents($filename, str_replace('99999999999999999999', '',$json));
	}
	else
	{
		echo $remote_url."\n";
		var_dump($json);
		die('ERRO 2, não foi possivel obter o ficheiro!');
	}
}
else
{
	echo $remote_url."\n";
	die('ERRO, não foi possivel obter o ficheiro!');
	exit;
}
//****************************************************
fwrite($fh, "Fase 2, comecar analise...\n");
//****************************************************

$json = str_replace('99999999999999999999', '',$json);
$json = json_decode($json, true);
$cont = 0; $cont2 = 0;
$rs = new DataObj;
$rs2 = new DataObj;
$rs3 = new DataObj;
global $DB_host,$DB_user,$DB_pass,$DB_name;
$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

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

$email_txt = '';
global $iva_default;
$filtros_rel2 = '';
$xz = 0;
$small=1;
$linha = 1;


//********************************************************************************************************
//**************** PARA CADA ARTIGO
//********************************************************************************************************
while (@ob_end_flush());
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
			foreach($produtos as $prod)
			{
				ob_flush();
				flush();

				$ll = intval(($linha * 100 ) / $t);
				if($ll != $ll2)
				{
					$ll2 = $ll;
				}

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
			 		$prod_Stock=				$prod['30'];
			 		$prod_Data=					$prod['13'];
			 		$prod_Preco=				$prod['16'];
			 		$prod_Preco_Promo=			$prod['17'];
			 		$prod_Preco_Original=		$prod['18'];
			 		$prod_QtdMinEnc=			$prod['21'];
			 	}
				else
				{
					$prod_ID=					$prod['ID'];
			 		$prod_Referencia=			trim($prod['Referencia']);
			 		$prod_Stock=				$prod['Stock_Novo'];
			 		$prod_Data=					$prod['Data'];
			 		$prod_Preco=				$prod['Preco'];
			 		$prod_Preco_Promo=			$prod['Preco_Promo'];
			 		$prod_Preco_Original=		$prod['Preco_Original2'];
			 		$prod_QtdMinEnc=			$prod['QtdMinEnc'];
			 	}

			 	if($importar_pvp_usado == 1)
			 	{
			 		$prod_Preco =  $prod_Preco_Original;
			 		if($tirar_iva==1)$prod_Preco=$prod_Preco/1.23;
			 	}
			 	if($prod_Stock==0)$prod_Stock=1;//se vier 0 da macorlux quer dizer que eles ainda vendem o artigo

				echo ' | '.$t;
				$t--;

				if($Margem != '')
				{
					$prod_Preco2=$prod_Preco;
					$prod_Preco = $prod_Preco * (1+($Margem / 100));
					echo "Faltam $t artigos para terminar - REF: $prod_Referencia - EAN: $Ref_Ean - PC: $prod_Preco2, Aplica margem e novo preço: $prod_Preco \n";
				}
				else echo "Faltam $t artigos para terminar - REF: $prod_Referencia - EAN: $Ref_Ean - PC: $prod_Preco \n";
				//****************************************************

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

						echo "<br>prod_Referencia = $prod_Referencia";
						echo "Ref_Ean=$Ref_Ean ";

						$ID_ORG = $prod_dados[$produto_id][0];//Id da familia onde esta o artigo
						$preco_revenoff_prod = $prod_dados[$produto_id][1];
						$sync = $prod_dados[$produto_id][3];
						
						$fam_id = $fams_dados[$prod_Familia['ID']];
						$sub_fam_id = $fams_dados[$prod_SubFamilia['ID']];

						if($importar_precos==1)
						{							
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
							$promo = 0;
						}
						// SE VIA BACKOFFICE TIRARMOS A SINCRONISAÇÃO ESTA FLAG FICA IGUAL A 1
						$udtime =  date("Y-m-d H:i:s");

						if($sync == 0)
						{
							$sql = 'UPDATE products SET Preco1="'.$prod_Preco.'",MargemCli="'.aplicarMargem_ret($prod_Preco).'",Preco2="'.ValorPagarMargens($prod_Preco).'",Data_ultimo_desconto="'.$Data_ultimo_desconto.'",Desconto_percentagem="'.$Desconto_percentagem.'",udtime="'.$udtime.'" WHERE ID="'.$produto_id.'"';
							$res = Runquery($db_link,$DB_name,$sql);							
							update_products_precos($produto_id,$val_p,$prod_QtdMinEnc,$prod_Stock,$prod_Referencia, 'EAN', 0, ($prod_Stock !='' ? 1 : 0),$Supplier_ID,0,$iva_default,$prod_Preco);
							unlink($site_shell_url.'files/cache/products/p'.$produto_id.'_pt.bin');//Remove a cache do artigo
						}
						$cont++;
					}					
				}
				$linha++;
			}

			$total_p = 0; $del = 0;

			echo '<br>Total Prods: ' . $total_p.'<br />';
			echo 'Fez '.$cont." UPDATES<br>\n";

			if(@copy($filename,$filename_importado)) @unlink($filename);
	 	}
	 	else echo "ERRO nao conseguio ler produtos";
	}
}

fclose($fh);

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