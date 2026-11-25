<?php

$rma=intval($_GET['rma'] ?? 0);

ini_set('max_execution_time','60000');
    ini_set('max_input_time','60000');
    ini_set('memory_limit','-1');
    set_time_limit(0);
    $path = '../../core/';
    include $path.'core_files.php';
    include $path.'admin_globals.php';
    require_once __DIR__.'/../signature_helpers.php';

$id=$rma;
$tipo=1;
$html = '';
$software_arr = array();
$hardware_arr = array();
$signature_csrf = rma_signature_csrf_token();

$contactos_lojas_maia = '
<a style="text-decoration:none; font-weight:bold; font-size:15pt;margin-bottom:5mm;color:#000000" href="'.$GLOBALS['site_http_url'].'">DotBright · telecomunicações</a><br>
Avenida Visconde Barreiros 167<br>
4470-151 Maia<br>
Telefone: 229 429 211<br>
E-mail: <a style="text-decoration:none;color:#000000" href="mailto:lojaonline@dotbright.pt">lojaonline@dotbright.pt</a><br>';

$contactos_lojas_matosinhos = '
<a style="text-decoration:none; font-weight:bold; font-size:15pt;margin-bottom:5mm;color:#000000" href="'.$GLOBALS['site_http_url'].'">DotBright · telecomunicações</a><br>
Rua Roberto Ivens, 879<br>
4450-256 Matosinhos<br>
Telefone: 220 142 941<br>
E-mail: <a style="text-decoration:none;color:#000000" href="mailto:lojaonline@dotbright.pt">lojaonline@dotbright.pt</a><br>';

$loja = $contactos_lojas_maia;

$_folhas=1; //TODO
global $DB_host, $DB_user, $DB_pass, $DB_name;
$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
$rs = new DataObj;
$rs2 = new DataObj;
$rs->db_open($db_link, $DB_name, 'SELECT * FROM rmas WHERE ID="'.$id.'"');
if(!$rs || $rs->EOF()) {
    rma_signature_error('A RMA indicada não existe ou já não está disponível.', 404);
}
if($rs && !$rs->EOF())
{
    $IDb = $rs->db_data['ID'];
    $ID_Cliente = $rs->db_data['ID_Cliente'];
    $Cliente_Nome = $rs->db_data['cliente_nome'].' '.$rs->db_data['cliente_sobrenome'].' '.$rs->db_data['cliente_apelido'];
    $Cliente_Morada = $rs->db_data['cliente_morada'];
    $Cliente_CP = $rs->db_data['cliente_cp'];
    $Cliente_Email = $rs->db_data['cliente_username'];
    $Cliente_Telefone = $rs->db_data['cliente_telefone1'].($rs->db_data['cliente_telefone2'] != '' ? ' / '.$rs->db_data['cliente_telefone2'] : '');
    $Cliente_Empresa = $rs->db_data['cliente_empresa'];
    $m_origem = $rs->db_data['m_origem'];

    $dataURI = $rs->db_data['signed2'];
    $signed_date = $rs->db_data['signed2_date'];


    $loja = '';
    // ONLINE || MAIA
    if($rs->db_data['m_origem'] == 1 || $rs->db_data['m_origem'] == 3)
    {
        $loja = $contactos_lojas_maia;
    }
    elseif($rs->db_data['m_origem'] == 2)
    {
        $loja = $contactos_lojas_matosinhos;
    }

    // $ID_Tecnico = $rs->db_data['ID_Tecnico'];
    $m_imei = $rs->db_data['m_imei'];
    $marca = $rs->db_data['m_marca'];
    $modelo = $rs->db_data['m_modelo'];
    $tipoequip = $rs->db_data['TipoEquip'];
    $nserie = $rs->db_data['m_nserie'];
    $avaria = stripslashes($rs->db_data['m_avaria']);
    $m_estado_equipamento = $rs->db_data['m_estado_equipamento'];
    $m_estado_equipamento_text = $rs->db_data['m_estado_equipamento_text'];
    $estadoequip = '';
    if(!$m_estado_equipamento || $m_estado_equipamento==0) $estadoequip = 'Riscos ligeiros';
    if($m_estado_equipamento == 1) $estadoequip = 'Alguns riscos e mossas';
    if($m_estado_equipamento == 2) $estadoequip = 'Muitos riscos e mossas';
    if($m_estado_equipamento == 3) $estadoequip = 'Muito danificado';
    $estadoequip .= '<br>'.$m_estado_equipamento_text;


    $resumo = array();
    $tipo_reparacao_po = ($rs->db_data['tipo_reparacao_po']==1 ? 'Pedido de orçamento' : '');
    if($tipo_reparacao_po) $resumo[] = $tipo_reparacao_po;
    $tipo_reparacao_gl = ($rs->db_data['tipo_reparacao_gl']==1 ? 'Garantia de loja' : '');
    if($tipo_reparacao_gl) $resumo[] = $tipo_reparacao_gl;
    $tipo_reparacao_gf = ($rs->db_data['tipo_reparacao_gf']==1 ? 'Garantia do fabricante' : '');
    if($tipo_reparacao_gf) $resumo[] = $tipo_reparacao_gf;
    $tipo_reparacao_gr = ($rs->db_data['tipo_reparacao_gr']==1 ? 'Garantia de reparação' : '');
    if($tipo_reparacao_gr) $resumo[] = $tipo_reparacao_gr;
    $ndocumento = ($rs->db_data['ndocumento'] != 'insira o numero de documento' ? $rs->db_data['ndocumento'] : '');
    if($ndocumento) $resumo[] = $ndocumento;



    $software_dr=$rs->db_data['software_dr'];
    $software_rc=$rs->db_data['software_rc'];
    $software_na=$rs->db_data['software_na'];
    $software_cg=$rs->db_data['software_cg'];
    $software_ci=$rs->db_data['software_ci'];
    $software_bl=$rs->db_data['software_bl'];
    $hardware_tp=$rs->db_data['hardware_tp'];
    $hardware_tnf=$rs->db_data['hardware_tnf'];
    $hardware_lp=$rs->db_data['hardware_lp'];
    $hardware_lpb=$rs->db_data['hardware_lpb'];
    $hardware_nc=$rs->db_data['hardware_nc'];
    $hardware_ssa=$rs->db_data['hardware_ssa'];
    $hardware_sa=$rs->db_data['hardware_sa'];
    $hardware_sam=$rs->db_data['hardware_sam'];
    $hardware_sal=$rs->db_data['hardware_sal'];
    $hardware_saa=$rs->db_data['hardware_saa'];
    $hardware_nl=$rs->db_data['hardware_nl'];
    $hardware_ncrg=$rs->db_data['hardware_ncrg'];
    $hardware_ncrw=$rs->db_data['hardware_ncrw'];
    $hardware_svl=$rs->db_data['hardware_svl'];
    $hardware_cfnf=$rs->db_data['hardware_cfnf'];
    $hardware_cpnf=$rs->db_data['hardware_cpnf'];
    $hardware_bhn=$rs->db_data['hardware_bhn'];
    $hardware_bvn=$rs->db_data['hardware_bvn'];
    $hardware_bsn=$rs->db_data['hardware_bsn'];
    $hardware_bof=$rs->db_data['hardware_bof'];
    $hardware_caj=$rs->db_data['hardware_caj'];
    $hardware_lsim=$rs->db_data['hardware_lsim'];
    $hardware_lmsd=$rs->db_data['hardware_lmsd'];
    $hardware_lc=$rs->db_data['hardware_lc'];
    $hardware_sp=$rs->db_data['hardware_sp'];
    $preorcamento = ($rs->db_data['preorcamento'] > 0  ? number_format($rs->db_data['preorcamento'],2).' €' : '');


    $acessorios = $rs->db_data['m_acessorios'];
    $observacoestec = $rs->db_data['ObservacoesTecnico'];
    $relintervencao = $rs->db_data['RelatorioIntervencao'];
    $DataInsert = $rs->db_data['DataInsert'];
    $Estado = $rs->db_data['m_estado'];


    if($software_dr) $software_arr[] = 'Desbloqueio à REDE';
    if($software_rc) $software_arr[] = 'Remover Código de Segurança';
    if($software_na) $software_arr[] = 'Não arranca';
    if($software_cg) $software_arr[] = 'Remover Conta Google';
    if($software_ci) $software_arr[] = 'Remover Conta iCloud';
    if($software_bl) $software_arr[] = 'Bloqueia / está lento';


    if($hardware_tp) $hardware_arr[] = 'Touch partido';
    if($hardware_tnf) $hardware_arr[] = 'Touch não funciona';
    if($hardware_lp) $hardware_arr[] = 'LCD partido';
    if($hardware_lpb) $hardware_arr[] = 'LCD em branco/preto';
    if($hardware_nc) $hardware_arr[] = 'Não carrega';
    if($hardware_ssa) $hardware_arr[] = 'Sem sinal áudio no auscultador';
    if($hardware_sa) $hardware_arr[] = 'Sinal áudio no auscultador baixo ou com Ruído';
    if($hardware_sam) $hardware_arr[] = 'Sem sinal áudio no microfone';
    if($hardware_sal) $hardware_arr[] = 'Sem sinal áudio no altifalante';
    if($hardware_saa) $hardware_arr[] = 'Sinal áudio no altifalante baixo ou com Ruído';
    if($hardware_nl) $hardware_arr[] = 'Não Liga';
    if($hardware_ncrg) $hardware_arr[] = 'Não capta REDE GSM';
    if($hardware_ncrw) $hardware_arr[] = 'Não capta REDE WIFI/bluetooth';
    if($hardware_svl) $hardware_arr[] = 'Lente LCD partido(LCD OK) 7 dias';
    if($hardware_cfnf) $hardware_arr[] = 'Camera frontal não funciona';
    if($hardware_cpnf) $hardware_arr[] = 'Camera principal não funciona';
    if($hardware_bhn) $hardware_arr[] = 'Botão Home não funciona';
    if($hardware_bvn) $hardware_arr[] = 'Botão de Volume não funciona';
    if($hardware_bsn) $hardware_arr[] = 'Botão de Silêncio não funciona';
    if($hardware_bof) $hardware_arr[] = 'Botão ON/OFF não funciona';
    if($hardware_caj) $hardware_arr[] = 'Conector audioJack não funciona';
    if($hardware_lsim) $hardware_arr[] = 'Leitor SIM';
    if($hardware_lmsd) $hardware_arr[] = 'Leitor MicroSD';
    if($hardware_lc) $hardware_arr[] = 'Lente da camera partida';
    if($hardware_sp) $hardware_arr[] = 'Sensor de proximidade';


    $m_padrao = $rs->db_data['m_padrao'];
    $codigo_desbloqueio = ($rs->db_data['codigo_desbloqueio']!=0 && $rs->db_data['codigo_desbloqueio']!='' ? $rs->db_data['codigo_desbloqueio'] : $m_padrao);



    $rs->db_close();
    $html .= '
    <style>body{font-family: Verdana, Geneva, sans-serif; }</style>
    <p style="text-align:center; font-size:14pt; font-weight:bold;margin: 0;">Folha de Recepção</p>
    <table style="width:19cm; height:5cm; border-top:1px solid #000; border-bottom:1px solid #000;">
        <tr>
            <td align="left">
                <img src="'.$GLOBALS['site_http_url'].'layout/'.PROJETO.'/logos/logo_top.png" style="width:6.5cm;margin-top:2mm; margin-bottom:2mm;" ><br>
            </td>

            <td align="right">
                '.$loja.'
            </td>
        </tr>
    </table>
    <table style="width:19cm; border-bottom:1px solid #000;">
        <tr >
            <td><b>Data Recebido:</b></td>
            <td>'.$DataInsert.'</td>
            <td align="right" width="10cm"><b style="font-size:18pt">Tracking: #'."DB".str_pad((int)$IDb, 6, "0", STR_PAD_LEFT).'</b></td>
        </tr>
        <tr>
            <td><b>Cliente:</b></td>
            <td>'.$Cliente_Nome.'</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td><b>Contacto:</b></td>
            <td>'.$Cliente_Telefone.'</p></td>
            <td>&nbsp;</td>
        </tr>
        '.(count($resumo) > 0 ? '
        <tr >
            <td height="0.5cm"><b>Resumo:</b></td>
            <td colspan="2">'.implode(', ', $resumo).'</td>

        </tr>' : '').'
    </table>



    <table style="width:19cm; border-bottom:1px solid #000;">
        <tr>
            <td valing="middle" colspan="4"><b style="font-size:12pt;">Dados do Equipamento:</b></td>'.($m_padrao!='' && $m_padrao!=0 ? '
            <td rowspan="4" width="110">
            <img src="'.$GLOBALS['site_http_url'].'rmas/rmas/pattern.php?t='.$m_padrao.'" border="0" width="110">
            </td>' : '').'
        </tr>
        <tr>
            <td width="1cm"><b>Imei:</b></td>
            <td width="2cm">'.$m_imei.'</td>
            <td width="1cm"><b>Nr. Série:</b></td>
            <td width="2cm">'.$nserie.'</td>
        </tr>
        <tr>
            <td width="1cm"><b>Marca:</b></td>
            <td width="2cm">'.$marca.'</td>
            <td width="1cm"><b>Modelo:</b></td>
            <td width="2cm">'.$modelo.'</td>

        </tr>
        <tr>
            <td width="1cm"><b>Código desbloqueio:</b></td>
            <td width="2cm">'.($codigo_desbloqueio == 0 || $codigo_desbloqueio == '' ? '' : $codigo_desbloqueio).'</td>
            <td width="1cm"><b>Pre Orçamento:</b></td>
            <td width="2cm">'.$preorcamento.'</td>

        </tr>
    </table>
    '.(count($software_arr) > 0  ? '
    <table style="width:19cm;">
        <tr>
            <td height="0.5cm" valing="middle"><b style="font-size:12pt;">Software</b></td>
        </tr>
    </table>
    <table  style="width:19cm; border-bottom:1px solid #000;">
    <tr>
    <td style="font-size:10pt">
        '.implode(', ', $software_arr).'
    </td>
    </tr>
    </table>' : '').'

    '.(count($hardware_arr) > 0 ? '
    <table style="width:19cm;">
        <tr>
            <td height="0.5cm" valing="middle"><b style="font-size:12pt;">Hardware</b></td>
        </tr>
    </table>
    <table  style="width:19cm; border-bottom:1px solid #000;">
    <tr>
    <td style="font-size:10pt">
        '.implode(', ', $hardware_arr).'

    </td>
    </tr>
    </table>' : '').'
    <table style="width:19cm;">
        <tr><td height="0.5cm"><b>Avaria Reportada:</b></td></tr>
        <tr><td style="border: 0.1px solid #000;padding:2mm;font-size:8pt">'.nl2br($avaria).'</td></tr>
    </table>
    <table style="width:19cm;">
        <tr><td height="0.5cm"><b>Estado do Equipamento:</b></td></tr>
        <tr><td style="border: 0.1px solid #000;padding:2mm;font-size:8pt">'.nl2br($estadoequip).'</td></tr>
    </table>
    <table style="width:19cm;">
        <tr><td height="0.5cm"><b>Acessórios</b></td></tr>
        <tr><td style="border: 0.1px solid #000;padding:2mm;font-size:8pt">'.nl2br($acessorios).'</td></tr>
    </table>
    <table style="width:19cm;">
        <tr><td style="border: 0.1px solid #000;padding:2mm; font-size:7pt;">
            É da responsabilidade do cliente efetuar cópias de segurança do sistema, aplicações e restantes dados, bem como desativar qualquer proteção por palavra-passe de forma a possibilitar a reparação, tanto ao nível de software ou hardware . A DotBright, Lda. não se responsabiliza pela perda de quaisquer dados, independentemente do tipo de anomalia do equipamento.<br>
            A garantia da DotBright, Lda. cobre as reparações efetuadas por um período de 1 ano (12 meses), exceto reparações ao nível da Logic Board, cuja garantia é de 6 meses (180 dias).<br>
            A garantia aplica-se apenas aos serviços realizados e não cobre danos causados por má utilização, quedas, líquidos, oxidação, impactos ou outros fatores externos.<br>
            Salvo exceções devidamente acordadas, a DotBright, Lda. fica autorizada a abrir os selos de garantia e proceder à desmontagem dos equipamentos, sempre que necessário para a reparação. Este procedimento pode implicar perda de originalidade, funcionalidades e cobertura de garantia externa do fabricante.<br>
            Os equipamentos reparados devem ser levantados num prazo máximo de 120 dias (seguidos) após conclusão da intervenção. Após este período, os equipamentos poderão ser considerados abandonados, não havendo lugar a reembolso ou compensação.<br>
            Todos os equipamentos são automaticamente reparados caso o custo da reparação não ultrapasse 35 €.<br>
            A DotBright, Lda. informa ainda que, devido a condições adversas que possam ocorrer durante a intervenção técnica, não é possível garantir o sucesso dos procedimentos diagnósticos. A empresa também não se responsabiliza por quaisquer perdas de dados, modificações de informação contida no equipamento ou danos em acessórios, películas, capas, cartões SIM, cartões de memória ou outros componentes deixados com o equipamento.
        </td></tr>
    </table>
    <table style="width:19cm; height:2cm;">
        <tr><td align="center" height="1cm"><b style="font-size:10pt"><input type="checkbox" value=1 id="checkbox1"'.($dataURI!='' ? ' disabled checked' : '').'>Declaro que aceito as condições propostas para a reparação do meu equipamento.<br>Autorizo, igualmente, a receção de informações sobre o estado da(s) minha(s) encomenda(s) e/ou reparação(ões), bem como comunicações relacionadas com produtos, serviços, newsletters, SMS e outras informações de caráter informativo e comercial.</b></td></tr>';

        if($dataURI) {
            $html .='<tr><td align=right><img src="'.$dataURI.'"></td></tr>';
            
            $html .= '<tr><td height="1cm" align="center"><b>Data:</b> '.$signed_date.'&nbsp;&nbsp;&nbsp;_______________________________________</td></tr>';
            //$html .= '<tr><td height="1cm" align="center"><b>Data:</b> ______/______/__________&nbsp;&nbsp;&nbsp;_______________________________________</td></tr>';
            $html .= '<tr><td>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(Assinatura do cliente)</td></tr>';
        }

        $html .= '</table>';
}


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta content="text/html; charset=utf8" http-equiv="Content-Type" />
<meta name="robots" content="noindex" />
<meta name="googlebot" content="noindex" />
<meta name="googlebot-news" content="noindex" />
<title>Signature</title>
<style type="text/css">
.disclaimerific {
    font-family: Arial, Helvetica, sans-serif;
}
.auto-style10 {
    text-align: left;
}
</style>
<link href="assets/jquery.signaturepad.css" rel="stylesheet">
<!--[if lt IE 9]><script src="assets/flashcanvas.js"></script><![endif]-->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>

   <link rel="stylesheet" type="text/css" href="css/default.css" />
        <link rel="stylesheet" type="text/css" href="css/component.css" />
        <script src="js/modernizr.custom.js"></script>
</head>

<body style="background-color: #DDDDDD">
    <?php
    if(!$rma)
    {?><form method="get" action="index.php" class="sigPad">
        <table align="center" cellpadding="0" cellspacing="0" style="width: 799px">
    <tr>
        <td align="center" style="padding:10px;background-color: #47a90b;color:#fff">RECEÇÃO</td>
    </tr>
    <tr>
        <td align="center" style="padding:10px;background-color: #47a90b;color:#fff">Insira o numero de RMA: <?=$rma?></td>
    </tr>
    <tr>
        <td align="center" style="padding:10px;background-color: #47a90b;color:#fff"><input type="text" name="rma"></td>
    </tr>
    <tr>
        <td align="center" style="padding:10px;background-color: #47a90b;color:#fff"><button id="btn-accept" style="width: 200px;color:white;" href="" type="submit" class="button1"><p style="font-size: 14px;">ENVIAR</p></button></td>
    </tr>
    <tr>
        <td align="center" style="padding:10px;background-color: #c40916;color:#fff"><a href="../signed/index.php"><p style="color: #fff;font-size: 14px;">ASSINAR ENTREGA</p></a></td>
    </tr>
    </table>
        </form><?

        die();
    }
    ?>



<table align="center" cellpadding="0" cellspacing="0" style="width: 799px">
    <tr>
        <td align="center" style="padding:10px;background-color: #87ceee;color:#fff">RMA <?=$rma?></td>
    </tr>
    <tr>
        <td align="center" style="padding:10px;background-color: #FFFFFF;color:#000000; padding-top: 30px;">
            <?
            echo $html;
            ?>
        </td>
    </tr>
<tr>
    <td style="background-color: #FFFFFF">
        <!-- Signature Table Box -->
                <div style="width:450px; height:auto;  margin: 0px auto 50px; auto; padding:20px; color:black;display:none" id="assinar"><?php

//$dataURI = 'data:image/png;base64,'.base64_encode(ob_get_clean());

                // echo HTML img element using that Data URI, somewhere on the page
                //echo 'rma:'.$rma;
                //echo '<img src="'.$dataURI.'">';
                ?>

                     <form method="post" action="capture_signature.php?rma=<?=$rma?>" class="sigPad">

                        <input type="hidden" name="csrf_token" value="<?=htmlspecialchars($signature_csrf, ENT_QUOTES, 'UTF-8')?>">

                        <p class="drawItDesc">Por favor assine aqui</p>

                        <div class="sig sigWrapper">
                          <canvas class="pad" width="400"></canvas>

                          <input type="hidden" name="output" class="output">
                        </div>
                        <br>
                        <div style="width:100%;">
                            <style>

                                </style>
                            <table style="width: 100%;text-align: center;">
                                <tr>
                                    <td style="width: 50%;">
                                     <div id="submit" >
                                        <button id="btn-accept" style="width: 100%;color:white;" href="" type="submit" class="button1"><p style="font-size: 14px;">ACEITAR</p></button>
                                    </div>
                                    </td>
                                    <td style="width: 50%;">
                                    <div id="clear" >
                                        <button style="width: 100%; background-color: #EFEFEF;border: 1px solid #666666;" href="" name="clear" type="submit" class="clearButton">
                                        <p style="font-size: 14px;">LIMPAR</p>
                                        </button>
                                    </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <!-- <div style="clear:both;"></div> -->
                      </form>

                </div>
				<script src="assets/flashcanvas.js"></script>
				<script src="assets/jquery.signaturepad.js"></script>
				<script src="assets/json2.min.js"></script>
				<script src="assets/functions.js"></script>
        <!-- End Signature Table Box -->

    </td>
</tr>
    <tr>
      <td style="background-color: #dddddd">
        <p style="margin-top: 0; margin-bottom: 0">&nbsp;</td>
  </tr>
    <tr>
      <td style="background-color: #dddddd; font-family:Arial, Helvetica, sans-serif; font-size:9px; color:#666"><div align="center">
        <p style="margin-top: 0; margin-bottom: 0">&copy; Copyright Dotbright.<br />
        </div></td>
  </tr>
    <tr>
      <td height="2" style="background-color: #dddddd"></span></td>
  </tr>
</table>
        <div align="center" style="width:750px; color:#666666"  >
            <p style="margin-top: 0; margin-bottom: 0"></div>
<p style="margin-top: 0; margin-bottom: 0"></p>
        </td>
    </tr>
<tr>
        <td style="text-align: center"></tr>
</td>
    </tr>
    <tr>
        <td style="text-align: center">
        <p style="margin-top: 0; margin-bottom: 0"></p>
        </td>
</tr>
</table>
</body>

</html>
<script>
$('#checkbox1').change(function() {
    $('#assinar').toggle(this.checked);
});
</script>
