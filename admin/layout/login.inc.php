<?php
function getDadosAdministradorAdmin($admin_id)
{
    global $DB_host, $DB_user, $DB_pass, $DB_name;
    $db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
    $rs = new Dataobj;
    $rs2 = new Dataobj;
    $sql = 'SELECT Nome,Grupo_ID FROM logins WHERE ID="' . $admin_id . '"';
    $rs->db_open($db_link, $DB_name, $sql);
    if (!$rs->EOF()) {
        $dados['Nome'] = $rs->db_data['Nome'];
        $dados['Grupo_ID'] = $rs->db_data['Grupo_ID'];
        $sql = 'SELECT Permissoes, Descricao FROM grupos_logins WHERE ID="' . $rs->db_data['Grupo_ID'] . '"';
        $rs2->db_open($db_link, $DB_name, $sql);
        if (!$rs2->EOF()) {
            $dados['Permissoes'] = decodePermissoesAdmin($rs2->db_data['Permissoes']);
            $dados['Descricao'] = $rs2->db_data['Descricao'];
        }
        $rs2->db_close();
    }
    $rs->db_close();

    // var_dump($dados);
    return $dados;
}

function decodePermissoesAdmin($Permissoes)
{
    $v_permissoes = array();
    if ($Permissoes !== '') {
        $v_modulos = explode('|', $Permissoes);
        foreach ($v_modulos as $key => $value) {
            $v_perms = explode('_', $value);
            $v_permissoes[$v_perms[0]] = $v_perms[1];
        }
    }

    return $v_permissoes;
}
?>