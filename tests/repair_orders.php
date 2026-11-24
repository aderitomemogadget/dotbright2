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
$in=array('r_avaria'=>"Ecrã partido & sem toque.\nNão liga <teste>.",'r_nome'=>'Cliente <script>','r_tel'=>'912345678','r_email'=>'teste@example.test','r_nif'=>'123456789','r_recolha'=>'Rua A','r_cp'=>'4470-151','r_cidade'=>'Maia','r_nome2'=>'Empresa','r_morada2'=>'Rua B','r_cp2'=>'4000-001','r_nif2'=>'999999999','r_periodo'=>'1','m_termos'=>'1','m_termos1'=>'1','r_unlock'=>'TEST-CODE','m_padrao'=>'1,2,3,6');
putenv('DOTBRIGHT_REPAIR_ACCESS_KEY');
ro_throws(function()use($p,$q,$in,$db){dotbright_repair_order_create($p,array(),$q,$in,$db);},'Access data accepted without encryption key.');
ro_assert((int)$db->query('SELECT COUNT(*) FROM repair_orders')->fetchColumn()===0,'Failed encryption left an incomplete row.');
putenv('DOTBRIGHT_REPAIR_ACCESS_KEY='.base64_encode(random_bytes(32)));
$id=dotbright_repair_order_create($p,array(),$q,$in,$db);
$row=dotbright_repair_order_find($db,$id);$d=json_decode($row['details_json'],true);
ro_assert($row['total_cents']===3990 && $d['quote']['shipping_gross']===10 && $d['quote']['shipping_net']===8.13 && $d['quote']['vat']===7.46 && $d['quote']['warranty']==='24 meses' && $d['quote']['service']['time']==='1 hora','Quote snapshot incorrect.');
ro_assert($d['customer']['r_nome2']==='Empresa' && $d['customer']['r_morada2']==='Rua B' && $d['customer']['r_entrega']==='Rua A' && $d['customer']['period']==='Manhã' && $d['consents']['terms']===true,'Full customer, billing, address or consent data missing.');
ro_assert($d['customer']['r_avaria'] === $in['r_avaria'], 'Fault description was not saved in the request snapshot.');
ro_assert(strpos($row['details_json'],'TEST-CODE')===false && strpos($row['access_cipher'],'TEST-CODE')===false,'Unlock data stored in clear text.');
ro_assert(dotbright_repair_order_decrypt($row['access_cipher'])===array('code'=>'TEST-CODE','pattern'=>'1,2,3,6'),'Encrypted data roundtrip failed.');
$bad=$row['access_cipher'];$bytes=base64_decode(substr($bad,3));$bytes[30]=chr(ord($bytes[30])^1);$bad='v1:'.base64_encode($bytes);
ro_throws(function()use($bad){dotbright_repair_order_decrypt($bad);},'Tampered ciphertext accepted.');
$originalKey=getenv('DOTBRIGHT_REPAIR_ACCESS_KEY');putenv('DOTBRIGHT_REPAIR_ACCESS_KEY='.base64_encode(random_bytes(32)));
ro_throws(function()use($row){dotbright_repair_order_decrypt($row['access_cipher']);},'Wrong key accepted.');putenv('DOTBRIGHT_REPAIR_ACCESS_KEY='.$originalKey);
$normal=$p;$normal['fam_id']=10;
ro_throws(function()use($normal,$q,$in,$db){dotbright_repair_order_create($normal,array(),$q,$in,$db);},'Normal product accepted.');
foreach (array(array('r_avaria'=>''),array('r_avaria'=>'   '),array('r_avaria'=>str_repeat('ç',2001)),array('r_avaria'=>array()),array('r_email'=>'invalid'),array('m_termos'=>'0'),array('r_nome'=>array()),array('r_unlock'=>str_repeat('a',11))) as $badInput) ro_throws(function()use($p,$q,$in,$badInput,$db){dotbright_repair_order_create($p,array(),$q,array_merge($in,$badInput),$db);},'Invalid request accepted.');
$p['prod_nome']='Changed catalogue';$q['total']=999;
ro_assert(dotbright_repair_order_find($db,$id)['product_name']==='Reparação lente','Catalogue change affected stored order.');
dotbright_repair_order_mail_result($id,'staff','failed',$db);
ro_assert(dotbright_repair_order_find($db,$id)['staff_mail']==='failed','Mail failure not retained.');
ro_assert(dotbright_repair_order_find($db,$id)['total_cents']===3990,'Mail failure damaged saved order.');
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
ro_assert(strpos($html, 'Ecrã partido &amp; sem toque.') !== false && strpos($html, 'Não liga &lt;teste&gt;.') !== false, 'Fault description must be displayed safely in the BO detail.');
ro_assert(strpos($html,'action="pagina.php"')!==false && strpos($html,'href="list.php"')!==false && strpos($html,'repair_orders.php')===false,'Detail must submit to the dedicated Repairs page and return to its list.');
ro_assert(substr_count($html,'role="tab"')===4 && substr_count($html,'role="tabpanel"')===4 && strpos($html,'class="ro-status-form"')!==false,'Compact detail must keep four data tabs and the status form.');
$ro_access=dotbright_repair_order_decrypt($row['access_cipher']);ob_start();include __DIR__.'/../admin/reparacoes/view.inc.php';$html=ob_get_clean();ro_assert(strpos($html,'TEST-CODE')!==false,'Explicit access reveal not rendered.');
$ro_row=null;$ro_id=0;$ro_term=$ro_status='';$ro_result=dotbright_repair_orders_search($db,'','',1);ob_start();include __DIR__.'/../admin/reparacoes/view.inc.php';$html=ob_get_clean();ro_assert(strpos($html,'Seguinte')!==false && strpos($html,'TEST-CODE')===false,'List pagination/secrecy failed.');
ro_assert(strpos($html,'action="list.php"')!==false && strpos($html,'href="pagina.php?id=')!==false && strpos($html,'repair_orders.php')===false,'Search, import and detail links must stay inside the Repairs folder.');
ro_assert(strpos($html,'id="content"')===false && strpos($html,'<nav')===false && strpos($html,'class="ro-pagination"')!==false,'Repairs must reuse the BO content wrapper and avoid the legacy global nav styles.');
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
// Historical requests keep their old RRE charge; the BO must not recalculate them.
$history=new PDO('sqlite::memory:',null,null,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));$history->exec($schema);
$old_quote=dotbright_repair_quote($p);
$old_quote['shipping_gross']=4.90;$old_quote['shipping_net']=3.98;$old_quote['vat']=6.51;$old_quote['total']=34.80;
$old_id=dotbright_repair_order_insert($history,dotbright_repair_order_payload($p,$old_quote,array_merge($in,array('r_unlock'=>'','m_padrao'=>''))));
ro_assert(dotbright_repair_quote($p)['total']===39.90,'Current low-price repair must use the new RRE rate.');
$ro_row=dotbright_repair_order_find($history,$old_id);$ro_access=null;
ob_start();include __DIR__.'/../admin/reparacoes/view.inc.php';$old_html=ob_get_clean();
ro_assert($ro_row['total_cents']===3480 && strpos($old_html,'4,90 €')!==false && strpos($old_html,'34,80 €')!==false,'Historical BO amounts were recalculated with the new RRE rate.');
$eligible=$p;$eligible['prod_precos']['PVP']=65;
$eligible_quote=dotbright_repair_quote($eligible);
$eligible_id=dotbright_repair_order_create($eligible,array(),$eligible_quote,array_merge($in,array('r_unlock'=>'','m_padrao'=>'')),$history);
$ro_row=dotbright_repair_order_find($history,$eligible_id);
ro_assert($ro_row['total_cents']===6500 && json_decode($ro_row['details_json'],true)['quote']['shipping_gross']===0,'Free RRE rate not stored at the inclusive 65 euro threshold.');
ob_start();include __DIR__.'/../admin/reparacoes/view.inc.php';$eligible_html=ob_get_clean();
ro_assert(strpos($eligible_html,'65,00 €')!==false && strpos($eligible_html,'0,00 €')!==false,'BO must show stored free RRE shipping.');
$ro_row=$legacy;$ro_access=null;
ob_start();include __DIR__.'/../admin/reparacoes/view.inc.php';$legacy_html=ob_get_clean();
ro_assert(!isset(json_decode($legacy['details_json'],true)['customer']['r_avaria']) && strpos($legacy_html,'Descrição da avaria')!==false,'Older requests without fault descriptions must remain readable without inventing data.');
// New form: delivery choice/notes, four access options and two-field billing.
$modern_db=new PDO('sqlite::memory:',null,null,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));$modern_db->exec($schema);
$case_quote=dotbright_repair_quote($p);
$modern=array_merge($in,array('r_form_version'=>'2','r_acesso'=>'remove','r_entrega_tipo'=>'same','r_nome2'=>'','r_nif2'=>'','r_recolha_obs'=>"Portaria & campainha\nAté às 18h.",'r_entrega'=>'STALE ADDRESS','r_entrega_cp'=>'STALE CP','r_entrega_cidade'=>'STALE CITY','r_entrega_obs'=>'STALE NOTE'));
$key=getenv('DOTBRIGHT_REPAIR_ACCESS_KEY');putenv('DOTBRIGHT_REPAIR_ACCESS_KEY');
foreach(array('remove','refuse') as $mode) {
    $order_id=dotbright_repair_order_create($p,array(),$case_quote,array_merge($modern,array('r_acesso'=>$mode)),$modern_db);
    $new_row=dotbright_repair_order_find($modern_db,$order_id);$new_details=json_decode($new_row['details_json'],true);$new_data=$new_details['customer'];
    ro_assert($new_row['access_mode']===$mode && $new_row['access_cipher']===null && $new_details['form_version']===2,'Non-secret access choices must be distinct and work without encryption configuration.');
    ro_assert($new_data['r_entrega']===$in['r_recolha'] && $new_data['r_entrega_cp']===$in['r_cp'] && $new_data['r_entrega_cidade']===$in['r_cidade'] && $new_data['r_entrega_obs']==='' && $new_data['r_recolha_obs']===$modern['r_recolha_obs'],'Same delivery must copy collection address and ignore stale alternative inputs.');
    ro_assert($new_data['r_nome2']===$in['r_nome'] && $new_data['r_nif2']===$in['r_nif'] && !isset($new_data['r_morada2']) && !isset($new_data['r_cp2']),'New billing must contain only name/NIF with customer fallbacks.');
    ro_assert(strpos($new_row['details_json'],'TEST-CODE')===false && strpos($new_row['details_json'],'1,2,3,6')===false,'Inactive access secrets leaked into the new request.');
}
ro_throws(function()use($modern,$p,$case_quote,$modern_db){dotbright_repair_order_create($p,array(),$case_quote,array_merge($modern,array('r_acesso'=>'code')),$modern_db);},'Secret code accepted without configured key.');
putenv('DOTBRIGHT_REPAIR_ACCESS_KEY='.$key);
foreach(array('code'=>array('code'=>'TEST-CODE'),'pattern'=>array('pattern'=>'12369')) as $mode=>$expected) {
    $order_id=dotbright_repair_order_create($p,array(),$case_quote,array_merge($modern,array('r_acesso'=>$mode,'m_padrao'=>'12369')),$modern_db);
    $new_row=dotbright_repair_order_find($modern_db,$order_id);
    ro_assert($new_row['access_mode']===$mode && dotbright_repair_order_decrypt($new_row['access_cipher'])===$expected,'Access selection must encrypt only the chosen secret.');
}
$different=array_merge($modern,array('r_acesso'=>'refuse','r_entrega_tipo'=>'different','r_entrega'=>'Rua entrega <teste>','r_entrega_cp'=>'1000-001','r_entrega_cidade'=>'Lisboa','r_entrega_obs'=>"Entregar à tarde.\nPorta B.",'r_nome2'=>'Empresa nova','r_nif2'=>'999123456'));
$order_id=dotbright_repair_order_create($p,array(),$case_quote,$different,$modern_db);$new_row=dotbright_repair_order_find($modern_db,$order_id);$new_data=json_decode($new_row['details_json'],true)['customer'];
foreach(array('r_entrega','r_entrega_cp','r_entrega_cidade','r_entrega_obs','r_recolha_obs','r_nome2','r_nif2') as $field) ro_assert($new_data[$field]===$different[$field],'Delivery/billing field not preserved: '.$field);
foreach(array(array('r_acesso'=>''),array('r_acesso'=>'invalid'),array('r_acesso'=>'code','r_unlock'=>''),array('r_acesso'=>'pattern','m_padrao'=>'abc'),array('r_entrega_cp'=>''),array('r_entrega_cidade'=>''),array('r_entrega'=>''),array('r_entrega_tipo'=>'invalid'),array('r_recolha_obs'=>str_repeat('x',1001))) as $bad) ro_throws(function()use($p,$case_quote,$different,$bad,$modern_db){dotbright_repair_order_create($p,array(),$case_quote,array_merge($different,$bad),$modern_db);},'New-form invalid or missing conditional fields accepted.');
$ro_row=$new_row;$ro_access=null;
ob_start();include __DIR__.'/../admin/reparacoes/view.inc.php';$modern_html=ob_get_clean();
ro_assert(strpos($modern_html,'Rua entrega &lt;teste&gt;')!==false && strpos($modern_html,'Lisboa')!==false && strpos($modern_html,'Portaria &amp; campainha')!==false && strpos($modern_html,'Não pretendo facultar o acesso.')!==false,'New address/notes/access choice missing or unescaped in the BO.');
ro_assert(strpos($modern_html,'Rua B')===false && strpos($modern_html,'4000-001')===false,'Old billing address fields must not appear for new requests.');
$ro_row=dotbright_repair_order_find($db,$id);$ro_access=null;
ob_start();include __DIR__.'/../admin/reparacoes/view.inc.php';$old_html=ob_get_clean();
ro_assert(strpos($old_html,'Rua B')!==false && strpos($old_html,'4000-001')!==false,'Historical billing address must remain visible.');
$current=array_merge($modern,array('r_form_version'=>'3','r_avaria'=>'   ','r_acesso'=>'refuse','r_nif'=>'STALE-CUSTOMER-NIF','r_nif2'=>'500000000'));
$current_id=dotbright_repair_order_create($p,array(),$case_quote,$current,$modern_db);
$current_row=dotbright_repair_order_find($modern_db,$current_id);$current_details=json_decode($current_row['details_json'],true);
ro_assert($current_details['form_version']===3 && $current_details['customer']['r_avaria']==='' && !isset($current_details['customer']['r_nif']) && $current_details['customer']['r_nif2']==='500000000','Specific repairs must allow blank notes and store only billing NIF.');
$blank_bill=dotbright_repair_order_payload($p,$case_quote,array_merge($current,array('r_nif2'=>'')));
ro_assert(json_decode($blank_bill['details_json'],true)['customer']['r_nif2']==='','Billing NIF must not inherit removed customer NIF.');
foreach(array('Diagnóstico/Orçamento gratuito iPhone','Diagnostico MacBook','Orçamento de reparação') as $name) {
    $diagnostic=array_merge($p,array('prod_nome'=>$name));
    ro_throws(function()use($diagnostic,$case_quote,$current){dotbright_repair_order_payload($diagnostic,$case_quote,$current);},'Diagnostic requests must retain required fault information.');
    dotbright_repair_order_payload($diagnostic,$case_quote,array_merge($current,array('r_avaria'=>'Não liga.')));
}
ro_assert(!dotbright_repair_fault_required(array_merge($p,array('prod_precos'=>array('PVP'=>0)))),'A zero price alone must not classify a specific repair as diagnostic.');
$ro_row=$current_row;$ro_access=null;
ob_start();include __DIR__.'/../admin/reparacoes/view.inc.php';$current_html=ob_get_clean();
preg_match('/<h3>Dados do cliente<\/h3>(.*?)<\/section>/s',$current_html,$current_client);
ro_assert(strpos($current_client[1],'NIF')===false && strpos($current_html,'500000000')!==false && strpos($current_html,'Informações adicionais sobre a avaria')!==false,'BO must omit customer NIF while keeping billing NIF and optional fault information.');

foreach(array('premium'=>'Compatível de qualidade premium','compatible'=>'Peça compatível') as $quality=>$expected_quality) {
    $quality_product=array_merge($p,array('prod_nome'=>$quality==='premium'?'Reparação OLED Premium - equivalente ao original':'Reparação ecrã equivalente ao original'));
    $quality_quote=dotbright_repair_quote($quality_product);
    $quality_id=dotbright_repair_order_create($quality_product,array(),$quality_quote,$current,$modern_db);
    $ro_row=dotbright_repair_order_find($modern_db,$quality_id);$ro_access=null;
    ro_assert(json_decode($ro_row['details_json'],true)['quote']['service']['part']===$expected_quality,'Stored quality differs from shared quote.');
    ob_start();include __DIR__.'/../admin/reparacoes/view.inc.php';$quality_bo=ob_get_clean();
    ro_assert(strpos($quality_bo,'<dt>Peça</dt><dd>'.$expected_quality.'</dd>')!==false && strpos($quality_bo,$quality_quote['service']['part_detail'])!==false,'BO must show the saved non-original quality and description.');
    $quality_product['prod_nome']='Reparação com peça original';dotbright_repair_quote($quality_product);
    ro_assert(dotbright_repair_order_find($modern_db,$quality_id)['details_json']===$ro_row['details_json'],'Catalogue quality changes must not rewrite historical orders.');
}

echo "PASS: full repair snapshots, encrypted access, input/family validation, mail state, immutable values, search/pagination, status concurrency, legacy import/deduplication, permissions, CSRF, BO rendering, optional notes, billing-only NIF, non-original quality snapshots and RRE rates/history. SQLite memory only.\n";
