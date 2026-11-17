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




?><!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>Transform Tool DOM + SVG</title>
	<style type="text/css">
		#stage { height:<?php echo $height?>px;	width:<?php echo ($width+150)?>px; position:relative; overflow: hidden; top:0; left:0 }
		#dom {background-image:url(<?php echo $_GET['image']?>); background-repeat:no-repeat; height:<?php echo $height?>px;	width:<?php echo ($width+150)?>px; position:absolute; overflow: hidden; top:0; left:0 }
		#dom img, #dom div, #dom svg { display:block; position:absolute; }
		#text-box { background-color:#8C9; color:#000; width:100px; height:75px; padding:0.5em; overflow:hidden; }
		#svg-tool {width:100%; height:100%; }
		#dunny{visibility:<?php echo ($imagem_por_cima==1 ? 'visible' : 'hidden'); ?>;border:#000 1px solid}

	</style>
</head>
<body onLoad="App.create();">

	<div id="stage">
	<img src="<?php echo $image_type?>" alt="" id="elem-wrapper" /> <?php /* style="transform-origin: 0px 0px 0px; z-index: 0; transform: matrix(<?php echo $skew;?>);" */?>
	<div id="dom">
		<img alt="" id="dunny" src="<?php echo $image_type?>" style="transform-origin: 0px 0px 0px; z-index: 0; transform: matrix(<?php echo $skew;?>);" />

		<svg id="svg-tool" xmlns="http://www.w3.org/2000/svg"></svg>
	</div>
	</div>

<?php/* 	<button onClick="App.instance.tool.setControls(ControlSet.getStandard()); App.instance.render();">Standard</button>
	<button onClick="App.instance.tool.setControls(ControlSet.getScaler()); App.instance.render();">Scaler</button>
	<button onClick="App.instance.tool.setControls(ControlSet.getScalerWithRotate()); App.instance.render();">Scaler With Rotate</button>
	<button onClick="App.instance.tool.setControls(ControlSet.getUniformScaler()); App.instance.render();">Uniform Scaler</button>
	<button onClick="App.instance.tool.setControls(ControlSet.getDynamic()); App.instance.render();">Dynamic</button>
	<button onClick="App.instance.tool.setControls(App.instance.getCustomControls()); App.instance.render();">Custom</button> */?>

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

	<script type="text/javascript" src="src/js/jquery.js"></script>
	<script type="text/javascript" src="Utils.js"></script>
	<script type="text/javascript" src="src/js/TransformTool.js"></script>
	<script type="text/javascript" src="src/js/DOMTransformTool.js"></script>
	<script type="text/javascript" src="TransformDOMPictures.js"></script>


</body>
</html>
<?php }?>