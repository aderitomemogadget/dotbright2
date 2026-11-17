<?php
include "settings.inc.php";
if ($task == 'apagar') {
    $task_button = "Apagar";
    $task_form   = 'apaga';
} elseif ($ID == 0) {
    $task_button = "Inserir";
    $task_form   = 'insere';
} else {
    $task_button = "Alterar";
    $task_form   = 'altera';
}

$db_link    = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
$rs         = new DataObj();
$rs2        = new DataObj();
$rs3        = new DataObj();
$Titulo     = "";
$Texto      = "";
$DeviceId   = "";
$Data_Envio = date('Y-m-d H:i:s');
$Ativo      = 0;

$ID = $_GET['ID'];

if ($ID != 0) {
    $sql = "SELECT * FROM rgpd_esquecimentos WHERE ID=$ID";
    $rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
    if (!$rs->EOF()) {
        $IDb        = $rs->db_data["ID"];
        $nome = $rs->db_data["nome"];
        $email = $rs->db_data["email"];
        $criado_em = $rs->db_data["criado_em"];
        $confirmado = $rs->db_data["confirmado"];
        $data_confirmacao = $rs->db_data["data_confirmacao"];
        $ID_user = $rs->db_data["ID_user"];
        $mensagem = $rs->db_data["mensagem"];
    }
    $rs->db_close();
}
include "../layout/header.inc.php";
include "../layout/body.inc.php";
?>
    <div class="center">
        <form name="form" method="post" action="pagina_trata.php?prev=0&ID=<?= $ID ?>&ID_ORG=<?= $ID_ORG ?>&task_form=<?= $task_form ?>&dump=<?= uniqID("") ?>">
            <input type="hidden" name="slashes" value="'">
            <table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
                <tr>
                    <td valign="top" align="left">
                        <?php include "../layout/menus.inc.php"; ?>
                        <table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
                            <tr>
                                <td valign="top">
                                    <table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="F6F6F6" class="border1">
                                        <tr>
                                            <td valign="top">
                                                <div class="form-group">
                                                    <label for="Nome">Nome: </label><br>
                                                    <label ><?= $nome?></label>
                                                </div>
                                                <div class="form-group">
                                                    <label for="Email">Email: </label><br>
                                                    <label ><?= $email?></label>
                                                </div>
                                                <div class="form-group">
                                                    <label for="criado">Criado em: </label><br>
                                                    <label ><?= $criado_em?></label>
                                                </div>
                                                <div class="form-group">
                                                    <label for="confirmado">Confirmado em: </label><br>
                                                    <label ><?= $data_confirmacao?></label>
                                                </div>
                                                <div class="form-group">
                                                    <label for="msg">Mensagem: </label><br>
                                                    <label ><?= $mensagem ?></label>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table><?php show_page_buttons(); ?>
                    </td>
                    <td width="10px">&nbsp;</td>
                    <td valign="top" width="220"><?php show_fast_tools(); ?><br/></td>
                </tr>
            </table>
        </form>
    </div>
    <script>
        $(function () {
            $(".datepicker").datetimepicker({
                dateFormat: 'yy-mm-dd',
                timeFormat: 'HH:mm:ss',
                pick12HourFormat: false
            });
        });
    </script>
<?php
include "../layout/bottom.inc.php";
