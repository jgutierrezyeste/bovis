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

define("PLUGIN_PLATAFORMAV_RELATION_LOCATION",1);

function plugin_portada_postinit() {
   global $PLUGIN_HOOKS;

   $PLUGIN_HOOKS['plugin_uninstall_after']['portada'] = array();
   $PLUGIN_HOOKS['item_purge']['portada']             = array();

   
}


/**
 * @return bool
**/
function plugin_portada_install() {
   global $DB;
 include_once(GLPI_ROOT."/plugins/portada/inc/profile.class.php");
 
	
   
   $DB->runFile(GLPI_ROOT . '/plugins/portada/sql/empty-2.0.0.sql');
 
   PluginPortadaProfile::initProfile();
   PluginPortadaProfile::createFirstAccess($_SESSION['glpiactiveprofile']['id']);


   return true;
}


/**
 * @return bool
**/
function plugin_portada_uninstall() {
   global $DB;
 
	
   $tables = array('glpi_plugin_portada_portadas',
                   'glpi_plugin_portada_portadatypes',
                   'glpi_plugin_portada_environments',
				   'glpi_plugin_portada_config',
                   'glpi_plugin_portada_relations',
                   'glpi_plugin_portada_portada_virtualcomputers',
                   'glpi_plugin_portada_portada_physicalcomputers',
				   'glpi_plugin_portada_state');

   foreach($tables as $table) {
      $DB->query("DROP TABLE `$table`");
   }
   
    $query = "DELETE
             FROM `glpi_profilerights`
             WHERE `name` IN ('plugin_portada', 'plugin_portada_open_ticket')";
	$DB->query($query);

   PluginPortadaMenu::removeRightsFromSession();
   PluginPortadaProfile::removeRightsFromSession();
 
   return true;
}

 
