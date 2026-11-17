<?php
    $path = dirname(__FILE__) . '/../../../core/';
    require_once $path . 'core_files.php';
    include  "funcoes.php";

    //Dados para sand box:
    /*define(MODULE_PAYMENT_VIVAWALLET_MERCHANTID,'0f55430e-a0e9-44e4-a7a1-052c1114debb');
    define(MODULE_PAYMENT_VIVAWALLET_MERCHANTPASS,'RVtygP2J5cSOZQBDYAsgsrVbHK01LG');
    define(MODULE_PAYMENT_VIVAWALLET_MODE,'Testing');*/

    //Dados para producao:
    define(MODULE_PAYMENT_VIVAWALLET_MERCHANTID,'8ada17ef-ad2f-46d5-8fda-e8033fdd28da');
    define(MODULE_PAYMENT_VIVAWALLET_MERCHANTPASS,'RVtygP2J5cSOZQBDYAsgsrVbHK01LG');
    define(MODULE_PAYMENT_VIVAWALLET_MODE,'Producao');


    //https://www.mhr.pt/external_api/pagamentos/pgm_vivawallet_visa/success.php?t=a4a467ca-0712-49f3-a19d-896dc9a9ceb2&s=9808262621904436&lang=pt-PT&eventId=0&eci=2

    //fail
    if(preg_match("/fail/i", $_SERVER['REQUEST_URI']))
    {
        if(isset($_GET['s']) && $_GET['s'] !='')
        {
            $rs=new DataObj;
            global $DB_host,$DB_user,$DB_pass, $DB_name;
            $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

            $sql = "select ref, sessionid,enc_id from vivawallet_data where OrderCode='".addslashes($_GET['s'])."'";
            $rs->db_open($db_link, $DB_name, $sql);
            if(!$rs->EOF())
            {
                $ID = $rs->db_data['id'];
                $enc_id = $rs->db_data['enc_id'];

                $sql = "update vivawallet_data set order_state='F' where OrderCode='".addslashes($_GET['s'])."'";
                $res=Runquery($db_link, $DB_name, $sql);

                $sql = "UPDATE encomendas SET Encomenda_Paga=0, Encomenda_Status='Cancel', Estado='37' WHERE ID='".$enc_id."'";
                $res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
                if($dados = cliente_logado())
                {
                    $referer = $GLOBALS['site_http_url'].seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'?show_compras=1&id_enc='.$enc_id;
                }
                else
                {
                    $referer = $GLOBALS['site_http_url'];
                }

                echo drawRedirectPage(getLinguas('key_shop_msg1_c'), $referer, 3000, 1);

                die();



            }
            $rs->db_close();
        }
    }//end fail

    //success
    if(preg_match("/success/i", $_SERVER['REQUEST_URI']))
    {

        if(isset($_GET['s']) && $_GET['s'] !='')
        {
            $rs=new DataObj;
            global $DB_host,$DB_user,$DB_pass, $DB_name;
            $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

            $sql = "select ref, sessionid,enc_id from vivawallet_data where OrderCode='".addslashes($_GET['s'])."'";
            $rs->db_open($db_link, $DB_name, $sql);
            if(!$rs->EOF())
            {
                $ID = $rs->db_data['id'];
                $enc_id = $rs->db_data['enc_id'];

                $sql = "update vivawallet_data set order_state='P' where OrderCode='".addslashes($_GET['s'])."'";
                $res=Runquery($db_link, $DB_name, $sql);

                $payment_status='Completed';
                $sql = "UPDATE encomendas SET Encomenda_Paga=1,Transaction_Code ='".addslashes($_GET['s'])."',Encomenda_Status='".$payment_status."',Estado='36' WHERE ID='".$enc_id."'";
                $res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
                update_encomendas_paga($enc_id);
                enviarEmailCliente($enc_id);


                if($dados = cliente_logado())
                {
                    $referer = $GLOBALS['site_http_url'].seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'?show_compras=1&id_enc='.$enc_id;
                }
                else
                {
                    $referer = $GLOBALS['site_http_url'];
                }

                unset($_SESSION['ss_shop']);
                unset($_SESSION['ss_n_enc']);
                $shop = array();
                $n_enc = 0;
                if($GLOBALS['module_voucher']==1)
                {
                    unset($_SESSION['ss_voucher']);
                    unset($_SESSION['ss_n_voucher']);
                    $voucher = array();
                    $n_voucher = 0;
                }

                echo drawRedirectPage(getLinguas('key_shop_msg1'), $referer, 3000, 1);

                die();



            }
            $rs->db_close();
        }
    }//end success


    //webhook
    if(1==1 || preg_match("/webhook/i", $_SERVER['REQUEST_URI']))
    {
        $postdata = file_get_contents("php://input");

        $MerchantID = MODULE_PAYMENT_VIVAWALLET_MERCHANTID;
        $Password =  html_entity_decode(MODULE_PAYMENT_VIVAWALLET_MERCHANTPASS);

        if(MODULE_PAYMENT_VIVAWALLET_MODE=='Testing'){
            $url   = 'https://demo.vivapayments.com/api/messages/config/token/';
        } else {
            $url   = 'https://www.vivapayments.com/api/messages/config/token/';
        }

        $data = "";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization:Basic '.base64_encode($MerchantID.':'.$Password)    ));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        if(curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);

        echo $response;



        file_put_contents('log/success2_'.date('Y_m_d_h_i_s').'.txt', file_get_contents('php://input'));

        try {

        if(is_object(json_decode($postdata))){
            $resultObj=json_decode($postdata);
        }
        } catch( Exception $e ) {
            echo $e->getMessage();
        }


        if(sizeof($resultObj->EventData) > 0)
        {
            /*
            $StatusId = $resultObj->EventData->StatusId;
            $OrderCode = $resultObj->EventData->OrderCode;
            $statustr = $this->vivawallet_processing;

            $check_query = "select ref, sessionid from vivawallet_data where order_state='I' and OrderCode='".addslashes($OrderCode)."'";
            $oQuery = $osC_Database->query($check_query);
            $oRecordset = $oQuery->execute();

            if(mysql_num_rows($oRecordset) && $StatusId=='F')
            {
                $oRecord = mysql_fetch_assoc($oRecordset);

                $update_query = "update vivawallet_data set order_state='P' where OrderCode='".addslashes($OrderCode)."'";
                $uQuery = $osC_Database->query($update_query);
                $uRecordset = $uQuery->execute();

                $comment = 'OrderCode ' . addslashes($OrderCode);
                osC_Order::process($oRecord['ref'], $this->order_status, $comment);
                $osC_ShoppingCart->reset(true);
            }*/
        }
    }//end webhook


?>