<?php
    //WebHook:
    //S7eNHuj1IhACUB0O4K0QsoX7+v9q4BWO96TtCSBCcjM=
    /*
    02e61f2dbb275e4bdda38256b8708fa2a7.eyJlIjoiMjAxNzM5ODE2NDM0NCIsInJvbGVzIjoiTUFOQUdFUiIsInRva2VuQXBwRGF0YSI6IntcIm1jXCI6XCI1NDAwNzVcIixcInRjXCI6XCIxNDYzMDYzXCJ9IiwiaSI6IjE3MDE3Nzg5NjQzNDQiLCJpcyI6Imh0dHBzOi8vcHJkLnNpdGUxLnNzby5zeXMuc2licy5wdC9hdXRoL3JlYWxtcy9ERVYuU0JPLUlOVC5QT1JUMSIsInR5cCI6IkJlYXJlciIsImlkIjoiUThlWTJITUZiV2ZlMjBmY2VjZDk3MjQ3YTBhMGExZDZjODhkN2EzYTY4In0=.c1470a4187d1fe9cf2e23c8d041648798af35bda2859b7ed88b9407c0dadeb8bbcadc1adce4acb937e8b113b15fab97f9415b485cdba403fe5fc15daf418ec1
    */

    //**************************************************
    //IPN do MBWAY e do VISA
    //**************************************************

die();//mudar para os dados finais do cliente


    file_put_contents('logs/sibs_ipn_'.date('Y_m_d_h_i_s').'.txt',$_SERVER);

    if($_SERVER['HTTP_X_INITIALIZATION_VECTOR'] != '')
    {
        $fp = fopen('php://input', 'r');
        $http_body = stream_get_contents($fp);
        fclose($fp);

        $iv_from_http_header = $_SERVER['HTTP_X_INITIALIZATION_VECTOR'];
        $auth_tag_from_http_header = $_SERVER['HTTP_X_AUTHENTICATION_TAG'];

        file_put_contents('logs/sibs_'.date('Y_m_d_h_i_s').'.txt',$iv_from_http_header.'&TAG='.$auth_tag_from_http_header.'&BODY='.$http_body);
        
        //$result = file_get_contents('http://62.28.70.93/sibs/webhookdecode.php?IV='.$iv_from_http_header.'&TAG='.$auth_tag_from_http_header.'&BODY='.$http_body);
        //if(!$result)$result = file_get_contents('http://www.memogadget.com/sibs/webhookdecode_g27.php?IV='.$iv_from_http_header.'&TAG='.$auth_tag_from_http_header.'&BODY='.$http_body);
        //********************************************************************************************/

        $key_from_configuration = 'S7eNHuj1IhACUB0O4K0QsoX7+v9q4BWO96TtCSBCcjM=';



       /* $key = hex2bin($key_from_configuration);
        $iv = hex2bin($iv_from_http_header);
        $auth_tag = hex2bin($auth_tag_from_http_header);
        $cipher_text = hex2bin($http_body);
        $result = openssl_decrypt($cipher_text, "aes-256-gcm", $key, OPENSSL_RAW_DATA, $iv, $auth_tag);*/

        //$result = sodium_decrypt($key_from_configuration, $iv_from_http_header, $http_body , $auth_tag_from_http_header);
        if(!$result)$result = file_get_contents('https://www.memogadget.com/sibs/webhookdecode_webelec.php?IV='.urlencode($iv_from_http_header).'&TAG='.urlencode($auth_tag_from_http_header).'&BODY='.urlencode($http_body));
                                                  //http://www.memogadget.com/sibs/webhookdecode_webelec.php
        //********************************************************************************************/


        file_put_contents('logs/sibs_w_'.date('Y_m_d_h_i_s').'.txt', $result);

        $json_decode=json_decode($result,true);
        $type=          $json_decode['type'];
        $id=            $json_decode['transactionID'];
        $paymentBrand=  $json_decode['paymentMethod'];
        $paymentType=   $json_decode['paymentType'];
        $code=          $json_decode['returnStatus']['statusCode'];
        $amount=        $json_decode['value'];



        //Esta a funcionar:
        if($paymentType=='PURS' && $paymentBrand=='MBWAY')
        {
            file_put_contents('logs/sibs_mbway_'.date('Y_m_d_h_i_s').'.txt', $result);

            $path=dirname( __FILE__ )."/../../../../core/";
            require_once $path.'core_files.php';

            $db_link = db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
            $sql = "select * from sibs_mbway_logs WHERE Erros like '%\"$id\"%'";
            $rs = new DataObj;
            $rs->db_open($db_link,$GLOBALS['DB_name'], $sql);
            if (!$rs->EOF())
            {
                $IDb = $rs->db_data['ID'];
                $Valor = $rs->db_data['Valor'];
                $client_id = $rs->db_data['client_id'];
                $id_enc = $rs->db_data['ID_Encomenda'];

                if($code == '000'
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
                    $sql = 'UPDATE sibs_mbway_logs set Estado = 13, Webhook = "'.addslashes(serialize($http_body)).'" WHERE ID = "'.$IDb.'"';
                    Runquery($db_link,$GLOBALS['DB_name'], $sql);
                    //$sql="UPDATE encomendas set Estado=14 where ID=$id_enc";
                    $sql="UPDATE encomendas set Estado=8,Encomenda_Paga=1 where ID=$id_enc";
                    Runquery($db_link, $GLOBALS['DB_name'], $sql);
                }
                else
                {                    
                    if($code == '100.396.101' || $code == '100.396.101' || $code == '10.106.0009' || $code == '01.106.0001')//recusado por algum motivo
                    {
                        $sql = 'UPDATE sibs_mbway_logs set Estado = 3, Webhook = "'.addslashes(serialize($http_body)).'" WHERE ID = "'.$IDb.'"';
                        Runquery($db_link,$GLOBALS['DB_name'], $sql);
                        $sql="UPDATE encomendas set Estado=23 where ID=$id_enc";
                        Runquery($db_link, $GLOBALS['DB_name'], $sql);
                    }  
                    elseif($code != '800.400.500')//timout
                    {
                        $sql = 'UPDATE sibs_mbway_logs set Estado = 4, Webhook = "'.addslashes(serialize($http_body)).'" WHERE ID = "'.$IDb.'"';
                        Runquery($db_link,$GLOBALS['DB_name'], $sql);
                        $sql="UPDATE encomendas set Estado=24 where ID=$id_enc";
                        Runquery($db_link, $GLOBALS['DB_name'], $sql);
                    }                
                }
            }
            $rs->db_close();
        }

        //..............................
        if($paymentType=='PURS' && $paymentBrand=='CARD')
        {
            $res = $json_decode;
            $transactionID =$res["merchant"]['transactionId'];

            $path=dirname( __FILE__ )."/../../../../core/";
            require_once $path.'core_files.php';

            $db_link = db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);

            $sql = 'SELECT * FROM pagamentos_unicre WHERE ID_ENC="'.$transactionID.'"';
            //echo($sql."<br>");
            $rs = new DataObj;
            $rs->db_open($db_link, $GLOBALS['DB_name'], $sql);

            if(!$rs->EOF())
            {
                $IDb=$rs->db_data['ID'];

                $ID_ENC=$rs->db_data['ID_ENC'];

                if($res['returnStatus']['statusMsg']=='Success' && $res['paymentStatus']=='Success')
                {
                    $sql = 'UPDATE pagamentos_unicre set Estado = 13, UDate="'.date("Y-m-d H:m:i").'", Code="'.$res['statusMsg'].'" WHERE ID = '.$IDb;
                    Runquery($db_link,$GLOBALS['DB_name'], $sql);
                   // $sql = 'UPDATE sibs_visa_logs set Estado = 13, Webhook = "'.addslashes(serialize($res)).'" WHERE ID_Pagamento = "'.$IDb.'"';
                   // Runquery($db_link,$GLOBALS['DB_name'], $sql);
                    $sql = "UPDATE encomendas set Estado=7,Encomenda_Paga=1 WHERE ID='$transactionID'";
                    $res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
                }
                else
                {
                    $sql = 'UPDATE pagamentos_unicre set Estado = 4, UDate="'.date("Y-m-d H:m:i").'", Code="'.$res['statusMsg'].'" WHERE ID = '.$IDb;
                    Runquery($db_link,$GLOBALS['DB_name'], $sql);
                  //  $sql = 'UPDATE sibs_visa_logs set Estado = 4, Webhook = "'.addslashes(serialize($res)).'" WHERE ID_Pagamento = "'.$IDb.'"';
                  //  Runquery($db_link,$GLOBALS['DB_name'], $sql);

                    $sql = "UPDATE encomendas SET Estado=22 WHERE ID='$ID_ENC'";
                    $res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
                }
            }
            $rs->db_close();

            
        }




        //Estas eram do sistema antigo VISA        
        if($paymentType=='DB' && ($paymentBrand=='MASTER' || $paymentBrand=='VISA'))
        {
            file_put_contents('logs/sibs_visa_'.date('Y_m_d_h_i_s').'.txt', $result);

            $payments_id=$json_decode['payload']['id'];
            $id=$json_decode['payload']['ndc'];
            $registrationId=$json_decode['payload']['registrationId'];//para pagamentos por token
            $card_bin=$json_decode['payload']['card']['bin'];
            $card_last4Digits=$json_decode['payload']['card']['last4Digits'];
            $card_holder=$json_decode['payload']['card']['holder'];
            $card_expiryMonth=$json_decode['payload']['card']['expiryMonth'];
            $card_expiryYear=$json_decode['payload']['card']['expiryYear'];


            //file_put_contents('logs/sibs_visa_2_'.date('Y_m_d_h_i_s').'.txt', $id);

            $path=dirname( __FILE__ )."/../../../../core/";
            require_once $path.'core_files.php';
            $db_link = db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);

            $sql = "select * from pagamentos_unicre WHERE Resposta1 like '%\"$id\"%'";
            file_put_contents('logs/sibs_visa_3_'.date('Y_m_d_h_i_s').'.txt', $sql);
            $rs = new DataObj;
            $rs2 = new DataObj;
            $rs->db_open($db_link,$GLOBALS['DB_name'], $sql);
            if (!$rs->EOF())
            {
                $IDb = $rs->db_data['ID'];
                $Telemovel = $rs->db_data['Telemovel'];
                $ValorCativo = $rs->db_data['ValorCativo'];
                $token=$rs->db_data['token'];

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
                    || $code == '000.200.100'

                )
                {
                    if($ValorCativo>0)
                    {
                        //neste caso não vamos integrar no NAV e vamos aguardar pelo recibo
                        $sql = 'UPDATE pagamentos_unicre set Estado = 23, Erro="'.$code.'", UDate="'.date("Y-m-d H:m:i").'" WHERE ID = '.$IDb;
                        Runquery($db_link,$GLOBALS['DB_name'], $sql);

                        $sql = 'UPDATE sibs_visa_logs set Estado = 13, Webhook = "'.addslashes(serialize($http_body)).'" WHERE ID_Encomenda = "'.$IDb.'"';
                        Runquery($db_link,$GLOBALS['DB_name'], $sql);

                        
                        $sql="SELECT * FROM `sa_clients` WHERE `client_id_app`='".$token."'";
                        $rs2->db_open($db_link,$GLOBALS['DB_name'],$sql);
                        if(!$rs2->EOF())
                        {
                            $valor_carregado=floatval($rs2->db_data['valor_carregado'])+$ValorCativo;
                            $sql="UPDATE `sa_clients` SET `valor_carregado`='$valor_carregado',`payments_id`='$IDb',`sibs_id`='$payments_id' WHERE `client_id_app`='".$token."'";
                            Runquery($db_link,$GLOBALS['DB_name'],$sql);
                        }
                        else
                        {
                            $sql="UPDATE `sa_clients` SET `valor_carregado`='$ValorCativo',`payments_id`='$IDb',`sibs_id`='$payments_id' WHERE `client_id_app`='".$token."'";
                            Runquery($db_link,$GLOBALS['DB_name'],$sql);
                        }
                        $rs2->db_close();


                        //-------------------------- este codigo tem de ir para outro lado
                        /*$valor_cobrar = 1;
                        $valor_registar = $ValorCativo - $valor_cobrar;
                        devolver_visa($payments_id,$valor_cobrar);
                        $sql = 'UPDATE pagamentos_unicre set Estado = 13, Valor='.$valor_cobrar.', UDate="'.date("Y-m-d H:m:i").'" WHERE ID = '.$IDb;
                        Runquery($db_link,$GLOBALS['DB_name'], $sql);*/
                    }
                    else
                    {
                        $sql = 'UPDATE pagamentos_unicre set Estado = 13, Erro="'.$code.'", UDate="'.date("Y-m-d H:m:i").'" WHERE ID = '.$IDb;
                        Runquery($db_link,$GLOBALS['DB_name'], $sql);
                        $sql = 'UPDATE sibs_visa_logs set Estado = 13, Webhook = "'.addslashes(serialize($http_body)).'" WHERE ID_Encomenda = "'.$IDb.'"';
                        Runquery($db_link,$GLOBALS['DB_name'], $sql);
                    }

                    if($registrationId!='')
                    {
                        //Atualiza com o ultimo embora acho que nao vou necessitar
                        $sql = "UPDATE sa_clients SET Sibs_Visa_checkoutId='$registrationId' WHERE telemovel = '$Telemovel'";
                        Runquery($db_link,$GLOBALS['DB_name'],$sql);
                        $sql="SELECT * FROM sibs_visa_cartoes WHERE Telemovel='".$Telemovel."' and registrationId = '$registrationId'";
                        $rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
                        if(!$rs->EOF())
                        {
                            //Ja existe
                        }
                        else
                        {
                            $sql = "INSERT INTO sibs_visa_cartoes (Telemovel,registrationId,bin,last4Digits,holder,expiryMonth,expiryYear) values ('$Telemovel','$registrationId','$card_bin','$card_last4Digits','$card_holder','$card_expiryMonth','$card_expiryYear')";
                            Runquery($db_link,$GLOBALS['DB_name'],$sql);
                        }
                        $rs->db_close();
                        //Caso este tipo de pagamento ainda nao exista vamos criar:
                        $sql="SELECT * FROM sa_clients_metodos_pagamento WHERE Telemovel='".$Telemovel."' and Tipo = '1' and registrationId = '$registrationId'";
                        $rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
                        if(!$rs->EOF())
                        {
                            //Ja existe
                        }
                        else
                        {
                            $sql="UPDATE sa_clients_metodos_pagamento set Predefinido = 0 WHERE Telemovel = '$Telemovel'";
                            $res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
                            $sql="INSERT INTO sa_clients_metodos_pagamento (Telemovel,registrationId,Tipo,Predefinido,last4Digits,holder,expiryMonth,expiryYear) values('$Telemovel','$registrationId','1','1','$card_last4Digits','$card_holder','$card_expiryMonth','$card_expiryYear')";
                            $res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
                        }
                        $rs->db_close();
                    }
                    
                }
                else
                {
                    $sql = 'UPDATE pagamentos_unicre set Estado = 4, Erro="'.$code.'", UDate="'.date("Y-m-d H:m:i").'" WHERE ID = '.$IDb;
                    Runquery($db_link,$GLOBALS['DB_name'], $sql);
                    $sql = 'UPDATE sibs_visa_logs set Estado = 4, Webhook = "'.addslashes(serialize($http_body)).'" WHERE ID_Encomenda = "'.$IDb.'"';
                    Runquery($db_link,$GLOBALS['DB_name'], $sql);
                }
            }
            $rs->db_close();
        }

        //Estas eram do sistema antigo REF. MB
        if($paymentBrand=='SIBS_MULTIBANCO' && ($paymentType=='RC' || $paymentType=='PA'))
        {
            file_put_contents('logs/sibs_mb_'.date('Y_m_d_h_i_s').'.txt', $result);

            $path=dirname( __FILE__ )."/../../../../core/";
            require_once $path.'core_files.php';
            $db_link = db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);


            $id=            $json_decode['payload']['referencedId'];



            $sql = "select * from pagamentos_unicre WHERE Resposta1 like '%\"$id\"%'";
            $rs = new DataObj;
            $rs->db_open($db_link,$GLOBALS['DB_name'], $sql);
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
                    Runquery($db_link,$GLOBALS['DB_name'], $sql);
                    $sql = 'UPDATE sibsreferences set Estado = 13, Webhook = "'.addslashes(serialize($http_body)).'" WHERE id_order = "'.$IDb.'"';
                    Runquery($db_link,$GLOBALS['DB_name'], $sql);
                }
                else
                {
                    $sql = 'UPDATE pagamentos_unicre set Estado = 4, UDate="'.date("Y-m-d H:m:i").'" WHERE ID = '.$IDb;
                    Runquery($db_link,$GLOBALS['DB_name'], $sql);
                    $sql = 'UPDATE sibsreferences set Estado = 4, Webhook = "'.addslashes(serialize($http_body)).'" WHERE id_order = "'.$IDb.'"';
                    Runquery($db_link,$GLOBALS['DB_name'], $sql);
                }
            }
            $rs->db_close();
        }

        

        //Para dpg:
        header('Content-Type: application/json; charset=utf-8');
        echo '{"statusCode": "200","statusMsg": "Success","notificationID": "'.$json_decode['notificationID'].'"}';
    }


    function sodium_decrypt( $webhookSecret, $iv_from_http_header, $http_body , $auth_tag_from_http_header )
    {
        $key = mb_convert_encoding($webhookSecret, "UTF-8", "BASE64");
        $iv = mb_convert_encoding($iv_from_http_header, "UTF-8", "BASE64");
        $cipher_text = mb_convert_encoding($http_body, "UTF-8", "BASE64") . mb_convert_encoding($auth_tag_from_http_header, "UTF-8", "BASE64");
        $result = sodium_crypto_aead_aes256gcm_decrypt($cipher_text, "", $iv, $key);
        //$result = openssl_decrypt($cipher_text, "aes-256-gcm", $key, OPENSSL_RAW_DATA, $iv, $auth_tag);
        return $result;
    }

?>