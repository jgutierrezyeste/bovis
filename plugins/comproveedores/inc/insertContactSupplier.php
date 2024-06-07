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

$ident = $_GET['identContacto'];
$nombre = $_GET['nombreContacto'];
$telefono = $_GET['telefonoContacto'];
$email = $_GET['emailContacto'];
$fkcontacttype = $_GET['cargoContacto'];
$delegacion = $_GET['delegacionContacto'];
$fkcv = $_GET['fkcv'];

if($ident>0){
    $sql = "UPDATE glpi_plugin_comproveedores_contacts 
                SET nombre='{$nombre}', 
                telefono='{$telefono}', 
                email='{$email}',
                fkcontacttype={$fkcontacttype},
                fkcv={$fkcv}, 
                delegacion='{$delegacion}'
            WHERE id = {$ident}";
}else{
    $sql = "INSERT INTO glpi_plugin_comproveedores_contacts (nombre,telefono,email,fkcontacttype,fkcv,delegacion)
            VALUE ('{$nombre}','{$telefono}','{$email}',{$fkcontacttype},{$fkcv},'{$delegacion}')";
}
//echo $sql;
$result = $DB->query($sql);
