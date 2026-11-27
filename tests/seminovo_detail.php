<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require dirname(__DIR__).'/layout/dotbright/code/seminovo_detail.php';
function verify_semi($test, $label) { if (!$test) throw new RuntimeException($label); echo 'PASS '.$label."\n"; }
$base = array('fam_id'=>163, 'prod_id'=>143608, 'prod_nome'=>'(SEMINOVO) IPHONE 18 PRO MAX 2TB GLACIAR',
    'prod_estado'=>'A+', 'prod_bateria'=>'100%', 'prod_garantia'=>'Garantia definida no catálogo', 'prod_precos'=>array('STOCK'=>1));
foreach (array('A+','A','B') as $grade) {
    foreach (array('100%','86','79','0','', '101', '<script>bad</script>') as $battery) {
        $p=$base; $p['prod_estado']=$grade; $p['prod_bateria']=$battery; $d=dotbright_seminovo_detail_data($p);
        verify_semi($d['grade']===$grade, 'preserve grade '.$grade.' / battery '.$battery);
        verify_semi($d['battery']===null || ($d['battery']>=0 && $d['battery']<=100), 'battery is optional and bounded');
    }
}
$p=$base;$p['fam_id']=1;
verify_semi(dotbright_seminovo_detail($p,'<main>Original new product</main>')==='<main>Original new product</main>', 'new products are unchanged');
$p['fam_id']=85;
verify_semi(dotbright_seminovo_detail($p,'repair form')==='repair form', 'repair form is unchanged');
$p=$base;$p['prod_nome']='</script><script>alert(1)</script>'; $p['prod_precos']['STOCK']=0;
$html=dotbright_seminovo_detail($p,'<button onclick="existingPurchase(143608)">Comprar</button>');
verify_semi(strpos($html,'<button onclick="existingPurchase(143608)">Comprar</button>')!==false, 'original purchase markup and action preserved');
verify_semi(strpos($html,'</script><script>alert(1)</script>')===false, 'JSON payload cannot inject scripts');
verify_semi(dotbright_seminovo_detail_data($p)['stock']===0.0, 'stock data is not changed');
verify_semi(dotbright_seminovo_detail_data($base)['warranty']==='Garantia definida no catálogo', 'warranty comes from existing data');
$card=array('id'=>1,'title'=>'(SEMINOVO) IPHONE 12 128GB BLACK');
$data=json_decode(html_entity_decode(dotbright_seminovo_related_data($card,$base),ENT_QUOTES,'UTF-8'),true);
verify_semi(strpos($data['title'],'GRAU: A+')!==false && strpos($data['title'],'BAT: 100%')!==false, 'related catalogue component receives actual condition and battery');
$source=file_get_contents(dirname(__DIR__).'/layout/dotbright/code/produto.php');
verify_semi(strpos($source,'return dotbright_seminovo_detail($prods, $return);')!==false, 'real product renderer uses the scoped presentation');
verify_semi(strpos($source,'onclick="adicionarCarrinhoProdutoOpcoes(')!==false, 'existing cart action is retained');
