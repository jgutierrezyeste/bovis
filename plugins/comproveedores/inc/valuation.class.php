<?php

/******************************************

	PLUGIN DE GESTION DE CURRICULUMS DE LOS PROVEEDORES


 ******************************************/

	class PluginComproveedoresValuation extends CommonDBTM{

		static $rightname	= "plugin_comproveedores";

		static function getTypeName($nb=0){
			return _n('Evaluación','Evaluaciones',1,'comproveedores');
		}

		function getTabNameForItem(CommonGLPI $item, $tabnum=1,$withtemplate=0){
                GLOBAL $DB,$CFG_GLPI;

                        $USERID = $_SESSION['glpiID'];
                        $self = new self();
                        $profile_Id=$self->getProfileByUserID($USERID);
                        if(in_array($profile_Id, array(3,4,14,15,16))){                    
                               // if($item-> getType()=="Supplier"){
                                        return self::createTabEntry('Evaluaciones');
                               // }
                        }
			return '';
		}

    static function displayTabContentForItem(CommonGLPI $item,$tabnum=1,$withtemplate=0){
    global $CFG_GLPI,$DB;
    $self = new self();

        if($item->getType()=='Supplier'){	
            if(isset($item->fields['cv_id'])){
                $self->showFormItemValuationProveedor($item, $withtemplate);
            }else{

                $self->showFormNoCV($item, $withtemplate);
            }
        }else if($item->getType()=='PluginComproveedoresCv'){
            $self->showFormValuationProveedor($item, $withtemplate);
        }else if($item->getType()=='ProjectTask'){

           $query ="select items_id from glpi_projecttaskteams where projecttasks_id=".$item->fields['id'];
           $result = $DB->query($query);

            if($result->num_rows!=0 || $_SESSION['glpiactiveprofile']['id']==4){
                 $self->showFormValuationPaquete($item, $withtemplate);
            }else{
                 $self->showFormNoAsignadoProveedor($item, $withtemplate);
            }
        }else if($item->getType()=='Project'){
                        $id_usuario=$_SESSION['glpiID'];

                        $query = "select distinct projectteams.items_id 
                                                        from glpi_projectteams as projectteams 
                                                        where projectteams.projects_id=".$item->fields['id']." and projectteams.items_id=".$id_usuario;

                        $result = $DB->query($query);

                        //Si un usuario de equipo de proyecto o tiene premisos de super-Admin, que entre
                        if($result->num_rows!=0 || $_SESSION['glpiactiveprofile']['id']==4){
                                        $self->showFormValuationProyecto($item, $withtemplate);
                        }
                        else{
                                        $self->showFormNoPermiso($item, $withtemplate);
                        }
        }            
    }

		function getSearchOptions(){

			$tab = array();

			$tab['common'] = ('Valoraciones');

			$tab[1]['table']	=$this->getTable();
			$tab[1]['field']	='name';
			$tab[1]['name']		=__('Name');
			$tab[1]['datatype']		='itemlink';
			$tab[1]['itemlink_type']	=$this->getTable();

			return $tab;

		}

		function registerType($type){
			if(!in_array($type, self::$types)){
				self::$types[]= $type;
			}		
		}

		static function getTypes($all=false) {
			if ($all) {
				return self::$types;
			}
    // Only allowed types
			$types = self::$types;
			foreach ($types as $key => $type) {
				if (!($item = getItemForItemtype($type))) {
					continue;
				}

				if (!$item->canView()) {
					unset($types[$key]);
				}
			}
			return $types;
		}
                
                
                
                // Visualizar Evaluaciones de un proveedor desde gestion de curriculum(Gestión curriculum/Evaluaciones)
		function showFormValuationProveedor($item, $withtemplate='') {	
			GLOBAL $DB,$CFG_GLPI;
                
                        $profile_Id=$this->getProfileByUserID($user_Id);
                        if(in_array($profile_Id, array(3,4,16))){
                            $CvId=$item->fields['id']; 

                            $query ="Select distinct
                                        contratos.id as contrato_id, 
                                        contratos.tipo_especialidad, 
                                        valoraciones.* 
                                        from glpi_projecttasks as contratos
                                        left join glpi_plugin_comproveedores_valuations as valoraciones on valoraciones.projecttasks_id=contratos.id 
                                        where valoraciones.id in(Select 
                                        max(valoraciones.id) from glpi_plugin_comproveedores_valuations as valoraciones
                                        where valoraciones.cv_id=$CvId group by valoraciones.projecttasks_id)";

                            $result = $DB->query($query);
                            //echo $query;

                            //Nos creamos 2 array, uno para la tabla Servicios profesionales y otro para Contratistas/Proveedores
                            $arrayServicioProfesionales=[];
                            $arrayContratistas=[];
                            while ($data=$DB->fetch_array($result)) {

                                    switch ($data['tipo_especialidad']) {
                                            case ($data['tipo_especialidad']==3):
                                                    $arrayServiciosProfesionales[]=$data;
                                                    break;
                                            case ($data['tipo_especialidad']<3):
                                                    $arrayContratistas[]=$data;
                                                    break;
                                    }
                            }
                            echo "";
                            echo "<div align='center'><table class='tab_cadre_fixehov'>";
                            echo "<tr class='tab_bg_2 tab_cadre_fixehov nohover' style='margin-top:0px !important;'><th colspan='14' >EVALUACIONES COMO CONTRATISTA</th></tr>";
                            //echo"<br/>";
                            echo "<tr><th style='min-width: 80px;'></th>";
                                    echo "<th style='width: 100px; background-color:#D8D8D8;' title='CALIDAD'>".__('Q.')."</th>";
                                    echo "<th style='width: 100px; background-color:#D8D8D8;' title='PLAZOS DE ENTREGA'>".__('PLZ')."</th>";
                                    echo "<th style='width: 100px; background-color:#D8D8D8;' title='COSTES'>".__('COST')."</th>";
                                    echo "<th style='width: 100px; background-color:#D8D8D8;' title='CULTURA EMPRESARIAL'>".__('CULT')."</th>";
                                    echo "<th style='width: 100px; background-color:#D8D8D8;' title='SUBCONTRATACIÓN'>".__('SUBC')."</th>";
                                    echo "<th style='width: 100px; background-color:#D8D8D8;' title='SEGURIDAD Y SALUD'>".__('SYS')."</th>";                                                                                
                                    echo "<th style='width: 100px; background-color:#D8D8D8;' title='BIM'>".__('BIM')."</th>";                                                                                
                                    echo "<th style='width: 100px; background-color:#D8D8D8;' title='CERTIFICACIONES'>".__('CERT')."</th>";
                            echo "</tr>";

                            foreach ($arrayContratistas as $contratista) {
                                echo "<tr style='height:50px;' class='tab_bg_2'>";
                                    echo "<td class='center' style='width:10px; text-align:left; background-color:#D8D8D8;'>";
                                        echo"<div>".Dropdown::getDropdownName("glpi_projecttasks",$contratista['contrato_id'])."</div>";
                                    echo"</td>";
                                    if($contratista['calidad']==0){
                                        echo "<td class='center'> - </td>";
                                    }else{
                                        echo "<td class='center' style=' font-weight: bold; color: black ;  background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($contratista['calidad']).".png); background-repeat: no-repeat;  background-position: center;'>".$contratista['calidad']."</td>";
                                    }
                                    if($contratista['planificacion']==0){                                        
                                        echo "<td class='center'> - </td>";
                                    }else{
                                        echo "<td class='center' style=' font-weight: bold; color: black ;  background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($contratista['planificacion']).".png); background-repeat: no-repeat;  background-position: center;'>".$contratista['planificacion']."</td>";
                                    }
                                    if($contratista['costes']==0){                                        
                                        echo "<td class='center'> - </td>";
                                    }else{
                                        echo "<td class='center' style=' font-weight: bold; color: black ;  background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($contratista['costes']).".png); background-repeat: no-repeat;  background-position: center;'>".$contratista['costes']."</td>";
                                    }                                    
                                    if($contratista['cultura_empresarial']==0){                                        
                                        echo "<td class='center'> - </td>";
                                    }else{
                                        echo "<td class='center' style=' font-weight: bold; color: black ;  background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($contratista['cultura_empresarial']).".png); background-repeat: no-repeat;  background-position: center;'>".$contratista['cultura_empresarial']."</td>";
                                    }                                       
                                    if($contratista['gestion_de_suministros_y_subcontratistas']==0){                                        
                                        echo "<td class='center'> - </td>";
                                    }else{
                                        echo "<td class='center' style=' font-weight: bold; color: black ;  background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($contratista['gestion_de_suministros_y_subcontratistas']).".png); background-repeat: no-repeat;  background-position: center;'>".$contratista['gestion_de_suministros_y_subcontratistas']."</td>";
                                    }                                                                           
                                    if($contratista['seguridad_y_salud_y_medioambiente']==0){                                        
                                        echo "<td class='center'> - </td>";
                                    }else{
                                        echo "<td class='center' style=' font-weight: bold; color: black ;  background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($contratista['seguridad_y_salud_y_medioambiente']).".png); background-repeat: no-repeat;  background-position: center;'>".$contratista['seguridad_y_salud_y_medioambiente']."</td>";
                                    }                                     
                                    if($contratista['bim']==0){                                        
                                        echo "<td class='center'> - </td>";
                                    }else{
                                        echo "<td class='center' style=' font-weight: bold; color: black ;  background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($contratista['bim']).".png); background-repeat: no-repeat;  background-position: center;'>".$contratista['bim']."</td>";
                                    }                                        
                                    if($contratista['certificacion_medioambiental']==0){                                        
                                        echo "<td class='center'> - </td>";
                                    }else{
                                        echo "<td class='center' style=' font-weight: bold; color: black ;  background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($contratista['certificacion_medioambiental']).".png); background-repeat: no-repeat;  background-position: center;'>".$contratista['certificacion_medioambiental']."</td>";
                                    }                                                
                                    
                                echo"</tr>";

                            }
                            //echo"<br/>";
                            echo "</table></div>";

                            echo "<div align='center'><table class='tab_cadre_fixehov'>";
                            echo "<tr class='tab_bg_2 tab_cadre_fixehov nohover'><th colspan='14' >Evaluaciones Servicios Profesionales</th></tr>";

                            echo "<tr><th style='min-width: 80px;'></th>";
                                    echo "<th style='width: 100px; background-color:#D8D8D8;'>".__('PROY BÁSICO')."</th>";
                                    echo "<th style='width: 100px; background-color:#D8D8D8;'>".__('PROY EJECUCIÓN')."</th>";
                                    echo "<th style='width: 100px; background-color:#D8D8D8;'>".__('CAP EMPRESA')."</th>";
                                    echo "<th style='width: 100px; background-color:#D8D8D8;'>".__('COLABORADOR')."</th>";
                                    echo "<th style='width: 100px; background-color:#D8D8D8;'>".__('CAPACIDAD')."</th>";
                                    echo "<th style='width: 100px; background-color:#D8D8D8;'>".__('ACTITUD')."</th>";                                                                                
                                    echo "<th style='width: 100px; background-color:#D8D8D8;'>".__('BIM')."</th>";
                            echo "</tr>";

                            foreach ($arrayServiciosProfesionales as $servicioProfesional) {
                                echo "<tr style='height:50px;' class='tab_bg_2'>";
                                    echo "<td class='center' style='width:10px; text-align:left; background-color:#D8D8D8;'>";
                                        echo"<div>".Dropdown::getDropdownName("glpi_projecttasks",$servicioProfesional['contrato_id'])."</div>";
                                    echo"</td>";
                                    if($servicioProfesional['proyecto_basico']==0){                                        
                                        echo "<td class='center'> - </td>";
                                    }else{
                                        echo "<td class='center' style=' font-weight: bold; color: black ;  background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($servicioProfesional['proyecto_basico']).".png); background-repeat: no-repeat;  background-position: center;'>".$servicioProfesional['proyecto_basico']."</td>";
                                    }                 
                                    if($servicioProfesional['capacidad_de_la_empresa']==0){                                        
                                        echo "<td class='center'> - </td>";
                                    }else{
                                        echo "<td class='center' style=' font-weight: bold; color: black ;  background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($servicioProfesional['capacidad_de_la_empresa']).".png); background-repeat: no-repeat;  background-position: center;'>".$servicioProfesional['capacidad_de_la_empresa']."</td>";
                                    }                                      
                                    if($servicioProfesional['colaboradores']==0){                                        
                                        echo "<td class='center'> - </td>";
                                    }else{
                                        echo "<td class='center' style=' font-weight: bold; color: black ;  background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($servicioProfesional['colaboradores']).".png); background-repeat: no-repeat;  background-position: center;'>".$servicioProfesional['colaboradores']."</td>";
                                    }                                      
                                    if($servicioProfesional['capacidad']==0){                                        
                                        echo "<td class='center'> - </td>";
                                    }else{
                                        echo "<td class='center' style=' font-weight: bold; color: black ;  background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($servicioProfesional['capacidad']).".png); background-repeat: no-repeat;  background-position: center;'>".$servicioProfesional['capacidad']."</td>";
                                    } 
                                    if($servicioProfesional['capacidad']==0){                                        
                                        echo "<td class='center'> - </td>";
                                    }else{
                                        echo "<td class='center' style=' font-weight: bold; color: black ;  background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($servicioProfesional['actitud']).".png); background-repeat: no-repeat;  background-position: center;'>".$servicioProfesional['actitud']."</td>";
                                    }                                     
                                    if($servicioProfesional['bim']==0){                                        
                                        echo "<td class='center'> - </td>";
                                    }else{
                                        echo "<td class='center' style=' font-weight: bold; color: black ;  background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($servicioProfesional['bim']).".png); background-repeat: no-repeat;  background-position: center;'>".$servicioProfesional['bim']."</td>";
                                    }  
                                echo"</tr>";
                            }
                            echo "</table></div>";

                            //echo "<br><br>";
                            echo "<div align='center' style='margin-top: 10px;'><table>";
                               echo "<tr>";
                                   echo "<td class='center'>Tipología de las calificaciones -></td>";                       
                                   echo "<td class='center'><img style='vertical-align:middle; margin: 5px 0px; width: 20px; height: 20px;' src=".$CFG_GLPI["root_doc"]."/pics/valoracion_1.png></td>";                                                             
                                   echo "<td  style='width: 50px;'>MALA</td>";
                                   echo "<td class='center'><img style='vertical-align:middle; margin: 5px 0px; width: 20px; height: 20px;' src=".$CFG_GLPI["root_doc"]."/pics/valoracion_2.png></td>";                                                            
                                   echo "<td  style='width: 50px;'>POBRE</td>";
                                   echo "<td class='center'><img style='vertical-align:middle; margin: 5px 0px; width: 20px; height: 20px;' src=".$CFG_GLPI["root_doc"]."/pics/valoracion_3.png></td>";                                                            
                                   echo "<td  style='width: 50px;'>ACEPTABLE</td>";
                                   echo "<td class='center'><img style='vertical-align:middle; margin: 5px 0px; width: 20px; height: 20px;' src=".$CFG_GLPI["root_doc"]."/pics/valoracion_4.png></td>";                                                            
                                   echo "<td  style='width: 50px;'>BUENA</td>";
                                   echo "<td class='center'><img style='vertical-align:middle; margin: 5px 0px; width: 20px; height: 20px;' src=".$CFG_GLPI["root_doc"]."/pics/valoracion_5.png></td>";                                                            
                                   echo "<td  style='width: 50px;'>EXCELENTE</td>";
                               echo "</tr>";
                           echo "</table></div>";
                            //echo"<br>";
                        }else{
                            echo "No posee permisos para visualizar esta pestaña, contacte con su administrador.";
                        }	
		}
                
    // Visualizar Evaluaciones de un proyecto(Proyecto/Evaluaciones)
    function showFormValuationProyecto($item, $withtemplate='') {

        GLOBAL $DB,$CFG_GLPI;

        echo"<style>
            .semaforo_valoracion{
                background-size: 24px;
            }
        </style>";

        $proyecto_id=$item->fields['id']; 
        $query2 = "select  distinct paquetes.id as paqueteid,
                        paquetes.code as codigo_paquete, 
                        prj.id as proyectoid,
                        prj.name as proyecto,
                        paquetes.name as nombre_paquete, 
                        paquetes.tipo_especialidad as tipo_especialidad, 
                        ult_nombreproveedor(paquetes.id) as nombre_proveedor,
                        ult_cifnif(paquetes.id) as cifnif,
                        ult_calidad(paquetes.id) as calidad,
                        ult_planificacion(paquetes.id) as planificacion,
                        ult_costes(paquetes.id) as costes,
                        ult_cultura_empresarial(paquetes.id) as cultura_empresarial,
                        ult_gestion_de_suministros_y_subcontratistas(paquetes.id) as gestion_de_suministros_y_subcontratistas,
                        ult_seguridad_y_salud_y_medioambiente(paquetes.id) as seguridad_y_salud_y_medioambiente,
                        ult_bim(paquetes.id) as bim,
                        ult_certificacion_medioambiental (paquetes.id) as certificacion_medioambiental,
                        ult_proyecto_basico (paquetes.id) as proyecto_basico,
                        ult_proyecto_de_ejecucion (paquetes.id) as proyecto_de_ejecucion,
                        ult_capacidad_de_la_empresa (paquetes.id) as capacidad_de_la_empresa,
                        ult_colaboradores (paquetes.id) as colaboradores,
                        ult_actitud(paquetes.id) as actitud,
                        ult_fecha(paquetes.id) as fecha,
                        ult_comentario(paquetes.id) as comentario,
                        ult_evaluacion_final(paquetes.id) as evaluacion_final                                        
                from glpi_projecttasks as paquetes 
                    left join glpi_projects as prj on prj.id = paquetes.projects_id
                where paquetes.projects_id=".$proyecto_id." And is_delete=0 And ult_nombreproveedor(paquetes.id) is not null
                order by paquetes.tipo_especialidad asc";

        $result2 = $DB->query($query2);
        //echo $query2;

        //Nos creamos 2 array, uno para la tabla Servicios profesionales y otro para Contratistas
        $arrayServicioProfesionales     = [];
        $arrayContratistas              = [];
        while ($data=$DB->fetch_array($result2)) {
                if($data['tipo_especialidad']==3){
                    $arrayServiciosProfesionales[] = $data;
                }
                if($data['tipo_especialidad']<3){
                    $arrayContratistas[] = $data;
                }
        }


        $query3 ="SELECT  distinct t.id as paqueteid, 
                        prj.name as proyecto,
                        t.projects_id as proyectoid,
                        t.name as contrato, subp.id, 
                        subp.name as subcontrato, 
                        subp.valoracion as valoracion, 
                        subp.suppliers_id, 
                        s.name as proveedor, 
                        s.cif as cif
                        FROM glpi_plugin_comproveedores_subpaquetes as subp
                        inner join glpi_suppliers as s on s.id = subp.suppliers_id
                        inner join glpi_projecttasks as t on t.id = subp.projecttasks_id 
                        inner join glpi_projects as prj on prj.id = t.projects_id
                        WHERE subp.is_deleted = 0 AND t.projects_id = ".$proyecto_id;
        $result3 = $DB->query($query3);                        
        //echo $query1;        
        //echo $query2;        
        //echo $query3;

//        echo "<div align='center' ><table>";
//            echo "<tr>";
//                echo "<td>CALIFICACIONES: </td>";
//                echo "<td class='center'><img style='vertical-align:middle; margin: 5px 0px;' src=".$CFG_GLPI["root_doc"]."/pics/valoracion_1.png></td>";                                                            
//                echo "<td  style='width: 20px;'>MALA</td>";
//
//                echo "<td class='center'><img style='vertical-align:middle; margin: 5px 0px;' src=".$CFG_GLPI["root_doc"]."/pics/valoracion_2.png></td>";                                                            
//                echo "<td  style='width: 20px;'>POBRE</td>";
//
//                echo "<td class='center'><img style='vertical-align:middle; margin: 5px 0px;' src=".$CFG_GLPI["root_doc"]."/pics/valoracion_3.png></td>";                                                            
//                echo "<td  style='width: 20px;'>ACEPTABLE</td>";
//
//                echo "<td class='center'><img style='vertical-align:middle; margin: 5px 0px;' src=".$CFG_GLPI["root_doc"]."/pics/valoracion_4.png></td>";                                                            
//                echo "<td  style='width: 20px;'>BUENA</td>";
//
//                echo "<td class='center'><img style='vertical-align:middle; margin: 5px 0px;' src=".$CFG_GLPI["root_doc"]."/pics/valoracion_5.png></td>";                                                            
//                echo "<td  style='width: 20px;'>EXCELENTE</td>";
//            echo "</tr>";
//        echo "</table></div>";
        
        echo "<div align='center' id='valoraciones' style='height: 550px;
                                                margin-bottom: 5px;
                                                float: left;
                                                position: relative;
                                                width: 98%;
                                                background-color: #e9ecf3;
                                                border-radius: 4px;
                                                padding: 10px;
                                                overflow-y: auto;'>";

        //Tabla Servicios profesionales     
        //echo "<div id='ctratistasprov' style='width: 100%; color: #FFF; background-color:#0e52a0; text-align: left; padding: 5px 0px 10px 10px; font-weight: bold;'>";
        echo "<div id='acordeon'>";
        echo "<h3 style='text-align: left; font-weight: bold;'> EVALUACIONES DE CONTRATISTAS Y PROVEEDORES</h3>";
        //echo "</div>";
        echo "<div>";
        echo"<table id='tblctryprov' class='display compact'>";
        echo "<thead>";
            echo "<tr>";                         
                    echo "<th>".__('Proyecto')."</th>";                                
                    echo "<th>".__('Contrato')."</th>";                                
                    echo "<th>".__('Código')."</th>";
                    echo "<th>".__('Proveedor')."</th>";
                    echo "<th>".__('NIF')."</th>";
                    echo "<th>".__('Fecha')."</th>";
                    echo "<th title='CALIDAD'>".__('Q')."</th>"; //calidad
                    echo "<th title='PLANIFICACIÓN'>".__('PLZ')."</th>"; //planificación
                    echo "<th title='COSTES'>".__('COST')."</th>"; //costes
                    echo "<th title='CULTURA EMPRESARIAL'>".__('CULT')."</th>"; //cultura empresarial
                    echo "<th title='SUBCONTRATISTAS'>".__('SUBC')."</th>";   //subcontratistas
                    echo "<th title='SEGURIDAD Y SALUD'>".__('SYS')."</th>";    //seguridad y salud
                    echo "<th>".__('BIM')."</th>";
                    echo "<th>".__('CERT')."</th>";
                    echo "<th>".__('MEDIA eval')."</th>";
                    echo "<th>".__('COMENTARIO')."</th>";
                    echo "<th>".__('Es eval. final')."</th>";
            echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        foreach ($arrayContratistas as  $contratista) {
            echo "<tr style='height:50px;' class='tab_bg_2'>";
                echo "<td class='center' style='text-align:left; '><a href='project.form.php?id={$contratista['proyectoid']} data-hasqtip='0' aria-descripbedby='qtip-0' >".$contratista['proyecto']."</a></td>";
                echo "<td class='center' style='text-align:left; '><a href='projecttask.form.php?id={$contratista['paqueteid']} data-hasqtip='0' aria-descripbedby='qtip-0' >".$contratista['nombre_paquete']."</a></td>"; 
                echo "<td class='center' style='text-align:left; '>".$contratista['codigo_paquete']."</td>";
                echo "<td class='center' style='text-align:left; '>".$contratista['nombre_proveedor']."</td>";
                echo "<td class='center' style='text-align:left; '>".$contratista['cifnif']."</td>";
                echo "<td class='center' style='text-align:left; '>".Html::convDate($contratista['fecha'], 1)."</td>";
                $valorMedia = 0;
                $i = 0;
                if(!empty($contratista['calidad']) && $contratista['calidad']!=0){
                    echo "<td class='semaforo_valoracion' style='background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($contratista['calidad']).".png);'>".$contratista['calidad']."</td>";
                    $valorMedia = $valorMedia + $contratista['calidad'];      
                    $i = $i + 1;
                }
                else{
                    echo"<td class='semaforo_valoracion'>-</td>";
                }
                if(!empty($contratista['planificacion']) && $contratista['planificacion']!=0){
                    echo "<td class='semaforo_valoracion' style='background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($contratista['planificacion']).".png);'>".$contratista['planificacion']."</td>";
                    $valorMedia = $valorMedia + $contratista['planificacion'];
                    $i = $i + 1;
                }
                else{
                    echo"<td class='semaforo_valoracion'>-</td>";
                }
                if(!empty($contratista['costes']) && $contratista['costes']!=0){
                    echo "<td class='semaforo_valoracion' style='background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($contratista['costes']).".png);'>".$contratista['costes']."</td>";
                    $valorMedia = $valorMedia + $contratista['costes'];
                    $i = $i + 1;
                }
                else{
                    echo"<td class='semaforo_valoracion'>-</td>";
                }
                if(!empty($contratista['cultura_empresarial']) && $contratista['cultura_empresarial']!=0){
                    echo "<td class='semaforo_valoracion' style='background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($contratista['cultura_empresarial']).".png);'>".$contratista['cultura_empresarial']."</td>";
                    $valorMedia = $valorMedia + $contratista['cultura_empresarial'];
                    $i = $i + 1;
                }
                else{
                    echo"<td class='semaforo_valoracion'>-</td>";
                }
                if(!empty($contratista['gestion_de_suministros_y_subcontratistas']) && $contratista['gestion_de_suministros_y_subcontratistas']!=0){
                    echo "<td class='semaforo_valoracion' style='background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($contratista['gestion_de_suministros_y_subcontratistas']).".png);'>".$contratista['gestion_de_suministros_y_subcontratistas']."</td>";
                    $valorMedia = $valorMedia + $contratista['gestion_de_suministros_y_subcontratistas'];
                    $i = $i + 1;
                }
                else{
                    echo"<td class='semaforo_valoracion'>-</td>";
                }
                if(!empty($contratista['seguridad_y_salud_y_medioambiente']) && $contratista['seguridad_y_salud_y_medioambiente']!=0){
                    echo "<td class='semaforo_valoracion' style='background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($contratista['seguridad_y_salud_y_medioambiente']).".png);'>".$contratista['seguridad_y_salud_y_medioambiente']."</td>";
                    $valorMedia = $valorMedia + $contratista['seguridad_y_salud_y_medioambiente'];
                    $i = $i + 1;
                }else{
                    echo"<td class='semaforo_valoracion'>-</td>";
                }
if(!empty($contratista['bim']) && $contratista['bim']!=0){
                    echo "<td class='semaforo_valoracion' style='background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($contratista['bim']).".png);'>".$contratista['bim']."</td>";
                    $valorMedia = $valorMedia + $contratista['bim'];
                    $i = $i + 1;
                }else{
                    echo"<td class='semaforo_valoracion'>-</td>";
                }           
                if(!empty($contratista['certificacion_medioambiental']) && $contratista['certificacion_medioambiental']!=0){
                    echo "<td class='semaforo_valoracion' style='background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($contratista['certificacion_medioambiental']).".png);'>".$contratista['certificacion_medioambiental']."</td>";
                    $valorMedia = $valorMedia + $contratista['certificacion_medioambiental'];
                    $i = $i + 1;
                }else{
                    echo"<td class='semaforo_valoracion'>-</td>";
                }     
                if($i==0){
                    $final = 0;
                }else{
                    $final = round($valorMedia/$i,0);
                }
                if ($final != 0) {
                    echo "<td class='semaforo_valoracion' style='background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($final).".png);'>".$final."</td>";
                } else {
                    echo "<td class='semaforo_valoracion'>-</td>";
                }
                if($contratista['comentario']){
                    echo "<td class='left'>".$contratista['comentario']."</td>";
                }
                else{
                    echo "<td class='center'>--</td>";
                }                                                
                if($contratista['evaluacion_final']==1){
                    echo "<td class='center' ><img  style='width: 20px; vertical-align:middle; margin: 10px 0px;' src='".$CFG_GLPI["root_doc"]."/pics/CHECK.png'></td>";
                }
                else{
                    echo "<td class='center' ><img  style='width: 20px; vertical-align:middle; margin: 10px 0px;' src='".$CFG_GLPI["root_doc"]."/pics/CHECK_no.png'></td>";
                }  
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
        
        //echo "<div style='color: #FFF; background-color:#0e52a0; text-align: left; padding: 5px 0px 10px 10px; font-weight: bold;'>";
        echo "<h3 style='text-align: left; font-weight: bold;'> EVALUACIONES DE SERVICIOS PROFESIONALES</h3>";
        //echo "</div>";
        echo "<div>";
        echo "<table id='tblsrvprof' class='display compact'>";
        echo "<thead>";
            echo "<tr>";
                    echo "<th>".__('Proyecto')."</th>";  
                    echo "<th>".__('Contrato')."</th>";                                
                    echo "<th>".__('Código')."</th>";
                    echo "<th>".__('Proveedor')."</th>";
                    echo "<th>".__('NIF')."</th>";
                    echo "<th>".__('Fecha')."</th>";
                    echo "<th>".__('PROY BÁSICO')."</th>";
                    echo "<th>".__('PROY EJECUCIÓN')."</th>";
                    echo "<th>".__('CAP EMPRESA')."</th>";
                    echo "<th>".__('COLABORADOR')."</th>";
                    echo "<th>".__('CAPACIDAD')."</th>";
                    echo "<th>".__('ACTITUD')."</th>";
                    echo "<th>".__('BIM')."</th>";
                    echo "<th>".__('MEDIA eval')."</th>";										
                    echo "<th>".__('COMENTARIO')."</th>";		                                        
                    echo "<th>".__('Evaluación Final')."</th>";
            echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
            foreach ($arrayServiciosProfesionales as  $servicio_profesional) {
                $valorMedia = 0;
                echo "<tr style='height:50px;' class='tab_bg_2'>";
                    echo "<td class='center' style='text-align:left;'><a href='project.form.php?id={$servicio_profesional['proyectoid']} data-hasqtip='0' aria-descripbedby='qtip-0' >".$servicio_profesional['proyecto']."</a></td>";                                                        
                    echo "<td class='center' style='text-align:left;'><a href='projecttask.form.php?id={$servicio_profesional['paqueteid']} data-hasqtip='0' aria-descripbedby='qtip-0' >".$servicio_profesional['nombre_paquete']."</a></td>";                                                        
                    echo "<td class='center' style='text-align:left; '>".$servicio_profesional['codigo_paquete']."</td>";
                    echo "<td class='center' style='text-align:left; '>".$servicio_profesional['nombre_proveedor']."</td>";
                    echo "<td class='center' style='text-align:left; '>".$servicio_profesional['cifnif']."</td>";
                    echo "<td class='center' style='text-align:left; '>".Html::convDateTime($servicio_profesional['fecha'], 1)."</td>";
                    if(!empty($servicio_profesional['proyecto_basico']) && $servicio_profesional['proyecto_basico']!=0){
                        echo "<td class='semaforo_valoracion' style='background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($servicio_profesional['proyecto_basico']).".png);'>".$servicio_profesional['proyecto_basico']."</td>";
                        $valorMedia = $valorMedia + $servicio_profesional['proyecto_basico'];
                        $i = $i + 1;
                    }
                    else{
                        echo"<td class='semaforo_valoracion'>-</td>";
                    }
                    if(!empty($servicio_profesional['proyecto_de_ejecucion']) && $servicio_profesional['proyecto_de_ejecucion']!=0){
                        echo "<td class='semaforo_valoracion' style='background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($servicio_profesional['proyecto_de_ejecucion']).".png);'>".$servicio_profesional['proyecto_de_ejecucion']."</td>";
                        $valorMedia = $valorMedia + $servicio_profesional['proyecto_de_ejecucion'];
                        $i = $i + 1;
                    }
                    else{
                        echo"<td class='semaforo_valoracion'>-</td>";
                    }
                    if(!empty($servicio_profesional['capacidad_de_la_empresa']) && $servicio_profesional['capacidad_de_la_empresa']!=0){
                        echo "<td class='semaforo_valoracion' style='background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($servicio_profesional['capacidad_de_la_empresa']).".png);'>".$servicio_profesional['capacidad_de_la_empresa']."</td>";
                        $valorMedia = $valorMedia + $servicio_profesional['capacidad_de_la_empresa'];
                        $i = $i + 1;
                    }
                    else{
                        echo"<td class='semaforo_valoracion' >-</td>";
                    }
                    if(!empty($servicio_profesional['colaboradores']) && $servicio_profesional['colaboradores']!=0){
                        echo "<td class='semaforo_valoracion' style='background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($servicio_profesional['colaboradores']).".png);'>".$servicio_profesional['colaboradores']."</td>";
                        $valorMedia = $valorMedia + $servicio_profesional['colaboradores'];
                        $i = $i + 1;
                    }
                    else{
                        echo"<td class='semaforo_valoracion' >-</td>";
                    }
                    if(!empty($servicio_profesional['capacidad_de_la_empresa']) && $servicio_profesional['capacidad_de_la_empresa']!=0){
                        echo "<td class='semaforo_valoracion' style='background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($servicio_profesional['capacidad_de_la_empresa']).".png);'>".$servicio_profesional['capacidad_de_la_empresa']."</td>";
                        $valorMedia = $valorMedia + $servicio_profesional['capacidad_de_la_empresa'];    
                        $i = $i + 1;
                    }
                    else{
                        echo"<td class='semaforo_valoracion' >-</td>";
                    }
                    if(!empty($servicio_profesional['actitud']) && $servicio_profesional['actitud']!=0){
                        echo "<td class='semaforo_valoracion' style='background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($servicio_profesional['actitud']).".png)'>".$servicio_profesional['actitud']."</td>";
                        $valorMedia = $valorMedia + $servicio_profesional['actitud'];     
                        $i = $i + 1;
                    }
                    else{
                        echo"<td class='semaforo_valoracion' >-</td>";
                    }                
                    if(!empty($servicio_profesional['bim']) && $servicio_profesional['bim']!=0){
                        echo "<td class='semaforo_valoracion' style='background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($servicio_profesional['bim']).".png);'>".$servicio_profesional['bim']."</td>";
                        $valorMedia = $valorMedia + $servicio_profesional['bim'];   
                        $i = $i + 1;
                    }
                    else{
                        echo"<td class='semaforo_valoracion' >-</td>";
                    }
                    if($i==0){
                        $final = 0;
                    }else{
                        $final = round($valorMedia/$i,0);
                    }                    
                    if ($final != 0) {
                        echo "<td class='semaforo_valoracion' style='background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($final).".png);'>".$final."</td>";
                    } else {
                        echo "<td class='semaforo_valoracion'>-</td>";
                    }					
                    if($contratista['comentario']){
                        echo "<td class='left'>".$contratista['comentario']."</td>";
                    }
                    else{
                        echo "<td class='center' >-</td>";
                    }                                               
                    if($servicio_profesional['evaluacion_final']==1){
                            echo "<td class='center' ><img  style='width: 20px; vertical-align:middle; margin: 10px 0px;' src='".$CFG_GLPI["root_doc"]."/pics/CHECK.png'></td>";
                    }
                    else{
                            echo "<td class='center' ><img  style='width: 20px; vertical-align:middle; margin: 10px 0px;' src='".$CFG_GLPI["root_doc"]."/pics/CHECK_no.png'></td>";
                    }  

                echo"</tr>";
        } 
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
        //Tabla SUBCONTRATISTAS    
        //echo "<div style='color: #FFF; background-color:#0e52a0; text-align: left; padding: 5px 0px 10px 10px; font-weight: bold;'>";
        echo "<h3 style='text-align: left; font-weight: bold;'> EVALUACIONES DE SUBCONTRATISTAS</h3>";
        //echo "</div>";
        echo "<div>";
        echo "<table id='tblsubctr' class='display compact'>";
        echo "<thead>";
        echo "<tr>";
            echo "<th>".__('Proyecto')."</th>";     
            echo "<th>".__('Contrato')."</th>";                                
            echo "<th>".__('Subcontrato')."</th>"; 
            echo "<th>".__('Subcontratista')."</th>";
            echo "<th>".__('NIF/CIF')."</th>";
            echo "<th>".__('Evaluación')."</th>";										
        echo "</tr>";
        echo "</thead>";                
        echo "<tbody>";
        foreach ($result3 as  $r) {
            $valorMedia = 0;
            echo "<tr style='height:50px;' class='tab_bg_2'>";
                    echo "<td style='text-align:left; '><a href='project.form.php?id={$r['proyectoid']} data-hasqtip='0' aria-descripbedby='qtip-0' >".$r['proyecto']."</a></td>";                                                        
                    echo "<td style='text-align:left; '><a href='projecttask.form.php?id={$r['paqueteid']} data-hasqtip='0' aria-descripbedby='qtip-0' >".$r['contrato']."</a></td>";                                                        
                    echo "<td class='left'>".$r['subcontrato']."</td>";
                    echo "<td class='left'>".$r['proveedor']."</td>";
                    echo "<td class='left'>".$r['cif']."</td>";
                    echo "<td class='center'>".$r['valoracion']."</td>";
            echo"</tr>";
        } 
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
        echo "</div>";
        //echo "<br><br>";

                
                echo "<script type='text/javascript'>

                    $('#tblsrvprof').DataTable({
                        'searching':      true,
                        'scrollY':        '200px',
                        'scrollCollapse': true,
                        'ordering':       true,
                        'paging':         false,
                        'language': {'url': '".$CFG_GLPI["root_doc"]."/lib/jqueryplugins/Spanish.json'}
                    });      
                    $('#tblctryprov').DataTable({
                        'searching':      true,
                        'scrollY':        '200px',
                        'scrollCollapse': true,
                        'ordering':       true,
                        'paging':         false,
                        'language': {'url': '".$CFG_GLPI["root_doc"]."/lib/jqueryplugins/Spanish.json'}
                    });
                    $('#tblsubctr').DataTable({
                        'searching':      true,
                        'scrollY':        '200px',
                        'scrollCollapse': true,
                        'ordering':       true,
                        'paging':         false,
                        'language': {'url': '".$CFG_GLPI["root_doc"]."/lib/jqueryplugins/Spanish.json'}
                    });                    

                    $('#acordeon').accordion({
                        collapsible: true,
                        heightStyle: 'content',
                        activate: function( event, ui ) {
                            $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
                        }                        
                    });

                </script>";
		
	}
           
        
        // Visualizar Evaluaciones de un contrato(Proyecto/contrato/Evaluaciones)
        function showFormValuationPaquete($item, $withtemplate='') {
        GLOBAL $DB,$CFG_GLPI;

            $contrato_id            = $item->fields['id'];
            $contenido_valoracion   = 0;
            echo "
                <style>
                    .muestra_valoracion{
                        text-align: center;
                        font-weight: bold;
                        color: #464646;
                        background-repeat: no-repeat;
                        background-position: center;
                        background-size: 20px;
                        font-size: 10px;    
                    }
                </style>
                
                <script type='text/javascript'>
                var arrayValoracion = [];

                for ( var i = 1; i <=3; i++ ) {
                    arrayValoracion[i] = []; 
                }

                $('.boton_editar').on('click', function(){

                    var aux = $(this).attr('id').replace('editValoracion_', '');
                    var id = aux.substr(0, aux.indexOf('_'));
                    var pos = aux.indexOf('_')+1;
                    var longitud = aux.length;
                    var tipo_especialidad = aux.substr(pos, longitud-pos);

                    aux = '#fechaValoracion_'+id;
                    var fecha = $(aux).html();
                    //alert(id+' '+fecha+' '+tipo_especialidad);
                    var parametros = {
                        'id': id,
                        'tipo_especialidad': tipo_especialidad,
                        'fecha': fecha
                    };

                    $.ajax({ 
                        type: 'GET',
                        data: parametros,                  
                        url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/valuation_subcriterio.php',                    
                        success:function(data){
                            $('#valoraciones').html(data);
                        },
                        error: function(result) {
                                alert('Data not found');
                        }
                     });                

                });

                function  abrirValoracionContrato(valoracion_id, tipo_especialidad, fecha){
                    var parametros = {
                        'id': valoracion_id,
                        'tipo_especialidad': tipo_especialidad,
                        'fecha': fecha
                    };

                    $.ajax({ 
                        type: 'GET',
                        data: parametros,                  
                        url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/valuation_subcriterio.php',                    
                        success:function(data){
                            $('#valoraciones').html(data);
                        },
                        error: function(result) {
                            alert('Data not found');
                        }
                    });
                }

                function  nuevaValoracionContrato(contrato_id, tipo_especialidad){
                    var parametros = {
                        'contrato_id': contrato_id,
                        'tipo_especialidad': tipo_especialidad
                    };

                    $.ajax({ 
                        type: 'GET',
                        data: parametros,                  
                        url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/valuation_subcriterio.php',                    
                        success:function(data){
                            $('#valoraciones').html(data);
                        },
                        error: function(result) {
                            alert('Data not found');
                        }
                    });
                }
            </script>";

            echo "<div id='valoraciones' align='center' style='min-height: 300px;float: left; position: relative; width:90%; background-color:#e9ecf3; border-radius: 4px; padding: 10px;' >";
            echo "<table id='tablaEvaluaciones' class='display compact' style='margin-top:0px; width:90%;'>";
            echo "<thead>";                                      
            $query = "SELECT  distinct valoracion.* , contrato.tipo_especialidad, proveedor.cv_id as proveedor_cv_id
                            FROM glpi_projecttasks as contrato 
                            left join glpi_plugin_comproveedores_valuations as valoracion  on contrato.id=valoracion.projecttasks_id
                            left join glpi_projecttaskteams as projecttaskteams  on projecttaskteams.projecttasks_id=valoracion.projecttasks_id
                            left join glpi_suppliers as proveedor  on proveedor.id=projecttaskteams.items_id
                            where contrato.id=".$contrato_id." order by valoracion.fecha desc";
            //echo $query;
            $result                             = $DB->query($query);

            $visualizar_cabecera                = true;
            $visualizar_boton_nueva_evaluacion  = true;
            $num_evaluacion                     = $result->num_rows;
            while ($data=$DB->fetch_array($result)) {
                $tipo_especialidad              = $data['tipo_especialidad'];

                //Si existe una valoración final, quitar el boton nueva evaluación
                if($data['evaluacion_final']==1){
                    $visualizar_boton_nueva_evaluacion  = false;
                }

                if($visualizar_cabecera){
                    $visualizar_cabecera    = false;
                        if($tipo_especialidad<3){
                            //CONTRATISTA y PROVEEDOR
                            echo "<th style='width: 80px; background-color:#D8D8D8;'>FINAL</th>";
                            echo "<th style='width: 80px; background-color:#D8D8D8; text-align: center;'>EDIT.</th>";
                            echo "<th style='width: 80px; background-color:#D8D8D8; text-align: center;'>BORR.</th>";
                            echo "<th style='width: 200px; background-color:#D8D8D8;'>FECHA</th>";                            
                            echo "<th style='width: 100px; background-color:#D8D8D8;' title='CALIDAD' >Q</th>";
                            echo "<th style='width: 100px; background-color:#D8D8D8;' title='PLAZOS'>PLZ</th>";
                            echo "<th style='width: 100px; background-color:#D8D8D8;' title='COSTES'>COST</th>";
                            echo "<th style='width: 100px; background-color:#D8D8D8;' title='CULTURA EMPRESARIAL'>CULT</th>";
                            echo "<th style='width: 100px; background-color:#D8D8D8;' title='SUBCONTRATOS'>SUBC</th>";
                            echo "<th style='width: 100px; background-color:#D8D8D8;' title='SEGURIDAD Y SALUD'>SyS</th>";
                            echo "<th style='width: 100px; background-color:#D8D8D8;' title='BIM'>BIM</th>";
                            echo "<th style='width: 100px; background-color:#D8D8D8;' title='CERTIFICACIONES'>CERT</th>";
                        }else{
                            //Servicios Profesionales
                            echo "<th style='width: 80px; background-color:#D8D8D8;'>FINAL</th>";
                            echo "<th style='width: 80px; background-color:#D8D8D8; text-align: center;'>EDIT.</th>";
                            echo "<th style='width: 80px; background-color:#D8D8D8; text-align: center;'>BORR.</th>";
                            echo "<th style='width: 200px; background-color:#D8D8D8;'>FECHA</th>";                                                        
                            echo "<th style='width: 100px; background-color:#D8D8D8;'>PROY BÁSICO</th>";
                            echo "<th style='width: 100px; background-color:#D8D8D8;'>PROY EJECUCIÓN</th>";
                            echo "<th style='width: 100px; background-color:#D8D8D8;'>CAP EMPRESA</th>";
                            echo "<th style='width: 100px; background-color:#D8D8D8;'>COLABORADOR</th>";
                            echo "<th style='width: 100px; background-color:#D8D8D8;'>CAPACIDAD</th>";
                            echo "<th style='width: 100px; background-color:#D8D8D8;'>ACTITUD</th>";
                            echo "<th style='width: 100px; background-color:#D8D8D8;'>BIM</th>";
                        }
                        echo "<th style='width: 100px; background-color:#D8D8D8;'>TOTAL</th>";
                        echo "<th style='width: 200px; background-color:#D8D8D8;'>APTITUD</th>";
                        echo "<th style='width: 800px; background-color:#D8D8D8;'>COMENTARIO</th>";
                    echo "</tr></thead><tbody>";
                }
                
                if(!empty($data['id'])){
                    echo"<tr style='height: 45px;'>";
                    if ($data['evaluacion_final']==1) {
                        echo "<td class='center'><img src='".$CFG_GLPI["root_doc"]."/pics/ok.png' /></td>";
                    }else{
                        echo "<td class='center'> - </td>";                    
                    }
                $strFecha = substr($data['fecha'],8,2).'-'.substr($data['fecha'],5,2).'-'.substr($data['fecha'],0,4);
                //echo "<td class='center'><a href='#' onclick='abrirValoracionContrato(".$data['id'].",".$tipo_especialidad.",'".$strFecha."')' ></td>";
                echo "<td class='center'><input type='submit' title='Editar' id='editValoracion_{$data['id']}_{$tipo_especialidad}' class='boton_editar' value=''/></td>";
                echo "<td class='center'><input type='submit' title='Borrar' id='deleteValoracion_".$data['id']."' class='boton_borrar' value='' style='margin:0px !important;'/></td>";
                $num_evaluacion--;                    
                if($data['fecha']) {
                    echo "<td id='fechaValoracion_{$data['id']}' class='center'>".$strFecha."</td>";                        
                }else{
                    echo "<td class='center'> - </td>";                        
                }
                $elementosValorados = 0;
                $tot = 0;
                if($tipo_especialidad<3){
                             //Contratista y Proveedor
                            if($data['calidad']!=0){
                               $elementosValorados++; 
                               $tot = $tot + $data['calidad'];
                               echo "<td class='muestra_valoracion' style='background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($data['calidad']).".png);'>".str_replace(".", ",", (string) round($data['calidad'],2))."</td>";
                            }else{
                               echo "<td class='center'> - </td>"; 
                            }
                            if($data['planificacion']!=0){
                               $elementosValorados++; 
                               $tot = $tot + $data['planificacion'];
                                echo "<td class='muestra_valoracion' style='background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($data['planificacion']).".png);'>".str_replace(".", ",", (string) round($data['planificacion'],2))."</td>";                               
                            }else{
                               echo "<td class='center'> - </td>"; 
                            }                  
                            if($data['costes']!=0){
                               $elementosValorados++; 
                               $tot = $tot + $data['costes'];
                                echo "<td class='muestra_valoracion' style='background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($data['costes']).".png);'>".str_replace(".", ",", (string) round($data['costes'],2))."</td>";                               
                            }else{
                               echo "<td class='center'> - </td>"; 
                            }           
                            if($data['cultura_empresarial']!=0){
                               $elementosValorados++; 
                               $tot = $tot + $data['cultura_empresarial'];
                                echo "<td class='muestra_valoracion' style='background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($data['cultura_empresarial']).".png);'>".str_replace(".", ",", (string) round($data['cultura_empresarial'],2))."</td>";                               
                            }else{
                               echo "<td class='center'> - </td>"; 
                            }                  
                            if($data['gestion_de_suministros_y_subcontratistas']!=0){
                               $elementosValorados++; 
                               $tot = $tot + $data['gestion_de_suministros_y_subcontratistas'];
                               echo "<td class='muestra_valoracion' style='background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($data['gestion_de_suministros_y_subcontratistas']).".png);'>".str_replace(".", ",", (string) round($data['gestion_de_suministros_y_subcontratistas'],2))."</td>";                               
                            }else{
                               echo "<td class='center'> - </td>"; 
                            }                           
                            if($data['seguridad_y_salud_y_medioambiente']!=0){
                               $elementosValorados++; 
                               $tot = $tot + $data['seguridad_y_salud_y_medioambiente'];
                               echo "<td class='muestra_valoracion' style='background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($data['seguridad_y_salud_y_medioambiente']).".png);'>".str_replace(".", ",", (string) round($data['seguridad_y_salud_y_medioambiente'],2))."</td>";                               
                            }else{
                               echo "<td class='center'> - </td>"; 
                            }                   
                            if($data['bim']!=0){
                               $elementosValorados++; 
                               $tot = $tot + $data['bim'];
                               echo "<td class='muestra_valoracion' style='background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($data['bim']).".png); background-repeat: no-repeat;'>".str_replace(".", ",", (string) round($data['bim'],2))."</td>";                               
                            }else{
                               echo "<td class='center'> - </td>"; 
                            }       
                            if($data['certificacion_medioambiental']!=0){
                               $elementosValorados++; 
                               $tot = $tot + $data['certificacion_medioambiental'];
                               echo "<td class='muestra_valoracion' style='background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($data['certificacion_medioambiental']).".png);'>".str_replace(".", ",", (string) round($data['certificacion_medioambiental'],2))."</td>";                               
                            }else{
                               echo "<td class='center'> - </td>"; 
                            }                             
                            
                    }else{
                            //Servicios Profesioneales
                            
                            if($data['proyecto_basico']!=0){
                               $elementosValorados++; 
                               $tot = $tot + $data['proyecto_basico'];
                               echo "<td class='muestra_valoracion' style='background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($data['proyecto_basico']).".png);'>".str_replace(".", ",", (string) round($data['proyecto_basico'],2))."</td>";                               
                            }else{
                               echo "<td class='center'> - </td>"; 
                            }  
                            if($data['proyecto_de_ejecucion']!=0){
                               $elementosValorados++; 
                               $tot = $tot + $data['proyecto_de_ejecucion'];
                               echo "<td class='muestra_valoracion' style='background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($data['proyecto_de_ejecucion']).".png);'>".str_replace(".", ",", (string) round($data['proyecto_de_ejecucion'],2))."</td>";                               
                            }else{
                               echo "<td class='center'> - </td>"; 
                            }          
                            if($data['capacidad_de_la_empresa']!=0){
                               $elementosValorados++; 
                               $tot = $tot + $data['capacidad_de_la_empresa'];
                               echo "<td class='muestra_valoracion' style='background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($data['capacidad_de_la_empresa']).".png);'>".str_replace(".", ",", (string) round($data['capacidad_de_la_empresa'],2))."</td>";                               
                            }else{
                               echo "<td class='center'> - </td>"; 
                            }          
                            if($data['colaboradores']!=0){
                               $elementosValorados++; 
                               $tot = $tot + $data['colaboradores'];
                               echo "<td class='muestra_valoracion' style='background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($data['colaboradores']).".png);'>".str_replace(".", ",", (string) round($data['colaboradores'],2))."</td>";                               
                            }else{
                               echo "<td class='center'> - </td>"; 
                            }                   
                            if($data['capacidad']!=0){
                               $elementosValorados++; 
                               $tot = $tot + $data['capacidad'];
                               echo "<td class='muestra_valoracion' style='background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($data['capacidad']).".png);'>".str_replace(".", ",", (string) round($data['capacidad'],2))."</td>";                               
                            }else{
                               echo "<td class='center'> - </td>"; 
                            }                             
                            if($data['actitud']!=0){
                               $elementosValorados++; 
                               $tot = $tot + $data['actitud'];
                               echo "<td class='muestra_valoracion' style='background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($data['actitud']).".png);'>".str_replace(".", ",", (string) round($data['actitud'],2))."</td>";                               
                            }else{
                               echo "<td class='center'> - </td>"; 
                            }                  
                            if($data['bim']!=0){
                               $elementosValorados++; 
                               $tot = $tot + $data['bim'];
                               echo "<td class='muestra_valoracion' style='background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($data['bim']).".png);'>".str_replace(".", ",", (string) round($data['bim'],2))."</td>";                               
                            }else{
                               echo "<td class='center'> - </td>"; 
                            }   
                            
/**                            echo "<td class='muestra_valoracion' style='background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($data['proyecto_basico']).".png);'>";
                                echo str_replace(".", ",", (string) round($data['proyecto_basico'],2));
                            echo "</td>";
                            echo "<td class='muestra_valoracion' style='background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($data['proyecto_de_ejecucion']).".png);'>".rtrim($data['proyecto_de_ejecucion'], '.0')."</td>";
                            echo "<td class='muestra_valoracion' style='background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($data['capacidad_de_la_empresa']).".png);'>".rtrim($data['capacidad_de_la_empresa'], '.0')."</td>";
                            echo "<td class='muestra_valoracion' style='background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($data['colaboradores']).".png);'>".rtrim($data['colaboradores'], '.0')."</td>";
                            echo "<td class='muestra_valoracion' style='background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($data['capacidad']).".png);'>".rtrim($data['capacidad'], '.0')."</td>";
                            echo "<td class='muestra_valoracion' style='background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($data['actitud']).".png);'>".rtrim($data['actitud'], '.0')."</td>";
                            echo "<td class='muestra_valoracion' style='background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($data['bim']).".png)'>".rtrim($data['bim'], '.0')."</td>";
 **/ 
 
                    }
                    
                    $valor = $tot/$elementosValorados;
                    $valorSTR = str_replace(".", ",", (string) round($valor,2));                    
                    //echo "<td class='center' style='border: 0px solid #BDBDDB;'>".$valor."</td>";
                    echo "<td class='muestra_valoracion' style='background-size: 30px; background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$this->getColorValoracion($valor).".png);'>".$valorSTR."</td>";
                    echo "<td class='left'>".$this->recomendacionesEvaluacion($valor)."</td>";
                    echo "<td class='left'>".$data['comentario']."</td>";

                   /** echo "<td style='border: 0px solid #BDBDDB;'>";
                        echo "<form action=".$CFG_GLPI["root_doc"]."/plugins/comproveedores/front/valuation.form.php method='post'>";
                            echo Html::hidden('_glpi_csrf_token', array('value' => Session::getNewCSRFToken()));
                            echo Html::hidden('id', array('value' =>$data['id']));
                            echo"<input type='submit' class='boton_borrar' name='delete_evaluacion' value='' style='border: none; width: 20px;height: 20px;background-size: 15px;'/>";
                        echo "</form>";
                    echo "</td>";**/

                    echo"</tr>";
                }
            }
            echo "</tbody>";
            echo "</table>";

            if($visualizar_boton_nueva_evaluacion){
                    echo "<div>";
                        //echo "<span onclick='nuevaValoracionContrato(".$contrato_id.", ".$tipo_especialidad.")' class='boton_valoracion' style='margin-right: 15px;'>NUEVA EVALUACIÓN ".$tipo_especialidad."</span>";
                        echo "<input id='botonValoracion' value='' style='height: 35px; width: 35px; background-size: 30px;' title='NUEVA EVALUACIÓN' class='boton_valoracion' type='submit' onclick='nuevaValoracionContrato(".$contrato_id.", ".$tipo_especialidad.")' />";
                    echo "</div>";
            }


            echo "<div align='center' style='margin-top: 10px;'><table>";
                echo "<tr>";
                    echo "<td class='center'>Tipología de las calificaciones -></td>";                       
                    echo "<td class='center'><img style='vertical-align:middle; margin: 5px 0px; width: 20px; height: 20px;' src=".$CFG_GLPI["root_doc"]."/pics/valoracion_1.png></td>";                                                             
                    echo "<td  style='width: 50px;'>MALA</td>";
                    echo "<td class='center'><img style='vertical-align:middle; margin: 5px 0px; width: 20px; height: 20px;' src=".$CFG_GLPI["root_doc"]."/pics/valoracion_2.png></td>";                                                            
                    echo "<td  style='width: 50px;'>POBRE</td>";
                    echo "<td class='center'><img style='vertical-align:middle; margin: 5px 0px; width: 20px; height: 20px;' src=".$CFG_GLPI["root_doc"]."/pics/valoracion_3.png></td>";                                                            
                    echo "<td  style='width: 50px;'>ACEPTABLE</td>";
                    echo "<td class='center'><img style='vertical-align:middle; margin: 5px 0px; width: 20px; height: 20px;' src=".$CFG_GLPI["root_doc"]."/pics/valoracion_4.png></td>";                                                            
                    echo "<td  style='width: 50px;'>BUENA</td>";
                    echo "<td class='center'><img style='vertical-align:middle; margin: 5px 0px; width: 20px; height: 20px;' src=".$CFG_GLPI["root_doc"]."/pics/valoracion_5.png></td>";                                                            
                    echo "<td  style='width: 50px;'>EXCELENTE</td>";
                echo "</tr>";
            echo "</table></div>";

            echo "</div>";
             
            //Obtenemos el CV_Id del contrato
            $cv_id='';

            $query2 ="select proveedor.cv_id 
                            from glpi_projecttaskteams as projecttaskteams
                            inner join glpi_suppliers as proveedor on projecttaskteams.items_id=proveedor.id 
                            where  projecttasks_id=".$contrato_id;

            $result2 = $DB->query($query2);

            while ($data=$DB->fetch_array($result2)) {
                    $cv_id=$data['cv_id'];
            }

            echo"<div id='evaluacion'>";
                    echo Html::hidden('cv_id', array('value' =>$cv_id));
            echo"</div>";
            echo "<script type='text/javascript'>
                    jQuery.extend( jQuery.fn.dataTableExt.oSort, {
                        'date-uk-pre': function ( a ) {
                            var ukDatea = a.split('-');
                            return (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
                        },
                        'date-uk-asc': function ( a, b ) {
                            return ((a < b) ? -1 : ((a > b) ? 1 : 0));
                        },
                        'date-uk-desc': function ( a, b ) {
                            return ((a < b) ? 1 : ((a > b) ? -1 : 0));
                        }
                    } );  
                    
                    $('#tablaEvaluaciones').DataTable({
                        'searching':      true,
                        'scrollY':        '250px',
                        'scrollCollapse': true,
                        'paging':         false,
                        'language': {'url': '".$CFG_GLPI["root_doc"]."/lib/jqueryplugins/Spanish.json'},
                        'dom': 'Bfrtip',
                        'buttons': [
                            'copyHtml5',
                            'excelHtml5',
                            'pdfHtml5'
                        ],                  
                        'order': [[ 3, 'desc' ]]                            
                    });
                    
                    $('.boton_borrar').on('click', function(){
                        var resp = confirm('¿Realmente desea quitar esta valoración?','Quitar valoración');
                        if(resp){
                            var idvaloracion = $(this).prop('id').replace('deleteValoracion_','');
                            //alert(idvaloracion);
                            $.ajax({ 
                                    async: false, 
                                    type: 'GET',
                                    data: {'id': idvaloracion},                  
                                    url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/quitarValidaciones.php',  				
                                    success:function(data){
                                            window.location.reload(true);
                                            //alert(data);
                                    },
                                    error: function(result) {
                                            alert('Data not found!');
                                    }
                            });                         
                        }
                    });
                    
            </script>";
	}
           
        function modificarValoracionPaquete($valoracion, $paquete_id){
            GLOBAL $DB,$CFG_GLPI;

            echo $this->consultaAjax();     

            //formato de fecha yyyy-mm-dd
            $_SESSION['glpidate_format']=0;
            echo "<div id='fecha_valoracion_".$valoracion."' style='text-align:left; display: -webkit-box;'>";
                        echo"<div style='margin-right:10px; position: relative; top: 3px;'>Fecha de valoración</div>";
                        echo"<div>";
                        Html::showDateTimeField("fecha");
                        echo"</div>";
            echo"</div>";
            echo "<div align='center'><table class='tab_cadre_fixehov'>";

            echo "<tr class='tab_bg_$valoracion tab_cadre_fixehov nohover'><th colspan='14' >Evaluación ".$valoracion."</th></tr>";
            //echo"<br/>";
            echo "<tr><th></th>";
                echo "<th style='width: 100px; background-color:#D8D8D8;'>".__('Mal')."</th>";
                echo "<th style='width: 100px; background-color:#D8D8D8;'>".__('Pobre')."</th>";
                echo "<th style='width: 100px; background-color:#D8D8D8;'>".__('Adecuado')."</th>";
                echo "<th style='width: 100px; background-color:#D8D8D8;'>".__('Bien')."</th>";
                echo "<th style='width: 100px; background-color:#D8D8D8;'>".__('Excelente')."</th>";
                echo "<th style='width: 100px; background-color:#D8D8D8;'>".__('Comentario')."</th>";
            echo "</tr>";

            $arrayValoraciones[0]=['calidad', 'Calidad'];
            $arrayValoraciones[1]=['plazo', 'Plazo'];
            $arrayValoraciones[2]=['costes', 'Costes'];
            $arrayValoraciones[3]=['cultura', 'Cultura'];
            $arrayValoraciones[4]=['suministros_y_subcontratistas', 'Suministros y Subcontratistas'];
            $arrayValoraciones[5]=['sys_y_medioambiente', 'Sys y Medioambiente'];

            foreach ($arrayValoraciones as $key => $value) {

                echo "<tr class='tab_bg_2' style='height:60px;'>";
                    echo"<td  class='center' id='criterio_".$key."_0_valoracion_$valoracion' style='width: 100px; background-color:#D8D8D8;'>$value[1]</td>";
                    echo"<td class='center' id='criterio_".$key."_1_valoracion_$valoracion' style='width: 100px; font-weight:bold;' onclick='valorElegido(1,$key,$valoracion)'></td>";
                    echo"<td class='center' id='criterio_".$key."_2_valoracion_$valoracion' style='width: 100px; font-weight:bold;' onclick='valorElegido(2, $key,$valoracion)'></td>";
                    echo"<td class='center' id='criterio_".$key."_3_valoracion_$valoracion' style='width: 100px; font-weight:bold;' onclick='valorElegido(3,$key,$valoracion)'></td>";
                    echo"<td class='center' id='criterio_".$key."_4_valoracion_$valoracion' style='width: 100px; font-weight:bold;' onclick='valorElegido(4,$key,$valoracion)'></td>";
                    echo"<td class='center' id='criterio_".$key."_5_valoracion_$valoracion' style='width: 100px; font-weight:bold;' onclick='valorElegido(5,$key,$valoracion)'></td>";
                    echo"<td class='center' id='criterio_".$key."_comentario_valoracion_$valoracion' style='width: 100px; font-weight:bold;'><textarea rows='4' cols='45' style='resize: none'></textarea></td>";
                echo "</tr>";    
            }

            //echo"<br/>";
            echo "</table></div>";

            $query ="SELECT  distinct * 
                FROM glpi_plugin_comproveedores_valuations as valoracion
                where valoracion.projecttasks_id=".$paquete_id." and valoracion.num_evaluacion=".$valoracion;

            $result = $DB->query($query);
            $contenido_valoracion=1;             
            while ($data=$DB->fetch_array($result)) {

                //Creamos un script donde se cagarán los valores de la consulta
                echo"<script type='text/javascript'>      
                       $( function() {";

                            foreach ($arrayValoraciones as $key => $value) {
                                $valor=$data[$value[0]];                                                
                                echo "$('#criterio_".$key."_comentario_valoracion_".$valoracion."').find('textarea').html('".$data[$value[0].'_coment']."');";
                                echo"valorElegido($valor, $key, $valoracion);";
                            } 

                        //Les pasamos el valor a los input de fecha de valoracion
                        echo"$('#fecha_valoracion_".$valoracion."').find('input[name=_fecha]').val('".$data['fecha']."');";    
                        echo"$('#fecha_valoracion_".$valoracion."').find('input[name=fecha]').val('".$data['fecha']."');";     
                echo"});</script>";


                $valoracion_id=$data['id'];   
            }

            //echo "<br><br>";
            echo"<div  id='boton_guardar_$valoracion'>";
            if($result->num_rows!=0){ 

                echo "<span onclick='guardarYModificarValoracion($paquete_id,$valoracion,$valoracion_id,\"update_valoracion\")' class='vsubmit' style='margin-right: 15px;'>MODIFICAR VALORACIÓN</span>";
            }  
            else{

                echo "<span onclick='guardarYModificarValoracion($paquete_id,$valoracion,-1,\"add_valoracion\")'class='vsubmit' style='margin-right: 15px;'>GUARDAR VALORACIÓN</span>";      
            }    
            echo"</div>";
            //echo"<br>";
            //echo"<br>";
        }
                
        function formatoFecha($f){
            $anio     = substr($f, 0, 4);
            $mes      = substr($f, 5, 2);
            $dia      = substr($f, 8, 2);
            return $dia."/".$mes."/".$anio;
        }
        
        function showForm(){
            GLOBAL $DB,$CFG_GLPI;

            echo"<script type='text/javascript'>                                           

                var arrayValoracion = [];

                for ( var i = 1; i <=3; i++ ) {
                    arrayValoracion[i] = []; 
                }
            </script>";

            echo $this->consultaAjax();

            $valoracion=0;
            $paquete_id=0;
            $query2 ="SELECT  distinct valoracion.num_evaluacion, valoracion.projecttasks_id 
                    FROM glpi_plugin_comproveedores_valuations as valoracion
                    where valoracion.id=".$_GET['id'];

            $result2 = $DB->query($query2);

            while ($data=$DB->fetch_array($result2)) {
                $valoracion=$data['num_evaluacion'];
                $paquete_id=$data['projecttasks_id'];
            }

            //formato de fecha yyyy-mm-dd
            $_SESSION['glpidate_format']=0;
            echo "<div id='fecha_valoracion_".$valoracion."' style='text-align:left; display: -webkit-box;'>";
                        echo"<div style='margin-right:10px; position: relative; top: 3px;'>Fecha de valoración</div>";
                        echo"<div>";
                        Html::showDateTimeField("fecha");
                        echo"</div>";
            echo"</div>";

            echo "<div align='center'><table class='tab_cadre_fixehov'>";
            echo "<tr class='tab_bg_$valoracion tab_cadre_fixehov nohover'><th colspan='14' >Evaluación ".$valoracion."</th></tr>";
            //echo"<br/>";
            echo "<tr><th></th>";
                echo "<th style='width: 100px; background-color:#D8D8D8;'>".__('Mal')."</th>";
                echo "<th style='width: 100px; background-color:#D8D8D8;'>".__('Pobre')."</th>";
                echo "<th style='width: 100px; background-color:#D8D8D8;'>".__('Adecuado')."</th>";
                echo "<th style='width: 100px; background-color:#D8D8D8;'>".__('Bien')."</th>";
                echo "<th style='width: 100px; background-color:#D8D8D8;'>".__('Excelente')."</th>";
                echo "<th style='width: 100px; background-color:#D8D8D8;'>".__('Comentario')."</th>";
            echo "</tr>";

            $arrayValoraciones[0]=['calidad', 'Calidad'];
            $arrayValoraciones[1]=['plazo', 'Plazo'];
            $arrayValoraciones[2]=['costes', 'Costes'];
            $arrayValoraciones[3]=['cultura', 'Cultura'];
            $arrayValoraciones[4]=['suministros_y_subcontratistas', 'Suministros y Subcontratistas'];
            $arrayValoraciones[5]=['sys_y_medioambiente', 'Sys y Medioambiente'];




            foreach ($arrayValoraciones as $key => $value) {

                echo "<tr class='tab_bg_2' style='height:60px;'>";
                    echo"<td class='center' id='criterio_".$key."_0_valoracion_$valoracion' style='background-color:#D8D8D8;'>$value[1]</td>";
                    echo"<td class='center' id='criterio_".$key."_1_valoracion_$valoracion' style='font-weight:bold;' onclick='valorElegido(1,$key,$valoracion)'></td>";
                    echo"<td class='center' id='criterio_".$key."_2_valoracion_$valoracion' style='font-weight:bold;' onclick='valorElegido(2, $key,$valoracion)'></td>";
                    echo"<td class='center' id='criterio_".$key."_3_valoracion_$valoracion' style='font-weight:bold;' onclick='valorElegido(3,$key,$valoracion)'></td>";
                    echo"<td class='center' id='criterio_".$key."_4_valoracion_$valoracion' style='font-weight:bold;' onclick='valorElegido(4,$key,$valoracion)'></td>";
                    echo"<td class='center' id='criterio_".$key."_5_valoracion_$valoracion' style='font-weight:bold;' onclick='valorElegido(5,$key,$valoracion)'></td>";
                    echo"<td class='center' id='criterio_".$key."_comentario_valoracion_$valoracion' style='font-weight:bold;'><textarea rows='4' cols='45' style='resize: none'></textarea></td>";
                echo "</tr>";    
            }

            //echo"<br/>";
            echo "</table></div>";

            $query ="SELECT * 
                    FROM glpi_plugin_comproveedores_valuations as valoracion
                    where valoracion.projecttasks_id=".$paquete_id." and valoracion.num_evaluacion=".$valoracion;


            $result = $DB->query($query);
            $contenido_valoracion=1;             
            while ($data=$DB->fetch_array($result)) {

                //Creamos un script donde se cagarán los valores de la consulta
                echo"<script type='text/javascript'>      
                           $( function() {";

                    foreach ($arrayValoraciones as $key => $value) {
                            $valor=$data[$value[0]];
                            echo "$('#criterio_".$key."_comentario_valoracion_".$valoracion."').find('textarea').html('".$data[$value[0].'_coment']."');";
                            echo"valorElegido($valor, $key, $valoracion);";
                    } 

                     //Les pasamos el valor a los input de fecha de valoración
                    echo"$('#fecha_valoracion_".$valoracion."').find('input[name=_fecha]').val('".$data['fecha']."');";    
                    echo"$('#fecha_valoracion_".$valoracion."').find('input[name=fecha]').val('".$data['fecha']."');";    
                echo"});</script>";
                $valoracion_id=$data['id'];   
            }

            //echo "<br><br>";
            echo"<div  id='boton_guardar_$valoracion'>";
            if($result->num_rows!=0){ 
                echo "<span onclick='guardarYModificarValoracion($paquete_id,$valoracion,$valoracion_id,\"update_valoracion\")' class='vsubmit' style='margin-right: 15px;'>MODIFICAR VALORACIÓN</span>";
            }  
            else{

                echo "<span onclick='guardarYModificarValoracion($paquete_id,$valoracion,-1,\"add_valoracion\")'class='vsubmit' style='margin-right: 15px;'>GUARDAR VALORACIÓN</span>";      
            }    
            echo"</div>";
            //echo"<br>";
            //echo"<br>";
        }    

        function getSupplierByUserID($Id){
                global $DB;
                $options=array();
                $query ="SELECT supplier_id as cv FROM glpi_users WHERE id=$Id";

                $result=$DB->query($query);
                $id=$DB->fetch_array($result);

                if($id['cv']<>''){
                        $options['id']=$id['cv'];
                }

                return $options['id'];
        }
                
        function getProfileByUserID($Id){
                global $DB;

                $query ="SELECT profiles_id as profile FROM glpi_users WHERE id=$Id";

                $result=$DB->query($query);
                $id=$DB->fetch_array($result);

                if($id['profile']<>''){
                        $options['profile']=$id['profile'];
                }
                return $options['profile'];
        }

        function showFormItemValuationProveedor($item, $withtemplate='') {	
            GLOBAL $DB,$CFG_GLPI;

            $CvId=$item->fields['cv_id']; 
            $USERID = $_SESSION['glpiID'];
            $self = new self();
            $profile_Id=$self->getProfileByUserID($USERID);
            
            if(in_array($profile_Id, array(3,4,14,16))){
                $query ="Select distinct p.id as proyectoid,
                    p.name as proyecto,
                    contratos.id as contratoid, 
                    contratos.name as contrato,
                    contratos.tipo_especialidad, 
                    ult_calidad(contratos.id) as calidad, 
                    ult_planificacion(contratos.id) as planificacion, 
                    ult_costes(contratos.id) as costes, 
                    ult_cultura_empresarial(contratos.id) as cultura_empresarial, 
                    ult_gestion_de_suministros_y_subcontratistas(contratos.id) as gestion_de_suministros_y_subcontratistas, 
                    ult_seguridad_y_salud_y_medioambiente(contratos.id) as seguridad_y_salud_y_medioambiente, 
                    ult_bim(contratos.id) as bim, 
                    ult_certificacion_medioambiental (contratos.id) as certificacion_medioambiental, 
                    ult_proyecto_basico (contratos.id) as proyecto_basico, 
                    ult_proyecto_de_ejecucion (contratos.id) as proyecto_de_ejecucion, 
                    ult_capacidad_de_la_empresa (contratos.id) as capacidad_de_la_empresa, 
                    ult_colaboradores (contratos.id) as colaboradores, 
                    ult_actitud(contratos.id) as actitud, 
                    ult_fecha(contratos.id) as fecha, 
                    ult_comentario(contratos.id) as comentario, 
                    ult_evaluacion_final(contratos.id) as evaluacion_final 
                    from glpi_projecttasks as contratos 
                    left join glpi_projecttaskteams as teams on teams.projecttasks_id = contratos.id
                    left join glpi_suppliers as sup on sup.id = teams.items_id
                    left join glpi_plugin_comproveedores_valuations as valoraciones on valoraciones.projecttasks_id=contratos.id and valoraciones.cv_id={$CvId} 
                    left join glpi_projects as p on p.id = contratos.projects_id 
                    where ult_evaluacion_final(contratos.id) in (0,1) and sup.cv_id = {$CvId} ";

                $result = $DB->query($query);
                //echo $query;
                //Nos creamos 2 array, uno para la tabla Servicios profesionales y otro para Contratistas
                $arrayServicioProfesionales=[];
                $arrayContratistas=[];
                while ($data=$DB->fetch_array($result)) {
                    if($data['tipo_especialidad']==3){
                                     $arrayServiciosProfesionales[]=$data;
                    }
                    if($data['tipo_especialidad']<3){
                                    $arrayContratistas[]=$data;
                    }
                }

                echo "<style>
                    .cabecera{
                        background-color:#D8D8D8;
                        padding: 10px;
                    }
                    .fila {
                        font-weight: normal;
                        color: #888 ;  
                        background-repeat: no-repeat;
                        background-position: center;
                        background-size: 20px;
                        font-size: 9px;    
                        padding: 5px;
                    }
                    .uno {
                        background-color: #e9ecf3; width:98%; height: 500px; margin-top: 0px; overflow-y:auto; padding: 10px;
                    }
                </style>";

                
                echo "<div class='uno'>";
                echo "<div id='acordeon'>";
                echo "<h3 style='text-align: left;'>EVALUACIONES COMO CONTRATISTA</h3>";
                echo "<div align='center'>";
                echo "<table id='tblContratistas' class='display compact' cellspacing='0' class='tablaEval' style='width:100%;'>";
                echo "<thead>";
                echo "<tr>";
                        echo "<th class='cabecera' style='width: 300px;'>PROYECTO</th>";
                        echo "<th class='cabecera' style='width: 300px;'>CONTRATO</th>";
                        echo "<th class='cabecera' >FINAL</th>";
                        echo "<th class='cabecera' style='width: 100px;'>FECHA</th>";
                        echo "<th class='cabecera' title='SISTEMAS DE CALIDAD'>".__('Q.')."</th>";
                        echo "<th class='cabecera' title='PLAZOS DE ENTREGA'>".__('PLZ.')."</th>";
                        echo "<th class='cabecera' title='COSTES'>".__('COST.')."</th>";
                        echo "<th class='cabecera' title='CULTURA EMPRESARIAL'>".__('CULT.')."</th>";
                        echo "<th class='cabecera' title='SUBCONTRATAS'>".__('SUBC.')."</th>";
                        echo "<th class='cabecera' title='SEGURIDAD Y SALUD'>".__('SYS.')."</th>";                                                                                
                        echo "<th class='cabecera' title='BIM'>".__('BIM')."</th>";                                                                                
                        echo "<th class='cabecera' title='CERTIFICACIONES'>".__('CERT.')."</th>";
                        echo "<th class='cabecera' title='MEDIA DE LOS VALORES'>".__('MEDIA')."</th>";
                        echo "<th class='cabecera' title='COMENTARIOS' style='width: 400px;'>".__('COMENTARIOS')."</th>";
                echo "</tr></thead>";
                echo "<tbody>";
                                   
                $strPath = $CFG_GLPI["root_doc"]."/pics/valoracion_";
                foreach ($arrayContratistas as $contratista) {
                    $media = 0;
                    $acum = 0;
                    $num = 0;
                    echo "<tr style='height: 35px;' class='tab_bg_2'>";
                        echo "<td class='left'><a href='project.form.php?id={$contratista['proyectoid']}' data-hasqtip='0' aria-descripbedby='qtip-0' >".$contratista['proyecto']."</a></td>";
                        echo "<td class='left'>";
                            echo"<div><a href='projecttask.form.php?id={$contratista['contratoid']}' data-hasqtip='0' aria-descripbedby='qtip-0'>".$contratista['contrato']."</a></div>";
                        echo"</td>";
                        echo "<td class='center'>";
                            if($contratista['evaluacion_final']==1){
                                echo "<img src='../pics/ok.png' style='width: 15px;'/>";
                            }else{
                                echo "-";
                            }
                        echo"</td>";                            
                        echo "<td class='center'>";
                            $fecha = new Datetime($contratista['fecha']);
                            echo $fecha->format('d-m-Y');
                        echo"</td>";
                        if($contratista['calidad']>0){
                            echo "<td class='center fila' style='background-image: url(".$strPath.$this->getColorValoracion($contratista['calidad']).".png);'>";
                            echo number_format($contratista['calidad'], 2);
                            $acum = $acum + $contratista['calidad'];
                            $num = $num + 1;
                        }else{
                            echo "<td class='center fila'>";
                        }
                        echo "</td>";
                        if($contratista['planificacion']>0){
                            echo "<td class='center fila' style='background-image: url(".$strPath.$this->getColorValoracion($contratista['planificacion']).".png);'>";                                
                            echo number_format($contratista['planificacion'],2);
                            $acum = $acum + $contratista['planificacion'];
                            $num = $num + 1;
                        }else{
                            echo "<td class='center fila'>";
                        }
                        echo "</td>";
                        if($contratista['costes']>0){
                            echo "<td class='center fila' style='background-image: url(".$strPath.$this->getColorValoracion($contratista['costes']).".png);'>";
                            echo number_format($contratista['costes'],2);
                            $acum = $acum + $contratista['costes'];
                            $num = $num + 1;
                        }else{
                            echo "<td class='center fila'>";
                        }
                        echo "</td>";
                        if($contratista['cultura_empresarial']>0){
                            echo "<td class='center fila' style='background-image: url(".$strPath.$this->getColorValoracion($contratista['cultura_empresarial']).".png);'>";                                
                            echo number_format($contratista['cultura_empresarial'],2);
                            $acum = $acum + $contratista['cultura_empresarial'];
                            $num = $num + 1;
                        }else{
                            echo "<td class='center fila'>";
                        }                              
                        echo "</td>";
                        if($contratista['gestion_de_suministros_y_subcontratistas']>0){
                            echo "<td class='center fila' style='background-image: url(".$strPath.$this->getColorValoracion($contratista['gestion_de_suministros_y_subcontratistas']).".png);'>";                                
                            echo number_format($contratista['gestion_de_suministros_y_subcontratistas'],2);
                            $acum = $acum + $contratista['gestion_de_suministros_y_subcontratistas'];
                            $num = $num + 1;
                        }else{
                            echo "<td class='center fila'>";
                        }                                
                        echo "</td>";
                        if($contratista['seguridad_y_salud_y_medioambiente']>0){
                            echo "<td class='center fila' style='background-image: url(".$strPath.$this->getColorValoracion($contratista['seguridad_y_salud_y_medioambiente']).".png);'>";                                
                            echo number_format($contratista['seguridad_y_salud_y_medioambiente'],2);
                            $acum = $acum + $contratista['seguridad_y_salud_y_medioambiente'];
                            $num = $num + 1;
                        }else{
                            echo "<td class='center fila'>";
                        }                               
                        echo "</td>";
                        if($contratista['bim']>0){
                            echo "<td class='center fila' style='background-image: url(".$strPath.$this->getColorValoracion($contratista['bim']).".png);'>";                                
                            echo number_format($contratista['bim'],2);
                            $acum = $acum + $contratista['bim'];
                            $num = $num + 1;
                        }else{
                            echo "<td class='center fila'>";
                        }                       
                        echo "</td>";
                        if($contratista['certificacion_medioambiental']>0){
                            echo "<td class='center fila' style='background-image: url(".$strPath.$this->getColorValoracion($contratista['certificacion_medioambiental']).".png);'>";                                
                            echo number_format($contratista['certificacion_medioambiental'],2);
                            $acum = $acum + $contratista['certificacion_medioambiental'];
                            $num = $num + 1;
                         }else{
                            echo "<td class='center fila'>";
                        }                                 
                        echo "</td>";
                        $media = round($acum/$num,2);                    
                        echo "<td class='center fila' style='background-image: url(".$strPath.$this->getColorValoracion($media).".png); background-size: 30px; color: #000;'>{$media}</td>";
                        echo "<td class='center fila' style='background-repeat: no-repeat;  background-position: center; background-size: 20px; font-size: 10px; text-align: justify; width: 400px; padding: 4px;'>".$contratista['comentario']."</td>";                    
                    echo"</tr>";
                }

                echo "</tbody></table></div>";
                
                $media = 0;
                $acum = 0;
                $num = 0;                    
                echo "<h3 style='text-align: left;'>EVALUACIONES COMO SERVICIOS PROFESIONALES</h3>";
                echo "<div align='center'>";
                echo "<table id='tblServiciosProfesionales' class='display compact' cellspacing='0' style='width:100%;'><thead>";
                echo "<tr>";
                        echo "<th class='cabecera' style='min-width: 80px;width: 300px;'>PROYECTO</th>";
                        echo "<th class='cabecera' style='min-width: 80px;width: 300px;'>CONTRATO</th>";
                        echo "<th class='cabecera' >FINAL</th>";
                        echo "<th class='cabecera' style='width: 100px;'>FECHA</th>";
                        echo "<th class='cabecera' style='width: 100px;'>".__('PROY BÁSICO')."</th>";
                        echo "<th class='cabecera' style='width: 100px;'>".__('PROY EJECUCIÓN')."</th>";
                        echo "<th class='cabecera' style='width: 100px;'>".__('CAP EMPRESA')."</th>";
                        echo "<th class='cabecera' style='width: 100px;'>".__('COLABORADOR')."</th>";
                        echo "<th class='cabecera' style='width: 100px;'>".__('CAPACIDAD')."</th>";
                        echo "<th class='cabecera' style='width: 100px;'>".__('ACTITUD')."</th>";                                                                                
                        echo "<th class='cabecera' style='width: 100px;'>".__('BIM')."</th>";
                        echo "<th class='cabecera' style='width: 100px;'>".__('MEDIA')."</th>";                    
                        echo "<th class='cabecera' style='width: 100px;'>".__('COMENTARIOS')."</th>";
                echo "</tr></thead><tbody>";
                 
                foreach ($arrayServiciosProfesionales as $servicioProfesional) {
                    $media = 0;  
                    $acum = 0;
                    $num = 0;  
                    echo "<tr style='height: 35px;' class='tab_bg_2'>";
                        echo "<td class='left'><a href='project.form.php?id={$servicioProfesional['proyectoid']}' data-hasqtip='0' aria-descripbedby='qtip-0' >".$servicioProfesional['proyecto']."</a></td>";
                        echo "<td class='left'>";
                            echo "<a href='projecttask.form.php?id={$servicioProfesional['contratoid']}' data-hasqtip='0' aria-descripbedby='qtip-0'>".$servicioProfesional['contrato']."</a>";
                        echo "</td>";
                        echo "<td class='center'>";
                            if($servicioProfesional['evaluacion_final']==1){
                                echo "<img src='../pics/ok.png' style='width: 15px;'/>";
                            }else{
                                echo "-";
                            }
                        echo"</td>";       
                        echo "<td class='center'>";
                            $fecha = new Datetime($servicioProfesional['fecha']);
                            echo $fecha->format('d-m-Y');                            
                        echo"</td>";                            
                        if($servicioProfesional['proyecto_basico']>0 && $servicioProfesional['proyecto_basico'] != null){
                            echo "<td class='center fila' style='background-image: url(".$strPath.$this->getColorValoracion($servicioProfesional['proyecto_basico']).".png);'>";
                            echo number_format($servicioProfesional['proyecto_basico'],0);
                            $acum = $acum + $servicioProfesional['proyecto_basico'];
                            //echo $acum;
                            $num++;
                        }else{
                            echo "<td class='center fila'> -";
                        }                            
                        if($servicioProfesional['proyecto_de_ejecucion']>0 && $servicioProfesional['proyecto_de_ejecucion'] != null){
                            echo "<td class='center fila' style='background-image: url(".$strPath.$this->getColorValoracion($servicioProfesional['proyecto_de_ejecucion']).".png);'>";
                            echo number_format($servicioProfesional['proyecto_de_ejecucion'],0);
                            $acum = $acum + $servicioProfesional['proyecto_de_ejecucion'];
                            //echo $acum;
                            $num++;
                        }else{
                            echo "<td class='center fila'> -";
                        }  
                        if($servicioProfesional['capacidad_de_la_empresa']>0 && $servicioProfesional['capacidad_de_la_empresa'] != null){
                            echo "<td class='center fila' style='background-image: url(".$strPath.$this->getColorValoracion($servicioProfesional['capacidad_de_la_empresa']).".png);'>";
                            echo number_format($servicioProfesional['capacidad_de_la_empresa'],0);
                            $acum = $acum + $servicioProfesional['capacidad_de_la_empresa'];
                            $num++;
                        }else{
                            echo "<td class='center fila'> -";
                        }
                        if($servicioProfesional['colaboradores']>0 && $servicioProfesional['colaboradores'] != null){
                            echo "<td class='center fila' style='background-image: url(".$strPath.$this->getColorValoracion($servicioProfesional['colaboradores']).".png);'>";
                            echo number_format($servicioProfesional['colaboradores'],0);
                            $acum = $acum + $servicioProfesional['colaboradores'];
                            $num++;
                        }else{
                            echo "<td class='center fila'> - ";
                        }
                        if($servicioProfesional['capacidad']>0 && $servicioProfesional['capacidad'] != null){
                            echo "<td class='center fila' style='background-image: url(".$strPath.$this->getColorValoracion($servicioProfesional['capacidad']).".png);'>";
                            echo number_format($servicioProfesional['capacidad'],0);
                            $acum = $acum + $servicioProfesional['capacidad'];
                            $num++;
                        }else{
                            echo "<td class='center fila'>-";
                        }
                        if($servicioProfesional['actitud']>0 && $servicioProfesional['actitud'] != null){
                            echo "<td class='center fila' style='background-image: url(".$strPath.$this->getColorValoracion($servicioProfesional['actitud']).".png);'>";
                            echo number_format($servicioProfesional['actitud'],0);
                            $acum = $acum + $servicioProfesional['actitud'];
                            $num++;
                        }else{
                            echo "<td class='center fila'> -";
                        }
                        if($servicioProfesional['bim']>0 && $servicioProfesional['bim'] != null){
                            echo "<td class='center fila' style='background-image: url(".$strPath.$this->getColorValoracion($servicioProfesional['bim']).".png);'>";
                            echo number_format($servicioProfesional['bim'],0);
                            $acum = $acum + $servicioProfesional['bim'];
                            $num++;
                        }else{
                            echo "<td class='center fila'>-";
                        }
                        $media = round($acum/$num,2); 
                        echo "<td class='center fila' style='background-image: url(".$strPath.$this->getColorValoracion($media).".png); background-size: 30px; color: #000;'>$media</td>";
                        echo "<td class='center fila' style='background-repeat: no-repeat;  background-position: center;'>".$servicioProfesional['comentarios']."</td>";
                    echo "</tr>";
                }
            echo "</tbody></table></div>";                

            $query3 ="SELECT distinct prj.id as proyectoid, 
                prj.name as proyecto, 
                contrato.id as contratoid,
                contrato.name as contrato, 
                subp.id as subcontratoid, 
                subp.name as subcontrato, 
                subp.valoracion as valoracion, 
                subp.suppliers_id as proveedorid
            FROM  glpi_plugin_comproveedores_subpaquetes as subp 
                left join glpi_suppliers as s on s.id = subp.suppliers_id
                left join glpi_projecttasks as contrato on contrato.id = subp.projecttasks_id		
                left join glpi_projects as prj on prj.id = contrato.projects_id
            WHERE  subp.suppliers_id = {$item->fields['id']}";
            $result3 = $DB->query($query3);    
            //echo $query3;
            echo "<h3 style='text-align: left;'>EVALUACIONES COMO SUBCONTRATISTA</h3>";
            echo "<div align='center'>";
            echo "<table id='tblSubContratistas' class='display compact' cellspacing='0' style='width:100%;'><thead>";
                    echo "<th class='cabecera' style='min-width: 80px; width: 300px; background-color:#D8D8D8;'>PROYECTO</th>";
                    echo "<th class='cabecera' style='min-width: 80px; width: 300px; background-color:#D8D8D8;'>CONTRATO</th>";
                    echo "<th class='cabecera' style='width: 100px; background-color:#D8D8D8;'>SUBCONTRATO</th>";
                    echo "<th class='cabecera' style='width: 100px; background-color:#D8D8D8;'>EVALUACIÓN</th>";
            echo "</tr></thead><tbody>";                
            foreach ($result3 as $sp) {
                echo "<tr class='tab_bg_2'>";
                    echo "<td class='left fila'><a href='project.form.php?id={$sp['proyectoid']}' data-hasqtip='0' aria-descripbedby='qtip-0' >".$sp['proyecto']."</a></td>";
                    echo "<td class='left fila'><a href='projecttask.form.php?id={$sp['contratoid']}' data-hasqtip='0' aria-descripbedby='qtip-0' >".$sp['contrato']."</a></td>";
                    echo "<td class='left fila'><a href='projecttasks.form.php?id={$sp['subcontratoid']}' data-hasqtip='0' aria-descripbedby='qtip-0' >".$sp['subcontrato']."</a></td>";
                    echo "<td class='left fila'>".$sp['valoracion']."</td>";
                echo "</tr>";
            }
            echo "</tbody></table></div>";

            echo "</div>";
            echo "</div>";

            echo "<div align='center' style='margin-top: 10px;'>";
            echo "<table>";
                echo "<tr>";
                    echo "<td class='center'>Tipología de las calificaciones -></td>";                       
                    echo "<td class='center'><img style='vertical-align:middle; margin: 5px 0px; width: 20px; height: 20px;' src=".$CFG_GLPI["root_doc"]."/pics/valoracion_1.png></td>";                                                             
                    echo "<td  style='width: 50px;'>MALA</td>";
                    echo "<td class='center'><img style='vertical-align:middle; margin: 5px 0px; width: 20px; height: 20px;' src=".$CFG_GLPI["root_doc"]."/pics/valoracion_2.png></td>";                                                            
                    echo "<td  style='width: 50px;'>POBRE</td>";
                    echo "<td class='center'><img style='vertical-align:middle; margin: 5px 0px; width: 20px; height: 20px;' src=".$CFG_GLPI["root_doc"]."/pics/valoracion_3.png></td>";                                                            
                    echo "<td  style='width: 50px;'>ACEPTABLE</td>";
                    echo "<td class='center'><img style='vertical-align:middle; margin: 5px 0px; width: 20px; height: 20px;' src=".$CFG_GLPI["root_doc"]."/pics/valoracion_4.png></td>";                                                            
                    echo "<td  style='width: 50px;'>BUENA</td>";
                    echo "<td class='center'><img style='vertical-align:middle; margin: 5px 0px; width: 20px; height: 20px;' src=".$CFG_GLPI["root_doc"]."/pics/valoracion_5.png></td>";                                                            
                    echo "<td  style='width: 50px;'>EXCELENTE</td>";
                echo "</tr>";
            echo "</table></div>";                
            }else{
                echo "No posee permisos para acceder a esta pestaña, contacte con su administrador.";
            }
            
            echo "<script type='text/javascript'>
                    $('#acordeon').accordion({
                        collapsible: true,
                        heightStyle: 'content',
                        activate: function( event, ui ) {
                            $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
                        }                        
                    });
                    
                    $('#tblContratistas').DataTable({
                        'searching':      true,
                        'scrollY':        '200px',
                        'scrollCollapse': true,
                        'ordering':       true,
                        'paging':         false,
                        'language': {'url': '".$CFG_GLPI["root_doc"]."/lib/jqueryplugins/Spanish.json'}
                    });  
                    $('#tblServiciosProfesionales').DataTable({
                        'searching':      true,
                        'scrollY':        '200px',
                        'scrollCollapse': true,
                        'ordering':       true,
                        'paging':         false,
                        'language': {'url': '".$CFG_GLPI["root_doc"]."/lib/jqueryplugins/Spanish.json'}
                    });   
                    $('#tblSubContratistas').DataTable({
                        'searching':      true,
                        'scrollY':        '200px',
                        'scrollCollapse': true,
                        'ordering':       true,
                        'paging':         false,
                        'language': {'url': '".$CFG_GLPI["root_doc"]."/lib/jqueryplugins/Spanish.json'}
                    });                     
                </script>";
        }
                       
	function showFormNoCV($ID, $options=[]) {
		//Aqui entra cuando no tien gestionado el curriculum

		echo "<div>Necesitas gestionar el CV antes de ver las evaluaciones</div>";
		//echo "<br>";
	}
                
	function showFormNoAsignadoProveedor($ID, $options=[]) {
			//Aqui entra cuando no tien gestionado el curriculum

			echo "<div style='width: 20%; margin-bottom: 15px; margin-top: 8px; margin-left: 40%; padding: 5px; border-radius: 4px; background-color: #f8f7f3;   -webkit-box-shadow: 12px 8px 5px -5px rgba(219,219,219,1);    -moz-box-shadow: 12px 8px 5px -5px rgba(219,219,219,1);    box-shadow: 12px 8px 5px -5px rgba(219,219,219,1);'>
                                Aún no existe un adjudicatario para este contrato. Necesita seleccionar un proveedor como adjudicatario, antes de poder evaluar.</div>";
			//echo "<br>";
	}
        
	function showFormNoPermiso($ID, $options=[]) {
			//Aqui entra cuando no tien gestionado el curriculum

			echo "<div>Solo pueden acceder los usuarios que este en Equipo de proyecto o sean Administrador</div>";
			//echo "<br>";
	}
                
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
                                $color=0;
				break;
		}

		return $color;
	}
                
	function recomendacionesEvaluacion($total){
		  
		switch ($total) {
			case $total<=1:
				$val = "MALO";
				break;
                        case $total>1 && $total<3:
				$val = "POCO RECOMENDABLE";
				break;
                        case $total>=3 && $total<=4:
				$val = "RECOMENDABLE";
				break;
			case $total>=5:
				$val = "MUY RECOMENDABLE";
				break;
			default:
				break;
		}
		
		return $val;
	}
                
        function consultaAjax(){
        GLOBAL $DB,$CFG_GLPI;
            $resultado="<script type='text/javascript'>  

            function valorElegido(valor_criterio, tipo_criterio, valoracion){

                for(i=1;i<=5;i++){
                    if(valor_criterio==i){
                        $('#criterio_'+tipo_criterio+'_'+i+'_valoracion_'+valoracion).css({
                                'background-image':'url(".$CFG_GLPI["root_doc"]."/pics/valoracion_'+valor_criterio+'.png)',
                                'background-repeat':'no-repeat',
                                'background-position':'center'});
                        $('#criterio_'+tipo_criterio+'_'+i+'_valoracion_'+valoracion).html(valor_criterio);

                        //añadimos el valor elegido a arrayValoracion
                        arrayValoracion[valoracion][tipo_criterio]=valor_criterio;                                                                        
                    }
                    else{
                        $('#criterio_'+tipo_criterio+'_'+i+'_valoracion_'+valoracion).css({'background-image':'none'});
                        $('#criterio_'+tipo_criterio+'_'+i+'_valoracion_'+valoracion).html('');
                    }                                       
                }                                      
            }
            function guardarYModificarValoracion(paquete_id, numero_valoracion, valoracion_id, metodo){

                var valoracionesCompletada=true;
                var arrayComentarios= [];
                //Si arrayValoracion no tiene todo los campos completado, no se añadira la valoración
                if( arrayValoracion[numero_valoracion].length==6){

                    for(i=0;i<arrayValoracion[numero_valoracion].length;i++){
                        if(arrayValoracion[numero_valoracion][i]==null ){
                                valoracionesCompletada=false; 
                        }
                        arrayComentarios[i]=$('#criterio_'+i+'_comentario_valoracion_'+numero_valoracion).find('textarea').val();
                    }

                    if(valoracionesCompletada){

                        var parametros = {
                            'metodo': metodo,
                            'arrayComentarios':arrayComentarios,
                            'valoracion_id': valoracion_id,
                            'paquete_id':paquete_id,
                            'numero_valoracion' : numero_valoracion,
                            'fecha':$('#fecha_valoracion_'+numero_valoracion).find('input[name=fecha]').val(), 
                            'arrayValoracion': arrayValoracion[numero_valoracion]    
                        };

                        $.ajax({ 
                            type: 'GET',
                            data: parametros,                  
                            url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/front/valuation.form.php',                    
                            success:function(data){
                                if(metodo=='add_valoracion'){

                                        $('#boton_guardar_'+numero_valoracion).html(
                                                '<span onclick=\'guardarYModificarValoracion('+paquete_id+','+numero_valoracion+','+data+',\"update_valoracion\")\' class=\'vsubmit\' style=\'margin-right: 15px;\'>MODIFICAR VALORACIÓN</span>'
                                        );
                                }
                            },
                            error: function(result) {
                                    alert('Data not found');
                            }
                        });
                    }
                }
            }   
             </script>";

            return $resultado;
        }
            
        static function cronEvaluacionesRecordatorio($task = null) {
               GLOBAL $DB,$CFG_GLPI;
               
               $email = new NotificationMailing();
               
               $meses=4;
                
               //Evaluaciones en la que la fecha de evaluación tiene mas de X meses de antiguedad
               $query="select 
                        (select configuracion.value from glpi_configs as configuracion where configuracion.name='asunto_correo') as asunto_correo,
                        (select configuracion.value from glpi_configs as configuracion where configuracion.name='cuerpo_correo') as cuerpo_correo,
                        (select configuracion.value from glpi_configs as configuracion where configuracion.name='firma_correo') as firma_correo,
                        (select configuracion.value from glpi_configs as configuracion where configuracion.name='remitente_correo') as remitente_correo,
                        (select configuracion.value from glpi_configs as configuracion where configuracion.name='remitente_nombre') as remitente_nombre,
                        group_concat(contratos.id) as contratos_id,
                        group_concat(contratos.name) as contratos_name,
                        usuarios.name as nombre_usuario,
                        evaluaciones.id as evaluacion_id,
                        evaluaciones.fecha as fecha,
                        email.email as email
                        from glpi_projectteams as projectteams 
                        left join glpi_projecttasks as contratos on contratos.projects_id=projectteams.projects_id
                        left join glpi_plugin_comproveedores_valuations as evaluaciones on evaluaciones.projecttasks_id 
                        and evaluaciones.id=(Select max(valoraciones.id) from glpi_plugin_comproveedores_valuations as valoraciones
                        where valoraciones.projecttasks_id=contratos.id) 
                        left join glpi_users as usuarios on usuarios.id=projectteams.items_id
                        left join glpi_useremails as email on email.users_id=usuarios.id 
                        where evaluaciones.fecha is not null 
                        and email.email is not null
                        and DATE(evaluaciones.fecha) <= DATE(NOW() - INTERVAL (select configuracion.value from glpi_configs as configuracion where configuracion.name='meses_valoraciones') month)
                        group by usuarios.id";
               
                $result = $DB->query($query);
                
                while ($data=$DB->fetch_array($result)) {

                        $contratos_name=explode( ',', $data['contratos_name']);

                        //Servicio de correo y puerto
                        $CFG_GLPI["smtp_host"]='aspmx.l.google.com';
                        $CFG_GLPI["smtp_port"]=25;

                        //Correo destinatario
                        $CFG_GLPI['admin_email']=$data['email'];

                        //Nombre destinatario
                        $CFG_GLPI["admin_email_name"]=$data['nombre_usuario'];

                        //Firma de los correos
                        $CFG_GLPI["mailing_signature"]=$data['firma_correo'];

                        //Titulo del correo
                        $subject=$data['asunto_correo'];

                        //Mensaje del correo
                        $body=$data['cuerpo_correo']." \n";
                        foreach ($contratos_name as $value) {
                                    
                                $body .=$value." \n";
                        }
                        

                        //Correo remitente
                        $remitente_correo=$data['remitente_correo'];

                        //Nombre Remitente
                        $remitente_nombre=$data['remitente_nombre'];

                        $email->sendCorreoEvaluaciones($subject, $body, $remitente_correo, $remitente_nombre);
                    
                }
                
        }
    
}