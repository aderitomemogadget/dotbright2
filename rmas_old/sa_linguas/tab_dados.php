<table width="100%" cellpadding="0" cellspacing="0" border="0"><input type="hidden" name="bloco_lang" id="bloco_lang" class="box" value="<?php echo $GLOBALS['admin_lang']?>" style="width:50px"/>
<tr><td colspan="2">&nbsp;</td></tr>


<tr>						
<td align="left" width="100">Chave:</td>
<td align="left"><input class="box" type="text" name="bloco_titulo" id="bloco_titulo" onkeypress="return validateQty(event);" value="<?php echo $bloco_titulo;?>" style="width:88%"/></td>						
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>

<?php
$rs2 = new Dataobj;
$rs3 = new Dataobj;
// $db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
$sql = 'SELECT Sigla, Cabecalho FROM linguas ORDER BY Ordem ASC'; // where Visivel=1 
$rs2->db_open($db_link, $GLOBALS['DB_name'], $sql);
if(!$rs2->EOF())
{
	while(!$rs2->EOF())
	{
		$bloco_texto = '';
		$sql = 'SELECT bloco_texto FROM '.TBMODULO_LANG.' where bloco_lang="'.$rs2->db_data['Sigla'].'" AND '.TBID.'='.$_GET['bloco_id'].'';
		$rs3->db_open($db_link, $GLOBALS['DB_name'], $sql);
		if(!$rs3->EOF()) $bloco_texto = $rs3->db_data['bloco_texto'];
		$rs3->db_close();
		?>	
		<tr>						
		<td align="left" width="100" style="vertical-align:top"><?php echo $rs2->db_data['Cabecalho']?>:</td>
		<td align="left"><textarea style="width:88%" class="box" type="text" name="bloco_texto_<?php echo $rs2->db_data['Sigla']?>" id="bloco_texto_<?php echo $rs2->db_data['Sigla']?>" ><?php echo $bloco_texto;?></textarea></td>						
		<td align="left" width="300">
		<a href="#" onclick="transform('up', 'bloco_texto_<?php echo $rs2->db_data['Sigla']?>');return false;" class="preto">Uppercase</a> | 
		<a href="#" onclick="transform('lo', 'bloco_texto_<?php echo $rs2->db_data['Sigla']?>');return false;" class="preto">Lowecase</a>
		</tr>		
		<tr><td colspan="2">&nbsp;</td></tr>
		<?php	
		$rs2->db_next();
	}
	$rs2->db_close();
	
}
$rs2->db_close();
?>
</table>
<script>
function transform(t, input)
{
	
	if(t=='up') document.getElementById(input).value = document.getElementById(input).value.toUpperCase();
	else if(t=='lo') document.getElementById(input).value = document.getElementById(input).value.toLowerCase();
	// else if(t=='fs') document.getElementById(input).value = document.getElementById(input).value.toUpperCase();
}

</script>