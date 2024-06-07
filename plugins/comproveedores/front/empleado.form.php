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

	$PluginEmpleado= new PluginComproveedoresEmpleado();

	
	if(isset($_POST['add'])){
		$PluginEmpleado->check(-1, CREATE, $_POST);
		$newID = $PluginEmpleado->add($_POST);
	
		if($_SESSION['glpibackcreated']) {
			Html::redirect($PluginEmpleado->getFormURL()."?id=".$newID);
		}

		Html::back();
	} else if(isset($_POST['update'])){
		$PluginEmpleado->check($_POST['id'], UPDATE);
		$PluginEmpleado->update($_POST);

		Html::back();
	} else if (isset($_POST["delete"])) {
		$_POST['fecha_fin']=date('Y-m-d H:i:s');
		$PluginEmpleado->check($_POST['id'], DELETE);
		$PluginEmpleado->delete($_POST);
		//Html::redirect($CFG_GLPI["root_doc"]."/plugins/comproveedores/front/cv.form.php");
		Html::back();

	} else if (isset($_POST["restore"])) {
		$PluginEmpleado->check($_POST['id'], PURGE);
		$PluginEmpleado->restore($_POST);
		Html::back();

	} else if (isset($_POST["purge"])) {
		$PluginEmpleado->check($_POST['id'], PURGE);
		$PluginEmpleado->delete($_POST, 1);
		
		Html::back();

	} else {
		$PluginEmpleado->checkGlobal(READ);


		/*//////////////////////////////////////////////////////////
		// MUESTRA LA CABECERA DE LA PAGINA DEL . FROM EXPERIENCIAS
		//////////////////////////////////////////////////////////*/

		$plugin = new Plugin();
		if ($plugin->isActivated("environment")) {
			Html::header(PluginComproveedoresEmpleado::getTypeName(2),
				'',"management","pluginenvironmentdisplay","comproveedores");
		} else {
			Html::header(PluginComproveedoresEmpleado::getTypeName(2), '', "management",
				"plugincomproveedorescvmenu");	
		}

		/*//////////////////////////////////////////////////////////
		// MUESTRA EL REGISTRO CORRESPONDIENTE AL ID SI SE LE MANDA
		//	O LA LISTA DE TODOS LOS REGISTROS SI NO SE LE PASA EL PARAMETRO ID
		//////////////////////////////////////////////////////////*/

		if(empty($_GET['id'])){
			Search::show('PluginComproveedoresEmpleado');
		}else{			
			$options['id']=$_GET['id'];
			$PluginEmpleado->display($options);
		}

		Html::footer();
	} 
	?>