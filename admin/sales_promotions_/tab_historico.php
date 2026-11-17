<div id="info_renovacoes">
<table width="100%" cellpadding="0" cellspacing="0" border="0" id="renovacoes" style="display:none">
<tr><td colspan="2">&nbsp;<input type="hidden" value="" class="RENOVACAO_ID" /></td></tr>
<tr>						
<td align="left">Data Pedido:</td>
<td align="left"><input type="text" class="box dropdate RENOVACAO_DATA" value="" /></td>
<td rowspan="10"><a href="#"><img src="img/delete.png" border="0" /></a></td>
</tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>						
<td width="130" align="left">Número:</td>
<td align="left"><input type="text" value="" class="box RENOVACAO_NUMERO" /></td>					
</tr>			
<tr><td colspan="2">&nbsp;</td></tr>
<tr>						
<td align="left">Data Boletim:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td><input type="text" class="box dropdate BOLETIM_DATA" value="" /></td>
<td align="right" align="left" width="100">Boletim PDF:</td>
<td align="left"><input type="file" class="BOLETIM_EXT" value="" /></td>
</tr>
</table>
</td>						
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
</table>
<?php
	
	$RENOVACAO_REL = 0;
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;
	$sql_renova = 
				'
				SELECT 
					renovacao_id,
					RENOVACAO_DATA,
					RENOVACAO_NUMERO,
					BOLETIM_DATA,
					BOLETIM_EXT,
					RENOVACAO_REL
				FROM 
					sa_renovacoes
				WHERE
					objeto_nome="'.MODULO.'"
					AND
					objeto_id="'.$marca_id.'"
					and 
					removido=0
				ORDER BY 
					RENOVACAO_REL ASC
				';
	$rs->db_open($db_link, $DB_name, $sql_renova);
	if(!$rs->EOF() || $rs->EOF())
	{
		while(!$rs->EOF())
		{			
			$RENOVACAO_DATA = ''; $RENOVACAO_NUMERO = '';$BOLETIM_DATA = ''; $BOLETIM_EXT = '';	$RENOVACAO_REL = '';
			$renovacao_id = $rs->db_data['renovacao_id'];			
			$RENOVACAO_DATA = $rs->db_data['RENOVACAO_DATA'];
			if($RENOVACAO_DATA=='0000-00-00') $RENOVACAO_DATA = '';
			$RENOVACAO_NUMERO = $rs->db_data['RENOVACAO_NUMERO'];
			$BOLETIM_DATA = $rs->db_data['BOLETIM_DATA'];
			if($BOLETIM_DATA=='0000-00-00') $BOLETIM_DATA = '';
			$BOLETIM_EXT = $rs->db_data['BOLETIM_EXT'];
			$RENOVACAO_REL = $rs->db_data['RENOVACAO_REL'];
	
			?>	
			<table width="100%" cellpadding="0" cellspacing="0" border="0" id="renovacoes<?php echo $RENOVACAO_REL?>">
			<tr><td colspan="2">&nbsp;<input type="hidden" value="<?php echo $RENOVACAO_REL?>;<?php echo $renovacao_id?>" name="RENOVACAO_ID[]" id="RENOVACAO_ID<?php echo $RENOVACAO_REL?>" class="box RENOVACAO_ID" /></td></tr>
			<tr>						
			<td align="left">Data Pedido:</td>
			<td align="left"><input type="text" name="RENOVACAO_DATA<?php echo $RENOVACAO_REL?>" id="RENOVACAO_DATA<?php echo $RENOVACAO_REL?>" class="box dropdate RENOVACAO_DATA" value="<?php echo $RENOVACAO_DATA?>" /></td>						
			<td rowspan="10"><a href="#" onclick="remove_renova('<?php echo $RENOVACAO_REL?>')"><img src="img/delete.png" border="0" /></a></td>
			</tr>
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr>						
			<td width="130" align="left">Número:</td>
			<td align="left"><input type="text" name="RENOVACAO_NUMERO<?php echo $RENOVACAO_REL?>" id="RENOVACAO_NUMERO<?php echo $RENOVACAO_REL?>" value="<?php echo $RENOVACAO_NUMERO;?>" class="box RENOVACAO_NUMERO" /></td>						
			</tr>			
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr>						
			<td align="left">Data Boletim:</td>
			<td align="left">
			<table cellpadding="0" cellspacing="0" border="0">
			<tr>
			<td><input type="text" name="BOLETIM_DATA<?php echo $RENOVACAO_REL?>" id="BOLETIM_DATA<?php echo $RENOVACAO_REL?>" class="box dropdate BOLETIM_DATA" value="<?php echo $BOLETIM_DATA?>" /></td>
			<td align="right" align="left" width="100">Boletim PDF:</td>
			<td align="left"><?php if($BOLETIM_EXT && file_exists('../../files/documentos/'.$BOLETIM_EXT)){ echo '<a class="pretourl_small fancybox2" href="../../files/documentos/'.$BOLETIM_EXT.'" target="_blank" class="url_reno">Abrir Ficheiro</a><br />';}?><input type="file" name="BOLETIM_EXT<?php echo $RENOVACAO_REL?>" id="BOLETIM_EXT1" class="BOLETIM_EXT" /></td>
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
	<input type="hidden" name="total_linhas_renovacoes" id="total_linhas_renovacoes" value="<?php echo ($RENOVACAO_REL+1)?>" />
</div>


<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td colspan="2">&nbsp;</td></tr>
<tr>						
<td width="30" align="left"><a href="#" onclick="return false;" class="addRow"><img src="img/add-item.png"></a></td>
<td align="left"><a href="#" onclick="return false;" class="pretourl addRow">Adicionar Renovação</a></td>
</tr>
</table>