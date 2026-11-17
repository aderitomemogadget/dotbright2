<?php

function d()
{
    
}


function show_traking()
{
	/*global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$fields=db_list_fields($DB_name,'rmas',$db_link);
	$columns=db_num_fields($fields);
	if($columns>0)
	{
		$field_array=array();
		for($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
		if(!in_array('notes',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE rmas ADD notes Text NOT NULL ');		
	}*/



	if($_POST['traking_id'])
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

		//DB027908
		$x_id=trim($_POST['traking_id']);
		$x_id=str_replace('DB0','', $x_id);

		$rs=new DataObj;
		$rs2=new DataObj;
		$rs->db_open($db_link,$DB_name,"Select * from rmas WHERE ID=".$x_id);
		if(!$rs->EOF())
		{
			$m_estado = $rs->db_data['m_estado'];
			$Notes = $rs->db_data['notes'];

			$sql="SELECT ee.ID, ee.Ordem, eel.Cabecalho FROM rmas_estados ee INNER JOIN rmas_estados_lang eel ON eel.ID_PAI = ee.ID where Lang='".$GLOBALS['admin_lang']."' AND ee.ID='".$m_estado."'";
			$rs2->db_open($db_link,$DB_name,$sql);
			if(!$rs2->EOF())
			{
				$Estado = $rs2->db_data['Cabecalho'];
			}
			$rs2->db_close();

			if(trim($_POST['traking_txt']))
			{
				$Notes=$Notes."\n".addslashes(trim($_POST['traking_txt']));
				$sql = 'update rmas set notes = "'.$Notes.'", lida=0 WHERE ID = '.$x_id;
				$res=Runquery($db_link,$DB_name,$sql);
			}

			

			echo '<form method="POST"><div style="padding:30px;text-align:center">';
			echo '<input type="hidden" name="traking_id" value="'.addslashes(trim($_POST['traking_id'])).'">';
			echo '<div style="width:100%;text-align:center;font-size:18px;color:#000">';
			echo addslashes(trim($_POST['traking_id']));
			echo '<br>';
			echo '<br>';
			echo $Estado;
			echo '<br>';
			echo '<br>';
			echo '</div>';

			echo '<div style="width:100%;text-align:center">';
			echo 'Caso pretenda enviar alguma informação adicional preencha o seguinte formulario';
			echo '<br>';
			echo '<br>';
			echo '<input class="search_query form-control" type="text" name="traking_txt" id="traking_txt" style="max-width:300%; width:100%;margin:0 auto"><br>';
			if ($Notes)echo nl2br($Notes).'<br><br>';
			echo '<button type="submit" name="submit_search" class="btn btn-default button-search"><span>ENVIAR</span></button><br>';
			echo '</div>';
			echo '</form>';

		}
		else
		{
			echo '<form method="POST"><div style="padding:30px;text-align:center">';
			echo 'Introduza o número de tracking para poder acompanhar o estado da sua reparação<br><br>';
			echo '<input class="search_query form-control" type="text" name="traking_id" id="traking_id" style="width:150px;margin:0 auto" value="DB"><br>';
			echo '<button type="submit" name="submit_search" class="btn btn-default button-search"><span>ENVIAR</span></button><br>';
			echo '</form>';
		}
		$rs->db_close();


	}
	else
	{
		// echo '<form method="POST"><div style="padding:30px;text-align:center">';
		// echo 'Introduza o número de tracking para poder acompanhar o estado da sua reparação<br><br>';
		// echo '<input class="search_query form-control" type="text" name="traking_id" id="traking_id" style="width:150px;margin:0 auto" value="DB"><br>';
		// echo '<button type="submit" name="submit_search" class="btn btn-default button-search"><span>ENVIAR</span></button><br>';
		// echo '</form>';	

		?>
		<div class="row">
			<div class="col-xs-12 col-md-6">
				<img src="layout/<? echo PROJETO ?>/images/dot_tracking.png" style="width: 100%;max-width: max-content;">
			</div>
			<div class="col-xs-12 col-md-6">
			<form method="POST"><div style="padding:30px;text-align:center">
					<span>Introduza o número de tracking para poder acompanhar o estado da sua reparação</span><br><br>
					<input class="search_query form-control" type="text" name="traking_id" id="traking_id" style="width:150px;margin:0 auto" value="DB"><br>
					<button type="submit" name="submit_search" class="btn btn-default button-search"><span>ENVIAR</span></button><br>
				</form>
			</div>
		</div>
		<?php
	}


}