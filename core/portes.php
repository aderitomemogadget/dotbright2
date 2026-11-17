<?php
if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) exit();
function get_product_families_entrega($id_pai)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$m_http_url_images,$site_http_url,$tema,$site_lang;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new Dataobj;
	$rs2=new Dataobj;
    $sql='
			SELECT
				pf.ID, pf.ID_ORG,
				pfl.Cabecalho,
				portes_especiais_tipo_instalacao,
				portes_especiais_sob_orcamento,
				portes_especiais_valor_pagar,
				portes_especiais_texto,
				portes_especiais_ativo
			FROM
				products_families pf
				INNER JOIN products_families_lang pfl ON pfl.ID_PAI = pf.ID
			WHERE
				pf.ID='.$id_pai;
    $rs->db_open($db_link,$DB_name,$sql);
    if(!$rs->EOF())
	{
		if(($rs->db_data['portes_especiais_valor_pagar'] > 0 || $rs->db_data['portes_especiais_sob_orcamento'] == 1) && $rs->db_data['portes_especiais_ativo'] == 1) $margem = array('id' => $rs->db_data['ID'].'_1', 'titulo' => $rs->db_data['Cabecalho'], 'promo_ativo' => $rs->db_data['portes_especiais_sob_orcamento'], 'valor' => $rs->db_data['portes_especiais_valor_pagar'], 'info' => $rs->db_data['portes_especiais_texto']);
		else $margem = get_product_families_entrega($rs->db_data['ID_ORG']);
    }
    $rs->db_close();
	return $margem;
}
function get_product_families_porte_especial($id_pai)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$m_http_url_images,$site_http_url,$tema,$site_lang;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new Dataobj;
    $sql='
			SELECT
				pf.ID, pf.ID_ORG,
				pfl.Cabecalho,
				portes_especiais_tipo_instalacao,
				portes_especiais_sob_orcamento,
				portes_especiais_valor_pagar,
				portes_especiais_texto,
				portes_especiais_ativo
			FROM
				products_families pf
				INNER JOIN products_families_lang pfl ON pfl.ID_PAI = pf.ID
			WHERE
				pf.ID='.$id_pai;
    $rs->db_open($db_link,$DB_name,$sql);
    if(!$rs->EOF())
	{
		if(($rs->db_data['portes_especiais_valor_pagar'] > 0 || $rs->db_data['portes_especiais_sob_orcamento'] == 1) && $rs->db_data['portes_especiais_ativo'] == 1) $margem = array('id' => $rs->db_data['ID'].'_1', 'titulo' => $rs->db_data['Cabecalho'], 'promo_ativo' => $rs->db_data['portes_especiais_sob_orcamento'], 'valor' => $rs->db_data['portes_especiais_valor_pagar'], 'info' => $rs->db_data['portes_especiais_texto']);
		else $margem = get_product_families_entrega($rs->db_data['ID_ORG']);
    }
    $rs->db_close();
	return $margem;
}

function BuscarArvoreFams_exp($id_org)
{
	$hConn = db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$obj = new DataObj;

	$arvore2 = array();
	$pai = $id_org;
	while($pai > 0)
	{
		$id_org = $pai;
		$sql = "SELECT ID, ID_ORG FROM products_families WHERE ID=$pai";
		$obj->db_open($hConn,$GLOBALS['DB_name'],$sql);
		$pai = $obj->db_data["ID_ORG"];
		$arvore2[] = $obj->db_data["ID"];
	}
	$output = array();
	$t = (count($arvore2)-1);
	for($a=0; $a<count($arvore2); $a++)
	{
		$output[$t] = $arvore2[$a];
		$t--;
	}
	ksort($output);
	$obj->db_close();


	return $output;
}
function verifica_expe($prod_id, $expedicao_id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang,$site_http_url;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$exp_id = array();
	$obj=new Dataobj;	$obj2=new Dataobj;	$rs2=new Dataobj;	 $fam_id1=0;
	$cat = 0;
	$sql='select pfo.ID_ORG from products p inner join products_families_obj pfo on p.ID=pfo.ID_PROD where p.ID='.$prod_id;
	$rs2->db_open($db_link,$GLOBALS['DB_name'],$sql);
	if(!$rs2->EOF()) $cat = $rs2->db_data['ID_ORG'];
	$rs2->db_close();

	// echo $cat.'<br>';
	$fams = BuscarArvoreFams_exp($cat);

	$fams2 = implode(',', $fams);

	if($fams2 != '')
	{
		$sql = 'SELECT expedicao_id FROM metodos_expedicao_portes WHERE fam_id IN('.$fams2.') AND expedicao_id = '.$expedicao_id.' GROUP BY expedicao_id';
		$rs2->db_open($db_link,$GLOBALS['DB_name'],$sql);
		if(!$rs2->EOF())
		{
			while(!$rs2->EOF())
			{
				$exp_id[] = $rs2->db_data['expedicao_id'];
				$rs2->db_next();
			}
		}
		$rs2->db_close();		
	}


	return $exp_id;
}
function re_atualiza_portes($prod_id,$portes_id,$m_pais)
{
	if(!$prod_id)return;

	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang,$site_http_url;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$obj=new Dataobj;
	$obj2=new Dataobj;
	$rs2=new Dataobj;
	//Será que escolheu a transportadora especial
	$cat = 0;
	$sql = 'select pfo.ID_ORG from products p inner join products_families_obj pfo on p.ID=pfo.ID_PROD where p.ID='.$prod_id;
	$rs2->db_open($db_link,$GLOBALS['DB_name'],$sql);
	if(!$rs2->EOF()) $cat = $rs2->db_data['ID_ORG'];
	$rs2->db_close();

	//PAra ja apenas existe na megadigital:
	/*$info = get_product_families_entrega($cat);
	if(isset($info['titulo']))
	{
		$id = $info['id'];
		if($id.'_1' == $portes_id)return $portes_id;
	}*/
	//******************** outros portes:
	$rs = getAllMetodosExpedicao2('','',$m_pais);
	if (!$rs->EOF())
	{
		$i=0;
		while (!$rs->EOF())
		{
			$ID = $rs->db_data['ID'];
			$expedicoes = verifica_expe($prod_id,$ID);
			if(in_array($ID, $expedicoes))
			{
				if($i==0)$default=$ID.'_0';
				if($ID.'_0' == $portes_id)return $portes_id;//EXISTE ESTE METODO PARA ESTE PAIS e para este produto
				$i++;
			}
			$rs->db_next();
		}
		$rs->db_close();
	}

	//**** afinal tem multiportes por artigo
	//multi_portes_prod($prod_id=0, $prod_valor=0)
	return $portes_id;
}

//Esta função permite obter apenas a garantia da sub-familia

/*
function get_multi_portes_prod_new($prod_id=0,$prod_valor=0,$fam_id=0)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;

	if($prod_id > 0 )
	{
		$sql='SELECT ID_ORG FROM products_families_obj WHERE ID_PROD = '.$prod_id;
		$rs->db_open($db_link, $DB_name, $sql);
		if(!$rs->EOF())
		{
			$ID_ORG=$rs->db_data['ID_ORG'];
			$rs->db_close();
			return get_multi_portes_prod_new(0,$prod_valor,$ID_ORG);
		}
	}
	else
	{
		$sql = 'SELECT familia_id, id, promo_ativo, valor, titulo, info, bloco_ext, valor_promo FROM multi_portes eg
				WHERE ini <= '.$prod_valor.' AND fim > '.$prod_valor.' AND familia_id LIKE "%;'.$fam_id.';%" AND visivel=1 ORDER BY valor=0, valor asc';
		$rs->db_open($db_link, $DB_name, $sql);
		if(!$rs->EOF()) {return $rs;}
		else
		{
			$rs->db_close();
			$sql = 'SELECT ID_ORG FROM products_families WHERE ID = '.$fam_id;
			$rs->db_open($db_link, $DB_name, $sql);
			if(!$rs->EOF())
			{
				$ID_ORG=$rs->db_data['ID_ORG'];
				$rs->db_close();
				if($ID_ORG>0)return get_multi_portes_prod_new(0,$prod_valor,$ID_ORG);
			}
		}
	}
	return $rs;
}
function multi_portes_prod($prod_id=0, $prod_valor=0, $prod_on=0, $pais='')
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang,$site_http_url;

	if((isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['multi_portes_prod'] == 1))
	{
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$obj=new Dataobj;
		$obj2=new Dataobj;
		$fam_id1=0;
		$return=array();
		$prod_valor = number_format($prod_valor,2,'.','');
		$obj=get_multi_portes_prod_new($prod_id,$prod_valor,0);
		if(!$obj->EOF())
		{

			while(!$obj->EOF())
			{
				$id=$obj->db_data['id'];//este id tem de ser minusculo
				if($id>0)
				{
					$sql='SELECT fp.PROD_ID FROM multi_portes_products fp WHERE fp.promo_id='.$id.' AND fp.PROD_ID='.$prod_id;

					$obj2->db_open($db_link, $DB_name, $sql);
					if($obj2->EOF())
					{
						$titulo=$obj->db_data['titulo'];
						$promo_ativo=$obj->db_data['promo_ativo'];
						$valor =$obj->db_data['valor'];
						$valor_promo =$obj->db_data['valor_promo'];
						$info =stripslashes($obj->db_data['info']);
						$bloco_ext =$obj->db_data['bloco_ext'];
						if($bloco_ext!='' && stream_resolve_include_path(SITE_SHELL_URL.'files/portes/'.$bloco_ext)) $termos=SITE_HTTP_URL.'files/portes/'.$bloco_ext;
						else $termos=seo('pages.php?page_id=37');

						$return[]=array('titulo'=>$titulo,'id'=>$id,'promo_ativo'=>$promo_ativo,'valor'=>($promo_ativo==1 ? $valor_promo : $valor),'valor_antigo'=>$valor,'info'=>$info,'termos'=>$termos);
					}
					$obj2->db_close();
				}
				$obj->db_next();
			}
		}
		$obj->db_close();

		return $return;
	}else return false;
}

function get_multi_portes_prod($prod_id=0, $prod_valor=0)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang,$site_http_url;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new Dataobj;
	$obj2=new Dataobj;
	$fam_id1=0;
	$return=array();
	$prod_valor = number_format($prod_valor,2,'.','');
	$obj=get_multi_portes_prod_new($prod_id,$prod_valor,0);
	if(!$obj->EOF())
	{
		while(!$obj->EOF())
		{
			$id=$obj->db_data['id'];
			if($id > 0)
			{
				$sql='SELECT fp.PROD_ID FROM multi_portes_products fp WHERE fp.promo_id='.$id.'AND fp.PROD_ID='.$prod_id.'';
				$obj2->db_open($db_link, $DB_name, $sql);
				if($obj2->EOF())
				{
					$titulo=$obj->db_data['titulo'];
					$promo_ativo=$obj->db_data['promo_ativo'];
					$valor =$obj->db_data['valor'];
					$valor_promo =$obj->db_data['valor_promo'];
					$info =stripslashes($obj->db_data['info']);
					$bloco_ext =$obj->db_data['bloco_ext'];
					if($bloco_ext!='' && stream_resolve_include_path(SITE_SHELL_URL.'files/portes/'.$bloco_ext)) $termos=SITE_HTTP_URL.'files/portes/'.$bloco_ext;
					else $termos=seo('pages.php?page_id=37');
					$return[]=array('titulo' => $titulo, 'id' => $id, 'promo_ativo' => $promo_ativo, 'valor' => ($promo_ativo==1 ? $valor_promo : $valor), 'valor_antigo' => $valor, 'info' => $info, 'termos' => $termos);
				}
				$obj2->db_close();
			}
			$obj->db_next();
		}
	}
	$obj->db_close();
	if(count($return) > 0)return $return[0];
	else return;
}

function multi_portes_prod_primeiro($prod_id=0, $prod_valor=0)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang,$site_http_url;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new Dataobj;
	$obj2=new Dataobj;
	$fam_id1=0;
	$return=array();
	$prod_valor = number_format($prod_valor,2,'.','');
	$obj=get_multi_portes_prod_new($prod_id,$prod_valor,0);
	if(!$obj->EOF())
	{
		$id=$obj->db_data['id'];
		$sql='SELECT fp.PROD_ID FROM multi_portes_products fp WHERE fp.promo_id='.$id.'AND fp.PROD_ID='.$prod_id;
		$obj2->db_open($db_link, $DB_name, $sql);
		if($obj2->EOF())
		{
			$titulo=$obj->db_data['titulo'];
			$promo_ativo=$obj->db_data['promo_ativo'];
			$valor =$obj->db_data['valor'];
			$valor_promo =$obj->db_data['valor_promo'];
			$info =stripslashes($obj->db_data['info']);
			$return[]=array('titulo' => $titulo, 'id' => $id, 'promo_ativo' => $promo_ativo, 'valor' => ($promo_ativo==1 ? $valor_promo : $valor), 'valor_antigo' => $valor, 'info' => $info);
		}
		$obj2->db_close();
	}
	$obj->db_close();
	return $return;
}
*/



function multi_portes_prod($prod_id=0,$prod_valor=0,$prod_peso=0)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang,$site_http_url;
	$db_link=\db_opendb_conection($DB_host, $DB_user, $DB_pass);

	$rs = new Dataobj;
	$rs2 = new Dataobj;
	$obj = new Dataobj;
	$obj2 = new Dataobj;

	$ID_ORG = 0;
	$return = array();


	$sql="SELECT ID_ORG FROM products_families_obj WHERE ID_PROD='".$prod_id."'";
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$ID_ORG=$rs->db_data['ID_ORG'];
	}
	$rs->db_close();

	if($ID_ORG>0)
	{
		$fam_id = explode(",",get_fam_recurs($ID_ORG));
	}

	if(count($fam_id)>0 && $prod_valor>0)
	{
		$sql = '
				SELECT
					familia_id, id, promo_ativo, valor,portes_pesos,portes_pesos_formula, titulo, info, bloco_ext
				FROM
					multi_portes eg
				WHERE
					(ini <= '.$prod_valor.' AND fim > '.$prod_valor.')
					AND visivel=1
					AND (';
					for($a=0; $a<count($fam_id); $a++)
					{
						if($a > 0) $sql .=	' OR familia_id LIKE "%;'.$fam_id[$a].';%"';
						else $sql .= ' familia_id  LIKE "%;'.$fam_id[$a].';%"';
					}
					$sql .=	')';

		$sql .=	'ORDER BY
					valor=0, valor asc
				';


		/*$sql = 'SELECT
					familia_id, id, promo_ativo, valor,portes_pesos,portes_pesos_formula, titulo, info, bloco_ext
				FROM
					multi_portes eg
				WHERE
					(ini <= '.$prod_valor.' AND fim > '.$prod_valor.') AND visivel=1 AND familia_id IN ('.$fam_ids.') ORDER BY valor=0,valor asc';


*/
		$obj->db_open($db_link, $DB_name, $sql);
		if(!$obj->EOF())
		{

			while(!$obj->EOF())
			{
				$id = $obj->db_data['id'];

				$sql = '
					SELECT
						fp.PROD_ID
					FROM
						multi_portes_products fp
					WHERE
						fp.promo_id = '.$id.'
						AND fp.PROD_ID = '.$prod_id.'
					';
				// echo $sql.'<br>';
				$obj2->db_open($db_link, $DB_name, $sql);
				if($obj2->EOF())
				{
					$titulo = $obj->db_data['titulo'];
					$promo_ativo = $obj->db_data['promo_ativo'];
					$valor  = $obj->db_data['valor'];
					$valor_promo  = $obj->db_data['valor_promo'];
					$portes_pesos = $obj->db_data['portes_pesos'];
					$portes_pesos_formula = $obj->db_data['portes_pesos_formula'];

					if($portes_pesos == 1)
					{
						//Em vez do valor habitual vamos obter o valor dos portes pelo peso
						if(is_string($portes_pesos_formula) && trim($portes_pesos_formula)!=='')
						{
							$formula_value=get_product_margin_formula_value(str_replace('Peso','PVP',$portes_pesos_formula),$prod_peso);
							if($formula_value!==null) $valor=$formula_value;
						}
					}

					$info  = stripslashes($obj->db_data['info']);
					$bloco_ext  = $obj->db_data['bloco_ext'];
					if($bloco_ext!='' && stream_resolve_include_path(SITE_SHELL_URL.'files/portes/'.$bloco_ext)) $termos = SITE_HTTP_URL.'files/portes/'.$bloco_ext;
					else $termos = \seo('pages.php?page_id=37');

					$return[] = array('titulo' => $titulo, 'id' => $id, 'promo_ativo' => $promo_ativo, 'valor' => ($promo_ativo==1 ? $valor_promo : $valor), 'valor_antigo' => $valor, 'info' => $info, 'termos' => $termos);
				}
				$obj2->db_close();

				$obj->db_next();
			}
		}
		$obj->db_close();
	}

	return $return;
}

function get_fam_recurs($ID=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=\db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new Dataobj;

	$fam_id = $ID;

	$sql="SELECT ID_ORG FROM products_families WHERE ID='".$ID."' AND ID_ORG > 0";
	$rs->db_open($db_link,$DB_name,$sql);
	while(!$rs->EOF())
	{
		$fam_id .= ','.get_fam_recurs($rs->db_data['ID_ORG']);
		$rs->db_next();
	}
	$rs->db_close();
	return $fam_id;
}
function get_multi_portes_prod($prod_id=0,$prod_valor=0,$prod_peso=0)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang,$site_http_url;
	$db_link=\db_opendb_conection($DB_host, $DB_user, $DB_pass);

	$rs = new Dataobj;
	$rs2 = new Dataobj;
	$obj = new Dataobj;
	$obj2 = new Dataobj;

	$ID_ORG = 0;
	$return = array();


	$sql="SELECT ID_ORG FROM products_families_obj WHERE ID_PROD='".$prod_id."'";
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$ID_ORG=$rs->db_data['ID_ORG'];
	}
	$rs->db_close();

	if($ID_ORG>0)
	{
		$fam_id = explode(",",get_fam_recurs($ID_ORG));
	}

	if(count($fam_id)>0)
	{
		$sql = 'SELECT
					familia_id, id, promo_ativo, valor,portes_pesos,portes_pesos_formula, titulo, info, bloco_ext
				FROM
					multi_portes eg
				WHERE
					(ini <= '.$prod_valor.' AND fim > '.$prod_valor.')
					AND visivel=1
					AND (';
					for($a=0; $a<count($fam_id); $a++)
					{
						if($a > 0) $sql .=	' OR familia_id LIKE "%;'.$fam_id[$a].';%"';
						else $sql .= ' familia_id  LIKE "%;'.$fam_id[$a].';%"';
					}
				$sql .=	') ORDER BY valor=0,valor asc';


		/*$sql = 'SELECT
					familia_id, id, promo_ativo, valor,portes_pesos,portes_pesos_formula, titulo, info, bloco_ext
				FROM
					multi_portes eg
				WHERE
					(ini <= '.$prod_valor.' AND fim > '.$prod_valor.') AND visivel=1 AND familia_id IN ('.$fam_ids.') ORDER BY valor=0,valor asc';*/




				// echo $sql;
		$obj->db_open($db_link, $DB_name, $sql);
		if(!$obj->EOF())
		{

			while(!$obj->EOF())
			{
				$id = $obj->db_data['id'];

				$sql = '
					SELECT
						fp.PROD_ID
					FROM
						multi_portes_products fp
					WHERE
						fp.promo_id = '.$id.'
						AND fp.PROD_ID = '.$prod_id.'
					';
				// echo $sql.'<br>';
				$obj2->db_open($db_link, $DB_name, $sql);
				if($obj2->EOF())
				{
					$titulo = $obj->db_data['titulo'];
					$promo_ativo = $obj->db_data['promo_ativo'];
					$valor  = $obj->db_data['valor'];
					$valor_promo  = $obj->db_data['valor_promo'];
					$info  = stripslashes($obj->db_data['info']);
					$bloco_ext  = $obj->db_data['bloco_ext'];
					$portes_pesos = $obj->db_data['portes_pesos'];
					$portes_pesos_formula = $obj->db_data['portes_pesos_formula'];

					if($portes_pesos == 1)
					{
						//Em vez do valor habitual vamos obter o valor dos portes pelo peso
						if(is_string($portes_pesos_formula) && trim($portes_pesos_formula)!=='')
						{
							$formula_value=get_product_margin_formula_value(str_replace('Peso','PVP',$portes_pesos_formula),$prod_peso);
							if($formula_value!==null) $valor=$formula_value;
						}
					}

					if($bloco_ext!='' && stream_resolve_include_path(SITE_SHELL_URL.'files/portes/'.$bloco_ext)) $termos = SITE_HTTP_URL.'files/portes/'.$bloco_ext;
					else $termos = \seo('pages.php?page_id=37');

					$return[] = array('titulo' => $titulo, 'id' => $id, 'promo_ativo' => $promo_ativo, 'valor' => ($promo_ativo==1 ? $valor_promo : $valor), 'valor_antigo' => $valor, 'info' => $info, 'termos' => $termos);
				}
				$obj2->db_close();

				$obj->db_next();
			}
		}
		$obj->db_close();
	}
	if(count($return) > 0)	return $return[0];
	else return;
}

function multi_portes_prod_primeiro($prod_id=0, $prod_valor=0)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang,$site_http_url;
	$db_link=\db_opendb_conection($DB_host, $DB_user, $DB_pass);

	$obj = new Dataobj;	$obj2 = new Dataobj;	 $fam_id1 = 0;

	$return = array();

	$sql = '
			SELECT
				fp.fam_id1, fp.fam_id2, fp.fam_id3, fp.fam_id4
			FROM
				filters_products fp

			WHERE
				fp.prod_id = '.$prod_id.'
			';
	$obj->db_open($db_link, $DB_name, $sql);
	if(!$obj->EOF())
	{
		if($obj->db_data['fam_id1'] > 0) $fam_id[] = $obj->db_data['fam_id1'];
		if($obj->db_data['fam_id2'] > 0) $fam_id[] = $obj->db_data['fam_id2'];
		if($obj->db_data['fam_id3'] > 0) $fam_id[] = $obj->db_data['fam_id3'];
		if($obj->db_data['fam_id4'] > 0) $fam_id[] = $obj->db_data['fam_id4'];
	}
	$obj->db_close();
	if(count($fam_id) > 0)
	{

		$info = get_produtc_info($prod_id);
		if ($opcoes_id > 0) $info['prod_precos'] = get_produtc_price_combo($prod_id, $opcoes_id);

		$sql = '
				SELECT
					familia_id, id, promo_ativo, valor, titulo, info
				FROM
					multi_portes eg
				WHERE
					(ini <= '.$prod_valor.' AND fim > '.$prod_valor.')
					AND visivel=1
					AND (';
					for($a=0; $a<count($fam_id); $a++)
					{
						if($a > 0) $sql .=	' OR familia_id LIKE "%;'.$fam_id[$a].';%"';
						else $sql .= ' familia_id  LIKE "%;'.$fam_id[$a].';%"';
					}
					$sql .=	')';

		$sql .=	'ORDER BY
					valor ASC
				LIMIT 1;
				';
				// echo $sql;
		$obj->db_open($db_link, $DB_name, $sql);
		if(!$obj->EOF())
		{

			$id = $obj->db_data['id'];

			$sql = '
				SELECT
					fp.PROD_ID
				FROM
					multi_portes_products fp
				WHERE
					fp.promo_id = '.$id.'
					AND fp.PROD_ID = '.$prod_id.'
				';
			// echo $sql.'<br>';
			$obj2->db_open($db_link, $DB_name, $sql);
			if($obj2->EOF())
			{
				$titulo = $obj->db_data['titulo'];
				$promo_ativo = $obj->db_data['promo_ativo'];
				$valor  = $obj->db_data['valor'];
				$valor_promo  = $obj->db_data['valor_promo'];
				$info  = stripslashes($obj->db_data['info']);

				$return[] = array('titulo' => $titulo, 'id' => $id, 'promo_ativo' => $promo_ativo, 'valor' => ($promo_ativo==1 ? $valor_promo : $valor), 'valor_antigo' => $valor, 'info' => $info);
			}
			$obj2->db_close();


		}
		$obj->db_close();
	}

	return $return;
}
