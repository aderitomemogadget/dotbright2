<?
	include "settings.inc.php";
	
	global $DB_host,$DB_user,$DB_pass,$ano;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$result=new DataObj;	

	if($an) $ano=$an;
	elseif(!$ano) $ano=date("Y");
	
	
	// TOTAL DE VISITAS DE HOJE
	$sql="select count(*) as Contador from stats WHERE pagename Like '%HomePage%' and data Like '".date("$ano-m-d")."'";
	// echo $sql;
	if($result->db_open($db_link,$DB_name,$sql))	if(!$result->EOF())	$n_visitas_hoje=$result->db_data["Contador"];
	$result->db_close();
	// DE ONTEM
	$sql="select count(*) as Contador from stats WHERE pagename Like '%HomePage%' and data Like '".date("$ano-m-d",strtotime("-1 day"))."'";
	if($result->db_open($db_link,$DB_name,$sql))	if(!$result->EOF())	$n_visitas_ontem=$result->db_data["Contador"];
	$result->db_close();
	// DE 2 dias
	$sql="select count(*) as Contador from stats WHERE pagename Like '%HomePage%' and data Like '".date("$ano-m-d",strtotime("-2 day"))."'";
	if($result->db_open($db_link,$DB_name,$sql))	if(!$result->EOF())	$n_visitas_2dias=$result->db_data["Contador"];
	$result->db_close();
	// DE 3 dias
	$sql="select count(*) as Contador from stats WHERE pagename Like '%HomePage%' and data Like '".date("$ano-m-d",strtotime("-3 day"))."'";
	if($result->db_open($db_link,$DB_name,$sql))	if(!$result->EOF())	$n_visitas_3dias=$result->db_data["Contador"];
	$result->db_close();
	// DA SEMANA
	$sql="select count(*) as Contador from stats WHERE pagename Like '%HomePage%' and data between '".date("$ano-m-d",strtotime("last Monday"))."' AND '".date("$ano-m-d")."'";
	if($result->db_open($db_link,$DB_name,$sql)) if(!$result->EOF())	$n_visitas_semana=$result->db_data["Contador"];
	$result->db_close();
	// DO MES
	$sql="select count(*) as Contador from stats WHERE pagename Like '%HomePage%' and data between '".date("$ano-m-00")."' AND '".date("$ano-m-d")."'";
	if($result->db_open($db_link,$DB_name,$sql)) if(!$result->EOF())	$n_visitas_mes=$result->db_data["Contador"];
	$result->db_close();
	// DO MES ANT
	$sql="select count(*) as Contador from stats WHERE pagename Like '%HomePage%' and data between '".date("$ano-m-00",strtotime("-1 month"))."' AND '".date("$ano-m-31",strtotime("-1 month"))."'";
	if($result->db_open($db_link,$DB_name,$sql)) if(!$result->EOF())	$n_visitas_mes_ant=$result->db_data["Contador"];
	$result->db_close();	

	// TOTAL DE PAGINAS VISITADAS DE HOJE
	$sql="select count(*) as Contador from stats WHERE data Like '".date("$ano-m-d")."'";
	if($result->db_open($db_link,$DB_name,$sql))	if(!$result->EOF())	$n_paginas_hoje=$result->db_data["Contador"];
	$result->db_close();
	// DE ONTEM
	$sql="select count(*) as Contador from stats WHERE data Like '".date("$ano-m-d",strtotime("-1 day"))."'";
	if($result->db_open($db_link,$DB_name,$sql))	if(!$result->EOF())	$n_paginas_ontem=$result->db_data["Contador"];
	$result->db_close();
	// 2 dias
	$sql="select count(*) as Contador from stats WHERE data Like '".date("$ano-m-d",strtotime("-2 day"))."'";
	if($result->db_open($db_link,$DB_name,$sql))	if(!$result->EOF())	$n_paginas_2dias=$result->db_data["Contador"];
	$result->db_close();
	// 3 dias
	$sql="select count(*) as Contador from stats WHERE data Like '".date("$ano-m-d",strtotime("-3 day"))."'";
	if($result->db_open($db_link,$DB_name,$sql))	if(!$result->EOF())	$n_paginas_3dias=$result->db_data["Contador"];
	$result->db_close();
	// DA SEMANA
	$sql="select count(*) as Contador from stats WHERE data between '".date("$ano-m-d",strtotime("last Monday"))."' AND '".date("$ano-m-d")."'";
	if($result->db_open($db_link,$DB_name,$sql))	if(!$result->EOF())	$n_paginas_semana=$result->db_data["Contador"];
	$result->db_close();
	// DO MES
	$sql="select count(*) as Contador from stats WHERE data between '".date("$ano-m-00")."' AND '".date("$ano-m-d")."'";
	if($result->db_open($db_link,$DB_name,$sql))	if(!$result->EOF())	$n_paginas_mes=$result->db_data["Contador"];
	$result->db_close();
	// DO MES ANT
	$sql="select count(*) as Contador from stats WHERE data between '".date("$ano-m-00",strtotime("-1 month"))."' AND '".date("$ano-m-31",strtotime("-1 month"))."'";
	if($result->db_open($db_link,$DB_name,$sql))	if(!$result->EOF())	$n_paginas_mes_ant=$result->db_data["Contador"];
	$result->db_close();
	
	// TOTAL DE PÀGINAS UNICAS VISTAS HOJE
	$sql="select count(distinct pagename) as Contador from stats WHERE data Like '".date("$ano-m-d")."'";
	if($result->db_open($db_link,$DB_name,$sql))	if(!$result->EOF())	$n_paginas_unicas_hoje=$result->db_data["Contador"];
	$result->db_close();
	// ONTEM
	$sql="select count(distinct pagename) as Contador from stats WHERE data Like '".date("$ano-m-d",strtotime("-1 day"))."'";
	if($result->db_open($db_link,$DB_name,$sql))	if(!$result->EOF())	$n_paginas_unicas_ontem=$result->db_data["Contador"];
	$result->db_close();
	// 2 dias
	$sql="select count(distinct pagename) as Contador from stats WHERE data Like '".date("$ano-m-d",strtotime("-2 day"))."'";
	if($result->db_open($db_link,$DB_name,$sql))	if(!$result->EOF())	$n_paginas_unicas_2dias=$result->db_data["Contador"];
	$result->db_close();
	// 3 dias
	$sql="select count(distinct pagename) as Contador from stats WHERE data Like '".date("$ano-m-d",strtotime("-3 day"))."'";
	if($result->db_open($db_link,$DB_name,$sql))	if(!$result->EOF())	$n_paginas_unicas_3dias=$result->db_data["Contador"];
	$result->db_close();
	// DO MES
	$n_paginas_unicas_semana="----";
	$n_paginas_unicas_mes="----";
	$n_paginas_unicas_mes_ant="----";
	
	// TOTAL DE VISITANTES DE HOJE
	$sql="select count(distinct IP) as Contador from stats WHERE data Like '".date("$ano-m-d")."'";
	if($result->db_open($db_link,$DB_name,$sql))	if(!$result->EOF())	$n_visitantes_hoje=$result->db_data["Contador"];
	$result->db_close();
	// DE ONTEM
	$sql="select count(distinct IP) as Contador from stats WHERE data Like '".date("$ano-m-d",strtotime("-1 day"))."'";
	if($result->db_open($db_link,$DB_name,$sql))	if(!$result->EOF())	$n_visitantes_ontem=$result->db_data["Contador"];
	$result->db_close();
	// 2 dias
	$sql="select count(distinct IP) as Contador from stats WHERE data Like '".date("$ano-m-d",strtotime("-2 day"))."'";
	if($result->db_open($db_link,$DB_name,$sql))	if(!$result->EOF())	$n_visitantes_2dias=$result->db_data["Contador"];
	$result->db_close();
	// 3 dias
	$sql="select count(distinct IP) as Contador from stats WHERE data Like '".date("$ano-m-d",strtotime("-3 day"))."'";
	if($result->db_open($db_link,$DB_name,$sql))	if(!$result->EOF())	$n_visitantes_3dias=$result->db_data["Contador"];
	$result->db_close();
	// DO MES
	$sql="select count(distinct IP) as Contador from stats WHERE data between '".date("$ano-m-00")."' AND '".date("$ano-m-d")."'";
	if($result->db_open($db_link,$DB_name,$sql))	if(!$result->EOF())	$n_visitantes_mes=$result->db_data["Contador"];
	$result->db_close();
	// DA SEMANA
	$sql="select count(distinct IP) as Contador from stats WHERE data between '".date("$ano-m-d",strtotime("last Monday"))."' AND '".date("$ano-m-d")."'";
	if($result->db_open($db_link,$DB_name,$sql))	if(!$result->EOF())	$n_visitantes_semana=$result->db_data["Contador"];
	$result->db_close();
	// DO MES ANTERIOR
	$sql="select count(distinct IP) as Contador from stats WHERE data between '".date("$ano-m-00",strtotime("-1 month"))."' AND '".date("$ano-m-31",strtotime("-1 month"))."'";
	if($result->db_open($db_link,$DB_name,$sql))	if(!$result->EOF())	$n_visitantes_mes_ant=$result->db_data["Contador"];
	$result->db_close();
	
	include "../layout/header.inc.php";	
	include "../layout/body.inc.php";
?>

	
	<style>
	td {
		font-family: "Arial";
		font-size: 12px;
	}
	A{			FONT-SIZE: 11px;	COLOR: #000000;	TEXT-DECORATION: none}
	A:active{	FONT-SIZE: 11px;	COLOR: #82aced;	TEXT-DECORATION: none}
	A:hover{	FONT-SIZE: 11px;	COLOR: #000000;	TEXT-DECORATION: none}
	A:link{		FONT-SIZE: 11px;	COLOR: #000000;	TEXT-DECORATION: none}
	
	A.brancoa{			FONT-SIZE: 11px;	COLOR: #000000;	TEXT-DECORATION: none}
	A.brancoa:active{	FONT-SIZE: 11px;	COLOR: #000000;	TEXT-DECORATION: none}
	A.brancoa:hover{	FONT-SIZE: 11px;	COLOR: FFFFFF;	TEXT-DECORATION: none}
	A.brancoa:link{		FONT-SIZE: 11px;	COLOR: #000000;	TEXT-DECORATION: none}
	body,td,th {
	font-family: Arial;
}
    </style>

<div class="center"><form action="geral.php" method="post" name="filtra">	
        <table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
        <tr>
            <td valign="top" align="left"><? include "../layout/menus.inc.php"; ?>
                <table class="new_quadros" width="880px" border=0 cellspacing=0 cellpadding="4" align="center">
                <tr> 
                    <td valign="top"><? include("menu.inc.php"); ?>
                    	<table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="F6F6F6" class="border1">
                        <tr> 
                            <td valign="top" align="center">
 
  
<table border=0 cellspacing=0 cellpadding=5>
<tr>
    <td align="center">Escolher Ano: <input type="text" style="width:70px; font-size:10px; text-align:center" value="<?=$ano?>" name="an"></td>
    <td align="right"><input name="submit" type="submit" value=" Mostrar " class="botao" style="width:70px"></td>
</tr>
</table>
<table width="100%" cellspacing=0 cellpadding=0 border=1 bordercolor="#dbdbdb">
<tr><td width="700">
	<iframe id="graficos" name="graficos" src="grafico.php?ano=<?=$ano?>" scrolling="no" width="800" height="300" frameborder=0>
	[Content for browsers that don't support iframes goes here]
	</iframe>
	<br>
	<table width="800" cellspacing=0 cellpadding=0 border=0>
	  <tr align="center" bgcolor="#82aced"> 
		<td class="branco"><a class="brancoa" href="grafico.php?ano=<?=$ano?>" target="graficos">grafico do mês</a> | <a class="brancoa" href="grafico2.php?ano=<?=$ano?>" target="graficos">grafico do ano</a> | </td>
	  </tr>
</table>
</td><td width="508" valign="top">


</td>
</tr>
</table>

<br>
<br>
<br>
Visitas 
- Paginas vistas - Visitantes unicos <br>
<table width="100%" cellspacing=0 cellpadding=0 border=1 bordercolor="#dbdbdb">
  	<tr>
      <td>
        <table width="100%" cellspacing="4" cellpadding=4 border=0>
        <tr align="center" bgcolor="#82aced"> 
          <td width="250" height="10">&nbsp;</td>
          <td class="branco">Média por hora</td>
          <td bgcolor="#82aced" class="branco">Hoje</td>
          <td class="branco">Ontem</td>
          <td class="branco">Durante a semana</td>
          <td class="branco">Durante o m&ecirc;s</td>
          <td class="branco">M&ecirc;s anterior</td>
        </tr>
        <tr>
          <td width="250" height="10" class="preto2">Total de visitas</td>
          <td align="center" class="pretob"><?=number_format($n_visitas_hoje/24,2,',','.')?></td>
          <td align="center" class="pretob"><?=number_format($n_visitas_hoje,0,'','.')?></td>
          <td align="center" class="pretob"><?=number_format($n_visitas_ontem,0,'','.')?></td>
          <td align="center" class="pretob"><?=number_format($n_visitas_semana,0,'','.')?></td>
          <td align="center" class="pretob"><?=number_format($n_visitas_mes,0,'','.')?></td>
          <td align="center" class="pretob"><?=number_format($n_visitas_mes_ant,0,'','.')?></td>
        </tr>
        <tr> 
          <td width="250" height="10" class="preto2">Total de paginas visitadas</td>
          <td align="center" class="pretob"><?=number_format($n_paginas_hoje/24,2,',','.')?></td>
          <td align="center" class="pretob"><?=number_format($n_paginas_hoje,0,'','.')?></td>
          <td align="center" class="pretob"><?=number_format($n_paginas_ontem,0,'','.')?></td>
          <td align="center" class="pretob"><?=number_format($n_paginas_semana,0,'','.')?></td>
          <td align="center" class="pretob"><?=number_format($n_paginas_mes,0,'','.')?></td>
          <td align="center" class="pretob"><?=number_format($n_paginas_mes_ant,0,'','.')?></td>
        </tr>
        <tr> 
          <td width="250" height="10" class="preto2">Total de p&aacute;ginas unicas visitadas</td>
          <td align="center" class="pretob"><?=number_format($n_paginas_unicas_hoje/24,2,',','.')?></td>
          <td align="center" class="pretob"><?=number_format($n_paginas_unicas_hoje,0,'','.')?></td>
          <td align="center" class="pretob"><?=number_format($n_paginas_unicas_ontem,0,'','.')?></td>
          <td align="center" class="pretob"><?=number_format($n_paginas_unicas_semana,0,'','.')?></td>
          <td align="center" class="pretob"><?=number_format($n_paginas_unicas_mes,0,'','.')?></td>
          <td align="center" class="pretob"><?=number_format($n_paginas_unicas_mes_ant,0,'','.')?></td>
        </tr>
        <tr> 
          <td width="250" height="10" class="preto2">Visitantes únicos diários</td>
          <td align="center" class="pretob"><?=number_format($n_visitantes_hoje/24,2,',','.')?></td>
          <td align="center" class="pretob"><?=number_format($n_visitantes_hoje,0,'','.')?></td>
          <td align="center" class="pretob"><?=number_format($n_visitantes_ontem,0,'','.')?></td>
          <td align="center" class="pretob"><?=number_format($n_visitantes_semana,0,'','.')?></td>
          <td align="center" class="pretob"><?=number_format($n_visitantes_mes,0,'','.')?></td>
          <td align="center" class="pretob"><?=number_format($n_visitantes_mes_ant,0,'','.')?></td>
        </tr>
      </table>
      </td>
    </tr>
</table><?
  // O MELHOR DIA DE VISITAS
	$sql="select count(*) as Contador,Data from stats WHERE YEAR(data)='$ano' and pagename Like 'Página - Home Page' group by data ORDER BY Contador DESC";
	//echo $sql;
	if($result->db_open($db_link,$DB_name,$sql))	if(!$result->EOF())	{$melhor_dia=$result->db_data["Contador"]; $melhor_data=$result->db_data["Data"];}
	$result->db_close();
	
	// O MELHOR DIA DE PAGINAS VISITADAS
	$sql="select count(*) as Contador,Data  from stats where YEAR(Data)='$ano' group by data ORDER BY Contador DESC";
	if($result->db_open($db_link,$DB_name,$sql))	if(!$result->EOF())	{$melhor_dia2=$result->db_data["Contador"]; $melhor_data2=$result->db_data["Data"];}
	$result->db_close();
	
	// O MELHOR DIA DE PÀGINAS UNICAS VISTAS
	$sql="select count(distinct pagename) as Contador,Data  from stats  where YEAR(Data)='$ano' group by data ORDER BY Contador DESC";
	if($result->db_open($db_link,$DB_name,$sql))	if(!$result->EOF())	{$melhor_dia3=$result->db_data["Contador"]; $melhor_data3=$result->db_data["Data"];}
	$result->db_close();
	
	// O MELHOR DIA DE VISITANTES
	$sql="select count(distinct IP) as Contador,Data  from stats  where YEAR(Data)='$ano' group by data ORDER BY Contador DESC";
	if($result->db_open($db_link,$DB_name,$sql))	if(!$result->EOF())	{$melhor_dia4=$result->db_data["Contador"]; $melhor_data4=$result->db_data["Data"];}
	$result->db_close();
  ?><br><br>
O Melhor dia<br>
<table width="100%" cellspacing=0 cellpadding=0 border=1 bordercolor="#dbdbdb">
  	<tr>
      <td>
        <table width="100%" cellspacing="4" cellpadding=4 border=0>
        <tr align="center" bgcolor="#82aced"> 
          <td width="250" height="10">&nbsp;</td>
          <td class="branco">Data</td>
          <td class="branco">O Melhor dia</td>
          <td class="branco">Estimativa para hoje</td>
          <td class="branco">Comparação</td>
        </tr>
        <tr>
          <td width="250" height="10" class="preto2">Total de visitas</td>
          <td align="center" class="pretob"><? if($melhor_data) echo $melhor_data; else echo "0";?></td>
          <td align="center" class="pretob"><? if($melhor_dia) echo number_format($melhor_dia,0,'','.'); else echo "0";?></td>
          <td align="center" class="pretob"><? echo number_format($n_visitas_hoje,0,'','.');?></td>
          <td align="center" class="pretob"><? if ($melhor_dia){ if($n_visitas_hoje < $melhor_dia) echo '-'.number_format((100-($n_visitas_hoje*100/$melhor_dia)),2,',','.'); else echo number_format(($melhor_dia*100/$n_visitas_hoje),2,',','.'); }else echo "0";?>%</td>
        </tr>
        <tr> 
          <td width="250" height="10" class="preto2">Total de paginas visitadas</td>
          <td align="center" class="pretob"><? if($melhor_data2) echo $melhor_data2; else echo "0";?></td>
          <td align="center" class="pretob"><?=number_format($melhor_dia2,0,'','.')?></td>
          <td align="center" class="pretob"><?=number_format($n_paginas_hoje,0,'','.')?></td>
          <td align="center" class="pretob"><? if ($melhor_dia2){ if($n_paginas_hoje < $melhor_dia2) echo '-'.number_format((100-($n_paginas_hoje*100/$melhor_dia2)),2,',','.'); else echo number_format(($melhor_dia2*100/$n_paginas_hoje),2,',','.'); }else echo "0";?>%</td>
        </tr>
        <tr> 
          <td width="250" height="10" class="preto2">Total de p&aacute;ginas unicas visitadas</td>
          <td align="center" class="pretob"><? if($melhor_data3) echo $melhor_data3; else echo "0";?></td>
          <td align="center" class="pretob"><?=number_format($melhor_dia3,0,'','.')?></td>
          <td align="center" class="pretob"><?=number_format($n_paginas_unicas_hoje,0,'','.')?></td>
          <td align="center" class="pretob"><? if ($melhor_dia3){ if($n_paginas_unicas_hoje < $melhor_dia3) echo '-'.number_format((100-($n_paginas_unicas_hoje*100/$melhor_dia3)),2,',','.'); else echo number_format(($melhor_dia3*100/$n_paginas_unicas_hoje),2,',','.'); }else echo "0";?>%</td>
        </tr>
        <tr> 
          <td width="250" height="10" class="preto2">Visitantes únicos diários</td>
          <td align="center" class="pretob"><? if($melhor_data2) echo $melhor_data4; else echo "0";?></td>
          <td align="center" class="pretob"><?=number_format($melhor_dia4,0,'','.')?></td>
          <td align="center" class="pretob"><?=number_format($n_visitantes_hoje,0,'','.')?></td>
          <td align="center" class="pretob"><? if ($melhor_dia4){if($n_visitantes_hoje < $melhor_dia4) echo '-'.number_format((100-($n_visitantes_hoje*100/$melhor_dia4)),2,',','.'); else echo number_format(($melhor_dia4*100/$n_visitantes_hoje),2,',','.'); }else echo "0";?>%</td>
        </tr>
      </table>
      </td>
    </tr>
</table><br><br>
Rápida evolução<br>
<table width="100%" cellspacing=0 cellpadding=0 border=1 bordercolor="#dbdbdb">
  	<tr>
      <td>
        <table width="100%" cellspacing="4" cellpadding=4 border=0>
        <tr align="center" bgcolor="#82aced"> 
          <td width="250" height="10">&nbsp;</td>
          <td class="branco">Visitas</td>
          <td class="branco">Paginas visitadas</td>
          <td class="branco">Paginas unicas visitadas</td>
          <td class="branco">Visitantes unicos</td>
        </tr>
        <tr>
          <td width="250" height="10" class="preto2">Hoje</td>
          <td align="center" class="pretob"><?=number_format($n_visitas_hoje,2,',','.')?></td>
          <td align="center" class="pretob"><?=number_format($n_paginas_hoje,2,',','.')?></td>
          <td align="center" class="pretob"><?=number_format($n_paginas_unicas_hoje,2,',','.')?></td>
          <td align="center" class="pretob"><?=number_format($n_visitantes_hoje,2,',','.')?></td>
        </tr>
        <tr> 
          <td width="250" height="10" class="preto2">Ontem</td>
          <td align="center" class="pretob"><?=number_format($n_visitas_ontem,2,',','.')?></td>
          <td align="center" class="pretob"><?=number_format($n_paginas_ontem,2,',','.')?></td>
          <td align="center" class="pretob"><?=number_format($n_paginas_unicas_ontem,2,',','.')?></td>
          <td align="center" class="pretob"><?=number_format($n_visitantes_ontem,2,',','.')?></td>
        </tr>
        <tr> 
          <td width="250" height="10" class="preto2">À 2 dias</td>
          <td align="center" class="pretob"><?=number_format($n_visitas_2dias,2,',','.')?></td>
          <td align="center" class="pretob"><?=number_format($n_paginas_2dias,2,',','.')?></td>
          <td align="center" class="pretob"><?=number_format($n_paginas_unicas_2dias,2,',','.')?></td>
          <td align="center" class="pretob"><?=number_format($n_visitantes_2dias,2,',','.')?></td>
        </tr>
        <tr> 
          <td width="250" height="10" class="preto2">À 3 dias</td>
          <td align="center" class="pretob"><?=number_format($n_visitas_3dias,2,',','.')?></td>
          <td align="center" class="pretob"><?=number_format($n_paginas_3dias,2,',','.')?></td>
          <td align="center" class="pretob"><?=number_format($n_paginas_unicas_3dias,2,',','.')?></td>
          <td align="center" class="pretob"><?=number_format($n_visitantes_3dias,2,',','.')?></td>
        </tr>
      </table>
      </td>
    </tr>
</table><br><br>
Últimas 40 Visitas<br>
<table width="100%" cellspacing=0 cellpadding=0 border=1 bordercolor="#dbdbdb">
  	<tr>
      <td>
		<table width="100%" cellpadding="2" cellspacing=0 border=1>
		  <tr align="center" bgcolor="#82aced"> 
			<td class="branco">Página</td>
			<td class="branco" width="80">Data</td>
			<td class="branco">Hora</td>
			<td class="branco">Dia da Semana</td>
			<td class="branco">Host</td>
			<td class="branco">Browser</td>
			<td class="branco">Origem do Link</td>
		  </tr>
			<?
			$result1=new DataObj;
			$sql="select * from stats where YEAR(Data)='$ano' order by data Desc Limit 0,40";
			//echo $sql;
			$result1->db_open($db_link,$DB_name,$sql);
			while (!$result1->EOF())
			{
			?>
			<tr>
				<td class="preto2"><?=$result1->db_data["pagename"];?></td>
				<td align="center" class="pretob"><?=$result1->db_data["data"];?></td>
				<td align="center" class="pretob"><?=$result1->db_data["hora"];?></td>
				<td align="center" class="pretob"><?=$result1->db_data["dayweek"];?></td>
				<td align="center" class="pretob"><?=$result1->db_data["host"];?></td>
				<td class="pretob"><? 
				
				$browser=$result1->db_data["browser"];
				$browser2=strlen($browser) > 35 ? substr($browser,0,32)."..." : $browser;
				echo '<a class=pretob href="#" title="'.$browser.'">'.$browser2.'</a>';
				?></td>
				<td align="center" class="pretob"><? 
				
				$referer=$result1->db_data["referer"];
				$referer2=strlen($referer) > 50 ? substr($referer,0,48)."..." : $referer;
				echo '<a class=pretob href="#" title="'.$referer.'">'.$referer2.'</a>';
				?></td>
			</tr>
			<?
			$result1->db_next();
			}
			$result1->db_close(1);
			?>
		</table>
	  </td>
    </tr>
</table>

</td>
                       </tr>
                       </table>
                	</td>
				</tr>
                </table>
            </td>
            <td width="10px">&nbsp;</td>
			<td valign="top" width=200px><br /></td>
		</tr>
		</table></form></div>
<? 	include "../layout/bottom.inc.php";?>