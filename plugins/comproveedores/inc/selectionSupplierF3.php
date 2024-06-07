<?php

use Glpi\Event;

include ("../../../inc/includes.php");

GLOBAL $DB,$CFG_GLPI;

$objCommonDBT=new CommonDBTM();
$preselecionIds='';

/**
$facturacion_maxima = 0;
$query = "select max(facturacion) as facturacion_maxima from glpi_plugin_comproveedores_annualbillings";
$result = $DB->query($query);
while ($data=$DB->fetch_array($result)) {
	$facturacion_maxima = $data['facturacion_maxima'];
}**/


$javascripts = "<script type='text/javascript'>
        
                var arrayProveedoresElegidos= new Array();
                
                function guardarPreseleccion( paquete_id, preselecionIds){

                        var parametros = {
                                'preseleccion_guardar': 'preseleccion_guardar',
                                'arrayPreselecion':preselecionIds,
                                'paquete_id' : paquete_id
                        };
                      
                        $.ajax({  
                                type: 'GET',        		
                                url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/front/selectionsupplier.form.php',
                                data: parametros,   		
                                success:function(data){ 
                                   alert(data);
                                },
                                error: function(result) {
                                    alert('Data not found');
                                }
                        });
                }

				function atras(paquete_id){
						
                        var parametros = {
                            'paquete_id': paquete_id
                        };
                      
                        $.ajax({  
                                type: 'GET',        		
                                url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/selectionSupplierF1.php',
                                data: parametros,   		
                                success:function(data){ 
                                   $('#selector_proveedor').html(data);
                                },
                                error: function(result) {
                                    alert('Data not found');
                                }
                        });					
				}

				
                function imprimirPdf(preselecionIds, contrato_id){
                                
                        window.open('".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/lisSelectionSupplierPDF.php?ids='+preselecionIds+'&contrato_id='+contrato_id,'_blank');

                }

                function setListaProveedorfiltro(supplier_id){
                   
                        if($('#proveedor_'+supplier_id).prop('checked')){
                           
                           arrayProveedoresElegidos[supplier_id]=supplier_id;
                        }
                        else{
                           
                            delete  arrayProveedoresElegidos[supplier_id];
                        }
				}
        
                function filtrarListaProveedores(paquete_id){
                
                        var experiencias_id=new Array();
                        for(var i=0; i<12; i++){
                            if($('.selector_proveedor').find('input[name=tipos_experiencias_'+i+']').prop('checked')){
                                experiencias_id.push($('.selector_proveedor').find('input[name=tipos_experiencias_'+i+']').val());
                            }
                        }
                      
                        if($('.selector_proveedor > input[name=intervencion_bovis]').prop('checked')) {	
							intervencion_bovis=1;
                        }else{	
							intervencion_bovis=0;
                        }

                        if($('.selector_proveedor > input[name=bim]').prop('checked')) {	
							bim=1;
                        }else{	
							bim=0;
                        }

                        if($('.selector_proveedor > input[name=breeam]').prop('checked')) {		
							breeam=1;
                        }else{	
							breeam=0;
						}

                        if($('.selector_proveedor > input[name=leed]').prop('checked')) {
							leed=1;
                        }else{	
							leed=0;
                        }

                        if($('.selector_proveedor > input[name=otros_certificados]').prop('checked')) {	
							otros_certificados=1;
                        }else{
							otros_certificados=0;
                        }

                       var parametros = {
                                'actualizar_lista': 'actualizar_lista',
                                'paquete_id':paquete_id,
                                'arrayProveedoresElegidos' : arrayProveedoresElegidos,
                                'nombre_proveedor': $('.selector_proveedor > input[name=nombre_proveedor]').val(),
                                'experiencia_id': experiencias_id,
                                'intervencion_bovis':intervencion_bovis,
                                'bim':bim,
                                'breeam':breeam,
                                'leed':leed,
                                'otros_certificados':otros_certificados
                        };

                        $.ajax({  
                            type: 'GET',        		
                            url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/front/selectionsupplier.form.php',
                            data: parametros,   		
                            success:function(data){
                                
                                //Pasamos los id de los proveedore a un array
                                var proveedores_aptos=data.split(',');
                                existe_proveedor=false;
                                
                                for(var i=0;i<=arrayProveedoresElegidos.length;i++){
                                
                                        //Recorremos en array para comprobar si coinciden
                                        for(var j=0;j<=proveedores_aptos.length;j++){
                                       
                                                //Si coinciden no se elimina, cumple con los requisitos del filtro
                                                if(proveedores_aptos[j]==arrayProveedoresElegidos[i] 
                                                && proveedores_aptos[j]!=null 
                                                && arrayProveedoresElegidos[i]!=null){
       
                                                        existe_proveedor=true;
                                               }
                                        }
                                        
                                        //Si no cumple los requisitos del filtro, ponemos el checkbox a falso y lo quitamos de arrayProveedoresElegidos
                                        if(!existe_proveedor && arrayProveedoresElegidos[i]!=null){
                                        
                                                $('#proveedor_'+arrayProveedoresElegidos[i]).prop('checked', false); 
                                                delete  arrayProveedoresElegidos[i];                                                                                               
                                        }else{
                                        
                                                existe_proveedor=false;
                                        }
                                }
                               
                            },
                            error: function(result) {
                                alert('Data not found');
                            }
                        });
				
				}
        
                function incluirProveedoresAlPaquete(paquete_id){
                                     
                        var numProveedores=0;

                        for(var i=0;i<=arrayProveedoresElegidos.length;i++){
                                
                                if(arrayProveedoresElegidos[i]!=null){
                                        numProveedores++;
                                }
                                   
                        }
                       
                        if(numProveedores==1){
                                var parametros = {
                                        'add_proveedor_al_paquete': 'add_proveedor_al_paquete',
                                        'paquete_id':paquete_id,
                                        'arrayProveedoresElegidos' : arrayProveedoresElegidos,   
                                };

                                $.ajax({  
                                    type: 'GET',        		
                                    url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/front/selectionsupplier.form.php',
                                    data: parametros,   		
                                    success:function(data){
                                        location.reload();
                                    },
                                    error: function(result) {
                                        alert('Data not found');
                                    }
                                });
                        }
				}
	
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

                }otrosSel
				
				$('#otros_certificados').on('click', function () {
					if ($('#otros_certificados').prop('checked') {
						$('#otrosSel').attr('opacity', '1');
					}else{
						$('#otrosSel').attr('opacity', '0');
					}
				});

        		$('#botonAnterior').on('click', function () {
                    atras(".$_GET['paquete_id'].");					
				});
				
        		$('#botonSiguiente').on('click', function () {
					
				});			
				
        		$('#botonExcel').on('click', function () {
					
				});					
				
        		$('#botonFiltro').on('click', function () {
					filtrarListaProveedores(".$_GET['paquete_id'].")
				});
</script>";
				
echo $javascripts;
$lista=getTiposExperiencias();
echo"<table  class='tab_cadre_fixe' style='width:30%;'>";

        echo"<tr class='tab_bg_1 center' style='vertical-align: top;'>";         
			echo "<td rowspan='7' style='width: 30%; text-align:left;'>" . __('Tipos de experiencias');
				echo "<div style='text-align:left; width: 200px; height: 200px; overflow-y: scroll; border: 1px solid #BDBDBD'>";
					foreach ($lista as $key => $value) {
						echo "&nbsp&nbsp<input type='checkbox' name='tipos_experiencias_$key' value=$key />&nbsp&nbsp".$value."<br />";
					}
				echo "</div>";
			echo "</td>";               
		echo "</tr>";
		
		echo"<tr class='tab_bg_1 center' style='height:10px;'>";
				echo "<td style='width:15%; text-align:left;' colspan='3'>" . __('Experiencia con BOVIS') . "</td>";
				echo "<td style='width:5%; text-align:left;' colspan='2' class='selector_proveedor' style='text-align: left;'>";
						echo"<input type='checkbox' name='intervencion_bovis'/>";
				echo "</td>";			
		echo "</tr>";
		
		echo"<tr class='tab_bg_1 center' style='height:10px;'>";
				echo "<td style='text-align:left;'>";
					echo "LEED"; 
				echo "</td>";
				echo "<td style='text-align:left;'>";
					echo "<input type='checkbox' name='leed'/>";
				echo "</td>";
				echo "<td style='text-align:left;'>";
					echo "BIM";
				echo "</td>";
				echo "<td style='text-align:left;'>";
					echo "<input type='checkbox' name='bim'/>";
				echo "</td>";
		echo "</tr>";
		echo"<tr class='tab_bg_1 center' style='height:10px;'>";
				echo "<td style='text-align:left;'>";
					echo "BREEAM";
				echo "</td>";
				echo "<td style='text-align:left;'>";
					echo "<input type='checkbox' name='breeam'/>";
				echo "</td>";
				echo "<td style='text-align:left;'>";
					echo "Otros";
				echo "</td>";
				echo "<td style='text-align:left;' colspan='2'>";
					echo "<input id='otros_certificados' type='checkbox' name='otros_certificados'/>";
				echo "</td>"; /*
				echo "<td style='text-align:left;'>";
					echo "<select id='otrosSel' style='opacity:0;'>";
						echo "<option> --------------------- </option>";
						echo "<option>CERTIFICADO 1</option>";
						echo "<option>CERTIFICADO 2</option>";
					echo "</select>";
				echo "</td>"; 	*/			
		echo "</tr>";
	
		echo"<tr class='tab_bg_1 center'>";
				echo"<td colspan='5' center>";
					//echo "<span class='vsubmit' style='margin-right: 15px;' onClick='location.reload();'>ATRAS</span>";
					//echo "<span onclick='filtrarListaProveedores(".$_GET['paquete_id'].")' class='vsubmit' style='margin-right: 15px;'>FILTRAR</span>";
					echo "<input id='botonAnterior' type='submit' title='RETROCEDER' class='boton_anterior' value=' ' style='float:left;'/>";
					echo "<input id='botonSiguiente' type='submit' title='SIGUIENTE' class='boton_siguiente' value=' ' style='float:left;'/>";
					echo "<input id='botonExcel' type='submit' title='EXPORTAR A EXCEL' class='boton_excel' value=' ' style='float:right;'/>";
					echo "<input id='botonFiltro' type='submit' title='AÑADIR FILTRO A LA SELECCIÓN ACTUAL' class='boton_filtro' value=' ' style='float:right;'/>";
				 echo"</td>";
		echo "</tr>";
echo "</table>";

echo "<div style='width:100%; margin-top:20px;'>";
include 'listSelectionSupplier.php';
echo "</div>";

$contrato_id=$_GET['paquete_id'];

/**
echo"<span onclick='imprimirPdf(\"$preselecionIds\", ".$contrato_id.")' class='vsubmit' style='margin-right: 15px; '>IMPRIMIR</span>";
echo"<span onclick='guardarPreseleccion(".$_GET['paquete_id'].",\"$preselecionIds\")' class='vsubmit' style='margin-right: 15px; '>GUARDAR PRESELECCIÓN</span>";
echo "<span onclick='incluirProveedoresAlPaquete(".$_GET['paquete_id'].")' class='boton_licitacion' style='margin-right: 15px;'> </span>";
echo"<br>";
echo"<br>";
echo"<br>";**/



function getTiposExperiencias(){
	GLOBAL $DB,$CFG_GLPI;


	$query ="SELECT name FROM `glpi_plugin_comproveedores_experiencestypes` order by id";

	$result = $DB->query($query);
	$i=1;
	while ($data=$DB->fetch_array($result)) {
		$lista[$i]=$data['name'];
		$i++;
	}

	return $lista;
}


