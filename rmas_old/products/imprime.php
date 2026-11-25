<? 
	include "settings.inc.php";
?>
<link href="templates/styles.css" rel="stylesheet" type="text/css">
<style>
td  {font-size:10px; font-family:Verdana}
</style>
<?
global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
$rs=new DataObj;
$rs2=new DataObj;
echo '<table border=0 cellspacing="3" cellpadding=0>';
echo '<tr>';
echo '<td align=center style=color:000000><strong>Fornecedor</strong></td>';
echo '<td align=center style=color:000000><strong>Familia</strong></td>';
echo '<td align=center style=color:000000><strong>Referência</strong></td>';
echo '<td align=center style=color:000000><strong>Nome do produto</strong></td>';
//echo '<td align=center style=color:000000><strong>Preço Custo</strong></td>';
echo '<td align=center style=color:000000><strong>PVP</strong></td>';
echo '<td align=center style=color:000000><strong>NOVO PVP</strong></td>';
echo '<td align=center style=color:000000><strong>Peso</strong></td>';
echo '<td align=center style=color:000000><strong>EM DESTAQUE/NOVIDADE</strong></td>';
echo '<td align=center style=color:000000><strong>VISIVEL</strong></td>';
echo '</tr>';

$count =0;
$sql1="SELECT products.*, Cabecalho, Cabecalho_tmp FROM products 
INNER JOIN products_lang ON products_lang.ID_PAI =products.ID 
where products.Ref!='' AND Preco1_tmp >0  AND Preco1_tmp < 50 and Preco1 > 50 group by products.ID order by products.ID_ORG asc, visitas desc ";


$sql1="SELECT products.*, Cabecalho, Cabecalho_tmp FROM products 
INNER JOIN products_lang ON products_lang.ID_PAI =products.ID 
where products.Ref!='' AND Preco1_tmp >0 AND ((Preco1_tmp - Preco1 > 50) OR (Preco1 - Preco1_tmp > 50)) group by products.ID order by products.ID_ORG asc, visitas desc ";


$sql1="SELECT products.*, Cabecalho, Cabecalho_tmp FROM products 
INNER JOIN products_lang ON products_lang.ID_PAI =products.ID 
where products.Ref!='' AND id_fornecedor =0 group by products.ID order by products.ID_ORG asc, visitas desc ";


$hRes=$rs->db_open($db_link,$DB_name,$sql1);
while(!$rs->EOF())
{
	$Cab=$rs->db_data["Cabecalho"];
	$Cab_tmp=$rs->db_data["Cabecalho_tmp"];
	if($Cab_tmp) $Cab.='<br /><font color=red>'.$Cab_tmp.'</font>';
	if(!$Cab) $Cab=" - ";
	$ID_PROD=$rs->db_data["ID"];
	$Ref=$rs->db_data["Ref"];
	$org=$rs->db_data["ID_ORG"];
	$precocusto=number_format($rs->db_data["PrecoCusto"],0,",",".");
	$preco=number_format($rs->db_data["Preco1"],2,",",".");
	$preco_tmp=number_format($rs->db_data["Preco1_tmp"],2,",",".");
	$importado=$rs->db_data["Importacao"];
	$aproved=$rs->db_data["Aproved"];
	$visitas=$rs->db_data["visitas"];
	$id_fornecedor=$rs->db_data["id_fornecedor"];

	if($count % 2 !=0)	$cor="#E0E0E0";
	else				$cor="#F6F6F6";

	$sql2="SELECT products_families.*,  products_families_lang.*, products_families.ID, products_families_lang.ID as IDD 
	FROM products_families INNER JOIN products_families_lang ON products_families_lang.ID_PAI =products_families.ID 
	where products_families.ID='".$org."'";
	$hRes=$rs2->db_open($db_link,$DB_name,$sql2);
	$id=$rs2->db_data["Ref"];
	$destaque=$rs2->db_data["Destaque"];
	$Cab2=$rs2->db_data["Cabecalho"];
	$rs2->db_close();

	if($id_fornecedor==1) $fornecedor="JPSACOUTO";
	elseif($id_fornecedor==2) $fornecedor="CPC";
	else $fornecedor="----";
	
	echo '<tr>';
	echo '<td width=100 align=center bgcolor="'.$cor.'" style=color:000000>'.$fornecedor.' </td>';
	echo '<td width=100 align=center bgcolor="'.$cor.'" style=color:000000>'.$id.' - '.$Cab2.' </td>';
	echo '<td width=150 bgcolor="'.$cor.'" style=color:000000>'.$Ref.'</td>';
	echo '<td bgcolor="'.$cor.'" style=color:000000>'.$Cab.'</td>';	
	//echo '<td bgcolor="'.$cor.'" style=color:000000 align=right>'.$precocusto.' &euro;</td>';
	echo '<td bgcolor="'.$cor.'" style=color:000000 align=right>'.$preco.' &euro;</td>';
	echo '<td bgcolor="'.$cor.'" style=color:000000 align=right>'.$preco_tmp.' &euro;</td>';
	echo '<td bgcolor="'.$cor.'" style=color:000000 align=right>'.$visitas.'</td>';
	
	if($destaque)	echo '<td bgcolor="'.$cor.'" style=color:000000 align=center>SIM</td>';
	else			echo '<td bgcolor="'.$cor.'" style=color:ff0000 align=center>NÃO</td>';
	
	if($aproved)	echo '<td bgcolor="'.$cor.'" style=color:000000 align=center>SIM</td>';
	else			echo '<td bgcolor="'.$cor.'" style=color:ff0000 align=center>NÃO</td>';
	
	echo '</tr>';
	$rs->db_next();
	$count++;
}
$rs->db_close();
echo'</table>';
?>