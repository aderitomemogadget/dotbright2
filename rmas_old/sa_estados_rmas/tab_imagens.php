<div id="info_renovacoes">
<table width="100%" cellpadding="0" cellspacing="0" border="0" id="renovacoes" style="display:none">
<tr><td colspan="2">&nbsp;<input type="hidden" value="" class="RENOVACAO_ID" /></td></tr>
<tr>						
<td align="left">Imagem:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td align="left"><input type="file" class="BOLETIM_EXT" value="" /></td>
</tr>
</table>
</td>						
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
</table>
<?php
	
	$bloco_rel = 0;
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;
	$sql_renova = 
				'
				SELECT 
					bloco_img_id,
					bloco_img_ext,
					bloco_rel
				FROM 
					blocos_imgs
				WHERE
					bloco_id = "'.$bloco_id.'"
				ORDER BY 
					bloco_rel ASC
				';
	$rs->db_open($db_link, $DB_name, $sql_renova);
	if(!$rs->EOF() || $rs->EOF())
	{
		while(!$rs->EOF())
		{			
			$bloco_img_id = ''; $bloco_img_ext = '';$bloco_rel = '';
			$bloco_img_id = $rs->db_data['bloco_img_id'];			
			$bloco_img_ext = $rs->db_data['bloco_img_ext'];			
			$bloco_rel = $rs->db_data['bloco_rel'];
	
			?>	
			<table width="100%" cellpadding="0" cellspacing="0" border="0" id="renovacoes<?php echo $bloco_rel?>">
			<tr><td colspan="2">&nbsp;<input type="hidden" value="<?php echo $bloco_rel?>;<?php echo $bloco_img_id?>" name="RENOVACAO_ID[]" id="RENOVACAO_ID<?php echo $bloco_rel?>" class="box RENOVACAO_ID" /></td></tr>
			<tr>						
			<td align="left">Imagem:</td>
			<td align="left">
			<table cellpadding="0" cellspacing="0" border="0">
			<tr>
			<td align="left"><?php if($bloco_img_ext && file_exists('../../files/'.FOLDER.'/'.$bloco_img_ext)){ echo '<a class="pretourl_small fancybox_img" href="../../files/'.FOLDER.'/'.$bloco_img_ext.'" target="_blank" class="url_reno">Abrir Ficheiro</a><br />';}?><input type="file" name="bloco_img_ext<?php echo $bloco_rel?>" id="bloco_img_ext<?php echo $bloco_rel?>" class="BOLETIM_EXT" /></td>
			</tr>
			</table>
			</td>						
			</tr>
			<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
			<tr><td colspan="2">&nbsp;</td></tr>
			</table>			
			<?php
			$rs->db_next();
		}
	}	
	?>
	<input type="hidden" name="total_linhas_renovacoes" id="total_linhas_renovacoes" value="<?php echo ($bloco_rel+1)?>" />
</div>


<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td colspan="2">&nbsp;</td></tr>
<tr>						
<td width="30" align="left"><a href="#" onclick="return false;" class="addRow"><img src="img/add-item.png"></a></td>
<td align="left"><a href="#" onclick="return false;" class="pretourl addRow">Adicionar Imagem</a></td>
</tr>
</table>