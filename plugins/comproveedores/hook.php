<?php

/******************************************

	PLUGIN DE GESTION DE CURRICULUMS DE LOS PROVEEDORES


 ******************************************/

	function plugin_comproveedores_postinit(){

		global $PLUGIN_HOOKS;

		$PLUGIN_HOOKS['plugin_uninstall_after']['comproveedores']	=	array();
		$PLUGIN_HOOKS['item_purge']['comproveedores']	=	array();

		foreach (PluginComproveedoresCv::getTypes(true) as $type){
			
			//CommonGLPI::registerStandardTab($type,'PluginComproveedoresCv');
			//CommonGLPI::registerStandardTab($type,'PluginComproveedoresUser');
			//CommonGLPI::registerStandardTab($type,'PluginComproveedoresExperience');
		}


	}

	function plugin_comproveedores_install(){

		global $DB;

		if (!TableExists("glpi_plugin_comproveedores_comproveedores")) {
			$DB->runFile(GLPI_ROOT . '/plugins/comproveedores/sql/empty-1.0.0.sql');
			$DB->runFile(GLPI_ROOT . '/plugins/comproveedores/sql/categorias.sql');

		}
		if (!FieldExists("glpi_profiles","create_cv_on_login")) {
			$DB->runFile(GLPI_ROOT . '/plugins/comproveedores/sql/empty_alter-1.0.0.sql');
		}else if (!FieldExists("glpi_suppliers","cv_id")) {
			$DB->runFile(GLPI_ROOT . '/plugins/comproveedores/sql/empty_alter-2.0.0.sql');
		}

		PluginComproveedoresProfile::initProfile();
		PluginComproveedoresProfile::createFirstAccess($_SESSION['glpiactiveprofile']['id']);


		return true;

	}

	function plugin_comproveedores_uninstall(){
		global $DB;
		$DB->runFile(GLPI_ROOT . '/plugins/comproveedores/sql/delete_relation.sql');
		return true;

	}

	function plugin_comproveedores_getDropdown(){
	// Aquí declararemos los dropdown que usemos en los formularios de este plugin 
	//y su informacion sea almacenada y mostrada desde tablas de este plugin	
	}

	function plugin_comproveedores_getAddSearchOptions($itemtype) {
	//Aqui insertaremos las opciones de busqueda que queramos que aparezcan en la busqueda principal de un activo concreto
	}

	function plugin_comproveedores_MassiveActions($type) {
	//Aqui añadiremos acciones a las acciones masivas cono la de transferir un activo de entidad.
	}

	function plugin_comproveedores_registerMethods(){

	}

	function plugin_comproveedores_AssingToTicket($tipes){

	}