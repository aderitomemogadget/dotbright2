<?
include "settings.inc.php";

$marca = $_GET['marca'];
global $DB_host,$DB_user,$DB_pass,$DB_name, $admin_lang; 

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

$hConn=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		
		$sql2="SELECT pfo.ID_ORG
		FROM products p 
		inner join products_families_obj pfo on (p.ID=pfo.ID_PROD)
		inner join products_families pf on (pf.ID=pfo.ID_ORG) 
		where p.ID_Marca = $marca and pf.visivel=1
 		group by p.ID_ORG";
		$obj2=new DataObj;
		$obj2->db_open($hConn,$DB_name,$sql2);
		$array_options_id = array();
		$array_options_nome = array();
		echo '<option value="">SEM FAMILIA</option>';
		while(!$obj2->EOF())
    	{
    		//var_dump($obj2);
    		$familias = BuscarArvore_gerada_cf($obj2->db_data['ID_ORG']);
    		if(!in_array($familias[0],$array_options_id)){
    			$array_options_id[] = $familias[0];
    			$sql3="SELECT * FROM products_families_lang pfl WHERE Lang='".$admin_lang."' and ID_PAI = ".$familias[0];
    			$obj3=new DataObj;
    			$obj3->db_open($hConn,$DB_name,$sql3);
    			$array_options_nome[] = $obj3->db_data['Cabecalho'];
    			$obj3->db_close();
    		}
			$obj2->db_next();
		}
		$obj2->db_close();
		for($i=0;$i<sizeof($array_options_id);$i++){
			echo '<option value="'.$array_options_id[$i].'"';
			echo '>'.$array_options_nome[$i].'</option>';
		}
		

?>
