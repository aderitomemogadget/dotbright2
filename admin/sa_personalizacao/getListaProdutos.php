<?php
  include "settings.inc.php";
  include "../layout/header.inc.php";

  $field = explode(';', $_GET['field']);
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
    /*echo '<table cellspacing=0 cellpadding=0 align="center" class="moduletable"><tr><th valign="top"></th></tr><tr><td><table width="100%" border=0 cellpadding=0 cellspacing="1">';
    show_links();
    echo '</table></td></tr></table><br><br>';
*/

    show_links();
    echo '</td></tr></table><br><br>';
?>
<table width="100%" border=0><tr><td align="center"><input class="btn btn-primary" type="reset" value="<?php echo write_lang('key_cancelar','Cancelar');?>" name="btnCancel" onClick="parent.jQuery.fancybox.close();"></td></tr></table>
</td></tr></table>
</body></html><?

function show_links($pai=0,$contador=-5)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $contador+=5;
	$rs=new DataObj;
    $sql="Select * from products where ID_ORG > 0 order by Ordem Asc";
	$hRes= $rs->db_open($db_link,$DB_name,$sql);
	$i=0;
    $c=0;
    while(!$rs->EOF())
	{
        if($c % 2 != 0) {$cor="#f0f3f5";$class="r1";$n="1";}
        else            {$cor="#FFFFFF";$class="r2";$n="2";}

	    $IDb=$rs->db_data["ID"];
	    $rec= new DataObj;
    	$rec->db_open($db_link,$DB_name,"Select * from products_lang where ID_PAI=$IDb and lang='$admin_lang'");
    	if(!$rec->EOF())
		{
			$nomeb=$rec->db_data["Cabecalho"];
			$i=0;
            ?>
                <table width="100%" height="50px" border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
                <tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
                <?
                    echo '<td align=left>'.$sp.'&nbsp;<a class="preto" href="javascript:;" onclick=\'javascript:ReturnUrl("'.$rs->db_data["ID"].';'.htmlentities($nomeb).'")\'>'.htmlentities_b($IDb.'-'.stripslashes($nomeb)).'</a></td>';
                ?>
                </tr>
                </table>
             <?
			/*echo '<tr align="left"><td>';
				while($i<$contador){echo "&nbsp;";$i++;}
				echo '.&nbsp;<a class="preto" href="javascript:;" onclick=\'javascript:ReturnUrl("'.$rs->db_data["ID"].';'.htmlentities($nomeb).'")\'>'.htmlentities_b($nomeb).'</a>';
			echo '</td></tr>';*/
		}
		$rec->db_close();
        $rs->db_next();
        $i++;
	}
	$rs->db_close();
}
?>