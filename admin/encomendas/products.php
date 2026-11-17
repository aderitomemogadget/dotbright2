<?php
include "settings.inc.php";
global $DB_host, $DB_user, $DB_pass, $DB_name;
$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
$rs2=new DataObj;
$rs3=new DataObj;

$flag = isset($_POST['flag']) ? intval($_POST['flag']) : 0;

// apagar
/*if ($flag == 1) {
    foreach ($_POST as $varName => $value) {
        $dv = $value;
        $str_id = substr($varName, strpos($varName, "_") + 1);
        if ($str_id > 0 && substr_count($varName, "chk_") > 0) {
            $res = Runquery($db_link, $DB_name, "update encomendas set Estado=-1 WHERE ID=$str_id");
            if (!$res) echo db_error($db_link) . $sql . "<br>";
        }
    };
}*/
//encomendar
if ($flag == 2)
{
    $lista_refs='';
    foreach ($_POST as $varName => $value) {
        $dv = $value;
        $str_id = substr($varName, strpos($varName, "_") + 1);
        if ($str_id > 0 && substr_count($varName, "chk_") > 0)
        {

            $sql="Select * from encomendas_produtos where ID = ".$str_id;
            $rs2->db_open($db_link,$DB_name,$sql);
            if(!$rs2->EOF())
            {
                $ID_Produto=$rs2->db_data['ID_Produto'];

                $sql="Select * from products where ID = ".$ID_Produto;
                $rs3->db_open($db_link,$DB_name,$sql);
                if(!$rs3->EOF())
                {
                    $Ref=$rs3->db_data['Ref'];
                    $lista_refs.=$Ref.'_';
                }
                $rs3->db_close();
            }
            $rs2->db_close();


        }
    };
    echo "<script>window.open('https://www.macorlux.pt/cart/1?r=".$lista_refs."','_new');</script>";
}

include "../layout/header.inc.php";
include "../layout/body.inc.php";
?>
    <table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
        <tr>
            <td valign="top" align="left">
                <?php include "../layout/menus.inc.php"; ?>
                <form action="products.php" method="post" name="lista_enc_prods" onSubmit="return checkFields()">
                    <?php
                    show_page_tools_produtos();
                    show_content_list_produtos();
                    show_page_bottom_tools_produtos();
                    ?>
                </form>
            </td>
        </tr>
    </table>
<?php include "../layout/bottom.inc.php";
