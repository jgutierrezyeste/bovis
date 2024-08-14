<?php

/******************************************

	PLUGIN DE GESTION DE CURRICULUMS DE LOS PROVEEDORES


 ******************************************/

	class PluginComproveedoresIntegratedmanagementsystem extends CommonDBTM{

		static $rightname	= "plugin_comproveedores";

		static function getTypeName($nb=0){
			return _n('Sistema integrado de gestión','Sistema integrado de gestión',1,'comproveedores');
		}

		function getTabNameForItem(CommonGLPI $item, $tabnum=1,$withtemplate=0){
			if($item-> getType()=="Supplier"){
				return self::createTabEntry('Sist. Integrado de Gestión');
			}
			return 'Sist. Integr. de Gestión';
		}


		static function displayTabContentForItem(CommonGLPI $item,$tabnum=1,$withtemplate=0){

			global $CFG_GLPI;
			$self = new self();

			//Entrada Administrador
			if($item->getType()=='Supplier'){	

				if(isset($item->fields['cv_id'])){
			
					$self->showFormItemSIG($item, $withtemplate);

				}else{
				
					$self->showFormNoCV($item, $withtemplate);
				}
			//entrada Proveedores
			}else if($item->getType()=='PluginComproveedoresCv'){
				$self->showFormItem($item, $withtemplate);
			}

		}

		function getSearchOptions(){

			$tab = array();

			$tab['common'] = ('Sistema integrado de gestión');

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
        
        function gestion($item, $withtemplate=''){
                GLOBAL $DB,$CFG_GLPI;
                $ver = false;
                if($item->fields['cv_id']){
//                    $CvId=$item->fields['cv_id']; 
                    if($item->fields['cv_id']==0 || isset($item->fields['cv_id']) == false){
                        $CvId = -1;
                    }else{
                        $CvId = $item->fields['cv_id'];     
                    }                    
                    $ver = true;
                }else{
                    $CvId=$item->fields['id'];
                    $ver = false;
                }


                $self                = new self();
                $user_Id             = $_SESSION['glpiID'];
                $profile_Id          = $self->getProfileByUserID($user_Id);
                $ver                 = true;
                if(in_array($profile_Id, array(3,4,14,16,9))){    
                    $ver = true;
                }else{
                    $ver = false;
                }                     
                if($ver === false){
                    echo "<script type='text/javascript'>"
                    . "$('#c_recherche').css('display', 'none'); "
                    . "$('#language_link').css('display', 'none'); "
                    . "$('#help_link').css('display', 'none'); "
                    . "$('#bookmark_link').css('display', 'none'); "
                    . "$('#debug_mode').css('display', 'none'); "
                    . "$('#page').css('margin', 'auto'); "
                    . "$('#c_ssmenu2').css('display', 'none');"
                    . "$('#goToList').css('display', 'none');"
                    . "$('#preferences_link').css('display', 'none');"
                    . "</script>";
                }

                $ID = 0;
                $sql = "SELECT * FROM glpi_plugin_comproveedores_integratedmanagementsystems WHERE cv_id={$CvId}";
                $ims = $DB->query($sql);
                while ($data=$DB->fetch_array($ims)) {
                    $ID = $data['id'];
                    $plan_gestion = $data['plan_gestion'];
                    $obs_plan_gestion = $data['obs_plan_gestion'];
                    $control_documentos = $data['control_documentos'];
                    $obs_control_documentos = $data['obs_control_documentos'];
                    $politica_calidad = $data['politica_calidad'];
                    $obs_politica_calidad = $data['obs_politica_calidad'];
                    $auditorias_internas = $data['auditorias_internas'];
                    $obs_auditorias_internas = $data['obs_auditorias_internas'];  
                    $plan_sostenibilidad = $data['plan_sostenibilidad'];
                    $obs_plan_sostenibilidad = $data['obs_plan_sostenibilidad'];                              
                    $sg_medioambiental = $data['sg_medioambiental'];
                    $obs_sg_medioambiental = $data['obs_sg_medioambiental'];     
                    $acciones_rsc = $data['acciones_rsc'];
                    $obs_acciones_rsc = $data['obs_acciones_rsc'];         
                    $gestion_rsc = $data['gestion_rsc'];
                    $obs_gestion_rsc = $data['obs_gestion_rsc'];                 
                    $sg_seguridad_y_salud = $data['sg_seguridad_y_salud'];
                    $obs_sg_seguridad_y_salud = $data['obs_sg_seguridad_y_salud'];         
                    $certificado_formacion = $data['certificado_formacion'];
                    $obs_certificado_formacion = $data['obs_certificado_formacion'];          
                    $departamento_segurida_y_salud = $data['departamento_segurida_y_salud'];
                    $obs_departamento_segurida_y_salud = $data['obs_departamento_segurida_y_salud'];            
                    $metodologia_segurida_y_salud = $data['metodologia_segurida_y_salud'];
                    $obs_metodologia_segurida_y_salud = $data['obs_metodologia_segurida_y_salud'];           
                    $formacion_segurida_y_salud = $data['formacion_segurida_y_salud'];
                    $obs_formacion_segurida_y_salud = $data['obs_formacion_segurida_y_salud'];               
                    $empleado_rp = $data['empleado_rp'];
                    $obs_empleado_rp = $data['obs_empleado_rp'];        
                    $empresa_asesoramiento = $data['empresa_asesoramiento'];
                    $obs_empresa_asesoramiento = $data['obs_empresa_asesoramiento'];         
                    $procedimiento_subcontratistas = $data['procedimiento_subcontratistas'];
                    $obs_procedimiento_subcontratistas = $data['obs_procedimiento_subcontratistas'];                   

                }


                if($ID==0){
                    $sql = "INSERT INTO glpi_plugin_comproveedores_integratedmanagementsystems (cv_id) VALUE ({$CvId})";
                    $insert = $DB->query($sql);
                    $sql = "SELECT max(id) as id FROM glpi_plugin_comproveedores_integratedmanagementsystems";
                    $nue = $DB->query($sql);
                    while ($aux=$DB->fetch_array($nue)) {
                        $ID = $aux['id'];
                    }
                }

                echo "<div id='contenedeorSIG' style='margin-bottom: 10px; float:left; width:98%; border-radius: 4px; padding: 8px; background-color: #e9ecf3;  overflow: auto; height: 620px; width: 125em;'>";
                echo "<input type='hidden' id='cv_id' name='cv_id' value='{$CvId}' />";
                echo "<input type='hidden' id='idIMS' name='idIMS' value='{$ID}' />";

                echo "<div id='cero' style='float: left; background-color:#ccc; width: 99%; margin-bottom: 4px; padding: 4px;'>
                    <input id='grabar' type='submit' class='boton_grabar' value='' title='grabar' style='float:left;'/>
                    <input id='limpiar' type='submit' class='boton_limpieza' value='' title='limpiar' style='float:left; margin-left:15px;'/>
                </div>";
                echo "<div id='uno' style='float: left; background-color:#e5e5e5; width:700px; overflow-y:auto; height: 43em; margin-bottom:10px;'>";
                echo "<table style='width: 100%;'>";
                echo"<tr style='font-weight: bold; font-size:12px; background-color: #0e52a0; color: #FFF;'>";
                echo "<td colspan='2' style='border-bottom: 1px solid #9e9b9b;'>Aseguramiento de calidad</td>";
                echo "<td style='border-bottom: 1px solid #9e9b9b;'>Observaciones/Comentarios</td>";
                echo "</tr>";

                echo"<tr>";
                echo "<td style='text-align: left; border-bottom: 1px solid #9e9b9b; padding-left: 14px;'>¿Tiene la empresa un sistema o plan de gestión?
                                <span style='color:#B40404'>(Indicar Acriditaciones Vigentes. Ejemplo:ISO 9001 o similar)</span></td>";
                echo "<td style='border-bottom: 1px solid #9e9b9b;'>";
                //Dropdown::c('plan_gestion', $this["plan_gestion"]);
                    if($plan_gestion==1){echo "<input id='plan_gestion' class='chkProp'  type='checkbox' style='font-size:18px; width:17px; height:17px;' checked/>";}
                    else{ echo "<input id='plan_gestion'  class='chkProp' type='checkbox' style='width:17px; height:17px;'/>"; }
                echo "</td><td style='border-bottom: 1px solid #9e9b9b;'>";
                echo "<textarea id='obs_plan_gestion' cols='40' rows='3' name='obs_plan_gestion' style='resize: none;'>".$obs_plan_gestion."</textarea>";
                echo "</td>";
                echo "</tr>";

                echo"<tr>";
                echo "<td style='text-align: left; border-bottom: 1px solid #9e9b9b; padding-left: 14px;'>". __('¿Posee procedimientos de control de documentos?') . "</td>";
                echo "<td style='border-bottom: 1px solid #9e9b9b;'>";
                //Dropdown::showYesNo('control_documentos',$this["control_documentos"]);
                    if($control_documentos==1){echo "<input id='control_documentos' class='chkProp' type='checkbox' style='font-size:18px; width:17px; height:17px;' checked/>";}
                    else{ echo "<input id='control_documentos' class='chkProp' type='checkbox' style='width:17px; height:17px;'/>"; }                        
                echo "</td><td style='border-bottom: 1px solid #9e9b9b;'>";
                echo "<textarea cols='40' rows='3' id='obs_control_documentos' style='resize: none;'>".$obs_control_documentos."</textarea>";
                echo "</td>";
                echo"</tr>";

                echo"<tr>";
                echo "<td style='text-align: left;border-bottom: 1px solid #9e9b9b; padding-left: 14px;'>". __('¿Posee Política de calidad?') . "</td>";
                echo "<td style='border-bottom: 1px solid #9e9b9b;'>";
                //Dropdown::showYesNo('politica_calidad', $this["politica_calidad"]);
                    if($politica_calidad==1){echo "<input id='politica_calidad' class='chkProp' type='checkbox' style='font-size:18px; width:17px; height:17px;' checked/>";}
                    else{ echo "<input id='politica_calidad' class='chkProp' type='checkbox' style='width:17px; height:17px;'/>"; }                          
                echo "</td><td style='border-bottom: 1px solid #9e9b9b;'>";
                echo "<textarea cols='40' rows='3' id='obs_politica_calidad' style='resize: none;'>".$obs_politica_calidad."</textarea>";
                echo "</td>";
                echo"</tr>";

                echo"<tr>";
                echo "<td style='text-align: left; border-bottom: 1px solid #9e9b9b; padding-left: 14px;'>". __('¿Realiza auditorias internas de calidad?') . "</td>";
                echo "<td style='border-bottom: 1px solid #9e9b9b;'>";
                //Dropdown::showYesNo('auditorias_internas', $this["auditorias_internas"]);
                    if($auditorias_internas==1){echo "<input id='auditorias_internas' class='chkProp' type='checkbox' style='font-size:18px; width:17px; height:17px;' checked/>";}
                    else{ echo "<input id='auditorias_internas' class='chkProp' type='checkbox' style='width:17px; height:17px;'/>"; }                          
                echo "</td><td style='border-bottom: 1px solid #9e9b9b;'>";
                echo "<textarea cols='40' rows='3' id='obs_auditorias_internas' style='resize: none;'>".$obs_auditorias_internas."</textarea>";
                echo "</td>";
                echo"</tr>";

                //Sostenibilidad
                echo"<tr class='center'  style='font-weight: bold; font-size:12px; background-color: #0e52a0; color: #FFF;'>";
                echo "<td colspan='2'>Sostenibilidad</td>";
                echo "<td>Observaciones/Comentarios</td>";
                echo "</tr>";

                echo"<tr>";
                echo "<td style='text-align: left; border-bottom: 1px solid #9e9b9b; padding-left: 14px;'>" . __('¿Tiene la empresa un plan de sostenibilidad?') . "</td>";
                echo "<td style='border-bottom: 1px solid #9e9b9b;'>";
                    if($plan_sostenibilidad==1){echo "<input id='plan_sostenibilidad' class='chkProp' type='checkbox' style='font-size:18px; width:17px; height:17px;' checked/>";}
                    else{ echo "<input id='plan_sostenibilidad' class='chkProp' type='checkbox' style='width:17px; height:17px;'/>"; }                             
                echo "</td><td style='border-bottom: 1px solid #9e9b9b;'>";
                echo "<textarea cols='40' rows='3' id='obs_plan_sostenibilidad' style='resize: none;'>".$obs_plan_sostenibilidad."</textarea>";
                echo "</td>";
                echo "</tr>";

                echo"<tr>";
                echo "<td style='text-align: left; border-bottom: 1px solid #9e9b9b; padding-left: 14px;'>¿Tiene acreditado un Sistema de Gestión Medioambiental?<span style='color:#B40404'>(Indicar Acriditaciones Vigentes. Ejemplo:ISO 14001 o similar)</span>'</td>";
                echo "<td style='border-bottom: 1px solid #9e9b9b;'>";
                //Dropdown::showYesNo('sg_medioambiental', $this["sg_medioambiental"]);
                    if($sg_medioambiental==1){echo "<input id='sg_medioambiental' class='chkProp' type='checkbox' style='font-size:18px; width:17px; height:17px;' checked/>";}
                    else{ echo "<input id='sg_medioambiental' class='chkProp' type='checkbox' style='width:17px; height:17px;'/>"; }                             
                echo "</td><td style='border-bottom: 1px solid #9e9b9b;'>";
                echo "<textarea cols='40' rows='3' id='obs_sg_medioambiental' style='resize: none;'>".$obs_sg_medioambiental."</textarea>";
                echo "</td>";
                echo"</tr>";

                //Responsabilidad Social Corporativa(RSC)

                echo"<tr style='font-weight: bold; font-size:12px; background-color: #0e52a0; color: #FFF;'>";
                echo "<td colspan='2'>Responsabilidad Social Corporativa(RSC)</td>";
                echo "<td>Observaciones/Comentarios</td>";
                echo "</tr>";

                echo"<tr>";
                echo "<td style='text-align: left; border-bottom: 1px solid #9e9b9b; padding-left: 14px;'>¿Realiza la Empresa Acciones en favor de la RSC?
                                <span style='color:#B40404'>(Indicar las más destacadas)</span>
                        </td>";
                echo "<td style='border-bottom: 1px solid #9e9b9b;'>";
                //Dropdown::showYesNo('acciones_rsc', $this["acciones_rsc"]);
                    if($acciones_rsc==1){echo "<input id='acciones_rsc' class='chkProp' type='checkbox' style='font-size:18px; width:17px; height:17px;' checked/>";}
                    else{ echo "<input id='acciones_rsc'  class='chkProp' type='checkbox' style='width:17px; height:17px;'/>"; }                                 
                echo "</td><td style='border-bottom: 1px solid #9e9b9b;'>";
                echo "<textarea cols='40' rows='3' id='obs_acciones_rsc' style='resize: none;'>".$obs_acciones_rsc."</textarea>";
                echo "</td>";
                echo "</tr>";

                echo"<tr>";
                echo "<td style='text-align: left; border-bottom: 1px solid #9e9b9b; padding-left: 14px;'>¿Tiene implementada una politica de gestión de la RSC?'
                                <span style='color:#B40404'>(Indicar qué política)</span>
                        </td>";
                echo "<td style='border-bottom: 1px solid #9e9b9b;'>";
                //Dropdown::showYesNo('gestion_rsc', $this["gestion_rsc"]);
                    if($gestion_rsc==1){echo "<input id='gestion_rsc' class='chkProp' type='checkbox' style='font-size:18px; width:17px; height:17px;' checked/>";}
                    else{ echo "<input id='gestion_rsc' class='chkProp' type='checkbox' style='width:17px; height:17px;' style='width:17px; height:17px;'/>"; }                                                         
                echo "</td><td style='border-bottom: 1px solid #9e9b9b;'>";
                echo "<textarea cols='40' rows='3' id='obs_gestion_rsc' style='resize: none;'>".$obs_gestion_rsc."</textarea>";
                echo "</td>";
                echo"</tr>";

                //Seguridad y Salud

                echo"<tr style='font-weight: bold; font-size:12px; background-color: #0e52a0; color: #FFF;'>";
                echo "<td colspan='2'>Seguridad y Salud</td>";
                echo "<td>Observaciones/Comentarios</td>";
                echo "</tr>";

                echo"<tr>";
                echo "<td style='text-align: left; border-bottom: 1px solid #9e9b9b; padding-left: 14px;'>¿Dispone de un sistema de gestión de la Seguridad y Salud tipo OSHAS 18001 o similar?'
                                <span style='color:#B40404'>(Indicar sistema de gestión similar)</span>
                        </td>";
                echo "<td style='border-bottom: 1px solid #9e9b9b;'>";
                //Dropdown::showYesNo('sg_seguridad_y_salud', $this["sg_seguridad_y_salud"]);
                    if($sg_seguridad_y_salud==1){echo "<input id='sg_seguridad_y_salud' class='chkProp' type='checkbox' style='font-size:18px; width:17px; height:17px;' checked/>";}
                    else{ echo "<input id='sg_seguridad_y_salud' class='chkProp' type='checkbox' style='width:17px; height:17px;'/>"; }                          
                echo "</td><td style='border-bottom: 1px solid #9e9b9b;'>";
                echo "<textarea cols='40' rows='3' id='obs_sg_seguridad_y_salud'  style='resize: none;'>".$obs_sg_seguridad_y_salud."</textarea>";
                echo "</td>";
                echo "</tr>";

                echo "<tr>";
                echo "<td style='text-align: left; border-bottom: 1px solid #9e9b9b; padding-left: 14px;'>¿La formación de los empleados está acreditada por un certificado de formación emitido por un organismo competente?'
                                <span style='color:#B40404'>(Indicar el organismo acreditador)</span>
                        </td>";
                echo "<td style='border-bottom: 1px solid #aed3ff;'>";
                //Dropdown::showYesNo('certificado_formacion', $this["certificado_formacion"]);
                    if($certificado_formacion==1){echo "<input id='certificado_formacion' class='chkProp' type='checkbox' style='font-size:18px; width:17px; height:17px;' checked/>";}
                    else{ echo "<input id='certificado_formacion' class='chkProp' type='checkbox' style='width:17px; height:17px;'/>"; }                                
                echo "</td><td style='border-bottom: 1px solid #9e9b9b;'>";
                echo "<textarea cols='40' rows='3' id='obs_certificado_formacion'  style='resize: none;'>".$obs_certificado_formacion."</textarea>";
                echo "</td>";
                echo"</tr>";

                echo"<tr>";
                echo "<td style='text-align: left; border-bottom: 1px solid #9e9b9b; padding-left: 14px;'>¿Cuenta la empresa con un departamento especializado en la Gestión de Seguridad y Salud?'
                        <span style='color:#B40404'>(Indicar Indicar número de empleados de dicho departamento)</span>
                        </td>";
                echo "<td style='border-bottom: 1px solid #9e9b9b;'>";
                //Dropdown::showYesNo('departamento_segurida_y_salud', $this["departamento_segurida_y_salud"]);
                    if($departamento_segurida_y_salud==1){echo "<input id='departamento_segurida_y_salud' class='chkProp' type='checkbox' style='font-size:18px; width:17px; height:17px;' checked/>";}
                    else{ echo "<input id='departamento_segurida_y_salud' class='chkProp' type='checkbox' style='width:17px; height:17px;'/>"; }                            
                echo "</td><td style='border-bottom: 1px solid #9e9b9b;'>";
                echo "<textarea cols='40' rows='3' id='obs_departamento_segurida_y_salud' style='resize: none;'>".$obs_departamento_segurida_y_salud."</textarea>";
                echo "</td>";
                echo"</tr>";

                echo"<tr>";
                echo "<td style='text-align: left; border-bottom: 1px solid #9e9b9b; padding-left: 14px;'>". __('¿Tiene implantada la empresa una metodología para medir, evaluar, auditar, inspeccionar, etc sus desempeño en Seguridad y Salud?') . "</td>";
                echo "<td style='border-bottom: 1px solid #9e9b9b;'>";
                //Dropdown::showYesNo('metodologia_segurida_y_salud', $this["metodologia_segurida_y_salud"]);
                    if($metodologia_segurida_y_salud==1){echo "<input id='metodologia_segurida_y_salud' class='chkProp' type='checkbox' style='font-size:18px; width:17px; height:17px;' checked/>";}
                    else{ echo "<input id='metodologia_segurida_y_salud' class='chkProp' type='checkbox' style='width:17px; height:17px;'/>"; }                              
                echo "</td><td style='border-bottom: 1px solid #9e9b9b;'>";
                echo "<textarea cols='40' rows='3' id='obs_metodologia_segurida_y_salud' style='resize: none;'>".$obs_metodologia_segurida_y_salud."</textarea>";
                echo "</td>";
                echo"</tr>";

                echo"<tr>";
                echo "<td style='text-align: left; border-bottom: 1px solid #9e9b9b; padding-left: 14px;'>¿Proporciona la empresa formación especifica en Seguridad y Salud?'
                                <span style='color:#B40404'>(Indicar número de horas de formación impartidas durante el último año)</span>
                        </td>";
                echo "<td style='border-bottom: 1px solid #9e9b9b;'>";
                //Dropdown::showYesNo('formacion_segurida_y_salud', $this["formacion_segurida_y_salud"]);
                    if($formacion_segurida_y_salud==1){echo "<input id='formacion_segurida_y_salud' class='chkProp' type='checkbox' style='font-size:18px; width:17px; height:17px;' checked/>";}
                    else{ echo "<input id='formacion_segurida_y_salud' class='chkProp' type='checkbox' style='width:17px; height:17px;'/>"; }                             
                echo "</td><td style='border-bottom: 1px solid #9e9b9b;'>";
                echo "<textarea cols='40' rows='3' id='obs_formacion_segurida_y_salud' style='resize: none;'>".$obs_formacion_segurida_y_salud."</textarea>";
                echo "</td>";
                echo"</tr>";

                echo"<tr>";
                echo "<td style='text-align: left; border-bottom: 1px solid #9e9b9b; padding-left: 14px;'>De la plantilla actual. ¿Cuántos empleados podrían ejercer como Recursos Preventivo en una obra?'
                                <span style='color:#B40404'>(Indicar número de empleados fijos capacitados)</span>
                        </td>";
                echo "<td style='border-bottom: 1px solid #9e9b9b;'>";
                //Dropdown::showYesNo('empleado_rp', $this["empleado_rp"]);
                    if($empleado_rp==1){echo "<input id='empleado_rp' class='chkProp' type='checkbox' style='font-size:18px; width:17px; height:17px;' checked/>";}
                    else{ echo "<input id='empleado_rp' class='chkProp' type='checkbox' style='width:17px; height:17px;'/>"; }                             
                echo "</td><td style='border-bottom: 1px solid #9e9b9b;'>";
                echo "<textarea cols='40' rows='3' id='obs_empleado_rp' style='resize: none;'>".$obs_empleado_rp."</textarea>";
                echo "</td>";
                echo"</tr>";

                echo"<tr>";
                echo "<td style='text-align: left; border-bottom: 1px solid #9e9b9b; padding-left: 14px;'>¿Dispone la empresa de Asesoría técina-legal competente para la asesoramiento y/o asistencia materia de Seguridad y Salud?'
                                <span style='color:#B40404'>(Indicar número de procesos judiciales o acciones legales relacionados con la Seguridad y Salud emprendidos contra la empresa en los últimos 5 años)</span>
                        </td>";
                echo "<td style='border-bottom: 1px solid #9e9b9b;'>";
                //Dropdown::showYesNo('empresa_asesoramiento', $this["empresa_asesoramiento"]);
                    if($empresa_asesoramiento==1){echo "<input id='empresa_asesoramiento' class='chkProp' type='checkbox' style='font-size:18px; width:17px; height:17px;' checked/>";}
                    else{ echo "<input id='empresa_asesoramiento' class='chkProp' type='checkbox' style='width:17px; height:17px;'/>"; }                              
                echo "</td><td style='border-bottom: 1px solid #9e9b9b;'>";
                echo "<textarea cols='40' rows='3' id='obs_empresa_asesoramiento' style='resize: none;'>".$obs_empresa_asesoramiento."</textarea>";
                echo "</td>";
                echo"</tr>";

                echo"<tr>";
                echo "<td style='text-align: left; border-bottom: 1px solid #9e9b9b; padding-left: 14px;'>En la práctica habitual ¿existe un procedimiento de la empresa que garantice que sus Subcontratistas son competentes y están capacitados para el desempeño de su trabajo con seguridad? 
                                <span style='color:#B40404'>(Caso de existir, indicar el número de Subcontratistas que ya habrían sido precalificados)</span>
                        </td>";
                echo "<td style='border-bottom: 1px solid #9e9b9b;'>";
                //Dropdown::showYesNo('procedimiento_subcontratistas', $this["procedimiento_subcontratistas"]);
                    if($procedimiento_subcontratistas==1){echo "<input id='procedimiento_subcontratistas' class='chkProp' type='checkbox' style='font-size:18px; width:17px; height:17px;' checked/>";}
                    else{ echo "<input id='procedimiento_subcontratistas' class='chkProp' type='checkbox' style='width:17px; height:17px;'/>"; }                             
                echo "</td><td style='border-bottom: 1px solid #9e9b9b;'>";
                echo "<textarea cols='40' rows='3' id='obs_procedimiento_subcontratistas' style='resize: none;'>".$obs_procedimiento_subcontratistas."</textarea>";
                echo "</td>";
                echo "</tr>";
                echo "</table></div>";

                echo "<tr><td colspan='3'>";

                //Consignar los siguientas índices de siniestralidad año por año
                echo "<div id='losRatios' style='height: 250px; float: left; width: 600px; background-color: #e5e5e5; margin-left: 10px; margin-right: 10px; margin-bottom: 10px; padding: 10px; border: 1px solid #ccc;'>";

                echo "</div>";

                $incidencia = 0;
                $frecuencia = 0;
                $gravedad = 0;                        
                $query3 ="SELECT * FROM glpi_plugin_comproveedores_lossratios WHERE cv_id=".$CvId." order by anio asc" ;
                $result3 = $DB->query($query3);
                if($result3->num_rows!=0){
                    $i=0;
                    while ($data=$DB->fetch_array($result3) && $i==0) {
                        $incidencia = $data['incidencia'];
                        $frecuencia = $data['frecuencia'];
                        $gravedad = $data['gravedad'];
                        $i++;
                    }
                }       
                if($ver){
                echo "<div style='font-size: 12px;text-align: left; "
                    . "-webkit-box-shadow: 12px 8px 5px -5px rgba(219,219,219,1);  "
                        . "-moz-box-shadow: 12px 8px 5px -5px rgba(219,219,219,1); "
                        . "box-shadow: 12px 8px 5px -5px rgba(219,219,219,1); "
                        . "background-color: #fff; padding: 10px 0px 0px 10px; "
                        . "border: 1px solid #ccc; border-radius: 4px; margin-top: 4px; margin-left:15px; "
                        . "width: 150px; height: 80px; float: left; position: relative;'>";
                echo "<strong>Índice de siniestralidad:</strong><br>";
                echo "<ul>";
                echo "<li>Incidencias < 85,46</li>";
                echo "<li>Frecuencia < 49,9</li>";
                echo "<li>Gravedad < 1,39</li>";
                echo "</ul>";
                echo "</div>";
                echo "<div style='text-align: left; -webkit-box-shadow: 12px 8px 5px -5px rgba(219,219,219,1);  -moz-box-shadow: 12px 8px 5px -5px rgba(219,219,219,1);    box-shadow: 12px 8px 5px -5px rgba(219,219,219,1); background-color: #fff; padding: 10px 0px 0px 10px; border: 1px solid #ccc; border-radius: 4px; margin-top: 4px; margin-left:15px; width: 400px; height: 100px; float: left; position: relative;'>";
                echo "<table style='font-size: 20px;'>";                        
                echo "<tr><td>Índice de siniestralidad</td><td id='tdSiniestralidad' style='padding-left:10px;'>";
                if($incidencia<85 && $frecuencia<49 && $gravedad<1){
                    echo "<img id='imagenSiniestralidad' src='".$CFG_GLPI["root_doc"]."/pics/si.png' style='' />";
                }else{
                    echo "<img id='imagenSiniestralidad' src='".$CFG_GLPI["root_doc"]."/pics/no.png' style='' />";
                }
                echo "</td></tr>";
                echo "<tr><td>Índice de sostenibilidad</td><td style='padding-left:10px;'>";
                if($incidencia<85.46 && $frecuencia<49.9 && $gravedad<1.39){
                    echo "<img id='imagenSostenibilidad' src='".$CFG_GLPI["root_doc"]."/pics/si.png' style='' />";
                }else{
                    echo "<img id='imagenSostenibilidad' src='".$CFG_GLPI["root_doc"]."/pics/no.png' style='' />";
                }
                echo "</td></tr>";                        
                echo "<tr><td>Índice de calidad</td><td style='padding-left:10px;'>";
                if($incidencia<85.46 && $frecuencia<49.9 && $gravedad<1.39){
                    echo "<img id='imagenCalidad' src='".$CFG_GLPI["root_doc"]."/pics/si.png' style='' />";
                }else{
                    echo "<img id='imagenCalidad' src='".$CFG_GLPI["root_doc"]."/pics/no.png' style='' />";
                }
                echo "</td></tr>";  
                echo "</table>";
                echo "</div>";   
                echo "</div>";
                }
                if($ver === false){
                    echo "<script type='text/javascript'>"
                    . "$('#c_recherche').css('display', 'none'); "
                    . "$('#language_link').css('display', 'none'); "
                    . "$('#help_link').css('display', 'none'); "
                    . "$('#bookmark_link').css('display', 'none'); "
                    . "$('#debug_mode').css('display', 'none'); "
                    . "$('#page').css('margin', 'auto'); "
                    . "$('#c_ssmenu2').css('display', 'none');"
                    . " $('#goToList').css('display', 'none');"
                    . "</script>";
                }
                echo "<script type='text/javascript'>
                        var profile = {$profile_Id};

                        verificaCalidad();
                        verificaSostenibilidad();
                        
                        function verificaSiniestralidad(num, frec, grav){
                            var strImg = '".$CFG_GLPI["root_doc"]."/pics/no.png';
                           
                            if(parseFloat(num)<85.46 && parseFloat(frec)<49.9 && parseFloat(grav)<1.39){
                                strImg='".$CFG_GLPI["root_doc"]."/pics/si.png';
                            }

                            $('#imagenSiniestralidad').prop('src', strImg);
                        }

                        $('#grabar').on('click', function(){

                            var id = $('#idIMS').val();     
                            var cvid = $('#cv_id').val();

                            var plan_gestion = 0;
                            if($('#plan_gestion').prop('checked')){plan_gestion=1;}
                            var obs_plan_gestion = $('#obs_plan_gestion').val();

                            var control_documentos = 0;
                            if($('#control_documentos').prop('checked')){control_documentos=1;}
                            var obs_control_documentos = $('#obs_control_documentos').val();         

                            var politica_calidad = 0;
                            if($('#politica_calidad').prop('checked')){politica_calidad=1;}
                            var obs_politica_calidad = $('#obs_politica_calidad').val();                                       

                            var auditorias_internas = 0;
                            if($('#auditorias_internas').prop('checked')){auditorias_internas=1;}
                            var obs_auditorias_internas = $('#obs_auditorias_internas').val(); 

                            var plan_sostenibilidad = 0;
                            if($('#plan_sostenibilidad').prop('checked')){plan_sostenibilidad=1;}
                            var obs_plan_sostenibilidad = $('#obs_plan_sostenibilidad').val();

                            var sg_medioambiental = 0;
                            if($('#sg_medioambiental').prop('checked')){sg_medioambiental=1;}
                            var obs_sg_medioambiental = $('#obs_sg_medioambiental').val();

                            var acciones_rsc = 0;
                            if($('#acciones_rsc').prop('checked')){acciones_rsc=1;}
                            var obs_acciones_rsc = $('#obs_acciones_rsc').val();

                            var gestion_rsc = 0;
                            if($('#gestion_rsc').prop('checked')){gestion_rsc=1;}
                            var obs_gestion_rsc = $('#obs_gestion_rsc').val();

                            var sg_seguridad_y_salud = 0;
                            if($('#sg_seguridad_y_salud').prop('checked')){sg_seguridad_y_salud=1;}
                            var obs_sg_seguridad_y_salud = $('#obs_sg_seguridad_y_salud').val();

                            var certificado_formacion = 0;
                            if($('#certificado_formacion').prop('checked')){certificado_formacion=1;}
                            var obs_certificado_formacion = $('#obs_certificado_formacion').val();

                            var departamento_segurida_y_salud = 0;
                            if($('#departamento_segurida_y_salud').prop('checked')){departamento_segurida_y_salud=1;}
                            var obs_departamento_segurida_y_salud = $('#obs_departamento_segurida_y_salud').val();

                            var metodologia_segurida_y_salud = 0;
                            if($('#metodologia_segurida_y_salud').prop('checked')){metodologia_segurida_y_salud=1;}
                            var obs_metodologia_segurida_y_salud = $('#obs_metodologia_segurida_y_salud').val();

                            var formacion_segurida_y_salud = 0;
                            if($('#formacion_segurida_y_salud').prop('checked')){formacion_segurida_y_salud=1;}
                            var obs_formacion_segurida_y_salud = $('#obs_formacion_segurida_y_salud').val();

                            var empleado_rp = 0;
                            if($('#empleado_rp').prop('checked')){empleado_rp=1;}
                            var obs_empleado_rp = $('#obs_empleado_rp').val();

                            var empresa_asesoramiento = 0;
                            if($('#empresa_asesoramiento').prop('checked')){empresa_asesoramiento=1;}
                            var obs_empresa_asesoramiento = $('#obs_empresa_asesoramiento').val();

                            var procedimiento_subcontratistas = 0;
                            if($('#procedimiento_subcontratistas').prop('checked')){procedimiento_subcontratistas=1;}
                            var obs_procedimiento_subcontratistas = $('#obs_procedimiento_subcontratistas').val();

                            var parametros = {id: id, 
                                            cvid: cvid,
                                            plan_gestion: plan_gestion,
                                            obs_plan_gestion: obs_plan_gestion,
                                            control_documentos: control_documentos,
                                            obs_control_documentos: obs_control_documentos,
                                            politica_calidad: politica_calidad,
                                            obs_politica_calidad: obs_politica_calidad,
                                            auditorias_internas: auditorias_internas,
                                            obs_auditorias_internas: obs_auditorias_internas,
                                            plan_sostenibilidad: plan_sostenibilidad,
                                            obs_plan_sostenibilidad: obs_plan_sostenibilidad,
                                            sg_medioambiental: sg_medioambiental,
                                            obs_sg_medioambiental: obs_sg_medioambiental,
                                            acciones_rsc: acciones_rsc,
                                            obs_acciones_rsc: obs_acciones_rsc,
                                            gestion_rsc: gestion_rsc,
                                            obs_gestion_rsc: obs_gestion_rsc,                                                    
                                            sg_seguridad_y_salud: sg_seguridad_y_salud,
                                            obs_sg_seguridad_y_salud: obs_sg_seguridad_y_salud,
                                            certificado_formacion: sg_seguridad_y_salud,
                                            obs_certificado_formacion: obs_certificado_formacion,           
                                            departamento_segurida_y_salud: departamento_segurida_y_salud,
                                            obs_departamento_segurida_y_salud: obs_departamento_segurida_y_salud,         
                                            metodologia_segurida_y_salud: metodologia_segurida_y_salud,
                                            obs_metodologia_segurida_y_salud: obs_metodologia_segurida_y_salud,   
                                            formacion_segurida_y_salud: formacion_segurida_y_salud,
                                            obs_formacion_segurida_y_salud: obs_formacion_segurida_y_salud,   
                                            empleado_rp: empleado_rp,
                                            obs_empleado_rp: obs_empleado_rp,   
                                            empresa_asesoramiento: empresa_asesoramiento,
                                            obs_empresa_asesoramiento: obs_empleado_rp,   
                                            procedimiento_subcontratistas: procedimiento_subcontratistas,
                                            obs_procedimiento_subcontratistas: obs_procedimiento_subcontratistas};
                            $.ajax({ 
                                async: false, 
                                type: 'GET',
                                data: parametros,                  
                                url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/editarIntegratedmanagementsystem.php',  				
                                success:function(data){
                                    location.reload();
                                    //alert(data);
                                },
                                error: function(result) {
                                    alert('Error de conexión con la base de datos!');
                                }
                            });                                                     

                        });


                        $('.boton_limpieza').on('click', function(){
                            var confirmacion = confirm('¿Realmente desea limpiar todo el formulario?');
                            if(confirmacion){
                                var id = $('#idIMS').val();     
                                var cvid = $('#cv_id').val();                                    
                                var parametros = {id: id, 
                                                cvid: cvid,
                                                plan_gestion: 0,
                                                obs_plan_gestion: '',
                                                control_documentos: 0,
                                                obs_control_documentos: '',
                                                politica_calidad: 0,
                                                obs_politica_calidad: '',
                                                auditorias_internas: 0,
                                                obs_auditorias_internas: '',
                                                plan_sostenibilidad: 0,
                                                obs_plan_sostenibilidad: '',
                                                sg_medioambiental: 0,
                                                obs_sg_medioambiental: '',
                                                acciones_rsc: 0,
                                                obs_acciones_rsc: '',
                                                gestion_rsc: 0,
                                                obs_gestion_rsc: '',                                                    
                                                sg_seguridad_y_salud: 0,
                                                obs_sg_seguridad_y_salud: '',
                                                certificado_formacion: 0,
                                                obs_certificado_formacion: '',           
                                                departamento_segurida_y_salud: 0,
                                                obs_departamento_segurida_y_salud: '',         
                                                metodologia_segurida_y_salud: 0,
                                                obs_metodologia_segurida_y_salud: '',   
                                                formacion_segurida_y_salud: 0,
                                                obs_formacion_segurida_y_salud: '',   
                                                empleado_rp: 0,
                                                obs_empleado_rp: '',   
                                                empresa_asesoramiento: 0,
                                                obs_empresa_asesoramiento: '',   
                                                procedimiento_subcontratistas: 0,
                                                obs_procedimiento_subcontratistas: ''};
                                $.ajax({ 
                                    async: false, 
                                    type: 'GET',
                                    data: parametros,                  
                                    url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/editarIntegratedmanagementsystem.php',  				
                                    success:function(data){
                                        location.reload();
                                        //alert(data);
                                    },
                                    error: function(result) {
                                        alert('Error de conexión con la base de datos!');
                                    }
                                });                                         
                            }
                        });


                        function srchLosratios(){
                            var CVID = $('#cv_id').val();
                            $.ajax({ 
                                async: false, 
                                type: 'GET',
                                data: {'cvid': CVID},                  
                                url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/srchLosratios.php',  				
                                success:function(data){
                                    $('#losRatios').html(data);
                                    var num = $('#tblSiniestrabilidad').find('td:eq(1)').text().replace(',','.');
                                    var frec = $('#tblSiniestrabilidad').find('td:eq(2)').text().replace(',','.');
                                    var grav = $('#tblSiniestrabilidad').find('td:eq(3)').text().replace(',','.');
                                    
                                    verificaSiniestralidad(num, frec, grav);
                                },
                                error: function(result) {
                                    alert('Error de conexión con la base de datos!');
                                }
                            });                            
                        }
                        
                        srchLosratios();
                        
                        
                        function verificaCalidad(){
                            var strImg = '".$CFG_GLPI["root_doc"]."/pics/no.png';
                                
                            var plangestion         = $('#plan_gestion').prop('checked');
                            var auditorias_internas = $('#auditorias_internas').prop('checked');
                            var politica_calidad    = $('#politica_calidad').prop('checked');
                            var control_documentos  = $('#control_documentos').prop('checked');
                            
                            if(plangestion && auditorias_internas && politica_calidad && control_documentos){
                                strImg='".$CFG_GLPI["root_doc"]."/pics/si.png';
                            }else{
                                strImg='".$CFG_GLPI["root_doc"]."/pics/no.png';
                            }
                            $('#imagenCalidad').prop('src', strImg);                            
                        }
                        
                        function verificaSostenibilidad(){
                            var strImg = '".$CFG_GLPI["root_doc"]."/pics/no.png';
                                
                            var plan_sostenibilidad = $('#plan_sostenibilidad').prop('checked');
                            var sg_medioambiental   = $('#sg_medioambiental').prop('checked');
                            
                            if(plan_sostenibilidad && sg_medioambiental){
                                strImg='".$CFG_GLPI["root_doc"]."/pics/si.png';
                            }else{
                                strImg='".$CFG_GLPI["root_doc"]."/pics/no.png';
                            }
                            $('#imagenSostenibilidad').prop('src', strImg);                            
                        }
                        
                        $('.chkProp').on('click', function(){
                            var id=$(this).attr('id');
                            
                            switch(id) {
                              case 'plan_gestion':
                                verificaCalidad();
                                break;
                              case 'auditorias_internas':
                                verificaCalidad();
                                break;                                
                              case 'politica_calidad':
                                verificaCalidad();
                                break;                                
                              case 'control_documentos':
                                verificaCalidad();
                                break;
                              case 'plan_sostenibilidad':
                                verificaSostenibilidad();
                                break;                                      
                              case 'sg_medioambiental':
                                verificaSostenibilidad();
                                break;                                
                              default:
                                break;
                            }                            
                        });

                        if((profile!=16) && (profile!=3) && (profile!=14) && (profile!=4) && (profile!=9)){
                            $('.chkProp').prop('disabled', 'true');
                            $('.boton_borrar').css('display', 'none');
                            $('.boton_add').css('display', 'none');
                            $('#cero').css('display', 'none');
                            $('textarea').prop('disabled', 'true');
                        }
                        
                </script>";                    
        }
                
                // USUARIO DE BOVIS
		function showFormItemSIG($item, $withtemplate='') {	
                    $this->gestion($item);
		}
                
                // USUARIO GESTOR DEL PROVEEDOR
		function showFormItem($item, $withtemplate='') {	
                    $this->gestion($item);
		}                

                // EN EL CASO DE NO TENER CV
		function showFormNoCV($ID, $options=[]) {
                    echo "<div>Necesitas gestionar el CV antes de acceder a Sistema integrado de gestión</div>";
                    echo "<br>";
		}
	
                
		function showForm($ID, $options=[]) {
                    
		}


}