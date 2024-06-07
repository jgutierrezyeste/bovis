<?php

use Glpi\Event;

GLOBAL $DB,$CFG_GLPI;

include ("../../../inc/includes.php");

$preseleccion_ids=$_GET['ids'];
$preseleccion_ids=str_replace("-", ",", $preseleccion_ids);
$preseleccion_ids= substr($preseleccion_ids, 0, -1);

$where='where proveedor.id in('.$preseleccion_ids.') ';

$where=$where." group by proveedor.name order by proveedor.name desc";

$contrato_id=$_GET['contrato_id'];
//Creamos la consulta y añadimos el where a la consulta
$query ="select 
(select name from glpi_projecttasks where id=".$contrato_id.") as nombre_contrato,
proveedor.id as supplier_id,
proveedor.name, 
GROUP_CONCAT(distinct especialidad.name SEPARATOR '\n')  as especialidad, 
facturacion.facturacion, 
proveedor.cv_id,
(select contrato1.tipo_especialidad from glpi_projecttasks as contrato1 where id=".$contrato_id.") as tipo_especialidad, 
ROUND(Sum(valoracion.calidad)/count(valoracion.calidad),2) as calidad, 
ROUND(Sum(valoracion.planificacion)/count(valoracion.planificacion),2) as planificacion,
ROUND(Sum(valoracion.costes)/count(valoracion.costes),2) as costes, 
ROUND(Sum(valoracion.cultura_empresarial)/count(valoracion.cultura_empresarial),2) as cultura_empresarial, 
ROUND(Sum(valoracion.gestion_de_suministros_y_subcontratistas)/count(valoracion.gestion_de_suministros_y_subcontratistas),2) as gestion_de_suministros_y_subcontratistas, 
ROUND(Sum(valoracion.seguridad_y_salud_y_medioambiente)/count(valoracion.seguridad_y_salud_y_medioambiente),2) as seguridad_y_salud_y_medioambiente,
ROUND(Sum(valoracion.bim)/count(valoracion.bim),2) as bim,
ROUND(Sum(valoracion.certificacion_medioambiental)/count(valoracion.certificacion_medioambiental),2) as certificacion_medioambiental,
ROUND(Sum(valoracion.proyecto_basico)/count(valoracion.proyecto_basico),2) as proyecto_basico, 
ROUND(Sum(valoracion.proyecto_de_ejecucion)/count(valoracion.proyecto_de_ejecucion),2) as proyecto_de_ejecucion,
ROUND(Sum(valoracion.capacidad_de_la_empresa)/count(valoracion.capacidad_de_la_empresa),2) as capacidad_de_la_empresa, 
ROUND(Sum(valoracion.colaboradores)/count(valoracion.colaboradores),2) as colaboradores, 
ROUND(Sum(valoracion.capacidad)/count(valoracion.capacidad),2) as capacidad, 
ROUND(Sum(valoracion.actitud)/count(valoracion.actitud),2) as actitud

from glpi_suppliers as proveedor 
LEFT JOIN glpi_plugin_comproveedores_annualbillings as facturacion on facturacion.cv_id=proveedor.cv_id and YEAR(facturacion.anio)=YEAR(now()) 
 LEFT JOIN glpi_projecttaskteams as projecttaskteams on projecttaskteams.items_id=proveedor.id
 LEFT JOIN glpi_projecttasks as contrato on contrato.id=projecttaskteams.projecttasks_id and contrato.tipo_especialidad=(select tipo_especialidad from glpi_projecttasks contrato_principal where id=".$contrato_id.")
 LEFT JOIN glpi_plugin_comproveedores_valuations as valoracion on valoracion.cv_id=proveedor.cv_id and valoracion.projecttasks_id=contrato.id
 LEFT JOIN glpi_plugin_comproveedores_listspecialties as lista_especialidades on lista_especialidades.cv_id=proveedor.cv_id 
 LEFT JOIN glpi_plugin_comproveedores_specialties as especialidad on especialidad.id=lista_especialidades.plugin_comproveedores_specialties_id AND lista_especialidades.plugin_comproveedores_roltypes_id=(select tipo_especialidad from glpi_projecttasks contrato_principal where id=".$contrato_id.")
 ".$where;

$result = $DB->query($query);


           $html="<div id='tabla_seleccion_proveedores' align='center'><table class='tab_cadre_fixehov' style='border-spacing: 0px;'>";
              
	$html.="<tr class='tab_bg_2 tab_cadre_fixehov nohover'>";
              
                $html.="<th class='center' style='border: 1px solid #BDBDDB; background-color: #F8F8F8; padding: 10px 5px; text-align: center;' colspan='14'>Lista de proveedores preseleccionados</th></tr>";
                
        
                 $visualizar_cabecera=true;
                while ($data=$DB->fetch_array($result)) {
                        
                        if($visualizar_cabecera){
                                
                                 $html.="<tr><th class='center' style='border: 1px solid #BDBDDB; background-color: #F8F8F8; padding: 10px 5px; text-align: center;' colspan='14'>".$data['nombre_contrato']."</th></tr>";
                
                                //solo visualizamos 1 vez la cabecera
                                $visualizar_cabecera=false;
                                $html.="<tr>";
                                
                                //Eliminación al visualizar la preselección
                                $html.="<th class='center' style='border: 1px solid #BDBDDB; background-color: #F8F8F8; padding: 10px 5px; text-align: center;' >".__('Nombre')."</th>";
                                $html.="<th class='center' style='border: 1px solid #BDBDDB; background-color: #F8F8F8; padding: 10px 5px; text-align: center;' >".__('Especialidad')."</th>";
                                $html.="<th class='center' style='border: 1px solid #BDBDDB; background-color: #F8F8F8; padding: 10px 5px; text-align: center;' >".__('CV')."</th>";
                                $html.="<th class='center' style='border: 1px solid #BDBDDB; background-color: #F8F8F8; padding: 10px 5px; text-align: center;' >".__('FACTURACIÓN')."</th>";

                                ////////Criterios Contratista///////
                                 if($data["tipo_especialidad"]==2){
                                        $html.= "<th class='center' style='border: 1px solid #BDBDDB; background-color: #F8F8F8; padding: 10px 5px; text-align: center;' >".__('Q')."</th>";
                                        $html.="<th class='center' style='border: 1px solid #BDBDDB; background-color: #F8F8F8; padding: 10px 5px; text-align: center;' >".__('PLZ')."</th>";
                                        $html.="<th class='center' style='border: 1px solid #BDBDDB; background-color: #F8F8F8; padding: 10px 5px; text-align: center;' >".__('COST')."</th>";
                                        $html.="<th class='center' style='border: 1px solid #BDBDDB; background-color: #F8F8F8; padding: 10px 5px; text-align: center;' >".__('CULT')."</th>";
                                        $html.="<th class='center' style='border: 1px solid #BDBDDB; background-color: #F8F8F8; padding: 10px 5px; text-align: center;' >".__('SUBC')."</th>";
                                        $html.= "<th class='center' style='border: 1px solid #BDBDDB; background-color: #F8F8F8; padding: 10px 5px; text-align: center;' >".__('SyS')."</th>";
                                        $html.="<th class='center' style='border: 1px solid #BDBDDB; background-color: #F8F8F8; padding: 10px 5px; text-align: center;' >".__('BIM')."</th>";
                                        $html.= "<th class='center' style='border: 1px solid #BDBDDB; background-color: #F8F8F8; padding: 10px 5px; text-align: center;' >".__('CERT')."</th>";

                                }else{
                                    ////////Criterios Servicios Profesionales ///////      
                                        $html.= "<th class='center' style='border: 1px solid #BDBDDB; background-color: #F8F8F8; padding: 10px 5px; text-align: center;' >".__('PROY BÁSICO')."</th>";
                                        $html.="<th class='center' style='border: 1px solid #BDBDDB; background-color: #F8F8F8; padding: 10px 5px; text-align: center;' >".__('PROY EJECUCIÓN')."</th>";
                                        $html.="<th class='center' style='border: 1px solid #BDBDDB; background-color: #F8F8F8; padding: 10px 5px; text-align: center;' >".__('PROY EJECUCIÓN')."</th>";
                                        $html.="<th class='center' style='border: 1px solid #BDBDDB; background-color: #F8F8F8; padding: 10px 5px; text-align: center;' >".__('COLABORADOR')."</th>";
                                        $html.="<th class='center' style='border: 1px solid #BDBDDB; background-color: #F8F8F8; padding: 10px 5px; text-align: center;' >".__('CAPACIDAD')."</th>";
                                        $html.= "<th class='center' style='border: 1px solid #BDBDDB; background-color: #F8F8F8; padding: 10px 5px; text-align: center;' >".__('ACTITUD')."</th>";
                                        $html.="<th class='center' style='border: 1px solid #BDBDDB; background-color: #F8F8F8; padding: 10px 5px; text-align: center;' >".__('BIM')."</th>";
                                }

                                $html.="</tr>";
                        }
                                      
                        //Añadimos los id de los proveedores para la preselección
                        $preselecionIds=$preselecionIds.$data["supplier_id"]."-";
            
                            $html.="<tr class='tab_bg_2'>";
                                    
                                       $html.="<td class='center' style=' border: 1px solid #BDBDDB;'>".$data["name"]."</td>";   
                                       if(!empty($data['especialidad'])){
                                             $html.="<td class='center' style=' border: 1px solid #BDBDDB;'>".$data['especialidad']."</td>";
                                       }else{
                                             $html.="<td class='center' style=' border: 1px solid #BDBDDB;'></td>";
                                       }
                                       if(!empty($data['cv_id'])){
                                           $html.="<td class='center' style='text-align: center; border: 1px solid #BDBDDB;'><img  style='vertical-align:middle; margin: 10px 0px;' src='../../../pics/CheckBoxTrue.png'></td>";
                                       }
                                       else{
                                           $html.="<td class='center' style='text-align: center; border: 1px solid #BDBDDB;'><img  style='vertical-align:middle; margin: 10px 0px;' src='../../../pics/CheckBoxFalse.png'></td>";
                                       }

                                       $facturacion=substr(number_format($data['facturacion'], 0, '', '.'),0,strlen(number_format($data['facturacion'], 0, '', '.')));

                                      $html.="<td class='center' style=' border: 1px solid #BDBDDB;'>".$facturacion."</td>";

                                       ////////Criterios Contratista///////
                                       if($data["tipo_especialidad"]==2){
                                                if(!empty($data['calidad'])){
                                                   $html.="<td class='center' style='text-align: center; border: 1px solid #BDBDDB; font-weight: bold; color: black ; text-shadow:  2 white; background-image: url(../../../pics/valoracion_".getColorValoracion($data['calidad']).".png); background-repeat: no-repeat;  background-position: center;'>".$data['calidad']."</td>";
                                                }
                                                else{
                                                    $html.="<td class='center' style=' border: 1px solid #BDBDDB;'></td>";
                                                }
                                                 if(!empty($data['planificacion'])){
                                                    $html.="<td class='center' style='text-align: center; border: 1px solid #BDBDDB; font-weight: bold; color: black ; text-shadow:  2 white; background-image: url(../../../pics/valoracion_".getColorValoracion($data['planificacion']).".png); background-repeat: no-repeat;  background-position: center;'>".$data['planificacion']."</td>";
                                                }
                                                else{
                                                    $html.="<td class='center' style=' border: 1px solid #BDBDDB;'></td>";
                                                }
                                                 if(!empty($data['costes'])){
                                                    $html.="<td class='center' style='text-align: center; border: 1px solid #BDBDDB; font-weight: bold; color: black ; text-shadow:  2 white; background-image: url(../../../pics/valoracion_".getColorValoracion($data['costes']).".png); background-repeat: no-repeat;  background-position: center;'>".$data['costes']."</td>";
                                                }
                                                else{
                                                    $html.="<td class='center' style=' border: 1px solid #BDBDDB;'></td>";
                                                }
                                                 if(!empty($data['cultura_empresarial'])){
                                                    $html.="<td class='center' style='text-align: center; border: 1px solid #BDBDDB; font-weight: bold; color: black ; text-shadow:  2 white; background-image: url(../../../pics/valoracion_".getColorValoracion($data['cultura_empresarial']).".png); background-repeat: no-repeat;  background-position: center;'>".$data['cultura_empresarial']."</td>";
                                                }
                                                else{
                                                    $html.="<td class='center' style=' border: 1px solid #BDBDDB;'></td>";
                                                }
                                                 if(!empty($data['gestion_de_suministros_y_subcontratistas'])){
                                                     $html.="<td class='center' style='text-align: center; border: 1px solid #BDBDDB; font-weight: bold; color: black ; text-shadow:  2 white; background-image: url(../../../pics/valoracion_".getColorValoracion($data['gestion_de_suministros_y_subcontratistas']).".png); background-repeat: no-repeat;  background-position: center;'>".$data['gestion_de_suministros_y_subcontratistas']."</td>";
                                                }
                                                else{
                                                    $html.="<td class='center' style=' border: 1px solid #BDBDDB;'></td>";
                                                }
                                                if(!empty($data['seguridad_y_salud_y_medioambiente'])){
                                                    $html.="<td class='center' style='text-align: center; border: 1px solid #BDBDDB; font-weight: bold; color: black ; text-shadow:  2 white; background-image: url(../../../pics/valoracion_".getColorValoracion($data['seguridad_y_salud_y_medioambiente']).".png); background-repeat: no-repeat;  background-position: center;'>".$data['seguridad_y_salud_y_medioambiente']."</td>";
                                                }
                                                else{
                                                    $html.="<td class='center' style=' border: 1px solid #BDBDDB;'></td>";
                                                }
                                                 if(!empty($data['bim'])){
                                                    $html.="<td class='center' style='text-align: center; border: 1px solid #BDBDDB; font-weight: bold; color: black ; text-shadow:  2 white; background-image: url(../../../pics/valoracion_".getColorValoracion($data['bim']).".png); background-repeat: no-repeat;  background-position: center;'>".$data['bim']."</td>";
                                                }
                                                else{
                                                    $html.="<td class='center' style=' border: 1px solid #BDBDDB;'></td>";
                                                }
                                                 if(!empty($data['certificacion_medioambiental'])){
                                                    $html.="<td class='center' style='text-align: center; border: 1px solid #BDBDDB; font-weight: bold; color: black ; text-shadow:  2 white; background-image: url(../../../pics/valoracion_".getColorValoracion($data['certificacion_medioambiental']).".png); background-repeat: no-repeat;  background-position: center;'>".$data['certificacion_medioambiental']."</td>";
                                                }
                                                else{
                                                    $html.="<td class='center' style=' border: 1px solid #BDBDDB;'></td>";
                                                }
                                                
                                        ///////Criterios Servicios Profesionales ///////          
                                        }else{
                                                 if(!empty($data['proyecto_basico'])){
                                                   $html.="<td class='center' style='text-align: center; border: 1px solid #BDBDDB; font-weight: bold; color: black ; text-shadow:  2 white; background-image: url(../../../pics/valoracion_".getColorValoracion($data['proyecto_basico']).".png); background-repeat: no-repeat;  background-position: center;'>".$data['proyecto_basico']."</td>";
                                                }
                                                else{
                                                    $html.="<td class='center' style=' border: 1px solid #BDBDDB;'></td>";
                                                }
                                                 if(!empty($data['proyecto_de_ejecucion'])){
                                                    $html.="<td class='center' style='text-align: center; border: 1px solid #BDBDDB; font-weight: bold; color: black ; text-shadow:  2 white; background-image: url(../../../pics/valoracion_".getColorValoracion($data['proyecto_de_ejecucion']).".png); background-repeat: no-repeat;  background-position: center;'>".$data['proyecto_de_ejecucion']."</td>";
                                                }
                                                else{
                                                    $html.="<td class='center' style=' border: 1px solid #BDBDDB;'></td>";
                                                }
                                                 if(!empty($data['capacidad_de_la_empresa'])){
                                                    $html.="<td class='center' style='text-align: center; border: 1px solid #BDBDDB; font-weight: bold; color: black ; text-shadow:  2 white; background-image: url(../../../pics/valoracion_".getColorValoracion($data['capacidad_de_la_empresa']).".png); background-repeat: no-repeat;  background-position: center;'>".$data['capacidad_de_la_empresa']."</td>";
                                                }
                                                else{
                                                    $html.="<td class='center' style=' border: 1px solid #BDBDDB;'></td>";
                                                }
                                                 if(!empty($data['colaboradores'])){
                                                    $html.="<td class='center' style='text-align: center; border: 1px solid #BDBDDB; font-weight: bold; color: black ; text-shadow:  2 white; background-image: url(../../../pics/valoracion_".getColorValoracion($data['colaboradores']).".png); background-repeat: no-repeat;  background-position: center;'>".$data['colaboradores']."</td>";
                                                }
                                                else{
                                                    $html.="<td class='center' style=' border: 1px solid #BDBDDB;'></td>";
                                                }
                                                 if(!empty($data['capacidad'])){
                                                     $html.="<td class='center' style='text-align: center; border: 1px solid #BDBDDB; font-weight: bold; color: black ; text-shadow:  2 white; background-image: url(../../../pics/valoracion_".getColorValoracion($data['capacidad']).".png); background-repeat: no-repeat;  background-position: center;'>".$data['capacidad']."</td>";
                                                }
                                                else{
                                                    $html.="<td class='center' style=' border: 1px solid #BDBDDB;'></td>";
                                                }
                                                if(!empty($data['actitud'])){
                                                    $html.="<td class='center' style='text-align: center; border: 1px solid #BDBDDB; font-weight: bold; color: black ; text-shadow:  2 white; background-image:url(../../../pics/valoracion_".getColorValoracion($data['actitud']).".png); background-repeat: no-repeat;  background-position: center;'>".$data['actitud']."</td>";
                                                }
                                                else{
                                                    $html.="<td class='center' style=' border: 1px solid #BDBDDB;'></td>";
                                                }
                                                 if(!empty($data['bim'])){
                                                    $html.="<td class='center' style='text-align: center; border: 1px solid #BDBDDB; font-weight: bold; color: black ; text-shadow:  2 white; background-image: url(../../../pics/valoracion_".getColorValoracion($data['bim']).".png); background-repeat: no-repeat;  background-position: center;'>".$data['bim']."</td>";
                                                }
                                                else{
                                                    $html.="<td class='center' style=' border: 1px solid #BDBDDB;'></td>";
                                                }
                                        }

                           $html.="</tr>";
                        
	}
	
	$html.="</table></div>";

        $nombre_pdf="Lista de Proyectos.pdf";
        //exportamos el contenido de la variable $html a pdf, y el pdf tendra el nombre de $nombre_pdf
       include ("../../../dompdf/output.php");
          function getColorValoracion($valor){
	           
            switch ($valor) {
                case $valor<=1:

                        $color=1;
                    break;
                case $valor<=2 && $valor>1:

                        $color=2;
                   break;
                case $valor<=3 && $valor>2:

                        $color=3;
                    break;
                case $valor<=4 && $valor>3:

                        $color=4;
                    break;
                case $valor<=5 && $valor>4:

                        $color=5;
                    break;
                default:
                    break;
            }

	return $color;
        }