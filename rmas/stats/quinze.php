<?
	include "settings.inc.php";
	
	global $DB_host,$DB_user,$DB_pass,$ano;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$result=new DataObj;	

	if($an) $ano=$an;
	elseif(!$ano) $ano=date("Y");
	
	
	// TOTAL DE VISITAS DE HOJE
	$sql="select count(*) as Contador from stats WHERE pagename Like 'Página - Home Page' and data Like '".date("$ano-m-d")."'";
	//echo $sql;
	if($result->db_open($db_link,$DB_name,$sql))	if(!$result->EOF())	$n_visitas_hoje=$result->db_data["Contador"];
	$result->db_close();
	// DE ONTEM
	$sql="select count(*) as Contador from stats WHERE pagename Like 'Página - Home Page' and data Like '".date("$ano-m-d",strtotime("-1 day"))."'";
	if($result->db_open($db_link,$DB_name,$sql))	if(!$result->EOF())	$n_visitas_ontem=$result->db_data["Contador"];
	$result->db_close();
	// DE 2 dias
	$sql="select count(*) as Contador from stats WHERE pagename Like 'Página - Home Page' and data Like '".date("$ano-m-d",strtotime("-2 day"))."'";
	if($result->db_open($db_link,$DB_name,$sql))	if(!$result->EOF())	$n_visitas_2dias=$result->db_data["Contador"];
	$result->db_close();
	// DE 3 dias
	$sql="select count(*) as Contador from stats WHERE pagename Like 'Página - Home Page' and data Like '".date("$ano-m-d",strtotime("-3 day"))."'";
	if($result->db_open($db_link,$DB_name,$sql))	if(!$result->EOF())	$n_visitas_3dias=$result->db_data["Contador"];
	$result->db_close();
	// DA SEMANA
	$sql="select count(*) as Contador from stats WHERE pagename Like 'Página - Home Page' and data between '".date("$ano-m-d",strtotime("last Monday"))."' AND '".date("$ano-m-d")."'";
	if($result->db_open($db_link,$DB_name,$sql)) if(!$result->EOF())	$n_visitas_semana=$result->db_data["Contador"];
	$result->db_close();
	// DO MES
	$sql="select count(*) as Contador from stats WHERE pagename Like 'Página - Home Page' and data between '".date("$ano-m-00")."' AND '".date("$ano-m-d")."'";
	if($result->db_open($db_link,$DB_name,$sql)) if(!$result->EOF())	$n_visitas_mes=$result->db_data["Contador"];
	$result->db_close();
	// DO MES ANT
	$sql="select count(*) as Contador from stats WHERE pagename Like 'Página - Home Page' and data between '".date("$ano-m-00",strtotime("-1 month"))."' AND '".date("$ano-m-31",strtotime("-1 month"))."'";
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
                            <td valign="top">Últimos 15 minutos <?
			$data2= date("Y-m-d");
			$hora2=time();
			$hora2=date("H:i:s",$hora2);
			$d2=date("H:i:s",strtotime($hora2 . " -15 minutes"));
			
			$result=new DataObj;
			$sql1="select distinct(host) from stats where (hora >='$d2' and hora<='$hora2') and data='$data2' order by hora desc";
			$result->db_open($db_link,$DB_name,$sql1);
			$var=0;
			while(!$result->EOF()){
			$var++;			
			$result->db_next();
			}
			$result->db_close();
			if ($var!=1) echo " [Existem ".$var." IP's diferentes]";
			else echo " [Existe ".$var." IP diferente]";
?><br>
<table width="100%" cellspacing=0 cellpadding=0 border=1 bordercolor="#dbdbdb">
  	<tr>
      <td>
		<table width="100%" cellpadding="2" cellspacing=0 border=0>
		  <tr align="center" bgcolor="#82aced"> 
			<td class="branco">Página</td>
			<td class="branco" width="100">Data</td>
			<td class="branco">Hora</td>
			<td class="branco">Dia da Semana</td>
			<td class="branco">Host</td>
			<td class="branco">Browser</td>
			<td class="branco">Origem do Link</td>
		  </tr>
			<?
			$result1=new DataObj;		
			$data= date("Y-m-d");
			$hora=time();
			$hora=date("H:i:s",$hora);
			$d=date("H:i:s", strtotime($hora . " -15 minutes"));			
			$sql="select * from stats where (hora >='$d' and hora<='$hora') and data='$data' order by hora desc";
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
				<td align="center" class="pretob"><?=$result1->db_data["browser"];?></td>
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