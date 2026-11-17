<? 
	include "settings.inc.php";
	AlteraCampo($_GET['op'],$_GET['ordem'],$_GET['value'],"form_options",$_GET['email'],$_GET['associate_page']);
	echo '<script type="text/javascript">open("form_fields.php?ID='.$ID.'&ID_ORG2='.$ID_ORG2.'&ID_ORG='.$ID_ORG.'","_self");</script>';
	
	function AlteraCampo($id,$ordem,$value,$Tabela="form_options",$email,$associate_page='')
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name,$Nome;
		$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$value = str2SQLstr($value);
		$sql = "Update $Tabela set Value=$value,Ordem='$ordem',Email='$email', Associate_page='".$associate_page."' WHERE ID=$id";
		$res = Runquery($db_link,$DB_name,$sql);
		if(!$res)  echo db_error($db_link);
		db_closedb_conection($db_link);
		return($res);
	}
?>