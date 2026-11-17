<?php
	ini_set('max_execution_time','0');
	ini_set('memory_limit','-1');
    require_once('settings.inc.php');
    require_once('../../layout/header.inc.php'); 
    require_once('../../layout/body.inc.php');?>
	<table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td valign="top" align="left"><?php require_once('../../layout/menus.inc.php'); ?>
            <table class="p1" width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr><td style="color:#FFF" align="center"><h3>Importa&ccedil;&atilde;o de produtos</h3></td></tr>
            </table>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td style="color:#fff;padding:10px;font-family:monospace;font-size:15px;" align="left"  bgcolor="#FFF"><?
                $ficheirocsv['name']=$_FILES['import_file']['name']; 
                $ficheirocsv['temp']=$_FILES['import_file']['tmp_name'];
            
                $ext=substr($ficheirocsv['name'], strpos($ficheirocsv['name'], '.')+1);
                if($ext!='txt' && $ext!='TXT')
                {
                   echo '<div style="color:#F00;">ERRO: tipo de ficheiro errado. Deve ser CSV (comma-separated values)</div>';
                }
                else
                {
                    $filename = 'dotbright_feed_export';
                    // $filename = 'produtos'.date('YmdHis');
                    $result=copy($ficheirocsv['temp'],FILE_PATH.$filename.'.'.$ext);
                   
	
					if(!$result) echo '<div style="color:#F00;">ERRO: Não foi possível colocar o ficheiro no servidor na pasta '.FILE_PATH.$filename.'.'.$ext.'</div>';
                    else
                    {
                        
						$f = FILE_PATH.$filename.'.'.$ext;

						$new_heading =  "Ref;Ref_Fabricante;Designacao;Preco_Final;Stock;Unidade_Negocio;Linha_Produto;Familia;Sub-Familia;Categoria;Marca;Data_modificacao;Descricao;Imagem;URL_Imagem;ean;Preco_Unitario;Taxa;IVA;IVATXT";
						// $new_heading =  "ItemCode	ItemDesc	ExtDesc	LOCALIZACAO	SAC	STM	SMI	QTE	DUV	DUC	PVP	PCU	IVA	XNGroupDesc_PT	XNCatDesc_PT	GEN	GPR	CLA	TPR	CAR	GAM	V(0)	V(1)	V(2)	V(3)	V(4)	V(5)	V(6)	V(7)	V(8)	V(9)	V(10)	V(11)	V(12)	V(13)	V(14)	V(15)	V(16)	V(17)	V(18)	V(19)	V(20)	V(21)	V(22)	V(23)	DTVAL	FPD	LAD	PRATELEIRA	GAMA	GRUPOHOMOGENEO	INACTIVO	PVP5	CNPEM";
						$new_heading .= "\n";

						// read into array
						$arr = file($f);

						// edit first line
						$arr[0] = $new_heading;

						// write back to file
						if(file_put_contents($f, implode($arr)))
						{
						
							$path = dirname( __FILE__ ) . '/utils/insere_prods.php';
							if(file_exists($path)) echo 'file exists<br>';
							$exe_file = "php ".$path." > /dev/null &";
							exec($exe_file);							
							
							echo '<div style="color:#F00;">--> o ficheiro foi copiado com sucesso para o servidor, vai se iniciar a actualização dos dados<br></div>';							
							
						}
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
	<? require_once('../../layout/bottom.inc.php');?>