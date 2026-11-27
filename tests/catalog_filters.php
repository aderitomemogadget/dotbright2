<?php
// Run: php tests/catalog_filters.php (no database or production writes).
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require dirname(__DIR__).'/core/return_filtros.php';

function check($condition, $message) {
    if (!$condition) throw new RuntimeException($message);
    echo 'PASS '.$message."\n";
}
function db_opendb_conection($host, $user, $pass) { return true; }
function db_real_escape_string($value) { return addslashes($value); }
class DataObj {
    public $db_data;
    private $rows = array();
    private $index = 0;
    function db_open($connection, $database, $sql) {
        $GLOBALS['filter_sql'] = $sql;
        $this->rows = $GLOBALS['filter_rows'];
        $this->index = 0;
        $this->db_data = isset($this->rows[0]) ? $this->rows[0] : array();
    }
    function EOF() { return $this->index >= count($this->rows); }
    function db_next() { $this->index++; $this->db_data = isset($this->rows[$this->index]) ? $this->rows[$this->index] : array(); }
    function db_close() {}
}
$GLOBALS['site_lang'] = 'pt';
$row = array('cat_id'=>3, 'valor_id'=>9, 'category_order'=>4, 'value_order'=>2,
    'category_name'=>'Filtrar por Marca', 'category_name2'=>'Marca pública',
    'values_name'=>'APPLE', 'values_name2'=>'Apple "Especial" </script>');
$GLOBALS['filter_rows'] = array($row, $row, array_merge($row, array(
    'cat_id'=>17, 'valor_id'=>44, 'category_name'=>'Filtro definido no BO', 'category_name2'=>'',
    'values_name'=>'Valor novo', 'values_name2'=>'')));
$json = getCategoriesProds(143608);
$filters = json_decode('['.$json.']', true);
check(count($filters) === 2, 'duplicate product assignments count once');
check($filters[0]['c_id'] === '3_9' && $filters[0]['cat_name'] === 'Marca pública', 'database IDs and public category names are preserved');
check($filters[0]['cat'] === 'Apple "Especial" </script>' && strpos($json, '</script>') === false, 'names round-trip safely in inline JSON');
check($filters[1]['cat_name'] === 'Filtro definido no BO' && $filters[1]['cat'] === 'Valor novo', 'arbitrary BO groups and values need no hardcoded mapping');
check($filters[0]['cat_order'] === 4 && $filters[0]['value_order'] === 2, 'BO group and value ordering is exported');
$sql = $GLOBALS['filter_sql'];
check(strpos($sql, 'FROM filters_products fp') !== false && strpos($sql, 'filters_products_fast') === false, 'reads product assignments rather than the search accelerator');
foreach (array('fp.prod_id=143608', 'fp.filtro_active=1', 'fp.filtro_area IN (0,2)', 'fc.category_active=1', 'fv.value_active=1', 'fcl.category_lang="pt"', 'fvl.values_lang="pt"') as $clause) {
    check(strpos($sql, $clause) !== false, 'query enforces '.$clause);
}
getCategoriesProds('143608 OR 1=1', 'en');
check(strpos($GLOBALS['filter_sql'], 'OR 1=1') === false && strpos($GLOBALS['filter_sql'], 'fcl.category_lang="en"') !== false, 'product IDs are integers and requested language is respected');
$GLOBALS['filter_rows'] = array();
check(getCategoriesProds(999) === '', 'products without assignments do not get invented filters');
