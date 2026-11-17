<?
	include "settings.inc.php";

	// vamos verificar se os campos existem todos na base de dados
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$fields=db_list_fields($DB_name,'modulos_config_site',$db_link);
	$columns=db_num_fields($fields);
	for($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
	if(!in_array('receive_notifications',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD receive_notifications tinyint(1) NOT NULL ');
	if(!in_array('receive_notifications_orders',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD receive_notifications_orders tinyint(1) NOT NULL ');
	if(!in_array('receive_notifications_news',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD receive_notifications_news tinyint(1) NOT NULL ');
	if(!in_array('receive_notifications_registo',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD receive_notifications_registo tinyint(1) NOT NULL ');


	$ID=1;

	if($task=='apagar')	{$task_button = write_lang('key_apagar','Apagar');		$task_form = 'apaga'; $btn_class='btn-primary';}
	elseif($ID==0)		{$task_button = write_lang('key_inserir','Alterar');	$task_form = 'altera';$btn_class='btn-primary';}
	else				{$task_button = write_lang('key_alterar','Alterar');	$task_form = 'altera';$btn_class='btn-primary';}


	$rs=new DataObj;

	if($ID!=0)
	{
		$rs = new DataObj;
		$rs->db_open($db_link, $DB_name, "SELECT * FROM modulos_config_site WHERE ID=$ID");
		if($rs)
		{
			$emails_encomenda 				= $rs->db_data["emails_encomenda"];
			$receive_notifications			= $rs->db_data["receive_notifications"];
			$Default_notifications_email	= $rs->db_data["Default_notifications_email"];
			$receive_notifications_orders	= $rs->db_data["receive_notifications_orders"];
			$receive_notifications_news		= $rs->db_data["receive_notifications_news"];
			$receive_notifications_registo	= $rs->db_data["receive_notifications_registo"];
		}
		$rs->db_close(1);
	}


	include "../layout/header.inc.php";
	include "../layout/body.inc.php";
?>


<div class="center">
<form name="form" method="post" action="pagina_trata.php?ID=<?php echo $ID?>&task_form=<?php echo $task_form?>&dump=<?php echo uniqID("")?>" enctype="multipart/form-data">
<input type="hidden" name="slashes" value="'">

<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
<tr>
<td  align="left"><?php include "../layout/menus.inc.php"; ?>
	<div class="panel panel-inverse overflow-hidden m-b-12">
        <div class="panel-heading">
            <h4 style="color:#fff">MARKETING E NOTIFICAÇÕES</h4>
        </div>
    </div>

		<table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
		<td>
			<table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="F6F6F6" class="border1">
			<tr>
			<td >
				<table width="100%" border=0 cellpadding="2" cellspacing=0>
				<tr>
					<td width="180" class="preto">Email para notificações:</td>
					<td align="left"><input name="Default_notifications_email" style="width:300px" maxlength="255" value="<?=$Default_notifications_email?>" class="form-control"></td>
				</tr>
				<tr>
					<td width="180" class="preto">Receber um aviso sempre que correr o importador:</td>
					<td align="left"><label for="visible"><input type="checkbox" name="receive_notifications" id="receive_notifications" value="1" <?php if($receive_notifications == '1') echo "checked='checked'";?>> <? echo write_lang('key_ativo','Ativo');?></label></td>
				</tr>
				<tr>
					<td width="180" class="preto">Receber um aviso sempre que alguem subscrever a newsletter:</td>
					<td align="left"><label for="visible"><input type="checkbox" name="receive_notifications_news" id="receive_notifications_news" value="1" <?php if($receive_notifications_news == '1') echo "checked='checked'";?>> <? echo write_lang('key_ativo','Ativo');?></label></td>
				</tr>
				<tr>
					<td width="180" class="preto">Receber um aviso sempre que alguem se registar:</td>
					<td align="left"><label for="visible"><input type="checkbox" name="receive_notifications_registo" id="receive_notifications_registo" value="1" <?php if($receive_notifications_registo == '1') echo "checked='checked'";?>> <? echo write_lang('key_ativo','Ativo');?></label></td>
				</tr>
				<tr>
					<td width="180" class="preto">Email para notificações de encomendas:</td>
					<td align="left"><input name="emails_encomenda" style="width:300px" maxlength="255" value="<?php echo $emails_encomenda?>" class="form-control"></td>
				</tr>
				<tr>
					<td width="380" class="preto">Receber uma cópia sempre que houver uma encomenda:</td>
					<td align="left"><label for="visible"><input type="checkbox" name="receive_notifications_orders" id="receive_notifications_orders" value="1" <?php if($receive_notifications_orders == '1') echo "checked='checked'";?>> <? echo write_lang('key_ativo','Ativo');?></label></td>
				</tr>
				</table>
			</td>
			</tr>
			</table>
		</td>
		</tr>
		</table>

		<? /*

		<table class="p1" width="100%" cellspacing="0" cellpadding="0" border="0">
	    <tr>
	    	<td class="t1" valign="middle" align="center" width="100%">

	        	<div style="border:1px solid #000;width: 150px;position:relative;float:left:padding:15px">
	        		<input class="btn btn-secondary" type="button" name="cancelar" value="CRIAR UM POPUP" onclick="#">
	        		<img src="popup.jpg" width="150">
	        	</div>

	        	<div style="border:1px solid #000;width: 150px;position:relative;float:left:padding:15px">
	        		<input class="btn btn-secondary" type="button" name="cancelar" value="CRIAR UM sticky-footer" onclick="#">
	        		<img src="popup.jpg" width="150">
	        	</div>

	        	<input class="btn btn-secondary" type="button" name="cancelar" value="ENVIAR UM EMAIL AOS CLIENTES" onclick="#">

	        	<input class="btn btn-secondary" type="button" name="cancelar" value="CRIAR UM sticky-header" onclick="#">
	        	<input class="btn btn-secondary" type="button" name="cancelar" value="CRIAR UM sticky-scroll" onclick="#">
	        	<input class="btn btn-secondary" type="button" name="cancelar" value="Ativar chat" onclick="#">
	        	<input class="btn btn-secondary" type="button" name="cancelar" value="integrate news with facebook" onclick="#">
	        	<input class="btn btn-secondary" type="button" name="cancelar" value="integrate news with twitter" onclick="#">
	        	<input class="btn btn-secondary" type="button" name="cancelar" value="integrate news with linkding" onclick="#">
			</td>
	    </tr>
		</table>

		<?php

		*/


		show_page_buttons();

		?>
        </td>
        <td width="10px">&nbsp;</td>
    </tr>
    </table>
    </form>
</div>
<br /><br />
<?php
include "../layout/bottom.inc.php";
?>
