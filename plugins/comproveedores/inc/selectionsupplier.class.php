<?php

/******************************************

	PLUGIN DE GESTION DE CURRICULUMS DE LOS PROVEEDORES


 ******************************************/

	class PluginComproveedoresSelectionSupplier extends CommonDBTM{

		static $rightname	= "plugin_comproveedores";

		static function getTypeName($nb=0){
			return _n('Licitación','Licitación',1,'comproveedores');
		}

		function getTabNameForItem(CommonGLPI $item, $tabnum=1,$withtemplate=0){
			if($item-> getType()=="Supplier"){
				return self::createTabEntry('Licitación');
			}
			return 'Licitación';
		}


		static function displayTabContentForItem(CommonGLPI $item,$tabnum=1,$withtemplate=0){

			global $CFG_GLPI;
			$self = new self();

			//Entrada Administrador
			
			$self->showFormItem($item, $withtemplate);
				
			


		}

		function getSearchOptions(){

			$tab = array();

			$tab['common'] = ('Licitación');

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

			$valor_contrato=$item->fields['valor_contrato'];
			$tipo_especialidad=$item->fields['tipo_especialidad'];
			$projecttasks_id=$item->fields['id']; 

                        $USERID = $_SESSION['glpiID'];
                        $self = new self();
                        $profile_Id=$self->getProfileByUserID($USERID);

                        $ver = true;
                        if(in_array($profile_Id, array(9,14,15))){  
                            $ver = false;
                            echo "<input id='ver' type='hidden' value='0' />";
                        }else{
                            echo "<input id='ver' type='hidden' value='1' />";
                        }
    
			$query = "SELECT name
			FROM glpi_plugin_comproveedores_roltypes
			WHERE ID=".$tipo_especialidad;
			$result = $DB->query($query);
			while ($data=$DB->fetch_array($result)) {
				$nombre_especialidad = $data['name'];
			}
			
			echo $this->consultaAjax($projecttasks_id);

			$query ="SELECT t.id, 
			t.projecttasks_id, 
			t.itemtype, 
			t.items_id, 
			s.name,
			s.cif,
			p.importe_ofertado, 
			p.calidad_oferta, 
			p.comentarios
			FROM glpi_projecttaskteams as t
			inner join glpi_plugin_comproveedores_preselections as p on t.projecttasks_id = p.projecttasks_id 
			inner join glpi_suppliers as s on s.id = t.items_id
			and t.items_id = p.suppliers_id
			WHERE t.projecttasks_id=$projecttasks_id" ;
			//echo $query;
		   
			$result = $DB->query($query);
			$proveedor_seleccionado = 0;
                        
                        echo "<div style='margin-bottom: 10px; height: 650px; overflow-y: auto; float: left; position:relative; width: 100%; background-color: #e9ecf3; border-radius: 4px; padding: 4px;'>";
                            echo "<div id='selector_proveedor' style=''>";

                            //Si no hay un proveedor seleccionado
                            if($result->num_rows == 0){
                                   $proveedor_seleccionado = 0;
                                   echo "<table style='width: 40%; margin-bottom: 15px; margin-top: 8px; margin-left: 30%; padding: 5px; border-radius: 4px; background-color: #f8f7f3;   -webkit-box-shadow: 12px 8px 5px -5px rgba(219,219,219,1);    -moz-box-shadow: 12px 8px 5px -5px rgba(219,219,219,1);    box-shadow: 12px 8px 5px -5px rgba(219,219,219,1);'>";                                    
                                   echo "<tr style='font-size: 12px; font-weight: bold;'>";
                                            echo "<td colspan='3' class='center' style='padding-right: 20px;'>PRESUPUESTO OBJETIVO (miles €): ".number_format($valor_contrato, 0, ',', '.')." € TIPO: ".$nombre_especialidad."</td>";
                                    echo "</tr>";
                                    echo "<tr>";
                                            echo "<td class='right'>NOMBRE :</td>";
                                            echo "<td class='left'><input id='nombre_lic' type='text' /></td>";
                                            echo "<td rowspan='2' class='left'><input id='incorporar' type='submit' class='boton_add' value=' ' title='INCORPORAR LICITADOR' style='margin-right: 15px;' /><input id='filtros' type='submit' class='boton_add_proveedor' value=' ' title='AÑADIR LICITADORES' style='margin-right: 15px;' /></td>";
                                    echo "</tr>";
                                    echo "<tr class='top' style='height: 30px;'>";
                                            echo "<td class='right'>CIF :</td>";
                                            echo "<td class='left'><input id='cif_lic' type='text' /></td>";
                                            echo "<td colspan='4'></td>";
                                    echo "</tr>";				
                                    echo "</table>";
                            }else{
                                    //si ya existe un proveedor seleccionado se muestran sus datos y los datos 
                                    //de las empresas que licitaron
                                    $proveedor_seleccionado = 1;
                                    echo "<table style='width: 60%; margin-left: 20%; margin-top: 8px; padding: 5px; border-radius: 4px; background-color: #f8f7f3;   -webkit-box-shadow: 12px 8px 5px -5px rgba(219,219,219,1);    -moz-box-shadow: 12px 8px 5px -5px rgba(219,219,219,1);    box-shadow: 12px 8px 5px -5px rgba(219,219,219,1);'>";
                                    while ($data=$DB->fetch_array($result)) {
                                            echo "<tr>";
                                                    echo "<td style='font-size: 16px; text-align: left;'>adjudicatario: <input id='nombre_adjudicatario' type='text' value='".$data['name']."' readonly style='width: 400px; font-size: 16px; font-weight: bold; background-color: #f8f7f3; border:none;'/></td>";
                                                    echo "<td style='font-size: 16px; text-align: left; padding-left:4px;'>cif/nif:  <input id='cif_adjudicatario' type='text' value='".$data['cif']."' readonly style='font-size: 16px; font-weight: bold; background-color: #f8f7f3; border:none;'/></td>";
                                            echo "</tr>";
                                            echo "<tr>";
                                                if($ver){
                                                    echo "<td style='font-size: 16px; text-align: left;'>importe de adjudicación: <input id='importe_adjudicatario' type='text' value='".number_format($data['importe_ofertado'],2,',','.')." €' readonly style='font-size: 16px; font-weight: bold; background-color: #f8f7f3; border:none;'/></td>";
                                                    echo "<td style='font-size: 16px; text-align: left;'>calidad de la oferta: <input id='calidad_adjudicatario' type='text' value='".$data['calidad_oferta']."' readonly style='font-size: 16px; font-weight: bold; background-color: #f8f7f3; border:none;'/></td>";
                                                }else{
                                                    echo "<td style='font-size: 16px; text-align: left;'>calidad de la oferta: <input id='calidad_adjudicatario' type='text' value='".$data['calidad_oferta']."' readonly style='font-size: 16px; font-weight: bold; background-color: #f8f7f3; border:none;'/></td>";                                                    
                                                    echo "<td style='font-size: 16px; text-align: left;'></td>";
                                                }
                                                
                                            echo "</tr>";
                                            echo "<tr>";
                                                    echo "<td colspan='2' style='font-size: 16px; text-align: left;'>comentarios: <input id='comentarios_adjudicatario' type='text' value='".$data['comentarios']."' readonly style='font-size: 16px; background-color: #f8f7f3; border:none; width: 600px;'/></td>";
                                            echo "</tr>";
                                    }
                                    echo "</table>";
                            }	
                        echo "<input type='hidden' id='proveedorSeleccionado' value='{$proveedor_seleccionado}' />";
                        echo "<div id='divLicitadores' style='float: left; position:relative; width: 100%;'></div>";
                        echo "</div>";
                        
			echo "<script type='text/javascript'>	
                                    var n=$('#nombre_lic').val();
                                    var c=$('#cif_lic').val();	

                                    $.ajax({ 
                                        async: false,  
                                        type: 'GET',
                                        data: {'projecttasks_id': ".$projecttasks_id.", 'nombre_adjudicatario': n},                  
                                        url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/listLicitadores.php',  				
                                        success:function(data){
                                                $('#divLicitadores').html(data);
                                        },
                                        error: function(result) {
                                                alert('Data not found: ".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/listLicitadores.php');
                                        }
                                    });
			</script>";
						
			//en el caso de que el proveedor ya esté seleccionado ocultamos el botón de quitar licitador
			//ya que el proceso ya se ha cerrado.
			if ($proveedor_seleccionado == 1) {
				echo "<script type='text/javascript'>
				$('.boton_borrar_licitadores').css('display', 'none');
				$('.boton_editar_licitadores').css('display', 'none');
                                $('.chkLic').css('display', 'none');
				</script>";
			}
                                             
		}
                
		function consultaAjax($ptid){

			GLOBAL $DB,$CFG_GLPI;

			$consulta="<script type='text/javascript'>
				
				$('#incorporar').on('click', function() {
                                    var n=$('#nombre_lic').val();
                                    var c=$('#cif_lic').val();	

                                    $.ajax({ 
                                        async: false, 
                                        type: 'GET',
                                        data: {'projecttasks_id':".$ptid.", 'nombre_lic':n, 'cif_lic':c},                  
                                        url: '".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/listLicitadores.php',  				
                                        success:function(data){
                                            $('#divLicitadores').html(data);
                                        },
                                        error: function(result) {
                                            alert('Data not found: ".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/listLicitadores.php');
                                        }
                                    });					
				});

				$('#filtros').on('click', function(){
                                    var strUrl = '".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/selectionSupplierF1.php';
                                    var projecttask_id = $('#identificador').val();
                                    
                                    $.ajax({ 
                                        async: false, 
                                        type: 'GET',        
                                        data: {'projecttask_id': projecttask_id},
                                        url: strUrl,  				
                                        success:function(data){
                                                $('#selector_proveedor').html(data);
                                        },
                                        error: function(result) {
                                                alert('Data not found: ".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/selectionSupplierF1.php');
                                        }
                                    });						
				});
				
				function seleccionProvedorFiltro(paquete_id, tipo_especialidad){
                                    $.ajax({ 
                                        async: false, 
                                        type: 'GET',
                                        data: {'paquete_id':  paquete_id, 'tipo_especialidad':tipo_especialidad},                  
                                        url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/selectionSupplierF1.php',                    
                                        success:function(data){
                                                        $('#selector_proveedor').html(data);
                                        },
                                        error: function(result) {
                                                        alert('Data not found');
                                        }
                                    });
				};

			</script>";

			return $consulta;
		}
}