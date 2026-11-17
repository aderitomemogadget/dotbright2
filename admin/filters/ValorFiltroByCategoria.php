<?php 

include "settings.inc.php";

function BuscarArvore_gerada_cf($id_org)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name;
    $hConn=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $obj=new DataObj;    
    
    $pai=$id_org;
    while($pai>0)
    {
        $id_org=$pai;
        $sql="SELECT ID,ID_ORG FROM products_families WHERE ID=$pai";
        $hRes=$obj->db_open($hConn,$DB_name,$sql);
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

$categoria = (isset($_GET['categoria'])) ? $_GET['categoria'] : "" ;

//echo $categoria;
global $DB_host,$DB_user,$DB_pass,$DB_name, $site_lang; 
$hConn=db_opendb_conection($DB_host,$DB_user,$DB_pass);
$obj2=new DataObj;


if($categoria != "")
    $sql2="SELECT fv.value_id as 'valor_id',fvl.values_name as 'valor_nome' FROM  filters_values fv inner join filters_values_lang fvl on fv.value_id=fvl.values_id  where fv.value_active=1 and fv.category_id=".$categoria." ORDER BY fvl.values_name ASC";
else
	$sql2="";
echo '<option value="">SEM VALOR</option>';
if($categoria != ""){
	$obj2->db_open($hConn,$DB_name,$sql2);
	while(!$obj2->EOF())
	{
		echo '<option value="'.$obj2->db_data['valor_id'].'" >'.$obj2->db_data['valor_nome'].'</option>';
		$obj2->db_next();
	}
	$obj2->db_close();
}
		

?>
