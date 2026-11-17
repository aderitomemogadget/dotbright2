<?php
if (isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__)
{
    exit();
}

//amostra serve para apenas mostrar como o forms vai ficar no backoffice
function show_forms($id, $amostra='')
{
    $_SESSION['token_form'.$id]=uniqid("");
    //AMP *****************************
    if(isset($_SESSION['ss_amp']) && $_SESSION['ss_amp']==1)return show_forms_amp($id, $amostra);
    //*********************************



    global $DB_host,$DB_user,$DB_pass, $DB_name,$id_formulario;
    $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new DataObj;
    $id_formulario=$id;

    ?>
    <script type="text/javascript">
        function f_humman<?=$id_formulario?>(){document.getElementById("humman<?=$id_formulario?>").value=1;}
        function isInteger(value) {
            return value == parseInt(value);
        }
        function checkInt(field, update) {
            if (!isInteger(field.value)) {
                Apprise("Por favor insira um numero inteiro!");
                field.value="";
            }
            //if (update)update_fields();
        }
        function checkEmail(field) {
            var email=field.value;
            var AtPos=email.indexOf("@");
            var StopPos=email.lastIndexOf(".");
            if (email == "" || AtPos == -1 || StopPos == -1 || (StopPos < AtPos) || (StopPos - AtPos == 1)) {
                Apprise("Por favor insira um email correcto!");
                field.value="";
            }
        }
    </script>
    <script>
        $(function () {
            $(".datepicker").datepicker({dateFormat: 'yy-mm-dd'});
        });
    </script>
    <?php
    $IDb=$id;
    $rs->db_open($db_link, $DB_name, "Select * from forms where ID=$id");
    if (!$rs->EOF())
    {
        $ArrayPalavras=explode(";", $rs->db_data["Email"]);
        $tipo=$rs->db_data["Tipo"];
        $esconde_obrigatorio=$rs->db_data["obrigatorio"];
        $forms_dif=$rs->db_data["forms"];
        $conf_dif=$rs->db_data["conf_dif"];
        $popup=$rs->db_data["popup"];
        $n_palavras=count($ArrayPalavras);
    }
    $rs->db_close();

    if(isset($_GET['id_forms']) && $_GET['id_forms']==$id)
    {
        require_once 'core/forms_send_request.php';
    }

    if (!$amostra)
    {
        echo '<form id="form'.$id.'" name="form'.$id.'" method="POST" onSubmit="return form_verify'.$id.'()" action="?id_forms='.$id.'" enctype="multipart/form-data">';
        echo '<input type="hidden" name="popup" value="' . $popup . '" />';
        echo '<input type="hidden" name="ajax" value="1" />';
        echo '<input type="hidden" name="page_id" value="' . $GLOBALS['page_id'] . '" />';
        echo '<input type="hidden" name="id_forms" value="' . $id . '" />';
        echo '<input type="hidden" name="URL" value="" />';
        echo '<input type="hidden" name="marketing" id="marketing" value="0" />';
        echo '<input type="hidden" id="humman'.$id_formulario.'" name="humman'.$id_formulario.'" value="-1">';
        echo '<input type="hidden" id="token'.$id_formulario.'" name="token'.$id_formulario.'" value="'.$_SESSION['token_form'.$id].'">';
        echo '<input type="hidden" id="ss'.$id_formulario.'" name="ss'.$id_formulario.'" value="">';
    }



    $border=0;
    $cellpadding=0;
    $cellspacing=0;

    if ($conf_dif == 1)
    {
        echo troca_campos($forms_dif, $ArrayPalavras, $tipo, $esconde_obrigatorio, $forms_dif, $conf_dif, $n_palavras,$id);

        $str_obrigatorios=campos_obrigatorios($id);
        if ($str_obrigatorios)
        {
            ?>
            <script type="text/javascript">
                function envia<?=$id;?>()
                {
                    $.ajax({
                            type: "POST",
                            url: SITE_HTTP_AJAX_URL+"get_ss.php",
                            data: { si: "<?=$id?>",ss: "<?=$_SESSION['token_form'.$id]?>"
                            }
                        }).done(function(resultado)
                        {
                            if(resultado!="")
                            {
                                $('#ss<?=$id_formulario?>').val(resultado);
                                $('#form<?=$id_formulario?>').submit();
                            }
                        });
                }
                function form_verify<?=$id?>()
                {
                    var campos_obrigatorios=new Array(<?=$str_obrigatorios?>);
                    var campos_preenchidos;
                    for (var obj=0; obj < campos_obrigatorios.length; obj++) {
                        $('#' + campos_obrigatorios[obj]).closest('.form-group').removeClass('has-error');
                    }
                    for (var obj2=0; obj2 < campos_obrigatorios.length; obj2++) {
                        if($('#' + campos_obrigatorios[obj2]).val()=="" || $('#' + campos_obrigatorios[obj2]).val()==0){
                            campos_preenchidos=1;
                            $('#' + campos_obrigatorios[obj2]).closest('.form-group').addClass('has-error');
                        }
                        if(document.getElementById('rgpd') && document.getElementById('rgpd').checked == false) {
                            campos_preenchidos = 3;
                        }
                    }
                    if (campos_preenchidos == 1) {
                        Apprise("<?php echo $GLOBALS['key_obrigatorio'][$GLOBALS['site_lang']];?>");
                        return false;
                    }
                    else if(campos_preenchidos == 3) {
                        Apprise("Selecione que leu e entendeu a Política de Privacidade e Cookies", 'erro');
                        return false;
                    }
                    /*else{
                        $("#form<?= $id?>")[0].reset();
                    }*/
                    return true;
                }
            </script>
            <?php
        }
        else
        {
            ?>
            <script type="text/javascript">
                function envia<?=$id;?>()
                {
                    $.ajax({
                            type: "POST",
                            url: SITE_HTTP_AJAX_URL+"get_ss.php",
                            data: { si: "<?=$id?>",ss: "<?=$_SESSION['token_form'.$id]?>"
                            }
                        }).done(function(resultado)
                        {
                            if(resultado!="")
                            {
                                $('#ss<?=$id_formulario?>').val(resultado);
                                $('#form<?=$id_formulario?>').submit();
                            }
                        });
                }
                function form_verify<?= $id?>() {
                    <? $sim="true";?>
                    return true;
                }
            </script>
    <?php
        }
    }
    else
    {

        $str_obrigatorios=campos_obrigatorios($id);
        if ($str_obrigatorios)
        {
            ?>
            <script type="text/javascript">
                function envia<?=$id;?>()
                {
                    $.ajax({
                            type: "POST",
                            url: SITE_HTTP_AJAX_URL+"get_ss.php",
                            data: { si: "<?=$id?>",ss: "<?=$_SESSION['token_form'.$id]?>"
                            }
                        }).done(function(resultado)
                        {
                            if(resultado!="")
                            {
                                $('#ss<?=$id_formulario?>').val(resultado);
                                $('#form<?=$id_formulario?>').submit();
                            }
                        });
                }
                function form_verify<?=$id?>()
                {
                    var campos_obrigatorios=new Array(<?=$str_obrigatorios?>);
                    var campos_preenchidos;
                    for (var obj=0; obj < campos_obrigatorios.length; obj++) {
                        $('#' + campos_obrigatorios[obj]).closest('.form-group').removeClass('has-error');
                    }
                    for (var obj2=0; obj2 < campos_obrigatorios.length; obj2++) {
                        if($('#' + campos_obrigatorios[obj2]).val()=="" || $('#' + campos_obrigatorios[obj2]).val()==0){
                            campos_preenchidos=1;
                            $('#' + campos_obrigatorios[obj2]).closest('.form-group').addClass('has-error');
                        }
                    }
                    if (campos_preenchidos == 1) {
                        Apprise('Campos de Preenchimento Obrigatório!');
                        return false;
                    }
                    else{

                    	return true;

                        //$("#form<?= $id?>")[0].reset();
                        /*$.ajax({
                            type: "POST",
                            url: SITE_HTTP_AJAX_URL+"get_ss.php",
                            data: { si: "<?=$id?>",ss: "<?=$_SESSION['token_form'.$id]?>"
                            }
                        }).done(function(resultado)
                        {
                            if(resultado!="")
                            {
                                $('#ss<?=$id_formulario?>').val(resultado);
                                return true;
                            }
                        });*/
                    }
                    return true;
                }
            </script>
            <?php
        }

        $obrigatorio=0;
        $str_obrigatorios='';
        $comBotaoSubmeter=FALSE;

        $sql="SELECT * FROM form_fields WHERE ID_ORG='$id' ORDER BY Ordem ASC";
        $id_form=$id;
        $rs->db_open($db_link, $DB_name, $sql);
        while (!$rs->EOF())
        {
            $dentro='';
            $largura=$rs->db_data["length2"];
            $tipo_campo=$rs->db_data["Tipo"];
            $id_formlario=$rs->db_data["ID"];
            $I1=$rs->db_data["img_ext"];
            $nome_campo=$rs->db_data["Name"];
            $alinhar_botao=$rs->db_data["alinhar_botao"];
            $dentro=$rs->db_data["dentro"];
            $row=$rs->db_data["linhas"];
            $altura=$rs->db_data["altura"];
            if (!$altura)
            {
                $altura="150";
            }
            if ($altura)
            {
                $altura=$altura . "px";
            }
            $Length=$rs->db_data["Length"];
            if (!$Length)
            {
                $Length="255";
            }
            $update_fields=1;
            if ($update_fields)
            {
                $update_fields2=1;
            }
            else
            {
                $update_fields2=0;
            }


            //campo de texto 1 linha
            if ($rs->db_data["Tipo"] == 1)
            {
                echo '<div class="form-group">';
                if ($rs->db_data["Name"] != '' && $dentro == 0)
                {
                    echo '<label class="control-label" for="field' . $rs->db_data["ID"] . '">' . $rs->db_data["Name"];
                    if ($rs->db_data["Obrigatorio"] == 1)
                    {
                        echo ' <span class="mandatory">*</span>';
                    }
                    echo '</label>';
                }
                if ($rs->db_data["legenda"] != '')
                {
                    echo '<label class="control-label legend-label" for="field' . $rs->db_data["ID"] . '">' . $rs->db_data["legenda"];
                    if ($rs->db_data["Obrigatorio"] == 1)
                    {
                        echo ' <span class="mandatory">*</span>';
                    }
                    echo '</label>';
                }
                echo '<input onfocus="f_humman'.$id_formulario.'()" type="text" class="form-control" id="field' . $rs->db_data["ID"] . '" name="field' . $rs->db_data["ID"] . '" maxlength="' . $Length . '"';

                if ($rs->db_data["Name"] != '' && $dentro == 1)
                {
                    echo ' placeholder="' . $rs->db_data["Name"] . '" ';
                }

                echo '>';
                echo '</div>';

                if ($rs->db_data["Obrigatorio"] == 1)
                {
                    $obrigatorio=1;
                    if ($str_obrigatorios)
                    {
                        $str_obrigatorios .= ',"field' . $rs->db_data["ID"] . '"';
                    }
                    else
                    {
                        $str_obrigatorios='"field' . $rs->db_data["ID"] . '"';
                    }
                }
            }
            //campo de texto multiplas linhas
            if ($rs->db_data["Tipo"] == 2)
            {
                echo '<div class="form-group">';
                if ($rs->db_data["Name"] != '' && $dentro == 0)
                {
                    echo '<label class="control-label" for="field' . $rs->db_data["ID"] . '">' . $rs->db_data["Name"];
                    if ($rs->db_data["Obrigatorio"] == 1)
                    {
                        echo ' <span class="mandatory">*</span>';
                    }
                    echo '</label>';
                }
                if ($rs->db_data["legenda"] != '')
                {
                    echo '<label class="control-label legend-label" for="field' . $rs->db_data["ID"] . '">' . $rs->db_data["legenda"];
                    if ($rs->db_data["Obrigatorio"] == 1)
                    {
                        echo ' <span class="mandatory">*</span>';
                    }
                    echo '</label>';
                }
                echo '<textarea class="form-control" id="field' . $rs->db_data["ID"] . '" name="field' . $rs->db_data["ID"] . '" maxlength="' . $Length . '"';

                if ($rs->db_data["Name"] != '' && $dentro == 1)
                {
                    echo ' placeholder="' . $rs->db_data["Name"] . '" ';
                }

                echo '></textarea>';
                echo '</div>';

                if ($rs->db_data["Obrigatorio"] == 1)
                {
                    $obrigatorio=1;
                    if ($str_obrigatorios)
                    {
                        $str_obrigatorios .= ',"field' . $rs->db_data["ID"] . '"';
                    }
                    else
                    {
                        $str_obrigatorios='"field' . $rs->db_data["ID"] . '"';
                    }
                }
            }
            //campo de valores numericos
            if ($rs->db_data["Tipo"] == 3)
            {
                echo '<div class="form-group">';
                if ($rs->db_data["Name"] != '' && $dentro == 0)
                {
                    echo '<label class="control-label" for="field' . $rs->db_data["ID"] . '">' . $rs->db_data["Name"];
                    if ($rs->db_data["Obrigatorio"] == 1)
                    {
                        echo ' <span class="mandatory">*</span>';
                    }
                    echo '</label>';
                }
                if ($rs->db_data["legenda"] != '')
                {
                    echo '<label class="control-label legend-label" for="field' . $rs->db_data["ID"] . '">' . $rs->db_data["legenda"];
                    if ($rs->db_data["Obrigatorio"] == 1)
                    {
                        echo ' <span class="mandatory">*</span>';
                    }
                    echo '</label>';
                }
                echo '<input onfocus="f_humman'.$id_formulario.'()" onchange="checkInt(this, ' . $update_fields2 . ');" type="text" class="form-control" id="field' . $rs->db_data["ID"] . '" name="field' . $rs->db_data["ID"] . '" maxlength="' . $Length . '"';

                if ($rs->db_data["Name"] != '' && $dentro == 1)
                {
                    echo ' placeholder="' . $rs->db_data["Name"] . '" ';
                }

                echo '>';
                echo '</div>';

                if ($rs->db_data["Obrigatorio"] == 1)
                {
                    $obrigatorio=1;
                    if ($str_obrigatorios)
                    {
                        $str_obrigatorios .= ',"field' . $rs->db_data["ID"] . '"';
                    }
                    else
                    {
                        $str_obrigatorios='"field' . $rs->db_data["ID"] . '"';
                    }
                }
            }
            //Radio buttons
            if ($rs->db_data["Tipo"] == 5)
            {
                echo '<div class="form-group">';
                if ($rs->db_data["Name"] != '')
                {
                    echo '<label class="form-control-static">' . $rs->db_data["Name"];
                    if ($rs->db_data["Obrigatorio"] == 1)
                    {
                        echo ' <span class="mandatory">*</span>';
                    }
                    echo '</label>';
                }
                if ($rs->db_data["legenda"] != '')
                {
                    echo '<label class="control-label legend-label" for="field' . $rs->db_data["ID"] . '">' . $rs->db_data["legenda"];
                    if ($rs->db_data["Obrigatorio"] == 1)
                    {
                        echo ' <span class="mandatory">*</span>';
                    }
                    echo '</label>';
                }

                $rs2=BuscarRegistoOpcoes($rs->db_data["ID"]);
                while (!$rs2->EOF())
                {
                    if ($rs->db_data["Align"] == 1)
                    {
                        echo '<div class="radio">';
                        echo '<label>' . $rs2->db_data["Value"] . ' <input type="radio" name="field' . $rs->db_data["ID"] . '" id="field' . $rs->db_data["ID"] . '" value="' . $rs2->db_data["Value"] . '"></label>';
                        echo '</div>';
                    }
                    else
                    {
                        echo '<div class="radio">';
                        echo '<label><input type="radio" name="field' . $rs->db_data["ID"] . '" id="field' . $rs->db_data["ID"] . '" value="' . $rs2->db_data["Value"] . '"> ' . $rs2->db_data["Value"] . '</label>';
                        echo '</div>';
                    }
                    $rs2->db_next();
                }

                if ($rs->db_data["Obrigatorio"] == 1)
                {
                    $obrigatorio=1;
                    if ($str_obrigatorios)
                    {
                        $str_obrigatorios .= ',"field' . $rs->db_data["ID"] . '"';
                    }
                    else
                    {
                        $str_obrigatorios='"field' . $rs->db_data["ID"] . '"';
                    }
                }
                $rs2->db_close();
                echo '</div>';
            }
            //Check-boxes
            if ($rs->db_data["Tipo"] == 6)
            {
                echo '<div class="form-group">';
                if ($rs->db_data["Name"] != '')
                {
                    echo '<label class="form-control-static">' . $rs->db_data["Name"];
                    if ($rs->db_data["Obrigatorio"] == 1)
                    {
                        echo ' <span class="mandatory">*</span>';
                    }
                    echo '</label>';
                }
                if ($rs->db_data["legenda"] != '')
                {
                    echo '<label class="control-label legend-label" for="field' . $rs->db_data["ID"] . '">' . $rs->db_data["legenda"];
                    if ($rs->db_data["Obrigatorio"] == 1)
                    {
                        echo ' <span class="mandatory">*</span>';
                    }
                    echo '</label>';
                }

                $rs2=BuscarRegistoOpcoes($rs->db_data["ID"]);
                $i=0;
                while (!$rs2->EOF())
                {
                    if ($rs->db_data["Align"] == 1)
                    {
                        echo '<div class="checkbox">';
                        echo '<label>' . $rs2->db_data["Value"] . ' <input type="checkbox" name="field' . $rs->db_data["ID"] . '" id="field' . $rs->db_data["ID"] . '" value="' . $rs2->db_data["Value"] . '"></label>';
                        echo '</div>';
                    }
                    else
                    {
                        echo '<div class="checkbox">';
                        echo '<label><input type="checkbox" name="field' . $rs->db_data["ID"] . '" id="field' . $rs->db_data["ID"] . '" value="' . $rs2->db_data["Value"] . '"> ' . $rs2->db_data["Value"] . '</label>';
                        echo '</div>';
                    }

                    $rs2->db_next();
                    $i++;
                }

                if ($rs->db_data["Obrigatorio"] == 1)
                {
                    $obrigatorio=1;
                    if ($str_obrigatorios)
                    {
                        $str_obrigatorios .= ',"field' . $rs->db_data["ID"] . '"';
                    }
                    else
                    {
                        $str_obrigatorios='"field' . $rs->db_data["ID"] . '"';
                    }
                }
                echo '<input type="hidden" name="field' . $rs->db_data["ID"] . '_counter" value="' . $i . '">';
                $rs2->db_close();
                echo '</div>';
            }
            //Combobox
            if ($rs->db_data["Tipo"] == 7)
            {
                echo '<div class="form-group">';
                if ($rs->db_data["Name"] != '' && $dentro == 0)
                {
                    echo '<label class="control-label" for="field' . $rs->db_data["ID"] . '">' . $rs->db_data["Name"];
                    if ($rs->db_data["Obrigatorio"] == 1)
                    {
                        echo ' <span class="mandatory">*</span>';
                    }
                    echo '</label>';
                }
                if ($rs->db_data["legenda"] != '')
                {
                    echo '<label class="control-label legend-label" for="field' . $rs->db_data["ID"] . '">' . $rs->db_data["legenda"];
                    if ($rs->db_data["Obrigatorio"] == 1)
                    {
                        echo ' <span class="mandatory">*</span>';
                    }
                    echo '</label>';
                }
                echo '<select class="form-control" name="field' . $rs->db_data["ID"] . '">';

                if ($dentro == 1)
                {
                    echo '<option value="">' . $nome_campo . '</option>';
                }
                $rs2=BuscarRegistoOpcoes($rs->db_data["ID"]);
                $IDb=$rs->db_data["ID"];
                if(isset($_GET['op'.$IDb]) && $_GET['op'.$IDb]>0) $z=$_GET['op'.$IDb];
                $i=1;
                while (!$rs2->EOF())
                {
                    if($i==$z)echo '<option selected value="' . $rs2->db_data["Value"] . '">' . $rs2->db_data["Value"] . '</option>';
                    else echo '<option value="' . $rs2->db_data["Value"] . '">' . $rs2->db_data["Value"] . '</option>';
                    $rs2->db_next();
                    $i++;
                }
                $rs2->db_close();

                echo '</select>';

                if ($rs->db_data["Obrigatorio"] == 1)
                {
                    $obrigatorio=1;
                    if ($str_obrigatorios)
                    {
                        $str_obrigatorios .= ',"field' . $rs->db_data["ID"] . '"';
                    }
                    else
                    {
                        $str_obrigatorios='"field' . $rs->db_data["ID"] . '"';
                    }
                }
                echo '</div>';
            }
            //Listbox
            if ($rs->db_data["Tipo"] == 8)
            {
                echo '<div class="form-group">';
                if ($rs->db_data["Name"] != '' && $dentro == 0)
                {
                    echo '<label class="control-label" for="field' . $rs->db_data["ID"] . '">' . $rs->db_data["Name"];
                    if ($rs->db_data["Obrigatorio"] == 1)
                    {
                        echo ' <span class="mandatory">*</span>';
                    }
                    echo '</label>';
                }
                if ($rs->db_data["legenda"] != '')
                {
                    echo '<label class="control-label legend-label" for="field' . $rs->db_data["ID"] . '">' . $rs->db_data["legenda"];
                    if ($rs->db_data["Obrigatorio"] == 1)
                    {
                        echo ' <span class="mandatory">*</span>';
                    }
                    echo '</label>';
                }
                echo '<select multiple class="form-control" name="field' . $rs->db_data["ID"] . '">';

                $rs2=BuscarRegistoOpcoes($rs->db_data["ID"]);
                while (!$rs2->EOF())
                {
                    if ($rs2->db_data["Associate_page"] == $_GET['page_id'])
                    {
                        echo '<option value="' . $rs2->db_data["Value"] . '" selected="selected">' . $rs2->db_data["Value"] . '</option>';
                    }
                    else
                    {
                        echo '<option value="' . $rs2->db_data["Value"] . '">' . $rs2->db_data["Value"] . '</option>';
                    }
                    $rs2->db_next();
                }
                $rs2->db_close();
                echo '</select>';

                if ($rs->db_data["Obrigatorio"] == 1)
                {
                    $obrigatorio=1;
                    if ($str_obrigatorios)
                    {
                        $str_obrigatorios .= ',"field' . $rs->db_data["ID"] . '"';
                    }
                    else
                    {
                        $str_obrigatorios='"field' . $rs->db_data["ID"] . '"';
                    }
                }
                echo '</div>';
            }
            //campo de upload ficheiro
            if ($rs->db_data["Tipo"] == 9)
            {
                echo '<div class="form-group">';
                if ($rs->db_data["Name"] != '' && $dentro == 0)
                {
                    echo '<label class="control-label" for="field' . $rs->db_data["ID"] . '">' . $rs->db_data["Name"];
                    if ($rs->db_data["Obrigatorio"] == 1)
                    {
                        echo ' <span class="mandatory">*</span>';
                    }
                    echo '</label>';
                }
                if ($rs->db_data["legenda"] != '')
                {
                    echo '<label class="control-label legend-label" for="field' . $rs->db_data["ID"] . '">' . $rs->db_data["legenda"];
                    if ($rs->db_data["Obrigatorio"] == 1)
                    {
                        echo ' <span class="mandatory">*</span>';
                    }
                    echo '</label>';
                }
                echo '<input onfocus="f_humman'.$id_formulario.'()" type="file" class="form-control" id="field' . $rs->db_data["ID"] . '" name="field' . $rs->db_data["ID"] . '">';
                echo '</div>';

                if ($rs->db_data["Obrigatorio"] == 1)
                {
                    $obrigatorio=1;
                    if ($str_obrigatorios)
                    {
                        $str_obrigatorios .= ',"field' . $rs->db_data["ID"] . '"';
                    }
                    else
                    {
                        $str_obrigatorios='"field' . $rs->db_data["ID"] . '"';
                    }
                }
            }
            //campo de data automatica
            if ($rs->db_data["Tipo"] == 10)
            {
                echo '<input type="hidden" name="field' . $rs->db_data["ID"] . '" value="' . date('Y-m-d') . '">';
            }
            //campo de data
            if ($rs->db_data["Tipo"] == 13)
            {
                echo '<div class="form-group">';
                if ($rs->db_data["Name"] != '' && $dentro == 0)
                {
                    echo '<label class="control-label" for="field' . $rs->db_data["ID"] . '">' . $rs->db_data["Name"];
                    if ($rs->db_data["Obrigatorio"] == 1)
                    {
                        echo ' <span class="mandatory">*</span>';
                    }
                    echo '</label>';
                }
                if ($rs->db_data["legenda"] != '')
                {
                    echo '<label class="control-label legend-label" for="field' . $rs->db_data["ID"] . '">' . $rs->db_data["legenda"];
                    if ($rs->db_data["Obrigatorio"] == 1)
                    {
                        echo ' <span class="mandatory">*</span>';
                    }
                    echo '</label>';
                }
                echo '<input onfocus="f_humman'.$id_formulario.'()" type="text" class="form-control datepicker" id="field' . $rs->db_data["ID"] . '" name="field' . $rs->db_data["ID"] . '" maxlength="' . $Length . '">';

                echo '</div>';

                if ($rs->db_data["Obrigatorio"] == 1)
                {
                    $obrigatorio=1;
                    if ($str_obrigatorios)
                    {
                        $str_obrigatorios .= ',"field' . $rs->db_data["ID"] . '"';
                    }
                    else
                    {
                        $str_obrigatorios='"field' . $rs->db_data["ID"] . '"';
                    }
                }
            }
            //Campo de email
            if ($rs->db_data["Tipo"] == 31)
            {
                echo '<div class="form-group">';
                if ($rs->db_data["Name"] != '' && $dentro == 0)
                {
                    echo '<label class="control-label" for="Email' . $rs->db_data["ID"] . '">' . $rs->db_data["Name"];
                    if ($rs->db_data["Obrigatorio"] == 1)
                    {
                        echo ' <span class="mandatory">*</span>';
                    }
                    echo '</label>';
                }
                if ($rs->db_data["legenda"] != '')
                {
                    echo '<label class="control-label legend-label" for="Email' . $rs->db_data["ID"] . '">' . $rs->db_data["legenda"];
                    if ($rs->db_data["Obrigatorio"] == 1)
                    {
                        echo ' <span class="mandatory">*</span>';
                    }
                    echo '</label>';
                }
                echo '<input type="email" onChange="checkEmail(this);" class="form-control" id="Email' . $rs->db_data["ID"] . '" name="Email' . $rs->db_data["ID"] . '" maxlength="' . $Length . '"';

                if ($rs->db_data["Name"] != '' && $dentro == 1)
                {
                    echo ' placeholder="' . $rs->db_data["Name"] . '" ';
                }

                echo '>';
                echo '</div>';

                if ($rs->db_data["Obrigatorio"] == 1)
                {
                    $obrigatorio=1;
                    if ($str_obrigatorios)
                    {
                        $str_obrigatorios .= ',"Email' . $rs->db_data["ID"] . '"';
                    }
                    else
                    {
                        $str_obrigatorios='"Email' . $rs->db_data["ID"] . '"';
                    }
                }
            }
            //Botão de submissão
            if ($rs->db_data["Tipo"] == 33)
            {
                $comBotaoSubmeter=TRUE;
                echo '<div class="form-group text-' . $alinhar_botao . '">';
                if ($I1 && stream_resolve_include_path($GLOBALS['site_shell_url'] . "files/forms/" . $id_formlario . "_bt_1." . $I1))
                {
                    echo '<input type="image" class="image" src="' . $GLOBALS['site_http_url'] . "files/forms/" . $id_formlario . "_bt_1." . $I1 . '" title="&nbsp;' . $nome_campo . '&nbsp;" alt="&nbsp;' . $nome_campo . '&nbsp;">';
                }
                else
                {
                    echo '<input onclick="javascript:envia'.$id_form.'()" type="button" value="&nbsp;' . $nome_campo . '&nbsp;" class="button3">';
                }
                echo '</div>';
            }
            //Captcha
            if ($rs->db_data["Tipo"] == 34)
            {
                echo '<input type="text" name="URL" style="display:none" value="">';
            }
            //RGPD
            if ($rs->db_data["Tipo"] == 35)
            {
                echo '<div class="form-group">';
                // if ($rs->db_data["Name"] != '')
                // {
                //     //echo '<label class="control-label">' . $rs->db_data["Name"];
                //     if ($rs->db_data["Obrigatorio"] == 1)
                //     {
                //        // echo ' <span class="mandatory">*</span>';
                //     }
                //     //echo '</label>';
                // }
                // if ($rs->db_data["legenda"] != '')
                // {
                //     //echo '<label class="control-label legend-label" for="field' . $rs->db_data["ID"] . '">' . $rs->db_data["legenda"];
                //     if ($rs->db_data["Obrigatorio"] == 1)
                //     {
                //       //  echo ' <span class="mandatory">*</span>';
                //     }
                //     //echo '</label>';
                // }

                $rs2=BuscarRegistoOpcoes($rs->db_data["ID"]);
                $i=0;
                while (!$rs2->EOF())
                {
                    // if ($rs->db_data["Align"] == 1)
                    // {
                    //     echo '<div class="checkbox">';
                    //     echo '<label><span><input type="checkbox" name="field' . $rs->db_data["ID"] . '_'.$i.'" id="field' . $rs->db_data["ID"] . '" value="' . $rs2->db_data["Value"] . '"></span>' . $rs2->db_data["Value"] . ' </label>';
                    //     echo '</div>';
                    // }
                    // else
                    // {
                    //     echo '<div class="checkbox">';
                        echo '<label><input type="checkbox" name="field' . $rs->db_data["ID"] . '_'.$i.'" id="rgpd" value="' . $rs2->db_data["Value"] . '"> ' . $rs2->db_data["Value"] . '</label>';
                    //     echo '</div>';
                    // }

                    $rs2->db_next();
                    $i++;
                }

                if ($rs->db_data["Obrigatorio"] == 1)
                {
                    $obrigatorio=1;
                    if ($str_checks)
                    {
                        $str_checks .= ',"field' . $rs->db_data["ID"] . '"';
                    }
                    else
                    {
                        $str_checks='"field' . $rs->db_data["ID"] . '"';
                    }
                }
                // echo '<input type="hidden" name="field' . $rs->db_data["ID"] . '_counter" value="' . $i . '">';
                $rs2->db_close();
                // echo '</div>';
                if ($rs->db_data["Name"] != '')
                {
                    echo '<label class="control-label2" for="rgpd">' . $rs->db_data["Name"];
                    if ($rs->db_data["Obrigatorio"] == 1)
                    {
                        echo ' <span class="mandatory">*</span>';
                    }
                    echo '</label>';
                }
                echo '</div>';
            }

            $rs->db_next();
        }
        $rs->db_close();

        if ($comBotaoSubmeter == FALSE)
        {
            echo '<div class="form-group text-' . $alinhar_botao . '">';
            echo '<input onclick="javascript:envia'.$id_form.'()" type="button" value="&nbsp;' . $GLOBALS['key_enviar'][$GLOBALS['site_lang']] . '&nbsp;" class="button3">';
            echo '</div>';
        }

        if ($obrigatorio)
        {
            ?>
            <script type="text/javascript">
            	function envia<?=$id;?>()
                {
                    $.ajax({
                            type: "POST",
                            url: SITE_HTTP_AJAX_URL+"get_ss.php",
                            data: { si: "<?=$id?>",ss: "<?=$_SESSION['token_form'.$id]?>"
                            }
                        }).done(function(resultado)
                        {
                            if(resultado!="")
                            {
                                $('#ss<?=$id_formulario?>').val(resultado);
                                $('#form<?=$id_formulario?>').submit();
                            }
                        });
                }
                function form_verify<?php echo $id.$my_string;?>() {
                    var campos_obrigatorios=new Array(<?=$str_obrigatorios?>);
                    var campos_check=new Array(<?=$str_checks?>);
                    var campos_preenchidos;
                    for (var obj=0; obj < campos_obrigatorios.length; obj++) {
                        $('#' + campos_obrigatorios[obj]).closest('.form_div<?php echo $my_string;?>').removeClass('has-error');
                    }
                    for (obj=0; obj < campos_obrigatorios.length; obj++) {
                        if (document.forms['form<?=$id.$my_string?>'].elements[campos_obrigatorios[obj]] == "" || document.forms['form<?=$id.$my_string?>'].elements[campos_obrigatorios[obj]].value == 0) {
                            campos_preenchidos=1;
                            $('#' + campos_obrigatorios[obj]).closest('.form_div<?php echo $my_string;?>').addClass('has-error');
                        }
                    }
                    if(campos_check!='')
                    {

                        for (var obj=0; obj < campos_check.length; obj++) {
                        if(document.forms['form<?=$id.$my_string?>'].elements[campos_check[obj]].checked)
                            {

                            }else{
                                 campos_preenchidos=1;
                            $('#' + campos_check[obj]).closest('.form_div<?php echo $my_string;?>').addClass('has-error');
                            }
                        }
                    }

                    if (campos_preenchidos == 1)
                    {
                        Apprise("<?php echo $GLOBALS['key_obrigatorio'][$GLOBALS['site_lang']];?>");
                        return false;
                    }
                    else
                    {
                        /*var data = new FormData();
                        //Form data
                        var form_data = $('#form<?php echo $id.$my_string;?>').serializeArray();
                        $.each(form_data, function (key, input) {
                            data.append(input.name, input.value);
                        });

                        //File data
                        var file_data = $('input[name="my_images"]')[0].files;
                        for (var i = 0; i < file_data.length; i++) {
                            data.append("my_images[]", file_data[i]);
                        }*/

                        var form = $('#form<?php echo $id.$my_string;?>').closest("form");
                        var formData = new FormData(form[0]);


                        $.ajax({
                        url: '<?=$GLOBALS['site_http_url']?>core/forms_send_request2.php',
                        type: 'POST',
                        processData: false,
                        contentType: false,
                        data: formData,
                        success: function(data){
                            Apprise(data);
                            $('#form<?php echo $id.$my_string;?>')[0].reset();

                            //if(id == 11 || id == 17 || id == 18){ ga('send', { hitType: 'event', eventCategory: 'Pedidos de Contacto', eventAction: 'Submissão', eventLabel: 'Sucesso' }); }
                        }
                        });
                        return false;
                    }

                }


            </script>
            <?php
        }
        else
        {
            ?>
            <script type="text/javascript">
            	function envia<?=$id;?>()
                {
                    $.ajax({
                            type: "POST",
                            url: SITE_HTTP_AJAX_URL+"get_ss.php",
                            data: { si: "<?=$id?>",ss: "<?=$_SESSION['token_form'.$id]?>"
                            }
                        }).done(function(resultado)
                        {
                            if(resultado!="")
                            {
                                $('#ss<?=$id_formulario?>').val(resultado);
                                $('#form<?=$id_formulario?>').submit();
                            }
                        });
                }
            	function form_verify<?php echo $id;?>() {
                    return true;
                }</script><?php
        }
    }

    if (!$amostra)
    {
        echo '</form>';
    }
}

function box_open($nome='', $width='', $nome_erro='', $obrigatorio='', $dentro=0, $esconde_obrigatorio=0, $conf_dif=0)
{
    global $site_http_url;
    if ($conf_dif == 1)
    {
        $esconde_obrigatorio=1;
    }
    $return='';
    $return .= '<div class="inputdiv" id="' . $nome_erro . '">';

    return $return;
}

function box_close($nome_erro='')
{
    global $site_http_url;
    $return='</div>';

    return $return;
}

function box_close3($nome_erro='')
{
    global $site_http_url;
    $return='';
    $return .= '</td><td width="4"><img src="' . $site_http_url . 'layout/forms/form_right_bx.png" border=0></td>';
    if ($nome_erro)
    {
        $return .= '</td><td width="1" align="left" valign="top"><div style="position:relative;width:1px;"><div style="position:absolute;left:-10px;top:4px"><img src="' . $site_http_url . 'layout/forms/blank.gif" id="' . $nome_erro . '" border=0 width="4"></div></div></td>';
    }
    $return .= '</tr></table>';

    return $return;
}

function box_open2($nome='', $width='', $nome_erro='', $obrigatorio='', $dentro=0, $esconde_obrigatorio=0, $conf_dif=0) // textarea
{
    global $site_http_url;
    if ($conf_dif == 1)
    {
        $esconde_obrigatorio=1;
    }
    $return='';
    $return .= '<div class="inputdiv" id="' . $nome_erro . '">';

    return $return;
}

function box_close2($nome_erro='', $height='') // textarea
{
    global $site_http_url;
    $height=str_replace("px", '', $height);
    $return='';

    $return .= '</div>';

    return $return;
}

function mostra_registos($numero)
{
    global $spam, $site_lang;
    $numero=explode(",", $numero);
    $ID_ORG=$numero[0];
    $campos_id=$numero[1];
    function GetId($ID_ORG, $f_nome, $f_data1, $f_data2, $main_date, $product='')
    {
        $id_list=array();
        global $DB_host,$DB_user,$DB_pass, $DB_name;
        $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
        $teste=new DataObj;
        $sql="SELECT ID FROM form_fields WHERE Tipo=32";
        $teste->db_open($db_link, $DB_name, $sql);
        $prod_id=$teste->db_data["ID"];
        $teste->db_close();
        $rs=new DataObj;
        $sql="SELECT ID FROM form_records WHERE ID_ORG=" . $ID_ORG . " ORDER BY ID DESC";
        $rs->db_open($db_link, $DB_name, $sql);
        while (!$rs->EOF())
        {
            $id_org[]=$rs->db_data["ID"];
            $rs->db_next();
        }
        $rs->db_close();

        foreach ($id_org as $id)
        {
            $sql="SELECT * FROM form_records_fields WHERE ID_ORG=$id";
            $hRes=$rs->db_open($db_link, $DB_name, $sql);
            while (!$rs->EOF())
            {
                $Name=$rs->db_data["Name"];
                $Value=$rs->db_data["Valor"];
                $id=$rs->db_data["ID_ORG"];
                if ($f_nome == '' && !$f_data1 == '' && !$f_data2 == '')
                {
                    if ($Name == $main_date && $Value >= $f_data1 && $Value <= $f_data2)
                    {
                        $id_list[]=$id;
                    }
                }
                elseif (!$f_nome == '')
                {
                    if ($Name == $product && $Value == $f_nome)
                    {
                        $id_by_name[]=$id;
                    }
                    if (!$f_data1 == '' && !$f_data2 == '' && $id_by_name)
                    {
                        $id_list=GetIdByDate($id_by_name, $f_data1, $f_data2, $main_date);
                    }
                    else
                    {
                        $id_list=$id_by_name;
                    }
                }
                else
                {
                    $id_list[]=$id;
                }
                $rs->db_next();
            }
            $rs->db_close();
        }

        return $id_list;
    }

    global $DB_host,$DB_user,$DB_pass, $DB_name, $Nome, $site_lang;
    $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new DataObj;
    $rs2=new DataObj;
    $sql="SELECT * FROM forms WHERE ID=$ID_ORG";
    $hRes=$rs->db_open($db_link, $DB_name, $sql);
    if (!$rs->EOF())
    {
        $nome=$rs->db_data["Name"];
        $campos_listagem=$rs->db_data["Campos_listagem"];
        $titulos_listagem=$rs->db_data["Titulos_listagem"];
    }
    $rs->db_close();
    $tem_data_auto=0;
    $sql="SELECT * FROM form_fields WHERE Tipo=10 AND ID_ORG=$ID_ORG";
    $hRes=$rs->db_open($db_link, $DB_name, $sql);
    if (!$rs->EOF())
    {
        $tem_data_auto=1;
    }
    $rs->db_close();
    $tem_prod=0;
    $sql="SELECT * FROM form_fields WHERE Tipo=32 AND ID_ORG=$ID_ORG";
    $hRes=$rs->db_open($db_link, $DB_name, $sql);
    if (!$rs->EOF())
    {
        $tem_prod=1;
    }
    $rs->db_close();
    if (!$bgn)
    {
        $bgn=0;
    }
    if (!$num)
    {
        $num=100000;
    }
    $sql="SELECT COUNT(ID) AS Total FROM form_records WHERE ID_ORG=" . $ID_ORG;
    $hRes=$rs->db_open($db_link, $DB_name, $sql);
    if (!$hRes)
    {
        echo db_error($db_link);
    }
    $total_pag=ceil($rs->db_data['Total'] / $num);
    $rs->db_close();
    $a_campos=explode(";", $campos_listagem);
    $n_campos=count($a_campos);
    $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $data=new DataObj;
    $sql="SELECT ID,Cabecalho FROM products_lang";
    $hRes=$data->db_open($db_link, $DB_name, $sql);
    while (!$data->EOF())
    {
        $header[]=$data->db_data["Cabecalho"];
        $id[]=$data->db_data["ID"];
        $data->db_next();
        $counter++;
    }
    $data->db_close();
    ?>
    <html>
    <head>
        <title>ADMIN</title>
        <meta http-equiv=Content-Type content="text/html; charset=utf-8">
        <link rel="stylesheet" type="text/css" href="<?=$includes_path;?>styles.css"/>
        <link href="templates/styles.css" rel="stylesheet" type="text/css">
    </head>
<body>
    <script>function ficheiro() {
            open("exportar_excel.php?dmp=<?=uniqid('');?>", target="_self");
        }</script>
<table width=100% border=0 cellspacing=2 cellpadding=2>
    <tr>
    <td width="16" height=20 valign="middle">&nbsp;</td><?
    if ($tem_data_auto)
    {
        ?>
        <td width="200" align="left" class="preto">&nbsp;&nbsp;</td><? }
    if ($tem_prod)
    {
        ?>
        <td width="190" align="center" class="preto">&nbsp;</td><? }
    if ($tem_data_auto || $tem_prod)
    {
        ?>
        <td width="16" align="center" valign="middle">&nbsp;</td><? } ?>
<td valign=top bgcolor="<?=$cor;?>" class="preto">&nbsp;</td><?
    global $DB_host,$DB_user,$DB_pass, $DB_name, $Nome, $site_lang;
    $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $count=0;
    $rs=new DataObj;
    $sql="SELECT * FROM form_records WHERE ID_ORG=" . $ID_ORG . " ORDER BY form_records.ID ASC ";
    $hRes=$rs->db_open($db_link, $DB_name, $sql);
    if (!$hRes)
    {
        echo db_error($db_link);
    }
    $rs->db_close();

    $data=new DataObj;
    $sql="SELECT ID FROM form_fields WHERE ID_ORG=" . $ID_ORG . "  AND Tipo=32";
    $hRes=$data->db_open($db_link, $DB_name, $sql);
    $product=$data->db_data["ID"];
    $data->db_close();
    $data=new DataObj;
    $sql="SELECT ID FROM form_fields WHERE ID_ORG=" . $ID_ORG . " AND Tipo=10";
    $hRes=$data->db_open($db_link, $DB_name, $sql);
    $main_date=$data->db_data["ID"];
    $data->db_close();
    $rs=new DataObj;
    $sql="SELECT * FROM forms WHERE ID=" . $ID_ORG;
    $hRes=$rs->db_open($db_link, $DB_name, $sql);
    $campos_listagem=$campos_id;
    if ($campos_listagem)
    {
        $a_campos=explode(";", $campos_listagem);
        $n_campos=count($a_campos);
        $i=0;
        if ($f_nome)
        {
            $ID=GetId($ID_ORG, $f_nome, $f_data1, $f_data2, $main_date, $product);
        }
        elseif (!$f_data1 == '' && !$f_data2 == '')
        {
            $ID=GetId($ID_ORG, $f_nome, $f_data1, $f_data2, $main_date, $product);
        }
        else
        {
            $list=GetId($ID_ORG, $f_nome, $f_data1, $f_data2, $main_date, $product);
            $clean_list=array();
            $clean_list=array_unique($list);
            $i=0;
            $ID=array();
            foreach ($clean_list as $key => $value)
            {
                if (!is_null($value))
                {
                    $ID[$i]=$value;
                    $i++;
                }
            }
        }
        $ID_List=$ID; ?>
        <td align="right" valign="top" bgcolor="<?=$cor;?>" class="preto"></td>
        </tr>
        </table><?
        for ($count=0; $count < count($ID); $count++)
        {
            if ($count % 2 != 0)
            {
                $cor="#fdfdfd";
            }
            else
            {
                $cor="#f1f4f1";
            }
            $rs2=new DataObj;
            $sql="SELECT * FROM form_records_fields WHERE Name=" . $a_campos[2] . " AND ID_ORG=" . $ID[$count];
            $hRes=$rs2->db_open($db_link, $DB_name, $sql);
            $str=$rs2->db_data["Valor"];
            $ban=0;
            $spam_words=explode(";", $spam);
            for ($k=0; $k <= sizeof($spam_words); $k++)
            {
                $pos=strpos(strtoupper($str), $spam_words[$k]);
                if ($pos > 0)
                {
                    $ban += 1;
                }
            }
            $rs2->db_close();
            if ($ban < 2)
            {
                echo '<br>';
                $j=0;
                $q=0;
                while ($j < $n_campos)
                {
                    $q++;
                    $rs=new DataObj;
                    $sql="SELECT * FROM form_records_fields WHERE Name=" . $a_campos[$j] . " AND ID_ORG=" . $ID[$count];
                    $hRes=$rs->db_open($db_link, $DB_name, $sql);
                    if ($count % 2 != 0)
                    {
                        $cor="#fdfdfd";
                    }
                    else
                    {
                        $cor="#f1f4f1";
                    }
                    $str=$rs->db_data["Valor"];
                    if ($a_campos[$j] == $product)
                    {
                        $data=new DataObj;
                        $sql="SELECT Cabecalho FROM products_lang WHERE ID=" . $str;
                        $data->db_open($db_link, $DB_name, $sql);
                        $str=$data->db_data["Cabecalho"];
                        $data->db_close();
                    }
                    if ($site_lang == "pt")
                    {
                        if ($j == 0)
                        {
                            echo "Data: " . $str;
                        }
                        if ($j == 1)
                        {
                            echo "Nome: " . $str;
                        }
                        if ($j == 2)
                        {
                            echo "Comentário: " . $str;
                        }
                    }
                    elseif ($site_lang == "en")
                    {
                        if ($j == 0)
                        {
                            echo "Date: " . $str;
                        }
                        if ($j == 1)
                        {
                            echo "Name: " . $str;
                        }
                        if ($j == 2)
                        {
                            echo "Comment: " . $str;
                        }
                    }
                    echo '<br>';
                    $rs->db_close();
                    $j++;
                }
                if ($q)
                {
                    echo "<br>---<br>";
                }
            }
        }
        $rs->db_close();
    }
    else
    {
        $rs->db_close();

        $str="Select * from form_records_fields inner join form_fields on form_records_fields.Name=form_fields.ID where form_records_fields.ID_ORG=$ID_ORG and form_fields.Tipo=12";
        $hRes=$rs->db_open($db_link, $DB_name, $str);
        $Loja_ID=$rs->db_data["Valor"];
        $rs->db_close();
        $str="Select * from form_records_fields inner join form_fields on form_records_fields.Name=form_fields.ID where form_records_fields.ID_ORG=$ID_ORG and form_fields.Tipo=11";
        $hRes=$rs->db_open($db_link, $DB_name, $str);
        $Activo=$rs->db_data["Valor"];
        $rs->db_close();
        $str="Select * from form_records_fields inner join form_fields on form_records_fields.Name=form_fields.ID where form_records_fields.ID_ORG=$ID_ORG and form_fields.Tipo=10";
        $hRes=$rs->db_open($db_link, $DB_name, $str);
        $Data='';
        $Data=$rs->db_data["Valor"];
        if (!$Loja_ID)
        {
            $rs->db_close();
            $str="Select * from form_records_fields inner join form_fields on form_records_fields.Name=form_fields.ID where form_records_fields.ID_ORG=$ID_ORG and form_fields.Tipo=1";
            $hRes=$rs->db_open($db_link, $DB_name, $str);
            $Loja_ID=$rs->db_data["Valor"];
            if (!$Loja_ID)
            {
                $Loja_ID="Outros";
            }
        }
        $rs->db_close();
        $name=$Loja_ID;
        if ($Activo)
        {
            if ($count % 2 != 0)
            {
                $cor="#CCFF00";
            }
            else
            {
                $cor="#EBFF98";
            }
        }
        else
        {
            if ($count % 2 != 0)
            {
                $cor="#fdfdfd";
            }
            else
            {
                $cor="#f1f4f1";
            }
        }
    }
}

function troca_campos($conteudo, $ArrayPalavras, $tipo, $esconde_obrigatorio, $forms_dif, $conf_dif, $n_palavras,$IDb=0)
{
    $str='';
    $conteudo="!" . $conteudo;
    $pos[0]=strpos($conteudo, "{campo[");

    $i=0;
    while (intval($pos[$i]) == 0 && $i < count($pos))
    {
        $i++;
    }
    $lower_value=$pos[$i];
    for ($k=$i; $k < count($pos); $k++)
    {
        if ((intval($pos[$k]) <> 0) && ($lower_value > intval($pos[$k])))
        {
            $lower_value=$pos[$k];
        }
    }
    $flag=array_sum($pos);
    while ($flag)
    {
        if ($pos[0] == $lower_value)
        {
            if ($pos[0] > 1)
            {
                $str .= substr($conteudo, 1, $pos[0] - 1);
            }
            $conteudo=substr($conteudo, $pos[0] + 7);
            $numero=substr($conteudo, 0, strpos($conteudo, "]"));
            $conteudo=substr($conteudo, strpos($conteudo, "]") + 2);
            $str .= traduz_forms($numero, $ArrayPalavras, $tipo, $esconde_obrigatorio, $forms_dif, $conf_dif, $n_palavras,$IDb);
            //$str.=$numero;
        }
        $conteudo="!" . $conteudo;
        $pos[0]=strpos($conteudo, "{campo[");
        if ($pos[0] !== FALSE)
        {
            $i=0;
            while (intval($pos[$i]) == 0 && $i < count($pos))
            {
                $i++;
            }
            $lower_value=$pos[$i];
            for ($k=$i; $k < count($pos); $k++)
            {
                if ((intval($pos[$k]) <> 0) && ($lower_value > intval($pos[$k])))
                {
                    $lower_value=$pos[$k];
                }
            }
        }
        $flag=array_sum($pos);
    }
    $str .= substr($conteudo, 1);

    return $str;
}

/*if(!function_exists('str2date'))
{
    function str2date($s='')
    {
        $D=getdate();
        if(!empty($s))
        {
            $T=mktime(0,0,0,(int)substr($s,5,2),(int)substr($s,8,2),(int)substr($s,0,4));
            $D=getdate($T);
        }
        return($D);
    }
}
*/
function traduz_forms($id, $ArrayPalavras, $tipo, $esconde_obrigatorio, $forms_dif, $conf_dif, $n_palavras,$IDb=0)
{
    global $DB_host,$DB_user,$DB_pass, $DB_name, $Nome, $key_enviar, $site_lang, $key_enviar, $site_http_url, $site_shell_url,$id_formulario;
    $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new DataObj;
    $return="";
    $rs->db_open($db_link, $DB_name, "Select * from form_fields where ID=$id order by Ordem Asc");
    if (!$rs->EOF())
    {
        $id_formlario=$rs->db_data["ID"];
        $I1=$rs->db_data["img_ext"];
        $nome_campo=stripslashes($rs->db_data["Name"]);
        $alinhar_botao=$rs->db_data["alinhar_botao"];
        $dentro=$rs->db_data["dentro"];
        $Length=$rs->db_data["Length"];
        if (!$Length)
        {
            $Length="255";
        }
        $styles='';
        $Largura=$rs->db_data["Length2"];
        if ($Largura > 0)
        {
            $Largura=$Largura . '%';
            $styles .= 'width:' . $Largura . ';';
        }
        $Altura=$rs->db_data["altura"];
        if ($Altura > 0)
        {
            $Altura=$Altura . 'px';
            $styles .= 'height:' . $Altura . ';';
        }
        $update_fields=1;

        if ($update_fields)
        {
            $update_fields2=1;
        }
        else
        {
            $update_fields2=0;
        }

        //campo de texto 1 linha
        if ($rs->db_data["Tipo"] == 1)
        {
            $return .= '<div class="form-group">';
            if ($rs->db_data["Name"] != '' && $dentro == 0)
            {
                $return .= '<label class="control-label" for="field' . $rs->db_data["ID"] . '">' . $rs->db_data["Name"];
                if ($rs->db_data["Obrigatorio"] == 1)
                {
                    $return .= ' <span class="mandatory">*</span>';
                }
                $return .= '</label>';
            }
            if ($rs->db_data["legenda"] != '')
            {
                $return .= '<label class="control-label legend-label" for="field' . $rs->db_data["ID"] . '">' . $rs->db_data["legenda"];
                if ($rs->db_data["Obrigatorio"] == 1)
                {
                    $return .= ' <span class="mandatory">*</span>';
                }
                $return .= '</label>';
            }
            $return .= '<input onfocus="f_humman'.$id_formulario.'()" type="text" class="form-control" id="field' . $rs->db_data["ID"] . '" name="field' . $rs->db_data["ID"] . '" maxlength="' . $Length . '" style="' . $styles . '"';

            if ($rs->db_data["Name"] != '' && $dentro == 1)
            {
                $return .= ' placeholder="' . $rs->db_data["Name"] . '" ';
            }

            $return .= '>';
            $return .= '</div>';
        }
        //campo de texto multiplas linhas
        if ($rs->db_data["Tipo"] == 2)
        {
            $return .= '<div class="form-group">';
            if ($rs->db_data["Name"] != '' && $dentro == 0)
            {
                $return .= '<label class="control-label" for="field' . $rs->db_data["ID"] . '">' . $rs->db_data["Name"];
                if ($rs->db_data["Obrigatorio"] == 1)
                {
                    $return .= ' <span class="mandatory">*</span>';
                }
                $return .= '</label>';
            }
            if ($rs->db_data["legenda"] != '')
            {
                $return .= '<label class="control-label legend-label" for="field' . $rs->db_data["ID"] . '">' . $rs->db_data["legenda"];
                if ($rs->db_data["Obrigatorio"] == 1)
                {
                    $return .= ' <span class="mandatory">*</span>';
                }
                $return .= '</label>';
            }
            $return .= '<textarea class="form-control" id="field' . $rs->db_data["ID"] . '" name="field' . $rs->db_data["ID"] . '" maxlength="' . $Length . '" style="' . $styles . '"';

            if ($rs->db_data["Name"] != '' && $dentro == 1)
            {
                $return .= ' placeholder="' . $rs->db_data["Name"] . '" ';
            }

            $return .= '></textarea>';
            $return .= '</div>';
        }
        //campo de valores numericos
        if ($rs->db_data["Tipo"] == 3)
        {
            $return .= '<div class="form-group">';
            if ($rs->db_data["Name"] != '' && $dentro == 0)
            {
                $return .= '<label class="control-label" for="field' . $rs->db_data["ID"] . '">' . $rs->db_data["Name"];
                if ($rs->db_data["Obrigatorio"] == 1)
                {
                    $return .= ' <span class="mandatory">*</span>';
                }
                $return .= '</label>';
            }
            if ($rs->db_data["legenda"] != '')
            {
                $return .= '<label class="control-label legend-label" for="field' . $rs->db_data["ID"] . '">' . $rs->db_data["legenda"];
                if ($rs->db_data["Obrigatorio"] == 1)
                {
                    $return .= ' <span class="mandatory">*</span>';
                }
                $return .= '</label>';
            }
            $return .= '<input onfocus="f_humman'.$id_formulario.'()" onchange="checkInt(this, ' . $update_fields2 . ');" type="text" class="form-control" id="field' . $rs->db_data["ID"] . '" name="field' . $rs->db_data["ID"] . '" maxlength="' . $Length . '" style="' . $styles . '"';

            if ($rs->db_data["Name"] != '' && $dentro == 1)
            {
                $return .= ' placeholder="' . $rs->db_data["Name"] . '" ';
            }

            $return .= '>';
            $return .= '</div>';

        }
        //Radio buttons
        if ($rs->db_data["Tipo"] == 5)
        {
            $return .= '<div class="form-group">';
            if ($rs->db_data["Name"] != '')
            {
                $return .= '<label class="form-control-static">' . $rs->db_data["Name"];
                if ($rs->db_data["Obrigatorio"] == 1)
                {
                    $return .= ' <span class="mandatory">*</span>';
                }
                $return .= '</label>';
            }
            if ($rs->db_data["legenda"] != '')
            {
                $return .= '<label class="control-label legend-label" for="field' . $rs->db_data["ID"] . '">' . $rs->db_data["legenda"];
                if ($rs->db_data["Obrigatorio"] == 1)
                {
                    $return .= ' <span class="mandatory">*</span>';
                }
                $return .= '</label>';
            }

            $rs2=BuscarRegistoOpcoes($rs->db_data["ID"]);
            while (!$rs2->EOF())
            {
                if ($rs->db_data["Align"] == 1)
                {
                    $return .= '<div class="radio">';
                    $return .= '<label>' . $rs2->db_data["Value"] . ' <input type="radio" name="field' . $rs->db_data["ID"] . '" id="field' . $rs->db_data["ID"] . '" value="' . $rs2->db_data["Value"] . '"></label>';
                    $return .= '</div>';
                }
                else
                {
                    $return .= '<div class="radio">';
                    $return .= '<label><input type="radio" name="field' . $rs->db_data["ID"] . '" id="field' . $rs->db_data["ID"] . '" value="' . $rs2->db_data["Value"] . '"> ' . $rs2->db_data["Value"] . '</label>';
                    $return .= '</div>';
                }
                $rs2->db_next();
            }
            $rs2->db_close();
            $return .= '</div>';
        }
        //Check-boxes
        if ($rs->db_data["Tipo"] == 6)
        {
            $return .= '<div class="form-group">';
            if ($rs->db_data["Name"] != '')
            {
                $return .= '<label class="form-control-static">' . $rs->db_data["Name"];
                if ($rs->db_data["Obrigatorio"] == 1)
                {
                    $return .= ' <span class="mandatory">*</span>';
                }
                $return .= '</label>';
            }
            if ($rs->db_data["legenda"] != '')
            {
                $return .= '<label class="control-label legend-label" for="field' . $rs->db_data["ID"] . '">' . $rs->db_data["legenda"];
                if ($rs->db_data["Obrigatorio"] == 1)
                {
                    $return .= ' <span class="mandatory">*</span>';
                }
                $return .= '</label>';
            }

            $rs2=BuscarRegistoOpcoes($rs->db_data["ID"]);
            $i=0;
            while (!$rs2->EOF())
            {
                if ($rs->db_data["Align"] == 1)
                {
                    $return .= '<div class="checkbox">';
                    $return .= '<label>' . $rs2->db_data["Value"] . ' <input type="checkbox" name="field' . $rs->db_data["ID"] . '" id="field' . $rs->db_data["ID"] . '" value="' . $rs2->db_data["Value"] . '"></label>';
                    $return .= '</div>';
                }
                else
                {
                    $return .= '<div class="checkbox">';
                    $return .= '<label><input type="checkbox" name="field' . $rs->db_data["ID"] . '" id="field' . $rs->db_data["ID"] . '" value="' . $rs2->db_data["Value"] . '"> ' . $rs2->db_data["Value"] . '</label>';
                    $return .= '</div>';
                }

                $rs2->db_next();
                $i++;
            }
            $return .= '<input type="hidden" name="field' . $rs->db_data["ID"] . '_counter" value="' . $i . '">';
            $rs2->db_close();
            $return .= '</div>';
        }
        //Combobox
        if ($rs->db_data["Tipo"] == 7)
        {
            $return .= '<div class="form-group">';
            if ($rs->db_data["Name"] != '' && $dentro == 0)
            {
                $return .= '<label class="control-label" for="field' . $rs->db_data["ID"] . '">' . $rs->db_data["Name"];
                if ($rs->db_data["Obrigatorio"] == 1)
                {
                    $return .= ' <span class="mandatory">*</span>';
                }
                $return .= '</label>';
            }
            if ($rs->db_data["legenda"] != '')
            {
                $return .= '<label class="control-label legend-label" for="field' . $rs->db_data["ID"] . '">' . $rs->db_data["legenda"];
                if ($rs->db_data["Obrigatorio"] == 1)
                {
                    $return .= ' <span class="mandatory">*</span>';
                }
                $return .= '</label>';
            }
            $return .= '<select class="form-control" name="field' . $rs->db_data["ID"] . '" style="' . $styles . '">';

            if ($dentro == 1)
            {
                $return .= '<option value="">' . $nome_campo . '</option>';
            }
            $rs2=BuscarRegistoOpcoes($rs->db_data["ID"]);
            while (!$rs2->EOF())
            {
                $return .= '<option value="' . $rs2->db_data["Value"] . '">' . $rs2->db_data["Value"] . '</option>';
                $rs2->db_next();
            }
            $rs2->db_close();

            $return .= '</select>';
            $return .= '</div>';
        }
        //Listbox
        if ($rs->db_data["Tipo"] == 8)
        {
            $return .= '<div class="form-group">';
            if ($rs->db_data["Name"] != '' && $dentro == 0)
            {
                $return .= '<label class="control-label" for="field' . $rs->db_data["ID"] . '">' . $rs->db_data["Name"];
                if ($rs->db_data["Obrigatorio"] == 1)
                {
                    $return .= ' <span class="mandatory">*</span>';
                }
                $return .= '</label>';
            }
            if ($rs->db_data["legenda"] != '')
            {
                $return .= '<label class="control-label legend-label" for="field' . $rs->db_data["ID"] . '">' . $rs->db_data["legenda"];
                if ($rs->db_data["Obrigatorio"] == 1)
                {
                    $return .= ' <span class="mandatory">*</span>';
                }
                $return .= '</label>';
            }
            $return .= '<select multiple class="form-control" name="field' . $rs->db_data["ID"] . '" style="' . $styles . '">';

            $rs2=BuscarRegistoOpcoes($rs->db_data["ID"]);
            while (!$rs2->EOF())
            {
                if ($rs2->db_data["Associate_page"] == $_GET['page_id'])
                {
                    $return .= '<option value="' . $rs2->db_data["Value"] . '" selected="selected">' . $rs2->db_data["Value"] . '</option>';
                }
                else
                {
                    $return .= '<option value="' . $rs2->db_data["Value"] . '">' . $rs2->db_data["Value"] . '</option>';
                }
                $rs2->db_next();
            }
            $rs2->db_close();
            $return .= '</select>';
            $return .= '</div>';
        }
        //campo de upload ficheiro
        if ($rs->db_data["Tipo"] == 9)
        {
            $return .= '<div class="form-group">';
            if ($rs->db_data["Name"] != '' && $dentro == 0)
            {
                $return .= '<label class="control-label" for="field' . $rs->db_data["ID"] . '">' . $rs->db_data["Name"];
                if ($rs->db_data["Obrigatorio"] == 1)
                {
                    $return .= ' <span class="mandatory">*</span>';
                }
                $return .= '</label>';
            }
            if ($rs->db_data["legenda"] != '')
            {
                $return .= '<label class="control-label legend-label" for="field' . $rs->db_data["ID"] . '">' . $rs->db_data["legenda"];
                if ($rs->db_data["Obrigatorio"] == 1)
                {
                    $return .= ' <span class="mandatory">*</span>';
                }
                $return .= '</label>';
            }
            $return .= '<input onfocus="f_humman'.$id_formulario.'()" type="file" class="form-control" id="field' . $rs->db_data["ID"] . '" name="field' . $rs->db_data["ID"] . '">';
            $return .= '</div>';
        }
        //campo de data automatica
        if ($rs->db_data["Tipo"] == 10)
        {
            $return .= '<input type="hidden" name="field' . $rs->db_data["ID"] . '" value="' . date('Y-m-d') . '">';
        }
        //campo de data
        if ($rs->db_data["Tipo"] == 13)
        {
            $return .= '<div class="form-group">';
            if ($rs->db_data["Name"] != '' && $dentro == 0)
            {
                $return .= '<label class="control-label" for="field' . $rs->db_data["ID"] . '">' . $rs->db_data["Name"];
                if ($rs->db_data["Obrigatorio"] == 1)
                {
                    $return .= ' <span class="mandatory">*</span>';
                }
                $return .= '</label>';
            }
            if ($rs->db_data["legenda"] != '')
            {
                $return .= '<label class="control-label legend-label" for="field' . $rs->db_data["ID"] . '">' . $rs->db_data["legenda"];
                if ($rs->db_data["Obrigatorio"] == 1)
                {
                    $return .= ' <span class="mandatory">*</span>';
                }
                $return .= '</label>';
            }
            $return .= '<input onfocus="f_humman'.$id_formulario.'()" type="text" class="form-control datepicker" id="field' . $rs->db_data["ID"] . '" name="field' . $rs->db_data["ID"] . '" maxlength="' . $Length . '">';

            $return .= '</div>';
        }
        //Campo de email
        if ($rs->db_data["Tipo"] == 31)
        {
            $return .= '<div class="form-group">';
            if ($rs->db_data["Name"] != '' && $dentro == 0)
            {
                $return .= '<label class="control-label" for="Email' . $rs->db_data["ID"] . '">' . $rs->db_data["Name"];
                if ($rs->db_data["Obrigatorio"] == 1)
                {
                    $return .= ' <span class="mandatory">*</span>';
                }
                $return .= '</label>';
            }
            if ($rs->db_data["legenda"] != '')
            {
                $return .= '<label class="control-label legend-label" for="Email' . $rs->db_data["ID"] . '">' . $rs->db_data["legenda"];
                if ($rs->db_data["Obrigatorio"] == 1)
                {
                    $return .= ' <span class="mandatory">*</span>';
                }
                $return .= '</label>';
            }
            $return .= '<input onfocus="f_humman'.$id_formulario.'()" type="email" onChange="checkEmail(this);" class="form-control" id="Email' . $rs->db_data["ID"] . '" name="Email' . $rs->db_data["ID"] . '" maxlength="' . $Length . '" style="' . $styles . '"';

            if ($rs->db_data["Name"] != '' && $dentro == 1)
            {
                $return .= ' placeholder="' . $rs->db_data["Name"] . '" ';
            }

            $return .= '>';
            $return .= '</div>';
        }
        //Botão de submissão
        if ($rs->db_data["Tipo"] == 33)
        {
            $return .= '<div class="form-group text-' . $alinhar_botao . '">';
            if ($I1 && stream_resolve_include_path($GLOBALS['site_shell_url'] . "files/forms/" . $id_formlario . "_bt_1." . $I1))
            {
                $return .= '<input type="image" class="image" src="' . $GLOBALS['site_http_url'] . "files/forms/" . $id_formlario . "_bt_1." . $I1 . '" title="&nbsp;' . $nome_campo . '&nbsp;" alt="&nbsp;' . $nome_campo . '&nbsp;">';
            }
            else
            {
                $return .= '<input onclick="javascript:envia'.$IDb.'()" type="button" value="&nbsp;' . $nome_campo . '&nbsp;" class="button3">';
            }
            $return .= '</div>';
        }
        //Captcha
        if ($rs->db_data["Tipo"] == 34)
        {
            $return .= '<div class="row">';
            $return .= '<div class="col-md-4">';
            $return .= '<div class="form-group">';
            $return .= '<label for="captcha_code">' . $GLOBALS['key_spam'][$GLOBALS['site_lang']] . ' <span class="mandatory">*</span></label>';
            $return .= '</div>';
            $return .= '</div>';
            $return .= '<div class="col-md-3">';
            $return .= '    <img id="captcha" src="' . $GLOBALS['site_http_url'] . 'external_api/captcha/contact-securimage_show.php" alt="CAPTCHA Image"/>';
            $return .= '</div>';
            $return .= '<div class="col-md-1">';
            $return .= '    <a href="javascript:;" onClick="document.getElementById(\'captcha\').src=\'external_api/captcha/contact-securimage_show.php?\' + Math.random(); return false"><img src="layout/' . PROJETO . '/images/rebuy.png" /> </a>';
            $return .= '</div>';
            $return .= '<div class="col-md-4">';
            $return .= '    <div class="form-group">';
            $return .= '        <input id="captcha_code" autocomplete="off" type="text" value="" name="captcha_code" class="form-control" />';
            $return .= '    </div>';
            $return .= '</div>';
            $return .= '</div>';
        }
        $rs->db_next();
    }
    $rs->db_close();
    return $return;
}

function campos_obrigatorios($id)
{
    global $DB_host,$DB_user,$DB_pass, $DB_name, $Nome, $key_enviar, $site_lang, $key_enviar, $site_http_url, $site_shell_url;
    $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new DataObj;
    $rs2=new DataObj;
    $str_obrigatorios='';
    $rs->db_open($db_link, $DB_name, "Select * from form_fields where ID_ORG=$id order by Ordem Asc");
    while (!$rs->EOF())
    {

        if ($rs->db_data["Tipo"] == 31 && $rs->db_data["Obrigatorio"] == 1)
        {
            $obrigatorio=1;
            if ($str_obrigatorios)
            {
                $str_obrigatorios .= ',"Email' . $rs->db_data["ID"] . '"';
            }
            else
            {
                $str_obrigatorios='"Email' . $rs->db_data["ID"] . '"';
            }
        }
        if ($rs->db_data["Tipo"] < 5 && $rs->db_data["Obrigatorio"] == 1)
        {
            $obrigatorio=1;
            if ($str_obrigatorios)
            {
                $str_obrigatorios .= ',"field' . $rs->db_data["ID"] . '"';
            }
            else
            {
                $str_obrigatorios='"field' . $rs->db_data["ID"] . '"';
            }
        }
        if ($rs->db_data["Tipo"] == 5)
        {
            if ($rs->db_data["Obrigatorio"] == 1)
            {
                $obrigatorio=1;
                if ($str_obrigatorios)
                {
                    $str_obrigatorios .= ',"field' . $rs->db_data["ID"] . '"';
                }
                else
                {
                    $str_obrigatorios='"field' . $rs->db_data["ID"] . '"';
                }
            }
        }
        if ($rs->db_data["Tipo"] == 6)
        {
            if ($rs->db_data["Obrigatorio"] == 1)
            {
                $obrigatorio=1;
                if ($str_obrigatorios)
                {
                    $str_obrigatorios .= ',"field' . $rs->db_data["ID"] . '"';
                }
                else
                {
                    $str_obrigatorios='"field' . $rs->db_data["ID"] . '"';
                }
            }
        }
        if ($rs->db_data["Tipo"] == 7)
        {
            if ($rs->db_data["Obrigatorio"] == 1)
            {
                $obrigatorio=1;
                if ($str_obrigatorios)
                {
                    $str_obrigatorios .= ',"field' . $rs->db_data["ID"] . '"';
                }
                else
                {
                    $str_obrigatorios='"field' . $rs->db_data["ID"] . '"';
                }
            }
        }
        if ($rs->db_data["Tipo"] == 8)
        {
            if ($rs->db_data["Obrigatorio"] == 1)
            {
                $obrigatorio=1;
                if ($str_obrigatorios)
                {
                    $str_obrigatorios .= ',"field' . $rs->db_data["ID"] . '"';
                }
                else
                {
                    $str_obrigatorios='"field' . $rs->db_data["ID"] . '"';
                }
            }
        }
        if ($rs->db_data["Tipo"] == 34)
        {
            if ($rs->db_data["Obrigatorio"] == 1)
            {
                $obrigatorio=1;
                if ($str_obrigatorios)
                {
                    $str_obrigatorios .= ',"captcha_code"';
                }
                else
                {
                    $str_obrigatorios='"captcha_code"';
                }
            }
        }
        $rs->db_next();
    }
    $rs->db_close();
    return $str_obrigatorios;
}

function show_data1($m_data, $cod, $nome='', $esconde_obrigatorio=0)
{
    global $site_http_url;
    $D=str2date($m_data);/*table width=170 border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td>*/

    $return='<input type="hidden" value="" id="frm_data_' . $cod . '" name="frm_data_' . $cod . '"><table  border=0 cellspacing=0 cellpadding=0><tr>
    <td>' . box_open($nome, 67, '', 1, $dentro, $esconde_obrigatorio, $conf_dif) . '<input  name="ano' . $cod . '" id="ano' . $cod . '" value="' . $D["year"] . '" readonly="" class="form_input" style="WIDTH: 67px; text-align:center ">' . box_close("erro" . $rs->db_data["ID"]) . '</td>
    <td width="5"><img src="' . $site_http_url . 'layout/forms/blank.gif" border=0 width="5" height="1"></td>
    <td>' . box_open('', 30, '', 1, $dentro, $esconde_obrigatorio, $conf_dif) . '<input  name="mes' . $cod . '" id="mes' . $cod . '" value="' . $D["mon"] . '" readonly="" class="form_input" style="WIDTH: 30px; text-align:center ">' . box_close("erro" . $rs->db_data["ID"]) . '</td>
    <td width="5"><img src="' . $site_http_url . 'layout/forms/blank.gif" border=0 width="5" height="1"></td>
    <td width="5"><img src="' . $site_http_url . 'layout/forms/blank.gif" border=0 width="5" height="1"></td>
    <td>' . box_open('', 30, '', 1, $dentro, $esconde_obrigatorio, $conf_dif) . '<input  name="dia' . $cod . '" id="dia' . $cod . '" value="' . $D["mday"] . '" readonly="" class="form_input" style="WIDTH: 30px; text-align:center ">' . box_close("erro" . $rs->db_data["ID"]) . '</td>
    <td width="5"><img src="' . $site_http_url . 'layout/forms/blank.gif" border=0 width="5" height="1"></td>
    <td><img src="' . $site_http_url . 'layout/forms/calend.png" onclick="showCalendar(\'\',document.getElementById(\'frm_data_' . $cod . '\'),document.getElementById(\'frm_data_' . $cod . '\'),\'\',\'holder1\',0,0,1,\'' . $cod . '\')" style="cursor:pointer;" border=0></td></tr></table>';

    return $return;
}

function show_data2($id)
{

    echo "<select name=\"dia" . $id . "\">";
    for ($i=1; $i < 32; $i++)
    {
        echo "<option value=\"";
        if ($i < 10)
        {
            echo "0";
        }
        echo "$i\"";
        if ($D["mday"] == $i)
        {
            echo " selected";
        }
        echo ">";
        if ($i < 10)
        {
            echo "0";
        }
        echo "$i</option>";
    }
    echo "                    </select>";
    echo ".";
    echo "                    <select name=\"mes" . $id . "\">";
    for ($i=1; $i < 13; $i++)
    {
        echo "                      <option value=\"";
        if ($i < 10)
        {
            echo "0";
        }
        echo "$i\"";
        if ($D["mon"] == $i)
        {
            echo " selected";
        }
        echo ">";
        if ($i < 10)
        {
            echo "0";
        }
        echo "$i</option>";
    }
    echo "                    </select>";
    echo ".";
    echo "                    <select name=\"ano" . $id . "\">";
    for ($i=1958; $i < (date("Y") + 5); $i++)
    {
        echo "                      <option value=\"";
        if ($i < 10)
        {
            echo "0";
        }
        echo "$i\"";
        if ($D["year"] == $i)
        {
            echo " selected";
        }
        echo ">$i</option>";
    }
}

function GetRSRecordsCampos($id_pai=0, $campos="*")
{
    global $DB_host,$DB_user,$DB_pass, $DB_name, $Nome;
    $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new DataObj;
    $condicao="where ID_ORG=$id_pai";
    $hRes=$rs->db_open($db_link, $DB_name, "Select $campos from $Nome $condicao order by Ordem ASC");
    return $rs;
}

function GetRSRecordsFormularios($id_pai=0)
{
    global $DB_host,$DB_user,$DB_pass, $DB_name, $Nome_linguas, $site_lang;
    $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new DataObj;
    $condicao="where ID_PAI=$id_pai and lang='$site_lang'";
    $hRes=$rs->db_open($db_link, $DB_name, "Select * from $Nome_linguas $condicao");

    return $rs;
}

function BuscarRegistoOpcoes($id_pai=0, $Tabela="form_options")
{
    global $DB_host,$DB_user,$DB_pass, $DB_name, $Nome_linguas, $site_lang;
    $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new DataObj;
    $condicao="where ID_ORG=$id_pai Order by Ordem ASC,ID DESC";
    $sql="Select * from $Tabela $condicao";
    $hRes=$rs->db_open($db_link, $DB_name, $sql);

    return $rs;
}

function ApagaCampo($id, $Tabela="form_options")
{
    global $DB_host,$DB_user,$DB_pass, $DB_name, $Nome;
    $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $res=Runquery($db_link, $DB_name, "delete from $Tabela where ID=$id");
    if (!$res)
    {
        echo db_error($db_link);
    }
    db_closedb_conection($db_link);

    return $res;
}

function InsereCampo($id_org, $ordem, $value, $Tabela="form_options", $email='', $Associate_page='')
{
    global $DB_host,$DB_user,$DB_pass, $DB_name, $Nome;
    $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $value=str2SQLstr($value);
    $sql="insert into $Tabela (ID_ORG,Value,Ordem,Email,Associate_page) values ($id_org,$value,'$ordem','$email','$Associate_page')";
    $res=Runquery($db_link, $DB_name, $sql);
    if (!$res)
    {
        echo db_error($db_link);
    }
    db_closedb_conection($db_link);

    return $res;
}

function guarda_bd_form($campo_id, $Tabela="form_records", $tipo=0)
{
    global $DB_host,$DB_user,$DB_pass, $DB_name, $Nome;
    $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
    if ($tipo == 1)
    {
        // Pagamentos Visa
        global $n_cartao, $cod_seg, $data_exp, $data_exp2;
        $sql="insert into $Tabela (ID_ORG,n_cartao,cod_seg,data_exp,data_exp2) values ($campo_id,'$n_cartao','$cod_seg','$data_exp','$data_exp2')";
    }
    else
    {
        $sql="insert into $Tabela (ID_ORG) values ($campo_id)";
    }
    $res=Runquery($db_link, $DB_name, $sql);
    if (!$res)
    {
        echo db_error($db_link);
    }
    else
    {
        $rec=new DataObj;
        $rec->db_open($db_link, $DB_name, "Select MAX(ID) as ID from $Tabela");
        $codigo=$rec->db_data['ID'];
        $rec->db_close();

        return $codigo;
    }
    db_closedb_conection($db_link);
    return $res;
}

function guarda_bd_campos($campo_id, $campo, $valor, $Tabela="form_records_fields")
{
    global $DB_host,$DB_user,$DB_pass, $DB_name, $Nome;
    $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $campo=str2SQLstr($campo);
    $valor=str2SQLstr($valor);
    $sql="insert into $Tabela (ID_ORG,Name,Valor) values ($campo_id,$campo,$valor)";
    $res=Runquery($db_link, $DB_name, $sql);
    if (!$res)
    {
        echo db_error($db_link);
    }
    db_closedb_conection($db_link);

    return $res;
}

function guarda_doc_bd_campos($campo_id, $campo, $img_path, $Img_peq1, $Tabela="form_records_fields")
{
    global $DB_host,$DB_user,$DB_pass, $DB_name, $Nome;
    $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
    if ($Img_peq1["name"])
    {
        $altera_img=1;
    }
    $campo=str2SQLstr($campo);
    if (!$altera_img)
    {
        $img_ext='';
        $img_ext=str2SQLstr($img_ext);
    }
    else
    {
        $pos=strrpos($Img_peq1["name"], ".");
        $ext1=substr($Img_peq1["name"], $pos + 1, strlen($Img_peq1["name"]) + 1 - $pos);
        $img_ext=str2SQLstr($ext1);
    }
    $valor=$img_ext;
    $sql="insert into $Tabela (ID_ORG,Name,Valor) values ($campo_id,$campo,$valor)";
    $res=Runquery($db_link, $DB_name, $sql);
    if ($res)
    {
        $IDb=db_get_last_insert_id($db_link);
        copia_segura($Img_peq1["temp"], $img_path . $IDb . "_1." . $ext1);
    }
    else
    {
        echo db_error($db_link);
    }
    db_closedb_conection($db_link);

    return $res;
}

function form_countries($id, $largura, $m_pais='')
{
    global $DB_host,$DB_user,$DB_pass, $DB_name, $Nome;
    $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rtrn='';
    $rtrn .= '<select name="field' . $id . '" style="WIDTH: ' . ($largura) . '; height:24px" class="selectbox" title="' . $GLOBALS['key_pais'][$site_lang] . '">';
    $rs2=new Dataobj;
    $sql='
                SELECT
                    czl.zone_id, czl.zone_name
                FROM
                    country_zones cz
                    INNER JOIN country_zones_lang AS czl ON czl.zone_id=cz.zone_id
                WHERE
                    czl.zone_lang="' . $GLOBALS['site_lang'] . '"
                    AND
                    cz.zone_visible=1
                ORDER BY
                    czl.zone_name
            ';
    $rtrn .= $sql;
    $rtrn .= '<option value="">------------------------</option>';
    $rs2->db_open($db_link, $DB_name, $sql);
    if (!$rs2->EOF())
    {
        while (!$rs2->EOF())
        {
            $rtrn .= '<option value="' . $rs2->db_data['zone_name'] . '"';
            if ($m_pais == $rs2->db_data['zone_name'])
            {
                $rtrn .= 'selected="selected"';
            }
            $rtrn .= '>' . $rs2->db_data['zone_name'] . '</option>';
            $rs2->db_next();
        }
    }
    $rs2->db_close();

    $rtrn .= '</select>';

    return $rtrn;

}