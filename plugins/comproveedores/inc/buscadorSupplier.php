<?php

GLOBAL $DB,$CFG_GLPI;
$objCommonDBT=new CommonDBTM;

//PROJECT
$opt_project['comments']=false;
$opt_project['addicon']=false;
$opt_project['width']='400px';
	
	
echo "<div style='position:relative; width:100%; margin: 0px auto 30px auto;'>";
echo "<table class='tab_cadre_fixe' style='width:35%;border-radius: 4px !important; box-shadow: 0px 1px 2px 1px #D2D2D2'>";
echo "<thead>";
    echo "<tr>";
        echo "<th colspan='9' class='titulo_tabla' >BÚSQUEDA DE PROVEEDORES</th>";
    echo"</tr>";
echo"</thead>";
echo "<tbody style='padding:4px !important;'>";
echo "<td rowspan='6' style='text-align: center;vertical-align: top;padding: 30px 4px 20px 1px;'><img id='imgProveedor' src='../pics/boton_proveedor_grande.png' style='height:8em;'/></td>";                        
    echo "<tr class='tab_bg_1 left' style='background-color:#FFF; border: 20px solid #BDBDDB;'>";
            echo "<td class='campos_busqueda' >" . __('Proveedor') . "</td>";
            echo "<td>";
            Html::autocompletionTextField($objCommonDBT,'nombre_proveedor');
            echo "</td>";
    echo "</tr>";

    echo"<tr class='tab_bg_1 left' style='background-color:#FFF; border: 20px solid #BDBDDB;'>";
            echo "<td class='campos_busqueda'>" . __('CIF') . "</td>";
            echo "<td>";
            Html::autocompletionTextField($objCommonDBT,'cif');
            echo "</td>";
    echo "</tr>";

    echo "<tr class='tab_bg_1 left' style='background-color:#FFF; border: 20px solid #BDBDDB;'>";
            echo "<td class='campos_busqueda'>" . __('Proyecto') . "</td>";
            echo "<td>";
            echo "<div id='IdProyecto'>";				
            Dropdown::show('Project', $opt_project); 
            echo "</div>";
            echo "</td>";				
            //Html::autocompletionTextField($objCommonDBT,'nombre_proyecto');
    echo "</tr>";

    echo "<tr class='tab_bg_1 left' style='padding-bottom: 20px; background-color:#FFF; border: 20px solid #BDBDDB;'>";
        echo "<td class='campos_busqueda'>" . __('Código del Proyecto') . "</td>";
        echo "<td>";
        Html::autocompletionTextField($objCommonDBT,'codigo_proyecto');
        echo "</td>";
    echo "</tr>";
    echo "<tr class='tab_bg_1 left' style='background-color:#FFF; border: 20px solid #BDBDDB;'>";
        echo "<td colspan='2' class='campos_busqueda'></td>";
    echo "</tr>";			
    echo "</tbody>";
    echo "<tr class='tab_bg_1 center' style='background-color:#FFF;'>";
        echo "<td colspan='4' style='border-top: 1px solid #f3f3f3;'>";
            echo "<input id='botonBuscar' title='BUSCAR 1 (Alt+B)' type='submit' name='search' value='' class='boton_buscar' accesskey='B'/>";
            echo "<input id='limpieza' type='submit' class='boton_limpieza' name='limpieza' value='' title='LIMPIAR (Alt+L)' accesskey='L' style='margin-left: 50px;'/>";
        echo "</td>";
    echo "</tr>";		
echo "</table>";
echo "</div>";

    $profileID = 0;
    $USERID = $_SESSION['glpiID'];
    $query0 = "SELECT profiles_id as profile FROM glpi_users WHERE id=$USERID";
    $result0 = $DB->query($query0);
    $aux0 = $DB->fetch_array($result0);
    if($aux0['profile']<>''){
        $profileID = $aux0['profile'];
    }
    $limite = 0;
    if(in_array($profileID, array(3,4,16))){
        $limite = 0;
    }else{
        $limite = 1;
    }

    
echo "<div id='divlist' style='width: 100%;border: 1px solid #ccc;min-height: 300px; background-color:#e6e6e6;'></div>";

echo "
<script type='text/javascript'>
        
        $('#botonBuscar').on('click', function () {
            buscar();
        });			
        $('#limpieza').on('click', function() {
            limpiar();
        });
        
        function limpiar() {
            $('[id^=\'textfield_nombre_proveedor\']').val('');
            $('[id^=\'textfield_cif\']').val('');
            $('input[name=projects_id]').val(0);
            $('input[name=projects_id]').change();
            $('[id^=\'textfield_codigo_proyecto\']').val('');
        }
        
        function buscar() {
            var fldnombre_proveedor = $('[id^=\'textfield_nombre_proveedor\']').val();
            var fldcif = $('[id^=\'textfield_cif\']').val();
            var fldid_proyecto = $('input[name=projects_id]').val();
            var fldcodigo_proyecto = $('[id^=\'textfield_codigo_proyecto\']').val();

            if(({$limite} == 1 && fldnombre_proveedor.length <3 && fldcif == '' && (fldid_proyecto == 0 || flid_proyecto == '') && fldcodigo_proyecto == '')){
                alert('Debe introducir mas información para poder realizar la búsqueda');
            }else{
                var parametros = {'nombre_proveedor': fldnombre_proveedor, 
                            'cif': fldcif, 
                            'id_proyecto': fldid_proyecto, 
                            'codigo_proyecto': fldcodigo_proyecto};
                $.ajax({  
                    type: 'GET',        	
                    async: false, 
                    url: '".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/listSupplier.php',
                    data: parametros,
                    success: function(data){
                        $('#divlist').html(data);
                    },
                    error: function(result) { alert('Data not found'); }
                });      
            }
        };
        


</script>
";
