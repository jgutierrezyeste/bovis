<?php
include ("../../../inc/includes.php");
GLOBAL $DB,$CFG_GLPI;
$objCommonDBT=new CommonDBTM;

$id = $_GET['id'];

        if ($id>0) {
            $sql = "DELETE FROM glpi_plugin_comproveedores_featuredcompanies WHERE id=".$id;
            $result = $DB->query($sql);
        }



