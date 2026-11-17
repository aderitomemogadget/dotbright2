<? 
	$path="../../../core/";
	require_once $path."sessions_engine.php";
	require_once $path."settings.php";
	require_once $path."db_engine.php";
	require_once $path."global.php";
	require_once $path."media.php";
	require_once $path."menus.php";
	require_once $path."seo_v2.php";
	require_once $path."linguas.php";
	require_once $path."gd.php";
	require_once $path."cookies.php";
	require_once $path."email.php";
	require_once $path."forms.php";

	include $path."admin_globals.php";
	include "funcoes.inc.php";
	$imprimir=true;
	

	$Name="";$Name2="";$Tipo="";$Values="";$Default=""; // $id_list = 0;$ID = "";$ID_ORG = "";
	if($ID_From!=0)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name; 
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass); 
		$dados=new DataObj;
		$sql="Select * from form_records where ID_From=$ID_From"; 
		$hRes= $dados->db_open($db_link,$DB_name,$sql); 
		if(!$dados->EOF())
		{
			$ID=$dados->db_data["ID"];
			$task_button="Alterar";
			$task_form="altera";
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

echo		$sql1="Select * from forms where ID='$ID_ORG'"; // ERRO??????
		$hRes= $dados2->db_open($db_link,$DB_name,$sql1); 
		if(!$dados2->EOF()) $forms=$dados2->db_data["Name"];
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
	foreach ($code as $id)
	{
		if($line_feed_validator != $id)
		{
			$linea = substr_replace($linea , "", -1);
			$linea .= "\n";
		}
		global $DB_host,$DB_user,$DB_pass,$DB_name; 
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass); 
		$sql="Select * FROM form_fields where ID_ORG=".$ID_ORG. " and Tipo!=33 order by Ordem";  //todos os campos menos o campo de botao
		$dados=new DataObj;
		if($ID!=0 || $ID_From!=0) $dados2=new DataObj; 
		$hRes= $dados->db_open($db_link,$DB_name,$sql); 
		$start_linha=1;
		$obrigatorio=0;
	
		while(!$dados->EOF())
		{
			 $cod=$dados->db_data["ID"]; 
			 $Name=$dados->db_data["Name"];
			 $Name2=$dados->db_data["Name2"];
			 $tipo=$dados->db_data["Tipo"]; 
			 $Length=$dados->db_data["Length"];
			 $Length2=$dados->db_data["Length2"];
			 $Align=$dados->db_data["Align"];
			 $Obrigatorio=$dados->db_data["Obrigatorio"];
			 $id_form=$dados->db_data["id_form"];
			 $id_campo=$dados->db_data["id_campo"];
			 
			 if(!$ID || $ID!=0)
			 {
				$sql="Select * FROM form_records_fields where ID_ORG=".$id. " and Name=$cod"; 
				$dados2=new DataObj;
				$hRes= $dados2->db_open($db_link,$DB_name,$sql); 
				$Valor=$dados2->db_data["Valor"];
				$line_feed_validator=$dados2->db_data["ID_ORG"];
				$id_img=$dados2->db_data["ID"];
				$dados2->db_close();
			 }
			 else
			 {
				 if($ID_From!=0)
				 {
					$sql="Select Valor,Name,form_records_fields.ID as ID FROM form_records_fields inner join form_records on form_records.ID=form_records_fields.ID_ORG where ID_From=".$ID_From. " and Name=$cod"; 
					$hRes= $dados2->db_open($db_link,$DB_name,$sql); 
					if(!$dados2->EOF())
					{
						$Valor=$dados2->db_data["Valor"];
						$dados2->db_close();
					}
					else $Valor="";
				 } 
			 }
			if(!$id_campo) $id_campo=0;
			if($ID != 0)
			{		
				if($start_linha)
				{
				  if($tipo!=15 && $tipo!=9 && $tipo!=0)
				  {
				  	if($Name2 && !$Name) $linea .= stripslashes($Name2).":;";
					else $linea .= stripslashes($Name).":;";
				   }
				  $start_linha=0;
				}
				elseif($tipo!=15 && $tipo!=9 && $tipo!=0) 
				{
					if($Name2 && !$Name) $linea .= stripslashes($Name2).";";
					else $linea .= stripslashes($Name).";";
				}
			}
			else	
			{
				if($Length2) $largura=$Length2."px";
				else $largura=($Length*100/254)."%";
			}			
			if($largura < 30) $largura=30;
			/****************************** INICIO LER DADOS DE CAMPOS PAI **************************************************/
			if($dados->db_data["herda"]==1 && !$Valor)
			{
				$sql="Select * from form_fields
				inner join form_records on form_records.ID_ORG=form_fields.ID_ORG 
				inner join form_records_fields on form_records_fields.Name=form_fields.ID 
				where form_records.ID=".$ID_From." and 
				form_records_fields.ID_ORG=".$ID_From." and form_fields.Name Like '".$dados->db_data["Name"]."'";
				$dados2=new DataObj;	
				$hRes= $dados2->db_open($db_link,$DB_name,$sql);
				if(!$dados2->EOF()) $Valor=$dados2->db_data["Valor"]."\n";
				$dados2->db_close();
			}
			/****************************** FIM LER DADOS DE CAMPOS PAI **************************************************/
			if(!$ID)
			{	
				$comma = ";";
			}
			else
			{		
				if($ID != 0) $comma = "\n";
				else $comma = ";";
			}
			if($tipo==1) $linea .= $Valor.$comma;
			if($tipo==2) $linea .= $Valor.$comma;
			if($tipo==3) $linea .= $Valor.$comma;
			if($tipo==30) $linea .= $Valor.$comma;
			if($tipo==4) $linea .= $Valor.$comma;
			if($tipo==31) $linea .= $Valor.$comma;
			if($tipo==10) $linea .= $Valor.$comma;
			if($tipo==32)
			{
				global $DB_host,$DB_user,$DB_pass,$DB_name; 
				$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass); 
				$sql = "SELECT * FROM products_lang WHERE ID = ". $Valor;
				$data=new DataObj;
				$hRes = $data->db_open($db_link,$DB_name,$sql);
				$linea.= $data->db_data["Cabecalho"].$comma;
			}
			if($tipo==11) 
			{
				if($Valor== 1) $linea .= $dados2->db_data["Value"].$comma;
				else $linea .= $dados2->db_data["Value"].$comma;
			}
			if($tipo==13) $linea .= $Valor.$comma;
			if($tipo==5)
			{
				$dados2=BuscarRegistoOpcoes($dados->db_data["ID"],"form_options");
				while(!$dados2->EOF())
				{
					if($Align==1) 
					{   
						if($Valor== $dados2->db_data["Value"]) $linea.=$dados2->db_data["Value"].$comma;
					}
					if($Align==2) 
					{   
						if($Valor== $dados2->db_data["Value"]) $linea.= $dados2->db_data["Value"].$comma;
					}
					if($Align==3) 
					{   
						if($Valor== $dados2->db_data["Value"]) $linea.= $dados2->db_data["Value"].$comma;
					}
					if($Align==4) 
					{   
						if($Valor== $dados2->db_data["Value"]) $linea.= $dados2->db_data["Value"].$comma;
					}
					$dados2->db_next();
				}
			$dados2->db_close();
			}
			if($tipo==6) 
			{
				$dados2=BuscarRegistoOpcoes($dados->db_data["ID"],"form_options");
				$i=0;
				$linea1="";
				while(!$dados2->EOF())
				{
					$m_valor=$dados2->db_data["Value"];
					if($Align==1)
					{ 
						if(strstr($Valor,$dados2->db_data["Value"])) $linea1.= $dados2->db_data["Value"]." | ";
					}
					if($Align==2) 
					{
						if(strstr($Valor,$dados2->db_data["Value"])) $linea1.= $dados2->db_data["Value"]." | ";
					}
					if($Align==3) 
					{
						if(strstr($Valor,$dados2->db_data["Value"])) $linea1.= $dados2->db_data["Value"]." | ";
					}
					if($Align==4) 
					{
						if(strstr($Valor,$dados2->db_data["Value"])) $linea1.= $dados2->db_data["Value"]." | ";
					}
				$dados2->db_next();
				$i++;
				}
				$linea .= $linea1.$comma;
			}
		
			if($tipo==7) 
			{
				$dados2=BuscarRegistoOpcoes($dados->db_data["ID"],"form_options");
				while(!$dados2->EOF())
				{
					if($Valor==$dados2->db_data["Value"]) $linea.=$dados2->db_data["Value"].$comma;
					$dados2->db_next();
				}  
				$dados2->db_close();
			}
		 
			if($tipo==8) 
			{
				$dados2=BuscarRegistoOpcoes($dados->db_data["ID"],"form_options");
				while(!$dados2->EOF())
				{
					if($Valor==$dados2->db_data["Value"])  $linea.=$dados2->db_data["Value"].$comma;
					$dados2->db_next();
				}
				$dados2->db_close();
			}
			if($dados->db_data["Muda"]==0)
			{
			  $start_linha=1;
			}
			$dados->db_next();
		}
		$dados->db_close();
	}
	if(!$ID) 
	{
		$data=new DataObj;
		$sql = "SELECT Name FROM forms WHERE ID = $ID_ORG"; 
		$hRes = $data->db_open($db_link,$DB_name,$sql); 
		$forms = $data->db_data["Name"]; 
		$data->db_close();
		$filename = "listagem_".$forms.".csv";
		$sql="SELECT Name FROM form_fields WHERE ID_ORG=".$ID_ORG. " ORDER BY Ordem"; 
		$hRes = $data->db_open($db_link,$DB_name,$sql); 
		while(!$data->EOF())
		{	
			if(!$fields) $fields = $data->db_data["Name"];
			else $fields .= ";" . $data->db_data["Name"];	
			$data->db_next();
		}
		$data->db_close();
		$line .= $fields . "\n" . $linea;
	}
	else
	{
		$filename = $forms."_".$ID.".csv";
		$line .= $linea;
	}
	
	if($line)
	{
	  /*header("Content-type: application/vnd.ms-excel");
	  header("Content-Disposition: attachment; filename=".$filename);
	  header("Pragma: no-cache");
	  header("Expires: 0");*/
	  echo $line;
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