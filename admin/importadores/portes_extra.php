<?php
if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) exit();


//CUSTOS DE LOGISTICA

//Esta função permite obter apenas a servicos_extra da sub-familia
function get_portes_extra_prod($prod_id=0,$prod_valor=0,$fam_id=0)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;

	if($prod_id > 0 )
	{
		$sql='SELECT ID_ORG FROM products_families_obj WHERE ID_PROD = '.$prod_id;
		$rs->db_open($db_link, $DB_name, $sql);
		if(!$rs->EOF())
		{
			$ID_ORG=$rs->db_data['ID_ORG'];
			$rs->db_close();
			return get_portes_extra_prod(0,$prod_valor,$ID_ORG);
		}
	}
	else
	{
		$sql = 'SELECT familia_id,titulo,garantia_id, garantia_n_anos, bloco_ext, img_ext, garantia_valor, promo_ativo, valor_promo, info FROM custos_logisticos eg
				WHERE garantia_ini <= '.$prod_valor.' AND garantia_fim > '.$prod_valor.' AND familia_id = '.$fam_id.' AND garantia_visivel=1 ORDER BY garantia_n_anos ASC';
		$rs->db_open($db_link, $DB_name, $sql);
		if(!$rs->EOF()) {return $rs;}
		else
		{
			$rs->db_close();
			$sql = 'SELECT ID_ORG FROM products_families WHERE ID = '.$fam_id;
			$rs2->db_open($db_link, $DB_name, $sql);
			if(!$rs2->EOF())
			{
				$ID_ORG=$rs2->db_data['ID_ORG'];
				$rs2->db_close();
				if($ID_ORG>0)return get_portes_extra_prod(0,$prod_valor,$ID_ORG);
			}
			$rs2->db_close();
		}
	}
	return $rs;
}

function portes_extra_prod($prod_id=0,$prod_valor=0)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new Dataobj;
	$return=array();

	$prod_valor = str_replace(",",".",$prod_valor);
	$obj=get_portes_extra_prod($prod_id,$prod_valor,0);
	if(!$obj->EOF())
	{
		while(!$obj->EOF())
		{
			$id=$obj->db_data['garantia_id'];
			$titulo=$obj->db_data['titulo'];
			$n_anos=$obj->db_data['garantia_n_anos'];
			$valor =$obj->db_data['garantia_valor'];
			$promo_ativo =$obj->db_data['promo_ativo'];
			$valor_promo =$obj->db_data['valor_promo'];
			$info =stripslashes($obj->db_data['info']);
			$bloco_ext =$obj->db_data['bloco_ext'];
			$img_ext =$obj->db_data['img_ext'];
			if($bloco_ext!='' && file_exists(SITE_SHELL_URL.'files/custos_logisticos/'.$bloco_ext)) $termos=SITE_HTTP_URL.'files/custos_logisticos/'.$bloco_ext;
			else $termos=seo('pages.php?page_id=37');

			if($img_ext!='' && file_exists(SITE_SHELL_URL.'files/custos_logisticos/'.$img_ext)) $img=SITE_HTTP_URL.'files/custos_logisticos/'.$img_ext;
			$return[]=array('titulo'=>$titulo,'id'=>$id,'anos'=>$n_anos,'promo_ativo'=>$promo_ativo,'valor' => ($promo_ativo==1 ? $valor_promo : $valor),'valor_antigo'=>$valor,'info'=>$info,'termos'=>$termos,'img'=>$img);

			$obj->db_next();
		}
	}
	$obj->db_close();

	return $return;
}
