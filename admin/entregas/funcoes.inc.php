<?php

function gera_lista_grupos_users($blocos_users)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	
	$blocos_users2 = explode(';',$blocos_users);

	global $admin_id;
	$where = 'WHERE Grupo_ID=5';	
	$rs->db_open($db_link,$DB_name,'Select ID, Nome from logins '.$where);
	echo '<select name="listagrupos[]" id="listagrupos" multiple="multiple"  class="chzn-select">';
    while(!$rs->EOF())
    {
        echo '<option value="'.$rs->db_data['ID'].'"';
		if(in_array($rs->db_data['ID'],$blocos_users2)) echo ' selected ';
		echo '>';
		echo $rs->db_data['Nome'].'</option>';
        $rs->db_next();
    }
    $rs->db_close(1);
	echo '</select>';
}

function GET_BLOCOS_ATIVIDADES()
{
	$db_link = db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs = new DataObj;$rs2 = new DataObj; $return = '';
	$sql = '
			SELECT 
				b.bloco_id, b.bloco_ext, b.bloco_data, b.bloco_open, bl.bloco_titulo, bl.bloco_resumo, bl.bloco_texto
			FROM  
				blocos b
				INNER JOIN blocos_lang bl ON bl.bloco_id = b.bloco_id
			WHERE 
				bl.bloco_lang = "'.$GLOBALS['admin_lang'].'"
				AND b.bloco_categorias="73;"
				AND b.bloco_id_pai=0
			ORDER BY 
				b.bloco_ordem ASC, b.bloco_data DESC
			';
	$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
	if(!$rs->EOF())
	{		
		$a = 6;
		$a2 = 0;
		
		$return .= '<div class="dashboard">';
		$return .= '<ul>';
				
		while(!$rs->EOF())
		{
			
			$bloco_id = $rs->db_data['bloco_id'];
			$bloco_open = $rs->db_data['bloco_open'];
			$bloco_titulo = $rs->db_data['bloco_titulo'];
			$bloco_data = $rs->db_data['bloco_data'];
			$bloco_resumo = $rs->db_data['bloco_resumo'];
			$bloco_texto = $rs->db_data['bloco_texto'];
			$bloco_ext = $rs->db_data['bloco_ext'];
			
			
			$img_file = 'files/documentos/'.$bloco_ext;	
			$ext = pathinfo($img_file, PATHINFO_EXTENSION);
			$img_file_med = str_replace('.'.$ext, '_dash.'.$ext, $img_file);	
			
			if(file_exists($GLOBALS['site_shell_url'].$img_file)) $original = date("Y-m-d H:i:s",filemtime($GLOBALS['site_shell_url'].$img_file)); // data/hora em que foi modificada/criada
			if(file_exists($GLOBALS['site_shell_url'].$img_file_med)) $miniatura = date("Y-m-d H:i:s",filemtime($GLOBALS['site_shell_url'].$img_file_med)); //data/hora em que foi modificada/criada
			
			if((!file_exists($GLOBALS['site_shell_url'].$img_file_med) || $miniatura<$original) && file_exists($GLOBALS['site_shell_url'].$img_file)) 
			{					
				if(resize_image($GLOBALS['site_shell_url'].$img_file,100,0,true, $GLOBALS['site_shell_url'].$img_file_med)){myimagefilledpolygon($GLOBALS['site_shell_url'].$img_file_med, $GLOBALS['site_shell_url'].$img_file_med, '#e31d1a');}		
			}
			
			
			if($a==$a2)
			{
				$return .= '</ul></div><div class="dashboard"><ul>';
				$a2 = 0;
			}
			
			if($bloco_open > 0) $URL_Link = $GLOBALS['site_http_url'].'bl-'.$bloco_open.'-'. str_replace(' ','',limpa_special($bloco_titulo)).'-'.$GLOBALS['site_lang'];
			else $URL_Link = $GLOBALS['site_http_url'].'bl-'.$bloco_id.'-'. str_replace(' ','',limpa_special($bloco_titulo)).'-'.$GLOBALS['site_lang'];
			$URL_Link = str_replace($GLOBALS['site_http_url'],'',$URL_Link);
			
			$return .= '<li style="cursor:pointer" onclick="window.open(\'sa_blocos/pagina.php?bloco_id='.$bloco_id.'\',\'_self\')"><div style="height:100px"><img src="'.$GLOBALS['site_http_url'].$img_file_med.'" border="0" /></div>';
			$return .= '<div style="height:20px;display: table-cell;vertical-align:middle;text-align: center;width:130px">';
			$return .= ''.$bloco_titulo.'</div>';
			$return .= '<div class="clear"></div>';
			$return .= '</li>';
					
			$a2++;
			$rs->db_next();
		}
		$return .= '</ul></div>';
	}
	$rs->db_close(1);
	
	return $return;
}
function show_menupath($id_org,$table="pages",$pagina="pagina.php",$tipo=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;  $rs1=new DataObj;
	if($id_org!=0)
	{
		$sql="SELECT bloco_id, bloco_id_pai FROM $table WHERE bloco_id=$id_org";
		$rs->db_open($db_link,$DB_name,$sql); 
		$codigo=$rs->db_data["bloco_id"];
		$id_org1=$rs->db_data["bloco_id_pai"];
		$rs->db_close();
		$sql="SELECT bloco_titulo FROM $table"."_lang WHERE bloco_id=$codigo";
		$rs1->db_open($db_link,$DB_name,$sql); 
		$C=stripslashes($rs1->db_data["bloco_titulo"]);
		$C1=$C!="" ? $C : "[EDITAR]";
		$rs1->db_close();
	}
	if($codigo!=0)
	{
		$str.=show_menupath($id_org1,$table,$pagina,$tipo);
		$str.=" - <a href=\"$pagina"."?bloco_id=$codigo&do=".date('YmdHis')."\" class=\"brancoh\">$C1</a>";
	}
	return $str;  
}

function gera_lista_path($id_org=0,$T,$Tlang,$tipo='')
{		
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;	
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;
	$sql = 'SELECT * FROM blocos WHERE bloco_id_pai="0" ORDER BY bloco_ordem Asc';
	$rs->db_open($db_link,$DB_name,$sql);
    echo '<select name="'.TBID.'_pai" style="width:350px" class="chzn-select">';
	if($id_org==0) echo '<option value="0" selected>- nenhuma -</option>';
	else echo '<option value="0">- nenhuma -</option>';
    while(!$rs->EOF())
    {
	    echo '<option value="'.$rs->db_data['bloco_id'].'"';
		if($id_org==$rs->db_data["bloco_id"]) echo ' selected ';
        $rs2=new DataObj;
	    $hRes=$rs2->db_open($db_link,$DB_name,'Select * from blocos_lang where bloco_id='.$rs->db_data['bloco_id'].' and bloco_lang="'.$GLOBALS['admin_lang'].'"');   
		echo '>'.$rs2->db_data['bloco_titulo'].'</option>';
	    $rs2->db_close();
	    gera_lista_path2($rs->db_data['bloco_id'],$id_org,$T,$Tlang);
        $rs->db_next();
    }
    $rs->db_close(1);
    echo '</select>';
}
function gera_lista_path2($id_start=0,$id_org=0,$T,$Tlang,$c=5)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;$rs2=new DataObj;
	$rs->db_open($db_link,$DB_name,'Select * from blocos where bloco_id_pai="'.$id_start.'" ORDER BY bloco_ordem ASC');
    while(!$rs->EOF())
    {
        echo '<option value="'.$rs->db_data['bloco_id'].'"';
		if($id_org==$rs->db_data['bloco_id']) echo ' selected ';
	    $hRes=$rs2->db_open($db_link,$DB_name,'Select * from blocos_lang where bloco_id="'.$rs->db_data['bloco_id'].'" AND bloco_lang="'.$GLOBALS['admin_lang'].'"');
		echo '>';
		$i=0;while($i<$c){echo '- ';$i++;}
		echo $rs2->db_data['bloco_titulo'].'</option>';
	    $rs2->db_close();
	    gera_lista_path2($rs->db_data['bloco_id'],$id_org,$T,$Tlang,$c+5);
        $rs->db_next();
    }
    $rs->db_close();	
}

function gera_lista_path_open($id_org=0,$T,$Tlang,$tipo='')
{		
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;	
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;
	$sql = 'SELECT * FROM blocos WHERE bloco_id_pai="'.$_GET['bloco_id'].'" ORDER BY bloco_ordem Asc';
	$rs->db_open($db_link,$DB_name,$sql);
    echo '<select name="bloco_open" style="width:350px" class="chzn-select">';
	if($id_org==0) echo '<option value="0" selected>- Esta -</option>';
	else echo '<option value="0">- Esta -</option>';
    while(!$rs->EOF())
    {
	    if(isset($_GET['bloco_id']) && $_GET['bloco_id'] == $rs->db_data['bloco_id']) $des = 'disabled="disabled"';
		else $des = '';
		
		echo '<option '.$des.' value="'.$rs->db_data['bloco_id'].'"';
		if($id_org==$rs->db_data["bloco_id"]) echo ' selected ';
        $rs2=new DataObj;
	    $hRes=$rs2->db_open($db_link,$DB_name,'Select * from blocos_lang where bloco_id='.$rs->db_data['bloco_id'].' and bloco_lang="'.$GLOBALS['admin_lang'].'"');   
		echo '>'.$rs2->db_data['bloco_titulo'].'</option>';
	    $rs2->db_close();
		gera_lista_path_open2($rs->db_data['bloco_id'],$id_org,$T,$Tlang);
        $rs->db_next();
    }
    $rs->db_close(1);
    echo '</select>';
}
function gera_lista_path_open2($id_start=0,$id_org=0,$T,$Tlang,$c=5)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;$rs2=new DataObj;
	$rs->db_open($db_link,$DB_name,'Select * from blocos where bloco_id_pai="'.$id_start.'" ORDER BY bloco_ordem ASC');
    while(!$rs->EOF())
    {
       if(isset($_GET['bloco_id']) && $_GET['bloco_id'] == $rs->db_data['bloco_id']) $des = 'disabled="disabled"';
		else $des = '';
		
		echo '<option '.$des.' value="'.$rs->db_data['bloco_id'].'"';
		if($id_org==$rs->db_data['bloco_id']) echo ' selected ';
	    $hRes=$rs2->db_open($db_link,$DB_name,'Select * from blocos_lang where bloco_id="'.$rs->db_data['bloco_id'].'" AND bloco_lang="'.$GLOBALS['admin_lang'].'"');
		echo '>';
		$i=0;while($i<$c){echo '- ';$i++;}
		echo $rs2->db_data['bloco_titulo'].'</option>';
	    $rs2->db_close();
	    gera_lista_path_open2($rs->db_data['bloco_id'],$id_org,$T,$Tlang,$c+5);
        $rs->db_next();
    }
    $rs->db_close();	
}

function getBrands($arr_brd)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;

	$result=array();

	$sql = '
			SELECT 
				b.ID,
				b.img_ext,
				bo.* 
			FROM  
				brands b
				INNER JOIN brands_lang bo ON bo.ID_PAI=b.ID			
			ORDER BY 
				b.Ordem ASC
				';
	$rs->db_open($db_link,$GLOBALS['DB_name'], $sql);	
	while(!$rs->EOF())
	{
		if(!in_array($rs->db_data['ID'], $arr_brd))
		{
			$brand_id=$rs->db_data['ID'];
			$brands=get_produtc_brand($brand_id);
			$brands['brand_id']=$brand_id;
			$result[]=$brands;
		}
		$rs->db_next();
	}
	$rs->db_close();

	return $result;
}
function gera_lista_grupos($arr_familias, $arr_brd)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj = new DataObj;
	
	

    echo '<select id="blocos_users" name="blocos_users[]" class="box chzn-select" multiple="multiple" style="font-size:13px;width:300px">';
    for($a=0; $a<count($arr_familias); $a++)
	{
		echo '<option value="'.$arr_familias[$a]['brand_id'].'"';		
		if(in_array($arr_familias[$a]['brand_id'], $arr_brd)) echo ' selected="selected">'.$arr_familias[$a]['marca_nome'].'</option>';
		else echo '>'.$arr_familias[$a]['marca_nome'].'</option>';
    }
    echo '</select>';
}
function gera_lista_brands_cat($arr_familias, $arr_brd)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj = new DataObj;

    echo '<select id="listademarcas" name="listademarcas[]" class="box chzn-select" style="font-size:13px;width:300px">';
    for($a=0; $a<count($arr_familias); $a++)
	{
		echo '<option value="'.$arr_familias[$a]['brand_id'].'"';		
		if(in_array($arr_familias[$a]['brand_id'], $arr_brd)) echo ' selected="selected">'.$arr_familias[$a]['marca_nome'].'</option>';
		else echo '>'.$arr_familias[$a]['marca_nome'].'</option>';
    }
    echo '</select>';
}

function gera_lista_brands_list($arr_familias, $arr_brd)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj = new DataObj;

    echo '<select id="listademarcasx" name="listademarcasx" class="chzn-select" style="font-size:13px;width:150px">';
	echo '<option value="">TODOS</option>';	
    for($a=0; $a<count($arr_familias); $a++)
	{
		echo '<option value="'.$arr_familias[$a]['brand_id'].'"';		
		if(in_array($arr_familias[$a]['brand_id'], $arr_brd)) echo ' selected="selected">'.$arr_familias[$a]['marca_nome'].'</option>';
		else echo '>'.$arr_familias[$a]['marca_nome'].'</option>';
    }
    echo '</select>';
}

function gera_brands_name($marca_id=0)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;

   $sql = '
			SELECT 
				bo.Nome 
			FROM  
				brands_lang bo
			WHERE
				ID_PAI="'.$marca_id.'"
				';
	$rs->db_open($db_link,$GLOBALS['DB_name'], $sql);	
	if(!$rs->EOF()) $brand_nome = $rs->db_data['Nome'];
	$rs->db_close();

	return $brand_nome;
}
function submeteDados()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);	

	$modulo_id = (isset($_POST[TBID]) && $_POST[TBID] > 0) ? $_POST[TBID] : 0;
	$modulo_duplicar = (isset($_POST['duplicar']) && $_POST['duplicar'] > 0) ? 1 : 0;
	$modulo_acao = (isset($_POST[TBID]) && $_POST[TBID] > 0) ? 'update' : 'insere';
	$CMPS_MODULO_DADOS = $GLOBALS['CMPS_MODULO_DADOS'];
	$tipo=$_GET['Tipo'];
	if($modulo_acao=='insere')
	{
		$sql = 'INSERT INTO '.TBMODULO;
		$sql .= '(';
		$i = 0; foreach($CMPS_MODULO_DADOS as $campos){ if($campos != TBID){ if($i > 0){ $sql .= ', ';} $sql .= $campos; $i++;} } // ARRAY DOS CAMPOS
		$sql .= ')VALUES(';
		$i = 0; 
		foreach($CMPS_MODULO_DADOS as $campos) // ARRAY PARA $_POST DOS CAMPOS
		{
			if($campos != TBID)
			{ 
				if($i>0){$sql .= ', ';}			
				if(substr_count($campos,'_ext'))
				{
					$nome_ficheiro = submete_ficheiro($campos, '');
					$sql .= '"'.$nome_ficheiro.'"';
				}
				else
				{
					$sql .= '"'.addslashes($_POST[$campos]).'"';
				}
				$i++;
			} 
		}		
		$sql .= '		
				)
				';			
	}
	elseif($modulo_acao=='update')
	{
		$sql = 'UPDATE '.TBMODULO.' SET ';
		$i = 0; 
		foreach($CMPS_MODULO_DADOS as $campos) // ARRAY DE CAMPOS E $_POST DOS CAMPOS
		{
			if($campos != TBID)
			{
				if(substr_count($campos,'_ext'))
				{
					$nome_ficheiro = submete_ficheiro($campos, '');
					if($i > 0 && $nome_ficheiro){$sql .= ', ';} 
					if($nome_ficheiro) $sql .= $campos.'="'.$nome_ficheiro.'"';
				}
				else
				{ 
					if($i > 0){$sql .= ', ';} 
					$sql .= $campos.'="'.addslashes($_POST[$campos]).'"';
				}
				$i++;
			}
		}
		$sql .= ' WHERE '.TBID.'='.$_POST[TBID];
	}
	//echo $sql;
	$res = Runquery($db_link, $DB_name, $sql);
	// echo '<pre style="color:#FFF">'.$sql.'</pre><br />';
	
	if($modulo_acao=='insere') $id_acao = db_get_last_insert_id($db_link);
	else $id_acao = $modulo_id;	
	
	if($res) submeteDadosLinguas($id_acao);
		
	return array('resultado' => $res, 'acao' => $modulo_acao, 'id_acao' => $id_acao);
}

function submeteDados2()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);	

	$modulo_id = (isset($_POST[TBID]) && $_POST[TBID] > 0) ? $_POST[TBID] : 0;
	$modulo_duplicar = (isset($_POST['duplicar']) && $_POST['duplicar'] > 0) ? 1 : 0;
	$modulo_acao = (isset($_POST[TBID]) && $_POST[TBID] > 0) ? 'update' : 'insere';
	$CMPS_MODULO_DADOS = $GLOBALS['CMPS_MODULO_DADOS'];
	$tipo=$_GET['Tipo'];
	if($modulo_acao=='insere')
	{
		$sql = 'INSERT INTO '.TBMODULO;
		$sql .= '(';
		$i = 0; foreach($CMPS_MODULO_DADOS as $campos){ if($campos != TBID){ if($i > 0){ $sql .= ', ';} $sql .= $campos; $i++;} } // ARRAY DOS CAMPOS
		$sql .= ')VALUES(';
		$i = 0; 
		foreach($CMPS_MODULO_DADOS as $campos) // ARRAY PARA $_POST DOS CAMPOS
		{
			if($campos != TBID)
			{ 
				if($i>0){$sql .= ', ';}			
				if(substr_count($campos,'_ext'))
				{
					$nome_ficheiro = submete_ficheiro($campos, '');
					$sql .= '"'.$nome_ficheiro.'"';
				}
				else
				{
					$sql .= '"'.addslashes($_POST[$campos]).'"';
				}
				$i++;
			} 
		}		
		$sql .= '		
				)
				';			
	}
	
	elseif($modulo_acao=='update')
	{
		$sql = 'UPDATE '.TBMODULO.' SET ';
		$i = 0; 
		foreach($CMPS_MODULO_DADOS as $campos) // ARRAY DE CAMPOS E $_POST DOS CAMPOS
		{
			if($campos != TBID)
			{
				if(substr_count($campos,'_ext'))
				{
					$nome_ficheiro = submete_ficheiro($campos, '');
					if($i > 0 && $nome_ficheiro){$sql .= ', ';} 
					if($nome_ficheiro) $sql .= $campos.'="'.$nome_ficheiro.'"';
				}
				else
				{ 
					if($i > 0){$sql .= ', ';} 
					$sql .= $campos.'="'.addslashes($_POST[$campos]).'"';
				}
				$i++;
			}
		}
		$sql .= ' WHERE '.TBID.'='.$_POST[TBID];
	}
	$res = Runquery($db_link, $DB_name, $sql);
	// echo '<pre style="color:#FFF">'.$sql.'</pre><br />';
	
	if($modulo_acao=='insere') $id_acao = db_get_last_insert_id($db_link);
	else $id_acao = $modulo_id;	
	
	if($res) submeteDadosLinguas($id_acao);
		
	return array('resultado' => $res, 'acao' => $modulo_acao, 'id_acao' => $id_acao);
}



function submeteDadosLinguas($modulo_id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);	

	// $modulo_id = (isset($_POST[TBID]) && $_POST[TBID] > 0) ? $_POST[TBID] : 0;
	$modulo_duplicar = (isset($_POST['duplicar']) && $_POST['duplicar'] > 0) ? 1 : 0;
	$rs = new DataObj;
	$sql='SELECT '.TBID.'_PAI FROM '.TBMODULO_LANG.' WHERE '.TBID.'_PAI='.$modulo_id.' AND Lang="'.$GLOBALS['admin_lang'].'"';
	// echo $sql.'<br />';
	$rs->db_open($db_link, $DB_name, $sql);	
	if(!$rs->EOF()) $modulo_acao = 'update'; else $modulo_acao = 'insere';
	$rs->db_close();	
	
	$CMPS_MODULO_DADOS = $GLOBALS['CMPS_MODULO_DADOS_LANG'];
		
	if($modulo_acao=='insere')
	{
		$sql = 'INSERT INTO '.TBMODULO_LANG;
		$sql .= '(';
		$i = 0; foreach($CMPS_MODULO_DADOS as $campos){ if($campos != 'ID'){ if($i > 0){ $sql .= ', ';} $sql .= $campos; $i++;} } // ARRAY DOS CAMPOS
		$sql .= ')VALUES(';
		$i = 0; 
		foreach($CMPS_MODULO_DADOS as $campos) // ARRAY PARA $_POST DOS CAMPOS
		{
			if($campos != 'ID')
			{ 
				if($i>0){$sql .= ', ';}			
				if(substr_count($campos,'_ext'))
				{
					$nome_ficheiro = submete_ficheiro($campos, '');
					$sql .= '"'.$nome_ficheiro.'"';
				}
				elseif($campos=='ID_PAI')
				{
					$sql .= '"'.$modulo_id.'"';
				}
				else
				{
					$sql .= '"'.addslashes($_POST[$campos]).'"';
				}
				$i++;
			} 
		}		
		$sql .= '		
				)
				';
	}
	elseif($modulo_acao=='update')
	{		
		$sql = 'UPDATE '.TBMODULO_LANG.' SET ';
		$i = 0; 
		foreach($CMPS_MODULO_DADOS as $campos) // ARRAY DE CAMPOS E $_POST DOS CAMPOS
		{
			if($campos != TBID.'_PAI' && $campos != 'Lang' && $campos != 'ID')
			{
				if(substr_count($campos,'_ext'))
				{
					$nome_ficheiro = submete_ficheiro($campos, '');
					if($i > 0 && $nome_ficheiro){$sql .= ', ';} 
					if($nome_ficheiro) $sql .= $campos.'="'.$nome_ficheiro.'"';
				}
				elseif($campos=='ID_PAI')
				{
					$sql .= $campos.'="'.$modulo_id.'"';
				}
				else
				{ 
					if($i > 0){$sql .= ', ';} 
					$sql .= $campos.'="'.addslashes($_POST[$campos]).'"';
				}
				$i++;
			}
		}
		$sql .= 'WHERE '.TBID.'_PAI='.$_POST[TBID].' AND Lang="'.$GLOBALS['admin_lang'].'"';
	}
	echo $sql;
	$res = Runquery($db_link, $DB_name, $sql);
	// echo '<pre style="color:#FFF">'.$sql.'</pre><br />';
}

function submeteimagens($bloco_id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);	
	$rs = new DataObj;
	
	$modulo_duplicar = (isset($_POST['duplicar']) && $_POST['duplicar'] > 0) ? 1 : 0;
	if($modulo_duplicar == 0)
	{
		$modulo_id = $bloco_id;	
		$CMPS_MODULO = $GLOBALS['CMPS_MODULO_IMGS'];
		
		// echo '<pre style="color:#FFF">';
		$query = '';
		
		foreach($_POST['RENOVACAO_ID'] as $field_num2)
		{
			$codis = explode(';', $field_num2);
			$field_num = $codis[0];
			$field_id = $codis[1];
			
			$modulo_acao = ($field_id > 0) ? 'update' : 'insere';
		
			if($modulo_acao=='insere')
			{	
				$sql = 'INSERT INTO '.TBIMAGENS;
				$sql .= '(';
				$i=0; foreach($CMPS_MODULO as $campos){ if($campos != TBIMGID){ if($i>0){$sql .= ', ';} $sql .= $campos; $i++;} } // ARRAY DOS CAMPOS
				$sql .= ')VALUES(';
				$i=0; 
				foreach($CMPS_MODULO as $campos) // ARRAY PARA $_POST DOS CAMPOS
				{
					if($campos != TBIMGID)
					{ 
						if($i>0){$sql .= ', ';} 
					
						if(substr_count($campos.$field_num,'_ext'))
						{
							$nome_ficheiro = submete_ficheiro($campos.$field_num, '');
							$sql .= '"'.$nome_ficheiro.'"';
						}
						elseif(substr_count($campos,'_rel'))
						{
							$sql .= '"'.$field_num.'"';
						}
						elseif(substr_count($campos,'bloco_id'))
						{
							$sql .= '"'.$modulo_id.'"';
						}
						else
						{
							$sql .= '"'.addslashes($_POST[$campos.$field_num]).'"';
						}
						$i++;
					}
				}		
				$sql .= ')';
			}
			elseif($modulo_acao=='update')
			{		
				$sql = 'UPDATE '.TBIMAGENS.' SET ';
				$i=0; 
				foreach($CMPS_MODULO as $campos) // ARRAY DE CAMPOS E $_POST DOS CAMPOS
				{
					if($campos != TBIMGID)
					{		
						if(substr_count($campos.$field_num,'_ext'))
						{
							$nome_ficheiro = submete_ficheiro($campos.$field_num, '');
							if($i>0 && $nome_ficheiro){$sql .= ', ';} 
							if($nome_ficheiro) $sql .= $campos.'="'.$nome_ficheiro.'"';
						}
						elseif(substr_count($campos,'_rel'))
						{
							if($i>0){$sql .= ', ';}
							$sql .= $campos.'="'.$field_num.'"';
						}
						elseif($campos=='bloco_id')
						{
							if($i>0){$sql .= ', ';}
							$sql .= $campos.'="'.$modulo_id.'"';
						}
						else
						{
							if($i>0){$sql .= ', ';} 
							$sql .= $campos.'="'.addslashes($_POST[$campos.$field_num]).'"';
						}
						$i++;
					}
				}
				$sql .= ' WHERE '.TBIMGID.'='.$field_id;
			}
			$res = Runquery($db_link, $DB_name, $sql);
			// echo $sql.'<br />';
		}
	}
	// echo '</pre>';
}
function submeteAverbamentos($id_marca)
{
	// echo '<pre style="color:#FFF">';
	tabs_submete_av('ENDERECO_ID');
	tabs_submete_av('NOME_ID');
	tabs_submete_av('TRANSMISSAO_ID');
	tabs_submete_av('FUSAO_ID');
	tabs_submete_av('LICENSA_ID');
	// echo '</pre>';	
}

function submeteOposicoes($id_marca)
{
	echo '<pre style="color:#FFF">';
	tabs_submete_op('OPOSICAO_ID');
	tabs_submete_op('RESPOSTA_ID');
	echo '</pre>';	
}

function tabs_submete_op($tab)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);	
	$rs = new DataObj;
	
	$modulo_id = (isset($_POST[TBID]) && $_POST[TBID] > 0) ? $_POST[TBID] : 0;
	$CMPS_MODULO = $GLOBALS['CMPS_OPOSICAO'];
	
	foreach($_POST[$tab] as $field_num2)
	{
		$codis = explode(';', $field_num2);
		$field_num = $codis[0];
		$field_id = $codis[1];
		$field_bloco = $codis[2];		
		$modulo_acao = ($field_id > 0) ? 'update' : 'insere';

		if($modulo_acao=='insere')
		{	
			$sql = 'INSERT INTO '.TBOPOSICOES;
			$sql .= '(';
			$i=0; foreach($CMPS_MODULO as $campos){ if($campos != TBOPOSICAOID){ if($i>0){$sql .= ', ';} $sql .= $campos; $i++;} } // ARRAY DOS CAMPOS
			$sql .= ')VALUES(';
			$i=0;
			foreach($CMPS_MODULO as $campos) // ARRAY PARA $_POST DOS CAMPOS
			{
				if($campos != TBOPOSICAOID)
				{
					if($i>0){$sql .= ', ';}
				
					if(substr_count($campos.$field_num,'_EXT'))
					{
						$nome_ficheiro = submete_ficheiro($field_bloco.'_'.$campos.$field_num, '');
						$sql .= '"'.$nome_ficheiro.'"';
					}
					elseif(substr_count($campos,'_REL'))
					{
						$sql .= '"'.$field_num.'"';
					}
					elseif(substr_count($campos,'objeto_nome'))
					{
						$sql .= '"'.MODULO.'"';
					}
					elseif(substr_count($campos,'objeto_id'))
					{
						$sql .= '"'.$modulo_id.'"';
					}
					elseif(substr_count($campos,'objeto_bloco'))
					{
						$sql .= '"'.$field_bloco.'"';
					}
					else
					{
						$sql .= '"'.addslashes($_POST[$field_bloco.'_'.$campos.$field_num]).'"';
					}
					$i++;
				}
			}		
			$sql .= ')';
		}
		elseif($modulo_acao=='update')
		{		
			$sql = 'UPDATE '.TBOPOSICOES.' SET ';
			$i=0; 
			foreach($CMPS_MODULO as $campos) // ARRAY DE CAMPOS E $_POST DOS CAMPOS
			{
				if($campos != TBOPOSICAOID && $campos != 'objeto_bloco')
				{
					if(substr_count($campos.$field_num,'_EXT'))
					{
						$nome_ficheiro = submete_ficheiro($field_bloco.'_'.$campos.$field_num, '');
						if($i>0 && $nome_ficheiro){$sql .= ', ';} 
						if($nome_ficheiro) $sql .= $campos.'="'.$nome_ficheiro.'"';
					}
					elseif(substr_count($campos,'_REL'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.$field_num.'"';
					}
					elseif(substr_count($campos,'objeto_nome'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.MODULO.'"';
					}
					elseif(substr_count($campos,'objeto_id'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.$modulo_id.'"';
					}
					else
					{
						if($i>0){$sql .= ', ';} 
						$sql .= $campos.'="'.addslashes($_POST[$field_bloco.'_'.$campos.$field_num]).'"';
					}
					$i++;
				} 
			}
			$sql .= ' WHERE '.TBOPOSICAOID.'='.$field_id.' AND objeto_bloco="'.$field_bloco.'"';
		}
		// echo $sql.'<br />';
		$res = Runquery($db_link, $DB_name, $sql);
	}
}

function submeteReclamacoes($id_marca)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);	
	$rs = new DataObj;
	
	$modulo_id = (isset($_POST[TBID]) && $_POST[TBID] > 0) ? $_POST[TBID] : 0;
	$CMPS_MODULO = $GLOBALS['CMPS_RECLAMACAO'];
	// echo '<pre style="color:#FFF">';
	foreach($_POST['RECLAMACAO_ID'] as $field_num2)
	{
		$codis = explode(';', $field_num2);
		$field_num = $codis[0];
		$field_id = $codis[1];
		$field_bloco = $codis[2];		
		$modulo_acao = ($field_id > 0) ? 'update' : 'insere';
		
		if($modulo_acao=='insere')
		{
			$sql = 'INSERT INTO '.TBRECLAMACOES;
			$sql .= '(';
			$i=0; foreach($CMPS_MODULO as $campos){ if($campos != TBRECLAMACAOID){ if($i>0){$sql .= ', ';} $sql .= $campos; $i++;} } // ARRAY DOS CAMPOS
			$sql .= ')VALUES(';
			$i=0;
			foreach($CMPS_MODULO as $campos) // ARRAY PARA $_POST DOS CAMPOS
			{
				if($campos != TBRECLAMACAOID)
				{
					if($i>0){$sql .= ', ';}				
					if(substr_count($campos.$field_num,'_EXT'))
					{
						$nome_ficheiro = submete_ficheiro($field_bloco.'_'.$campos.$field_num, '');
						$sql .= '"'.$nome_ficheiro.'"';
					}
					elseif(substr_count($campos,'_REL'))
					{
						$sql .= '"'.$field_num.'"';
					}
					elseif(substr_count($campos,'objeto_nome'))
					{
						$sql .= '"'.MODULO.'"';
					}
					elseif(substr_count($campos,'objeto_id'))
					{
						$sql .= '"'.$modulo_id.'"';
					}
					elseif(substr_count($campos,'objeto_bloco'))
					{
						$sql .= '"'.$field_bloco.'"';
					}
					else
					{
						$sql .= '"'.addslashes($_POST[$field_bloco.'_'.$campos.$field_num]).'"';
					}
					$i++;
				}
			}
			$sql .= ')';
		}
		elseif($modulo_acao=='update')
		{		
			$sql = 'UPDATE '.TBRECLAMACOES.' SET ';
			$i=0; 
			foreach($CMPS_MODULO as $campos) // ARRAY DE CAMPOS E $_POST DOS CAMPOS
			{
				if($campos != TBRECLAMACAOID && $campos != 'objeto_bloco')
				{
					if(substr_count($campos.$field_num,'_EXT'))
					{
						$nome_ficheiro = submete_ficheiro($field_bloco.'_'.$campos.$field_num, '');
						if($i>0 && $nome_ficheiro){$sql .= ', ';} 
						if($nome_ficheiro) $sql .= $campos.'="'.$nome_ficheiro.'"';
					}
					elseif(substr_count($campos,'_REL'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.$field_num.'"';
					}
					elseif(substr_count($campos,'objeto_nome'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.MODULO.'"';
					}
					elseif(substr_count($campos,'objeto_id'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.$modulo_id.'"';
					}
					else
					{
						if($i>0){$sql .= ', ';} 
						$sql .= $campos.'="'.addslashes($_POST[$field_bloco.'_'.$campos.$field_num]).'"';
					}
					$i++;
				} 
			}
			$sql .= ' WHERE '.TBRECLAMACAOID.'='.$field_id;			
		}
		// echo $sql.'<br />';
		// echo '</pre>';
		$res = Runquery($db_link, $DB_name, $sql);
	}
}

function submeteRecursos($id_marca)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);	
	$rs = new DataObj;
	
	$modulo_id = (isset($_POST[TBID]) && $_POST[TBID] > 0) ? $_POST[TBID] : 0;
	$CMPS_MODULO = $GLOBALS['CMPS_RECURSOS'];
	// echo '<pre style="color:#FFF">';
	foreach($_POST['RECURSO_ID'] as $field_num2)
	{
		$codis = explode(';', $field_num2);
		$field_num = $codis[0];
		$field_id = $codis[1];
		$field_bloco = $codis[2];		
		$modulo_acao = ($field_id > 0) ? 'update' : 'insere';
		
		if($modulo_acao=='insere')
		{
			$sql = 'INSERT INTO '.TBRECURSOS;
			$sql .= '(';
			$i=0; foreach($CMPS_MODULO as $campos){ if($campos != TBRECURSOID){ if($i>0){$sql .= ', ';} $sql .= $campos; $i++;} } // ARRAY DOS CAMPOS
			$sql .= ')VALUES(';
			$i=0;
			foreach($CMPS_MODULO as $campos) // ARRAY PARA $_POST DOS CAMPOS
			{
				if($campos != TBRECURSOID)
				{
					if($i>0){$sql .= ', ';}				
					if(substr_count($campos.$field_num,'_EXT'))
					{
						$nome_ficheiro = submete_ficheiro($field_bloco.'_'.$campos.$field_num, '');
						$sql .= '"'.$nome_ficheiro.'"';
					}
					elseif(substr_count($campos,'_REL'))
					{
						$sql .= '"'.$field_num.'"';
					}
					elseif(substr_count($campos,'objeto_nome'))
					{
						$sql .= '"'.MODULO.'"';
					}
					elseif(substr_count($campos,'objeto_id'))
					{
						$sql .= '"'.$modulo_id.'"';
					}
					elseif(substr_count($campos,'objeto_bloco'))
					{
						$sql .= '"'.$field_bloco.'"';
					}
					else
					{
						$sql .= '"'.addslashes($_POST[$field_bloco.'_'.$campos.$field_num]).'"';
					}
					$i++;
				}
			}
			$sql .= ')';
		}
		elseif($modulo_acao=='update')
		{		
			$sql = 'UPDATE '.TBRECURSOS.' SET ';
			$i=0; 
			foreach($CMPS_MODULO as $campos) // ARRAY DE CAMPOS E $_POST DOS CAMPOS
			{
				if($campos != TBRECURSOID && $campos != 'objeto_bloco')
				{
					if(substr_count($campos.$field_num,'_EXT'))
					{
						$nome_ficheiro = submete_ficheiro($field_bloco.'_'.$campos.$field_num, '');
						if($i>0 && $nome_ficheiro){$sql .= ', ';} 
						if($nome_ficheiro) $sql .= $campos.'="'.$nome_ficheiro.'"';
					}
					elseif(substr_count($campos,'_REL'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.$field_num.'"';
					}
					elseif(substr_count($campos,'objeto_nome'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.MODULO.'"';
					}
					elseif(substr_count($campos,'objeto_id'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.$modulo_id.'"';
					}
					else
					{
						if($i>0){$sql .= ', ';} 
						$sql .= $campos.'="'.addslashes($_POST[$field_bloco.'_'.$campos.$field_num]).'"';
					}
					$i++;
				} 
			}
			$sql .= ' WHERE '.TBRECURSOID.'='.$field_id;			
		}
		// echo $sql.'<br />';
		// echo '</pre>';
		$res = Runquery($db_link, $DB_name, $sql);
	}
}

function submete_ficheiro($file_name, $nome_ficheiro_old)
{
	$img_path = '../../files/documentos/';
	$Img_peq["name"] = $_FILES[$file_name]['name'];
	$Img_peq["temp"] = $_FILES[$file_name]['tmp_name']; 
	
	$nome_ficheiro = '';
	$nome_ficheiro = $nome_ficheiro_old;
	if($Img_peq["name"]!="")
	{ 
		$pos = strrpos($Img_peq['name'],"."); 
		$img_ext = substr($Img_peq['name'], $pos+1, strlen($Img_peq['name'])+1-$pos);
		if(file_exists($img_path.$nome_ficheiro_old) && !empty($nome_ficheiro_old))
		{		
			unlink($img_path.$nome_ficheiro_old);
		}		
		$token = date('YmdHis').bin2hex(openssl_random_pseudo_bytes(5));	
		$nome_ficheiro = $token.".".$img_ext;
	
		copia_segura($Img_peq["temp"], $img_path.$nome_ficheiro);
	}
	
	return $nome_ficheiro;
}

function show_page_buttons()
{
	global $converter,$task_button,$ID,$ID_ORG,$Estado,$T;
	?>
	<div class="clearboth"></div>
	<div id="slidebottom">
    <table width="100%" border=0 cellspacing=0 cellpadding=0>
    <tr>
    	<td width="5px"></td>
    	<td align="left" valign="middle" class="t1"><?php
		
				echo '<input class="botao" type="submit" name="Submit" value="'.$task_button.'">';
				if($task_form=="insere" || $task_form=="apaga" || $task_form=="duplicar")
				{
					?><input class="botao" type="button" name="cancelar" value="<?php echo write_lang('key_cancelar','Cancelar')?>" onMouseUp="window.open('list.php','_self')" /><? 
				}
				if($task_form=="altera"){?><input class="botao" type="button" name="Apagar" value="<?php echo write_lang('key_apagar','Apagar')?>" onMouseUp="window.open('pagina_trata.php?ID=<?php echo $ID?>&task_form=apaga','_self')" /><? }
			?>
            <input class="botao" type="button" name="cancelar" value="<?php echo write_lang('key_cancelar','Cancelar')?>" onMouseUp="window.open('list.php','_self')" />
		</td>
     </tr>
	</table>
	</div>	
<?php
}
function show_fast_tools()
{
	global $ID, $admin_id,$BOTOES_M, $BOTOES_L;
	?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0>
	<tr> 
	<td height="35px">&nbsp;</td>
	</tr>
    </table>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr> 
	<td class="titulo2" align="center" height="20">FERRAMENTAS</td>
	</tr>
	<?php 
	if(isset($BOTOES_L) && is_array($BOTOES_L))
	{
		$a=1;
		foreach($BOTOES_L as $key => $value)
		{
			echo $a;
			if($a==2 && isset($_GET[TBID]) && $_GET[TBID] > 0)
			{
			?>
				<tr>
				<td class="preto" align="center"><input class="botao" type="button" style="width:160px;height:20px" value="<?php echo $key?>" onclick="window.open('<?php echo $value?>','_self')" /></td>
				</tr>
			<?php 
			}
			elseif($a!=2)
			{
			?>
				<tr>
				<td class="preto" align="center"><input class="botao" type="button" style="width:160px;height:20px" value="<?php echo $key?>" onclick="window.open('<?php echo $value?>','_self')" /></td>
				</tr>
			<?php 
			}
			
			$a++;
		}
	}
	if(isset($BOTOES_M) && is_array($BOTOES_M))
	{
		$a=1;
		foreach($BOTOES_M as $key => $value)
		{
			echo $a;
			if($a==2 && isset($_GET[TBID]) && $_GET[TBID] > 0)
			{
			?>
				<tr>
				<td class="preto" align="center"><input class="botao" type="button" style="width:160px;height:20px" value="<?php echo $key?>" onclick="window.open('<?php echo $value?>','_self')" /></td>
				</tr>
			<?php 
			}
			elseif($a!=2)
			{
			?>
				<tr>
				<td class="preto" align="center"><input class="botao" type="button" style="width:160px;height:20px" value="<?php echo $key?>" onclick="window.open('<?php echo $value?>','_self')" /></td>
				</tr>
			<?php 
			}
			
			$a++;
		}
	}
	?>
	</table>
<?php }
function show_page_tools($filter=0)
{
	global $tipos; ?>
	<script>
	function checkFields(){
		return true;
	}
	</script>
	<table width="100%" height="50px" border=0 cellspacing=0 cellpadding=0 class="p1">
		<tr>
			<td width="5%" align="center">&nbsp;</td>
		    <td width="95%" valign="middle" style="font-weight:bold;color:#FFF"><?php echo MODULO?></td>
		</tr>
	</table>
	
<?php }
function show_page_bottom_tools()
{?>
<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
<table width="100%" style="background:#FFF" border="0" cellspacing="0" cellpadding="0">
<tr>
<td width="150" height="35" align="right" style="font-weight:bold">Para os Selecionados:&nbsp;</td>
<td height="35" width="275"><select name="ACCOES" id="ACCOES" class="box">
<option value="">-</option>
<?php if(isset($GLOBALS['BOTOES_B']) && !empty($GLOBALS['BOTOES_B'])){
foreach($GLOBALS['BOTOES_B'] as $value => $key){
echo '<option value="'.$key.'">'.$value.'</option>';
}}?>
</select></td>
<td  height="35" align="left" style="font-weight:bold"><img src="img/reload.png" onclick="$('#lista_paginas').submit();" style="cursor:pointer" /></td>
</tr>
</table>	
<?php
}

function acoes_selecionados()
{
	if(isset($_POST['ACCOES']))
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		if($_POST['ACCOES']==1) // REMOVER REGISTO
		{
			foreach($_POST as $varName=> $value) 
			{
				$str_id = substr($varName,strpos($varName,"_") + 1);
				if($str_id > 0 && substr_count($varName,"chk_") > 0)
				{
					$sql = 'DELETE FROM '.TBMODULO.' WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					$sql = 'DELETE FROM '.TBMODULO_lang.' WHERE '.TBID.'_PAI='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					// $sql = 'DELETE FROM '.TBIMAGENS.' WHERE '.TBID.'='.$str_id;
					// $res = Runquery($db_link,$DB_name,$sql);
					if(!$res) echo db_error($db_link) . $sql . '<br>';
				}
			}
		}
		elseif($_POST['ACCOES']==3) // ATIVAR REGISTOS
		{
			foreach($_POST as $varName=> $value) 
			{
				$str_id = substr($varName,strpos($varName,"_") + 1);
				if($str_id > 0 && substr_count($varName,"chk_") > 0)
				{
					$sql = 'UPDATE '.TBMODULO.' SET Visivel=1 WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					if(!$res) echo db_error($db_link) . $sql . '<br>';
				}
			}
		}
		elseif($_POST['ACCOES']==2) // DESATIVAR REGISTOS
		{
			foreach($_POST as $varName=> $value) 
			{
				$str_id = substr($varName,strpos($varName,"_") + 1);
				if($str_id > 0 && substr_count($varName,"chk_") > 0)
				{
					$sql = 'UPDATE '.TBMODULO.' SET Visivel=0 WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					if(!$res) echo db_error($db_link) . $sql . '<br>';
				}
			}
		}
		elseif($_POST['ACCOES']==4) // REMOVER REGISTOs
		{
			foreach($_POST as $varName=> $value) 
			{
				$str_id = substr($varName,strpos($varName,"_") + 1);
				if($str_id > 0 && substr_count($varName,"chk_") > 0)
				{
					$sql = 'DELETE FROM '.TBMODULO.' WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					$sql = 'DELETE FROM '.TBMODULO_lang.' WHERE '.TBID.'_PAI='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					// $sql = 'DELETE FROM '.TBIMAGENS.' WHERE '.TBID.'='.$str_id;
					// $res = Runquery($db_link,$DB_name,$sql);
				}
			}
		}
	}
	elseif(isset($_GET['ACCOES']))
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		if($_GET['ACCOES']==1) // REMOVER REGISTO
		{
			$str_id = $_GET['ID'];
			if($str_id > 0)
			{
				$sql = 'DELETE FROM '.TBMODULO.' WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					$sql = 'DELETE FROM '.TBMODULO_lang.' WHERE '.TBID.'_PAI='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					// $sql = 'DELETE FROM '.TBIMAGENS.' WHERE '.TBID.'='.$str_id;
					// $res = Runquery($db_link,$DB_name,$sql);
				if(!$res) echo db_error($db_link) . $sql . '<br>';
			}
		}
		elseif($_GET['ACCOES']==3) // ATIVAR REGISTO
		{
			foreach($_GET as $varName=> $value) 
			{
				$str_id = substr($varName,strpos($varName,"_") + 1);
				if($str_id > 0 && substr_count($varName,"chk_") > 0)
				{
					$sql = 'UPDATE '.TBMODULO.' SET Visivel=1 WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					if(!$res) echo db_error($db_link) . $sql . '<br>';
				}
			}
		}
		elseif($_GET['ACCOES']==2) // DESATIVAR REGISTO
		{
			foreach($_GET as $varName=> $value)
			{
				$str_id = substr($varName,strpos($varName,"_") + 1);
				if($str_id > 0 && substr_count($varName,"chk_") > 0)
				{
					$sql = 'UPDATE '.TBMODULO.' SET Visivel=0 WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					if(!$res) echo db_error($db_link) . $sql . '<br>';
				}
			}
		}
		elseif($_GET['ACCOES']==4) // REMOVER REGISTOs
		{
			foreach($_GET as $varName=> $value) 
			{
				$str_id = substr($varName,strpos($varName,"_") + 1);
				if($str_id > 0 && substr_count($varName,"chk_") > 0)
				{
					$sql = 'DELETE FROM '.TBMODULO.' WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					$sql = 'DELETE FROM '.TBMODULO_lang.' WHERE '.TBID.'_PAI='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					// $sql = 'DELETE FROM '.TBIMAGENS.' WHERE '.TBID.'='.$str_id;
					// $res = Runquery($db_link,$DB_name,$sql);
				}
			}
		}
	}
}
function show_content_list($pai=0,$contador=-5,$count=0,$md=0,$span=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj; $rs2 = new DataObj;
	
	$pesq = '';
	if(isset($_GET['p']) && $_GET['p']==1)
	{
		if(isset($_GET['DESIGNACAO']) && $_GET['DESIGNACAO']!='') $pesq .= ' AND bl.Nome like "%'.$_GET['DESIGNACAO'].'%"';
		// if(isset($_GET['DATA_PEDIDO']) && $_GET['DATA_PEDIDO']!='' && $_GET['DATA_PEDIDO']!='0000-00-00') $pesq .= ' AND b.bloco_data like "%'.$_GET['DATA_PEDIDO'].'%"';
		if(isset($_GET['bloco_visivel']) && $_GET['bloco_visivel']!='') $pesq .= ' AND b.Visivel="'.$_GET['bloco_visivel'].'"';
		// if(isset($_GET['ATIVIDADE']) && $_GET['ATIVIDADE']!='') $pesq .= ' AND b.bloco_categorias="'.$_GET['ATIVIDADE'].';"';
	}
	$sql = '
			SELECT 
				b.'.TBID.', b.Ordem, b.Visivel, bl.Nome
			FROM 
				'.TBMODULO.' b 
				INNER JOIN '.TBMODULO_LANG.' bl on bl.'.TBID.'_PAI=b.'.TBID.'			
			WHERE 
				 Lang="'.$GLOBALS['admin_lang'].'"
				'.$pesq.' 
			ORDER BY 
				Ordem ASC, b.'.TBID.' ASC
			';
	 //echo $sql;
	$rs->db_open($db_link, $DB_name, $sql);		
	$total = $rs->db_rows;
	$rs->db_close();
	
	$n_pags = ceil($total / N_PAGINAS);
	if(!isset($_GET['pagina'])) $pagina = 1;
	else $pagina = $_GET['pagina'];
	
	$inicio = $pagina - 1;
	$inicio = $inicio * N_PAGINAS;							
	
	$sql2 = $sql.' Limit '.$inicio.', '.N_PAGINAS;
	$rs->db_open($db_link,$DB_name,$sql2);
	if(!$rs->EOF() || (isset($_GET['p']) && $_GET['p']==1))
	{
		?>
		<table width="100%" height="50px" border=0 cellspacing=0 cellpadding=0 class="p1">
		<tr bgcolor="#FFFFFF">
		<td height="35" align="center">&nbsp;</td>		
		<td width="100" valign="middle" align="center"><b>Estado<b></td>
		<td valign="middle" align="left"><b>Designação</b></td>		
		<td width="10" valign="middle">&nbsp;</td>
		<td width="10" valign="middle">&nbsp;</td>		
		</tr>
		<tr bgcolor="#FFFFFF">
		<td height="35" align="center"><label class="label_check2" for="chk_all"><input type="checkbox" name="chk_all" id="chk_all" value="1" class="cb" /></label></td>
		<td width="50" valign="middle" align="center"><select style="width:80px;font-size:11px;padding-top:4px;" name="bloco_visivel" id="bloco_visivel" class="chzn-select"><option value="" <?php if(!isset($_GET['bloco_visivel']) && !$_GET['bloco_visivel']) echo 'selected="selected"';?>>TODOS</option><option value="1" <?php if(isset($_GET['bloco_visivel']) && $_GET['bloco_visivel']=='1') echo 'selected="selected"';?>>Visivel</option><option value="2" <?php if(isset($_GET['bloco_visivel']) && $_GET['bloco_visivel']=='2') echo 'selected="selected"';?>>Invisivel</option></select></td>
		<td valign="middle" align="left"><input type="text" style="width:450px" name="DESIGNACAO" id="DESIGNACAO" class="box" value="<?php if(isset($_GET['DESIGNACAO'])) echo $_GET['DESIGNACAO']?>" /></td>	
		<td valign="middle" align="left" colspan="2"><img src="img/reload.png" title="Pesquisar" border="0" onclick="pesquisa('<?php echo ((isset($_GET[TBID]) && $_GET[TBID] > 0) ? 'pagina.php?'.TBID.'='.$_GET[TBID] : '')?>'); return false;" style="cursor:pointer" /></td>
		</tr>
		<?php
		while(!$rs->EOF())
		{
			$count++;			
			$id = $rs->db_data[TBID];
			$Visivel = $rs->db_data["Visivel"];
			$bloco_visivel_img = (isset($rs->db_data["Visivel"]) && $rs->db_data["Visivel"]==1) ? 'ok.png' : 'hidden.png';
			$Nome = $rs->db_data["Nome"];
			$Ordem = $rs->db_data["Ordem"];
			
			
			if($Visivel == 1)
			{
				if($cor=='#c8ecfa')	{$cor="#def2fa";}
				else				{$cor="#c8ecfa";}
			}
			else
			{
				if($cor=='#FF9999')	{$cor="#FF6666";}
				else				{$cor="#FF9999";}
			}
			
			?>			
			<tr bgcolor="<?php echo $cor;?>" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?php echo $cor;?>'">			
			<td align="center"><label class="label_check2" for="chk_<?php echo $id?>"><input type="checkbox" name="chk_<?php echo $id?>" id="chk_<?php echo $id?>" value="<?php echo $id?>" class="cb" /></label></td>
			<td valign="middle" align="center"><a title="<?php echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?<?php echo TBID?>=<?php echo $id;?>&Dump=<? echo uniqid("");?>"><img src="img/<?php echo $bloco_visivel_img?>"/></a></td>
			<td valign="middle" align="left"><a title="<?php echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?<?php echo TBID?>=<?php echo $id;?>&Dump=<? echo uniqid("");?>"><? if($Nome){echo htmlentities_b(stripslashes($Nome));} else echo "[Edit]";?></a></td>
			<td valign="middle"><a title="Duplicar" class="lista_links" href="pagina.php?<?php echo TBID?>=<?php echo $id;?>&duplicar=1&Dump=<? echo uniqid("");?>"><img src="img/next-item.png" border="0" /></a></td>
			<td valign="middle"><a title="Remover" class="lista_links" onclick="if(confirm('Tem a certeza que pretente remover este registo?')) return true; else return false;" href="list.php?<?php echo TBID?>=<?php echo $id;?>&ACCOES=1&Dump=<? echo uniqid("");?>"><img src="img/delete-item.png" border="0" /></a></td>
			</tr>			
			<?php
			$rs->db_next();
		}
		?>
		</table>
		<?php		
		if($n_pags > 1)
		{       
			
			$url_pages = 'list.php?DESIGNACAO='.urlencode($_GET['DESIGNACAO']).'&bloco_visivel='.urlencode($_GET['bloco_visivel']);
			
			$return = '<table width="100%" class="new_quadros" border=0 cellspacing=2 cellpadding=1><tr>';
			if($pagina > 6)
			{
				$pgn = 'onclick=window.open("'.$url_pages.'&pagina=1","_self")';				
				$return .= '<td width="2px">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer;padding-top:2px;border:#000 0px solid" >1... </td>';
			}
			for($pi = ($pagina - 6); $pi < ($pagina + 7); $pi++)
			{
				$pgn = 'onclick=window.open("'.$url_pages.'&pagina='.$pi.'","_self")'; 
				$return .= ($pi >= 1 && $pi <= $n_pags) ? $pagina == $pi ? '<td width="2px">&nbsp;</td><td width="20" height="20" align="center" style="color:#FFF; font-weight:bold">'.sprintf(($pi)).'</td>' : '<td width="2px">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer">'.sprintf($pi).'</td>' :'';
			}
			if($n_pags!=$pagina)
			{
				$pgn='onclick=window.open("'.$url_pages.'&pagina='.$n_pags.'","_self")'; 
				$return .= '<td width="2px" style="">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer;padding-top:2px;border:#000 0px solid" > ...'.sprintf($n_pags).'</td>';
			}
			$return .= '<td>&nbsp;</td></tr></table>';
			echo $return;
		}
		
		return 1;
	}
	elseif($pai==0)
	{?>
		<table height="50px" border=0 cellspacing=0 cellpadding=0 class="r1">
		<tr bgcolor="#e8e7e7" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='#e8e7e7'">
		<td colspan="8" align="center">Não Existem <?php echo MODULO?></td>
		</tr>
		</table>
	<?php 
	}
	$rs->db_close();
}

function show_content_list2($pai=0,$contador=-5,$count=0,$md=0,$span=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj; $rs2 = new DataObj;
	
	$pesq = '';
	if(isset($_GET['p']) && $_GET['p']==1)
	{
		if(isset($_GET['DESIGNACAO']) && $_GET['DESIGNACAO']!='') $pesq .= ' AND bl.Nome like "%'.$_GET['DESIGNACAO'].'%"';
		// if(isset($_GET['DATA_PEDIDO']) && $_GET['DATA_PEDIDO']!='' && $_GET['DATA_PEDIDO']!='0000-00-00') $pesq .= ' AND b.bloco_data like "%'.$_GET['DATA_PEDIDO'].'%"';
		if(isset($_GET['bloco_visivel']) && $_GET['bloco_visivel']!='') $pesq .= ' AND b.Visivel="'.$_GET['bloco_visivel'].'"';
		// if(isset($_GET['ATIVIDADE']) && $_GET['ATIVIDADE']!='') $pesq .= ' AND b.bloco_categorias="'.$_GET['ATIVIDADE'].';"';
	}
	$sql = '
			SELECT 
				b.'.TBID.', b.Ordem, b.Visivel, bl.Nome
			FROM 
				'.TBMODULO.' b 
				INNER JOIN '.TBMODULO_LANG.' bl on bl.'.TBID.'_PAI=b.'.TBID.'			
			WHERE 
			    b.Tipo=1
				AND
				1=1 
				AND Lang="'.$GLOBALS['admin_lang'].'"
				'.$pesq.' 
			ORDER BY 
				Ordem ASC, b.'.TBID.' ASC
			';
	// echo $sql;
	$rs->db_open($db_link, $DB_name, $sql);		
	$total = $rs->db_rows;
	$rs->db_close();
	
	$n_pags = ceil($total / N_PAGINAS);
	if(!isset($_GET['pagina'])) $pagina = 1;
	else $pagina = $_GET['pagina'];
	
	$inicio = $pagina - 1;
	$inicio = $inicio * N_PAGINAS;							
	
	$sql2 = $sql.' Limit '.$inicio.', '.N_PAGINAS;
	$rs->db_open($db_link,$DB_name,$sql2);
	if(!$rs->EOF() || (isset($_GET['p']) && $_GET['p']==1))
	{
		?>
		<table width="100%" height="50px" border=0 cellspacing=0 cellpadding=0 class="p1">
		<tr bgcolor="#FFFFFF">
		<td height="35" align="center">&nbsp;</td>		
		<td width="100" valign="middle" align="center"><b>Estado<b></td>
		<td valign="middle" align="left"><b>Designação</b></td>		
		<td width="10" valign="middle">&nbsp;</td>
		<td width="10" valign="middle">&nbsp;</td>		
		</tr>
		<tr bgcolor="#FFFFFF">
		<td height="35" align="center"><label class="label_check2" for="chk_all"><input type="checkbox" name="chk_all" id="chk_all" value="1" class="cb" /></label></td>
		<td width="50" valign="middle" align="center"><select style="width:80px;font-size:11px;padding-top:4px;" name="bloco_visivel" id="bloco_visivel" class="chzn-select"><option value="" <?php if(!isset($_GET['bloco_visivel']) && !$_GET['bloco_visivel']) echo 'selected="selected"';?>>TODOS</option><option value="1" <?php if(isset($_GET['bloco_visivel']) && $_GET['bloco_visivel']=='1') echo 'selected="selected"';?>>Visivel</option><option value="2" <?php if(isset($_GET['bloco_visivel']) && $_GET['bloco_visivel']=='2') echo 'selected="selected"';?>>Invisivel</option></select></td>
		<td valign="middle" align="left"><input type="text" style="width:450px" name="DESIGNACAO" id="DESIGNACAO" class="box" value="<?php if(isset($_GET['DESIGNACAO'])) echo $_GET['DESIGNACAO']?>" /></td>	
		<td valign="middle" align="left" colspan="2"><img src="img/reload.png" title="Pesquisar" border="0" onclick="pesquisa('<?php echo ((isset($_GET[TBID]) && $_GET[TBID] > 0) ? 'pagina.php?'.TBID.'='.$_GET[TBID] : '')?>'); return false;" style="cursor:pointer" /></td>
		</tr>
		<?php
		while(!$rs->EOF())
		{
			$count++;			
			$id = $rs->db_data[TBID];
			$Visivel = $rs->db_data["Visivel"];
			$bloco_visivel_img = (isset($rs->db_data["Visivel"]) && $rs->db_data["Visivel"]==1) ? 'ok.png' : 'hidden.png';
			$Nome = $rs->db_data["Nome"];
			$Ordem = $rs->db_data["Ordem"];
			
			
			if($Visivel == 1)
			{
				if($cor=='#c8ecfa')	{$cor="#def2fa";}
				else				{$cor="#c8ecfa";}
			}
			else
			{
				if($cor=='#FF9999')	{$cor="#FF6666";}
				else				{$cor="#FF9999";}
			}
			
			?>			
			<tr bgcolor="<?php echo $cor;?>" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?php echo $cor;?>'">			
			<td align="center"><label class="label_check2" for="chk_<?php echo $id?>"><input type="checkbox" name="chk_<?php echo $id?>" id="chk_<?php echo $id?>" value="<?php echo $id?>" class="cb" /></label></td>
			<td valign="middle" align="center"><a title="<?php echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?<?php echo TBID?>=<?php echo $id;?>&Dump=<? echo uniqid("");?>"><img src="img/<?php echo $bloco_visivel_img?>"/></a></td>
			<td valign="middle" align="left"><a title="<?php echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?<?php echo TBID?>=<?php echo $id;?>&Dump=<? echo uniqid("");?>"><? if($Nome){echo htmlentities_b(stripslashes($Nome));} else echo "[Edit]";?></a></td>
			<td valign="middle"><a title="Duplicar" class="lista_links" href="pagina.php?<?php echo TBID?>=<?php echo $id;?>&duplicar=1&Dump=<? echo uniqid("");?>"><img src="img/next-item.png" border="0" /></a></td>
			<td valign="middle"><a title="Remover" class="lista_links" onclick="if(confirm('Tem a certeza que pretente remover este registo?')) return true; else return false;" href="list.php?<?php echo TBID?>=<?php echo $id;?>&ACCOES=1&Dump=<? echo uniqid("");?>"><img src="img/delete-item.png" border="0" /></a></td>
			</tr>			
			<?php
			$rs->db_next();
		}
		?>
		</table>
		<?php		
		if($n_pags > 1)
		{       
			
			$url_pages = 'list.php?DESIGNACAO='.urlencode($_GET['DESIGNACAO']).'&bloco_visivel='.urlencode($_GET['bloco_visivel']);
			
			$return = '<table width="100%" class="new_quadros" border=0 cellspacing=2 cellpadding=1><tr>';
			if($pagina > 6)
			{
				$pgn = 'onclick=window.open("'.$url_pages.'&pagina=1","_self")';				
				$return .= '<td width="2px">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer;padding-top:2px;border:#000 0px solid" >1... </td>';
			}
			for($pi = ($pagina - 6); $pi < ($pagina + 7); $pi++)
			{
				$pgn = 'onclick=window.open("'.$url_pages.'&pagina='.$pi.'","_self")'; 
				$return .= ($pi >= 1 && $pi <= $n_pags) ? $pagina == $pi ? '<td width="2px">&nbsp;</td><td width="20" height="20" align="center" style="color:#FFF; font-weight:bold">'.sprintf(($pi)).'</td>' : '<td width="2px">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer">'.sprintf($pi).'</td>' :'';
			}
			if($n_pags!=$pagina)
			{
				$pgn='onclick=window.open("'.$url_pages.'&pagina='.$n_pags.'","_self")'; 
				$return .= '<td width="2px" style="">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer;padding-top:2px;border:#000 0px solid" > ...'.sprintf($n_pags).'</td>';
			}
			$return .= '<td>&nbsp;</td></tr></table>';
			echo $return;
		}
		
		return 1;
	}
	elseif($pai==0)
	{?>
		<table height="50px" border=0 cellspacing=0 cellpadding=0 class="r1">
		<tr bgcolor="#e8e7e7" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='#e8e7e7'">
		<td colspan="8" align="center">Não Existem <?php echo MODULO?></td>
		</tr>
		</table>
	<?php 
	}
	$rs->db_close();
}
?>