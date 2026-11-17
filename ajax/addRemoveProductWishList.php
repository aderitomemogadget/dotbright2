<?php
	$path=dirname( __FILE__ ).'/../core/';	
	include $path.'core_files.php';	
	if(isset($_POST['action'])) $action=$_POST['action']; else $action='';
	if(isset($_POST['prodid']) && intval($_POST['prodid']) > 0) $prodid=intval($_POST['prodid']);	else $prodid=0;
	if($prodid > 0 && $action != '') addRemoveWishList($prodid,$action);
?>