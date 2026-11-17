<?php
    /**
    * Class and Function List:
    * Function list:
    * - formatEAN()
    * - checkNumberFormat()
    * - formatText()
    * - getFilterID()
    * - insertFilters()
    * - products_families_obj_save()
    * - products_objs_save()
    * - ScanFilesDir()
    * - insertImages()
    * Classes list:
    */
    ini_set('max_execution_time','0');
    ini_set('memory_limit','-1');
    ini_set('display_errors',1);
    ini_set('display_startup_errors',1);
    error_reporting(0);
    require_once ('settings.inc.php');
    require_once ('../../layout/header.inc.php');
    require_once ('../../layout/body.inc.php');

    if(!$step)
    {
        $ficheirocsv['name'] = $_FILES['import_file']['name'];
        $ficheirocsv['temp'] = $_FILES['import_file']['tmp_name'];
        $ext = substr($ficheirocsv['name'],strpos($ficheirocsv['name'],'.') + 1);
    }
    else
    {
        $filename = $_GET['ficheiro'];

        global $DB_host,$DB_user,$DB_pass,$DB_name;
        $db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
        $rs1 = new DataObj;
        $total_produtos_insert = 0;
        $total_produtos_update = 0;
        $linha = 0;
    }
    ?>
    <style type="text/css">
     table,th,td,a,div {
        font-family: arial;
        font-size: 10px;
        }
        h1{
         font-size: 12px;
        }
    </style>
    <table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td valign="top" align="left"><?php require_once ('../../layout/menus.inc.php');?>
          <table class="p1" width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr><td style="color:#FFF" align="center"><h3>Importa&ccedil;&atilde;o de produtos</h3></td></tr>
            </table>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td style="color:#000;padding:10px;font-family:monospace;font-size:15px" align="left" bgcolor="#FFF">
                <?php
                if(!$step)
                {
                    echo '<div style="color:#F00;">'.date('d-m-Y H:m:s').' - INÍCIO DE IMPORTAÇÃO</div><br>';

                    $erros = 0;
                    if ($ext != 'txt' && $ext != 'TXT' && $ext != 'csv' && $ext != 'CSV' && $ext != 'xlsx' && $ext != 'XLSX')
                    {
                        echo '<div style="color:#F00;">ERRO #1: o ficheiro submetido deve ser do tipo CSV (comma-separated value) ou XLSX (excel)</div><br>';
                    }
                    else
                    {
                        //$filename = $ficheirocsv['name'].'_'.date('YmdYHis');
                        $filename = uniqid('').'_'.date('YmdYHis');
                        $result = copy($ficheirocsv['temp'],FILE_PATH.strtolower($filename.'.'.$ext));
                        if (!$result)
                        {
                            echo '<div style="color:#F00;">'.date('d-m-Y H:m:s').' - ERRO #2: falhou a cópia do ficheiro para o servidor</div><br>';
                        }
                        else
                        {
                            $file = FILE_PATH.strtolower($filename.'.'.$ext);
                            echo '<div style="color:#F00;">'.date('d-m-Y H:m:s').' - SUCESSO #1: ficheiro copiado com sucesso para o servidor (mostrar as primeiras 10 linhas)</div><br>';

                            $amostra=0;

                            echo '<div>Coluna 1 = Nome do produto (<span style="color:#0000ff">Inserção</span>) <span style="color:#ff0000">Obrigatório</span> <br>';
                            echo 'Coluna 2 = Código da Marca  (<span style="color:#f59c1a">Inserção e Update</span>) <span style="color:#ff0000">Obrigatório</span><br>';
                            echo 'Coluna 3 = Referencia do produto  (<span style="color:#0000ff">Inserção</span>) <span style="color:#ff0000">Obrigatório</span><br>';
                            echo 'Coluna 4 = Referencia do produto Pai agregado (<span style="color:#0000ff">Inserção</span>)<br>';
                            echo 'Coluna 5 = Designação abreviada (<span style="color:#0000ff">Inserção</span>)<br>';
                            echo 'Coluna 6 = EAN (<span style="color:#0000ff">Inserção</span>)<br>';
                            echo 'Coluna 7 = SKU (<span style="color:#0000ff">Inserção</span>)<br>';
                            echo 'Coluna 8 = PVP (<span style="color:#0000ff">Inserção</span>)<br>';
                            echo 'Coluna 9 = PVP com IVA (<span style="color:#0000ff">Inserção</span>)<br>';
                            echo 'Coluna 10 = Codigo da Familia (<span style="color:#0000ff">Inserção</span>) <span style="color:#ff0000">Obrigatório</span> <br>';
                            echo 'Coluna 11 = Descrição (<span style="color:#0000ff">Inserção</span>)<br>';
                            echo 'Coluna 12 = Filtro 1 (<span style="color:#f59c1a">Inserção e Update</span>)<br>';
                            echo 'Coluna 13 = Filtro 2 (<span style="color:#f59c1a">Inserção e Update</span>)<br>';
                            echo 'Coluna 14 = Filtro 3 (<span style="color:#f59c1a">Inserção e Update</span>)<br>';
                            echo 'Coluna ... = Filtro ... (<span style="color:#f59c1a">Inserção e Update</span>)<br></div><br>';


                            echo '<table border=1 cellpadding=5 cellspacing=0>';
                            if(strtolower($ext)=='xlsx')
                            {
                                //Vamos carregar para a memoria os dados do ficheiro
                                require('php-excel-reader/excel_reader2.php');
                                require('SpreadsheetReader.php');
                                $Reader = new SpreadsheetReader($file);
                                $Sheets = $Reader -> Sheets();
                                // ler a primeira folha
                                $Reader -> ChangeSheet(0);


                                foreach ($Reader as $Row => $data)
                                {
                                    if($amostra==0)$titulos=$data;

                                    if( $amostra > 10) break;
                                    echo '<tr><td>'.implode("</td><td>",$data).'</td></tr>';
                                    $amostra++;
                                }

                            }
                            else
                            {
                                $fp = fopen($file,"r");
                                while ($amostra < 10 && ($data = fgetcsv($fp,filesize($file) ,';','"')) !== false)
                                {

                                    if($amostra==0)$titulos=$data;
                                    echo '<tr><td>'.implode("</td><td>",$data).'</td></tr>';
                                    $amostra++;
                                }
                                fclose($fp);
                            }

                            echo '</table>';

                            $i=11;
                            echo '<div><br><h1><strong>Filtros detectados:</strong></h1><br>';
                            while($i<count($titulos))
                            {
                                echo $titulos[$i].'<br>';
                                $i++;
                            }
                            echo '</div>';

                            echo '<br><div style="color:#F00;"><a class="btn btn-primary" href="import_trata.php?step=2&ficheiro='.$filename.'.'.$ext.'">Clique para continuar se estiver tudo OK</a></div><br>';
                            echo '<br><div style="color:#F00;"><a class="btn btn-warning" href="import.php">Clique para cancelar importacao</a></div><br>';
                        }
                    }
                }
                else
                {

                    echo '<div style="color:#F00;">'.date('d-m-Y H:m:s').' - FASE 2 DA IMPORTAÇÃO</div><br>';

                    $file = FILE_PATH.$filename;
                    $amostra=0;
                    $total_produtos=0;
                    $total_produtos_update = 0;
                    $total_produtos_insert = 0;
                    $erros=0;

                    if(substr($filename,-4)=='xlsx')
                    {
                        //Vamos carregar para a memoria os dados do ficheiro
                        require('php-excel-reader/excel_reader2.php');
                        require('SpreadsheetReader.php');
                        $Reader = new SpreadsheetReader($file);
                        $Sheets = $Reader -> Sheets();
                        // ler a primeira folha
                        $Reader -> ChangeSheet(0);


                        foreach ($Reader as $Row => $data)
                        {
                            if($amostra==0)$titulos=$data;
                            else
                            {
                            	$total_produtos++;
                            	atualiza_dados_produtos($titulos,$data,$total_produtos_update,$total_produtos_insert,$erros);
                            }
                            $amostra++;
                        }
                    }
                    else
                    {
                        $fp = fopen($file,"r");
                        while ( ($data = fgetcsv($fp,filesize($file) ,';','"')) !== false)
                        {

                            if($amostra==0)$titulos=$data;
                            else
                            {
                            	$total_produtos++;
                            	atualiza_dados_produtos($titulos,$data,$total_produtos_update,$total_produtos_insert,$erros);
                            }
                            $amostra++;
                        }
                        fclose($fp);
                    }


                    echo '<div style="color:#F00">'.date('d-m-Y H:m:s').' - total de produtos: '.$total_produtos.'<br><br>'.date('d-m-Y H:m:s').' - produtos actualizados: '.$total_produtos_update.'<br><br>'.date('d-m-Y H:m:s').' - produtos inseridos: '.$total_produtos_insert.' </div><br>';
                    echo '<div style="color:#F00">'.date('d-m-Y H:m:s').' - PROCESSO TERMINADO</div><br>';
                    if ($erros) echo 'obs: ocorreram erros - consultar logs</div>';
                    else echo 'obs: não se registaram erros</div>';
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
    require_once ('../../layout/bottom.inc.php');
?>