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
$id = $_GET['idfinancial'];

$sql = "UPDATE glpi_plugin_comproveedores_annualbillings SET anio=";
if($anio==''){$sql.="null";}else{$sql.="{$anio}";}
$sql.=", facturacion=";
if($facturacion==''){$sql.="null";}else{$sql.="{$facturacion}";}
$sql.=", beneficios_impuestos=";
if($beneficiosai==''){$sql.="null";}else{$sql.="{$beneficiosai}";}
$sql.=", resultado=";
if($resultado==''){$sql.="null";}else{$sql.="{$resultado}";}
$sql.=", total_activo=";
if($totalactivo==''){$sql.="null";}else{$sql.="{$totalactivo}";}
$sql.=", activo_circulante=";
if($activocirculante==''){$sql.="null";}else{$sql.="{$activocirculante}";}
$sql.=", pasivo_circulante=";
if($pasivocirculante==''){$sql.="null";}else{$sql.="{$pasivocirculante}";}
$sql.=", cash_flow="; 
if($cashflow==''){$sql.="null";}else{$sql.="{$cashflow}";}
$sql.=", fondos_propios=";
if($fondospropios==''){$sql.="null";}else{$sql.="{$fondospropios}";}
$sql.=" ,recursos_ajenos="; 
if($fondosajenos==''){$sql.="null";}else{$sql.="{$fondosajenos}";}
$sql.=", cv_id=".$cvid;
$sql.=" WHERE id=".$id;
$insert = $DB->query($sql);
//echo $sql;

//Html::footer();
