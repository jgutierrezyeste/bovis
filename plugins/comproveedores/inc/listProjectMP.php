<?php

use Glpi\Event;
include ("../../../inc/includes.php");
        
GLOBAL $DB,$CFG_GLPI;

    $profileID = 0;
    $USERID = $_SESSION['glpiID'];
    $query0 = "SELECT profiles_id as profile FROM glpi_users WHERE id=$USERID";
    $result0 = $DB->query($query0);
    $aux0 = $DB->fetch_array($result0);
    if($aux0['profile']<>''){
        $profileID = $aux0['profile'];
    }
    $ver = true;
    if(in_array($profileID, array(9))){  
        $ver = false;
        echo "<input id='ver' type='hidden' value='0' />";
    }else{
        echo "<input id='ver' type='hidden' value='1' />";
    }    
    
    $queryPrj = "select projects_id from glpi_projectteams where itemtype = 'User' And items_id = $USERID";
    $resultPrj = $DB->query($queryPrj);
    $prj = array();
    while ($data=$DB->fetch_array($resultPrj)) {
        array_push($prj, $data['projects_id']);
    }
    
    
        
$where='';

$query ="select proyectos.id, 
proyectos.name, 
proyectos.code, 
st.name as tiposervicio,
proyectos.projectstates_id, 
proyectos.plan_start_date, 
proyectos.plan_end_date, 
clie.name as cliente, 
if(proyectos.importe_proyecto is null, 0, proyectos.importe_proyecto) as importe,
(select GROUP_CONCAT(distinct t.name SEPARATOR '<br>')  
	from glpi_projecttasks as t where t.projects_id=proyectos.id and t.is_delete=0) as contratos, 
(select count(*) from glpi_projecttasks where projects_id=proyectos.id) as numero_paquetes, 
(select count(valoracion1.id) as numero	
	from glpi_projecttasks as paquetes1 
		left join glpi_plugin_comproveedores_valuations as valoracion1 on valoracion1.projecttasks_id=paquetes1.id 
	where paquetes1.projects_id=proyectos.id) as numero_evaluaciones, 
(select items_id 
	from glpi_projectteams 
	where projects_id=proyectos.id and gerente=1) as usuario_cargo_proyecto 
from glpi_projects as proyectos 
	left join glpi_projectclientes as clie on clie.id = proyectos.projectclientes_id 
            left join glpi_plugin_comproveedores_servicetypes as st on st.id = proyectos.plugin_comproveedores_servicetypes_id
            left join glpi_projectteams as pt on pt.projects_id = proyectos.id";

$where = " pt.items_id = ".$USERID." AND proyectos.is_deleted=0 AND (TIMESTAMPDIFF(MONTH, plan_start_date, plan_end_date)<={$_GET['duracionMaxima']} AND TIMESTAMPDIFF(MONTH, plan_start_date, plan_end_date)>={$_GET['duracionMinima']}) ";
$where = $where." AND (IF(proyectos.importe_proyecto IS NULL,0, proyectos.importe_proyecto)<={$_GET['costeMaximo']} And 
    IF(proyectos.importe_proyecto IS NULL, 0, proyectos.importe_proyecto)>={$_GET['costeMinimo']}) ";    
    

   
   if(!empty($_GET['denominacion'])){
        if($where!='') {$where.=' and ';}        
         $where=$where."UPPER(proyectos.name) LIKE UPPER('%".$_GET['denominacion']."%')";
    }

    if(!empty($_GET['codigo'])){
        if($where!='') {$where.=' and ';}        
         $where=$where."UPPER(proyectos.code) LIKE UPPER('%".$_GET['codigo']."%')";
    }    

    if(!empty($_GET['serviceType'])){
        if($where!='') {$where.=' and ';}
        $where=$where."plugin_comproveedores_servicetypes_id=".$_GET['serviceType'];
    }

    if(!empty($_GET['experiencestypesid'])){
        if($where!='') {$where.=' and ';}
        $where=$where."plugin_comproveedores_experiencestypes_id=".$_GET['experiencestypesid'];
    }    

    if(!empty($_GET['region'])){
        if($where!='') {$where.=' and ';}
        $where=$where."plugin_comproveedores_communities_id=".$_GET['region'];
    }    

    if(!empty($_GET['provincia'])){
        if($where!='') {$where.=' and ';}
        $where=$where."plugin_comproveedores_provinces_id=".$_GET['provincia'];
    }    

    if(!empty($_GET['estado']) && $_GET['estado']>0){
        if($where!='') {$where.=' and ';}
        $where=$where."projectstates_id=".$_GET['estado'];
    }    

    if(!empty($_GET['cliente']) && $_GET['cliente']>0){
        if($where!='') {$where.=' and ';}
        $where=$where."proyectos.projectclientes_id=".$_GET['cliente'];
    }       

    if(!empty($_GET['anio'])){
        if($where!='') {$where.=' and ';}
        $where=$where."(year(plan_start_date)<=".$_GET['anio']." AND year(plan_end_date)>=".$_GET['anio'].") or (plan_end_date is null)";
    }      
    if ($where!='') {$where=' where '.$where;}



$where = $where." order by proyectos.id desc";


$query=$query.$where;

$result = $DB->query($query);




	echo "<table id='tablaProyectos' class='display compact' style='padding:4px; font-size:10px;'>";
	echo "<thead>";
            echo "<tr>";

                echo "<th>".__('Nombre')."</th>";
                echo "<th>".__('Código del proyecto')."</th>";
                echo "<th>".__('Cliente')."</th>";
                echo "<th>".__('Estado')."</th>";
                echo "<th>".__('Inicio')."</th>";
                echo "<th>".__('Fin')."</th>";
                echo "<th>".__('Servicio')."</th>";
                echo "<th>".__('Contratos')."</th>";                
                echo "<th>".__('Nº Contratos')."</th>";
                echo "<th>".__('Nº Evaluaciones')."</th>";
                echo "<th>".__('Gerente del proyecto')."</th>";
            echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
	while ($data=$DB->fetch_array($result)) {
            if(($profileID == 14 && in_array($data['id'], $prj)) || (in_array($profileID, array(3,4,15,16))) ){ 
                echo "<tr>";
                    
                    if($profileID == 15){
                        echo "<td style='text-align:left;'>".$data["name"]."</td>";               
                    }else{
                        echo "<td style='text-align:left;'><a href='".$CFG_GLPI["root_doc"]."/front/project.form.php?id=".$data["id"]."'>".$data["name"]."</a></td>";                   
                    }
                    echo "<td class='left'>".$data['code']."</td>";
                    echo "<td class='left'>".$data['cliente']."</td>";
                    echo "<td class='center'>".Dropdown::getDropdownName("glpi_projectstates",$data['projectstates_id'])."</td>";
                    echo "<td class='center'>".Html::convDateTime($data['plan_start_date'], 3)."</td>";
                    echo "<td class='center'>".Html::convDateTime($data['plan_end_date'], 3)."</td>";
                    echo "<td class='center'>".$data['tiposervicio']."</td>";
                    echo "<td class='left' style='font-size: 8px;'>".$data['contratos']."</td>";
                    echo "<td class='center'>".$data['numero_paquetes']."</td>";
                    echo "<td class='center'>".$data['numero_evaluaciones']."</td>";
                    echo "<td class='center'>".Dropdown::getDropdownName("glpi_users",$data['usuario_cargo_proyecto'])."</td>";                                        
                echo "</tr>";     
            }
	}
        echo "</tbody>";
	echo "</table>";
        
	echo"<script type='text/javascript'>  
                $(document).ready(function() {
                    if($('#ver').val() == '1'){
                        $('#tablaProyectos').DataTable({
                            'searching':      true,
                            'scrollY':        '400px',
                            'scrollCollapse': true,
                            'ordering':       true,
                            'paging':         false,
                            'language': {'url': '".$CFG_GLPI["root_doc"]."/lib/jqueryplugins/Spanish.json'},
                            'dom': 'Bfrtip',
                            'buttons': [
                                'copyHtml5',
                                'excelHtml5',
                                'pdfHtml5'
                            ]                            
                        });
                    }else{
                        $('#tablaProyectos').DataTable({
                            'searching':      true,
                            'scrollY':        '400px',
                            'scrollCollapse': true,
                            'ordering':       true,
                            'paging':         false,
                            'language': {'url': '".$CFG_GLPI["root_doc"]."/lib/jqueryplugins/Spanish.json'}                         
                        });                    
                    }
                    $('.especialidades').css('display', 'none');

                });                        
	</script>";