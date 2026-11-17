<?php
require_once('comprafacil.php');

/**
 * Neste ficheiro vamos gerar uma referência com limite
 * de tempo para pagamento. Vamos também passar uma série de
 * informação sobre o cliente que realizou a compra para
 * registo no sistema do CompraFácil
 **/
$pagamento = new comprafacil;
$pagamento->enable_debug(); // "Ligar" modo de depuração => todos os erros serão mostrados

/**
 * As seguintes linhas de código têm de ser SEMPRE
 * executadas antes de ser realizado o pedido em si.
 * 
 * Atenção que o método set_origem(), caso seja passado
 * um URL, será esse o URL chamado quando o pagamento for
 * realizado (útil caso se queira automatizar o processo de 
 * confirmação de pagamento).
 **/
$pagamento->set_username('2d07f5a394f405bf15b1b2ef6781f2b8'); // Definir o username de acesso à plataforma CompraFácil
$pagamento->set_password('34e1d8bc7e4d84ed0e4de27d1aa64a91'); // Definir a password de acesso à plataforma CompraFácil
$pagamento->set_valor(25.00); // Definir o valor da referência
$pagamento->set_origem("http://www.diogoosorio.com/confirmar_comprafacil.php"); // Definir a origem do pagamento

/**
 * Os parâmetros seguintes são opcionais e servem apenas
 * para referência do utilizador (esta informação fica registada
 * no painel do CompraFácil).
 **/
$pagamento->informacao = "Produto de Teste Nr 1."; // Definir alguma informação adicional sobre a compra (opcional)
// $pagamento->refExt = 233; // Definir uma referência própria. Pode ser útil na validação do pagamento (opcional)
//$pagamento->set_backoffice_user(2); // Definir o ID do utilizador do backoffice a quem a referência será associada (opcional)

/**
 * Os próximos parâmetros são relativos à informação do cliente
 * que realizou a compra.
 * 
 * Aqui serão utilizadas todas as opções possíveis, mas 
 * nenhuma delas é obrigatória para realizar o pedido.
 **/
 // $pagamento->nome   = "Diogo Osório";
 // $pagamento->morada = "Rua Salgueiro Maia, nº 23, 1º Dto.";
 // $pagamento->cPostal = "2778-445";
 // $pagamento->localidade = "Lisboa";
 // $pagamento->nif = "123456789";
 // $pagamento->telf = "2147785547";
 // $pagamento->email = "cliente@dominio.pt";
 
 /**
 * Para se obter a informação pretendida é necessário seguir dois
 * passos:
 *  1. Realizar o pedido (get_referencia_limite_valor()).
 *  2. Mostrar a informação recolhida: get_entidade(), get_referencia(), get_valor(), get_referencia_completa()
 **/
$pagamento->get_referencia_limite_valor(); // Realizar o pedido ao webservice
?>
<p>
    Posso mostrar a informação recolhida manualmente: 
    <strong>Entidade:</strong> <?php echo $pagamento->get_entidade(); ?>, 
    <strong>Referência:</strong> <?php echo $pagamento->get_referencia(); ?>,
    <strong>Valor:</strong> <?php echo $pagamento->get_valor(); ?>.
</p>

<p>
    Ou alternativamente chamar o método get_referencia_completa() que gera uma lista do tipo:
    <?php echo $pagamento->get_referencia_completa(); ?>
</p>