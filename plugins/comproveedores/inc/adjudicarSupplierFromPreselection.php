<?php
include ("../../../inc/includes.php");
GLOBAL $DB,$CFG_GLPI;
$objCommonDBT=new CommonDBTM;

$idsupplier = $_GET['idsupplier'];
$idprojecttask = $_GET['idprojecttask'];

	$sql_consulta= "SELECT id from glpi_projecttaskteams where projecttasks_id=$idprojecttask";
	$result=$DB->query($sql_consulta);

	if($result->num_rows == 0)
	{
		$sql = "insert into glpi_projecttaskteams (projecttasks_id, itemtype, items_id)
			values ( $idprojecttask, 'Supplier', $idsupplier)";
		$DB->query($sql);
	}
	else{
		$sql = "update glpi_projecttaskteams set items_id=$idsupplier
				 where projecttasks_id=$idprojecttask";
		$DB->query($sql);
	}
?>