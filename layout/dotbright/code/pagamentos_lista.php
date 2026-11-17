<?php
$includes_path="../includes/";
include $includes_path . "includes.inc.php";
include "../templates/topo_pastas.inc.php";

$nome="Pagamentos";
$tabela="pagamentos_linhas";
$permissao=get_permissoes2($nome, "PAG", "bottom_pastas");

if(!isset($lasturl) || $lasturl=="") $lasturl=getenv('HTTP_REFERER');
if($lasturl=="") $lasturl="facturacao.php";
if ($accao=="apagar") { $Action="Apagar"; $Form="apaga"; }
else { $Action="Gravar"; }

if(!$data_ini) $data_ini=date ("Y") . "-" . date ("m") . "-" . date ("d");
carrega_funcoes_calendario();
?>
<td valign=top>
<script type="text/javascript">
var IE=document.all?true:false;
if (!IE) document.captureEvents(Event.MOUSEMOVE)

function mostra(name,mostra,campo)
{
	if(mostra)
	{
		var el=document.getElementById(name);
		el.style.display="block";
	}
}
function popitup(url) {
	newwindow=window.open(url,'Imprime', 'scrollbars=yes, resizable=0, width=800 height=600');
	if (window.focus) {newwindow.focus()}
	return false;
}

function roundit(Num, Places)
{
	if (Places > 0)
	{
		if ((Num.toString().length - Num.toString().lastIndexOf('.')) > (Places + 1)) return Math.round(Num * (Math.pow(10, Places))) / (Math.pow(10, Places));
		else return Num;
	}
	else return Math.round(Num);
}
function isInteger(value) {	return value==parseInt(value);}
function checkInt(field) {
	if(!isInteger(field.value)){alert("Por favor insira um numero inteiro!");field.value="";}
	else if(field.value<1){alert("Por favor insira um numero inteiro positivo!");field.value="";}
}
function imprimir(event, tabela, id, url)
{
	mostra=0;
	popup_activa=0;
	document.getElementById('conteudo').style.width="200px";
	if(!isIE && document.getElementById('conteudo').style.visibility!="visible") mostra=1;
	else
	{
    	if (document.layers && document.layers['conteudo'] !=null && document.layers['conteudo'].visibility!='visible'){
			mostra=1;
		}
		else if (document.all && document.all['conteudo'].style.visibility!='visible'){
			mostra=1;
		}
	}
	if(mostra==1)
	{
		carrega_pagina('imprimir_ajax.php?tabela='+ tabela +'&ID='+id + url,"conteudo");
		var pos_y=(document.body.clientHeight/2) - (280 / 2);
		var pos_x=(document.body.clientWidth) - (840 / 2);
		reposition('conteudo',pos_x,pos_y);
		show('conteudo');
	}
}
function imprimir2(_url, url, pagina, tipo, modelo)
{
    hide('conteudo');
    popitup(_url+"impressoes/"+pagina+""+url+"&tipo="+tipo+"&modelo="+modelo);
}
</script>
 <?
$pagamento=""; $n_factura=""; $n_contrib=""; $design=""; $morada=""; $localiadade=""; $codpos1=""; $codpos2=""; $codpos3="";

if($ID!=0)
	{
		global $Server, $UserName, $db_password, $lang_crm, $theme, $ss_user;
		$hConn=OpenConnection($Server, $UserName, $db_password);
	 	$dados=new clsRecordset;
		if(!isset($t))	$t="facturacao";
		else			$t=base64_decode($t);
		$sql="Select * from $t where ID=$ID";
		$hRes=$dados->Open($hConn, $DBName, $sql);
		if($dados)
		{
			$ID=$dados->aFields["ID"];
			$ID2=$dados->aFields["ID2"];
			$fechado=$dados->aFields["Fechado"];
			$n_contrib=$dados->aFields["N_Contribuinte"];
			$tipodoc=$dados->aFields["TipoDoc"];
			$data_ini=$dados->aFields["Data_emissao"];
			$n_contrib=$dados->aFields["N_Contribuinte"];
			$b_anulado=$dados->aFields["Anulado"];
			$design=$dados->aFields["Des_Social"];
			$morada=$dados->aFields["Morada"];
			$localidade=$dados->aFields["Localidade"];
			$codpos1=$dados->aFields["Cod_Pos1"];
			$codpos2=$dados->aFields["Cod_Pos2"];
			$codpos3=$dados->aFields["Cod_Pos3"];
			$obs=$dados->aFields["Obs"];

			$totaldoc=$dados->aFields["TotalDocComIva"];

			if($dados->aFields["TotalDocRecibos"])$totalregularizado=$dados->aFields["TotalDocRecibos"];
			else	$totalregularizado=$dados->aFields["TotalDocRecebimentos"];


			$totalcreditos=$dados->aFields["TotalDocCreditos"];
		}
		$dados->close();

		$totalpendente=$totaldoc-$totalcreditos-$totalregularizado;
		$obj=new clsRecordset;
		$obj->Open($hConn, $DBName, "SELECT count(*) as total from documentos_pendentes where TipoDoc='" . $tipodoc . "' and ID_ORG=" . $ID2 . " and ValorPend>0");
		$tempendente=$obj->aFields["total"];
		$obj->close();
	}
 ?>
<td valign=top>
	<table style="width:100%; height:31px;" bgcolor="<?=$cor_fundo;?>" cellspacing=0 background="../templates/<?=$theme?>/bgtit.jpg">
		<tr>
			<td width=22 height=31><img src="../templates/nada.gif" width=4 height=25><img src="../templates/<?=$theme?>/bullet_grey.gif" width=18 height=25></td>
			<td height=31 class="branco" style="vertical-align:middle"><b>&nbsp;Pagamentos</b></td>
			<td>&nbsp;</td>
		</tr>
	</table>
	<table width="100%" cellspacing="0" cellpadding=1 border="0" bgcolor="#F1F4F6">
		<tr>
			<td class="topo">
				<?php if(!$fechado) $erro="Não é possível gerar pagamentos sem que o documento esteja fechado";
				else{
					if($tempendente==0) $erro="O documento não tem pendentes";
					else{?>
						<a class="preto" href="pagamentos_edita.php?ID=0&ID_ORG=0&doc1=<?=$ID2?> - <?=$tipodoc?>&n_contrib=<?=$n_contrib?>"><img title="Adicionar um Novo Documento" src="<?=$site_path?>../templates/<?=$theme?>/new.png" width="16" height="16" border="0" align="absmiddle" /></a>
				<?php }
				}?>
				&nbsp;LISTA DE PAGAMENTOS<br />
				<?php if($erro) echo $erro;?>
			</td>
		</tr>
	</table>
	<table cellspacing="0" cellpadding=1 border="0" bgcolor="#F1F4F6">
		<tr height="20px">
			<td width="10px">&nbsp;</td>
			<td	class="titulos_campos" width="20px">&nbsp;</td>
			<td class="titulos_campos" width="70">TIPO DOC</td>
			<td class="titulos_campos" width="100">ID</td>
			<td width="80" class="titulos_campos">DATA</td>
			<td width="250" class="titulos_campos">REFER&Ecirc;NCIA</td>
			<td width="100" class="titulos_campos">REGULARIZADO</td>
			<td width="100" class="titulos_campos">T.FINANCEIRO</td>
			<td width="80" class="titulos_campos">ANULADO</td>
			<td width="80" class="titulos_campos">FECHADO</td>
			<td class="titulos_campos" width="20px">&nbsp;</td>
		</tr>
		<?
		$dados=new clsRecordset();
		$dados2=new clsRecordset();
		$dados->open($hConn,$DBName,"select pagamentos.ID as IDCab, pagamentos.ID2 as ID2Cab,pagamentos.TipoDoc as TipoDocCab, Data,Anulado,Fechado, pagamentos_linhas.* from pagamentos_linhas, pagamentos where pagamentos_linhas.N_Doc=" . $ID2 . "  and pagamentos_linhas.TipoDoc='" . $tipodoc . "' and pagamentos.ID=pagamentos_linhas.ID_ORG");
		while(!$dados->EOF())
		{
			$data=$dados->aFields["Data"];
			$IDpagamento=$dados->aFields["IDCab"];
			$ID2pagamento=$dados->aFields["ID2Cab"];
			$TipoDocpagamento=$dados->aFields["TipoDocCab"];
			$documento=$dados->aFields["Documento"];
			$b_anulado="";
			if ($dados->aFields["Anulado"]) $b_anulado= " checked ";
			$b_fechado="";
			if ($dados->aFields["Fechado"]) $b_fechado= " checked ";

			$regularizado=number_format($dados->aFields["Regularizado"],2,',','.');

			$sql="SELECT sum(Valor) as total from pagamentos_financeira where ID_ORG=" . $IDpagamento ." and TipoDoc='" . $TipoDocpagamento ."'";
			$dados2->Open($hConn, $DBName, $sql);
			$financeiro=$dados2->aFields["total"];
			$financeiro=number_format($financeiro,2,',','.');
			$dados2->close();

			if($count % 2 != 0)	$cor="#E0E0E0";
			else				$cor="#F6F6F6";?>
			<tr bgcolor="<?=$cor;?>" onmouseover="this.style.backgroundColor='#DBE7F1'; this.style.cursor='pointer';" onmouseout="this.style.backgroundColor='<?=$cor;?>'" onDblClick="javascript:abrir('pagamentos_edita.php?ID=<?=$ID;?>')">
				<td width="10px">&nbsp;</td>
				<td class="texto">&nbsp;</td>
				<td class="texto" title="<?=$TipoDocpagamento?>"><a class="preto" href="pagamentos_edita.php?ID=<?=$IDpagamento?>"><? echo $TipoDocpagamento; ?></a></td>
				<td class="texto" title="<?=$ID2pagamento?>"><a class="preto" href="pagamentos_edita.php?ID=<?=$IDpagamento?>"><? echo $ID2pagamento; ?></a></td>
				<td class="texto" title="<?=$data_ini?>"><?=$data_ini?></td>
				<td class="texto" title="<?=$documento?>"><?=$documento;?></td>
				<td class="texto" style="text-align:right" title="<?=$regularizado?>"><?=$regularizado;?></td>
				<td class="texto" style="text-align:right" title="<?=$financeiro?>"><?=$financeiro;?></td>
				<td class="texto"><input disabled type='checkbox' value=1 <?=$b_anulado?> /></td>
				<td class="texto"><input disabled type='checkbox' value=1 <?=$b_fechado?> /></td>
				<td class="texto" nowrap="nowrap">
					<img src="../templates/<?=$theme?>/print.gif" align="absmiddle" border="0" onclick="imprimir(event, 'pagamentos','<?=$IDpagamento?>','')" title="Imprimir Documento" style="cursor:pointer"/>
				</td>
			</tr>
			<?

			$dados->next();
		}
		$dados->close();
			?>
	</table>
</form>



<script type="text/javascript">
function inserirLinha(theme) {
	var oRows=document.getElementById('IDTabelaLinhas').getElementsByTagName('tr');
	numOfRows=oRows.length-1;


	if(jQuery('#preco' + numOfRows).val()!="" && (jQuery('#data' + numOfRows).val()!="" || jQuery('#desig' + numOfRows).val()!=""))
	{
		numOfRows=eval(numOfRows)+1;
		jQuery('#totallinhas').val(numOfRows);
		j=eval(numOfRows + 1)



		var str='<tr id="linha' + numOfRows + '">';
		str +='<td>&nbsp;</td><td class="preto" align="center">';
		str +='<input type="text" name="data'+ numOfRows + '" id="data'+ numOfRows + '" style="width: 80px; height: 17px;" class="small" value="" onchange=\"javascript:inserirLinha(\'' + theme + '\');\" />';
		str +=' <img src="../templates/calend.jpg" border="0" align="absmiddle" onclick="showCalendar(\'\',document.getElementById(\'data'+ numOfRows + '\'),document.getElementById(\'data'+ numOfRows + '\'),\'\',\'holder1\',0,30,1)" style="cursor:pointer;" />';
		str +='</td>';

		str+='<td align="center" class="preto">';
		str+='<input type="text" name="desig'+numOfRows+'" style="width:100%;HEIGHT: 18px;" class="small" value="" onchange=\"javascript:inserirLinha(\'' + theme + '\');\"></td>';

		str+='<td align="center" class="preto"><input type="text" name="preco'+numOfRows+'" id="preco'+numOfRows+'" style="width:100%;HEIGHT: 18px; text-align:right" class="small" value="" onchange=\"javascript:calcularTotais();inserirLinha(\'' + theme + '\');\"></td>';

		str +="<td valign=\"middle\"><img alt=\"Apagar\" src=\"../templates/" + theme + "/crm_apagar_1.jpg\" width=\"19\" height=\"16\" border=\"0\" align=\"absmiddle\" onclick=\"javascript:ApagarLinhas(" + numOfRows + ")\" id=\"Apagar" + numOfRows + "\" />";
		str+='<input type="hidden" name="ID_linha' + numOfRows + '" id="ID_linha' + numOfRows + '" value="" /></td>';
		str +="</tr>";

		var newItem=$(str)

		$("#IDTabelaLinhas> tbody").append(newItem);

		j=eval(numOfRows-1);
		document.getElementById("Apagar" + j).style.display="block";
		document.getElementById("Apagar" + numOfRows).style.display="none";
		//if(document.getElementById("sel_armazem"))document.getElementById("sel_armazem").disabled="true";
	}
	return numOfRows;
}

function ApagarLinhas(i)
{
	if((eval("document.form.ID_linha" + i) && eval("document.form.ID_linha" + i + ".value")!="") || eval("document.form.data" + i + ".value")!="" || eval("document.form.preco" + i + ".value")!="" || eval("document.form.desig" + i + ".value")!="")
	{
		confirmar=confirm("Tem a certeza que pretende apagar esta linha?");
		if (confirmar)
		{
			limparCampos(i);
			calcularTotais();
		}
	}
}

function limparCampos(num_campo)
{
	eval("document.form.data" + num_campo + ".value=''");
	document.getElementById("data" + num_campo).style.display='none';
	eval("document.form.preco" + num_campo + ".value=''");
	document.getElementById("preco" + num_campo).style.display='none';
	eval("document.form.desig" + num_campo + ".value=''");
	document.getElementById("desig" + num_campo).style.display='none';
	document.getElementById("linha" + num_campo).className='hidden';
	document.getElementById("linha" + num_campo).style.display="none";

	if(jQuery('#Apagar' + num_campo).attr("id")) document.getElementById("Apagar" + num_campo).style.visibility="hidden";
}

function calcularTotais()
{
	var _total=0;
	var aux=0;
	var oRows=document.getElementById('IDTabelaLinhas').getElementsByTagName('tr');
	numOfRows=oRows.length-1;
	for(i=1; i<=numOfRows; i++){
		if (jQuery('#preco' + i).attr("id") && jQuery('#preco' + i).val()!=""){
			_total+=Number(jQuery('#preco' + i).val());
		}
	}
	document.getElementById("resultado").innerHTML=roundit(_total,2);
}
function validanumero(campo)
{
	if(jQuery('#' + campo).attr("id"))
	{
		if(isNaN(eval("document.form." + campo +".value")))
		{
			var aux=eval("document.form." + campo +".value").replace(',','.');
			aux=aux.replace(/[^0-9.]/g,'');
			eval("document.form." + campo +".value='" + aux + "'");
		}
		return eval("document.form." + campo +".value");
	}
	return 0;
}
</script>
<? include "../templates/bottom_pastas.inc.php"; ?>