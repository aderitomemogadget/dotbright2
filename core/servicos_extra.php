<?php
if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) exit();

//Esta função permite obter apenas a servicos_extra da sub-familia
function servicos_extra_prod($prod_id=0,$prod_valor=0)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$return=array();
	$prod_valor = str_replace(",",".",$prod_valor);

	//--> novo codigo, temos de ir a todas as familias pai
	$sql='SELECT ID_ORG FROM products_families_obj WHERE ID_PROD = '.$prod_id;
	$rs->db_open($db_link, $DB_name, $sql);
	if(!$rs->EOF())
	{
		$ID_ORG=$rs->db_data['ID_ORG'];
	}
	$rs->db_close();
	if($_GET['log']==1)echo "ID_ORG = $ID_ORG | ";

	$fams = BuscarArvoreFams_extra($ID_ORG);
	$ID_Fam=get_servicos_extra_mais_proximo($prod_id,$prod_valor,0);

	if($_GET['log']==1)var_dump($fams);


	$fams2 = implode(',', $fams);
	if($fams2 != '')
	{
		if($_GET['log']==1)var_dump($fams2);


		$sql = 'SELECT familia_id,titulo,garantia_id, garantia_n_anos, bloco_ext, img_ext, garantia_valor, promo_ativo, valor_promo, info FROM servicos_extra eg
					WHERE garantia_ini <= '.$prod_valor.' AND garantia_fim > '.$prod_valor.' AND ((familia_id = '.$ID_Fam.' AND Acumulativo=0) OR (familia_id IN('.$fams2.') AND Acumulativo=1) ) AND garantia_visivel=1 ORDER BY garantia_n_anos ASC';

		if($_GET['log']==1)echo " | ".$sql."|";

		$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
		if(!$rs->EOF())
		{
			while(!$rs->EOF())
			{
				$id=$rs->db_data['garantia_id'];
				$titulo=$rs->db_data['titulo'];
				$n_anos=$rs->db_data['garantia_n_anos'];
				$valor =$rs->db_data['garantia_valor'];
				$promo_ativo =$rs->db_data['promo_ativo'];
				$valor_promo =$rs->db_data['valor_promo'];
				$info =stripslashes($rs->db_data['info']);
				$bloco_ext =$rs->db_data['bloco_ext'];
				$img_ext =$rs->db_data['img_ext'];
				if($bloco_ext!='' && stream_resolve_include_path(SITE_SHELL_URL.'files/servicos_extra/'.$bloco_ext)) $termos=SITE_HTTP_URL.'files/servicos_extra/'.$bloco_ext;
				else $termos=seo('pages.php?page_id=37');

				if($img_ext!='' && stream_resolve_include_path(SITE_SHELL_URL.'files/servicos_extra/'.$img_ext)) $img=SITE_HTTP_URL.'files/servicos_extra/'.$img_ext;
				$return[]=array('titulo'=>$titulo,'id'=>$id,'anos'=>$n_anos,'promo_ativo'=>$promo_ativo,'valor' => ($promo_ativo==1 ? $valor_promo : $valor),'valor_antigo'=>$valor,'info'=>$info,'termos'=>$termos,'img'=>$img);

				$rs->db_next();
			}
		}
		$rs->db_close();
	}

	return $return;
}
function BuscarArvoreFams_extra($id_org=0)
{
	if($_GET['log']==1)echo " PORTES | ";



	$hConn = db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$obj = new DataObj;

	$arvore2=array();
	$output=array();

	$pai = $id_org;
	$i=0;
	while($pai > 0)
	{
		$id_org = $pai;
		$sql = "SELECT ID, ID_ORG FROM products_families WHERE ID=$pai";
	
	if($_GET['log']==1)echo $sql." | ";

		$obj->db_open($hConn,$GLOBALS['DB_name'],$sql);
		$pai = $obj->db_data["ID_ORG"];
		$arvore2[$i] = $obj->db_data["ID"];
		$i++;
	}
	if($_GET['log']==1)var_dump($arvore2);

	$t = (count($arvore2)-1);
	for($a=0; $a<count($arvore2); $a++)
	{
		$output[$t] = $arvore2[$a];
		$t--;
	}
	ksort($output);
	$obj->db_close();

	if($_GET['log']==1)var_dump($output);

	return $output;
}


function get_servicos_extra_mais_proximo($prod_id=0,$prod_valor=0,$fam_id=0)
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
			return get_servicos_extra_mais_proximo(0,$prod_valor,$ID_ORG);
		}
	}
	else
	{
		$sql = 'SELECT familia_id,titulo,garantia_id, garantia_n_anos, bloco_ext, img_ext, garantia_valor, promo_ativo, valor_promo, info FROM servicos_extra eg
				WHERE garantia_ini <= '.$prod_valor.' AND garantia_fim > '.$prod_valor.' AND familia_id = '.$fam_id.' AND garantia_visivel=1 AND Acumulativo=0 ORDER BY garantia_n_anos ASC';


		$rs->db_open($db_link, $DB_name, $sql);
		if(!$rs->EOF())
		{
			$rs->db_close();
			return $fam_id;
		}
		else
		{
			$rs->db_close();
			$sql = 'SELECT ID_ORG FROM products_families WHERE ID = '.$fam_id;
			$rs2->db_open($db_link, $DB_name, $sql);
			if(!$rs2->EOF())
			{
				$ID_ORG=$rs2->db_data['ID_ORG'];
				$rs2->db_close();
				if($ID_ORG>0)return get_servicos_extra_mais_proximo(0,$prod_valor,$ID_ORG);
			}
			$rs2->db_close();
		}
	}
	return 0;
}