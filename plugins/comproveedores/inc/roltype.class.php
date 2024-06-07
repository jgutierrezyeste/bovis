<?php
class PluginComproveedoresRoltype extends CommonDropdown{
	static $rightname="plugin_comproveedores";
	var $can_be_translated=true;

	static function getTypeName($nb=0){
		return _n('Tipo rol', 'Tipo rol', $nb, 'comproveedores');
	}
}
?>