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

	$PluginlistSpecialty= new PluginComproveedoresListspecialty();

	
	if(isset($_POST['add'])){
            
		$PluginlistSpecialty->check(-1, CREATE, $_POST);

		$newID = $PluginlistSpecialty->add($_POST);
	
		if($_SESSION['glpibackcreated']) {
			Html::redirect($PluginlistSpecialty->getFormURL()."?id=".$newID);
		}

		Html::back();
	} else if(isset($_POST['update'])){
		$PluginlistSpecialty->check($_POST['id'], UPDATE);
		$PluginlistSpecialty->update($_POST);

		Html::back();
	} else if (isset($_POST["delete"])) {
		$_POST['fecha_fin']=date('Y-m-d H:i:s');
		$PluginlistSpecialty->check($_POST['id'], DELETE);
		$PluginlistSpecialty->delete($_POST);
		Html::back();

	} else if (isset($_POST["restore"])) {
		$PluginlistSpecialty->check($_POST['id'], PURGE);
		$PluginlistSpecialty->restore($_POST);
		Html::back();

	} else if (isset($_POST["purge"])) {
		$PluginlistSpecialty->check($_POST['id'], PURGE);
		$PluginlistSpecialty->delete($_POST, 1);

		Html::back();
	}if(isset($_GET['add_nueva_especialidad'])){

		$query="INSERT INTO `glpi_plugin_comproveedores_specialties` (`id`, `glpi_plugin_comproveedores_categories_id`, `name`) VALUES (NULL, '".$_GET['categoria_nueva_especialidad']."', '".$_GET['nombre_especialidad']."')";
		$DB->query($query);
		
		Html::back();
	}else {
		$PluginlistSpecialty->checkGlobal(READ);


		/*//////////////////////////////////////////////////////////
		// MUESTRA LA CABECERA DE LA PAGINA DEL . FROM EXPERIENCIAS
		//////////////////////////////////////////////////////////*/

		$plugin = new Plugin();
		if ($plugin->isActivated("environment")) {
			Html::header(PluginComproveedoresListspecialty::getTypeName(2),
				'',"management","pluginenvironmentdisplay","comproveedores");
		} else {
			Html::header(PluginComproveedoresListspecialty::getTypeName(2), '', "management",
				"plugincomproveedorescvmenu");
		}

		/*//////////////////////////////////////////////////////////
		// MUESTRA EL REGISTRO CORRESPONDIENTE AL ID SI SE LE MANDA
		//	O LA LISTA DE TODOS LOS REGISTROS SI NO SE LE PASA EL PARAMETRO ID
		//////////////////////////////////////////////////////////*/

		if(empty($_GET['id'])){
			Search::show('PluginComproveedoresListspecialty');
		}else{			
			$options['id']=$_GET['id'];
			$PluginExperience->display($options);
		}

		Html::footer();
	} 
	?>