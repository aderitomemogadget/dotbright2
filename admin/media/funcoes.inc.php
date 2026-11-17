<?php
function show_page_buttons()
{
	global $task_button,$btn_class;
	?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
    <tr>
    	<td class="t1" valign="middle" align="center">
        	<input class="btn <?=$btn_class?>" type="submit" name="Submit" value="<?=$task_button;?> ">
        	<input class="btn btn-secondary" type="button" name="cancelar" value="<? echo write_lang('key_cancelar','Cancelar');?>" onclick="javascript:window.open('list.php','_self');">
		</td>
     </tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<?
}
function show_page_tools($filter=0)
{?>
	<div class="panel panel-inverse overflow-hidden m-b-12">
        <div class="panel-heading">
            <h4 style="color:#fff">MEDIA</h4>
        </div>
    </div>
    <table class="p2" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td width="29" align="center" style="color:#fff">Tipos de media</td>
	</tr>
	</table>
<? }
function show_fast_tools()
{
	global $ID, $ID_ORG, $Familia, $comportamento, $comportamentos_arr;
	?>
    <table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
	<tr>
		<td width="5px" align="left"></td>
		<td class="titulo2" align="center">FAST TOOLS</td>
		<td width="5px" align="right"></td>
	</tr>
	<tr>
		<td width="5px" align="left"></td>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<? echo write_lang('key_criar_novo_media','Criar Novo Media');?>" onclick="javascript:window.open('../media_type/pagina.php?ID=0&ID_ORG=0&Dump=<?=uniqid("");?>','_self')"></td>
		<td width="5px" align="right"></td>
	</tr>
	<tr>
		<td width="5px" align="left"></td>
        <?php
        if(isset($Familia) && $Familia == 0 && in_array($comportamento,$comportamentos_arr))
		{
		?>
        	<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<? echo write_lang('key_inserir_elemento','Inserir elemento');?>" onclick="javascript:window.open('pagina.php?Familia=<?=$ID?>&Dump=<?=uniqid("");?>','_self')"></td>
         <?
		} else {
		 ?>
        <td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<? echo write_lang('key_inserir_elemento','Inserir elemento');?>" onclick="javascript:window.open('pagina.php?ID=0&ID_ORG=<?=$ID_ORG?>&Dump=<?=uniqid("");?>','_self')"></td>
        <?
		}
		?>
		<td width="5px" align="right"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">

<? }
function conta_banners($tipo)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$dbh=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$result=new DataObj;
	$result->db_open($dbh,$DB_name,"select count(*) as total from media where Tipo='$tipo'");
	$result->db_close();
	return $result->db_data["total"];
}
function banner_tipos($menu_ID=0)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$poli_area;

    echo '<select name="tipo" style="width:100%" class="form-control">';
	$dbh=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$flag=0;
	if($dbh)
	{
		$result=new DataObj;
		$result->db_open($dbh,$DB_name,"select * from media_type");
		while (!$result->EOF())
		{
		    $codigo=$result->db_data["ID"];
            $nome=$result->db_data["Nome"];

			$tipo = $result->db_data["Comportamento"];
			$largura=$result->db_data["largura"];
			$altura=$result->db_data["altura"];
			$Protegido=$result->db_data["Protegido"];

			if($Protegido == 0)
			{
				for($a=0; $a < count($GLOBALS['publicidade']['codigo']); $a++)
				{
					if($tipo == $GLOBALS['publicidade']['codigo'][$a] || ($tipo == '0' && !$GLOBALS['publicidade']['codigo'][$a])) $comportamento = $GLOBALS['publicidade']['nome'][$a] . (($largura > 0 && $altura > 0) ? (' - L:' . $largura . ' x A:' . $altura) : ($largura > 0 ? ' - L:' . $largura : ($altura > 0 ? ' -  A:' . $altura : '' )));
				}

				echo '<option value="'. $codigo .'"';
				if($menu_ID == 0 && $flag==0){ echo 'selected="selected"'; $flag=1; }
				if($menu_ID == $codigo){ echo 'selected="selected"';$flag=1; }
				echo ">".$nome .' ('.$comportamento.')</option>';
			}
			$result->db_next();
		}
		$result->db_close();
	}
	echo '</select>';
}
function banner_linguas($lingua='')
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$poli_area;

    echo '<select name="LINGUA" style="width:100px" class="form-control">';
	$dbh=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$flag=0;
	if($dbh)
	{
		$result=new DataObj;
		$result->db_open($dbh,$DB_name,"SELECT Sigla, Cabecalho FROM linguas ORDER BY Ordem");
		echo '<option value=""';
		if($lingua == ''){ echo 'selected="selected"';}
		echo '>TODAS</option>';

		while (!$result->EOF())
		{
		    $codigo=$result->db_data["ID"];
            $Cabecalho=$result->db_data["Cabecalho"];

			$Sigla = $result->db_data["Sigla"];


				echo '<option value="'. $Sigla .'"';
				if($lingua == $Sigla){ echo 'selected="selected"';}
				echo ">".$Cabecalho .'</option>';

			$result->db_next();
		}
		$result->db_close();
	}
	echo '</select>';
}
function get_banner_lingua($lingua='')
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$poli_area;
	$Cabecalho = 'TODAS';
	$dbh = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	if($dbh)
	{
		$result=new DataObj;
		$result->db_open($dbh,$DB_name,"SELECT Cabecalho FROM linguas WHERE Sigla ='".$lingua."' ORDER BY Ordem");
		if(!$result->EOF())$Cabecalho=$result->db_data["Cabecalho"];
		$result->db_close();
	}
	return $Cabecalho;
}
function banner_media($tipo = 0, $menu_ID = 0)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$poli_area;

    echo '<select name="Familia" style="width:100%" class="form-control">';
	$dbh=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$flag=0;
	if($dbh)
	{
		$result=new DataObj;
		$result->db_open($dbh,$DB_name,"select * from media WHERE Tipo='".$tipo."'");
		while (!$result->EOF())
		{
		    $codigo = $result->db_data["ID"];
            $nome = $result->db_data["Name"];

			$tipo = $result->db_data["Comportamento"];
			$largura = $result->db_data["largura"];
			$altura = $result->db_data["altura"];
			$Protegido = $result->db_data["Protegido"];

			if($Protegido == 0)
			{
				for($a=0; $a < count($GLOBALS['publicidade']['codigo']); $a++)
				{
					if($tipo == $GLOBALS['publicidade']['codigo'][$a] || ($tipo == '0' && !$GLOBALS['publicidade']['codigo'][$a])) $comportamento = $GLOBALS['publicidade']['nome'][$a] . (($largura > 0 && $altura > 0) ? (' - L:' . $largura . ' x A:' . $altura) : ($largura > 0 ? ' - L:' . $largura : ($altura > 0 ? ' -  A:' . $altura : '' )));
				}

				echo '<option value="'. $codigo .'"';
				if($menu_ID == 0 && $flag==0){ echo 'selected'; $flag=1; }
				if($menu_ID == $codigo){ echo "selected";$flag=1; }
				echo ">".$nome .' ('.$comportamento.')</option>';
			}
			$result->db_next();
		}
		$result->db_close();
	}
	echo '</select>';
}
function InsertNewRecord($nome,$url,$largura,$altura,$tipo,$pagina,$anunciante,$data1,$data2,$n_impressoes,$n_cliques,$peso,$visible,$img_path,$Img_peq1,$script,$permanente=0,$Descritivo="",$tema="",$Familia)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$poli_area;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$altera_img=0;if($Img_peq1["name"]){$altera_img=1;$pos=strrpos($Img_peq1["name"],".");$img_ext=substr($Img_peq1["name"],$pos+1,strlen($Img_peq1["name"])+1-$pos);}

    $nome1=str2SQLstr($nome);
    $url1=str2SQLstr($url);
    $largura1=str2SQLstr($largura);
    $altura1=str2SQLstr($altura);
    $tipo1=str2SQLstr($tipo);
    $pagina1=str2SQLstr($pagina);
    $anunciante1=str2SQLstr($anunciante);
    $data11=date2SQLstr($data1);
    $data21=date2SQLstr($data2);
    $n_impressoes1=str2SQLstr($n_impressoes);
    $n_cliques1=str2SQLstr($n_cliques);
    $peso1=str2SQLstr($peso);
	$visible1=str2SQLstr($visible);
	$script=str2SQLstr($script);
	$permanente1=str2SQLstr($permanente);
	$Familia=str2SQLstr($Familia);
	$LINGUA = $_POST['LINGUA'];

	//$sql="insert into media (Name,URL,img_ext,Largura,Altura,Tipo,Pagina,ID_Anunciante,Data1,Data2,N_impressoes,N_cliques,Peso,Visible,Script,Permanente,Descritivo,TemaID) values ($nome1,$url1,'$img_ext',$largura1,$altura1,$tipo1,$pagina1,$anunciante1,$data11,$data21,$n_impressoes1,$n_cliques1,$peso1,$visible1,$script,$permanente1,'$Descritivo','$tema')";
	$sql="insert into media (
		Name,
		URL,
		img_ext,
		Largura,
		Altura,
		Tipo,
		Pagina,
		ID_Anunciante,
		Data1,
		Data2,
		N_impressoes,
		N_cliques,
		Peso,
		Visible,
		Script,
		Permanente,
		Descritivo,
		TemaID,
		Familia,
		LINGUA
	)
	values
	(
		$nome1,
		$url1,
		'$img_ext',
		$largura1,
		$altura1,
		$tipo1,
		$pagina1,
		$anunciante1,
		$data11,
		$data21,
		$n_impressoes1,
		$n_cliques1,
		$peso1,
		$visible1,
		$script,
		$permanente1,
		'$Descritivo',
		'$tema',
		$Familia,
		'$LINGUA'
	)";
	$res=Runquery($db_link,$DB_name,$sql);
	if($res)
	{
		$new_id=db_get_last_insert_id($db_link);
		// Runquery($db_link,$DB_name,'DROP TABLE IF EXISTS media_m');//limpar a tabela de ram
		if($altera_img)	copia_segura($Img_peq1["temp"],$img_path.$new_id."_1.".$img_ext);
	}
	else echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
function DeleteRecord($id,$img_path)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $sql= "delete from media where ID=$id";
	$res=Runquery($db_link,$DB_name,$sql);
	if($res)
	{
		// Runquery($db_link,$DB_name,'DROP TABLE IF EXISTS media_m');//limpar a tabela de ram
		if($ext1!="")
		{
			$ficheiro1=$id."_1.".$ext1;
			unlink($img_path.$ficheiro1);
		}
	}
	else echo db_error($db_link);
	return($res);
}
function ChangeRecord($id,$nome,$url,$largura,$altura,$tipo,$pagina,$anunciante,$data1,$data2,$n_impressoes,$n_cliques,$peso,$visible,$img_path,$Img_peq1,$Apagar_img1,$tem_foto1,$script,$permanente=0,$Descritivo="",$tema="")
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	if($id_org==$id)$id_org=0;
	$altera=0;
	$rs->db_open($db_link,$DB_name,"Select * from media WHERE ID=$id");
	if(!$rs->EOF())
	{
		$altera=1;
		$img_ext=$rs->db_data["img_ext"];
	}
	$rs->db_close();
	if(!$altera)return InsertNewRecord($nome,$url,$largura,$altura,$tipo,$pagina,$anunciante,$data1,$data2,$n_impressoes,$n_cliques,$peso,$visible,$img_path,$Img_peq1,$script,$permanente,$Descritivo,$tema);
	else
	{
		$altera_img=0;if($Img_peq1["name"]){$altera_img=1;$pos=strrpos($Img_peq1["name"],".");$img_ext=substr($Img_peq1["name"],$pos+1,strlen($Img_peq1["name"])+1-$pos);}
		//IMAGEM NUMERO 1
		if($tem_foto1 && $Apagar_img1){$ficheiro1=$id."_1.".$img_ext;unlink($img_path.$ficheiro1);if(!$altera_img)$img_ext='';}
		if($altera_img)copia_segura($Img_peq1["temp"],$img_path.$id."_1.".$img_ext);

		$nome1=str2SQLstr($nome);
        $url1=str2SQLstr($url);
        $largura1=str2SQLstr($largura);
        $altura1=str2SQLstr($altura);
        $tipo1=str2SQLstr($tipo);
        $pagina1=str2SQLstr($pagina);
        $anunciante1=str2SQLstr($anunciante);
        $data11=date2SQLstr($data1);
        $data21=date2SQLstr($data2);
        $n_impressoes1=str2SQLstr($n_impressoes);
        $n_cliques1=str2SQLstr($n_cliques);
    	$visible1=str2SQLstr($visible);
    	$permanente1=str2SQLstr($permanente);
    	$peso1=str2SQLstr($peso);
		$script=str2SQLstr($script);
		$LINGUA = $_POST['LINGUA'];
		$sql="Update media Set LINGUA='$LINGUA',Name=$nome1,URL=$url1,Visible=$visible1,img_ext='$img_ext',Largura=$largura1,Altura=$altura1,Tipo=$tipo1,Pagina=$pagina1,ID_Anunciante=$anunciante1,Peso=$peso1,Data1=$data11,Data2=$data21,N_impressoes=$n_impressoes1,N_cliques=$n_cliques1,Script=$script,Permanente=$permanente1,Descritivo='$Descritivo',TemaID='$tema' where ID=$id";
		$res=Runquery($db_link,$DB_name,$sql);
		if(!$res)echo "ERRO: ".db_error($db_link)." -- > ".$sql;

		// Runquery($db_link,$DB_name,'DROP TABLE IF EXISTS media_m');//limpar a tabela de ram

		db_closedb_conection($db_link);
		return($res);
	}
}
?>