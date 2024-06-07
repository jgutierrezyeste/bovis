<?php
include ("../../../inc/includes.php");
GLOBAL $DB,$CFG_GLPI;
$objCommonDBT=new CommonDBTM;

$id = $_GET['idfinancial'];

        if ($id>0) {
            $sql = "DELETE FROM glpi_plugin_comproveedores_annualbillings WHERE id=".$id;
            $result = $DB->query($sql);
        }
