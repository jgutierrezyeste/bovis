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
    if(in_array($profileID, array(9,14,15))){  
        $ver = false;
        echo "<input id='ver' type='hidden' value='0' />";
    }else{
        echo "<input id='ver' type='hidden' value='1' />";
    }   

echo "<script type='text/javascript'>
	$(document).ready(function() {     
            if($('#ver').val() == '1'){
                $('#tablaFiltros').DataTable({
                    'searching':      true,
                    'scrollY':        '300px',
                    'scrollCollapse': true,
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
                $('#tablaFiltros').DataTable({
                    'searching':      true,
                    'scrollY':        '300px',
                    'scrollCollapse': true,
                    'paging':         false,
                    'language': {'url': '".$CFG_GLPI["root_doc"]."/lib/jqueryplugins/Spanish.json'}                      
                });            
            }

            $('.dataTables_wrapper .ui-toolbar').css('padding','0px');
            $('.dataTables_wrapper .ui-toolbar').css('border', '0px');
	});
        
</script>";


//GROUP_CONCAT(distinct proyectos.name SEPARATOR '<br>') as 'proyectos',
//GROUP_CONCAT(distinct paquetes.name SEPARATOR '<br>') as 'contratos'
$where='';
$query="SELECT 
proveedores.id as 'proveedor_id',
proveedores.name as 'proveedor_nombre',
proveedores.cif as 'cif',
proveedores.cv_id as 'cv',
GROUP_CONCAT(distinct concat(proyectos.name, ' - ',paquetes.name) SEPARATOR '<br>') as proyectos
FROM glpi_suppliers as proveedores 
LEFT JOIN  glpi_projecttaskteams as paquetes_proveedor on proveedores.id=paquetes_proveedor.items_id
LEFT JOIN glpi_projecttasks as paquetes	on paquetes_proveedor.projecttasks_id=paquetes.id
LEFT JOIN  glpi_projects as proyectos on proyectos.id=paquetes.projects_id";

    //Añadimos los filtros al where de la consulta
    if($_GET['nombre_proveedor']!=''){
        $where.="UPPER(proveedores.name) LIKE UPPER('%".$_GET['nombre_proveedor']."%')";
    }
    if($_GET['cif']!=''){
       if($where!='') {$where.=' or ';}
        $where.=$where." trim(proveedores.cif)=trim('".$_GET['cif']."')";
    }
    if($_GET['id_proyecto']!='' && $_GET['id_proyecto']>0){
       if($where!='' && $where!='0') {$where.=' or ';}
       $where.=" proyectos.id=".$_GET['id_proyecto']." ";
    }
    if($_GET['codigo_proyecto']!=''){
       if($where!='') {$where.=' or ';}
       $where.=" proyectos.code='".$_GET['codigo_proyecto']."' ";
    }
    //$where.=" proveedores.is_deleted=0 ";

    if ($where!='') {
        $where=" where proveedores.is_deleted=0 and ".$where; 
    }else{ 
        $where=" where proveedores.is_deleted=0 ";
    }
    //$where = "".$where;

    $groupby = ' group by proveedores.id, proveedores.name, proveedores.cif, proveedores.cv_id';
    $orderby = ' order by 2 desc';

    $query = $query.$where.$groupby.$orderby;
    //echo $query;
   
    $result = $DB->query($query);

    echo "<div align='center'>";
       echo "<table id='tablaFiltros' class='display compact dataTable no-footer' style='padding:4px; font-size:10px;' >";
       echo "<thead>";
       echo "<tr>";
               echo "<th>".__('Nombre')."</th>";
               echo "<th>".__('CIF')."</th>";
               echo "<th>".__('Proyecto')."</th>";
               //echo "<th>".__('Contratos')."</th>";
               echo "<th>".__('CV')."</th>";
    echo "</tr></thead><tbody>";

    while ($data = $DB->fetch_array($result)) {
        echo "<tr class='tab_bg_2'>";
                $nombreSupplier = "";
                if ($data["proveedor_nombre"]==""){
                    $nombreSupplier="(sin nombre)";
                }else{
                    $nombreSupplier=$data["proveedor_nombre"];
                }
                echo "<td class='left'><a href='".$CFG_GLPI["root_doc"]."/front/supplier.form.php?id=".$data["proveedor_id"]."'>".$nombreSupplier."</a></td>";               
                echo "<td class='center'>".$data['cif']."</td>";
                echo "<td class='left'>".$data['proyectos']."</td>";
                //echo "<td style='text-align:left; font-size:10px;'>".$data['contratos']."</td>";
                if(!empty($data['cv'])){
                   echo "<td class='center' style='color:transparent;font-size:2px; background-repeat: no-repeat; background-size: 20px; background-position: center; background-image:url(".$CFG_GLPI["root_doc"]."/pics/CHECK.png);'>SI</td>";
                }
                else{
                   echo "<td class='center' style='color:transparent;font-size:2px; background-repeat: no-repeat; background-size: 20px; background-position: center; background-image:url(".$CFG_GLPI["root_doc"]."/pics/CHECK_no.png);'>NO</td>";
                }  
        echo "</tr>";
    }
    echo"</tbody>";
    echo "</table></div>";
    echo"<br>";
	
