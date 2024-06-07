<?php
    include ("../../../inc/includes.php");
    GLOBAL $DB,$CFG_GLPI;
    $objCommonDBT = new CommonDBTM;
    
    $verEsp         = $_GET['verEsp'];
    $cvid           = $_GET['cvid'];

    echo "<input type='submit' class='boton_add' id='addEmpresaDestacada' value='' title='NUEVA EMRESA' style='margin: 4px 10px 0px 4px; float: left;'/>";
    echo "<table id='tablaEmpresas' class='display compact hover' >";
    echo "<thead>";
    echo "<tr>";
    echo "<th>NOMBRE DE LAS EMPRESAS DESTACADAS</th>";                        
    if($verEsp){echo "<th style='width: 50px;' >Edit</th>";}
    if($verEsp){echo "<th style='width: 50px;' >Quit</th>";}
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    $query2 ="SELECT * FROM glpi_plugin_comproveedores_featuredcompanies WHERE cv_id=".$cvid." ORDER BY id asc";
    $result2 = $DB->query($query2);
    $i=1;                                                
    while ($data=$DB->fetch_array($result2)) {
        $i++;
        echo "<tr>";  
        echo "<td class='left' id='NombreEmpresaDestacada_{$data['id']}'>{$data['nombre_empresa_destacada']}</td>";
        if($verEsp){
            echo "<td class='center'>";
            echo "<input type='submit' id='editarEmpresaDestacada_{$data['id']}' class='boton_editar_empresadestacada' value='' title='editar empresa'/>";
            echo "</td>";
        }                            
        if($verEsp){
            echo "<td class='center'>";
            echo "<input type='submit' id='quitarEmpresaDestacada_{$data['id']}' class='boton_borrar_empresadestacada' value='' title='quitar empresa'/>";
            echo "</td>";                               
        }
        echo"</tr>";                            
    }
    echo "</tbody>";
    echo "</table>";

    echo "<div id='modalEmpresasDestacadas' title='AÑADIR / EDITAR EMPRESA DESTACADA'>";
        echo "<input type='hidden' id='modalIdEmpresaDestacada' value='' />";
        echo "Razón social:<br> <input type='text' id='modalDenominacionEmpresaDestacada' value='' style='width: 400px;'/>";
    echo "</div>";   
            
    echo "<script type='text/javascript'>
            $('#addEmpresaDestacada').on('click', function(){
                $('#modalIdEmpresaDestacada').val(0);
                $('#modalDenominacionEmpresaDestacada').val('');                            
                $('#modalEmpresasDestacadas').dialog('open');
            });        
            $('#tablaEmpresas').DataTable({
                    'searching':      true,
                    'scrollY':        '100px',
                    'scrollCollapse': true,
                    'paging':         false,
                    'info':           false,
                    'searching':      false,
                    'language': {'url': '".$CFG_GLPI["root_doc"]."/lib/jqueryplugins/Spanish.json'}       
            });         

            $('.boton_borrar_empresadestacada').on('click', function(){
                var resp = confirm('¿Realmente desea quitar esta empresa?', 'CV de proveedor');
                if(resp){
                    var iden = $(this).prop('id').replace('quitarEmpresaDestacada_', '');
                    $.ajax({ 
                        async: false, 
                        type: 'GET',
                        data: {id: iden},                 
                        url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/quitarEmpresaDestacada.php',                    
                        success:function(data){
                            srchEmpresasDestacadas ($('#cvIdOculto').val(), {$verEsp});
                        },
                        error: function(result) {
                            alert('Error al actualizar');
                        }
                    });                                         
                }
            });                   
            
            $('.boton_editar_empresadestacada').on('click', function(){
                var id = $(this).attr('id').replace('editarEmpresaDestacada_','');
                var aux = '#NombreEmpresaDestacada_'+id;
                var nombre = $(aux).html().trim();

                $('#modalIdEmpresaDestacada').val(id);
                $('#modalDenominacionEmpresaDestacada').val(nombre);

                $('#modalEmpresasDestacadas').dialog('open');
            });

            $('#modalEmpresasDestacadas').dialog({
                autoOpen: false,
                height: 250,
                width: 450,
                modal: true,
                buttons: {
                    'Aceptar': function() {
                        var denominacion = $('#modalDenominacionEmpresaDestacada').val();
                        var cvid = $('#cvIdOculto').val();
                        var id = $('#modalIdEmpresaDestacada').val();                                        
                        $.ajax({ 
                            async: false, 
                            type: 'GET',
                            data: { 'denominacion': denominacion, 
                                    'cvid': cvid,  
                                    'id': id},
                            url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/insertEmpresaDestacada.php',                    
                            success:function(data){
                                srchEmpresasDestacadas ($('#cvIdOculto').val(), {$verEsp});
                            },
                            error: function(result) {
                                alert('Error al actualizar empresas destacadas');
                            }
                        });                           
                        $('#modalEmpresasDestacadas').dialog('close');
                    },
                    'Cancelar': function() {
                        $('#modalEmpresasDestacadas').dialog('close');
                    }
                },
                close: function() {
                    $('#modalEmpresasDestacadas').dialog('close');
                }
            }); 
    </script>";
                        

