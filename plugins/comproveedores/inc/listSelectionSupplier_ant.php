<?php
include_once ("../../../inc/includes.php");
//use Glpi\Event;
GLOBAL $DB,$CFG_GLPI;
$objCommonDBT=new CommonDBTM;


//obtenemos el id del contrato, si es en visualizar preselección o si es en elegir proveedor
$contrato_id = "";
$where = "";
$TieneEspecialidades = 0;
$TieneAmbitos = 0;
$TieneExperiencias = 0;
$fase = $_GET['fase'];

if(!$_GET['Proveedores']){
    $strProveedores = '';
}else{
    $strProveedores = $_GET['Proveedores'];
}

if(!empty($preseleccion)){
    $contrato_id=$_GET['id'];
}else{
    if($_GET['paquete_id']){
        $contrato_id=$_GET['paquete_id'];
    }else{
        if($_GET['projecttasks_id']){
            $contrato_id=$_GET['projecttasks_id'];
        }else{$contrato_id='0';}
    }
}
$intervencion = 0;
if(isset($_GET['intervencion'])){
    $intervencion = $_GET['intervencion'];
}


echo "<script type='text/javascript'>


        $('#checkAll').on('click', function() {
            if ($('#checkAll').prop('checked') == false) {
                $('.clsIncorpora').prop('checked', false);
            }else{
                $('.clsIncorpora').prop('checked', true);
            }
        });

        $('#incorporarSeleccionados').on('click', function() {

            var n = $('#nombre_lic').val();
            var c = $('#cif_lic').val();	
            var s = $('.clsIncorpora');
            var suppliers_id = '';
            var contrato=$('#identificador').val();
            var i = 0;
            $('.clsIncorpora:checked').each(function() {
                if(i == 0) {
                    suppliers_id = $(this).attr('id').replace('proveedor_', '');
                }else{
                    suppliers_id = suppliers_id + ',' + $(this).attr('id').replace('proveedor_', '');
                }
                i = i + 1;
            });
            if(i > 0){
                $.ajax({ 
                    async: false, 
                    type: 'GET',
                    data: {'projecttasks_id':contrato, 'nombre_lic':n, 'cif_lic':c, 'supplier_id':suppliers_id},                  
                    url: '".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/listLicitadores.php',  				
                    success:function(data){
                            location.reload();
                        },
                        error: function(result) {
                            alert('Data not found: ".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/listLicitadores.php');
                        }
                });       
            }else{
                alert('No ha seleccionado ningún proveedor,<br> antes de continuar debe seleccionar algún proveedor<br>para acceder a la siguiente fase de preselección.');
            }

        });      

        $('.control-expandir').on('click', function() {
            
            var id = $(this).attr('id').replace('expand_','');
            var idoculto = '#filaEspecialidad_'+id;
            var idocultoexpand = '#ocultoExpand_'+id;
            if($(idocultoexpand).length>0){
                var contenido = $(idocultoexpand).val();
                if(contenido=='0'){
                    $(this).css('background-image', 'url(../pics/collapse.png)');  
                    $(idocultoexpand).val('1');
                    $(idoculto).css('display', '');
                }else{
                    $(this).css('background-image', 'url(../pics/expand.png)');
                    $(idocultoexpand).val('0');
                    $(idoculto).css('display', 'none');
                }
            }else{
                alert('no entra');
            }
            
        }); 
        
        $('.boton_quitar').on('click', function() {
            var id = $(this).attr('id').replace('quitar_','');
            var aux = '#filaSeleccion_'+id;
            $(aux).remove();
        });

	$(document).ready(function() {
            jQuery.extend( jQuery.fn.dataTableExt.oSort, {
                'currency-pre': function ( a ) {
                    a = (a==='-') ? 0 : a.replace( /[^\d\-\.]/g, '' );
                    return parseFloat( a );
                },

                'currency-asc': function ( a, b ) {
                    return a - b;
                },

                'currency-desc': function ( a, b ) {
                    return b - a;
                }
            });               
            if({$fase}==1){
                $('#tablaFiltros').DataTable({
                    'searching':      true,
                    'scrollY':        '200px',
                    'scrollCollapse': true,
                    'ordering':       true,
                    'order': [[2, 'asc']],
                    'paging':         false,
                    'language': {'url': '".$CFG_GLPI["root_doc"]."/lib/jqueryplugins/Spanish.json'},
                    'columns': [
                        null,
                        null,
                        null,
                        null,
                        null,
                        {'type': 'currency'},                    
                        null,
                        null,
                        null
                    ],
                    'dom': 'Bfrtip',
                            'buttons': [
                                'copyHtml5',
                                'excelHtml5',
                                'pdfHtml5'
                            ]
                });
            }else{
                $('#tablaFiltros').DataTable({
                    'searching':      true,
                    'scrollY':        '200px',
                    'scrollCollapse': true,
                    'ordering':       true,
                    'order': [[2, 'asc']],
                    'paging':         false,
                    'language': {'url': '".$CFG_GLPI["root_doc"]."/lib/jqueryplugins/Spanish.json'},
                    'columns': [
                        null,
                        null,
                        {'type': 'currency'},
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null
                    ],
                    'dom': 'Bfrtip',
                            'buttons': [
                                'copyHtml5',
                                'excelHtml5',
                                'pdfHtml5'
                            ]
                });            
            }
            $('.especialidades').css('display', 'none');

	});
        
</script>";



////////////////////////////////Preselección
//where para la Preselección
if(!empty($preseleccion)){
     $where = " p.id in(".$preseleccion.")";
}
/////////////////////////////////

/////////////////////////////////SelectionsupplierF1
//Comprobamos que se ha enviado algún filtro de busqueda
//$filtrar=false;

//foreach ($_GET as $value) {
//         if($value!=''){
//                $filtrar=true;
//         }
//}
//Si hay algún filtro y viene de la página selectionsupplierF1 que ponga el where en la consulta
// if($filtrar && $_GET['PrimerFiltro']){
//        $where  .=  " where ";
//}  
///////////////////////////////////

//Añadimos los filtros al where de la consulta
if((isset($_GET['Proveedores'])) && ($_GET['Proveedores']!='')){
    if($where!=''){
        $where .= " And ";
    }
    $where .= "p.id in (".$_GET['Proveedores'].")";
}
if(isset($_GET['nombre_proveedor'])){
    if($where!=''){
        $where .= " And ";
    }
    $where .= "UPPER(p.name) LIKE UPPER('%".$_GET['nombre_proveedor']."%')";
}
if(isset($_GET['idtipo'])){
    if ($_GET['idtipo']>0) {
        if($where!=''){
            $where .= " And ";
        }
        $where .= "le.plugin_comproveedores_roltypes_id=".$_GET['idtipo'];
        $TieneEspecialidades = 1;
    }
 }
if(isset($_GET['idcategoria'])){
    if ($_GET['idcategoria']>0) {
        if($where!=''){
            $where .= " And ";
        }            
        $where .= "le.plugin_comproveedores_categories_id=".$_GET['idcategoria'];
        $TieneEspecialidades = 1;
    }
 }
if(isset($_GET['idespecialidad'])){
    if ($_GET['idespecialidad']>0) {	
        if($where!=''){
            $where .= " And ";
        }
        $where .= "le.plugin_comproveedores_specialties_id in (".$_GET['idespecialidad'].")";
        $TieneEspecialidades = 1;
    }
 }
if(isset($_GET['minima'])){
    if($where!=''){
        $where .= " And ";
    }    
    $where .= "facturacion(p.id)>=".$_GET['minima'];
 } 
if(isset($_GET['maxima'])){
    if($where!=''){
        $where .= " And ";
    }    
    $where .= "facturacion(p.id)<=".$_GET['maxima'];
 }
if(isset($_GET['idregion']) && $_GET['idregion']!=0){
    if($where!=''){
        $where .= " And ";
    }    
    $where .= "p.plugin_comproveedores_communities_id=".$_GET['idregion'];
 }  
if(isset($_GET['idprovincia']) && $_GET['idprovincia']!=0 ){
    if($where!=''){
        $where .= " And ";
    }    
    $where .= "p.plugin_comproveedores_provinces_id=".$_GET['idprovincia'];
 } 
//if(isset($_GET['bim']) && $_GET['bim']!=0 ){
//    if($where!=''){
//        $where .= " And ";
//    }    
//    $where .= "experiencia_BIM(p.id)=".$_GET['bim'];
// }  
//if(isset($_GET['leed']) && $_GET['leed']!=0 ){
//    if($where!=''){
//        $where .= " And ";
//    }    
//    $where .= "experiencia_LEED(p.id)=".$_GET['leed'];
// }   
//if(isset($_GET['breeam']) && $_GET['breeam']!=0 ){
//    if($where!=''){
//        $where .= " And ";
//    }    
//    $where .= "experiencia_BREEAM(p.id)=".$_GET['breeam'];
// }   
//if(isset($_GET['otros']) && $_GET['otros']!=0 ){
//    if($where!=''){
//        $where .= " And ";
//    }    
//    $where .= "experiencia_OTROS(p.id)=".$_GET['otros'];
// }   
if(isset($_GET['intervencion']) && $_GET['intervencion']!=0 ){
    if($where!=''){
        $where .= " And ";
    }    
    $where .= "experiencia_BOVIS(p.id)=".$_GET['bovis'];
 }   
if(isset($_GET['strExperiencias']) && $_GET['strExperiencias']!='' ){
//    if($where!=''){
//        $where .= " And ";
//    } 
//    $where .= " e.plugin_comproveedores_experiencestypes_id IN (".$_GET['idexperiencias'].")";
    $strExperiencias = $_GET['strExperiencias'];
    $arrayExperiencias = explode(',',$strExperiencias);
    $TieneExperiencias = 1;
    
    //$where .= "find_in_set(tiposdeexperiencias(p.id),".$_GET['idexperiencias'].")";
 }  
if(isset($_GET['ambitos']) && $_GET['ambitos']!='' ){
    if($where!=''){
        $where .= " And ";
    } 
    $where .= " l.plugin_comproveedores_ambitos_id IN (".$_GET['ambitos'].") ";
    $TieneAmbitos = 1;
 } 
 //Eliminamos el ultimo and y ordenamos por proveedor,
 //en el caso de que venga de la página selectionsupplierF1, sino estamos es que estamos en la preselección y no hay and en el where
 /**if($_GET['PrimerFiltro']){
     $posicion= strripos($where, ' and');
     $where = substr($where, 0, $posicion);
 }**/
 
if($where!=""){
    $where = " WHERE p.is_deleted=0 And ".$where;
}
$orderby = " ORDER BY p.name desc ";

//Creamos la consulta y añadimos el where a la consulta
if($fase == 1){
$strquery = "SELECT distinct p.id as supplier_id,
            p.name as proveedor, 
            categorias(p.id) as categoria,            
            especialidades(p.id) as especialidad,
            cv.id as cv_id,
            facturacion(p.id) as facturacion, 
            calidad(p.id) as calidad, 
            planificacion(p.id) as planificacion, 
            costes(p.id) as costes, 
            cultura_empresarial(p.id) as cultura_empresarial, 
            gestion_de_suministros_y_subcontratistas(p.id) as gestion_de_suministros_y_subcontratistas, 
            seguridad_y_salud_y_medioambiente(p.id) as seguridad_y_salud_y_medioambiente, bim(p.id) as bim, 
            certificacion_medioambiental(p.id) as certificacion_medioambiental, 
            proyecto_basico(p.id) as proyecto_basico, 
            proyecto_de_ejecucion(p.id) as proyecto_de_ejecucion, 
            capacidad_de_la_empresa(p.id) as capacidad_de_la_empresa, 
            colaboradores(p.id) as colaboradores, 
            capacidad(p.id) as capacidad, 
            actitud(p.id) as actitud,
            experiencia_BIM(p.id) as experiencia_BIM,
            experiencia_BREEAM(p.id) as experiencia_BREEAM,
            experiencia_LEED(p.id) as experiencia_LEED,
            experiencia_OTROS(p.id) as experiencia_OTROS            
FROM glpi_suppliers as p
    left join glpi_plugin_comproveedores_cvs as cv on cv.id = p.cv_id and cv.is_deleted=0 ";
}else{
    
$strquery = "SELECT distinct p.id as supplier_id,
            p.name as proveedor, 
            cv.id as cv_id,
            facturacion(p.id) as facturacion, 
            calidad(p.id) as calidad, 
            planificacion(p.id) as planificacion, 
            costes(p.id) as costes, 
            cultura_empresarial(p.id) as cultura_empresarial, 
            gestion_de_suministros_y_subcontratistas(p.id) as gestion_de_suministros_y_subcontratistas, 
            seguridad_y_salud_y_medioambiente(p.id) as seguridad_y_salud_y_medioambiente, bim(p.id) as bim, 
            certificacion_medioambiental(p.id) as certificacion_medioambiental, 
            proyecto_basico(p.id) as proyecto_basico, 
            proyecto_de_ejecucion(p.id) as proyecto_de_ejecucion, 
            capacidad_de_la_empresa(p.id) as capacidad_de_la_empresa, 
            colaboradores(p.id) as colaboradores, 
            capacidad(p.id) as capacidad, 
            actitud(p.id) as actitud,
            experiencia_BOVIS(p.id) as experiencia_BOVIS,
            experiencia_BIM(p.id) as experiencia_BIM,
            experiencia_BREEAM(p.id) as experiencia_BREEAM,
            experiencia_LEED(p.id) as experiencia_LEED,
            experiencia_OTROS(p.id) as experiencia_OTROS,
            tiposExperienciaId(cv.id) as tiposExperienciaId 
FROM glpi_suppliers as p
    left join glpi_plugin_comproveedores_cvs as cv on cv.id = p.cv_id and cv.is_deleted=0 ";    
}
if($TieneEspecialidades ==  1 ){
    $strquery .= "left join glpi_plugin_comproveedores_listspecialties as le on le.cv_id = cv.id ";
}
if($TieneAmbitos == 1){
    $strquery .= "left join glpi_plugin_comproveedores_listambitos as l on l.cv_id = p.cv_id ";
}
//if($TieneExperiencias ==  1 ){
//    $strquery .= "left join glpi_plugin_comproveedores_experiences as e on e.cv_id=p.cv_id ";
//}
$sql = $strquery." ".$where." ".$orderby;
$result = $DB->query($sql);
//echo $sql."<br> preselecionados = ".$_GET['Proveedores'];
echo $sql;

if($_GET['fase']<8){
   $html = "FASE {$_GET['fase']}";
   $html.="<input id='incorporarSeleccionados' type='submit' class='boton_add' value=' ' title='INCORPORAR LICITADORES SELECCIONADOS' style='float:left;'>";
   $html.="<table id='tablaFiltros' class='display compact dataTable no-footer'>";
   $html.="<thead>";
//            $html.="<th colspan='9'>EVALUACIÓN DE CONTRATISTAS Y PROVEEDORES</th>";
//            $html.="<th colspan='7'>EVALUACIÓN DE SERVICIOS PROFESIONALES</th>";
	$html.="<tr>";
                $visualizar_cabecera=true;

                if($visualizar_cabecera){                               
                    //solo visualizamos 1 vez la cabecera
                    $visualizar_cabecera=false;

                    //Eliminación al visualizar la preselección
                    if(empty($preseleccion)){
                            if($fase==1){$html.="<th><input id='checkAll' type='checkbox'/></th>";}
                    }
                    //$html.= "<th style='border:none;'>Sel.</th>";
                    $html.= "<th title='QUITAR'>quit</th>";                       
                    $html.= "<th>PROVEEDOR</th>";
                    if($fase==1){$html.= "<th>CATEGORÍAS</th>";}
                    if($fase==1){$html.= "<th>ESPECIALIDADES</th>";}
                    $html.= "<th>FACTURACIÓN (€)</th>";                    
                    $html.= "<th>CV</th>";
                    $html.= "<th>Experiencia con BOVIS</th>";
                    //if($_GET['tipo_especialidad']<3){
                    if($_GET['fase']==2){
                        $html.= "<th title='CALIDAD'>Q</th>";
                        $html.= "<th title='PLANIFICACIÓN'>PLZ</th>";
                        $html.= "<th title='COSTES'>COST</th>";
                        $html.= "<th title='CULTURA EMPRESARIAL'>CULT</th>";
                        $html.= "<th title='GESTIÓN DE SUMINISTROS Y SUBCONTRATISTAS'>SUBC</th>";
                        $html.= "<th title='SEGURIDAD Y SALUD'>SyS</th>";
                        $html.= "<th title='CERTIFICADOS CALIDAD Y MEDIO AMBIENTE'>CERT</th>";
                        $html.= "<th title='BIM'>BIM</th>";
                    }
                    $html.= "<th title='NOTA'>NOTA EVAL.</th>";
                    //}else{  
//                    $html.= "<th title='BIM' style='border:none;'>&nbsp;BIM&nbsp;&nbsp;</th>";
//                    $html.= "<th title='PROYECTOS BÁSICOS' style='border:none;'>PR BAS</th>";
//                    $html.= "<th title='PROYECTOS EJECUTADOS' style='border:none;'>PR EJE</th>";
//                    $html.= "<th title='COLABORADORES' style='border:none;'>COLAB&nbsp;</th>";
//                    $html.= "<th title='CAPACIDAD DE EMPRESA' style='border:none;'>CAP EM</th>";                            
//                    $html.= "<th title='CAPACIDAD' style='border:none;'>&nbsp;CAP&nbsp;&nbsp;</th>";
//                    $html.= "<th title='ACTITUD' style='border:none;'>&nbsp;ACT&nbsp;&nbsp;</th>";                        
                   //}
                }
                $html.="</tr>";
                $html.="</thead>";
                $html.="<tbody>";
                $nota = 0;
                $num  = 0;
                
                while ($data=$DB->fetch_array($result)) {		
                    
                    $arrayE = explode(',',$data['tiposExperienciaId']);
                    $arrayComp = array_diff( $arrayExperiencias, $arrayE);
                    
                    if(count($arrayComp) <= 0 && strlen($data['tiposExperienciaId'])>=0){
                        
                        //Añadimos los id de los proveedores para la preselección
                        $preselecionIds=$preselecionIds.$data["supplier_id"]."-";

                        $html.="<tr id='filaSeleccion_{$data["supplier_id"]}'>";

                        //Eliminación al visualizar la preselección  border: 1px solid #BDBDDB;
                        if(empty($preseleccion) && ($fase == 1)){
                           $html.="<td style='text-align: left;'>";
                                $html.="<input id='proveedor_".$data["supplier_id"]."' class='clsIncorpora' type='checkbox'/>";
                           $html.="</td>";					   
                        } 
                        $html.="<td style='vertical-align: middle; text-align: center;'>";
                            $html.="<input id='quitar_{$data["supplier_id"]}' type='submit' class='boton_quitar' value='' title='QUITAR DE LA SELECCIÓN' style='width: 18px; height: 18px;'/>";
                        $html.="</td>";                    
                        $html.="<td class='left'>";
                        //.$strExperiencias." // ".$data['tiposExperienciaId']." 
                            $html.="<a href='".$CFG_GLPI["root_doc"]."/front/supplier.form.php?id=".$data["supplier_id"]."'>".$data["proveedor"]."</a>";
                        $html.="</td>";                    
                        if($fase==1){$html.="<td style='text-align:left; font-size:7px;'>".$data['categoria']." </td>";     }
                        if($fase==1){$html.="<td style='text-align:left; font-size:7px;'>".$data['especialidad']." </td>";     }
                        $strfact = number_format($data['facturacion'], 2, ',', '.');
                        $html.="<td style='text-align:center;'>".$strfact."</td>";    
                        
                        //TIENE CV??
                        if(!empty($data['cv_id'])){
                            $html.="<td style='vertical-align:middle; text-align:center;'>";
                            $html.="<image style='width:15px;' src='".$CFG_GLPI["root_doc"]."/pics/CHECK.png' />";
                            $html.="</td>";
                        }else{
                            $html.="<td style='text-align:center;'>";
                            $html.="<image style='width:15px;' src='".$CFG_GLPI["root_doc"]."/pics/CHECK_no.png' />";
                            $html.="</td>";
                        }
                        
                        //EXPERIENCIA CON BOVIS
                        if(!empty($data['experiencia_BOVIS'])){
                            $html.="<td style='vertical-align:middle;text-align:center;'>";
                            $html.="<image style='width:15px;' src='".$CFG_GLPI["root_doc"]."/pics/CHECK.png' />";
                            $html.="</td>";
                        }else{
                            $html.="<td style='text-align:center;'>";
                            $html.="<image style='width:15px;' src='".$CFG_GLPI["root_doc"]."/pics/CHECK_no.png' />";
                            $html.="</td>";
                        }
                        
                        ////////Criterios Contratistas y PROVEEDORES///////
                        //if($_GET['tipo_especialidad']<3){
                        if(!empty($data['calidad'])){
                            if($_GET['fase']==2){
                                $html.="<td class='semaforo' title='{$data['calidad']}' style='background-color: #d5e6fb; background-size: 20px;background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".getColorValoracion($data['calidad']).".png);'> </td>";
                            }
                            $num = $num + 1;
                            $nota = $nota + $data['calidad'];
                        }else{
                            if($_GET['fase']==2){
                            $html.="<td style='background-color: #d5e6fb;'> - </td>";
                            }
                        }
                        if(!empty($data['planificacion'])){
                            if($_GET['fase']==2){
                                $html.="<td class='semaforo' title='{$data['planificacion']}' style='background-color: #d5e6fb;background-size: 20px;background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".getColorValoracion($data['planificacion']).".png);'> </td>";
                            }
                            $num = $num + 1;
                            $nota = $nota + $data['planificacion'];                        
                        }
                        else{
                            if($_GET['fase']==2){
                            $html.="<td style='background-color: #d5e6fb;'> - </td>";
                            }
                        }
                         if(!empty($data['costes'])){
                            if($_GET['fase']==2){
                                $html.="<td class='semaforo' title='{$data['costes']}' style='background-color: #d5e6fb;background-size: 20px;background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".getColorValoracion($data['costes']).".png);'> </td>";
                            }
                            $num = $num + 1;
                            $nota = $nota + $data['costes'];                        
                        }else{
                            if($_GET['fase']==2){
                            $html.="<td style='background-color: #d5e6fb;'> - </td>";
                            }
                        }
                         if(!empty($data['cultura_empresarial'])){
                            if($_GET['fase']==2){
                                $html.="<td  class='semaforo' title='{$data['cultura_empresarial']}' style='background-color: #d5e6fb;background-size: 20px;background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".getColorValoracion($data['cultura_empresarial']).".png); '> </td>";
                            }
                            $num = $num + 1;
                            $nota = $nota + $data['cultura_empresarial'];                        
                        }else{
                            if($_GET['fase']==2){
                            $html.="<td style='background-color: #d5e6fb;'> - </td>";
                            }
                        }
                         if(!empty($data['gestion_de_suministros_y_subcontratistas'])){
                            if($_GET['fase']==2){
                                $html.="<td  class='semaforo'  title='{$data['gestion_de_suministros_y_subcontratistas']}' style='background-color: #d5e6fb; background-size: 20px;background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".getColorValoracion($data['gestion_de_suministros_y_subcontratistas']).".png); '> </td>";
                            }
                            $num = $num + 1;
                            $nota = $nota + $data['gestion_de_suministros_y_subcontratistas'];                        
                        }else{
                            if($_GET['fase']==2){
                            $html.="<td style='background-color: #d5e6fb;'> - </td>";
                            }
                        }
                        if(!empty($data['seguridad_y_salud_y_medioambiente'])){
                            if($_GET['fase']==2){
                                $html.="<td  class='semaforo'  title='{$data['seguridad_y_salud_y_medioambiente']}' style='background-color: #d5e6fb; background-size: 20px;background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".getColorValoracion($data['seguridad_y_salud_y_medioambiente']).".png); '> </td>";
                            }
                            $num = $num + 1;
                            $nota = $nota + $data['seguridad_y_salud_y_medioambiente'];                        
                        }else{
                            if($_GET['fase']==2){
                            $html.="<td style='background-color: #d5e6fb;'> - </td>";
                            }
                        }
                        if(!empty($data['certificacion_medioambiental'])){
                            if($_GET['fase']==2){
                                $html.="<td  class='semaforo'  title='{$data['certificacion_medioambiental']}' style='background-color: #d5e6fb; background-size: 20px;background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".getColorValoracion($data['certificacion_medioambiental']).".png); '> </td>";
                            }
                            $num = $num + 1;
                            $nota = $nota + $data['certificacion_medioambiental'];                        
                        }else{
                            if($_GET['fase']==2){
                            $html.="<td style='background-color: #d5e6fb;'> - </td>";
                            }
                        }                
                        //if(!empty($data['bim']) && !empty($data['certificacion_medioambiental'])){
                        if(!empty($data['bim'])){
                            if($_GET['fase']==2){
                                $html.="<td class='semaforo'  title='{$data['bim']}' style='background-color: #d5e6fb; background-size: 20px;background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".getColorValoracion($data['bim']).".png); '> </td>";
                            }
                            $num = $num + 1;
                            $nota = $nota + $data['bim'];                        
                        }else{
                            if($_GET['fase']==2){
                            $html.="<td style='background-color: #d5e6fb;'> - </td>";
                            }
                        }     
                        if(!empty($data['nota'])){
                            $media = round($nota / $num, 0);
                            $html.="<td class='semaforo' style='background-color: #d5e6fb; background-size: 20px;background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".getColorValoracion($media).".png); '> </td>";
                        }else{
                            $html.="<td style='background-color: #d5e6fb;'> - </td>";
                        }                    
                ///////Criterios Servicios Profesionales y CONSULTORES ///////     
                //}else{
    //                    if(!empty($data['bim'])){
    //                        $html.="<td class='semaforo' style='background-size: 20px;background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".getColorValoracion($data['bim']).".png); '> </td>";
    //                    }
    //                    else{
    //                        $html.="<td></td>";
    //                    }                    
    //                    if(!empty($data['proyecto_basico'])){
    //                       $html.="<td class='semaforo' style='background-size: 20px;background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".getColorValoracion($data['proyecto_basico']).".png); '> </td>";
    //                    }
    //                    else{
    //                        $html.="<td></td>";
    //                    }
    //                    if(!empty($data['proyecto_de_ejecucion'])){
    //                        $html.="<td class='semaforo' style='background-size: 20px;background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".getColorValoracion($data['proyecto_de_ejecucion']).".png); '> </td>";
    //                    }
    //                    else{
    //                        $html.="<td></td>";
    //                    }
    //                     if(!empty($data['colaboradores'])){
    //                        $html.="<td class='semaforo' style='background-size: 20px;background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".getColorValoracion($data['colaboradores']).".png); '> </td>";
    //                    }
    //                    else{
    //                        $html.="<td></td>";
    //                    }
    //                   if(!empty($data['capacidad_de_la_empresa'])){
    //                        $html.="<td class='semaforo' style='background-size: 20px;background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".getColorValoracion($data['capacidad_de_la_empresa']).".png); '> </td>";
    //                    }
    //                    else{
    //                        $html.="<td></td>";
    //                    }                        
    //                    if(!empty($data['capacidad'])){
    //                        $html.="<td class='semaforo' style='background-size: 20px;background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".getColorValoracion($data['capacidad']).".png); '> </td>";
    //                    }
    //                    else{
    //                        $html.="<td> </td>";
    //                    }
    //                    if(!empty($data['actitud'])){
    //                        $html.="<td class='semaforo' style='background-size: 20px;background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".getColorValoracion($data['actitud']).".png); '> </td>";
    //                    }
    //                    else{
    //                        $html.="<td></td>";
    //                    }

                        $html.="</tr>";
                    
                    }
	}
 
	$html.="</tbody>";
	$html.="</table>";

//        echo "<div align='center'>";
//            echo "<table>";
//                echo "<tr>";
//                    echo "<td class='center'><img style='vertical-align:middle; margin: 5px 0px;' src=".$CFG_GLPI["root_doc"]."/pics/valoracion_1.png></td>";                                                            
//                    echo "<td  style='width: 50px;'>Calificación MALA</td>";
//                    echo "<td class='center'><img style='vertical-align:middle; margin: 5px 0px;' src=".$CFG_GLPI["root_doc"]."/pics/valoracion_2.png></td>";                                                            
//                    echo "<td  style='width: 50px;'>Calificación POBRE</td>";
//                    echo "<td class='center'><img style='vertical-align:middle; margin: 5px 0px;' src=".$CFG_GLPI["root_doc"]."/pics/valoracion_3.png></td>";                                                            
//                    echo "<td  style='width: 50px;'>Calificación ACEPTABLE</td>";
//                    echo "<td class='center'><img style='vertical-align:middle; margin: 5px 0px;' src=".$CFG_GLPI["root_doc"]."/pics/valoracion_4.png></td>";                                                            
//                    echo "<td  style='width: 50px;'>Calificación BUENA</td>";
//                    echo "<td class='center'><img style='vertical-align:middle; margin: 5px 0px;' src=".$CFG_GLPI["root_doc"]."/pics/valoracion_5.png></td>";                                                            
//                    echo "<td  style='width: 50px;'>Calificación EXCELENTE</td>";
//                echo "</tr>";
//            echo "</table>";
//        echo "</div>";
        echo $html;
}
        function getColorValoracion($valor){
	           
            switch ($valor) {
                case $valor<=1:
                    $color=1;
                    break;
                case $valor<=2 && $valor>1:
                    $color=2;
                   break;
                case $valor<=3 && $valor>2:
                    $color=3;
                    break;
                case $valor<=4 && $valor>3:
                    $color=4;
                    break;
                case $valor<=5 && $valor>4:
                    $color=5;
                    break;
                default:
                    $color=0;
                    break;
            }

            return $color;
        } 
