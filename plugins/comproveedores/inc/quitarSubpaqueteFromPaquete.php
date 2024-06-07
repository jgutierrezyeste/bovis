<?php
include ("../../../inc/includes.php");
GLOBAL $DB,$CFG_GLPI;
$objCommonDBT=new CommonDBTM;

$idlicitador = $_GET['id'];

        if ($idlicitador!=0) {
            $sql = "DELETE FROM glpi_plugin_comproveedores_subpaquetes WHERE id=".$idlicitador;
            $result = $DB->query($sql);

        }