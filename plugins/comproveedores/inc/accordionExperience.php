<?php

use Glpi\Event;

include ("../../../inc/includes.php");

GLOBAL $DB,$CFG_GLPI;

$objExperiencia=new PluginComproveedoresExperience;

echo consultaJquery();
$fkcv = $_GET['cv_id'];

if($fkcv == 0){$fkcv = -1;}
echo "<div id='accordion'>";

	///////Intervencion Bovis			 	
        $cad1= "select count(*) as numero, intervencion_bovis as bovis from glpi_plugin_comproveedores_experiences where is_deleted=0 and cv_id={$fkcv} and intervencion_bovis=1 ";
	$res1 = $DB->query($cad1);
        $numero = 0;
	foreach ($res1 as $f1){
            if ($f1['numero']>0){
                $numero = $f1['numero'];
            }
	}
        
        echo "<h3 name='intervencion_bovis' class='tipo_experiencia tipo_experiencia_intervencion_bovis' style='font-weight: bold; text-align:left;'>Intervención Bovis (".$numero.")</h3>";
        echo "<div style='padding:10px; min-height: 260px; max-height: 260px;' class='tipo_experiencia_intervencion_bovis'>";  
        echo "</div>";
        
	//////Tipos de experiencias
	$cadena= "select t.descripcion, t.id, (select count(*) from glpi_plugin_comproveedores_experiences as e
 			where e.plugin_comproveedores_experiencestypes_id = t.id && cv_id={$fkcv} and is_deleted=0 and e.plugin_comproveedores_experiencestypes_id!=0) as numero 
 					 from glpi_plugin_comproveedores_experiencestypes as t ";
        //echo $cadena;
	$result = $DB->query($cadena);
	foreach ($result as $fila){
            if ($fila['numero']>0){
            echo "<h3 name='".$fila['id']."' class='tipo_experiencia tipo_experiencia_".$fila['id']."'  style='font-weight: bold;'>";
            echo $fila['descripcion']." (".$fila['numero'].")";
            echo "</h3>";
            echo "<div style='padding:10px; min-height: 260px; max-height: 260px;' class='tipo_experiencia_".$fila['id']."'>";  
            echo "</div>";
            }
	}
	              

	//////Sin experiencias    
	$cadena= "select count(*) as numero from glpi_plugin_comproveedores_experiences 
                where is_deleted=0 and cv_id={$fkcv} and plugin_comproveedores_experiencestypes_id=0 and intervencion_bovis=0 ";
	$result = $DB->query($cadena);
	foreach ($result as $fila){
            if ($fila['numero']>0){
            echo "<h3 name='sin_experiencia' class='tipo_experiencia tipo_experiencia_sin_experiencia' style='font-weight: bold;'>Sin Experiencias (".$fila['numero'].")</h3>";
            echo "<div style='padding:0px; min-height: 260px; max-height: 260px;' class='tipo_experiencia_sin_experiencia'>";  
            echo "</div>";	
            }
	}
echo"</div>";	
			


function consultaJquery(){

    GLOBAL $CFG_GLPI;

    $consulta="<script type='text/javascript'>

            $(document).ready(function() {

                //Añadimos la función acordeon a las listas 
                $( '#accordion' ).accordion({collapsible:true, active: false});

                //Añadimos onclick a las lista para que se cargen a elegirlas
                $('h3[class*=tipo_experiencia_]').on('click', function() {
                    actualizarLista($(this).attr('name'));	
                });

            });

    </script>";

    return $consulta;

}
