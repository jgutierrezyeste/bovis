<?php

/******************************************

	PLUGIN DE GESTION DE CURRICULUMS DE LOS PROVEEDORES


 ******************************************/


	function plugin_init_comproveedores(){

		global $PLUGIN_HOOKS, $CFG_GLPI;
		$PLUGIN_HOOKS['csrf_compliant']['comproveedores'] = true;


		Plugin::registerClass('PluginComproveedoresComproveedore', array('linkuser_types' => true,
			'linkuser_tech_types' => true,
			'linkgroup_types' => true,
			'linkgroup_tech_types' => true,
			'infocom_types' => true,
			'document_types' => true,
			'contract_types' => true,
			'ticket_types' => true,
			'helpdesk_visible_types' => true));

		Plugin::registerClass('PluginComproveedoresProfile', array('addtabon' => 'Profile'));
		if (Session::getLoginUserID()) {
			$plugin = new Plugin();
			if (!$plugin->isActivated('environment') && Session::haveRight("plugin_comproveedores", READ)) {
				$PLUGIN_HOOKS['menu_toadd']['comproveedores'] = array('management' => 'PluginComproveedoresCvmenu');
			}

			$PLUGIN_HOOKS['use_massive_action']['comproveedores'] = 1;
		}

		
			
		//Plugin::registerClass('Contract_Supplier', array('addtabon' => '../plugin/comproveedores/inc/PluginComproveedoresCv'));
		Plugin::registerClass('PluginComproveedoresCv', array('addtabon' => 'Supplier'));
		Plugin::registerClass('PluginComproveedoresUser', array('addtabon' => 'Supplier'));
		Plugin::registerClass('PluginComproveedoresExperience', array('addtabon' => 'Supplier'));
                Plugin::registerClass('PluginComproveedoresExperiencesState', array('addtabon' => 'Supplier'));
                Plugin::registerClass('PluginComproveedoresExperiencestype', array('addtabon' => 'Supplier'));                
		Plugin::registerClass('PluginComproveedoresListspecialty', array('addtabon' => 'Supplier'));
		Plugin::registerClass('PluginComproveedoresEmpleado', array('addtabon' => 'Supplier'));
		Plugin::registerClass('PluginComproveedoresInsurance', array('addtabon' => 'Supplier'));
		Plugin::registerClass('PluginComproveedoresIntegratedmanagementsystem', array('addtabon' => 'Supplier'));
		Plugin::registerClass('PluginComproveedoresFinancial', array('addtabon' => 'Supplier'));
		Plugin::registerClass('PluginComproveedoresValuation', array('addtabon' => 'Supplier'));
                Plugin::registerClass('PluginComproveedoresHistory', array('addtabon' => 'Supplier'));
                Plugin::registerClass('PluginComproveedoresTier', array('addtabon' => 'Supplier'));
                Plugin::registerClass('PluginComproveedoresProvince', array('addtabon' => 'Supplier'));
                Plugin::registerClass('PluginComproveedoresCommunity', array('addtabon' => 'Supplier'));
                Plugin::registerClass('PluginComproveedoresAmbito', array('addtabon' => 'Supplier'));
                
                
		$PLUGIN_HOOKS['post_init']['comproveedores'] = 'plugin_comproveedores_postinit';
		$PLUGIN_HOOKS['add_css']['comproveedores']   = "cvStyle.css";
	}

	function plugin_version_comproveedores(){
		return array('name' => 'Gestion avanzada de proveedores',
			'version' => '1.0.0',
			'author' => 'Fotex: Daniel Torvisco',
			'license' => 'GPLv3+',
			'homepage' => 'https://www.fotex.es',
			'minGlpiVersion' => '9.2');
	}

	function plugin_comproveedores_check_prerequisites(){
		if (version_compare(GLPI_VERSION, '9.1', 'lt') || version_compare(GLPI_VERSION, '9.3', 'ge')) {
			echo "This plugin requires GLPI >= 9.2 and GLPI < 9.3";
			return false;
		}
		return true;
	}

	function plugin_comproveedores_check_config(){

		return true;
	}