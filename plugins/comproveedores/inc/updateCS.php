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

    
$capital_social = $_GET['capital_social'];
$id = $_GET['id'];

$sql = "UPDATE glpi_plugin_comproveedores_cvs SET capital_social={$capital_social} ";
$sql.=" WHERE id=".$id;
$insert = $DB->query($sql);
echo $sql;


