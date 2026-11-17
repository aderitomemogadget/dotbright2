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
function getStringBetween($str,$from,$to)
{
    $sub = substr($str, strpos($str,$from)+strlen($from),strlen($str));
    return substr($sub,0,strpos($sub,$to));
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
		if(substr_count($_GET['skew'], '('))	$skew =  getStringBetween($_GET['skew'],'(',')'); //preg_replace("/[^0-9,.]/", '', $_GET['skew']);
		else $skew = $_GET['skew'];
		$skew = preg_replace("/[^0-9,.]/", '', $skew);

		$sql = '
				UPDATE
					frames_save
				SET
					skew="'.$skew.'", pos_top="'.$_GET['top'].'", pos_left="'.$_GET['left'].'", width="'.$_GET['width'].'", height="'.$_GET['height'].'", anglo="'.$_GET['anglo'].'", imagem_por_cima="'.$_GET['imagem_por_cima'].'",
					data_transform_scalex="'.$_GET['data-transform-scalex'].'",
					data_transform_scaley="'.$_GET['data-transform-scaley'].'",
					data_transform_skewx="'.$_GET['data-transform-skewx'].'",
					data_transform_skewy="'.$_GET['data-transform-skewy'].'",
					data_transform_rotate="'.$_GET['data-transform-rotate'].'",
					data_transform="'.$_GET['data-transform'].'"
				WHERE
					save_id='.$_GET['id'];

		$res = Runquery($db_link, $DB_name, $sql);

		echo '<script>window.open("rotate.php?image='.$_GET['image'].'&id='.$_GET['id'].'&id_pai='.$_GET['id_pai'].'","_self");</script>';
	}


	$sql = '
			SELECT
				pos_top, pos_left, width, height, anglo, skew, imagem_por_cima,
				data_transform_scalex, data_transform_scaley,data_transform_skewx,data_transform_skewy,data_transform_rotate,data_transform
				FROM frames_save WHERE save_id="'.intval($_GET['id']).'"';
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		if($rs->db_data['pos_top']!=0) $pos_top = $rs->db_data['pos_top'];
		if($rs->db_data['pos_left']!=0) $pos_left = $rs->db_data['pos_left'];
		if($rs->db_data['width']!=0) $pos_width = $rs->db_data['width'];
		if($rs->db_data['height']!=0) $pos_height = $rs->db_data['height'];
		if($rs->db_data['anglo']!=0) $anglo = $rs->db_data['anglo'];
		if($rs->db_data['skew']!='') $skew = $rs->db_data['skew'];

		if($rs->db_data['data_transform_scalex']!='') $data_transform_scalex = $rs->db_data['data_transform_scalex'];
		if($rs->db_data['data_transform_scaley']!='') $data_transform_scaley = $rs->db_data['data_transform_scaley'];
		if($rs->db_data['data_transform_skewx']!='') $data_transform_skewx = $rs->db_data['data_transform_skewx'];
		if($rs->db_data['data_transform_skewy']!='') $data_transform_skewy = $rs->db_data['data_transform_skewy'];
		if($rs->db_data['data_transform_rotate']!='') $data_transform_rotate = $rs->db_data['data_transform_rotate'];
		if($rs->db_data['data_transform']!='') $data_transform = $rs->db_data['data_transform'];

		$imagem_por_cima = $rs->db_data['imagem_por_cima'];
	}
	$rs->db_close();

?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt" lang="pt">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Memogadget Plugin</title>

<script type="text/javascript" src="http://code.jquery.com/jquery-1.5.2.js"></script>
<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/themes/base/jquery-ui.css">
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/ui/jquery.ui.transform.js"></script>
<script type="text/javascript" src="js/ui/jquery.ui.transformable.js"></script>

<style>
	body{font-family:arial}
	#preview-zone
	{
		background-repeat: no-repeat;
		border:    none;
		height:     <?php echo $height?>px;
		/* overflow:   hidden; */
		width:      <?php echo $width?>px;
		margin :0 auto;
	/* 	position:relative   */
	}

	#draggable-zone
	{
		background-image:url(<?php echo $_GET['image']?>);
		border:    none;
		height:     <?php echo $height?>px;
		margin:     0 auto;
		/* overflow:   hidden; */
		width:      <?php echo $width?>px;
		 position:absolute;
		top:0;
		left:0
	}

	.ui-icon { background-color: #fff;}
	#elem-wrapper{visibility:<?php echo ($imagem_por_cima==1 ? 'visible' : 'hidden'); ?>}
	#elem-final{position:absolute}

	.box {position: absolute; width: <?php echo $width?>px; border: 1px black solid; z-index:10; left: 0px;}
	.box1 {background:none;}

	.box.ui-resizable-disabled { opacity: 1.0; filter: Alpha(Opacity=100);}
	.infobox {position: absolute; width:400px; height: 175px; border: 1px green solid; font-size: 10px}
	.infobox2 {position: absolute; width:400px; height: 175px; border: 1px green solid; font-size: 10px}
	li{ list-style-type: bold; padding: 0; margin-left: 36px;}
	button {position: relative; left: 20px;}
</style>

</head>
<body>





<?php/* 	<div id="preview-zone">
	<img src="<?php echo $image_type?>" style="width: <?php echo $pos_width?>px; left: <?php echo $pos_left?>px; top: <?php echo $pos_top?>px;" alt="" id="elem-final" />
		<div id="draggable-zone">
			<div class="box box1" id="draggable-wrapper" style="width: <?php echo $pos_width?>px;  left: <?php echo $pos_left?>px; top: <?php echo $pos_top?>px;">
				<div id="resizable-wrapper">
					<img src="<?php echo $image_type?>" style="width: <?php echo $pos_width?>px; " alt="" id="elem-wrapper" />
				</div>
			</div>
		</div>
	</div> */?>
	<div id="preview-zone">
	<img src="<?php echo $image_type?>" style="width: <?php echo $pos_width?>px; left: <?php echo $pos_left?>px; top: <?php echo $pos_top?>px;" alt="" id="elem-final" />
	<div id="draggable-zone">
	<div class='box box1' style="width: <?php echo $pos_width?>px; left: <?php echo $pos_left?>px; top: <?php echo $pos_top?>px;">

	<img src="<?php echo $image_type?>" style="width: <?php echo $pos_width?>px; " alt="" id="elem-wrapper" />
	</div>
	</div>
	</div>
	<div class='infobox' style="display:none">Div Transformation Info:</div>

	<div class="zone">

	<?php if($image_type != ''){?>
	<form method="get" id="form_tipo2">
	<input type="hidden" name="gravar" value="1">
	<input type="hidden" name="image" value="<?php echo $_GET['image']?>">
	<input type="hidden" name="id" value="<?php echo $_GET['id']?>">
	<input type="hidden" name="id_pai" value="<?php echo $_GET['id_pai']?>">
	<input type="hidden" name="top" id="top" value="<?php echo $pos_top?>">	<input type="hidden" name="left" id="left" value="<?php echo $pos_left?>">
	<input type="hidden" name="width" id="width" value="<?php echo $pos_width?>"> <input type="hidden" name="height" id="height" value="<?php echo $pos_height?>">
	<input type="hidden" name="anglo" id="anglo" value="<?php echo $anglo?>">
	<input type="hidden" name="skew" id="skew" value="<?php echo $skew?>">
	<input type="hidden" name="data-transform-scalex" id="data-transform-scalex" value="<?php echo $data_transform_scalex?>">
	<input type="hidden" name="data-transform-scaley" id="data-transform-scaley" value="<?php echo $data_transform_scaley?>">
	<input type="hidden" name="data-transform-skewx" id="data-transform-skewx" value="<?php echo $data_transform_skewx?>">
	<input type="hidden" name="data-transform-skewy" id="data-transform-skewy" value="<?php echo $data_transform_skewy?>">
	<input type="hidden" name="data-transform-rotate" id="data-transform-rotate" value="<?php echo $data_transform_rotate?>">
	<input type="hidden" name="data-transform" id="data-transform" value="<?php echo $data_transform?>">
	Imagem por cima: <input type="checkbox" name="imagem_por_cima" id="imagem_por_cima" <?php echo ($imagem_por_cima==1 ? 'checked="checked"' : '')?> value="1" onchange="if(this.checked==true){$('#elem-wrapper').css('visibility','visible');}else{$('#elem-wrapper').css('visibility','hidden');}">
	<input type="submit" value="Gravar" />
	</form>

	</div>

	<?php }?>



	<script type="text/javascript">
function str_replace_fl(search, replace, subject, count) {

  var i = 0,
    j = 0,
    temp = '',
    repl = '',
    sl = 0,
    fl = 0,
    f = [].concat(search),
    r = [].concat(replace),
    s = subject,
    ra = Object.prototype.toString.call(r) === '[object Array]',
    sa = Object.prototype.toString.call(s) === '[object Array]';
  s = [].concat(s);
  if (count) {
    this.window[count] = 0;
  }

  for (i = 0, sl = s.length; i < sl; i++) {
    if (s[i] === '') {
      continue;
    }
    for (j = 0, fl = f.length; j < fl; j++) {
      temp = s[i] + '';
      repl = ra ? (r[j] !== undefined ? r[j] : '') : r[0];
      s[i] = (temp)
        .split(f[j])
        .join(repl);
      if (count && s[i] !== temp) {
        this.window[count] += (temp.length - s[i].length) / f[j].length;
      }
    }
  }
  return sa ? s : s[0];
}
	$(document).ready(function()
	{
		var b=$('.box');
		var ib=$('.infobox');
		b.draggable({stop: draggableInfo});
		b.resizable({aspectRatio: true,stop: resizableInfo});

		b.setTransform('skewy','<?php echo ($data_transform_skewy!='' ? $data_transform_skewy : 0);?>');
		b.setTransform('skewx','<?php echo ($data_transform_skewx!='' ? $data_transform_skewx : 0);?>');
		b.setTransform('scalex','<?php echo ($data_transform_scalex!='' ? $data_transform_scalex : 0);?>');
		b.setTransform('scaley','<?php echo ($data_transform_scaley!='' ? $data_transform_scaley : 0);?>');
		b.setTransform('rotate','<?php echo ($data_transform_rotate!='' ? $data_transform_rotate : 0);?>');
		b.transformable({rotateStop: rotateInfo, skewStop: skewInfo, /* scaleStop: resizableInfo, */ rotate: showrotate/* , scale: constrainscale */});

		function constrainscale(e,ui)
		{
		}

		function showrotate(e,ui)
		{

		}


		function skewInfo (e,ui)
		{
			var u=$(this).getTransform();


			var o=$(this).tOffset();
			function xytostr(c)
			{
				return(parseInt(c.x)+','+parseInt(c.y));
			}


			$('#elem-final').css("transform", $('.box').attr('data-transform'));
			$('#elem-final').css("-moz-transform", $('.box').attr('data-transform'));
			$('#elem-final').css("-webkit-transform", $('.box').attr('data-transform'));
			$('#elem-final').css("-o-transform", $('.box').attr('data-transform'));

			$('#skew').val($('.box').attr('data-transform'));


			if($('.istransformed').length > 0)
			{


				$('#data-transform-scalex').val($('.istransformed').attr('data-transform-scalex'));
				$('#data-transform-scaley').val($('.istransformed').attr('data-transform-scaley'));
				$('#data-transform-skewx').val($('.istransformed').attr('data-transform-skewx'));
				$('#data-transform-skewy').val($('.istransformed').attr('data-transform-skewy'));
				$('#data-transform-rotate').val($('.istransformed').attr('data-transform-rotate'));
				$('#data-transform').val($('.istransformed').attr('data-transform'));
			}

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

			if($('.istransformed').length > 0)
			{
				$('#data-transform-scalex').val($('.istransformed').attr('data-transform-scalex'));
				$('#data-transform-scaley').val($('.istransformed').attr('data-transform-scaley'));
				$('#data-transform-skewx').val($('.istransformed').attr('data-transform-skewx'));
				$('#data-transform-skewy').val($('.istransformed').attr('data-transform-skewy'));
				$('#data-transform-rotate').val($('.istransformed').attr('data-transform-rotate'));
				$('#data-transform').val($('.istransformed').attr('data-transform'));
			}
		}

	function draggableInfo(e,ui)
	{
        var o = $(this).tOffset();

		$('#left').val(o.left);
		$('#top').val(o.top);

		$('#elem-final').css({'top' : o.top+'px ','left' : o.left+'px', 'width' :  $('.box1').width()+'px ','height' : $('.box1').height()+'px'});

		if($('.istransformed').length > 0)
		{
			$('#data-transform-scalex').val($('.istransformed').attr('data-transform-scalex'));
			$('#data-transform-scaley').val($('.istransformed').attr('data-transform-scaley'));
			$('#data-transform-skewx').val($('.istransformed').attr('data-transform-skewx'));
			$('#data-transform-skewy').val($('.istransformed').attr('data-transform-skewy'));
			$('#data-transform-rotate').val($('.istransformed').attr('data-transform-rotate'));
			$('#data-transform').val($('.istransformed').attr('data-transform'));
		}

    }
	function resizableInfo (e,ui)
	{
		image_w = ui.size.width;
		image_h = ui.size.height;

		$('#width').val(image_w);
		$('#height').val(image_h);

		$('#elem-final').css({'width' : image_w+'px ','height' : image_h+'px'});

		if($('.istransformed').length > 0)
		{
			$('#data-transform-scalex').val($('.istransformed').attr('data-transform-scalex'));
			$('#data-transform-scaley').val($('.istransformed').attr('data-transform-scaley'));
			$('#data-transform-skewx').val($('.istransformed').attr('data-transform-skewx'));
			$('#data-transform-skewy').val($('.istransformed').attr('data-transform-skewy'));
			$('#data-transform-rotate').val($('.istransformed').attr('data-transform-rotate'));
			$('#data-transform').val($('.istransformed').attr('data-transform'));
		}
    }


	<?php if($skew!=''){?>
	$('#elem-final').css({"transform":'<?php echo $data_transform;?>', "-moz-transform":'<?php echo $data_transform;?>', "-webkit-transform":'<?php echo $data_transform;?>', "-o-transform":'<?php echo $data_transform;?>'});

		/* $('.box').addClass('istransformed');
		$('.box').attr('data-transform-scalex', '<?php echo $data_transform_scalex?>');
		$('.box').attr('data-transform-scaley', '<?php echo $data_transform_scaley?>');
		$('.box').attr('data-transform-skewx', '<?php echo $data_transform_skewx?>');
		$('.box').attr('data-transform-skewy', '<?php echo $data_transform_skewy?>');
		$('.box').attr('data-transform-rotate', '<?php echo $data_transform_rotate?>');
		$('.box').attr('data-transform', '<?php echo $data_transform?>'); */
		// $('#elem-final').attr('data-transform', '<?php echo $data_transform?>');

		/* var obj = $('#elem-final');
		obj.css({'-moz-transform':'matrix(<?php echo $skew;?>)','-webkit-transform':'matrix(<?php echo $skew;?>)','-o-transform':'matrix(<?php echo $skew;?>)','-ms-transform':'matrix(<?php echo $skew;?>)','transform':'matrix(<?php echo $skew;?>)'});
 */

	<?php } ?>


	// class="box box1 ui-draggable ui-resizable " transformable-opts="" style="width: 269.502px; left: 85.6667px; top: 50px; transform: matrix(1, 0, -0.0596789, 1, 0px, 0px);" data-transform-scalex="1" data-transform-scaley="1" data-transform-skewx="-0.05960823591421893" data-transform-skewy="0" data-transform-rotate="0" data-transform="matrix(1.0000000000000000,0.0000000000000000,-0.0596789352357995,1.0000000000000000,0.0000000000000000,0.0000000000000000)"


	});
	</script>

</body>
</html>
<?php }?>