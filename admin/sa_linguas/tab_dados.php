<table width="100%" cellpadding="0" cellspacing="0" border="0"><input type="hidden" name="bloco_lang" id="bloco_lang" class="form-control" value="<?php echo $GLOBALS['admin_lang']?>" style="width:50px"/>
<tr><td colspan="2">&nbsp;</td></tr>


<tr>
<td align="left" width="100">Chave:</td>
<td align="left"><input class="form-control" type="text" name="bloco_titulo" id="bloco_titulo" onkeypress="return validateQty(event);" value="<?php echo $bloco_titulo;?>" style="width:88%"/></td>
<td align="left" width="300"><a href="#" onclick="transform_all();return false;" class="preto">Traduzir tudo</a></td>
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
	$first_lingua = $rs2->db_data['Sigla'];
	$s='';
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
		<td align="left"><textarea style="width:88%;height:80px" class="form-control" type="text" name="bloco_texto_<?php echo $rs2->db_data['Sigla']?>" id="bloco_texto_<?php echo $rs2->db_data['Sigla']?>" ><?php echo $bloco_texto;?></textarea></td>
		<td align="left" width="300">
		<?php if($rs2->db_data['Sigla']!='pt'){?><a href="#" onclick="transform('trad', 'bloco_texto_<?php echo $rs2->db_data['Sigla']?>','<?php echo $first_lingua?>','<?php echo str_replace('ho', 'ne', $rs2->db_data['Sigla'])?>');return false;" class="preto">Traduzir</a> | <?php }?>
		<a href="#" onclick="transform('up', 'bloco_texto_<?php echo $rs2->db_data['Sigla']?>');return false;" class="preto">Uppercase</a> |
		<a href="#" onclick="transform('lo', 'bloco_texto_<?php echo $rs2->db_data['Sigla']?>');return false;" class="preto">Lowecase</a>
		</tr>
		<?php

		$s.="transform('trad', 'bloco_texto_".$rs2->db_data['Sigla']."','".$first_lingua."','".str_replace('ho', 'ne', $rs2->db_data['Sigla'])."');\n";
		$rs2->db_next();
	}
	$rs2->db_close();

}
$rs2->db_close();
?>
</table>
<script>
function transform_all(t, input, lfrom, lto)
{
 <?=$s?>
}
function transform(t, input, lfrom, lto)
{

	if(t=='up') document.getElementById(input).value = document.getElementById(input).value.toUpperCase();
	else if(t=='lo') document.getElementById(input).value = document.getElementById(input).value.toLowerCase();
	else if(t=='trad')
	{

		/*
		https://tech.yandex.com/keys/?service=trnsl
		login: aderito.fernandes
		pass: verbatim
		key:   trnsl.1.1.20160826T172445Z.ad542005a80e547d.9ff6f4f7d599e4951974daafc13d5dd10bd89613

		https://translate.yandex.net/api/v1.5/tr.json/translate ?
		key=<API key>
		 & text to translate=<text to translate>
		 & translation direction=<translation direction>
		 & [format=<text format>]
		 & [options=<translation options>]
		 & [callback=<name of the callback function>]

		 */

		 if(lto=='ne')lto='nl';

		$.ajax({
			type : "GET",
			url : "https://translate.yandex.net/api/v1.5/tr.json/translate",
			dataType : 'html',
			cache: false,
			contentType: "application/x-www-form-urlencoded; charset=UTF-8",
			/*data : "url=http://api.apertium.org/json/translate?q="+$("#bloco_texto_pt").val()+"&langpair=pt|"+lto+"&callback=foo",*/
			data : "key=trnsl.1.1.20160826T172445Z.ad542005a80e547d.9ff6f4f7d599e4951974daafc13d5dd10bd89613&text="+$("#bloco_texto_pt").val()+"&lang=pt-"+lto+"&format=html",
			success : function(iData)
			{
				console.log(iData);
				obj = JSON.parse(iData);
				console.log(obj["text"]);

				$("#"+input).val(obj["text"]);
			},
			error:function (xhr, ajaxOptions, thrownError){ alert(1);}
		});




		// document.getElementById(input).value = document.getElementById(input).value.toUpperCase();
		/*
		console.log("http://api.apertium.org/json/translate?q="+$("#bloco_texto_pt").val()+"&langpair=pt|"+lto+"&callback=foo");

		$.ajax({
			type : "GET",
			/*url : "get_lingua.php",
			url : "http://api.apertium.org/json/translate",
			dataType : 'html',
			cache: false,
			contentType: "application/x-www-form-urlencoded; charset=UTF-8",
			/*data : "url=http://api.apertium.org/json/translate?q="+$("#bloco_texto_pt").val()+"&langpair=pt|"+lto+"&callback=foo",
			data : "q="+$("#bloco_texto_pt").val()+"&langpair=pt|"+lto+"&callback=foo",
			success : function(iData)
			{
				console.log(iData)

				//$("#"+input).val(iData["responseData"]["translatedText"]);
			},
			error:function (xhr, ajaxOptions, thrownError){ alert(1);}
		});*/


	}
}

</script>