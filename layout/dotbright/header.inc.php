<?php

	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;
	$rs2=new DataObj;

	//atribui cliente logado aos produtos visualizados pela $_SESSION['CLIENTE_ID_TEMP']
	global $tem_produtos_visualizados, $headers_adicionais;
	if($tem_produtos_visualizados){if(cliente_logado())verificaProdutosVisualizados();}

	// VERIFICAR SE EXISTE SEO PARA ESTE CONTEUDO
	if(!isset($page_id)) // VAI CARREGAR A DEFAULT HOME
	{
		$rs->db_open($db_link,DB_NAME,'SELECT ID,Template,Tipo,Data_modify,sim,img_ext,img_ext2,PageEnter,DefaultHome2,bootstrap,bootstrap_p FROM pages WHERE DefaultHome=1');
		if(!$rs->EOF())
		{
			if(!defined('is_home')) define('is_home',1);
			$page_id=$rs->db_data['ID'];
			if($rs->db_data['img_ext2']) $imagem_fundo2='files/pages/'.$rs->db_data['ID'].'_2.'.$rs->db_data['img_ext2'];
		}
		else $rs->db_close();
	}
	// ********************************************************
	// SE FOR PAGINA
	// ********************************************************
	if($page_id)
	{
		global $page_name;
		if(!defined('is_home') || is_home != 1)$rs->db_open($db_link,DB_NAME,'SELECT ID,Template,Tipo,Data_modify,sim,img_ext,img_ext2,PageEnter,DefaultHome2,bootstrap,bootstrap_p FROM pages WHERE ID='.$page_id);
		if(!$rs->EOF())
		{
			$GLOBALS['template']=$rs->db_data['Template'];
			$page_tipo=$rs->db_data['Tipo'];
			$bootstrap=$rs->db_data['bootstrap'];
			$bootstrap_p=$rs->db_data['bootstrap_p'];
			$I1 = $rs->db_data['img_ext'];
			$GLOBALS['ocultar_titulo']=$rs->db_data['sim'];

			if($rs->db_data['DefaultHome2']==1 && !defined('is_home')) define('is_home',1);

			if($rs->db_data['img_ext2']) $imagem_fundo2='files/pages/'.$rs->db_data['ID'].'_2.'.$rs->db_data['img_ext2'];
			if($GLOBALS['template'] == -1) {if($GLOBALS['site_template']) $GLOBALS['template']=$GLOBALS['site_template'];}
			elseif($GLOBALS['template'] > 0) $_SESSION['ss_site_template']=$GLOBALS['template'];

			$PageEnter=$rs->db_data['PageEnter'];
			$DefaultHome=$rs->db_data['DefaultHome2'];
		    $tipo=$rs->db_data['Tipo'];
    		$data=$rs->db_data['Data_modify'];

			if($I1 != '')
			{
				$img_file1 = "files/pages/".$page_id."_1.".$I1;
				$img = array();
	            if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file1)){$img[] = $img_file1;}
	            if(!empty($img))
	            {
	                for ($a = 0; $a < count($img); $a++)
	                {
	                    $imgs .= '<img alt="'.$page_name.'" src="'.$img[$a].'?d='.dotbright_image_mtime($GLOBALS['site_shell_url'].$img_file1).'" />';
	                }
	            }
			}

			if($page_id == 1 || $DefaultHome == 1)
			{
				if(!defined('is_home')) define('is_home',1);
			}
		}
		$rs->db_close();

		$rs->db_open($db_link,DB_NAME,'SELECT SEO_title,SEO_description,Cabecalho,Cabecalho2,Texto,Texto2,Texto10,Texto11,Texto12,Texto13,Texto14,Texto15,Texto16,Texto17,Texto18,Texto19,Texto20,Lang FROM pages_lang WHERE ID_PAI='.$page_id.' AND Lang="'.$GLOBALS['site_lang'].'"');
		if(!$rs->EOF())
		{
			$SEO_title=\strip_html_tagsc(stripslashes($rs->db_data['SEO_title']));
			$SEO_description=\strip_html_tagsc(stripslashes($rs->db_data['SEO_description']));
			$page_name=stripslashes($rs->db_data['Cabecalho']);
			$sub_page_name=stripslashes($rs->db_data['Cabecalho2']);
			grava_visita($page_name);
			if(!$SEO_title) $SEO_title=\strip_html_tagsc(stripslashes($rs->db_data['Cabecalho']));
			if(!$SEO_description) $SEO_description=\strip_html_tagsc(stripslashes($rs->db_data['Texto2']));
			if(!$SEO_description) $SEO_description=\strip_html_tagsc(stripslashes($rs->db_data['Texto']));

			$Texto='';
			$Texto10='';
			$Texto11='';
			$Texto12='';
			$Texto13='';
			$Texto14='';
			$Texto15='';
			$Texto16='';
			$Texto17='';
			$Texto18='';
			$Texto19='';
			$Texto20='';

			if($rs->db_data['Texto']) $Texto  =stripslashes($rs->db_data['Texto']);
			if($rs->db_data['Texto10']) $Texto10=stripslashes($rs->db_data['Texto10']);
			if($rs->db_data['Texto11']) $Texto11=stripslashes($rs->db_data['Texto11']);
			if($rs->db_data['Texto12']) $Texto12=stripslashes($rs->db_data['Texto12']);
			if($rs->db_data['Texto13']) $Texto13=stripslashes($rs->db_data['Texto13']);
			if($rs->db_data['Texto14']) $Texto14=stripslashes($rs->db_data['Texto14']);
			if($rs->db_data['Texto15']) $Texto15=stripslashes($rs->db_data['Texto15']);
			if($rs->db_data['Texto16']) $Texto16=stripslashes($rs->db_data['Texto16']);
			if($rs->db_data['Texto17']) $Texto17=stripslashes($rs->db_data['Texto17']);
			if($rs->db_data['Texto18']) $Texto18=stripslashes($rs->db_data['Texto18']);
			if($rs->db_data['Texto19']) $Texto19=stripslashes($rs->db_data['Texto19']);
			if($rs->db_data['Texto20']) $Texto20=stripslashes($rs->db_data['Texto20']);
		}
		$rs->db_close();

		if(dotbright_image_resolve($GLOBALS['site_shell_url'].'files/pages/'.$page_id.'_seo_'.$GLOBALS['site_lang'].'.jpg'))$imgfb = 'files/pages/'.$page_id.'_seo_'.$GLOBALS['site_lang'].'.jpg?d='.uniqid('');

		$SEO_title.=' - '.$_SERVER['HTTP_HOST'];
	}
	// ********************************************************
	// SE FOR FAMILIA DE PRODUTO
	// ********************************************************
	if ($page_id == $GLOBALS['catalog_page_id'])
	{
		if (isset($_GET['cat']))
		{
			$SEMINOVOS=0;
			$REPARACOES=0;
			$array_f=encontraFamiliasRecursiva_x(intval($_GET['cat']));
			if(in_array(7,$array_f))$SEMINOVOS=1;
			if(in_array(85,$array_f))$REPARACOES=1;

			
			$fam_info=get_family_info(intval($_GET['cat']));
			$fam_parent=$fam_info['fam_parent'];
			$SEO_title=\strip_html_tagsc($fam_info['fam_nome']);
			$SEO_title=\strip_html_tagsc(str_replace('<br>',' ',$SEO_title));
			$SEO_title=\strip_html_tagsc(str_replace('<font class="menu_sub_text">',' ',$SEO_title));
			$SEO_title=\strip_html_tagsc(str_replace('</font>',' ',$SEO_title));

			while($fam_parent != 0)
			{
				$fam_info=get_family_info(intval($fam_parent));
				$fam_parent=$fam_info['fam_parent'];

				$SEO_title2=\strip_html_tagsc($fam_info['fam_nome']);
				$SEO_title2=\strip_html_tagsc(str_replace('<br>',' ',$SEO_title2));
				$SEO_title2=\strip_html_tagsc(str_replace('<font class="menu_sub_text">',' ',$SEO_title2));
				$SEO_title2=\strip_html_tagsc(str_replace('</font>',' ',$SEO_title2));

				$SEO_title=$SEO_title2.' - '.$SEO_title;
			}

			$SEO_title.=' - '.$_SERVER['HTTP_HOST'];
			$SEO_description=$SEO_title.' '.$SEO_DESCRIPTION;
			$page_name=$fam_info['fam_nome'];

			if(dotbright_image_resolve($GLOBALS['site_shell_url'].'files/products_families/'.$_GET['cat'].'_seo_'.$GLOBALS['site_lang'].'.jpg'))
			$imgfb = 'files/products_families/'.$_GET['cat'].'_seo_'.$GLOBALS['site_lang'].'.jpg?d='.uniqid('');
		}
	}
	// ********************************************************
	// SE FOR PRODUTO
	// ********************************************************
	if ($page_id == $GLOBALS['products_page_id'])
	{
		if(isset($_GET['ean']) && $_GET['ean'] != '')
		{
			$sql='SELECT products_objs.*,SUBSTRING_INDEX(SUBSTRING_INDEX(P.Ref, "_", 1), ",", -1) AS uniq_ref,Sync,Cabecalho,Variante,estado,bateria,Ref,u_catetiq,u_coretiq,u_imagetiq,P.ID_Marca,P.Ordem as Ordem2,FPO.ID_ORG as ID_Fam,obj_Supplier_ID,fornecedor_importacao
				FROM products_objs
				INNER JOIN products_families_obj FPO on FPO.ID_PROD=products_objs.obj_type_id
				INNER JOIN products_lang PL on PL.ID_PAI=products_objs.obj_type_id
				INNER JOIN products P on P.ID=products_objs.obj_type_id
				WHERE P.Ref like "%'.$_GET['ean'].'%" AND PL.Lang="pt" AND P.Aproved=1 AND products_objs.obj_val2=0 AND products_objs.obj_stock>0  AND (obj_val2=0 OR obj_val2 IS NULL) GROUP BY uniq_ref ORDER BY P.bateria DESC,products_objs.obj_pvp DESC';

			$rs->db_open($db_link,DB_NAME,$sql);
			if(!$rs->EOF())
			{
				$_GET['prod_id']=$rs->db_data['obj_type_id'];
			}
			$rs->db_close();
		}
		$prods=\get_produtc_info($_GET['prod_id']);
		if(!$prods)
		{
			header("HTTP/1.0 404 Not Found");
		}

		// LER DADOS DA FAMILIA
		$fam_info=get_family_info(intval($prods['fam_id']));
		$brand=\get_produtc_brand(intval($prods['prod_id_marca']));
		$SEO_title=\strip_html_tagsc($prods['prod_nome'].' - '.$brand['marca_nome'].' - '.$fam_info['fam_nome'].' - '.$_SERVER['HTTP_HOST']);
		$SEO_description=\strip_html_tagsc($prods['prod_nome'].','.$brand['marca_nome'].','.$fam_info['fam_nome'].','.$prods['prod_descricao2']);

		$page_name=$prods['prod_nome'];
		$imgfb=$prods['prd_imgm_fb'];
		$tem_video=0;
		if($prods['prod_video']) $tem_video=1;

		if(dotbright_image_resolve($GLOBALS['site_shell_url'].'files/products/'.$prods['prod_id'].'_seo_'.$GLOBALS['site_lang'].'.jpg'))$imgfb = 'files/products/'.$prods['prod_id'].'_seo_'.$GLOBALS['site_lang'].'.jpg?d='.uniqid('');
	}
	// ********************************************************
	// SE FOR IMOVEL
	// ********************************************************
	if(isset($_GET['imoveis_id']) && $_GET['imoveis_id']>0)
	{
		$imoveis_id=$_GET['imoveis_id'];
		$sql="SELECT imoveis.ID,imoveis.ID_ORG,imoveis.created_at,imoveis.img_ext,imoveis.img2_ext,imoveis.img3_ext, imoveis.localicacao, imoveis.tipologia,imoveis.squarem, imoveis.areaterreno,imoveis.condicao,imoveis.Preco_prec, imoveis_lang.Cabecalho,imoveis_lang.Texto1,imoveis_lang.Texto2,imoveis_lang.Texto3 FROM imoveis inner join imoveis_lang on imoveis_lang.ID_PAI=imoveis.ID WHERE Lang='$site_lang' AND Aproved=1 AND ID_ORG>0 and imoveis.ID=$imoveis_id";
		$rs->db_open($db_link,DB_NAME,$sql);
		if(!$rs->EOF())
		{
			$titulo=$rs->db_data['Cabecalho'];
			$texto1=str_replace('&trade;','<sup>TM</sup>',limpa_editor_lixo($rs->db_data['Texto1']));
			$sql="SELECT * FROM imoveis_images WHERE ID_ORG='$imoveis_id'";
			$rs2->db_open($db_link, $GLOBALS['DB_name'], $sql);
			if(!$rs2->EOF())
			{
				$img_ext = $rs2->db_data['img_ext'];
				$ordem = $rs2->db_data['Ordem'];
				if($img_ext && dotbright_image_resolve($site_shell_url.'files/imoveis/'.$imoveis_id.'_'.$ordem.'.'.$img_ext))
					$imgfb='files/imoveis/'.$imoveis_id.'_'.$ordem.'.'.$img_ext;
			}
			$rs2->db_close();

			$SEO_title=\strip_html_tagsc($titulo.' - '.$_SERVER['HTTP_HOST']);
			$SEO_description=\strip_html_tagsc($titulo.','.$texto1);

		}
		$rs->db_close();
	}
	// ********************************************************
	// SE FOR PAGINA DE MARCA
	// ********************************************************
	if (isset($_GET['s_marca']) && $_GET['s_marca'] > 0 && isset($_GET['c']) && $_GET['c'] > 0)
	{
		$fam_info=get_family_info(intval($_GET['c']));
		$brand=\get_produtc_brand(intval($_GET['s_marca']));
		$SEO_title=\strip_html_tagsc($fam_info['fam_nome'].' - '.$brand['marca_nome'].' - '.$_SERVER['HTTP_HOST']);
		$SEO_description=\strip_html_tagsc($fam_info['fam_nome'].','.$brand['marca_nome']);
		$page_name=$brand['marca_nome'];
	}
	elseif(isset($_GET['s_marca']) && $_GET['s_marca'] > 0)
	{
		$brand=\get_produtc_brand($_GET['s_marca']);
		$SEO_title=\strip_html_tagsc($brand['marca_nome'].' - '.$_SERVER['HTTP_HOST']);
		$SEO_description=\strip_html_tagsc($brand['marca_nome']);
		$page_name=$brand['marca_nome'];
	}
	// ********************************************************
	// SE FOR PESQUISA
	// ********************************************************
	if (isset($_GET['search']) && $_GET['search'] != '')$_GET['pesquisa']=$_GET['search'];
	if (substr_count($_SERVER['SCRIPT_FILENAME'],'find.php') && isset($_GET['pesquisa']) && $_GET['pesquisa'] != '')
	{
		$SEO_title='Pesquisa por: '.\strip_html_tagsc($_GET['pesquisa'].' - '.$_SERVER['HTTP_HOST']);
		$SEO_description='Pesquisa por: '.\strip_html_tagsc($_GET['pesquisa']);
		$page_name='Pesquisa';
	}
	// ********************************************************
	// Se for novidades ou promoções:
	// ********************************************************
	if (isset($_GET['tipo_catalogo']))
	{
		if ($_GET['tipo_catalogo'] == 'novidade')
		{
			$SEO_title='Novidades'.' - '.$_SERVER['HTTP_HOST'];
			$SEO_description='Novidades';
			$page_name='Novidades';
		}
		if ($_GET['tipo_catalogo'] == 'promocao')
		{
			$SEO_title='Promoções'.' - '.$_SERVER['HTTP_HOST'];
			$SEO_description='Promoções';
			$page_name='Promoções';
		}
	}
	if(!$SEO_description)$SEO_description=$SEO_DESCRIPTION;
	$SEO_description=substr($SEO_description,0,156);

	// ********************************************************
	// GERAR FICHEIROS DE JSON
	// ********************************************************
	// SE FOR A HOME PAGE
	// ********************************************************

	if(defined('is_home') && is_home==1 && $GLOBALS['tem_loja'])
	{
		//****************************** DESTAQUES *******************//
		$f_json_destaque=gera_json_destaques();
		//****************************** PROMOCOES *******************//
	//	$f_json_promocao=gera_json_home_promocoes();
		//****************************** MAIS VENDIDOS *******************//
	//	$f_json_maisvendido=gera_json_maisvendidos();
		//****************************** NOVIDADES *******************//
	//	$f_json_novidades=gera_json_home_novidades();
		//****************************** RANDOM *******************//
	//	$f_json_random=gera_json_home_random();
	}
	elseif(isset($_GET['prod_id']) && $_GET['prod_id'] > 0)
	{
		$prod_id = intval($_GET['prod_id']);
		//****************************** NOVIDADES *******************//
		$f_json=gera_json_relacionados($prod_id);
		echo $gera_cache=\gera_cache('files/filtros/json_rel_'.intval($_GET['prod_id']).'_'.$GLOBALS['site_lang'].'.js','',$f_json);		
	}
	elseif((isset($_GET['cat']) && $_GET['cat'] > 0) || (isset($_GET['page_id']) && $_GET['page_id'] > 0 && $_GET['page_id'] != $GLOBALS['pesquisa_page_id']))
	{
		//Caso estejamos a tentar ver a familia 1 ( A que tem todos os produtos, devemos ser redireccionados para a home page )
		if($_GET['cat']==1)
		{
			//echo '<meta http-equiv="refresh" content="0; url='.$site_http_url.'" />';
			die();
		}

		$tipo_catalogo='';
		if($_GET['page_id'] == $GLOBALS['destaque_page_id']) $tipo_catalogo='destaque';
		elseif($_GET['page_id'] == $GLOBALS['outlet_page_id']) $tipo_catalogo='outlet';
		elseif($_GET['page_id'] == $GLOBALS['promocao_page_id']) $tipo_catalogo='promocao';
		elseif($_GET['page_id'] == $GLOBALS['novidade_page_id']) $tipo_catalogo='novidade';
		elseif($_GET['page_id'] == $GLOBALS['dodia_page_id']) $tipo_catalogo='dodia';
		//elseif($_GET['page_id'] == $GLOBALS['catalog_page_id']) $tipo_catalogo='catalogo';

		if(isset($_GET['cat']) && $_GET['cat'] > 0)
		{
			$f_json=gera_json_categorias();
			$gera_cache='<script>'.$f_json.'</script>';

			/*$id_org_gerador=isset($_GET['cat']) ? $_GET['cat'] : (isset($_GET['c']) && $_GET['c'] > 0 ? $_GET['c'] : $fam_id_cf); // FAMILIAS
			$f_original='files/filtros/json_'.$id_org_gerador.'_'.$GLOBALS['site_lang'].'.js';
			$data_change=filemtime(SITE_SHELL_URL.$f_original);
			$gera_cache='<script type="text/javascript" src="'.$f_original.'?dp='.$data_change.'"></script>';*/
			//Se forem mais de 10.000 artigos não podem ser gerados filtros
			//if($j<10000)$FILTROS=\getGenFiles(array('cat' => intval($id_org_gerador),'FILTROS_SHOW' => 1,'site_country' => $_SESSION['ss_site_country']));
		}
		elseif($tipo_catalogo != '')
		{
			if($tipo_catalogo=='outlet')
			{
				$f_json=gera_json_outlet();
				$gera_cache=\gera_cache('files/filtros/json_'.urlencode($tipo_catalogo).'_'.$GLOBALS['site_lang'].'.js','',$f_json);
			}
			if($tipo_catalogo=='promocao')
			{
				$f_json=gera_json_cat_promocoes();
				$gera_cache=\gera_cache('files/filtros/json_'.urlencode($tipo_catalogo).'_'.$GLOBALS['site_lang'].'.js','',$f_json);
			}

			//$f_json=\getGenFiles(array('tipo_catalogo' => urlencode($tipo_catalogo),'PRODS_SHOW' => 1,'site_country' => $_SESSION['ss_site_country']));
			/*$gera_cache=\gera_cache('files/filtros/json_'.urlencode($tipo_catalogo).'_'.$GLOBALS['site_lang'].'.js','',$f_json);
			$FILTROS=\getGenFiles(array('tipo_catalogo' => urlencode($tipo_catalogo),'FILTROS_SHOW' => 1,'site_country' => $_SESSION['ss_site_country']));*/

			//$FILTROS=\getGenFiles(array('tipo_catalogo' => urlencode($tipo_catalogo),'FILTROS_SHOW' => 1,'site_country' => $_SESSION['ss_site_country']));

		}
		elseif((isset($_GET['page_id']) && $_GET['page_id'] > 0 && $_GET['page_id'] == $GLOBALS['catalog_page_id']) && !isset($_GET['cat']))
		{

			//echo '<meta http-equiv="refresh" content="0; url='.$site_http_url.'" />';
			//die();

			/*$f_json=\getGenFiles(array('ALLPRODUCTS' => 1,'PRODS_SHOW' => 1,'site_country' => $_SESSION['ss_site_country']));
			echo "------------teste--------";
			echo $gera_cache=\gera_cache('files/filtros/json_'.urlencode($tipo_catalogo).'_'.$GLOBALS['site_lang'].'.js','',$f_json);
			echo "------------teste--------";
			$FILTROS=\getGenFiles(array('ALLPRODUCTS' => 1,'FILTROS_SHOW' => 1,'site_country' => $_SESSION['ss_site_country']));*/

		}
		elseif((isset($_GET['page_id']) && $_GET['page_id'] > 0 && ($_GET['page_id'] == $GLOBALS['registo_page_id'] || $_GET['page_id'] == $GLOBALS['wishlist_page_id'])) && isset($_GET['show_wishlist']) &&  $_GET['show_wishlist'] == 1)
		{

			//WISHLIST


			//TODOss
			// OLD CODE
			/*$dados=cliente_logado();

			if($dados) $cli_id=$dados['cli_id']; else $cli_id=0;
			$prods=getWishListCliente($cli_id,$_SESSION['CLIENTE_ID_TEMP']);

			//Alterar a proxima linha //TODOss
			$f_json=\getGenFiles(array('filtro_wishlist' => 1,'session_p' => implode(';',$prods),'site_country' => $_SESSION['ss_site_country']));

			$gera_cache=\gera_cache('files/filtros/json_wishlist_'.uniqid().'_'.$GLOBALS['site_lang'].'.js','',$f_json);*/

			$dados=cliente_logado();

			if($dados) $cli_id=$dados['cli_id']; else $cli_id=0;
			$client_id_temp=$_SESSION['CLIENTE_ID_TEMP'];

			/*ss*/
			if($cli_id > 0 && $client_id_temp!='') $sql='SELECT cw.PROD_ID, obj_type_id,obj_pvp,obj_pvp2,obj_stock
				FROM clientes_wishlist AS cw INNER JOIN products on cw.PROD_ID = products.ID INNER JOIN products_objs on products.ID=products_objs.obj_type_id
	  			WHERE (cw.CLIENT_ID="'.$cli_id.'" OR cw.CLIENTE_ID_TEMP="'.$client_id_temp.'") AND products.Aproved=1  AND products_objs.obj_ativo=1 ORDER BY cw.ID DESC';
			else $sql='SELECT cw.PROD_ID, obj_type_id,obj_pvp,obj_pvp2,obj_stock
				FROM clientes_wishlist AS cw INNER JOIN products on cw.PROD_ID = products.ID INNER JOIN products_objs on products.ID=products_objs.obj_type_id
				WHERE (cw.CLIENT_ID="'.$cli_id.'" OR cw.CLIENTE_ID_TEMP="'.$client_id_temp.'") AND products.Aproved=1 AND products_objs.obj_ativo=1 ORDER BY cw.ID DESC';

			$rs->db_open($db_link,DB_NAME,$sql);
			if(!$rs->EOF())
			{
				$f_json='var products=[';
				$j=0;
				while(!$rs->EOF())
				{
					$m_id=$rs->db_data['obj_type_id'];
					$pvp=$price2=0;
					$prods=\get_produtc_info_essentials($m_id);
					if(is_array($prods))
					{
						$new_prods = '';
						$new_prods = verifica_promo($m_id, $prods['prod_precos']);
						if(!empty($new_prods))
						{
							$prods = $new_prods;
						}

						$global_promos='[{}]';
						if($prods['prod_precos']['PVP'] > 0)
						{
							$pvp=$prods['prod_precos']['PVP'];
							if($prods['prod_precos']['TEM_IVA'] == 0 && $pvp) $return .= number_format($pvp,2,',','.').' &euro;';
							else if($pvp) $return .= number_format($pvp,2,',','.').' &euro;';
						}
						if($prods['prod_precos']['PRECO_ANTIGO'] > 0)
						{
							$price2=$prods['prod_precos']['PRECO_ANTIGO'];
							$global_promos='['.\show_global_promos($m_id).']';
							// var_dump($global_promos); die();
						}

						// ARVORE DE FAMILIAS, PARA VÁRIOS NIVEIS
						$fam_id = $prods['fam_id'];
						$fams = BuscarArvoreFams_F_FrontEnd($fam_id);
						$fm = '';
						$families = array();
						for($a=0;$a<count($fams);$a++)
						{
							if($fams[$a] > 0) $families[] = array('', $fams[$a]);
							if($a > 0){ $fm .= '_f'.$fams[$a];}else{$fm .= 'f'.$fams[$a];}
						}

						$prod_id=$m_id;
						//$filtro_id = \getFiltroID($prods["prod_id_marca"]);
						//$link=$site_http_url.\seo('pages.php?page_id='.$GLOBALS['products_page_id'].'&prod_id='.$prod_id, $prod_id, 2);
						$link=$site_http_url.\seo2(2,$GLOBALS['products_page_id'],$prods,$GLOBALS['site_lang']);
						if($j>0)$f_json.=',';

						$categories_prod = \getCategoriesProds($m_id);
						$families_prod = \getFamiliesProds($families);

						if(!$families_prod)$families_prod='{}';
						if(!$categories_prod)$categories_prod='{}';

						$f_json.='{
							"id":"'.$m_id.'",
							"ord" : '.$prods['prod_ordem'].',
							"ref":"'.$prods['prod_ref'].'",
							"price" : '.$pvp.',
							"price_riscado" : '.$price2.',
							"pvp":"&euro; '.$pvp.'",
							"stock": "'.$prods['prod_precos']['STOCK'].'",
							"image":"'.$prods['prd_imgm_new'].'",
							"title":"'.addslashes($prods['prod_nome']).'",
							"subtitle":"'.preg_replace( "/\r|\n/", " ", (strip_tags($prods['prod_descricao'],'<br>'))).'",
							"caminho":"'.$link.'",
							"promo":"'.$prods['prod_precos']['PROMOCAO'].'",
							"promo_data":"0000-00-00",
							"global_promos" : '.$global_promos.',
							"destaque":"'.$prods['prod_precos']['DESTAQUE'].'",
							"m_id":"'.$prods['prod_id_marca'].'",
							"c" : ['.$categories_prod.'],
							"f" : ['.$families_prod.'],
							"u_i":"'.$prods["u_imagetiq"].'",
							"u_c":"'.$prods["u_catetiq"].'",
							"u_co":"'.$prods["u_coretiq"].'",
							"carrinho":"'.$prods['prod_carrinho'].'",
							"novidade":"'.$prods['prod_precos']['NOVO'].'",
							"novidade_data":"0000-00-00",
							"outlet":"'.$prods['prod_precos']['OUTLET'].'",
							"outlet_data":"0000-00-00",
							"destaque_data":"0000-00-00",
							"dodia_data":"0000-00-00",

							"fl":"0"}';
							$j++;
					}

					$rs->db_next();
				}
				$f_json.='];';
				$f_json=preg_replace('/[\x00-\x1F\x7F]/u', '', $f_json);

			}
			else
			{
				$f_json='var products=[];';
			}
			$rs->db_close();

			/* just to optimize and create less temporary files */
			if($cli_id > 0) $wish_file_id = $cli_id;
			else $wish_file_id = $_SESSION['CLIENTE_ID_TEMP'];

			$gera_cache=\gera_cache('files/filtros/json_wishlist_'.$wish_file_id.'_'.$GLOBALS['site_lang'].'.js','',$f_json);
		}
	}
	elseif(isset($_GET['s_marca']))
	{
		$f_json=gera_json_marcas();
		$id_org_gerador=(isset($_GET['s_marca']) && $_GET['s_marca'] > 0 ? $_GET['s_marca'] : 0);

		$f_original='files/filtros/json_m_'.$id_org_gerador.'_'.$GLOBALS['site_lang'].'.js';
		$data_change=filemtime(SITE_SHELL_URL.$f_original);
		$gera_cache='<script type="text/javascript" src="'.$f_original.'?dp='.$data_change.'"></script>';
	}
	elseif(isset($_GET['pesquisa']) )
	{
		$pesquisa=strip_html_tags($_GET['pesquisa']);
		$f_json=gera_json_pesquisa($pesquisa);
		if(substr_count($f_json,'window.location.href')){ echo $f_json;exit;}
		$pesquisaTxt= '<script>'.$f_json.'</script>';
	}
	else $pesquisaTxt= '';
	// OBTER TITULOS DAS PÁGINAS // TODO
	// $page_info=getPageInfo();

	if($SEO_title)$SEO_title=str_replace('"',' ',html_entity_decode($SEO_title,ENT_QUOTES,"UTF-8"));
	if($SEO_description)$SEO_description=str_replace('"',' ',html_entity_decode($SEO_description,ENT_QUOTES,"UTF-8"));

	if($_GET['amp']==1){include $GLOBALS['site_shell_url'].'index_amp.php'; die();}

	header("Expires: 0");
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Cache-Control: post-check=0, pre-check=0', FALSE);
	header('Pragma: no-cache');

?><!DOCTYPE html>
<html lang="<?=$GLOBALS['site_lang']?>">
<head>
<?php echo dotbright_files_images_browser_script(); ?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="robots" content="INDEX,FOLLOW,ARCHIVE">
    
    <meta name="viewport" content="user-scalable=no,width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1">
    
    <meta name="HandheldFriendly" content="true" />
	<link rel="canonical" href="<?=$https.$_SERVER['HTTP_HOST'].parse_url( $_SERVER['REQUEST_URI'],PHP_URL_PATH ); ?>" />
	<link rel="alternate" hreflang="<?=$GLOBALS['site_lang'];?>-<?=$GLOBALS['site_lang'];?>" href="<?=$http.$_SERVER['HTTP_HOST'].parse_url( $_SERVER['REQUEST_URI'],PHP_URL_PATH ); ?>" />
	<link rel="dns-prefetch" href="//fonts.googleapis.com"/>
	<link rel="dns-prefetch" href="//www.google-analytics.com"/>
	<meta property="og:title" content="<?php echo (isset($page_info) && $page_info['title'] !='' ? $page_info['title'] : $SEO_title); ?>">
	<meta property="og:url" content="<?=$GLOBALS['site_lang'];?>">
	<meta property="og:type" content="website">
	<meta property="og:description" content="<?php echo (isset($page_info) && $page_info['description'] !='' ? $page_info['description'] : $SEO_description); ?>">
	<?php if(isset($imgfb) && $imgfb){?>
	<link rel="image_src" href="<?=dotbright_image_url(preg_match('~^https?://~', $imgfb) ? $imgfb : $GLOBALS['site_http_url'].$imgfb)?>" />
	<meta property="og:image" content="<?=dotbright_image_url(preg_match('~^https?://~', $imgfb) ? $imgfb : $GLOBALS['site_http_url'].$imgfb)?>" />
	<? }?>
	<meta name="twitter:card" content="summary_large_image"/>
	<meta name="twitter:description" content="<?php echo (isset($page_info) && $page_info['description'] !='' ? $page_info['description'] : $SEO_description); ?>"/>
	<meta name="twitter:title" content="<?php echo (isset($page_info) && $page_info['title'] !='' ? $page_info['title'] : $SEO_title); ?>"/>
	<meta name="twitter:site" content="@<?=$TAG_EMPRESA?>"/>
	<meta name="twitter:image" content="<?=dotbright_image_url(preg_match('~^https?://~', $imgfb) ? $imgfb : $GLOBALS['site_http_url'].$imgfb)?>"/>
	<meta name="twitter:creator" content="@<?=$TAG_EMPRESA?>"/>
    <meta name="description" content="<?php echo (isset($page_info) && $page_info['description'] !='' ? $page_info['description'] : $SEO_description); ?>">
    <meta name="author" content="<?php echo AUTOR;?>">
	<meta name="revisit-after" content="3 days" />

    <title><?php echo (isset($page_info) && $page_info['title'] !='' ? $page_info['title'] : $SEO_title); ?></title>
	<?php
	/*
	//Descomentar se o site tiver varias linguas
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new Dataobj;
	$sql='SELECT Sigla,Cabecalho FROM linguas where Visivel=1 ORDER BY Ordem ASC';
	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	if(!$rs->EOF())
	{
		while(!$rs->EOF())
		{
			$sigla=$rs->db_data['Sigla'];
			echo '<link rel="alternate" hreflang="'.$sigla.'-'.$sigla.'" href="'.str_replace('-'.$GLOBALS['site_lang'],'-'.$sigla,$http.$_SERVER['HTTP_HOST'].parse_url( $_SERVER['REQUEST_URI'],PHP_URL_PATH )).'" />';
			echo "\n";

			$rs->db_next();
		}
		$rs->db_close();
	}
	*/?>
	<link rel="shortcut icon" href="files/icons/favicon_v_<?php echo dotbright_image_mtime('files/icons/favicon.ico')?>.ico" type="image/x-icon"/>
	<link rel="icon" type="image/png" sizes="96x96"   href="files/icons/favicon-96x96_v_<?php echo dotbright_image_mtime('files/icons/favicon-96x96.png')?>.png">
	<link rel="icon" type="image/png" sizes="32x32"	  href="files/icons/favicon-32x32_v_<?php echo dotbright_image_mtime('files/icons/favicon-32x32.png')?>.png">
	<link rel="icon" type="image/png" sizes="16x16"   href="files/icons/favicon-16x16_v_<?php echo dotbright_image_mtime('files/icons/favicon-16x16.png')?>.png">
	<?/*<link rel="manifest" href="<?=SITE_HTTP_URL?>files/icons/manifest_v_<?php echo dotbright_image_mtime('files/icons/manifest.json')?>.json">*/?>
	<!-- Chrome,Firefox OS and Opera -->
	<meta name="theme-color" content="<?=$GLOBALS['theme_color']?>">
	<!-- Windows Phone -->
	<meta name="msapplication-navbutton-color" content="<?=$GLOBALS['theme_color']?>">
	<!-- iOS Safari -->
	<meta name="apple-mobile-web-app-status-bar-style" content="<?=$GLOBALS['theme_color']?>">
	<meta name="msapplication-TileColor" content="<?=$GLOBALS['theme_color']?>">
	<meta name="msapplication-TileImage" content="files/icons/ms-icon-144x144_v_<?php echo dotbright_image_mtime('files/icons/ms-icon-144x144.png')?>.png">
	
	<? /* <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,900,900italic&subset=latin,cyrillic-ext,greek-ext,greek,vietnamese,latin-ext,cyrillic" type="text/css" media="all"/> */ ?>
	
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet">
	

	<?php
	// ********************************************************
	// SE FOR A HOME PAGE
	// ********************************************************
	if(defined('is_home'))
	{
		?>
		<script type="application/ld+json">
		{
			"@context": "http://schema.org",
			"@type": "WebSite",
			"name": "<?=$TAG_EMPRESA?>",
	  		"alternateName": "<?=$SEO_DESCRIPTION?>",
			"url": "<?=$GLOBALS['site_http_url']?>",
			"potentialAction": {
				"@type": "SearchAction",
				"target": "<?=$GLOBALS['site_http_url']?>p<?=$pesquisa_page_id?>-pesquisa-pt?pesquisa={search_term_string}",
				"query-input": "required name=search_term_string"
			}
		}
		</script>
		<?
	}
	// ********************************************************
	// SE FOR UM PRODUTO
	// ********************************************************
	if ($page_id == $GLOBALS['products_page_id'])
	{
		$precos=$prods['prod_precos'];
		$PODE_ENCOMENDAR=$precos['PODE_ENCOMENDAR'];

		$availability='Outofstock';
		if($PODE_ENCOMENDAR || $precos['STOCK'] > 0)$availability='InStock';

		?>
		<script type="application/ld+json">
	    {
	        "@context": "http://schema.org/",
	        "@type": "Product",
	        "name": "<?=$SEO_title?>",
	        "image": "<?=$imgfb?>",
	        "description": "<?=$SEO_description?>",
	        "brand": {
	            "@type": "Thing",
	            "name": "<?=$prods['prod_marca']['marca_nome']?>"
	        },
	        "offers": {
	            "@type": "Offer",
	            "priceCurrency": "<?php echo MOEDA_ISO?>",
	            "price": "<?=$prods['prod_precos']['PVP'];?>",
	            "priceValidUntil": "<?=date('Y-M-d',strtotime('+7 day'));?>",
	            "itemCondition": "http://schema.org/NewCondition",
	            "availability": "http://schema.org/<?=$availability?>",
	            "seller": {
	                "@type": "Organization",
	                "name": "<?=$TAG_EMPRESA?>"
	            },
				"url": "<?=$GLOBALS['site_http_url']?>"
	        }
	    }
	    </script>
	    <?
		$amp_url=$https.$_SERVER['HTTP_HOST'].parse_url( $_SERVER['REQUEST_URI'],PHP_URL_PATH );
		$amp_url=str_replace($https.$_SERVER['HTTP_HOST'].'/p',$https.$_SERVER['HTTP_HOST'].'/amp',$amp_url);
		?><link rel="amphtml" href="<?=$amp_url?>" />
		<?php
	}
	?>
	<!-- CSS COMUNS A TODOS OS PROJETOS -->
    <link rel="stylesheet" type="text/css" media="all" href="<?=SITE_HTTP_URL?>external_api/site/reset/reset_v_<?=dumpcache?>.css">
    <link rel="stylesheet" type="text/css" media="all" href="<?=SITE_HTTP_URL?>external_api/geral_v_<?=dumpcache?>.css">
	<!-- BOOTSTRAP -->
    <link rel="stylesheet" type="text/css" media="all" href="<?=SITE_HTTP_URL?>external_api/site/bootstrap/css/bootstrap.min.css">
    <link  rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.css">
	<!-- FONT AWESOME -->
    <link rel="stylesheet" type="text/css" media="all" href="<?=SITE_HTTP_URL?>external_api/site/font-awesome4/css/fontawesome.css">
    <script defer src="<?=SITE_HTTP_URL?>external_api/site/font-awesome4/js/all.js"></script>
    <!-- CSS PROJETO -->
	<link rel="stylesheet" type="text/css" media="all" href="<?=SITE_HTTP_URL?>layout/<?=PROJETO?>/filtros/filtros_v_<?=dumpcache?>.css">
	<link rel="stylesheet" type="text/css" media="all" href="<?=SITE_HTTP_URL?>layout/<?=PROJETO?>/css/css_external/global_v_<?=dumpcache?>.css">
	<link rel="stylesheet" type="text/css" media="all" href="<?=SITE_HTTP_URL?>layout/<?=PROJETO?>/css/css_external/blocktags_v_<?=dumpcache?>.css">
	<link rel="stylesheet" type="text/css" media="all" href="<?=SITE_HTTP_URL?>layout/<?=PROJETO?>/css/css_external/blocksearch_v_<?=dumpcache?>.css">
	<!-- CSS MENUS -->
	<link rel="stylesheet" type="text/css" media="all" href="<?=SITE_HTTP_URL?>layout/<?=PROJETO?>/menu/menu_topo_v_<?=dumpcache?>.css">
	<link rel="stylesheet" type="text/css" media="all" href="<?=SITE_HTTP_URL?>layout/<?=PROJETO?>/menu/menu_drop_v_<?=dumpcache?>.css">
	<!-- SCRIPTS EXTERNOS -->
	
	<link rel="stylesheet" type="text/css" media="all" href="<?=SITE_HTTP_URL?>external_api/site/reveal/jquery.fancybox_v_<?=dumpcache?>.css">
	<script>var APPRISE_LOGO='<?php echo (defined('LOGOTIPO_POPUPS') ? LOGOTIPO_POPUPS : 'layout/'.PROJETO.'/logos/logo_top_resp.png'); ?>';</script>
	<link rel="stylesheet" type="text/css" media="all" href="<?=SITE_HTTP_URL?>external_api/site/apprise/apprise-v2_v_<?=dumpcache?>.css">
	<link rel="stylesheet" type="text/css" media="all" href="<?=SITE_HTTP_URL?>external_api/site/chosen/chosen_v_<?=dumpcache?>.css">
	<link rel="stylesheet" type="text/css" media="all" href="<?=SITE_HTTP_URL?>external_api/site/mobile_menu/blurred-menu_v_<?=dumpcache?>.css">
	
	<link rel="stylesheet" type="text/css" media="all" href="<?=SITE_HTTP_URL?>external_api/site/mobile_menu/push2/powerlistmenu_v_<?=dumpcache?>8.css">
	
	<link rel="stylesheet" type="text/css" media="all" href="<?=SITE_HTTP_URL?>external_api/OverlayScrollbars-master/css/OverlayScrollbars.min.css">
	
	<!-- SLIDER PREÇOS -->
    <link rel="stylesheet" type="text/css" media="all" href="<?=SITE_HTTP_URL?>external_api/site/slider_precos/nouislider.css">
	<!-- SLIDE BLOCOS RESPONSIVO -->
	<link rel="stylesheet"  href="<?=SITE_HTTP_URL?>external_api/site/slider_blocos/css/lightslider_v_<?=dumpcache?>.css">
	<!-- MEDIAS -->
	<link rel="stylesheet" href="<?=SITE_HTTP_URL?>core/media/slides/js3/jquery.bxslider.css" type="text/css" media="all">
	<link rel="stylesheet" type="text/css" media="all" href="<?=SITE_HTTP_URL?>layout/<?=PROJETO?>/css/template_v_<?=dumpcache;?>.css">
	<!-- PRODUTOS/FILTROS -->
	<link rel="stylesheet" href="<?=SITE_HTTP_URL?>layout/<?=PROJETO?>/css/template_blocos_v_<?=dumpcache?>.css" type="text/css" media="all">
	<link rel="stylesheet" href="<?=SITE_HTTP_URL?>layout/<?=PROJETO?>/css/template_cart_v_<?=dumpcache?>.css" type="text/css" media="all">
	<link rel="stylesheet" href="<?=SITE_HTTP_URL?>layout/<?=PROJETO?>/css/template_media_v_<?=dumpcache?>.css" type="text/css" media="all">
	<!-- TABS -->
	<link href="<?=SITE_HTTP_URL?>external_api/site/tabs/css/easy-responsive-tabs_v_<?=dumpcache?>.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" type="text/css" href="<?=SITE_HTTP_URL?>external_api/site/jquery/jquery.accordion.css" />

	<link rel="stylesheet" type="text/css" href="<?=SITE_HTTP_URL?>external_api/site/megamenu/css/style_v_<?=dumpcache?>.css">
	<? /*<!-- PAGEFLIP -->
	<!-- <link rel="stylesheet" href="<?=SITE_HTTP_URL?>external_api/pageflip/css/normalize_v_<?=dumpcache?>.css"> -->
	<link rel="stylesheet" href="<?=SITE_HTTP_URL?>external_api/pageflip/wow_book/wow_book_v_<?=dumpcache?>.css" type="text/css"/>*/?>

	<!-- SmartMenus core CSS (required) -->
	<link href="<?=SITE_HTTP_URL?>layout/<?=PROJETO?>/smartmenus/css/sm-core-css.css" rel="stylesheet" type="text/css" />
	<!-- "sm-clean" menu theme (optional, you can use your own CSS, too) -->
	<link href="<?=SITE_HTTP_URL?>layout/<?=PROJETO?>/smartmenus/css/sm-clean/sm-clean.css" rel="stylesheet" type="text/css" />

	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

	<!-- GERADO -->
	<link href="<?=SITE_HTTP_URL?>layout/<?=PROJETO?>/css/template_geradas_v_<?=dumpcache?>.css" rel="stylesheet" type="text/css">
	<link href="<?=SITE_HTTP_URL?>layout/<?=PROJETO?>/css/category-arrows.css?v=20261029a" rel="stylesheet" type="text/css">
	<link href="<?=SITE_HTTP_URL?>layout/<?=PROJETO?>/css/catalogue-cards.css?v=20261029b" rel="stylesheet" type="text/css">
	<link href="<?=SITE_HTTP_URL?>layout/<?=PROJETO?>/css/storefront-ux.css?v=20261029b" rel="stylesheet">
	<link href="<?=SITE_HTTP_URL?>layout/<?=PROJETO?>/css/discovery.css?v=20261031a" rel="stylesheet">
	<link href="<?=SITE_HTTP_URL?>layout/<?=PROJETO?>/css/homepage-polish.css?v=20261030a" rel="stylesheet">
	<script defer src="<?=SITE_HTTP_URL?>layout/<?=PROJETO?>/js/homepage-polish.js?v=20261029c"></script>
	<link href="<?=SITE_HTTP_URL?>layout/<?=PROJETO?>/css/product-page-polish.css?v=20261031h" rel="stylesheet">
	<script defer src="<?=SITE_HTTP_URL?>layout/<?=PROJETO?>/js/storefront-ux.js?v=20261029b"></script>
	<link href="<?=SITE_HTTP_URL?>layout/<?=PROJETO?>/css/catalogo_v_<?=dumpcache?>.css" rel="stylesheet" type="text/css">
	<link href="<?=SITE_HTTP_URL?>external_api/pattern_lock/patternLock_v_<?=dumpcache?>.css" rel="stylesheet" type="text/css">

	<?php if(!$GLOBALS['tem_loja']){ ?>
	<link href="<?=SITE_HTTP_URL?>layout/<?=PROJETO?>/css/theme_institucional1_v_<?=dumpcache?>.css" rel="stylesheet" type="text/css">
	<?php } ?>

	<!-- TEMPLATE MELHORAMENTOS MOBILE -->
	<link href="<?=SITE_HTTP_URL?>layout/<?=PROJETO?>/css/mobile_template1_v_<?=dumpcache?>.css" rel="stylesheet" type="text/css">

	
	<link href="<?=SITE_HTTP_URL?>files/classes_v_<?=dumpcache?>.css" rel="stylesheet" type="text/css">

	<link rel="preload" href="<?=SITE_HTTP_URL?>layout/<?=PROJETO?>/vendor/fontawesome-free/webfonts/fa-regular-400.woff2" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" href="<?=SITE_HTTP_URL?>layout/<?=PROJETO?>/vendor/fontawesome-free/webfonts/fa-solid-900.woff2" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" href="<?=SITE_HTTP_URL?>layout/<?=PROJETO?>/vendor/fontawesome-free/webfonts/fa-brands-400.woff2" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" href="<?=SITE_HTTP_URL?>layout/<?=PROJETO?>/vendor/fonts/wolmart.ttf?png09e" as="font" type="font/ttf" crossorigin="anonymous">
	<link href="<?=SITE_HTTP_URL?>layout/<?=PROJETO?>/vendor/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="<?=SITE_HTTP_URL?>layout/<?=PROJETO?>/assets/vendor/swiper/swiper-bundle.min.css">
	

	<?php if(isset($GLOBALS['tem_menu_top']) && $GLOBALS['tem_menu_top'] == 1){?><link rel="stylesheet" href="layout/<?=PROJETO?>/menu/menu_topo_horizontal_v_<?=dumpcache?>.css" type="text/css" media="all" /><?php }?>
	<link rel="stylesheet" type="text/css" href="<?=SITE_HTTP_URL?>external_api/site/tooltipster/css/tooltipster.bundle.min.css" />
	<link rel="stylesheet" type="text/css" href="<?=SITE_HTTP_URL?>external_api/slider-revolution/css/settings_v_<?=dumpcache?>.css">
	<link rel="stylesheet" href="<?=SITE_HTTP_URL?>layout/<?=PROJETO?>/css/site-wide-polish.css?v=20261102a">
	<link rel="stylesheet" href="<?=SITE_HTTP_URL?>layout/<?=PROJETO?>/css/catalog-cards.css?v=20261127b">
	<link rel="stylesheet" href="<?=SITE_HTTP_URL?>layout/<?=PROJETO?>/css/catalog-filters.css?v=<?=filemtime(__DIR__.'/css/catalog-filters.css')?>">
	<link rel="stylesheet" href="<?=SITE_HTTP_URL?>layout/<?=PROJETO?>/css/catalog-header.css?v=<?=filemtime(__DIR__.'/css/catalog-header.css')?>">
	<script defer src="<?=SITE_HTTP_URL?>layout/<?=PROJETO?>/js/site-wide-polish.js?v=20261029a"></script>
	<script type="text/javascript">

		
		if (/iPad|iPhone|iPod/.test(navigator.userAgent)) {
		  window.document.addEventListener('touchmove', e => {
		    if(e.scale !== 1) {
		      e.preventDefault();
		    }
		  }, {passive: false});

		}

	var memogadgetcookiebot_path= "<?=$_SERVER["HTTP_HOST"]?>";
	var memogadgetcookiebot_path_http  = "<?=$_SERVER["REQUEST_SCHEME"]?>://<?=$_SERVER["HTTP_HOST"]?>";
	</script><?
	$memogadgetcookiebot_path = $_SERVER["HTTP_HOST"];
	$memogadgetcookiebot_path_no_www = substr($_SERVER["HTTP_HOST"], 3);
	?><script id="MemoGadgetCookiebot" src="<?=SITE_HTTP_URL?>cookies/memogadgetcookiebot_v_<?=dumpcache?>.js" data-ga="UA-99596070-1" data-cbid="5e1e5748-fccd-4860-97ce-796ba64f42b0" type="text/javascript"></script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="//oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="//oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
	<!-- JQUERY VAI FICAR NESTA ZONA TEMPORARIAMENTE -->
	<script src="<?=SITE_HTTP_URL?>external_api/site/jquery/jquery-1.12.3.min.js"></script>
	<script src="<?=SITE_HTTP_URL?>external_api/site/jquery/jquery-migrate-1.3.0.min.js"></script>
    <? echo $pesquisaTxt; ?>
	<script  src="<?=SITE_HTTP_URL?>external_api/site/mobile_menu/jquery-ui-1.js"></script>
	<script  src="<?=SITE_HTTP_URL?>external_api/site/mobile_menu/blurry-menu_v_<?=dumpcache?>.js"></script>
	<script  src="<?=SITE_HTTP_URL?>external_api/site/mobile_menu/push2/powerlistmenu.js"></script>
	<script  src="<?=SITE_HTTP_URL?>external_api/site/jquery/jquery.accordion.js"></script>

	<script >
		var powerinstance;
		jQuery(function($){
			powerinstance=new Powerlistmenu({menuid: 'powermenu1'});
			powerinstance2=new Powerlistmenu({menuid: 'powermenu2',rightarrow: '<span class="right">+</span>',selectedul: '#selected<?php echo (isset($_GET['frame']) && intval($_GET['frame']) > 0 ? intval($_GET['frame']) : ''); ?>'});
		});
	</script>
	<script src="<?=SITE_HTTP_URL?>files/linguas/linguas_v_<?=dumpcache?>.js"></script>
	<script src="<?=SITE_HTTP_URL?>external_api/site/bootstrap/js/bootstrap.min_v_<?=dumpcache?>.js"></script>
	<script src="<?=SITE_HTTP_URL?>external_api/site/bootstrap/js/bootstrap-datepicker_v_<?=dumpcache?>.js"></script>
	<script src="<?=SITE_HTTP_URL?>external_api/site/bootstrap/js/bootstrap-slider.min_v_<?=dumpcache?>.js"></script>
	<script src="<?=SITE_HTTP_URL?>external_api/site/bootstrap/js/bootstrap-select.min.js?d=<?=dumpcache?>"></script>
	<script src="<?=SITE_HTTP_URL?>external_api/site/bootstrap/js/i18n/<?=$GLOBALS['site_lang'];?>.min_v_<?=dumpcache?>.js"></script>
	<script src="<?=SITE_HTTP_URL?>external_api/site/chosen/chosen.jquery.min2_v_<?=dumpcache?>.js"></script>
	<script src="<?=SITE_HTTP_URL?>external_api/site/apprise/apprise-v2_v_<?=dumpcache?>.js"></script>
	<script src="<?=SITE_HTTP_URL?>external_api/site/tooltipster/js/tooltipster.bundle_v_<?=dumpcache?>.js"></script>
	<script src="<?=SITE_HTTP_URL?>external_api/site/reveal/jquery.fancybox-min_v_<?=dumpcache?>.js"></script>
	<script src="<?=SITE_HTTP_URL?>external_api/site/placeholder_v_<?=dumpcache?>.js"></script>
	<script src="<?=SITE_HTTP_URL?>external_api/site/jquery/jquery.easing.min.js"></script>
	<script src="<?=SITE_HTTP_URL?>external_api/site/filtros/jquery.cookies.2.2.0.min_v_<?=dumpcache?>.js"></script>
	<script>
		var TIPOPAGINA='<?php echo (isset($_GET['show_wishlist']) && $_GET['show_wishlist'] ? 'wishlist' : '')?>';
		var SITE_HTTP_URL='<?=$GLOBALS['site_http_url'];?>';
		var SITE_HTTP_AJAX_URL='<?=$GLOBALS['site_http_ajax_url'];?>';
		var PROJETO='<?=PROJETO;?>';
		var HOME='<?php echo (defined('is_home') ? 1 : 0);?>';
		var pesquisa='<?php if(isset($_GET["pesquisa"])) echo base64_encode($_GET["pesquisa"]); else echo "";?>';
		var id_produto='<?php if(isset($_GET["prod_id"])) echo intval($_GET["prod_id"])?>';
		var view_type='<?php if(isset($_GET['view'])) echo $_GET['view']; else echo 0 ?>';
		var ADICIONAR='<?php echo getLinguas('key_adicionar')?>';
		var homeslider_loop=1;
		var homeslider_pause=233000;
		var homeslider_speed=500;
		var homeslider_width=1170;
		<?php 

			if(isset($_GET["pesquisa"]) && $_GET["pesquisa"]!='')echo 'var defaultsortOrder=-1;';
			else 
			{
				if($REPARACOES)echo 'var defaultsortOrder=4;';
				else echo 'var defaultsortOrder=3;';
			}
		?>

		var LINGUA='<?=$GLOBALS['site_lang']?>';
		var PRODUCT_DIA='<?php echo detectProdutosDia();?>';
		var TEM_ENC_SEM_STOCK='<?php echo ((isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_enc_sem_stock'] == 1) ? 1 : 0);?>';
	</script>
	<script src="layout/<?=PROJETO?>/css/css_external/jquery.easing.js"></script>
	<script src="layout/<?=PROJETO?>/css/css_external/tools.js"></script>
	<script src="layout/<?=PROJETO?>/css/css_external/global.js"></script>
	<script src="layout/<?=PROJETO?>/css/css_external/10-bootstrap.min.js"></script>
	<script src="layout/<?=PROJETO?>/css/css_external/jquery.scrollTo.js"></script>
	<script src="layout/<?=PROJETO?>/css/css_external/jquery.autocomplete.js"></script>
	<script src="layout/<?=PROJETO?>/css/css_external/jquery.lazyload.js"></script>
	<script src="<?=SITE_HTTP_URL?>external_api/isotope/isotope.pkgd.min.js"></script>
	<script src="<?=SITE_HTTP_URL?>external_api/slider-revolution/js/jquery.themepunch.plugins.min.js"></script>
	<script src="<?=SITE_HTTP_URL?>external_api/slider-revolution/js/jquery.themepunch.revolution.min.js"></script>
	<script src="<?=SITE_HTTP_URL?>external_api/site/slider_blocos/js/lightslider_v_<?=dumpcache?>.js"></script>
	<? /*<script src="<?=SITE_HTTP_URL?>external_api/site/carouFredSel/jquery.carouFredSel-6.2.1_v_<?=dumpcache?>.js"></script> */ ?>
	<script src="<?=SITE_HTTP_URL?>external_api/site/filtros/thenBy.js"></script>
	<script src="<?=SITE_HTTP_URL?>external_api/OverlayScrollbars-master/js/OverlayScrollbars.min.js"></script>
	<script src="<?=SITE_HTTP_URL?>external_api/vticker/jquery.vticker.js?dp=<?php echo dumpcache?>"></script>
	<script src="<?=SITE_HTTP_URL?>external_api/pattern_lock/patternLock.min.js"></script>

	<!-- SmartMenus jQuery plugin -->
	<script type="text/javascript" src="layout/<?=PROJETO?>/smartmenus/jquery.smartmenus.js"></script>


	<?
	// $rs3=new DataObj;
	// $rs3->db_open($db_link,DB_NAME,'SELECT cookie_txt_barra,cookie_txt_modal,cookie_posicao FROM modulos_config_site');
	// if(!$rs3->EOF())
	// {
	// 	$cookie_txt_barra	=	$rs3->db_data['cookie_txt_barra'];
	// 	$cookie_txt_modal	=	$rs3->db_data['cookie_txt_modal'];
	// 	$cookie_posicao		=	$rs3->db_data['cookie_posicao'];

	// 	$cookie_txt_barra=preg_replace( "/\r|\n/", "", $cookie_txt_barra);
	// 	$cookie_txt_modal=preg_replace( "/\r|\n/", "<br>", $cookie_txt_modal);
	// }
	// $rs3->db_close();
	?>

	<script>
		var tipopagina='<?php echo (isset($tipo_catalogo) ? $tipopagina : '');?>';
		var today_date='<?php echo date('Y-m-d')?>';
		var linguas='';
		var filtro_selecionado='<?php if(isset($_GET['filtro'])) echo addslashes($_GET['filtro']); else echo "0";?>';
		var im_fnd="";
		var SITE_TITLE ="<?php if(isset($SEO_title))echo $SEO_title;?>";
		var SITE_PATH ="<?=$GLOBALS['site_http_url'];?>";
		var SESSAO="<?php echo (isset($GLOBALS['cli_id']) ? $GLOBALS['cli_id'] : '');?>";
		var REVEN2="";
		var REVEN="";
		var filtros_naoativos='';
		var marca_ativa='<?php echo (isset($marca_ativa) ? $marca_ativa : '');?>';
		var familia_ativa='<?php echo (isset($_GET['cat']) ? intval($_GET['cat']) : '');?>';
		<?php if(isset($produto) && $produto){?>var produto_ativo='2';<?php }else{?>var produto_ativo='';<?php }?>
		var pesquisa='<?php if(isset($_GET["pesquisa"])) echo base64_encode($_GET["pesquisa"]); else echo "";?>';
		var id_produto='<?php if(isset($_GET["prod_id"])) echo intval($_GET["prod_id"]);?>';
		var view_type='<?php if(isset($_GET['view'])) echo $_GET['view']; else echo 0 ?>';
		
		document.addEventListener("DOMContentLoaded", function() {
			OverlayScrollbars(document.querySelectorAll("body"), { });
		});
	</script>
	<!-- <script id="cookiebarjs" src="<?=SITE_HTTP_URL?>layout/cookiebar.js?dp=<?=dumpcache?>&lang=<?=$GLOBALS['site_lang']?>" ></script> --><?

	if(isset($gera_cache) && $gera_cache!=''){ echo $gera_cache; }

	if(isset($f_json) && $f_json!='' || isset($_GET["pesquisa"]) || isset($_GET["show_wishlist"]))
	{
		?>
		<script src="<?=SITE_HTTP_URL?>external_api/site/filtros/filter_v_<?=dumpcache?>.js?v=<?=filemtime(__DIR__.'/../../external_api/site/filtros/filter.js')?>"></script>
		<script src="<?=SITE_HTTP_URL?>layout/<?= PROJETO?>/js/bloco_produto_v_<?=dumpcache?>.js"></script>
		<script src="<?=SITE_HTTP_URL?>layout/<?= PROJETO?>/js/catalog-filters.js?v=<?=filemtime(__DIR__.'/js/catalog-filters.js')?>"></script>
		<?php 
	}?>

	<script src="<?=SITE_HTTP_URL?>external_api/site/filtros/filter_destroy_v_<?=dumpcache?>.js"></script>
	<script src="layout/<?=PROJETO?>/js/funcoes_v_<?=dumpcache?>.js"></script>
	<script src="<?=SITE_HTTP_URL?>core/media/slides/js3/jquery.bxslider.js"></script>
	<script src="<?=SITE_HTTP_URL?>layout/<?=PROJETO?>/menu/menu_drop.js"></script>
	<script src="<?=SITE_HTTP_URL?>external_api/site/tabs/js/easy-responsive-tabs.js"></script>

	<script src="<?=SITE_HTTP_URL?>external_api/site/wNumb/wNumb_v_<?=dumpcache?>.js"></script>
	<script src="<?=SITE_HTTP_URL?>external_api/site/slider_precos/nouislider_v_<?=dumpcache?>.js"></script>

	<?php if(isset($GLOBALS['tem_menu_top']) && $GLOBALS['tem_menu_top'] == 1){?><script src="layout/<?=PROJETO?>/menu/menu_topo_v_<?=dumpcache?>.js"></script><?php }?>
	<?php if(dotbright_image_resolve(SITE_SHELL_URL.'files/promos/promos.js')){?><script src="<?=SITE_HTTP_URL?>files/promos/promos.js?dp=<?=dumpcache?>"></script><?php }?>
	<!-- MEGAMENU -->
	<script src="<?=SITE_HTTP_URL?>external_api/site/megamenu/js/megamenu.js"></script>
	<!-- FICHEIRO QUE CHAMA TODAS AS FUNCOES -->
	<script src="<?=SITE_HTTP_URL?>layout/<?=PROJETO?>/js/site_v_<?=dumpcache?>.js?ux=20261029b"></script>
	<!-- MODULOS HTML5 -->
	<script src="<?=SITE_HTTP_URL?>layout/<?=PROJETO?>/js/theme_v_<?=dumpcache?>.js"></script>
