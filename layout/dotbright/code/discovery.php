<?php
// Uses Dotbright's catalogue and pricing engine; no schema or customer writes.
if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) { http_response_code(404); exit; }
function db_disc_escape($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function db_disc_cache_allowed() {
    static $allowed=null;
    if($allowed!==null)return $allowed;
    $method=$_SERVER['REQUEST_METHOD']??'GET';
    $allowed=$method==='GET'
        && empty($GLOBALS['cli_id'])
        && empty($GLOBALS['admin_id'])
        && empty($GLOBALS['online_edit'])
        && empty($GLOBALS['site_prev'])
        && function_exists('apcu_fetch')
        && function_exists('apcu_store')
        && (!function_exists('apcu_enabled') || apcu_enabled());
    return $allowed;
}
function db_disc_cache_key($group,$identity) {
    $host=$_SERVER['HTTP_HOST']??'dotbright.pt';
    $database=$GLOBALS['DB_name']??'';
    return 'dotbright:discovery:v1:'.$group.':'.hash('sha256',$host.'|'.$database.'|'.dotbright_product_images_cache_key().'|'.$identity);
}
function db_disc_cache_fetch($group,$identity,&$found) {
    $found=false;
    if(!db_disc_cache_allowed())return null;
    return apcu_fetch(db_disc_cache_key($group,$identity),$found);
}
function db_disc_cache_store($group,$identity,$value,$ttl) {
    if(db_disc_cache_allowed())apcu_store(db_disc_cache_key($group,$identity),$value,(int)$ttl);
    return $value;
}
function db_disc_rows($sql,$ttl=120) {
    $identity=($GLOBALS['site_lang']??'pt').'|'.$sql;
    $cached=db_disc_cache_fetch('query',$identity,$found);
    if($found && is_array($cached))return $cached;
    $db=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
    $rs=new DataObj; $rs->db_open($db,$GLOBALS['DB_name'],$sql); $rows=array();
    while(!$rs->EOF()) { $rows[]=$rs->db_data; $rs->db_next(); } $rs->db_close();
    return db_disc_cache_store('query',$identity,$rows,$ttl);
}
function db_disc_categories($parent=1) {
    $lang=addslashes($GLOBALS['site_lang']); $parent=(int)$parent;
    return db_disc_rows("SELECT pf.ID,pf.ID_ORG,pf.img_ext,pl.Cabecalho FROM products_families pf INNER JOIN products_families_lang pl ON pl.ID_PAI=pf.ID WHERE pf.ID_ORG=$parent AND pf.visivel=1 AND pl.Lang='$lang' ORDER BY pf.Ordem,pf.ID LIMIT 40",300);
}
function db_disc_roots() { $rows=db_disc_categories(1); return $rows ?: db_disc_categories(0); }
function db_disc_category_url($id) { return seo('pages.php?page_id='.(int)$GLOBALS['catalog_page_id'].'&cat='.(int)$id,(int)$id,1); }
function db_disc_category_fallback($id) {
    $fallbacks=array(
        2935=>'laptops.webp',
        3525=>'smart-pet.webp',
        3572=>'mens-grooming.webp',
        3604=>'garment-care.webp',
        3643=>'audio-recorder.webp',
        3649=>'usb-c-usb-b.webp'
    );
    return isset($fallbacks[(int)$id])?'layout/dotbright/images/category-fallbacks/'.$fallbacks[(int)$id]:'';
}
function db_disc_category_product_images() {
    static $images=null;
    if(is_array($images))return $images;
    $images=array();
    $roots=db_disc_roots();
    $rootIds=array_values(array_filter(array_map('intval',array_column($roots,'ID'))));
    if(!$rootIds)return $images;
    $rootList=implode(',',$rootIds);
    $sql="WITH RECURSIVE category_tree(root_id,category_id) AS (
        SELECT pf.ID,pf.ID FROM products_families pf WHERE pf.ID_ORG IN ($rootList) AND pf.visivel=1
        UNION ALL
        SELECT category_tree.root_id,pf.ID FROM category_tree INNER JOIN products_families pf ON pf.ID_ORG=category_tree.category_id WHERE pf.visivel=1
    ), ranked AS (
        SELECT category_tree.root_id,pi.ID_ORG product_id,pi.Ordem,pi.img_ext,
            ROW_NUMBER() OVER(PARTITION BY category_tree.root_id ORDER BY p.Data_criacao DESC,pi.Ordem) image_rank
        FROM category_tree
        INNER JOIN products_families_obj fpo ON fpo.ID_ORG=category_tree.category_id
        INNER JOIN products p ON p.ID=fpo.ID_PROD AND p.Aproved=1
        INNER JOIN products_images pi ON pi.ID_ORG=p.ID
    )
    SELECT root_id,product_id,Ordem,img_ext FROM ranked WHERE image_rank<=8 ORDER BY root_id,image_rank";
    $rows=db_disc_rows($sql,900);
    $siteRoot=rtrim((string)($GLOBALS['site_shell_url']??''),'/\\').DIRECTORY_SEPARATOR;
    foreach($rows as $row){
        $categoryId=(int)$row['root_id'];
        if(isset($images[$categoryId]))continue;
        $productId=(int)$row['product_id'];$order=(int)$row['Ordem'];
        $extension=strtolower((string)$row['img_ext']);
        if(!$productId||!$order||!preg_match('/^[a-z0-9]{2,5}$/i',$extension))continue;
        $thumb='files/products/'.$productId.'_'.$order.'_thum.png';
        $original='files/products/'.$productId.'_'.$order.'.'.$extension;
        if(dotbright_image_exists($siteRoot.str_replace('/',DIRECTORY_SEPARATOR,$thumb)))$images[$categoryId]=dotbright_image_url($thumb);
        elseif(dotbright_image_exists($siteRoot.str_replace('/',DIRECTORY_SEPARATOR,$original)))$images[$categoryId]=dotbright_image_url($original);
    }
    return $images;
}
function db_disc_category_image($category) {
    $id=(int)($category['ID']??0);
    $siteRoot=rtrim((string)($GLOBALS['site_shell_url']??''),'/\\').DIRECTORY_SEPARATOR;
    $extension=strtolower((string)($category['img_ext']??''));
    if($id&&preg_match('/^[a-z0-9]{2,5}$/i',$extension)){
        $familyImage='files/products_families/'.$id.'_1.'.$extension;
        if(dotbright_image_exists($siteRoot.str_replace('/',DIRECTORY_SEPARATOR,$familyImage)))return $familyImage;
    }
    $productImages=db_disc_category_product_images();
    if(isset($productImages[$id]))return $productImages[$id];
    $fallback=db_disc_category_fallback($id);
    if($fallback!==''&&dotbright_image_exists($siteRoot.str_replace('/',DIRECTORY_SEPARATOR,$fallback)))return $fallback;
    return '';
}
function db_disc_has_physical_image($id,$images) {
    $siteRoot=rtrim((string)($GLOBALS['site_shell_url']??''),'/\\');
    if($siteRoot==='')return false;
    $root=$siteRoot.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'products'.DIRECTORY_SEPARATOR;
    foreach(array_filter(explode(',',(string)$images)) as $image){
        list($order,$extension)=array_pad(explode(':',$image,2),2,'');
        if($order!=='' && preg_match('/^[a-z0-9]{2,5}$/i',$extension)
            && dotbright_image_exists($root.(int)$id.'_'.(int)$order.'.'.strtolower($extension)))return true;
    }
    return false;
}
function db_disc_ids($kind) {
    static $memo=array(); if(isset($memo[$kind]))return $memo[$kind];
    $lang=addslashes($GLOBALS['site_lang']); $kind=$kind==='destaques'?'destaques':'novidades';
    $filter=$kind==='destaques'?' AND po.obj_destaque=1':'';
    $rows=db_disc_rows("SELECT p.ID,p.Data_criacao,GROUP_CONCAT(DISTINCT CONCAT(pi.Ordem,':',pi.img_ext) ORDER BY pi.Ordem SEPARATOR ',') Images FROM products p INNER JOIN products_lang pl ON pl.ID_PAI=p.ID LEFT JOIN products_images pi ON pi.ID_ORG=p.ID WHERE p.Aproved=1 AND pl.Lang='$lang' AND EXISTS (SELECT 1 FROM products_objs po WHERE po.obj_type_id=p.ID AND (po.obj_val2=0 OR po.obj_val2 IS NULL) AND po.obj_stock>0 AND po.obj_pvp>0 $filter) AND EXISTS (SELECT 1 FROM products_families_obj fpo INNER JOIN products_families pf ON pf.ID=fpo.ID_ORG WHERE fpo.ID_Prod=p.ID AND pf.visivel=1) GROUP BY p.ID,p.Data_criacao ORDER BY p.Data_criacao DESC,p.ID DESC LIMIT 500",60);
    $ids=array();
    foreach($rows as $row){
        if(db_disc_has_physical_image((int)$row['ID'],(string)($row['Images']??'')))$ids[]=(int)$row['ID'];
        if(count($ids)>=120)break;
    }
    return $memo[$kind]=$ids;
}
function db_disc_card_data($id) {
    static $requestCache=array();
    $id=(int)$id;
    if(array_key_exists($id,$requestCache))return $requestCache[$id];
    $identity=($GLOBALS['site_lang']??'pt').'|'.$id;
    $data=db_disc_cache_fetch('card',$identity,$found);
    if($found && is_array($data))return $requestCache[$id]=$data;
    $p=get_produtc_info($id);
    if(!is_array($p)||empty($p['prod_id']))return $requestCache[$id]=null;
    $data=array(
        'prod_id'=>(int)$p['prod_id'],
        'name'=>html_entity_decode(strip_tags($p['prod_nome']),ENT_QUOTES,'UTF-8'),
        'image'=>$p['prd_imgm_new']?:$p['prd_imgm_med'],
        'price'=>(float)($p['prod_precos']['PVP']??0)
    );
    db_disc_cache_store('card',$identity,$data,45);
    return $requestCache[$id]=$data;
}
function db_disc_card($id,$mini=false) {
    $p=db_disc_card_data($id);
    if(!$p)return;
    $url=seo('pages.php?page_id='.(int)$GLOBALS['products_page_id'],(int)$id,2);
    $name=$p['name']; $image=$p['image']; $price=$p['price'];
    echo '<article class="db-disc-card'.($mini?' db-disc-mini':'').'" data-product-id="'.(int)$id.'"><a class="db-disc-photo" href="'.db_disc_escape($url).'"><img loading="lazy" decoding="async" src="'.db_disc_escape($image).'" alt="'.db_disc_escape($name).'"></a><div class="db-disc-copy"><h3><a href="'.db_disc_escape($url).'">'.db_disc_escape($name).'</a></h3><strong>'.($price>0?numberformat($price,1):'Consultar').'</strong>';
    if(!$mini)echo '<a class="db-disc-view" href="'.db_disc_escape($url).'">Ver produto <span aria-hidden="true">→</span></a>';
    echo '</div></article>';
}
function db_disc_area($root,$full=false) {
    $id=(int)$root['ID']; $name=$root['Cabecalho']; $children=db_disc_categories($id);
    echo '<article class="db-disc-area"><h3><a href="'.db_disc_escape(db_disc_category_url($id)).'">'.db_disc_escape($name).' <span aria-hidden="true">→</span></a></h3><div class="db-disc-subcats">';
    foreach(array_slice($children,0,$full?8:4) as $c){
        $img=db_disc_category_image($c);
        echo '<a href="'.db_disc_escape(db_disc_category_url($c['ID'])).'">';
        if($img!=='')echo '<img class="db-disc-category-photo" loading="lazy" decoding="async" width="320" height="200" src="'.db_disc_escape($img).'" alt="">';
        else echo '<svg viewBox="0 0 48 48" aria-hidden="true"><rect x="8" y="6" width="32" height="28" rx="4"/><path d="M18 41h12M24 34v7M15 14h18M15 21h12"/></svg>';
        echo '<span>'.db_disc_escape($c['Cabecalho']).'</span></a>';
    }
    if(!$children)echo '<a href="'.db_disc_escape(db_disc_category_url($id)).'">Explorar produtos →</a>';
    echo '</div></article>';
}
function db_disc_nav(){echo '<nav class="db-disc-nav" aria-label="Descobrir a Dotbright"><a href="novidades.php">Novidades</a><a href="destaques.php">Destaques</a><a href="categorias.php">Todas as categorias <span aria-hidden="true">→</span></a></nav>';}
function db_disc_home(){
    static $done=false;if($done)return;$done=true;
    echo '<div class="db-discovery db-discovery-home">';db_disc_nav();
    echo '<section><div class="db-disc-heading"><h2>Novidades</h2><a href="novidades.php">Ver todas →</a></div><div class="db-disc-grid db-disc-home-new">';
    foreach(array_slice(db_disc_ids('novidades'),0,12) as $id)db_disc_card($id);echo '</div></section>';
    echo '<section><div class="db-disc-heading"><h2>Produtos em destaque</h2><a href="destaques.php">Ver mais →</a></div><div class="db-disc-minis">';
    $ids=array_slice(db_disc_ids('destaques'),0,4);foreach($ids as $id)db_disc_card($id,true);if(!$ids)echo '<p>Estamos a preparar novos destaques. Descobre entretanto as novidades.</p>';echo '</div></section>';
    echo '<section><div class="db-disc-heading"><div><h2>Áreas temáticas</h2><p>Inspira-te e explora as principais áreas da loja.</p></div><a href="categorias.php">Todas as categorias →</a></div><div class="db-disc-areas">';
    foreach(array_slice(db_disc_roots(),0,4) as $root)db_disc_area($root);echo '</div></section></div>';
}
function db_disc_page($kind){
    $title=$kind==='categorias'?'Todas as categorias':($kind==='destaques'?'Produtos em destaque':'Novidades');
    echo '<main class="db-discovery"><header class="db-disc-hero"><p><a href="/">Início</a> / '.db_disc_escape($title).'</p><h1>'.db_disc_escape($title).'</h1><p>Descobre a seleção Dotbright e encontra o que procuras.</p>';db_disc_nav();echo '</header><section>';
    if($kind==='categorias'){echo '<div class="db-disc-areas">';foreach(db_disc_roots() as $r)db_disc_area($r,true);echo '</div>';}
    else{
        $page=max(1,min(5,(int)($_GET['pagina']??1)));$ids=db_disc_ids($kind);$slice=array_slice($ids,($page-1)*24,24);
        echo '<div class="db-disc-grid">';foreach($slice as $id)db_disc_card($id);echo '</div>';
        if(!$slice)echo '<p>Sem produtos nesta seleção de momento. <a href="novidades.php">Ver novidades</a></p>';
        if(count($ids)>24){echo '<nav class="db-disc-pagination" aria-label="Páginas">';for($i=1;$i<=ceil(count($ids)/24);$i++)echo '<a '.($i===$page?'aria-current="page"':'').' href="'.$kind.'.php?pagina='.$i.'">'.$i.'</a>';echo '</nav>';}
    }
    echo '</section></main>';
}
