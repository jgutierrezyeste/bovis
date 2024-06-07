<?php
    include ("../../../inc/includes.php");
    GLOBAL $DB,$CFG_GLPI;
    $objCommonDBT=new CommonDBTM;
    
$Id                 = $_GET['id'];
$cvid               = $_GET['cvid'];
$name               = $_GET['name'];
$cif                = $_GET['cif'];
$suppliertypes_id   = $_GET['suppliertypes_id'];
$address            = $_GET['address'];
$postcode           = $_GET['postcode'];
$town               = $_GET['town'];
$state              = $_GET['state'];
$country             = $_GET['country'];
$website            = $_GET['website'];
$phonenumber        = $_GET['phonenumber'];
$fax                = $_GET['fax'];
$email              = $_GET['email'];
$cvid               = $_GET['cvid'];  

        $hoy            = getdate();
        $today          = date('Y-m-d');  
  

$sql ="UPDATE `glpi_suppliers` SET 
`name` = '{$name}', `suppliertypes_id` = {$suppliertypes_id}, `address` = '{$address}', 
`postcode` = '{$postcode}', `town` = '{$town}', `state` = '{$state}', `country` = '{$country}', `website` = '{$website}', 
`phonenumber` = '{$phonenumber}', `fax` = '{$fax}', 
`email` = '{$email}', `date_mod` = '{$today}', `cv_id` = {$cvid}, 
`cif` =  '{$cif}'
WHERE ID =  {$Id} ";

$result = $DB->query($sql);
//echo $sql;


