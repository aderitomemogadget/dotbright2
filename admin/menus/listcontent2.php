<?php
  include "settings.inc.php";
  include "../layout/header.inc.php";

  $field = explode(';', $_GET['field']);
?>
<script language="javascript">
	function ReturnUrl(string, string2, string3){
		var campo = parent.document.getElementById('<?php if(isset($_GET['field'])){ echo $field[1];}?>');
		if(campo) campo.value = string;
		var campo2 = parent.document.getElementById('<?php if(isset($_GET['field'])){ echo $field[0];}?>');
		if(campo2) campo2.value = string2;
		var campo3 = parent.document.getElementById('<?php if(isset($_GET['field'])){ echo $field[2];}?>');
		if(campo3) campo3.value = string3;
		parent.jQuery.fancybox.close();
	}
</script>

<style>
	html, body{
		background:#FFF;
	}

	.bt-submit2{
		float: left;
		background: #00689c; /* Old browsers */
		background: -moz-linear-gradient(top,  #00689c 20%, #005179 100%); /* FF3.6+ */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(20%,#00689c), color-stop(100%,#005179)); /* Chrome,Safari4+ */
		background: -webkit-linear-gradient(top,  #00689c 20%,#005179 100%); /* Chrome10+,Safari5.1+ */
		background: -o-linear-gradient(top,  #00689c 20%,#005179 100%); /* Opera 11.10+ */
		background: -ms-linear-gradient(top,  #00689c 20%,#005179 100%); /* IE10+ */
		background: linear-gradient(to bottom,  #00689c 20%,#005179 100%); /* W3C */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#00689c', endColorstr='#005179',GradientType=0 ); /* IE6-9 */
		font-size: 12px;
		padding: 4px 6px 4px 6px;
		color: #fff;
		cursor: pointer;
		width: 110px;
		height: 25px;
		text-align: center;
		border-radius: 3px;
		border:0px;
		border-top: 2px solid #317696;
		border-bottom: 2px solid #001d32;
	}

	.bt-submit2:hover{
		background: #00689c;
	}
</style>

</head>
<body topmargin=0 leftmargin=0>

	<table width="100%" border=0 cellspacing=0 cellpadding=0  style="background:#FFF">
		<tr>
			<td align="center"><h2>Website Content</h2></td>
		</tr>
	</table>

	<?php
	// echo '<table cellspacing=0 cellpadding=0 align="center" class="moduletable" width="100%"><tr><th valign="top"></th></tr><tr><td style="vertical-align:top"><table width="100%" border=0 cellpadding=0 cellspacing="1">';
	// show_links();
	// echo '</table></td><td style="vertical-align:top"><table width="100%" border=0 cellpadding=0 cellspacing="1">';
	// show_links_marcas();
	// echo '</table></td></tr></table><br><br>';
	?>

	<br>
	<div style="margin:0 auto;width:330px">
		<div style="float:left;margin-bottom:10px;font-family:arial;sans-serif"><input type="text" id="search-criteria" style="border:1px solid #ccc;color:#000;width:200px;height:22px"/></div>
		<div style="float:left;margin-bottom:10px;font-family:arial;sans-serif;margin-left:5px"><input type="button" id="search" value="Pesquisar" class="bt-submit2"/></div>
		<div style="clear:both"></div>
	</div>
	<div style="clear:both"></div>
	<div class="div-box-menu-right" style="font-family:arial, sans-serif;margin:0 auto;width:95%">
		<?php
		// echo '<table width="100%" border=0 cellpadding=0 cellspacing="1">';
		show_links();
		show_links_marcas();
		// echo '</table><br><br>';
		?>
	</div>

	<script>
		$(document).ready(function(){
			// $('.contact-name').hide();
			$('#search').click(function(){
				$('.contact-name').hide();
				var txt = $('#search-criteria').val();
				$('.contact-name').each(function(){
					var contactId = $(this).attr('data-id');
					var contactText = $(this).text().toUpperCase();
					if(contactId.indexOf(txt) != -1 || $(this).text().toUpperCase().indexOf(txt.toUpperCase()) != -1){
						$(this).show();
					}
					// if($(this).text().toUpperCase().indexOf(txt.toUpperCase()) != -1){
					// 	$(this).show();
					// }
				});
			});
		});
	</script>

	<table width="100%" border=0>
		<tr>
			<td align="center"><input class="btn btn-primary" type="reset" value="<?php echo write_lang('key_cancelar','Cancelar');?>" name="btnCancel" onClick="parent.jQuery.fancybox.close();"></td>
		</tr>
	</table>

</body>
</html>

<?php
function show_links_marcas($pai=0,$contador=-5)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$contador+=5;
	$rs=new DataObj;
	$sql="Select * from brands order by Ordem Asc";
	$hRes= $rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	?><div class="div-box-menu-right" style="font-family:arial, sans-serif;margin:0 auto;width:95%;text-align:left;padding:10px;font-weight:bold">MARCAS</div><?php
	while(!$rs->EOF()){
		$IDb=$rs->db_data["ID"];
		$rec= new DataObj;
		$rec->db_open($db_link,$DB_name,"Select * from brands_lang where ID_PAI=$IDb and lang='$admin_lang'");
		if(!$rec->EOF()){
			$nomeb=$rec->db_data["Nome"];

			if($cor=='#c8ecfa')	{$cor="#def2fa";}
			else				{$cor="#c8ecfa";}

			$i=0;
			echo '<div style="margin-bottom:5px;white-space: nowrap;overflow-x: hidden;background:'.$cor.';padding:5px;width:100%;cursor:pointer" class="contact-name" onclick=\'javascript:ReturnUrl("'.$rs->db_data["ID"].'","'.htmlentities($nomeb).'","marcas")\'>';
			echo htmlentities_b($nomeb);
			echo '</div>';

		}
		$rec->db_close();
		$rs->db_next();
		$i++;
	}
	$rs->db_close();
}

function show_links($pai=0,$contador=-5, $t=0){
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$contador+=5;
	$rs=new DataObj;
	$sql="Select * from products_families where ID_ORG='".$pai."' and visivel = 1 order by Ordem Asc";
	$hRes= $rs->db_open($db_link,$DB_name,$sql);
	$i=0;

	if(!$rs->EOF()){
		if($t==0){?><div class="div-box-menu-right" style="font-family:arial, sans-serif;margin:0 auto;width:95%;text-align:left;padding:10px;font-weight:bold">CATEGORIAS</div><?php }
		while(!$rs->EOF()){
			$IDb=$rs->db_data["ID"];
			$rec= new DataObj;
			$rec->db_open($db_link,$DB_name,"Select * from products_families_lang where ID_PAI=$IDb and lang='$admin_lang'");
			if(!$rec->EOF()){
				$nomeb=$rec->db_data["Cabecalho"];
				$i=0;
				// echo '<tr align="left"><td>';
				// echo '<div style="margin-bottom:5px;white-space: nowrap;overflow-x: hidden;background:'.$cor.';padding:5px;width:100%;cursor:pointer;text-align:left" class="contact-name" onclick=\'javascript:ReturnUrl("'.$rs->db_data["ID"].'","'.htmlentities($nomeb).'","familias")\'>';
				echo '<div style="margin-bottom:5px;white-space: nowrap;overflow-x: hidden;background:'.$cor.';padding:5px;width:100%;cursor:pointer;text-align:left" class="contact-name" data-id="'.$rs->db_data["ID"].'" onclick=\'javascript:ReturnUrl("'.$rs->db_data["ID"].'","'.htmlentities($nomeb).'","familias")\'>';
					while($i<$contador){echo "&nbsp;";$i++;}
					echo '.&nbsp;'.htmlentities_b($nomeb).'';
				echo '</div>';
				show_links($IDb,$contador, 1);
			}
			$rec->db_close();
			$rs->db_next();
			$i++;
		}
	}
	$rs->db_close();
}

function show_links2($pai=0,$contador=-5){
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$contador+=5;
	$rs=new DataObj;
	$sql="Select * from products_families where ID_ORG='".$pai."'  order by Ordem Asc";
	$hRes= $rs->db_open($db_link,$DB_name,$sql);
	$i=0;

	if(!$rs->EOF()){
		?><div class="div-box-menu-right" style="font-family:arial, sans-serif;margin:0 auto;width:95%;text-align:left;padding:10px;font-weight:bold">MARCAS</div><?php
		while(!$rs->EOF()){
			$IDb=$rs->db_data["ID"];
			$rec= new DataObj;
			$rec->db_open($db_link,$DB_name,"Select * from products_families_lang where ID_PAI=$IDb and lang='$admin_lang'");
			if(!$rec->EOF()){
				$nomeb=$rec->db_data["Cabecalho"];
				$i=0;
				echo '<tr align="left"><td>';
					while($i<$contador){echo "&nbsp;";$i++;}
					echo '.&nbsp;<a class="preto" href="javascript:;" onclick=\'javascript:ReturnUrl("'.$rs->db_data["ID"].'","'.htmlentities($nomeb).'","familias")\'>'.htmlentities_b($nomeb).'</a>';
				echo '</td></tr>';
				// show_links($IDb,$contador);
			}
			$rec->db_close();
			$rs->db_next();
			$i++;
		}
	}
	$rs->db_close();
}
?>