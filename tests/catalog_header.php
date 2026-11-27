<?php
// php tests/catalog_header.php — isolated fixtures, no production database writes.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require dirname(__DIR__).'/core/catalog_header.php';
function check($condition, $label) {
    if (!$condition) throw new RuntimeException($label);
    echo 'PASS '.$label."\n";
}
function str2SQLstr($value) { return "'".addslashes($value)."'"; }
function db_opendb_conection($host, $user, $pass) { return true; }
function db_closedb_conection($db) {}
// In the BO, the shared-image helper leaves non-product image URLs unchanged.
function dotbright_image_url($url) { return $url; }
function Runquery($db, $database, $sql) { $GLOBALS['saved_sql']=$sql; return !empty($GLOBALS['save_ok']); }
class DataObj {
    public $db_data;
    function db_open($db, $database, $sql) {
        $GLOBALS['read_sql']=$sql;
        $this->db_data=$GLOBALS['fixture_row'];
        if (isset($GLOBALS['fixture_tree']) && preg_match('/P.ID=(\d+)/', $sql, $match)) {
            preg_match("/PL.Lang='([^']+)'/", $sql, $language);
            $key=$match[1].'_'.(isset($language[1]) ? $language[1] : '');
            $this->db_data=isset($GLOBALS['fixture_tree'][$key]) ? $GLOBALS['fixture_tree'][$key] : array();
            $GLOBALS['tree_reads']++;
        }
        return true;
    }
    function EOF() { return empty($this->db_data); }
    function db_close() {}
}
$GLOBALS['site_http_url']='https://example.test/dotbright2/';
$GLOBALS['fixture_row']=array('Cabecalho'=>'Categoria 163', 'catalogo_titulo'=>'', 'catalogo_texto'=>'', 'catalogo_banner'=>'');
$GLOBALS['save_ok']=true;
check(dotbright_render_catalog_header(array()) === '', 'no configured header renders no empty block');
$row=$GLOBALS['fixture_row']; $row['catalogo_texto']="Primeira linha\n<script>alert(1)</script>";
$html=dotbright_render_catalog_header($row);
check(strpos($html, '<h1>Categoria 163</h1>') !== false, 'empty custom title falls back to this category name');
check(strpos($html, '<script>') === false && strpos($html, '&lt;script&gt;') !== false && strpos($html, '<br') !== false, 'text is escaped and preserves line breaks');
$row['catalogo_titulo']='Título independente'; $row['catalogo_banner']='163_catalogo_pt_0123456789abcdef.png';
$html=dotbright_render_catalog_header($row);
check(strpos($html, 'Título independente') !== false && strpos($html, '<img') !== false, 'banner and custom title render together');
check(dotbright_catalog_banner_url('../bad.php') === '' && dotbright_catalog_banner_url('163_1.png') === '', 'banner cannot reference paths or unrelated category images');
check(dotbright_catalog_banner_url('163_catalogo_pt_0123456789abcdef.png') === 'https://example.test/dotbright2/files/products_families/163_catalogo_pt_0123456789abcdef.png', 'BO preview uses an absolute URL even when the image helper returns its input');
dotbright_catalog_header(163, 'pt');
check(strpos($GLOBALS['read_sql'], 'P.ID=163') !== false && strpos($GLOBALS['read_sql'], 'P.visivel=1') !== false && strpos($GLOBALS['read_sql'], "PL.Lang='pt'") !== false, 'reads the selected visible category in the requested language');
check(dotbright_save_catalog_header(163, 'en', array(), array()), 'legacy saves without new form fields leave the header untouched');
$post=array('catalogo_header_present'=>1,'catalogo_titulo'=>"João's title",'catalogo_texto'=>'Texto');
check(dotbright_save_catalog_header(163, 'en', $post, array()), 'saves independent language content');
check(strpos($GLOBALS['saved_sql'], "Lang='en'") !== false && strpos($GLOBALS['saved_sql'], "João\\'s title") !== false, 'language and quoted title persist safely');
$GLOBALS['fixture_row']['catalogo_banner']='163_catalogo_pt_0123456789abcdef.png';
dotbright_save_catalog_header(163, 'pt', $post, array());
check(strpos($GLOBALS['saved_sql'], '163_catalogo_pt_0123456789abcdef.png') !== false, 'saving text preserves an existing banner');
$post['catalogo_banner_apagar']=1;dotbright_save_catalog_header(163, 'pt', $post, array());
check(strpos($GLOBALS['saved_sql'], "catalogo_banner=''") !== false, 'explicit remove clears the banner');
$GLOBALS['save_ok']=false;
check(!dotbright_save_catalog_header(163, 'pt', $post, array()), 'database errors are reported as failed saves');
$rejected=false;
try { dotbright_catalog_header_validate(array('catalogo_titulo'=>str_repeat('x',256)), array()); } catch (InvalidArgumentException $e) { $rejected=true; }
check($rejected, 'overlong titles are rejected before saving');
$rejected=false;
try { dotbright_catalog_banner_validate(array('error'=>UPLOAD_ERR_OK,'tmp_name'=>__FILE__)); } catch (InvalidArgumentException $e) { $rejected=true; }
check($rejected, 'a renamed script is rejected as an image');
check(dotbright_catalog_banner_validate(array('error'=>UPLOAD_ERR_NO_FILE)) === '', 'optional banner may be left empty');
$GLOBALS['fixture_row']=$row;
$_GET['cat']=163;
$GLOBALS['site_lang']='en';
ob_start();$banner=catalogo_banner();$intro=catalogo_titulo_texto();$unexpectedOutput=ob_get_clean();
check($unexpectedOutput === '' && strpos($banner, '<img') !== false && strpos($banner, '<h1>') === false, 'banner template function returns only the image without echoing');
check(strpos($intro, '<h1>Título independente</h1>') !== false && strpos($intro, '<img') === false && strpos($intro, '&lt;script&gt;') !== false, 'intro template function returns escaped title and text separately');
check(strpos($GLOBALS['read_sql'], 'P.ID=163') !== false && strpos($GLOBALS['read_sql'], "PL.Lang='en'") !== false, 'template functions resolve the current category and language');
unset($_GET['cat']);
check(catalogo_banner() === '' && catalogo_titulo_texto() === '', 'template functions render nothing when no category is selected');
$GLOBALS['fixture_tree']=array(
    '163_pt'=>array('catalogo_category_id'=>163,'catalogo_parent_id'=>7,'Cabecalho'=>'Subcategoria','catalogo_titulo'=>'Título próprio','catalogo_texto'=>'  ','catalogo_banner'=>''),
    '7_pt'=>array('catalogo_category_id'=>7,'catalogo_parent_id'=>1,'catalogo_titulo'=>'Título pai','catalogo_texto'=>'Texto pai','catalogo_banner'=>''),
    '1_pt'=>array('catalogo_category_id'=>1,'catalogo_parent_id'=>0,'catalogo_titulo'=>'Título principal','catalogo_texto'=>'Texto principal','catalogo_banner'=>'1_catalogo_pt_0123456789abcdef.png'),
    '163_en'=>array('catalogo_category_id'=>163,'catalogo_parent_id'=>7),
    '7_en'=>array('catalogo_category_id'=>7,'catalogo_parent_id'=>0,'catalogo_titulo'=>'English parent','catalogo_texto'=>'English text','catalogo_banner'=>'7_catalogo_en_0123456789abcdef.png')
);
$GLOBALS['tree_reads']=0;
$inherited=dotbright_get_catalog_header(163,'pt');
check($inherited['catalogo_titulo']==='Título próprio' && $inherited['catalogo_texto']==='Texto pai' && $inherited['catalogo_banner']==='1_catalogo_pt_0123456789abcdef.png', 'inherits each empty field from the nearest ancestor while preserving own content');
check($inherited['Cabecalho']==='Subcategoria', 'category name fallback remains the selected subcategory name');
$english=dotbright_get_catalog_header(163,'en');
check($english['catalogo_titulo']==='English parent' && $english['catalogo_texto']==='English text', 'missing child translation can inherit in the same language without mixing languages');
$GLOBALS['fixture_tree']['163_pt']['catalogo_titulo']='';
$GLOBALS['fixture_tree']['163_pt']['catalogo_parent_id']=0;
$withoutParent=dotbright_get_catalog_header(163,'pt');
check(trim($withoutParent['catalogo_texto'])==='' && $withoutParent['catalogo_titulo']==='', 'root category without configured content remains empty');
$GLOBALS['fixture_tree']['163_pt']['catalogo_parent_id']=7;
$GLOBALS['fixture_tree']['7_pt']['catalogo_parent_id']=163;
$GLOBALS['tree_reads']=0;
dotbright_get_catalog_header(163,'pt');
check($GLOBALS['tree_reads']===2, 'cyclic category hierarchy cannot cause an infinite loop');
unset($GLOBALS['fixture_tree']['7_pt']);
$GLOBALS['tree_reads']=0;
$missingParent=dotbright_get_catalog_header(163,'pt');
check($GLOBALS['tree_reads']===2 && $missingParent['catalogo_titulo']==='', 'missing or hidden parent does not supply content');
unset($GLOBALS['fixture_tree']);
// Exercise the actual legacy save function, including its hidden-editor behaviour.
$source=file_get_contents(dirname(__DIR__).'/admin/products/funcoes.inc.php');
$start=strpos($source, 'function ChangeFamLinguas(');
$end=strpos($source, 'function altera_imagem(', $start);
eval(substr($source, $start, $end-$start));
$GLOBALS['admin_lang']='pt';
$GLOBALS['save_ok']=true;
$GLOBALS['fixture_row']=array('ID'=>1,'SEO_img'=>'','ICON_img'=>'','Texto'=>"Texto d'origem");
$_POST=array('prod_txt1'=>''); $_FILES=array();
$previousErrors=error_reporting(E_ERROR | E_PARSE);
ChangeFamLinguas(163,'Nome','','','');
check(strpos($GLOBALS['saved_sql'], "Texto='Texto d\\'origem'") !== false, 'hidden legacy text editor preserves its existing value');
$_POST['texto']='';
ChangeFamLinguas(163,'Nome','','','');
check(strpos($GLOBALS['saved_sql'], "Texto=''") !== false, 'an explicitly cleared legacy text field is saved');
error_reporting($previousErrors);
if (getenv('CATALOG_HEADER_PREVIEW_FILE')) file_put_contents(getenv('CATALOG_HEADER_PREVIEW_FILE'), $html);
