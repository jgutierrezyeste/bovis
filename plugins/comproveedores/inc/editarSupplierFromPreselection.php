<?php
include ("../../../inc/includes.php");
GLOBAL $DB,$CFG_GLPI;
$objCommonDBT=new CommonDBTM;

$idlicitador = $_GET['idlicitador'];
$idprojecttask = $_GET['idprojecttask'];
$importe = $_GET['importe'];
$calidad = $_GET['calidad'];
$comentarios = $_GET['comentarios'];

	$sql = "UPDATE glpi_plugin_comproveedores_preselections 
        SET importe_ofertado={$importe}, calidad_oferta={$calidad}, comentarios='{$comentarios}' WHERE id=".$idlicitador;
	$DB->query($sql);
	
?>