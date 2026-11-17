<?php
$path = dirname(__FILE__) . '/../../../core/';
require_once $path . 'core_files.php';

$ifthen = new IFTHENCallback();

if ( ! is_null(filter_input(INPUT_GET, 'entidade')) && ! is_null(filter_input(INPUT_GET,
        'referencia')) && ! is_null(filter_input(INPUT_GET, 'valor')) && ! is_null(filter_input(INPUT_GET, 'chave'))) {
    $key        = filter_input(INPUT_GET, 'chave');
    $entidade   = filter_input(INPUT_GET, 'entidade');
    $referencia = filter_input(INPUT_GET, 'referencia');

    if ( ! $ifthen->isValidAntiPhishingKey($key)) {
        http_response_code(400);
        exit;
    }

    if ( ! $ifthen->updateOrder($entidade, $referencia)) {
        http_response_code(400);
    }
} else {
    http_response_code(400);
}

class IFTHENCallback
{
    /**
     * Chave anti-phishing acordade com a IFTHEN
     */
    public $key = 'i4rotcini05zgswq2gltgs2t8dyftbpssmzee5o0pfbdnz3jjokcne4v';

    /**
     * IFTHENCallback constructor.
     */
    public function __construct()
    {
        /*$dbConn = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
        $dbo    = new DataObj();
        $sql    = "SELECT chavebo FROM metodos_pagamento WHERE Tipo=4";
        $dbo->db_open($dbConn, $GLOBALS['DB_name'], $sql);
        if ( ! $dbo->EOF()) {
            $this->key = $dbo->db_data["chavebo"];
        }
        $dbo->db_close();*/

    }

    /**
     * Valida se a chave no Backoffice é igual à enviada pela IFTHEN
     *
     * @param string $key Chave Anti-phishing.
     *
     * @return bool
     */
    function isValidAntiPhishingKey($key)
    {
        return ($this->key === $key);
    }

    /**
     * Verifica se existe uma encomenda com a entidade / referência e atualizada o estado.
     *
     * @param string $entidade
     * @param string $referencia
     *
     * @return bool
     */
    function updateOrder($entidade, $referencia)
    {
        $dbConn = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
        $dbo    = new DataObj();
        $res    = false;
        $sql    = "SELECT ID FROM encomendas WHERE Ent_atm='$entidade' AND Ref_atm='$referencia' AND Encomenda_Paga=0 AND Data_Criacao > NOW() - INTERVAL 2 WEEK";
        $dbo->db_open($dbConn, $GLOBALS['DB_name'], $sql);
        if ( ! $dbo->EOF()) {
            $enc_id=$dbo->db_data['ID'];
            $sql = "UPDATE encomendas SET Encomenda_Paga=1, Estado='13' WHERE ID='{$dbo->db_data['ID']}'";
            $res = Runquery($dbConn, $GLOBALS['DB_name'], $sql);
            update_encomendas_paga($enc_id);
            enviarEmailCliente($enc_id,'', 13);
        }
        $dbo->db_close();

        return $res;
    }
}