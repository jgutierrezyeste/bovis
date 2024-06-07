<?php
    include ("../../../inc/includes.php");
    GLOBAL $DB,$CFG_GLPI;
    $objCommonDBT=new CommonDBTM;
    
    $cvid = $_GET['cvid'];
    $sql = "UPDATE glpi_plugin_comproveedores_cvs SET is_deleted=1 "
            . "WHERE id=".$cvid;
    $result = $DB->query($sql);
    
    $sql = "UPDATE glpi_suppliers SET cv_id = null "
            . "WHERE cv_id=".$cvid;
    $result = $DB->query($sql);    
    echo $sql;