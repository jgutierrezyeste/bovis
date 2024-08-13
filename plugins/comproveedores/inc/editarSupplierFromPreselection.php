<?php
include ("../../../inc/includes.php");
GLOBAL $DB,$CFG_GLPI;
$objCommonDBT=new CommonDBTM;



$idlicitador = $_GET['idlicitador'];
$idprojecttask = $_GET['idprojecttask'];
$importe = $_GET['importe'];

$calidad = $_GET['calidad'];

$comentarios = $_GET['comentarios'];

if($calidad ==' '){
	if ($importe ==' ' or !$importe)
		$sql = "UPDATE glpi_plugin_comproveedores_preselections 
        SET importe_ofertado=0, calidad_oferta=' ', comentarios='{$comentarios}' WHERE id=".$idlicitador;
    else
    	$sql = "UPDATE glpi_plugin_comproveedores_preselections 
        SET importe_ofertado={$importe}, calidad_oferta=' ', comentarios='{$comentarios}' WHERE id=".$idlicitador;
}
else{
	if ($importe == ' ' or !$importe)
		$sql = "UPDATE glpi_plugin_comproveedores_preselections 
        	SET importe_ofertado=' ', calidad_oferta={$calidad}, comentarios='{$comentarios}' WHERE id=".$idlicitador;
    else{
    	$sql = "UPDATE glpi_plugin_comproveedores_preselections 
        SET importe_ofertado={$importe}, calidad_oferta={$calidad}, comentarios='{$comentarios}' WHERE id=".$idlicitador;

    }
}


$DB->query($sql);
	
?>