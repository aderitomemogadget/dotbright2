<?
function show_page_buttons()
{
	global $task_button,$btn_class;
	?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
    <tr>
    	<td align="center" valign="middle" class="t1">
        	<input class="btn <?=$btn_class?>" type="submit" name="Submit" value="<?=$task_button;?> ">
        	<input class="btn btn-secondary" type="button" name="cancelar" value="<? echo write_lang('key_cancelar','Cancelar')?>" onMouseUp="javascript:history.go(-1);">
		</td>
     </tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<?
}
function show_page_tools($filter=0)
{?>
	<div class="panel panel-inverse overflow-hidden m-b-12">
        <div class="panel-heading">
            <h4 style="color:#fff">MOEDA</h4>
        </div>
    </div>
	<table width="100%" border=0 cellspacing=0 cellpadding=0 class="p1">
	<tr>
		<td width=29 class="text-center branco"><input onClick='makeSelection("filter","lista_paginas")' type="checkbox" name="filter" id="filter" value="1"  class="cb"></td>
		<td width="10px" class="text-center branco"></td>
		<td>&nbsp;</td>
	</tr>
	</table>
<? }
function show_page_bottom_tools()
{?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td width="200px" class="t1">&nbsp;Com os selecionados:</td>
		<td width="125px"><select name="flag" id="flag" class="select1">
				<option value="" selected="selected"></option>
				<option value="1">Apagar</option>
				<option value="2">Activar</option>
                <option value="3">Desactivar</option>
			</select>
		</td>
        <td width="5px"></td>
		<td>&nbsp;<input type="submit"  name="submeter" value="aplicar ação" class="btn btn-primary" style="width:120px"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<?
}
function show_fast_tools()
{?>
	<table width="200" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
		<td class="titulo2 text-center branco">FAST TOOLS</td>
	</tr>
	<tr>
		<td class="preto text-center"><input class="btn btn-primary" type="button" style="width:160px" value="<? echo write_lang('key_configurar_nova_moeda','Configurar Nova Moeda')?>" onclick="javascript:window.open('pagina.php?ID=0&ID_ORG=0&Dump=<?=uniqid("");?>','_self')"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<? }
function linguas_disponiveis($sigla='')
{

$rs = new DataObj;$rs2 = new DataObj;
global $DB_host,$DB_user,$DB_pass,$DB_name,$prev,$online_edit;
$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

$sql = 'SELECT L.Sigla, L.Cabecalho FROM linguas AS L';
$rs->db_open($db_link,$DB_name,$sql);
if(!$rs->EOF())
{
	echo '<select name="Sigla" data-placeholder="Escolha uma Lingua" class="form-control" style="width:200px;">';
	while(!$rs->EOF())
	{
		echo '<option value="'.$rs->db_data['Sigla'].'"'; if($sigla==$rs->db_data['Sigla']){echo 'selected="selected"';} echo '>'.$rs->db_data['Cabecalho'].'</option>';
		$rs->db_next();
	}
	echo '</select>';
}
$rs->db_close();

}
function getCurrencyCountriesAll($countries_id=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo,$from;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;$rs2=new DataObj;
	$sql="Select countries_id, countries_name, countries_name2, countries_currency from currency_countries Order by countries_name2";
	$db_res=$rs2->db_open($db_link,$DB_name,$sql);
	if(!$rs2->EOF())
	{
		echo '<select name="countries_id" data-placeholder="Escolha uma Moeda" class="form-control" style="width:300px;" tabindex="2">';
		while(!$rs2->EOF())
		{
			echo '<option value="'.$rs2->db_data['countries_id'].'"'; if($countries_id==$rs2->db_data['countries_id']){ echo 'selected="selected"';} echo'>'.$rs2->db_data["countries_name2"]." | ".$rs2->db_data["countries_currency"].'</option>';
			$rs2->db_next();
		}
		echo '</select>';
	}
	$rs2->db_close();
}
function getCountriesAll($countryx=0)
{
	global $site_http_url;
	include "../../external_api/countries/countries.php";

	echo '<select name="country" data-placeholder="Escolha uma Moeda" class="form-control" style="width:300px;" tabindex="2">';
	foreach($countries as $key => $country)
	{
		echo '<option value="'.$key.'"'; if($countryx==$key){ echo 'selected="selected"';} echo'>'.$country[1].'</option>';

	}
	echo '</select>';

}
function getCountry($country)
{
	global $site_http_url;
	include "../../external_api/countries/countries.php";
	return $countries[$country][1];
}
function getCurrencyCountries($coutries_id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo,$from;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs2 = new DataObj;
	$sql="Select countries_name, countries_name2, countries_currency from currency_countries WHERE countries_id=$coutries_id";
	$db_res = $rs2->db_open($db_link,$DB_name,$sql);
	if(!$rs2->EOF()) return array($rs2->db_data["countries_name"],$rs2->db_data["countries_name2"],trim($rs2->db_data["countries_currency"]));
	$rs2->db_close();

}
function getCurrencyLanguage($coutries_lang='')
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo,$from;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs2 = new DataObj;
	$sql="SELECT L.Sigla, L.Cabecalho FROM linguas AS L WHERE L.Sigla='$coutries_lang'";
	$db_res = $rs2->db_open($db_link,$DB_name,$sql);
	if(!$rs2->EOF()) return $rs2->db_data["Cabecalho"];
	$rs2->db_close();

}
function show_content_list()
{
	$path="../../core/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo,$from;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;$rs2=new DataObj;

	$contador+=5;
	if(!$from)$from=0;
	$qtd=30;
	$sql_all="Select * from currency";
	$sql="Select * from currency limit $from,$qtd";
	$rs->db_open($db_link,$DB_name,$sql_all);
	if(!$rs->EOF())$total=$rs->db_rows;
	$rs->db_close();

	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	?><table border=0 cellspacing=0 cellpadding=0 width="100%" style="background:#283130">
	<tr>
	<td width="29" class="text-center branco">&nbsp;</td>
	<td width="4" align="right">&nbsp;</td>
	<td width="29" class="text-center branco">&nbsp;</td>
	<td valign="middle" class="branco" ><b><? echo write_lang('key_pais','País')?> | <? echo write_lang('key_pais','Moedas')?> | <? echo write_lang('key_abr_linguas','Abr. - Lingua')?></b></td>
	<td valign="middle" class="branco" width="85" align="left"  style="font-size:11px"><b><? echo write_lang('key_valor_moeda','Valor Moeda')?></b></td>
	<td valign="middle" class="text-center branco" width="95" style="font-size:11px" ><b><? echo write_lang('key_atualiza_moeda','Atualiza Moeda')?></b></td>
	<td valign="middle" width="35">&nbsp;</td>
	<td width="100" align="right" class=preto>&nbsp;</td>
	</tr>
	<?
	while(!$rs->EOF())
	{
		$count++;
		$currency_id=$rs->db_data["currency_id"];
		$country=$rs->db_data["country"];
		$currency_country=$rs->db_data["currency_country"];
		$currency_lingua=$rs->db_data["currency_lingua"];
		$currency_estado=$rs->db_data["currency_estado"];
		$currency_value=$rs->db_data["currency_value"];
		$currency_symbol=$rs->db_data["currency_symbol"];
		$currency_update=$rs->db_data["currency_update"];
		$currency_lance=$rs->db_data["currency_lance"];
		if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
		else				{$cor="#FFFFFF";$class="r2";$n="2";}
		$i=0;

		$currency = getCurrencyCountries($currency_country);
		?>
		<tr bgcolor="<?=$cor?>" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
			<td width="29" class="preto" align="center"><input type="checkbox" name="chk_<?=$IDb?>" id="chk_<?=$IDb?>" value="<?=$IDb?>" class="cb"></td>
			<td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg"></td>
			<td width="29" class="preto" align="center"><img src="../images/page.png"></td>
            <td valign="middle"><a title="<? echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?currency_id=<?=$currency_id;?>&Dump=<? echo uniqid("");?>"><? echo getCountry($country).' | '.$currency[1].' | '.$currency[2]?> - <?=getCurrencyLanguage($currency_lingua)?> </a></td>
            <td valign="middle" width="70" align="left" class="preto" style="font-size:11px"><? echo $currency_symbol.' '.$currency_value?></td>
			<td valign="middle" class="preto" align="center" width="65" style="font-size:11px"><? if($currency_update) echo write_lang('key_automatico','Automático'); else echo  write_lang('key_manual','Manual');?></td>
			<td valign="middle" width="35"><img src="<? if($currency_estado) echo "ok.png"; else echo "hidden.png";?>" border=0 align="absmiddle"></td>
			<td width="100" align="right" class=preto><a class="italico" href="pagina.php?currency_id=<?=$currency_id?>&task=apagar&Dump=<? echo uniqid("");?>"><img title="<? echo write_lang('key_apagar','Apagar')?>" src="del.png" border=0></a></td>
		</tr>
		<?
		$rs->db_next();
		$i++;
	}
	?></table><?
	$rs->db_close();
	show_paginacao_bk($total,$qtd);
}
function show_content_from_search($reg)
{
	if(!$reg)return false;
	$path="../../core/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql="Select * from packs WHERE ID IN($reg) order by Nome ASC";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	while(!$rs->EOF())
	{
		$count++;
		$IDb=$rs->db_data["ID"];
		$Sigla=$rs->db_data["Sigla"];
		$designacao=$rs->db_data["Nickname"]. " -- ".$rs->db_data["Nome"];
		if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
		else				{$cor="#FFFFFF";$class="r2";$n="2";}
		$i=0;
		?><table border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
		<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
			<td width="29" class="text-center branco"><input type="checkbox" name="chk_<?=$IDb?>" id="chk_<?=$IDb?>" value="<?=$IDb?>" class="cb"></td>
			<td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg"></td>

			<td valign="middle" width="35"><? while($i<$contador){echo "&nbsp;";$i++;}?><img title="editar cliente" src="ok.png" border=0 align="absmiddle"></td>
            <td valign="middle"><a title="<? echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&Dump=<? echo uniqid("");?>"><? if($designacao){echo htmlentities_b(stripslashes($designacao));} else echo "[Editar]";?></a></td>
			<td width="100" align="right" class=preto><a class="italico" href="pagina.php?ID=<?=$IDb?>&ID_ORG=<? echo "$ID_ORG";?>&task=apagar&Dump=<? echo uniqid("");?>"><img title="<? echo write_lang('key_apagar','Apagar')?>" src="del.png" border=0></a></td>
		</tr>
		</table><?
		$rs->db_next();
		$i++;
	}
	$rs->db_close();
}
function currency_convert($Amount,$currencyfrom,$currencyto)
{
	$json = json_decode(file_get_contents('http://rate-exchange.appspot.com/currency?from='.$currencyfrom.'&to='.$currencyto));
	return $json->rate;
}
function getCurrency($countries_id)
{
	$currency_taxa = '1.00';
	$currency = getCurrencyCountries($countries_id);
	if($currency[2] != 'EUR')
	{
		$currency_taxa = currency_convert('1','EUR',$currency[2]);
		// echo $currency_taxa;
		$currency_taxa = preg_replace("/[^0-9,.]/", "",$currency_taxa);

	}
	return $currency_taxa;
}
function InsertNewRecord()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$countries_id 		= intval($_POST['countries_id']);
	$country 			= $_POST['country'];
	$sigla 				= $_POST['Sigla'];
	$currency_symbol 	= $_POST['currency_symbol'];
	$currency_update 	= intval($_POST['currency_update']);
	$currency_value 	= floatval($_POST['currency_value']);
	$currency_estado 	= intval($_POST['currency_estado']);

	$slashes = $_POST['slashes'];
	if($slashes=="'")
	{
		$country=addslashes($country);
		$sigla=addslashes($sigla);
		$currency_symbol=addslashes($currency_symbol);
	}
	if($currency_update)
	{
		$currency_value = getCurrency($countries_id);
	}

	$img_peq1['name']=$_FILES['img_peq1']['name'];
	$img_peq1['temp']=$_FILES['img_peq1']['tmp_name'];
	$img_path="../../files/currency/";
	$altera_img=0;
	$img_ext='';
	if($img_peq1['name']){$altera_img = 1; $pos=strrpos($img_peq1['name'],'.'); $img_ext = substr($img_peq1['name'],$pos+1,strlen($img_peq1['name'])+1-$pos);	}

	$sql = 'INSERT INTO currency(currency_country,currency_lingua,currency_estado,country,currency_value,currency_symbol,currency_update,img_ext) values ("'.$countries_id.'","'.$sigla.'","'.$currency_estado.'","'.$country.'","'.$currency_value.'","'.$currency_symbol.'","'.$currency_update.'","'.$img_ext.'")';
	$res = Runquery($db_link,$DB_name,$sql);
	if($res)
	{
		$new_id = db_get_last_insert_id($db_link);
		if($altera_img)	copia_segura($img_peq1["temp"],$img_path.$new_id."_1.".$img_ext);
	}
	else echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
function DeleteRecord()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$currency_id 		= intval($_GET['currency_id']);
	$res=Runquery($db_link,$DB_name,'delete from currency where currency_id="'.$currency_id.'"');
	if(!$res)echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
function ChangeRecord()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$altera=0;
	$currency_id 		= intval($_GET['currency_id']);
	$rs->db_open($db_link,$DB_name,'Select * from currency WHERE currency_id="'.$currency_id.'"');
	if(!$rs->EOF())
	{
		$altera=1;
		$img_ext=$rs->db_data["img_ext"];
	}
	$rs->db_close();

	if(!$altera)
	{
		return InsertNewRecord();
	}
	else
	{
		$countries_id 		= intval($_POST['countries_id']);
		$country 			= $_POST['country'];
		$sigla 				= $_POST['Sigla'];
		$currency_symbol 	= $_POST['currency_symbol'];
		$currency_update 	= intval($_POST['currency_update']);
		$currency_value 	= floatval($_POST['currency_value']);
		$currency_estado 	= intval($_POST['currency_estado']);

		$slashes = $_POST['slashes'];
		if($slashes=="'")
		{
			$country=addslashes($country);
			$sigla=addslashes($sigla);
			$currency_symbol=addslashes($currency_symbol);
		}
		if($currency_update)
		{
			$currency_value = getCurrency($countries_id);
		}

		$img_peq1['name']=$_FILES['img_peq1']['name'];
		$img_peq1['temp']=$_FILES['img_peq1']['tmp_name'];
		$tem_foto1 		= intval($_POST['tem_foto1']);
		$Apagar_img1 	= intval($_POST['Apagar_img1']);
		$img_path="../../files/currency/";
		$altera_img=0;
		if($img_peq1['name']){$altera_img = 1; $pos=strrpos($img_peq1['name'],'.'); $img_ext = substr($img_peq1['name'],$pos+1,strlen($img_peq1['name'])+1-$pos);	}

		if($tem_foto1 && $Apagar_img1){$ficheiro1 = $currency_id."_1.".$img_ext;unlink($img_path.$ficheiro1); if(!$altera_img) $img_ext='';}
		if($altera_img) copia_segura($img_peq1["temp"],$img_path.$currency_id."_1.".$img_ext);

		$sql = 'UPDATE
					currency
				SET
					img_ext="'.$img_ext.'",
					country="'.$country.'",
					currency_country="'.$countries_id.'",
					currency_lingua="'.$sigla.'",
					currency_estado="'.$currency_estado.'",
					currency_value="'.$currency_value.'",
					currency_symbol="'.$currency_symbol.'",
					currency_update="'.$currency_update.'"
				WHERE
					currency_id="'.$currency_id.'"';
		$res=Runquery($db_link,$DB_name,$sql);
		db_closedb_conection($db_link);
		return($res);
	}
}
function SearchRecords($d)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$d=strtolower(ltrim(rtrim($d)));
	$sql="SELECT distinct ID,
	lower(Nome) as Descricao,length(lower(Nome)) AS len,
	((length(Nome)-length(replace(lower(Nome),'".$d."','')))/length('".$d."'))/(length(lower(Nome))+1-length(replace(lower(Nome),' ','')) ) AS occwds,
	((length(Nome)-length(replace(lower(Nome),'".$d."','')))/length('".$d."')) AS occ
	FROM packs
	WHERE (lower(Nome) LIKE '%".$d."%') ORDER BY occ DESC,occwds DESC  ";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	if(!$db_res) echo "ERRO";
	return $rs;
}
function show_paginacao_bk($total,$qtd)
{
	global $from;
	if($total>$qtd)
	{
		$pageURL=get_uri();
		if(substr_count($pageURL,"&from"))$pageURL=substr($pageURL,0,strpos($pageURL,"&from"));
		if(substr_count($pageURL,"?from")){$pageURL=substr($pageURL,0,strpos($pageURL,"?from"));}
		if(substr_count($pageURL,"?")>0)$op="&";else $op="?";
		$i=0;
		echo '<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
		    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
			<tr>
				<td class="t1" style="text-align:center">';



		echo "page ";
		while($i<($total/$qtd))
		{
			if($i>0)echo " - ";
			if($from==($i*$qtd))echo ($i+1);
			else echo "<a class='numbers' href='".$pageURL.$op."from=".($i*$qtd)."'>".($i+1)."</a>";
			$i++;
		}
		echo "</td></tr></table>";
	}
}
?>