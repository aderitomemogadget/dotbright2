<?php
	$Supplier_ID=$IDb=intval($_GET['ID']);

	$path = dirname(__FILE__).'/../../../core/';
	include $path.'core_files.php';
	include "settings_crons.inc.php";
	include "funcoes.inc.php";

	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang,$Suppliers_nomes;
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$rs3=new Dataobj;
?>
<div style="border:solid 1px #000">
	<div style="width:100%;background-color: #000;color:#fff;padding:5px">HISTORICO DE PREÇOS (últimos 100)</div>

	<div style="padding:0">
		<div style="background-color: #fff">
			<div style="position:absolute;right:18px;top:3px;z-index:4;cursor:pointer;color:#fff;font-size: 16px" onclick="close_prod('#info1')"><i class="far fa-times-circle"></i></div>
			<div style="display:flex;background-color:#f6f6f6;padding:15px">
				<div>
						<div style="width:100%;float:left">
							<div style="background-color:#fffccc;padding:5px;width:150px;float:left;border:solid 1px #ccc">Supplier_ID</div>
							<div style="background-color:#fffccc;padding:5px;width:100px;float:left;border:solid 1px #ccc">pvp</div>
							<div style="background-color:#fffccc;padding:5px;width:100px;float:left;border:solid 1px #ccc">Stock</div>
							<div style="background-color:#fffccc;padding:5px;width:200px;float:left;border:solid 1px #ccc">Data</div>
							<div style="background-color:#fffccc;padding:5px;width:200px;float:left;border:solid 1px #ccc">Atual PVP Online</div>
							<div style="background-color:#fffccc;padding:5px;width:200px;float:left;border:solid 1px #ccc">PROD ID</div>
							<div style="background-color:#fffccc;padding:5px;width:200px;float:left;border:solid 1px #ccc">EAN</div>
							<div style="background-color:#fffccc;padding:5px;width:200px;float:left;border:solid 1px #ccc">FORNECEDOR</div>
						</div>
						<?php
							$sql="SELECT * FROM products_objs_history WHERE Supplier_ID='".$IDb."' order BY ID DESC limit 0,100";
							$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
							$rs2->db_open($db_link, $DB_name, $sql);
							$i=0;
							while(!$rs2->EOF())
							{
								$id_org=$rs2->db_data['ID_ORG'];
								$iva=$rs2->db_data['iva'];
								$pvp=$rs2->db_data['pvp'];
								$Stock=$rs2->db_data['Stock'];
								$Supplier_ID =$rs2->db_data['Supplier_ID'];
								$data_i =$rs2->db_data['data_i'];

								$sql="SELECT * FROM products_objs WHERE obj_id='".$id_org."'";
								$rs3->db_open($db_link, $DB_name, $sql);
								$atual_pvp=$rs3->db_data['obj_pvp'];
								$obj_type_id=$rs3->db_data['obj_type_id'];
								$obj_Supplier_ID=$rs3->db_data['obj_Supplier_ID'];
								$rs3->db_close();

								$sql="SELECT Ref_Ean FROM products WHERE ID='".$obj_type_id."'";
								$rs3->db_open($db_link, $DB_name, $sql);
								$EAN=$rs3->db_data['Ref_Ean'];
								$rs3->db_close();

								echo '<div style="width:100%;float:left">
									<div style="padding:5px;width:150px;float:left;border:solid 1px #ccc">'.$Suppliers_nomes[$Supplier_ID].'</div>
									<div style="padding:5px;width:100px;float:left;border:solid 1px #ccc">'.$pvp.'</div>
									<div style="padding:5px;width:100px;float:left;border:solid 1px #ccc">'.$Stock.'</div>
									<div style="padding:5px;width:200px;float:left;border:solid 1px #ccc">'.$data_i.'</div>
									<div style="padding:5px;width:200px;float:left;border:solid 1px #ccc">'.$atual_pvp.'</div>
									<div style="padding:5px;width:200px;float:left;border:solid 1px #ccc"><a target="_new" href="../../products/products.php?ID='.$obj_type_id.'">'.$obj_type_id.'</a></div>
									<div style="padding:5px;width:200px;float:left;border:solid 1px #ccc">'.$EAN.'</div>
									<div style="padding:5px;width:200px;float:left;border:solid 1px #ccc">'.$Suppliers_nomes[$obj_Supplier_ID].'</div>
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