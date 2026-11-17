<?php
    require_once('settings.inc.php');
    require_once('../../layout/header.inc.php');
    require_once('../../layout/body.inc.php');
?>
<style type="text/css" media="screen">{    
 table, th, td {
  border: 1px solid #707478;
    }
}
</style>

<table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td valign="top" align="left"><?php require_once('../../layout/menus.inc.php'); ?>
            <table class="p1" width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr><td style="color:#FFF" align="center"><h3>Importa&ccedil;&atilde;o de produtos</h3></td></tr>
            </table>
            <form name="form1" method="post" action="utils/import_images.php" enctype="multipart/form-data">
                <table width="100%" border="0" cellpadding="0" cellspacing="0"  style="background-color:#FFF">
    				<tr><td style="background-color:#DDD" colspan="2" height="25" align="center"><b></b></td></tr>
                    <tr> 
                        <td colspan="2" style="padding:0 10px;background-color:#DDD" align="center">
                            
                           

                        </td>
                    </tr>
                    <tr>
                        <td  align="left" width="120" style="padding:10px">Ficheiro <b></b>:</td>
                        <td align="left" valign="top" style="padding:10px">
                            <input type="file" name="import_img[]" id="import_img" multiple="true">
                            
                            &nbsp;
                            <input type="submit" class="botao" name="img_files" value="Importar" />
                        </td>
                    </tr>  
                    <?php if(count(glob(dirname(__FILE__) . '/../../files/importar/*.csv')) > 0){ ?>
                    <tr>
                        <td  align="left" width="120" style="padding:10px;vertical-align:top" >Ficheiros para importar:</td>
                        <td align="left" valign="top" style="padding:10px">
                        <div id="ficheiro_imp">
                        <?php 
                        $tem_ficheiros = 0;
                        foreach (glob(dirname(__FILE__) . '/../../files/importar/*.csv') as $filename)
                        {
                            $ext = pathinfo($filename, PATHINFO_EXTENSION);
                            $file = explode('_', str_replace(dirname(__FILE__) . '/../../files/importar_admin/', '', $filename));
                            echo $file[0].'.'.$ext.'<br>';
                            $tem_ficheiros = 1;
                        }
                        ?></div><?php
                        
                        if($tem_ficheiros == 1)
                        {
                            ?>
                            <br><br><div id="carregar"><input type="button" name="atualizar" id="atualizar" value="Atualizar Base Dados Manual" onclick="callfile();return false;" class="botao" /></div>
                            <div id="carregar2" style="display:none;background:#FFF"></div>
                            
                            <div id="carregar2" style="display:none"><div></div><img src="loading.gif" border="0" style="vertical-align: middle;" /> a importar...<br><br><a href="#" onclick="cancelfile();return false;">Cancelar</a><br><br></div></b>
                        <?php 
                        }
                        /* echo '<br>';
                        echo 'a importar: ';
                        $file_atualizar = dirname(__FILE__) . '/../../files/importar/atualizar.txt';
                        if(file_exists($file_atualizar))
                        { 
                            $line = fgets(fopen($file_atualizar, 'r'));
                            $ext = pathinfo($line, PATHINFO_EXTENSION);
                            $file = explode('_', str_replace(SITE_SHELL_URL . 'files/importar/', '', $line));
                            // echo SITE_SHELL_URL . 'files/importar/<br>';
                            // echo '<b>'.$file[0].'.'.$ext.'</b> <img src="img/320.gif" border=""><br>';
                            
                            
                        } */
                        
                        ?>
                        </td>
                    </tr>
                    <?php }?>
                </table>
            </form>
            <table class="p1" width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr><td style="color:#FFF" align="center">&nbsp;</td></tr>
            </table>
        </td>
    </tr>
</table>
<?php
?>
<script>
$( document).ready(function()
{
	// $.ajax({type: 'GET',url: "verifica.php",dataType: 'html',cache: false, success: function(id){if(id=='-1'){document.getElementById('carregar').style.display="block";document.getElementById('carregar2').style.display="none";}else if(id==0){document.getElementById('carregar').style.display="none";document.getElementById('carregar2').style.display="none";}else{ if(Number(id) > Number($('#mypercold').val(id))){ $('#mypercold').val(Number(id));};document.getElementById('carregar').style.display="none"; document.getElementById('carregar2').style.display="block";}}	});
	$.ajax({type: 'GET',url: "verifica.php",dataType: 'html',cache: false, success: function(id){if(id=='-1'){document.getElementById('carregar').style.display="block";document.getElementById('carregar2').style.display="none";}else if(id==0){document.getElementById('carregar').style.display="none";document.getElementById('carregar2').style.display="none";}else{ if(id == 'ON'){};document.getElementById('carregar').style.display="none"; document.getElementById('carregar2').style.display="block";}}	});
	var tt = setInterval(function()
	{
		// $.ajax({type: 'GET',url: "verifica.php",dataType: 'html',cache: false, success: function(id){if(id=='-1'){document.getElementById('carregar').style.display="block";document.getElementById('carregar2').style.display="none";}else if(id==0){document.getElementById('carregar').style.display="none";document.getElementById('carregar2').style.display="none";}else{ if(Number(id) > Number($('#mypercold').val(id))){ $('#mypercold').val(Number(id));};document.getElementById('carregar').style.display="none"; document.getElementById('carregar2').style.display="block";}}	});
		$.ajax({type: 'GET',url: "verifica.php",dataType: 'html',cache: false, success: function(id){if(id=='-1'){document.getElementById('carregar').style.display="block";document.getElementById('carregar2').style.display="none";}else if(id==0){document.getElementById('carregar').style.display="none";document.getElementById('carregar2').style.display="none";}else{if(id == 'ON'){};document.getElementById('carregar').style.display="none"; document.getElementById('carregar2').style.display="block";}}	});
	},
	2000);
});
function callfile()
{
	$.ajax({type: 'GET',url: "utils/run.php",dataType: 'html',cache: false, success: function(id){}});
	document.getElementById('carregar').style.display="none";
	document.getElementById('carregar2').style.display="block";
}
function cancelfile()
{
	$.ajax({type: 'GET',url: "utils/cancel.php",dataType: 'html',cache: false, success: function(id){}});
	// document.getElementById('carregar').style.display="none";
	// document.getElementById('carregar2').style.display="block";
}
</script>
<?php require_once('../../layout/bottom.inc.php');?>