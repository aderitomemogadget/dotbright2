<?php
	include "settings.inc.php";

	// vamos verificar se os campos existem todos na base de dados
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$fields=db_list_fields($DB_name,'ficheiros_lang',$db_link);
	$columns=db_num_fields($fields);
	for($i=0;$i < $columns;$i++){$field_array[]=db_field_name($fields,$i);}
	if(!in_array('SEO_title',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE ficheiros_lang ADD SEO_title varchar(255) NOT NULL ');
	if(!in_array('SEO_description',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE ficheiros_lang ADD SEO_description varchar(255) NOT NULL ');


	global $DB_host,$db_link,$DB_name,$DB_user,$DB_pass;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	if($task && $ID_PROD)
	{
		switch ($task)
		{
			case "apaga":
				$sql="UPDATE ficheiros SET Home=0 WHERE ficheiros.ID='$ID_PROD'";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo "<br>".db_error($db_link);
			break;
			case "inserir":
				$sql="UPDATE ficheiros SET Home=1,Ordem=$Ordem WHERE ficheiros.ID='$ID_PROD'";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res) echo "<br>".db_error($db_link);
			break;
		}
	}


	if(isset($_POST['post_fam']) && $_POST['post_fam']==1)
	{

		$sql = 'TRUNCATE TABLE `produtos_regras1`';
		$res = Runquery($db_link,$DB_name,$sql);

		if($res)
		{
			$sql =
					'
						INSERT INTO
							produtos_regras1
							(
								ID_ORG,
								Ordem
							)
							VALUES
							(
								"'.$_POST['fam1'].'",
								"'.$_POST['ordem1'].'"
							)
					';
			$res = Runquery($db_link,$DB_name,$sql);

			$sql =
					'
						INSERT INTO
							produtos_regras1
							(
								ID_ORG,
								Ordem
							)
							VALUES
							(
								"'.$_POST['fam2'].'",
								"'.$_POST['ordem2'].'"
							)
					';
			$res = Runquery($db_link,$DB_name,$sql);

			$sql =
					'
						INSERT INTO
							ficheiros_regras1
							(
								ID_ORG,
								Ordem
							)
							VALUES
							(
								"'.$_POST['fam3'].'",
								"'.$_POST['ordem3'].'"
							)
					';
			$res = Runquery($db_link,$DB_name,$sql);

			$sql =
					'
						INSERT INTO
							ficheiros_regras1
							(
								ID_ORG,
								Ordem
							)
							VALUES
							(
								"'.$_POST['fam4'].'",
								"'.$_POST['ordem4'].'"
							)
					';
			$res = Runquery($db_link,$DB_name,$sql);
		}
	}

	db_closedb_conection($db_link);

	include "../layout/header.inc.php";
	include "../layout/body.inc.php";
?>
<script>
function allFams(value)
{
	$('.fams').hide('');
	// $('#rel'+value).html('<img src="del2.png" border="0" title="close box" />');
	if(value > 0)
	{
		$.get('allfams.php?val='+value+'&dp=<? echo date("YmdHis")?>', function(data) {  $('#fams'+value).show("slow"); $('#fams'+value).html(data);});
	}
}
function totext2(value, txt, txt2)
{
	// alert(value+' - '+txt+' - '+txt2+' ');
	// $('#rel'+value).html('<img src="del2.png" border="0" title="close box" />');
	if(value > 0)
	{
		$('#fam'+value).val(txt);
		$('#fam_'+value).html(txt2);
		// alert($('#fam'+value).val())
		// alert($('#fam_'+value).val());
	}
	$('.fams').hide('');
}
</script>
<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
<tr>
    <td valign="top" align="left"><?
        include "../layout/menus.inc.php";
		// show_page_title(write_lang('key_categorias','Categorias'));

		//echo produto_destaque_choose();

        show_page_tools(filter);?>
        <form action="reordena.php" name="lista_produtos" method="post">
        <? show_content_list();
        show_page_bottom_tools();?>
        </form>

    </td>
    <td width="10px">&nbsp;</td>
    <td valign="top"><? show_fast_tools();?></td>
</tr>
</table>
<? 	include "../layout/bottom.inc.php";?>