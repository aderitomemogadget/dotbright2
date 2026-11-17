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
            <td valign="top" align="left">
            	<div class="panel panel-inverse overflow-hidden m-b-12">
			        <div class="panel-heading">
			            <h4 style="color:#fff">ESTATISTICAS</h4>
			        </div>
			    </div>
			    <? include "../layout/menus.inc.php"; ?>
                <table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
                <tr>
                    <td valign="top"><? include("menu.inc.php"); ?>
                    	<table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="F6F6F6" class="border1">
                        <tr>
                            <td valign="top" align="center">Visitas detalhas da página "<?=$pagina?>"<br><br />
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
			$sql="select * from stats where Data BETWEEN '$f_data1' AND '$f_data2' and pagename Like '$pagina'";
			$result1->db_open($db_link,$DB_name,$sql);
			while (!$result1->EOF())
			{
			?>
			<tr>
				<td class="preto2"><?=$result1->db_data["pagename"];?></td>
				<td class="pretob" align="center"><?=$result1->db_data["data"];?></td>
				<td class="pretob"><?=$result1->db_data["hora"];?></td>
				<td class="pretob"><?=$result1->db_data["dayweek"];?></td>
				<td class="pretob"><?=$result1->db_data["host"];?></td>
				<td class="pretob"><?

				$browser=$result1->db_data["browser"];
				$browser2=strlen($browser) > 35 ? substr($browser,0,32)."..." : $browser;
				echo '<a class=pretob href="#" title="'.$browser.'">'.$browser2.'</a>';
				?></td>
				<td class="pretob"><?

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
		</tr>
		</table></div>
<? 	include "../layout/bottom.inc.php";?>