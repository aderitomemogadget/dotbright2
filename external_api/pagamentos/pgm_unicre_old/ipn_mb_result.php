<?php

    //**************************************************
    //IPN das referencias Multibanco
    //**************************************************

    //IPN do MBWAY e das referencias mb
    $fp = fopen('php://input', 'r');
    $http_body = stream_get_contents($fp);
    fclose($fp);

    $iv_from_http_header = $_SERVER['HTTP_X_INITIALIZATION_VECTOR'];
    $auth_tag_from_http_header = $_SERVER['HTTP_X_AUTHENTICATION_TAG'];

    file_put_contents('logs/sibs_mb0_'.date('Y_m_d_h_i_s').'.txt',$iv_from_http_header.'&TAG='.$auth_tag_from_http_header.'&BODY='.$http_body);
    $result = file_get_contents('http://www.memogadget.com/sibs/webhookdecode.php?IV='.$iv_from_http_header.'&TAG='.$auth_tag_from_http_header.'&BODY='.$http_body);
    file_put_contents('logs/sibs_mb_'.date('Y_m_d_h_i_s').'.txt', $result);

    $json_decode=json_decode($result,true);
    $type=          $json_decode['type'];
    $id=            $json_decode['payload']['referencedId'];
    $paymentBrand=  $json_decode['payload']['paymentBrand'];
    $amount=        $json_decode['payload']['amount'];
    $code=          $json_decode['payload']['result']['code'];

    if($type=='PAYMENT' && $paymentBrand=='SIBS_MULTIBANCO')
    {
        $includes_path = "/home/app/public_html/admin/core/";
        include $includes_path."includes.inc.php";
        $db_link = db_opendb_conection(SERVER,USERNAME,DB_PASSWORD);


        $sql = "select * from pagamentos_unicre WHERE Resposta1 like '%\"$id\"%'";
        $rs = new DataObj;
        $rs->db_open($db_link,DBNAME, $sql);
        if (!$rs->EOF())
        {
            $IDb = $rs->db_data['ID'];

            if($code == '000.000.000'
                || $code == '000.100.112'
                || $code == '000.000.000'
                || $code == '000.000.100'
                || $code == '000.100.105'
                || $code == '000.100.106'
                || $code == '000.100.110'
                || $code == '000.100.111'
                || $code == '000.100.112'
                || $code == '000.300.000'
                || $code == '000.300.100'
                || $code == '000.300.101'
                || $code == '000.300.102'
                || $code == '000.300.103'
                || $code == '000.310.100'
                || $code == '000.310.101'
                || $code == '000.310.110'
                || $code == '000.600.000'
                || $code == '000.400.000'
                || $code == '000.400.010'
                || $code == '000.400.020'
                || $code == '000.400.040'
                || $code == '000.400.050'
                || $code == '000.400.060'
                || $code == '000.400.070'
                || $code == '000.400.080'
                || $code == '000.400.081'
                || $code == '000.400.082'
                || $code == '000.400.090'
                || $code == '000.400.100'
                || $code == '000.400.110'
            )
            {
                $sql = 'UPDATE pagamentos_unicre set Estado = 13, UDate="'.date("Y-m-d H:m:i").'" WHERE ID = '.$IDb;
                Runquery($db_link,DBNAME, $sql);
                $sql = 'UPDATE sibsreferences set Estado = 13, Webhook = "'.addslashes(serialize($http_body)).'" WHERE id_order = "Enc'.$IDb.'"';
                Runquery($db_link,DBNAME, $sql);
            }
            else
            {
                $sql = 'UPDATE pagamentos_unicre set Estado = 4, UDate="'.date("Y-m-d H:m:i").'" WHERE ID = '.$IDb;
                Runquery($db_link,DBNAME, $sql);
                $sql = 'UPDATE sibsreferences set Estado = 4, Webhook = "'.addslashes(serialize($http_body)).'" WHERE id_order = "Enc'.$IDb.'"';
                Runquery($db_link,DBNAME, $sql);
            }
        }
        $rs->db_close();
    }

?>