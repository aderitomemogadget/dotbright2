<?php
	ini_set('max_execution_time','0');
	ini_set('memory_limit','1024M');

	if(isset($argv) && $argv[1] > 0)$Supplier_ID=intval($argv[1]);
	if(isset($_GET['Supplier_ID']))$Supplier_ID=intval($_GET['Supplier_ID']);

    require_once('settings.inc.php');
    require_once('../layout/header.inc.php');
    require_once('../layout/body.inc.php');

	// VAMOS ATUALIZAR TODAS AS FAMILIAS COM UM CÓDIGO ESPECIAL
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);


?>
<style>
	.botao{padding:0 5px}
	.container{width:100%;margin:0 auto}
	.container label{display:block;padding:5px 0}
	ul.tabs{margin:0px;padding:0px;list-style:none}
	ul.tabs li{background:none;color:#222;display:inline-block;padding:5px 10px;cursor:pointer;font-weight:bold;border-right:solid 1px;border-top:solid 1px;font-size:11px;border-top-left-radius:4px;border-top-right-radius:4px}
	ul.tabs li.current{background:#FFF;color:#222}
	.tab-content{display:none;background:#FFF;padding:15px;font-size:12px}
	.tab-content.current{display:inherit}
</style>
<script>
function close_prod(e)
{
	$(e).css({"opacity":"0"});
	$(e).hide();
	$(e).html('');
	var e2 = e+'_x';
	$('html, body').animate({     scrollTop: ($(e2).offset().top-300) + 'px'   }, 1000, 'swing');
}
function load_prod(e,url)
{
	$(e).html('loading....');
	$(e).load(url,function() {
		$(e).css({"opacity":"0"});
		$(e).show();
		// smooth scroll to the anchor id
		$('html, body').animate({     scrollTop: ($(e).offset().top-150) + 'px'   }, 1000, 'swing');
		$(e).animate({"opacity":"1","transition":"opacity .5s ease-in-out"}, 'slow');
	});
}
$(document).ready(function(){
	$('ul.tabs li').click(function(){
		var tab_id = $(this).attr('data-tab');
		$('#tab_sel').val(tab_id);
		$('ul.tabs li').removeClass('current');
		$('.tab-content').removeClass('current');
		$(this).addClass('current');
		$("#"+tab_id).addClass('current');
	});
	<?php
	if(isset($_GET['tab_sel']) && $_GET['tab_sel'] !='')
	{?>
		var tab_id = '<?php echo $_GET['tab_sel'];?>';
		$('#tab_sel').val(tab_id);
		$('ul.tabs li').removeClass('current');
		$('.tab-content').removeClass('current');
		$("#"+tab_id+"-t").addClass('current');
		$("#"+tab_id).addClass('current');
		<?php
	}
	?>
	$('.fancybox_url').live('click', function(){ var href = $(this).attr('href');$.fancybox({ 'showCloseButton' :false, 'titlePosition' :'inside', title :true, autoSize :false, modal :false, fitToView :false, topRatio :0, content :'<iframe width="100%" height="100%" src="'+href+'"></iframe>', width :754, maxHeight :600, scrolling :'no', type :'html', onComplete:function() {$("#fancybox-wrap").stop().animate({'top':'10px',}, 0);$("#fancybox-wrap").css({'z-index':'9999999999999999',}, 0);} });return false;});
});
function start()
{
	$.ajax({
		type:'GET',
		url:"start.php?d=<?=uniqid('')?>",
		dataType:'html',
		cache:false,
		async:true,
		success:function(id){ alert("terminada a atualização");}});
}
</script>
<table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
	<td valign="top" align="left"><?php require_once('../layout/menus.inc.php');?>
		<div class="panel panel-inverse overflow-hidden m-b-12">
			<div class="panel-heading">
				<h4 style="color:#fff">Atualizar a cache do site</h4>
				<div style="padding:5px">CPU: <?php $load = sys_getloadavg(); echo $load[0] . "%"; ?></div>
				<div id="carregar" style="display:block;float:right;margin-top:-30px"><input class="btn btn-primary" type="button" value="Iniciar atualizaçao" onclick="start();return false;" /></div><br>
				<div id="carregar3" style="display:block;background:#dde7d9;color:#000"></div>
			</div>
		</div>
		<br>
		<script>
			$( document).ready(function()
			{
				var tt = setInterval(function(){loadsexport();},5000);
			});
			function loadsexport()
			{
				$.ajax({
					type:'GET',
					url:"status.php?d=<?=uniqid('')?>",
					dataType:'html',
					cache:false,
					success:function(html)
					{
						document.getElementById('carregar3').innerHTML = html;
					}
				});
			}
		</script>

		<img src="../images/bsh.png" width="100%" height="30px">
		
	</td>
</tr>
</table><?php require_once('../layout/bottom.inc.php');