<? 
include "settings.inc.php";

InsereCampo($ID_ORG,$value);
echo "<script type=\"text/javascript\">open(\"form_fields.php?ID=".$ID. "&ID_ORG=".$ID_ORG."\",\"frmMain\");</SCRIPT>";
?> 
