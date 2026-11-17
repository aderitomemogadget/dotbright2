<?php

    //**************************************************
    //IPN do MBWAY e do VISA
    //**************************************************

    if($_SERVER['HTTP_X_INITIALIZATION_VECTOR'] != '')
    {
        $fp = fopen('php://input', 'r');
        $http_body = stream_get_contents($fp);
        fclose($fp);

        $iv_from_http_header = $_SERVER['HTTP_X_INITIALIZATION_VECTOR'];
        $auth_tag_from_http_header = $_SERVER['HTTP_X_AUTHENTICATION_TAG'];

        file_put_contents('logs/sibs_'.date('Y_m_d_h_i_s').'.txt',$iv_from_http_header.'&TAG='.$auth_tag_from_http_header.'&BODY='.$http_body);
        $result = file_get_contents('http://www.memogadget.com/sibs/webhookdecode.php?IV='.$iv_from_http_header.'&TAG='.$auth_tag_from_http_header.'&BODY='.$http_body);    
        file_put_contents('logs/sibs_w_'.date('Y_m_d_h_i_s').'.txt', $result);

        $json_decode=json_decode($result,true);
        $type=          $json_decode['type'];
        $id=            $json_decode['payload']['id'];
        
        $paymentType=   $json_decode['payload']['paymentType'];
        $paymentBrand=  $json_decode['payload']['paymentBrand'];
        $amount=        $json_decode['payload']['amount'];
        $code=          $json_decode['payload']['result']['code'];



        if($type=='PAYMENT' && $paymentType=='DB' && $paymentBrand=='MBWAY')
        {
            file_put_contents('logs/sibs_mbway_'.date('Y_m_d_h_i_s').'.txt', $result);

            $path =  dirname( __FILE__ ) . '/../../../core/';
            include $path.'core_files.php';
            $db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);

            $sql = "select * from pagamentos_unicre WHERE Resposta1 like '%\"$id\"%'";
            $rs = new DataObj;
            $rs->db_open($db_link,$GLOBALS['DB_name'], $sql);
            if (!$rs->EOF())
            {
                $IDb = $rs->db_data['ID'];
                $ID_ENC = $rs->db_data['ID_ENC'];

                if($code == '000.000.000'
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
                    $sql = 'UPDATE pagamentos_unicre set Estado = 13, UDate="'.date("Y-m-d H:m:i").'", Code="'.$code.'" WHERE ID = '.$IDb;
                    Runquery($db_link,$GLOBALS['DB_name'], $sql);
                    $sql = 'UPDATE sibs_mbway_logs set Estado = 13, Webhook = "'.addslashes(serialize($http_body)).'" WHERE ID_Encomenda = "Enc'.$IDb.'"';
                    Runquery($db_link,$GLOBALS['DB_name'], $sql);

                    $sql = "UPDATE encomendas SET Encomenda_Paga=1, Estado='23' WHERE ID='$ID_ENC'";
                    $res = Runquery($db_link, $GLOBALS['DB_name'], $sql);

                    enviarEmailCliente($ID_ENC,'', 13);
                }
                else
                {
                    
                    if($code == '100.396.101')
                    {
                        $sql = 'UPDATE pagamentos_unicre set Estado = 3, UDate="'.date("Y-m-d H:m:i").'", Code="'.$code.'" WHERE ID = '.$IDb;
                        Runquery($db_link,$GLOBALS['DB_name'], $sql);
                        $sql = 'UPDATE sibs_mbway_logs set Estado = 3, Webhook = "'.addslashes(serialize($http_body)).'" WHERE ID_Encomenda = "Enc'.$IDb.'"';
                        Runquery($db_link,$GLOBALS['DB_name'], $sql);
                    }  
                    elseif($code != '800.400.500')
                    {
                        $sql = 'UPDATE pagamentos_unicre set Estado = 4, UDate="'.date("Y-m-d H:m:i").'", Code="'.$code.'" WHERE ID = '.$IDb;
                        Runquery($db_link,$GLOBALS['DB_name'], $sql);
                        $sql = 'UPDATE sibs_mbway_logs set Estado = 4, Webhook = "'.addslashes(serialize($http_body)).'" WHERE ID_Encomenda = "Enc'.$IDb.'"';
                        Runquery($db_link,$GLOBALS['DB_name'], $sql);
                    }                
                }
            }
            $rs->db_close();
        }


        if($type=='PAYMENT' && $paymentType=='DB' && ($paymentBrand=='MASTER' || $paymentBrand=='VISA'))
        {
            file_put_contents('logs/sibs_visa_'.date('Y_m_d_h_i_s').'.txt', $result);

            $id=$json_decode['payload']['ndc'];

            $registrationId=$json_decode['payload']['registrationId'];//para pagamentos por token
            $card_bin=$json_decode['payload']['card']['bin'];
            $card_last4Digits=$json_decode['payload']['card']['last4Digits'];
            $card_holder=$json_decode['payload']['card']['holder'];
            $card_expiryMonth=$json_decode['payload']['card']['expiryMonth'];
            $card_expiryYear=$json_decode['payload']['card']['expiryYear'];


            file_put_contents('logs/sibs_visa_2_'.date('Y_m_d_h_i_s').'.txt', $id);

            $path =  dirname( __FILE__ ) . '/../../../core/';
            include $path.'core_files.php';
            $db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);

            $sql = "select * from pagamentos_unicre WHERE Resposta1 like '%\"$id\"%'";
            file_put_contents('logs/sibs_visa_3_'.date('Y_m_d_h_i_s').'.txt', $sql);
            $rs = new DataObj;
            $rs->db_open($db_link,$GLOBALS['DB_name'], $sql);
            if (!$rs->EOF())
            {
                $IDb = $rs->db_data['ID'];
                $ID_ENC = $rs->db_data['ID_ENC'];

                if($code == '000.000.000'
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
                    $sql = 'UPDATE pagamentos_unicre set Estado = 13, UDate="'.date("Y-m-d H:m:i").'", Code="'.$code.'" WHERE ID = '.$IDb;
                    Runquery($db_link,$GLOBALS['DB_name'], $sql);
                    $sql = 'UPDATE sibs_visa_logs set Estado = 13, Webhook = "'.addslashes(serialize($http_body)).'" WHERE ID_Pagamento = "'.$IDb.'"';
                    Runquery($db_link,$GLOBALS['DB_name'], $sql);

                    $sql = "UPDATE encomendas SET Encomenda_Paga=1, Estado='23' WHERE ID='$ID_ENC'";
                    $res = Runquery($db_link, $GLOBALS['DB_name'], $sql);

                }
                else
                {
                    $sql = 'UPDATE pagamentos_unicre set Estado = 4, UDate="'.date("Y-m-d H:m:i").'", Code="'.$code.'" WHERE ID = '.$IDb;
                    Runquery($db_link,$GLOBALS['DB_name'], $sql);
                    $sql = 'UPDATE sibs_visa_logs set Estado = 4, Webhook = "'.addslashes(serialize($http_body)).'" WHERE ID_Pagamento = "'.$IDb.'"';
                    Runquery($db_link,$GLOBALS['DB_name'], $sql);
                }
            }
            $rs->db_close();
        }

        if($type=='PAYMENT' && $paymentBrand=='SIBS_MULTIBANCO' && $paymentType=='RC')
        {
            file_put_contents('logs/sibs_mb_'.date('Y_m_d_h_i_s').'.txt', $result);

            $path =  dirname( __FILE__ ) . '/../../../core/';
            include $path.'core_files.php';

            $db_link = db_opendb_conection(SERVER,USERNAME,DB_PASSWORD);
            $id=            $json_decode['payload']['referencedId'];



            $sql = "select * from pagamentos_unicre WHERE Resposta1 like '%\"$id\"%'";
            $rs = new DataObj;
            $rs->db_open($db_link,$GLOBALS['DB_name'], $sql);
            if (!$rs->EOF())
            {
                $IDb = $rs->db_data['ID'];
                $ID_ENC = $rs->db_data['ID_ENC'];

                if($code == '000.000.000'
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
                    $sql = 'UPDATE pagamentos_unicre set Estado = 13, UDate="'.date("Y-m-d H:m:i").'", Code="'.$code.'" WHERE ID = '.$IDb;
                    Runquery($db_link,$GLOBALS['DB_name'], $sql);
                    $sql = 'UPDATE sibsreferences set Estado = 13, Webhook = "'.addslashes(serialize($http_body)).'" WHERE id_order = "Enc'.$IDb.'"';
                    Runquery($db_link,$GLOBALS['DB_name'], $sql);

                    $sql = "UPDATE encomendas SET Encomenda_Paga=1, Estado='23' WHERE ID='$ID_ENC'";
                    $res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
                }
                else
                {
                    $sql = 'UPDATE pagamentos_unicre set Estado = 4, UDate="'.date("Y-m-d H:m:i").'", Code="'.$code.'" WHERE ID = '.$IDb;
                    Runquery($db_link,$GLOBALS['DB_name'], $sql);
                    $sql = 'UPDATE sibsreferences set Estado = 4, Webhook = "'.addslashes(serialize($http_body)).'" WHERE id_order = "Enc'.$IDb.'"';
                    Runquery($db_link,$GLOBALS['DB_name'], $sql);
                }
            }
            $rs->db_close();
        }
    }
?>