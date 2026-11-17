<table width="100%" cellpadding="0" cellspacing="0" border="0"><input type="hidden" name="bloco_lang" id="bloco_lang" class="form-control" value="<?php echo $GLOBALS['admin_lang']?>" style="width:50px"/>
<tr><td colspan="2">&nbsp;</td></tr>


<tr>
<td align="left" width="350">LOGOTIPO PRINCIPAL:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td align="left" style="width:300px">
<?php


if($img_ext && file_exists('../../files/'.FOLDER.'/'.$img_ext)){ echo '<a class="pretourl_small fancybox_img" href="../../files/'.FOLDER.'/'.$img_ext.'" target="_blank" class="url_reno">Abrir Ficheiro</a><br />';}?><input type="file" name="img_ext" id="img_ext" />

</td>
<td align="left">
	<?php if($img_ext && file_exists('../../files/'.FOLDER.'/'.$img_ext)){ echo '<img src="../../files/'.FOLDER.'/'.$img_ext.'" style="width:100%;height:50px" />';}?>
</td>
</tr>
</table>
</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">FAVICON (PNG):</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td align="left" style="width:300px">
<?php if($img2_ext && file_exists('../../files/'.FOLDER.'/'.$img2_ext)){ echo '<a class="pretourl_small fancybox_img" href="../../files/'.FOLDER.'/'.$img2_ext.'" target="_blank" class="url_reno">Abrir Ficheiro</a><br />';}?><input type="file" name="img2_ext" id="img2_ext" />
</td>
<td align="left">
<?php if($img2_ext && file_exists('../../files/'.FOLDER.'/'.$img2_ext)){ echo '<img src="../../files/'.FOLDER.'/'.$img2_ext.'" style="width:100%;height:50px" />';}?>
</td>
</tr>
</table>
</td>
</tr>


<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">LOGOTIPO PARA AS POPUPS:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td align="left" style="width:300px">
<?php if($img3_ext && file_exists('../../files/'.FOLDER.'/'.$img3_ext)){ echo '<a class="pretourl_small fancybox_img" href="../../files/'.FOLDER.'/'.$img3_ext.'" target="_blank" class="url_reno">Abrir Ficheiro</a><br />';}?><input type="file" name="img3_ext" id="img3_ext" />
</td>
<td align="left">
<?php if($img3_ext && file_exists('../../files/'.FOLDER.'/'.$img3_ext)){ echo '<img src="../../files/'.FOLDER.'/'.$img3_ext.'" style="width:100%;height:50px" />';}?>
</td>
</tr>
</table>
</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">LOGOTIPO PARA AS TEMPLATES DE EMAIL:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td align="left" style="width:300px">
<?php if($img4_ext && file_exists('../../files/'.FOLDER.'/'.$img4_ext)){ echo '<a class="pretourl_small fancybox_img" href="../../files/'.FOLDER.'/'.$img4_ext.'" target="_blank" class="url_reno">Abrir Ficheiro</a><br />';}?><input type="file" name="img4_ext" id="img4_ext" />
</td>
<td align="left">
<?php if($img4_ext && file_exists('../../files/'.FOLDER.'/'.$img4_ext)){ echo '<img src="../../files/'.FOLDER.'/'.$img4_ext.'" style="width:100%;height:50px" />';}?>
</td>
</tr>
</table>
</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">LOGOTIPO PARA OS DISPOSITIVOS MOVEIS:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td align="left" style="width:300px">
<?php if($img5_ext && file_exists('../../files/'.FOLDER.'/'.$img5_ext)){ echo '<a class="pretourl_small fancybox_img" href="../../files/'.FOLDER.'/'.$img5_ext.'" target="_blank" class="url_reno">Abrir Ficheiro</a><br />';}?><input type="file" name="img5_ext" id="img5_ext" />
</td>
<td align="left">
<?php if($img5_ext && file_exists('../../files/'.FOLDER.'/'.$img5_ext)){ echo '<img src="../../files/'.FOLDER.'/'.$img5_ext.'" style="width:100%;height:50px" />';}?>
</td>
</tr>
</table>
</td>
</tr>

</table>