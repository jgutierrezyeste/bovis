<?php

/******************************************

	PLUGIN DE GESTION DE CURRICULUMS DE LOS PROVEEDORES


 ******************************************/



	include ("../../../inc/includes.php");


	
	if(!isset($_GET["id"])) {
		$_GET["id"] = "";
	}
	if (!isset($_GET["withtemplate"])) {
		$_GET["withtemplate"] = "";
	}

	$PluginComproveedores= new PluginComproveedoresComproveedore();


	if(isset($_POST['add'])){
		$PluginComproveedores->check(-1, CREATE, $_POST);
		$newID = $PluginComproveedores->add($_POST);
		if ($_SESSION['glpibackcreated']) {
			Html::redirect($PluginComproveedores->getFormURL()."?id=".$newID);
		}
		Html::back();
	} else if(isset($_POST['update'])){
		$PluginComproveedores->check($_POST['id'], UPDATE);
		$PluginComproveedores->update($_POST);
		Html::back();
	} else if (isset($_POST["delete"])) {
		$_POST['fecha_fin']=date('Y-m-d H:i:s');
		$PluginComproveedores->check($_POST['id'], DELETE);
		$PluginComproveedores->delete($_POST);
		Html::redirect($CFG_GLPI["root_doc"]."/plugins/comproveedores/front/comproveedore.php");

	} else if (isset($_POST["restore"])) {
		$PluginComproveedores->check($_POST['id'], PURGE);
		$PluginComproveedores->restore($_POST);
		Html::back();

	} else if (isset($_POST["purge"])) {
		$PluginComproveedores->check($_POST['id'], PURGE);
		$PluginComproveedores->delete($_POST, 1);

		Html::redirect($CFG_GLPI["root_doc"]."/plugins/comproveedores/front/comproveedore.php");
	} 	else {


		$PluginComproveedores->checkGlobal(READ);
		
		//check environment meta-plugin installtion for change header
		$plugin = new Plugin();
		if ($plugin->isActivated("environment")) {
			Html::header(PluginComproveedoresComproveedore::getTypeName(2),
				'',"management","pluginenvironmentdisplay","comproveedores");
		} else {
			Html::header(PluginComproveedoresComproveedore::getTypeName(2), '', "management",
				"plugincomproveedoresmenu");
		}
		$PluginComproveedores->display($_GET);

		Html::footer();
	} 
	?>