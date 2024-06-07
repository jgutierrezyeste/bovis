<?php

use Glpi\Event;

GLOBAL $DB,$CFG_GLPI;

include ("../../../inc/includes.php");


$query ="select 
proyectos.id,
proyectos.name,
proyectos.code,
proyectos.projectstates_id,
(select count(*) from glpi_projecttasks where projects_id=proyectos.id) as numero_paquetes,

(select count(valoracion1.id) as numero 

from glpi_projecttasks as paquetes1 

left join glpi_plugin_comproveedores_valuations as valoracion1 
on valoracion1.projecttasks_id=paquetes1.id

where paquetes1.projects_id=proyectos.id) as numero_evaluaciones,

(select items_id from glpi_projectteams where projects_id=proyectos.id and gerente=1) as usuario_cargo_proyecto 

from glpi_projects  as proyectos where proyectos.id in(".$_GET['id'].") order by proyectos.id desc";

$result = $DB->query($query);
           
           $html= "<div align='center'><table style='border-spacing:0px;'>";
	$html.= "<tr><th colspan='6' style='border: 1px solid #BDBDDB; background-color: #D8D8D8; padding: 10px 5px; text-align: center;'>Lista de proyectos</th></tr>";
	$html.="<br/>";
	$html.="<tr style='background-color: #D8D8D8;'>";
                           
                            $html.= "<th style='border: 1px solid #BDBDDB; font-weight: bold; text-align: left; padding: 10px 5px;'>".__('Nombre')."</th>";
                            $html.="<th style='border: 1px solid #BDBDDB; font-weight: bold; text-align: left; padding: 10px 5px;'>".__('Código del proyecto')."</th>";
                            $html.= "<th style='border: 1px solid #BDBDDB; font-weight: bold; text-align: left; padding: 10px 5px;'>".__('Estado')."</th>";
                            $html.= "<th style='border: 1px solid #BDBDDB; font-weight: bold; text-align: left; padding: 10px 5px;'>".__('Nº Contratos')."</th>";
                            $html.= "<th style='border: 1px solid #BDBDDB; font-weight: bold; text-align: left; padding: 10px 5px;'>".__('Nº Evaluaciones')."</th>";
                            $html.= "<th style='border: 1px solid #BDBDDB; font-weight: bold; text-align: left; padding: 10px 5px;'>".__('Gerente')."</th>";
                   $html.= "</tr>";

	while ($data=$DB->fetch_array($result)) {
                            
                                
                               $html.= "<tr class='tab_bg_2' style='border: 1px solid #BDBDDB;'>";
                                $html.="<td class='center' style='border: 1px solid #BDBDDB; width:350px; padding: 10px 5px;'>".$data["name"]."</td>";               
		$html.= "<td class='center' style='border: 1px solid #BDBDDB; padding: 10px 5px;'>".$data['code']."</td>";
		$html.= "<td class='center' style='border: 1px solid #BDBDDB; padding: 10px 5px;'>".Dropdown::getDropdownName("glpi_projectstates",$data['projectstates_id'])."</td>";
                                     $html.= "<td class='center' style='border: 1px solid #BDBDDB; padding: 10px 5px;'>".$data['numero_paquetes']."</td>";
                                      $html.= "<td class='center' style='border: 1px solid #BDBDDB; padding: 10px 5px;'>".$data['numero_evaluaciones']."</td>";
                                      $html.= "<td class='center' style='border: 1px solid #BDBDDB; padding: 10px 5px;'>".Dropdown::getDropdownName("glpi_users",$data['usuario_cargo_proyecto'])."</td>";
                                                                          
                                $html.= "</tr>";
                             
	}
	$html.="<br/>";
	$html.= "</table></div>";
	$html.="<br>";

        $nombre_pdf="Lista de Proyectos.pdf";
        //exportamos el contenido de la variable $html a pdf, y el pdf tendra el nombre de $nombre_pdf
       include ("../../../dompdf/output.php");
      