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
$fondosajenos = $_GET['fondosajenos'];
$facturacion = $_GET['facturacion'];
$beneficiosai = $_GET['beneficiosai'];
$resultado = $_GET['resultado'];
$totalactivo = $_GET['totalactivo'];
$activocirculante = $_GET['activocirculante'];
$pasivocirculante = $_GET['pasivocirculante'];
$cashflow = $_GET['cashflow'];
$fondospropios = $_GET['fondospropios'];
$cvid = $_GET['cvid'];

$sql = "INSERT INTO glpi_plugin_comproveedores_annualbillings (anio, 
    facturacion, 
    beneficios_impuestos, 
    resultado, 
    total_activo, 
    activo_circulante, 
    pasivo_circulante, 
    cash_flow, 
    fondos_propios, 
    recursos_ajenos, 
    cv_id) VALUES (";
if($anio==''){$sql.="null";}else{$sql.="{$anio}";}
$sql.=",";
if($facturacion==''){$sql.="null";}else{$sql.="{$facturacion}";}
$sql.=",";
if($beneficiosai==''){$sql.="null";}else{$sql.="{$beneficiosai}";}
$sql.=",";
if($resultado==''){$sql.="null";}else{$sql.="{$resultado}";}
$sql.=",";
if($totalactivo==''){$sql.="null";}else{$sql.="{$totalactivo}";}
$sql.=",";
if($activocirculante==''){$sql.="null";}else{$sql.="{$activocirculante}";}
$sql.=",";
if($pasivocirculante==''){$sql.="null";}else{$sql.="{$pasivocirculante}";}
$sql.=",";
if($cashflow==''){$sql.="null";}else{$sql.="{$cashflow}";}
$sql.=",";
if($fondospropios==''){$sql.="null";}else{$sql.="{$fondospropios}";}
$sql.=",";
if($fondosajenos==''){$sql.="null";}else{$sql.="{$fondosajenos}";}
$sql.=",{$cvid})";

$insert = $DB->query($sql);
//echo $sql;


