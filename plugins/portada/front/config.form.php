<?php
/*
 * @version version 1.0.0
 -------------------------------------------------------------------------
 portada plugin for GLPI
 Copyright (C) 2014-2016 by the portada Development Team.

 https://www.fotex.es
 -------------------------------------------------------------------------

 LICENSE

 @package   portada
 @author    Fotex: Daniel Torvisco, Maria Rosa Cambero.
 @since     version 1.0
 --------------------------------------------------------------------------
 */

include ('../../../inc/includes.php');

Session::checkRight("config", UPDATE);

$plugin = new Plugin();
if ($plugin->isActivated("portada")) {
   $config = new PluginPortadaConfig();
   if (isset($_POST["add"])) {
            /*echo "Hola";
            echo "<br>";
	   echo "Realizando";
           echo "<br>";*/
           if($_POST['id']!=''){
               $config->editPortada($_POST);
               //echo "Actualización";
           }else{
               //echo "Adición";
               $config->addPortada($_POST);
           }
           //var_dump($_POST);
           // die();
      Html::back();
   } if (isset($_GET["drop"])) {
	
	$config->dropPortada($_GET);	
       
   } else {
       Html::header('Portada', '', "assets", "pluginportadamenu", "config");
       $config->showForm();
       Html::footer();
   }
} else {
      Html::header(__('Setup'),'',"config","plugins");
      echo "<div align='center'><br><br>";
      echo "<img src=\"".$CFG_GLPI["root_doc"]."/pics/warning.png\" alt=\"warning\"><br><br>";
      echo "<b>".__('Please activate the plugin', 'portada')."</b></div>";
}
?>