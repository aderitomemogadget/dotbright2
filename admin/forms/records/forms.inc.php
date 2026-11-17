<?
function show_forms($id)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$Nome,$key_enviar;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$dados2=new DataObj;
	$sql="Select * from form_fields where ID_ORG=$id And Tipo=30";
	$hRes=$dados2->db_open($db_link,$DB_name,$sql);
	$update_fields="";
	if(!$dados2->EOF())
	{
	  $update_fields='onchange="javascript:update_fields();"';
	  echo '<script>
    function update_fields()
    {
    ';
      while(!$dados2->EOF())
      {
        echo 'field'.$dados2->db_data["ID"].'();';
        $dados2->db_next();
      }
    echo '
    }
    </script>';
  }
  $dados2->db_close();
  
	$dados=new DataObj;
	$hRes=$dados->db_open($db_link,$DB_name,"Select Email,Tipo from forms where ID=$id");
	echo '
		<script language="JavaScript" type="text/JavaScript">
	<!--
	function isInteger'.$id.'(value){return value==parseInt(value);}
	function checkInt'.$id.'(field,update)
	{
		if(!isInteger'.$id.'(field.value)){alert("Por favor insira um numero inteiro!");field.value="";}
		if(update) update_fields();
	}
	function checkEmail'.$id.'(field){email=field.value;AtPos=email.indexOf("@");StopPos=email.lastIndexOf(".");Message="";if(email==""||AtPos==-1||StopPos==-1||(StopPos<AtPos)||(StopPos-AtPos==1)){alert("Por favor insira um email correcto!");field.value="";}}
	//-->
	</script>
	<form name="form'.$id.'" method="post" onSubmit="return teste()" action="send_request.php?id='.$id.'" enctype="multipart/form-data">';
	if(!$dados->EOF())
	{
		$ArrayPalavras=explode(";",$dados->db_data["Email"]);
		$tipo=$dados->db_data["Tipo"];
		$n_palavras=count($ArrayPalavras);
		if($n_palavras>1)
		{    
		   echo '<table width="100%" height="22" border=0 cellpadding=0 cellspacing=0>';
		   echo '<tr> 
					<td width=10></td>
					<td width=150 class="forms">Para: </td>
					<td class="forms">';                                
		  echo '<select name="m_email'.$i.'">'; 
		  for($j=0;$j<$n_palavras;$j++)
			  {
				$ArrayPalavras[$j]=str2SQLLikestr($ArrayPalavras[$j]); 
				echo '<OPTION value="'.$ArrayPalavras[$j].'">'.$ArrayPalavras[$j].'</OPTION>';
			  }
		  echo "</select>";
		  echo '</td></tr></table>';
		}
	}
	$hRes=$dados->db_open($db_link,$DB_name,"Select * from form_fields where ID_ORG=$id order by Ordem Asc");
	  echo ' <table width="100%" height="22" border=0 cellpadding="2" cellspacing=0><tr><td width="75"></td></tr>';
               $start_linha=1;
               $obrigatorio=0;
               $str_obrigatorios="";
			   $campo_ant_mudou=0;
               while(!$dados->EOF())
                {
					
                    if($start_linha)
                    {
                       if($dados->db_data["Tipo"]==12)
						{
						   echo '<tr> 
									<td width=10></td>
									<td width=100 class="preto"></td>
									<td class="preto"';						
						}
						elseif($dados->db_data["Tipo"]==13 || $dados->db_data["Tipo"]==30 || $dados->db_data["Tipo"]==31)
						{
							echo '<tr> 
													<td width=10></td>
													<td class="cinza2" align=right>'. stripslashes($dados->db_data["Name"]).'</td>
													<td class="preto"';
						}
						elseif($dados->db_data["Tipo"]>=10 || $dados->db_data["Altera_only"]=="1")
						{
						   echo '<tr> 
									<td width=10></td>
									<td class="preto"> </td>
									<td class="preto"';
						}
						else
						{
							if($dados->db_data["Tipo"]==0 || (($dados->db_data["Tipo"]==5 || $dados->db_data["Tipo"]==6 ) && $dados->db_data["Full_line"]==1))
							{
							  echo '<tr>
										<td width=10></td>
										<td class="cinza2" colspan=2>'. stripslashes($dados->db_data["Name"]).' ';
							}
							else
							{
								echo '<tr> 
								<td width=10></td>
								<td class="cinza2" align=left>'. stripslashes($dados->db_data["Name"]).' </td>
								<td width=590 class="preto"';
								
								if($dados->db_data["Align"]==1 || $dados->db_data["Align"]==3) echo "align=left";
								
							}
							$start_linha=0;
						}
						if($dados->db_data["Tipo"]!=0 && !(($dados->db_data["Tipo"]==5 || $dados->db_data["Tipo"]==6) && $dados->db_data["Full_line"]==1)) echo '>';
                    }
                    else echo stripslashes($dados->db_data["Name"]."  ");
  
                        if($dados->db_data["Length2"]) $largura=$dados->db_data["Length2"]."px";
                        else $largura=($dados->db_data["Length"]*100/254)."%";
                        if($dados->db_data["Obrigatorio"]==1 && $dados->db_data["Muda"]==0 && $campo_ant_mudou==0) echo tabela_ini($largura);
						if($largura < 30) $largura=30;

						if($update_fields) $update_fields2=1;
                        if($dados->db_data["Tipo"]==1 && $dados->db_data["legenda"]) echo '<input '.$update_fields.' class=login name="field'.$dados->db_data["ID"].'" style="WIDTH: '.$largura.'; HEIGHT: 20px" maxlength="'.$dados->db_data["Length"].'">&nbsp;'.$dados->db_data["legenda"].'&nbsp;';
			            if($dados->db_data["Tipo"]==1 && !$dados->db_data["legenda"]) echo '<input '.$update_fields.' class=login name="field'.$dados->db_data["ID"].'" style="WIDTH: '.$largura.'; HEIGHT: 20px" maxlength="'.$dados->db_data["Length"].'">&nbsp;';
                        if($dados->db_data["Tipo"]==2) echo '<textarea class=login name="field'.$dados->db_data["ID"].'" style="WIDTH: 100%; HEIGHT: 100px;"></textarea>';
                        if($dados->db_data["Tipo"]==3 && $dados->db_data["legenda"]) echo '<input value=0 onChange="checkInt'.$id.'(this,'.$update_fields2.');" class=login name="field'.$dados->db_data["ID"].'" style="WIDTH: '.$largura.'; HEIGHT: 20px" maxlength="'.$dados->db_data["Length"].'">&nbsp;'.$dados->db_data["legenda"].'&nbsp;';
						if($dados->db_data["Tipo"]==3 && !$dados->db_data["legenda"]) echo '<input value=0 onChange="checkInt'.$id.'(this,'.$update_fields2.');" class=login name="field'.$dados->db_data["ID"].'" style="WIDTH: '.$largura.'; HEIGHT: 20px" maxlength="'.$dados->db_data["Length"].'">&nbsp;';
                        if($dados->db_data["Tipo"]==4) echo '<input class=login type="password" name="field'.$dados->db_data["ID"].'" style="WIDTH: '.$largura.'; HEIGHT: 20px" maxlength="'.$dados->db_data["Length"].'">';
       					if($dados->db_data["Tipo"]==9) echo '<input type="file" class=forms name="field'.$dados->db_data["ID"].'" style="HEIGHT: 20px">';
           		       if($dados->db_data["Tipo"]==31) echo '<input type="text" class=forms name="Email" style="HEIGHT: 20px">';
					   if($dados->db_data["Tipo"]==32) 
					   {
					   		global $ID;
					   		echo '<input type = "hidden" name = "field'.$dados->db_data["ID"].'" value = "'.$ID.'" class = forms/>';				
					   }
					    if($dados->db_data["Tipo"]==30)
						{
						  $formula=$dados->db_data["formula"];
		
						  $str='eval(document.form'.$id.'.field';
						  $formula=str_replace("[",$str,$formula);
						  $formula=str_replace("]",".value)",$formula);
						  echo '<script> function field'.$dados->db_data["ID"].'()
						  {
							var resultado='.$formula.';
							if(resultado) document.form'.$id.'.field'.$dados->db_data["ID"].'.value=resultado;
							else document.form'.$id.'.field'.$dados->db_data["ID"].'.value="";
						  }
						  </script>
						  ';
						  echo '<input readonly="" class=forms type="text" name="field'.$dados->db_data["ID"].'" style="WIDTH: '.$largura.'; HEIGHT: 20px" maxlength="'.$dados->db_data["Length"].'">';
						  if($dados->db_data["legenda"]) echo '&nbsp;'.$dados->db_data["legenda"].'&nbsp;';
						}
						if($dados->db_data["Tipo"]==10)
                        {
						  $D=getdate();
						  $ano=$D["year"];
						  $mes=$D["mon"];
						  $dia=$D["mday"];
						  if($mes < 10) $mes="0".$mes;
						  if($dia < 10) $dia="0".$dia;
						  echo '<input type=hidden name="field'.$dados->db_data["ID"].'" value="'.$ano.'-'.$mes.'-'.$dia.'">';
						}
              					
                        if($dados->db_data["Tipo"]<5 && $dados->db_data["Obrigatorio"]==1) 
                        {
                          if($dados->db_data["Muda"]==0 && $campo_ant_mudou==0) echo '</td><td width=15 align=center><font color=ff0000> *</font>';
						  else	echo '<font color=ff0000> *</font>';
                          $obrigatorio=1; 
                          //$str_obrigatorios='document.form.field'.$dados->db_data["ID"].'.value==""';
    					            if($str_obrigatorios)   $str_obrigatorios.=' || document.form'.$id.'.field'.$dados->db_data["ID"].'.value==""';
                          else    $str_obrigatorios='document.form'.$id.'.field'.$dados->db_data["ID"].'.value==""';
                        }
                        if($dados->db_data["Tipo"]==5) 
                        {
							if($dados->db_data["Name2"]) echo stripslashes($dados->db_data["Name2"])."<br>";
                          $dados2=BuscarRegistoOpcoes($dados->db_data["ID"]);
                          while(!$dados2->EOF())
                          {
                              if($dados->db_data["Align"]==1) echo $dados2->db_data["Value"].'<INPUT TYPE="radio" NAME="field'.$dados->db_data["ID"].'" value="'.$dados2->db_data["Value"].'">';
                              if($dados->db_data["Align"]==2) echo '<INPUT TYPE="radio" NAME="field'.$dados->db_data["ID"].'" value="'.$dados2->db_data["Value"].'">'.$dados2->db_data["Value"];
                              if($dados->db_data["Align"]==3) echo $dados2->db_data["Value"].'<INPUT TYPE="radio" NAME="field'.$dados->db_data["ID"].'" value="'.$dados2->db_data["Value"].'"><br>';
                              if($dados->db_data["Align"]==4) echo '<INPUT TYPE="radio" NAME="field'.$dados->db_data["ID"].'" value="'.$dados2->db_data["Value"].'">'.$dados2->db_data["Value"].'<br>';
                              $dados2->db_next();
                          }
                          
                          if($dados->db_data["Obrigatorio"]==1) 
                          {
                              if($dados->db_data["Muda"]==0 && $campo_ant_mudou==0) echo '</td><td width=15 align=center><font color=ff0000> *</font>';
							  else	echo '<font color=ff0000> *</font>';
                              $obrigatorio=1; 
                              if($str_obrigatorios)   $str_obrigatorios.=' || document.form'.$id.'.field'.$dados->db_data["ID"].'.value==""';
                              else    $str_obrigatorios='document.form'.$id.'.field'.$dados->db_data["ID"].'.value==""';
                          }
                          echo "<br>";
                          $dados2->db_close();
                        }
                        
                        if($dados->db_data["Tipo"]==6) 
                        {
							if($dados->db_data["Name2"]) echo stripslashes($dados->db_data["Name2"])."<br>";
                            $dados2=BuscarRegistoOpcoes($dados->db_data["ID"]);
							$i=0;
							if($dados->db_data["Align"]<3) echo '<table width="100%" border=0 cellpadding=0 cellspacing=0><tr>';
							else echo '<table width="100%" border=0 cellpadding=0 cellspacing=0>';
                            while(!$dados2->EOF())
                            {
                                if($dados->db_data["Align"]==1) echo '<td>'.$dados2->db_data["Value"].'</td><td width=10><INPUT TYPE="checkbox" NAME="field'.$dados->db_data["ID"].'_'.$i.'" value="'.$dados2->db_data["Value"].'"></td>';
                                if($dados->db_data["Align"]==2) echo '<td width=10><INPUT TYPE="checkbox" NAME="field'.$dados->db_data["ID"].'_'.$i.'" value="'.$dados2->db_data["Value"].'"></td><td>'.$dados2->db_data["Value"].'</td>';
                                if($dados->db_data["Align"]==3) echo '<tr><td>'.$dados2->db_data["Value"].'</td><td width=10><INPUT TYPE="checkbox" NAME="field'.$dados->db_data["ID"].'_'.$i.'" value="'.$dados2->db_data["Value"].'"></td></tr>';
                                if($dados->db_data["Align"]==4) echo '<tr><td width=10><INPUT TYPE="checkbox" NAME="field'.$dados->db_data["ID"].'_'.$i.'" value="'.$dados2->db_data["Value"].'"></td><td>'.$dados2->db_data["Value"].'</td></tr>';
                                $dados2->db_next();
								$i++;
                            }
							if($dados->db_data["Align"]<3) echo '</tr></table>';
							else echo '</table>';
                            if($dados->db_data["Obrigatorio"]==1)
                            {
                                if($dados->db_data["Muda"]==0 && $campo_ant_mudou==0) echo '</td><td width=15 align=center><font color=ff0000> *</font>';
								else	echo '<font color=ff0000> *</font>';
                                $obrigatorio=1; 
                                if($str_obrigatorios)   $str_obrigatorios.=' || document.form'.$id.'.field'.$dados->db_data["ID"].'.value==""';
                                else    $str_obrigatorios='document.form'.$id.'.field'.$dados->db_data["ID"].'.value==""';
                            }
							echo '<input type="hidden" name="field'.$dados->db_data["ID"].'_counter" value="'.$i.'">';
                            echo "<br>";
                            $dados2->db_close();
                        }
                        
                        if($dados->db_data["Tipo"]==7) 
                        {
                            echo '<select class=forms name="field'.$dados->db_data["ID"].'">';
                            $dados2=BuscarRegistoOpcoes($dados->db_data["ID"]);
                            while(!$dados2->EOF())
                            {
                                echo '<OPTION value="'.$dados2->db_data["Value"].'">'.$dados2->db_data["Value"].'</OPTION>';
                                $dados2->db_next();
                            }
                            $dados2->db_close();
                            echo '</select>';
                            if($dados->db_data["Obrigatorio"]==1) 
                            {
                                if($dados->db_data["Muda"]==0 && $campo_ant_mudou==0) echo '</td><td width=15 align=center><font color=ff0000> *</font>';
								else	echo '<font color=ff0000> *</font>';
                                $obrigatorio=1; 
                                if($str_obrigatorios)   $str_obrigatorios.=' || document.form'.$id.'.field'.$dados->db_data["ID"].'.value==""';
                                else    $str_obrigatorios='document.form'.$id.'.field'.$dados->db_data["ID"].'.value==""';
                            }
                        }
                        
                        if($dados->db_data["Tipo"]==8) 
                        {
                            echo '<select class=forms name="field'.$dados->db_data["ID"].'" size=5>';
                            $dados2=BuscarRegistoOpcoes($dados->db_data["ID"]);
                            while(!$dados2->EOF())
                            {
                                echo '<OPTION value="'.$dados2->db_data["Value"].'">'.$dados2->db_data["Value"].'</OPTION>';
                                $dados2->db_next();
                            }
                            $dados2->db_close();
                            echo '</select>';
                            if($dados->db_data["Obrigatorio"]==1) 
                            {
                                if($dados->db_data["Muda"]==0 && $campo_ant_mudou==0) echo '</td><td width=15 align=center><font color=ff0000> *</font>';
								else	echo '<font color=ff0000> *</font>';
                                $obrigatorio=1; 
                                if($str_obrigatorios)   $str_obrigatorios.=' || document.form'.$id.'.field'.$dados->db_data["ID"].'.value==""';
                                else    $str_obrigatorios='document.form'.$id.'.field'.$dados->db_data["ID"].'.value==""';
                            }
                        }
                        
					              if($dados->db_data["Tipo"]==13){show_data1($Valor,$dados->db_data["ID"]);}
					              
            					 if($dados->db_data["Tipo"]==14)
            					 {
            						$sql="Select * from form_records_fields where Name=".$dados->db_data["id_campo"];
            						$obj=new DataObj;
            						$hRes=$obj->db_open($db_link,$DB_name,$sql);
            						if(!$obj->EOF())
            						{
            							echo '<select name="field'.$dados->db_data["ID"].'">';
            							
            							echo '<OPTION value="0"';
            							if($Valor=="0") echo " selected";
            							echo '>----</OPTION>';
            							
            							while(!$obj->EOF())
            							{
            								echo '<OPTION value="'.$obj->db_data["ID"].'"';
            								if($obj->db_data["ID"]==$Valor) echo " selected";
            								echo '>'.$obj->db_data["Valor"].'</OPTION>';
            								if($Obrigatorio==1)
											{
												if($dados->db_data["Muda"]==0 && $campo_ant_mudou==0) echo '</td><td width=15 align=center><font color=ff0000> *</font>';
												else	echo '<font color=ff0000> *</font>';
												$obrigatorio=1;
											}
            								$obj->db_next();
            							}
            							$obj->db_close();
            							echo '</select>';
            						}
            					 }
            					 
            					 if($dados->db_data["Tipo"]==15)
            					 {
            						echo '<INPUT TYPE=button NAME="field'.$dados->db_data["ID"].'" value="'.$Name.'" onclick=\'javascript:window.open("webmail.php?action=edita_forms&ID=0&ID_ORG='.$id_form.'&ID_From='.$codigo.'","_self")\'>';
            					 }
								 
								 if($dados->db_data["Obrigatorio"]==1 && $dados->db_data["Muda"]==0 && $campo_ant_mudou==0) echo tabela_fim();
								 
                    if($dados->db_data["Muda"]==0)
                    {
                        $start_linha=1;
                        echo '</td>
                        </tr>';
                    }else echo "&nbsp;&nbsp;";
					$campo_ant_mudou=$dados->db_data["Muda"];
					if($dados->db_data["Tipo"]==18)
					 {
					 	global $admin_user,$admin_passwd;
						echo '<input type=hidden name="field'.$dados->db_data["ID"].'" value="'.return_user_id($admin_user,$admin_passwd).'">';
					 }
                $dados->db_next();
                }
                
                if($start_linha)
                {
                    echo '</td>
                    </tr>';
                }
                
                if($obrigatorio){
                ?>
                <script language="JavaScript" type="text/JavaScript">
        		<!--
                function teste()
                {
                	if(<?=$str_obrigatorios?>)
                	{
                		alert("<? global $key_registo1b; echo $key_registo1b;?>");
                		return false;
                	}
                	else
                	{
                	    return true;
                	}
                }
        		//-->
        		</script>
                <tr> 
                    <td width=10 height=5></td>
                    <td width=150></td>
                    <td></td>
                </tr>
                <tr> 
                    <td width=10></td>
                    <td width=100></td>
                    <td class="cinza2"><font color=ff0000>*</font> <? global $key_registo4; echo $key_registo4;?></td>          
                </tr>
                <tr> 
                    <td width=10 height=5></td>
                    <td width=150></td>
                    <td></td>
                </tr>
                <? }
                else
                {?>
                <script language="JavaScript" type="text/JavaScript">
        		<!--
                function teste()
                {
                    return true;
                }
        		//-->
        		</script>
                <? }
				if($tipo==1)
				{
					echo '<script> function VerificaNR(event){
						var keyCode=event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
						var caract=new RegExp(/^[0-9\b]+$/i);
						var caract=caract.test(String.fromCharCode(keyCode));
						if(!caract){ /*alert("Caracter inválido: " +String.fromCharCode(keyCode));*/
						keyCode=0;
						return false;
						}
						}
						function popitup(url){
							newwindow=window.open(url,\'name\',\'height=200,width=550\');
							if(window.focus){newwindow.focus()}
							return false;
						}
						</script>
					<tr> 
                    <td width=10></td>
					<td width=100>Pagamento: </td>
                    <td class="cinza2">';
					echo 'N&ordm; do Cart&atilde;o: <input type="text" class="forms" value="" name="n_cartao" style="width:150px; height:17" maxlength="16" onKeyPress="return VerificaNR(event);"><br>';
					echo 'C&oacute;digo de Seguran&ccedil;a: <input type="text" class="inputbox2" value="" name="cod_seg" style="width:50px; height:17" maxlength="3" onKeypress="return VerificaNR(event);"> <font color="#FFCC33"> *</font> <a href="popup_ajuda.php" onClick="return popitup(\'pop_ajuda.php\')"><img src="templates/b_help.png" align="absmiddle" border=0 title="ajuda"></a><br>';
					echo 'Data de expira&ccedil;&atilde;o: <input type="text" name="data_exp" style="width:60px; height:17" class="inputbox2" value="Ano" maxlength="4" onKeyPress="return VerificaNR(event);" onFocus="document.form'.$id.'.data_exp.value=\'\';">  <input type="text" name="data_exp2" style="width:60px; height:17" class="inputbox2" value="Mes" maxlength="2" onKeyPress="return VerificaNR(event);"  onFocus="document.form'.$id.'.data_exp2.value=\'\';">';
					echo '</td>
					</tr>';
				}
				
                echo '
                <tr> 
                    <td width=10></td>
                    <td width=150></td>
                    <td align="left" class="titulo"><input type="submit" class=button value="'.$key_enviar.'"><!--<input type=image src="templates/button_enviar.gif">--></td>
                </tr>
            </table>
    </form>';
}
function tabela_ini($largura)
{	
	if($largura > 0) return '<table border=0 cellpadding=0 cellspacing=0><tr><td width='.$largura.'>';
	else return '<table border=0 cellpadding=0 cellspacing=0><tr><td>';
}
function tabela_fim(){	return '</td></tr></table>';}
function show_data1($m_data,$cod)
{
	$D=str2date($m_data);
	echo "<table width=170 border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td><select name=\"dia".$cod."\">";
	for($i=1;$i<32;$i++)
	{
		echo "<option value=\"";
		if($i<10) echo "0";
		echo "$i\"";
		if($D["mday"]==$i)	echo " selected";
		echo">";
		if($i<10) echo "0";echo "$i</option>\n";
	}
	echo "                    </select>\n";
	echo ".\n";
	echo "                    <select name=\"mes".$cod."\">\n";
	for($i=1;$i<13;$i++)
	{
		echo "<option value=\"";
		if($i<10) echo "0";
		echo "$i\"";
		if($D["mon"]==$i)  echo " selected";
		echo">";
		if($i<10) echo "0";
		echo "$i</option>\n";
	}
	echo "                    </select>\n";
	echo ".\n";
	echo "                    <select name=\"ano".$cod."\">";
	for($i=1900;$i<(date("Y")+5);$i++)
	{		
		echo "                      <option value=\"";
		if($i<10) echo "0";
		echo "$i\"";
		if($D["year"]==$i)
			echo " selected";
		echo ">$i</option>";
	}
	echo '</td></tr></table>';
}
function show_data2($id)
{

	echo "<select name=\"dia".$id."\">";
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
	echo "                    <select name=\"mes".$id."\">\n";
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
	echo "                    <select name=\"ano".$id."\">";
	for($i=1900;$i<(date("Y")+5);$i++)
	{		
		echo "                      <option value=\"";
		if($i<10) echo "0";
		echo "$i\"";
		if($D["year"]==$i)
			echo " selected";
		echo ">$i</option>\n";
	}
}

function GetRSRecordsCampos($id_pai=0,$campos="*")
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$Nome;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$condicao="where ID_ORG=$id_pai";
	$hRes=$obj->db_open($db_link,$DB_name,"Select $campos from $Nome $condicao order by Ordem ASC");
	return $obj;
}
function BuscarRegistoOpcoes($id_pai=0,$Tabela="form_options")
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$Nome_linguas;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;	
	$condicao="where ID_ORG=$id_pai";
	$hRes=$obj->db_open($db_link,$DB_name,"Select * from $Tabela $condicao");
	return $obj;
}
function ApagaCampo($id,$Tabela="form_options")
{	
	global $DB_host,$DB_user,$DB_pass,$DB_name,$Nome;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$res=Runquery($db_link,$DB_name,"delete from $Tabela where ID=$id");
	if(!$res)  echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}

function InsereCampo($id_org,$value,$Tabela="form_options")
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$Nome;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$value=str2SQLstr($value);
	$sql="insert into $Tabela (ID_ORG,Value) values ($id_org,$value)";
	$res=Runquery($db_link,$DB_name,$sql);
	if(!$res)  echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}

function guarda_bd_form($campo_id,$Tabela="form_records",$tipo=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$Nome;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	if($tipo==1)
	{
		// Pagamentos Visa
		global $n_cartao,$cod_seg,$data_exp,$data_exp2;
		$sql="insert into $Tabela (ID_ORG,n_cartao,cod_seg,data_exp,data_exp2) values ($campo_id,'$n_cartao','$cod_seg','$data_exp','$data_exp2')";
	}
	else $sql="insert into $Tabela (ID_ORG) values ($campo_id)";
	$res=Runquery($db_link,$DB_name,$sql);
	if(!$res) echo db_error($db_link);
	else
	{
	    $rec=new DataObj;
		$rec->db_open($db_link,$DB_name,"Select MAX(ID) from $Tabela");
		$codigo=$rec->db_data[0];
		return $codigo;
    }
	db_closedb_conection($db_link);
	return($res);
}

function guarda_bd_campos($campo_id,$campo,$valor,$Tabela="form_records_fields")
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$Nome;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$campo=str2SQLstr($campo);
	$valor=str2SQLstr($valor);
	$sql="insert into $Tabela (ID_ORG,Name,Valor) values ($campo_id,$campo,$valor)";
	$res=Runquery($db_link,$DB_name,$sql);
	if(!$res) echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}

function guarda_doc_bd_campos($campo_id,$campo,$img_path,$Img_peq1,$Tabela="form_records_fields")
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$Nome;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	if($Img_peq1["name"]) $altera_img=1;
	$campo=str2SQLstr($campo);
	if(!$altera_img)
	{
		$img_ext="";
		$img_ext=str2SQLstr($img_ext);
	}
	else
	{
		$pos=strrpos($Img_peq1["name"],".");
		$ext1=substr($Img_peq1["name"],$pos + 1,strlen($Img_peq1["name"]) + 1 - $pos);
		$img_ext=str2SQLstr($ext1);
	}
	$valor=$img_ext;
	$sql="insert into $Tabela (ID_ORG,Name,Valor) values ($campo_id,$campo,$valor)";
	$res=Runquery($db_link,$DB_name,$sql);
	if($res)
	{
		if($altera_img)
		{
			$rec=new DataObj;
			$rec->db_open($db_link,$DB_name,"Select MAX(ID) from $Tabela");
			copia_segura($Img_peq1["temp"],$img_path.$rec->db_data[0]."_1.".$ext1);	
		}
	}
	else echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}

function form_date($str_date)
{
	echo $str_date;
	/*
	Golbal $DB_host,$DB_user,$DB_pass, ,$DB_name, $Nome;
	$db_link = db_opendb_conection ($DB_host,$DB_user,$DB_pass);
	$sql = "INSERT INTO $Nome (ID_ORG, Date) VALUES ($str_date)";
	$res = Runquery($db_link,$DB_name,$sql);
	*/
}
?>