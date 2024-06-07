<?php

GLOBAL $DB,$CFG_GLPI;

$objCommonDBT=new CommonDBTM;


echo "<script type='text/javascript'>

</script>";

echo consultaAjax();


echo "<form method='GET' action='".$CFG_GLPI["root_doc"]."/front/supplier.php'>";
echo"<table class='tab_cadre_fixe' style='margin-bottom: 20px; margin-top: 20px; width:50%; border-radius: 4px !important; box-shadow: 0px 1px 2px 1px #D2D2D2'>";
		echo"<thead><tr><th colspan='9' class='titulo_tabla'>PRESELECCIÓN DE PROVEEDORES (2/2)</th></tr></thead>";
		echo"<tbody>";
        echo"<tr class='tab_bg_1 center' >";
                echo "<td>" . __('Intervención de BOVIS') . "</td>";
                echo "<td class='selector_proveedor' style='text-align: left;'>";
                        echo "<input type='checkbox' name='intervencion_bovis'/>";
                echo "</td>";			
        echo "</tr>";

        echo"<tr class='tab_bg_1 center' style='vertical-align: top;'>";         
                echo "<td rowspan='5'>" . __('Tipos de experiencias') . "</td>";
                        $lista=getTiposExperiencias();
                        echo "<td rowspan='5' class='selector_proveedor' >";
		echo "<div style='text-align:left; width: 300px; height: 100px; overflow-y: scroll; border: 1px solid #BDBDBD'>";
                                        foreach ($lista as $key => $value) {
			echo "&nbsp&nbsp<input type='checkbox' name='tipos_experiencias_$key' value=$key />&nbsp&nbsp".$value."<br />";
                                        }
		echo "</div>";
                echo "</td>";               
        echo "</tr>";

        echo"<tr class='tab_bg_1 center' >";
                echo "<td>" . __('BIM') . "</td>";
                        echo "<td class='selector_proveedor' style='text-align: left;'>";
                                echo"<input type='checkbox' name='bim'/>";
                        echo "</td>";
        echo "</tr>";

        echo"<tr class='tab_bg_1 center'>";
                echo "<td>" . __('LEED') . "</td>";
	echo "<td class='selector_proveedor' style='text-align: left;'>";
                        echo"<input type='checkbox' name='leed'/>";
	echo "</td>";
        echo "</tr>";

        echo"<tr class='tab_bg_1 center '>";
                echo "<td>" . __('BREEAM') . "</td>";
                echo "<td class='selector_proveedor' style='text-align: left;'>";
                        echo"<input type='checkbox' name='breeam'/>";
                echo "</td>";                       
        echo "</tr>";

        echo"<tr class='tab_bg_1 center'>";
                echo "<td center>" . __('Otros certificados') . "</td>";
	echo "<td class='selector_proveedor' style='text-align: left;'>";
                        echo"<input type='checkbox' name='otros_certificados'/>";
	echo "</td>";
	echo "</tr>";
	echo"<tr class='tab_bg_1 center'>";
		echo"<td colspan='4' >";
		echo"</td>";
	echo "</tr>";
	echo"<tr class='tab_bg_1 center'>";
			echo"<td colspan='2' style='border-top: 1px solid #F3F3F3;'>";
				echo "<input style='float:right;' onclick='location.reload();' type='submit' class='boton_anterior' name='anterior' value=''  title='ANTERIOR (Alt+A)' accesskey='A'/>";
/**					echo "<span class='boton_anterior' style='margin-right: 15px;' onClick='location.reload();'> </span>";
					echo "<span onclick='filtrarListaProveedores(".$_GET['paquete_id'].")' class='boton_filtro' style='margin-right: 15px;'> </span>";
**/					
			 echo"</td>";
			echo"<td colspan='2' style='border-top: 1px solid #F3F3F3;'>";
				echo "<input onclick='filtrarListaProveedores(".$_GET['paquete_id'].")' type='submit' class='boton_filtro' name='filtro' value='' title='SIGUIENTE (Alt+F)' accesskey='F'/>";
			 echo"</td>";			 
	echo "</tr>";
echo "</tbody>";			
echo "</table>";
echo "</form>";


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

function consultaAjax(){

    GLOBAL $DB,$CFG_GLPI;
    
    $consulta="<script type='text/javascript'>
        
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

	}

	</script>";

    return $consulta;
}