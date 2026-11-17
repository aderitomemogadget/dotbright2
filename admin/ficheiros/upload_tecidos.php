<?php
include "settings.inc.php";

if($update==1 && $id_file)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$img_name=$_POST['img_name'.$id_file];
	$img_name=addslashes($img_name);
	$img_ordem=$_POST['img_ordem'.$id_file] ? $_POST['img_ordem'.$id_file] : 0;

	$sql="update $tablename set Name = '$img_name', Ordem=$img_ordem WHERE ID = $id_file";
	$res=Runquery($db_link,$DB_name,$sql);
}
elseif($del==1 && $id_file)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$sql="delete from $tablename WHERE ID = $id_file";
	$res=Runquery($db_link,$DB_name,$sql);
	if($res)
	{
		unlink($img_path.$file1);
		unlink($img_path.$file2);
	}
}
else
{
	if(!substr_count($_FILES['ad_img_peq'.$id_file]['name'],".jpg") && !substr_count($_FILES['ad_img_peq'.$id_file]['name'],".JPG")
		&& !substr_count($_FILES['ad_img_peq'.$id_file]['name'],".gif") && !substr_count($_FILES['ad_img_peq'.$id_file]['name'],".GIF")
		&& !substr_count($_FILES['ad_img_peq'.$id_file]['name'],".png") && !substr_count($_FILES['ad_img_peq'.$id_file]['name'],".PNG")) die();
	$img_path=dirname(__FILE__)."/../../files/".$tablename."/";
	$Img_peq1['Name']=$_FILES['ad_img_peq'.$id_file]['name'];
	$Img_peq1["temp"]=$_FILES['ad_img_peq'.$id_file]['tmp_name'];
	$altera_img=0;if($Img_peq1['Name']){$altera_img=1;$pos=strrpos($Img_peq1['Name'],".");$img_ext=substr($Img_peq1['Name'],$pos+1,strlen($Img_peq1['Name'])+1-$pos);}
	if(!$id_file && !$altera_img)die();
	$img_name=$_POST['img_name'.$id_file];
	$img_ordem=$_POST['img_ordem'.$id_file] ? $_POST['img_ordem'.$id_file] : 0;
	if(!$img_name)
	{
		$img_name=$Img_peq1['Name'];
	}
	$img_name=addslashes($img_name);
	if(!$id_file && $altera_img)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$sql="insert into $tablename(ID_ORG,img_ext,Data,Name,Ordem) values ('$id_org','$img_ext','".date("Y-m-d")."','".$img_name."',$img_ordem)";
		$res=Runquery($db_link,$DB_name,$sql);
		if($res)$new_id=db_get_last_insert_id($db_link);
		if($altera_img)copia_segura($Img_peq1["temp"],$img_path.$id_org."_".$new_id."_1.".$img_ext);
	}
	else
	{
		$new_id=$id_file;
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$sql="update $tablename set img_ext = '$img_ext',Name='".$img_name."', Ordem=$img_ordem WHERE ID = $id_file";
		$res=Runquery($db_link,$DB_name,$sql);
		if($altera_img)copia_segura($Img_peq1["temp"],$img_path.$id_org."_".$id_file."_1.".$img_ext);
	}
}
global $page_id,$ID_Torneio,$fight; ?>
<script language="javascript" type="text/javascript">
	parent.window.location.reload(true);
</script>