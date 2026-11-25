<?php
	$path =  dirname( __FILE__ ).'/../../core/';
	include $path.'core_files.php';
	
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);	

	if(isset($_GET['m']) && $_GET['m']!='')
	{	
		$marca_id = intval($_GET['m']);		
		

		$db_sel = new DataObj();
		$sql = 'SELECT modelo FROM modelos_rmas WHERE marca_id='.$marca_id.' ORDER BY Ordem ASC, modelo ASC';
		$db_sel->db_open($db_link,$DB_name,$sql);
		if(!$db_sel->EOF()) 
		{	
		?>	
			<input type="text" name="novo_modelo" id="novo_modelo" style="display: none;width:120px" class="box" placeholder="Insira novo Modelo" />
			<select id="sel_modelo" name="m_modelo" onchange="newmodelo(this.value);" class="chzn-select" style="width:120px">
			<option value="">----------</option>
			<option value="new">NOVO</option>
				<?php

				
				while(!$db_sel->EOF()) 
				{
					$nome = $db_sel->db_data['modelo'];
					?>
					<option value="<?php echo $nome; ?>" <?php if ($m_modelo == $nome) echo 'selected="selected"';?> ><?php echo $nome?></option>
					<?php
					$db_sel->db_next();
				}
				$db_sel->db_close();
				?>
			</select><script> $(".chzn-select").chosen();</script><?php
		}
		else
		{
		?>
			<input type="text" name="novo_modelo" id="novo_modelo" style="display: block;width:120px" class="box" placeholder="Insira novo Modelo" />
			<select id="sel_modelo" name="m_modelo" onchange="newmodelo(this.value);" class="chzn-select" style="width:120px">
			<option value="new">NOVO</option>
			</select><script> $(".chzn-select").chosen();</script>
		<?php	
		}
		
	}
	else
	{
		?>
		<input type="text" name="novo_modelo" id="novo_modelo" style="display: block;width:120px" class="box" placeholder="Insira novo Modelo" />
		<select id="sel_modelo" name="m_modelo" onchange="newmodelo(this.value);" class="chzn-select" style="width:120px">
		<option value="new">NOVO</option>
		</select><script> $(".chzn-select").chosen();</script>
		<?php
	}