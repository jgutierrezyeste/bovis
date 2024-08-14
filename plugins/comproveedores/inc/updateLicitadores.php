   
            
<?php	

include ("../../../inc/includes.php");
GLOBAL $DB,$CFG_GLPI;
$objCommonDBT=new CommonDBTM;


if ($_GET['projecttasks_id']) {
    $projecttasks_id = $_GET['projecttasks_id'];
}else{
    $projecttasks_id=0;
}

$nombre_contrato_query ="SELECT t.name 
             
            
            FROM glpi_projecttasks as t
            
            WHERE t.id=$projecttasks_id" ; 
$nombre_contrato= $DB->query($nombre_contrato_query);


/*
if ($_GET['nombre_adjudicatario']) {
    $nombre_adjudicatario = $_GET['nombre_adjudicatario'];
}else{
    $nombre_adjudicatario = '';
}*/
$nombre_adjudicatario='';



if ($_GET['nombre_lic']) {
    $nombrelic = $_GET['nombre_lic'];
}else{
    $nombrelic = '';
}
if ($_GET['cif_lic']) {
    $ciflic = $_GET['cif_lic'];
}else{
    $ciflic = '';
}
if ($_GET['supplier_id']) {
    $supplier_id = $_GET['supplier_id'];
}else{
    $supplier_id = '0';
}

/*proveedor seleccionado como adjudicatario*/
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
            /*añadimos la condicion de que la empresa no se encuentre eliminada */
            WHERE t.projecttasks_id=$projecttasks_id and s.is_deleted= 0" ;

$result = $DB->query($query);

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
    echo "<input id='verLicitadoresModificar' type='hidden' value='0' />";
}else{
    $ver = true;
    echo "<input id='verLicitadoresModificar' type='hidden' value='1' />";
}  

$where = "";

//en el caso de incorporar la búsqueda de un licitador en concreto lo 
//buscamos y lo incorporamos a la tabla de preseleccionados en caso de que no existan ya
if ($nombrelic != '') {
    $where = " (s.name like '%".$nombrelic."%') " ;
}
if ($ciflic != '') {
    if ($where != "") { $where = $where." AND "; }
    $where = " (s.cif like '%".$ciflic."%') " ;
}	
if ($supplier_id != '' And $supplier_id != '0') {
    if ($where != "") { $where = $where." AND "; }
    $where = " (s.id in ({$supplier_id})) " ;
}	 

if ($where != "") { 
    $where = " WHERE ".$where; 
    $sql = "INSERT INTO glpi_plugin_comproveedores_preselections  (suppliers_id, projecttasks_id, is_deleted, is_recursive, entities_id)
    SELECT DISTINCT s.id, ".$projecttasks_id.", 0, 0, 0
    FROM glpi_suppliers as s ";

    $sql = $sql.$where; 

    $result = $DB->query($sql);
    //si obtenemos algo de esta consulta hay que incorporarlos a la tabla de preseleccionados
    //el usuario ya quitará los que no crea necesarios
} 


$queryPreseleccionados = "SELECT p.id as licitador_id,
suppliers_id,
s.name as licitador,
s.cif cif_nif,
p.projecttasks_id,
importe_ofertado,
calidad_oferta,
comentarios,
if(t.id>0, 1, 0) as adjudicatario

FROM glpi_plugin_comproveedores_preselections as p
INNER JOIN glpi_suppliers as s ON p.suppliers_id = s.id
LEFT join glpi_projecttaskteams as t on p.projecttasks_id = t.projecttasks_id and p.suppliers_id = t.items_id

WHERE p.projecttasks_id=".$projecttasks_id." AND s.is_deleted=0 AND p.is_deleted=0;";

$resultPreseleccionados = $DB->query($queryPreseleccionados);


//echo $queryPreseleccionados;

         
echo "<table style='width: 60%; margin-left: 40%; margin-top: 8px; padding: 5px; border-radius: 4px; background-color: #f8f7f3;  '>";
                    while ($contrato=$DB->fetch_array($nombre_contrato)){
                                    
                                     echo "<tr>
                                     
                                     <td><input type='text' style='color: #0e52a0; width: 400px; font-size: 18px; text-align: center;font-weight: bold; border:none; size: ".strlen($contrato['name']).";'  id='contratoModificar_adjudicatario' 
                                     value='".$contrato['name']."' readonly> ";
                                  
                                    echo "</td></tr>";
                                }
echo "</table>";
                                            
/*tabla superior que muestra el adjudicatario*/
echo "<table style='width: 60%; margin-left: 20%; margin-top: 8px; padding: 5px; border-radius: 4px; background-color: #f8f7f3;   -webkit-box-shadow: 12px 8px 5px -5px rgba(219,219,219,1);    -moz-box-shadow: 12px 8px 5px -5px rgba(219,219,219,1);    box-shadow: 12px 8px 5px -5px rgba(219,219,219,1);'>";
                                   
                                   
                                    while ($data=$DB->fetch_array($result)) {
                                            
                                            echo "<tr>";


                                                    echo "<td style='font-size: 16px; text-align: left;'>adjudicatario: <input id='nombreModificar_adjudicatario' type='text' value='".$data['name']."' readonly style='width: 400px; font-size: 16px; font-weight: bold; background-color: #f8f7f3; border:none;'/></td>";
                                                    echo "<td style='font-size: 16px; text-align: left; padding-left:4px;'>cif/nif:  <input id='cifModificar_adjudicatario' type='text' value='".$data['cif']."' readonly style='font-size: 16px; font-weight: bold; background-color: #f8f7f3; border:none;'/></td>";
                                            echo "</tr>";
                                            echo "<tr>";
                                                if($ver){
                                                    echo "<td style='font-size: 16px; text-align: left;'>importe de adjudicación: <input id='importeModificar_adjudicatario' type='text' value='".number_format($data['importe_ofertado'],2,',','.')." €' readonly style='font-size: 16px; font-weight: bold; background-color: #f8f7f3; border:none;'/></td>";
                                                    echo "<td style='font-size: 16px; text-align: left;'>calidad de la oferta: <input id='calidadModificar_adjudicatario' type='text' value='".$data['calidad_oferta']."' readonly style='font-size: 16px; font-weight: bold; background-color: #f8f7f3; border:none;'/></td>";
                                                }else{
                                                    echo "<td style='font-size: 16px; text-align: left;'>calidad de la oferta: <input id='calidadModificar_adjudicatario' type='text' value='".$data['calidad_oferta']."' readonly style='font-size: 16px; font-weight: bold; background-color: #f8f7f3; border:none;'/></td>";                                                    
                                                    echo "<td style='font-size: 16px; text-align: left;'></td>";
                                                }
                                                
                                            echo "</tr>";
                                            echo "<tr>";
                                                    echo "<td colspan='2' style='font-size: 16px; text-align: left;'>comentarios: <input id='comentariosModificar_adjudicatario' type='text' value='".$data['comentarios']."' readonly style='font-size: 16px; background-color: #f8f7f3; border:none; width: 600px;'/></td>";
                                            echo "</tr>";
                                    }
                                    echo "</table>";

/*hasta aqui tabla superior de datos del proveedor seleccionado*/
/*menu lateral*/
/**/


echo "<table id='tblLicitadores_Modificar' class='display compact' style='width:100%; float:left; position: relative;'>";
echo "<thead>";
echo "<tr>";

    echo "<th>licitador</th>";
    echo "<th>cif/nif</th>";
    if($ver){echo "<th>importe ofertado </th>";}
    echo "<th style='width: 100px;'>calidad oferta</th>";
    echo "<th>comentarios</th>";
    echo "<th style='width: 100px;'>adjudicatario</th>";
    if($ver && $nombre_adjudicatario == ''){
        echo "<th></th>";                
        echo "<th></th>";     
    }           
echo "</tr>";
echo "</thead>";
echo "<tbody>";
//if ($resultPreseleccionados->num_rows>0) {				
    while ($data=$DB->fetch_array($resultPreseleccionados)) {
        
        echo "<tr id='licitadormodificar_".$data['licitador_id']."' class='fila' >";
        echo "<td id='nombreModificar_".$data['licitador_id']."' class='left columna'>";
        echo  "<a href='supplier.form.php?id=".$data['suppliers_id']."'>".$data['licitador']."</a>";
        echo "</td>";
        echo "<td id='cifModificar_".$data['licitador_id']."' class='center columna'>";
        echo    $data['cif_nif'];
        echo "</td>";
        if($ver){
            if ($data['importe_ofertado']=="NULL"){
                echo "<td id='importeModificar_".$data['licitador_id']."' class='columna' style='text-align:center;'> ".' '." </td>";
            }else{
                echo "<td id='importeModificar_".$data['licitador_id']."' class='columna'>".number_format($data['importe_ofertado'],2,',','.')." </td>";
            }	
        }
        if (!$data['calidad_oferta']OR $data['calidad_oferta']=="NULL"){					
                echo "<td id='calidadModificar_".$data['licitador_id']."' class='columna' style='text-align:center; width: 100px;'>";
                echo    "<input id='ocultoModificar_calidad_".$data['licitador_id']."' value='".$data['calidad_oferta']."' type='hidden' />";
                echo "</td>";		
        }else{
                echo "<td id='calidadModificar_".$data['licitador_id']."' class='columna' style='font-size: 10px; color: #777; text-align:center; width: 100px;"
                        . "background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$data['calidad_oferta'].".png); background-repeat: no-repeat; background-size: 18px; background-position: center;'>";
                echo "<input id='ocultoModificar_calidad_".$data['licitador_id']."' value='".$data['calidad_oferta']."' type='hidden' />";
                echo $data['calidad_oferta'];
                //echo "<image title='puntuación = ".$data['calidad_oferta']."' src='' style='width:18px;height:18px;'/>";
                echo "</td>";								
        }
        echo "<td id='comentarioModificar_".$data['licitador_id']."' class='columna' style='text-align:left;'>".$data['comentarios']."</td>";
        
        echo "<td style='text-align:center; width: 100px;' class='columnaCHK' >";
            echo "<input type='hidden' id='supplierModificar_id_".$data['licitador_id']."' value='".$data['suppliers_id']."' />";
            
                
                    
                        echo "<input id='chkAdjudicatarioModificar_".$data['licitador_id']."' class='chkLic' type='checkbox'/>";                     
                    
            
            
        echo "</td>";
        if($ver && $nombre_adjudicatario == ''){ 
            echo "<td><input id='editarLicitadoresModificar_{$data['licitador_id']}' class='boton_editar_licitadores' type='submit' value='' title='editar elemento'/></td>";
            echo "<td><input id='quitarLicitadoresModificar_{$data['licitador_id']}' type='submit' value=' ' class='boton_borrar_licitadores' style='background-size:15px; width:20px; height:20px;border-radius:2px;'/></td>";
        }
        
        echo "</tr>";
    }	

//}else{
//    echo "<tr>";
//        echo "<td></td>";
//        echo "<td></td>";
//        echo "<td></td>";
//        echo "<td></td>";
//        echo "<td></td>";		
//        echo "<td></td>";
//        echo "<td></td>";	
//        if($ver && $nombre_adjudicatario != ''){ 
//            echo "<td></td>";
//            echo "<td></td>";			
//        }
//    echo "</tr>";
//}
echo "</tbody>";
echo "</table>";


echo "<div id='dialogoModificar' title='Edición de Licitador'>

    <input id='txt_projecttaskModificar_id' type='hidden' value='".$projecttasks_id."' />
    <input id='txt_suppliersModificar_id' type='hidden' value='' />
    <table style='width:90%;'>
    <tr style='height:40px;'>
        <td>nombre del licitador:</td><td><input type='text' id='txt_nombreModificar_lic' value='' style='width:300px;' readonly/></td>
    </tr>			
    <tr style='height:50px;'>
        <td>importe ofertado:</td><td><input id='txt_importeModificar_lic' type='text' value='' /> €</td>
    </tr>
    <tr style='height:50px;'>
        <td>comentarios:</td><td><textarea id='txt_comentariosModificar_lic' value='' cols='50' rows='4'/></td>
    </tr>			
    <tr style=''>
        <td>calidad de la oferta:</td><td><input id='txt_calidadModificar_lic' type='hidden' value='' /></td>
    </tr>
    <tr >
        <td colspan='2'>
                <image id='valModificar_1' class='valores' title='Valor = 1' style='padding:4px; cursor:pointer;' src='".$CFG_GLPI["root_doc"]."/pics/valoracion_1.png' />
                <image id='valModificar_2' class='valores' title='Valor = 2' style='padding:4px; cursor:pointer;' src='".$CFG_GLPI["root_doc"]."/pics/valoracion_2.png' />
                <image id='valModificar_3' class='valores' title='Valor = 3' style='padding:4px; cursor:pointer;' src='".$CFG_GLPI["root_doc"]."/pics/valoracion_3.png' />
                <image id='valModificar_4' class='valores' title='Valor = 4' style='padding:4px; cursor:pointer;' src='".$CFG_GLPI["root_doc"]."/pics/valoracion_4.png' />
                <image id='valModificar_5' class='valores' title='Valor = 5' style='padding:4px; cursor:pointer;' src='".$CFG_GLPI["root_doc"]."/pics/valoracion_5.png' />
        </td>
        <td><input id='valorModificar' type='text' value='0' size='1' readonly style='height: 35px; border:none; background-color: #FFF; font-size:30px; font-weight: bold;'/></td>
    </tr>
    </table>
</div>";

echo "<div id='dialogoBorradoModificar' title='Quitar Licitador'>
    <table>
    <tr>
        <td style='vertical-align:middle;'><img src='../pics/warning.png' style='margin-right: 10px;'/></td>
        <td style='vertical-align:middle; text-align: justify;'>¿Realmente desea quitar este licitador del proceso de selección?</td>
    </tr> 
    </table>
    <input id='quitarIdLicitadorModificar' type='hidden' value='' />
</div>";
echo "<div id='dialogoAdjudicatarioModificar' title='Adjudicar licitador'>
<table>
<tr>
<td>
    <image src='".$CFG_GLPI["root_doc"]."/pics/interrogacion.png' style='width:50px; hight:50px;'/>
</td>
<td style='padding:10px;'>
    ¿Realmente desea adjudicar este contrato a <label id='adjModificar'></label>?
</td>
<input id='adjudicarIdSupplierModificar' type='hidden' value='' />
<input id='adjudicarIdProjectTaskModificar' type='hidden' value='' />
</div>";			

echo "<script type='text/javascript'>
var ver = $('#verLicitadoresModificar').val();

if(ver == 1){
    $('#tblLicitadores_Modificar').DataTable({
            'searching':      true,
            'scrollY':        '300px',
            'scrollCollapse': true,
            'paging':         false,
            'language': {'url': '".$CFG_GLPI["root_doc"]."/lib/jqueryplugins/Spanish.json'},
            'dom': 'Bfrtip',
            'language': {
                'info': 'Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros',
                'search': 'Buscar:',
                'decimal': ',',
                'thousands': '.'
            },             
            'buttons': [
                'copyHtml5',
                'excelHtml5',
                'pdfHtml5'
            ]                      
    });
}else{
    $('#tblLicitadores_Modificar').DataTable({
            'searching':      true,
            'scrollY':        '300px',
            'scrollCollapse': true,
            'paging':         false,
            'language': {'url': '".$CFG_GLPI["root_doc"]."/lib/jqueryplugins/Spanish.json'}                    
    });        
}

$('#dialogoModificar').dialog({
autoOpen: false,
height: 370,
width: 520,
modal: true,
buttons: {
    'Aceptar': function() { 
        var idprojecttask = $('#txt_projecttaskModificar_id').val();
        var idlicitador = $('#txt_suppliersModificar_id').val();
        var importe = $('#txt_importeModificar_lic').val().replace(' ','').replace('€', '').replace('.','').replace(',','');
        var calidad = $('#valorModificar').val();
        var comentarios = $('#txt_comentariosModificar_lic').val();
        $.ajax({ 
                async: false, 
                type: 'GET',
                data: {'idlicitador':idlicitador,	
                       'idprojecttask':idprojecttask, 
                       'importe':importe, 
                       'calidad':calidad,
                       'comentarios':comentarios},                  
                url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/editarSupplierFromPreselection.php',  				
                success:function(data){
                        window.location.reload(true);
                },
                error: function(result) {
                        alert('Data not found: ".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/updateLicitadores.php');
                }
        });				

        $('#dialogoModificar').dialog('close');
    },
    'Cancelar': function() {
      $('#dialogoModificar').dialog('close');
    }
},
close: function() {
    $('#dialogoModificar').dialog('close');
}
});

$('#dialogoAdjudicatarioModificar').dialog({
autoOpen: false,
height: 200,
width: 250,
modal: true,
buttons: {
    'SI': function() { 
            var idsupplier = $('#adjudicarIdSupplierModificar').val();
            var idprojecttask = $('#adjudicarIdProjectTaskModificar').val();

            $.ajax({ 
                    async: false, 
                    type: 'GET',
                    data: {'idsupplier':idsupplier,	'idprojecttask':idprojecttask},                  
                    url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/actualizarSupplierFromPreselection.php',  				
                    success:function(data){
                            window.location.reload(true);
                    },
                    error: function(result) {
                            alert('Data not found: ".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/listLicitadores.php');
                    }
            });

            $('#dialogoAdjudicatarioModificar').dialog('close');},
    'NO': function() {
            $('#dialogoAdjudicatarioModificar').dialog('close');
    }
},
close: function() {
    $('#dialogoAdjudicatarioModificar').dialog('close');
}
});				

$('#dialogoBorradoModificar').dialog({
autoOpen: false,
height: 200,
width: 250,
modal: true,
buttons: {
    'SI': function() { 
        var idlicitador = $('#quitarIdLicitadorModificar').val();

        $.ajax({ 
            async: false, 
            type: 'GET',
            data: {'idlicitador': idlicitador},                  
            url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/quitarSupplierFromPreselection.php',  				
            success:function(data){
                    window.location.reload(true);
            },
            error: function(result) {
                    alert('Data not found: ".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/listLicitadores.php');
            }
        });

        $('#dialogoBorradoModificar').dialog('close');},
    'NO': function() {
            $('#dialogoBorradoModificar').dialog('close');
    }
},
close: function() {
    $('#dialogoBorradoModificar').dialog('close');
}
});		

$('.boton_editar_licitadores').on('click', function(){
    var idlicitador = $(this).attr('id').replace('editarLicitadoresModificar_','');
    
    
    var id = '#nombreModificar_'+idlicitador;
    var nombre_lic = $(id).text();
   
    id = '#importeModificar_'+idlicitador;
    var importe_lic = $(id).text();
    id = '#ocultoModificar_calidad_'+idlicitador;
        var calidad_lic = $(id).val();
    
    id = '#comentarioModificar_'+idlicitador;
    var comentarios_lic = $(id).text();			

    $('#dialogoModificar').dialog('open');
    $('#txt_suppliersModificar_id').val(idlicitador);
    $('#txt_nombreModificar_lic').val(nombre_lic);
    $('#txt_importeModificar_lic').val(importe_lic.replace(',00','').replace(' ','').replace('€', '').replace('.','').replace(',',''));
    $('#valorModificar').val(calidad_lic);
    $('#txt_comentariosModificar_lic').val(comentarios_lic);

    id = '#valModificar_'+calidad_lic;
    $(id).css('background-color', 'rgb(251, 171, 171)');		
});

$('.boton_borrar_licitadores').on('click', function(){	

        var id = $(this).attr('id').replace('quitarLicitadoresModificar_','');
        $('#quitarIdLicitadorModificar').val(id);
        $('#dialogoBorradoModificar').dialog('open');

});		

$('.valores').on('click', function(){		
    $('.valores').css('background-color', '#FFFFFF');
    var id = $(this).attr('id').replace('valModificar_','');
    $('.valores').css('background-color', '#fff');
    $(this).css('background-color', 'rgb(251, 171, 171)');
    $('#valorModificar').val(id);
});		


$('.chkLic').on('click', function(){	   

var idLic = $(this).attr('id').replace('chkAdjudicatarioModificar_','');
var cont = 1;
var idnom = '#nombreModificar_'+idLic;
var nombre = $(idnom).text();
var supplier = '#supplierModificar_id_'+idLic;
var idadj = $(supplier).val();
$('#adjudicarIdSupplierModificar').val(idadj);
$('#adjudicarIdProjectTaskModificar').val(".$projecttasks_id.");

if(".$projecttasks_id.">0 && idadj>0){
        if(cont==1){			
                $('#dialogoAdjudicatarioModificar').dialog('open');
                $('#adjModificar').text(nombre);				
        }else{
                alert('Debe seleccionar un único licitador');
        }
}else{
        alert('No se ha registra un contrato o proveedor.');
}
});	


 		

</script>";			

