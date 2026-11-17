<?
	include "settings.inc.php";
	include "../layout/header.inc.php";
	include "../layout/body.inc.php";?>
    <table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
	<tr>
    <td valign="top" align="left"><?
        include "../layout/menus.inc.php";?>
        <table width="100%" border=0 cellspacing=0 cellpadding=0 class="p1">
        <tr>
            <td width=29 align="center"></td>
            <td width="10px" align="center"></td>
            <td align="right" class="branco"></td>
            <td width="5"></td>
        </tr>
        </table><?
		global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
		$dbh=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		if($dbh)
		{
			$sql="select news1_families.*, URL, Cabecalho from news1_families inner join news1_families_lang on news1_families_lang.ID_PAI=news1_families.ID where ID_ORG='0' and Lang='$admin_lang' order by Ordem ASC";
			$result=new DataObj;
			$result->db_open($dbh,$DB_name,$sql);
			if(!$result->EOF())
			{
				$contador=0;
				echo '<table width=100% border=0 cellpadding=5 cellspacing=0 class="r2">';
				while (!$result->EOF())
				{
					if($contador==0) echo '<tr>';
					$codigo=$result->db_data["ID"];
					$codigo_pai=$result->db_data["ID_ORG"];
					if($result->db_data["URL"]) $nome=$result->db_data["URL"];
					else $nome=$result->db_data["Cabecalho"];?>
                    <td>
                    <table width=100% border=0 cellpadding=0 cellspacing=0>
                    <tr>
						<td width="25" height=25 valign="middle"><img src="<?=$site_http_url?>admin/images/page.png" align="absmiddle"></td><td><? if($nome) echo '<a class=italico href="news1_families.php?ID='.$codigo.'&ID_ORG='.$codigo_pai.'">'.$nome.'</a>';?></td>
					</tr>
                    </table>
                   <? tree_news1($codigo)?>
					</td><?
                    $contador++;
					if($contador>2){echo '</tr>';$contador=0;}
					$result->db_next();
				}
				echo '</table>';
			}
			$result->db_close();
		}
		function tree_news1($menu_ID=0)
		{
			global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$admin_lang;
			$dbh=db_opendb_conection($DB_host,$DB_user,$DB_pass);
			if($dbh && $menu_ID>0)
			{
				$result2=new DataObj;
				$sql="select news1_families.*,Cabecalho,URL,ID_ORG from news1_families inner join news1_families_lang on news1_families_lang.ID_PAI=news1_families.ID where ID_ORG='$menu_ID' and Lang='$admin_lang' order by Ordem ASC";
				$result2->db_open($dbh,$DB_name,$sql);
				if(!$result2->EOF())
				{
					echo '<table width=100% border=0 cellpadding=0 cellspacing=0><tr>
                    	<td width=10>&nbsp;</td>
                        <td height=15 class=menu_titulo>
						<table width=100% border=0 cellpadding=0 cellspacing=0>';
					while (!$result2->EOF())
					{
						$codigo=$result2->db_data["ID"];
						$codigo_pai=$result2->db_data["ID_ORG"];
						if($result2->db_data["URL"]) $nome=$result2->db_data["URL"];
						else $nome=$result2->db_data["Cabecalho"];
						echo '<tr><td width=5></td><td class=menu width="25" height=25><img src="'.$site_http_url.'admin/images/page2.png"></td><td><a class=italico href="news1_families.php?ID='.$codigo.'&ID_ORG='.$codigo_pai.'">'.$nome.'</a></td></tr>';
						echo '<tr><td width=5></td><td class=menu>';
						tree_news1($codigo);
						echo '</td></tr>';
						$result2->db_next();
					}
					echo '</table></td>
                    </tr>
                    </table>';
				}
				$result2->db_close();
			}
		}
?>
    </td>
    <td width="10px">&nbsp;</td>
    <td valign="top"><? show_fast_tools();?></td>
</tr>
</table>
<? 	include "../layout/bottom.inc.php";?>