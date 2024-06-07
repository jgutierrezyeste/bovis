<?php
	use Glpi\Event;

	include ("../../../inc/includes.php");

	$opt4['comments']  = false;
	$opt4['addicon']   = false;
        $opt4['width']     = '203px';
	$opt4['condition'] = 'plugin_comproveedores_communities_id='.$_GET['idComunidad'];
        
        if(!empty($_GET['idProvincia'])){
            $opt4['value']= $_GET['idProvincia'];
        }
	
	Dropdown::show('PluginComproveedoresProvince',$opt4);

	
