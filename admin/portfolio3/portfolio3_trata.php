<?
	include "settings.inc.php";
	$retorno="list.php";
	if(isset($_POST['slashes']))
	{
		$Img_peq1["name"]=$_FILES['img_peq1']['name'];
		$Img_peq1["temp"]=$_FILES['img_peq1']['tmp_name'];
		$Img_peq2["name"]=$_FILES['img_peq2']['name'];
		$Img_peq2["temp"]=$_FILES['img_peq2']['tmp_name'];
		$Img_peq3["name"]=$_FILES['img_peq3']['name'];
		$Img_peq3["temp"]=$_FILES['img_peq3']['tmp_name'];
		$Img_peq4["name"]=$_FILES['img_peq4']['name'];
		$Img_peq4["temp"]=$_FILES['img_peq4']['tmp_name'];
		$Img_peq5["name"]=$_FILES['img_peq5']['name'];
		$Img_peq5["temp"]=$_FILES['img_peq5']['tmp_name'];
		$Img_peq6["name"]=$_FILES['img_peq6']['name'];
		$Img_peq6["temp"]=$_FILES['img_peq6']['tmp_name'];
		$Img_peq7["name"]=$_FILES['img_peq7']['name'];
		$Img_peq7["temp"]=$_FILES['img_peq7']['tmp_name'];
		$img_path="../../files/portfolio3/";
		$Download_peq["name"]=$_FILES['download_peq']['name'];
		$Download_peq["temp"]=$_FILES['download_peq']['tmp_name'];
		$img_path_download="../../files/downloads/";
		if ($slashes=="'")
		{
			$cabecalho=addslashes($cabecalho);
			$texto1=addslashes($texto1);
			$texto2=addslashes($texto2);
			$texto3=addslashes($texto3);
			$texto4=addslashes($texto4);
			$cor=addslashes($cor);
			$tamanho=addslashes($tamanho);
			$strSearchWords=addslashes($strSearchWords);
		}
		if($task_form=='insere')
		{
			$retorno="portfolio3_families.php?ID=".$ID_ORG;
			$result=InsertNewProd($ID_ORG,$ordem,$cabecalho,$ref,$tamanho,$cor,$texto1,$texto2,$texto3,$img_path,$Img_peq1,$Img_peq2,$Img_peq3,$Img_peq4,$Img_peq6,$Img_peq7,$preco1,$preco2,$precopromo1,$precopromo2,$margem,$margem_activa,$destaque,$novidade,$promocao,$home,$saldo,$aproved,$tipo,$preco_prec,$strSearchWords,$marca,$peso,$usado,$stock_txt,$configurado,$configurador,$medidas,$precocusto,$stock,$download,$img_path_download,$Download_peq,$decumentario,$texto4,$pais1,$ano1);
			if($result==1)	show_box_OK(1,$retorno);
			else if($result==2)	show_box(write_lang('key_box_inserir','INSERIR'),write_lang('key_box_erro_inserir','ERRO AO INSERIR'),"Já existe um produto com essa Referencia",$retorno,0,1);
			else show_box_ERROR(1,$retorno);
		}
		elseif($task_form=='altera')
		{
			$retorno="portfolio3.php?ID=$ID&ID_ORG=$ID_ORG";
			$result=ChangeProd($ID,$m_familia,$ordem,$cabecalho,$ref,$tamanho,$cor,$texto1,$texto2,$texto3,$img_path,$Img_peq1,$Img_peq2,$Img_peq3,$Img_peq4,$Img_peq6,$Img_peq7,$Apagar_img1,$Apagar_img2,$Apagar_img3,$Apagar_img4,$Apagar_img6,$Apagar_img7,$tem_foto1,$tem_foto2,$tem_foto3,$tem_foto4,$tem_foto6,$tem_foto7,$preco1,$preco2,$precopromo1,$precopromo2,$margem,$margem_activa,$destaque,$novidade,$promocao,$home,$saldo,$aproved,$tipo,$preco_prec,$strSearchWords,$marca,$peso,$usado,$stock_txt,$configurado,$configurador,$medidas,$precocusto,$stock,$download,$img_path_download,$Download_peq,$Apagar_download,$tem_download,$decumentario,$texto4,$pais1,$ano1);
			if($result==1)	show_box_OK(2,$retorno);
			else if($result==2)	show_box(write_lang('key_box_alterar','ALTERAR'),write_lang('key_box_erro_alterar','ERRO AO ALTERAR'),"Já existe um produto com essa Referencia",$retorno,0,1);
			else		show_box_ERROR(2,$retorno);
		}
		elseif($task_form=='apaga')
		{
			$retorno="list.php";
			$result=DeleteProd($ID,$img_path);
			if($result)	show_box_OK(3,$retorno);
			else		show_box_ERROR(3,$retorno);
		}
		elseif($task_form=="duplicar")
		{
			$retorno="portfolio3_families.php?ID=".$ID_ORG;
			$result=InsertNewProd($ID_ORG,$ordem,$cabecalho,$ref,$tamanho,$cor,$texto1,$texto2,$texto3,$img_path,$Img_peq1,$Img_peq2,$Img_peq3,$Img_peq4,$Img_peq6,$Img_peq7,$preco1,$preco2,$precopromo1,$precopromo2,$margem,$margem_activa,$destaque,$novidade,$promocao,$home,$saldo,$aproved,$tipo,$preco_prec,$strSearchWords,$marca,$peso,$usado,$stock_txt,$configurado,$configurador,$medidas,$precocusto,$stock,$download,$img_path_download,$Download_peq,$decumentario,$texto4,$pais1,$ano1);
			if($result==1)	show_box_OK(1,$retorno);
			else if($result==2)	show_box(write_lang('key_box_inserir','INSERIR'),write_lang('key_box_erro_inserir','ERRO AO INSERIR'),"Já existe um produto com essa Referencia",$retorno,0,1);
			else		show_box_ERROR(1,$retorno);
		}
	}
	else show_box_ERROR(4,$retorno);
?>