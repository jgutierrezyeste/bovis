<?php
include ("../../../inc/includes.php");
GLOBAL $DB,$CFG_GLPI;
$objCommonDBT=new CommonDBTM;

$gerente = $_GET['gerente'];
$idteam = $_GET['id'];

	$sql = "UPDATE glpi_projectteams 
        SET gerente={$gerente} WHERE id=".$idteam;
	$DB->query($sql);
	
?>