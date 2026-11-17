<?
	$path="../../core/";
	require_once $path."settings.php";
	require_once $path."sessions_engine.php";
	require_once $path."db_engine_pdo.php";
	require_once $path."global.php";
	require_once $path."media.php";
	require_once $path."menus.php";
	require_once $path."seo.php";
	require_once $path."linguas.php";
	require_once $path."gd.php";
	require_once $path."cookies.php";
	require_once $path."email.php";
	require_once $path."forms.php";
	require_once "funcoes.inc.php";
	$imprimir=true;
	
	$Name="";$Name2="";$Tipo="";$Values="";$Default=""; 
	
	if(isset($_POST['id_list'])) $id_list=$_POST['id_list'];

	if($id_list) $code = $id_list;
	elseif($ID) $code[] = $ID;

	$line= "Nome;Email;Data; \n";
	foreach ($code as $id)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name; 
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass); 
		$sql="Select * FROM emails where ID=".$id;
		$dados=new DataObj;
		$hRes= $dados->db_open($db_link,$DB_name,$sql); 
		$start_linha=1;
		$obrigatorio=0;
		$filename = "listagem.csv";
		
		while(!$dados->EOF())
		{
			 $Email=$dados->db_data["Email"];
			 $Name=$dados->db_data["Nome"];
			 $Data=$dados->db_data["Data"];
			 $Data_modify=$dados->db_data["Data_modify"];
			 $line.=$Name.";".$Email.";".$Data."; \n";
		
			$dados->db_next();
		}
		$dados->db_close();
	}
	
	if($line)
	{
	  	header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
		header("Content-Disposition: attachment;filename=\"".$filename."\"");
		header("Cache-Control: max-age=0");
	 	echo iconv("UTF-8", "Windows-1252",$line);
	}
	
?>