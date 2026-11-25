<? 
	include "settings.inc.php";
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;
	if(isset($_GET['nr']))
	{
		$rs->db_open($db_link,$DB_name,'SELECT * FROM rmas WHERE NSerie="'.$_GET['nr'].'" AND Estado=6');
		if(!$rs->EOF())echo '<a href="pagina.php?ID='.$rs->db_data['ID'].'&d='.uniqid("").'" target="_blank"><img src="alerta.png" style="float:left;margin-left:5px" title="Última Reparação em: '.$rs->db_data['DataUpdate'].'"></a>';
	}
?>