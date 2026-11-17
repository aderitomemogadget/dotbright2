<?php

    $id=intval($_GET["id"]);

    $_GET ='';
    $_POST ='';
    $_REQUEST ='';

    unset($_GET);
    unset($_POST);
    unset($_REQUEST);

    require_once("core/core_files.php");
    require_once("layout/header.php");
	require_once("layout/template_topo.php");

    global $site_title,$site_http_url,$key_newsletter_title,$key_newsletter_subtitle,$site_lang;


    if(isset($_GET['t']) && $_GET['t']==0)
    {
        //NEWSLETTERS
?>
<div class="titulo_pagina"><? echo $key_newsletter_title[$site_lang]; ?></div><br/>
<div class="subtitulo_pagina2"><? echo $key_newsletter_subtitle[$site_lang]; ?></div><br/>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
    <tr>
    <td align="right">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td align="right">&nbsp;</td>
    <td class=preto>
      <?
        $obj=BuscarRegistosMaillingList2($_GET['Email']);
        if(!$obj->EOF())
        {
        	$nome=htmlentities($obj->db_data["Nome"]);

        	echo "A nossa newsletter pretende levar até si todas as novidades relativas à <b>".$site_title."</b>, bem como sobre o mundo da tecnologia em geral. Todos os aspectos relacionados com a newsletter são pensados ao mais ínfimo pormenor,para serem sucintos,directos ao objectivo,claros na mensagem a passar para o leitor. Temos,igualmente,a preocupação de enviar a newsletter com uma periodicidade que a torne ao mesmo tempo pouco notada,por não ser muito frequente,mas com mensagens que perdurem.<br><br>";
        	echo "Tem a certeza que pretende anular o seu registo da nossa base de dados de contactos?<br/><br/>";

        	echo "<img src=\"".$site_http_url."layout/seta2_on.gif\" align=absmiddle>&nbsp;<a href=\"".$site_http_url."unsubscrive_ok.php?Email=".$_GET['Email']."&t=".$_GET['t']."\" class=preto><b>SIM</b></a><br>";
        	echo "<img src=\"".$site_http_url."layout/seta2_on.gif\" align=absmiddle>&nbsp;<a href=\"".$site_http_url."index.php\" class=preto><b>NÃO</b></a>";
        	$obj->db_close(1);
        }
		else
		{

			$obj=BuscarRegistosLogin($_GET['Email']);
			if(!$obj->EOF())
			{
				$nome=htmlentities($obj->db_data["Nome"]);

				echo "A nossa newsletter pretende levar até si todas as novidades relativas à <b>".$site_title."</b>, bem como sobre o mundo da tecnologia em geral. Todos os aspectos relacionados com a newsletter são pensados ao mais ínfimo pormenor,para serem sucintos,directos ao objectivo,claros na mensagem a passar para o leitor. Temos,igualmente,a preocupação de enviar a newsletter com uma periodicidade que a torne ao mesmo tempo pouco notada,por não ser muito frequente,mas com mensagens que perdurem.<br><br>";
				echo "Tem a certeza que pretende anular o seu registo da nossa base de dados de contactos?";
				echo "<br /><br />";
				echo "<img src=\"".$site_http_url."layout/seta2_on.gif\" align=absmiddle>&nbsp;<a href=\"".$site_http_url."unsubscrive_ok.php?Email=".$_GET['Email']."&t=".$_GET['t']."\" class=preto><b>SIM</b></a><br>";
				echo "<img src=\"".$site_http_url."layout/seta2_on.gif\" align=absmiddle>&nbsp;<a href=\"".$site_http_url."index.php\" class=preto><b>NÃO</b></a>";
				$obj->db_close(1);
			}
		}
        ?>
    </td>
  </tr>
</table>
<br />
<?php
    }
    else if(isset($_GET['t']) && $_GET['t']==1)
    {
        //NOTIFICACAO DE VISITA
    ?>
    <div class="titulo_pagina">Notificação de produtos visitados</div><br/>
    <table width="100%" border="0" cellspacing="0" cellpadding="2">
      <tr>
        <td align="right">&nbsp;</td>
        <td class=preto>
          <?

                $obj=BuscarRegistosLogin($_GET['Email']);
                if(!$obj->EOF())
                {
                    $nome=htmlentities($obj->db_data["Nome"]);

                    echo "A nossa notificação pretende levar até si alguns dos produtos visitados por si<br>";
                    echo "Tem a certeza que pretende anular o seu registo?";
                    echo "<br /><br />";
                    echo "<img src=\"".$site_http_url."layout/seta2_on.gif\" align=absmiddle>&nbsp;<a href=\"".$site_http_url."unsubscrive_ok.php?Email=".$_GET['Email']."&nw=".$_GET['nw']."&t=".$_GET['t']."\" class=preto><b>SIM</b></a><br>";
                    echo "<img src=\"".$site_http_url."layout/seta2_on.gif\" align=absmiddle>&nbsp;<a href=\"".$site_http_url."index.php\" class=preto><b>NÃO</b></a>";
                    $obj->db_close(1);
                }
            ?>
        </td>
      </tr>
    </table>
    <br />
    <?php
    }
    else if(isset($_GET['t']) && $_GET['t']==2)
    {
        //NOTIFICACAO DE NOVIDADES
    ?>
    <div class="titulo_pagina">Notificação de novidades</div><br/>
    <table width="100%" border="0" cellspacing="0" cellpadding="2">
      <tr>
        <td align="right">&nbsp;</td>
        <td class=preto>
          <?

                $obj=BuscarRegistosLogin($_GET['Email']);
                if(!$obj->EOF())
                {
                    $nome=htmlentities($obj->db_data["Nome"]);

                    echo "A nossa notificação pretende levar até si todas as nossas novidades diariamente<br>";
                    echo "Tem a certeza que pretende anular o seu registo?";
                    echo "<br /><br />";
                    echo "<img src=\"".$site_http_url."layout/seta2_on.gif\" align=absmiddle>&nbsp;<a href=\"".$site_http_url."unsubscrive_ok.php?Email=".$_GET['Email']."&nw=".$_GET['nw']."&t=".$_GET['t']."\" class=preto><b>SIM</b></a><br>";
                    echo "<img src=\"".$site_http_url."layout/seta2_on.gif\" align=absmiddle>&nbsp;<a href=\"".$site_http_url."index.php\" class=preto><b>NÃO</b></a>";
                    $obj->db_close(1);
                }
            ?>
        </td>
      </tr>
    </table>
    <br />
    <?php
    }
    require_once("layout/template_bottom.php");
?>