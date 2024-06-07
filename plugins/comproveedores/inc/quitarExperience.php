<?php
include ("../../../inc/includes.php");
GLOBAL $DB,$CFG_GLPI;
$objCommonDBT=new CommonDBTM;

$id = $_GET['id'];

        if ($id>0) {
            $sql = "UPDATE glpi_plugin_comproveedores_experiences SET is_deleted=1 WHERE id=".$id;
            $result = $DB->query($sql);
        }
