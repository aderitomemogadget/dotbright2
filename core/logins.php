<?php
if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__)exit();

function cliente_logado()
{
    global $cli_id, $cli_token;
    if(!$cli_id || !$cli_token)return FALSE;
    global $DB_host,$DB_user,$DB_pass,$DB_name;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new Dataobj;
    //$sql='SELECT Nome,Apelido,Grupo_ID,Nif,Email,Pais FROM clientes WHERE ID="'.$cli_id.'" AND Ativo=1 AND Token="'.$cli_token.'"';
    if ($GLOBALS['registo_validar_email']==1)$sql = 'SELECT Nome,Apelido,Grupo_ID, Nif, Email, Pais FROM clientes WHERE ID="' . $cli_id . '" AND Ativo=1 AND Estado=1 AND Token="' . $cli_token . '"';
    else $sql = 'SELECT Nome,Apelido,Grupo_ID, Nif, Email, Pais FROM clientes WHERE ID="' . $cli_id . '" AND Ativo=1 AND Token="' . $cli_token . '"';

    $rs->db_open($db_link,$DB_name,$sql);
    $logado=0;
    if(!$rs->EOF())
	{
        $dados['cli_id']=$cli_id;
        $dados['Nome']=$rs->db_data['Nome'];
        $dados['Apelido']=$rs->db_data['Apelido'];
        $dados['Grupo']=$rs->db_data['Grupo_ID'];
        $dados['Nif']=$rs->db_data['Nif'];
        $dados['Email']=$rs->db_data['Email'];
        $dados['Pais']=$rs->db_data['Pais'];
        $logado=1;
    }
    $rs->db_close();
    if($logado)
	{
        return $dados;
    }
	else
	{
        return FALSE;
    }
}
function cliente_perfil($cli_id=0)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new Dataobj;
    $sql='SELECT Nome,Apelido,Grupo_ID,Nif,Email,Pais,Testes FROM clientes WHERE ID="'.$cli_id.'"';
    $rs->db_open($db_link,$DB_name,$sql);
    $logado=0;
    if(!$rs->EOF())
	{
        $dados['cli_id']=$cli_id;
        $dados['Nome']=$rs->db_data['Nome'];
        $dados['Apelido']=$rs->db_data['Apelido'];
        $dados['Grupo']=$rs->db_data['Grupo_ID'];
        $dados['Nif']=$rs->db_data['Nif'];
        $dados['Email']=$rs->db_data['Email'];
        $dados['Pais']=$rs->db_data['Pais'];
        $dados['Testes']=$rs->db_data['Testes'];
        $logado=1;
    }
    $rs->db_close();
    if($logado)
	{
        return $dados;
    }
	else
	{
        return FALSE;
    }
}
function getDadosCliente($cli_id)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new Dataobj;
    $sql='SELECT Nome,Cidade,Pais,Email FROM clientes WHERE ID="'.$cli_id.'"';
    $rs->db_open($db_link,$DB_name,$sql);
    if(!$rs->EOF())
    {
        $dados['Nome']=$rs->db_data['Nome'];
        $dados['Cidade']=$rs->db_data['Cidade'];
        $dados['Pais']=country_name($rs->db_data['Pais']);
        $dados['Email']=$rs->db_data['Email'];
    }
    $rs->db_close();
    return $dados;
}
function encripta($db_password)
{
    //return hash_hmac("sha256", $db_password,$db_password);
    //return md5($db_password);
    //$mySalt=_ENC_SALT;
    //$arrTemp = array();
    //for ($i=0;$i<strlen($db_password);$i++) $arrTemp[] = substr($db_password,$i,1);
    //return "AES.".mt_rand(1000,9999).base64_encode(implode($mySalt,array_reverse($arrTemp)));
    //new Blowfish(str_pad('', 56, md5('ps'.__FILE__)), str_pad('', 56, md5('iv'.__FILE__)));

    //estilo prestashop:
    //

    /*There are two methods, one with md5, the other with bcrypt.

    $encryptedPassword =  md5(_COOKIE_KEY_.$textPasswd);

    or

    $encryptedPassword = password_hash($textPasswd, PASSWORD_BCRYPT);

    see https://www.php.net/manual/en/function.password-hash.php

    The bcrypt method doesn't use salt.

    */


	$auth_method = defined('_AUTH_METHOD_') ? constant('_AUTH_METHOD_') : 'base64';
	if($auth_method == 'cookie_key')
	{
		$cookie_key = defined('_COOKIE_KEY_') ? constant('_COOKIE_KEY_') : '';
		return md5($cookie_key.$db_password);
	}
	if($auth_method == 'md5')return md5($db_password);
   return base64_encode($db_password);
}
function desencripta($ss_passwd)
{
    return base64_decode($ss_passwd);
}
function decodePermissoes($Permissoes)
{
    $v_permissoes=array();
    if($Permissoes !== '')
    {
        $v_modulos=explode('|', $Permissoes);
        foreach ($v_modulos as $key => $value)
        {
            $v_perms=explode('_', $value);
            $v_permissoes[$v_perms[0]]=$v_perms[1];
        }
    }
    return $v_permissoes;
}
function show_private_contents()
{
    global $cli_id, $cli_token, $site_lang;
    if(!$cli_id || !$cli_token)return FALSE;

    $logado=FALSE;
    if($dados=cliente_logado())$logado=TRUE;

    global $DB_host,$DB_user,$DB_pass,$DB_name;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new Dataobj;
    $rs2=new Dataobj;
    $sql="Select Grupo_ID from clientes where ID='$cli_id' and Ativo=1 and Token='$cli_token'";
    $rs->db_open($db_link,$DB_name,$sql);
    if(!$rs->EOF())
    {
        echo "<br /><br />";
        $grupo_id=$rs->db_data['Grupo_ID'];
        if($grupo_id>0)$sql="SELECT pages.ID,Cabecalho FROM pages INNER JOIN pages_lang ON pages_lang.ID_PAI=pages.ID WHERE Tipo=1 and ID_Grupo=$grupo_id and Visible=1 and Lang='$site_lang'";
        else $sql="SELECT pages.ID,Cabecalho FROM pages INNER JOIN pages_lang ON pages_lang.ID_PAI=pages.ID WHERE Tipo=1 and ID_Grupo=0 and Visible=1 and Lang='$site_lang'";

        $rs2->db_open($db_link,$DB_name,$sql);
        while(!$rs2->EOF())
        {
            echo "<a href='pages.php?page_id=".$rs2->db_data['ID']."'>".$rs2->db_data['Cabecalho']."</a><br />";
            $rs2->db_next();
        }
        $rs2->db_close();
    }
    $rs->db_close();
    if($logado) return $dados;
    else return FALSE;
}
function verificalogin()
{
    $dados=cliente_logado();
    if($dados)
    {
        echo '<script>window.open("'.seo('pages.php?page_id=1').'","_self")</script>';
        exit;
    }
}
function verificalogin2()
{
    $dados=cliente_logado();
    if(!$dados)
    {
        echo '<script>window.open("'.seo('pages.php?page_id=5').'?registo=1", "_self")</script>';
        exit;
    }
}
function show_menu_login()
{
    global $site_http_url;
    $dados = cliente_logado(); $presocial = ''; $facebook = ''; $welcome = '';
    if($dados)
    {
        $welcome .= '<div class="button2" style="padding-left: 4px;padding-right: 4px;cursor:default;color:#000">'.$GLOBALS['key_login_loggedin'][$GLOBALS['site_lang']].' '.$dados['Nome'].' '.$dados['Apelido'].'</div>';
        $welcome .= '<div class="button2" style="padding-left: 4px;padding-right: 4px;" onclick="window.open(\''.seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'\',\'_top\');">Minha Área</div>';
        $welcome .= '<div class="button2" style="padding-left: 4px;padding-right: 4px;" onclick="window.open(\''.seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'?show_compras=1\',\'_top\');">Encomendas</div>';
        $welcome .= '<div class="button2" style="padding-left: 4px;padding-right: 4px;" onclick="window.open(\''.seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'?show_rmas=1\',\'_top\');">Pedidos Assistência</div>';
        $welcome .= '<div class="button2" style="padding-left: 4px;padding-right: 4px;" onclick="window.open(\''.seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'?logout=1\',\'_top\');">Logout</div>';
    }
    else
    {  
        $welcome .= '
            <div class="menu_login">
                    <div class="row">
                        <div class="col-md-2">
                            <img src="layout/images/login.svg" alt="login">
                        </div>
                        <div class="col-md-10">
                            <span style="color: #ff8a00;">Bem-vindo</span> <br>
                            <span onclick="toggleLoginBox()";>Login</span><span > / <a style="color: #fff;" href="'.seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'">Registo</a></span> 
                        </div>                            
                    </div> 
                </div>
        ';
    }
    $str = $facebook.$presocial.$welcome.'<div class="clear"></div>';
    return $str;
}
function show_menu_login4()
{
    if($_SESSION['ss_ismobile']==1)return;
    //-------------------------------------

    
    global $site_http_url;
    $dados = cliente_logado(); $presocial = ''; $facebook = ''; $welcome = '';

    if($dados)
    {

        $welcome .= '
            <div class="menu_login log_on">
                    <div class="row">
                        <div class="col-md-2"> 
                            <img src="layout/'.PROJETO.'/images/login.svg" alt="login">
                        </div>
                        <div class="col-md-10">
                            <span style="color: #ff8a00;">Bem-vindo</span> <br>';
                            // $welcome .= '            <span style="cursor:pointer" onclick="window.open(\''.seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'\',\'_top\');">'.$GLOBALS['key_login_loggedin'][$GLOBALS['site_lang']].' '.$dados['Nome'].' '.$dados['Apelido'].'</span><span > / <a style="color: #fff;" onclick="window.open(\''.seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'?logout=1\',\'_top\');" href="javascript:;">Logout</a></span> ';
                            $welcome .= '     <span style="cursor:pointer" onclick="window.open(\''.seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'\',\'_top\');">'.$GLOBALS['key_login_loggedin'][$GLOBALS['site_lang']].' '.$dados['Nome'].'</span><span > / <a style="color: #fff;" onclick="window.open(\''.seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'?logout=1\',\'_top\');" href="javascript:;">Logout</a></span> 
                        </div>                            
                    </div> 
                </div>
        ';
    }
    else
    {
        $welcome .= '
            <div class="menu_login">
                    <div class="row">
                        <div class="col-md-2">
                            <img src="layout/'.PROJETO.'/images/login.svg" alt="login">
                        </div>
                        <div class="col-md-10">
                            <span style="color: #ff8a00;">Bem-vindo</span> <br>
                            <span style="cursor:pointer" onclick="toggleLoginBox()";>Login</span><span > / <a style="color: #fff;" href="'.seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'">Registo</a></span> 
                        </div>                            
                    </div> 
                </div>
        ';
    }

    //$facebook = '<div class="social" style="width:40px">&nbsp;</div>';

    $str = $welcome;
    return $str;
}
function show_menu_login2($tp=0)
{
    /*-------------- Begin Social Network Login -----------*/
    global $DB_host,$DB_user,$DB_pass,$DB_name;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new DataObj;
    $sql='SELECT facebook_id,facebook_secret,facebook_ativo,google_id,google_secret,google_ativo FROM modulos_config_site WHERE ID=1';
    $rs->db_open($db_link,$DB_name,$sql);
    if(!$rs->EOF())
    {
        $facebook_activo=$rs->db_data['facebook_ativo'];
        $google_activo=$rs->db_data['google_ativo'];
        if($facebook_activo==1 || $google_activo==1)
        {?>
        	<div class="zz-separator-text">
        		<span style="position: relative;z-index: 1;padding: 0 10px;box-sizing: inherit;text-align: center;font-size: 18px;color:#000; background: #FFFFFF">Utilize as Redes Sociais</span>
        	</div>
        	<div class="container_social_login">
        		<div class="row_social_login">
        		  <div class="col_social_login" style="text-align:center;">
        				 <?php if($facebook_activo==1){ ?>
                            <div class="facebook GTM_login_facebook" onclick="login_facebook()" style="margin-bottom:20px;">
            					<a href="#" class="fb_social_login btn_social_login" style="width:100%;">
            						<?=getLinguas('key_login_facebook',$GLOBALS['site_lang']) ?>
            					</a>
            				</div>
        				<?} if($google_activo==1){ ?>
                        <div class="google GTM_login_google" onclick="login_google()">
        					<a href="#" class="google_social_login btn_social_login" style="width:100%;">Login Google</a>
        				</div>
                        <?php } ?>
        		  </div>
        		</div>
        	</div>
        	<div class="zz-separator-text">
        		<span style="position:relative;z-index:1;padding:0 10px;box-sizing:inherit;text-align:center;font-size:18px;color:#000;background:#FFFFFF">Ou</span>
        	</div>
    	   <?php
        }
    }
    $rs->db_close();
    /*-------------- End Social Network Login -----------*/
    $dados=cliente_logado();
    if($dados)
    {
    ?>
        <div id="login_frm" class="loginbox" style="width:150px;margin:0 auto">
            <div class="background" style="width:150px;margin:0 auto">
                <div style="text-align:center"><?=getLinguas('key_areareservada',$GLOBALS['site_lang']); ?> </div>
                <br>
                <div class="c"></div>
                <div id="normal" class="normalbox HvMd" style="display:block;width:150px;margin:0 auto">
                    <div class="button3" style="line-height:30px;margin:10px auto;width:150px" onclick="window.open('<?=seo('pages.php?page_id='.$GLOBALS['minhaconta_page_id']); ?>','_top');"><?=getLinguas('key_conta_pessoal',$GLOBALS['site_lang']); ?></div>
                    <div class="button3" style="line-height:30px;margin:10px auto;width:150px" onclick="window.open('<?=seo('pages.php?page_id='.$GLOBALS['minhaconta_page_id']).'?show_wishlist=1'; ?>','_top');"><?=getLinguas('key_wishlist',$GLOBALS['site_lang']); ?></div>
                    <div class="button3" style="line-height:30px;margin:10px auto;width:150px" onclick="window.open('<?=seo('pages.php?page_id='.$GLOBALS['minhaconta_page_id']).'?show_compras=1'; ?>','_top');"><?=getLinguas('key_encomendas',$GLOBALS['site_lang']); ?></div>
                    <div class="button3" style="line-height:30px;margin:10px auto;width:150px" onclick="window.open('<?=seo('pages.php?page_id='.$GLOBALS['minhaconta_page_id']).'?logout=1'; ?>','_top');">Logout</div>
                </div>
            </div>
        </div><?php
    }
    else
    {?>
        <div id="login_frm" class="loginbox" style="width: 470px;">
            <a href="<?=seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'?registar=1'; ?>" class="link-register"><?=getLinguas('key_user_notregistadomsg',$GLOBALS['site_lang']); ?></a>
            <br> <br>
            <form action="<?=seo('pages.php?page_id='.$GLOBALS['registo_page_id']) ?>" method="post" target="_top" name="login_form" id="login_form" onsubmit="return validarLogin('mobile');">
                <input type="hidden" name="login" value="1"/> <input type="hidden" name="submit1" value="1"/>
                <input type="hidden" name="redirect" value="<?php if(isset($_GET['redir']) && $_GET['redir'] !='' && $_GET['redir'] !='undefined') {echo urlencode($_GET['redir']); } ?>"/>
				<input type="hidden" name="referer" value="<?php if(isset($_GET['redir']) && $_GET['redir'] !='' && $_GET['redir'] !='undefined') {echo urlencode($_GET['redir']); } ?>">
                <div class="form-group">
                    <input id="popm_email_mobile" class="form-control" type="text" name="m_email" value="" placeholder="<?=getLinguas('key_email',$GLOBALS['site_lang']); ?>*" autocomplete="off"/>
                </div>
                <div class="form-group">
                    <input id="popm_password_mobile" class="form-control" type="password" name="m_password" value="" placeholder="<?=getLinguas('key_password',$GLOBALS['site_lang']); ?>*" autocomplete="off"/>
                </div>
                <div class="floatright" style="font-size:10px;">* <?=getLinguas('key_obrigatorio2',$GLOBALS['site_lang']); ?></div>
                <div class="c"></div>
                <div class="row">
                    <div class="col-xs-6">
                        <input type="submit" class="button3-sub" value="OK"/>
                    </div>
                    <div class="col-xs-6">
                        <a href="<?=seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'?recupera=1'; ?>" class="link-recuperate"><?=getLinguas('key_cliente_recuperar_pass',$GLOBALS['site_lang']); ?></a>
                    </div>
                </div>
            </form>
        </div><?php
    }
}
function show_menu_login3($tp=0)
{
    $dados=cliente_logado();
    if($dados)
    {
        ?>
        <div id="loginboxpop" class="loginbox2" style="width:150px;margin:0 auto">
            <div class="background" style="width:150px;margin:0 auto">
                <div style="text-align:center"><?=getLinguas('key_areareservada',$GLOBALS['site_lang']); ?> </div>
                <br>
                <div class="c"></div>
                <div id="normal" class="normalbox HvMd" style="display:block;width:150px;margin:0 auto">
                    <div class="button3" style="line-height:30px;margin:10px auto;width:150px" onclick="window.open('<?=seo('pages.php?page_id='.$GLOBALS['minhaconta_page_id']); ?>','_top');"><?=getLinguas('key_conta_pessoal',$GLOBALS['site_lang']); ?></div>
                    <div class="button3" style="line-height:30px;margin:10px auto;width:150px" onclick="window.open('<?=seo('pages.php?page_id='.$GLOBALS['minhaconta_page_id']).'?show_wishlist=1'; ?>','_top');"><?=getLinguas('key_wishlist',$GLOBALS['site_lang']); ?></div>
                    <div class="button3" style="line-height:30px;margin:10px auto;width:150px" onclick="window.open('<?=seo('pages.php?page_id='.$GLOBALS['minhaconta_page_id']).'?show_compras=1'; ?>','_top');"><?=getLinguas('key_encomendas',$GLOBALS['site_lang']); ?></div>
                    <div class="button3" style="line-height:30px;margin:10px auto;width:150px" onclick="window.open('<?=seo('pages.php?page_id='.$GLOBALS['minhaconta_page_id']).'?logout=1'; ?>','_top');">Logout</div>
                </div>
            </div>
        </div><?php
    }
    else
    {?>
        <div  id="loginboxpop" class="loginbox2" style="background:#FFF">
            <a href="<?=seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'?registar=1'; ?>" class="link-register"><?=getLinguas('key_user_notregistadomsg',$GLOBALS['site_lang']); ?></a>
            <br> <br>
            <form action="<?=seo('pages.php?page_id='.$GLOBALS['registo_page_id']) ?>" method="post" target="_top" name="login_form" id="login_form" onsubmit="return validarLogin('pc');">
                <input type="hidden" name="login" value="1"/> <input type="hidden" name="submit1" value="1"/>
                <input type="hidden" name="redirect" value="<?php if(isset($redir)) {
                    echo $redir;
                } ?>"/>
                <div class="form-group">
                    <input id="popm_email_pc" class="form-control" type="text" name="m_email" value="" placeholder="<?=getLinguas('key_email',$GLOBALS['site_lang']); ?>*" autocomplete="off"/>
                </div>
                <div class="form-group">
                    <input id="popm_password_pc" class="form-control" type="password" name="m_password" value="" placeholder="<?=getLinguas('key_password',$GLOBALS['site_lang']); ?>*" autocomplete="off"/>
                </div>
                <div class="floatright" style="font-size:10px;">* <?=getLinguas('key_obrigatorio2',$GLOBALS['site_lang']); ?></div>
                <div class="c"></div>
                <div class="row">
                    <div class="col-xs-12 center">
                        <input type="submit" class="button3-sub" value="OK"/>
                    </div>
                    <div class="col-xs-12">
                        <a href="<?php echo seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'?recupera=1'; ?>" class="link-recuperate"><?=getLinguas('key_cliente_recuperar_pass',$GLOBALS['site_lang']); ?></a>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }
}
function autenticarUtilizadorFacebook($fbid, $email="", $nome="", $apelido="", $location="")
{
    global $DB_host,$DB_user,$DB_pass,$DB_name;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new Dataobj;
    $sql='SELECT ID FROM clientes WHERE fbid="'.$fbid.'" OR Email="'.$email.'"';
    $rs->db_open($db_link,$DB_name,$sql);
    if(!$rs->EOF()) {
        //já existe um utilizador com este facebook id, logo vamos autenticar
        global $cli_id, $cli_token;
        $cli_id=$_SESSION['ss_cli_id']=$rs->db_data['ID'];
        $cli_token=$_SESSION['ss_cli_token']=uniqid('');

        $sql="UPDATE clientes SET failed_attempts=0,Token='".$cli_token."',fbid='".$fbid."' WHERE ID=".$rs->db_data['ID'];
        Runquery($db_link,$DB_name,$sql);

        foreach ($_SESSION as $key => $val) {
            if(substr_count($key, 'wish_prod_')) {
                if(addRemoveWishList($val, 'add') == 0) {
                    unset($_SESSION[$key]);
                }
            }
        }

        $_SESSION['ss_cli_ft']=0;//reset tentativas
        $res=1;
    } else {
        $pais='';
        $cidade='';
        if($location != "") {
            $vlocal=explode(',', $location);
            if(is_array($vlocal) && count($vlocal) == 3) {
                $cidade=rtrim(ltrim($vlocal[1]));
                $pais=country_id(rtrim(ltrim($vlocal[2])));
            }
        }
        $password=encripta(generateRandomString());

        $sql="INSERT INTO clientes(Username,Email,Nome,Apelido,fbid,Estado,Ativo,Pais,Cidade,Password) VALUES('".$email."','".$email."','".$nome."','".$apelido."','".$fbid."',1,1,'".$pais."','".$cidade."','".$password."')";
        $res=Runquery($db_link,$DB_name,$sql);
        if($res) {
            $idcli=db_get_last_insert_id($db_link);
            global $cli_id, $cli_token;
            $cli_id=$_SESSION['ss_cli_id']=$idcli;
            $cli_token=$_SESSION['ss_cli_token']=uniqid('');

            $sql="UPDATE clientes SET failed_attempts=0,Token='".$cli_token."' WHERE ID=".$idcli;
            Runquery($db_link,$DB_name,$sql);

            foreach ($_SESSION as $key => $val) {
                if(substr_count($key, 'wish_prod_')) {
                    if(addRemoveWishList($val, 'add') == 0) {
                        unset($_SESSION[$key]);
                    }
                }
            }

            $_SESSION['ss_cli_ft']=0;//reset tentativas
            $res=1;
        }
    }
    $rs->db_close();

    return $res;
}

//Login pelo Twitter
function autenticarUtilizadorTwitter($twitterid,$email='',$nome='',$apelido='',$location='')
{
    global $DB_host,$DB_user,$DB_pass,$DB_name;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new Dataobj;
    $sql='SELECT ID FROM clientes WHERE twitterid="'.$twitterid.'" OR Email="'.$email.'"';
    $rs->db_open($db_link,$DB_name,$sql);
    if(!$rs->EOF())
    {
        //já existe um utilizador com este facebook id, logo vamos autenticar
        global $cli_id, $cli_token;
        $cli_id=$_SESSION['ss_cli_id']=$rs->db_data['ID'];
        $cli_token=$_SESSION['ss_cli_token']=uniqid('');
        $sql="UPDATE clientes SET failed_attempts=0,Token='".$cli_token."',twitterid='".$twitterid."' WHERE ID=".$rs->db_data['ID'];
        Runquery($db_link,$DB_name,$sql);
        foreach ($_SESSION as $key => $val)
        {
            if(substr_count($key, 'wish_prod_'))
            {
                if(addRemoveWishList($val, 'add') == 0)
                {
                    unset($_SESSION[$key]);
                }
            }
        }
        $_SESSION['ss_cli_ft']=0;//reset tentativas
        $res=1;
    }
    else
    {
        $pais='';
        $cidade='';
        if($location != "")
        {
            $vlocal=explode(',',$location);
            if(is_array($vlocal) && count($vlocal) == 3)
            {
                $cidade=rtrim(ltrim($vlocal[1]));
                $pais=country_id(rtrim(ltrim($vlocal[2])));
            }
        }
        $password=encripta(generateRandomString());
        $sql="INSERT INTO clientes(Username,Email,Nome,Apelido,twitterid,Estado,Ativo,Pais,Cidade,Password) VALUES('".$email."','".$email."','".$nome."','".$apelido."','".$twitterid."',1,1,'".$pais."','".$cidade."','".$password."')";
        $res=Runquery($db_link,$DB_name,$sql);
        if($res)
        {
            $idcli=db_get_last_insert_id($db_link);
            global $cli_id, $cli_token;
            $cli_id=$_SESSION['ss_cli_id']=$idcli;
            $cli_token=$_SESSION['ss_cli_token']=uniqid('');
            $sql="UPDATE clientes SET failed_attempts=0,Token='".$cli_token."' WHERE ID=".$idcli;
            Runquery($db_link,$DB_name,$sql);
            foreach ($_SESSION as $key => $val)
            {
                if(substr_count($key, 'wish_prod_'))
                {
                    if(addRemoveWishList($val, 'add') == 0)
                    {
                        unset($_SESSION[$key]);
                    }
                }
            }
            $_SESSION['ss_cli_ft']=0;//reset tentativas
            $res=1;
        }
    }
    $rs->db_close();
    return $res;
}
function autenticarUtilizadorGoogle($googleid,$email='',$nome='',$apelido='',$location='')
{
    global $DB_host,$DB_user,$DB_pass,$DB_name;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new Dataobj;
    $googleid=md5($googleid);
    $sql='SELECT ID FROM clientes WHERE googleid="'.$googleid.'" AND Email="'.$email.'"';
    $rs->db_open($db_link,$DB_name,$sql);
    if(!$rs->EOF())
    {
        //já existe um utilizador com este facebook id, logo vamos autenticar
        global $cli_id, $cli_token;
        $cli_id=$_SESSION['ss_cli_id']=$rs->db_data['ID'];
        $cli_token=$_SESSION['ss_cli_token']=uniqid('');
        $sql="UPDATE clientes SET failed_attempts=0,Token='".$cli_token."',googleid='".$googleid."' WHERE ID=".$rs->db_data['ID'];
        Runquery($db_link,$DB_name,$sql);
        foreach ($_SESSION as $key => $val)
        {
            if(substr_count($key, 'wish_prod_'))
            {
                if(addRemoveWishList($val, 'add') == 0)
                {
                    unset($_SESSION[$key]);
                }
            }
        }
        $_SESSION['ss_cli_ft']=0;//reset tentativas
        $res=1;
    }
    else
    {
        $pais='';
        $cidade='';
        if($location != '')
        {
            $vlocal=explode(',', $location);
            if(is_array($vlocal) && count($vlocal) == 3)
            {
                $cidade=rtrim(ltrim($vlocal[1]));
                $pais=country_id(rtrim(ltrim($vlocal[2])));
            }
        }
        $password=encripta(generateRandomString());
        $sql="INSERT INTO clientes(Username,Email,Nome,Apelido,googleid,Estado,Ativo,Pais,Cidade,Password) VALUES('".$email."','".$email."','".$nome."','".$apelido."','".$googleid."',1,1,'".$pais."','".$cidade."','".$password."')";
        $res=Runquery($db_link,$DB_name,$sql);
        if($res)
        {
            $idcli=db_get_last_insert_id($db_link);
            global $cli_id, $cli_token;
            $cli_id=$_SESSION['ss_cli_id']=$idcli;
            $cli_token=$_SESSION['ss_cli_token']=uniqid('');
            $sql="UPDATE clientes SET failed_attempts=0,Token='".$cli_token."' WHERE ID=".$idcli;
            Runquery($db_link,$DB_name,$sql);
            foreach ($_SESSION as $key => $val)
            {
                if(substr_count($key, 'wish_prod_'))
                {
                    if(addRemoveWishList($val, 'add') == 0)
                    {
                        unset($_SESSION[$key]);
                    }
                }
            }
            $_SESSION['ss_cli_ft']=0;//reset tentativas
            $res=1;
        }
    }
    $rs->db_close();
    return $res;
}
function getDadosAdministrador($admin_id)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new Dataobj;
    $rs2=new Dataobj;
    $sql='SELECT Nome,Grupo_ID FROM logins WHERE ID="'.$admin_id.'"';
    $rs->db_open($db_link,$DB_name,$sql);
    if(!$rs->EOF())
    {
        $dados['Nome']=$rs->db_data['Nome'];
        $dados['Grupo_ID']=$rs->db_data['Grupo_ID'];
        $sql='SELECT Permissoes, Descricao FROM grupos_logins WHERE ID="'.$rs->db_data['Grupo_ID'].'"';
        $rs2->db_open($db_link,$DB_name,$sql);
        if(!$rs2->EOF())
        {
            $dados['Permissoes']=decodePermissoes($rs2->db_data['Permissoes']);
            $dados['Descricao']=$rs2->db_data['Descricao'];
        }
        $rs2->db_close();
    }
    $rs->db_close();
    return $dados;
}
function getProdutosNotificacao($email)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new Dataobj;
    $sql='SELECT ID_Prod FROM clientes_notificacoes WHERE Email="'.$email.'"';
    $rs->db_open($db_link,$DB_name,$sql);
    $dados=array();
    while (!$rs->EOF())
    {
        $dados[]['ID_Prod']=$rs->db_data['ID_Prod'];
        $rs->db_next();
    }
    $rs->db_close();
    return $dados;
}
function inserirProdutosNotificacao($email, $id_produto)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $email=filter_var($email, FILTER_VALIDATE_EMAIL);
    $id_produto=filter_var($id_produto, FILTER_VALIDATE_INT);
    $rs=new Dataobj;
    $sql='SELECT ID_Prod FROM clientes_notificacoes WHERE Email="'.$email.'" AND ID_Prod="'.$id_produto.'"';
    $rs->db_open($db_link,$DB_name,$sql);
    if($rs->EOF())
	{
        $sql = 'INSERT INTO clientes_notificacoes(ID_Prod,Email) VALUES ("'.$id_produto.'","'.$email.'")';
        Runquery($db_link,$DB_name,$sql, $params);
		$produto_url=SITE_HTTP_URL.seo('pages.php?page_id='.$GLOBALS['products_page_id'].'&prod_id='.$id_produto, $id_produto, 2);
		$body='Novo Pedido de Alerta de Stock:<br><br>';
		$body .= 'Email:'.$email.'<br>';
		$body .= '<a href="'.$produto_url.'">Clique para Visualizar o Produto</a><br>';
		send_email($GLOBALS['Default_notifications_email'],'Novo Pedido Alerta de Stock',$body);
    }
    $rs->db_close();
}

function inserirProdutosNotificacaoImgs($email, $id_produto)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $email=filter_var($email, FILTER_VALIDATE_EMAIL);
    $id_produto=filter_var($id_produto, FILTER_VALIDATE_INT);
    $rs=new Dataobj;
    $sql='SELECT ID_Prod FROM clientes_notificacoes_imgs  WHERE Email="'.$email.'" AND ID_Prod="'.$id_produto.'"';
    $rs->db_open($db_link,$DB_name,$sql);
    if($rs->EOF())
	{
        $sql = 'INSERT INTO clientes_notificacoes_imgs (ID_Prod,Email) VALUES ("'.$id_produto.'","'.$email.'")';
        Runquery($db_link,$DB_name,$sql, $params);
		$produto_url=SITE_HTTP_URL.seo('pages.php?page_id='.$GLOBALS['products_page_id'].'&prod_id='.$id_produto, $id_produto, 2);
		$body='Pedido de fotos do produto real:<br><br>';
		$body .= 'Email:'.$email.'<br>';
		$body .= '<a href="'.$produto_url.'">Clique para Visualizar o Produto</a><br>';
		send_email($GLOBALS['Default_notifications_email'],'Novo Pedido Alerta de Stock',$body);
    }
    $rs->db_close();
}
function actualizarProdutoNotificacao($cli_id, $id_produto)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new Dataobj;
    $sql='SELECT ID FROM clientes_visitas WHERE ID_ORG="'.$cli_id.'" AND ID_Prod="'.$id_produto.'" AND Enviado=0';
    $rs->db_open($db_link,$DB_name,$sql);
    if(!$rs->EOF())
    {
        $sql='UPDATE clientes_visitas SET Enviado=1 WHERE ID='.$rs->db_data['ID'];
        Runquery($db_link,$DB_name,$sql);
    }
    $rs->db_close();
}
function unsubscriveNotificarProdutoVisita($cli_id)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $sql='UPDATE clientes SET Notify_products="0" WHERE ID="'.$cli_id.'"';
    $res=Runquery($db_link,$DB_name,$sql);
    $sql='DELETE FROM clientes_visitas WHERE ID_ORG="'.$cli_id.'"';
    $res=Runquery($db_link,$DB_name,$sql);
    return $res;
}
function notificarProdutoVisita()
{
    global $DB_host,$DB_user,$DB_pass,$DB_name, $site_http_url;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new Dataobj;
    $rs2=new Dataobj;
    $data=date("Y-m-d H:i:s",strtotime('-2 days'));
    $sql='SELECT c.ID,c.Nome,c.Apelido,c.Email
    	FROM clientes_visitas AS cv
		INNER JOIN clientes c ON c.ID=cv.ID_ORG
    	WHERE cv.Enviado="0" AND cv.Data_Visita <= "'.$data.'"
    	GROUP BY c.ID';
    $rs->db_open($db_link,$DB_name,$sql);
    while (!$rs->EOF())
    {
        $ID=$rs->db_data['ID'];
        $Nome=$rs->db_data['Nome'];
        $Apelido=$rs->db_data['Apelido'];
        $Email=$rs->db_data['Email'];
        $sql2='SELECT * FROM clientes_visitas AS cv WHERE cv.Enviado="0" AND cv.Data_Visita <= "'.$data.'" AND cv.ID_ORG="'.$ID.'"';
        $produtos='';
        $ids='';
        $rs2->db_open($db_link,$DB_name,$sql2);
        if(!$rs2->EOF())
        {
            $produtos .= '<table width="100%" cellpadding="0" cellspacing="0">';
            $n_prods=0;
            while (!$rs2->EOF())
            {
                $IDb=$rs2->db_data['ID'];
                $prods=get_produtc_info($IDb);
                $ID_Prod=$rs2->db_data['ID_Prod'];
                $url=$site_http_url.seo('pages.php?page_id='.$GLOBALS['products_page_id'],$ID_Prod,2);
                $prods=get_produtc_info($ID_Prod);
                if($prods)
                {
                    if(!substr_count($prods['prd_imgm_cart'], $site_http_url))
                    {
                        $img=$site_http_url.$prods['prd_imgm_cart'];
                    }
                    else
                    {
                        $img=$prods['prd_imgm_cart'];
                    }
                    // $produtos.='<tr><td><img src="'.$img.'"></td><td><a href="'.$url.'" target="_blank">'.$prods['prod_nome'].'</a></td></tr>';
                    if($n_prods < 10)
                    {
                        $produtos .= '<tr><td><a href="'.$url.'" target="_blank" style="color:#000;text-decoration:none">'.$prods['prod_nome'].'</a></td></tr>';
                        $produtos .= '<tr><td>&nbsp;</td></tr>';
                    }
                    $n_prods++;
                }
                $ids .= $IDb.',';
                $rs2->db_next();
            }
            $produtos .= '</table>';
        }
        $rs2->db_close();
        $ids=substr($ids, 0, -1);
        $body=array('[name]' => $Nome, '[surname]' => $Apelido, '[produtos]' => $produtos);
        $message=template_email($GLOBALS['template_notificacaovisita'], $body);
        $message['body'] .= get_disclaimer(1, $ID, '', $Email, 0, 0, 1);
        if(send_email($Email, $message['subject'], $message['body']))
        {
            $sql='UPDATE clientes_visitas SET Enviado=1 WHERE ID IN('.$ids.')';
            $res=Runquery($db_link,$DB_name,$sql);
            registarLogCron('Notificação de visitas ao cliente (produtos) - '.$Nome.' - '.$Apelido, $sql, date("Y-m-d H:i:s"));
        }
        $rs->db_next();
    }
    $rs->db_close();
}
function inserirProdutoVisita($cli_id,$id_produto)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new Dataobj;
    $rs2=new Dataobj;
    $sql='SELECT Notify_products FROM clientes WHERE ID="'.$cli_id.'"';
    $rs2->db_open($db_link,$DB_name,$sql);
    if(!$rs2->EOF())
    {
        if($rs2->db_data['Notify_products'] == 1)
        {
            $sql='SELECT ID FROM clientes_visitas WHERE ID_ORG="'.$cli_id.'" AND ID_Prod="'.$id_produto.'" AND Enviado=0';
            $rs->db_open($db_link,$DB_name,$sql);
            $dados=array();
            if($rs->EOF())
            {
                $sql='INSERT INTO clientes_visitas(ID_ORG,ID_Prod,Enviado,Data_Visita) VALUES ("'.$cli_id.'","'.$id_produto.'","0","'.date("Y-m-d H:i:s").'")';
                $res=Runquery($db_link,$DB_name,$sql);
            }
            else
            {
                $sql='UPDATE clientes_visitas SET Data_Visita="'.date("Y-m-d H:i:s").'" WHERE ID='.$rs->db_data['ID'];
                $res=Runquery($db_link,$DB_name,$sql);
            }
            $sql='UPDATE clientes_visitas SET Data_Visita="'.date("Y-m-d H:i:s").'" WHERE ID_ORG="'.$cli_id.'" AND Enviado=0';
            $res=Runquery($db_link,$DB_name,$sql);
            $rs->db_close();
        }
    }
    $rs2->db_close();
}
function unsubscriveProdutoNovidades($cli_id)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $sql='UPDATE clientes SET Notify_novidades="0" WHERE ID="'.$cli_id.'"';
    $res=Runquery($db_link,$DB_name,$sql);
    return $res;
}
function notificarProdutoNovidades()
{
    global $DB_host,$DB_user,$DB_pass,$DB_name, $site_http_url;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new Dataobj;
    $rs2=new Dataobj;
    // PRODUTOS NOVOS ADICIONADOS NO DIA ANTERIOR
    $sql='SELECT P.ID FROM products P INNER JOIN filters_products pfo ON pfo.prod_id=P.ID INNER JOIN products_objs AS PO ON PO.obj_type_id=P.ID
			WHERE
				P.Aproved=1
				AND P.Data_criacao=DATE(NOW()-INTERVAL 1 DAY)
				AND P.img_ext<>""
				AND PO.obj_stock > 0
			GROUP BY P.ID';
    $rs2->db_open($db_link,$DB_name,$sql);
    if(!$rs2->EOF())
    {
        while (!$rs2->EOF())
        {
            $prds1[]=$rs2->db_data['ID'];
            $rs2->db_next();
        }
    }
    $rs2->db_close();

    // PRODUTOS PROMOCAO
    $sql='SELECT
				P.ID, (PO.obj_pvp-PO.obj_promo) AS df
			FROM
				products P
				INNER JOIN filters_products pfo ON pfo.prod_id=P.ID
				INNER JOIN products_objs AS PO ON PO.obj_type_id=P.ID
			WHERE
				P.Aproved=1
				AND P.img_ext<>""
				AND PO.obj_promo_ativo=1 AND PO.obj_promo > 0 AND PO.obj_pvp > PO.obj_promo
				AND PO.obj_stock > 0
			GROUP BY
				P.ID
			ORDER BY df DESC
			LIMIT 6';
    $rs2->db_open($db_link,$DB_name,$sql);
    if(!$rs2->EOF())
    {
        while (!$rs2->EOF())
        {
            $prds2[]=$rs2->db_data['ID'];
            $rs2->db_next();
        }
    }
    $rs2->db_close();

    // PRODUTOS OUTLET
    $sql='SELECT P.ID FROM products P INNER JOIN filters_products pfo ON pfo.prod_id=P.ID INNER JOIN products_objs AS PO ON PO.obj_type_id=P.ID
			WHERE
				P.Aproved=1
				AND P.img_ext<>""
				AND PO.obj_outlet=1
				AND PO.obj_stock > 0
			GROUP BY
				P.ID
			LIMIT 6';
    $rs2->db_open($db_link,$DB_name,$sql);
    if(!$rs2->EOF())
    {
        while (!$rs2->EOF())
        {
            $prds3[]=$rs2->db_data['ID'];
            $rs2->db_next();
        }
    }
    $rs2->db_close();
    $produtos='';
    $n_prods=0;
    $n_prods_l=3;
    $n_prods_l2=0;
    if(isset($prds1) && !empty($prds1))
    {
        $produtos .= '<table cellpadding="0" width="100%" height="40" cellspacing="0" border="0" bgcolor="#54b4ff">';
        $produtos .= '<tr><td width="20"></td><td style="color:#FFF;font-size:16px;font-family:verdana,sans-serif;font-weight:bold">Produtos Novos</td><td width="20"></td></tr>';
        $produtos .= '</tr></table>';
        $produtos .= '<table cellpadding="0" width="100%" height="40" cellspacing="0" border="0">';
        $produtos .= '<tr><td width="20"></td><td style="color:#c0c0c0;font-size:13px;font-family:verdana,sans-serif;font-weight:bold">Adicionamos mais produtos ao nosso catálogo</td><td width="20"></td></tr>';
        $produtos .= '</tr></table>';
        $produtos .= '<table cellpadding="0" height="100%" cellspacing="0" border="0" align="center" style="margin-left:auto;margin-right:auto;">';
        $produtos .= '<tr>';

        for ($a=0; $a < count($prds1); $a++)
        {
            $ID_Prod=$prds1[$a];
            $prods=get_produtc_info($ID_Prod, '', 1);//o parametro 1 serve para regerar as imagens
            if($prods)
            {
                $url=$site_http_url.seo('pages.php?page_id='.$GLOBALS['products_page_id'], $ID_Prod, 2);
                $prods=get_produtc_info($ID_Prod);
                if($prods)
                {
                    if(!substr_count($prods['prd_imgm_new'], $site_http_url))
                    {
                        $img=$site_http_url.$prods['prd_imgm_new'];
                    }
                    else
                    {
                        $img=$prods['prd_imgm_new'];
                    }
                    if($n_prods_l == $n_prods_l2)
                    {
                        $produtos .= '</tr><tr><td height="10" colspan="10"><img src="'.$site_http_url.'layout/default/images/blank.gif" width="10" height="10" border="0" /></td></tr><tr>';
                        $n_prods_l2=0;
                    }
                    // if($n_prods_l2 > 0){$produtos.='<td width="10"><img src="'.$site_http_url.'layout/default/blank.gif" width="10" height="300" border="0" /></td>';}
                    $produtos .= '<td width="200" height="300" style="height:300px">
					<table width="200" height="300" cellpadding="0" cellspacing="0" style="height:300px">
					<tr>
					   <td align="center" height="200">
					       <a href="'.$url.'" target="_blank" style="color:#000;text-decoration:none"><img src="'.$img.'" border="0" /></a>
					   </td>
					</tr>
					<tr>
					   <td align="center" height="50"><a href="'.$url.'" target="_blank" style="color:#000;text-decoration:none; font-size:12px;font-family:verdana,sans-serif">'.$prods['prod_nome'].'</a></td>
					</tr>
					<tr>
					   <td align="center" height="25" style="font-size:14px;font-family:verdana,sans-serif;color:#54b4ff;font-weight:bold">'.numberformat($prods['prod_precos']['PVP']).'</td>
					</tr>
					<tr>
					   <td align="center" height="25"><a href="'.$site_http_url.'p'.$GLOBALS['cart_purchage_id'].'-carrinho-finalizar-pt?prod_add='.$ID_Prod.'&prod_obj_id='.$ID_Prod.'" target="_blank" style="color:#000;text-decoration:none"><img src="'.$site_http_url.'layout/default/cart_n.png" border="0" /></a></td>
					</tr>
					</table>
					</td>';
                    $n_prods_l2++;
                }
                $n_prods++;
            }
        }
        $produtos .= '</tr></table>';
    }
    $n_prods_l=3;
    $n_prods_l2=0;
    if(isset($prds2) && !empty($prds2))
    {
        $produtos .= '<br /><table cellpadding="0" width="100%" height="40" cellspacing="0" border="0" bgcolor="#54b4ff">';
        $produtos .= '<tr><td width="20"></td><td style="color:#FFF;font-size:16px;font-family:verdana,sans-serif;font-weight:bold">Produtos em Promoção</td><td width="20"></td></tr>';
        $produtos .= '</tr></table>';

        $produtos .= '<table cellpadding="0" width="100%" height="40" cellspacing="0" border="0">';
        $produtos .= '<tr><td width="20"></td><td style="color:#c0c0c0;font-size:13px;font-family:verdana,sans-serif;font-weight:bold">Baixamos ainda mais os preços, oferendo-lhe ainda uma vasta gama de produtos ao melhor preço</td><td width="20"></td></tr>';
        $produtos .= '</tr></table>';

        $produtos .= '<table cellpadding="0" height="100%" cellspacing="0" border="0" align="center" style="margin-left:auto;margin-right:auto;">';
        $produtos .= '<tr>';

        for ($a=0;$a < count($prds2);$a++)
        {
            $ID_Prod=$prds2[$a];
            $prods=get_produtc_info($ID_Prod,'',1);//o parametro 1 serve para regerar as imagens
            if($prods)
            {
                $url=$site_http_url.seo('pages.php?page_id='.$GLOBALS['products_page_id'],$ID_Prod,2);
                $prods=get_produtc_info($ID_Prod);
                if($prods)
                {
                    if(!substr_count($prods['prd_imgm_new'], $site_http_url))
                    {
                        $img=$site_http_url.$prods['prd_imgm_new'];
                    }
                    else
                    {
                        $img=$prods['prd_imgm_new'];
                    }
                    if($n_prods_l == $n_prods_l2)
                    {
                        $produtos .= '</tr><tr><td height="10" colspan="10"><img src="'.$site_http_url.'layout/default/blank.gif" width="10" height="10" border="0" /></td></tr><tr>';
                        $n_prods_l2=0;
                    }
                    $percdesconto=getDescontoAplicado($prods['prod_precos']['PRECO_ANTIGO'], $prods['prod_precos']['PVP']);
                    $produtos .= '<td width="200" height="300" style="height:300px">
					<br />
					<table width="200" height="300" cellpadding="0" cellspacing="0" style="height:300px">
					<tr>
					   <td align="right">
					       <table width="70" height="20" cellpadding="0" cellspacing="0" bgcolor="#ec600b" align="right">
					       <tr>
					           <td align="center" style="color:#FFFFFF;font-size:12px">-'.$percdesconto.'%</td>
					       </tr>
					       </table>
					   </td>
					</tr>
					<tr>
					   <td align="center" height="200"><a href="'.$url.'" target="_blank" style="color:#000;text-decoration:none"><img src="'.$img.'" border="0" /></a></td>
					</tr>
					<tr>
					   <td align="center" height="50"><a href="'.$url.'" target="_blank" style="color:#000;text-decoration:none; font-size:12px;font-family:verdana,sans-serif">'.$prods['prod_nome'].'</a></td>
					</tr>
					<tr>
					   <td align="center" height="25" style="font-size:12px;font-family:verdana,sans-serif;color:#c0c0c0;font-weight:bold;text-decoration:line-through;">'.numberformat($prods['prod_precos']['PRECO_ANTIGO']).'</td>
					</tr>
					<tr>
					   <td align="center" height="25" style="font-size:14px;font-family:verdana,sans-serif;color:#54b4ff;font-weight:bold">'.numberformat($prods['prod_precos']['PVP']).'</td>
					</tr>
					<tr>
					   <td align="center" height="25"><a href="'.$site_http_url.'p'.$GLOBALS['cart_purchage_id'].'-carrinho-finalizar-pt?prod_add='.$ID_Prod.'&prod_obj_id='.$ID_Prod.'" target="_blank" style="color:#000;text-decoration:none"><img src="'.$site_http_url.'layout/default/cart_n.png" border="0" /></a></td>
					</tr>
					</table>
					</td>';
                    $n_prods_l2++;
                }
                $n_prods++;
            }
        }
        $produtos .= '</tr></table>';
    }
    $n_prods_l=3;
    $n_prods_l2=0;
    if((isset($prds1) && !empty($prds1) || isset($prds2) && !empty($prds2)) && isset($prds3) && !empty($prds3))
    {
        $produtos .= '<br /><table cellpadding="0" width="100%" height="40" cellspacing="0" border="0" bgcolor="#54b4ff">';
        $produtos .= '<tr><td width="20"></td><td style="color:#FFF;font-size:16px;font-family:verdana,sans-serif;font-weight:bold">Produtos Outlet</td><td width="20"></td></tr>';
        $produtos .= '</tr></table>';

        $produtos .= '<table cellpadding="0" height="100%" cellspacing="0" border="0" align="center" style="margin-left:auto;margin-right:auto;">';
        $produtos .= '<tr>';

        for ($a=0; $a < count($prds3); $a++)
         {
            $ID_Prod=$prds3[$a];
            $prods=get_produtc_info($ID_Prod, '', 1);//o parametro 1 serve para regerar as imagens

            if($prods)
            {
                $url=$site_http_url.seo('pages.php?page_id='.$GLOBALS['products_page_id'], $ID_Prod, 2);
                $prods=get_produtc_info($ID_Prod);
                if($prods)
                {
                    if(!substr_count($prods['prd_imgm_new'], $site_http_url))
                    {
                        $img=$site_http_url.$prods['prd_imgm_new'];
                    }
                    else
                    {
                        $img=$prods['prd_imgm_new'];
                    }
                    if($n_prods_l == $n_prods_l2)
                    {
                        $produtos .= '</tr><tr><td height="10" colspan="10"><img src="'.$site_http_url.'layout/default/blank.gif" width="10" height="10" border="0" /></td></tr><tr>';
                        $n_prods_l2=0;
                    }
                    $produtos .= '<td width="200" height="300" style="height:300px">
					<table width="200" height="300" cellpadding="0" cellspacing="0" style="height:300px">
					<tr>
					   <td align="center" height="200"><a href="'.$url.'" target="_blank" style="color:#000;text-decoration:none"><img src="'.$img.'" border="0" /></a></td>
					</tr>
					<tr>
					   <td align="center" height="50"><a href="'.$url.'" target="_blank" style="color:#000;text-decoration:none; font-size:12px;font-family:verdana,sans-serif">'.$prods['prod_nome'].'</a></td>
					</tr>
					<tr>
					   <td align="center" height="25" style="font-size:14px;font-family:verdana,sans-serif;color:#54b4ff;font-weight:bold">'.numberformat($prods['prod_precos']['PVP']).'</td>
					</tr>
					<tr>
					   <td align="center" height="25"><a href="'.$site_http_url.'p'.$GLOBALS['cart_purchage_id'].'-carrinho-finalizar-pt?prod_add='.$ID_Prod.'&prod_obj_id='.$ID_Prod.'" target="_blank" style="color:#000;text-decoration:none"><img src="'.$site_http_url.'layout/default/cart_n.png" border="0" /></a></td>
					</tr>
					</table>
					</td>';
                    $n_prods_l2++;
                }
                $n_prods++;
            }
        }
        $produtos .= '</tr></table>';
    }
    if($n_prods > 0)
    {
        $sql='SELECT ID,Nome,Apelido,Email FROM clientes WHERE Newsletter="1" AND Data_Notify_novidades < "'.date('Y-m-d').'"';
        $rs->db_open($db_link,$DB_name,$sql);
        while (!$rs->EOF())
        {
            $ID=$rs->db_data['ID'];
            $Nome=$rs->db_data['Nome'];
            $Apelido=$rs->db_data['Apelido'];
            $Email=$rs->db_data['Email'];

            $body=array('[name]' => $Nome, '[surname]' => $Apelido, '[produtos]' => $produtos);
            $message=template_email($GLOBALS['template_notificacaonovidades'], $body, '500px', 1);
            $message['body'] .= get_disclaimer(1, $ID, '', $Email, 0, 0, 2);
            if(send_email($Email, $message['subject'], $message['body']))
            {
                $sql='UPDATE clientes SET Data_Notify_novidades="'.date("Y-m-d").'" WHERE ID='.$ID;
                $res=Runquery($db_link,$DB_name,$sql);
                registarLogCron('Notificação de Novidades ao cliente (produtos) - '.$Nome.' - '.$Apelido, $sql, date("Y-m-d H:i:s"));
            }
            $rs->db_next();
        }
        $rs->db_close();
    }
}
function registarTentativaLogin()
{
    global $DB_host,$DB_user,$DB_pass,$DB_name;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $ip=getenv("REMOTE_ADDR");
    $params=array(':ip' => $ip);
    $res=Runquery($db_link,$DB_name,'INSERT INTO clientes_ddos(Data,IP) VALUES (NOW(),:ip)', $params);
}
function limparTentativaLogin()
{
    global $DB_host,$DB_user,$DB_pass,$DB_name;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $ip=getenv("REMOTE_ADDR");
    $params=array(':ip' => $ip);
    $res=Runquery($db_link,$DB_name,'DELETE FROM clientes_ddos WHERE IP=:ip', $params);
}
function validarTentativaLogin()
{
    global $DB_host,$DB_user,$DB_pass,$DB_name;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $ip=getenv("REMOTE_ADDR");
    $rs=new Dataobj;
    $sql='SELECT count(*) AS NumTentativas FROM clientes_ddos WHERE IP="'.$ip.'" AND (Data>=DATE_SUB(NOW(),INTERVAL 1 MINUTE) AND Data<=NOW())';
    $rs->db_open($db_link,$DB_name,$sql);
    $num=0;
    if(!$rs->EOF())$num=$rs->db_data['NumTentativas'];
    $rs->db_close();
    return $num;
}
function getEnderecos($IdCliente,$Tipo=1,$IdEndereco=0)
{
    $db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
    $rs=new Dataobj();
    $sql="SELECT * FROM clientes_enderecos WHERE ID_Cliente='$IdCliente' ";
    if($IdEndereco > 0)$sql .= " AND ID='$IdEndereco' ";
    if($Tipo == 1) $sql .= " ORDER BY ID ASC LIMIT 1 ";
    else $sql .= " ORDER BY ID ASC ";
    $rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
    return $rs;
}
function showFormLivroEnderecosz($idEndereco=0, $apagar=0)
{
    $db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
    $rs_paises=new Dataobj();
    $dados=cliente_logado();
    $html='<div class="row">';
    $html .= '<div class="col-md-12">';
    $html .= '<script type="text/javascript">';
    $html .= "function validarEndereco()
    {
        var cor='#ffffe5';
        var cor2='#FFFFFF';
        var campos_obrigatorios;
        campos_obrigatorios=new Array('m_nome','m_apelido', 'm_morada', 'm_telefone', 'm_codigo_postal', 'm_cidade', 'm_pais', 'm_contribuinte');
        var campos_preenchidos;
        var i=0;
        while (i < campos_obrigatorios.length)
        {
            $('input[name=\"' + campos_obrigatorios[i] + '\"]').css('background-color', cor2);
            i=i + 1;
        }
        i=0;
        while (i < campos_obrigatorios.length)
        {
            if(document.forms['enderecoform'].elements[campos_obrigatorios[i]].value == \"\")
            {
                campos_preenchidos=1;
                $('input[name=\"' + campos_obrigatorios[i] + '\"]').css('background-color', cor);
            }
            i=i + 1;
        }
        if(campos_preenchidos == 1)
        {
            Apprise('".getLinguas('key_camposobr',$GLOBALS['site_lang'])."');
            return false;
        }
        return true;
    }";
    $html .= '</script>';
    $html .= '<form method="post" onsubmit="return validarEndereco();" name="enderecoform">';
    $titulo='ADICIONAR NOVO ENDEREÇO';
    $nomeBtn='CRIAR';
    $showForm=0;
    $designacao='';
    $nome='';
    $apelido='';
    $morada='';
    $telefone='';
    $codigo_postal='';
    $cidade='';
    $contribuinte='';
    $pais=165;
    if($idEndereco == 0)
    {
		$nome=isset($_POST['m_nome']) ? $_POST['m_nome'] : $rs->db_data['Nome'];
        $apelido=isset($_POST['m_apelido']) ? $_POST['m_apelido'] : $rs->db_data['Apelido'];
        $morada=isset($_POST['m_morada']) ? $_POST['m_morada'] : '';
        $telefone=isset($_POST['m_telefone']) ? $_POST['m_telefone'] : '';
        $codigo_postal=isset($_POST['m_codigo_postal']) ? $_POST['m_codigo_postal'] : '';
        $cidade=isset($_POST['m_cidade']) ? $_POST['m_cidade'] : '';
        $contribuinte=isset($_POST['m_contribuinte']) ? $_POST['m_contribuinte'] : '';
        $pais=isset($_POST['m_pais']) ? $_POST['m_pais'] : 165;
        if(
            isset($_POST['m_nome']) && $_POST['m_nome'] != '' &&
            isset($_POST['m_apelido']) && $_POST['m_apelido'] != '' &&
            isset($_POST['m_morada']) && $_POST['m_morada'] != '' &&
            isset($_POST['m_telefone']) && $_POST['m_telefone'] != '' &&
            isset($_POST['m_codigo_postal']) && $_POST['m_codigo_postal'] != '' &&
            isset($_POST['m_cidade']) && $_POST['m_cidade'] != '' &&
			isset($_POST['m_contribuinte']) && $_POST['m_contribuinte'] != '' &&
            isset($_POST['m_pais']) && $_POST['m_pais'] > 0
        ) {
            $sql="INSERT INTO clientes_enderecos(ID_Cliente,Nome,Apelido,Morada,Codigo_Postal,Cidade,Pais,Telefone,Nif) VALUES ('{$dados['cli_id']}','$nome','$apelido','$morada','$codigo_postal','$cidade','$pais','$telefone','$contribuinte')";
            Runquery($db_link,$GLOBALS['DB_name'], $sql);
            $showForm=2;
        }
        else
        {
            $showForm=1;
        }
    }
    else
    {
        $rs=getEnderecos($dados['cli_id'], 1, $idEndereco);
        if(!$rs->EOF())
        {
            $nome=isset($_POST['m_nome']) ? $_POST['m_nome'] : $rs->db_data['Nome'];
            $apelido=isset($_POST['m_apelido']) ? $_POST['m_apelido'] : $rs->db_data['Apelido'];
            $morada=isset($_POST['m_morada']) ? $_POST['m_morada'] : $rs->db_data['Morada'];
            $telefone=isset($_POST['m_telefone']) ? $_POST['m_telefone'] : $rs->db_data['Telefone'];
            $codigo_postal=isset($_POST['m_codigo_postal']) ? $_POST['m_codigo_postal'] : $rs->db_data['Codigo_Postal'];
            $cidade=isset($_POST['m_cidade']) ? $_POST['m_cidade'] : $rs->db_data['Cidade'];
            $pais=isset($_POST['m_pais']) ? $_POST['m_pais'] : $rs->db_data['Pais'];
            $contribuinte=isset($_POST['m_contribuinte']) ? $_POST['m_contribuinte'] : $rs->db_data['Nif'];
            if($apagar == 1)
            {
                $titulo='APAGAR ENDEREÇO';
                $nomeBtn='APAGAR';
            }
            else
            {
                $titulo='EDITAR ENDEREÇO';
                $nomeBtn='ALTERAR';
            }
            if(
                isset($_POST['m_nome']) && $_POST['m_nome'] != '' &&
                isset($_POST['m_apelido']) && $_POST['m_apelido'] != '' &&
                isset($_POST['m_morada']) && $_POST['m_morada'] != '' &&
                isset($_POST['m_telefone']) && $_POST['m_telefone'] != '' &&
                isset($_POST['m_codigo_postal']) && $_POST['m_codigo_postal'] != '' &&
                isset($_POST['m_cidade']) && $_POST['m_cidade'] != '' &&
                isset($_POST['m_contribuinte']) && $_POST['m_contribuinte'] != '' &&
                isset($_POST['m_pais']) && $_POST['m_pais'] > 0
            ) {
                if($apagar == 1)
                {
                    $sql="DELETE FROM clientes_enderecos WHERE ID_Cliente='{$dados['cli_id']}' AND ID='$idEndereco'";
                    Runquery($db_link,$GLOBALS['DB_name'],$sql);
                    $showForm=2;
                }
                else
                {
                    $sql="UPDATE clientes_enderecos SET ID_Cliente='{$dados['cli_id']}',Designacao='$designacao',Morada='$morada',Codigo_Postal='$codigo_postal',Cidade='$cidade',Pais='$pais',Telefone='$telefone' WHERE ID='$idEndereco'";
                    $res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
                    $rs3=getEnderecos($dados['cli_id'],1);
                    if($res && $rs3->db_data['ID'] == $idEndereco)
                    {
                        $sql="UPDATE clientes SET Empresa='$designacao',Morada='$morada',Codigo_Postal='$codigo_postal',Cidade='$cidade',Pais='$pais',Telefone='$telefone' WHERE ID='{$dados['cli_id']}'";
                        Runquery($db_link,$GLOBALS['DB_name'], $sql);
                    }
                    $showForm=2;
                }
            }
            else
            {
                $showForm=1;
            }
        }
        $rs->db_close();
    }
    if($showForm == 0)
    {
        $html .= '<div class="row">';
        $html .= '<div class="col-md-12">Endereço inexistente<br><br></div>';
        $html .= '<div class="col-md-12">';
        $html .= '<input type="button" value="&lt; VOLTAR" class="button3" onclick="window.open(\''.seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'\',\'_self\')">';
        $html .= '</div>';
        $html .= '</div>';
    }
    elseif($showForm == 1)
    {
        $html .= '<div class="subtitulo_d">'.$titulo.'</div>';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-6">';
		$html .= '<div class="form-group">';
		$html .= '<label for="m_nome">Nome <span class="mandatory">*</span></label>';
		$html .= '<input type="text" class="form-control" id="m_nome" name="m_nome" value="'.$nome.'">';
		$html .= '</div>';
		$html .= '</div>';

		$html .= '<div class="col-md-6">';
		$html .= '<div class="form-group">';
		$html .= '<label for="m_apelido">Apelido <span class="mandatory">*</span></label>';
		$html .= '<input type="text"  class="form-control" id="m_apelido" name="m_apelido" value="'.$apelido.'">';
		$html .= '</div>';
		$html .= '</div>';

        $html .= '<div class="col-md-6">';
        $html .= '<div class="form-group">';
        $html .= '<label for="m_morada">Morada <span class="mandatory">*</span></label>';
        $html .= '<input type="text" class="form-control" id="m_morada" name="m_morada" value="'.$morada.'">';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="col-md-6">';
        $html .= '<div class="form-group">';
        $html .= '<label for="m_codigo_postal">Código Postal <span class="mandatory">*</span></label>';
        $html .= '<input type="text" class="form-control" id="m_codigo_postal" name="m_codigo_postal" value="'.$codigo_postal.'">';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="col-md-6">';
        $html .= '<div class="form-group">';
        $html .= '<label for="m_cidade">Cidade <span class="mandatory">*</span></label>';
        $html .= '<input type="text" class="form-control" id="m_cidade" name="m_cidade" value="'.$cidade.'">';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="col-md-6">';
        $html .= '<div class="form-group">';
        $html .= '<label for="m_pais">'.getLinguas('key_pais',$GLOBALS['site_lang']).' <span class="mandatory">*</span></label>';
        $html .= '<select class="chzn-select" name="m_pais" id="m_pais" data-live-search="true">';
        $sql="SELECT c.zone_id,czl.zone_name FROM country_zones AS c INNER JOIN country_zones_lang AS czl ON c.zone_id=czl.zone_id WHERE czl.zone_lang='{$GLOBALS['site_lang']}' AND c.zone_visible=1 ORDER BY czl.zone_name ASC";
        $rs_paises->db_open($db_link,$GLOBALS['DB_name'], $sql);
        while (!$rs_paises->EOF())
        {
            $zone_name=$rs_paises->db_data['zone_name'];
            $zone_id=$rs_paises->db_data['zone_id'];
            $html .= '<option value="'.$zone_id.'" ';
            if($pais == $zone_id)
            {
                $html .= 'selected="selected"';
            }
            $html .= '>'.$zone_name.'</option>';
            $rs_paises->db_next();
        }
        $rs_paises->db_close();
        $html .= '</select>';
        $html .= '</div>';
        $html .= '</div>';

		$html .= '<div class="col-md-6">';
        $html .= '<div class="form-group">';
        $html .= '<label for="m_contribuinte">Nº Contribuinte <span class="mandatory">*</span></label>';
        $html .= '<input type="text" class="form-control" id="m_contribuinte" name="m_contribuinte" value="'.$contribuinte.'">';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="col-md-6">';
        $html .= '<div class="form-group">';
        $html .= '<label for="m_telefone">Telefone <span class="mandatory">*</span></label>';
        $html .= '<input type="text" class="form-control" id="m_telefone" name="m_telefone" value="'.$telefone.'">';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<input style="margin-right:10px" type="button" value="&lt; VOLTAR" class="button3" onclick="window.open(\''.seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'\',\'_self\')"><input type="submit" value="'.$nomeBtn.'" class="button3">';
    }
    else
    {
        $html .= '<script type="text/javascript">window.open(\''.seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'\',\'_self\');</script>';
    }
    $html .= '</form>';
    $html .= '</div>';
    $html .= '</div>';
    return $html;
}
function showListLivroEnderecosaaa()
{
    $dados=cliente_logado();
    $html='<div class="row">';
    $html .= '<div class="col-md-12">';
    $html .= '<div class="subtitulo_d">ENDEREÇO DE FACTURAÇÃO</div>';
    $rs=getEnderecos($dados['cli_id']);
    if(!$rs->EOF())
    {
        $pais=country_name($rs->db_data['Pais']);
        $html .= '<div class="form-group">';
        $html .= "{$rs->db_data['Nome']} {$rs->db_data['Apelido']}<br>{$rs->db_data['Morada']}<br>{$rs->db_data['Codigo_Postal']}<br>{$rs->db_data['Cidade']}<br>{$pais}<br>{$rs->db_data['Nif']}<br>{$rs->db_data['Telefone']}";
        $html .= '</div>';
        $html .= '<div class="form-group">';
        $html .= '<input type="button" value="ALTERAR" class="button3" onclick="window.open(\''.seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'?id_endereco='.$rs->db_data['ID'].'\',\'_self\')">';
        $html .= '</div>';
    }
    $rs->db_close();

    $html .= '</div>';
    $html .= '<div class="col-md-12"><br>';
    $html .= '<div class="subtitulo_d">ENDEREÇOS ADICIONAIS</div>';

    $pos=0;
    $rs=getEnderecos($dados['cli_id'], 2);
    if(!$rs->EOF())
    {
        while (!$rs->EOF())
        {
            if($pos > 0)
            {
                $pais=country_name($rs->db_data['Pais']);
                $html .= '<div class="row">';
                $html .= '<div class="col-md-12">';
                $html .= "{$rs->db_data['Nome']} {$rs->db_data['Apelido']}<br>{$rs->db_data['Morada']}<br>{$rs->db_data['Codigo_Postal']}<br>{$rs->db_data['Cidade']}<br>{$pais}<br>{$rs->db_data['Nif']}<br>{$rs->db_data['Telefone']}<br><br>";
                $html .= '</div>';
                $html .= '<div class="col-md-12">';
                $html .= '<input style="margin-right:10px" type="button" value="ALTERAR" class="button3" onclick="window.open(\''.seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'?id_endereco='.$rs->db_data['ID'].'\',\'_self\')"><input type="button" value="APAGAR" class="button3" onclick="window.open(\''.seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'?id_endereco='.$rs->db_data['ID'].'&apagar=1\',\'_self\')">';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '<br>';
            }
            $rs->db_next();
            $pos++;
        }
    }
    $rs->db_close();
    $html .= '<input type="button" value="CRIAR" class="button3" onclick="window.open(\''.seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'?id_endereco=0\',\'_self\')">';
    $html .= '</div>';
    $html .= '</div>';
    return $html;
}
function loadSubscriveNewsletter()
{
    $html='';
    $db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
    $rs=new Dataobj();
    $sql='SELECT * FROM clientes WHERE ID="'.$GLOBALS['cli_id'].'" AND Ativo=1 AND Token="'.$GLOBALS['cli_token'].'"';
    $rs->db_open($db_link,$GLOBALS['DB_name'], $sql);
    if(!$rs->EOF())
    {
        $m_nome=$rs->db_data['Nome'];
        $m_apelido=$rs->db_data['Apelido'];
        $m_newsletter=$rs->db_data['Newsletter'];
        $m_email=$rs->db_data['Email'];
        $m_morada=$rs->db_data['Morada'];
        $m_codpos=$rs->db_data['Codigo_Postal'];
        $m_telefone=$rs->db_data['Telefone'];
        $m_contribuinte=$rs->db_data['Nif'];

        if(isset($_POST['m_newsletter']))
        {
            $m_newsletter=$_POST['m_newsletter'];

            $nl1=new DataObj;
            $nl2=new DataObj;
            if($m_newsletter == 1)
            {
                $sql='DELETE FROM nl_emails_removidos WHERE Email="'.$m_email.'"';
                $res=Runquery($db_link,$GLOBALS['DB_name'], $sql);

                $sql='SELECT ID FROM nl_grupos WHERE Visivel=1';
                $nl2->db_open($db_link,$GLOBALS['DB_name'], $sql);
                if(!$nl2->EOF())
                {
                    $id_grupo=$nl2->db_data['ID'];
                }
                else
                {
                    $id_grupo=0;
                }
                $nl2->db_close();

                $sql='SELECT ID FROM nl_emails_registados WHERE Email="'.$m_email.'"';
                $nl1->db_open($db_link,$GLOBALS['DB_name'], $sql);
                if(!$nl1->EOF())
                {
                    $nlid=$nl1->db_data['ID'];
                    $sql='UPDATE nl_emails_registados SET ID_ORG="'.$id_grupo.'",Email="'.$m_email.'",Nome="'.$m_nome.'",Apelido="'.$m_apelido.'",Morada="'.$m_morada.'",CodPos="'.$m_codpos.'",N_Contribuint="'.$m_contribuinte.'",Telemovel="'.$m_telefone.'",data_hora="'.date("Y-m-d H:i:s").'" WHERE ID='.$nlid;
                    $res=Runquery($db_link,$GLOBALS['DB_name'], $sql);
                }
                else
                {
                    $sql="INSERT INTO nl_emails_registados (ID_ORG,Nome,Email,data_hora,Apelido,Morada,CodPos,N_Contribuint,Telemovel) values ('$id_grupo','$m_nome','$m_email','".date("Y-m-d H:i:s")."','".$m_apelido."','".$m_morada."','".$m_codpos."','".$m_contribuinte."','".$m_telefone."')";
                    $res=Runquery($db_link,$GLOBALS['DB_name'], $sql);
                }
                $nl1->db_close();
            }
            else
            {
                $sql='DELETE FROM nl_emails_registados WHERE Email="'.$m_email.'"';
                $res=Runquery($db_link,$GLOBALS['DB_name'], $sql);

                $sql='SELECT ID FROM nl_grupos WHERE Visivel=1';
                $nl2->db_open($db_link,$GLOBALS['DB_name'], $sql);
                if(!$nl2->EOF())
                {
                    $id_grupo=$nl2->db_data['ID'];
                }
                else
                {
                    $id_grupo=0;
                }
                $nl2->db_close();

                $sql='SELECT ID FROM nl_emails_removidos WHERE Email="'.$m_email.'"';
                $nl1->db_open($db_link,$GLOBALS['DB_name'], $sql);
                if(!$nl1->EOF())
                {
                    $nlid=$nl1->db_data['ID'];
                    $sql='UPDATE nl_emails_removidos SET ID_ORG="'.$id_grupo.'",Email="'.$m_email.'",Nome="'.$m_nome.'",Apelido="'.$m_apelido.'",Morada="'.$m_morada.'",CodPos="'.$m_codpos.'",N_Contribuinte="'.$m_contribuinte.'",Telemovel="'.$m_telefone.'",DataRemocao="'.date("Y-m-d H:i:s").'" WHERE ID='.$nlid;
                    $res=Runquery($db_link,$GLOBALS['DB_name'], $sql);
                }
                else
                {
                    $sql="INSERT INTO nl_emails_removidos (ID_ORG,Nome,Email,DataRemocao,Apelido,Morada,CodPos,N_Contribuinte,Telemovel) values ('$id_grupo','$m_nome','$m_email','".date("Y-m-d")."','".$m_apelido."','".$m_morada."','".$m_codpos."','".$m_contribuinte."','".$m_telefone."')";
                    $res=Runquery($db_link,$GLOBALS['DB_name'], $sql);
                }
                $nl1->db_close();
            }
        }
        $html .= '<form method="post">';
        $html .= '<div class="form-group">';
        $html .= '<label class="radio-inline">';
        $html .= '<input type="radio" name="m_newsletter" id="m_newsletter1" value="1" '.($m_newsletter == 1 ? 'checked="checked"' : '').' onchange="this.form.submit();"> Subscrição activa';
        $html .= '</label>';
        $html .= '<label class="radio-inline">';
        $html .= '<input type="radio" name="m_newsletter" id="m_newsletter0" value="0" '.($m_newsletter == 0 ? 'checked="checked"' : '').' onchange="this.form.submit();"> Subscrição inactiva';
        $html .= '</label>';
        $html .= '</div>';
        $html .= '</form>';
    }
    $rs->db_close();
    return $html;
}
function show_addthis()
{
    $db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
    $rs=new Dataobj();
    $return='';
    $sql='SELECT addthis_id, addthis_ativo FROM modulos_config_site';
    $rs->db_open($db_link,$GLOBALS['DB_name'], $sql);
    if(!$rs->EOF())
    {
        $addthis_ativo=$rs->db_data['addthis_ativo'];
        if($addthis_ativo == 1)
        {
            $addthis_id=$rs->db_data['addthis_id'];
            $return .= '<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid='.$addthis_id.'"></script>';
            $return .='<div class="addthis_inline_share_toolbox"></div>';
        }
    }
    $rs->db_close();
    return $return;
}
