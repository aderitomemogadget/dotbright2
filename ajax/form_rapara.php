<?php
	$path=dirname( __FILE__ ).'/../core/';	
	include $path.'core_files.php';	
	
	if ($_SERVER['REQUEST_METHOD'] === 'POST')
	{
		$nome = addslashes($_POST['r_nome']);
		$recolha = addslashes($_POST['r_recolha']);
		$entrega = addslashes($_POST['r_entrega']);
		$cp = addslashes($_POST['r_cp']);
		$cidade = addslashes($_POST['r_cidade']);
		$telefone = addslashes($_POST['r_tel']);
		$email = addslashes($_POST['r_email']);
		$nif = addslashes($_POST['r_nif']);
		$periodoRecolha = addslashes($_POST['r_periodo']);

		if($periodoRecolha=="1")$periodoRecolha='recollha durante a manhã';
		if($periodoRecolha=="2")$periodoRecolha='recollha durante a tarde';

		$nome2 = addslashes($_POST['r_nome2']);
		$morada2 = addslashes($_POST['r_morada2']);
		$cp2 = addslashes($_POST['r_cp2']);
		$nif2 = addslashes($_POST['r_nif2']);
		$unlock = addslashes($_POST['r_unlock']);

		$r_recolha = addslashes($_POST['r_recolha']);
		$r_nome = addslashes($_POST['r_nome']);
		$r_recolha = addslashes($_POST['r_recolha']);
		$r_cp = addslashes($_POST['r_cp']);
		$r_cidade = addslashes($_POST['r_cidade']);
		$r_tel = addslashes($_POST['r_tel']);
		$r_email = addslashes($_POST['r_email']);




		$prod_id = addslashes($_POST['prod_id']);

		$prods=get_produtc_info(intval($_POST['prod_id']));
		$prods['prod_ref']=explode('_',$prods['prod_ref'])[0];

		

		$body = array(
			'[prod_ref]'        => $prods['prod_ref'],
			'[prodnome]'        => $prods['prod_nome'],
			'[nome]'         	=> $nome,
			'[email]'         	=> $email,
			'[entrega]'         => $rc,
			'[entrega]'         => $entrega,
			'[telefone]' 		=> $telefone,
			'[nif]' 			=> $nif,
			'[cidade]' 			=> $cidade,
			'[cp]' 				=> $cp,
			'[periodoRecolha]' 	=> $periodoRecolha,
			'[unlock]' 			=> $unlock,
			'[nome2]' 			=> $nome2,
			'[morada2]' 		=> $morada2,
			'[nif2]' 			=> $nif2,
			'[cp2]' 			=> $cp2
		);

		//$message = template_email(14,$body);

		if(!$nome2)$nome2=$nome;
		if(!$morada2)$morada2=$morada;
		if(!$cp2)$cp2=$cp;
		if(!$nif2)$nif2=$nif;
		if(!$entrega)$entrega=$r_recolha;

		$message['subject'] ='Pedido de reparação.';

		$message['body'] .= '<div style="width:100%;float:left;border-radius:10px;border:solid 1px #ccc;margin-top:10px;padding:15px">';
			$message['body'] .= '<div style="width:100%;float:left;font-size:11px;font-weight:600;color:#ff8900">Resumo dos seus dados</div>';
			$message['body'] .= '<div style="width:100%;float:left;font-size:11px;font-weight:600">Nome completo: '.$r_nome.'</div>';
			$message['body'] .= '<div style="width:100%;float:left;font-size:11px;font-weight:600">Morada para recolha: '.$r_recolha.'</div>';
			$message['body'] .= '<div style="width:100%;float:left;font-size:11px;font-weight:600">Morada para entrega: '.$entrega.'</div>';

			$message['body'] .= '<div style="width:100%;float:left;font-size:11px;font-weight:600">Código Postal: '.$r_cp.'</div>';
			$message['body'] .= '<div style="width:100%;float:left;font-size:11px;font-weight:600">Telemovel: '.$telefone.'</div>';
			$message['body'] .= '<div style="width:100%;float:left;font-size:11px;font-weight:600">Email: '.$email.'</div>';
			$message['body'] .= '<div style="width:100%;float:left;font-size:11px;font-weight:600">Recolha: '.$periodoRecolha.'</div>';
			$message['body'] .= '<div style="width:100%;float:left;font-size:11px;font-weight:600">NIF: '.$nif.'</div>';

			$message['body'] .= '<div style="width:100%;float:left;font-size:11px;font-weight:600;color:#ff8900;margin-top:10px">Resumo dos seus dados Faturação</div>';
			$message['body'] .= '<div style="width:100%;float:left;font-size:11px;font-weight:600">Nome completo: '.$nome2.'</div>';
			$message['body'] .= '<div style="width:100%;float:left;font-size:11px;font-weight:600">Morada: '.$morada2.'</div>';
			$message['body'] .= '<div style="width:100%;float:left;font-size:11px;font-weight:600">Código Postal: '.$cp2.'</div>';
			$message['body'] .= '<div style="width:100%;float:left;font-size:11px;font-weight:600">NIF: '.$nif2.'</div>';
		$message['body'] .= '</div>';



		$message['body'] .= '<div style="width:100%;float:left;border-radius:10px;border:solid 1px #ccc;margin-top:10px;padding:15px">';
		$message['body'] .= '<div style="width:100%;float:left;font-size:11px;font-weight:600;color:#ff8900">Resumo da avaria</div>';
		$message['body'] .= '<div style="width:100%;float:left;font-size:11px;font-weight:600">';

		$message['body'] .= rtrim($prods['prod_nome'], '-').'<br>';
		if($prods['prod_ref']) $message['body'] .= '<b>REF:</b> '.$prods['prod_ref'].'<br>';
		if($prods['Ref_Ean']) $message['body'] .= '<b>EAN:</b> '.$prods['Ref_Ean'].'<br>';
		if($prods['fam_id'] == '163')
		{
			$message['body'] .= '24 MESES DE GARANTIA';
		}
		else
		{
			$message['body'] .= '36 MESES DE GARANTIA';
		}	
		$message['body'] .= '</div>';
		$message['body'] .= '</div>';



		if ($prods['prod_precos']['PROMOCAO'] == 1 && (strtotime($prods['prod_precos']['Promo_data_ini']) <= $today && strtotime($prods['prod_precos']['Promo_data_fim']) >=$today))$txt_campanhas = $prods['prod_precos']['Promo_descricao'];
								
		if($prods['prod_precos']['PVP'] > 0)
		{
			$pvp=$prods['prod_precos']['PVP'];
	
			if($prods['prod_precos']['IVA'] > 0)
			{
				$pvp_com_iva=$prods['prod_precos']['PVP'];
				$pvp_sem_iva=($prods['prod_precos']['PVP']/(1+$prods['prod_precos']['IVA']/100));
			}
			else
			{
				$pvp_sem_iva=$prods['prod_precos']['PVP'];
				$pvp_com_iva=($prods['prod_precos']['PVP']*(1+$prods['prod_precos']['IVA']/100));
			}
		}





		$portes=3.9837;
		$iva_portes=4.90-3.9837;

		$message['body'] .= '<div style="width:100%;float:left;border-radius:10px;border:solid 1px #ccc;margin-top:10px;padding:15px">';
			$message['body'] .= '<div style="width:100%;float:left;font-size:11px;font-weight:600;color:#ff8900">Valor estimado</div>';
			$message['body'] .= '<div style="width:100%;float:left;font-size:11px;font-weight:600">';
				$message['body'] .= '<div style="width:33%;float:left">AVARIA<br>'.numberformat($pvp_sem_iva).'</div>';
				$message['body'] .= '<div style="width:33%;float:left">PORTES<br>'.numberformat($portes).'</div>';
				$message['body'] .= '<div style="width:33%;float:left">IVA: '.numberformat($pvp_com_iva-$pvp_sem_iva+$iva_portes).'<br></div>';
				$message['body'] .= '<div style="width:33%;float:left">TOTAL: '.numberformat($pvp_com_iva+$portes).'</div>';
			$message['body'] .= '</div>';
		$message['body'] .= '</div>';



		// if(send_email('assistencia@dotbright.pt',$message['subject'],$message['body'])){
		//if(send_email('joao.carvalho@memogadget.com',$message['subject'],$message['body'])){

		$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$sql = "INSERT INTO pedidos(prod_id,nome_completo,morada,cp,telefone,NIF) VALUES ('$prod_id','$nome','$recolha','$cp','$telefone','$nif')";
		$res = Runquery($db_link,$DB_name,$sql);
		if(send_email('assistencia@dotbright.pt',$message['subject'],$message['body']))
		{
			echo 'Recebemos os seu pedido de recolha';
			send_email('aderitofernandes@gmail.com',$message['subject'],$message['body']);
			

$html='Muito obrigado pelo seu pedido de reparação.<br><br>

Próximos passos:<br><br>

1. <b>Acondicionamento do Equipamento</b>: Por favor, acondicione o seu equipamento. Utilizar a caixa original é sempre uma boa ideia. Caso não tenha a caixa original, qualquer outra caixa adequada servirá.<br><br>

2. <b>Recolha</b>: O estafeta da Nacex irá passar amanhã na morada indicada, no período que escolheu (manhã ou tarde). Apenas precisa de entregar o pacote ao estafeta.<br><br>

3. <b>Processo de Reparação</b>: Depois da recolha, o seu equipamento estará em boas mãos e trabalharemos para ser o mais breves possível na conclusão da reparação.<br><br>

Se precisar de esclarecimentos, ou quiser acrescentar alguma informação envie-nos uma mensagem pelo WhatsApp Seremos mais rápidos a responder desta forma.<br>
Whatsapp <a href="https://wa.me/914228888">https://wa.me/914228888</a><br><br>

Estamos à vossa disposição!<br><br>

Agradecemos a vossa preferência.<br><br>

Cumprimentos,<br>
Equipa DotBright';



$html___='Muito obrigado pelo seu pedido de reparação.<br><br>

<b>*Próximos passos:*</b><br><br>

1. <b>*Acondicionamento do Equipamento:*</b> Por favor, acondicione o seu equipamento. Utilizar a caixa original é sempre uma boa ideia. Caso não tenha a caixa original, qualquer outra caixa adequada servirá.<br><br>

2. <b>*Recolha:*</b> O estafeta da Nacex irá passar amanhã na morada indicada, no período que escolheu (manhã ou tarde). Apenas precisa de entregar o pacote ao estafeta.<br><br>

3. <b>*Processo de Reparação:*</b> Depois da recolha, o seu equipamento estará em boas mãos e trabalharemos para ser o mais breves possível na reparação.<br><br>

Se tiver alguma dúvida ou precisar de esclarecimentos, pode entrar em contacto connosco através do nosso número de WhatsApp: 914 228 888.<br><br><br>


Se precisar de mais alguma alteração, estamos à disposição!<br><br>

Obrigado pela preferência';

			send_email($email,$message['subject'],$html);

		}
		else
		{
			echo "Erro: requisição inválida.";
		}
	}

?>