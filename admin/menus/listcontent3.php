<?
  include "settings.inc.php";
  include "../layout/header.inc.php";
?>
<script language="javascript">
    function ReturnUrl(string)
    {
		var campo = parent.document.getElementById('<?php if(isset($_GET['field'])){ echo $_GET['field']; }?>');
		if(campo) campo.value = string;
		parent.jQuery.fancybox.close();
    }
</script>
<style>
html, body{background:#FFF}
</style>
</head>
<body topmargin=0 leftmargin=0>
<table width="100%" border=0 cellspacing=0 cellpadding=0  style="background:#FFF">
    <tr>
      <td align="center"><h2>Website Content</h2></td>
    </tr>
  </table>
<table class="border1" width="100%" heigh="100%" border=0 cellspacing=0 cellpadding=0>
  <tr><td>
<?
    echo '<table cellspacing=0 cellpadding=0 align="center" class="moduletable"><tr><th valign="top"></th></tr><tr><td><table width="100%" border=0 cellpadding=0 cellspacing="1">';
    show_links();
    echo '</table></td></tr></table><br><br>';
?>
<table width="100%" border=0><tr><td align="center"><input class="btn btn-primary" type="reset" value="<?php echo write_lang('key_cancelar','Cancelar');?>" name="btnCancel" onClick="window.close();"></td></tr></table>
</td></tr></table>
</body></html><?

function show_links($pai=0,$contador=-5)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
    	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
        $contador+=5;
    	$rs=new DataObj;
        $sql="Select * from media_type";
    	$hRes= $rs->db_open($db_link,$DB_name,$sql);
    	$i=0;
        while(!$rs->EOF())
    	{

			$tipo=$rs->db_data["Comportamento"];
			$largura=$rs->db_data["largura"];
			$altura=$rs->db_data["altura"];
			$Protegido=$rs->db_data["Protegido"];
    	    $nomeb=$rs->db_data["Nome"];

			$i=0;
			echo '<tr align="left"><td>';
				while($i<$contador){echo "&nbsp;";$i++;}
				echo '.&nbsp;<a class="preto" href="javascript:;" onclick=\'javascript:ReturnUrl("'.$rs->db_data["ID"].';'.htmlentities($nomeb).'")\'>'.htmlentities_b($nomeb).'</a>';
			echo '</td></tr>';


            $rs->db_next();
            $i++;
    	}
    	$rs->db_close();
}

?>