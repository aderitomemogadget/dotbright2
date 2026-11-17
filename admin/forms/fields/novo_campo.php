<? 
	include "settings.inc.php";	
	InsereCampo($_GET['ID_ORG2'],$_GET['ordem'],$_GET['value'],"form_options",$_GET['email'],$_GET['associate_page']);
	echo '<script type="text/javascript">open("form_fields.php?ID='.$ID.'&ID_ORG='.$ID_ORG.'","_self");</SCRIPT>';
?>