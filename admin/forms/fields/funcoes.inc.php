<?
function show_page_buttons()
{
	global $task_button,$ID_ORG;
	?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
    <tr>
    	<td align="center" valign="middle" class="t1"> <input class="btn btn-primary" type="submit" name="Submit" value="<? echo $task_button;?> Field" style="cursor:pointer"> <input class="btn btn-secondary" type="button" name="cancelar" value="Go Back" style="cursor:pointer" onClick="window.open('list.php?ID_ORG=<?=$ID_ORG?>','_self')"></td>
     </tr>
	</table>
	<img src="../../images/bsh.png" width="100%" height="30px">
<?
}
function show_fast_tools()
{
	global $ID_ORG;?>

	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
		<td width="5px" align="left"></td>
		<td class="titulo2" align="center">FAST TOOLS</td>
		<td width="5px" align="right"></td>
	</tr>
	<tr>
		<td width="5px" align="left"></td>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<?=write_lang('key_new_field','');?>" onclick="javascript:window.open('form_fields.php?ID=0&ID_ORG=<?=$ID_ORG?>&Dump=<?=uniqid("");?>','_self')"></td>
		<td width="5px" align="right"></td>
	</tr>
	</table>
	<img src="../../images/bsh.png" width="100%" height="30px">
<? }
function GetRSRecords2($id=0,$id_org=0,$campos="*")
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	if($id!=0)
	{
		$condicao="where ID=$id";
		if($id_org!=0) $condicao="$condicao and where ID_ORG=$id_org";
	}
	else
	{
		$condicao="";
		if($id_org!=0) $condicao="where ID_ORG=$id_org";
	}
	$hRes=$obj->db_open($db_link,$DB_name,"Select $campos from form_fields $condicao order by Ordem Asc");
	return $obj;
}
function InsertNewRecord($id_org,$ordem=0,$name,$name2,$tipo=0,$length=0,$length2=0,$align=1,$muda=0,$obrigatorio=0,$permissoes=0,$sequencial=0,$altera_only=0,$backoffice=0,$id_form=0,$id_campo=0,$herda=0,$matrix_type,$row_matrix_row,$row_matrix_col,$legenda,$full_line=0,$img_path, $Img_peq1,$alinhar_botao,$dentro,$linhas,$altura)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$altera_img=0;if($Img_peq1["name"]){$altera_img=1;$pos=strrpos($Img_peq1["name"],".");$img_ext=substr($Img_peq1["name"],$pos+1,strlen($Img_peq1["name"])+1-$pos);}

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

	//INSERE VALORES NA BD
	$sql="insert into form_fields (ID_ORG,Ordem,Name,Name2,Tipo,Length,Length2,Align,Muda,Obrigatorio,Sequencial,Permissoes,Altera_only,Backoffice,id_form,id_campo,herda,matrix_type,row_labels,col_labels,legenda,formula,Full_line,img_ext,alinhar_botao,dentro,linhas,altura) values ($id_org,$ordem,$name,$name2,$tipo,$length,$length2,$align,$muda,$obrigatorio,$sequencial,$permissoes,$altera_only,$backoffice,$id_form,$id_campo,$herda,$matrix_type,$row_matrix_row,$row_matrix_col,$legenda,$formula,$full_line,'$img_ext','$alinhar_botao','$dentro','$linhas','$altura')";
	$res=Runquery($db_link,$DB_name,$sql);
	if($res)
	{
		$new_id=db_get_last_insert_id($db_link);
		if($altera_img)	copia_segura($new_id,$img_path.$new_id."_bt_1.".$ext1);
	}
	else echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
function DeleteRecord($id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$res=Runquery($db_link,$DB_name,"delete from form_fields where ID=$id");
	if(!$res) echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
function ChangeRecord($id,$ordem,$name,$name2,$tipo,$length,$length2,$align=1,$muda=0,$obrigatorio=0,$permissoes=0,$sequencial=0,$altera_only=0,$backoffice=0,$id_form=0,$id_campo=0,$herda=0,$matrix_type,$row_matrix_row,$row_matrix_col,$legenda,$formula,$full_line=0,$img_path, $Img_peq1, $Apagar_img1, $tem_foto1,$alinhar_botao,$dentro,$linhas,$altura)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$altera=0;
	$rs->db_open($db_link,$DB_name,"Select * from form_fields WHERE ID=$id");
	if(!$rs->EOF())
	{
		$altera=1;
		$img_ext=$rs->db_data["img_ext"];
	}
	$rs->db_close();
	if(!$altera)return InsertNewRecord($id_org,$ordem,$name,$name2,$tipo,$length,$length2,$align,$muda,$obrigatorio,$permissoes,$sequencial,$altera_only,$backoffice,$id_form,$id_campo,$herda,$matrix_type,$row_matrix_row,$row_matrix_col,$legenda,$formula,$full_line,$img_path, $Img_peq1,$alinhar_botao,$dentro,$linhas,$altura);
	else
	{
		$altera_img=0;if($Img_peq1["name"]){$altera_img=1;$pos=strrpos($Img_peq1["name"],".");$img_ext=substr($Img_peq1["name"],$pos+1,strlen($Img_peq1["name"])+1-$pos);}
		//IMAGEM NUMERO 1
		if($tem_foto1 && $Apagar_img1){$ficheiro1=$id."_1.".$img_ext;unlink($img_path.$ficheiro1);if(!$altera_img)$img_ext='';}
		if($altera_img)copia_segura($Img_peq1["temp"],$img_path.$id."_1.".$img_ext);

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
		$legenda=str2SQLstr($legenda);
		$formula=str2SQLstr($formula);

		$matrix_type=str2SQLstr($matrix_type);
		$row_matrix_row=str2SQLstr($row_matrix_row);
		$row_matrix_col=str2SQLstr($row_matrix_col);
		$full_line=str2SQLstr($full_line);

		$sql="Update form_fields Set Ordem=$ordem,Name=$name,Name2=$name2,Tipo=$tipo,Length=$length,Length2=$length2,Align=$align,Muda=$muda,Obrigatorio=$obrigatorio,Sequencial=$sequencial,Permissoes=$permissoes,Altera_only=$altera_only,Backoffice=$backoffice,id_form=$id_form,id_campo=$id_campo,herda=$herda,matrix_type=$matrix_type,row_labels=$row_matrix_row,col_labels=$row_matrix_col,legenda=$legenda,formula=$formula,Full_line=$full_line,img_ext='$img_ext', alinhar_botao='$alinhar_botao',dentro='$dentro',linhas='$linhas',altura='$altura' where ID=$id";

		$res=Runquery($db_link,$DB_name,$sql);
		db_closedb_conection($db_link);
		return($res);
	}
}

function show_data()
{
	echo "<select name=\"dia\">";
	for($i=1;$i<32;$i++)
	{
		echo "                      <option value=\"";
		if($i<10) echo "0";
		echo "$i\"";
		if($D["mday"]==$i)	echo " selected";
		echo">";
		if($i<10) echo "0";echo "$i</option>\n";
	}
	echo "                    </select>\n";
	echo ".\n";
	echo "                    <select name=\"mes\">\n";
	for($i=1;$i<13;$i++)
	{
		echo "                      <option value=\"";
		if($i<10) echo "0";
		echo "$i\"";
		if($D["mon"]==$i)
			echo " selected";
		echo">";
		if($i<10) echo "0";
		echo "$i</option>\n";
	}
	echo "                    </select>\n";
	echo ".\n";
	echo "                    <select name=\"ano\">";
	for($i=2010;$i<(date("Y")+5);$i++)
	{
		echo "                      <option value=\"";
		if($i<10) echo "0";
		echo "$i\"";
		if($D["year"]==$i)
			echo " selected";
		echo ">$i</option>\n";
	}
}
?>