<?php
	$path=dirname( __FILE__ ).'/../core/';	
	include $path.'core_files.php';	

	$id = $_GET['id']==''?1:$_GET['id'];

	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs2 = new DataObj;
	$sql = "SELECT Template FROM pages WHERE ID=".$id;
	$rs2->db_open($db_link, $DB_name, $sql);
	if(!$rs2->EOF())
	{
		$id_pai = $rs2->db_data['Template'];
	}
	$rs2->db_close();


	$rs = new DataObj;
	$sql = "SELECT * FROM templates_imgs WHERE ID_PAI=".$id_pai." ORDER BY rand() LIMIT 1";
	$rs->db_open($db_link, $DB_name, $sql);
	while(!$rs->EOF())
	{
		
		$imge_ext = 'files/templates/'.$id_pai.'_'.$rs->db_data['ID'].'e.'.$rs->db_data['imge_ext'];
		$imgd_ext = 'files/templates/'.$id_pai.'_'.$rs->db_data['ID'].'d.'.$rs->db_data['imgd_ext'];


		list($width,$height) = getimagesize($GLOBALS['site_shell_url'].'files/templates/'.$id_pai.'_'.$rs->db_data['ID'].'e.'.$rs->db_data['imge_ext']);
		$heighte = $height;
		$widthe = $width;
		list($width,$height) = getimagesize($GLOBALS['site_shell_url'].'files/templates/'.$id_pai.'_'.$rs->db_data['ID'].'d.'.$rs->db_data['imgd_ext']);
		$heightd = $height;

		$link_imge_ext =  seo($rs->db_data['link_imge_ext']);
		$link_imgd_ext = seo($rs->db_data['link_imgd_ext']);

		$bgcolore = $rs->db_data['bgcolore'];
		$bgcolord = $rs->db_data['bgcolord'];

		$rs->db_next();
	}
	$rs->db_close();
$output = array();

$content_width = $GLOBALS['content_width'];

$output['dive'] = '<div  style="background:'.$bgcolore.';width:calc((100% - '.$content_width.'px)/2);top: 0;height:100vh;position: fixed;left: 0;text-align:right;">';
$output['dive'] .= '<a href="'.$link_imge_ext.'">';
$output['dive'] .= '<img src="'.$imge_ext .'" style="position:relative;top:calc(50% - '.($heighte/2).'px);margin-left:-'.($widthe).'px">';

$output['dive'] .= '</a>';

$output['dive'] .= '</div>';


$output['divd'] = '<div  style="background:'.$bgcolord.';width:calc((100% - '.$content_width.'px)/2);top: 0;height:100vh;position: fixed;right: 0;text-align:left;">';
$output['divd'] .= '<a href="'.$link_imgd_ext.'">';
$output['divd'] .= '<img src="'.$imgd_ext .'" style="position:relative;top:calc(50% - '.($heightd/2).'px)">';
$output['divd'] .= '</a>';
$output['divd'] .= '</div>';




 

echo json_encode($output);
?>