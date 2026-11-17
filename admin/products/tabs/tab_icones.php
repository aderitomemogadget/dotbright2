<table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="F6F6F6" class="border1">
<tr> 
<td valign="top">

<table width="100%" border=0 cellpadding="2" cellspacing=0>

<?
$ar_store_icons = isset($ar_store_icons) && is_array($ar_store_icons) ? $ar_store_icons : [];
$rs->db_open($db_link, $DB_name, "SELECT products_components.ID, img_ext, Nome FROM products_components LEFT JOIN products_components_lang ON products_components.ID=products_components_lang.ID_PAI WHERE Visivel=1 ORDER BY Ordem ASC");
$idx=0;
while(!$rs->EOF())
{
	if(fmod($idx, 4)==0) 
		echo '</tr><tr>';
	?>
	<td style="text-align: center; padding-top: 20px;">
		<label style="border:solid 1px #ccc;padding:5px"><? if(file_exists("../../files/products_components/".$rs->db_data['ID']."_1.".$rs->db_data['img_ext'])){?><img src="<? echo "../../files/products_components/".$rs->db_data['ID']."_1.".$rs->db_data['img_ext'];?>" style="max-height:48px"><br><? }?>
		<input type="checkbox" name="icn_chk[<?=$rs->db_data['ID']?>]" id="icn_chk_<?=$rs->db_data['ID']?>" value="1"
			<? if(in_array($rs->db_data['ID'], $ar_store_icons)){ ?>checked="true"<? } ?>
		><br />
		<?=$rs->db_data['Nome']?></label>
	</td>
	<?
	$idx++;
	$rs->db_next();
}
$rs->db_close();
?>

<!-- <tr>
<td class="preto">Descrição:</td>
</tr>
<tr>
<td class="preto"><textarea name="texto1" id="texto1" style="WIDTH:100%; HEIGHT:100px"><? echo $T;?></textarea></td>
</tr> -->

<!-- <tr>
<td class="preto">Detalhes:</td>
</tr>
<tr>
<td class="preto"><textarea name="texto3" id="texto3" style="WIDTH:100%; HEIGHT:100px"><? echo $T3;?></textarea><? show_editor("oEdit3","texto3","100%",250)?></td>
</tr> -->


<!-- <tr>
<td class="preto">Ficha Técnica:</td>
</tr>
<tr>
<td class="preto"><textarea name="texto4" id="texto4" style="WIDTH:100%; HEIGHT:100px"><? echo $T4;?></textarea><? show_editor("oEdit2","texto4","100%",250)?></td>
</tr> -->

<?php if(isset($prod_conf) && $prod_conf['tem_tab_extra'] == 1){?>
<tr>
<td class="preto">Nome do Tab Extra: <input type="text" name="cabecalho_texto5" style="WIDTH:280px" maxlength="255" value="<? echo $Cabecalho_Texto5;?>"></td>
</tr>
<tr>
<td class="preto"><textarea name="texto5" id="texto5" style="WIDTH:100%; HEIGHT:100px"><? echo $Texto5;?></textarea><? show_editor("oEditm","texto5","100%",250)?></td>
</tr>  
<?php }?>
</table>
</td>
</tr>
</table>
