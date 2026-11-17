<?php
// $_GET['log_errors']=1;
require_once "settings.inc.php";
ini_set('meemory_limit', '-1');
ini_set('post_max_size', '2048M');
ini_set('upload_max_filesize', '2048M');

global $DB_host, $DB_user, $DB_pass, $DB_name;
$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
$obj=new DataObj;


function get_prod_fam_prom($prod_id = 0)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$obj = new DataObj;


	$ID_ORG = 0;
	$sql = '
			SELECT
				ID_ORG
			FROM
				products
			WHERE
				ID = '.$prod_id.'

		';
	$obj->db_open($db_link, $DB_name, $sql);
	$return = array();
	if(!$obj->EOF())
	{
		$ID_ORG = $obj->db_data["ID_ORG"];
	}
    $obj->db_close();

	return $ID_ORG;

	db_closedb_conection($db_link);

}

function get_prod_marca_prom($prod_id = 0)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$obj = new DataObj;


	$Marca_id = 0;
	$sql = '
			SELECT
				ID_Marca
			FROM
				products
			WHERE
				ID = '.$prod_id.'

		';
	$obj->db_open($db_link, $DB_name, $sql);
	$return = array();
	if(!$obj->EOF())
	{
		$Marca_id = $obj->db_data["ID_Marca"];
	}
    $obj->db_close();

	return $Marca_id;

	db_closedb_conection($db_link);

}

function get_produtos($f_familia2 = '', $f_marca = '')
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$Nome,$ID,$ID_Fam, $txt_email14;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;	$obj2=new DataObj;	$obj3=new DataObj;

	if($f_familia2) $w .= ' AND (fp.fam_id4 = "'.$f_familia2.'" OR fp.fam_id3 = "'.$f_familia2.'" OR fp.fam_id2 = "'.$f_familia2.'" OR fp.fam_id1 = "'.$f_familia2.'")';
	if($f_marca) $w .= ' AND (fp.marca_id = "'.$f_marca.'")';


	$sql = '
			SELECT
				prod_id
			FROM
				filters_products fp
			WHERE
				filtro_active = 1
				'.$w.'
			GROUP BY
				prod_id
		';

	$obj->db_open($db_link, $DB_name, $sql);
	$return = array();
	if(!$obj->EOF())
	{
		while(!$obj->EOF())
		{
			$return[] = $obj->db_data["prod_id"];

			$obj->db_next();
		}
	}
    $obj->db_close();

	return $return;

	db_closedb_conection($db_link);

}

if(isset($_POST['prdi']) && $_POST['prdi']==1)
{
	$c = count($_POST); $keys = array_keys($_POST); $i = 0;
	while($i < $c)
	{
		if(substr_count($keys[$i], 'prodon_')) $prdon[] = $_POST[$keys[$i]];
		if(substr_count($keys[$i], 'prodoff_')) $prdoff[] = $_POST[$keys[$i]];
		$i++;
	}

	if(isset($prdon) && is_array($prdon) && !empty($prdon))
	{

		$tot = count($prdon);
		$b = 0;
		// $insert = 'INSERT INTO sales_promotions_products (promo_id, DESCONTO, PROD_ID, FAMILIAX, MARCAX) VALUES ';
		$insert = 'REPLACE INTO sales_promotions_products (promo_id, DESCONTO, PROD_ID, FAMILIAX, MARCAX, FLAG) VALUES ';
		for($a=0; $a<$tot; $a++)
		{
			// $sql = 'SELECT * FROM sales_promotions_products WHERE promo_id="'.$_POST['ID_PAI'].'" AND DESCONTO="'.$_POST['Desconto'].'" AND PROD_ID="'.$prdon[$a].'" AND FAMILIA=0 AND MARCA=0';
			// $obj->db_open($db_link, $DB_name, $sql);
			// if($obj->EOF())
			// {
				$upd = 'UPDATE sales_promotions_products SET FLAG=0 WHERE promo_id="'.$_POST['ID_PAI'].'" AND DESCONTO="'.$_POST['Desconto'].'" AND PROD_ID="'.$prdon[$a].'"';
				$res = Runquery($db_link, $DB_name, $upd);


				/* if(isset($_POST['famid']) && $_POST['famid'] > 0) $fam_id = $_POST['famid']; else  */$fam_id = get_prod_fam_prom($prdon[$a]);
				/* if(isset($_POST['marcaid']) && $_POST['marcaid'] > 0) $marcaid = $_POST['marcaid']; else  */$marca_id = get_prod_marca_prom($prdon[$a]);

				if($b > 0) $insert .= ', ("'.$_POST['ID_PAI'].'","'.$_POST['Desconto'].'","'.$prdon[$a].'", "'.$fam_id.'", "'.$marca_id.'", 1)';
				else $insert .= '("'.$_POST['ID_PAI'].'","'.$_POST['Desconto'].'","'.$prdon[$a].'", "'.$fam_id.'", "'.$marca_id.'", 1)';
				$b++;
			// }
			// $obj->db_close();
		}
		// echo 'xx'.$insert."\n";
		if($b > 0) $res = Runquery($db_link, $DB_name, $insert);

		$del = 'DELETE FROM sales_promotions_products WHERE FLAG=0 AND promo_id="'.$_POST['ID_PAI'].'" AND DESCONTO="'.$_POST['Desconto'].'"';
		$res = Runquery($db_link, $DB_name, $del);

		echo 1;

	}
	if(isset($prdoff) && is_array($prdoff) && !empty($prdoff))
	{
		$tot = count($prdoff);
		for($a=0; $a<$tot; $a++)
		{
			$delete = 'DELETE FROM sales_promotions_products WHERE promo_id="'.$_POST['ID_PAI'].'" AND DESCONTO="'.$_POST['Desconto'].'" AND PROD_ID="'.$prdoff[$a].'"';
			$res = Runquery($db_link, $DB_name, $delete);
		}
		echo 1;

	}
}
else
{
	if(isset($_POST['famid']) && $_POST['famid'] > 0 && isset($_POST['sel_marca']) && $_POST['marcaid'] > 0)
	{
		$prods  = get_produtos($_POST['famid'], $_POST['marcaid']);
	}
	elseif(isset($_POST['marcaid']) && $_POST['marcaid'] > 0)
	{
		$prods  = get_produtos('', $_POST['marcaid']);
	}
	elseif(isset($_POST['famid']) && $_POST['famid'] > 0)
	{
		$prods  = get_produtos($_POST['famid'], '');
	}

	$tot = count($prods);
	if($tot > 0 && isset($_POST['remove']) && $_POST['remove']=='')
	{
		// echo 'tot: '.$tot;
		$b = 0;
		// $insert = 'INSERT INTO sales_promotions_products (promo_id, DESCONTO, PROD_ID, FAMILIAX, MARCAX) VALUES ';


		// $upd = 'UPDATE sales_promotions_products SET FLAG=0 WHERE promo_id="'.$_POST['ID_PAI'].'" AND DESCONTO="'.$_POST['Desconto'].'"';
		// $res = Runquery($db_link, $DB_name, $upd);

		$insert = 'REPLACE INTO sales_promotions_products (promo_id, DESCONTO, PROD_ID, FAMILIAX, MARCAX, FLAG) VALUES ';
		for($a=0; $a<$tot; $a++)
		{


			/* if(isset($_POST['famid']) && $_POST['famid'] > 0) $fam_id = $_POST['famid']; else  */$fam_id = get_prod_fam_prom($prods[$a]);
			/* if(isset($_POST['marcaid']) && $_POST['marcaid'] > 0) $marcaid = $_POST['marcaid']; else */ $marca_id = get_prod_marca_prom($prods[$a]);

			if($b > 0) $insert .= ', ("'.$_POST['ID_PAI'].'","'.$_POST['Desconto'].'","'.$prods[$a].'", "'.$fam_id.'", "'.$marca_id.'", 1)';
			else $insert .= '("'.$_POST['ID_PAI'].'","'.$_POST['Desconto'].'","'.$prods[$a].'", "'.$fam_id.'", "'.$marca_id.'", 1)';
			$b++;
		}

		if($b > 0) $res = Runquery($db_link, $DB_name, $insert);

		// $del = 'DELETE FROM sales_promotions_products WHERE FLAG=0 AND promo_id="'.$_POST['ID_PAI'].'" AND DESCONTO="'.$_POST['Desconto'].'"';
		// $res = Runquery($db_link, $DB_name, $del);

		// echo $insert;

		echo 1;

	}
	elseif($tot > 0 && isset($_POST['remove']) && $_POST['remove']==1)
	{
		for($a=0; $a<$tot; $a++)
		{
			$delete = 'DELETE FROM sales_promotions_products WHERE promo_id = "'.$_POST['ID_PAI'].'" AND DESCONTO = "'.$_POST['Desconto'].'" AND PROD_ID = "'.$prods[$a].'"';
			$res = Runquery($db_link, $DB_name, $delete);
			$obj->db_close();
		}
		echo 1;

	}

}
 db_closedb_conection($db_link);


?>