<?php
    include ("../../../inc/includes.php");
    GLOBAL $DB,$CFG_GLPI;
    $objCommonDBT=new CommonDBTM;
    
    $cvid           = $_GET['cvid'];
    $denominacion   = $_GET['denominacion'];
    $id             = $_GET['id'];

    if($id==0){
        $sql = "INSERT INTO glpi_plugin_comproveedores_subcontractingcompanies (nombre_empresa_subcontratista, cv_id) VALUES ('{$denominacion}', {$cvid})";
    }else{
        $sql = "UPDATE glpi_plugin_comproveedores_subcontractingcompanies SET nombre_empresa_subcontratista = '{$denominacion}' WHERE id = {$id}";
    }
    $result = $DB->query($sql);


