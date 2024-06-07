<?php
	use Glpi\Event;

	include ("../../../inc/includes.php");

	$opt4['comments']  = false;
	$opt4['addicon']   = false;
        $opt4['width']     = '203px';
        
        if(!empty($_GET['idProvincia'])){
            $opt4['value']= $_GET['idTier'];
        }
	
	Dropdown::show('PluginComproveedoresTier',$opt4);

	
