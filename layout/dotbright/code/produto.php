<?php
if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) exit();
function containsDecimal($value)
{
    $dec=explode('.', $value);
    if (isset($dec[1]) && $dec[1] > 0) return true;

    return false;
}

function mostra_bloco_simples($prods)
{
    $url=str_replace($GLOBALS['site_http_url'], '', seo('pages.php?page_id='.$GLOBALS['products_page_id'], $prods['prod_id'], 2));
    $dados=cliente_logado();

    // $return ='<div class="col-md-3">';
    $return .= '<div class="prd_d">';
    $return .= '<div class="prd_img"><a style="text-decoration:none" href="'.$url.'" title="'.$prods['prod_nome'].'"><img border="0" src="'.($prods['prd_imgm_new'] == '' ? 'layout/'.PROJETO.'/images/no_photo.png' :$prods['prd_imgm_new']).'" alt="..."></a></div>';
    $return .= '<div class="prd_layer">';
    $return .= '<div class="prd_title"><a target="_top" href="'.$url.'" title="'.$prods['prod_nome'].'">'.$prods['prod_nome'].'</a></div>';
    $return .= '<div class="clear"></div>';
    if ($prods['Ref_Ean'] != '')
	{
        $return .= '<div style="font-size:12px;color:#999">';
        $return .= $prods['Ref_Ean'];
        $return .= '</div>';
        $return .= '<div class="clear"></div>';
    }

    if ($dados)
    {
        if ($prods['prod_precos']['PROMOCAO'] == 1 && $prods['prod_precos']['PRECO_ANTIGO'] > 0 && $prods['prod_precos']['PRECO_ANTIGO'] > $prods['prod_precos']['PVP'])
		{
			$return .= '<div style="text-align:center;display:block;padding:5px;background:#f8f8f8;"><h5 style="padding:0px;margin:0px">'.$prod['global_promos'][0]['designacao'].'</h5><span style="font-size:10px;">'.substr($prod['global_promos'][0]['data_inicio'], 0, 10).' - '.substr($prod['global_promos'][0]['data_fim'], 0,10).'<b data-toggle="tooltip" title="" data-original-title="'.$prod['global_promos']['descricao'].'">&nbsp<img src="layout/'.PROJETO.'/images/alert.png" width="13"></b></span></div>';
            $return .= '<div info="'.$prods['prod_id'].'" id="precos_'.$prods['prod_id'].'" class="prdprice_n"><div class="promo"><p>'.numberformat(($prods['prod_precos']['PRECO_ANTIGO']), 3).'</p></div><div class="price"><p>'.numberformat($prods['prod_precos']['PVP'], 3).'</p></div></div>';
        }
		else
		{
            $return .= '<div info="'.$prods['prod_id'].'" id="precos_'.$prods['prod_id'].'" class="prdprice_n"><div class="price"><p>'.numberformat(($prods['prod_precos']['PVP']), 3).'</p></div></div>';
        }
        $return .= '<div class="clear"></div>';
    }
    $return .= '</div>';

    if ($prods['prod_precos']['PVP'] > 0 && $dados) {
        $return .= '<div class="prodby">'.getLinguas('key_adicionar').'<input type="hidden" name="prodid" value="'.$prods['prod_id'].'"><input type="hidden" name="prodfiltros" value=""></div>';
    }
    /* if($dados=cliente_logado())
    {
        $cli_id=$dados['cli_id'];
        if(isProductInWishList($prods['prod_id'], $cli_id))
        {
            $return .= '<div class="prd_whish HvRm produtowl'.$prods['prod_id'].'"  onclick="removeProductWishList2(\''.$prods['prod_id'].'\',\'0\');"><img src="layout/'.PROJETO.'/images/wishlist2_added.png" alt="Favorito" /></div>';
        }
        else
        {
            $return .= '<div class="prd_whish HvRm produtowl'.$prods['prod_id'].'"  onclick="addProductWishList2(\''.$prods['prod_id'].'\');"><img src="layout/'.PROJETO.'/images/wishlist2.png" alt="Favorito" /></div>';
        }
    }
    else
    {
        if(isset($_SESSION['wish_prod_'.$prods['prod_id']]))
        {
            $return .= '<div class="prd_whish HvRm produtowl'.$prods['prod_id'].'"  onclick="removeProductWishList2Offline(\''.$prods['prod_id'].'\',\'0\');"><img src="layout/'.PROJETO.'/images/wishlist2_added.png" alt="Favorito" /></div>';
        }
        else
        {
            $return .= '<div class="prd_whish HvRm produtowl'.$prods['prod_id'].'" onclick="addProductWishList2Offline(\''.$prods['prod_id'].'\');"><img src="layout/'.PROJETO.'/images/wishlist2.png" alt="Favorito" /></div>';
        }
    } */
    $return .= '</div>';
    // $return .= '</div>';

    return $return;
}



function get_personalizacao_produto($prod_id=0, $bloco_filho=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$ordem=0;$on=0;
	$sql2='
			SELECT
				bloco_titulo, Outros
			FROM
				products_personalizacao2 pp
				INNER JOIN personalizacao p ON (p.bloco_id=pp.bloco_id)
				INNER JOIN personalizacao_lang pl ON pl.bloco_id=p.bloco_id
			WHERE
				pp.prod_id="'.$prod_id.'"
				AND pp.Outros_on=1
				AND pp.Outros <> ""
			GROUP BY
				pp.bloco_id
			ORDER BY
				pp.ordem
			';
			 // echo $sql2.'<br>';
	$obj->db_open($db_link,$DB_name,$sql2);

	if(!$obj->EOF())
	{
		$return='';$a=1;$b=0;
		$TOTAL=$obj->db_rows;
		$TOTAL_COLUNA=ceil($TOTAL / 2);
		//echo $TOTAL;
		$return .= '<div class="col-md-12">';
		while(!$obj->EOF())
		{
			$ordem=$obj->db_data['ordem'];
			$bloco_id=$obj->db_data['bloco_id'];
			$bloco_titulo=$obj->db_data['bloco_titulo'];
			$Outros=$obj->db_data['Outros'];
			$bloco_resumo=$obj->db_data['bloco_resumo'];
			$bloco_titulo_campo=limpa_special($obj->db_data['bloco_titulo']);
			$bloco_id_filho=$obj->db_data['bloco_id_filho'];
			$bloco_tipo=$obj->db_data['bloco_tipo'];

			$bloco_obrigatorio=$obj->db_data['obrigatorio'];
			$obrigatorio_el_id=$obj->db_data['obrigatorio_el_id'];
			$bloco_aparece=$obj->db_data['aparece'];
			$aparece_el_id=$obj->db_data['aparece_el_id'];

			$bloco_preco=$obj->db_data['bloco_preco'];

			// $atributos=get_personalizacao_produto2($prod_id, $bloco_id, $bloco_tipo, $bloco_titulo, $bloco_resumo, $bloco_titulo_campo, $bloco_obrigatorio, $bloco_preco, $obrigatorio_el_id);
			// $return1=$atributos['return'];

			// $atributos2=get_personalizacao_produto2_outros($prod_id, $bloco_id);
			// $return2=$atributos2['return'];

			// if($return1 != '' || $return2 != '')
			// {

				$return .= '<div class="clear"></div>';
				$return .= '<div class="col-md-3" style="padding-top:5px">';

				$return .= '<h5 style="margin-top:0;"><b>'.$bloco_titulo.'</b></h5>';

				$return .= '</div><div class="col-md-9" style="padding-top:5px">';
				$return .= nl2br($Outros);
				// if($return1 != '') $return .= $return1;
				// if($return2 != '') $return .= $return2;


				$return .= '</div>';
			// }
			$b++;
			$obj->db_next();
		}
		$return .='</div>';
	}
	$obj->db_close(1);

	return $return;
}

function get_personalizacao_produto2($prod_id=0, $bloco_id_pai=0, $bloco_tipo=0, $bloco_titulo_p='', $bloco_titulo_p2='', $bloco_titulo_campo='', $bloco_obrigatorio=0, $bloco_preco=0, $obrigatorio_el_id=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$ordem=0;$on=0;
	$sql2='
			SELECT
				pp.bloco_id, pp.ordem,bloco_titulo, bloco_resumo, bloco_preco, pp.bloco_id_filho
			FROM

				products_personalizacao pp
				INNER JOIN personalizacao p ON (p.bloco_id=pp.bloco_id_filho)
				INNER JOIN personalizacao_lang pl ON pl.bloco_id=p.bloco_id
			WHERE
				pp.prod_id="'.$prod_id.'"
				AND pp.bloco_id="'.$bloco_id_pai.'"
				AND pp.Outros_on=0
				AND pl.bloco_lang="'.$GLOBALS['site_lang'].'"
			ORDER BY
				pp.ordem, pp.ordemp
			';
			// echo $sql2.'<br>';
	$obj->db_open($db_link,$DB_name,$sql2);

	if(!$obj->EOF())
	{
		$return='';


		$return2='';
		while(!$obj->EOF())
		{
			$ordem=$obj->db_data['ordem'];
			$bloco_id=$obj->db_data['bloco_id'];
			$bloco_titulo=$obj->db_data['bloco_titulo'];
			$bloco_id_filho=$obj->db_data['bloco_id_filho'];
			$bloco_preco=$obj->db_data['bloco_preco'];


			$return .= '<h5 style="margin-top:0;font-weight:normal">'.$bloco_titulo.'</h5>';

			$obj->db_next();
		}


	}
	$obj->db_close(1);

	return $return;
}

function get_personalizacao_produto2_outros($prod_id=0, $bloco_id_pai=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$ordem=0;$on=0;
	$sql2='
			SELECT
				pp.Outros
			FROM

				products_personalizacao pp
			WHERE
				pp.prod_id="'.$prod_id.'"
				AND pp.bloco_id="'.$bloco_id_pai.'"
				AND pp.Outros_on=1
			ORDER BY
				pp.ordem
			';
			// $return .= $sql2.'<br>';
	$obj->db_open($db_link,$DB_name,$sql2);

	if(!$obj->EOF())
	{
		$return='';


		$return2='';
		while(!$obj->EOF())
		{

			$bloco_titulo=$obj->db_data['Outros'];



			$return .= '<h5 style="margin-top:0;font-weight:normal">'.str_replace("\n", '<br>', $bloco_titulo).'</h5>';

			$obj->db_next();
		}
	}
	$obj->db_close(1);

	return $return;
}

function ficha_produto_amp($prods)
{
	$tem_qtt=0;$label_promo='';
	$precos=$prods['prod_precos'];
	$PODE_ENCOMENDAR=$precos['PODE_ENCOMENDAR'];
    $w=322;
	$h=79;
    $tam=count($prods['prd_imgs']['img_cart']);

	$return .= "<div class='container'><div class=\"header\">";
	$return .= '<div class="amp">';
    $return .= '<amp-img src="/files/modulos_config_site/1_1.png"
	    width="'.$w.'"
	    height="'.$h.'"
	    layout="responsive"
	    alt="'.rtrim($prods['prod_nome'], '-').'"></amp-img>';
	$return .= '</div></div>';

	$return .='



      <h2 class="m1">'.rtrim($prods['prod_nome'], '-').'</h2>';
    if ($prods['Ref_Ean'] != '') $return .= '<h5 class="amp"><strong>EAN '.$prods['Ref_Ean'].'</strong></h5>';
	if (!substr_count($prods['prd_imgm_med'], 'no_photo'))
	{
		$w=298;
		$h=298;

		$return .= '<amp-img src="'.$prods['prd_imgs']['img_med'][0].'"
	    width="'.$w.'"
	    height="'.$h.'"
	    layout="responsive"
	    alt="'.rtrim($prods['prod_nome'], '-').'"></amp-img>';
	}
	else
	{
		$return .= '<amp-img src="'.$prods['prd_imgm_med'].'"
	    width="'.$w.'"
	    height="'.$h.'"
	    layout="responsive"
	    alt="'.rtrim($prods['prod_nome'], '-').'"></amp-img><br>';
	}


	$return .='

      <div class="m1">
        <amp-social-share type="twitter"
                          width="30"
                          height="22"></amp-social-share>
        <amp-social-share type="gplus"
                          width="30"
                          height="22"></amp-social-share>
        <amp-social-share type="email"
                          width="30"
                          height="22"></amp-social-share>
        <amp-social-share type="pinterest"
                          width="33"
                          height="22"></amp-social-share>
      </div>

      <p id="product-description" class="m1">'.strip_html_tagsc($prods['prod_nome'].' '.$prods['prod_descricao2']);




	$return .='
	</p>


	<p class="price-description">Preço:';


	$tem_qtt=0;
	if($prods['prod_precos']['PVP'] > 0)
	{
		$pvp=$prods['prod_precos']['PVP'];
		if($prods['prod_precos']['TEM_IVA'] == 0 && $pvp) $return .= numberformat($pvp);
		else if($pvp) $return .= '<div class="amp"><strong class="amp_preco">'.numberformat($pvp).'</strong></div><br>';
	}



	$stock_txt='';
	if($precos['STOCK'] >= 3) $stock_txt='<div class="amp disponivel" title="Disponibilidade Total"> Stock :Disponibilidade Total</div>';
	elseif($precos['STOCK'] == 2) $stock_txt='<div class="amp limitado" title="Disponibilidade Reduzida"> Stock :Disponibilidade Reduzida</div>';
	elseif($precos['STOCK'] == 1) $stock_txt='<div class="amp porencomenda" title="Disponivel por Encomenda"> Stock :Disponivel por Encomenda</div>';
	else $stock_txt='<div class="amp esgotado" title="Disponivel Brevemente"> Stock :Disponivel Brevemente</div>';
	$return .= $stock_txt;

	$return .='</p>';


	$return .='

  <amp-selector role="tablist" layout="container" class="ampTabContainer ampstart-headerbar-nav" keyboard-select-mode="select">
  <div role="tab" class="tabButton h4 ampstart-nav-item" selected option="a">SOBRE</div>
  <div role="tabpanel" class="tabContent p1 p">';

	$tem_filtros=0;
    $filtros=filtros_ficha_produto($prods['prod_id'], $prods['fam_id']);
	if ($filtros) $tem_filtros=1;
	$filtros2=filtros_lista_produto($prods['prod_id'], $prods['fam_id']);
	if($filtros2)
	{
		$return .= $filtros2;
	}



	global $https;
	$canonical=str_replace('amp','p',$https.$_SERVER['HTTP_HOST'].parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ));
	$return .= '<a href="'.$canonical.'"><button class="amp amp_btn btn" type="button"><span>'.getLinguas('key_comprar').'</span></button></a>';
	$tem_qtt=1;


	$descricao_verifica=strip_html_tags($prods['prod_descricao']);
	$detalhe_verifica=strip_html_tags($prods['prod_como_funciona']);
	$ficha_verifica=strip_html_tags($prods['prod_envio']);
	$prod_video=$prods['prod_video'];
	$script=str_replace(array('http://youtu.be/', 'https://youtu.be/', 'http://www.youtube.com/watch?v=', 'https://www.youtube.com/watch?v='), '//www.youtube.com/embed/',$prod_video);


	if($descricao_verifica !='' || $detalhe_verifica != '' || $ficha_verifica != '' || $prod_video != '')
	{
		$return .= '<div class="amp">

		<div class="resp-tabs-container amp">
		'.($descricao_verifica!='' ? '<div class="amp amp_small"><p>'.nl2br(strip_html_tags_fp($prods['prod_descricao2'])).'</p></div>' :'').'
		'.($detalhe_verifica!='' ? '<div class="amp amp_small"><p>'.$prods['prod_como_funciona'].'</p></div>' :'').'
		'.($ficha_verifica!='' ? '<div class="amp amp_small"><p>'.$prods['prod_envio'].'</p></div>' :'').'
		'.($prod_video!='' ? '<div class="amp"><p><amp-youtube layout="responsive" autoplay width="480" height="270" data-videoid="'.str_replace('//www.youtube.com/embed/','',$script).'"></amp-youtube></p></div>' :'').'

		</div>
		</div>';
	}
	$return .= "</div>";
	$return .= '
	</div>
	</amp-selector>';
	/*

    $tem_filtros=0;
    $filtros=filtros_ficha_produto($prods['prod_id'], $prods['fam_id']);
	if ($filtros) $tem_filtros=1;

	$return .= '<h1 itemprop="name" class="amp titulo-prod">'.rtrim($prods['prod_nome'], '-').'</h1><br><br>';

	$filtros2=filtros_lista_produto($prods['prod_id'], $prods['fam_id']);
	if($filtros2)
	{
		$return .= '<div class="amp">'.$filtros2.'</div>';

	}
	$tem_qtt=0;
	if($prods['prod_precos']['PVP'] > 0)
	{
		$pvp=$prods['prod_precos']['PVP'];
		if($prods['prod_precos']['TEM_IVA'] == 0 && $pvp) $return .= numberformat($pvp);
		else if($pvp) $return .= '<div class="amp"><strong>'.numberformat($pvp).'</strong></div><br>';
	}
	if(($precos['STOCK'] > 0 || $PODE_ENCOMENDAR == 1)&& $prods['prod_precos']['PVP'] > 0 && $prods['prod_carrinho'] == 1)
	{
		if($tem_filtros) $return .= $filtros;
	}

	$stock_txt='';

	if($precos['STOCK'] >= 3) $stock_txt='<div class="amp disponivel" title="Disponibilidade Total"> Stock :Disponibilidade Total</div>';
	elseif($precos['STOCK'] == 2) $stock_txt='<div class="amp limitado" title="Disponibilidade Reduzida"> Stock :Disponibilidade Reduzida</div>';
	elseif($precos['STOCK'] == 1) $stock_txt='<div class="amp porencomenda" title="Disponivel por Encomenda"> Stock :Disponivel por Encomenda</div>';
	else $stock_txt='<div class="amp esgotado" title="Disponivel Brevemente"> Stock :Disponivel Brevemente</div>';

	$return .= $stock_txt;
	global $https;
	$canonical=str_replace('amp','p',$https.$_SERVER['HTTP_HOST'].parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ));
	$return .= '<a href="'.$canonical.'"><button class="amp amp_btn btn" type="button"><span>'.getLinguas('key_comprar').'</span></button></a>';
	$tem_qtt=1;


	$descricao_verifica=strip_html_tags($prods['prod_descricao']);
	$detalhe_verifica=strip_html_tags($prods['prod_como_funciona']);
	$ficha_verifica=strip_html_tags($prods['prod_envio']);
	$prod_video=$prods['prod_video'];
	$script=str_replace(array('http://youtu.be/', 'https://youtu.be/', 'http://www.youtube.com/watch?v=', 'https://www.youtube.com/watch?v='), '//www.youtube.com/embed/',$prod_video);


	if($descricao_verifica !='' || $detalhe_verifica != '' || $ficha_verifica != '' || $prod_video != '')
	{
		$return .= '<div class="amp">

		<div class="resp-tabs-container amp">
		'.($descricao_verifica!='' ? '<div class="amp amp_small"><p>'.nl2br(strip_html_tags_fp($prods['prod_descricao2'])).'</p></div>' :'').'
		'.($detalhe_verifica!='' ? '<div class="amp amp_small"><p>'.$prods['prod_como_funciona'].'</p></div>' :'').'
		'.($ficha_verifica!='' ? '<div class="amp amp_small"><p>'.$prods['prod_envio'].'</p></div>' :'').'
		'.($prod_video!='' ? '<div class="amp"><p><amp-youtube layout="responsive" autoplay width="480" height="270" data-videoid="'.str_replace('//www.youtube.com/embed/','',$script).'"></amp-youtube></p></div>' :'').'

		</div>
		</div>';
	}

	$return .= "</div>";
	*/
	$return .= '<div class="amp amp_bg_dark"><a class="amp_url" href="http://amp.memogadget.com">powered by AMP memogadget.com</a></div>';

    return $return;
}

function ficha_produtoold($prods)
{
    
	$new_prods = verifica_promo($prods['prod_id'], $prods['prod_precos']);
        
	if(!empty($new_prods))
	{
		$prods = $new_prods;
	}
	if(isset($_GET['amp']) && intval($_GET['amp']) == 1) return ficha_produto_amp($prods);

	$not_produto_standard=$prods['produto_standard'];
	$c_submissao_ficheiro=$prods['c_submissao_ficheiro'];

	if($not_produto_standard==1)return ficha_produto_nstandard($prods);

	global $tem_produtos_visualizados;
    $dados_cliente=cliente_logado();


    $P_CONF=unserialize(PROD_CONF);

    if($tem_produtos_visualizados)
    {
	    ?>

	    <script>
		$(document).ready(function()
		{
			$.ajax({
	        type:"POST",
	        url:SITE_HTTP_AJAX_URL + "addRemoveProductVisualizados.php",
	        data:{action:"add", prodid:<?php echo $prods['prod_id'];?>}
		    }).done(function (resultado) {


		    });
		});

	    </script>

	    <?php
	}

	$return='<link rel="stylesheet" href="layout/'.PROJETO.'/css/css_external/product_v_'.filemtime('layout/'.PROJETO.'/css/css_external/product.css').'.css" media="all"/> ';

	$return .= '<script>quantityAvailable=9999;</script>';
	$return .= '<script type="text/javascript" src="layout/'.PROJETO.'/css/css_external/product.js"></script> ';
    $return .= BREADCRUMBS(1, intval($prods['fam_id']));


	$tem_qtt=0;
	$label_promo='';
	$precos=$prods['prod_precos'];

	$PODE_ENCOMENDAR=$precos['PODE_ENCOMENDAR'];

	$today = date("U");

	if ((($precos['STOCK'] > 0 || $PODE_ENCOMENDAR == 1) && $prods['prod_precos']['PVP'] > 0 && $prods['prod_carrinho'] == 1))
	{
		if ($prods['prod_precos']['PROMOCAO'] == 1 && (strtotime($prods['prod_precos']['Promo_data_ini']) <= $today && strtotime($prods['prod_precos']['Promo_data_fim']) >=$today))
		{
			//$return .= '<div style="text-align:center;display:block;padding:5px;background:#f8f8f8;"><h5 style="padding:0px;margin:0px">'.$prods['global_promos'][0]['designacao'].'</h5><span style="font-size:10px;">'.substr($prods['global_promos'][0]['data_inicio'], 0, 10).' - '.substr($prods['global_promos'][0]['data_fim'], 0,10).'<b data-toggle="tooltip" title="" data-original-title="'.$prods['global_promos']['descricao'].'">&nbsp<img src="layout/'.PROJETO.'/images/alert.png" width="13"></b></span></div>';
			$label_promo='<div class="ribbon-wrapper2"><img src="layout/'.PROJETO.'/images_gerado/promo.png" alt="" /></div>';
		}
	}

    $return .= '<div class="primary_block row"><div class="container"><div class="top-hr"></div></div>';


    if($not_produto_standard)$return.='<input type="hidden" id="produto_standard" name="produto_standard" value="0">';
	else $return.='<input type="hidden" id="produto_standard" name="produto_standard" value="1">';


    $return .= '<div class="pb-left-column col-xs-12 col-sm-12 col-md-6">';



    $tam=count($prods['prd_imgs']['img_cart']);
	if($prods['u_catetiq'] )
	{
		if($prods['u_imagetiq'] != '')
		{
			$temp=explode(".",$prods['u_imagetiq']);
			if(strtolower(end($temp)) == "pdf" ||  strtolower(end($temp)) == "jpg" ||  strtolower(end($temp)) == "png")
			{
				$return .= '<a href="'.$prods['u_imagetiq'].'" target="_blank" class="eenergia" style="background-color:'.$prods['u_coretiq'].';border-right-color:'.$prods['u_coretiq'].'">'.$prods['u_catetiq'].'</a>';
			}
			else
			{
				$return .= '<a class="eenergia fancybox" style="background-color:'.$prods['u_coretiq'].';border-right-color:'.$prods['u_coretiq'].';cursor:pointer;" href="'.$prods['u_imagetiq'].'">'.$prods['u_catetiq'].'</a>';
			}
		}
		else
		{
			$return .= '<div class="eenergia" style="background-color:'.$prods['u_coretiq'].';border-right-color:'.$prods['u_coretiq'].'">'.$prods['u_catetiq'].'</div>';
		}
		if($prods['EtiquetaEnergiaNova']==1) $return .= '<div class="g_a">&nbsp;</div>';
	}

	if (!strpos($prods['prd_imgs']['img'], 'no_photo') !== false) {
		$img_type = array('img_cart','img_med','img_new','img_news','img_fb');
		$m = 1;
		foreach ($img_type as $imty) {
			if (strpos($prods['prd_imgs'][$imty], 'no_photo') !== false) {
			    $m = 0;
			    break;
			}
		}
		if(!$m){
			$newimg = gerarneededimages($prods['prod_id']);
		}
	}

	$return .= "<div class='product-gallery-block resp1_'>";
	$return .= '<div class="campanhas-prods" style="display:none;position:absolute;top:0;z-index:9999"></div>';
	$return .= "<ul class='product-gallery'>";

	if (!substr_count($prods['prd_imgm_med'], 'no_photo'))
	{
		for ($figi=0;$figi < $tam;$figi++)
		{
			if($figi==0)
			{
				$return .= '<li class="item active" data-thumb="'.$prods['prd_imgs']['img_med'][$figi].'">';
				$return .= $label_promo;
				if($tam>1)$return .= '<a href="'.$prods['prd_imgs']['img'][$figi].'" target="_blank" class="fancybox" id="big'.$figi.'" data-fancybox-group="gallery"><img class="zoom" src="'.$prods['prd_imgs']['img_med'][$figi].'" alt="'.rtrim($prods['prod_nome'], '-').'" border="0" /></a>';
				else $return .= '<a href="'.$prods['prd_imgs']['img'][$figi].'" target="_blank" class="fancybox" id="big'.$figi.'" data-fancybox="gallery"><img class="zoom" src="'.$prods['prd_imgs']['img_med'][$figi].'" alt="'.rtrim($prods['prod_nome'], '-').'" border="0" /></a>';
				$return .= '</li>';
			}
			else
			{
				$return .= '<li class="item" data-thumb="'.$prods['prd_imgs']['img_med'][$figi].'">'.$label_promo.'<span class="helper 5"></span><a href="'.$prods['prd_imgs']['img'][$figi].'" target="_blank" class="fancybox" id="big'.$figi.'" data-fancybox-group="gallery"><img src="'.$prods['prd_imgs']['img_med'][$figi].'" alt="'.rtrim($prods['prod_nome'], '-').'" border="0" /></a></li>';
			}
		}
	}
	else
	{
		$return .= '<li class="item active"  data-thumb="'.$prods['prd_imgs']['img_med'][$figi].'">'.$label_promo.'<span class="helper 6"></span><img src="'.$prods['prd_imgm_med'].'" alt="'.rtrim($prods['prod_nome'], '-').'" border="0" /></li>';
	}

	$return .= '</ul>';

	$return .= "</div>";
	/*$return .= "<div class='product-gallery-block resp2'>";
	$return .= '<div class="campanhas-prods" style="display:none;position:relative;z-index:9999"></div>';
	$return .= "<ul class='product-gallery-resp'>";

	if (!substr_count($prods['prd_imgm_med'], 'no_photo'))
	{
		for ($figi=0;$figi < $tam;$figi++)
		{
		if($figi==0) {$return .= '<li class="item active" data-thumb="'.$prods['prd_imgs']['img_med'][$figi].'">'.$label_promo.'<span class="helper"></span><a href="'.$prods['prd_imgs']['img'][$figi].'" target="_blank" class="fancybox" id="big'.$figi.'" data-fancybox-group="gallery"><img src="'.$prods['prd_imgs']['img_med'][$figi].'" alt="'.rtrim($prods['prod_nome'], '-').'" border="0" /></a></li>';}
		else {$return .= '<li class="item" data-thumb="'.$prods['prd_imgs']['img_med'][$figi].'">'.$label_promo.'<span class="helper"></span><a href="'.$prods['prd_imgs']['img'][$figi].'" target="_blank" class="fancybox" id="big'.$figi.'" data-fancybox-group="gallery"><img src="'.$prods['prd_imgs']['img_med'][$figi].'" alt="'.rtrim($prods['prod_nome'], '-').'" border="0" /></a></li>';}
		}
	}
	else
	{
		$return .= '<li class="item active"  data-thumb="'.$prods['prd_imgs']['img_med'][$figi].'">'.$label_promo.'<span class="helper"></span><img src="'.$prods['prd_imgm_med'].'" alt="'.rtrim($prods['prod_nome'], '-').'" border="0" /></li>';
	}

	$return .= '</ul>';
	$return .= '</div>';*/


    $tem_filtros=0;
    $filtros = filtros_ficha_produto($prods['prod_id'], $prods['fam_id']);

	if ($filtros) $tem_filtros=1;


	$return .= '</div>';
	$return .= '<div class="pb-right-column col-xs-12 col-sm-12  col-md-1"></div>';

	$return .= '<div class="pb-center-column col-xs-12 col-sm-12 col-md-5">
	<div class="row">
	<div class="col-sm-12">
	<h1 itemprop="name" class="titulo-prod">'.rtrim($prods['prod_nome'], '-').'</h1>';

	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang,$site_http_url;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$obj = new Dataobj;
	$sql = 'SELECT * FROM brands_lang WHERE ID_PAI =(SELECT ID_Marca FROM products whERE id = '.$prods['prod_id'].')';
	$obj->db_open($db_link, $DB_name, $sql);
	if(!$obj->EOF())
	{
		$prods_marca_lang = $obj->db_data['Nome'];
	}
	$obj->db_close();

	if ($prods['Ref_Ean'] != '') $return .= '<h5 style="margin-top:0"><strong>EAN '.$prods['Ref_Ean'].'</strong></h5>';
	$return .= '<span><h4>'.$prods_marca_lang.'</h4></span>';
	$return .= '<div class="clear"></div> <br>';
	

	$return .= '<style> .awesome-content{display:inline-block;width:10px;height:10px;opacity:0.8;font-size:13px;margin-left:4px;margin-top:-2px;} </style>';
	$return .= '<div class="inputs">';

	// $filtros2 = filtros_lista_produto($prods['prod_id'], $prods['fam_id']);
	if($filtros2)
	{
		$return .= '<div class="descricao">';
		$return .= $filtros2;
		$return .= '</div>';
	}

	/* if (strip_html_tags($prods['prod_descricao']) != '')
	{

		$return .= '
		<div class="descricao">
		<p>'.nl2br(strip_html_tags_fp($prods['prod_descricao'])).'</p>
		</div>';
	} */

	$return .= '</div></div></div><div class="clear"></div>';


	$return .= '<div class="box-info-product">';
	$tem_qtt=0;
		$return .= '<div class="content_prices clearfix">';
		if ($prods['prod_precos']['PROMOCAO'] == 1 && strtotime($prods['prod_precos']['Promo_data_fim']) >= strtotime($prods['prod_precos']['Promo_data_ini']) && $prods['prod_precos']['Promo_data_fim']!='0000-00-00' && $prods['prod_precos']['Promo_data_ini']!='0000-00-00')
		{
			$return .= '<div class="tag tag-promocao" style="font-size:20px;line-height:28px">';
			$return .= '<span style="text-decoration:line-through;color:#656565">'.numberformat($prods['prod_precos']['PRECO_ANTIGO'],2).'</span><br>';
			$return .= '</div>';
		}
		$return .= '<div class="price" id="pricechange">';
		$txt_campanhas = '';
		if ($prods['prod_precos']['PROMOCAO'] == 1 && (strtotime($prods['prod_precos']['Promo_data_ini']) <= $today && strtotime($prods['prod_precos']['Promo_data_fim']) >=$today))
		{
			$txt_campanhas = $prods['prod_precos']['Promo_descricao'];
		}
		$return .= '<p class="our_price_display" itemtype="http://schema.org/Offer" itemscope="" itemprop="offers">';
		if($prods['prod_precos']['PVP'] > 0)
		{
			$pvp=$prods['prod_precos']['PVP'];

			if($prods['prod_precos']['IVA'] > 0)
			{
				$pvp_com_iva=$prods['prod_precos']['PVP'];
				$pvp_sem_iva=($prods['prod_precos']['PVP']/(1+$prods['prod_precos']['IVA']/100));
			}
			else
			{
				$pvp_sem_iva=$prods['prod_precos']['PVP'];
				$pvp_com_iva=($prods['prod_precos']['PVP']*(1+$prods['prod_precos']['IVA']/100));
			}
			// a pedido do cliente dia 18-01-2021
			// $return .= '<span style="font-size:16px">'.numberformat($pvp_sem_iva).' (Sem IVA)</span><br>';
			//$return .= '<span style="font-size:26px;color:#10892b">'.numberformat($pvp_com_iva).' (Com IVA)</span><br>';
			$return .= '<span style="font-size:26px">'.numberformat($pvp_com_iva).'</span><br>';
			/*if($prods['prod_precos']['TEM_IVA'] == 0 && $pvp) $return .= numberformat($pvp).' ';
			else if($pvp) $return .= numberformat($pvp);*/
		}
		$return .= '</p>';
		$return .='</div>';
		$return .= '</div>';
		//***********************************tem_extensao_garantia
			if(isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_extensao_garantia'] == 1)
			{
				$garantia=extensao_garantia_prod($prods['prod_id'],$prods['prod_precos']['PVP']);
				if(!empty($garantia))
				{
					$return.='<div class="clear"></div><div class="blocos_escolha 902"><h3 style="font-size:16px">Extensão de Garantia</h3><div>';
					$return.='<div class="cont" style="padding-bottom:5px"><div class="prdprice_n" style="font-size:11px;text-align:left"><label class="lbl_radio" for="prod_garantia" style="width:auto;padding:0;padding-left:25px;text-align:left">Sem Garantia Extra<input type="radio" name="prod_garantia" id="prod_garantia" value="0" checked="checked" /></label></div><div class="clear"></div></div>';
					$return.='<div class="clear"></div>';

					for($a=0;$a<count($garantia);$a++)
					{
						$garantia[$a]['titulo'] = 'Garantia Extra de '.$garantia[$a]['anos'].($garantia[$a]['anos'] > 1 ? ' anos' :' ano' ).'&nbsp';
						$return.='<div class="cont"><div class="prdprice_n floatleft" style="font-size:11px;text-align:left"><label class="lbl_radio " for="prod_garantia_'.$garantia[$a]['id'].'" style="width:auto;padding:0;padding-left:25px">'.$garantia[$a]['titulo'].'&nbsp;'.($garantia[$a]['info'] != '' ? '<div class="awesome-content tooltip" title="'.htmlentities($garantia[$a]['info']).'" aria-hidden="false" ><i class="fa fa-info-circle " ></i></div>' :'').'  <input type="radio" name="prod_garantia" id="prod_garantia_'.$garantia[$a]['id'].'" value="'.$garantia[$a]['id'].'" /></label></div>';
						$return.='<div class="prdprice_n floatright " style="text-align:right;width:100%;">';
						$return.=($garantia[$a]['promo_ativo'] == 1 ? '<span style="text-decoration:line-through;font-size:11px;color:#333333">'.numberformat($garantia[$a]['valor_antigo']).'</span>&nbsp;<span style="font-size:12px">'.($garantia[$a]['valor'] > 0 ? 'Agora:' :'').'</span>&nbsp;' :'&nbsp;').''.($garantia[$a]['valor'] > 0 ? '+ '.numberformat($garantia[$a]['valor']) :'Gratuíto');
						$return.='</div><div class="clear"></div></div>';
					}
					$return .='</div>';
					$return .='</div>';
				}
			}
			//***********************************tem_servicos_extra
			if(isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_servicos_extra'] == 1)
			{
				$servicos_extra=servicos_extra_prod($prods['prod_id'],$prods['prod_precos']['PVP']);
				if(!empty($servicos_extra))
				{
					$return.='<div class="clear"></div><div class="blocos_escolha 902"><h3 style="font-size:16px">Serviços Extra</h3><div>';
					$return.='<div class="cont" style="padding-bottom:5px"><div class="prdprice_n" style="font-size:11px;text-align:left"><label class="lbl_radio" for="prod_servicos_extra" style="width:auto;padding:0;padding-left:25px;text-align:left">Sem Serviços Extra<input type="radio" name="prod_servicos_extra" id="prod_servicos_extra" value="0" checked="checked" /></label></div><div class="clear"></div></div>';
					$return.='<div class="clear"></div>';

					for($a=0;$a<count($servicos_extra);$a++)
					{
						$return.='<div class="cont"><div class="prdprice_n floatleft" style="font-size:11px;text-align:left"><label class="lbl_radio " for="prod_servicos_extra_'.$servicos_extra[$a]['id'].'" style="width:auto;padding:0;padding-left:25px">'.$servicos_extra[$a]['titulo'].'&nbsp;'.($servicos_extra[$a]['info'] != '' ? '<div class="awesome-content tooltip" title="'.htmlentities($servicos_extra[$a]['info']).'" aria-hidden="false" ><i class="fa fa-info-circle " ></i></div>' :'').'  <input type="radio" name="prod_servicos_extra" id="prod_servicos_extra_'.$servicos_extra[$a]['id'].'" value="'.$servicos_extra[$a]['id'].'" /></label></div>';
						$return.='<div class="prdprice_n floatright " style="text-align:right;width:100%;">';
						$return.=($servicos_extra[$a]['promo_ativo'] == 1 ? '<span style="text-decoration:line-through;font-size:11px;color:#333333">'.numberformat($servicos_extra[$a]['valor_antigo']).'</span>&nbsp;<span style="font-size:12px">'.($servicos_extra[$a]['valor'] > 0 ? 'Agora:' :'').'</span>&nbsp;' :'&nbsp;').''.($servicos_extra[$a]['valor'] > 0 ? '+ '.numberformat($servicos_extra[$a]['valor']) :'Gratuíto');
						$return.='</div><div class="clear"></div></div>';
					}
					$return .='</div>';
					$return .='</div>';
				}
			}
			//***********************************tem_multiportes
			if(isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_multiportes'] == 1)
			{
				$portes=multi_portes_prod($prods['prod_id'],$prods['prod_precos']['PVP']);
				if(!empty($portes))
				{
					$return.='<div class="clear"></div><div class="blocos_escolha xpto"><h3 style="font-size:16px;">Entrega</h3><div>';
					$return.='<div class="clear"></div>';
					for($a=0;$a<count($portes);$a++)
					{
						$return.='<div class="cont"><div class="prdprice_n floatleft" style="font-size:11px;text-align:left"><label class="lbl_radio " for="prod_porte_'.$portes[$a]['id'].'" style="width:auto;padding:0;padding-left:25px">'.$portes[$a]['titulo'].'&nbsp;'.($portes[$a]['info'] != '' ? '<div class="awesome-content tooltip" title="'.htmlentities($portes[$a]['info']).'" aria-hidden="false"><i class="fa fa-info-circle " ></i></div>' :'').'  <input type="radio" name="prod_porte" id="prod_porte_'.$portes[$a]['id'].'" value="'.$portes[$a]['id'].'" /></label></div>';
						$return.='<span class="termos">Li e aceito os <a href="'.$portes[$a]['termos'].'" target="_blank"><u>Termos e Condições</u></a></span><div class="clear"></div>';
						$return.='<div class="prdprice_n floatright" style="text-align:right;width:100%">';
						$return.=($portes[$a]['promo_ativo'] == 1 ? '<span style="text-decoration:line-through;font-size:11px;color:#333333">'.numberformat($portes[$a]['valor_antigo']).'</span>&nbsp;<span style="font-size:12px">'.($portes[$a]['valor'] > 0 ? 'Agora:' :'').'</span>&nbsp;' :'&nbsp;').''.($portes[$a]['valor'] > 0 ? '+ '.numberformat($portes[$a]['valor']) :'Gratuíto');
						$return.='</div><div class="clear"></div></div>';
					}
					$return .='</div></div>';
				}
			}
			//***********************************
		$return .= '<div class="product_attributes clearfix">';
	
		$stock_txt='';

		if($precos['STOCK'] >= 3) $stock_txt='<span class="disponivel" title="Disponibilidade Total" > <img src="layout/'.PROJETO.'/images/status.png" alt="Disponibilidade Total" title="Disponibilidade Total"> Em stock</span>';
		elseif($precos['STOCK'] == 2) $stock_txt='<span class="limitado" title="Disponibilidade Reduzida"> <img src="layout/'.PROJETO.'/images/status_away.png" alt="Disponibilidade Reduzida" title="Disponibilidade Reduzida"> Stock Reduzido</span>';
		elseif($precos['STOCK'] == 1) $stock_txt='<span class="porencomenda" title="Disponivel por Encomenda"> <img src="layout/'.PROJETO.'/images/status_blue.png" alt="Disponivel Brevemente" title="Disponivel Brevemente"> Por Encomenda</span>';
		else $stock_txt='<span class="esgotado" title="Disponivel Brevemente"> <img src="layout/'.PROJETO.'/images/status_busy.png" alt="Disponivel por Encomenda" title="Disponivel por Encomenda"> Disponível Brevemente</span>';

		$wish='';$res=0;
		if($dados_cliente)
		{
			$cli_id=$dados_cliente['cli_id'];
			if(isProductInWishList($prods['prod_id'], $cli_id, $_SESSION['CLIENTE_ID_TEMP'])) $res=1;
			else $res=0;
		}
		elseif($_SESSION['CLIENTE_ID_TEMP'] != '')
		{
			//if(isProductInWishList($prods['prod_id'], 0, $_SESSION['CLIENTE_ID_TEMP'])) $res=1;
			//else $res=0;
		}

		if($dados_cliente)
		{
			if($res == 1) $wish='<a class="icon-coracao produtowl'.$prods['prod_id'].'" href="#" onclick="removeProductWishList(\''.$prods['prod_id'].'\', 0);return false;"><span title="'.getLinguas('key_wishlist_rem').'">'.getLinguas('key_wishlist_rem').'<i style="margin-left:5px;" class="fas fa-heart" aria-hidden="true"></i></span></a>';
			else $wish='<a class="icon-coracao produtowl'.$prods['prod_id'].'" href="#" onclick="addProductWishList(\''.$prods['prod_id'].'\');return false;"><span title="'.getLinguas('key_wishlist_add').'">'.getLinguas('key_wishlist_add').'<i style="margin-left:5px;" class="far fa-heart" aria-hidden="true"></i></span></a>';
		}


		$return .= '</div>';


		$return .= '<div class="clear"></div><br>';

		if($prods['prod_estado'] != ''){
			$return .= '<div class="grade">';
			$return .= '<h4><strong>GRADE</strong></h4>';
			// $return .= '<br>';
			$return .= '<span style="border:1px solid;border-radius:5px;padding:5px 25px;font-weight:900;font-size:15px;">GRADE '.$prods['prod_estado'].'</span>';
			
			if($prods['prod_estado'] == 'SWAP'){
				$return .= '<br><br>';
				$return .= '<span style="font-size:12px;"><strong>  (SWAP:</strong> O telefone está selado, não foi aberto.)</span>';
			}
			elseif ($prods['prod_estado'] == 'A+') {
				$return .= '<br><br>';
				$return .= '<span style="font-size:12px;"><strong>  (A+:</strong> Como novo:O telefone encontra-se igual a novo, sem marcas ou riscos.)</span>';
			}
			elseif ($prods['prod_estado'] == 'A') {
				$return .= '<br><br>';
				$return .= '<span style="font-size:12px;"><strong>  (A:</strong>  Muito bom:O telefone pode apresentar marcas ligeiras, pouco visíveis, tais como pequenos riscos, no entanto nunca marcas muito perceptíveis.)</span>';
			}
			elseif ($prods['prod_estado'] == 'B') {
				$return .= '<br><br>';
				$return .= '<span style="font-size:12px;"><strong>  (B:</strong> Bom:Apresenta marcas visíveis, tais como riscos, amolgadelas e outras marcas.)</span>';
			}
			elseif ($prods['prod_estado'] == 'C+') {
				$return .= '<br><br>';
				$return .= '<span style="font-size:12px;"><strong>  (C+:</strong> ...)</span>';
			}
			elseif ($prods['prod_estado'] == 'C') {
				$return .= '<br><br>';
				$return .= '<span style="font-size:12px;"><strong>  (C:</strong> Satisfatório:O telefone apresenta marcas muito visíveis, tais como riscos muito acentuados e amolgadelas profundas.)</span>';
			}
			elseif ($prods['prod_estado'] == 'N') {
				$return .= '<br><br>';
				$return .= '<span style="font-size:12px;"><strong>  (N:</strong> ...)</span>';
			}
			$return .= '</div>';
			
		}

		if($_GET['dev']==1)var_dump($prods);



		$return .= '<div class="clear"></div><br>';

		//if($prods['prod_bateria'] != ''){
		if(($prods['fam_id'] == '163')&&($prods['prod_bateria'] != '' ) ){// echo '<pre>'.$prods['fam_id'].'</pre>';
		
		$bateria = str_replace('%', '', $prods['prod_bateria']);

		

			if($bateria > 0 ){
				//$bateria = '100';
				$return .= '<div class="bateria">';
					$return .= '<h4><strong>BATERIA</strong></h4>';
					// $return .= '<br>';
					$return .= '<span style="border:1px solid;border-radius:20px;padding:5px 25px;font-weight:900;font-size:15px;">BATERIA '.$prods['prod_bateria'].'</span>';
				$return .= '</div>';
            }
		}

	/*	if($prods['fam_id'] == '163'){
			$return .= '<br><div class="clear"></div><br>';
			$return .= '<a class="bt_imgs fancy_stock" href="'.$GLOBALS['site_http_ajax_url'].'setNotificacaoProdutoImgs.php?idprod='.$prods['prod_id'].'"> <img src="/layout/dotbright/images/camara.svg"> SOLICITAÇÃO DE FOTOS DO EQUIPAMENTO </a>';
		}
		*/
		$return .= '<br><div class="clear"></div><br>';

		
		if($prods['prod_localizacao_fisica'] != '')
		{
			$return .= '<div class="localizacao_fisica">';
			$return .= '<h4><strong>STOCK:</strong></h4>';
			$return .= '<span> <img src="/layout/dotbright/images/stock_on.svg"><strong>ONLINE | </strong><strong style="color:#0fa200">(Disponível) </strong></span>';
			$return .= '<br>';

			$loc_loja = $prods['prod_localizacao_fisica'];
			if($loc_loja == 'Porto' || $loc_loja == 'PORTO' || $loc_loja == 'porto')
			{
				if($precos['STOCK'] > 0)
				{
					$return .= '<span> <img src="/layout/dotbright/images/stock_on.svg"> <strong style="text-transform:uppercase;">LOJA '.$prods['prod_localizacao_fisica'].' | </strong><strong style="color:#0fa200">(Disponível) </strong></span>';
					$return .= '<br>';
					// $return .= '<span> <img src="/layout/dotbright/images/stock_encomenda.svg"> <strong style="text-transform:uppercase">LOJA de MATOSINHOS</strong></span>';
				}
				else $return .= '<span> <img src="/layout/dotbright/images/status_busy.png" style="max-width:9px;"><strong style="color:#00ffff">Disponível Brevemente</strong></span>';
			}
			/*
			elseif($loc_loja == 'Matosinhos' || $loc_loja == 'MATOSINHOS' || $loc_loja == 'matosinhos')
			{
				$return .= '<span> <img src="/layout/dotbright/images/stock_on.svg"> <strong style="text-transform:uppercase">LOJA '.$prods['prod_localizacao_fisica'].' | </strong><strong style="color:#0fa200">(Disponível) </strong></span>';
				$return .= '<br>';
				$return .= '<span> <img src="/layout/dotbright/images/stock_encomenda.svg"> <strong style="text-transform:uppercase">LOJA da MAIA</strong></span>';
			}
			*/
			$return .= '</div>';
			$return .= '<br><div class="clear"></div><br>';
		}
		else
		{
			$return .= '<div class="localizacao_fisica">';
			$return .= '<h4><strong>STOCK:</strong></h4>';


			$stock_txt='';
			if($precos['STOCK'] >= 3) $stock_txt='<div class="amp disponivel" title="Disponibilidade Total"> Stock :Disponibilidade Total</div>';
			elseif($precos['STOCK'] == 2) $stock_txt='<div class="amp limitado" title="Disponibilidade Reduzida"> Stock :Disponibilidade Reduzida</div>';
			elseif($precos['STOCK'] == 1) $stock_txt='<div class="amp porencomenda" title="Disponivel por Encomenda"> Stock :Disponivel por Encomenda</div>';
			else $stock_txt='<div class="amp esgotado" title="Disponivel Brevemente"> Stock :Disponivel Brevemente</div>';
			$return .= $stock_txt;
			/*
			if($precos['STOCK'] > 0)
			{
				$return .= '<span> <img src="/layout/dotbright/images/stock_on.svg"><strong>ONLINE | </strong><strong style="color:#0fa200;">(Disponível) </strong></span>';
				$return .= '<br>';
				$return .= '<span> <img src="/layout/dotbright/images/stock_encomenda.svg"> <strong style="text-transform:uppercase">LOJA de MATOSINHOS</strong></span>';
				$return .= '<br>';
				$return .= '<span> <img src="/layout/dotbright/images/stock_encomenda.svg"> <strong style="text-transform:uppercase">LOJA da MAIA</strong></span>';
				$return .= '</div>';
			}
			else $return .= '<span> <img src="/layout/dotbright/images/status_busy.png" style="max-width:9px;"><strong style="color:#00ffff">Disponível Brevemente </strong></span>';
			*/

			$return .= '<br><div class="clear"></div><br>';
		}

		//Semi novos e oportunidades
	    if($prods['fam_id'] == '163')
	    {
	        //$return .= '<br><div class="clear"></div><br>';
	        //$return .= '<a class="bt_imgs fancy_stock" href="'.$GLOBALS['site_http_ajax_url'].'setNotificacaoProdutoImgs.php?idprod='.$prods['prod_id'].'"> <img src="/layout/dotbright/images/camara.svg"> Clique para solicitar fotos do equipamento </a>';
	    }

		$return .= '<div class="box-cart-bottom">';


		if(($precos['STOCK'] > 0 || $PODE_ENCOMENDAR == 1) && $prods['prod_precos']['PVP'] > 0 && $prods['prod_carrinho'] == 1)
		{
			$return .= '<p id="quantity_wanted_p">
			<label>'.getLinguas('key_quantidade',$GLOBALS['site_lang']).'</label>
			<input type="text" value="1" class="text" id="quantity_wanted" name="qty" style="border:1px solid rgb(189, 194, 201);">
			<a class="btn btn-default button-minus product_quantity_down" data-field-qty="qty" href="#">
			<span>
			<i class="fa fa-minus"></i>
			</span>
			</a>
			<a class="btn btn-default button-plus product_quantity_up" data-field-qty="qty" href="">
			<span>
			<i class="fa fa-plus"></i>
			</span>
			</a>
			<span class="clearfix"></span>
			</p>';
		}

	

		$return .= '<div class="buttons_bottom_block no-print" id="add_to_cart">';
		$return .= $wish;
		$return .= '<div class="clear"></div>';
		// $return .= $stock_txt;
		$return .= '<div class="clear"></div>';

		if((($precos['STOCK'] == 0  || $precos['STOCK'] == 1) && $PODE_ENCOMENDAR == 1) && $prods['prod_precos']['PVP'] > 0 && $prods['prod_carrinho'] == 1)
		{
			$return .= '<button  class="btn btn-default ajax_add_to_cart_product_button fp_comprar" onclick="adicionarCarrinhoProdutoOpcoes('.$prods['prod_id'].',$(\'#quantity_wanted\').val());" type="button"><span>'.getLinguas('key_comprar').'</span></button>';
			// $return .= '<br> <br> <button class="fp_comprar_esgotado fancy_stock" href="'.$GLOBALS['site_http_ajax_url'].'setNotificacaoProduto.php?idprod='.$prods['prod_id'].'">ALERTA DE STOCK</button>';
		}
		elseif($precos['STOCK'] > 1 && $prods['prod_precos']['PVP'] > 0 && $prods['prod_carrinho'] == 1) 
			$return .= '<button  class="btn btn-default fp_comprar" 
			                      
			                     type="button">
                          <span>'.getLinguas('key_comprar').'</span>
                        </button>';
		else
			$return .= '<button class="fp_comprar_esgotado fancy_stock" href="'.$GLOBALS['site_http_ajax_url'].'setNotificacaoProduto.php?idprod='.$prods['prod_id'].'">ALERTA DE STOCK</button>';

		$return .= '<div class="clear"></div>
		</div>';

		$return .= '<div class="row">';
		$return .= '<div class="col-md-4">';
		$return .= '<img style="width:100%;max-width:120px;height:65px;" src="layout/dotbright/images/cadeado.svg" alt="compra segura" border="0">';
		$return .= '</div>';
		$return .= '<div class="col-md-4">';
		$return .= '<img style="width:100%;max-width:120px;height:65px;" src="layout/dotbright/images/garantia24meses2.svg" alt="3 Anos de Garantia" border="0">';
		$return .= '</div>';
		$return .= '<div class="col-md-4">';
		$return .= '<img style="width:100%;max-width:120px;height:65px;" src="layout/dotbright/images/envios_rapidos.svg" alt="envio gratis" border="0">';
		$return .= '</div>';
		$return .= '</div>';

		//$return .= '<img style="max-width:130px" border="0" src="layout/'.PROJETO.'/images/garantia1.png" alt="3 Anos de Garantia">';
		//$return .= '<img style="max-width:130px" border="0" src="layout/'.PROJETO.'/images/compra-segura2.png" alt="compra segura">';
		//$return .= '<img style="max-width:130px" border="0" src="layout/'.PROJETO.'/images/envio_gratis.png" alt="envio gratis">*<br>';
		
		$return .= '<span style="padding:30px"><p><b>*Para entregas nos Açores ou Madeira consultar a loja.</p></b></span>';
		
		
		/*$return .= '

		<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
			<div class="panel panel-default sidebar-prod">
		    	<div class="panel-heading" role="tab" id="heading-vantages">
		      		<h4 class="panel-title">
		          		<i class="fa fa-check-circle"></i> As nossas vantagens
		      		</h4>
		    	</div>
		    	<div id="collapse-vantages" class="panel-collapse" aria-labelledby="heading-vantages">
		      		<div class="panel-body" style="background-color:whitesmoke">
		      			<p>
				            <i class="fa fa-truck"></i> <b data-toggle="tooltip" title="" data-original-title="*apenas em Portugal Continental">Portes grátis para Portugal Continental em todos os artigos<p>(Não inclui Ilhas)</p></b>
				            </p><div class="hidden-md hidden-lg">*apenas em Portugal Continental</div>
				        <p></p>
				        <p>
				             <i class="fas fa-ship"></i>  <b data-toggle="tooltip" title="" data-original-title="*Enviar email para geral@on4tech.pt">Para entregas personalizadas contactar a nossa loja</b>
				            </p><div class="hidden-md hidden-lg">*Enviar email para geral@on4tech.pt</div>
				        <p></p>
				        <p>
				             <i class="fa fa-calendar-alt"></i> <b data-toggle="tooltip" title="" data-original-title="A garantia de todos os produtos comprados em nossa loja online é de 36 meses.">3 Anos de Garantia</b>
				            </p><div class="hidden-md hidden-lg">A garantia de todos os produtos comprados em nossa loja online é de 24 meses.</div>
				        <p></p>
				        <p>
				            <i class="fa fa-exchange-alt"></i>  <b data-toggle="tooltip" title="" data-original-title="Você pode devolver os artigos comprados no prazo de 14 dias. Os produtos devem estar em condições normais e na embalagem original do fábrica.">Trocas e devoluções  - <a href="p22-trocas-e-devolucoes-pt"> Ver Condições</a></b>
				            </p><div class="hidden-md hidden-lg">Você pode devolver os artigos comprados no prazo de 14 dias. Os produtos devem estar em condições normais e na embalagem original do fábrica.</div>
				        <p></p>

		      		</div>
		    	</div>
		  	</div>
		  	<div class="panel panel-default sidebar-prod">
				<div class="panel-heading" role="tab" id="heading-payment">
		      		<h4 class="panel-title">
		          		 <i class="fa fa-credit-card"></i> Opções de Pagamento
		      		</h4>
		    	</div>
		    	<div id="collapse-payment" class="panel-collapse" role="tabpanel" aria-labelledby="heading-payment">
		      		<div class="panel-body" style="background-color:whitesmoke">
				    	<p>Referência Multibanco</p>
				    	<p>Transferência Bancária</p>
		      		</div>
		    	</div>
		  	</div>
		</div>

		';*/


		$prod_txt1 = get_more_text($prods['prod_id']);
		if($prod_txt1!='')$return .= '<span style="padding-top:30px">'.$prod_txt1.'</span>';


		$return .= '<div class="clear"></div></div>';

		$tem_qtt=1;


    $return .= '</div><div class="clear"></div>';
	$return .= '</div>';


	$return .= '</div>';






	//configurado em PROD_CONF no conf/conf.php
	if($P_CONF['tem_relacionados'] == 1)
	{
		$produtosrelacionados=hasProdutosRelacionados($_GET['prod_id']);
		/*if($produtosrelacionados > 0)
		{
			$return.='<div class="relacionados_wp col-md-12"><div class="titulo_a">'.getLinguas('txt_relacionados',$GLOBALS['site_lang']).'</div>';
			$return.='<div class="clear"></div>';
			$return.=bloco_catalogo_fp().'</div>';
		}*/




		$return .= '
			<div class="c"></div><br><br><div class="c"></div>
			<div class="catalogo_topo_filtros" style="background:none;color:#000">
				<div class="destaque_img">
					<div class="titulo" style="width:100%;background-color:#fff;color:#000">RECOMENDADO <span>PARA SI</span></div>
					<div class="clear"></div>
					<div class="item">
						<div class="leftbutton" id="goToPrevSlide_promocao"></div>
						<div class="slidercontent">
							<ul id="home-promocao">';

							ob_start();

							global $DB_host,$DB_user,$DB_pass, $DB_name, $site_lang;
						    $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
						    $rs=new DataObj;

							//$sql='SELECT p.ID FROM products p INNER JOIN products_families_obj pfo on p.ID=pfo.ID_Prod WHERE pfo.ID_ORG='.$prods['fam_id'].' AND p.Aproved=1 AND p.Aproved>=1 group by pfo.ID_Prod Limit 0,10';
							$sql='SELECT pfo.ID_Prod as idprod FROM products p
							INNER JOIN products_families_obj pfo ON p.ID=pfo.ID_Prod
							INNER JOIN products_objs po ON po.obj_type_id=pfo.ID_Prod
							WHERE pfo.ID_ORG='.$prods['fam_id'].' AND pfo.ID_Prod!='.$_GET['prod_id'].' AND p.Aproved=1  GROUP BY pfo.ID_Prod ORDER BY po.obj_pvp ASC LIMIT 10 ';

                            $rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
							while(!$rs->EOF())
							{
								$prods2=get_produtc_info($rs->db_data['idprod']);

								$link=$site_http_url.\seo2(2,$GLOBALS['products_page_id'],$prods2,$GLOBALS['site_lang']);

								$prod2['id']=$prods2['prod_id'];
								$prod2['title']=$prods2['prod_nome'];
								$prod2['caminho']=$link;
								$prod2['image']=$prods2['prd_imgm_med'];
								$prod2['subtitle']=$prods2['prod_subtitulo'];
								$prod2['ref']=$prods2['prod_ref'];
								$prod2['carrinho']=$prods2['prod_carrinho'];

								$prod2['price']=$prods2['prod_precos']['PVP'];
								$prod2['price_riscado']=$prods2['prod_precos']['PRECO_ANTIGO'];
								$prod2['stock']=$prods2['prod_precos']['STOCK'];
								$prod2['promo']=$prods2['prod_precos']['PROMOCAO'];

								$prod2['destaque']=$prods2['prod_precos']['DESTAQUE'];
								$prod2['novo']=$prods2['prod_precos']['NOVO'];
								$prod2['outlet']=$prods2['prod_precos']['OUTLET'];

								$prod2['u_c']=$prods2['u_catetiq'];
								$prod2['u_i']=$prods2['u_imagetiq'];
								$prod2['u_co']=$prods2['u_coretiq'];


								show_blocos_destaque($prod2);
								$rs->db_next();
							}
							$tam=$rs->db_rows;
							$rs->db_close();


							$txt = ob_get_contents();
							$return .= $txt;

							ob_end_clean();
							$return .= '
							</ul>
						</div>
						<div class="rightbutton" id="goToNextSlide_promocao"></div>
						<div class="clear"></div>
					</div>
				</div>
			</div>';
	}
	/*


	if ($prn['bydef'] > 0)
	{
		//echo 'we are defined';

		$return .= '<div class="relacionados_wp col-md-12"><div class="titulo_a">'.getLinguas('txt_relacionados',$GLOBALS['site_lang']).'</div>';
		$return .= '<div class="clear"></div>';
		$return .= bloco_catalogo_fp_rela();
		$return .=  '</div>';
	}
	if ($prn['default'] > 0 && $prn['bydef']==0)
	{
		//echo 'we are by default';

		$return .= '<div class="relacionados_wp col-md-12"><div class="titulo_a">'.getLinguas('txt_relacionados',$GLOBALS['site_lang']).'</div>';
		$return .= '<div class="clear"></div>';
		$return .= bloco_catalogo_fp_rela();
		$return .=  '</div>';
	}*/

    return $return;	
}

function ficha_produto($prods)
{
	/* é esta a função da ficha de produto */
    $new_prods = verifica_promo($prods['prod_id'], $prods['prod_precos']);

    if(!empty($new_prods))
    {
        $prods = $new_prods;
    }
	
    if(isset($_GET['amp']) && intval($_GET['amp']) == 1) return ficha_produto_amp($prods);

    $not_produto_standard=$prods['produto_standard'];
    $c_submissao_ficheiro=$prods['c_submissao_ficheiro'];

    if($not_produto_standard==1)return ficha_produto_nstandard($prods);

    global $tem_produtos_visualizados;
    $dados_cliente=cliente_logado();

    $P_CONF=unserialize(PROD_CONF);

    if($tem_produtos_visualizados)
    {
        ?>
        <script>
            $(document).ready(function(){
                $.ajax({
                    type:"POST",
                    url:SITE_HTTP_AJAX_URL + "addRemoveProductVisualizados.php",
                    data:{action:"add", prodid:<?php echo $prods['prod_id'];?>}
                }).done(function (resultado) {});
            });
        </script>
        <?php
    }

    $return='<link rel="stylesheet" href="layout/'.PROJETO.'/css/css_external/product_v_'.filemtime('layout/'.PROJETO.'/css/css_external/product.css').'.css" media="all"/> ';
    $return='<link rel="stylesheet" href="layout/'.PROJETO.'/vendor/swiper/swiper-bundle.min.css" media="all"/> ';



    $return .= '<script>quantityAvailable=9999;</script>';
    $return .= '<script type="text/javascript" src="layout/'.PROJETO.'/css/css_external/product.js"></script> ';
    $return .= BREADCRUMBS(1, intval($prods['fam_id']));


    $tem_qtt=0;
    $label_promo='';
    $precos=$prods['prod_precos'];

    $PODE_ENCOMENDAR=$precos['PODE_ENCOMENDAR'];

    $today = date("U");

    if ((($precos['STOCK'] > 0 || $PODE_ENCOMENDAR == 1) && $prods['prod_precos']['PVP'] > 0 && $prods['prod_carrinho'] == 1))
    {
        if ($prods['prod_precos']['PROMOCAO'] == 1 && (strtotime($prods['prod_precos']['Promo_data_ini']) <= $today && strtotime($prods['prod_precos']['Promo_data_fim']) >=$today))
        {
            //$return .= '<div style="text-align:center;display:block;padding:5px;background:#f8f8f8;"><h5 style="padding:0px;margin:0px">'.$prods['global_promos'][0]['designacao'].'</h5><span style="font-size:10px;">'.substr($prods['global_promos'][0]['data_inicio'], 0, 10).' - '.substr($prods['global_promos'][0]['data_fim'], 0,10).'<b data-toggle="tooltip" title="" data-original-title="'.$prods['global_promos']['descricao'].'">&nbsp<img src="layout/'.PROJETO.'/images/alert.png" width="13"></b></span></div>';
            $label_promo='<div class="ribbon-wrapper2"><img src="layout/'.PROJETO.'/images_gerado/promo.png" alt="" /></div>';
        }
    }
    //elseif ($prods['prod_precos']['PROMOCAO'] == 2 && $prods['prod_precos']['PRECO_ANTIGO'] > 0 && $prods['prod_precos']['PRECO_ANTIGO'] > $prods['prod_precos']['PVP'])
    
    if ($prods['prod_precos']['NOVO'] == 1)
	{
		$label_promo = '<div class="ribbon-wrapper3"><div class="ribbon-promo2">NOVIDADE</div></div>';
	}
    if ($prods['prod_precos']['PROMOCAO'] == 2)
	{
		$label_promo = '<div class="ribbon-wrapper3"><div class="ribbon-promo2">BEST PRICE</div></div>';
	}

	//if($_GET['t']==1) echo  $prods['prod_precos']['OUTLETDT'];

	//if($prods['prod_precos']['OUTLET'] == 1 &&  strtotime($prods['prod_precos']['OUTLETDT']) >= strtotime(date('Y-m-d')))
	if($prods['prod_precos']['OUTLET'] == 1)
	{
		$label_promo = '<div class="ribbon-wrapper3"><div class="ribbon-promo2">'.($prods['prod_precos']['OUTLETBOX'] == '' ? 'OUTLET' : $prods['prod_precos']['OUTLETBOX']).'</div></div>';
	}

    if($not_produto_standard) $return.='<input type="hidden" id="produto_standard" name="produto_standard" value="0">';
    else $return.='<input type="hidden" id="produto_standard" name="produto_standard" value="1">';

    $fam2 = $prods['fam_id'];


    $reparacoes = 0;
    $p_way = geraCatalogoRecursivo(intval($prods['fam_id']));
    $fam = get_family_info(intval($p_way[0]));
    if (!empty($fam) && is_array($fam) && $fam['reparacoes'] == 1) $reparacoes = 1;


    if($reparacoes == 1)
    {
    	$return .= '<div class="product row"> 
    					<div class="col-xs-12 col-sm-12 col-md-12">'.ficha_p1_final($prods).'</div>
    					<div class="col-xs-12 col-sm-12 col-md-12">'.prod_mais_vendido_reparacoes($fam2).'</div>
	                </div>';
    }
    else
    {
	    $return .= '<div class="product row"> 
	                    <div class="col-xs-12 col-sm-12 col-md-12">
	                     <div id="part1" class="col-md-9" style="position:relative;z-index:2">
	                       '.ficha_p1_final($prods,$label_promo).'
	                     </div>
	                     <div id="part3" class="col-md-3">
	                    '.prod_mais_vendido($fam2).'
	                     </div>
	                </div>
	                </div>';
    }
    return $return;
}

function img_prod($prodsimg, $tam, $zoom =0 )
{
   // print_r($prodsimg);
	/*
    $return = '<div class="swiper-slide slide_1"><img src="files/products/25538_1_prod.png?dp=1685636848" alt="(SEMINOVO)  IPHONE 12 128GB BLACK" border="0"></div>
           <div class="swiper-slide slide_2"><img src="files/products/25538_2_prod.png?dp=1685636848" alt="(SEMINOVO)  IPHONE 12 128GB BLACK" border="0"></div>
           <div class="swiper-slide slide_3"><img src="files/products/25538_3_prod.png?dp=1685636848" alt="(SEMINOVO)  IPHONE 12 128GB BLACK" border="0"></div>
           <div class="swiper-slide slide_4"><img src="files/products/25538_4_prod.png?dp=1685636848" alt="(SEMINOVO)  IPHONE 12 128GB BLACK" border="0"></div>
           <div class="swiper-slide slide_5"><img src="files/products/25538_5_prod.png?dp=1685636848" alt="(SEMINOVO)  IPHONE 12 128GB BLACK" border="0"></div>';
	*/
    if (!substr_count($prodsimg, 'no_photo'))
    {
        for ($figi=0;$figi < $tam;$figi++)
        {
            if($figi==0)
            {
                $return .=  '<div class="swiper-slide slide_'.$figi.'">';
                    if($zoom == 1 ) {
                        //$return .= '<figure class="product-image_sw" id="product-image_sw'.$figi.'" style="position:relative;overflow:hidden;cursor:pointer;">';
                        $return .= ' <a href="'.$prodsimg['prd_imgs'][$figi].'" target="_blank" class="fancybox " id="big'.$figi.'" data-fancybox-group="gallery" >';
                        $return .=  ' <img class="zoom"  src="'.$prodsimg['img_med'][$figi].'" ';
                    }else{
                        //$return .=  ' <img   src="'.$prodsimg['img_med'][$figi].'" ';
                        $return .=  '<img class="zoom" src="'.$prodsimg['img_med'][$figi].'" ';

                    }
                    if($zoom == 1 ) {
                        $return .= '';
                    }
                        $return .=  '  alt="'.rtrim($prodsimg['prod_nome'], '-').'" border="0">';
                        if($zoom == 1 ) {
                                $return .= ' </a> ';
                        }
                    $return .= ' </div>';
            }
            else
            {
                $return .=  '<div class="swiper-slide slide_'.$figi.'">';
                if($zoom == 1 ) {
                    ///$return .= '<figure class="product-image_sw" id="product-image_sw'.$figi.'" style="position:relative;overflow:hidden;cursor:pointer;">';
                    $return .= ' <a href="'.$prodsimg['prd_imgs'][$figi].'" target="_blank" class="fancybox " id="big'.$figi.'" data-fancybox="gallery"  >';
                }
                $return .=  ' <img class="zoom"  src="'.$prodsimg['img_med'][$figi].'" ';
                    if($zoom == 1 ) {
                        $return .= '';
                    }
                    $return .=  '  alt="'.rtrim($prodsimg['prod_nome'], '-').'" border="0">';
                    if($zoom == 1 ) {
                        $return .= ' </a></figure>';
                    }
                    $return .= ' </div>';
                  //  $return .= '<li class="item" data-thumb="'.$prodsimage['img_med'][$figi].'">'.$label_promo.'<span class="helper"></span><a href="'.$prods['prd_imgs']['img'][$figi].'" target="_blank" class="fancybox" id="big'.$figi.'" data-fancybox-group="gallery"><img src="'.$prods['prd_imgs']['img_med'][$figi].'" alt="'.rtrim($prods['prod_nome'], '-').'" border="0" /></a></li>';
            }
        }
    }
    else
    {
        $return .= '<li class="item "  data-thumb="'.$prods['prd_imgs']['img_med'][$figi].'">'.$label_promo.'
                        <span class="helper 7"></span>
                        <img src="'.$prods['prd_imgm_med'].'" alt="'.rtrim($prods['prod_nome'], '-').'" border="0" />
                    </li>';
    }



	return $return;
}


function validateDates($initialDate, $finalDate) {
	$initialTimestamp = strtotime($initialDate);
	$finalTimestamp = strtotime($finalDate);
	
	if ($initialTimestamp === false || $finalTimestamp === false) {
		// Invalid date format
		return false;
	}
	
	if ($finalTimestamp > $initialTimestamp) {
		return true;
	} else {
		return false;
	}
}

function ficha_p1($prods)
{
    global $DB_host, $DB_user, $DB_pass, $DB_name, $site_lang, $site_http_url;
    $db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
    $obj = new Dataobj;
    $sql = 'SELECT * FROM brands_lang WHERE ID_PAI =(SELECT ID_Marca FROM products whERE id = '.$prods['prod_id'].')';
    $obj->db_open($db_link, $DB_name, $sql);
    if (!$obj->EOF()) {
        $brand_id = $obj->db_data['ID_PAI'];
        $prods_marca_lang = $obj->db_data['Nome'];
    }
    $obj->db_close();

    $url_brand = 'm-'.$brand_id.'-'.str_replace(' ', '', limpa_special($prods_marca_lang)).'-'.$site_lang;

    $precos = $prods['prod_precos'];

    $PODE_ENCOMENDAR = $precos['PODE_ENCOMENDAR'];
    $tam = count($prods['prd_imgs']['img_cart']);

    $p_way = geraCatalogoRecursivo(intval($prods['fam_id']));
    $fam = get_family_info(intval($p_way[0]));

    $reparacoes = 0;
    if (!empty($fam) && is_array($fam) && $fam['reparacoes'] == 1) $reparacoes = 1;

    //$reparacoes=1;//teste //falta fazer umas alterações no BO

    // echo 'reparacoes:'.$reparacoes;
    //echo '<pre> store_icons:'.$prods['store_icons'].'</pre>';
    // $return .= style_prod();
    $return .= '<!-- L1 a -->'.$reparacoes;
    $return .= '<script src="layout/'.PROJETO.'/vendor/zoom/jquery.zoom.js"></script> ';

    $return .= '<link rel="stylesheet" type="text/css" href="layout/'.PROJETO.'/vendor/photoswipe/photoswipe.min.css"></script> ';
    $return .= '<link rel="stylesheet" type="text/css" href="layout/'.PROJETO.'/vendor/photoswipe/default-skin/default-skin.min.css">';


    /*
    <link rel="stylesheet" type="text/css" href="assets/vendor/photoswipe/photoswipe.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendor/photoswipe/default-skin/default-skin.min.css">
    <script src="assets/vendor/photoswipe/photoswipe.js"></script>
    <script src="assets/vendor/photoswipe/photoswipe-ui-default.js"></script>
	*/

    $return .= ' ';


    $return .= ' 
				<script>
				/*
				$(document).ready(function(){
					var zoomopt  = {
						responsive:true,
						borderSize:0,
						zoomType:\'inner\',
						onZoomIn:true,
						magnify:2.1,
					};
					*/';
					/*
					for ($i=0;$i < count($prods['prd_imgs']['img_cart']);$i++) {
						$return .= '
								$("#product-image_sw'.$i.'").zoom(zoomopt);
								';
					}
					*/
    $return .= '/* });*/
	
	/*
	$(document).ready(function(){
	var container = [];

	$(\'.img_prod1\').find(\'figure\').each(function() {
		var $link = $(this).find(\'a\'),
		item = {
			src:$link.attr(\'href\'),
			w:$link.data(\'width\'),
			h:$link.data(\'height\'),
			title:$link.data(\'caption\')
		};
		container.push(item);
	});

	$(\'#img_prod\').click(function(event) { alert("oi");
		event.preventDefault();

		var $pswp = $(\'.pswp\')[0],
		options = {
			index:$(this).parent(\'figure\').index(),
		bgOpacity:0.8,
		captionEl:false,
		tapToClose:true,
		shareEl:false,
		fullscreenEl:false,
		};

		var gallery = new PhotoSwipe($pswp, PhotoSwipeUI_Default, container, options);
		gallery.init();
	});
	});
	// }(jQuery));
	*/
	</script>    ';
    $return .= '<div class="product product-single row">
             		<div class="col-xs-12 col-sm-12 col-md-6">';
			$tam = count($prods['prd_imgs']['img_cart']);
			$return .= "<div class='product-gallery-block resp2'>";
				$return .= "<ul class='product-gallery'>";
								for ($figi = 0;$figi < $tam;$figi++) {
									if ($figi == 0) {
										$return .= '<li class="item active" data-thumb="'.$prods['prd_imgs']['img_med'][$figi].'">';
										$return .= $label_promo;
										if ($tam > 1) $return .= '<a href="'.$prods['prd_imgs']['img'][$figi].'" target="_blank" class="fancybox " id="big'.$figi.'" data-fancybox-group="gallery"><img class="zoom" src="'.$prods['prd_imgs']['img_med'][$figi].'" alt="'.rtrim($prods['prod_nome'], '-').'" border="0" /></a>';
										else $return .= '<a href="'.$prods['prd_imgs']['img'][$figi].'" target="_blank" class="fancybox" id="big'.$figi.'" data-fancybox="gallery"><img class="zoom" src="'.$prods['prd_imgs']['img_med'][$figi].'" alt="'.rtrim($prods['prod_nome'], '-').'" border="0" /></a>';
										$return .= '</li>';
									} else {
										$return .= '<li class="item" data-thumb="'.$prods['prd_imgs']['img_med'][$figi].'">'.$label_promo.'<a href="'.$prods['prd_imgs']['img'][$figi].'" target="_blank" class="fancybox" id="big'.$figi.'" data-fancybox-group="gallery"><img src="'.$prods['prd_imgs']['img_med'][$figi].'" alt="'.rtrim($prods['prod_nome'], '-').'" border="0" /></a></li>';
									}
								}
				$return .= '</ul>';
			$return .= '</div>';

    /*
         <div class="product-gallery1 product-gallery-sticky1">
             <!-- Swiper -->
             <div class="swiper-container">
                   <div class="swiper mySwiper2">
                      <div class="swiper-wrapper img_prod1">'.img_prod($prods['prd_imgs'], $tam,1).'
                      </div>
                  <div class="swiper-button-next" style="display:none;" ></div>
                       <div class="swiper-button-prev" style="display:none;" ></div>
                        <!--<a href="#" class="product-gallery-btn product-image-full"><i class="w-icon-zoom"></i></a>-->
                   </div>
                     <div thumbsSlider="" class="swiper mySwiper">
                          <div class="product-thumbs swiper-wrapper">'.img_prod($prods['prd_imgs'], $tam,0).'</div>
                      </div>
             </div>
        </div>';*/


    // $return .= ' <script src="layout/'.PROJETO.'/vendor/photoswipe/photoswipe.js"></script> ';
    //$return .= ' <script src="layout/'.PROJETO.'/vendor/photoswipe/photoswipe-ui-default.js"></script> ';

    // echo '<pre> store_icons:'.print_r($prods).'</pre>';
    if ($prods['store_icons'] != 'undefined' && $prods['store_icons'] != '') {
        $grau_t = $prods['store_icons'];
        $grau = explode(';', $grau_t);
        $top = 5;
        $grau_c = count($grau) - 1;
        $grau_c = ($grau_c > 4) ? 4 :$grau_c;
        for ($a = 0;$a < $grau_c;$a++) {


            $gg = explode('-', $grau[$a]);
            $return .= '<div class="prod_cat_icons" style="top:'.$top.'px;" ><img title="'.nome_icons($gg[0]).'" src="../../files/products_components/'.$gg[0].'_2.svg" height="48"></div>';
            $top += 45;
        }
    }

    if ($prods['fam_id'] == '163') {
        $return .= '<div class="garantia_icons" >
                    	<img style="width:100%;max-width:310px;height:70px;" src="layout/dotbright/images/garantia24meses2.svg" alt="3 Anos de Garantia" border="0"> 
                    </div>';
    }
    

    $return .= ' <!--<div class="prod_cat_icons" style="top:5px;" title=""><img src="../../files/products_components/11_2.svg" height="48"></div> 
              <div class="prod_cat_icons" style="top:50px;" title=""><img src="../../files/products_components/3_2.svg" height="48"></div>-->
                  
             </div>
             <div class="product-detail col-md-6">
                <div class="product-info">
                  <div class="product-info-header">
                	    <h1 itemprop="name" class="product-title">'.rtrim($prods['prod_nome'], '-').'</h1>
                	    <br>
                	    <a href="'.$url_brand.'" title="'.$prods_marca_lang.'">
                	    <span style="border:1px solid;border-radius:5px;padding:5px 25px;font-weight:900;font-size:15px">'.$prods_marca_lang.'</span>
                	    </a>
                  </div>
                 
                  
                 ';

                 if($_GET['dev']==1)var_dump($prods);

				 if($reparacoes == 0)
				 {
                    $return .='<div class="product-stock"> ';

					if(($prods['fam_id'] == '163') && ($prods['prod_bateria'] > 0))
					{
						$return .= '<div> '.bateria($prods['prod_bateria'],$prods['prod_estado']).'</div>';
					}

					if($prods['prod_localizacao_fisica'] != '')
					{
						$return .= '<div class="localizacao_fisica">';
						$return .= '<h4><strong>STOCK:</strong></h4>';


						$stock_txt='';
						if($precos['STOCK'] >= 3) $stock_txt='<div class="amp disponivel" title="Disponibilidade Total"> Stock :Disponibilidade Total</div>';
						elseif($precos['STOCK'] == 2) $stock_txt='<div class="amp limitado" title="Disponibilidade Reduzida"> Stock :Disponibilidade Reduzida</div>';
						elseif($precos['STOCK'] == 1) $stock_txt='<div class="amp porencomenda" title="Disponivel por Encomenda"> Stock :Disponivel por Encomenda</div>';
						else $stock_txt='<div class="amp esgotado" title="Disponivel Brevemente"> Stock :Disponivel Brevemente</div>';
						$return .= $stock_txt;


						/*
						$return .= '<span> <img src="/layout/dotbright/images/stock_on.svg"> ONLINE | <strong style="color:#0fa200;">(Disponível) </strong></span>';
						$return .= '<br>';

						$loc_loja = $prods['prod_localizacao_fisica'];
						if($loc_loja == 'Porto' || $loc_loja == 'PORTO' || $loc_loja == 'porto')
						{
							$return .= '<span> <img src="/layout/dotbright/images/stock_on.svg"> 
                                                <span style="text-transform:uppercase;">LOJA '.$prods['prod_localizacao_fisica'].'  </span>
                                                <span style="color:#0fa200;">(Disponível) </span>
                                         </span>';
							$return .= '<br>';
							$return .= '<span> <img src="/layout/dotbright/images/stock_encomenda.svg"> LOJA de MATOSINHOS</span>';
						}
						elseif($loc_loja == 'Matosinhos' || $loc_loja == 'MATOSINHOS' || $loc_loja == 'matosinhos')
						{
							$return .= '<span> <img src="/layout/dotbright/images/stock_on.svg"> <strong style="text-transform:uppercase;">LOJA '.$prods['prod_localizacao_fisica'].'  </strong><strong style="color:#0fa200;">(Disponível) </strong></span>';
							$return .= '<br>';
							$return .= '<span> <img src="/layout/dotbright/images/stock_encomenda.svg"> <strong style="text-transform:uppercase">LOJA da MAIA</strong></span>';
						}
						*/

                        /*if($prods['fam_id'] == '163')
                        {
                            $return .= '<div>
											<button class="btn btn-lg btn-primary1 btn-outline btn-icon-left fancy_stock" href="'.$GLOBALS['site_http_ajax_url'].'setNotificacaoProdutoImgs.php?idprod='.$prods['prod_id'].'"> 
												<i class="fa fa-camera"></i>
												Clique para solicitar fotos do equipamento 
											</button>
										</div>';
                        }*/

						$return .= '</div>';						
						/*aqui bateria e grade */
					}
					else
					{
					  $return .= '<div class="localizacao_fisica">';
					  $return .= '<h4><strong>STOCK:</strong></h4>';

					  $stock_txt='';
						if($precos['STOCK'] >= 3) $stock_txt='<div class="amp disponivel" title="Disponibilidade Total"> Stock :Disponibilidade Total</div>';
						elseif($precos['STOCK'] == 2) $stock_txt='<div class="amp limitado" title="Disponibilidade Reduzida"> Stock :Disponibilidade Reduzida</div>';
						elseif($precos['STOCK'] == 1) $stock_txt='<div class="amp porencomenda" title="Disponivel por Encomenda"> Stock :Disponivel por Encomenda</div>';
						else $stock_txt='<div class="amp esgotado" title="Disponivel Brevemente"> Stock :Disponivel Brevemente</div>';
						$return .= $stock_txt;

						/*
						$return .= '<span> <img src="/layout/dotbright/images/stock_on.svg">ONLINE <strong style="color:#0fa200">(Disponível) </strong></span>';
						$return .= '<br>';
						$return .= '<span> <img src="/layout/dotbright/images/stock_encomenda.svg">LOJA de MATOSINHOS</span>';
						$return .= '<br>';
						$return .= '<span> <img src="/layout/dotbright/images/stock_encomenda.svg">LOJA da MAIA</span>';
						$return .= '</div>';
						$return .= '<br><div class="clear"></div><br>';
						*/

					  	//Semi novos e oportunidades
					    /*if($prods['fam_id'] == '163')
                        {
                            $return .= '<div>
											<button class="btn btn-lg btn-primary1 btn-outline btn-icon-left fancy_stock" href="'.$GLOBALS['site_http_ajax_url'].'setNotificacaoProdutoImgs.php?idprod='.$prods['prod_id'].'"> 
												<i class="fa fa-camera"></i>												
												Clique para solicitar fotos do equipamento 
											</button>
										</div>';							
                        }*/
				 	}
					$return .=' </div>';					
				 }

    // if($prods['prod_bateria'] != ''){

    $txt_iva = "";

    // $bateria = str_replace('%', '', $prods['prod_bateria']);

    // if( ($bateria < 0 ) && ($prods['prod_id_marca'] == 5 ) ) {
    //     $bat = '100%';
    // }else{
    //     $bat =  $bateria ;//$prods['prod_bateria'];
    // }

    // if(($prods['fam_id'] == '163')&&( $bateria < 0 )){
    //     $txt_iva = "";
	// 	$return .= '<div>Bateria:'.$bat.'</div>';
    //     $return .=  bateria($bat, $prods['prod_estado']);
	//     $txt_cont = ' row';
    //     $txt1_a = '';//'<div class="col-md-4 ">';
    //     $txt1_f = '';//'</div>';
    //     $txt12_a = '';//'<div class="col-md-4 ">';
    //     $txt12_f = '';//'</div>';

    // }else{
    //     $txt_cont = '';
    //     if(($prods['fam_id'] != '163')) {
    //         $txt_iva = "(iva inlcuido)";
    //     }else{
    //         $txt_iva = "";
    //         $prod_ref =  strtoupper(substr($prods['prod_ref'], -3));
    //         if($prod_ref == 'RFB'){
    //             $txt_iva = "(iva inlcuido)";
    //         }   
	// 		if($prod_ref == 'IVA'){
	// 			$txt_iva = "(iva inlcuido)";
	// 		}
    //     }
    // }

	$return .= '<div class="product-purchase-container"> ';

		// if ($prods['prod_precos']['PROMOCAO'] == 1 && validateDates($prods['prod_precos']['Promo_data_ini'], $prods['prod_precos']['Promo_data_fim'])) {
		// 	// echo "Final date is greater than initial date.";
		// 	$return .= '<div class="tag tag-promocao" style="font-size:20px;line-height:28px">';
		// 	$return .= '<span style="text-decoration:line-through;color:#656565">'.numberformat($prods['prod_precos']['PRECO_ANTIGO'],2).'</span><br>';
		// 	$return .= '</div>';
		// } else {
		// 	echo "Final date is not greater than initial date.";
		// }

		$txt_campanhas = '';
		if ($prods['prod_precos']['PROMOCAO'] == 1 && (strtotime($prods['prod_precos']['Promo_data_ini']) <= $today && strtotime($prods['prod_precos']['Promo_data_fim']) >=$today))
			$txt_campanhas = $prods['prod_precos']['Promo_descricao'];
	
	$return .= '<p class="our_price_display" itemtype="http://schema.org/Offer" itemscope="" itemprop="offers">';
		if ($prods['prod_precos']['PROMOCAO'] == 1 && strtotime($prods['prod_precos']['Promo_data_fim']) >= strtotime($prods['prod_precos']['Promo_data_ini']) && $prods['prod_precos']['Promo_data_fim']!='0000-00-00' && $prods['prod_precos']['Promo_data_ini']!='0000-00-00'){
			$return .= '<div class="tag tag-promocao" style="font-size:30px;line-height:32px;font-weight:600;font-family:"Poppins", sans-serif;">';
				$return .= '<span style="font-size:16px;color:#ec870e">PVP:</span> <span style="color:#656565;text-decoration:line-through;font-size:18px;">'.numberformat($prods['prod_precos']['PVP'], 1).'</span> <span style="font-size:27px;color:#000;font-weight:800">'.numberformat($prods['prod_precos']['obj_promo'], 1).'</span><br>';
			$return .= '</div>';
		}
		else{
			if($prods['prod_precos']['PVP'] > 0){
				$pvp=$prods['prod_precos']['PVP'];
		
				if($prods['prod_precos']['IVA'] > 0){
					$pvp_com_iva=$prods['prod_precos']['PVP'];
					$pvp_sem_iva=($prods['prod_precos']['PVP']/(1+$prods['prod_precos']['IVA']/100));
				}
				else{
					$pvp_sem_iva=$prods['prod_precos']['PVP'];
					$pvp_com_iva=($prods['prod_precos']['PVP']*(1+$prods['prod_precos']['IVA']/100));
				}
		
				$return .= '<span style="font-size:16px;color:#ec870e">PVP:</span> 
							<span style="font-size:27px;color:#000;font-weight:800">'.numberformat($pvp_com_iva, 1).'</span>
							<span style="font-size :10px">'.$txt_iva.'</span><br>';
			}
		}
	$return .= '</p>';

	$return .='<div class="product-form container">';
	$return .= '<div class="row" style="width:100%;max-width:375px;margin:0;">';

	
	if(($precos['STOCK'] > 0 || $PODE_ENCOMENDAR == 1) && $prods['prod_precos']['PVP'] > 0 && $prods['prod_carrinho'] == 1)
	{
		$return .= '<div class="col-md-5 col-xs-6" style="padding:0;">';
			$return .= '<div class="product-qty-form" id="quantity_wanted_p">';
				$return .= '<div class="input-group">';
					$return .= '<input type="number" value="1" class="text qtde_a" id="quantity_wanted" name="qty" style="border:1px solid rgb(189,194,201);">';
					$return .= '<button type="button" data-field-qty="qty" class="quantity-plus w-icon-plus product_quantity_up"> </button>';
					$return .= '<button type="button" data-field-qty="qty" class="quantity-minus w-icon-minus product_quantity_down"> </button>';
					$return .= '<span class="clearfix"></span>';
				$return .= '</div>';
			$return .= '</div>';
		$return .='</div>';

		$return .= '<div class="col-md-7 col-xs-6" style="padding:0">';
		if($reparacoes)
		{
			$return .= '<button style="text-align:center;padding-right:0.1em;padding-left:0.1em;font-size:18px !important" class="exclusive btn btn-cart btn-default btn-cart  fp_comprar" onclick="adicionarCarrinhoProdutoOpcoes('.$prods['prod_id'].',$(\'#quantity_wanted\').val());" type="button">'.getLinguas('key_adicionar_reparacoes').'</button>';
		}
		else
		{
			if((($precos['STOCK'] == 0  || $precos['STOCK'] == 1) && $PODE_ENCOMENDAR == 1) && $prods['prod_precos']['PVP'] > 0 && $prods['prod_carrinho'] == 1)
			{
				$return .= '<button class="btn btn-cart btn-default " onclick="adicionarCarrinhoProdutoOpcoes('.$prods['prod_id'].',$(\'.qtde_a\').val());" type="button"><span>'.getLinguas('key_comprar').'</span></button>';
			}
			elseif($precos['STOCK'] > 1 && $prods['prod_precos']['PVP'] > 0 && $prods['prod_carrinho'] == 1)
			{
				$return .= '<button  class="btn btn-cart btn-default btn-cart" onclick="adicionarCarrinhoProdutoOpcoes('.$prods['prod_id'].',$(\'.qtde_a\').val());" type="button"><span>'.getLinguas('key_comprar').'</span></button>';
			}					
			else
			{
				$return .= '<button class="fp_comprar_esgotado fancy_stock" href="'.$GLOBALS['site_http_ajax_url'].'setNotificacaoProduto.php?idprod='.$prods['prod_id'].'"><span>ALERTA DE STOCK</span></button>';
			}
		}
		$return .='</div>';
	}
	else
	{
		$return .= '<div class="col-md-7 col-xs-6" style="padding:0">';
		if($reparacoes)
		{
			$return .= '<button style="text-align:center;padding-right:0.1em ;padding-left:0.1em;font-size:18px !important;" class="exclusive btn btn-cart btn-default btn-cart  fp_comprar" onclick="adicionarCarrinhoProdutoOpcoes('.$prods['prod_id'].',$(\'#quantity_wanted\').val());" type="button">'.getLinguas('key_adicionar_reparacoes').'</button>';
		}
		else
		{
			if((($precos['STOCK'] == 0  || $precos['STOCK'] == 1) && $PODE_ENCOMENDAR == 1) && $prods['prod_precos']['PVP'] > 0 && $prods['prod_carrinho'] == 1)
			{
				$return .= '<button  class="btn btn-cart btn-default " onclick="adicionarCarrinhoProdutoOpcoes('.$prods['prod_id'].',$(\'.qtde_a\').val())" type="button"><span>'.getLinguas('key_comprar').'</span></button>';
			}
			elseif($precos['STOCK'] > 1 && $prods['prod_precos']['PVP'] > 0 && $prods['prod_carrinho'] == 1)
			{
				$return .= '<button  class="btn btn-cart btn-default btn-cart" onclick="adicionarCarrinhoProdutoOpcoes('.$prods['prod_id'].',$(\'.qtde_a\').val());" type="button"><span>'.getLinguas('key_comprar').'</span></button>';
			}
			else
			{
				$return .= '<button class="fp_comprar_esgotado fancy_stock" href="'.$GLOBALS['site_http_ajax_url'].'setNotificacaoProduto.php?idprod='.$prods['prod_id'].'"><span>ALERTA DE STOCK</span></button>';
			}
		}
		$return .='</div>';
	}

    $p_way = geraCatalogoRecursivo(intval($prods['fam_id']));
    $fam = get_family_info(intval($p_way[0]));
    $reparacoes = 0;
    if(!empty($fam) && is_array($fam) && $fam['reparacoes'] == 1) $reparacoes = 1;
    $return .='</div>';
    if($reparacoes == 0)
    {
        $return .= '<div class="widget widget-icon-box mb-6 row" style="background:#FFF;position:relative;z-index:2;justify-content:center;width:100%;max-width:375px">
                     <div class="col-md-12">
                        <div class="col-xs-4 col-md-4 icon-box icon-box-side" style="display:grid;justify-content:center;">
                           <img style="width:100%;max-width:310px;height:70px;" src="layout/dotbright/images/cadeado.svg" alt="compra segura" border="0">
                        </div>
                        <div class="col-xs-4 col-md-4 icon-box icon-box-side" style="display:grid;justify-content:center;">
                           <img style="width:100%;max-width:310px;height:70px;" src="layout/dotbright/images/garantia24meses2.svg" alt="3 Anos de Garantia" border="0">
                        </div>
                        <div class="col-xs-4 col-md-4 icon-box icon-box-side" style="display:grid;justify-content:center;">
                            <img style="width:100%;max-width:310px;height:70px;" src="layout/dotbright/images/envios_rapidos.svg" alt="envio gratis" border="0">
                        </div>
                     </div>
                  </div>';
    }
    else
    {
        $return .= ' 
 
            <div class="widget widget-icon-box mb-6 row" style="background:#FFF;position:relative;z-index:2;width:100%;max-width:375px">
             <div class="col-md-12">
                <div class="col-xs-4 col-md-4 icon-box icon-box-side">
                   <img style="width:100%;max-width:310px;height:65px;" src="layout/dotbright/images/tecnicos.svg" alt="compra segura" border="0">
		        </div>
                <div class="col-xs-4 col-md-4 icon-box icon-box-side">
                   <img style="width:100%;max-width:310px;height:65px;" src="layout/dotbright/images/lab_certificados.svg" alt="3 Anos de Garantia" border="0">
                </div>
                <div class="col-xs-4 col-md-4 icon-box icon-box-side">
                    <img style="width:100%;max-width:310px;height:65px;" src="layout/dotbright/images/pecas_originais.svg" alt="envio gratis" border="0">
                </div>
              </div>  
            </div> ';
    }

 /*   $return .= '<div class="col-xs-4 col-md-4 icon-box icon-box-side" style="display:grid;justify-content:center;">
                                   <img style="width:100%;max-width:310px;height:70px;" src="layout/dotbright/images/cadeado.svg" alt="compra segura" border="0">
                                </div>
                                <div class="col-xs-4 col-md-4 icon-box icon-box-side" style="display:grid;justify-content:center;">
                                   <img style="width:100%;max-width:310px;height:70px;" src="layout/dotbright/images/garantia24meses2.svg" alt="3 Anos de Garantia" border="0">
                                </div>
                                <div class="col-xs-4 col-md-4 icon-box icon-box-side" style="display:grid;justify-content:center;">
                                    <img style="width:100%;max-width:310px;height:70px;" src="layout/dotbright/images/envios_rapidos.svg" alt="envio gratis" border="0">
                                </div>';
	*/

	//$return .='</div>';
    $return .= '<div class="clear"></div>';
        $return .='</div>';
    $wish='';
    $res=0;
        $return .=' </div> <!-- product-purchase-container 1-->';
        $return .=' 
                </div> <!-- product-info-->
            </div> <!-- product-detail--> 
      </div> <!-- row--> 
	  <div class="clear"></div> 
	  <br>
      <div class="row">
        <div class="col-md-12">';
                $descricao_verifica = strip_html_tags($prods['prod_descricao']);
                $detalhe_verifica = strip_html_tags($prods['prod_como_funciona']);
                $ficha_verifica = strip_html_tags($prods['prod_envio']);
                $prod_video = $prods['prod_video'];
                $customizado_titulo= strip_html_tags($prods['prod_customizado_titulo']);
                $customizado= strip_html_tags($prods['prod_customizado']);
                $script=str_replace(array('http://youtu.be/', 'https://youtu.be/', 'http://www.youtube.com/watch?v=', 'https://www.youtube.com/watch?v='), '//www.youtube.com/embed/',$prod_video);

                //$produtosrelacionados=hasProdutosRelacionados($_GET['prod_id']);
                //$prn=hasProdutosRelacionados_novo($_GET['prod_id']);

                if($descricao_verifica !='' || $detalhe_verifica != '' || $ficha_verifica != '' || $prod_video != '' || $customizado != '')
                { //echo('aqquui'.strlen($descricao_verifica).'<br>'.$descricao_verifica );
                    $return .= '<div class="horizontalTab">
                            <ul class="resp-tabs-list">
                            '.($descricao_verifica!='' ? '<li>'.getLinguas('txt_descricao',$GLOBALS['site_lang']).'</li>' :'').'
                            '.($detalhe_verifica!='' ? '<li style="text-transform:uppercase;">'.getLinguas('key_detalhes',$GLOBALS['site_lang']).'</li>' :'').'
                            '.($ficha_verifica!='' ? '<li>'.getLinguas('key_ficha_tecnica',$GLOBALS['site_lang']).'</li>' :'').'
                            '.($prods['prod_ee'] != '' || $prods['prod_ue'] != '' || $prods['prod_dt'] != '' ? '<li>'.getLinguas('key_outros_fxs',$GLOBALS['site_lang']).'</li>' :'').'
                            '.($prod_video != '' ? '<li>'.getLinguas('key_video',$GLOBALS['site_lang']).'</li>' :'').'
                            '.($customizado != '' ? '<li>'.$customizado_titulo.'</li>' :'').'</ul><div class="resp-tabs-container">
                            '.($descricao_verifica!='' ? '<div><p>'.text_smart($prods['prod_descricao']).'</p></div>' :'').'
                            '.($detalhe_verifica!='' ? '<div><p>'.text_smart($prods['prod_como_funciona']).'</p></div>' :'').'
                            '.($ficha_verifica!='' ? '<div><p>'.$prods['prod_envio'].'</p></div>' :'').'
                            '.($prods['prod_ee'] != '' || $prods['prod_ue'] != ''  || $prods['prod_dt'] != '' ? '<div><p>' :'').'
                            '.($prods['prod_ee'] != '' ? '<a style="color:#666;text-decoration:none;text-transform:uppercase;font-weight:bold" href="'.$prods['prod_ee'].'" target="_blank">&#9658;&nbsp;&nbsp;'.getLinguas('key_etiqueta_energ',$GLOBALS['site_lang']).'</a><br>' :'').'
                            '.($prods['prod_ue'] != '' ? '<a style="color:#666;text-decoration:none;text-transform:uppercase;font-weight:bold" href="'.$prods['prod_ue'].'" target="_blank">&#9658;&nbsp;&nbsp;'.getLinguas('key_fx_produto_ue',$GLOBALS['site_lang']).'</a><br>' :'').'
                            '.($prods['prod_dt'] != '' ? '<a style="color:#666;text-decoration:none;text-transform:uppercase;font-weight:bold" href="'.$prods['prod_dt'].'" target="_blank">&#9658;&nbsp;&nbsp;'.getLinguas('key_desenho_tecnico',$GLOBALS['site_lang']).'</a>' :'').'
                            '.($prods['prod_ee'] != '' || $prods['prod_ue'] != '' || $prods['prod_dt'] != '' ? '</p></div>' :'').'
                            '.($prod_video!='' ? '<div><p><iframe width="100%" height="450" src="'.$script.'" frameborder="0" allowfullscreen></iframe></p></div>' :'').'
                            '.($customizado!='' ? '<div><p>'.nl2br(strip_html_tags_fp($customizado)).'</p></div>' :'').'
                    
                            </div>
                            </div>';
                }
    $return .='    </div>
                </div> ';

    $return.= '<script src="layout/'.PROJETO.'/vendor/swiper/swiper-bundle.min.js"></script> ';

    $return .= ' ';

    $return .= '   <script>
   /*
   if ($ && $.fn.zoom) {
    $("#ex1").zoom();
    $("#ex2").zoom({ on:"grab" });
  }
    */
	if ($ && $.fn.zoom) {
	$("#product-image_sw").zoom();
	}
                         
                      let swiper = new Swiper(".mySwiper", {
                         spaceBetween:10,
                         slidesPerView:4,
                         freeMode:true,
                         watchSlidesProgress:true,
                         
                        });
                        
                        let swiper2 = new Swiper(".mySwiper2", {
                         spaceBetween:10,
                         navigation:{
                           nextEl:".swiper-button-next",
                           prevEl:".swiper-button-prev",
                         },
                         thumbs:{
                           swiper:swiper,
                         },
                        });
                     
                        var swiperSlide = document.getElementsByClassName(\'swiper-slide\')
                            for(var index = 0;index< swiperSlide.length;index++){
                            swiperSlide[index].addEventListener(\'mouseover\',function(e){
                                swiper2.zoom.in();
                            })
                            
                            swiperSlide[index].addEventListener(\'mouseout\',function(e){
                                swiper2.zoom.out();
                            })
                            }
                    
                    </script>';

    return $return;
}

function ficha_p1_final($prods,$labels='')
{
    global $DB_host, $DB_user, $DB_pass, $DB_name, $site_lang, $site_http_url;
    $db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
    $obj = new Dataobj;
    $sql = 'SELECT * FROM brands_lang WHERE ID_PAI =(SELECT ID_Marca FROM products whERE id = '.$prods['prod_id'].')';
    $obj->db_open($db_link, $DB_name, $sql);
    if (!$obj->EOF())
    {
        $brand_id = $obj->db_data['ID_PAI'];
        $prods_marca_lang = $obj->db_data['Nome'];
    }
    $obj->db_close();

    $dados_cliente = cliente_logado();

    $url_brand = 'm-'.$brand_id.'-'.str_replace(' ', '', limpa_special($prods_marca_lang)).'-'.$site_lang;

    $precos = $prods['prod_precos'];

    $PODE_ENCOMENDAR = $precos['PODE_ENCOMENDAR'];
    $tam = count($prods['prd_imgs']['img_cart']);

    $p_way = geraCatalogoRecursivo(intval($prods['fam_id']));
    $fam = get_family_info(intval($p_way[0]));

    $reparacoes = 0;
    if (!empty($fam) && is_array($fam) && $fam['reparacoes'] == 1) $reparacoes = 1;
    if($reparacoes == 1) {
        require_once __DIR__.'/../../../core/repair_request.php';
        $repair_quote = dotbright_repair_quote($prods);
    }

	$wish='';$res=0;
	if($dados_cliente)
	{
		$cli_id=$dados_cliente['cli_id'];
		if(isProductInWishList($prods['prod_id'], $cli_id, $_SESSION['CLIENTE_ID_TEMP'])) $res=1;
		else $res=0;
	}
	elseif($_SESSION['CLIENTE_ID_TEMP'] != '')
	{
		//if(isProductInWishList($prods['prod_id'], 0, $_SESSION['CLIENTE_ID_TEMP'])) $res=1;
		//else $res=0;
	}

	// if($res == 1) $wish='<a class="icon-coracao produtowl'.$prods['prod_id'].'" href="#" onclick="removeProductWishList(\''.$prods['prod_id'].'\', 0);return false;"><span title="'.getLinguas('key_wishlist_rem').'"><i style="margin-left:5px;" class="fas fa-heart" aria-hidden="true"></i></span></a>';
	// else $wish='<a class="icon-coracao produtowl'.$prods['prod_id'].'" href="#" onclick="addProductWishList(\''.$prods['prod_id'].'\');return false;"><span title="'.getLinguas('key_wishlist_add').'"><i style="margin-left:5px;" class="far fa-heart" aria-hidden="true"></i></span></a>';



	if($dados_cliente)
	{
		if($res == 1)$wish = '<div class="wish" style="position:absolute;top:0%;right:5%;border:1px solid;border-radius:50%;display:block;width:40px;height:40px;text-align:center">
							<span style="font-size:20px;padding:10px;border:1px solid;border-radius:50%;width:40px;height:40px;display:block;margin:0 auto;">
								<a class="icon-coracao produtowl'.$prods['prod_id'].'" href="#" onclick="removeProductWishList(\''.$prods['prod_id'].'\', 0);return false" style="font-size:20px">
									<i class="fas fa-heart" aria-hidden="true"></i>
								</a>
							</span>
							<span>Wishlists</span>
						</div>';
		else $wish = '<div class="wish" style="position:absolute;top:0%;right:5%;text-align:center">
					<span style="font-size:20px;padding:10px;border:1px solid;border-radius:50%;width:40px;height:40px;display:block;margin:0 auto">
						<a class="icon-coracao produtowl'.$prods['prod_id'].'" href="#" onclick="addProductWishList(\''.$prods['prod_id'].'\', 0);return false" style="font-size:20px">
							<i class="far fa-heart" aria-hidden="true"></i>
						</a>
					</span>
					<span>Wishlists</span>
				</div>';
	}

	$conteudo_dentro_img = '';

	if ($prods['store_icons'] != 'undefined' && $prods['store_icons'] != '')
	{
		$grau_t = $prods['store_icons'];
		$grau = explode(';', $grau_t);
		$top = 5;
		$grau_c = count($grau) - 1;
		$grau_c = ($grau_c > 4) ? 4 :$grau_c;
		for ($a = 0;$a < $grau_c;$a++) {
			$gg = explode('-', $grau[$a]);
			$conteudo_dentro_img .= '<div class="prod_cat_icons" style="top:'.$top.'px;" ><img title="'.nome_icons($gg[0]).'" src="../../files/products_components/'.$gg[0].'_2.svg" height="48"></div>';
			$top += 45;
		}
	}

	if($prods['fam_id'] == '163')
	{
		$conteudo_dentro_img .= '<div class="garantia">
									<img src="/layout/dotbright/images/2anos_garantia.png" style="width:100%;max-width:100px">
								</div>';
	}

	//$conteudo_dentro_img .= $wish;

	$share = '<div class="share" style="position:absolute;top:65px;right:5%;text-align:center;width:auto;margin:0;cursor:pointer">
										<span style="font-size:20px;padding:10px;border:1px solid;border-radius:50%;width:40px;height:40px;display:block;margin:0 auto">
											<a class="icon-share produtowl'.$prods['prod_id'].'" style="font-size:20px;" data-toggle="modal" data-target="#exampleModal">
												<i class="fa fa-share-alt" aria-hidden="true"></i>
											</a>
										</span>
										<span>Partilhar</span>
									</div>';
	
    $return .= '<!-- L1 b--> ';
    $return .= '<script src="layout/'.PROJETO.'/vendor/zoom/jquery.zoom.js"></script> ';
    $return .= '<link rel="stylesheet" type="text/css" href="layout/'.PROJETO.'/vendor/photoswipe/photoswipe.min.css"></script> ';
    $return .= '<link rel="stylesheet" type="text/css" href="layout/'.PROJETO.'/vendor/photoswipe/default-skin/default-skin.min.css">';
    
    $return .= '<div class="product product-single row'.($reparacoes == 1 ? ' db-repair-panels' : '').'" style="margin-top:50px">';
    

		if($reparacoes == 1) $repair_info_start = strlen($return);
		$return .= ($reparacoes == 1 ? '<div id="reparacao-info" class="col-xs-12 reparacao-painel"><h2 class="reparacao-etapa-titulo" tabindex="-1">Etapa 1 de 4 — Informação da reparação</h2>' : '<div class="col-xs-12 col-sm-12 col-md-6">');
		$return .= $labels;


			$tam = count($prods['prd_imgs']['img_cart']);

			// echo '<pre>';
			// print_r($prods['prd_imgs']['img_cart']);
			// echo '</pre>';

			$return .= "<div class='product-gallery-block resp2'>";
				$return .= "<ul class='product-gallery'>";

								$fundos = ["/layout/dotbright/images/fundo_1.png", "/layout/dotbright/images/fundo_2.png", "/layout/dotbright/images/fundo_3.png"];
								$fundos_aleatorio = array_rand($fundos);
								$fundo = $fundos[$fundos_aleatorio];

								for ($figi = 0;$figi < $tam;$figi++)
								{
									$gallery_image = $prods['prd_imgs']['img'][$figi];
									$gallery_image_med = $prods['prd_imgs']['img_med'][$figi];
									if (!empty($prods['seminovo_reference_image'])) {
										$reference_file = $GLOBALS['site_shell_url'].'files/products/'.$prods['prod_id'].'_1.jpg';
										$reference_version = dotbright_image_exists($reference_file) ? (string)dotbright_image_mtime($reference_file) : (string)time();
										$gallery_image .= (strpos($gallery_image, '?') === false ? '?' : '&').'refv='.$reference_version;
										$gallery_image_med .= (strpos($gallery_image_med, '?') === false ? '?' : '&').'refv='.$reference_version;
									}
									// if ($figi == 0) {
										//ativamos so para os seminovos
										// if($prods['fam_id'] != '85') $return .= '<li class="item active" data-thumb="'.$prods['prd_imgs']['img_med'][$figi].'" style="background:url('.$fundo.') center center / 90% no-repeat;background-size:auto">';
										if($prods['fam_id'] == '163') $return .= '<li class="item active" data-thumb="'.$gallery_image_med.'" style="background:url('.$fundo.') center center / 90% no-repeat;background-size:auto">';
										else $return .= '<li class="item active" data-thumb="'.$gallery_image_med.'">';
										$return .= $label_promo;
										if ($tam > 1) $return .= '<a href="'.$gallery_image.'" target="_blank" class="fancybox " id="big'.$figi.'" data-fancybox-group="gallery"><img class="zoom" src="'.$gallery_image_med.'" alt="'.rtrim($prods['prod_nome'], '-').'" border="0" /></a>';
										else $return .= '<a href="'.$gallery_image.'" target="_blank" class="fancybox" id="big'.$figi.'" data-fancybox="gallery"><img class="zoom" src="'.$gallery_image_med.'" alt="'.rtrim($prods['prod_nome'], '-').'" border="0" /></a>';
										$return .= $conteudo_dentro_img;
										$return .= '</li>';
									// } 
									// else {
									// 	$return .= '<li class="item" data-thumb="'.$prods['prd_imgs']['img_med'][$figi].'">'.$label_promo.'<a href="'.$prods['prd_imgs']['img'][$figi].'" target="_blank" class="fancybox" id="big'.$figi.'" data-fancybox-group="gallery"><img src="'.$prods['prd_imgs']['img_med'][$figi].'" alt="'.rtrim($prods['prod_nome'],'-').'" border="0" style="background:#fff;"/></a>';
									// 	$return .= $conteudo_dentro_img;
									// 	$return .= '</li>';
									// }
								}

				$return .= '</ul>';
			$return .= '</div>';
			if (!empty($prods['seminovo_reference_image'])) {
				$return .= '<div class="db-reference-image-note" role="note"><strong>Imagem ilustrativa.</strong> Peça fotografias reais antes de encomendar.</div>';
			}


			//$return .= '<div style="position:absolute;right:0;top:25%">';
				

			if($reparacoes == 1)
			{

			}
			else
			{
				$return .= $wish;
				$return .= $share;
			}

			//$return .= '</div>';
						

						// if ($prods['fam_id'] == '163') {
						// 	$return .= '<div class="garantia_icons" >
						// 					<img style="width:100%;max-width:310px;height:70px;" src="layout/dotbright/images/garantia24meses2.svg" alt="3 Anos de Garantia" border="0"> 
						// 				</div>';
						// }

						if ($prods['store_icons'] != 'undefined' && $prods['store_icons'] != '') {
							$return .= '<div class="info_store_icons" style="margin-top:25px">';
							$grau_t = $prods['store_icons'];
							$grau = explode(';', $grau_t);
							$grau_c = count($grau) - 1;
							$grau_c = ($grau_c > 4) ? 4 :$grau_c;
							for ($a = 0;$a < $grau_c;$a++) {
								$gg = explode('-', $grau[$a]);
								$return .= '<div id="info_store_icons'.$gg[0].'"><img title="'.nome_icons($gg[0]).'" src="../../files/products_components/'.$gg[0].'_2.svg" height="48"> <span style="margin-left:10px;">'.nome_icons($gg[0]).'</span></div>';
							}				
							$return .= '</div>';		
						}

						$url=str_replace($GLOBALS['site_http_url'], '', seo('pages.php?page_id='.$GLOBALS['products_page_id'], $prods['prod_id'], 2));

						
						/* Modal */
						$return .= '
						<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
							<div class="modal-dialog" role="document" style="top:50%;left:50%;transform:translate(-50%, -50%);margin:0;">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title" id="exampleModalLabel">Partilhar</h5>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
									</div>


							<div class="modal-body">
							    <div class="share-list">

							        <a class="share-item" href="#" onclick="window.open(\'https://www.facebook.com/sharer/sharer.php?u='.$site_http_url.$url.'\',\'facebook-share-dialog\',\'width=626,height=436\');return false;">
							            <img src="layout/dotbright/images/facebook.svg">
							        </a>

							        <a class="share-item" href="http://www.linkedin.com/shareArticle?mini=true&url='.$site_http_url.$url.'" target="_blank">
							            <img src="layout/dotbright/images/linkedin.svg">
							        </a>

							        <a class="share-item" href="#" onclick="window.open(\'http://pinterest.com/pin/create/bookmarklet/?media=layout/redes_sociais/pinterest.png&url='.$site_http_url.$url.'\',\'\',\'width=650,height=350\');return false;">
							            <img src="layout/dotbright/images/pinterest.svg">
							        </a>

							        <a class="share-item" href="#" onclick="window.open(\'https://twitter.com/intent/tweet?url='.$site_http_url.$url.'\',\'\',\'width=650,height=350\');return false;">
							            <img src="layout/dotbright/images/twitter.svg">
							        </a>

							        <a class="share-item" href="https://wa.me/?text='.$site_http_url.$url.'" target="_blank">
							            <img src="layout/dotbright/images/whatsapp.svg">
							        </a>

							        <!-- COPY -->
							        <a class="share-item" href="#" onclick="copyToClipboard(\''.$site_http_url.$url.'\'); return false;">
							            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
							                <path d="M16 1H4C2.9 1 2 1.9 2 3V15H4V3H16V1ZM19 5H8C6.9 5 6 5.9 6 7V21C6 22.1 6.9 23 8 23H19C20.1 23 21 22.1 21 21V7C21 5.9 20.1 5 19 5ZM19 21H8V7H19V21Z"/>
							            </svg>
							        </a>

							    </div>
							</div>

							
											</div>
										</div>
									</div>';


							/*

						<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
										<div class="modal-dialog" role="document" style="top:50%;left:50%;transform:translate(-50%, -50%);margin:0;">
											<div class="modal-content">
												<div class="modal-header">
													<h5 class="modal-title" id="exampleModalLabel">Partilhar</h5>
													<button type="button" class="close" data-dismiss="modal" aria-label="Close">
														<span aria-hidden="true">&times;</span>
													</button>
												</div>
												<div class="modal-body">
													<a href="#"  onclick="window.open(\'https://www.facebook.com/sharer/sharer.php?u='.$site_http_url.$url.'\',\'facebook-share-dialog\',\'width=626,height=436\');return false;">
														<img class="share2" src="layout/dotbright/images/facebook.svg">
													</a>
													<a href="http://www.linkedin.com/shareArticle?mini=true&url='.$site_http_url.$url.'" target="_blank">
														<img class="share2" src="layout/dotbright/images/linkedin.svg">
													</a>
													<a href="#" onclick="window.open(\'http://pinterest.com/pin/create/bookmarklet/?media=layout/redes_sociais/pinterest.png&amp;url='.$site_http_url.$url.'\',\'\',\'width=650, height=350, toolbar=no, status=no\');return(false);">
														<img class="share2" src="layout/dotbright/images/pinterest.svg">
													</a>
													<a href="#" onclick="window.open(\'https://twitter.com/intent/tweet?text=Tweet%20this&amp;url='.$site_http_url.$url.'\',\'\',\'width=650, height=350, toolbar=no, status=no\');return(false);">
														<img class="share2" src="layout/dotbright/images/twitter.svg" style="width: 100%;max-width: 32px;">
													</a>

													<a href="#" onclick="copyToClipboard(\''.$site_http_url.$url.'\'); return false;">
													    <i style="font-size:22px;margin-top:5px" class="fas fa-copy share2"></i>
													</a>

													<a href="https://wa.me/?text='.$site_http_url.$url.'" target="_blank">
													    <img class="share2" src="layout/dotbright/images/whatsapp.svg">
													</a>
												</div>
											</div>
										</div>
									</div>';

									*/

				//if(($reparacoes == 0) && ($prods['prod_localizacao_fisica'] == '') && ($prods['fam_id'] == '163'))
				if($prods['fam_id'] == '163')
				{
					if($_GET['dev']==1)var_dump($prods);
					
					$return .= '<div class="clear"></div><br>';
					if(empty($prods['seminovo_reference_image'])) {
						$return .= '<div class="row sl_fotos" style="padding-top:10px;">';
							$return .= '<span><b>Por se tratar de um produto seminovo, confirme o estado real do equipamento antes de encomendar.</b></span>';
							$return .= '<span>Solicite fotografias reais deste equipamento através do botão abaixo:</span>';
						$return .= '</div>';
					}
					$return .= '<br><br>';
					$return .= '<div class="row sl_fotos">';
						// $return .= '<div class="col-xs-12 col-sm-12 col-md-6 col-md-6">';
						// 	$return .= '<button class="btn btn-lg btn-primary1 btn-outline btn-icon-left fancy_stock" href="'.$GLOBALS['site_http_ajax_url'].'setNotificacaoProdutoImgs.php?idprod='.$prods['prod_id'].'" style="border-radius:20px;margin:0 auto;width: 100%;max-width: 100%;"> ';
						// 		$return .= '<i class="fa fa-camera" style="margin-right:10px;"></i>';
						// 		$return .= 'Solicitar fotos do equipamento ';
						// 	$return .= '</button>';
						// $return .= '</div>';
						$return .= '<div class="col-xs-12 col-sm-12 col-md-12 col-md-12" style="text-align:center">';

							$wa_num = '351914228888';
							// $wa_message = "Olá, gostaria de solicitar fotos do equipamento seminovo com ID ".$prods['prod_id'];
							$wa_message = "Olá, gostaria de solicitar fotos do equipamento seminovo com referencia ".$prods['prod_ref'];

							$return .= '<a class="btn btn-lg btn-primary1 btn-outline btn-icon-left" href="https://wa.me/'.$wa_num.'?text='.$wa_message.'" target="_blank" style="border-radius:20px;margin:0 auto;text-decoration:none;width:100%;max-width:max-content;background:#00e676">';
								$return .= '<i class="fa fa-camera" style="margin-right:10px"></i>';
								$return .= 'Solicitar fotos por Whatsapp';
							$return .= '</a>';
						
						$return .= '</div>';
					$return .= '</div>';							
					$return .= '<div class="clear"></div><br>';	
				}

				//if($reparacoes == 1 && $dados_cliente['cli_id']==36)
				if($reparacoes == 1)
				{
					$return .= '<h1 itemprop="name" class="product-title" style="margin-top:10px">'.rtrim($prods['prod_nome'], '-').'</h1>';
					$return .= '<div id="gt" class="row 1" style="gap:5px;font-size:11px">';
					if($prods['prod_ref'])
					{
						$prods['prod_ref']=explode('_',$prods['prod_ref'])[0];
						$return .= '<span> <b>REF:</b> '.$prods['prod_ref'].' | </span>';
					}
					if($prods['Ref_Ean']) $return .= '<span> <b>EAN:</b> '.$prods['Ref_Ean'].' | </span>';

                    $return .= '<span> '.strtoupper($repair_quote['warranty']).' DE GARANTIA</span>';
					$return .= '</div>';
					$capacidade = '';
					if (preg_match("/\b(\d+GB)\b/", $prods['prod_nome'], $matches)) $capacidade = $matches[0];
					/*
					$cores = [
						'black' => ['black', 'preto'],
						'white' => ['white', 'branco'],
						'green' => ['green', 'verde'],
						'orange' => ['orange', 'laranja'],
						'yellow' => ['yellow', 'amarelo'],
						'red' => ['red', 'vermelho'],
						'blue' => ['blue', 'azul'],
						'silver' => ['silver', 'prata'],
						'gold' => ['gold', 'dourado'],
						'Navy' => ['Navy', 'azul-marinho']
					];
					$cor_array = null;
					$nome_prod_min = strtolower($prods['prod_nome']);
					foreach ($cores as $cores_array => $variantes)
					{
						foreach ($variantes as $variante)
						{
							$variante_min = strtolower($variante);
							if (strpos($nome_prod_min, $variante_min) !== false)
							{
								$cor_array = $cores_array;
								break 2;
							}
						}
					}
					if ($cor_array !== null)
					{
						$color = $cor_array;
						if (isset($cores[$color]))
						{
							$cor_pt = $cores[$color][1];
							$cor_prod = ucfirst($cor_pt);
						}
					}*/

					

                    $return .= '<p class="our_price_display" itemtype="http://schema.org/Offer" itemscope="" itemprop="offers">';
                    $return .= '<span style="font-size:16px;color:#ec870e">PVP:</span> ';
                    if($repair_quote['promotion']) $return .= '<span style="color:#656565;text-decoration:line-through;font-size:18px;">'.numberformat($repair_quote['old_price'], 1).'</span> ';
                    $return .= '<span style="font-size:27px;color:#000;font-weight:800">'.numberformat($repair_quote['repair_gross'], 1).'</span> <span style="font-size:17px;color:#000;font-weight:300">(já inclui IVA)</span><span style="font-size:10px">'.$txt_iva.'</span><br>';
                    $return .= '</p>';





				}

		if($reparacoes == 1) $return .= '<button type="button" class="reparacao-avancar" onclick="reparacaoEtapa(2)">AVANÇAR PARA O PASSO SEGUINTE</button>';
    	$return .= '</div>';
		if($reparacoes == 1) {
			require_once __DIR__.'/repair_info_panel.php';
			$repair_info_html = substr($return, $repair_info_start);
			$return = substr($return, 0, $repair_info_start).dotbright_repair_info_panel($prods, $repair_info_html);
		}

		$return .= ($reparacoes == 1 ? '<div id="reparacao-formulario" class="product-detail col-md-12" style="display:none">' : '<div class="product-detail col-md-6">');
			$return .= '<div class="product-info">';

				//if($reparacoes == 1 && $dados_cliente['cli_id']==36)
				if($reparacoes == 1)
				{
					$return .= '<script>';
                        $return .= 'function reparacaoEtapa(etapa){
                                        $("#reparacao-info").toggle(etapa == 1);
                                        $("#reparacao-formulario").toggle(etapa != 1);
                                        $("#passo1").toggle(etapa == 2);
                                        $("#passo2").toggle(etapa == 3);
                                        $("#passo3").toggle(etapa == 4);
                                        $("#step").val(etapa);
                                        var painel = etapa == 1 ? $("#reparacao-info") : $("#passo" + (etapa - 1));
                                        painel.find(".reparacao-etapa-titulo").trigger("focus");
                                    }
                                    function reparacaoAvancar(etapa){
                                        var campos = etapa == 3 ? ["r_nome", "r_tel", "r_email"] : ["r_recolha", "r_cp", "r_cidade"];
                                        var primeiro = null;
                                        campos.forEach(function(id){
                                            var campo = $("#" + id);
                                            var vazio = campo.val() == "" || campo.val() == 0;
                                            campo.closest(".form-group").toggleClass("has-error", vazio);
                                            if(vazio && !primeiro) primeiro = campo;
                                        });
                                        if(primeiro){
                                            Apprise("Para prosseguir para o próximo passo, necessita primeiro de preencher os campos necessários que estão identificados com uma cor diferente");
                                            primeiro.trigger("focus");
                                            return false;
                                        }
                                        if(etapa == 4) passo3();
                                        else reparacaoEtapa(etapa);
                                        return false;
                                    }';

						$return .= 'function passo3(){
										

										var nome = $.trim($("#r_nome").val());
										var recolha = $.trim($("#r_recolha").val());
										var entrega = $.trim($("#r_entrega").val()) || recolha;
										var cp = $.trim($("#r_cp").val());
										var cidade = $.trim($("#r_cidade").val());
										var telefone = $.trim($("#r_tel").val());
										var email = $.trim($("#r_email").val());
										var nif = $.trim($("#r_nif").val());
										var periodoRecolha = $("#r_manha").is(":checked") ? "Manhã" : ($("#r_tarde").is(":checked") ? "Tarde" : "Não especificado");
										var nome2 = $.trim($("#r_nome2").val());
										var morada2 = $.trim($("#r_morada2").val());
										var cp2 = $.trim($("#r_cp2").val());
										var nif2 = $.trim($("#r_nif2").val());
										var unlock = $.trim($("#r_unlock").val());
									
										$("#resumo_nome").text(nome);
										$("#resumo_recolha").text(recolha);
										$("#resumo_entrega").text(entrega);
										$("#resumo_CP").text(cp);
										$("#resumo_cidade").text(cidade);
										$("#resumo_telefone").text(telefone);
										$("#resumo_email").text(email);
										$("#resumo_nif").text(nif);
										$("#resumo_periodoRecolha").text(periodoRecolha);

										$("#resumo_nome2").text(nome2);
										$("#resumo_morada").text(morada2);
										$("#resumo_CP2").text(cp2);
										$("#resumo_nif2").text(nif2);
										$("#resumo_unlock").text(unlock);


										if(nome2=="")$("#resumo_nome2").text(nome);
										if(morada2=="")$("#resumo_morada").text(recolha);
										if(cp2=="")$("#resumo_CP2").text(cp);
										if(nif2=="")$("#resumo_nif2").text(nif);
										

										reparacaoEtapa(4);

									}';
						$return .= 'function passo4(){	
										var campos_preenchidos=0;

										if(document.getElementById("m_termos") && document.getElementById("m_termos").checked == false) {campos_preenchidos = 3;}
										if(document.getElementById("m_termos1") && document.getElementById("m_termos1").checked == false) {campos_preenchidos = 4;}

										if(campos_preenchidos==3){Apprise("Selecione que leu e aceita os Termos e Condições","erro");return false;}
										else if(campos_preenchidos==4){Apprise("Selecione que leu e aceita os Termos de Confidencialidade","erro");return false;}

										
										var dados = $("#form_repara").serialize();
										$.ajax({
											type: "POST",
											url: "ajax/form_rapara.php",
											data: dados,
											success: function(response) {
                                                if ($.trim(response) === "Pedido enviado com sucesso!") {
                                                    Apprise("Pedido enviado com sucesso!", {
                                                        override: false,
                                                        buttons: {confirm: {
                                                            text: "OK", id: "confirm", className: "red",
                                                            action: function() {window.location.href = SITE_HTTP_URL;}
                                                        }}
                                                    });
                                                } else {Apprise(response);}
                                            },
											error: function() {Apprise("Não foi possivel enviar os seus dados, por favor tente novamente mais tarde");}
										});
									}';

					$return .= 'function validaCampos(){
										var step = $("#step").val();

										if(step==2)reparacaoAvancar(3);
                                        else if(step==3)reparacaoAvancar(4);
                                        else if(step==4)passo4();
										return false;										
									}';

					$return .= '</script>';

					$return .= '<div>';
					$return .= '<link rel="stylesheet" href="layout/'.PROJETO.'/css/repair-customer-panel.css?v='.filemtime(__DIR__.'/../css/repair-customer-panel.css').'">';
                    $return .= '<style>';
					$return .= '.ri{padding-left:10px;padding-right:10px;width:100%;border-radius:10px;border:1px solid;background-color:transparent}';
                    $return .= '.reparacao-painel{display:flow-root;max-width:900px;margin:0 auto;float:none}.reparacao-etapa-titulo{font-size:20px;margin:0 0 24px;color:#ff8900}.reparacao-etapa-titulo:focus{outline:none}.reparacao-navegacao{clear:both;padding-top:30px;display:flex;gap:12px;flex-wrap:wrap}.reparacao-avancar,.reparacao-voltar{border:0;border-radius:10px;padding:10px 18px;color:#fff;cursor:pointer}.reparacao-avancar{background:#ff8900}.reparacao-voltar{background:#9f9f9f}#reparacao-info>.reparacao-avancar{display:block;width:100%;margin-top:30px}#reparacao-formulario .product-info{width:100%;max-width:none;margin:0}#reparacao-formulario .reparacao-painel{width:100%}';
					$return .= '</style>';
					
					$return .= '<form id="form_repara" name="form_repara" onsubmit="return validaCampos()">';

						$return .= '<input type="hidden" id="step" value="1">';
						$return .= '<input type="hidden" id="prod_id" name="prod_id" value="'.$prods['prod_id'].'">';
						$return .= '<div id="resposta"></div>';

					
                        $m_padrao = 0;
                        $return .= <<<'REPAIR_CUSTOMER_PANEL'
<div id="passo1" class="reparacao-painel db-customer-panel" style="display:none">
    <header class="db-customer-header">
        <span class="db-customer-step"><b>2</b> Etapa 2 de 4</span>
        <h2 class="reparacao-etapa-titulo" tabindex="-1">Dados do cliente</h2>
    </header>
    <div class="db-customer-grid">
        <section class="db-customer-section" aria-labelledby="repair-customer-heading">
            <h3 id="repair-customer-heading"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="12" cy="7" r="4"/><path d="M3 22v-3a9 9 0 0 1 18 0v3Z"/></svg><span>Dados do cliente</span></h3>
            <div class="db-customer-fields">
                <div class="db-customer-field db-customer-field-wide form-group">
                    <label for="r_nome">Nome completo <span class="mandatory">*</span></label>
                    <input id="r_nome" name="r_nome" type="text" class="ri" placeholder="Ex.: João Silva">
                </div>
                <div class="db-customer-field form-group">
                    <label for="r_tel">Telefone <span class="mandatory">*</span></label>
                    <input id="r_tel" name="r_tel" type="text" class="ri" placeholder="Ex.: 912 345 678">
                </div>
                <div class="db-customer-field form-group">
                    <label for="r_email">Email <span class="mandatory">*</span></label>
                    <input onchange="check_email()" maxlength="80" id="r_email" name="r_email" type="text" class="ri" placeholder="Ex.: joao@mail.pt">
                </div>
                <div class="db-customer-field db-customer-field-wide form-group">
                    <label for="r_nif">NIF</label>
                    <input onchange="check_nif1()" maxlength="9" id="r_nif" name="r_nif" type="text" class="ri">
                </div>
            </div>
        </section>
        <section class="db-customer-section" aria-labelledby="repair-billing-heading">
            <h3 id="repair-billing-heading"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" aria-hidden="true"><path d="M5 2h10l4 4v16H5ZM15 2v5h4M8 11h8M8 15h8M8 19h5"/></svg><span>Dados para faturação</span></h3>
            <div class="db-customer-fields">
                <div class="db-customer-field db-customer-field-wide form-group">
                    <label for="r_nome2">Nome ou Designação (pode ser empresa)<small>Se diferente do indicado no pedido de recolha</small></label>
                    <input id="r_nome2" name="r_nome2" type="text" class="ri">
                </div>
                <div class="db-customer-field db-customer-field-wide form-group">
                    <label for="r_morada2">Morada<small>Se diferente do indicado no pedido de recolha</small></label>
                    <input id="r_morada2" name="r_morada2" type="text" class="ri">
                </div>
                <div class="db-customer-field form-group">
                    <label for="r_cp2">Código Postal<small>Se diferente do indicado no pedido de recolha</small></label>
                    <input id="r_cp2" name="r_cp2" type="text" class="ri">
                </div>
                <div class="db-customer-field form-group">
                    <label for="r_nif2">NIF<small>Se diferente do indicado no pedido de recolha</small></label>
                    <input id="r_nif2" name="r_nif2" type="text" class="ri">
                </div>
            </div>
        </section>
        <section class="db-customer-section db-customer-access" aria-labelledby="repair-access-heading">
            <h3 id="repair-access-heading"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="16" cy="8" r="5"/><path d="m12 12-9 9m2-2 2 2m0-6 2 2"/></svg><span>Acesso ao equipamento</span></h3>
            <p>Para podermos testar o seu equipamento, por favor, remova ou indique o código de desbloqueio do equipamento a reparar.</p>
            <div class="db-customer-fields">
                <div class="db-customer-field form-group">
                    <label for="r_unlock">Código de desbloqueio</label>
                    <input maxlength="10" id="r_unlock" name="r_unlock" type="text" class="ri">
                </div>
                <div class="db-customer-field form-group">
                    <label for="m_padrao">Padrão</label>
                    <div class="db-customer-pattern-controls">
                        <input class="ri" type="text" onclick="showpattern();$('#buttonpattern2').hide();$('#buttonpattern').show();" name="m_padrao" id="m_padrao" value="">
                        <input class="btn btn-default button-search" type="button" id="buttonpattern2" onclick="showpattern();$('#buttonpattern2').hide();$('#buttonpattern').show();" value="Mostrar">
                        <input class="btn btn-default button-search" type="button" id="buttonpattern" onclick="hidepattern();$('#buttonpattern').hide();$('#buttonpattern2').show();" style="display:none" value="Esconder">
                        <div id="mypattern" class="mhn-lock pattern_minha"></div>
                    </div>
                </div>
            </div>
            <div class="db-customer-notes">
                <p><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 11v6M12 7v1"/></svg><span>Sem acesso ao equipamento, poderemos não conseguir realizar alguns testes ou concluir determinados procedimentos.</span></p>
                <p><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="4" y="10" width="16" height="12" rx="2"/><path d="M7 10V7a5 5 0 0 1 10 0v3"/></svg><span>O código é tratado de forma confidencial e utilizado apenas quando necessário para a reparação e os testes.</span></p>
            </div>
            <p style="margin-top:16px"><strong>Preparação para recolha do equipamento</strong><br>Prepare o seu equipamento para a recolha. Acondicione devidamente o equipamento para o transporte.</p>
        </section>
    </div>
    <div class="reparacao-navegacao">
        <button type="button" class="reparacao-voltar" onclick="reparacaoEtapa(1)">Voltar</button>
        <button type="button" class="reparacao-avancar" onclick="reparacaoAvancar(3)">Continuar <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 5 7 7-7 7"/></svg></button>
    </div>
</div>
REPAIR_CUSTOMER_PANEL;

                        $return .= <<<'REPAIR_ADDRESS_PANEL'
<div id="passo2" class="reparacao-painel db-address-panel" style="display:none">
    <div class="db-address-header">
        <span class="db-address-step"><b>3</b> Etapa 3 de 4</span>
        <h2 class="reparacao-etapa-titulo" tabindex="-1">Morada de recolha</h2>
    </div>
    <div class="db-address-content">
        <div class="db-address-grid">
            <section class="db-address-section" aria-labelledby="repair-collection-heading">
                <h3 id="repair-collection-heading"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg><span>Morada de recolha</span></h3>
                <div class="db-address-fields">
                    <div class="db-address-field db-address-field-wide form-group">
                        <label for="r_recolha">Morada de recolha <span class="mandatory">*</span></label>
                        <input id="r_recolha" name="r_recolha" type="text" class="ri" placeholder="Ex.: Avenida da República, n.º 123, 3.º Esq.">
                    </div>
                    <div class="db-address-field form-group">
                        <label for="r_cp">Código Postal <span class="mandatory">*</span></label>
                        <input id="r_cp" name="r_cp" type="text" class="ri" placeholder="Ex.: 4470-151">
                    </div>
                    <div class="db-address-field form-group">
                        <label for="r_cidade">Cidade <span class="mandatory">*</span></label>
                        <input id="r_cidade" name="r_cidade" type="text" class="ri" placeholder="Ex.: Maia">
                    </div>
                </div>
                <fieldset class="db-address-period">
                    <legend>Período preferencial de recolha</legend>
                    <div class="db-address-period-options">
                        <label for="r_manha"><input id="r_manha" name="r_periodo" type="radio" value="1"><span>Manhã</span></label>
                        <label for="r_tarde"><input id="r_tarde" name="r_periodo" type="radio" value="2"><span>Tarde</span></label>
                    </div>
                </fieldset>
            </section>
            <section class="db-address-section" aria-labelledby="repair-delivery-heading">
                <h3 id="repair-delivery-heading"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" aria-hidden="true"><path d="m2 11 10-9 10 9M5 9v13h14V9M9 22v-8h6v8"/></svg><span>Morada de entrega</span></h3>
                <div class="db-address-field form-group">
                    <label for="r_entrega">Morada de entrega<small>Se diferente da morada de recolha</small></label>
                    <input id="r_entrega" name="r_entrega" type="text" class="ri" placeholder="Preencha se a morada de entrega for diferente">
                </div>
            </section>
        </div>
        <section class="db-address-packing" aria-labelledby="repair-packing-heading">
            <h3 id="repair-packing-heading"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" aria-hidden="true"><path d="m3 7 9-5 9 5v10l-9 5-9-5Zm0 0 9 5 9-5M12 12v10M7 4l10 6"/></svg><span>Dicas de embalamento</span></h3>
            <div class="db-address-packing-tips">
                <div><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" aria-hidden="true"><path d="m3 8 9-4 9 4-9 4Zm0 0v10l9 4 9-4V8M12 12v10M3 8 1 4l9-3 2 3m9 4 2-4-9-3-2 3"/></svg><p>Utilize a caixa original, se possível, dentro de uma caixa de envio.</p></div>
                <div><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="8" y="2" width="8" height="16" rx="2"/><path d="m5 9-3 4 4 9h12l4-9-3-4M11 15h2"/></svg><p>Envolva o equipamento em papel ou num pano macio e preencha os espaços vazios.</p></div>
                <div><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" aria-hidden="true"><path d="m3 7 9-5 9 5v10l-9 5-9-5Zm0 0 9 5 9-5M12 12v10M7 4l10 6v5l-4 2V8"/></svg><p>Feche bem a caixa com fita adesiva.</p></div>
            </div>
        </section>
    </div>
    <div class="reparacao-navegacao">
        <button type="button" class="reparacao-voltar" onclick="reparacaoEtapa(2)">Voltar</button>
        <button type="button" class="reparacao-avancar" onclick="reparacaoAvancar(4)">Continuar para o resumo <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 5 7 7-7 7"/></svg></button>
    </div>
</div>
REPAIR_ADDRESS_PANEL;

                        $return .= <<<'REPAIR_SUMMARY_OPEN'
<div id="passo3" class="reparacao-painel db-summary-panel" style="display:none">
    <div class="db-summary-header">
        <span class="db-summary-step"><b>4</b> Etapa 4 de 4</span>
        <h2 class="reparacao-etapa-titulo" tabindex="-1">Resumo e conclusão do pedido</h2>
    </div>
    <div class="db-summary-content">
        <p class="db-summary-intro">O seu pedido de recolha está quase concluído! Confirme os dados e o valor estimado antes de solicitar a recolha.</p>
        <div class="db-summary-columns">
            <div class="db-summary-main">
                <div class="db-summary-data-grid">
                    <section class="db-summary-card" aria-labelledby="repair-summary-customer">
                        <h3 id="repair-summary-customer"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="12" cy="7" r="4"/><path d="M3 22v-3a9 9 0 0 1 18 0v3Z"/></svg><span>Dados do cliente</span></h3>
                        <dl class="db-summary-data">
                            <div><dt>Nome completo</dt><dd id="resumo_nome"></dd></div>
                            <div><dt>Telemóvel</dt><dd id="resumo_telefone"></dd></div>
                            <div><dt>Email</dt><dd id="resumo_email"></dd></div>
                            <div><dt>NIF</dt><dd id="resumo_nif"></dd></div>
                        </dl>
                    </section>
                    <section class="db-summary-card" aria-labelledby="repair-summary-billing">
                        <h3 id="repair-summary-billing"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" aria-hidden="true"><path d="M5 2h10l4 4v16H5ZM15 2v5h4M8 11h8M8 15h8M8 19h5"/></svg><span>Dados de faturação</span></h3>
                        <dl class="db-summary-data">
                            <div><dt>Nome completo</dt><dd id="resumo_nome2"></dd></div>
                            <div><dt>Morada</dt><dd id="resumo_morada"></dd></div>
                            <div><dt>Código Postal</dt><dd id="resumo_CP2"></dd></div>
                            <div><dt>NIF</dt><dd id="resumo_nif2"></dd></div>
                        </dl>
                    </section>
                </div>
                <section class="db-summary-card db-summary-address" aria-labelledby="repair-summary-address">
                    <h3 id="repair-summary-address"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg><span>Recolha e entrega</span></h3>
                    <dl class="db-summary-data db-summary-address-data">
                        <div><dt>Morada para recolha</dt><dd id="resumo_recolha"></dd></div>
                        <div><dt>Código Postal</dt><dd id="resumo_CP"></dd></div>
                        <div><dt>Morada para entrega</dt><dd id="resumo_entrega"></dd></div>
                        <div><dt>Período de recolha</dt><dd id="resumo_periodoRecolha"></dd></div>
                    </dl>
                </section>
            </div>
            <div class="db-summary-side">
                <section class="db-summary-card" aria-labelledby="repair-summary-service">
                    <h3 id="repair-summary-service"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="m14 7-9 9a3 3 0 0 0 4 4l9-9M14 7a5 5 0 0 1 6-5l-3 3 2 2 3-3a5 5 0 0 1-6 6"/></svg><span>Resumo da avaria</span></h3>
                    <div class="db-summary-service">
REPAIR_SUMMARY_OPEN;
								$return .= rtrim($prods['prod_nome'], '-').'<br>';
								if($prods['prod_ref']) $return .= '<b>REF:</b> '.$prods['prod_ref'].'<br>';
								if($prods['Ref_Ean']) $return .= '<b>EAN:</b> '.$prods['Ref_Ean'].'<br>';
                                $return .= strtoupper($repair_quote['warranty']).' DE GARANTIA';
                                $return .= '<br><b>Prazo estimado de reparação em loja:</b> '.htmlspecialchars($repair_quote['service']['time'], ENT_QUOTES, 'UTF-8');
                                $return .= '<br><b>Tipo de peça:</b> '.htmlspecialchars($repair_quote['service']['part'], ENT_QUOTES, 'UTF-8');
                                $return .= '<br><b>Descrição da peça:</b> '.htmlspecialchars($repair_quote['service']['part_detail'], ENT_QUOTES, 'UTF-8');

                        $return .= '</div></section>';
                        $return .= '<section class="db-summary-card db-summary-amounts" aria-labelledby="repair-summary-amounts"><h3 id="repair-summary-amounts"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="4" y="2" width="16" height="20" rx="2"/><path d="M7 6h10M7 11h3M14 11h3M7 15h3M14 15h3M7 19h3M14 19h3"/></svg><span>Valor estimado</span></h3><dl class="db-summary-prices"><div><dt>Avaria</dt><dd>'.numberformat($repair_quote['repair_net']).'</dd></div><div><dt>Portes</dt><dd>'.numberformat($repair_quote['shipping_net']).'</dd></div><div><dt>IVA</dt><dd>'.numberformat($repair_quote['vat']).'</dd></div><div class="db-summary-total"><dt>Total</dt><dd>'.numberformat($repair_quote['total']).'</dd></div></dl></section>';
                        $return .= <<<'REPAIR_SUMMARY_CLOSE'
            </div>
        </div>
        <section class="db-summary-terms" aria-labelledby="repair-summary-terms">
            <h3 id="repair-summary-terms">Confirmação do pedido</h3>
            <div class="db-summary-consent">
                <label for="m_termos"><input type="checkbox" id="m_termos" name="m_termos" value="1"><span>Li e aceito os <strong>Termos e Condições</strong> <span class="mandatory">*</span></span></label>
                <a href="p37-termos-e-condicoes-pt?popup=1" class="fancy_content">Ler os termos e condições</a>
            </div>
            <div class="db-summary-consent">
                <label for="m_termos1"><input type="checkbox" id="m_termos1" name="m_termos1" value="1"><span>Li e aceito os <strong>Termos de Confidencialidade</strong> <span class="mandatory">*</span></span></label>
                <a href="p43-politica-de-privacidade-pt?popup=1" class="fancy_content">Política de Privacidade</a>
            </div>
        </section>
        <p class="db-summary-note"><strong>Observações:</strong> Após submeter o pedido, a equipa confirmará os detalhes e o valor estimado, agendará a recolha e combinará consigo o pagamento. Receberá um email com o resumo do pedido.</p>
    </div>
    <div class="reparacao-navegacao">
        <button type="button" class="reparacao-voltar" onclick="reparacaoEtapa(3)">Voltar e corrigir</button>
        <button type="button" class="reparacao-avancar" onclick="passo4()">Solicitar recolha <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 4 4L19 6"/></svg></button>
    </div>
</div>
REPAIR_SUMMARY_CLOSE;

					$return .= '</form>';

					$return .= '
					<script>
						function showpattern()
						{
					        var pattern = document.getElementById("mypattern");
					        pattern.style.display = "block";
					        var bpattern = document.getElementById("buttonpattern");
					        bpattern.style.display = "block";
					    }
					    function hidepattern(){
					        var pattern = document.getElementById("mypattern");
					        pattern.style.display= "none";
					        var bpattern = document.getElementById("buttonpattern");
					        bpattern.style.display = "none";
					    }
					    function check_email()
						{
							var email=$(\'#r_email\').val();
							var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
							if(email.match(mailformat))
							{
							}
							else
							{
						    	Apprise(\'O email que inseriu não é válido.\');								
								$("#r_email").val("");
								$("#r_email").css("background-color","#fffddf");
								return false;
							}
							return true;
						}
					    function check_nif1()
						{
							var nif=$(\'#r_nif\').val();
							
							if(!validaNIF(nif))
							{
								Apprise(\'O NIF não é válido.\');								
								$("#r_nif").val("");
								$("#r_nif").css("background-color","#fffddf");
								return false;
							}
							return true;
						}
						function validaNIF(value)
						{
						  value = value + \'\';
						  if (!/^[0-9]{9}$/.test(value)) return false;
						  if (!/^[123]|45|5/.test(value)) return false;
						  let tot =
						    value[0] * 9 +
						    value[1] * 8 +
						    value[2] * 7 +
						    value[3] * 6 +
						    value[4] * 5 +
						    value[5] * 4 +
						    value[6] * 3 +
						    value[7] * 2;
						  let div = tot / 11;
						  let mod = tot - parseInt(div) * 11;
						  let tst = mod == 1 || mod == 0 ? 0 : 11 - mod;
						  return value[8] == tst;
						}

					    $(function(){mhnUI.setup();});
					    mhnUI={
					        pattern: "",
					        setup: function() {
					            this.lock(),this.colors()

					        },
					        lock: function()
					        {
					            mhnUI.page.hide(),pattern=new PatternLock(".mhn-lock",{

					                onDraw:function(pattern){
					                    var x_padrao = document.getElementById("m_padrao");
					                    x_padrao.value=pattern;
					                },
					                margin: 15
					            }),$(".mhn-lock-title").html($(".mhn-lock-title").data("title")),pattern.checkForPattern("123",function() {
					                $(".patt-holder").addClass("")
					        }';
					        if(isset($m_padrao) && $m_padrao!='')$return .= ',pattern.setPattern("'.$m_padrao.'")';
					        $return .= '
					        );
					        },
					        page: {
					            show: function(t) {
					                t=t ? t : "page-home",$(".mhn-ui-page").hide(),$(".mhn-ui-page." + t).show()
					            },
					            hide: function(t) {
					                t=t ? t : "page-lock",$(".mhn-ui-page").hide(),$(".mhn-ui-page." + t).show()
					            }
					        },
					        colors: function() {
					            $(".mhn-ui-icon span").on("mouseover",function() {
					                $(this).css("background",$(this).data("color"))
					            }).on("mouseout",function() {
					                $(this).removeAttr("style")
					            })
					        }
					    };
						!function(t,e,n,a){"use strict";function r(t){for(var e=t.holder,n=t.option,a=n.matrix,r=n.margin,i=n.radius,o=[\'<ul class="patt-wrap" style="padding:\'+r+\'px">\'],s=0,l=a[0]*a[1];l>s;s++)o.push(\'<li class="patt-circ" style="margin:\'+r+"px;width : "+2*i+"px;height : "+2*i+"px;-webkit-border-radius: "+i+"px;-moz-border-radius: "+i+"px;border-radius: "+i+\'px;"><div class="patt-dots"></div></li>\');o.push("</ul>"),e.html(o.join("")).css({width:a[1]*(2*i+2*r)+2*r+"px",height:a[0]*(2*i+2*r)+2*r+"px"}),t.pattCircle=t.holder.find(".patt-circ")}function i(t,e,n,a){var r=e-t,i=a-n;return{length:Math.ceil(Math.sqrt(r*r+i*i)),angle:Math.round(180*Math.atan2(i,r)/Math.PI)}}function o(){}function s(e,n){var a=this,i=a.token=Math.random(),h=p[i]=new o,u=h.holder=t(e);if(0!=u.length){h.object=a,n=h.option=t.extend({},s.defaults,n),r(h),u.addClass("patt-holder"),"static"==u.css("position")&&u.css("position","relative"),u.on("touchstart mousedown",function(t){d.call(this,t,a)}),h.option.onDraw=n.onDraw||l;var c=n.mapper;h.mapperFunc="object"==typeof c?function(t){return c[t]}:"function"==typeof c?c:l,h.option.mapper=null}}var l=function(){},p={},d=function(e,a){e.preventDefault();var r=p[a.token];if(!r.disabled){r.option.patternVisible||r.holder.addClass("patt-hidden");var i="touchstart"==e.type?"touchmove":"mousemove",o="touchstart"==e.type?"touchend":"mouseup";t(this).on(i+".pattern-move",function(t){h.call(this,t,a)}),t(n).one(o,function(){u.call(this,e,a)});var s=r.holder.find(".patt-wrap"),l=s.offset();r.wrapTop=l.top,r.wrapLeft=l.left,a.reset()}},h=function(e,n){e.preventDefault();var a=e.pageX||e.originalEvent.touches[0].pageX,r=e.pageY||e.originalEvent.touches[0].pageY,o=p[n.token],s=o.pattCircle,l=o.patternAry,d=o.option.lineOnMove,h=o.getIdxFromPoint(a,r),u=h.idx,c=o.mapperFunc(u)||u;if(l.length>0){var f=i(o.lineX1,h.x,o.lineY1,h.y);o.line.css({width:f.length+10+"px",transform:"rotate("+f.angle+"deg)"})}if(u){if(-1==l.indexOf(c)){var v,m=t(s[u-1]);if(o.lastPosObj){for(var g=o.lastPosObj,x=g.i,w=g.j,b=Math.abs(h.i-x),j=Math.abs(h.j-w);(0==b&&j>1||0==j&&b>1||j==b&&j>1)&&(w!=h.j||x!=h.i);){x=b?Math.min(h.i,x)+1:x,w=j?Math.min(h.j,w)+1:w,b=Math.abs(h.i-x),j=Math.abs(h.j-w);var M=(w-1)*o.option.matrix[1]+x,y=o.mapperFunc(M)||M;-1==l.indexOf(y)&&(t(s[M-1]).addClass("hovered"),l.push(y))}v=[],h.j-g.j>0?v.push("s"):h.j-g.j<0?v.push("n"):0,h.i-g.i>0?v.push("e"):h.i-g.i<0?v.push("w"):0,v=v.join("-")}m.addClass("hovered"),l.push(c);var P=o.option.margin,k=o.option.radius,C=(h.i-1)*(2*P+2*k)+2*P+k,O=(h.j-1)*(2*P+2*k)+2*P+k;if(1!=l.length){var D=i(o.lineX1,C,o.lineY1,O);o.line.css({width:D.length+10+"px",transform:"rotate("+D.angle+"deg)"}),d||o.line.show()}v&&(o.lastElm.addClass(v+" dir"),o.line.addClass(v+" dir"));var E=t(\'<div class="patt-lines" style="top:\'+(O-5)+"px;left:"+(C-5)+\'px"></div>\');o.line=E,o.lineX1=C,o.lineY1=O,o.holder.append(E),d||o.line.hide(),o.lastElm=m}o.lastPosObj=h}},u=function(t,e){t.preventDefault();var n=p[e.token],a=n.patternAry.join("");n.holder.off(".pattern-move").removeClass("patt-hidden"),a&&(n.option.onDraw(a),n.line.remove(),n.rightPattern&&(a==n.rightPattern?n.onSuccess():(n.onError(),e.error())))};o.prototype={constructor:o,getIdxFromPoint:function(t,e){var n=this.option,a=n.matrix,r=t-this.wrapLeft,i=e-this.wrapTop,o=null,s=n.margin,l=2*n.radius+2*s,p=Math.ceil(r/l),d=Math.ceil(i/l),h=r%l,u=i%l;return p<=a[1]&&d<=a[0]&&h>2*s&&u>2*s&&(o=(d-1)*a[1]+p),{idx:o,i:p,j:d,x:r,y:i}}},s.prototype={constructor:s,option:function(t,e){var n=p[this.token],i=n.option;return e===a?i[t]:(i[t]=e,void(("margin"==t||"matrix"==t||"radius"==t)&&r(n)))},getPattern:function(){return p[this.token].patternAry.join("")},setPattern:function(t){var e=p[this.token],n=e.option,a=n.matrix,r=n.margin,i=n.radius;if(n.enableSetPattern){this.reset(),e.wrapLeft=0,e.wrapTop=0;for(var o=0;o<t.length;o++){var s=t[o]-1,d=s%a[1],u=Math.floor(s/a[1]),c=d*(2*r+2*i)+2*r+i,f=u*(2*r+2*i)+2*r+i;h.call(null,{pageX:c,pageY:f,preventDefault:l,originalEvent:{touches:[{pageX:c,pageY:f}]}},this)}}},enable:function(){var t=p[this.token];t.disabled=!1},disable:function(){var t=p[this.token];t.disabled=!0},reset:function(){var t=p[this.token];t.pattCircle.removeClass("hovered dir s n w e s-w s-e n-w n-e"),t.holder.find(".patt-lines").remove(),t.patternAry=[],t.lastPosObj=null,t.holder.removeClass("patt-error patt-success")},error:function(){p[this.token].holder.addClass("patt-error")},checkForPattern:function(t,e,n){var a=p[this.token];a.rightPattern=t,a.onSuccess=e||l,a.onError=n||l}},s.defaults={matrix:[3,3],margin:20,radius:25,patternVisible:!0,lineOnMove:!0,enableSetPattern:1},e.PatternLock=s}(jQuery,window,document);
						</script>';



				}
				else
				{
					$return .= '<div class="product-info-header">';					

					if($reparacoes == 1)
					{

					}
					else
					{
						$return .= '<a href="'.$url_brand.'" title="'.$prods_marca_lang.'">';
							$return .= '<span style="border:1px solid;border-radius:10px;padding:5px 25px;font-weight:800;font-size:15px;color:#000">'.$prods_marca_lang.'</span>';
						$return .= '</a>';
					}

						$return .= '<h1 itemprop="name" class="product-title" style="margin-top:10px;">'.rtrim($prods['prod_nome'], '-').'</h1>';
						$return .= '<div id="gt" class="row 2" style="gap:5px;font-size:11px">';
							if($prods['prod_ref'])
							{
								$prods['prod_ref']=explode('_',$prods['prod_ref'])[0];
								$return .= '<span> <b>REF:</b> '.$prods['prod_ref'].' | </span>';
							}
							if($prods['Ref_Ean']) $return .= '<span> <b>EAN:</b> '.$prods['Ref_Ean'].' | </span>';

							if(substr_count($prods['prod_ref'],'BAT')>0 && substr($prods['prod_ref'],0,3)=='REP')
							{
								$return .= '<span> 3 MESES DE GARANTIA</span>';
							}
							else
							{
								if($prods['fam_id'] == '163')
								{
									$return .= '<span> 24 MESES DE GARANTIA</span>';
								}
								else
								{
									$return .= '<span> 36 MESES DE GARANTIA</span>';
								}
							} 
							
						$return .= '</div>';
					$return .= '</div>';

					$capacidade = '';
					if (preg_match("/\b(\d+GB)\b/", $prods['prod_nome'], $matches)) $capacidade = $matches[0];

					/*
					$cores = [
						'black' => ['black', 'preto'],
						'white' => ['white', 'branco'],
						'green' => ['green', 'verde'],
						'orange' => ['orange', 'laranja'],
						'yellow' => ['yellow', 'amarelo'],
						'red' => ['red', 'vermelho'],
						'blue' => ['blue', 'azul'],
						'silver' => ['silver', 'prata'],
						'gold' => ['gold', 'dourado'],
						'Navy' => ['Navy', 'azul-marinho']
					];

					$cor_array = null;

					$nome_prod_min = strtolower($prods['prod_nome']);

					foreach ($cores as $cores_array => $variantes) {
						foreach ($variantes as $variante) {
							$variante_min = strtolower($variante);
							if (strpos($nome_prod_min, $variante_min) !== false) {
								$cor_array = $cores_array;
								break 2;
							}
						}
					}

					if ($cor_array !== null) {
						$color = $cor_array;
						if (isset($cores[$color])) {
							$cor_pt = $cores[$color][1];
							$cor_prod = ucfirst($cor_pt);
						}
					}*/

					$return .= '<div class="product-purchase-container"> ';
					$txt_campanhas = '';
					if ($prods['prod_precos']['PROMOCAO'] == 1 && (strtotime($prods['prod_precos']['Promo_data_ini']) <= $today && strtotime($prods['prod_precos']['Promo_data_fim']) >=$today))$txt_campanhas = $prods['prod_precos']['Promo_descricao'];
								
					$return .= '<p class="our_price_display" itemtype="http://schema.org/Offer" itemscope="" itemprop="offers">';
						if ($prods['prod_precos']['PROMOCAO'] == 1 && strtotime($prods['prod_precos']['Promo_data_fim']) >= strtotime($prods['prod_precos']['Promo_data_ini']) && $prods['prod_precos']['Promo_data_fim']!='0000-00-00' && $prods['prod_precos']['Promo_data_ini']!='0000-00-00')
						{
							$return .= '<div class="tag tag-promocao" style="font-size:30px;line-height:32px;font-weight:600;font-family:"Poppins",sans-serif">';
								$return .= '<span style="font-size:16px;color:#ec870e">PVP:</span> <span style="color:#656565;text-decoration:line-through;font-size:18px;">'.numberformat($prods['prod_precos']['PVP'], 1).'</span> <span style="font-size:27px;color:#000;font-weight:800">'.numberformat($prods['prod_precos']['obj_promo'], 1).'</span><br>';
							$return .= '</div>';
						}
						elseif ($prods['prod_precos']['PROMOCAO'] == 2 && $prods['prod_precos']['PRECO_ANTIGO'] > 0 && $prods['prod_precos']['PRECO_ANTIGO'] > $prods['prod_precos']['PVP'])
						{
							$return .= '<div class="tag tag-promocao" style="font-size:16px;line-height:28px">';
							$return .= '<span style="text-decoration:line-through;color:#FF0000">'.numberformat($prods['prod_precos']['PRECO_ANTIGO'], 1).'</span><br>';
							$return .= '</div>';
						}
						else
						{
							if($prods['prod_precos']['PVP'] > 0)
							{
								$pvp=$prods['prod_precos']['PVP'];
								if($prods['prod_precos']['IVA'] > 0)
								{
									$pvp_com_iva=$prods['prod_precos']['PVP'];
									$pvp_sem_iva=($prods['prod_precos']['PVP']/(1+$prods['prod_precos']['IVA']/100));
								}
								else
								{
									$pvp_sem_iva=$prods['prod_precos']['PVP'];
									$pvp_com_iva=($prods['prod_precos']['PVP']*(1+$prods['prod_precos']['IVA']/100));
								}						
								$return .= '<span style="font-size:16px;color:#ec870e">PVP:</span> 
											<span style="font-size:27px;color:#000;font-weight:800">'.numberformat($pvp_com_iva, 1).'</span>
											<span style="font-size :10px">'.$txt_iva.'</span><br>';
							}
						}
					$return .= '</p>';

						$return .='<div class="product-form container">';
							$return .= '<div class="row" style="width:100%;max-width:375px;margin:0">';



								if($reparacoes == 1)
								{
									/*
									
									if($dados_cliente['cli_id']==36)
									{

									}
									else
									{
										$return .= '<form class="form-registo" method="post" name="registoform" id="registoform" onsubmit="return validar_pedido()">';
										$return .= '<div class="row">'.mostra_fotos_familia($prods['fam_id']).'</div>';
										$return .= 'Recolhemos o seu equipamento danificado <br>ou pode optar por deixar o seu equipamento em uma das nossas lojas.<br>Não perca tempo.<br><br>';
										$return .= '<input type="submit" style="margin-top:10px;text-align:center;padding-right:0.1em;padding-left:0.1em;font-size:18px !important" class="exclusive btn btn-cart btn-default btn-cart  fp_comprar" value="Solicite agora a recolha">';
										$return .= '<input type="hidden" name="prod_id" value="'.$prods['prod_id'].'">
										<div class="row">
								            <div class="col-md-6">
								                <div class="form-group">
								                    <label for="m_nome">Nome <span class="mandatory">*</span></label>
								                    <input id="m_nome" maxlength="255" autocomplete="off" type="text" value="" name="m_nome" class="form-control " style="">
								                </div>
								            </div>
								            <div class="col-md-6">
								                <div class="form-group">
								                    <label for="m_morada">Morada <span class="mandatory">*</span></label>
								                    <input id="m_morada" maxlength="255" autocomplete="off" type="text" value="" name="m_morada" class="form-control " style="">
								                </div>
								            </div>
								            <div class="col-md-6">
								                <div class="form-group">
								                    <label for="m_codpos">Código Postal <span class="mandatory">*</span></label>
								                    <input id="m_codpos" maxlength="255" autocomplete="off" type="text" value="" name="m_codpos" class="form-control " style="">
								                </div>
								            </div>
								            
											<div class="clear"></div>
								            <div class="col-md-6">
								                <div class="form-group">
								                    <label for="m_contribuinte">Nº Contribuinte </label>
								                    <input id="m_contribuinte" maxlength="255" autocomplete="off" type="text" value="" name="m_contribuinte" class="form-control " style="">
								                </div>
								            </div>
								            <div class="col-md-6">
								                <div class="form-group">
								                    <label for="m_telefone">Contacto Telefone <span class="mandatory">*</span></label>
								                    <input id="m_telefone" maxlength="255" autocomplete="off" type="text" value="" name="m_telefone" class="form-control " style="">
								                </div>
								            </div>


								            <div class="checkbox">
									            <label for="m_termos"> <input type="checkbox" id="m_termos" name="m_termos" value="1"> <strong>Li e Aceito os Termos e Condições</strong><span class="mandatory">*</span> </label> (<a href="p37-termos-e-condicoes-pt?popup=1" class="fancy_content">Ler os termos e condições</a>)
									            <br><label for="m_termos1"> <input type="checkbox" id="m_termos1" name="m_termos1" value="1"> <strong>Li e Aceito os Termos de Confidencialidade </strong><span class="mandatory">*</span> </label> (<a href="p43-politica-de-privacidade-pt?popup=1" class="fancy_content">Política de Privacidade</a>)
									        </div>


								        </div>

								        <script>
								        function validar_pedido()
										{
											var cor = "#ffffe5";
											var cor2 = "#ffffff";

											campos_obrigatorios=new Array("m_nome","m_morada","m_codpos","m_telefone");
											var campos_preenchidos;
											var i=0;while(i < campos_obrigatorios.length){ $(\'input[name="\'+campos_obrigatorios[i]+\'"]\').css("background-color",cor2);i=i+1;}
											var i=0;while(i < campos_obrigatorios.length){ if(document.forms[\'registoform\'].elements[campos_obrigatorios[i]].value==""){ campos_preenchidos=1;$(\'input[name="\'+campos_obrigatorios[i]+\'"]\').css("background-color",cor);} i=i+1;}

											if(campos_preenchidos !=1)
											{
												if(document.getElementById(\'m_termos\') && document.getElementById(\'m_termos\').checked == false) {campos_preenchidos = 3;}
												if(document.getElementById(\'m_termos1\') && document.getElementById(\'m_termos1\').checked == false) {campos_preenchidos = 4;}
											}

											if(campos_preenchidos==1){Apprise("Para prosseguir, preencha todos os campos assinalados.",\'erro\');return false;}
											else if(campos_preenchidos==2){Apprise("Selecione o campo de prevenção spam para sabermos que não é um robô",\'erro\');return false;}
											else if(campos_preenchidos==3){Apprise("Selecione que leu e aceita os Termos e Condições",\'erro\');return false;}
											else if(campos_preenchidos==4){Apprise("Selecione que leu e aceita os Termos de Confidencialidade",\'erro\');return false;}

											envia_dados();

											return false;
										}


										function envia_dados()
										{
											$.ajax({
													type: "POST",
													url: SITE_HTTP_AJAX_URL+"submit_pedidos.php",
													data: $("#registoform").serialize()
												}).done(function(resultado)
												{
													Apprise("Recebemos o seu pedido, deverá aguardar agora um contacto do nosso departamento de logistica para agendar o levantamento do equipamento",\'erro\');
												});
										}

										</script>


								        </form>';
								    }
								    */




								}
								else
								{
									if(($precos['STOCK'] > 0 || $PODE_ENCOMENDAR == 1) && $prods['prod_precos']['PVP'] > 0 && $prods['prod_carrinho'] == 1)
									{
										$return .= '<div class="col-md-5 col-xs-6" style="padding:0;">';
											$return .= '<div class="product-qty-form" id="quantity_wanted_p">';
												$return .= '<div class="input-group">';
													$return .= '<input type="number" value="1" class="text qtde_a" id="quantity_wanted" name="qty" style="border:1px solid rgb(189, 194, 201);">';
													$return .= '<button type="button" data-field-qty="qty" class="quantity-plus w-icon-plus product_quantity_up"> </button>';
													$return .= '<button type="button" data-field-qty="qty" class="quantity-minus w-icon-minus product_quantity_down"> </button>';
													$return .= '<span class="clearfix"></span>';
												$return .= '</div>';
											$return .= '</div>';
										$return .='</div>';

										$return .= '<div class="col-md-7 col-xs-6" style="padding:0">';
											// echo '<pre>stock:'.$precos['STOCK'].'<br>PODE_ENCOMENDAR:'.$PODE_ENCOMENDAR.'<br>pvp:'.$prods['prod_precos']['PVP'].'<br>carrinho:'.$prods['prod_carrinho'].'<br></pre>';		
											if($reparacoes)
											{
												$return .= '<button style="text-align:center;padding-right:0.1em ;padding-left:0.1em;font-size:18px !important;" class="exclusive btn btn-cart btn-default btn-cart  fp_comprar" onclick="adicionarCarrinhoProdutoOpcoes('.$prods['prod_id'].',$(\'#quantity_wanted\').val());" type="button">'.getLinguas('key_adicionar_reparacoes').'</button>';
											}
											else{
												if((($precos['STOCK'] == 0  || $precos['STOCK'] == 1) && $PODE_ENCOMENDAR == 1) && $prods['prod_precos']['PVP'] > 0 && $prods['prod_carrinho'] == 1){
													$return .= '<button  class="btn btn-cart btn-default " onclick="adicionarCarrinhoProdutoOpcoes('.$prods['prod_id'].',$(\'.qtde_a\').val());" type="button">
																	<span>'.getLinguas('key_comprar').'</span>
																</button>';
													// $return .= '<br> <br> <button class="fp_comprar_esgotado fancy_stock" href="'.$GLOBALS['site_http_ajax_url'].'setNotificacaoProduto.php?idprod='.$prods['prod_id'].'">ALERTA DE STOCK</button>';
												}
												// elseif($precos['STOCK'] > 1 && $prods['prod_precos']['PVP'] > 0 && $prods['prod_carrinho'] == 1){
												elseif($precos['STOCK'] >= 1 && $prods['prod_precos']['PVP'] > 0 && $prods['prod_carrinho'] == 1){
													$return .= '<button  class="btn btn-cart btn-default btn-cart" onclick="adicionarCarrinhoProdutoOpcoes('.$prods['prod_id'].',$(\'.qtde_a\').val());" type="button">
																	<span>'.getLinguas('key_comprar').'</span>
																</button>';
												}					
												else{
													$return .= '<button class="fp_comprar_esgotado fancy_stock" href="'.$GLOBALS['site_http_ajax_url'].'setNotificacaoProduto.php?idprod='.$prods['prod_id'].'"><span>ALERTA DE STOCK</span></button>';
												}											
											}
											
										$return .='</div>';
									}
									else
									{
										$return .= '<div class="col-md-7 col-xs-12" style="padding:0">';
											if($reparacoes)
											{
												$return .= '<button style="text-align:center;padding-right:0.1em ;padding-left:0.1em;font-size:18px !important;" class="exclusive btn btn-cart btn-default btn-cart  fp_comprar" onclick="adicionarCarrinhoProdutoOpcoes('.$prods['prod_id'].',$(\'#quantity_wanted\').val());" type="button">'.getLinguas('key_adicionar_reparacoes').'</button>';
											}
											else{
												if((($precos['STOCK'] == 0  || $precos['STOCK'] == 1) && $PODE_ENCOMENDAR == 1) && $prods['prod_precos']['PVP'] > 0 && $prods['prod_carrinho'] == 1)
												{
													$return .= '<button  class="btn btn-cart btn-default " onclick="adicionarCarrinhoProdutoOpcoes('.$prods['prod_id'].',$(\'.qtde_a\').val());" type="button">
																	<span>'.getLinguas('key_comprar').'</span>
																</button>';
												}
												elseif($precos['STOCK'] > 1 && $prods['prod_precos']['PVP'] > 0 && $prods['prod_carrinho'] == 1)
													$return .= '<button  class="btn btn-cart btn-default btn-cart" onclick="adicionarCarrinhoProdutoOpcoes('.$prods['prod_id'].',$(\'.qtde_a\').val());" type="button">
																<span>'.getLinguas('key_comprar').'</span>
																</button>';
												else
													$return .= '<button class="fp_comprar_esgotado fancy_stock" href="'.$GLOBALS['site_http_ajax_url'].'setNotificacaoProduto.php?idprod='.$prods['prod_id'].'"><span>ALERTA DE STOCK</span></button>';
											}
										
										$return .='</div>';
									}
								}


								$p_way = geraCatalogoRecursivo(intval($prods['fam_id']));
								$fam = get_family_info(intval($p_way[0]));
								$reparacoes = 0;
								if(!empty($fam) && is_array($fam) && $fam['reparacoes'] == 1) $reparacoes = 1;

							$return .='</div>';

							if($reparacoes == 0)
							{
								if($prods['fam_id'] == '163')
								{
									$return .= '<div class="row" style="margin-top:30px;">';
										$return .= '<div id="prod_info_dt" class="col-xs-12 col-sm-10 col-md-8 col-lg-8">';
											if($prods['prod_marca']['marca_nome'] == 'APPLE')
											{
												if($capacidade)
												{
													$return .= '<div class="info_detalhes">
																	<span style="gap:15px;display:flex"><b>Capacidade:</b> '.$capacidade.'</span>
																</div>';
												}
												if($prods['prod_bateria'])
												{
													$return .= '<div class="info_detalhes">
																	<span style="gap:15px;display:flex"><b>Bateria:</b> '.$prods['prod_bateria'].'%</span>
																</div>';
												}
											}
											else
											{
												if($capacidade)
												{
													$return .= '<div class="info_detalhes">
																	<span style="gap:15px;display:flex"><b>Capacidade:</b> '.$capacidade.'</span>
																</div>';
												}
											}
											//comentado dia 11-04-2024 a pedido da Cristina
											/*
											if($cor_prod)
											{
												$return .= '<div class="info_detalhes">
																<div class="row" style="margin:0">
																	<span><b>Cor:</b></span>
																	<div class="color_pick" id="colorPicker" style="padding:8px;display:inline-block;border:1px solid #ccc;background-color:'.getColor($cor_prod).';  margin:0 10px 0 20px;"></div>	
																	<span>'.$cor_prod.'</span>														
																</div>
															</div>';
											}
											*/
												
											// if($prods['prod_estado']){
											// 	$return .= '<div class="info_detalhes">
											// 					<span style="gap:15px;display:flex;"><b>Estado:</b> '.$prods['prod_estado'].'</span>
											// 				</div>';
											// }else{
											// 	$return .= '<div class="info_detalhes">
											// 					<span style="gap:15px;display:flex;"><b>Estado:</b> A+</span>
											// 				</div>';
											// }

											if($prods['prod_estado'] == 'SWAP'){
												$return .= '<div class="info_detalhes">
																<span style="display:block;"><b>Grau: </b><b style="color:#EC870E;">'.$prods['prod_estado'].'</b></span>
																<span style="margin-top:5px;display:block;font-size:14px"><b> Selado</b></span>
																<span style="margin-top:5px;display:block;font-size:12px">O telefone está selado, não foi aberto.</span>
															</div>';
											}elseif($prods['prod_estado'] == 'A+'){
												$return .= '<div class="info_detalhes">
																<span style="display:block;"><b>Grau: </b><b style="color:#EC870E;">'.$prods['prod_estado'].'</b></span>
																<span style="margin-top:5px;display:block;font-size:14px"><b> Como novo</b></span>
																<span style="margin-top:5px;display:block;font-size:12px">O telefone encontra-se igual a novo, sem marcas ou riscos.</span>
															</div>';
											}elseif($prods['prod_estado'] == 'A'){
												$return .= '<div class="info_detalhes">
																<span style="display:block;"><b>Grau: </b><b style="color:#EC870E;">'.$prods['prod_estado'].'</b></span>
																<span style="margin-top:5px;display:block;font-size:14px"><b> Muito bom</b></span>
																<span style="margin-top:5px;display:block;font-size:12px">O telefone pode apresentar marcas ligeiras, pouco visíveis, tais como pequenos riscos, no entanto nunca marcas muito perceptíveis.</span>
															</div>';
											}elseif($prods['prod_estado'] == 'B'){
												$return .= '<div class="info_detalhes">
																<span style="display:block;"><b>Grau: </b><b style="color:#EC870E;">'.$prods['prod_estado'].'</b></span>
																<span style="margin-top:5px;display:block;font-size:14px"><b> Bom</b></span>
																<span style="margin-top:5px;display:block;font-size:12px">Apresenta marcas visíveis, tais como riscos, amolgadelas e outras marcas.</span>
															</div>';
											}elseif($prods['prod_estado'] == 'C+'){
												// $return .= '<div class="info_detalhes">
												// 				<span style="display:block;"><b>Grau: </b><b style="color:#EC870E;">'.$prods['prod_estado'].'</b></span>
												// 				<span style="margin-top:5px;display:block;font-size:14px"><b> C+</b></span>
												// 			</div>';
												$return .= '<div class="info_detalhes">
																<span style="display:block;"><b>Grau: </b><b style="color:#EC870E;">C</b></span>
																<span style="margin-top:5px;display:block;font-size:14px"><b> Satisfatório</b></span>
																<span style="margin-top:5px;display:block;font-size:12px">O telefone apresenta marcas muito visíveis, tais como riscos muito acentuados e amolgadelas profundas.</span>
															</div>';
											}elseif($prods['prod_estado'] == 'C'){
												$return .= '<div class="info_detalhes">
																<span style="display:block;"><b>Grau: </b><b style="color:#EC870E;">'.$prods['prod_estado'].'</b></span>
																<span style="margin-top:5px;display:block;font-size:14px"><b> Satisfatório</b></span>
																<span style="margin-top:5px;display:block;font-size:12px">O telefone apresenta marcas muito visíveis, tais como riscos muito acentuados e amolgadelas profundas.</span>
															</div>';
											}elseif($prods['prod_estado'] == 'N'){
												// $return .= '<div class="info_detalhes">
												// 				<span style="display:block;"><b>Grau: </b><b style="color:#EC870E;">'.$prods['prod_estado'].'</b></span>
												// 				<span style="margin-top:5px;display:block;font-size:14px"><b> N</b></span>
												// 			</div>';
												$return .= '<div class="info_detalhes">
																<span style="display:block;"><b>Grau: </b><b style="color:#EC870E;">SWAP</b></span>
																<span style="margin-top:5px;display:block;font-size:14px"><b> Selado</b></span>
																<span style="margin-top:5px;display:block;font-size:12px">O telefone está selado, não foi aberto.</span>
															</div>';
											}
											else{
												// $return .= '<div class="info_detalhes">
												// 				<span style="display:block;"><b>Grau: </b><b style="color:#EC870E;">SWAP</b></span>
												// 				<span style="margin-top:5px;display:block;font-size:14px"><b> Como novo</b></span>
												// 				<span style="margin-top:5px;display:block;font-size:12px">O telefone encontra-se igual a novo, sem marcas ou riscos.</span>
												// 			</div>';
												$return .= '<div class="info_detalhes">
															<span style="display:block;"><b>Grau: </b><b style="color:#EC870E;">SWAP</b></span>
															<span style="margin-top:5px;display:block;font-size:14px"><b> Selado</b></span>
															<span style="margin-top:5px;display:block;font-size:12px">O telefone está selado, não foi aberto.</span>
														</div>';
											}
			
										$return .= '</div>';
									$return .= '</div>';
								}	
							}	

							$return .= '<div class="clear"></div>';
						$return .='</div>';

						$wish='';
						$res=0;

					$return .=' </div>';
				}
				
				if($reparacoes == 0)
				{
					$entrega='';
					
					$loja_txt = 'Loja da Maia';

					if($prods['obj_Supplier_ID']=='3')$loja_txt='Disponível Online';//Service soft:
					//if($prods['obj_Supplier_ID']=='4')$loja_txt='Disponível Online';//fd: --> apenas para obter fotos
					if($prods['obj_Supplier_ID']=='5')$loja_txt='Disponível Online';//wdmi:
					
					$return .= '<div>';
					$return .= '<div class="localizacao_fisica">';
					$return .= '<h4><strong>STOCK:</strong></h4>';

					$stock_txt='';
					
					if($precos['STOCK'] > 0)
					{
						$return .= '<span> <img src="/layout/dotbright/images/stock_on.svg"> <strong>'.$loja_txt.'</strong></span>';
					}
					else
					{
						$return .= '<span> <img src="/layout/dotbright/images/stock_off.svg"> <strong>(Disponivel Brevemente) </strong></span>';
					}
			
					/*
					if($precos['STOCK'] >= 3) $stock_txt='<span class="disponivel" title="Disponibilidade Total" > <img src="layout/'.PROJETO.'/images/status.png" alt="Disponibilidade Total" title="Disponibilidade Total"> Em stock</span>';
					elseif($precos['STOCK'] == 2) $stock_txt='<span class="limitado" title="Disponibilidade Reduzida"> <img src="layout/'.PROJETO.'/images/status_away.png" alt="Disponibilidade Reduzida" title="Disponibilidade Reduzida"> Stock Reduzido</span>';
					elseif($precos['STOCK'] == 1) $stock_txt='<span class="porencomenda" title="Disponivel por Encomenda"> <img src="layout/'.PROJETO.'/images/status_blue.png" alt="Disponivel Brevemente" title="Disponivel Brevemente"> Por Encomenda</span>';
					else $stock_txt='<span class="esgotado" title="Disponivel Brevemente"> <img src="layout/'.PROJETO.'/images/status_busy.png" alt="Disponivel por Encomenda" title="Disponivel por Encomenda"> Disponível Brevemente</span>';
					*/


					$return .= '</div>';
					$return .=' </div>';
					//$return .= '<br><div class="clear"></div><a class="fancybox2" data-fancybox-type="iframe" href="https://gpt.memogadget.com/?k=dot&n='.$prods['prod_nome'].'"><img style="width:100px" src="layout/AI_info.svg"></a>';
				}
				
				if($_GET['floa']==1)
				{
					$return .= layoutFloa($pvp_com_iva);	
				}
				
				$txt_iva = "";

			$return .='</div>';
		$return .='</div>';

	$return .='</div>';

	

	$return .='<div class="clear"></div> ';
	$return .='<br>';


	if($prods['prod_id'] != 0)
	{
		$rs=new DataObj;	
		$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);			
		$sql = "SELECT * FROM products_lang WHERE ID_PAI= ".$prods['prod_id'];
		$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);

		$rs2=new DataObj;	
		$sql2 = 'SELECT pa.bloco_id, pa.bloco_tipo, pal.bloco_titulo, pp.bloco_id as ppbl, pp.Outros, pp.Outros_on,pp.ordem
				FROM personalizacao pa 
				INNER JOIN personalizacao_lang pal ON pal.bloco_id=pa.bloco_id
				LEFT JOIN products_personalizacao2 pp ON pp.bloco_id = pa.bloco_id AND prod_id="'.$prods['prod_id'].'" 
				WHERE pal.bloco_lang="'.$GLOBALS['admin_lang'].'" AND pa.bloco_id_pai="0" AND  pp.Outros_on=1
				GROUP BY pa.bloco_id
				ORDER BY pp.ordem=0,pp.ordem, pa.bloco_ordem=0, pa.bloco_ordem,pp.ordem,pa.bloco_id';
		$rs2->db_open($db_link,$GLOBALS['DB_name'],$sql2);

		if(!$rs->EOF())
		{
			$prod_nome=htmlentities_memo(stripslashes($rs->db_data['Cabecalho']));
			$prod_subtitulo=htmlentities_memo(stripslashes($rs->db_data['Subtitulo']));
			$prod_descricao=converterPontoParaTraco(limpa_editor_lixo(stripslashes(strlen($rs->db_data['Texto1']) > 300 ? html_cut($rs->db_data['Texto1'],297)."... " :$rs->db_data['Texto1'])));
			$prod_descricao2=converterPontoParaTraco($rs->db_data['Texto1']);
			$prod_features=converterPontoParaTraco($rs->db_data['Texto1']);
			$prod_tempo_entrega=converterPontoParaTraco($rs->db_data['tempo_entrega']);
			$prod_garantia=limpa_editor_lixo(stripslashes($rs->db_data['Tamanho']));
			$prod_como_funciona=converterPontoParaTraco($rs->db_data['Texto3']);
			$prod_envio=converterPontoParaTraco($rs->db_data['Texto4']);
			$prod_customizado_titulo=htmlentities_memo(stripslashes($rs->db_data['Cabecalho_Texto5']));
			$prod_customizado=converterPontoParaTraco($rs->db_data['Texto5']);

			$atributos = '';
			if (rtrim(strip_tags($prods['prod_como_funciona'])) != "")
			{
				$atributos = get_personalizacao_produto($prods['prod_id']);				
			}
			else
			{
				$atributos = get_personalizacao_produto($prods['prod_id']);				
			}
			



			if($prod_descricao !='' || $prod_como_funciona != '' || $prod_envio != '' || $prod_customizado != '' || $atributos)
			{
				$return .='<div class="row">';
					$return .='<div class="col-md-12">';
						$return .= '<div class="horizontalTab">';
							$return .= '<ul class="resp-tabs-list">';
								$return .= ''.($prod_descricao!='' ? '<li>'.getLinguas('txt_descricao',$GLOBALS['site_lang']).'</li>' :'').'';
								$return .= ''.($prod_como_funciona!='' ? '<li style="text-transform:uppercase;">'.getLinguas('key_detalhes',$GLOBALS['site_lang']).'</li>' :'').'';
								$return .= ''.($prod_envio!='' ? '<li>'.getLinguas('key_ficha_tecnica',$GLOBALS['site_lang']).'</li>' :'').'';
								$return .= ''.($prods['prod_ee'] != '' || $prods['prod_ue'] != '' || $prods['prod_dt'] != '' ? '<li>'.getLinguas('key_outros_fxs',$GLOBALS['site_lang']).'</li>' :'').'';
								$return .= ''.($prod_customizado != '' ? '<li>'.$prod_customizado_titulo.'</li>' :'').'';

								$return .= ''.(!$rs2->EOF() ? '<li>ESPECIFICAÇÕES</li>' :'').'';
							$return .= '</ul>';
							$return .= '<div class="resp-tabs-container">';
								
								$return .= ''.($prod_descricao!='' ? '<div><p>'.$prods['prod_descricao'].'</p></div>' :'').'';
								$return .= ''.($prod_como_funciona!='' ? '<div><p>'.text_smart($prod_como_funciona).'</p></div>' :'').'';
								$return .= ''.($prod_envio!='' ? '<div><p>'.$prod_envio.'</p></div>' :'').'';
								$return .= ''.($prods['prod_ee'] != '' || $prods['prod_ue'] != ''  || $prods['prod_dt'] != '' ? '<div><p>' :'').'';
								$return .= ''.($prods['prod_ee'] != '' ? '<a style="color:#666;text-decoration:none;text-transform:uppercase;font-weight:bold" href="'.$prods['prod_ee'].'" target="_blank">&#9658;&nbsp;&nbsp;'.getLinguas('key_etiqueta_energ',$GLOBALS['site_lang']).'</a><br>' :'').'';
								$return .= ''.($prods['prod_ue'] != '' ? '<a style="color:#666;text-decoration:none;text-transform:uppercase;font-weight:bold" href="'.$prods['prod_ue'].'" target="_blank">&#9658;&nbsp;&nbsp;'.getLinguas('key_fx_produto_ue',$GLOBALS['site_lang']).'</a><br>' :'').'';
								$return .= ''.($prods['prod_dt'] != '' ? '<a style="color:#666;text-decoration:none;text-transform:uppercase;font-weight:bold" href="'.$prods['prod_dt'].'" target="_blank">&#9658;&nbsp;&nbsp;'.getLinguas('key_desenho_tecnico',$GLOBALS['site_lang']).'</a>' :'').'';
								$return .= ''.($prods['prod_ee'] != '' || $prods['prod_ue'] != '' || $prods['prod_dt'] != '' ? '</p></div>' :'').'';
								$return .= ''.($prod_customizado!='' ? '<div><p>'.nl2br(strip_html_tags_fp($prod_customizado)).'</p></div>' :'').'';

								$return .= ''.($atributos!='' ? '<div><p>'.$atributos.'</p></div>' :'').'';



								// $return .='<div class="row">';
								// $rowCount = 0;
								// 	while(!$rs2->EOF()){
								// 		$backgroundColor = ($rowCount % 2 == 0) ? '#f7f7f7' :'';// Change to your desired color

								// 		$return .='<div class="row" style="margin-bottom:5px;background-color:'.$backgroundColor.'">';
								// 			$return .='<div class="col-md-3" style="font-weight:bold;font-size:13px;">'.$rs2->db_data['bloco_titulo'].'<br> <span style="font-size:11px"></span></div>';
								// 			$return .='<div class="col-md-9" style="font-size:13px;">'.$rs2->db_data['Outros'].'<br> <span style="font-size:11px"></span></div>';
								// 		$return .='</div>';
								// 		$rowCount++;
								// 		$rs2->db_next();
								// 	}
								// $return .= '</div>';

							$return .= '</div>';
						$return .= '</div>';
					$return .='</div>';
				$return .='</div> ';
			}
				
	
			// if(!$rs2->EOF()){
			// 	$return .='<div class="clear"></div> <br> <br>';
			// 	$return .='<div class="row">';
			// 	$return .='<h2 style="padding:20px 0;border-bottom:1px solid #ccc;width:100%;">ESPECIFICAÇÕES</h2>';
			// 	$return .='</div>';
			
			// 	while(!$rs2->EOF()){
			// 		$return .='<div class="row" style="margin-bottom:5px">';
			// 			$return .='<div class="col-md-3" style="font-weight:bold;font-size:13px;">'.$rs2->db_data['bloco_titulo'].'<br> <span style="font-size:11px"></span></div>';
			// 			$return .='<div class="col-md-9" style="font-size:13px;">'.$rs2->db_data['Outros'].'<br> <span style="font-size:11px"></span></div>';
			// 		$return .='</div>';
			// 		$rs2->db_next();
			// 	}
			// }
		}
	}

	$return .= '<br><br><br>';

	// $return .='<div class="row">';
	// 	$return .='<div class="col-md-12">';
	// 		$descricao_verifica = strip_html_tags($prods['prod_descricao']);
	// 		$detalhe_verifica = strip_html_tags($prods['prod_como_funciona']);
	// 		$ficha_verifica = strip_html_tags($prods['prod_envio']);
	// 		$prod_video = $prods['prod_video'];
	// 		$customizado_titulo= strip_html_tags($prods['prod_customizado_titulo']);
	// 		$customizado= strip_html_tags($prods['prod_customizado']);
	// 		$script=str_replace(array('http://youtu.be/', 'https://youtu.be/', 'http://www.youtube.com/watch?v=', 'https://www.youtube.com/watch?v='), '//www.youtube.com/embed/',$prod_video);

	// 		if($descricao_verifica !='' || $detalhe_verifica != '' || $ficha_verifica != '' || $prod_video != '' || $customizado != '')
	// 		{ 
	// 			$return .= '<div class="horizontalTab">';
	// 				$return .= '<ul class="resp-tabs-list">';
	// 					$return .= ''.($descricao_verifica!='' ? '<li>'.getLinguas('txt_descricao',$GLOBALS['site_lang']).'</li>' :'').'';
	// 					$return .= ''.($detalhe_verifica!='' ? '<li>'.getLinguas('key_detalhes',$GLOBALS['site_lang']).'</li>' :'').'';
	// 					$return .= ''.($ficha_verifica!='' ? '<li>'.getLinguas('key_ficha_tecnica',$GLOBALS['site_lang']).'</li>' :'').'';
	// 					$return .= ''.($prods['prod_ee'] != '' || $prods['prod_ue'] != '' || $prods['prod_dt'] != '' ? '<li>'.getLinguas('key_outros_fxs',$GLOBALS['site_lang']).'</li>' :'').'';
	// 					$return .= ''.($prod_video != '' ? '<li>'.getLinguas('key_video',$GLOBALS['site_lang']).'</li>' :'').'';
	// 					$return .= ''.($customizado != '' ? '<li>'.$customizado_titulo.'</li>' :'').'';
	// 				$return .= '</ul>';
	// 				$return .= '<div class="resp-tabs-container">';
	// 					$return .= ''.($descricao_verifica!='' ? '<div><p>'.text_smart($prods['prod_descricao']).'</p></div>' :'').'';
	// 					$return .= ''.($detalhe_verifica!='' ? '<div><p>'.text_smart($prods['prod_como_funciona']).'</p></div>' :'').'';
	// 					$return .= ''.($ficha_verifica!='' ? '<div><p>'.$prods['prod_envio'].'</p></div>' :'').'';
	// 					$return .= ''.($prods['prod_ee'] != '' || $prods['prod_ue'] != ''  || $prods['prod_dt'] != '' ? '<div><p>' :'').'';
	// 					$return .= ''.($prods['prod_ee'] != '' ? '<a style="color:#666;text-decoration:none;text-transform:uppercase;font-weight:bold" href="'.$prods['prod_ee'].'" target="_blank">&#9658;&nbsp;&nbsp;'.getLinguas('key_etiqueta_energ',$GLOBALS['site_lang']).'</a><br>' :'').'';
	// 					$return .= ''.($prods['prod_ue'] != '' ? '<a style="color:#666;text-decoration:none;text-transform:uppercase;font-weight:bold" href="'.$prods['prod_ue'].'" target="_blank">&#9658;&nbsp;&nbsp;'.getLinguas('key_fx_produto_ue',$GLOBALS['site_lang']).'</a><br>' :'').'';
	// 					$return .= ''.($prods['prod_dt'] != '' ? '<a style="color:#666;text-decoration:none;text-transform:uppercase;font-weight:bold" href="'.$prods['prod_dt'].'" target="_blank">&#9658;&nbsp;&nbsp;'.getLinguas('key_desenho_tecnico',$GLOBALS['site_lang']).'</a>' :'').'';
	// 					$return .= ''.($prods['prod_ee'] != '' || $prods['prod_ue'] != '' || $prods['prod_dt'] != '' ? '</p></div>' :'').'';
	// 					$return .= ''.($prod_video!='' ? '<div><p><iframe width="100%" height="450" src="'.$script.'" frameborder="0" allowfullscreen></iframe></p></div>' :'').'';
	// 					$return .= ''.($customizado!='' ? '<div><p>'.nl2br(strip_html_tags_fp($customizado)).'</p></div>' :'').'';
	// 				$return .= '</div>';
	// 			$return .= '</div>';
	// 		}
	// 	$return .='</div>';
	// $return .='</div> ';

    $return.= '<script src="layout/'.PROJETO.'/vendor/swiper/swiper-bundle.min.js"></script> ';

    $return .= '
		<script>
			if ($ && $.fn.zoom) {
				$("#product-image_sw").zoom();
			}
				
			let swiper = new Swiper(".mySwiper", {
				spaceBetween:10,
				slidesPerView:4,
				freeMode:true,
				watchSlidesProgress:true,                         
			});
			
			let swiper2 = new Swiper(".mySwiper2", {
				spaceBetween:10,
				navigation:{
				nextEl:".swiper-button-next",
				prevEl:".swiper-button-prev",
				},
				thumbs:{
				swiper:swiper,
				},
			});
			
			var swiperSlide = document.getElementsByClassName(\'swiper-slide\')
			for(var index = 0;index< swiperSlide.length;index++){
				swiperSlide[index].addEventListener(\'mouseover\',function(e){
					swiper2.zoom.in();
				})
				swiperSlide[index].addEventListener(\'mouseout\',function(e){
					swiper2.zoom.out();
				})
			}                    
		</script>
	';
    
	return $return;
}


function mostra_fotos_familia($FAM_ID=0)
{
	if($FAM_ID==0)return '';

	$return='';
	$FAM_ID_PAI=0;

	$rs=new DataObj;
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);

	$sql='SELECT ID,ID_ORG,img_ext FROM products_families WHERE ID='.$FAM_ID;
    $rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
    if(!$rs->EOF())
    {
    	$FAM_ID=$rs->db_data['ID'];
    	$FAM_ID_PAI=$rs->db_data['ID_ORG'];
    	$img_ext=$rs->db_data['img_ext'];
    	if($img_ext != '') $return = '<img style="width:50px" src="'.$GLOBALS['site_http_url'].'files/products_families/'.$FAM_ID.'_1.'.$img_ext.'">';
    }
    $rs->db_close();

    return mostra_fotos_familia($FAM_ID_PAI).$return;

}

function ficha_p3($rep){
    if($rep == 0) {

        $return = ' <div class="row">
                       <div class="col-md-2">
                        
                       </div>
                       <div class="col-md-10" >
                            <div class="widget widget-icon-box mb-6" style="background:#FFF;position:relative;z-index:2;justify-content:left;">
                                <div class="col-xs-4 col-md-12 icon-box icon-box-side" style="display:grid;justify-content:left;">
                                   <img style="width:100%;max-width:310px;height:70px;" src="layout/dotbright/images/cadeado.svg" alt="compra segura" border="0">
                                </div>
                                <div class="col-xs-4 col-md-12 icon-box icon-box-side" style="display:grid;justify-content:left;">
                                   <img style="width:100%;max-width:310px;height:70px;" src="layout/dotbright/images/garantia24meses2.svg" alt="3 Anos de Garantia" border="0">
                                </div>
                                <div class="col-xs-4 col-md-12 icon-box icon-box-side" style="display:grid;justify-content:left;">
                                    <img style="width:100%;max-width:310px;height:70px;" src="layout/dotbright/images/envios_rapidos.svg" alt="envio gratis" border="0">
                                </div>
                            </div>
                       </div>
                    </div>
             ';
    }else{
        $return = ' 
 
            <div class="widget widget-icon-box mb-6" style="background:#FFF;position:relative;z-index:2">
                <div class="col-xs-4 col-md-12 icon-box icon-box-side">
                   <img style="width:100%;max-width:310px;height:65px;" src="layout/dotbright/images/tecnicos.svg" alt="compra segura" border="0">
		        </div>
                <div class="col-xs-4 col-md-12 icon-box icon-box-side">
                   <img style="width:100%;max-width:310px;height:65px;" src="layout/dotbright/images/lab_certificados.svg" alt="3 Anos de Garantia" border="0">
                </div>
                <div class="col-xs-4 col-md-12 icon-box icon-box-side">
                    <img style="width:100%;max-width:310px;height:65px;" src="layout/dotbright/images/pecas_originais.svg" alt="envio gratis" border="0">
                </div>
            </div> ';


    }
return $return ;

}

function prod_mais_vendido($fam)
{
    $return = '';
    $i = 0;
    global $DB_host,$DB_user,$DB_pass, $DB_name, $site_lang;
    $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new DataObj;

    if($fam == '163')
    {
    	//Semi novos
    	$sql='SELECT pfo.ID_Prod as idprod,CONCAT(p.EAN,p.estado) AS uniq_ref 
    	FROM products p
	    INNER JOIN products_families_obj pfo ON p.ID=pfo.ID_Prod
		INNER JOIN products_objs po ON po.obj_type_id=pfo.ID_Prod
		WHERE pfo.ID_ORG='.$fam.' AND pfo.ID_Prod!='.$_GET['prod_id'].' AND p.Aproved=1 AND po.obj_val2=0
		GROUP BY uniq_ref
		ORDER BY po.obj_pvp ASC 
		LIMIT 10';

		/*
		$sql='SELECT products_objs.*,CONCAT(P.EAN,P.estado) AS uniq_ref,Sync,Cabecalho,Variante,estado,bateria,Ref,u_catetiq,u_coretiq,u_imagetiq,P.ID_Marca,P.Ordem as Ordem2,FPO.ID_ORG as ID_Fam,obj_Supplier_ID,fornecedor_importacao
		FROM products_objs
		INNER JOIN products_families_obj FPO on FPO.ID_PROD=products_objs.obj_type_id
		INNER JOIN products_lang PL on PL.ID_PAI=products_objs.obj_type_id
		INNER JOIN products P on P.ID=products_objs.obj_type_id
		WHERE FPO.ID_ORG IN ('.$fam.') AND FPO.ID_Prod!='.$_GET['prod_id'].' AND PL.Lang="pt" AND P.Aproved=1 AND products_objs.obj_val2=0 AND products_objs.obj_stock>0  AND (obj_val2=0 OR obj_val2 IS NULL) GROUP BY uniq_ref ORDER BY P.bateria DESC,products_objs.obj_pvp DESC';
		*/

    }
    else
    {
	    $sql='SELECT pfo.ID_Prod as idprod FROM products p
	    INNER JOIN products_families_obj pfo ON p.ID=pfo.ID_Prod
		INNER JOIN products_objs po ON po.obj_type_id=pfo.ID_Prod
		WHERE pfo.ID_ORG='.$fam.' AND pfo.ID_Prod!='.$_GET['prod_id'].' AND p.Aproved=1 AND po.obj_stock > 0 GROUP BY pfo.ID_Prod ORDER BY po.obj_pvp ASC LIMIT 10';
    }

    $rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
    if(!$rs->EOF())
    {
    	$return .= '<div id="w_prod" class="widget widget-products" style="height:370px">
                <div class="title-link-wrapper mb-2">
                    <h4 class="title title-link font-weight-bold" style="margin-top:0px">Outras Sugestões</h4>
                </div>
                <div class="swiper-container">
                <div class="swiper mySwiper3" style="position:relative;z-index:1">
                <div class="swiper-wrapper">';

	    while(!$rs->EOF())
	    {
	        $prods2=get_produtc_info($rs->db_data['idprod']);
	        $link=$site_http_url.\seo2(2,$GLOBALS['products_page_id'],$prods2,$GLOBALS['site_lang']);
	        $prod2['id']=$prods2['prod_id'];
	        $prod2['title']=$prods2['prod_nome'];
	        $prod2['caminho']=$link;
	        $prod2['image']=$prods2['prd_imgm_med'];
	        $prod2['subtitle']=$prods2['prod_subtitulo'];
	        $prod2['ref']=$prods2['prod_ref'];
	        $prod2['carrinho']=$prods2['prod_carrinho'];
	        $prod2['price']=$prods2['prod_precos']['PVP'];
	        $prod2['price_riscado']=$prods2['prod_precos']['PRECO_ANTIGO'];
	        $prod2['stock']=$prods2['prod_precos']['STOCK'];
	        $prod2['promo']=$prods2['prod_precos']['PROMOCAO'];
	        $prod2['destaque']=$prods2['prod_precos']['DESTAQUE'];
	        $prod2['novo']=$prods2['prod_precos']['NOVO'];
	        $prod2['outlet']=$prods2['prod_precos']['OUTLET'];
	        $prod2['u_c']=$prods2['u_catetiq'];
	        $prod2['u_i']=$prods2['u_imagetiq'];
	        $prod2['u_co']=$prods2['u_coretiq'];
	        if($prod2['image']!='')
	        {	        	
	        	$return .= '<div class="swiper-slide slide_'.$i.'" onclick="window.location.href = \''.$prod2['caminho'].'\'" style="cursor:pointer">
	                        <div class="product product-widget "> 
	                            <div style="width:120px;display:flex;height:auto;overflow:hidden;padding:10px"><img style="width:100%" src="'.$prod2['image'].'" alt="'.rtrim( $prod2['title'], '-').'" border="0"></div>
	                            <div class="product-details_r">
	                                <h5 class="product-name"><a href="#">'.$prod2['title'].'</a></h5> 
	                             <div class="product-price1">'. numberformat($prod2['price'], 1) .'</div>
	                                
	                             </div>
	                        </div> 
	                     </div> ';
	        }
	        $i++;

	        $rs->db_next();
	    }
    $tam=$rs->db_rows;
    $rs->db_close();
    $return .= '   </div>
                    </div>
                       <div class="swiper-pagination" style="display:none"></div>
                   </div>
        </div> ';

	$return .= '<div class="clear"></div>';
	}
    
	$return .= ''; // Manual suggestions: storefront-ux.js supplies accessible controls.
	/*
 <div class="widget widget-products">
                <div class="title-link-wrapper mb-2">
                    <h4 class="title title-link font-weight-bold">Recomendado para Si</h4>
                </div>
                <div class="swiper mySwiper3">
                       <div class="swiper-wrapper">
                         <div class="swiper-slide slide_1">Slide 1</div>
                         <div class="swiper-slide slide_2">Slide 2</div>
                         <div class="swiper-slide slide_3">Slide 3</div>
                         <div class="swiper-slide slide_4">Slide 4</div>
                         <div class="swiper-slide slide_5">Slide 5</div>
                       </div>
                       <div class="swiper-pagination"></div>
                   </div>
        </div>
 	* */


	return $return;
}
function prod_mais_vendido_reparacoes($fam)
{
    
    $i = 0;

    global $DB_host,$DB_user,$DB_pass, $DB_name, $site_lang;
    $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new DataObj;

    //$sql='SELECT p.ID FROM products p INNER JOIN products_families_obj pfo on p.ID=pfo.ID_Prod WHERE pfo.ID_ORG='.$prods['fam_id'].' AND p.Aproved=1 AND p.Aproved>=1 group by pfo.ID_Prod Limit 0,10';
    $sql='SELECT pfo.ID_Prod as idprod FROM products p
							INNER JOIN products_families_obj pfo ON p.ID=pfo.ID_Prod
							INNER JOIN products_objs po ON po.obj_type_id=pfo.ID_Prod
							WHERE pfo.ID_ORG='.$fam/*$prods['fam_id']*/.' AND pfo.ID_Prod!='.$_GET['prod_id'].' AND p.Aproved=1  GROUP BY pfo.ID_Prod ORDER BY po.obj_pvp ASC LIMIT 10 ';
    $rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
    if(!$rs->EOF())
    {
    	$return = ' <div id="w_prod" class="widget widget-products" style="height:370px">
                <div class="title-link-wrapper mb-2">
                    <h4 class="title title-link font-weight-bold" style="margin-top:0px">Outras Sugestões</h4>
                </div>
                <div class="swiper-container">
                <div class="swiper mySwiper3" style="position:relative;z-index:1">
                <div class="swiper-wrapper">';


	    while(!$rs->EOF())
	    {
	        $prods2=get_produtc_info($rs->db_data['idprod']);

	        $link=$site_http_url.\seo2(2,$GLOBALS['products_page_id'],$prods2,$GLOBALS['site_lang']);

	        $prod2['id']=$prods2['prod_id'];
	        $prod2['title']=$prods2['prod_nome'];
	        $prod2['caminho']=$link;
	        $prod2['image']=$prods2['prd_imgm_med'];
	        $prod2['subtitle']=$prods2['prod_subtitulo'];
	        $prod2['ref']=$prods2['prod_ref'];
	        $prod2['carrinho']=$prods2['prod_carrinho'];

	        $prod2['price']=$prods2['prod_precos']['PVP'];
	        $prod2['price_riscado']=$prods2['prod_precos']['PRECO_ANTIGO'];
	        $prod2['stock']=$prods2['prod_precos']['STOCK'];
	        $prod2['promo']=$prods2['prod_precos']['PROMOCAO'];

	        $prod2['destaque']=$prods2['prod_precos']['DESTAQUE'];
	        $prod2['novo']=$prods2['prod_precos']['NOVO'];
	        $prod2['outlet']=$prods2['prod_precos']['OUTLET'];

	        $prod2['u_c']=$prods2['u_catetiq'];
	        $prod2['u_i']=$prods2['u_imagetiq'];
	        $prod2['u_co']=$prods2['u_coretiq'];


			if($prod2['image']!='')
			{				
	        	$return .= '  <div class="swiper-slide slide_'.$i.'" onclick="window.location.href = \''.$prod2['caminho'].'\'" style="cursor:pointer;">
	                        <div class="product product-widget "> 
	                            <div style="width:120px;display:flex;height:auto;overflow:hidden;padding:10px"><img style="width:100%"  src="'.$prod2['image'].'" alt="'.rtrim( $prod2['title'], '-').'" border="0"></div>
	                            <div class="product-details_r">
	                                <h5 class="product-name"><a href="#">'.$prod2['title'].'</a></h5> 
	                             <div class="product-price1">'. numberformat($prod2['price'], 1) .'</div>
	                                
	                             </div>
	                        </div> 
	                     </div> ';
			}
	        $i++;

	        $rs->db_next();
	    }
	    $tam=$rs->db_rows;
	    $rs->db_close();
	    $return .= '   </div>
	                    </div>
	                       <div class="swiper-pagination" style="display:none"></div>
	                   </div>
	        </div> ';
		$return .= '<div class="clear"></div>';
	}
    
	$return .= ''; // Manual suggestions: storefront-ux.js supplies accessible controls.
	return $return;
}
function bateria($perc , $prod_estado){
    $bateria = str_replace('%', '', $perc);
	
	if($bateria == ''){
		$bateria = '100';
	}

    $return = '<div class="product-stock row"> ';
		$return .= '<div class="col-md-12 col-xs-12" style="padding-bottom:10px;border:1px solid #eee;border-bottom:none;">';

			$return.= '<script src="layout/'.PROJETO.'/js/radial-progress-bar.js"></script> ';

			$return .= '<div class="bateria" style="text-align:center;">';
				$return .= '<h4><strong>BATERIA</strong></h4>';
				$return .= '<div id="pie" style="width:100px;"></div>';
			$return .= '</div>';

			$return.= '
				<script>
					jQuery("#pie").radialPieChart("init", {
						\'font-size\':20,
						\'text-color\':\'#ec870e\',
						\'fill\':20,
						\'data\':[
							{\'color\':"#0fa200", \'perc\':"'.$bateria.'"},
							
						]
					});
				</script>
			';

		$return .= '</div>';
		$return .= '<div class="col-md-12 col-xs-12" style="padding:10px 0;border:1px solid #eee;">';
			if($prod_estado != ''){
				$return .= '<div class="grade" style="padding-left:20px;text-align:center;">';
					$return .= '<h4><strong>GRADE</strong></h4>';
					// $return .= '<br>';
					$return .= '<span style="border:1px solid;border-radius:5px;padding:5px 25px;font-weight:900;font-size:15px;">GRADE '.$prod_estado.'</span>';

					if($prod_estado == 'SWAP'){
						$return .= '<br><br>';
						$return .= '<span style="font-size:12px;"><strong>  (SWAP:</strong> O telefone está selado, não foi aberto.)</span>';
					}
					elseif ($prod_estado == 'A+') {
						$return .= '<br><br>';
						$return .= '<span style="font-size:12px;"><strong>  (A+:</strong> Como novo:O telefone encontra-se igual a novo, sem marcas ou riscos.)</span>';
					}
					elseif ($prod_estado== 'A') {
						$return .= '<br><br>';
						$return .= '<span style="font-size:12px;"><strong>  (A:</strong>  Muito bom:O telefone pode apresentar marcas ligeiras, pouco visíveis, tais como pequenos riscos, no entanto nunca marcas muito perceptíveis.)</span>';
					}
					elseif ($prod_estado == 'B') {
						$return .= '<br><br>';
						$return .= '<span style="font-size:12px;"><strong>  (B:</strong> Bom:Apresenta marcas visíveis, tais como riscos, amolgadelas e outras marcas.)</span>';
					}
					elseif ($prod_estado == 'C+') {
						$return .= '<br><br>';
						$return .= '<span style="font-size:12px;"><strong>  (C+:</strong> ...)</span>';
					}
					elseif ($prod_estado == 'C') {
						$return .= '<br><br>';
						$return .= '<span style="font-size:12px;"><strong>  (C:</strong> Satisfatório:O telefone apresenta marcas muito visíveis, tais como riscos muito acentuados e amolgadelas profundas.)</span>';
					}
					elseif ($prod_estado == 'N') {
						$return .= '<br><br>';
						$return .= '<span style="font-size:12px;"><strong>  (N:</strong> ...)</span>';
					}
				$return .= '</div>';
			}
		$return .= '</div>';
	$return .= '</div>';

    return $return;
}
function ficha_produto____($prods)
{

	$new_prods = verifica_promo($prods['prod_id'], $prods['prod_precos']);
	if(!empty($new_prods))
	{
		$prods = $new_prods;
	}
	if(isset($_GET['amp']) && intval($_GET['amp']) == 1) return ficha_produto_amp($prods);

	$not_produto_standard=$prods['produto_standard'];
	$c_submissao_ficheiro=$prods['c_submissao_ficheiro'];

	if($not_produto_standard==1)return ficha_produto_nstandard($prods);

	global $tem_produtos_visualizados;
    $dados_cliente=cliente_logado();


    $P_CONF=unserialize(PROD_CONF);

    if($tem_produtos_visualizados)
    {
	    ?>

	    <script>
		$(document).ready(function()
		{
			$.ajax({
	        type:"POST",
	        url:SITE_HTTP_AJAX_URL + "addRemoveProductVisualizados.php",
	        data:{action:"add", prodid:<?php echo $prods['prod_id'];?>}
		    }).done(function (resultado) {


		    });
		});

	    </script>

	    <?php
	}

	$return='<link rel="stylesheet" href="layout/'.PROJETO.'/css/css_external/product_v_'.filemtime('layout/'.PROJETO.'/css/css_external/product.css').'.css" media="all"/> ';

	$return .= '<script>quantityAvailable=9999;</script>';
	$return .= '<script type="text/javascript" src="layout/'.PROJETO.'/css/css_external/product.js"></script> ';
    $return .= BREADCRUMBS(1, intval($prods['fam_id']));


	$tem_qtt=0;
	$label_promo='';
	$precos=$prods['prod_precos'];

	$PODE_ENCOMENDAR=$precos['PODE_ENCOMENDAR'];

	$today = date("U");

	if ((($precos['STOCK'] > 0 || $PODE_ENCOMENDAR == 1) && $prods['prod_precos']['PVP'] > 0 && $prods['prod_carrinho'] == 1))
	{
		if ($prods['prod_precos']['PROMOCAO'] == 1 && (strtotime($prods['prod_precos']['Promo_data_ini']) <= $today && strtotime($prods['prod_precos']['Promo_data_fim']) >=$today))
		{
			//$return .= '<div style="text-align:center;display:block;padding:5px;background:#f8f8f8;"><h5 style="padding:0px;margin:0px">'.$prods['global_promos'][0]['designacao'].'</h5><span style="font-size:10px;">'.substr($prods['global_promos'][0]['data_inicio'], 0, 10).' - '.substr($prods['global_promos'][0]['data_fim'], 0,10).'<b data-toggle="tooltip" title="" data-original-title="'.$prods['global_promos']['descricao'].'">&nbsp<img src="layout/'.PROJETO.'/images/alert.png" width="13"></b></span></div>';
			$label_promo='<div class="ribbon-wrapper2"><img src="layout/'.PROJETO.'/images_gerado/promo.png" alt="" /></div>';
		}
	}

    $return .= '<div class="primary_block row"><div class="container"><div class="top-hr"></div></div>';


    if($not_produto_standard)$return.='<input type="hidden" id="produto_standard" name="produto_standard" value="0">';
	else $return.='<input type="hidden" id="produto_standard" name="produto_standard" value="1">';


    $return .= '<div class="pb-left-column col-xs-12 col-sm-12 col-md-6">';



    $tam=count($prods['prd_imgs']['img_cart']);
	if($prods['u_catetiq'] )
	{
		if($prods['u_imagetiq'] != '')
		{
			$temp=explode(".",$prods['u_imagetiq']);
			if(strtolower(end($temp)) == "pdf" ||  strtolower(end($temp)) == "jpg" ||  strtolower(end($temp)) == "png")
			{
				$return .= '<a href="'.$prods['u_imagetiq'].'" target="_blank" class="eenergia" style="background-color:'.$prods['u_coretiq'].';border-right-color:'.$prods['u_coretiq'].'">'.$prods['u_catetiq'].'</a>';
			}
			else
			{
				$return .= '<a class="eenergia fancybox" style="background-color:'.$prods['u_coretiq'].';border-right-color:'.$prods['u_coretiq'].';cursor:pointer;" href="'.$prods['u_imagetiq'].'">'.$prods['u_catetiq'].'</a>';
			}
		}
		else
		{
			$return .= '<div class="eenergia" style="background-color:'.$prods['u_coretiq'].';border-right-color:'.$prods['u_coretiq'].'">'.$prods['u_catetiq'].'</div>';
		}
		if($prods['EtiquetaEnergiaNova']==1) $return .= '<div class="g_a">&nbsp;</div>';
	}

	if (!strpos($prods['prd_imgs']['img'], 'no_photo') !== false) {
		$img_type = array('img_cart','img_med','img_new','img_news','img_fb');
		$m = 1;
		foreach ($img_type as $imty) {
			if (strpos($prods['prd_imgs'][$imty], 'no_photo') !== false) {
			    $m = 0;
			    break;
			}
		}
		if(!$m){
			$newimg = gerarneededimages($prods['prod_id']);
		}
	}

	$return .= "<div class='product-gallery-block resp1'>";
	$return .= '<div class="campanhas-prods" style="display:none;position:absolute;top:0;z-index:9999"></div>';
	$return .= "<ul class='product-gallery'>";

	if (!substr_count($prods['prd_imgm_med'], 'no_photo'))
	{
		for ($figi=0;$figi < $tam;$figi++)
		{
			if($figi==0)
			{
				$return .= '<li class="item active" data-thumb="'.$prods['prd_imgs']['img_med'][$figi].'">';
				$return .= $label_promo;
				$return .= '<span class="helper 8"></span>';
				if($tam>1)$return .= '<a href="'.$prods['prd_imgs']['img'][$figi].'" target="_blank" class="fancybox" id="big'.$figi.'" data-fancybox-group="gallery"><img class="zoom" src="'.$prods['prd_imgs']['img_med'][$figi].'" alt="'.rtrim($prods['prod_nome'], '-').'" border="0" /></a>';
				else $return .= '<a href="'.$prods['prd_imgs']['img'][$figi].'" target="_blank" class="fancybox" id="big'.$figi.'" data-fancybox="gallery"><img class="zoom" src="'.$prods['prd_imgs']['img_med'][$figi].'" alt="'.rtrim($prods['prod_nome'], '-').'" border="0" /></a>';
				$return .= '</li>';
			}
			else
			{
				$return .= '<li class="item" data-thumb="'.$prods['prd_imgs']['img_med'][$figi].'">'.$label_promo.'<span class="helper 9"></span><a href="'.$prods['prd_imgs']['img'][$figi].'" target="_blank" class="fancybox" id="big'.$figi.'" data-fancybox-group="gallery"><img src="'.$prods['prd_imgs']['img_med'][$figi].'" alt="'.rtrim($prods['prod_nome'], '-').'" border="0" /></a></li>';
			}
		}
	}
	else
	{
		$return .= '<li class="item active"  data-thumb="'.$prods['prd_imgs']['img_med'][$figi].'">'.$label_promo.'<span class="helper 10"></span><img src="'.$prods['prd_imgm_med'].'" alt="'.rtrim($prods['prod_nome'], '-').'" border="0" /></li>';
	}

	$return .= '</ul>';

	$return .= "</div>";
	$return .= "<div class='product-gallery-block resp2'>";
	$return .= '<div class="campanhas-prods" style="display:none;position:relative;z-index:9999"></div>';
	$return .= "<ul class='product-gallery-resp'>";

	if (!substr_count($prods['prd_imgm_med'], 'no_photo'))
	{
		for ($figi=0;$figi < $tam;$figi++)
		{
		if($figi==0) {$return .= '<li class="item active" data-thumb="'.$prods['prd_imgs']['img_med'][$figi].'">'.$label_promo.'<span class="helper 11"></span><a href="'.$prods['prd_imgs']['img'][$figi].'" target="_blank" class="fancybox" id="big'.$figi.'" data-fancybox-group="gallery"><img src="'.$prods['prd_imgs']['img_med'][$figi].'" alt="'.rtrim($prods['prod_nome'], '-').'" border="0" /></a></li>';}
		else {$return .= '<li class="item" data-thumb="'.$prods['prd_imgs']['img_med'][$figi].'">'.$label_promo.'<span class="helper 12"></span><a href="'.$prods['prd_imgs']['img'][$figi].'" target="_blank" class="fancybox" id="big'.$figi.'" data-fancybox-group="gallery"><img src="'.$prods['prd_imgs']['img_med'][$figi].'" alt="'.rtrim($prods['prod_nome'], '-').'" border="0" /></a></li>';}
		}
	}
	else
	{
		$return .= '<li class="item active"  data-thumb="'.$prods['prd_imgs']['img_med'][$figi].'">'.$label_promo.'<span class="helper 13"></span><img src="'.$prods['prd_imgm_med'].'" alt="'.rtrim($prods['prod_nome'], '-').'" border="0" /></li>';
	}

	$return .= '</ul>';
	$return .= '</div>';


    $tem_filtros=0;
    $filtros = filtros_ficha_produto($prods['prod_id'], $prods['fam_id']);

	if ($filtros) $tem_filtros=1;


	$return .= '</div>';
	$return .= '<div class="pb-right-column col-xs-12 col-sm-12  col-md-1"></div>';

	$return .= '<div class="pb-center-column col-xs-12 col-sm-12 col-md-5">
	<div class="row">
	<div class="col-sm-12">
	<h1 itemprop="name" class="titulo-prod">'.rtrim($prods['prod_nome'], '-').'</h1>';
	if ($prods['Ref_Ean'] != '') $return .= '<h5 style="margin-top:0"><strong>EAN '.$prods['Ref_Ean'].'</strong></h5>';

	$return .= '<style> .awesome-content{display:inline-block;width:10px;height:10px;opacity:0.8;font-size:13px;margin-left:4px;margin-top:-2px;} </style>';
	$return .= '<div class="inputs">';

	$filtros2 = filtros_lista_produto($prods['prod_id'], $prods['fam_id']);
	if($filtros2)
	{
		$return .= '<div class="descricao">';
		$return .= $filtros2;
		$return .= '</div>';
	}

	/* if (strip_html_tags($prods['prod_descricao']) != '')
	{

		$return .= '
		<div class="descricao">
		<p>'.nl2br(strip_html_tags_fp($prods['prod_descricao'])).'</p>
		</div>';
	} */

	$return .= '</div></div></div><div class="clear"></div>';


	$return .= '<div class="box-info-product">';
	$tem_qtt=0;
		$return .= '<div class="content_prices clearfix">';
		if ($prods['prod_precos']['PROMOCAO'] == 1 && strtotime($prods['prod_precos']['Promo_data_fim']) >= strtotime($prods['prod_precos']['Promo_data_ini']) && $prods['prod_precos']['Promo_data_fim']!='0000-00-00' && $prods['prod_precos']['Promo_data_ini']!='0000-00-00')
		{
			$return .= '<div class="tag tag-promocao" style="font-size:20px;line-height:28px">';
			$return .= '<span style="text-decoration:line-through;color:#656565">'.numberformat($prods['prod_precos']['PRECO_ANTIGO'],2).'</span><br>';
			$return .= '</div>';
		}
		$return .= '<div class="price" id="pricechange">';
		$txt_campanhas = '';
		if ($prods['prod_precos']['PROMOCAO'] == 1 && (strtotime($prods['prod_precos']['Promo_data_ini']) <= $today && strtotime($prods['prod_precos']['Promo_data_fim']) >=$today))
		{
			$txt_campanhas = $prods['prod_precos']['Promo_descricao'];
			$return .='<div style="font-size:10px">'.$txt_campanhas. ' válida de ('.$prods['prod_precos']['Promo_data_ini'].') até ('.$prods['prod_precos']['Promo_data_fim'].')</div><br>';
		}
		$return .= '<p class="our_price_display" itemtype="http://schema.org/Offer" itemscope="" itemprop="offers">';
		if($prods['prod_precos']['PVP'] > 0)
		{
			$pvp=$prods['prod_precos']['PVP'];

			if($prods['prod_precos']['IVA'] > 0)
			{
				$pvp_com_iva=$prods['prod_precos']['PVP'];
				$pvp_sem_iva=($prods['prod_precos']['PVP']/(1+$prods['prod_precos']['IVA']/100));
			}
			else
			{
				$pvp_sem_iva=$prods['prod_precos']['PVP'];
				$pvp_com_iva=($prods['prod_precos']['PVP']*(1+$prods['prod_precos']['IVA']/100));
			}
			// a pedido do cliente dia 18-01-2021
			// $return .= '<span style="font-size:16px">'.numberformat($pvp_sem_iva).' (Sem IVA)</span><br>';
			//$return .= '<span style="font-size:26px;color:#10892b">'.numberformat($pvp_com_iva).' (Com IVA)</span><br>';
			$return .= '<span style="font-size:26px">'.numberformat($pvp_com_iva).'</span><br>';
			/*if($prods['prod_precos']['TEM_IVA'] == 0 && $pvp) $return .= numberformat($pvp).' ';
			else if($pvp) $return .= numberformat($pvp);*/
		}
		$return .= '</p>';
		$return .='</div>';
		$return .= '</div>';
		$garantia=extensao_garantia_prod($prods['prod_id'], $prods['prod_precos']['PVP']);
		if(!empty($garantia))
		{
			$return .= '<div class="clear"></div><div class="blocos_escolha"><h3 style="font-size:16px">Extensão de Garantia</h3><div>';
			$return .= '<div class="cont" style="padding-bottom:5px"><div class="prdprice_n" style="font-size:11px;text-align:left"><label class="lbl_radio" for="prod_garantia" style="width:auto;padding:0;padding-left:25px;text-align:left">Sem Garantia Extra<input type="radio" name="prod_garantia" id="prod_garantia" value="0" checked="checked" /></label></div><div class="clear"></div></div>';
			$return .= '<div class="clear"></div>';

			for($a=0;$a<count($garantia);$a++)
			{
				$return .= '<div class="cont"><div class="prdprice_n floatleft"><label class="lbl_radio" for="prod_garantia_'.$garantia[$a]['id'].'" style="width:auto;padding:0;padding-left:25px">Garantia Extra de '.$garantia[$a]['anos'].($garantia[$a]['anos'] > 1 ? ' anos' :' ano' ).'&nbsp;'.($garantia[$a]['info'] != '' ? '<div class="awesome-content tooltip" title="'.htmlentities($garantia[$a]['info']).'" aria-hidden="false" ><i class="fa fa-info-circle" ></i></div>' :'').' <input type="radio" name="prod_garantia" id="prod_garantia_'.$garantia[$a]['id'].'" value="'.$garantia[$a]['id'].'" /></label>
				<span class="termos"><a href="'.$garantia[$a]['termos'].'" target="_blank"><u>'.getLinguas('key_termos',$GLOBALS['site_lang']).'</u></a></span><div class="clear"></div>
				</div>';
				$return .= '<div class="prdprice_n floatright" >';
				$return .= ($garantia[$a]['promo_ativo'] == 1 ? '<span style="text-decoration:line-through;font-size:11px;color:#333333">'.numberformat($garantia[$a]['valor_antigo']).'</span>&nbsp;<span style="font-size:12px">'.($garantia[$a]['valor'] > 0 ? 'Agora:' :'').'</span>&nbsp;' :'&nbsp;').''.($garantia[$a]['valor'] > 0 ? '+ '.numberformat($garantia[$a]['valor']) :'Gratuíto');
				$return .= '</div><div class="clear"></div></div><div class="clear"></div>';
			}
			$return .='</div>';
			$return .='</div>';
		}
		$portes=multi_portes_prod($prods['prod_id'], $prods['prod_precos']['PVP']);
		if(!empty($portes))
		{
			$return .= '<div class="clear"></div><div class="blocos_escolha"><h3 style="font-size:16px;">Entrega</h3><div>';
			$return .= '<div class="clear"></div>';
			for($a=0;$a<count($portes);$a++)
			{
				$return .= '<div class="cont"><div class="prdprice_n floatleft" style="font-size:11px;text-align:left"><label class="lbl_radio" for="prod_porte_'.$portes[$a]['id'].'" style="width:auto;padding:0;padding-left:25px">'.$portes[$a]['titulo'].'&nbsp;'.($portes[$a]['info'] != '' ? '<div class="awesome-content tooltip" title="'.htmlentities($portes[$a]['info']).'" aria-hidden="false" ><i class="fa fa-info-circle " ></i></div>' :'').'  <input type="radio" name="prod_porte" id="prod_porte_'.$portes[$a]['id'].'" value="'.$portes[$a]['id'].'" /></label>
				<span class="termos">Li e aceito os <a href="'.$portes[$a]['termos'].'" target="_blank"><u>Termos e Condições</u></a></span><div class="clear"></div>
				</div>';
				$return .= '<div class="prdprice_n floatright">';
				$return .= ($portes[$a]['promo_ativo'] == 1 ? '<span style="text-decoration:line-through;font-size:11px;color:#333333">'.numberformat($portes[$a]['valor_antigo']).'</span>&nbsp;<span style="font-size:12px">'.($portes[$a]['valor'] > 0 ? 'Agora:' :'').'</span>&nbsp;' :'&nbsp;').''.($portes[$a]['valor'] > 0 ? '+ '.numberformat($portes[$a]['valor']) :'Gratuíto');
				$return .= '</div><div class="clear"></div></div>';
			}
			$return .='</div></div>';
		}
		$return .= '<div class="product_attributes clearfix">';
		if(($precos['STOCK'] > 0 || $PODE_ENCOMENDAR == 1) && $prods['prod_precos']['PVP'] > 0 && $prods['prod_carrinho'] == 1)
		{
			$return .= '<p id="quantity_wanted_p">
			<label>'.getLinguas('key_quantidade',$GLOBALS['site_lang']).'</label>
			<input type="text" value="1" class="text" id="quantity_wanted" name="qty" style="border:1px solid rgb(189, 194, 201);">
			<a class="btn btn-default button-minus product_quantity_down" data-field-qty="qty" href="#">
			<span>
			<i class="fa fa-minus"></i>
			</span>
			</a>
			<a class="btn btn-default button-plus product_quantity_up" data-field-qty="qty" href="">
			<span>
			<i class="fa fa-plus"></i>
			</span>
			</a>
			<span class="clearfix"></span>
			</p>';
		 }
		$stock_txt='';

		if($precos['STOCK'] >= 3) $stock_txt='<span class="disponivel" title="Disponibilidade Total" > Stock disponivel</span>';
		elseif($precos['STOCK'] == 2) $stock_txt='<span class="limitado" title="Disponibilidade Reduzida"> Stock Reduzido</span>';
		elseif($precos['STOCK'] == 1) $stock_txt='<span class="porencomenda" title="Disponivel por Encomenda"> Por Encomenda</span>';
		else $stock_txt='<span class="esgotado" title="Disponivel Brevemente"> Disponivel Brevemente</span>';

		$wish='';$res=0;
		if($dados_cliente)
		{
			$cli_id=$dados_cliente['cli_id'];
			if(isProductInWishList($prods['prod_id'], $cli_id, $_SESSION['CLIENTE_ID_TEMP'])) $res=1;
			else $res=0;
		}
		elseif($_SESSION['CLIENTE_ID_TEMP'] != '')
		{
			if(isProductInWishList($prods['prod_id'], 0, $_SESSION['CLIENTE_ID_TEMP'])) $res=1;
			else $res=0;
		}

		/*if($res == 1) $wish='<a class="icon-coracao produtowl'.$prods['prod_id'].'" href="#" onclick="removeProductWishList(\''.$prods['prod_id'].'\', 0);return false;"><span title="'.getLinguas('key_wishlist_rem').'"><i class="fas fa-star" aria-hidden="true"></i></span></a>';
		else $wish='<a class="icon-coracao produtowl'.$prods['prod_id'].'" href="#" onclick="addProductWishList(\''.$prods['prod_id'].'\');return false;"><span title="'.getLinguas('key_wishlist_add').'"><i class="far fa-star" aria-hidden="true"></i></span></a>';*/

		$return .= '</div>';




		$return .= '<div class="box-cart-bottom">';



		$return .= '<div class="buttons_bottom_block no-print" id="add_to_cart">';
		/*$return .= $wish;
		$return .= '<div class="clear"></div>';*/
		$return .= $stock_txt;
		$return .= '<div class="clear"></div>';

		if((($precos['STOCK'] == 0  || $precos['STOCK'] == 1) && $PODE_ENCOMENDAR == 1) && $prods['prod_precos']['PVP'] > 0 && $prods['prod_carrinho'] == 1)
		{
			$return .= '<button  class="btn btn-default ajax_add_to_cart_product_button fp_comprar" onclick="adicionarCarrinhoProdutoOpcoes('.$prods['prod_id'].',$(\'#quantity_wanted\').val());" type="button"><span>'.getLinguas('key_comprar').'</span></button>';
			$return .= '<br> <br> <button class="fp_comprar_esgotado fancy_stock" href="'.$GLOBALS['site_http_ajax_url'].'setNotificacaoProduto.php?idprod='.$prods['prod_id'].'">Alerta de Stock</button>';
		}
		elseif($precos['STOCK'] > 0 && $prods['prod_precos']['PVP'] > 0 && $prods['prod_carrinho'] == 1) $return .= '<button  class="btn btn-default ajax_add_to_cart_product_button fp_comprar" onclick="adicionarCarrinhoProdutoOpcoes('.$prods['prod_id'].',$(\'#quantity_wanted\').val());" type="button"><span>'.getLinguas('key_comprar').'</span></button>';
		else $return .= '<button class="fp_comprar_esgotado fancy_stock" href="'.$GLOBALS['site_http_ajax_url'].'setNotificacaoProduto.php?idprod='.$prods['prod_id'].'">Alerta de Stock</button>';

		$return .= '<div class="clear"></div>
		</div>';


		$return .= '<img style="max-width:130px" border="0" src="layout/'.PROJETO.'/images/compra-segura2.png" alt="compra segura"><br>';

		$prod_txt1 = get_more_text($prods['prod_id']);
		if($prod_txt1!='')$return .= '<span style="padding-top:30px">'.$prod_txt1.'</span>';


		$return .= '<div class="clear"></div></div>';

		$tem_qtt=1;


    $return .= '</div><div class="clear"></div>';
	$return .= '</div>';


	$return .= '</div>';



	$descricao_verifica = strip_html_tags($prods['prod_descricao']);
	$detalhe_verifica = strip_html_tags($prods['prod_como_funciona']);
	$ficha_verifica = strip_html_tags($prods['prod_envio']);
	$prod_video = $prods['prod_video'];
	$customizado_titulo= strip_html_tags($prods['prod_customizado_titulo']);
	$customizado= strip_html_tags($prods['prod_customizado']);
	$script=str_replace(array('http://youtu.be/', 'https://youtu.be/', 'http://www.youtube.com/watch?v=', 'https://www.youtube.com/watch?v='), '//www.youtube.com/embed/',$prod_video);
	
	//$produtosrelacionados=hasProdutosRelacionados($_GET['prod_id']);
	//$prn=hasProdutosRelacionados_novo($_GET['prod_id']);

	if($descricao_verifica !='' || $detalhe_verifica != '' || $ficha_verifica != '' || $prod_video != '' || $customizado != '')
	{
		$return .= '<div class="horizontalTab">
		<ul class="resp-tabs-list">
		'.($descricao_verifica!='' ? '<li>'.getLinguas('txt_descricao',$GLOBALS['site_lang']).'</li>' :'').'
		'.($detalhe_verifica!='' ? '<li>'.getLinguas('key_detalhes',$GLOBALS['site_lang']).'</li>' :'').'
		'.($ficha_verifica!='' ? '<li>'.getLinguas('key_ficha_tecnica',$GLOBALS['site_lang']).'</li>' :'').'
		'.($prods['prod_ee'] != '' || $prods['prod_ue'] != '' || $prods['prod_dt'] != '' ? '<li>'.getLinguas('key_outros_fxs',$GLOBALS['site_lang']).'</li>' :'').'
		'.($prod_video != '' ? '<li>'.getLinguas('key_video',$GLOBALS['site_lang']).'</li>' :'').'
		'.($customizado != '' ? '<li>'.$customizado_titulo.'</li>' :'').'
		</ul>
		<div class="resp-tabs-container">

		'.($descricao_verifica!='' ? '<div><p>'.nl2br(strip_html_tags_fp($prods['prod_descricao2'])).'</p></div>' :'').'
		'.($detalhe_verifica!='' ? '<div><p>'.str_replace('´´','"',str_replace('|','<br>',str_replace('. ','.<br>',$prods['prod_como_funciona']))).'</p></div>' :'').'
		'.($ficha_verifica!='' ? '<div><p>'.$prods['prod_envio'].'</p></div>' :'').'
		'.($prods['prod_ee'] != '' || $prods['prod_ue'] != ''  || $prods['prod_dt'] != '' ? '<div><p>' :'').'
		'.($prods['prod_ee'] != '' ? '<a style="color:#666;text-decoration:none;text-transform:uppercase;font-weight:bold" href="'.$prods['prod_ee'].'" target="_blank">&#9658;&nbsp;&nbsp;'.getLinguas('key_etiqueta_energ',$GLOBALS['site_lang']).'</a><br>' :'').'
		'.($prods['prod_ue'] != '' ? '<a style="color:#666;text-decoration:none;text-transform:uppercase;font-weight:bold" href="'.$prods['prod_ue'].'" target="_blank">&#9658;&nbsp;&nbsp;'.getLinguas('key_fx_produto_ue',$GLOBALS['site_lang']).'</a><br>' :'').'
		'.($prods['prod_dt'] != '' ? '<a style="color:#666;text-decoration:none;text-transform:uppercase;font-weight:bold" href="'.$prods['prod_dt'].'" target="_blank">&#9658;&nbsp;&nbsp;'.getLinguas('key_desenho_tecnico',$GLOBALS['site_lang']).'</a>' :'').'
		'.($prods['prod_ee'] != '' || $prods['prod_ue'] != '' || $prods['prod_dt'] != '' ? '</p></div>' :'').'
		'.($prod_video!='' ? '<div><p><iframe width="100%" height="450" src="'.$script.'" frameborder="0" allowfullscreen></iframe></p></div>' :'').'
		'.($customizado!='' ? '<div><p>'.nl2br(strip_html_tags_fp($customizado)).'</p></div>' :'').'

		</div>';
	}


	//configurado em PROD_CONF no conf/conf.php
	if($P_CONF['tem_relacionados'] == 1)
	{
		$produtosrelacionados=hasProdutosRelacionados($_GET['prod_id']);
		/*if($produtosrelacionados > 0)
		{
			$return.='<div class="relacionados_wp col-md-12"><div class="titulo_a">'.getLinguas('txt_relacionados',$GLOBALS['site_lang']).'</div>';
			$return.='<div class="clear"></div>';
			$return.=bloco_catalogo_fp().'</div>';
		}*/

		$return .= '
			<div class="c"></div><br><br><div class="c"></div>
			<div class="catalogo_topo_filtros" style="background:none;color:#000">
				<div class="destaque_img">
					<div class="titulo" style="width:100%;background-color:#232323;color:#fff">RECOMENDADO <span>PARA SI</span></div>
					<div class="clear"></div>
					<div class="item">
						<div class="leftbutton" id="goToPrevSlide_promocao"></div>
						<div class="slidercontent">
							<ul id="home-promocao">';

							ob_start();

							global $DB_host,$DB_user,$DB_pass, $DB_name, $site_lang;
						    $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
						    $rs=new DataObj;

							//$sql='SELECT p.ID FROM products p INNER JOIN products_families_obj pfo on p.ID=pfo.ID_Prod WHERE pfo.ID_ORG='.$prods['fam_id'].' AND p.Aproved=1 AND p.Aproved>=1 group by pfo.ID_Prod Limit 0,10';
							$sql='SELECT pfo.ID_Prod as idprod FROM products p
							INNER JOIN products_families_obj pfo ON p.ID=pfo.ID_Prod
							INNER JOIN products_objs po ON po.obj_type_id=pfo.ID_Prod
							WHERE pfo.ID_ORG='.$prods['fam_id'].' AND pfo.ID_Prod!='.$_GET['prod_id'].' AND p.Aproved=1  GROUP BY pfo.ID_Prod ORDER BY po.obj_pvp ASC LIMIT 10 ';
							$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
							while(!$rs->EOF())
							{
								$prods2=get_produtc_info($rs->db_data['idprod']);

								$link=$site_http_url.\seo2(2,$GLOBALS['products_page_id'],$prods2,$GLOBALS['site_lang']);

								$prod2['id']=$prods2['prod_id'];
								$prod2['title']=$prods2['prod_nome'];
								$prod2['caminho']=$link;
								$prod2['image']=$prods2['prd_imgm_med'];
								$prod2['subtitle']=$prods2['prod_subtitulo'];
								$prod2['ref']=$prods2['prod_ref'];
								$prod2['carrinho']=$prods2['prod_carrinho'];

								$prod2['price']=$prods2['prod_precos']['PVP'];
								$prod2['price_riscado']=$prods2['prod_precos']['PRECO_ANTIGO'];
								$prod2['stock']=$prods2['prod_precos']['STOCK'];
								$prod2['promo']=$prods2['prod_precos']['PROMOCAO'];

								$prod2['destaque']=$prods2['prod_precos']['DESTAQUE'];
								$prod2['novo']=$prods2['prod_precos']['NOVO'];
								$prod2['outlet']=$prods2['prod_precos']['OUTLET'];

								$prod2['u_c']=$prods2['u_catetiq'];
								$prod2['u_i']=$prods2['u_imagetiq'];
								$prod2['u_co']=$prods2['u_coretiq'];


								show_blocos_destaque($prod2);
								$rs->db_next();
							}
							$tam=$rs->db_rows;
							$rs->db_close();


							$txt = ob_get_contents();
							$return .= $txt;

							ob_end_clean();
							$return .= '
							</ul>
						</div>
						<div class="rightbutton" id="goToNextSlide_promocao"></div>
						<div class="clear"></div>
					</div>
				</div>
			</div>';
	}
	/*


	if ($prn['bydef'] > 0)
	{
		//echo 'we are defined';

		$return .= '<div class="relacionados_wp col-md-12"><div class="titulo_a">'.getLinguas('txt_relacionados',$GLOBALS['site_lang']).'</div>';
		$return .= '<div class="clear"></div>';
		$return .= bloco_catalogo_fp_rela();
		$return .=  '</div>';
	}
	if ($prn['default'] > 0 && $prn['bydef']==0)
	{
		//echo 'we are by default';

		$return .= '<div class="relacionados_wp col-md-12"><div class="titulo_a">'.getLinguas('txt_relacionados',$GLOBALS['site_lang']).'</div>';
		$return .= '<div class="clear"></div>';
		$return .= bloco_catalogo_fp_rela();
		$return .=  '</div>';
	}*/

    return $return;
}

function produto_velho_prod($prod_id=0)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang,$site_http_url;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);

	$obj = new Dataobj;

	$return = array();

	$sql = '
			SELECT
				familia_id, garantia_id, garantia_n_anos, garantia_valor
			FROM
				troca_velho eg
				INNER JOIN troca_velho_products egp ON egp.promo_id=eg.garantia_id
			WHERE
				egp.PROD_ID = "'.$prod_id.'"
				AND garantia_visivel=1
			ORDER BY
				garantia_n_anos ASC
			';
			// echo $sql;
	$obj->db_open($db_link, $DB_name, $sql);
	if(!$obj->EOF())
	{

		while(!$obj->EOF())
		{
			$garantia_id = $obj->db_data['garantia_id'];

			$familia_id = $obj->db_data['familia_id'];
			$garantia_n_anos = $obj->db_data['garantia_n_anos'];
			$garantia_valor  = $obj->db_data['garantia_valor'];

			$return[] = array('familia_id' => $familia_id, 'garantia_id' => $garantia_id, 'garantia_anos' => $garantia_n_anos, 'garantia_valor' => $garantia_valor);

			$obj->db_next();
		}
	}
	$obj->db_close();

	return $return;
}
function montagem_prod($prod_id=0)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang,$site_http_url;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);

	$obj = new Dataobj;

	$return = array();

	$sql = '
			SELECT
				familia_id,garantia_id, garantia_n_anos, garantia_valor
			FROM
				montagem eg
				INNER JOIN montagem_products egp ON egp.promo_id=eg.garantia_id
			WHERE
				egp.PROD_ID = "'.$prod_id.'"
				AND garantia_visivel=1
			ORDER BY
				garantia_n_anos ASC
			';
			// echo $sql;
	$obj->db_open($db_link, $DB_name, $sql);
	if(!$obj->EOF())
	{

		while(!$obj->EOF())
		{
			$garantia_id = $obj->db_data['garantia_id'];

			$familia_id = $obj->db_data['familia_id'];
			$garantia_n_anos = $obj->db_data['garantia_n_anos'];
			$garantia_valor  = $obj->db_data['garantia_valor'];

			$return[] = array('familia_id' => $familia_id,'garantia_id' => $garantia_id, 'garantia_anos' => $garantia_n_anos, 'garantia_valor' => $garantia_valor);

			$obj->db_next();
		}
	}
	$obj->db_close();

	return $return;
}
function ficha_produto_nstandard($prods)
{
    $dados_cliente=cliente_logado();

	$return='<link rel="stylesheet" href="layout/'.PROJETO.'/css/css_external/product_v_'.filemtime('layout/'.PROJETO.'/css/css_external/product.css').'.css" media="all"/> ';

	$return .= '<script>quantityAvailable=9999;</script>';
	$return .= '<script type="text/javascript" src="layout/'.PROJETO.'/css/css_external/product.js"></script> ';
    $return .= BREADCRUMBS(1, intval($prods['fam_id']));


	$tem_qtt=0;
	$label_promo='';
	$precos=$prods['prod_precos'];

	$PODE_ENCOMENDAR=$precos['PODE_ENCOMENDAR'];

	$today = date("U");

	if ((($precos['STOCK'] > 0 || $PODE_ENCOMENDAR == 1) && $prods['prod_precos']['PVP'] > 0 && $prods['prod_carrinho'] == 1))
	{
		if ($prods['prod_precos']['PROMOCAO'] == 1 && $prods['prod_precos']['PRECO_ANTIGO'] > 0 && $prods['prod_precos']['PRECO_ANTIGO'] > $prods['prod_precos']['PVP'] && (strtotime($prods['prod_precos']['Promo_data_ini']) <= $today && strtotime($prods['prod_precos']['Promo_data_fim']) >=$today))
		{
			$label_promo='<div class="ribbon-wrapper2"><img src="layout/'.PROJETO.'/images_gerado/promo.png" alt="" /></div>';
		}
	}

    $return .= '<div class="primary_block row"><div class="container"><div class="top-hr"></div></div>';
    $return .= '<div class="pb-left-column col-xs-12 col-sm-12 col-md-6">';
    $tam=count($prods['prd_imgs']['img_cart']);

	if($prods['u_catetiq'] )
	{
		if($prods['u_imagetiq'] != '')
		{
			$temp=explode(".",$prods['u_imagetiq']);
			if(strtolower(end($temp)) == "pdf")
			{
				$return .= '<a href="'.$prods['u_imagetiq'].'" target="_blank" class="eenergia" style="background-color:'.$prods['u_coretiq'].';border-right-color:'.$prods['u_coretiq'].'">'.$prods['u_catetiq'].'</a>';
			}
			else
			{
				$return .= '<a class="eenergia fancybox" style="background-color:'.$prods['u_coretiq'].';border-right-color:'.$prods['u_coretiq'].';cursor:pointer;" href="'.$prods['u_imagetiq'].'">'.$prods['u_catetiq'].'</a>';
			}
		}
		else
		{
			$return .= '<div class="eenergia" style="background-color:'.$prods['u_coretiq'].';border-right-color:'.$prods['u_coretiq'].'">'.$prods['u_catetiq'].'</div>';
		}

	}


	$return .= "<div class='product-gallery-block resp1'>";
	$return .= '<div class="campanhas-prods" style="display:none;position:absolute;top:0;z-index:9999"></div>';
	$return .= "<ul class='product-gallery'>";

	if (!substr_count($prods['prd_imgm_med'], 'no_photo'))
	{
		for ($figi=0;$figi < $tam;$figi++)
		{
			if($figi==0)
			{
				$return .= '<li class="item active" data-thumb="'.$prods['prd_imgs']['img_new'][$figi].'">';
				$return .= $label_promo;
				$return .= '<span class="helper 14"></span>';
				if($tam>1)$return .= '<a href="'.$prods['prd_imgs']['img'][$figi].'" target="_blank" class="fancybox" id="big'.$figi.'" data-fancybox-group="gallery"><img class="zoom" src="'.$prods['prd_imgs']['img_med'][$figi].'" alt="'.rtrim($prods['prod_nome'], '-').'" border="0" /></a>';
				else $return .= '<a href="'.$prods['prd_imgs']['img'][$figi].'" target="_blank" class="fancybox" id="big'.$figi.'" data-fancybox="gallery"><img class="zoom" src="'.$prods['prd_imgs']['img_med'][$figi].'" alt="'.rtrim($prods['prod_nome'], '-').'" border="0" style="" /></a>';
				$return .= '</li>';
			}
			else
			{
				$return .= '<li class="item" data-thumb="'.$prods['prd_imgs']['img_new'][$figi].'">'.$label_promo.'<span class="helper"></span><a href="'.$prods['prd_imgs']['img'][$figi].'" target="_blank" class="fancybox" id="big'.$figi.'" data-fancybox-group="gallery"><img src="'.$prods['prd_imgs']['img_med'][$figi].'" alt="'.rtrim($prods['prod_nome'], '-').'" border="0" /></a></li>';
			}
		}
	}
	else
	{
		$return .= '<li class="item active"  data-thumb="'.$prods['prd_imgs']['img_new'][$figi].'">'.$label_promo.'<span class="helper"></span><img src="'.$prods['prd_imgm_med'].'" alt="'.rtrim($prods['prod_nome'], '-').'" border="0" /></li>';
	}

	$return .= '</ul>';

	$return .= "</div>";
	$return .= "<div class='product-gallery-block resp2'>";
	$return .= '<div class="campanhas-prods" style="display:none;position:relative;z-index:9999"></div>';
	$return .= "<ul class='product-gallery-resp'>";

	if (!substr_count($prods['prd_imgm_med'], 'no_photo'))
	{
		for ($figi=0;$figi < $tam;$figi++)
		{
		if($figi==0) {$return .= '<li class="item active" data-thumb="'.$prods['prd_imgs']['img_med'][$figi].'">'.$label_promo.'<span class="helper"></span><a href="'.$prods['prd_imgs']['img'][$figi].'" target="_blank" class="fancybox" id="big'.$figi.'" data-fancybox-group="gallery"><img src="'.$prods['prd_imgs']['img_med'][$figi].'" alt="'.rtrim($prods['prod_nome'], '-').'" border="0" /></a></li>';}
		else {$return .= '<li class="item" data-thumb="'.$prods['prd_imgs']['img_med'][$figi].'">'.$label_promo.'<span class="helper"></span><a href="'.$prods['prd_imgs']['img'][$figi].'" target="_blank" class="fancybox" id="big'.$figi.'" data-fancybox-group="gallery"><img src="'.$prods['prd_imgs']['img_med'][$figi].'" alt="'.rtrim($prods['prod_nome'], '-').'" border="0" /></a></li>';}
		}
	}
	else
	{
		$return .= '<li class="item active"  data-thumb="'.$prods['prd_imgs']['img_med'][$figi].'">'.$label_promo.'<span class="helper"></span><img src="'.$prods['prd_imgm_med'].'" alt="'.rtrim($prods['prod_nome'], '-').'" border="0" /></li>';
	}

	$return .= '</ul>';
	$return .= '</div>';


    $tem_filtros=0;
    $filtros = filtros_ficha_produto($prods['prod_id'], $prods['fam_id']);

	if ($filtros) $tem_filtros=1;


	$return .= '</div>';
	$return .= '<div class="pb-right-column col-xs-12 col-sm-12  col-md-1"></div>';

	$return .= '<div class="pb-center-column col-xs-12 col-sm-12 col-md-5">
	<div class="row">
	<div class="col-sm-12">
	<h1 itemprop="name" class="titulo-prod">'.rtrim($prods['prod_nome'], '-').'</h1>';
	if ($prods['Ref_Ean'] != '') $return .= '<h2 style="margin-top:0"><strong>EAN '.$prods['Ref_Ean'].'</strong></h2>';

	$return .= '<style> .awesome-content{display:inline-block;width:10px;height:10px;opacity:0.8;font-size:13px;margin-left:4px;margin-top:-2px;} </style>';
	$return .= '<div class="inputs">';

	$filtros2 = filtros_lista_produto($prods['prod_id'], $prods['fam_id']);
	$return .= '<div class="descricao">';
	if($tem_filtros)
	{
		$return .= $filtros;
	}
	if($filtros2)
	{
		$return .= $filtros2;
	}
	$return .= '</div>';

	/* if (strip_html_tags($prods['prod_descricao']) != '')
	{

		$return .= '
		<div class="descricao">
		<p>'.nl2br(strip_html_tags_fp($prods['prod_descricao'])).'</p>
		</div>';
	} */

	$return .= '</div></div></div><div class="clear"></div>';
	$return .= '<div class="box-info-product">';

	$tem_qtt=0;
		$return .= '<div class="content_prices clearfix">';

		$return .= '<div class="price" id="pricechange">';

		$txt_campanhas = '';
		if ($prods['prod_precos']['PROMOCAO'] == 1 && $prods['prod_precos']['PRECO_ANTIGO'] > 0 && $prods['prod_precos']['PRECO_ANTIGO'] > $prods['prod_precos']['PVP'])
		{
			$txt_campanhas = $prods['prod_precos']['Promo_descricao'];
			$return .= '<div class="tag tag-promocao" style="font-size:16px;line-height:28px">';
			$return .= 'Antes:'.numberformat($prods['prod_precos']['PRECO_ANTIGO'],2).' <sup>(1)</sup><br>';
			$return .= '</div>';
		}

		$return .= '<p class="our_price_display" itemtype="http://schema.org/Offer" itemscope="" itemprop="offers">';
		if($prods['prod_precos']['PVP'] > 0)
		{
			$pvp=$prods['prod_precos']['PVP'];

			if($prods['prod_precos']['TEM_IVA'] == 0 && $pvp) $return .= number_format($pvp,2,',','.').' &euro;';
			else if($pvp) $return .= number_format($pvp,2,',','.').' &euro;';
		}

		$return .= '</p></div>';

		$return .= '</div>';

		$garantia = extensao_garantia_prod($prods['prod_id'], $prods['prod_precos']['PVP']);
				if(!empty($garantia))
				{
					$return .= '<div class="c"></div><div style="border-bottom:#e2e2e2 1px solid;margin-bottom:5px;height:1px">&nbsp;</div><div class="c"></div><div style="text-align:center;font-size:14px;color:#d1488a;background:#f3f4f4;padding:5px"><h3>Extensão de garantia</h3></div><div style="text-align:center;font-size:14px;background:#f3f4f4;padding:5px">';

					$return .= '<div class="prdprice_n" style="font-size:14px;text-align:left"><label class="lbl_radio" for="prod_garantia" style="width:auto;padding:0;padding-left:25px;text-align:left">Sem Extensão<input type="radio" name="prod_garantia" id="prod_garantia" value="0" checked="checked" /></label></div>';
					$return .= '<div class="prdprice_n floatright"  style="font-size:15px">';
					$return .= '</div><div class="c"></div>';

					for($a=0;$a<count($garantia);$a++)
					{
						$return .= '<div class="prdprice_n" style="font-size:14px;text-align:left"><label class="lbl_radio" for="prod_garantia_'.$garantia[$a]['garantia_id'].'" style="width:auto;padding:0;padding-left:25px">Com Extensão ('.$garantia[$a]['garantia_anos'].($garantia[$a]['garantia_anos'] > 1 ? ' anos' :' ano' ).') <input type="radio" name="prod_garantia" id="prod_garantia_'.$garantia[$a]['garantia_id'].'" value="'.$garantia[$a]['garantia_id'].'" /></label></div>';
						$return .= '<div class="prdprice_n floatright"  style="font-size:15px">';
						//$return .= numberformat($garantia[$a]['garantia_valor']).' (com IVA) ';
						$return .= '+ '.numberformat($garantia[$a]['garantia_valor']);
						$return .= '</div><div class="c"></div>';
					}

					$return .='</div>';
				}

				$garantia = produto_velho_prod($prods['prod_id']);
				if(!empty($garantia))
				{
					$return .= '<div class="c"></div><div style="border-bottom:#e2e2e2 1px solid;margin-bottom:5px;height:1px">&nbsp;</div><div class="c"></div><div style="text-align:center;font-size:14px;color:#d1488a;background:#f3f4f4;padding:5px"><h3>Recolha de Equipamento Antigo</h3></div><div style="text-align:center;font-size:14px;background:#f3f4f4;padding:5px">';

					$return .= '<div class="prdprice_n" style="font-size:14px;text-align:left"><label class="lbl_radio" for="prod_troca" style="width:auto;padding:0;padding-left:25px">Sem Recolha <input type="radio" name="prod_troca" id="prod_troca" value="0" checked="checked" /></label></div>';
					$return .= '<div class="prdprice_n floatright"  style="font-size:15px">';
					$return .= '</div><div class="c"></div>';

					for($a=0;$a<count($garantia);$a++)
					{
						$return .= '<div class="prdprice_n" style="font-size:14px;text-align:left"><label class="lbl_radio" for="prod_troca_'.$garantia[$a]['garantia_id'].'" style="width:auto;padding:0;padding-left:25px;text-align:left">Com Recolha <input type="radio" name="prod_troca" id="prod_troca_'.$garantia[$a]['garantia_id'].'" value="'.$garantia[$a]['garantia_id'].'" /></label></div>';
						$return .= '<div class="prdprice_n floatright"  style="font-size:15px">';
						//$return .= numberformat($garantia[$a]['garantia_valor']).' (com IVA) ';
						$return .= '+ '.numberformat($garantia[$a]['garantia_valor']);
						$return .= '</div><div class="c"></div>';
					}
					$return .='</div>';
				}

				$montagem = montagem_prod($prods['prod_id']);
				if(!empty($montagem))
				{
					$return .= '<div class="c"></div><div style="border-bottom:#e2e2e2 1px solid;margin-bottom:5px;height:1px">&nbsp;</div><div class="c"></div><div style="text-align:center;font-size:14px;color:#d1488a;background:#f3f4f4;padding:5px"><h3>Montagem</h3></div><div style="text-align:center;font-size:14px;background:#f3f4f4;padding:5px">';

					$return .= '<div class="prdprice_n" style="font-size:14px;text-align:left"><label class="lbl_radio" for="montagem" style="width:auto;padding:0;padding-left:25px">Sem Montagem <input type="radio" name="montagem" id="montagem" value="0" checked="checked" /></label></div>';
					$return .= '<div class="prdprice_n floatright"  style="font-size:15px">';
					$return .= '</div><div class="c"></div>';

					for($a=0;$a<count($montagem);$a++)
					{
						$return .= '<div class="prdprice_n" style="font-size:14px;text-align:left"><label class="lbl_radio" for="montagem_'.$montagem[$a]['garantia_id'].'" style="width:auto;padding:0;padding-left:25px;text-align:left">Com Montagem <input type="radio" name="montagem" id="montagem_'.$montagem[$a]['garantia_id'].'" value="'.$montagem[$a]['garantia_id'].'" /></label></div>';
						$return .= '<div class="prdprice_n floatright"  style="font-size:15px">';
						//$return .= numberformat($garantia[$a]['garantia_valor']).' (com IVA) ';
						$return .= '+ '.numberformat($montagem[$a]['garantia_valor']);
						$return .= '</div><div class="c"></div>';
					}
					$return .='</div>';
				}


		$return .= '<div class="product_attributes clearfix">';
		if(($precos['STOCK'] > 1 || $PODE_ENCOMENDAR == 1) && $prods['prod_precos']['PVP'] > 0 && $prods['prod_carrinho'] == 1)
		{
			$return .= '<p id="quantity_wanted_p">
			<label>'.getLinguas('key_quantidade',$GLOBALS['site_lang']).'</label>
			<input type="text" value="1" class="text" id="quantity_wanted" name="qty" style="border:1px solid rgb(189, 194, 201);">
			<a class="btn btn-default button-minus product_quantity_down" data-field-qty="qty" href="#">
			<span>
			<i class="fa fa-minus"></i>
			</span>
			</a>
			<a class="btn btn-default button-plus product_quantity_up" data-field-qty="qty" href="">
			<span>
			<i class="fa fa-plus"></i>
			</span>
			</a>
			<span class="clearfix"></span>
			</p>';
		 }
		$stock_txt='';


		if($PODE_ENCOMENDAR || ($prods['prod_carrinho'] > 0 && $precos['STOCK'] > 1 && $prods['prod_precos']['PVP'] > 0))
		{
			if($precos['STOCK'] >= 3) $stock_txt='<span class="disponivel" title="Disponibilidade Total" > Stock disponivel</span>';
			elseif($precos['STOCK'] == 2) $stock_txt='<span class="limitado" title="Disponibilidade Reduzida"> Stock Reduzido</span>';
			elseif($precos['STOCK'] == 0) $stock_txt='<span class="porencomenda" title="Disponivel por Encomenda"> Por Encomenda</span>';
			else $stock_txt='<span class="esgotado" title="Disponivel Brevemente"> Disponivel Brevemente</span>';
		}
		else
		{
			$stock_txt='<span class="esgotado" title="Disponivel Brevemente"> Disponivel Brevemente</span>';
		}
		// if($precos['STOCK'] >= 1) $stock_txt='<span class="limitado">Stock <img src="layout/'.PROJETO.'/images/status_away.png" /></span>';
		// elseif($precos['STOCK'] == 1) $stock_txt='<span class="limitado">Stock <img src="layout/'.PROJETO.'/images/status_away.png" /></span>';
		// else if($precos['STOCK'] > 1) $stock_txt='<span class="disponivel">Stock <img src="layout/'.PROJETO.'/images/status.png" /></span>';
		// else $stock_txt='<span class="esgotado">Stock <img src="layout/'.PROJETO.'/images/status_busy.png" /></span>';


		$wish='';$res=0;
		if($dados_cliente)
		{
			$cli_id=$dados_cliente['cli_id'];
			if(isProductInWishList($prods['prod_id'], $cli_id, $_SESSION['CLIENTE_ID_TEMP'])) $res=1;
			else $res=0;
		}
		elseif($_SESSION['CLIENTE_ID_TEMP'] != '')
		{
			if(isProductInWishList($prods['prod_id'], 0, $_SESSION['CLIENTE_ID_TEMP'])) $res=1;
			else $res=0;
		}

		if($res == 1) $wish='<a class="icon-coracao produtowl'.$prods['prod_id'].'" href="#" onclick="removeProductWishList(\''.$prods['prod_id'].'\', 0);return false;"><span title="'.getLinguas('key_wishlist_rem').'"><i class="fas fa-star" aria-hidden="true"></i></span></a>';
		else $wish='<a class="icon-coracao produtowl'.$prods['prod_id'].'" href="#" onclick="addProductWishList(\''.$prods['prod_id'].'\');return false"><span title="'.getLinguas('key_wishlist_add').'"><i class="far fa-star" aria-hidden="true"></i></span></a>';

		$return .= '</div>';



	if($PODE_ENCOMENDAR || ($prods['prod_carrinho'] == 1 ))
	{
		$return .= '<div class="box-cart-bottom">';

		$return .= '<div class="buttons_bottom_block no-print" id="add_to_cart">';
		$return .= $wish;
		$return .= '<div class="clear"></div>';
		$return .= $stock_txt;
		$return .= '<div class="clear"></div>';

		/* if((($precos['STOCK'] == 0  || $precos['STOCK'] == 1) && $PODE_ENCOMENDAR == 1) && $prods['prod_precos']['PVP'] > 0 && $prods['prod_carrinho'] == 1)
		{
			$return .= '<button  class="btn btn-default ajax_add_to_cart_product_button fp_comprar" onclick="adicionarCarrinhoProdutoOpcoes('.$prods['prod_id'].',$(\'#quantity_wanted\').val());" type="button"><span>'.getLinguas('key_comprar').'</span></button>';
			$return .= '<br> <br> <button class="fp_comprar_esgotado fancy_stock" href="'.$GLOBALS['site_http_ajax_url'].'setNotificacaoProduto.php?idprod='.$prods['prod_id'].'">Alerta de Stock</button>';
		}
		else */if($precos['STOCK'] > 1 && $prods['prod_precos']['PVP'] > 0 && $prods['prod_carrinho'] == 1) $return .= '<button  class="btn btn-default ajax_add_to_cart_product_button fp_comprar" onclick="adicionarCarrinhoProdutoOpcoes('.$prods['prod_id'].',$(\'#quantity_wanted\').val());" type="button"><span>'.getLinguas('key_comprar').'</span></button>';
		else $return .= '<button class="fp_comprar_esgotado fancy_stock" href="'.$GLOBALS['site_http_ajax_url'].'setNotificacaoProduto.php?idprod='.$prods['prod_id'].'">Alerta de Stock</button>';

		$return .= '<div class="clear"></div>
		</div>';

		$return .= '<img style="max-width:130px" border="0" src="layout/'.PROJETO.'/images/compra-segura2.png" alt="compra segura">';



		//$return .= '<span style="padding:30px">Entrega prevista entre 2 a 3 dias úteis.</span>';
		/*$return .= '<img  border="0" src="layout/'.PROJETO.'/images/entregas.svg" alt="entregas">';*/
		//$return .= '<object id="entregas" type="image/svg+xml" data="layout/'.PROJETO.'/images/entregas.svg">Your browser does not support SVG</object>';
		/*$return .= '<style>.BackgroundImage {
		        background:url("layout/'.PROJETO.'/images/entregas.svg") no-repeat scroll left top;
		        width:241px;
		        height:29px;
		}</style>
		<div class="BackgroundImage"></div>';*/
		//	if($precos['STOCK'] > 1)$return .= '<br><div style="font-size:10px">Tudo faremos para que o produto chegue até si no dia seguinte à compra, se comprar até às 14:00 produtos em stock e por multibanco. Contudo somos alheios a problemas com transportes e eventuais erros de informação de stock.</div>';


		$return .= '<div class="clear"></div></div>';
	}

	$tem_qtt=1;
    $return .= '</div><div class="clear"></div>';
	$return .= '</div>';
	$return .= '</div>';

	$today = date("U");
	if($prods['prod_precos']['PROMOCAO'] == 1 && (strtotime($prods['prod_precos']['Promo_data_ini']) <= $today && strtotime($prods['prod_precos']['Promo_data_fim']) >=$today))
	{
		if($txt_campanhas != '')
		{
			$return .= '<span style="text-align:left;font-weight:bold;color:#C51015;font-size:14px;margin-bottom:10px">(1) - '.$txt_campanhas.'</span><br><br>';
		}
		else
		{
			$Promo_data_ini = $prods['prod_precos']['Promo_data_ini'];
			$Promo_data_fim = $prods['prod_precos']['Promo_data_fim'];

			$return .= '<span style="text-align:left;font-weight:bold;color:#C51015;font-size:14px;margin-bottom:10px">(1) - Promoção válida de '.$Promo_data_ini.' a '.$Promo_data_fim.'</span><br><br>';
		}
	}

	$descricao_verifica = strip_html_tags($prods['prod_descricao']);
	$detalhe_verifica = strip_html_tags($prods['prod_como_funciona']);
	$ficha_verifica = strip_html_tags($prods['prod_envio']);
	$prod_video = $prods['prod_video'];
	$script=str_replace(array('http://youtu.be/', 'https://youtu.be/', 'http://www.youtube.com/watch?v=', 'https://www.youtube.com/watch?v='), '//www.youtube.com/embed/',$prod_video);
	// $prod_campanhas = $prods['prod_video'];
	//printr($prods);

	//$produtosrelacionados=hasProdutosRelacionados($_GET['prod_id']);
	//$prn=hasProdutosRelacionados_novo($_GET['prod_id']);

	if($descricao_verifica !='' || $detalhe_verifica != '' || $ficha_verifica != '' || $prod_video != '')
	{
		$return .= '<div class="horizontalTab">
		<ul class="resp-tabs-list">
		'.($descricao_verifica!='' ? '<li>'.getLinguas('txt_descricao',$GLOBALS['site_lang']).'</li>' :'').'
		'.($detalhe_verifica!='' ? '<li>'.getLinguas('key_detalhes',$GLOBALS['site_lang']).'</li>' :'').'
		'.($ficha_verifica!='' ? '<li>'.getLinguas('key_ficha_tecnica',$GLOBALS['site_lang']).'</li>' :'').'
		'.($prods['prod_ee'] != '' || $prods['prod_ue'] != '' || $prods['prod_dt'] != '' ? '<li>'.getLinguas('key_outros_fxs',$GLOBALS['site_lang']).'</li>' :'').'
		'.($prod_video != '' ? '<li>'.getLinguas('key_video',$GLOBALS['site_lang']).'</li>' :'').'
		'/**.($prn['bydef'] || $prn['default'] ? '<li>'.getLinguas('txt_relacionados',$GLOBALS['site_lang']).'</li>' :'')/**/.'
		</ul>
		<div class="resp-tabs-container">

		'.($descricao_verifica!='' ? '<div><p>'.nl2br(strip_html_tags_fp($prods['prod_descricao2'])).'</p></div>' :'').'
		'.($detalhe_verifica!='' ? '<div><p>'.str_replace('´´','"',str_replace('|','<br>',str_replace('. ','.<br>',$prods['prod_como_funciona']))).'</p></div>' :'').'
		'.($ficha_verifica!='' ? '<div><p>'.$prods['prod_envio'].'</p></div>' :'').'
		'.($prods['prod_ee'] != '' || $prods['prod_ue'] != ''  || $prods['prod_dt'] != '' ? '<div><p>' :'').'
		'.($prods['prod_ee'] != '' ? '<a style="color:#666;text-decoration:none;text-transform:uppercase;font-weight:bold" href="'.$prods['prod_ee'].'" target="_blank">&#9658;&nbsp;&nbsp;'.getLinguas('key_etiqueta_energ',$GLOBALS['site_lang']).'</a><br>' :'').'
		'.($prods['prod_ue'] != '' ? '<a style="color:#666;text-decoration:none;text-transform:uppercase;font-weight:bold" href="'.$prods['prod_ue'].'" target="_blank">&#9658;&nbsp;&nbsp;'.getLinguas('key_fx_produto_ue',$GLOBALS['site_lang']).'</a><br>' :'').'
		'.($prods['prod_dt'] != '' ? '<a style="color:#666;text-decoration:none;text-transform:uppercase;font-weight:bold" href="'.$prods['prod_dt'].'" target="_blank">&#9658;&nbsp;&nbsp;'.getLinguas('key_desenho_tecnico',$GLOBALS['site_lang']).'</a>' :'').'
		'.($prods['prod_ee'] != '' || $prods['prod_ue'] != '' || $prods['prod_dt'] != '' ? '</p></div>' :'').'
		'.($prod_video!='' ? '<div><p><iframe width="100%" height="450" src="'.$script.'" frameborder="0" allowfullscreen></iframe></p></div>' :'').'

		</div>';
	}


	//configurado em PROD_CONF no conf/conf.php
	if($P_CONF['tem_relacionados'] == 1)
	{
		$produtosrelacionados=hasProdutosRelacionados($_GET['prod_id']);
		/*if($produtosrelacionados > 0)
		{
			$return.='<div class="relacionados_wp col-md-12"><div class="titulo_a">'.getLinguas('txt_relacionados',$GLOBALS['site_lang']).'</div>';
			$return.='<div class="clear"></div>';
			$return.=bloco_catalogo_fp().'</div>';
		}*/

		$return .= '
			<div class="c"></div><br><br><div class="c"></div>
			<div class="catalogo_topo_filtros" style="background:none;color:#000">
				<div class="destaque_img">
					<div class="titulo" style="width:100%;background-color:#232323;color:#fff">RECOMENDADO <span>PARA SI</span></div>
					<div class="clear"></div>
					<div class="item">
						<div class="leftbutton" id="goToPrevSlide_promocao"></div>
						<div class="slidercontent">
							<ul id="home-promocao">';

							ob_start();

							global $DB_host,$DB_user,$DB_pass, $DB_name, $site_lang;
						    $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
						    $rs=new DataObj;

							//$sql='SELECT p.ID FROM products p INNER JOIN products_families_obj pfo on p.ID=pfo.ID_Prod WHERE pfo.ID_ORG='.$prods['fam_id'].' AND p.Aproved=1 AND p.Aproved>=1 group by pfo.ID_Prod Limit 0,10';
							$sql='SELECT pfo.ID_Prod as idprod FROM products p
							INNER JOIN products_families_obj pfo ON p.ID=pfo.ID_Prod
							INNER JOIN products_objs po ON po.obj_type_id=pfo.ID_Prod
							WHERE pfo.ID_ORG='.$prods['fam_id'].' AND pfo.ID_Prod!='.$_GET['prod_id'].' AND p.Aproved=1  GROUP BY pfo.ID_Prod ORDER BY po.obj_pvp ASC LIMIT 10 ';
							$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
							while(!$rs->EOF())
							{
								$prods2=get_produtc_info($rs->db_data['idprod']);

								$link=$site_http_url.\seo2(2,$GLOBALS['products_page_id'],$prods2,$GLOBALS['site_lang']);

								$prod2['id']=$prods2['prod_id'];
								$prod2['title']=$prods2['prod_nome'];
								$prod2['caminho']=$link;
								$prod2['image']=$prods2['prd_imgm_med'];
								$prod2['subtitle']=$prods2['prod_subtitulo'];
								$prod2['ref']=$prods2['prod_ref'];
								$prod2['carrinho']=$prods2['prod_carrinho'];

								$prod2['price']=$prods2['prod_precos']['PVP'];
								$prod2['price_riscado']=$prods2['prod_precos']['PRECO_ANTIGO'];
								$prod2['stock']=$prods2['prod_precos']['STOCK'];
								$prod2['promo']=$prods2['prod_precos']['PROMOCAO'];

								$prod2['destaque']=$prods2['prod_precos']['DESTAQUE'];
								$prod2['novo']=$prods2['prod_precos']['NOVO'];
								$prod2['outlet']=$prods2['prod_precos']['OUTLET'];

								$prod2['u_c']=$prods2['u_catetiq'];
								$prod2['u_i']=$prods2['u_imagetiq'];
								$prod2['u_co']=$prods2['u_coretiq'];


								show_blocos_destaque($prod2);
								$rs->db_next();
							}
							$tam=$rs->db_rows;
							$rs->db_close();


							$txt = ob_get_contents();
							$return .= $txt;

							ob_end_clean();
							$return .= '
							</ul>
						</div>
						<div class="rightbutton" id="goToNextSlide_promocao"></div>
						<div class="clear"></div>
					</div>
				</div>
			</div>';
	}
	/*


	if ($prn['bydef'] > 0)
	{
		//echo 'we are defined';

		$return .= '<div class="relacionados_wp col-md-12"><div class="titulo_a">'.getLinguas('txt_relacionados',$GLOBALS['site_lang']).'</div>';
		$return .= '<div class="clear"></div>';
		$return .= bloco_catalogo_fp_rela();
		$return .=  '</div>';
	}
	if ($prn['default'] > 0 && $prn['bydef']==0)
	{
		//echo 'we are by default';

		$return .= '<div class="relacionados_wp col-md-12"><div class="titulo_a">'.getLinguas('txt_relacionados',$GLOBALS['site_lang']).'</div>';
		$return .= '<div class="clear"></div>';
		$return .= bloco_catalogo_fp_rela();
		$return .=  '</div>';
	}*/

    return $return;
}

function ficha_produto_nstandard__grafica($prods)
{
	$not_produto_standard=1;
	$c_submissao_ficheiro=$prods['c_submissao_ficheiro'];
    $dados_cliente=cliente_logado();
	$return='<link rel="stylesheet" href="layout/'.PROJETO.'/css/css_external/product_v_'.filemtime('layout/'.PROJETO.'/css/css_external/product.css').'.css" media="all"/> ';

	$return .= '<script>quantityAvailable=9999;</script>';
	$return .= '<script type="text/javascript" src="layout/'.PROJETO.'/css/css_external/product.js"></script> ';
    $return .= BREADCRUMBS(1, intval($prods['fam_id']));

	$tem_qtt=0;$label_promo='';
	$precos=$prods['prod_precos'];
	$PODE_ENCOMENDAR=$precos['PODE_ENCOMENDAR'];

    $return .= '<div class="primary_block row"><div class="container"><div class="top-hr"></div></div>';
	$return.='<input type="hidden" id="produto_standard" name="produto_standard" value="0">';

	//************************************************

		$return .= '<div class="pb-left-column col-xs-12 col-sm-12 col-md-6">';
		$return .= '<h1 itemprop="name" class="titulo-prod" style="padding-left:30px">'.rtrim($prods['prod_nome'], '-').'</h1>';
	    $tam=count($prods['prd_imgs']['img_cart']);
		if($prods['u_catetiq'] )
		{
			if($prods['u_imagetiq'] != '')
			{
				$temp=explode(".",$prods['u_imagetiq']);
				if(strtolower(end($temp)) == "pdf")
				{
					$return .= '<a href="'.$prods['u_imagetiq'].'" target="_blank" class="eenergia" style="background-color:'.$prods['u_coretiq'].';border-right-color:'.$prods['u_coretiq'].'">'.$prods['u_catetiq'].'</a>';
				}
				else
				{
					$return .= '<a class="eenergia fancybox" style="background-color:'.$prods['u_coretiq'].';border-right-color:'.$prods['u_coretiq'].';cursor:pointer;" href="'.$prods['u_imagetiq'].'">'.$prods['u_catetiq'].'</a>';
				}
			}
			else
			{
				$return .= '<div class="eenergia" style="background-color:'.$prods['u_coretiq'].';border-right-color:'.$prods['u_coretiq'].'">'.$prods['u_catetiq'].'</div>';
			}
		}
		$return .= "<div class='product-gallery-block resp1' style=\"padding-left:30px\">";
		$return .= '<div class="campanhas-prods" style="display:none;position:absolute;top:0;z-index:9999;height:400px"></div>';
		$return .= "<ul class='product-gallery' style='height:400px'>";
		if (!substr_count($prods['prd_imgm_med'], 'no_photo'))
		{
			for ($figi=0;$figi < $tam;$figi++)
			{
				if($figi==0)
				{
					$return .= '<li class="item active" data-thumb="'.$prods['prd_imgs']['img_med'][$figi].'">';
					$return .= $label_promo;
					$return .= '<span class="helper"></span>';
					if($tam>1)$return .= '<a href="'.$prods['prd_imgs']['img'][$figi].'" target="_blank" class="fancybox" id="big'.$figi.'" data-fancybox-group="gallery"><img class="zoom" src="'.$prods['prd_imgs']['img_med'][$figi].'" alt="'.rtrim($prods['prod_nome'], '-').'" border="0" /></a>';
					else $return .= '<a href="'.$prods['prd_imgs']['img'][$figi].'" target="_blank" class="fancybox" id="big'.$figi.'" data-fancybox="gallery"><img class="zoom" src="'.$prods['prd_imgs']['img_med'][$figi].'" alt="'.rtrim($prods['prod_nome'], '-').'" border="0" /></a>';
					$return .= '</li>';
				}
				else
				{
					$return .= '<li class="item" data-thumb="'.$prods['prd_imgs']['img_med'][$figi].'">'.$label_promo.'<span class="helper"></span><a href="'.$prods['prd_imgs']['img'][$figi].'" target="_blank" class="fancybox" id="big'.$figi.'" data-fancybox-group="gallery"><img src="'.$prods['prd_imgs']['img_med'][$figi].'" alt="'.rtrim($prods['prod_nome'], '-').'" border="0" /></a></li>';
				}
			}
		}
		else
		{
			$return .= '<li class="item active" data-thumb="'.$prods['prd_imgs']['img_med'][$figi].'">'.$label_promo.'<span class="helper"></span><img src="'.$prods['prd_imgm_med'].'" alt="'.rtrim($prods['prod_nome'], '-').'" border="0" /></li>';
		}
		$return .= '</ul>';
		$return .= "</div>";
		$return .= "<div class='product-gallery-block resp2'>";
		$return .= '<div class="campanhas-prods" style="display:none;position:relative;z-index:9999"></div>';
		$return .= "<ul class='product-gallery-resp'>";

		if (!substr_count($prods['prd_imgm_med'], 'no_photo'))
		{
			for ($figi=0;$figi < $tam;$figi++)
			{
			if($figi==0) {$return .= '<li class="item active" data-thumb="'.$prods['prd_imgs']['img_med'][$figi].'">'.$label_promo.'<span class="helper"></span><a href="'.$prods['prd_imgs']['img'][$figi].'" target="_blank" class="fancybox" id="big'.$figi.'" data-fancybox-group="gallery"><img src="'.$prods['prd_imgs']['img_med'][$figi].'" alt="'.rtrim($prods['prod_nome'], '-').'" border="0" /></a></li>';}
			else {$return .= '<li class="item" data-thumb="'.$prods['prd_imgs']['img_med'][$figi].'">'.$label_promo.'<span class="helper"></span><a href="'.$prods['prd_imgs']['img'][$figi].'" target="_blank" class="fancybox" id="big'.$figi.'" data-fancybox-group="gallery"><img src="'.$prods['prd_imgs']['img_med'][$figi].'" alt="'.rtrim($prods['prod_nome'], '-').'" border="0" /></a></li>';}
			}
		}
		else
		{
			$return .= '<li class="item active"  data-thumb="'.$prods['prd_imgs']['img_med'][$figi].'">'.$label_promo.'<span class="helper"></span><img src="'.$prods['prd_imgm_med'].'" alt="'.rtrim($prods['prod_nome'], '-').'" border="0" /></li>';
		}
		$return .= '</ul>';
		$return .= '</div>';
	    $tem_filtros=0;
	    $array_filtro = filtros_ficha_produto_grafica($prods['prod_id'], $prods['fam_id']);
	    $filtros=$array_filtro['html'];
	    $prod_combo=$array_filtro['combo'];
		$quantidade_id=$array_filtro['quantidade'];

		if ($filtros) $tem_filtros=1;

		$return .= '</div>';
		$return .= '<div class="pb-right-column col-xs-12 col-sm-12  col-md-1"></div>';
		$return .= '<div class="pb-center-column col-xs-12 col-sm-12 col-md-5">
		<div class="row ">
		<div class="col-sm-12">
		';

		$return .= '<div class="inputs">';

		$filtros2 = filtros_lista_produto($prods['prod_id'], $prods['fam_id']);
		if($filtros2)
		{
			$return .= '<div class="descricao">';
			$return .= $filtros2;
			$return .= '</div>';
		}

		$return .= '</div></div></div><div class="clear"></div>';
		$return .= '<div class="box-info-product">';
		$return .='<div class="container_caracteristicas">';


		$descricao_verifica = strip_html_tags($prods['prod_descricao']);
		$detalhe_verifica = strip_html_tags($prods['prod_como_funciona']);
		$ficha_verifica = strip_html_tags($prods['prod_envio']);
		$prod_video = $prods['prod_video'];
		$script=str_replace(array('http://youtu.be/', 'https://youtu.be/', 'http://www.youtube.com/watch?v=', 'https://www.youtube.com/watch?v='), '//www.youtube.com/embed/',$prod_video);


		if($descricao_verifica !='' || $detalhe_verifica != '' || $ficha_verifica != '' || $prod_video != '')
		{
			$return .= '<div class="horizontalTab">
			<ul class="resp-tabs-list">
			'.($descricao_verifica!='' ? '<li style="width:50%">'.getLinguas('txt_descricao',$GLOBALS['site_lang']).'</li>' :'').'
			'.($detalhe_verifica!='' ? '<li style="width:50%">'.getLinguas('key_detalhes',$GLOBALS['site_lang']).'</li>' :'').'
			'.($ficha_verifica!='' ? '<li style="width:50%">'.getLinguas('key_ficha_tecnica',$GLOBALS['site_lang']).'</li>' :'').'
			'.($prods['prod_ee'] != '' || $prods['prod_ue'] != '' || $prods['prod_dt'] != '' ? '<li>'.getLinguas('key_outros_fxs',$GLOBALS['site_lang']).'</li>' :'').'
			'.($prod_video != '' ? '<li style="width:50%">'.getLinguas('key_video',$GLOBALS['site_lang']).'</li>' :'').'
			'.($prod_video != '' ? '<li style="width:50%">'.getLinguas('key_video',$GLOBALS['site_lang']).'</li>' :'').'
			</ul>
			<div class="resp-tabs-container" style="padding:0px">

			'.($descricao_verifica!='' ? '<div><p>'.nl2br(strip_html_tags_fp($prods['prod_descricao2'])).'</p></div>' :'').'
			'.($detalhe_verifica!='' ? '<div><p>'.$prods['prod_como_funciona'].'</p></div>' :'').'
			'.($ficha_verifica!='' ? '<div><p>'.$prods['prod_envio'].'</p></div>' :'').'
			'.($prods['prod_ee'] != '' || $prods['prod_ue'] != ''  || $prods['prod_dt'] != '' ? '<div><p>' :'').'
			'.($prods['prod_ee'] != '' ? '<a style="color:#666;text-decoration:none;text-transform:uppercase;font-weight:bold" href="'.$prods['prod_ee'].'" target="_blank">&#9658;&nbsp;&nbsp;'.getLinguas('key_etiqueta_energ',$GLOBALS['site_lang']).'</a><br>' :'').'
			'.($prods['prod_ue'] != '' ? '<a style="color:#666;text-decoration:none;text-transform:uppercase;font-weight:bold" href="'.$prods['prod_ue'].'" target="_blank">&#9658;&nbsp;&nbsp;'.getLinguas('key_fx_produto_ue',$GLOBALS['site_lang']).'</a><br>' :'').'
			'.($prods['prod_dt'] != '' ? '<a style="color:#666;text-decoration:none;text-transform:uppercase;font-weight:bold" href="'.$prods['prod_dt'].'" target="_blank">&#9658;&nbsp;&nbsp;'.getLinguas('key_desenho_tecnico',$GLOBALS['site_lang']).'</a>' :'').'
			'.($prods['prod_ee'] != '' || $prods['prod_ue'] != '' || $prods['prod_dt'] != '' ? '</p></div>' :'').'
			'.($prod_video!='' ? '<div><p><iframe width="100%" height="450" src="'.$script.'" frameborder="0" allowfullscreen></iframe></p></div>' :'').'

			</div>
			</div>';
		}


		//submição de ficheiros :
		$prods['dpi'] = '300';
		$prods['formatos']='jpg;png;jpeg;JPG;PNG;JPEG;pdf;PDF;heic;HEIC';
		if($prods['dpi']!='' && $prods['formatos']!='' && $prods['c_submissao_ficheiro']==1)
		{
			$return.='<div class="container_qualidade_produto"><div class="imagem_sub_produto"><div><div class="nome_tab">Envia-nos o teu Ficheiro</div></div></div>';
			$return.='<div class="container_info_imagem_sub">';
			$link=seo('pages.php?page_id=114');
			//$return.='<p>Caso seja a primeira vez que esteja a utilizar os nossos serviços, ou tenha alguma dúvida <b><a class="fancy_content" href="'.$link.'?popup=1" target="_blank" >clique aqui</a></b>. </p><br>';
			$formatos=explode(';',$prods['formatos']);
			$tipo_ficheiros='';
			$sting_para_java='';
			foreach($formatos as $type)
			{
				if($type!='')
				{
					if($tipo_ficheiros!='')
					{
						$tipo_ficheiros.=', '.$type;
						$sting_para_java.=';'.$type;
					}
					else
					{
						$tipo_ficheiros.=$type;
						$sting_para_java.=$type;
					}
				}
			}
			$return.="<p>DPI's ( resolução ) minimo recomendado:<b>".$prods['dpi']." dpi's</b></p>";
			$return.='<p>Formatos permitidos:<b>'.$tipo_ficheiros.'</b></p>';

			$return.='<div id="input_file"><input type="file" name="ficheiro_sub" id="ficheiro_sub" class="inputfile"><label id="ficheiro_sub_lable" for="ficheiro_sub">Insira o seu ficheiro</label></div>';
			$return.='</div></div>';
			$return.='<input type="hidden" id="ficheiros_permitidos" name="ficheiros_permitidos" value="'.$sting_para_java.'">
					  <input type="hidden" id="tem_ficheiro" name="tem_ficheiro" value="1">';
			?><script>
			   $( document ).ready(function()
			   {
			   		var inputs = document.querySelectorAll( ".inputfile" );
					var ficheiros_port=$("#ficheiros_permitidos").val();
					Array.prototype.forEach.call( inputs, function( input )
					{
						var label = input.nextElementSibling,
						labelVal = label.innerHTML;

						input.addEventListener( "change", function( f )
						{
							var fileName = f.target.value.split("\\").pop();
							if( fileName )
							{
								var formato_ficheiro_submetido=fileName.split(".").pop();
								var formatos=ficheiros_port.split(";");
								var erro=1;
								formatos.forEach(function(element) {
									if (element==formato_ficheiro_submetido)erro=0;
								});
								if(erro==1)
								{
									Apprise('O formato do ficheiro submetido não é permitido. <br><br>Em caso de duvida verifique a página de Ajuda ou contacte-nos.');
									$("#ficheiro_sub").val('');
									document.getElementById("ficheiro_sub_lable").innerHTML ="Insira o seu ficheiro";
								}
								else{
									document.getElementById("ficheiro_sub_lable").innerHTML = fileName;
								}
							}
							else
							{
								label.innerHTML = labelVal;
							}
						});
					});
				});
			</script><?php
		}


		$return .='<div class="caracteristicas_produto" style="margin-top:10px"><div><div class="resp-tab-item">Personalizar o artigo <b>'.$prods['prod_nome'].'</b></div></div></div>';
		if($filtros)
		{
			$filtros=str_replace('col-md-3', 'col-md-12', $filtros);
			$return .= '<div class="filtros_produtos">';
			$return .= $filtros;
			$return .= '</div>';
		}
		$return .='</div>';
		// vamos verificar se existe algum serviço associado ao produto
		$servicos=filtros_servicos($prods['prod_id']);
		if($servicos!='')
		{
			$return.= $servicos;
		}
		// vamos agora criar a tab de Quantidade
		$quantidade=filtros_quantidade($prods['prod_id'],$prod_combo);
		if($quantidade!='')
		{
			$return.='<div style="display:none" class="container_qualidade_produto"><div class="quantidade_produto"><div><div class="nome_tab">Quantidade</div></div></div>';
			$return.='<div class="container_info_quantidade">';
			$return.= $quantidade;
			$return.='</div></div>';
		}

		$return.='<input type="hidden" id="code" value="">';
		$return .='<div class="resumo_produto col-md-12"><div class="row">';
		$return .='<div class="col-md-12 row preco_prod">';
		$return .=gera_resumo_carrinho_grafica($prods['prod_id'],$prod_combo,$quantidade_id);
		$return .='</div>';

		$return .='<div class="col-md-12 resumo_produto_texto"> <p class="resumo">RESUMO</p>';
		$return .=gera_resumo($prods['prod_id'],$prod_combo,$quantidade_id);
		$return .='</div>';
		$return .='</div>';
		$return .='</div>';

    $return .= '</div><div class="clear"></div>';
	$return .= '</div>';

	$return .= '</div>';


    return $return;
}
function ficha_produto_nstandard_($prods)
{
	global $shop;
	$dados = cliente_logado();
	$loja_id = 0;
	if(isset($_GET['loja']) && $_GET['loja'] > 0)
	{
		$loja_id = intval($_GET['loja']);
	}


	if($prods['prod_precos']['STOCK'] > 0 && $prods['prod_precos']['STOCK'] < 3)$disponibilidade = 'Disponibilidade:<span class="limitado">Stock limitado</span>';
	elseif($prods['prod_precos']['STOCK'] >= 3)$disponibilidade = 'Disponibilidade:<span class="disponivel">Em stock</span>';
	else $disponibilidade = 'Disponibilidade:<span class="esgotado">Esgotado</span>';


	$return = '<link rel="stylesheet" href="layout/'.PROJETO.'/css/css_external/product.css" media="all"/>';
	$return .= '<script>quantityAvailable = 9999;</script>';
	$return .= '<script type="text/javascript" src="layout/'.PROJETO.'/css/css_external/product.js"></script>';
	$return .= BREADCRUMBS(1);
	$return .= '<div class="primary_block row"><div class="container"><div class="top-hr"></div></div>';
	$tam = count($prods['prd_imgs']['img_cart']);
	$return .= '<div class="clear"></div>';
	$return .= '<div class="clear"></div>';


	$return .= '<div class="pb-left-column col-xs-12 col-sm-6">';
	$return .= '<div class="class-1"><ul class="class-1 product-gallery">';
	if (!substr_count($prods['prd_imgm_med'], 'no_photo'))
	{
		for ($figi=0;$figi < $tam;$figi++)
		{
			if($figi==0)
			{
				$return .= '<li class="item active" data-thumb="'.$prods['prd_imgs']['img_med'][$figi].'">';
				$return .= $label_promo;
				$return .= '<span class="helper"></span>';
				$return .= '<a href="'.$prods['prd_imgs']['img'][$figi].'" target="_blank" class="fancybox" id="big'.$figi.'" data-fancybox-group="gallery"><img class="zoom" src="'.$prods['prd_imgs']['img_med'][$figi].'" alt="'.rtrim($prods['prod_nome'], '-').'" border="0" /></a>';
				$return .= '</li>';
			}
			else
			{
				$return .= '<li class="item" data-thumb="'.$prods['prd_imgs']['img_med'][$figi].'">'.$label_promo.'<span class="helper"></span><a href="'.$prods['prd_imgs']['img'][$figi].'" target="_blank" class="fancybox" id="big'.$figi.'" data-fancybox-group="gallery"><img src="'.$prods['prd_imgs']['img_med'][$figi].'" alt="'.rtrim($prods['prod_nome'], '-').'" border="0" /></a></li>';
			}
		}
	}
	else
	{
		$return .= '<li class="item active"  data-thumb="'.$prods['prd_imgs']['img_med'][$figi].'">'.$label_promo.'<span class="helper"></span><img src="'.$prods['prd_imgm_med'].'" alt="'.rtrim($prods['prod_nome'], '-').'" border="0" /></li>';
	}

	$return .= '</ul></div>';

	$return .= '<div class="class-2"><ul class="product-gallery2">';

	if (!substr_count($prods['prd_imgm_med'], 'no_photo'))
	{
		for ($figi=0;$figi < $tam;$figi++)
		{

		if($figi==0)
		{
			$return .= '<li class="item active" data-thumb="'.$prods['prd_imgs']['img_med'][$figi].'">';
			$return .= $label_promo;
			$return .= '<span class="helper"></span>';
			$return .= '<a href="'.$prods['prd_imgs']['img'][$figi].'" target="_blank" class="fancybox" id="big'.$figi.'" data-fancybox-group="gallery"><img class="zoom" src="'.$prods['prd_imgs']['img_med'][$figi].'" alt="'.rtrim($prods['prod_nome'], '-').'" border="0" /></a>';
			$return .= '</li>';
		}
		else
		{
			$return .= '<li class="item" data-thumb="'.$prods['prd_imgs']['img_med'][$figi].'">'.$label_promo.'<span class="helper"></span><a href="'.$prods['prd_imgs']['img'][$figi].'" target="_blank" class="fancybox" id="big'.$figi.'" data-fancybox-group="gallery"><img src="'.$prods['prd_imgs']['img_med'][$figi].'" alt="'.rtrim($prods['prod_nome'], '-').'" border="0" /></a></li>';}
		}
	}
	else
	{
		$return .= '<li class="item active"  data-thumb="'.$prods['prd_imgs']['img_med'][$figi].'">'.$label_promo.'<span class="helper"></span><img src="'.$prods['prd_imgm_med'].'" alt="'.rtrim($prods['prod_nome'], '-').'" border="0" /></li>';
	}

	$return .= '</ul></div>';

	$precos = $prods['prod_precos'];
	$tem_filtros = 0;
	$filtros = filtros_ficha_produto($prods['prod_id'],$prods['prod_ref_pai'], $prods['fam_id']);
	if($filtros) $tem_filtros = 1;

	$return .= '<div class="box-info-product" style="padding:30px 20px">';
	$return .='<div class="row">
		   <h1 itemprop="name">'.rtrim($prods['prod_nome'], '-').'</h1>';
	$return .= '<div class="inputs">';
	if ($prods['prod_ref'] != '') $return .= '<div class="ref" title="Referência"><span class="car">Referência:</span><span class="car2 ">'.$prods['prod_ref'].'</span></div>';

	if ($prods['prod_ref_ean'] != '') $return .= '<div class="ref" title="EAN"><span class="car">EAN:</span><span class="car2 ">'.$prods['prod_ref_ean'].'</span></div>';

	$return .= '</div>';
	$tem_qtt = 0;
	if ($precos['STOCK'] > 0 && $prods['prod_precos']['PVP'] > 0 && $prods['prod_carrinho'] == 1 && 1)
	{
		if ($prods['prod_descricao'] != '')
		{
			$return .= '<div class="descricao clear">'.$prods['prod_descricao'].'</div>';
		}

		//tamanhos
		// if($tem_filtros) $return .= $filtros;

	}

	$return .= '</div><div class="clear"></div>';
	if ($precos['STOCK'] > 0 && $prods['prod_precos']['PVP'] > 0 && $prods['prod_carrinho'] == 1)
	{

	}
	elseif($dados)
	{
		$return .= '<div class="produto_estado_stock">'.$disponibilidade.'</div>';
		$return .= '<div class="fp_comprar_esgotado fancy_stock" href="'.$GLOBALS['site_http_ajax_url'].'setNotificacaoProduto.php?idprod='.$prods['prod_id'].'">Esgotado - Deseja receber uma notificação quando o produto estiver disponível? Deixe-nos o seu e-mail.</div>';
	}

	$return .= '</div>';


	$return .='<div class="clear"></div>';
	//accordions
	if (trim(strip_tags($prods['prod_como_funciona'])) != "" || trim(strip_tags($prods['prod_features'])) != "")
	{
		$return .='<div class="especificacoes">';
		if (trim(strip_tags($prods['prod_como_funciona'])) != "")
		{
			$return .='<div class="panel-group accordion xpto1" id="accordion" role="tablist" aria-multiselectable="true">';
			$return .='		<div class="panel panel-default">
								<div class="panel-heading" role="tab" id="heading_1">
								    <div class="panel-title">
								        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse_1" aria-expanded="true" aria-controls="collapse_1" onclick="plusminus(1,this)">
								            MAIS DETALHES
								            <span class="accplus" id="accplus1" style="float:right;" data-open="0">+</span>
								        </a>

								    </div>
								</div>
						        <div id="collapse_1" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading_1">
									<div class="panel-body">'.$prods['prod_como_funciona'].'</div>';
		    $return .='			</div>
							</div>
						</div>';
		}

		if (trim(strip_tags($prods['prod_features'])) != "")
		{
			$return .='<div class="panel-group accordion xpto2" id="accordion" role="tablist" aria-multiselectable="true">';
			$return .='		<div class="panel panel-default">
								<div class="panel-heading" role="tab" id="heading_2">
								    <div class="panel-title">
								        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse_2" aria-expanded="true" aria-controls="collapse_2" onclick="plusminus(2,this)">
								            INFORMAÇÃO ADICIONAL
								            <span class="accplus" id="accplus2" style="float:right;" data-open="0">+</span>
								        </a>
								    </div>
								</div>
						        <div id="collapse_2" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading_2">
									<div class="panel-body">'.$prods['prod_features'].'</div>';
		   $return .='			</div>
							</div>
						</div>';
		}

		$return .= '</div>';
	}
	$return .= '<div class="clear"></div>';
	//--FIM accordions--
	$return .= '<div class="share container" style="padding-right:0px;">';
	$return .= '<div style="float:left;width:100%;padding-top:23px;font-size:11px">';

	/**********************************************/

	$dados = cliente_logado();
	$cli_id = $dados['cli_id'];

	$sql='SELECT * from clientes_wishlist WHERE CLIENT_ID='.$cli_id.' AND PROD_ID='.$prods['prod_id'];
	//echo $sql;
	global $DB_host, $DB_user, $DB_pass, $DB_name,$site_lang;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rsw = new DataObj;

	$ewishlist = 0;
	$rsw->db_open($db_link, $GLOBALS['DB_name'], $sql);
	if(!$rsw->EOF())
	{
		$ewishlist = 1;
	}
	$rsw->db_close();

	/**
		echo '<pre>';
		print_r($prods);
		echo '</pre>';
	/**/
	if($cli_id>0)
	{
		$returnwn = '<div class="wishlist-heart" style="">Wishlist</div> <span style="cursor:pointer;" class="produtowl'.$prods['prod_id'].' " href="#" onclick="addProductWishList(\''.$prods['prod_id'].'\');return false;"><span title="Adicionar à Wishlist"> <i class="fa fa-coracao-o GTM_adicionar_wishlist" aria-hidden="true"></i></span></span>';

		$returnwy = '<div class="wishlist-heart " style="">Wishlist</div> <span style="cursor:pointer;" class="produtowl'.$prods['prod_id'].' " href="#" onclick="removeProductWishList(\''.$prods['prod_id'].'\',\'0\');return false;"><span title="Remover da Wishlist"> <i class="fa fa-coracao GTM_remover_wishlist" aria-hidden="true"></i></span></span>';
	}
	//$returnwy = '<span style="cursor:pointer;" class="produtowl'.$prods['prod_id'].'  active_heart" href="#" onclick="removeProductWishList(\''.$prods['prod_id'].'\');return false;"><span title="Adicionar à Wishlist"><i class="fa fa-star GTM_remover_wishlist" aria-hidden="true"></i></span></span>';

	$return .= $ewishlist?$returnwy:$returnwn;

	// $return .= '<div class="icon-coracao prd_whish produtowl'.$prods['prod_id'].'"  onclick="addProductWishList(\''.$prods['prod_id'].'\');" style="cursor:pointer;">Wishlist &#x2661;</div>';
	$return .= '<div class="memo-social no-print" style="float:right;text-align:right;"></div>';
	$return .= '</div>';
	$return .= '</div>';
	$return .= '</div>';

	$return .= '<script>';
	$return .= 'function plusminus(num,obj){
					$(".accplus").html("+");
					if($("#accplus"+num).attr("data-open")!=1){
						$("#accplus"+num).attr("data-open",1);
						$("#accplus"+num).html("-");
						return true;
					}
					$(".accplus").attr("data-open",0);
				}';
	$return .= '</script>';

	$return .= '<div class="pb-right-column col-xs-12 col-sm-6" >';

	$produtosrelacionados = hasProdutosRelacionados($prods['prod_id']);
	if ($produtosrelacionados > 0)
	{
		$return .= '<div class="c"></div>';
		$return .= bloco_catalogo_fp_filtros();
	}

	$tem_filtros = 0;
	$filtros = filtros_ficha_produto($prods['prod_id'],$prods['prod_ref_pai'], $prods['fam_id']);


	if($filtros) $tem_filtros = 1;
	//tamanhos
	if($tem_filtros) $return .= $filtros;

	//$return .= alterarproductloja();

	if($loja_id == 0)
	{


	}
	else
	{
		$t = $_GET['o']==1?1:0;
		$order = array(0=>'ASC',1=>'DESC');//isset($_GET['o']==1)
		$sql='SELECT * from products_objs WHERE obj_type_id='.$prods['prod_id'].' AND obj_store = "'.$loja_id.'" AND obj_type="products" order by obj_pvp '.$order[$t].' ';
		//echo $sql;
		global $DB_host, $DB_user, $DB_pass, $DB_name,$site_lang;
		$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
		$rs = new DataObj;
		$rs2 = new DataObj;

		$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
		if(!$rs->EOF())
		{
			$loja_nome='';
			$obj_store=$rs->db_data['obj_store'];
			$obj_pvp=$rs->db_data['obj_pvp'];
			$sql='SELECT * from logins_lojas WHERE ID='.$obj_store;
			$rs2->db_open($db_link, $GLOBALS['DB_name'], $sql);
			if(!$rs2->EOF())
			{
				$loja_nome=$rs2->db_data['Nome'];
			}
			$rs2->db_close();
		}
		$rs->db_close();



		$return .= '<div style="width:100%;background-color:#F9F9F9;display:table;padding:30px">';

		$return .= '<div style="width:100%;height:50px">';
		$return .= '<div style="float:left;width:50%">
			<div style="color:#007DC2;font-size:16px;font-weight:300;float:left">'.$loja_nome.'</div>
			<div style="margin-left:10px;color:#000;font-size:12px;font-weight:300;float:left">(945 vendas)</div>
			<div style="float:left;width:100%">';
			$return .= '<i class="fa fa-star disable" aria-hidden="true"></i>';
			$return .= '<i class="fa fa-star blue" aria-hidden="true"></i>';
			$return .= '<i class="fa fa-star blue" aria-hidden="true"></i>';
			$return .= '<i class="fa fa-star blue" aria-hidden="true"></i>';
			$return .= '<i class="fa fa-star blue" aria-hidden="true"></i>';
			$return .= '</div>
			</div>';

		$valor_portes = '';
		$prazo_de_entrega = '';
		$sql='SELECT valor_portes, prazo_de_entrega from lojas WHERE ID_PAI='.$obj_store;
		$rs2->db_open($db_link, $GLOBALS['DB_name'], $sql);
		if(!$rs2->EOF())
		{
			if($rs2->db_data['valor_portes'] != '')$valor_portes='<div>Custos de envio:'.number_format($rs2->db_data['valor_portes'],2).'€</div>';
			if($rs2->db_data['prazo_de_entrega'] != '') $prazo_de_entrega='<div>Prazo de entrega:'.$rs2->db_data['prazo_de_entrega'].'</div>';
		}
		$rs2->db_close();

		$return .= '<div style="float:left;width:50%;text-align:right">
			<div style="color:#007DC2;font-size:18px;font-weight:600;float:left;width:100%;height:30px">'.number_format($obj_pvp,2).'€</div>
			<div style="color:#000;font-size:12px;font-weight:300;float:left;width:100%;height:30px">'.$valor_portes.$prazo_de_entrega.'999</div></div>';
		$return .= '</div>';

		$return .= '<div style="float:left;width:100%;color:#000;margin-top:20px;text-align:center;background-color:#F0F0F0"><a class="form-control btn btn-success btn-block btn-lg" href="#">COLOCAR UMA QUESTÃO AO VENDEDOR</a></div>';
		$return .= '<div onclick="$(\'#aproductnamef\').text($(\'#aproductname\').text());" style="float:left;width:100%;color:#000;margin-top:5px;text-align:center;background-color:#F0F0F0"><a class="form-control btn btn-success btn-block btn-lg" href="#errorreport" class="fancybox" data-width="1200" onclick="$(\'#reportsuccess\').html(\'\');">REPORTAR UM ERRO</a></div>';
		$return .= '</div>';

		// //tamanhos
		$return .= '<br>';
		$return .= '<div class="produto_estado_stock">'.$disponibilidade.'</div>';
		$return .= '<div class="product_attributes clear">
					<div class="quantidade" id="quantity_wanted_p">
						<div class="quantidade-input">
							<input type="text" value="1" class="text" id="quantity_wanted" name="qty">
						</div>
						<div class="quantidade-buttons" style="width:30px;float:left;">
							<a class="product_quantity_up" data-field-qty="qty" href="" style="width:100%;float:left;">
								<span>
									<i class="fa fa-plus"></i>
								</span>
							</a>
							<a class="product_quantity_down" data-field-qty="qty" href="#" style="width:100%;float:left;">
								<span>
									<i class="fa fa-minus"></i>
								</span>
							</a>

						</div>
					</div>
					</div>';

		$return .= '<div class="box-cart-bottom">
		<div class="buttons_bottom_block no-print" id="add_to_cart">';

		//antes de permitir a compra verificar si o EAN é único
		$rs3 = new DataObj;
	    $sql3 = 'select ID from products where ref_ean="'.$prods['prod_ref_ean'].'" and ref_ean!=""';
	    $rs3->db_open($db_link, $GLOBALS['DB_name'], $sql3);
	    if($rs3->db_rows>0 && $rs3->db_rows==1){
			$return .= '<button  class="ajax_add_to_cart_product_button fp_comprar" onclick="adicionarCarrinhoProdutoOpcoes('.$prods['prod_id'].',$(\'#quantity_wanted\').val(),'.intval($_GET['loja']).');" type="button">
			<span>'.getLinguas('key_adicionar').'<i class="fa fa-angle-right"></i></span>
			</button>';
		}else{
			$return .= '<div style="color:#ff0000;font-weight:bold;font-size:20px;line-height:1.3;">O botão de compra não é apressentado porque o produto apresentado contém erros na sua configuração EAN. A Fisiopartner agradeçe ao o report do erro mencionado</div>';
		}

		$return .= '</div>
		</div>';


		$return .= '<div style="width:100%;height:50px;margin-top:50px">';
		$return .= '<div style="float:left;width:50%;color:#007DC2">Outras ofertas</div>';
		$return .= '<div style="float:left;width:50%;text-align:right;padding:10px 0 10px 10px">';
		$al = explode("&",(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" :"http")."://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}");

			$actual_link = 	$al[0];

		$return .= '<select style="border:none;border-top:solid 1px;width:150px;padding:5px;" onchange="reorder(this.value,\''.$actual_link.'\')">';
		$return .= '<option value="" '.($_GET['o']==0?'selected':'').'>Mais Barato</option>';
		$return .= '<option value="1" '.($_GET['o']==1?'selected':'').'>Mais Caro</option>';
		$return .= '</select>';
		$return .= '</div>';
		$return .= '</div>';
		$return .= '<script>
			function reorder(val,url){
				if(val=="" || val==0)
				window.open(url,"_top");
				else
				window.open(url+"&o="+val,"_top");
			}
		</script>';
	}


	$return .= '<div id="errorreport" style="display:none" data-width="1200">';
	$return .= '<h2><span id="aproductnamef"></span></h2>';
	$return .= '<input type="hidden" class="form-control" placeholder="product" name="id_prod" value="'.$_GET['prod_id'].'">';



	$return .= '<textarea id="producterrortext" class="form-control" placeholder="Message de erro" style="width:400px;"  rows="4" ></textarea>';
	$return .= '<div class="ver_mais" onclick="reportaerro('.$prods['prod_id'].')" "><a>Submeter</a></div><span id="reportsuccess"></span>';
	$return .= '</div>';

	$return .= '<script>';
	$return .= 'function reportaerro(id){';
	$return .= 'var erro = $("#producterrortext").val();';
	$return .= 'var params = {"id" :id,"producterro" :erro };';

	/**/
	$return .= 'if(erro!=""){';
	$return .= '$.ajax({
				        data: params,
				        url:  "'.$GLOBALS['site_http_url'].'ajax/producterror.php",
				        dataType:"json",
				        success:function (json) {
				          $("#reportsuccess").html("<div class=\'alert\'>Obrigado<br>O seu reporte de erro foi submetido com sucesso<a href=\'#\' class=\'close\' data-dismiss=\'alert\' aria-label=\'close\' title=\'close\'>×</a></div>");
				          $("#producterrortext").val(\'\');
				        },
				type: "post"
				    });';
				    /**/
	$return .= '}else{';
	$return .= '$("#reportsuccess").html("<div class=\'alert\' style=\'color:red;\'>O seu reporte ainda não foi preenchido<a href=\'#\' class=\'close\' data-dismiss=\'alert\' aria-label=\'close\' title=\'close\'>×</a></div>");';
	$return .= '}';
	$return .= '}';
	$return .= '</script>';
	$return .= '</div><div class="clear"></div></div>';
	$return .= '<div class="clear"></div>';

	$produtosrelacionados = hasProdutosRelacionados($prods['prod_id']);
	if ($produtosrelacionados > 0)
	{
		$return .= '<div class="relacionados_wp clear"><div class="titulo_a">PRODUTOS RELACIONADOS</div>';
		$return .= '<div class="c"></div>';
		$return .= bloco_catalogo_fp();
		$return .= '</div>';
	}

	return $return;
}
function getProdutosAlternativos($id_marca, $prod_id=0)
{
    global $DB_host,$DB_user,$DB_pass, $DB_name;

    $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new DataObj;

    $result='';
	// produtos_alternativos
    $sql='
			SELECT
				ID AS idprod
			FROM
				products
			WHERE
				ID_ORG='.$id_marca.'
				AND ID<>'.$prod_id.'
			ORDER BY RAND()
			LIMIT 50

		';
    // echo $sql;
    $rs->db_open($db_link, $GLOBALS['DB_name'], $sql);

    if (!$rs->EOF()) {
        $i=0;
        $z=0;

        while (!$rs->EOF()) {
            $i++;
            $prod_id=$rs->db_data['idprod'];
            $prods=get_produtc_info($prod_id);
            if ($prods && !substr_count($prods['prd_imgm_new'], 'no_photo') && $z <= 3) {
                $result .= '<li>'.mostra_bloco_simples($prods).'</li>';

                $z++;
            }
            $rs->db_next();
        }


    }
    $rs->db_close();
    return $result;
}

function getProdutosColecao($id_marca, $prod_id)
{
    global $DB_host,$DB_user,$DB_pass, $DB_name;

    $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new DataObj;

    $result='';
	// produtos_alternativos
    $sql='
			SELECT
				ID_Prod2 AS idprod
			FROM
				produtos_alternativos
			WHERE
				ID_Prod='.$prod_id.'
				AND ID_Prod2<>'.$prod_id.'
			GROUP BY
				ID_Prod2

		';
    // echo $sql;
    $rs->db_open($db_link, $GLOBALS['DB_name'], $sql);

    if (!$rs->EOF()) {
        $i=0;
        $z=0;

        while (!$rs->EOF()) {
            $i++;
            $prod_id=$rs->db_data['idprod'];
            $prods=get_produtc_info($prod_id);
            if ($prods) {
                $result .= '<li>'.mostra_bloco_simples($prods).'</li>';
            }
            $rs->db_next();
        }

        // $result.='<div class="clear"></div>';

    }
    $rs->db_close();
    return $result;
}

function getProdutosRelacionados2($prod_id)
{
    global $DB_host,$DB_user,$DB_pass, $DB_name;
    $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new DataObj;
    // $prods=get_produtc_info($prod_id);
    // $idfam=$prods['fam_id'];

    $result='';
    $sql='
			SELECT
				ID_Prod2 AS idprod
			FROM
				produtos_opcoes
			WHERE
				ID_Prod='.$prod_id.'
				AND ID_Prod2<>'.$prod_id.'
			GROUP BY
				ID_Prod2

		';
    // echo $sql;
    $rs->db_open($db_link, $GLOBALS['DB_name'], $sql);

    if (!$rs->EOF()) {
        $i=0;
        $z=0;

        while (!$rs->EOF()) {
            $i++;
            $prod_id=$rs->db_data['idprod'];
            $prods=get_produtc_info($prod_id);
            if ($prods) {
                $result .= '<li>'.mostra_bloco_simples($prods).'</li>';
            }
            $rs->db_next();
        }

        // $result.='<div class="clear"></div>';

    }
    $rs->db_close();


    return $result;
}

function filtros_lista_produto2($prod_id=0, $fam_id=0)
{
    global $DB_host,$DB_user,$DB_pass, $DB_name, $site_lang;
    $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $obj2=new DataObj;
    $cores=new DataObj;
    $filtros=array();
    $return='';
    $sql2='
			SELECT
				fp.cat_id, fp.valor_id, fp.filtro_area
			FROM
				filters_products fp
			WHERE
				prod_id='.$prod_id.'
				AND fp.filtros_rel2 <> ""
				AND fp.filtro_active=1
				AND  (fp.filtro_area=0)
			GROUP BY
				fp.valor_id, fp.cat_id
			ORDER BY
				fp.valor_id DESC
			';

    $obj2->db_open($db_link, $DB_name, $sql2);
    $a=0;
    while (!$obj2->EOF()) {
        $filtros[$obj2->db_data['cat_id']][]=$obj2->db_data['valor_id'];

        $obj2->db_next();
    }
    $obj2->db_close();

    foreach ($filtros as $key => $value) {
        $cat=buscacategoriaTipoeNome($key);
        $category_type=buscacategoriaTipofiltros($key);

        $return .= '<div class="spacing"><span class="car">'.$cat['category_name'].':</span>';

        for ($a=0;$a < count($value);$a++) {
            $nm_value=buscavalorfiltros_arr($value[$a]);
            $return .= '<span class="car2">'.$nm_value['values_name'].'</span>';
        }

        $return .= '</div>';
    }

    return $return;
}

function filtros_lista_produto3($prod_id=0, $fam_id=0)
{
    global $DB_host,$DB_user,$DB_pass, $DB_name, $site_lang;
    $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $obj2=new DataObj;
    $cores=new DataObj;
    $filtros=array();
    $return='';
    $sql2='
			SELECT
				fp.cat_id, fp.valor_id, fp.filtro_area
			FROM
				filters_products fp
			WHERE
				prod_id='.$prod_id.'
				AND fp.filtros_rel2 <> ""
				AND fp.filtro_active=1
				AND  (fp.filtro_area=2 OR fp.filtro_area=1)
			GROUP BY
				fp.valor_id, fp.cat_id
			ORDER BY
				fp.valor_id DESC
			';
    $obj2->db_open($db_link, $DB_name, $sql2);
    $a=0;
    while (!$obj2->EOF()) {
        $filtros[$obj2->db_data['cat_id']][]=$obj2->db_data['valor_id'];

        $obj2->db_next();
    }
    $obj2->db_close();

    foreach ($filtros as $key => $value) {
        $cat=buscacategoriaTipoeNome($key);
        $category_type=buscacategoriaTipofiltros($key);

        // $return .= '<div class="spacing"><span class="car">'.$cat['category_name'].':</span>';

        for ($a=0;$a < count($value);$a++) {
            $nm_value=buscavalorfiltros_arr($value[$a]);
            $return .= ', '.$nm_value['values_name'].'';
        }


    }

    return $return;
}

function stock_plataforma()
{
	global $DB_host,$DB_user,$DB_pass, $DB_name, $site_lang;
    $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new DataObj;
    $rs2='';

    $sql ="SELECT  * FROM modulos_config_site";
    $rs->db_open($db_link, $DB_name, $sql);
    if(!$rs->EOF()) $rs2 = $rs->db_data;
    $rs->db_close();
    return $rs2;
}

function get_stock_text($prod_id = 0)
{
	if(!isset($_SESSION))session_start();
	if(isset($_SESSION['configs']))
	{
		$configs = $_SESSION['configs'];
	}
	else
	{
		$_SESSION['configs']=$configs = stock_plataforma();
	}

	$stock_txt='';

	$precos = get_produtc_price($prod_id);

	if($configs['stock_verde_ativo'] == 1 && $precos['STOCK'] >= $configs['stock_verde_qtd']) //3
	{
		// var_dump('ola');
		$stock_txt='<span class="disponivel" title="'.getLinguas($configs['stock_verde'],$GLOBALS['site_lang']).'">'.getLinguas($configs['stock_verde'],$GLOBALS['site_lang']).'<i class="fa fa-circle" alt="'.getLinguas($configs['stock_verde'],$GLOBALS['site_lang']).'" title="'.getLinguas($configs['stock_verde'],$GLOBALS['site_lang']).'" aria-hidden="true"></i></span>';
	}
	elseif($configs['stock_amarelo_ativo'] == 1 && $precos['STOCK'] == $configs['stock_amarelo_qtd']) //2
	{
		$stock_txt='<span class="limitado" title="'.getLinguas($configs['stock_amarelo'],$GLOBALS['site_lang']).'">'.getLinguas($configs['stock_amarelo'],$GLOBALS['site_lang']).'<i class="fa fa-circle" alt="'.getLinguas($configs['stock_amarelo'],$GLOBALS['site_lang']).'" title="'.getLinguas($configs['stock_amarelo'],$GLOBALS['site_lang']).'" aria-hidden="true"></i></span>';
	}
	elseif($configs['stock_azul_ativo'] == 1 && $precos['STOCK'] == $configs['stock_azul_qtd']) //1
	{
		$stock_txt='<span class="porencomenda" title="'.getLinguas($configs['stock_azul'],$GLOBALS['site_lang']).'">'.getLinguas($configs['stock_azul'],$GLOBALS['site_lang']).'<i class="fa fa-circle" alt="'.getLinguas($configs['stock_azul'],$GLOBALS['site_lang']).'" title="'.getLinguas($configs['stock_azul'],$GLOBALS['site_lang']).'" aria-hidden="true"></i></span>';
	}
	elseif($configs['stock_vermelho_ativo'] == 1 && $precos['STOCK'] == $configs['stock_vermelho_qtd']) //1
	{
		$stock_txt='<span class="esgotado" title="'.getLinguas($configs['stock_vermelho'],$GLOBALS['site_lang']).'">'.getLinguas($configs['stock_vermelho'],$GLOBALS['site_lang']).'<i class="fa fa-circle" alt="'.getLinguas($configs['stock_vermelho'],$GLOBALS['site_lang']).'" title="'.getLinguas($configs['stock_vermelho'],$GLOBALS['site_lang']).'" aria-hidden="true"></i></span>';
	}
	else
	{
		$stock_txt='<span class="esgotado" title="'.getLinguas($configs['stock_vermelho'],$GLOBALS['site_lang']).'">'.getLinguas($configs['stock_vermelho'],$GLOBALS['site_lang']).'<i class="fa fa-circle" alt="'.getLinguas($configs['stock_vermelho'],$GLOBALS['site_lang']).'" title="'.getLinguas($configs['stock_vermelho'],$GLOBALS['site_lang']).'" aria-hidden="true"></i></span>';
	}

	return $stock_txt;
}

function gerarneededimages($prod_id='')
{
	$arr_imagens=get_product_images($prod_id);

	if(count($arr_imagens) > 0)
	{
		$webp=0;
		if( strpos( $_SERVER['HTTP_ACCEPT'], 'image/webp' ) !== false )$webp=1;// webp is supported!
		$webp=0;

		$i_small=unserialize(IMG_SMALL);
		$i_thum=unserialize(IMG_THUM);
		for ($i=0;$i < count($arr_imagens);$i++)
		{
			$ext=$arr_imagens[$i]['img_ext'];
			$ordem=$arr_imagens[$i]['Ordem'];
			$img_file='files/products/'.$prod_id.'_'.$ordem.'.'.$ext;

			$img_file_small='files/products/'.$prod_id.'_'.$ordem.'_small.png';
			$img_file_thum='files/products/'.$prod_id.'_'.$ordem.'_thum.png';
			$img_file_prod=$img_file; // Product detail uses the original image.

			// VAMOS PRIMEIRO GERADAR
			if($i_small['ativo'] == 1)	geraimagens($img_file,$img_file_small,$i_small['w'],$i_small['h'],$i_small['crop'],$i_small['m_trans'],$i_small['c_white']);
			if($i_thum['ativo'] == 1) 	geraimagens($img_file,$img_file_thum,  $i_thum['w'],$i_thum['h'],$i_thum['crop'],$i_thum['m_trans'],$i_thum['c_white']);

			$img_file_small_cache='';
			$img_file_thum_cache='';
			$img_file_prod_cache='';

			// VAMOS PASSAR A USAR APENAS 3 TAMANHOS,PEQUENA, MEDIA, E FICHA DE PRODUTO

			//Se existir a original
			if(dotbright_image_file_exists($GLOBALS['site_shell_url'].$img_file) && $ext!='')
			{
				if(!dotbright_image_file_exists($GLOBALS['site_shell_url'].$img_file_small))geraimagens($img_file,$img_file_small,$i_small['w'],$i_small['h'],$i_small['crop'],$i_small['m_trans'],$i_small['c_white']);
				if(!dotbright_image_file_exists($GLOBALS['site_shell_url'].$img_file_thum))geraimagens($img_file,$img_file_thum,  $i_thum['w'],$i_thum['h'],$i_thum['crop'],$i_thum['m_trans'],$i_thum['c_white']);
			}

			if(dotbright_image_file_exists($GLOBALS['site_shell_url'].$img_file_small) && $ext!='') $img_file_small_cache='files/products/'.$prod_id.'_'.$ordem.'_small.png?dp='.dotbright_image_mtime($GLOBALS['site_shell_url'].$img_file_small);
			if(dotbright_image_file_exists($GLOBALS['site_shell_url'].$img_file_thum) && $ext!='') $img_file_thum_cache='files/products/'.$prod_id.'_'.$ordem.'_thum.png?dp='.dotbright_image_mtime($GLOBALS['site_shell_url'].$img_file_thum);
			if(dotbright_image_file_exists($GLOBALS['site_shell_url'].$img_file_prod ) && $ext!='') $img_file_prod_cache=$img_file.'?dp='.dotbright_image_mtime($GLOBALS['site_shell_url'].$img_file);



			if($webp==1)
			{
				$img_webp_small='files/products/'.$prod_id.'_'.$ordem.'_small.webp';
				$img_webp_thum='files/products/'.$prod_id.'_'.$ordem.'_thum.webp';

				if(dotbright_image_file_exists($GLOBALS['site_shell_url'].$img_file_small))
				{
					if(!dotbright_image_file_exists($GLOBALS['site_shell_url'].$img_webp_small))
						imgtowepp($GLOBALS['site_shell_url'].$img_file_small,$GLOBALS['site_shell_url'].$img_webp_small);

					$img_file_small='files/products/'.$prod_id.'_'.$ordem.'_small.webp';
					$img_file_small_cache='files/products/'.$prod_id.'_'.$ordem.'_small.webp?dp='.dotbright_image_mtime($GLOBALS['site_shell_url'].$img_webp_small);

				}

				if(dotbright_image_file_exists($GLOBALS['site_shell_url'].$img_file_thum))
				{
					if(!dotbright_image_file_exists($GLOBALS['site_shell_url'].$img_webp_thum))imgtowepp($GLOBALS['site_shell_url'].$img_file_thum,$GLOBALS['site_shell_url'].$img_webp_thum);


					$img_file_thum='files/products/'.$prod_id.'_'.$ordem.'_thum.webp';
					$img_file_thum_cache='files/products/'.$prod_id.'_'.$ordem.'_thum.webp?dp='.dotbright_image_mtime($GLOBALS['site_shell_url'].$img_webp_thum);

				}

			}

			if(dotbright_image_file_exists($GLOBALS['site_shell_url'].$img_file_small) && $ext!='') $img_file_small=$img_file_small_cache;else $img_file_small=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo_cart.png';
			if(dotbright_image_file_exists($GLOBALS['site_shell_url'].$img_file_thum) && $ext!='') $img_file_thum=$img_file_thum_cache;else $img_file_thum=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo.png';
			if(dotbright_image_file_exists($GLOBALS['site_shell_url'].$img_file_prod) && $ext!='') $img_file_prod=$img_file_prod_cache;else $img_file_prod=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo2.png';
			if($ext)
			{
				$arr_img[]=$GLOBALS['site_http_url'].$img_file;
				$arr_img_new[]=$img_file_thum;
				$arr_img_news[]=$img_file_thum;
				$arr_img_cart[]=$img_file_small;
				$arr_img_med[]=$img_file_prod;

				$arr_img_fb[]  =$img_file_prod;
			}
			else
			{
				$arr_img[]=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo.png';
				$arr_img_cart[]=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo_cart.png';
				$arr_img_med[]=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo2.png';
				$arr_img_new[]=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo.png';
				$arr_img_news[]=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo_cart.png';
				$arr_img_fb[]=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo.png';
			}/**/
		}
	}

	$oimages = array();
	$oimages['prd_imgm']=$arr_img[0];
	$oimages['prd_imgm_new']=$arr_img_new[0];
	$oimages['prd_imgm_news']=$arr_img_news[0];
	$oimages['prd_imgm_cart']=$arr_img_cart[0];
	$oimages['prd_imgm_med']=$arr_img_med[0];
	$oimages['prd_imgm_fb']=$arr_img_fb[0];

}

function showimg($src = ''){
	echo '<img src='.$src.'>';
}
function text_smart($txt='')
{
	

	$return='';
	//$txt_array=explode("\n",$txt);	
	//$txt_array = preg_split('/ (<p>|<br>) /', $txt);

	$txt=str_replace(' | ','/',$txt);

	$txt=str_replace('<br  | >','<br>',$txt);
	$txt=str_replace('. ','.<br>',$txt);
	$txt=str_replace('|',' ',$txt);
	$txt=str_replace('´´','"',$txt);
	$txt=str_replace("\n",'<br>',$txt);



    $txt_array = explode('<br>',$txt);


	$i=0;
	$t=count($txt_array);
	while($i<$t)
	{
		$txt_original = trim($txt_array[$i]);
		/*$txt_caps = strtoupper($txt_original);*/

		if($txt_original != '')
		{			
			/*if($txt_original == $txt_caps) $return.='<div style="margin-top:10px;width:100%;color:#ff8a00;font-weight:600">'.$txt_caps.'</div>';
			else */$return.=$txt_original.'<br>';
		}

		$i++;
	}

	/*$return=nl2br(strip_html_tags_fp($txt));*/

	return $return;
}
function  get_more_text($prod_id=0)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang,$site_http_url;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$obj = new Dataobj;
	$return = '';
	$sql = 'SELECT prod_txt1 FROM products_families_lang INNER JOIN products_families_obj ON products_families_obj.ID_ORG=products_families_lang.ID_PAI
			WHERE products_families_obj.ID_PROD='.$prod_id.' AND products_families_lang.Lang="'.$site_lang.'"';
	$obj->db_open($db_link, $DB_name, $sql);
	if(!$obj->EOF())
	{
		$return = $obj->db_data['prod_txt1'];
	}
	$obj->db_close();

	return $return;
}

function nome_icons($id){

    global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang,$site_http_url;
    $db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
    $obj = new Dataobj;
    $return = '';
    $sql = 'SELECT products_components.ID, img_ext, Nome 
            FROM products_components 
            inner JOIN products_components_lang 
            ON products_components.ID=products_components_lang.ID_PAI 
            and products_components.ID='.$id.' and products_components_lang.Lang = "'.$site_lang.'"
            WHERE Visivel=1';
   // echo '<pre>'.$sql.'</pre>';
    $obj->db_open($db_link, $DB_name, $sql);
    if(!$obj->EOF())
    {
        $txt_nome = $obj->db_data['Nome'];
    }
    $obj->db_close();

    return $txt_nome;

}

function getColor($cor_prod)
{
	switch (strtolower($cor_prod))
	{
		case 'red':
			return 'red';
		case 'vermelho':
			return 'red';
		case 'blue':
			return 'blue';
		case 'azul':
			return 'azul';
		case 'green';
			return 'green';
		case 'verde';
			return 'green';
		case 'black':
			return 'black';
		case 'preto':
			return 'black';
		case 'branco':
			return 'white';
		case 'white':
			return 'white';
		case 'rosa';
			return 'pink';
		case 'dourado';
			return '#f0c9c2';
		case 'cinza';
			return '#b0b2b6';
		case 'cinzento';
			return '#b0b2b6';
		default:
			return 'white';
	}
}
