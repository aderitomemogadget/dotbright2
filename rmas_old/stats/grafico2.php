<?
  include "settings.inc.php";

	global $DB_host,$DB_user,$DB_pass,$ano;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$result=new DataObj;
  // TOTAL DE VISITAS DO ANO POR MES
  if(!$ano) $ano=date("Y");
  $max=0;
  $mes=1;
  while($mes <=12)
  {
	  $sql="select count(*) as Contador from stats WHERE pagename Like '%HomePage%' and data >='".date($ano."-".$mes."-00")."' and data <='".date($ano."-".$mes."-31")."' group by data";
	  $start=0;
	  if($result->db_open($db_link,$DB_name,$sql)) while(!$result->EOF())	
	  {
		if($start==0) $total_mes[$mes]=$result->db_data["Contador"];
		else $total_mes[$mes]+=$result->db_data["Contador"];
		$result->db_next();
		$start=1;
	  }
	  $result->db_close();
	  $max+=$total_mes[$mes];
	  $mes++;
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
<table>
	<tr bgcolor="#EFEFEF"> 
		<td><table width="100%" border=0 cellspacing="5" cellpadding=0>
        <tr align="center" valign="bottom">
        <?
        $i=0;
        while($i<12)
        {?>
          <td width="50" height=200>
            <table width="10" height="<?=$total_mes[$i]*200/$max?>px" border=0 cellpadding=0 cellspacing=0 bgcolor="#a9de0f">
            <tr>
              <td>&nbsp;</td>
            </tr>
            </table>
          </td><?
          $i++;
        }?>
        </tr>
    <tr align="center" valign="bottom"><?
     $i=0;
        while($i<=12)
        {?>
          <td width="50"><?=$total_mes[$i];?></td><?
          $i++;
        }?>
    </tr>
    <tr align="center" valign="bottom"><?
     $i=0;
	 // Escrever a legenda
        while($i<=12)
        {?>
          <td width="50"><?=$i;?></td><?
          $i++;
        }?>
    </tr>
  </table></td>
		<td>&nbsp;</td>
	</tr>
	<tr bgcolor="#DEDEEF">
		<td>Total de paginas consultadas durante o ano </td>
		<td>&nbsp;</td>
	</tr>
</table>
</td>
  </tr>
</table>