<?php
    include ("../../../inc/includes.php");
    GLOBAL $DB,$CFG_GLPI;
    $objCommonDBT=new CommonDBTM;
    
    $cvid = $_GET['cvid'];
    $sql = "DELETE FROM glpi_plugin_comproveedores_listambitos WHERE cv_id=".$cvid;
    $result = $DB->query($sql);
    //echo $sql;