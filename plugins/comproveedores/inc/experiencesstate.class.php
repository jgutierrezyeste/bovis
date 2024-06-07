
<?php
class PluginComproveedoresExperiencesstate extends CommonDropdown{    
	static $rightname="plugin_comproveedores";
	var $can_be_translated=true;

	static function getTypeName($nb=0){
		return _n('Estado Experiencia', 'Estados Experiencias', $nb, 'comproveedores');
	}
}
?>