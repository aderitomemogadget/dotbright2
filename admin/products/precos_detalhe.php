<?php

	$IDb2=intval($_GET['ID2']);
	$IDb=intval($_GET['ID']);
	include "settings.inc.php";

	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang,$Suppliers_nomes;
	$rs=new Dataobj;
	$rs2=new Dataobj;
?>
<div style="border:solid 1px #000">
	<div style="width: 100%;background-color: #000;color:#fff;padding:5px">HISTORICO DE PREÇOS (últimos 100)</div>

	<div style="padding:0">
		<div style="background-color: #fff">

			<div style="position:absolute;right:18px;top:3px;z-index:4;cursor:pointer;color:#fff;font-size: 16px" onclick="close_prod('#prod_<?=$IDb2?>')"><i class="far fa-times-circle"></i></div>

			<div style="display:flex;background-color:#f6f6f6;padding:15px">

				<div >
						<div style="width:100%;float:left">
							<div style="background-color:#fffccc;padding:5px;width:150px;float:left;border:solid 1px #ccc">Supplier_ID</div>
							<div style="background-color:#fffccc;padding:5px;width:100px;float:left;border:solid 1px #ccc">preço sem iva</div>
							<div style="background-color:#fffccc;padding:5px;width:50px;float:left;border:solid 1px #ccc">Stock</div>
							<div style="background-color:#fffccc;padding:5px;width:200px;float:left;border:solid 1px #ccc">Data Update</div>
						</div>
						<?php

							$sql="SELECT iva,pvp,Stock,Supplier_ID,data_i,data_u FROM products_objs_history WHERE ID_ORG='".$IDb."' order BY data_u DESC limit 0,10";
							$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
							$rs2->db_open($db_link, $DB_name, $sql);
							$i=0;
							while(!$rs2->EOF())
							{
								$iva=$rs2->db_data['iva'];
								$pc=$rs2->db_data['pc'];
								$pvp=$rs2->db_data['pvp'];
								$pvp=$pvp/1.23;
								$Stock=$rs2->db_data['Stock'];
								$Supplier_ID =$rs2->db_data['Supplier_ID'];
								$data_i =$rs2->db_data['data_i'];
								//$data_u =$rs2->db_data['data_u'];
								//if($data_u=='0000-00-00 00:00:00')$data_u=$data_i;
								
								echo '<div style="width:100%;float:left">
									<div style="padding:5px;width:150px;float:left;border:solid 1px #ccc">'.$Suppliers_nomes[$Supplier_ID].'&nbsp;</div>
									<div style="padding:5px;width:100px;float:left;border:solid 1px #ccc">'.$pvp.'&nbsp;</div>
									<div style="padding:5px;width:50px;float:left;border:solid 1px #ccc">'.$Stock.'&nbsp;</div>
									<div style="padding:5px;width:200px;float:left;border:solid 1px #ccc">'.$data_i.'&nbsp;</div>
									</div>';

								$i++;

								$rs2->db_next();
							}
							$rs2->db_close();

						?>

				</div>
			</div>

		</div>
	</div>
</div>