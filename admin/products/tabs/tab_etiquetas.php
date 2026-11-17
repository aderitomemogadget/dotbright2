<table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="#F6F6F6" class="border1">
<tr>
<td valign="top">
<table width="100%" border=0 cellpadding="2" cellspacing=0>
	<tr>
		<td width="250" class="preto">ULR para video:</td>
		<td class="preto"><input type="text" name="Video" id="Video" style="WIDTH:100%" value="<? echo stripslashes($Video);?>"></td>
	</tr>

	<tr>
		<td width="250" class="preto">u_catetiq:</td>
		<td class="preto"><input type="text" name="u_catetiq" id="u_catetiq" style="WIDTH:100%" value="<? echo stripslashes($u_catetiq);?>"></td>
	</tr>
	<tr>
		<td width="250" class="preto">u_coretiq:</td>
		<td class="preto"><input type="text" name="u_coretiq" id="u_coretiq" style="WIDTH:100%" value="<? echo stripslashes($u_coretiq);?>"></td>
	</tr>
	<tr>
		<td width="150" class="preto">Cor em # da etiqueta energética:</td>
		<td class="preto">
			<table border=0 cellpadding="0" cellspacing=0>
			<tr>
			<td valign="top" width="110">
				<input maxlength="7" type="text" name="corEE" id="corEE" style="WIDTH:100px;<?php echo ($corEE!='' ? 'background:'.$corEE : '');?>" value="<? echo stripslashes($corEE);?>" >
			</td>
			<td>
				<div style="float:left; width:100%; heigth:auto;">
					<div style="float:left; width:20px; height:20px; margin-bottom:10px; background-color:#00A54F; cursor:pointer;margin:2px;" onclick="mudaCor('#00A54F')"></div>
					<div style="float:left; width:20px; height:20px; margin-bottom:10px; background-color:#4CB849; cursor:pointer;margin:2px;" onclick="mudaCor('#4CB849')"></div>
					<div style="float:left; width:20px; height:20px; margin-bottom:10px; background-color:#BDD632; cursor:pointer;margin:2px;" onclick="mudaCor('#BDD632')"></div>
					<div style="float:left; width:20px; height:20px; margin-bottom:10px; background-color:#FFF102; cursor:pointer;margin:2px;" onclick="mudaCor('#FFF102')"></div>
					<div style="float:left; width:20px; height:20px; margin-bottom:10px; background-color:#FDB815; cursor:pointer;margin:2px;" onclick="mudaCor('#FDB815')"></div>
					<div style="float:left; width:20px; height:20px; margin-bottom:10px; background-color:#F36E21; cursor:pointer;margin:2px;" onclick="mudaCor('#F36E21')"></div>
					<div style="float:left; width:20px; height:20px; margin-bottom:10px; background-color:#EE1D23; cursor:pointer;margin:2px;" onclick="mudaCor('#EE1D23')"></div>
				</div>
			</td>
			</tr>
			</table>
			<script>
			function mudaCor(elem)
			{
				$('#corEE').val(elem);
				$('#corEE').css('background-color',elem);
			}
			</script>
		</td>
	</tr>
	<tr>
		<td width="150" class="preto">Ficheiro de etiqueta energética:</td>
		<td class="preto">
		<?
			if($ee_ext!='')
			{?>
				<a href="<? echo "../../files/products/EE/".$ID."_1.".$ee_ext;?>" target="_new">VER FICHEIRO</a><br><?
			  if($task!="apagar"){?><? echo write_lang('key_apagar','Apagar')?> <input type="checkbox" name="Apagar_ee_ext" value="1"> <? echo write_lang('key_ou','ou')?> <? echo write_lang('key_alterar','Alterar')?> <input type="file" name="ee_ext"><input type="hidden" name="tem_ee_ext" value="1"><? }
			}
			else{?><? echo write_lang('key_nova','Nova')?>:<input type="file" name="ee_ext"><input type="hidden" name="tem_ee_ext" value="1"><? }?>

		</td>
	</tr>
	<tr>
		<td width="150" class="preto">Ficheiro de ficha da união europeia:</td>
		<td class="preto">
		<?
			if($ue_ext!='')
			{?>
				<a href="<? echo "../../files/products/UE/".$ID."_1.".$ue_ext;?>" target="_new">VER FICHEIRO</a><br><?
			  if($task!="apagar"){?><? echo write_lang('key_apagar','Apagar')?> <input type="checkbox" name="Apagar_ue_ext" value="1"> <? echo write_lang('key_ou','ou')?> <? echo write_lang('key_alterar','Alterar')?> <input type="file" name="ue_ext"><input type="hidden" name="tem_ue_ext" value="1"><? }
			}
			else{?><? echo write_lang('key_nova','Nova')?>:<input type="file" name="ue_ext"><input type="hidden" name="tem_ue_ext" value="1"><? }?>

			</td>
	</tr>
	<tr>
		<td width="150" class="preto">Ficheiro dos desenhos tecnicos:</td>
		<td class="preto">
			<?
			if($dt_ext!='')
			{?>
				<a href="<? echo "../../files/products/DT/".$ID."_1.".$dt_ext;?>" target="_new">VER FICHEIRO</a><br><?
			  if($task!="apagar"){?><? echo write_lang('key_apagar','Apagar')?> <input type="checkbox" name="Apagar_dt_ext" value="1"> <? echo write_lang('key_ou','ou')?> <? echo write_lang('key_alterar','Alterar')?> <input type="file" name="dt_ext"><input type="hidden" name="tem_dt_ext" value="1"><? }
			}
			else{?><? echo write_lang('key_nova','Nova')?>:<input type="file" name="dt_ext"><input type="hidden" name="tem_dt_ext" value="1"><? }?>


		</td>
	</tr>
	</table></td>
</tr>
</table>