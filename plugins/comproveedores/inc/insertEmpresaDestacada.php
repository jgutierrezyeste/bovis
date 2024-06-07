<?php
    include ("../../../inc/includes.php");
    GLOBAL $DB,$CFG_GLPI;
    $objCommonDBT = new CommonDBTM;
    
    $cvid           = $_GET['cvid'];
    $id             = $_GET['id'];
    $denominacion   = $_GET['denominacion'];

    if($id==0){
    $sql = "INSERT INTO glpi_plugin_comproveedores_featuredcompanies (nombre_empresa_destacada, cv_id) VALUES ('{$denominacion}', {$cvid})";
    }else{
    $sql = "UPDATE glpi_plugin_comproveedores_featuredcompanies SET nombre_empresa_destacada = '{$denominacion}' WHERE id = {$id}";    
    }
    $result = $DB->query($sql);
    //echo $sql;


