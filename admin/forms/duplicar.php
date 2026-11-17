<?
include "settings.inc.php";
	// error_reporting(E_ALL);
		// ini_set('display_errors', 1);
$m_url="../forms/list.php?dump=".uniqid("");
$result=duplicar($ID);
if($result) show_box_OK(1,$m_url);
else show_box_ERROR(1,$m_url);

function duplicar($ID)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$Nome;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$rs2=new DataObj;

	$sql="Select * from forms where ID=".$ID;
	$hRes=$rs->db_open($db_link,$DB_name,$sql);
	$ID_form=$rs->db_data["ID"];
	$name=$rs->db_data["Name"]." - (Copia)";
	$email=$rs->db_data["Email"];
	$texto=$rs->db_data["Texto0"];
	$texto2=$rs->db_data["Texto2"];
	$texto3=$rs->db_data["Texto3"];
	$sub=$rs->db_data["sub"];
	$campos_listagem=$rs->db_data["Campos_listagem"];
	$tipo=$rs->db_data["Tipo"];
	$assunto=$rs->db_data["assunto"];
	$name1=str2SQLstr($name);
	$email1=str2SQLstr($email);
	$titulo=str2SQLstr($titulo);
	$assunto=str2SQLstr($assunto);
	$listagem=str2SQLstr($listagem);
	$link=str2SQLstr($link);
	$sub=str2SQLstr($sub);
	$campos_listagem=str2SQLstr($campos_listagem);
	$tipo=str2SQLstr($tipo);
	$sql="insert into forms(Name,Email,Texto0,Texto2,Texto3,Campos_listagem,Tipo,Texto4,assunto) values ($name1,$email1,'$texto','$texto2','$texto3','',$tipo,'$texto4',$assunto)";
	$res=Runquery($db_link,$DB_name,$sql);
	$idd=db_get_last_insert_id($db_link);
	if(!$res) echo db_error($db_link);
	else
	{
		$sql1="Select * from form_fields where ID_ORG=".$ID;
		$rs2->db_open($db_link,$DB_name,$sql1);
		while(!$rs2->EOF())
		{
			$id_campos=$rs2->db_data["ID"];
			$ordem=$rs2->db_data["Ordem"];
			$name=$rs2->db_data["Name"];
			$name2=$rs2->db_data["Name2"];
			$length=$rs2->db_data["Length"];
			$length2=$rs2->db_data["Length2"];
			$tipo=$rs2->db_data["Tipo"];
			$align=$rs2->db_data["Align"];
			$muda=$rs2->db_data["Muda"];
			$obrigatorio=$rs2->db_data["Obrigatorio"];
			$permissoes=$rs2->db_data["Permissoes"];
			$altera_only=$rs2->db_data["Altera_only"];
			$sequencial=$rs2->db_data["Sequencial"];
			$backoffice=$rs2->db_data["Backoffice"];
			$id_form=$rs2->db_data["id_form"];
			$id_campo=$rs2->db_data["id_campo"];
			$herda=$rs2->db_data["herda"];
			$matrix_type=$rs2->db_data["matrix_type"];
			$row_matrix_row=$rs2->db_data["row_labels"];
			$row_matrix_col=$rs2->db_data["col_labels"];
			$legenda=$rs2->db_data["legenda"];
			$formula=$rs2->db_data["formula"];
			$full_line=$rs2->db_data["Full_line"];
			$ordem=str2SQLstr($ordem);
			$name=str2SQLstr($name);
			$name2=str2SQLstr($name2);
			$tipo=str2SQLstr($tipo);
			$length=str2SQLstr($length);
			$length2=str2SQLstr($length2);
			$align=str2SQLstr($align);
			$muda=str2SQLstr($muda);
			$obrigatorio=str2SQLstr($obrigatorio);
			$permissoes=str2SQLstr($permissoes);
			$sequencial=str2SQLstr($sequencial);
			$altera_only=str2SQLstr($altera_only);
			$backoffice=str2SQLstr($backoffice);
			$id_campo=str2SQLstr($id_campo);
			$id_form=str2SQLstr($id_form);
			$herda=str2SQLstr($herda);
			$matrix_type=str2SQLstr($matrix_type);
			$row_matrix_row=str2SQLstr($row_matrix_row);
			$row_matrix_col=str2SQLstr($row_matrix_col);
			$legenda=str2SQLstr($legenda);
			$formula=str2SQLstr($formula);
			$full_line=str2SQLstr($full_line);		
			$sql3="insert into form_fields(ID_ORG,Ordem,Name,Name2,Tipo,Length,Length2,Align,Muda,Obrigatorio,Sequencial,Permissoes,Altera_only,Backoffice,id_form,id_campo,herda,matrix_type,row_labels,col_labels,legenda,formula,Full_line) values ($idd,$ordem,$name,$name2,$tipo,$length,$length2,$align,$muda,$obrigatorio,$sequencial,$permissoes,$altera_only,$backoffice,$id_form,$id_campo,$herda,$matrix_type,$row_matrix_row,$row_matrix_col,$legenda,$formula,$full_line)";
			
			$res2=Runquery($db_link,$DB_name,$sql3);		
			$rs2->db_next();
		}
		$rs2->db_close();
	}
	return $res;
}
?>