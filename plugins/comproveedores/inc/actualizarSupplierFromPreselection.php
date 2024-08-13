<?php
include ("../../../inc/includes.php");
GLOBAL $DB,$CFG_GLPI;
$objCommonDBT=new CommonDBTM;

$idsupplier = $_GET['idsupplier'];
$idprojecttask = $_GET['idprojecttask'];


	$sql = "update glpi_projecttaskteams set items_id=$idsupplier
			 where projecttasks_id=$idprojecttask";
	$archivo=fopen("updateadjudicatario.txt","w+");
	fwrite($archivo,$sql);
	$DB->query($sql);
	
?>