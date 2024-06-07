<?php
    include ("../../../inc/includes.php");
    GLOBAL $DB,$CFG_GLPI;
    $objCommonDBT = new CommonDBTM;
    
    $verEsp         = $_GET['verEsp'];
    $cvid           = $_GET['cvid'];

    $query2 ="SELECT * FROM glpi_plugin_comproveedores_subcontractingcompanies WHERE cv_id={$cvid} order by puesto asc";
    $result2 = $DB->query($query2);            
    echo "<input type='submit' class='boton_add' id='addSubcontratistas' value='' title='NUEVA NOMBRE' style='margin: 4px 10px 0px 4px; float: left;'/>";
    echo "<table id='tablaSubcontratistas' class='compact hover' style='width: 600px;'><thead><th>RAZÓN SOCIAL</th>";
    if($verEsp){
        echo "<th style='width: 50px;'>Quit.</th>";
    }
    echo "</thead><tbody>";
        while ($data=$DB->fetch_array($result2)) {
            echo "<tr><td>";
            echo $data['nombre_empresa_subcontratista'];
            echo "</td>";
            if($verEsp){echo "<td style='text-align: center;'><input id='quitarSubcontratistas_{$data['id']}' class='boton_borrar_subcontratista' type='submit' value='' title='quitar elemento'/></td>"; }
            echo "</tr>";
        } 
    echo "</tbody></table>";
    echo "<div id='modalSubcontratistas' title='AÑADIR SUBCONTRATISTAS'>";
        echo "<input id='modalSubcontratistaId' value='' type='hidden' >";
        echo "Razón social:<br> <input type='text' id='modalDenominacionSubcontratistas' value='' style='width: 400px;'/>";
    echo "</div>";     

    echo "<script type='text/javascript'>
        
            $('#addSubcontratistas').on('click', function(){
                $('#modalSubcontratistaId').val('0');  
                $('#modalDenominacionSubcontratistas').val('');                            
                $('#modalSubcontratistas').dialog('open');
            });        

            $('#tablaSubcontratistas').DataTable({
                      'searching':      true,
                      'scrollY':        '200px',
                      'scrollCollapse': true,
                      'paging':         false,
                      'info':           false,
                      'searching':      false,                                
                      'language': {'url': '".$CFG_GLPI["root_doc"]."/lib/jqueryplugins/Spanish.json'}
            });   
       
            $('.boton_borrar_subcontratista').on('click', function(){
                var resp = confirm('¿Realmente desea quitar este subcontratista?', 'CV de proveedor');
                if(resp){
                    var iden = $(this).prop('id').replace('quitarSubcontratistas_', '');
                    $.ajax({ 
                        async: false, 
                        type: 'GET',
                        data: {id: iden},                 
                        url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/quitarSubcontratistas.php',                    
                        success:function(data){
                            srchSubcontratistas ($('#cvIdOculto').val(), {$verEsp});
                        },
                        error: function(result) {
                            alert('Error al actualizar');
                        }
                    });                                         
                }
            });
            
            $('#modalSubcontratistas').dialog({
                autoOpen: false,
                height: 250,
                width: 450,
                modal: true,
                buttons: {
                    'Aceptar': function() {
                        var id = $('#modalSubcontratistaId').val();
                        var denominacion = $('#modalDenominacionSubcontratistas').val();
                        var cvid = $('#cvIdOculto').val();
                        $.ajax({ 
                            async: false, 
                            type: 'GET',
                            data: {'denominacion': denominacion, 'cvid': cvid, 'id': id},                 
                            url: '".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/insertSubcontratistas.php',                    
                            success:function(data){
                                srchSubcontratistas ($('#cvIdOculto').val(), {$verEsp});
                                $('#modalSubcontratistas').dialog('close');
                            },
                            error: function(result) {
                                alert('Error al actualizar ámbitos');
                                $('#modalSubcontratistas').dialog('close');
                            }
                        });
                    },
                    'Cancelar': function() {
                        $('#modalSubcontratistas').dialog('close');
                    }
                },
                close: function() {
                    $('#modalSubcontratistas').dialog('close');
                }
            }); 
    </script>";
                        

