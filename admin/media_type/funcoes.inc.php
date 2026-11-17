<?php
function get_banner_lingua($lingua='')
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$poli_area;
	$Cabecalho = 'Todas';
	$dbh = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	if($dbh)
	{
		$result=new DataObj;
		$result->db_open($dbh,$DB_name,"SELECT Cabecalho FROM linguas WHERE Sigla ='".$lingua."' ORDER BY Ordem");
		if(!$result->EOF()) $Cabecalho=$result->db_data["Cabecalho"];
		$result->db_close();
	}
	return $Cabecalho;
}
function show_page_buttons($btn_class='')
{
	global $task_button;
	?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
    <tr>
    	<td align="center" valign="middle" class="t1">
        	<input class="btn <?=$btn_class?>" type="submit" name="Submit" value="<?=$task_button;?> ">
        	<input class="btn btn-secondary" type="button" name="cancelar" value="<? echo write_lang('key_cancelar','Cancelar');?>" onClick="window.open('../media/list.php','_self');">
		</td>
     </tr>
	</table>
<?
}
function show_fast_tools()
{
	global $ID_ORG,$ID;?>
    <table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
	<tr>
		<td class="titulo2" align="center">FAST TOOLS</td>
	</tr>
	<tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<? echo write_lang('key_criar_novo_grupo','Criar Novo Grupo');?>" onclick="javascript:window.open('pagina.php?ID=0&ID_ORG=<?=$ID_ORG?>&Dump=<?=uniqid("");?>','_self')"></td>
	</tr>
	<tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<? echo write_lang('key_inserir_novo_elemento','Inserir Novo Elemento');?>" onclick="javascript:window.open('../media/pagina.php?ID=0&ID_ORG=<?=$ID?>&Dump=<?=uniqid("");?>','_self')"></td>
	</tr>
	</table>
<? }
function show_lang_pro($ver="")
{
    global $n_linguas,$linguas,$admin_lang;
    if($n_linguas>1)
    {
		$m_str=$linguas;
		echo '<select name="m_lingua'.$ver.'">';
		while($m_str <> "")
        {
            $act_lang=substr($m_str,0,strpos($m_str,","));
            if($act_lang==""){$act_lang=$m_str;$m_str="";}
            else $m_str=substr($m_str,strpos($m_str,",")+1,strlen($m_str));
			echo '<option value="'.$act_lang.'"';
			if($act_lang==$admin_lang) echo ' selected';
			echo '>'.$act_lang.'</option>';
        }
		echo '</select>';
	}
}
function GetRSRecords2($id=0,$campos="*")
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	if($id!=0)	$condicao="where ID=$id";
	else		$condicao="";
	$hRes=$rs->db_open($db_link,$DB_name,"Select $campos from media_type $condicao");
	return $rs;
}

function InsertNewRecord($name,$help,$menu_tipo,$comportamento,$altura,$largura,$select_image,$tipo_fundo,$bg_color,$legendas,$aleatoria)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$name1=str2SQLstr($name);
	$help1=str2SQLstr($help);
	$menu_tipo1=str2SQLstr($menu_tipo);
	$comportamento=str2SQLstr($comportamento);
	$altura=str_replace(",",".",$altura);
	$largura=str_replace(",",".",$largura);
	$altura=str2SQLstr($altura);
	$largura=str2SQLstr($largura);
	//INSERE VALORES NA BD
	$sql="insert into media_type (Nome,Help,Tipo,Comportamento,altura,largura,select_image,tipo_fundo,bg_color,legendas,Aleatorio) values ($name1,$help1,$menu_tipo1,$comportamento,$altura,$largura,'$select_image','$tipo_fundo','$bg_color','$legendas','$aleatoria')";
	$res=Runquery($db_link,$DB_name,$sql);
	$res=Runquery($db_link,$DB_name,'DROP TABLE IF EXISTS media_type_m');//limpar a tabela de ram
	if(!$res)	echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
function DeleteRecord($id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$res=Runquery($db_link,$DB_name,"delete from media_type where ID=$id");
	if(!$res)	echo db_error($db_link);
	$res=Runquery($db_link,$DB_name,'DROP TABLE IF EXISTS media_type_m');//limpar a tabela de ram
	db_closedb_conection($db_link);
	return($res);
}
function ChangeRecord($id,$name,$help,$menu_tipo,$comportamento,$altura,$largura,$select_image,$tipo_fundo,$bg_color,$legendas,$aleatoria)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$altera=0;
	$rs->db_open($db_link,$DB_name,"Select * from media_type WHERE ID=$id");
	if(!$rs->EOF())
	{
		$altera=1;
		$w=$rs->db_data["largura"];
		$h=$rs->db_data["altura"];
	}
	$rs->db_close();
	if(!$altera)return InsertNewRecord($name,$help,$menu_tipo,$comportamento,$altura,$largura,$select_image,$tipo_fundo,$bg_color,$legendas,$aleatoria);
	else
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs=new DataObj;

		$name1=str2SQLstr($name);
		$help1=str2SQLstr($help);
		$menu_tipo1=str2SQLstr($menu_tipo);
		$comportamento=str2SQLstr($comportamento);
		$altura=str_replace(",",".",$altura);
		$largura=str_replace(",",".",$largura);
		$altura=str2SQLstr($altura);
		$largura=str2SQLstr($largura);
		$sql="Update media_type Set Nome=$name1,Help=$help1,Tipo=$menu_tipo1,Comportamento=$comportamento,largura=$largura,altura=$altura,select_image='$select_image',tipo_fundo='$tipo_fundo',bg_color='$bg_color',legendas='$legendas',Aleatorio='$aleatoria' where ID=$id";
		$res=Runquery($db_link,$DB_name,$sql);
		if($h != $altura || $w != $largura)
		{
			//regera imagens
			$hRes=$rs->db_open($db_link,$DB_name,"Select * from media where Tipo='".$id."'");
			while(!$rs->EOF())
			{
				$t++;
				$codigo=$rs->db_data["ID"];
				$I1=$rs->db_data["img_ext"];
				if($I1!="" && file_exists("../../files/media/$codigo"."_1_big.".$I1))unlink("../../files/media/$codigo"."_1_big.".$I1);
				if($I1!="" && file_exists("../../files/media/$codigo"."_1_red.".$I1))unlink("../../files/media/$codigo"."_1_red.".$I1);
				if($I1!="" && file_exists("../../files/media/$codigo"."_1_med.".$I1))unlink("../../files/media/$codigo"."_1_med.".$I1);
				if($I1!="" && file_exists("../../files/media/$codigo"."_1_med_p.".$I1))unlink("../../files/media/$codigo"."_1_med_p.".$I1);
				$rs->db_next();
			}
			$rs->db_close();
		}
		$res=Runquery($db_link,$DB_name,'DROP TABLE IF EXISTS media_type_m');//limpar a tabela de ram
		db_closedb_conection($db_link);
		return($res);
	}
}
function show_easy_create()
{
	global $browser,$ID;
	if(($browser=="Firefox" || $browser=="Mozilla") && $ID)
	{?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
	<tr>

		<td class="titulo2" align="center">EASY CREATE</td>

	</tr>
	<tr>

		<td class=preto align="center">

			<table width="100%" border=0 cellspacing=0 cellpadding=0 height="100px" bgcolor="#FFFFFF">
			<tr>
				<td align="center"><? include "easycreate.php"; ?></td>
			</tr>
			</table>

		</td>

	</tr>
	</table>

<? }}
?>