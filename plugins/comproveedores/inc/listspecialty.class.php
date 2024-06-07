
<?php
class PluginComproveedoresListspecialty extends CommonDBTM{

	static $rightname	= "plugin_comproveedores";

	static function getTypeName($nb=0){
		return _n('Especialidades','Especialidades',1,'comproveedores');
	}

	function getTabNameForItem(CommonGLPI $item, $tabnum=1,$withtemplate=0){
		if($item->getType()=="Supplier"){
			return self::createTabEntry('Especialidades');
		}
		return 'Especialidades';
	}

	static function displayTabContentForItem(CommonGLPI $item,$tabnum=1,$withtemplate=0){
		global $CFG_GLPI;
		$self = new self();

		if($item->getType()=='Supplier'){
                    //Si entramos desde la parte del proveedor (como técnico de BOVIS)
                    if(isset($item->fields['cv_id'])){
                        $self->showFormItemSpecialty($item, $withtemplate);
                    }else{
                        $self->showFormNoCV($item, $withtemplate);
                    }
		}else if($item->getType()=='PluginComproveedoresCv'){
                    //Si entramos desde la parte de la gestión del CV
                    $self->showFormItem($item, $withtemplate);
		}
	}

	function getSearchOptions(){

		$tab = array();
		$tab['common'] = ('Especialidades');
		$tab[1]['table']	='glpi_plugin_comproveedores_roltypes';
		$tab[1]['field']	='name';
		$tab[1]['name']		=__('name');
		$tab[1]['datatype']		='text';

		return $tab;
	}

	function registerType($type){
		if(!in_array($type, self::$types)){
			self::$types[]= $type;
		}		
	}

	function showFormNoCV($ID, $options=[]) {
			//Aqui entra cuando no tien gestionado el curriculum

		echo "<div>Necesitas gestionar el CV antes de añadir especialidades</div>";
		echo "<br>";
	}

	function showForm($ID, $options=[]) {
			//Aqui entra desde el inicio de los proveedores
		
	}


        /* técnico BOVIS */
	function showFormItemSpecialty($item, $withtemplate='') {
	GLOBAL $DB,$CFG_GLPI;

            $opt['specific_tags']=array('onchange' => 'cambiarCategorias(value)');
            $opt['comments']= false;
            $opt['addicon']= false;

            if($item->fields['cv_id']==0 || isset($item->fields['cv_id']) == false){
                $CvId = -1;
            }else{
                $CvId=$item->fields['cv_id'];     
            }
            echo "<div style='float:left; width:98%; border-radius: 4px; padding: 8px; background-color: #e9ecf3; height: 500px; margin-bottom: 10px;'>";
            echo $this->consultaAjax();

            $self = new self();            
            $user_Id             = $_SESSION['glpiID'];
            $profile_Id          = $self->getProfileByUserID($user_Id);
            $ver                 = true;
            if(in_array($profile_Id, array(3,4,16))){    
                $ver = true;
                echo "<input id='verBotonesEspecialidades' type='hidden' value='1' />";
            }else{
                $ver = false;
                echo "<input id='verBotonesEspecialidades' type='hidden' value='0' />";
            }              
            
            echo "<input id='cv_id' type='hidden' value='{$CvId}' />";
            echo Html::hidden('_glpi_csrf_token', array('value' => Session::getNewCSRFToken()));
            echo "<table id='tblAux' class='tab_cadre_fixe' style='margin-top:0px; background-color: #f8f7f3; border-radius: 4px; -webkit-box-shadow: 12px 8px 5px -5px rgba(219,219,219,1);    -moz-box-shadow: 12px 8px 5px -5px rgba(219,219,219,1);    box-shadow: 12px 8px 5px -5px rgba(219,219,219,1);'><tbody>";
            echo "<tr>";
                echo "<td style='vertical-align:top;'>" . __('Tipo') . "</td>";
                echo "<td style='vertical-align:top;'>";
                    Dropdown::show('PluginComproveedoresRoltype',$opt);
                echo "</td>";
                echo "<td rowspan='2' style='vertical-align:top;'>" . __('Especialidades') . "</td>";
                echo "<td rowspan='2' style='vertical-align:top;'>";
                    echo "<div id='IdEspecialidades' class='list-especialidades'> ------ </div>";
                echo "</td>";                
            echo "</tr>";
            echo "<tr>";
                echo "<td style='vertical-align:top;'>Categorías</td>";
                echo "<td style='vertical-align:top;'>";
                    echo "<div id='IdCategorias'>";
                        echo "<span class='no-wrap'><div class='select2-container'><a class='select2-choice'><span class='select2-chosen'>------</span></a></div></span>";
                    echo "</div>";
                echo "</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td colspan='4' style='text-align: center;'>";
                    echo "<input id='addToCV' type='submit' title='AÑADIR ESPECIALIDAD AL CV' class='boton_add' name='add' value='' />";
                echo "</td>";
            echo "</tr>";
            echo "</table>";

            
            
            /////Formulario añadir nueva especialidad
            /**
            echo"<div style='display:none;' id='dialogNuevaEspecialidad' title='Añadir Nueva Especialidad'>";
            echo"<form action='".$CFG_GLPI["root_doc"]."/plugins/comproveedores/front/listspecialty.form.php' method='get'>";
            echo"<div>Nombre Especialidad</div>";
                    Html::autocompletionTextField($this, "nombre_especialidad");
                    echo Html::hidden('categoria_nueva_especialidad');
            echo"<input type='submit' name='add_nueva_especialidad' title='AÑADIR NUEVA ESPECIALIDAD AL CV' value='AÑADIR' />";
            echo"</form>";
            echo"</div>";**/

            /*///////////////////////////////
            //LISTAR ESPECIALIDADES DEL PROVEEDOR
            ///////////////////////////////*/
            $query2 ="SELECT ls.id, rt.name as roltypes, c.name as categories, sp.name as specialities, ls.plugin_comproveedores_roltypes_id, ls.plugin_comproveedores_categories_id, ls.plugin_comproveedores_specialties_id, ls.cv_id
FROM glpi_plugin_comproveedores_listspecialties as ls
left join glpi_plugin_comproveedores_roltypes as rt on ls.plugin_comproveedores_roltypes_id = rt.id
left join glpi_plugin_comproveedores_categories as c on c.id = ls.plugin_comproveedores_categories_id
left join glpi_plugin_comproveedores_specialties as sp on sp.id = ls.plugin_comproveedores_specialties_id
            WHERE cv_id=".$CvId;

            $result2 = $DB->query($query2);

            //Ocultar lista, si no existe ninguna especialidad
            if($result2->num_rows!=0){

                    echo "<div style='position: relative;float: left; width:100%;'>";
                    //echo $query2;
                    echo "<table id='tablaEspecialidades' class='display compact' style='margin-top:0px;'>";
                    echo "<thead>";
                        echo "<tr>";
                            echo "<th>".__('Tipo')."</th>";
                            echo "<th>".__('Categoría')."</th>";
                            echo "<th>".__('Especialidad')."</th>";
                            //if($profile_Id!=14){
                            echo "<th>".__('Quitar')."</th>";
                            //}
                        echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";

                    while ($data=$DB->fetch_array($result2)) {
                            echo "<tr>";
                                echo "<td class='left'>".$data['roltypes']."</td>";
                                echo "<td class='left'>".$data['categories']."</td>";
                                echo "<td class='left'>".$data['specialities']."</td>";
//                                if($profile_Id!=14){
                                    echo "<td>";
                                    echo "<input id='quitar_{$data['id']}' title='Quitar especialidad' type='submit' class='boton_borrar' value='' style='border-radius:0px;width:15px;height:15px;background-size:12px;'/>";                        
                                    echo "</td>";
//                                }
                            echo"</tr>";
                    }

                    echo"</tbody>";
                    echo "</table>";
                    echo "</div>";

            }	
            echo "</div>";

//            if($profile_Id==14){
//                echo "<script type='text/javascript'>";
//                echo "$('#tblAux').css('display', 'none');";
//                echo "</script>";
//            }
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
                
                
        /*usuario proveedor*/
        function showFormItem($item, $withtemplate='') {
	GLOBAL $DB,$CFG_GLPI;

            $opt['specific_tags']=array('onchange' => 'cambiarCategorias(value)');
            $opt['comments']= false;
            $opt['addicon']= false;


            $CvId = $item->fields['id']; 
            if($CvId == 0){$CvId = -1;}            

            $self                = new self();
            $user_Id             = $_SESSION['glpiID'];
            $profile_Id          = $self->getProfileByUserID($user_Id);
            $ver                 = true;
            if(in_array($profile_Id, array(3,4,16))){    
                $ver = true;
                echo "<input id='verBotonesEspecialidades' type='hidden' value='1' />";
            }else{
                $ver = false;
                echo "<input id='verBotonesEspecialidades' type='hidden' value='0' />";
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
            
            echo "<div style='float:left; width:98%; border-radius: 4px; padding: 8px; background-color: #e9ecf3; height: 500px; margin-bottom: 10px;'>";
            echo $this->consultaAjax();

            echo "<input id='cv_id' type='hidden' value='{$CvId}' />";
            echo "<table id='tblAux' class='tab_cadre_fixe' style='margin-top:0px; background-color: #f8f7f3; border-radius: 4px; -webkit-box-shadow: 12px 8px 5px -5px rgba(219,219,219,1);    -moz-box-shadow: 12px 8px 5px -5px rgba(219,219,219,1);    box-shadow: 12px 8px 5px -5px rgba(219,219,219,1);'><tbody>";
            echo "<tr>";
                echo "<td style='vertical-align:top;'>" . __('Tipo') . "</td>";
                echo "<td style='vertical-align:top;'>";
                    Dropdown::show('PluginComproveedoresRoltype',$opt);
                echo "</td>";
                echo "<td rowspan='2' style='vertical-align:top;'>" . __('Especialidades') . "</td>";
                echo "<td rowspan='2' style='vertical-align:top;'>";
                    echo "<div id='IdEspecialidades' class='list-especialidades'> ------ </div>";
                echo "</td>";                
            echo "</tr>";
            echo "<tr>";
                echo "<td style='vertical-align:top;'>Categorías</td>";
                echo "<td style='vertical-align:top;'>";
                    echo "<div id='IdCategorias'>";
                        echo "<span class='no-wrap'><div class='select2-container'><a class='select2-choice'><span class='select2-chosen'>------</span></a></div></span>";
                    echo "</div>";
                echo "</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td colspan='4' style='text-align: center;'>";
                    echo "<input id='addToCV' type='submit' title='AÑADIR ESPECIALIDAD AL CV' class='boton_add' name='add' value='' />";
                echo "</td>";
            echo "</tr>";
            echo "</table>";
            //echo "</div>";

            /////Formulario añadir nueva especialidad
            /**
            echo"<div style='display:none;' id='dialogNuevaEspecialidad' title='Añadir Nueva Especialidad'>";
            echo"<form action='".$CFG_GLPI["root_doc"]."/plugins/comproveedores/front/listspecialty.form.php' method='get'>";
            echo"<div>Nombre Especialidad</div>";
                    Html::autocompletionTextField($this, "nombre_especialidad");
                    echo Html::hidden('categoria_nueva_especialidad');
            echo"<input type='submit' name='add_nueva_especialidad' title='AÑADIR NUEVA ESPECIALIDAD AL CV' value='AÑADIR' />";
            echo"</form>";
            echo"</div>";**/

            /*///////////////////////////////
            //LISTAR ESPECIALIDADES DEL PROVEEDOR
            ///////////////////////////////*/

            $query2 ="SELECT ls.id, rt.name as roltypes, c.name as categories, sp.name as specialities, ls.plugin_comproveedores_roltypes_id, ls.plugin_comproveedores_categories_id, ls.plugin_comproveedores_specialties_id, ls.cv_id
FROM glpi_plugin_comproveedores_listspecialties as ls
left join glpi_plugin_comproveedores_roltypes as rt on ls.plugin_comproveedores_roltypes_id = rt.id
left join glpi_plugin_comproveedores_categories as c on c.id = ls.plugin_comproveedores_categories_id
left join glpi_plugin_comproveedores_specialties as sp on sp.id = ls.plugin_comproveedores_specialties_id
            WHERE cv_id=".$CvId;
            
            $result2 = $DB->query($query2);

            //Ocultar lista, si no existe ninguna especialidad
            if($result2->num_rows!=0){

                    echo "<div style='position: relative;float: left; width:100%;'>";
                    echo "<table id='tablaEspecialidades' class='display compact' style='margin-top:0px;'>";
                    echo "<thead>";
                        echo "<tr>";
                            echo "<th>".__('Tipo')."</th>";
                            echo "<th>".__('Categoría')."</th>";
                            echo "<th>".__('Especialidad')."</th>";
                            echo "<th>".__('Quitar')."</th>";
                        echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";

                    while ($data=$DB->fetch_array($result2)) {
                            echo "<tr>";
                                echo "<td class='left'>".$data['roltypes']."</td>";
                                echo "<td class='left'>".$data['categories']."</td>";
                                echo "<td class='left'>".$data['specialities']."</td>";
                                echo "<td>";
                                    echo"<input id='quitar_{$data['id']}' title='Quitar especialidad' type='submit' class='boton_borrar' value='' style='border-radius:0px;width:15px;height:15px;background-size:12px;'/>";                        
                                echo "</td>";
                            echo"</tr>";
                    }

                    echo"</tbody>";
                    echo "</table>";
                    echo "</div>";

            }	
            echo "</div>";
        }

        function consultaAjax(){
        GLOBAL $DB,$CFG_GLPI;
            $consulta="<script type='text/javascript'>

                $(document).ready(function(){
                
                    $('#dialogoFormulario').dialog({
                        // Indica si la ventana se abre de forma automática
                        autoOpen: false,
                        // Indica si la ventana es modal
                        modal: true,
                        // Largo
                        width: 350,
                        // Alto
                        height: 'auto'
                    });
                    
                    if($('#verBotonesEspecialidades').val()=='1'){
                        $('#tablaEspecialidades').DataTable({
                            'searching':      true,
                            'scrollY':        '250px',
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
                        $('#tablaEspecialidades').DataTable({
                            'searching':      true,
                            'scrollY':        '250px',
                            'scrollCollapse': true,
                            'ordering':       true,
                            'paging':         false,
                            'language': {'url': '".$CFG_GLPI["root_doc"]."/lib/jqueryplugins/Spanish.json'}                
                        });                       
                    }
                    
                });

                function recarga(){
                    window.location.reload(true);
                };
                
                $('.boton_borrar').on('click', function(){
                
                    var resp = confirm('¿Realmente desea quitar este elemento?', 'Confirme borrado');
                    if(resp){
                        var id = $(this).attr('id').replace('quitar_', '');
                        var parametros = {'id' : id};

                        $.ajax({  
                            type: 'GET',  
                            async: false,               
                            url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/quitarEspecialidadEnCV.php',                    
                            data: parametros, 
                            success:function(data){
                                recarga();
                            },
                            error: function(result) {
                                alert('Data not found');
                            }
                        });      
                    }
                });

                $('#addToCV').on('click',function(){
                    
                    var idtipoespecialidad = 0;
                    if($('input[name=plugin_comproveedores_roltypes_id]').length){
                        idtipoespecialidad = $('input[name=plugin_comproveedores_roltypes_id]').val();
                    }
                    
                    var idcategoria = 0;
                    if($('input[name=plugin_comproveedores_categories_id]').length){
                        idcategoria = $('input[name=plugin_comproveedores_categories_id]').val();
                    }
                    
                    var cv_id = $('#cv_id').val();
                    var identificador = $('#identificador').val();
                    var idespecialidad = 0;
                    
                    if($('.especialidades_check').length){
                        $('.especialidades_check:checked').each(function() {
                            idespecialidad = 0;
                            if($(this).val()!=''){
                                idespecialidad = $(this).val();
                            }

                            var parametros = {
                                    'idtipo' : idtipoespecialidad,
                                    'idcategoria' : idcategoria,
                                    'idespecialidad': idespecialidad,
                                    'idcv': cv_id
                            };      

                            $.ajax({  
                                type: 'GET',        		
                                url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/insertarEspecialidadEnCV.php',
                                data: parametros,   		
                                success:function(data){  
                                    recarga();
                                },
                                error: function(result) {
                                    alert('Data not found');
                                }
                            });                                
                        }); 
                    }else{
                        idespecialidad = 0;
                        var parametros = {
                                'idtipo' : idtipoespecialidad,
                                'idcategoria' : idcategoria,
                                'idespecialidad': idespecialidad,
                                'idcv': cv_id
                        };    
                        $.ajax({  
                            type: 'GET',        		
                            url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/insertarEspecialidadEnCV.php',
                            data: parametros,   		
                            success:function(data){      
                                recarga();
                            },
                            error: function(result) {
                                alert('Data not found');
                            }
                        });                            
                    }
                    
                });

                function cambiarCategorias(valor){
                    $.ajax({  
                        type: 'GET',        		
                        url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/selectCategoriesAndSpecialty.php',
                        data: {idRolType:valor, tipo:'categoria'},   		
                        success:function(data){
                            $('#IdCategorias').html(data);
                        },
                        error: function(result) {
                            alert('Data not found');
                        }
                    });
                }

                function cambiarEspecialidades(valor){
                    $.ajax({  
                        type: 'GET',        		
                        url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/selectCategoriesAndSpecialty.php',
                        data: {idCategories:valor, tipo:'especialidad'},   		
                        success:function(data){
                            $('#IdEspecialidades').html(data);
                        },
                        error: function(result) {
                            alert('Data not found');
                        }
                    });

                    //añadimos al formulario de añadir nueva especialidad, el is de la categoria
                    $('[name=categoria_nueva_especialidad]').val(valor);
                }

                function añadirNuevaEspecialidad(){
                    if($('[name=categoria_nueva_especialidad]').val()!=''){
                        $('#dialogNuevaEspecialidad').dialog();
                    }	
                }

                </script>";

                return $consulta;
        }

	}