<?php
    include ("../../../inc/includes.php");
    GLOBAL $DB,$CFG_GLPI;
    $objCommonDBT = new CommonDBTM;
        
    $verEsp         = $_GET['verEsp'];
    $cvid           = $_GET['cvid'];
//624
    $query2 = "SELECT id, nombre, DATE_FORMAT(fecha_cambio,'%d/%m/%Y') as fecha FROM glpi_plugin_comproveedores_previousnamescompanies WHERE cv_id={$cvid} order by fecha_cambio asc";
    $result2 = $DB->query($query2);
    echo "<input type='submit' class='boton_add' id='addNombreEmpresa' value='' title='NUEVO NOMBRE ANTERIOR DE LA EMPRESA' style='margin: 4px 10px 0px 4px; float: left;'/>";
    echo "<table id='tablaNombresAnteriores' class='display compact hover'>";
    echo "<thead>";
        echo "<tr>";
            echo "<th>NOMBRES ANTERIORES DE LA EMPRESA</th>";                        
            echo "<th>FECHA DEL CAMBIO</th>"; 
            if($verEsp){echo "<th style='width: 50px;' >Edit</th>";}
            if($verEsp){echo "<th style='width: 50px;' >Quit</th>";}
        echo "</tr>";
    echo "</thead>";
    echo "<tbody>";                        
    while ($data=$DB->fetch_array($result2)) {
        echo "<tr>";
            echo "<td id='NombresAnteriores_nombre_{$data['id']}'>{$data['nombre']}</td>"; 
            echo "<td id='NombresAnteriores_fecha_{$data['id']}'>{$data['fecha']}</td>";
            if($verEsp){ echo "<td><input id='editarNombresAnteriores_{$data['id']}' class='boton_editar_nombreanterior' type='submit' value='' title='quitar elemento'/></td>"; }                            
            if($verEsp){ echo "<td><input id='quitarNombresAnteriores_{$data['id']}' class='boton_borrar_nombreanterior' type='submit' value='' title='quitar elemento'/></td>"; }
        echo "</tr>";                            
    }
    echo "</tbody>";
    echo "</table>";  

    echo "<div id='modalNombresAnteriores' title='AÑADIR/EDITAR NOMBRE ANTERIOR DE LA EMPRESA'>";
        echo "<input type='hidden' id='modalId_cambioNombreAnterior' value='0'>";
        echo "<table>";
        echo "<tr>";
            echo "<td style='text-align: left;'>Razón social:<br><input type='text' id='modalDenominacionNombreAnterior' value='' style='width: 400px;'/></td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td style='text-align: left;'>Fecha del cambio:<br><input type='date' id='modalfecha_cambioNombreAnterior' value='' /></td>";
        echo "</tr>";                            
        echo "</table>";
    echo "</div>";
            
    echo "<script type='text/javascript'>
            $('#tablaNombresAnteriores').DataTable({
                    'searching':      true,
                    'scrollY':        '100px',
                    'scrollCollapse': true,
                    'paging':         false,
                    'info':           false,
                    'searching':      false,                                
                    'language': {'url': '".$CFG_GLPI["root_doc"]."/lib/jqueryplugins/Spanish.json'}              
            });     
                            
            $('#modalNombresAnteriores').dialog({
                autoOpen: false,
                height: 250,
                width: 450,
                modal: true,
                buttons: {
                    'Aceptar': function() {
                        var cvid = {$cvid};
                        var id = $('#modalId_cambioNombreAnterior').val();
                        var denominacion = $('#modalDenominacionNombreAnterior').val();
                        var fecha_cambio = $('#modalfecha_cambioNombreAnterior').val();     

                        $.ajax({ 
                            async: false, 
                            type: 'GET',
                            data: { 'id': id,
                                    'denominacion': denominacion, 
                                    'fecha_cambio': fecha_cambio,
                                    'cvid': cvid },                 
                            url: '".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/insertNombreAnterior.php',                    
                            success: function(data){
                                srchNombresAnteriores ($('#cvIdOculto').val(), {$verEsp});
                                $('#modalNombresAnteriores').dialog('close');
                            },
                            error: function(result) {
                                alert('Error al actualizar los nombres anteriores');
                                $('#modalNombresAnteriores').dialog('close');
                            }
                           
                        });                                         
                    },
                    'Cancelar': function() {
                        $('#modalNombresAnteriores').dialog('close');
                    }
                },
                close: function() {
                    $('#modalNombresAnteriores').dialog('close');
                }
            }); 
            

            $('#addNombreEmpresa').on('click', function(){
                $('#modalId_cambioNombreAnterior').val(0);
                $('#modalDenominacionNombreAnterior').val('');
                $('#modalfecha_cambioNombreAnterior').val('');

                $('#modalNombresAnteriores').dialog('open');
            });            
             

            $('.boton_borrar_nombreanterior').on('click', function(){
                var resp = confirm('¿Realmente desea quitar este nombre?', 'CV de proveedor');
                if(resp){
                    var iden = $(this).prop('id').replace('quitarNombresAnteriores_', '');
                    $.ajax({ 
                        async: false, 
                        type: 'GET',
                        data: {id: iden},                 
                        url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/quitarNombreAnterior.php',                    
                        success:function(data){
                            srchNombresAnteriores ($('#cvIdOculto').val(), {$verEsp});
                        },
                        error: function(result) {
                            alert('Error al actualizar');
                        }
                    });                                         
                }
            });

            
            $('.boton_editar_nombreanterior').on('click', function(){
                var id = $(this).attr('id').replace('editarNombresAnteriores_','');
                var aux = '#NombresAnteriores_nombre_'+id;
                var nombre = $(aux).html().trim();
                var aux = '#NombresAnteriores_fecha_'+id;
                var fecha = $(aux).html().trim();

                $('#modalId_cambioNombreAnterior').val(id);
                $('#modalDenominacionNombreAnterior').val(nombre);
                $('#modalfecha_cambioNombreAnterior').val(fecha);

                $('#modalNombresAnteriores').dialog('open');
            });              
    </script>";
                        

