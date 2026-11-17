<?php
if (isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__) exit();


function squareblocks(){
global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
$table = 'oportunidades_carreira';
$rs = new Dataobj;
$db_link=db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);

$sql = "SELECT data, titulo, ID, visivel FROM ".$table." ORDER by ordem";
$rs->db_open($db_link,$DB_name,$sql);
	$data = array();
	echo '<ul class="oportunidadeslist">';
	while(!$rs->EOF()){
		if($rs->db_data['visivel']){
			$data = array();
			 $data['ID'] = $rs->db_data['ID'];
			 $data['data'] = $rs->db_data['data'];
			 $data['titulo'] = $rs->db_data['titulo'];
			 echo '<li>';
			 echo squareblock($data);
			 echo '</li>';
		}


		$rs->db_next();
	}
	echo '</ul>';
	$rs->db_close();
}


function squareblock($data=''){
	$str = '';
	$str .= '<div class="occontent">';
		$str .= '<div class="ocheader">';
		$str .= $data['data'];
		$str .= '</div>';
		$str .= '<div class="ocbody">';
		$str .= $data['titulo'];
		$str .= '</div>';
		$str .= '<div class="ocfooter">';
			$str .= '<a href="">';
				$str .= '» Ver oportunidades';
			$str .= '</a>';
		$str .= '</div>';
	$str .= '</div>';
	return $str;
}



function oportunidadepage(){
$id = $_GET['prod_id'];



}


?>