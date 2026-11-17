<?php
    include "settings.inc.php";
    include "../layout/header.inc.php";

     if(isset($_GET['add']) && $_GET['add']==1)
    {
        global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$site_http_url;
        $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
        $rs=new DataObj;
        $prod=$_GET['ID_Prod2'];
        $sql="SELECT * FROM products_extraloja WHERE ID_Prod=".$prod;
        $hRes=$rs->db_open($db_link,$DB_name,$sql);
        if($rs->EOF())$res=Runquery($db_link,$DB_name,"INSERT INTO products_extraloja VALUES ('".$prod."')");
        $rs->db_close();
        ?>
        <script>window.opener.location.href='<?=$site_http_url?>admin/products/list_extra.php?id_produto=<?=$prod?>&added=1';window.close();</script>
        <?php
    }
?>
<table class="border1" width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
    <td>
        <table border="0" cellspacing="0" cellpadding="0" align="center" width="100%">
        <tr><td></td></tr>
        <tr>
            <td>
                <table width="100%" border="0" cellpadding="0" cellspacing="1">
                    <? show_links();?>
                </table>
            </td>
        </tr>
        </table>
        <br><br>
    </td>
</tr>
</table>
<?php

function show_links($pai=0,$contador=-5)
{
	$site_http_url="../../includes/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$id_produto,$id_select;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$contador+=5;
	$rs=new DataObj;
	$sql="SELECT *,products_families.ID from products_families inner join products_families_lang on products_families_lang.ID_PAI=products_families.ID where ID_ORG=$pai and Lang='$admin_lang' order by Ordem Asc";
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
		if($IDa==$id_select){$vc="[-]";$link='add_product_extra.php?id_produto='.$id_produto.'&novo=1';}
		else {$vc="[+]";$link='add_product_extra.php?id_produto='.$id_produto.'&novo=1&id_select='.$IDa;}
		echo '&nbsp;<a class="preto" href="'.$link.'">'.$vc.'&nbsp;'.htmlentities_b($nomeb)."</a>";
		echo '</b></td></tr>';
		if($IDa==$id_select) show_sub_links($IDb,$contador);
		show_links($IDb,$contador);
		$i++;
		$rs->db_next();
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
	$sql="Select * from products inner join products_lang on products_lang.ID_PAI=products.ID INNER JOIN products_families_obj as PFO ON products.ID=PFO.ID_PROD where PFO.ID_ORG=$pai and Lang='$admin_lang' order by products_lang.Cabecalho Asc";
	$hRes=$rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	while(!$rs->EOF())
	{
		$IDb=$rs->db_data["ID_PAI"];
		$nomeb=$rs->db_data["Cabecalho"];
		$i=0;
		echo '<tr align="left"><td class="preto" bgcolor="E0E0E0">';
		while($i<$contador){echo "&nbsp;";$i++;}
		echo '<img src="../images/arrow.png" align="absmiddle">&nbsp;<a class="preto" href="add_product_extra.php?id_produto='.$id_produto.'&ID_Prod2='.$rs->db_data["ID_PAI"].'&add=1">'.htmlentities_b($nomeb).'</a>';
		echo '</td></tr>';
		$rs->db_next();
		$i++;
	}
	$rs->db_close();
}

?>