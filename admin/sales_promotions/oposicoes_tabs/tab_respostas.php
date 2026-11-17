<div id="info_resposta">
<table width="100%" cellpadding="0" cellspacing="0" border="0" id="RESPOSTA" style="display:none">
<tr><td colspan="2">&nbsp;<input type="hidden" class="RESPOSTA_ID" /></td></tr>
<tr>						
<td align="left">Data Oposição:</td>
<td align="left">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td><input type="text" class="box dropdate RESPOSTA_DATA" value="" /></td>
<td align="right" width="120">Nome Openente:&nbsp;&nbsp;</td>
<td align="left"><input type="text" style="width:280px" class="box RESPOSTA_OPONENTE" value="" /></td>
</tr>
</table>
</td>						
</tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>						
<td align="left">Direitos Oponente:</td>
<td><textarea style="WIDTH: 350px; HEIGHT: 100px" class="box RESPOSTA_OPONENTE_DIREITOS"></textarea></td>
</tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>						
<td align="left">Nome Requerente:</td>
<td align="left">
<input type="text" class="box RESPOSTA_REQUERENTE" value="" />
</td>						
</tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>						
<td align="left">Direitos Requerente:</td>
<td><textarea style="WIDTH: 350px; HEIGHT: 100px" class="box RESPOSTA_REQUERENTE_DIREITOS"></textarea></td>
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
</table>
<?php	
	$REL = 0;
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;
	$sql = 
			'
			SELECT 
				oposicao_id,
				DATA,
				OPONENTE,
				OPONENTE_DIREITOS,
				REQUERENTE,
				REQUERENTE_DIREITOS,
				RENOVACAO_REL
			FROM 
				sa_oposicoes
			WHERE
				objeto_bloco="RESPOSTA"
				and
				objeto_nome="'.MODULO.'"
				AND
				objeto_id="'.$marca_id.'"
				and 
				removido=0
			ORDER BY 
				RENOVACAO_REL ASC
			';
	$rs->db_open($db_link, $DB_name, $sql);
	if(!$rs->EOF())
	{
		while(!$rs->EOF())
		{			
			$oposicao_id = '';$DATA = ''; $OPONENTE = '';$OPONENTE_DIREITOS = ''; $REQUERENTE = ''; $REQUERENTE_DIREITOS = ''; $REL = '';			
			$oposicao_id = $rs->db_data['oposicao_id'];
			$DATA = $rs->db_data['DATA'];
			if($DATA=='0000-00-00') $DATA = '';
			$OPONENTE = $rs->db_data['OPONENTE'];
			$OPONENTE_DIREITOS = $rs->db_data['OPONENTE_DIREITOS'];
			$REQUERENTE = $rs->db_data['REQUERENTE'];
			$REQUERENTE_DIREITOS = $rs->db_data['REQUERENTE_DIREITOS'];
			$REL = $rs->db_data['RENOVACAO_REL'];
			?>	
			<table width="100%" cellpadding="0" cellspacing="0" border="0" id="RESPOSTA<?php echo $REL?>">
			<tr><td colspan="2">&nbsp;<input type="hidden" value="<?php echo $REL?>;<?php echo $oposicao_id?>;RESPOSTA" name="RESPOSTA_ID[]" id="RESPOSTA_ID<?php echo $REL?>" class="RESPOSTA_ID" /></td></tr>
			<tr>						
			<td align="left">Data Oposição:</td>
			<td align="left">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
			<td><input type="text" name="RESPOSTA_DATA<?php echo $REL?>" id="RESPOSTA_DATA<?php echo $REL?>" class="box dropdate RESPOSTA_DATA" value="<?php echo $DATA?>" /></td>
			<td align="right" width="120">Nome Openente:&nbsp;&nbsp;</td>
			<td align="left"><input type="text" style="width:280px" class="box RESPOSTA_OPONENTE" name="RESPOSTA_OPONENTE<?php echo $REL?>" id="RESPOSTA_OPONENTE<?php echo $REL?>" value="<?php echo $OPONENTE;?>" /></td>
			</tr>
			</table>
			</td>						
			</tr>
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr>						
			<td align="left">Direitos Oponente:</td>
			<td><textarea style="WIDTH: 350px; HEIGHT: 100px" class="box RESPOSTA_OPONENTE_DIREITOS" name="RESPOSTA_OPONENTE_DIREITOS<?php echo $REL?>" id="RESPOSTA_OPONENTE_DIREITOS<?php echo $REL?>"><?php echo $OPONENTE_DIREITOS?></textarea></td>
			</tr>
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr>						
			<td align="left">Nome Requerente:</td>
			<td align="left">
			<input type="text" name="RESPOSTA_REQUERENTE<?php echo $REL?>" id="RESPOSTA_REQUERENTE<?php echo $REL?>" class="box RESPOSTA_REQUERENTE" value="<?php echo $REQUERENTE?>" />
			</td>						
			</tr>
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr>						
			<td align="left">Direitos Requerente:</td>
			<td><textarea style="WIDTH: 350px; HEIGHT: 100px" class="box RESPOSTA_REQUERENTE_DIREITOS" name="RESPOSTA_REQUERENTE_DIREITOS<?php echo $REL?>" id="RESPOSTA_REQUERENTE_DIREITOS<?php echo $REL?>"><?php echo $REQUERENTE_DIREITOS?></textarea></td>
			</tr>
			<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
			<tr><td colspan="2">&nbsp;</td></tr>
			</table>			
			<?php
			$rs->db_next();
		}
	}	
	?>
	<input type="hidden" name="total_linhas_resposta" id="total_linhas_resposta" value="<?php echo ($REL+1)?>" />

</div>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>						
<td width="30" align="left"><a href="#" onclick="return false;" class="addRow_resposta"><img src="img/add-item.png"></a></td>
<td align="left"><a href="#" onclick="return false;" class="pretourl addRow_resposta">Adicionar Oposição</a></td>
</tr>
<tr><td colspan="2">&nbsp;</td></tr>
</table>
<div class="clearboth"></div>