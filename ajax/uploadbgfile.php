<?php
	$path=dirname( __FILE__ ).'/../core/';	
	include $path.'core_files.php';	

$filee = $_FILES['filee'];
$filed = $_FILES['filed'];

$url_linke = $_POST['url_linke'];
$url_linkd = $_POST['url_linkd'];

$colorbge = $_POST['colorbge'];
$colorbgd = $_POST['colorbgd'];

$ID_PAI = $_POST['ID_PAI'];

$table = 'templates_imgs';
/**/
$mpath = $GLOBALS['site_shell_url'].'files/templates/'; // upload directory

$db_link=db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);

if($_FILES['filee']['size']>0 && $_FILES['filed']['size']>0)
{
		$filee = $_FILES['filee']['name'];
		$tmpe = $_FILES['filee']['tmp_name'];
		$exte = strtolower(pathinfo($filee, PATHINFO_EXTENSION));

		$filed = $_FILES['filed']['name'];
		$tmpd = $_FILES['filed']['tmp_name'];
		$extd = strtolower(pathinfo($filed, PATHINFO_EXTENSION));

		$sql = "INSERT INTO ".$table." (ID_PAI,imge_ext,imgd_ext,link_imge_ext,link_imgd_ext,bgcolore,bgcolord,activa) VALUES ('".$ID_PAI."','".$exte."','".$extd."','".$url_linke."','".$url_linkd."','".$colorbge."','".$colorbgd."','1')";

		
		$res = Runquery($db_link,DB_NAME,$sql);
		if($res){
			$new_id=db_get_last_insert_id($db_link);
			$pathe = $mpath.$ID_PAI.'_'.$new_id.'e.'.$exte;
			copy($tmpe,$pathe);
			$pathd = $mpath.$ID_PAI.'_'.$new_id.'d.'.$extd;
			copy($tmpd,$pathd);
		}
}


$str = templateimages($ID_PAI);
/**
$mpath = $GLOBALS['site_http_url'].'files/templates/';
//image format
//9_1e.jpg
//$ID_PAI_$ID(side).(ext)

$rs = new Dataobj;
	$db_link=db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
	
	$sql = "SELECT ID FROM ".$table." WHERE ID_PAI=".$ID_PAI;
	$rs->db_open($db_link,$DB_name,$sql);
	$str = '';
	$str .= '<div class="template-images">';
	while(!$rs->EOF()){


		$rs->db_data['ID_PAI'];

		$str = '<img src ="'.$mpath.$ID_PAI.'_'.$rs->db_data['ID'].'e.'.$rs->db_data['imge_ext'].'">';
		$str = '<img src ="'.$mpath.$ID_PAI.'_'.$rs->db_data['ID'].'e.'.$rs->db_data['imge_ext'].'">';

		$rs->db_data['imgd_ext'];
		
		$rs->db_next();
	}
	$rs->db_close();

	$str .= '</div>';


/**/
	echo json_encode($str);
?>