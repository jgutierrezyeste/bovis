<?php
    include ("../../../inc/includes.php");
    GLOBAL $DB,$CFG_GLPI;
    $objCommonDBT=new CommonDBTM;

    $sql = "SELECT ID, NAME FROM glpi_plugin_comproveedores_ambitos WHERE is_deleted=0 ORDER BY NAME ASC";
    $result = $DB->query($sql);
    $output = "<ul>";
    $output = "<li id='lineaAmbito_0' class='lineaAmbito'><input id='checkAmbito_0' type='checkbox' value='0' checked class='ambitos_check'><label id='etiquetaAmbito_0' class='etiquetaAmbito_check' style='font-weight:bold;'> SELECCIONAR TODOS </label></li>";
    while ($data = $DB->fetch_array($result)) {
        $output .= "<li id='lineaAmbito_{$data['ID']}' class='lineaAmbito'><input id='checkAmbito_{$data['ID']}' value='{$data['ID']}' class='ambitos_check' type='checkbox' checked ><label id='etiquetaAmbito_{$data['ID']}' class='etiquetaAmbito_check'> {$data['NAME']}</label></li>";
    }
    $output.= "</ul>";

    $output .= "
        <script type='text/javascript'>


        $('#checkAmbito_0').on('click', function() {
            if($(this).prop('checked') == true){
                $('.ambitos_check').prop('checked',true);
            }else{
                $('.ambitos_check').prop('checked',false);
            }
        });

        $('.etiquetaAmbito_check').on('click', function(){                       
            var id = $(this).attr('id').replace('etiquetaAmbito_', '');
            var str= '#checkAmbito_'+id;
            var val = $(str).prop('checked');

            if(id==0){
                if(val==true){
                    $('.ambitos_check').prop('checked',true);
                }else{
                    $('.ambitos_check').prop('checked',false);
                }
            }else{
                if(val==true){
                    $(str).prop('checked',false);
                }else{
                    $(str).prop('checked',true);
                }                          
            }
        });


    </script>";
    echo $output;                   


