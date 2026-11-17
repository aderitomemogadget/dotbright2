<?php
	set_time_limit(0);
	$path = dirname( __FILE__ ).'/../core/';
	require_once $path.'core_files.php';

	$x_op0 = clean_var($_POST['m_op0']);
	$x_op1 = clean_var($_POST['m_op1']);
	$x_op2 = clean_var($_POST['m_op2']);
	$x_op3 = clean_var($_POST['m_op3']);
	$x_op4 = clean_var($_POST['m_op4']);
	$x_op10 = clean_var($_POST['m_op10']);
	$x_idades = clean_var($_POST['m_idades']);

	$_SESSION['seguros_op0']=$x_op0;
	$_SESSION['seguros_op1']=$x_op1;
	$_SESSION['seguros_op2']=$x_op2;
	$_SESSION['seguros_op3']=$x_op3;
	$_SESSION['seguros_op4']=$x_op4;
	$_SESSION['seguros_op10']=$x_op10;
	$_SESSION['seguros_idades']=$x_idades;

	echo 'ok';

?>