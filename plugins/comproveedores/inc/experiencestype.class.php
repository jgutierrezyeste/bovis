<?php
class PluginComproveedoresExperiencestype extends CommonDropdown{
	static $rightname="plugin_comproveedores";
	var $can_be_translated=true;

	static function getTypeName($nb=0){
		return _n('Tipos de experiencias', 'Tipos de experiencias', $nb, 'comproveedores');
	}
}
?>