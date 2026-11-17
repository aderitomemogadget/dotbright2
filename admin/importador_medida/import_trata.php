<?php

    ini_set('max_execution_time','0');
    ini_set('memory_limit','-1');
    ini_set('display_errors',1);
    ini_set('display_startup_errors',1);
    error_reporting(-1);
    require_once ('settings.inc.php');
    require_once ('../layout/header.inc.php');
    require_once ('../layout/body.inc.php');


        $ficheirocsv['name'] = $_FILES['import_file']['name'];
        $ficheirocsv['temp'] = $_FILES['import_file']['tmp_name'];
        $ext = substr($ficheirocsv['name'],strpos($ficheirocsv['name'],'.') + 1);


       global $site_shell_url;

    ?>
    <table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td valign="top" align="left"><?php require_once ('../layout/menus.inc.php');?>
          <table class="p1" width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr><td style="color:#FFF" align="center"><h3>Importa&ccedil;&atilde;o de produtos</h3></td></tr>
            </table>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td style="color:#000;padding:10px;font-family:monospace;font-size:15px" align="left" bgcolor="#FFF">
                <?php
                    echo '<div style="color:#F00;">'.date('d-m-Y H:m:s').' - INÍCIO DE IMPORTAÇÃO</div><br>';

                    $erros = 0;
                    if ($ext != 'xlsx' && $ext != 'XLSX')
                    {
                        echo '<div style="color:#F00;">ERRO #1: o ficheiro submetido deve ser do tipo CSV (comma-separated value) ou XLSX (excel)</div><br>';
                    }
                    else
                    {
                        $filename = uniqid('').'_'.date('YmdYHis');
                        $result = copy($ficheirocsv['temp'],$path_dest = $site_shell_url.'files/tmp/'.strtolower($ficheirocsv['name']));
                        if (!$result)
                        {
                            echo '<div style="color:#F00;">'.date('d-m-Y H:m:s').' - ERRO #2: falhou a cópia do ficheiro para o servidor</div><br>';
                        }
                        else
                        {
                            $file = FILE_PATH.strtolower($filename.'.'.$ext);
                            echo '<div style="color:#F00;">'.date('d-m-Y H:m:s').' - SUCESSO #1: ficheiro copiado com sucesso para o servidor</div><br>';



                            echo '<br><div style="color:#F00;"><a class="btn btn-primary" href="import.php">Clique para continuar</a></div><br>';
                        }
                    }

                ?>
              </td>
            </tr>
            </table>


            <table class="p1" width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr><td style="color:#FFF" align="center">&nbsp;</td></tr>
            </table>


        </td>
        <td width="10px">&nbsp;</td>
        <td valign="top"></td>
    </tr>
    </table>
<?php
    require_once ('../layout/bottom.inc.php');
?>