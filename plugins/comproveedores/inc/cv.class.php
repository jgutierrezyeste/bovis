<?php

/******************************************

	PLUGIN DE GESTION DE CURRICULUMS DE LOS PROVEEDORES


 ******************************************/

	class PluginComproveedoresCv extends CommonDBTM{

		static public $itemtype	=	'PluginComproveedoresComproveedore';
		static public $items_id	=	'plugin_comproveedores_cv';
		static $types = array('Computer');

		static $rightname	= "plugin_comproveedores";

		static function getTypeName($nb=0){
			return _n('DATOS GENERALES','DATOS GENERALES',1,'comproveedores');
		}

		function getTabNameForItem(CommonGLPI $item, $tabnum=1,$withtemplate=0){
			if($item-> getType()=="Supplier"){
				return self::createTabEntry('Gestión de CV');
			}
			return 'CV Detallado';
		}

		static function displayTabContentForItem(CommonGLPI $item,$tabnum=1,$withtemplate=0){
			
			
			global $CFG_GLPI;
			$self = new self();
                        
                        /*
			if($item->getType()=='Supplier'){
				$self->showFormItem($item, $withtemplate);
			}else if($item->getType()=='PluginComproveedoresComproveedore'){
				$self->showFormComproveedores();
			}else{
				//$self->showForm();
			}*/
                        $self->showFormItem($item, $withtemplate);
		}
		
		function getSearchOptions(){

			$tab = array();

			$tab['common'] = ('CVs');

			$tab[1]['table']	=$this->getTable();
			$tab[1]['field']	='name';
			$tab[1]['name']		=__('Name');
			$tab[1]['datatype']		='itemlink';
			$tab[1]['itemlink_type']	=$this->getTable();

			return $tab;

		}

		function defineTabs($options=array()){
                    $ong = array();
                    $user_Id=$_SESSION['glpiID'];
                    $profile_Id=$this->getProfileByUserID($user_Id);
                                        
                    $this->addDefaultFormTab($ong);						
                    $this->addStandardTab('PluginComproveedoresUser', $ong, $options);
                    $this->addStandardTab('PluginComproveedoresExperience', $ong, $options);
                    $this->addStandardTab('PluginComproveedoresListspecialty', $ong, $options);
                    $this->addStandardTab('PluginComproveedoresEmpleado', $ong, $options);
                    $this->addStandardTab('PluginComproveedoresInsurance', $ong, $options);
                    $this->addStandardTab('PluginComproveedoresIntegratedmanagementsystem', $ong, $options);
                    $this->addStandardTab('PluginComproveedoresFinancial', $ong, $options);
                    $this->addStandardTab('PluginComproveedoresUser', $ong, $options);
                    if(in_array(array(3,4,16),$profile_Id)){
                        $this->addStandardTab('PluginComproveedoresValuation', $ong, $options);
                    }
                    //$this->addStandardTab('PluginComproveedoresHistory', $ong, $options);
                         
                    return $ong;
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

                
                function getUserName($iduser){
                    global $DB;
                    $query = "Select id, name, concat(realname,' ',firstname) as nombre From glpi_users Where id={$iduser}";
                    $r = $DB->query($query);
                    $c = $DB->fetch_array($r);
                    $name = $c['nombre'];  
                    return $name;
                }
              
            /** USUARIO ESTRATÉGICO Y ADMINISTRADOR **/
            function showFormItem($item, $withtemplate='') {
                global $DB;
                global $CFG_GLPI;

                $options = [];
                $supplier_id  = $item->fields['id'];
                $q = "select * from glpi_suppliers where id = {$supplier_id} limit 1";
                $r = $DB->query($q);
                //echo $q;
                $supplier = $DB->fetch_array($r);

                if(isset($supplier['cv_id']) && $supplier['cv_id']!=0){
                    $ID = $supplier['cv_id'];
                }else{
                    //Hay que dar de alta el CV del proveedor
                    $aux = getdate();
                    if($aux['mon']<10){$mes='0'.$aux['mon'];}else{$mes=$aux['mon'];}
                    if($aux['mday']<10){$dia = '0'.$aux['mday'];}else{$dia=$aux['mday'];}
                    $fecha_alta = $aux['year'].'-'.$mes.'-'.$dia;
                    $usuarioID = $_SESSION['glpiID'];
                    $sql = "INSERT INTO glpi_plugin_comproveedores_cvs (name,
                            supplier_id,
                            fecha_alta,
                            usuario_alta
                            ) VALUES ('{$name}',{$supplier_id},'{$fecha_alta}',{$usuarioID})";                            
                    $result = $DB->query($sql);

                    $auxid = 0;
                    $sql = "select max(id) as id from glpi_plugin_comproveedores_cvs where is_deleted=0 and supplier_id={$supplier_id}";
                    $r = $DB->query($sql);
                    while ($dat = $DB->fetch_array($r)) {                           
                         $auxid = $dat[0];
                    }

                    $sql = "UPDATE glpi_suppliers
                            SET cv_id = {$auxid}
                           WHERE id = {$supplier_id}";    
                    $r2 = $DB->query($sql);                            
                    $ID = $auxid;
                    echo $fecha_alta;
                }
                //echo $ID;

                $self = new self();    
                $self->muestraCV($ID, $options);
        }
                                

        /** USUARIO EDITOR PROVEEDOR **/
        function showForm($ID, $options=[]) {
                global $DB;
                global $CFG_GLPI;

                $self = new self();
                $self->muestraCV($ID, $options);
        }
                
        function botones($ver){
            //Si el perfil es: general o proveedor, no puede exportar
            if($ver){
                $b = ",
                    'dom': 'Bfrtip',
                            'buttons': [
                                'copyHtml5',
                                'excelHtml5',
                                'pdfHtml5'
                            ]";                        
            }else{
                $b = "";
            }
            return $b;
        }


        function muestraCV($ID, $options=[]) {
            global $DB;
            global $CFG_GLPI;

            echo "<style>
                .botones{
                    padding: 10px;
                    background-color: #ccc;
                    margin-bottom: 10px;
                    text-align: left;
                }                         
                .vigencia{
                    color: #444;
                    font-size: 12px;
                }
                .filaFormulario{
                    color: #444;
                    font-size: 14px;
                }
                .marco {
                    width: 90%;
                    background-color: #e9ecf3;
                    height: 450px;
                    border-radius: 4px;
                    overflow-y: auto;                                
                }                       
                .boton_borrar {
                    margin-left: 0px !important; 
                }

            </style>";
            //echo "<strong>".$ID."</strong>";
            if($ID<1){
                $name                               = '';
                $supplier_id                        = $ID;
                $empresa_matriz_nombre              = '';
                $empresa_matriz_direccion           = '';
                $empresa_matriz_pais                = '';
                $empresa_matriz_ciudad              = '';
                $empresa_matriz_provincia           = '';
                $empresa_matriz_cp                  = '';
                $titulacion_superior                = 0;
                $titulacion_grado_medio             = 0;
                $tecnicos_no_universitarios         = 0;
                $personal                           = 0;
                $otros_categoria_numeros_empleados  = 0;
                $aceptacion                         = 0;
                $fecha_aceptacion                   = '';
                $usuario_aceptacion                 = 0;
                $fecha_ultima_modificacion          = '';
                $usuario_ultima_modificacion        = 0;
                    $f = getdate();
                    if($f['mon']<10){$mes='0'.$f['mon'];}else{$mes=$f['mon'];}
                $fecha_alta = $f['year'].'-'.$mes.'-'.$f['mday'].' '.$f['hours'].':'.$f['minutes'].':'.$f['seconds'];                        
                $usuario_alta                       = 0;    
                $fechaAux                           = '';                       
                $fechaCad                           = '';
            }else{
                $q = "select * from glpi_plugin_comproveedores_cvs where is_deleted=0 and id = {$ID}";
                $r = $DB->query($q);
                $cv = $DB->fetch_array($r);

                // LECTURA DE CAMPOS
                $name                               = $cv['name'];
                $supplier_id                        = $cv['supplier_id'];
                $empresa_matriz_nombre              = $cv['empresa_matriz_nombre'];
                $empresa_matriz_direccion           = $cv['empresa_matriz_direccion'];
                $empresa_matriz_pais                = $cv['empresa_matriz_pais'];
                $empresa_matriz_ciudad              = $cv['empresa_matriz_ciudad'];
                $empresa_matriz_provincia           = $cv['empresa_matriz_provincia'];
                $empresa_matriz_cp                  = $cv['empresa_matriz_CP'];
                $titulacion_superior                = $cv['titulacion_superior'];
                $titulacion_grado_medio             = $cv['titulacion_grado_medio'];
                $tecnicos_no_universitarios         = $cv['tecnicos_no_universitarios'];
                $personal                           = $cv['personal'];
                $otros_categoria_numeros_empleados  = $cv['otros_categoria_numeros_empleados'];
                $capital_social                     = $cv['capital_social'];
                $aceptacion                         = $cv['aceptacion'];
                $fecha_aceptacion                   = date("d-m-Y", strtotime($cv['fecha_aceptacion']));
                $usuario_aceptacion                 = $cv['usuario_aceptacion'];
                
                if($cv['fecha_ultima_modificacion']=='0000-00-00 00:00:00'){ 
                    $fecha_ultima_modificacion = ''; 
                }else{
                    $fecha_ultima_modificacion = date("d-m-Y", strtotime($cv['fecha_ultima_modificacion']));
                }
                $usuario_ultima_modificacion = $cv['usuario_ultima_modificacion'];                        
                if($cv['fecha_alta']=='0000-00-00 00:00:00'){
                    $fecha_alta = '';
                }else{
                    $fecha_alta = date("d-m-Y", strtotime($cv['fecha_alta']));
                }
                $usuario_alta                       = $cv['usuario_alta'];    
                $fechaAux                           = $cv['fecha_aceptacion'];   
                if($fechaAux=='0000-00-00 00:00:00'){ 
                    $f = getdate();
                    if($f['mon']<10){$mes='0'.$f['mon'];}else{$mes=$f['mon'];}
                    $fechaAux = $f['year'].'-'.$mes.'-'.$f['mday'].' '.$f['hours'].':'.$f['minutes'].':'.$f['seconds'];
                }                        
                $fechaCad = date("d-m-Y", strtotime($fecha_ultima_modificacion."+1 year"));
                if($cv['fecha_alta']=='0000-00-00- 00:00:00'){ 
                    $fecha_alta = $fechaAux; 
                }else{
                    $fecha_alta = $cv['fecha_alta'];
                }
            }

            $fecha_aceptacion = $fechaAux;
            $self                = new self();
            //OBTENEMOS DATOS DEL PROVEEDOR
            $supplier            = new Supplier();
            $supplier            = $self->getSupplierCompleteBySupplierId($supplier_id);          
            $options             = array();

            //DATOS DEL PERFIL Y USUARIO
            $options['colspan']  = 4;
            $options['cv']       = true;
            $user_Id             = $_SESSION['glpiID'];
            $profile_Id          = $self->getProfileByUserID($user_Id);
            $ver                 = true;
            $verEsp              = true;
            if(in_array($profile_Id, array(3,4,14,16))){    
                $ver = true;
            }else{
                $ver = false;
            } 
            $auxfechaalta = substr($fecha_alta,8,2).'-'.substr($fecha_alta,5,2).'-'.substr($fecha_alta,0,4);
            $auxfechaceptacion = substr($fecha_aceptacion,8,2).'-'.substr($fecha_aceptacion,5,2).'-'.substr($fecha_aceptacion,0,4);
            echo "<div class='botones'>";
//                        if($profile_Id == 14){
//                            $verEsp = false;
//                            echo "<input id='guardarCV' type='submit' class='boton_grabar_off' title='GUARDAR' disabled value='' style='margin-right: 5px;'/>";
//                            echo "<input id='quitarCV' type='submit' class='boton_borrar_off' title='BORRAR'   disabled value='' />";      
//                            echo "<script type='text/javascript'>"                            
//                                    . "$('.boton_borrar_empresadestacada').css('display','none');" 
//                                    . "$('.boton_borrar_nombreanterior').css('display','none');" 
//                                    . "$('.boton_borrar_subcontratista').css('display','none');"                               
//                                    . "</script>";
//                        }else{
                    echo "<input id='guardarCV' type='submit' class='boton_grabar' title='GUARDAR' value='' style='margin-right: 5px;'/>";
                    echo "<input id='quitarCV' type='submit' class='boton_borrar' title='BORRAR'  value='' />";                           
//                        }
                echo "<input id='aceptacionOculto' type='hidden' value='{$aceptacion}' />";
                echo "<input id='usuarioOculto' type='hidden' value='{$user_Id}' />";
                echo "<input id='usuarioAceptacionOculto' type='hidden' value='{$usuario_aceptacion}' />";
                echo "<input id='fechaAceptacionOculto' type='hidden'  value='{$fecha_aceptacion}' />";
                echo "<input id='cvIdOculto' type='hidden'  value='{$ID}' />";
                echo "<input id='supplierIdOculto' type='hidden'  value='{$supplier_id}' />";
                echo "<input id='fechaAltaOculto' type='hidden'  value='{$fecha_alta}' />";
                echo "<input id='verEspOculto' type='hidden'  value='{$verEsp}' />";
                //echo "<input id='fechaVigenciaOculto' type='hidden'  value='{$fecha_alta}' />";
            echo "</div>";
            echo "<div class='marco tab_cadre'>";

            $self = new self();   
//                        if($cv_id']>0){
//                            $ID = $cv_id'];
//                        }                    
            $options             = array();
            $options['colspan']  = 4;
            $options['cv']       = true;                   
            //oculto los distintos menús si el usuario no tiene un perfil adecuado
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
                . "$('.boton_add').css('display','none');"     
                . "</script>";
            }            

            echo "<table class='center' style='width: 100%;'>";
            //si ya ha aceptado la cesión de datos se muestra la opción de cancelación
            if($aceptacion=='1'){
                echo "<tr class='tab_bg_2 tab_cadre_fixehov nohover' style='background-color:#e6e6e6;'>";
                    echo "<td colspan='4' style='padding: 2px;'>";
                        echo "Si desea cancelar la inclusión de su empresa en nuestra base de datos, por favor, envíe un correo
                        a la dirección <a href:'mailto=informatica@bovis.es'>informatica@bovis.es</a>";
                    echo "</td>";
                echo "</tr>";
            }             
            //DATOS DE VIGENCIA
            echo "<tr class='tab_bg_2 tab_cadre_fixehov nohover'><th colspan='4' style='color: #fff; background-color: #0e52a0; text-align: left;'>» ".__("DATOS DE VIGENCIA")."</th></tr>";
            echo "<tr>";
            echo "<td style='display: inline-flex;'>";
                echo "<table>";
                echo "<tr>";
                    echo "<td id='fechaAltaTD' class='center vigencia' style='padding: 4px; font-weight: bold; font-size: 14px; border: 1px solid #ccc; border-radius: 4px; background-color: #e6e6e6;'>";
                    echo "Fecha de alta: {$auxfechaalta}";
                    echo "</td>";
                    echo "<td id='fechaModificacionTD' class='center vigencia' style='padding: 4px; font-weight: bold; font-size: 14px; border: 1px solid #ccc; border-radius: 4px; background-color: #e6e6e6;'>";
                    echo "Fecha de la última modificación: {$fecha_ultima_modificacion}";
                    echo "</td>";
                    echo "<td id='fechaFinVigenciaTD' class='center vigencia' style='padding: 4px; font-weight: bold; font-size: 14px; border: 1px solid #ccc; border-radius: 4px; background-color: #e6e6e6;'>";
                    echo "Fecha de fin de vigencia: {$fechaCad}";
                    echo "</td>";
                    echo "<td id='fechaAceptacionTD' class='center vigencia' style='padding: 4px; font-weight: bold; font-size: 14px; border: 1px solid #ccc; border-radius: 4px; background-color: #e6e6e6;'>";
                    echo "Fecha de aceptación: {$auxfechaceptacion}</td>";
                    echo "</td>";
                echo "</tr>";
                echo "</table>";
            echo "</td>";
            echo "</tr>";

            //DATOS DE CONTACTO
            echo "<tr class='tab_bg_2 tab_cadre_fixehov nohover'><th colspan='4' style='color: #fff; background-color: #0e52a0; text-align: left;'>» ".__("INFORMACIÓN DE CONTACTO")."</th></tr>";                        
            echo "<tr class='filaFormulario'>";
            echo "<td class='center'>";
            echo "<table style='margin: 14px; width: 80%;'>";
            echo "<tr class='filaFormulario'>";
                echo "<td class='right'>NOMBRE/RAZÓN SOCIAL: </td>";
                echo "<td colspan='3' class='left'>";
                echo "<input type='text' id='SUP_name' value='{$supplier->fields['name']}' style='width:500px; font-size: 14px;' />";
                echo "</td>";
            echo "</tr>";
            echo "<tr class='filaFormulario'>";                  
                echo "<td class='right'>CIF: </td>";
                echo "<td class='left'>";
                echo "<input id='SUP_cif' type='text' value='{$supplier->fields['cif']}' />";
                echo "</td>";                    
                echo "<td class='right'>FÓRMULA JURÍDICA: </td>";	
                echo "<td class='left'>";
                SupplierType::dropdown(['value' => $supplier->fields['suppliertypes_id']]);
                echo "</td>";
            echo "</tr>";
            echo "<tr class='filaFormulario'>";
                echo "<td class='right'>EMAIL: </td>";
                echo "<td class='left' style>";

                echo "<input id='SUP_email' type='text' value='{$supplier->fields['email']}' />";
                echo "</td>";
                echo "<td class='right'>TELÉFONO: </td>";
                echo "<td class='left'>";

                echo "<input id='SUP_phonenumber' type='text' value='{$supplier->fields['phonenumber']}' />";
                echo "</td>";                        
            echo "</tr>";
            echo "<tr class='filaFormulario'>";
                echo "<td class='right'>FAX: </td>";
                echo "<td class='left'>";

                echo "<input id='SUP_fax' type='text' value='{$supplier->fields['fax']}' />";
                echo "</td>";
                echo "<td class='right'>WEBSITE: </td>";
                echo "<td class='left'>";

                echo "<input id='SUP_website' type='text' value='{$supplier->fields['website']}' />";
                echo "</td>";
            echo "</tr>";
            echo "<tr class='filaFormulario'>";
                echo "<td class='right top'>DIRECCIÓN: </td>";
                echo "<td colspan='3' class='left'>";
                echo "<textarea cols='74' rows='3' id='SUP_address' name='address'>".$supplier->fields["address"]."</textarea>";
                echo "</td>";
            echo "</tr>";
            echo"<tr class='filaFormulario'>";
                echo "<td class='right'>CÓD. POSTAL: </td>";
                echo "<td class='left'>";

                echo "<input id='SUP_postcode' type='text' value='{$supplier->fields['postcode']}' />";
                echo "</td>";
                echo "<td class='right'>PAÍS: </td>";
                echo "<td class='left'>";
                echo "<input id='SUP_country' type='text' value='{$supplier->fields['country']}' />";
                echo "</td>";                        
            echo"</tr>";                    
            echo"<tr class='filaFormulario' >";
                echo "<td class='right'>PROVINCIA: </td>";
                echo "<td class='left'>";

                echo "<input id='SUP_state' type='text' value='{$supplier->fields['state']}' />";
                echo "</td>";
                echo"<td class='right'>LOCALIDAD: </td>";
                echo "<td class='left'>";
                echo "<input id='SUP_town' type='text' value='{$supplier->fields['town']}' />";
            echo "</td></tr>";                             
            echo "</table>";
            echo "</td>";
            echo "</tr>";


            //ÁMBITO GEOGRÁFICO DEL TRABAJO
            echo "<tr class='tab_bg_2 tab_cadre_fixehov nohover'><th colspan='4' style='color: #fff; background-color: #0e52a0; text-align: left;'>» ".__("ÁMBITO GEOGRÁFICO DE TRABAJO")."</th></tr>";
            echo "<tr>";
            echo "<td class='center' style='display:grid;'>";
                echo "<table>";
                echo "<tr class='filaFormulario'><td class='right top' style='padding: 4px;'>ÁMBITO GEOGRÁFICO:</td>";
                echo "<td colspan='3' class='center' style='padding: 4px;'>";
                    echo "<div id='IdAmbitos' style='width: 300px;' class='list-ambitos'>";
                    $sqlambito = "SELECT a.ID, a.NAME, 
                        if((select count(*) as num from glpi_plugin_comproveedores_listambitos as l where (l.cv_id={$ID} or {$ID}=0) and plugin_comproveedores_ambitos_id=a.id)>0, true, false) as presencia 
                    FROM glpi_plugin_comproveedores_ambitos as a";
                        //echo $sqlambito;
                        $resultambito = $DB->query($sqlambito);
                        $num = 0;
                        echo "<ul>";
                        echo "<li id='lineaAmbito_0' class='lineaAmbito'><input id='checkAmbito_0' type='checkbox' value='0' class='ambitos_check'><label id='etiquetaAmbito_0' class='etiquetaAmbito_check' style='font-weight:bold;'> TODAS LAS CCAA (ESPAÑA) </label></li>";
                        while ($dataambito = $DB->fetch_array($resultambito)) {
                            if($dataambito['presencia']==0){
                                echo "<li id='lineaAmbito_{$dataambito['ID']}' class='lineaAmbito'><input id='checkAmbito_{$dataambito['ID']}' value='{$dataambito['ID']}' class='ambitos_check' type='checkbox' ><label id='etiquetaAmbito_{$dataambito['ID']}' class='etiquetaAmbito_check'> {$dataambito['NAME']}</label></li>";
                            }else{
                                echo "<li id='lineaAmbito_{$dataambito['ID']}' class='lineaAmbito'><input id='checkAmbito_{$dataambito['ID']}' value='{$dataambito['ID']}' class='ambitos_check' type='checkbox' checked><label id='etiquetaAmbito_{$dataambito['ID']}' class='etiquetaAmbito_check'> {$dataambito['NAME']}</label></li>";
                            }
                            $num++;
                        }
                        echo "</ul>";
                    echo "</div>";                        
                echo "</td>";
                echo "</tr>";    
                echo "</table>";
            echo "</td>";
            echo "</tr>";                        

            // EMPRESA MATRIZ
            echo "<tr class='tab_bg_2 tab_cadre_fixehov nohover'><th colspan='4' style='color: #fff; background-color: #0e52a0; text-align:left;' >» ".__("EMPRESA MATRIZ (si la tiene)")."</th></tr>";
            echo "<tr>";
            echo "<td class='center' style='display:inline-flex;'>";
                echo "<table style='width: 70%;'>";
                echo "<tr><td class='right' style='padding: 4px;'>RAZÓN SOCIAL:</td>";
                echo "<td colspan='3' class='left' style='padding: 4px;'>";
                echo "<input id='empresa_matriz_nombre' type='text' value='{$empresa_matriz_nombre}' />";
                echo "</td>";
                echo "</tr>";

                echo "<tr>";
                echo "<td class='right top' style='padding: 4px;'>DIRECCIÓN: </td>";
                echo "<td colspan='3' class='left' style='padding: 4px;'>";
                echo "<textarea cols='81' rows='3' ID='empresa_matriz_direccion' style='padding: 4px;'>{$empresa_matriz_direccion}</textarea>";
                echo "</td>";
                echo "</tr>";

                echo"<tr>";
                echo "<td class='right' style='padding: 4px;'>CÓD. POSTAL: </td>";
                echo "<td class='left' style='padding: 4px;'>";
                echo "<input id='empresa_matriz_CP' type='text' value='{$empresa_matriz_CP}' />";
                echo "</td>";                             
                echo "<td class='right' style='padding: 4px;'>PROVINCIA:</td>";
                echo "<td class='left' style='padding: 4px;'>";
                echo "<input id='empresa_matriz_provincia' type='text' value='{$empresa_matriz_provincia}' />";
                echo "</td>";
                echo "</tr>";

                echo"<tr>";
                echo "<td class='right' style='padding: 4px;'>LOCALIDAD: </td>";
                echo "<td class='left' style='padding: 4px;'>";
                echo "<input id='empresa_matriz_ciudad' type='text' value='{$empresa_matriz_ciudad}' />";
                echo "</td>";                            
                echo "<td class='right' style='padding: 4px;'>PAÍS: </td>";
                echo "<td class='left' style='padding: 4px;'>";
                echo "<input id='empresa_matriz_pais' type='text' value='{$empresa_matriz_pais}' />";
                echo "</td>";                             
                echo "</tr>";
                echo "</table>";
            echo "</td>";
            echo "</tr>";                        

            // EMPRESAS MAS DESTACADAS DEL GRUPO
            echo "<tr class='tab_bg_2 tab_cadre_fixehov nohover'><th colspan='4' style='color: #fff; background-color: #0e52a0; text-align: left;'>» LISTA DE EMPRESAS MÁS DESTACADAS DEL GRUPO</th></tr>";
            echo "<tr>";                        
            echo "<td id='empresadestacadaTD' class='center' style='padding: 10px; display: inline-flex;'>";                           

            echo "</td>";
            echo "</tr>";
                        
            // NOMBRES ANTERIORES DE LA EMPRESA
            echo "<tr class='tab_bg_2 tab_cadre_fixehov nohover'>";
            echo "<th id='listaAnteriores' colspan='4' style='color: #fff; background-color: #0e52a0; text-align: left;'>» LISTA DE NOMBRES ANTERIORES DE LA EMPRESA, SI HUBIERA CAMBIADO EN LOS ÚLTIMOS 5 AÑOS</th></tr>";
            echo "<tr>";
            echo "<td id='nombresanterioresTD' style='padding: 10px; display: inline-flex;'  class='center'>";

            echo "</td>";
            echo "</tr>";              

            // CATEGORÍA PROFESIONAL
            echo "<tr class='tab_bg_2 tab_cadre_fixehov nohover'><th colspan='4' style='color: #fff; background-color: #0e52a0; text-align: left;'>» ".__("CATEGORÍA Y NÚMERO DE EMPLEADOS")."</th></tr>";
            echo "<tr>";
                echo "<td class='center' style='display: inline-flex'>";
                    echo "<table class='center'>";
                        echo "<tr>";
                            echo "<td class='right'>Nº DE EMPLEADOS CON TITULACIÓN UNIVERSITARIA:</td>";
                            echo "<td class='left' style='padding: 4px;'>";
                            echo "<input id='titulacion_superior' type='text' value='{$titulacion_superior}' />";
                            echo "</td>";
                            echo "<td class='right' style='padding: 4px;'>Nº TOTAL DE EMPLEADOS:</td>";
                            echo "<td class='left' style='padding: 4px;'>";
                            echo "<input id='personal' type='text' value='{$personal}' />";
                            echo "</td>";
                        echo "</tr>";
                        echo "<tr>";
                            echo "<td class='right' style='padding: 4px;'>Nº DE EMPLEADOS CON TITULACIÓN DE GRADO MEDIO:</td>";
                            echo "<td class='left' style='padding: 4px;'>";
                            echo "<input id='titulacion_grado_medio' type='text' value='{$titulacion_grado_medio}' />";
                            echo "</td>";
                            echo "<td class='right' style='padding: 4px;'>Nº DE EMPLEADOS EN OTRAS CATEGORÍAS:</td>";
                            echo "</td>";
                            echo "<td class='left' style='padding: 4px;'>";
                            echo "<input id='otros_categoria_numeros_empleados' type='text' value='{$otros_categoria_numeros_empleados}' />";
                            echo "</td>";
                        echo "</tr>";
                        echo "<tr>";
                            echo "<td class='right' style='padding: 4px;'>Nº DE TÉCNICOS NO UNIVERSITARIOS:</td>";
                            echo "<td class='left' style='padding: 4px;'>";
                            echo "<input id='tecnicos_no_universitarios' type='text' value='{$tecnicos_no_universitarios}' />";
                            echo "</td>";
                        echo "</tr>";
                    echo "</table>";
                echo "</td>";
            echo "</tr>";        

            // PRINCIPALES SUBCONTRATISTAS
            echo "<tr class='tab_bg_2 tab_cadre_fixehov nohover'>";
            echo "<th colspan='4' style='color: #fff; background-color: #0e52a0; text-align: left;'>» PRINCIPALES EMPRESAS SUBCONTRATISTAS, COLABORADORAS O PROFESIONALES QUE TRABAJAN HABITUALMENTE CON LA EMPRESA</th>";
            echo "</tr>";
            echo "<tr>";
                echo "<td id='subcontratistasTD' style='padding: 10px; display: inline-flex;' class='left'>";
                echo "</td>";
            echo "</tr>";

            // COMENTARIOS
            echo "<tr class='tab_bg_2 tab_cadre_fixehov nohover'><th colspan='4' style='color: #fff; background-color: #0e52a0; text-align: left;'>» ".__("COMENTARIOS")."</th></tr>";
            echo "<tr>";
                echo "<td colspan='4' class='center' style='padding: 4px;'>";
                echo "<textarea cols='120' rows='8' id='comment' name='comment' >".$this->fields["comment"]."</textarea>";
                echo "</td>";
            echo "</tr>";

            // MODALES
            echo "<div id='modalAceptacion' title='Aceptación términos confidencialidad' style='padding:10px; text-align: justify;'>";
                echo "En cumplimiento de la normativa vigente en materia de protección de datos le informamos que el "
                . "responsable de sus datos personales es BOVIS PROJECT MANAGEMENT SA, y los utilizará para gestionar su base de datos de contratistas. "
                . "Sus datos no serán cedidos a terceros, salvo por obligaciones legales. Conservaremos los datos mientras "
                . "no manifieste su derecho de supresión. Darse de alta como contratista de BOVIS PROJECT MANAGEMENT SA, "
                . "implica la aceptación de estos términos de privacidad. Puede ejercer sus derechos sobre protección de datos a "
                . "través de info@bovis.es. Puede obtener más información sobre protección de datos en nuestro Aviso Legal.";
            echo "</div>";   




    echo "<script type='text/javascript'>

            srchEmpresasDestacadas ($('#cvIdOculto').val(), $('#verEspOculto').val());
            srchNombresAnteriores  ($('#cvIdOculto').val(), $('#verEspOculto').val());
            srchSubcontratistas    ($('#cvIdOculto').val(), $('#verEspOculto').val());

            var num = $('.ambitos_check:checked').size();
            if(num<{$num}){
                $('#checkAmbito_0').prop('checked', false);
            }else{
                $('#checkAmbito_0').prop('checked', true);
            }

            const paises=['Francia','Portugal','UK'];

            




$('#checkAmbito_0').on('click', function() {
    
                if($(this).prop('checked') == true){

                    for (var i = 20; i < 42; i++)
                        {
                            
                           $('#checkAmbito_'+i+'').prop('checked',true);
                           var texto=$('#etiquetaAmbito_'+i+'').text();
                           var texto=texto.trim();
                           if( paises.includes(texto) )
                            {
                                
                               $('#checkAmbito_'+i+'').prop('checked',false); 
                            } 
                        }
                }else{
                    $('.ambitos_check').prop('checked',false);
                }
});




/*var pais1='Portugal';
var pais2='Francia';
var pais3='UK';

$('#checkAmbito_0').on('click', function() {
    
    
                if($(this).prop('checked') == true){

                    for (var i = 20; i < 42; i++)
                        {
                           
                           $('#checkAmbito_'+i+'').prop('checked',true);
                           var texto=($('#etiquetaAmbito_'+i+'').text());
                           var texto=texto.trim();
                          
                           
                            if (texto == pais1 || texto == pais2 || texto == pais3) 
                            {
                                
                                $('#checkAmbito_'+i+'').prop('checked',false);
                            }
                           
                           
                        }
                }else{
                    $('.ambitos_check').prop('checked',false);
                }
});*/





            $('#addSubcontratistas').on('click', function(){
                $('#modalDenominacionSubcontratistas').val('');
                $('#modalSubcontratistas').dialog('open');
            }); 

            $('#modalAceptacion').dialog({
                autoOpen: false,
                height: 400,
                width: 360,
                modal: true,
                buttons: {
                    'Aceptar': function() {
                        var today = new Date();
                        $('#aceptacionOculto').val(1);
                        $('#usuarioAceptacionOculto').val({$user_Id});
                        var dia = today.getDate();
                        var mes = today.getMonth()+1;
                        dia = (dia<10 ? '0' : '') + dia;
                        mes = (mes<10 ? '0' : '') + mes;
                        anio = today.getFullYear();
                        $('#fechaAceptacionOculto').val(anio+'-'+mes+'-'+dia);    
                        $('#fechaAceptacionTD').text('Fecha de aceptación: '+dia+'-'+mes+'-'+anio);
                        $(this).dialog('close');
                    },
                    'Cancelar': function() {
                        $(this).dialog('close');
                    }
                },
                close: function() {
                    $(this).dialog('close');
                }
            }); 

            if($('#aceptacionOculto').val()=='0'){
                $('#modalAceptacion').dialog('open');
            }

            function srchNombresAnteriores (cvid, verEsp){
                $.ajax({ 
                    async: false, 
                    type: 'GET',
                    data: {'cvid': cvid, 'verEsp': verEsp },
                    url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/srchNombresAnteriores.php',                    
                    success: function(data){
                        $('#nombresanterioresTD').html(data);
                    },
                    error: function(result) {
                        alert('Error al actualizar los nombres anteriores');
                    }
                });                             
            }

            function srchSubcontratistas (cvid, verEsp){
                $.ajax({ 
                    async: false, 
                    type: 'GET',
                    data: {'cvid': cvid, 'verEsp': verEsp },
                    url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/srchSubcontratistas.php',                    
                    success: function(data){
                        $('#subcontratistasTD').html(data);
                    },
                    error: function(result) {
                        alert('Error al actualizar los subcontratistas');
                    }
                });                             
            }
                            
                            
            function srchEmpresasDestacadas (cvid, verEsp){
                $.ajax({ 
                    async: false, 
                    type: 'GET',
                    data: {'cvid': cvid, 'verEsp': verEsp },
                    url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/srchEmpresaDestacada.php',                    
                    success:function(data){
                        $('#empresadestacadaTD').html(data);
                    },
                    error: function(result) {
                        alert('Error al actualizar empresas destacadas');
                    }
                });                             
            }
  

            function gestionAmbito(){
                var cvid = $('#cvIdOculto').val();
                var parametros = {
                    'cvid' : cvid
                };                              
                if(cvid>0){
                    $.ajax({ 
                        async: false, 
                        type: 'GET',
                        data: parametros,                 
                        url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/quitarAmbitos.php',                    
                        success:function(data){
                            grabacionAmbito();
                        },
                        error: function(result) {
                            alert('Error al actualizar ámbitos');
                        }
                    });         
                }
            }

            function grabacionAmbito() {
                var iden = 0;
                var sql = '';
                var cvid = $('#cvIdOculto').val();
                $('.ambitos_check:checked').each(function(){
                    iden = $(this).attr('id').replace('checkAmbito_', '');   
                    $.ajax({ 
                        async: false, 
                        type: 'GET',
                        data: {cvid: cvid, ambitoid: iden},                 
                        url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/insertAmbito.php',                    
                        success:function(data){
                        },
                        error: function(result) {
                            alert('Error al actualizar');
                        }
                    });                                                                                
                });

            };  

            $('#quitarCV').on('click', function(){
                var resp = confirm('¿Realmente desea quitar este CV?');
                if(resp){
                    var cvid = $('#cvIdOculto').val();
                    
                    $.ajax({ 
                        async: false, 
                        type: 'GET',
                        data: {cvid: cvid},                 
                        url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/quitarCV.php',                    
                        success:function(data){
                            alert(data);
                            //window.location.reload(true);
                        },
                        error: function(result) {
                            alert('Error al actualizar');
                        }
                    });                     
                }
            });
                            
            function insertCV(){

                var today = new Date();
                var dia = today.getDate();
                var mes = today.getMonth()+1;
                    dia = (dia<10 ? '0' : '') + dia;
                    mes = (mes<10 ? '0' : '') + mes;
                    anio = today.getFullYear();                                    
                var hoy = anio+'-'+mes+'-'+dia;

                var cvid = $('#cvIdOculto').val();
                var userid = $('#usuarioOculto').val();
                var aceptacion = $('#aceptacionOculto').val();
                var capital_social = $('#capital_social').val();
                var comment  = $('#comment').val();
                var empresa_matriz_nombre = $('#empresa_matriz_nombre').val();
                var empresa_matriz_direccion = $('#empresa_matriz_direccion').val();
                var empresa_matriz_ciudad = $('#empresa_matriz_ciudad').val();
                var empresa_matriz_CP = $('#empresa_matriz_CP').val();
                var empresa_matriz_pais = $('#empresa_matriz_pais').val();
                var empresa_matriz_provincia = $('#empresa_matriz_provincia').val();

                var fecha_aceptacion = $('#fechaAceptacionOculto').val();
                var fecha_alta = $('#fecha_alta').val();
                var usuario_alta = 0;
                if(cvid == '0'){
                    fecha_alta = hoy;
                    usuario_alta = userid;
                }

                var fecha_ultima_modificacion = hoy;
                var id = cvid;
                var name = $('#SUP_name').val();
                var otros_categoria_numeros_empleados = $('#otros_categoria_numeros_empleados').val();
                var personal = $('#personal').val();
                var supplier_id = $('#supplierIdOculto').val();    
                var tecnicos_no_universitarios = $('#tecnicos_no_universitarios').val();
                var titulacion_grado_medio = $('#titulacion_grado_medio').val();
                var titulacion_superior = $('#titulacion_superior').val();
                var usuario_aceptacion = $('#usuarioAceptacionOculto').val();

                var usuario_ultima_modificacion = userid;

                var parametros = {
                        'aceptacion': aceptacion,
                        'capital_social': capital_social,
                        'comment': comment,
                        'empresa_matriz_nombre': empresa_matriz_nombre,
                        'empresa_matriz_direccion': empresa_matriz_direccion,
                        'empresa_matriz_ciudad': empresa_matriz_ciudad,
                        'empresa_matriz_CP': empresa_matriz_CP,
                        'empresa_matriz_pais': empresa_matriz_pais,
                        'empresa_matriz_provincia': empresa_matriz_provincia,
                        'fecha_aceptacion': fecha_aceptacion,
                        'fecha_alta': fecha_alta,
                        'fecha_ultima_modificacion': fecha_ultima_modificacion,
                        'id': id,
                        'name': name,
                        'otros_categoria_numeros_empleados': otros_categoria_numeros_empleados,
                        'personal': personal,
                        'supplier_id': supplier_id,
                        'tecnicos_no_universitarios': tecnicos_no_universitarios,
                        'titulacion_grado_medio': titulacion_grado_medio,
                        'titulacion_superior': titulacion_superior,
                        'usuario_aceptacion': usuario_aceptacion,
                        'usuario_alta': usuario_alta,
                        'usuario_ultima_modificacion': usuario_ultima_modificacion                                         
                };
                var CvId = 0;
                
                $.ajax({  
                    async: false, 
                    type: 'GET',
                    url: '".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/insertCV.php',
                    data: parametros,
                    success: function(data){
                        CvId = data;
                        alert('¡Grabación completa!');
                    },
                    error: function(result) { 
                        CvId = 0;
                        alert('¡Error! no se pudo gabar el CV correctamente.');
                    }                                 
                });                
                return CvId;
            }

            function updateProveedor(id){
                var salida              = false;
                var name                = $('#SUP_name').val();
                var cif                 = $('#SUP_cif').val();
                var suppliertypes_id    = $('input[name=suppliertypes_id]').val();
                var email               = $('#SUP_email').val();
                var phonenumber         = $('#SUP_phonenumber').val();
                var phonenumber         = $('#SUP_phonenumber').val();
                var fax                 = $('#SUP_fax').val();
                var website             = $('#SUP_website').val();
                var address             = $('#SUP_address').val();
                var postcode            = $('#SUP_postcode').val();
                var country             = $('#SUP_country').val();
                var state               = $('#SUP_state').val();
                var town                = $('#SUP_town').val();
                var supplier_id         = $('#supplierIdOculto').val(); 

                var parametros = {
                        'id': supplier_id,
                        'cvid': id,                                    
                        'name': name,
                        'cif': cif,
                        'suppliertypes_id': suppliertypes_id,
                        'email': email,
                        'phonenumber': phonenumber,
                        'fax': fax,
                        'website': website,
                        'address': address,
                        'postcode': postcode,
                        'country': country,
                        'state': state,
                        'town': town
                };

                $.ajax({  
                    type: 'GET',        		
                    url: '".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/updateSupplier.php',
                    data: parametros,
                    success: function(data){
                        salida = true;
                    },
                    error: function(result) { 
                        salida = false;
                    }
                });                
                return salida;
            }

            $('.boton_grabar').on('click', function(){
                $(this).prop('disabled',true);
                gestionAmbito();
                var cvid = 0;
                cvid = insertCV();

                if(cvid>0){
                    var salida = updateProveedor(cvid);
                    if(salida){
                        alert('CV y proveedor grabado con éxito.');
                    }else{
                        //alert('Error al procesar la actualización del proveedor');
                    }   
                }else{
                    alert('CV grabado con éxito');
                }
            });


        </script>";                           
    echo "</div>";                        

}

                

		function getSupplierCompleteByCv($Id){
			global $DB;
			$options=array();
			$query ="SELECT *  FROM glpi_suppliers WHERE cv_id=$Id";

			$result=$DB->query($query);
			$data=$DB->fetch_array($result);

			$itemSupplier=new Supplier();
			
			
			foreach ($data as $key => $value) {
				$itemSupplier->fields[$key]=$value;
			}
			return $itemSupplier;
		}

		function getSupplierCompleteBySupplierId($Id){
			global $DB;
			$options=array();
			$query ="SELECT *  FROM glpi_suppliers WHERE id=$Id";

			$result=$DB->query($query);
			$data=$DB->fetch_array($result);

			$itemSupplier=new Supplier();
			
			
			foreach ($data as $key => $value) {
				$itemSupplier->fields[$key]=$value;
			}
			return $itemSupplier;
		}
                
                function CapitalSocial(){
                    $cs = $this->fields["capital_social"];
                    return $cs;
                }
                
                function getCVByID($Id){
                    global $DB;
                    $query = "select * from glpi_plugins_comproveedores_cvs where is_deleted=0 and id = {$ID}";
                    $result=$DB->query($query);
                    $cv = $DB->fetch_array($result);
                    $this->fields['id'] = $cv['id'];
                    $this->fields['name'] = $cv['name'];
                    $this->fields['supplier_id'] = $cv['supplier_id'];
                    $this->fields['empresa_matriz_nombre'] = $cv['empresa_matriz_nombre'];
                    $this->fields['empresa_matriz_direccion'] = $cv['empresa_matriz_direccion'];
                    $this->fields['empresa_matriz_pais'] = $cv['empresa_matriz_pais'];
                    $this->fields['empresa_matriz_ciudad'] = $cv['empresa_matriz_ciudad'];
                    $this->fields['empresa_matriz_provincia'] = $cv['empresa_matriz_provincia'];
                    $this->fields['empresa_matriz_cp'] = $cv['empresa_matriz_cp'];
                    $this->fields['titulacion_superior'] = $cv['titulacion_superior'];
                    $this->fields['titulacion_grado_medio'] = $cv['titulacion_grado_medio'];
                    $this->fields['tecnicos_no_universitarios'] = $cv['tecnicos_no_universitarios'];
                    $this->fields['personal'] = $cv['personal'];
                    $this->fields['otros_categoria_numeros_empleados'] = $cv['otros_categoria_numeros_empleados'];
                    $this->fields['capital_social'] = $cv['capital_social'];
                    $this->fields['states_id'] = $cv['states_id'];
                    $this->fields['aceptacion'] = $cv['aceptacion'];
                    $this->fields['fecha_aceptacion'] = $cv['fecha_aceptacion'];
                    $this->fields['usurio_aceptacion'] = $cv['usurio_aceptacion'];
                    $this->fields['fecha_ultima_modificacion'] = $cv['fecha_ultima_modificacion'];
                    $this->fields['usuario_ultima_modificacion'] = $cv['fecha_ultima_modificacion'];
                    $this->fields['fecha_alta'] = $cv['fecha_alta'];
                    $this->fields['usuario_alta'] = $cv['usuario_alta'];                    
                    
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
                
                function getCapitalSocial($Id){
			global $DB;

			$query ="SELECT capital_social FROM glpi_ WHERE id=$Id";

			$result=$DB->query($query);
			$id=$DB->fetch_array($result);

			if($id['profile']<>''){
				$options['profile']=$id['profile'];
			}
			return $options['profile'];                    
                }

	}