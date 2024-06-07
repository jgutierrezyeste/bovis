<?php
    include ("../../../inc/includes.php");
    GLOBAL $DB,$CFG_GLPI;
    $objCommonDBT=new CommonDBTM;
    
    $id = $_GET['id'];
    $cvid = $_GET['cv_id'];
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
    
    
    $sql="UPDATE glpi_plugin_comproveedores_experiences
SET
`name` = replace(trim('{$name}'), '\n', ''),
`plugin_comproveedores_experiencesstates_id` = {$plugin_comproveedores_experiencesstates_id},
`intervencion_bovis` = {$experiencia_bovis},
`plugin_comproveedores_experiencestypes_id` = {$plugin_comproveedores_experiencestypes_id},
`plugin_comproveedores_communities_id` = {$plugin_comproveedores_communities_id},
`cliente` = replace(trim('{$cliente}'), '\n', ''),
`anio` = {$anio},
`importe` = {$importe},
`duracion` = {$duracion},
`bim` = {$bim},
`breeam` = {$breeam},
`leed` = {$leed},
`otros_certificados` = {$otros_certificados},
`observaciones` = replace(trim('{$observaciones}'), '\n', ''),
`cv_id` = {$cvid},
`is_deleted` = 0,
`externalid` = 0,
`is_recursive` = 0,
`entities_id` = 0,
`otros_name` = ''
WHERE `id` = {$id}";
    
    
   $result = $DB->query($sql);
   //echo $sql;


