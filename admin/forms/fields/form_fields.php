<?
	include "settings.inc.php";
	if($task=="apagar"){$task_button="Delete";$task_form="apaga";}
	elseif($ID==0){$task_button="Insert";$task_form="insere";}
	else {$task_button="Change";$task_form="altera";} 
	$Name="";$Tipo="";$Values="";$Default="";
	// Talvez tenha de criar outra tabela
	if($ID!=0)
	{
		$rs=GetRSRecords2($ID);
		if($rs)
		{ 
			$campo_cod=$rs->db_data["ID"];
			$Ordem=$rs->db_data["Ordem"];
			$Name=stripslashes($rs->db_data["Name"]);
			$Name2=stripslashes($rs->db_data["Name2"]);
			$Tipo=$rs->db_data["Tipo"];
			$align=$rs->db_data["Align"];
			$Length=$rs->db_data["Length"];
			$Length2=$rs->db_data["Length2"];
			$legenda=$rs->db_data["legenda"];
			$Muda=$rs->db_data["Muda"];
			$herda=$rs->db_data["herda"];
			$Obrigatorio=$rs->db_data["Obrigatorio"];
			$Permissoes=$rs->db_data["Permissoes"];
			$Sequencial=$rs->db_data["Sequencial"];
			$altera_only=$rs->db_data["Altera_only"];
			$backoffice=$rs->db_data["Backoffice"];
			$id_campo=$rs->db_data["id_campo"];
			$id_form=$rs->db_data["id_form"];
			$matrix_type=$rs->db_data["matrix_type"];
			$row_labels=$rs->db_data["row_labels"];
			$col_labels=$rs->db_data["col_labels"];
			$formula=$rs->db_data["formula"];
			$full_line=$rs->db_data["Full_line"];
			$I1=$rs->db_data["img_ext"];
			$alinhar_botao=$rs->db_data["alinhar_botao"];
			$dentro=$rs->db_data["dentro"];
			$linhas=$rs->db_data["linhas"];
			$altura=$rs->db_data["altura"];
		}
		$rs->db_close(1);
	}	
	include "../../layout/header.inc.php";	
	include "../../layout/body.inc.php";
	
	global $DB_host,$DB_user,$DB_pass,$DB_name,$Nome_linguas,$admin_lang;
?>
<script language='Javascript'>
	function change_tipo(targ,selObj)
	{
		 var tipo=selObj.options[selObj.selectedIndex].value;
		 if(tipo==33){document.getElementById('imagem_bt1').style.display="block"; document.getElementById('imagem_bt2').style.display="block";}
		 else{document.getElementById('imagem_bt1').style.display="none";document.getElementById('imagem_bt2').style.display="none";}
		 if(tipo==2){document.getElementById('row_bt1').style.display="block"; document.getElementById('row_bt2').style.display="block";}
		 else{document.getElementById('row_bt1').style.display="none";document.getElementById('row_bt2').style.display="none";}
		 if(tipo==5 || tipo==6){<? if($campo_cod){?>document.form.length.disabled=true;document.form.campo.disabled=false;<? }?>}
		 else{<? if($campo_cod){?>document.form.length.disabled=false;document.form.campo.disabled=true;<? }?>}
	}
	function inserir_campo(){open("novo_campo.php?ID=<?=$ID?>&value=" + form.campo.value + "&ordem=" + form.campo_ordem.value + "&ID_ORG2=<?=$campo_cod?>&ID_ORG=<?=$ID_ORG?>&email=" + form.campo_email.value+"&associate_page=" + form.associate_page.value,"_self");}
	function apagar_seleccao(){open("apagar_campo.php?ID=<?=$ID?>&ID_ORG2=<?=$campo_cod?>&ID_ORG=<?=$ID_ORG?>&ID_Campo=" + form.opcoes[form.opcoes.selectedIndex].value,"_self");}
	function editar_seleccao(){open("form_fields.php?ID=<?=$ID?>&ID_ORG2=<?=$campo_cod?>&ID_ORG=<?=$ID_ORG?>&op="+form.opcoes[form.opcoes.selectedIndex].value,"_self");}
	function alterar_campo(){open("altera_campo.php?op=<?=$op?>&value=" + form.campo.value + "&ordem=" + form.campo_ordem.value + "&ID=<?=$ID?>&ID_ORG2=<?=$campo_cod?>&ID_ORG=<?=$ID_ORG?>&email=" + form.campo_email.value+"&associate_page=" + form.associate_page.value,"_self");}
	
</script>
<div class="center">
	<form name="form" method="post" action="pagina_trata.php?ID=<?=$ID;?>&ID_ORG=<?=$ID_ORG?>&task_form=<?=$task_form?>&dump=<?=uniqID("")?>" enctype="multipart/form-data">
		<input type="hidden" name="slashes" value="'">
		<table width="100%" class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
        <tr>
            <td valign="top" align="left"><? include "../../layout/menus.inc.php"; ?>
                <table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
                <tr>
                    <td valign="top">
                        <table width=100% border=0 cellpadding=2 cellspacing=0 bgcolor="#F6F6F6" class=quadros>                        
                        <tr> 
                            <td>&nbsp;</td>
                            <td class=preto align=left>Order:</td>
                            <td align=left class="preto"><input name=ordem style="WIDTH: 40px; HEIGHT: 20px" maxlength=3 value="<? echo $Ordem;?>">
                            Required field:
                            <select name=obrigatorio><option value="0"<? if($Obrigatorio=="0") echo " selected";?>>NO</option><option value="1"<? if($Obrigatorio=="1") echo " selected";?>>YES</option></select> 
                            </td>
                        </tr><tr> 
                            <td>&nbsp;</td>
                            <td class=preto align=left>Field Type:</td>
                            <td align=left class=preto><select name=tipo onChange="change_tipo('parent',this);"><? foreach($tipos as $valor=>$tipo_d){echo "<option value='$valor' ";if($valor==$Tipo) echo "SELECTED";echo ">$tipo_d</option>";}?></select></td>
                        </tr>
                        <tr> 
                            <td>&nbsp;</td>
                            <td class=preto align=left>Name / Label:</td>
                            <td align=left class=preto><input name=name style="WIDTH: 300px; HEIGHT: 20px" maxlength=255 value="<? echo $Name;?>"></td>
                        </tr>
                        <tr> 
                            <td>&nbsp;</td>
                            <td class=preto align=left>Label inside the box:</td>
                            <td align=left class=preto><select name="dentro"><option value="0" <? if($dentro=="0" || !$dentro){ echo " selected";}?>>NO</option><option value="1" <? if($dentro=="1"){ echo " selected";}?>>YES</option></select> &nbsp;(only valid on text fields)</td>
                        </tr>
                        <tr> 
                            <td>&nbsp;</td>
                            <td class=preto align=left>Has break line:</td>
                            <td align=left class=preto><select name=muda><option value="0"<? if($Muda=="0") echo " selected";?>>YES</option><option value="1"<? if($Muda=="1") echo " selected";?>>NO</option></select></td>
                        </tr>
                        <? if($Tipo=="0"){ // Legenda?>
                        <tr> 
                            <td>&nbsp;</td>
                            <td class=preto align=left>Alternative Label:</td>
                            <td align=left class=preto><textarea name=name2 style="WIDTH: 100%; HEIGHT: 150px"><? echo $Name2;?></textarea></td>
                        </tr>
                          <? } if($Tipo!="5" && $Tipo!="6"){ // Radiobottom ou Checkbox?>
                        <tr> 
                            <td>&nbsp;</td>
                            <td class=preto align=left>Total of characters: </td>
                            <td align=left class=preto> <input <? if($Tipo==5 || $Tipo=="6") echo "disabled"?> name="length" style="WIDTH: 40px; HEIGHT: 20px" maxlength="3" value="<? echo $Length;?>">
                              Field width:<input name=length2 style="WIDTH: 40px; HEIGHT: 20px" maxlength=3 value="<? echo $Length2;?>">(in pixels)</td>
                        </tr><? }?>
                        <tr> 
                            <td>&nbsp;</td>
                            <td class=preto align=left>Show help text:</td>
                            <td align=left><input name=legenda style="WIDTH:300px; HEIGHT: 20px" maxlength=254 value="<? echo $legenda;?>"></td>
                        </tr>
                          <? 
						 // não está em uso mas pode ser util mais tarde
						 /* 
                        if($Tipo==14 || $Tipo==15)
                        {
                        ?>
                        <tr> 
                            <td height=10 width=10></td>
                            <td width=150 class=preto>ID do Formulário:</td>
                            <td><input name=id_form style="WIDTH: 250px; HEIGHT: 20px" maxlength=255 value="<? echo $id_form;?>"></td>
                        </tr><? 
						if($Tipo==14){?>
                        <tr> 
                            <td height=10 width=10></td>
                            <td width=150 class=preto>ID do Campo:</td>
                            <td><input name=id_campo style="WIDTH: 250px; HEIGHT: 20px" maxlength=255 value="<? echo $id_campo;?>"></td>
                        </tr><?
						}
                        }
                        if($Tipo==16)
                        {
                        ?>
                        <tr id="row_matrix_type">
                            <td>&nbsp;</td>
                            <td width="150" class="preto" align="left" valign="top">Dados p/ matriz:</td>
                            <td align="left" valign="top" class="preto"><select name="matrix_type"><option value="0"<? if($matrix_type=="0") echo " selected"; ?>>Check-Box</option><option value="1"<? if($matrix_type=="1") echo " selected"; ?>>Texto</option></select></td>
                            <td width="4"></td>
                        </tr>
                        <tr><td colspan=4 height=1></td></tr>
                        <tr id="row_matrix_row">
                            <td>&nbsp;</td>
                            <td width="150" class="preto" align="left" valign="top">Linhas (separar com <b><i>;</i></b>):</td>
                            <td align="left" valign="top" class="preto"><input name="row_labels" style="width: 100%; height: 20px" maxlength="254" value="<? echo $row_labels; ?>"></td>
                            <td width="4"></td>
                        </tr>
                        <tr id="row_matrix_col">
                            <td>&nbsp;</td>
                            <td width="150" class="preto" align="left" valign="top">Colunas (separar com <b><i>;</i></b>):</td>
                            <td align="left" valign="top" class="preto">
                              <input name="col_labels" style="width: 100%; height: 20px" maxlength="254" value="<? echo $col_labels; ?>"><br>
                              Para se definir os campos deve fazer-se xxxxx[13,0,0]<br>
                               Tipos: <br>
                               <!--1 - Texto<br>
                               2 - Texto Longo<br>
                               3 - Numérico<br>
                               4 - Password<br>
                               5 - Radiobottom<br>
                               6 - Checkbox<br>
                               7 - ComboBox<br>
                               8 - List Box<br>
                               9 - Ficheiro<br>
                               10 - Data<br>
                               11 - Visivel<br>
                               12 - Uso Interno<br>-->
                               14 - Dados de outro formulário</td>
                          <td width="4"></td>
                          </tr>
                          <? }
						*/
                        if($campo_cod && $Tipo>4 && $Tipo<9)
                        {
							$valor="";$ordem="";
							if($op)
							{
								
								$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
								$rs2=new DataObj;
								$sql="SELECT * FROM form_options where ID=$op";
								$hRes=$rs2->db_open($db_link,$DB_name,$sql);
								if(!$rs2->EOF())
								{
									$ordem=$rs2->db_data["Ordem"];
									$valor=$rs2->db_data["Value"];
									$email=$rs2->db_data["Email"];
									$Associate_page=$rs2->db_data["Associate_page"];
								}
								$rs2->db_close(1);
							}
                        ?>
                        <tr>
                        	<td height=10 width=10></td>
                            <td width=150 class=preto></td>
                            <td class=preto>Fields:</td>
                        </tr>
                        <tr>
                       	  <td height=10 width=10></td>
                            <td width=150 align="right" class=preto>Order:</td>
                          <td class=preto><input name=campo_ordem value="<?=$ordem?>" style="WIDTH: 50px; HEIGHT: 20px" <? if($Tipo<>"5" && $Tipo<>"6" && $Tipo<>"7" && $Tipo<>"8") echo "disabled"?>></td>
                        </tr>
                        <tr>
                       	  <td height=10 width=10></td>
                            <td width=150 align="right" class=preto>Name:</td>
                            <td class=preto><input name=campo value="<?=$valor?>" style="WIDTH: 300px; HEIGHT: 20px" maxlength=128 <? if($Tipo<>"5" && $Tipo<>"6" && $Tipo<>"7" && $Tipo<>"8") echo "disabled"?>>
                        </tr>
						<tr>
                       	  <td height=10 width=10></td>
                            <td width=150 align="right" class=preto>Email:</td>
                            <td class=preto><input name=campo_email value="<?=$email?>" style="WIDTH: 300px; HEIGHT: 20px" maxlength=128 <? if($Tipo<>"5" && $Tipo<>"6" && $Tipo<>"7" && $Tipo<>"8") echo "disabled"?>>
                          <? if($op){?><input type="hidden" name="op" value="<?=$op?>" />&nbsp;<?}else{?>&nbsp;<? }?></td>
                        </tr>
						<tr>
                       	  <td height=10 width=10></td>
                            <td width=150 align="right" class=preto>Page:</td>
                            <td class=preto><input name=associate_page value="<?=$Associate_page?>" style="WIDTH: 300px; HEIGHT: 20px" maxlength=128 <? if($Tipo<>"5" && $Tipo<>"6" && $Tipo<>"7" && $Tipo<>"8") echo "disabled"?>>
                          <? if($op){?><input type="hidden" name="op" value="<?=$op?>" />&nbsp;<input type=button name=inserir value="<< Save Changes" onmouseup=javascript:alterar_campo();><?}else{?>&nbsp;<input type=button name=inserir value="<< Add fields to the list" onmouseup=javascript:inserir_campo();><? }?></td>
                        </tr>
                        <tr>
                            <td height=10 width=10></td>
                            <td width=150 align="right">
                            	<a class="italico" href="javascript:editar_seleccao();">Edit Selected >></a><br /><br />
								<a class="italico" href="javascript:apagar_seleccao();">Remove Selected >></a>
                            </td>
                            <td><? 
							echo "<select name='opcoes' size='5' style='width:300px; height:150px'>";
							if($campo_cod!=0)
							{
								$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
								$hRes=$rs->db_open($db_link,$DB_name,"Select * from form_options where ID_ORG=$ID Order by ORDEM asc");
								while(!$rs->EOF())
								{
									$value=$rs->db_data["Value"];
									$ID_Campo=$rs->db_data["ID"];
									$selected=$rs->db_data["Selected"];
									$ordem=$rs->db_data["Ordem"];
									echo "<option ";
									if($selected) echo "selected ";
									echo 'value="'.$ID_Campo.'">'.$ordem." - ".nl2br(htmlentities_b($value)).'</option>';
									$rs->db_next();
								}
								$rs->db_close(1);
							}
							echo "</select>";?>
                            </td>
                        </tr><?
						if($Tipo=="5" || $Tipo=="6")
						{?>
                        <tr>
                        	<td height=10 width=10></td>
                            <td width=150 class=preto>Alignment of fields:</td>
                            <td><SELECT NAME=align>
                                	<option value="1"<? if($align=="1") echo " selected";?>>Horizontal text to the left</option>
                                    <option value="2"<? if($align=="2") echo " selected";?>>Horizontal and text on right</option>
                                    <option value="3"<? if($align=="3") echo " selected";?>>Vertical text to the left</option>
                                    <option value="4"<? if($align=="4") echo " selected";?>>Vertical text to the right</option>
                                </SELECT>
                            </td>
                        </tr>
                        <tr> 
                            <td>&nbsp;</td>
                            <td class=preto align=left>Occupy entire line?:</td>
                            <td align=left class=preto><select name=full_line><option value="0"<? if($full_line=="0") echo " selected";?>>NO</option><option value="1"<? if($full_line=="1") echo " selected";?>>YES</option></select>
                            Alternative label:<input name=name2 style="WIDTH: 400px; HEIGHT: 20px" maxlength=255 value="<? echo $Name2;?>">(appears above options)</td>
                        </tr>
                        <tr>
                        	<td height=10 width=10></td>
                            <td width=150>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr><?
						}
                        }
						//Campo de calculo
						if($Tipo==30){?><tr><td height=10 width=10></td><td width=150 class=preto>Formula:</td><td><input name=formula style="WIDTH: 250px; HEIGHT: 20px" maxlength=255 value="<? echo $formula;?>"></td><td width=4></td></tr><? }?>
                        <tr>
                        	<td height=10 width=10></td>
                            <td width=150></td>
                            <td></td>
                        </tr>
                        <tr> 
                            <td height=10 width=10></td>
                            <td width=150 valign="top" class="preto"><div style="<? if($Tipo==33){ ?>display:block<? }else{?>display:none<? }?>" id="imagem_bt1">Button image</div><div style="<? if($Tipo==2){ ?>display:block<? }else{?>display:none<? }?>" id="row_bt1">Height</div></td>
                            <td class="preto">
                            <div style="<? if($Tipo==2){ ?>display:block<? }else{?>display:none<? }?>" id="row_bt2"><input name="linhas" type="hidden" style="WIDTH:50px; HEIGHT: 20px" maxlength=255 value="<? echo $linhas;?>"> <input name="altura" style="WIDTH:50px; HEIGHT: 20px" maxlength=255 value="<? echo $altura;?>">
                            <br>
                            </div>
                            <div style="<? if($Tipo==33){ ?>display:block<? }else{?>display:none<? }?>" id="imagem_bt2"><?
                            if($I1!="")
							{?><img src ="<?="../../../files/forms/$ID"."_1.".$I1;?>" /></a><br><?
							if($task!="apagar"){?><br>Delete <input type='checkbox' name='Apagar_img1' value='1'> or Change <input type='file' name='img_peq1'> <input type='hidden' name='tem_foto1' value='1'><? }
                            }
                            else{?>New: <input type=file name='img_peq1'> <input type='hidden' name='tem_foto1' value='0'><? }?>
                            <br>Align Button <select name="alinhar_botao"><option value="left" <? if($alinhar_botao=="left"){?> selected<? }?>>Left</option><option value="center" <? if($alinhar_botao=="center"){?> selected<? }?>>Center</option><option value="right" <? if($alinhar_botao=="right"){?> selected<? }?>>Right</option></select>
                            <br><br>Note: The button will always appear beneath all fields, the field order is irrelevant
                            </div>
                            </td>
                        </tr>
                        </table>
                    </td>
                </tr>
                </table><? show_page_buttons();?>
            </td>
				
        </tr>
        </table>
	</form>
</div>
<? 	include "../../layout/bottom.inc.php";?>