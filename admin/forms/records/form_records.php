<? 
	include "settings.inc.php";
	 
	if($task=="apagar"){$task_button="Apagar";$task_form="apaga";} 
	elseif($ID==0){$task_button="Inserir";$task_form="insere";} 
	else {$task_button="Alterar";$task_form="altera";}  
	$Name="";$Tipo="";$Values="";$Default=""; 
	// Talvez tenha de criar outra tabela 
	
	if($ID_From!=0)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name; 
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass); 
		$rs=new DataObj;
		$sql="Select * from form_records where ID_From=$ID_From"; 
		$hRes=$rs->db_open($db_link,$DB_name,$sql); 
		if(!$rs->EOF())
		{
			$ID=$rs->db_data["ID"];
			$task_button="Alterar";
			$task_form="altera";
			$rs->db_close();
		}
	}
	include "../../layout/header.inc.php";	
	include "../../layout/body.inc.php";
?> 
<div class="center">
<form name="form" method="post" action="form_records_trata.php?ID=<?=$ID;?>&ID_ORG=<?=$ID_ORG;?>&task_form=<?=$task_form?>&dump=<?=uniqID("")?>" enctype="multipart/form-data">
	<input type="hidden" name="slashes" value="'">
    <table align="center" border=0 cellpadding=0 cellspacing=0>
    <tr>
        <td valign="top"><? include "../../layout/menus.inc.php"; ?>
            <table class="new_quadros" width="880px" border=0 cellspacing=0 cellpadding="2" class="text-center">
            <tr>
                <td valign="top" align="left">
                	
                    <table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="#F6F6F6" class="border1">
                    <tr> 
                        <td valign="top">
                        <? 
						if($ID_From!=0) echo '<input type=hidden name=ID_From value='.$ID_From.'>';
						if($ID!=0)
						{
							global $DB_host,$DB_user,$DB_pass,$DB_name; 
							$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass); 
							$rs=new DataObj;
							$sql="Select * FROM form_records where ID=".$ID; 
							$hRes=$rs->db_open($db_link,$DB_name,$sql); 
							if(!$rs->EOF())
							{
								$n_cartao=$rs->db_data["n_cartao"];
								$cod_seg=$rs->db_data["cod_seg"];
								$data_exp=$rs->db_data["data_exp"];
								$data_exp2=$rs->db_data["data_exp2"];
								$sql="Select * FROM form_records where ID=".$rs->db_data["ID_From"]; 
								$rs2=new DataObj;
								$hRes=$rs2->db_open($db_link,$DB_name,$sql); 
								if(!$rs2->EOF())
								{	
									echo '<input type=hidden name=volta2 value='.$rs2->db_data["ID_ORG"].'>';
									$rs2->db_close();
								}
								$rs->db_close();
							}
						} 
						$sql="Select * FROM form_fields where ID_ORG=".$ID_ORG. " and Tipo!=33 order by Ordem";  //todos os campos menos o campo de botao
						global $DB_host,$DB_user,$DB_pass,$DB_name; 
						$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass); 
						$rs=new DataObj;
						if($ID!=0 || $ID_From!=0) $rs2=new DataObj; 
						$hRes=$rs->db_open($db_link,$DB_name,$sql); 
						echo '<table width=100% class=quadros height=22 border=0 cellpadding=2 cellspacing=0>'; 
						$start_linha=1;
						$obrigatorio=0;
						while(!$rs->EOF())
						{
							$cod=$rs->db_data["ID"]; 
							$Name=$rs->db_data["Name"]; 
							$tipo=$rs->db_data["Tipo"]; 
							$Length=$rs->db_data["Length"];
							$Length2=$rs->db_data["Length2"];
							$Align=$rs->db_data["Align"];
							$Obrigatorio=$rs->db_data["Obrigatorio"];
							$id_form=$rs->db_data["id_form"];
							$id_campo=$rs->db_data["id_campo"];
							if($ID!=0)
							{
								$sql="Select * FROM form_records_fields where ID_ORG=".$ID. " and Name=$cod"; 
								$hRes=$rs2->db_open($db_link,$DB_name,$sql); 
								$Valor=$rs2->db_data["Valor"];
								$id_img=$rs2->db_data["ID"];
								$rs2->db_close();
							}
							else
							{
								if($ID_From!=0)
								{
									$sql="Select Valor,Name,form_records_fields.ID as ID FROM form_records_fields inner join form_records on form_records.ID=form_records_fields.ID_ORG where ID_From=".$ID_From. " and Name=$cod"; 
									$hRes=$rs2->db_open($db_link,$DB_name,$sql); 
									if(!$rs2->EOF())
									{
										$Valor=$rs2->db_data["Valor"];
										$rs2->db_close();
									}
									else $Valor="";
								}
							}
							if(!$id_campo) $id_campo=0;
							if($start_linha)
							{
								echo '<tr><td width=10></td>
								<td width=150 class=preto>';
								if($tipo!=15) echo stripslashes($Name);
								echo '</td><td class="preto"';
								if($rs->db_data["Align"]==1 || $rs->db_data["Align"]==3) echo "align=left";
								echo '>';
								$start_linha=0;
							}
							elseif($tipo!=15) echo stripslashes($Name)." ";
							if($Length2) $largura=$Length2."px";
							else $largura=($Length*100/254)."%";
							if($largura < 30) $largura=30;
							/****************************** INICIO LER DADOS DE Outra tabela **************************************************/
							if($rs->db_data["herda"]==1 && !$Valor)
							{
								$sql="Select * from form_fields
								inner join form_records on form_records.ID_ORG=form_fields.ID_ORG 
								inner join form_records_fields on form_records_fields.Name=form_fields.ID 
								where form_records.ID=".$ID_From." and 
								form_records_fields.ID_ORG=".$ID_From." and form_fields.Name Like '".$rs->db_data["Name"]."'";
								$rs2=new DataObj;	
								$hRes=$rs2->db_open($db_link,$DB_name,$sql);
								if(!$rs2->EOF()) $Valor=$rs2->db_data["Valor"];
								$rs2->db_close();	
							}
							/********************************************************************************/
							if($tipo==1) echo '<input class=forms name="field'.$cod.'" style="WIDTH: '.$largura.'; HEIGHT: 20px" maxlength="'.$Length.'" value="'.$Valor.'">';
							if($tipo==2) echo '<textarea class=forms name="field'.$cod.'" style="WIDTH: 100%; HEIGHT: 100px;">'.$Valor.'</textarea>';
							if($tipo==3) echo '<input class=forms name="field'.$cod.'" style="WIDTH: '.$largura.'; HEIGHT: 20px" maxlength="'.$Length.'" value="'.$Valor.'">';
							if($tipo==30) echo '<input readonly="" class=forms name="field'.$cod.'" style="WIDTH: '.$largura.'; HEIGHT: 20px" maxlength="'.$Length.'" value="'.$Valor.'">';
							if($tipo==4) echo '<input class=forms name="field'.$cod.'" style="WIDTH: '.$largura.'; HEIGHT: 20px" maxlength="'.$Length.'" value="'.$Valor.'">';
							if($tipo==9 && $ID!=0 && $Valor) echo '<a href="../../../files/forms/'.$id_img.'_1.'.$Valor.'" target=_new>Documento ('.$Valor.')</a>';
							elseif($tipo==9) echo '<input type=file class=forms name="field'.$cod.'" style="HEIGHT: 20px">';
							if($tipo==31) echo '<input name="field'.$cod.'" class=forms name="Email" style="WIDTH: '.$largura.'; HEIGHT: 20px" maxlength="'.$Length.'" value="'.$Valor.'">';
							if($rs->db_data["Tipo"]==32) 
							{
								global $ID,$nome_temp;
								echo '<input type = "text" name = "Product" value = "'.$Valor.'" style="WIDTH: '.$largura.'">';				
							}
							if($tipo==10){echo show_data($Valor,$cod);}
							if($tipo==11) 
							{
								if($Valor==1) echo '<INPUT TYPE=checkbox NAME="field'.$cod.'" value="1" checked>'.$rs2->db_data["Value"];
								else echo '<INPUT TYPE=checkbox NAME="field'.$cod.'" value="1">'.$rs2->db_data["Value"];
							}
							if($tipo==12) echo $Valor.'<input type=hidden name="field'.$cod.'" value="'.$Valor.'">';
							if($tipo==13){echo show_data($Valor,$cod);}
							if($tipo==14)
							{
								$sql="Select * from form_records_fields where Name=".$id_campo;
								$obj=new DataObj;
								$hRes=$obj->db_open($db_link,$DB_name,$sql);
								if(!$obj->EOF())
								{
									echo '<select name="field'.$cod.'">';
									
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
							}
							if($tipo==15 && $task_form=="altera"){echo '<INPUT TYPE=button NAME="field'.$cod.'" value="'.$Name.'" onclick=\'javascript:window.open("form_records.php?ID=0&ID_ORG='.$id_form.'&ID_From='.$ID.'","_self")\'>';}
							if($rs->db_data["Tipo"]==16) 
							{
								$rs3=new DataObj;
								$rows=explode(";",$rs->db_data["row_labels"]);
								$cols=explode(";",$rs->db_data["col_labels"]);
								$col_width=90/sizeof($cols);      
								echo '<br>';
								echo '<table width=100% class="border1" border=0 cellpadding=2 cellspacing=0>';
								echo '<tr>';
								echo '<td width=10%>&nbsp;</td>';
								for($k=0;$k<sizeof($cols);$k++) 
								{	
									$m_titulo=$cols[$k];	
									$xpos1=strpos($m_titulo,'[');
									$xpos2=strpos($m_titulo,']');
									if($xpos1 && $xpos2)	$m_titulo=substr($m_titulo,0,$xpos1);
									echo '<td width='.$colwidth.'% align=center>'.$m_titulo.'</td>';
								}
								echo '</tr>';
								for($k=0;$k<sizeof($rows);$k++)
								{
									echo '<tr>';
									echo '<td width=10%>'.$rows[$k].'</td>';
									for($n=0;$n<sizeof($cols);$n++)
									{
										$resp=(($k+1)*100)+($n+1);
										$sql3='select * from form_records_fields where Name="'.$cod.'_r'.($k+1).'_c'.($n+1).'" and ID_ORG='.$ID;
										$hRes=$rs3->db_open($db_link,$DB_name,$sql3);			
										echo '<td width='.$colwidth.'% align=center>';            
										if($rs->db_data["matrix_type"]==0)
										{
											echo '<input type="checkbox" name="field'.$cod.'_r'.($k+1).'_c'.($n+1).'" value="x"';
											if(!$rs3->EOF() && $rs3->db_data["Valor"]=="x") echo " checked";
											echo '>';
										}
										else
										{
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
													if(!$rs3->EOF() && $rs3->db_data["Valor"]<>"") $Valor=$rs3->db_data["Valor"];
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
												if(!$rs3->EOF() && $rs3->db_data["Valor"]<>"") echo ' value="'.$rs3->db_data["Valor"].'"';
												echo '>';
											}
										}
										echo '</td>';
									}
									echo '</tr>';
								}
								echo '</table>';
							}
							if($tipo==17)
							{
								// Se novo
								if(!$ID)
								{
									echo $Valor.'(numeração automática)<input type=hidden name="field'.$cod.'" value="'.$Valor.'">';
								}
								else echo $Valor.'<input type=hidden name="field'.$cod.'" value="'.$Valor.'">';
							}
							if($tipo==18)
							{
								// VALORES DE OUTRO FORMULARIO -14
								/*************************************************************************************/
								$sql="Select * from logins order by username";
								$obj=new DataObj;
								$hRes=$obj->db_open($db_link,$DB_name,$sql);
								if(!$obj->EOF())
								{
									echo '<select name="field'.$cod.'">';
									echo '<OPTION value="0"';
									if(!$Valor) echo " selected";
									echo '>----</OPTION>';
									while(!$obj->EOF())
									{			
										echo '<OPTION value="'.$obj->db_data["ID"].'"';
										if($obj->db_data["ID"]==$Valor) echo " selected";
										echo '>'.$obj->db_data["username"].'</OPTION>';
										if($Obrigatorio==1){echo '<font color=ff0000> *</font>';$obrigatorio=1;}
										$obj->db_next();
									}
									$obj->db_close();
									echo '</select>';
								}
								/*************************************************************************************/
							}
							if($tipo<5 && $Obrigatorio==1){echo '<font color=ff0000> *</font>';$obrigatorio=1;}
							if($tipo==5)
							{
								$rs2=BuscarRegistoOpcoes($rs->db_data["ID"],"form_options");						
								while(!$rs2->EOF())
								{
									if($Align==1)
									{
										if($Valor==$rs2->db_data["Value"])  echo $rs2->db_data["Value"].'<INPUT TYPE=radio NAME="field'.$cod.'" value="'.$rs2->db_data["Value"].'" checked>';
										else echo $rs2->db_data["Value"].'<INPUT TYPE=radio NAME="field'.$cod.'" value="'.$rs2->db_data["Value"].'">';
									}
									if($Align==2) 
									{
										if($Valor==$rs2->db_data["Value"])  echo '<INPUT TYPE=radio NAME="field'.$cod.'" value="'.$rs2->db_data["Value"].'" checked>'.$rs2->db_data["Value"];
										else echo '<INPUT TYPE=radio NAME="field'.$cod.'" value="'.$rs2->db_data["Value"].'">'.$rs2->db_data["Value"];
									}
									if($Align==3) 
									{
										if($Valor==$rs2->db_data["Value"]) echo $rs2->db_data["Value"].'<INPUT TYPE=radio NAME="field'.$cod.'" value="'.$rs2->db_data["Value"].'" checked><br>';
										else echo $rs2->db_data["Value"].'<INPUT TYPE=radio NAME="field'.$cod.'" value="'.$rs2->db_data["Value"].'"><br>';
									}
									if($Align==4) 
									{
										if($Valor==$rs2->db_data["Value"]) echo '<INPUT TYPE=radio NAME="field'.$cod.'" value="'.$rs2->db_data["Value"].'" checked>'.$rs2->db_data["Value"].'<br>';
										else echo '<INPUT TYPE=radio NAME="field'.$cod.'" value="'.$rs2->db_data["Value"].'">'.$rs2->db_data["Value"].'<br>';
									}
									$rs2->db_next();
								}
								if($Obrigatorio==1){echo '<font color=ff0000> *</font>';$obrigatorio=1;}
								echo "<br>";
								$rs2->db_close();
							}
							if($tipo==6) 
							{
								$rs2=BuscarRegistoOpcoes($rs->db_data["ID"],"form_options");
								$i=0;
								while(!$rs2->EOF())
								{
									$m_valor=$rs2->db_data["Value"];
									if($Align==1)
									{ 
										if(strstr($Valor,$rs2->db_data["Value"])) echo $rs2->db_data["Value"].'<INPUT TYPE=checkbox NAME="field'.$cod.'_'.$i.'" value="'.$rs2->db_data["Value"].'" checked>';
										else echo $rs2->db_data["Value"].'<INPUT TYPE=checkbox NAME="field'.$cod.'_'.$i.'" value="'.$rs2->db_data["Value"].'">';
									}
									if($Align==2) 
									{
										if(strstr($Valor,$rs2->db_data["Value"])) echo '<INPUT TYPE=checkbox NAME="field'.$cod.'_'.$i.'" value="'.$rs2->db_data["Value"].'" checked>'.$rs2->db_data["Value"];
										else echo '<INPUT TYPE=checkbox NAME="field'.$cod.'_'.$i.'" value="'.$rs2->db_data["Value"].'">'.$rs2->db_data["Value"];
									}
									if($Align==3) 
									{
										if(strstr($Valor,$rs2->db_data["Value"])) echo $rs2->db_data["Value"].'<INPUT TYPE=checkbox NAME="field'.$cod.'_'.$i.'" value="'.$rs2->db_data["Value"].'" checked><br>';
										else echo $rs2->db_data["Value"].'<INPUT TYPE=checkbox NAME="field'.$cod.'_'.$i.'" value="'.$rs2->db_data["Value"].'"><br>';
									}
									if($Align==4) 
									{
										if(strstr($Valor,$rs2->db_data["Value"])) echo '<INPUT TYPE=checkbox NAME="field'.$cod.'_'.$i.'" value="'.$rs2->db_data["Value"].'" checked>'.$rs2->db_data["Value"].'<br>';
										else echo '<INPUT TYPE=checkbox NAME="field'.$cod.'_'.$i.'" value="'.$rs2->db_data["Value"].'">'.$rs2->db_data["Value"].'<br>';
									}
									$rs2->db_next();
									$i++;
								}
								if($Obrigatorio==1){echo '<font color=ff0000> *</font>';$obrigatorio=1;}
								echo '<input type="hidden" name="field'.$cod.'_counter" value="'.$i.'">';
								echo "<br>";
								$rs2->db_close();
							}
							if($tipo==7) 
							{
								echo '<select name="field'.$cod.'">';
								$rs2=BuscarRegistoOpcoes($rs->db_data["ID"],"form_options");
								while(!$rs2->EOF())
								{
									echo '<OPTION value="'.$rs2->db_data["Value"].'"';
									if($Valor==$rs2->db_data["Value"]) echo ' selected';
									echo '>'.$rs2->db_data["Value"].'</OPTION>';
									$rs2->db_next();
								}
								if($Obrigatorio==1){echo '<font color=ff0000> *</font>';$obrigatorio=1;}
								$rs2->db_close();
								echo '</select>';
							}

							if($tipo==8) 
							{
								echo '<select name="field'.$cod.'" size=5>';
								$rs2=BuscarRegistoOpcoes($rs->db_data["ID"],"form_options");
								while(!$rs2->EOF())
								{
									echo '<OPTION value="'.$rs2->db_data["Value"].'">'.$rs2->db_data["Value"].'</OPTION>';
									$rs2->db_next();
								}
								$rs2->db_close();
								echo '</select>';
								if($Obrigatorio==1){echo '<font color=ff0000> *</font>';$obrigatorio=1;}
							}
							if($rs->db_data["Muda"]==0)
							{
								$start_linha=1;
								echo '</td></tr>';
							}
							else echo "&nbsp;&nbsp;";
							$rs->db_next();
						}
						if($start_linha)echo '</td></tr>';
						if($obrigatorio)
						{?><tr><td width=10></td><td width=100></td><td class=preto><font color=ff0000>*</font> Campos de preenchimento obrigatório</td></tr><? }
						if($n_cartao)
						{
							echo '<tr>
								<td width=10></td>
								<td width=100>Pagamento: </td>
								<td class="preto">';
								echo 'N&ordm; do Cart&atilde;o: <input type="text" class="forms" value="'.$n_cartao.'" name="n_cartao" style="width:150px; height:17" maxlength="16"><br>';
								echo 'C&oacute;digo de Seguran&ccedil;a: <input type="text" class="inputbox2" value="'.$cod_seg.'" name="cod_seg" style="width:50px; height:17" maxlength="3"><br>';
								echo 'Data de expira&ccedil;&atilde;o: <input type="text" name="data_exp" style="width:60px; height:17" class="inputbox2" value="'.$data_exp.'" maxlength="4">  <input type="text" name="data_exp2" style="width:60px; height:17" class="inputbox2" value="'.$data_exp2.'" maxlength="2">';
								echo '</td>
								</tr>';
						}
						echo '</table>';
						$rs->db_close();
						?>
					
	            	</td></tr></table>
                </td>
            </tr>
            </table><? show_page_buttons();?>
        </td>
            <td width="10px">&nbsp;</td>
            <td valign="top" width="200">&nbsp;</td>
    </tr>
    </table>
</form>
</div>
<? 	include "../layout/bottom.inc.php";?>
<?
function show_data($m_data,$cod)
{
	$D=str2date($m_data);
	echo "<select name=\"dia".$cod."\">";
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