<?php

use Glpi\Event;
include ("../../../inc/includes.php");
GLOBAL $DB,$CFG_GLPI;

$objExperiencia=new PluginComproveedoresExperience;
$fkcv = $_GET['cv_id'];

if($fkcv == 0){$fkcv = -1;}
	
if($_GET['tipo']=='intervencion_bovis'){
    $query ="SELECT * FROM glpi_plugin_comproveedores_experiences WHERE cv_id=".$fkcv." and intervencion_bovis=1 and is_deleted=0 order by id desc";
}elseif($_GET['tipo']=='sin_experiencia'){
    $query ="SELECT * FROM glpi_plugin_comproveedores_experiences WHERE cv_id=".$fkcv." and intervencion_bovis=0 and plugin_comproveedores_experiencestypes_id=0 and is_deleted=0 order by id desc";
}else{
    $query ="SELECT * FROM glpi_plugin_comproveedores_experiences WHERE cv_id=".$fkcv." and plugin_comproveedores_experiencestypes_id='".$_GET['tipo']."' and is_deleted=0  order by id desc";
}
$profile_id = $_GET['profile_id'];

//echo consultaAjaxListExperiencia();
$result = $DB->query($query);
//Ocultar lista, si no existe ninguna expeciencia
//echo "<div class='actualizarLista' align='center' style='padding:0px;'>";
echo "<table id='data_table_".$_GET['tipo']."' class='display compact dataTable no-footer' style='font-size: 10px;width:100%; float:left; position: relative;'>";
$numero_registros=1;

echo "<thead>";
    echo "<tr>";
        echo "<th>Proyecto</th>";
        echo "<th>Estado</th>";
        if($_GET['tipo']=='intervencion_bovis'){
            echo "<th>Exper.</th>";
        }
        echo "<th>CCAA</th>";
        echo "<th>Cliente</th>";
        echo "<th>Año</th>";
        echo "<th>Importe</th>";
        echo "<th>Meses</th>";
        echo "<th>BOVIS</th>";
        echo "<th>BIM</th>";
        echo "<th>Bre</th>";
        echo "<th>Le</th>";
        echo "<th>Otr</th>";
        if($profile_id!=14){
        echo "<th>Modif</th>";
        echo "<th>Quit</th>";}
    echo "</tr>";
echo "</thead>";

echo "<tbody>";
while ($data=$DB->fetch_array($result)) {

    echo "<tr id='exper_".$data['id']."' title='{$data['observaciones']}'>";
    echo "<td id='name_{$data['id']}' class='left'>".$data['name']."</td>";
    echo "<td id='estado_{$data['id']}' class='left'>";
    echo "<input type='hidden' id='idestado_{$data['id']}' value='{$data['plugin_comproveedores_experiencesstates_id']}' >";
    echo "<input type='hidden' id='idtype_{$data['id']}' value='{$data['plugin_comproveedores_experiencestypes_id']}' >";  
    echo "<input type='hidden' id='observaciones_{$data['id']}' value='{$data['observaciones']}' >";  
    echo Dropdown::getDropdownName("glpi_plugin_comproveedores_experiencesstates",$data['plugin_comproveedores_experiencesstates_id']);
    echo "</td>";
    if($_GET['tipo']=='intervencion_bovis'){
        echo "<td id='intervencion_bovis_{$data['id']}' class='left'>";
        echo Dropdown::getDropdownName("glpi_plugin_comproveedores_experiencestypes",$data['plugin_comproveedores_experiencestypes_id']);
        echo "</td>";
    }
    echo "<td id='ubicacion_{$data['id']}' class='left'>";
    echo "<input type='hidden' id='idcommunity_{$data['id']}' value='{$data['plugin_comproveedores_communities_id']}' >";
    echo Dropdown::getDropdownName("glpi_plugin_comproveedores_communities",$data['plugin_comproveedores_communities_id']);
    echo "</td>";
    echo "<td id='cliente_{$data['id']}' class='center'>".$data['cliente']."</td>";
    
    $anio = $data['anio'];
    echo "<td id='anio_{$data['id']}' class='center'>".$anio."</td>";
    
    $importe = number_format($data['importe'], 2, ',', '.');
    echo "<td id='importe_{$data['id']}' class='right'>".$importe."</td>";
    echo "<td id='duracion_{$data['id']}' class='center'>".$data['duracion']."</td>";
    echo "<td class='center'>";
    if($data['intervencion_bovis']=='1'){
        echo "<input type='checkbox'  id='checkbox_bovis_{$data['id']}' disabled = 'disabled' checked = 'checked' style='width: 20px; '>";
    }else{
        echo "<input type='checkbox'  id='checkbox_bovis_{$data['id']}' disabled = 'disabled' style='width: 20px;'>";
    }
    echo "</td>";
    echo "<td class='center'>";
    if($data['bim']=='1'){
        echo "<input type='checkbox'  id='checkbox_bim_{$data['id']}' disabled = 'disabled' checked = 'checked' style='width: 20px;'>";
    }else{
        echo "<input type='checkbox'  id='checkbox_bim_{$data['id']}' disabled = 'disabled'  style='width: 20px;'>";
    }
    echo "</td>";
    echo "<td class='center'>";
    if($data['breeam']=='1'){
        echo "<input type='checkbox'  id='checkbox_breeam_{$data['id']}' disabled = 'disabled' checked = 'checked'  style='width: 20px;'>";
    }else{
        echo "<input type='checkbox'  id='checkbox_breeam_{$data['id']}' disabled = 'disabled' style='width: 20px;'>";
    }
    echo "</td>";
    echo "<td class='center'>";
    if($data['leed']=='1'){
        echo "<input type='checkbox'  id='checkbox_leed_{$data['id']}' disabled = 'disabled' checked = 'checked'  style='width: 20px;'>";
    }else{
        echo "<input type='checkbox'  id='checkbox_leed_{$data['id']}' disabled = 'disabled'  style='width: 20px;'>";
    }
    echo "</td>";
    echo "<td class='center'>";
    if($data['otros_certificados']=='1'){									
        echo "<input type='checkbox' id='checkbox_otros_certificados_{$data['id']}' disabled = 'disabled' checked = 'checked' style='width: 20px;'>";
    }else{
        echo "<input type='checkbox' id='checkbox_otros_certificados_{$data['id']}' disabled = 'disabled' style='width: 20px;'>";
    }
    echo "</td>";
    if($profile_id!=14){    
    echo"<td class='center'>";
    echo    "<input type='submit' title='editar' value='' class='boton_editar' onclick='modificar(".$data['id'].");' title='editar' style='background-size: 14px; width: 19px; height: 19px;'/>";
    echo "</td>";
   
    echo "<td class='center'>";
    echo "<input id='quitar_{$data['id']}' title='Quitar acceso' type='submit' class='boton_borrar' value='' name='purge' style='background-size: 14px;width: 19px;height: 19px;'/>";
    echo "</td>";}
    echo "</tr>";
    echo "</form>";
    $numero_registros++;
}
echo "</tbody>";
echo "</table>";


    $js="<script type='text/javascript'>
            $(document).ready(function(){
                
                $('#data_table_".$_GET['tipo']."').DataTable({
                    'scrollY':        '200px',
                    'scrollCollapse': true,
                    'searching': false,
                    'info': false,
                    'paging': false,
                    'language': {'url': '".$CFG_GLPI["root_doc"]."/lib/jqueryplugins/Spanish.json'}                  
                });
                
                $('.boton_borrar').on('click', function(){
                
                    var resp = confirm('¿Realmente desea quitar este elemento?', 'Confirme borrado');
                    if(resp){
                        var id = $(this).attr('id').replace('quitar_', '');
                        var parametros = {'id' : id};

                        $.ajax({  
                            type: 'GET',  
                            async: false,               
                            url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/quitarExperience.php',                    
                            data: parametros, 
                            success:function(data){
                                acordeonExperiencia(parametros);
                            },
                            error: function(result) {
                                alert('Data not found');
                            }
                        });      
                    }
                });
            });

    </script>";

echo $js;	

