<?php
include ("../../../inc/includes.php");
GLOBAL $DB,$CFG_GLPI;
$objCommonDBT=new CommonDBTM;

$idsupplier = $_GET['idsupplier'];
$idprojecttask = $_GET['idprojecttask'];


$sql_consulta="SELECT id from glpi_projecttaskteams where projecttasks_id=$idprojecttask && items_id=$idsupplier";
$result= $DB->query($sql_consulta);


	if($result->num_rows != 0)
	{
		 $sql ="DELETE FROM glpi_projecttaskteams where projecttasks_id=".$idprojecttask. " and items_id=".$idsupplier;
		 $DB->query($sql);
	}
	
?>