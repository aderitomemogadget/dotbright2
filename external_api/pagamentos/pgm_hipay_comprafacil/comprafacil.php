<?php
/**
 * @name CompraFácil Classe Pagamento
 * @author Diogo Osório (diogo.g.osorio@gmail.com) 
 * @license GPL 3.0
 * @url www.diogoosorio.com
 * @version 0.1a
 * 
 * @description
 *      Esta classe permite realizar pedidos ao webservice
 *      da Hi-Media (http://www.comprafacil.pt). 
 *      Neste momento apenas permite realizar pedidos de 
 *      referências temporárias (11249), com ou sem informa-
 *      ção do cliente.
 **/
Class comprafacil{
    
    private $username;
    private $password;
    //private $username = 'username';
    //private $password = 'password';
    
    private $wsURL;
    private $tipo;
    private $idUserBack = -1;
    private $origem;
    private $debug      = false;
    private $soapClient;
    
    private $entidade;
    private $referencia;
    private $validade;
    private $valor;
    
    private $pago;
    private $estado;
    private $dataUltimoPagamento;
    private $totalPagamentos;
    
    public $informacao, $nome, $morada, $cPostal, $localidade, $nif, $refExt, $telf, $email, $produtoID;
    
    public function set_origem($url){
        $this->origem = $url;
    }
    
    public function set_username($user){
        $this->username = $user;
    }
    
    public function set_password($password){
        $this->password = $password;
    }
    
    public function set_valor($valor){
        $this->valor = (double) $valor;
    }
    
    public function set_produtID($produtoID){
        $this->produtoID = (int) $produtoID;
    }
    
    public function set_backoffice_user($user_id){
        $this->idUserBack = (int) $user_id;
    }
    
    public function is_pago(){
        return $this->pago;
    }
    
    public function get_estado(){
        return $this->estado;
    }
    
    public function get_data_ultimo_pagamento(){
        return $this->dataUltimoPagamento;
    }
    
    public function get_total_pagamentos(){
        return $this->totalPagamentos;
    }
    
    public function get_referencia(){
        return $this->referencia;
    }
    
    public function get_entidade(){
        return $this->entidade;
    }
    
    public function get_valor(){
        return $this->valor;
    }
    
    public function get_pago(){
        return $this->pago;
    }
    
    /**
     * Retorna uma lista com todas as informações 
     * resultantes da chamada get_info_compra()
     * 
     * @access public
     * @return String
     **/    
    public function get_informacao_compra(){
        $return = '<ul id="info-compra">';
        $return .= '<li><strong>Pago:</strong> ';
        $return .= $this->pago ? 'Sim' : 'Não';
        $return .= '</li>';
        $return .= '<li><strong>Estado:</strong> ' . $this->estado . '</li>';
        $return .= '<li><strong>Data Último Pagamento: </strong>' . $this->dataUltimoPagamento . '</li>';
        $return .= '<li><strong>Total Pagamentos:</strong> ' . $this->totalPagamentos . '</li>';
        $return .= '</ul>';
        return $return; 
    }
    
    /**
     * Retorna uma lista com todos os parâmetros
     * relevantes para uma referência, definidos
     * no objecto naquele instante
     * 
     * @access public
     * @return String
     **/
    public function get_referencia_completa(){
        $return = '<ul id="cp-payment">';
        if(isset($this->entidade))
            $return .= '<li><strong>Entidade:</strong> ' . $this->entidade . '</li>';
        
        $return .= '<li><strong>Referência:</strong> ' . $this->referencia . '</li>';
        $return .= '<li><strong>Valor:</strong> ' . $this->valor . '</li>';
        
        if(isset($this->validade))
            $return .= '<li><strong>Validade:</strong> ' . $this->validade . '</li>';
            
        $return .= '</ul>';
        
        return $return;
        
    }
    
    /**
     * Define se a classe está em modo debug (e 
     * consequentemente mostra todos os erros), ou não
     * 
     * @access public
     * @return void
     **/
    public function enable_debug(){
        $this->debug = true;
        
        if(!extension_loaded('soap'))
            trigger_error("A biblioteca SOAP não foi carregada!");
    }
    
    /**
     * Faz uma chamada ao serviço da Hi-Media e requisita
     * uma referência com validade e valor definido.
     * 
     * Retorna verdadeiro caso não existam erros, ou falso
     * caso ocorra um.
     * 
     * @access public
     * @return boolean
     **/
    public function get_referencia_limite_valor(){
        $this->tipo = 1;
        $this->wsURL = 'https://hm.comprafacil.pt/SIBSClick2/webservice/clicksmsV4.asmx?WSDL';
        $this->validate();
        $this->create_soap_client();
        
        $parameters = Array(
            "origem"            => $this->origem,
            "IDCliente"         => $this->username,
            "password"          => $this->password,
            "valor"             => $this->valor,
            "informacao"        => htmlentities($this->informacao),
            "IDUserBackoffice"  => $this->idUserBack
        );
        
        try{
            $res = $this->soapClient->SaveCompraToBDValor1($parameters);
            if($res->SaveCompraToBDValor1Result){
                $this->entidade = $res->entidade;
                $this->referencia = $res->referencia;
                $this->valor = number_format($res->valorOut, 2);
            } else {
                if($this->debug)
                    die($res->error);
            }
        } catch (Exception $e){
            if($this->debug)
                die($e);
        }
        
        return true;
    }
    
    
    /**
     * Faz uma chamada ao serviço da Hi-Media e requisita
     * uma referência com validade, valor definido e regista
     * a informação do cliente no webservice.
     * 
     * Retorna verdadeiro caso não existam erros, ou falso
     * caso ocorra um.
     * 
     * @access public
     * @return boolean
     **/
    public function get_referencia_limite_valor_cliente(){
        $this->tipo = 2;
        $this->wsURL = 'https://hm.comprafacil.pt/SIBSClick2/webservice/clicksmsV4.asmx?WSDL';
        $this->validate();
        $this->create_soap_client();
        
        $paramenters = Array(
            "origem"            => $this->origem,
            "IDCliente"         => $this->username,
            "password"          => $this->password,
            "valor"             => $this->valor,
            "informacao"        => $this->informacao,
            "nome"              => $this->nome,
            "morada"            => $this->morada,
            "codPostal"         => $this->cPostal,
            "localidade"        => $this->localidade,
            "NIF"               => $this->nif,
            "RefExterna"        => $this->refExt,
            "telefoneContacto"  => $this->telf,
            "email"             => $this->email,
            "IDUserBackoffice"  => $this->idUserBack
        );
        
        try{
            $res = $this->soapClient->SaveCompraToBDValor2($parameters);
            
            if($res->SaveCompraToBDValor2Result){
                $this->entidade = $res->entidade;
                $this->referencia = $res->referencia;
                $this->valor = $res->valorOut;
            } else {
                if($this->debug)
                    die($res->error);
            }
        } catch(Exception $e){
            if($this->debug)
                die($e);
            return false;
        }
        
        return true;
    }
    
    /**
     * Faz uma chamada ao serviço da Hi-Media e requisita
     * toda a informação relativa a uma referência
     * 
     * Retorna verdadeiro caso não existam erros, ou falso
     * caso ocorra um.
     * 
     * @access public
     * @return boolean
     **/
    public function get_info_compra(){
        $this->validate();
        
        if(!isset($this->soapClient))
            $this->create_soap_client();
        
        if(!isset($this->referencia))
            die("Para poder obter o estado de uma referência, tem primeiro de ter essa referência definida!");
        
        $parameters = Array(
                        "referencia"    => $this->referencia,
                        "IDCliente"     => $this->username,
                        "password"      => $this->password
                    );
        
        try{
            $res = $this->soapClient->getInfoCompra($parameters);
            if($res->getInfoCompraResult){
                $this->pago = $res->pago;
                $this->estado = $res->estado;
                $this->dataUltimoPagamento = $res->dataUltimoPagamento;
                $this->totalPagamentos = $res->TotalPagamentos;
            } else {
                die($res->error);
            }
        } catch(Exception $e){
            if($this->debug)
                die($e);
        }
    }
    
    /**
     * Verifica se uma referência está paga.
     * 
     * Retorna true, caso esteja ou false caso
     * não esteja.
     * 
     * @access public
     * @param String $referencia A referência a testar
     * @return boolean
     **/
    public function esta_paga($referencia){
        $this->wsURL = 'https://hm.comprafacil.pt/SIBSClick2/webservice/clicksmsV4.asmx?WSDL';
        $this->referencia = $referencia;
        $this->valor = 2; // Valor "dummy"
        $this->origem = "dummy"; // Valor "dummy"
        
        $this->get_info_compra();
        return $this->pago;
    }
    
    
    /**
     * Verifica se todos so campos necessários para efectuar
     * o pedido estão preenchidos. O objectivo é evitar pedidos
     * desnecessários que, à partida se saiba que irão retornar
     * algum erro.
     * 
     * @access private
     * @return void
     **/
    private function validate(){
        if(!isset($this->username)){
            die("O username de acesso à plataforma não está definida.");
        }
        
        if(!isset($this->password)){
            die("A password de acesso à plataforma não está definida.");
        }
            
        if(($this->tipo == 1 || $this->tipo == 2) && !isset($this->valor)){
             die("Para gerar uma referência com valor, tem primeiro de definir o valor!");
        }
        
        if(($this->tipo == 3 || $this->tipo == 4) && !isset($this->produtoID)){
            die("Para gerar uma referência sem valor, tem primeiro de definir o ID do produto.");
        } 
        
        if(!isset($this->origem)){
            die("Tem de definir uma origem para a chamada.");
        }           
    }
    
    
    /**
     * Cria um cliente SOAP para o URL definido
     * em cada momento.
     * 
     * @access private
     * @return void
     **/
    private function create_soap_client(){
        if(!isset($this->wsURL)){
            die("Tem de definir o endereço antes de criar o cliente SOAP!");
        }
        
        $options = $this->debug ? Array('encoding' => 'ISO-8859-1', 'trace' => true, 'exceptions' => true) : Array('encoding' => 'ISO-8859-1', 'exceptions' => true);
        
        try{
            $this->soapClient = new SoapClient($this->wsURL, $options);
        }catch(Exception $e){
            if($this->debug)
                die($e);
        }
    }
    
}
?>