<?
global $ID_Coverage;
?>				
	<style>
    #settings {padding:5px 20px;font-family:Arial;font-size:11px;border-top:#000 1px solid;border-bottom:#000 1px solid;background-color:#000;color:#fff}
    td.tm{color:#000}
    </style>
    <style type="text/css">
    .fd-zone10 {position: relative;overflow: hidden;width: 15em;margin: 0 auto;text-align: center}
    .fd-file {width:100px;opacity: 0;font-size: 118px;position:absolute;right: 0;top: 0;z-index: 1;padding: 0;margin: 0;cursor: pointer;filter: alpha(opacity=0);font-family: sans-serif}
    .fd-zone:hover{border:1px dashed red}
	
	#zone10{min-width: 90%; min-height: 100px;padding: 10px; border: 1px dotted #ccc;text-align: center; position: relative;background-color:#F0FAFF}
    #zone10:before{content: "Arraste as imagens para aqui!";color:#CCC;font-size:25px;position:absolute;width:100%;left:0;top:50%;margin:-25px 0 0;z-index:1;}
    #zone10:hover{border:1px dashed red}
	
    </style>
    <script type="text/javascript" src="filedrop.js"></script>    
    <table width=100% border=0 cellspacing=0 cellpadding=0><tr><td>
        <table width="100%" border=0 cellspacing=0 cellpadding=0>
        <tr><td colspan=4>
        
        <noscript style="color: maroon"><h2>JavaScript is disabled in your browser. How do you expect FileDrop to work?</h2></noscript>
        <div id="zone10" style="border: 1px solid #CCC">
            <p class="legend"></p>
            <p class="progress">
        		<span id="bar_zone10"></span>
        	</p>
        </div>
        
     
	<script type="text/javascript">
	  var options = {iframe: {url: '<?=urlencode('easycreate_process.php?ID='.$ID.'&up=true')?>'}};
      var zone = new FileDrop('zone10', options);
	  var t_files=0;
	  var n_file=0;
      zone.event('send', function (files)
	  {
		  files.images().each(function (file)
		  {
				file.readData(
				  	function (uri)
					{
						var img=new Image;
						img.src=uri;
						img.width=150;
						img.height=150;
						t_files++;
						zone.el.appendChild(img); 
					},
					function (error){alert('ERRO! Não é possivel ler a imagem.');},
				'uri')
				file.event('done', function (xhr)
				{
					t_files--;
					if(t_files<1)
					{
						alert("Todos os ficheiro foram inseridos/actualizados com sucesso");
						window.open('pagina.php?ID=<?=$ID?>','_self');
					}
												
				});
				file.event('error', function (e, xhr){alert("ERRO!"+xhr.responseText);});
				file.sendTo('easycreate_process.php?ID=<?=$ID?>&up=true');
		  });
      });
      zone.event('iframeDone', function (xhr)
	  {
        alert('Terminado, envio feito via <iframe>:\n\n' + xhr.responseText);
      });
      fd.addEvent(fd.byID('multiple'), 'change', function (e)
	  {
        zone.multiple(e.currentTarget || e.srcElement.checked);
      });
    </script>
    </td></tr>
    </table>
</td></tr></table>