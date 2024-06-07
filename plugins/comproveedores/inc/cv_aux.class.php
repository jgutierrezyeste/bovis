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
			if($item->getType()=='Supplier'){
				$self->showFormItem($item, $withtemplate);
			}else if($item->getType()=='PluginComproveedoresComproveedore'){
				$self->showFormComproveedores();
			}else{
					//$self->showForm();
			}
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
                    if(in_array($profile_Id, array(3,4,16))){
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
                        $self = new self();     
                        
                        //print_r ($_SESSION);
                        /**
                        foreach($_SESSION as $key =>$valor)
                        {
                                echo "variable : $key Valor: $valor <br>";
                        }**/
                        
			//Aqui entra desde la pestaña de proveedores

			$ID=$item->fields['cv_id'];	
			
			$options = array();
			$options['colspan']  = 4;
			
			$this->initForm($ID, $options);
			$this->showFormHeader($options);
			$user_Id=$_SESSION['glpiID'];

			echo "<tr class='tab_bg_2 tab_cadre_fixehov nohover' style='background-color: #e6e6e6; visibility:collapse;'><th colspan='4'>".__("Información General")." </th></tr>";
			echo "<tr class='tab_bg_2 tab_cadre_fixehov nohover'>";
			echo Html::hidden('supplier_id', array('value' => $item->fields['id']));
			echo"<tr style='visibility:collapse;'>";
                            echo "<td>" . __('Name') . "</td>";
                            echo "<td>";
                            Html::autocompletionTextField($item, "name");
                            echo "</td>";
                            echo "<td>" . __('CIF') . "</td>";
                            echo "<td>";
                            Html::autocompletionTextField($item, "cif");
                            echo "</td>";
			echo "</tr>";
                        echo "<tr style='visibility:collapse;'>";
                            echo "<td>" . __('Forma Juridica') . "</td>";
                            echo "<td>";
                            SupplierType::dropdown(['value' => $item->fields['suppliertypes_id']]);
                            echo "</td>";
                        echo "</tr>";			
                        echo "<tr style='visibility:collapse;'>";               
                            echo "<td>"._n('Email', 'Emails', 1)."</td>";
                            echo "<td>";
                            Html::autocompletionTextField($item, "email");
                            echo "</td>";
			echo "</tr>";
			echo "<tr class='tab_bg_1' style='visibility:collapse;'>";
                            echo "<td>".__('Fax')."</td>";
                            echo "<td>";
                            Html::autocompletionTextField($item, "fax");
                            echo "</td>";
                            echo "<td>".__('Website')."</td>";
                            echo "<td>";
                            Html::autocompletionTextField($item, "website");
                            echo "</td>";
			echo "</tr>";
			echo "<tr class='tab_bg_1' style='visibility:collapse;'>";
                            echo "<td>" . __('Teléfono') . "</td>";
                            echo "<td>";
                            Html::autocompletionTextField($item, "phonenumber");
                            echo "</td>";
			echo"</tr>";

			echo "<tr class='tab_bg_2 tab_cadre_fixehov nohover' style='visibility:collapse;'><th colspan='4'>".__("Dirección")."</th></tr>";
			echo "<tr class='tab_bg_2 tab_cadre_fixehov nohover' style='visibility:collapse;'>";
                            echo "<td class='middle'>".__('Address')."</td>";
                            echo "<td class='middle'>";
                            echo "<textarea cols='37' rows='3' name='address'>".$item->fields["address"]."</textarea>";
                            echo "</td>";
                            echo "<td>".__('Country')."</td>";
                            echo "<td>";
                            Html::autocompletionTextField($item, "country");
                            echo "</td>";
			echo "</tr>";

			echo"<tr class='tab_bg_1' style='visibility:collapse;'>";
                            echo "<td>" . __('Provincia') . "</td>";
                            echo "<td>";
                            Html::autocompletionTextField($item, "state");
                            echo "</td>";
                            echo "<td>". __('City')."</td>";
                            echo "<td>";
                            Html::autocompletionTextField($item, "town", ['size' => 23]);
                            echo "</td>";
                        echo "</tr>";

			echo"<tr class='tab_bg_1' style='visibility:collapse;'>";
                            echo "<td>" . __('Código Postal') . "</td>";
                            echo "<td>";
                            Html::autocompletionTextField($item, "postcode");
                            echo "</td>";
                        echo "</tr>";

			echo "<tr class='tab_bg_2 tab_cadre_fixehov nohover'><th colspan='4' style='background-color: #e6e6e6;'>".__("DATOS DE VIGENCIA DEL CURRICULUM DE EMPRESA")."</th></tr>";
			echo "<tr style='font-size: 14px;'>";
                            $UsuarioAlta = $this->fields["usuario_alta"];
                            echo "<td class='right'>Fecha de creación:  </td>";
                            echo "<td class='left' colspan='3'>";
                            $fecha = new Datetime($this->fields["fecha_alta"]);
                            echo " ".$fecha->format('d-m-Y');
                            if($UsuarioAlta>0){                                
                                echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Usuario: ".$self->getUserName($UsuarioAlta)."</td>";
                            }else{
                                echo "   </td>";
                            }
                        echo "</tr>";
                        echo "<tr style='font-size: 14px;'>";
                            echo "<td class='right'>";
                            echo "Fecha de la última modificación:";
                            echo "</td>";                        
                            echo "<td class='left' colspan='3'>";
                            $fecha = new Datetime($this->fields["fecha_ultima_modificacion"]);
                            echo " ".$fecha->format('d-m-Y');                                      
                            $UsuarioUltimaModificacion = $this->fields["usuario_ultima_modficacion"];  
                            if($UsuarioUltimaModificacion>0){
                                echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Usuario: ".$self->getUserName($UsuarioUltimaModificacion)."</td>";
                            }else{
                                echo "</td>";
                            }
                        echo "</tr>";
                        echo "<tr style='font-weight: bold; font-size: 14px;'>";
                            echo "<td class='right' >";
                            echo "Fecha fin de vigencia:";
                            echo "</td>";                        
                            echo "<td class='left' colspan='3'>";
                            $fechacaducidad = $fecha;
                            $fechacaducidad->modify('+1 year');
                            echo $fechacaducidad->format('d-m-Y');                                      
                            echo "</td>";                              
                        echo "</tr>";       
                        
			echo "<tr class='tab_bg_2 tab_cadre_fixehov nohover'><th colspan='4' style='background-color: #e6e6e6;'>".__("Empresa matriz(Si la tiene)")."</th></tr>";
			//echo "<tr class='tab_bg_2 tab_cadre_fixehov nohover'>";
			echo "<tr><td class='right'>NOMBRE :</td>";
			echo "<td colspan='5' class='left'>";
			Html::autocompletionTextField($this, "empresa_matriz_nombre", ['style' => 'width:800px;']);
			echo "</td>";
			echo "</tr>";

			echo "<tr class=''>";
			echo "<td class='right top'>DIRECCIÓN :</td>";
			echo "<td class='left'>";
			echo "<textarea cols='50' rows='3' name='empresa_matriz_direccion'>".$this->fields["empresa_matriz_direccion"]."</textarea>";

			echo "</td>";
			echo "<td class='right'>PAÍS :</td>";
			echo "<td class='left'>";
			Html::autocompletionTextField($this, "empresa_matriz_pais");
			echo "</td>";
			echo "</tr>";

			echo"<tr class=''>";
			echo "<td class='right'>PROVINCIA :</td>";
			echo "<td class='left'>";
			Html::autocompletionTextField($this, "empresa_matriz_provincia");
			echo"</td>";
			echo"<td class='right'>LOCALIDAD :</td>";
			echo "<td class='left'>";
			Html::autocompletionTextField($this, "empresa_matriz_ciudad", ['size' => 23]);
			echo"</td></tr>";

			echo"<tr class=''>";
			echo "<td class='right'>" . __('Código Postal') . "</td>";
			echo "<td class='left'>";
			Html::autocompletionTextField($this, "empresa_matriz_CP");
			echo "</td></tr>";

			//////////Lista de empresas mas destacadas///////

			echo "<tr class='tab_bg_2 tab_cadre_fixehov nohover'><th colspan='4' style='background-color: #e6e6e6;'>".__("Lista de empresas más destacadas del grupo")."</th></tr>";

			//consultamos la empresas más destacada de este CV
			$empresas_destacadas;
			if($ID!=''){
				$query2 ="SELECT * FROM glpi_plugin_comproveedores_featuredcompanies WHERE cv_id=".$ID." order by puesto asc";
				$result2 = $DB->query($query2);
				if($result2->num_rows!=0){
					$i=1;
					while ($data=$DB->fetch_array($result2)) {
						$empresas_destacadas['nombre_empresa_destacada'.$i]=$data['nombre_empresa_destacada'];
						$i++;
					}
				}
			}
			

			//visualizamos las empresas más destacada en el caso de que existan
			for($i=1; $i<=6; $i+=2){
				echo "<tr class=''>";
				echo "<td colspan='2' center>".$i.". ";
				if(!empty($empresas_destacadas['nombre_empresa_destacada'.$i])){
					Html::autocompletionTextField($this, "nombre_empresa_destacada".$i, array('option'=>'size="50"', 'value'=>$empresas_destacadas['nombre_empresa_destacada'.$i]));
				}else{
					Html::autocompletionTextField($this, "nombre_empresa_destacada".$i, array('option'=>'size="50"'));
				}
				echo "</td>";
				echo "<td colspan='2' center>".($i+1).". ";
				
				if(!empty($empresas_destacadas['nombre_empresa_destacada'.($i+1)])){
					Html::autocompletionTextField($this, "nombre_empresa_destacada".($i+1), array('option'=>'size="50"', 'value'=>$empresas_destacadas['nombre_empresa_destacada'.($i+1)]));
				}else{
					Html::autocompletionTextField($this, "nombre_empresa_destacada".($i+1), array('option'=>'size="50"'));
				}
				echo "</td>";
				echo"</tr>";		
			}

			//////////Lista de nombres anteriores de la empresa, si hubiera cambiado en los Últimos 5 años///////
			echo "<tr class='tab_bg_2 tab_cadre_fixehov nohover'><th colspan='4' style='background-color: #e6e6e6;'>".__("Lista de nombres anteriores de la empresa, si hubiera cambiado en los Últimos 5 años")."</th></tr>";
			//consultamos los nombres anteriores de la empresas de este CV
			$nombre_anteriores;

			//fecha actual y fecha hace 5 años
			$fecha_actual=date("Y-m-d H:i:s.").gettimeofday()["usec"];
			$fecha_ultimos_cambios=date("Y-m-d H:i:s.", strtotime('-5 year')).gettimeofday()["usec"];
			if($ID!=''){
				$query2="SELECT * FROM glpi_plugin_comproveedores_previousnamescompanies WHERE cv_id=".$ID." and  fecha_cambio<=CAST('".$fecha_actual."' AS DATETIME) AND fecha_cambio>= CAST('".$fecha_ultimos_cambios."' AS DATETIME) order by fecha_cambio, id asc limit 6";
				$result2 = $DB->query($query2);
				if($result2->num_rows!=0){
					$i=1;
					while ($data=$DB->fetch_array($result2)) {
						$nombre_anteriores['nombre'.$i]=$data['nombre'];
						$nombre_anteriores['fecha_cambio'.$i]=$data['fecha_cambio'];
						$i++;
					}
				}
			}
			//Visualizamos los nombres anteriores de la empresas en el caso de que existan
			for($i=1; $i<=4; $i+=2){
				echo "<tr class=''>";
				echo "<td colspan='2' center>".$i.". ";
				if(isset($nombre_anteriores['nombre'.$i])){
					Html::autocompletionTextField($this, "nombre".$i, array('option'=>'size="50"', 'value'=>$nombre_anteriores['nombre'.$i]));
					echo Html::hidden('fecha_cambio'.$i, array('value'=>$nombre_anteriores['fecha_cambio'.$i]));
				}else{
					Html::autocompletionTextField($this, "nombre".$i, array('option'=>'size="50"'));
					echo Html::hidden('fecha_cambio'.$i, array('value'=>date("Y-m-d H:i:s.").gettimeofday()["usec"]));
				}
				echo "</td>";
				echo "<td colspan='2' center>".($i+1).". ";
				
				if(isset($nombre_anteriores['nombre'.($i+1)])){
					Html::autocompletionTextField($this, "nombre".($i+1), array('option'=>'size="50"', 'value'=>$nombre_anteriores['nombre'.($i+1)]));
					echo Html::hidden('fecha_cambio'.($i+1), array('value'=>$nombre_anteriores['fecha_cambio'.($i+1)]));
				}else{
					Html::autocompletionTextField($this, "nombre".($i+1), array('option'=>'size="50"'));
					echo Html::hidden('fecha_cambio'.($i+1), array('value'=>date("Y-m-d H:i:s.").gettimeofday()["usec"]));
				}
				echo "</td>";
				echo"</tr>";		
			}

			///////////////////Categoías y número de empleados//////////
			echo "<tr class='tab_bg_2 tab_cadre_fixehov nohover'><th colspan='4' style='background-color: #e6e6e6;'>".__("Categoría y número de empleados")."</th></tr>";

			echo "<tr class=''>";
			echo "<td class='right'NÚMERO DE EMPLEADOS CON TITULACIÓN UNIVERSITARIA = </td>";
                        echo "<td class='left'>";
			Html::autocompletionTextField($this, "titulacion_superior");
			echo "</td>";
			echo "<td class='right'>NÚMERO TOTAL DE EMPLEADOS = </td>";
                        echo "<td class='left'>";
			Html::autocompletionTextField($this, "personal");
			echo "</td>";
			echo "</tr>";
			
			echo "<tr class=''>";
			echo "<td class='right'>NÚMERO DE EMPLEADOS CON TITULACIÓN DE GRADO MEDIO = </td>";
                        echo "<td class='left'>";
			Html::autocompletionTextField($this, "titulacion_grado_medio");
			echo "</td>";
			echo "<td class='right'>NÚMERO DE EMPLEADOS EN OTRAS CATEGORÍAS = </td>";
			echo "</td>";
			echo "<td class='left'>";
                        Html::autocompletionTextField($this, "otros_categoria_numeros_empleados");
                        echo "</td>";
			echo "</tr>";

			echo "<tr class=''>";
			echo "<td class='right'>NÚMERO DE TÉCNICOS NO UNIVERSITARIOS = </td>";
                        echo "<td class='left'>";
			Html::autocompletionTextField($this, "tecnicos_no_universitarios");
			echo "</td>";
			echo "</tr>";

			/////////Principales empresas subcontratista, colaboradoras y/o profesionales que trabajan habitualmente con la empresa ///////

			echo "<tr class='tab_bg_2 tab_cadre_fixehov nohover'><th colspan='4' style='background-color: #e6e6e6;'>".__("Principales empresas subcontratistas, colaboradoras y/o profesionales que trabajan habitualmente con la empresa")."</th></tr>";
			////consultamos la principales empresas subcontratista de este CV
			$empresas_subcontratistas;
			if($ID!=''){
                            $query2 ="SELECT * FROM glpi_plugin_comproveedores_subcontractingcompanies WHERE cv_id=".$ID." order by puesto asc";
                            $result2 = $DB->query($query2);
                            if($result2->num_rows!=0){
                                $i=1;
                                while ($data=$DB->fetch_array($result2)) {
                                        $empresas_subcontratistas['nombre_empresa_subcontratista'.$i]=$data['nombre_empresa_subcontratista'];
                                        $i++;
                                }
                            }
			}

			//visualizamos a principales empresas subcontratista en el caso de que existan
			for($i=1; $i<=10; $i+=2){
				echo "<tr class=''>";
				echo "<td colspan='2' center>".$i.". ";
				if(!empty($empresas_subcontratistas['nombre_empresa_subcontratista'.$i])){
                                    Html::autocompletionTextField($this, "nombre_empresa_subcontratista".$i, array('option'=>'size="50"', 'value'=>$empresas_subcontratistas['nombre_empresa_subcontratista'.$i]));
				}else{
                                    Html::autocompletionTextField($this, "nombre_empresa_subcontratista".$i, array('option'=>'size="50"'));
				}
				echo "</td>";
				echo "<td colspan='2' center>".($i+1).". ";
				if(!empty($empresas_subcontratistas['nombre_empresa_subcontratista'.($i+1)])){
                                    Html::autocompletionTextField($this, "nombre_empresa_subcontratista".($i+1), array('option'=>'size="50"', 'value'=>$empresas_subcontratistas['nombre_empresa_subcontratista'.($i+1)]));
				}else{
                                    Html::autocompletionTextField($this, "nombre_empresa_subcontratista".($i+1), array('option'=>'size="50"'));
				}
				echo "</td>";
				echo"</tr>";		
			}

			echo "<tr class='tab_bg_2 tab_cadre_fixehov nohover'><th colspan='4' style='background-color: #e6e6e6;'>".__("Comentarios")."</th></tr>";
			echo "<tr>";
                            echo "<td class='right top'>COMENTARIOS : </td>";
                            echo "<td colspan='3' class='left'>";
                            echo "<textarea cols='120' rows='8' name='comment' >".$this->fields["comment"]."</textarea>";
                            echo "</td>";
                        echo "</tr>";
			echo "</tbody>";
                        echo "</table>";
                        echo "<script type='text/javascript'>
                            $('#mainformtable').css('width', '99%');
                            $('#mainformtable').css('background-color', '#e9ecf3');
                            $('#mainformtable').removeClass('tab_cadre_fixe');
                            $('#tabsbody').css('max-height', '500px');
                            $('#tabsbody').css('overflow-y', 'scroll');
                        </script>";
			$this->showFormButtons($options);
		}
                

                /** USUARIO EDITOR PROVEEDOR **/
		function showForm($ID, $options=[]) {
                    //Aqui entra desde el inicio de los proveedores y desde el menu
                    global $CFG_GLPI, $DB;

                    $options['colspan']      = 4;
                    $options['formtitle']    = "Datos de la empresa";

                    $user_Id=$_SESSION['glpiID'];
                    $profile_Id=$this->getProfileByUserID($user_Id);
                    $this->initForm($ID, $options);
                    $this->showFormHeader($options);

                    
                    echo Html::hidden('id', array('value' => $this->fields['id']));

                    if($profile_Id!=9){				
                            echo Html::hidden('supplier_id', array('value' => $this->fields['supplier_id']));
                            $data=$this->getSupplierCompleteByCv($ID);
                    }else{
                            echo Html::hidden('supplier_id', array('value' => $this->getSupplierByUserID($user_Id)));
                            $supp_id=$this->getSupplierByUserID($user_Id);
                            $data=$this->getSupplierCompleteBySupplierId($supp_id);
                    }
                    echo"<table class='' style='margin-top: 0px; width:90%;'>";
                    echo "<tbody>";
                    if($this->fields['aceptacion']=='1'){
                        echo "<tr class='tab_bg_2 tab_cadre_fixehov nohover' style='background-color:#e6e6e6;'>";
                            echo "<th colspan='4' style='padding: 2px;'>";
                            echo "Si desea cancelar la inclusión de su empresa en nuestra base de datos, por favor, envíe un correo"
                            . "a la dirección <a href:'mailto=informatica@bovis.es'>informatica@bovis.es</a>";
                            echo "</th>";
                        echo "</tr>";
                    }
                    echo "<tr class='tab_bg_2 tab_cadre_fixehov nohover'>";
                        echo "<th colspan='4' style='background-color: #e6e6e6;'>".__("Información General")."</th>";
                    echo "</tr>";
                    echo "<tr>";
			echo "<td>" . __('Name') . "</td>";
			echo "<td colspan='3'>";
			Html::autocompletionTextField($data, "name", ['style' => 'width:700px;']);
			echo "</td>";
                    echo "</tr>";
                    echo "<tr>";                  
			echo "<td>" . __('CIF') . "</td>";
			echo "<td>";
			Html::autocompletionTextField($data, "cif");
			echo "</td>";                    
			echo "<td>" . __('Forma Juridica') . "</td>";	
			echo "<td>";
                        SupplierType::dropdown(['value' => $data->fields['suppliertypes_id']]);
                        echo "</td>";
                    echo "</tr>";
                    echo "<tr>";
                        echo "<td>"._n('Email', 'Emails', 1)."</td>";
                        echo "<td>";
                        Html::autocompletionTextField($data, "email");
                        echo "</td>";
			echo "<td>" . __('Teléfono') . "</td>";
			echo "<td>";
			Html::autocompletionTextField($data, "phonenumber");
                        echo "</td>";                        
                    echo "</tr>";
                    echo "<tr class='tab_bg_1'>";
                        echo "<td>".__('Fax')."</td>";
                        echo "<td>";
                        Html::autocompletionTextField($data, "fax");
                        echo "</td>";
                        echo "<td>".__('Website')."</td>";
                        echo "<td>";
                        Html::autocompletionTextField($data, "website");
                        echo "</td>";
                    echo "</tr>";
                    echo "<tr>";
                        echo "<td>".__("Dirección")."</td>";
			echo "<td colspan='3'>";
			echo "<textarea cols='113' rows='3' name='address'>".$data->fields["address"]."</textarea>";
			echo "</td>";
                    echo "</tr>";
                    echo"<tr class='tab_bg_1'>";
			echo "<td>" . __('Código Postal') . "</td>";
			echo "<td>";
			Html::autocompletionTextField($data, "postcode");
			echo "</td>";
			echo "<td>Pais</td>";
			echo "<td>";
			Html::autocompletionTextField($data, "country");
			echo "</td>";                        
                    echo"</tr>";                    
                    echo"<tr class='tab_bg_1' >";
			echo "<td>" . __('Provincia') . "</td>";
			echo "<td>";
			Html::autocompletionTextField($data, "state");
			echo "</td>";
			echo"<td>". __('City')."</td>";
			echo "<td>";
			Html::autocompletionTextField($data, "town", ['size' => 23]);

			echo "</td></tr>";



                    echo "<tr class='tab_bg_2 tab_cadre_fixehov nohover'>";
                        echo "<th colspan='4' style='background-color: #e6e6e6;'>".__("Empresa matriz(Si la tiene)")."</th>";
                    echo "</tr>";
                    echo "<tr class='tab_bg_2 tab_cadre_fixehov nohover;'></tr>";
	
                    echo "<tr class='tab_bg_1' ><td>" . __('Name') . "</td>";
			echo "<td colspan='3'>";
			Html::autocompletionTextField($this, "empresa_matriz_nombre", ['style' => 'width:700px;']);
			echo "</td>";
			echo "</tr>";

			echo "<tr class='tab_bg_1'>";
			echo "<td>" . __('Dirección') . "</td>";
			echo "<td>";
			echo "<textarea cols='37' rows='3' name='empresa_matriz_direccion'>".$this->fields["empresa_matriz_direccion"]."</textarea>";
			echo "</td>";
			echo "<td>".__('Country')."</td>";
			echo "<td>";
			Html::autocompletionTextField($this, "empresa_matriz_pais");
			echo "</td>";
			echo "</tr>";


			echo"<tr class='tab_bg_1'>";
			echo "<td>" . __('Provincia') . "</td>";
			echo "<td>";
			Html::autocompletionTextField($this, "empresa_matriz_provincia");
			echo"</td>";
			echo"<td>". __('City')."</td>";
			echo "<td>";
			Html::autocompletionTextField($this, "empresa_matriz_ciudad", ['size' => 23]);
			echo"</td></tr>";


			echo"<tr class='tab_bg_1'>";
			echo "<td>" . __('Código Postal') . "</td>";
			echo "<td>";
			Html::autocompletionTextField($this, "empresa_matriz_CP");
			echo "</td></tr>";

			//////////Lista de empresas mas destacadas///////

			echo "<tr class='tab_bg_2 tab_cadre_fixehov nohover'><th colspan='4' style='background-color: #e6e6e6;'>".__("Lista de empresas más destacadas del grupo")."</th></tr>";


			//consultamos la empresas más destacada de este CV
			$empresas_destacadas;

			if($ID!=''){
				$query2 ="SELECT * FROM glpi_plugin_comproveedores_featuredcompanies WHERE cv_id=".$ID." order by puesto asc";

				$result2 = $DB->query($query2);

				if($result2->num_rows!=0){

					$i=1;
					while ($data=$DB->fetch_array($result2)) {
						
						$empresas_destacadas['nombre_empresa_destacada'.$i]=$data['nombre_empresa_destacada'];
						$i++;
					}
				}
			}
			//Visualizamos las empresas más destacada en el caso de que existan
			for($i=1; $i<=6; $i+=2){

				echo "<tr class='tab_bg_1'>";

				echo "<td colspan='2' center>".$i.". ";

				if(!empty($empresas_destacadas['nombre_empresa_destacada'.$i])){
					Html::autocompletionTextField($this, "nombre_empresa_destacada".$i, array('option'=>'size="60"', 'value'=>$empresas_destacadas['nombre_empresa_destacada'.$i]));
				}else{
					Html::autocompletionTextField($this, "nombre_empresa_destacada".$i, array('option'=>'size="60"'));
				}
				
				echo "</td>";

				echo "<td colspan='2' center>".($i+1).". ";
				
				if(!empty($empresas_destacadas['nombre_empresa_destacada'.($i+1)])){
					Html::autocompletionTextField($this, "nombre_empresa_destacada".($i+1), array('option'=>'size="60"', 'value'=>$empresas_destacadas['nombre_empresa_destacada'.($i+1)]));
				}else{
					Html::autocompletionTextField($this, "nombre_empresa_destacada".($i+1), array('option'=>'size="60"'));
				}

				echo "</td>";

				echo"</tr>";		
			}
//////////Lista de nombres anteriores de la empresa, si hubiera cambiado en los Últimos 5 años///////

			echo "<tr class='tab_bg_2 tab_cadre_fixehov nohover'><th colspan='4' style='background-color: #e6e6e6;'>".__("Lista de nombres anteriores de la empresa, si hubiera cambiado en los Últimos 5 años")."</th></tr>";


			//consultamos los nombres anteriores de la empresas de este CV
			$nombre_anteriores;

			//fecha actual y fecha hace 5 años
			$fecha_actual=date("Y-m-d H:i:s.").gettimeofday()["usec"];
			$fecha_ultimos_cambios=date("Y-m-d H:i:s.", strtotime('-5 year')).gettimeofday()["usec"];

			if($ID!=''){

				$query2="SELECT * FROM glpi_plugin_comproveedores_previousnamescompanies WHERE cv_id=".$ID." and  fecha_cambio<=CAST('".$fecha_actual."' AS DATETIME) AND fecha_cambio>= CAST('".$fecha_ultimos_cambios."' AS DATETIME) order by fecha_cambio, id asc limit 6";

				$result2 = $DB->query($query2);

				if($result2->num_rows!=0){

					$i=1;
					while ($data=$DB->fetch_array($result2)) {
						
						$nombre_anteriores['nombre'.$i]=$data['nombre'];
						$nombre_anteriores['fecha_cambio'.$i]=$data['fecha_cambio'];
						$i++;
					}
				}
			}
			//Visualizamos los nombres anteriores de la empresas en el caso de que existan
			for($i=1; $i<=4; $i+=2){

				echo "<tr class='tab_bg_1'>";

				echo "<td colspan='2' center>".$i.". ";

				if(isset($nombre_anteriores['nombre'.$i])){

					Html::autocompletionTextField($this, "nombre".$i, array('option'=>'size="50"', 'value'=>$nombre_anteriores['nombre'.$i]));

					echo Html::hidden('fecha_cambio'.$i, array('value'=>$nombre_anteriores['fecha_cambio'.$i]));

				}else{

					Html::autocompletionTextField($this, "nombre".$i, array('option'=>'size="50"'));

					echo Html::hidden('fecha_cambio'.$i, array('value'=>date("Y-m-d H:i:s.").gettimeofday()["usec"]));

				}
				
				echo "</td>";

				echo "<td colspan='2' center>".($i+1).". ";
				
				if(isset($nombre_anteriores['nombre'.($i+1)])){

					Html::autocompletionTextField($this, "nombre".($i+1), array('option'=>'size="50"', 'value'=>$nombre_anteriores['nombre'.($i+1)]));

					echo Html::hidden('fecha_cambio'.($i+1), array('value'=>$nombre_anteriores['fecha_cambio'.($i+1)]));

				}else{

					Html::autocompletionTextField($this, "nombre".($i+1), array('option'=>'size="50"'));

					echo Html::hidden('fecha_cambio'.($i+1), array('value'=>date("Y-m-d H:i:s.").gettimeofday()["usec"]));

				}

				echo "</td>";

				echo"</tr>";		
			}
			
			///////////////////Categoías y número de empleados//////////
			echo "<tr class='tab_bg_2 tab_cadre_fixehov nohover'><th colspan='4' style='background-color: #e6e6e6;'>".__("Categoría y número de empleados")."</th></tr>";
			echo "<tr class='tab_bg_2 tab_cadre_fixehov nohover'>";

			echo "<tr class='tab_bg_1'>";
			echo "<td>Nº ";
			Html::autocompletionTextField($this, "titulacion_superior");
			echo "</td>";
			echo "<td>" . __('Titulación Superior') . "</td>";
			echo "<td>Nº ";
			Html::autocompletionTextField($this, "personal");
			echo "</td>";
			echo "<td>" . __('Personal') . "</td>";
			echo "</tr>";
			
			echo "<tr class='tab_bg_1'>";
			echo "<td>Nº ";
			Html::autocompletionTextField($this, "titulacion_grado_medio");
			echo "</td>";
			echo "<td>" . __('Titulación Grado Medio') . "</td>";
			echo "<td>Nº ";
			Html::autocompletionTextField($this, "otros_categoria_numeros_empleados");
			echo "</td>";
			echo "<td>" . __('Otros') . "</td>";
			echo "</tr>";

			echo "<tr class='tab_bg_1'>";
			echo "<td>Nº ";
			Html::autocompletionTextField($this, "tecnicos_no_universitarios");
			echo "</td>";
			echo "<td>" . __('Técnicos No Universitarios') . "</td>";
			echo "</tr>";

			/////////Principales empresas subcontratista, colaboradoras y/o profesionales que trabajan habitualmente con la empresa ///////

			echo "<tr class='tab_bg_2 tab_cadre_fixehov nohover'><th colspan='4' style='background-color: #e6e6e6;'>".__("Principales empresas subcontratistas, colaboradoras y/o profesionales que trabajan habitualmente con la empresa")."</th></tr>";

			////consultamos la principales empresas subcontratista de este CV
			$empresas_subcontratistas;

			if($ID!=''){
				$query2 ="SELECT * FROM glpi_plugin_comproveedores_subcontractingcompanies WHERE cv_id=".$ID." order by puesto asc";

				$result2 = $DB->query($query2);

				if($result2->num_rows!=0){

					$i=1;
					while ($data=$DB->fetch_array($result2)) {
						
						$empresas_subcontratistas['nombre_empresa_subcontratista'.$i]=$data['nombre_empresa_subcontratista'];
						$i++;
					}
				}
			}

			//visualizamos las empresas más destacada en el caso de que existan
			for($i=1; $i<=10; $i+=2){

				echo "<tr class='tab_bg_1'>";

				echo "<td colspan='2' center>".$i.". ";

				if(!empty($empresas_subcontratistas['nombre_empresa_subcontratista'.$i])){
					Html::autocompletionTextField($this, "nombre_empresa_subcontratista".$i, array('option'=>'size="50"', 'value'=>$empresas_subcontratistas['nombre_empresa_subcontratista'.$i]));
				}else{
					Html::autocompletionTextField($this, "nombre_empresa_subcontratista".$i, array('option'=>'size="50"'));
				}
				
				echo "</td>";

				echo "<td colspan='2' center>".($i+1).". ";
				
				if(!empty($empresas_subcontratistas['nombre_empresa_subcontratista'.($i+1)])){
					Html::autocompletionTextField($this, "nombre_empresa_subcontratista".($i+1), array('option'=>'size="50"', 'value'=>$empresas_subcontratistas['nombre_empresa_subcontratista'.($i+1)]));
				}else{
					Html::autocompletionTextField($this, "nombre_empresa_subcontratista".($i+1), array('option'=>'size="50"'));
				}

				echo "</td>";

				echo"</tr>";		
			}
			//////////


			echo "<tr class='tab_bg_2 tab_cadre_fixehov nohover'><th colspan='4' style='background-color: #e6e6e6;'>".__("Comentarios")."</th></tr>";

			echo "<tr>";
                            echo "<td>".__('Comments')."</td>";
                            echo "<td colspan='3'>";
                            echo "<textarea cols='110' rows='5' name='comment' >".$this->fields["comment"]."</textarea>";
                            echo "</td>";
                        echo "</tr>";

			echo "<tr class='tab_bg_2 tab_cadre_fixehov nohover'><th colspan='4' style='background-color: #e6e6e6;'>".__("HISTORICO")."</th></tr>";
			echo "<tr>";
                            echo "<td>Fecha de creación: </td>";
                            echo "<td> {$this->fields["fecha_alta"]}</td>";
                            $UsuarioAlta = $this->fields["usuario_alta"];
                            echo "<td colspan='2'>{$UsuarioAlta} </td>";
                        echo "</tr>";
                        echo "<tr>";
                            echo "<td>";
                            echo "Fecha de Última modificación:";
                            echo "</td>";                        
                            echo "<td>";
                            echo $this->fields["fecha_ultima_modificacion"];
                            echo "</td>";           
                            //User::getNameId($item->fields["usuario_ultima_modficacion"]);                        
                            echo "<td colspan='2'>{$this->fields["usuario_ultima_modificacion"]}</td>";
                        echo "</tr>";                        
			echo "</tbody></table>";
			$this->showFormButtons($options);
                        if($this->fields["aceptacion"]=='0'){
                            echo "<script type='text/javascript'>
                                    var resp = confirm('<p style=\"width: 400px; text-align: justify;\">En cumplimiento de la normativa vigente en materia de protección de datos le informamos que el responsable de sus datos personales es BOVIS PROJECT MANAGEMENT SA, y los utilizará para gestionar su base de datos de contratistas. Sus datos no serán cedidos a terceros, salvo por obligaciones legales. Conservaremos los datos mientras no manifieste su derecho de supresión. Darse de alta como contratista de BOVIS PROJECT MANAGEMENT SA, implica la aceptación de estos términos de privacidad. Puede ejercer sus derechos sobre protección de datos a través de info@bovis.es. Puede obtener más información sobre protección de datos en nuestro Aviso Legal.</p>', 'Consentimiento expreso');
                                    if(resp){
                                            var parametros = {cvid: {$this->fields['id']},
                                            aceptacion: 1,
                                            usuarioid: {$user_Id}};
                                            var strURL = '".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/consentimientoExpreso.php';
                                            $.ajax({
                                                url: strURL,
                                                data: parametros,
                                                async: false,
                                                globl: false,
                                                type: 'GET',
                                                cache: false,
                                                error: function () { 
                                                    alert('Error en la aceptación');
                                                }
                                            });   
                                    }else{
                                            var parametros = {cvid: {$this->fields['id']},
                                            aceptacion: 0,
                                            usuarioid: {$user_Id}};
                                            var strURL = '".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/consentimientoExpreso.php';
                                            $.ajax({
                                                url: strURL,
                                                data: parametros,
                                                async: false,
                                                globl: false,
                                                type: 'GET',
                                                cache: false,
                                                error: function () { 
                                                    alert('Error en la no aceptación');
                                                }
                                            });                                          
                                    }
                            </script>";
                        }
                        echo "<script type='text/javascript'>
                            $('.icons_block').css('display', 'none');
                        </script>";
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

	}