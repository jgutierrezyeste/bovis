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

/**
 * Class PluginAppliancesMenu
**/
class PluginPortadaMenu extends CommonGLPI {
   static $rightname = 'plugin_portada';


   /**
    * Return the localized name of the current Type
    *
    * @return translated
   **/
    static function getMenuName() {
      return _n('Plataforma Virtual', 'Plataforma Virtual', 2, 'portada');
   }

   /**
    * @return array
   **/
   static function getMenuContent() {

      $menu                                           = array();
      $menu['title']                                  = self::getMenuName();
      //$menu['page']                                   = "/plugins/portada/front/portada.php";
	  
	  // $menu['options']['portada']['links']['config'] = PluginPortadaConfig::getFormURL(false);
	  // $menu['options']['config']['title'] = __('Setup');
      // $menu['options']['config']['page']  = PluginPortadaConfig::getFormURL(false);
	  
      //$menu['links']['search']                        = PluginPortadaPortada::getSearchURL(false);
	  $menu['links']['config']                        = PluginPortadaConfig::getFormURL(false);
      //if (PluginPortadaPortada::canCreate()) {
        // $menu['links']['add']                        = PluginPortadaPortada::getFormURL(false);
		 $menu['links']['add'] = '/plugins/portada/front/setup.templates.php?add=1';
      //}
	  
         $menu['links']['template'] = '/plugins/portada/front/setup.templates.php?add=0';                
      

      return $menu;
   }


   static function removeRightsFromSession() {

      if (isset($_SESSION['glpimenu']['tools']['types']['PluginPortadaMenu'])) {
         unset($_SESSION['glpimenu']['tools']['types']['PluginPortadaMenu']);
      }
      if (isset($_SESSION['glpimenu']['tools']['content']['pluginportadamenu'])) {
         unset($_SESSION['glpimenu']['tools']['content']['pluginportadamenu']);
      }
   }
}