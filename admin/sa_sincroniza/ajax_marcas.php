<?php
	require_once "settings.inc.php";


	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

	if(isset($_GET['fam_id']) && $_GET['fam_id'] != '')
	{

		$blocos_fams = $_GET['id_org'];
		$blocos_fams = rtrim($blocos_fams, ';');
		$blocos_fams = ltrim($blocos_fams, ';');
		$blocos_fams = explode(';', $blocos_fams);


		$rs = new DataObj; $rs2 = new DataObj;
		$sql = 'SELECT filters_products.marca_id, brands_lang.Nome FROM filters_products INNER JOIN brands_lang ON brands_lang.ID_PAI = filters_products.marca_id  WHERE (fam_id1 IN('.$_GET['fam_id'].') OR fam_id2 IN('.$_GET['fam_id'].') OR fam_id3 IN('.$_GET['fam_id'].') OR fam_id4 IN('.$_GET['fam_id'].')) GROUP BY marca_id';

		$rs->db_open($db_link, $DB_name, $sql);
		if(!$rs->EOF())
		{
			echo '<select name="marca_id[]" id="marca_id" class="form-control2"  multiple="multiple" data-placeholder="Todas" style="width:200px">';
			while(!$rs->EOF())
			{
				$marca_id = $rs->db_data['marca_id'];
				$marca_nome = $rs->db_data['Nome'];

				echo '<option value="'.$marca_id.'"';
				if(in_array($marca_id, $blocos_fams)) echo ' selected="selected" ';

				echo '>'. htmlentities_memo(stripslashes($marca_nome)).'</option>';

				$rs->db_next();
			}
			 echo '</select>';
		}
		$rs->db_close();
	}
	else
	{
		echo "Selecione Família";
	}