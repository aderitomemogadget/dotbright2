<?
if($d==1){?><table border="0" cellspacing="10" cellpadding="0">
<tr>
  <td>desde</td>
  <td><input style="width:70px; font-size:10px" name="f_data1" value="<?=$f_data1;?>" readonly="true">
	at&eacute;</td>
  <td><input style="width:70px; font-size:10px" name="f_data2" value="<?=$f_data2;?>" readonly="true"></td>
</tr>
</table>
<br>
<input name="grafico" type="radio" value="1" <? if($grafico==1) echo "checked";?>>&nbsp;Gr&aacute;fico di&aacute;rio do mes: <select name="mes">
<option value="1" <? if($mes==1) echo "selected";?>>Janeiro</option>
<option value="2" <? if($mes==2) echo "selected";?>>Fevereiro</option>
<option value="3" <? if($mes==3) echo "selected";?>>Março</option>
<option value="4" <? if($mes==4) echo "selected";?>>Abril</option>
<option value="5" <? if($mes==5) echo "selected";?>>Maio</option>
<option value="6" <? if($mes==6) echo "selected";?>>Junho</option>
<option value="7" <? if($mes==7) echo "selected";?>>Julho</option>
<option value="8" <? if($mes==8) echo "selected";?>>Agosto</option>
<option value="9" <? if($mes==9) echo "selected";?>>Setembro</option>
<option value="10" <? if($mes==10) echo "selected";?>>Outubro</option>
<option value="11" <? if($mes==11) echo "selected";?>>Novembro</option>
<option value="12" <? if($mes==12) echo "selected";?>>Dezembro</option>
</select><br>
<input name="grafico" type="radio" value="2" <? if($grafico==2) echo "checked";?>>&nbsp;Gr&aacute;fico mensal do ano: <select name="ano"><?
$i=-8;
while($i<8)
{
	$ano1=date("Y", strtotime("$i year"));
	echo "<option ";
	if($ano==$ano1) echo "selected";
	echo " value=\"$ano1\">$ano1</option>";
	$i++;
}
?>
</select><br>
<br>
<input class="botao" name="submit" type="submit" value=" Procurar agora "><br><br><?
}
else
{?>
<table border="0" cellspacing="10" cellpadding="10">
<tr>
  <td>desde</td>
  <td><input style="width:70px; font-size:10px" name="f_data1" value="<?=$f_data1;?>">
	at&eacute;</td>
  <td><input style="width:70px; font-size:10px" name="f_data2" value="<?=$f_data2;?>"></td>
</tr>
</table>
<br>
<input name="grafico" type="radio" value="1" <? if($grafico==1) echo "checked";?>>&nbsp;Gr&aacute;fico di&aacute;rio ou <input name="grafico" type="radio" value="2" <? if($grafico==2) echo "checked";?>>&nbsp;Gr&aacute;fico mensal <br>
<br>
<input class="botao" name="submit" type="submit" value=" Procurar agora "><br><br><? }?>