<?
	include "settings.inc.php";
	$retorno="config.php";
	if($_POST['slashes'])
	{
		if($_POST['ID']==0)
		{
			$result=InsertNewConfig();
			if($result)	show_box_OK(1,$retorno);
			else		show_box_ERROR(1,$retorno);
		}
		elseif ($_POST['ID']==1)
		{
			$result= ChangeConfig();
			if($result)	show_box_OK(2,$retorno);
			else		show_box_ERROR(2,$retorno);	
		}
	}else show_box_ERROR(4,$retorno);
?>