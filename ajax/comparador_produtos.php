<?php
	$path='../core/';
	include $path.'core_files.php';

	echo '<b>COMPARAR PRODUTOS</b>';


	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;

	$dados=cliente_logado();
	if($dados['cli_id'] != '') $cli_id=$dados['cli_id'];
	else $cli_id=0;
	$client_id_temp=$_SESSION['CLIENTE_ID_TEMP'];

	$s = '';
	if($client_id > 0 && $client_id_temp!='') $sql='SELECT cw.PROD_ID FROM clientes_comparador cw WHERE (cw.CLIENT_ID="'.$client_id.'" OR cw.CLIENTE_ID_TEMP="'.$client_id_temp.'") ORDER BY cw.ID DESC limit 0,3';
	else $sql='SELECT cw.PROD_ID FROM clientes_comparador cw WHERE (cw.CLIENTE_ID_TEMP="'.$client_id_temp.'") ORDER BY cw.ID DESC limit 0,3';

	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	if(!$rs->EOF())
	{
		$s .= '<div class="container fix_container" style="overflow:hidden">';
		$c=0;

		while(!$rs->EOF())
		{
			$c++;
			$s .= '<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">';
			$prod_id=$rs->db_data['PROD_ID'];

			$prod = get_produtc_info($prod_id);
			//$s.= show_bloco_catalogo($prods);


			$id=$prod['prod_id'];
			$title=$prod['prod_nome'];
			$image=$prod['prd_imgm_cart'];
			$caminho=seo('pages.php?page_id='.$GLOBALS['products_page_id'].'&prod_id='.$id, $id, 6);
			$ref=$prod['prod_ref'];
			$wish='';
			$res=0;
			$prod_img=(($image=='' || substr_count($image,'no_photo.png')) ? '' : $image);
			if($prod_img == '')
			{
				if(file_exists(dirname( __FILE__ ) .'../files/products/'.$id.'_1_thum.JPG')) $image=IMG_FOLDER.'/products/'.$id.'_1_thum.JPG';
				elseif(file_exists(dirname( __FILE__ ) .'../files/products/'.$id.'_1_thum.jpg')) $image=IMG_FOLDER.'/products/'.$id.'_1_thum.jpg';
				elseif(file_exists(dirname( __FILE__ ) .'../files/products/'.$id.'_1_thum.PNG')) $image=IMG_FOLDER.'/products/'.$id.'_1_thum.PNG';
				elseif(file_exists(dirname( __FILE__ ) .'../files/products/'.$id.'_1_thum.png')) $image=IMG_FOLDER.'/products/'.$id.'_1_thum.png';
				elseif(file_exists(dirname( __FILE__ ) .'../files/products/'.$id.'_1.png')) $image=IMG_FOLDER.'/products/'.$id.'_1.png';
				elseif(file_exists(dirname( __FILE__ ) .'../files/products/'.$id.'_1.jpg')) $image=IMG_FOLDER.'/products/'.$id.'_1.jpg';
			}

			/*$s .= '<li>';
			$s .= '
					<a title="'.$title.'" href="'.$caminho.'" target="_top" alt="'.$title.'">
					<div>
					<div class="prd_img"><span class="helper"></span><img alt="..." src="'.$image.'" '.(substr_count($image,'no_photo.png') ? 'style="height:146px"' : '').' border="0"></div>
					<div class="prd_layer">
					<div class="prd_title">'.$title.'</div>
					<div class="c"></div>
					<div class="c"></div>
					</div>
					</div>
					</a>
					';
			$s .= '</li>';*/


			$s .= '
			<div class="prod-popup">
				<div class="wp-side-image">
					<a title="'.$title.'" href="'.$caminho.'" target="_top" alt="'.$title.'">
						<div class="image"><img style="width:50px" src="'.$image.'" alt="'.$title.'"></div>
					</a>
				</div>
				<div class="wp-side-text">
					<div class="prod-name" style="text-align:left">'.$title.'</div>
					<div class="c" style="text-align:left;cursor:pointer;margin-top:10px" onclick="removeProductComparador(\''.$id.'\',\'0\');return false;">remover</div>
				</div>
				<div class="c"></div>
			</div>';




			$rs->db_next();
			$s.='</div>';
		}
		while($c<3)
		{
			$s .= '<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">';
			$s.='</div>';
			$c++;
		}

		$url=$GLOBALS['site_http_url'].seo('pages.php?page_id='.$GLOBALS['pagina_comparador']);

		//$url=$site_http_url.\seo2(2,$GLOBALS['pagina_comparador'],$prods,$GLOBALS['site_lang']);

		$s.= '<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12" onclick=\'window.open("'.$url.'","_top")\'>';
		$s.= '<span id="button-menu-sup">COMPARAR PRODUTOS<div class="seta closed" id="setamenu"></div></span></b>';
		$s.='</div>';

		$s.='</div>';


	}
	$rs->db_close();

	$s.='<div class="clear"></div>';

	echo $s;

?>