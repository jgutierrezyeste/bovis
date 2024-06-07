<?php

/******************************************

	PLUGIN DE GESTION DE CURRICULUMS DE LOS PROVEEDORES


 ******************************************/

	class PluginComproveedoresSubpaquete extends CommonDBTM{

		static $rightname	= "plugin_comproveedores";

		static function getTypeName($nb=0){
			return _n('SubPContrato','Subcontratistas',1,'comproveedores');
		}

		function getTabNameForItem(CommonGLPI $item, $tabnum=1,$withtemplate=0){
			if($item-> getType()=="Supplier"){
				return self::createTabEntry('Subcontratistas');
			}
			return 'Subcontratistas';
		}


		static function displayTabContentForItem(CommonGLPI $item,$tabnum=1,$withtemplate=0){

			global $CFG_GLPI;
			$self = new self();

			//Entrada Administrador
			
			$self->showFormItem($item, $withtemplate);
				
			


		}

		function getSearchOptions(){

			$tab = array();

			$tab['common'] = ('Subcontratista');

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

			GLOBAL $DB,$CFG_GLPI;

			$projecttasks_id=$item->fields['id']; 

                        $profileID = 0;
                        $USERID = $_SESSION['glpiID'];
                        $query0 = "SELECT profiles_id as profile FROM glpi_users WHERE id=$USERID";
                        $result0 = $DB->query($query0);
                        $aux0 = $DB->fetch_array($result0);
                        if($aux0['profile']<>''){
                            $profileID = $aux0['profile'];
                        }
                        $ver = true;
                        if(in_array($profileID, array(9,15))){  
                            $ver = false;
                            echo "<input id='verSubpaquete' type='hidden' value='0' />";
                        }else{
                            echo "<input id='verSubpaquete' type='hidden' value='1' />";
                        }                        
                        
			echo"<form style='margin-bottom:0px;' action=".$CFG_GLPI["root_doc"]."/plugins/comproveedores/front/subpaquete.form.php method='post'>";		
			echo Html::hidden('projecttasks_id', array('value' => $projecttasks_id));

			echo Html::hidden('_glpi_csrf_token', array('value' => Session::getNewCSRFToken()));
                        
                        
			echo "<div class='center' style='float: left; position: relative; margin-bottom: 10px; width:90%; background-color:#e9ecf3; border-radius: 4px; padding: 10px;'>";
			echo"<table class='tab_cadre_fixe' style='margin-bottom: 0px; margin-top: 4px; margin-left: 20%; padding: 5px; border-radius: 4px; background-color: #f8f7f3; -webkit-box-shadow: 12px 8px 5px -5px rgba(219,219,219,1); -moz-box-shadow: 12px 8px 5px -5px rgba(219,219,219,1); box-shadow: 12px 8px 5px -5px rgba(219,219,219,1);'><tbody>";                       
			echo "<td>" . __('Nombre del subcontrato') . "</td>";
			echo "<td>";
                                    Html::autocompletionTextField($this, "name");
			echo "</td>";
			echo "<td>". __('Subcontratista') . "</td>";
			echo "<td class='left'>";                    
                                    Dropdown::show('supplier',array('comments'=>false, 'width'=>'400px'));
			echo "</td>";  
			echo "<td class='left'>";                        
                                    echo "<div id='addproveedor' title='nuevo proveedor' class='boton_add_proveedor' style='width:25px;height:25px;background-size:24px;' ></div>";
			echo "</td>";                                                
			echo "<td>" . __('Valoración') . "</td>";
			echo "<td>";
                                    Dropdown::showFromArray('valoracion',array('----'=>'----', 'NO APTO'=>'NO APTO', 'POCO RECOMENDABLE'=>'POCO RECOMENDABLE', 'RECOMENDABLE'=>'RECOMENDABLE', 'MUY RECOMENDABLE'=>'MUY RECOMENDABLE'));
			echo "</td>";
                        echo "<td> </td>";
			echo "</tr>";

			echo"<tr class='center'>";
			echo"<td colspan='6'>";
                        echo "<input type='submit' class='boton_add' name='add' value='' style=''/>";
                        echo "</td>";
			echo"<tr class=''>";
			echo"</tr>";
			echo"</tbody>";
			echo"</table>";
			echo"</form>";

			$query2 ="SELECT * FROM glpi_plugin_comproveedores_subpaquetes WHERE projecttasks_id=$projecttasks_id" ;
                                               
			$result2 = $DB->query($query2);

                        echo "<table id='tablaSubcontratos' class='display compact'>";
                        echo "<thead>";
                        echo "<tr><th>".__('Subcontrato')."</th>";
                                echo "<th>".__('Subcontratista')."</th>";
                                echo "<th>".__('Valoración')."</th>";
                                echo "<th> </th>";
                        echo "</tr>";  
                        echo "</thead>";
                        echo "<tbody>";
                        if($result2->num_rows!=0){
                            while ($data=$DB->fetch_array($result2)) {
                                echo "<tr>";
                                        echo "<td class='center'>".$data['name']."</td>";
                                        echo "<td class='center'>".Dropdown::getDropdownName("glpi_suppliers",$data['suppliers_id'])."</td>";
                                        echo "<td class='center'>".$data['valoracion']."</td>";
                                        echo "<td><img id='delSubpaquete_{$data['id']}' class='boton_borrar' style='border: none; background-size:15px; width:20px; height:20px; border-radius:2px;' /></td>";
                                echo "</tr>";
                            }
                        }
			echo "</tbody></table></div>";
                        
                        echo "<div id='dialogoCrearSupplier' title='Crear subcontratista'>
                            <table>
                            <tr>
                                <td>CIF/NIF: </td>
                                <td><input id='cif' type='text' value=''/></td>
                            </tr>
                            <tr>
                                <td>NOMBRE: </td>
                                <td><input id='nombre' type='text' value=''/></td>
                            </tr>                            
                            </table>
                        </div>";
                        
                        echo "
                            <script type='text/javascript'>

                                if($('#verSubpaquete').val() == '1'){
                                    $('#tablaSubcontratos').DataTable({
                                        'searching':      true,
                                        'scrollY':        '480px',
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
                                    $('#tablaSubcontratos').DataTable({
                                        'searching':      true,
                                        'scrollY':        '480px',
                                        'scrollCollapse': true,
                                        'paging':         false,
                                        'language': {'url': '".$CFG_GLPI["root_doc"]."/lib/jqueryplugins/Spanish.json'}                  
                                    });                                
                                }
                                
                                $('.boton_borrar').on('click', function(){
                                
                                    var id = $(this).attr('id').replace('delSubpaquete_', '');
                                    $.ajax({ 
                                        async: false, 
                                        type: 'GET',
                                        data: {'id': id},                  
                                        url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/quitarSubpaqueteFromPaquete.php',  				
                                        success:function(data){
                                                window.location.reload(true);
                                        },
                                        error: function(result) {
                                                alert('Data not found');
                                        }
                                    });                                    
                                });
                                
                                $('#addproveedor').on('click', function() {

                                    $('#dialogoCrearSupplier').dialog('open');
                                });
                                
                               
                                $('#dialogoCrearSupplier').dialog({
                                    autoOpen: false,
                                    height: 200,
                                    width: 320,
                                    modal: true,
                                    buttons: {
                                        'Aceptar': function() { 
                                            var cif = $('#cif').val();
                                            var nombre = $('#nombre').val();
                                            $.ajax({ 
                                                    async: false, 
                                                    type: 'GET',
                                                    data: {'cif':cif,	
                                                           'nombre':nombre},                  
                                                    url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/crearSubContratista.php',  				
                                                    success:function(data){
                                                            window.location.reload(true);
                                                    },
                                                    error: function(result) {
                                                            alert('Data not found!');
                                                    }
                                            });				
                                            $('#dialogoCrearSupplier').dialog('close');
                                        },
                                        'Cancelar': function() {
                                          $('#dialogoCrearSupplier').dialog('close');
                                        }
                                    },
                                    close: function() {
                                        $('#dialogoCrearSupplier').dialog('close');
                                    }
                                });
                            </script>
                        ";
                                              
		}

}