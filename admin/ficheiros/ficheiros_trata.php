<?php
	include "settings.inc.php";
	$retorno="list.php";

	//echo $m_familia;
	if(isset($_POST['slashes']))
	{
		$Img_peq = $_FILES['img_peq'];
		$Apagar_img = $_POST['Apagar_img'];
		$tem_foto = $_POST['tem_foto'];
		$img_path = '../../files/ficheiros/';
		$Download_peq["name"] = $_FILES['download_peq']['name'];
		$Download_peq["temp"] = $_FILES['download_peq']['tmp_name'];
		$img_path_download = "../../files/products_downl/";

		if($slashes=="'")
		{
			$cabecalho=addslashes($cabecalho);
			$texto1=addslashes($texto1);
			$texto2=addslashes($texto2);
			$texto3=addslashes($texto3);
			$texto4=addslashes($texto4);
		}
		if($task_form=='insere')
		{
			$retorno="ficheiros_pasta.php?ID=".$m_familia;
			$result=InsertNewProd($m_familia,$ordem,$cabecalho,$ref,$tamanho,$cor,$texto1,$texto2,$texto3,$img_path,$Img_peq,$preco1,$preco2,$precopromo1,$precopromo2,$margem,$margem_activa,$destaque,$novidade,$promocao,$home,$saldo,$aproved,$tipo,$preco_prec,$strSearchWords,$marca,$peso,$usado,$stock_txt,$configurado,$configurador,$medidas,$precocusto,$stock,$download,$img_path_download,$Download_peq,$decumentario,$texto4,$pais1,$ano1,$SEO_title,$SEO_description,$Grupo_ID);
			if($result==1)	show_box_OK(1,$retorno);
			else if($result==2)	show_box(write_lang('key_box_inserir','INSERIR'),write_lang('key_box_erro_inserir','ERRO AO INSERIR'),"Já existe um produto com essa Referencia",$retorno,0,1);
			else show_box_ERROR(1,$retorno);
		}
		elseif($task_form=='altera')
		{
			$retorno="ficheiros.php?ID=$ID&ID_ORG=$ID_ORG";
			$result=ChangeProd($ID,$m_familia,$ordem,$cabecalho,$ref,$tamanho,$cor,$texto1,$texto2,$texto3,$img_path,$Img_peq,$Apagar_img,$tem_foto,$preco1,$preco2,$precopromo1,$precopromo2,$margem,$margem_activa,$destaque,$novidade,$promocao,$home,$saldo,$aproved,$tipo,$preco_prec,$strSearchWords,$marca,$peso,$usado,$stock_txt,$configurado,$configurador,$medidas,$precocusto,$stock,$download,$img_path_download,$Download_peq,$Apagar_download,$tem_download,$decumentario,$texto4,$pais1,$ano1,$SEO_title,$SEO_description,$Grupo_ID);
			if($result==1)	show_box_OK(2,$retorno);
			else if($result==2)	show_box(write_lang('key_box_alterar','ALTERAR'),write_lang('key_box_erro_alterar','ERRO AO ALTERAR'),"Já existe um ficheiro com essa Referencia",$retorno,0,1);
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
			$retorno="ficheiros_pasta.php?ID=".$ID_ORG;
			$result=InsertNewProd($ID_ORG,$ordem,$cabecalho,$ref,$tamanho,$cor,$texto1,$texto2,$texto3,$img_path,$Img_peq,$preco1,$preco2,$precopromo1,$precopromo2,$margem,$margem_activa,$destaque,$novidade,$promocao,$home,$saldo,$aproved,$tipo,$preco_prec,$strSearchWords,$marca,$peso,$usado,$stock_txt,$configurado,$configurador,$medidas,$precocusto,$stock,$download,$img_path_download,$Download_peq,$decumentario,$texto4,$pais1,$ano1,$SEO_title,$SEO_description,$Grupo_ID);
			if($result==1)	show_box_OK(1,$retorno);
			else if($result==2)	show_box(write_lang('key_box_inserir','INSERIR'),write_lang('key_box_erro_inserir','ERRO AO INSERIR'),"Já existe um ficheiro com essa Referencia",$retorno,0,1);
			else		show_box_ERROR(1,$retorno);
		}
	}
	else show_box_ERROR(4,$retorno);
?>