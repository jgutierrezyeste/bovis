
<?php

/******************************************

	PLUGIN DE GESTION DE CURRICULUMS DE LOS PROVEEDORES


 ******************************************/


class PluginComproveedoresMenu extends CommonGLPI {
	
	static $rightname = 'plugin_comproveedores';
	
	static function getMenuName(){
		return _n("Gestion de proveedor","Gestionar CV de proveedores",2,"comproveedores");
	}
	
	static function getMenuContent(){
		$menu = array();
		
		$menu['title'] = self::getMenuName();
		$menu['page']  = "/plugins/comproveedores/front/comproveedore.php";
		
		$menu['links']['search'] = PluginComproveedoresComproveedore::getSearchURL(false);
		$menu['links']['add'] = PluginComproveedoresComproveedore::getFormURL(false);
		
		return $menu;
	}
	
	static function removeRightsFromSession() {

      if (isset($_SESSION['glpimenu']['management']['types']['PluginComproveedoresMenu'])) {
         unset($_SESSION['glpimenu']['management']['types']['PluginComproveedoresMenu']);
      }
      if (isset($_SESSION['glpimenu']['management']['content']['plugincomproveedoresmenu'])) {
         unset($_SESSION['glpimenu']['management']['content']['plugincomproveedoresmenu']);
      }
   }
	
	
} 