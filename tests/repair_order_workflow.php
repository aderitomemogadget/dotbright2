<?php
// Offline integration: SQLite only. Mail/SMS functions fail if accidentally called.
set_error_handler(function($severity,$message,$file,$line) {throw new ErrorException($message,0,$severity,$file,$line);});
require_once __DIR__.'/../core/repair_orders.php';
function wf_assert($condition,$message) {if (!$condition) throw new RuntimeException($message);}
function wf_throws($callback,$message) {try {$callback();} catch (Throwable $e) {return;} throw new RuntimeException($message);}
function send_email_rma() {throw new RuntimeException('Real notifications forbidden in this test.');}
function send_sms_nos_detalhado() {throw new RuntimeException('Real notifications forbidden in this test.');}
function numberformat($n) {return number_format($n,2,',','.').' €';}
$db=new PDO('sqlite::memory:',null,null,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));
wf_assert(!dotbright_repair_workflow_ready($db),'Absent migration must be detected.');
$db->exec("CREATE TABLE repair_orders (id INTEGER PRIMARY KEY AUTOINCREMENT,legacy_id INTEGER UNIQUE,created_at TEXT,status TEXT DEFAULT 'received',version INTEGER DEFAULT 1,customer_name TEXT,customer_email TEXT,customer_phone TEXT,product_id INTEGER,product_name TEXT,product_reference TEXT,total_cents INTEGER,details_json TEXT,access_mode TEXT,access_cipher TEXT,staff_mail TEXT DEFAULT 'pending',customer_mail TEXT DEFAULT 'pending',copy_mail TEXT DEFAULT 'pending',mail_updated_at TEXT,updated_at TEXT,updated_by INTEGER,repair_status TEXT DEFAULT 'not_recorded',product_status TEXT DEFAULT 'not_recorded',rma_id INTEGER UNIQUE,rma_sent_at TEXT,rma_sent_by INTEGER)");
$db->exec('CREATE TABLE repair_order_events (id INTEGER PRIMARY KEY AUTOINCREMENT,repair_order_id INTEGER,event_type TEXT,actor_id INTEGER,before_json TEXT,after_json TEXT,created_at TEXT)');
wf_assert(dotbright_repair_workflow_ready($db) && !dotbright_repair_rma_ready($db),'Partial migration detection failed.');
$customer=array('r_nome'=>'João <teste>','r_nome2'=>'Empresa Ω','r_nif2'=>'999999999','r_recolha'=>str_repeat('á',999).'Ω','r_cp'=>'4470-151 EXTRA','r_cidade'=>'Maia','r_recolha_obs'=>'Tocar à campainha','r_entrega'=>'Rua B','r_entrega_cp'=>'4000-001','r_entrega_cidade'=>'Porto','r_entrega_obs'=>'Entregar na portaria','r_entrega_tipo'=>'different','period'=>'Tarde','r_avaria'=>"Ecrã partido\nSem toque");
$quote=array('repair_gross'=>189.9,'shipping_gross'=>0,'total'=>189.9,'warranty'=>'36 meses','service'=>array('time'=>'Sob consulta','part'=>'Compatível de qualidade premium','part_detail'=>'Peça compatível de qualidade premium.'));
$record=array('created_at'=>gmdate('Y-m-d H:i:s'),'status'=>'received','customer_name'=>$customer['r_nome'],'customer_email'=>'example@example.test','customer_phone'=>'912345678','product_id'=>123373,'product_name'=>'Reparação OLED Premium iPhone 17 Pro Max','product_reference'=>'REP-OLED','total_cents'=>18990,'details_json'=>json_encode(array('form_version'=>3,'customer'=>$customer,'quote'=>$quote),JSON_UNESCAPED_UNICODE),'access_mode'=>'code','access_cipher'=>'opaque-encrypted-secret','updated_at'=>gmdate('Y-m-d H:i:s'));
$input=array('rma_origin'=>'2','rma_state'=>'4','rma_brand'=>'Apple','rma_model'=>'iPhone 17 Pro Max','rma_imei'=>'','rma_serial'=>'SERIAL-TEST');
$id=dotbright_repair_order_insert($db,$record);$row=dotbright_repair_order_find($db,$id);
wf_assert($row['repair_status']==='received' && $row['product_status']==='awaiting_collection','New source states were not initialized.');
wf_throws(function()use($db,$id,$input){dotbright_repair_send_to_rma($db,$id,1,$input,7);},'Transfer without migration accepted.');
$payload=dotbright_repair_rma_payload($row,$input,7);
// Optional schema contract against the supplied export: no SQL is executed from it.
if (!empty($argv[1])) {
    $dump=file_get_contents($argv[1]);
    wf_assert(preg_match('/CREATE TABLE `rmas` \((.*?)\n\) ENGINE=/s',$dump,$match)===1,'RMA schema absent in export.');
    preg_match_all('/^\s*`([^`]+)` ([^\r\n]+)/m',$match[1],$columns,PREG_SET_ORDER);
    $schema=array();foreach($columns as $column) $schema[$column[1]]=$column[2];
    foreach($payload as $key=>$value) wf_assert($key==='repair_order_id' || isset($schema[$key]),'Unknown destination column: '.$key);
    foreach($schema as $key=>$definition) if (strpos($definition,'NOT NULL')!==false && strpos($definition,'DEFAULT')===false && strpos($definition,'AUTO_INCREMENT')===false) wf_assert(array_key_exists($key,$payload),'Missing mandatory destination column: '.$key);
    wf_assert(strpos($match[0],'ENGINE=')!==false && stripos($dump,'INSERT INTO')===false,'Expected schema-only export.');
}
$definitions=array();foreach($payload as $key=>$value) $definitions[]=$key==='repair_order_id'?'repair_order_id INTEGER UNIQUE':$key.' TEXT';
$db->exec('CREATE TABLE rmas (ID INTEGER PRIMARY KEY AUTOINCREMENT,'.implode(',',$definitions).')');
$db->exec('CREATE TABLE rmas_estados (ID INTEGER,Visivel INTEGER,Ordem INTEGER)');
$db->exec('CREATE TABLE rmas_estados_lang (ID_PAI INTEGER,Lang TEXT,Cabecalho TEXT)');
$db->exec('CREATE TABLE rma_lojas (zone_id INTEGER,zone_visible INTEGER,zone_order INTEGER,Loja TEXT)');
$db->exec('CREATE TABLE rma_lojas_lang (zone_id INTEGER,zone_lang TEXT,zone_name TEXT)');
$db->exec("INSERT INTO rmas_estados VALUES (4,1,1),(5,0,2); INSERT INTO rmas_estados_lang VALUES (4,'pt','Recebido'),(5,'pt','Oculto'); INSERT INTO rma_lojas VALUES (2,1,1,'MAIA'); INSERT INTO rma_lojas_lang VALUES (2,'pt','Maia')");
wf_assert(dotbright_repair_rma_ready($db),'Full migration not detected.');
dotbright_repair_workflow_update($db,$id,1,'review','awaiting_payment','received',7);
wf_assert(dotbright_repair_order_find($db,$id)['version']===2,'Workflow update version failed.');
wf_throws(function()use($db,$id){dotbright_repair_workflow_update($db,$id,1,'closed','completed','delivered',7);},'Stale status update accepted.');
wf_throws(function()use($db,$id){dotbright_repair_workflow_update($db,$id,2,'review','sent_to_rma','received',7);},'Unlinked sent-to-RMA status accepted.');
wf_throws(function()use($db,$id,$input){dotbright_repair_send_to_rma($db,$id,1,$input,7);},'Stale transfer accepted.');
wf_throws(function()use($db,$id,$input){dotbright_repair_send_to_rma($db,$id,2,array_merge($input,array('rma_state'=>'5')),7);},'Hidden RMA state accepted.');
wf_throws(function()use($db,$id,$input){dotbright_repair_send_to_rma($db,$id,2,array_merge($input,array('rma_origin'=>'99')),7);},'Unknown origin accepted.');
wf_throws(function()use($db,$id,$input){dotbright_repair_send_to_rma($db,$id,2,array_merge($input,array('rma_serial'=>array())),7);},'Malformed serial accepted.');
wf_assert((int)$db->query('SELECT COUNT(*) FROM rmas')->fetchColumn()===0,'Rejected transfer left a destination record.');
$rma=dotbright_repair_send_to_rma($db,$id,2,$input,7);
$saved=$db->query('SELECT * FROM rmas WHERE ID='.$rma)->fetch(PDO::FETCH_ASSOC);$linked=dotbright_repair_order_find($db,$id);
wf_assert((int)$linked['rma_id']===$rma && $linked['repair_status']==='sent_to_rma' && $linked['product_status']==='received','Link or independent states incorrect.');
wf_assert($linked['details_json']===$record['details_json'] && $linked['access_cipher']==='opaque-encrypted-secret' && $linked['total_cents']===18990,'Source snapshot, access or amount changed.');
wf_assert($saved['cliente_morada']===$customer['r_recolha'] && $saved['cliente_cp']===$customer['r_cp'] && $saved['cliente_empresa']==='Empresa Ω' && $saved['cliente_nif']==='999999999' && $saved['m_modelo']==='iPhone 17 Pro Max' && $saved['Loja']==='MAIA','Customer/billing/equipment mapping lost data.');
wf_assert($saved['codigo_desbloqueio']==='' && (int)$saved['m_padrao']===0 && strpos(json_encode($saved),'opaque-encrypted-secret')===false,'Access secrets copied to legacy RMA.');
wf_assert((float)$saved['preorcamento']===189.9 && (float)$saved['total']===0.0 && (float)$saved['m_valor']===0.0 && (int)$saved['m_pago']===0,'Estimate created completed work/payment.');
wf_assert($saved['notes']==='','Transfer incorrectly flagged a new customer reply.');
foreach(array('Compatível de qualidade premium','Rua B','4000-001','Porto','Entregar na portaria','Tocar à campainha','Tarde','189,90 EUR') as $text) wf_assert(strpos($saved['m_observacoes'],$text)!==false,'Context missing from RMA: '.$text);
wf_assert(strpos($saved['m_avaria'],"Ecrã partido\nSem toque")!==false && $saved['m_tracking']==='DB000001','Fault/tracking mapping failed.');
wf_assert(dotbright_repair_send_to_rma($db,$id,2,$input,7)===$rma && (int)$db->query('SELECT COUNT(*) FROM rmas')->fetchColumn()===1,'Repeated submit duplicated RMA.');
wf_assert((int)$db->query('SELECT COUNT(*) FROM repair_order_events')->fetchColumn()===2,'Audit missing or duplicated.');
wf_assert(dotbright_repair_orders_search($db,'','',1,'sent_to_rma','received')['total']===1 && dotbright_repair_orders_search($db,'','',1,'paid','')['total']===0,'Workflow filters failed.');
// A MyISAM insert may survive a failed InnoDB transaction. Retry must reuse that row.
$second=dotbright_repair_order_insert($db,$record);$orphan=dotbright_repair_rma_payload(dotbright_repair_order_find($db,$second),$input,7);
$stmt=$db->prepare('INSERT INTO rmas ('.implode(',',array_keys($orphan)).') VALUES ('.implode(',',array_fill(0,count($orphan),'?')).')');$stmt->execute(array_values($orphan));$orphanId=(int)$db->lastInsertId();
wf_assert(dotbright_repair_send_to_rma($db,$second,1,$input,7)===$orphanId && (int)$db->query('SELECT COUNT(*) FROM rmas')->fetchColumn()===2,'Interrupted-transfer recovery duplicated RMA.');
$third=dotbright_repair_order_insert($db,$record);dotbright_repair_workflow_update($db,$third,1,'cancelled','cancelled','awaiting_collection',7);
wf_throws(function()use($db,$third,$input){dotbright_repair_send_to_rma($db,$third,2,$input,7);},'Cancelled source transferred.');
$fourth=dotbright_repair_order_insert($db,array_merge($record,array('legacy_id'=>123,'details_json'=>'{"legacy":true}')));
wf_assert(dotbright_repair_order_find($db,$fourth)['repair_status']==='not_recorded','Historical workflow invented.');
wf_throws(function()use($db,$fourth,$input){dotbright_repair_send_to_rma($db,$fourth,1,$input,7);},'Incomplete historical source transferred.');
// Render real BO views, including the transfer form and linked detail.
$_SESSION=array();$_POST=$_GET=array();$ro_csrf=dotbright_repair_order_csrf();$ro_labels=dotbright_repair_order_statuses();$ro_error=$ro_flash='';$ro_access=null;$ro_can_reveal=true;$ro_workflow=$ro_rma_ready=true;$ro_events=array();$ro_rma_options=dotbright_repair_rma_options($db);
$ro_row=dotbright_repair_order_find($db,$id);ob_start();include __DIR__.'/../admin/reparacoes/view.inc.php';$html=ob_get_clean();
wf_assert(strpos($html,'RMA #1')!==false && substr_count($html,'role="tab"')===4 && strpos($html,'opaque-encrypted-secret')===false && strpos($html,'name="repair_status"')!==false,'Linked detail rendering failed.');
$fifth=dotbright_repair_order_insert($db,$record);$ro_row=dotbright_repair_order_find($db,$fifth);ob_start();include __DIR__.'/../admin/reparacoes/view.inc.php';$html=ob_get_clean();
wf_assert(strpos($html,'name="rma_state"')!==false && strpos($html,'name="_csrf"')!==false && strpos($html,'name="rma_model"')!==false,'Transfer form absent.');
$ro_row=null;$ro_id=0;$ro_term=$ro_status=$ro_repair_filter=$ro_product_filter='';$ro_result=dotbright_repair_orders_search($db,'','',1);ob_start();include __DIR__.'/../admin/reparacoes/view.inc.php';$html=ob_get_clean();
wf_assert(strpos($html,'Enviar para RMA')!==false && strpos($html,'Estado reparação')!==false && strpos($html,'Estado produto')!==false,'List workflow/actions missing.');
// Loading all legacy RMA function declarations no longer needs the private SMS library.
require_once __DIR__.'/../rmas/rmas/funcoes.inc.php';
wf_assert(function_exists('show_content_list') && function_exists('rma_send_sms_notice'),'RMA functions failed to load without private transport.');
// The legacy summary must preserve a transferred model absent from auxiliary lists,
// and display customer markup as text instead of executable HTML.
class DataObj {
    public $db_data=array();
    function db_open($link,$database,$sql) {return true;}
    function db_close() {}
    function EOF() {return true;}
}
function write_lang($key,$fallback) {return $fallback;}
function wf_render_rma_summary($saved) {
    $fixture=__DIR__.'/../rmas/rmas/tabs/resumo.php';
    foreach(token_get_all(file_get_contents($fixture)) as $token) if (is_array($token) && $token[0]===T_VARIABLE) {
        $name=substr($token[1],1);
        if (!in_array($name,array('GLOBALS','_GET','_POST','_SESSION'),true) && !isset($$name)) $$name='';
    }
    extract($saved,EXTR_OVERWRITE);$software_arr=$hardware_arr=array();
    $GLOBALS['site_http_url']='https://example.test/';
    ob_start();include $fixture;return ob_get_clean();
}
$legacy_html=wf_render_rma_summary(array_merge($saved,array('m_modelo'=>'Model "TEST" <img src=x onerror=alert(1)>','m_marca'=>'Unknown brand','m_avaria'=>'</textarea><script>alert(1)</script>')));
wf_assert(strpos($legacy_html,'<script>alert(1)</script>')===false && strpos($legacy_html,'<img src=x')===false && strpos($legacy_html,'&lt;script&gt;alert(1)&lt;/script&gt;')!==false && strpos($legacy_html,'value="Unknown brand" selected')!==false && strpos($legacy_html,'value="Model &quot;TEST&quot; &lt;img src=x onerror=alert(1)&gt;" selected')!==false,'Legacy summary lost unknown equipment data or executed customer markup.');
echo "PASS: RRE/RMA mapping, schema contract, independent states, optimistic concurrency, idempotency/recovery, history, filters, permissions-compatible views, protected access and optional SMS dependency. Offline only.\n";
