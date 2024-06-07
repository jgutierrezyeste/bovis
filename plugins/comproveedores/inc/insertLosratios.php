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

$anio = $_GET['anio'];
$incidencia = $_GET['incidencia'];
$frecuencia = $_GET['frecuencia'];
$gravedad = $_GET['gravedad'];
$cvid = $_GET['cvid'];

$sql = "INSERT INTO glpi_plugin_comproveedores_lossratios (anio, 
    incidencia, 
    frecuencia,
    gravedad,
    entities_id,
    is_recursive,
    cv_id) VALUES (";
if($anio==''){$sql.="null";}else{$sql.="{$anio}";}
$sql.=",";
if($incidencia==''){$sql.="null";}else{$sql.="{$incidencia}";}
$sql.=",";
if($frecuencia==''){$sql.="null";}else{$sql.="{$frecuencia}";}
$sql.=",";
if($gravedad==''){$sql.="null";}else{$sql.="{$gravedad}";}
$sql.=",0";
$sql.=",0";
$sql.=",{$cvid})";
//echo $sql;
$insert = $DB->query($sql);
$i = $DB->insert_id();
echo $i;


