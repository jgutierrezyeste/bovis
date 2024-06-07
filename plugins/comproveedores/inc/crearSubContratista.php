<?php
include ("../../../inc/includes.php");
GLOBAL $DB,$CFG_GLPI;
$objCommonDBT=new CommonDBTM;

$cif = $_GET['cif'];
$nombre = $_GET['nombre'];


	$sql = "INSERT INTO glpi_suppliers (entities_id, is_recursive, cif, name)
        VALUES (0,0,'{$cif}', '{$nombre}')";
	$DB->query($sql);
	