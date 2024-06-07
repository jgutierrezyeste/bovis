
<?php
class PluginComproveedoresPreviousnamescompany extends CommonDBTM{
	static $rightname="plugin_comproveedores";
	var $can_be_translated=true;

	static function getTypeName($nb=0){
		return _n('Nombre anteriores de la empresa', 'Nombre anteriores de la empresa', $nb, 'comproveedores');
	}
}
?>