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
		$sql = 'UPDATE frames_save SET pos_top="'.$_GET['top'].'", pos_left="'.$_GET['left'].'", width="'.$_GET['width'].'", height="'.$_GET['height'].'", anglo="'.$_GET['anglo'].'", imagem_por_cima="'.$_GET['imagem_por_cima'].'" WHERE save_id='.$_GET['id'];
		$res = Runquery($db_link, $DB_name, $sql);

		echo '<script>window.open("rotate.php?image='.$_GET['image'].'&id='.$_GET['id'].'&id_pai='.$_GET['id_pai'].'","_self");</script>';
	}


	$sql = 'SELECT pos_top, pos_left, width, height, anglo, imagem_por_cima FROM frames_save WHERE save_id="'.intval($_GET['id']).'"';
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		if($rs->db_data['pos_top']!=0) $pos_top = $rs->db_data['pos_top'];
		if($rs->db_data['pos_left']!=0) $pos_left = $rs->db_data['pos_left'];
		if($rs->db_data['width']!=0) $pos_width = $rs->db_data['width'];
		if($rs->db_data['height']!=0) $pos_height = $rs->db_data['height'];
		if($rs->db_data['anglo']!=0) $anglo = $rs->db_data['anglo'];
		$imagem_por_cima = $rs->db_data['imagem_por_cima'];
	}
	$rs->db_close();




?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt" lang="pt">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Memogadget Plugin</title>

<link rel="stylesheet" type="text/css" href="css/jquery.ui.core.css" />
<link rel="stylesheet" type="text/css" href="css/jquery.ui.resizable.css" />

<style>
	body{font-family:arial}
	#preview-zone
	{
		background-repeat: no-repeat;
		border:    none;
		height:     <?php echo $height?>px;
		overflow:   hidden;
		width:      <?php echo $width?>px;
		margin :0 auto;
		position:relative
	}
	#draggable-zone
	{
		background-image:url(<?php echo $_GET['image']?>);
		border:    none;
		height:     <?php echo $height?>px;
		margin:     0 auto;
		overflow:   hidden;
		width:      <?php echo $width?>px;
		position:absolute;
		top:0;
		left:0
	}
	.ui-wrapper { overflow:visible !important; border:#000 1px solid }

	.ui-resizable-handle { background: #f5dc58; border: 1px solid #FFF;  z-index: 2;}
	.ui-rotatable-handle { background: #f5dc58; border: 1px solid #FFF; border-radius: 5px; -moz-border-radius: 5px; -o-border-radius: 5px; -webkit-border-radius: 5px; cursor: pointer;  height: 10px; left: 50%; margin: 0 0 0 -5px; position: absolute; top: -5px; width: 10px;}
	.ui-rotatable-handle.clone {visibility:  hidden;}
	.zone{text-align:center;background:#ddd;width:<?php echo $width?>px;margin: 0 auto}
	.zone select{margin: 0 auto}

	#elem-wrapper{visibility:<?php echo ($imagem_por_cima==1 ? 'visible' : 'hidden'); ?>}
	#elem-final{position:absolute}
</style>

</head>
<body>



	<div class="zone">

	<?php


	if($image_type != ''){?>
	<form method="get" id="form_tipo2">
	<input type="hidden" name="gravar" value="1">
	<input type="hidden" name="image" value="<?php echo $_GET['image']?>">
	<input type="hidden" name="id" value="<?php echo $_GET['id']?>">
	<input type="hidden" name="id_pai" value="<?php echo $_GET['id_pai']?>">
	<input type="hidden" name="top" id="top" value="<?php echo $pos_top?>">	<input type="hidden" name="left" id="left" value="<?php echo $pos_left?>">
	<input type="hidden" name="width" id="width" value="<?php echo $pos_width?>"> <input type="hidden" name="height" id="height" value="<?php echo $pos_height?>">
	<input type="hidden" name="anglo" id="anglo" value="<?php echo $anglo?>">
	Imagem por cima: <input type="checkbox" name="imagem_por_cima" id="imagem_por_cima" <?php echo ($imagem_por_cima==1 ? 'checked="checked"' : '')?> value="1" onchange="if(this.checked==true){$('#elem-wrapper').css('visibility','visible');}else{$('#elem-wrapper').css('visibility','hidden');}">
	<input type="submit" value="Gravar" />
	</form>


	</div>

	<div id="preview-zone">
	<img src="<?php echo $image_type?>" style="width: <?php echo $pos_width?>px; left: <?php echo $pos_left?>px; top: <?php echo $pos_top?>px;" alt="" id="elem-final" />
		<div id="draggable-zone">
			<div id="draggable-wrapper" style="width: <?php echo $pos_width?>px;  left: <?php echo $pos_left?>px; top: <?php echo $pos_top?>px;">
				<div id="resizable-wrapper">
					<img src="<?php echo $image_type?>" style="width: <?php echo $pos_width?>px; " alt="" id="elem-wrapper" />
				</div>
			</div>
		</div>
	</div>
	<?php }?>

	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/ui/jquery.ui.core.js"></script>
	<script type="text/javascript" src="js/ui/jquery.ui.widget.js"></script>
	<script type="text/javascript" src="js/ui/jquery.ui.mouse.js"></script>
	<script type="text/javascript" src="js/ui/jquery.ui.draggable.js"></script>
	<script type="text/javascript" src="js/ui/jquery.ui.resizable.js"></script>
	<script type="text/javascript" src="js/ui/jquery.ui.rotatable.js?dp=<?php echo date('Ymdhis')?>"></script>

	<script type="text/javascript">
	$(document).ready(function()
	{
		var drWr = $('#draggable-wrapper'), rsWr = $('#resizable-wrapper'), elem = $('#elem-wrapper');

		elem.resizable(
		{
			aspectRatio: true,
			handles:     'ne, nw, se, sw',
			stop: function( event, ui )
			{
				image_w = ui.size.width;
				image_h = ui.size.height;

				$('#width').val(image_w);
				$('#height').val(image_h);

				$('#elem-final').css({'width' : image_w+'px ','height' : image_h+'px'});
			}
		});

		drWr.draggable({
			stop: function( event, ui )
			{
				image_x = ui.position.left;
				image_y = ui.position.top;
				$('#left').val(image_x);
				$('#top').val(image_y);

				$('#elem-final').css({'top' : image_y+'px ','left' : image_x+'px', 'width' :  $('#elem-wrapper').width()+'px ','height' : $('#elem-wrapper').height()+'px'});
			}
		});

		drWr.rotatable({
		<?php if($anglo != 0) {?>angle: <?php echo $anglo?>,<?php }?>
		stop: function(event, ui)
		{
			anglo = ui.angle.stop;

			$('#elem-final').css("transform", "rotate(" + anglo + "rad)");
			$('#elem-final').css("-moz-transform", "rotate(" + anglo + "rad)");
			$('#elem-final').css("-webkit-transform", "rotate(" + anglo + "rad)");
			$('#elem-final').css("-o-transform", "rotate(" + anglo + "rad)");
			$('#anglo').val(anglo);
		}});


		<?php if($anglo != 0) {?>$('#elem-final').rotatable( {angle: <?php echo $anglo?>} );<?php }?>

	});
	</script>

</body>
</html>
<?php }?>