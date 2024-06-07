<?php

/* 
 * Gestión de proveedores BOVIS es una aplicaciÃ³n desarrollada por el equipo TI de 
FOMENTO DE TÃ‰CNICAS EXTREMEÃ‘AS S.L. (FOTEX)
+34924207328
http://www.fotex.es
Comienzo del desarrollo: enero de 2019
 */


        var bovis   = 0;
        if($('#intervencionBOVIS').prop('checked')){
            bovis = 1;
        }               
        alert(bovis);

        var strExperiencias = '';
        $('.especialidades_check:checked').each(function() {
            if(strExperiencias!=''){
                strExperiencias = strExperiencias+','+$(this).val();
            }else{
                strExperiencias = $(this).val();
            }
        });               

        var min = 0;
        var max = 999999999;
        if ($('#hiddenminima').val().replace('.', '').replace(',','.') != '') {
            min = $('#hiddenminima').val().replace('.', '').replace(',','.');
        }		
        if ($('#hiddenmaxima').val().replace('.', '').replace(',','.') != '') {
            max = $('#hiddenmaxima').val().replace('.', '').replace(',','.');
        }
        var idtipo = 0;
        if($('#hiddenidtipo').length){
            idtipo = $('#hiddenidtipo').val();
        }
        var idcategoria = 0;
        if($('#idcategoria').length){
            idcategoria = $('#idcategoria').val();
        }
        var idespecialidad = 0;
        if($('#hiddenidespecialidad').length){
            idespecialidad = $('#hiddenidespecialidad').val();
        }         
        var idregion = 0;
        if($('#hiddenidregion').length){
            idregion = $('#hiddenidregion').val();
        }                
        var idprovincia = 0;
        if($('#idprovincia').length){
            idprovincia = $('#idprovincia').val();
        }   
        var paquete_id = 0;
        if($('#hiddenpaquete_id').length){
            paquete_id = $('#hiddenpaquete_id').val();
        }            
        var preseleccion = '';
        if($('#hiddenpreseleccion').length){
            preseleccion = $('#hiddenpreseleccion').val();
        }                                     
        var ambitos = '';
        if($('#hiddenambito').length){
            ambitos = $('#hiddenambito').val();
        }             
        var proveedores = '';
        if($('#hiddenProveedores').length){
            proveedores = $('#hiddenProveedores').val();
        } 
        var intervencion = 0;
        if($('#intervencion_bovis').prop('checked')){
            intervencion = 1;
        }
        alert(intervencion);


        var parametros = {
            'Proveedores': proveedores,
            'ambitos': ambitos,
            'bovis': bovis,
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
            'intervencion': intervencion
        };        

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