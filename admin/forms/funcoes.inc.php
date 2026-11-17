<?
function show_page_buttons()
{
	global $task_button,$btn_class;
	?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
    <tr>
    	<td align="center" valign="middle" class="t1">
			<input class="btn <?=$btn_class?>" type="submit" name="Submit" value="<?=$task_button;?>">
            <input class="btn btn-secondary" type="button" name="cancelar" value="Cancel" onMouseUp="javascript:history.go(-1);">
		</td>
     </tr>
	</table>
<?
}
function show_fast_tools()
{
	global $ID;?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>

		<td class="titulo2 text-center">FAST TOOLS</td>
	</tr>
	<tr>
		<td class="preto text-center"><input class="btn btn-primary" type="button" style="width:160px" value="<?=write_lang('key_new_form','');?>" onclick="javascript:window.open('pagina.php?ID=0&ID_ORG=0&Dump=<?=uniqid("");?>','_self')"></td>
	</tr><?
	if($ID){?>
	<tr>
		<td class="preto text-center"><input class="btn btn-primary" type="button" style="width:160px" value="<?=write_lang('key_configure_fields','');?>" onclick="javascript:window.open('fields/list.php?ID_ORG=<?=$ID;?>&num=25','_self')"></td>
	</tr>
    <tr>
		<td class="preto text-center"><input class="btn btn-primary" type="button" style="width:160px" value="<?=write_lang('key_view_info','');?>" onclick="javascript:window.open('records/list.php?ID_ORG=<?=$ID;?>&num=25','_self')"></td>
	</tr>
    <? }?>
	</table>

<? }
function show_page_tools($filter=0)
{?>
	<div class="panel panel-inverse overflow-hidden m-b-12">
        <div class="panel-heading">
            <h4 style="color:#fff">FORMULÁRIOS</h4>
        </div>
    </div>
<? }
function show_page_bottom_tools()
{?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td width="130px" class="t1">&nbsp;With the selectd:</td>
		<td width="125px"><select name="flag" id="flag" class="select1">
				<option value="" selected="selected"></option>
				<option value="1">Delete</option>
				<option value="2">Reorder</option>
			</select>
		</td>
		<td>&nbsp;<input type="submit"  name="submeter" value="aply action" class="btn btn-primary" style="width:120px"></td>
	</tr>
	</table>

<?
}
function show_content_list()
{
	$path="../../core/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$dados=new DataObj;
	$hRes=$dados->db_open($db_link,$DB_name,"Select * from forms where sub<>1");
	$count=0;
	while(!$dados->EOF())
	{
		$count++;
		if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
		else				{$cor="#FFFFFF";$class="r2";$n="2";}
		$codigo=$dados->db_data["ID"];
		$name=$dados->db_data["Name"];
		if(!$name)$name='[EDITAR]';
		$sub=$dados->db_data["sub"];
		?>
		<table width="100%" border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
		<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'" height="32px">
	        <td width="10">&nbsp;</td>
			<td class=preto><?=$codigo?> - <? echo htmlentities_b($name);?></td>
			<td width="600" align="right" class=preto>
            	<a style="margin: 2px;" href="duplicar.php?ID=<?=$dados->db_data["ID"];?>"><img alt="" src="duplicar2.png" title="<?=write_lang('key_duplicar','Duplicar');?>" border=0 align="absmiddle" /></a>
                <a style="margin: 2px;" href="pagina.php?ID=<?=$dados->db_data["ID"];?>"><img alt="" src="config2.png" title="<?=write_lang('key_configurar','Configurar');?>" border=0 align="absmiddle" /></a>
                <a style="margin: 2px;" href="fields/list.php?ID_ORG=<?=$codigo;?>"><img alt="" src="fields2.png" border=0  title="<?=write_lang('key_editar_campos','Definir Campos');?>" align="absmiddle" /></a>
                <a style="margin: 2px;" href="records/list.php?ID_ORG=<?=$codigo;?>&num=25"><img alt="" src="records2.png"  title="<?=write_lang('key_listar_registos','Listar Registos');?>" border=0 align="absmiddle" /></a>
                <a style="margin: 2px;" href="pagina.php?ID=<?=$dados->db_data["ID"];?>&task=apagar"><img alt="" src="del2.png"  title="<?=write_lang('key_apagar_formulario','Apagar Formulário');?>" border=0 align="absmiddle" /></a>
           </td>
		</tr>
        </table><?
	  $dados->db_next();
	  $count++;
	}
	$dados->db_close();
	$hRes=$dados->db_open($db_link,$DB_name,"Select * from forms where sub=1");
	while(!$dados->EOF())
	{
		$count++;
		if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
		else				{$cor="#FFFFFF";$class="r2";$n="2";}
		$codigo=$dados->db_data["ID"];
		$name=$dados->db_data["Name"];
		if(!$name)$name='[EDITAR]';
		$sub=$dados->db_data["sub"];?>
        <table width="100%" border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
        <tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
        	<td class=preto><?=$codigo?></td>
            <td class=preto><? echo htmlentities_b($name);?></td>
            <td width="500" align="right" class=preto>
            	<a class="italico" href="pagina.php?ID=<?=$dados->db_data["ID"];?>"><?=write_lang('key_configurar','Configurar');?><img alt="" src="config.png" border=0 align="absmiddle"></a>
                <a class="italico" href="fields/list.php?ID_ORG=<?=$codigo;?>"><?=write_lang('key_editar_campos','Editar Campos');?><img alt="" src="fields.png" border=0 align="absmiddle"></a>
                <a class=italico href="records/list.php?ID_ORG=<?=$codigo;?>&num=25"><?=write_lang('key_listar_registos','Listar Registos');?><img alt="" src="records.png" border=0 align="absmiddle"></a>
                <a class="italico" href="pagina.php?ID=<?=$dados->db_data["ID"];?>&task=apagar"><?=write_lang('key_apagar_formulario','Apagar Formulário');?><img alt="" src="del.png" border=0 align="absmiddle"></a>
			</td>
		</tr>
        </table><?
		$dados->db_next();
	}
	$dados->db_close(1);
}
function GetRSRecords2($id=0,$campos="*")
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	if($id!=0) $condicao="where ID=$id";
	else $condicao="";
	$hRes=$rs->db_open($db_link,$DB_name,"Select $campos from forms $condicao");
	return $rs;
}
function InsertNewRecord($name,$email,$titulo,$texto,$texto2,$texto3,$texto4,$assunto,$listagem,$link,$sub,$campos_listagem,$tipo,$obrigatorio,$popup)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$name1=str2SQLstr($name);
	$email1=str2SQLstr($email);
	$titulo=str2SQLstr($titulo);
	$assunto=str2SQLstr($assunto);
	$listagem=str2SQLstr($listagem);
	$link=str2SQLstr($link);
	$sub=str2SQLstr($sub);
	$campos_listagem=str2SQLstr($campos_listagem);
	$tipo=str2SQLstr($tipo);
	//INSERE VALORES NA BD
	$sql="insert into forms (Name,Email,Texto0,Texto2,Texto3,Campos_listagem,Tipo,Texto4,assunto,obrigatorio) values ($name1,$email1,'$texto','$texto2','$texto3',$campos_listagem,$tipo,'$texto4',$assunto,'$obrigatorio')";
	$res=Runquery($db_link,$DB_name,$sql);
	//CASO NAO TENHA INSERIDO OS VALORES COM SUCESSO MOSTRA O ERRO
	if(!$res) echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
function DeleteRecord($id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	if(DeleteRecord_sublinks("form_fields",$id,$img_path))
	{
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$res=Runquery($db_link,$DB_name,"delete from forms where ID=$id");
		if(!$res) echo db_error($db_link);
		db_closedb_conection($db_link);
		return($res);
	}
}
//////////////////////////////////////////////////////////////7777
function DeleteRecord_sublinks($Nome,$id_org,$img_path)
{
	$dados=GetRSRecords_sublinks($Nome,0,$id_org);
	$result=1;
	while(!$dados->EOF())
	{
		$id=$dados->db_data["ID"];
		$result=DeleteRecord_sublink($Nome,$id,$img_path);
		if(!$result)
		{
			$dados->db_close();
			return $result;
		}
		$dados->db_next();
	}
	$dados->db_close();
	return $result;
}
function GetRSRecords_sublinks($Nome,$id=0,$id_org=0,$campos="*")
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
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
	$hRes=$rs->db_open($db_link,$DB_name,"Select $campos from forms $condicao");
	return $rs;
}
function DeleteRecord_sublink($Nome,$id,$img_path)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$hRes=$rs->db_open($db_link,$DB_name,"Select * from forms where ID=$id");
	$ext1=$rs->db_data["img_ext"];
	$res=Runquery($db_link,$DB_name,"delete from forms where ID=$id");
	if($res)
	{
		if($ext1!="") //CASO TENHA IMAGENS ELIMINA-AS
		{
			$ficheiro1=$id."_1.".$ext1;
			unlink($img_path.$ficheiro1);
		}
	}
	else echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
//////////////////////////////////////////////////////////////7777
function ChangeRecord($id,$name,$email,$titulo,$texto,$texto2,$texto3,$texto4,$assunto,$listagem,$link,$sub,$campos_listagem,$tipo,$obrigatorio,$popup)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$altera=0;
	$rs->db_open($db_link,$DB_name,"Select * from forms WHERE ID=$id");
	if(!$rs->EOF())$altera=1;
	$rs->db_close();
	if(!$altera) InsertNewRecord($name,$email,$titulo,$texto,$texto2,$texto3,$texto4,$assunto,$listagem,$link,$sub,$campos_listagem,$tipo,$obrigatorio,$popup);
	else
	{
		$name1=str2SQLstr($name);
		$email1=str2SQLstr($email);
		$titulo=str2SQLstr($titulo);
		$assunto=str2SQLstr($assunto);
		$listagem=str2SQLstr($listagem);
		$link=str2SQLstr($link);
		$sub=str2SQLstr($sub);
		$campos_listagem=str2SQLstr($campos_listagem);
		$tipo=str2SQLstr($tipo);
		$sql="Update forms Set Name=$name1,Email=$email1,Texto0='$texto',Texto2='$texto2',Texto3='$texto3',Campos_listagem=$campos_listagem,Tipo=$tipo,Texto4='$texto4',assunto=$assunto,obrigatorio='$obrigatorio' where ID=$id";
		$res=Runquery($db_link,$DB_name,$sql);
		db_closedb_conection($db_link);
		return($res);
	}
}
?>