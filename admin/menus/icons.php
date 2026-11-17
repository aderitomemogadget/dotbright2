<?php
  include "settings.inc.php";
  include "../layout/header.inc.php"; 
?>
<script language="javascript">
    function ReturnUrl(string='',string2 = '')
    {
      var campo = parent.document.getElementById('<?php if(isset($_GET['field'])){ echo $_GET['field']; }?>');
      if(campo) {campo.value = string;}
      var campo2 = parent.document.getElementById('<?php if(isset($_GET['field2'])){ echo $_GET['field2']; }?>');
      if(campo2){campo2.innerHTML = '<i class="'+string2+' fa-3x" ></i>&nbsp;&nbsp;&nbsp; <input type="hidden" name="texto_icon_fa" value="" >';}
		  parent.jQuery.fancybox.close();
    }
</script>
</head>
<body topmargin=0 leftmargin=0>
<table width="100%" border=0 cellspacing=0 cellpadding=10>
    <tr> 
      <td align="center"><h2>Menu Icons</h2></td>
    </tr>
  </table>
<table class="border1" width="100%" heigh="100%" border=0 cellspacing=10 cellpadding=0>
<tr><td>
  <?php show_links();?>
<table width="100%" border=0><tr><td align="center"><input class="botao" type="reset" value="<?php echo write_lang('key_cancelar','Cancelar');?>" name="btnCancel" onClick="parent.jQuery.fancybox.close();"></td></tr></table>
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
        $c=0;
/**
        while(!$rs->EOF())
    	{
            if($c % 2 != 0) {$cor="#def2fa";$class="r1";$n="1";}
            else            {$cor="#F6F6F6";$class="r2";$n="2";}

    	    $IDb=$rs->db_data["ID"];    	    
    	    $rec= new DataObj;
        	$rec->db_open($db_link,$DB_name,"Select * from pages_lang where ID_PAI=$IDb and lang='$admin_lang'");
        	if(!$rec->EOF())
			{
				$nomeb=$rec->db_data["Cabecalho"];						
				$i=0;				
				$sp = ''; while($i<$contador){$sp .= "&nbsp;";$i++;}?>
                <table height="50px" border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
                <tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
                <?
				    echo '<td align=left>'.$sp.'&nbsp;<a class="preto" href="javascript:;" onclick=\'javascript:ReturnUrl("pages.php?page_id='.$rs->db_data["ID"].'")\'>'.htmlentities_b($nomeb).'</a></td>';
                ?>
                </tr>
                </table>
                <?
				
			//	show_links2($IDb,$contador,$c);
			}
			$rec->db_close();
            $rs->db_next();	
            $i++;
            $c++;
    	}
    	$rs->db_close();
      /**/
//Enquire Now   EmailOrCallMeBack
//Request a Test Drive BookTestDrive
      // available actions
/**
    $actions = array(
              array('action'=>'EmailOrCallMeBack','name'=>'Enquire Now'),
              array('action'=>'BookTestDrive','name'=>'Request a Test Drive'),
              array('action'=>'RequestAQuote','name'=>'Request a Quote'),
              array('action'=>'BookService','name'=>'Request a service'),
              array('action'=>'BookAppointment','name'=>'Book an Appointment'),
              array('action'=>'ValueMyVehicle','name'=>'Value my Vehicle')
            );
            /**/
       $actions = array(
              array('action'=>'fas fa-sign-in-alt'),
              array('action'=>'fas fa-user'),
              array('action'=>'far fa-user'),
              array('action'=>'fas fa-key'),
              array('action'=>'far fa-star'),
              array('action'=>'fas fa-star'),
              array('action'=>'fas fa-shopping-cart'),
              array('action'=>'fas fa-shopping-basket'),
              array('action'=>'fas fa-store'),
              array('action'=>'fas fa-shopping-bag'),
              array('action'=>'fas fa-tag')
            );
       //<i class="far fa-user"></i>
    $tv = sizeof($actions); 
       ?> 

       <table class="r2" height="50px" cellspacing="0" cellpadding="0" border="0"> 
        <tbody><tr  style="background-color: rgb(246, 246, 246);">
        <td align="left">&nbsp;</td>                </tr>
        </tbody></table>

        <table class="r2" height="50px" cellspacing="0" cellpadding="0" border="0">
        <tbody><tr  style="background-color: rgb(246, 246, 246);font-weight: bold;">
        <td align="left">&nbsp;</td>                </tr>
        </tbody></table>

        <style type="text/css">
          .overicon:hover{
            background:#f7f7f7 !important;
          }
        </style>

        <!--js buttons-->
<div class="container">
          <div class="row">
        <?php
        $i=0;
        $k=0;
            for($j=0;$j<$tv;$j++){
              $bg = $i % 2 == 0 ? 'background:rgb(222, 242, 250);':'';
               $i++;
        ?>

        
            <div class="col-3 overicon" style="<?=$bg?>;text-align: center;padding: 16px;"  >
              <a class="preto" href="javascript:;" onclick="javascript:ReturnUrl(&quot;<?=$actions[$j]['action']?>&quot;,&quot;<?=$actions[$j]['action']?>&quot;)"><i class="<?=$actions[$j]['action']?> fa-3x"></i></a>
            </div>


       

        <?php
        if($k==3){
          echo '</div><div class="row">';
          $k=0;
          
        }
       
            }

?>
 </div>
        </div>

<table class="r1" height="50px" cellspacing="0" cellpadding="0" border="0">
        <tbody><tr onmouseover="this.style.backgroundColor='#fff'" onmouseout="this.style.backgroundColor='#fff'" style="background-color: rgb(222, 242, 250);">
        <td align="left">&nbsp;</td>                </tr>
        </tbody></table> 



        <?php

/**
             $rs = new DataObj;
        $sql = "SELECT products_families_lang.Cabecalho AS pflCabecalho, 
                       products_families_lang.ID_PAI AS pflID_PAI,
                       products_families.ID AS pflID 
        FROM products_families 
        INNER JOIN products_families_lang on  products_families.ID=products_families_lang.ID_PAI
        WHERE products_families.ID_ORG=0";
        $rs->db_open($db_link, $DB_name, $sql);   
        $categories = array(); 
        while(!$rs->EOF()) 
        {
            echo '<table class="r1" height="50px" cellspacing="0" cellpadding="0" border="0">';
            echo '<tbody>';
            echo '<tr onmouseover="this.style.backgroundColor=\'#DBE7F1\'" onmouseout="this.style.backgroundColor=\'#def2fa\'" style="background-color: rgb(222, 242, 250);">';
            echo '<td>';
            echo '<div  style="font-weight:bold;margin: 10px 7px; text-align:left;padding-left:3px;">'.$rs->db_data["pflCabecalho"].'</div>';
            
            echo '</td>';
            echo '</tr>';
            echo product_sub_categories($rs->db_data["pflID"]).'';
            echo '</tbody>';
            echo '</table>';
            $rs->db_next();
        }   
        $rs->db_close();

/**/
        ?>






       <?php


}
function show_links2($pai=0,$contador=-5,$c=0)
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
        if($c % 2 != 0) {$cor="#def2fa";$class="r1";$n="1";}
        else            {$cor="#F6F6F6";$class="r2";$n="2";}
	    
        $IDb=$rs->db_data["ID"];	    
	    $rec= new DataObj;
    	$rec->db_open($db_link,$DB_name,"Select * from pages_lang where ID_PAI=$IDb and lang='$admin_lang'");
    	if(!$rec->EOF())
		{
			$nomeb=$rec->db_data["Cabecalho"];
			$i=0;
			$sp = ''; $sp = ''; while($i<$contador){$sp .= "&nbsp;";$i++;}			
            ?>
            <table height="50px" border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
            <tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
            <?
                echo '<td align=left>'.$sp.'&nbsp;<a class="preto" href="javascript:;" onclick=\'javascript:ReturnUrl("pages.php?page_id='.$rs->db_data["ID"].'")\'>'.htmlentities_b($nomeb).'</a></td>';
            ?>
            </tr>
            </table>
            <?
			show_links2($IDb,$contador,$c);
		}
		$rec->db_close();
        $rs->db_next();	
        $i++;
        $c++;
	}
	$rs->db_close();
}


 function product_sub_categories($ID_ORG = ''){
        global $DB_host, $DB_user, $DB_pass, $DB_name, $site_http_url;
        $db_link=db_opendb_conection($DB_host, $DB_user, $DB_pass);
        $rs = new DataObj;

         $sql = "SELECT products_families_lang.Cabecalho AS pflCabecalho, 
                       products_families_lang.ID_PAI AS pflID_PAI,
                       CONCAT('".$GLOBALS['site_http_url']."files/products_families/',products_families_lang.ID_PAI,'_1.',products_families.img_ext) AS img_ref
                        FROM products_families 
        INNER JOIN products_families_lang on  products_families.ID=products_families_lang.ID_PAI
        WHERE visivel= 1 and products_families.ID_ORG=".$ID_ORG;
       

        $rs->db_open($db_link, $DB_name, $sql);   
        $categories = array(); 
        $pdata = array(); 
        $str = '';
        while(!$rs->EOF()) 
        {
           $ID_PAI = $rs->db_data["pflID_PAI"];
           $Cabecalho = $rs->db_data["pflCabecalho"];
           $title = $rs->db_data["pflCabecalho"];
           $img = $rs->db_data["img_ref"];
           //$str .= '<br>';
       
           $URL_Link=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$ID_PAI,$ID_PAI,1);
          
          $str.= '<tr style="height:20px;">';
          $str.= '<td>';
          $str .= '<div style="text-align:left;padding-left:25px;margin:5px 0px;">';
           $str .= '<a class="preto" href="javascript:;" onclick=\'javascript:ReturnUrl("pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$ID_PAI.'")\'>';
           $str .= htmlentities_b($title);
           $str .= '</a>';
          $str .= '</div>';

          $str.= '</td>';
          $str.= '</tr>';

     
           $rs->db_next();
        }   
        $rs->db_close();
        return $str;
    }





?>