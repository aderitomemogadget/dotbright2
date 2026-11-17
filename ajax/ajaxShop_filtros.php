<?php
	$path=dirname( __FILE__ ) . '/../core/';
	include $path . 'core_files.php';
	error_reporting(E_ALL);
	ini_set('display_errors',0);
	$return='';
	if(isset($_POST['codes']) && $_POST['codes'] != '' && isset($_POST['prodid']) && intval($_POST['prodid']) > 0)
	{
		$prod_id=intval($_POST['prodid']);
		$res=filtros_ficha_produto_combo($prod_id, rtrim($_POST['codes'], '|'));
		echo rtrim($res['return'], '[spl]');
		echo '[splp]';
		$prods['prod_precos']=get_produtc_price_combo($prod_id, $res['combo_id']);

		echo '<input type="hidden" value="'.$res['combo_id'].'" id="opcoes_id" />';
		if ($prods['prod_precos']['PRECO_ANTIGO'] > 0 && $prods['prod_precos']['PRECO_ANTIGO'] > $prods['prod_precos']['PVP'])
		{
			$return .= '<div class="tag tag-promocao" style="font-size:16px;line-height:28px">';
			$return .= 'Antes: '.numberformat($prods['prod_precos']['PRECO_ANTIGO']).'<br>';
			$return .= '</div>';
		}
		if($prods['prod_precos']['PVP'] > 0)
		{
			$return .= '<p itemprop="offers" itemscope="" itemtype="http://schema.org/Offer" class="our_price_display">';
			$pvp=$prods['prod_precos']['PVP'];
			if($pvp) $return .= MOEDA.' '.number_format($pvp,2,'.','');
			$return .= '</p>';
		}
		$return .= '<div class="c"></div>';
		echo $return;
		//echo '[splp]99999';
		echo $prods['prod_precos']['STOCK'];

		//Sera que este filtro tem alguma foto?
		$hConn = db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
		$rs=new DataObj;
		$prod_id=$_POST['prodid'];
		$codigos=explode('|',$_POST['codes']);
		$i=0;
		$t=count($codigos);
		while($i<$t)
		{
			$array_codigos=explode('-',$codigos[$i]);
			$cat_id=$array_codigos[0];
			$valor_id=$array_codigos[1];
			
			$sql = 'SELECT img_ext FROM filters_products WHERE cat_id="'.$cat_id.'" AND valor_id="'.$valor_id.'" AND prod_id="'.$prod_id.'"';
			$rs->db_open($hConn,$GLOBALS['DB_name'],$sql);
			$img_ext=$rs->db_data["img_ext"];
			$rs->db_close();

			if($img_ext != '')
			{		
				echo '[splp]';
				echo $foto_name=$prod_id.'_f_'.$cat_id.'_'.$valor_id.'.'.$img_ext;
				$img_path='../../files/products/';
			}
			$i++;
		}

	}
?>