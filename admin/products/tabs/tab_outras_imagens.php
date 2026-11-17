<table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="#F6F6F6" class="border1">
<tr>
<td valign="top">
	<table width="100%" border=0 cellpadding="2" cellspacing=0>
	<tr>
		<td width="150" class="preto">Imagem para destaque na home page PC:</td>
		<td class="preto">
		<?
			if($destaque_ext!='')
			{
				echo '<div style="border:dashed 1px #ccc"><img src="../../files/products/'.$ID.'_d.'.$destaque_ext.'?dp='.date("YmdHis").'"></div>';

				?><br><?
			  if($task!="apagar"){?><? echo write_lang('key_apagar','Apagar')?> <input type="checkbox" name="Apagar_destaque_ext" value="1"> <? echo write_lang('key_ou','ou')?> <? echo write_lang('key_alterar','Alterar')?> <input type="file" name="destaque_ext"><input type="hidden" name="tem_destaque_ext" value="1"><? }
			}
			else{?><? echo write_lang('key_nova','Nova')?>:<input type="file" name="destaque_ext"><input type="hidden" name="tem_destaque_ext" value="1"><? }?>

		</td>
	</tr>
	<tr>
		<td width="150" class="preto">Imagem para destaque na home page MOBILE:</td>
		<td class="preto">
		<?
			if($destaque2_ext!='')
			{
				echo '<div style="border:dashed 1px #ccc"><img src="../../files/products/'.$ID.'_d2.'.$destaque2_ext.'?dp='.date("YmdHis").'"></div>';

				?><br><?
			  if($task!="apagar"){?><? echo write_lang('key_apagar','Apagar')?> <input type="checkbox" name="Apagar_destaque3_ext" value="1"> <? echo write_lang('key_ou','ou')?> <? echo write_lang('key_alterar','Alterar')?> <input type="file" name="destaque2_ext"><input type="hidden" name="tem_destaque2_ext" value="1"><? }
			}
			else{?><? echo write_lang('key_nova','Nova')?>:<input type="file" name="destaque3_ext"><input type="hidden" name="tem_destaque3_ext" value="1"><? }?>

		</td>
	</tr>
	<tr>
		<td width="150" class="preto">Imagem para rodape ficha produto PC:</td>
		<td class="preto">
		<?
			if($destaque3_ext!='')
			{
				echo '<div style="border:dashed 1px #ccc"><img src="../../files/products/'.$ID.'_d3.'.$destaque3_ext.'?dp='.date("YmdHis").'"></div>';

				?><br><?
			  if($task!="apagar"){?><? echo write_lang('key_apagar','Apagar')?> <input type="checkbox" name="Apagar_destaque3_ext" value="1"> <? echo write_lang('key_ou','ou')?> <? echo write_lang('key_alterar','Alterar')?> <input type="file" name="destaque3_ext"><input type="hidden" name="tem_destaque3_ext" value="1"><? }
			}
			else{?><? echo write_lang('key_nova','Nova')?>:<input type="file" name="destaque3_ext"><input type="hidden" name="tem_destaque3_ext" value="1"><? }?>

		</td>
	</tr>
	<tr>
		<td width="150" class="preto">Imagem para rodape ficha produto MOBILE:</td>
		<td class="preto">
		<?
			if($destaque4_ext!='')
			{
				echo '<div style="border:dashed 1px #ccc"><img src="../../files/products/'.$ID.'_d4.'.$destaque4_ext.'?dp='.date("YmdHis").'"></div>';

				?><br><?
			  if($task!="apagar"){?><? echo write_lang('key_apagar','Apagar')?> <input type="checkbox" name="Apagar_destaque4_ext" value="1"> <? echo write_lang('key_ou','ou')?> <? echo write_lang('key_alterar','Alterar')?> <input type="file" name="destaque4_ext"><input type="hidden" name="tem_destaque4_ext" value="1"><? }
			}
			else{?><? echo write_lang('key_nova','Nova')?>:<input type="file" name="destaque4_ext"><input type="hidden" name="tem_destaque4_ext" value="1"><? }?>

		</td>
	</tr>
	</table>
</td>
</tr>
</table>