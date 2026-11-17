<?php
// $_GET['log_errors']=1;
require_once "settings.inc.php";
ini_set('meemory_limit', '1000M');
ini_set('post_max_size', '1000M');
ini_set('upload_max_filesize', '1000M');

global $DB_host, $DB_user, $DB_pass, $DB_name;
$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
$obj=new DataObj;


function get_produtos($f_familia2 = '', $f_marca = '')
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$Nome,$ID,$ID_Fam, $txt_email14;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;	$obj2=new DataObj;	$obj3=new DataObj;

	if($f_familia2) $w .= ' AND (fp.fam_id3 = "'.$f_familia2.'" OR fp.fam_id2 = "'.$f_familia2.'" OR fp.fam_id1 = "'.$f_familia2.'")';
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
$rem = 0;
// print_r($_POST);
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
		$insert = 'INSERT INTO extensao_garantia_products (promo_id, DESCONTO, PROD_ID, FAMILIAX, MARCAX) VALUES ';
		for($a=0; $a<$tot; $a++)
		{

			$sql = 'SELECT * FROM extensao_garantia_products WHERE promo_id="'.$_POST['ID_PAI'].'" AND DESCONTO="'.$_POST['Desconto'].'" AND PROD_ID="'.$prdon[$a].'" AND FAMILIAX=0 AND MARCAX=0';
			// echo $sql.'<br>';
			$obj->db_open($db_link, $DB_name, $sql);
			if($obj->EOF())
			{
				if($b > 0) $insert .= ', ("'.$_POST['ID_PAI'].'","'.$_POST['Desconto'].'","'.$prdon[$a].'", "'.$_POST['famid'].'", "'.$_POST['marcaid'].'")';
				else $insert .= '("'.$_POST['ID_PAI'].'","'.$_POST['Desconto'].'","'.$prdon[$a].'", "'.$_POST['famid'].'", "'.$_POST['marcaid'].'")';
				$b++;
			}
			$obj->db_close();
		}
		// echo $insert."\n";
		if($b > 0) $res = Runquery($db_link, $DB_name, $insert);
		echo 1;
		// exec("php ".$GLOBALS['site_shell_url']."admin/sales_promotions/cache/do_promos.php > /dev/null &");
	}
	if(isset($prdoff) && is_array($prdoff) && !empty($prdoff))
	{
		$tot = count($prdoff);
		for($a=0; $a<$tot; $a++)
		{
			$delete = 'DELETE FROM extensao_garantia_products WHERE promo_id="'.$_POST['ID_PAI'].'" AND DESCONTO="'.$_POST['Desconto'].'" AND PROD_ID="'.$prdoff[$a].'"';
			$res = Runquery($db_link, $DB_name, $delete);
		}
		echo 1;
		// exec("php ".$GLOBALS['site_shell_url']."admin/sales_promotions/cache/do_promos.php > /dev/null &");
	}
}
else
{
	if(isset($_POST['famid']) && $_POST['famid'] > 0 && isset($_POST['marcaid']) && $_POST['marcaid'] > 0)
	{
		$prods  = get_produtos($_POST['famid'], $_POST['marcaid']);
		$rem = 1;
	}
	elseif(isset($_POST['marcaid']) && $_POST['marcaid'] > 0)
	{
		$prods  = get_produtos('', $_POST['marcaid']);
		$rem = 2;
	}
	elseif(isset($_POST['famid']) && $_POST['famid'] > 0)
	{
		$prods  = get_produtos($_POST['famid'], '');
		$rem = 3;
	}

	$tot = count($prods);
	if($tot > 0 && isset($_POST['remove']) && $_POST['remove']=='')
	{
		// echo 'tot: '.$tot;
		$b = 0;
		$insert = 'INSERT INTO extensao_garantia_products (promo_id, DESCONTO, PROD_ID, FAMILIAX, MARCAX) VALUES ';
		for($a=0; $a<$tot; $a++)
		{

			$sql = 'SELECT * FROM extensao_garantia_products WHERE promo_id="'.$_POST['ID_PAI'].'" AND DESCONTO="'.$_POST['Desconto'].'" AND PROD_ID="'.$prods[$a].'" AND FAMILIA=0 AND MARCA=0';
			$obj->db_open($db_link, $DB_name, $sql);
			if($obj->EOF())
			{
				if($b > 0) $insert .= ', ("'.$_POST['ID_PAI'].'","'.$_POST['Desconto'].'","'.$prods[$a].'", "'.$_POST['famid'].'", "'.$_POST['marcaid'].'")';
				else $insert .= '("'.$_POST['ID_PAI'].'","'.$_POST['Desconto'].'","'.$prods[$a].'", "'.$_POST['famid'].'", "'.$_POST['marcaid'].'")';
				$b++;
			}
			$obj->db_close();
		}
		// echo $insert."\n";
		if($b > 0) $res = Runquery($db_link, $DB_name, $insert);
		echo 1;
		// exec("php ".$GLOBALS['site_shell_url']."admin/sales_promotions/cache/do_promos.php > /dev/null &");
	}
	elseif($tot > 0 && isset($_POST['remove']) && $_POST['remove']==1)
	{
		for($a=0; $a<$tot; $a++)
		{
			if($rem == 1 && $_POST['marcaid'] > 0) $delete = 'DELETE FROM extensao_garantia_products WHERE promo_id = "'.$_POST['ID_PAI'].'" AND PROD_ID = "'.$prods[$a].'" AND FAMILIAX="'.$_POST['famid'].'" AND MARCAX="'.$_POST['marcaid'].'"';
			elseif($rem == 2) $delete = 'DELETE FROM extensao_garantia_products WHERE promo_id = "'.$_POST['ID_PAI'].'" AND PROD_ID = "'.$prods[$a].'" AND FAMILIAX=0 AND MARCAX="'.$_POST['marcaid'].'"';
			elseif($rem == 3) $delete = 'DELETE FROM extensao_garantia_products WHERE promo_id = "'.$_POST['ID_PAI'].'" AND PROD_ID = "'.$prods[$a].'" AND FAMILIAX="'.$_POST['famid'].'" AND MARCAX=0';
			// echo $delete.'<br>';
			$res = Runquery($db_link, $DB_name, $delete);
			$obj->db_close();
		}
		echo 1;
		// exec("php ".$GLOBALS['site_shell_url']."admin/sales_promotions/cache/do_promos.php > /dev/null &");
	}

}
 db_closedb_conection($db_link);