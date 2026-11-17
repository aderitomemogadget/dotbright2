<? 
	include "settings.inc.php";
	ApagaCampo($_GET['ID_Campo'],"form_options");
	echo "<script type=\"text/javascript\">open(\"form_fields.php?ID=".$ID. "&ID_ORG=".$ID_ORG."\",\"frmMain\");</SCRIPT>"; 
?>