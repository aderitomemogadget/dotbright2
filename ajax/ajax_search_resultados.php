<?php
global $path;
$path="../core/";
require_once $path.'settings.php';
require_once $path.'seguranca.php';
require_once $path.'db_engine_pdo.php';
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


ini_set('display_errors',1);
        ini_set('display_startup_errors',1);
        error_reporting(1);

$searchstr=mb_strtolower(rtrim(ltrim($_GET['q'])),'UTF-8');
if ($searchstr != '')
{
    $familias=array();
    $maxResults = 10;
    $response   = '';
    $prods = searchProductsMemoryTable4($searchstr);

    foreach ($prods as $prodId => $prodNome)
    {
        $famNome=trim(explode('|', $prodNome)[1]);
        $fam_id=explode('|', $prodNome)[2];
        if($fam_id>0 && $famNome!='')$familias[$fam_id]=$famNome;
    }
    //-------------------------------------------------------------
    //  1. Mostrar FAMÍLIAS (categorias)

    $response .= '<div class="linha_familias">';
    $response .= '<h3><b>Categorias</b> relacionadas:</h3>';
    $response .= '<div class="lista_familias">';

    foreach ($familias as $fam_id => $famNome)
    {
        $p_seo = seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$fam_id, $fam_id,1);
        //$p_seo = 'pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$fam_id;
        $response .= '<div class="familia-item" onclick="go(\''.$p_seo.'\')">'.htmlspecialchars($famNome).'</div>';
    }
    $response .= '</div>';
    $response .= '</div>';





    /*
    $db_link = \db_opendb_conection($DB_host, $DB_user, $DB_pass);
    $rs = new DataObj;

    $sql = "
        SELECT pf.id, pfl.cabecalho as nome
        FROM products_families pf
        INNER JOIN products_families_lang pfl ON pf.ID = pfl.ID_PAI
        WHERE 
            pf.visivel = 1
            AND pfl.lang = '".$site_lang."'
            AND LOWER(pfl.cabecalho) LIKE '%".addslashes($searchstr)."%'
        ORDER BY pfl.cabecalho ASC
        LIMIT 5;
    ";
    $rs->db_open($db_link, $DB_name, $sql);

    if (!$rs->EOF())
    {
        $response .= '<div class="linha_familias">';
            $response .= '<h3><b>Categorias</b> relacionadas:</h3>';
            $response .= '<div class="lista_familias">';
                while (!$rs->EOF())
                {
                    $idFam = $rs->db_data['id'];
                    $nomeFam = $rs->db_data['nome'];
                    // $urlFam = seo('pages.php?page_id=2&fam='.$idFam, $idFam, 2);
                    $p_seo = seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$idFam, $idFam,1);
                    $response .= '<div class="familia-item" onclick="go(\''.$p_seo.'\')">'.htmlspecialchars($nomeFam).'</div>';
                    $rs->db_next();
                }
            $response .= '</div>';
        $response .= '</div>';
    }
    else{
        $return .= '<div class="linha_familias">';
            $return .= '<h3>Categorias que te podem interessar</h3>';
            $return .= '<div class="lista_familias">';
                $return .= 'Não foram encontradas categorias!';
            $return .= '</div>';
        $return .= '</div>';
    }*/


    //  2. Mostrar PRODUTOS (em Swiper)
    if (!empty($prods))
    {
        $response .= '<div class="linha_produtos">';
            $response .= '<h3><b>Produtos</b> mais relevantes encontrados:</h3>';
            $response .= '<div class="swiper searchSwiper">';
                $response .= '<div class="swiper-wrapper">';
                    $count = 0;
                    foreach ($prods as $prodId => $prodNome)
                    {
                        $prodNome=trim(explode('|', $prodNome)[0]);



                        if ($count++ >= $maxResults) break;

                        $img = getProductThumb($prodId);
                        $img_file = $img['url'];
                        $ordem = $img['ordem'];
                        $ext=$img['ext'];


                        // The resolver returns a verified image URL or the no-photo URL.
                        // Shared URLs are already absolute and cannot be checked as local files.
                        $img_url = preg_match('~^https?://~i', $img_file) ? $img_file : $GLOBALS['site_http_url'].$img_file;
                        $imagem = '<img src="'.htmlspecialchars($img_url, ENT_QUOTES, 'UTF-8').'" alt="'.htmlspecialchars($prodNome, ENT_QUOTES, 'UTF-8').'" />';
                        $url = seo('pages.php?page_id='.$GLOBALS['products_page_id'], $prodId, 2);
                        $response .= '<div class="swiper-slide">';
                            $response .= '<div class="opt" onclick="go(\''.$url.'\')">';
                                $response .= '<div class="image">'.$imagem.'</div>';
                                $response .= '<div class="name">'.stripslashes($prodNome).'</div>';
                            $response .= '</div>';
                        $response .= '</div>';
                    }
                $response .= '</div>';
                $response .= '<div class="swiper-button-prev"></div>';
                $response .= '<div class="swiper-button-next"></div>';
            $response .= '</div>';
        $response .= '</div>';
        
       $response .= '
            <script>
                function ajustarAlturaSlides(swiperSelector) {
                    var maxHeight = 0;
                    var slides = document.querySelectorAll(swiperSelector + " .swiper-slide");

                    slides.forEach(function(slide){
                        slide.style.height = "auto";
                        if (slide.offsetHeight > maxHeight) maxHeight = slide.offsetHeight;
                    });

                    slides.forEach(function(slide){
                        slide.style.height = maxHeight + "px";
                    });
                }

                var mySwiper = new Swiper(".searchSwiper", {
                    slidesPerView: 2,
                    spaceBetween: 3,
                    autoHeight: false,
                    watchSlidesProgress: true,
                    navigation: {
                        nextEl: ".swiper-button-next",
                        prevEl: ".swiper-button-prev",
                    },
                    breakpoints: {
                        900:  { slidesPerView: 3, spaceBetween: 3 },
                        1155: { slidesPerView: 4, spaceBetween: 3 },
                        1440: { slidesPerView: 5, spaceBetween: 3 },
                        1600: { slidesPerView: 6, spaceBetween: 3 },
                        1920: { slidesPerView: 7, spaceBetween: 3 }
                    },
                    on: {
                        init: function() {
                            ajustarAlturaSlides(".searchSwiper");
                        },
                        resize: function() {
                            ajustarAlturaSlides(".searchSwiper");
                        }
                    }
                });

                var btnPrev = document.querySelector(".searchSwiper .swiper-button-prev");
                btnPrev.style.display = "none";

                mySwiper.on("slideChange", function () {
                    btnPrev.style.display = mySwiper.isBeginning ? "none" : "flex";
                    var btnNext = document.querySelector(".searchSwiper .swiper-button-next");
                    btnNext.style.display = mySwiper.isEnd ? "none" : "flex";
                });


                var swiperEl = document.querySelector("#search_resultados .searchSwiper");
                swiperEl.style.padding = "15px 40px 20px 0";
                function atualizarPadding()
                {
                    if (mySwiper.isBeginning)
                    {
                        swiperEl.style.padding = "15px 40px 20px 0";
                    }
                    else
                    {
                        swiperEl.style.padding = "15px 20px 20px";
                    }
                }
                atualizarPadding();
                mySwiper.on("slideChange", atualizarPadding);
            </script>';

    }

    if ($response == '')
    {
        $response = '<div class="opt HvRm">'.$GLOBALS['key_search_sem_sugestoes'][$GLOBALS['site_lang']].'</div>';
    }


    echo $response;
}



function getProductThumb($prod_id=0)
{
    $baseDir = rtrim($GLOBALS['site_shell_url'],'/') . '/files/products/';
    $baseUrl = 'files/products/';

    $ordens = [0,1,2,3];
    $exts   = ['webp','jpg','jpeg','png'];

    foreach($ordens as $ordem){

        foreach($exts as $ext){

            $fileThumb = $baseDir . $prod_id . '_' . $ordem . '_thum.' . $ext;

            if(dotbright_image_exists($fileThumb)){
                return [
                    'url'   => dotbright_image_url($baseUrl . $prod_id . '_' . $ordem . '_thum.' . $ext),
                    'ordem' => $ordem,
                    'ext'   => $ext,
                    'type'  => 'thumb'
                ];
            }

            $fileOriginal = $baseDir . $prod_id . '_' . $ordem . '.' . $ext;

            if(dotbright_image_exists($fileOriginal)){
                return [
                    'url'   => dotbright_image_url($baseUrl . $prod_id . '_' . $ordem . '.' . $ext),
                    'ordem' => $ordem,
                    'ext'   => $ext,
                    'type'  => 'original'
                ];
            }
        }
    }

    return [
        'url'   => rtrim($GLOBALS['site_http_url'],'/') . '/layout/'.PROJETO.'/images/no_photo.png',
        'ordem' => null,
        'ext'   => null,
        'type'  => 'fallback'
    ];
}
