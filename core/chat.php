<?php
if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) exit();
function show_chat($show_off=1)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang,$site_http_url;
    global $key_chat_title,$key_chat_offline,$key_chat_online,$key_chat_away,$key_chat_msgoffline,$key_chat_msgoffline2,$key_chat_offline_send,$key_camposobr;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new Dataobj;
	$estado=0;$msg="";
	$sql="Select * from chat_admin where Estado > 0 AND Data > date_sub(now(), interval 15 second)";
	$obj->db_open($db_link, $DB_name, $sql);
	if(!$obj->EOF() && $obj->db_data["Estado"]==1)
	{
		$idadmin=$obj->db_data['ID_Admin'];
		$msg=$key_chat_online[$site_lang];
		$estado=1;
	}
	elseif(!$obj->EOF() && $obj->db_data["Estado"]==2)
	{
		$msg=$key_chat_away[$site_lang];
		$estado=0;
	}
	elseif($show_off)
	{
		$msg=$key_chat_offline[$site_lang];
		$estado=0;
	}
	$obj->db_close();
	if($estado==1)echo '<div id="chat_unopen" class="chat_unopen_on" style="display:block;" onclick="toggleChatBox();">';
	else echo '<div id="chat_unopen" class="chat_unopen" style="display:block;" onclick="toggleChatBox();">';
	echo '<div class="HvMd">'.$msg.'</div>';
	echo '</div>';
	if($estado==1)
	{
		//ONLINE
		echo '<div id="chat_open_wrapper" class="chat_open_wrapper">';
		echo '<div class="chat_header_on HvMd" onclick="toggleChatBox();" title="Clique para fechar a janela">';
		echo '<div>'.$key_chat_title[$site_lang].'</div>';
		echo '</div>';
		echo '<iframe style="background-color:#D7D7D7" marginheight="0" allowtransparency="true" marginwidth="0" src="'.$site_http_url.'chat_cliente.php?id_admin='.$idadmin.'"  width="308" height="320" frameborder="0" SCROLLING="no"><p>Your browser does not support iframes.</p></iframe>';
		echo '</div>';
	}else{
		?>
		<script type="text/javascript">
			function validarContacto(){
				var cor="#ffffe5";
				var cor2="#FFFFFF";
				campos_obrigatorios=new Array("field1","field2","field3");
				var campos_preenchidos;
				for (obj in campos_obrigatorios){
					if($('input[name="'+campos_obrigatorios[obj]+'"]').length>0){
						$('input[name="'+campos_obrigatorios[obj]+'"]').css("background-color",cor2);
					}
					if($('textarea[name="'+campos_obrigatorios[obj]+'"]').length>0){
						$('textarea[name="'+campos_obrigatorios[obj]+'"]').css("background-color",cor2);
					}
				}
				for (obj in campos_obrigatorios){
					if(document.forms['contactos'].elements[campos_obrigatorios[obj]].value==""){
						campos_preenchidos=1;
						if($('input[name="'+campos_obrigatorios[obj]+'"]').length>0){
							$('input[name="'+campos_obrigatorios[obj]+'"]').css("background-color",cor);
						}
						if($('textarea[name="'+campos_obrigatorios[obj]+'"]').length>0){
							$('textarea[name="'+campos_obrigatorios[obj]+'"]').css("background-color",cor);
						}
					}
				}
				if(campos_preenchidos==1){alert("<?=$key_camposobr[$site_lang]?>");return false;}
				return true;
			}
		</script>
		<?php //OFFLINE ou AWAY
		echo '<div id="chat_open_wrapper" class="chat_open_wrapper">';
		echo '<div class="chat_header HvMd" onclick="toggleChatBox();" title="Clique para fechar a janela">';
		echo '<div>'.$key_chat_title[$site_lang].'</div>';
		echo '</div>';
		echo '<form method="POST" action="send_request.php?id_forms=1" name="contactos" onsubmit="return validarContacto();">';
		echo '<div class="content">';
		echo '<div class="HvBd">'.$key_chat_msgoffline[$site_lang].'</div>';
		echo '<div class="HvMd" style="font-size:13px;margin-bottom:20px">'.$key_chat_msgoffline2[$site_lang].'</div>';
		echo '<div class="inputwrapper">';
		echo '<div style="float:left;width:25px;margin-top:3px;text-align:center;"><img src="layout/default/images/usericon.png" alt=""></div>';
		echo '<input type="text" name="field1" id="field1" style="width:220px">';
		echo '</div>';
		echo '<div class="inputwrapper">';
		echo '<div style="float:left;width:25px;margin-top:3px;text-align:center;"><img src="layout/default/images/emailicon.png" alt=""></div>';
		echo '<input type="text" name="field2" id="field2" style="width:220px">';
		echo '</div>';
		echo '<div class="inputwrapper">';
		echo '<div style="float:left;width:25px;margin-top:3px;text-align:center;"><img src="layout/default/images/msgicon.png" alt=""></div>';
		echo '<textarea name="field3" id="field3" style="width:220px;height:80px"></textarea>';
		echo '</div>';
		echo '<input type="submit" id="submitbt" class="none"><div class="sendbutton HvBd" onclick="$(\'#submitbt\').trigger(\'click\');">'.$key_chat_offline_send[$site_lang].'</div>';
		echo '</div>';
		echo '</form>';
		echo '</div>';
	}
}