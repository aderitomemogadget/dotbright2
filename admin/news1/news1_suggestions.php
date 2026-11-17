<?
    include "settings.inc.php";
    $page_titulo="Produtos Alternativos";
    if($novo==1) $page_titulo="Seleccione o novo Produto a Sugerir";
?>
<html>
<head>
	<title>ADMIN</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" href="<?=$site_http_url?>admin/admin_estilos.css"/>
    <style>body{background:#ffffff}</style>
</head>
<body>
<table width="100%" border=0 cellspacing=0 cellpadding=0 class="p1">
<tr>
	<td align="center"><h2 style="color:#FFF"><?=$page_titulo;?></h2></td>
</tr>
</table>
<?
if($novo==1)
{?>
    <table class="border1" width="100%" heigh="100%" border=0 cellspacing=0 cellpadding=0>
    <tr><td>
        <table border=0 cellspacing=0 cellpadding=0 align="center" width=100%><tr><td></td></tr><tr><td><table width="100%" border=0 cellpadding=0 cellspacing="1">
            <? show_links();?>
        </table>
   </td></tr></table><br><br>
<table width="100%" border=0><tr><td align="center"><input class="btn btn-primary" type="reset" value=" Cancelar " name="btn btn-primary" onClick="javascript:history.go(-1);"></td></tr></table>
</td></tr></table>
<? }
else
{
    if($add==1)
    {
        global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
    	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    	$rs=new DataObj;
    	$sql="select * from produtos_alternativos where ID_Prod=$id_produto and ID_Prod2=$ID_Prod2";
    	$hRes=$rs->db_open($db_link,$DB_name,$sql);
    	if($rs->EOF())
    	{
    	    $sql="insert into produtos_alternativos (ID_Prod, ID_Prod2) values ($id_produto,$ID_Prod2)";
	        $res=Runquery($db_link,$DB_name,$sql);
    	}
    	$rs->db_close();
    }
    if($apagar==1)
    {
        global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
    	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    	$rs=new DataObj;
    	$sql="delete from produtos_alternativos where ID_Prod=$id_produto and ID_Prod2=$ID_Prod2";
        $res=Runquery($db_link,$DB_name,$sql);
    	$rs->db_close();
    }?>
	<table class="listagem" width="100%" border=0 cellspacing=0 cellpadding="2">
    <tr>
    	<td bgcolor="#E0E0E0"><a class="italico" href="news1_suggestions.php?id_produto=<?=$id_produto?>&novo=1"><img alt="" src="../images/page.png" width="25" height="25" border=0 align="absmiddle">Associar produto</a></td>
    </tr>
    </table><?
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql="select * from produtos_alternativos inner join news1 on (news1.ID=produtos_alternativos.ID_Prod) where ID_Prod=$id_produto";
	$hRes=$rs->db_open($db_link,$DB_name,$sql);

$count=0;
while(!$rs->EOF())
{
	$codigo=$rs->db_data["ID_Prod2"];
    $rs2=new DataObj;
	$sql="select * from news1 inner join news1_lang on (news1.ID=news1_lang.ID_PAI) where ID_PAI=$codigo and Lang='$admin_lang'";
	$hRes=$rs2->db_open($db_link,$DB_name,$sql);
	$cabecalho=($rs2->db_data["Cabecalho"]);
	$rs2->db_close();

	if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
	else				{$cor="#FFFFFF";$class="r2";$n="2";}
	?>
    <table height="50px" border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
	<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
        <td width="25"><img alt="" src="../images/page.png" border=0 align="absmiddle"></td>
        <td class="preto"><? echo "$ordem";?>&nbsp;<? if($cabecalho) echo htmlentities_b($cabecalho); else echo "[Não tem nome atribuido]"?></td>
        <td width="100" align="right"><a class="italico" href="news1_suggestions.php?id_produto=<?=$id_produto?>&ID_Prod2=<?=$codigo?>&apagar=1"><img alt="" src="del.png" border=0 align="absmiddle"></a></td>
        <td width="10">&nbsp;</td>
    </tr>
	</table><?
	$rs->db_next();
	$count++;
}
$rs->db_close();
?>
<table width="100%" border=0><tr><td align="center"><input class="btn btn-primary" type="reset" value="Fechar" name="btn btn-primary" onClick="window.close();"></td></tr></table>
<? }?>

</body>
</html>
<?
function show_links($pai=0,$contador=-5)
{
        $site_http_url="../../includes/";
       global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$id_produto,$id_select;
    	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
        $contador+=5;
    	$rs=new DataObj;
        $sql="Select *,news1_families.ID from news1_families inner join news1_families_lang on news1_families_lang.ID_PAI=news1_families.ID where ID_ORG=$pai and Lang='$admin_lang' order by Ordem Asc";
    	$hRes=$rs->db_open($db_link,$DB_name,$sql);
    	$i=0;
        while(!$rs->EOF())
    	{
			 $IDa=$rs->db_data["ID"];
    	    $IDb=$rs->db_data["ID_PAI"];
        	$nomeb=$rs->db_data["Cabecalho"];
            $i=0;
            echo '<tr align="left"><td class=preto bgcolor=E0E0E0><b>';
                while($i<$contador){echo "&nbsp;";$i++;}
						  if($IDa==$id_select){$vc="[-]";$link='news1_suggestions.php?id_produto='.$id_produto.'&novo=1';}
				  else {$vc="[+]";$link='news1_suggestions.php?id_produto='.$id_produto.'&novo=1&id_select='.$IDa;}
                echo '&nbsp;<a class="preto" href="'.$link.'">'.$vc.'&nbsp;'.htmlentities_b($nomeb)."</a>";
            echo '</b></td></tr>';
            if($IDa==$id_select) show_sub_links($IDb,$contador);
            show_links($IDb,$contador);
            $rs->db_next();
            $i++;
    	}
    	$rs->db_close();
}

function show_sub_links($pai=0,$contador=-5)
{
        $site_http_url="../../includes/";
        global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$id_produto,$admin_lang;
    	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
        $contador+=5;
    	$rs=new DataObj;
        $sql="Select * from news1 inner join news1_lang on news1_lang.ID_PAI=news1.ID where ID_ORG=$pai and Lang='$admin_lang' order by Ordem Asc";
    	$hRes=$rs->db_open($db_link,$DB_name,$sql);
    	$i=0;
        while(!$rs->EOF())
    	{
    	    $IDb=$rs->db_data["ID_PAI"];
        	$nomeb=$rs->db_data["Cabecalho"];
            $i=0;
            echo '<tr align="left"><td class=preto>';
                while($i<$contador){echo "&nbsp;";$i++;}
                echo '<img alt="" src="../images/arrow.png" align="absmiddle">&nbsp;<a class="preto" href="news1_suggestions.php?id_produto='.$id_produto.'&ID_Prod2='.$rs->db_data["ID_PAI"].'&add=1")\'>'.htmlentities_b($nomeb).'</a>';
            echo '</td></tr>';
            $rs->db_next();
            $i++;
    	}
    	$rs->db_close();
}
?>