<?php
// Uses SQLite in memory and a catalogue double. No real database, mail or credentials.
set_error_handler(function($severity,$message,$file,$line) { throw new ErrorException($message,0,$severity,$file,$line); });
require_once __DIR__.'/../core/repair_orders.php';
function ro_assert($ok,$message) { if (!$ok) throw new Exception($message); }
function ro_throws($fn,$message) { try {$fn();} catch (Throwable $e) {return;} throw new Exception($message); }
$GLOBALS['DB_host']=$GLOBALS['DB_user']=$GLOBALS['DB_pass']=$GLOBALS['DB_name']='test';
function db_opendb_conection($a,$b,$c) {return true;}
class DataObj {
    public $db_data;
    function db_open($l,$d,$sql) {
        $this->db_data=false;
        if (preg_match('/FROM products_families WHERE ID=(\d+)/',$sql,$m)) {
            $parents=array(85=>0,86=>85,10=>0);
            if (isset($parents[$m[1]])) $this->db_data=array('ID'=>$m[1],'ID_ORG'=>$parents[$m[1]]);
        }
        return true;
    }
    function db_close() {}
}
function numberformat($n) {return number_format($n,2,',','.').' €';}
$db=new PDO('sqlite::memory:',null,null,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));
$db->exec("CREATE TABLE repair_orders (id INTEGER PRIMARY KEY AUTOINCREMENT, legacy_id INTEGER UNIQUE, created_at TEXT, status TEXT DEFAULT 'received', version INTEGER DEFAULT 1, customer_name TEXT DEFAULT '', customer_email TEXT DEFAULT '', customer_phone TEXT DEFAULT '', product_id INTEGER, product_name TEXT DEFAULT '', product_reference TEXT DEFAULT '', total_cents INTEGER, details_json TEXT, access_mode TEXT DEFAULT 'not_provided', access_cipher TEXT, staff_mail TEXT DEFAULT 'pending', customer_mail TEXT DEFAULT 'pending', copy_mail TEXT DEFAULT 'pending', mail_updated_at TEXT, updated_at TEXT, updated_by INTEGER)");
$db->exec('CREATE TABLE pedidos (ID INTEGER PRIMARY KEY,prod_id INTEGER,nome_completo TEXT,telefone TEXT,email TEXT,NIF TEXT,morada TEXT,cp TEXT,localidade TEXT)');
$db->exec('CREATE TABLE products_families_obj (ID_PROD INTEGER,ID_ORG INTEGER)');
$p=array('prod_id'=>123371,'fam_id'=>86,'prod_nome'=>'Reparação lente','prod_ref'=>'REP-LENTE','Ref_Ean'=>'123','prod_precos'=>array('PVP'=>29.90,'IVA'=>23));
$q=dotbright_repair_quote($p);$q['warranty_months']=24;$q['warranty']='24 meses';$q['service']=array('time'=>'1 hora','part'=>'Peça original','part_detail'=>'Descrição curta & confirmação','part_icon'=>'shield','warranty_months'=>24);
$in=array('r_nome'=>'Cliente <script>','r_tel'=>'912345678','r_email'=>'teste@example.test','r_nif'=>'123456789','r_recolha'=>'Rua A','r_cp'=>'4470-151','r_cidade'=>'Maia','r_nome2'=>'Empresa','r_morada2'=>'Rua B','r_cp2'=>'4000-001','r_nif2'=>'999999999','r_periodo'=>'1','m_termos'=>'1','m_termos1'=>'1','r_unlock'=>'TEST-CODE','m_padrao'=>'1,2,3,6');
putenv('DOTBRIGHT_REPAIR_ACCESS_KEY');
ro_throws(function()use($p,$q,$in,$db){dotbright_repair_order_create($p,array(),$q,$in,$db);},'Access data accepted without encryption key.');
ro_assert((int)$db->query('SELECT COUNT(*) FROM repair_orders')->fetchColumn()===0,'Failed encryption left an incomplete row.');
putenv('DOTBRIGHT_REPAIR_ACCESS_KEY='.base64_encode(random_bytes(32)));
$id=dotbright_repair_order_create($p,array(),$q,$in,$db);
$row=dotbright_repair_order_find($db,$id);$d=json_decode($row['details_json'],true);
ro_assert($row['total_cents']===3480 && $d['quote']['warranty']==='24 meses' && $d['quote']['service']['time']==='1 hora','Quote snapshot incorrect.');
ro_assert($d['customer']['r_nome2']==='Empresa' && $d['customer']['r_morada2']==='Rua B' && $d['customer']['r_entrega']==='Rua A' && $d['customer']['period']==='Manhã' && $d['consents']['terms']===true,'Full customer, billing, address or consent data missing.');
ro_assert(strpos($row['details_json'],'TEST-CODE')===false && strpos($row['access_cipher'],'TEST-CODE')===false,'Unlock data stored in clear text.');
ro_assert(dotbright_repair_order_decrypt($row['access_cipher'])===array('code'=>'TEST-CODE','pattern'=>'1,2,3,6'),'Encrypted data roundtrip failed.');
$bad=$row['access_cipher'];$bytes=base64_decode(substr($bad,3));$bytes[30]=chr(ord($bytes[30])^1);$bad='v1:'.base64_encode($bytes);
ro_throws(function()use($bad){dotbright_repair_order_decrypt($bad);},'Tampered ciphertext accepted.');
$originalKey=getenv('DOTBRIGHT_REPAIR_ACCESS_KEY');putenv('DOTBRIGHT_REPAIR_ACCESS_KEY='.base64_encode(random_bytes(32)));
ro_throws(function()use($row){dotbright_repair_order_decrypt($row['access_cipher']);},'Wrong key accepted.');putenv('DOTBRIGHT_REPAIR_ACCESS_KEY='.$originalKey);
$normal=$p;$normal['fam_id']=10;
ro_throws(function()use($normal,$q,$in,$db){dotbright_repair_order_create($normal,array(),$q,$in,$db);},'Normal product accepted.');
foreach (array(array('r_email'=>'invalid'),array('m_termos'=>'0'),array('r_nome'=>array()),array('r_unlock'=>str_repeat('a',11))) as $badInput) ro_throws(function()use($p,$q,$in,$badInput,$db){dotbright_repair_order_create($p,array(),$q,array_merge($in,$badInput),$db);},'Invalid request accepted.');
$p['prod_nome']='Changed catalogue';$q['total']=999;
ro_assert(dotbright_repair_order_find($db,$id)['product_name']==='Reparação lente','Catalogue change affected stored order.');
dotbright_repair_order_mail_result($id,'staff','failed',$db);
ro_assert(dotbright_repair_order_find($db,$id)['staff_mail']==='failed','Mail failure not retained.');
ro_assert(dotbright_repair_order_find($db,$id)['total_cents']===3480,'Mail failure damaged saved order.');
dotbright_repair_order_update($db,$id,1,'review',7);
ro_assert(dotbright_repair_order_find($db,$id)['status']==='review','Status not saved.');
ro_throws(function()use($db,$id){dotbright_repair_order_update($db,$id,1,'closed',8);},'Stale update overwritten.');
ro_throws(function()use($db,$id){dotbright_repair_order_update($db,$id,2,'invalid',8);},'Invalid status accepted.');
ro_assert(dotbright_repair_orders_search($db,'REP-'.$id,'review',1)['total']===1,'Number/status search failed.');
foreach(array('teste@example.test','912345678','REP-LENTE','Cliente') as $term) ro_assert(dotbright_repair_orders_search($db,$term,'',1)['total']===1,'Search failed: '.$term);
ro_assert(dotbright_repair_orders_search($db,"%' OR 1=1 --",'',1)['total']===0,'Search escaped literal treated as SQL.');
for($i=0;$i<31;$i++) {$record=dotbright_repair_order_payload($p,$q,array_merge($in,array('r_unlock'=>'','m_padrao'=>'')));dotbright_repair_order_insert($db,$record);}
ro_assert(count(dotbright_repair_orders_search($db,'','',1)['rows'])===30 && count(dotbright_repair_orders_search($db,'','',2)['rows'])===2,'Pagination failed.');
$db->exec("INSERT INTO pedidos (ID,prod_id,nome_completo,telefone,morada,cp) VALUES (93,123371,'Antigo','912000000','Rua antiga','4000'),(94,999,'Normal','913000000','Rua','5000')");
$db->exec('INSERT INTO products_families_obj VALUES (123371,86),(999,10)');
$import=dotbright_repair_order_import_legacy($db);
ro_assert($import['imported']===1 && $import['skipped']===1,'Legacy family filter failed.');
ro_assert(dotbright_repair_order_import_legacy($db)['imported']===0,'Legacy import created duplicate.');
$legacy=$db->query('SELECT * FROM repair_orders WHERE legacy_id=93')->fetch(PDO::FETCH_ASSOC);
ro_assert($legacy['created_at']===null && $legacy['total_cents']===null && $legacy['product_name']==='' && !isset(json_decode($legacy['details_json'],true)['quote']),'Legacy import invented historical data.');
ro_assert(!dotbright_repair_order_can_manage(0,array(8=>1)) && !dotbright_repair_order_can_manage(7,array(8=>0)) && dotbright_repair_order_can_manage(7,array(8=>1)),'Management permission incorrect.');
ro_assert(!dotbright_repair_order_can_reveal(7,array(8=>1)) && dotbright_repair_order_can_reveal(7,array(8=>1,9=>1)),'Access reveal permission incorrect.');
$_SESSION=array();$token=dotbright_repair_order_csrf();ro_assert(dotbright_repair_order_verify_csrf($token) && !dotbright_repair_order_verify_csrf('bad'),'CSRF verification failed.');
// Render the actual admin detail/list views. Secrets must not be embedded before explicit reveal.
$ro_csrf=$token;$ro_labels=dotbright_repair_order_statuses();$ro_error=$ro_flash='';$ro_id=$id;$ro_row=dotbright_repair_order_find($db,$id);$ro_access=null;$ro_can_reveal=true;
ob_start();include __DIR__.'/../admin/reparacoes/view.inc.php';$html=ob_get_clean();
ro_assert(strpos($html,'Cliente &lt;script&gt;')!==false && strpos($html,'TEST-CODE')===false && strpos($html,$row['access_cipher'])===false && strpos($html,'Descrição curta &amp; confirmação')!==false,'Detail escaped data incorrectly or exposed secrets.');
ro_assert(strpos($html,'action="pagina.php"')!==false && strpos($html,'href="list.php"')!==false && strpos($html,'repair_orders.php')===false,'Detail must submit to the dedicated Repairs page and return to its list.');
$ro_access=dotbright_repair_order_decrypt($row['access_cipher']);ob_start();include __DIR__.'/../admin/reparacoes/view.inc.php';$html=ob_get_clean();ro_assert(strpos($html,'TEST-CODE')!==false,'Explicit access reveal not rendered.');
$ro_row=null;$ro_id=0;$ro_term=$ro_status='';$ro_result=dotbright_repair_orders_search($db,'','',1);ob_start();include __DIR__.'/../admin/reparacoes/view.inc.php';$html=ob_get_clean();ro_assert(strpos($html,'Seguinte')!==false && strpos($html,'TEST-CODE')===false,'List pagination/secrecy failed.');
ro_assert(strpos($html,'action="list.php"')!==false && strpos($html,'href="pagina.php?id=')!==false && strpos($html,'repair_orders.php')===false,'Search, import and detail links must stay inside the Repairs folder.');
require_once __DIR__.'/../admin/repair_orders_widget.inc.php';
$dashboard=dotbright_repair_orders_dashboard(7,array(8=>1),true,$db);
ro_assert(substr_count($dashboard,'<tr>')===10 && strpos($dashboard,'Últimos pedidos de reparação')!==false,'Dashboard must show ten latest repair requests in a separate panel.');
ro_assert(strpos($dashboard,'reparacoes/pagina.php?id=')!==false && strpos($dashboard,'Ver todos os pedidos de reparação')!==false && strpos($dashboard,'encomendas/')===false,'Dashboard links point to shop orders instead of repair requests.');
ro_assert(strpos($dashboard,'TEST-CODE')===false && strpos($dashboard,'Cliente &lt;script&gt;')!==false,'Dashboard exposes access data or unescaped customer text.');
$latest=(int)$db->query('SELECT MAX(id) FROM repair_orders')->fetchColumn();
ro_assert(strpos($dashboard,'REP-'.$latest)!==false && strpos($dashboard,'REP-'.$id.'</a>')===false,'Dashboard must select newest IDs, limited to ten.');
ro_assert(dotbright_repair_orders_dashboard(7,array(9=>1),true,$db)==='' && dotbright_repair_orders_dashboard(0,array(8=>1),true,$db)==='' && dotbright_repair_orders_dashboard(7,array(8=>1),false,$db)==='','Dashboard permission/shop guard failed.');
$empty=new PDO('sqlite::memory:',null,null,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));
$unavailable=dotbright_repair_orders_dashboard(7,array(8=>1),true,$empty);
ro_assert(strpos($unavailable,'Não foi possível carregar')!==false && strpos($unavailable,'Ver todos os pedidos')!==false,'Missing migration must not break the homepage.');
$schema=$db->query("SELECT sql FROM sqlite_master WHERE name='repair_orders'")->fetchColumn();$empty->exec($schema);
ro_assert(strpos(dotbright_repair_orders_dashboard(7,array(8=>1),true,$empty),'Ainda não existem pedidos')!==false,'Empty dashboard message missing.');
echo "PASS: full repair snapshots, encrypted access, input/family validation, mail state, immutable values, search/pagination, status concurrency, legacy import/deduplication, permissions, CSRF, BO rendering and latest-repairs dashboard. SQLite memory only.\n";
