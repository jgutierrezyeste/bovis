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

$cvid = $_GET['cvid'];
$aceptacion = $_GET['aceptacion'];
$usuarioid = $_GET['usuarioid'];


	$sql = "update glpi_plugin_comproveedores_cvs set aceptacion='{$activar}', usuario_aceptacion={$usuarioid}, fecha_aceptacion=now()
        where id={$cvid}";
	$DB->query($sql);