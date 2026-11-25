<?php
	include "settings.inc.php";

	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;$rs2=new DataObj;
	
	
	if(isset($_POST['cat_id']) && isset($_POST['a']) && $_POST['cat_id'] > 0 && $_POST['a'] > 0)
	{
		$obj2 = new DataObj;
		$sql2 = '
				SELECT 
					fv.value_id as valor_id, 
					fvl.values_name as valor_nome 
				FROM  
					filters_values fv 
					INNER JOIN filters_values_lang fvl ON fv.value_id=fvl.values_id 
				WHERE 
					value_active=1 
					AND category_id='.$_POST['cat_id'].' 
				ORDER BY 
					fvl.values_name ASC
				';
		// echo $sql2;
		$obj2->db_open($db_link,$DB_name,$sql2);
		if(!$obj2->EOF())
		{
		?>			
			<select name="valor[]" id="valor<?php echo $_POST['a']?>" class="chzn-select2 valclone" style="width:180px">
			<option value="">SEM VALOR</option>
			<?php 
			while(!$obj2->EOF())
			{
				?><option value="<?php echo $obj2->db_data['valor_id']?>" <?php if(isset($_POST['value_id']) && $_POST['value_id'] == $obj2->db_data['valor_id']){ echo "selected"; } ?> ><?php echo $obj2->db_data['valor_nome']?></option><?php 
				$obj2->db_next();
			}			
			?>
			</select>
<?php 	}
		$obj2->db_close(); 
	} ?>