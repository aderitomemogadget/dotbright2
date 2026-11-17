<?php
    include "settings.inc.php";
    if($task=="apagar") {$task_button="Apagar";$task_form="apaga";}
    elseif($ID==0){$task_button="Inserir";$task_form="insere";}
    else {$task_button="Alterar";$task_form="altera";}
    $D=getdate();
    $D2=getdate();
    $C="";$T="";$I1="";$I2="";$ordem="0";
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$module_voucher,$admin_id;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new DataObj;
    $rs2=new DataObj;
    $rs3=new DataObj;

    $count=0;
    $count_res=0;
    // $sql="SELECT ID_PAI FROM reserva_stocks WHERE Qtd>0";

    // get individual products
    $sql="SELECT ID, Estado FROM encomendas WHERE Estado IN (7, 16, 38, 8, 37, 15) ORDER BY ID ASC";
    $rs3->db_open($db_link,$DB_name,$sql);
    while(!$rs3->EOF()) // Existe o codigo de desconto disponivel
    {
        $count++;
        $ID_Enc=$rs3->db_data["ID"];
        $Estado_Enc=$rs3->db_data["Estado"];
        
        $sql="SELECT * FROM reserva_stocks WHERE ID_PAI=".$ID_Enc." AND Qtd > 0";
        $rs2->db_open($db_link,$DB_name,$sql);
        if(!$rs2->EOF())
        {
            $count_res++;
            echo '<br>'.$ID_Enc.' ('.$Estado_Enc.') ';
            echo ' (com reserva) ';

            if($Estado_Enc==7 || $Estado_Enc==16 || $Estado_Enc==38)
            {
                echo '[PAGA]';
                // update_encomendas_paga($ID_Enc);
            }
            elseif($Estado_Enc==8 || $Estado_Enc==37 || $Estado_Enc==15)
            {
                echo '[NÃO PAGA]';
                // update_encomendas_nao_paga($ID_Enc);
            }
            
        }
        else
        {
            // echo '<br>'.$ID_Enc.' ('.$Estado_Enc.') ';
            // echo ' (sem reserva) ';
        }

        $rs3->db_next();
    }
    $rs3->db_close();

    echo '<br><hr><br>total: '.$count;
    echo '<br>com reserva: '.$count_res;



?>