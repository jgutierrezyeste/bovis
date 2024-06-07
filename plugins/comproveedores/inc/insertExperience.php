<?php
    include ("../../../inc/includes.php");
    GLOBAL $DB,$CFG_GLPI;
    $objCommonDBT=new CommonDBTM;
    
    $cv_id = $_GET['cv_id'];
    $plugin_comproveedores_experiencesstates_id = $_GET['plugin_comproveedores_experiencesstates_id'];
    $experiencia_bovis = $_GET['experiencia_bovis'];
    $plugin_comproveedores_experiencestypes_id = $_GET['plugin_comproveedores_experiencestypes_id'];
    $plugin_comproveedores_communities_id = $_GET['plugin_comproveedores_communities_id'];
    $name = $_GET['name'];
    $cliente = $_GET['cliente'];
    $anio = $_GET['anio'];
    $importe = $_GET['importe'];
    $duracion = $_GET['duracion'];
    $bim = $_GET['bim'];
    $breeam = $_GET['breeam'];
    $leed = $_GET['leed'];
    $otros_certificados = $_GET['otros_certificados'];
    $observaciones = $_GET['observaciones'];
    
    $sql = "INSERT INTO glpi_plugin_comproveedores_experiences 
    (`name`,
`plugin_comproveedores_experiencesstates_id`,
`intervencion_bovis`,
`plugin_comproveedores_experiencestypes_id`,
`plugin_comproveedores_communities_id`,
`cliente`,
`anio`,
`importe`,
`duracion`,
`bim`,
`breeam`,
`leed`,
`otros_certificados`,
`observaciones`,
`cv_id`,
`is_deleted`,
`externalid`,
`is_recursive`,
`entities_id`,
`otros_name`) 
    VALUES (replace(trim('{$name}'), '\n', ''),{$plugin_comproveedores_experiencesstates_id},
    {$experiencia_bovis},{$plugin_comproveedores_experiencestypes_id},
    {$plugin_comproveedores_communities_id},replace(trim('{$cliente}'), '\n', ''),{$anio},{$importe},{$duracion},
    {$bim},{$breeam},{$leed},{$otros_certificados},replace(trim('{$observaciones}'), '\n', ''),{$cv_id},0,0,0,0,'')";
    
    $result = $DB->query($sql);
    //echo $sql;


