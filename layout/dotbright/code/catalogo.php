<?php
if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) exit();


function show_blocos_destaque($prod)
{
    $id=$prod['id'];
    $title=$prod['title'];
    $caminho=$prod['caminho'];
    $fl=$prod['fl'];
    $image=$prod['image'];
    $subtitle=$prod['subtitle'];
    $ref=$prod['ref'];
    $price_riscado='';
    $price_riscado=($prod['price_riscado'] > $prod['price'] ? '<span>'.numberformat($prod['price_riscado'],1).'</span>' : '');

    $price=0; $price=$prod['price'];
    $stock=0; $stock=$prod['stock'];
    $promo=0; $promo=$prod['promo'];
    // $u_c=''; $u_c=$prod['u_c'];
    $stock_txt='';


    /*if($stock >= 3) $stock_txt='<span class="disponivel" title="Disponibilidade Total"> Stock: <img src="layout/'.PROJETO.'/images/status.png" alt="Disponibilidade Total" title="Disponibilidade Total" /></span>';
    elseif($stock == 2) $stock_txt='<span class="limitado" title="Disponibilidade Reduzida"> Stock: <img src="layout/'.PROJETO.'/images/status_away.png" alt="Disponibilidade Reduzida" title="Disponibilidade Reduzida" /></span>';
    elseif($stock == 0) $stock_txt='<span class="porencomenda" title="Disponivel por Encomenda"> Stock: <img src="layout/'.PROJETO.'/images/status_busy.png" alt="Disponivel por Encomenda" title="Disponivel por Encomenda" /></span>';
    else $stock_txt='<span class="esgotado" title="Disponivel Brevemente"> Stock: <img src="layout/'.PROJETO.'/images/status_blue.png" alt="Disponivel Brevemente" title="Disponivel Brevemente" /></span>';*/

    if($stock >0) $stock_txt='<span class="disponivel" title="Disponibilidade Total"> Stock: <img src="layout/'.PROJETO.'/images/status.png" alt="Disponibilidade Total" title="Disponibilidade Total" /></span>';
    else $stock_txt='<span class="esgotado" title="Disponivel Brevemente"> Stock: <img src="layout/'.PROJETO.'/images/status_blue.png" alt="Disponivel Brevemente" title="Disponivel Brevemente" /></span>';

    $prom='';
    $botao='';
    if(($stock > 0 || (isset($carrinho_conf['tem_enc_sem_stock']) && $carrinho_conf['tem_enc_sem_stock']==1)) && $price > 0) $botao .= '<div class="prodby">'.getLinguas('key_comprar').'<input type="hidden" value="'.$id .'" name="prodid"/><input type="hidden" value="'.($fl == 1 ? $caminho : '').'" name="prodfiltros" /></div>';
    else $botao .= '<div class="prodby-des fancy_stock" href="'.$GLOBALS['site_http_ajax_url'].'setNotificacaoProduto.php?idprod='.$id .'">&nbsp;ALERTA DE STOCK</div>';

    $wish=''; $res=0;
    if($dados_cliente)
    {
        $cli_id=$dados_cliente['cli_id'];
        if(isProductInWishList($id,$cli_id,$_SESSION['CLIENTE_ID_TEMP'])) $res=1;
        else $res=0;
    }
    elseif($_SESSION['CLIENTE_ID_TEMP'] != '')
    {
        if(isProductInWishList($id,0,$_SESSION['CLIENTE_ID_TEMP'])) $res=1;
        else $res=0;
    }

    if($res == 1) $wish='<a class="icon-coracao produtowl'.$id.'" href="#" onclick="removeProductWishList(\''.$id.'\',0);return false;"><span title="'.getLinguas('key_wishlist_rem').'">'.getLinguas('key_wishlist_rem').'<i class="fas fa-heart" aria-hidden="true"></i></span></a>';
    else $wish='<a class="icon-coracao produtowl'.$id.'" href="#" onclick="addProductWishList(\''.$id.'\');return false;"><span title="'.getLinguas('key_wishlist_add').'">'.getLinguas('key_wishlist_add').'<i class="far fa-heart" aria-hidden="true"></i></span></a>';

    $etiqueta='';

    if(trim($prod['u_c']) != '' && trim($prod['u_c']) != 'N/A')
    {
        if($prod['u_i'] != '')$etiqueta='<a style="background-color:'.$prod['u_co'].'; border-right-color:'.$prod['u_co'].'" class="eenergia2 fancybox" title="Etiqueta Energética" href="'.$prod['u_i'].'" target="_blank">'.$prod['u_c'].'</a>';
        else $etiqueta='<a style="background-color:'.$prod['u_co'].'; border-right-color:'.$prod['u_co'].'" class="eenergia2" title="Etiqueta Energética">'.$prod['u_c'].'</a>';
    }

    $prod_img=(($image=='' || substr_count($image,'no_photo.png')) ? '' : $image);
    if($prod_img == '')
    {
        if(dotbright_image_exists(dirname( __FILE__ ) .'/../../../files/products/'.$id.'_1_thum.JPG')) $image='files/products/'.$id.'_1_thum.JPG';
        elseif(dotbright_image_exists(dirname( __FILE__ ) .'/../../../files/products/'.$id.'_1_thum.jpg')) $image='files/products/'.$id.'_1_thum.jpg';
        elseif(dotbright_image_exists(dirname( __FILE__ ) .'/../../../files/products/'.$id.'_1_thum.PNG')) $image='files/products/'.$id.'_1_thum.PNG';
        elseif(dotbright_image_exists(dirname( __FILE__ ) .'/../../../files/products/'.$id.'_1_thum.png')) $image='files/products/'.$id.'_1_thum.png';
        elseif(dotbright_image_exists(dirname( __FILE__ ) .'/../../../files/products/'.$id.'_1.png')) $image='files/products/'.$id.'_1.png';
        elseif(dotbright_image_exists(dirname( __FILE__ ) .'/../../../files/products/'.$id.'_1.jpg')) $image='files/products/'.$id.'_1.jpg';
    }
    // <div class="prd_ref">Ref. '.$ref.'</div>

    $label_promo = '';
    /*var promo = 0;
	if(object.promo==1 && object.price > 0 && promoEndDate.getTime() >= todayDate.getTime()) //&& object.price_riscado > object.price
	{
		if( object.price_riscado > object.price)
		{
			var percdesconto = getDescontoAplicado(object.price_riscado, object.price);
			promo = number_format_(object.price_riscado,2,',','.');
		}
		cell += '<div class="ribbon-wrapper"><div class="ribbon-promo">PROMOÇÃO</div></div>';
		// cell += '<div class="prd_tagdiscount">-'+percdesconto+'%</div>';
	}*/

	//if($_GET['a']==1)var_dump($prod);

    if ($prod['novidade'] == 1)
	{
		$label_promo = '<div class="ribbon-wrapper3"><div class="ribbon-promo2">NOVIDADE</div></div>';
	}
    if ($prod['promo'] == 2)
	{
		$label_promo = '<div class="ribbon-wrapper3"><div class="ribbon-promo2">BEST PRICE</div></div>';
	}

    echo '<li>';
    echo '
				<div class="prd_d">
				'.$label_promo.'
				'.$prom.'
				'.$etiqueta.'
				<div class="prd_img"><a title="'.$title.'" href="'.$caminho.'" style="text-decoration:none"><span class="helper 1"></span><img alt="..." src="'.dotbright_image_url($image).'" '.(substr_count($image,'no_photo.png') ? 'style="height:146px"' : 'style="max-height:160px"').' border="0"></a></div>
				<div class="prd_layer">
				<div class="prd_title"><a title="'.$title.'" href="'.$caminho.'" target="_top" alt="'.$title.'">'.$title.'</a></div>
				<div class="c"></div>
				<div class="prd_stock">'.$stock_txt.'</div>
				<div class="c"></div>';
				//<div class="c"></div> 
				//<!--<div class="prd_wish">'.$wish.'</div>-->


    if($price_riscado!='')
    {
        echo '<div style="text-align:center;display:block;padding:5px;background:#f8f8f8"><h5 style="padding: 0px; margin: 0px">'.$prod['global_promos'][0]['designacao'].'</h5><span style="font-size: 10px;">'.substr($prod['global_promos'][0]['data_inicio'],0,10).' - '.substr($prod['global_promos'][0]['data_fim'],0,10).'<b data-toggle="tooltip" title="" data-original-title="'.$prod['global_promos']['descricao'].'">&nbsp<img src="layout/'.PROJETO.'/images/alert.png" width="13"></b></span></div>';
    }
    else
    {
        $prods=get_produtc_info($id);

        $descricao=substr(strip_html_tags($prods['prod_descricao']),0,90);
        //echo '<div style="height:52px;text-align:center;display:block;padding:0px;font-size:12px;overflow:hidden;text-overflow:ellipsis">'.$descricao.'</div>';
    }
    echo '
				<div class="prdprice_n" id="home_precos_'.$id.'" info="'.$id.'|0">
				<div class="price">'.($price_riscado!='' ? $price_riscado : '').'&nbsp;'.numberformat($price,1).'</div>
				<div class="clear" style="border-bottom:solid 1px #ccc;margin-bottom:10px"></div>
				'.$botao.'
				<div class="c"></div>
				</div><div class="c"></div></div></div>';
    echo '</li>';
}


function show_blocos_destaqueold($prod)
{
		$id=$prod['id'];
		$title=$prod['title'];
		$caminho=$prod['caminho'];
		$fl=$prod['fl'];
		$image=$prod['image'];
		$subtitle=$prod['subtitle'];
		$ref=$prod['ref'];
		$price_riscado='';
		$price_riscado=($prod['price_riscado'] > $prod['price'] ? '<span>'.numberformat($prod['price_riscado'],1).'</span>' : '');

		$price=0; $price=$prod['price'];
		$stock=0; $stock=$prod['stock'];
		$promo=0; $promo=$prod['promo'];
		// $u_c=''; $u_c=$prod['u_c'];
		$stock_txt='';


		if($stock >= 3) $stock_txt='<span class="disponivel" title="Disponibilidade Total"> Stock <img src="layout/'.PROJETO.'/images/status.png" alt="Disponibilidade Total" title="Disponibilidade Total" /></span>';
		elseif($stock == 2) $stock_txt='<span class="limitado" title="Disponibilidade Reduzida"> Stock <img src="layout/'.PROJETO.'/images/status_away.png" alt="Disponibilidade Reduzida" title="Disponibilidade Reduzida" /></span>';
		elseif($stock == 0) $stock_txt='<span class="porencomenda" title="Disponivel por Encomenda"> Stock <img src="layout/'.PROJETO.'/images/status_busy.png" alt="Disponivel por Encomenda" title="Disponivel por Encomenda" /></span>';
		else $stock_txt='<span class="esgotado" title="Disponivel Brevemente"> Stock <img src="layout/'.PROJETO.'/images/status_blue.png" alt="Disponivel Brevemente" title="Disponivel Brevemente" /></span>';


		$prom='';

		$botao='';
		if(($stock > 0 || (isset($carrinho_conf['tem_enc_sem_stock']) && $carrinho_conf['tem_enc_sem_stock']==1)) && $price > 0) $botao .= '<div class="prodby">'.getLinguas('key_comprar').'<input type="hidden" value="'.$id .'" name="prodid"/><input type="hidden" value="'.($fl == 1 ? $caminho : '').'" name="prodfiltros" /></div>';
		else $botao .= '<div class="prodby-des fancy_stock" href="'.$GLOBALS['site_http_ajax_url'].'setNotificacaoProduto.php?idprod='.$id .'">&nbsp;</div>';

		$wish=''; $res=0;
		if($dados_cliente)
		{
			$cli_id=$dados_cliente['cli_id'];
			if(isProductInWishList($id,$cli_id,$_SESSION['CLIENTE_ID_TEMP'])) $res=1;
			else $res=0;
		}
		elseif($_SESSION['CLIENTE_ID_TEMP'] != '')
		{
			if(isProductInWishList($id,0,$_SESSION['CLIENTE_ID_TEMP'])) $res=1;
			else $res=0;
		}

		/*if($res == 1) $wish='<a class="icon-coracao produtowl'.$id.'" href="#" onclick="removeProductWishList(\''.$id.'\',0);return false;"><span title="'.getLinguas('key_wishlist_rem').'">'.getLinguas('key_wishlist_rem').'<i class="fas fa-heart" aria-hidden="true"></i></span></a>';
		else $wish='<a class="icon-coracao produtowl'.$id.'" href="#" onclick="addProductWishList(\''.$id.'\');return false;"><span title="'.getLinguas('key_wishlist_add').'">'.getLinguas('key_wishlist_add').'<i class="far fa-heart" aria-hidden="true"></i></span></a>';*/

		$etiqueta='';

		if(trim($prod['u_c']) != '' && trim($prod['u_c']) != 'N/A')
		{
			if($prod['u_i'] != '')
			{
				$etiqueta='<a style="background-color:'.$prod['u_co'].'; border-right-color:'.$prod['u_co'].'" class="eenergia2 fancybox" title="Etiqueta Energética" href="'.$prod['u_i'].'" target="_blank">'.$prod['u_c'].'</a>';
			}
			else
			{
				$etiqueta='<a style="background-color:'.$prod['u_co'].'; border-right-color:'.$prod['u_co'].'" class="eenergia2" title="Etiqueta Energética">'.$prod['u_c'].'</a>';
			}
		}

		$prod_img=(($image=='' || substr_count($image,'no_photo.png')) ? '' : $image);
		if($prod_img == '')
		{
			if(dotbright_image_file_exists(dirname( __FILE__ ) .'/../../../files/products/'.$id.'_1_thum.JPG')) $image='files/products/'.$id.'_1_thum.JPG';
			elseif(dotbright_image_file_exists(dirname( __FILE__ ) .'/../../../files/products/'.$id.'_1_thum.jpg')) $image='files/products/'.$id.'_1_thum.jpg';
			elseif(dotbright_image_file_exists(dirname( __FILE__ ) .'/../../../files/products/'.$id.'_1_thum.PNG')) $image='files/products/'.$id.'_1_thum.PNG';
			elseif(dotbright_image_file_exists(dirname( __FILE__ ) .'/../../../files/products/'.$id.'_1_thum.png')) $image='files/products/'.$id.'_1_thum.png';
			elseif(dotbright_image_file_exists(dirname( __FILE__ ) .'/../../../files/products/'.$id.'_1.png')) $image='files/products/'.$id.'_1.png';
			elseif(dotbright_image_file_exists(dirname( __FILE__ ) .'/../../../files/products/'.$id.'_1.jpg')) $image='files/products/'.$id.'_1.jpg';
		}
		// <div class="prd_ref">Ref. '.$ref.'</div>
	
		// echo '<pre>'; print_r($prod); echo '</pre>';
		
		echo '<div class="prd_d">';
		echo $prom;
		echo $etiqueta;
		echo '<div class="prd_img"><a title="'.$title.'" href="'.$caminho.'" style="text-decoration:none"><span class="helper 2"></span><img alt="..." src="'.dotbright_image_url($image).'" '.(substr_count($image,'no_photo.png') ? 'style="height:146px"' : 'style="max-height:130px"').' border="0"></a></div>';
		echo '<div class="prd_layer">';
		echo '<div class="prd_title"><a title="'.$title.'" href="'.$caminho.'" target="_top" alt="'.$title.'">'.$title.'</a></div>';

		//icons 
		// console.log(object.a33);
		// if(typeof object.a33 != 'undefined' && object.a33 != '')
		// {
		// 	var grau_t = object.a33;
		// 	var grau = grau_t.split(',');
		// 	var top=5;
		// 	console.log(grau);
		// 	for(var a=0; a<grau.length; a++)
		// 	{
		// 		var gg = grau[a].split('-');
		// 		cell += '<div class="prod_cat_icons" style="top:'+top+'px;" title=""><img src="../../files/products_components/'+gg[0]+'_2.svg" height="48"></div>';
		// 		top+=45;
		// 	}
		// }

		// echo 'store_icons: '.$prod['store_icons'];

		if($prod['store_icons'] != 'undefined' && $prod['store_icons'] != '')
		{
			$grau_t = $prod['store_icons'];
			$grau = explode(';',$grau_t);
			$top=2;
			$grau_c = count($grau)-1;
			$grau_c=($grau_c>4)?4:$grau_c;
			for($a=0; $a<$grau_c; $a++)
			{
				$gg = explode('-',$grau[$a]);
				echo '<div class="prod_cat_icons" style="top:'.$top.'px;" title="">
				        <img title="' . nome_icons($gg[0]) . '" src="../../files/products_components/'.$gg[0].'_2.svg" height="28">
				        </div>';
				$top+=30;
			}
		}

		echo '<div class="c"></div>';	

		echo '<div class="c"></div>';
		echo '<div class="prd_stock">'.$stock_txt.'</div>';
		echo '<div class="c"></div>';

		/*
				'<div class="prd_d">
				'.$prom.'
				'.$etiqueta.'
				<div class="prd_img"><a title="'.$title.'" href="'.$caminho.'" style="text-decoration:none"><span class="helper 3"></span><img alt="..." src="'.dotbright_image_url($image).'" '.(substr_count($image,'no_photo.png') ? 'style="height:146px"' : 'style="max-height:130px"').' border="0"></a></div>
				<div class="prd_layer">
				<div class="prd_title"><a title="'.$title.'" href="'.$caminho.'" target="_top" alt="'.$title.'">'.$title.'</a></div>
				<div class="c"></div>
				<div class="prd_stock">'.$stock_txt.'</div>
				<div class="c"></div>
				<div class="prd_wish">'.$wish.'</div>
				<div class="c"></div>';

		*/


		if($price_riscado!='')
		{
			echo '<div style="text-align:center;display:block;padding:5px;background:#f8f8f8"><h5 style="padding: 0px; margin: 0px">'.$prod['global_promos'][0]['designacao'].'</h5><span style="font-size: 10px;">'.substr($prod['global_promos'][0]['data_inicio'],0,10).' - '.substr($prod['global_promos'][0]['data_fim'],0,10).'<b data-toggle="tooltip" title="" data-original-title="'.$prod['global_promos']['descricao'].'">&nbsp<img src="layout/'.PROJETO.'/images/alert.png" width="13"></b></span></div>';
		}
		else
		{
			$prods=get_produtc_info($id);

			$descricao=substr(strip_html_tags($prods['prod_descricao']),0,90);
			//echo '<div style="height:52px;text-align:center;display:block;padding:0px;font-size:12px;overflow:hidden;text-overflow:ellipsis">'.$descricao.'</div>';
		}
		echo '
		<div class="prdprice_n" id="home_precos_'.$id.'" info="'.$id.'|0">
		<div class="price">'.($price_riscado!='' ? $price_riscado : '').'&nbsp;'.numberformat($price,1).'</div>
		<div class="clear" style="border-bottom:solid 1px #ccc;margin-bottom:10px"></div>
		'.$botao.'
		<div class="c"></div>
		</div><div class="c"></div></div></div>';
}

function bloco_catalogo($tipos_destaques='')
{
    if(defined('is_home') && is_home==1 && !isset($_GET['popup'])) {
        require_once __DIR__.'/discovery.php'; db_disc_home(); return;
    }

    global $headers_adicionais;
    if (defined('CARRINHO_CONF')) $carrinho_conf=unserialize(CARRINHO_CONF); else $carrinho_conf=array();

    $mostra_destaque=0;
    $mostra_promocao=0;
    $mostra_maisvendido=0;
    $mostra_novos=0;
    $mostra_homepage=0;
    $mostra_relacionados=0;
    $mostra_publicos=0;
	$mostra_random=0;
    if(defined('is_home'))  $mostra_homepage=1;

    if(substr_count($tipos_destaques,'destaques')) $mostra_destaque=1;
    if(substr_count($tipos_destaques,'promocoes')) $mostra_promocao=1;
    if(substr_count($tipos_destaques,'maisvendidos')) $mostra_maisvendido=1;
    if(substr_count($tipos_destaques,'novos')) $mostra_novos=1;
    if(substr_count($tipos_destaques,'homepage')) $mostra_homepage=1;
    if(substr_count($tipos_destaques,'relacionados')) $mostra_relacionados=1;
    if(substr_count($tipos_destaques,'publicas')) $mostra_publicos=1;
	if(substr_count($tipos_destaques,'random')) $mostra_random=1;

    $reparacoes=0; $cat=0;
    if(isset($_GET['cat']) && intval($_GET['cat']) > 0) $cat=intval($_GET['cat']);
    $dados_cliente=cliente_logado();

    ?>
<div class="cat_wrap <?php if($mostra_homepage == 1){?>ishome<?php }?>" <?php if($mostra_homepage == 1){?> style="padding-left:0; width:100%"<?php }?>>
    <?php 

    if($_GET['page_id'] == 148)
    {

    }
    else
    {
    	if($mostra_homepage == 0 && $mostra_relacionados == 0 && $mostra_publicos == 0 && !isset($_GET['show_wishlist'])) echo BREADCRUMBS(1);
    }

    $res_fams='';
    if($mostra_homepage == 1)
    {
        $f_json_destaque=$GLOBALS['f_json_destaque'];
        if(is_array($f_json_destaque) && !empty($f_json_destaque) && $mostra_destaque == 1 && isset($f_json_destaque['Produtos']) && count($f_json_destaque['Produtos']) > 0)
        {
			//novo
			?>
			<div class="c"></div>
			<div class="c"></div>
            <div style="background:none; color:#000">
                <div class="destaque_img"> <!-- 352 -->
					<div class="titulo"><?=getLinguas('key_produtos_destaque',$GLOBALS['site_lang'])?></div>
                    <div class="clear"></div>
                    <div class="item"><!--maisvendido 358-->
                        <div class="slidercontent" id="product_show">
							<?php
                                foreach($f_json_destaque as $produtos)
                                {
                                    foreach($produtos as $prod)
                                    {
                                        show_blocos_destaque($prod);
                                    }
                                }
							?>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
			</div>
			<?

			//antigo
            /*
			<div class="catalogo_topo_filtros" style="background:none; color:#000">
            	<div class="destaque_img">
					<div class="titulo"><?=getLinguas('key_produtos_destaque',$GLOBALS['site_lang'])?></div>
					<div class="clear"></div>
					<div class="item">
						<div class="leftbutton" id="goToPrevSlide_destaque"></div>
						<div class="slidercontent">
							<ul id="home-destaque" class="home-destaque">
								<?php
									foreach($f_json_destaque as $produtos)
									{
										foreach($produtos as $prod)
										{
											show_blocos_destaque($prod);
										}
									}
								?>
							</ul>
						</div>
						<div class="rightbutton" id="goToNextSlide_destaque"></div>
						<div class="clear"></div>
					</div>
            	</div>
             </div>

			 */

			
			/*
				$dados_cliente = cliente_logado();
				global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang;
				$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
				$rs=new Dataobj;
				$rs->db_open($db_link,$DB_name,"SELECT products_components.ID,img_ext,Nome FROM products_components LEFT JOIN products_components_lang ON products_components.ID=products_components_lang.ID_PAI WHERE Visivel=1 and products_components.ID <> 12 ORDER BY Ordem ASC");
				$idx=0;
				if(!$rs->EOF()){
					?>
					<div class="b_icons row" style="margin-bottom: 20px">
						<?
							while(!$rs->EOF())
							{
								if(fmod($idx,4)==0) 
									echo '</tr><tr>';
								?>
								<div class="bloco_icons">
									<!-- <img src="<? echo "../../files/products_components/".$rs->db_data['ID']."_1.".$rs->db_data['img_ext'];?>" height="48"> -->
									<img src="<? echo "../../files/products_components/".$rs->db_data['ID']."_2.svg"?>" height="48">
									<span><?=$rs->db_data['Nome']?></span>
								</div>
								<?
								$idx++;
								$rs->db_next();
							}
						?>
					</div>

					<?
				}
				
				$rs->db_close();*/
			?>

			<div class="b_icons row" style="margin: 20px auto;  background: #cccc; padding: 5px 0">
				<div class="col-md-1"></div>
				<div class="col-md-2">
					<img src="layout/<?=PROJETO?>/images/envios.svg" alt="">
				</div>
				<div class="col-md-2">
				<img src="layout/<?=PROJETO?>/images/reparacoes.svg" alt="">
				</div>
				<div class="col-md-2">
				<img src="layout/<?=PROJETO?>/images/atendimento.svg" alt="">
				</div>
				<div class="col-md-2">
				<img src="layout/<?=PROJETO?>/images/segura.svg" alt="">
				</div>
				<div class="col-md-2">
				<img src="layout/<?=PROJETO?>/images/garantia.svg" alt="">
				</div>
				<div class="col-md-1"></div>
			</div>

			<?php
		
        }
        $f_json_promocao = $GLOBALS['f_json_promocao'];
        if(is_array($f_json_promocao) && !empty($f_json_promocao) && $mostra_promocao == 1 && isset($f_json_promocao['Produtos']) && count($f_json_promocao['Produtos']) > 0)
        {
            ?>
            <div class="c"></div><br><br><div class="c"></div>
            <div class="catalogo_topo_filtros" style="background:none; color:#000">
            <div class="destaque_img">
                <div class="titulo">PROMOÇÕES</div>
                <div class="clear"></div>
                <div class="item"><!-- promocao -->
                    <div class="leftbutton" id="goToPrevSlide_promocao"></div>
                    <div class="slidercontent" style="overflow: hidden;"> 
                        <ul id="home-promocao" class="home-promocao" style="width: 1632px;height: 386px;padding-bottom: 0%;transform: translate3d(0px,0px,0px);visibility: visible;">
                            <?php
                            foreach($f_json_promocao as $produtos)
                            {
                                foreach($produtos as $prod)
                                {
                                    show_blocos_destaque($prod);
                                }
                            }
                            ?>
                        </ul>
                    </div>
                    <div class="rightbutton" id="goToNextSlide_promocao"></div>
                    <div class="clear"></div>
                </div>
            </div>
            </div>

            <?php
        }
        $f_json_maisvendido= $GLOBALS['f_json_maisvendido'];
        if(is_array($f_json_maisvendido) && !empty($f_json_maisvendido)&& $mostra_maisvendido == 1 && isset($f_json_maisvendido['Produtos']) && count($f_json_maisvendido['Produtos']) > 0)
        {
            ?>
            <div class="c"></div><br><br><div class="c"></div>


            <div class="catalogo_topo_filtros" style="background:none; color:#000">
                <div class="destaque_img">
                    <div class="titulo">PRODUTOS <span>EM PROMOÇÃO</span></div>
                    <div class="clear"></div>
                    <div class="item"><!-- promocao -->
                        <div class="leftbutton" id="goToPrevSlide_destaque"></div>
                        <div class="slidercontent">
                            <ul id="home-destaque" class="home-destaque">
                                <?php
                                foreach($f_json_maisvendido as $produtos)
                                {
                                    foreach($produtos as $prod)
                                    {
                                        show_blocos_destaque($prod);
                                    }
                                }
                                ?>
                            </ul>
                        </div>
                        <div class="rightbutton" id="goToNextSlide_destaque"></div>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>

            <?php

        }

        $f_json_novidades= $GLOBALS['f_json_novidades'];
        if(is_array($f_json_novidades) && !empty($f_json_novidades)&& $mostra_novos == 1 && isset($f_json_novidades['Produtos']) && count($f_json_novidades['Produtos']) > 0)
        {
            ?>
            <div class="c"></div><br><br><div class="c"></div>

            <div class="catalogo_topo_filtros" style="background:none; color:#000">
                <div class="destaque_img">
                    <div class="titulo">
                        <?php echo'PRODUTOS'. getLinguas('key_just',$GLOBALS['site_lang'])?>
                        <span><?php echo 'NOVIDADE'. getLinguas('key_arrived',$GLOBALS['site_lang'])?></span>

                    </div>
                    <div class="clear"></div>
                    <div class="item"><!-- novidae -->
                        <div class="leftbutton" id="goToPrevSlide_maisvendido"></div>
                        <div class="slidercontent">
                            <ul id="home-maisvendido" class="home-destaque">
                                <?php
                                foreach($f_json_novidades as $produtos)
                                {
                                    foreach($produtos as $prod)
                                    {
                                        show_blocos_destaque($prod);
                                    }
                                }
                                ?>
                            </ul>
                        </div>
                        <div class="rightbutton" id="goToNextSlide_maisvendido"></div>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>



            <?php
        }

		$f_json_random= $GLOBALS['f_json_random'];
		// echo 'random: '.count($f_json_random['Produtos']);
        if(is_array($f_json_random) && !empty($f_json_random)&& $mostra_random == 1 && isset($f_json_random['Produtos']) && count($f_json_random['Produtos']) > 0)
        {
            ?>
            <div class="c"></div><br><br><div class="c"></div>

            <div class="catalogo_topo_filtros" style="background:none; color:#000">
                <div class="destaque_img">
                    <div class="titulo"></div>
                    <div class="clear"></div>
                    <div class="item"><!-- novidae -->
                        <div class="leftbutton" id="goToPrevSlide_maisvendido"></div>
                        <div class="slidercontent">
                            <ul id="home-maisvendido" class="home-destaque">
                                <?php
                                foreach($f_json_random as $produtos)
                                {
                                    foreach($produtos as $prod)
                                    {
                                        show_blocos_destaque($prod);
                                    }
                                }
                                ?>
                            </ul>
                        </div>
                        <div class="rightbutton" id="goToNextSlide_maisvendido"></div>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
            <?php
        }
    }
    elseif($mostra_publicos == 1)
    {
        $f_json_maisvendido= $GLOBALS['f_json_maisvendido'];
        if(is_array($f_json_maisvendido) && !empty($f_json_maisvendido)&& $mostra_publicos == 1 && isset($f_json_maisvendido['Produtos']) && count($f_json_maisvendido['Produtos']) > 0)
        {
            ?>
            <div class="c"></div><br><br><div class="c"></div>
            <div class="catalogo_topo_filtros" style="background:none; color:#000">
                <div class="destaque_img">
                    <div class="titulo" style="font-weight: bold;">
                        <?php echo getLinguas('key_featured',$GLOBALS['site_lang'])?>
                        <b style="color:#3db6e7"><?php echo getLinguas('key_products',$GLOBALS['site_lang'])?></b>
                    </div>
                    <div class="clear"></div>
                    <div class="item"><!--maisvendido-->
                        <div class="slidercontent" id="product_show">
                            <?php
                            foreach($f_json_maisvendido as $produtos)
                            {
                                foreach($produtos as $prod)
                                {
                                    show_blocos_destaque($prod);
                                }
                            }
                            ?>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
            <?php

        }
    }
    elseif($mostra_relacionados == 1)
    {
        $f_json_relacionados= $GLOBALS['f_json_relacionados'];
        if(is_array($f_json_relacionados) && !empty($f_json_relacionados) && $mostra_relacionados == 1 && isset($f_json_relacionados['Produtos']) && count($f_json_relacionados['Produtos']) > 0)
        {
            ?>
            <div class="catalogo_topo_filtros" style="background:none; color:#000">
                <?php $f_json_relacionados= $GLOBALS['f_json_relacionados']; ?>
                <div class="destaque_img">
                    <div class="titulo"><?=getLinguas('key_produtos_destaque',$GLOBALS['site_lang'])?></div>
                    <div class="clear"></div>
                    <div class="item"><!--relacionados-->
                        <div class="slidercontent" id="product_show">
                            <?php
                            foreach($f_json_relacionados as $produtos)
                            {
                                foreach($produtos as $prod)
                                {
                                    show_blocos_destaque($prod);
                                }
                            }
                            ?>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>

            <?php
        }
    }
    else
    {
    	if($_GET['page_id'] == 148)
    	{?>
    		<div class="catalogo_topo_filtros">
    			<img src="files/banner.jpg" style="width:100%"><br>
    			<div style="width:100%;padding-top:5px;text-align:left;font-size:16px">Escolha uma das opções</div>
            <?php if(!isset($_GET['view'])) $_GET['view']=0; ?>

            <div class="filtros">                
                <input type="hidden" id="product_order" value="-1">
                <select name="f_visualizacao" id="f_visualizacao" style="display:none">                    
                   <option value="99999" selected="selected">Ver Todos</option>
                </select>
            </div>
            <div class="c"></div>
        </div>
        <div class="c"></div>
        <div id="product_show"></div><?
    	}
    	else
    	{
        ?>
        <div class="catalogo_topo_filtros">

            <?php if(!isset($_GET['view'])) $_GET['view']=0; ?>

            <div class="filtros">
                <div class="float-left">
                    <div class="info-extra-paginacao"></div>
                </div>
                <?php if($headers_adicionais) {?>
                    <div class="float-right">
                        <div class="vista-catalogo">
                            <div class="ver"><?=getLinguas('key_ver',$GLOBALS['site_lang'])?></div>
                            <a id="icon_view" class="icon_view<?php  if(!isset($_COOKIE['vista']) ||(isset($_COOKIE['vista']) && $_COOKIE['vista']==0)) echo ' view_activo';?>" onclick="mudaVista(0)">
                                <i class="fas fa-th-large"></i>
                                <span><?=getLinguas('key_grelha',$GLOBALS['site_lang'])?></span>
                            </a>
                            <a id="list_view" class="list_view<?php  if(isset($_COOKIE['vista']) && $_COOKIE['vista']==1) echo ' view_activo';?>" onclick="mudaVista(1)">
                                <i class="fas fa-th-list"></i>
                                <span><?=getLinguas('key_lista',$GLOBALS['site_lang'])?></span>
                            </a>
                        </div>
                    </div>
                <?php } ?>
                <div class="float-right">
                    <div class="paginacao"></div>
                </div>
                <div class="float-right">
                    <div class="filtro_order" style="<?php if($mostra_homepage == 0){?>display:block<?php }else{?>display:none !important<?php }?>">
                        <? /*

					<?php echo getLinguas('txt_ordby',$GLOBALS['site_lang']);?>:

					*/ ?>

                        <select id="product_order" class="filter chzn-select2">

                            <?php 

                            if(isset($_GET["pesquisa"]) && $_GET["pesquisa"]!='')$selected_order = '-1';
							else 
							{
								if($REPARACOES)$selected_order = '4';
								else $selected_order = '3';
							}
                            

                            //if(defined('ORDENAR_POR') && ORDENAR_POR !=''){$selected_order = ORDENAR_POR;}

                            ?>
                            <option value="-1" <?php echo ($selected_order == '-1' ? 'selected="selected"' : '');?>>Relevancia</option>
                            <option value="0" <?php echo ($selected_order == '0' ? 'selected="selected"' : '');?>>A..Z</option>
                            <option value="1" <?php echo ($selected_order == '1' ? 'selected="selected"' : '');?>>Z..A</option>
                            <option value="2" <?php echo ($selected_order == '2' ? 'selected="selected"' : '');?>><?php echo getLinguas('txt_maisbarato');?></option>
                            <option value="3" <?php echo ($selected_order == '3' ? 'selected="selected"' : '');?>><?php echo getLinguas('txt_maiscaro');?></option>

                        </select>
                    </div>
                    <?php
                    if(isset($_GET['cat']) && intval($_GET['cat']) > 0)
                    {

                        //echo '<div class="mobile">'.filtros_left(1).'</div>';
                        echo '<div class="mobile pesq_txt"><div class="pesq_txt_padding"><input placeholder="pesquisa rápida" type="text" id="search_box2" name="pesquisa" class="pesq_input" value="" /></div></div>';
                    }
                    else
                    {

                        //echo '<div class="mobile">'.filtros_left(1).'</div>';
                        echo '<div class="mobile pesq_txt"><div class="pesq_txt_padding"><input placeholder="pesquisa rápida" type="text" id="search_box2" name="pesquisa" class="pesq_input" value="" /></div></div>';

                        echo '<input type="hidden" id="search_box" name="pesquisa" class="pesq_input" value="" />';

                    }
                    ?>
                </div>
                <div class="float-right">
                    <?php if($mostra_homepage == 1) echo '<span style="color:#333;font-weight:700; padding-top:7px; font-size:16px;padding-left: 6px;">DESTAQUES</span>'; ?>
                    <div class="filtro_view" style="<?php if($mostra_homepage == 0){?>display:block<?php }else{?>display:none !important<?php }?>">

                        <? /*
					<?php echo getLinguas('txt_produtosper',$GLOBALS['site_lang']);?>:&nbsp;
					*/ ?>


                        <select class="view chzn-select2" name="f_visualizacao" id="f_visualizacao">
                            <?php if($mostra_homepage == 1){?><option value="8" selected="selected">8 <?php echo getLinguas('txt_produtos');?></option><?php }else{?><option value="12" selected="selected">12 <?php echo getLinguas('txt_produtos');?></option>
                                <option value="24">24 <?php echo getLinguas('txt_produtos');?></option>
                                <option value="48">48 <?php echo getLinguas('txt_produtos');?></option>
                                <option value="99999">Ver Todos</option><?php }?>
                        </select>
                    </div>
                </div>
                <div class="c"></div>
            </div>
            <div class="c"></div>
        </div>

        <div class="c"></div>


        <div class="c"></div>

        <div id="product_show"></div>
		<?
		}
		/*
		if($_GET['cat'] == 85)
		{
			$dados_cliente = cliente_logado();
			global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang;
			$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
			$rs=new Dataobj;
			$rs->db_open($db_link,$DB_name,"SELECT products_components.ID,img_ext,Nome FROM products_components LEFT JOIN products_components_lang ON products_components.ID=products_components_lang.ID_PAI WHERE Visivel=1 and products_components.ID <> 12 ORDER BY Ordem ASC");
			$idx=0;
			if(!$rs->EOF())
			{
				?>
				<div class="b_icons row" style="margin-bottom: 20px">
					<?
						while(!$rs->EOF())
						{
							if(fmod($idx,4)==0) 
								echo '</tr><tr>';
							?>
							<div class="bloco_icons">
								<!-- <img src="<? echo "../../files/products_components/".$rs->db_data['ID']."_1.".$rs->db_data['img_ext'];?>" height="48"> -->
								<img src="<? echo "../../files/products_components/".$rs->db_data['ID']."_2.svg"?>" height="48">
								<span><?=$rs->db_data['Nome']?></span>
							</div>
							<?
							$idx++;
							$rs->db_next();
						}
					?>
				</div>

				<?
			}
			
			$rs->db_close();
		}*/
		?>
        <?php if( $cat > 0){ ?>
        <div class="catalogo_bottom_filtros">

            <div class="filtros">
                <div class="float-left">
                    <div class="info-extra-paginacao"></div>
                </div>
                <div class="float-right">
                    <div class="paginacao"></div>
                </div>



                <div class="c"></div>
            </div>
            <div class="c"></div>
        </div><?php
    }?>
        </div>
        </div>
        <div class="c"></div>
    <?php } ?>
    </div>
    <div class="c"></div>
    <?php
}


function getSubMenuItensbrands_cat($ID_ORG)
{
	$menus='';

	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$familia=new Dataobj;
	$sql='
			SELECT
				pf.ID,pf.img_ext,pfl.Nome
			FROM
				brands pf
				INNER JOIN brands_lang pfl ON pfl.ID_PAI=pf.ID
				INNER JOIN filters_products fp ON fp.marca_id=pf.ID
				INNER JOIN products p ON p.ID=fp.prod_id
			WHERE
				pf.Visivel=1
				AND p.Aproved=1
				AND pfl.lang="'.$site_lang.'"
				AND pfl.Nome <> ""
				AND
				(
					fp.fam_id1='.$ID_ORG.'
					OR fp.fam_id2='.$ID_ORG.'
					OR fp.fam_id3='.$ID_ORG.'
				)
			GROUP BY
				pf.ID asc
			ORDER BY
				pf.Ordem=0,pf.Ordem asc,pfl.Nome asc';
		// echo $sql;
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$menus .= '<div class="row">';

		while(!$rs->EOF())
		{
			$marca_id=$rs->db_data['ID'];
			$marca_name=$rs->db_data['Nome'];
			$I1=$rs->db_data['img_ext'];

			// $URL_Link='mr-'.$marca_id.'-'. str_replace(' ','',limpa_special($marca_name)).'-'.$site_lang.'?c='.$ID_ORG;
			$URL_Link='m-'.$marca_id.'-'. str_replace(' ','',limpa_special($marca_name)).'-'.$site_lang.'?c='.$ID_ORG;

			$img_file='files/brands/'.$marca_id.'_1.'.$I1;
			$img_file_new='files/brands/'.$marca_id.'_1_cat.'.$I1;
			// $img_file_new2='files/brands/'.$marca_id.'_1_menu_grey.'.$I1;

			geraimagens($img_file,$img_file_new,150,0,array('ativo' => 1,'w' => 200,'h' => 240),0,0,0);
			// geraimagens($img_file,$img_file_new2,100,50,1,0,0,1);

			if(dotbright_image_file_exists($img_file_new)) $marca_name='<img src="'.$img_file_new.'" class="ch_img" style="vertical-align:middle" title="" alt="" />';

			$menus .= '<div class="col-md-3 text-center"><a href="'.$URL_Link.'">'.$marca_name.'</a></div>';

			$rs->db_next();
		}
		$menus .= '</div>';
	}
	$rs->db_close();
	return $menus;
}
function GERA_CATALOGO_FAMILIAS($fams=array())
{

	global $site_http_url;

	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj; $rs2=new DataObj;

	if(isset($_GET['cat']) && $_GET['cat'] > 0) $cat=intval($_GET['cat']); else $cat=0;

	$nivel=count($fams);

	// if($nivel == 2)
	// {
		// $sum_fams=show_menu_subfamilias($cat);
		// print_r($sum_fams);
	// }
	// echo $nivel;
	$key=(array_search ($cat,$fams)+1);

	// $p_way=geraCatalogoRecursivo($cat);

	// SE FOR A FAMILIA PRINCIPAL
	/* if(!empty($p_way) && $p_way[0] == $cat)
	{
		echo getSubMenuItensbrands_cat($cat);
	}
	else
	{ */
		$sql='
				SELECT
					pf.ID,pf.ID_ORG,pfl.Cabecalho, pfl.Cabecalho2,pfl.Texto,pf.img_ext
				FROM
					products_families pf
					INNER JOIN products_families_lang pfl ON (pf.ID=pfl.ID_PAI)
				WHERE
					pf.visivel=1
					AND pfl.Lang="'.$GLOBALS['site_lang'].'"
					AND pf.ID_ORG="'.$cat.'"
				GROUP BY
					pf.ID
				ORDER BY Ordem Asc
			';
			// echo $sql;
		$s='';
		$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);

		// echo $sql;
		$i=0; $a=1; $a2=0;

		if(!$rs->EOF())
		{
			$s='<div class="row">';

			while(!$rs->EOF())
			{
				$i++;
				$IDb=$rs->db_data['ID'];
				$img_ext=$rs->db_data['img_ext'];
				$Cabecalho=$rs->db_data['Cabecalho2'];
				if(!$Cabecalho) $Cabecalho=$rs->db_data['Cabecalho'];
				$Texto=$rs->db_data['Texto'];

				$img_file='files/products_families/'.$IDb.'_1.'.$img_ext;
				$img_file_new='files/products_families/'.$IDb.'_1_cat.'.$img_ext;

				if($nivel == 2) geraimagens($img_file,$img_file_new,100,100,array('ativo' => 1,'w' => 160,'h' => 200),0,0,0);
				else geraimagens($img_file,$img_file_new,160,160,array('ativo' => 1,'w' => 200,'h' => 240),0,0,0);


				$url=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$IDb,$IDb,1);

				// if($a == $a2){ $a2=0; $s .= '</ul><ul>';}

				$s .= '<div class="col-md-4 text-center" style="margin-bottom:10px"><div style="border:#c0c0c0 1px solid">';

				$s .= '<div class="row"><div class="col-md-12 text-center" >';

				if($img_ext && dotbright_image_file_exists('files/products_families/'.$IDb.'_1.'.$img_ext))$s .= '<a href="'.$url.'"><img src="'.$img_file_new.'?dp='.date('YmdHis').'" alt="" /></a>';
				else
				{
					if($nivel == 2) $s .= '<a href="'.$url.'"><img src="layout/'.PROJETO.'/images/blank.gif" width="160" height="200" alt="" /></a>';
					else $s .= '<a href="'.$url.'"><img src="layout/'.PROJETO.'/images/blank.gif" width="200" height="240" alt="" /></a>';
				}
				$s .= '</div>';

				$s .= '<div class="col-md-12 text-center" style="height:25px;margin-bottom:10px">';
				$s .= '<a href="'.$url.'" title="'.$Cabecalho.'" '.$open.' '.$Index.' class="bg alignmiddle" style="position:absolute;bottom:0;width:100%;display:block;">';
				if($nivel == 3) $s .= '<span class="title" style="font-size:16px">'.$Cabecalho.'</span>';
				elseif($nivel == 2)$s .= '<span class="title" style="font-size:30px">'.$Cabecalho.'</span>';
				elseif($nivel == 1) $s .= '<span class="title" style="font-size:20px">Reparações</span>';
				else $s .= '<span class="title" style="font-size:20px">Reparações</span>';
				$s .= '</a>';
				$s .= '</div>';
				$s .= '</div>';

				$s .= '</div>';
				$s .= '</div>';

				$a2++;

				$rs->db_next();
			}
			$s.= '</div>';
		}
		$rs->db_close();

		return $s;
	// }
}

function show_menus_families_multiple($id_pai = 0)
{
	$dados_cliente = cliente_logado();

	$ID_ORG = 0;
	if(isset($_GET['cat']) && intval($_GET['cat']) > 0)
	{
		$ID_ORG =  intval($_GET['cat']);
	}

	$cache_filename = SITE_SHELL_URL.'files/tmp/show_menus_families_'.$id_pai.'_'.$ID_ORG.'_'.$GLOBALS['site_lang'].'.txt';

	$time_expire = time() - 60*60*24*1; // Expire Time (2 minute)
	$gerar = '';
//$gerar=1;
	if(isset($_GET['gerar'])) $gerar = intval($_GET['gerar']);
	// echo $cache_filename;
	if($gerar!=1 && file_exists($cache_filename) && filectime($cache_filename) > $time_expire) $return = file_get_contents($cache_filename);
	else
	{

		$rec_fams = getCategoriesRec($ID_ORG);

		global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs=new Dataobj;
		$rs2=new Dataobj;
		$rs3=new Dataobj;
		$return='';

		if(isset($_GET['cat']) && intval($_GET['cat']) > 0)
		{
			$return .= '<div class="horizontalTab2"><ul class="resp-tabs-list"><li>'.getLinguas('txt_f_filterby',$GLOBALS['site_lang']).':</li><li>'.getLinguas('txt_categorias',$GLOBALS['site_lang']).'</li></ul><div class="resp-tabs-container"><div>';
			$return .= filtros_left(1);
			$return .=  '</div><div>';
		}

		$return_resp='';
		$sql = 'SELECT
					m.ID,m.img_ext,m.catalogo_id,m.tipo_catalogo,m.TipoMenu,m.URL_Link,
					ml.URL_Nome2
				FROM
					menus_links m
					INNER JOIN	menus_links_lang ml ON ml.ID_PAI=m.ID
				WHERE
					m.ID_ORG="'.$id_pai.'"
					AND m.Visivel=1
					AND ml.Lang="'.$GLOBALS['site_lang'].'"
					'.(isset($dados_cliente) && is_array($dados_cliente) ? ' AND (Logado = 2 OR Logado = 0)' : ' AND (Logado = 1 OR Logado = 0)').'
				ORDER BY
					m.Ordem ASC
				';
		// echo $sql;

		$rs->db_open($db_link,$DB_name,$sql);


		if(!$rs->EOF())
		{
			$return .= '<div class="drop">';
			$return .= '<ul>';
			$return .= '<li class="active">';
			$return .= '<ul>';

			while(!$rs->EOF())
			{
				$submenus = ''; $submenus_mobile = '';
				$menu_id = $rs->db_data['ID'];
				$menu_ext = $rs->db_data['img_ext'];
				$catalogo_id = $rs->db_data['catalogo_id'];
				$catalogo_tipo = $rs->db_data['tipo_catalogo']; // CAMPO 'Catálogo de Produtos' MARCAS ou CATEGORIAS
				$menu_tipo = $rs->db_data['TipoMenu']; // SELECT BOX BACKOFFICE 'Tipo Menu'
				$menu_nome = $rs->db_data['URL_Nome2'];
				$menu_url = seo($rs->db_data['URL_Link']);
				$img = '';
				if(dotbright_image_file_exists('files/menus/'.$menu_id.'_1.'.$menu_ext) && $menu_ext!='') $img = '<img src="files/menus/'.$menu_id.'_1.'.$menu_ext.'" alt="">';

				$p_seo2 = $menu_url;

				$class = '';
				if($catalogo_id > 0 && $menu_tipo == 'familias')
				{
					$sum_fams = show_menu_subfamilias($catalogo_id,0,$rec_fams,$reparacoes);


					if(in_array($catalogo_id,$rec_fams)) $class = 'class="t active"'; else $class = 'class="t"';

					$p_seo = seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$catalogo_id,$catalogo_id,1);

					if(empty($sum_fams['return']) || $reparacoes == 1) $p_seo2 = $p_seo; else $p_seo2 = '#';
				}

				$return .= '<li '.$class.'>';
				// <i class="fa fa-angle-right" aria-hidden="true"></i>
				$return .= '<a style="width:100%" href="'.$p_seo2.'" class="t">'.$img.' '.$menu_nome.'</a>';

				if(!empty($sum_fams['return']) && ($catalogo_id > 0 && $menu_tipo == 'familias')) $return .= '<ul>'.$sum_fams['return'].'</ul>';

				$return .= '</li>';

				$rs->db_next();
			}
			$return.='</ul></li></ul><div class="c"></div></div>';
		}else
		{
			$return .= '<div class="drop">';
			$return .= '<ul>';
			$return .= '<li class="active">';

			$sum_fams = show_menu_subfamilias(0,0,$rec_fams,$reparacoes);
			if(!empty($sum_fams['return'])) $return .= '<ul>'.$sum_fams['return'].'</ul>';

			$return.='</li></ul><div class="c"></div></div>';
		}
		$rs->db_close();

		if(isset($_GET['cat']) && intval($_GET['cat']) > 0)
		{
			$return .= '</div></div></div>';
		}

		$fh = fopen($cache_filename,'w');
		fwrite($fh,$return);
		fclose($fh);
		chmod($cache_filename,0755);

	}

	return $return;
}

function show_menus_families_bk_nao_eliminar($id_pai = 0)
{
	$dados_cliente = cliente_logado();

	$ID_ORG = 0;
	if(isset($_GET['cat']) && intval($_GET['cat']) > 0)
	{
		$ID_ORG =  intval($_GET['cat']);
	}

	$cache_filename = SITE_SHELL_URL.'files/tmp/show_menus_families_'.$id_pai.'_'.$ID_ORG.'_'.$GLOBALS['site_lang'].'.txt';

	$time_expire = time() - 1*120*1; // Expire Time (2 minute)
	$gerar = '';

	if(isset($_GET['gerar'])) $gerar = intval($_GET['gerar']);
	// echo $cache_filename;
	if($gerar!=1 && file_exists($cache_filename) && filectime($cache_filename) > $time_expire) $return = file_get_contents($cache_filename);
	else
	{

		$rec_fams = getCategoriesRec($ID_ORG);

		global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs=new Dataobj;
		$rs2=new Dataobj;
		$rs3=new Dataobj;
		$return='';

		// if(isset($_GET['cat']) && intval($_GET['cat']) > 0)
		// {
			// $return .= '<div class="horizontalTab2"><ul class="resp-tabs-list"><li>'.getLinguas('txt_f_filterby',$GLOBALS['site_lang']).':</li><li>'.getLinguas('txt_categorias',$GLOBALS['site_lang']).'</li></ul><div class="resp-tabs-container"><div>';
			// $return .= filtros_left(1);
			// $return .=  '</div><div>';
		// }

		$return_resp='';
		$sql = 'SELECT
					m.ID,m.img_ext,m.catalogo_id,m.tipo_catalogo,m.TipoMenu,m.URL_Link,
					ml.URL_Nome2
				FROM
					menus_links m
					INNER JOIN	menus_links_lang ml ON ml.ID_PAI=m.ID
				WHERE
					m.ID_ORG="'.$id_pai.'"
					AND m.Visivel=1
					AND ml.Lang="'.$GLOBALS['site_lang'].'"
					'.(isset($dados_cliente) && is_array($dados_cliente) ? ' AND (Logado = 2 OR Logado = 0)' : ' AND (Logado = 1 OR Logado = 0)').'
				ORDER BY
					m.Ordem ASC
				';
		// echo $sql;

		$rs->db_open($db_link,$DB_name,$sql);


		if(!$rs->EOF())
		{
			$return .= '<div class="drop">';
			$return .= '<ul>';
			$return .= '<li class="active">';
			$return .= '<ul>';

			while(!$rs->EOF())
			{
				$submenus = ''; $submenus_mobile = '';
				$menu_id = $rs->db_data['ID'];
				$menu_ext = $rs->db_data['img_ext'];
				$catalogo_id = $rs->db_data['catalogo_id'];
				$catalogo_tipo = $rs->db_data['tipo_catalogo']; // CAMPO 'Catálogo de Produtos' MARCAS ou CATEGORIAS
				$menu_tipo = $rs->db_data['TipoMenu']; // SELECT BOX BACKOFFICE 'Tipo Menu'
				$menu_nome = $rs->db_data['URL_Nome2'];
				$menu_url = seo($rs->db_data['URL_Link']);
				$img = '';
				if(dotbright_image_file_exists('files/menus/'.$menu_id.'_1.'.$menu_ext) && $menu_ext!='') $img = '<img src="files/menus/'.$menu_id.'_1.'.$menu_ext.'" alt="">';

				$p_seo2 = $menu_url;

				$class = '';
				if($catalogo_id > 0 && $menu_tipo == 'familias')
				{
					$sum_fams = show_menu_subfamilias($catalogo_id,0,$rec_fams,$reparacoes);


					if(in_array($catalogo_id,$rec_fams)) $class = 'class="t active"'; else $class = 'class="t" ';

					$p_seo = seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$catalogo_id,$catalogo_id,1);

					if(empty($sum_fams['return']) || $reparacoes == 1) $p_seo2 = $p_seo; else $p_seo2 = '#';
				}



				$return .= '<li '.$class.'>';
				// <i class="fa fa-angle-right" aria-hidden="true"></i>
				if($catalogo_id > 0 && $menu_tipo == '')
				{
					$p_seo = seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$catalogo_id,$catalogo_id,1);
					$return .= '<a style="width:100%" href="'.$p_seo.'" class="t">'.$img.' '.$menu_nome.'</a>';
				}

				if(!empty($sum_fams['return']) && ($catalogo_id > 0 && $menu_tipo == 'familias')) $return .= '<ul style="display:block">'.$sum_fams['return'].'</ul>';


				$return .= '</li>';

				$rs->db_next();
			}
			$return.='</ul></li></ul><div class="c"></div></div>';
		}
		else
		{
			$return .= '<div class="drop">';
			$return .= '<ul>';
			$return .= '<li class="active">';

			$sum_fams = show_menu_subfamilias(0,0,$rec_fams,$reparacoes);
			if(!empty($sum_fams['return'])) $return .= '<ul>'.$sum_fams['return'].'</ul>';

			$return.='</li></ul><div class="c"></div></div>';
		}
		$rs->db_close();

		// if(isset($_GET['cat']) && intval($_GET['cat']) > 0)
		// {
			// $return .= '</div></div></div>';
		// }

		$fh = fopen($cache_filename,'w');
		fwrite($fh,$return);
		fclose($fh);
		chmod($cache_filename,0755);

	}

	return $return;
}


function show_menus_reparacoes($id_pai = 0)
{
	$dados_cliente = cliente_logado();
	$ID_ORG = 0;
	if(isset($_GET['cat']) && intval($_GET['cat']) > 0)
	{
		$ID_ORG =  intval($_GET['cat']);
	}
	$cache_filename = SITE_SHELL_URL.'files/tmp/show_menus_repara_'.$id_pai.'_'.$ID_ORG.'_'.$GLOBALS['site_lang'].'.txt';

	$time_expire = time() - 60*60*24*1;
	$gerar = '';

	$reparacoes=1;

	if(isset($_GET['gerar'])) $gerar = intval($_GET['gerar']);
	//$gerar=1;	
	if($gerar!=1 && file_exists($cache_filename) && filectime($cache_filename) > $time_expire) $return = file_get_contents($cache_filename);
	else
	{
		$rec_fams = getCategoriesRec($ID_ORG);
		global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs=new Dataobj;
		$rs2=new Dataobj;
		$rs3=new Dataobj;

		$return='';
		$return_resp='';
		$sql = 'SELECT m.ID,m.img_ext,m.catalogo_id,m.tipo_catalogo,m.TipoMenu,m.URL_Link,ml.URL_Nome2
				FROM menus_links m INNER JOIN menus_links_lang ml ON ml.ID_PAI=m.ID
				WHERE
					m.ID_ORG="'.$id_pai.'"
					AND m.Visivel=1
					AND ml.Lang="'.$GLOBALS['site_lang'].'"
					'.(isset($dados_cliente) && is_array($dados_cliente) ? ' AND (Logado = 2 OR Logado = 0)' : ' AND (Logado = 1 OR Logado = 0)').'
				ORDER BY m.Ordem ASC';
		$rs->db_open($db_link,$DB_name,$sql);

		if(!$rs->EOF())
		{
			$return .= '<div style="color:#ec870e;font-size:16px;border-bottom:solid 1px #232323">Reparações</div>';
			$return .= '<div class="drop">';
			$return .= '<ul>';
			$return .= '<li class="active">';
			$return .= '<ul>';

			while(!$rs->EOF())
			{
				$submenus = ''; $submenus_mobile = '';
				$menu_id = $rs->db_data['ID'];
				$menu_ext = $rs->db_data['img_ext'];
				$catalogo_id = $rs->db_data['catalogo_id'];
				$catalogo_tipo = $rs->db_data['tipo_catalogo']; // CAMPO 'Catálogo de Produtos' MARCAS ou CATEGORIAS
				$menu_tipo = $rs->db_data['TipoMenu']; // SELECT BOX BACKOFFICE 'Tipo Menu'
				$menu_nome = $rs->db_data['URL_Nome2'];
				$menu_url = seo($rs->db_data['URL_Link']);

				$menu_nome = trim(str_replace('Reparações','',$menu_nome));
				$img = '';
				if(dotbright_image_file_exists('files/menus/'.$menu_id.'_1.'.$menu_ext) && $menu_ext!='') $img = '<img src="files/menus/'.$menu_id.'_1.'.$menu_ext.'" alt="">';

				$p_seo2 = $menu_url;

				$class = '';
				if($catalogo_id > 0 && $menu_tipo == 'familias')
				{
					$sum_fams = show_menu_subfamilias($catalogo_id,0,$rec_fams,$reparacoes);
					if(in_array($catalogo_id,$rec_fams)) $class = 'class="t active"'; else $class = 'class="t"';
					$p_seo = seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$catalogo_id,$catalogo_id,1);
					//if(empty($sum_fams['return']) || $reparacoes == 1) $p_seo2 = $p_seo; else $p_seo2 = '#';

if($_GET['gerar']==1)print_r($sum_fams);
					if(empty($sum_fams['return'])) $p_seo2 = $p_seo; else $p_seo2 = '#';
				}

				$return .= '<li '.$class.'>';
				// <i class="fa fa-angle-right" aria-hidden="true"></i>
				
				if($p_seo2 != '#')$return .= '<a style="width:100%" href="'.$p_seo2.'" class="t url">'.$img.' '.$menu_nome.'</a>';
				else $return .= '<a style="width:100%" href="'.$p_seo2.'" class="t cat">'.$img.' '.$menu_nome.'</a>';

				if(!empty($sum_fams['return']) && ($catalogo_id > 0 && $menu_tipo == 'familias')) $return .= '<ul>'.$sum_fams['return'].'</ul>';

				$return .= '</li>';

				$rs->db_next();
			}
			$return.='</ul></li></ul><div class="c"></div></div>';
		}
		else
		{
			$rs4=new Dataobj;
			$sql = 'SELECT
					m.ID,m.img_ext,m.catalogo_id,m.tipo_catalogo,m.TipoMenu,m.URL_Link,
					ml.URL_Nome2
				FROM
					menus_links m
					INNER JOIN	menus_links_lang ml ON ml.ID_PAI=m.ID
				WHERE
					m.ID="'.$id_pai.'"
					AND m.Visivel=1
					AND ml.Lang="'.$GLOBALS['site_lang'].'"
					'.(isset($dados_cliente) && is_array($dados_cliente) ? ' AND (Logado = 2 OR Logado = 0)' : ' AND (Logado = 1 OR Logado = 0)').'
				ORDER BY
					m.Ordem ASC
				';

			$rs4->db_open($db_link,$DB_name,$sql);
			if(!$rs4->EOF())
			{
				$return .= '<div style="color:#ec870e;font-size:16px;border-bottom:solid 1px #232323">Reparações</div>';

				$return .= '<div class="drop">';
				$return .= '<ul>';
				$return .= '<li class="active">';

				$sum_fams = show_menu_subfamilias($rs4->db_data['catalogo_id'],0,$rec_fams,$reparacoes);
				if(!empty($sum_fams['return'])) $return .= '<ul>'.$sum_fams['return'].'</ul>';

				$return.='</li></ul><div class="c"></div></div>';
			}
			$rs4->db_close();
		}
		$rs->db_close();

		$fh = fopen($cache_filename,'w');
		fwrite($fh,$return);
		fclose($fh);
		chmod($cache_filename,0755);

	}

	return $return;
}
function show_menus_families($id_pai = 0)
{
	$id_pai = 1;
	$dados_cliente = cliente_logado();
	$ID_ORG = 0;
	if(isset($_GET['cat']) && intval($_GET['cat']) > 0)
	{
		$ID_ORG =  intval($_GET['cat']);
	}
	$cache_filename = SITE_SHELL_URL.'files/tmp/show_menus_families_'.$id_pai.'_'.$ID_ORG.'_'.$GLOBALS['site_lang'].'.txt';

	$time_expire = time() - 60*60*24*1;
	$gerar = '';

	if(isset($_GET['gerar'])) $gerar = intval($_GET['gerar']);
	// echo $cache_filename;
	//$gerar=1;	
	// if($gerar!=1 && file_exists($cache_filename) && filectime($cache_filename) > $time_expire) $return = file_get_contents($cache_filename);
	// else
	// {
		$rec_fams = getCategoriesRec($ID_ORG);
		global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs=new Dataobj;
		$rs2=new Dataobj;
		$rs3=new Dataobj;

		$return='';
		/*
		if((isset($_GET['cat']) && intval($_GET['cat']) > 0) || (isset($_GET['pesquisa']) && $_GET['pesquisa'] != ''))
		{
			//$return .= '<div class="horizontalTab2"><ul class="resp-tabs-list"><li>'.getLinguas('txt_f_filterby',$GLOBALS['site_lang']).':</li><li>'.getLinguas('txt_categorias',$GLOBALS['site_lang']).'</li></ul><div class="resp-tabs-container"><div>';
			$return .= filtros_left(1);
			//$return .=  '</div><div>';
		}
		*/
		$return .= filtros_left(1);

		$return_resp='';
		$sql = 'SELECT
					m.ID,m.img_ext,m.catalogo_id,m.tipo_catalogo,m.TipoMenu,m.URL_Link,
					ml.URL_Nome2
				FROM
					menus_links m
					INNER JOIN	menus_links_lang ml ON ml.ID_PAI=m.ID
				WHERE
					m.ID_ORG="'.$id_pai.'"
					AND m.Visivel=1
					AND ml.Lang="'.$GLOBALS['site_lang'].'"
					'.(isset($dados_cliente) && is_array($dados_cliente) ? ' AND (Logado = 2 OR Logado = 0)' : ' AND (Logado = 1 OR Logado = 0)').'
				ORDER BY
					m.Ordem ASC
				';


		$rs->db_open($db_link,$DB_name,$sql);


		if(!$rs->EOF())
		{
			$return .= '<div class="PC drop 1">';
			$return .= '<ul>';
			$return .= '<li class="active">';
			$return .= '<ul>';

			while(!$rs->EOF())
			{
				$submenus = ''; $submenus_mobile = '';
				$menu_id = $rs->db_data['ID'];
				$menu_ext = $rs->db_data['img_ext'];
				$catalogo_id = $rs->db_data['catalogo_id'];
				$catalogo_tipo = $rs->db_data['tipo_catalogo']; // CAMPO 'Catálogo de Produtos' MARCAS ou CATEGORIAS
				$menu_tipo = $rs->db_data['TipoMenu']; // SELECT BOX BACKOFFICE 'Tipo Menu'
				$menu_nome = $rs->db_data['URL_Nome2'];
				$menu_url = seo($rs->db_data['URL_Link']);
				$img = '';
				if(dotbright_image_file_exists('files/menus/'.$menu_id.'_1.'.$menu_ext) && $menu_ext!='') $img = '<img src="files/menus/'.$menu_id.'_1.'.$menu_ext.'" alt="">';

				$p_seo2 = $menu_url;

				$class = '';

				// echo '<pre>catalogo_id: '.$catalogo_id.'<br>menu_tipo: '.$menu_tipo.'</pre>';
				if($catalogo_id > 0 && $menu_tipo == 'familias')
				{
					$sum_fams = show_menu_subfamilias_menu($menu_id,0,$rec_fams,$reparacoes);
					if(in_array($catalogo_id,$rec_fams)) $class = 'class="t active"'; else $class = 'class="t"';
					$p_seo = seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$catalogo_id,$catalogo_id,1);
					if(empty($sum_fams['return']) || $reparacoes == 1) $p_seo2 = $p_seo; else $p_seo2 = '#';
				}

				$return .= '<li '.$class.'>';
				$menu_nome = trim(str_replace('Reparações','',$menu_nome));				
				if($p_seo2 != '#')$return .= '<a style="width:100%" href="'.$p_seo2.'" class="t url">'.$img.' '.$menu_nome.'</a>';
				else $return .= '<a style="width:100%" href="'.$p_seo2.'" class="t cat">'.$img.' '.$menu_nome.'</a>';

				if(!empty($sum_fams['return']) && ($catalogo_id > 0 && $menu_tipo == 'familias')) $return .= '<ul>'.$sum_fams['return'].'</ul>';

				$return .= '</li>';

				$rs->db_next();
			}
			$return.='</ul></li></ul><div class="c"></div></div>';
		}
		else
		{
			$rs4=new Dataobj;
			$sql = 'SELECT
					m.ID,m.img_ext,m.catalogo_id,m.tipo_catalogo,m.TipoMenu,m.URL_Link,
					ml.URL_Nome2
				FROM
					menus_links m
					INNER JOIN	menus_links_lang ml ON ml.ID_PAI=m.ID
				WHERE
					m.ID="'.$id_pai.'"
					AND m.Visivel=1
					AND ml.Lang="'.$GLOBALS['site_lang'].'"
					'.(isset($dados_cliente) && is_array($dados_cliente) ? ' AND (Logado = 2 OR Logado = 0)' : ' AND (Logado = 1 OR Logado = 0)').'
				ORDER BY
					m.Ordem ASC
				';
			//echo $sql;

			$rs4->db_open($db_link,$DB_name,$sql);
			if(!$rs4->EOF())
			{
				$return .= '<div class="PC drop">';
				$return .= '<ul>';
				$return .= '<li class="active">';

				$sum_fams = show_menu_subfamilias($rs4->db_data['catalogo_id'],0,$rec_fams,$reparacoes);
				if(!empty($sum_fams['return'])) $return .= '<ul>'.$sum_fams['return'].'</ul>';

				$return.='</li></ul><div class="c"></div></div>';
			}
			$rs4->db_close();
		}
		$rs->db_close();

		/*if(isset($_GET['cat']) && intval($_GET['cat']) > 0)
		{
			$return .= '</div></div></div>';
		}*/

		$fh = fopen($cache_filename,'w');
		fwrite($fh,$return);
		fclose($fh);
		chmod($cache_filename,0755);

	// }

	return $return;
}

function show_menus_families_inner($id_pai = 0)
{
	$dados_cliente = cliente_logado();
	$ID_ORG = 0;
	if(isset($_GET['cat']) && intval($_GET['cat']) > 0)
	{
		$ID_ORG =  intval($_GET['cat']);
	}
	$cache_filename = SITE_SHELL_URL.'files/tmp/show_menus_families_inner_'.$id_pai.'_'.$ID_ORG.'_'.$GLOBALS['site_lang'].'.txt';

	$time_expire = time() - 60*60*24*1;
	$gerar = '';

	if(isset($_GET['gerar'])) $gerar = intval($_GET['gerar']);
	// echo $cache_filename;
	//$gerar=1;	
	if($gerar!=1 && file_exists($cache_filename) && filectime($cache_filename) > $time_expire) $return = file_get_contents($cache_filename);
	else
	{
		$rec_fams = getCategoriesRec($ID_ORG);
		global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs=new Dataobj;
		$rs2=new Dataobj;
		$rs3=new Dataobj;

		$return='';
		if((isset($_GET['cat']) && intval($_GET['cat']) > 0) || (isset($_GET['pesquisa']) && $_GET['pesquisa'] != ''))
		{
			//$return .= '<div class="horizontalTab2"><ul class="resp-tabs-list"><li>'.getLinguas('txt_f_filterby',$GLOBALS['site_lang']).':</li><li>'.getLinguas('txt_categorias',$GLOBALS['site_lang']).'</li></ul><div class="resp-tabs-container"><div>';
			$return .= filtros_left(1);
			//$return .=  '</div><div>';
		}

		$return .= '<div class="drop">';
		$return .= '<ul>';
		$return .= '<li class="active">';

		$sum_fams = show_menu_subfamilias_inner($rs4->db_data['catalogo_id'],0,$rec_fams,$reparacoes);
		if(!empty($sum_fams['return'])) $return .= '<ul>'.$sum_fams['return'].'</ul>';

		$return.='</li></ul><div class="c"></div></div>';

		/*if(isset($_GET['cat']) && intval($_GET['cat']) > 0)
		{
			$return .= '</div></div></div>';
		}*/

		$fh = fopen($cache_filename,'w');
		fwrite($fh,$return);
		fclose($fh);
		chmod($cache_filename,0755);

	}

	return $return;
}

function show_menu_familias($ID_ORG=0,$reparacoes=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$m_http_url_images,$site_http_url,$tema,$site_lang,$page_id,$seo,$site_shell_url;
    $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new Dataobj; $rs2=new Dataobj;

	$ID_ORG=0;
	if(isset($_GET['cat']) && intval($_GET['cat']) > 0)
	{
		$ID_ORG= intval($_GET['cat']);
	}
	$cache_filename = SITE_SHELL_URL.'files/tmp/show_menu_familias_'.$id_pai.'_'.$ID_ORG.'_'.$GLOBALS['site_lang'].'.txt';
	$time_expire = time() - 60*60*24*1;
	$gerar = '';

	if(isset($_GET['gerar'])) $gerar = intval($_GET['gerar']);
	if($gerar!=1 && file_exists($cache_filename) && filectime($cache_filename) > $time_expire) $return = file_get_contents($cache_filename);
	else
	{
		$rec_fams=getCategoriesRec($ID_ORG);
		$return='';
		$sql='SELECT pf.ID,pfl.Cabecalho,pfl.Cabecalho2 FROM products_families pf INNER JOIN products_families_lang pfl ON pfl.ID_PAI=pf.ID WHERE pf.ID_ORG=0 AND pfl.lang="'.$GLOBALS['site_lang'].'" AND visivel=1 ORDER BY pf.Ordem=0,pf.Ordem';
		$rs->db_open($db_link,$DB_name,$sql);

		if(!$rs->EOF())
		{
			$x=7;
			$y=0;
			$return .= '<div class="drop">';
			$return .= '<ul>';
			$return .= '<li class="active">';
			$return .= '<ul>';
			while(!$rs->EOF())
			{
				$nomeb=($rs->db_data['Cabecalho2']!='' ? $rs->db_data['Cabecalho2'] : ''.$rs->db_data['Cabecalho']);
				$catalogo_id=$rs->db_data['ID'];
				$sum_fams=show_menu_subfamilias($catalogo_id,0,$rec_fams,$reparacoes);

				$class='';
				if(in_array($catalogo_id,$rec_fams)) $class='class="t active"'; else $class='class="t"';
				$p_seo=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$catalogo_id,$catalogo_id,1);
				if(empty($sum_fams['return']) || $reparacoes == 1) $p_seo2=$p_seo; else $p_seo2='#';
				$return .= '<li '.$class.'>';
				$return .= '<a style="width:100%" href="'.$p_seo2.'" class="t" alt="'.$nomeb.'"><i class="fa fa-angle-right" aria-hidden="true"></i> .'.$nomeb.'</a>';
				if(!empty($sum_fams['return'])) $return .= $sum_fams['return'];
				$return .= '</li>';
				$y++;
				$rs->db_next();
			}
			$return.='</ul></li></ul><div class="c"></div></div>';
		}
		$rs->db_close();

		$fh = fopen($cache_filename,'w');
		fwrite($fh,$return);
		fclose($fh);
	}
	return $return;
}
function show_menu_subfamilias_v_anterior($ID_ORG=0,$z=0,$rec_fams=array(),$reparacoes=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$m_http_url_images,$site_http_url,$tema,$site_lang,$page_id,$seo,$site_shell_url;
    $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new Dataobj;
    $rs2=new Dataobj;
	$return='';
    $sql='SELECT pf.ID,pfl.Cabecalho,pfl.Cabecalho2 FROM products_families pf INNER JOIN products_families_lang pfl ON pfl.ID_PAI=pf.ID WHERE pf.ID_ORG='.$ID_ORG.' AND pfl.lang="'.$GLOBALS['site_lang'].'" AND visivel=1 ORDER BY pf.Ordem=0,pf.Ordem';
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$x=7; $y=0;
		while(!$rs->EOF())
		{
			$nomeb=(trim($rs->db_data['Cabecalho2']) != '' ? $rs->db_data['Cabecalho2'] : $rs->db_data['Cabecalho']);
			$catalogo_id=$rs->db_data['ID'];
			$sum_fams=show_menu_subfamilias($catalogo_id,0,$rec_fams);
			$p_seo=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$catalogo_id,$catalogo_id,1);
			if(empty($sum_fams['return']) || $reparacoes == 1) $p_seo2=$p_seo; else $p_seo2='#';
			$class='';
			if(in_array($catalogo_id,$rec_fams)) $class='class="t active"'; else $class='class="t"';
			$return .= '<li '.$class.'>';
			$return .= '<a class="t '.(empty($sum_fams['return']) ? 'no' : '').'" style="width:100%;" href="'.$p_seo2.'" alt="'.$nomeb.'">'.$nomeb.'</a>';
			if(!empty($sum_fams['return'])) $return .= '<ul>'.$sum_fams['return'].'</ul>';
			$return .= '</li>';
			$y++;
			$rs->db_next();
		}
	}
	$total=$rs->db_rows;
	$rs->db_close();
	return array('return' => $return,'total' => $total);
}
function show_menu_subfamilias($ID_ORG=0,$z=0,$rec_fams=array(),$reparacoes=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$m_http_url_images,$site_http_url,$tema,$site_lang,$page_id,$seo,$site_shell_url;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new Dataobj;
    $rs2=new Dataobj;
    $familia = new Dataobj;
    $familia2 = new Dataobj;
	$return='';
    $sql='SELECT
				pf.ID,pfl.Cabecalho
			FROM
				products_families pf INNER JOIN products_families_lang pfl ON pfl.ID_PAI=pf.ID
			WHERE
				pf.ID_ORG='.$ID_ORG.'
				AND pfl.lang="'.$GLOBALS['site_lang'].'"
				AND visivel=1
			ORDER BY
				pf.Ordem ASC';

	if($ID_ORG==85)$reparacoes=1;

	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$x=7;
		$y=0;
		while(!$rs->EOF())
		{
			$nomeb=$rs->db_data['Cabecalho'];
			$nomeb = trim(str_replace('Reparações','',$nomeb));

			$catalogo_id=$rs->db_data['ID'];
			$sum_fams=show_menu_subfamilias($catalogo_id,0,$rec_fams,$reparacoes);
			
			if($GLOBALS['page_id']==148 || $reparacoes==1)$p_seo=seo('pages.php?page_id=148&cat='.$catalogo_id,$catalogo_id,1);
			else $p_seo=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$catalogo_id,$catalogo_id,1);

			if(empty($sum_fams['return'])) $p_seo2=$p_seo; else $p_seo2='#';
			//if(empty($sum_fams['return']) || $reparacoes == 1) $p_seo2=$p_seo; else $p_seo2='#';

			$class='';
			if(in_array($catalogo_id,$rec_fams)) $class='class="t active"'; else $class='class="t"';
			$return .= '<li '.$class.'>';
			if($p_seo2 != '#')$return .= '<a class="t url" style="width:100%;" href="'.$p_seo2.'" alt="'.$nomeb.'">'.$nomeb.'</a>';
			else $return .= '<a class="t cat" style="width:100%;" href="'.$p_seo2.'" alt="'.$nomeb.'">'.$nomeb.'</a>';

			if(!empty($sum_fams['return'])) $return .= '<ul>'.$sum_fams['return'].'</ul>';
			$return .= '</li>';
			$y++;
			$rs->db_next();
		}
	}
	$total=$rs->db_rows;
	$rs->db_close();
	return array('return' => $return,'total' => $total);
}

function show_menu_subfamilias_menu($ID_ORG=0,$z=0,$rec_fams=array(),$reparacoes=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$m_http_url_images,$site_http_url,$tema,$site_lang,$page_id,$seo,$site_shell_url;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new Dataobj;
    $rs2=new Dataobj;
    $familia = new Dataobj;
    $familia2 = new Dataobj;
	$return='';

	$sql = 'SELECT
					m.ID,m.img_ext,m.catalogo_id,m.tipo_catalogo,m.TipoMenu,m.URL_Link,
					ml.URL_Nome2
				FROM
					menus_links m
					INNER JOIN	menus_links_lang ml ON ml.ID_PAI=m.ID
				WHERE
					m.ID_ORG="'.$ID_ORG.'"
					AND m.Visivel=1
					AND ml.Lang="'.$GLOBALS['site_lang'].'"
					'.(isset($dados_cliente) && is_array($dados_cliente) ? ' AND (Logado = 2 OR Logado = 0)' : ' AND (Logado = 1 OR Logado = 0)').'
				ORDER BY
					m.Ordem ASC
				';

				// echo '<pre>'.$sql.'</pre>';

	if($ID_ORG==85)$reparacoes=1;

	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$p_seo = '';
		$p_seo2 = '';
		$menu_url= '';
		while(!$rs->EOF())
		{
			$submenus = ''; $submenus_mobile = '';
			$menu_id = $rs->db_data['ID'];
			$menu_ext = $rs->db_data['img_ext'];
			$catalogo_id = $rs->db_data['catalogo_id'];
			$catalogo_tipo = $rs->db_data['tipo_catalogo'];
			$menu_tipo = $rs->db_data['TipoMenu'];
			$menu_nome = $rs->db_data['URL_Nome2'];
			$menu_url = seo($rs->db_data['URL_Link']);
			$img = '';

			$p_seo2 = $menu_url;

			$class = '';

			if($catalogo_id > 0 && $menu_tipo == 'familias')
			{
				$sum_fams2 = show_menu_subfamilias2($catalogo_id,0,$rec_fams,$reparacoes);

				if(in_array($catalogo_id,$rec_fams)) $class = 'class="t active"'; else $class = 'class="t"';
				$p_seo = seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$catalogo_id,$catalogo_id,1);
				if(empty($sum_fams2['return']) || $reparacoes == 1) $p_seo2 = $p_seo; else $p_seo2 = '#';
			}

			$return .= '<li '.$class.'>';

			$menu_nome = trim(str_replace('Reparações','',$menu_nome));
			
			if($p_seo2 != '#')$return .= '<a style="width:100%" href="'.$p_seo2.'" class="t url">'.$img.' '.$menu_nome.'</a>';
			else $return .= '<a style="width:100%" href="'.$p_seo2.'" class="t cat">'.$img.' '.$menu_nome.'</a>';

			
			//if($_GET['pp']==1)print_r($sum_fams2['return']);
			if(!empty($sum_fams2['return'])) $return .= '<ul>'.$sum_fams2['return'].'</ul>';

			// if(!empty($sum_fams2['return']) && ($catalogo_id > 0 && $menu_tipo == 'familias')) $return .= '<ul>'.$sum_fams2['return'].'</ul>';

			$return .= '</li>';
			$rs->db_next();
		}
	}
	$total=$rs->db_rows;
	$rs->db_close();
	return array('return' => $return,'total' => $total);
}

function show_menu_subfamilias_inner($ID_ORG=0,$z=0,$rec_fams=array(),$reparacoes=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$m_http_url_images,$site_http_url,$tema,$site_lang,$page_id,$seo,$site_shell_url;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new Dataobj;
    $rs2=new Dataobj;
    $familia = new Dataobj;
    $familia2 = new Dataobj;
	$return='';

	$ID_ORG=$_GET['cat'];

    $sql='SELECT
				pf.ID,pfl.Cabecalho
			FROM
				products_families pf INNER JOIN products_families_lang pfl ON pfl.ID_PAI=pf.ID
			WHERE
				pf.ID_ORG='.$ID_ORG.'
				AND pfl.lang="'.$GLOBALS['site_lang'].'"
				AND visivel=1
			ORDER BY
				pf.Ordem ASC';
				//pf.Ordem=0,pf.Ordem';

	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$x=7;
		$y=0;
		while(!$rs->EOF())
		{
			$nomeb=$rs->db_data['Cabecalho'];
			$catalogo_id=$rs->db_data['ID'];
			$sum_fams=show_menu_subfamilias($catalogo_id,0,$rec_fams);
			$p_seo=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$catalogo_id,$catalogo_id,1);
			if(empty($sum_fams['return']) || $reparacoes == 1) $p_seo2=$p_seo; else $p_seo2='#';
			$class='';
			if(in_array($catalogo_id,$rec_fams)) $class='class="t active"'; else $class='class="t"';
			// if(temproductfiltersfams($catalogo_id))
			// {
				$return .= '<li '.$class.'>';

				if($p_seo2 != '#')$return .= '<a class="t url" style="width:100%;" href="'.$p_seo2.'" alt="'.$nomeb.'">'.$nomeb.'</a>';
				else $return .= '<a class="t cat" style="width:100%;" href="'.$p_seo2.'" alt="'.$nomeb.'">'.$nomeb.'</a>';

				if(!empty($sum_fams['return'])) $return .= '<ul>'.$sum_fams['return'].'</ul>';
				$return .= '</li>';
			$y++;
			$rs->db_next();
		}
		// $return.='</ul>';
	}
	$total=$rs->db_rows;
	$rs->db_close();

	return array('return' => $return,'total' => $total);
}



function show_menu_subfamilias2($ID_ORG=0,$z=0,$rec_fams=array(),$reparacoes=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$m_http_url_images,$site_http_url,$tema,$site_lang,$page_id,$seo,$site_shell_url;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new Dataobj;
    $rs2=new Dataobj;
    $familia = new Dataobj;
    $familia2 = new Dataobj;
	$return='';

    $sql='SELECT
				pf.ID,pfl.Cabecalho
			FROM
				products_families pf INNER JOIN products_families_lang pfl ON pfl.ID_PAI=pf.ID
			WHERE
				pf.ID_ORG='.$ID_ORG.'
				AND pfl.lang="'.$GLOBALS['site_lang'].'"
				AND visivel=1
			ORDER BY
				pf.Ordem ASC';
				//pf.Ordem=0,pf.Ordem';

	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$x=7;
		$y=0;
		while(!$rs->EOF())
		{
			$nomeb=$rs->db_data['Cabecalho'];
			$catalogo_id=$rs->db_data['ID'];
			$sum_fams=show_menu_subfamilias2($catalogo_id,0,$rec_fams,$reparacoes);
			$p_seo=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$catalogo_id,$catalogo_id,1);
			if(empty($sum_fams['return']) || $reparacoes == 1) $p_seo2=$p_seo; else $p_seo2='#';
			$class='';
			if(in_array($catalogo_id,$rec_fams)) $class='class="t active"'; else $class='class="t"';
			// if(temproductfiltersfams($catalogo_id))
			// {
				$return .= '<li '.$class.'>';

				if($p_seo2 != '#')$return .= '<a class="t url" style="width:100%;" href="'.$p_seo2.'" alt="'.$nomeb.'">'.$nomeb.'</a>';
				else $return .= '<a class="t cat" style="width:100%;" href="'.$p_seo2.'" alt="'.$nomeb.'">'.$nomeb.'</a>';

				if(!empty($sum_fams['return'])) $return .= '<ul>'.$sum_fams['return'].'</ul>';
				$return .= '</li>';
			$y++;
			$rs->db_next();
		}
		// $return.='</ul>';
	}
	$total=$rs->db_rows;
	$rs->db_close();

	return array('return' => $return,'total' => $total);
}

function getProdutosRelacionados($prod_id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$prods=get_produtc_info($prod_id);
	$idfam=$prods['fam_id'];

	// $result=array();

	$sql='
			SELECT
				pfo.ID_Prod2 as idprod
			FROM
				products p
				INNER JOIN produtos_opcoes pfo ON p.ID=pfo.ID_Prod
			WHERE
				pfo.ID_Prod='.$prod_id.'
				AND p.Aproved=1
			GROUP BY
				pfo.ID_Prod2
		';
	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	if($rs->EOF())
	{
		$sql='
				SELECT
					pfo.ID_Prod as idprod
				FROM
					products p
					INNER JOIN products_families_obj pfo ON p.ID=pfo.ID_Prod
				WHERE
					pfo.ID_ORG='.$idfam.'
					AND pfo.ID_Prod!='.$prod_id.'
					AND p.Aproved=1
				GROUP BY
					pfo.ID_Prod
				ORDER BY
						RAND()
				LIMIT 20
			';
		// echo $sql;
		$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	}
	if(!$rs->EOF())
	{

		while(!$rs->EOF())
		{
			$i++;
			$prod_id=$rs->db_data['idprod'];

			$result[]=$prod_id;

			$rs->db_next();
		}
	}
	$rs->db_close();


	return $result;
}

function BuscarProdutosNovidades()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;

	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql='
			SELECT
				P.ID
			FROM
				products AS P
				INNER JOIN products_lang AS PL ON PL.ID_PAI=P.ID
				INNER JOIN products_objs AS PO ON P.ID=PO.obj_type_id
			WHERE
				P.Aproved=1
				AND
				(
					(PO.obj_novo=1)
				)
				AND	PL.Lang="' . $GLOBALS['site_lang'] . '"
			GROUP BY
				P.ID
			LIMIT 20
		';
	$rs->db_open($db_link,$DB_name,$sql);
	if($rs->EOF())
	{
		$rs->db_close();
		$sql='
				SELECT
					P.ID
				FROM
					products P
					INNER JOIN products_lang AS PL ON PL.ID_PAI=P.ID
					INNER JOIN products_objs AS PO ON P.ID=PO.obj_type_id
				WHERE
					P.Aproved=1
					AND PO.obj_stock > 0
				GROUP BY
					P.ID
				ORDER BY
						RAND()
				LIMIT 100
			';
		// echo $sql;
		$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);

	}
	$s=array();
	if (!$rs->EOF())
	{
		while (!$rs->EOF())
		{
			$prod_id=$rs->db_data['ID'];

			$s[]=$prod_id;

			$rs->db_next();
		}
	}
	$rs->db_close();

	return $s;
}

function BuscarProdutosPromocoes($PRODS_LIMIT=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;

	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql='
			SELECT
				P.ID
			FROM
				products AS P
				INNER JOIN products_lang AS PL ON PL.ID_PAI=P.ID
				INNER JOIN products_objs AS PO ON P.ID=PO.obj_type_id
			WHERE
				P.Aproved=1
				AND
				(
					(PO.obj_promo_ativo=1)
				)
				AND	PL.Lang="' . $GLOBALS['site_lang'] . '"
			GROUP BY
				P.ID
			'.($PRODS_LIMIT > 0 ? 'Limit '.$PRODS_LIMIT: '').'
		';
		// echo $sql;
	$rs->db_open($db_link,$DB_name,$sql);
	$s=array();
	if (!$rs->EOF())
	{
		while (!$rs->EOF())
		{
			$prod_id=$rs->db_data['ID'];
			$s[]=$prod_id;
			$rs->db_next();
		}
	}
	$rs->db_close();

	return $s;
}
function bloco_catalogo_fp()
{
	$return='<div class="filtros_wrapper">';

	$return .= filtros_left();
	$return .= '</div>';
	$return .= '<div class="cat_wrap" style="width:100%">

	<div class="catalogo_topo_filtros">
	<div class="c"></div>
	<div class="paginacao"></div>';
	if(!isset($_GET['view'])) $_GET['view']=1;
	$return .= '<div style="float:left; width:72px;display:none;">

	<a id="icon_view" class="icon_view" onclick="mudaVista(0)">
	<img id="img_icons_view" src="'.$GLOBALS['site_http_url'].'layout/'.PROJETO.'/filtros/icons.png" alt="L">
	<img id="img_icons_view_activo" src="'.$GLOBALS['site_http_url'].'layout/'.PROJETO.'/filtros/icons_activo.png" alt="L">
	</a>
	<a id="list_view" class="list_view view_activo" onclick="mudaVista(1)">
	<img id="img_list_view" style="display: none" src="'.$GLOBALS['site_http_url'].'layout/'.PROJETO.'/filtros/lista.png" alt="L">
	<img id="img_list_view_activo" style="display:inline" src="'.$GLOBALS['site_http_url'].'layout/'.PROJETO.'/filtros/lista_activo.png" alt="L">
	</a>
	</div>
	<div class="filtro_order" style="display:none">
	<select id="product_order" class="chzn-select2" style="display:none">
	<option value="0" selected="selected">A..Z</option>
	<option value="1">Z..A</option>
	<option value="2">'.getLinguas('txt_maisbarato',$GLOBALS['site_lang']).'</option>
	<option value="3">'.getLinguas('txt_maiscaro',$GLOBALS['site_lang']).'</option>
	</select>
	</div>
	<div class="filtro_view" style="display:none">
	<select class="chzn-select2" name="f_visualizacao" id="f_visualizacao" >
	<option value="5" selected="selected">5 por página</option>
	</select>
	</div>
	<div class="c"></div>
	</div>
	<div class="c"></div>
	<div id="product_show"></div>
	<div class="c"></div>
	<div class="catalogo_topo_filtros" style="background:none;display:none"><div class="paginacao" style="margin-bottom:20px"></div></div>
	<div class="c"></div>
	</div>
	<div class="c"></div>';

	return $return;
}

function bloco_catalogo_fp_rela()
{

	$return='<div class="filtros_wrapper">';

	$return .= filtros_left();
	$return .= '</div>';
	$return .= '<div class="cat_wrap" style="width:100%">

	<div class="catalogo_topo_filtros">
	<div class="c"></div>
	<div class="paginacao"></div>';
	//printr($_GET);
	//echo 'flag: 1561 '.$_GET['cat'].'<br>';
	if(!isset($_GET['view'])) $_GET['view']=1;
	$return .= '<div style="float:left; width:72px;display:none;">

	<a id="icon_view" class="icon_view" onclick="mudaVista(0)">
	<img id="img_icons_view" src="'.$GLOBALS['site_http_url'].'layout/'.PROJETO.'/filtros/icons.png" alt="L">
	<img id="img_icons_view_activo" src="'.$GLOBALS['site_http_url'].'layout/'.PROJETO.'/filtros/icons_activo.png" alt="L">
	</a>
	<a id="list_view" class="list_view view_activo" onclick="mudaVista(1)">
	<img id="img_list_view" style="display: none" src="'.$GLOBALS['site_http_url'].'layout/'.PROJETO.'/filtros/lista.png" alt="L">
	<img id="img_list_view_activo" style="display:inline" src="'.$GLOBALS['site_http_url'].'layout/'.PROJETO.'/filtros/lista_activo.png" alt="L">
	</a>
	</div>
	<div class="filtro_order" style="display:none">
	<select id="product_order" class="chzn-select2" style="display:none">
	<option value="0" selected="selected">A..Z</option>
	<option value="1">Z..A</option>
	<option value="2">'.getLinguas('txt_maisbarato',$GLOBALS['site_lang']).'</option>
	<option value="3">'.getLinguas('txt_maiscaro',$GLOBALS['site_lang']).'</option>
	</select>
	</div>
	<div class="filtro_view" style="display:none">
	<select class="chzn-select2" name="f_visualizacao" id="f_visualizacao" >
	<option value="5" selected="selected">5 por página</option>
	</select>
	</div>
	<div class="c"></div>
	</div>
	<div class="c"></div>
	<div id="product_show"></div>
	<div class="c"></div>
	<div class="catalogo_topo_filtros" style="background:none;display:none"><div class="paginacao" style="margin-bottom:20px"></div></div>
	<div class="c"></div>
	</div>
	<div class="c"></div>';

	return $return;
}



function GERA_PRODUTOS_DESTAQUES()
{
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj; $rs2=new DataObj;
	 $on=0;
	// SE O CLIENTE TIVER PRODUTOS CONFIGURADOS DO TIPO NOVO,PROMO,DESTAQUE
	// OR (PO.obj_novo=1 AND PO.obj_novo_data >= CURDATE())
	// OR (PO.obj_promo_ativo=1 AND PO.obj_promo_data >= CURDATE())
	// OR (PO.obj_outlet=1 AND PO.obj_outlet_data >= CURDATE())

	$fams_ord = $GLOBALS['fams_ord'];

	$sql='
			SELECT
				P.ID
			FROM
				products AS P
				INNER JOIN products_lang AS PL ON PL.ID_PAI=P.ID
				INNER JOIN products_objs AS PO ON P.ID=PO.obj_type_id
			WHERE
				P.Aproved=1
				AND
				(
					(PO.obj_destaque=1)
					OR (PO.obj_novo=1)
				)
				AND PO.obj_stock > 0
				AND	PL.Lang="'.$GLOBALS['site_lang'].'"
			GROUP BY
				P.ID
			ORDER BY
				rand()
			LIMIT 20
		';
		//FIELD(ID_ORG,'.$fams_ord.') DESC
		// echo $sql;
	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	if(!$rs->EOF()){ $on=1;}
	if($rs->EOF())
{
		$sql='
				SELECT
					P.ID
				FROM
					products AS P
					INNER JOIN products_lang AS PL ON PL.ID_PAI=P.ID
					INNER JOIN products_objs AS PO ON P.ID=PO.obj_type_id
				WHERE
					P.Aproved=1
					AND PO.obj_stock > 0
					AND PO.obj_promo=0
				GROUP BY
					P.ID
				ORDER BY
					FIELD(ID_ORG,'.$fams_ord.') DESC
				LIMIT 20
				';
		$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	}
	$s=array();
	if(!$rs->EOF())
	{
		while(!$rs->EOF())
		{
			$prod_id=$rs->db_data['ID'];
			$s[]=$prod_id;
			$rs->db_next();
		}
	}
	$rs->db_close();

	if(count($s) < 6 && $on == 1)
	{
		$sql='
					SELECT
						P.ID
					FROM
						products AS P
						INNER JOIN products_lang AS PL ON PL.ID_PAI=P.ID
						INNER JOIN products_objs AS PO ON P.ID=PO.obj_type_id
					WHERE
						P.Aproved=1
						AND P.img_ext<>""
						AND PO.obj_stock > 0
						AND PO.obj_promo=0
					GROUP BY
						P.ID
					ORDER BY
						FIELD(ID_ORG,'.$fams_ord.') DESC
					LIMIT '.(6 - count($s)).'
					';
			$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
			if(!$rs->EOF())
			{
				while(!$rs->EOF())
				{
					$prod_id=$rs->db_data['ID'];

					if(!in_array($prod_id,$s)) $s[]=$prod_id;

					$rs->db_next();
				}
			}
			$rs->db_close();
	}


	return $s;
}

function GERA_PRODUTOS_DESTAQUES_VISTITADOS()
{
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj; $rs2=new DataObj;
	$on=0;

	$sql='
			SELECT
				P.ID
			FROM
				products AS P
				INNER JOIN products_lang AS PL ON PL.ID_PAI=P.ID
				INNER JOIN products_objs AS PO ON P.ID=PO.obj_type_id
			WHERE
				P.Aproved=1
				AND P.visitas > 0
				AND PO.obj_stock > 0
				AND	PL.Lang="'.$GLOBALS['site_lang'].'"
			GROUP BY
				P.ID
			ORDER BY
				rand()
			LIMIT 20
		';
		// echo $sql;
	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	$s=array();
	if(!$rs->EOF())
	{
		while(!$rs->EOF())
		{
			$prod_id=$rs->db_data['ID'];

			$s[]=$prod_id;

			$rs->db_next();
		}
	}
	$rs->db_close();

	return $s;
}
// AND PO.obj_destaque_data >= CURDATE())
function GERA_PRODUTOS_NOVIDADES()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;

	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql='
			SELECT
				P.ID
			FROM
				products AS P
				INNER JOIN products_lang AS PL ON PL.ID_PAI=P.ID
				INNER JOIN products_objs AS PO ON P.ID=PO.obj_type_id
				LEFT JOIN brands AS BR ON P.ID_Marca=BR.ID
			WHERE
				P.Aproved=1
				AND PO.obj_novo=1
				AND	PO.obj_pvp > 0
				AND PO.obj_stock > 0
				AND	PL.Lang="'.$GLOBALS['site_lang'].'"
			GROUP BY
				P.ID
			ORDER BY rand()
		';
	// AND PO.obj_novo_data >= CURDATE()) OR (PO.obj_novo=1 AND PO.obj_novo_data="0000-00-00")
	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	$s=array();
	if(!$rs->EOF())
	{
		while(!$rs->EOF())
		{
			$prod_id=$rs->db_data['ID'];
			$s[]=$prod_id;
			$rs->db_next();
		}
	}
	$rs->db_close();
	return $s;
}



function GERA_PRODUTOS_PROMOCOES()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;

	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql='
			SELECT
				P.ID
			FROM
				products AS P
				INNER JOIN products_lang AS PL
				INNER JOIN products_objs AS PO ON P.ID=PO.obj_type_id
				LEFT JOIN brands AS BR ON P.ID_Marca=BR.ID
			WHERE
				P.Aproved=1
				AND (PO.obj_promo_ativo=1)
				AND	PO.obj_pvp > 0
				AND PO.obj_stock > 0
				AND	PL.Lang="'.$GLOBALS['site_lang'].'"
			GROUP BY
				P.ID
			ORDER BY rand()
			limit 20
			';

	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	$s=array();
	if(!$rs->EOF())
	{
		while(!$rs->EOF())
		{
			$prod_id=$rs->db_data['ID'];
			$s[]=$prod_id;
			$rs->db_next();
		}
	}
	$rs->db_close();
	return $s;
}
function GERA_PRODUTOS_OUTLET()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;

	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$sql='SELECT p.ID as PID,pl.Cabecalho,pol.Cabecalho,pf.*
			FROM products p
			INNER JOIN products_lang pl ON pl.ID_PAI=p.ID
			INNER JOIN filters_products pf ON pf.prod_id=p.ID
			INNER JOIN products_objs po ON po.obj_type_id=p.ID
			INNER JOIN products_objs_lang pol ON pol.ID_PAI=po.obj_id
			INNER JOIN brands b ON b.ID=p.ID_Marca
			WHERE p.Aproved=1 AND po.obj_outlet=1 AND pl.Lang="'.$site_lang.'" AND pol.Lang="'.$site_lang.'" AND b.Visivel=1 group by p.ID';
	$obj->db_open($db_link,$DB_name,$sql);
	return $obj;
}

function BuscarProdutosDia()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;

	$fams_ord = $GLOBALS['fams_ord'];

	$sql='
				SELECT
					P.ID
				FROM
					encomendas_produtos ep
					INNER JOIN products AS P ON P.ID=ep.ID_Produto
					INNER JOIN products_lang AS PL ON PL.ID_PAI=P.ID
					INNER JOIN products_objs AS PO ON P.ID=PO.obj_type_id
				WHERE
					P.Aproved=1
					AND PO.obj_stock > 0
					AND	PL.Lang="'.$GLOBALS['site_lang'].'"
				GROUP BY
					P.ID
				ORDER BY rand()
				Limit 20
			';
			//ORDER BY FIELD(ID_ORG,'.$fams_ord.') DESC
	$rs->db_open($db_link ,$DB_name,$sql);
	if($rs->EOF() || $rs->db_rows < 10)
	{
		$rs->db_close();

		$sql='
				SELECT
						P.ID
					FROM
						products AS P
						INNER JOIN products_lang AS PL ON PL.ID_PAI=P.ID
						INNER JOIN products_objs AS PO ON P.ID=PO.obj_type_id
					WHERE
						P.Aproved=1
						AND PO.obj_stock >= 3
						AND PO.obj_promo=0
						AND PO.obj_pvp > 0
					GROUP BY
						P.ID_ORG
					ORDER BY
						PO.obj_stock ASC,RAND()
					LIMIT 20
			';
			//ORDER BY FIELD(ID_ORG,'.$fams_ord.') DESC
		$rs->db_open($db_link,$DB_name,$sql);
	}
	$s=array();
	if(!$rs->EOF())
	{
		while(!$rs->EOF())
		{
			$prod_id=$rs->db_data['ID'];

			if(dotbright_image_file_exists($GLOBSLS['site_shell_url'].'files/products/'.$prod_id.'_1.jpg') || dotbright_image_file_exists($GLOBSLS['site_shell_url'].'files/products/'.$prod_id.'_1.png')) $s[]=$prod_id;

			$rs->db_next();
		}
	}
	$rs->db_close();


	if(count($s) < 5)
	{
		$sql='
				SELECT
						P.ID
					FROM
						products AS P
						INNER JOIN products_lang AS PL ON PL.ID_PAI=P.ID
						INNER JOIN products_objs AS PO ON P.ID=PO.obj_type_id
					WHERE
						P.Aproved=1
						AND PO.obj_stock > 0
						AND PO.obj_promo=0
					GROUP BY
						P.ID
					ORDER BY
						PO.obj_stock DESC
					ORDER BY rand()
					LIMIT 20
			';
			//FIELD(ID_ORG,'.$fams_ord.') DESC,
			// echo $sql;
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			while(!$rs->EOF())
			{
				$prod_id=$rs->db_data['ID'];

				if(dotbright_image_file_exists($GLOBSLS['site_shell_url'].'files/products/'.$prod_id.'_1.jpg') || dotbright_image_file_exists($GLOBSLS['site_shell_url'].'files/products/'.$prod_id.'_1.png')) $s[] = $prod_id;

				$rs->db_next();
			}
		}
		$rs->db_close();
	}

	return $s;
}
function detectProdutosDia()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;

	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql='SELECT P.ID FROM products AS P INNER JOIN products_lang AS PL ON PL.ID_PAI=P.ID INNER JOIN products_objs AS PO ON P.ID=PO.obj_type_id WHERE P.Aproved=1 AND ( (PO.obj_dia=1 AND PO.obj_dia_data=CURDATE()) ) AND PO.obj_stock > 0 AND	PL.Lang="'.$GLOBALS['site_lang'].'" GROUP BY P.ID';
	$rs->db_open($db_link,$DB_name,$sql);
	$s=0;
	if(!$rs->EOF()) $s=date('Y/m/d');
	$rs->db_close();
	return $s;
}

function BuscarProdutosMaisVendidos()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql='
			SELECT
					P.ID
				FROM
					products AS P
					INNER JOIN products_lang AS PL ON PL.ID_PAI=P.ID
					INNER JOIN products_objs AS PO ON P.ID=PO.obj_type_id
				WHERE
					P.Aproved=1
					AND PO.obj_stock > 0
					AND PO.obj_dia=1
				GROUP BY
					P.ID
				ORDER BY rand()
				LIMIT 20
		';
		//FIELD(ID_ORG,'.$fams_ord.') DESC,
		// echo $sql;
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		while(!$rs->EOF())
		{
			$prod_id=$rs->db_data['ID'];

			if(dotbright_image_file_exists($GLOBSLS['site_shell_url'].'files/products/'.$prod_id.'_1.jpg') || dotbright_image_file_exists($GLOBSLS['site_shell_url'].'files/products/'.$prod_id.'_1.png')) $s[] = $prod_id;

			$rs->db_next();
		}
	}
	$rs->db_close();

	return $s;
}
