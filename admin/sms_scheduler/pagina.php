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
if ($ID != 0) {
    $sql = "SELECT ID, Titulo, Texto, DeviceId, Data_Envio, Ativo FROM sms_scheduler WHERE ID=$ID";
    $rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
    if (!$rs->EOF()) {
        $IDb        = $rs->db_data["ID"];
        $Titulo     = stripslashes($rs->db_data["Titulo"]);
        $Texto      = stripslashes($rs->db_data["Texto"]);
        $DeviceId   = stripslashes($rs->db_data["DeviceId"]);
        $Data_Envio = $rs->db_data["Data_Envio"];
        $Ativo      = $rs->db_data["Ativo"];
    }
    $rs->db_close();
}
include "../layout/header.inc.php";
include "../layout/body.inc.php";
?>
    <div class="center">
        <form name="form" method="post" action="pagina_trata.php?prev=0&ID=<?= $ID ?>&ID_ORG=<?= $ID_ORG ?>&task_form=<?= $task_form ?>&dump=<?= uniqID("") ?>">
            <input type="hidden" name="slashes" value="'">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(smsSchedulerCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
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
                                                    <div class="checkbox">
                                                        <label for="Ativo">
                                                            <input id="Ativo" type="checkbox" <?= ($Ativo) ? ' checked="checked" ' : '' ?> name="Ativo" value="1">
                                                            Ativo
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="Data_Envio">Data de Envio</label>
                                                    <input id="Data_Envio" name="Data_Envio" class="form-control datepicker" maxlength="255" value="<?= $Data_Envio; ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="Titulo">Título</label>
                                                    <input id="Titulo" name="Titulo" class="form-control" maxlength="255" value="<?= $Titulo; ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="Texto">Texto SMS</label>
                                                    <textarea id="Texto" name="Texto" class="form-control" maxlength="150"><?= $Texto; ?></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label for="DeviceId">Device ID (Cartão)</label>
                                                    <input id="DeviceId" name="DeviceId" class="form-control" maxlength="255" value="<?= $DeviceId; ?>">
                                                </div>
                                                <table width="100%" class="p1">
                                                    <tr>
                                                        <td class="t1" align="center" valign="middle">Utilizadores para enviar</td>
                                                    </tr>
                                                </table>
                                                <?php
                                                $sql = "SELECT c.ID, c.Nome, c.Apelido, c.Telefone, c.Email, scu.ID as scuId, scu.Telemovel FROM clientes AS c LEFT JOIN sms_scheduler_users AS scu ON scu.ID_Cliente=c.ID AND scu.ID_ORG='$ID' WHERE c.Estado=1";
                                                $rs2->db_open($db_link, $GLOBALS['DB_name'], $sql);
                                                if (!$rs2->EOF()) {
                                                    ?>
                                                    <table class="table table-bordered">
                                                        <thead>
                                                        <tr>
                                                            <th align="left"></th>
                                                            <th align="left">Nome</th>
                                                            <th align="left">Email</th>
                                                            <th align="left">Telemóvel</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php
                                                        while (!$rs2->EOF()) {
                                                            $IDb          = $rs2->db_data["ID"];
                                                            $Nome         = $rs2->db_data["Nome"];
                                                            $Apelido      = $rs2->db_data["Apelido"];
                                                            $Email        = $rs2->db_data["Email"];
                                                            $scuId        = $rs2->db_data["scuId"];
                                                            $Telemovel    = $rs2->db_data["Telefone"];
                                                            $scuTelemovel = $rs2->db_data["Telemovel"];

                                                            if (!$scuTelemovel) {
                                                                $scuTelemovel = $Telemovel;
                                                            }

                                                            $checked = '';
                                                            if ($ID == 0 || $scuId > 0) {
                                                                $checked = ' checked=checked ';
                                                            }

                                                            ?>
                                                            <tr>
                                                                <td class="preto" align="left" valign="top" width="20">
                                                                    <input id="f_send_<?= $IDb ?>" type="checkbox" name="f_send[]" value="<?= $IDb ?>" <?= $checked ?>>
                                                                </td>
                                                                <td align="left" valign="top">
                                                                    <label for="f_send_<?= $IDb ?>"><?= $Nome . ' ' . $Apelido; ?></label>
                                                                </td>
                                                                <td align="left" valign="top">
                                                                    <label for="f_send_<?= $IDb ?>"><?= $Email; ?></label>
                                                                </td>
                                                                <td align="left" valign="top">
                                                                    <label for="f_send_<?= $IDb ?>"><?= $scuTelemovel; ?></label>
                                                                </td>
                                                            </tr>
                                                            <?php

                                                            $rs2->db_next();
                                                        }
                                                        ?>
                                                        </tbody>
                                                    </table>
                                                    <?php
                                                }
                                                $rs2->db_close();
                                                ?>
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
