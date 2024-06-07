<?php

/******************************************

	PLUGIN DE GESTION DE CURRICULUMS DE LOS PROVEEDORES


 ******************************************/

	class PluginComproveedoresExperience extends CommonDBTM{

		static $rightname	= "plugin_comproveedores";

		static function getTypeName($nb=0){
			return _n('Expeciencias','Expeciencias',1,'comproveedores');
		}

		function getTabNameForItem(CommonGLPI $item, $tabnum=1,$withtemplate=0){
			if($item-> getType()=="Supplier"){
				return self::createTabEntry('Experiencias');
			}
			return 'Experiencias';
		}


		static function displayTabContentForItem(CommonGLPI $item,$tabnum=1,$withtemplate=0){

			global $CFG_GLPI;
			$self = new self();

			if($item->getType()=='Supplier'){	

				if(isset($item->fields['cv_id'])){
			
					$self->showFormItemExperience($item, $withtemplate);

				}else{
				
					$self->showFormNoCV($item, $withtemplate);
				}
				
			}else if($item->getType()=='PluginComproveedoresCv'){
				$self->showFormItem($item, $withtemplate);
			}


		}

		function getSearchOptions(){

			$tab = array();

			$tab['common'] = ('Experiencias');

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

		function showFormItem($item, $withtemplate='') {	
                    $this->formularioCV($item, $withtemplate);
		}

		function showFormItemExperience($item, $withtemplate='') {	
                    $this->formularioCV($item, $withtemplate);
                }

		function showFormNoCV($ID, $options=[]) {
			//Aqui entra cuando no tien gestionado el curriculum

			echo "<div>Necesitas gestionar el CV antes de añadir expeciencias</div>";
			echo "<br>";
		}

		function showForm($ID, $options=[]) {
			//Aqui entra desde el inicio de los proveedores

			global $CFG_GLPI;
			$this->initForm($ID, $options);
			$this->showFormHeader($options);

			$opt2['comments']= false;
			$opt2['addicon']= false;
			$opt2['value']=  $this->fields["plugin_comproveedores_communities_id"];

			$opt3['comments']= false;
			$opt3['addicon']= false;
			$opt3['value']=  $this->fields["plugin_comproveedores_experienciesstates_id"];
                        
			$opt['comments']= false;
			$opt['addicon']= false;
			$opt['value']=  $this->fields["plugin_comproveedores_experiencestypes_id"];
			
			echo"<tr class='tab_bg_1 center'>";
			echo "<td>" . __('Estado') . "</td>";
			echo "<td>";
			Dropdown::show('PluginComproveedoresExperiencesstate', $opt3);
			echo "</td>";
			echo "</tr>";
			echo"<tr class='tab_bg_1 center'>";
			echo "<td>" . __('Intervención de BOVIS') . "</td>";
			echo "<td id='intervencionBovis'>";
			Dropdown::showYesNo('intervencion_bovis');
			echo "</td>";
			echo "<td class='tipos_experiencias'>" . __('Tipos de experiencias') . "</td>";
			echo "<td class='tipos_experiencias'>";
			Dropdown::show('PluginComproveedoresExperiencestype', $opt);
			echo "</td>";
			echo"</tr>";
			echo"<tr class='tab_bg_1 center'>";
			echo "<td>" . __('Nombre proyecto') . "</td>";
			echo "<td>";
			Html::autocompletionTextField($this, "name");
			echo "</td>";
			echo "<td>" . __('Comunidad Autonoma') . "</td>";
			echo "<td>";
			
			Dropdown::show('PluginComproveedoresCommunity',$opt2);

			echo "</td>";
			echo"</tr>";

			echo"<tr class='tab_bg_1 center'>";
			echo "<td>" . __('Cliente') . "</td>";
			echo "<td>";
			Html::autocompletionTextField($this, "cliente");
			echo "</td>";
			echo "<td>" . __('Año') . "</td>";
			echo "<td>";

			$anio = date("Y", strtotime($this->fields["anio"]));
			$anio++;
			Dropdown::showFromArray('anio', $this->getYears(),array('value'=>$anio));
			echo "</td>";
			echo"</tr>";

			echo"<tr class='tab_bg_1 center'>";
			echo "<td>" . __('Importe contratado') . "</td>";
			echo "<td>";
			$importe=number_format($this->fields["importe"], 2, ',', '.');
			Html::autocompletionTextField($this, "importe",array('value'=>$importe));
			echo "</td>";
			echo "<td>" . __('Duración de su contratado') . "</td>";
			echo "<td>";
			Html::autocompletionTextField($this, "duracion");
			echo "</td>";
			echo"</tr>";

			echo"<tr class='tab_bg_1 center'>";
			echo "<td>" . __('BIM') . "</td>";
			echo "<td>";
			Dropdown::showFromArray('bim', array(-1 =>'------', 1=>'Sí' , 0 =>'No'),array('value'=>$this->fields['bim']));
			echo "</td>";
			echo "<td>" . __('Breeam') . "</td>";
			echo "<td>";
			Dropdown::showFromArray('breeam', array(-1 =>'------', 1=>'Sí' , 0 =>'No'),array('value'=>$this->fields['breeam']));
			echo "</td>";
			echo"</tr>";

			echo"<tr class='tab_bg_1 center'>";
			echo "<td>" . __('Leed') . "</td>";
			echo "<td>";
			Dropdown::showFromArray('leed', array(-1 =>'------', 1=>'Sí' , 0 =>'No'),array('value'=>$this->fields['leed']));
			echo "</td>";
			echo "<td>" . __('Otros certificados') . "</td>";
			echo "<td>";
			Dropdown::showFromArray('otros_certificados', array(-1 =>'------', 1=>'Sí' , 0 =>'No'),array('value'=>$this->fields['otros_certificados']));
			echo "</td>";
			echo"</tr>";

			echo"<tr class='tab_bg_1 center'>";
			echo "<td>" . __('Cpd Tier') . "</td>";
			echo "<td>";
			Dropdown::showFromArray('cpd_tier', array(-1 =>'------', 1=>'Sí' , 0 =>'No'),array('value'=>$this->fields['cpd_tier']));
			echo "</td>";
			echo "<td>" . __('Observaciones') . "</td>";
			echo "<td>";
			Html::autocompletionTextField($this, "observaciones");
			echo "</td>";
			echo "</tr>";

			$this->showFormButtons($options);

			
		}


		function getYears(){
			$year = date("Y");
			for ($i= $year; $i >=  1985; $i--) {
                            $lista[$i]=$i;
			}
			return $lista;
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
                
                function formularioCV($item, $withtemplate='') {
                    GLOBAL $DB,$CFG_GLPI;

                    $CvId = $item->fields['cv_id']; 
                    $Experiencia_Id=0;
                    
                    $optUbicacion['comments']= false;
                    $optUbicacion['addicon']= false;
                    $optUbicacion['value']=  $this->fields["plugin_comproveedores_communities_id"];

                    $optEstado['comments']= false;
                    $optEstado['addicon']= false;
                    $optEstado['value']=  $this->fields["plugin_comproveedores_experienciesstates_id"];

                    $opt['comments']= false;
                    $opt['addicon']= false;
                    $opt['visible']= true;
                    $opt['value']=  $this->fields["plugin_comproveedores_experiencestypes_id"];                      


                    //DATOS DEL PERFIL Y USUARIO
                    $self                = new self();
                    $user_Id             = $_SESSION['glpiID'];
                    $profile_Id          = $self->getProfileByUserID($user_Id);
                    $ver                 = true;
                    if(in_array($profile_Id, array(3,4,16))){    
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

                    echo "<div style='overflow-y: auto; height: 450px; font-size:10px; border-radius: 4px; padding: 8px; background-color: #e9ecf3;'>";
                    echo $this->consultaAjax();

                    //echo "<p style='vertical-align:middle; font-size: 15px; font-weight:bold; margin: 10px 0px'> <img id='nuevaExperiencia' style='cursor: pointer; vertical-align:middle; margin: 10px 0px;' src='../pics/meta_plus.png'> Añadir y modificar experiencia</p>";

                    //echo"<form id='formulario' style='display:none' action=".$CFG_GLPI["root_doc"]."/plugins/comproveedores/front/experience.form.php method='post'>";		
                    //echo Html::hidden('cv_id', array('value' => $CvId)); 
                    echo Html::hidden('_glpi_csrf_token', array('value' => Session::getNewCSRFToken()));


                    echo "<div class='center' id='actualizarFormulario' style='width: 80%; margin-left:10%;'>";
                    echo "<input type='hidden' id='idPerfil' name='idPerfil' value='{$profile_Id}' >";
                    echo "<input type='hidden' id='idExperiencia' name='idExperiencia' value='0' >";
                    echo "<input type='hidden' id='cv_id' name='cv_id' value='{$CvId}' >";
                    echo "<table class='tab_cadre_fixe' style='width: 100%; margin-top: 4px; padding: 5px;border-radius:4px; background-color:#f8f7f3; -webkit-box-shadow: 12px 8px 5px -5px rgba(219,219,219,1);-moz-box-shadow: 12px 8px 5px -5px rgba(219,219,219,1);box-shadow: 12px 8px 5px -5px rgba(219,219,219,1);'>";
                    echo "<tbody>";
                    echo "<tr>";
                    //echo Html::hidden('idExperiencia');

                    echo "<tr>";
                        echo "<td style='vertical-align: bottom;'>Proyecto(*)</td>";
                        echo "<td style='vertical-align: bottom;'>" . __('Estado(*)') . "</td>";
                        echo "<td style='vertical-align: bottom;'>" . __('Tipología(*)') . "</td>";
                        echo "<td style='vertical-align: bottom;'>" . __('Duración (meses)') . "</td>";
                        echo "<td style='vertical-align: bottom;'>" . __('Experiencia con BOVIS') . "</td>";
                        echo "<td style='vertical-align: bottom;'>" . __('BIM') . "</td>";
                        echo "<td style='vertical-align: bottom;'>" . __('Breeam') . "</td>";
                        echo "<td style='vertical-align: bottom;'>" . __('Leed') . "</td>";
                        echo "<td style='vertical-align: bottom;'>" . __('Otros') . "</td>";
                    echo "</tr>";

                    echo"<tr>";
                        echo "<td id='nombreExperiencia' style='vertical-align: top;'>";
                        echo "<textarea style='padding:7px; resize: none; display:block; margin-left:auto; margin-right:auto;' cols='60' rows='3' name='name'></textarea>";
                        echo "</td>";
                        echo "<td style='vertical-align: top;'>";
                            Dropdown::show('PluginComproveedoresExperiencesstate', $optEstado);
                        echo "</td>";
                        echo "<td id='tipos_experiencias'  style='vertical-align: top;'>";
                            Dropdown::show('PluginComproveedoresExperiencestype', $opt);
                        echo "<td  style='vertical-align: top;'>";
                        //Html::autocompletionTextField($this, "duracion", ['option' =>'style="width: 100px;"']);
                        echo "<input type='text' id='duracion' name='duracion' value='' style='width: 100px;' />";
                        echo "</td>";
                        echo "<td id='intervencionBovis'  style='vertical-align: top;'>";
                        echo "<input type='checkbox' name='experiencia_bovis' value='1' style='width: 20px; cursor: pointer;'>";
                        echo "</td>";
                        echo "<td  style='vertical-align: top;'>";
                        echo "<input type='checkbox' name='bim' value='1' style='width: 20px; cursor: pointer;'>";
                        echo "</td>";
                        echo "<td  style='vertical-align: top;'>";
                        echo "<input type='checkbox' name='breeam' value='1' style='width: 20px; cursor: pointer;'>";
                        echo "</td>";
                        echo "<td  style='vertical-align: top;'>";
                        echo "<input type='checkbox' name='leed' value='1' style='width: 20px; cursor: pointer;'>";
                        echo "</td>";
                        echo "<td  style='vertical-align: top;'>";
                        echo "<input type='checkbox' name='otros_certificados' value='1' style='width: 20px; cursor: pointer;'>";
                        echo "</td>";
                    echo "</tr>";

                    echo"<tr>";
                        echo "<td style='vertical-align: bottom;'>" . __('Cliente') . "</td>";
                        echo "<td style='vertical-align: bottom;'>" . __('Año') . "</td>";
                        echo "<td style='vertical-align: bottom;'>" . __('Ubicación') . "</td>";
                        echo "<td style='vertical-align: bottom;'>" . __('Importe (miles €)') . "</td>";
                            echo Html::hidden("<td>" . __('Cpd Tier') . "</td>");
                        echo "<td colspan='5' style='vertical-align: bottom;'>" . __('Observaciones') . "</td>";
                    echo "</tr>";

                    echo"<tr>";
                        echo Html::hidden("<td>");
                        echo Html::hidden("<input type='checkbox' name='cpd_tier' value='1' style='text-align-last: center'>");
                        echo Html::hidden("</td>");
                        echo "<td  style='vertical-align: top;'>";
                            echo "<textarea style='padding:7px; resize: none;' cols='60' rows='3' name='cliente'></textarea>";
                        echo "</td>";
                        echo "<td  style='vertical-align: top;'>";
                            Html::autocompletionTextField($this, "anio", ['option' =>'style="width: 80px;"']);
                        echo "</td>";
                        echo "<td  style='vertical-align: top;'>";
                            Dropdown::show('PluginComproveedoresCommunity', $optUbicacion);
                        echo "</td>";
                        echo "<td id='importeExperiencia'  style='vertical-align: top;'>";
                            Html::autocompletionTextField($this, "importe", ['option' =>'style="width: 100px;"']);
                        echo "</td>";
                        echo "<td colspan='5'  style='vertical-align: top;'>";
                        echo "<textarea style='padding:7px; width:95%; resize: none;' cols='20' rows='3' name='observaciones'></textarea>";
                        echo "</td>";
                    echo"</tr>";
                    echo"</tbody>";
                    echo"</table>";


                    echo "<div style='margin-bottom: 15px; margin-top: 15px;'>";
                        echo "<input id='add'    title='añadir nueva experiencia' type='submit' class='boton_add'      name='add'      style='margin-right: 15px;' value='' >";
                        echo "<input id='addADD' title='añadir nueva experiencia sin borrar' type='submit' class='boton_addADD'      name='addADD'      style='margin-right: 15px;' value='' >";
                        echo "<input id='update' title='grabar' type='submit' class='boton_grabar'   name='update'   style='margin-right: 15px;' value='' >";
                        echo "<input id='cancel' title='cancelar' type='submit' class='boton_cancelar' name='cancelar' style='margin-right: 15px;' value=''>";
                    echo "</div>";


                    echo "</div>";

                    //echo"</form>";

//                        if($profile_Id==14){
//                            echo "<script type='text/javascript'>"
//                            . " $('#actualizarFormulario').css('display', 'none');"
//                            . "</script>";
//                        }
                    /*///////////////////////////////
                    //LISTAR EXPERIENCIA DEL PROVEEDOR
                    ///////////////////////////////*/

                    echo"<div id='cargar_listas'></div>";

                echo "</div>";	                    
                }


    function consultaAjax(){

        GLOBAL $CFG_GLPI;

        $consulta =
        "<script type='text/javascript'>

            function gestionBotones(){
                if($('#idExperiencia').val()=='0'){
                    $('#add').css('display','inline');
                    $('#addADD').css('display','inline');
                    $('#update').css('display','none');
                }else{
                    $('#add').css('display', 'none');
                    $('#addADD').css('display','none');
                    $('#update').css('display', 'inline');
                }                                
            }

            function acordeonExperiencia(parametros){
                $('#cargar_listas').html('');
                $.ajax({  
                    type: 'GET',  
                    async: false, 
                    data: {'cv_id' : $('input[name=cv_id]').val()},             
                    url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/accordionExperience.php',                     
                    success:function(data){
                        $('#cargar_listas').html(data);        					
                    },
                    error: function(result) {
                            alert('Data not found');
                    }
                });

                //Abrimos la lista que se ha añadido o modificado
                if(parametros!=null){
                    //Abrimos la lista que se a modificado
                    if(parametros['experiencia_bovis']==1){
                        $('h3[name=intervencion_bovis]').click();
                    }
                    if(parametros['experiencia_bovis']==0 && parametros['plugin_comproveedores_experiencestypes_id']=='0'){
                        $('h3[name=sin_experiencia]').click();
                    }
                    if(parametros['experiencia_bovis']==0 && parametros['plugin_comproveedores_experiencestypes_id']!='0'){
                        $('h3[name='+parametros['plugin_comproveedores_experiencestypes_id']+']').click();
                    }
                }
            } //acordeonExperiencia
            

            function actualizarLista(tipo){
                var nombre_tabla = 'tipo_experiencia_'+tipo;
                var perfil = $('#idPerfil').val();
                $.ajax({ 
                    async: false, 
                    type: 'GET',
                    data: {'cv_id': $('input[name=cv_id]').val(), 'tipo': tipo, 'profile_id':  perfil},                  
                    url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/listExperience.php',                    
                    success:function(data){
                            $('div[class*='+nombre_tabla+']').html(data);
                    },
                    error: function(result) {
                            alert('Data not found');
                    }
                });
            }
            
            function limpiarFormulario(){
            
                $('textarea[name=name]').val(''); 
                $('textarea[name=cliente]').val('');
                $('input[name=idExperiencia]').val(0);
                $('input[name=importe]').val('');
                $('input[name=anio]').val('');
                $('input[name=plugin_comproveedores_experiencesstates_id]').val(0);
                $('input[name=plugin_comproveedores_experiencesstates_id]').change();      
                $('input[name=plugin_comproveedores_communities_id]').val(0);
                $('input[name=plugin_comproveedores_communities_id]').change();                
                $('input[name=plugin_comproveedores_experiencestypes_id]').val(0);
                $('input[name=plugin_comproveedores_experiencestypes_id]').change();     
                $('input[name=duracion]').val('');
                $('input[name=experiencia_bovis]').prop('checked', false);
                $('input[name=bim]').prop('checked', false);
                $('input[name=breeam]').prop('checked', false);
                $('input[name=leed]').prop('checked', false);
                $('input[name=otros_certificados]').prop('checked', false);
                $('textarea[name=observaciones]').val('');
            }

            function modificar(idExperiencia){
                var dat = '';
                var aux = '';

                $('#idExperiencia').val(idExperiencia);
                aux = '#name_'+idExperiencia;
                dat = $(aux).text().trim();
                $('textarea[name=name]').val(dat);  

                aux = '#cliente_'+idExperiencia;
                dat = $(aux).text().trim();
                $('textarea[name=cliente]').val(dat);                                      

                $('input[name=idExperiencia]').val(idExperiencia);
                aux = '#anio_'+idExperiencia;
                dat = $(aux).text().trim();
                $('input[name=anio]').val(dat);

                aux = '#importe_'+idExperiencia;
                dat = $(aux).text().trim();
                $('input[name=importe]').val(dat);                                    

                aux = '#idestado_'+idExperiencia;
                dat = $(aux).val();
                $('input[name=plugin_comproveedores_experiencesstates_id]').val(dat);
                $('input[name=plugin_comproveedores_experiencesstates_id]').change();

                aux = '#idcommunity_'+idExperiencia;
                dat = $(aux).val();
                $('input[name=plugin_comproveedores_communities_id]').val(dat);
                $('input[name=plugin_comproveedores_communities_id]').change();

                aux = '#idtype_'+idExperiencia;
                dat = $(aux).val();
                $('input[name=plugin_comproveedores_experiencestypes_id]').val(dat);
                $('input[name=plugin_comproveedores_experiencestypes_id]').change();

                aux = '#duracion_'+idExperiencia;
                dat = $(aux).text().trim();
                $('input[name=duracion]').val(dat);

                aux = '#checkbox_bovis_'+idExperiencia;
                $('input[name=experiencia_bovis]').prop('checked', $(aux).prop('checked'));

                aux = '#checkbox_bim_'+idExperiencia;
                $('input[name=bim]').prop('checked', $(aux).prop('checked'));

                aux = '#checkbox_breeam_'+idExperiencia;
                $('input[name=breeam]').prop('checked', $(aux).prop('checked'));

                aux = '#checkbox_leed_'+idExperiencia;
                $('input[name=leed]').prop('checked', $(aux).prop('checked'));

                aux = '#checkbox_otros_certificados_'+idExperiencia;
                $('input[name=otros_certificados]').prop('checked', $(aux).prop('checked'));

                aux = '#observaciones_'+idExperiencia;
                dat = $(aux).val();
                $('textarea[name=observaciones]').val(dat);

                gestionBotones();
            }

            //Limpia el formulario
            $('#cancel').on('click', function(){
                limpiarFormulario();
            }); 

            function anadirADD(){
                var experiencia_bovis = 0;
                if($('input[name=experiencia_bovis]').prop('checked')) {	
                    experiencia_bovis = 1;
                }
                var bim = 0;
                if($('input[name=bim]').prop('checked')) {	
                    bim = 1;
                }
                var breeam = 0;
                if($('input[name=breeam]').prop('checked')) {		
                    breeam = 1;
                }
                var leed = 0;
                if($('input[name=leed]').prop('checked')) {
                    leed = 1;
                }
                var otros_certificados = 0;
                if($('input[name=otros_certificados]').prop('checked')) {	
                    otros_certificados = 1;
                }
                var anio = 0;
                if($('input[name=anio]').val()!=''){
                    anio = $('input[name=anio]').val();
                }
                var duracion = 0;
                if($('input[name=duracion]').val()!=''){
                    duracion = $('input[name=duracion]').val();
                }
                var importe = 0;
                if($('input[name=importe]').val()!=''){
                    importe = $('input[name=importe]').val().replace('.','').replace(',','.');
                }

                var plugin_comproveedores_experiencesstates_id = $('input[name=plugin_comproveedores_experiencesstates_id]').val();
                var plugin_comproveedores_experiencestypes_id = $('input[name=plugin_comproveedores_experiencestypes_id]').val();
                var plugin_comproveedores_communities_id = $('input[name=plugin_comproveedores_communities_id]').val();
                var name = $('textarea[name=name]').val();
                var cliente = $('textarea[name=cliente]').val();
                var observaciones = $('textarea[name=observaciones]').val();
                var cv_id = $('input[name=cv_id]').val();
                var id = $('#idExperiencia').val();

                var parametros = {
                    'id'                                        : id,
                    'cv_id'                                     : cv_id,
                    'plugin_comproveedores_experiencesstates_id': plugin_comproveedores_experiencesstates_id,
                    'experiencia_bovis'                         : experiencia_bovis,
                    'plugin_comproveedores_experiencestypes_id' : plugin_comproveedores_experiencestypes_id,
                    'plugin_comproveedores_communities_id'      : plugin_comproveedores_communities_id,
                    'name'                                      : name,
                    'cliente'                                   : cliente,
                    'anio'                                      : anio,
                    'importe'                                   : importe,
                    'duracion'                                  : duracion,
                    'bim'                                       : bim,
                    'breeam'                                    : breeam,
                    'leed'                                      : leed,
                    'otros_certificados'                        : otros_certificados,
                    'observaciones'                             : observaciones
                };

                $.ajax({  
                    type: 'GET',  
                    async: false,               
                    url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/insertExperience.php',                    
                    data: parametros, 
                    success:function(data){ },
                    error: function(result) {
                        alert('Data not found');
                    }
                });

                //refrescamos el acordeon con las listas de experiencias
                acordeonExperiencia(parametros);

            } //anadirADD

            //Verifica que todo está correcto antes de llamar al botón añadir
            $('#add').on('click', function(){
                var estadoid = 0;
                if($('input[name=plugin_comproveedores_experiencesstates_id]').val()!=''){
                    estadoid = $('input[name=plugin_comproveedores_experiencesstates_id]').val();
                }
                var tipoid = 0;
                if($('input[name=plugin_comproveedores_experiencestypes_id]').val()!=''){
                    tipoid = $('input[name=plugin_comproveedores_experiencestypes_id]').val();
                }
                var nombre = $('textarea[name=name]').val();

                if(estadoid>0 && nombre!='' && tipoid>0){
                    anadir();
                }else{
                    alert('Debe completar los campos obligatorios');
                }
            });

            //Verifica que todo está correco y añade una y otra vez.
            $('#addADD').on('click', function(){
                var estadoid = 0;
                if($('input[name=plugin_comproveedores_experiencesstates_id]').val()!=''){
                    estadoid = $('input[name=plugin_comproveedores_experiencesstates_id]').val();
                }
                var tipoid = 0;
                if($('input[name=plugin_comproveedores_experiencestypes_id]').val()!=''){
                    tipoid = $('input[name=plugin_comproveedores_experiencestypes_id]').val();
                }
                var nombre = $('textarea[name=name]').val();

                if(estadoid>0 && nombre!='' && tipoid>0){
                    anadirADD();
                }else{
                    alert('Debe completar los campos obligatorios');
                }
            });

            function anadir(){
                var experiencia_bovis = 0;
                if($('input[name=experiencia_bovis]').prop('checked')) {	
                    experiencia_bovis = 1;
                }
                var bim = 0;
                if($('input[name=bim]').prop('checked')) {	
                    bim = 1;
                }
                var breeam = 0;
                if($('input[name=breeam]').prop('checked')) {		
                    breeam = 1;
                }
                var leed = 0;
                if($('input[name=leed]').prop('checked')) {
                    leed = 1;
                }
                var otros_certificados = 0;
                if($('input[name=otros_certificados]').prop('checked')) {	
                    otros_certificados = 1;
                }
                var anio = 0;
                if($('input[name=anio]').val()!=''){
                    anio = $('input[name=anio]').val();
                }
                var duracion = 0;
                if($('#duracion').val()!=''){
                    duracion = $('#duracion').val();
                }
                var importe = 0;
                if($('input[name=importe]').val()!=''){
                    importe = $('input[name=importe]').val().replace('.','').replace(',','.').replace('€', '');
                }

                var plugin_comproveedores_experiencesstates_id = $('input[name=plugin_comproveedores_experiencesstates_id]').val();
                var plugin_comproveedores_experiencestypes_id = $('input[name=plugin_comproveedores_experiencestypes_id]').val();
                var plugin_comproveedores_communities_id = $('input[name=plugin_comproveedores_communities_id]').val();
                var name = $('textarea[name=name]').val();
                var cliente = $('textarea[name=cliente]').val();
                var observaciones = $('textarea[name=observaciones]').val();
                var cv_id = $('input[name=cv_id]').val();
                var id = $('#idExperiencia').val();

                var parametros = {
                    'id'                                        : id,
                    'cv_id'                                     : cv_id,
                    'plugin_comproveedores_experiencesstates_id': plugin_comproveedores_experiencesstates_id,
                    'experiencia_bovis'                         : experiencia_bovis,
                    'plugin_comproveedores_experiencestypes_id' : plugin_comproveedores_experiencestypes_id,
                    'plugin_comproveedores_communities_id'      : plugin_comproveedores_communities_id,
                    'name'                                      : name,
                    'cliente'                                   : cliente,
                    'anio'                                      : anio,
                    'importe'                                   : importe,
                    'duracion'                                  : duracion,
                    'bim'                                       : bim,
                    'breeam'                                    : breeam,
                    'leed'                                      : leed,
                    'otros_certificados'                        : otros_certificados,
                    'observaciones'                             : observaciones
                };

                $.ajax({  
                    type: 'GET',  
                    async: false,               
                    url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/insertExperience.php',                    
                    data: parametros, 
                    success:function(data){ },
                    error: function(result) {
                        alert('Data not found');
                    }
                });

                //refrescamos el acordeon con las listas de experiencias
                acordeonExperiencia(parametros);
                limpiarFormulario();

            } //anadir

            function guardarModificacion(){
                var experiencia_bovis = 0;
                if($('input[name=experiencia_bovis]').prop('checked')) {	
                    experiencia_bovis = 1;
                }
                var bim = 0;
                if($('input[name=bim]').prop('checked')) {	
                    bim = 1;
                }
                var breeam = 0;
                if($('input[name=breeam]').prop('checked')) {		
                    breeam = 1;
                }
                var leed = 0;
                if($('input[name=leed]').prop('checked')) {
                    leed = 1;
                }
                var otros_certificados = 0;
                if($('input[name=otros_certificados]').prop('checked')) {	
                    otros_certificados = 1;
                }
                var anio = 0;
                if($('input[name=anio]').val()!=''){
                    anio = $('input[name=anio]').val();
                }
                var duracion = 0;
                if($('input[name=duracion]').val()!=''){
                    duracion = $('input[name=duracion]').val();
                }
                var importe = 0;
                if($('input[name=importe]').val()!=''){
                    importe = $('input[name=importe]').val().replace('.','').replace(',','.');
                }                                    
                var plugin_comproveedores_experiencesstates_id = $('input[name=plugin_comproveedores_experiencesstates_id]').val();
                var plugin_comproveedores_experiencestypes_id = $('input[name=plugin_comproveedores_experiencestypes_id]').val();
                var plugin_comproveedores_communities_id = $('input[name=plugin_comproveedores_communities_id]').val();
                var name = $('textarea[name=name]').val();
                var cliente = $('textarea[name=cliente]').val();
                var observaciones = $('textarea[name=observaciones]').val();
                var cv_id = $('input[name=cv_id]').val();
                var id = $('#idExperiencia').val();

                var parametros = {
                    'id'                                        : id,
                    'cv_id'                                     : cv_id,
                    'plugin_comproveedores_experiencesstates_id': plugin_comproveedores_experiencesstates_id,
                    'experiencia_bovis'                         : experiencia_bovis,
                    'plugin_comproveedores_experiencestypes_id' : plugin_comproveedores_experiencestypes_id,
                    'plugin_comproveedores_communities_id'      : plugin_comproveedores_communities_id,
                    'name'                                      : name,
                    'cliente'                                   : cliente,
                    'anio'                                      : anio,
                    'importe'                                   : importe,
                    'duracion'                                  : duracion,
                    'bim'                                       : bim,
                    'breeam'                                    : breeam,
                    'leed'                                      : leed,
                    'otros_certificados'                        : otros_certificados,
                    'observaciones'                             : observaciones
                };

                $.ajax({  
                        type: 'GET',  
                        async: false,                
                        url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/updateExperience.php',                    
                        data: parametros, 
                        success:function(data){},
                        error: function(result) {
                            alert('Data not found');
                        }
                });

                //refrescamos el acordeon con las listas de experiencias
                acordeonExperiencia(parametros);	

            } //update               


            $(document).ready(function() {
                //ocultamos el botón guardar modificación
                $('#guardar_modificacion').hide();

                //Añadimos el acordeon con las listas de experiencias
                acordeonExperiencia(null);
                gestionBotones();
            });
            
        </script>";

        return $consulta;
    }

    
}