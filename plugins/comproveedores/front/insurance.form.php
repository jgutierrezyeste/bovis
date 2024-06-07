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

	$PluginInsurance= new PluginComproveedoresInsurance();

	
	if(isset($_POST['add'])){
		$PluginInsurance->check(-1, CREATE, $_POST);
		$newID = $PluginInsurance->add($_POST);
	
		if($_SESSION['glpibackcreated']) {
			Html::redirect($PluginInsurance->getFormURL()."?id=".$newID);
		}

		Html::back();
	} else if(isset($_POST['update'])){
		$PluginInsurance->check($_POST['id'], UPDATE);
		$PluginInsurance->update($_POST);

		Html::back();
	} else if (isset($_POST["delete"])) {
		$_POST['fecha_fin']=date('Y-m-d H:i:s');
		$PluginInsurance->check($_POST['id'], DELETE);
		$PluginInsurance->delete($_POST);
		//Html::redirect($CFG_GLPI["root_doc"]."/plugins/comproveedores/front/cv.form.php");
		Html::back();

	} else if (isset($_POST["restore"])) {
		$PluginInsurance->check($_POST['id'], PURGE);
		$PluginInsurance->restore($_POST);
		Html::back();

	} else if (isset($_POST["purge"])) {
		$PluginInsurance->check($_POST['id'], PURGE);
		$PluginInsurance->delete($_POST, 1);
		
		Html::back();

	} else {
		$PluginInsurance->checkGlobal(READ);


		/*//////////////////////////////////////////////////////////
		// MUESTRA LA CABECERA DE LA PAGINA DEL . FROM EXPERIENCIAS
		//////////////////////////////////////////////////////////*/

		$plugin = new Plugin();
		if ($plugin->isActivated("environment")) {
			Html::header(PluginComproveedoresInsurance::getTypeName(2),
				'',"management","pluginenvironmentdisplay","comproveedores");
		} else {
			Html::header(PluginComproveedoresInsurance::getTypeName(2), '', "management",
				"plugincomproveedorescvmenu");	
		}

		/*//////////////////////////////////////////////////////////
		// MUESTRA EL REGISTRO CORRESPONDIENTE AL ID SI SE LE MANDA
		//	O LA LISTA DE TODOS LOS REGISTROS SI NO SE LE PASA EL PARAMETRO ID
		//////////////////////////////////////////////////////////*/

		if(empty($_GET['id'])){
			Search::show('PluginComproveedoresInsurance');
		}else{			
			$options['id']=$_GET['id'];
			$PluginInsurance->display($options);
		}

		Html::footer();
	} 
	?>