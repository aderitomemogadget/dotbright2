<?php
$url = $_GET['file'];
//$url2=intval(str_replace('/var/www/vhosts/mhr.pt/httpdocs/conf/../files/bk/log','',$url));//Para proteger contra vulnerabilidades
$url2=intval(str_replace($GLOBALS['site_shell_url'].'files/bk/log','',$url));//Para proteger contra vulnerabilidades


?><script type="text/javascript" src="../editor/ckeditor_4_5_1/ckeditor.js"></script>
<script type="text/javascript" src="../editor/ckeditor_4_5_1/ckfinder/ckfinder.js"></script>
<table width="100%" cellpadding="0" cellspacing="0" border="0"><input type="hidden" name="Lang" id="Lang" class="form-control" value="<?php echo $GLOBALS['admin_lang']?>" style="width:50px"/>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left" width="100">Data:</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
	<td width="45"><?=$url2?></td>
</tr>
</table>
</td>
</tr><?php

$fh1 = fopen($GLOBALS['site_shell_url']."files/bk/log".$url2, 'r') or die("Unable to open file!");

echo '<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>';
echo '<tr><td colspan="2">&nbsp;</td></tr>';
echo '<tr><td align="left">LOG:</td><td align="left">';
	echo '<textarea class="form-control" name="Texto" id="Texto" style="width:100%;height:600px">';
while(!feof($fh1))
{
	//echo '<pre>';
	echo fgets($fh1);
	//echo '</pre>';
}
	echo '</textarea>';
echo '</td></tr>';
?>
<tr>
</table>
<?php /*
<td align="left" width="150">User :</td>
<td align="left">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td width="45">yyyyy</td>
</tr>
</table>
</td>
</tr>

<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td align="left">LOG:</td>
<td align="left"><textarea class="form-control" name="Texto" id="Texto" style="width:99%;height:300px"><?php



$fh1 = fopen($GLOBALS['site_shell_url']."files/bk/log".$url2, 'r') or die("Unable to open file!");
//echo fgets($fh1);
echo fread($fh1,filesize($GLOBALS['site_shell_url']."files/bk/log".$url2));
fclose($fh1);
?></textarea>  <? show_editor_small("oEdit1", "Texto", 880, 250)?></td>
</tr>

</table>*/?>