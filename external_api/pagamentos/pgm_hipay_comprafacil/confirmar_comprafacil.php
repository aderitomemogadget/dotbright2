<?php
require_once('comprafacil.php');

/**
 * Neste ficheiro vamos verificar se uma referência 
 * foi realmente paga.
 * 
 * De notar que neste exemplo se espera que seja o
 * sistema do CompraFácil que chame a página, passando
 * como argumento (via GET) a própria referência a ser
 * confirmada.
 * 
 * No entanto e naturalmente, a referência a verificar
 * pode ser introduzida de qualquer outra forma.
 **/
 //$ref = $_GET['ref'];
 $ref = "912 500 961";
 $comprafacil = new comprafacil;
 $comprafacil->enable_debug(); // "Ligar" modo de depuração => todos os erros serão mostrados
 
 /**
 * As seguintes linhas de código têm de ser SEMPRE
 * executadas antes de ser realizado o pedido em si.
 * 
 * Atenção que o método set_origem(), caso seja passado
 * um URL, será esse o URL chamado quando o pagamento for
 * realizado (útil caso se queira automatizar o processo de 
 * confirmação de pagamento).
 **/
$comprafacil->set_username('xxx'); // Definir o username de acesso à plataforma CompraFácil
$comprafacil->set_password('xxx'); // Definir a password de acesso à plataforma CompraFácil


/**
 * Agora vem o verdadeiro teste.
 **/
if($comprafacil->esta_paga($ref)){
    // A referência foi realmente paga.
    // Aqui pode adicionar o seu código...
     
    // Ficam também definidas automaticamente uma série de propriedades do pagamento:
    if($comprafacil->get_pago()){ // Se estiver paga
        echo "<strong>Estado:</strong> " . $comprafacil->get_estado() . "<br />";
        echo "<strong>Data Último Pagamento:</strong> " . $comprafacil->get_data_ultimo_pagamento() . "<br />";
        echo "<strong>Total Pagamentos:</strong> " . $comprafacil->get_total_pagamentos() . "<br />";
    }
} else {
    echo "Referência não paga!<br />";
}
?>