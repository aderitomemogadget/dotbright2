<?php
// Exercise the real legacy save handler with a database double. No external IO.
set_error_handler(function($n,$s,$f,$l){throw new ErrorException($s,0,$n,$f,$l);});
require_once __DIR__.'/../rmas/rmas/funcoes.inc.php';
function br_assert($ok,$message){if(!$ok) throw new RuntimeException($message);}
define('TBID','ID');define('TBMODULO','rmas');define('TBMODULO_LANG','rmas_linhas');
$GLOBALS['DB_host']=$GLOBALS['DB_user']=$GLOBALS['DB_pass']=$GLOBALS['DB_name']='offline';
$GLOBALS['lojas_rmas']=array(1=>'Online',2=>'Loja de Matosinhos',3=>'Loja de Maia');
$GLOBALS['CMPS_MODULO_DADOS']=array('ID','m_estado','m_origem','cliente_nome','cliente_username','cliente_telefone1','cliente_morada','cliente_cp','cliente_sobrenome','cliente_apelido','m_imei','codigo_desbloqueio','m_padrao','preorcamento','total');
$GLOBALS['CMPS_MODULO_DADOS_LANG']=array();
$GLOBALS['queries']=array();$GLOBALS['broken_link']=false;$GLOBALS['fail_update']=false;
function db_opendb_conection($a,$b,$c){return true;}
function db_get_last_insert_id($db){return 123;}
function Runquery($link,$db,$sql){$GLOBALS['queries'][]=$sql;return !($GLOBALS['fail_update'] && strpos($sql,'UPDATE rmas SET')===0);}
function send_email_rma(){throw new RuntimeException('Unexpected email attempt.');}
function send_sms_nos_detalhado(){throw new RuntimeException('Unexpected SMS attempt.');}
class DataObj {
    public $db_data;private $empty=true;
    function db_open($link,$db,$sql){
        if(strpos($sql,'SELECT * FROM rmas WHERE ID=')===0){$this->db_data=array('repair_order_id'=>strpos($sql,'44346')!==false?10:0);$this->empty=false;}
        elseif(strpos($sql,'SELECT id,rma_id FROM repair_orders WHERE id=10 AND rma_id=44346')===0){$this->empty=$GLOBALS['broken_link'];$this->db_data=array('id'=>10,'rma_id'=>44346);}
        elseif(strpos($sql,'SELECT * FROM rmas_emp')===0){$this->empty=true;}
        elseif(strpos($sql,'SELECT img_ext FROM products_images_rmas')===0){$this->empty=true;}
        else throw new RuntimeException('Unexpected query (possible customer account or notification side effect): '.$sql);
        return true;
    }
    function EOF(){return $this->empty;}
    function db_close(){}
}
$_SESSION=array('ss_admin_id'=>16);$token=dotbright_rma_repair_token();
$post=array('ID'=>'44346','_rre_csrf'=>$token,'m_estado'=>'4','m_origem'=>'3','cliente_nome'=>'João','cliente_username'=>'teste@example.test','cliente_telefone1'=>'912345678','cliente_morada'=>'Rua A','cliente_cp'=>'4470-151','cliente_sobrenome'=>'','cliente_apelido'=>'','m_imei'=>'','codigo_desbloqueio'=>'MUST-NOT-SAVE','m_padrao'=>'12345','preorcamento'=>'469.90','total'=>'0','Apagar_img'=>array(),'tem_foto'=>array(),'qtd'=>array(),'ref'=>array(),'desc'=>array(),'prec'=>array(),'disp'=>array(),'envio_email_orcamento'=>'1');
$_FILES=array('img_peq'=>array('name'=>array()));$_POST=$post;
$originConfig=$GLOBALS['lojas_rmas'];unset($GLOBALS['lojas_rmas']);
dotbright_rma_repair_validate($post);
br_assert(dotbright_rma_origins()[1]==='Online','Legacy origin fallback missing.');
$GLOBALS['lojas_rmas']=$originConfig;
$saved=submeteDados();
br_assert($saved['resultado']===true && (int)$saved['id_acao']===44346,'Linked RRE save failed without surname/IMEI.');
$sql=implode("\n",$GLOBALS['queries']);
br_assert(strpos($sql,'m_estado="4"')!==false && strpos($sql,'preorcamento="469.90"')!==false && strpos($sql,'total="0"')!==false,'Technical state or amounts lost.');
br_assert(strpos($sql,'MUST-NOT-SAVE')===false && strpos($sql,'m_padrao="0"')!==false && strpos($sql,$token)===false,'Source access data/CSRF leaked into legacy writes/logs.');
br_assert(strpos($sql,'INSERT INTO rmas_logs')!==false && strpos($sql,'DADOS_ENVIO')!==false,'Legacy history not recorded with schema required fields.');
br_assert(stripos($sql,' clientes')===false,'Saving RRE created/overwrote an account.');
$_SESSION['ss_admin_id']=10;$_POST=$post;$GLOBALS['queries']=array();
br_assert(submeteDados()['resultado']===true && strpos(implode("\n",$GLOBALS['queries']),'INSERT INTO rmas_logs')!==false,'Linked RRE lost history for legacy operator 10.');
$_SESSION['ss_admin_id']=16;
$GLOBALS['queries']=array();$_POST=array_merge($post,array('m_estado'=>'15','imei_emp'=>'LOAN-TEST','estado_emp'=>'2'));
br_assert(rma_is_loan_block_result(submeteDados()['resultado']) && !$GLOBALS['queries'],'RRE save bypassed outstanding-loan finalization guard.');
// Direct POST must enforce the same source validation, even when JS is bypassed.
foreach(array(array('_rre_csrf'=>'wrong'),array('cliente_nome'=>''),array('cliente_username'=>'invalid'),array('m_origem'=>'99'),array('m_estado'=>'x')) as $bad){
    $GLOBALS['queries']=array();$_POST=array_merge($post,$bad);$result=submeteDados();
    br_assert($result['resultado']===false && !$GLOBALS['queries'],'Invalid linked save mutated data.');
}
$GLOBALS['broken_link']=true;$GLOBALS['queries']=array();$_POST=$post;
br_assert(submeteDados()['resultado']===false && !$GLOBALS['queries'],'Unverified source entered the legacy save path.');
$GLOBALS['broken_link']=false;$GLOBALS['fail_update']=true;$GLOBALS['queries']=array();$_POST=$post;
br_assert(submeteDados()['resultado']===false && count($GLOBALS['queries'])===1,'Failed RMA update created history/secondary writes.');
br_assert(dotbright_rma_repair_context(123)===array(),'Unlinked legacy RMA incorrectly treated as RRE.');
echo "PASS: real legacy RMA save, linked source validation/CSRF, optional surname/IMEI, preserved amounts, logs, no account writes, no emails/SMS, protected secrets and failed-save guards. Database double only.\n";
