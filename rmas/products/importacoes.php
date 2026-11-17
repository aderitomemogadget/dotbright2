<?
	include "settings.inc.php";
	include "../layout/header.inc.php";	
	include "../layout/body.inc.php";
?>
<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
<tr>
    <td valign="top" align="left"><? 
        include "../layout/menus.inc.php";?>
    <script type="text/javascript" src="filedrop.js"></script>
    <style type="text/css">
    .fd-zone10 {position: relative;overflow: hidden;width: 15em;margin: 0 auto;text-align: center}
    .fd-file {opacity: 0;font-size: 118px;position: absolute;right: 0;top: 0;z-index: 1;padding: 0;margin: 0;cursor: pointer;filter: alpha(opacity=0);font-family: sans-serif}
    .fd-zone.over { border-color: maroon; background: #eee}
	
	#zone10{min-width: 90%; min-height: 100px;padding: 10px; border: 1px dotted #ccc;text-align: center; position: relative;background-color:#F0FAFF}
    </style>
</head>
                <body>
                <table width=100% border=0 cellspacing=0 cellpadding=0><tr><td>
                    <table width="100%" border=0 cellspacing=0 cellpadding=0>
                    
                    <tr><td colspan=4>
    <noscript style="color: maroon"><h2>JavaScript is disabled in your browser. How do you expect FileDrop to work?</h2></noscript>
    <div id="zone10" style="border: 1px solid #CCC">
      <p class="legend">
        Arraste as fotos para criar produtos ou para atualizar as fotos de produtos a partir das referencias
      </p>
    
      <!-- You can also use <progress> tag of HTML5: -->
      <p class="progress">
        <span id="bar_zone10"></span>
      </p>
    </div>
    <script type="text/javascript">
      var options = {iframe: {url: 'upload.php'}};
      var zone = new FileDrop('zone10', options);
	  var t_files=0;
	  var n_file=0;
      zone.event('send', function (files)
	  {
		  files.images().each(function (file)
		  {
				// Reset the progress when a new upload starts:
				file.event('sendXHR',function(){fd.byID('bar_zone10').style.width=0;})
			 	// Update progress when browser reports it:
			 	file.event('progress', function (current, total)
				{
					var width = current / total * 100 + '%';
					fd.byID('bar_zone10').style.width = width;
				})

				file.readData(
				  	function (uri)
					{
						var img=new Image;
						img.src=uri;
						img.width=150;
						img.height=150;
						t_files++;
						var h=document.createElement("H1")
 						var t=document.createTextNode('.');
						zone.el.appendChild(t);
						//zone.el.appendChild(img); 
					},
					function (error){alert('Ph, noes! Cannot read your image.');},
				'uri')
				
				file.event('done', function (xhr)
				{
					n_file++;
					var h=document.createElement("H1")
 					var t=document.createTextNode('( '+n_file+'/'+t_files+') '+file.name+" ---> Uploaded "+xhr.responseText);
					zone.el.appendChild(t);
					
					var br = document.createElement("br");
					zone.el.appendChild(br);zone.el.appendChild(br);
						
					//alert(xhr.responseText);
					//alert('Done uploading ' + this.name + ', response:\n\n' + xhr.responseText);
				});
				file.event('error', function (e, xhr) 
				{
					//alert(xhr.status + ', ' + xhr.statusText)
					
					var h=document.createElement("H1")
					var t=document.createTextNode(file.name+" ---> not Uploaded "+xhr.responseText);
					zone.el.appendChild(t);
					
					var br = document.createElement("br");
					zone.el.appendChild(br);zone.el.appendChild(br);
					
				});
				file.sendTo('upload.php');
		  
		  
		  });
			
			
			/*;




        files.each(function (file)
		{
          file.event('done', function (xhr)
		  {
            alert('Done uploading ' + this.name + ', response:\n\n' + xhr.responseText);
          });
		  file.event('error', function (e, xhr) 
		  {
			  alert(xhr.status + ', ' + xhr.statusText)
		  })
		  
		  
          file.sendTo('upload.php');
        });*/
      });
	  
      zone.event('iframeDone', function (xhr)
	  {
        alert('Done uploading via <iframe>, response:\n\n' + xhr.responseText);
      });
      fd.addEvent(fd.byID('multiple'), 'change', function (e)
	  {
        zone.multiple(e.currentTarget || e.srcElement.checked);
      });
    </script>

					</td></tr>
					</table>
				</td></tr></table>
</td>
    <td width="10px">&nbsp;</td>
    <td valign="top">&nbsp;</td>
</tr>
</table>
<? 	include "../layout/bottom.inc.php";?>