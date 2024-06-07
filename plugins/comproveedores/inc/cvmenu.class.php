
<?php

/******************************************

	PLUGIN DE GESTION DE CURRICULUMS DE LOS PROVEEDORES


 ******************************************/


class PluginComproveedoresCvmenu extends CommonGLPI {
	
	static $rightname = 'plugin_comproveedores';
	
	static function getMenuName(){
		return _n("Gestión Curriculum","Gestion Curriculum",2,"comproveedores");
	}
	
	static function getMenuContent(){
		$menu = array();
		
		$menu['title'] = self::getMenuName();
		$menu['page']  = "/plugins/comproveedores/front/cv.form.php";
		
		//$menu['links']['add'] = PluginComproveedoresCv::getFormURL(false);
		
		return $menu;
	}
	
	static function removeRightsFromSession() {

      if (isset($_SESSION['glpimenu']['management']['types']['PluginComproveedoresCvmenu'])) {
         unset($_SESSION['glpimenu']['management']['types']['PluginComproveedoresCvmenu']);
      }
      if (isset($_SESSION['glpimenu']['management']['content']['plugincomproveedorescvmenu'])) {
         unset($_SESSION['glpimenu']['management']['content']['plugincomproveedorescvmenu']);
      }
   }
	
	
} 