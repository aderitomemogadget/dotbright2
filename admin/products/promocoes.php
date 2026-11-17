<?php
  include "settings.inc.php";
  include "../layout/header.inc.php"; 
?>
<script language="javascript">
    function ReturnUrl(string='',string2 = '')
    {
    /************************************/
		var campo = parent.document.getElementById('<?php if(isset($_GET['field'])){ echo $_GET['field']; }?>');
		if(campo) campo.value = string;
    /************************************/
    var campo2 = parent.document.getElementById('<?php if(isset($_GET['field2'])){ echo $_GET['field2']; }?>');
    if(campo2) {
      //campo2.value = atob(string2);
      campo2.innerHTML = atob(string2);
    }

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
      <td align="center"><h2>Promoções</h2></td>
    </tr>
  </table>
<table class="border1" width="100%" heigh="100%" border=0 cellspacing=0 cellpadding=0>
  <tr><td>
<?
    show_links();
?>
<table width="100%" border=0><tr><td align="center"><input class="botao" type="reset" value="<?php echo write_lang('key_cancelar','Cancelar');?>" name="btnCancel" onClick="parent.jQuery.fancybox.close();"></td></tr></table>
</td></tr></table>
</body></html><?

function show_links()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
    	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    	$rs=new DataObj;
        $sql="Select * from sales_promotions 
              where (NOW() BETWEEN DATA_INICIO AND DATA_FIM)
                    AND ATIVO='1' 
              order by DESIGNACAO Asc";
    	$hRes= $rs->db_open($db_link,$DB_name,$sql);
        $c=0;
        echo '<table border=0 cellspacing=0 cellpadding=0 style="width:100%;">';
        echo '<tr style="height:50px;font-weight:bold;">';
        echo '<td>Promoção</td>';
        echo '<td>Ativa</td>';
        echo '<td>Data inicio</td>';
        echo '<td>Data fim</td>';
        echo '<td></td>';
        echo '</tr>';
        while(!$rs->EOF())
    	{
          if($c % 2 != 0) {$cor="#def2fa";$class="r1";}
          else            {$cor="#F6F6F6";$class="r2";}
          $promo_id = $rs->db_data["promo_id"]; 
          $DESIGNACAO = $rs->db_data["DESIGNACAO"]; 
          $Ativo = $rs->db_data["ATIVO"]; 
          $DATA_INICIO = $rs->db_data["DATA_INICIO"]; 
    	    $DATA_FIM = $rs->db_data["DATA_FIM"]; 
          $plink = '<a href="../sales_promotions/pagina.php?promo_id='.$promo_id.'&Dump='.uniqid().'" target="_blank" onclick="parent.jQuery.fancybox.close();" style="font-size:20px;">&#x1F589;</a>';

          $promo_link = base64_encode('<a href="../sales_promotions/pagina.php?promo_id='.$promo_id.'&Dump='.uniqid().'" target="_blank">'.utf8_decode($DESIGNACAO).'</a>');
        ?>
                
                <tr style="height:50px;width:100%;" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'" class="<?=$class?>">
                <?
				    echo '<td align=left>'.$sp.'&nbsp;<a class="preto" href="javascript:;" onclick=\'javascript:ReturnUrl("'.$promo_id.'","'.$promo_link.'")\'>'.htmlentities_b($DESIGNACAO).'</a></td>';
                ?>
                <td><?php echo $Ativo;?></td>
                <td><?php echo $DATA_INICIO;?></td>
                <td><?php echo $DATA_FIM;?></td>
                <td><?php echo $plink;?></td>
                </tr>
               
                <?
            $rs->db_next();  
    	} 
      echo '</table>';
    	$rs->db_close();
}
?>