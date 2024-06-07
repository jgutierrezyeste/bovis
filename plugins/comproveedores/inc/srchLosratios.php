<?php
    include ("../../../inc/includes.php");
    GLOBAL $DB,$CFG_GLPI;
    $objCommonDBT = new CommonDBTM;
    
    $cvid = $_GET['cvid'];
    echo "<input id='cvidLosRatios' type='hidden' value='{$cvid}' >";
    echo "<h4 style='background-color: #0e52a0; padding: 4px; color: #fff;'>Consignar los siguientes índices de siniestralidad</h4>";
    echo "<table id='gestionLosratios'>";
        echo "<tr>";
            echo "<td>Año: <input type='text' id='ADDanio' value='' size='4' ></td>";
            echo "<td>Incidencia: <input type='text' id='ADDincidencia' value='' size='4' ></td>";
            echo "<td>Frecuencia: <input type='text' id='ADDfrecuencia' value='' size='4' ></td>";
            echo "<td>Gravedad: <input type='text' id='ADDgravedad' value='' size='4' ></td>";
            echo "<td style='padding-left: 50px;'><input id='addlosratios' type='submit' class='boton_add' value=''/></td>";
        echo "</tr>";
    echo "</table>";
    
    echo "<table id='tblSiniestrabilidad' class='display compact' >";
        echo "<thead>";
        echo "<tr>";
                echo "<th>Año</th>";
                echo "<th>Incidencia</th>";
                echo "<th>Frecuencia</th>";
                echo "<th>Gravedad</th>";
                echo "<th></th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
            $query2 ="SELECT * FROM glpi_plugin_comproveedores_lossratios WHERE cv_id=".$cvid." order by anio desc" ;
            $result2 = $DB->query($query2);
            if($result2->num_rows!=0){
                    $i=0;
                    $incidencia = 0;
                    $frecuencia = 0;
                    $gravedad = 0;                                
                    while ($data=$DB->fetch_array($result2)) {
                        echo "<tr>";
                        if($i==0){
                            $incidencia = $data['incidencia'];
                            $frecuencia = $data['frecuencia'];
                            $gravedad = $data['gravedad'];                                        
                        }
                        echo "<td id='anio_{$data['id']}'>";
                        echo number_format($data['anio'], 0, ',', '.');
                        echo "</td>";                                    
                        echo "<td id='incidencia_{$data['id']}'>";
                        echo number_format($data['incidencia'], 2, ',', '.');
                        echo "</td>";
                        echo "<td id='frecuencia_{$data['id']}'>";
                        echo number_format($data['frecuencia'], 2, ',', '.');
                        echo "</td>";
                        echo "<td id='gravedad_{$data['id']}'>";
                        echo number_format($data['gravedad'], 2, ',', '.');
                        echo "</td>";            
                        echo "<td><input id='quitarAnio_{$data['id']}' type='submit' value='' class='boton_borrar'></td>";
                        echo "</tr>";      
                        $i++;
                    }
            }           
        echo "</tbody>";
    echo "</table>";

    echo "<script type='text/javascript'>
            var cvid = $('#cvidLosRatios').val();

            $('#tblSiniestrabilidad').DataTable({
                'scrollY':        110,
                'scrollCollapse': true,
                'searching': false,
                'info': false,
                'paging': false,
                'ordering': false,         
                'language': {'url': '".$CFG_GLPI["root_doc"]."/lib/jqueryplugins/Spanish.json'}                  
            });
                        
            $('#addlosratios').on('click', function(){
                
                if(cvid != '0'){

                    var a = $('#ADDanio').val();
                    var i = $('#ADDincidencia').val();
                    var f = $('#ADDfrecuencia').val();
                    var g = $('#ADDgravedad').val();

                    $.ajax({ 
                        async: false, 
                        type: 'GET',
                        data: {'anio': a, 'incidencia': i, 'gravedad': g, 'frecuencia': f, 'cvid': cvid},                  
                        url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/insertLosratios.php',  				
                        success:function(data){
                            srchLosratios();
                        },
                        error: function(result) {
                            alert('Error de conexión con la base de datos!');
                        }
                    });   
                }else{
                    alert('No se pueden insertar los datos');
                }
            });   

            $('.boton_borrar').on('click', function(){

                var msg = confirm('¿Realmente desea elminar este elemento?');
                if(msg){
                    var id = $(this).attr('id').replace('quitarAnio_','');
                    $.ajax({ 
                        async: false, 
                        type: 'GET',
                        data: {'id': id},                  
                        url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/quitarLosratios.php',  				
                        success:function(data){
                            srchLosratios();
                        },
                        error: function(result) {
                            alert('Error de conexión con la base de datos!');
                        }
                    });       
                }
            });
    </script>";
                        

