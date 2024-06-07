<?php

use Glpi\Event;
include ("../../../inc/includes.php");
GLOBAL $DB,$CFG_GLPI;


    $valoracion         = 0;
    $valoracion_id      = 0;
    if(isset($_GET['contrato_id'])){
        $contrato_id = $_GET['contrato_id'];
    }else{
        $contrato_id = 0;
    }
    if($_GET['fecha']=='' && !isset($_GET['fecha'])){
        $hoy            = getdate();
        $fecha          = date('d-m-Y');            
    }else{
        $fecha            = $_GET['fecha'];
    }
    if(isset($_GET['tipo_especialidad'])){
        $IdTipoEspecialidad = $_GET['tipo_especialidad'];
    }else{
        $IdTipoEspecialidad = 0;
    }
        
    echo consultaAjax();    
   
    $query2 = "select 
            TotalIdsSubcriterios(".$IdTipoEspecialidad.") as total_ids_subcriterios,
            (select count(criterio2.id) as num
                from glpi_plugin_comproveedores_criterios as criterio2 
                where criterio2.criterio_padre=criterio.criterio_padre and criterio2.tipo_especialidad=".$IdTipoEspecialidad.") as num_subcriterios, 
            (select GROUP_CONCAT(criterio4.id ORDER BY criterio4.id asc) 
                from glpi_plugin_comproveedores_criterios as criterio4 
                where criterio4.criterio_padre=criterio.criterio_padre and criterio4.tipo_especialidad=".$IdTipoEspecialidad.") as num_ids_criterio, 
            criterio.id as criterio_id,
            criterio.criterio_padre, 
            criterio.criterio_hijo,
            criterio.ponderacion,
            criterio.denom_Mala,
            criterio.denom_Excelente
            from glpi_plugin_comproveedores_criterios as criterio 
            where criterio.tipo_especialidad=".$IdTipoEspecialidad." order by 5,6";
    $result2 = $DB->query($query2);
    //echo $query2;

    if(isset($_GET['id'])){
        $display    = "none";
    }else{
        $display    = "-webkit-box";
    }
    
    echo "<div align='center' style='height: 600px; overflow-y: scroll;'>";
    echo "<table cellspacing='0' cellpadding='0' style='margin-top:0px; border: none; padding: 0px; width: 80%;'>";
    
    echo "<tr style='border: none;'>";
        echo "<td colspan='2'>";
            echo "<div id='visualizar_ultima_eval' style='display: -webkit-box; font-size: 14px; padding-top: 5px;'>¿ES EVALUACIÓN FINAL? ";
            echo "<input id='evaluacion_final' style='width:17px; height:17px; margin-left: 4px;' type='checkbox'/>";
            echo "</div>";
        echo "</td>";
        echo "<td colspan='7' style='font-size: 14px;'>";
            echo "COMENTARIOS:";
        echo "</td>";
    echo "</tr>";
    echo "<tr style='border: none;'>";
        echo "<td colspan='2' style=' border: none;'>";
        echo "<div id='fecha_valoracion' style='display: -webkit-box; font-size: 14px; '>FECHA DE VALORACIÓN: &nbsp;"; 
            //$fechaAux = date('Y-m-d', strtotime($fecha));
            $fechaAux = substr($fecha, 6, 4).'-'.substr($fecha, 3, 2).'-'.substr($fecha, 0, 2);
            echo "<input id='fechaValoracion' type='date' style='font-size: 14px; padding: 4px;' value='{$fechaAux}'/>";
        echo "</div>";
        echo "</td>";

        //Si es una nueva evaluación que aparezca el Evaluación final. 
        //Esta con display para el caso en que se modifica la ultima evaluación, para que pueda desmarcar y crear nuevas
        echo "<td colspan='7' class='left'>";
            echo "<textarea  id='comentario' rows='4' cols='90' maxlength='255' style='overflow-y: auto; font-size: 14px; resize: none; margin-bottom: 10px;'></textarea>";
        echo "</td>";
    echo"</tr>";
    echo "</table>";

    echo "<style>
        .puntuable :hover{
            background-color: #9dc4f4 !important;
        }
    </style>";
    echo "<table id='tablaTrab' class='display compact' style='margin-top:0px; width:90%;'>";
    echo "<thead>";                                          
        //echo "<tr class=' tab_cadre_fixehov nohover' style='border: none;'><th colspan='10' style='border: none;'>Evaluación</th></tr>";
        echo "<tr>";
            echo "<th style='width: 60px; background-color:#D8D8D8; border: none;' class='colporcentaje'>Aplica</th>";
            echo "<th style='background-color:#D8D8D8; border: none;'>Criterios</th>";
            echo "<th style='background-color:#D8D8D8; border: none;'>Subcriterios</th>";
            echo "<th style='width: 60px; background-color:#D8D8D8; border: none;'>".__('Mal<br>(1)')."</th>";
            echo "<th style='width: 60px; background-color:#D8D8D8; border: none;'>".__('Pobre<br>(2)')."</th>";
            echo "<th style='width: 60px; background-color:#D8D8D8; border: none;'>".__('Adecuado<br>(3)')."</th>";
            echo "<th style='width: 60px; background-color:#D8D8D8; border: none;'>".__('Bien<br>(4)')."</th>";
            echo "<th style='width: 60px; background-color:#D8D8D8; border: none;'>".__('Excelente<br>(5)')."</th>";
            echo "<th style='width: 60px; background-color:#D8D8D8; border: none;'>".__('Valor')."</th>";
        echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    $cambio_criterio_padre  = '';                        
    $nombre_criterio        = '';
    $columna_par_impar      = 0;
    $num = 0;
    while ($data=$DB->fetch_array($result2)) {

        //Color criterios
        if ($nombre_criterio!=$data['criterio_padre']){
            $nombre_criterio=$data['criterio_padre'];
            if($columna_par_impar==0){
                $color_criterio='#fff';
                $columna_par_impar  = 1;
            }else{
                $color_criterio='#f3f3f3';
                $columna_par_impar  = 0;
            }

        }

        $total_ids_subcriterios=$data['total_ids_subcriterios'];

        echo "<tr class='tab_bg_2 puntuable' style='height:20px; border: none;'>";
            $criterio_padre=str_replace ( '_' , ' ' , $data['criterio_padre']);
            $criterio_padre=ucfirst($criterio_padre);
            if($cambio_criterio_padre!=$data['criterio_padre']){
              
                //$cambio_criterio_padre=$data['criterio_padre'];
                $num = $data['num_subcriterios'];
                echo "<td rowspan='".$num."' class='center' style='background-color:".$color_criterio.";font-weight:bold; border: none;'>";
                    echo "<input id='chk_{$data['criterio_padre']}' class='cuadrochequeo' type='checkbox' checked />";
                echo "</td>";                   
                echo "<td rowspan='".$num."' id='criterio_".$data['criterio_id']."_valor_0' class='left' style='padding: 4px; background-color:".$color_criterio.";font-size: 12px; font-weight:bold;  border: none;'>";
                echo $criterio_padre;									
                echo "</td>";               
            }
            echo "<td class='left' style='padding: 4px; background-color:".$color_criterio."; border: none;'>";
                echo $data['criterio_hijo']."<input type='hidden' class='cls{$data['criterio_padre']}' value='{$data['criterio_id']}'/>";
            echo "</td>";
            echo "<td title='".$data['denom_Mala']."' class='center {$data['criterio_padre']}' id='criterio_".$data['criterio_id']."_valor_1' style='cursor: pointer; background-color:".$color_criterio.";font-weight:bold; border: 1px solid #c8c8c8;' onclick='valorElegido(1,".$data['criterio_id'].", \"".$data['num_ids_criterio']."\", \"".$data['criterio_padre']."\")'></td>";
            echo "<td class='center {$data['criterio_padre']}' id='criterio_".$data['criterio_id']."_valor_2' style='cursor: pointer; background-color:".$color_criterio.";font-weight:bold; border: 1px solid #c8c8c8;' onclick='valorElegido(2,".$data['criterio_id'].", \"".$data['num_ids_criterio']."\", \"".$data['criterio_padre']."\")'></td>";
            echo "<td class='center {$data['criterio_padre']}' id='criterio_".$data['criterio_id']."_valor_3' style='cursor: pointer; background-color:".$color_criterio.";font-weight:bold; border: 1px solid #c8c8c8;' onclick='valorElegido(3,".$data['criterio_id'].", \"".$data['num_ids_criterio']."\", \"".$data['criterio_padre']."\")'></td>";
            echo "<td class='center {$data['criterio_padre']}' id='criterio_".$data['criterio_id']."_valor_4' style='cursor: pointer; background-color:".$color_criterio.";font-weight:bold; border: 1px solid #c8c8c8;' onclick='valorElegido(4,".$data['criterio_id'].", \"".$data['num_ids_criterio']."\", \"".$data['criterio_padre']."\")'></td>";
            echo "<td id='criterio_".$data['criterio_id']."_valor_5' title='".$data['denom_Excelente']."' class='center puntuable {$data['criterio_padre']}' style='cursor: pointer; background-color:".$color_criterio.";font-weight:bold; border: 1px solid #c8c8c8;' onclick='valorElegido(5,".$data['criterio_id'].", \"".$data['num_ids_criterio']."\", \"".$data['criterio_padre']."\")'></td>";

            if($cambio_criterio_padre!=$data['criterio_padre']){
               $cambio_criterio_padre=$data['criterio_padre'];
               echo"<td rowspan='".$num."' id='criterio_padre_".$data['criterio_padre']."' class='center valor' style='background-color:".$color_criterio.";font-weight:bold; border: 1px solid #c8c8c8;'></td>";
            }
        echo "</tr>";  
    }
    echo "</tbody>";
    echo "</table>";

    if(isset($_GET['id'])){
        $ID = $_GET['id'];
        $query="select sv.*, 
            criterio.*, 
            v.fecha, 
            v.comentario, 
            v.projecttasks_id as contrato_id, 
            v.evaluacion_final as evaluacion_final, 
            (select GROUP_CONCAT(id ORDER BY id asc) 
                    from glpi_plugin_comproveedores_criterios as criterio3 
                where criterio3.tipo_especialidad=3 and criterio3.criterio_padre=criterio.criterio_padre) as num_ids_criterio, 
            (select v2.id 
                    from glpi_plugin_comproveedores_valuations as v2 
                where v2.projecttasks_id=v.projecttasks_id order by v2.id desc limit 1) as id_ultima_evaluacion 
            from glpi_plugin_comproveedores_subvaluations as sv 
                    left join glpi_plugin_comproveedores_criterios as criterio on sv.criterio_id=criterio.id 
                    left join glpi_plugin_comproveedores_valuations as v on v.id=sv.valuation_id 
            where sv.valuation_id={$ID} order by criterio_id asc";                   

        $result = $DB->query($query);
        //echo $query;
        //Creamos un script donde se cagarán los valores de la consulta
        echo "<script type='text/javascript'>      
               $( function() {";
                        $evaluacion_final = 0;
                        while ($data = $DB->fetch_array($result)) {
                            $valoracion_id                  = $data['valuation_id'];
                            $contrato_id                    = $data['contrato_id'];
                            $fecha                          = $data['fecha'];
                            $comentario                     = $data['comentario'];
                            $ultima_evaluacion_guardada_id  = $data['id_ultima_evaluacion'];

                            echo "valorElegido(".$data['valor'].", ".$data['criterio_id'].", \"".$data['num_ids_criterio']."\",\"".$data['criterio_padre']."\");"; 

                            //Si la evaluación tiene marcado el check de ultima evaluación, que pueda quitarlo y sequir creardo evaluaciones.
                            $evaluacion_final = $data['evaluacion_final'];
                        }
                        //Es la evaluación final(marco el check de evaluación final)
                        if($evaluacion_final == 1 ){
                                echo "$('#visualizar_ultima_eval').attr('style', 'display: -webkit-box; font-size: 14px;');";
                                echo "$('#evaluacion_final').attr('checked', true);";
                        }else{ 
                            //Es la última evaluación (pero no marco el check de evaluación final, al modificar pueda ponerlo como evaluación final)
                            if($ultima_evaluacion_guardada_id == $valoracion_id){
                                    echo "$('#visualizar_ultima_eval').attr('style', 'display:-webkit-box; font-size: 14px;');";
                                    echo "$('#evaluacion_final').attr('checked', false);";
                            } 
                        }
                        //Les pasamos el valor a los input de fecha de valoración
                        //echo"$('#fecha_valoracion').find('input[name=_fecha]').val('".$fecha."');";    
                        //echo"$('#fecha_valoracion').find('input[name=fecha]').val('".$fecha."');";    
                        echo "$('#comentario').html('".$comentario."');";
                echo "});


        </script>";
    }

    
    echo "<tr style='background-color: #ccc; border: none; border: none; text-align: left; padding-left: 20px;'>";
        echo "<td colspan='1' style='padding: 10px; border: none; text-align: right;'><input id='regresar' type='submit' title='regresar a la lista' class='boton_anterior' style='margin-right: 15px;' onClick='location.reload();' value=''/></td>";    
        echo "<td colspan='8' style='padding: 10px; border: none; text-align: left;'>";
        if(isset($_GET['id'])){
            echo "<input type='submit' title='grabar' onclick='guardarYModificarSubValoracion(\"{$total_ids_subcriterios}\",".$contrato_id.", ".$valoracion_id.",".$IdTipoEspecialidad.", \"update_valoracion\")' class='boton_grabar' style='margin-right: 15px;' value=''/>";
        }else{
            echo "<input type='submit' title='grabar' onclick='guardarYModificarSubValoracion(\"{$total_ids_subcriterios}\",".$contrato_id.", ".$valoracion_id.",".$IdTipoEspecialidad.", \"add_valoracion\")'     class='boton_grabar' style='margin-right: 15px;' value=''/>";      
        }           
        echo "</td>";
    echo "</tr>"; 
    echo "</table></div>"; 


                                
                                
    function consultaAjax(){
    GLOBAL $DB,$CFG_GLPI;
        $resultado="<script type='text/javascript'>            

            var arrayValoracion = [];
            for ( var i = 1; i <=3; i++ ) {
                arrayValoracion[i] = []; 
            }
            var arraySubValoracionValor = [];
            
            function valorElegido(valor_criterio, tipo_criterio, num_subcriterios, criterio_padre){
                var aux = '#chk_'+criterio_padre;
                if($(aux).prop('checked')){
                    for(i=1; i<=5; i++){
                        if(valor_criterio == i){
                            $('#criterio_'+tipo_criterio+'_valor_'+i).css({
                                'background-image':'url(".$CFG_GLPI["root_doc"]."/pics/valoracion_'+valor_criterio+'.png)',
                                'background-repeat':'no-repeat',
                                'background-size': '24px',
                                'background-position':'center'});
                            $('#criterio_'+tipo_criterio+'_valor_'+i).html(valor_criterio);
                        }
                        else{
                            $('#criterio_'+tipo_criterio+'_valor_'+i).css({'background-image':'none'});
                            $('#criterio_'+tipo_criterio+'_valor_'+i).html('');
                        }                                  
                    }    
                    arraySubValoracionValor[tipo_criterio] = valor_criterio;
                    totalSubvaloracion(num_subcriterios, criterio_padre);
                    $('#criterio_padre_'+criterio_padre).css('opacity','1');
                }else{
                    arraySubValoracionValor[tipo_criterio] = 0;
                    $('#criterio_padre_'+criterio_padre).html(0);
                    $('#criterio_padre_'+criterio_padre).css('opacity','0');
                }
                if(valor_criterio==0){
                    $(aux).prop('checked', false);
                }
            }

            //Comprobamos que todo los valores de un criterio esten rellenos para sacar el total
            function totalSubvaloracion(num_subcriterios, criterio_padre){
                var arrayIdsCriterio = num_subcriterios.split(',');
                var inicio = 0;
                var fin = arrayIdsCriterio.length - 1;
                var total_criterio   = 0;
                var acum = 0;

                //Calculamos el total para el criterio_padre elegido
                for(i=inicio; i<=fin; i++){
                    if($('#criterio_'+arrayIdsCriterio[i]+'_porcentaje').text()!=''){
                        porcentaje  = Number($('#criterio_'+arrayIdsCriterio[i]+'_porcentaje').text().replace('%', '')) / 100;
                    }else{
                        porcentaje  = 1;
                    }
                    acum  = acum + (arraySubValoracionValor[arrayIdsCriterio[i]] * porcentaje);
                }
                total_criterio  = Math.round(acum/(i));
                $('#criterio_padre_'+criterio_padre).html(total_criterio);
            }

            //para transformar la fecha en un valor grabable en la base de datos en formato americano
            function getfecha(f){
                var aux = '';
                if(f.length>0){
                    aux = f.substr(6,4)+'-'+f.substr(3,2)+'-'+f.substr(0,2);
                }
                return aux;
            }
            

            function guardarYModificarSubValoracion(total_ids_subcriterios, contrato_id, valoracion_id, tipo_especialidad, metodo){
                //Comprobamos que esten todo los valores rellenos
                var valores_completados       = true;
                var arrayTotalIdsSubcriterios = total_ids_subcriterios.split(',');

                //Guardamos los valores de los comentarios
                if($('#evaluacion_final').prop('checked')) {	
                    eval_final = 1;
                }else{	
                    eval_final = 0;
                }
                var auxDate = new Date($('#fechaValoracion').val());
                var dia = auxDate.getDate();
                var mes = auxDate.getMonth()+1;
                var anio = auxDate.getFullYear();
                var fecha = anio+'-'+mes+'-'+dia;

                //alert(fecha);
                var cv_id = $('#evaluacion').find('input[name=cv_id]').val();
                
                //recojo indicadores padre
                var calidad                                     = $('#criterio_padre_calidad').html();
                var planificacion                               = $('#criterio_padre_planificacion').html();
                var costes                                      = $('#criterio_padre_costes').html();
                var cultura_empresarial                         = $('#criterio_padre_cultura_empresarial').html();
                var gestion_de_suministros_y_subcontratistas    = $('#criterio_padre_gestion_de_suministros_y_subcontratistas').html();
                var seguridad_y_salud_y_medioambiente           = $('#criterio_padre_seguridad_y_salud_y_medioambiente').html();
                var bim                                         = $('#criterio_padre_bim').html();
                var certificacion_medioambiental                = $('#criterio_padre_certificacion_medioambiental').html();
                var proyecto_basico                             = $('#criterio_padre_proyecto_basico').html();
                var proyecto_de_ejecucion                       = $('#criterio_padre_proyecto_de_ejecucion').html();
                var capacidad_de_la_empresa                     = $('#criterio_padre_capacidad_de_la_empresa').html();
                var colaboradores                               = $('#criterio_padre_colaboradores').html();
                var capacidad                                   = $('#criterio_padre_capacidad').html();                
                var actitud                                     = $('#criterio_padre_actitud').html();      


                //Guardamos las subvaloraciones y valoraciones
                var parametros = {
                    'metodo': metodo,
                    'contrato_id': contrato_id,
                    'valoracion_id': valoracion_id,
                    'cv_id': cv_id,
                    'fecha': fecha, 
                    'calidad': calidad,
                    'planificacion': planificacion,
                    'costes': costes,
                    'cultura_empresarial': cultura_empresarial,
                    'gestion_de_suministros_y_subcontratistas': gestion_de_suministros_y_subcontratistas,
                    'seguridad_y_salud_y_medioambiente': seguridad_y_salud_y_medioambiente,
                    'bim': bim,
                    'certificacion_medioambiental': certificacion_medioambiental,
                    'proyecto_basico': proyecto_basico,
                    'proyecto_de_ejecucion': proyecto_de_ejecucion,
                    'capacidad_de_la_empresa': capacidad_de_la_empresa,
                    'colaboradores': colaboradores,
                    'capacidad': capacidad,
                    'actitud': actitud,
                    'guardarSubvaloraciones': '',
                    'comentario': $('#comentario').val(),
                    'tipo_especialidad': tipo_especialidad,
                    'arraySubValoracionValor': arraySubValoracionValor,                                            
                    'eval_final': eval_final
                };
                $.ajax({ 
                    type: 'GET',
                    data: parametros,                  
                    url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/front/valuation.form.php',                    
                    success:function(){
                        location.reload();
                    },
                    error: function(result) {
                        alert('Data not found');
                    }
                });
            }   
            
            $( document ).ready(function() {
                $('.hasDatepicker').css('font-size', '14px');
            });

            function limpiarValoresPadre(strPadre){
                //recopilo todos los id de los subcriterios del padre seleccionado en strIdSubcriterios
                var aux = '.cls'+strPadre;
                var strIdSubcriterios = '';
                var ident = '';
                $(aux).each(function(){
                    ident = $(this).val();
                    if(strIdSubcriterios == '') {
                        strIdSubcriterios = ident;
                    }else{
                        strIdSubcriterios = strIdSubcriterios+','+ident;
                    }
                }); 
                $(aux).each(function(){
                    ident = $(this).val();
                    valorElegido(0, ident, strIdSubcriterios, strPadre);
                });
                
            }

            $('.cuadrochequeo').on('click', function(){
                var padre = $(this).attr('id').replace('chk_','');
                if(this.checked){
                    aux = '#criterio_padre_'+padre;
                   $(aux).css('opacity','1');
                }else{
                   aux = '#criterio_padre_'+padre;
                   $(aux).html('0');
                   $(aux).css('opacity','0');
                   aux = '.'+padre;
                   $(aux).css('background-image',''); 
                   $(aux).html('');
                   limpiarValoresPadre(padre);                 
                }
                
            });

        </script>";
        
                
        return $resultado;
    }   
