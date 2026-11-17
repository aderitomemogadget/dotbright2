<?php
    require_once('settings.inc.php');
    require_once('../../layout/header.inc.php');
    require_once('../../layout/body.inc.php');
?>
<table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td valign="top" align="left"><?php require_once('../../layout/menus.inc.php'); ?>
            <table class="p1" width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr><td style="color:#FFF" align="center"><h3>Atualizador de produtos</h3></td></tr>
            </table>

                <table width="100%" border="0" cellpadding="0" cellspacing="0"  style="background-color:#DDD">


					<tr>

                        <td align="left" valign="top" style="padding:10px" colspan="2">
						<div id="ficheiro_imp" style="font-size:13px;overflow:hidden;overflow-x:auto;height:auto;max-width:1080px"></div>
							<br><br><div id="carregar" style="display:none"><input type="button" name="atualizar" id="atualizar" value="3 - Prosseguir com a atualização do site" onclick="callfile();return false;" class="btn btn-primary" /></div>
							<div id="carregar2" style="display:none;background:#FFF"></div>
						</td>
                    </tr>

                </table>


            <table class="p1" width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr><td style="color:#FFF" align="center">&nbsp;</td></tr>
            </table>
        </td>

    </tr>
</table>

<script>
function callfile()
{
	$.ajax({type: 'GET',url: "run_webservice.php",dataType: 'html',cache: false, success: function(id){}});
	document.getElementById('carregar').style.display="none";
	document.getElementById('carregar2').style.display="block";
	window.scrollTo(0,document.body.scrollHeight);
}
function remove2()
{
	if(confirm('Tem a certeza que quer cancelar a importação, os produtos que já foram atualizados não serão repostos?'))
	{
		$.ajax({type: 'GET',url: "remove.php?remove2=1", dataType: 'html', cache: false, success: function(id){}});
	}
}
$( document).ready(function()
{
	$.ajax({type: 'GET',url: "verifica.php",dataType: 'html',cache: false, success: function(id2){ idx = id2.split('[split]'); id = idx[0];  if(id=='-1'){document.getElementById('carregar').style.display="block";document.getElementById('carregar2').style.display="none";document.getElementById('ficheiro_imp').innerHTML = idx[1];}else if(id==0){document.getElementById('carregar').style.display="none";document.getElementById('carregar2').style.display="none";}else{ document.getElementById('carregar').style.display="none"; document.getElementById('carregar2').style.display="block"; document.getElementById('carregar2').innerHTML = id;}document.getElementById('ficheiro_imp').innerHTML = idx[1];}});
	var tt = setInterval(function()
	{
		$.ajax({type: 'GET',url: "verifica.php",dataType: 'html',cache: false, success: function(id2){ idx = id2.split('[split]'); id = idx[0];  if(id=='-1'){document.getElementById('carregar').style.display="block";document.getElementById('carregar2').style.display="none";document.getElementById('ficheiro_imp').innerHTML = idx[1];}else if(id==0){document.getElementById('carregar').style.display="none";document.getElementById('carregar2').style.display="none";}else{document.getElementById('carregar').style.display="none"; document.getElementById('carregar2').style.display="block"; document.getElementById('carregar2').innerHTML = id;} document.getElementById('ficheiro_imp').innerHTML = idx[1];window.scrollTo(0,document.body.scrollHeight);}});

	},
	3000);
});

</script>
<?php require_once('../../layout/bottom.inc.php');