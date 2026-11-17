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
            <td valign="top"  align="left"><? include "../layout/menus.inc.php"; ?>
                <table class="new_quadros" width="880px" border=0 cellspacing=0 cellpadding="0" align="center">
                <tr>
                    <td valign="top"><? include("menu.inc.php"); ?>
                    	<table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="F6F6F6" class="border1">
                        <tr>
                            <td valign="top">Detalhe de encomendas do produto<br>
	<table width="100%" cellspacing=0 cellpadding=0 border=1 bordercolor="#dbdbdb">
  	<tr>
      <td>
		<table width="100%" cellpadding="2" cellspacing=0 border=0>
		  <tr align="center" bgcolor="#82aced">
			<td class="branco">Encomenda</td>
			<td class="branco">ID CLIENTE</td>
			<td class="branco">Nome</td>
			<td class="branco">Qtd </td>
			<td class="branco">Valor</td>
		  </tr>
			<?
			$result1=new DataObj;
			$sql="select encomendas_produtos.*,encomendas.ID as cod_enc,encomendas.ID_Cliente from encomendas_produtos inner join encomendas on encomendas.ID=encomendas_produtos.ID_PAI where Data_Criacao BETWEEN '$f_data1' AND '$f_data2' and ID_Produto='$codigo'";
			$result1->db_open($db_link,$DB_name,$sql);
			while (!$result1->EOF())
			{
			?>
			<tr>
				<td class="preto2" align="center"><a href="../encomendas/pagina.php?ID=<?=$result1->db_data["cod_enc"];?>&Table_Nome=encomendas&NomeM=Encomendas"><?=$result1->db_data["cod_enc"];?></a></td>
				<td class="pretob" align="center"><?=$result1->db_data["ID_Cliente"];?></td>
				<td class="pretob" align="center"><?=$result1->db_data["Nome"];?></td>
				<td class="pretob" align="center"><?=$result1->db_data["Qtd"];?></td>
				<td class="pretob" align="center"><?=$result1->db_data["Valor"];?></td>
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
		</table></div>
<? 	include "../layout/bottom.inc.php";?>