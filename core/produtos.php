<?php
if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) exit();
require_once __DIR__.'/product_images.php';

function promos_familias()
{
	/*$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;
	$rs2=new DataObj;
	$rs3=new DataObj;
	$sql='
			SELECT P.ID,POL.Cabecalho as Ref,P.Peso,P.Carrinho,P.ID_Marca,P.Preco1,P.Destaque,PL.Cabecalho,PL.Texto1,PL.Texto2,PL.Texto3,PL.Texto4,PL.Cabecalho_Texto5,PL.Texto5,PL.Cor,PL.Tamanho
			FROM products AS P
			INNER JOIN products_lang AS PL ON PL.ID_PAI=P.ID
			INNER JOIN products_objs as PO ON PO.obj_type_id=P.ID
			INNER JOIN products_objs_lang as POL ON POL.ID_PAI=PO.obj_id
			WHERE
				PO.obj_promo > 0
				AND	PO.obj_promo != PO.obj_pvp
				AND	PO.obj_promo_ativo=1
				AND	PO.obj_ativo=1
				AND PL.Lang="'.$GLOBALS['site_lang'].'"
				AND POL.Lang="'.$GLOBALS['site_lang'].'"
			ORDER by P.Ordem As
		';
	$rs2->db_open($db_link,$GLOBALS['DB_name'],$sql_subFam);*/
}

function encontraFamiliasRecursiva_x($id_org=0)
{
    $db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new Dataobj;
	$arvore[0]=$id_org;
	$i=1;
	while($id_org > 0)
	{
		$rs->db_open($db_link,$GLOBALS['DB_name'],"SELECT ID_ORG FROM products_families WHERE ID=$id_org");
		$id_org=$rs->db_data["ID_ORG"];
		$arvore[$i]=$id_org;
		$rs->db_close();
		$i++;
	}
	return array_values(array_filter(array_reverse($arvore)));
}

function products_objs_fam($fam_id=0)
{
	$prods=\get_produtc_info(0,'',0,$fam_id);
	$return='';
	if($prods!==NULL)
	{
		$url=$site_http_url.\seo2(2,$GLOBALS['products_page_id'],$prods,$GLOBALS['site_lang']);
		$return .='<div style="float:left;width:300px;cursor:pointer" onclick="window.open(\''.$url.'\',\'_self\')"><div class="produto_wrapper2" style="background:#FFF;padding:10px;height:100%">';
		$return .='<table border="0" cellpadding="0" cellspacing="0" width="100%">';
		$return .='<tr>';
		$return .='<td align="center" rowspan="2" width="150" height="120"><div class="figura"><img src="'.$prods['prd_imgm_new'].'" alt="" /></div></td>';
		$return .='<td style="padding:0 5px">';
		$return .='<div class="titulo" style="font-size:12px;font-weight:bold !important;word-break:break-all">'.$prods['prod_nome'].'</div>';
		$return .='<div class="subtitulo" style="font-size:12px">'.$prods['prod_subtitulo'].'</div>';
		$return .='<div class="referencia" style="font-size:12px">'.$prods['prod_precos']['Modelo'].'</div>';
		$return .='</td>';
		$return .='</tr>';
		$return .='<tr>';
		$return .='<td>';
		$return .='<div width="60" class="preco">'.numberformat($prods['prod_precos']['PVP']).'</div>';
		$return .='</td>';
		$return .='</tr>';
		$return .='</table>';
		$return .='</div></div>';
	}
	return $return;
}

function produto_recomendacao()
{
	$m_nome=$_POST['m_nome'];
	$m_email=$_POST['m_email'];
	$m_nome2=$_POST['m_nome2'];
	$m_email2=$_POST['m_email2'];
	$recomendar=$_POST['recomendar'];
	if($m_nome && $m_email && $m_nome2 && $m_email2 && $recomendar)
	{
		$body=array
		(
			'[name]'=>$m_nome,
			'[name2]'=>$m_nome2,
			'[produto]'=>$recomendar
		);
		$message=template_email($GLOBALS['template_recomedacao_prod'],$body);
		if(send_email($m_email2,$message['subject'],$message['body']))
		{
			show_alerta($msg,'msg');
		}
	}
}

function products_objs_show($prod_id=0)
{
	global $key_apagar,$m_resumo,$n_enc,$enc,$shop,$site_http_url,$key_minishop_products,$site_lang,$site_http_ajax_url;
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;
	$rs2=new DataObj;
	$prods=\get_produtc_info($prod_id);
	$produto_nao_standard=$prods['produto_standard'];
	$return='';


	if($prods!==NULL)
	{
       // print_r($prods);
       // die('aquiii222');

		// $produto_nao_standard=0;//Alteracao forçada porque causa da grafica online
		if($produto_nao_standard==1)
		{
			//Produtos do tipo grafica com upload de ficheiro
			$p_way=geraCatalogoRecursivo(intval($prods['fam_id']));
			$fam=get_family_info(intval($p_way[0]));
			$reparacoes=0;
			if(!empty($fam) && is_array($fam) && $fam['reparacoes'] == 1) $reparacoes=1;

			if(isset($_POST['opcoes']) && $_POST['opcoes']!='') $prod_add_opcoes=$_POST['opcoes']; else $prod_add_opcoes='';
			if(isset($_POST['opcoes_id']) && $_POST['opcoes_id']!='') $opcoes_id=$_POST['opcoes_id']; else $opcoes_id='';
			if(isset($_POST['garantia_add']) && $_POST['garantia_add'] > 0) $garantia_add=$_POST['garantia_add']; else $garantia_add=0;
			if(isset($_POST['porte_add']) && $_POST['porte_add'] > 0) $porte_add=$_POST['porte_add']; else $porte_add=0;
			$servicos='';
			$altura=0;
			$largura=0;
			$tempo=0;
			if(isset($_POST['qtd']) && $_POST['qtd'] > 0) $quantidade=$_POST['qtd']; else $quantidade=1;

			if(isset($_POST['codes'])) $combo=$_POST['codes'];
			if(isset($_POST['qnt']) && $_POST['qnt'] > 0) $opcçao_quantidade= $_POST['qtd'];// $_POST['qnt'];
			if(isset($_POST['tempo'])) $tempo=$_POST['tempo'];
			if(isset($_POST['serv'])) $servicos=$_POST['serv'];
			if(isset($_POST['altura']))$altura=$_POST['altura'];
			if(isset($_POST['largura'])) $largura=$_POST['largura'];
			if(isset($_POST['data_realizacao'])) $data_realizacao=$_POST['data_realizacao'];
			if(isset($_FILES) && $_FILES['file']['name']!='')
			{
				$nome_ficheiro=guarda_ficheiros_enviados($_FILES);
			}

			if($servicos==';')$servicos='';
			$valores_imp=get_preco_compra($combo,$prod_id,$tempo,$opcçao_quantidade,$quantidade,$servicos,$altura,$largura);
			//print_r($valores_imp);
			//	$return .=$valores_imp['sql'];
			$precos_finais=$valores_imp['preco'];
			$id_combo=$valores_imp['id_combo'];

			//adicionar o produto ao carrinho
			// $return .= '<script>$.get(\''.seo('pages.php?page_id='.$GLOBALS['cart_purchage_id']).'?prod_add='.$prods['prod_id'].'&prod_add_obj='.$prod_id.'&prod_add_qtd='.$quantidade.'&prod_add_opcoes='.$prod_add_opcoes.'&prod_combo_id='.$id_combo.'&prod_serv='.$servicos.'&prod_quant_id='.$opcçao_quantidade.'&largura='.$largura.'&altura='.$altura.'&tempo_escolhido='.$tempo.'&tempo_realizacao='.$data_realizacao.'&fichei='.$nome_ficheiro.'\').done(function(){$.get(\''.$site_http_ajax_url.'ajaxShop.php?tipo=3\').done(function(resultado){var res=resultado.split("[spl]");$(".itens").html(res[0]+"Carrinho");$(".itensvalue").html(res[1]);$("#listProdsCart").html(res[2]); });});</script>';

			//adicionar o produto ao carro
			if($reparacoes == 1) $return .= '<script>$.get(\''.seo('pages.php?page_id='.$GLOBALS['cart_reparacoes_purchage_id']).'?prod_add_reparacoes='.$prods['prod_id'].'&prod_add_reparacoes_obj='.$prod_id.'&prod_add_reparacoes_qtd='.$quantidade.'&prod_add_reparacoes_opcoes='.$prod_add_opcoes.'&prod_combo_id='.$opcoes_id.'\').done(function(){$.get(\''.$site_http_ajax_url.'ajaxShop2.php?tipo=3\').done(function(resultado){var res = resultado.split("[spl]"); $(".itensnum2").html(res[0]);$(".itensvalue2").html(res[1]); if(res[2] > 0){$(\'#cart-bonus2\').show();}  $("#listProdsCart2").html(res[3]); });});</script>';
			else $return .= '<script>$.get(\''.seo('pages.php?page_id='.$GLOBALS['cart_purchage_id']).'?prod_add='.$prods['prod_id'].'&prod_add_obj='.$prod_id.'&prod_add_qtd='.$quantidade.'&prod_add_opcoes='.$prod_add_opcoes.'&prod_garantia_add='.$garantia_add.'&prod_combo_id='.$opcoes_id.'\').done(function(){$.get(\''.$site_http_ajax_url.'ajaxShop.php?tipo=3\').done(function(resultado){var res = resultado.split("[spl]"); $(".itensnum").html(res[0]);$(".itensvalue").html(res[1]);$("#listProdsCart").html(res[2]); });});</script>';


			$total += ($prods['prod_precos']['PVP'] * $quantidade);
			//$preco_combinatoria=preco_final_comb($prod_id,$combo,$opcçao_quantidade,$qtd);

			if(isset($_POST['opcoes_id']) && $_POST['opcoes_id'] > 0)  $prods['prod_precos']=get_produtc_price_combo($prods['prod_id'], $_POST['opcoes_id']);

			$valor_portes_gratuitos=getPortesGratuitos();
			if($total>=$valor_portes_gratuitos)$faltam='Sem custos de envio!';
			else
			{
				$faltam=$valor_portes_gratuitos-$total;
				$faltam='Faltam '.numberformat($faltam).' para não ter custos de envio!';
			}
			$return .='<div class="background">';
			$return .='<div id="shoppopup" class="compraboxinner">';
			$return .= show_alerta('<span style="color:#000;font-size:13px">'.getLinguas('key_adicionado_carrinho',$GLOBALS['site_lang']).'</span>','msg');
            $return .='<div class="closebutton" onclick="fecharPopupAdicionarProduto()" title="Clique para fechar a janela"></div>';
			$return .='<div class="c"></div>';
			$return .='<div class="produto_wrapper">';
			$return .='<table border="0" cellpadding="0" cellspacing="0" width="100%">';
			$return .='<tr>';
			$return .='<td width="100"><div class="figura"><img src="'.$prods['prd_imgm_new'].'" alt=""></div></td>';
			$return .='<td style="padding:0 5px;">';
			$return .='<div class="titulo"><b>'.$prods['prod_nome'].'</b></div>';
			//$return .='<div class="filtros">'.$prod_add_opcoes.'</div>';
			// $return .='<div class="subtitulo" style="font-size:10px">'.$prods['prod_subtitulo'].'</div>';
			// $return .='<div class="referencia">'.$prods['prod_precos']['Modelo'].'</div>';
			$return .='<div id="1" class="quantidade">'.getLinguas('key_quantidade',$GLOBALS['site_lang']).': '.$opcçao_quantidade.' X '.$quantidade.' unit.</div>';
			//$return .='<div class="data_realiza">'.getLinguas('key_data_realizacao',$GLOBALS['site_lang']).' : '.$data_realizacao.'</div>';


			$return .='<div class="preco">'.$precos_finais.' </div>';
			$return .='</td>';

			$return .='</tr>';
			$return .='</table>';
			$return .='</div>';
			$return .='<div class="butoes">';
			$return .='<div class="botaocontinuar" onclick="fecharPopupAdicionarProduto()">'.getLinguas('key_adicionar_continuar_comprar',$GLOBALS['site_lang']).'</div>';
			if($reparacoes == 1)$return .='<div class="botaofinalizar" onclick="javascript:window.open(\''.seo('pages.php?page_id='.$GLOBALS['cart_reparacoes_purchage_id']).'\',\'_top\')">'.getLinguas('key_finalizar_encomenda',$GLOBALS['site_lang']).'</div>';
			else $return .='<div class="botaofinalizar" onclick="javascript:window.open(\''.seo('pages.php?page_id='.$GLOBALS['cart_purchage_id']).'\',\'_top\')">'.getLinguas('key_finalizar_encomenda',$GLOBALS['site_lang']).'</div>';
			$return .='</div>';
			$return .='</div>';
			$return .='</div>';
		}
		else
		{
			//Produtos standards
			//
			$p_way=geraCatalogoRecursivo(intval($prods['fam_id']));
			$fam=get_family_info(intval($p_way[0]));
			$reparacoes=0;
			if(!empty($fam) && is_array($fam) && $fam['reparacoes'] == 1) $reparacoes=1;

			// echo 'reparacoes: '.$reparacoes;

			//obter a quantidade (caso exista)
			if(isset($_GET['opcoes']) && $_GET['opcoes']!='') $prod_add_opcoes=$_GET['opcoes']; else $prod_add_opcoes='';
			if(isset($_GET['opcoesids']) && $_GET['opcoesids']!='') $opcoesids=$_GET['opcoesids']; else $opcoesids='';
			if(isset($_GET['opcoes_id']) && $_GET['opcoes_id']!='') $opcoes_id=$_GET['opcoes_id']; else $opcoes_id='';
			if(isset($_GET['garantia_add']) && $_GET['garantia_add'] > 0) $garantia_add=$_GET['garantia_add']; else $garantia_add=0;
			if(isset($_GET['porte_add']) && $_GET['porte_add'] > 0) $porte_add=$_GET['porte_add']; else $porte_add=0;
			if(isset($_GET['servicos_extra_add']) && $_GET['servicos_extra_add'] > 0) $servicos_extra_add=$_GET['servicos_extra_add']; else $servicos_extra_add=0;
			if(isset($_GET['qtd']) && $_GET['qtd'] > 0) $quantidade=$_GET['qtd']; else $quantidade=1;
          //  print_r($prods);

			if(isset($_FILES) && $_FILES['file']['name']!='')
			{
				$nome_ficheiro=guarda_ficheiros_enviados($_FILES);
			}

			$quantidade=verifica_qtd($prod_id,$quantidade);


			//adicionar o produto ao carro
			/* if($reparacoes == 1) $return .= '<script>$.get(\''.seo('pages.php?page_id='.$GLOBALS['cart_reparacoes_purchage_id']).'?prod_add_reparacoes='.$prods['prod_id'].'&prod_add_reparacoes_obj='.$prod_id.'&prod_add_reparacoes_qtd='.$quantidade.'&prod_add_reparacoes_opcoes='.$prod_add_opcoes.'&prod_combo_id='.$opcoes_id.'\').done(function(){$.get(\''.$site_http_ajax_url.'ajaxShop2.php?tipo=3\').done(function(resultado){var res=resultado.split("[spl]"); $(".itensnum2").html(res[0]);$(".itensvalue2").html(res[1]); if(res[2] > 0){$(\'#cart-bonus2\').show();}  $("#listProdsCart2").html(res[3]); });});</script>';
			else  */

			//adicionar o produto ao carro
		//	if($reparacoes == 1) $return .= '<script>$.get(\''.seo('pages.php?page_id='.$GLOBALS['cart_reparacoes_purchage_id']).'?prod_add_reparacoes='.$prods['prod_id'].'&prod_add_reparacoes_obj='.$prod_id.'&prod_add_reparacoes_qtd='.$quantidade.'&prod_add_reparacoes_opcoes='.$prod_add_opcoes.'&prod_combo_id='.$opcoes_id.'\').done(function(){$.get(\''.$site_http_ajax_url.'ajaxShop2.php?tipo=3\').done(function(resultado){var res = resultado.split("[spl]"); $(".itensnum2").html(res[0]);$(".itensvalue2").html(res[1]); if(res[2] > 0){$(\'#cart-bonus2\').show();}  $("#listProdsCart2").html(res[3]); });});</script>';
		//	else $return .= '<script>$.get(\''.seo('pages.php?page_id='.$GLOBALS['cart_purchage_id']).'?prod_add='.$prods['prod_id'].'&prod_add_obj='.$prod_id.'&prod_add_qtd='.$quantidade.'&prod_add_opcoes='.$prod_add_opcoes.'&prod_garantia_add='.$garantia_add.'&prod_combo_id='.$opcoes_id.'\').done(function(){$.get(\''.$site_http_ajax_url.'ajaxShop.php?tipo=3\').done(function(resultado){var res = resultado.split("[spl]"); $(".itensnum").html(res[0]);$(".itensvalue").html(res[1]);$("#listProdsCart").html(res[2]); });});</script>';
	//echo($reparacoes);

            if($reparacoes == 1) {
                $return .= '<script>$.get(\''.seo('pages.php?page_id='.$GLOBALS['cart_reparacoes_purchage_id']).'?prod_add_reparacoes='.$prods['prod_id'].'&prod_add_reparacoes_obj='.$prod_id.'&prod_add_reparacoes_qtd='.$quantidade.'&prod_add_reparacoes_opcoes='.$prod_add_opcoes.'&prod_combo_id='.$opcoes_id.'\').done(function(){$.get(\''.$site_http_ajax_url.'ajaxShop2.php?tipo=3\').done(function(resultado){var res = resultado.split("[spl]"); $(".itensnum2").html(res[0]);$(".itensvalue2").html(res[1]); if(res[2] > 0){$(\'#cart-bonus2\').show();}  $("#listProdsCart2").html(res[3]); });});</script>';

            }else {
                $m_pais = 165;//quando estamos na ficha de artigo ainda nao temos o paius seleccionado
                $porte = multi_portes_prod($prod_id, $prods['prod_precos']['PVP'], 0, $m_pais);
                $porte_add = (isset($porte[0]['id']) && $porte[0]['id'] != '' ? $porte[0]['id'] : '1_0');

                //invoca a tag [shopping_cart3]
                //que corresponde à função carrinho_purchase();
                //Esta função deve adicionar os parametros ao carrinho de sessao
                $return .= '<script>$.get(\'' . seo('pages.php?page_id=' . $GLOBALS['cart_purchage_id']);
                $return .= '?prod_add=' . $prods['prod_id'];
                $return .= '&prod_add_obj=' . $prod_id;
                $return .= '&prod_add_qtd=' . $quantidade;
                $return .= '&prod_add_opcoes=' . $prod_add_opcoes;
                $return .= '&prod_garantia_add=' . $garantia_add;
                $return .= '&prod_porte_add=' . $porte_add;
                $return .= '&prod_servicos_extra_add=' . $servicos_extra_add;
                $return .= '&fichei=' . $nome_ficheiro;
                $return .= '&prod_combo_id=' . $opcoes_id . '\').done(function(){$.get(\'' . $site_http_ajax_url . 'ajaxShop.php?tipo=3\').done(function(resultado){var res=resultado.split("[spl]"); $(".itensnum").html(res[0]);$(".itensvalue").html(res[1]);$("#listProdsCart").html(res[2]); });});</script>';
            }
                //percorrer o carrinho actual (sem o produto adicionado e calcular o preco)
                if ($GLOBALS['n_enc'] > 0) {
                    $i = 1;
                    $found = 0;
                    $portes_pagar = 0;
                    $total = 0;
                    while ($i <= $GLOBALS['n_enc'] && $found == 0) {
                        $Preco = 0;
                        $Nome = '';
                        $total_desconto = 0;
                        $portes = 0;
                        $prod_id = $shop[$i][1];
                        $qtd = $shop[$i][2];
                        $tipo = $shop[$i][3];
                        $chave = $shop[$i][4];
                        $prod_obj = $shop[$i][5];
                        $opcoes = $shop[$i][6];
                        $opcoes_id = (isset($shop[$i][7]) ? $shop[$i][7] : 0);
                        $info = \get_produtc_info($prod_id);
                        if ($opcoes_id > 0) $info['prod_precos'] = get_produtc_price_combo($prod_id, $opcoes_id);
                        if ($prods !== NULL) $total += ($info['prod_precos']['PVP'] * $qtd);
                        $i++;
                    }
                }

			if($found==0)$qtd=0;
			$available_stock=get_stock($prod_id); // se for um produto,vamos ver se tem stock
	//echo('quantidade estoque '.$available_stock.'<br>' );
	//			 echo 'quantidade: '.$quantidade;
			if($reparacoes == 0) if($quantidade+$qtd > $available_stock)$quantidade=$available_stock-$qtd;
	//			 echo '<br>quantidade 2: '.$quantidade;
	//            die('<br>287aquiii -'.$quantidade);
			$total += ($prods['prod_precos']['PVP'] * $quantidade);
			if(isset($_GET['opcoes_id']) && $_GET['opcoes_id'] > 0)  $prods['prod_precos']=get_produtc_price_combo($prods['prod_id'], $_GET['opcoes_id']);

			$valor_portes_gratuitos=getPortesGratuitos();
			if($total>=$valor_portes_gratuitos)$faltam='Sem custos de envio!';
			else
			{
				$faltam=$valor_portes_gratuitos-$total;
				$faltam='Faltam '.numberformat($faltam).' para não ter custos de envio!';
			}


			$return .='<div class="background" id="301">';
			$return .='<div id="shoppopup" class="compraboxinner">';
			$return .= show_alerta('<span style="color:#000;font-size:13px">'.getLinguas('key_adicionado_carrinho',$GLOBALS['site_lang']).'</span>','msg'); $return .='<div class="closebutton" onclick="fecharPopupAdicionarProduto()" title="Clique para fechar a janela"></div>';
			$return .='<div class="c"></div>';
			$return .='<div class="produto_wrapper">';
			$return .='<table border="0" cellpadding="0" cellspacing="0" width="100%">';
			$return .='<tr>';
			$return .='<td width="100"><div class="figura"><img src="'.$prods['prd_imgm_new'].'" alt=""></div></td>';
			$return .='<td style="padding:0 5px;">';
			$return .='<div class="titulo">'.((isset($prod_add_opcoes) && $prod_add_opcoes!='') ? $prods['prod_nome'].','.$prod_add_opcoes : $prods['prod_nome']).'</div>';

			// if($quantidade>0)$return .='<div class="quantidade">'.getLinguas('key_quantidade',$GLOBALS['site_lang']).': '.$quantidade.'</div>';
			// if($quantidade>0)$return .='<div class="preco" style="width:100%">'.numberformat($prods['prod_precos']['PVP']*$quantidade,1).'</div>';
			// else $return .='<div class="preco" style="width:100%">já não existem mais unidades em stock</div>';
			$return .='<div id="305" class="quantidade">'.$GLOBALS['key_quantidade'][$GLOBALS['site_lang']].': '.$quantidade.'</div>';
			$return .='<div class="preco">'.numberformat($prods['prod_precos']['PVP']*$quantidade, 1).'</div>';

			$return .='</td>';
			$return .='</tr>';
			$return .='</table>';
			$return .='</div>';
			$return .='<div class="butoes">';
			$return .='<div class="botaocontinuar" onclick="fecharPopupAdicionarProduto()">'.getLinguas('key_adicionar_continuar_comprar',$GLOBALS['site_lang']).'</div>';
			if($reparacoes == 1)$return .='<div class="botaofinalizar" onclick="javascript:window.open(\''.seo('pages.php?page_id='.$GLOBALS['cart_reparacoes_purchage_id']).'\',\'_top\')">'.getLinguas('key_finalizar_encomenda',$GLOBALS['site_lang']).'</div>';
			else $return .='<div class="botaofinalizar" onclick="javascript:window.open(\''.seo('pages.php?page_id='.$GLOBALS['cart_purchage_id']).'\',\'_top\')">'.getLinguas('key_finalizar_encomenda',$GLOBALS['site_lang']).'</div>';
			$return .='</div>';
			$return .='</div>';
			$return .='</div>';
		}
	}
	return $return;
}

function get_preco_compra($codes,$prodid,$tempo,$qnt,$multiple,$servicos,$altura=0,$largura=0)
{
	$preco_final_siva='';
	$return='';
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	if(isset($codes) && $codes != '' && isset($prodid) && intval($prodid) > 0 )
	{

		/*$qnt_esp=get_qnt_esp($prodid);

		if($qnt_esp['flag_esp']==1 && $altura > 0 && $largura > 0 )
		{
			$area=($altura/100)*($largura/100);
			$rs=new DataObj;
			$sql='Select products_objs_combo.obj_id,products_objs_combo.obj_pvp,products_objs_combo.obj_iva,products_objs_combo.obj_promo,products_objs.obj_promo_ativo,products_objs_combo.obj_qtdmin from products_objs_combo INNER JOIN products_objs ON products_objs.obj_type_id=products_objs_combo.obj_type_id where products_objs_combo.obj_combo2=";'.$codes.'" and products_objs_combo.obj_type_id='.$prodid.' and products_objs_combo.obj_qtdmin < '.$area.' order by products_objs_combo.obj_qtdmin DESC';
			$rs->db_open($db_link,$DB_name,$sql);
			$p_sql=$sql;

			$id=$rs->db_data['obj_id'];
			$produto_civa=number_format(($rs->db_data['obj_pvp'])*$area,2);
			$produto_iva=(intval($rs->db_data['obj_iva']))/100;

			$obj_promo=number_format($rs->db_data['obj_promo']*$area,2);
			$obj_promo_ativo=$rs->db_data['obj_promo_ativo'];
			if($obj_promo_ativo=1 && $obj_promo > 0 && $obj_promo < $produto_civa)
			{
				$produto_civa=$obj_promo;
			}

		}
		elseif($qnt_esp['flag_esp']==0 && isset($qnt))
		{*/
		if(isset($qnt))
		{
			// 1º vamos buscar o valor da combinacao escolhida e da quantidade minima
			$rs=new DataObj;
			$sql='Select products_objs_combo.obj_id,products_objs_combo.obj_pvp,products_objs_combo.obj_iva ,products_objs.obj_promo_ativo,products_objs_combo.obj_promo from products_objs_combo INNER JOIN products_objs ON  products_objs.obj_type_id=products_objs_combo.obj_type_id where products_objs_combo.obj_combo2=";'.$codes.'" and products_objs_combo.obj_type_id='.$prodid;
			$rs->db_open($db_link,$DB_name,$sql);
			$p_sql=$sql;
			$id=$rs->db_data['obj_id'];
			$produto_civa=$rs->db_data['obj_pvp'];
			$produto_iva=intval($rs->db_data['obj_iva'])/100;
			$obj_promo=$rs->db_data['obj_promo'];
			$obj_promo_ativo=$rs->db_data['obj_promo_ativo'];
			/*if($obj_promo_ativo=1 && $obj_promo > 0 && $obj_promo < $produto_civa)*/
			if($obj_promo_ativo=1 && $obj_promo > 0)
			{
				$produto_civa=$obj_promo;
			}
		}
		$produto_Siva=$produto_civa/(1+$produto_iva);
		$preco_final_siva=$produto_Siva;
		// vamos verificar se existe quantidade
		if($multiple>0 )
		{
			$preco_final_siva=$preco_final_siva*intval($multiple);
		}
		$rs->db_close();
		if($id!='' && $produto_civa!='' && $produto_iva!='' && isset($tempo) && $tempo >0)
		{
			// 2º vamos agora a verificar o valor acrescido do tempo de entrega
			$rs1=new DataObj;
			$sql='Select ID,custo_entrega from entrega where ID='.$tempo;
			$rs1->db_open($db_link,$DB_name,$sql);
		    $return.="tempo:".$tempo."  ".$sql;
			$id_entrega=$rs1->db_data['ID'];
			$custo_entrega=($rs1->db_data['custo_entrega'])/100; // é um custo % em relação ao valor total do produto

			$preco_final_siva=$preco_final_siva*$custo_entrega;
			$rs1->db_close();

			//3º vamos verificar se existe algum serviço selecionado
			if(isset($servicos) && $servicos!='' )
			{
				$array_servicos=explode(";",$servicos);
				foreach($array_servicos as $servico_id)
				{
					$rs2=new DataObj;
					$sql='Select ID,Tipo,Custos,margem from servicos where ID='.$servico_id.' and Visivel=1';

					$rs2->db_open($db_link,$DB_name,$sql);
					if(!$rs2->EOF())
					{
						$tipo=$rs2->db_data['Tipo'];  // tipo = 1 é valor fixo tipo = 2 percentual
						$custo=$rs2->db_data['Custos'];
						$custo=$custo/1.23;
						$margem=$rs2->db_data['margem'];

						if($tipo==1)
						{
							$preco_final_siva=$preco_final_siva+$custo;
						}else{
							// vai ficar por fazer as do tipo margem
						}
					}
					$rs2->db_close();
				}

			}

			// 4º por final vamos calcular o valor com iva e arredondar os valores da saida
			$preco_final_civa=$preco_final_siva*(1+$produto_iva);
			//$preco_final_civa=numberformat($preco_final_civa);
			//$preco_final_siva=numberformat($preco_final_siva);

			$return=$preco_final_civa;
		}
		else
		{
			$preco_final_civa=$preco_final_siva*(1+$produto_iva);
			//$preco_final_civa=numberformat($preco_final_civa);
			//$preco_final_siva=numberformat($preco_final_siva);
			$return=$preco_final_civa;
		}
	}
	else
	{
		$return=print('erro 1.0!');
	}
	return array( 'preco'=> $return,'id_combo'=>$id,'sql'=>$p_sql);
}

function products_objs_info($obj_id=0)
{
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;
	$sql="SELECT obj_id,Cabecalho,obj_pvp,obj_promo,obj_promo_ativo FROM products_objs inner join products_objs_lang on products_objs_lang.ID_PAI=products_objs.obj_id WHERE obj_type='products' AND obj_id='".$obj_id."' and Lang='".$GLOBALS['site_lang']."' and obj_ativo=1 Order by obj_pvp ASC";
	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	if(!$rs->EOF())
	{
		$obj_id=$rs->db_data['obj_id'];
		$obj_nome=htmlentities_memo(stripslashes($rs->db_data['Cabecalho']));
		$obj_pvp=$rs->db_data['obj_pvp'];
		$obj_promo=$rs->db_data['obj_promo'];
		$obj_promo_ativo=$rs->db_data['obj_promo_ativo'];
	}
	$rs->db_close();
	return $obj_nome;
}

function shopfams()
{
	global $m_http_url_images,$site_http_url,$tema,$site_lang,$template;
    $db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
    $rs=new Dataobj;
    $rs2=new Dataobj;
    $sql="Select * from products_families where visivel=1 and Download=1 order by Ordem asc limit 4";
    $hRes=$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	$i=0;
	$uri=$_SERVER['REQUEST_URI'];
	$uri=str_replace($site_http_url,"",$uri);
	$right=0; $zindex=4;
	$return='';
    while(!$rs->EOF())
	{
		$i++;
	    $IDb=$rs->db_data['ID'];
		$URL_Link=str_replace($GLOBALS['site_http_url'],'',seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$IDb,$IDb,1));
		$Largura=$rs->db_data["Largura"];
		$templates_id=explode(';',$rs->db_data['template_id']);
	    $condicao="where ID_PAI=$IDb and lang='$site_lang'";
	    $rs2->db_open($db_link,$GLOBALS['DB_name'],"Select * from products_families_lang $condicao");
	    if(!$rs2->EOF())
		{
			$nomeb=htmlentities_memo(stripslashes($rs2->db_data['Cabecalho']));
			if($i==4){ $wd=';width:254px'; $b=2;}else{ $wd=''; $b='';}
			$id_pai=get_pai($_GET['cat']);
			$return .= '<div style="font-size:15px;font-weight:bold;color:#FFF;text-align:right;line-height:21px;cursor:pointer" onClick=\'window.open("'.$URL_Link.'","_top")\'>';
			$return .=  $nomeb;
			$return .=  '</div>';
		}
		$rs2->db_close();
		$rs->db_next();
	}
    $rs->db_close();
	if($return) return '<div class="shop-fams"><div style="margin:20px;margin-top:45px">'.$return.'</div></div>';
	else return '';
}

function searchIntoBooleanMode($stringfind,$stringreplace,$string,$secondstring='')
{
	$stringbooleanmode='';
	$vec_words=explode($stringfind,$string);
	for ($i=0; $i < count($vec_words); $i++)
	{
		if($i==0) $stringbooleanmode .= $stringreplace.$vec_words[$i].$secondstring;
		else $stringbooleanmode .= ' '.$stringreplace.$vec_words[$i].$secondstring;
	}
	return $stringbooleanmode;
}

function getMediaFamilia($ID)
{
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;
	$rs->db_open($db_link,$GLOBALS['DB_name'],"Select * from products_families where ID=$ID order by Ordem Desc");
    if($rs)
    {
		$ID_Media=$rs->db_data["ID_Media"];
    	$rs->db_close();
		if($ID_Media>0) return \show_media($ID_Media);
    }
    $rs->db_close();
}

function catalogo_produtos($tipo='')
{
	// FUNÇÃO EM /LAYOUT/PROJECTO/CODE/CATALOGO.PHP
	bloco_catalogo();
}

function get_associados($ref='',$style='font-size:14px;line-height:16px',$txt=0)
{
	global $site_http_url,$site_http_ajax_url,$site_lang;
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$return='';
	$sql='SELECT
				PL.Cabecalho
			FROM products AS P
				INNER JOIN products_lang AS PL ON PL.ID_PAI=P.ID
			WHERE
				P.Ref_Ass="'.$ref.'"
				AND PL.Lang="'.$GLOBALS['site_lang'].'"
				AND Aproved_ass=1
			ORDER BY
				PL.Cabecalho Asc';
	$rs=new DataObj;
	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	if(!$rs->EOF())
	{
		if($txt==0) $return .= '<ul style="list-style-type:square;padding-left:20px">';
		while(!$rs->EOF())
		{
			if($txt==1) $return .= $rs->db_data['Cabecalho'].'<br />';
			else $return .= '<li style="'.$style.'">'.$rs->db_data['Cabecalho'].'</li>';
			$rs->db_next();
		}
		if($txt==0) $return .= '</ul>';
	}
	$rs->db_close();
	return $return;
}

function get_metodos_prod()
{
	global $site_http_url,$site_http_ajax_url,$site_lang,$site_shell_url;
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs_metodos=new DataObj;
	$sql='SELECT mp.*,mpl.Cabecalho,mpl.Texto1 FROM metodos_pagamento as mp inner join metodos_pagamento_lang as mpl on mp.ID=mpl.ID_PAI WHERE mpl.Lang="'.$GLOBALS['site_lang'].'" AND Visivel=1 order by mp.Tipo ASC';
	$rs_metodos->db_open($db_link,$GLOBALS['DB_name'],$sql);
	$pos=0;
	while(!$rs_metodos->EOF())
	{
		$nome 	= $rs_metodos->db_data['Cabecalho'];
		$ID_metodo		= $rs_metodos->db_data['ID'];
		$Ext	 		= $rs_metodos->db_data['img_ext'];
		if($Ext!="")
		{
			$nome_metodo='<img src="files/metodos_pagamento/'.$ID_metodo.'_1.'.$Ext.'" alt="'.$nome.'" title="'.$nome.'" />';
		}
		else
		{
			$nome_metodo=$nome;
		}
		$return .= '<div class="row" style="background:#eaeaea;margin-bottom:2px"><div class="col-xs-3 text-center">'.$nome_metodo.'</div><div class="col-xs-8 text-left" style="vertical-align:middle;line-height:22px"><span>'.$nome.'</span></div></div>';
		$pos++;
		$rs_metodos->db_next();
	}
	$rs_metodos->db_close();
	return $return;
}

function buscavalorfiltrosx_arr($ID)
{
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$dados=new DataObj;
	$sql="SELECT
				filters_values_lang.values_name,filters_values_lang.values_colorhex,filters_values.value_ext,filters_values.value_id
			FROM
				filters_values
				INNER JOIN filters_values_lang ON filters_values_lang.values_id=filters_values.value_id
			WHERE
				filters_values_lang.values_id='$ID'
				AND filters_values_lang.values_lang='".$GLOBALS['site_lang']."'";

	$dados->db_open($db_link,$GLOBALS['DB_name'],$sql);
	if(!$dados->EOF())
	{
		$values_name=$dados->db_data["values_name"];
		$values_colorhex=$dados->db_data["values_colorhex"];
		$value_id=$dados->db_data["value_id"];
		$value_ext=$dados->db_data["value_ext"];
	}
	$dados->db_close();
	return array('values_name'=>$values_name,'values_colorhex'=>$values_colorhex,'value_id'=>$value_id,'value_ext'=>$value_ext);
}

function filtros_ficha_produto_combinacoes_kk($prod_id=0,$fam_id=0)
{
    global $site_lang;
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$obj2=new DataObj; $return=array();
	$sql2='SELECT
				fp.cat_id,fp.valor_id,fp.filtro_area
			FROM
				filters_products fp
			WHERE
				(fp.fam_id1='.$fam_id.' OR fp.fam_id2='.$fam_id.' OR fp.fam_id3='.$fam_id.' OR fp.fam_id4='.$fam_id.')
				AND prod_id='.$prod_id.'
				AND fp.filtros_rel2 <> ""
				AND fp.filtro_active=1
				AND (fp.filtro_area=1 OR fp.filtro_area=2)
			GROUP BY
				fp.valor_id,fp.cat_id
			Order By
				fp.filtro_id asc';
	$obj2->db_open($db_link,$GLOBALS['DB_name'],$sql2);
	$a=0;
	while(!$obj2->EOF())
	{
		$cat_nome =''; $cat_nome=buscacategoriaTipoeNomex($obj2->db_data['cat_id']);
		$val_nome=''; $val_nome=buscavalorfiltrosx($obj2->db_data['valor_id']);
		if($cat_nome && $val_nome) $return[]=array('cat_id'=>$obj2->db_data['cat_id'],'cat_nome'=>$cat_nome['category_name'],'valor_nome'=>$val_nome,'valor_id'=>$obj2->db_data['valor_id']);
		$obj2->db_next();
	}
	$obj2->db_close();
	return $return;
}

function show_global_promos($prod_id=0)
{
	$promo='';
	global $site_lang;
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;
	$sql="SELECT Promo_data_ini,Promo_data_fim,Promo_descricao FROM products_objs WHERE obj_type_id=$prod_id AND obj_type='products'";
	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	if(!$rs->EOF())
	{
		$promo='{ "designacao" : "'.$rs->db_data['Promo_descricao'].'","data_inicio" : "'.$rs->db_data['Promo_data_ini'].'","data_fim" : "'.$rs->db_data['Promo_data_fim'].'","descricao" : "'.$rs->db_data['Promo_descricao'].'"}';
	}
	$rs->db_close();
	return $promo;
}

function get_produtc_price_combo($prod_id,$obj_id)
{
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;$rs2=new DataObj;
	if(isset($prod_id) && $prod_id > 0)
	{
		$sql='SELECT p.PrecoPromoMargem,p.descontopreco,poc.obj_iva,poc.obj_pvp,poc.obj_promo,poc.obj_promo_ativo,poc.obj_stock,poc.obj_ref,poc.obj_destaque,poc.obj_travelsize,poc.obj_novo,poc.obj_outlet FROM products_objs_combo poc INNER JOIN products p on p.ID=poc.obj_type_id WHERE poc.obj_type="products" AND poc.obj_type_id="'.$prod_id.'" AND poc.obj_id="'.$obj_id.'" ORDER BY poc.obj_pvp ASC LIMIT 1';
		$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
		if(!$rs->EOF())
		{
			//Vamos aplicar um desconto de 4% aos produtos que venham da macorlux
			$valor_pvp=$rs->db_data['obj_pvp'];
			$valor_final_pvp=$valor_pvp;
			$valor_pvp_promo=$rs->db_data['obj_promo'];
			$valor_final_pvp_promo=$valor_pvp_promo;
			//Vamos adicionar 10 euros de portes
			$preco 		= ($valor_final_pvp );
			$precopromo=($valor_final_pvp_promo);
			// NECESSITAMOS DE SABER O ID_ORG DO PRODUTO
			$sql='SELECT pfo.ID_ORG FROM products p INNER JOIN products_families_obj pfo ON p.ID=pfo.ID_PROD WHERE p.ID='.$prod_id;
			$rs2->db_open($db_link,$GLOBALS['DB_name'],$sql);
			if(!$rs2->EOF()) $cat=$rs2->db_data['ID_ORG'];
			$rs2->db_close();
			// VAMOS VERIFICAR SE AS FAMILIAS DO PRODUTO TEM ALGUMA MARGEM
			$margem=get_product_families($cat);
			$margem_formula=get_product_families_Margem_formula($cat);
			//Falta ir buscar a pertentagem
			// VAMOS COLOCAR MARGEM no preco
			if($margem != 0 && $preco > 0) $preco=$preco + ($preco * ($margem / 100));
			if($margem != 0 && $precopromo > 0) $precopromo=$precopromo + ($precopromo * ($margem / 100));
			/*if($rs->db_data['obj_promo'] > 0 && $preco > $precopromo)*/
			if($rs->db_data['obj_promo'] > 0 && $precopromo > 0)
			{
				return array(
				'Modelo' 			=> htmlentities_memo(stripslashes($rs->db_data['obj_ref'])),
				'Medida' 			=> '',
				'PVP' 				=> $precopromo,
				'PRECO_ANTIGO' 		=> $preco,
				'STOCK' 			=> $rs->db_data['obj_stock'],
				'PROMOCAO' 			=> $rs->db_data['obj_promo_ativo'],
				'DESTAQUE' 			=> $rs->db_data['obj_destaque'],
				'OUTLET' 			=> $rs->db_data['obj_outlet'],
				'TRAVELSIZE' 		=> $rs->db_data['obj_travelsize'],
				'NOVO' 				=> $rs->db_data['obj_novo'],
				'IVA' 				=> $rs->db_data['obj_iva']);
			}
			else
			{
				return array(
				'Modelo' 			=> htmlentities_memo(stripslashes($rs->db_data['obj_ref'])),
				'Medida' 			=> '',
				'PVP' 				=> $preco,
				'PRECO_ANTIGO' 		=> 0,
				'STOCK' 			=> $rs->db_data['obj_stock'],
				'PROMOCAO' 			=> $rs->db_data['obj_promo_ativo'],
				'DESTAQUE' 			=> $rs->db_data['obj_destaque'],
				'OUTLET' 			=> $rs->db_data['obj_outlet'],
				'TRAVELSIZE' 		=> $rs->db_data['obj_travelsize'],
				'NOVO' 				=> $rs->db_data['obj_novo'],
				'IVA' 				=> $rs->db_data['obj_iva']);
			}
		}
		$rs->db_close();
	}
	return;
}

function filtros_array($prod_id=0,$fam_id=0)
{
	global $site_lang;
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$obj2=new DataObj; $filtros=array();$return='';
	$sql2='SELECT fp.cat_id,fp.valor_id,fp.filtro_area
			FROM filters_products fp
			WHERE
				(fp.fam_id1='.$fam_id.' OR fp.fam_id2='.$fam_id.')
				AND prod_id='.$prod_id.'
				AND fp.filtros_rel2 <> ""
				AND fp.filtro_active=1
			GROUP BY fp.valor_id,fp.cat_id
			Order By fp.filtro_id asc';
	$obj2->db_open($db_link,$GLOBALS['DB_name'],$sql2);
	$a=0;
	while(!$obj2->EOF())
	{
		$filtros[$obj2->db_data['cat_id']]=buscavalorfiltros($obj2->db_data['valor_id']);
		$obj2->db_next();
	}
	$obj2->db_close();
	return $filtros;
}

function filtros_lista_produto($prod_id=0,$fam_id=0,$area=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj2 = new DataObj;
	$cores = new DataObj;
	$dados = new DataObj;
	$filtros = array();$return = '';
	$sql2 = '
			SELECT
				fp.cat_id,fp.valor_id,fp.filtro_area
			FROM
				filters_products fp
			WHERE
				(fp.fam_id1='.$fam_id.' OR fp.fam_id2='.$fam_id.')
				AND prod_id='.$prod_id.'
				AND fp.filtros_rel2 <> ""
				AND fp.filtro_active = 1
				AND  (fp.filtro_area='.$area.')
			GROUP BY
				fp.valor_id,fp.cat_id
			Order By
				fp.valor_id desc
			';

	//echo $sql2;
	$obj2->db_open($db_link,$DB_name,$sql2);
	$a = 0;
	while(!$obj2->EOF())
	{
		$filtros[$obj2->db_data['cat_id']][] = $obj2->db_data['valor_id'];
		$obj2->db_next();
	}
	$obj2->db_close();
	//print_r($filtros);
	$return .='<div class="prod_filtros">';
	foreach($filtros as $key=>$value)
	{
		$cat = buscacategoriaTipoeNome($key);
		$category_type = buscacategoriaTipofiltros($key);
		$return .= '<div class="div'.$key.'">';
		$return .= '<div class="categ" data="subval'.$key.'">';
		if($cat['category_name']!='')$return .= '<div style="text-transform:uppercase;font-weight:bold;float:left;margin-right:10px">'.$cat['category_name'].': </div>';
		//$return .= '<div style="float:left;border:#000 0px solid;margin-bottom:5px;padding-top:5px" id="subval'.$key.'" class="subval">';
		//$return .= '</div>';
		$values_as_string = implode(",",$value);

		//$sql="SELECT value_id,values_name,value_order,value_ext from filters_values INNER JOIN filters_values_lang ON filters_values_lang.values_id = filters_values.value_id  where filters_values.value_id IN ($values_as_string) and filters_values_lang.values_lang='".$site_lang."' ORDER BY value_order ASC";
		$sql="SELECT DISTINCT value_id,values_name,value_order,value_ext,filters_values.value_id_import_ref from filters_values INNER JOIN filters_values_lang ON filters_values_lang.values_id = filters_values.value_id  where filters_values.value_id IN ($values_as_string) and filters_values_lang.values_lang='".$site_lang."' ORDER BY value_order ASC";
		$dados->db_open($db_link,$DB_name,$sql);
		while(!$dados->EOF())
		{
			$value_id = $dados->db_data['value_id'];
			$values_name = $dados->db_data['values_name'];
			$value_order = $dados->db_data['value_order'];
			$value_ext = $dados->db_data['value_ext'];
			$value_id_import_ref = $dados->db_data['value_id_import_ref'];

			if($category_type == 2)
			{
				$sql6 = 'SELECT fv.values_colorhex FROM filters_values_lang fv WHERE fv.values_id='.$value_id;
				$cores->db_open($db_link,$DB_name,$sql6);
				$hex = '';if(!$cores->EOF()) $hex = $cores->db_data['values_colorhex'];
				$cores->db_close();

				if(!$hex)
				{
					if($value_ext &&  dotbright_image_resolve($GLOBALS['site_shell_url'].'files/filters/'.$value_id.'_1.'.$value_ext))$return .= '<img src="'.$GLOBALS['site_http_url'].'files/filters/'.$value_id.'_1.'.$value_ext.'" alt="" width="40" border="0" />';
					else $return .= '<div style="margin-left:10px"  title="'.$values_name.'">'.$values_name.'</div>';
				}
				else $return .= '<div style="float:left;background:'.$hex.';width:30px;height:30px" class="color_check round" title="'.$values_name.'">&nbsp;</div>';
			}
			elseif($category_type == 3)
			{
				$return .= '<span class="care" style="margin-left:0px"  title="'.$values_name.'">'.$value_id_import_ref.'</span>';
			}
			else
			{
				if($value_ext &&  dotbright_image_resolve($GLOBALS['site_shell_url'].'files/filters/'.$value_id.'_1.'.$value_ext)) $return .= '<img style="margin-right:10px" src="'.$GLOBALS['site_http_url'].'files/filters/'.$value_id.'_1.'.$value_ext.'" title="'.$values_name.'" alt="" width="40" border="0" />';
				else $return .= '<span style="margin-left:10px"  title="'.$values_name.'">'.$values_name.'</span>';
			}
			$dados->db_next();
		}
		$dados->db_close();


		$return .= '</div></div>';
	}
	$return .='</div><div class="c"></div>';
	return $return;
}

function filtros_lista_produto_na_ficha($prod_id=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj2 = new DataObj;
	$cores = new DataObj;
	$dados = new DataObj;
	$filtros = array();$return = '';
	$sql2 = '
			SELECT
				fp.cat_id,fp.valor_id,fp.filtro_area
			FROM
				filters_products fp
			WHERE
				prod_id='.$prod_id.'
				AND fp.filtros_rel2 <> ""
				AND fp.filtro_active = 1
			GROUP BY
				fp.valor_id,fp.cat_id
			Order By
				fp.valor_id desc
			';

	//echo $sql2;
	$obj2->db_open($db_link,$DB_name,$sql2);
	$a = 0;
	while(!$obj2->EOF())
	{
		$filtros[$obj2->db_data['cat_id']][] = $obj2->db_data['valor_id'];
		$obj2->db_next();
	}
	$obj2->db_close();
	//print_r($filtros);
	$return .='<div class="prod_filtros">';
	foreach($filtros as $key=>$value)
	{
		$cat = buscacategoriaTipoeNome($key);
		$category_type = buscacategoriaTipofiltros($key);
		$return .= '<div class="div'.$key.'">';
		$return .= '<div class="categ" data="subval'.$key.'">';
		if($cat['category_name']!='')$return .= '<div style="text-transform:uppercase;font-weight:bold;float:left;margin-right:10px">'.$cat['category_name'].': </div>';
		//$return .= '<div style="float:left;border:#000 0px solid;margin-bottom:5px;padding-top:5px" id="subval'.$key.'" class="subval">';
		//$return .= '</div>';
		$values_as_string = implode(",",$value);

		//$sql="SELECT value_id,values_name,value_order,value_ext from filters_values INNER JOIN filters_values_lang ON filters_values_lang.values_id = filters_values.value_id  where filters_values.value_id IN ($values_as_string) and filters_values_lang.values_lang='".$site_lang."' ORDER BY value_order ASC";
		$sql="SELECT DISTINCT value_id,values_name,value_order,value_ext,filters_values.value_id_import_ref from filters_values INNER JOIN filters_values_lang ON filters_values_lang.values_id = filters_values.value_id  where filters_values.value_id IN ($values_as_string) and filters_values_lang.values_lang='".$site_lang."' ORDER BY value_order ASC";
		$dados->db_open($db_link,$DB_name,$sql);
		while(!$dados->EOF())
		{
			$value_id = $dados->db_data['value_id'];
			$values_name = $dados->db_data['values_name'];
			$value_order = $dados->db_data['value_order'];
			$value_ext = $dados->db_data['value_ext'];
			$value_id_import_ref = $dados->db_data['value_id_import_ref'];

			if($category_type == 2)
			{
				$sql6 = 'SELECT fv.values_colorhex FROM filters_values_lang fv WHERE fv.values_id='.$value_id;
				$cores->db_open($db_link,$DB_name,$sql6);
				$hex = '';if(!$cores->EOF()) $hex = $cores->db_data['values_colorhex'];
				$cores->db_close();

				if(!$hex)
				{
					if($value_ext &&  dotbright_image_resolve($GLOBALS['site_shell_url'].'files/filters/'.$value_id.'_1.'.$value_ext))$return .= '<img src="'.$GLOBALS['site_http_url'].'files/filters/'.$value_id.'_1.'.$value_ext.'" alt="" width="40" border="0" />';
					else $return .= '<div style="margin-left:10px"  title="'.$values_name.'">'.$values_name.'</div>';
				}
				else $return .= '<div style="float:left;background:'.$hex.';width:30px;height:30px" class="color_check round" title="'.$values_name.'">&nbsp;</div>';
			}
			elseif($category_type == 3)
			{
				$return .= '<span class="care" style="margin-left:0px"  title="'.$values_name.'">'.$value_id_import_ref.'</span>';
			}
			else
			{
				if($value_ext &&  dotbright_image_resolve($GLOBALS['site_shell_url'].'files/filters/'.$value_id.'_1.'.$value_ext)) $return .= '<img style="margin-right:10px" src="'.$GLOBALS['site_http_url'].'files/filters/'.$value_id.'_1.'.$value_ext.'" title="'.$values_name.'" alt="" width="40" border="0" />';
				else $return .= '<span style="margin-left:10px"  title="'.$values_name.'">'.$values_name.'</span>';
			}
			$dados->db_next();
		}
		$dados->db_close();


		$return .= '</div></div>';
	}
	$return .='</div><div class="c"></div>';
	return $return;
}

function filtros_lista_produto_OLD($prod_id=0,$fam_id=0,$area=0)
{
	global $site_lang;
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$obj2=new DataObj; $filtros=array();$return='';
	$sql2='SELECT
				fp.cat_id,fp.valor_id,fp.filtro_area
			FROM
				filters_products fp
			WHERE
				(fp.fam_id1='.$fam_id.' OR fp.fam_id2='.$fam_id.')
				AND prod_id='.$prod_id.'
				AND fp.filtros_rel2 <> ""
				AND fp.filtro_active=1
				AND fp.filtro_area='.$area.'
			GROUP BY fp.valor_id,fp.cat_id
			Order By fp.filtro_id asc';
	$obj2->db_open($db_link,$GLOBALS['DB_name'],$sql2);
	$a=0;
	while(!$obj2->EOF())
	{
		$filtros[$obj2->db_data['cat_id']][]=$obj2->db_data['valor_id'];
		$obj2->db_next();
	}
	$obj2->db_close();
	foreach($filtros as $key=>$value)
	{
		$cat=buscacategoriaTipoeNome($key);
		if($cat['category_name'] != '')
		{
			$return .= ''.$cat['category_name'].': ';
			$return .= '';
			for($a=0;$a<count($value); $a++)
			{
				$nm_value=buscavalorfiltros($value[$a]);
				$return .=  '<span>'.$nm_value.'</span>';
			}
			$return .= '<br />';
		}
	}
	return $return;
}

function produto_google_breadcrums()
{
	$result='';
	$str='';
	if(isset($_GET['cat'])) $cat=$_GET['cat'];
	global $m_http_url_images,$site_http_url,$tema,$site_lang;
    $db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
    $rs=new Dataobj;
	if(isset($_GET['prod_id']) && $_GET['prod_id'] > 0){
		$sql='select pfo.ID_ORG,pl.Cabecalho from products p inner join products_lang pl on p.ID=pl.ID_PAI inner join products_families_obj pfo on p.ID=pfo.ID_PROD where pl.Lang="'.$GLOBALS['site_lang'].'" and p.ID='.$_GET['prod_id'];
		$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
		if(!$rs->EOF()) {
			$cat=$rs->db_data['ID_ORG'];
			$nome_prod=htmlentities_memo(stripslashes(mb_strtolower($rs->db_data['Cabecalho'],"UTF-8")));
		}
		$rs->db_close();
	}
	if(isset($cat) && $cat > 0) {
		$p_seo=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$cat,$cat,1);
		$sql='select pf.ID,pf.ID_ORG,pfl.Cabecalho,pfl.Cabecalho2 from products_families pf inner join products_families_lang pfl on (pf.ID=pfl.ID_PAI) where pf.visivel=1 and pfl.Lang="'.$GLOBALS['site_lang'].'" and pf.ID='.$cat;
		$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
		if(!$rs->EOF()){
			$p_seo=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$rs->db_data['ID'],$rs->db_data['ID'],1);
			if($rs->db_data['ID_ORG']!=0){
				$str=produto_google_breadcrums_recursivo($rs->db_data['ID_ORG'],0);
				if($rs->db_data['Cabecalho2'])  $str .= '&nbsp;&gt;&nbsp;'.htmlentities_memo(stripslashes(mb_strtolower($rs->db_data['Cabecalho2'],"UTF-8"))).'';
				else $str .= '&nbsp;&gt;&nbsp;'.htmlentities_memo(stripslashes(mb_strtolower($rs->db_data['Cabecalho'],"UTF-8"))).'';
			}
		}
		$rs->db_close();
	}
	$result.=$str;
	return $result;
}

function produto_google_breadcrums_recursivo($id_pai,$pp)
{
	global $m_http_url_images,$site_http_url,$tema,$site_lang;
    $db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
    $rs=new Dataobj;
    $sql='select pf.ID,pf.ID_ORG,pfl.Cabecalho from products_families pf inner join products_families_lang pfl on (pf.ID=pfl.ID_PAI) where pf.visivel=1  and pfl.Lang="'.$GLOBALS['site_lang'].'" and pf.ID='.$id_pai;
    $rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
    $str='';
    if(!$rs->EOF())
	{
    	$p_seo=str_replace($GLOBALS['site_http_url'],'',seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$rs->db_data['ID'],$rs->db_data['ID'],1));
		if($rs->db_data['ID_ORG']==0)
		{
			if($pp==0) $str=htmlentities_memo(stripslashes(mb_strtolower($rs->db_data['Cabecalho'],"UTF-8"))).''.$str;
			else  $str='&nbsp;&gt;&nbsp;'.htmlentities_memo(stripslashes(mb_strtolower($rs->db_data['Cabecalho'],"UTF-8"))).''.$str;
		}
		else
		{
			if($pp==0) $str=htmlentities_memo(stripslashes(mb_strtolower($rs->db_data['Cabecalho'],"UTF-8"))).''.$str;
			else  $str='&nbsp;&gt;&nbsp;'.htmlentities_memo(stripslashes(mb_strtolower($rs->db_data['Cabecalho'],"UTF-8"))).''.$str;

    		$pp++;
			$str=produto_google_breadcrums_recursivo($rs->db_data['ID_ORG'],$pp).$str;
		}
	}
	$rs->db_close();
	return $str;
}

function produto_google_rich_snippets($prods)
{
	if($_GET['amp']==1)return '';

	global $site_http_url;
	$code='';
	$code.='<div class="richsnippets" itemscope itemtype="http://data-vocabulary.org/Product">';
	if($prods['prod_marca']['marca_nome']!='') $code.='<span itemprop="brand">'.$prods['prod_marca']['marca_nome'].'</span>';
	if($prods['prod_nome']!='') $code.='<span itemprop="name">'.$prods['prod_nome'].'</span>';
	if(!substr_count($prods['prd_imgm_med'],'no_photo')) $code.='<img itemprop="image" alt="'.$prods['prod_nome'].'" src="'.$site_http_url.$prods['prd_imgm_med'].'" />';
	if(rtrim(strip_tags($prods['prod_features']))!="") $code.='<span itemprop="description">'.rtrim(ltrim(strip_tags(str_replace(array('<br />','<br>'),' | ',html_entity_decode($prods['prod_features'],ENT_QUOTES,"UTF-8"))))).'</span>';
	$ppath=produto_google_breadcrums();
	$code.='<span itemprop="category" content="'.$ppath.'">'.$ppath.'</span>';
	$code.='<span itemprop="offerDetails" itemscope itemtype="http://data-vocabulary.org/Offer">';
		$code.='<meta itemprop="currency" content="'.MOEDA_ISO.'" />';
		$code.='<span itemprop="price">'.str_replace(',','.',str_replace(array('.','&euro;',' '),'',numberformat($prods['prod_precos']['PVP'],2,'.',''))).'</span>';
		$code.='<span itemprop="condition" content="new">Novo</span>';
		if($prods['prod_precos']['STOCK'] > 0 && $prods['prod_precos']['STOCK'] < 3)$code.='<span itemprop="availability" content="in_stock">Produto Limitado</span>';
		else if($prods['prod_precos']['STOCK'] >= 3)$code.='<span itemprop="availability" content="in_stock">Produto disponível</span>';
		else $code.='<span itemprop="availability" content="out_of_stock">Produto esgotado</span>';
	$code.='</span>';
	$code.='</div>';
	return $code;
}

function produto()
{
	global $site_http_url,$site_http_ajax_url,$site_lang,$site_shell_url;
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$prods=\get_produtc_info($_GET['prod_id']);
	if($prods['fam_id'] == '163')
	{
		//se o produto que está na familia de usados só pode ser apresentado se tiver stock
		if($prods['prod_precos']['STOCK']<=0)
		{
			$prods = NULL;
		}
	}
	if($prods !== NULL)
	{
		$rs_metodos=new DataObj;
		if($dados=\cliente_logado()) inserirProdutoVisita($dados['cli_id'],$prods['prod_id']);
		$sql='update LOW_PRIORITY products set visitas=visitas+1 where ID='.$_GET['prod_id'];
		$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
		$return='';
		$return .= produto_google_rich_snippets($prods);
		$gerar='';
		$return .= ficha_produto($prods);
	}
	else
	{
		echo show_alerta2('Produto não existe ou encontra-se indisponível.');
	}
	return $return;
}

function produto_novidade()
{
	$return='';
	$prods=get_produtc_new('novidade');
	if($prods!='')
	{
		$link=$site_http_url.\seo2(2,$GLOBALS['products_page_id'],$prods,$GLOBALS['site_lang']);
		$return ='<div class="novidade" onclick="window.open(\''.$link.'\',\'_top\')">';
		$return .= '<div class="title">';
		$return .= getLinguas('key_ultimasnovidades',$GLOBALS['site_lang']);
		$return .= '</div>';
		$return .= '<div class="image">';
		$return .= '<img src="'.$prods['prd_imgm_new'].'" alt="" title="'.$prods['prod_nome'].'" align="left" />'.$prods['prod_descricao'];
		$return .= '</div>';
		$return .= '</div>';
	}
	return $return;
}

function produto_destaque()
{
	$dados=\cliente_logado();
	global $key_apagar,$m_resumo;
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;$rs2=new DataObj;
	$t=2;
	$t2=0;
	for($b=1;$b<=count($bloco_fam);$b++)
	{
		$stl='';
		if($t==$t2)$t2=0;
		if($t2>0) $stl=';margin-left:2px';
		if($bloco_fam[$b]['FAM']==0)$return .= show_lista_noticias_last(8,$stl);
		else
		{
			$info=get_family_info($bloco_fam[$b]['FAM']);
			$return .= '<div class="destaques destaques-curto" style="float:left;border:#000 0px solid;margin-bottom:5px;width:454px;height:225px'.$stl.'"><div class="destaques-titulo">'.getLinguas('key_destaques',$GLOBALS['site_lang']).' - '.$info['fam_nome'].'<span></span></div><div class="destaques-produtos">';
				$prodsx=get_produtc_new('destaque',2,2,$bloco_fam[$b]['FAM']);
				for($a=0;$a<count($prodsx['prod_id']);$a++)
				{
					if($a==0) $style='destaques-produto-item destaques-produto-item-first';
					else $style='destaques-produto-item';
					$idx=$prodsx['prod_id'][$a];
					if($dados)
					{
						if($prodsx['prod_precos'][$a]['PRECO_ANTIGO'] > 0) $return .= '<div class="old-pvp">'.numberformat($prodsx['prod_precos'][$a]['PRECO_ANTIGO']).'</div>';
					}
					$url=$site_http_url.\seo2(2,$GLOBALS['products_page_id'],$prodsx,$GLOBALS['site_lang']);
					$return .= '<div class="'.$style.'" style="width:224px">';
					if($prodsx['prod_precos'][$a]['PRECO_ANTIGO'] > 0 && $dados=\cliente_logado()) $return .= '<div class="old-pvp">'.getLinguas('key_antes',$GLOBALS['site_lang']).' <span>'.numberformat($prodsx['prod_precos'][$a]['PRECO_ANTIGO']).'</span></div>';
					$return .= '<div class="destaques-nome">'.$prodsx['prod_nome'][$a].'</div>';
					$return .= '<div class="destaques-img" style="text-align:center"><a href="'.$url.'"><img src="'.$prodsx['prd_imgm_new'][$a].'" alt="" title="'.$prodsx['prod_nome'][$a].'" /></a></div>';
					$return .= '<div class="floatRight">';
					if($dados=\cliente_logado())
					{
						$return .= '<div class="destaques-preco">'.getLinguas('key_desde',$GLOBALS['site_lang']).' ';
						$return .= numberformat($prodsx['prod_precos'][$a]['PVP']);
						$return .= '</div>';
					}
					$return .= '<div class="destaques-vermais"><a href="'.$url.'">'.getLinguas('key_ver',$GLOBALS['site_lang']).' +</a></div>';
					$return .= '</div>';
					$return .= '</div>';
				}
			$return .= '</div></div>';
		}
		$t2++;
	}
	$return .= '<div class="c"></div>';
	return $return;
}

function get_produtc_info_cache($prod_id=0,$prod_obj_id='',$gera_images=1,$id_fam=0,$lingua='')
{
	global $prod_id_cache,$prod_dados;
	if($prod_id_cache != $prod_id)
	{
		$prod_dados=get_produtc_info($prod_id,$prod_obj_id,$gera_images,$id_fam,$lingua);
		$prod_id_cache = $prod_id;
	}
	return $prod_dados;
}

function del_produtc_info($prod_id=0,$prod_obj_id='',$gera_images=1,$id_fam=0,$lingua='',$gerar=0)
{
	if($lingua == '')
	{
		if(isset($_SERVER['REQUEST_URI']) && substr_count($_SERVER['REQUEST_URI'],'/admin/')) $lingua=$GLOBALS['admin_lang'];
		else $lingua=$GLOBALS['site_lang'];
	}
	$prods=NULL;

	if(dotbright_image_resolve($GLOBALS['site_shell_url'].'files/cache/products/p'.dotbright_product_images_cache_key().$prod_id.'_'.$lingua.'.bin'))
	{
		unlink($GLOBALS['site_shell_url'].'files/cache/products/p'.dotbright_product_images_cache_key().$prod_id.'_'.$lingua.'.bin');
	}
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$sql = "DELETE FROM acelerador_pesquisa WHERE ID_Prod=".$prod_id;
	if(!Runquery($db_link,$GLOBALS['DB_name'],$sql)) echo "Data Base ERROR: " . db_error($db_link).$sql."<br>";
}

function get_reparacoes_img($ref)
{
	$img='';
	if(substr($ref, 0,3)=='REP')
	{
		//if(substr_count($ref, 'DIAGNOSTICO')) $img='diagno.jpg';
		if(substr_count($ref, 'FRP')) $img='GOOGLE.jpg';
		if(substr_count($ref, 'SIM')) $img='GAVETASIM.jpg';
		if(substr_count($ref, 'TOUCH')) $img='TOUCH.jpg';
		if(substr_count($ref, 'LCD')) $img='LCD.jpg';
		if(substr_count($ref, 'WIFI')) $img='WIFI.jpg';
		if(substr_count($ref, 'REDE')) $img='REDE.jpg';
		if(substr_count($ref, 'TDN')) $img='TDN.jpg';
		if(substr_count($ref, 'TDDI')) $img='TDDI.jpg';
		if(substr_count($ref, 'CAMARA')) $img='CAMARA.jpg';
		if(substr_count($ref, 'UNLOCK')) $img='UNLOCK.jpg';
		if(substr_count($ref, 'CODIGO')) $img='CODIGO.jpg';
		if(substr_count($ref, 'SWAP')) $img='SWAP.jpg';
		if(substr_count($ref, 'HOME')) $img='HOME.jpg';
		if(substr_count($ref, 'MODULO')) $img='MODULO.jpg';
		if(substr_count($ref, 'MODULOFRAME')) $img='MODULO_aro.jpg';
		if(substr_count($ref, 'CCARGA')) $img='CCARGA.jpg';
		if(substr_count($ref, 'LEITORSIM')) $img='LEITORSIM.jpg';
		if(substr_count($ref, 'AUSCULTADOR')) $img='AUSCULTADOR.jpg';
		if(substr_count($ref, 'MICROFONE')) $img='MICROFONE.jpg';
		if(substr_count($ref, 'ALTIFALANTE')) $img='ALTIFALANTE.jpg';
		if(substr_count($ref, 'BATERIA')) $img='BATERIA.jpg';
		if(substr_count($ref, 'CARCACA')) $img='CARCACA.jpg';
		if(substr_count($ref, 'DIAGNOSTICO')) $img='DIAGNOSTICO.jpg';
		if(substr_count($ref, 'ONOFF')) $img='ONOFF.jpg';
		if(substr_count($ref, 'SILENCIO')) $img='SILENCIO.jpg';
		if(substr_count($ref, 'VOLUME')) $img='VOLUME.jpg';
		if(substr_count($ref, 'VIBRADOR')) $img='VIBRADOR.jpg';
		if(substr_count($ref, 'SENSOR')) $img='SENSOR.jpg';
		if(substr_count($ref, 'ICCARGA')) $img='IC_BOARD.jpg';
		if(substr_count($ref, 'AUDIOJACK')) $img='AUDIOJACK.jpg';
		if(substr_count($ref, 'VIDROTRAS')) $img='TRASEIRA.jpg';
		if(substr_count($ref, 'DILCD')) $img='DILCD.jpg';
		if(substr_count($ref, 'NLCD')) $img='NLCD.jpg';
		if(substr_count($ref, 'CAM3X')) $img='CAM3X.jpg';
		if(substr_count($ref, 'CAM2V')) $img='CAM2V.jpg';
		if(substr_count($ref, 'CAM2D')) $img='CAM2D.jpg';
		if(substr_count($ref, 'VTR3X')) $img='VTR3X.jpg';
		if(substr_count($ref, 'VTR2V')) $img='VTR2V.jpg';
		if(substr_count($ref, 'VTR2D')) $img='VTR2D.jpg';
		if(substr_count($ref, 'LCAM3X')) $img='LCAM3X.jpg';
		if(substr_count($ref, 'LCAM2V')) $img='LCAM2V.jpg';
		if(substr_count($ref, 'LCAM2D')) $img='LCAM2D.jpg';
		if(substr_count($ref, 'BMF')) $img='BMF.jpg';
		if(substr_count($ref, 'CFN')) $img='CFN.jpg';
		if(substr_count($ref, 'CFDI')) $img='CFDI.jpg';
		if(substr_count($ref, 'CARC2V')) $img='CARC2V.jpg';
		if(substr_count($ref, 'CARC2D')) $img='CARC2D.jpg';
		if(substr_count($ref, 'CARC3X')) $img='CARC3X.jpg';
		if(substr_count($ref, 'OLEDDILCD')) $img='DILCD.jpg';
		if(substr_count($ref, 'OLEDNLCD')) $img='NLCD.jpg';
		if(substr_count($ref, 'MBLCD')) $img='LCD_MAC.jpg';
		if(substr_count($ref, 'IMLCD')) $img='LCD_IMAC.jpg';
		if(substr_count($ref, 'MBBATERIA')) $img='BATERIA_MAC.jpg';
		if(substr_count($ref, 'IMBATERIA')) $img='BATERIA_IMAC.jpg';
		
		if(substr_count($ref, 'LENTE1CC')) $img='LENTE1CC.jpg';
		if(substr_count($ref, 'LENTE1CE')) $img='LENTE1CE.jpg';
		if(substr_count($ref, 'LENTE1CEV')) $img='LENTE1CEV.jpg';
		if(substr_count($ref, 'LENTE2CC')) $img='LENTE2CC.jpg';
		if(substr_count($ref, 'LENTE2CEV')) $img='LENTE2CEV.jpg';
		if(substr_count($ref, 'LENTE2CEH')) $img='LENTE2CEH.jpg';
		if(substr_count($ref, 'LENTEi2C')) $img='LENTEi2C.jpg';
		if(substr_count($ref, 'LENTE3C')) $img='LENTE3C.jpg';
		if(substr_count($ref, 'LENTEi3C')) $img='LENTEi3C.jpg';
		if(substr_count($ref, 'LENTE4C')) $img='LENTE4C.jpg';
		if(substr_count($ref, 'CAMARA1CC')) $img='CAM1CC.jpg';
		if(substr_count($ref, 'CAMARA1CE')) $img='CAM1CE.jpg';
		if(substr_count($ref, 'CAMARA1CEV')) $img='CAM1CEV.jpg';
		if(substr_count($ref, 'CAMARA2CC')) $img='CAM2CC.jpg';
		if(substr_count($ref, 'CAMARA2CEV')) $img='CAM2CEV.jpg';
		if(substr_count($ref, 'CAMARA2CEH')) $img='CAM2CEH.jpg';
		if(substr_count($ref, 'CAMARAi2C')) $img='CAMi2C.jpg';
		if(substr_count($ref, 'CAMARA3C')) $img='CAM3C.jpg';
		if(substr_count($ref, 'CAMARAi3C')) $img='CAMi3C.jpg';
		if(substr_count($ref, 'CAMARA4C')) $img='CAM4C.jpg';
		if(substr_count($ref, 'TTRASEIRA2CC')) $img='TTRAS2CC.jpg';
		if(substr_count($ref, 'TTRASEIRA3CC')) $img='TTRAS3CC.jpg';
		if(substr_count($ref, 'VIDROTRAS2CC')) $img='VIDROTRAS2CC.jpg';
		if(substr_count($ref, 'VIDROTRAS2EH')) $img='VIDROTRAS2EH.jpg';
		if(substr_count($ref, 'VIDROTRAS2EV')) $img='VIDROTRAS2EV.jpg';
		if(substr_count($ref, 'VIDROTRASi2C')) $img='VIDROTRASi2C.jpg';
		if(substr_count($ref, 'VIDROTRAS3CC')) $img='VIDROTRAS3CC.jpg';
		if(substr_count($ref, 'VIDROTRASi3C')) $img='VIDROTRASi3C.jpg';
		if(substr_count($ref, 'REBALLING')) $img='REBALLING.jpg';
		
		if(substr_count($ref, 'LENTECAMARA')) $img='LENTE.jpg';
		if(substr_count($ref, 'TAMPATRASEIRA')) $img='TRASEIRA.jpg';
		if(substr_count($ref, 'CAMARAFRONTAL')) $img='CAMARAFRONTAL.jpg';
		if(substr_count($ref, 'VIDROTRASEIRO')) $img='TRASEIRA.jpg';
		if(substr_count($ref, 'MODULOORIG')) $img='MODULO.jpg';
		if(substr_count($ref, 'MODULOCOMP')) $img='OLED.jpg';
		
		if(substr_count($ref, 'TDDIOFI')) $img='TDDIOFI.jpg';
		if(substr_count($ref, 'TDNOFI')) $img='TDNOFI.jpg';
		if(substr_count($ref, 'DILCDOFI')) $img='DILCDOFI.jpg';
		if(substr_count($ref, 'NLCDOFI')) $img='NLCDOFI.jpg';
		if(substr_count($ref, 'BATOFI')) $img='BATOFI.jpg';
		if(substr_count($ref, 'CAM3XOFI')) $img='CAM3XOFI.jpg';
		if(substr_count($ref, 'CAM2VOFI')) $img='CAM2VOFI.jpg';
		if(substr_count($ref, 'CAM2DOFI')) $img='CAM2DOFI.jpg';
		if(substr_count($ref, 'TDDIOFI')) $img='TDDIOFI.jpg';

		if(substr_count($ref, 'REPIPHONE7AUSCULTADOR')) $img='AUSCULTADOR.jpg';
		if(substr_count($ref, 'REPIPHONE7AUSC')) $img='AUSCULTADOR.jpg';
		if(substr_count($ref, 'REPIPHONE7SPEAKER')) $img='AUSCULTADOR.jpg';
		if(substr_count($ref, 'REPIPHONE7MICROFONE')) $img='MICROFONE.jpg';
		if(substr_count($ref, 'REPIPHONE7MIC')) $img='MICROFONE.jpg';
		if(substr_count($ref, 'REPIPHONE7ALTIFALANTE')) $img='ALTIFALANTE.jpg';
		if(substr_count($ref, 'REPIPHONE7ALTI')) $img='ALTIFALANTE.jpg';
		if(substr_count($ref, 'REPIPHONE7CAMARA')) $img='CAMARA.jpg';
		if(substr_count($ref, 'REPIPHONE7CAMARAFRONTAL')) $img='CAMARAFRONTAL.jpg';
		if(substr_count($ref, 'REPIPHONE7CAMARAFRONTAL')) $img='CAMARAFRONTAL.jpg';
		if(substr_count($ref, 'REPIPHONE7VIBRADOR')) $img='VIBRADOR.jpg';
		if(substr_count($ref, 'REPIPHONE7VIBRA')) $img='VIBRADOR.jpg';
		if(substr_count($ref, 'REPIPHONE7ONOFF')) $img='ONOFF.jpg';
		if(substr_count($ref, 'REPIPHONE7SILENCIO')) $img='SILENCIO.jpg';
		if(substr_count($ref, 'REPIPHONE7MUTE')) $img='SILENCIO.jpg';
		if(substr_count($ref, 'REPIPHONE7VOLUME')) $img='VOLUME.jpg';
		if(substr_count($ref, 'REPIPHONE7HOME')) $img='HOME.jpg';
		if(substr_count($ref, 'REPIPHONE7SENSOR')) $img='SENSOR.jpg';
		if(substr_count($ref, 'REPIPHONE7AUDIOJACK')) $img='AUDIOJACK.jpg';
		if(substr_count($ref, 'REPIPHONE7CARGA')) $img='CCARGA.jpg';
		if(substr_count($ref, 'REPIPHONE7BATERIA')) $img='BATERIA.jpg';
		if(substr_count($ref, 'REPIPHONE7CARCACA')) $img='CARCACA.jpg';
		if(substr_count($ref, 'REPIPHONE7TRAS')) $img='VIDROTRASEIRO.jpg';
		if(substr_count($ref, 'REPSGA705TRASEIRA')) $img='TAMPA_TRASEIRA.jpg';
		if(substr_count($ref, 'REPIPHONE12PRODIAGNOSTICO')) $img='DIAGNOSTICO.jpg';
		if(substr_count($ref, 'REPIPHONE12PRODIAG')) $img='DIAGNOSTICO.jpg';
		if(substr_count($ref, 'REPIPHONE12PROLENTECAMARA')) $img='LENTECAMARA.jpg';
		if(substr_count($ref, 'REPIPHONE12PROLENTECAM')) $img='LENTECAMARA.jpg';
		if(substr_count($ref, 'REPIPHONE11LEITORSIM')) $img='LEITORSIM.jpg';
		if(substr_count($ref, 'REPIPHONE11SIM')) $img='LEITORSIM.jpg';
		if(substr_count($ref, 'REPIPAD7LCD')) $img='LCD (1).jpg';
		if(substr_count($ref, 'REPIPAD7TOUCH')) $img='TOUCH.jpg';
		if(substr_count($ref, 'REPIPHONE6WIFI')) $img='WIFI.jpg';
		if(substr_count($ref, 'REPIPHONE6ICAUDIO')) $img='IC_BOARD.jpg';
		if(substr_count($ref, 'REPIPHONE6ICTOUCH')) $img='IC_BOARD.jpg';
		if(substr_count($ref, 'REPIPHONE6ICBACKLIGHT')) $img='IC_BOARD.jpg';
		if(substr_count($ref, 'REPIPHONE6ICCARGA')) $img='IC_BOARD.jpg';
		if(substr_count($ref, 'REPIP11MODULOOR')) $img='MODULO.jpg';
		if(substr_count($ref, 'REPIPHONE11MODULOLCD')) $img='LCD.jpg';
	}
	return $img;
}


function get_imagem_produto_total($ref, $prod_id)
{
	$base_path = $GLOBALS['site_shell_url'];
	$base_url = $GLOBALS['site_http_url'];
	$no_photo_path = 'layout/'.PROJETO.'/images/';

	//NOTA: Pf não copiem codigo direto do chat GPT, neste caso, eu necessito que seja com if sequencial, e não array:

	if(substr($ref, 0,3)=='REP')
	{
		//if(substr_count($ref, 'DIAGNOSTICO')) $img='diagno.jpg';
		if(substr_count($ref, 'FRP')) $img='GOOGLE.jpg';
		if(substr_count($ref, 'SIM')) $img='GAVETASIM.jpg';
		if(substr_count($ref, 'TOUCH')) $img='TOUCH.jpg';
		if(substr_count($ref, 'LCD')) $img='LCD.jpg';
		if(substr_count($ref, 'WIFI')) $img='WIFI.jpg';
		if(substr_count($ref, 'REDE')) $img='REDE.jpg';
		if(substr_count($ref, 'TDN')) $img='TDN.jpg';
		if(substr_count($ref, 'TDDI')) $img='TDDI.jpg';
		if(substr_count($ref, 'CAMARA')) $img='CAMARA.jpg';
		if(substr_count($ref, 'UNLOCK')) $img='UNLOCK.jpg';
		if(substr_count($ref, 'CODIGO')) $img='CODIGO.jpg';
		if(substr_count($ref, 'SWAP')) $img='SWAP.jpg';
		if(substr_count($ref, 'HOME')) $img='HOME.jpg';
		if(substr_count($ref, 'MODULO')) $img='MODULO.jpg';
		if(substr_count($ref, 'MODULOFRAME')) $img='MODULO_aro.jpg';
		if(substr_count($ref, 'CCARGA')) $img='CCARGA.jpg';
		if(substr_count($ref, 'LEITORSIM')) $img='LEITORSIM.jpg';
		if(substr_count($ref, 'AUSCULTADOR')) $img='AUSCULTADOR.jpg';
		if(substr_count($ref, 'MICROFONE')) $img='MICROFONE.jpg';
		if(substr_count($ref, 'ALTIFALANTE')) $img='ALTIFALANTE.jpg';
		if(substr_count($ref, 'BATERIA')) $img='BATERIA.jpg';
		if(substr_count($ref, 'CARCACA')) $img='CARCACA.jpg';
		if(substr_count($ref, 'DIAGNOSTICO')) $img='DIAGNOSTICO.jpg';
		if(substr_count($ref, 'ONOFF')) $img='ONOFF.jpg';
		if(substr_count($ref, 'SILENCIO')) $img='SILENCIO.jpg';
		if(substr_count($ref, 'VOLUME')) $img='VOLUME.jpg';
		if(substr_count($ref, 'VIBRADOR')) $img='VIBRADOR.jpg';
		if(substr_count($ref, 'SENSOR')) $img='SENSOR.jpg';
		if(substr_count($ref, 'ICCARGA')) $img='IC_BOARD.jpg';
		if(substr_count($ref, 'AUDIOJACK')) $img='AUDIOJACK.jpg';
		if(substr_count($ref, 'VIDROTRAS')) $img='TRASEIRA.jpg';
		if(substr_count($ref, 'DILCD')) $img='DILCD.jpg';
		if(substr_count($ref, 'NLCD')) $img='NLCD.jpg';
		if(substr_count($ref, 'CAM3X')) $img='CAM3X.jpg';
		if(substr_count($ref, 'CAM2V')) $img='CAM2V.jpg';
		if(substr_count($ref, 'CAM2D')) $img='CAM2D.jpg';
		if(substr_count($ref, 'VTR3X')) $img='VTR3X.jpg';
		if(substr_count($ref, 'VTR2V')) $img='VTR2V.jpg';
		if(substr_count($ref, 'VTR2D')) $img='VTR2D.jpg';
		if(substr_count($ref, 'LCAM3X')) $img='LCAM3X.jpg';
		if(substr_count($ref, 'LCAM2V')) $img='LCAM2V.jpg';
		if(substr_count($ref, 'LCAM2D')) $img='LCAM2D.jpg';
		if(substr_count($ref, 'BMF')) $img='BMF.jpg';
		if(substr_count($ref, 'CFN')) $img='CFN.jpg';
		if(substr_count($ref, 'CFDI')) $img='CFDI.jpg';
		if(substr_count($ref, 'CARC2V')) $img='CARC2V.jpg';
		if(substr_count($ref, 'CARC2D')) $img='CARC2D.jpg';
		if(substr_count($ref, 'CARC3X')) $img='CARC3X.jpg';
		if(substr_count($ref, 'OLEDDILCD')) $img='DILCD.jpg';
		if(substr_count($ref, 'OLEDNLCD')) $img='NLCD.jpg';
		if(substr_count($ref, 'MBLCD')) $img='LCD_MAC.jpg';
		if(substr_count($ref, 'IMLCD')) $img='LCD_IMAC.jpg';
		if(substr_count($ref, 'MBBATERIA')) $img='BATERIA_MAC.jpg';
		if(substr_count($ref, 'IMBATERIA')) $img='BATERIA_IMAC.jpg';
		
		if(substr_count($ref, 'LENTE1CC')) $img='LENTE1CC.jpg';
		if(substr_count($ref, 'LENTE1CE')) $img='LENTE1CE.jpg';
		if(substr_count($ref, 'LENTE1CEV')) $img='LENTE1CEV.jpg';
		if(substr_count($ref, 'LENTE2CC')) $img='LENTE2CC.jpg';
		if(substr_count($ref, 'LENTE2CEV')) $img='LENTE2CEV.jpg';
		if(substr_count($ref, 'LENTE2CEH')) $img='LENTE2CEH.jpg';
		if(substr_count($ref, 'LENTEi2C')) $img='LENTEi2C.jpg';
		if(substr_count($ref, 'LENTE3C')) $img='LENTE3C.jpg';
		if(substr_count($ref, 'LENTEi3C')) $img='LENTEi3C.jpg';
		if(substr_count($ref, 'LENTE4C')) $img='LENTE4C.jpg';
		if(substr_count($ref, 'CAMARA1CC')) $img='CAM1CC.jpg';
		if(substr_count($ref, 'CAMARA1CE')) $img='CAM1CE.jpg';
		if(substr_count($ref, 'CAMARA1CEV')) $img='CAM1CEV.jpg';
		if(substr_count($ref, 'CAMARA2CC')) $img='CAM2CC.jpg';
		if(substr_count($ref, 'CAMARA2CEV')) $img='CAM2CEV.jpg';
		if(substr_count($ref, 'CAMARA2CEH')) $img='CAM2CEH.jpg';
		if(substr_count($ref, 'CAMARAi2C')) $img='CAMi2C.jpg';
		if(substr_count($ref, 'CAMARA3C')) $img='CAM3C.jpg';
		if(substr_count($ref, 'CAMARAi3C')) $img='CAMi3C.jpg';
		if(substr_count($ref, 'CAMARA4C')) $img='CAM4C.jpg';
		if(substr_count($ref, 'TTRASEIRA2CC')) $img='TTRAS2CC.jpg';
		if(substr_count($ref, 'TTRASEIRA3CC')) $img='TTRAS3CC.jpg';
		if(substr_count($ref, 'VIDROTRAS2CC')) $img='VIDROTRAS2CC.jpg';
		if(substr_count($ref, 'VIDROTRAS2EH')) $img='VIDROTRAS2EH.jpg';
		if(substr_count($ref, 'VIDROTRAS2EV')) $img='VIDROTRAS2EV.jpg';
		if(substr_count($ref, 'VIDROTRASi2C')) $img='VIDROTRASi2C.jpg';
		if(substr_count($ref, 'VIDROTRAS3CC')) $img='VIDROTRAS3CC.jpg';
		if(substr_count($ref, 'VIDROTRASi3C')) $img='VIDROTRASi3C.jpg';
		if(substr_count($ref, 'REBALLING')) $img='REBALLING.jpg';
		
		if(substr_count($ref, 'LENTECAMARA')) $img='LENTE.jpg';
		if(substr_count($ref, 'TAMPATRASEIRA')) $img='TRASEIRA.jpg';
		if(substr_count($ref, 'CAMARAFRONTAL')) $img='CAMARAFRONTAL.jpg';
		if(substr_count($ref, 'VIDROTRASEIRO')) $img='TRASEIRA.jpg';
		if(substr_count($ref, 'MODULOORIG')) $img='MODULO.jpg';
		if(substr_count($ref, 'MODULOCOMP')) $img='OLED.jpg';
		
		if(substr_count($ref, 'TDDIOFI')) $img='TDDIOFI.jpg';
		if(substr_count($ref, 'TDNOFI')) $img='TDNOFI.jpg';
		if(substr_count($ref, 'DILCDOFI')) $img='DILCDOFI.jpg';
		if(substr_count($ref, 'NLCDOFI')) $img='NLCDOFI.jpg';
		if(substr_count($ref, 'BATOFI')) $img='BATOFI.jpg';
		if(substr_count($ref, 'CAM3XOFI')) $img='CAM3XOFI.jpg';
		if(substr_count($ref, 'CAM2VOFI')) $img='CAM2VOFI.jpg';
		if(substr_count($ref, 'CAM2DOFI')) $img='CAM2DOFI.jpg';
		if(substr_count($ref, 'TDDIOFI')) $img='TDDIOFI.jpg';

		if(substr_count($ref, 'REPIPHONE7AUSCULTADOR')) $img='AUSCULTADOR.jpg';
		if(substr_count($ref, 'REPIPHONE7AUSC')) $img='AUSCULTADOR.jpg';
		if(substr_count($ref, 'REPIPHONE7SPEAKER')) $img='AUSCULTADOR.jpg';
		if(substr_count($ref, 'REPIPHONE7MICROFONE')) $img='MICROFONE.jpg';
		if(substr_count($ref, 'REPIPHONE7MIC')) $img='MICROFONE.jpg';
		if(substr_count($ref, 'REPIPHONE7ALTIFALANTE')) $img='ALTIFALANTE.jpg';
		if(substr_count($ref, 'REPIPHONE7ALTI')) $img='ALTIFALANTE.jpg';
		if(substr_count($ref, 'REPIPHONE7CAMARA')) $img='CAMARA.jpg';
		if(substr_count($ref, 'REPIPHONE7CAMARAFRONTAL')) $img='CAMARAFRONTAL.jpg';
		if(substr_count($ref, 'REPIPHONE7CAMARAFRONTAL')) $img='CAMARAFRONTAL.jpg';
		if(substr_count($ref, 'REPIPHONE7VIBRADOR')) $img='VIBRADOR.jpg';
		if(substr_count($ref, 'REPIPHONE7VIBRA')) $img='VIBRADOR.jpg';
		if(substr_count($ref, 'REPIPHONE7ONOFF')) $img='ONOFF.jpg';
		if(substr_count($ref, 'REPIPHONE7SILENCIO')) $img='SILENCIO.jpg';
		if(substr_count($ref, 'REPIPHONE7MUTE')) $img='SILENCIO.jpg';
		if(substr_count($ref, 'REPIPHONE7VOLUME')) $img='VOLUME.jpg';
		if(substr_count($ref, 'REPIPHONE7HOME')) $img='HOME.jpg';
		if(substr_count($ref, 'REPIPHONE7SENSOR')) $img='SENSOR.jpg';
		if(substr_count($ref, 'REPIPHONE7AUDIOJACK')) $img='AUDIOJACK.jpg';
		if(substr_count($ref, 'REPIPHONE7CARGA')) $img='CCARGA.jpg';
		if(substr_count($ref, 'REPIPHONE7BATERIA')) $img='BATERIA.jpg';
		if(substr_count($ref, 'REPIPHONE7CARCACA')) $img='CARCACA.jpg';
		if(substr_count($ref, 'REPIPHONE7TRAS')) $img='VIDROTRASEIRO.jpg';
		if(substr_count($ref, 'REPSGA705TRASEIRA')) $img='TAMPA_TRASEIRA.jpg';
		if(substr_count($ref, 'REPIPHONE12PRODIAGNOSTICO')) $img='DIAGNOSTICO.jpg';
		if(substr_count($ref, 'REPIPHONE12PRODIAG')) $img='DIAGNOSTICO.jpg';
		if(substr_count($ref, 'REPIPHONE12PROLENTECAMARA')) $img='LENTECAMARA.jpg';
		if(substr_count($ref, 'REPIPHONE12PROLENTECAM')) $img='LENTECAMARA.jpg';
		if(substr_count($ref, 'REPIPHONE11LEITORSIM')) $img='LEITORSIM.jpg';
		if(substr_count($ref, 'REPIPHONE11SIM')) $img='LEITORSIM.jpg';
		if(substr_count($ref, 'REPIPAD7LCD')) $img='LCD (1).jpg';
		if(substr_count($ref, 'REPIPAD7TOUCH')) $img='TOUCH.jpg';
		if(substr_count($ref, 'REPIPHONE6WIFI')) $img='WIFI.jpg';
		if(substr_count($ref, 'REPIPHONE6ICAUDIO')) $img='IC_BOARD.jpg';
		if(substr_count($ref, 'REPIPHONE6ICTOUCH')) $img='IC_BOARD.jpg';
		if(substr_count($ref, 'REPIPHONE6ICBACKLIGHT')) $img='IC_BOARD.jpg';
		if(substr_count($ref, 'REPIPHONE6ICCARGA')) $img='IC_BOARD.jpg';
		if(substr_count($ref, 'REPIP11MODULOOR')) $img='MODULO.jpg';
		if(substr_count($ref, 'REPIPHONE11MODULOLCD')) $img='LCD.jpg';

		$img_file=$GLOBALS['site_http_url'].'layout/imgs/'.$img;

		if(!dotbright_image_exists($GLOBALS['site_shell_url'].'layout/imgs/'.$img) )$img_file=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo.png';

	}

	if($img_file!='')$result['main'] = $img_file;




















	//---------------------------------- o codigo seguinte está errado:

	/*
	$map = [
		'FRP' => 'GOOGLE.jpg',
        'SIM' => 'GAVETASIM.jpg',
        'TOUCH' => 'TOUCH.jpg',
        'LCD' => 'LCD.jpg',
        'WIFI' => 'WIFI.jpg',
        'REDE' => 'REDE.jpg',
        'TDN' => 'TDN.jpg',
        'TDDI' => 'TDDI.jpg',
        'CAMARA' => 'CAMARA.jpg',
        'UNLOCK' => 'UNLOCK.jpg',
        'CODIGO' => 'CODIGO.jpg',
        'SWAP' => 'SWAP.jpg',
        'HOME' => 'HOME.jpg',
        'MODULO' => 'MODULO.jpg',
        'MODULOFRAME' => 'MODULO_aro.jpg',
        'CCARGA' => 'CCARGA.jpg',
        'LEITORSIM' => 'LEITORSIM.jpg',
        'AUSCULTADOR' => 'AUSCULTADOR.jpg',
        'MICROFONE' => 'MICROFONE.jpg',
        'ALTIFALANTE' => 'ALTIFALANTE.jpg',
        'BATERIA' => 'BATERIA.jpg',
        'CARCACA' => 'CARCACA.jpg',
        'DIAGNOSTICO' => 'DIAGNOSTICO.jpg',
        'ONOFF' => 'ONOFF.jpg',
        'SILENCIO' => 'SILENCIO.jpg',
        'VOLUME' => 'VOLUME.jpg',
        'VIBRADOR' => 'VIBRADOR.jpg',
        'SENSOR' => 'SENSOR.jpg',
        'ICCARGA' => 'IC_BOARD.jpg',
        'AUDIOJACK' => 'AUDIOJACK.jpg',
        'VIDROTRAS' => 'TRASEIRA.jpg',
        'DILCD' => 'DILCD.jpg',
        'NLCD' => 'NLCD.jpg',
        'CAM3X' => 'CAM3X.jpg',
        'CAM2V' => 'CAM2V.jpg',
        'CAM2D' => 'CAM2D.jpg',
        'VTR3X' => 'VTR3X.jpg',
        'VTR2V' => 'VTR2V.jpg',
        'VTR2D' => 'VTR2D.jpg',
        'LCAM3X' => 'LCAM3X.jpg',
        'LCAM2V' => 'LCAM2V.jpg',
        'LCAM2D' => 'LCAM2D.jpg',
        'BMF' => 'BMF.jpg',
        'CFN' => 'CFN.jpg',
        'CFDI' => 'CFDI.jpg',
        'CARC2V' => 'CARC2V.jpg',
        'CARC2D' => 'CARC2D.jpg',
        'CARC3X' => 'CARC3X.jpg',
        'OLEDDILCD' => 'DILCD.jpg',
        'OLEDNLCD' => 'NLCD.jpg',
        'MBLCD' => 'LCD_MAC.jpg',
        'IMLCD' => 'LCD_IMAC.jpg',
        'MBBATERIA' => 'BATERIA_MAC.jpg',
        'IMBATERIA' => 'BATERIA_IMAC.jpg',

        'LENTE1CC' => 'LENTE1CC.jpg',
        'LENTE1CE' => 'LENTE1CE.jpg',
        'LENTE1CEV' => 'LENTE1CEV.jpg',
        'LENTE2CC' => 'LENTE2CC.jpg',
        'LENTE2CEV' => 'LENTE2CEV.jpg',
        'LENTE2CEH' => 'LENTE2CEH.jpg',
        'LENTEi2C' => 'LENTEi2C.jpg',
        'LENTE3C' => 'LENTE3C.jpg',
        'LENTEi3C' => 'LENTEi3C.jpg',
        'LENTE4C' => 'LENTE4C.jpg',
        'CAMARA1CC' => 'CAM1CC.jpg',
        'CAMARA1CE' => 'CAM1CE.jpg',
        'CAMARA1CEV' => 'CAM1CEV.jpg',
        'CAMARA2CC' => 'CAM2CC.jpg',
        'CAMARA2CEV' => 'CAM2CEV.jpg',
        'CAMARA2CEH' => 'CAM2CEH.jpg',
        'CAMARAi2C' => 'CAMi2C.jpg',
        'CAMARA3C' => 'CAM3C.jpg',
        'CAMARAi3C' => 'CAMi3C.jpg',
        'CAMARA4C' => 'CAM4C.jpg',
        'TTRASEIRA2CC' => 'TTRAS2CC.jpg',
        'TTRASEIRA3CC' => 'TTRAS3CC.jpg',
        'VIDROTRAS2CC' => 'VIDROTRAS2CC.jpg',
        'VIDROTRAS2EH' => 'VIDROTRAS2EH.jpg',
        'VIDROTRAS2EV' => 'VIDROTRAS2EV.jpg',
        'VIDROTRASi2C' => 'VIDROTRASi2C.jpg',
        'VIDROTRAS3CC' => 'VIDROTRAS3CC.jpg',
        'VIDROTRASi3C' => 'VIDROTRASi3C.jpg',
        'REBALLING' => 'REBALLING.jpg',

        'LENTECAMARA' => 'LENTE.jpg',
        'TAMPATRASEIRA' => 'TRASEIRA.jpg',
        'CAMARAFRONTAL' => 'CAMARAFRONTAL.jpg',
        'VIDROTRASEIRO' => 'TRASEIRA.jpg',
        'MODULOORIG' => 'MODULO.jpg',
        'MODULOCOMP' => 'OLED.jpg',

        'TDDIOFI' => 'TDDIOFI.jpg',
        'TDNOFI' => 'TDNOFI.jpg',
        'DILCDOFI' => 'DILCDOFI.jpg',
        'NLCDOFI' => 'NLCDOFI.jpg',
        'BATOFI' => 'BATOFI.jpg',
        'CAM3XOFI' => 'CAM3XOFI.jpg',
        'CAM2VOFI' => 'CAM2VOFI.jpg',
        'CAM2DOFI' => 'CAM2DOFI.jpg',
        'TDDIOFI' => 'TDDIOFI.jpg',

        'REPIPHONE7AUSCULTADOR' => 'AUSCULTADOR.jpg',
        'REPIPHONE7AUSC' => 'AUSCULTADOR.jpg',
        'REPIPHONE7SPEAKER' => 'AUSCULTADOR.jpg',
        'REPIPHONE7MICROFONE' => 'MICROFONE.jpg',
        'REPIPHONE7MIC' => 'MICROFONE.jpg',
        'REPIPHONE7ALTIFALANTE' => 'ALTIFALANTE.jpg',
        'REPIPHONE7ALTI' => 'ALTIFALANTE.jpg',
        'REPIPHONE7CAMARA' => 'CAMARA.jpg',
        'REPIPHONE7CAMARAFRONTAL' => 'CAMARAFRONTAL.jpg',
        'REPIPHONE7CAMARAFRONTAL' => 'CAMARAFRONTAL.jpg',
        'REPIPHONE7VIBRADOR' => 'VIBRADOR.jpg',
        'REPIPHONE7VIBRA' => 'VIBRADOR.jpg',
        'REPIPHONE7ONOFF' => 'ONOFF.jpg',
        'REPIPHONE7SILENCIO' => 'SILENCIO.jpg',
        'REPIPHONE7MUTE' => 'SILENCIO.jpg',
        'REPIPHONE7VOLUME' => 'VOLUME.jpg',
        'REPIPHONE7HOME' => 'HOME.jpg',
        'REPIPHONE7SENSOR' => 'SENSOR.jpg',
        'REPIPHONE7AUDIOJACK' => 'AUDIOJACK.jpg',
        'REPIPHONE7CARGA' => 'CCARGA.jpg',
        'REPIPHONE7BATERIA' => 'BATERIA.jpg',
        'REPIPHONE7CARCACA' => 'CARCACA.jpg',
        'REPIPHONE7TRAS' => 'VIDROTRASEIRO.jpg',
        'REPSGA705TRASEIRA' => 'TAMPA_TRASEIRA.jpg',
        'REPIPHONE12PRODIAGNOSTICO' => 'DIAGNOSTICO.jpg',
        'REPIPHONE12PRODIAG' => 'DIAGNOSTICO.jpg',
        'REPIPHONE12PROLENTECAMARA' => 'LENTECAMARA.jpg',
        'REPIPHONE12PROLENTECAM' => 'LENTECAMARA.jpg',
        'REPIPHONE11LEITORSIM' => 'LEITORSIM.jpg',
        'REPIPHONE11SIM' => 'LEITORSIM.jpg',
        'REPIPAD7LCD' => 'LCD (1).jpg',
        'REPIPAD7TOUCH' => 'TOUCH.jpg',
        'REPIPHONE6WIFI' => 'WIFI.jpg',
        'REPIPHONE6ICAUDIO' => 'IC_BOARD.jpg',
        'REPIPHONE6ICTOUCH' => 'IC_BOARD.jpg',
        'REPIPHONE6ICBACKLIGHT' => 'IC_BOARD.jpg',
        'REPIPHONE6ICCARGA' => 'IC_BOARD.jpg',
        'REPIP11MODULOOR' => 'MODULO.jpg',
        'REPIPHONE11MODULOLCD' => 'LCD.jpg'
	];

	$result = [
		'main' => $base_url.$no_photo_path.'no_photo.png',
		'cart' => $base_url.$no_photo_path.'no_photo_cart.png',
		'med'  => $base_url.$no_photo_path.'no_photo2.png',
		'new'  => $base_url.$no_photo_path.'no_photo.png',
		'news' => $base_url.$no_photo_path.'no_photo_cart.png',
		'fb'   => $base_url.$no_photo_path.'no_photo_cart.png',
	];

	// 1. Se for reparação, tenta identificar imagem
	if (substr($ref, 0, 3) === 'REP') {
		foreach ($map as $key => $img_file) {
			if (stripos($ref, $key) !== false) {
				$full_path = $base_path.'layout/imgs/'.$img_file;
				if (is_file($full_path)) {
					$url = 'layout/imgs/'.$img_file;
					$result['main'] = $url;
					$result['cart'] = $url;
					$result['med']  = $url;
					$result['new']  = $url;
					$result['news'] = $url;
					return $result;
				}
				break;
			}
		}
	}
	*/


	//Caso tenha imagens na ficha de produto, vamos usar antes essas:

	// 2. Tenta imagens do produto
	$arr_imagens = get_product_images($prod_id);
	if(count($arr_imagens) > 0)
	{
		$img = $arr_imagens[0];
		$ordem = $img['Ordem'];
		$ext = $img['img_ext'];

		$prefix = 'files/products/'.$prod_id.'_'.$ordem;

		for ($i=0; $i < count($arr_imagens); $i++)
		{
			$result['main'] = $base_url.$prefix.'.'.$ext;
			$result['cart'] = $base_url.$prefix.'_small.png';
			$result['med']  = $base_url.$prefix.'_prod.png';
			$result['new']  = $base_url.$prefix.'_thum.png';
			$result['news'] = $base_url.$prefix.'_thum.png';
			$result['fb']   = $base_url.$prefix.'_prod.png';
		}
	}

	if (dotbright_shared_images()) {
		$result['med']=$result['main'];
		$result['fb']=$result['main'];
		return array_map('dotbright_image_url', $result);
	}
	return $result;
}

function get_produtc_info($prod_id=0,$prod_obj_id='',$gera_images=1,$id_fam=0,$lingua='',$gerar=0,$obj='')
{
	if($lingua == '')
	{
		if(isset($_SERVER['REQUEST_URI']) && substr_count($_SERVER['REQUEST_URI'],'/admin/')) $lingua=$GLOBALS['admin_lang'];
		else $lingua=$GLOBALS['site_lang'];
	}
	$prods=NULL;

	if(isset($prod_id) && $prod_id > 0 || isset($id_fam) && $id_fam > 0)
	{
		//Se ja carreguei os dados deste artigo ao carregar a pagina, vou ler da memoria:
		global $produtos_pagina;
		if(!is_array($produtos_pagina))$produtos_pagina=array();
		if($produtos_pagina[$prod_id]==1 && dotbright_image_resolve($GLOBALS['site_shell_url'].'files/cache/products/p'.dotbright_product_images_cache_key().$prod_id.'_'.$lingua.'.bin') )
		{
			$prods = unserialize(file_get_contents($GLOBALS['site_shell_url'].'files/cache/products/p'.dotbright_product_images_cache_key().$prod_id.'_'.$lingua.'.bin'));
		}
		else
		{
			if(isset($GLOBALS['site_prev']) && $GLOBALS['site_prev']==1 && $GLOBALS['admin_id']>0)
			{
				if($id_fam > 0) $and .= ' AND PFO.ID_ORG='.$id_fam;
				else $and=' P.ID='.$prod_id;
			}
			else
			{
				$and =' P.Aproved=1 AND PF.visivel=1 ';
				if($id_fam > 0) $and .= ' AND PFO.ID_ORG='.$id_fam;
				else $and .= ' AND P.ID='.$prod_id;
			}

			$and .= ' AND obj_type="products"';
			$and .= ' AND (obj_val2=0 OR obj_val2 IS NULL)';


			$sql='SELECT P.ID,P.Variante,P.Ordem,P.fornecedor_importacao,P.EtiquetaEnergiaNova,P.Ref as Ref_Original,P.Ref_secundaria as Ref,P.Ref_Ean, P.Peso,P.peso_cliente,P.Carrinho,P.ID_Marca,P.Preco1,P.personalizacao,P.encomenda_sem_stock,
						P.Video,P.corEE,P.ee_ext,P.ue_ext,P.dt_ext,P.u_catetiq,P.u_coretiq,P.u_imagetiq,P.estados_produto,P.produto_standard,P.c_submissao_ficheiro,
						P.BOX1,P.BOX2,P.CABLE,P.CHARGER,P.HEADSET,P.COVER,
						P.destaque_ext,P.destaque2_ext,P.destaque3_ext,P.destaque4_ext,P.margem_fam,P.bateria,P.estado,P.localizacao_fisica,
						PL.Cabecalho,PL.Texto1,PL.Texto2,PL.Texto3,PL.Texto4,PL.Cabecalho_Texto5,PL.Texto5,PL.Cor,PL.Tamanho,PL.Subtitulo,PL.tempo_entrega,PFO.ID_ORG,P.store_icons,

						PO.obj_destaque,PO.obj_outlet,PO.obj_iva,PO.obj_iva_txt,PO.obj_stock,PO.obj_val2,
						PO.obj_pvp,PO.obj_promo,PO.obj_promo_ativo,PO.obj_promo_data,PO.obj_Supplier_ID,
						PO.obj_destaque_data,PO.obj_travelsize,PO.obj_novo,PO.obj_novo_data,PO.obj_outlet_data,PO.obj_dia,PO.obj_dia_data

					FROM
						products AS P
						INNER JOIN products_lang AS PL ON PL.ID_PAI=P.ID
						INNER JOIN products_families_obj as PFO ON P.ID=PFO.ID_PROD
						INNER JOIN products_families as PF ON PF.ID=PFO.ID_ORG
						INNER JOIN products_objs as PO ON PO.obj_type_id=P.ID
					WHERE
						'.$and.' AND PL.Lang="'.$lingua.'"';

			if($id_fam > 0) $sql .= ' ORDER by RAND() Asc LIMIT 1';
			else $sql .= ' ORDER by P.Ordem Asc';

			// echo '<pre>';
			//if($prod_id=='37382') 
			// echo $sql;
			// echo '</pre>';
			$rs2=new DataObj;
			$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
			
			if($obj != '')$rs=$obj;
			else 
			{
				$rs=new DataObj;
				$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
			}			
			if(!$rs->EOF())
			{
				$P_CONF=unserialize(PROD_CONF);
				if(defined('CARRINHO_CONF')) $carrinho_conf=unserialize(CARRINHO_CONF); else $carrinho_conf=array();
				$prod_id=$rs->db_data['ID'];
				$prod_ordem=$rs->db_data['Ordem'];
				$fam_id=$rs->db_data['ID_ORG'];
				$obj_iva_txt=$rs->db_data['obj_iva_txt'];
				$obj_iva=$rs->db_data['obj_iva'];
				$peso=$rs->db_data['Peso'];
				$peso_cliente=$rs->db_data['peso_cliente'];
				$Ref_Original=$rs->db_data['Ref_Original'];
				$ref=($rs->db_data['Ref']=='' ? $rs->db_data['Ref_Original'] : $rs->db_data['Ref']);
				$Ref_Ean=$rs->db_data['Ref_Ean'];
				$produto_standard=$rs->db_data['produto_standard'];
				$c_submissao_ficheiro=$rs->db_data['c_submissao_ficheiro'];
				$marca=$rs->db_data['ID_Marca'];
				$carrinho=$rs->db_data['Carrinho'];
				$estados_produto=$rs->db_data['estados_produto'];
				$obj_stock=$rs->db_data['obj_stock'];
				$encomenda_sem_stock=(isset($carrinho_conf['tem_enc_sem_stock']) && $carrinho_conf['tem_enc_sem_stock'] == 1 ? 1 : $rs->db_data['encomenda_sem_stock']);
				$personalizacao=explode(';',$rs->db_data['personalizacao']);


				$BOX1=$rs->db_data['BOX1'];
				$BOX2=$rs->db_data['BOX2'];
				$CABLE=$rs->db_data['CABLE'];
				$CHARGER=$rs->db_data['CHARGER'];
				$HEADSET=$rs->db_data['HEADSET'];
				$COVER=$rs->db_data['COVER'];
				
				$bateria=$rs->db_data['bateria'];
				$estado=$rs->db_data['estado'];
				
				//------------------
				$seminovo_reference_image=0;
				
				if(substr($ref, 0,3)=='REP')
				{
					$img=get_reparacoes_img($ref);
					//echo $GLOBALS['site_shell_url'].'layout/imgs/'.$img;
					/*
					if(!is_file($GLOBALS['site_shell_url'].'layout/imgs/'.$img) )
					{
						$img=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo.png';
						$arr_img[0]=$img;
						$arr_img_cart[0]=$img;
						$arr_img_med[0]=$img;
						$arr_img_new[0]=$img;
						$arr_img_news[0]=$img;
					}
					else
					{
						$arr_img[0]='layout/imgs/'.$img;
						$arr_img_cart[0]='layout/imgs/'.$img;
						$arr_img_med[0]='layout/imgs/'.$img;
						$arr_img_new[0]='layout/imgs/'.$img;
						$arr_img_news[0]='layout/imgs/'.$img;
					}
					*/

					if(dotbright_image_exists($GLOBALS['site_shell_url'].'layout/imgs/'.$img) ){
						$arr_img[0]='layout/imgs/'.$img;
						$arr_img_cart[0]='layout/imgs/'.$img;
						$arr_img_med[0]='layout/imgs/'.$img;
						$arr_img_new[0]='layout/imgs/'.$img;
						$arr_img_news[0]='layout/imgs/'.$img;
					}
					else{
						$arr_imagens=get_product_images($prod_id);				
						if(count($arr_imagens) > 0)
						{
							$webp=0;
							if( strpos( $_SERVER['HTTP_ACCEPT'],'image/webp' ) !== false )$webp=1;// webp is supported!
							$webp=0; //Ainda esta em fase de testes

							$i_small=unserialize(IMG_SMALL);
							$i_thum=unserialize(IMG_THUM);
							for ($i=0; $i < count($arr_imagens); $i++)
							{
								if(!empty($arr_imagens[$i]['is_reference']))$seminovo_reference_image=1;
								$ext=$arr_imagens[$i]['img_ext'];
								$ordem=$arr_imagens[$i]['Ordem'];
								$img_file='files/products/'.$prod_id.'_'.$ordem.'.'.$ext;

								if(!dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file) && $ext!='')
								{
									if($ext=='png')$ext='jpg';
									$img_file='files/products/'.$prod_id.'_'.$ordem.'.'.$ext;
								}

								$img_file_small='files/products/'.$prod_id.'_'.$ordem.'_small.png';
								$img_file_thum='files/products/'.$prod_id.'_'.$ordem.'_thum.png';
								$img_file_prod=$img_file; // Product detail uses the original image.

								// VAMOS PRIMEIRO GERADAR
								if($i_small['ativo'] == 1)	geraimagens($img_file,$img_file_small,$i_small['w'],$i_small['h'],$i_small['crop'],$i_small['m_trans'],$i_small['c_white']);
								if($i_thum['ativo'] == 1) 	geraimagens($img_file,$img_file_thum, $i_thum['w'],$i_thum['h'],$i_thum['crop'],$i_thum['m_trans'],$i_thum['c_white']);

								$img_file_small_cache='';
								$img_file_thum_cache='';
								$img_file_prod_cache='';

								// VAMOS PASSAR A USAR APENAS 3 TAMANHOS,PEQUENA,MEDIA,E FICHA DE PRODUTO
								//Se existir a original
								if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file) && $ext!='')
								{
									if(!dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_small))geraimagens($img_file,$img_file_small,$i_small['w'],$i_small['h'],$i_small['crop'],$i_small['m_trans'],$i_small['c_white']);
									if(!dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_thum))geraimagens($img_file,$img_file_thum, $i_thum['w'],$i_thum['h'],$i_thum['crop'],$i_thum['m_trans'],$i_thum['c_white']);
								}
								if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_small) && $ext!='') $img_file_small_cache='files/products/'.$prod_id.'_'.$ordem.'_small.png?dp='.dotbright_image_mtime($GLOBALS['site_shell_url'].$img_file_small);
								if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_thum) && $ext!='') $img_file_thum_cache='files/products/'.$prod_id.'_'.$ordem.'_thum.png?dp='.dotbright_image_mtime($GLOBALS['site_shell_url'].$img_file_thum);
								if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_prod ) && $ext!='') $img_file_prod_cache=$img_file.'?dp='.dotbright_image_mtime($GLOBALS['site_shell_url'].$img_file);



								if($webp==1)
								{
									$img_webp_small='files/products/'.$prod_id.'_'.$ordem.'_small.webp';
									$img_webp_thum='files/products/'.$prod_id.'_'.$ordem.'_thum.webp';
									if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_small))
									{
										if(!dotbright_image_resolve($GLOBALS['site_shell_url'].$img_webp_small))convert_webp($img_file_small);
										$img_file_small='files/products/'.$prod_id.'_'.$ordem.'_small.webp';
										$img_file_small_cache='files/products/'.$prod_id.'_'.$ordem.'_small.webp?dp='.dotbright_image_mtime($GLOBALS['site_shell_url'].$img_webp_small);
									}
									if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_thum))
									{
										if(!dotbright_image_resolve($GLOBALS['site_shell_url'].$img_webp_thum))convert_webp($img_file_thum);
										$img_file_thum='files/products/'.$prod_id.'_'.$ordem.'_thum.webp';
										$img_file_thum_cache='files/products/'.$prod_id.'_'.$ordem.'_thum.webp?dp='.dotbright_image_mtime($GLOBALS['site_shell_url'].$img_webp_thum);
									}

								}

								if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_small) && $ext!='') $img_file_small=$img_file_small_cache; else $img_file_small=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo_cart.png';
								if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_thum) && $ext!='') $img_file_thum=$img_file_thum_cache; else $img_file_thum=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo.png';
								if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_prod) && $ext!='') $img_file_prod=$img_file_prod_cache; else $img_file_prod=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo2.png';

								if($ext)
								{
									$arr_img[]=$GLOBALS['site_http_url'].$img_file;
									$arr_img_cart[]=$img_file_small;
									$arr_img_med[]=$img_file_prod;
									$arr_img_new[]=$img_file_thum;
									$arr_img_news[]=$img_file_thum;
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
								}
							}
						}
						else
						{
							$arr_img[0]=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo.png';
							$arr_img_cart[0]=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo_cart.png';
							$arr_img_med[0]=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo2.png';
							$arr_img_new[0]=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo.png';
							$arr_img_news[0]=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo_cart.png';
							if($i_fb['ativo'] == 1) $arr_img_fb[0]=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo_cart.png';
						}
					}
				}
				else
				{
					$arr_imagens=get_product_images($prod_id);				
					if(count($arr_imagens) > 0)
					{
						$webp=0;
						if( strpos( $_SERVER['HTTP_ACCEPT'],'image/webp' ) !== false )$webp=1;// webp is supported!
						$webp=0; //Ainda esta em fase de testes

						$i_small=unserialize(IMG_SMALL);
						$i_thum=unserialize(IMG_THUM);
						for ($i=0; $i < count($arr_imagens); $i++)
						{
							if(!empty($arr_imagens[$i]['is_reference']))$seminovo_reference_image=1;
							$ext=$arr_imagens[$i]['img_ext'];
							$ordem=$arr_imagens[$i]['Ordem'];
							$img_file='files/products/'.$prod_id.'_'.$ordem.'.'.$ext;

							if(!dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file) && $ext!='')
							{
								if($ext=='png')$ext='jpg';
								$img_file='files/products/'.$prod_id.'_'.$ordem.'.'.$ext;
							}

							$img_file_small='files/products/'.$prod_id.'_'.$ordem.'_small.png';
							$img_file_thum='files/products/'.$prod_id.'_'.$ordem.'_thum.png';
							$img_file_prod=$img_file; // Product detail uses the original image.

							// VAMOS PRIMEIRO GERADAR
							if($i_small['ativo'] == 1)	geraimagens($img_file,$img_file_small,$i_small['w'],$i_small['h'],$i_small['crop'],$i_small['m_trans'],$i_small['c_white']);
							if($i_thum['ativo'] == 1) 	geraimagens($img_file,$img_file_thum, $i_thum['w'],$i_thum['h'],$i_thum['crop'],$i_thum['m_trans'],$i_thum['c_white']);

							$img_file_small_cache='';
							$img_file_thum_cache='';
							$img_file_prod_cache='';

							// VAMOS PASSAR A USAR APENAS 3 TAMANHOS,PEQUENA,MEDIA,E FICHA DE PRODUTO
							//Se existir a original
							if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file) && $ext!='')
							{
								if(!dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_small))geraimagens($img_file,$img_file_small,$i_small['w'],$i_small['h'],$i_small['crop'],$i_small['m_trans'],$i_small['c_white']);
								if(!dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_thum))geraimagens($img_file,$img_file_thum, $i_thum['w'],$i_thum['h'],$i_thum['crop'],$i_thum['m_trans'],$i_thum['c_white']);
							}
							if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_small) && $ext!='') $img_file_small_cache='files/products/'.$prod_id.'_'.$ordem.'_small.png?dp='.dotbright_image_mtime($GLOBALS['site_shell_url'].$img_file_small);
							if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_thum) && $ext!='') $img_file_thum_cache='files/products/'.$prod_id.'_'.$ordem.'_thum.png?dp='.dotbright_image_mtime($GLOBALS['site_shell_url'].$img_file_thum);
							if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_prod ) && $ext!='') $img_file_prod_cache=$img_file.'?dp='.dotbright_image_mtime($GLOBALS['site_shell_url'].$img_file);



							if($webp==1)
							{
								$img_webp_small='files/products/'.$prod_id.'_'.$ordem.'_small.webp';
								$img_webp_thum='files/products/'.$prod_id.'_'.$ordem.'_thum.webp';
								if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_small))
								{
									if(!dotbright_image_resolve($GLOBALS['site_shell_url'].$img_webp_small))convert_webp($img_file_small);
									$img_file_small='files/products/'.$prod_id.'_'.$ordem.'_small.webp';
									$img_file_small_cache='files/products/'.$prod_id.'_'.$ordem.'_small.webp?dp='.dotbright_image_mtime($GLOBALS['site_shell_url'].$img_webp_small);
								}
								if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_thum))
								{
									if(!dotbright_image_resolve($GLOBALS['site_shell_url'].$img_webp_thum))convert_webp($img_file_thum);
									$img_file_thum='files/products/'.$prod_id.'_'.$ordem.'_thum.webp';
									$img_file_thum_cache='files/products/'.$prod_id.'_'.$ordem.'_thum.webp?dp='.dotbright_image_mtime($GLOBALS['site_shell_url'].$img_webp_thum);
								}

							}

							if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_small) && $ext!='') $img_file_small=$img_file_small_cache; else $img_file_small=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo_cart.png';
							if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_thum) && $ext!='') $img_file_thum=$img_file_thum_cache; else $img_file_thum=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo.png';
							if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_prod) && $ext!='') $img_file_prod=$img_file_prod_cache; else $img_file_prod=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo2.png';

							if($ext)
							{
								$arr_img[]=$GLOBALS['site_http_url'].$img_file;
								$arr_img_cart[]=$img_file_small;
								$arr_img_med[]=$img_file_prod;
								$arr_img_new[]=$img_file_thum;
								$arr_img_news[]=$img_file_thum;
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
							}
						}
					}
					else
					{
						$arr_img[0]=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo.png';
						$arr_img_cart[0]=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo_cart.png';
						$arr_img_med[0]=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo2.png';
						$arr_img_new[0]=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo.png';
						$arr_img_news[0]=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo_cart.png';
						if($i_fb['ativo'] == 1) $arr_img_fb[0]=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo_cart.png';
					}
				}
				

				/*
				$imgs_pt = get_imagem_produto_total($ref,$prod_id);
				echo '<pre>';
				print_r($imgs_pt);
				echo '</pre>';
				$arr_img[0] = $imgs_pt['main'];
				$arr_img_cart[0] = $imgs_pt['cart'];
				$arr_img_med[0] = $imgs_pt['med'];
				$arr_img_new[0] = $imgs_pt['new'];
				$arr_img_news[0] = $imgs_pt['news'];
				*/



				$destaque_ext='';
				$destaque2_ext='';
				$destaque3_ext='';
				$destaque4_ext='';

				if($rs->db_data['destaque_ext']!='')$destaque_ext=$rs->db_data['destaque_ext'];
				if($rs->db_data['destaque2_ext']!='')$destaque2_ext=$rs->db_data['destaque2_ext'];
				if($rs->db_data['destaque3_ext']!='')$destaque3_ext=$rs->db_data['destaque3_ext'];
				if($rs->db_data['destaque4_ext']!='')$destaque4_ext=$rs->db_data['destaque4_ext'];

				$ext=$arr_imagens[$i]['img_ext'];
				if(dotbright_image_resolve($GLOBALS['site_shell_url'].'files/products/'.$prod_id.'_d.'.$destaque_ext ) && $destaque_ext!='') $destaque_ext='files/products/'.$prod_id.'_d.'.$destaque_ext.'?dp='.dotbright_image_mtime($GLOBALS['site_shell_url'].'files/products/'.$prod_id.'_d.'.$destaque_ext);
				if(dotbright_image_resolve($GLOBALS['site_shell_url'].'files/products/'.$prod_id.'_d2.'.$destaque2_ext ) && $destaque2_ext!='') $destaque2_ext='files/products/'.$prod_id.'_d2.'.$destaque2_ext.'?dp='.dotbright_image_mtime($GLOBALS['site_shell_url'].'files/products/'.$prod_id.'_d2.'.$destaque2_ext);
				if(dotbright_image_resolve($GLOBALS['site_shell_url'].'files/products/'.$prod_id.'_d3.'.$destaque3_ext ) && $destaque3_ext!='') $destaque3_ext='files/products/'.$prod_id.'_d3.'.$destaque3_ext.'?dp='.dotbright_image_mtime($GLOBALS['site_shell_url'].'files/products/'.$prod_id.'_d3.'.$destaque3_ext);
				if(dotbright_image_resolve($GLOBALS['site_shell_url'].'files/products/'.$prod_id.'_d4.'.$destaque4_ext ) && $destaque4_ext!='') $destaque4_ext='files/products/'.$prod_id.'_d4.'.$destaque4_ext.'?dp='.dotbright_image_mtime($GLOBALS['site_shell_url'].'files/products/'.$prod_id.'_d4.'.$destaque4_ext);




				$prods['prod_id']=$prod_id;
				$prods['prod_ordem']=$prod_ordem;
				$prods['prod_carrinho']=$carrinho;
				$prods['obj_Supplier_ID']=$rs->db_data['obj_Supplier_ID'];
				
				

				$prods['produto_standard']=$produto_standard;
				$prods['c_submissao_ficheiro']=$c_submissao_ficheiro;
				$prods['prod_peso']=((isset($peso_cliente) && $peso_cliente > 0) ? $peso_cliente : $peso);
				$prods['fam_id']=$fam_id;
				$prods['prod_precos']=get_produtc_price($prod_id,'',$rs);//se passar o rs ja faz menos 1 query a bd
				$prods['obj_stock']=$prods['prod_precos']['STOCK'];
				$prods['Ref_Original']=$Ref_Original;
				$prods['prod_ref']=$ref;
				$prods['prod_destaque']=$rs->db_data['obj_destaque'];
				$prods['prod_outlet']=$rs->db_data['obj_outlet'];
				$prods['fornecedor_importacao']=$rs->db_data['fornecedor_importacao'];
				$prods['prod_iva']=$obj_iva;
				$prods['prod_iva_txt']=$obj_iva_txt;
				$prods['prod_enc_sem_stock']=$encomenda_sem_stock;
				$prods['Ref_Ean']=$Ref_Ean;
				$prods['estados_produto']=$estados_produto;
				$prods['prd_imgm']=$arr_img[0];
				$prods['prd_imgm_new']=$arr_img_new[0];
				$prods['prd_imgm_news']=$arr_img_news[0];
				$prods['prd_imgm_cart']=$arr_img_cart[0];
				$prods['prd_imgm_med']=$arr_img_med[0];
				$prods['prd_imgm_fb']=$arr_img_fb[0];
				$prods['seminovo_reference_image']=$seminovo_reference_image;
				
				$prods['BOX1']=$BOX1;
				$prods['BOX2']=$BOX2;
				$prods['CABLE']=$CABLE;
				$prods['CHARGER']=$CHARGER;
				$prods['HEADSET']=$HEADSET;
				$prods['COVER']=$COVER;

				$prods['bateria']=$bateria;
				$prods['estado']=$estado;

				$prods['destaque_ext']=$destaque_ext;
				$prods['destaque2_ext']=$destaque2_ext;
				$prods['destaque3_ext']=$destaque3_ext;
				$prods['destaque4_ext']=$destaque4_ext;


				if(substr($ref,0,3)=='REP')
				{
					//Reparacao
					$prods['prod_enc_sem_stock']=1;
					$prods['prod_precos']['STOCK']=99999;
				}

				// if($rs->db_data['ID']==25538) echo '<pre>ID: '.$rs->db_data['ID'].'<br>prod_como_funciona: '.$rs->db_data['Texto3'].'</pre>';

				$prods['prod_nome']=htmlentities_memo(stripslashes($rs->db_data['Cabecalho']));
				$prods['prod_subtitulo']=htmlentities_memo(stripslashes($rs->db_data['Subtitulo']));
				$prods['prod_descricao']=converterPontoParaTraco(limpa_editor_lixo(stripslashes(strlen($rs->db_data['Texto1']) > 300 ? html_cut($rs->db_data['Texto1'],297)."... " : $rs->db_data['Texto1'])));
				$prods['prod_descricao2']=converterPontoParaTraco($rs->db_data['Texto1']);
				$prods['prod_features']=converterPontoParaTraco($rs->db_data['Texto1']);
				$prods['prod_tempo_entrega']=converterPontoParaTraco($rs->db_data['tempo_entrega']);
				$prods['prod_garantia']=limpa_editor_lixo(stripslashes($rs->db_data['Tamanho']));
				$prods['prod_como_funciona']=converterPontoParaTraco($rs->db_data['Texto3']);
				$prods['prod_envio']=converterPontoParaTraco($rs->db_data['Texto4']);
				$prods['prod_customizado_titulo']=htmlentities_memo(stripslashes($rs->db_data['Cabecalho_Texto5']));
				$prods['prod_customizado']=converterPontoParaTraco($rs->db_data['Texto5']);
				$prods['prod_id_marca']=$marca;
				$prods['prod_marca']=get_produtc_brand($marca);
				$prods['prd_imgs']=array('img'=>$arr_img,'img_cart'=>$arr_img_cart,'img_med'=>$arr_img_med,'img_new'=>$arr_img_new,'img_news'=>$arr_img_news,'img_fb'=>$arr_img_fb);

				$prods['prod_bateria']=htmlentities_memo(stripslashes($rs->db_data['bateria']));
				$prods['prod_estado']=htmlentities_memo(stripslashes($rs->db_data['estado']));
				$prods['prod_localizacao_fisica']=htmlentities_memo(stripslashes($rs->db_data['localizacao_fisica']));

				$aux_icons=$rs->db_data['store_icons'];
				$prods['store_icons']=$rs->db_data['store_icons'];

				// if($rs->db_data['ID']==25538) echo '<br><br>prod_como_funciona: '.$prods['prod_como_funciona'];
				
				$prods['ref_cols']=$rs->db_data['ref_cols'];
				$prods_aux['ref_vals']=$rs->db_data['ref_vals'];

				$aux_icons = substr($aux_icons, 0, strlen($aux_icons)-1);
				$aux_icons = str_replace(';', ',', $aux_icons);

				/* ETIQUETAS ENERGETICAS */
				// if(isset($P_CONF) && $P_CONF['tem_etiquetas_energeticas'] == 1)
				// {
					$prods['u_catetiq']=$rs->db_data['u_catetiq'];
					$prods['u_coretiq']=$rs->db_data['u_coretiq'];
					if($rs->db_data['u_imagetiq'] != '' && dotbright_image_resolve($GLOBALS['site_shell_url'].'files/products/EE/'.$rs->db_data['u_imagetiq'])) $prods['u_imagetiq']=SITE_HTTP_URL.'files/products/EE/'.$rs->db_data['u_imagetiq'];
					else $prods['u_imagetiq']='';

					$prods['prod_video']=$rs->db_data['Video'];
					$prods['prod_coree'] =$rs->db_data['corEE'];
					$prods['EtiquetaEnergiaNova']  = $rs->db_data['EtiquetaEnergiaNova'];

					if($rs->db_data['ee_ext'] && dotbright_image_resolve($GLOBALS['site_shell_url'].'files/products/EE/'.$prod_id.'_1.'.$rs->db_data['ee_ext'])) $prods['prod_ee'] =$GLOBALS['site_http_url'].'files/products/EE/'.$prod_id.'_1.'.$rs->db_data['ee_ext'];
					if($rs->db_data['ue_ext'] && dotbright_image_resolve($GLOBALS['site_shell_url'].'files/products/UE/'.$prod_id.'_1.'.$rs->db_data['ue_ext'])) $prods['prod_ue'] =$GLOBALS['site_http_url'].'files/products/UE/'.$prod_id.'_1.'.$rs->db_data['ue_ext'];
					if($rs->db_data['dt_ext'] && dotbright_image_resolve($GLOBALS['site_shell_url'].'files/products/DT/'.$prod_id.'_1.'.$rs->db_data['dt_ext'])) $prods['prod_dt']=$GLOBALS['site_http_url'].'files/products/DT/'.$prod_id.'_1.'.$rs->db_data['dt_ext'];
				// }
				/* BONUS */
				if(isset($P_CONF) && $P_CONF['tem_bonus'] == 1) $prods['BONUS']=show_prod_points($prod_id,$prods['prod_precos']);
				//$prods['GLOBAL_PROMO']=show_global_promos();
				/* PERSONALIZACAO PRODUTO */
				if(isset($P_CONF) && $P_CONF['tem_personalizacao'] == 1) $prods['PERSONALIZACAO']=show_personalizao($personalizacao);

				//**********************************************************
				//grava o array em fx para nao ter de vir de bd
				file_put_contents($GLOBALS['site_shell_url'].'files/cache/products/p'.dotbright_product_images_cache_key().$prod_id.'_'.$lingua.'.bin',serialize($prods));
				$produtos_pagina[$prod_id]=1;

				//Atualiza acelerador de pesquisa
				/*
				$texto = $cabecalho = addslashes($rs->db_data['Cabecalho']);
				$id_fam = $rs->db_data['ID_ORG'];
				$fams = encontraFamiliasRecursiva_x($id_fam);
				if(isset($fams[0])) $fam_id1 = $fams[0]; else $fam_id1 = $id_org;
				if(isset($fams[1])) $fam_id2 = $fams[1]; else $fam_id2 = 0;
				if(isset($fams[2])) $fam_id3 = $fams[2]; else $fam_id3 = 0;
				if(isset($fams[3])) $fam_id4 = $fams[3]; else $fam_id4 = 0;
				$ID_Marca = $rs->db_data['ID_Marca'];
				$sql="SELECT Nome FROM brands_lang WHERE Lang='$site_lang' and ID_PAI=$ID_Marca";
				$hRes=$rs2->db_open( $db_link,$DB_name,$sql);
				if(!$rs2->EOF())
				{
					$marcax=addslashes($rs2->db_data['Nome']);
				}
				$rs2->db_close();

				if($marcax) $marcax = ','.$marcax;
				else $marcax = '';
				$sql="SELECT Cabecalho FROM products_families_lang WHERE Lang='$site_lang' and ID_PAI=$id_fam";
				$hRes=$rs2->db_open( $db_link,$DB_name,$sql);
				if(!$rs2->EOF())
				{
					$cabecalho=addslashes($rs2->db_data['Cabecalho']);
				}
				$rs2->db_close();

				if($familia) $familia = ','.$familia;
				else $familia = '';
				$texto.= $marcax.$familia;



				//**********************************************************
				$sql="SELECT ID_Prod FROM acelerador_pesquisa WHERE ID_Prod='$prod_id'";
				$hRes=$rs2->db_open($db_link,$DB_name,$sql);
				if(!$rs2->EOF())
		        {
		            $sql = "UPDATE acelerador_pesquisa set Ref='$ref',EAN='$Ref_Ean',Texto='$texto',Flag=1,Cabecalho='$cabecalho',Preco_Site='$preco_site',fam_id1='$fam_id1',fam_id2='$fam_id2',fam_id3='$fam_id3',fam_id4='$fam_id4',marca_id='$ID_Marca' WHERE ID_Prod=".$prod_id;
		        }
				else
		        {
		            $sql = "INSERT INTO acelerador_pesquisa (Ref,EAN,Texto,ID_Prod,Flag,Cabecalho,Preco_Site,fam_id1,fam_id2,fam_id3,fam_id4,marca_id) values ('$ref','$EAN','$texto','$prod_id',1,'$cabecalho','$preco_site','$fam_id1','$fam_id2','$fam_id3','$fam_id4','$ID_Marca')";
		        }
				$rs2->db_close();
				if(!Runquery($db_link,$GLOBALS['DB_name'],$sql)) echo "Data Base ERROR: " . db_error($db_link).$sql."<br>";
				*/
				//**********************************************************


			}
			if($obj == '')$rs->db_close();
		}
	}

	return dotbright_product_image_fields($prods);
}

function get_produtc_info_essentials($prod_id=0,$ignore_img=0)
{
	if($lingua == '')
	{
		if(isset($_SERVER['REQUEST_URI']) && substr_count($_SERVER['REQUEST_URI'],'/admin/')) $lingua=$GLOBALS['admin_lang'];
		else $lingua=$GLOBALS['site_lang'];
	}
	$prods=NULL;
	if(isset($prod_id) && $prod_id > 0 || isset($id_fam) && $id_fam > 0)
	{
		//Se ja carreguei os dados deste artigo ao carregar a pagina, vou ler da memoria:
		global $produtos_pagina;
		if(!is_array($produtos_pagina))$produtos_pagina=array();
		if($produtos_pagina[$prod_id]==1 && dotbright_image_resolve($GLOBALS['site_shell_url'].'files/cache/products/p'.dotbright_product_images_cache_key().$prod_id.'_'.$lingua.'.bin') )
		{
			$prods = unserialize(file_get_contents($GLOBALS['site_shell_url'].'files/cache/products/p'.dotbright_product_images_cache_key().$prod_id.'_'.$lingua.'.bin'));
		}
		else
		{
			if(isset($GLOBALS['site_prev']) && $GLOBALS['site_prev']==1)
			{
				if($id_fam > 0) $and .= ' AND PFO.ID_ORG='.$id_fam;
				else $and=' P.ID='.$prod_id;
			}
			else
			{
				$and =' P.Aproved=1 ';
				$and .= ' AND P.ID='.$prod_id;
			}

			$and .= ' AND obj_type="products"';
			$and .= ' AND (obj_val2=0 OR obj_val2 IS NULL)';

			$sql='SELECT distinct P.ID,P.udtime,P.Ordem,P.Ref as Ref_Original,P.Ref_secundaria as Ref,P.Ref_Ean, P.Peso,P.peso_cliente,P.Carrinho,P.ID_Marca,P.Preco1,P.personalizacao,P.encomenda_sem_stock,
						P.Video,P.corEE,P.ee_ext,P.ue_ext,P.dt_ext,P.u_catetiq,P.u_coretiq,P.u_imagetiq,P.produto_standard,P.c_submissao_ficheiro,
						PL.Cabecalho,PL.Texto1,PL.Texto2,PL.Texto3,PL.Texto4,PL.Cabecalho_Texto5,PL.Texto5,PL.Cor,PL.Tamanho,PL.Subtitulo,PL.tempo_entrega,P.store_icons, P.store_icons, P.bateria,P.estado,
						PO.obj_destaque,PO.obj_outlet,PO.obj_iva,PO.obj_iva_txt,

						PO.obj_destaque,PO.obj_outlet,PO.obj_iva,PO.obj_iva_txt,PO.obj_stock,PO.obj_val2,
						PO.obj_pvp,PO.obj_promo,PO.obj_promo_ativo,PO.obj_promo_data,PO.obj_Supplier_ID,
						PO.obj_destaque_data,PO.obj_travelsize,PO.obj_novo,PO.obj_novo_data,PO.obj_outlet_data,PO.obj_dia,PO.obj_dia_data,

						PFO.ID_ORG,PO.obj_stock
					FROM
						products AS P
						INNER JOIN products_lang AS PL ON PL.ID_PAI=P.ID
						INNER JOIN products_families_obj as PFO ON P.ID=PFO.ID_PROD
						INNER JOIN products_objs as PO ON PO.obj_type_id=P.ID
					WHERE
						'.$and.'
						AND PO.obj_stock > 0
						AND PO.obj_pvp > 0
						AND PL.Lang="'.$lingua.'"';

			if($id_fam > 0) $sql .= ' ORDER by RAND() Asc LIMIT 1';
			else $sql .= ' ORDER by P.Ordem Asc';

			$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
			$rs=new DataObj;
           // die($sql);
			$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
			if(!$rs->EOF())
			{
				$P_CONF=unserialize(PROD_CONF);
				if(defined('CARRINHO_CONF')) $carrinho_conf=unserialize(CARRINHO_CONF); else $carrinho_conf=array();
				$prod_id=$rs->db_data['ID'];
				$fam_id=$rs->db_data['ID_ORG'];
				$prod_ordem=$rs->db_data['Ordem'];
				$obj_iva=$rs->db_data['obj_iva'];
				$Ref_Original=$rs->db_data['Ref_Original'];
				$ref=($rs->db_data['Ref']=='' ? $rs->db_data['Ref_Original'] : $rs->db_data['Ref']);
				$Ref_Ean=$rs->db_data['Ref_Ean'];
				$marca=$rs->db_data['ID_Marca'];
				$produto_standard=$rs->db_data['produto_standard'];
				$c_submissao_ficheiro=$rs->db_data['c_submissao_ficheiro'];
				$carrinho=$rs->db_data['Carrinho'];
				$estados_produto=$rs->db_data['estados_produto'];
				if(!$ignore_img)
				{
					//$arr_imagens=get_product_images_first($prod_id);//na versao rapida trazemos apenas 1 imagem
					$arr_imagens=get_product_images($prod_id);//na versao rapida trazemos apenas 1 imagem

                    if(count($arr_imagens) > 0)
					{
						$webp=0;
						if( strpos( $_SERVER['HTTP_ACCEPT'],'image/webp' ) !== false )$webp=1;// webp is supported!
						$webp=0;

						$i_small=unserialize(IMG_SMALL);
						$i_thum=unserialize(IMG_THUM);
						$i=0;

						for ($i=0; $i < count($arr_imagens); $i++)
						{

							$ext=$arr_imagens[$i]['img_ext'];
							$ordem=$arr_imagens[$i]['Ordem'];
							$img_file='files/products/'.$prod_id.'_'.$ordem.'.'.$ext;

							$img_file_small='files/products/'.$prod_id.'_'.$ordem.'_small.png';
							$img_file_thum='files/products/'.$prod_id.'_'.$ordem.'_thum.png';
							$img_file_prod=$img_file; // Product detail uses the original image.

							// VAMOS PRIMEIRO GERAR
							if($i_small['ativo'] == 1)	geraimagens($img_file,$img_file_small,$i_small['w'],$i_small['h'],$i_small['crop'],$i_small['m_trans'],$i_small['c_white']);
							if($i_thum['ativo'] == 1) 	geraimagens($img_file,$img_file_thum, $i_thum['w'],$i_thum['h'],$i_thum['crop'],$i_thum['m_trans'],$i_thum['c_white']);

							$img_file_small_cache='';
							$img_file_thum_cache='';
							$img_file_prod_cache='';

							// VAMOS PASSAR A USAR APENAS 3 TAMANHOS,PEQUENA,MEDIA,E FICHA DE PRODUTO

							//Se existir a original
							if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file) && $ext!='')
							{
								if(!dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_small))geraimagens($img_file,$img_file_small,$i_small['w'],$i_small['h'],$i_small['crop'],$i_small['m_trans'],$i_small['c_white']);
								if(!dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_thum))geraimagens($img_file,$img_file_thum, $i_thum['w'],$i_thum['h'],$i_thum['crop'],$i_thum['m_trans'],$i_thum['c_white']);
							}

							if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_small) && $ext!='') $img_file_small_cache='files/products/'.$prod_id.'_'.$ordem.'_small.png?dp='.dotbright_image_mtime($GLOBALS['site_shell_url'].$img_file_small);
							if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_thum) && $ext!='') $img_file_thum_cache='files/products/'.$prod_id.'_'.$ordem.'_thum.png?dp='.dotbright_image_mtime($GLOBALS['site_shell_url'].$img_file_thum);
							if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_prod ) && $ext!='') $img_file_prod_cache=$img_file.'?dp='.dotbright_image_mtime($GLOBALS['site_shell_url'].$img_file);

							if($webp==1)
							{
								$img_webp_small='files/products/'.$prod_id.'_'.$ordem.'_small.webp';
								$img_webp_thum='files/products/'.$prod_id.'_'.$ordem.'_thum.webp';

								if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_small))
								{
									if(!dotbright_image_resolve($GLOBALS['site_shell_url'].$img_webp_small))convert_webp($img_file_small);
									$img_file_small='files/products/'.$prod_id.'_'.$ordem.'_small.webp';
									$img_file_small_cache='files/products/'.$prod_id.'_'.$ordem.'_small.webp?dp='.dotbright_image_mtime($GLOBALS['site_shell_url'].$img_webp_small);
								}

								if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_thum))
								{
									if(!dotbright_image_resolve($GLOBALS['site_shell_url'].$img_webp_thum))convert_webp($img_file_thum);
									$img_file_thum='files/products/'.$prod_id.'_'.$ordem.'_thum.webp';
									$img_file_thum_cache='files/products/'.$prod_id.'_'.$ordem.'_thum.webp?dp='.dotbright_image_mtime($GLOBALS['site_shell_url'].$img_webp_thum);
								}

							}

							if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_small) && $ext!='') $img_file_small=$img_file_small_cache; else $img_file_small=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo_cart.png';
							if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_thum) && $ext!='') $img_file_thum=$img_file_thum_cache; else $img_file_thum=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo.png';
							if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_prod) && $ext!='') $img_file_prod=$img_file_prod_cache; else $img_file_prod=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo2.png';

							if($ext)
							{
								$arr_img[]=$GLOBALS['site_http_url'].$img_file;
								$arr_img_cart[]=$img_file_small;
								$arr_img_med[]=$img_file_prod;
								$arr_img_new[]=$img_file_thum;
								$arr_img_news[]=$img_file_thum;
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
							}
						}
					}
					else
					{
						$arr_img[0]=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo.png';
						$arr_img_cart[0]=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo_cart.png';
						$arr_img_med[0]=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo2.png';
						$arr_img_new[0]=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo.png';
						$arr_img_news[0]=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo_cart.png';
						if($i_fb['ativo'] == 1) $arr_img_fb[0]=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo_cart.png';
					}
					$prods['prd_imgm_new']=$arr_img_new[0];
					$prods['prd_imgm_news']=$arr_img_news[0];
					$prods['prd_imgm_cart']=$arr_img_cart[0];
					$prods['prd_imgm_med']=$arr_img_med[0];
					$prods['prd_imgm_fb']=$arr_img_fb[0];
				}
				$prods['prod_id']=$prod_id;
				$prods['fam_id']=$fam_id;
				$prods['prod_ordem']=$prod_ordem;
				$prods['prod_carrinho']=$carrinho;
				$prods['produto_standard']=$produto_standard;
				$prods['c_submissao_ficheiro']=$c_submissao_ficheiro;
				$prods['prod_ref']=$ref;
				$prods['Ref_Original']=$Ref_Original;
				$prods['prod_destaque']=$rs->db_data['obj_destaque'];
				$prods['prod_outlet']=$rs->db_data['obj_outlet'];
				$prods['prod_iva']=$obj_iva;
				$prods['Ref_Ean']=$Ref_Ean;
				$prods['prod_nome']=stripslashes($rs->db_data['Cabecalho']);
				$prods['prod_subtitulo']=stripslashes($rs->db_data['Subtitulo']);
				$prods['prod_descricao']=converterPontoParaTraco(limpa_editor_lixo(stripslashes(strlen($rs->db_data['Texto1']) > 150 ? html_cut($rs->db_data['Texto1'],147)."... " : $rs->db_data['Texto1'])));
				$prods['prod_precos']=get_produtc_price($prod_id,'',$rs);//se passar o rs ja faz menos 1 query a bd
				$prods['prod_id_marca']=$marca;
				$prods['prod_marca']=get_produtc_brand($marca);
				if(!$ignore_img)$prods['prd_imgs']=array('img'=>$arr_img,'img_cart'=>$arr_img_cart,'img_med'=>$arr_img_med,'img_new'=>$arr_img_new,'img_news'=>$arr_img_news,'img_fb'=>$arr_img_fb);
				
				$aux_icons=$rs->db_data['store_icons'];
				$prods['store_icons']=$rs->db_data['store_icons'];
				
				$prods['prod_bateria']=htmlentities_memo(stripslashes($rs->db_data['bateria']));
				$prods['prod_estado']=htmlentities_memo(stripslashes($rs->db_data['estado']));


				if(substr($ref,0,3)=='REP')
				{
					//Reparacao
					$prods['prod_enc_sem_stock']=1;
					$prods['prod_precos']['STOCK']=99999;
				}

				
			}
			$rs->db_close();
		}
	}
	return dotbright_product_image_fields($prods);
}

function filtros_ficha_produto_verifica($prod_id=0)
{
	global $site_lang;
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$obj2=new DataObj; $filtros=array();$return='';
	$sql2='SELECT fp.cat_id,fp.valor_id,fp.filtro_area
			FROM filters_products fp
			WHERE prod_id='.$prod_id.' AND fp.filtros_rel2 <> "" AND fp.filtro_active=1 AND (fp.filtro_area=1 OR fp.filtro_area=2)
			GROUP BY fp.valor_id,fp.cat_id
			Order By fp.filtro_id asc';
	$obj2->db_open($db_link,$GLOBALS['DB_name'],$sql2);
	$a=0;
	if(!$obj2->EOF())$a=1;
	$obj2->db_close();
	return $a;
}

function show_personalizao($personalizacao=array())
{
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$dados=new DataObj;
	$return=array();
	if(!empty($personalizacao))
	{
		$OR=''; $sql_or='';
		for($a=0; $a<(count($personalizacao)-1); $a++)
		{
			if($a > 0) $OR .= ' OR pa.bloco_id='.$personalizacao[$a];
			else  $OR .= ' pa.bloco_id='.$personalizacao[$a];
		}
		if($OR!='') $sql_or='('.$OR.')';
		$sql='SELECT pa.bloco_id,Pa.zona_id,Pal.bloco_titulo,Pal.bloco_morada FROM personalizacao pa INNER JOIN personalizacao_lang pal ON pal.bloco_id=pa.bloco_id WHERE ';
		$sql .= $sql_or;
		$sql .= ' AND bloco_ativo=1 AND pal.bloco_lang="'.$GLOBALS['admin_lang'].'" ';
		$dados->db_open($db_link,$GLOBALS['DB_name'],$sql);
		if(!$dados->EOF())
		{
			while(!$dados->EOF())
			{
				$return[$dados->db_data['zona_id']]=array('id'=>$dados->db_data['bloco_id'],'nome'=>$dados->db_data['bloco_titulo'],'descricao'=>$dados->db_data['bloco_morada']);
				$dados->db_next();
			}
		}
		$dados->db_close();
	}
	return $return;
}

function converterPontoParaTraco($campo)
{
	$campo=limpa_editor_lixo(stripslashes($campo));
	$campo=str_replace('QUALIDADE DE IMAGEM:','<b>QUALIDADE DE IMAGEM:</b>',$campo);
	$campo=str_replace('QUALIDADE DE SOM','<b>QUALIDADE DE SOM</b>',$campo);
	$campo=str_replace('Acessórios Incluidos:','<b>Acessórios Incluidos:</b>',$campo);
	$campo=str_replace('QUALIDADE DE IMAGEM:','<b>QUALIDADE DE IMAGEM:</b>',$campo);
	return $campo;
}

function get_produtc_new($tipo='',$limit=0,$rel=0,$fam=0)
{
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;
	if($rel==1)
	{
		$sql='SELECT PO.ID_Prod2 as ID,P.Ref,P.ID_ORG,P.img_ext,P.Preco1,P.Destaque,PL.Cabecalho,PL.Texto1,PL.Texto2,PL.Texto3,PL.Cor as Trailer
		FROM products AS P
		INNER JOIN products_lang AS PL ON PL.ID_PAI=P.ID
		INNER JOIN produtos_opcoes AS PO ON PO.ID_Prod2=P.ID ';
	}
	else
	{
		$sql='SELECT P.ID,P.Ref,P.ID_ORG,P.img_ext,P.Preco1,P.Destaque,PL.Cabecalho,PL.Texto1,PL.Texto2,PL.Texto3,PL.Cor as Trailer
		FROM products AS P
		INNER JOIN products_lang AS PL ON PL.ID_PAI=P.ID';
	}
	$sql .= ' WHERE P.Aproved=1 AND PL.Lang="'.$GLOBALS['site_lang'].'"';
	if($fam > 0)$sql .= ' AND P.ID_ORG="'.$fam.'"';
	if($tipo == 'novidade')$sql .= ' AND P.Novidade=1 ';
	elseif($tipo == 'destaque')$sql .= ' AND P.Home=1 ';
	if($rel==1)$sql .= ' GROUP BY PO.ID_Prod2 ';
	if($limit > 0)$sql .= ' limit '.$limit.' ';
	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	if(!$rs->EOF())
	{
		$prods=array();
		$i=0;
		while(!$rs->EOF())
		{
			$prod_id=$rs->db_data['ID'];
			$fam_id=$rs->db_data['ID_ORG'];
			$I1=$rs->db_data['img_ext'];
			$ref=$rs->db_data['Ref'];
			$img_file='files/products/'.$prod_id.'_1.'.$I1;
			$img_file_cart='files/products/'.$prod_id.'_1_cart.png';
			$img_file_med='files/products/'.$prod_id.'_1_med.png';
			$img_file_new='files/products/'.$prod_id.'_1_new.png';
			if (dotbright_shared_images()) {
				if (!dotbright_image_exists($GLOBALS['site_shell_url'].$img_file)) {
					foreach (array('png','jpg','jpeg','webp','gif','avif','JPG','PNG') as $candidate) {
						$alternative='files/products/'.$prod_id.'_1.'.$candidate;
						if (dotbright_image_exists($GLOBALS['site_shell_url'].$alternative)) { $img_file=$alternative; break; }
					}
				}
				$img_file_cart=dotbright_image_url('files/products/'.$prod_id.'_1_small.png');
				$img_file_med=dotbright_image_url($img_file);
				$img_file_new=dotbright_image_url('files/products/'.$prod_id.'_1_thum.png');
			} else {
			$img_file_cart_cache='files/products/'.$prod_id.'_'.$ordem.'_small_v_'.dotbright_image_mtime($GLOBALS['site_shell_url'].$img_file_cart).'.png';
			$img_file_med_cache='files/products/'.$prod_id.'_'.$ordem.'_thum_v_'.dotbright_image_mtime($GLOBALS['site_shell_url'].$img_file_med).'.png';
			$img_file_new_cache='files/products/'.$prod_id.'_'.$ordem.'_prod_v_'.dotbright_image_mtime($GLOBALS['site_shell_url'].$img_file_new).'.png';

			if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file)) $original=date('Y-m-d H:i:s',dotbright_image_mtime($GLOBALS['site_shell_url'].$img_file)); // data/hora em que foi modificada/criada
			if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_cart)) $cart=date('Y-m-d H:i:s',dotbright_image_mtime($GLOBALS['site_shell_url'].$img_file_cart)); //data/hora em que foi modificada/criada
			if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_med)) $med=date('Y-m-d H:i:s',dotbright_image_mtime($GLOBALS['site_shell_url'].$img_file_med)); //data/hora em que foi modificada/criada
			if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_new)) $new=date('Y-m-d H:i:s',dotbright_image_mtime($GLOBALS['site_shell_url'].$img_file_new)); //data/hora em que foi modificada/criada

			if((!dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_cart) || $cart<$original) && dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file))
			{
				crop_white_space($GLOBALS['site_shell_url'],$img_file,$img_file_cart);
				if(redimencionaImagem($GLOBALS['site_shell_url'],$img_file_cart,50,50,$img_file_cart)){make_transparent($GLOBALS['site_shell_url'],$img_file_cart,$img_file_cart);}
			}
			if((!dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_med) || $med<$original) && dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file))
			{
				crop_white_space($GLOBALS['site_shell_url'],$img_file,$img_file_med);
				if(redimencionaImagem($GLOBALS['site_shell_url'],$img_file_med,298,298,$img_file_med)){make_transparent($GLOBALS['site_shell_url'],$img_file_med,$img_file_med);}
			}
			if((!dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_new) || $prd<$original) && dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file))
			{
				crop_white_space($GLOBALS['site_shell_url'],$img_file,$img_file_new);
				if(redimencionaImagem($GLOBALS['site_shell_url'],$img_file_new,140,140,$img_file_new)){make_transparent($GLOBALS['site_shell_url'],$img_file_new,$img_file_new);}
			}
			if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_cart) && $I1!='') $img_file_cart=$GLOBALS['site_http_url'].$img_file_cart_cache;
			else $img_file_cart=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo_cart.png';
			if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_med) && $I1!='') $img_file_med=$GLOBALS['site_http_url'].$img_file_med_cache;
			else $img_file_med=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo2.png';
			if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_new) && $I1!='') $img_file_new=$GLOBALS['site_http_url'].$img_file_new_cache;
			else $img_file_new=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo.png';
			}
			$prods['prod_id'][$i]=$prod_id;
			$prods['fam_id'][$i]=$fam_id;
			$prods['prod_ref'][$i]=$ref;
			$prods['prd_imgm'][$i]=$img_file_cart;
			$prods['prd_imgm_med'][$i]=$img_file_med;
			$prods['prd_imgm_new'][$i]=$img_file_new;
			$prods['prod_nome'][$i]=htmlentities_memo(stripslashes($rs->db_data['Cabecalho']));
			$prods['prod_descricao'][$i]=str_replace(". ",'.<br>',str_replace("\n",'<br>',limpa_editor_lixo(stripslashes(strlen($rs->db_data['Texto1']) > 195 ? substr($rs->db_data['Texto1'],0,192) : $rs->db_data['Texto1']))));
			$prods['prod_descricao2'][$i]=str_replace(". ",'.<br>',str_replace("\n",'<br>',limpa_editor_lixo(stripslashes($rs->db_data['Texto1']))));
			$prods['prod_features'][$i]=str_replace(". ",'.<br>',str_replace("\n",'<br>',limpa_editor_lixo(stripslashes($rs->db_data['Texto3']))));
			$prods['prod_precos'][$i]=get_produtc_price($prod_id);
			$rs->db_next();
			$i++;
		}
	}
	$rs->db_close();
	return dotbright_product_image_fields($prods);
}

function show_brands()
{
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new Dataobj;
	$sql_enc="SELECT b.ID,b.img_ext2,bl.Nome FROM brands b INNER JOIN brands_lang bl ON bl.ID_PAI=b.ID WHERE Visivel=1";
	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql_enc);
	$seo=seo('pages.php?page_id='.$GLOBALS['catalog_page_id']);
	$brand_list ='';
	while(!$rs->EOF())
	{
		$target='?brand='.$rs->db_data['ID'];
		$brand_list .='<a href="m-'.$rs->db_data['ID'].'-'.limpa_special($rs->db_data['Nome']).'-'.$GLOBALS['site_lang'].'"><div style="float:left; padding:5px"><img alt="" title="" src="'.$GLOBALS['site_http_url'].'files/brands/'.$rs->db_data['ID'].'_2.'.$rs->db_data['img_ext2'].'" ></div></a>';
		$rs->db_next();
	}
	$rs->db_close();
	return $brand_list;
}

function get_family_info($fam_id)
{
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;
	if(isset($fam_id) && $fam_id > 0)
	{
		$sql='SELECT
					P.ID,
					P.ID_ORG,
					P.Download,
					PL.Cabecalho,
					PL.Cabecalho2
				FROM products_families AS P INNER JOIN products_families_lang AS PL ON PL.ID_PAI=P.ID
				WHERE P.visivel=1 AND P.ID='.$fam_id.' AND PL.Lang="'.$GLOBALS['site_lang'].'"
				ORDER by P.Ordem Asc';
		$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
		if(!$rs->EOF())
		{
			$fam_id=$rs->db_data['ID'];
			$fam_parent=$rs->db_data['ID_ORG'];
			if($rs->db_data['Cabecalho2']) $fam_nome=htmlentities_memo(stripslashes($rs->db_data['Cabecalho2']));
			else $fam_nome=htmlentities_memo(stripslashes($rs->db_data['Cabecalho']));
			$fams['fam_id']=$fam_id;
			$fams['fam_parent']=$fam_parent;
			$fams['fam_nome']=limpa_editor_lixo(stripslashes($fam_nome));
			$fams['reparacoes']=$rs->db_data['Download'];
		}
		$rs->db_close();
	}
	return $fams;
}

function get_product_families($id_pai=0)
{
    $db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
    $rs=new Dataobj;
    $margem=array();

    $sql='SELECT ID,ID_ORG,MargemCli,MargemTipo FROM products_families WHERE visivel=1 AND ID="'.$id_pai.'"';
    $rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
    if(!$rs->EOF())
	{
		if($rs->db_data['MargemCli'] != 0)
		{
			$margem[0]=$rs->db_data['MargemCli'];
			$margem[1]=$rs->db_data['MargemTipo'];
		}
		else $margem=get_product_families($rs->db_data['ID_ORG']);
    }
    $rs->db_close();
	return $margem;
}

function get_product_families_Margem_formula($id_pai)
{
    $db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
    $rs=new Dataobj; $margem=0;
    $sql='SELECT pf.ID,pf.ID_ORG,pf.Margem_formula FROM products_families pf WHERE pf.visivel=1 AND pf.ID='.$id_pai;
    $rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
    if(!$rs->EOF())
	{
		if(trim($rs->db_data['Margem_formula']) != '') $margem=$rs->db_data['Margem_formula'];
		else $margem=get_product_families_Margem_formula($rs->db_data['ID_ORG']);
    }
    $rs->db_close();
	return $margem;
}

/**
 * Avalia as formulas de margem antigas sem executar codigo PHP vindo da BD.
 * Suporta apenas PVP, numeros, parenteses e os operadores +, -, * e /.
 * Devolve null quando a formula nao pertence a este subconjunto seguro.
 */
function get_product_margin_formula_value($formula,$pvp)
{
	if(!is_string($formula) || trim($formula)==='') return null;

	$expression=trim($formula);
	$expression=preg_replace('/^<\?(?:php)?/i','',$expression);
	$expression=preg_replace('/\?>$/','',$expression);
	$expression=preg_replace('/^(?:echo|return)\s+/i','',$expression);
	$expression=preg_replace('/^\$margem\s*=\s*/i','',$expression);
	$expression=rtrim(trim($expression),"; \t\n\r\0\x0B");

	$pvp_value=rtrim(rtrim(sprintf('%.14F',(float)$pvp),'0'),'.');
	if($pvp_value==='') $pvp_value='0';
	$expression=preg_replace('/\bPVP\b/',$pvp_value,$expression);
	$expression=str_replace(',','.',$expression);
	$expression=preg_replace('/\s+/','',$expression);
	if($expression==='' || preg_match('/[^0-9\.\+\-\*\/\(\)]/',$expression)) return null;

	preg_match_all('/(?:\d+(?:\.\d*)?|\.\d+|[\+\-\*\/\(\)])/',$expression,$matches);
	$tokens=$matches[0];
	if(implode('',$tokens)!==$expression) return null;

	$output=array();
	$operators=array();
	$precedence=array('u+'=>3,'u-'=>3,'*'=>2,'/'=>2,'+'=>1,'-'=>1);
	$expect_operand=true;
	foreach($tokens as $token)
	{
		if(is_numeric($token))
		{
			$output[]=(float)$token;
			$expect_operand=false;
		}
		elseif($token==='(')
		{
			$operators[]=$token;
			$expect_operand=true;
		}
		elseif($token===')')
		{
			$found_open=false;
			while(count($operators)>0)
			{
				$operator=array_pop($operators);
				if($operator==='(')
				{
					$found_open=true;
					break;
				}
				$output[]=$operator;
			}
			if(!$found_open || $expect_operand) return null;
			$expect_operand=false;
		}
		else
		{
			if($expect_operand)
			{
				if($token!=='+' && $token!=='-') return null;
				$token='u'.$token;
			}
			while(count($operators)>0)
			{
				$top=end($operators);
				if($top==='(' || $precedence[$top]<$precedence[$token]) break;
				$output[]=array_pop($operators);
			}
			$operators[]=$token;
			$expect_operand=true;
		}
	}
	if($expect_operand) return null;
	while(count($operators)>0)
	{
		$operator=array_pop($operators);
		if($operator==='(') return null;
		$output[]=$operator;
	}

	$values=array();
	foreach($output as $token)
	{
		if(is_float($token))
		{
			$values[]=$token;
			continue;
		}
		if($token==='u+' || $token==='u-')
		{
			if(count($values)<1) return null;
			$value=array_pop($values);
			$values[]=($token==='u-') ? -$value : $value;
			continue;
		}
		if(count($values)<2) return null;
		$right=array_pop($values);
		$left=array_pop($values);
		if($token==='+') $value=$left+$right;
		elseif($token==='-') $value=$left-$right;
		elseif($token==='*') $value=$left*$right;
		else
		{
			if($right==0.0) return null;
			$value=$left/$right;
		}
		if(!is_finite($value)) return null;
		$values[]=$value;
	}
	return count($values)===1 ? $values[0] : null;
}

function retiraiva($valor_original=0,$iva_p=0)
{
	$valor_final=$valor_original;
	if($valor_original > 0)
	{
		if($iva_p > 0) $iva=$iva_p;
		else $iva=$GLOBALS['iva_default'];
		$valor=$valor_original;
		$perc=1 + ($iva / 100);
		$valor_final=$valor / $perc;
	}
	return $valor_final;
}

function get_produtc_price($prod_id=0,$prod_obj_id='',$obj='')
{
	date_default_timezone_set("Europe/Lisbon");
	setlocale(LC_TIME,"Portuguese_Standard");
	setlocale(LC_ALL,"en_US.UTF-8;LC_NUMERIC=C");
	setlocale(LC_NUMERIC,'en_US');

	global $cli_id,$cli_token;
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;
	$rs2=new DataObj;

	$revenda=0;
	if($dados=\cliente_logado())
	{
		$revenda=$dados['Grupo'];
	}

	if(isset($prod_id) && $prod_id > 0)
	{
		$preco_mercado = 0;
		$sql = 'SELECT obj_pvp FROM products_objs INNER JOIN products on products.ID=products_objs.obj_type_id WHERE obj_type="products" AND obj_type_id="'.$prod_id.'" AND obj_val2 = "4" AND products_objs.obj_pvp > 0';
		if($prod_obj_id > 0) $sql .=' AND products_objs.obj_id="'.$prod_obj_id.'"';
		$sql .= ' ORDER BY products_objs.obj_pvp ASC LIMIT 1';
		$rs->db_open($db_link,$GLOBALS['DB_name'], $sql);
		if(!$rs->EOF())
		{
			$preco_mercado = $rs->db_data['obj_pvp'];
		}
		$rs->db_close();
		//-------------------------------------------------------------------




		if(defined('CARRINHO_CONF')) $carrinho_conf=unserialize(CARRINHO_CONF); else $carrinho_conf=array();

		$sql='SELECT products_objs.obj_type_id as obj_type_id,products.encomenda_sem_stock,products.margem_fam,products.Sync,obj_iva,obj_iva_txt,obj_pvp,obj_promo,obj_promo_ativo,obj_promo_data,obj_Supplier_ID,
					obj_stock,products.Ref as Cabecalho,products.udtime,obj_destaque,obj_val2,obj_destaque_data,obj_travelsize,obj_novo,obj_novo_data,obj_outlet,outlet_box,
					obj_outlet_data,obj_dia,obj_dia_data,estados_produto,grau,fornecedor_importacao,Promo_descricao,products_objs.Promo_data_fim as Promo_data_fim,products_objs.Promo_data_ini as Promo_data_ini
				FROM products_objs INNER JOIN products on products.ID=products_objs.obj_type_id
				WHERE obj_type="products" AND obj_type_id="'.$prod_id.'"';

				if($revenda > 0)$sql .= ' AND obj_val2="'.$revenda.'" ';
				else $sql .= ' AND (obj_val2=0 OR obj_val2 IS NULL)';
				

		$sql .=' AND products_objs.obj_pvp > 0';
		if($prod_obj_id > 0) $sql .=' AND products_objs.obj_id="'.$prod_obj_id.'"';
		$sql .= ' ORDER BY products_objs.obj_pvp DESC LIMIT 1';

		$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
		if(!$rs->EOF())
		{
			$obj_pvp=$rs->db_data['obj_pvp'];
			$precopromo=$rs->db_data['obj_promo'];
			$obj_Supplier_ID=$rs->db_data['obj_Supplier_ID'];
			$encomenda_sem_stock=$rs->db_data['encomenda_sem_stock'];
			$fornecedor_importacao=$rs->db_data['fornecedor_importacao'];
			$Sync=$rs->db_data['Sync'];
			$obj_iva=0;
			$obj_iva=$rs->db_data['obj_iva'];
			$obj_iva_txt=$rs->db_data['obj_iva_txt'];
			$margem_fam=$rs->db_data['margem_fam'];
			$tem_iva=1;
			// NECESSITAMOS DE SABER O ID_ORG DO PRODUTO
			$sql='select ID_ORG from products_families_obj where ID_PROD='.$prod_id;
			$rs2->db_open($db_link,$GLOBALS['DB_name'],$sql);
			if(!$rs2->EOF())$cat=$rs2->db_data['ID_ORG'];
			$rs2->db_close();

			if($margem_fam==1)
			{
				//ignorar margens
			}
			else
			{
				if($GLOBALS['ignorar_margens']==1 && $Sync==1)
				{
					//Vamos ignorar as margens
				}
				else
				{
					// VAMOS VERIFICAR SE AS FAMILIAS DO PRODUTO TEM ALGUMA MARGEM
					$margem_formula=get_product_families_Margem_formula($cat);
					$margem_formula_value=get_product_margin_formula_value($margem_formula,$obj_pvp);
					if($margem_formula_value!==null) $margem=$margem_formula_value;
					else
					{
						$margem_fam=get_product_families($cat);
						$margem=$margem_fam[0];


						//print_r($margem_fam);
					}
				}
				// VAMOS COLOCAR MARGEM
				$pc=$obj_pvp;

				if(isset($GLOBALS['Margem_personalizada']) && $GLOBALS['Margem_personalizada']==1)
				{
					if($precopromo>0)$precopromo=get_margem_personalizada($precopromo,$obj_Supplier_ID,$fornecedor_importacao,$cat);
					if($obj_pvp>0)$obj_pvp=get_margem_personalizada($obj_pvp,$obj_Supplier_ID,$fornecedor_importacao,$cat);
				}

				if($margem_fam[1]==1)
				{
					if($margem != 0 && $obj_pvp > 0) $obj_pvp=floatval($obj_pvp) + floatval($margem);
					if($margem != 0 && $precopromo > 0) $precopromo=floatval($precopromo) + floatval($margem);
				}
				else
				{
					if($margem != 0 && $obj_pvp > 0) $obj_pvp=$obj_pvp + ($obj_pvp * ($margem / 100));
					if($margem != 0 && $precopromo > 0) $precopromo=$precopromo + ($precopromo * ($margem / 100));
				}
			}


			//Agora vamos arredondar o preço
			//Nao posso arrendondar aqui o valor,so na funcao numberformat ou vai haver duplo arredondamento
			/*
			$r=2;//valor default para arredondamento
			$n_casas=2;//valor default para arredondamento
			if(defined('_ROUND_DOUBLE_') )
			{
				$r=_ROUND_DOUBLE_;
				if(defined('_ROUND_DOUBLE_') && _ROUND_UP_ == 1 ) $obj_pvp = round($obj_pvp);
				else $obj_pvp = ceil($obj_pvp);

				if(defined('_ROUND_ACERTO_') ) $obj_pvp+=_ROUND_ACERTO_;
				if($obj_pvp<0)$obj_pvp=0;

				$precopromo = ceil($precopromo);
				if(defined('_ROUND_ACERTO_') ) $precopromo+=_ROUND_ACERTO_;
				if($precopromo<0)$precopromo=0;
			}*/


			if($Sync==1)
			{
				// NECESSITAMOS DE retirar o stock de reserva ao artigo
				$T=0;
				$sql='select sum(Qtd) as T from reserva_stocks where ID_Produto='.$prod_id;
				$rs2->db_open($db_link,$GLOBALS['DB_name'],$sql);
				if(!$rs2->EOF()) $T=$rs2->db_data['T'];
				$rs2->db_close();
				if($T>0)$rs->db_data['obj_stock']=$rs->db_data['obj_stock']-$T;
			}


			$Promo_descricao=str_replace("< | p>","<p>",$Promo_descricao);
		


			$today=date("U");
			$obj_type_id= 		$rs->db_data['obj_type_id'];
			$obj_stock=			$rs->db_data['obj_stock'];
			$obj_promo_ativo=	$rs->db_data['obj_promo_ativo'];
			$Promo_descricao=	$rs->db_data['Promo_descricao'];
			$Promo_data_fim=	$rs->db_data['Promo_data_fim'];
			$Promo_data_ini=	$rs->db_data['Promo_data_ini'];
			$obj_promo_data=	$rs->db_data['obj_promo_data'];
			$obj_destaque=		$rs->db_data['obj_destaque'];
			$obj_destaque_data=	$rs->db_data['obj_destaque_data'];
			$obj_outlet=		$rs->db_data['obj_outlet'];
			$outlet_box=		$rs->db_data['outlet_box'];
			$obj_outlet_data=	$rs->db_data['obj_outlet_data'];
			$obj_dia=			$rs->db_data['obj_dia'];
			$obj_dia_data=		$rs->db_data['obj_dia_data'];
			$obj_novo=			$rs->db_data['obj_novo'];
			$obj_novo_data=		$rs->db_data['obj_novo_data'];
			$obj_iva_txt=		$rs->db_data['obj_iva_txt'];
			$grau=				$rs->db_data['grau'];
			$udtime=			$rs->db_data['udtime'];
			$obj_promo=			$rs->db_data['obj_promo'];

			/*if($rs->db_data['obj_promo'] > 0
				&& $rs->db_data['obj_promo_ativo']==1
				&& $obj_pvp > $precopromo
				&& strtotime($rs->db_data['Promo_data_ini']) <= strtotime(date('Y-m-d H:i:s'))
				&& strtotime($rs->db_data['Promo_data_fim']) >= strtotime(date('Y-m-d H:i:s'))
				&& strtotime($rs->db_data['Promo_data_fim']) >= strtotime($rs->db_data['Promo_data_ini'])
				&& $rs->db_data['Promo_data_fim']!='0000-00-00'
				&& $rs->db_data['Promo_data_ini']!='0000-00-00')*/

			if( $rs->db_data['obj_promo'] > 0 && $rs->db_data['obj_promo_ativo']==1
				&& $precopromo > 0
				&& strtotime($rs->db_data['Promo_data_ini']) <= strtotime(date('Y-m-d H:i:s'))
				&& strtotime($rs->db_data['Promo_data_fim']) >= strtotime(date('Y-m-d H:i:s'))
				&& strtotime($rs->db_data['Promo_data_fim']) >= strtotime($rs->db_data['Promo_data_ini'])
				&& $rs->db_data['Promo_data_fim']!='0000-00-00'
				&& $rs->db_data['Promo_data_ini']!='0000-00-00')
			{
				if($obj=='')$rs->db_close();
				return array(
				'obj_type_id' 		=> $obj_type_id,
				'Medida' 			=> '',
				'Sync' 				=> $Sync,
				'PVP' 				=> $precopromo,
				'PC' 				=> $pc,
				'PRECO_ANTIGO' 		=> $obj_pvp,
				'STOCK' 			=> $obj_stock,
				'PROMOCAO' 			=> $obj_promo_ativo,
				'obj_promo' 		=> $obj_promo,
				'Promo_descricao' 	=> $Promo_descricao,
				'Promo_data_fim' 	=> $Promo_data_fim,
				'Promo_data_ini' 	=> $Promo_data_ini,
				'PROMOCAODT' 		=> $obj_promo_data,
				'DESTAQUE' 			=> $obj_destaque,
				'DESTAQUEDT' 		=> $obj_destaque_data,
				'OUTLET' 			=> $obj_outlet,
				'OUTLETDT' 			=> $obj_outlet_data,
				'OUTLETBOX' 		=> $outlet_box,
				'DODIA' 			=> $obj_dia,
				'DODIADT' 			=> $obj_dia_data,
				'NOVO' 				=> $obj_novo,
				'NOVODT' 			=> $obj_novo_data,
				'IVA' 				=> ($obj_iva ==0 ? $GLOBALS['iva_default'] : $obj_iva),
				'IVA_TXT' 			=> $obj_iva_txt,
				'GRAU' 				=> $grau,
				'UDTIME'			=> $udtime,
				'TEM_IVA' 			=> $tem_iva,
				'PODE_ENCOMENDAR' 	=> (isset($carrinho_conf['tem_enc_sem_stock']) && $carrinho_conf['tem_enc_sem_stock'] == 1 ? 1 : $encomenda_sem_stock),
				'fornecedor_importacao' =>$fornecedor_importacao
				);
			}
			elseif($preco_mercado > 0 && $preco_mercado > $obj_pvp)
			{
				if($obj=='')$rs->db_close();
				return array(
				'obj_type_id' 		=> $obj_type_id,
				'Medida' 			=> '',
				'Sync' 				=> $Sync,
				'PC' 				=> $pc,
				'PVP' 				=> $obj_pvp,
				'PRECO_ANTIGO' 		=> $obj_promo_ativo,
				'STOCK' 			=> $obj_stock,
				'PROMOCAO' 			=> 2,
				'obj_promo' 		=> $obj_promo,
				'Promo_descricao' 	=> $Promo_descricao,
				'Promo_data_fim' 	=> $Promo_data_fim,
				'Promo_data_ini' 	=> $Promo_data_ini,
				'PROMOCAODT' 		=> $obj_promo_data,
				'DESTAQUE' 			=> $obj_destaque,
				'DESTAQUEDT' 		=> $obj_destaque_data,
				'OUTLET' 			=> $obj_outlet,
				'OUTLETDT' 			=> $obj_outlet_data,
				'OUTLETBOX' 		=> $outlet_box,
				'DODIA' 			=> $obj_dia,
				'DODIADT' 			=> $obj_dia_data,
				'NOVO' 				=> $obj_novo,
				'NOVODT' 			=> $obj_novo_data,
				'IVA' 				=> ($obj_iva ==0 ? $GLOBALS['iva_default'] : $obj_iva),
				'IVA_TXT' 			=> $obj_iva_txt,
				'GRAU' 				=> $grau,
				'UDTIME'			=> $udtime,
				'TEM_IVA' 			=> $tem_iva,
				'PODE_ENCOMENDAR' 	=> (isset($carrinho_conf['tem_enc_sem_stock']) && $carrinho_conf['tem_enc_sem_stock'] == 1 ? 1 : $encomenda_sem_stock),
				'fornecedor_importacao' =>$fornecedor_importacao
				);
			}
			/*elseif(substr($obj['Ref'], 0,3)=='REP'){
				return array(
				'obj_type_id' 		=> $obj_type_id,
				'Medida' 			=> '',
				'Sync' 				=> $Sync,
				'PC' 				=> $pc,
				'PVP' 				=> 0,
				'PRECO_ANTIGO' 		=> 0,
				'STOCK' 			=> $obj_stock,
				'PROMOCAO' 			=> $obj_promo_ativo, //estava a 1
				'obj_promo' 		=> $obj_promo,
				'Promo_descricao' 	=> $Promo_descricao,
				'Promo_data_fim' 	=> $Promo_data_fim,
				'Promo_data_ini' 	=> $Promo_data_ini,
				'PROMOCAODT' 		=> $obj_promo_data,
				'DESTAQUE' 			=> $obj_destaque,
				'DESTAQUEDT' 		=> $obj_destaque_data,
				'OUTLET' 			=> $obj_outlet,
				'OUTLETDT' 			=> $obj_outlet_data,
				'OUTLETBOX' 		=> $outlet_box,
				'DODIA' 			=> $obj_dia,
				'DODIADT' 			=> $obj_dia_data,
				'NOVO' 				=> $obj_novo,
				'NOVODT' 			=> $obj_novo_data,
				'IVA' 				=> ($obj_iva ==0 ? $GLOBALS['iva_default'] : $obj_iva),
				'IVA_TXT' 			=> $obj_iva_txt,
				'GRAU' 				=> $grau,
				'UDTIME'			=> $udtime,
				'TEM_IVA' 			=> $tem_iva,
				'PODE_ENCOMENDAR' 	=> (isset($carrinho_conf['tem_enc_sem_stock']) && $carrinho_conf['tem_enc_sem_stock'] == 1 ? 1 : $encomenda_sem_stock),
				'fornecedor_importacao' =>$fornecedor_importacao
				);
			}*/
			else
			{
				if($obj=='')$rs->db_close();
				return array(
				'obj_type_id' 		=> $obj_type_id,
				'Medida' 			=> '',
				'Sync' 				=> $Sync,
				'PC' 				=> $pc,
				'PVP' 				=> $obj_pvp,
				'PRECO_ANTIGO' 		=> $obj_promo_ativo,
				'STOCK' 			=> $obj_stock,
				'PROMOCAO' 			=> $obj_promo_ativo, //estava a 1
				'obj_promo' 		=> $obj_promo,
				'Promo_descricao' 	=> $Promo_descricao,
				'Promo_data_fim' 	=> $Promo_data_fim,
				'Promo_data_ini' 	=> $Promo_data_ini,
				'PROMOCAODT' 		=> $obj_promo_data,
				'DESTAQUE' 			=> $obj_destaque,
				'DESTAQUEDT' 		=> $obj_destaque_data,
				'OUTLET' 			=> $obj_outlet,
				'OUTLETDT' 			=> $obj_outlet_data,
				'OUTLETBOX' 		=> $outlet_box,
				'DODIA' 			=> $obj_dia,
				'DODIADT' 			=> $obj_dia_data,
				'NOVO' 				=> $obj_novo,
				'NOVODT' 			=> $obj_novo_data,
				'IVA' 				=> ($obj_iva ==0 ? $GLOBALS['iva_default'] : $obj_iva),
				'IVA_TXT' 			=> $obj_iva_txt,
				'GRAU' 				=> $grau,
				'UDTIME'			=> $udtime,
				'TEM_IVA' 			=> $tem_iva,
				'PODE_ENCOMENDAR' 	=> (isset($carrinho_conf['tem_enc_sem_stock']) && $carrinho_conf['tem_enc_sem_stock'] == 1 ? 1 : $encomenda_sem_stock),
				'fornecedor_importacao' =>$fornecedor_importacao
				);
			}
		}
		if($obj=='')$rs->db_close();
	}
	return;
}

function buscacategoriaTipoeNomex($ID)
{
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$dados=new DataObj;
	$sql="SELECT filters_categories.category_type,filters_categories_lang.category_name FROM filters_categories INNER JOIN filters_categories_lang ON filters_categories_lang.category_id=filters_categories.category_id  WHERE filters_categories.category_id='$ID'";
	$dados->db_open($db_link,$GLOBALS['DB_name'],$sql);
	if(!$dados->EOF())
	{
		$category_type=$dados->db_data["category_type"];
		$category_name=$dados->db_data["category_name"];
	}
	$dados->db_close();
	return array('category_type'=>$category_type,'category_name'=>$category_name);
}

function buscavalorfiltrosx($ID)
{
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$dados=new DataObj;
	$sql="SELECT values_name from filters_values_lang where values_id='$ID' and values_lang='".$GLOBALS['site_lang']."'";
	$dados->db_open($db_link,$GLOBALS['DB_name'],$sql);
	if(!$dados->EOF())
	{
		$values_name=$dados->db_data["values_name"];
	}
	$dados->db_close();
	return $values_name;
}

function get_produtc_brand($marca_id)
{
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;
	if(isset($marca_id) && $marca_id > 0)
	{
		//$sql='SELECT brands.ID,brands.img_ext,brands.img_ext2,brands_lang.Nome FROM brands INNER JOIN brands_lang on brands_lang.ID_PAI=brands.ID WHERE brands.ID="'.$marca_id.'" AND brands.Visivel=1';
		$sql='SELECT brands.ID,brands.img_ext,brands.img_ext2,brands_lang.Nome FROM brands INNER JOIN brands_lang on brands_lang.ID_PAI=brands.ID WHERE brands.ID="'.$marca_id.'"';
		$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
		if(!$rs->EOF())
		{
			$I1=$rs->db_data['img_ext'];
			$I2=$rs->db_data['img_ext2'];
			if($I1 != '')
			{
				$img_file='files/brands/'.$marca_id.'_1.'.$I1;
				$img_file_new='files/brands/'.$marca_id.'_1_new.'.$I1;
				$img_file_shop='files/brands/'.$marca_id.'_1_shop.'.$I1;
				$img_file_small='files/brands/'.$marca_id.'_1_small.'.$I1;
				if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file)) $original=date('Y-m-d H:i:s',dotbright_image_mtime($GLOBALS['site_shell_url'].$img_file)); // data/hora em que foi modificada/criada
				if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_new)) $new=date('Y-m-d H:i:s',dotbright_image_mtime($GLOBALS['site_shell_url'].$img_file_new)); //data/hora em que foi modificada/criada
				if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_shop)) $shop=date('Y-m-d H:i:s',dotbright_image_mtime($GLOBALS['site_shell_url'].$img_file_shop)); //data/hora em que foi modificada/criada
				if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_small)) $small=date('Y-m-d H:i:s',dotbright_image_mtime($GLOBALS['site_shell_url'].$img_file_small)); //data/hora em que foi modificada/criada
				if((!dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_new) || $new<$original) && dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file)){if(redimencionaImagem($GLOBALS['site_shell_url'],$img_file,84,46,$img_file_new)){ cropImage($GLOBALS['site_shell_url'].$img_file_new,$GLOBALS['site_shell_url'].$img_file_new,84,46,0,'#FFFFFF');}}
				if((!dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_shop) || $shop<$original) && dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file)){if(redimencionaImagem($GLOBALS['site_shell_url'],$img_file,154,60,$img_file_shop)){ cropImage($GLOBALS['site_shell_url'].$img_file_shop,$GLOBALS['site_shell_url'].$img_file_shop,154,60,0,'#FFFFFF');}}
				if((!dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_small) || $small<$original) && dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file)){if(redimencionaImagem($GLOBALS['site_shell_url'],$img_file,80,43,$img_file_small)){ cropImage($GLOBALS['site_shell_url'].$img_file_small,$GLOBALS['site_shell_url'].$img_file_small,80,43,0,'#FFFFFF');}}
				$vec['marca_img']=$img_file;
				$vec['marca_img_new']=$img_file_new;
				$vec['marca_img_shop']=$img_file_shop;
				$vec['marca_img_small']=$img_file_small;
				$vec['marca_nome']=$rs->db_data['Nome'];
				$vec['marca_id']=$rs->db_data['ID'];
				if($I2 != '')
				{
					$img_file='files/brands/'.$marca_id.'_2.'.$I2;
					$img_file_new='files/brands/'.$marca_id.'_2_new.'.$I2;
					$img_file_shop='files/brands/'.$marca_id.'_2_shop.'.$I2;
					$img_file_small='files/brands/'.$marca_id.'_2_small.'.$I2;
					if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file)) $original=date('Y-m-d H:i:s',dotbright_image_mtime($GLOBALS['site_shell_url'].$img_file)); // data/hora em que foi modificada/criada
					if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_new)) $new=date('Y-m-d H:i:s',dotbright_image_mtime($GLOBALS['site_shell_url'].$img_file_new)); //data/hora em que foi modificada/criada
					if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_shop)) $shop=date('Y-m-d H:i:s',dotbright_image_mtime($GLOBALS['site_shell_url'].$img_file_shop)); //data/hora em que foi modificada/criada
					if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_small)) $small=date('Y-m-d H:i:s',dotbright_image_mtime($GLOBALS['site_shell_url'].$img_file_small)); //data/hora em que foi modificada/criada
					if((!dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_new) || $new<$original) && dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file)){if(redimencionaImagem($GLOBALS['site_shell_url'],$img_file,84,46,$img_file_new)){ cropImage($GLOBALS['site_shell_url'].$img_file_new,$GLOBALS['site_shell_url'].$img_file_new,84,46,0,'#FFFFFF');}}
					if((!dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_shop) || $shop<$original) && dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file)){if(redimencionaImagem($GLOBALS['site_shell_url'],$img_file,154,60,$img_file_shop)){ cropImage($GLOBALS['site_shell_url'].$img_file_shop,$GLOBALS['site_shell_url'].$img_file_shop,154,60,0,'#FFFFFF');}}
					if((!dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_small) || $small<$original) && dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file)){if(redimencionaImagem($GLOBALS['site_shell_url'],$img_file,80,43,$img_file_small)){ cropImage($GLOBALS['site_shell_url'].$img_file_small,$GLOBALS['site_shell_url'].$img_file_small,80,43,0,'#FFFFFF');}}
					$vec['marca_img2']=$img_file;
					$vec['marca_img_new2']=$img_file_new;
					$vec['marca_img_shop2']=$img_file_shop;
					$vec['marca_img_small2']=$img_file_small;
					$vec['marca_nome2']=$rs->db_data['Nome'];
					$vec['marca_id2']=$rs->db_data['ID'];
				}
				else // CASO NAO EXISTA IMAGEM _2 VAMOS COLOCAR A IMAGEM DA PRIMEIRA
				{
					$vec['marca_img2']=$img_file;
					$vec['marca_img_new2']=$img_file_new;
					$vec['marca_img_shop2']=$img_file_shop;
					$vec['marca_img_small2']=$img_file_small;
					$vec['marca_nome2']=$rs->db_data['Nome'];
					$vec['marca_id2']=$rs->db_data['ID'];
				}
			}
			else
			{
				$vec['marca_img']='';
				$vec['marca_img_new']='';
				$vec['marca_img_shop']='';
				$vec['marca_img_small']='';
				$vec['marca_nome']=$rs->db_data['Nome'];
				$vec['marca_id']=$rs->db_data['ID'];
				$vec['marca_img2']='';
				$vec['marca_img_new2']='';
				$vec['marca_img_shop2']='';
				$vec['marca_img_small2']='';
				$vec['marca_nome2']=$rs->db_data['Nome'];
				$vec['marca_id2']=$rs->db_data['ID'];
			}
			$rs->db_close();
			return $vec;
		}
		else
		{
			$vec['marca_img']='';
			$vec['marca_img_new']='';
			$vec['marca_img_shop']='';
			$vec['marca_img_small']='';
			$vec['marca_nome']='';
			$vec['marca_id']=0;
			$vec['marca_img2']='';
			$vec['marca_img_new2']='';
			$vec['marca_img_shop2']='';
			$vec['marca_img_small2']='';
			$vec['marca_nome2']='';
			$vec['marca_id2']=0;
			$rs->db_close();
			return $vec;
		}
	}
	return;
}

function show_random_novidade()
{
	//esta funcao ja nao e usada a muito tempo
	/*global $m_http_url_images,$site_http_url,$tema,$site_lang;
    $db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
    $rs=new Dataobj;
	$sql='SELECT P.ID,P.Ref,P.ID_ORG,P.img_ext,P.Preco1,PL.Cabecalho FROM products AS P INNER JOIN products_lang AS PL ON PL.ID_PAI=P.ID WHERE P.Aproved=1 AND P.Home=1 AND PL.Lang="'.$GLOBALS['site_lang'].'" ORDER by RAND() LIMIT 1';
	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	if(!$rs->EOF())
	{
		$prods=\get_produtc_info($rs->db_data['ID']);
		if($prods != NULL)
		{
			$imgs=$prods['prd_imgs_small'];
			$precos=$prods['prod_precos'];
			$url=$site_http_url.\seo2(2,$GLOBALS['products_page_id'],$prods,$GLOBALS['site_lang']);
			echo '<a href="'.$url.'">';
			if($imgs['img1_med']) $img=$imgs['img1_med'];
			elseif($imgs['img2_med']) $img=$imgs['img2_med'];
			elseif($imgs['img3_med']) $img=$imgs['img3_med'];
			elseif($imgs['img4_med']) $img=$imgs['img4_med'];
			else $img=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo.png';
			echo '<img src="'.$img.'" width="160" title="'.$rs->db_data['Cabecalho'].'"><br>';
			echo  htmlentities_memo(stripslashes($rs->db_data['Cabecalho'])).'<br>';
			echo '<div class="newProdsideboxPrice">'.numberformat($rs->db_data['Preco1']).'</div></a>';
		}
	}
	$rs->db_close();*/
}

function show_breadcrums()
{
	$zindex=50;
	$zindex2=51;
	$result='';
	$nome_prod='';
	$str='';
	$p_seo='#';
	if(isset($_GET['cat'])) $cat=$_GET['cat'];
	global $m_http_url_images,$site_http_url,$tema,$site_lang;
    $db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
    $rs=new Dataobj;
	if(isset($_GET['prod_id']) && $_GET['prod_id'] > 0)
	{
		$sql='select pfo.ID_ORG,(pl.Cabecalho) from products p inner join products_lang pl on p.ID=pl.ID_PAI inner join products_families_obj pfo on p.ID=pfo.ID_PROD where pl.Lang="'.$GLOBALS['site_lang'].'" and p.ID='.$_GET['prod_id'];
		$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
		if(!$rs->EOF())
		{
			$cat=$rs->db_data['ID_ORG'];
			$nome_prod=htmlentities_memo(stripslashes(upper_str($rs->db_data['Cabecalho'])));
		}
		$rs->db_close();
	}
	if(isset($cat) && $cat > 0)
	{
		$p_seo=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$cat,$cat,1);
		$sql='select pf.ID,Pf.ID_ORG,(pfl.Cabecalho),(pfl.Cabecalho2) from products_families pf inner join products_families_lang pfl on (pf.ID=pfl.ID_PAI) where pf.visivel=1 and pfl.Lang="'.$GLOBALS['site_lang'].'" and pf.ID='.$cat;
		$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
		if(!$rs->EOF())
		{
			$p_seo=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$rs->db_data['ID'],$rs->db_data['ID'],1);
			if($rs->db_data['ID_ORG'] != 0)
			{
				$str=show_breadcrums_recursivo($rs->db_data['ID_ORG'],($zindex+1));
				$zindex2=$zindex++;

				if($nome_prod) $cl='';
				else $cl='color:#000';

				if($rs->db_data['Cabecalho2'])  $str .= '<li>  <a onclick="clean_filters();return true;" style="z-index:'.$zindex2.';'.$cl.'" href="'.$p_seo.'">'.htmlentities_memo(stripslashes(upper_str($rs->db_data['Cabecalho2']))).'</a></li>';
				else $str .= '<li>  <a onclick="clean_filters();return true;" style="z-index:'.$zindex2.';'.$cl.'" href="'.$p_seo.'">'.htmlentities_memo(stripslashes(upper_str($rs->db_data['Cabecalho']))).'</a></li>';
			}
			else
			{
				$zindex2=$zindex++;

				if($rs->db_data['Cabecalho2'])  $str='<li>  <a onclick="clean_filters();return true;" class="HvRm" href="'.$p_seo.'">'.htmlentities_memo(stripslashes(upper_str($rs->db_data['Cabecalho2']))).'</a></li>';
				else $str='<li>  <a onclick="clean_filters();return true;" style="z-index:'.$zindex2.'" href="'.$p_seo.'">'.htmlentities_memo(stripslashes(upper_str($rs->db_data['Cabecalho']))).'</a></li>';
			}
		}
		$rs->db_close();
	}
	$result.='<ul class="crumbs">';
	$result.='<li class="first"><a href="'.$GLOBALS['site_http_url'].'" style="z-index:52"><span></span>'.upper_str(getLinguas('key_inicio',$GLOBALS['site_lang'])).'</a></li>';
	$result.=$str;
	if(isset($_GET['tipo_catalogo']) && $_GET['tipo_catalogo']=='novidade') $result.='<li>  <a style="z-index:'.($zindex2).'" href="'.$p_seo.'" onclick="return false;">NOVIDADES</a></li>';
	elseif(isset($_GET['tipo_catalogo']) && $_GET['tipo_catalogo']=='promocao') $result.='<li>  <a style="z-index:'.($zindex2).'" href="'.$p_seo.'" onclick="return false;">PROMOÇÔES</a></li>';
	if($nome_prod) $result.= '<li>  <a style="z-index:'.($zindex2-1).';color:#000" href="'.$p_seo.'" onclick="return false;">'.$nome_prod.'</a></li>';
	$result.='</ul>';

	return $result;
}

function show_breadcrums_recursivo($id_pai,$zindex=0)
{
	global $m_http_url_images,$site_http_url,$tema,$site_lang;
    $db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
    $rs=new Dataobj;
    $sql='select pf.ID,Pf.ID_ORG,(pfl.Cabecalho) from products_families pf inner join products_families_lang pfl on (pf.ID=pfl.ID_PAI) where pf.visivel=1  and pfl.Lang="'.$GLOBALS['site_lang'].'" and pf.ID='.$id_pai;
    $rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
    $str='';
    if(!$rs->EOF())
	{
    	$p_seo=str_replace($GLOBALS['site_http_url'],'',seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$rs->db_data['ID'],$rs->db_data['ID'],1));
		if($rs->db_data['ID_ORG']==0)
		{
			$str='<li><a onclick="clean_filters();return true;" style="z-index:'.$zindex.'" href="'.$p_seo.'">'.htmlentities_memo(stripslashes(upper_str($rs->db_data['Cabecalho'],"UTF-8"))).'</a></li>'.$str;
		}
		else
		{
			$str='<li><a onclick="clean_filters();return true;" style="z-index:'.$zindex.'" href="'.$p_seo.'">'.htmlentities_memo(stripslashes(upper_str($rs->db_data['Cabecalho'],"UTF-8"))).'</a></li>'.$str;
			$str=show_breadcrums_recursivo($rs->db_data['ID_ORG'],($zindex+1)).$str;
		}
	}
	$rs->db_close();
	return $str;
}

function upper_str($str)
{
  $str=str_replace("á","Á",$str); $str=str_replace("à","À",$str); $str=str_replace("â","Â",$str); $str=str_replace("ä","Ä",$str); $str=str_replace("ã","Ã",$str);
  $str=str_replace("é","É",$str); $str=str_replace("è","È",$str); $str=str_replace("ê","Ê",$str); $str=str_replace("ë","Ë",$str);
  $str=str_replace("í","Í",$str); $str=str_replace("ì","Ì",$str); $str=str_replace("î","Î",$str); $str=str_replace("ï","Ï",$str);
  $str=str_replace("ó","Ó",$str); $str=str_replace("ò","Ò",$str); $str=str_replace("ô","Ô",$str); $str=str_replace("ö","Ö",$str); $str=str_replace("õ","Õ",$str);
  $str=str_replace("ú","Ú",$str); $str=str_replace("ù","Ù",$str); $str=str_replace("û","Û",$str); $str=str_replace("ü","Ü",$str);
  $str=str_replace("ç","Ç",$str);
  return mb_strtoupper(ltrim(rtrim($str)));
}

function encontraFamiliasRecursiva($id_pai)
{
    $db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
    $rs=new Dataobj;
    $sql='select pf.ID,pf.ID_ORG,pfl.Cabecalho from products_families pf inner join products_families_lang pfl on (pf.ID=pfl.ID_PAI) where pf.visivel=1 and Download=1 and pfl.Lang="'.$GLOBALS['site_lang'].'" and pf.ID_ORG='.$id_pai;
    $rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
    $i=0;
    while(!$rs->EOF())
    {
    	$array[$i]=encontraFamiliasRecursiva($rs->db_data['ID']);
    	$i++;
    	$array[$i]=$rs->db_data['ID'];
    	$i++;
    	$rs->db_next();
    }
    $rs->db_close();
    return $array;
}

function getProdutosDestaques()
{
	global $site_shell_url,$site_http_url_internal;
	$produtos_destaque='files/produtosdestaque_'.$_SESSION['ss_site_country'].'.php';
	$time_expire=time() - 1*60*1; // Expire Time (1 minute)
	$gerar='';
	if(isset($_GET['gerar']))$gerar=$_GET['gerar'];
	if(($gerar!=1) && ((stream_resolve_include_path($site_shell_url.$produtos_destaque) && filectime($site_shell_url.$produtos_destaque) > $time_expire))) echo file_get_contents2($site_http_url_internal.$produtos_destaque);
	else
	{
		$destaques=GERA_PRODUTOS_DESTAQUES();
		$file_name=$site_shell_url.$produtos_destaque;
		$fh=fopen($file_name,'w');
		fwrite($fh,$destaques);
		fclose($fh);
		if(stream_resolve_include_path($site_shell_url.$produtos_destaque)) echo file_get_contents2($site_http_url_internal.$produtos_destaque);
	}
}

function getProdutosPromocoes()
{
	global $site_shell_url,$site_http_url_internal;
	$produtos_promocao='files/produtospromocao_'.$_SESSION['ss_site_country'].'.php';
	$time_expire=time() - 1*60*1; // Expire Time (1 minute)
	if(stream_resolve_include_path($site_shell_url.$produtos_promocao) && filectime($site_shell_url.$produtos_promocao) > $time_expire)  echo file_get_contents2($site_http_url_internal.$produtos_promocao);
	else
	{
		$promos=GERA_PRODUTOS_PROMOCAO();
		$file_name=$site_shell_url.$produtos_promocao;
		$fh=fopen($file_name,'w');
		fwrite($fh,$promos);
		fclose($fh);
		if(stream_resolve_include_path($site_shell_url.$produtos_promocao)) echo file_get_contents2($site_http_url_internal.$produtos_promocao);
	}
}

function getListaMarcas()
{
	global $site_shell_url,$site_http_url_internal;
	$marcas_destaque='files/marcasdestaque.php';
	$time_expire=time() - 1*60*1; // Expire Time (1 minute)
	if(isset($_GET['gerar']))$gerar=$_GET['gerar'];
	if(($gerar!=1) && ((stream_resolve_include_path($site_shell_url.$marcas_destaque) && filectime($site_shell_url.$marcas_destaque) > $time_expire))) echo file_get_contents2($site_http_url_internal.$marcas_destaque);
	else
	{
		$marcas=GERA_MARCAS_DESTAQUE();
		$file_name=$site_shell_url.$marcas_destaque;
		$fh=fopen($file_name,'w');
		fwrite($fh,$marcas);
		fclose($fh);
		if(stream_resolve_include_path($site_shell_url.$marcas_destaque)) echo file_get_contents2($site_http_url_internal.$marcas_destaque);
	}
}

function GERA_MARCAS_DESTAQUE()
{
	$result=''; $i=0;
	global $site_http_url,$site_lang;
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;
	$sql='SELECT brands.ID,brands.img_ext,brands_lang.Nome
			FROM brands INNER JOIN brands_lang ON brands.ID=brands_lang.ID_PAI INNER JOIN products p ON p.ID_Marca=brands.ID
			WHERE brands.Visivel=1 AND p.Aproved=1
			GROUP BY brands.ID
			ORDER BY brands.Ordem ASC';
	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	if(!$rs->EOF())
	{
		//$result .= '<link rel="stylesheet"  href="external_api/brandslider/css/lightslider.css?dp='.dumpcache.'" />';
		//$result .= '<script src="external_api/brandslider/js/lightslider.js?dp='.dumpcache.'"></script>';

		//http://sachinchoolur.github.io/lightslider/settings.html

		$result .= '<script>$(document).ready(function() {$("#brand-slider").lightSlider({loop:true,keyPress:true,controls:true,pager:false,gallery:false,autoWidth:true,slideMargin:20});});</script>';
		$result .= '<div class="brand-item" style="padding-left: 15px; padding-right: 15px; max-width: 1650px !important;"><ul id="brand-slider" class="brand-slider">';
		while(!$rs->EOF())
		{
			$i++;
			$brand_id=$rs->db_data['ID'];
			$brand_name=$rs->db_data['Nome'];
			$I1=$rs->db_data['img_ext'];
			$img_file=$img_file_new=$img_file_shop=$img_file_small='';
			if($I1 != '')
			{
				$img_file='files/brands/'.$brand_id.'_1.'.$I1;
				$img_file_new='files/brands/'.$brand_id.'_1_new.'.$I1;
				$img_file_shop='files/brands/'.$brand_id.'_1_shop.'.$I1;
				$img_file_small='files/brands/'.$brand_id.'_1_small.'.$I1;
				$img_file_new_cache='files/brands/'.$brand_id.'_1_new_v_'.dotbright_image_mtime($GLOBALS['site_shell_url'].$img_file_new).'.'.$I1;
				if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file)) $original=date('Y-m-d H:i:s',dotbright_image_mtime($GLOBALS['site_shell_url'].$img_file)); // data/hora em que foi modificada/criada
				if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_new)) $new=date('Y-m-d H:i:s',dotbright_image_mtime($GLOBALS['site_shell_url'].$img_file_new)); //data/hora em que foi modificada/criada
				if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_shop)) $shop=date('Y-m-d H:i:s',dotbright_image_mtime($GLOBALS['site_shell_url'].$img_file_shop)); //data/hora em que foi modificada/criada
				if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_small)) $small=date('Y-m-d H:i:s',dotbright_image_mtime($GLOBALS['site_shell_url'].$img_file_small)); //data/hora em que foi modificada/criada
				if((!dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_new) || $new<$original) && dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file))
				{
					geraimagens($img_file,$img_file_new,70,27,0,0,1);
					list($width,$height)=dotbright_files_image_size($GLOBALS['site_shell_url'].$img_file_new);
					cropImage($GLOBALS['site_shell_url'].$img_file_new,$GLOBALS['site_shell_url'].$img_file_new,$width,27,0,'#FFFFFF');
				}
				if((!dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_shop) || $shop<$original) && dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file)){if(redimencionaImagem($GLOBALS['site_shell_url'],$img_file,154,60,$img_file_shop)){ cropImage($GLOBALS['site_shell_url'].$img_file_shop,$GLOBALS['site_shell_url'].$img_file_shop,154,60,0,'#FFFFFF');}}
				if((!dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_small) || $small<$original) && dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file)){if(redimencionaImagem($GLOBALS['site_shell_url'],$img_file,80,43,$img_file_small)){ cropImage($GLOBALS['site_shell_url'].$img_file_small,$GLOBALS['site_shell_url'].$img_file_small,80,43,0,'#FFFFFF');}}
				if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_new) && $img_file_new !='' && $I1!='')
				{
					$img_file_new=$img_file_new_cache;
					$url= 'm-'.$brand_id.'-'. str_replace(' ','',limpa_special($brand_name)).'-'.$site_lang;
					if($img_file_new)
					{
						$result .= '<li><a href="'.$url.'" title="'.$brand_name.'"><img src="'.$img_file_new.'" id="mc_imgload'.$i.'" datasrc="'.$img_file_new.'" alt="'.addslashes($brand_name).'" /></a></li>';
					}
				}
			}
			$rs->db_next();
		}
		$result.= '</ul></div>';
	}
	$rs->db_close();
	// return $result;
}

function blocohtml(){
}

function GERA_PRODUTO_PROMOCAO()
{
	//funcao usada para destaque de 1 artigo na home page
	global $site_http_url;
	$s='';
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;
	$sql='SELECT P.ID
			FROM products AS P INNER JOIN brands AS BR ON P.ID_Marca=BR.ID INNER JOIN products_objs as PO ON PO.obj_type_id=P.ID
			WHERE
				P.Aproved=1
				AND PO.obj_promo_ativo=1
				AND BR.Visivel=1
				AND P.img_ext<>""
				AND P.Ref<>""
			ORDER BY RAND()
			LIMIT 1';
	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	if(!$rs->EOF())
	{
		$prod_id=$rs->db_data['ID'];
		$prods=\get_produtc_info($prod_id);
		if($prods != NULL)
		{
			$s.= mostra_bloco($prods);
		}
	}
	$rs->db_close();

	$rs=new DataObj;
	$sql='SELECT P.ID
			FROM products AS P INNER JOIN brands AS BR ON P.ID_Marca=BR.ID INNER JOIN products_objs as PO ON PO.obj_type_id=P.ID
			WHERE
				P.Aproved=1
				AND PO.obj_outlet=1
				AND BR.Visivel=1
				AND P.img_ext<>""
				AND P.Ref<>""
			ORDER BY RAND()
			LIMIT 1';
	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	if(!$rs->EOF())
	{
		$prod_id=$rs->db_data['ID'];
		$prods=\get_produtc_info($prod_id);
		if($prods != NULL)
		{
			$s .= mostra_bloco($prods);
		}
	}
	$rs->db_close();
	return $s;
}

function GERA_PRODUTOS_PROMOCAO($limit=0,$order='')
{
	global $site_http_url;

	$result='';
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;
	$sql='
			SELECT P.ID
			FROM
				products AS P
				INNER JOIN brands AS BR ON P.ID_Marca=BR.ID
				INNER JOIN products_objs as PO ON PO.obj_type_id=P.ID
				INNER JOIN products_promocoes as PP ON PP.ID_Prod=P.ID
			WHERE
				P.Aproved=1 AND PO.obj_promo_ativo=1 AND BR.Visivel=1
			ORDER BY
				PP.Ordem ASC
			';
	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	if(!$rs->EOF())
	{
		$numprods=$rs->db_rows;
		if($numprods>4) $style="display:block";
		else $style="display:none";
		$result.= '<div class="promowrap">';
		$result.= '<div class="promo_header">';
		$result.= '<div class="promo_title HvLt">'.getLinguas('key_promocoes',$GLOBALS['site_lang']).'</div>';
		$url=$site_http_url.\seo2(5,$GLOBALS['catalog_page_id'],$GLOBALS['catalog_page_id'],$GLOBALS['site_lang']);
		$result.= '<div class="promo_more HvLt"><a href="'.$url.'">'.getLinguas('key_catalogo_ver_promocoes',$GLOBALS['site_lang']).'</a>&nbsp;<img src="layout/'.PROJETO.'/images/grey_triangle.png" alt=""></div>';
		//$result.= '<div class="promo_more HvLt"><a href="'.str_replace($site_http_url,'',seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&tipo_catalogo=promocao','promocao',5)).'">'.getLinguas('key_catalogo_ver_promocoes',$GLOBALS['site_lang']).'</a>&nbsp;<img src="layout/'.PROJETO.'/images/grey_triangle.png" alt=""></div>';
		$result.= '</div>';
		$result.= '<div class="c"></div>';
		$result.= '<div class="arrowleft" style="'.$style.'" onclick="promocoesScrollLeft();"><img src="layout/'.PROJETO.'/images/homesliderarrowleft.png" alt=""></div>';
		$result.= '<div id="promocoesinnerslider" class="slidwrap">';
		$result.= '<div class="slidinner">';
		while(!$rs->EOF())
		{
			$prod_id=$rs->db_data['ID'];
			$prods=\get_produtc_info($prod_id);
			//$url=str_replace($site_http_url,'',seo('pages.php?page_id='.$GLOBALS['products_page_id'],$prods['prod_id'],2));
			$url=str_replace($site_http_url,'',seo_prod($prods));

			$result.= '<div class="prd_prom">';
			if($dados=\cliente_logado())
			{
				$cli_id=$dados['cli_id'];
				if(isProductInWishList($prods['prod_id'],$cli_id))
				{
					$result.= '<div class="prd_whish produtowl'.$prods['prod_id'].'" title="O produto foi removido com sucesso dos seus favoritos" onclick="removeProductWishList(\''.$prods['prod_id'].'\',\'0\');"><img src="layout/'.PROJETO.'/images/wishlist_added.png" alt=""></div>';
				}
				else
				{
					$result.= '<div class="prd_whish produtowl'.$prods['prod_id'].'" title="O produto foi adicionado com sucesso aos seus favoritos" onclick="addProductWishList(\''.$prods['prod_id'].'\');"><img src="layout/'.PROJETO.'/images/wishlist.png" alt=""></div>';
				}
			}
			else
			{
				if(isset($_SESSION['wish_prod_'.$prods['prod_id']]))
				{
					$result.= '<div class="prd_whish produtowl'.$prods['prod_id'].'" title="O produto foi removido com sucesso dos seus favoritos" onclick="removeProductWishListOffline(\''.$prods['prod_id'].'\',\'0\');"><img src="layout/'.PROJETO.'/images/wishlist_added.png" alt=""></div>';
				}
				else
				{
					$result.= '<div class="prd_whish produtowl'.$prods['prod_id'].'" title="O produto foi adicionado com sucesso aos seus favoritos" onclick="addProductWishListOffline(\''.$prods['prod_id'].'\');"><img src="layout/'.PROJETO.'/images/wishlist.png" alt=""></div>';
				}
			}
			$result.= '<div class="prd_img"><a title="'.$prods['prod_nome'].'" href="'.$url.'"><img src="'.$prods['prd_imgm_new'].'" alt=""></a></div>';
			$result.= '<div class="prd_title"><a title="'.$prods['prod_nome'].'" href="'.$url.'">'.mb_strtolower($prods['prod_nome'],"UTF-8").'</a></div>';
			$result.= '<div class="prd_subtitle">Ref.: '.$prods['prod_ref'].'</div>';

			if($prods['prod_precos']['STOCK']>0 && $prods['prod_precos']['STOCK']<3)
			{
				$result.='<div style="text-align:center">Stock: <img src="layout/'.PROJETO.'/images/status_away.png" /></div>';
			}
			else if($prods['prod_precos']['STOCK']>=3)
			{
				$result.='<div style="text-align:center">Stock: <img src="layout/'.PROJETO.'/images/status.png" /></div>';
			}
			else
			{
				$result.='<div style="text-align:center">Stock: <img src="layout/'.PROJETO.'/images/status_busy.png" /></div>';
			}
			if($prods['prod_precos']['PROMOCAO']==1 && $prods['prod_precos']['PRECO_ANTIGO']>0 && $prods['prod_precos']['PRECO_ANTIGO'] > $prods['prod_precos']['PVP'])
			{
				$result.= '<div class="prdprice_ant HvRm">'.numberformat($prods['prod_precos']['PRECO_ANTIGO']).'</div>';
				$percdesconto=getDescontoAplicado($prods['prod_precos']['PRECO_ANTIGO'],$prods['prod_precos']['PVP']);
				$result.= '<div class="prd_tagdiscount HvBd">-'.$percdesconto.'%</div>';
			}
			else
			{
				$result.= '<div class="prdprice_ant HvRm"></div>';
			}
			if($prods['prod_precos']['PROMOCAO']==0 && $prods['prod_precos']['TRAVELSIZE']==1)
			{
				$result.= '<div class="prd_tagship HvBd"></div>';
			}
			if($prods['prod_precos']['PROMOCAO']==0 && $prods['prod_precos']['NOVO']==1 && $prods['prod_precos']['TRAVELSIZE']==0)
			{
				$result.= '<div class="prdtag_new HvBd">'.getLinguas('key_prdtag_new',$GLOBALS['site_lang']).'</div>';
			}
			if($prods['prod_precos']['PVP']>0)
			{
				$result.= '<div class="prdprice_n">'.numberformat($prods['prod_precos']['PVP']).'</div>';
			}
			else
			{
				$result.= '<div class="prdprice_n"></div>';
			}
			// if($prods['prod_precos']['STOCK']>0 && $prods['prod_precos']['PVP']>0 && $prods['prod_carrinho']==1)
			if($prods['prod_precos']['PVP']>0 && $prods['prod_carrinho']==1)
			{
				$result.= '<div class="prodby HvRm none"><input type="hidden" value="'.$prods['prod_id'].'" name="prodid"/>'.getLinguas('key_catalogo_comprar',$GLOBALS['site_lang']).'</div>';
			}
			else
			{
				$result.= '<div class="prodby_disabled HvRm none" onclick="window.open(\''.$url.'\',\'_self\')">'.getLinguas('key_catalogo_comprar',$GLOBALS['site_lang']).'</div>';
			}
			$result.= '</div>';
			$rs->db_next();
		}
		$result.= '</div>';
		$result.= '</div>';
		$result.= '<div class="arrowright" style="'.$style.'" onclick="promocoesScrollRight();"><img src="layout/'.PROJETO.'/images/homesliderarrowright.png" alt=""></div>';
		$result.= '</div>';
		$widthnova=236*$numprods;
		// $result.= '<script>$("#promocoesinnerslider > .slidinner").width('.$widthnova.');$("#promocoesinnerslider").scrollLeft(0);</script>';
	}
	$rs->db_close();

	return $result;
}

function getDescontoAplicado($precoantigo,$preconovo,$round=0){	return number_format((100-($preconovo/$precoantigo)*100),2);}

function get_product_images_first($prod_id)
{
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;
	$result=array();
	if(isset($prod_id) && $prod_id > 0)
	{
		$sql='SELECT pi.img_ext,pi.Ordem,pi.URL_Source FROM products_images pi WHERE pi.ID_ORG='.$prod_id.'
			AND (COALESCE(pi.URL_Source,"") NOT LIKE "dotbright-reference:%"
				OR NOT EXISTS (SELECT 1 FROM products_images real_img WHERE real_img.ID_ORG=pi.ID_ORG
					AND COALESCE(real_img.URL_Source,"") NOT LIKE "dotbright-reference:%"))
			ORDER BY pi.Ordem Asc limit 0,1';
		$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
		if(!$rs->EOF())
		{
			/*$result[0]['img_ext'] 	= $rs->db_data['img_ext'];
			$result[0]['Ordem'] 	= $rs->db_data['Ordem'];*/


			$ext = $rs->db_data['img_ext'];
			$ordem = $rs->db_data['Ordem'];
			$img_file=IMG_FOLDER.'/products/'.$prod_id.'_'.$ordem.'.'.$ext;

			if(!dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file) )
			{
				if($ext=='png') $ext='jpg';
				else $ext='png';

				$img_file=IMG_FOLDER.'/products/'.$prod_id.'_'.$ordem.'.'.$ext;
				if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file) )
				{
					$sql='UPDATE products_images set img_ext="'.$ext.'" WHERE ID_ORG = "'.$prod_id.'"';
					if (!dotbright_shared_images()) $res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
				}
			}


			$result[0]['img_ext'] 	= $ext;
			$result[0]['Ordem'] 	= $ordem;
			$result[0]['is_reference'] = str_starts_with((string)($rs->db_data['URL_Source'] ?? ''), 'dotbright-reference:');


		}
		$rs->db_close();
	}
	return $result;
}

function get_product_images($prod_id)
{
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;
	$result=array();
	if(isset($prod_id) && $prod_id > 0)
	{
		$sql='SELECT pi.img_ext,pi.Ordem,pi.URL_Source FROM products_images pi WHERE pi.ID_ORG='.$prod_id.'
			AND (COALESCE(pi.URL_Source,"") NOT LIKE "dotbright-reference:%"
				OR NOT EXISTS (SELECT 1 FROM products_images real_img WHERE real_img.ID_ORG=pi.ID_ORG
					AND COALESCE(real_img.URL_Source,"") NOT LIKE "dotbright-reference:%"))
			ORDER BY pi.Ordem Asc';
		$pos=0;
		$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
		if(!$rs->EOF())
		{
			while(!$rs->EOF())
			{
				$ext = $rs->db_data['img_ext'];
				$ordem = $rs->db_data['Ordem'];
				$img_file=IMG_FOLDER.'/products/'.$prod_id.'_'.$ordem.'.'.$ext;

				if(!dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file) )
				{
					if($ext=='png') $ext='jpg';
					else $ext='png';

					$img_file=IMG_FOLDER.'/products/'.$prod_id.'_'.$ordem.'.'.$ext;
					if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file) )
					{
						$sql='UPDATE products_images set img_ext="'.$ext.'" WHERE ID_ORG = "'.$prod_id.'"';
						if (!dotbright_shared_images()) $res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
					}
				}


				$result[$pos]['img_ext'] 	= $ext;
				$result[$pos]['Ordem'] 		= $ordem;
				$result[$pos]['is_reference'] = str_starts_with((string)($rs->db_data['URL_Source'] ?? ''), 'dotbright-reference:');
				$pos++;
				$rs->db_next();
			}
		}
		else
		{
			//Nao encontrou em base de dados contudo pode existir em ficheiro
			$ordem=1;
			$ext='png';
			$img_file=IMG_FOLDER.'/products/'.$prod_id.'_'.$ordem.'.'.$ext;
			if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file) )
			{
				$sql='INSERT INTO products_images ( ID_ORG,img_ext,Ordem ) values ( "'.$prod_id.'","'.$ext.'","'.$ordem.'" ) ';
				if (!dotbright_shared_images()) $res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
				else $result[]=array('img_ext'=>$ext,'Ordem'=>$ordem,'is_reference'=>false);
			}

			$ext='jpg';
			$img_file=IMG_FOLDER.'/products/'.$prod_id.'_'.$ordem.'.'.$ext;
			if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file) )
			{
				$sql='INSERT INTO products_images ( ID_ORG,img_ext,Ordem ) values ( "'.$prod_id.'","'.$ext.'","'.$ordem.'" ) ';
				if (!dotbright_shared_images()) $res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
				else $result[]=array('img_ext'=>$ext,'Ordem'=>$ordem,'is_reference'=>false);
			}

		}
		$rs->db_close();
	}
	return $result;
}

function showTestemunhosProduto($prod_id)
{
	global $key_testemunhos_title,$key_testemunhos_giveopinion,$key_testemunhos_nodata,$site_lang,$site_http_ajax_url,$site_http_url;
	$testemunhos 	= getTestemunhosProduto($prod_id);
	$tam 			= count($testemunhos);
	$result='';
	if($tam>0)
	{
		$result.='<div class="testemunhos_wrapper">';
		$result.='<div class="testemunhos_header">';
		$result.='<div class="testemunhos_title HvLt">'.$key_testemunhos_title[$site_lang].'</div>';
		$result.='<div class="testemunhos_more"><img src="layout/'.PROJETO.'/images/pencilicon.png" alt=""> <a id="fancy_testemunho" href="'.$site_http_ajax_url.'ajaxTestemunho.php?idprod='.$prod_id.'" target="_blank" data-fancybox-type="iframe">'.$key_testemunhos_giveopinion[$site_lang].'</a></div>';
		$result.='</div>';
		$result.='<div class="c"></div>';
		$result.='<div class="conteudo">';

		if($tam !=0)
		{
			$result.='<table border="0" cellspacing="0" cellpadding="0">';
			for($i=0;$i<$tam;$i++)
			{
				$result.='<tr>';
				$result.='<td class="HvRm">';
				$result.='<span>'.$testemunhos[$i]['Nome'].'</span>,'.$testemunhos[$i]['Pais'].'<br><br>';
				$result.='</td>';
				$result.='</tr>';
				$result.='<tr>';
				$result.='<td class="HvRm" style="color:#6E6E6E;font-size:12px;">';
				$result.=nl2br($testemunhos[$i]['Texto']);
				$result.='</td>';
				$result.='</tr>';
				if($tam>1 && ($i+1)!=$tam) $result.='<tr><td colspan="2"><hr></td></tr>';
			}
			$result.='</table>';
		}
		$result.='</div>';
		$result.='</div>';
	}
	else
	{
		$result.='<div class="testemunhos_wrapper">';
		$result.='<div class="testemunhos_header">';
		$result.='<div class="testemunhos_more"><img src="layout/'.PROJETO.'/images/pencilicon.png" alt=""> <a id="fancy_testemunho" href="'.$site_http_ajax_url.'ajaxTestemunho.php?idprod='.$prod_id.'" target="_blank" data-fancybox-type="iframe">'.$key_testemunhos_giveopinion[$site_lang].'</a></div>';
		$result.='</div>';
		$result.='<div class="c"></div>';
		$result.='<div class="conteudo">';
		$result.='</div>';
		$result.='</div>';
	}
	return $result;
}

function getTestemunhosProduto($prod_id)
{
	global $site_http_url;
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;
	$pos=0;
	$resultado=array();
	$sql='SELECT Nome,Local,Pais,Texto FROM products_testemunhos WHERE Visivel=1 and  ID_PROD='.$prod_id.' ORDER BY DATA DESC';
	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	while(!$rs->EOF())
	{
		$resultado[$pos]['Nome']		= $rs->db_data['Nome'];
		$resultado[$pos]['Local'] 		= $rs->db_data['Local'];
		$resultado[$pos]['Pais']		= $rs->db_data['Pais'];
		$resultado[$pos]['Texto'] 		= $rs->db_data["Texto"];
		$pos++;
		$rs->db_next();
	}
	$rs->db_close();
	return $resultado;
}

function getProdutosRelacionadosCarrinho($prod_id)
{
	//Esta funcao nao esta em uso de momento
	/*
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;
	$prods=\get_produtc_info($prod_id);
	$idfam=$prods['fam_id'];
	$result='';
	$sql='SELECT pfo.ID_Prod2 as idprod FROM produtos_opcoes pfo WHERE pfo.ID_Prod='.$prod_id.'ORDER BY RAND() LIMIT 2';
	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	if($rs->EOF())
	{
		$rs->db_close();
		$sql='SELECT pfo.ID_Prod as idprod FROM products_families_obj pfo WHERE pfo.ID_ORG='.$idfam.' AND pfo.ID_Prod!='.$prod_id.'ORDER BY RAND() LIMIT 2';
		$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	}
	if(!$rs->EOF())
	{
		$i=0; $z=0;
		$result .= '<div class="equiv_wrap">';
		while(!$rs->EOF())
		{
			$i++;
			$prod_id=$rs->db_data['idprod'];
			$prods=\get_produtc_info($prod_id);
			if($prods && !substr_count($prods['prd_imgm_new'],'no_photo') && $z<=4)
			{
				$result.= mostra_bloco_carrinho($prods);
				$z++;
			}
			$rs->db_next();
		}
		$result.='<div class="c"></div></div>';
	}
	$rs->db_close();*/
	return $result;
}

function hasProdutosSugestoes($prod_id)
{
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;
	$sql='SELECT count(po.ID_Prod2) as Tam FROM produtos_sugestoes po WHERE po.ID_Prod='.$prod_id.' ORDER BY po.ID_Prod ASC';
	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	$tam=$rs->db_data['Tam'];
	$rs->db_close();
	return $tam;
}

function hasProdutosRelacionados($prod_id='')
{
	$prods=\get_produtc_info($prod_id);
	$tam='';
	if($prods != NULL && $idfam=$prods['fam_id'])
	{
		$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
		$rs=new DataObj;
		$rs->db_open($db_link,$GLOBALS['DB_name'],'SELECT p.ID FROM products p INNER JOIN products_families_obj pfo on p.ID=pfo.ID_Prod WHERE pfo.ID_ORG='.$idfam.' AND p.Aproved=1 group by pfo.ID_Prod');
		$tam=$rs->db_rows;
		$rs->db_close();
	}
	return $tam;
}

function hasProdutosRelacionados_novo($prod_id='')
{
	global $site_lang;
	$tam='';
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;
	$sql='select ID_Prod from produtos_opcoes inner join products on (products.ID=produtos_opcoes.ID_Prod) where ID_Prod='.$prod_id;
	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	$output=array('bydef'=>0,'default'=>0);
	if($rs->db_rows>0)
	{
		$output['bydef']=$rs2->db_rows;
	}
	$rs->db_close();
	$prods=\get_produtc_info($prod_id);
	if($prods != NULL && $output['bydef']==0)
	{
		$idfam=$prods['fam_id'];
		$sql='SELECT p.ID FROM products p INNER JOIN products_families_obj pfo on p.ID=pfo.ID_Prod WHERE pfo.ID_ORG='.$idfam.' AND p.Aproved=1 group by pfo.ID_Prod';
		$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
		$tam=$rs->db_rows;
		$rs->db_close();
		$output['default']=$tam;
	}
	return $output;
}

function getProdutosSugestoes($prod_id)
{
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;
	$result='';
	$sql='SELECT po.ID_Prod2 FROM produtos_sugestoes po WHERE po.ID_Prod='.$prod_id.' ORDER BY RAND() LIMIT 10';
	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	if(!$rs->EOF())
	{
		$result.='<div id="sugestoesinnerslider" class="slidwrap">';
		while(!$rs->EOF())
		{
			$i++;
			$prod_id=$rs->db_data['ID_Prod2'];
			$prods=\get_produtc_info($prod_id);
			if($prods && !substr_count($prods['prd_imgm_new'],'no_photo') && $z<=4)
			{
				$result.= mostra_bloco($prods);
				$z++;
			}
			$rs->db_next();
		}
		$result.='<div class="c"></div></div>';
	}
	$rs->db_close();
	return $result;
}

function addRemoveWishList($idprod,$tipo)
{
    $db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
    $dados=\cliente_logado();
	$cli_id=$dados['cli_id'];
	$cli_id_temp=$_SESSION['CLIENTE_ID_TEMP'];
	if($tipo == 'add')
	{
		if(!isProductInWishList($idprod,$cli_id,$cli_id_temp))
		{
			//$sql='INSERT INTO clientes_wishlist (PROD_ID,CLIENT_ID,CLIENTE_ID_TEMP) VALUES ("'.$idprod.'","'.$cli_id.'","'.$cli_id_temp.'")';
            $sql='INSERT INTO clientes_wishlist (PROD_ID,CLIENT_ID) VALUES ("'.$idprod.'","'.$cli_id.'")';

            //die($sql);
			$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
			if(!$res) echo "-1";
			else echo getLinguas('key_wishlist_rem');
		}
		else
		{
			echo getLinguas('key_wishlist_rem');
		}
	}
	else if($tipo == 'remove')
	{
		if($client_id > 0 && $client_id_temp != '') $sql='DELETE FROM `clientes_wishlist` WHERE PROD_ID="'.$idprod.'" AND (CLIENT_ID="'.$cli_id.'" OR CLIENTE_ID_TEMP="'.$cli_id_temp.'")';
		else $sql='DELETE FROM `clientes_wishlist` WHERE PROD_ID="'.$idprod.'" AND CLIENTE_ID_TEMP="'.$cli_id_temp.'"';
		$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
		if(!$res) echo "-1";
		else echo getLinguas('key_wishlist_add');
	}
}

function addRemoveVisualizados($idprod,$tipo)
{
    $db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
    $dados=\cliente_logado();
	$cli_id=$dados['cli_id'];
	$cli_id_temp=$_SESSION['CLIENTE_ID_TEMP'];
	if($tipo == 'add')
	{
		if(!isProductInVisualizados($idprod,$cli_id,$cli_id_temp))
		{
			$sql='INSERT INTO clientes_prod_visualizados (PROD_ID,CLIENT_ID,CLIENTE_ID_TEMP) VALUES ("'.$idprod.'","'.$cli_id.'","'.$cli_id_temp.'")';
			$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
			if(!$res) echo "-1";
		}
	}
}

function showAfiliados()
{
}

function isProductInWishList($prodid,$client_id,$client_id_temp)
{
	//Para tornar esta funcao mais rapida,a primeira vez que for invocada vai carregar para a memoria todos os produtos da wishlist
	global $wishlist_produtos;

	if(!isset($wishlist_produtos) || $wishlist_produtos==NULL)
	{
		$wishlist_produtos=array();

		$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	    $rs=new DataObj;
	    $result=false;
	    if($client_id > 0 && $client_id_temp != '') $sql='SELECT PROD_ID FROM clientes_wishlist WHERE (CLIENT_ID="'.$client_id.'" OR CLIENTE_ID_TEMP="'.$client_id_temp.'")';
		else $sql='SELECT PROD_ID FROM clientes_wishlist WHERE CLIENTE_ID_TEMP="'.$client_id_temp.'"';
		$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	    while(!$rs->EOF())
	    {
	    	$wishlist_produtos[$rs->db_data['PROD_ID']]=1;
	    	$rs->db_next();
	    }
	    $rs->db_close();
	}

	$result=false;
	if($wishlist_produtos[$prodid]==1)$result=true;

    /*$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
    $rs=new DataObj;
    $result=false;
    if($client_id > 0 && $client_id_temp != '') $sql='SELECT ID FROM clientes_wishlist WHERE (CLIENT_ID="'.$client_id.'" OR CLIENTE_ID_TEMP="'.$client_id_temp.'") AND PROD_ID="'.$prodid.'"';
	else $sql='SELECT ID FROM clientes_wishlist WHERE CLIENTE_ID_TEMP="'.$client_id_temp.'" AND PROD_ID="'.$prodid.'"';

    $rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
    if(!$rs->EOF()) $result=true;
    $rs->db_close();*/
    return $result;
}

function isProductInVisualizados($prodid,$client_id,$client_id_temp)
{
	//Para tornar esta funcao mais rapida,a primeira vez que for invocada vai carregar para a memoria todos os produtos da wishlist
	global $visualizados_produtos;
	if(!isset($visualizados_produtos))
	{
		$visualizados_produtos=array();

		$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	    $rs=new DataObj;
	    $result=false;
	    if($client_id > 0 && $client_id_temp != '') $sql='SELECT PROD_ID FROM clientes_prod_visualizados WHERE (CLIENT_ID="'.$client_id.'" OR CLIENTE_ID_TEMP="'.$client_id_temp.'")';
		else $sql='SELECT PROD_ID FROM clientes_prod_visualizados WHERE CLIENTE_ID_TEMP="'.$client_id_temp.'"';
		$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	    while(!$rs->EOF())
	    {
	    	$visualizados_produtos[$rs->db_data['PROD_ID']]=1;
	    	$rs->db_next();
	    }
	    $rs->db_close();
	}
	$result=false;
	if($visualizados_produtos[$prodid]==1)$result=true;
	/*
    $db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
    $rs=new DataObj;
    $result=false;
    if($client_id > 0 && $client_id_temp != '') $sql='SELECT ID FROM clientes_prod_visualizados WHERE (CLIENT_ID="'.$client_id.'" OR CLIENTE_ID_TEMP="'.$client_id_temp.'") AND PROD_ID="'.$prodid.'"';
	else $sql='SELECT ID FROM clientes_prod_visualizados WHERE CLIENTE_ID_TEMP="'.$client_id_temp.'" AND PROD_ID="'.$prodid.'"';
    $rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
    if(!$rs->EOF()) $result=true;
    $rs->db_close();
    */
    return $result;
}

function showWishList()
{
	global $site_lang,$site_http_url;
	global $key_wishlist_title,$key_wishlist_subtitle,$key_wishlist_addallproducts;
	echo '<div class="titulo_pagina">'.$key_wishlist_title[$site_lang].'</div>';
    echo '<br>';
	echo '<div style="font-size:20px;color:#6E6E6E">'.$key_wishlist_subtitle[$site_lang].'</div>';
	echo '<div style="font-size:13px;color:#6E6E6E"></div>';
	echo '<br>';
	echo '<br>';
	echo '<div class="wish_wrap">';
	echo '<a href="javascript:;" onclick="AddAllWishlist(\''.seo("pages.php?page_id=".$GLOBALS['cart_purchage_id']).'\');" class="HvMd AddAllWishlist">'.$key_wishlist_addallproducts[$site_lang].'</a>';
	echo '<br /><br /><hr class="line2"><br />';
	catalogo_produtos();
	echo'<div class="c"></div>';
	echo '</div>';
}

function getWishListCliente($client_id=0,$client_id_temp='')
{
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;
	$result=0;
	$pos=0;
	if($client_id > 0 && $client_id_temp!='') $sql='SELECT COUNT(cw.PROD_ID) AS count FROM clientes_wishlist cw WHERE (cw.CLIENT_ID="'.$client_id.'" OR cw.CLIENTE_ID_TEMP="'.$client_id_temp.'") ORDER BY cw.ID DESC';
	else $sql='SELECT COUNT(cw.PROD_ID) AS count FROM clientes_wishlist cw WHERE (cw.CLIENTE_ID_TEMP="'.$client_id_temp.'") ORDER BY cw.ID DESC';
	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	if(!$rs->EOF())
	{
		$result=$rs->db_data['count'];
	}
	$rs->db_close();
	return $result;
}

function productCategoryHasProducts($family_id=0)
{
	return productSubCategoryHasProducts($family_id);
	/*$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;
	$sql='SELECT ID FROM products_families WHERE ID_ORG='.$family_id;
	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	while(!$rs->EOF())
	{
		$idsubfam=$rs->db_data['ID'];
		if(productSubCategoryHasProducts($idsubfam)==1)
		{
			$rs->db_close();
			return 1;
		}
		$rs->db_next();
	}
	$rs->db_close();
	return 0;*/
}

function productSubCategoryHasProducts($family_id=0)
{
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;
	$result=0;
	$pos=0;
	$sql='SELECT ID_PROD FROM products_families_obj WHERE ID_ORG='.$family_id;
	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	if(!$rs->EOF()) $result=1;
	$rs->db_close();

	if($result==0)
	{
		$sql='SELECT ID FROM products_families WHERE ID_ORG='.$family_id;
		$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
		while(!$rs->EOF())
		{
			$idsubfam=$rs->db_data['ID'];
			if(productSubCategoryHasProducts($idsubfam)==1)
			{
				$rs->db_close();
				return 1;
			}
			$rs->db_next();
		}
		$rs->db_close();
	}
	return $result;
}

function productfiltersfams($family_id)
{
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;
	$result=0;
	$pos=0;
	//Se a familia base for 1 então:
	$sql='SELECT count(prod_id) as total
	FROM filters_products
	WHERE (fam_id2='.$family_id.' OR fam_id3='.$family_id.') AND filtros_rel2 <> "" AND filtro_active=1 AND prod_id > 0';
	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	if(!$rs->EOF() && $rs->db_data['total'] > 0) $result=1;
	$rs->db_close();
	return $result;
}

function inserirNovoTestemunho()
{
	global $site_http_url;
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);

	$idprod 	= addslashes($_POST['idprod']);
	$nome 		= addslashes($_POST['testemunho_nome']);
	$local 		= addslashes($_POST['testemunho_local']);
	$pais 		= addslashes($_POST['testemunho_pais']);
	$texto 		= addslashes($_POST['testemunho_mensagem']);
	$data 		= date("Y-m-d H:i:s");

	$sql='INSERT INTO products_testemunhos(ID_PROD,Nome,Local,Pais,Texto,Visivel,Data) VALUES ("'.$idprod.'","'.$nome.'","'.$local.'","'.$pais.'","'.$texto.'",0,"'.$data.'")';
	$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
	$new_id=db_get_last_insert_id($db_link);
	$sql='INSERT INTO mensagens(Data,Assunto,Mensagem,ID_Cliente) VALUES ("'.$data.'","Novo Testemunho","Novo Testemunho,<a href=\''.$site_http_url.'admin/testemunhos/pagina.php?ID='.$new_id.'\' target=\'_top\'>Ver Testemunho</a>",0)';
	$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
	return $res;
}

function getProductsExtraShop()
{
	global $site_lang;
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;
	$result=array();
	$sql='SELECT
                P.ID
            FROM
                products_extraloja AS PEL
                INNER JOIN products AS P
                ON PEL.ID_Prod=P.ID
                INNER JOIN brands AS BR
                ON P.ID_Marca=BR.ID
            WHERE
                P.Aproved=1
                AND BR.Visivel=1
			Order by
				RAND()
			Limit 0,6';
	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	while(!$rs->EOF())
	{
		$prod_id=$rs->db_data['ID'];
		$prods=\get_produtc_info($prod_id);
		$result[]=$prods;
		$rs->db_next();
	}
	$rs->db_close();
	return $result;
}

function getBrandsOffersShop()
{
	global $site_lang;
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;
	$result=array();
	$sql='SELECT b.ID,b.img_ext,bo.*
			FROM brands b INNER JOIN brands_offers bo on bo.ID_Brand=b.ID
			ORDER BY bo.Ordem ASC
			LIMIT 0,6';
	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	while(!$rs->EOF())
	{
		$brand_id=$rs->db_data['ID'];
		$brands=get_produtc_brand($brand_id);
		$brands['brand_id']=$brand_id;
		$result[]=$brands;
		$rs->db_next();
	}
	$rs->db_close();
	return $result;
}

function MOSTRA_FAMILIAS($ID_ORG=0,$n=0)
{
	global $m_http_url_images,$site_http_url,$tema,$site_lang;
    $db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
    $rs=new Dataobj;$rs2=new Dataobj;
	if($ID_ORG==0) echo '<div class="fams-right"><div><b>CATÁLOGO COMPLETO</b></div>';
	$i=0;
	$s='';
	while($i<$n)
	{
		$i++;
		$s='style="margin-left:15px"';
	}
	$sql="Select pf.ID,pfl.Cabecalho from products_families pf inner join products_families_lang pfl on pfl.ID_PAI=pf.ID where pf.ID_ORG=".$ID_ORG." and pfl.lang='$site_lang' and visivel=1";
	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	while(!$rs->EOF())
	{
		$nomeb=$rs->db_data["Cabecalho"];
		$fam_id=$rs->db_data["ID"];
		$URL_Link='';
		$URL_Link=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$fam_id,$fam_id,1);
		$URL_Link=seo(str_replace($site_http_url,'',$URL_Link));
		echo '<div '.$s.' class="fams">&middot; <a class="default" href="'.$URL_Link.'">'.$nomeb.'</a></div>';
		MOSTRA_FAMILIAS($fam_id,$n+1);
		$rs->db_next();
	}
	$rs->db_close();
	if($ID_ORG==0) echo '</div>';
}

function verifica_promo($prod_id=0,$prods=array())
{
    $db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
    $new_prods='';
    if($prods['PROMOCAO'] == 1 && $prods['Promo_data_ini'] != '' && $prods['Promo_data_fim'] != '')
    {
    	$date=date("Y-m-d 00:00:00");
    	$today=strtotime($date);
    	$Promo_data_fim=$prods['Promo_data_fim'];
    	$Promo_data_ini=$prods['Promo_data_ini'];
    	if($today > strtotime($Promo_data_fim))
    	{
    		$sql ="UPDATE products_objs SET obj_promo_ativo=0 WHERE obj_type_id =$prod_id";
    		Runquery($db_link,$GLOBALS['DB_name'],$sql);
    		$new_prods=\get_produtc_info_essentials($prod_id);
    	}
    }
    return $new_prods;
}

function BuscarArvoreFams_F_FrontEnd($id_org=0)
{
	$hConn=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$obj=new DataObj;
	$n=0;
	$arvore2[$n]=$id_org;
	while($id_org > 0)
	{
		$sql="SELECT ID,ID_ORG FROM products_families WHERE ID=$id_org";
		$obj->db_open($hConn,$GLOBALS['DB_name'],$sql);
		$id_org=$obj->db_data["ID_ORG"];
		if($id_org > 0)
		{
			$n++;
			$arvore2[$n]=$id_org;
		}
		$obj->db_close();
	}
	$output=array_reverse($arvore2);
	return $output;
}

function verificaProdutosVisualizados()
{
    $db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
    $rs=new Dataobj;
	$dados=cliente_logado();
	if($dados)
	{
		$cli_id_temp=$_SESSION['CLIENTE_ID_TEMP'];
	    $ID=0;
	    $sql="SELECT * FROM clientes_prod_visualizados WHERE CLIENT_ID=0 AND updated=0 AND CLIENTE_ID_TEMP ='$cli_id_temp'";
	    $rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	    if(!$rs->EOF())
	    {
	        while (!$rs->EOF())
	        {
	            $ID=$rs->db_data['ID'];
	            $sql="UPDATE clientes_prod_visualizados SET updated=1,CLIENT_ID=".$dados['cli_id']." WHERE ID=$ID";
	            Runquery($db_link,$GLOBALS['DB_name'],$sql);
	            $rs->db_next();
	        }
	    }
	    $rs->db_close();
    }
}

function update_products_precos($ID_PROD=0,$obj_pvp=0,$obj_qtdmin=0,$obj_stock=0,$obj_ref='',$obj_ean='',$tipo_pvp=0,$com_stock=0,$Supplier_ID=0,$price_conf=0,$obj_iva='23',$pc=0)
{
	if($ID_PROD<=0)return;

	$log=0;

	$com_stock=1;//Nao sei porque tinha esta variavel

	$obj_stock=intval($obj_stock);
	$Supplier_ID=intval($Supplier_ID);

	global $prod_objs_history_dados,$stock_minimo_nulo;
	global $site_shell_url;

	$stock_minimo=0;
	if(isset($stock_minimo_nulo))$stock_minimo=$stock_minimo_nulo;

	$rs=new DataObj;
	$rs2=new DataObj;
	$rs3=new DataObj;
	$db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	if($db_link)
	{
		//Vamos atualizar a data do artigo
		$udtime = date("Y-m-d H:i:s");
		$sql = 'UPDATE products SET udtime="'.$udtime.'",Aproved=1,ID_Admin=0 WHERE ID="'.$ID_PROD.'"';
		$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);

		//Obter os dados do preço do artigo
		$sql='SELECT obj_id,obj_Supplier_ID,obj_pvp,obj_stock FROM products_objs WHERE obj_type_id="'.$ID_PROD.'" ORDER BY obj_id DESC';
		if($log==1)file_put_contents($site_shell_url.'files/bk/importar'.$Supplier_ID.date("Ymd"), $ID_PROD.";".$sql."\n\n", FILE_APPEND);
		$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
		if(!$rs->EOF())
		{

			

			$obj_id=$rs->db_data['obj_id'];
			$obj_Supplier_ID=$rs->db_data['obj_Supplier_ID'];
			$obj_pvp_atual=trim($rs->db_data['obj_pvp']);
			$obj_stock_atual=trim($rs->db_data['obj_stock']);


			//Converter tudo para o mesmo formato:
			//***********************************************************
			$obj_pvp = round($obj_pvp,4);
			$obj_pvp_atual = round($obj_pvp_atual,4);
			$obj_stock = round($obj_stock,4);
			$obj_stock_atual = round($obj_stock_atual,4);


			//*************************************************************
			//E se eu gravar apenas um preco de hoje por tipo de importador/produto !!
			$sql='DELETE FROM products_objs_history_day WHERE ID_ORG="'.$obj_id.'" AND Supplier_ID="'.$Supplier_ID.'"';
			$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
			if($log==1)file_put_contents($site_shell_url.'files/bk/importar'.$Supplier_ID.date("Ymd"), $ID_PROD.";".$sql."\n\n", FILE_APPEND);
			$sql='INSERT INTO products_objs_history_day(ID_ORG,Supplier_ID,pvp,iva,Stock,Ref_loja,data_i,data_u,Flag,pc) values("'.$obj_id.'","'.$Supplier_ID.'","'.$obj_pvp.'","'.$obj_iva.'","'.$obj_stock.'","'.$obj_ref.'","'.date('Y-m-d H:i:s').'","'.date('Y-m-d H:i:s').'","2","'.$pc.'")';
			$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
			if(!$res)echo db_error($db_link);
			if($log==1)file_put_contents($site_shell_url.'files/bk/importar'.$Supplier_ID.date("Ymd"), $ID_PROD.";".$sql."\n\n", FILE_APPEND);
			$sql='INSERT INTO products_objs_history(ID_ORG,Supplier_ID,pvp,iva,Stock,Ref_loja,data_i,data_u,Flag,pc) values("'.$obj_id.'","'.$Supplier_ID.'","'.$obj_pvp.'","'.$obj_iva.'","'.$obj_stock.'","'.$obj_ref.'","'.date('Y-m-d H:i:s').'","'.date('Y-m-d H:i:s').'","2","'.$pc.'")';
			$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
			if(!$res)echo db_error($db_link);
			if($log==1)file_put_contents($site_shell_url.'files/bk/importar'.$Supplier_ID.date("Ymd"), $ID_PROD.";".$sql."\n\n", FILE_APPEND);
			//1 - Vamos verificar se este artigo ja tem preços deste fornecedor
			//*************************************************************
			//$Supplier_ID é o id do fornecedor que esta a ser importado
			//$obj_pvp é o preco que vem do fornecedor que esta a ser importado
			//$obj_stock é o stock que vem do fornecedor que esta a ser importado
			//obj_Supplier_ID é o id do fornecedor que esta associado ao artigo neste momento
			
			$fields='';
			if($com_stock == 1) $fields .= ',obj_stock="'.$obj_stock.'"';

			if($log==1)file_put_contents($site_shell_url.'files/bk/importar'.$Supplier_ID.date("Ymd"), $ID_PROD.";$obj_pvp > 0 && $obj_stock > $stock_minimo\n\n", FILE_APPEND);
			if($obj_pvp > 0 && $obj_stock > $stock_minimo)
			{
				if($log==1)file_put_contents($site_shell_url.'files/bk/importar'.$Supplier_ID.date("Ymd"), $ID_PROD.";$Supplier_ID == $obj_Supplier_ID\n\n", FILE_APPEND);

				if($Supplier_ID == $obj_Supplier_ID)
				{
					//Como é o mesmo fornecedor será para manter, vamos atualizar o preço e o stock
					$sql='UPDATE products_objs SET obj_pvp="'.$obj_pvp.'",obj_stock="'.$obj_stock.'" WHERE obj_type_id="'.$ID_PROD.'"';
					$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
					if($log==1)file_put_contents($site_shell_url.'files/bk/importar'.$Supplier_ID.date("Ymd"), $ID_PROD.";".$sql."\n\n", FILE_APPEND);
				}
				else
				{
					if($log==1)file_put_contents($site_shell_url.'files/bk/importar'.$Supplier_ID.date("Ymd"), $ID_PROD.";price_conf = $price_conf\n\n", FILE_APPEND);
					$nao_atualizou_preco=1;
					//é outro fornecedor,será para trocar?
					if($price_conf==0)
					{
						if($log==1)file_put_contents($site_shell_url.'files/bk/importar'.$Supplier_ID.date("Ymd"), $ID_PROD.";if($obj_pvp < $obj_pvp_atual || ($obj_pvp == $obj_pvp_atual && ($obj_stock >= $obj_stock_atual)) || ($obj_stock_atual<=$stock_minimo) )\n\n", FILE_APPEND);						
						//Se tiver um valor inferior
						if($obj_pvp < $obj_pvp_atual || ($obj_pvp == $obj_pvp_atual && ($obj_stock >= $obj_stock_atual)) || ($obj_stock_atual<=$stock_minimo) || ($obj_pvp_atual <=0 && $obj_pvp > 0 ) )
						{
							$nao_atualizou_preco=0;
							//é para trocar
							$sql='UPDATE products_objs SET obj_Supplier_ID="'.$Supplier_ID.'",obj_pvp="'.$obj_pvp.'",obj_stock="'.$obj_stock.'" WHERE obj_type_id="'.$ID_PROD.'"';
							$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
							if($log==1)file_put_contents($site_shell_url.'files/bk/importar'.$Supplier_ID.date("Ymd"), $ID_PROD.";".$sql."\n\n", FILE_APPEND);
						}
					}
					else
					{
						//Se tiver um stock superior
						if($obj_stock > $obj_stock_atual)
						{
							$nao_atualizou_preco=0;
							//é para trocar
							$sql='UPDATE products_objs SET obj_Supplier_ID="'.$Supplier_ID.'",obj_pvp="'.$obj_pvp.'",obj_stock="'.$obj_stock.'" WHERE obj_type_id="'.$ID_PROD.'"';
							$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
							if($log==1)file_put_contents($site_shell_url.'files/bk/importar'.$Supplier_ID.date("Ymd"), $ID_PROD.";".$sql."\n\n", FILE_APPEND);							
						}
					}

					if($nao_atualizou_preco)
					{
						$hoje=date('Y-m-d');
						$sql='SELECT ID FROM products_objs_history_day WHERE ID_ORG="'.$obj_id.'" AND Supplier_ID <> "'.$Supplier_ID.'" AND (DATE(data_i)="'.$hoje.'" OR DATE(data_u)="'.$hoje.'")';
						$rs2->db_open($db_link,$GLOBALS['DB_name'],$sql);
						if($log==1)file_put_contents($site_shell_url.'files/bk/importar'.$Supplier_ID.date("Ymd"), $ID_PROD.";".$sql."\n\n", FILE_APPEND);
						if(!$rs2->EOF())
						{
							//Ja foi atualizado hoje entao nao faz nada
						}
						else
						{
							//é para colocar este novo supplier
							$sql='UPDATE products_objs SET obj_Supplier_ID="'.$Supplier_ID.'",obj_pvp="'.$obj_pvp.'",obj_stock="'.$obj_stock.'" WHERE obj_type_id="'.$ID_PROD.'"';
							if($log==1)file_put_contents($site_shell_url.'files/bk/importar'.$Supplier_ID.date("Ymd"), $ID_PROD.";".$sql."\n\n", FILE_APPEND);							
							$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
						}
						$rs2->db_close();

					}
				}
			}
			else
			{
				//veio sem preço ou sem stock por isso o stock sera sempre = 0
				if($Supplier_ID == $obj_Supplier_ID)
				{
					//Se era este fornecedor que estava ativo tenho de ir buscar outro
					if($price_conf==0)$object_preco=get_lowest_price($obj_id,$obj_Supplier_ID); //menor preco
					else $object_preco=get_higher_stock($obj_id,$obj_Supplier_ID); //mais stock

					//Se receber >0 é porque é para trocar de fornecedor
					if($object_preco['pvp']>0)
					{
						$obj_pvp2 				= $object_preco['pvp'];	//novo pvp
						$obj_stock2 			= $object_preco['Stock']; //novo stock
						$novo_obj_Supplier_ID 	= $object_preco['Supplier_ID']; //Novo fornecedor

						$fields='';
						if($com_stock == 1) $fields .= ',obj_stock="'.$obj_stock.'"';

						//Só se o melhor preço for o deste fornecedor entao atualiza os valores
						if($obj_pvp2>0 && $obj_stock2>$stock_minimo)
						{
							$sql='UPDATE products_objs SET obj_Supplier_ID="'.$novo_obj_Supplier_ID.'",obj_pvp="'.$obj_pvp2.'",obj_stock="'.$obj_stock2.'" WHERE obj_type_id="'.$ID_PROD.'"';
							$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
							if($log==1)file_put_contents($site_shell_url.'files/bk/importar'.$Supplier_ID.date("Ymd"), $ID_PROD.";".$sql."\n\n", FILE_APPEND);
						}
						else
						{
							//infelizmente o recomendado tb não tem preço ou stock,vamos atualizar este artigo e colocar sem stock
							if($obj_pvp > 0)$sql='UPDATE products_objs SET obj_stock="'.$obj_stock.'",obj_pvp="'.$obj_pvp.'" WHERE obj_type_id="'.$ID_PROD.'"';
							else $sql='UPDATE products_objs SET obj_stock="'.$obj_stock.'" WHERE obj_type_id="'.$ID_PROD.'"';
							$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
							if($log==1)file_put_contents($site_shell_url.'files/bk/importar'.$Supplier_ID.date("Ymd"), $ID_PROD.";".$sql."\n\n", FILE_APPEND);
						}
					}
					else
					{
						$object_preco=get_higest_price($obj_id,$obj_Supplier_ID); //menor preco
						$obj_pvp2 			= $object_preco['pvp'];	//novo pvp
						$obj_stock2 			= $object_preco['Stock']; //novo stock
						$novo_obj_Supplier_ID 	= $object_preco['Supplier_ID']; //Novo fornecedor

						if($obj_pvp2 > 0 && $obj_pvp2 > $obj_pvp_atual)$sql='UPDATE products_objs SET obj_Supplier_ID="'.$novo_obj_Supplier_ID.'",obj_stock="'.$obj_stock2.'",obj_pvp="'.$obj_pvp2.'" WHERE obj_type_id="'.$ID_PROD.'"';
						else $sql='UPDATE products_objs SET obj_stock="'.$obj_stock.'",obj_pvp="'.$obj_pvp.'" WHERE obj_type_id="'.$ID_PROD.'"';
						$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
						
						if($log==1)file_put_contents($site_shell_url.'files/bk/importar'.$Supplier_ID.date("Ymd"), $ID_PROD.";".$sql."\n\n", FILE_APPEND);
					}
				}
				else
				{
					//Atencao que veio sem preco ou sem stock
					if($obj_Supplier_ID=='')
					{
						//Nao tinha vendedor, entao vamos associar este vendedor ao produto
						$sql='UPDATE products_objs SET obj_Supplier_ID="'.$Supplier_ID.'",obj_pvp="'.$obj_pvp.'",obj_stock="'.$obj_stock.'" WHERE obj_type_id="'.$ID_PROD.'"';
						$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
						if($log==1)file_put_contents($site_shell_url.'files/bk/importar'.$Supplier_ID.date("Ymd"), $ID_PROD.";".$sql."\n\n", FILE_APPEND);
					}
					else
					{
						//Ainda nao foi atualizado hoje
						//ler a data mais recente em que o preco foi atualizado ou inserido
						$data_i = '';
						$data_u = '';
						//Vamos ler a data da ultima atualização de preço para este artigo de outra loja
						$sql='SELECT ID,pvp,Stock,data_u FROM products_objs_history_day WHERE Supplier_ID!="'.$Supplier_ID.'" AND ID_ORG="'.$obj_id.'" ORDER BY ID DESC limit 0,1';
						if($log==1)file_put_contents($site_shell_url.'files/bk/importar'.$Supplier_ID.date("Ymd"), $ID_PROD.";".$sql."\n\n", FILE_APPEND);
						$rs3->db_open($db_link,$GLOBALS['DB_name'],$sql);
						if(!$rs3->EOF())
						{
							$data_i = $rs3->db_data['data_i'];
							$data_u = $rs3->db_data['data_u'];
						}
						$rs3->db_close();

						//Ainda nao foi atualizado hoje
						if(substr_count($data_i,date("Y-m-d")) <= 0 && substr_count($data_u,date("Y-m-d")) <= 0 )
						{
							$sql='UPDATE products_objs SET obj_Supplier_ID="'.$Supplier_ID.'",obj_pvp="'.$obj_pvp.'",obj_stock="'.$obj_stock.'" WHERE obj_type_id="'.$ID_PROD.'"';
							$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
							if($log==1)file_put_contents($site_shell_url.'files/bk/importar'.$Supplier_ID.date("Ymd"), $ID_PROD.";".$sql."\n\n", FILE_APPEND);
						}
						else
						{
							if($log==1)file_put_contents($site_shell_url.'files/bk/importar'.$Supplier_ID.date("Ymd"), " ELSE \n\n", FILE_APPEND);

							//Ja foi atualizado hoje mas pode ter subido o preço, só vou mudar se o stock de hoje <=1

							if($obj_stock_atual <= $stock_minimo)
							{								
								$object_preco=get_higest_price($obj_id,$obj_Supplier_ID); //menor preco
								$obj_pvp2 			= $object_preco['pvp'];	//novo pvp
								$obj_stock2 			= $object_preco['Stock']; //novo stock
								$novo_obj_Supplier_ID 	= $object_preco['Supplier_ID']; //Novo fornecedor
								if($obj_pvp2 > 0 && $obj_pvp2 > $obj_pvp_atual)
								{
									$sql='UPDATE products_objs SET obj_Supplier_ID="'.$novo_obj_Supplier_ID.'",obj_stock="'.$obj_stock2.'",obj_pvp="'.$obj_pvp2.'" WHERE obj_type_id="'.$ID_PROD.'"';
									$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
									if($log==1)file_put_contents($site_shell_url.'files/bk/importar'.$Supplier_ID.date("Ymd"), $ID_PROD.";".$sql."\n\n", FILE_APPEND);
								}
							}
						}
					}
				}
			}
		}
		else
		{
			$fields_c='';
			$fields_v='';
			if($com_stock == 1)
			{
				$fields_c .= ',obj_stock';
				$fields_v .= ',"'.$obj_stock.'"';
			}
			$sql='INSERT INTO products_objs (obj_Supplier_ID,obj_type_id,obj_ativo,obj_pvp,obj_qtdmin,obj_type,obj_val2'.$fields_c.') VALUES ("'.$Supplier_ID.'","'.$ID_PROD.'",1,"'.$obj_pvp.'","'.$obj_qtdmin.'","products","'.$tipo_pvp.'"'.$fields_v.')';
			$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
			$obj_id=db_get_last_insert_id($db_link);
			$sql='INSERT INTO products_objs_lang(Cabecalho,ID_PAI,Lang) VALUES ("'.$obj_ref.'","'.$obj_id.'","pt")';
			$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
			//*************************************************************
			//$sql='SELECT pvp FROM products_objs_history WHERE ID_ORG="'.$obj_id.'" AND Supplier_ID="'.$Supplier_ID.'" ORDER BY ID DESC';
			$hoje=date('Y-m-d');
			$sql='SELECT pvp FROM products_objs_history_day WHERE ID_ORG="'.$obj_id.'" AND Supplier_ID="'.$Supplier_ID.'" AND (DATE(data_i)="'.$hoje.'" OR DATE(data_u)="'.$hoje.'") ORDER BY ID DESC';
			$rs2->db_open($db_link,$GLOBALS['DB_name'],$sql);
			if(!$rs2->EOF())
			{
				//Encontrou,vamos comparar os pvp
				$pvp2= $rs2->db_data['pvp'];
				if($pvp2 != $obj_pvp)
				{
					$sql='INSERT INTO products_objs_history_day(ID_ORG,Supplier_ID,iva,Stock,data_i,data_u,Flag,pc) values("'.$obj_id.'","'.$Supplier_ID.'","'.$obj_iva.'","'.$obj_stock.'","'.date('Y-m-d H:i:s').'","'.date('Y-m-d H:i:s').'","3","'.$pc.'")';
					$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);

					$sql='INSERT LOW_PRIORITY INTO products_objs_history(ID_ORG,Supplier_ID,iva,Stock,data_i,data_u,Flag,pc) values("'.$obj_id.'","'.$Supplier_ID.'","'.$obj_iva.'","'.$obj_stock.'","'.date('Y-m-d H:i:s').'","'.date('Y-m-d H:i:s').'","3","'.$pc.'")';
					$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
				}
			}
			else
			{
				$sql='INSERT INTO products_objs_history_day(ID_ORG,Supplier_ID,iva,Stock,data_i,data_u,Flag,pc) values("'.$obj_id.'","'.$Supplier_ID.'","'.$obj_iva.'","'.$obj_stock.'","'.date('Y-m-d H:i:s').'","'.date('Y-m-d H:i:s').'","4","'.$pc.'")';
				$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);

				$sql='INSERT LOW_PRIORITY INTO products_objs_history(ID_ORG,Supplier_ID,iva,Stock,data_i,data_u,Flag,pc) values("'.$obj_id.'","'.$Supplier_ID.'","'.$obj_iva.'","'.$obj_stock.'","'.date('Y-m-d H:i:s').'","'.date('Y-m-d H:i:s').'","4","'.$pc.'")';
				$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
			}
			$rs2->db_close();
			//*************************************************************
		}
		$rs->db_close();
	}
}

function getImportadorConfiguracoes($Supplier_ID)
{
	$db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$obj=new DataObj;
	$sql2='SELECT * FROM a_importador_configuracoes WHERE conf_id='.$Supplier_ID;
	$obj->db_open($db_link,$GLOBALS['DB_name'],$sql2);
	return $obj;
}

function get_lowest_price($obj_id,$obj_Supplier_ID=0)
{
	$db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs2=new DataObj;
	$hoje=date("Y-m-d");

	$sql='SELECT * FROM products_objs_history_day WHERE Supplier_ID <> '.$obj_Supplier_ID.'
	AND pvp>0 AND Stock>0 AND ID_ORG="'.$obj_id.'"
	AND (DATE(data_i)="'.$hoje.'" OR DATE(data_u)="'.$hoje.'") ORDER BY pvp asc,ID DESC';

	$rs2->db_open($db_link,$GLOBALS['DB_name'],$sql);
	$object['pvp']='';
	if(!$rs2->EOF())
	{
		//Nao deve entrar aqui caso o preco mais baixo seja do mesmo fornecedor que esta a ser importado
		$object['ID']= $rs2->db_data['ID'];
		$object['ID_ORG']= $rs2->db_data['ID_ORG'];
		$object['iva']= $rs2->db_data['iva'];
		$object['pvp']= $rs2->db_data['pvp'];
		$object['Stock']= $rs2->db_data['Stock'];
		$object['Supplier_ID']= $rs2->db_data['Supplier_ID'];
		$object['data_i']= $rs2->db_data['data_i'];
	}
	$rs2->db_close();
	return $object;
}

function get_higest_price($obj_id,$obj_Supplier_ID=0)
{
	$db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs2=new DataObj;
	$hoje=date("Y-m-d");

	$sql='SELECT * FROM products_objs_history_day WHERE Supplier_ID <> '.$obj_Supplier_ID.'
	AND pvp>0 AND Stock>0 AND ID_ORG="'.$obj_id.'"
	AND (DATE(data_i)="'.$hoje.'" OR DATE(data_u)="'.$hoje.'") ORDER BY pvp desc,ID DESC';

	$rs2->db_open($db_link,$GLOBALS['DB_name'],$sql);
	$object['pvp']='';
	if(!$rs2->EOF())
	{
		//Nao deve entrar aqui caso o preco mais baixo seja do mesmo fornecedor que esta a ser importado
		$object['ID']= $rs2->db_data['ID'];
		$object['ID_ORG']= $rs2->db_data['ID_ORG'];
		$object['iva']= $rs2->db_data['iva'];
		$object['pvp']= $rs2->db_data['pvp'];
		$object['Stock']= $rs2->db_data['Stock'];
		$object['Supplier_ID']= $rs2->db_data['Supplier_ID'];
		$object['data_i']= $rs2->db_data['data_i'];
	}
	$rs2->db_close();
	return $object;
}

function get_higher_stock($obj_id,$obj_Supplier_ID=0)
{
	$db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs2=new DataObj;
	$hoje=date("Y-m-d");
	$sql='SELECT * FROM products_objs_history_day WHERE Supplier_ID <> '.$obj_Supplier_ID.' AND pvp>0 AND Stock>0 AND ID_ORG="'.$obj_id.'" AND (DATE(data_i)="'.$hoje.'" OR DATE(data_u)="'.$hoje.'") ORDER BY Stock desc,ID DESC';
	//$sql='SELECT * FROM products_objs_history_day WHERE pvp > 0 and ID_ORG="'.$obj_id.'" ORDER BY Stock desc,ID DESC';
	$rs2->db_open($db_link,$GLOBALS['DB_name'],$sql);
	$object['Stock']='';
	$object['pvp']='0';
	if(!$rs2->EOF())
	{
		//Nao deve entrar aqui caso o preco mais baixo seja do mesmo fornecedor que esta a ser importado
		$object['ID']= $rs2->db_data['ID'];
		$object['ID_ORG']= $rs2->db_data['ID_ORG'];
		$object['iva']= $rs2->db_data['iva'];
		$object['pvp']= $rs2->db_data['pvp'];
		$object['Stock']= $rs2->db_data['Stock'];
		$object['Supplier_ID']= $rs2->db_data['Supplier_ID'];
		$object['data_i']= $rs2->db_data['data_i'];
	}
	$rs2->db_close();
	return $object;
}

function mostra_produtos($prod_page)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$rs2=new DataObj;

	echo '<style>
	.prd_d {min-height: 300px; background:transparent; max-height:300px; border:0 }
	.prd_d .prd_img {background:#fff;height:280px}
	.prd_d .prd_title {color: #000;font-weight:600}</style>';

	$sql='SELECT obj_type_id,obj_pvp,obj_pvp2,obj_stock,
			P.ID,P.Ordem,P.fornecedor_importacao,P.Ref as Ref_Original,P.Ref_secundaria as Ref,P.Ref_Ean,P.Peso,P.peso_cliente,P.Carrinho,P.ID_Marca,P.Preco1
			FROM products_objs
			INNER JOIN products P on P.ID=products_objs.obj_type_id
			WHERE P.Aproved=1 AND products_objs.obj_ativo=1 group by products_objs.obj_type_id limit 5000';//mais de 2.000 produtos,é lento
	echo '<div class="catprod_wrap">';
	echo '<div id="product_show">';
	echo '<ul>';
	$rs->db_open($db_link,DB_NAME,$sql);
	while(!$rs->EOF())
	{
		$m_id=$rs->db_data['obj_type_id'];
		$prods=\get_produtc_info($m_id,1);
		if(is_array($prods))
		{
			$new_prods='';
			if(!empty($new_prods))
			{
				$prods=$new_prods;
			}
			$prod_id=$m_id;
			$link=$site_http_url.\seo2(2,$prod_page,$prods,$GLOBALS['site_lang']);

			$precos=$prods['prod_precos'];

			if($prods['prd_imgm_med']) $img=$prods['prd_imgm_med'];
			else $img=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo.png';

			echo '<li class="item item">';
			echo '<div class="prd_d" id="prd_d_'.$m_id.'">';
			echo '<div class="prd_img">';
			echo '<a title="'.addslashes($prods['prod_nome']).'" href="'.$link.'" style="text-decoration:none"><span class="helper"></span>';
			echo '<img alt="..." src="'.$img.'" class="lazy" id="img_101393" data-original="" border="0">';
			echo '</a>';
			echo '</div>';
			echo '<div class="prd_layer">';
			echo '<div class="prd_title"><a title="'.addslashes($prods['prod_nome']).'" href="'.$link.'" target="_top">'.addslashes($prods['prod_nome']).'</a>';
			echo '<div class="prod_descricao"><a title="'.addslashes($prods['prod_descricao2']).'" href="'.$link.'" target="_top">'.addslashes($prods['prod_descricao']).'</a>';
			echo '</div>';
			echo '</div>';
			echo '</div>';
			echo '</li>';
			$j++;
		}
		$rs->db_next();
	}
	echo '</ul>';
	echo '</div>';
	echo '</div>';

	$prods=\get_produtc_info(0,'',0,$fam_id);
	$return='';
	if($prods!==NULL)
	{
		$url=$site_http_url.\seo2(2,$GLOBALS['products_page_id'],$prods,$GLOBALS['site_lang']);
		$return .='<div style="float:left;width:300px;cursor:pointer" onclick="window.open(\''.$url.'\',\'_self\')"><div class="produto_wrapper2" style="background:#FFF;padding:10px;height:100%">';
		$return .='<table border="0" cellpadding="0" cellspacing="0" width="100%">';
		$return .='<tr>';
		$return .='<td align="center" rowspan="2" width="150" height="120"><div class="figura"><img src="'.$prods['prd_imgm_new'].'" alt="" /></div></td>';
		$return .='<td style="padding:0 5px">';
		$return .='<div class="titulo" style="font-size:12px;font-weight:bold !important;word-break:break-all">'.$prods['prod_nome'].'</div>';
		$return .='<div class="subtitulo" style="font-size:12px">'.$prods['prod_subtitulo'].'</div>';
		$return .='<div class="referencia" style="font-size:12px">'.$prods['prod_precos']['Modelo'].'</div>';
		$return .='</td>';
		$return .='</tr>';
		$return .='<tr>';
		$return .='<td>';
		$return .='<div width="60" class="preco">'.numberformat($prods['prod_precos']['PVP']).'</div>';
		$return .='</td>';
		$return .='</tr>';
		$return .='</table>';
		$return .='</div></div>';
	}
	return $return;
}

function mostra_produto_detalhe($prods)
{
	$m_id=intval($_GET['prod_id']);
	$prods=\get_produtc_info($m_id,1);
	$new_prods=verifica_promo($prods['prod_id'],$prods['prod_precos']);
	if(!empty($new_prods))
	{
		$prods=$new_prods;
	}
	global $tem_produtos_visualizados;
    $dados_cliente=cliente_logado();
    if($tem_produtos_visualizados)
    {
	    ?><script>$(document).ready(function(){$.ajax({type: "POST",url: SITE_HTTP_AJAX_URL + "addRemoveProductVisualizados.php",data: {action: "add",prodid: <?php echo $prods['prod_id']; ?>}}).done(function (resultado) {});});</script><?php
	}
	$return='<link rel="stylesheet" href="layout/'.PROJETO.'/css/css_external/product_v_'.filemtime('layout/'.PROJETO.'/css/css_external/product.css').'.css" media="all"/> ';
	$return .= '<script>quantityAvailable=9999;</script>';
	$return .= '<script type="text/javascript" src="layout/'.PROJETO.'/css/css_external/product.js"></script> ';
	$return .= '<div style="padding:30px;text-align:center">';
    $return .= BREADCRUMBS(1,intval($prods['fam_id']));
	$return .= '</div>';
	$return .= '<div style="padding:20px;text-align:left;padding-bottom:50px">';
    $return .= '<div style="font-size:40px">AZEITES</div>';
	$return .= '</div>';
	$tem_qtt=0;
	$label_promo='';
	$precos=$prods['prod_precos'];
	$PODE_ENCOMENDAR=$precos['PODE_ENCOMENDAR'];
	$today=date("U");
	if((($precos['STOCK'] > 0 || $PODE_ENCOMENDAR == 1) && $prods['prod_precos']['PVP'] > 0 && $prods['prod_carrinho'] == 1))
	{
		/*if($prods['prod_precos']['PROMOCAO'] == 1 && $prods['prod_precos']['PRECO_ANTIGO'] > 0 && $prods['prod_precos']['PRECO_ANTIGO'] > $prods['prod_precos']['PVP'] && (strtotime($prods['prod_precos']['Promo_data_ini']) <= $today && strtotime($prods['prod_precos']['Promo_data_fim']) >=$today))
		{
			$label_promo='<div class="ribbon-wrapper2"><img src="layout/'.PROJETO.'/images_gerado/promo.png" alt="" /></div>';
		}*/

		if($prods['prod_precos']['PROMOCAO'] == 1 && (strtotime($prods['prod_precos']['Promo_data_ini']) <= $today && strtotime($prods['prod_precos']['Promo_data_fim']) >=$today))
		{
			$label_promo='<div class="ribbon-wrapper2"><img src="layout/'.PROJETO.'/images_gerado/promo.png" alt="" /></div>';
		}
	}
    $return .= '<div class="primary_block row">';
    $return .= '<div class="pb-left-column col-xs-12 col-sm-12 col-md-6">';
    $tam=count($prods['prd_imgs']['img_cart']);
	if($prods['u_catetiq'] )
	{
		if($prods['u_imagetiq'] != '')
		{
			$temp=explode(".",$prods['u_imagetiq']);
			if(strtolower(end($temp)) == "pdf")
			{
				$return .= '<a href="'.$prods['u_imagetiq'].'" target="_blank" class="eenergia" style="background-color:'.$prods['u_coretiq'].'; border-right-color:'.$prods['u_coretiq'].'">'.$prods['u_catetiq'].'</a>';
			}
			else
			{
				$return .= '<a class="eenergia fancybox" style="background-color:'.$prods['u_coretiq'].'; border-right-color:'.$prods['u_coretiq'].';cursor:pointer;" href="'.$prods['u_imagetiq'].'">'.$prods['u_catetiq'].'</a>';
			}
		}
		else
		{
			$return .= '<div class="eenergia" style="background-color:'.$prods['u_coretiq'].'; border-right-color:'.$prods['u_coretiq'].'">'.$prods['u_catetiq'].'</div>';
		}
	}
	if(!strpos($prods['prd_imgs']['img'],'no_photo') !== false)
	{
		$img_type=array('img_cart','img_med','img_new','img_news','img_fb');
		$m=1;
		foreach ($img_type as $imty)
		{
			if(strpos($prods['prd_imgs'][$imty],'no_photo') !== false)
			{
		    	$m=0;
		    	break;
			}
		}
		if(!$m){$newimg=gerarneededimages($prods['prod_id']);}
	}
	$return .= "<div class='product-gallery-block resp1 seraestam'>";
	$return .= '<div class="campanhas-prods" style="display:none;position:absolute;top:0;z-index:9999"></div>';
	$return .= "<ul class='product-gallery'>";
	if(!substr_count($prods['prd_imgm_med'],'no_photo'))
	{
		for ($figi=0; $figi < $tam; $figi++)
		{
			if($figi==0)
			{
				$return .= '<li class="item active" data-thumb="'.$prods['prd_imgs']['img_med'][$figi].'">';
				$return .= $label_promo;
				$return .= '<span class="helper"></span>';
				if($tam>1)$return .= '<a href="'.$prods['prd_imgs']['img'][$figi].'" target="_blank" class="fancybox" id="big'.$figi.'" data-fancybox-group="gallery"><img class="zoom" src="'.$prods['prd_imgs']['img_med'][$figi].'" alt="'.rtrim($prods['prod_nome'],'-').'" border="0" /></a>';
				else $return .= '<a href="'.$prods['prd_imgs']['img'][$figi].'" target="_blank" class="fancybox" id="big'.$figi.'" data-fancybox="gallery"><img class="zoom" src="'.$prods['prd_imgs']['img_med'][$figi].'" alt="'.rtrim($prods['prod_nome'],'-').'" border="0" /></a>';
				$return .= '</li>';
			}
			else
			{
				$return .= '<li class="item" data-thumb="'.$prods['prd_imgs']['img_med'][$figi].'">'.$label_promo.'<span class="helper"></span><a href="'.$prods['prd_imgs']['img'][$figi].'" target="_blank" class="fancybox" id="big'.$figi.'" data-fancybox-group="gallery"><img src="'.$prods['prd_imgs']['img_med'][$figi].'" alt="'.rtrim($prods['prod_nome'],'-').'" border="0" /></a></li>';
			}
		}
	}
	else
	{
		$return .= '<li class="item active"  data-thumb="'.$prods['prd_imgs']['img_med'][$figi].'">'.$label_promo.'<span class="helper"></span><img src="'.$prods['prd_imgm_med'].'" alt="'.rtrim($prods['prod_nome'],'-').'" border="0" /></li>';
	}
	$return .= '</ul>';
	$return .= "</div>";
	$return .= "<div class='product-gallery-block resp2'>";
	$return .= '<div class="campanhas-prods" style="display:none;position:relative;z-index:9999"></div>';
	$return .= "<ul class='product-gallery-resp_'>";
	if(!substr_count($prods['prd_imgm_med'],'no_photo'))
	{
		for ($figi=0; $figi < $tam; $figi++)
		{
			if($figi==0) {$return .= '<li class="item active" data-thumb="'.$prods['prd_imgs']['img_med'][$figi].'">'.$label_promo.'<span class="helper"></span><a href="'.$prods['prd_imgs']['img'][$figi].'" target="_blank" class="fancybox" id="big'.$figi.'" data-fancybox-group="gallery"><img src="'.$prods['prd_imgs']['img_med'][$figi].'" alt="'.rtrim($prods['prod_nome'],'-').'" border="0" /></a></li>';}
			else {$return .= '<li class="item" data-thumb="'.$prods['prd_imgs']['img_med'][$figi].'">'.$label_promo.'<span class="helper"></span><a href="'.$prods['prd_imgs']['img'][$figi].'" target="_blank" class="fancybox" id="big'.$figi.'" data-fancybox-group="gallery"><img src="'.$prods['prd_imgs']['img_med'][$figi].'" alt="'.rtrim($prods['prod_nome'],'-').'" border="0" /></a></li>';}
		}
	}
	else
	{
		$return .= '<li class="item active"  data-thumb="'.$prods['prd_imgs']['img_med'][$figi].'">'.$label_promo.'<span class="helper"></span><img src="'.$prods['prd_imgm_med'].'" alt="'.rtrim($prods['prod_nome'],'-').'" border="0" /></li>';
	}
	$return .= '</ul>';
	$return .= '</div>';
    $tem_filtros=0;
    $filtros=filtros_ficha_produto($prods['prod_id'],$prods['fam_id']);
    if($filtros) $tem_filtros=1;

	$return .= '</div>';
	$return .= '<div class="pb-center-column col-xs-12 col-sm-12 col-md-6" style="padding-left:15px">
	<div class="row">
	<div class="col-sm-12">
		<h1 itemprop="name" class="titulo-prod">'.rtrim($prods['prod_nome'],'-').'</h1>';
			$return .= ($prods['prod_descricao2']).'<br>'.'<hr><br>';
			$return .= ($prods['prod_como_funciona']);
			$return .= ($prods['prod_envio']);

	if($prods['Ref_Ean'] != '') $return .= '<h5 style="margin-top:0"><strong>EAN '.$prods['Ref_Ean'].'</strong></h5>';
	$return .= '<style> .awesome-content{display:inline-block; width: 10px; height: 10px; opacity:0.8; font-size: 13px; margin-left: 4px; margin-top: -2px}</style>';
	$return .= '<div class="inputs">';
	$filtros2=filtros_lista_produto($prods['prod_id'],$prods['fam_id']);
	if($filtros2)
	{
		$return .= '<div class="descricao">';
		$return .= $filtros2;
		$return .= '</div>';
	}

	$return .= '</div></div></div>';
	$return .= '<div class="clear"></div>';
    $return .= '</div><div class="clear"></div>';
	$return .= '</div>';
	$return .= '</div>';

    return $return;
}

function gera_resumo($prod_id,$combo,$quantidade_id,$largura=0,$altura=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$m_http_url_images,$site_http_url,$tema,$site_lang,$m_http_url_images;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new Dataobj;
	$image_path=$m_http_url_images."products/";
	$sql="Select img_ext,ID from products_images where ID_ORG='".$prod_id."' and Ordem=1";
	//$return=$sql;
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$image_ext=$rs->db_data["img_ext"];
		$fam_id=$rs->db_data["ID"];
	}
	$rs->db_close();
	$return='<div class="col-md-2"><img src="'.$image_path.$prod_id.'_1_prod.png" width="100%"></div>';
	$combos_separados=explode(";",$combo);
	// vamos buscar o nome do produto
	$rs1=new Dataobj;
	$sql='Select p.ID,pl.Cabecalho,p.qnt_esp from products as p inner join products_lang as pl on pl.ID_PAI=p.ID where p.ID='.$prod_id;
	$rs1->db_open($db_link,$DB_name,$sql);
	$nome_produto=$rs1->db_data["Cabecalho"];
	$qnt_esp=$rs1->db_data["qnt_esp"];
	$rs1->db_close();
	// cria a string com o nome dos filtros do produto selecionado
	$string_do_produto='';
	foreach($combos_separados as $values){
		$componentes=explode("-",$values);
		$id_cat=$componentes[0];
		$id_value=$componentes[1];
		$rs2=new Dataobj;
		if($id_cat!='' && $id_value!='')
		{
			$sql='Select f.value_id,fl.values_name from filters_values as f inner join filters_values_lang as fl on fl.values_id=f.value_id where f.category_id='.$id_cat.' and .f.value_id='.$id_value ;
			$rs2->db_open($db_link,$DB_name,$sql);
			$string_do_produto.=" ".$rs2->db_data["values_name"].";";
			//$return.=$rs2->db_data["values_name"].' - ';
			$rs2->db_close();

		}
	}
	// vamos chamar a funcao do tempo de realização
	$data_realizacao=tempo_realizacao($quantidade_id);

	$return .='<input type="hidden" id="quantidade_mult" value="1">'; // esta variavel servirá para podermos saber o multiplo do produto que o cliente deseja
	if($data_realizacao>0)
	{
		if($qnt_esp==1)
		{
			$return.='<div class="col-md-10"><p class="nome_produto_resumo">'.$nome_produto.'</p><p class="string_produto_resumo">'.$string_do_produto.'</p><p class="hora_encomenda">Encomenda até: <span id="hora_encomenda_span"></span></p><p class="realizacao">Data estimada de entrega: <span id="t_realizacao"> '.$data_realizacao.'</span></p><p class="qtd_resumo_largura">Largura : <span id="unidades_largura"></span> cm</p><p class="qtd_resumo_altura">Altura : <span id="unidades_altura"></span> cm</p></div>';
			$return.='<script>
				var largura=$("#largura").val();
				var altura=$("#altura").val();
				$("#unidades_largura").append(largura);
				$("#unidades_altura").append(altura);
			</script>';
			$return.=countdow_realizacao();
		}
		else
		{
			$return.='<div class="col-md-10"><p class="nome_produto_resumo">'.$nome_produto.'</p><p class="string_produto_resumo">'.$string_do_produto.'</p><p class="hora_encomenda">Encomenda até: <span id="hora_encomenda_span"></span></p><p class="realizacao">Data estimada de entrega: <span id="t_realizacao"> '.$data_realizacao.'</span></p><p class="qtd_resumo">Qtd.: <span id="unidades_resumo">1</span></p></div>';
			// vamos chamar a função do countdown para a encomenda
			$return.=countdow_realizacao();

		}
	}


	return $return;
}

function countdow_realizacao(){

	// vamos fazer a função js do countdown da encomenda
	//1º verificar o dia não é um fim de semana ou uma sexta depois das 18h
	$dia_semana=date("w"); // 0-Domingo 6-Sabado
	$hora=date("G"); // vamos determinar que as encomendas podem chegar até as 18h caso contrário ficará para o proximo dia

	if($dia_semana=='0' || $dia_semana=='7' || $dia_semana=='6')
	{
		$data_alvo=strtotime("next Monday");
	}else{
		if($hora=='18' || $hora=='19' || $hora=='20' || $hora=='21' || $hora=='22' || $hora=='23')
		{
			$data_alvo=strtotime("+1 day");
		}else{
			$data_alvo=strtotime("now");
		}
	}
	$data=date("M j,Y",$data_alvo);
	$return.='<script>
	var countDownDate = new Date("'.$data.' 18:00:00").getTime();

	var x = setInterval(function() {
		var now = new Date().getTime();
		var distance = countDownDate - now;

		var days = Math.floor(distance / (1000 * 60 * 60 * 24));
		var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
		var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
		var seconds = Math.floor((distance % (1000 * 60)) / 1000);

		if(days==0)
		{
			document.getElementById("hora_encomenda_span").innerHTML =  hours + "h "+ minutes + "m " + seconds + "s ";
		}else{
			document.getElementById("hora_encomenda_span").innerHTML = days + "d " + hours + "h "+ minutes + "m " + seconds + "s ";
		}

	},1000);
	</script>';
	return $return;
}

function tempo_realizacao($opcao_selecionada)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$m_http_url_images,$site_http_url,$tema,$site_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$dias=0;
	$sql='Select ID,dias from entrega where ID='.$opcao_selecionada;
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$dias=$rs->db_data["dias"];
	}
	$rs->db_close();
	if($dias==0)return;
	$data_a_realizar=strtotime("+".$dias." weekdays");
	$data=date("d-m-Y",$data_a_realizar);
	return $data;
}

function buscar_precos_resumo($prod_id,$combo,$quantidade,$gerado_de_inicio=0,$largura,$altura)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$m_http_url_images,$site_http_url,$tema,$site_lang,$m_http_url_images;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;

	$qnt_esp=get_qnt_esp($prod_id);
	if($qnt_esp['flag_esp']==1)
	{
		if($prod_id!='' && $combo!='' && $gerado_de_inicio=1 && $altura==0 && $altura==0)
		{
			//nececitamos de calcular a área do produto antes de fazermos a query
			$area=($qnt_esp['L_padrao']/100)*($qnt_esp['A_padrao']/100);
			//1º vamos buscar o preço com iva  e verificar se é um produto em promoção ou não
			$sql='Select products_objs_combo.obj_id,products_objs_combo.obj_iva,products_objs_combo.obj_pvp,products_objs_combo.obj_promo,products_objs.obj_promo_ativo,products_objs_combo.obj_qtdmin from products_objs_combo INNER JOIN products_objs ON products_objs.obj_type_id=products_objs_combo.obj_type_id where products_objs_combo.obj_combo2="'.$combo.'" and products_objs_combo.obj_type_id='.$prod_id.' and products_objs_combo.obj_qtdmin < '.$area.' order by products_objs_combo.obj_qtdmin DESC';
			$rs->db_open($db_link,$DB_name,$sql);
			$promo=$rs->db_data['obj_promo_ativo'];
			$obj_qtdmin=$rs->db_data['obj_qtdmin'];
			$promo_preco=$rs->db_data['obj_promo']*$area;
			//$promo_preco=number_format(($rs->db_data['obj_promo'])*$area,2);
			$iva=intval($rs->db_data['obj_iva'])/100;
			$preco_Civa=$rs->db_data['obj_pvp']*$area;
			//$preco_Civa=number_format(($rs->db_data['obj_pvp'])*$area,2);
			/*if($promo=1 && $promo_preco > 0 && $promo_preco < $preco_Civa )*/
			if($promo=1 && $promo_preco > 0)
			{
				$preco_Civa=$promo_preco;
			}
			$preco_Siva=$preco_Civa*(1-$iva);
			$rs->db_close();
		}
		else if($prod_id!='' && $combo!='' && $largura >0 && $altura >0  && $gerado_de_inicio=1)
		{
			//nececitamos de calcular a área do produto antes de fazermos a query
			$area=($altura/100)*($largura/100);
			//1º vamos buscar o preço com iva
			$sql='Select products_objs_combo.obj_id,products_objs_combo.obj_iva,products_objs_combo.obj_pvp,products_objs_combo.obj_promo,products_objs.obj_promo_ativo,products_objs_combo.obj_qtdmin from  products_objs_combo INNER JOIN products_objs ON products_objs.obj_type_id=products_objs_combo.obj_type_id where products_objs_combo.obj_combo2="'.$combo.'" and products_objs_combo.obj_type_id='.$prod_id.' and products_objs_combo.obj_qtdmin < '.$area.' order by products_objs_combo.obj_qtdmin DESC';
			$rs->db_open($db_link,$DB_name,$sql);
			$iva=intval($rs->db_data['obj_iva'])/100;
			$promo=$rs->db_data['obj_promo_ativo'];
			$obj_qtdmin=$rs->db_data['obj_qtdmin'];
			$promo_preco=$rs->db_data['obj_promo']*$area;
			$preco_Civa=$rs->db_data['obj_pvp']*$area;
			/*if($promo=1 && $promo_preco > 0 && $promo_preco < $preco_Civa )*/
			if($promo=1 && $promo_preco > 0 )
			{
				$preco_Civa=$promo_preco;
			}
			$preco_Siva=$preco_Civa/(1+$iva);
			$rs->db_close();
		}
	}
	else
	{
		if($prod_id!='' && $combo!='' && $quantidade!='' && $gerado_de_inicio=0)
		{
			//1º vamos buscar o preço com iva  e verificar se é um produto em promoção ou não
			$sql='Select products_objs_combo.obj_id,products_objs_combo.obj_iva,products_objs_combo.obj_pvp,products_objs.obj_promo_ativo,products_objs_combo.obj_promo,products_objs_combo.obj_qtdmin from  products_objs_combo INNER JOIN products_objs on products_objs.obj_type=products_objs_combo.obj_type_id   where products_objs_combo.obj_combo2="'.$combo.'" and products_objs_combo.obj_type_id='.$prod_id.' and products_objs_combo.obj_qtdmin='.$quantidade;
			$rs->db_open($db_link,$DB_name,$sql);
			$promo=$rs->db_data['obj_promo_ativo'];
			$promo_preco=$rs->db_data['obj_promo'];
			$iva=intval($rs->db_data['obj_iva'])/100;
			$obj_qtdmin=$rs->db_data['obj_qtdmin'];
			$preco_Civa=$rs->db_data['obj_pvp'];
			/*if($promo=1 && $promo_preco > 0 && $promo_preco < $preco_Civa )*/
			if($promo=1 && $promo_preco > 0 )
			{
				$preco_Civa=$promo_preco;
			}
			$preco_Siva=$preco_Civa*(1-$iva);
			$rs->db_close();
		}
		else if($prod_id!='' && $combo!='' && $quantidade!='' && $gerado_de_inicio=1)
		{
			//1º vamos buscar o preço com iva
			$sql='Select products_objs_combo.obj_id,products_objs_combo.obj_iva,products_objs_combo.obj_pvp,products_objs_combo.obj_promo,products_objs.obj_promo_ativo,products_objs_combo.obj_qtdmin from products_objs_combo INNER JOIN products_objs ON products_objs.obj_type_id=products_objs_combo.obj_type_id where products_objs_combo.obj_combo2="'.$combo.'" and products_objs_combo.obj_type_id='.$prod_id.' order by products_objs_combo.obj_qtdmin ASC' ;
			$rs->db_open($db_link,$DB_name,$sql);
			$iva=intval($rs->db_data['obj_iva'])/100;
			$promo=$rs->db_data['obj_promo_ativo'];
			$promo_preco=$rs->db_data['obj_promo'];
			$preco_Civa=$rs->db_data['obj_pvp'];
			$obj_qtdmin=$rs->db_data['obj_qtdmin'];
			/*if($promo=1 && $promo_preco > 0 && $promo_preco < $preco_Civa )*/
			if($promo=1 && $promo_preco > 0 )
			{
				$preco_Civa=$promo_preco;
			}
			$preco_Siva=$preco_Civa/(1+$iva);
			$rs->db_close();
		}
	}
	//$preco_Siva=numberformat($preco_Siva);
	//$preco_Civa=numberformat($preco_Civa);
	return array('preco_Civa'=>$preco_Civa,'preco_Siva'=>$preco_Siva,'sql'=>$area,'obj_qtdmin'=>$obj_qtdmin);

}

function gera_resumo_carrinho_grafica($prod_id,$combo,$quantidade_id,$largura=0 ,$altura=0 ,$ajax=0)
{
	if($prod_id!='' && $combo!='' && $quantidade_id!='')
	{
	$return='<div class="col-xs-10">';

	$return.='<div id="quantidade_div"><p class="quantidade_prod"> <span id="qnt_prod_span"></span></p></div>';
	$return.='<div class="div_precos">';
	$return.='<p class="liquid">Preço líquido <span id="peco_liq"></span></p>';
	$return.='<p class="preco_final">Preço com IVA <span id="peco_fin"></span></p>';
	$return.='</div>';
	$return.='<div class="adicion_carr" onclick="adicionarCarrinhoProdutoOpcoes('.$prod_id.',$(\'#quantidade_mult\').attr(\'value\'));" >adicionar ao carrinho</div>';
	$return.='</div>';
	$return.='<div class="col-xs-2 no_padding">';
	$return.='<p class="quant_text">Qtd.</p>';
	$return.='<div class="menos_qtd" onclick="muda_qnt(1);">-</div>';
	$return.='<div class="mais_qtd" onclick="muda_qnt(2);">+</div>';
	$return.='</div>';
	$dados= buscar_precos_resumo($prod_id,$combo,$quantidade_id,1,$largura,$altura);

	//var_dump($dados);

	//$return.=$dados['sql'];
	$preco_Civa=$dados['preco_Civa'];
	$preco_Siva=$dados['preco_Siva'];
	$quantidade_id=intval($dados['obj_qtdmin']);
	$qnt_esp=get_qnt_esp($prod_id);
	if($qnt_esp['flag_esp']==1)
	{
		$quantidade_id=1;
	}
	//$return.=$dados['sql'];
	$return.='<script>
		$("#peco_liq").empty();
		$("#peco_liq").append("'.$preco_Siva.'");
		$("#peco_fin").empty();
		$("#peco_fin").append("'.$preco_Civa.'");
		$("#qnt_prod_span").empty();
		$("#qnt_prod_span").append("'.$quantidade_id.'");
		$("#unidades_resumo").empty();
		$("#unidades_resumo").append("'.$quantidade_id.'");

	function muda_qnt(funcao){

		var qnt=$("#quantidade_mult").attr("value");

		console.log("funcao = "+funcao);
		console.log("quantidade_mult.value = qnt = "+qnt);


		if(funcao==1)
		{

			if(qnt>1)
			{
				qnt=parseInt(qnt)-1;
				$("#quantidade_mult").attr("value",qnt);
			}
		}
		if(funcao==2)
		{
			qnt=parseInt(qnt)+1;
			$("#quantidade_mult").attr("value",qnt);

			console.log("qnt = "+qnt);

		}

		var qnt_esp=$("#qnt_esp").attr("value");

			console.log("qnt_esp = "+qnt_esp);


		if(qnt_esp==0)
		{
			var quantidadet=$(".choose_quant_active").attr("value");
			var quantidade_fin= parseInt(quantidadet)*parseInt(qnt);
			$("#qnt_prod_span").empty();
			$("#qnt_prod_span").append(quantidade_fin);
			$("#unidades_resumo").empty();
			$("#unidades_resumo").append(quantidade_fin);

			console.log("quantidadet = "+quantidadet);
			console.log("quantidade_fin = "+quantidade_fin);


		}
		else
		{
			$("#qnt_prod_span").empty();
			$("#qnt_prod_span").append(qnt);
			$("#unidades_resumo").empty();
			$("#unidades_resumo").append(qnt);
		}


		var codes="";
		total_selected=0;
		$("select.opcoesprod").find("option:selected").each(function()
		{
			cat_id=$(this).attr("data-catid");
			val_id=$(this).attr("data-valueid");
			if(cat_id > 0 && val_id > 0)
			{
				codes += cat_id+"-"+val_id+";";
				total_selected++;
			}
		});
		var prod_id='.$prod_id.';
		var array_serv=$("#serv_code").attr("value");
		var tempo= $("#tempo_entrega").val();
		var mult=$("#quantidade_mult").attr("value");
		var qnt=0;
		var altura=0;
		var largura=0;
		if(qnt_esp==1)
			{
				largura=$("#largura").attr("value");
				altura=$("#altura").attr("value");
			}else{
			 qnt=$(".choose_quant_active").attr("value");
			}

		$.ajax(
			{
			type: "POST",
			url: SITE_HTTP_AJAX_URL + "ajaxShop_quantidade_grafica.php",
			data: { codes:codes,prodid:prod_id,tempo:tempo,servicos:array_serv,multiple:mult,qnt:qnt,alt:altura,lar:largura}
			})
			.done(function(res)
			{
				$(".container_info_quantidade").empty();
				$(".container_info_quantidade").append(res);
			});


		qnt=$(".choose_quant_active").attr("value");

		$.ajax(
			{
			type: "POST",
			url: SITE_HTTP_AJAX_URL + "ajaxShop_mudanca_preco_grafica.php",
			data: { codes:codes,prodid:prod_id,tempo:tempo,servicos:array_serv,multiple:mult,qnt:qnt,alt:altura,lar:largura}
			})
			.done(function(res)
			{
				var result=res.split(",");
				$("#peco_fin").empty();
				$("#peco_fin").append(result[0]);
				$("#peco_liq").empty();
				$("#peco_liq").append(result[1]);
			});

	}
	</script>';
	//$precos=buscar_precos($prod_id,$combo,$quantidade_id);
	}
	return $return;
}


function guarda_ficheiros_enviados($file)
{
	if($file['file']['name']!='')
	{
		$pos=strrpos($file['file']['name'],".");
		$img_ext=substr($file['file']['name'],$pos+1,strlen($file['file']['name'])+1-$pos);
		$new_file_name=uniqid().'.'.$img_ext;
		$destino='../files/imagens_clientes/'.$new_file_name;
		$res=copia_segura_ficheiros_clientes($file['file']['tmp_name'],$destino);
		if($res)
		{
			return $new_file_name;
		}else{
			return false;
		}
	}else{
		return false;
	}

}

function copia_segura_ficheiros_clientes($image,$dest)
{
	//Vamos verificar qual o tipo de ficheiro:
	$original_extension=pathinfo($dest,PATHINFO_EXTENSION);
	$dest2=str_replace('.'.$original_extension,'',$dest);
	if($original_extension == 'jpg'){ return copy($image,$dest2.'.jpg'); }
	if($original_extension == 'JPG'){ return copy($image,$dest2.'.JPG'); }
	if($original_extension == 'png'){ return copy($image,$dest2.'.png'); }
	if($original_extension == 'PNG'){ return copy($image,$dest2.'.PNG'); }
	if($original_extension == 'gif'){ return copy($image,$dest2.'.gif'); }
	if($original_extension == 'GIF'){ return copy($image,$dest2.'.GIF'); }
	if($original_extension == 'pdf'){ return copy($image,$dest2.'.pdf'); }
	if($original_extension == 'PDF'){ return copy($image,$dest2.'.PDF'); }
	if($original_extension == 'tiff'){ return copy($image,$dest2.'.tiff'); }
	if($original_extension == 'TIFF'){ return copy($image,$dest2.'.TIFF'); }
	if($original_extension == 'ai'){ return copy($image,$dest2.'.ai'); }
	if($original_extension == 'AI'){ return copy($image,$dest2.'.AI'); }
	if($original_extension == 'HEIC'){ return copy($image,$dest2.'.HEIC'); }
	if($original_extension == 'heic'){ return copy($image,$dest2.'.heic'); }

	return false;
}

function somadatasfloa($dias){
    $Date = date('Y-m-d');

    return  date('d/m/Y', strtotime($Date. ' + '.$dias.' days'));


}

function layoutFloa($total){
 
    $total3 = numberformat(round(($total/3),2));
   $total4 = numberformat(round(($total/4),2));
    $modal = "";
    $m3 = 1;
    $m4 = 1;
//echo $total;

    $txt =' <style>
         .pp-floa-simu {
            display: flex;
            gap: 5px;
            flex-direction: row;
            flex-wrap: wrap;
        }
        .pp-floa-simu .multichoose {
            width: auto;
            margin: 0;
			height: 30px;
        }
        
         .multichoose {
            background-color: #fff;
            border: solid 1px #CECECE;
            border-radius: 24px;
            display: flex;
            justify-content: space-between;
            width: 80%;
            margin: 12px auto;
        }
        
        .multichoose div.selected {
            background-color: #009FFF;
            color: #fff;
        }
        
        .pp-floa-simu .multichoose div {
            padding: 0 15px;
        }
        .multichoose div {
            border-radius: 24px;
            width: 50%;
            background-color: #fff;
            color: #3A3A3A;
            font-size: 13px;
            text-align: center;
            font-weight: bold;
            cursor: pointer;
            line-height: 28px;
        }
           .breadcrumbfloa {
            width: 80% !important;
            margin: 0 auto;
        }
        
        .breadcrumbfloa ul, .tippy-box .breadcrumbfloa ul {
            margin: 0;
            padding: 0;
            line-height: 35px;
            }
            .breadcrumbfloa ul li, .tippy-box .breadcrumbfloa ul li {
            list-style: none;
            position: relative;
        }
        
        ul {
            margin: 0;
            padding: 0;
            list-style: none;
        }
        
        .breadcrumbfloa .fcontainer, .tippy-box .breadcrumbfloa .fcontainer {
            display: flex;
            justify-content: space-between;
        }
        
         .fraisadd2 {
            margin-right: 35px;
            margin-left: 20px;
        }
        
        .fraisadd2 {
            font-size: 14px;
            color: #009fff;
            display: flex;
            justify-content: space-between;
            background-color: #fff;
            padding: 10px 10px 0;
            border-radius: 8px;
            margin-right: 26px;
        }
        
        .breadcrumbfloa ul li.active::before, .breadcrumbfloa ul li.active::before {
    background: rgb(0, 159, 255);
    background: -moz-linear-gradient(180deg, rgba(0, 159, 255, 1) 50%, rgba(228, 229, 230, 1) 50%);
    background: -webkit-linear-gradient(180deg, rgba(0, 159, 255, 1) 50%, rgba(228, 229, 230, 1) 50%);
    background: linear-gradient(180deg, rgba(0, 159, 255, 1) 50%, rgba(228, 229, 230, 1) 50%);
}

.breadcrumbfloa ul li::before, .tippy-box .breadcrumbfloa ul li::before {
    content: \'\';
    position: absolute;
    width: 4px;
    height: 25px;
    z-index: 998;
    background-color: #fff;
    left: -23px;
    top: 25px;
}

.breadcrumbfloa ul li.active::after, .tippy-box .breadcrumbfloa ul li.active::after {
    background-color: #009FFF;
}
.breadcrumbfloa ul li::after, .tippy-box .breadcrumbfloa ul li::after {
    content: \'\';
    position: absolute;
    z-index: 999;
    left: -30px;
    top: 8px;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background-color: #fff;
}
        </style>';

    $txt .=" <script>
                 function sel3(){
                     $('#view-simu').html('3 x {$total3}');
                                                $('.multichoose div').removeClass('selected');
                                                $(\"#view-choose3x\").addClass('selected');
                                                $(\"#choose3x\").addClass('selected');
                                                $('.breadcrumbfloa').hide();
                                                $(\"#bread3x\").show();
                 }
                 
                 function sel4(){
                   $('#view-simu').html('4 x {$total4}');
                                                $('.multichoose div').removeClass('selected');
                                                $(\"#view-choose4x\").addClass('selected');
                                                $(\"#choose4x\").addClass('selected');
                                                $('.breadcrumbfloa').hide();
                                                $(\"#bread4x\").show();
                                                $(\"#view-choose4x\").addClass('selected');
                 }
 

</script> ";

  //  if(($total < 50) || ($total > 2500)){ $m3 = 0; return ''; }
  //  if(($total < 350) || ($total > 2500)){ $m4 = 0; $txt .= '<style> .multichoose div { width: 100% !important; } </style>';  }

    $modal .= '<div class="modal fade" id="modalfloa" tabindex="-1" >
                    <div class="modal-dialog " role="document" style="top: 50%">
                        <div class="modal-content">
                           
                            <div class="modal-body alert alert-primary alert-bg alert-button alert-block show-code-action" style="margin-bottom: 0px !important; background-color: #d7e8f8">
                               <div>
                                    <div class="multichoose">';
    if($m3 == 1 ) {
        $modal .= '    <div id="choose3x" onclick="sel3()" class="selected">3X</div>';
    }
    if($m4 == 1 ) {
        $modal .= '    <div id="choose4x" onclick="sel4()" class="">4X</div>';
    }
    $modal .= ' </div>
                                    <div class="breadcrumbfloa" id="bread3x" style="">
                                        <ul>
                                            <li class="active">
                                            <div class="fcontainer">
                                                <div class="bold fs-18">Hoje</div>
                                                <div class="bold fs-18">'.$total3.'</div>
                                             </div>
                                             </li>
                                            <li>
                                            <div class="fcontainer">
                                                <div>'.somadatasfloa(30).'</div>
                                                <div>'.$total3.'</div>
                                            </div>
                                            </li>
                                            <li class="last">
                                            <div class="fcontainer">
                                                <div>'.somadatasfloa(60).'</div>
                                                <div>'.$total3.'</div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="breadcrumbfloa" id="bread4x" style="display: none;">
                                        <ul>
                                            <li class="active"><div class="fcontainer"><div class="bold fs-18">Hoje</div><div class="bold fs-18">'.$total4.'</div></div></li>
                                            <li><div class="fcontainer"><div>'.somadatasfloa(30).'</div><div>'.$total4.'</div></div></li>
                                            <li><div class="fcontainer"><div>'.somadatasfloa(60).'</div><div>'.$total4.'</div></div></li>
                                            <li class="last"><div class="fcontainer"><div>'.somadatasfloa(90).'</div><div>'.$total4.'</div></div></li>
                                        </ul>
                                    </div>
                                    <div class="fraisadd2 mt-3" style="border-bottom-left-radius: 0;border-bottom-right-radius: 0;">
                                        <div style="color:#3A3A3A !important;font-size:16px;font-weight: bold;">Total</div>
                                        <div id="breadcrumbtotal" style="font-weight:bold;color:#3A3A3A !important;font-size:16px;">'.numberformat($total).'</div>
                                    </div>
                                    <div class="fraisadd2 mb-3 pb-2" style="border-top-left-radius: 0;border-top-right-radius: 0;"><div><div> </div></div><div></div></div>

                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>';

    $txt .= ' <div class="pp-floa-simu">
        <img style="max-height: 30px;" src="files/floa-logo-color.png">
        <div class="multichoose">';
    if($m3 == 1 ) {
    $txt .= '         <div id="view-choose3x" onclick="sel3()" class="selected">3X</div>';
    }
    if($m4 == 1 ) {
    $txt .= '       <div id="view-choose4x" onclick="sel4()" class="">4X</div>';
    }
    $txt .= ' </div>
        <div style="display:flex;line-height:28px; font-size: 22px;">
            <div class="bold" id="view-simu">3 x '.$total3.'</div> 
            <button type="button" class="btn btn-primary" 
                                    data-toggle="modal" 
                                    data-target="#modalfloa" style="display: inline;width: max-content;padding: 5px 5px 0px;background: transparent;border: 0;vertical-align: top;color: #ec870e;">
            <i class="fa fa-info-circle" aria-hidden="true"></i>
            </button>
            <!--<img id="floa-infobullepp" data-bs-toggle="modal" data-bs-target="#floamea" class="infobulle" src="assets-floa/img/infobulle.png">-->
        </div>
        </div> ';

    return $modal.$txt;
}
