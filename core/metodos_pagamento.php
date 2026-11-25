<?php
if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__ )exit();

function getAllMetodosPagamento($lang='')
{
    if($lang == '') $lang=$GLOBALS['site_lang'];
    $db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
    $rs=new Dataobj();

    $cl = cliente_perfil($GLOBALS['cli_id']);

    if($cl['Testes']=='1')$sql="SELECT mp.*,mpl.Cabecalho,mpl.Texto1 FROM metodos_pagamento AS mp INNER JOIN metodos_pagamento_lang AS mpl ON mp.ID=mpl.ID_PAI WHERE mpl.Lang='$lang' ORDER BY mp.ID ASC";
    else $sql="SELECT mp.*,mpl.Cabecalho,mpl.Texto1 FROM metodos_pagamento AS mp INNER JOIN metodos_pagamento_lang AS mpl ON mp.ID=mpl.ID_PAI WHERE mpl.Lang='$lang' AND Visivel='1' ORDER BY mp.ID ASC";
    $rs->db_open($db_link,$GLOBALS['DB_name'], $sql);
    return $rs;
}
function getFirstMetodosPagamento($lang='')
{
    if($lang == '') $lang=$GLOBALS['site_lang'];
    $db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
    $rs=new Dataobj();
    $sql="SELECT mp.*,mpl.Cabecalho,mpl.Texto1 FROM metodos_pagamento AS mp INNER JOIN metodos_pagamento_lang AS mpl ON mp.ID=mpl.ID_PAI WHERE mpl.Lang='$lang' AND Visivel='1' ORDER BY mp.ID ASC LIMIT 1";
    $rs->db_open($db_link,$GLOBALS['DB_name'], $sql);
    return $rs;
}
function getMetodoPagamentoById($id, $lang='')
{
    if($lang == '') $lang=$GLOBALS['site_lang'];
    $db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
    $rs=new Dataobj();
    $sql="SELECT mp.*,mpl.Cabecalho,mpl.Texto1 FROM metodos_pagamento AS mp INNER JOIN metodos_pagamento_lang AS mpl ON mp.ID=mpl.ID_PAI WHERE mpl.Lang='$lang' AND mp.ID='$id' ORDER BY mp.ID ASC";
    $rs->db_open($db_link,$GLOBALS['DB_name'], $sql);
    return $rs;
}
function getNomeMetodoPagamentoByID($id)
{
    $db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
    $rs2=new DataObj();
    $metodo_pagamento='';
    $sql="SELECT mpl.Cabecalho FROM metodos_pagamento AS mp INNER JOIN metodos_pagamento_lang AS mpl ON mpl.ID_PAI=mp.ID WHERE mp.ID='$id' AND mpl.Lang='{$GLOBALS['site_lang']}'";
    $rs2->db_open($db_link,$GLOBALS['DB_name'], $sql);
    if(!$rs2->EOF())
    {
        $metodo_pagamento=$rs2->db_data['Cabecalho'];
    }
    $rs2->db_close();
    return $metodo_pagamento;
}
function getEncomendaMetodoPagamento($metodoPagamento)
{
    if($metodoPagamento == 0)$rs=getFirstMetodosPagamento();
    else $rs=getMetodoPagamentoById($metodoPagamento);

    if(!$rs->EOF())$info=$rs->db_data;
    else $info=false;

    $rs->db_close();
    return $info;
}
function showLayoutMetodosPagamento_admin($metodoPagamento)
{
    global $m_metodoenvio; //Podem existir metodos de pagamento que não sejam para disponibilizar para todos os metodos de envio, exemplo, na loja
    $ponto_recolha_loja=0;
    if($m_metodoenvio>0)
    {
        $metodoEnvioObj=getEncomendaMetodoExpedicao($m_metodoenvio);
        $ponto_recolha_loja=$metodoEnvioObj['Loja'];
    }
    ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <span class="icon">
                    <i class="fa fa-credit-card"></i>
                </span>
                <span class="text"><?=getLinguas('key_pgm_p2',$GLOBALS['site_lang']);?></h5><br><?=getLinguas('key_pagamento',$GLOBALS['site_lang']);?></span>
            </h4>
        </div>
        <div class="panel-body">
            <?php
            $rs=getAllMetodosPagamento();
            if(!$rs->EOF())
            {
                $pos=0;
                while (!$rs->EOF())
                {
                    $nome_metodo=$rs->db_data['Cabecalho'];
                    $ID=$rs->db_data['ID'];
                    $metodo_descricao=$rs->db_data['Texto1'];
                    $img_ext=$rs->db_data['img_ext'];
                    $tipo=$rs->db_data['Tipo'];

                    ?>
                    <div class="form-group">
                        <div class="radio">
                            <label for="pagamento<?php echo $ID ?>" style="font-size:12px;border:solid 1px #ccc;width:100%;padding:5px;cursor:pointer">
                                <div style="height:50px;line-height:50px;float:left"><input class="metodos_pagamento" type="radio" name="m_metodopagamento" id="pagamento<?php echo $ID ?>" value="<?php echo $ID ?>" <?php echo ($metodoPagamento == $ID) ? ' checked="checked" ' : ''; ?> onchange="reload_shopping_cart();"></div>
                                <?php
                                    if(dotbright_image_resolve($GLOBALS['site_shell_url'].'files/metodos_pagamento/'.$ID.'_1.'.$img_ext) )
                                    {
                                        echo '<div style="height:50px;line-height:50px;float:left"><img style="height:50px" src="'.$GLOBALS['site_http_url'].'files/metodos_pagamento/'.$ID.'_1.'.$img_ext.'?dp='.dumpcache.'" alt="'.$nome_metodo.'" /></div>';
                                        if($metodo_descricao != '')echo str_replace("\n",'<br>', $metodo_descricao);
                                        else echo '<div style="height:50px;line-height:50px;float:left">'.$nome_metodo.'</div>';
                                    }
                                    else
                                    {
                                        if($metodo_descricao != '')echo str_replace("\n",'<br>', $metodo_descricao);
                                        else echo '<div style="height:50px;line-height:50px;float:left">'.$nome_metodo.'</div>';
                                    }?>
                                    <br>
                                <?php
                                /*if($metodo_descricao != '')
                                {
                                     echo str_replace("\n",'<br>', $metodo_descricao);
                                }*/
                                ?>
                            </label><?php

                            if($metodoPagamento == $ID && ($tipo==MP_TP_MBWAY_SIBS || $tipo==MP_TP_MBWAY_CGD || $tipo==MP_TP_MBWAY_LUSOPAY || $tipo==MP_TP_MBWAY_IFTHEN || $tipo==MP_TP_MBWAY_EUPAGO || $tipo==MP_TP_MBWAY_UNICRE || $tipo==MP_TP_MBWAY_EASYPAY) )
                            {
                                //Mbway numero de telefone
                                echo '<div style="border:solid 1px #ccc;padding: 10px;" class="info_pagamento" id="info_'.$tipo.'" >
                                    <br>Para solicitar o pagamento por MBWay, indique o telemóvel associado à sua aplicação MBWay <br>
                                    A solicitação de pagamento só será efetuada após concluir a encomenda:<br>
                                    <input class="form-control" style="width:150px" type="text" id="mbway_tel" name="mbway_tel"> (Não necessita de colocar +351)</div>';
                            }

                            ?>
                        </div>
                    </div>
                    <?php

                    $pos++;
                    $rs->db_next();
                }
                $rs->db_close();

            }?>
        </div>
    </div><?php
}
function showLayoutMetodosPagamento($metodoPagamento)
{
    global $m_metodoenvio; //Podem existir metodos de pagamento que não sejam para disponibilizar para todos os metodos de envio, exemplo, na loja
    $ponto_recolha_loja=0;
    if($m_metodoenvio>0)
    {
        $metodoEnvioObj=getEncomendaMetodoExpedicao($m_metodoenvio);
        $ponto_recolha_loja=$metodoEnvioObj['Loja'];
    }
    ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <span class="icon">
                    <i class="fa fa-credit-card"></i>
                </span>
                <span class="text"><?=getLinguas('key_pgm_p2',$GLOBALS['site_lang']);?></h5><br><?=getLinguas('key_pagamento',$GLOBALS['site_lang']);?></span>
            </h4>
        </div>
        <div class="panel-body">
            <?php
            $rs=getAllMetodosPagamento();
            if(!$rs->EOF())
            {
                $pos=0;
                while (!$rs->EOF())
                {
                    $nome_metodo=$rs->db_data['Cabecalho'];
                    $ID=$rs->db_data['ID'];
                    $metodo_descricao=$rs->db_data['Texto1'];
                    $img_ext=$rs->db_data['img_ext'];
                    $tipo=$rs->db_data['Tipo'];

                    if($tipo != 20 || ($tipo==20 && $ponto_recolha_loja==1))
                    {
                    ?>
                    <div class="form-group">
                        <div class="radio">
                            <label for="pagamento<?php echo $ID ?>" style="font-size:12px;border:solid 1px #ccc;width:100%;padding:5px;cursor:pointer;display: flex;align-items: center;">
                                <div style="height:50px;line-height:50px;float:left">
                                        <input class="metodos_pagamento" type="radio" name="m_metodopagamento" id="pagamento<?php echo $ID ?>" value="<?php echo $ID ?>" <?php echo ($metodoPagamento == $ID) ? ' checked="checked" ' : ''; ?> onchange="reload_shopping_cart();">
                                </div>
                                <?php
                                    if(dotbright_image_resolve($GLOBALS['site_shell_url'].'files/metodos_pagamento/'.$ID.'_1.'.$img_ext) ){
                                        echo '<div style="margin-right:10px;/*width:150px*/"><img style="height:50px" src="'.$GLOBALS['site_http_url'].'files/metodos_pagamento/'.$ID.'_1.'.$img_ext.'?dp='.dumpcache.'" alt="'.$nome_metodo.'" /></div>';
                                        
                                        if($metodo_descricao != ''){
                                            echo '<div class="pagamentos_descricao"><div>';
                                            echo str_replace("\n",'<br>', $metodo_descricao);
                                            echo '</div></div>';
                                        }
                                        //else echo '<div style="height:50px;line-height:50px;float:left">'.$nome_metodo.'</div>';
                                    }
                                    else{
                                        echo '<div><div>';

                                        if($metodo_descricao != '')echo str_replace("\n",'<br>', $metodo_descricao);
                                        else echo $nome_metodo;

                                        echo '</div></div>';
                                    } ?>
                                    <br>
                                <?php
                                /*if($metodo_descricao != '')
                                {
                                     echo str_replace("\n",'<br>', $metodo_descricao);
                                }*/
                                ?>
                            </label><?php

                            if($metodoPagamento == $ID && ((defined('MP_TP_MBWAY_SIBS') && $tipo == MP_TP_MBWAY_SIBS) || (defined('MP_TP_MBWAY_CGD') && $tipo == MP_TP_MBWAY_CGD) || (defined('MP_TP_MBWAY_LUSOPAY') && $tipo == MP_TP_MBWAY_LUSOPAY) || (defined('MP_TP_MBWAY_IFTHEN') && $tipo == MP_TP_MBWAY_IFTHEN) || (defined('MP_TP_MBWAY_EUPAGO') && $tipo == MP_TP_MBWAY_EUPAGO) || (defined('MP_TP_MBWAY_UNICRE') && $tipo == MP_TP_MBWAY_UNICRE) || (defined('MP_TP_MBWAY_EASYPAY') && $tipo == MP_TP_MBWAY_EASYPAY)) )
                            {
                                //Mbway numero de telefone
                                echo '<div style="border:solid 1px #ccc;padding: 10px;" class="info_pagamento" id="info_'.$tipo.'" >
                                    <br>Para solicitar o pagamento por MBWay, indique o telemóvel associado à sua aplicação MBWay <br>
                                    A solicitação de pagamento só será efetuada após concluir a encomenda:<br>
                                    <input class="form-control" style="width:150px" type="text" id="mbway_tel" name="mbway_tel"> (Não necessita de colocar +351)</div>';
                            }

                             if ($metodoPagamento == $ID && (defined('MP_TP_FLOAPAY') && $tipo == MP_TP_FLOAPAY))
                                    {
                                        echo '<div style="border:solid 1px #ccc;padding:10px" class="info_pagamento" id="info_'.$tipo.'" >                                   
                                        A solicitação de pagamento só será efetuada após concluir a encomenda:<br>
                                        <input class="form-control" style="width:150px" type="text" id="floa_dtnasc"  name="floa_dtnasc" placeholder="dd/mm/aaaa" value="';
                                        if(isset($_POST['floa_dtnasc'])) {echo $_POST['floa_dtnasc'];}
                                        echo '">(Informe a Data Nascimento)</div>';
                                    }

                            if ($metodoPagamento == $ID && defined('MP_TP_SEQURA') && $tipo == MP_TP_SEQURA)
                            {
                                echo '<div style="border:solid 1px #ccc;padding:10px" class="info_pagamento" id="info_'.$tipo.'">
                                    Depois de concluir a encomenda, os dados necessários ao pagamento em prestações serão pedidos diretamente no formulário seguro da seQura.
                                </div>';
                            }

                            /*if($metodoPagamento == $ID && ((defined('MP_TP_CC_VIVAWALLET') && $tipo == MP_TP_CC_VIVAWALLET)) )
                            {
                                echo '<div class="info_pagamento" id="info_'.$tipo.'" >';

                                ?>

                                <div class="row item" style="padding:15px" id="visa_<?=MP_TP_CC_VIVAWALLET?>">
                                    <div class="small-2 column"></div>
                                    <div class="small-14 column">

                                    <div class="row" style="border:solid 1px #ccc;padding: 10px;">
                                        <div class="col-xs-12 col-sm-6">
                                            <div class="form-group">
                                                <label for="card-number" style="margin:0;padding:0;font-weight:bold">Número do Cartão</label>
                                                <input style="max-width:100%;width:100%" type="text" maxlength="20" autocomplete="off" class="form-control card-number required" name="card-number<?=$tipo?>">
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-6">
                                            <div class="form-group">
                                                <label for="card-cvc" style="margin:0;padding:0;font-weight:bold">CVC</label>
                                                <input style="max-width:100px" type="text" maxlength="4" autocomplete="off" class="form-control card-cvc required" name="card-cvc<?=$tipo?>">
                                            </div>
                                        </div>
                                        <div class="col-xs-12">

                                            <div class="form-group">
                                            <div class="expiry-wrapper">
                                            <div class="row">
                                            <div class="col-xs-12 col-sm-4">
                                            <label for="card-expiry-month" style="margin:0;padding:0;font-weight:bold">Mês</label>
                                                <select class="form-control required" style="width:100%;color:#000" name="card-expiry-month<?=$tipo?>">
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                    <option value="5">5</option>
                                                    <option value="6">6</option>
                                                    <option value="7">7</option>
                                                    <option value="8">8</option>
                                                    <option value="9">9</option>
                                                    <option value="10">10</option>
                                                    <option value="11">11</option>
                                                    <option value="12">12</option>
                                                </select>
                                            </div>
                                            <div class="col-xs-12 col-sm-4">
                                            <label for="stripe-sensitive" style="margin:0;padding:0;font-weight:bold">Ano</label>
                                                <select class="form-control required" style="color:#000" name="card-expiry-year<?=$tipo?>">
                                                    <?php
                                                    $ano = date('Y');
                                                    $i=0;
                                                    while($i<=10)
                                                    {
                                                        echo '<option value="'.$ano.'">'.$ano.'</option>';
                                                        $ano++;
                                                        $i++;
                                                    }
                                                    ?>
                                                </select>

                                            </div>
                                            </div>
                                            </div>

                                            <span class="payment-errors"></span>

                                        </div>
                                        </div>
                                    </div>


                                    </div>
                                </div><?php

                                echo '</div>';
                            }*/

                            ?>
                        </div>
                    </div>
                    <?php
                    }
                    $pos++;
                    $rs->db_next();
                }
                $rs->db_close();

            }?>
        </div>
    </div><?php
}
function getMetodoPagamentoPaypalGateway($idMetodo, $totalEncomenda, $idEncomenda)
{
    $html='';
    $rs=getMetodoPagamentoById($idMetodo);
    if(!$rs->EOF())
	{
        require_once(SITE_SHELL_URL.$GLOBALS['pgm_metodos'][MP_TP_PAYPAL]['url'].'paypal.class.php');
        $p=new paypal_class;
        $Sandbox=$rs->db_data["Sandbox"];
        if($Sandbox == 1)
        {
            $conta_paypal=$rs->db_data["Username_sandbox"];
            $p->paypal_url='https://www.sandbox.paypal.com/cgi-bin/webscr';
        }
        else
        {
            $conta_paypal=$rs->db_data["Username"];
            $p->paypal_url='https://www.paypal.com/cgi-bin/webscr';
        }
		$this_script=SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_PAYPAL]['url'].'paypal.php';
        $_SESSION['paypal_encid']=$idEncomenda;
        $p->add_field('business', $conta_paypal);
        $p->add_field('return', $this_script.'?action=success');
        $p->add_field('cancel_return', $this_script.'?action=cancel');
        $p->add_field('notify_url', $this_script.'?action=ipn');
        $p->add_field('item_name', 'on4Tech.pt - Encomenda: '.$idEncomenda);
        $p->add_field('amount', $totalEncomenda);
        $p->add_field('item_number', $idEncomenda);
        $p->add_field('quantity', 1);
        $p->add_field('shopping_url', 'Clique aqui para voltar ao '.$GLOBALS['site_title'].' website');
        $p->add_field('currency_code', 'EUR');
        $p->add_field('no_shipping', '1');
        $p->add_field('site_url', str_replace('http://', 'https://', SITE_HTTP_URL));
        $p->add_field('image_url', 'layout/'.PROJETO.'/logos/logo.png');
        $p->add_field('cmd', '_xclick');
        $p->add_field('rm', '2');// Return method=POST
        $p->submit_paypal_post(); // submit the fields to paypal
        unset($_SESSION['CLIENTE_ID_TEMP']);
        $pp='<img src="' .SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_PAYPAL]['url'].'images/paypal_load.gif" border="0" />&nbsp;<img src="'.SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_PAYPAL]['url'].'images/checkout1.png" border=0><br /><br />Dentro de momento será reencaminhado para o paypal para fazer o pagamento, Se não for reencaminhado <a href="javascript:;" onclick=\'document.forms[\'paypal_form\'].submit();\'><u>clique aqui</u></a>';
        $html .= show_alerta2(getLinguas('key_shop_msg1',$GLOBALS['site_lang']).'<br /><br /><span style="color:#4f7e9e">'.$pp.'</span><br /><br />');
        $html .= '<script>setTimeout(function(){document.forms[\'paypal_form\'].submit();},5000);</script>';
    }
    $rs->db_close();
    return $html;
}
function getMetodoPagamentoFloaPayGateway($idMetodo, $totalEncomenda, $idEncomenda)
{
    $html='';
    unset($_SESSION['CLIENTE_ID_TEMP']);
    $url=SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_FLOAPAY]['url'] .'floa.php?id_enc='.$idEncomenda;

    if($GLOBALS['site_lang'] == 'pt')
    {
        $pp='<br /><br />Dentro de momento será reencaminhado para a FloaPay para fazer o pagamento, Se não for reencaminhado <a style="color:#289BA2;text-decoration:none;" href="javascript:;" onclick=\'window.open("'.$ifthen_url.'","_self")\'>clique aqui</a>';
    }
    else
    {
        $pp='<br /><br />Within moments you will be redirected to FloaPay to make the payment, if not redirected <a style="color:#289BA2;text-decoration:none;" href="javascript:;" onclick=\'window.open("'.$ifthen_url.'","_self")\'>click here</a>';
    }
    $html .= show_alerta2(getLinguas('key_shop_msg1',$GLOBALS['site_lang']).'<br /><br /><span style="color:#4f7e9e">'.$pp.'</span><br /><br />');
    $html .= '<script>setTimeout(function(){window.open("'.$url.'","_self");},5000);</script>';
    return $html;
}

function getMetodoPagamentoSequraPayGateway($idMetodo, $totalEncomenda, $idEncomenda)
{
    require_once dirname(__FILE__) . '/../external_api/pagamentos/pgm_sequra/sequra_config.php';
    require_once dirname(__FILE__) . '/../external_api/pagamentos/pgm_sequra/sequra_lib.php';

    if (!sequra_is_ready())
    {
        return show_alerta2('O pagamento seQura encontra-se temporariamente indisponível. A encomenda foi registada; selecione outra forma de pagamento ou contacte-nos.');
    }

    if (!sequra_sandbox_order_access_allowed($idEncomenda))
    {
        sequra_log($idEncomenda, sequra_order_reference($idEncomenda), '', '', 'SANDBOX_FORBIDDEN', 'Encomenda fora do grupo de testes.');
        return show_alerta2('O pagamento seQura de testes não está disponível para esta encomenda. A encomenda ficou registada; selecione outra forma de pagamento ou contacte-nos.');
    }

    $payload = sequra_get_order_payload($idEncomenda);
    if (!$payload)
    {
        sequra_log($idEncomenda, sequra_order_reference($idEncomenda), '', '', 'CHECKOUT_ERROR', 'Encomenda local não encontrada.');
        return show_alerta2('Não foi possível preparar o pagamento seQura. A encomenda ficou registada; contacte-nos ou selecione outra forma de pagamento.');
    }

    $result = sequra_create_order($payload);
    $checkout_url = isset($result['response']['checkout_url']) ? trim($result['response']['checkout_url']) : '';
    sequra_log(
        $idEncomenda,
        sequra_order_reference($idEncomenda),
        $result['request'],
        $result['response_raw'],
        $checkout_url !== '' ? 'CHECKOUT_CREATED' : 'CHECKOUT_ERROR',
        $result['curl_error']
    );
    if ($checkout_url === '')
    {
        return show_alerta2('Não foi possível contactar a seQura. A encomenda ficou registada; tente novamente ou selecione outra forma de pagamento.');
    }

    unset($_SESSION['CLIENTE_ID_TEMP']);
    $safe_url = htmlspecialchars($checkout_url, ENT_QUOTES, 'UTF-8');
    $html = show_alerta2(
        getLinguas('key_shop_msg1', $GLOBALS['site_lang']) .
        '<br><br>A reencaminhar para o formulário seguro da seQura...' .
        '<br><br><a href="' . $safe_url . '">Clique aqui se o redirecionamento não acontecer.</a>'
    );
    $html .= '<script>window.location.replace(' . json_encode($checkout_url) . ');</script>';
    return $html;
}

function getMetodoPagamentoTransBancariaGateway($idMetodo, $totalEncomenda, $idEncomenda)
{
    $html='';
    $rs=getMetodoPagamentoById($idMetodo);

    if(!$rs->EOF())
	{
		// echo 2;
		$message_mb='<div id="transf" style="width: 60%; margin: 0px auto; text-align: center; padding: 10px;"><span style="color:#000000">Dados para transferência</span><br><br>';
		$message_mb2='<div id="transf" style="width: 100%; margin: 0px auto; text-align: left; padding: 0;"><span style="color:#000000">Dados para transferência</span><br><br>';
        if($rs->db_data['nib'] != "")
		{
            $message_mb .= ''.$rs->db_data['nib'].'<br><br>';
            $message_mb2 .= ''.$rs->db_data['nib'].'<br><br>';
        }
        $message_mb .= 'Total: '.numberformat($totalEncomenda).'<br /></div><button onclick="ImprimeTransf(\'transf\')">'.getLinguas('key_imp',$GLOBALS['site_lang']).'</button>';
        $message_mb2 .= 'Total: '.numberformat($totalEncomenda).'</div>';
        $html .= '<script type="text/javascript">';
        $html .= 'function ImprimeTransf(strid) {';
        $html .= 'var prtContent=document.getElementById(strid);';
        $html .= 'var WinPrint=window.open(\'\', \'\', \'left=-10,top=-10,width=0,height=0,toolbar=0,scrollbars=0,status=0\');';
        $html .= 'WinPrint.document.write(prtContent.innerHTML);';
        $html .= 'WinPrint.document.close();';
        $html .= 'WinPrint.focus();';
        $html .= 'WinPrint.print();';
        $html .= 'WinPrint.close();';
        $html .= '}';
        $html .= '</script>';
    }
    $rs->db_close();
	if($idEncomenda > 0)
	{
		$db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
		$sql='UPDATE encomendas SET enc_pagamento_dados="'.addslashes($message_mb2).'" WHERE ID='.$idEncomenda;
		Runquery($db_link,$GLOBALS['DB_name'], $sql);
	}
    return $html;
}
function getMetodoPagamentoUnicreGateway($totalEncomenda, $idEncomenda)
{
    $sandbox=1;

    $html='';
    unset($_SESSION['CLIENTE_ID_TEMP']);

    $db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);

    // Encomenda
    $sql = 'SELECT * FROM encomendas WHERE ID="'.$idEncomenda.'"';
    $rs = new DataObj;
    $rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
    if($rs->EOF())
    {
        die();
    }
    else
    {
        $enc_Nome=$rs->db_data['enc_Nome'];
        $enc_Apelido=$rs->db_data['enc_Apelido'];
        $enc_Morada=$rs->db_data['enc_Morada'];
        $enc_Cidade=$rs->db_data['enc_Cidade'];
        
        $Post_Code=$rs->db_data['enc_Codigo_Postal'];
        $E_Mail=$rs->db_data['enc_Email'];
        $enc_id=$rs->db_data['ID'];
        $Valor=$rs->db_data['TotalDocComIva'];
    }
    $rs->db_close();

    $sql = 'INSERT INTO pagamentos_unicre(Tipo,ID_ENC,Estado,CDate,Valor,Resposta1) VALUES (1,"'.$enc_id.'",0,"'.date("Y-m-d H:i:s").'","'.$Valor.'","")';
    $res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
    $pagamento_id=db_get_last_insert_id($db_link);
    
    $sql="INSERT INTO sibs_visa_logs (ID_Pagamento, Data, Json, Estado, Erros) VALUES('".$pagamento_id."','".date("Y-m-d H:m:i")."', 'Tel = $m_telemovel | Valor = $valor_pagar', '0','')";
    $res = Runquery($db_link,$GLOBALS['DB_name'], $sql);
    $ID_Log=db_get_last_insert_id($db_link);
    $Valor=number_format($Valor,2,'.','');
    $Country='PT';

    if($sandbox==1)
    {
        //Credenciais de Teste:
        $authentication_entityId = '8ac7a4c97b519e7c017b53a2a54a0eb8';
        $Bearer = 'OGFjN2E0Yzk3NGQ0Yjg4YjAxNzRkOGI4MWMzNTA2Zjh8OFI4Y3F6emNXbg==';
        $Entidade = '23867';

        if($Sibs_Visa_checkoutId !='')
        {
            $url = "https://test.oppwa.com/v1/checkouts";
            $data = "entityId=$authentication_entityId".
                    "&amount=$Valor".
                    "&currency=EUR".
                    "&billing.city=$enc_Cidade".
                    "&billing.country=$Country".
                    "&billing.street1=".urlencode($enc_Morada).
                    "&billing.postcode=$Post_Code".
                    "&testMode=EXTERNAL".
                    "&customParameters[SIBS_ENV]=QLY".
                    "&customer.email=$E_Mail".
                    "&paymentType=DB".
                    "&registrations[0].id=$Sibs_Visa_checkoutId";
        }
        else
        {
            //Trocar email pelo do cliente e a morada
            $url = "https://test.oppwa.com/v1/checkouts";
            $data = "entityId=$authentication_entityId".
                    "&amount=$Valor".
                    "&currency=EUR".
                    "&billing.city=$enc_Cidade".
                    "&billing.country=$Country".
                    "&billing.street1=".urlencode($enc_Morada).
                    "&billing.postcode=$Post_Code".
                    "&testMode=EXTERNAL".
                    "&customParameters[SIBS_ENV]=QLY".
                    "&customer.email=$E_Mail".
                    "&createRegistration=false".
                    "&merchantTransactionId=$id_enc" .
                    "&paymentType=DB";
        }
    }
    else
    {   
        //Credenciais de producao:
        $authentication_entityId = '8ac7a4c97b519e7c017b53a2a54a0eb8';
        $Bearer = 'OGFjN2E0Yzk3NGQ0Yjg4YjAxNzRkOGI4MWMzNTA2Zjh8OFI4Y3F6emNXbg==';
        $Entidade = '25802';


        if($Sibs_Visa_checkoutId !='')
        {
            $url = "https://oppwa.com/v1/checkouts";
            $data = "entityId=$authentication_entityId".
                    "&amount=$Valor".
                    "&currency=EUR".
                    "&billing.city=$enc_Cidade".
                    "&billing.country=$Country".
                    "&billing.street1=".urlencode($enc_Morada).
                    "&billing.postcode=$Post_Code".
                    "&customer.email=$E_Mail".
                    "&merchantTransactionId=$id_enc" .
                    "&paymentType=DB".
                    "&registrations[0].id=$Sibs_Visa_checkoutId";
        }
        else
        {
            //Trocar email pelo do cliente e a morada
            $url = "https://oppwa.com/v1/checkouts";
            $data = "entityId=$authentication_entityId".
                    "&amount=$Valor".
                    "&currency=EUR".
                    "&billing.city=$enc_Cidade".
                    "&billing.country=$Country".
                    "&billing.street1=".urlencode($enc_Morada).
                    "&billing.postcode=$Post_Code".
                    "&customer.email=$E_Mail".
                    "&merchantTransactionId=$id_enc" .
                    "&createRegistration=false".
                    "&paymentType=DB";
        }
    }
    //---------------------------------

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                   'Authorization:Bearer '.$Bearer));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $responseData = curl_exec($ch);
    if(curl_errno($ch)) {
        //return curl_error($ch);
    }
    curl_close($ch);
    $resposta=json_decode($responseData,true);

    $sql = 'UPDATE pagamentos_unicre SET Resposta1="'.addslashes(serialize($resposta)).'",checkoutId="'.$resposta['id'].'" WHERE ID = '.$pagamento_id;
    $res = Runquery($db_link,$GLOBALS['DB_name'],$sql);

    echo '
        <style>
        .login-form {
          max-width: 350px;
          width: 80%;
          margin: 0 auto;
          position: relative;
          z-index: 2;
          background: #ccc;
          background-color: rgb(255, 255, 255);
        }        
        </style>    
    <div id="overlay-login" style="min-height: 628px; height: 628px; display: block;">
        <div class="header-out" style="position:absolute"><div class="header" id="header2_login"></div></div>';
    $html .= '
    <style>
        .wpwl-label{font-size:12px;line-height:14px}
        .wpwl-button{border-radius:9px}
        option{}
        .wpwl-form {margin:0;padding: 10px;text-align: left;font-size:12px;line-height:14px}

        .wpwl-button-pay {
              float: none;
              margin: 0 auto;
              position: relative;
              display: block;
            }

        #overlay-login
        {
            min-height: auto !important;
            height: auto !important;
        }
    </style>';




    $html .= '
    <div id="home_not_logado" class="login-box" style="margin-top:10px">

            <div style="padding:10px;max-width:900px;margin: 0 auto">
                <b>Encomenda registada com sucesso</b>
                <br>Deverá agora preencher os dados relativos ao seu cartão para prosseguir com o pagamento da sua encomenda.
                <br>Pode confirmar mais abaixo o resumo da sua encomenda caso tenha alguma dúvida.<br>
            </div>
            <br>

        <div class="login-form" style="background-color:transparent">

            <div style="width:100%;background-color:#fff;text-align:center;padding:0px;border-radius:0px;margin-top:0px;border:solid 1px #ccc">';

            $html .= '<img src="'.SITE_HTTP_URL.'external_api/pagamentos/pgm_unicre/images/checkoutunicre.png">';

            //------------------------------------
            $html .= '
            <script>
                var wpwlOptions = {
                    style:"plain",
                    onReady: function(e){
                        $(".wpwl-label-brand").html("Card Brand");
                        $(".wpwl-form-card").find(".wpwl-button-pay").on("click", function(e){validateHolder(e);});
                    },
                    onBeforeSubmitCard: function(e){return validateHolder(e);},
                    locale:"pt",
                    paymentTarget:"_top",           
                    registrations: {
                        requireCvv: true
                    },
                    iframeStyles:{
                        "card-number-placeholder":{
                            "color":"#ff0000",
                            "font-size":"14px",
                            "font-family":"Titillium Web"
                        },
                            "cvv-placeholder":{
                            "color":"#0000ff",
                                "font-size":"14px",
                                "font-family":"Titillium Web"
                        }
                    }           
                }
                function validateHolder(e)
                {
                  var holder = $(".wpwl-control-cardHolder").val();
                  if (holder.trim().length < 2)
                  {
                    $(".wpwl-control-cardHolder").addClass("wpwl-has-error").after("<div class=\'wpwl-hint wpwl-hint-cardHolderError\'>Invalid card holder</div>");
                    return false;
                  }
                  return true;
                }

            </script>';

            if($sandbox==1)
            {
                if($Sibs_Visa_checkoutId=='')
                {
                    $html .= '<script src="https://test.oppwa.com/v1/paymentWidgets.js?checkoutId='.$resposta['id'].'"></script>';
                }
                else
                {
                    $html .= '<script src="https://test.oppwa.com/v1/paymentWidgets.js?checkoutId='.$resposta['id'].'"></script>';
                }
            }
            else
            {       
                if($Sibs_Visa_checkoutId=='')
                {
                    $html .= '<script src="https://oppwa.com/v1/paymentWidgets.js?checkoutId='.$resposta['id'].'"></script>';
                }
                else
                {
                    $html .= '<script src="https://oppwa.com/v1/paymentWidgets.js?checkoutId='.$resposta['id'].'"></script>';
                }
            }
            $html .= '<form action="'.SITE_HTTP_URL.'external_api/pagamentos/pgm_unicre/ipn_visa_result.php?r='.$r.'#makeHolderMandatory" class="paymentWidgets" data-brands="VISA MASTER"></form>';


            //------------------------------------


        $html .='VALOR A PAGAMENTO: '.numberformat($Valor);

    $html .= '</div>';

            /*<div style="width:100%;text-align:center;padding:10px;border-radius:12px;border-bottom-right-radius:0px;margin-top:30px">               
                <input class="button3" type="button" name="bt-login1" value="CANCELAR" onmouseup="window.open(\''.SITE_HTTP_URL.'external_api/pagamentos/pgm_unicre/ipn_visa_result.php\',\'_self\');">
            </div>*/

    $html .= '
        </div>      
        </div>
        </div>
        <div style="margin-top:30px;width:100%;display:table;position:relative">';


    $enc_state=estado_encomenda($estado,$GLOBALS['site_lang']);
    $html .=showInfoEncomenda($idEncomenda,'800',1,$enc_state['texto']);

    $html .= '<br>';
    $html .= '</div>';

    /*$html='';
    unset($_SESSION['CLIENTE_ID_TEMP']);
    $html .= '<form action="'.SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_UNICRE]['url'].'doWebPayment.php" method="post" name="unicreform">';
    $html .= '<input type="hidden" name="ref" id="ref" value="'.$idEncomenda.'">';
    $html .= '<input type="hidden" name="amount" id="amount" value="'.intval(floatval($totalEncomenda) * 100).'">';
    $html .= '<input type="hidden" name="currency" id="currency" value="978">';
    $html .= '</form>';
    $pp='Dentro de momento será reencaminhado para a unicre para fazer o pagamento, Se não for reencaminhado <a href="javascript:;" onclick=\'document.unicreform.submit();\'><u>clique aqui</u></a><br/><img src="'.SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_UNICRE]['url'].'images/checkoutunicre.png" border=0>';
    $html .= show_alerta2(getLinguas('key_shop_msg1',$GLOBALS['site_lang']).'<br /><br /><span style="color:#4f7e9e">'.$pp.'</span><br /><br />');
    $html .= '<script>setTimeout(function(){document.unicreform.submit();},5000);</script>';
    return $html;*/
    return $html;
}
function getMetodoPagamentoCobrancaGateway()
{
    $html=show_alerta2('<img src="layout/'.PROJETO.'/images/icon-submit-cart.png" />');
    return $html;
}
function getMetodoPagamentoEmLoja()
{
    $html=show_alerta2('<img src="layout/'.PROJETO.'/images/icon-submit-cart.png" />');
    return $html;
}
function getMetodoPagamentoMBIfthenGateway($idEncomenda)
{
    $html=show_alerta2(getLinguas('key_shop_msg1',$GLOBALS['site_lang']).'<br /><br />');
    $html .= '<div id="mb_result"></div>';
    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') $html=file_get_contents(SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_MB_IFTHEN]['url'] .'mb_ifthen.php?id_enc='.$idEncomenda);
    else $html=file_get_contents(SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_MB_IFTHEN]['url'] .'mb_ifthen.php?id_enc='.$idEncomenda);
    return $html;
}
function getMetodoPagamentoPayShopIfthenGateway($idEncomenda)
{
    $html=show_alerta2(getLinguas('key_shop_msg1',$GLOBALS['site_lang']).'<br /><br />');
    $html .= '<div id="mb_result"></div>';

    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') $html=file_get_contents(SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_PAYSHOP_IFTHEN]['url'] .'payshop.php?id_enc='.$idEncomenda);
    else $html=file_get_contents(SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_PAYSHOP_IFTHEN]['url'] .'payshop.php?id_enc='.$idEncomenda);
    return $html;
}
function getMetodoPagamentoVisaIfthenGateway($idEncomenda=0)
{
    $html='';
    unset($_SESSION['CLIENTE_ID_TEMP']);
    $ifthen_url=SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_VISA_IFTHEN]['url'] .'visa.php?id_enc='.$idEncomenda;

    if($GLOBALS['site_lang'] == 'pt')
    {
        $pp='<br /><br />Dentro de momento será reencaminhado para a IFTHEN para fazer o pagamento, Se não for reencaminhado <a style="color:#289BA2;text-decoration:none;" href="javascript:;" onclick=\'window.open("'.$ifthen_url.'","_self")\'>clique aqui</a>';
    }
    else
    {
        $pp='<br /><br />Within moments you will be redirected to IFTHEN to make the payment, if not redirected <a style="color:#289BA2;text-decoration:none;" href="javascript:;" onclick=\'window.open("'.$ifthen_url.'","_self")\'>click here</a>';
    }
    $html .= show_alerta2(getLinguas('key_shop_msg1',$GLOBALS['site_lang']).'<br /><br /><span style="color:#4f7e9e">'.$pp.'</span><br /><br />');
    $html .= '<script>setTimeout(function(){window.open("'.$ifthen_url.'","_self");},5000);</script>';
    return $html;
}
function getMetodoPagamentoVisaEasyPay($idEncomenda=0)
{
    $html='';
    unset($_SESSION['CLIENTE_ID_TEMP']);
    $easypay_url=SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_CC_EASYPAY]['url'] .'easypay.php?metodo=cc&id_enc='.$idEncomenda;

    if($GLOBALS['site_lang'] == 'pt')
    {
        $pp='<br /><br />Dentro de momento será reencaminhado para a easypay para fazer o pagamento, Se não for reencaminhado <a style="color:#289BA2;text-decoration:none;" href="javascript:;" onclick=\'window.open("'.$ifthen_url.'","_self")\'>clique aqui</a>';
    }
    else
    {
        $pp='<br /><br />Within moments you will be redirected to easypay to make the payment, if not redirected <a style="color:#289BA2;text-decoration:none;" href="javascript:;" onclick=\'window.open("'.$ifthen_url.'","_self")\'>click here</a>';
    }
    $html .= show_alerta2(getLinguas('key_shop_msg1',$GLOBALS['site_lang']).'<br /><br /><span style="color:#4f7e9e">'.$pp.'</span><br /><br />');
    $html .= '<script>setTimeout(function(){window.open("'.$easypay_url.'","_self");},5000);</script>';
    return $html;
}
function getMetodoPagamentoMBLusopayGateway($idEncomenda)
{
    $html=show_alerta2(getLinguas('key_shop_msg1',$GLOBALS['site_lang']).'<br /><br />');
    $html .= '<div id="mb_result"></div>';
	if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') $html=file_get_contents(SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_MB_LUSOPAY]['url'] .'mb_lusopay.php?id_enc='.$idEncomenda);
	else $html=file_get_contents(SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_MB_LUSOPAY]['url'] .'mb_lusopay.php?id_enc='.$idEncomenda);
    return $html;
}
function getMetodoPagamentoMBEasypayGateway($idEncomenda)
{
    $html=show_alerta2(getLinguas('key_shop_msg1',$GLOBALS['site_lang']).'<br /><br />');
    $html .= '<div id="mb_result"></div>';
	if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') $html=file_get_contents(SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_MB_EASYPAY]['url'] .'mb_easypay.php?id_enc='.$idEncomenda);
	else $html=file_get_contents(SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_MB_EASYPAY]['url'] .'mb_easypay.php?id_enc='.$idEncomenda);
    return $html;
}
function getMetodoPagamentoHipayGateway($idEncomenda)
{
    $html='';
    unset($_SESSION['CLIENTE_ID_TEMP']);
    $_SESSION['hipay_encid']=$idEncomenda;
    $hipay_url=SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_HIPAY]['url'] .'hipay.php?id_enc='.$idEncomenda;
    if($GLOBALS['site_lang'] == 'pt')
    {
        $pp='<br /><br />Dentro de momento será reencaminhado para o hipay para fazer o pagamento, Se não for reencaminhado <a style="color:#289BA2;text-decoration:none;" href="javascript:;" onclick=\'window.open("'.$hipay_url.'","_self")\'>clique aqui</a>';
    }
    else
    {
        $pp='<br /><br />Within moments you will be redirected to hipay to make the payment, if not redirected <a style="color:#289BA2;text-decoration:none;" href="javascript:;" onclick=\'window.open("'.$hipay_url.'","_self")\'>click here</a>';
    }
    $html .= show_alerta2(getLinguas('key_shop_msg1',$GLOBALS['site_lang']).'<br /><br /><span style="color:#4f7e9e">'.$pp.'</span><br /><br />');
    $html .= '<script>setTimeout(function(){window.open("'.$hipay_url.'","_self");},5000);</script>';
    return $html;
}
function getMetodoPagamentoEasypayGateway($idEncomenda)
{
    $html='';
    unset($_SESSION['CLIENTE_ID_TEMP']);
    $_SESSION['hipay_encid']=$idEncomenda;
    $easypay_url=SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_EASYPAY]['url'] .'easypay.php?id_enc='.$idEncomenda;
    if($GLOBALS['site_lang'] == 'pt')
    {
        $pp='<br /><br />Dentro de momento será reencaminhado para a easypay para fazer o pagamento, Se não for reencaminhado <a style="color:#289BA2;text-decoration:none;" href="javascript:;" onclick=\'window.open("'.$easypay_url.'","_self")\'>clique aqui</a>';
    }
    else
    {
        $pp='<br /><br />Within moments you will be redirected to easypay to make the payment, if not redirected <a style="color:#289BA2;text-decoration:none;" href="javascript:;" onclick=\'window.open("'.$easypay_url.'","_self")\'>click here</a>';
    }
    $html .= show_alerta2(getLinguas('key_shop_msg1',$GLOBALS['site_lang']).'<br /><br /><span style="color:#4f7e9e">'.$pp.'</span><br /><br />');
    $html .= '<script>setTimeout(function(){window.open("'.$easypay_url.'","_self");},5000);</script>';
    return $html;
}
function getMetodoPagamentoMBSibsGateway($idEncomenda)
{
    $html=show_alerta2(getLinguas('key_shop_msg1',$GLOBALS['site_lang']).'<br /><br />');
    $html .= '<div id="mb_result"></div>';

    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') $html=file_get_contents(SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_MB_SIBS]['url'] .'mb_sibs.php?id_enc='.$idEncomenda);
    else $html=file_get_contents(SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_MB_SIBS]['url'] .'mb_sibs.php?id_enc='.$idEncomenda);
    return $html;
}
function getMetodoPagamentoMBeupagoGateway($idEncomenda)
{
    $html=show_alerta2(getLinguas('key_shop_msg1',$GLOBALS['site_lang']).'<br /><br />');
    $html .= '<div id="mb_result"></div>';

    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') $html=file_get_contents(SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_MB_EUPAGO]['url'] .'mb_eupago.php?id_enc='.$idEncomenda);
    else $html=file_get_contents(SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_MB_EUPAGO]['url'] .'mb_eupago.php?id_enc='.$idEncomenda);
    return $html;
}
function getMetodoPagamentoPayShopeupagoGateway($idEncomenda)
{
    $html=show_alerta2(getLinguas('key_shop_msg1',$GLOBALS['site_lang']).'<br /><br />');
    $html .= '<div id="mb_result"></div>';

    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') $html=file_get_contents(SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_PAYSHOP_EUPAGO]['url'] .'payshop.php?id_enc='.$idEncomenda);
    else $html=file_get_contents(SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_PAYSHOP_EUPAGO]['url'] .'payshop.php?id_enc='.$idEncomenda);
    return $html;
}
function getMetodoPagamentoMBCGDGateway($idEncomenda)
{
    $html=show_alerta2(getLinguas('key_shop_msg1',$GLOBALS['site_lang']).'<br /><br />');
    $html .= '<div id="mb_result"></div>';
	if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') $html=file_get_contents(SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_MB_CGD]['url'] .'mb_cgd.php?id_enc='.$idEncomenda);
	else $html=file_get_contents(SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_MB_CGD]['url'] .'mb_cgd.php?id_enc='.$idEncomenda);
    return $html;
}
function getMetodoPagamentoEasyGateway($idEncomenda)
{
    $html='';
    unset($_SESSION['CLIENTE_ID_TEMP']);
    $_SESSION['hipay_encid']=$idEncomenda;
    $easypay_url=SITE_HTTP_URL . $GLOBALS['pgm_metodos'][MP_TP_CC_EASYPAY]['url'] .'easypay.php?id_enc=' . $idEncomenda;
    if ($GLOBALS['site_lang'] == 'pt')
    {
        $pp='<br /><br />Dentro de momento será reencaminhado para o site da easypay para fazer o pagamento, Se não for reencaminhado <a style="color:#289BA2;text-decoration:none;" href="javascript:;" onclick=\'window.open("' . $easypay_url . '","_self")\'>clique aqui</a>';
    }
    else
    {
        $pp='<br /><br />Within moments you will be redirected to easypay to make the payment, if not redirected <a style="color:#289BA2;text-decoration:none;" href="javascript:;" onclick=\'window.open("' . $easypay_url . '","_self")\'>click here</a>';
    }
    $html .= show_alerta2(\getLinguas('key_shop_msg1',$GLOBALS['site_lang']) . '<br /><br /><span style="color:#4f7e9e">' . $pp . '</span><br /><br />');
    $html .= '<script>setTimeout(function(){window.open("' . $easypay_url . '","_self");},5000);</script>';
    return $html;
}

//MBWAY ************************************************************
//MBWAY ************************************************************
//MBWAY ************************************************************
//MBWAY ************************************************************
function getMetodoPagamentoMBWayUnicreGateway($idEncomenda)
{
    $mbway_tel=$_POST['mbway_tel'];
    if(!$mbway_tel)
    {
        return false;
    }
    else
    {
        // Encomenda
        $db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
        $sql = 'SELECT * FROM encomendas WHERE ID="'.$idEncomenda.'"';
        $rs = new DataObj;
        $rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
        if($rs->EOF())
        {
            die();
        }
        else
        {
            $Address=$rs->db_data['enc_morada'];
            $City=$rs->db_data['enc_cidade'];
            $Post_Code=$rs->db_data['enc_Codigo_Postal'];
            $E_Mail=$rs->db_data['enc_Email'];
            $enc_id=$rs->db_data['ID'];
            $Valor=$rs->db_data['TotalDocComIva'];
        }
        $rs->db_close();


        $sql = 'INSERT INTO pagamentos_unicre(Tipo,ID_ENC,Estado,CDate,Valor,Resposta1) VALUES (1,"'.$idEncomenda.'",0,"'.date("Y-m-d H:i:s").'","'.$Valor.'","")';
        $res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
        $pagamento_id=db_get_last_insert_id($db_link);



        $html='<script>
            var myVMBWAY;
            function verifica_mbway(id_enc)
            {
                var dump="'.uniqID().'";

                $.ajax({
                    type: "GET",
                    url: SITE_HTTP_URL+"'.$GLOBALS['pgm_metodos'][MP_TP_MBWAY_UNICRE]['url'].'mbway_status.php",
                    data: { id_enc: id_enc,d:dump }
                }).done(function(resultado)
                {
                    if(resultado > 0)
                    {

                        if(resultado == 1) {$(\'#mb_result\').html("<div style=\"text-align:center;padding:30px;color:#00f;font-size:22px\">Pagamento Cancelado pelo utilizador</div>"); }
                        if(resultado == 2) {$(\'#mb_result\').html("<div style=\"text-align:center;padding:30px;color:#00f;font-size:22px\">User authorization failed</div>"); }
                        if(resultado == 3) {$(\'#mb_result\').html("<div style=\"text-align:center;padding:30px;color:#00f;font-size:22px\">Transaction declined</div>"); }
                        if(resultado == 13){$(\'#mb_result\').html("<div style=\"text-align:center;padding:30px;color:#00f;font-size:22px\">Pagamento recebido com sucesso</div>"); }

                        clearTimeout(myVMBWAY);
                    }
                    else
                    {
                        $(\'#mb_result\').html("Pagamento pendente, deve consultar o seu telemovel para aprovar o pagamento");
                    }

                    return true;
                });
                myVMBWAY=setTimeout(verifica_mbway, 5000, id_enc);
            }
            myVMBWAY=setTimeout(verifica_mbway, 5000,"'.$idEncomenda.'");

            </script>';
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') $html.=file_get_contents(SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_MBWAY_UNICRE]['url'] .'mbway.php?mbway_tel='.$mbway_tel.'&id_enc='.$idEncomenda);
        else $html.=file_get_contents(SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_MBWAY_UNICRE]['url'] .'mbway.php?mbway_tel='.$mbway_tel.'&id_enc='.$idEncomenda);

        $html.='<div class="row notify successbox text-center" style="border: solid 3px #ffe082"><span class="alerticon" style="background:url('.SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_MBWAY_UNICRE]['url'] .'mbway.jpg) no-repeat center;background-size:64px auto"></span><p><div id="mb_result"><b>O pedido de pagamento foi enviado, tem 4 minutos para proceder à confirmação.<br><br></b></div></p></div>';
        return $html;
    }
}
function getMetodoPagamentoMBWaySibsGateway($idEncomenda)
{
    $mbway_tel=$_POST['mbway_tel'];
    if(!$mbway_tel)
    {
        return false;
    }
    else
    {
        
        $html='<script>
            var myVMBWAY;
            function verifica_mbway(id_enc)
            {
                var dump="'.uniqID().'";

                $.ajax({
                    type: "GET",
                    url: SITE_HTTP_URL+"'.$GLOBALS['pgm_metodos'][MP_TP_MBWAY_SIBS]['url'].'mbway_status.php",
                    data: { id_enc: id_enc,d:dump }
                }).done(function(resultado)
                {
                    if(resultado > 0)
                    {

                        if(resultado == 1) {$(\'#mb_result\').html("<div style=\"text-align:center;padding:30px;color:#00f;font-size:22px\">Pagamento Cancelado pelo utilizador</div>"); }
                        if(resultado == 2) {$(\'#mb_result\').html("<div style=\"text-align:center;padding:30px;color:#00f;font-size:22px\">User authorization failed</div>"); }
                        if(resultado == 3) {$(\'#mb_result\').html("<div style=\"text-align:center;padding:30px;color:#00f;font-size:22px\">Transaction declined</div>"); }
                        if(resultado == 13){$(\'#mb_result\').html("<div style=\"text-align:center;padding:30px;color:#00f;font-size:22px\">Pagamento recebido com sucesso</div>"); }

                        clearTimeout(myVMBWAY);
                    }
                    else
                    {
                        $(\'#mb_result\').html("Pagamento pendente, deve consultar o seu telemovel para aprovar o pagamento");
                    }

                    return true;
                });
                myVMBWAY=setTimeout(verifica_mbway, 5000, id_enc);
            }
            </script>';
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') $html.=file_get_contents(SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_MBWAY_SIBS]['url'] .'mbway.php?mbway_tel='.$mbway_tel.'&id_enc='.$idEncomenda);
        else $html.=file_get_contents(SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_MBWAY_SIBS]['url'] .'mbway.php?mbway_tel='.$mbway_tel.'&id_enc='.$idEncomenda);

        $html.='<div class="row notify successbox text-center" style="border: solid 3px #ffe082"><span class="alerticon" style="background:url(external_api/pagamentos/pgm_sibs/mbway/mbway.jpg) no-repeat center;background-size:64px auto"></span><p><div id="mb_result"><b>O pedido de pagamento foi enviado, tem 4 minutos para proceder à confirmação.<br><br></b></div></p></div>';
        return $html;
    }
}
function getMetodoPagamentoMBWAYifthenGateway($idEncomenda)
{
    $mbway_tel=$_POST['mbway_tel'];
    if(!$mbway_tel)
    {
        return false;
    }
    else
    {
        $html='<script>
            var myVMBWAY;
            function verifica_mbway(id_enc)
            {
                var dump="'.uniqID().'";

                $.ajax({
                    type: "GET",
                    url: SITE_HTTP_URL+"'.$GLOBALS['pgm_metodos'][MP_TP_MBWAY_IFTHEN]['url'].'mbway_status.php",
                    data: { id_enc: id_enc,d:dump }
                }).done(function(resultado)
                {
                    if(resultado > 0)
                    {

                        if(resultado == 1) {$(\'#mb_result\').html("<div style=\"text-align:center;padding:30px;color:#00f;font-size:22px\">Pagamento Cancelado pelo utilizador</div>"); }
                        if(resultado == 2) {$(\'#mb_result\').html("<div style=\"text-align:center;padding:30px;color:#00f;font-size:22px\">User authorization failed</div>"); }
                        if(resultado == 3) {$(\'#mb_result\').html("<div style=\"text-align:center;padding:30px;color:#00f;font-size:22px\">Transaction declined</div>"); }
                        if(resultado == 13){$(\'#mb_result\').html("<div style=\"text-align:center;padding:30px;color:#00f;font-size:22px\">Pagamento recebido com sucesso</div>"); }

                        clearTimeout(myVMBWAY);
                    }
                    else
                    {
                        $(\'#mb_result\').html("Deverá receber em breve o pedido de pagamento na sua aplicação MB WAY. Caso não receba nenhuma notificação, verifique o pagamento dentro da aplicação, no separador ATIVIDADE");
                    }

                    return true;
                });
                myVMBWAY=setTimeout(verifica_mbway, 5000, id_enc);
            }
            </script>';
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') $html.=file_get_contents(SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_MBWAY_IFTHEN]['url'] .'mbway.php?mbway_tel='.$mbway_tel.'&id_enc='.$idEncomenda);
        else $html.=file_get_contents(SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_MBWAY_IFTHEN]['url'] .'mbway.php?mbway_tel='.$mbway_tel.'&id_enc='.$idEncomenda);

        $html.='<div class="row notify successbox text-center" style="border: solid 3px #ffe082"><span class="alerticon" style="background:url('.SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_MBWAY_IFTHEN]['url'].'mbway.jpg) no-repeat center;background-size:64px auto"></span><p><div id="mb_result"><b>Deverá receber em breve o pedido de pagamento na sua aplicação MB WAY. No entanto, aconselhamos a abrir já a aplicação na secção de "ATIVIDADE", pois por vezes o telemóvel não recebe nenhuma notificação. O pagamento irá expirar em 5 minutos.<br>Obrigado<br></b></div></p></div>';
        return $html;
    }
}
function getMetodoPagamentoMBWAYeasypay($idEncomenda)
{
    $mbway_tel=$_POST['mbway_tel'];
    if(!$mbway_tel)
    {
        return false;
    }
    else
    {
        $html='<script>
            var myVMBWAY;
            function verifica_mbway(id_enc)
            {
                var dump="'.uniqID().'";
                var metodo="status";

                $.ajax({
                    type: "GET",
                    url: "'.SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_MBWAY_EASYPAY]['url'].'easypay.php",
                    data: { id_enc: id_enc,d:dump,metodo:metodo }
                }).done(function(resultado)
                {
                    if(resultado > 0)
                    {

                        if(resultado == 1) {$(\'#mb_result\').html("<div style=\"text-align:center;padding:30px;color:#00f;font-size:22px\">Pagamento Cancelado pelo utilizador</div>"); }
                        if(resultado == 2) {$(\'#mb_result\').html("<div style=\"text-align:center;padding:30px;color:#00f;font-size:22px\">Pagamento Cancelado</div>"); }
                        if(resultado == 3) {$(\'#mb_result\').html("<div style=\"text-align:center;padding:30px;color:#00f;font-size:22px\">Pagamento Cancelado</div>"); }
                        if(resultado == 13){$(\'#mb_result\').html("<div style=\"text-align:center;padding:30px;color:#00f;font-size:22px\">Pagamento recebido com sucesso</div>"); }

                        clearTimeout(myVMBWAY);
                    }
                    else
                    {
                        $(\'#mb_result\').html("Deverá receber em breve o pedido de pagamento na sua aplicação MB WAY.<br>No entanto, aconselhamos a abrir já a aplicação na secção de "ATIVIDADE", pois por vezes o telemóvel não recebe nenhuma notificação.<br>O pagamento irá expirar em 5 minutos.
Obrigado");
                    }

                    return true;
                });
                myVMBWAY=setTimeout(verifica_mbway, 5000, id_enc);
            }
            myVMBWAY=setTimeout(verifica_mbway, 5000,"'.$idEncomenda.'");
            </script>';
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') $html.=file_get_contents(SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_MBWAY_EASYPAY]['url'] .'easypay.php?metodo=mbw&mbway_tel='.$mbway_tel.'&id_enc='.$idEncomenda);
        else $html.=file_get_contents(SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_MBWAY_EASYPAY]['url'] .'easypay.php?metodo=mbw&mbway_tel='.$mbway_tel.'&id_enc='.$idEncomenda);

        $html.='<div class="row notify successbox text-center" style="border: solid 3px #ffe082"><span class="alerticon" style="background:url('.SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_MBWAY_EASYPAY]['url'].'mbway.jpg) no-repeat center;background-size:64px auto"></span><p><div id="mb_result"><b>Deverá receber em breve o pedido de pagamento na sua aplicação MB WAY. No entanto, aconselhamos a abrir já a aplicação na secção de "ATIVIDADE", pois por vezes o telemóvel não recebe nenhuma notificação. O pagamento irá expirar em 5 minutos.<br>Obrigado<br></b></div></p></div>';
        return $html;
    }
}
function getMetodoPagamentoMBWAYLusopayGateway($idEncomenda)
{
    $mbway_tel=$_POST['mbway_tel'];
    if(!$mbway_tel)
    {
        return false;
    }
    else
    {
        $html=show_alerta2(getLinguas('key_shop_msg1',$GLOBALS['site_lang']).'<br /><br />');
        $html .= '<div id="mb_result"></div>';
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') $html=file_get_contents(SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_MBWAY_LUSOPAY]['url'] .'lusopay.php?mbway_tel='.$mbway_tel.'&id_enc='.$idEncomenda);
        else $html=file_get_contents(SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_MBWAY_LUSOPAY]['url'] .'lusopay.php?mbway_tel='.$mbway_tel.'&id_enc='.$idEncomenda);
        return $html;
    }
}

function getMetodoPagamentoMBWayEupagoGateway($idEncomenda)
{
    $mbway_tel=$_POST['mbway_tel'];
    if(!$mbway_tel)
    {
        return false;
    }
    else
    {
        $html='<script>
            var myVMBWAY;
            function verifica_mbway(id_enc)
            {
                var dump="'.uniqID().'";

                $.ajax({
                    type: "GET",
                    url: SITE_HTTP_URL+"'.$GLOBALS['pgm_metodos'][MP_TP_MBWAY_EUPAGO]['url'].'mbway_status.php",
                    data: { id_enc: id_enc,d:dump }
                }).done(function(resultado)
                {
                    if(resultado > 0)
                    {

                        if(resultado == 1) {$(\'#mb_result\').html("<div style=\"text-align:center;padding:30px;color:#00f;font-size:22px\">Pagamento Cancelado pelo utilizador</div>"); }
                        if(resultado == 2) {$(\'#mb_result\').html("<div style=\"text-align:center;padding:30px;color:#00f;font-size:22px\">User authorization failed</div>"); }
                        if(resultado == 3) {$(\'#mb_result\').html("<div style=\"text-align:center;padding:30px;color:#00f;font-size:22px\">Transaction declined</div>"); }
                        if(resultado == 13){$(\'#mb_result\').html("<div style=\"text-align:center;padding:30px;color:#00f;font-size:22px\">Pagamento recebido com sucesso</div>"); }

                        clearTimeout(myVMBWAY);
                    }
                    else
                    {
                        $(\'#mb_result\').html("Pagamento pendente, deve consultar o seu telemovel para aprovar o pagamento");
                    }

                    return true;
                });
                myVMBWAY=setTimeout(verifica_mbway, 5000, id_enc);
            }
            </script>';
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') $html.=file_get_contents(SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_MBWAY_EUPAGO]['url'] .'mbway.php?mbway_tel='.$mbway_tel.'&id_enc='.$idEncomenda);
        else $html.=file_get_contents(SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_MBWAY_EUPAGO]['url'] .'mbway.php?mbway_tel='.$mbway_tel.'&id_enc='.$idEncomenda);

        $html.='<div class="row notify successbox text-center" style="border: solid 3px #ffe082"><span class="alerticon" style="background:url(external_api/pagamentos/pgm_sibs/mbway/mbway.jpg) no-repeat center;background-size:64px auto"></span><p><div id="mb_result"><b>O pedido de pagamento foi enviado, tem 4 minutos para proceder à confirmação.<br><br></b></div></p></div>';
        return $html;
    }
}
function getMetodoPagamentoMBWayCGDGateway($idEncomenda)
{
    $mbway_tel=$_POST['mbway_tel'];
    if(!$mbway_tel)
    {
        return false;
    }
    else
    {
        $html='<script>
            var myVMBWAY;
            function verifica_mbway(id_enc)
            {
                var dump="'.uniqID().'";

                $.ajax({
                    type: "GET",
                    url: SITE_HTTP_URL+"'.$GLOBALS['pgm_metodos'][MP_TP_MBWAY_CGD]['url'].'mbway_status.php",
                    data: { id_enc: id_enc,d:dump }
                }).done(function(resultado)
                {
                    if(resultado > 0)
                    {

                        if(resultado == 1) {$(\'#mb_result\').html("<div style=\"text-align:center;padding:30px;color:#00f;font-size:22px\">Pagamento Cancelado pelo utilizador</div>"); }
                        if(resultado == 2) {$(\'#mb_result\').html("<div style=\"text-align:center;padding:30px;color:#00f;font-size:22px\">User authorization failed</div>"); }
                        if(resultado == 3) {$(\'#mb_result\').html("<div style=\"text-align:center;padding:30px;color:#00f;font-size:22px\">Transaction declined</div>"); }
                        if(resultado == 13){$(\'#mb_result\').html("<div style=\"text-align:center;padding:30px;color:#00f;font-size:22px\">Pagamento recebido com sucesso</div>"); }

                        clearTimeout(myVMBWAY);
                    }
                    else
                    {
                        $(\'#mb_result\').html("Pagamento pendente, deve consultar o seu telemovel para aprovar o pagamento");
                    }

                    return true;
                });
                myVMBWAY=setTimeout(verifica_mbway, 5000, id_enc);
            }
            </script>';
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') $html.=file_get_contents(SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_MBWAY_CGD]['url'] .'mbway.php?mbway_tel='.$mbway_tel.'&id_enc='.$idEncomenda);
        else $html.=file_get_contents(SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_MBWAY_CGD]['url'] .'mbway.php?mbway_tel='.$mbway_tel.'&id_enc='.$idEncomenda);

        $html.='<div class="row notify successbox text-center" style="border: solid 3px #ffe082"><span class="alerticon" style="background:url(external_api/pagamentos/pgm_sibs/mbway/mbway.jpg) no-repeat center;background-size:64px auto"></span><p><div id="mb_result"><b>O pedido de pagamento foi enviado, tem 4 minutos para proceder à confirmação.<br><br></b></div></p></div>';
        return $html;
    }
}
function getMetodoPagamentoVivawalletGateway($idEncomenda)
{
    $html='';
    unset($_SESSION['CLIENTE_ID_TEMP']);
    $html .= '<form action="'.SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_CC_VIVAWALLET]['url'].'vivawallet.php" method="post" name="vivawalletform">';
    $html .= '<input type="hidden" name="idEncomenda" id="idEncomenda" value="'.$idEncomenda.'">';
    $html .= '</form>';
    $pp='Dentro de momento será reencaminhado para a Vivawallet para fazer o pagamento, Se não for reencaminhado <a href="javascript:;" onclick=\'document.vivawalletform.submit();\'><u>clique aqui</u></a><br/><img src="'.SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_CC_VIVAWALLET]['url'].'vivawallet.gif" border=0>';
    $html .= show_alerta2(getLinguas('key_shop_msg1',$GLOBALS['site_lang']).'<br /><br /><span style="color:#4f7e9e">'.$pp.'</span><br /><br />');
    $html .= '<script>setTimeout(function(){document.vivawalletform.submit();},5000);</script>';
    return $html;
}
function getMetodoPagamentoOneyGateway($idEncomenda)
{
    $html='';
    unset($_SESSION['CLIENTE_ID_TEMP']);
    $html .= '<form action="'.SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_ONEY]['url'].'process.php" method="post" name="oneyform">';
    $html .= '<input type="hidden" name="idEncomenda" id="idEncomenda" value="'.$idEncomenda.'">';
    $html .= '</form>';
    $pp='Dentro de momento será reencaminhado para a Oney, Se não for reencaminhado <a href="javascript:;" onclick=\'document.oneyform.submit();\'><u>clique aqui</u></a><br/><img src="'.SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_ONEY]['url'].'oney.gif" border=0>';
    $html .= show_alerta2(getLinguas('key_shop_msg1',$GLOBALS['site_lang']).'<br /><br /><span style="color:#4f7e9e">'.$pp.'</span><br /><br />');
    $html .= '<script>setTimeout(function(){document.oneyform.submit();},5000);</script>';
    return $html;
}
function getMetodoPagamentoOneyGateway_4x($idEncomenda)
{
    $html='';
    unset($_SESSION['CLIENTE_ID_TEMP']);
    $html .= '<form action="'.SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_ONEY4]['url'].'process.php" method="post" name="oneyform">';
    $html .= '<input type="hidden" name="idEncomenda" id="idEncomenda" value="'.$idEncomenda.'">';
    $html .= '</form>';
    $pp='Dentro de momento será reencaminhado para a Oney, Se não for reencaminhado <a href="javascript:;" onclick=\'document.oneyform.submit();\'><u>clique aqui</u></a><br/>';
    $html .= show_alerta2(getLinguas('key_shop_msg1',$GLOBALS['site_lang']).'<br /><br /><span style="color:#4f7e9e">'.$pp.'</span><br /><br />');
    $html .= '<script>setTimeout(function(){document.oneyform.submit();},5000);</script>';
    return $html;
}

/*function getMetodoPagamentoVivawalletGateway($idEncomenda)
{
    $card_number=$_POST['card-number'.MP_TP_CC_VIVAWALLET];
    $card_cvc=$_POST['card-cvc'.MP_TP_CC_VIVAWALLET];
    $card_expiry_month=$_POST['card-expiry-month'.MP_TP_CC_VIVAWALLET];
    $card_expiry_year=$_POST['card-expiry-year'.MP_TP_CC_VIVAWALLET];
    if(!$card_number)
    {
        return false;
    }
    else
    {
        $html='<script>
            var myVIVAWALLET;
            function verifica_vivawallet(id_enc)
            {
                var dump="'.uniqID().'";

                $.ajax({
                    type: "GET",
                    url: SITE_HTTP_URL+"'.$GLOBALS['pgm_metodos'][MP_TP_CC_VIVAWALLET]['url'].'status.php",
                    data: { id_enc: id_enc,d:dump }
                }).done(function(resultado)
                {
                    if(resultado > 0)
                    {

                        if(resultado == 1) {$(\'#mb_result\').html("<div style=\"text-align:center;padding:30px;color:#00f;font-size:22px\">Pagamento Cancelado pelo utilizador</div>"); }
                        if(resultado == 2) {$(\'#mb_result\').html("<div style=\"text-align:center;padding:30px;color:#00f;font-size:22px\">User authorization failed</div>"); }
                        if(resultado == 3) {$(\'#mb_result\').html("<div style=\"text-align:center;padding:30px;color:#00f;font-size:22px\">Transaction declined</div>"); }
                        if(resultado == 13){$(\'#mb_result\').html("<div style=\"text-align:center;padding:30px;color:#00f;font-size:22px\">Pagamento recebido com sucesso</div>"); }

                        clearTimeout(myVIVAWALLET);
                    }
                    else
                    {
                        $(\'#mb_result\').html("Pagamento em aprovação, aguarde...");
                    }

                    return true;
                });
                myVIVAWALLET=setTimeout(verifica_vivawallet, 5000, id_enc);
            }
            </script>';
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') $html.=file_get_contents(SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_CC_VIVAWALLET]['url'] .'vivawallet.php?card_number='.$card_number.'&card_cvc='.$card_cvc.'&card_expiry_month='.$card_expiry_month.'&card_expiry_year='.$card_expiry_year.'&id_enc='.$idEncomenda);
        else $html.=file_get_contents(SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_CC_VIVAWALLET]['url'] .'vivawallet.php?card_number='.$card_number.'&card_cvc='.$card_cvc.'&card_expiry_month='.$card_expiry_month.'&card_expiry_year='.$card_expiry_year.'&id_enc='.$idEncomenda);

        $html.='<div class="row notify successbox text-center" style="border: solid 3px #ffe082"><span class="alerticon" style="background:url('.SITE_HTTP_URL.$GLOBALS['pgm_metodos'][MP_TP_CC_VIVAWALLET]['url'].'visa.jpg) no-repeat center;background-size:64px auto"></span><p><div id="mb_result"><b>O pedido de pagamento foi enviado, tem 4 minutos para proceder à confirmação.<br><br></b></div></p></div>';
        return $html;
    }
}*/

?>
