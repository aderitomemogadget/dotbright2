<?php

global $DB_host,$DB_user,$DB_pass,$DB_name;	
$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
$dados = new DataObj;
?>

<table width="100%" border="0" cellpadding="10" cellspacing="0" bgcolor="#F6F6F6" class="border1">
<tr> 
<td valign="top">


<?php

$sql = '
		SELECT 
			pa.bloco_id, pal.bloco_titulo
		FROM 
			personalizacao pa
			INNER JOIN personalizacao_lang pal ON pal.bloco_id=pa.bloco_id
		WHERE
			pal.bloco_lang="'.$GLOBALS['admin_lang'].'"
		';
$dados->db_open($db_link,$DB_name,$sql);
if(!$dados->EOF())
{
	?><table  border="0" cellpadding="2" cellspacing="0"><?php
	while(!$dados->EOF())
	{
		
		// echo in_array($dados->db_data['bloco_id'], $personalizacao).'<br>';
		?>		
			<tr>
			<td class="preto"><?php echo $dados->db_data['bloco_titulo']; ?></td>
			<td class="preto" align="left">
			<input type="checkbox" name="personalizacao[]" <?php if(isset($personalizacao) && in_array($dados->db_data['bloco_id'], $personalizacao)){ echo 'checked="checked"';}?> value="<?php echo $dados->db_data['bloco_id']; ?>" />
			</td>
			</tr>
		<?php
		$dados->db_next();
	}
	?></table><?php
}
$dados->db_close();

?>
</td>
</tr>
</table>
