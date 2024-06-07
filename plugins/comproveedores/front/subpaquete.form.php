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

	$PluginSubpaquete= new PluginComproveedoresSubpaquete();

	
	if(isset($_POST['add'])){
		$PluginSubpaquete->check(-1, CREATE, $_POST);
		$newID = $PluginSubpaquete->add($_POST);
	
		if($_SESSION['glpibackcreated']) {
			Html::redirect($PluginSubpaquete->getFormURL()."?id=".$newID);
		}

		Html::back();
	} else if(isset($_POST['update'])){
		$PluginSubpaquete->check($_POST['id'], UPDATE);
		$PluginSubpaquete->update($_POST);

		Html::back();
	} else if (isset($_POST["delete"])) {
		$_POST['fecha_fin']=date('Y-m-d H:i:s');
		$PluginSubpaquete->check($_POST['id'], DELETE);
		$PluginSubpaquete->delete($_POST);
		//Html::redirect($CFG_GLPI["root_doc"]."/plugins/comproveedores/front/cv.form.php");
		Html::back();

	} else if (isset($_POST["restore"])) {
		$PluginSubpaquete->check($_POST['id'], PURGE);
		$PluginSubpaquete->restore($_POST);
		Html::back();

	} else if (isset($_POST["purge"])) {
		$PluginSubpaquete->check($_POST['id'], PURGE);
		$PluginSubpaquete->delete($_POST, 1);
		
		Html::back();

	} else {
		$PluginSubpaquete->checkGlobal(READ);

		$plugin = new Plugin();
		if ($plugin->isActivated("environment")) {
			Html::header(PluginComproveedoresSubpaquete::getTypeName(2),
				'',"management","pluginenvironmentdisplay","comproveedores");
		} else {
			Html::header(PluginComproveedoresSubpaquete::getTypeName(2), '', "management",
				"plugincomproveedorescvmenu");	
		}

		if(empty($_GET['id'])){
			Search::show('PluginComproveedoresSubpaquete');
		}else{			
			$options['id']=$_GET['id'];
			$PluginSubpaquete->display($options);
		}

		Html::footer();
	} 
	?>