<?php
    include ("../../../inc/includes.php");
    GLOBAL $DB,$CFG_GLPI;
    $objCommonDBT=new CommonDBTM;
    
    $cvid           = $_GET['cvid'];
    $denominacion   = $_GET['denominacion'];

    $sql ="INSERT INTO `glpi_suppliers`
(`name`, `suppliertypes_id`, `proveedortypes_id`, `address`, 
`postcode`, `town`, `state`, `country`, `website`, 
`phonenumber`, `fax`, 
`email`, `date_mod`, `cv_id`, 
`cif`) 
VALUES ()";


    $result = $DB->query($sql);
    //echo $sql;


