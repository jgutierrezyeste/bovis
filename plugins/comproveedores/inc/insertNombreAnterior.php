<?php
    include ("../../../inc/includes.php");
    GLOBAL $DB,$CFG_GLPI;
    $objCommonDBT=new CommonDBTM;
    
    $cvid           = $_GET['cvid'];
    $denominacion   = $_GET['denominacion'];
    $fecha_cambio   = $_GET['fecha_cambio'];
    $id             = $_GET['id'];
    
    //año-mes-dia
    
    $ano = substr($fecha_cambio, 6, 4);
    $mes = substr($fecha_cambio, 3, 2);
    $dia = substr($fecha_cambio, 0, 2);
    $fec = $anio+'-'+mes+'-'+dia;
    
    if($id == 0){
        $sql = "INSERT INTO glpi_plugin_comproveedores_previousnamescompanies (nombre, fecha_cambio, cv_id) VALUES ('{$denominacion}', '{$fecha_cambio}', {$cvid})";
    }else{
        $sql = "UPDATE glpi_plugin_comproveedores_previousnamescompanies SET nombre = '{$denominacion}', fecha_cambio = '{$fecha_cambio}', cv_id = {$cvid} WHERE id = {$id} ";
    }
    $result = $DB->query($sql);
    //echo $sql;


