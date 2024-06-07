<?php

	use Glpi\Event;

	include ("../../../inc/includes.php");

	$opt['comments']= false;
	$opt['addicon']= false;
	if(isset($_GET['width'])){
		$opt['width']= $_GET['width'];
	}
	
	if($_GET['tipo']=='categoria'){

		$opt['specific_tags']=array('onchange' => 'cambiarEspecialidades(value)');
		$opt['condition']='glpi_plugin_comproveedores_roltypes_id='.$_GET['idRolType'];
		$opt['width']= '400px';
	
		Dropdown::show('PluginComproveedoresCategory', $opt);

	}else{

		$opt['condition']='glpi_plugin_comproveedores_categories_id='.$_GET['idCategories'];
		$opt['width']= '500px';
                $sql = "SELECT ID, NAME FROM glpi_plugin_comproveedores_specialties WHERE glpi_plugin_comproveedores_categories_id={$_GET['idCategories']} ORDER BY NAME ASC";
                $output = "";
                $result = $DB->query($sql);
                if($result->num_rows!=0){
                    $output = "<ul>";
                    $output = "<li id='linea_0' class='linea'><input id='check_0' type='checkbox' value='0' checked ><label id='etiqueta_0' class='etiqueta_check' style='font-weight:bold;'> SELECCIONAR TODOS </label></li>";
                    while ($data = $DB->fetch_array($result)) {
                        $output .= "<li id='linea_{$data['ID']}' class='linea'><input id='check_{$data['ID']}' value='{$data['ID']}' class='especialidades_check' type='checkbox' checked ><label id='etiqueta_{$data['ID']}' class='etiqueta_check'> {$data['NAME']}</label></li>";
                    }
                    $output .= "</ul>";

                    $output .= "
                        <script type='text/javascript'>


                        $('#check_0').on('click', function() {
                            if($(this).prop('checked') == true){
                                $('.especialidades_check').prop('checked',true);
                            }else{
                                $('.especialidades_check').prop('checked',false);
                            }
                        });

                        $('.etiqueta_check').on('click', function(){                       
                            var id = $(this).attr('id').replace('etiqueta_', '');
                            var str= '#check_'+id;
                            var val = $(str).prop('checked');

                            if(id==0){
                                if(val==true){
                                    $('.especialidades_check').prop('checked',true);
                                }else{
                                    $('.especialidades_check').prop('checked',false);
                                }
                            }else{
                                if(val==true){
                                    $(str).prop('checked',false);
                                }else{
                                    $(str).prop('checked',true);
                                }                          
                            }
                        });


                    </script>";
                }
                echo $output;                   
                //Dropdown::checkboxLis t('PluginComproveedoresSpecialty', $opt);
                
		//Dropdown::show('PluginComproveedoresSpecialty',$opt);
	}
	
	
