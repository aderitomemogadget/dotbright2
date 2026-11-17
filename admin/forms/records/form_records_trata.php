<? 
include "settings.inc.php";

if($volta) $m_url="form_records.php?ID=$volta&ID_ORG=$volta2"; 
else $m_url="list.php?ID_ORG=$ID_ORG"; 

$Nome="form_records_fields"; 
global $DB_host,$DB_user,$DB_pass,$DB_name,$Nome; 
$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass); 
if ($task_form=="insere")
{
  $data=getdate();
  $ano=$data["year"];
  $mes=$data["mon"];
  $dia=$data["mday"];
  $dados=new DataObj;
  $campo_id_pai=guarda_bd_form2($ID_ORG,$DB_host,$DB_user,$DB_pass,$DB_name,"form_records",$ID_From);
  $hRes=$dados->db_open( $db_link,$DB_name,"Select * from form_fields where ID_ORG=$ID_ORG");
  while(!$dados->EOF())
  {
    $campo=$dados->db_data["Name"];
    $campo_id=$dados->db_data["ID"];
    $tipo=$dados->db_data["Tipo"];
	if($tipo==16)
	{
		$rows=explode(";",$dados->db_data["row_labels"]);
		$cols=explode(";",$dados->db_data["col_labels"]);

		for($k=0; $k<sizeof($rows); $k++) {
		  for($n=0; $n<sizeof($cols); $n++) {
			$Valor="field".$campo_id."_r".($k+1)."_c".($n+1);
			$resp=(($k+1)*100)+($n+1);

			$sql="Update $Nome Set Valor='".$$Valor."' where Name='".$campo_id."_r".($k+1)."_c".($n+1)."' and ID_ORG=$campo_id_pai"; 
			$sql2="insert into $Nome (Valor,Name,ID_ORG) values ('".$$Valor."','".$campo_id."_r".($k+1)."_c".($n+1)."','".$campo_id_pai."')";					
			// Vamos ver se o registo já foi inserido
			$sql3='select * from '.$Nome.' where Name="'.$campo_id.'_r'.($k+1).'_c'.($n+1).'" and ID_ORG='.$campo_id_pai;
			$dados3=new DataObj;
			$hRes=$dados3->db_open( $db_link,$DB_name,$sql3); 
			if(!$dados3->EOF())
			{
				$result=Runquery($db_link,$DB_name,$sql); 
				$dados3->db_close();
			}
			else Runquery($db_link,$DB_name,$sql2);					
		  }
		}
	}
    elseif($tipo==9)
    {
      $variavel_name="field".$campo_id."_name";
      $variavel="field".$campo_id;
      $Documento["name"]=$$variavel_name;
      $Documento["temp"]=$$variavel;
      $img_path="../../../img_upload/formularios/";
      guarda_doc_bd_campos2($campo_id_pai,$campo_id,$img_path,$Documento,$DB_host,$DB_user,$DB_pass,$DB_name);
	  guarda_doc_bd_campos2($campo_id_pai,$campo_id,$img_path,$Documento,$Server,$UserName,$db_password,$DBName);
	}
	elseif($tipo==10 || $tipo==13)
    {
	  $ano="ano".$campo_id;
	  $mes="mes".$campo_id;
	  $dia="dia".$campo_id;
	  $valor=$$ano."-".$$mes."-".$$dia;
	  guarda_bd_campos2($campo_id_pai,$campo_id,$valor,$DB_host,$DB_user,$DB_pass,$DB_name);  
    }
	elseif($tipo==6)
	{
		$campo_html="field".$campo_id."_counter";
		$contador=$$campo_html;
		$variavel="field".$campo_id."_0";
		$valor=$$variavel;
		for($k=1; $k<$contador; $k++) 
		{
			$variavel="field".$campo_id."_".$k;
			$valor.="{}".$$variavel;
		}
		guarda_bd_campos2($campo_id_pai,$campo_id,$valor,$DB_host,$DB_user,$DB_pass,$DB_name);  
	}
	elseif($tipo==17)
	{
		$sql='select MAX(Valor) from form_records_fields inner join form_records on form_records.ID=form_records_fields.ID_ORG where form_records.ID_ORG='.$ID_ORG.' and Name='.$campo_id;
		$rec=new DataObj;
		$rec->db_open($db_link,$DB_name,$sql);
		if($rec->EOF()) $valor=1;
		else $valor=$rec->db_data[0] + 1;
		$rec->db_close();
		guarda_bd_campos2($campo_id_pai,$campo_id,$valor,$DB_host,$DB_user,$DB_pass,$DB_name);
	}
	elseif($tipo == 32)
	{
		$valor = $Product;
		guarda_bd_campos2($campo_id_pai,$campo_id,$valor,$DB_host,$DB_user,$DB_pass,$DB_name);
	}
    else
    {
      $variavel="field".$campo_id;
      $mailbody.="\n\n$campo :\n".$$variavel;
      guarda_bd_campos2($campo_id_pai,$campo_id,$$variavel,$DB_host,$DB_user,$DB_pass,$DB_name);
    }
    $dados->db_next();
  }
 $dados->db_close(); 
 show_box_OK(1,$m_url); 
} 		
if ($task_form=="altera")
{
	if($ID!=0)
	{
		$sql="Select * FROM form_fields where ID_ORG=".$ID_ORG. " order by Ordem"; 
		$dados=new DataObj;
		$hRes=$dados->db_open( $db_link,$DB_name,$sql);
		while(!$dados->EOF())
		{
			$cod=$dados->db_data["ID"];
			$Name=$dados->db_data["Name"];
			$tipo=$dados->db_data["Tipo"];
			$Valor="field".$cod;
			if($tipo==16)
			{
				$rows=explode(";",$dados->db_data["row_labels"]);
				$cols=explode(";",$dados->db_data["col_labels"]);

				for($k=0; $k<sizeof($rows); $k++) {
				  for($n=0; $n<sizeof($cols); $n++) {
					$Valor="field".$cod."_r".($k+1)."_c".($n+1);
					$resp=(($k+1)*100)+($n+1);

					$sql="Update $Nome Set Valor='".$$Valor."' where Name='".$cod."_r".($k+1)."_c".($n+1)."' and ID_ORG=$ID"; 
					$sql2="insert into $Nome (Valor,Name,ID_ORG) values ('".$$Valor."','".$cod."_r".($k+1)."_c".($n+1)."','".$ID."')";					
					// Vamos ver se o registo já foi inserido
					$sql3='select * from '.$Nome.' where Name="'.$cod.'_r'.($k+1).'_c'.($n+1).'" and ID_ORG='.$ID;
					$dados3=new DataObj;
					$hRes=$dados3->db_open( $db_link,$DB_name,$sql3); 
					if(!$dados3->EOF())
					{
						$result=Runquery($db_link,$DB_name,$sql); 
						$dados3->db_close();
					}
					else	Runquery($db_link,$DB_name,$sql2);					
				  }
				}
			}
			elseif($tipo == 32)
			{
				$sql = "UPDATE $Nome SET VALOR = '" . $Product . "' WHERE Name = \"" . $cod . "\" AND ID_ORG = $ID";
				$result = Runquery($db_link,$DB_name,$sql);
			}
			elseif($tipo!=9)
			{
				if($tipo==10 || $tipo==13)
				{
				  $ano="ano".$cod;
				  $mes="mes".$cod;
				  $dia="dia".$cod;
				  $Valor=$$ano."-".$$mes."-".$$dia;
				  $sql="Update $Nome Set Valor='".$Valor."' where Name=\"".$cod."\" and ID_ORG=$ID"; 
				  $sql2="insert into $Nome (Valor,Name,ID_ORG) values ('".$Valor."','".$cod."','".$ID."')";
				}
				elseif($tipo==6)
				{
					$campo_html="field".$cod."_counter";
					$contador=$$campo_html;
					$variavel="field".$cod."_0";
					$Valor=$$variavel;
					for($k=1; $k<$contador; $k++) 
					{
						$variavel="field".$cod."_".$k;
						$Valor.="{}".$$variavel;
					}
					$sql="Update $Nome Set Valor='".$Valor."' where Name='".$cod."' and ID_ORG=$ID"; 
					$sql2="insert into $Nome (Valor,Name,ID_ORG) values ('".$Valor."','".$cod."','".$ID."')";
				}				
				else 
				{
					$sql="Update $Nome Set Valor='".$$Valor."' where Name='".$cod."' and ID_ORG=$ID"; 
					$sql2="insert into $Nome (Valor,Name,ID_ORG) values ('".$$Valor."','".$cod."','".$ID."')";
				}
				$dados3=new DataObj;
				$sql3="select * from $Nome where Name=\"".$cod."\" and ID_ORG=$ID";
				$hRes=$dados3->db_open( $db_link,$DB_name,$sql3); 
				if(!$dados3->EOF())
				{
					$result=Runquery($db_link,$DB_name,$sql); 
					$dados3->db_close();
				}
				else	Runquery($db_link,$DB_name,$sql2);
			}
			$dados->db_next(); 
		} 
		$dados->db_close(); 
	} 
	if($result) show_box_OK(2,$m_url); 
	else show_box_ERROR(2,$m_url); 
} 
elseif($task_form=="apaga") 
{
	$result=Runquery($db_link,$DB_name,"delete from $Nome where ID_ORG=$ID");
	if($result)  
	{
		$result2=Runquery($db_link,$DB_name,"delete from form_records where ID=$ID");
		if($result)show_box_OK(3,$m_url); 
		else show_box_ERROR(3,$m_url); 
	} 
	else show_box_ERROR(3,$m_url); 
} 
?>