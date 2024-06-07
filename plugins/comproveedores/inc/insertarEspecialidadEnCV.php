<?php

/* 
 * RSU es una aplicación desarrollada por el equipo TI de 
FOMENTO DE TÉCNICAS EXTREMEÑAS S.L. (FOTEX)
+34924207328
http://www.fotex.es
Comienzo del desarrollo: enero de 2019
 */

include ("../../../inc/includes.php");
GLOBAL $DB,$CFG_GLPI;
$objCommonDBT=new CommonDBTM;

$idtipo = $_GET['idtipo'];
$idcategoria = $_GET['idcategoria'];
$idespecialidad = $_GET['idespecialidad'];
$idcv = $_GET['idcv'];

$sql = "INSERT INTO glpi_plugin_comproveedores_listspecialties (`plugin_comproveedores_roltypes_id`,
        `plugin_comproveedores_categories_id`,
        `plugin_comproveedores_specialties_id`,
        `cv_id`)
        VALUE ({$idtipo},{$idcategoria},{$idespecialidad},{$idcv})";
        
        $result = $DB->query($sql);
        //echo $sql;
