<?
include "settings.inc.php";
$Img_peq1["name"] = $_FILES['img_peq1']['name'];
$Img_peq1["temp"] = $_FILES['img_peq1']['tmp_name'];
$img_path = "../../../files/forms/";

if($task_form=="insere")
{
	$m_url="list.php?ID_ORG=".$ID_ORG."&dump=".uniqid("");
	$result=InsertNewRecord($ID_ORG,$ordem,$name,$name2,$tipo,$length,$length2,$align,$muda,$obrigatorio,$permissoes,$sequencial,$altera_only,$backoffice,$id_form,$id_campo,$herda,$matrix_type,$row_labels,$col_labels,$legenda,$formula,$full_line,$img_path, $Img_peq1,$alinhar_botao,$dentro,$linhas,$altura);
	if($result) show_box_OK(1,$m_url);
	else show_box_ERROR(1,$m_url);
}
elseif($task_form=="altera")
{
	$m_url="form_fields.php?ID=$ID&ID_ORG=".$ID_ORG."&dump=".uniqid("");
	$result=ChangeRecord($ID,$ordem,$name,$name2,$tipo,$length,$length2,$align,$muda,$obrigatorio,$permissoes,$sequencial,$altera_only,$backoffice,$id_form,$id_campo,$herda,$matrix_type,$row_labels,$col_labels,$legenda,$formula,$full_line,$img_path, $Img_peq1, $Apagar_img1, $tem_foto1,$alinhar_botao,$dentro,$linhas,$altura);
	if($result) show_box_OK(2,$m_url);
	else show_box_ERROR(2,$m_url);
}
elseif($task_form=="apaga")
{
	$m_url="list.php?ID_ORG=".$ID_ORG."&dump=".uniqid("");
	$result=DeleteRecord($ID);
	if($result) show_box_OK(3,$m_url);
	else show_box_ERROR(3,$m_url);
}
?>