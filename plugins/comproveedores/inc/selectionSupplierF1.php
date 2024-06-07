<?php
include ("../../../inc/includes.php");
GLOBAL $DB,$CFG_GLPI;
$objCommonDBT=new CommonDBTM;

$facturacion_maxima = 9999999999;
$query = "select max(facturacion) as facturacion_maxima from glpi_plugin_comproveedores_annualbillings";
$result = $DB->query($query);
while ($data=$DB->fetch_array($result)) {
	$facturacion_maxima = $data['facturacion_maxima'];
};
$projecttasks_id = 0;
if(!$_GET['projecttask_id']){
    $projecttasks_id = 0;
}else{
    $projecttasks_id = $_GET['projecttask_id'];
}
if(!$_GET['idtipo']){
    $idtipo = 0;
}else{
    $idtipo = $_GET['idtipo'];
}
if(!$_GET['idcategoria']){
    $idcategoria = 0;
}else{
    $idcategoria = $_GET['idcategoria'];
}
if(!$_GET['idespecialidad']){
    $idespecialidad = '';
}else{
    $idespecialidad = $_GET['idespecialidad'];
}
if(!$_GET['minima']){
    $minima = 0;
}else{
    $minima = $_GET['minima'];
}
if(!$_GET['maxima']){
    $maxima = 9999999999;
}else{
    $maxima = $_GET['maxima'];
}
if(!$_GET['ambitos']){
    $ambitos = '';
}else{
    $ambitos = $_GET['ambitos'];
}
if(!$_GET['Proveedores']){
    $strProveedores = '';
}else{
    $strProveedores = $_GET['Proveedores'];
}
//$arrayProveedores = explode(',', $strProveedores);

$js = "
<script type='text/javascript'>
        
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
        
	function seleccionProvedorFiltro(paquete_id){

            var d = new Date();
            var year = d.getFullYear();
            var min = 0;
            var max = 9999999999;
            if ($('#minima').val().replace('.', '').replace(',','.') == '') {
                    min = 0;
            }else{
                    min = $('#minima').val().replace('.', '').replace(',','.');
            }		
            
            var idtipoespecialidad = 0;

            if($('input[name=plugin_comproveedores_roltypes_id]').length){
                idtipoespecialidad = $('input[name=plugin_comproveedores_roltypes_id]').val();
            }
            var idcategoria = 0;
            if($('input[name=plugin_comproveedores_categories_id]').length){
                idcategoria = $('input[name=plugin_comproveedores_categories_id]').val();
            }

            var idespecialidad = '';
            $('.especialidades_check:checked').each(function() {
                if(idespecialidad!=''){
                    idespecialidad = idespecialidad+',';
                }
                idespecialidad = idespecialidad+$(this).val();
            });     

            var idregion = 0;
            if($('input[name=plugin_comproveedores_communities_id]').length){
                idregion = $('input[name=plugin_comproveedores_communities_id]').val();
            }                
            var idprovincia = 0;
            if($('input[name=plugin_comproveedores_provinces_id]').length){
                idprovincia = $('input[name=plugin_comproveedores_provinces_id]').val();
            }                 
            var ambitos = '';
            var idambito = '0';
            var cont = 0;
            $('.ambitos_check:checked').each(function(){
                if(ambitos.length>0){
                    idambito = $(this).attr('id').replace('checkAmbito_', '');
                    if(idambito!='0'){
                        ambitos = ambitos+','+$(this).attr('id').replace('checkAmbito_', '');
                    }
                }else{
                    idambito = $(this).attr('id').replace('checkAmbito_', '');
                    if(idambito!='0'){                
                        ambitos = $(this).attr('id').replace('checkAmbito_', '');
                    }
                }
                cont = cont + 1;
            });
            if(cont>=19){ ambitos = ''; }
            
            var strProveedores = '';
            var i = 0;
            $('.clsIncorpora:checked').each(function() {
                if(i == 0) {
                    strProveedores = $(this).attr('id').replace('proveedor_', '');
                }else{
                    strProveedores = strProveedores + ',' + $(this).attr('id').replace('proveedor_', '');
                }
                i = i + 1;
            });            
            if(i>0){
                var parametros = {
                    'Proveedores': strProveedores,
                    'PrimerFiltro' : true,
                    'paquete_id' : paquete_id,
                    'idtipo' : idtipoespecialidad,
                    'idcategoria' : idcategoria,
                    'idespecialidad': idespecialidad,
                    'minima' : min,
                    'maxima' : max,
                    'idregion': idregion,
                    'idprovincia': idprovincia,
                    'ambitos': ambitos,
                    'strExperiencias': '0,2,11,5,7,6,4,8,1,10,3,9,999'
                };
                
                $.ajax({ 
                        async: false, 
                        type: 'GET',
                        data: parametros,                 
                        url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/selectionSupplierF2.php',                    
                        success:function(data){
                            if($('#selector_proveedor').length){
                                $('#selector_proveedor').html(data);
                            }else{
                                $('#page').html(data);
                            }			
                        },
                        error: function(result) {
                                alert('Data not found');
                        }
                });
            }else{
                alert('No ha seleccionado ningún proveedor!<br>Para acceder a la siguiente fase de preselección,<br>antes debe seleccionar algún proveedor de la tabla.');
            }
	};
		
	function currency(value, decimals, separators) {
            decimals = decimals >= 0 ? parseInt(decimals, 0) : 0;
            separators = separators || ['.', '.', ','];
            var number = (parseFloat(value) || 0).toFixed(decimals);
            if (number.length <= (4 + decimals)) {
                    return number.replace('.', separators[separators.length - 1]);
            }
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
            return (parts.length == 3 ? '-' : '') + result;
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

	function cargarAmbito(){
            $.ajax({  
                type: 'GET',        		
                url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/selectAmbito.php',
                data: {},   		
                success:function(data){
                    $('#IdAmbitos').html(data);
                },
                error: function(result) {
                    alert('Data not found');
                }
            });
	}

	$('#anterior').on('click', function() {
            window.location.reload(true);
	});
	
	$('#siguiente').on('click', function() {
            var proyecttasks_id = $('#identificador').val();
            seleccionProvedorFiltro(proyecttasks_id);
	});
        
        $('#limpieza').on('click', function() {
            $.ajax({ 
                async: false, 
                type: 'GET',                
                url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/selectionSupplierF1.php',                    
                success:function(data){
                        $('#page').html(data);
                },
                error: function(result) {
                        alert('Data not found');
                }
            });
        });
        
        function Buscar(){
            var d = new Date();
            var year = d.getFullYear();
            var min = 0;
            var max = 9999999999;
            if ($('#minima').val().replace('.', '').replace(',','.') == '') {
                    min = 0;
            }else{
                    min = $('#minima').val().replace('.', '').replace(',','.');
            }		

            var idtipoespecialidad = 0;

            if($('input[name=plugin_comproveedores_roltypes_id]').length){
                idtipoespecialidad = $('input[name=plugin_comproveedores_roltypes_id]').val();
            }
            var idcategoria = 0;
            if($('input[name=plugin_comproveedores_categories_id]').length){
                idcategoria = $('input[name=plugin_comproveedores_categories_id]').val();
            }

            var idespecialidad = '';
            $('.especialidades_check:checked').each(function() {
                if(idespecialidad!=''){
                    idespecialidad = idespecialidad+',';
                }
                idespecialidad = idespecialidad+$(this).val();
            });     

            var idregion = 0;
            if($('input[name=plugin_comproveedores_communities_id]').length){
                idregion = $('input[name=plugin_comproveedores_communities_id]').val();
            }                
            var idprovincia = 0;
            if($('input[name=plugin_comproveedores_provinces_id]').length){
                idprovincia = $('input[name=plugin_comproveedores_provinces_id]').val();
            }                 
            var ambitos = '';
            var idambito = '0';
            var cantidad = 0;
            $('.ambitos_check:checked').each(function(){
                if(ambitos.length>0){
                    idambito = $(this).attr('id').replace('checkAmbito_', '');
                    if(idambito!='0'){
                        ambitos = ambitos+','+$(this).attr('id').replace('checkAmbito_', '');
                        cantidad++;
                    }
                }else{
                    idambito = $(this).attr('id').replace('checkAmbito_', '');
                    if(idambito!='0'){                
                        ambitos = $(this).attr('id').replace('checkAmbito_', '');
                        cantidad++;
                    }
                }
            });
            if(cantidad==19){ambitos = '';}
            var paquete_id = 0;
            if($('#identificador').length){
                paquete_id = $('#identificador').val();
            }
            
            var parametros = {
                'PrimerFiltro' : true,
                'Proveedores': '{$strProveedores}',
                'preseleccion' : '',
                'paquete_id' : paquete_id,
                'idtipo' : idtipoespecialidad,
                'idcategoria' : idcategoria,
                'idespecialidad': idespecialidad,
                'minima' : min,
                'maxima' : max,
                'idregion': idregion,
                'idprovincia': idprovincia,
                'ambitos': ambitos,
                'fase': 1
            };
            if(idcategoria>0){
                $.ajax({ 
                    async: true, 
                    type: 'GET',
                    data: parametros,                 
                    url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/listSelectionSupplier.php',       
                    success:function(data){
                        if($('#resultado').length>0){
                            $('#resultado').fadeIn(1000).html(data);
                        }else{
                            $('#page').html(data);
                        }			
                    },
                    error: function(result) {
                        alert('Data not found');
                    }
                });  
            }else{
                alert('Debe introducir al menos una categoría para poder realizar la búsqueda.');
            }        
        }


        $('#buscar').on('click', function() {
            $('#resultado').html('<div class=\'esperaResultado\'></div>');
            $('#buscar').addClass('boton_buscar_off');
            $('#buscar').removeClass('boton_buscar');
            $('#buscar').prop('disabled', true);
            Buscar();
            $('#buscar').addClass('boton_buscar');
            $('#buscar').removeClass('boton_buscar_off');
            $('#buscar').prop('disabled', '');            
        });
        


        $('#slider-facturacion').slider({
            range: true,
            min: 0,
            max: 9999999999,
            values: [ 0, 300 ],
            slide: function( event, ui ) {
                $('#minima' ).val( currency(ui.values[ 0 ]));
                $('#maxima' ).val( currency(ui.values[ 1 ]));
            }
        });        
        cargarAmbito();

        if({$idtipo}==0){              
            $('#anterior').prop('display','');
        }else{

            $('input[name=plugin_comproveedores_roltypes_id]').val({$idtipo});
            $('#minima').val({$minima});
            $('input[name=plugin_comproveedores_categories_id]').val({$idcategoria});
                
//            $('.especialidades_check:checked').each(function() {
//                if(idespecialidad!=''){
//                    idespecialidad = idespecialidad+',';
//                }
//                idespecialidad = idespecialidad+$(this).val();
//            });            
            $('#resultado').html('<div class=\'esperaResultado\'></div>');
            $('#buscar').addClass('boton_buscar_off');
            $('#buscar').removeClass('boton_buscar');
            $('#buscar').prop('disabled', true);
            Buscar();
            $('#buscar').addClass('boton_buscar');
            $('#buscar').removeClass('boton_buscar_off');
            $('#buscar').prop('disabled', '');  
        }
       
</script>";

        $tipo_especialidad = 0;
        if($projecttasks_id>0){
            $query = "select tipo_especialidad from glpi_projecttasks where id=".$projecttasks_id;
            $result = $DB->query($query);
            while ($data=$DB->fetch_array($result)) {
                    $tipo_especialidad = $data['tipo_especialidad'];
            }
            $idtipo = $tipo_especialidad;
        }
	
        //TIPO
        $opt_tipo['comments']=false;
        $opt_tipo['addicon']=false;
        $opt_tipo['width']='300px';
        $opt_tipo['value']  = $idtipo;
        $opt_tipo['specific_tags']  = array('onchange' => 'cambiarCategorias(value)');

	//CATEGORÍAS
	$opt_categoria['comments']      = false;
	$opt_categoria['addicon']       = false;
	$opt_categoria['width']         = '300px';
	$opt_categoria['condition']     = 'glpi_plugin_comproveedores_roltypes_id='.$idtipo;
        $opt_categoria['value']         = $idcategoria;
	$opt_categoria['specific_tags'] = array('onchange' => 'cambiarEspecialidades(value)');
	
	//PROJECT
	$opt_project['comments']        = false;
	$opt_project['addicon']         = false;
	$opt_project['width']           = '400px';
	
	//CCAA
	$opt3['comments']               = false;
	$opt3['addicon']                = false;
	$opt3['width']                  = '203px';
	$opt3['specific_tags']          = array('onchange' => 'cambiarProvincia(value, false)');

	//AMBITO
	$opt4['comments']               = false;
	$opt4['addicon']                = false;
	$opt4['width']                  = '200px';        
echo "
  <style>
  .ui-progressbar {
    position: relative;
    border: 1px solid #444;
    margin:4px;
  }
  .progress-label {
    position: absolute;
    left: 50%;
    top: 4px;
    font-weight: bold;
    text-shadow: 1px 1px 0 #fff;
  }
  .esperaResultado{
    background-image: url(../pics/spinner48.gif);
    background-position: center center;
    background-repeat: no-repeat;
    width: 100px;
    height: 60px;
    margin-top: 4%;
    display: inline-block;
    vertical-align: middle;
  }
  </style>";
echo "<input type='hidden' value='".$idtipo."' id='tipo_especialidad'/>";
echo "<table class='tab_cadre_fixe' style='font-size: 10px; margin-top: 4px; border-radius: 4px !important;box-shadow: 0px 1px 2px 1px #D2D2D2;'>";
    echo "<thead><tr><td class='center' colspan='7' style='color:#fff; background-color:#0e52a0;font-size: 14px; height: 20px;'>PRESELECCIÓN DE EMPRESAS</td></tr></thead>";
    echo "<tbody>";
    echo "<tr class='tab_bg_1'>";
        echo "<td class='campos_busqueda' >Tipo</td>";
        echo "<td colspan='1' >";
            echo "<div id='IdTipoEspecialidad'>";
                Dropdown::show('PluginComproveedoresRoltype', $opt_tipo);              
            echo "</div>";
        echo "</td>";
        echo "<td></td>";
        echo "<td class='campos_busqueda' >" . __('Especialidades') . "</td>";
        echo "<td rowspan='4' style='vertical-align: top; padding-left: 10px;'>";
                echo "<div id='IdEspecialidades' class='list-especialidades'>";
                echo "---";
                echo "</div>";
        echo "</td>";                                    
        echo "<td class='campos_busqueda' >" . __('Ámbitos') . "</td>";
        echo "<td rowspan='4' style='vertical-align: top;'>";
                echo "<div id='IdAmbitos' style='width: 200px;' class='list-ambitos'>";
                echo "---";
                echo "</div>";
        echo "</td>";                                   
    echo "</tr>";             
    echo "<tr class='tab_bg_1'>";
        echo "<td class='campos_busqueda' >Categorías</td>";
        echo "<td colspan='6' >";
                echo "<div id='IdCategorias'>";
                    Dropdown::show('PluginComproveedoresCategory',$opt_categoria);              
                echo "</div>";
        echo "</td>";
    echo "</tr>";

    echo "<tr class='tab_bg_1 left' style='background-color:#FFF; border: 20px solid #BDBDDB;'>";
        echo "<td class='campos_busqueda' colspan='2'>Facturación último ejercicio >= ";
        echo "<input id='minima' type='text' value='0' style='width: 100px;'/> €";
        echo "</td>";
    echo "</tr>";		

    /**
    echo "<tr class='tab_bg_1 left' style='background-color:#FFF; border: 20px solid #BDBDDB;'>";
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
    **/

    echo "</tbody>";
    echo "<tfoot>";
    echo "<tr class='tab_bg_1 center' style='background-color:#FFF; border: 20px solid #BDBDDB;'>";
        echo "<td colspan='7' class='center' style='border-top:1px solid #f3f3f3;'>";
                //echo "get->{$idtipo} {$idcategoria} {$idespecialidad} {$ambitos} {$preseleccion} ";
                //echo "<input id='anterior' type='submit' class='boton_anterior' name='searchAnterior' value='' title='ANTERIOR (Alt+A)' accesskey='A' style='margin-right: 10px;'/>";
                echo "<input id='buscar' type='submit' class='boton_buscar' name='buscar' value='' title='BUSCAR (Alt+B)' accesskey='B' style='margin-right: 10px;'/>";
                echo "<input id='limpieza' type='submit' class='boton_limpieza' name='limpieza' value='' title='LIMPIAR (Alt+L)' accesskey='L' style='margin-right: 10px;'/>";
                echo "<input id='siguiente' type='submit' class='boton_siguiente' name='siguiente' value='' title='SIGUIENTE (Alt+S)' accesskey='S'/>";
        echo "</td>";
        //echo "<td></td>";
    echo "</tr>";
    echo "</tfoot>";

echo "</table>";
//echo "<div id='progressbar'><div class='progress-label'>Cargando...</div></div>";
//echo "preseleccionados = ".$strProveedores;
echo "<div id='resultado' style='width: 100%;padding:0;border: 1px solid #ccc; height: 400px; background-color:#e6e6e6; text-align: center;'>";

echo "</div>";

echo $js;