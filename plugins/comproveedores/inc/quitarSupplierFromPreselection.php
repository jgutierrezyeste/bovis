<?php
include ("../../../inc/includes.php");
GLOBAL $DB,$CFG_GLPI;
$objCommonDBT=new CommonDBTM;

$idlicitador = $_GET['idlicitador'];

        if ($idlicitador!=0) {
            $sql = "DELETE FROM glpi_plugin_comproveedores_preselections WHERE id=".$idlicitador;
            $result = $DB->query($sql);
/**
            $_GET['projecttasks_id']=$idprojecttask;
            $_GET['nombre_lic']='';
            $_GET['cif_lic']='';
            include($CFG_GLPI['root_doc']."/plugins/comproveedores/inc/listLicitadores.php");**/
        }