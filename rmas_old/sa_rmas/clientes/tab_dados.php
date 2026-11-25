<table width="100%" cellpadding="0" cellspacing="0" border="0"><input type="hidden" name="<?php echo CAMPO_LINGUA?>" id="<?php echo CAMPO_LINGUA?>" class="box" value="<?php echo $GLOBALS['admin_lang']?>" style="width:50px"/>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>						
<td align="left" width="100">Cliente Nº:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td width="45"><input type="text" name="cliente_id_site" id="cliente_id_site" class="box" value="<?php echo $cliente_id_site?>" style="width:150px"/></td>
<td width="20">&nbsp;</td>
<td width="300"><a href="pesquisa_cliente.php" field="cliente_id_site" fieldbd="ID" class="preto search" style="font-size:15px"><u><i class="fa fa-search" style="width:30px; text-align: center;"></i>Pesquisar por Cliente Nº</u></a></td>
</tr>
</table>
<div class="wrap"><div class="fechar"><i class="fa fa-close" style="text-align: center;"></i> FECHAR</div><div class="results" id="cliente_id_site_results">&nbsp;</div></div>
</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>						
<td align="left">Nif:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td width="45"><input class="box" type="text" name="cliente_nif" id="cliente_nif" value="<?php echo $cliente_nif;?>" style="width:150px"/></td>
<td width="20">&nbsp;</td>
<td width="300"><a href="" class="preto" style="font-size:15px"><u><i class="fa fa-search" style="width:30px; text-align: center;"></i>Pesquisar por Nif</u></a></td>
</tr>
</table>


</td>
</tr>


<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>						
<td align="left">Nome:</td>
<td align="left"><input class="box" type="text" name="cliente_nome" id="cliente_nome" value="<?php echo $cliente_nome;?>" style="width:88%"/></td>
</tr>


<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>						
<td align="left">Morada:</td>
<td align="left"><input class="box" type="text" name="cliente_morada" id="cliente_morada" value="<?php echo $cliente_morada;?>" style="width:88%"/></td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>						
<td align="left">Código Postal:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td width="45"><input class="box" type="text" name="cliente_codigopostal" id="cliente_codigopostal" value="<?php echo $cliente_codigopostal;?>" style="width:250px"/></td>
<td align="center" width="150">Localidade:</td>
<td align="left"><input class="box" type="text" name="cliente_localidade" id="cliente_localidade" value="<?php echo $cliente_localidade;?>" style="width:250px"/></td>
</tr>
</table>
</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">Telefone:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td width="45"><input class="box" type="text" name="cliente_telefone" id="cliente_telefone" value="<?php echo $cliente_telefone;?>" style="width:250px"/></td>
<td align="center" width="150">Telemovel:</td>
<td align="left"><input class="box" type="text" name="cliente_telemovel" id="cliente_telemovel" value="<?php echo $cliente_telemovel;?>" style="width:250px"/></td>	
</tr>
</table>
</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>						
<td align="left">Fax:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td width="45"><input class="box" type="text" name="cliente_fax" id="cliente_fax" value="<?php echo $cliente_fax;?>" style="width:250px"/></td>
<td align="center" width="150">Email:</td>
<td align="left"><input class="box" type="text" name="cliente_email" id="cliente_email" value="<?php echo $cliente_email;?>" style="width:250px"/></td>
</tr>
</table>
</td>
</tr>


<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
</table>