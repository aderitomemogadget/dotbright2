<?php
if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) exit();

function show_area_cliente($class='PC')
{
	$dados_cliente = cliente_logado();

	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$rs3=new Dataobj;
	$return='';
	$return_resp='';
	$sql = 'SELECT
				m.ID, m.catalogo_id, m.tipo_catalogo, m.img_ext, m.TipoMenu, m.URL_Link,
				ml.URL_Nome2
			FROM
				menus_links m
				INNER JOIN	menus_links_lang ml ON ml.ID_PAI=m.ID
			WHERE
				m.TipoMenu = "menu_reservado"
				AND m.Visivel=1
				AND ml.Lang="'.$GLOBALS['site_lang'].'"
				'.(isset($dados_cliente) && is_array($dados_cliente) ? ' AND (Logado = 2 OR Logado = 0)' : ' AND (Logado = 1 OR Logado = 0)').'
			ORDER BY
				m.Ordem ASC';
	$rs->db_open($db_link,$DB_name,$sql);

	$return = '';
	if(!$rs->EOF())
	{
		echo '<div class="tabs" id="tabs-edit">	';
		$tabs = 0;
		while(!$rs->EOF())
		{
			$menu_id = $rs->db_data['ID'];
			$menu_ext = $rs->db_data['img_ext'];
			$catalogo_id = $rs->db_data['catalogo_id'];
			$catalogo_tipo = $rs->db_data['tipo_catalogo']; // CAMPO 'Catálogo de Produtos' MARCAS ou CATEGORIAS
			$menu_tipo = $rs->db_data['TipoMenu']; // SELECT BOX BACKOFFICE 'Tipo Menu'
			$menu_nome = $rs->db_data['URL_Nome2'];
			$menu_url = seo($rs->db_data['URL_Link']);
			if(isset($_GET['show_carteira']) && substr_count($menu_url,'show_carteira')) echo '<div class="tab active" id="tab'.$menu_id.'" onclick="window.open(\''.$menu_url.'\',\'_self\')"><div class="titulo-edit">'.$menu_nome.'</div></div>';
			elseif(isset($_GET['show_compras']) && substr_count($menu_url,'show_compras')) echo '<div class="tab active" id="tab'.$menu_id.'" onclick="window.open(\''.$menu_url.'\',\'_self\')"><div class="titulo-edit">'.$menu_nome.'</div></div>';
			elseif(isset($_GET['show_wishlist']) && substr_count($menu_url,'show_wishlist')) echo '<div class="tab active" id="tab'.$menu_id.'" onclick="window.open(\''.$menu_url.'\',\'_self\')"><div class="titulo-edit">'.$menu_nome.'</div></div>';
			elseif(isset($_GET['show_rmas']) && substr_count($menu_url,'show_rmas')) echo '<div class="tab active" id="tab'.$menu_id.'" onclick="window.open(\''.$menu_url.'\',\'_self\')"><div class="titulo-edit">'.$menu_nome.'</div></div>';
			elseif(!isset($_GET['show_carteira']) && !isset($_GET['show_compras']) && !isset($_GET['show_rmas']) && !isset($_GET['show_wishlist']) && !substr_count($menu_url,'show_wishlist')&& !substr_count($menu_url,'show_compras')&& !substr_count($menu_url,'show_carteira')&& !substr_count($menu_url,'show_rmas')) echo '<div class="tab active" id="tab'.$menu_id.'" onclick="window.open(\''.$menu_url.'\',\'_self\')"><div class="titulo-edit">'.$menu_nome.'</div></div>';
			else echo '<div class="tab" id="tab'.$menu_id.'" onclick="window.open(\''.$menu_url.'\',\'_self\')"><div class="titulo-edit">'.$menu_nome.'</div></div>';
			$tabs = 1;
			$rs->db_next();
		}
		echo '<div class="clear"></div>';
		if(isset($tabs) && $tabs > 0)
		{
			echo '<div class="tabs-content">';
			if(!isset($_GET['show_carteira']) && !isset($_GET['show_compras']) && !isset($_GET['show_rmas']) && !isset($_GET['show_wishlist'])){?> <div class="tab-content active" id="tab-content1"><?php showFormEditarPerfil($dados_cliente);?></div><?php }
			elseif(isset($_GET['show_carteira']) && $_GET['show_carteira'] == 1){?><div class="tab-content active" id="tab-content2"><?php echo loadLivroEnderecos();?></div><?php }
			elseif(isset($_GET['show_compras']) && $_GET['show_compras'] == 1){?><div class="tab-content active" id="tab-content3"><?php echo showMenageOrders($dados_cliente);?></div><?php }
			elseif(isset($_GET['show_wishlist']) && $_GET['show_wishlist'] == 1){?><div class="tab-content active" id="tab-content3"><div class="catprod_wrap"><?php bloco_catalogo();?></div></div><?php }
			elseif(isset($_GET['show_rmas']) && $_GET['show_rmas'] == 1){ } // TODO
			echo '</div>';
		}
		echo '</div>';
	}
	$rs->db_close(1);
}

function show_area_cliente_()
{
	$dados_cliente = cliente_logado();
	if(is_array($dados_cliente))
	{
	?>
	<div class="tabs" id="tabs-edit">

	<div class="tab <?php echo (!isset($_GET['show_carteira']) && !isset($_GET['show_compras']) && !isset($_GET['show_rmas']) ? 'active' : '')?>" id="tab1" onclick="window.open('<?php echo seo('pages.php?page_id=' . $GLOBALS['registo_page_id']);?>','_self')"><div class="titulo-edit">Perfil</div></div>
	<div class="tab <?php echo (isset($_GET['show_carteira']) ? ' active' : '')?>" id="tab2" onclick="window.open('<?php echo seo('pages.php?page_id=' . $GLOBALS['registo_page_id']).'?show_carteira=1';?>','_self')"><div class="titulo-edit">Os meus endereços</div></div>
	<div class="tab <?php echo (isset($_GET['show_compras']) ? ' active' : '')?>" id="tab3" onclick="window.open('<?php echo seo('pages.php?page_id=' . $GLOBALS['registo_page_id']).'?show_compras=1';?>','_self')"><div class="titulo-edit">As minhas encomendas</div></div>
	<div class="tab <?php echo (isset($_GET['show_rmas']) ? ' active' : '')?>" id="tab4" onclick="window.open('<?php echo seo('pages.php?page_id=' . $GLOBALS['registo_page_id']).'?show_rmas=1';?>','_self')"><div class="titulo-edit">Os meus pedidos de assistência</div></div>



	<div class="clear"></div>

	<div class="tabs-content">
	<?php if(!isset($_GET['show_carteira']) && !isset($_GET['show_compras']) && !isset($_GET['show_rmas'])){?><div class="tab-content active" id="tab-content1"><?php showFormEditarPerfil($dados_cliente);?></div><?php }?>
	<?php if(isset($_GET['show_carteira']) && $_GET['show_carteira'] == 1){?><div class="tab-content active" id="tab-content2"><?php echo loadLivroEnderecos();?></div><?php }?>
	<?php if(isset($_GET['show_compras']) && $_GET['show_compras'] == 1){?><div class="tab-content active" id="tab-content3"><?php showMenageOrders($dados_cliente);?></div><?php }?>
	<?php if(isset($_GET['show_rmas']) && $_GET['show_rmas'] == 1){?><div class="tab-content active" id="tab-content4"><?php //showMenageOrders($dados_cliente);?></div><?php }?>

	</div>
	</div>
	<?php
	}
}
function showFormEditarPerfil()
{
    // RGPD: authenticated account requests and consent preferences.
    require_once __DIR__.'/../../../core/privacy.php';
    store_privacy_account_link();

    $db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
    $dados_cliente = cliente_logado();
    $dbo = new DataObj();
    $sql = "SELECT * FROM clientes WHERE ID='{$dados_cliente['cli_id']}'";
    $dbo->db_open($db_link, $GLOBALS['DB_name'], $sql);
    if (!$dbo->EOF())
	{
        $nome = htmlentities($dbo->db_data['Nome']);
        $apelido = htmlentities($dbo->db_data['Apelido']);
        $email = htmlentities($dbo->db_data['Email']);
        if (isset($_POST['m_nome'])) $nome = htmlentities($_POST['m_nome']);
        if (isset($_POST['m_apelido'])) $apelido = htmlentities($_POST['m_apelido']);
        if (isset($_POST['m_email'])) $email = htmlentities($_POST['m_email']);

        ?>
        <script type="text/javascript">
            function validar_editarperfil() {
                var campos_obrigatorios = ["m_nome", "m_apelido", "m_email"];
                var campos_preenchidos;
                var form_name = "registo_cliente";
                var i = 0;
                while (i < campos_obrigatorios.length) {
                    var $cmp = $('#' + form_name + ' input[name="' + campos_obrigatorios[i] + '"]');
                    $cmp.closest('.form-group').removeClass('has-success');
                    $cmp.closest('.form-group').removeClass('has-error');
                    i = i + 1;
                }
                i = 0;
                while (i < campos_obrigatorios.length) {
                    if (document.forms[form_name].elements[campos_obrigatorios[i]].value == "") {
                        campos_preenchidos = 1;
                        $('#' + form_name + ' input[name="' + campos_obrigatorios[i] + '"]').closest('.form-group').addClass('has-error');
                    }
                    i = i + 1;
                }

                if (document.forms[form_name].elements["m_password"].value != '' || document.forms[form_name].elements["m_password_repete"].value != '') {
                    if (document.forms[form_name].elements["m_password"].value != document.forms[form_name].elements["m_password_repete"].value) {
                        $('#' + form_name + ' input[name="m_password"]').closest('.form-group').addClass('has-error');
                        $('#' + form_name + ' input[name="m_password_repete"]').closest('.form-group').addClass('has-error');
                        new Apprise("<?php echo getLinguas('key_password_repetida',$GLOBALS['site_lang'])?>");
                        return false;
                    }
                }

                if (campos_preenchidos == 1) {
                    new Apprise("<?php echo getLinguas('key_camposobr',$GLOBALS['site_lang']);?>");
                    return false;
                }
                return true;
            }
        </script>

        <div class="row">
            <div class="col-md-12">

                <div class="row">

                    <div class="col-md-12">
                        <form class="editarperfil_form" method="post" name="editarperfil_form" onSubmit="return validar_editarperfil()">
                            <input type="hidden" name="registar_update" value="1"/>
                            <input type="hidden" name="submit1" value="1"/> <br><br>


							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label for="m_email">Email <span class="mandatory">*</span></label>
										<input id="m_email" maxlength="255" autocomplete="off" value="<?php echo $email; ?>" name="m_email" class="form-control" style="" type="text">
									</div>
								</div>


								<div class="clear"></div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="m_nome">Nome <span class="mandatory">*</span></label>
										<input id="m_nome" maxlength="255" autocomplete="off" value="<?php echo $nome; ?>" name="m_nome" class="form-control" style="" type="text">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">

										<label for="m_apelido">Apelido <span class="mandatory">*</span></label>
										<input id="m_apelido" maxlength="255" autocomplete="off" value="<?php echo $apelido; ?>" name="m_apelido" class="form-control" style="" type="text">
									</div>
								</div>

								<div class="clear"></div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="m_password">Password Nova <span class="mandatory">*</span></label>
										  <input type="password" name="m_password" id="m_password" value="" class="form-control" />
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="m_password_repete">Repetir Password Nova <span class="mandatory">*</span></label>
										 <input type="password" name="m_password_repete" id="m_password_repete" value="" class="form-control"/>
									</div>
								</div>
							</div>



                            <div class="form-group text-center">
                                <input type="submit" value="SALVAR ALTERAÇÕES" class="button3">
                            </div>
                            <br><br>
                        </form>
                    </div>

                </div>
            </div>

        </div>
        <?php
    }
    $dbo->db_close();
}
function showMenageOrders($dados)
{

	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo,$from,$cli_id,$page_id,$key_wishlist_notloggedin,$site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;$rs2=new DataObj;$rs3=new Dataobj;
	$i=0;
	if(!$dados)
    {
		echo ($key_wishlist_notloggedin[$site_lang]);
	}
    else
    {

		$url = seo('pages.php?page_id='.$GLOBALS['areacliente_page_id']).'?show_compras=1';


		if(!$from)$from=0;
		$qtd=10;

		if(isset($_GET['id_enc']) && $_GET['id_enc'] > 0)
		{
			 $total = 1;
		}
		else
		{
			$sql_all="Select * from encomendas where ID_Cliente=$cli_id AND Estado > -1";
			$rs->db_open($db_link,$DB_name,$sql_all);
			if(!$rs->EOF()) $total = $rs->db_rows;
			$rs->db_close();
		}


		define('N_PAGINAS', 5);

		if(isset($_GET['id_enc']) && $_GET['id_enc'] > 0) $sql = "SELECT * FROM encomendas where ID_Cliente=$cli_id AND  Estado > -1 AND ID='".$_GET['id_enc']."' Order by ID desc";
		else $sql = "Select * from encomendas where ID_Cliente=$cli_id AND  Estado > -1 Order by ID desc";


		// $rs->bind_params($params);
		$rs->db_open($db_link, $DB_name, $sql);
		$total = $rs->db_rows;
		$rs->db_close();

		$n_pags = ceil($total / N_PAGINAS);
		if(!isset($_GET['pagina'])) $pagina = 1;
		else $pagina = $_GET['pagina'];

		$inicio = $pagina - 1;
		$inicio = $inicio * N_PAGINAS;

		$sql2 = $sql.' LIMIT '.$inicio.', '.N_PAGINAS;
		$rs->db_open($db_link,$DB_name, $sql2);




		// $rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			if($n_pags > 1)
			{
				$return2 = '<div class="clear" style="height:10px"></div><div class="paginacao" style="text-align:right">';

				$p_seo = seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'?show_compras=1';

				if(substr_count($p_seo, '?')) $ash = '&pagina='.($pagina <= 1 ? 1 : ($pagina-1)).'&show_compras=1';	else $ash = '?pagina='.($pagina <= 1 ? 1 : ($pagina-1)).'&show_compras=1';
				$pgn = $p_seo.$ash;
				$return2 .= '<a href="'.$pgn.'" style="display:inline-block;cursor:pointer;padding:0 9px;margin-top: -9px;color:#000;font-size:40px;text-decoration:none" class="col-md"><i class="fa fa-angle-left" aria-hidden="true"></i></a>';


				if($pagina > 2)
				{

					if(substr_count($p_seo, '?')) $ash = '&pagina=1&show_compras=1';	else $ash = '?pagina=1&show_compras=1';
					$pgn = $p_seo.$ash;
					$return2 .= '<a href="'.$pgn.'" style="display:inline-block;cursor:pointer;padding:2px 4px;color:#000;font-size:14px;text-decoration:none" class="col-md">1....</a>';
				}
				$ultimo_num = 0;
				for($pi = ($pagina - 1); $pi < ($pagina + 2); $pi++)
				{
					$arr_pag[] = $pi;
					if(substr_count($p_seo, '?')) $ash = '&pagina='.$pi.'&show_compras=1';	else $ash = '?pagina='.$pi.'&show_compras=1';
					$pgn = $p_seo.$ash;
					$return2 .= ($pi >= 1 && $pi <= $n_pags) ? $pagina == $pi ? ($pi!=1 ? '<div style="font-size:20px;padding:2px 4px;display:inline-block;color:#000">,</div>' : '').'<div style="font-size:14px;color:#FFF;padding:2px 4px;background:#000;display:inline-block" >'.$pi.'</div>' : ($pi!=1 ? '<div style="font-size:20px;padding:2px 4px;display:inline-block;color:#000">,</div>' : '').'<a href="'.$pgn.'" style="display:inline-block;font-size:14px;cursor:pointer;padding:2px 4px;color:#000;text-decoration:none" >'.$pi.'</a>' :'';
					$ultimo_num = ($pi > $n_pags) ? $n_pags : 0;
				}

				if($n_pags!=$ultimo_num && !in_array($n_pags, $arr_pag))
				{

					if(substr_count($p_seo, '?')) $ash = '&pagina='.$n_pags.'&show_compras=1';	else $ash = '?pagina='.$n_pags.'&show_compras=1';
					$pgn = $p_seo.$ash;
					$return2 .= '<div style="font-size:14px;padding:2px;display:inline-block;color:#000">,</div><a href="'.$pgn.'" style="display:inline-block;font-size:14px;cursor:pointer;padding:2px 4px;color:#000;text-decoration:none" >....'.$n_pags.'</a>';
				}

				if(substr_count($p_seo, '?')) $ash = '&pagina='.($pagina >= $n_pags ? $n_pags : ($pagina+1)).'&show_compras=1';	else $ash = '?pagina='.($pagina >= $n_pags ? $n_pags : ($pagina+1)).'&show_compras=1';
				$pgn = $p_seo.$ash;
				$return2 .= '<a href="'.$pgn.'" style="display:inline-block;cursor:pointer;padding:0 4px;margin-top: -9px;color:#000;font-size:40px;text-decoration:none" class="col-md"><i class="fa fa-angle-right" aria-hidden="true"></i></a>';
				$return2 .= '</div>';
			}


			$i = $count = 0;?>
			<script>
			function cancelarEncomenda(id_enc){

				if(confirm("Deseja Cancelar a Encomenda Nº: "+id_enc+"?"))
				{
					return true;
				}
				return false;
			}
			function submitopt(id_enc, enc_token)
			{

				<?php if($GLOBALS['n_enc'] > 0){?>

				Apprise("<?php echo getLinguas('key_prod_cart_on')?>");

				<?php }else{?>
				var empty = '';	var escolha = '';
				$('.opt_subs').each(function(i, obj){ if($(this).val()=='' || $(this).val()==0) empty = 1;});

				if(empty == 1)
				{
					Apprise("<?php echo getLinguas('key_shop_msg_sub')?>");
				}
				else if(confirm("<?php echo getLinguas('key_shop_msg_alterar')?>"))
				{
					//window.open('<?php echo seo('pages.php?page_id='.$GLOBALS['cart_page_id'])?>?id_enc='+id_enc+'&enc_token='+enc_token);
					return true;
				}
				<?php }?>

				return false;
			}
			</script>
			<?php

			while(!$rs->EOF())
			{
				$count++;
				$IDb=$rs->db_data["ID"];
				$Referencia=$rs->db_data["Referencia"];
				$Cond_pag=$rs->db_data["Cond_pag"];
				$Local_Entrega=$rs->db_data["Local_Entrega"];
				$Tipo=$rs->db_data["Tipo"];
				$Anulada=$rs->db_data["Anulada"];
				$RazaoAnulacao=$rs->db_data["RazaoAnulacao"];
				$ID_Cliente=$rs->db_data["ID_Cliente"];
				$N_Contribuinte=$rs->db_data["enc_Nif"];
				$Des_Social=$rs->db_data["Des_Social"];
				$enc_Nome = $rs->db_data["enc_Nome"];
				$enc_Apelido = $rs->db_data["enc_Apelido"];
				$enc_NomeEntrega = $rs->db_data["enc_Apelido_ent"];
				$enc_ApelidoEntrega = $rs->db_data["enc_Apelido_ent"];
				$Morada=$rs->db_data["enc_Morada"];
				$Localidade=$rs->db_data["enc_Localidade"];
				$Cod_Pos=$rs->db_data["enc_Codigo_Postal"];
				$Cidade=$rs->db_data["enc_Cidade"];
				$CidadeEntrega=$rs->db_data["enc_Cidade_ent"];
				$Pais=country_name($rs->db_data["enc_Pais"]);
				$PaisEntrega=country_name($rs->db_data["enc_Pais_ent"]);
				$MoradaEntrega=$rs->db_data["enc_Morada_ent"];
				$LocalidadeEntrega=$rs->db_data["LocalidadeEntrega"];
				$Cod_PosEntrega=$rs->db_data["enc_Codigo_Postal_ent"];
				$telefone=$rs->db_data["enc_Telefone"];
				$email=$rs->db_data["enc_Email"];
				$Obs=$rs->db_data["Obs"];
				$Portes=$rs->db_data["Portes"];
				$PortesSemIva=$rs->db_data["PortesSemIva"];
				$Taxas=$rs->db_data["Taxas"];
				$TaxasSemIva=$rs->db_data["TaxasSemIva"];
				$Cobrar=$rs->db_data["Cobrar"];
				$TaxasPaypal=$rs->db_data["TaxasPaypal"];
				$ValorDesconto=$rs->db_data["ValorDesconto"];
				$ID_Vendedor=$rs->db_data["ID_Vendedor"];
				$ID_Distribuidor=$rs->db_data["ID_Distribuidor"];
				$PropostaDe=$rs->db_data["PropostaDe"];
				$ID_Armazem=$rs->db_data["ID_Armazem"];
				$Local_carga=$rs->db_data["Local_carga"];
				$Local_descarga=$rs->db_data["Local_descarga"];
				$Expedicao=$rs->db_data["Expedicao"];
				$Viatura_expedicao=$rs->db_data["Viatura_expedicao"];
				$ID_Expedicao=$rs->db_data["ID_Expedicao"];
				$IDGuia=$rs->db_data["IDGuia"];
				$Operador=$rs->db_data["Operador"];
				$ValorDescontoExtra=$rs->db_data["ValorDescontoExtra"];
				$ValorJustificado=$rs->db_data["ValorJustificado"];
				$TotalDocComIva=$rs->db_data["TotalDocComIva"];
				$TotalDocSemIva=$rs->db_data["TotalDocSemIva"];
				$TotalDocDescontos=$rs->db_data["TotalDocDescontos"];
				$TotalDocOutrasTaxas=$rs->db_data["TotalDocOutrasTaxas"];
				$TotalDocIva=$rs->db_data["TotalDocIva"];
				$Data_Criacao=$rs->db_data["Data_Criacao"];
				$Data_Impressao=$rs->db_data["Data_Impressao"];
				$Data_Doc_Forn=$rs->db_data["Data_Doc_Forn"];
				$Data_expedicao=$rs->db_data["Data_expedicao"];
				$Data_entrega=$rs->db_data["Data_entrega"];
				$Estado=$rs->db_data["Estado"];
				$m_guia=$rs->db_data["IDGuia"];
				$MOEDA=$rs->db_data["MOEDA"];
				$entidadeatm=$rs->db_data["Ent_atm"];
				$referenciaatm=$rs->db_data["Ref_atm"];
				$Metodo_pagamento=$rs->db_data["Metodo_pagamento"];
				$Metodo_expedicao=$rs->db_data["Metodo_envio"];
				$Encomenda_Paga=$rs->db_data["Encomenda_Paga"];
				$enc_token=$rs->db_data["enc_token"];
				$TotalIva=$rs->db_data["TotalIva"];

				$enc_info='';

				$corTexto = '#FFF';
				if($count % 2 != 0)	{$cor="#999";$n="1";}
				else				{$cor="#777";$n="2";}


				$enc_state =  estado_encomenda($Estado, $GLOBALS['site_lang']);

				$enc_info = ($enc_state['titulo2']!='' ? $enc_state['titulo2']: $enc_state['titulo']);
				$cor =  '#'.$enc_state['cor'];
				$corTexto = '#000';


				$extra='';

				$i =0;

				global $id_enc;
				if(isset($id_enc)) $id_enc=intval($id_enc);
				if(isset($_GET['pagina']) && intval($_GET['pagina']) > 0) $fromurl = '&paginato='.intval($_GET['pagina']);
				if(isset($_GET['paginato']) && intval($_GET['paginato']) > 0) $tourl = '?pagina='.intval($_GET['paginato']);
				$url_on = seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'?id_enc='.$IDb.$fromurl.'&show_compras=1';
				$url_off = seo('pages.php?page_id='.$GLOBALS['registo_page_id']).$tourl.'?show_compras=1';



				?>
			<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 text-center">

			<div class="row margin-bottom" style="margin-left:-10px; margin-right:-10px;">

			<div class="col-xs-12 col-sm-12 col-md-6 text-left padding3">
			<div class="col-xs-12 col-sm-12 col-md-12 text-left padding3">
			<b>Número da Encomenda</b>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-12 text-left padding3">
				<input type="text" autocomplete="off" title="" maxlength="255" readonly="" value="#<?php echo $IDb;?>" class="inputregisto" />
				</div>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-6 text-left padding3">
			<div class="col-xs-12 col-sm-12 col-md-12 text-left padding3">
			<b>Estado</b>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-12 text-left padding3">
				<div class="inputregisto" style="line-height:40px"><b><?php echo $enc_info.($Estado == 7 ? ' '.str_replace(',','<span style="font-family:verdana,sans-serif">,</span>', date('M d, Y', strtotime($Data_expedicao))) : '');?></b> </div>
				</div>
			</div>
			</div>

			</div>
			<?php if(isset($_GET['id_enc']) && intval($_GET['id_enc']) == $IDb ){?>
			<div class="col-xs-12 col-sm-12 col-md-12 text-right"><div style="font-size:14px;padding:10px 0;margin-bottom:0;cursor:pointer" onclick="window.open('<?php echo $url_off?>', '_self')"> - Fechar Resumo</div></div>
			<?php }else{?>
			<div class="col-xs-12 col-sm-12 col-md-12 text-right"><div style="border-bottom:#000 1px solid;font-size:14px;padding:10px 0;margin-bottom:10px;cursor:pointer" onclick="window.open('<?php echo $url_on?>', '_self')"> + Ver Resumo da Encomenda</div></div><br>
			<?php }?>
			</div>







				<?php
				if($id_enc == $IDb)
				{
						?>
						<div class="row shopping-cart2">
						<div class="col-md-6 product">
						<b>Dados de Facturação</b><br>	<br>
						<div class="row">
							<div class="clear"></div>
							<div class="col-xs-12 col-md-6">
								<div class="form-group">
									<label for="m_nome">Nome</label><br>
									<?php echo $enc_Nome;?>
								</div>
							</div>
							<div class="col-xs-12 col-md-6">
								<div class="form-group">

									<label for="m_apelido">Apelido</label><br>
									<?php echo $enc_Apelido;?>
								</div>
							</div>
							<div class="clear"></div>
							<div class="col-xs-12">
								<div class="form-group">
									<label for="m_nome">Morada</label><br>
									<?php echo $Morada;?>
								</div>
							</div>
							<div class="clear"></div>
							<div class="col-xs-12 col-md-6">
								<div class="form-group">

									<label for="m_apelido">Código Postal</label><br>
									<?php echo $Cod_Pos;?>
								</div>
							</div>
							<div class="col-xs-12 col-md-6">
								<div class="form-group">

									<label for="m_apelido">Localidade</label><br>
									<?php echo $Cidade;?>
								</div>
							</div>
						</div>
						</div>
						<div class="col-md-6 product">
						<b>Dados de Entrega</b><br>	<br>
						<div class="row">
							<div class="clear"></div>
							<div class="col-xs-12 col-md-6">
								<div class="form-group">
									<label for="m_nome">Nome</label><br>
									<?php echo $enc_NomeEntrega;?>
								</div>
							</div>
							<div class="col-xs-12 col-md-6">
								<div class="form-group">

									<label for="m_apelido">Apelido</label><br>
									<?php echo $enc_ApelidoEntrega;?>
								</div>
							</div>
							<div class="clear"></div>
							<div class="col-xs-12">
								<div class="form-group">
									<label for="m_nome">Morada</label><br>
									<?php echo $MoradaEntrega;?>
								</div>
							</div>
							<div class="clear"></div>
							<div class="col-xs-12 col-md-6">
								<div class="form-group">

									<label for="m_apelido">Código Postal</label><br>
									<?php echo $Cod_PosEntrega;?>
								</div>
							</div>
							<div class="col-xs-12 col-md-6">
								<div class="form-group">

									<label for="m_apelido">Localidade</label><br>
									<?php echo $CidadeEntrega;?>
								</div>
							</div>
						</div>
						</div>
						</div>
						<form name="substituir_enc" action="<?php echo seo('pages.php?page_id='.$GLOBALS['registo_page_id'])?>" method="GET" onsubmit="return submitopt('<?php echo $IDb?>', '<?php echo $enc_token?>')">


						<br>



						<div class="shopping-cart2">

						<div class="column-labels">
						<label class="product-image">Produto</label>
						<label class="product-details">&nbsp;</label>
						<label class="product-price"><?php echo getLinguas('key_preco'); ?></label>
						<label class="product-quantity"><?php echo getLinguas('key_quantidade'); ?></label>
						<label class="product-line-price">Total</label>
						</div>

						<?php
						$altera = 0;
						$sql = "SELECT * FROM encomendas_produtos WHERE ID_PAI=".intval($_GET['id_enc'])." AND Apagar = 0 AND voucher_id = 0 AND Estado!=8";
						// echo $sql.'<br>';
						$rs2->db_open($db_link, $DB_name, $sql);
						while(!$rs2->EOF())
						{
							$IDb = $rs2->db_data["ID"];
							$ID_Cliente = $rs2->db_data["ID_Cliente"];
							$ID_Produto = $rs2->db_data["ID_Produto"];
							$Ref = ''; $Ref = $rs2->db_data["Ref"];
							$Nome = ''; $Nome = $rs2->db_data["Nome"];

							// INFO DO PRODUTO
							$info = get_produtc_info($ID_Produto);

							// MODELO DO PRODUTO
							// $modelo = getModelos($info['prod_modelo']);

							// STYLE DO PRODUTO
							// $style = getTipos($info['prod_tipo']);
							$Ref = rtrim($rs2->db_data["Ref"],'-');
							$Nome = $rs2->db_data["Nome"];
							$Qtd = $rs2->db_data["Qtd"];
							$Tamanho = $rs2->db_data["Tamanho"];
							$Cor = $rs2->db_data["Cor"];
							$Valor = $rs2->db_data["Valor"];
							$Valor_Desconto = $rs2->db_data["Valor_Desconto"];
							$Tipo = $rs2->db_data["Tipo"];
							$m_estado = $rs2->db_data["Estado"];
							$enc_state2 =  estado_encomenda($m_estado, $GLOBALS['site_lang']);
							$ID_Conf = $rs2->db_data["ID_Conf"];
							$login = $rs2->db_data["login"];
							$Chave = $rs2->db_data["Chave"];
							$Obs = $rs2->db_data["Obs"];

							$url = str_replace($site_http_url,'',seo('pages.php?page_id='.$GLOBALS['products_page_id'], $ID_Produto, 2));

							?>
							<div class="product">
							<div class="product-image text-center">
							<?php if($Tipo == 0){echo '&nbsp;';}else{?><img src="<?php echo $info['prd_imgm']; ?>" alt="" border="0" /><?php }?>
							</div>
							<div class="product-details">
							<div class="product-title"><?php echo ($Ref!='' ? '<b>'.$Ref.'</b><br>' : ''); ?><?php echo $Nome; ?></div>

							</div>
							<div class="product-price"><?php echo numberformat($Valor)?></div>
							<div class="product-quantity"><?php echo number_format($Qtd,0); ?></div>
							<div class="product-line-price"><?php echo numberformat(($Valor * $Qtd))?></div>
							</div>
							<?php if($Valor_Desconto > 0){?>
							<div class="product">
							<div class="product-image text-center">
							&nbsp;
							</div>
							<div class="product-details" style="min-height:auto">
							<div class="product-title"><b>DESCONTO</b></div>

							</div>
							<div class="product-price">- <?php echo numberformat($Valor_Desconto)?></div>
							<div class="product-quantity">&nbsp;</div>
							<div class="product-line-price"><?php echo numberformat(($Valor * $Qtd)-$Valor_Desconto);?></div>
							</div><br>
							<?php }

							// getIMEIS($ID_Produto);

							$rs2->db_next();
						}

						$rs2->db_close();


						?>

						<div class="totals">
							<div class="totals-item">
							<label class="text-left">Portes</label>
							<div class="totals-value" id="cart-shipping"><?php echo numberformat($Portes, 1);?></div>
							</div>
							<div class="clear"></div>
							<div class="totals-item totals-item-total">
							<label class="text-left">Total</label>
							<div class="totals-value" id="cart-total"><?php echo numberformat($TotalDocComIva, 1);?></div>
							</div>
							<div class="clear"></div>
							</div>
						</div>


						<br />


						<?php


					?></form><?php
				}

				$rs->db_next();
				$i++;
			}


			$rs->db_close();
			// echo show_paginacao_encomendas($total,$qtd);
			echo $return2;
		}
	}
	// return $i;
}

function getIMEIS($ID_Produto=0)
{
	if($ID_Produto > 0)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo,$from,$cli_id,$page_id,$key_wishlist_notloggedin,$site_lang;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs3=new Dataobj;

		$sql = "SELECT rma_imei, rma_n_serie, rma_obs FROM encomendas_produtos_rmas WHERE ID_PAI=".intval($_GET['id_enc'])." AND ID_Produto='".$ID_Produto."'";
		$rs3->db_open($db_link, $DB_name, $sql);
		while(!$rs3->EOF())
		{
			$rma_imei = $rs3->db_data["rma_imei"];
			$rma_n_serie = $rs3->db_data["rma_n_serie"];
			$rma_obs = $rs3->db_data["rma_obs"];


			?>
			<div class="product">
			<div class="product-image text-center">&nbsp;</div>
			<div class="product-details" style="width:28%;min-height:auto">
			<div class="product-title"><b>IMEI</b><br><?php echo $rma_imei; ?></div>
			</div>
			<div class="product-details" style="width:28%;min-height:auto">
			<div class="product-title"><b>Nº Série</b><br><?php echo $rma_n_serie; ?></div>
			</div>
			<div class="product-details" style="width:28%;min-height:auto">
			<div class="product-title"><b>Observações</b><br><?php echo $rma_obs; ?></div>
			</div>
			</div>
			<?php
			$rs3->db_next();
		}

		$rs3->db_close();
		echo '<br>';
	}
}

function loadLivroEnderecos($id=0)
{
    if($id > 0) $dados = cliente_perfil($id); else $dados = cliente_logado();


/* 	if($id == 0)
	{
		if($dados)
		{
			// $html .= '<h1 class="text-center">OS MEUS ENDEREÇOS</h1>';
			// $html .= '<h2 class="subtitle text-center">'.mb_strtoupper(html_entity_decode($dados['Nome'] . ' ' . $dados['Apelido']), 'UTF-8').'</h2>';
		}
		else
		{
			// $html .= '<h1 class="text-center">SIGN IN</h1>';
			// $html .= '<h2 class="subtitle text-center">'.mb_strtoupper(html_entity_decode($dados['Nome'] . ' ' . $dados['Apelido']), 'UTF-8').'</h2>';
		}
    } */
	$html .= '<div class="row"><div class="col-md-12">';


    $html .= '<div class="c"></div>
                <div class="row">

                    <div class="col-md-12">';
    if ($dados && $GLOBALS['tem_modulo_enderecos'] == 1)
	{

        if (isset($_GET['id_endereco']))
		{
            $idEndereco = intval($_GET['id_endereco']);
            if ($idEndereco > 0)
			{
                if (isset($_GET['apagar']))
				{
                    //obter a pagina de apagar
                    $html .= showFormLivroEnderecos($idEndereco, 1, $dados);
                }
				else
				{
                    //obter a pagina de edicao
                    $html .= showFormLivroEnderecos($idEndereco, 0, $dados);
                }
            }
            else
			{
                $html .= showFormLivroEnderecos(0, 0, $dados);
            }
        }
		else
		{
            //obter a pagina de listagem
            $html .= showListLivroEnderecos($dados);
            $html .= showFormLivroEnderecos(0, 0, $dados);
        }
    }

    $html.='<br> <br>
                    </div>

                </div>
            </div>

        </div>';

    return $html;
}


function showFormLivroEnderecos($idEndereco = 0, $apagar = 0, $dados=array())
{
    $db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
    $rs_paises = new Dataobj();
    // $dados = cliente_logado();

   	if(isset($_SERVER['REQUEST_URI']) && substr_count($_SERVER['REQUEST_URI'], 'admin/clientes/')) $action = 'pagina.php?ID='.intval($_GET['ID']).'&tab=1&show_carteira=1&id_endereco=' . $idEndereco;
	else $action = seo('pages.php?page_id=' . $GLOBALS['registo_page_id']) . '?show_carteira=1&id_endereco=' . $idEndereco;

    if(isset($_GET['apagar']))
    {
        $action .= '&apagar=1';
    }

	if(isset($_SERVER['REQUEST_URI']) && substr_count($_SERVER['REQUEST_URI'], 'admin/clientes/')) $action_back = 'pagina.php?ID='.intval($_GET['ID']).'&tab=1&show_carteira=1';
	else $action_back = seo('pages.php?page_id=' . $GLOBALS['registo_page_id']) . '?show_carteira=1';

	if(isset($_GET['show_carteira']) && isset($_GET['id_endereco']) && isset($_GET['apagar']) && intval($_GET['show_carteira']) == 1 && intval($_GET['id_endereco']) > 0 && intval($_GET['apagar']) == 1) $html = '<h3 class="text-left">Apagar endereço</h3>';
	elseif(isset($_GET['show_carteira']) && isset($_GET['id_endereco']) && intval($_GET['show_carteira']) == 1 && intval($_GET['id_endereco']) > 0) $html = '<h3 class="text-left">Alterar endereço</h3>';
	elseif(isset($_GET['show_carteira']) && intval($_GET['show_carteira']) == 1) $html = '<h3 class="text-left">Criar novo endereço</h3>';


	$html .= '<div class="row">';
    $html .= '<div class="col-md-12">';
    $html .= '<script type="text/javascript">';
    $html .= "function validarEndereco() {
        var cor = '#ffffe5';
        var cor2 = '#FFFFFF';
        var campos_obrigatorios;
        campos_obrigatorios = new Array('m_nome', 'm_apelido', 'm_morada', 'm_telefone', 'm_codigo_postal', 'm_cidade', 'm_pais', 'm_contribuinte');

        var campos_preenchidos;
        var i = 0;
        while (i < campos_obrigatorios.length) {
            $('input[name=\"' + campos_obrigatorios[i] + '\"]').css('background-color', cor2);
            i = i + 1;
        }
        i = 0;
        while (i < campos_obrigatorios.length) {
            if (document.forms['enderecoform'].elements[campos_obrigatorios[i]].value == \"\") {
                campos_preenchidos = 1;
                $('input[name=\"' + campos_obrigatorios[i] + '\"]').css('background-color', cor);
            }
            i = i + 1;
        }
        if (campos_preenchidos == 1) {
            Apprise('" . getLinguas('key_camposobr',$GLOBALS['site_lang']) . "');
            return false;
        }
        return true;
    }";
    $html .= '</script>';
    $html .= '<form method="post" onsubmit="return validarEndereco();" name="enderecoform" action="' . $action . '">';
    $titulo = 'ADICIONAR NOVO ENDEREÇO';
    $nomeBtn = 'ADICIONAR ENDEREÇO';
    $showForm = 0;
    $nome = '';
    $apelido = '';
    $designacao = '';
    $morada = '';
    $telefone = '';
    $codigo_postal = '';
    $cidade = '';
    $contribuinte = '';
    $pais = 165;

    if ($idEndereco == 0) {

        $nome = isset($_POST['m_nome']) ? $_POST['m_nome'] : '';
        $apelido = isset($_POST['m_apelido']) ? $_POST['m_apelido'] : '';
        // $designacao = isset($_POST['m_designacao']) ? $_POST['m_designacao'] : '';
        $morada = isset($_POST['m_morada']) ? $_POST['m_morada'] : '';
        $telefone = isset($_POST['m_telefone']) ? $_POST['m_telefone'] : '';
        $codigo_postal = isset($_POST['m_codigo_postal']) ? $_POST['m_codigo_postal'] : '';
        $cidade = isset($_POST['m_cidade']) ? $_POST['m_cidade'] : '';
        $contribuinte = isset($_POST['m_contribuinte']) ? $_POST['m_contribuinte'] : '';
        $pais = isset($_POST['m_pais']) ? $_POST['m_pais'] : 165;

        if (
            isset($_POST['m_nome']) && $_POST['m_nome'] != '' &&
            isset($_POST['m_apelido']) && $_POST['m_apelido'] != '' &&
            // isset($_POST['m_designacao']) && $_POST['m_designacao'] != '' &&
            isset($_POST['m_morada']) && $_POST['m_morada'] != '' &&
            isset($_POST['m_telefone']) && $_POST['m_telefone'] != '' &&
            isset($_POST['m_codigo_postal']) && $_POST['m_codigo_postal'] != '' &&
            isset($_POST['m_cidade']) && $_POST['m_cidade'] != '' &&
            isset($_POST['m_contribuinte']) && $_POST['m_contribuinte'] != '' &&
            isset($_POST['m_pais']) && $_POST['m_pais'] > 0
        ) {
            $sql = "INSERT INTO clientes_enderecos(ID_Cliente, Nome, Apelido, Designacao, Morada, Codigo_Postal, Cidade, Pais, Telefone, Nif) VALUES ('{$dados['cli_id']}', '$nome', '$apelido', '$designacao', '$morada', '$codigo_postal', '$cidade', '$pais', '$telefone', '$contribuinte')";
            Runquery($db_link, $GLOBALS['DB_name'], $sql);

            $showForm = 2;
        } else {
            $showForm = 1;
        }
    } else {
        $rs = getEnderecos($dados['cli_id'], 1, $idEndereco);
        if (!$rs->EOF()) {

            $nome = isset($_POST['m_nome']) ? $_POST['m_nome'] : $rs->db_data['Nome'];
            $apelido = isset($_POST['m_apelido']) ? $_POST['m_apelido'] : $rs->db_data['Apelido'];
            $designacao = isset($_POST['m_designacao']) ? $_POST['m_designacao'] : $rs->db_data['Designacao'];
            $morada = isset($_POST['m_morada']) ? $_POST['m_morada'] : $rs->db_data['Morada'];
            $telefone = isset($_POST['m_telefone']) ? $_POST['m_telefone'] : $rs->db_data['Telefone'];
            $codigo_postal = isset($_POST['m_codigo_postal']) ? $_POST['m_codigo_postal'] : $rs->db_data['Codigo_Postal'];
            $cidade = isset($_POST['m_cidade']) ? $_POST['m_cidade'] : $rs->db_data['Cidade'];
            $contribuinte = isset($_POST['m_contribuinte']) ? $_POST['m_contribuinte'] : $rs->db_data['Nif'];
            $pais = isset($_POST['m_pais']) ? $_POST['m_pais'] : $rs->db_data['Pais'];

            if ($apagar == 1) {
                $titulo = 'APAGAR ENDEREÇO';
                $nomeBtn = 'APAGAR';
            } else {
                $titulo = 'EDITAR ENDEREÇO';
                $nomeBtn = 'ALTERAR';
            }

            if (
                isset($_POST['m_nome']) && $_POST['m_nome'] != '' &&
                isset($_POST['m_apelido']) && $_POST['m_apelido'] != '' &&
                //isset($_POST['m_designacao']) && $_POST['m_designacao'] != '' &&
                isset($_POST['m_morada']) && $_POST['m_morada'] != '' &&
                isset($_POST['m_telefone']) && $_POST['m_telefone'] != '' &&
                isset($_POST['m_codigo_postal']) && $_POST['m_codigo_postal'] != '' &&
                isset($_POST['m_cidade']) && $_POST['m_cidade'] != '' &&
                isset($_POST['m_contribuinte']) && $_POST['m_contribuinte'] != '' &&
                isset($_POST['m_pais']) && $_POST['m_pais'] > 0
            ) {
                if ($apagar == 1) {
                    $sql = "DELETE FROM clientes_enderecos WHERE ID_Cliente='{$dados['cli_id']}' AND ID='$idEndereco'";
                    Runquery($db_link, $GLOBALS['DB_name'], $sql);
                    $showForm = 2;
                } else {

                    $sql = "UPDATE clientes_enderecos SET ID_Cliente='{$dados['cli_id']}', Nome='$nome', Apelido='$apelido', Nif='$contribuinte', Designacao='$designacao', Morada='$morada', Codigo_Postal='$codigo_postal', Cidade='$cidade', Pais='$pais', Telefone='$telefone' WHERE ID='$idEndereco'";
                    Runquery($db_link, $GLOBALS['DB_name'], $sql);

                    /*$rs3 = getEnderecos($dados['cli_id'], 1);

                    if ($res && $rs3->db_data['ID'] == $idEndereco) {
                        $sql = "UPDATE clientes SET Empresa='$designacao', Morada='$morada', Codigo_Postal='$codigo_postal', Cidade='$cidade', Pais='$pais', Telefone='$telefone' WHERE ID='{$dados['cli_id']}'";
                        Runquery($db_link, $GLOBALS['DB_name'], $sql);
                    }*/
                    $showForm = 2;
                }
            } else {
                $showForm = 1;
            }
        }
        $rs->db_close();
    }

    if ($showForm == 0)
	{
        $html .= '<div class="row">';
        $html .= '<div class="col-md-12">Endereço inexistente<br><br></div>';
        $html .= '<div class="col-md-12">';
        $html .= ' <div class="form-group text-center"><input type="button" value="&lt; VOLTAR" onclick="window.open(\'' . seo('pages.php?page_id=' . $GLOBALS['registo_page_id']) . '?show_carteira=1\',\'_self\')" class="button3"></div>';
        $html .= '</div>';
        $html .= '</div>';
    }
	elseif ($showForm == 1)
	{

        $html .= '<div class="subtitulo_d hidden">' . $titulo . '</div>';

        $html .= '<div class="row">';

        $html .= '<div class="col-md-6">';
        $html .= '<div class="form-group"><label for="m_nome">'.getLinguas('key_nome').' <span class="mandatory">*</span></label>';
        $html .= '<input type="text" class="form-control" id="m_nome" name="m_nome" value="' . $nome . '">';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="col-md-6">';
        $html .= '<div class="form-group"><label for="m_apelido">'.getLinguas('key_apelido').' <span class="mandatory">*</span></label>';
        $html .= '<input type="text" class="form-control" id="m_apelido" name="m_apelido" value="' . $apelido . '">';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="col-md-12 hidden">';
        $html .= '<div class="form-group"><label for="m_designacao">'.getLinguas('key_empresa').' <span class="mandatory">*</span></label>';
        $html .= '<input type="text" class="form-control" id="m_designacao" name="m_designacao" value="' . $designacao . '">';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="col-md-12">';
        $html .= '<div class="form-group"><label for="m_nome">'.getLinguas('key_morada').' <span class="mandatory">*</span></label>';
        $html .= '<input type="text"class="form-control" id="m_morada" name="m_morada" value="' . $morada . '">';
        $html .= '</div>';
        $html .= '</div>';



        $html .= '<div class="col-md-6">';
        $html .= '<div class="form-group"><label for="m_cidade">'.getLinguas('key_cidade').' <span class="mandatory">*</span></label>';
        $html .= '<input type="text" class="form-control" id="m_cidade" name="m_cidade" value="' . $cidade . '">';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="col-md-6"><label for="m_codigo_postal">'.getLinguas('key_codpos').' <span class="mandatory">*</span></label>';
        $html .= '<div class="form-group">';
        $html .= '<input type="text" class="form-control" id="m_codigo_postal" name="m_codigo_postal" value="' . $codigo_postal . '">';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="col-md-6">';
        $html .= '<div class="form-group"><label for="m_pais">'.getLinguas('key_pais').' <span class="mandatory">*</span></label>';
        $html .= '<select class="form-control" name="m_pais" id="m_pais" data-live-search="true">';
        $sql = "SELECT c.zone_id,czl.zone_name FROM country_zones AS c INNER JOIN country_zones_lang AS czl ON c.zone_id=czl.zone_id WHERE czl.zone_lang='{$GLOBALS['site_lang']}' AND c.zone_visible=1 ORDER BY czl.zone_name ASC";
        $rs_paises->db_open($db_link, $GLOBALS['DB_name'], $sql);
        while (!$rs_paises->EOF()) {
            $zone_name = $rs_paises->db_data['zone_name'];
            $zone_id = $rs_paises->db_data['zone_id'];
            $html .= '<option value="' . $zone_id . '" ';
            if ($pais == $zone_id) {
                $html .= 'selected="selected"';
            }
            $html .= '>' . $zone_name . '</option>';

            $rs_paises->db_next();
        }
        $rs_paises->db_close();
        $html .= '</select>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="col-md-6">';
        $html .= '<div class="form-group"><label for="m_contribuinte">'.getLinguas('key_nif').' <span class="mandatory">*</span></label>';
        $html .= '<input type="text" class="form-control" id="m_contribuinte" name="m_contribuinte" value="' . $contribuinte . '">';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="col-md-12">';
        $html .= '<div class="form-group"><label for="m_telefone">'.getLinguas('key_tel').' <span class="mandatory">*</span></label>';
        $html .= '<input type="text" class="form-control" id="m_telefone" name="m_telefone" value="' . $telefone . '">';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

		if(isset($_SERVER['REQUEST_URI']) && substr_count($_SERVER['REQUEST_URI'], 'admin/clientes/'))
		{

			$html .= ' <div class="form-group text-center"><input type="submit" value="' . $nomeBtn . '" class="button3"></div>';
			if(isset($_GET['show_carteira']))$html .= ' <div class="form-group text-center"><input type="button" value="VOLTAR À LISTAGEM DE MORADAS" class="btn btn-dotb" onclick="window.open(\'pagina.php?ID='.intval($_GET['ID']).'&tab=1\',\'_self\')"></div>';
		}
		else
		{

			$html .= ' <div class="form-group text-center"><input type="button" value="VOLTAR" onclick="window.open(\''.$action_back. '\',\'_self\')" class="button3" style="margin-right:5px"><input type="submit" value="' . $nomeBtn . '" class="button3"></div>';
		}


    } else {
       	if(isset($_SERVER['REQUEST_URI']) && substr_count($_SERVER['REQUEST_URI'], 'admin/clientes/')) $html .= '<script type="text/javascript">window.open(\'pagina.php?ID='.intval($_GET['ID']).'&tab=1\',\'_self\');</script>';
		else $html .= '<script type="text/javascript">window.open(\'' . seo('pages.php?page_id=' . $GLOBALS['registo_page_id']) . '?show_carteira=1\',\'_self\');</script>';
    }


    $html .= '</form>';
    $html .= '</div>';
    $html .= '</div>';

    return $html;
}

function showListLivroEnderecos($dados)
{
    //$dados = cliente_logado();

    $html = '<h3 class="text-left">Endereços</h3>';

    $rs = getEnderecos($dados['cli_id'], 2);
    if (!$rs->EOF())
	{
        $html .= '<ul class="list-enderecos">';
        while (!$rs->EOF())
		{

			$pais = country_name($rs->db_data['Pais']);

			// SE ESTIVER NO ADMIN
			if(isset($_SERVER['REQUEST_URI']) && substr_count($_SERVER['REQUEST_URI'], 'admin/clientes/'))
			{
				$html .= '<li style="border-bottom:#333 1px solid;margin-bottom:10px">';

				$html .= '<div style="float:left;padding:10px;width:50%;box-sizing:border-box;font-size:15px">';
				$html .= "{$rs->db_data['Nome']} {$rs->db_data['Apelido']}<br>{$rs->db_data['Morada']}<br>{$rs->db_data['Codigo_Postal']}, {$rs->db_data['Cidade']}<br>NIF: {$rs->db_data['Nif']}<br>TEL: {$rs->db_data['Telefone']}<br><br>";
				$html .= '</div>';
				$html .= '<div style="float:left;padding:10px;width:50%;box-sizing:border-box">';

				$html .= '<a href="pagina.php?ID='.intval($_GET['ID']).'&tab=1&show_carteira=1&id_endereco=' . $rs->db_data['ID'] . '" class="button3" style="color:#FFF;margin-right:2px">Editar</a>';
				$html .= '<a href="pagina.php?ID='.intval($_GET['ID']).'&tab=1&show_carteira=1&id_endereco=' . $rs->db_data['ID'] . '&apagar=1" class="button3" style="color:#FFF">Eliminar</a>';

				$html .= '</div><div style="clear:both"></div>';

				$html .= '</li>';
			}
			else
			{
				$html .= '<li style="border-bottom:#c0c0c0 1px solid;margin-bottom:10px">';
				$html .= '<div class="row">';
				$html .= '<div class="col-sm-7">';
				$html .= "{$rs->db_data['Nome']} {$rs->db_data['Apelido']}<br>{$rs->db_data['Morada']}<br>{$rs->db_data['Codigo_Postal']}, {$rs->db_data['Cidade']}<br>NIF: {$rs->db_data['Nif']}<br>TEL: {$rs->db_data['Telefone']}<br><br>";
				$html .= '</div>';
				$html .= '<div class="col-sm-5 text-right">';

				$html .= '<a href="' . seo('pages.php?page_id=' . $GLOBALS['registo_page_id']) . '?show_carteira=1&id_endereco=' . $rs->db_data['ID'] . '" class="button3" style="color:#FFF;margin-right:2px">Editar</a>';
				$html .= '<a href="' . seo('pages.php?page_id=' . $GLOBALS['registo_page_id']) . '?show_carteira=1&id_endereco=' . $rs->db_data['ID'] . '&apagar=1" class="button3" style="color:#FFF">Eliminar</a>';

				$html .= '</div>';
				$html .= '</div>';
				$html .= '</li>';
			}
            $rs->db_next();
        }
        $html .= '</ul>';
    }
    $rs->db_close();

	if(isset($_SERVER['REQUEST_URI']) && substr_count($_SERVER['REQUEST_URI'], 'admin/clientes/'))  $html .= '<div style="clear:both"></div><br>';

    return $html;
}