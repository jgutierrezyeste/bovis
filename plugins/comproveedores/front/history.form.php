<?php

/******************************************

	PLUGIN DE GESTION DE CURRICULUMS DE LOS PROVEEDORES


 ******************************************/

	use Glpi\Event;

	include ("../../../inc/includes.php");
	global $DB;
	Session::checkLoginUser();
	
	if(!isset($_GET["id"])) {
		$_GET["id"] = "";
	}
	if (!isset($_GET["withtemplate"])) {
		$_GET["withtemplate"] = "";
	}

	$PluginHistory= new PluginComproveedoresHistory();

	
	if(isset($_POST['add'])){
		$PluginHistory->check(-1, CREATE, $_POST);
		$newID = $PluginHistory->add($_POST);
	
		if($_SESSION['glpibackcreated']) {
			Html::redirect($PluginHistory->getFormURL()."?id=".$newID);
		}

		Html::back();
	} else if(isset($_POST['update'])){
		$PluginHistory->check($_POST['id'], UPDATE);
		$PluginHistory->update($_POST);

		Html::back();
	} else if (isset($_POST["delete"])) {
		$_POST['fecha_fin']=date('Y-m-d H:i:s');
		$PluginHistory->check($_POST['id'], DELETE);
		$PluginHistory->delete($_POST);
		//Html::redirect($CFG_GLPI["root_doc"]."/plugins/comproveedores/front/cv.form.php");
		Html::back();

	} else if (isset($_POST["restore"])) {
		$PluginHistory->check($_POST['id'], PURGE);
		$PluginHistory->restore($_POST);
		Html::back();

	} else if (isset($_POST["purge"])) {
		$PluginHistory->check($_POST['id'], PURGE);
		$PluginHistory->delete($_POST, 1);
		
		Html::back();

	} else {
		$PluginHistory->checkGlobal(READ);

		$plugin = new Plugin();
		if ($plugin->isActivated("environment")) {
			Html::header(PluginComproveedoresHistory::getTypeName(2),
				'',"management","pluginenvironmentdisplay","comproveedores");
		} else {
			Html::header(PluginComproveedoresHistory::getTypeName(2), '', "management",
				"plugincomproveedorescvmenu");	
		}

		if(empty($_GET['id'])){
			Search::show('PluginComproveedoresHistory');
		}else{			
			$options['id']=$_GET['id'];
			$PluginHistory->display($options);
		}

		Html::footer();
	} 
	?>