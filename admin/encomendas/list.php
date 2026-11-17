<?php
	include "settings.inc.php";
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);


	$fields=db_list_fields($DB_name,'encomendas',$db_link);
	$columns=db_num_fields($fields);
	if($columns>0)
	{
		$field_array=array();
		for($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
		if(!in_array('portes_taxa',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE encomendas ADD portes_taxa double NOT NULL ');
		if(!in_array('total_portes_com_iva',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE encomendas ADD total_portes_com_iva double NOT NULL ');
		if(!in_array('total_portes_sem_iva',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE encomendas ADD total_portes_sem_iva double NOT NULL ');
	}


	$fields=db_list_fields($DB_name,'encomendas_produtos',$db_link);
	$columns=db_num_fields($fields);
	if($columns>0)
	{
		$field_array=array();
		for($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
		if(!in_array('Valor_custo_sem_iva',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE encomendas_produtos ADD Valor_custo_sem_iva double NOT NULL ');
		if(!in_array('Valor_sem_iva',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE encomendas_produtos ADD Valor_sem_iva double NOT NULL ');
		if(!in_array('IVA_valor',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE encomendas_produtos ADD IVA_valor double NOT NULL ');
		if(!in_array('IVA_taxa',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE encomendas_produtos ADD IVA_taxa double NOT NULL ');
		if(!in_array('Peso',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE encomendas_produtos ADD Peso double NOT NULL ');
		if(!in_array('EAN',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE encomendas_produtos ADD EAN varchar(50) NOT NULL ');

		if(!in_array('Ecotax',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE encomendas_produtos ADD Ecotax double NOT NULL ');
		if(!in_array('Ecotax_tax_rate',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE encomendas_produtos ADD Ecotax_tax_rate double NOT NULL ');
		if(!in_array('Data_entrega',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE encomendas_produtos ADD Data_entrega Date NOT NULL ');
		if(!in_array('file',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE encomendas_produtos ADD file varchar(255) NOT NULL ');
		if(!in_array('servicos',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE encomendas_produtos ADD servicos varchar(255) NOT NULL ');
		if(!in_array('Largura',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE encomendas_produtos ADD Largura int(11) NOT NULL ');
		if(!in_array('Altura',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE encomendas_produtos ADD Altura int(11) NOT NULL ');
		if(!in_array('qnt_min',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE encomendas_produtos ADD qnt_min int(11) NOT NULL ');
		if(!in_array('product_supplier_reference',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE encomendas_produtos ADD product_supplier_reference varchar(100) NOT NULL ');

		if(!in_array('Valor_com_iva',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE encomendas_produtos ADD Valor_com_iva double NOT NULL ');


	}

	// esta condicao srve apenas para reenviar o email ao cliente, os emails de alteracao do estado sao tratados no funcoes.inc.php
	$notify = 0;
	if(isset($_GET['x_id']) && $_GET['x_id'] > 0)
	{
		$x_id = $_GET['x_id'];

		if(isset($_GET['notify']) && $_GET['notify']==2)
		{
			$rs=new DataObj;
			$sql = "SELECT * FROM encomendas WHERE ID = '$x_id'";
            $rs->db_open($db_link,$DB_name,$sql);
            if(!$rs->EOF())
            {
                $enc_id =  $rs->db_data['ID'];
                $Referencia =  $rs->db_data['Referencia'];
				$Lingua=$rs->db_data["Lingua"];
				$Data_Criacao=$rs->db_data["Data_Criacao"];
				$TotalDocSemIva=$rs->db_data["TotalDocSemIva"];
				$ID_Cliente=$rs->db_data["ID_Cliente"];
				$Contacto=$rs->db_data["Contacto"];
				$m_estado=$rs->db_data["Estado"];
				$m_pagamento=$rs->db_data["Metodo_pagamento"];
				$m_guia=$rs->db_data["IDGuia"];
				$Nome=$rs->db_data["enc_Nome"];
				$Apelido=$rs->db_data["enc_Apelido"];
				$Nickname=$rs->db_data["enc_Nome"];
				$Username=$rs->db_data["enc_Email"];
				$email_cliente=$rs->db_data["email_cliente"];
				$enc_Email=$rs->db_data["enc_Email"];


				$IDGuia=$rs->db_data["IDGuia"];
				$Telefone = $rs->db_data['enc_Telefone'];
				$enc_Fatura = $rs->db_data['enc_Fatura'];
				if(!$Contacto) $Contacto="N/A";
				if($Lingua=='') $Lingua = 'pt';

				$rs->db_close();




				/*$sql = 'SELECT * FROM clientes WHERE ID="'.$ID_Cliente.'"';
				$rs->db_open($db_link,$DB_name,$sql);
				if($rs)
				{
					$m_email=$rs->db_data['Email'];
				}
				$rs->db_close();*/

                //Vai buscar os artigos
                $linhas_prods = '';$linhas_titulos = '';
				$sql='SELECT Ref, Nome, Estado FROM encomendas_produtos WHERE ID_PAI="'.$enc_id.'" AND (Estado=4 OR Estado=9)';
				$rs->db_open($db_link, $DB_name, $sql);
				if(!$rs->EOF())
				{
					while(!$rs->EOF())
					{
						$prod_estado = $rs->db_data['Estado'];
						$prod_ref = $rs->db_data['Ref'];
						$prod_nome = $rs->db_data['Nome'];

						// echo $prod_estado.'<br>';
						$enc_state = estado_encomenda($prod_estado, $Lingua);
						// $enc_state['titulo'];
						$prod_tr = str_replace('[produto]', str_replace('-', '', $prod_ref).' - '.$prod_nome, $GLOBALS['prod'][$Lingua]);
						$prod_tr = str_replace('[estado]', $enc_state['titulo2'], $prod_tr);

						if(!substr_count($linhas_titulos, $enc_state['titulo']))
						{
							if($linhas_titulos != '') $linhas_titulos .= ' / '.$enc_state['titulo'];
							else $linhas_titulos .= $enc_state['titulo'];
						}

						if($linhas_prods != '')  $linhas_prods .= '<br>'.$prod_tr.'.';
						else  $linhas_prods .= $prod_tr.'.';

						$rs->db_next();
					}
				}
				$rs->db_close();


				//Prepara o corpo do email
                global $FromName;
				$message=array();
				$enc_info=get_purchase_info($enc_id);
				$enc_state=estado_encomenda($m_estado,$GLOBALS['site_lang']);
				$message['body']=showInfoEncomenda($enc_id,'800',1,$enc_state['texto']);


				global $usar_token_enc;
				if($usar_token_enc==1)$enc_id=$Referencia;


				$enc_fatura_url='';
				if(file_exists($GLOBALS['site_shell_url'].'files/encomendas/'.$enc_Fatura) && $enc_Fatura!='')
				{
					$enc_fatura_url='<b>Fatura:</b>&nbsp;<a href="'.$GLOBALS['site_http_url'].'files/encomendas/'.$enc_Fatura.'" target="_new">Descarregue aqui a sua fatura</a>';
				}


				$body=array(
							'[n_enc]'=>$enc_id,
							'[guia]'=>$x_guia,
							'[area_cliente]'=>'<a href="'.$GLOBALS['site_http_url'].seo('pages.php?page_id='.$GLOBALS['areacliente_page_id']).'?show_compras=1" style="text-decoration:underline">Área Cliente</a>',
							'[portes]'=>numberformat($enc_info['enc_cabecalho']['Portes']),
							'[nome]'=>$enc_info['enc_cabecalho']['enc_Nome'],
							'[fatura]'=>$enc_fatura_url
						);


				$message=template_email($GLOBALS['template_encomenda'],$body,800,0,$message['body'],0,getLinguas('key_encomenda').' '.$enc_state['titulo']);


                if(send_email($enc_Email,str_replace('[nr]',$enc_id,$message['subject']).' '.$enc_state['titulo'],$message['body']))
				{
					$sql = "INSERT INTO encomendas_emails(Tabela, ID_ORG, Data_envio, MailTo, Subject, Body) VALUES('encomendas', '".$ID."','".date("Y-m-d h:i:s")."','".$enc_Email."','".$mailsubject."','".$message['body']."')";
					$res = Runquery($db_link,$DB_name,$sql);
				}

            }




		}
		else
		{
			$rs=new DataObj;
			$rs2=new DataObj;
			$rs3=new DataObj;


			if($x_estado=='7' || $x_estado=='16') $sql='UPDATE encomendas SET Estado="'.$x_estado.'", IDGuia="'.$_GET['x_guia'].'", Data_Limite="'.date('Y-m-d H:i:s').'", Data_expedicao="'.date('Y-m-d H:i:s').'" WHERE ID = "'.$x_id.'"';
			else $sql='UPDATE encomendas SET Estado="'.$x_estado.'", IDGuia="'.$_GET['x_guia'].'", Data_Limite="'.date('Y-m-d H:i:s').'" WHERE ID = "'.$x_id.'"';

			$res = Runquery($db_link,$DB_name,$sql);
			if(!$res) echo db_error($db_link).$sql."<br>";

			if($x_estado==7 || $x_estado==16 || $x_estado==38)update_encomendas_paga($x_id);
			if($x_estado==8 || $x_estado==37 || $x_estado==15)update_encomendas_nao_paga($x_id);

			$enc_state = estado_encomenda($x_estado, $GLOBALS['admin_lang']);
			$notify = $enc_state['envia_mail'];


			$sql="Select * from encomendas_produtos WHERE ID_PAI=$x_id And Apagar = 0 And Estado!=8";
			$db_res=$rs->db_open($db_link,$DB_name,$sql);
			while(!$rs->EOF())
			{
				$ID_ENC=$rs->db_data["ID"];
				$tipo=$rs->db_data["Tipo"];
				$ID_Produto=$rs->db_data["ID_Produto"];
				$ID_Cliente=$rs->db_data["ID_Cliente"];
				$Estado_anterior=$rs->db_data["Estado"];

				$tipo=0;

				$sql='UPDATE encomendas_produtos SET Estado="'.$x_estado.'", Estado="'.$x_estado.'" WHERE ID = "'.$ID_ENC.'"';
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql."<br>";

				$rs->db_next();
			}
		}

	}
	if($_POST["flag"]==1)
	{
		foreach($_POST as $varName=> $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$res=Runquery($db_link,$DB_name,"update encomendas set Estado=-1 WHERE ID=$str_id");
				if(!$res)echo db_error($db_link).$sql."<br>";
			}
		};
	}
	// if($flag==1)
	// {
	// 	foreach($_POST as $varName=> $value)
	// 	{
	// 		$dv=$value;
	// 		$str_id=substr($varName,strpos($varName,"_")+1);
	// 		if($str_id > 0 && substr_count($varName,"chk_") > 0)
	// 		{
	// 			$res=Runquery($db_link,$DB_name,"update encomendas set Estado=-1 WHERE ID=$str_id");
	// 			if(!$res)echo db_error($db_link).$sql."<br>";
	// 		}
	// 	};
	// }

	if(isset($new_estado) && $new_estado!='')
	{
		foreach($_POST as $varName=> $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				if($new_estado=='999')
					$sql="update encomendas set Estado=-1 WHERE ID=$str_id";
				else
					$sql="update encomendas set Estado=".$new_estado." WHERE ID=$str_id";
				// var_dump($sql);
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql."<br>";
				if($new_estado==7 || $new_estado==16 || $new_estado==38)update_encomendas_paga($str_id);
				if($new_estado==8 || $new_estado==37 || $new_estado==15)update_encomendas_nao_paga($str_id);
			}
		};
		// die();
	}

	$pesq=0;
	if($_POST && ($_POST["f_id"] || $_POST["f_data1"] || $_POST["f_data2"] || $_POST["f_idcli"] || $_POST["f_contacto"] || $_POST["f_pagamento"] || $_POST["f_estado"] || $_POST["f_guia"] || $_POST["f_promos"]))
	{
		$pesq=1;
		/*$registos=SearchRecords();
		$reg="";
		while(!$registos->EOF()){if($reg)$reg.=",";$reg.=$registos->db_data["ID"];$registos->db_next();}
		$registos->db_close();*/
	}
	include "../layout/header.inc.php";
	include "../layout/body.inc.php";
	?>
	<link rel="stylesheet" type="text/css" href="sa.css" />
	<link rel="stylesheet" type="text/css" href="chosen.css?dp=<?php echo dumpacache?>" />
	<script language="JavaScript" src="sa.js"></script>
    <table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
	<tr>
		<td valign="top" align="left"><? ;
			include "../layout/menus.inc.php";


			if(isset($_POST["env_email"])){envia_email();}

			if($notify == 1)
			{
				show_notify_area();
			}
			else
			{
			?>
	            <form action="list.php" method="post" name="lista_paginas" onSubmit="return checkFields()"><?

				show_page_tools(filter);
				if($reg || $pesq==1) show_content_list($reg);
				else show_content_list();

				show_page_bottom_tools();?>

                </form><?php
			}
			?>
		</td>
		<?php /* <td width="10px">&nbsp;</td>
    	<td valign="top" width="200"><? show_fast_tools();?></td> */?>
	</tr>
	</table>
	<script src="chosen.jquery.min.js" type="text/javascript"></script>
	<script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true}); </script>
<? 	include "../layout/bottom.inc.php";?>
