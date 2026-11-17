<?
  include "settings.inc.php";
	global $DB_host,$DB_user,$DB_pass,$ano;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$result=new DataObj;

  // TOTAL DE VISITAS DO MES
  if(!$ano) $ano=date("Y");
  if(!$mes) $mes=date("m");
  //$sql="select count(*) as Contador,EXTRACT(DAY FROM DATA) AS dia from stats WHERE pagename Like 'Página - Home Page' and data >= '".date("$ano-$mes-00")."' and data <= '".date("$ano-12-31")."' group by data";
 	$sql="select count(*) as Contador, EXTRACT(DAY FROM DATA) AS dia from stats WHERE pagename Like '%HomePage%' and data between '".date("$ano-$mes-00")."' and '".date("$ano-$mes-31")."' group by data";
	//echo $sql;
   if($result->db_open($db_link,$DB_name,$sql))	while(!$result->EOF())	
  {
    $dia=$result->db_data["dia"];
    $datay[$dia]=$result->db_data["Contador"];
    $result->db_next();
  }
  $result->db_close();
  
  $i=1;
  $max=0;
 if($mes==1){ while($i<=31)
  {
    if(!$datay[$i]) $datay[$i]=0;
    if($max<$datay[$i]) $max=$datay[$i];
    $datax[$i]=$i;
    $i++;
  }
  }elseif($mes==2){ while($i<=28)
  {
    if(!$datay[$i]) $datay[$i]=0;
    if($max<$datay[$i]) $max=$datay[$i];
    $datax[$i]=$i;
    $i++;
  }
  }elseif($mes==3){ while($i<=31)
  {
    if(!$datay[$i]) $datay[$i]=0;
    if($max<$datay[$i]) $max=$datay[$i];
    $datax[$i]=$i;
    $i++;
  }
  }elseif($mes==4){ while($i<=30)
  {
    if(!$datay[$i]) $datay[$i]=0;
    if($max<$datay[$i]) $max=$datay[$i];
    $datax[$i]=$i;
    $i++;
  }
  }elseif($mes==5){ while($i<=31)
  {
    if(!$datay[$i]) $datay[$i]=0;
    if($max<$datay[$i]) $max=$datay[$i];
    $datax[$i]=$i;
    $i++;
  }
  }elseif($mes==6){ while($i<=30)
  {
    if(!$datay[$i]) $datay[$i]=0;
    if($max<$datay[$i]) $max=$datay[$i];
    $datax[$i]=$i;
    $i++;
  }
  }elseif($mes==7){ while($i<=31)
  {
    if(!$datay[$i]) $datay[$i]=0;
    if($max<$datay[$i]) $max=$datay[$i];
    $datax[$i]=$i;
    $i++;
  }
  }elseif($mes==8){ while($i<=31)
  {
    if(!$datay[$i]) $datay[$i]=0;
    if($max<$datay[$i]) $max=$datay[$i];
    $datax[$i]=$i;
    $i++;
  }
  }elseif($mes==9){ while($i<=30)
  {
    if(!$datay[$i]) $datay[$i]=0;
    if($max<$datay[$i]) $max=$datay[$i];
    $datax[$i]=$i;
    $i++;
  }
  }elseif($mes==10){ while($i<=31)
  {
    if(!$datay[$i]) $datay[$i]=0;
    if($max<$datay[$i]) $max=$datay[$i];
    $datax[$i]=$i;
    $i++;
  }
  }elseif($mes==11){ while($i<=30)
  {
    if(!$datay[$i]) $datay[$i]=0;
    if($max<$datay[$i]) $max=$datay[$i];
    $datax[$i]=$i;
    $i++;
  }
  }elseif($mes==12){ while($i<=31)
  {
    if(!$datay[$i]) $datay[$i]=0;
    if($max<$datay[$i]) $max=$datay[$i];
    $datax[$i]=$i;
    $i++;
  }
  }
 db_closedb_conection($db_link);
?>
<link rel="stylesheet" href="calendario.css" type="text/css">
<style type="text/css">
body,td,th {
	font-family: Arial, Helvetica, sans-serif;
}
</style>
<table width="100%" border=0 cellspacing=0 cellpadding="5" bordercolor="#848484">
<tr>
	<td align='left' bgcolor='#FFFFFF'>
	<table class=thin bordercolor="#a9de0f" border="1" cellpadding=0 cellspacing=0 width="100%"><tr><? 
	$i=1; 
	while($i<=12) 
	{ 
		?>
		<td width="8%" align="center" <? if($i==$mes) echo " bgcolor=#a9de0f"; else { ?> onmouseover="this.style.backgroundColor='#FFEAF3';this.style.cursor='pointer';" onmouseout="this.style.backgroundColor='#FFFFFF'" onclick="javascript:window.open('grafico.php?mes=<?=$i?>&ano=<?=$ano?>','_self')"<?
		}?>><?=$i?></td> 
		<? $i++;}?></tr></table>
	<table>
	<tr bgcolor="#EFEFEF"> 
		<td><table width="100%" border=0 cellspacing="5" cellpadding=0>
        <tr align="center" valign="bottom">
        <?
        $i=1;
		if($mes==1){
        while($i<=31)
        {
		?>
          <td width="50" height=200>
            <table width="10" height="<?=$datay[$i]*200/$max?>px" border=0 cellpadding=0 cellspacing=0 bgcolor="#a9de0f">
            <tr>
              <td>&nbsp;</td>
            </tr>
            </table>
          </td><?
          $i++;
        }
		}elseif($mes==2){
        while($i<=28)
        {?>
          <td width="50" height=200>
            <table width="10" height="<?=$datay[$i]*200/$max?>px" border=0 cellpadding=0 cellspacing=0 bgcolor="#a9de0f">
            <tr>
              <td>&nbsp;</td>
            </tr>
            </table>
          </td><?
          $i++;
        }
		}elseif($mes==3){
        while($i<=31)
        {?>
          <td width="50" height=200>
            <table width="10" height="<?=$datay[$i]*200/$max?>px" border=0 cellpadding=0 cellspacing=0 bgcolor="#a9de0f">
            <tr>
              <td>&nbsp;</td>
            </tr>
            </table>
          </td><?
          $i++;
        }
		}elseif($mes==4){
        while($i<=30)
        {?>
          <td width="50" height=200>
            <table width="10" height="<?=$datay[$i]*200/$max?>px" border=0 cellpadding=0 cellspacing=0 bgcolor="#a9de0f">
            <tr>
              <td>&nbsp;</td>
            </tr>
            </table>
          </td><?
          $i++;
        }
		}elseif($mes==5){
        while($i<=31)
        {?>
          <td width="50" height=200>
            <table width="10" height="<?=$datay[$i]*200/$max?>px" border=0 cellpadding=0 cellspacing=0 bgcolor="#a9de0f">
            <tr>
              <td>&nbsp;</td>
            </tr>
            </table>
          </td><?
          $i++;
        }
		}elseif($mes==6){
        while($i<=30)
        {?>
          <td width="50" height=200>
            <table width="10" height="<?=$datay[$i]*200/$max?>px" border=0 cellpadding=0 cellspacing=0 bgcolor="#a9de0f">
            <tr>
              <td>&nbsp;</td>
            </tr>
            </table>
          </td><?
          $i++;
        }
		}elseif($mes==7){
        while($i<=31)
        {?>
          <td width="50" height=200>
            <table width="10" height="<?=$datay[$i]*200/$max?>px" border=0 cellpadding=0 cellspacing=0 bgcolor="#a9de0f">
            <tr>
              <td>&nbsp;</td>
            </tr>
            </table>
          </td><?
          $i++;
        }
		}elseif($mes==8){
        while($i<=31)
        {?>
          <td width="50" height=200>
            <table width="10" height="<?=$datay[$i]*200/$max?>px" border=0 cellpadding=0 cellspacing=0 bgcolor="#a9de0f">
            <tr>
              <td>&nbsp;</td>
            </tr>
            </table>
          </td><?
          $i++;
        }
		}elseif($mes==9){
        while($i<=30)
        {?>
          <td width="50" height=200>
            <table width="10" height="<?=$datay[$i]*200/$max?>px" border=0 cellpadding=0 cellspacing=0 bgcolor="#a9de0f">
            <tr>
              <td>&nbsp;</td>
            </tr>
            </table>
          </td><?
          $i++;
        }
		}elseif($mes==10){
        while($i<=31)
        {?>
          <td width="50" height=200>
            <table width="10" height="<?=$datay[$i]*200/$max?>px" border=0 cellpadding=0 cellspacing=0 bgcolor="#a9de0f">
            <tr>
              <td>&nbsp;</td>
            </tr>
            </table>
          </td><?
          $i++;
        }
		}elseif($mes==11){
        while($i<=30)
        {?>
          <td width="50" height=200>
            <table width="10" height="<?=$datay[$i]*200/$max?>px" border=0 cellpadding=0 cellspacing=0 bgcolor="#a9de0f">
            <tr>
              <td>&nbsp;</td>
            </tr>
            </table>
          </td><?
          $i++;
        }
		}elseif($mes==12){
        while($i<=31)
        {?>
          <td width="50" height=200>
            <table width="10" height="<?=$datay[$i]*200/$max?>px" border=0 cellpadding=0 cellspacing=0 bgcolor="#a9de0f">
            <tr>
              <td>&nbsp;</td>
            </tr>
            </table>
          </td><?
          $i++;
        }
		}
		
		?>
        </tr>
    <tr align="center" valign="bottom"><?
     $i=1;
	  if($mes==1){
        while($i<=31)
        {?>
          <td width="50"><?=$datay[$i];?></td><?
          $i++;
        }
		}elseif($mes==2){
		    while($i<=28)
        {?>
          <td width="50"><?=$datay[$i];?></td><?
          $i++;
        }
		}elseif($mes==3){
		    while($i<=31)
        {?>
          <td width="50"><?=$datay[$i];?></td><?
          $i++;
        }
		}elseif($mes==4){
		    while($i<=30)
        {?>
          <td width="50"><?=$datay[$i];?></td><?
          $i++;
        }
		}elseif($mes==5){
		    while($i<=31)
        {?>
          <td width="50"><?=$datay[$i];?></td><?
          $i++;
        }
		}elseif($mes==6){
		    while($i<=30)
        {?>
          <td width="50"><?=$datay[$i];?></td><?
          $i++;
        }
		}elseif($mes==7){
		    while($i<=31)
        {?>
          <td width="50"><?=$datay[$i];?></td><?
          $i++;
        }
		}elseif($mes==8){
		    while($i<=31)
        {?>
          <td width="50"><?=$datay[$i];?></td><?
          $i++;
        }
		}elseif($mes==9){
		    while($i<=30)
        {?>
          <td width="50"><?=$datay[$i];?></td><?
          $i++;
        }
		}elseif($mes==10){
		    while($i<=31)
        {?>
          <td width="50"><?=$datay[$i];?></td><?
          $i++;
        }
		}elseif($mes==11){
		    while($i<=30)
        {?>
          <td width="50"><?=$datay[$i];?></td><?
          $i++;
        }
		}elseif($mes==12){
		    while($i<=31)
        {?>
          <td width="50"><?=$datay[$i];?></td><?
          $i++;
        }
		}
		
		?>
    </tr>
    <tr align="center" valign="bottom"><?
     $i=1;
	 if($mes==1){
	 
        while($i<=31)
        {?>
          <td width="50"><?=$i;?></td><?
          $i++;
        }
		}elseif($mes==2){
		 while($i<=28)
        {?>
          <td width="50"><?=$i;?></td><?
          $i++;
        }
		}elseif($mes==3) {
		 while($i<=31)
        {?>
          <td width="50"><?=$i;?></td><?
          $i++;
        }
		}elseif($mes==4){
		 while($i<=30)
        {?>
          <td width="50"><?=$i;?></td><?
          $i++;
        }
		}elseif($mes==5){
		 while($i<=31)
        {?>
          <td width="50"><?=$i;?></td><?
          $i++;
        }
		}elseif($mes==6){
		 while($i<=30)
        {?>
          <td width="50"><?=$i;?></td><?
          $i++;
        }
		}elseif($mes==7) {
		 while($i<=31)
        {?>
          <td width="50"><?=$i;?></td><?
          $i++;
        }
		}elseif($mes==8){
		 while($i<=31)
        {?>
          <td width="50"><?=$i;?></td><?
          $i++;
        }
		}elseif($mes==9){
		 while($i<=30)
        {?>
          <td width="50"><?=$i;?></td><?
          $i++;
        }
		}elseif($mes==10){
		 while($i<=31)
        {?>
          <td width="50"><?=$i;?></td><?
          $i++;
        }
		}elseif($mes==11){
		 while($i<=30)
        {?>
          <td width="50"><?=$i;?></td><?
          $i++;
        }
		}elseif($mes==12){
		 while($i<=31)
        {?>
          <td width="50"><?=$i;?></td><?
          $i++;
        }
		}
		
		?>
    </tr>
  </table></td>
		
	</tr>
	<tr bgcolor="#DEDEEF">
		<td align="center">Total de visitas a Home Page durante o m&ecirc;s </td>
		
	</tr>
</table>
</td>
  </tr>
</table>