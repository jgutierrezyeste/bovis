   
            
<?php	

include ("../../../inc/includes.php");
GLOBAL $DB,$CFG_GLPI;
$objCommonDBT=new CommonDBTM;

if ($_GET['projecttasks_id']) {
    $projecttasks_id = $_GET['projecttasks_id'];
}else{
    $projecttasks_id=0;
}
if ($_GET['nombre_adjudicatario']) {
    $nombre_adjudicatario = $_GET['nombre_adjudicatario'];
}else{
    $nombre_adjudicatario = '';
}
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

/*¿¿adjudicado??*/
$adjudicado=0;

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
    echo "<input id='verLicitadores' type='hidden' value='0' />";
}else{
    $ver = true;
    echo "<input id='verLicitadores' type='hidden' value='1' />";
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

echo "<table id='tblLicitadores' class='display compact' style='width:100%; float:left; position: relative;'>";
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
        echo "<tr id='licitador_".$data['licitador_id']."' class='fila' >";
        echo "<td id='nombre_".$data['licitador_id']."' class='left columna'>";
        echo  "<a href='supplier.form.php?id=".$data['suppliers_id']."'>".$data['licitador']."</a>";
        echo "</td>";
        echo "<td id='cif_".$data['licitador_id']."' class='center columna'>";
        echo    $data['cif_nif'];
        echo "</td>";
        if($ver){
            if ($data['importe_ofertado'] == "NULL"){
                    echo "<td id='importe_".$data['licitador_id']."' class='columna' style='text-align:center;'>".' '."</td>";
            }else{
                    echo "<td id='importe_".$data['licitador_id']."' class='columna'>".number_format($data['importe_ofertado'],2,',','.')." </td>";
            }	
        }
        if (!$data['calidad_oferta'] OR $data['calidad_oferta']=="NULL"){					
                echo "<td id='calidad_".$data['licitador_id']."' class='columna' style='text-align:center; width: 100px;'>";
                echo    "<input id='oculto_calidad_".$data['licitador_id']."' value=' ".$data['calidad_oferta']."' type='hidden' />";
                echo "</td>";		
        }else{
                echo "<td id='calidad_".$data['licitador_id']."' class='columna' style='font-size: 10px; color: #777; text-align:center; width: 100px;"
                        . "background-image: url(".$CFG_GLPI["root_doc"]."/pics/valoracion_".$data['calidad_oferta'].".png); background-repeat: no-repeat; background-size: 18px; background-position: center;'>";
                echo "<input id='oculto_calidad_".$data['licitador_id']."' value='".$data['calidad_oferta']."' type='hidden' />";
                echo $data['calidad_oferta'];
                //echo "<image title='puntuación = ".$data['calidad_oferta']."' src='' style='width:18px;height:18px;'/>";
                echo "</td>";								
        }
        echo "<td id='comentario_".$data['licitador_id']."' class='columna' style='text-align:left;'>".$data['comentarios']."</td>";

        echo "<td style='text-align:center; width: 100px;' class='columnaCHK' >";
            echo "<input type='hidden' id='supplier_id_".$data['licitador_id']."' value='".$data['suppliers_id']."' />";
            if($data['adjudicatario']==1){
                    $adjudicatario=1;
                    echo "<image src='".$CFG_GLPI["root_doc"]."/pics/ok.png' title='adjudicatario' style='width:20px;height:20px;'/>";
            }else{
                    if($nombre_adjudicatario==''){
                        echo "<input id='chkAdjudicatario_".$data['licitador_id']."' class='chkLic' type='checkbox'/>";						
                    }
                    else {
                        echo " - ";
                    }
            }
        echo "</td>";
        if($ver && $nombre_adjudicatario == ''){ 
            echo "<td><input id='editarLicitadores_{$data['licitador_id']}' class='boton_editar_licitadores' type='submit' value='' title='editar elemento'/></td>";
            echo "<td><input id='quitarLicitadores_{$data['licitador_id']}' type='submit' value=' ' class='boton_borrar_licitadores' style='background-size:15px; width:20px; height:20px;border-radius:2px;'/></td>";

        }
        
        echo "</tr>";
    }	



echo "</tbody>";
echo "</table>";
if( $adjudicatario == 1)
    {   
        echo "<input style='height:50px; width:150px; font-size:18px' type='button' id='modificarAdjudicatario' value='Editar licitacion'/> ";
        /*echo "<input style='height:50px; width:150px; font-size:18px' type='button' id='comparativo' value='Comparativo'/> ";*/
        /*echo "<a href='".$CFG_GLPI['root_doc'].
                   "/inc/document_item.php' class='edit_document fa fa-eye pointer' title='".
                   _sx("button", "Show")."'>";*/
        /*echo "<form method='post' action='".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/insertArchivoComparativo.php' enctype='multipart/form-data'>
                <input type='file' name='archivo' id='archivo'/>
                <input type='submit' value='enviar'/>
                </form>";*/
                
    }


/*echo "<div id='dialogoArchivo' title='Añadir comparativo'>
                
              
                     
                    
                        <form method ='POST' action='".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/insertArchivoComparativo.php' enctype='multipart/form-data' >


                            <input type='file' id='archivo' name='archivo'/>
                            <input type='submit' value='enviar fichero' />

                        </form>
                           
                
              

        </div>";
*/

echo "<div id='dialogo' title='Edición de Licitador'>

    <input id='txt_projecttask_id' type='hidden' value='".$projecttasks_id."' />
    <input id='txt_suppliers_id' type='hidden' value='' />
    <table style='width:90%;'>
    <tr style='height:40px;'>
        <td>nombre del licitador:</td><td><input type='text' id='txt_nombre_lic' value='' style='width:300px;' readonly/></td>
    </tr>			
    <tr style='height:50px;'>
        <td>importe ofertado:</td><td><input id='txt_importe_lic' type='text' value='' /> €</td>
    </tr>
    <tr style='height:50px;'>
        <td>comentarios:</td><td><textarea id='txt_comentarios_lic' value='' cols='50' rows='4'/></td>
    </tr>			
    <tr style=''>
        <td>calidad de la oferta:</td><td><input id='txt_calidad_lic' type='hidden' value='' /></td>
    </tr>
    <tr >
        <td colspan='2'>
                <image id='val_1' class='valores' title='Valor = 1' style='padding:4px; cursor:pointer;' src='".$CFG_GLPI["root_doc"]."/pics/valoracion_1.png' />
                <image id='val_2' class='valores' title='Valor = 2' style='padding:4px; cursor:pointer;' src='".$CFG_GLPI["root_doc"]."/pics/valoracion_2.png' />
                <image id='val_3' class='valores' title='Valor = 3' style='padding:4px; cursor:pointer;' src='".$CFG_GLPI["root_doc"]."/pics/valoracion_3.png' />
                <image id='val_4' class='valores' title='Valor = 4' style='padding:4px; cursor:pointer;' src='".$CFG_GLPI["root_doc"]."/pics/valoracion_4.png' />
                <image id='val_5' class='valores' title='Valor = 5' style='padding:4px; cursor:pointer;' src='".$CFG_GLPI["root_doc"]."/pics/valoracion_5.png' />
        </td>
        <td><input id='valor' type='text' value='0' size='1' readonly style='height: 35px; border:none; background-color: #FFF; font-size:30px; font-weight: bold;'/></td>
    </tr>
    </table>
</div>";

echo "<div id='dialogoBorrado' title='Quitar Licitador'>
    <table>
    <tr>
        <td style='vertical-align:middle;'><img src='../pics/warning.png' style='margin-right: 10px;'/></td>
        <td style='vertical-align:middle; text-align: justify;'>¿Realmente desea quitar este licitador del proceso de selección?</td>
    </tr> 
    </table>
    <input id='quitarIdLicitador' type='hidden' value='' />
</div>";


echo "<div id='dialogoAdjudicatario' title='Adjudicar licitador'>
<table>
<tr>
<td>
    <image src='".$CFG_GLPI["root_doc"]."/pics/interrogacion.png' style='width:50px; hight:50px;'/>
</td>
<td style='padding:10px;'>
    ¿Realmente desea adjudicar este contrato a <label id='adj'></label>?
</td>
<input id='adjudicarIdSupplier' type='hidden' value='' />
<input id='adjudicarIdProjectTask' type='hidden' value='' />
</div>";			

echo "<script type='text/javascript'>
var ver = $('#verLicitadores').val();

if(ver == 1){
    $('#tblLicitadores').DataTable({
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
    $('#tblLicitadores').DataTable({
            'searching':      true,
            'scrollY':        '300px',
            'scrollCollapse': true,
            'paging':         false,
            'language': {'url': '".$CFG_GLPI["root_doc"]."/lib/jqueryplugins/Spanish.json'}                    
    });        
}



$('#dialogo').dialog({
autoOpen: false,
height: 370,
width: 520,
modal: true,
buttons: {
    'Aceptar': function() { 
        var idprojecttask = $('#txt_projecttask_id').val();
        var idlicitador = $('#txt_suppliers_id').val();
        var importe = $('#txt_importe_lic').val().replace(' ','').replace('€', '').replace('.','').replace(',','');
        var calidad = $('#valor').val();
        var comentarios = $('#txt_comentarios_lic').val();
        
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
                        alert('Data not found: ".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/listLicitadores.php');
                }
        });				

        $('#dialogo').dialog('close');
    },
    'Cancelar': function() {
      $('#dialogo').dialog('close');
    }
},
close: function() {
    $('#dialogo').dialog('close');
}
});


/*dialogo para insertar documento*/

/*$('#dialogoArchivo').dialog({
    autoOpen:false,
    height:200,
    modal:true,
    buttons: {
    'Aceptar': function() { 
       
        var idprojecttask = $('#file_projecttask_id').val();
        var comparativo = '".$_FILES['archivo']."';
        alert (comparativo);
        $.ajax({ 
                async: false, 
                type: 'POST',
                data: {  
                       'idprojecttask':idprojecttask,
                       'comparativo':comparativo 
                       
                      },                  
                url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/insertArchivoComparativo.php',                  
                success:function(data){
                        //window.location.reload(true);
                },
                error: function(result) {
                        alert('Data not found: ".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/listLicitadores.php');
                }
        });         
                     

        $('#dialogoArchivo').dialog('close');
    },
    'Cancelar': function() {
      $('#dialogoArchivo').dialog('close');
    }
},
close: function() {
    $('#dialogoArchivo').dialog('close');
}
    
});*/

/*fin dialogo para añadir comparativo*/

$('#dialogoAdjudicatario').dialog({
autoOpen: false,
height: 200,
width: 250,
modal: true,
buttons: {
    'SI': function() { 
            var idsupplier = $('#adjudicarIdSupplier').val();
            var idprojecttask = $('#adjudicarIdProjectTask').val();

            $.ajax({ 
                    async: false, 
                    type: 'GET',
                    data: {'idsupplier':idsupplier,	'idprojecttask':idprojecttask},                  
                    url:'".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/adjudicarSupplierFromPreselection.php',  				
                    success:function(data){
                            window.location.reload(true);
                    },
                    error: function(result) {
                            alert('Data not found: ".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/listLicitadores.php');
                    }
            });

            $('#dialogoAdjudicatario').dialog('close');},
    'NO': function() {
            $('#dialogoAdjudicatario').dialog('close');
    }
},
close: function() {
    $('#dialogoAdjudicatario').dialog('close');
}
});				

$('#dialogoBorrado').dialog({
autoOpen: false,
height: 200,
width: 250,
modal: true,
buttons: {
    'SI': function() { 
        var idlicitador = $('#quitarIdLicitador').val();

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

        $('#dialogoBorrado').dialog('close');},
    'NO': function() {
            $('#dialogoBorrado').dialog('close');
    }
},
close: function() {
    $('#dialogoBorrado').dialog('close');
}
});		

$('.boton_editar_licitadores').on('click', function(){
    var idlicitador = $(this).attr('id').replace('editarLicitadores_','');
    var id = '#nombre_'+idlicitador;
    var nombre_lic = $(id).text();
    id = '#importe_'+idlicitador;
    var importe_lic = $(id).text();
    id = '#oculto_calidad_'+idlicitador;
    var calidad_lic = $(id).val();
    id = '#comentario_'+idlicitador;
    var comentarios_lic = $(id).text();			

    $('#dialogo').dialog('open');
    $('#txt_suppliers_id').val(idlicitador);
    $('#txt_nombre_lic').val(nombre_lic);
    $('#txt_importe_lic').val(importe_lic.replace(',00','').replace(' ','').replace('€', '').replace('.','').replace(',',''));
    $('#valor').val(calidad_lic);
    $('#txt_comentarios_lic').val(comentarios_lic);

    id = '#val_'+calidad_lic;
    $(id).css('background-color', 'rgb(251, 171, 171)');		
});

$('.boton_borrar_licitadores').on('click', function(){	

        var id = $(this).attr('id').replace('quitarLicitadores_','');
        $('#quitarIdLicitador').val(id);
        $('#dialogoBorrado').dialog('open');

});		

$('.valores').on('click', function(){		
    $('.valores').css('background-color', '#FFFFFF');
    var id = $(this).attr('id').replace('val_','');
    $('.valores').css('background-color', '#fff');
    $(this).css('background-color', 'rgb(251, 171, 171)');
    $('#valor').val(id);
});		


$('.chkLic').on('click', function(){	   

var idLic = $(this).attr('id').replace('chkAdjudicatario_','');
var cont = 1;
var idnom = '#nombre_'+idLic;
var nombre = $(idnom).text();
var supplier = '#supplier_id_'+idLic;
var idadj = $(supplier).val();
$('#adjudicarIdSupplier').val(idadj);
$('#adjudicarIdProjectTask').val(".$projecttasks_id.");

if(".$projecttasks_id.">0 && idadj>0){
        if(cont==1){			
                $('#dialogoAdjudicatario').dialog('open');
                $('#adj').text(nombre);				
        }else{
                alert('Debe seleccionar un único licitador');
        }
}else{
        alert('No se ha registra un contrato o proveedor.');
}
});	


/*recargamos lista de licitadores para modificar adjudicatario*/

$('#modificarAdjudicatario').on('click', function(){
    var idprojecttask = ".$projecttasks_id.";
    
$.ajax({ 
                    async: false, 
                    type: 'GET',
                    data: {'projecttasks_id':idprojecttask },                  
                    url: '".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/updateLicitadores.php',                
                    success:function(data){
                           
                             
                             $('#page').html(data);
                        },
                        error: function(result) {
                            alert('Data not found: ".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/updateLicitadores.php');
                        }
                });  

}); 		

/*$('#comparativo').on('click', function(){
    var idprojecttask = ".$projecttasks_id.";
     $('#dialogoArchivo').dialog('open');


                

});*/         

</script>";			

