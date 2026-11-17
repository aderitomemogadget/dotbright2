<?php
	$ID=intval($_GET['ID']);
	include "settings.inc.php";
	include "../layout/header.inc.php";



	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang,$Suppliers_nomes;
	$rs=new Dataobj;
	$rs2=new Dataobj;
?><table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="#F6F6F6" class="border1">
<tr>
	<td valign="top">
		<table width="100%" cellspacing=0 cellpadding=0 border=1 bordercolor="#dbdbdb">
	  	<tr>
	      <td>
			<table width="100%" cellpadding="2" cellspacing=0 border=0>
			  <tr align="center" bgcolor="#82aced">
				<td class="branco">Encomenda</td>
				<td class="branco">Data</td>
				<td class="branco">ID cliente</td>
				<td class="branco">Nome</td>
				<td class="branco">Qtd </td>
				<td class="branco">Valor</td>
			  </tr>
				<?
				if($f_data1=="" && $f_data2=="")
				{
					$f_data1=date ("Y")."-".date ("m")."-01";
					$f_data2=date ("Y")."-".date ("m")."-".date ("d");
				}
				global $DB_host,$DB_user,$DB_pass;
				$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

				$result1=new DataObj;
				$sql="select encomendas_produtos.*,encomendas.ID as cod_enc,encomendas.ID_Cliente,encomendas.Data_Criacao from encomendas_produtos inner join encomendas on encomendas.ID=encomendas_produtos.ID_PAI where ID_Produto='$ID' order by cod_enc DESC";
				$result1->db_open($db_link,$DB_name,$sql);
				while (!$result1->EOF())
				{
				?>
				<tr>
					<td class="preto2" align="center"><a class="preto" href="../encomendas/pagina.php?ID=<?=$result1->db_data["cod_enc"];?>&Table_Nome=encomendas&NomeM=Encomendas"><?=$result1->db_data["cod_enc"];?></a></td>
					<td class="pretob" align="center"><?=$result1->db_data["Data_Criacao"];?></td>
					<td class="pretob" align="center"><a class="preto" href="../clientes/pagina.php?ID=<?=$result1->db_data["ID_Cliente"];?>"><?=$result1->db_data["ID_Cliente"];?></a></td>
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