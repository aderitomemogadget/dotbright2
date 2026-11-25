<?	include "settings.inc.php";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$m_url="list.php";
	
	print_r($_POST);
	
	
	if(isset($_POST['flag']) && $_POST['flag'] == 2)
	{
		foreach($_POST as $varName=> $value) 
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chks_") > 0)
			{
				$sql="UPDATE products_families SET Ordem=$dv WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql."<br>";
				
				$var_nome="name_".$str_id;
				$nome=$$var_nome;
				if($nome)
				{
					$sql="UPDATE products_families_lang SET Cabecalho='$nome' WHERE ID_PAI=$str_id and Lang='$admin_lang'";
					$res=Runquery($db_link,$DB_name,$sql);
					if(!$res)echo db_error($db_link).$sql."<br>";
				}
			}
		}
	}
	elseif(isset($_POST['flag']) && $_POST['flag'] == 3)
	{
		foreach($_POST as $varName=> $value) 
		{
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0 && $_POST['chk_'.$str_id] == $str_id)
			{
				$sql="UPDATE products_families SET Visivel=1 WHERE ID=$str_id";
				// echo "<span style='color:#FFF'>".$sql."<br></span>";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql."<br>";
			}
			else $res = 1;
		}
	}
	elseif(isset($_POST['flag']) && $_POST['flag'] == 4)
	{
		foreach($_POST as $varName=> $value) 
		{
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0 && $_POST['chk_'.$str_id] == $str_id)
			{
				$sql="UPDATE products_families SET Visivel=0 WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql."<br>";
			}
		}
	}
	if($res)	show_box_OK(2,$m_url);
	else		show_box_ERROR(2,$m_url);
	db_closedb_conection($db_link);
?>