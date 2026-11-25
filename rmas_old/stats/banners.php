<?
	include "settings.inc.php";
	
	global $DB_host,$DB_user,$DB_pass;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$result=new DataObj;	
	
	if($f_data1=="" && $f_data2=="")
	{
		$f_data1=date ("Y")."-".date ("m")."-01";
		$f_data2=date ("Y")."-".date ("m")."-".date ("d");
	}
	
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
	body,td,th {
	font-family: Arial;
}
    </style>

<div class="center">
        <table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
        <tr>
            <td valign="top" align="left"><? include "../layout/menus.inc.php"; ?>
                <table class="new_quadros" width="880px" border=0 cellspacing=0 cellpadding="4" align="center">
                <tr> 
                    <td valign="top"><? include("menu.inc.php"); ?>
                    	<table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="F6F6F6" class="border1">
                        <tr> 
                            <td valign="top">Total de Visitas por p&aacute;gina <br>
<table width="100%" cellspacing=0 cellpadding=0 border=1 bordercolor="#dbdbdb">
  	<tr>
      <td valign="top">
		<table width="100%" cellpadding="2" cellspacing=0 border=0>
		  <tr align="center" bgcolor="#82aced"> 
			<td class="branco">Página</td>
			<td class="branco">Nº de visitas</td>
		  </tr>
			<?
			$result1=new DataObj;
			$sql="select pagename,count(*) as Contador from stats where Data BETWEEN '$f_data1' AND '$f_data2' Group By pagename Order by Contador Desc";
			$result1->db_open($db_link,$DB_name,$sql);
			while (!$result1->EOF())
			{
				$file=$result1->db_data["pagename"];
				$n_acessos=$result1->db_data["Contador"];
				?>
              <tr>
                <td class="preto2"><a class="preto2" href="detalhe.php?f_data1=<?=$f_data1;?>&f_data2=<?=$f_data2;?>&pagina=<?=$file;?>"><?=$file;?>:  </a></td>
                <td class="pretob" align="center"><?=$n_acessos;?></td>
              </tr><?
				$result1->db_next();
			}
			?>
		</table>
	  </td>
	  <td width="415"><form name="formFiltro" method="post">
        <table width="415" border=0 align="center" cellpadding="2" cellspacing=0 class="bordo">
          <tr>
            <td align="center" bgcolor="#82aced" class=titulobordo><b>Mostrar
                todas as visitas</b></td>
          </tr>
          <tr bgcolor="#F1F1F1">
            <td align="center" bgcolor="#FFFFFF" class=preto><table border=0 cellspacing="10" cellpadding=0>
                <tr>
                  <td>desde</td>
                  <td><input style="width:70px; font-size:10px" name="f_data1" value="<?=$f_data1;?>">
                    at&eacute;</td>
                  <td><input style="width:70px; font-size:10px" name="f_data2" value="<?=$f_data2;?>"></td>
                </tr>
              </table>
                <br>
                <input name="submit" type="submit" value="Procurar agora">
            </td>
          </tr>
        </table>
      </form></td>
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
		</table></div>
<? 	include "../layout/bottom.inc.php";?>