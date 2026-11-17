<?php
	include "settings.inc.php";

	$top_search=$_POST['top_search'];


	include "../layout/header.inc.php";
	include "../layout/body.inc.php";

	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;


	?>
	<style type="text/css">
		.todolist-input, .todolist-title {
	    	display: table-cell;
	    	padding: 0px;
	        padding-right: 0px;
		}
		.todolist-input {
		    text-align: center;
		    padding-left: 5px;
		    padding-right: 5px;
		}
		.todolist > li.active > a .todolist-title {
		    padding-left: 5px;
    		text-decoration: none;
    	}
    	.panel .panel-heading .panel-title {width: 50%; float: left;}
    </style>

<div class="center">
<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
<tr>
<td align="left" valign="top"><? include "../layout/menus.inc.php"; ?>
	<div class="panel panel-inverse overflow-hidden m-b-12">
        <div class="panel-heading">
            <h4 style="color:#fff">PESQUISA POR "<?=$top_search?>"</h4>
        </div>
    </div>
    <table width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
	<tr>
	<td valign="top">
	<?php

	if($top_search != '')
	{
		$rs->db_open($db_link, $DB_name, 'SELECT ID_PAI,Cabecalho FROM products_families_lang WHERE Cabecalho like "%'.$top_search.'%" and lang="'.$admin_lang.'" Order by ID_PAI DESC Limit 10');
		if(!$rs->EOF())
		{?>
			<div class="panel panel-inverse" data-sortable-id="index-8">
				<div class="panel-heading ui-sortable-handle" style="height:40px">
				<h4 class="panel-title">Familias de produtos (max: 10)</h4>
				<div class="panel-heading-btn">
					<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
					<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-redo"></i></a>
					<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
					<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
				</div>
				</div>
				<div class="panel-body p-0">
				<ul class="todolist"><?php

				while(!$rs->EOF())
				{
					$IDb=$rs->db_data["ID_PAI"];
					$C=htmlentities_memo(stripslashes($rs->db_data["Cabecalho"]));
					$C2=htmlentities_memo(stripslashes($rs->db_data["Cabecalho2"]));?>

					<li class="active">
						<a href="../products/products_families.php?ID=<?=$IDb?>" class="todolist-container active" data-click="todolist">
							<div class="todolist-input"><input type="checkbox" name=""></div>
							<div class="todolist-title"><?=$C?></div>
						</a>
					</li><?php
					$rs->db_next();
				}?>
				</ul>
				</div>
				</div><?php
		}
		$rs->db_close();

		$sql="Select products.*,products_lang.Cabecalho,products_objs.obj_stock,products_objs.obj_pvp
        FROM products
        INNER JOIN products_lang ON products_lang.ID_PAI=products.ID
        INNER JOIN products_objs ON products_objs.obj_type_id=products.ID
        WHERE
        (
        	lower(products_lang.Cabecalho) like '%".strtolower($top_search)."%'
        	OR lower(products.Ref) like '%".strtolower($top_search)."%'
        	OR lower(products.Ref_Ean) like '%".strtolower($top_search)."%'
        	OR products.ID = '".intval($top_search)."'
        )
        AND products_lang.Lang='$admin_lang'
        GROUP by products.ID
        ORDER BY products_lang.Cabecalho Asc
        limit 0,10";

		$rs->db_open($db_link, $DB_name, $sql);
		if(!$rs->EOF())
		{?><br>
			<div class="panel panel-inverse" data-sortable-id="index-8">
				<div class="panel-heading ui-sortable-handle" style="height:40px">
				<h4 class="panel-title">Produtos (max: 10)</h4>
				<div class="panel-heading-btn">
					<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
					<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-redo"></i></a>
					<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
					<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
				</div>
				</div>
				<div class="panel-body p-0">
				<ul class="todolist"><?php

				while(!$rs->EOF())
				{
					$IDb=$rs->db_data["ID"];
					$Ref=$rs->db_data["Ref"];
					
					$Variante = $rs->db_data["Variante"];
					$estado = $rs->db_data["estado"];
					$bateria = $rs->db_data["bateria"];
		
					$Stock = $rs->db_data["obj_stock"];
					$PVP = $rs->db_data["obj_pvp"];

					$C=htmlentities_memo(stripslashes($rs->db_data["Cabecalho"]));
					?>
					<li class="active" style="height:30px;line-height:30px">
						<a href="../products/products.php?ID=<?=$IDb?>" class="todolist-container active" data-click="todolist">
							<div class="todolist-title" style="width:300px;border-right:1px solid #ddd"><?=$Ref?></div>
							<div class="todolist-title" style="border-right:1px solid #ddd;padding-right:5px"><?=$C?></div>
							<div class="todolist-title" style="width:300px">Estado: <?=$estado?>, bateria: <?=$bateria?>, Stock: <?=$Stock?>, PVP: <?=$PVP?></div>
						</a>
					</li><?php
					$rs->db_next();
				}?>
				</ul>
				</div>
				</div><?php
		}
		$rs->db_close();
	}
	?>
	</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
</div>
</body>
</html>