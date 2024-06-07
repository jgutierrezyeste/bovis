<?php
use Glpi\Event;

GLOBAL $DB,$CFG_GLPI;


$where='';

/////////////////////////////////SelectionsupplierF1
//Comprobamos que se ha enviado algún filtro de busqueda
$filtrar=false;

foreach ($_GET as $value) {
         if($value!='' && $value!='Siguiente'){
                $filtrar=true;
         }
}
//Si hay algún filtro y viene de la página selectionsupplierF1 que ponga el where en la consulta
$where=$where." WHERE (proveedor.id>0) ";
if($filtrar){

	if((isset($_GET["nombre_proveedor"])) && (!empty($_GET["nombre_proveedor"]))){
	 $where=$where." AND UPPER(proveedor.name) LIKE UPPER('%".$_GET['nombre_proveedor']."%')";
	}
	if((isset($_GET["IdTipo"])) && (!empty($_GET["IdTipo"]))){
	 $where=$where." AND (plugin_comproveedores_roltypes_id=".$_GET['IdTipo'].")";
	}	
	if((isset($_GET["IdProyecto"])) && (!empty($_GET["IdProyecto"]))){
	 $where=$where." AND (proyectos.id=".$_GET['IdProyecto'].")";
	}
	if((isset($_GET["codigo_proyecto"])) && (!empty($_GET["codigo_proyecto"]))){
	 $where=$where." AND (proyectos.code='".$_GET['codigo_proyecto']."') ";
	} 
	if((isset($_GET["minima"])) && (!empty($_GET["minima"]))){
	 $where=$where." AND (facturacion>=".$_GET['minima'].")"; 
	}  
	if((isset($_GET["maxima"])) && (!empty($_GET["maxima"]))){
	 $where=$where." AND (facturacion<=".$_GET['maxima'].")"; 
	}  	
	if((isset($_GET["IdCategorias"])) && (!empty($_GET["IdCategorias"]))){
	 $where=$where." AND (lista_especialidades.plugin_comproveedores_specialties_id=".$_GET['IdCategorias']." AND proveedor.cv_id=lista_especialidades.cv_id)";
	} 
	if((isset($_GET["IdEspecialidades"])) && (!empty($_GET["IdEspecialidades"]))){
	 $where=$where." AND (lista_especialidades.plugin_comproveedores_categories_id=".$_GET['IdEspecialidades']." AND proveedor.cv_id=lista_especialidades.cv_id)";
	} 
        if((isset($_GET["ambitos"])) && (!empty($_GET["Ambitos"]))){
	 $where=$where." AND (l.plugin_comproveedores_ambitos_id in ({$_GET["ambitos"]}))";
	} 
}  
	//Creamos la consulta y añadimos el where a la consulta
	
	echo "<script type='text/javascript'>
	    $(document).ready(function () {
                $('#tblList').DataTable({
                        'searching': false,
                        'paging': false,
                        'scrollY': 400,
                        'ordering': true,				
                        'order': [[1, 'asc']],
                        'language': {
                            'decimal': '',
                            'emptyTable': 'No hay información',
                            'info': 'Mostrando _START_ a _END_ de _TOTAL_ Entradas',
                            'infoEmpty': 'Mostrando 0 to 0 of 0 Entradas',
                            'infoFiltered': '(Filtrado de _MAX_ total entradas)',
                            'infoPostFix': '',
                            'thousands': ',',
                            'lengthMenu': 'Mostrar _MENU_ Entradas',
                            'loadingRecords': 'Cargando...',
                            'processing': 'Procesando...',
                            'search': 'Buscar:',
                            'zeroRecords': 'Sin resultados encontrados',
                            'paginate': {
                                'first': 'Primero',
                                'last': 'Ultimo',
                                'next': 'Siguiente',
                                'previous': 'Anterior'}}			
                });
            });
	</script>
	";
	
	$query ="select 
	proveedor.id as supplier_id,
	proyectos.name as nombre_proyecto,
	proyectos.code as codigo_proyecto,
	contratos.name as nombre_contrato,
	proveedor.name as proveedor,
	especialidades(proveedor.id) as especialidad, 
	facturacion.facturacion as fac, 
	proveedor.cv_id,
	lista_especialidades.plugin_comproveedores_roltypes_id as tipo_especialidad,
	calidad(proveedor.id) as calidad, 
	planificacion(proveedor.id) as planificacion, 
	costes(proveedor.id) as costes,
	cultura_empresarial(proveedor.id) as cultura_empresarial,  
	gestion_de_suministros_y_subcontratistas(proveedor.id) as gestion_de_suministros_y_subcontratistas, 
	seguridad_y_salud_y_medioambiente(proveedor.id)  as seguridad_y_salud_y_medioambiente,
	bim_contratista(proveedor.id) as bim_contratista,  
	bim_servicio(proveedor.id) as bim_servicio,
	certificacion_medioambiental(proveedor.id) as certificacion_medioambiental, 
	proyecto_basico(proveedor.id) as proyecto_basico,  
	proyecto_de_ejecucion(proveedor.id) as proyecto_de_ejecucion, 
	capacidad_de_la_empresa(proveedor.id) as capacidad_de_la_empresa, 
	colaboradores(proveedor.id) as colaboradores, 
	capacidad(proveedor.id) as capacidad,
	actitud(proveedor.id) as actitud
	FROM glpi_suppliers as proveedor
	LEFT JOIN glpi_projecttaskteams as teams on teams.items_id=proveedor.id AND teams.itemtype='Supplier'
	LEFT JOIN glpi_projecttasks as contratos on contratos.id=teams.projecttasks_id
	LEFT JOIN glpi_projects as proyectos on proyectos.id=contratos.projects_id
	LEFT JOIN glpi_plugin_comproveedores_valuations as evaluaciones on evaluaciones.projecttasks_id=contratos.id 
	LEFT JOIN glpi_plugin_comproveedores_listspecialties as lista_especialidades on lista_especialidades.cv_id=proveedor.cv_id 
	LEFT JOIN glpi_plugin_comproveedores_specialties as especialidad on especialidad.id=lista_especialidades.plugin_comproveedores_specialties_id
	LEFT JOIN glpi_plugin_comproveedores_annualbillings as facturacion on facturacion.cv_id=proveedor.cv_id AND year(facturacion.anio) = maximo_anio(proveedor.cv_id)
	LEFT JOIN glpi_plugin_comproveedores_listambitos as l on l.cv_id = proveedor.cv_id";
        
	//$query = $query.$where." ORDER BY 2 DESC";

	$result = $DB->query($query);
	
	
	echo "<div style='width: 90%; margin-left:12%; background-color:#FFF;'>";
	echo "<table id='tblList' class='display compact dataTable no-footer'>";	
		echo "<thead>";
		echo "<tr>";		
			echo "<th>".__('Tipo')."</th>";
			echo "<th>".__('Proveedor')."</th>";
			echo "<th>".__('Contrato')."</th>";
			echo "<th>".__('Proyecto')."</th>";
			echo "<th>".__('Código proyecto')."</th>";
			echo "<th>".__('Especialidad')."</th>";
			echo "<th>".__('CV')."</th>";
			echo "<th>".__('Facturación')."</th>";
			echo "<th>".__('Q')."</th>";
			echo "<th>".__('PLZ')."</th>";
			echo "<th>".__('COST')."</th>";
			echo "<th>".__('CULT')."</th>";
			echo "<th>".__('SUBC')."</th>";
			echo "<th>".__('SyS')."</th>";
			echo "<th>".__('BIM')."</th>";
			echo "<th>".__('CERT')."</th>";		
		echo"</tr>";
		echo"</thead>";
		echo"<tbody>";
		while ($data = $DB->fetch_array($result)) {

				echo"<tr>";
				if(!empty($data['tipo_especialidad'])){			
					echo"<td>";	
					switch ($data['tipo_especialidad']) {
						case 1:
						echo "SUMIN.";
						break;
						case 2:
						echo "CONTR.";
						break;
					} 
					echo "</td>";
				}else {
					echo"<td>";		
					echo"</td>";
				}				
				if(!empty($data['proveedor'])){			
					echo"<td>";				
					echo"<a href='".$CFG_GLPI["root_doc"]."/front/supplier.form.php?id=".$data["supplier_id"]."'>".$data['proveedor']."</a></td>";   
				}else {
					echo"<td>";		
					echo"</td>";
				}
				if(!empty($data['nombre_contrato'])){
					 echo"<td>".$data['nombre_contrato']."</td>";
				}else{
					 echo"<td class='celda_list'></td>";
				}
				 if(!empty($data['nombre_proyecto'])){
					 echo"<td>".$data['nombre_proyecto']."</td>";
				}else{
					echo"<td></td>";
				}
				 if(!empty($data['codigo_proyecto'])){
					echo"<td>".$data['codigo_proyecto']."</td>";
				}else{
					 echo"<td></td>";
				}
				if(!empty($data['especialidad'])){
					echo"<td>".$data['especialidad']."</td>";
				}else{
					 echo"<td></td>";
				}
				if(!empty($data['cv_id'])){
				   echo"<td><img  style='vertical-align:middle; margin: 10px 0px;' src='".$CFG_GLPI["root_doc"]."/pics/CheckBoxTrue.png'></td>";
				}else{
				   echo"<td><img  style='vertical-align:middle; margin: 10px 0px;' src='".$CFG_GLPI["root_doc"]."/pics/CheckBoxFalse.png'></td>";
				}
				if(!empty($data['fac'])){
					$facturacion='0,00';
				}else {
					$facturacion=substr(number_format($data['fac'], 0, '', '.'),0,strlen(number_format($data['fac'], 0, '', '.')));
				}
				echo"<td class='celda_list'>".$facturacion." €</td>";
				if(!empty($data['calidad'])){
					echo"<td style='color: black ; text-shadow:  2 white; background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".getColorValoracion($data['calidad']).".png); background-repeat: no-repeat;  background-position: center;'>".$data['calidad']."</td>";
				}else{
					echo"<td></td>";
				}
				if(!empty($data['planificacion'])){
					echo"<td style='font-weight: bold; color: black ; text-shadow:  2 white; background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".getColorValoracion($data['planificacion']).".png); background-repeat: no-repeat;  background-position: center;'>".$data['planificacion']."</td>";
				}
				else{
					echo"<td></td>";
				}
				if(!empty($data['costes'])){
					echo"<td style='font-weight: bold; color: black ; text-shadow:  2 white; background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".getColorValoracion($data['costes']).".png); background-repeat: no-repeat;  background-position: center;'>".$data['costes']."</td>";
				}
				else{
					echo"<td></td>";
				}
				if(!empty($data['cultura_empresarial'])){
					echo"<td style='font-weight: bold; color: black ; text-shadow:  2 white; background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".getColorValoracion($data['cultura_empresarial']).".png); background-repeat: no-repeat;  background-position: center;'>".$data['cultura_empresarial']."</td>";
				}
				else{
					echo"<td></td>";
				}
				if(!empty($data['gestion_de_suministros_y_subcontratistas'])){
					 echo"<td style='font-weight: bold; color: black ; text-shadow:  2 white; background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".getColorValoracion($data['gestion_de_suministros_y_subcontratistas']).".png); background-repeat: no-repeat;  background-position: center;'>".$data['gestion_de_suministros_y_subcontratistas']."</td>";
				}
				else{
					echo"<td></td>";
				}
				if(!empty($data['seguridad_y_salud_y_medioambiente'])){
					echo"<td style='font-weight: bold; color: black ; text-shadow:  2 white; background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".getColorValoracion($data['seguridad_y_salud_y_medioambiente']).".png); background-repeat: no-repeat;  background-position: center;'>".$data['seguridad_y_salud_y_medioambiente']."</td>";
				}else{
					echo"<td></td>";
				}
				if(!empty($data['bim_contratista'])){
					echo"<td style='font-weight: bold; color: black ; text-shadow:  2 white; background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".getColorValoracion($data['bim_contratista']).".png); background-repeat: no-repeat;  background-position: center;'>".$data['bim_contratista']."</td>";
				}else{
					echo"<td></td>";
				}
				if(!empty($data['certificacion_medioambiental'])){
					echo"<td style='font-weight: bold; color: black ; text-shadow:  2 white; background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".getColorValoracion($data['certificacion_medioambiental']).".png); background-repeat: no-repeat;  background-position: center;'>".$data['certificacion_medioambiental']."</td>";
				}else{
					echo"<td></td>";
				}		
				echo"</tr>";			
		}
		echo"</tbody>";
	echo"</table>";
	echo "</div>";

        
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
		
		