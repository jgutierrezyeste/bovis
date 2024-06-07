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
 @author    Fotex: Daniel Torvisco, Julio Alberto Marquez.
 @since     version 1.0
 --------------------------------------------------------------------------
 */

// Init the hooks of the plugins -Needed
function plugin_init_portada() {
   global $PLUGIN_HOOKS,$CFG_GLPI;

   $PLUGIN_HOOKS['csrf_compliant']['portada'] = true;
   $PLUGIN_HOOKS['change_profile']['portada'] = array('PluginPortadaProfile', 'initProfile');
   $PLUGIN_HOOKS['assign_to_ticket']['portada'] = true;
   
   
	
	


   Plugin::registerClass('PluginPortadaProfile', array('addtabon' => 'Profile'));
   
	
	
	  
	
  
  
   //if glpi is loaded
   if (Session::getLoginUserID()) {
	 
      $plugin = new Plugin();
      if (!$plugin->isActivated('environment')
         && Session::haveRight("plugin_portada", READ)) {

		$PLUGIN_HOOKS['menu_toadd']['portada'] = array('assets' => 'PluginPortadaMenu');
      }
	 
		$PLUGIN_HOOKS['use_massive_action']['portada'] = 1;
		$PLUGIN_HOOKS['config_page']['portada'] = 'front/config.form.php';
   }

   
   // Import from Data_Injection plugin
   $PLUGIN_HOOKS['data_injection']['portada'] = "plugin_portada_data_injection_variables";

   // Import webservice
   $PLUGIN_HOOKS['webservices']['portada'] = 'plugin_portada_registerMethods';

   // End init, when all types are registered
   $PLUGIN_HOOKS['post_init']['portada'] = 'plugin_portada_postinit';
}

//Get the name and the version of the plugin - Needed
function plugin_version_portada() {

   return array('name'           => __('Portada', 'portada'),
                'version'        => '2.0.2',
                'author'         => 'Bovis(Fotex:Daniel Torvisco, Maria Rosa Cambero)',
                'license'        => 'GPLv3+',
                'homepage'       => 'http://www.fotex.es',
                'minGlpiVersion' => '0.90');
				
}


function plugin_portada_check_prerequisites() {

   if (version_compare(GLPI_VERSION,'9.2','lt') || version_compare(GLPI_VERSION,'9.3','ge')) {
      echo "This plugin requires GLPI >= 9.1 and GLPI < 9.2";
      return false;
   }
   return true;
}


function plugin_portada_check_config() {
   return true;
}


