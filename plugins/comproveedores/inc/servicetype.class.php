
<?php
class PluginComproveedoresServicetype extends CommonDropdown{
	static $rightname="plugin_comproveedores";
	var $can_be_translated=true;

	static function getTypeName($nb=0){
		return _n('Tipo de servicio', 'Tipos de servicios', $nb, 'comproveedores');
	}
}
?>