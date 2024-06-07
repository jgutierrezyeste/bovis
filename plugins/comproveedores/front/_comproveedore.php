<?php

/******************************************

	PLUGIN DE GESTION DE CURRICULUMS DE LOS PROVEEDORES


 ******************************************/

	include("../../../inc/includes.php");

	$plugin =new Plugin();

	if($plugin->isActivated("environment")){
		Html::header(PluginComproveedoresComproveedore::getTypeName(2),'',"management","pluginenvironmentdisplay","comproveedores");
	}else{
		Html::header(PluginComproveedoresComproveedore::getTypeName(2),'',"management","plugincomproveedoresmenu");
	}

	if(Session::haveRight("plugin_comproveedores",READ)
		/*|| Session::haveRight("config",UPDATE)*/){
		Search::show('PluginComproveedoresComproveedore');
	}else{
		Html::displayRightError();
	}
	Html::footer();