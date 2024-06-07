<?php

/******************************************

	PLUGIN DE GESTION DE CURRICULUMS DE LOS PROVEEDORES


 ******************************************/

	class PluginComproveedoresEmpleado extends CommonDBTM{

		static $rightname	= "plugin_comproveedores";

		static function getTypeName($nb=0){
			return _n('Empleado en plantilla','Empleado en plantilla',1,'comproveedores');
		}

		function getTabNameForItem(CommonGLPI $item, $tabnum=1,$withtemplate=0){
			if($item-> getType()=="Supplier"){
				return self::createTabEntry('Empleados en plantilla');
			}
			return 'Empleados en plantilla';
		}


		static function displayTabContentForItem(CommonGLPI $item,$tabnum=1,$withtemplate=0){

			global $CFG_GLPI;
			$self = new self();

			if($item->getType()=='Supplier'){	
				if(isset($item->fields['cv_id'])){
					$self->showFormItemEmpleado($item, $withtemplate);
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
        
    function showFormItem($item, $withtemplate='') {	
            GLOBAL $DB,$CFG_GLPI;


            /*///////////////////////////////
            //AÑADIR EMPLEADOS AL PROVEEDOR
            ///////////////////////////////*/
            if($item->fields['cv_id']==0 || isset($item->fields['cv_id']) == false){
                $CvId = -1;
            }else{
                $CvId = $item->fields['cv_id'];     
            }

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
            
            echo"<form action=".$CFG_GLPI["root_doc"]."/plugins/comproveedores/front/empleado.form.php method='post'>";		
            echo Html::hidden('cv_id', array('value' => $CvId));

            echo Html::hidden('_glpi_csrf_token', array('value' => Session::getNewCSRFToken()));
            echo "<div class='center'>";
            echo"<table class='tab_cadre_fixe'><tbody>";
            echo"<tr class='headerRow'>";
            echo"<th colspan='4'>Añadir datos</th></tr>";
            echo"<tr class='tab_bg_1 center'>";
            echo "<td>" . __('Año') . "</td>";
            echo "<td>";

            Dropdown::showFromArray('anio', $this->getYears(),'');
            echo "</td>";
            echo"</tr>";

            echo"<tr class='tab_bg_1 center'>";
            echo "<td>" . __('Empleados Fijos') . "</td>";
            echo "<td>";
            Html::autocompletionTextField($this, "empleados_fijos");
            echo "</td>";
            echo "<td>" . __('Empleados Eventuales') . "</td>";
            echo "<td>";
            Html::autocompletionTextField($this, "empleados_eventuales");

            echo "</td>";
            echo"</tr>";



            echo"<tr><td colspan='4' class='center'><input type='submit' class='boton_add' name='add' value='' title='AÑADIR' /></td></tr>";
            echo"<tr class='tab_bg_1'>";
            echo"</tr>";
            echo"</tbody>";
            echo"</table>";
            echo"</div>";
            echo"</form>";

            $query2 ="SELECT * FROM glpi_plugin_comproveedores_empleados WHERE cv_id=$CvId" ;

            $result2 = $DB->query($query2);

            //Ocultar lista, si no existe ninguna expeciencia
            if($result2->num_rows!=0){

                echo "<div align='center'><table class='tab_cadre_fixehov'>";
                echo "<tr class='tab_bg_2 tab_cadre_fixehov nohover'><th colspan='14'>Empleados en plantilla</th></tr>";
                echo"<br/>";
                if (Session::isMultiEntitiesMode()){
                        echo "<th>".__('Entity')."</th>";
                }
                echo "<th>".__('Año')."</th>";
                echo "<th>".__('Empleados Fijos')."</th>";
                echo "<th>".__('Empleados Eventuales')."</th>";
                echo "<th>".__('Eliminar')."</th>";									
                echo "</tr>";

                while ($data=$DB->fetch_array($result2)) {
                        if($data['is_deleted']==""){
                                $data['is_deleted']=1;
                        }

                        echo "<tr>";
                        echo "<td class='center'>".$data['anio']."</td>";
                        echo "<td class='center'>".$data['empleados_fijos']."</td>";
                        echo "<td class='center'>".$data['empleados_eventuales']."</td>";

                        echo "<td class='center'>";
                        echo"<form action=".$CFG_GLPI["root_doc"]."/plugins/comproveedores/front/empleado.form.php method='post'>";
                        echo Html::hidden('id', array('value' => $data['id']));
                        echo Html::hidden('cv_id', array('value' => $data['cv_id']));
                        echo Html::hidden('_glpi_csrf_token', array('value' => Session::getNewCSRFToken()));
                        echo"<input title='Quitar acceso' type='submit' class='boton_borrar' value='' title='QUITAR' ame='purge'/>";
                        echo "</td>";
                        echo "</tr>";
                        echo"</form>";

                }
                echo "</tr>";
                echo "</table></div>";
                echo"<br>";
            }
            echo "<script type='text/javascript'>
            $('.icons_block').css('display', 'none');
            </script>";    

    }


    function showForm($ID, $options=[]) {
//Aqui entra desde el inicio de los proveedores
            global $CFG_GLPI;
            $this->initForm($ID, $options);
            $this->showFormHeader($options);

            echo"<th colspan='4'>Usuarios</th>";
            echo"<tr class='tab_bg_1 center'>";
            echo "<td>" . __('Nombre proyecto') . "</td>";
            echo "<td>";
            Html::autocompletionTextField($this, "name");
            echo "</td>";
            echo "<td>" . __('Comunidad Autonoma') . "</td>";
            echo "<td>";
//Html::autocompletionTextField($this, "comunidad");
            Dropdown::show('PluginComproveedoresCommunity',
                    array('value' => $this->fields["plugin_comproveedores_communities_id"]));

            echo "</td>";
            echo"</tr>";

            echo"<tr class='tab_bg_1 center'>";
            echo "<td>" . __('Cliente') . "</td>";
            echo "<td>";
            Html::autocompletionTextField($this, "cliente");
            echo "</td>";
            echo "<td>" . __('Año') . "</td>";
            echo "<td>";
            $options['value']=$this->fields["anio"];
            Dropdown::showFromArray('anio', $this->getYears(),array($options));
            echo "</td>";
            echo"</tr>";

            echo"<tr class='tab_bg_1 center'>";
            echo "<td>" . __('Importe contratado') . "</td>";
            echo "<td>";
            Html::autocompletionTextField($this, "importe");
            echo "</td>";
            echo "<td>" . __('Duración de su contratado') . "</td>";
            echo "<td>";
            Html::autocompletionTextField($this, "duracion");
            echo "</td>";
            echo"</tr>";

            echo"<tr class='tab_bg_1 center'>";
            echo "<td>" . __('BIM') . "</td>";
            echo "<td>";
            Dropdown::showYesNo('bim',$this->fields['bim']);
            echo "</td>";
            echo "<td>" . __('Breeam') . "</td>";
            echo "<td>";
            Dropdown::showYesNo('breeam',$this->fields['breeam']);
            echo "</td>";
            echo"</tr>";

            echo"<tr class='tab_bg_1 center'>";
            echo "<td>" . __('Leed') . "</td>";
            echo "<td>";
            Dropdown::showYesNo('leed',$this->fields['leed']);
            echo "</td>";
            echo "<td>" . __('Otros certificados') . "</td>";
            echo "<td>";
            Dropdown::showYesNo('otros_certificados',$this->fields['otros_certificados']);
            echo "</td>";
            echo"</tr>";

            echo"<tr class='tab_bg_1 center'>";
            echo "<td>" . __('Cpd Tier') . "</td>";
            echo "<td>";
            Dropdown::showYesNo('cpd_tier',null);
            echo "</td>";
            echo "<td>" . __('Observaciones') . "</td>";
            echo "<td>";
            Html::autocompletionTextField($this, "observaciones");
            echo "</td>";
            echo "</tr>";

            $this->showFormButtons($options);
    }




    function showFormItemEmpleado($item, $withtemplate='') {	

            GLOBAL $DB,$CFG_GLPI;


    /*///////////////////////////////
    //AÑADIR EXPERIENCIA AL PROVEEDOR
    ///////////////////////////////*/

    if($item->fields['cv_id']==0 || isset($item->fields['cv_id']) == false){
        $CvId = -1;
    }else{
        $CvId = $item->fields['cv_id'];     
    }            
    //$CvId=$item->fields['cv_id']; 
    
    $self                = new self();
    $user_Id             = $_SESSION['glpiID'];
    $profile_Id          = $self->getProfileByUserID($user_Id);

    echo "<div id='uno' style='background-color: #e9ecf3; width:98%; height: 500px; margin-top: 0px; overflow-y:auto;'>";
    //if($profile_Id!=14){
        echo"<form action=".$CFG_GLPI["root_doc"]."/plugins/comproveedores/front/empleado.form.php method='post'>";		
        echo Html::hidden('cv_id', array('value' => $CvId));

        echo Html::hidden('_glpi_csrf_token', array('value' => Session::getNewCSRFToken()));
        echo "<div class='center'>";
        echo"<table class='tab_cadre_fixe'><tbody>";
        echo"<tr class='headerRow'>";
        echo"<th colspan='4'>Añadir datos</th></tr>";
        echo"<tr class='tab_bg_1 center'>";
        echo "<td class='right'>" . __('Año') . "</td>";
        echo "<td class='left'>";
        Dropdown::showFromArray('anio', $this->getYears(),'');
        echo "</td>";
        echo"</tr>";

        echo"<tr class='tab_bg_1 center'>";
        echo "<td class='right'>" . __('Empleados Fijos') . "</td>";
        echo "<td class='left'>";
        Html::autocompletionTextField($this, "empleados_fijos");
        echo "</td>";
        echo "<td class='right'>" . __('Empleados Eventuales') . "</td>";
        echo "<td class='left'>";
        Html::autocompletionTextField($this, "empleados_eventuales");

        echo "</td>";
        echo"</tr>";
        echo"<tr><td colspan='4' class='center'><input type='submit' class='boton_add' name='add' value='' title='AÑADIR' /></td></tr>";
        echo"<tr class='tab_bg_1'>";
        echo"</tr>";
        echo"</tbody>";
        echo"</table>";
        echo"</div>";
        echo"</form>";
    //}
    /*///////////////////////////////
    //LISTAR EXPERIENCIA DEL PROVEEDOR
    ///////////////////////////////*/

    $query2 ="SELECT * FROM glpi_plugin_comproveedores_empleados WHERE cv_id=$CvId" ;

    $result2 = $DB->query($query2);

    //Ocultar lista, si no existe ninguna expeciencia
    if($result2->num_rows!=0){

            echo "<div align='center'><table class='tab_cadre_fixehov'>";
            echo "<tr class='tab_bg_2 tab_cadre_fixehov nohover'><th colspan='14'>Empleados en plantilla</th></tr>";
            echo"<br/>";
            if (Session::isMultiEntitiesMode())
                    echo "<th>".__('Entity')."</th>";
                    echo "<th>".__('Año')."</th>";
                    echo "<th>".__('Empleados Fijos')."</th>";
                    echo "<th>".__('Empleados Eventuales')."</th>";
                    //if($profile_Id!=14){
                        echo "<th>".__('Eliminar')."</th>";
                    //}						
                    echo "</tr>";

                    while ($data=$DB->fetch_array($result2)) {
                            if($data['is_deleted']==""){
                                    $data['is_deleted']=1;
                            }
                            echo "<tr>";
                            echo "<td class='center'>".$data['anio']."</td>";
                            echo "<td class='center'>".$data['empleados_fijos']."</td>";
                            echo "<td class='center'>".$data['empleados_eventuales']."</td>";
                            //if($profile_Id!=14){
                                echo "<td class='center'>";
                                echo"<form action=".$CFG_GLPI["root_doc"]."/plugins/comproveedores/front/empleado.form.php method='post'>";
                                echo Html::hidden('id', array('value' => $data['id']));
                                echo Html::hidden('cv_id', array('value' => $data['cv_id']));
                                echo Html::hidden('_glpi_csrf_token', array('value' => Session::getNewCSRFToken()));
                                echo"<input title='Quitar acceso' type='submit' class='boton_borrar' value='' title='QUITAR' name='purge'/>";
                                echo "</td>";
                                echo"</form>";                                    
                            //}
                            echo "</tr>";
                    }
                    echo "</tr>";
                    echo "</table></div>";
                    echo"<br>";

    }
    echo "</div>";
    }

    function getYears(){
            $year = date("Y");
            for ($i= $year; $i >  ($year-10); $i--) {

                    $lista[$i]=$i;

            }
            return $lista;
    }

    function showFormNoCV($ID, $options=[]) {
//Aqui entra cuando no tien gestionado el curriculum

            echo "<div>Necesitas gestionar el CV antes de añadir empleados en plantilla</div>";
            echo "<br>";
    }
}