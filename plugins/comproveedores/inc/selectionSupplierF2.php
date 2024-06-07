<?php

use Glpi\Event;

include ("../../../inc/includes.php");

GLOBAL $DB,$CFG_GLPI;

$objCommonDBT=new CommonDBTM();
$preselecionIds='';

$query = "";
if(isset($_GET['Proveedores'])){
    $strProveedores = $_GET['Proveedores'];
}else{
    $strProveedores = '';
}
$intPaqueteId = 0;
if(isset($_GET['paquete_id'])){
    $intPaqueteId = $_GET['paquete_id'];
}

$javascripts = "<script type='text/javascript'>
    
    var arrayProveedoresElegidos= new Array();

    function guardarPreseleccion( paquete_id, preselecionIds){

        var parametros = {
                'preseleccion_guardar': 'preseleccion_guardar',
                'arrayPreselecion':preselecionIds,
                'paquete_id' : paquete_id
        };

        $.ajax({  
                type: 'GET',        		
                url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/front/selectionsupplier.form.php',
                data: parametros,   		
                success:function(data){ 
                   alert(data);
                },
                error: function(result) {
                    alert('Data not found');
                }
        });
    }

    function atras(paquete_id){
        var parametros = {
            'paquete_id': paquete_id
        };

        $.ajax({  
                type: 'GET',        		
                url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/selectionSupplierF1.php',
                data: parametros,   		
                success:function(data){ 
                   $('#selector_proveedor').html(data);
                },
                error: function(result) {
                    alert('Data not found');
                }
        });					
    }

    function siguiente(paquete_id){	
        var parametros = {
            'paquete_id': paquete_id
        };

        $.ajax({  
            type: 'GET',        		
            url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/selectionSupplierF3.php',
            data: parametros,   		
            success:function(data){ 
               $('#selector_proveedor').html(data);
            },
            error: function(result) {
                alert('Data not found');
            }
        });					
    }

    function setListaProveedorfiltro(supplier_id){
        if($('#proveedor_'+supplier_id).prop('checked')){
           arrayProveedoresElegidos[supplier_id]=supplier_id;
        }
        else{
            delete  arrayProveedoresElegidos[supplier_id];
        }
    }

    function incluirProveedoresAlPaquete(paquete_id){
        var numProveedores=0;
        for(var i=0;i<=arrayProveedoresElegidos.length;i++){
            if(arrayProveedoresElegidos[i]!=null){
                    numProveedores++;
            }
        }
        if(numProveedores==1){
            var parametros = {
                    'add_proveedor_al_paquete': 'add_proveedor_al_paquete',
                    'paquete_id':paquete_id,
                    'arrayProveedoresElegidos' : arrayProveedoresElegidos,   
            };
            $.ajax({  
                type: 'GET',        		
                url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/front/selectionsupplier.form.php',
                data: parametros,   		
                success:function(data){
                    location.reload();
                },
                error: function(result) {
                    alert('Data not found');
                }
            });
        }
    }


    $('#botonAnterior').on('click', function () {
        if({$intPaqueteId}>0){
            atras({$intPaqueteId});
        }else{
            var parametros = {
                    'idtipo': {$_GET['idtipo']},
                    'idcategoria': {$_GET['idcategoria']},
                    'idespecialidad' : '{$_GET['idespecialidad']}',   
                    'minima' : {$_GET['minima']},   
                    'maxima' : {$_GET['maxima']},   
                    'ambitos' : '{$_GET['ambitos']}',   
                    'Proveedores' : '{$_GET['Proveedores']}'
            };
            $.ajax({ 
                async: false, 
                type: 'GET',                
                data: parametros,   
                url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/selectionSupplierF1.php',                    
                success:function(data){
                    $('#page').html(data);
                },
                error: function(result) {
                    alert('Data not found');
                }
            });
        }
    });

    $('#botonSiguiente').on('click', function () {
        siguiente({$intPaqueteId});					
    });			


    $('#botonFiltro').on('click', function () {
        
        //EXPERIENCIA CON BOVIS (INTERVENCIÓN EN PROYECTOS DE BOVIS)
        var intervencionBOVIS = 0;
        if( $('#intervencionBOVIS').is(':checked') ){
            intervencionBOVIS = 1;
        }               
        //CHECKBOXES DE LAS EXPERIENCIAS
        var strExperiencias = '';
        $('.especialidades_check:checked').each(function() {
            if(strExperiencias!=''){
                strExperiencias = strExperiencias+','+$(this).val();
            }else{
                strExperiencias = $(this).val();
            }
        }); 
        
        //IMPORTES
        var min = 0;
        var str1 = '';
        var max = 999999999;
        var impMin = $('#hiddenminima').val();
        if (impMin!='' && impMin.length>0) {
            str1 = impMin.replace('.', '').replace(',', '.');
            min = str1;
        }		
        //TIPO
        var idtipo = 0;
        if($('#hiddenidtipo').length){
            idtipo = $('#hiddenidtipo').val();
        }
        //CATEGORÍA
        var idcategoria = 0;
        if($('#idcategoria').length){
            idcategoria = $('#idcategoria').val();
        }
        //ESPECIALIDAD
        var idespecialidad = 0;
        if($('#hiddenidespecialidad').length){
            idespecialidad = $('#hiddenidespecialidad').val();
        }         
        //REGIÓN
        var idregion = 0;
        if($('#hiddenidregion').length){
            idregion = $('#hiddenidregion').val();
        }            
        //PROVINCIA
        var idprovincia = 0;
        if($('#idprovincia').length){
            idprovincia = $('#idprovincia').val();
        }   
        //PAQUETE
        var paquete_id = 0;
        if($('#hiddenpaquete_id').length){
            paquete_id = $('#hiddenpaquete_id').val();
        }            
        //PRESELECIÓN
        var preseleccion = '';
        if($('#hiddenpreseleccion').length){
            preseleccion = $('#hiddenpreseleccion').val();
        }            
        //ÁMBITOS
        var ambitos = '';
        if($('#hiddenambito').length){
            ambitos = $('#hiddenambito').val();
        }        
        //PROVEEDORES SELECCIONADOS
        var proveedores = '';
        if($('#hiddenProveedores').length){
            proveedores = $('#hiddenProveedores').val();
        }
        
        //MONTO LOS PARÁMETROS RECOPILADOS
        var parametros = {
            'Proveedores': proveedores,
            'ambitos': ambitos,
            'strExperiencias': strExperiencias,                  
            'paquete_id': paquete_id,
            'idtipo' : idtipo,
            'idcategoria' : idcategoria,
            'idespecialidad': idespecialidad,
            'minima' : min,
            'maxima' : max,
            'idregion': idregion,
            'idprovincia': idprovincia,
            'fase': 2,
            'preseleccion': preseleccion,
            'intervencion': intervencionBOVIS
        };        
        //LLAMO A LA BÚSQUEDA SEGÚN LOS PARÁMETROS ELEGIDOS
        $.ajax({ 
            async: false, 
            type: 'GET',                
            url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/listSelectionSupplier.php',                
            data: parametros,
            success:function(data){
                $('#resultado').html(data);
            },
            error: function(result) {
                alert('Data not found');
            }
        });
    });

    function gestionCheck(id, val, str, cuadrocheck){

        
        if(id==0 ){
            if(cuadrocheck == 0){
                if(val==true){
                    $('.especialidades_check').prop('checked',false);
                }else{
                    $('.especialidades_check').prop('checked',true);
                }
            }else{
                if(val==true){
                    $('.especialidades_check').prop('checked',true);
                }else{
                    $('.especialidades_check').prop('checked',false);
                }           
            }
        }else{
            if(cuadrocheck == 0){
                if(val==true){
                    $(str).prop('checked',false);
                    $('#check_0').prop('checked', false);
                }else{
                    $(str).prop('checked',1);
                }                    
            }else{
                if(val==false){
                    $(str).prop('checked',false);
                    $('#check_0').prop('checked', false);
                }else{
                    $(str).prop('checked',true);
                }                                
            }
        }    
    }

    $('.especialidades_check').on('click', function() {
        var id = $(this).attr('id').replace('check_', '');
        var str = '#check_'+id;
        var val = $(str).prop('checked');
        
        gestionCheck(id, val, str, 1);
    });
    
    $('.etiqueta_check').on('click', function(){                       
        var id = $(this).attr('id').replace('etiqueta_', '');
        var str= '#check_'+id;
        var val = $(str).prop('checked');

        gestionCheck(id, val, str, 0);
    });

    var identificador = 0;
    if($('#identificador').length){
         identificador = $('#identificador').val();
         //alert('dentro');
    }
    if(identificador<1){
         $('#incorporarSeleccionados').css('display', 'none');
    }
    

</script>";

echo "<div id='caja01' class='search_page' style='width:98%; display: inline-flex;'>";
echo $javascripts;
//$lista=getTiposExperiencias();



$strProveedores = "";
if(isset($_GET['Proveedores'])){
    $strProveedores = $_GET['Proveedores'];
}


echo "<input type='hidden' id='hiddenidtipo' value='{$_GET['idtipo']}' >";
echo "<input type='hidden' id='hiddenidcategoria' value='{$_GET['idcategoria']}' >";
echo "<input type='hidden' id='hiddenidespecialidad' value='{$_GET['idespecialidad']}' >";
echo "<input type='hidden' id='hiddenminima' value='{$_GET['minima']}' >";
echo "<input type='hidden' id='hiddenmaxima' value='{$_GET['maxima']}' >";
echo "<input type='hidden' id='hiddenidregion' value='{$_GET['idregion']}' >";
echo "<input type='hidden' id='hiddenidprovincia' value='{$_GET['idprovincia']}' >";
echo "<input type='hidden' id='hiddenpaquete_id' value='{$_GET['paquete_id']}' >";
echo "<input type='hidden' id='hiddenpreseleccion' value='{$_GET['preseleccion']}' >";
echo "<input type='hidden' id='hiddenambito' value='{$_GET['ambitos']}' >";
echo "<input type='hidden' id='hiddenProveedores' value='{$strProveedores}' >";



echo"<table class='tab_cadre_fixe' style='font-size: 10px; width: 20%; border-radius: 4px !important;box-shadow: 0px 1px 2px 1px #D2D2D2;'>";
 //style='border-bottom: 10px solid #fff; padding: 5px;  background-color: #f8f8f8; color: #0e52a0;'
        echo "<thead><tr><td class='center' colspan='6' style='color:#fff; background-color:#0e52a0;font-size: 14px; height: 20px;'>FILTROS</td></tr></thead>";
            echo"<tr class='tab_bg_1 center' style='vertical-align: top; '>";         
                echo "<td rowspan='7' style='width: 30%; text-align:left;'>" . __('Tipos de experiencias');
                    echo "<div class='list-especialidades' style='font-size: 10px; width: 150px; height: 210px;'><ul>";
                        echo "<li id='linea_0' class='linea' style='BACKGROUND-COLOR: #d5e6fb;'><input id='check_0' type='checkbox' value='0' checked class='especialidades_check'><label id='etiqueta_0' class='etiqueta_check' style='font-weight:bold;'> SELECCIONAR TODOS </label></li>";                    
                        echo "<li id='linea_999' class='linea' style='BACKGROUND-COLOR: #d5e6fb;' TITLE='El proveedor no posee experiencia registrada'><input id='check_999' type='checkbox' value='999' checked class='especialidades_check'><label id='etiqueta_999' class='etiqueta_check' style='font-weight:bold;'> SIN EXPER. REGISTRADA </label></li>";
                        $query ="SELECT id, name FROM glpi_plugin_comproveedores_experiencestypes order by name";
                        $result = $DB->query($query);                        
                        while ($data=$DB->fetch_array($result)) {
                            echo "<li id='linea_{$data['id']}' class='linea'><input id='check_{$data['id']}' value='{$data['id']}' class='especialidades_check' type='checkbox' checked><label id='etiqueta_{$data['id']}' class='etiqueta_check'> {$data['name']}</label></li>";
                        }
                    echo "</ul></div>";
                echo "</td>";               
            echo "</tr>";

            echo"<tr class='tab_bg_1 center' style='height:10px;' >";
                echo "<td style='font-size: 10px; text-align:right;'  title='En caso de no activarlo se mostarán con experiencia y sin experiencia con BOVIS'>" . __('Experiencia con BOVIS') . "</td>";
                echo "<td style='font-size: 10px; width: 18%; text-align:left;' class='selector_proveedor'>";
                        echo "<input id='intervencionBOVIS' type='checkbox' name='intervencion_bovis' title='En caso de no activarlo se mostarán con experiencia y sin experiencia con BOVIS'/>";
                echo "</td>";			
            echo "</tr>";

            echo"<tr class='tab_bg_1 center' style='height:10px; display: none;'>";
                echo "<td style='font-size: 10px; text-align:left;'>";
                        echo "LEED"; 
                echo "</td>";
                echo "<td style='font-size: 10px; text-align:left;'>";
                        echo "<input type='checkbox' id='checkleed'/>";
                echo "</td>";
                echo "<td style='font-size: 10px; text-align:left;'>";
                        echo "BIM";
                echo "</td>";
                echo "<td style='text-align:left;'>";
                        echo "<input type='checkbox' id='checkbim'/>";
                echo "</td>";
            echo "</tr>";
            echo"<tr rowspan='4' class='tab_bg_1 center' style='height:10px;  display: none;'>";
                echo "<td style='font-size: 10px; text-align:left;'>";
                        echo "BREEAM";
                echo "</td>";
                echo "<td style='font-size: 10px; text-align:left;'>";
                        echo "<input type='checkbox' id='checkbreeam'/>";
                echo "</td>";
                echo "<td style='font-size: 10px; text-align:left;'>";
                        echo "Otros";
                echo "</td>";
                echo "<td style='font-size: 10px; text-align:left;'>";
                        echo "<input id='otros_certificados' type='checkbox' id='checkotros_certificados'/>";
                echo "</td>"; 
                /**
                echo "<td style='font-size: 10px; text-align:left;'>";
                        echo "<select id='otrosSel' style='font-size: 10px; opacity:0;'>";
                                echo "<option> --------------------- </option>";
                                echo "<option>CERTIFICADO 1</option>";
                                echo "<option>CERTIFICADO 2</option>";
                        echo "</select>";
                echo "</td>"; 			**/	
            echo "</tr>";

            echo"<tr class='tab_bg_1'>";
                echo"<td colspan='5' style='text-align: center; vertical-align: middle;'>";
                    //echo "<span class='vsubmit' style='margin-right: 15px;' onClick='location.reload();'>ATRAS</span>";
                    //echo "<span onclick='filtrarListaProveedores(".$_GET['paquete_id'].")' class='vsubmit' style='margin-right: 15px;'>FILTRAR</span>";
                    echo "<input id='botonAnterior' type='submit' title='RETROCEDER' class='boton_anterior' value=' '  style='margin-top: 10px; margin-right: 10px;'/>";
                    //if ($_GET['paquete_id']>0){
                        
                    //}
                    //echo "<input id='botonSiguiente' type='submit' title='SIGUIENTE' class='boton_siguiente' value=' ' style='float:left;'/>";
                    echo "<input id='botonFiltro' type='submit' title='MOSTRAR SOLO AQUELLOS QUE CUMPLAN LAS CONDICIONES' class='boton_filtro' style='margin-top: 10px;' value=' '/>";					
                 echo"</td>";
            echo "</tr>";
echo "</table>";


$_GET['fase'] = 2;
$_GET['Proveedores'] = $strProveedores;

echo "<div id='resultado' style='margin-top: 10px; margin-left: 15px; height: 600px; width: 80%;border: 1px solid #ccc;min-height: 300px; background-color:#e6e6e6; text-align: center;'>";
include 'listSelectionSupplier.php';
echo "</div>";
echo "</div>";

$contrato_id=$_GET['paquete_id'];


/**
echo"<span onclick='imprimirPdf(\"$preselecionIds\", ".$contrato_id.")' class='vsubmit' style='margin-right: 15px; '>IMPRIMIR</span>";
echo"<span onclick='guardarPreseleccion(".$_GET['paquete_id'].",\"$preselecionIds\")' class='vsubmit' style='margin-right: 15px; '>GUARDAR PRESELECCIÓN</span>";
echo "<span onclick='incluirProveedoresAlPaquete(".$_GET['paquete_id'].")' class='boton_licitacion' style='margin-right: 15px;'> </span>";
echo"<br>";
echo"<br>";
echo"<br>";**/



function getTiposExperiencias(){
    GLOBAL $DB,$CFG_GLPI;

    $query ="SELECT id, name FROM `glpi_plugin_comproveedores_experiencestypes` order by id";
    $result = $DB->query($query);
    $i=1;
    while ($data=$DB->fetch_array($result)) {
        $lista[$i]=$data['name'];
        $i++;
    }

    return $lista;
}


