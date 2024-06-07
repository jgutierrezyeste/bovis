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

$fromEmail = $_GET['origen'];
$toEmail = $_GET['destino'];
$subjectEmail = $_GET['asunto'];
$messageEmail = $_GET['mensaje'];
$clave = $_GET['clave'];
$idusuario = $_GET['idusuario'];
$idcontacto = $_GET['idcontacto'];


$pm = new PHPMailer();
$pm->isSMTP();
$pm->SMTPAuth = true;
$pm->SMTPSecure = 'ssl';
$pm->Host = 'smtp.serviciodecorreo.es';
$pm->Port = '465';
$pm->Username = 'info@fotex.es';
$pm->Password = 'Chkdsk2002';
$pm->From = $fromEmail;
$pm->addAddress($toEmail);
$pm->CharSet = 'UTF-8';
$pm->Subject = $subjectEmail;
$pm->isHTML(true);
$pm->Body = html_entity_decode ($messageEmail);


    $claveCOD = auth::getPasswordHash($clave);


    $sql = "update glpi_plugin_comproveedores_contacts  set clave = '{$clave}'  where id = {$idcontacto}";
    $r1 = $DB->query($sql);    

    $sql = "update glpi_users  set password = '{$claveCOD}' where id = {$idusuario}";
    $r2 = $DB->query($sql);        

    $exito = $pm->Send();
    if($exito){
        echo "El correo ha sido enviado y la clave actualizada.";
    }else{
        echo "La clave actualizada pero el correo no ha posido ser enviado.";
    }
