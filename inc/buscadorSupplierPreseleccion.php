<?php
include ("../../../inc/includes.php");
GLOBAL $DB,$CFG_GLPI;
$objCommonDBT=new CommonDBTM;

$facturacion_maxima = 0;
$query = "select max(facturacion) as facturacion_maxima from glpi_plugin_comproveedores_annualbillings";
$result = $DB->query($query);
while ($data=$DB->fetch_array($result)) {
	$facturacion_maxima = $data['facturacion_maxima'];
}

echo "<script type='text/javascript'>

	function currency(value, decimals, separators) {
            decimals = decimals >= 0 ? parseInt(decimals, 0) : 2;
            separators = separators || ['.', '.', ','];
            var number = (parseFloat(value) || 0).toFixed(decimals);
            if (number.length <= (4 + decimals))
                    return number.replace('.', separators[separators.length - 1]);
            var parts = number.split(/[-.]/);
            value = parts[parts.length > 1 ? parts.length - 2 : 0];
            var result = value.substr(value.length - 3, 3) + (parts.length > 1 ?
                    separators[separators.length - 1] + parts[parts.length - 1] : '');
            var start = value.length - 6;
            var idx = 0;
            while (start > -3) {
                result = (start > 0 ? value.substr(start, 3) : value.substr(0, 3 + start))
                        + separators[idx] + result;
                idx = (++idx) % 2;
                start -= 3;
            }
            return (parts.length == 3 ? '-' : '') + result + ' €';
	}        

	function cambiarProvincia(valor, cargar_pagina){

            var provincia=null;
            if('#id_provincia'!='' && cargar_pagina){
                provincia='28'; 
            }

            var parametros = {
                'idComunidad': valor,
                'idProvincia':provincia
            };

            $.ajax({  
                type: 'GET',        		
                url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/select_provinces.php',
                data: parametros,
                success:function(data){
                    $('#id_provincia').html(data);
                },
                error: function(result) { alert('Data not found');	}
            });
	}
		
	function cambiarEspecialidades(valor){
            $.ajax({  
                type: 'GET',        		
                url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/selectCategoriesAndSpecialty.php',
                data: {idCategories:valor, tipo:'especialidad', width:'200'},   		
                success:function(data){
                    $('#IdEspecialidades').html(data);
                },
                error: function(result) {
                        alert('Data not found');
                }
            });
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
	
        $( '#slider-facturacion').slider({
            range: true,
            min: 0,
            max: ".$facturacion_maxima.",
            values: [ 75, 300 ],
            slide: function( event, ui ) {
                $('#minima' ).val( currency(ui.values[ 0 ]));
                $('#maxima' ).val( currency(ui.values[ 1 ]));
            }
        });        
        
</script>";

//TIPO
$opt_tipo['comments']=false;
$opt_tipo['addicon']=false;
$opt_tipo['width']='400px';
$opt_tipo['specific_tags']=array('onchange' => 'cambiarCategorias(value)');

//CATEGORÍAS
$opt_categoria['comments']=false;
$opt_categoria['addicon']=false;
$opt_categoria['width']='400px';
$opt_categoria['specific_tags']=array('onchange' => 'cambiarEspecialidades(value)');

//PROJECT
$opt_project['comments']=false;
$opt_project['addicon']=false;
$opt_project['width']='400px';

//CCAA
$opt3['comments']=false;
$opt3['addicon']=false;
$opt3['width']='203px';
$opt3['specific_tags']=array('onchange' => 'cambiarProvincia(value, false)');	

echo "<form method='GET' action='".$CFG_GLPI["root_doc"]."/front/supplier.php'>";
echo Html::hidden('_glpi_csrf_token', array('value' => Session::getNewCSRFToken()));
echo Html::hidden('preseleccion', array('value' => true));
echo"<table class='tab_cadre_fixe' style='margin-top: 20px; width:45%;border-radius: 4px !important; box-shadow: 0px 1px 2px 1px #D2D2D2'>";
		    echo"<thead><tr><th colspan='9' class='titulo_tabla'>asdPRESELECCIÓN DE PROVEEDORES (1/2)</th></tr></thead>";
			echo"<tbody>";
		
			echo"<tr class='tab_bg_1'>";
                            echo "<td class='campos_busqueda' >Tipo</td>";
                            echo "<td colspan='2' >";
                                    echo "<div id='IdTipo'>";
                                        Dropdown::show('PluginComproveedoresRoltype',$opt_tipo);              
                                    echo "</div>";
                            echo "</td>";
			echo "</tr>";
			
			echo"<tr class='tab_bg_1'>";
				echo "<td class='campos_busqueda' >Categorias</td>";
				echo "<td colspan='2' >";
					echo "<div id='IdCategorias' style='width:400px;'>";
							Dropdown::show('PluginComproveedoresCategory',$opt_categoria);              
					echo "</div>";
				echo "</td>";
			echo "</tr>";
        
			echo"<tr class='tab_bg_1'>";
				echo"<tr class='tab_bg_1'>";
				echo "<td class='campos_busqueda' >" . __('Especialidades') . "</td>";
				echo "<td colspan='2' >";
					echo "<div id='IdEspecialidades'>";
					echo "<span  class='no-wrap'>
							<div class='select2-container'>
							<a class='select2-choice'>
							<span class='select2-chosen' style='width:140px;'>------</span>
							</a>
							</div>
							</span>";
					echo "</div>";
				echo "</td>";
			echo "</tr>";
			
			echo"<tr class='tab_bg_1 left' style='background-color:#FFF; border: 20px solid #BDBDDB;'>";
				echo "<td class='campos_busqueda' >" . __('Facturación último ejercicio') . "</td>";
				echo "<td colspan='2' >";
				echo "mínima: <input type='text' id='minima' /> máxima: <input type='text' id='maxima' />";
				echo "<div id='slider-facturacion'style='margin-top: 4px; margin-bottom: 4px;'></div>";
				echo "</td>";
			echo "</tr>";		
			

			echo"<tr class='tab_bg_1 left' style='background-color:#FFF; border: 20px solid #BDBDDB;'>";
				echo "<td class='campos_busqueda'>";
				echo "Región ";
				echo "</td>";
				echo "<td id='idRegion'>";
				Dropdown::show('PluginComproveedoresCommunity',$opt3);
				echo "</td>";
				echo "<td class='campos_busqueda'>";
				echo "Provincia ";
				echo "</td>";
				echo "<td id='idProvincia'>";
				echo "<div id='id_provincia' style='display:inline-block; position:relative;'><span class='no-wrap'><div class='select2-container'><a class='select2-choice'><span class='select2-chosen'>-----------------------------</span></a></div></span></div>";
				echo "</td>";
			echo "</tr>";

			echo"<tr class='tab_bg_1 left' style='background-color:#FFF; border: 20px solid #BDBDDB;'>";
				echo "<td colspan='4' style='' class='campos_busqueda'>";
				echo "</td>";
			echo "</tr>";
			
			echo"</tbody>";
			echo"<tfoot>";
			echo"<tr class='tab_bg_1 center' style='background-color:#FFF; border: 20px solid #BDBDDB;'>";
            echo "<td colspan='4' style='border-top:1px solid #f3f3f3;'><input type='submit' class='boton_siguiente' name='search' value='' class='submit' title='SIGUIENTE (Alt+S)' accesskey='S'/></td>";
			echo "</tr>";
			echo"</tfoot>";
			
echo "</table>";
echo "</form>";