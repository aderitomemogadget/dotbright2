<?php

function gera_lista_produtosx($id_org=0,$space=1, $line='')
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$ID;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj = new DataObj;

	$sql = "
			SELECT pf.ID, pf.Ref, pfl.Cabecalho, spp.MARCAX, spp.FAMILIAX
			FROM products pf
			INNER JOIN products_sync_web_prods spp ON spp.PROD_ID = pf.ID
			INNER JOIN products_lang pfl ON pfl.ID_PAI = pf.ID
			WHERE
				spp.MARCA=0
				AND
				spp.FAMILIA=0
				AND
				spp.promo_id='".$id_org."'

			GROUP BY
				pf.ID
			ORDER BY
				 spp.MARCAX ASC, spp.FAMILIAX desc
			";

	$obj->db_open($db_link, $DB_name, $sql);
	$ln = '';
    while(!$obj->EOF())
	{

		$return .= '<div style="clear:both"></div>';

		if($ln!=$obj->db_data["MARCAX"].'-'.$obj->db_data["FAMILIAX"])
		{

			$return .= '<b>'.($obj->db_data["MARCAX"] > 0 ? getbrandsx($obj->db_data["MARCAX"]).', ' : ''). rtrim(show_breadcrums_recursivo_on($obj->db_data["FAMILIAX"]),'-').'</b> - <a style="color:#1d7fd3;text-decoration:underline;font-size:12px" href="avancada.php?ID_PAI='.$_GET['ID_PAI'].'&Desconto='.$_GET['Desconto'].'&campo=&tipoorder=DESC&pesq_id=&pesq_ref=&pesq_desig=&sel_marca='.($obj->db_data["MARCAX"] > 0 ? $obj->db_data["MARCAX"] : '').'&m_familia='.$obj->db_data["FAMILIAX"].'">pesquisar</a>';
			$ln = $obj->db_data["MARCAX"].'-'.$obj->db_data["FAMILIAX"];
		}

		$return .= '<div style="clear:both"></div>

					<label class="label_check2" style="padding-left:20px;padding-top:1px;font-size:11px;margin-left:'.$space.'px" for="fam_'.$obj->db_data["ID"].'_'.$line.'">
					<div style="float:left;width:13px">-</div>';

		$line2 = str_replace('.','_',$line);

		$return .= '<div style="float:left;width:300px;padding-top:5px;padding-bottom:5px">'.htmlentities_memo(stripslashes($obj->db_data["Ref"])).'-'.htmlentities_memo(stripslashes($obj->db_data["Cabecalho"])).'<div style="clear:both"></div></div>
		<div style="clear:both"></div></label><div style="clear:both"></div>';
// echo $obj->db_data["Cabecalho"].'<br />';

	    // $return .= gera_lista_familias($obj->db_data["ID"], ($space+10));
        $obj->db_next();
    }
    $obj->db_close();


	return $return;

}

function getbrandsx($id=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;
	$sql = 'SELECT Nome FROM brands_lang WHERE ID_PAI="'.$id.'" AND Lang="'.$GLOBALS['admin_lang'].'"';
	$rs->db_open($db_link, $DB_name, $sql);
	if(!$rs->EOF())
	{
		return $rs->db_data['Nome'];
	}
	$rs->db_close();
	return;
}
function getfamiliesx($id=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;
	$sql = 'SELECT Cabecalho FROM products_families_lang WHERE ID_PAI="'.$id.'" AND Lang="'.$GLOBALS['admin_lang'].'"';
	$rs->db_open($db_link, $DB_name, $sql);
	if(!$rs->EOF())
	{
		return ' | '.$rs->db_data['Cabecalho'];
	}
	$rs->db_close();
	return;
}