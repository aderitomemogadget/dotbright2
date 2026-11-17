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
	
	$image_type = ''; if($bloco_ext && file_exists('../../../files/frames/'.$bloco_ext)) $image_type = '../../../files/frames/'.$bloco_ext;
	
	$pos_top = 150; $pos_left = 150; $pos_width = 150; $pos_height = 150; $anglo = 0;
	
 	
	
	@list($width, $height) = @getimagesize($_GET['image']);

	// ESCOLHA SELECTBOX
	if(isset($image_type) && $image_type!='')
	{ 
		@list($width_prev, $height_prev) = @getimagesize($image_type);
		$width_prev2 = 150;
		$height_prev = (($width_prev2 * $height_prev) / $width_prev);
	}
	
	// echo $width_prev;
	
	
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
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="en">
    <head>
        <title>memogadget - interactive image manipulation</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="description" content="memogadget - interactive image manipulation" />
        <meta name="keywords" content="jquery, php, fancy, css3, rotation" />
        <link type="text/css" href="css/jquery.ui.theme.css" rel="stylesheet" />
        <link type="text/css" href="css/jquery.ui.core.css" rel="stylesheet" />
        <link type="text/css" href="css/jquery.ui.resizable.css" rel="stylesheet" />
        <link type="text/css" href="css/jquery.ui.slider.css" rel="stylesheet" />
        <link type="text/css" href="css/style.css" rel="stylesheet" />
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js"></script>
        <script type="text/javascript" src="json2.js"></script>
		
		<style>
		#content {
			height:<?php echo ($height+30)?>px;
		}
		.background
		{
			width:<?php echo ($width)?>px;
			height:<?php echo ($height)?>px;
			z-index:999
		}
		#obj_0
		{
			z-index:999;
			position:absolute;
		}
		#objects{
			
			z-index:9999
		}
		#obj_1_x{
			
			position:absolute;
			z-index: 99;
			visibility:hidden
		}
		</style>
    </head>
    <body>
        <div id="content">
            <div id="background" class="background">
				<img id="obj_1_x" width="<?php echo $width_prev2?>" height="<?php echo $height_prev?>"  class="ui-widget-content" src="<?php echo $image_type?>" alt="el"/>
                <img id="obj_0" width="<?php echo $width?>" height="<?php echo $height?>" src="<?php echo $_GET['image']?>" />
				
                
            </div>
            <div id="tools" style="font-size:12px">
			Ver imagem por cima:&nbsp;<input type="checkbox" name="imagem_por_cima" id="imagem_por_cima" <?php echo ($imagem_por_cima==1 ? 'checked="checked"' : '')?> value="1" onchange="if(this.checked==true){$('#obj_1_x').css('z-index','9999');}else{$('#obj_1_x').css('z-index','99');}"><br>
            </div>
            
			<div id="objects" style="font-size:12px">
			Arraste a imagem:<br>
                <div class="obj_item"><img id="obj_1" width="<?php echo $width_prev2?>" height="<?php echo $height_prev?>"  class="ui-widget-content" src="<?php echo $image_type;?>" alt="el"/></div>

            </div>
            <a id="submit"><span></span></a>
			<form id="jsonform" action="merge.php" method="POST">
				<input id="jsondata" name="jsondata" type="hidden" value="" autocomplete="off"></input>
			</form>
        </div>
        <script>
            $(document).ready(function(id) 
			{
		
				var count_dropped_hits = 0;
				var data = {
                    "images": [
                        {"id" : "obj_0" ,"src" : "<?php echo $_GET['image']?>"	,"width" : "<?php echo $width?>", "height" : "<?php echo $height?>"}
                    ]
                };
				// Array Remove 
				Array.prototype.remove = function(from, to) 
				{
				  var rest = this.slice((to || from) + 1 || this.length);
				  this.length = from < 0 ? this.length + from : from;
				  return this.push.apply(this, rest);
				};
				
				/*  remove an object from data */
				$('.remove',$('#tools')).live('click',function()
				{
					var $this = $(this);
					
					/* the element next to this is the input that stores the obj id */
					var objid = $this.next().val();
					/* remove the object from the sidebar */
					$this.parent().remove();
					/* ,from the picture */
					var divwrapper = $('#'+objid).parent().parent();
					$('#'+objid).remove();
					/* add again to the objects list */
					var image_elem 		= $this.parent().find('img');
					var thumb_width 	= image_elem.attr('width');
					var thumb_height 	= image_elem.attr('height');
					var thumb_src 		= image_elem.attr('src');
					$('<img/>',{
						id 			: 	objid,
						src			: 	thumb_src,
						width		:	thumb_width, 
						//height		:	thumb_height,
						className	:	'ui-widget-content'
					}).appendTo(divwrapper).resizable({
						handles	: 'se',
						stop	: resizestop 
					}).parent('.ui-wrapper').draggable({
						revert: 'invalid'
					});
					/* and unregister it - delete from object data */
					var index = exist_object(objid);
					data.images.remove(index);
					
					
					
				});
                
                /*  checks if an object was already registered */
                function exist_object(id){
                    for(var i = 0;i<data.images.length;++i){
                        if(data.images[i].id == id)
                            return i;
                    }
                    return -1;
                }
				
				/* triggered when stop resizing an object */
				function resizestop(event, ui) {
					//calculate and change values to obj (width and height)
					var $this 		= $(this);
					var objid		= $this.find('.ui-widget-content').attr('id');
					var objwidth 	= ui.size.width;
					var objheight 	= ui.size.height;
				
					var index 		= exist_object(objid);
				
					if(index!=-1) { //if exists (it should!) update width and height
						data.images[index].width 	= objwidth;
						data.images[index].height 	= objheight;
					}
					
					atualiza();
                }
				/* objects are resizable and draggable */
                $('#objects img').resizable({
                    /* only diagonal (south east)*/
					aspectRatio: true,
                    handles	: 'se',
					stop	: resizestop 
                }).parent('.ui-wrapper').draggable({
                    revert	: 'invalid',
					
                });
				
                $('#background').droppable({
                    accept	: '#objects div', /* accept only draggables from #objects */
                    drop	: function(event, ui) 
					{
                       
						
						var $this 		= $(this);
                        ++count_dropped_hits;
						var draggable_elem = ui.draggable;
						draggable_elem.css('z-index',count_dropped_hits);
						/* object was dropped : register it */
                        var objsrc 		= draggable_elem.find('.ui-widget-content').attr('src');
                        var objwidth 	= parseFloat(draggable_elem.css('width'),10);
                        var objheight 	= parseFloat(draggable_elem.css('height'),10);
					
                        /* for top and left we decrease the top and left of the droppable element */
                        var objtop		= ui.offset.top - $this.offset().top;
                        var objleft		= ui.offset.left - $this.offset().left;
                                       
                        var objid		= draggable_elem.find('.ui-widget-content').attr('id');
					
                        var index 		= exist_object(objid);
						
                        if(index!=-1) { //if exists update top and left
                            data.images[index].top 	= objtop;
                            data.images[index].left = objleft;						
                        }
                        else
						{					
							/* register new one */
                            var newObject = { 
								'id' 		: objid,
                                'src' 		: objsrc,
                                'width' 	: objwidth,
                                'height' 	: objheight,
                                'top' 		: objtop,
                                'left' 		: objleft,
								'rotation'  : '0'
                            };
							data.images.push(newObject);
		
							$('<div/>',{
								className	:	'item'
							}).append(
								$('<div/>',{
									className	:	'thumb',
									html		:	'<img width="50" class="ui-widget-content" src="'+objsrc+'"></img>'
								})
							).append(
								$('<div/>',{
									className	:	'slider',
									html		:	'<span>Rotate</span><span class="degrees">0</span>'
								})
							).append(
								$('<a/>',{
									className	:	'remove'
								})
							).append(
								$('<input/>',{
									type		:	'hidden',
									value		:	objid		// keeps track of which object is associated
								})
							).appendTo($('#tools'));
							
							
							$('.slider').slider({
								orientation	: 'horizontal',
								max			: 180,
								min			: -180,
								value		: 0,
								slide		: function(event, ui) {
									var $this = $(this);
									/* Change the rotation and register that value in data object when it stops */
									draggable_elem.css({'-moz-transform':'rotate('+ui.value+'deg)','-webkit-transform':'rotate('+ui.value+'deg)'});
									$('.degrees',$this).html(ui.value);
									
									
								},
								stop		: function(event, ui) {
									newObject.rotation = ui.value;
									atualiza(ui.value);
								}
							});	
							
                        }
						
						atualiza();
			
						
						
                    }
                });
				
				
				function atualiza(rotate)
				{
						objwidth = data.images[1]['width'];
						objheight = data.images[1]['height'];
						objtop = data.images[1]['top'];
						objleft = data.images[1]['left'];
					
						$('#obj_1_x').css({'width':objwidth,'height':objheight,'left':(objleft),'top':objtop,'visibility':'visible'});
						
						if(rotate!=undefined) $('#obj_1_x').css({'-moz-transform':'rotate('+rotate+'deg)','-webkit-transform':'rotate('+rotate+'deg)'})
							
						if($('#obj_1').attr('src')!='images/cm_fill.gif') $('#obj_1').attr('src', 'images/cm_fill.gif');
					
				}
			
				/* User presses the download button */
                $('#submit').bind('click',function()
				{
                    var dataString  = JSON.stringify(data);
					
					// console.log(dataString);
                    $('#jsondata').val(dataString);
					$('#jsonform').submit();
                });
            });
        </script>
    </body>
</html>
<?php }?>