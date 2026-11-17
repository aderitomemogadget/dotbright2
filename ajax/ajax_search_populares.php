<?php
global $path;
$path="../core/";
require_once $path.'settings.php';
require_once $path.'seguranca.php';
require_once $path.'db_engine.php';
require_once $path.'settings_modulos.php';
require_once $path.'sessions_engine.php';
require_once $path.'global.php';
require_once $path.'admin_engine.php';
// MODULOS
require_once $path.'portes.php';
require_once $path.'media.php';
require_once $path.'menus.php';
require_once $path.'seo.php';
require_once $path.'linguas.php';
require_once $path.'gd.php';
require_once $path.'search.php';
require_once $path.'special.php';
require_once $path.'logins.php';
require_once $path.'produtos.php';
require_once $path.'promocoes.php';
require_once $path.'templates.php';
require_once $path.'filtros.php';
require_once $path.'return_filtros.php';

global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang;
$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
$rs = new DataObj;

$return = '';

$sql = "SELECT 
            ep.ID_Produto,
            COUNT(ep.ID_Produto) AS total_vendidos,
            pl.Cabecalho AS nome,
            pi.img_ext AS imagem
        FROM encomendas e 
        INNER JOIN encomendas_produtos ep ON e.ID = ep.ID_PAI
        INNER JOIN products p ON ep.ID_Produto = p.ID
        INNER JOIN products_lang pl ON p.ID = pl.ID_PAI
        LEFT JOIN products_images pi ON pi.ID_ORG = p.ID
        WHERE 1=1
            AND p.Aproved = 1
            AND e.Encomenda_Paga = 1
            AND pl.lang= '".$site_lang."'
        GROUP BY ep.ID_Produto
        ORDER BY total_vendidos DESC
        LIMIT 4";
$rs->db_open($db_link, $DB_name, $sql);
if (!$rs->EOF()){
    $return .= '<div class="search_populares">';
        $return .= '<h3>Produtos populares</h3>';
            $return .= '<div class="search_populares_list">';
        while(!$rs->EOF()){
            $ID_Produto = $rs->db_data['ID_Produto'];
            $nome = $rs->db_data['nome'];
            // $preco = $rs->db_data['preco'];
            $img_ext = $rs->db_data['imagem'];

            $img = !empty($img_ext) ? $site_http_url.'files/products/'.$ID_Produto.'_1.'.$img_ext : $site_http_url.'layouy/'.PROJETO.'/images/no-photo.jpg';


            $return .= '<div class="item">';
                $return .= '<img src="'.dotbright_image_url($img).'" alt="'.htmlspecialchars($row['nome']).'">';
                $return .= '<div class="info">';
                    $return .= '<span class="nome">'.$nome.'</span>';
                    $return .= '<span class="preco">'.$preco.'€</span>';
                $return .= '</div>';
            $return .= '</div>';
            $rs->db_next();
        }
    $return .= '</div>';
}
else{
    $return .= '<div class="search_populares">';
        $return .= '<h3>Produtos populares</h3>';
        $return .= '<div class="search_populares_list">';
            $return .= 'Não foram encontrados produtos!';
        $return .= '</div>';
    $return .= '</div>';
}

echo $return;