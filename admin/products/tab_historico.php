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
			<table width="100%" cellpadding="2" cellspacing=0 border=1>
			  <tr align="center" bgcolor="#82aced">
				<td class="branco">Data</td>
				<?

				$i=0;
				$t=count($GLOBALS['lista_importadores']);
				while($i<$t)
				{
					echo '<td class="branco">'.$GLOBALS['lista_importadores'][$i]['name'].'</td>';
					$i++;
				}
				?>
			  </tr>
			  	 <?
			  	 	echo '<tr><td></td>';
			  		$k=0;
					$s=count($GLOBALS['lista_importadores']);
					while($k<$s)
					{
						echo '<td>';
						echo '<table width="100%" cellpadding="2" cellspacing=0 border=1>';
						echo '<tr align="center" >';
						echo '<td >PREÇO</td>';
						echo '<td >STOCK</td>';
						echo '</tr>';
						echo '</table>';
						echo '</td>';
						$k++;
					}
					echo '</tr>';
					?>
				<?
				if($f_data1=="" && $f_data2=="")
				{
					$f_data1=date ("Y")."-".date ("m")."-01";
					$f_data2=date ("Y")."-".date ("m")."-".date ("d");
				}
				global $DB_host,$DB_user,$DB_pass;
				$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

				$result1=new DataObj;
				$rs=new DataObj;
				$rs2=new DataObj;



				$sql='SELECT obj_id FROM products_objs WHERE obj_type="products" AND obj_type_id="'.$ID.'" order by obj_id DESC';
				$rs->db_open($db_link,$DB_name,$sql);
				if(!$rs->EOF())
				{
					$obj_id=$rs->db_data['obj_id'];
				}
				$rs->db_close();



				$sql="SELECT DISTINCT DATE(data_i) as Data FROM products_objs_history WHERE ID_ORG='".$obj_id."' order BY Data DESC limit 0,50";
				$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
				// echo $sql;
				$rs->db_open($db_link, $DB_name, $sql);
				while(!$rs->EOF())
				{
					$data=$rs->db_data['Data'];
					echo '<tr>';
					echo '<td>'.$data.'</td>';


					$i=0;
					while($i<$t)
					{
						$Supplier_ID = $GLOBALS['lista_importadores'][$i]['id'];
						$sql="SELECT pvp,Stock FROM products_objs_history WHERE ID_ORG='".$obj_id."' and (DATE(data_i) = '".$data."' OR DATE(data_u) = '".$data."') and Supplier_ID=$Supplier_ID Order by ID DESC limit 0,1";
						$rs2->db_open($db_link, $DB_name, $sql);
						if(!$rs2->EOF())
						{
							echo '<td>';
							if(!$rs2->EOF())
							{
								$pvp=$rs2->db_data['pvp'];
								$pvp=number_format($pvp/1.23,2,'.',',');

								echo '<table width="100%" cellpadding="2" cellspacing=0 border=1>';
								echo '<tr align="center" >';
								echo '<td width="50%">'.$pvp.'€</td>';
								echo '<td width="50%">'.$rs2->db_data['Stock'].'</td>';
								echo '</tr>';
								echo '</table>';
							}
							echo '</td>';
						}
						else echo '<td></td>';
						$rs2->db_close();
						$i++;
					}

					echo '</tr>';

					$rs->db_next();
				}
				$rs->db_close();
				?>
			</table>
		  </td>
	    </tr>
		</table>
	</td>
</tr>
</table>