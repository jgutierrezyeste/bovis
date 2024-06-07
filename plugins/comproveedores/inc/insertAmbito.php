<?php
    include ("../../../inc/includes.php");
    GLOBAL $DB,$CFG_GLPI;
    $objCommonDBT=new CommonDBTM;
    
    $cvid = $_GET['cvid'];
    $ambitoid = $_GET['ambitoid'];
    $sql = "INSERT INTO glpi_plugin_comproveedores_listambitos (plugin_comproveedores_ambitos_id,cv_id) VALUES ({$ambitoid},{$cvid})";
    $result = $DB->query($sql);
    //echo $sql;


