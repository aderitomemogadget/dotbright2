<?php

	if(isset($_POST['si']) && $_POST['ss'])
	{
		$path='../core/';
		include $path.'core_files.php';

		$si=intval($_POST['si']);
		$ss=$_POST['ss'];
		echo $_SESSION['ss'.$si]=uniqid("");
	}
?>