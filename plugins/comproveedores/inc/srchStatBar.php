<?php

/* 
 * RSU es una aplicación desarrollada por el equipo TI de 
FOMENTO DE TÉCNICAS EXTREMEÑAS S.L. (FOTEX)
+34924207328
http://www.fotex.es
Comienzo del desarrollo: enero de 2019
 */


include ("../../../inc/includes.php");
GLOBAL $DB,$CFG_GLPI;
$objCommonDBT=new CommonDBTM;

$ticks = "";        
$src = "";
$cad = "";
$valor = $_GET['valor'];
if(isset($_GET['id'])){ $id = $_GET['id']; }else{$id = 0;}


switch($valor){
case '1':
    $sql = "select anio, sum(facturacion) as total
            from glpi_plugin_comproveedores_annualbillings
            where cv_id={$id}
            group by anio
            order by anio asc";

    break;
    
case '2':
    $sql = "select anio, sum(beneficios_impuestos) as total
            from glpi_plugin_comproveedores_annualbillings
            where cv_id={$id}
            group by anio
            order by anio asc";
    break;    

case '3':
    $sql = "select anio, sum(resultado) as total
            from glpi_plugin_comproveedores_annualbillings
            where cv_id={$id}
            group by anio
            order by anio asc";
    break;    

case '4':
    $sql = "select anio, sum(total_activo) as total
            from glpi_plugin_comproveedores_annualbillings
            where cv_id={$id}
            group by anio
            order by anio asc";
    break; 

case '5':
    $sql = "select anio, sum(activo_circulante) as total
            from glpi_plugin_comproveedores_annualbillings
            where cv_id={$id}
            group by anio
            order by anio asc";
    break; 

case '6':
    $sql = "select anio, sum(pasivo_circulante) as total
            from glpi_plugin_comproveedores_annualbillings
            where cv_id={$id}
            group by anio
            order by anio asc";
    break; 

case '7':
    $sql = "select anio, sum(pasivo_circulante) as total
            from glpi_plugin_comproveedores_annualbillings
            where cv_id={$id}
            group by anio
            order by anio asc";
    break; 

case '8':
    $sql = "select anio, sum(cash_flow) as total
            from glpi_plugin_comproveedores_annualbillings
            where cv_id={$id}
            group by anio
            order by anio asc";
    break; 

case '9':
    $sql = "select anio, sum(fondos_propios) as total
            from glpi_plugin_comproveedores_annualbillings
            where cv_id={$id}
            group by anio
            order by anio asc";
    break; 

case '10':
    $sql = "select anio, sum(fondos_ajenos) as total
            from glpi_plugin_comproveedores_annualbillings
            where cv_id={$id}
            group by anio
            order by anio asc";
    break; 
}

    //echo $sql;
    $result = $DB->query($sql);
    if ($result->num_rows>0) {	
        
        $cad  = "<div id='flot-placeholder' style='width: 298px; height: 180px; position: relative; border: none;'>";
        $cad .= "</div>";            
        $s  = "<script type='text/javascript'>";
        $ticks = $ticks." var ticks = [";
        $s .= " var data = [";
        $i = 0;    
        while ($fila = $DB->fetch_array($result)) { 
            $s .= "[{$i}, {$fila['total']}],";
            $ticks .= "[{$i}, '{$fila['anio']}'],";
            $i++;
        }
        $ticks = trim($ticks, ",");
        $ticks = $ticks."]; ";      
        $s = trim($s, ",");
        $s .= "]; ";    
        $s .= "var dataset=[{label:'Evolución en €', data:data, color:'#5482ff'}]; ";
        $s = $cad.$s.$ticks;    

        $s .= "
        var options = {
                series: {
                    bars: {
                        show: true
                    }
                },
                bars: {
                    align: 'center',
                    barWidth: 0.5
                },
                xaxis: {
                    axisLabel: 'Ejercicios',
                    axisLabelUseCanvas: true,
                    axisLabelFontSizePixels: 10,
                    axisLabelFontFamily: 'Verdana, Arial',
                    axisLabelPadding: 10,
                    ticks: ticks
                },
                yaxis: {
                    axisLabel: 'importes',
                    axisLabelUseCanvas: true,
                    axisLabelFontSizePixels: 12,
                    axisLabelFontFamily: 'Verdana, Arial',
                    axisLabelPadding: 3,
                    tickFormatter: function (v, axis) {
                        return v + '€';
                    }
                },
                legend: {
                    noColumns: 0,
                    labelBoxBorderColor: '#000000',
                    position: 'nw'
                },
                grid: {
                    hoverable: true,
                    borderWidth: 2,
                    backgroundColor: { colors: ['#ffffff', '#EDF5FF'] }
                }                         
        };

        $(document).ready(function () {
            $('#flot-placeholder').plot(dataset, options);
        });";

        $s .= "</script>";            


    }else{
        $cad="sin datos";
    }
    echo $cad.$s; 