<table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="F6F6F6" class="border1">
<tr> 
<td valign="top">

<table width="100%" border=0 cellpadding="2" cellspacing=0>
<tr>
<td class="preto">Descrição:</td>
</tr>
<tr>
<td class="preto"><textarea name="texto1" id="texto1" style="WIDTH:100%; HEIGHT:100px"><? echo $T;?></textarea></td>
</tr>
<tr>
<td class="preto">Saiba mais:</td>
</tr>
<tr>
<td class="preto"><textarea name="texto3" id="texto3" style="WIDTH:100%; HEIGHT:100px"><? echo $T3;?></textarea><? show_editor("oEdit3","texto3","100%",250)?></td>
</tr><?php if(isset($prod_conf) && $prod_conf['tem_comousar'] == 1){?>
<tr>
<td class="preto">Como usar:</td>
</tr>
<tr>
<td class="preto"><textarea name="texto4" id="texto4" style="WIDTH:100%; HEIGHT:100px"><? echo $T4;?></textarea><? show_editor("oEdit2","texto4","100%",250)?></td>
</tr><?php }?>
<?php if(isset($prod_conf) && $prod_conf['tem_tab_extra'] == 1){?>
<tr>
<td class="preto">Nome do Tab Extra: <input type="text" name="cabecalho_texto5" style="WIDTH:280px" maxlength="255" value="<? echo $Cabecalho_Texto5;?>"></td>
</tr>
<tr>
<td class="preto"><textarea name="texto5" id="texto5" style="WIDTH:100%; HEIGHT:100px"><? echo $Texto5;?></textarea><? show_editor("oEdit2","texto5","100%",250)?></td>
</tr>  
<?php }?>
</table>
</td>
</tr>
</table>