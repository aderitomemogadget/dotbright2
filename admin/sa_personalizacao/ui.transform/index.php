<?php
$path =  dirname( __FILE__ ).'/../../../core/';
require_once $path . 'core_files.php';
require_once $path . 'admin_globals.php';


function getCategoriesRecursive($id_org)
{
	$hConn=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$obj = new DataObj;
	$arvore2 = array();
	$pai = $id_org;
	while($pai > 0)
	{
		$id_org=$pai;
		$sql="SELECT bloco_id, bloco_id_pai FROM frames WHERE bloco_id=$pai";
		$obj->db_open($hConn,$GLOBALS['DB_name'],$sql);
		$pai=$obj->db_data["bloco_id_pai"];
		$arvore2[]=$obj->db_data["bloco_id"];
	}

	if(empty($arvore2)) $arvore2[] = $id_org;

	$output="";
	$t = (count($arvore2)-1);
	for($a=0;$a<count($arvore2);$a++)
	{
		$output[$t] = $arvore2[$a];
		$t--;
	}
	ksort($output);
	$obj->db_close();
	return $output;
}


if(isset($_GET['image']) && isset($_GET['id']) && $_GET['image']!='' && intval($_GET['id']) > 0 )
{

	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);

	$rs = new DataObj;

	// VAMOS SABER A CONFIGURAÇÃO PRINCIPAL as MEDIDAS E A IMAGEM
	$arr = getCategoriesRecursive(intval($_GET['id_pai']));
	$sql = 'SELECT width, height, bloco_ext FROM frames WHERE bloco_id="'.$arr[0].'"';

	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$height_prev = $rs->db_data['width'];
		$height_principal = $rs->db_data['height'];
		$bloco_ext = $rs->db_data['bloco_ext'];
	}
	$rs->db_close();

	// echo $bloco_ext;


	$image_type = ''; if($bloco_ext && file_exists('../../../files/frames/'.$bloco_ext)) $image_type = '../../../files/frames/'.$bloco_ext;

	$pos_top = 150; $pos_left = 150; $pos_width = 150; $pos_height = 150; $anglo = 0;
	// echo $image_type.'<br>';


	@list($width, $height) = @getimagesize($_GET['image']);

	// ESCOLHA SELECTBOX
	// if(isset($_GET['image_type']) && $_GET['image_type']!='') @list($width_prev, $height_prev) = @getimagesize($_GET['image_type']);


	// SALVAR CONFIGURAÇOES DA IMAGEM
	if(isset($_GET['gravar']) && $_GET['gravar'] == 1)
	{
		$skew = preg_replace("/[^0-9,.]/", '', $_GET['skew']);

		$sql = 'UPDATE frames_save SET skew="'.$skew.'", pos_top="'.$_GET['top'].'", pos_left="'.$_GET['left'].'", width="'.$_GET['width'].'", height="'.$_GET['height'].'", anglo="'.$_GET['anglo'].'", imagem_por_cima="'.$_GET['imagem_por_cima'].'" WHERE save_id='.$_GET['id'];
		$res = Runquery($db_link, $DB_name, $sql);

		echo '<script>window.open("index.php?image='.$_GET['image'].'&id='.$_GET['id'].'&id_pai='.$_GET['id_pai'].'","_self");</script>';
	}


	$sql = 'SELECT pos_top, pos_left, width, height, anglo, skew, imagem_por_cima FROM frames_save WHERE save_id="'.intval($_GET['id']).'"';
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		if($rs->db_data['pos_top']!=0) $pos_top = $rs->db_data['pos_top'];
		if($rs->db_data['pos_left']!=0) $pos_left = $rs->db_data['pos_left'];
		if($rs->db_data['width']!=0) $pos_width = $rs->db_data['width'];
		if($rs->db_data['height']!=0) $pos_height = $rs->db_data['height'];
		if($rs->db_data['anglo']!=0) $anglo = $rs->db_data['anglo'];
		if($rs->db_data['skew']!='') $skew = $rs->db_data['skew'];
		$imagem_por_cima = $rs->db_data['imagem_por_cima'];
	}
	$rs->db_close();




?><!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>tools - by memogadget&reg;</title>

<script type="text/javascript" src="js/jquery-1.5.2.js"></script>
<script type="text/javascript" src="js/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/jquery.transform.js"></script>
<script type="text/javascript" src="js/jquery.transformable.js"></script>
<script type="text/javascript" src="js/perspective-transform.js"></script>
<script type="text/javascript" src="js/main.js"></script>
<link rel="stylesheet" type="text/css" href="css/index.css" media="all">

  <style type="text/css">
        #container {
            position:relative;
            overflow: clip;
			background-image:url(<?php echo $_GET['image']?>);
			border:none;
			height:<?php echo $height?>px;
			width:<?php echo $width?>px;
        }
        #box {
            position: absolute;
            top: 0px;
            left: 0px;
			width: <?php echo $pos_width?>px;
			height: <?php echo $pos_height?>px;

            border: 1px solid red;
            transform-origin: 0 0;
            -webkit-transform-origin: 0 0;
            -moz-transform-origin: 0 0;
            -o-transform-origin: 0 0;
        }
        #box img {
            width: <?php echo $pos_width?>px;
            height: <?php echo $pos_height?>px;
        }
        .corner {
            position: absolute;
            top: 0px; left: 0px;
            border: 1px solid blue;
        }
    </style>
<script type="text/javascript">

$(document).ready(function()
{
	<?php /* var b=$('.box');
	var ib=$('.infobox');
	b.draggable({stop: draggableInfo});
	b.resizable({aspectRatio: true,stop: resizableInfo});
	b.transformable({rotateStop: rotateInfo, skewStop: skewInfo, rotate: showrotate});

	function constrainscale(e,ui) {}

	function showrotate(e,ui) { }


	function skewInfo (e,ui)
	{
		// console.log(ui);

		$('#elem-final').css("transform", $('.box').attr('data-transform'));
		$('#elem-final').css("-moz-transform", $('.box').attr('data-transform'));
		$('#elem-final').css("-webkit-transform", $('.box').attr('data-transform'));
		$('#elem-final').css("-o-transform", $('.box').attr('data-transform'));

		$('#skew').val(0+','+$('.box').attr('data-transform-skewy')+','+$('.box').attr('data-transform-skewx')+','+$('.box').attr('data-transform-scaley')+','+$('.box').attr('data-transform-scalex'));

	}


	function rotateInfo (e,ui)
	{
		var u=$(this).getTransform();
		anglo = ui.angle.rad;
		$('#elem-final').css("transform", "rotate(" + anglo + "rad)");
		$('#elem-final').css("-moz-transform", "rotate(" + anglo + "rad)");
		$('#elem-final').css("-webkit-transform", "rotate(" + anglo + "rad)");
		$('#elem-final').css("-o-transform", "rotate(" + anglo + "rad)");
		$('#anglo').val(anglo);
	}

	function draggableInfo(e,ui)
	{
		var o = $(this).tOffset();
		$('#left').val(o.left);
		$('#top').val(o.top);
		$('#elem-final').css({'top' : o.top+'px ','left' : o.left+'px', 'width' :  $('.box1').width()+'px ','height' : $('.box1').height()+'px'});
	}
	function resizableInfo (e,ui)
	{
		image_w = ui.size.width;
		image_h = ui.size.height;
		$('#width').val(image_w);
		$('#height').val(image_h);
		$('#elem-final').css({'width' : image_w+'px ','height' : image_h+'px'});
	}

	<?php if($skew!=''){?>
	$('#elem-final').css("transform", 'matrix(<?php echo $skew;?>)');
	$('#elem-final').css("-moz-transform", 'matrix(<?php echo $skew;?>)');
	$('#elem-final').css("-webkit-transform", 'matrix(<?php echo $skew;?>)');
	$('#elem-final').css("-o-transform", 'matrix(<?php echo $skew;?>)');
	<?php } */?>
});
</script>




<style>
#preview-zone {background-repeat: no-repeat; border:none; height:<?php echo $height?>px;width:<?php echo $width?>px;  }
#draggable-zone{ background-image:url(<?php echo $_GET['image']?>); border:none;height:<?php echo $height?>px; margin:0 auto; width:<?php echo $width?>px; position:absolute; top:0; left:0}
#elem-wrapper{visibility:<?php echo ($imagem_por_cima==1 ? 'visible' : 'hidden'); ?>}
#elem-final{position:absolute}
.box,.box1 {width: <?php echo $pos_width?>px; left: <?php echo $pos_left?>px; top: <?php echo $pos_top?>px;}
</style>

</head>
<body>
<!--button class="reset">Reset Me</button-->




    <div id="container">
        <div id="box">
            <img src="<?php echo $image_type?>" style="width: <?php echo $pos_width?>px;">
        </div>
        <div id="marker0" class="corner">TL</div>
        <div id="marker2" class="corner">TR</div>
        <div id="marker4" class="corner">BL</div>
        <div id="marker6" class="corner">BR</div>
    </div>





<?php if($image_type != ''){?>
<!--div id="preview-zone"><img src="<?php echo $image_type?>" style="width: <?php echo $pos_width?>px; left: <?php echo $pos_left?>px; top: <?php echo $pos_top?>px;" alt="" id="elem-final" /><div id="draggable-zone"></div><div class="box box1"><img src="<?php echo $image_type?>" style="width: <?php echo $pos_width?>px; " alt="" id="elem-wrapper" /></div></div-->
<div class="infobox" style="display:none">Div Transformation Info:</div>


<div class="zone">

	<form method="get" id="form_tipo2">
	<input type="hidden" name="gravar" value="1">
	<input type="hidden" name="image" value="<?php echo $_GET['image']?>">
	<input type="hidden" name="id" value="<?php echo $_GET['id']?>">
	<input type="hidden" name="id_pai" value="<?php echo $_GET['id_pai']?>">
	<input type="text" name="top" id="top" value="<?php echo $pos_top?>">	<input type="hidden" name="left" id="left" value="<?php echo $pos_left?>">
	<input type="text" name="width" id="width" value="<?php echo $pos_width?>"> <input type="hidden" name="height" id="height" value="<?php echo $pos_height?>">
	<input type="text" name="anglo" id="anglo" value="<?php echo $anglo?>">
	<input type="text" name="skew" id="skew" value="<?php echo $skew;?>" style="width:300px">
	Imagem por cima: <input type="checkbox" name="imagem_por_cima" id="imagem_por_cima" <?php echo ($imagem_por_cima==1 ? 'checked="checked"' : '')?> value="1" onchange="if(this.checked==true){$('#elem-wrapper').css('visibility','visible');}else{$('#elem-wrapper').css('visibility','hidden');}">
	<input type="submit" value="Gravar" /><button class="reset">Reset</button>
	</form>

</div>

<?php }?>

</body>
</html>
<?php }?>