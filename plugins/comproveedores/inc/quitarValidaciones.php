<?php
    include ("../../../inc/includes.php");
    GLOBAL $DB,$CFG_GLPI;
    $objCommonDBT=new CommonDBTM;
    
    $id = $_GET['id'];
    $sql = "DELETE FROM glpi_plugin_comproveedores_valuations WHERE id=".$id;
    $result = $DB->query($sql);
    //echo $sql;