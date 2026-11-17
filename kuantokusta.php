<?php
	$_GET ='';
	$_POST ='';
	$_REQUEST ='';

	unset($_GET);
	unset($_POST);
	unset($_REQUEST);

	header('Content-Type: text/html; charset=utf-8');
	$path1=dirname( __FILE__ ).'/conf/';
	$path2=dirname( __FILE__ ).'/core/';

	include $path1.'conf.php';

	require_once $path2.DB_ENGINE;
	require_once $path2.'settings_modulos.php';
	require_once $path2.'sessions_engine.php';
	require_once $path2.'global.php';
	require_once $path2.'admin_engine.php';

	// MODULOS de LOJA
	require_once $path2.'garantia.php';
	require_once $path2.'servicos_extra.php';
	require_once $path2.'portes.php';
	require_once $path2.'portes_extra.php';

	/*require_once $path2.'loja.php';*/
	require_once $path2.'media.php';
	require_once $path2.'seo.php';
	require_once $path2.'linguas.php';
	require_once $path2.'gd.php';
	require_once $path2.'produtos.php';
	require_once $path2.'promocoes.php';
	require_once $path2.'filtros.php';
	require_once $path2.'servicos_extra.php';

	$site_lang='pt';

	global $site_shell_url;

	if($GLOBALS['exec_available']==1)
	{
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
		exec('rm -f '.$site_shell_url.'files/cache/*.*');
		exec('rm -f '.$site_shell_url.'files/filtros/*1*.*');
		exec('rm -f '.$site_shell_url.'files/filtros/*2*.*');
		exec('rm -f '.$site_shell_url.'files/filtros/*3*.*');
		exec('rm -f '.$site_shell_url.'files/filtros/*4*.*');
		exec('rm -f '.$site_shell_url.'files/filtros/*5*.*');
		exec('rm -f '.$site_shell_url.'files/filtros/*6*.*');
		exec('rm -f '.$site_shell_url.'files/filtros/*7*.*');
		exec('rm -f '.$site_shell_url.'files/filtros/*8*.*');
		exec('rm -f '.$site_shell_url.'files/filtros/*9*.*');
		exec('rm -f '.$site_shell_url.'files/filtros/*0*.*');
		exec('rm -f '.$site_shell_url.'files/filtros/*.*');
		exec('rm -f '.$site_shell_url.'files/tmp/*.*');
	}
	else
	{
		remove_files($site_shell_url.'files/cache/products');
		remove_files($site_shell_url.'files/cache');
		remove_files($site_shell_url.'files/filtros');
		remove_files($site_shell_url.'files/tmp');
	}

	function remove_files($path)
	{
		if ($handle = opendir($path))
	    {
	        while (false !== ($file = readdir($handle)))
	        {
	            if( is_file($file) )
	            {
	                unlink($file);
	            }
	        }
	        closedir($handle);
	    }
	}


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
$prods_tz = 'FAMILIA;MARCA;REFERENCIA;EAN;NOME PRODUTO;PRECO;LINK PRODUTO;FOTOGRAFIA PRODUTO;DISPONIBILIDADE;PORTES\n';


$myFiletmp=$site_shell_url."files/kk_tmp".$dt.".txt";
if(file_exists($myFile)) unlink($myFile);
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
		$prod_url = $site_http_url.seo('pages.php?page_id='.$GLOBALS['products_page_id'], $prod_id, 2);


		if($t_stock >= 2) $disponib = 'Disponibilidade Total';
		elseif($t_stock == 0) $disponib = 'Disponivel por Encomenda';
		else $disponib = 'Disponivel Brevemente';
		if($t_stock>1 && $prod_carrinho>=1 && $EAN != '')
		{
			$v_portes = 0;
			$res = '';
			//Tenho de adicionar uma flag, so deve entrar na funcao seguinte se tiver produtos com preços em filtros
			//$res = \filtros_ficha_produto_combinacoes_kk($prod_id,$fam_id);
			if($res)
			{
				for($a=0; $a<count($res); $a++)
				{
					$code = $res[$a]['cat_id'].'-'.$res[$a]['valor_id'];
					$preco_novo = 0;
					$sql2 = 'SELECT
								fp.obj_id, fp.obj_pvp, fp.obj_combo
							FROM
								products_objs_combo fp
							WHERE
								fp.obj_type_id="'.$prod_id.'"
								AND fp.obj_pvp > 0
								AND fp.obj_ref <> ""
								AND fp.obj_combo2 LIKE "%;'.$code.';%"
							';
					$rec2->db_open($hConn, $DB_name, $sql2);
					if(!$rec2->EOF()) $preco_novo = $rec2->db_data['obj_pvp'];
					$rec2->db_close();

					$v_portes = get_multi_portes_prod($prod_id, ($preco_novo > 0 ? $preco_novo : $preco));

					if($v_portes['valor'])$portes=$v_portes['valor'];
					else $portes=getPortesArtigo(2,165,$prod_id);

					//CUSTOS DE LOGISTICA POR ARTIGO E QUANTIDADE
					$total_custos_logisticos=0;

					$custos_logisticos=portes_extra_prod($prods['prod_id'],$prods['prod_precos']['PVP']);
					for($a=0; $a<count($custos_logisticos); $a++)
					{
						$total_custos_logisticos+=($custos_logisticos[$a]['valor'] );
					}
					//Agora vamos verificar se tem portes extra de custos de logistica
					if($total_custos_logisticos>0)$portes=$total_custos_logisticos;
					if($preco >= 400 && ($total_custos_logisticos<=0)) $portes=0;

					$prods_tz = $familia.';'.$marcax.';'.$ref.';'.$EAN.';'.$prod_nome.' '.$res[$a]['cat_nome'].' '.$res[$a]['valor_nome'].';'.($preco_novo > 0 ? $preco_novo : $preco).';'.$prod_url.'?sel='.urlencode($res[$a]['cat_nome'].': '.$res[$a]['valor_nome']).';'.$prod_foto.';'.$disponib.';'.$portes."\n";
					fwrite($fh,$prods_tz);

				}
			}
			else
			{
				$v_portes = \get_multi_portes_prod($prod_id, $preco);

				if($v_portes['valor'])$portes=$v_portes['valor'];
				else $portes=getPortesArtigo(2,165,$prod_id);

				$total_custos_logisticos=0;

				//CUSTOS DE LOGISTICA POR ARTIGO E QUANTIDADE
				$custos_logisticos=portes_extra_prod($prods['prod_id'],$prods['prod_precos']['PVP']);
				for($a=0; $a<count($custos_logisticos); $a++)
				{
					$total_custos_logisticos+=($custos_logisticos[$a]['valor'] );
				}
				//Agora vamos verificar se tem portes extra de custos de logistica
				if($total_custos_logisticos>0)$portes=$total_custos_logisticos;
				if($preco >= 400 && ($total_custos_logisticos<=0)) $portes=0;

				$prods_tz = $familia.';'.$marcax.';'.$ref.';'.$EAN.';'.$prod_nome.' '.$res[$a]['cat_nome'].' '.$res[$a]['valor_nome'].';'.$preco.';'.$prod_url.';'.$prod_foto.';'.$disponib.';'.$portes."\n";
				fwrite($fh,$prods_tz);
			}
		}
	}
	$rec->db_next();
}
$rec->db_close();

fclose($fh);

if($myFiletmp)
{
	$dt=date('Y-m-d');
	$dt_ontem=date('Y-m-d', strtotime($dt . "+1 days"));
	$myFile=$site_shell_url."files/kk".$dt.".txt";
	if(file_exists($myFile)) unlink($myFile);

	copy($myFiletmp,$myFile);

	/*$myFilegz=$site_shell_url."files/kuantokusta.tar.gz";
	if(file_exists($myFilegz)) unlink($file_exists);*/

	$myFile_ontem=$site_shell_url."files/kk".$dt_ontem.".txt";
	/*$fh=fopen($myFile,'w') or die("can't open file");
	$stringData=$prods_tz;
	fwrite($fh, $stringData);
	fclose($fh);*/
	if(file_exists($myFile)) exec('cd '.$site_shell_url.'files/; tar -cf kuantokusta.tar.gz kk'.$dt.'.txt');
	if(file_exists($myFile_ontem)) unlink($myFile_ontem);
}
echo "FIM";





function getPortesArtigo($metodo_expedicao,$pais,$prod_id)
{
	$peso_total=0;
	$portes=0;

	$info=\get_produtc_info($prod_id,1);
	$peso_total=floatval($info['prod_peso']);
	$enc_total=$info['prod_precos']['PVP'];

	$linhas_peso=getLinhasPortes($metodo_expedicao,$pais);
	$tam=count($linhas_peso);
	for ($i=0; $i < $tam; $i++) {
		$pesomin=$linhas_peso[$i]['PesoMin'];
		$pesomax=$linhas_peso[$i]['PesoMax'];
		$preco=$linhas_peso[$i]['Preco'];
		if($peso_total >= $pesomin && $peso_total <= $pesomax) {
			$portes=$preco;
			break;
		} else {
			if($i == ($tam - 1)) {
				//É a ultima linha e não esta entre os valores
				$portes=$preco;
			}
		}
	}
	// SE FOR LISBOA E A ENCOMENDA FOR SUPERIOE A 200
	// echo $pais.'==432 && '.$enc_total.' >= 200';
	// if($pais==432 && $enc_total > 200) $portes=0;
	return $portes;
}
function getLinhasPortes($metodo_expedicao,$pais)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$iva_default;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$rs2=new DataObj;
	$result=array();
	$pos=0;
	$sql="SELECT zone_type FROM country_zones WHERE zone_id='{$pais}'";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$zone_type=$rs->db_data['zone_type'];
		$sql='SELECT * FROM portes WHERE ID_MetodoExpedicao='.$metodo_expedicao.' AND zone_type='.$zone_type.' ORDER BY PesoMin ASC,PesoMax ASC';
		$db_res=$rs2->db_open($db_link,$DB_name,$sql);
		while (!$rs2->EOF())
		{
			$result[$pos]['PesoMin']=$rs2->db_data['PesoMin'];
			$result[$pos]['PesoMax']=$rs2->db_data['PesoMax'];
			$usar_iva=$rs2->db_data['usar_iva'];
			if($usar_iva == 1 && $iva_default > 0)
			{
				$iva=1 + (floatval($iva_default) / 100);
				$preco=$rs2->db_data['Preco'] * $iva;
			}
			else
			{
				$preco=$rs2->db_data['Preco'];
			}
			$result[$pos]['Preco']=$preco;
			$pos++;
			$rs2->db_next();
		}
		$rs2->db_close();
		$rs->db_next();
	}
	$rs->db_close();

	return $result;
}
?>