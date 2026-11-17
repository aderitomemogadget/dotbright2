<?php
	header('Content-Type: text/html; charset=utf-8');
	global $path;
	$path = dirname( __FILE__ ) . '/../';


	$includes_path=dirname( __FILE__ ) . '/../../core/';
	include $includes_path.'core_files.php';

	ini_set('max_execution_time','60000');
	ini_set('max_input_time','60000');
	ini_set('memory_limit','-1');

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(0);

	define('APENAS_TRADUZIR', 1);

	$path2 = dirname( __FILE__ ) . '/../../';


	// vamos verificar se os campos existem todos na base de dados
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	verifica_campo('pages','Data_modify');
	verifica_campo('pages_lang','Data_modify');
	verifica_campo('menus_links','Data_modify');
	verifica_campo('menus_links_lang','Data_modify');
	verifica_campo('templates','Data_modify');
	verifica_campo('templates_lang','Data_modify');
	verifica_campo('templates_email','Data_modify');
	verifica_campo('templates_email_lang','Data_modify');
	verifica_campo('modulos','Data_modify');
	verifica_campo('modulos_lang','Data_modify');

	verifica_campo('cidades','Data_modify');
	verifica_campo('cidades_lang','Data_modify');

	verifica_campo('pontos_mapa','Data_modify');
	verifica_campo('pontos_mapa_lang','Data_modify');

	verifica_campo('pontos_mapa2','Data_modify');
	verifica_campo('pontos_mapa2_lang','Data_modify');



	function verifica_campo($T,$c)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$fields=db_list_fields($DB_name,'pages_lang',$db_link);
		$columns=db_num_fields($fields);

		$fields=db_list_fields($DB_name,$T,$db_link);
		$columns=db_num_fields($fields);
		for($i=0;$i < $columns;$i++){$field_array[]=db_field_name($fields,$i);}
		if(!in_array($c,$field_array)){echo '<br>ALTER TABLE '.$T.' ADD '.$c.' Date NOT NULL ';$res=Runquery($db_link,$DB_name,'ALTER TABLE '.$T.' ADD '.$c.' Date NOT NULL ');}
	}



	function translate_now($txt, $lang1, $lang2)
	{
		return $txt;


		$lang1=strtolower($lang1);
		$lang2=strtolower($lang2);

		if($lang2=='ho')$lang2='nl';

		//'key' => 'trnsl.1.1.20160826T172445Z.ad542005a80e547d.9ff6f4f7d599e4951974daafc13d5dd10bd89613',
		//'key' => 'trnsl.1.1.20171025T164305Z.51db79e62fd45ffa.fe47d57ba604f0da4577dfc7955e93b47b19b2c3',
		//'key' => 'trnsl.1.1.20171025T150953Z.a5d6546a54d0ed94.7e015afb6ae1d2d8481ca15724a109a79145fa0a',
		//'key' => 'trnsl.1.1.20171025T112917Z.452332c9a490061f.86cf5f326ae028985980496a70e9f1feb6eae11a',
		//'key' => 'trnsl.1.1.20171025T105248Z.012e69135860a6dc.f619138f6da56c7f8e550ff095faa8504f323edd',
		//
		//trnsl.1.1.20180118T100046Z.1354fddfa2baf5f0.ac01372bc5c59298b9a3b43d7e94bf9dd1015a6a
		//
		//
		//Criada pelo nelson:
		//trnsl.1.1.20180118T101144Z.b3672d36a3accbc3.adc0c40c3378bb4a37f41387af5cae8d8af9cbdd
		//pelo rafael
		//trnsl.1.1.20180118T101523Z.220f7156f44c8804.5ac90091f9711e8970b47593bcac2bd3ece39903

		$new_str = file_get_contents('https://translate.yandex.net/api/v1.5/tr.json/translate?'.http_build_query([
		'key' => 'trnsl.1.1.20180118T100046Z.1354fddfa2baf5f0.ac01372bc5c59298b9a3b43d7e94bf9dd1015a6a',
		'text' => ($txt),
		'format' => 'html',
		'lang' => "$lang1-$lang2",
		]));

		if(!$new_str)
		{
			$error = error_get_last();
			echo "HTTP request failed. Error was: ".$error['message'];
			echo '<textarea>'.$txt.'</textarea>';
			die();
		}

		$obj = json_decode($new_str);

		$array_obj = $obj->{'text'};
		$T=$array_obj[0];

		return $T;

	}

	//Vamos procurar em todas as páginas publicas se há conteudos para traduzir:

	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$rs2=new DataObj;

	$linguas=array();
	/*$sql='SELECT l.Sigla,l.Cabecalho FROM linguas l WHERE l.Visivel=1 and Sigla <> "'.$lingua_website.'" ORDER BY l.Ordem ASC';
	$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
	while (!$rs->EOF())
	{
		$linguas[]=$rs->db_data['Sigla'];
		$rs->db_next();
	}
	$rs->db_close();*/
	$linguas[0]='es';

	$t=count($linguas);





	ob_start();

//*******************************************************
echo	$T='pages';
	$T2='pages_lang';


	$rs->db_open($db_link,$DB_name,'Select '.$T.'.Data_modify as Data_modify1,'.$T2.'.* from '.$T.' INNER JOIN '.$T2.' ON '.$T2.'.ID_PAI='.$T.'.ID WHERE  '.$T2.'.Lang = "'.$lingua_website.'" AND '.$T2.'.Data_modify <= '.$T.'.Data_modify order by '.$T.'.ID Asc');
    while(!$rs->EOF())
    {
		$id_pai=$rs->db_data['ID_PAI'];
		$Cabecalho_=$rs->db_data['Cabecalho'];
		$Texto_=$rs->db_data['Texto'];
		$Texto2_=$rs->db_data['Texto2'];
		$Texto3_=$rs->db_data['Texto3'];
		$SEO_title_=$rs->db_data['SEO_title'];
		$SEO_description_=$rs->db_data['SEO_description'];
		$Data_modify1=$rs->db_data['Data_modify1'];

		//Agora vamos verificar se já foi traduzido:



		$i=0;
		while ($i<$t)
		{
			$sigla=$linguas[$i];

			if($Data_modify1=='0000-00-00')$rs2->db_open($db_link,$DB_name,'Select * from '.$T2.' WHERE ID_PAI='.$id_pai.' AND Lang = "'.$sigla.'" AND Data_modify <> "0000-00-00"');
			else $rs2->db_open($db_link,$DB_name,'Select * from '.$T2.' WHERE ID_PAI='.$id_pai.' AND Lang = "'.$sigla.'" AND Data_modify > "'.$Data_modify1.'"');

			echo $id_pai.' »» '.$sigla.' »» ';
			if(!$rs2->EOF())
			{
				//já esta atualizado
				$rs2->db_close();

				echo "já esta atualizado<br>";
			}
			else
			{
				$rs2->db_close();

				//Vamos traduzir
				echo "a traduzir pagina ".$Cabecalho_." ";

				if($Cabecalho_)$Cabecalho=stripslashes($Cabecalho_);
				if($Texto_)$Texto=stripslashes($Texto_);
				if($Texto2_)$Texto2=stripslashes($Texto2_);
				if($Texto3_)$Texto3=stripslashes($Texto3_);
				if($SEO_title_)$SEO_title=stripslashes($SEO_title_);
				if($SEO_description_)$SEO_description=stripslashes($SEO_description_);

				if($Cabecalho)$Cabecalho=translate_now($Cabecalho, $lingua_website, $sigla);
				if($Texto)$Texto=translate_now($Texto, $lingua_website, $sigla);
				if($Texto2)$Texto2=translate_now($Texto2, $lingua_website, $sigla);
				if($Texto3)$Texto3=translate_now($Texto3, $lingua_website, $sigla);
				if($SEO_title)$SEO_title=translate_now($SEO_title, $lingua_website, $sigla);
				if($SEO_description)$SEO_description=translate_now($SEO_description, $lingua_website, $sigla);

   				$ok=0;
				if($Cabecalho){$Cabecalho=addslashes($Cabecalho);$ok=1;}
				if($Texto){$Texto=addslashes($Texto);$ok=1;}
				if($Texto2){$Texto2=addslashes($Texto2);$ok=1;}
				if($Texto3){$Texto3=addslashes($Texto3);$ok=1;}
				if($SEO_title){$SEO_title=addslashes($SEO_title);$ok=1;}
				if($SEO_description){$SEO_description=addslashes($SEO_description);$ok=1;}


				$Texto=str_replace('[formule(','[form(',$Texto);
				$Texto2=str_replace('[formule(','[form(',$Texto2);
				$Texto3=str_replace('[formule(','[form(',$Texto3);


				echo " »» ".$Cabecalho." »» ";
				ob_flush();
   				flush();

				//Vamos inserir ou fazer update?
				if($ok)
				{
					$rs2->db_open($db_link,$DB_name,'Select * from '.$T2.' WHERE ID_PAI='.$id_pai.' AND '.$T2.'.Lang = "'.$sigla.'"');
					if(!$rs2->EOF())
					{
						echo "U";
						$sql="Update $T2 Set Cabecalho='$Cabecalho',Texto='$Texto',Texto2='$Texto2',Texto3='$Texto3',SEO_title='$SEO_title',SEO_description='$SEO_description',Data_modify='".date("Y-m-d")."' where ID_PAI=$id_pai and Lang='$sigla'";
					}
					else
					{
						echo "I";
						$sql="insert into $T2 (ID_PAI,Cabecalho,Texto,Texto2,Texto3,Lang,SEO_title,SEO_description,Data_modify) values ($id_pai,'$Cabecalho','$Texto','$Texto2','$Texto3','$sigla','$SEO_title','$SEO_description','".date("Y-m-d")."')";
					}
					$res=Runquery($db_link,$DB_name,$sql);
					if(!$res)echo db_error($db_link).$sql;
				}
				echo "<br>";
			}
			$i++;
		}

        $rs->db_next();
    }
    $rs->db_close();

	//*******************************************************
    //
    //
    //*******************************************************
echo	$T='modulos';
	$T2='modulos_lang';

	$rs->db_open($db_link,$DB_name,'Select '.$T.'.Data_modify as Data_modify1,'.$T2.'.* from '.$T.' INNER JOIN '.$T2.' ON '.$T2.'.ID_PAI='.$T.'.ID WHERE  '.$T2.'.Lang = "'.$lingua_website.'" AND '.$T2.'.Data_modify <= '.$T.'.Data_modify order by '.$T.'.ID Asc');
    while(!$rs->EOF())
    {
		$id_pai=$rs->db_data['ID_PAI'];
		$Cabecalho_=$rs->db_data['titulo'];
		$Texto1_=$rs->db_data['resumo'];
		$Data_modify1=$rs->db_data['Data_modify1'];

		//Agora vamos verificar se já foi traduzido:

		$i=0;
		while ($i<$t)
		{
			$sigla=$linguas[$i];

			if($Data_modify1=='0000-00-00')$rs2->db_open($db_link,$DB_name,'Select * from '.$T2.' WHERE ID_PAI='.$id_pai.' AND Lang = "'.$sigla.'" AND Data_modify <> "0000-00-00"');
			else $rs2->db_open($db_link,$DB_name,'Select * from '.$T2.' WHERE ID_PAI='.$id_pai.' AND Lang = "'.$sigla.'" AND Data_modify > "'.$Data_modify1.'"');

			echo $id_pai.' »» '.$sigla.' »» ';
			if(!$rs2->EOF())
			{
				//já esta atualizado
				$rs2->db_close();

				echo "já esta atualizado<br>";
			}
			else
			{
				$rs2->db_close();

				//Vamos traduzir
				echo "a traduzir pagina ".$Cabecalho_." ";


				$Cabecalho='';
				$Texto1='';

				if($Cabecalho_)	$Cabecalho=stripslashes($Cabecalho_);
				if($Texto1_)	$Texto1=stripslashes($Texto1_);

				if($Cabecalho)$Cabecalho=translate_now($Cabecalho, $lingua_website, $sigla);
				if($Texto1)$Texto1=translate_now($Texto1, $lingua_website, $sigla);


   				$ok=0;
				if($Cabecalho){$Cabecalho=addslashes($Cabecalho);$ok=1;}
				if($Texto1){$Texto1=addslashes($Texto1);$ok=1;}

				echo " »» ".$Cabecalho." »» ";
				ob_flush();
   				flush();

				//Vamos inserir ou fazer update?
				if($ok)
				{
					$rs2->db_open($db_link,$DB_name,'Select * from '.$T2.' WHERE ID_PAI='.$id_pai.' AND '.$T2.'.Lang = "'.$sigla.'"');
					if(!$rs2->EOF())
					{
						echo "U";
						$sql="Update $T2 Set titulo='$Cabecalho',resumo='$Texto1',Data_modify='".date("Y-m-d")."' where ID_PAI=$id_pai and Lang='$sigla'";
					}
					else
					{
						echo "I";
						$sql="insert into $T2 (ID_PAI,titulo,resumo,Lang,Data_modify) values ($id_pai,'$Cabecalho','$Texto1','$sigla','".date("Y-m-d")."')";
					}
					$res=Runquery($db_link,$DB_name,$sql);
					if(!$res)echo db_error($db_link).$sql;
				}
				echo "<br>";
			}
			$i++;
		}

        $rs->db_next();
    }
    $rs->db_close();
    //*******************************************************
    //*******************************************************
echo	$T='modulos_imgs';
	$T2='modulos_imgs_lang';

	$rs->db_open($db_link,$DB_name,'Select '.$T.'.Data_modify as Data_modify1,'.$T2.'.* from '.$T.' INNER JOIN '.$T2.' ON '.$T2.'.bloco_img_ID_PAI='.$T.'.bloco_img_id WHERE  '.$T2.'.lang = "'.$lingua_website.'" AND '.$T2.'.Data_modify <= '.$T.'.Data_modify order by '.$T.'.bloco_img_id Asc');
    while(!$rs->EOF())
    {
		$id_pai=$rs->db_data['bloco_img_ID_PAI'];
		$Cabecalho_=$rs->db_data['bloco_img_titulo'];
		$bloco_img_txt_=$rs->db_data['bloco_img_txt'];
		$Data_modify1=$rs->db_data['Data_modify1'];

		$bloco_video_yt =$rs->db_data['bloco_video_yt'];
		$bloco_video_vimeo=$rs->db_data['bloco_video_vimeo'];
		$bloco_video_code =$rs->db_data['bloco_video_code'];
		//Agora vamos verificar se já foi traduzido:



		$i=0;
		while ($i<$t)
		{
			$sigla=$linguas[$i];

			if($Data_modify1=='0000-00-00')$rs2->db_open($db_link,$DB_name,'Select * from '.$T2.' WHERE bloco_img_ID_PAI='.$id_pai.' AND lang = "'.$sigla.'" AND Data_modify <> "0000-00-00"');
			else $rs2->db_open($db_link,$DB_name,'Select * from '.$T2.' WHERE bloco_img_ID_PAI='.$id_pai.' AND lang = "'.$sigla.'" AND Data_modify > "'.$Data_modify1.'"');

			echo $id_pai.' »» '.$sigla.' »» ';
			if(!$rs2->EOF())
			{
				//já esta atualizado
				$rs2->db_close();

				echo "já esta atualizado<br>";
			}
			else
			{
				$rs2->db_close();

				//Vamos traduzir
				echo "a traduzir pagina ".$Cabecalho_." ";


				$Cabecalho='';
				$Texto1='';

				if($Cabecalho_)	$Cabecalho=stripslashes($Cabecalho_);
				if($bloco_img_txt_)	$Texto1=stripslashes($bloco_img_txt_);

				if($Cabecalho)$Cabecalho=translate_now($Cabecalho, $lingua_website, $sigla);
				if($Texto1)$Texto1=translate_now($Texto1, $lingua_website, $sigla);

   				$ok=0;
				if($Cabecalho){$Cabecalho=addslashes($Cabecalho);$ok=1;}
				if($Texto1){$Texto1=addslashes($Texto1);$ok=1;}

				echo " »» ".$Cabecalho." »» ";
				ob_flush();
   				flush();

				//Vamos inserir ou fazer update?
				if($ok)
				{
					$rs2->db_open($db_link,$DB_name,'Select * from '.$T2.' WHERE bloco_img_ID_PAI='.$id_pai.' AND '.$T2.'.lang = "'.$sigla.'"');
					if(!$rs2->EOF())
					{
						echo "U";
						$sql="Update $T2 Set bloco_img_titulo='$Cabecalho',bloco_img_txt='$Texto1',bloco_video_yt='$bloco_video_yt',bloco_video_vimeo='$bloco_video_vimeo',bloco_video_code='$bloco_video_code',Data_modify='".date("Y-m-d")."' where bloco_img_ID_PAI=$id_pai and lang='$sigla'";
					}
					else
					{
						echo "I";
						$sql="insert into $T2 (bloco_img_ID_PAI,bloco_img_titulo,bloco_img_txt,bloco_video_yt,bloco_video_vimeo,bloco_video_code,lang,Data_modify) values ($id_pai,'$Cabecalho','$Texto1','$bloco_video_yt','$bloco_video_vimeo','$bloco_video_code','$sigla','".date("Y-m-d")."')";
					}
					$res=Runquery($db_link,$DB_name,$sql);
					if(!$res)echo db_error($db_link).$sql;
				}
				echo "<br>";
			}
			$i++;
		}

        $rs->db_next();
    }
    $rs->db_close();

    

	//*******************************************************
    //
    //*******************************************************
    //*******************************************************
echo	$T='templates';
	$T2='templates_lang';

	$rs->db_open($db_link,$DB_name,'Select '.$T.'.Data_modify as Data_modify1,'.$T2.'.* from '.$T.' INNER JOIN '.$T2.' ON '.$T2.'.ID_PAI='.$T.'.ID WHERE  '.$T2.'.Lang = "'.$lingua_website.'" AND '.$T2.'.Data_modify <= '.$T.'.Data_modify order by '.$T.'.ID Asc');
    while(!$rs->EOF())
    {
		$id_pai=$rs->db_data['ID_PAI'];
		$Cabecalho_=$rs->db_data['Cabecalho'];
		$Texto1_=$rs->db_data['Texto1'];
		$Texto2_=$rs->db_data['Texto2'];
		$Data_modify1=$rs->db_data['Data_modify1'];

		//Agora vamos verificar se já foi traduzido:

		$i=0;
		while ($i<$t)
		{
			$sigla=$linguas[$i];

			if($Data_modify1=='0000-00-00')$rs2->db_open($db_link,$DB_name,'Select * from '.$T2.' WHERE ID_PAI='.$id_pai.' AND Lang = "'.$sigla.'" AND Data_modify <> "0000-00-00"');
			else $rs2->db_open($db_link,$DB_name,'Select * from '.$T2.' WHERE ID_PAI='.$id_pai.' AND Lang = "'.$sigla.'" AND Data_modify > "'.$Data_modify1.'"');

			echo $id_pai.' »» '.$sigla.' »» ';
			if(!$rs2->EOF())
			{
				//já esta atualizado
				$rs2->db_close();

				echo "já esta atualizado<br>";
			}
			else
			{
				$rs2->db_close();

				//Vamos traduzir
				echo "a traduzir pagina ".$Cabecalho_." ";


				$Cabecalho='';
				$Texto1='';
				$Texto2='';

				if($Cabecalho_)	$Cabecalho=stripslashes($Cabecalho_);
				if($Texto1_)	$Texto1=stripslashes($Texto1_);
				if($Texto2_)	$Texto2=stripslashes($Texto2_);

				/*if($Cabecalho)$Cabecalho=translate_now($Cabecalho, $lingua_website, $sigla);
				if($Texto1)$Texto1=translate_now($Texto1, $lingua_website, $sigla);
				if($Texto2)$Texto2=translate_now($Texto2, $lingua_website, $sigla);*/


   				$ok=0;
				if($Cabecalho){$Cabecalho=addslashes($Cabecalho);$ok=1;}
				if($Texto1){$Texto1=addslashes($Texto1);$ok=1;}
				if($Texto2){$Texto2=addslashes($Texto2);$ok=1;}

				echo " »» ".$Cabecalho." »» ";
				ob_flush();
   				flush();

				//Vamos inserir ou fazer update?
				if($ok)
				{
					$rs2->db_open($db_link,$DB_name,'Select * from '.$T2.' WHERE ID_PAI='.$id_pai.' AND '.$T2.'.Lang = "'.$sigla.'"');
					if(!$rs2->EOF())
					{
						echo "U";
						$sql="Update $T2 Set Cabecalho='$Cabecalho',Texto1='$Texto1',Texto2='$Texto2',Data_modify='".date("Y-m-d")."' where ID_PAI=$id_pai and Lang='$sigla'";
					}
					else
					{
						echo "I";
						$sql="insert into $T2 (ID_PAI,Cabecalho,Texto1,Texto2,Lang,Data_modify) values ($id_pai,'$Cabecalho','$Texto1','$Texto2','$sigla','".date("Y-m-d")."')";
					}
					$res=Runquery($db_link,$DB_name,$sql);
					if(!$res)echo db_error($db_link).$sql;
				}
				echo "<br>";
			}
			$i++;
		}

        $rs->db_next();
    }
    $rs->db_close();
    //*******************************************************
	ob_end_flush();





	echo	$T='templates_email';
	$T2='templates_email_lang';

	$rs->db_open($db_link,$DB_name,'Select '.$T.'.Data_modify as Data_modify1,'.$T2.'.* from '.$T.' INNER JOIN '.$T2.' ON '.$T2.'.ID_PAI='.$T.'.ID WHERE  '.$T2.'.Lang = "'.$lingua_website.'" AND '.$T2.'.Data_modify <= '.$T.'.Data_modify order by '.$T.'.ID Asc');
    while(!$rs->EOF())
    {
		$id_pai=$rs->db_data['ID_PAI'];
		$Cabecalho_=$rs->db_data['Cabecalho'];
		$Texto1_=$rs->db_data['Texto1'];
		$Texto2_=$rs->db_data['Texto2'];
		$Data_modify1=$rs->db_data['Data_modify1'];

		//Agora vamos verificar se já foi traduzido:

		$i=0;
		while ($i<$t)
		{
			$sigla=$linguas[$i];

			if($Data_modify1=='0000-00-00')$rs2->db_open($db_link,$DB_name,'Select * from '.$T2.' WHERE ID_PAI='.$id_pai.' AND Lang = "'.$sigla.'" AND Data_modify <> "0000-00-00"');
			else $rs2->db_open($db_link,$DB_name,'Select * from '.$T2.' WHERE ID_PAI='.$id_pai.' AND Lang = "'.$sigla.'" AND Data_modify > "'.$Data_modify1.'"');

			echo $id_pai.' »» '.$sigla.' »» ';
			if(!$rs2->EOF())
			{
				//já esta atualizado
				$rs2->db_close();

				echo "já esta atualizado<br>";
			}
			else
			{
				$rs2->db_close();

				//Vamos traduzir
				echo "a traduzir pagina ".$Cabecalho_." ";


				$Cabecalho='';
				$Texto1='';
				$Texto2='';

				if($Cabecalho_)	$Cabecalho=stripslashes($Cabecalho_);
				if($Texto1_)	$Texto1=stripslashes($Texto1_);
				if($Texto2_)	$Texto2=stripslashes($Texto2_);

				/*if($Cabecalho)$Cabecalho=translate_now($Cabecalho, $lingua_website, $sigla);
				if($Texto1)$Texto1=translate_now($Texto1, $lingua_website, $sigla);
				if($Texto2)$Texto2=translate_now($Texto2, $lingua_website, $sigla);*/


   				$ok=0;
				if($Cabecalho){$Cabecalho=addslashes($Cabecalho);$ok=1;}
				if($Texto1){$Texto1=addslashes($Texto1);$ok=1;}
				if($Texto2){$Texto2=addslashes($Texto2);$ok=1;}

				echo " »» ".$Cabecalho." »» ";
				ob_flush();
   				flush();

				//Vamos inserir ou fazer update?
				if($ok)
				{
					$rs2->db_open($db_link,$DB_name,'Select * from '.$T2.' WHERE ID_PAI='.$id_pai.' AND '.$T2.'.Lang = "'.$sigla.'"');
					if(!$rs2->EOF())
					{
						echo "U";
						$sql="Update $T2 Set Cabecalho='$Cabecalho',Texto1='$Texto1',Texto2='$Texto2',Data_modify='".date("Y-m-d")."' where ID_PAI=$id_pai and Lang='$sigla'";
					}
					else
					{
						echo "I";
						$sql="insert into $T2 (ID_PAI,Cabecalho,Texto1,Texto2,Lang,Data_modify) values ($id_pai,'$Cabecalho','$Texto1','$Texto2','$sigla','".date("Y-m-d")."')";
					}
					$res=Runquery($db_link,$DB_name,$sql);
					if(!$res)echo db_error($db_link).$sql;
				}
				echo "<br>";
			}
			$i++;
		}

        $rs->db_next();
    }
    $rs->db_close();
    //*******************************************************
	ob_end_flush();





	echo	$T='cidades';
	$T2='cidades_lang';

	$rs->db_open($db_link,$DB_name,'Select '.$T.'.Data_modify as Data_modify1,'.$T2.'.* from '.$T.' INNER JOIN '.$T2.' ON '.$T2.'.ID_PAI='.$T.'.ID WHERE  '.$T2.'.Lang = "'.$lingua_website.'" AND '.$T2.'.Data_modify <= '.$T.'.Data_modify order by '.$T.'.ID Asc');
    while(!$rs->EOF())
    {
		$id_pai=$rs->db_data['ID_PAI'];
		$Nome_=$rs->db_data['Nome'];
		$Data_modify1=$rs->db_data['Data_modify1'];

		//Agora vamos verificar se já foi traduzido:

		$i=0;
		while ($i<$t)
		{
			$sigla=$linguas[$i];

			if($Data_modify1=='0000-00-00')$rs2->db_open($db_link,$DB_name,'Select * from '.$T2.' WHERE ID_PAI='.$id_pai.' AND Lang = "'.$sigla.'" AND Data_modify <> "0000-00-00"');
			else $rs2->db_open($db_link,$DB_name,'Select * from '.$T2.' WHERE ID_PAI='.$id_pai.' AND Lang = "'.$sigla.'" AND Data_modify > "'.$Data_modify1.'"');

			echo $id_pai.' »» '.$sigla.' »» ';
			if(!$rs2->EOF())
			{
				//já esta atualizado
				$rs2->db_close();

				echo "já esta atualizado<br>";
			}
			else
			{
				$rs2->db_close();

				//Vamos traduzir
				echo "a traduzir pagina ".$Nome_." ";


				$Nome='';

				if($Nome_)	$Nome=stripslashes($Nome_);

   				$ok=0;
				if($Nome){$Nome=addslashes($Nome);$ok=1;}

				echo " »» ".$Nome." »» ";
				ob_flush();
   				flush();

				//Vamos inserir ou fazer update?
				if($ok)
				{
					$rs2->db_open($db_link,$DB_name,'Select * from '.$T2.' WHERE ID_PAI='.$id_pai.' AND '.$T2.'.Lang = "'.$sigla.'"');
					if(!$rs2->EOF())
					{
						echo "U";
						$sql="Update $T2 Set Nome='$Nome',Data_modify='".date("Y-m-d")."' where ID_PAI=$id_pai and Lang='$sigla'";
					}
					else
					{
						echo "I";
						$sql="insert into $T2 (ID_PAI,Nome,Lang,Data_modify) values ($id_pai,'$Nome','$sigla','".date("Y-m-d")."')";
					}
					$res=Runquery($db_link,$DB_name,$sql);
					if(!$res)echo db_error($db_link).$sql;
				}
				echo "<br>";
			}
			$i++;
		}

        $rs->db_next();
    }
    $rs->db_close();
    //*******************************************************
	ob_end_flush();



echo	$T='pontos_mapa';
	$T2='pontos_mapa_lang';

	$rs->db_open($db_link,$DB_name,'Select '.$T.'.Data_modify as Data_modify1,'.$T2.'.* from '.$T.' INNER JOIN '.$T2.' ON '.$T2.'.ID_PAI='.$T.'.ID WHERE  '.$T2.'.Lang = "'.$lingua_website.'" AND '.$T2.'.Data_modify <= '.$T.'.Data_modify order by '.$T.'.ID Asc');
    $rs->db_open($db_link,$DB_name,'Select '.$T.'.Data_modify as Data_modify1,'.$T2.'.* from '.$T.' INNER JOIN '.$T2.' ON '.$T2.'.ID_PAI='.$T.'.ID WHERE  '.$T2.'.Lang = "'.$lingua_website.'" AND '.$T2.'.Data_modify <= '.$T.'.Data_modify order by '.$T.'.ID Asc');
    while(!$rs->EOF())
    {
		$id_pai=$rs->db_data['ID_PAI'];
		$Nome_=$rs->db_data['Nome'];
		$Data_modify1=$rs->db_data['Data_modify1'];

		//Agora vamos verificar se já foi traduzido:

		$i=0;
		while ($i<$t)
		{
			$sigla=$linguas[$i];

			if($Data_modify1=='0000-00-00')$rs2->db_open($db_link,$DB_name,'Select * from '.$T2.' WHERE ID_PAI='.$id_pai.' AND Lang = "'.$sigla.'" AND Data_modify <> "0000-00-00"');
			else $rs2->db_open($db_link,$DB_name,'Select * from '.$T2.' WHERE ID_PAI='.$id_pai.' AND Lang = "'.$sigla.'" AND Data_modify > "'.$Data_modify1.'"');

			echo $id_pai.' »» '.$sigla.' »» ';
			if(!$rs2->EOF())
			{
				//já esta atualizado
				$rs2->db_close();

				echo "já esta atualizado<br>";
			}
			else
			{
				$rs2->db_close();

				//Vamos traduzir
				echo "a traduzir pagina ".$Nome_." ";


				$Nome='';

				if($Nome_)	$Nome=stripslashes($Nome_);

   				$ok=0;
				if($Nome){$Nome=addslashes($Nome);$ok=1;}

				echo " »» ".$Nome." »» ";
				ob_flush();
   				flush();

				//Vamos inserir ou fazer update?
				if($ok)
				{
					$rs2->db_open($db_link,$DB_name,'Select * from '.$T2.' WHERE ID_PAI='.$id_pai.' AND '.$T2.'.Lang = "'.$sigla.'"');
					if(!$rs2->EOF())
					{
						echo "U";
						$sql="Update $T2 Set Nome='$Nome',Data_modify='".date("Y-m-d")."' where ID_PAI=$id_pai and Lang='$sigla'";
					}
					else
					{
						echo "I";
						$sql="insert into $T2 (ID_PAI,Nome,Lang,Data_modify) values ($id_pai,'$Nome','$sigla','".date("Y-m-d")."')";
					}
					$res=Runquery($db_link,$DB_name,$sql);
					if(!$res)echo db_error($db_link).$sql;
				}
				echo "<br>";
			}
			$i++;
		}

        $rs->db_next();
    }
    $rs->db_close();
    //*******************************************************
	ob_end_flush();



echo	$T='pontos_mapa2';
	$T2='pontos_mapa2_lang';

	$rs->db_open($db_link,$DB_name,'Select '.$T.'.Data_modify as Data_modify1,'.$T2.'.* from '.$T.' INNER JOIN '.$T2.' ON '.$T2.'.ID_PAI='.$T.'.ID WHERE  '.$T2.'.Lang = "'.$lingua_website.'" AND '.$T2.'.Data_modify <= '.$T.'.Data_modify order by '.$T.'.ID Asc');
    $rs->db_open($db_link,$DB_name,'Select '.$T.'.Data_modify as Data_modify1,'.$T2.'.* from '.$T.' INNER JOIN '.$T2.' ON '.$T2.'.ID_PAI='.$T.'.ID WHERE  '.$T2.'.Lang = "'.$lingua_website.'" AND '.$T2.'.Data_modify <= '.$T.'.Data_modify order by '.$T.'.ID Asc');
    while(!$rs->EOF())
    {
		$id_pai=$rs->db_data['ID_PAI'];
		$Nome_=$rs->db_data['Nome'];
		$Data_modify1=$rs->db_data['Data_modify1'];

		//Agora vamos verificar se já foi traduzido:

		$i=0;
		while ($i<$t)
		{
			$sigla=$linguas[$i];

			if($Data_modify1=='0000-00-00')$rs2->db_open($db_link,$DB_name,'Select * from '.$T2.' WHERE ID_PAI='.$id_pai.' AND Lang = "'.$sigla.'" AND Data_modify <> "0000-00-00"');
			else $rs2->db_open($db_link,$DB_name,'Select * from '.$T2.' WHERE ID_PAI='.$id_pai.' AND Lang = "'.$sigla.'" AND Data_modify > "'.$Data_modify1.'"');

			echo $id_pai.' »» '.$sigla.' »» ';
			if(!$rs2->EOF())
			{
				//já esta atualizado
				$rs2->db_close();

				echo "já esta atualizado<br>";
			}
			else
			{
				$rs2->db_close();

				//Vamos traduzir
				echo "a traduzir pagina ".$Nome_." ";


				$Nome='';

				if($Nome_)	$Nome=stripslashes($Nome_);

   				$ok=0;
				if($Nome){$Nome=addslashes($Nome);$ok=1;}

				echo " »» ".$Nome." »» ";
				ob_flush();
   				flush();

				//Vamos inserir ou fazer update?
				if($ok)
				{
					$rs2->db_open($db_link,$DB_name,'Select * from '.$T2.' WHERE ID_PAI='.$id_pai.' AND '.$T2.'.Lang = "'.$sigla.'"');
					if(!$rs2->EOF())
					{
						echo "U";
						$sql="Update $T2 Set Nome='$Nome',Data_modify='".date("Y-m-d")."' where ID_PAI=$id_pai and Lang='$sigla'";
					}
					else
					{
						echo "I";
						$sql="insert into $T2 (ID_PAI,Nome,Lang,Data_modify) values ($id_pai,'$Nome','$sigla','".date("Y-m-d")."')";
					}
					$res=Runquery($db_link,$DB_name,$sql);
					if(!$res)echo db_error($db_link).$sql;
				}
				echo "<br>";
			}
			$i++;
		}

        $rs->db_next();
    }
    $rs->db_close();
    //*******************************************************
	ob_end_flush();



	
    //*******************************************************
    //
    //
    //
    //
    //*******************************************************
echo	$T='menus_links';
	$T2='menus_links_lang';

echo'<br><br>';
echo 'Select '.$T.'.Data_modify as Data_modify1,'.$T2.'.* from '.$T.' INNER JOIN '.$T2.' ON '.$T2.'.ID_PAI='.$T.'.ID WHERE  '.$T2.'.Lang = "'.$lingua_website.'" AND '.$T2.'.Data_modify <= '.$T.'.Data_modify order by '.$T.'.ID Asc';
echo'<br><br>';




	$rs->db_open($db_link,$DB_name,'Select '.$T.'.Data_modify as Data_modify1,'.$T2.'.* from '.$T.' INNER JOIN '.$T2.' ON '.$T2.'.ID_PAI='.$T.'.ID WHERE  '.$T2.'.Lang = "'.$lingua_website.'" AND '.$T2.'.Data_modify <= '.$T.'.Data_modify order by '.$T.'.ID Asc');
    while(!$rs->EOF())
    {
		$id_pai=$rs->db_data['ID_PAI'];
		$Cabecalho_=$rs->db_data['URL_Nome2'];
		$Data_modify1=$rs->db_data['Data_modify1'];

		//Agora vamos verificar se já foi traduzido:



		$i=0;
		while ($i<$t)
		{
			$sigla=$linguas[$i];

			if($Data_modify1=='0000-00-00')$rs2->db_open($db_link,$DB_name,'Select * from '.$T2.' WHERE ID_PAI='.$id_pai.' AND Lang = "'.$sigla.'" AND Data_modify <> "0000-00-00"');
			else $rs2->db_open($db_link,$DB_name,'Select * from '.$T2.' WHERE ID_PAI='.$id_pai.' AND Lang = "'.$sigla.'" AND Data_modify > "'.$Data_modify1.'"');

			echo $id_pai.' »» '.$sigla.' »» ';
			if(!$rs2->EOF())
			{
				//já esta atualizado
				$rs2->db_close();

				echo "já esta atualizado<br>";
			}
			else
			{
				$rs2->db_close();

				//Vamos traduzir
				echo "a traduzir pagina ".$Cabecalho_." ";

				if($Cabecalho_)$Cabecalho=stripslashes($Cabecalho_);

				if($Cabecalho)$Cabecalho=translate_now($Cabecalho, $lingua_website, $sigla);

   				$ok=0;
				if($Cabecalho){$Cabecalho=addslashes($Cabecalho);$ok=1;}

				echo " »» ".$Cabecalho." »» ";
				ob_flush();
   				flush();

				//Vamos inserir ou fazer update?
				if($ok)
				{
					$rs2->db_open($db_link,$DB_name,'Select * from '.$T2.' WHERE ID_PAI='.$id_pai.' AND '.$T2.'.Lang = "'.$sigla.'"');
					if(!$rs2->EOF())
					{
						echo "U";
						$sql="Update $T2 Set URL_Nome2='$Cabecalho',Data_modify='".date("Y-m-d")."' where ID_PAI=$id_pai and Lang='$sigla'";
					}
					else
					{
						echo "I";
						$sql="insert into $T2 (ID_PAI,URL_Nome2,Lang,Data_modify) values ($id_pai,'$Cabecalho','$sigla','".date("Y-m-d")."')";
					}
					$res=Runquery($db_link,$DB_name,$sql);
					if(!$res)echo db_error($db_link).$sql;
				}
				echo "<br>";
			}
			$i++;
		}

        $rs->db_next();
    }
    $rs->db_close();
    //*******************************************************








//*******************************************************
echo	$T='products_families';
	$T2='products_families_lang';

	$rs->db_open($db_link,$DB_name,'Select '.$T.'.Data_modify as Data_modify1,'.$T2.'.* from '.$T.' INNER JOIN '.$T2.' ON '.$T2.'.ID_PAI='.$T.'.ID WHERE  '.$T2.'.Lang = "'.$lingua_website.'" AND '.$T2.'.Data_modify <= '.$T.'.Data_modify order by '.$T.'.ID Asc');
    while(!$rs->EOF())
    {
		$id_pai=$rs->db_data['ID_PAI'];
		$Cabecalho_=$rs->db_data['Cabecalho'];
		$Areas_=$rs->db_data['Areas'];
		$Data_modify1=$rs->db_data['Data_modify1'];

		//Agora vamos verificar se já foi traduzido:



		$i=0;
		while ($i<$t)
		{
			$sigla=$linguas[$i];

			if($Data_modify1=='0000-00-00')$rs2->db_open($db_link,$DB_name,'Select * from '.$T2.' WHERE ID_PAI='.$id_pai.' AND Lang = "'.$sigla.'" AND Data_modify <> "0000-00-00"');
			else $rs2->db_open($db_link,$DB_name,'Select * from '.$T2.' WHERE ID_PAI='.$id_pai.' AND Lang = "'.$sigla.'" AND Data_modify > "'.$Data_modify1.'"');

			echo $id_pai.' »» '.$sigla.' »» ';
			if(!$rs2->EOF())
			{
				//já esta atualizado
				$rs2->db_close();

				echo "já esta atualizado<br>";
			}
			else
			{
				$rs2->db_close();

				//Vamos traduzir
				echo "a traduzir pagina ".$Cabecalho_." ";

				$Cabecalho='';
				$Areas='';

				if($Cabecalho_)	$Cabecalho=stripslashes($Cabecalho_);
				if($Areas_)		$Areas=stripslashes($Areas_);

				if($Cabecalho)$Cabecalho=translate_now($Cabecalho, $lingua_website, $sigla);
				if($Areas)$Areas=translate_now($Areas, $lingua_website, $sigla);

   				$ok=0;
				if($Cabecalho){$Cabecalho=addslashes($Cabecalho);$ok=1;}
				if($Areas){$Areas=addslashes($Areas);$ok=1;}

				echo " »» ".$Cabecalho." »» ";
				ob_flush();
   				flush();

				//Vamos inserir ou fazer update?
				if($ok)
				{
					$rs2->db_open($db_link,$DB_name,'Select * from '.$T2.' WHERE ID_PAI='.$id_pai.' AND '.$T2.'.Lang = "'.$sigla.'"');
					if(!$rs2->EOF())
					{
						echo "U";
						$sql="Update $T2 Set Cabecalho='$Cabecalho',Areas='$Areas',Data_modify='".date("Y-m-d")."' where ID_PAI=$id_pai and Lang='$sigla'";
					}
					else
					{
						echo "I";
						$sql="insert into $T2 (ID_PAI,Cabecalho,Areas,Lang,Data_modify) values ($id_pai,'$Cabecalho','$Areas','$sigla','".date("Y-m-d")."')";
					}
					$res=Runquery($db_link,$DB_name,$sql);
					if(!$res)echo db_error($db_link).$sql;
				}
				echo "<br>";
			}
			$i++;
		}

        $rs->db_next();
    }
    $rs->db_close();
    //*******************************************************
    //
    //*******************************************************
echo	$T='products';
	$T2='products_lang';

	$rs->db_open($db_link,$DB_name,'Select '.$T.'.Data_modify as Data_modify1,'.$T2.'.* from '.$T.' INNER JOIN '.$T2.' ON '.$T2.'.ID_PAI='.$T.'.ID WHERE  '.$T2.'.Lang = "'.$lingua_website.'" AND '.$T2.'.Data_modify <= '.$T.'.Data_modify order by '.$T.'.ID Asc');
    while(!$rs->EOF())
    {
		$id_pai=$rs->db_data['ID_PAI'];
		$Cabecalho_=$rs->db_data['Cabecalho'];
		$Texto1_=$rs->db_data['Texto1'];
		$Texto2_=$rs->db_data['Texto2'];
		$Texto3_=$rs->db_data['Texto3'];
		$SEO_title_=$rs->db_data['SEO_title'];
		$SEO_description_=$rs->db_data['SEO_description'];
		$Data_modify1=$rs->db_data['Data_modify1'];

		//Agora vamos verificar se já foi traduzido:



		$i=0;
		while ($i<$t)
		{
			$sigla=$linguas[$i];

			if($Data_modify1=='0000-00-00')$rs2->db_open($db_link,$DB_name,'Select * from '.$T2.' WHERE ID_PAI='.$id_pai.' AND Lang = "'.$sigla.'" AND Data_modify <> "0000-00-00"');
			else $rs2->db_open($db_link,$DB_name,'Select * from '.$T2.' WHERE ID_PAI='.$id_pai.' AND Lang = "'.$sigla.'" AND Data_modify > "'.$Data_modify1.'"');

			echo $id_pai.' »» '.$sigla.' »» ';
			if(!$rs2->EOF())
			{
				//já esta atualizado
				$rs2->db_close();

				echo "já esta atualizado<br>";
			}
			else
			{
				$rs2->db_close();

				//Vamos traduzir
				echo "a traduzir pagina ".$Cabecalho_." ";


				$Cabecalho='';
				$Texto1='';
				$Texto2='';
				$Texto3='';
				$SEO_title='';
				$SEO_description='';

				if($Cabecalho_)	$Cabecalho=stripslashes($Cabecalho_);
				if($Texto1_)	$Texto1=stripslashes($Texto1_);
				if($Texto2_)	$Texto2=stripslashes($Texto2_);
				if($Texto3_)	$Texto3=stripslashes($Texto3_);
				if($SEO_title_)	$SEO_title=stripslashes($SEO_title_);
				if($SEO_description_)	$SEO_description=stripslashes($SEO_description_);

				if($Cabecalho)$Cabecalho=translate_now($Cabecalho, $lingua_website, $sigla);
				if($Texto1)$Texto1=translate_now($Texto1, $lingua_website, $sigla);
				if($Texto2)$Texto2=translate_now($Texto2, $lingua_website, $sigla);
				if($Texto3)$Texto3=translate_now($Texto3, $lingua_website, $sigla);
				if($SEO_title)$SEO_title=translate_now($SEO_title, $lingua_website, $sigla);
				if($SEO_description)$SEO_description=translate_now($SEO_description, $lingua_website, $sigla);

   				$ok=0;
				if($Cabecalho){$Cabecalho=addslashes($Cabecalho);$ok=1;}
				if($Texto1){$Texto1=addslashes($Texto1);$ok=1;}
				if($Texto2){$Texto2=addslashes($Texto2);$ok=1;}
				if($Texto3){$Texto3=addslashes($Texto3);$ok=1;}
				if($SEO_title){$SEO_title=addslashes($SEO_title);$ok=1;}
				if($SEO_description){$SEO_description=addslashes($SEO_description);$ok=1;}

				echo " »» ".$Cabecalho." »» ";
				ob_flush();
   				flush();

				//Vamos inserir ou fazer update?
				if($ok)
				{
					$rs2->db_open($db_link,$DB_name,'Select * from '.$T2.' WHERE ID_PAI='.$id_pai.' AND '.$T2.'.Lang = "'.$sigla.'"');
					if(!$rs2->EOF())
					{
						echo "U";
						$sql="Update $T2 Set Cabecalho='$Cabecalho',Texto1='$Texto1',Texto2='$Texto2',Texto3='$Texto3',SEO_title='$SEO_title',SEO_description='$SEO_description',Data_modify='".date("Y-m-d")."' where ID_PAI=$id_pai and Lang='$sigla'";
					}
					else
					{
						echo "I";
						$sql="insert into $T2 (ID_PAI,Cabecalho,Texto1,Texto2,Texto3,SEO_title,SEO_description,Lang,Data_modify) values ($id_pai,'$Cabecalho','$Texto1','$Texto2','$Texto3','$SEO_title','$SEO_description','$sigla','".date("Y-m-d")."')";
					}
					$res=Runquery($db_link,$DB_name,$sql);
					if(!$res)echo db_error($db_link).$sql;
				}
				echo "<br>";
			}
			$i++;
		}

        $rs->db_next();
    }
    $rs->db_close();
    //*******************************************************
    //
    //
    //*******************************************************
echo	$T='modulos';
	$T2='modulos_lang';

	$rs->db_open($db_link,$DB_name,'Select '.$T.'.Data_modify as Data_modify1,'.$T2.'.* from '.$T.' INNER JOIN '.$T2.' ON '.$T2.'.ID_PAI='.$T.'.ID WHERE  '.$T2.'.Lang = "'.$lingua_website.'" AND '.$T2.'.Data_modify <= '.$T.'.Data_modify order by '.$T.'.ID Asc');
    while(!$rs->EOF())
    {
		$id_pai=$rs->db_data['ID_PAI'];
		$Cabecalho_=$rs->db_data['titulo'];
		$Texto1_=$rs->db_data['resumo'];
		$Data_modify1=$rs->db_data['Data_modify1'];

		//Agora vamos verificar se já foi traduzido:

		$i=0;
		while ($i<$t)
		{
			$sigla=$linguas[$i];

			if($Data_modify1=='0000-00-00')$rs2->db_open($db_link,$DB_name,'Select * from '.$T2.' WHERE ID_PAI='.$id_pai.' AND Lang = "'.$sigla.'" AND Data_modify <> "0000-00-00"');
			else $rs2->db_open($db_link,$DB_name,'Select * from '.$T2.' WHERE ID_PAI='.$id_pai.' AND Lang = "'.$sigla.'" AND Data_modify > "'.$Data_modify1.'"');

			echo $id_pai.' »» '.$sigla.' »» ';
			if(!$rs2->EOF())
			{
				//já esta atualizado
				$rs2->db_close();

				echo "já esta atualizado<br>";
			}
			else
			{
				$rs2->db_close();

				//Vamos traduzir
				echo "a traduzir pagina ".$Cabecalho_." ";


				$Cabecalho='';
				$Texto1='';

				if($Cabecalho_)	$Cabecalho=stripslashes($Cabecalho_);
				if($Texto1_)	$Texto1=stripslashes($Texto1_);

				if($Cabecalho)$Cabecalho=translate_now($Cabecalho, $lingua_website, $sigla);
				if($Texto1)$Texto1=translate_now($Texto1, $lingua_website, $sigla);


   				$ok=0;
				if($Cabecalho){$Cabecalho=addslashes($Cabecalho);$ok=1;}
				if($Texto1){$Texto1=addslashes($Texto1);$ok=1;}

				echo " »» ".$Cabecalho." »» ";
				ob_flush();
   				flush();

				//Vamos inserir ou fazer update?
				if($ok)
				{
					$rs2->db_open($db_link,$DB_name,'Select * from '.$T2.' WHERE ID_PAI='.$id_pai.' AND '.$T2.'.Lang = "'.$sigla.'"');
					if(!$rs2->EOF())
					{
						echo "U";
						$sql="Update $T2 Set titulo='$Cabecalho',resumo='$Texto1',Data_modify='".date("Y-m-d")."' where ID_PAI=$id_pai and Lang='$sigla'";
					}
					else
					{
						echo "I";
						$sql="insert into $T2 (ID_PAI,titulo,resumo,Lang,Data_modify) values ($id_pai,'$Cabecalho','$Texto1','$sigla','".date("Y-m-d")."')";
					}
					$res=Runquery($db_link,$DB_name,$sql);
					if(!$res)echo db_error($db_link).$sql;
				}
				echo "<br>";
			}
			$i++;
		}

        $rs->db_next();
    }
    $rs->db_close();
    //*******************************************************
    //*******************************************************
echo	$T='modulos_imgs';
	$T2='modulos_imgs_lang';

	$rs->db_open($db_link,$DB_name,'Select '.$T.'.Data_modify as Data_modify1,'.$T2.'.* from '.$T.' INNER JOIN '.$T2.' ON '.$T2.'.bloco_img_ID_PAI='.$T.'.bloco_img_id WHERE  '.$T2.'.lang = "'.$lingua_website.'" AND '.$T2.'.Data_modify <= '.$T.'.Data_modify order by '.$T.'.bloco_img_id Asc');
    while(!$rs->EOF())
    {
		$id_pai=$rs->db_data['bloco_img_ID_PAI'];
		$Cabecalho_=$rs->db_data['bloco_img_titulo'];
		$bloco_img_txt_=$rs->db_data['bloco_img_txt'];
		$Data_modify1=$rs->db_data['Data_modify1'];

		$bloco_video_yt =$rs->db_data['bloco_video_yt'];
		$bloco_video_vimeo=$rs->db_data['bloco_video_vimeo'];
		$bloco_video_code =$rs->db_data['bloco_video_code'];
		//Agora vamos verificar se já foi traduzido:



		$i=0;
		while ($i<$t)
		{
			$sigla=$linguas[$i];

			if($Data_modify1=='0000-00-00')$rs2->db_open($db_link,$DB_name,'Select * from '.$T2.' WHERE bloco_img_ID_PAI='.$id_pai.' AND lang = "'.$sigla.'" AND Data_modify <> "0000-00-00"');
			else $rs2->db_open($db_link,$DB_name,'Select * from '.$T2.' WHERE bloco_img_ID_PAI='.$id_pai.' AND lang = "'.$sigla.'" AND Data_modify > "'.$Data_modify1.'"');

			echo $id_pai.' »» '.$sigla.' »» ';
			if(!$rs2->EOF())
			{
				//já esta atualizado
				$rs2->db_close();

				echo "já esta atualizado<br>";
			}
			else
			{
				$rs2->db_close();

				//Vamos traduzir
				echo "a traduzir pagina ".$Cabecalho_." ";


				$Cabecalho='';
				$Texto1='';

				if($Cabecalho_)	$Cabecalho=stripslashes($Cabecalho_);
				if($bloco_img_txt_)	$Texto1=stripslashes($bloco_img_txt_);

				if($Cabecalho)$Cabecalho=translate_now($Cabecalho, $lingua_website, $sigla);
				if($Texto1)$Texto1=translate_now($Texto1, $lingua_website, $sigla);

   				$ok=0;
				if($Cabecalho){$Cabecalho=addslashes($Cabecalho);$ok=1;}
				if($Texto1){$Texto1=addslashes($Texto1);$ok=1;}

				echo " »» ".$Cabecalho." »» ";
				ob_flush();
   				flush();

				//Vamos inserir ou fazer update?
				if($ok)
				{
					$rs2->db_open($db_link,$DB_name,'Select * from '.$T2.' WHERE bloco_img_ID_PAI='.$id_pai.' AND '.$T2.'.lang = "'.$sigla.'"');
					if(!$rs2->EOF())
					{
						echo "U";
						$sql="Update $T2 Set bloco_img_titulo='$Cabecalho',bloco_img_txt='$Texto1',bloco_video_yt='$bloco_video_yt',bloco_video_vimeo='$bloco_video_vimeo',bloco_video_code='$bloco_video_code',Data_modify='".date("Y-m-d")."' where bloco_img_ID_PAI=$id_pai and lang='$sigla'";
					}
					else
					{
						echo "I";
						$sql="insert into $T2 (bloco_img_ID_PAI,bloco_img_titulo,bloco_img_txt,bloco_video_yt,bloco_video_vimeo,bloco_video_code,lang,Data_modify) values ($id_pai,'$Cabecalho','$Texto1','$bloco_video_yt','$bloco_video_vimeo','$bloco_video_code','$sigla','".date("Y-m-d")."')";
					}
					$res=Runquery($db_link,$DB_name,$sql);
					if(!$res)echo db_error($db_link).$sql;
				}
				echo "<br>";
			}
			$i++;
		}

        $rs->db_next();
    }
    $rs->db_close();
    







?>