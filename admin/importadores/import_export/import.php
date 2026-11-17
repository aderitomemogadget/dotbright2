<?php
    require_once('settings.inc.php');
    require_once('../../layout/header.inc.php');
    require_once('../../layout/body.inc.php');

    ini_set('display_errors',1);
    ini_set('display_startup_errors',1);
    error_reporting(-1);
?>
<style type="text/css">
     table, th, td, a, div {
        font-family: arial;
        font-size: 10px;
        }
    </style>

<table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td valign="top" align="left"><?php require_once('../../layout/menus.inc.php'); ?>
            <table class="p1" width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr><td style="color:#FFF" align="center"><h3>Importa&ccedil;&atilde;o de produtos</h3></td></tr>
            </table>
            <form name="form1" method="post" action="import_trata.php" enctype="multipart/form-data">
                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color:#FFF">
    				<tr><td colspan="2" height="25" align="center"><b>ESTRUTURA DO FICHEIRO</b></td></tr>
                    <tr>
                        <td colspan="2" style="padding:0 10px;" align="center">
                            <table border="1" cellspacing="0" cellpadding="5" width="100%">
                                <tr>
                                    <td align="center" style="font-weight:bold">DESIGNAÇÃO</td>
                                    <td align="center" style="font-weight:bold">MARCA ID</td>
                                    <td align="center" style="font-weight:bold">REFERÊNCIA</td>
                                    <td align="center" style="font-weight:bold">REFERÊNCIA PAI</td>
                                    <td align="center" style="font-weight:bold">DESIGNAÇÃO ABREVIADA</td>
                                    <td align="center" style="font-weight:bold">EAN</td>
                                    <td align="center" style="font-weight:bold">SKU</td>
                                    <td align="center" style="font-weight:bold">PVP</td>
                                    <td align="center" style="font-weight:bold">PVP com IVA</td>
                                    <td align="center" style="font-weight:bold">FAMILIA ID</td>
                                    <td align="center" style="font-weight:bold">DESCRIÇÃO</td>
                                    <td align="center" style="font-weight:bold">FILTRO 1</td>
                                    <td align="center" style="font-weight:bold">FILTRO 2</td>
                                    <td align="center" style="font-weight:bold">FILTRO ...</td>
                                </tr>
                                <tr>
                                    <td align="center" style="font-weight:bold;">x...x</td>
                                    <td align="center" style="font-weight:bold;">x...x</td>
                                    <td align="center" style="font-weight:bold;">x...x</td>
                                    <td align="center" style="font-weight:bold;">x...x</td>
                                    <td align="center" style="font-weight:bold;">x...x</td>
                                    <td align="center" style="font-weight:bold;">x...x</td>
                                    <td align="center" style="font-weight:bold;">x...x</td>
                                    <td align="center" style="font-weight:bold;">x...x</td>
                                    <td align="center" style="font-weight:bold;">x...x</td>
                                    <td align="center" style="font-weight:bold;">x...x</td>
                                    <td align="center" style="font-weight:bold;">xxxxxxxxxxxxx</td>
                                    <td align="center" style="font-weight:bold;">x...x</td>
                                    <td align="center" style="font-weight:bold;">x...x</td>
                                    <td align="center" style="font-weight:bold;">x...x</td>
                                </tr>
                                <tr>
                                    <td align="center" style="font-weight:bold;color:#FF0000">Obrigatório</td>
                                    <td align="center" style="font-weight:bold;color:#FF0000">Obrigatório</td>
                                    <td align="center" style="font-weight:bold;color:#FF0000">Obrigatório</td>
                                    <td align="center" style="font-weight:bold;color:#FF0000"></td>
                                    <td align="center" style="font-weight:bold;color:#FF0000"></td>
                                    <td align="center" style="font-weight:bold;color:#FF0000"></td>
                                    <td align="center" style="font-weight:bold;color:#FF0000"></td>
                                    <td align="center" style="font-weight:bold;color:#FF0000"></td>
                                    <td align="center" style="font-weight:bold;color:#FF0000"></td>
                                    <td align="center" style="font-weight:bold;color:#FF0000">Obrigatório</td>
                                    <td align="center" style="font-weight:bold;color:#FF0000"></td>
                                    <td align="center" style="font-weight:bold;color:#FF0000"></td>
                                    <td align="center" style="font-weight:bold;color:#FF0000"></td>
                                    <td align="center" style="font-weight:bold;color:#FF0000"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr><td colspan="2" height="25" align="center"><br><b>Exemplo de Ficheiro<br>(Cabeçalho + Linhas)</b><br><br></td></tr>
                    <tr>
                        <td colspan="2" style="padding:0 10px;" align="center">

                            <table border="1" cellspacing="0" cellpadding="5" cellspacing="5" width="100%">
                                <tr>
                                    <td align="center" style="font-weight:bold">DESIGNAÇÃO</td>
                                    <td align="center" style="font-weight:bold">MARCA ID</td>
                                    <td align="center" style="font-weight:bold">REFERÊNCIA</td>
                                    <td align="center" style="font-weight:bold">REFERÊNCIA PAI</td>
                                    <td align="center" style="font-weight:bold">DESIGNAÇÃO ABREVIADA</td>
                                    <td align="center" style="font-weight:bold">EAN</td>
                                    <td align="center" style="font-weight:bold">SKU</td>
                                    <td align="center" style="font-weight:bold">PVP</td>
                                    <td align="center" style="font-weight:bold">PVP com IVA</td>
                                    <td align="center" style="font-weight:bold">FAMILIA ID</td>
                                    <td align="center" style="font-weight:bold">DESCRIÇÃO</td>
                                    <td align="center" style="font-weight:bold">FILTRO 1</td>
                                    <td align="center" style="font-weight:bold">FILTRO 2</td>
                                    <td align="center" style="font-weight:bold">FILTRO ...</td>
                                </tr>
                                <tr>
                                   <td align="center" style="font-weight:bold;">Sponser Carbo Loader</td>
                                    <td align="center" style="font-weight:bold;">86</td>
                                    <td align="center" style="font-weight:bold;">SP028</td>
                                    <td align="center" style="font-weight:bold;">SP028</td>
                                    <td align="center" style="font-weight:bold;">Sponser Carbo Loader</td>
                                    <td align="center" style="font-weight:bold;">7611174045006</td>
                                    <td align="center" style="font-weight:bold;"></td>
                                    <td align="center" style="font-weight:bold;"></td>
                                    <td align="center" style="font-weight:bold;"></td>
                                    <td align="center" style="font-weight:bold;"></td>
                                    <td align="center" style="font-weight:bold;"></td>
                                    <td align="center" style="font-weight:bold;">1200 g</td>
                                    <td align="center" style="font-weight:bold;">Limão Laranja</td>
                                    <td align="center" style="font-weight:bold;"></td>
                                </tr>
                                <tr>
                                    <td align="center" style="font-weight:bold;">Sponser Salt Caps 120caps</td>
                                    <td align="center" style="font-weight:bold;">86</td>
                                    <td align="center" style="font-weight:bold;">SP031</td>
                                    <td align="center" style="font-weight:bold;">SP031</td>
                                    <td align="center" style="font-weight:bold;">Sponser Salt Caps 120caps</td>
                                    <td align="center" style="font-weight:bold;">7611174046164</td>
                                    <td align="center" style="font-weight:bold;"></td>
                                    <td align="center" style="font-weight:bold;"></td>
                                    <td align="center" style="font-weight:bold;"></td>
                                    <td align="center" style="font-weight:bold;"></td>
                                    <td align="center" style="font-weight:bold;"></td>
                                    <td align="center" style="font-weight:bold;"></td>
                                    <td align="center" style="font-weight:bold;"></td>
                                    <td align="center" style="font-weight:bold;"></td>
                                </tr>
                            </table>
                            <hr/>
                            <table border="1" cellspacing="0" cellpadding="5">
                                <tr>
                                    <td align="left" style="font-weight:bold; background-color: #FFFF99; color: red; font-size: 12px;">
                            a) APENAS são aceites ficheiros do tipo CSV, TXT ou XLSX.<br><br>
                            b) A primeira linha Deve conter o nome dos campos.<br><br>
                            c) No caso de ficheiros CSV os campos devem ser separados por ponto e vírgula (;).<br><br>
                            d) Devido a incompatibilidade do Mac todos os ficheiros devem ser gerados/criados em ambiente Windows!<br><br>
                           <hr><br>
                           Exemplo de (formatação) Ficheiros CSV:<br><br>
                           EAN;Nome;STOCK;PVP;Tamanho ;Género;Idade;Cor;Tamanho ;Tamanho Salming;Sabor;Tamanho;Cor;Tamanho;Tamanho;Cor;Peso;Quantidade;Volume;Peso teste;Sabor Kinetica;color;;<br>
                           5060339190020;OTE Carbo Booster Neutro 1Kg;0;0,00;;;;;;;;;;;;;;;;;;;;<br>
                           5060339190037;OTE Energy Drink saqueta 43g;0;0,00;;;;;;;Laranja;;;;;;43g;1 uni.;;;;;;<br>
                           5060339190044;OTE Energy Drink saqueta 43g;0;0,00;;;;;;;Laranja;;;;;;43g;1 uni.;;;;;;<br>
                           5060339190051;OTE Energy Drink 1,2Kg;0;0,00;;;;;;;Laranja;;;;;;1,2kg;1 uni.;;;;;;<br>
                           5060339190068;OTE Energy Drink saqueta 43g;0;0,00;;;;;;;Groselha Negra;;;;;;43g;1 uni.;;;;;;<br>
                           ...<br>
                           ...<br>
                            </td></tr></table>

                            <hr/>
                        </td>
                    </tr>
                    <tr>
                        <td align="left" width="120" style="padding:10px"><input type="file" name="import_file"></td>
                        <td align="left" valign="top" style="padding:10px"><input type="submit" class="botao" value="Importar CSV ou TXT ou XLSX" /></td>
                    </tr>
                </table>
            </form>
            <table class="p1" width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr><td style="color:#FFF" align="center">&nbsp;</td></tr>
            </table>
        </td>
    </tr>
</table>

<?php require_once('../../layout/bottom.inc.php'); ?>  