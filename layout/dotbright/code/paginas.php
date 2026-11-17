<?php
if (isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__) exit();

function getPaginaById($page_id)
{
	$db_link 	= db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
	$rs 		= new DataObj;
	$params 	= array(
		':Lang' => $GLOBALS['site_lang'],
		':ID' => $page_id
	);
	$sql = 'SELECT p.*,pl.Cabecalho,pl.Texto2,pl.Lang
			FROM pages p
				INNER JOIN pages_lang pl ON pl.ID_PAI=p.ID
			WHERE pl.Lang=:Lang and p.ID=:ID';
	$rs->bind_params($params);
	$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);

	return $rs;
}
function drawBreadcrumbsPages($page_id)
{
	$html = '';
	$str = '';
	$rs = getPaginaById($page_id);
	if(!$rs->EOF())
	{
		$page_id_parent = $rs->db_data['ID_ORG'];
		$Cabecalho 		= stripslashes($rs->db_data['Cabecalho']);
		$defaulthome 	= $rs->db_data['DefaultHome'];
		$p_seo 			= seo('pages.php?page_id='.$page_id);

		if(!$defaulthome)
		{
			$html .= '<div class="fw-pages-breadcrumbs">';
			$html .= '<div class="container">';


			$str = '';
			if($page_id_parent)
			{
				$str = drawBreadcrumbsPagesRecursive($page_id_parent);
			}

			$str .= '<span class="separator">&nbsp;&raquo;&nbsp;</span>'.$Cabecalho.'';



			$html .= '<a href="'.$GLOBALS['site_http_url'].'homepage" title="'.getLinguas('key_inicio',$GLOBALS['site_lang']).'">'.getLinguas('key_inicio',$GLOBALS['site_lang']).'</a>';
			$html .= $str;


			$html .= '</div>';
			$html .= '</div>';
		}
	}
	$rs->db_close();

	return $html;
}
function drawBreadcrumbsPagesRecursive($id_pai)
{
	$str = '';
	$rs = getPaginaById($id_pai);
	if (!$rs->EOF()) {
		$page_idx = $rs->db_data['ID'];
		$page_id_parent = $rs->db_data['ID_ORG'];
		$nome_temp=$rs->db_data['Cabecalho'];
		$Visible=$rs->db_data['Visible'];

		$p_seo=seo('pages.php?page_id='.$page_idx);
		if ($page_id_parent==0)
		{
			if($Visible == 1) $str='&nbsp;&gt;&nbsp;<a href="'.$p_seo.'" title="'.$nome_temp.'">'.$nome_temp.'</a>';
			//else $str='&nbsp;&gt;&nbsp;<span>'.$nome_temp.'</span>';
		}
		else
		{
			if($Visible == 1) $str='&nbsp;&gt;&nbsp;<a href="'.$p_seo.'" title="'.$nome_temp.'">'.$nome_temp.'</a>';
			//else $str='&nbsp;&gt;&nbsp;<span>'.$nome_temp.'</span>';
			$str=drawBreadcrumbsPagesRecursive($page_id_parent).$str;
		}
	}
	$rs->db_close();
	return $str;
}
function geraPaginaRecursiva($id_org)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
    $db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new Dataobj;
	$pai = $id_org;

	$arvore[] = $id_org;
	while($pai > 0)
	{
		$id_org = $pai;

		$sql = 'SELECT ID_ORG FROM pages WHERE ID='.$pai.' AND Visible=1';
		$rs->db_open($db_link,$DB_name,$sql);
		$arvore[] = $pai = $rs->db_data["ID_ORG"];
		$rs->db_close();
	}
	return array_values(array_filter(array_reverse($arvore)));
}
function geraPaginaRecursiva_c($arr=array())
{
	global $DB_host, $DB_user, $DB_pass, $DB_name;
    $db_link=\db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new Dataobj;

	for($a=0; $a<count($arr); $a++)
	{
		$sql = 'SELECT Cabecalho FROM pages p INNER JOIN pages_lang pl ON pl.ID_PAI = p.ID WHERE pl.ID_PAI='.$arr[$a].' AND pl.Lang="'.$GLOBALS['site_lang'].'"  AND p.Visible=1';
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$arvore['titulo'][] = $rs->db_data["Cabecalho"];
			$arvore['id'][] = $arr[$a];
		}
		$rs->db_close();
	}

	return $arvore;
}
/*function FAM_TXT($active = 0)
{
	if(isset($_GET['prod_id']) && $_GET['prod_id'] > 0)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs = new Dataobj;
		echo $sql='select pfo.ID_ORG, (pl.Cabecalho) from products p inner join products_lang pl on p.ID=pl.ID_PAI inner join products_families_obj pfo on p.ID=pfo.ID_PROD where pl.Lang="'.$GLOBALS['site_lang'].'" and p.ID='.intval($_GET['prod_id']);
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			echo $cat = $rs->db_data['ID_ORG'];
			$nome_prod = htmlentities_memo(stripslashes(upper_str($rs->db_data['Cabecalho'])));
		}
		$rs->db_close();

		$p_seo = seo('pages.php?page_id='.$GLOBALS['products_page_id'], intval($_GET['prod_id']), 2);
	}
}*/
function BREADCRUMBS($active = 0, $fam_id=0)
{
	$titulo = ''; $s = '';
	$pagina = isset($_GET['page_id']) ? 1 : 0;
	$catalogo = isset($_GET['cat']) ? 1 : 0;
	$catalogo_m = isset($_GET['c']) ? 1 : 0;
	$marca = (isset($_GET['s_marca']) && $_GET['s_marca'] > 0) ? 1 : 0;
	$produto = isset($_GET['prod_id']) ? 1 : 0;
	$pesquisa = isset($_GET['pesquisa']) ? 1 : 0;
	$tipo_catalogo = isset($GLOBALS['tipo_catalogo']) ? 1 : 0;


	if(($catalogo != 1 && $produto != 1 && $pesquisa != 1 && $tipo_catalogo  != 1) || $active == 1)
	{
		$sdt='<script type="application/ld+json">{';
		$sdt.='"@context": "http://schema.org",';
		$sdt.='"@type": "BreadcrumbList",';
		$sdt.='"itemListElement": [';
		$sdt_n=1;
		$sdt .= '{"@type": "ListItem","position": '.$sdt_n.',"item": {"@id": "'.$GLOBALS['site_http_url'].'","name":"Home"}},';$sdt_n++;




		if($fam_id > 0)$s = '<div class="breadcrumb clearfix">';
		else $s = '<div class="breadcrumb clearfix">';
		$s .= '<a class="home" href="'.$GLOBALS['site_http_url'].'" title=""><i class="fa fa-home"></i></a> <span class="navigation-pipe"><i class="fa fa-angle-right" aria-hidden="true"></i></span>';

		if($produto == 1)
		{

			if(isset($fam_id))
			{
				$p_way = geraCatalogoRecursivo($fam_id);
				$p_way_t = geraCatalogoRecursivo_c($p_way);
				if(count($p_way_t) > 0)
				{
					$s2 = '';
					for($a=0; $a<count($p_way_t['titulo']); $a++)
					{
						// if($a == 0) $s2 .= $p_way_t['titulo'][$a].': ';
						// else $s2 .= $p_way_t['titulo'][$a];

						$p_seo = seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$p_way_t['id'][$a], $p_way_t['id'][$a],1);

						if($a==0) $s2 .= '<a href="'.$p_seo.'">'.$p_way_t['titulo'][$a].'</a>';
						else $s2 .= '<span class="navigation-pipe"><i class="fa fa-angle-right" aria-hidden="true"></i></span> <a href="'.$p_seo.'">'.$p_way_t['titulo'][$a].'</a>';

						/*if($a==0) $s2 .= '<a href="'.$p_seo.'" onclick="clean_filters();return true;">'.$p_way_t['titulo'][$a].'</a>';
						else $s2 .= '<span class="navigation-pipe"><i class="fa fa-angle-right" aria-hidden="true"></i></span> <a href="'.$p_seo.'" onclick="clean_filters();return true;">'.$p_way_t['titulo'][$a].'</a>';*/

						$sdt .= '{"@type": "ListItem","position": '.$sdt_n.',"item": {"@id": "'.$GLOBALS['site_http_url'].$p_seo.'","name":"'.$p_way_t['titulo'][$a].'"}},';$sdt_n++;
					}

					// $titulo = $p_way_t['titulo'][count($p_way_t['titulo'])-1];
					$titulo = $p_way_t['titulo'][0];


					if(isset($_GET['prod_id']) && $_GET['prod_id'] > 0)
					{
						global $DB_host,$DB_user,$DB_pass,$DB_name;
						$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
						$rs = new Dataobj;
						$sql='select pfo.ID_ORG, (pl.Cabecalho) from products p inner join products_lang pl on p.ID=pl.ID_PAI inner join products_families_obj pfo on p.ID=pfo.ID_PROD where pl.Lang="'.$GLOBALS['site_lang'].'" and p.ID='.intval($_GET['prod_id']);
						$rs->db_open($db_link,$DB_name,$sql);
						if(!$rs->EOF())
						{
							$cat=$rs->db_data['ID_ORG'];
							$nome_prod=htmlentities_memo(stripslashes(upper_str($rs->db_data['Cabecalho'])));
						}
						$rs->db_close();
					}

					$p_seo = seo('pages.php?page_id='.$GLOBALS['products_page_id'], intval($_GET['prod_id']), 2);

					$s .= ''.ltrim(rtrim($s2, ': '), ': '). '<span class="navigation-pipe"><i class="fa fa-angle-right" aria-hidden="true"></i></span><a href="'.$p_seo.'" class="breadfinal">'.$nome_prod.'</a>';
					//$s .= ''.ltrim(rtrim($s2, ': '), ': '). '<span class="navigation-pipe"><i class="fa fa-angle-right" aria-hidden="true"></i></span><a href="'.$p_seo.'" onclick="clean_filters();return true;" class="breadfinal">'.$nome_prod.'</a>';

					// $sdt .= '{"@type": "ListItem","position": '.$sdt_n.',"item": {"@id": "'.$GLOBALS['site_http_url'].$p_seo.'","name":"'.$nome_prod.'"}},';$sdt_n++;
				}
			}
		}
		elseif($marca == 1 && $catalogo_m == 1)
		{
			$p_way =  get_produtc_brand(intval($_GET['s_marca']));
			$p_way_f = geraCatalogoRecursivo(intval($_GET['c']));
			$p_way_t = geraCatalogoRecursivo_c($p_way_f);

			if(count($p_way_t) > 0)
			{
				$s2 = '';
				for($a=0; $a<count($p_way_t['titulo']); $a++)
				{
					$p_seo = seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$p_way_t['id'][$a], $p_way_t['id'][$a],1);

					if($s2 == '') $s2 .= '<a href="'.$p_seo.'" onclick="clean_filters();return true;">'.$p_way_t['titulo'][$a].'</a>';
					else $s2 .= '<span class="navigation-pipe"><i class="fa fa-angle-right" aria-hidden="true"></i></span> <a href="'.$p_seo.'" onclick="clean_filters();return true;">'.$p_way_t['titulo'][$a].'</a>';

					$sdt .= '{"@type": "ListItem","position": '.$sdt_n.',"item": {"@id": "'.$GLOBALS['site_http_url'].$p_seo.'","name":"'.$p_way_t['titulo'][$a].'"}},';$sdt_n++;

				}
				$titulo = $p_way_t['titulo'][0];

				$s .= '<span>'.ltrim(rtrim($s2, ': '), ': '). '</span>';
			}

			$p_seo = 'm-'.$p_way['marca_id'].'-'. str_replace(' ','',limpa_special($p_way['marca_nome'])).'-'.$GLOBALS['site_lang'];

			$sdt .= '{"@type": "ListItem","position": '.$sdt_n.',"item": {"@id": "'.$GLOBALS['site_http_url'].$p_seo.'","name":"'.$p_way['marca_nome'].'"}},';$sdt_n++;
			$s3 = '<span class="navigation-pipe"><i class="fa fa-angle-right" aria-hidden="true"></i></span> <a href="'.$p_seo.'" onclick="clean_filters();return true;">'.$p_way['marca_nome'].'</a>';
			$titulo = $p_way_t['titulo'][0];

			$s .= '<span>'.ltrim(rtrim($s3, ': '), ': '). '</span>';

		}
		elseif($catalogo == 1)
		{
			if(isset($_GET['cat']))
			{
				$p_way = geraCatalogoRecursivo($_GET['cat']);
				$p_way_t = geraCatalogoRecursivo_c($p_way);
				if(count($p_way_t) > 0)
				{
					$s2 = '';
					for($a=0; $a<count($p_way_t['titulo']); $a++)
					{
						if($GLOBALS['page_id']==148)$p_seo = seo('pages.php?page_id=148&cat='.$p_way_t['id'][$a], $p_way_t['id'][$a],1);
						else $p_seo = seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$p_way_t['id'][$a], $p_way_t['id'][$a],1);
						
						if($a==0)  $s2 .= '<a href="'.$p_seo.'">&nbsp;'.$p_way_t['titulo'][$a].'&nbsp;</a>';
						else $s2 .= '<span class="navigation-pipe"><i class="fa fa-angle-right" aria-hidden="true"></i></span> <a href="'.$p_seo.'">'.$p_way_t['titulo'][$a].'&nbsp;</a>';

						$sdt .= '{"@type": "ListItem","position": '.$sdt_n.',"item": {"@id": "'.$GLOBALS['site_http_url'].$p_seo.'","name":"'.$p_way_t['titulo'][$a].'"}},';$sdt_n++;
					}

					$titulo = $p_way_t['titulo'][0];

					$s .= ''.ltrim(rtrim($s2, ': '), ': '). '';


				}
			}
		}
		elseif($marca == 1)
		{
			if(isset($_GET['s_marca']))
			{
				$p_way =  get_produtc_brand(intval($_GET['s_marca']));

				$s2 = '';

				$p_seo = 'm-'.intval($_GET['s_marca']).'-'. str_replace(' ', '', limpa_special($p_way['marca_nome'])).'-'.$GLOBALS['site_lang'];

				$s2 .= '<a href="'.$p_seo.'" onclick="clean_filters();return true;">'.$p_way['marca_nome'].'</a>';

				$titulo = $p_way_t['titulo'][0];

				// $s .= '<span>'.ltrim(rtrim($s2, ': '), ': '). '</span>';
				$s .= ''.ltrim(rtrim($s2, ': '), ': '). '';

				$sdt .= '{"@type": "ListItem","position": '.$sdt_n.',"item": {"@id": "'.$GLOBALS['site_http_url'].$p_seo.'","name":"'.$p_way['marca_nome'].'"}},';$sdt_n++;
			}
		}
		else
		{
			if(isset($_GET['page_id']))
			{
				$p_way = geraPaginaRecursiva($_GET['page_id']);
				$p_way_t = geraPaginaRecursiva_c($p_way);
				if(count($p_way_t) > 0)
				{
					$s2 = '';
					for($a=0; $a<count($p_way_t['titulo']); $a++)
					{
						if($a == 0) $s2 .= '<a href="'.$p_seo.'" onclick="clean_filters();return true;">&nbsp;'.$p_way_t['titulo'][$a].'&nbsp;</a> ';
						else $s2 .= '<span class="navigation-pipe"><i class="fa fa-angle-right" aria-hidden="true"></i></span> <a href="'.$p_seo.'" onclick="clean_filters();return true;">'.$p_way_t['titulo'][$a].'&nbsp;</a>';

						$sdt .= '{"@type": "ListItem","position": '.$sdt_n.',"item": {"@id": "'.$GLOBALS['site_http_url'].seo('pages.php?page_id='.$p_way_t['id'][$a]).'","name":"'.ltrim(rtrim($p_way_t['titulo'][$a], ': '), ': ').'"}},';$sdt_n++;
					}

					// $titulo = $p_way_t['titulo'][count($p_way_t['titulo'])-1];
					if($p_way_t['titulo'][0]) $titulo = $p_way_t['titulo'][0];
					elseif($p_way_t['titulo'][1]) $titulo = $p_way_t['titulo'][1];

					// $s .= '<span>&nbsp;'.ltrim(rtrim($s2, ': '), ': '). '</span>';
					$s .= ''.ltrim(rtrim($s2, ': '), ': '). '';

					$sdt .= '{"@type": "ListItem","position": '.$sdt_n.',"item": {"@id": "'.$GLOBALS['site_http_url'].seo('pages.php?page_id='.$p_way_t['id'][count($p_way_t['id'])-1]).'","name":"'.ltrim(rtrim($s2, ': '), ': ').'"}},';$sdt_n++;
				}
			}
		}

		// $s .= '</nav>';
		// $s .= '<div class="c"></div>';
		if($pesquisa == 1) $s .= ':  <span>'.htmlentities($_GET['pesquisa']).'</span>';
		// $s .= '</div>';
		// $s .= '<div class="c"></div>';
		// if($catalogo!=1 && $produto != 1) $s .= '<div class="bt-line"></div>';

		$s .= '</div>';

		$sdt=substr($sdt, 0, -1);
		$sdt.=']}</script>';


		/*
		if((isset($_GET['cat']) && intval($_GET['cat']) > 0) || $pesquisa == 1)
		{
			$sdt .='<div class="filtros_bt_mobile"><div onclick="show_hide_filtros()" class="botao">FILTRAR POR<svg style="margin-left: 10px" width="1em" height="1em" viewBox="0 0 17 13" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.535 12.007H2.923v-1.334H.506V9.34h2.417V8.007h1.612v4zm12.086-1.334H6.146V9.34h10.475v1.333zm-4.029-2.666h-1.611V6.673H.506V5.34H10.98V4.015h1.611v3.992zm4.029-1.334h-2.417V5.34h2.417v1.333zM7.758 4.007H6.146V2.673H.506V1.34h5.64V.007h1.612v4zm8.863-1.334H9.369V1.34h7.252v1.333z" fill="currentColor"></path></svg></div></div>';
		}
		*/
	}
	return $s.$sdt;
} 
function filtros_mobile()
{
	$pesquisa = isset($_GET['pesquisa']) ? 1 : 0;	
	$sdt='';
	if((isset($_GET['cat']) && intval($_GET['cat']) > 0) || $pesquisa == 1)
	{
		$sdt ='<div class="filtros_bt_mobile"><div onclick="show_hide_filtros()" class="botao">FILTRAR POR<svg style="margin-left: 10px" width="1em" height="1em" viewBox="0 0 17 13" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.535 12.007H2.923v-1.334H.506V9.34h2.417V8.007h1.612v4zm12.086-1.334H6.146V9.34h10.475v1.333zm-4.029-2.666h-1.611V6.673H.506V5.34H10.98V4.015h1.611v3.992zm4.029-1.334h-2.417V5.34h2.417v1.333zM7.758 4.007H6.146V2.673H.506V1.34h5.64V.007h1.612v4zm8.863-1.334H9.369V1.34h7.252v1.333z" fill="currentColor"></path></svg></div></div>';
		$sdt .='
		<div class="catalogo_mobile_filtros">
			<div class="ordem" style="margin-top:5px;margin-right:15px">Ordenar por:<br>

						<select id="product_order" class="filter chzn-select2">';

							if(isset($_GET["pesquisa"]) && $_GET["pesquisa"]!='')$selected_order = '-1';
							else 
							{
								if($REPARACOES)$selected_order = '4';
								else $selected_order = '3';
							}


                            //$selected_order = '-1'; if(defined('ORDENAR_POR') && ORDENAR_POR !=''){$selected_order = ORDENAR_POR;}

                            $sdt .='<option value="-1" '.($selected_order == '-1' ? 'selected="selected"' : '').'>Relevancia</option>';
                            $sdt .='<option value="0" '.($selected_order == '0' ? 'selected="selected"' : '').'>A..Z</option>';
                            $sdt .='<option value="1" '.($selected_order == '1' ? 'selected="selected"' : '').'>Z..A</option>';
                            $sdt .='<option value="2" '.($selected_order == '2' ? 'selected="selected"' : '').'>'.getLinguas('txt_maisbarato').'</option>';
                            $sdt .='<option value="3" '.($selected_order == '3' ? 'selected="selected"' : '').'>'.getLinguas('txt_maiscaro').'</option>';
                            $sdt .='
                        </select>
			</div>
            <div class="c"></div>
        </div>
		<div class="catalogo_mobile_pesquisa">
            <div class="pesq_txt_padding"><input placeholder="pesquisar nesta familia" type="text" id="search_box2" name="pesquisa" class="pesq_input" value="" /></div>
			<div class="float-left"><div class="info-extra-paginacao"></div></div>
            <div class="float-right"><div class="paginacao"></div></div>
        </div>';
	}
	echo $sdt;
}
// FUNCOES PARA CATALOGO
function geraCatalogoRecursivo($id_org)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
    $db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new Dataobj;
	$pai = $id_org;

	$arvore[] = $id_org;
	while($pai > 0)
	{
		$id_org = $pai;

		$sql = 'SELECT ID_ORG FROM products_families WHERE ID='.$pai;
		$rs->db_open($db_link,$DB_name,$sql);
		$arvore[] = $pai = $rs->db_data["ID_ORG"];
		$rs->db_close();
	}
	return array_values(array_filter(array_reverse($arvore)));
}

function geraCatalogoRecursivo_c($arr=array())
{
	global $DB_host, $DB_user, $DB_pass, $DB_name;
    $db_link=\db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new Dataobj;

	for($a=0; $a<count($arr); $a++)
	{
		$sql = 'SELECT Cabecalho, Cabecalho2 FROM products_families p INNER JOIN products_families_lang pl ON pl.ID_PAI = p.ID WHERE pl.ID_PAI='.$arr[$a].' AND pl.Lang="'.$GLOBALS['site_lang'].'"  AND p.visivel=1';
		$rs->db_open($db_link,$DB_name,$sql);
		$arvore['titulo'][] = ($rs->db_data["Cabecalho2"] !='' ? $rs->db_data["Cabecalho2"] : $rs->db_data["Cabecalho"]);
		$arvore['id'][] = $arr[$a];
		$rs->db_close();
	}

	return $arvore;
}


