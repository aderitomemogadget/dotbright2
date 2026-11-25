<?php
if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__) exit();

function getAllMetodosExpedicao($lang='',$tipo=1,$m_pais=165)
{
    if($lang == '') $lang=$GLOBALS['site_lang'];
    $db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
    $rs=new Dataobj();
    $sql="SELECT mp.*,mpl.Cabecalho,mpl.Texto1 FROM metodos_expedicao AS mp INNER JOIN metodos_expedicao_lang AS mpl ON mp.ID=mpl.ID_PAI WHERE mpl.Lang='$lang' AND Visivel='1' AND Tipo='".$tipo."' AND m_pais='".$m_pais."' ORDER BY mp.Ordem ASC,mp.ID ASC";
    $rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
    return $rs;
}
/*function getAllMetodosExpedicao2($lang='', $tipo=1,$pais=165)
{
    if ($lang == '') $lang=$GLOBALS['site_lang'];
    $db_link=db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
    $rs=new Dataobj();
 echo   $sql="SELECT mp.*,mpl.Cabecalho ,mpl.Texto1 FROM metodos_expedicao AS mp INNER JOIN metodos_expedicao_lang AS mpl ON mp.ID=mpl.ID_PAI WHERE mp.m_pais='$pais' AND mpl.Lang='$lang' AND Visivel='1' AND Tipo!=3 AND Tipo!=4 ORDER BY mp.Ordem ASC";
    $rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
    return $rs;
}*/
function getAllMetodosExpedicao2($lang='', $tipo=1,$pais=165)
{
    if ($lang == '') $lang=$GLOBALS['site_lang'];

    if($GLOBALS['log2']==1 && ($GLOBALS['cli_id']==1293)){echo '<hr>funcao getAllMetodosExpedicao2 vamos fazer um query '.$tipo;}

    $db_link=db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
    $rs=new Dataobj();
    //$sql="SELECT mp.*,mpl.Cabecalho ,mpl.Texto1 FROM metodos_expedicao AS mp INNER JOIN metodos_expedicao_lang AS mpl ON mp.ID=mpl.ID_PAI WHERE mpl.Lang='$lang' AND Visivel='1' AND Tipo!=3 AND Tipo!=4 ORDER BY mp.Ordem ASC";
    $sql="SELECT mp.*,mpl.Cabecalho ,mpl.Texto1 FROM metodos_expedicao AS mp INNER JOIN metodos_expedicao_lang AS mpl ON mp.ID=mpl.ID_PAI WHERE mp.m_pais='$pais' AND mpl.Lang='$lang' AND Visivel='1' AND Tipo!=3 AND Tipo!=4 ORDER BY mp.Ordem ASC";
    $rs->db_open($db_link, $GLOBALS['DB_name'], $sql);

    if($GLOBALS['log2']==1 && ($GLOBALS['cli_id']==1293))echo $sql;
    // echo $sql;
    return $rs;
}
function getFirstMetodosExpedicao($lang='',$tipo=1,$m_pais=165)
{
    if($lang == '') $lang=$GLOBALS['site_lang'];
    $db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
    $rs=new Dataobj();
    $sql="SELECT mp.*,mpl.Cabecalho,mpl.Texto1 FROM metodos_expedicao AS mp INNER JOIN metodos_expedicao_lang AS mpl ON mp.ID=mpl.ID_PAI WHERE mpl.Lang='$lang' AND Visivel='1' AND Tipo='".$tipo."' AND m_pais='".$m_pais."' ORDER BY mp.Ordem ASC LIMIT 1";
    $rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
    return $rs;
}
function getMetodoExpedicaoById($id,$lang='')
{
    if($lang == '') $lang=$GLOBALS['site_lang'];
    $db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
    $rs=new Dataobj();
    $sql="SELECT mp.*,mpl.Cabecalho,mpl.Texto1 FROM metodos_expedicao AS mp INNER JOIN metodos_expedicao_lang AS mpl ON mp.ID=mpl.ID_PAI WHERE mpl.Lang='$lang' AND mp.ID='$id' ORDER BY mp.ID ASC";
    $rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
    return $rs;
}
function getNomeMetodoExpedicaoById($id)
{
    $db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
    $rs=new Dataobj();
    $metodo_expedicao='';
    $sql="SELECT mel.Cabecalho FROM metodos_expedicao me INNER JOIN metodos_expedicao_lang mel ON mel.ID_PAI=me.ID WHERE me.ID='$id' AND mel.Lang='{$GLOBALS['site_lang']}'";
    $rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
    if(!$rs->EOF())
    {
        $metodo_expedicao=$rs->db_data['Cabecalho'];
    }
    $rs->db_close();
    return $metodo_expedicao;
}
function getEncomendaMetodoExpedicao($metodoExpedicao,$tipo=1,$m_pais=165)
{
    if(!$m_pais)$m_pais=165;
	if($metodoExpedicao == 0 )$rs=getFirstMetodosExpedicao('',$tipo,$m_pais);
    else $rs=getMetodoExpedicaoById($metodoExpedicao);
    if(!$rs->EOF())$info=$rs->db_data;
	else $info=false;
    $rs->db_close();
    return $info;
}
function showLayoutMetodosExpedicao($metodoExpedicao=0,$tipo=1,$m_pais=165)
{
    if(!$m_pais)$m_pais=165;
    ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <span class="icon">
                    <i class="fa fa-book"></i>
                </span>
                <span class="text">Método de Expedição</h5><br>Por favor selecione o modo de entrega da sua encomenda</span>
            </h4>
        </div>
        <div class="panel-body">
            <?php

			$rs=getAllMetodosExpedicao('',$tipo,$m_pais);
            if(!$rs->EOF())
			{
                //Como por pais os ids podem mudar:
                $found=0;
                while (!$rs->EOF())
                {
                    $ID=$rs->db_data['ID'];
                    
                    if($_GET['pp']==1)echo "$ID=$metodoExpedicao|$ID<br>";


                    if($ID==$metodoExpedicao)$found=1;
                    $rs->db_next();
                }
                $rs->db_close();
                $rs=getAllMetodosExpedicao('',$tipo,$m_pais);


                $pos=0;
                while (!$rs->EOF())
				{
                    $nome_metodo=$rs->db_data['Cabecalho'];
                    $ID=$rs->db_data['ID'];

                    //Selecciona o primeiro por defeito
                    if(!$metodoExpedicao || !$found)$metodoExpedicao=$ID;

                    if($_GET['pp']==1)echo "metodoExpedicao=$metodoExpedicao|$ID<br>";


                    $metodo_descricao=$rs->db_data['Texto1'];
                    $img_ext=$rs->db_data['img_ext'];
        			$portes=getPortesCarrinho($ID,$m_pais);
                    ?>
                    <div class="form-group">
                        <div class="radio" style="vertical-align:top">
                        
                            <label for="expedicao<?php echo $ID ?>" style="font-size:12px;border:solid 1px #ccc;width:100%;padding:15px 5px;cursor:pointer;display: flex;align-content: center;align-items: center;">
                                <div>
                                    <input class="metodos_expedicao" type="radio" name="m_metodoenvio" style="padding: 0;margin-top: 0 !important;" id="expedicao<?php echo $ID ?>" value="<?php echo $ID ?>" <?php echo ($metodoExpedicao == $ID) ? ' checked="checked" ' : ''; ?> onchange="reload_shopping_cart()">
                                </div>
                                <?php
                                if($portes > 0){
                                    if(dotbright_image_resolve($GLOBALS['site_shell_url'].'files/metodos_expedicao/'.$ID.'_1.'.$img_ext)){
                                        echo '<div><img src="files/metodos_expedicao/'.$ID.'_1.'.$img_ext.'" style="max-height:50px"></div>';
                                    }
                                    if($GLOBALS['mostra_preco_portes']==1)echo '<div>&nbsp;'.$nome_metodo.' ( + '.numberformat($portes).')</div>';
                                    else echo '<div>&nbsp;'.$nome_metodo.'</div>';
                                }
                                else{
                                    if(dotbright_image_resolve($GLOBALS['site_shell_url'].'files/metodos_expedicao/'.$ID.'_1.'.$img_ext)){
                                        echo '<div><img src="files/metodos_expedicao/'.$ID.'_1.'.$img_ext.'" style="max-height:50px"></div>';
                                    }
                                    echo '<div>&nbsp;'.$nome_metodo.'</div>';
                                }
                                ?>
                                <br>
                            </label> 
                            <?php
                            if($metodo_descricao != '' && $metodoExpedicao == $ID){
                                ?>
                                <div style="font-size:12px;vertical-align:top;padding-left:20px"><?php echo nl2br($metodo_descricao); ?></div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                    $pos++;
                    $rs->db_next();
                }
                $rs->db_close();
            }?>
        </div>
    </div><?php
}
function showLayoutMetodosExpedicao_mostra($metodoExpedicao,$tipo=1,$pais=165)
{
    ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <span class="icon">
                    <i class="fa fa-book"></i>
                </span>
                <span class="text">Método de Expedição<br><span style="font-weight:normal">Por favor selecione por baixo de cada produto o método de envio pretendido</span></span>
            </h4>
        </div>
        <div class="panel-body">
            <?php
            $rs=getAllMetodosExpedicao('', $tipo,$pais);
            if (!$rs->EOF()) {
                $pos=0;
                while (!$rs->EOF()) {
                    $nome_metodo=$rs->db_data['Cabecalho'];
                    $ID=$rs->db_data['ID'];
                    $metodo_descricao=$rs->db_data['Texto1'];
                    $Tipo=$rs->db_data['Tipo'];

                    $portes=getPortesCarrinho($ID, $pais);
                    ?>
                    <div class="form-group">
                        <div class="radio" style="vertical-align:top;font-size:12px;margin-bottom:10px;color:#000;text-transform:uppercase">
                            <b><?php echo ($pos+1).'. ';
                            if($portes > 0) echo $nome_metodo.' ( + '.numberformat($portes).')';
                            elseif($portes == 0 && $Tipo == 3) echo $nome_metodo.'';
                            else echo $nome_metodo.' ( Gratuíto)';?></b> <br>
                            <div style="font-size:12px;vertical-align:top;padding-left:10px"><?php echo nl2br($metodo_descricao); ?></div>
                        </div>
                    </div>
                    <?php
                    $pos++;
                    $rs->db_next();
                }
                $rs->db_close();
            }?>
        </div>
    </div><?php
}