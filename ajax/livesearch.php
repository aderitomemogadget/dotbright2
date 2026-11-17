<?php
global $path;
$path="../core/";
require_once $path.'settings.php';

require_once $path.'seguranca.php';
require_once $path.DB_ENGINE;
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

//$searchstr=mb_strtolower(rtrim(ltrim($_GET['q'])),'UTF-8');
$searchstr=trim($_GET['q']);
if($searchstr != '')
{
    $maxResults=10;
    $totalRecords=0;
    $hint        ='';

    $prods=searchProductsMemoryTable($searchstr,11);

    if(!empty($prods))
    {
        foreach($prods as $prodId => $prodNome)
        {
            $totalRecords++;
            if($totalRecords <= $maxResults)
            {
                $img_file='files/products/'.$prodId.'_1_small.png';
                $imagem  ='';
                if(file_exists($GLOBALS['site_shell_url'].$img_file))
                {
                    $imagem='<img border="0" src="'.$GLOBALS['site_http_url'].$img_file.'" />';
                }
                $url =seo('pages.php?page_id='.$GLOBALS['products_page_id'],$prodId,2);
                if($prodNome!='')$hint .= '<div onclick="go(\''.$url.'\')" class="opt"><div class="image">'.$imagem.'</div><div class="name">'.stripslashes($prodNome).'</div></div>';
            }
            else
            {
                break;
            }
        }
    }


    if($totalRecords >=($maxResults + 1))
    {
        $hint .= '<div onclick="fs(\''.$searchstr.'\',\'\')" class="opt" style="height:30px;line-height:30px;text-align:center;background-color:#000000;color:#ffffff;width:100%">VER TODOS</div>';
    }

    $qtd=1;

    if($hint == "") {
        $response='<div class="opt HvRm" onclick="$(\'#search_hinters\').hide();">'.$GLOBALS['key_search_sem_sugestoes'][$GLOBALS['site_lang']].'</div>';
    } else {
        $response=$hint;
    }
    echo $response;
}