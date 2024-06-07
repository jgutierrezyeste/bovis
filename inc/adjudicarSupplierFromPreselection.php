<?php
include ("../../../inc/includes.php");
GLOBAL $DB,$CFG_GLPI;
$objCommonDBT=new CommonDBTM;

$idsupplier = $_GET['idsupplier'];
$idprojecttask = $_GET['idprojecttask'];


	$sql = "insert into glpi_projecttaskteams (projecttasks_id, itemtype, items_id)
			values ( $idprojecttask, 'Supplier', $idsupplier)";
	$DB->query($sql);
	
?>