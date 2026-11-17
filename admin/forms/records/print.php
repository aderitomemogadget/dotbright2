<? 
include "settings.inc.php"; 
?> 
<html> 
<head> 
<title>Backoffice</title> 
<meta http-equiv=Content-Type content="text/html; charset=utf-8"> 
<link rel=stylesheet type=text/css href="<? echo $includes_path;?>backoffice.css"> 
</head> 
<body> 
<?
if($ID_ORG!=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name; 
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass); 
	$dados=new DataObj;
	$sql="Select * from forms where ID=$ID_ORG"; 
	$hRes=$dados->db_open($db_link,$DB_name,$sql); 
	if(!$hRes) echo db_error($db_link);
	if(!$dados->EOF())
	{
		if($dados->db_data["Texto0"])
		{
			$name=$dados->db_data["Name"];
			$key_email=$dados->db_data["Texto0"];
			$key_email = str_replace("[nome]",$name,$key_email);
			parseContents($key_email);			
		}
		$dados->db_close();
	}
	else echo "ERRO";
} 
?>
</body> 
</html>
<?
function parseContents($conteudo)
{
    $conteudo="!".$conteudo;
    $pos[0]=strpos($conteudo,"{campodesc(");
    $pos[1]=strpos($conteudo,"{camporesp(");
    $pos[2]=strpos($conteudo,"{campocoment(");
    $pos[3]=strpos($conteudo,"{list_str(");
    $pos[4]=strpos($conteudo,"{list_wkn(");
    $pos[5]=strpos($conteudo,"{list_opt(");
    $pos[6]=strpos($conteudo,"{list_thr(");
    $pos[7]=strpos($conteudo,"{list_stropt(");
    $pos[8]=strpos($conteudo,"{list_strthr(");
    $pos[9]=strpos($conteudo,"{list_wknopt(");
    $pos[10]=strpos($conteudo,"{list_wknthr(");
    $pos[11]=strpos($conteudo,"{plano_accao(");

    $i=0;
    while(intval($pos[$i])==0 && $i<sizeof($pos)) $i++;
    $lower_value=$pos[$i];
    for($k=$i;$k<sizeof($pos);$k++){
        if((intval($pos[$k])<>0) && ($lower_value>intval($pos[$k]))) $lower_value=$pos[$k];
    }

    $flag=array_sum($pos);

    while($flag){
        if($pos[0]==$lower_value){
            if($pos[0]>1) echo substr($conteudo,1,$pos[0]-1);
            $conteudo=substr($conteudo,$pos[0]+11);
            $numero=substr($conteudo,0,strpos($conteudo,")"));
            $conteudo=substr($conteudo,strpos($conteudo,")")+2);
            show_campo(1,$numero);
        } elseif($pos[1]==$lower_value){
            if($pos[1]>1) echo substr($conteudo,1,$pos[1]-1);
            $conteudo=substr($conteudo,$pos[1]+11);
            $numero=substr($conteudo,0,strpos($conteudo,")"));
            $conteudo=substr($conteudo,strpos($conteudo,")")+2);
            show_campo(2,$numero);
        } elseif($pos[2]==$lower_value){
            if($pos[2]>1) echo substr($conteudo,1,$pos[2]-1);
            $conteudo=substr($conteudo,$pos[2]+13);
            $numero=substr($conteudo,0,strpos($conteudo,")"));
            $conteudo=substr($conteudo,strpos($conteudo,")")+2);
            show_campo(3,$numero);
        } elseif($pos[3]==$lower_value){
            if($pos[3]>1) echo substr($conteudo,1,$pos[3]-1);
            $conteudo=substr($conteudo,$pos[3]+10);
            $numero=substr($conteudo,0,strpos($conteudo,")"));
            $conteudo=substr($conteudo,strpos($conteudo,")")+2);
            show_basic_swot("S",$numero);
        } elseif($pos[4]==$lower_value){
            if($pos[4]>1) echo substr($conteudo,1,$pos[4]-1);
            $conteudo=substr($conteudo,$pos[4]+10);
            $numero=substr($conteudo,0,strpos($conteudo,")"));
            $conteudo=substr($conteudo,strpos($conteudo,")")+2);
            show_basic_swot("W",$numero);
        } elseif($pos[5]==$lower_value){
            if($pos[5]>1) echo substr($conteudo,1,$pos[5]-1);
            $conteudo=substr($conteudo,$pos[5]+10);
            $numero=substr($conteudo,0,strpos($conteudo,")"));
            $conteudo=substr($conteudo,strpos($conteudo,")")+2);
            show_basic_swot("O",$numero);
        } elseif($pos[6]==$lower_value){
            if($pos[6]>1) echo substr($conteudo,1,$pos[6]-1);
            $conteudo=substr($conteudo,$pos[6]+10);
            $numero=substr($conteudo,0,strpos($conteudo,")"));
            $conteudo=substr($conteudo,strpos($conteudo,")")+2);
            show_basic_swot("T",$numero);
        } elseif($pos[7]==$lower_value){
            if($pos[7]>1) echo substr($conteudo,1,$pos[7]-1);
            $conteudo=substr($conteudo,$pos[7]+13);
            $numero=substr($conteudo,0,strpos($conteudo,")"));
            $conteudo=substr($conteudo,strpos($conteudo,")")+2);
            show_extended_swot("S","O",$numero);
        } elseif($pos[8]==$lower_value){
            if($pos[8]>1) echo substr($conteudo,1,$pos[8]-1);
            $conteudo=substr($conteudo,$pos[8]+13);
            $numero=substr($conteudo,0,strpos($conteudo,")"));
            $conteudo=substr($conteudo,strpos($conteudo,")")+2);
            show_extended_swot("S","T",$numero);
        } elseif($pos[9]==$lower_value){
            if($pos[9]>1) echo substr($conteudo,1,$pos[9]-1);
            $conteudo=substr($conteudo,$pos[9]+13);
            $numero=substr($conteudo,0,strpos($conteudo,")"));
            $conteudo=substr($conteudo,strpos($conteudo,")")+2);
            show_extended_swot("W","O",$numero);
        } elseif($pos[10]==$lower_value){
            if($pos[10]>1) echo substr($conteudo,1,$pos[10]-1);
            $conteudo=substr($conteudo,$pos[10]+13);
            $numero=substr($conteudo,0,strpos($conteudo,")"));
            $conteudo=substr($conteudo,strpos($conteudo,")")+2);
            show_extended_swot("W","T",$numero);
        } elseif($pos[11]==$lower_value){
            if($pos[11]>1) echo substr($conteudo,1,$pos[11]-1);
            $conteudo=substr($conteudo,$pos[11]+13);
            $numero=substr($conteudo,0,strpos($conteudo,")"));
            $conteudo=substr($conteudo,strpos($conteudo,")")+2);
            show_plano_accao($numero);
        }
        
        
        $conteudo="!".$conteudo;
        $pos[0]=strpos($conteudo,"{campodesc(");
        $pos[1]=strpos($conteudo,"{camporesp(");
        $pos[2]=strpos($conteudo,"{campocoment(");
        $pos[3]=strpos($conteudo,"{list_str(");
        $pos[4]=strpos($conteudo,"{list_wkn(");
        $pos[5]=strpos($conteudo,"{list_opt(");
        $pos[6]=strpos($conteudo,"{list_thr(");
        $pos[7]=strpos($conteudo,"{list_stropt(");
        $pos[8]=strpos($conteudo,"{list_strthr(");
        $pos[9]=strpos($conteudo,"{list_wknopt(");
        $pos[10]=strpos($conteudo,"{list_wknthr(");
        $pos[11]=strpos($conteudo,"{plano_accao(");

        $i=0;
        while(intval($pos[$i])==0 && $i<sizeof($pos)) $i++;
        $lower_value=$pos[$i];
        for($k=$i;$k<sizeof($pos);$k++){
            if((intval($pos[$k])<>0) && ($lower_value>intval($pos[$k]))) $lower_value=$pos[$k];
        }

        $flag=array_sum($pos);
    }

    echo substr($conteudo,1);
}
function show_campo($tipo,$numero)
{
  global $DB_host,$DB_user,$DB_pass,$DB_name,$id_org,$ID,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$dados=new DataObj;
	$dados2=new DataObj;

    $sql="SELECT * FROM form_fields WHERE id=".$numero;
    $hRes=$dados->db_open($db_link,$DB_name,$sql);
    
    if(!$dados->EOF()) 
	{
		if($dados->db_data["col_labels"])
		{
				$cod=$dados->db_data["ID"];
				$dados3=new DataObj;
				$rows=explode(";",$dados->db_data["row_labels"]);
				$cols=explode(";",$dados->db_data["col_labels"]);
				$col_width=90/sizeof($cols);
			  
				echo '<br>';
				echo '<table width=100% class="border1" border=0 cellpadding=2 cellspacing=0>';
				echo '<tr>';
				echo '<td width=10%>&nbsp;</td>';
				for($k=0;$k<sizeof($cols);$k++){
					$m_titulo=$cols[$k];	
					$xpos1=strpos($m_titulo,'[');
					$xpos2=strpos($m_titulo,']');
					if($xpos1 && $xpos2)	$m_titulo=substr($m_titulo,0,$xpos1);
					echo '<td width='.$colwidth.'% align=center>'.$m_titulo.'</td>';
				}
				echo '</tr>';
				
				for($k=0;$k<sizeof($rows);$k++){
				  echo '<tr>';
				  echo '<td width=10%>'.$rows[$k].'</td>';
				  for($n=0;$n<sizeof($cols);$n++){
					$resp=(($k+1)*100)+($n+1);            
					$sql3='select * from form_records_fields where Name="'.$cod.'_r'.($k+1).'_c'.($n+1).'" and ID_ORG='.$ID;
		
					$hRes=$dados3->db_open($db_link,$DB_name,$sql3);
					
					echo '<td width='.$colwidth.'% align=center>';
					
					if($dados->db_data["matrix_type"]==0){
					  echo '<input type="checkbox" name="field'.$cod.'_r'.($k+1).'_c'.($n+1).'" value="x"';
					  if(!$dados3->EOF() && $dados3->db_data["Valor"]=="x") echo " checked";
					  echo '>';
					} else {
				
					  $campo_aux=$cols[$n];
					  $pos1=strpos($campo_aux,'[');
					  $pos2=strpos($campo_aux,']');
					  if($pos1 && $pos2)
					  {
						$campo_aux2=substr($campo_aux,$pos1+1);	
						$pos2=strpos($campo_aux2,']');
						$campo_aux2=substr($campo_aux2,0,$pos2);			  	
						$valores=explode(",",$campo_aux2);
						if($valores[0]==14)
						{
							// VALORES DE OUTRO FORMULARIO -14
							/*************************************************************************************/
							if(!$dados3->EOF() && $dados3->db_data["Valor"]<>"") $Valor=$dados3->db_data["Valor"];
							$sql="Select * from form_records_fields where Name=".$valores[2];
							$obj=new DataObj;
							$hRes=$obj->db_open($db_link,$DB_name,$sql);
							if(!$obj->EOF())
							{
								echo '<select name="field'.$cod.'_r'.($k+1).'_c'.($n+1).'">';
								echo '<OPTION value="0"';
								if($Valor=="0") echo " selected";
								echo '>----</OPTION>';
								while(!$obj->EOF())
								{			
									echo '<OPTION value="'.$obj->db_data["ID"].'"';
									if($obj->db_data["ID"]==$Valor) echo " selected";
									echo '>'.$obj->db_data["Valor"].'</OPTION>';
									if($Obrigatorio==1){echo '<font color=ff0000> *</font>';$obrigatorio=1;}
									$obj->db_next();
								}
								$obj->db_close();
								echo '</select>';
							}
							/*************************************************************************************/
						}
					  }
					  else
					  {
						  echo '<input class="matrix" name="field'.$cod.'_r'.($k+1).'_c'.($n+1).'" style="WIDTH: 100%; HEIGHT: 100%"';
						  if(!$dados3->EOF() && $dados3->db_data["Valor"]<>"") echo ' value="'.$dados3->db_data["Valor"].'"';
						  echo '>';
					  }
					}
					echo '</td>';
				  }
				  echo '</tr>';
				}
				echo '</table>';
		}
		else
		{		
			$tipo=$dados->db_data["Tipo"];
			
			$sql="SELECT * FROM form_records_fields WHERE ID_ORG=".$ID." AND Name=".$dados->db_data["ID"];
		    $hRes=$dados->db_open($db_link,$DB_name,$sql);
		    if(!$dados->EOF()) $Valor=stripslashes($dados->db_data["Valor"]);
			
			if($tipo==32)
			 {
			 	$hRes=$dados2->db_open($db_link,$DB_name,"Select * from products_lang WHERE ID_PAI=$Valor and Lang='$admin_lang'");
				echo ($dados2->db_data["Cabecalho"]);
				$dados2->db_close(); 
			 }
			 else
			 {
			 	$Valor=str_replace("{}"," | ",$Valor);
			 	echo $Valor;
			 }
		}
  }
}
?>