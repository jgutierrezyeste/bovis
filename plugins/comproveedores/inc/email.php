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

$fromEmail = $_GET['fromEmail'];
$toEmail = $_GET['toEmail'];
$subjectEmail = $_GET['subjectEmail'];
$messageEmail = $_GET['messageEmail'];


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


    $exito = $pm->Send();
    if($exito){
        echo "El correo ha sido enviado correctamente.";
    }else{
        echo "El correo no ha posido ser enviado. ".$pm->ErrorInfo;
    }