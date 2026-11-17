<?
	include "settings.inc.php";
	include "../layout/header.inc.php";
	include "../layout/body.inc.php";
	$porpagina=15;
	if ($accao == "apagar")	{$Action="Apagar"; $Form="apaga";}
	elseif ($ID == 0)		{$Action="Inserir"; $Form="insere";}
	else					{$Action="Alterar"; $Form="altera";}

	function BuscarArvore_gerada_cf($id_org)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$obj=new DataObj;
		$pai=$id_org;
		while($pai>0)
		{
			$id_org=$pai;
			$sql="SELECT ID,ID_ORG FROM products_families WHERE ID=$pai";
			$hRes=$obj->db_open($db_link,$DB_name,$sql);
			$pai=$obj->db_data["ID_ORG"];
			$arvore2[]=$obj->db_data["ID"];
		}
		$output="";
		$t=(count($arvore2)-1);
		for($a=0;$a<count($arvore2);$a++)
		{
			$output[$t]=$arvore2[$a];
			$t--;
		}
		ksort($output);
		return $output;
	}
	function getMarcas()
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$obj=new DataObj;
		$sql="select brands.ID, brands_lang.Nome from brands inner join brands_lang on brands.ID=brands_lang.ID_PAI order by brands_lang.Nome ASC";
		$obj->db_open($db_link,$DB_name,$sql);

		$data = array();
		while (!$obj->EOF())
		{
			$temp = array();
			$temp[] = $obj->db_data['ID'];
			$temp[] = $obj->db_data['Nome'];
			$data[] = $temp;

			$obj->db_next();
		}
		$obj->db_close();

		return $data;
	}

	function getFamilias()
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$obj=new DataObj;
		$sql2 = "SELECT pf.ID, pfl.Cabecalho
		FROM products_families pf
		INNER JOIN products_families_lang pfl ON ( pf.ID = pfl.ID_PAI )
		WHERE pf.ID_ORG =0";
		$obj2=new DataObj;
		$obj2->db_open($db_link,$DB_name,$sql2);
		$data = array();
		$temp = array();
		$valor_antigo = 0;
		while(!$obj2->EOF())
		{
			$temp = array();
			$temp[] = $obj2->db_data['ID'];
			$temp[] = $obj2->db_data['Cabecalho'];
			$data[] = $temp;
			$obj2->db_next();
		}
		$obj2->db_close();
		return $data;
	}
	function getFiltros1()
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$obj=new DataObj;
		$sql2 = "SELECT fc.category_id,fcl.category_name FROM filters_categories as fc inner join filters_categories_lang as fcl on fc.category_id=fcl.category_id WHERE fcl.category_lang='".$site_lang."' order by fcl.category_name ASC";
		$obj2=new DataObj;
		$obj2->db_open($db_link,$DB_name,$sql2);
		$data = array();
		$temp = array();
		$valor_antigo = 0;
		while(!$obj2->EOF())
		{

			$temp = array();
			$temp[] = $obj2->db_data['category_id'];
			$temp[] = $obj2->db_data['category_name'];
			$data[] = $temp;
			$obj2->db_next();
		}
		$obj2->db_close();
		return $data;
	}
?>
<div class="center">
	<table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
	<tr>
	<td valign="top" align="left"><?php include "../layout/menus.inc.php"; ?>
	<table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
	<tr>
	<td valign="top">
	<table width="100%" border=0 cellspacing=0 cellpadding="5" class="subtitulo_janela">
	<tr>
	<td valign="middle"><a href="list.php" class="branco"> &#60; Voltar para a lista de Filtros</a></td>
	</tr>
	</table>
	</tr>
	<tr>
	<td align="left" valign="top" class="preto">
	<table width="100%" cellpadding=0 cellspacing=0 border=0 style="background-color:#FFF">
	<tr>
	<td><div style="overflow-x:auto; max-width:1658px; width: 100%;">
		<style>
		a {
    color: #666666;
    font-family: 'Ubuntu',arial;
    font-size: 11px;
    text-decoration: none;
}
a:link {
    color: #666666;
    font-family: 'Ubuntu',arial;
    font-size: 11px;
    text-decoration: none;
}
a:active {
    color: #666666;
    font-family: 'Ubuntu',arial;
    font-size: 11px;
    text-decoration: none;
}
a:hover {
    color: #3399FF;
    font-family: 'Ubuntu',arial;
    font-size: 11px;
    text-decoration: none;
}
#fl_left
{
	float:left;
	width:300px;
	height:100px;
}
.pagina
{
	width: 32px;
	height: 13px;
	border-style:solid;
	border-width:1px;
	text-align:center;
	margin:2px;
	float:left;
}
.pagina:hover
{
	color:white;
	background-color:black;
}

.pagina_pontos
{
	width: 20px;
	height: 13px;
	text-align:center;
	margin:2px;
	float:left;
}
.pagina:hover
{
	color:white;
	background-color:black;
}

.pagina_selecionada
{
	width: 32px;
	height: 13px;
	border-style:solid;
	border-width:1px;
	text-align:center;
	margin:2px;
	float:left;
	color:white;
	background-color:black;
}

td {font-size:10px;}
</style>
<script>
function center_pop(msgbox)
{
	var x = (window.innerWidth / 2) - (document.getElementById(msgbox).offsetWidth / 2);
	var y = (window.offsetHeight / 2) - (document.getElementById(msgbox).offsetHeight / 2);
	return [x,y];
}
function openRelacionados()
{
	window.open("popup_relacionados.php?ID1=<?php echo $ID?>","relacionados","width=800,height=500,scrollbars=1");
}
function openSugestoes()
{
	window.open("popup_sugestoes.php?ID1=<?php echo $ID?>","sugestoes","width=800,height=500,scrollbars=1");
}

function showmorefiltros()
{
	if($("#maisfiltros").is(":visible"))
	{
		$("td[id=maisfiltros]").hide();
		$("#mostrafiltros").html("[+] mais filtros");
	} else
	{
		$("td[id=maisfiltros]").show();
		$("#mostrafiltros").html("[-] menos filtros");
	}

}

function mudaFamilia(id)
{
	var marca = $('#marca_'+id+' option:selected').val();
	var xhr = false;
	if (window.ActiveXObject){
		xhr = new ActiveXObject("Microsoft.XMLHTTP");
	}
	else {
	xhr = new XMLHttpRequest();
	}
	xhr.open("GET","FamiliasByMarca.php?marca="+marca,true); // Insert a reference of the php page you wanna get instead of yourpage.php
	xhr.send(null);
	xhr.onreadystatechange=function()
	  {
	  if (xhr.readyState==4 && xhr.status==200)
		{
		$("#familia_"+id).empty();
		$("#familia_"+id).append(xhr.responseText);
		mudaSubFamilia(id);
		}
	  }

}

function mudaSubFamilia(id)
{
	var familia = $('#familia_'+id+' option:selected').val();
	var xhr = false;
	if (window.ActiveXObject){
		xhr = new ActiveXObject("Microsoft.XMLHTTP");
	}
	else {
		xhr = new XMLHttpRequest();
	}
	xhr.open("GET","SubFamiliaByFamilia.php?familia="+familia,true); // Insert a reference of the php page you wanna get instead of yourpage.php
	xhr.send(null);
	xhr.onreadystatechange=function()
	  {
	  if (xhr.readyState==4 && xhr.status==200)
		{
		$("#subfamilia_"+id).empty();
		$("#subfamilia_"+id).append(xhr.responseText);
		}
	  }
}

function mudaValor(id,nrfiltro)
{
	var categoria = $('#filtro'+nrfiltro+'categoria_'+id+' option:selected').val();
	var xhr = false;
	if (window.ActiveXObject){
		xhr = new ActiveXObject("Microsoft.XMLHTTP");
	}
	else {
		xhr = new XMLHttpRequest();
	}
	xhr.open("GET","ValorFiltroByCategoria.php?categoria="+categoria,true); // Insert a reference of the php page you wanna get instead of yourpage.php
	xhr.send(null);
	xhr.onreadystatechange=function()
	  {
	  if (xhr.readyState==4 && xhr.status==200)
		{
			$('#filtro'+nrfiltro+'valor_'+id).empty();
			$('#filtro'+nrfiltro+'valor_'+id).append(xhr.responseText);
			$('#filtro'+nrfiltro+'valor_'+id).append("<option value=\"-\">--NOVO VALOR--</option>");
		}
	  }
}
function insereNovoValor(id,nrfiltro){
	var categoria = $('#filtro'+nrfiltro+'categoria_'+id+' option:selected').val();
	var valor = $('#novofiltro'+nrfiltro+'valor_'+id).val();
	if(valor != ""){
	var xhr = false;
	if (window.ActiveXObject){
		xhr = new ActiveXObject("Microsoft.XMLHTTP");
	}
	else {
		xhr = new XMLHttpRequest();
	}
	xhr.open("GET","InsereValorByCategoria.php?categoria="+categoria+"&valor="+valor,true); // Insert a reference of the php page you wanna get instead of yourpage.php
	xhr.send(null);
	xhr.onreadystatechange=function()
	  {
	  if (xhr.readyState==4 && xhr.status==200)
		{
			for(var i=0;i<<?php echo $porpagina?>;i++){
				for(var j=0;j<12;j++){
					mudaValor(j,i);
					$("#input"+j+"valor_"+i).hide();
					$('#novofiltro'+j+'valor_'+i).val('');
				}
			}
		}
	  }
	}else {alert("Campo vazio, por favor insira um valor");}
}

function mostraInput(id,nrfiltro){
	var valor = $('#filtro'+nrfiltro+'valor_'+id+' option:selected').text();
	if(valor == "--NOVO VALOR--"){
		$("#input"+nrfiltro+"valor_"+id).show();
	}else $("#input"+nrfiltro+"valor_"+id).hide();

}

function gravaLinha(id){

	var referencia1 = $("input[name=referencia]").eq(id).val();
	var idproduto1 = $("input[name=idproduto]").eq(id).val();
	var marca1 = $("select[name=marca] option:selected").eq(id).val();
	var familia1 = $("select[name=familia] option:selected").eq(id).val();
	var subfamilia1 = $("select[name=subfamilia] option:selected").eq(id).val();
	var categoria1 = $("select[name=filtro1categoria] option:selected").eq(id).val();
	var categoria2 = $("select[name=filtro2categoria] option:selected").eq(id).val();
	var categoria3 = $("select[name=filtro3categoria] option:selected").eq(id).val();
	var categoria4 = $("select[name=filtro4categoria] option:selected").eq(id).val();
	var categoria5 = $("select[name=filtro5categoria] option:selected").eq(id).val();
	var categoria6 = $("select[name=filtro6categoria] option:selected").eq(id).val();
	var categoria7 = $("select[name=filtro7categoria] option:selected").eq(id).val();
	var categoria8 = $("select[name=filtro8categoria] option:selected").eq(id).val();
	var categoria9 = $("select[name=filtro9categoria] option:selected").eq(id).val();
	var categoria10 = $("select[name=filtro10categoria] option:selected").eq(id).val();
	var categoria11 = $("select[name=filtro11categoria] option:selected").eq(id).val();
	var valor1 = $("select[name=filtro1valor] option:selected").eq(id).val();
	var valor2 = $("select[name=filtro2valor] option:selected").eq(id).val();
	var valor3 = $("select[name=filtro3valor] option:selected").eq(id).val();
	var valor4 = $("select[name=filtro4valor] option:selected").eq(id).val();
	var valor5 = $("select[name=filtro5valor] option:selected").eq(id).val();
	var valor6 = $("select[name=filtro6valor] option:selected").eq(id).val();
	var valor7 = $("select[name=filtro7valor] option:selected").eq(id).val();
	var valor8 = $("select[name=filtro8valor] option:selected").eq(id).val();
	var valor9 = $("select[name=filtro9valor] option:selected").eq(id).val();
	var valor10 = $("select[name=filtro10valor] option:selected").eq(id).val();
	var valor11 = $("select[name=filtro11valor] option:selected").eq(id).val();

	var request = $.ajax({
		type: "POST",
		url: "produtos_2_save.php",
		dataType: "html",
		data: {
			referencia: referencia1,
			idproduto: idproduto1,
			marca: marca1,
			familia: familia1,
			subfamilia: subfamilia1,
			filtro1categoria: categoria1,
			filtro2categoria: categoria2,
			filtro3categoria: categoria3,
			filtro4categoria: categoria4,
			filtro5categoria: categoria5,
			filtro6categoria: categoria6,
			filtro7categoria: categoria7,
			filtro8categoria: categoria8,
			filtro9categoria: categoria9,
			filtro10categoria: categoria10,
			filtro11categoria: categoria11,
			filtro1valor: valor1,
			filtro2valor: valor2,
			filtro3valor: valor3,
			filtro4valor: valor4,
			filtro5valor: valor5,
			filtro6valor: valor6,
			filtro7valor: valor7,
			filtro8valor: valor8,
			filtro9valor: valor9,
			filtro10valor: valor10,
			filtro11valor: valor11
		}
     });
    request.done(function(msg) {$("#tr_"+id).css("background-color","#66FF99")});
	request.fail(function(jqXHR, textStatus) {$("#tr_"+id).css("background-color","#CC3333")});
}

function elementInViewport(ele) {
	var el = document.getElementById(ele);
	var top = el.offsetTop;
	var left = el.offsetLeft;
	var width = el.offsetWidth;
	var height = el.offsetHeight;

	while(el.offsetParent) {
		el = el.offsetParent;
		top += el.offsetTop;
		left += el.offsetLeft;
	}

	return (
		top >= window.pageYOffset &&
		left >= window.pageXOffset &&
		(top + height) <= (window.pageYOffset + window.innerHeight) &&
		(left + width) <= (window.pageXOffset + window.innerWidth)
	);
}

function copiaLinha(id){


	$("select[name=marca]").eq(id+1).val($("select[name=marca] option:selected").eq(id).val());

	$("select[name=familia]").eq(id+1).val($("select[name=familia] option:selected").eq(id).val());

	$("select[name=subfamilia]").eq(id+1).html($("select[name=subfamilia]").eq(id).html());
	$("select[name=subfamilia]").eq(id+1).val($("select[name=subfamilia] option:selected").eq(id).val());

	$("select[name=filtro1categoria]").eq(id+1).val($("select[name=filtro1categoria] option:selected").eq(id).val());
	$("select[name=filtro2categoria]").eq(id+1).val($("select[name=filtro2categoria] option:selected").eq(id).val());
	$("select[name=filtro3categoria]").eq(id+1).val($("select[name=filtro3categoria] option:selected").eq(id).val());
	$("select[name=filtro4categoria]").eq(id+1).val($("select[name=filtro4categoria] option:selected").eq(id).val());
	$("select[name=filtro5categoria]").eq(id+1).val($("select[name=filtro5categoria] option:selected").eq(id).val());
	$("select[name=filtro6categoria]").eq(id+1).val($("select[name=filtro6categoria] option:selected").eq(id).val());
	$("select[name=filtro7categoria]").eq(id+1).val($("select[name=filtro7categoria] option:selected").eq(id).val());
	$("select[name=filtro8categoria]").eq(id+1).val($("select[name=filtro8categoria] option:selected").eq(id).val());
	$("select[name=filtro9categoria]").eq(id+1).val($("select[name=filtro9categoria] option:selected").eq(id).val());
	$("select[name=filtro10categoria]").eq(id+1).val($("select[name=filtro10categoria] option:selected").eq(id).val());
	$("select[name=filtro11categoria]").eq(id+1).val($("select[name=filtro11categoria] option:selected").eq(id).val());

	$("select[name=filtro1valor]").eq(id+1).html($("select[name=filtro1valor]").eq(id).html());
	$("select[name=filtro1valor]").eq(id+1).val($("select[name=filtro1valor] option:selected").eq(id).val());

	$("select[name=filtro2valor]").eq(id+1).html($("select[name=filtro2valor]").eq(id).html());
	$("select[name=filtro2valor]").eq(id+1).val($("select[name=filtro2valor] option:selected").eq(id).val());

	$("select[name=filtro3valor]").eq(id+1).html($("select[name=filtro3valor]").eq(id).html());
	$("select[name=filtro3valor]").eq(id+1).val($("select[name=filtro3valor] option:selected").eq(id).val());

	$("select[name=filtro4valor]").eq(id+1).html($("select[name=filtro4valor]").eq(id).html());
	$("select[name=filtro4valor]").eq(id+1).val($("select[name=filtro4valor] option:selected").eq(id).val());

	$("select[name=filtro5valor]").eq(id+1).html($("select[name=filtro5valor]").eq(id).html());
	$("select[name=filtro5valor]").eq(id+1).val($("select[name=filtro5valor] option:selected").eq(id).val());

	$("select[name=filtro6valor]").eq(id+1).html($("select[name=filtro6valor]").eq(id).html());
	$("select[name=filtro6valor]").eq(id+1).val($("select[name=filtro6valor] option:selected").eq(id).val());

	$("select[name=filtro7valor]").eq(id+1).html($("select[name=filtro7valor]").eq(id).html());
	$("select[name=filtro7valor]").eq(id+1).val($("select[name=filtro7valor] option:selected").eq(id).val());

	$("select[name=filtro8valor]").eq(id+1).html($("select[name=filtro8valor]").eq(id).html());
	$("select[name=filtro8valor]").eq(id+1).val($("select[name=filtro8valor] option:selected").eq(id).val());

	$("select[name=filtro9valor]").eq(id+1).html($("select[name=filtro9valor]").eq(id).html());
	$("select[name=filtro9valor]").eq(id+1).val($("select[name=filtro9valor] option:selected").eq(id).val());

	$("select[name=filtro10valor]").eq(id+1).html($("select[name=filtro10valor]").eq(id).html());
	$("select[name=filtro10valor]").eq(id+1).val($("select[name=filtro10valor] option:selected").eq(id).val());

	$("select[name=filtro11valor]").eq(id+1).html($("select[name=filtro11valor]").eq(id).html());
	$("select[name=filtro11valor]").eq(id+1).val($("select[name=filtro11valor] option:selected").eq(id).val());

}

$(window).scroll(function () {
	console.log(elementInViewport("ref"));
	if(!elementInViewport("ref")){
		$("div[id='ref1']").show();
	}else{
 		$("div[id='ref1']").hide();
	}
});
function popitup(url) {
	newwindow=window.open(url,'name','height=200,width=300');
	if (window.focus) {newwindow.focus()}
	return false;
}


</script>

<?php

global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);


$array_Marcas = getMarcas();
$array_Familias = getFamilias();
$array_Categorias = getFiltros1();

$s_ref = $_GET['s_ref'];
$s_nome = $_GET['s_nome'];
$s_marca = $_GET['s_marca'];
$s_familia = $_GET['s_familia'];
$s_subfamilia = $_GET['s_subfamilia'];
$sem_filtros = $_GET['sem_filtros'];
$todos = $_GET['todos'];
if($todos)$sem_filtros=0;

$s_filtro_categoria = array();
$s_filtro_valor = array();
for($m = 1; $m<12; $m++)
{
	$s_filtro_categoria[] = $_GET['s_filtro_categoria'.$m];
	$s_filtro_valor[] = $_GET['s_filtro_valor'.$m];

	$filtro_url .= '&s_filtro_categoria'.$m.'='.$_GET['s_filtro_categoria'.$m].'&s_filtro_valor'.$m.'='.$_GET['s_filtro_valor'.$m];

}
$pagina_actual = (isset($_GET['pag'])) ? $_GET['pag'] : 0;
?>
<div style="padding-left:10px; width:100%;">
	<?php
	$paginas=new DataObj;
	if($s_ref)$w.=' AND pobjl.Cabecalho Like "%'.$s_ref.'%" ';
	if($s_nome)$w.=' AND p1l.Cabecalho Like "%'.$s_nome.'%" ';
	if($s_marca){
		if($s_marca!="SM")
			$w.=' AND p1.ID_Marca = '.$s_marca.' ';
		else
			$w.=' AND p1.ID_Marca = 0 ';
	}
	if($s_familia)$w.=' AND pf.fam_id1 = '.$s_familia.' ';
	if($s_subfamilia)$w.=' AND pf.fam_id2 = '.$s_subfamilia.' ';
	for($m = 1; $m<12; $m++)
	{
		if($s_filtro_categoria[$m-1])$w.=' AND pf.cat_id = '.$s_filtro_categoria[$m-1].' ';
		if($s_filtro_valor[$m-1])$w.=' AND pf.valor_id = '.$s_filtro_valor[$m-1].' ';
	}

	if($com_filtros_invisiveis) $aproved = "p1.Aproved = 0";
	else $aproved = "p1.Aproved = 1";
	$aproved=' 1=1 ';


	?>
</div>
<table class="quadros" width="100%" border="1" cellspacing="0" cellpadding="2">

	<tr><form name="search" action="produtos_2.php" method="get">
		<td align="center"><input class="btn btn-primary" name="search" type="submit" value=" Procurar "/></td>
		<?php /*<td style="text-transform:uppercase; font-size:11px; color:white; font-weight:bold;" bgcolor="black" align="center"></td>*/?>
		<td style="vertical-align:top; text-transform:uppercase; font-size:11px; color:white; font-weight:bold;" bgcolor="black" align="center" class="preto2">Referencia</br><input type="text" style="width:70px;height:15px" name="s_ref" value="<?php echo $s_ref?>"></td>
		<td style="vertical-align:top; text-transform:uppercase; font-size:11px; color:white; font-weight:bold;" bgcolor="black" align="center" class="preto2">Nome</br><input type="text" style="width:90%;height:15px" name="s_nome" value="<?php echo $s_nome?>"></td>
		<td style="vertical-align:top; text-transform:uppercase; font-size:11px; color:white; font-weight:bold;" bgcolor="black" align="center" class="preto2">Marca</br>
		<?php

			//Search MARCA

			?><select class="select_produtos2" name="s_marca" style="width:120px">
				<option value="">SEM VALOR</option>
				<option value="SM"  <?php if ($s_marca== "SM") echo ' selected ';?>>SEM MARCA</option>
				<?php foreach($array_Marcas as $marca){
					?><option value="<?php echo $marca[0]?>" <?php if ($s_marca== $marca[0]) echo ' selected ';?>><?php echo $marca[1]?></option> <?php
				}?>
			</select>
			</td>
		<td style="text-transform:uppercase; font-size:11px; color:white; font-weight:bold;" bgcolor="black" align="center" class="preto2">Familia</br>
		<?php

			//Search FAMILIA

			?><select class="select_produtos2" id="s_familia" onchange="" name="s_familia" style="width:120px">
				<option value="">SEM VALOR</option>
				<?php

				foreach($array_Familias as $fam){
					?><option value="<?php echo $fam[0]?>" <?php if($s_familia == $fam[0]){ echo "selected"; } ?>><?php echo $fam[1]?></option><?php
				} ?>
			</select>
		</td>
		<td style="text-transform:uppercase; font-size:11px; color:white; font-weight:bold;" bgcolor="black" align="center" class="preto2">Sub-Familia</br>
		<?php

			//Search SUBFAMILIA

			$sql2="SELECT pf.*, pfl.* from products_families pf
			inner join products_families_lang pfl on (pf.ID=pfl.ID_PAI)
			where pf.visivel=1 order by pfl.Cabecalho ASC";
			$obj2=new DataObj;
			//$sql2="SELECT p1.*, p1l.Cabecalho FROM produtos_1_familias p1 join produtos_1_familias_linguas p1l on (p1.ID=p1l.ID_PAI) WHERE p1.ID_ORG in ".$str_id_pai;
			$obj2->db_open($db_link,$DB_name,$sql2);

			?>
			<select class="select_produtos2" id="s_subfamilia"  name="s_subfamilia" style="width:120px">
				<option value="">SEM VALOR</option>
				<?php
				$array_options_id = array();
				$array_options_nome = array();

				while(!$obj2->EOF())
				{
					//var_dump($obj2);
					$familias = BuscarArvore_gerada_cf($obj2->db_data['ID_PAI']);
					if($familias[1]!=NULL &&  !in_array($familias[1],$array_options_id)){
						$array_options_id[] = $familias[1];
						$array_options_nome[] = $obj2->db_data['Cabecalho'];
					}
					$obj2->db_next();
				}
				$obj2->db_close();
				for($i=0;$i<sizeof($array_options_id);$i++){
					echo '<option title="'.$array_options_nome[$i].'" value="'.$array_options_id[$i].'"';
					if($s_subfamilia == $array_options_id[$i]){ echo "selected"; }
					echo '>'.$array_options_nome[$i].'</option>';
				}?>
			</select>
		</td>
		<?php
		for($j=1;$j<12;$j++){
		?>
		<td style="text-transform:uppercase; font-size:11px; color:white; font-weight:bold;" bgcolor="black" align="center" class="preto2">Filtro <?php echo $j?></br>
		<?php

		//Search Filtro Categoria

		?><select class="select_produtos2" name="s_filtro_categoria<?=$j?>" style="width:120px<? if($s_filtro_categoria[$j-1]=='')echo ';bgcolor="black"';?>">
			<option value="">SEM FILTRO</option>
			<?php foreach($array_Categorias as $categoria){
				?><option value="<?php echo $categoria[0]?>" <?php if($s_filtro_categoria[$j-1]==$categoria[0]) echo "selected"; ?> ><?php echo $categoria[1]?></option><?php
			}?>
			</select>
		</td>
		<td style="text-transform:uppercase; font-size:11px; color:white; font-weight:bold;" bgcolor="black" align="center" class="preto2">Valor Filtro <?php echo $j?></br>
		<?php

		//Search Filtro Valor

		$obj2=new DataObj;
		$sql2="SELECT fv.value_id as 'valor_id',fvl.values_name as 'valor_nome' FROM  filters_values fv inner join filters_values_lang fvl on fv.value_id=fvl.values_id  where value_active=1 ORDER BY fvl.values_name ASC";
		//echo $sql2;
		$obj2->db_open($db_link,$DB_name,$sql2);
		$obj2->db_rows;
		?>

			<select class="select_produtos2" name="s_filtro_valor<?=$j?>" style="width:120px">
				<option value="">SEM VALOR</option>
				<?php
				while(!$obj2->EOF())
				{
					?><option value="<?php echo $obj2->db_data['valor_id']?>" <?php if($s_filtro_valor[$j-1]==$obj2->db_data['valor_id']) echo "selected"; ?> ><?php echo $obj2->db_data['valor_nome']?></option><?php
					$obj2->db_next();
				}
				$obj2->db_close();
				?>
			</select>
		</td>
		<?php } ?>
	</tr>
	</form>
	<?php
	$objectos=new DataObj;
	if($pagina_actual==0 || !isset($pagina_actual)){
		$p = 'limit 0,'.$porpagina;
	}else{
		$p_temp = $pagina_actual*$porpagina;
		$p = 'limit '.$p_temp.','.$porpagina;
	}
	/*$sql_procura="Select p1.* , p1l.Cabecalho, pf.fam_id1, pf.fam_id2
	from produtos_1 p1
	inner join produtos_1_linguas p1l on (p1.ID=p1l.ID_PAI)
	left join produtos_filtros pf on (p1.ID=pf.prod_id)
	where $aproved $w
	group by p1.Ref order by p1.Ref $p";*/
	$sql_procura="SELECT
		p1.* , p1l.Cabecalho, pf.fam_id1, pf.fam_id2,pobjl.Cabecalho as Referencia, pfo.ID_ORG as familia
	from
		products p1
		inner join
			products_lang p1l on (p1.ID=p1l.ID_PAI)
		inner join
			products_objs pobj on (p1.ID=pobj.obj_type_id)
		inner join
			products_objs_lang pobjl on (pobjl.ID_PAI=pobj.obj_id)
		left join
			filters_products pf on (p1.ID=pf.prod_id)
		left join
			products_families_obj pfo on (p1.ID=pfo.ID_PROD)
	where
		$aproved $w
	group by
		pobjl.Cabecalho
	order by
		CAST(pobjl.Cabecalho AS SIGNED)
	$p";
//	echo $sql_procura;
	$objectos->db_open($db_link,$DB_name,$sql_procura);

	$count=0;
    while(!$objectos->EOF())
    {
    	//REFERENCIA
		?>
	<tr  id="tr_<?=$count?>" style="background-color:<?php if($count%2==0) echo "white"; else echo "#E6E6E6 "?>">
	<form name="produto2" action="produtos_2_save.php?url=<?php echo curPageURL()?>" method="POST">
		<td class="preto">
		<a style="cursor:pointer; float:left;" onclick="gravaLinha(<?php echo $count?>)">
		<img src="<?php echo $site_url?>button_save.png"  border="0" align="absmiddle"></a>

		</td>
		<?php /*<td class="preto">
			<a style="cursor:pointer; float:left;" onclick="copiaLinha(<?php echo $count?>)">Copiar</a>
		</td>*/?>
		<td style="width:100px; position:relative" class="preto2">
			<input type="hidden" name="referencia" value="<?php echo $objectos->db_data["Referencia"]?>"/>
			<input type="hidden" name="idproduto" value="<?php echo $objectos->db_data["ID"]?>"/>
			<input type="hidden" id="contafiltros" name="contafiltros" value="0"/>
			<div id="ref"><?php echo $objectos->db_data["Referencia"]?></div>
			<div id="ref1" style="position:absolute; display:none; bottom:20px;left:-110px;"><div  style="position:fixed;background: <?php if($count%2==0) echo "white"; else echo "#E6E6E6 "?>; width:100px; height:20px;"><div style="margin-top:5px;text-align:center"><?php echo $objectos->db_data["Referencia"]?></div></div></div></td>
		<td style="white-space:nowrap;font-size:10px;" title="<?php echo $objectos->db_data["Cabecalho"]?>" class="preto2"><?php echo $objectos->db_data["Cabecalho"]?></td>

		<td class="preto2" <?php if($objectos->db_data["ID_Marca"] > 0){?>style="background:#4bc8fa"<?php }else{?>style="background:#FF0000"<?php } ?>>
        <?php  //MARCA
        ?>
        	<select class="select_produtos2"  id="marca_<?=$count?>" name="marca" style="width:120px">
				<option value="">SEM VALOR</option>
				<?php foreach($array_Marcas as $marca){
					?><option value="<?php echo $marca[0]?>" <?php if ($objectos->db_data["ID_Marca"]== $marca[0]) echo ' selected ';?>><?php echo $marca[1]?></option> <?php
				}?>
			</select>
        </td>
		<?php

				$arrayFams = BuscarArvore_gerada_cf($objectos->db_data['familia']);
				if($objectos->db_data['fam_id1']=="") $famcmp1=$arrayFams[0];
				else $famcmp1=$objectos->db_data['fam_id1'];
		?>
	    <td  <?php if($famcmp1 > 0){?>style="background:#4bc8fa"<?php }else{?>style="background:#FF0000"<?php } ?>>
        <?php //FAMILIA ?>
        <select style="width:120px" class="select_produtos2" id="familia_<?=$count?>" onchange="mudaSubFamilia(<?php echo $count?>)" name="familia">
			<option value="">SEM VALOR</option>
			<?php

			foreach($array_Familias as $fam)
			{
				?><option value="<?php echo $fam[0]?>" <?php if($famcmp1 == $fam[0]){ echo "selected"; $id_familia = $famcmp1; } ?>><?php echo $fam[1]?></option><?php
			} ?>
		</select>
        </td>
		<?php
		$obj2=new DataObj;
		if($id_familia!=0)
		{
			$sql2="SELECT pfl.* FROM  products_families pf
			inner join products_families_lang pfl on (pf.ID = pfl.ID_PAI)
			WHERE  pf.ID_ORG=".$id_familia." and pfl.Lang='".$site_lang."'";
			$obj2->db_open($db_link,$DB_name,$sql2);
		}
		if($objectos->db_data['fam_id2']=="") $famcmp2=$arrayFams[1];
		else $famcmp2=$objectos->db_data['fam_id2'];
		?>
		<td <?php if($famcmp2 > 0){?>style="background:#4bc8fa"<?php }else{?>style="background:#FF0000"<?php } ?>>

		<?php  //SUBFAMILIA ?>
        <select class="select_produtos2" id="subfamilia_<?=$count?>" name="subfamilia" style="width:120px">
        <option value="">SEM VALOR</option>
        <?php

        if($id_familia!=0)
		{
			while(!$obj2->EOF())
    		{

				?><option value="<?php echo $obj2->db_data['ID_PAI']?>" <?php if($famcmp2 == $obj2->db_data['ID_PAI']) echo "selected" ?> ><?php echo $obj2->db_data['Cabecalho']?></option><?php
				$obj2->db_next();
			}
			$obj2->db_close();
		}
		?>
		</select>
		</td>
		<?php
		$obj2=new DataObj;
		$sql2="SELECT * FROM  `filters_products` WHERE  `prod_id` =".$objectos->db_data["ID"].' ORDER BY cat_id ASC';
		$obj2->db_open($db_link,$DB_name,$sql2);
		$array_filtros = array();
		$array_filtros_valores = array();
		while(!$obj2->EOF())
		{
			$array_filtros[] = $obj2->db_data['cat_id'];
			$array_filtros_valores[] = $obj2->db_data['valor_id'];
			$obj2->db_next();
		}
		$obj2->db_close();

		for($j=1;$j<12;$j++)
		{

		//FILTRO

		$class_aux='';
		if(!$array_filtros[$j-1])$class_aux="b";

		?><td <?php if($array_filtros[$j-1] > 0){?>style="background:#4bc8fa"<?php }else{?>style="background:#FF0000"<?php } ?>>

			<select class="select_produtos3<?=$class_aux?>" id="filtro<?=$j?>categoria_<?=$count?>" onchange="mudaValor(<?php echo $count?>,<?php echo $j?>)" name="filtro<?=$j?>categoria" style="width:120px">
				<option value="">SEM FILTRO</option>
				<?php foreach($array_Categorias as $categoria){
					?><option value="<?php echo $categoria[0]?>" <?php if($array_filtros[$j-1]==$categoria[0]) echo "selected"; ?> ><?php echo $categoria[1]?></option><?php
				}?>
			</select>

			</td>
			<td  align="center" <?php if($array_filtros_valores[$j-1] > 0){?>style="background:#4bc8fa"<?php }else{?>style="background:#FF0000"<?php } ?>>
				<?php
				//VALOR FILTRO
				$obj2=new DataObj;

				if(isset($array_filtros[$j-1]))$sql2="SELECT fv.value_id as 'valor_id',fvl.values_name as 'valor_nome' FROM  filters_values fv inner join filters_values_lang fvl on fv.value_id=fvl.values_id  where fv.value_active=1 and fv.category_id=".$array_filtros[$j-1]." ORDER BY fvl.values_name ASC";
				else $sql2="";
				if($sql2!="")$obj2->db_open($db_link,$DB_name,$sql2);


				//FILTRO

				$class_aux='';
				if($array_filtros_valores[$j-1]==0)$class_aux="b";
				?>

				<select class="select_produtos4<?=$class_aux?>" onchange="mostraInput(<?php echo $count?>,<?php echo $j?>)" id="filtro<?=$j?>valor_<?=$count?>" name="filtro<?=$j?>valor" style="width:120px">
					<option value="">SEM VALOR</option>
					<?php
					$cont_temp = 0;
					if($sql2!=""){
					while(!$obj2->EOF())
					{
						?><option value="<?php echo $obj2->db_data['valor_id']?>" <?php if($array_filtros_valores[$j-1]==$obj2->db_data['valor_id']) echo "selected"; ?> ><?php echo $obj2->db_data['valor_nome']?></option><?php
						$obj2->db_next();
						$cont_temp++;
					}
					$obj2->db_close();
					}
					?>
					<option value="-">--NOVO VALOR--</option>
				</select>
				<div id="input<?=$j?>valor_<?=$count?>" style="display:none"><input id="novofiltro<?=$j?>valor_<?=$count?>" style="width:90px;" type="text" ><input type="button" value="Insere" onclick="insereNovoValor(<?php echo $count?>,<?php echo $j?>)"></div>
			</td>
			</td>
			<?php
			} ?>

	</form>
	</tr>
	    <?php

        $objectos->db_next();
		$count++;
    }
    $objectos->db_close();
	?>
</table></div>
	</td>
	</tr>
	</table>
	</td>
	</tr>



	</table>
	<table bgcolor="#282D31" width="100%" border="0" cellspacing="0" cellpadding="2">
						<tr>
							<td class="branco" align="center" style="line-height:20px;">


								<?
								$sql_procura="SELECT
		p1.* , p1l.Cabecalho, pf.fam_id1, pf.fam_id2,pobjl.Cabecalho as Referencia
	from
		products p1
		inner join
			products_lang p1l on (p1.ID=p1l.ID_PAI)
		inner join
			products_objs pobj on (p1.ID=pobj.obj_type_id)
		inner join
			products_objs_lang pobjl on (pobjl.ID_PAI=pobj.obj_type_id)
		left join
			filters_products pf on (p1.ID=pf.prod_id)
	where
		$aproved $w
	group by
		pobjl.Cabecalho
	order by
		CAST(pobjl.Cabecalho AS SIGNED)";


	$paginas->db_open($db_link,$DB_name,$sql_procura);
	$contagem = $paginas->db_rows;
	$paginacao = ceil($contagem/$porpagina);
	$paginas->db_close();


		$texto_url = "produtos_2.php?s_ref=".$s_ref."&s_nome=".$s_nome."&s_marca=".$s_marca."&s_familia=".$s_familia."&s_subfamilia=".$s_subfamilia.$filtro_url."&sem_filtros=$sem_filtros&com_filtros_invisiveis=$com_filtros_invisiveis";
		if($paginacao>1)
		{

		echo "Página: ";

		$limit_paginas=10;
		if($pagina_actual>1) echo '<a class=page_number href="'.$texto_url.'&pag=1">&lt;&lt;</a>&nbsp;';
		if($pagina_actual>1) echo '<a class=page_number href="'.$texto_url.'&pag='.($pagina_actual-1).'">&lt;</a> ';
		if($pagina_actual>$limit_paginas) echo ' ... ';

		if($pagina_actual<=($limit_paginas/2)) $start = 1;
		else $start = $pagina_actual-($limit_paginas/2)+1;



		if($pagina_actual<=($paginacao-($limit_paginas/2))-1) $end = $start+$limit_paginas;
		else $end = $paginacao;


			for($i=$start;$i<$end;$i++)
			{
				if($pagina_actual==$i)	echo '<a class=page_number2 href="'.$texto_url.'&pag='.$i.'">'.$i.'</a> ';
				else				echo '<a class=page_number href="'.$texto_url.'&pag='.$i.'">'.$i.'</a> ';
			}
		if($pagina_actual<=($paginacao-$limit_paginas)) echo ' ... ';
		if($pagina_actual<($paginacao-1)) echo '<a class=page_number href="'.$texto_url.'&pag='.($pagina_actual+1).'">&gt;</a> ';
		if($pagina_actual<($paginacao-1)) echo '<a class=page_number href="'.$texto_url.'&pag='.($paginacao-1).'">&gt;&gt;</a> ';

    db_closedb_conection($db_link);

    }
                                ?>
							</td>
						</tr>
						</table>
	</td>
	<td width="10px">&nbsp;</td>
	<td valign="top"><?php /*show_fast_tools();*/?></td>
	</tr>
	</table>


	</div>
<?php include "../layout/bottom.inc.php";?>