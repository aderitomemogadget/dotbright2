<?
    include "settings.inc.php";
    include "../layout/header.inc.php"; 
    include "../layout/body.inc.php";
   
    if(isset($_GET['apagar']) && $_GET['apagar']==1)
    {
        global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
        $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
        $rs=new DataObj;
        $prod=$_GET['id_produto'];
        $res=Runquery($db_link,$DB_name,"DELETE from products_extraloja where ID_Prod=".$prod);
        $rs->db_close();
    }
?>
<table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
    <td valign="top" align="left">
        <? include "../layout/menus.inc.php";?>
        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="p1">
        <tr>
            <td align="center"><h2 style="color:#FFF">Produtos Extra Loja</h2></td>
        </tr>
        </table>
        <table class="listagem" width="100%" border=0 cellspacing=0 cellpadding="2">
        <tr>
            <td bgcolor="#E0E0E0">
                <?php
                    global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
                    
                    $db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
                    $rs = new DataObj;

                    $result=array();
                    $count=0;
                    $sql = 'SELECT 
                                P.ID
                            FROM 
                                products_extraloja AS PEL
                                INNER JOIN products AS P
                                ON PEL.ID_Prod = P.ID
                                INNER JOIN brands AS BR 
                                ON P.ID_Marca = BR.ID
                            WHERE 
                                P.Aproved=1
                                AND BR.Visivel=1';
                            //echo $sql;
                    $rs->db_open($db_link,$GLOBALS['DB_name'], $sql);   
                    while(!$rs->EOF()){
                        $prod_id = $rs->db_data['ID'];
                        $prods = get_produtc_info($prod_id);

                        $cabecalho=$prods['prod_nome'];
                        $idprod=$prods['prod_id'];

                        if($count % 2 != 0) {$cor="#def2fa";$class="r1";$n="1";}
                        else                {$cor="#F6F6F6";$class="r2";$n="2";}
                        ?>
                        <table height="50px" border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
                            <tr id="<?=$idprod?>" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
                                <td width="25"><a class="preto" href="products.php?ID=<?=$idprod?>&ID_ORG=0&Nivel=0"><img src="../images/page.png" border=0 align="absmiddle"></a></td>
                                <td class="preto"><a class="preto" href="products.php?ID=<?=$idprod?>&ID_ORG=0&Nivel=0"><? echo "$ordem";?>&nbsp;<? if($cabecalho) echo htmlentities_b($cabecalho); else echo "[Não tem nome atribuido]"?></a></td>
                                <td width="100" align="right"><a class="italico" href="list_extra.php?id_produto=<?=$idprod?>&apagar=1"><img src="del.png" border=0 align="absmiddle"></a></td>
                                <td width="10">&nbsp;</td>
                            </tr>
                        </table>
                        <?
                        $count++;
                        $rs->db_next();
                    }
                    $rs->db_close();

                    if(isset($_GET['added']) && $_GET['added']==1){
                    ?>
                        <script>document.getElementById(<?=$_GET['id_produto']?>).style.background="#0F0";</script>
                    <?php } ?>
            </td>
        </tr>
        </table>
    </td>
    <td width="10px">&nbsp;</td>
    <td valign="top"><? show_fast_tools_extra();?></td>
</tr>
</table>
<?
include "../layout/bottom.inc.php";
?>