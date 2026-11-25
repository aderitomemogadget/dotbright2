<? 
	$path="../../core/";
	require_once $path."sessions_engine.php";
	require_once $path."settings.php";
	require_once $path."db_engine_pdo.php";
	require_once $path."global.php";
	require_once $path."media.php";
	require_once $path."menus.php";
	require_once $path."linguas.php";
	require_once $path."gd.php";
	require_once $path."cookies.php";
	require_once $path."email.php";
	require_once $path."forms.php";
	include "funcoes.inc.php";
	$imprimir=true;
	$Name='';$Name2='';$Tipo='';$Values='';$Default='';
	if($ID_From!=0)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name; 
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass); 
		$dados=new DataObj;
		$hRes= $dados->db_open($db_link,$DB_name,"Select * from form_records where ID_From=$ID_From");
		if(!$dados->EOF())
		{
			$ID=$dados->db_data['ID'];
			$task_button="Alterar";$task_form='altera';
			$dados->db_close();
		}
	} 
	$Nome="form_records_fields";
	if($ID_From!=0) echo '<input type=hidden name=ID_From value='.$ID_From.'>';
	if($ID!=0)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name; 
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass); 
		$dados=new DataObj;
		$dados2=new DataObj;

		$hRes= $dados2->db_open($db_link,$DB_name,"Select * from forms where ID='$ID_ORG'"); 
		if(!$dados2->EOF()) $forms=$dados2->db_data['Name'];
		$dados2->db_close();
		$hRes= $dados->db_open($db_link,$DB_name,"Select * FROM form_records where ID=".$ID); 
		if(!$dados->EOF())
		{
			$n_cartao=$dados->db_data["n_cartao"];
			$cod_seg=$dados->db_data["cod_seg"];
			$data_exp=$dados->db_data["data_exp"];
			$data_exp2=$dados->db_data["data_exp2"];
			$dados2=new DataObj;
			$hRes= $dados2->db_open($db_link,$DB_name,"Select * FROM form_records where ID=".$dados->db_data["ID_From"]);
			if(!$dados2->EOF())echo '<input type=hidden name=volta2 value='.$dados2->db_data["ID_ORG"].'>';
			$dados2->db_close();
			$dados->db_close();
		}
	}
	if($id_list)
	{
		$code = $id_list;
	}
	elseif($ID) $code[]=$ID;
	$line= "codigo;nome;username;nif; \n";

	global $DB_host,$DB_user,$DB_pass,$DB_name; 
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$dados=new DataObj;
	
	foreach ($code as $id)
	{
		if($line_feed_validator != $id)
		{
			$linea = substr_replace($linea , "", -1);
			$linea .= "\n";
		}
		if($ID!=0 || $ID_From!=0) $dados2=new DataObj; 
		$hRes= $dados->db_open($db_link,$DB_name,"Select * FROM clientes where ID=".$id); 
		$start_linha=1;
		$obrigatorio=0;
		$filename = "listagem.csv";
		while(!$dados->EOF())
		{
			$cod=$dados->db_data['ID']; 
			$Name=$dados->db_data["Nome"];
			$user=$dados->db_data['Username']; 
			$nif=$dados->db_data["Nif"];
			$line .= $cod.";".$Name.";".$user.";".$nif."; \n";
			$dados->db_next();
		}
		$dados->db_close();
	}
	if($line)
	{
	  header("Content-type: application/vnd.ms-excel");
	  header("Content-Disposition: attachment; filename=".$filename);
	  header("Content-type: text/html; charset=utf-8");
	  header("Pragma: no-cache");
	  header("Expires: 0");
	  echo iconv( "UTF-8", "Windows-1252",$line);
	}
	function show_data($m_data,$cod)
	{
		if($m_data) $D=str2date($m_data);
		echo "<select name=\"dia".$cod."\">";
		if($D["mday"] < 1)echo "<option value=\"\" selected>---</option>\n";
		for($i=1;$i<32;$i++)
		{		
			echo "<option value=\"";
			if($i<10) echo "0";
			echo "$i\"";
			if($D["mday"]==$i)	echo " selected";
			echo">";
			if($i<10) echo "0";echo "$i</option>\n";
		}
		echo "</select>\n";
		echo ".\n";
		echo "<select name=\"mes".$cod."\">\n";
		if($D["mon"] < 1)echo "<option value=\"\" selected>---</option>\n";
		for($i=1;$i<13;$i++)
		{		
			echo "<option value=\"";
			if($i<10) echo "0";
			echo "$i\"";
			if($D["mon"]==$i)
				echo " selected";
			echo">";
			if($i<10) echo "0";
			echo "$i</option>\n";
		}
		echo "</select>\n";
		echo ".\n";
		echo "<select name=\"ano".$cod."\">";
		if($D["year"] < 1)echo "<option value=\"\" selected>---</option>\n";
		for($i=1900;$i<(date("Y")+5);$i++)
		{		
			echo "<option value=\"";
			if($i<10) echo "0";
			echo "$i\"";
			if($D["year"]==$i)
			echo " selected";
			echo ">$i</option>\n";
		}
		echo "</select>\n";
	}
?>