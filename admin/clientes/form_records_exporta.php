<? 
	//$path="../../../core/";
	$path="../../core/";
	require_once $path."sessions_engine.php";
	require_once $path."settings.php";
	require_once $path."db_engine.php";
	require_once $path."global.php";
	require_once $path."media.php";
	require_once $path."menus.php";
	require_once $path."linguas.php";
	require_once $path."gd.php";
	require_once $path."cookies.php";
	require_once $path."email.php";
	require_once $path."forms.php";

	//include $path.'admin_globals.php';
	include "funcoes.inc.php";
	$imprimir=true;
	
	$Name="";$Name2="";$Tipo="";$Values="";$Default=""; // $id_list=0;$ID = "";$ID_ORG = "";
	
	
	if($ID_From!=0)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name; 
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass); 
		$dados=new DataObj;
		$sql="Select * from form_records where ID_From=$ID_From"; 
		$hRes= $dados->db_open($db_link,$DB_name,$sql); 
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
		
		$sql1="Select * from forms where ID='$ID_ORG'"; // ERRO??????
		$hRes= $dados2->db_open($db_link,$DB_name,$sql1); 
		if(!$dados2->EOF()) $forms=$dados2->db_data['Name'];
		$dados2->db_close();
		$sql="Select * FROM form_records where ID=".$ID; 
		$hRes= $dados->db_open($db_link,$DB_name,$sql); 
		if(!$dados->EOF())
		{
			$n_cartao=$dados->db_data["n_cartao"];
			$cod_seg=$dados->db_data["cod_seg"];
			$data_exp=$dados->db_data["data_exp"];
			$data_exp2=$dados->db_data["data_exp2"];
	
			$sql="Select * FROM form_records where ID=".$dados->db_data["ID_From"]; 
			$dados2=new DataObj;
			$hRes= $dados2->db_open($db_link,$DB_name,$sql); 
			if(!$dados2->EOF())
			{	
				echo '<input type=hidden name=volta2 value='.$dados2->db_data["ID_ORG"].'>';
				$dados2->db_close();
			}
			$dados->db_close();
		}
	}
	
	if($id_list)
	{
		$code = $id_list;
	}
	elseif($ID) $code[] = $ID;
	//$line= "codigo;nome;Cartao_num;Cartao_tipo;BI;Email;Phone;Data registo;Hora; \n";
	//$line= "codigo;nome;Email; \n";
	$line= "codigo;nome;username;nif; \n";
	foreach ($code as $id)
	{
		
		if($line_feed_validator != $id)
		{
			$linea = substr_replace($linea , "", -1);
			$linea .= "\n";
		}
		global $DB_host,$DB_user,$DB_pass,$DB_name; 
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass); 
		//$sql="Select * FROM form_fields where ID_ORG=".$ID_ORG. " and Tipo!=33 order by Ordem";  //todos os campos menos o campo de botao
		$sql="Select * FROM clientes where ID=".$id;
		$dados=new DataObj;
		if($ID!=0 || $ID_From!=0) $dados2=new DataObj; 
		$hRes= $dados->db_open($db_link,$DB_name,$sql); 
		$start_linha=1;
		$obrigatorio=0;
		$filename = "listagem.csv";
		
		while(!$dados->EOF())
		{
			 $cod=$dados->db_data['ID']; 
			 $Name=$dados->db_data["Nome"];
			 $user=$dados->db_data['Username']; 
			 $nif=$dados->db_data["Nif"];
			 /*$Cartao_num=$dados->db_data["Cartao_num"];
			 $Cartao_tipo=$dados->db_data["Cartao_tipo"]; 
			 $BI=$dados->db_data["BI"];
			 //$Email=$dados->db_data["Email"];
			 $Email=$dados->db_data["Username"];
			 $Phone=$dados->db_data["Phone"];
			 $Data=$dados->db_data["Data"];
			 $Data_modify=$dados->db_data["Data_modify"];
			 $IP=$dados->db_data["IP"];
			 $Hora=$dados->db_data["Hora"];
			 */
				
			 //$line .= $cod.";".$Name.";".$Cartao_num.";".$Cartao_tipo.";".$BI.";".$Email.";".$Phone.";".$Data.";".$Data_modify.";".$IP.";".$Hora."; \n";
			 //$line .= $cod.";".$Name.";".$Cartao_num.";".$Cartao_tipo.";".$BI.";".$Email.";".$Phone.";".$Data.";".$Hora."; \n";
			 $line .= $cod.";".$Name.";".$user.";".$nif."; \n";
		
			$dados->db_next();
		}
		$dados->db_close();
	}
	/*if(!$ID) 
	{
		$data=new DataObj;
		$sql = "SELECT Name FROM forms WHERE ID = $ID_ORG"; 
		$hRes = $data->db_open($db_link,$DB_name,$sql); 
		$forms = $data->db_data['Name']; 
		$data->db_close();
		$filename = "listagem_".$forms.".csv";
		$sql="SELECT Name FROM form_fields WHERE ID_ORG=".$ID_ORG. " ORDER BY Ordem"; 
		$hRes = $data->db_open($db_link,$DB_name,$sql); 
		while(!$data->EOF())
		{	
			if(!$fields) $fields = $data->db_data['Name'];
			else $fields .= ";" . $data->db_data['Name'];	
			$data->db_next();
		}
		$data->db_close();
		$line .= $fields . "\n" . $linea;
	}
	else
	{
		echo 2;
		$filename = $forms."_".$ID.".csv";
		$line .= $linea;
	}*/
	
	if($line)
	{
	  header("Content-type: application/vnd.ms-excel");
	  header("Content-Disposition: attachment; filename=".$filename);
//	  header("Content-Type: text/html; charset=utf-8");
	  header("Content-type: text/html; charset=utf-8");
	  header("Pragma: no-cache");
	  header("Expires: 0");
//	  echo $line;
	  echo iconv( "UTF-8", "Windows-1252",$line);
	}
	
	function show_data($m_data,$cod)
	{
		if($m_data) $D=str2date($m_data);
	
		echo "<select name=\"dia".$cod."\">";
		if($D["mday"] < 1)echo "<option value=\"\" selected>---</option>\n";
		for($i=1;$i<32;$i++)
		{		
			echo "                      <option value=\"";
			if($i<10) echo "0";
			echo "$i\"";
			if($D["mday"]==$i)	echo " selected";
			echo">";
			if($i<10) echo "0";echo "$i</option>\n";
		}
		echo "                    </select>\n";
		echo ".\n";
		echo "                    <select name=\"mes".$cod."\">\n";
		if($D["mon"] < 1)echo "<option value=\"\" selected>---</option>\n";
		for($i=1;$i<13;$i++)
		{		
			echo "                      <option value=\"";
			if($i<10) echo "0";
			echo "$i\"";
			if($D["mon"]==$i)
				echo " selected";
			echo">";
			if($i<10) echo "0";
			echo "$i</option>\n";
		}
		echo "                    </select>\n";
		echo ".\n";
		echo "                    <select name=\"ano".$cod."\">";
		if($D["year"] < 1)echo "<option value=\"\" selected>---</option>\n";
		for($i=1900;$i<(date("Y")+5);$i++)
		{		
			echo "                      <option value=\"";
			if($i<10) echo "0";
			echo "$i\"";
			if($D["year"]==$i)
			echo " selected";
			echo ">$i</option>\n";
		}
		echo "                    </select>\n";
	}
?>