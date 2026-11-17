<?
function show_page_buttons()
{
	global $task_button,$btn_class;
	?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
    <tr>
    	<td align="center" valign="middle" class="t1">
        	<input class="btn <?=$btn_class?>" type="submit" name="Submit" value="<?=$task_button;?> ">
        	<input class="btn btn-secondary" type="button" name="cancelar" value="<? echo write_lang('key_cancelar','Cancelar');?>" onclick="javascript:window.open('list.php','_self');">
		</td>
     </tr>
	</table>
<?
}
function show_page_tools($filter=0)
{?>
	<div class="panel panel-inverse overflow-hidden m-b-12">
        <div class="panel-heading">
            <h4 style="color:#fff">MEDIA</h4>
        </div>
    </div>
<? }
function show_fast_tools()
{
	global $ID, $ID_ORG, $Familia, $comportamento, $comportamentos_arr;
	?>
    <table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
	<tr>
		<td width="5px" align="left"></td>
		<td class="titulo2" align="center">FAST TOOLS</td>
		<td width="5px" align="right"></td>
	</tr>
	<tr>
		<td width="5px" align="left"></td>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<? echo write_lang('key_criar_novo_media','Criar Novo Media');?>" onclick="javascript:window.open('../media_type/pagina.php?ID=0&ID_ORG=0&Dump=<?=uniqid("");?>','_self')"></td>
		<td width="5px" align="right"></td>
	</tr>
	<tr>
		<td width="5px" align="left"></td>
        <?php
        if(isset($Familia) && $Familia == 0 && in_array($comportamento,$comportamentos_arr))
		{
		?>
        	<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<? echo write_lang('key_inserir_elemento','Inserir elemento');?>" onclick="javascript:window.open('pagina.php?Familia=<?=$ID?>&Dump=<?=uniqid("");?>','_self')"></td>
         <?
		} else {
		 ?>
        <td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<? echo write_lang('key_inserir_elemento','Inserir elemento');?>" onclick="javascript:window.open('pagina.php?ID=0&ID_ORG=<?=$ID_ORG?>&Dump=<?=uniqid("");?>','_self')"></td>
        <?
		}
		?>
		<td width="5px" align="right"></td>
	</tr>
	</table>

<? }
function InsertNewRecord($emails_encomenda='',$receive_notifications='',$Default_notifications_email='',$receive_notifications_orders='',$receive_notifications_news='',$receive_notifications_registo='')
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$poli_area;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$sql="insert into modulos_config_site (ID,emails_encomenda,receive_notifications,Default_notifications_email,receive_notifications_orders,receive_notifications_news,receive_notifications_registo)
		values ('1','$emails_encomenda','$receive_notifications','$Default_notifications_email','$receive_notifications_orders','$receive_notifications_news','$receive_notifications_registo')";
	$res=Runquery($db_link,$DB_name,$sql);
	if($res)
	{
		$new_id=db_get_last_insert_id($db_link);
	}
	else echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
function ChangeRecord($id,$emails_encomenda='',$receive_notifications='',$Default_notifications_email='',$receive_notifications_orders='',$receive_notifications_news='',$receive_notifications_registo='')
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	if($id_org==$id)$id_org=0;
	$altera=0;
	$rs->db_open($db_link,$DB_name,"Select * from modulos_config_site WHERE ID=$id");
	if(!$rs->EOF())
	{
		$altera=1;
	}
	$rs->db_close();
	if(!$altera)return InsertNewRecord($emails_encomenda,$receive_notifications,$Default_notifications_email,$receive_notifications_orders,$receive_notifications_news,$receive_notifications_registo);
	else
	{

		$sql="Update modulos_config_site Set emails_encomenda='$emails_encomenda',receive_notifications='$receive_notifications',Default_notifications_email='$Default_notifications_email',receive_notifications_orders='$receive_notifications_orders',receive_notifications_news='$receive_notifications_news',receive_notifications_registo='$receive_notifications_registo' where ID=$id";
		$res=Runquery($db_link,$DB_name,$sql);
		if(!$res)echo "ERRO: ".db_error($db_link)." -- > ".$sql;

		db_closedb_conection($db_link);
		return($res);
	}
}
?>