<? 
	include "settings.inc.php";

	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;

	$obj_id=$_POST['obj_id'];

	$sql="DELETE FROM `products_objs_lang` WHERE  ID_PAI='".$obj_id."'";
	$res=Runquery($db_link,$DB_name,$sql);
	if($res){
		$sql="DELETE FROM `products_objs` WHERE obj_type='products' AND obj_id='".$obj_id."'";
		$res=Runquery($db_link,$DB_name,$sql);
		if($res){
			echo 0;
		}else{
			echo -1;
		}
	}else{
		echo -1;
	}	
?>