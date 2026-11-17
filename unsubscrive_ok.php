<?php
  $id=intval($_GET["id"]);

  $_GET ='';
  $_POST ='';
  $_REQUEST ='';

  unset($_GET);
  unset($_POST);
  unset($_REQUEST);

  $path="core/";
  include $path."core_files.php";
	$ssEntidade="Ok";
  include "layout/header.php";
  //como veio de uma pagina de conteudos vamos forçar o ambiente default
  include "layout/template_topo.php";
  global $site_title,$site_http_url,$key_newsletter_title,$key_newsletter_subtitle,$site_lang;

    if(isset($_GET['t']) && $_GET['t']==0)
    {
?>
<div class="titulo_pagina"><? echo $key_newsletter_title[$site_lang]; ?></div>
<br>
<div style="font-size:13px;color:#6E6E6E"><? echo $key_newsletter_subtitle[$site_lang]; ?></div>
<br>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
  <tr>
    <td align="right" height="8"></td>
    <td colspan="2"></td>
  </tr>
  <tr>
    <td align="right">&nbsp;</td>
    <td class=preto align="center"><table width="53%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="34" align="center" valign="bottom"><br><?
            $result=Unsubscrive($_GET['Email']);
            if($result)	echo show_alerta2("O seu registo foi removido com sucesso da nossa base de dados.");
            else		echo show_alerta2("N&atilde;o foi possível remover o seu registo,por favor tente novamente mais tarde.");
        ?>
      <p></td>
  </tr>
</table>
    </td>
  </tr>
</table>
<br />
<?
  }
  else if(isset($_GET['t']) && $_GET['t']==1)
  {
?>

<div class="titulo_pagina">Notificação de produtos visitados</div><br/>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
  <tr>
    <td align="right" height="8"></td>
    <td colspan="2"></td>
  </tr>
  <tr>
    <td align="right">&nbsp;</td>
    <td class=preto align="center"><table width="53%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="34" align="center" valign="bottom"><br><?
            $result=unsubscriveNotificarProdutoVisita($_GET['nw']);
            if($result) echo show_alerta2("O seu registo foi removido com sucesso da nossa base de dados.");
            else echo show_alerta2("N&atilde;o foi possível remover o seu registo,por favor tente novamente mais tarde.");
        ?>
      <p></td>
  </tr>
</table>
    </td>
  </tr>
</table>
<br />
<?
    }
    else if(isset($_GET['t']) && $_GET['t']==2)
  {
?>

<div class="titulo_pagina">Notificação de novidades</div><br/>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
  <tr>
    <td align="right" height="8"></td>
    <td colspan="2"></td>
  </tr>
  <tr>
    <td align="right">&nbsp;</td>
    <td class=preto align="center"><table width="53%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="34" align="center" valign="bottom"><br><?
            $result=unsubscriveProdutoNovidades($_GET['nw']);
            if($result) echo show_alerta2("O seu registo foi removido com sucesso da nossa base de dados.");
            else echo show_alerta2("N&atilde;o foi possível remover o seu registo,por favor tente novamente mais tarde.");
        ?>
      <p></td>
  </tr>
</table>
    </td>
  </tr>
</table>
<br />
<?
    }
  include "layout/template_bottom.php";
?>