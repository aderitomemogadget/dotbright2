<?php
if (isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__) {
    exit();
}

function strong_words( $title, $searched_words_array) {
    foreach ($searched_words_array as $word){
        $lastPos = 0;
        $positions = array();
        while (($lastPos = stripos($title, $word, $lastPos))!== false) {
           $positions[] = $lastPos;
           $lastPos = $lastPos + strlen($word);
        }
        rsort($positions);
        foreach ($positions as $pos) {
            $title = strong_word($title , $word, $pos);
        }
    }
    $title = str_replace('#####','</strong>',$title);
    $title = str_replace('*****','<strong>',$title);
    return $title;
}

function strong_word($title , $word, $pos){
//ugly hack to not use <strong> , </strong> here directly, as it can get replaced if searched word contains charcters from strong
$title = substr_replace($title, '#####', $pos+strlen($word) , 0) ;
$title = substr_replace($title, '*****', $pos , 0) ;
return $title;
}
function pluralParaSingular($palavra) {
    // Lista de regras simples (incompleta, mas útil)
    $regras = [
        '/ões$/' => 'ão',     // Ex: leões → leão
        '/ães$/' => 'ão',     // Ex: pães → pão
        '/ais$/' => 'al',     // Ex: animais → animal
        '/eis$/' => 'el',     // Ex: papéis → papel
        '/is$/' => 'il',      // Ex: barris → barril
        '/res$/' => 'r',      // Ex: flores → flor
        '/ses$/' => 's',      // Ex: casas → casa (mas cuidado com exceções)
        '/zes$/' => 'z',      // Ex: cruzes → cruz
        '/s$/' => '',         // fallback: Ex: livros → livro
    ];

    foreach ($regras as $regex => $substituicao) {
        if (preg_match($regex, $palavra)) {
            return preg_replace($regex, $substituicao, $palavra);
        }
    }

    // Se nenhuma regra bater, retorna a palavra original
    return $palavra;
}
function searchProductsMemoryTable2($searchstr,$PRODS_LIMIT=999999)
{
    $db_link     = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
    $rs          = new DataObj();
    $PRODS_LIMIT = 0;
    if (!empty($vars) && array_key_exists('PRODS_LIMIT', $vars))
    {
        $PRODS_LIMIT = $vars['PRODS_LIMIT'];
    }

    $PRODS_LIMIT=1000;

    $prods = array();
    $searchstr2=iconv('UTF-8','ASCII//TRANSLIT',$searchstr);
    $array_palavras=explode(" ",$searchstr);
    $and='';
    $and2='';
    $i=0;
    while($i< count($array_palavras))
    {
        $k=$array_palavras[$i];
        if($i>0)$and .= " OR ";
        $and .= " ( STR like '$k' ) ";
        $and2 .= " ( STR like '$k%' ) ";
        $i++;
    }
    $t=0;
    $array_palavras2=explode(" ",$searchstr2);
    $sql = " SELECT ID_Prod,COUNT(ID_Prod) as QTD, Cabecalho FROM acelerador_pesquisa2 WHERE ( " . $and . " ) GROUP BY ID_Prod ORDER BY QTD DESC, Cabecalho ASC " . ($PRODS_LIMIT > 0 ? 'Limit ' . $PRODS_LIMIT : '');
    $rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
    if (!$rs->EOF())
    {
        while (!$rs->EOF())
        {
            $nome = $rs->db_data['Cabecalho'];
            $nome=strong_words( $nome, $array_palavras2);
            $prods[$rs->db_data['ID_Prod']] = $nome;
            $rs->db_next();
            $t++;
        }
    }
    $rs->db_close();

    if($t<1)
    {
        $sql = " SELECT ID_Prod,COUNT(ID_Prod) as QTD, Cabecalho FROM acelerador_pesquisa2 WHERE ( " . $and2 . " ) GROUP BY ID_Prod ORDER BY QTD DESC, Cabecalho ASC " . ($PRODS_LIMIT > 0 ? 'Limit ' . $PRODS_LIMIT : '');
        $rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
        if (!$rs->EOF())
        {
            while (!$rs->EOF())
            {
                $nome = $rs->db_data['Cabecalho'];
                $nome=strong_words( $nome, $array_palavras2);
                $prods[$rs->db_data['ID_Prod']] = $nome;
                $rs->db_next();
                $t++;
            }
        }
        $rs->db_close();
    }

    return $prods;
}
function searchProductsMemoryTable3($searchstr,$PRODS_LIMIT=0)
{
    $db_link     = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
    $res=Runquery($db_link,$GLOBALS['DB_name'],'SET SESSION group_concat_max_len=1000000');
    $rs          = new DataObj();
    $prods = array();

    if($PRODS_LIMIT == 0)
    {
        $PRODS_LIMIT=1000;
    }

    //$searchstr2=iconv('UTF-8','ASCII//TRANSLIT',$searchstr);
    //$searchstr2 = preg_replace('/[^a-zA-Z0-9_\.\,\-áÁàÀãÃâÂéÉèÈêÊíÍìÌîÎóÓòÒõÕôÔúÚùÙçÇ& ]/', '',$searchstr2);
    //$searchstr2 = filter_var($searchstr2, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH);
    //$searchstr2 = str_replace(array('.',',','-','_',','),' ',$searchstr2);


    $searchstr=trim($searchstr);



    $searchstr2 = mb_convert_encoding($searchstr, "UTF-8");
    $searchstr2 = preg_replace('/[^a-zA-Z0-9_\.\,\-áÁàÀãÃâÂéÉèÈêÊíÍìÌîÎóÓòÒõÕôÔúÚùÙçÇ& ]/', '',$searchstr2);
    $searchstr2 = filter_var($searchstr2, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH);
    $searchstr2 = str_replace(array('.',',','-','_',','),' ',$searchstr2);




    $array_palavras=explode(" ",$searchstr2);
    $and='';
    $and2='';
    $i=0;
    while($i< count($array_palavras))
    {
        $k=$array_palavras[$i];
        if($i>0)$and .= " OR ";
        if($i>0)$and2 .= " OR ";
        $and .= " ( STR like '$k' ) ";
        $and2 .= " ( STR like '$k%' ) ";
        $i++;
    }


    $array_palavras2=explode(" ",$searchstr2);
    $total=count($array_palavras2);
    //----------------------------
    //$sql = " SELECT ID_Prod,COUNT(ID_Prod) as QTD, Cabecalho FROM acelerador_pesquisa2 WHERE ( " . $and . " ) GROUP BY ID_Prod ORDER BY QTD DESC, Cabecalho ASC " . ($PRODS_LIMIT > 0 ? 'Limit ' . $PRODS_LIMIT : '');
    $sql = " SELECT ID_Prod,COUNT(ID_Prod) as QTD,SUM( Peso ) AS Peso2,Cabecalho FROM acelerador_pesquisa2 WHERE ( " . $and . " ) GROUP BY ID_Prod ORDER BY QTD DESC,Peso2 DESC, Cabecalho ASC " . ($PRODS_LIMIT > 0 ? 'Limit ' . $PRODS_LIMIT : '');


    //-----------------------------------------------------
    /*
    // MySQL 5.7 
        // mysql 5.6.39
        // centos upgrade mysql from 5.6 to 5.7
        // https://plenium.medium.com/upgrade-mysql-5-6-to-5-7-on-centos-7-571652f0cbc8

        mysqldump --databases on4tech.pt --tables acelerador_pesquisa2 --where="STR like 'monitores'"


        SELECT GROUP_CONCAT(ID_Prod,'|',Cabecalho SEPARATOR '||') 
        FROM (SELECT ID_Prod,COUNT(ID_Prod) as QTD, Cabecalho FROM acelerador_pesquisa2 WHERE ( ( STR like 'monitores' ) ) GROUP BY ID_Prod ORDER BY QTD DESC, Cabecalho ASC)
        as Dados;

        */

    $t=0;
    //$sql_json = "SELECT TO_BASE64(JSON_ARRAYAGG(JSON_OBJECT('ID_Prod',ID_Prod,'Cabecalho',Cabecalho))) AS `dados` FROM ($sql) as R";
    $sql_json = "SELECT GROUP_CONCAT(ID_Prod,'|',Cabecalho SEPARATOR '[]') AS `dados` FROM ($sql) as R";
    $db_data='';
    $rs->db_open($db_link,$GLOBALS['DB_name'],$sql_json);
    if(!$rs->EOF())$db_data=($rs->db_data['dados']);
    //if(!$rs->EOF())$db_data=base64_decode($rs->db_data['dados']);
    $rs->db_close();

    //$db_data = iconv('UTF-8','ASCII//TRANSLIT',$db_data);
    //$dados = json_decode($db_data, true);

    if($db_data!='')$dados = explode('[]',$db_data);

    $n=0;
    $t2=count($dados);
    while($n<$t2)
    {
        $linha=explode('|',$dados[$n]);

        $ID_Prod = $linha[0];

        $nome = $linha[1];

        $nome=strong_words( $nome, $array_palavras2);
        $prods[$ID_Prod]=$nome;
        $n++;
        $t++;
    }

    //----------------------------
    if($t<=0)
    {
        $sql = "SELECT ID_Prod,COUNT(ID_Prod) as QTD, Cabecalho FROM acelerador_pesquisa2 WHERE ( " . $and2 . " ) GROUP BY ID_Prod ORDER BY QTD DESC, Cabecalho ASC " . ($PRODS_LIMIT > 0 ? 'Limit ' . $PRODS_LIMIT : '');
        $sql_json = "SELECT GROUP_CONCAT(ID_Prod,'|',Cabecalho SEPARATOR '[]') AS `dados` FROM ($sql) as R";
    
        $rs->db_open($db_link, $GLOBALS['DB_name'], $sql_json);
        if(!$rs->EOF())$db_data=($rs->db_data['dados']);
        $rs->db_close();

        if($db_data!='')$dados = explode('[]',$db_data);
        $n=0;
        $t2=count($dados);
        while($n<$t2)
        {
            $linha=explode('|',$dados[$n]);
            $ID_Prod = $linha[0];
            $nome = $linha[1];
            $nome=strong_words( $nome, $array_palavras2);
            $prods[$ID_Prod]=$nome;
            $n++;
            $t++;
        }
    }

    return $prods;
}
function searchProductsMemoryTable($searchstr,$PRODS_LIMIT=500,$NO_STRONG_WORDS=0)
{
    return searchProductsMemoryTable4($searchstr,$PRODS_LIMIT,$NO_STRONG_WORDS);

    //return searchProductsMemoryTable3($searchstr,$PRODS_LIMIT);

    $db_link     = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
    $rs          = new DataObj();
    $PRODS_LIMIT = 0;
    if (!empty($vars) && array_key_exists('PRODS_LIMIT', $vars))
    {
        $PRODS_LIMIT = $vars['PRODS_LIMIT'];
    }
    $PRODS_LIMIT=1000;

    $prods = array();
    $searchstr2=iconv('UTF-8','ASCII//TRANSLIT',$searchstr);
    $searchstr2 = preg_replace('/[^a-zA-Z0-9_\.\,\-áÁàÀãÃâÂéÉèÈêÊíÍìÌîÎóÓòÒõÕôÔúÚùÙçÇ& ]/', '',$searchstr2);
    $searchstr2 = filter_var($searchstr2, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH);
    $searchstr2 = str_replace(array('.',',','-','_',','),' ',$searchstr2);

    $array_palavras=explode(" ",$searchstr);
    $and='';
    $and2='';
    $i=0;
    $t=0;
    while($i< count($array_palavras))
    {
        $k=$array_palavras[$i];
        if($i>0)$and .= " OR ";
        if($i>0)$and2 .= " OR ";
        $and .= " ( STR like '$k' ) ";
        $and2 .= " ( STR like '$k%' ) ";
        $i++;
    }

    $array_palavras2=explode(" ",$searchstr2);
    $total=count($array_palavras2);
    //----------------------------
    $sql = " SELECT ID_Prod,COUNT(ID_Prod) as QTD,SUM( Peso ) AS Peso2, Cabecalho FROM acelerador_pesquisa2 WHERE ( " . $and . " ) GROUP BY ID_Prod ORDER BY QTD DESC,Peso2 DESC, Cabecalho ASC " . ($PRODS_LIMIT > 0 ? 'Limit ' . $PRODS_LIMIT : '');
    if($_GET['ver_sql']==1){echo($sql."<br><br>");}
    $t=0;
    $rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
    if (!$rs->EOF())
    {
        if($rs->db_data['QTD'] >0)$total=$rs->db_data['QTD'];
        
        while (!$rs->EOF())
        {
            if($rs->db_data['QTD'] >= $total)
            {
                $t++;
                $nome = $rs->db_data['Cabecalho'];
                $nome=strong_words( $nome, $array_palavras2);
                $prods[$rs->db_data['ID_Prod']] = $nome;
            }
            $rs->db_next();
        }
        if($t<=0)
        {
            //$rs->first();
            $rs->db_close();
            $rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
            while (!$rs->EOF())
            {
                $t++;
                $nome = $rs->db_data['Cabecalho'];
                $nome=strong_words( $nome, $array_palavras2);
                $prods[$rs->db_data['ID_Prod']] = $nome;
                $rs->db_next();
            }
        }
    }
    $rs->db_close();
    //----------------------------
    if($t<=0)
    {
        $sql = " SELECT ID_Prod,COUNT(ID_Prod) as QTD, Cabecalho FROM acelerador_pesquisa2 WHERE ( " . $and2 . " ) GROUP BY ID_Prod ORDER BY QTD DESC, Cabecalho ASC " . ($PRODS_LIMIT > 0 ? 'Limit ' . $PRODS_LIMIT : '');
        if($_GET['ver_sql']==1){echo($sql."<br><br>");}
        $rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
        if (!$rs->EOF())
        {
            while (!$rs->EOF())
            {
                $t++;
                $nome = $rs->db_data['Cabecalho'];
                $nome=strong_words( $nome, $array_palavras2);
                $prods[$rs->db_data['ID_Prod']] = $nome;
                $rs->db_next();
            }
        }
        $rs->db_close();
    }

    return $prods;
}
function searchProductsMemoryTable___($searchstr,$PRODS_LIMIT=999999)
{
    $db_link     = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
    $rs          = new DataObj();
    $PRODS_LIMIT = 0;
    if (!empty($vars) && array_key_exists('PRODS_LIMIT', $vars))
    {
        $PRODS_LIMIT = $vars['PRODS_LIMIT'];
    }
    $PRODS_LIMIT=1000;

    $prods = array();
    $searchstr2=iconv('UTF-8','ASCII//TRANSLIT',$searchstr);
    $searchstr2 = preg_replace('/[^a-zA-Z0-9_\.\,\-áÁàÀãÃâÂéÉèÈêÊíÍìÌîÎóÓòÒõÕôÔúÚùÙçÇ& ]/', '',$searchstr2);
    $searchstr2 = filter_var($searchstr2, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH);
    $searchstr2 = str_replace(array('.',',','-','_',','),' ',$searchstr2);

    $array_palavras=explode(" ",$searchstr);
    $and='';
    $and2='';
    $i=0;
    $t=0;
    while($i< count($array_palavras))
    {
        $k=$array_palavras[$i];
        if($i>0)$and .= " OR ";
        $and .= " ( STR like '$k' ) ";
        $and2 .= " ( STR like '$k%' ) ";
        $i++;
    }

    $array_palavras2=explode(" ",$searchstr2);
    $sql = " SELECT ID_Prod,COUNT(ID_Prod) as QTD,SUM( Peso ) AS Peso2, Cabecalho FROM acelerador_pesquisa2 WHERE ( " . $and . " ) GROUP BY ID_Prod ORDER BY QTD DESC,Peso2 DESC, Cabecalho ASC " . ($PRODS_LIMIT > 0 ? 'Limit ' . $PRODS_LIMIT : '');
    $rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
    if (!$rs->EOF())
    {
        while (!$rs->EOF())
        {
            $nome = $rs->db_data['Cabecalho'];
            $nome=strong_words( $nome, $array_palavras2);
            $prods[$rs->db_data['ID_Prod']] = $nome;
            $rs->db_next();
            $t++;
        }
    }
    $rs->db_close();

    if($t<100)
    {
        $sql = " SELECT ID_Prod,COUNT(ID_Prod) as QTD, Cabecalho FROM acelerador_pesquisa2 WHERE ( " . $and2 . " ) GROUP BY ID_Prod ORDER BY QTD DESC, Cabecalho ASC " . ($PRODS_LIMIT > 0 ? 'Limit ' . $PRODS_LIMIT : '');
        $rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
        if (!$rs->EOF())
        {
            while (!$rs->EOF())
            {
                $nome = $rs->db_data['Cabecalho'];
                $nome=strong_words( $nome, $array_palavras2);
                $prods[$rs->db_data['ID_Prod']] = $nome;
                $rs->db_next();
                $t++;
            }
        }
        $rs->db_close();
    }

    return $prods;
}
function normaliza_texto($txt)
{
    if (!mb_check_encoding($txt, 'UTF-8')) {
        $txt = mb_convert_encoding($txt, 'UTF-8', 'ISO-8859-1');
    }
    //$txt = preg_replace('/[^a-zA-Z0-9_\.\,\-áÁàÀãÃâÂéÉèÈêÊíÍìÌîÎóÓòÒõÕôÔúÚùÙçÇ& ]/', '',$txt);
    //$txt = filter_var($txt, FILTER_UNSAFE_RAW);
    //$txt = str_replace(array('.',',','-','_',','),' ',$txt);
    //$txt = str_replace(array('.',',','-','_',','),' ',$txt);

    $txt = mb_strtolower($txt, 'UTF-8');
    $txt = preg_replace('/[^0-9a-záàãâéèêíìîóòõôúùç ]+/u', ' ', $txt);
    $txt = preg_replace('/\s+/u', ' ', $txt);
    
    return trim($txt);
}
function searchProductsMemoryTable4($searchstr='',$PRODS_LIMIT=500,$NO_STRONG_WORDS=0)
{
    $db_link     = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
    $res=Runquery($db_link,$GLOBALS['DB_name'],'SET SESSION group_concat_max_len=1000000');
    $rs          = new DataObj();
    $prods = array();

    $searchstr = normaliza_texto($searchstr);

    $palavras = explode(" ",$searchstr);
    $and='';
    $and2='';
    $i=0;

    $palavras_validas = [];
    foreach ($palavras as $p)
    {
        if ($p == '') continue;
        if (in_array($p, ['de','da','do','por','barato','baratos','barata','baratas'])) {continue;}
        $palavras_validas[] = $p;
        $sing = pluralParaSingular($p);
        if ($sing !== $p) {
            //$palavras_validas[] = $sing;
            $palavras_validas[] = $sing;
        }
    }
    $palavras_validas = array_unique($palavras_validas);
    $where_in = implode("','", $palavras_validas);
    $sql = " SELECT ID_Prod,SUM(Peso) as QTD, termo as Cabecalho,Fam,fam_id FROM autocomplete_produtos WHERE prefix IN ( '$where_in' ) GROUP BY ID_Prod ORDER BY QTD DESC, PVP DESC, Cabecalho ASC " . ($PRODS_LIMIT > 0 ? 'Limit ' . $PRODS_LIMIT : '');
    $sql_json = "SELECT GROUP_CONCAT(ID_Prod,'|',Cabecalho,'|',Fam,'|',fam_id SEPARATOR '[]') AS `dados` FROM ($sql) as R";
    $db_data='';
    $rs->db_open($db_link,$GLOBALS['DB_name'],$sql_json);
    if(!$rs->EOF())$db_data=($rs->db_data['dados']);
    $rs->db_close();
    if($db_data!='')$dados = explode('[]',$db_data);
    $n=0;
    $t2=count((array)$dados);
    while($n<$t2)
    {
        $linha=explode('|',$dados[$n]);
        $ID_Prod = $linha[0];
        $nome = $linha[1];
        if($NO_STRONG_WORDS==0)$nome=strong_words( $nome, $palavras_validas);
        $prods[$ID_Prod]=$nome.'|'.$linha[2].'|'.$linha[3];
        $n++;
    }
    return $prods;
}
function strtolower_min($str)
{
    $str = str_replace("Á", "á", $str);
    $str = str_replace("À", "à", $str);
    $str = str_replace("Â", "â", $str);
    $str = str_replace("Ä", "ä", $str);
    $str = str_replace("Ã", "ã", $str);
    $str = str_replace("É", "é", $str);
    $str = str_replace("È", "è", $str);
    $str = str_replace("Ê", "ê", $str);
    $str = str_replace("Ë", "ë", $str);
    $str = str_replace("Í", "í", $str);
    $str = str_replace("Ì", "ì", $str);
    $str = str_replace("Î", "î", $str);
    $str = str_replace("Ï", "ï", $str);
    $str = str_replace("Ó", "ó", $str);
    $str = str_replace("Ò", "ò", $str);
    $str = str_replace("Ô", "ô", $str);
    $str = str_replace("Ö", "ö", $str);
    $str = str_replace("Õ", "õ", $str);
    $str = str_replace("Ú", "ú", $str);
    $str = str_replace("Ù", "ù", $str);
    $str = str_replace("Û", "û", $str);
    $str = str_replace("Ü", "ü", $str);
    $str = str_replace("Ç", "ç", $str);

    return mb_strtolower(ltrim(rtrim($str)), 'UTF-8');
}

function getGenFiles($vars = array())
{
    $site_country    = '';
    $destaques       = 0;
    $fam_id          = 0;
    $relacionados    = 0;
    $cat             = '';
    $PRODS_SHOW      = '';
    $tipo_catalogo   = '';
    $ALLPRODUCTS     = '';
    $filtro_wishlist = '';
    $session_p       = '';
    $filtro_fam      = '';
    $filtro_marcas   = '';
    $pesquisa        = '';
    $filtro_pesquisa = '';
    $nojson          = 0;
    $FILTROS_SHOW    = 0;
    $PRODS_LIMIT     = 0;

    if (!empty($vars) && array_key_exists('destaques', $vars)) {
        $destaques = $vars['destaques'];
    }
    if (!empty($vars) && array_key_exists('fam_id', $vars)) {
        $fam_id = $vars['fam_id'];
    }
    if (!empty($vars) && array_key_exists('prod_id', $vars)) {
        $prod_id = $vars['prod_id'];
    }
    if (!empty($vars) && array_key_exists('site_country', $vars)) {
        $site_country = $vars['site_country'];
    }
    if (!empty($vars) && array_key_exists('relacionados', $vars)) {
        $relacionados = $vars['relacionados'];
    }
    if (!empty($vars) && array_key_exists('cat', $vars)) {
        $cat = $vars['cat'];
    }
    if (!empty($vars) && array_key_exists('PRODS_SHOW', $vars)) {
        $PRODS_SHOW = $vars['PRODS_SHOW'];
    }
    if (!empty($vars) && array_key_exists('tipo_catalogo', $vars)) {
        $tipo_catalogo = $vars['tipo_catalogo'];
    }
    if (!empty($vars) && array_key_exists('ALLPRODUCTS', $vars)) {
        $ALLPRODUCTS = $vars['ALLPRODUCTS'];
    }
    if (!empty($vars) && array_key_exists('filtro_wishlist', $vars)) {
        $filtro_wishlist = $vars['filtro_wishlist'];
    }
    if (!empty($vars) && array_key_exists('session_p', $vars)) {
        $session_p = $vars['session_p'];
    }
    if (!empty($vars) && array_key_exists('filtro_fam', $vars)) {
        $filtro_fam = $vars['filtro_fam'];
    }
    if (!empty($vars) && array_key_exists('filtro_marcas', $vars)) {
        $filtro_marcas = $vars['filtro_marcas'];
    }
    if (!empty($vars) && array_key_exists('pesquisa', $vars)) {
        $pesquisa = $vars['pesquisa'];
    }
    if (!empty($vars) && array_key_exists('filtro_pesquisa', $vars)) {
        $filtro_pesquisa = $vars['filtro_pesquisa'];
    }
    if (!empty($vars) && array_key_exists('PRODS_LIMIT', $vars)) {
        $PRODS_LIMIT = $vars['PRODS_LIMIT'];
    }
    if (!empty($vars) && array_key_exists('nojson', $vars)) {
        $nojson = $vars['nojson'];
    }
    if (!empty($vars) && array_key_exists('FILTROS_SHOW', $vars)) {
        $FILTROS_SHOW = $vars['FILTROS_SHOW'];
    }

    $return = '';

    $familia_filtros   = 'files/filtros/filtros_prods_' . $cat . '_' . $GLOBALS['site_lang'] . '.txt';
    $familia_filtros_c = 'files/filtros/filtros_prods_' . $cat . '_c_' . $GLOBALS['site_lang'] . '.txt';

    $marcas_filtros   = 'files/filtros/marca_filtros_prods_' . $filtro_marcas . '_' . $GLOBALS['site_lang'] . '.txt';
    $marcas_filtros_c = 'files/filtros/marca_filtros_prods_' . $filtro_marcas . '_c_' . $GLOBALS['site_lang'] . '.txt';
    if ($FILTROS_SHOW == 1 && $ALLPRODUCTS == 1) {

        $filtros .= FILTRO_CARACTERISTICAS_NEW(0, 'all');
        $return  .= $filtros;

    } elseif ($ALLPRODUCTS == 1) {
        $lines = '';
        $a     = 0;

        foreach (glob($GLOBALS['site_shell_url'] . "files/filtros/*_p*" . $GLOBALS['site_lang'] . ".json") as $filename) {
            $dh  = fopen($filename, 'r');
            $prd = fread($dh, filesize($filename));
            if ($a > 0) {
                $lines .= ',' . $prd;
            } else {
                $lines .= $prd;
            }
            $a++;
        }

        if ($lines) {
            $return .= 'var products=[' . $lines . '];';
        }
    } elseif ($filtro_wishlist == 1) {
        $prods = explode(';', $session_p);

        $lines = '';
        $b     = 0;

        for ($a = 0;$a < count($prods);$a++) {
            foreach (glob($GLOBALS['site_shell_url'] . "files/filtros/*_p" . $prods[$a] . "_" . $GLOBALS['site_lang'] . ".json") as $filename) {
                $dh  = fopen($filename, 'r');
                $prd = fread($dh, filesize($filename));
                if ($b > 0) {
                    $lines .= ',' . $prd;
                } else {
                    $lines .= $prd;
                }
                $b++;
            }
        }
        if ($lines) {
            $return .= 'var products=[' . $lines . '];';
        }
    } elseif ($destaques == 1) {
        $lines = '';
        $b     = 0;
        $prods = GERA_PRODUTOS_DESTAQUES();
        for ($a = 0;$a < count($prods);$a++) {
            foreach (glob($GLOBALS['site_shell_url'] . "files/filtros/*_p" . $prods[$a] . "_" . $GLOBALS['site_lang'] . ".json") as $filename) {
                $dh  = fopen($filename, 'r');
                $prd = fread($dh, filesize($filename));
                if ($b > 0) {
                    $lines .= ',' . $prd;
                } else {
                    $lines .= $prd;
                }
                $b++;
            }
        }
        if ($lines) {
            $return .= 'var products=[' . $lines . '];';
        }
    } elseif ($relacionados == 1) {
        $lines = '';
        $b     = 0;
        $prods = getProdutosRelacionados($prod_id);
        for ($a = 0;$a < count($prods);$a++) {
            foreach (glob($GLOBALS['site_shell_url'] . "files/filtros/*_p" . $prods[$a] . "_" . $GLOBALS['site_lang'] . ".json") as $filename) {
                $dh  = fopen($filename, 'r');
                $prd = fread($dh, filesize($filename));
                if ($b > 0) {
                    $lines .= ',' . $prd;
                } else {
                    $lines .= $prd;
                }
                $b++;
            }
        }
        if ($lines) {
            $return .= 'var products=[' . $lines . '];';
        }
    } elseif ($PRODS_SHOW == 1 && $cat > 0) {

        $lines = '';
        $b     = 0;

        foreach (glob($GLOBALS['site_shell_url'] . "files/filtros/*f" . $cat . "_*_" . $GLOBALS['site_lang'] . ".json") as $filename) {
            $dh  = fopen($filename, 'r');
            $prd = fread($dh, filesize($filename));
            if (!substr_count($prd, 'ref" : ""')) {
                if ($b > 0) {
                    $lines .= ',' . $prd;
                } else {
                    $lines .= $prd;
                }
                $b++;
            }
        }

        if ($lines) {
            $return .= 'var products=[' . $lines . '];';
        }
    } elseif ($FILTROS_SHOW == 1 && $cat > 0) {
        $filtros = '';

        // echo 'xxx'.stream_resolve_include_path(SITE_SHELL_URL.$familia_filtros);
        // echo '<br>';
        // echo SITE_SHELL_URL.$familia_filtros;

        if (stream_resolve_include_path(SITE_SHELL_URL . $familia_filtros)) {
            // $return .=  file_get_contents2($GLOBALS['site_http_url_internal'].$familia_filtros);

            $filename = SITE_SHELL_URL . $familia_filtros;
            $fp       = fopen($filename, "r");
            $return   .= fread($fp, filesize($filename));
            fclose($fp);
        }
        if (stream_resolve_include_path(SITE_SHELL_URL . $familia_filtros_c)) {
            // $return .=  file_get_contents2($GLOBALS['site_http_url_internal'].$familia_filtros);

            $filename = SITE_SHELL_URL . $familia_filtros_c;
            $fp       = fopen($filename, "r");
            $return   .= fread($fp, filesize($filename));
            fclose($fp);
        }

        // if(stream_resolve_include_path($GLOBALS['site_shell_url'].$familia_filtros)) $return .=  file_get_contents2($GLOBALS['site_http_url_internal'].$familia_filtros);
        // if(stream_resolve_include_path($GLOBALS['site_shell_url'].$familia_filtros_c)) $return .=  file_get_contents2($GLOBALS['site_http_url_internal'].$familia_filtros_c);
    } elseif ($PRODS_SHOW == 1 && $filtro_marcas) // AQUI COMEÇAM AS MARCAS -
    {
        $lines = '';
        $a     = 0;

        // $filtro_fam=intval($_GET['filtro_fam']);

        $way = '';
        if ($filtro_fam > 0) {
            $way = glob($GLOBALS['site_shell_url'] . "files/filtros/*f" . $filtro_fam . "_*_m" . $filtro_marcas . "_*_" . $GLOBALS['site_lang'] . ".json");

        } else {
            $way = glob($GLOBALS['site_shell_url'] . "files/filtros/*_m" . $filtro_marcas . "_*_" . $GLOBALS['site_lang'] . ".json");
        }

        foreach ($way as $filename) {

            // $return .=  $filename.'<br>';
            $dh  = fopen($filename, 'r');
            $prd = fread($dh, filesize($filename));
            if ($a > 0) {
                $lines .= ',' . $prd;
            } else {
                $lines .= $prd;
            }
            $a++;
        }

        if ($lines) {
            $return .= 'var products=[' . $lines . '];';
        }
    } elseif ($FILTROS_SHOW == 1 && $filtro_marcas) {
        if (stream_resolve_include_path($GLOBALS['site_shell_url'] . $marcas_filtros)) {
            $return .= file_get_contents2($GLOBALS['site_http_url_internal'] . $marcas_filtros);
        }
        if (stream_resolve_include_path($GLOBALS['site_shell_url'] . $marcas_filtros_c)) {
            $return .= file_get_contents2($GLOBALS['site_http_url_internal'] . $marcas_filtros_c);
        }
    } elseif ((isset($_GET['FILTROS_SHOW_FM']) && $_GET['FILTROS_SHOW_FM'] == 1) && $filtro_marcas && $filtro_fam) {
        $filtros = FILTRO_MARCAS_NEW_FM($filtro_marcas, $filtro_fam);
        $return  .= $filtros;
    } elseif (($PRODS_SHOW == 1) && $tipo_catalogo == "destaque") {
        $lines = '';
        $b     = 0;
        $prods = GERA_PRODUTOS_DESTAQUES();
        for ($a = 0;$a < count($prods);$a++) {
            foreach (glob($GLOBALS['site_shell_url'] . "files/filtros/*_p" . $prods[$a] . "_" . $GLOBALS['site_lang'] . ".json") as $filename) {
                $dh  = fopen($filename, 'r');
                $prd = fread($dh, filesize($filename));
                if ($b > 0) {
                    $lines .= ',' . $prd;
                } else {
                    $lines .= $prd;
                }
                $b++;
            }
        }
        if ($lines && $nojson == 0) {
            $return .= 'var products=[' . $lines . '];';
        } elseif ($lines && $nojson == 1) {
            $return .= $lines;
        }
    } elseif (($PRODS_SHOW == 1) && $tipo_catalogo == "visitados") {
        $lines = '';
        $b     = 0;
        $prods = GERA_PRODUTOS_DESTAQUES_VISTITADOS();
        for ($a = 0;$a < count($prods);$a++) {
            foreach (glob($GLOBALS['site_shell_url'] . "files/filtros/*_p" . $prods[$a] . "_" . $GLOBALS['site_lang'] . ".json") as $filename) {
                $dh  = fopen($filename, 'r');
                $prd = fread($dh, filesize($filename));
                if ($b > 0) {
                    $lines .= ',' . $prd;
                } else {
                    $lines .= $prd;
                }
                $b++;
            }
        }
        if ($lines && $nojson == 0) {
            $return .= 'var products=[' . $lines . '];';
        } elseif ($lines && $nojson == 1) {
            $return .= $lines;
        }
    } elseif (($PRODS_SHOW == 1) && $tipo_catalogo == "dodia") {
        $lines = '';
        $b     = 0;
        $prods = BuscarProdutosDia();
        for ($a = 0;$a < count($prods);$a++) {
            foreach (glob($GLOBALS['site_shell_url'] . "files/filtros/*_p" . $prods[$a] . "_" . $GLOBALS['site_lang'] . ".json") as $filename) {
                $dh  = fopen($filename, 'r');
                $prd = fread($dh, filesize($filename));
                if ($b > 0) {
                    $lines .= ',' . $prd;
                } else {
                    $lines .= $prd;
                }
                $b++;
            }
        }
        if ($lines) {
            $return .= 'var products=[' . $lines . '];';
        }

    } elseif ($FILTROS_SHOW == 1 && $tipo_catalogo == "destaque") {
        $filtros .= FILTRO_CARACTERISTICAS_NEW(0, 5);
        $return  .= $filtros;
    } elseif ($FILTROS_SHOW == 1 && $tipo_catalogo == "outlet") {
        $filtros .= FILTRO_CARACTERISTICAS_NEW(0, 'outlet');
        $return  .= $filtros;
    } elseif (($PRODS_SHOW == 1) && $tipo_catalogo == "outlet") {
        $lines = '';
        $b     = 0;
        $prods = BuscarProdutosOutlet();

        for ($a = 0;$a < count($prods);$a++) {
            foreach (glob($GLOBALS['site_shell_url'] . "files/filtros/*_p" . $prods[$a] . "_" . $GLOBALS['site_lang'] . ".json") as $filename) {
                $dh  = fopen($filename, 'r');
                $prd = fread($dh, filesize($filename));
                if ($b > 0) {
                    $lines .= ',' . $prd;
                } else {
                    $lines .= $prd;
                }
                $b++;
            }
        }
        if ($lines) {
            $return .= 'var products=[' . $lines . '];';
        }
    } elseif ($FILTROS_SHOW == 1 && $tipo_catalogo == "novidade") {
        $filtros .= FILTRO_CARACTERISTICAS_NEW(0, 'novidade');
        $return  .= $filtros;
    } elseif (($PRODS_SHOW == 1) && $tipo_catalogo == "novidade") {
        $lines = '';
        $b     = 0;
        $prods = BuscarProdutosNovidades();
        for ($a = 0;$a < count($prods);$a++) {
            foreach (glob($GLOBALS['site_shell_url'] . "files/filtros/*_p" . $prods[$a] . "_" . $GLOBALS['site_lang'] . ".json") as $filename) {
                $dh  = fopen($filename, 'r');
                $prd = fread($dh, filesize($filename));
                if ($b > 0) {
                    $lines .= ',' . $prd;
                } else {
                    $lines .= $prd;
                }
                $b++;
            }
        }
        if ($lines) {
            $return .= 'var products=[' . $lines . '];';
        }
    } elseif ($FILTROS_SHOW == 1 && $tipo_catalogo == "promocao") {
        // $filtros.= filtros_left();
        // $filtros.= FILTRO_MARCAS_NEW(0,4);
        // $filtros.= FILTRO_PRECOS_NEW(getPrecosblocos(0,4));
        $filtros .= FILTRO_CARACTERISTICAS_NEW(0, 4);
        $return  .= $filtros;
    } elseif (($PRODS_SHOW == 1) && $tipo_catalogo == "promocao") {
        $lines = '';
        $b     = 0;
        $prods = BuscarProdutosPromocoes($PRODS_LIMIT);
        for ($a = 0;$a < count($prods);$a++) {
            foreach (glob($GLOBALS['site_shell_url'] . "files/filtros/*_p" . $prods[$a] . "_" . $GLOBALS['site_lang'] . ".json") as $filename) {
                $dh  = fopen($filename, 'r');
                $prd = fread($dh, filesize($filename));
                if ($b > 0) {
                    $lines .= ',' . $prd;
                } else {
                    $lines .= $prd;
                }
                $b++;
            }
        }
        if ($lines && $nojson == 0) {
            $return .= 'var products=[' . $lines . '];';
        } elseif ($lines && $nojson == 1) {
            $return .= $lines;
        }
    } elseif (($PRODS_SHOW == 1) && $tipo_catalogo == "maisvendido") {
        $lines = '';
        $b     = 0;
        $prods = BuscarProdutosMaisVendidos();
        for ($a = 0;$a < count($prods);$a++) {
            foreach (glob($GLOBALS['site_shell_url'] . "files/filtros/*_p" . $prods[$a] . "_" . $GLOBALS['site_lang'] . ".json") as $filename) {
                $dh  = fopen($filename, 'r');
                $prd = fread($dh, filesize($filename));
                if ($b > 0) {
                    $lines .= ',' . $prd;
                } else {
                    $lines .= $prd;
                }
                $b++;
            }
        }
        if ($lines && $nojson == 0) {
            $return .= 'var products=[' . $lines . '];';
        } elseif ($lines && $nojson == 1) {
            $return .= $lines;
        }
    } elseif ($FILTROS_SHOW == 1 && $filtro_pesquisa) {
        if ($pesquisa) {
            $Str_pesquisar = $pesquisa;
            // $filtros.= FILTRO_MARCAS_NEW(0,3);
            // $filtros.= FILTRO_PRECOS_NEW(getPrecosblocos(0,3));
            $filtros .= FILTRO_CARACTERISTICAS_NEW(0, 3);
            $return  .= $filtros;
        }
    }
    elseif (($PRODS_SHOW == 1) && $filtro_pesquisa)
    {
        global $pesquisa;

        if ($pesquisa)
        {
            $searchstr = mb_strtolower(rtrim(ltrim($pesquisa)), 'UTF-8');

            $prod_id    = array();
            $maxResults = 32;

            //$prods = searchProductsMemoryTable($searchstr);
            // 1º pesquisar por ref ou EAN =

            //$prods = searchProductsByRef(1, $searchstr);
            $prods = searchProductsMemoryTable($searchstr);
            if (!empty($prods))
            {
                $prod_id = array_replace($prods, $prod_id);
            }
            else
            {
                // 2º pesquisar por nome =
                $prods = searchProductsByName(1, $searchstr);
                if (!empty($prods)) {
                    $prod_id = array_replace($prods, $prod_id);
                } else {
                    // 3º pesquisar por familia =
                    $prods = searchProductsByFamilyName(1, $searchstr);
                    if (!empty($prods)) {
                        $prod_id = array_replace($prods, $prod_id);
                    } else {
                        // 4º pesquisar por marca =
                        $prods = searchProductsByBrandName(1, $searchstr);
                        if (!empty($prods)) {
                            $prod_id = array_replace($prods, $prod_id);
                        } else {
                            // 5º pesquisar por ref no inicio ou fim
                            $total = count($prod_id);
                            if ($total <= $maxResults) {
                                $prods = searchProductsByRef(2, $searchstr);
                                if (!empty($prods)) {
                                    $prod_id = array_replace($prods, $prod_id);
                                }
                            }
                            // 6º pesquisar por nome no inicio ou fim
                            $total = count($prod_id);
                            if ($total <= $maxResults) {
                                $prods = searchProductsByName(2, $searchstr);
                                if (!empty($prods)) {
                                    $prod_id = array_replace($prods, $prod_id);
                                }
                            }
                            // 7º pesquisar por familia no inicio ou fim
                            $total = count($prod_id);
                            if ($total <= $maxResults) {
                                $prods = searchProductsByFamilyName(2, $searchstr);
                                if (!empty($prods)) {
                                    $prod_id = array_replace($prods, $prod_id);
                                }
                            }
                            // 8º pesquisar por marca no inicio ou fim
                            $total = count($prod_id);
                            if ($total <= $maxResults) {
                                $prods = searchProductsByBrandName(2, $searchstr);
                                if (!empty($prods)) {
                                    $prod_id = array_replace($prods, $prod_id);
                                }

                                $total = count($prod_id);
                                if ($total <= $maxResults) {
                                    // 9º pesquisar por ref parcial
                                    if ($total <= $maxResults) {
                                        $prods = searchProductsByRef(3, $searchstr);
                                        if (!empty($prods)) {
                                            $prod_id = array_replace($prods, $prod_id);
                                        }
                                    }
                                    // 10º pesquisar por nome parcial
                                    $total = count($prod_id);
                                    if ($total <= $maxResults) {
                                        $prods = searchProductsByName(3, $searchstr);
                                        if (!empty($prods)) {
                                            $prod_id = array_replace($prods, $prod_id);
                                        }
                                    }
                                    // 11º pesquisar por familia parcial
                                    $total = count($prod_id);
                                    if ($total <= $maxResults) {
                                        $prods = searchProductsByFamilyName(3, $searchstr);
                                        if (!empty($prods)) {
                                            $prod_id = array_replace($prods, $prod_id);
                                        }
                                    }
                                    // 12º pesquisar por marca parcial
                                    $total = count($prod_id);
                                    if ($total <= $maxResults) {
                                        $prods = searchProductsByBrandName(3, $searchstr);
                                        if (!empty($prods)) {
                                            $prod_id = array_replace($prods, $prod_id);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }



            $total  = count($prod_id);
            $return = '';
            $unico  = 0;
            if ($total == 1)
            {
                $unico = 1;
            }

            // if($log_sql == 1) echo $total.'<br>';

            $lines = '';
            if ($total > 0)
            {
                if ($unico == 1)
                {
                    reset($prod_id);
                    $prod_id = key($prod_id);
                    $return  .= '<script>window.location.href="' . seo('pages.php?page_id=' . $GLOBALS['products_page_id'],$prod_id, 2) . '";</script>';
                }
                else
                {
                    $c = 0;
                    foreach ($prod_id as $prodId => $prodName)
                    {
                        foreach (glob($GLOBALS['site_shell_url'] . "files/filtros/*_p" . $prodId . "_" . $GLOBALS['site_lang'] . ".json") as $filename)
                        {
                            $dh  = fopen($filename, 'r');
                            $prd = fread($dh, 8192);
                            if ($c > 0)
                            {
                                $lines .= ',' . $prd;
                            }
                            else
                            {
                                $lines .= $prd;
                            }
                            $c++;
                        }
                    }
                }
            }
            $filtros = '';
            if ($lines && $nojson == 0)
            {
                $lines=str_replace('"m_id" : ,"','"m_id" : "","',$lines);
                $return .= 'var products=[' . $lines . '];';
            }
            elseif ($lines && $nojson == 1)
            {
                $return .= $lines;
            }
        }
    }
    /*elseif(($PRODS_SHOW == 1) && $filtro_pesquisa)
    {
        global $pesquisa,$DB_host,$DB_user,$DB_pass,$DB_name;
        $existe=0;
        $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
        $rs=new DataObj;
        $rs2=new DataObj;

        if($pesquisa)
        {
            // SE ESTIVERMOS NA HOME PAGE VAMOS FAZER UMA PESQUISA DIRETA A BASE DE DADOS
            $Str_pesquisar=strtolower_min($pesquisa);

            global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;

            $unico=1;
            $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
            $rs=new DataObj;

            // $and=' lower(pol.Cabecalho) like "%'.$Str_pesquisar.'%" ';
            $and=' lower(pl.Cabecalho) like "%'.$Str_pesquisar.'%" ';
            $and .= ' OR replace(lower(pl.Cabecalho), \' \',\'\') like "%'.$Str_pesquisar.'%" ';
            $and .= ' OR lower(p.Ref) like "%'.$Str_pesquisar.'%" ';
            // $and .= ' OR lower(pl.Texto1) like "%'.$Str_pesquisar.'%" ';
            $sql='
                    SELECT
                        p.ID as PID
                    FROM
                        products p
                        INNER JOIN products_lang pl ON pl.ID_PAI=p.ID
                        INNER JOIN products_objs po ON po.obj_type_id=p.ID
                        INNER JOIN brands b ON b.ID=p.ID_Marca
                    WHERE
                        ( '.$and.' )
                        AND p.Aproved=1
                        AND pl.Lang="'.$site_lang.'"
                        AND b.Visivel=1
                    GROUP BY
                        p.ID
                    ORDER BY
                        p.TXPV DESC
                    '.($PRODS_LIMIT > 0 ? 'Limit '.$PRODS_LIMIT: '').'
                    ';
            // echo 'a) '.$sql.'<br>';
            $rs->db_open($db_link,$DB_name,$sql);

            if($rs->EOF())
            {
                $unico=0;
                // $Str_pesquisar1=$Str_pesquisar;


                $Str_pesquisar1=explode(' ', $Str_pesquisar);
                $and1='';
                $and2='';
                for($z=0; $z<count($Str_pesquisar1); $z++)
                {
                    if($and1 != '') $and1 .= ' AND lower(pl.Cabecalho) like "%'.$Str_pesquisar1[$z].'%"';
                    else $and1 .= ' lower(pl.Cabecalho) like "%'.$Str_pesquisar1[$z].'%"';

                    if($and2 != '') $and2 .= ' AND lower(pl.Texto1) like "%'.$Str_pesquisar1[$z].'%"';
                    else $and2 .= ' lower(pl.Texto1) like "%'.$Str_pesquisar1[$z].'%"';
                }
                // $and=' (pl.Cabecalho like "%'.$Str_pesquisar1.'%" A pl.Cabecalho like "%'.$Str_pesquisar2.'%") ';
                // $and .= ' OR (p.Ref like "%'.$Str_pesquisar1.'%" OR p.Ref like "%'.$Str_pesquisar2.'%") ';
                // $and .= ' OR (pl.Texto1 like "%'.$Str_pesquisar1.'%" OR pl.Texto1 like "%'.$Str_pesquisar2.'%") ';
                $sql='
                        SELECT
                            p.ID as PID
                        FROM
                            products p
                            INNER JOIN products_lang pl ON pl.ID_PAI=p.ID
                            INNER JOIN products_objs po ON po.obj_type_id=p.ID
                            INNER JOIN brands b ON b.ID=p.ID_Marca
                        WHERE
                            (( '.$and1.' ) OR ( '.$and2.' ))
                            AND p.Aproved=1
                            AND pl.Lang="'.$site_lang.'"
                            AND b.Visivel=1
                        GROUP BY
                            p.ID
                        ORDER BY
                        p.TXPV DESC
                        '.($PRODS_LIMIT > 0 ? 'Limit '.$PRODS_LIMIT: '').'
                    ';
                    // echo 'b) '.$sql;
                    $rs->db_open($db_link,$DB_name,$sql);
                    if($rs->EOF() && !is_numeric($Str_pesquisar))
                    {
                        $unico=0;
                        $Str_pesquisar1=$Str_pesquisar;
                        $Str_pesquisar2=$Str_pesquisar;

                            $Str_pesquisar1=substr($Str_pesquisar,0,strlen($Str_pesquisar1)-1);
                            $Str_pesquisar2=substr($Str_pesquisar,1,strlen($Str_pesquisar1));
                            // $and=' (pol.Cabecalho like "%'.$Str_pesquisar1.'%" OR pol.Cabecalho like "%'.$Str_pesquisar2.'%") ';
                            $and=' (lower(pl.Cabecalho) like "%'.$Str_pesquisar1.'%" OR lower(pl.Cabecalho) like "%'.$Str_pesquisar2.'%") ';
                            $and .= ' OR (lower(p.Ref) like "%'.$Str_pesquisar1.'%" OR lower(p.Ref) like "%'.$Str_pesquisar2.'%") ';
                            $and .= ' OR (lower(pl.Texto1) like "%'.$Str_pesquisar1.'%" OR lower(pl.Texto1) like "%'.$Str_pesquisar2.'%") ';
                            $sql='
                                    SELECT
                                        p.ID as PID
                                    FROM
                                        products p
                                        INNER JOIN products_lang pl ON pl.ID_PAI=p.ID
                                        INNER JOIN products_objs po ON po.obj_type_id=p.ID
                                        INNER JOIN brands b ON b.ID=p.ID_Marca
                                    WHERE
                                        ( '.$and.' )
                                        AND p.Aproved=1
                                        AND pl.Lang="'.$site_lang.'"
                                        AND b.Visivel=1
                                    GROUP BY
                                        p.ID
                                    ORDER BY
                                    p.TXPV DESC
                                    '.($PRODS_LIMIT > 0 ? 'Limit '.$PRODS_LIMIT: '').'
                                ';
                                $rs->db_open($db_link,$DB_name,$sql);

                    }
            }



            $total=$rs->db_rows;	$return='';

            $lines='';
            if($total > 0)
            {
                if($total==1 && $unico==1 && $PRODS_LIMIT==0)
                {
                    $prod_id=$rs->db_data['PID'];
                    $return .=  '<script>window.location.href="'.seo('pages.php?page_id='.$GLOBALS['products_page_id'], $prod_id, 2).'";</script>';
                }
                else
                {
                    while(!$rs->EOF())
                    {
                        $prod_id=$rs->db_data['PID'];

                        foreach (glob($GLOBALS['site_shell_url']."files/filtros/*_p".$prod_id."_".$GLOBALS['site_lang'].".json") as $filename)
                        {
                            $dh=fopen($filename, 'r'); $prd=fread($dh, filesize($filename));
                            if($b > 0) $lines .= ','.$prd; else $lines .= $prd;
                            $b++;
                        }
                        $rs->db_next();
                    }
                }
            }
            $rs->db_close();
            $filtros='';
            if($lines && $nojson == 0) $return .=  'var products=['.$lines.'];';
            elseif($lines && $nojson == 1) $return .= $lines;
        }
    }*/ 

    return $return;
}

function stripAccents($str)
{
    return strtr(utf8_decode($str), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'),
        'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
}

function stripPlural($str)
{
    if (mb_substr($str, -2) == 'as') {
        $str = str_replace('as', 'a', $str);
    }
    if (mb_substr($str, -3) == 'ies') {
        $str = str_replace('ies', 'y', $str);
    }
    if (mb_substr($str, -2) == 'ies') {
        $str = str_replace('es', 'e', $str);
    }
    if (mb_substr($str, -2) == 'is') {
        $str = str_replace('is', 'i', $str);
    }
    if (mb_substr($str, -2) == 'os') {
        $str = str_replace('os', 'o', $str);
    }
    if (mb_substr($str, -2) == 'us') {
        $str = str_replace('us', 'u', $str);
    }
    if (mb_substr($str, -2) == 's') {
        $str = str_replace('s', '', $str);
    }

    return $str;
}



function searchProductsByRef($type=1,$searchstr='')
{
    $db_link     = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
    $rs          = new DataObj();
    $PRODS_LIMIT = 0;
    if (!empty($vars) && array_key_exists('PRODS_LIMIT', $vars)) {
        $PRODS_LIMIT = $vars['PRODS_LIMIT'];
    }

    $and = '';
    if ($type == 1)
    {
        $and .= " LOWER(p.Ref) = '$searchstr' ";
        $and .= " OR LOWER(p.Ref_ean)='$searchstr' ";
    }
    else
    {
        $searchSemAcentos                = stripAccents($searchstr);
        $searchComPalavrasMeio           = str_replace(' ', '%', $searchstr);
        $searchSemPlural                 = stripPlural($searchstr);
        $searchPalavras                  = explode(' ', $searchstr);
        $searchVariasPalavras            = implode(',', $searchPalavras);
        $searchVariasPalavrasSemAccentos = $searchPalavras;
        foreach ($searchVariasPalavrasSemAccentos as $pos => $palavra)$searchVariasPalavrasSemAccentos[$pos] = stripAccents($palavra);
        $searchVariasPalavrasSemAccentos = implode(',', $searchVariasPalavrasSemAccentos);
        $searchVariasPalavrasSemPlural   = $searchPalavras;
        foreach ($searchVariasPalavrasSemPlural as $pos => $palavra)$searchVariasPalavrasSemPlural[$pos] = stripPlural($palavra);
        $searchVariasPalavrasSemPlural = implode(',', $searchVariasPalavrasSemPlural);
        $searchComPalavrasMeioSemPlural = $searchPalavras;
        foreach ($searchComPalavrasMeioSemPlural as $pos => $palavra)$searchComPalavrasMeioSemPlural[$pos] = stripPlural($palavra);

        if ($type == 2)
        {
            $and .= " LOWER(p.Ref) like '%$searchstr' OR LOWER(p.Ref) like '$searchstr%' ";
            $and .= " OR LOWER(p.Ref) like '%$searchSemAcentos' OR LOWER(p.Ref) like '$searchSemAcentos%' ";
            $and .= " OR LOWER(p.Ref) like '%$searchComPalavrasMeio' OR LOWER(p.Ref) like '$searchComPalavrasMeio%' ";
            $and .= " OR LOWER(p.Ref) like '%$searchSemPlural' OR LOWER(p.Ref) like '$searchSemPlural%' ";
            $and .= " OR LOWER(p.Ref_ean) like '%$searchstr' OR LOWER(p.Ref_ean) like '$searchstr%' ";
            $and .= " OR LOWER(p.Ref_ean) like '%$searchSemAcentos' OR LOWER(p.Ref_ean) like '$searchSemAcentos%' ";
            $and .= " OR LOWER(p.Ref_ean) like '%$searchComPalavrasMeio' OR LOWER(p.Ref_ean) like '$searchComPalavrasMeio%' ";
            $and .= " OR LOWER(p.Ref_ean) like '%$searchSemPlural' OR LOWER(p.Ref_ean) like '$searchSemPlural%' ";
        }
        elseif ($type == 3)
        {
            $and .= " LOWER(p.Ref) like '%$searchstr' OR LOWER(p.Ref) like '$searchstr%' ";
            $and .= " OR LOWER(p.Ref) like '%$searchSemAcentos' OR LOWER(p.Ref) like '$searchSemAcentos%' ";
            $and .= " OR LOWER(p.Ref) like '%$searchComPalavrasMeio' OR LOWER(p.Ref) like '$searchComPalavrasMeio%' ";
            $and .= " OR LOWER(p.Ref) like '%$searchSemPlural' OR LOWER(p.Ref) like '$searchSemPlural%' ";
            $and .= " OR LOWER(p.Ref_ean) like '%$searchstr' OR LOWER(p.Ref_ean) like '$searchstr%' ";
            $and .= " OR LOWER(p.Ref_ean) like '%$searchSemAcentos' OR LOWER(p.Ref_ean) like '$searchSemAcentos%' ";
            $and .= " OR LOWER(p.Ref_ean) like '%$searchComPalavrasMeio' OR LOWER(p.Ref_ean) like '$searchComPalavrasMeio%' ";
            $and .= " OR LOWER(p.Ref_ean) like '%$searchSemPlural' OR LOWER(p.Ref_ean) like '$searchSemPlural%' ";
            $and .= " OR (INSTR('$searchstr', LOWER(p.Ref)) > 0 AND p.Ref <> '') OR LOWER(p.Ref) like '%$searchstr%' ";
            $and .= " OR (INSTR('$searchSemAcentos', LOWER(p.Ref)) > 0 AND p.Ref <> '') OR LOWER(p.Ref) like '%$searchSemAcentos%' ";
            $and .= " OR LOWER(p.Ref) like '%$searchComPalavrasMeio%' ";
            $and .= " OR (INSTR('$searchSemPlural', LOWER(p.Ref)) > 0 AND p.Ref <> '') OR LOWER(p.Ref) like '%$searchSemPlural%' ";
            $and .= " OR (INSTR('$searchstr', LOWER(p.Ref_ean)) > 0 AND p.Ref_ean <> '') OR LOWER(p.Ref_ean) like '%$searchstr%' ";
            $and .= " OR (INSTR('$searchSemAcentos', LOWER(p.Ref_ean)) > 0 AND p.Ref_ean <> '') OR LOWER(p.Ref_ean) like '%$searchSemAcentos%' ";
            $and .= " OR LOWER(p.Ref_ean) like '%$searchComPalavrasMeio%' ";
            $and .= " OR (INSTR('$searchSemPlural', LOWER(p.Ref_ean)) > 0 AND p.Ref_ean <> '') OR LOWER(p.Ref_ean) like '%$searchSemPlural%' ";
            $and .= " OR LOWER(p.Ref) IN ('$searchVariasPalavras') ";
            $and .= " OR LOWER(p.Ref) IN ('$searchVariasPalavrasSemAccentos') ";
            $and .= " OR LOWER(p.Ref) IN ('$searchVariasPalavrasSemPlural') ";
            foreach ($searchComPalavrasMeioSemPlural as $pos => $palavra) {
                $and .= " OR LOWER(p.Ref) like '%$palavra%' ";
            }
            $and .= " OR LOWER(p.Ref_ean) IN ('$searchVariasPalavras') ";
            $and .= " OR LOWER(p.Ref_ean) IN ('$searchVariasPalavrasSemAccentos') ";
            $and .= " OR LOWER(p.Ref_ean) IN ('$searchVariasPalavrasSemPlural') ";
            foreach ($searchComPalavrasMeioSemPlural as $pos => $palavra) {
                $and .= " OR LOWER(p.Ref_ean) like '%$palavra%' ";
            }
        }
    }

    $prods=array();

    $sql = " SELECT p.ID, pl.Cabecalho FROM products p INNER JOIN products_lang AS pl ON pl.ID_PAI=p.ID WHERE ( " . $and . " ) AND p.Aproved=1 AND pl.Lang='{$GLOBALS['site_lang']}' GROUP BY p.ID ORDER BY pl.Cabecalho ASC " . ($PRODS_LIMIT > 0 ? 'Limit ' . $PRODS_LIMIT : '');
    $rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
    if (!$rs->EOF())
    {
        while (!$rs->EOF())
        {
            $prods[$rs->db_data['ID']] = $rs->db_data['Cabecalho'];
            $rs->db_next();
        }
    }
    $rs->db_close();

    return $prods;
}

function searchProductsByName($type = 1, $searchstr)
{
    $db_link     = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
    $rs          = new DataObj();
    $PRODS_LIMIT = 0;
    if (!empty($vars) && array_key_exists('PRODS_LIMIT', $vars)) {
        $PRODS_LIMIT = $vars['PRODS_LIMIT'];
    }
    $prods                           = array();

    if ($type == 1)
    {
        $and = " LOWER(pl.Cabecalho) Like '%$searchstr%' ";
    }
    else
    {
        $searchSemAcentos                = stripAccents($searchstr);
        $searchComPalavrasMeio           = str_replace(' ', '%', $searchstr);
        $searchSemPlural                 = stripPlural($searchstr);
        $searchPalavras                  = explode(' ', $searchstr);
        $searchVariasPalavras            = implode(',', $searchPalavras);
        $searchVariasPalavrasSemAccentos = $searchPalavras;
        foreach ($searchVariasPalavrasSemAccentos as $pos => $palavra)$searchVariasPalavrasSemAccentos[$pos] = stripAccents($palavra);
        $searchVariasPalavrasSemAccentos = implode(',', $searchVariasPalavrasSemAccentos);
        $searchVariasPalavrasSemPlural   = $searchPalavras;
        foreach ($searchVariasPalavrasSemPlural as $pos => $palavra)$searchVariasPalavrasSemPlural[$pos] = stripPlural($palavra);
        $searchVariasPalavrasSemPlural = implode(',', $searchVariasPalavrasSemPlural);
        $searchComPalavrasMeioSemPlural = $searchPalavras;
        foreach ($searchComPalavrasMeioSemPlural as $pos => $palavra)$searchComPalavrasMeioSemPlural[$pos] = stripPlural($palavra);

        $and = '';
        if ($type == 2)
        {
            $and .= " LOWER(pl.Cabecalho) like '%$searchstr' OR LOWER(pl.Cabecalho) like '$searchstr%' ";
            $and .= " OR LOWER(pl.Cabecalho) like '%$searchSemAcentos' OR LOWER(pl.Cabecalho) like '$searchSemAcentos%' ";
            $and .= " OR LOWER(pl.Cabecalho) like '%$searchComPalavrasMeio' OR LOWER(pl.Cabecalho) like '$searchComPalavrasMeio%' ";
            $and .= " OR LOWER(pl.Cabecalho) like '%$searchSemPlural' OR LOWER(pl.Cabecalho) like '$searchSemPlural%' ";
        } elseif ($type == 3) {
            $and .= " LOWER(pl.Cabecalho) like '%$searchstr' OR LOWER(pl.Cabecalho) like '$searchstr%' ";
            $and .= " OR LOWER(pl.Cabecalho) like '%$searchSemAcentos' OR LOWER(pl.Cabecalho) like '$searchSemAcentos%' ";
            $and .= " OR LOWER(pl.Cabecalho) like '%$searchComPalavrasMeio' OR LOWER(pl.Cabecalho) like '$searchComPalavrasMeio%' ";
            $and .= " OR LOWER(pl.Cabecalho) like '%$searchSemPlural' OR LOWER(pl.Cabecalho) like '$searchSemPlural%' ";
            $and .= " OR (INSTR('$searchstr', LOWER(pl.Cabecalho)) > 0 AND pl.Cabecalho <> '') OR LOWER(pl.Cabecalho) like '%$searchstr%' ";
            $and .= " OR (INSTR('$searchSemAcentos', LOWER(pl.Cabecalho)) > 0 AND pl.Cabecalho <> '') OR LOWER(pl.Cabecalho) like '%$searchSemAcentos%' ";
            $and .= " OR LOWER(pl.Cabecalho) like '%$searchComPalavrasMeio%' ";
            $and .= " OR (INSTR('$searchSemPlural', LOWER(pl.Cabecalho)) > 0 AND pl.Cabecalho <> '') OR LOWER(pl.Cabecalho) like '%$searchSemPlural%' ";
            $and .= " OR LOWER(pl.Cabecalho) IN ('$searchVariasPalavras') ";
            $and .= " OR LOWER(pl.Cabecalho) IN ('$searchVariasPalavrasSemAccentos') ";
            $and .= " OR LOWER(pl.Cabecalho) IN ('$searchVariasPalavrasSemPlural') ";
            foreach ($searchComPalavrasMeioSemPlural as $pos => $palavra) {
                $and .= " OR LOWER(pl.Cabecalho) like '%$palavra%' ";
            }
        }
    }

    $sql = " SELECT p.ID, pl.Cabecalho FROM products p INNER JOIN products_lang AS pl ON pl.ID_PAI=p.ID WHERE ( " . $and . " ) AND p.Aproved=1 AND pl.Lang='{$GLOBALS['site_lang']}' GROUP BY p.ID ORDER BY pl.Cabecalho ASC " . ($PRODS_LIMIT > 0 ? 'Limit ' . $PRODS_LIMIT : '');
    $rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
    if (!$rs->EOF()) {
        while (!$rs->EOF()) {
            $prods[$rs->db_data['ID']] = $rs->db_data['Cabecalho'];
            $rs->db_next();
        }
    }
    $rs->db_close();

    return $prods;
}

function searchProductsByFamilyName($type = 1, $searchstr)
{
    $db_link     = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
    $rs          = new DataObj();
    $PRODS_LIMIT = 0;
    if (!empty($vars) && array_key_exists('PRODS_LIMIT', $vars)) {
        $PRODS_LIMIT = $vars['PRODS_LIMIT'];
    }
    $prods                           = array();

    $and = '';
    if ($type == 1)
    {
        $and .= " LOWER(pfl.Cabecalho) Like '%$searchstr%' ";
    }
    else
    {
        $searchSemAcentos                = stripAccents($searchstr);
        $searchComPalavrasMeio           = str_replace(' ', '%', $searchstr);
        $searchSemPlural                 = stripPlural($searchstr);
        $searchPalavras                  = explode(' ', $searchstr);
        $searchVariasPalavras            = implode(',', $searchPalavras);
        $searchVariasPalavrasSemAccentos = $searchPalavras;
        foreach ($searchVariasPalavrasSemAccentos as $pos => $palavra) {
            $searchVariasPalavrasSemAccentos[$pos] = stripAccents($palavra);
        }
        $searchVariasPalavrasSemAccentos = implode(',', $searchVariasPalavrasSemAccentos);
        $searchVariasPalavrasSemPlural   = $searchPalavras;
        foreach ($searchVariasPalavrasSemPlural as $pos => $palavra) {
            $searchVariasPalavrasSemPlural[$pos] = stripPlural($palavra);
        }
        $searchVariasPalavrasSemPlural = implode(',', $searchVariasPalavrasSemPlural);

        $searchComPalavrasMeioSemPlural = $searchPalavras;
        foreach ($searchComPalavrasMeioSemPlural as $pos => $palavra) {
            $searchComPalavrasMeioSemPlural[$pos] = stripPlural($palavra);
        }

        if ($type == 2)
        {
            $and .= " LOWER(pfl.Cabecalho) like '%$searchstr' OR LOWER(pfl.Cabecalho) like '$searchstr%' ";
            $and .= " OR LOWER(pfl.Cabecalho) like '%$searchSemAcentos' OR LOWER(pfl.Cabecalho) like '$searchSemAcentos%' ";
            $and .= " OR LOWER(pfl.Cabecalho) like '%$searchComPalavrasMeio' OR LOWER(pfl.Cabecalho) like '$searchComPalavrasMeio%' ";
            $and .= " OR LOWER(pfl.Cabecalho) like '%$searchSemPlural' OR LOWER(pfl.Cabecalho) like '$searchSemPlural%' ";
        } elseif ($type == 3) {
            $and .= " LOWER(pfl.Cabecalho) like '%$searchstr' OR LOWER(pfl.Cabecalho) like '$searchstr%' ";
            $and .= " OR LOWER(pfl.Cabecalho) like '%$searchSemAcentos' OR LOWER(pfl.Cabecalho) like '$searchSemAcentos%' ";
            $and .= " OR LOWER(pfl.Cabecalho) like '%$searchComPalavrasMeio' OR LOWER(pfl.Cabecalho) like '$searchComPalavrasMeio%' ";
            $and .= " OR LOWER(pfl.Cabecalho) like '%$searchSemPlural' OR LOWER(pfl.Cabecalho) like '$searchSemPlural%' ";
            $and .= " OR (INSTR('$searchstr', LOWER(pfl.Cabecalho)) > 0 AND pfl.Cabecalho <> '') OR LOWER(pfl.Cabecalho) like '%$searchstr%' ";
            $and .= " OR (INSTR('$searchSemAcentos', LOWER(pfl.Cabecalho)) > 0 AND pfl.Cabecalho <> '') OR LOWER(pfl.Cabecalho) like '%$searchSemAcentos%' ";
            $and .= " OR LOWER(pfl.Cabecalho) like '%$searchComPalavrasMeio%' ";
            $and .= " OR (INSTR('$searchSemPlural', LOWER(pfl.Cabecalho)) > 0 AND pfl.Cabecalho <> '') OR LOWER(pfl.Cabecalho) like '%$searchSemPlural%' ";
            $and .= " OR LOWER(pfl.Cabecalho) IN ('$searchVariasPalavras') ";
            $and .= " OR LOWER(pfl.Cabecalho) IN ('$searchVariasPalavrasSemAccentos') ";
            $and .= " OR LOWER(pfl.Cabecalho) IN ('$searchVariasPalavrasSemPlural') ";
            foreach ($searchComPalavrasMeioSemPlural as $pos => $palavra) {
                $and .= " OR LOWER(pfl.Cabecalho) like '%$palavra%' ";
            }
        }
    }

    $sql = " SELECT p.ID, pl.Cabecalho FROM products p INNER JOIN products_lang AS pl ON pl.ID_PAI=p.ID INNER JOIN filters_products AS fp ON fp.prod_id=p.ID INNER JOIN products_families AS pf ON (fp.fam_id1=pf.ID OR fp.fam_id2=pf.ID OR fp.fam_id3=pf.ID OR fp.fam_id4=pf.ID) INNER JOIN products_families_lang AS pfl ON pfl.ID_PAI=pf.ID WHERE ( " . $and . " ) AND p.Aproved=1 AND pl.Lang='{$GLOBALS['site_lang']}' AND pf.Visivel=1 AND pfl.Lang='{$GLOBALS['site_lang']}' GROUP BY p.ID ORDER BY pl.Cabecalho ASC " . ($PRODS_LIMIT > 0 ? 'Limit ' . $PRODS_LIMIT : '');
    $rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
    if (!$rs->EOF()) {
        while (!$rs->EOF()) {
            $prods[$rs->db_data['ID']] = $rs->db_data['Cabecalho'];
            $rs->db_next();
        }
    }
    $rs->db_close();

    return $prods;
}

function searchProductsByBrandName($type = 1, $searchstr)
{
    $db_link     = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
    $rs          = new DataObj();
    $PRODS_LIMIT = 0;
    if (!empty($vars) && array_key_exists('PRODS_LIMIT', $vars)) {
        $PRODS_LIMIT = $vars['PRODS_LIMIT'];
    }
    $prods                           = array();
    $searchSemAcentos                = stripAccents($searchstr);
    $searchComPalavrasMeio           = str_replace(' ', '%', $searchstr);
    $searchSemPlural                 = stripPlural($searchstr);
    $searchPalavras                  = explode(' ', $searchstr);
    $searchVariasPalavras            = implode(',', $searchPalavras);
    $searchVariasPalavrasSemAccentos = $searchPalavras;
    foreach ($searchVariasPalavrasSemAccentos as $pos => $palavra) {
        $searchVariasPalavrasSemAccentos[$pos] = stripAccents($palavra);
    }
    $searchVariasPalavrasSemAccentos = implode(',', $searchVariasPalavrasSemAccentos);
    $searchVariasPalavrasSemPlural   = $searchPalavras;
    foreach ($searchVariasPalavrasSemPlural as $pos => $palavra) {
        $searchVariasPalavrasSemPlural[$pos] = stripPlural($palavra);
    }
    $searchVariasPalavrasSemPlural = implode(',', $searchVariasPalavrasSemPlural);

    $searchComPalavrasMeioSemPlural = $searchPalavras;
    foreach ($searchComPalavrasMeioSemPlural as $pos => $palavra) {
        $searchComPalavrasMeioSemPlural[$pos] = stripPlural($palavra);
    }
    $and = '';
    if ($type == 1) {
        $and .= " LOWER(bl.Nome) = '$searchstr' ";
    } elseif ($type == 2) {
        $and .= " LOWER(bl.Nome) like '%$searchstr' OR LOWER(bl.Nome) like '$searchstr%' ";
        $and .= " OR LOWER(bl.Nome) like '%$searchSemAcentos' OR LOWER(bl.Nome) like '$searchSemAcentos%' ";
        $and .= " OR LOWER(bl.Nome) like '%$searchComPalavrasMeio' OR LOWER(bl.Nome) like '$searchComPalavrasMeio%' ";
        $and .= " OR LOWER(bl.Nome) like '%$searchSemPlural' OR LOWER(bl.Nome) like '$searchSemPlural%' ";
    } elseif ($type == 3) {
        $and .= " LOWER(bl.Nome) like '%$searchstr' OR LOWER(bl.Nome) like '$searchstr%' ";
        $and .= " OR LOWER(bl.Nome) like '%$searchSemAcentos' OR LOWER(bl.Nome) like '$searchSemAcentos%' ";
        $and .= " OR LOWER(bl.Nome) like '%$searchComPalavrasMeio' OR LOWER(bl.Nome) like '$searchComPalavrasMeio%' ";
        $and .= " OR LOWER(bl.Nome) like '%$searchSemPlural' OR LOWER(bl.Nome) like '$searchSemPlural%' ";
        $and .= " OR (INSTR('$searchstr', LOWER(bl.Nome)) > 0 AND bl.Nome <> '') OR LOWER(bl.Nome) like '%$searchstr%' ";
        $and .= " OR (INSTR('$searchSemAcentos', LOWER(bl.Nome)) > 0 AND bl.Nome <> '') OR LOWER(bl.Nome) like '%$searchSemAcentos%' ";
        $and .= " OR LOWER(bl.Nome) like '%$searchComPalavrasMeio%' ";
        $and .= " OR (INSTR('$searchSemPlural', LOWER(bl.Nome)) > 0 AND bl.Nome <> '') OR LOWER(bl.Nome) like '%$searchSemPlural%' ";
        $and .= " OR LOWER(bl.Nome) IN ('$searchVariasPalavras') ";
        $and .= " OR LOWER(bl.Nome) IN ('$searchVariasPalavrasSemAccentos') ";
        $and .= " OR LOWER(bl.Nome) IN ('$searchVariasPalavrasSemPlural') ";
        foreach ($searchComPalavrasMeioSemPlural as $pos => $palavra) {
            $and .= " OR LOWER(bl.Nome) like '%$palavra%' ";
        }
    }

    $sql = " SELECT p.ID, pl.Cabecalho FROM products p INNER JOIN products_lang AS pl ON pl.ID_PAI=p.ID INNER JOIN filters_products AS fp ON fp.prod_id=p.ID INNER JOIN brands AS b ON fp.marca_id=b.ID INNER JOIN brands_lang AS bl ON bl.ID_PAI=b.ID WHERE ( " . $and . " ) AND p.Aproved=1 AND pl.Lang='{$GLOBALS['site_lang']}' AND b.Visivel=1 AND bl.Lang='{$GLOBALS['site_lang']}' GROUP BY p.ID ORDER BY pl.Cabecalho ASC " . ($PRODS_LIMIT > 0 ? 'Limit ' . $PRODS_LIMIT : '');
    $rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
    if (!$rs->EOF()) {
        while (!$rs->EOF()) {
            $prods[$rs->db_data['ID']] = $rs->db_data['Cabecalho'];
            $rs->db_next();
        }
    }
    $rs->db_close();

    return $prods;
}




function getCategoriesProds($prod_id=0, $lingua='')
{
    global $DB_host,$DB_user,$DB_pass,$DB_name;
    if ($lingua === '') $lingua = isset($GLOBALS['site_lang']) ? $GLOBALS['site_lang'] : $GLOBALS['admin_lang'];
    $db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs = new DataObj;
    // Read the BO assignments directly: the search accelerator can omit recent edits.
    // Areas 0 and 2 mean listing-only and both listing/product selection.
    $sql = 'SELECT fp.cat_id, fp.valor_id, fc.category_order, fv.value_order,
                   fcl.category_name, fcl.category_name2, fvl.values_name, fvl.values_name2
            FROM filters_products fp
            INNER JOIN filters_categories fc ON fc.category_id=fp.cat_id
            INNER JOIN filters_categories_lang fcl ON fcl.category_id=fp.cat_id
            INNER JOIN filters_values fv ON fv.value_id=fp.valor_id AND fv.category_id=fp.cat_id
            INNER JOIN filters_values_lang fvl ON fvl.values_id=fp.valor_id
            WHERE fp.prod_id='.(int)$prod_id.' AND fp.filtro_active=1
              AND fp.filtro_area IN (0,2) AND fc.category_active=1 AND fv.value_active=1
              AND fcl.category_lang="'.db_real_escape_string($lingua).'"
              AND fvl.values_lang="'.db_real_escape_string($lingua).'"
            ORDER BY fc.category_order ASC, fp.cat_id ASC, fv.value_order ASC, fp.valor_id ASC';
    $filters = array();
    $seen = array();
    $rs->db_open($db_link,$DB_name,$sql);
    while(!$rs->EOF())
    {
        $row = $rs->db_data;
        $id = (int)$row['cat_id'].'_'.(int)$row['valor_id'];
        $category = trim($row['category_name2']) !== '' ? $row['category_name2'] : $row['category_name'];
        $value = trim($row['values_name2']) !== '' ? $row['values_name2'] : $row['values_name'];
        if (!isset($seen[$id]) && trim($category) !== '' && trim($value) !== '') {
            $filters[] = json_encode(array(
                'cat' => $value, 'c_id' => $id, 'cat_name' => $category,
                'cat_order' => (int)$row['category_order'], 'value_order' => (int)$row['value_order']
            ), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
            $seen[$id] = true;
        }
        $rs->db_next();
    }
    $rs->db_close();
    return implode(',', $filters);
}

/*function getCategoriesProds__($prod_id=0)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name;
    $db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs = new DataObj;
    $sql = 'SELECT Filtros FROM acelerador_pesquisa WHERE ID_Prod='.$prod_id.' Limit 0,1';
    $rs->db_open($db_link,$DB_name,$sql);
    if(!$rs->EOF())
    {
        $combo = $rs->db_data["Filtros"];
        $combos_separados=explode(";",$combo);
        $return='';
        foreach($combos_separados as $values)
        {
            $componentes=explode("_",$values);
            $id_cat=$componentes[0];
            $id_value=$componentes[1];
            
            if($id_cat!='' && $id_value!='')
            {
                $valor_name=buscavalorfiltros($id_value);
                if($return != '') $return .= ',{ "cat" : "'.$valor_name.'","c_id" : "'.$id_cat.'_'.$id_value.'" }';
                else $return .= '{ "cat" : "'.$valor_name.'","c_id" : "'.$id_cat.'_'.$id_value.'" }';
            }
        }
    }
    $rs->db_close();
    return $return;
}*/

function getCategoriesProds_($prod_id=0)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name;
    $db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $familia = new DataObj;
    $arr_valores = array();
    $sql = 'SELECT cat_id,valor_id FROM filters_products WHERE prod_id='.$prod_id.' GROUP BY valor_id ';

    $familia->db_open($db_link,$DB_name,$sql);
    if(!$familia->EOF())
    {
        $old_cat_id = array('');
        while(!$familia->EOF())
        {
            $valor_id=$familia->db_data['valor_id'];
            $valor_nome=buscavalorfiltros($valor_id);
            if($valor_nome) $arr_valores[] = array($familia->db_data['cat_id'].'_'.$valor_id, $valor_nome);
            $familia->db_next();
        }
    }
    $familia->db_close();
    $return='';
    for($a=0; $a < count($arr_valores); $a++)
    {
        if($a > 0) $return .= ',{ "cat" : "'.$arr_valores[$a][1].'","c_id" : "'.$arr_valores[$a][0].'" }';
        else $return .= '{ "cat" : "'.$arr_valores[$a][1].'","c_id" : "'.$arr_valores[$a][0].'" }';
    }
    return $return;
}
function getFamiliesProds($families=array())
{
    $return='';
    if(!is_array($families)) $families=array();
    for($a=0; $a < count($families); $a++)
    {
        if($a > 0) $return .= ',{ "fam" : "'.$families[$a][0].'","f_id" : '.$families[$a][1].' }';
        else $return .= '{ "fam" : "'.$families[$a][0].'","f_id" : '.$families[$a][1].' }';
    }
    return $return;
}
function search_elastic($indice='',$str='')
{
    // Forth Step: Search Property Listing 
    $json_doc = array(
        "query" => [
            "multi_match" => array(
                 "query" =>  "*$str*",
                 "fields" =>["Cabecalho","Texto","Ref","EAN"],
                 "fuzziness" => "AUTO")
        ]
    );


    $_json_doc = array(
        "query" => [
            "match" => array(
                 "Cabecalho" =>  array(
                    "query" => "*$str*",
                    "auto_generate_synonyms_phrase_query" => "true")
             )
        ]
    );

    $_json_doc = array(
        "query" => [
            "match_phrase" => [
                    "Texto" => "*$str*"
             ]
        ]
    );


    $json_doc = json_encode($json_doc);
    $baseUri = 'http://localhost:9200/'.$indice.'/listing/_search?size=50';
    $curlInit = curl_init();
    curl_setopt($curlInit, CURLOPT_URL, $baseUri);
    curl_setopt($curlInit, CURLOPT_PORT, '9200');
    curl_setopt($curlInit, CURLOPT_TIMEOUT, 200);
    curl_setopt($curlInit, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curlInit, CURLOPT_FORBID_REUSE, 0);
    curl_setopt($curlInit, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($curlInit, CURLOPT_POSTFIELDS, $json_doc);
    curl_setopt($curlInit, CURLOPT_HTTPHEADER,
        array(
            'Content-Type:application/json'
        )
    );
    $response = curl_exec($curlInit);
    curl_close($curlInit);
    return $response;
}
