<?
  include "settings.inc.php";
  include "../layout/header.inc.php";
?>
<script language="javascript">
    function _CloseOnEsc() 
    {
      if (event.keyCode == 27) 
      { 
        window.close(); 
        return; 
       }
    }
    
    function ReturnUrl(string)
    {
        window.returnValue=string;
        window.close();
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
<table width="100%" border=0><tr><td align="center"><input class="botao" type="reset" value="<?php echo write_lang('key_cancelar','Cancelar');?>" name="btnCancel" onClick="window.close();"></td></tr></table>
</td></tr></table>
</body></html><?

function show_links($pai=0,$contador=-5)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $contador+=5;
	$rs=new DataObj;
    $sql="Select * from pages where ID_ORG='0' order by Ordem Asc";
	$hRes= $rs->db_open($db_link,$DB_name,$sql);
	$i=0;
    while(!$rs->EOF())
	{
	    $IDb=$rs->db_data["ID"];
	    
	    $rec= new DataObj;
    	$rec->db_open($db_link,$DB_name,"Select * from pages_lang where ID_PAI=$IDb and lang='$admin_lang'");
    	if(!$rec->EOF())
		{
			$nomeb=$rec->db_data["Cabecalho"];
			
			
			$i=0;
			echo '<tr align="left"><td>';
				while($i<$contador){echo "&nbsp;";$i++;}
				echo '.&nbsp;<a class="preto" href="javascript:;" onclick=\'javascript:ReturnUrl("index.php?page_id='.$rs->db_data["ID"].'")\'>'.htmlentities_b($nomeb).'</a>';
			echo '</td></tr>';
			show_links2($IDb,$contador);
		}
		$rec->db_close();
        $rs->db_next();	
        $i++;
	}
	$rs->db_close();
}
function show_links2($pai=0,$contador=-5)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $contador+=5;
	$rs=new DataObj;
    $sql="Select * from pages where ID_ORG='".$pai."'  order by Ordem Asc";
	$hRes= $rs->db_open($db_link,$DB_name,$sql);
	$i=0;
    while(!$rs->EOF())
	{
	    $IDb=$rs->db_data["ID"];
	    
	    $rec= new DataObj;
    	$rec->db_open($db_link,$DB_name,"Select * from pages_lang where ID_PAI=$IDb and lang='$admin_lang'");
    	if(!$rec->EOF())
		{
			$nomeb=$rec->db_data["Cabecalho"];
			
			
			$i=0;
			echo '<tr align="left"><td>';
				while($i<$contador){echo "&nbsp;";$i++;}
				echo '.&nbsp;<a class="preto" href="javascript:;" onclick=\'javascript:ReturnUrl("index.php?page_id='.$rs->db_data["ID"].'")\'>'.htmlentities_b($nomeb).'</a>';
			echo '</td></tr>';
		}
		$rec->db_close();
        $rs->db_next();	
        $i++;
	}
	$rs->db_close();
}
?>