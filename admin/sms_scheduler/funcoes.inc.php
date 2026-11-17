<?php
function show_page_buttons()
{
    global $task_button;
    ?>
    <table width="100%" border=0 cellspacing=0 cellpadding=0>
    <tr>
        <td height="5px"></td>
    </tr>
    </table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
    <tr>
        <td align="center" valign="middle" class="t1">
            <input class="btn btn-primary" type="submit" name="Submit" value=" <?= $task_button; ?> ">
            <input class="btn btn-secondary" type="button" name="cancelar" value=" Cancelar " onMouseUp="history.go(-1);">
        </td>
    </tr>
    </table>
    <img src="../images/bsh.png" width="100%" height="30px">
    <?php
}

function show_page_tools($filter = 0)
{
    ?>
    <div class="panel panel-inverse overflow-hidden m-b-12">
        <div class="panel-heading">
            <h4 style="color:#fff">SMS</h4>
        </div>
    </div>
    <table width="100%" border=0 cellspacing=0 cellpadding=0 class="p1">
        <tr>
            <td width=29 align="center">
                <input title="" onClick='makeSelection("filter","lista_paginas")' type="checkbox" name="filter" id="filter" value="1" class="cb">
            </td>
            <td width="10px" align="center"></td>
            <td width="91px" class="t1">Pesquisar:</td>
            <td class=pesq>
                <input title="" type="text" name="m_pesquisa" value="<?= $_POST["m_pesquisa"] ?>" class="form-control"  >
            </td>
            <td width="28px">
                <input type="submit" value="submeter pesquisa" class="btn btn-primary" >
            </td>
            <td>&nbsp;</td>
        </tr>
    </table>
    <?php
}

function show_page_bottom_tools()
{
    ?>
    <table width="100%" border=0 cellspacing=0 cellpadding=0>
        <tr>
            <td height="5px"></td>
        </tr>
    </table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
        <tr>
            <td width="130px" class="t1">&nbsp;Com os seleccionados:</td>
            <td width="125px"><select title="" name="flag" id="flag" class="select1">
                    <option value="" selected="selected"></option>
                    <option value="1">Apagar</option>
                </select>
            </td>
            <td>&nbsp;<input type="submit" name="submeter" value="Aplicar Ação" class="btn btn-primary" style="width:120px">
            </td>
        </tr>
    </table>
    <img src="../images/bsh.png" width="100%" height="30px">
    <?php
}

function show_fast_tools()
{
    ?>
    <table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
        <tr>
            <td width="5px" align="left"></td>
            <td class="titulo2" align="center">FAST TOOLS</td>
            <td width="5px" align="right"></td>
        </tr>
        <tr>
            <td width="5px" align="left"></td>
            <td class=preto align="center">
                <input class="btn btn-primary" type="button" style="width:160px" value="Agendar novo envio" onclick="window.open('pagina.php?ID=0&ID_ORG=0&Dump=<?= uniqid(""); ?>','_self')">
            </td>
            <td width="5px" align="right"></td>
        </tr>
    </table>
    <img src="../images/bsh.png" width="100%" height="30px">
    <?php
}

function show_content_list()
{
    global $DB_host, $DB_user, $DB_pass, $DB_name;
    $db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
    $rs      = new DataObj;
    $sql     = "SELECT * FROM sms_scheduler";
    $rs->db_open($db_link, $DB_name, $sql);
    $count = 0;
    if (!$rs->EOF()) {
        while (!$rs->EOF()) {
            $count++;
            $IDb        = $rs->db_data["ID"];
            $Titulo     = stripslashes($rs->db_data["Titulo"]);
            $Data_Envio = $rs->db_data["Data_Envio"];
            $Ativo      = $rs->db_data["Ativo"];

            if ($count % 2 != 0) {
                $cor   = "#def2fa";
                $class = "r1";
                $n     = "1";
            } else {
                $cor   = "#F6F6F6";
                $class = "r2";
                $n     = "2";
            }
            ?>
            <table border=0 cellspacing=0 cellpadding=0 class="<?= $class ?>">
                <tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?= $cor; ?>'">
                    <td width="29" align="center">
                        <input title="" type="checkbox" name="chk_<?= $IDb ?>" id="chk_<?= $IDb ?>" value="<?= $IDb ?>" class="cb">
                    </td>
                    <td width="4" align="right"><img width="2" src="../images/s6_<?= $n ?>.jpg"></td>
                    <td>
                        <a title="editar página" class="lista_links" href="pagina.php?ID=<?= $IDb; ?>&Dump=<?= uniqid(""); ?>">
                            <?php
                            if ($Titulo) {
                                echo htmlentities_b(stripslashes($Titulo));
                            } else {
                                echo "[Editar]";
                            }
                            ?>
                        </a>
                    </td>
                    <td valign="middle" width="130">
                        <a title="editar envio" class="lista_links" href="pagina.php?ID=<?= $IDb; ?>&Dump=<?= uniqid(""); ?>">
                            <?php
                            if ($Data_Envio) {
                                echo htmlentities_b(stripslashes($Data_Envio));
                            } else {
                                echo "[Editar]";
                            }
                            ?>
                        </a>
                    </td>
                    <td valign="middle" width="35">
                        <?php
                        if ($Ativo) {
                            echo '<img title="editar envio" src="ok.png" border=0 align="absmiddle">';
                        } else {
                            echo '<img title="editar envio" src="hidden.png" border=0 align="absmiddle">';
                        }
                        ?>
                    </td>
                    <td width="100" align="right" class="preto">
                        <a class="italico" href="pagina.php?ID=<?= $IDb ?>&task=apagar&Dump=<?= uniqid(""); ?>"><img title="apagar envio" src="del.png" border=0></a>
                    </td>
                </tr>
            </table>
            <?php
            $rs->db_next();
        }
    } else {
        if ($count % 2 != 0) {
            $cor   = "#def2fa";
            $class = "r1";
        } else {
            $cor   = "#F6F6F6";
            $class = "r2";
        }
        ?>
        <table border=0 cellspacing=0 cellpadding=0 class="<?= $class ?>">
            <tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?= $cor; ?>'">
                <td valign="middle" align="center">Sem Resultados encontrados</td>
            </tr>
        </table>
        <?php
    }

    $rs->db_close();
}

function show_content_from_search($reg)
{
    if (!$reg) {
        return false;
    }
    global $DB_host, $DB_user, $DB_pass, $DB_name;
    $db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
    $rs      = new DataObj;
    $sql     = "Select * from sms_scheduler WHERE ID IN($reg)";
    $rs->db_open($db_link, $DB_name, $sql);
    $count = 0;
    if (!$rs->EOF()) {
        while (!$rs->EOF()) {
            $count++;
            $IDb        = $rs->db_data["ID"];
            $Titulo     = stripslashes($rs->db_data["Titulo"]);
            $Data_Envio = $rs->db_data["Data_Envio"];
            $Ativo      = $rs->db_data["Ativo"];

            if ($count % 2 != 0) {
                $cor   = "#def2fa";
                $class = "r1";
                $n     = "1";
            } else {
                $cor   = "#F6F6F6";
                $class = "r2";
                $n     = "2";
            }
            ?>
            <table border=0 cellspacing=0 cellpadding=0 class="<?= $class ?>">
                <tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?= $cor; ?>'">
                    <td width="29" align="center">
                        <input title="" type="checkbox" name="chk_<?= $IDb ?>" id="chk_<?= $IDb ?>" value="<?= $IDb ?>" class="cb">
                    </td>
                    <td width="4" align="right"><img width="2" src="../images/s6_<?= $n ?>.jpg"></td>
                    <td valign="middle" width="35">
                        <?php
                        if ($Ativo) {
                            echo '<img title="editar envio" src="ok.png" border=0 align="absmiddle">';
                        } else {
                            echo '<img title="editar envio" src="hidden.png" border=0 align="absmiddle">';
                        }
                        ?>
                    </td>
                    <td valign="middle" width="70">
                        <a title="editar página" class="lista_links" href="pagina.php?ID=<?= $IDb; ?>&Dump=<?= uniqid(""); ?>">
                            <?php
                            if ($Titulo) {
                                echo htmlentities_b(stripslashes($Titulo));
                            } else {
                                echo "[Editar]";
                            }
                            ?>
                        </a>
                    </td>
                    <td valign="middle" width="130">
                        <a title="editar envio" class="lista_links" href="pagina.php?ID=<?= $IDb; ?>&Dump=<?= uniqid(""); ?>">
                            <?php
                            if ($Data_Envio) {
                                echo htmlentities_b(stripslashes($Data_Envio));
                            } else {
                                echo "[Editar]";
                            }
                            ?>
                        </a>
                    </td>
                    <td width="100" align="right" class=preto>
                        <a class="italico" href="pagina.php?ID=<?= $IDb ?>&task=apagar&Dump=<?= uniqid(""); ?>"><img title="apagar envio" src="del.png" border=0></a>
                    </td>
                </tr>
            </table>
            <?php
            $rs->db_next();
        }
    } else {
        if ($count % 2 != 0) {
            $cor   = "#def2fa";
            $class = "r1";
        } else {
            $cor   = "#F6F6F6";
            $class = "r2";
        }
        ?>
        <table border=0 cellspacing=0 cellpadding=0 class="<?= $class ?>">
            <tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?= $cor; ?>'">
                <td valign="middle" align="center">Sem Resultados encontrados</td>
            </tr>
        </table>
        <?php
    }
    $rs->db_close();

    return '';
}

function InsertNewRecord()
{
    global $DB_host, $DB_user, $DB_pass, $DB_name;
    $db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);

    $res = 0;

    if (isset($_POST['slashes']) && $_POST['slashes'] == "'") {
        $Titulo     = addslashes($_POST["Titulo"]);
        $Texto      = addslashes($_POST["Texto"]);
        $Data_Envio = addslashes($_POST["Data_Envio"]);
        $DeviceId   = addslashes($_POST["DeviceId"]);
        $Ativo      = intval($_POST["Ativo"]);

        $sql = "insert into sms_scheduler (Titulo, Texto, DeviceId, Data_Envio, Ativo) values ('$Titulo', '$Texto', '$DeviceId', '$Data_Envio', '$Ativo')";
        $res = Runquery($db_link, $DB_name, $sql);
        if (!$res) {
            echo db_error($db_link);
        } else {
            $new_id = db_get_last_insert_id($db_link);

            updateSmsUsers($new_id);
        }
        db_closedb_conection($db_link);
    }

    return ($res);
}

function DeleteRecord()
{
    global $DB_host, $DB_user, $DB_pass, $DB_name;
    $db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
    $id      = isset($_GET['ID']) ? (int) $_GET['ID'] : 0;
    $res     = Runquery($db_link, $DB_name, "delete from sms_scheduler_users where ID_ORG=$id");
    if (!$res) {
        echo db_error($db_link);
    }
    $res = Runquery($db_link, $DB_name, "delete from sms_scheduler where ID=$id");
    if (!$res) {
        echo db_error($db_link);
    }
    db_closedb_conection($db_link);

    return ($res);
}

function ChangeRecord()
{
    global $DB_host, $DB_user, $DB_pass, $DB_name;
    $db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);

    $rs = new DataObj;

    $id     = isset($_GET['ID']) ? (int) $_GET['ID'] : 0;
    $altera = 0;
    $rs->db_open($db_link, $DB_name, "Select ID from sms_scheduler WHERE ID=$id");
    if (!$rs->EOF()) {
        $altera = 1;
    }
    $rs->db_close();
    if (!$altera) {
        return InsertNewRecord();
    } else {
        if (isset($_POST['slashes']) && $_POST['slashes'] == "'") {
            $Titulo     = addslashes($_POST["Titulo"]);
            $Texto      = addslashes($_POST["Texto"]);
            $DeviceId   = addslashes($_POST["DeviceId"]);
            $Data_Envio = addslashes($_POST["Data_Envio"]);
            $Ativo      = intval($_POST["Ativo"]);

            $sql = "UPDATE sms_scheduler SET Titulo='$Titulo', Texto='$Texto', DeviceId='$DeviceId', Data_Envio='$Data_Envio', Ativo='$Ativo' WHERE ID='$id'";
            $res = Runquery($db_link, $DB_name, $sql);

            updateSmsUsers($id);

            db_closedb_conection($db_link);
        }

        return ($res);
    }
}

function updateSmsUsers($id)
{
    global $DB_host, $DB_user, $DB_pass, $DB_name;
    $db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);

    $rs  = new DataObj();
    $rs2 = new DataObj();

    $cardsList = array();

    $selectedUsers = isset($_POST['f_send']) && is_array($_POST['f_send']) ? $_POST['f_send'] : array();

    foreach ($selectedUsers as $idUser) {
        $idUser = (int) $idUser;
        if ($idUser <= 0) {
            continue;
        }

        $rs->db_open($db_link, $DB_name, "Select ID, Telefone from clientes WHERE ID=$idUser");
        if (!$rs->EOF()) {
            $rs2->db_open($db_link, $DB_name,
                "Select ID from sms_scheduler_users WHERE ID_ORG='$id' AND ID_Cliente='{$rs->db_data['ID']}'");
            if ($rs2->EOF()) {
                $sql = "insert into sms_scheduler_users (ID_ORG, ID_Cliente, Telemovel, Enviado) values ('$id', '{$rs->db_data['ID']}', '{$rs->db_data['Telefone']}', '0')";
                Runquery($db_link, $DB_name, $sql);
            }
            $rs2->db_close();

            $cardsList[] = $rs->db_data['ID'];
        }
        $rs->db_close();
    }

    if (!empty($cardsList)) {
        $cardsList = implode(',', $cardsList);
        Runquery($db_link, $DB_name, "delete from sms_scheduler_users where ID_ORG=" . (int) $id . " AND ID_Cliente not in ($cardsList)");
    } else {
        Runquery($db_link, $DB_name, "delete from sms_scheduler_users where ID_ORG=" . (int) $id);
    }

    db_closedb_conection($db_link);
}

function SearchRecords($d)
{
    global $DB_host, $DB_user, $DB_pass, $DB_name;
    $db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
    $rs      = new DataObj();
    $d       = addslashes(strtolower(ltrim(rtrim($d))));
    $sql     = "SELECT DISTINCT ID, lower(Titulo) as Titulo,length(lower(Titulo)) AS len, ((length(Titulo)-length(replace(lower(Titulo),'$d','')))/length('$d'))/(length(lower(Titulo))+1-length(replace(lower(Titulo),' ','')) ) AS occwds, ((length(Titulo)-length(replace(lower(Titulo),'$d','')))/length('$d')) AS occ FROM sms_scheduler WHERE (lower(Referencia) LIKE '%$d%') ORDER BY occ DESC, occwds DESC";
    $rs->db_open($db_link, $DB_name, $sql);

    return $rs;
}
