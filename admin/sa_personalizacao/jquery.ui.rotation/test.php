<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <meta name="robots" content="noindex, nofollow">
  <meta name="googlebot" content="noindex, nofollow">

  
  

  

<script type="text/javascript" src="http://code.jquery.com/jquery-1.5.2.js"></script>
<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/themes/base/jquery-ui.css">
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/jquery-ui.min.js"></script>
<script type="text/javascript" src="http://flin.org/js/jquery.transform.js"></script>
<script type="text/javascript" src="http://flin.org/js/jquery.transformable.js"></script>
    
  

<style type="text/css">
.box {position: absolute; width:300px; height: 300px; border: 1px black solid; top: 210px; z-index:10; left: 0px;}
.box1 {background-color: #d0d0d0;}

.box.ui-resizable-disabled { opacity: 1.0; filter: Alpha(Opacity=100);}
.infobox {position: absolute; width:400px; height: 175px; border: 1px green solid; font-size: 10px}
li{ list-style-type: bold; padding: 0; margin-left: 36px;} 
button {position: relative; left: 20px;}
</style>

<title>awesome resize skew rotate example by slashdottir</title>

  
    




<script type='text/javascript'>
$(window).load(function(){
$(function() {

    var b=$('.box');
    var ib=$('.infobox');
    b.draggable({stop: showinfo});
    b.resizable({stop: showinfo});
    b.transformable({rotateStop: showinfo, skewStop: showinfo, scaleStop: showinfo, rotate: showrotate, scale: constrainscale});
    
    //containment is still a bit expiramental
    // $('#box3').draggable('option','containment','parent')
        // .transformable('option','containment',true)
        // .resizable('option','containment','parent');
    
    function constrainscale(e,ui) {
        //uncomment to see how you can set value to constrain size
        //if (ui.scalex<0.5) ui.scalex=0.5;
        //if (ui.scaley<0.5) ui.scaley=0.5;
    }
    
    function showrotate(e,ui) {
        $('#rotate').html(ui.angle.rad);
        //uncomment these to see how you can set the value to constrain angle
        //if(ui.angle.rad>1)ui.angle.rad=1;
        //if(ui.angle.rad<-1) ui.angle.rad=-1;
    }
    
    function showinfo (e,ui) 
	{
        var u=$(this).getTransform();
        var o=$(this).tOffset();
        function xytostr(c) 
		{
            return(parseInt(c.x)+','+parseInt(c.y));
        }
        ib.html(
            'Div Transformation Info:<ul>Current Matrix:<li>'
            + $(this).matrixToArray()+'</li></ul><ul>Rotation:<li id="rotate">'
            +u.rotate+'</li></ul><ul>Skew:<li>'
            +u.skew+"</li></ul><ul>Scale:<li>"
            +u.scale+'</li></ul><ul>Offset:<li>'
            +'left:'+parseInt(o.left)+", top:"+parseInt(o.top)
            +', right:'+parseInt(o.right)+'bottom:'+parseInt(o.bottom)
            +'</li></ul>'
            +'<ul>Corners:<li>lt:'+xytostr(o.corners[0])
            +', rt:'+xytostr(o.corners[1])
            +', rb:'+xytostr(o.corners[2])
            +', lb:'+xytostr(o.corners[3])
        );
    }
    $('.reset').click(function() {
        $(this).parent().transformable('reset');
        showinfo.call($(this).parent());
    });
});
});

</script>

  
</head>

<body>
  
	<div class='box box1'>
	<button class='reset'>Reset Me</button>
	<img width=200 height=119 src="http://upload.wikimedia.org/wikipedia/commons/e/e9/Dog%27s_guardian_angel.jpg">  
	</div>

<div class='infobox'>Div Transformation Info:</div>
  
</body>

</html>

