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

	$PluginFinancial= new PluginComproveedoresFinancial();
	$PluginCv= new PluginComproveedoresCv();
	$PluginAnnualBilling= new PluginComproveedoresAnnualbilling();
	
	if(isset($_POST['add'])){

		//cambiar formato de capital social
		$_POST['capital_social']=str_replace('.', '', $_POST['capital_social']);
		$_POST['capital_social']=str_replace(',', '.', $_POST['capital_social']);

		$PluginCv->check(-1, CREATE, $_POST);
		$newID = $PluginCv->add($_POST);
	
		//comprobamos si existe o no, los años de sinisestralidad, y en funcion de que exista o no, añadimos o modificamos
		InsertAndUpdateAnnualBilling($DB, $PluginAnnualBilling);
		
		if($_SESSION['glpibackcreated']) {
			Html::redirect($PluginFinancial->getFormURL()."?id=".$newID);
		}

		Html::back();
	} else if(isset($_POST['update'])){

		//cambiar formato de capital social
		$_POST['capital_social']=str_replace('.', '', $_POST['capital_social']);
		$_POST['capital_social']=str_replace(',', '.', $_POST['capital_social']);

		$PluginCv->check($_POST['id'], UPDATE);
		$PluginCv->update($_POST);

		//comprobamos si existe o no, los años de sinisestralidad, y en funcion de que exista o no, añadimos o modificamos
		InsertAndUpdateAnnualBilling($DB, $PluginAnnualBilling);
		
		Html::back();
	} else if (isset($_POST["delete"])) {
		$_POST['fecha_fin']=date('Y-m-d H:i:s');
		$PluginCv->check($_POST['id'], DELETE);
		$PluginCv->delete($_POST);
		Html::back();

	} else if (isset($_POST["restore"])) {
		$PluginCv->check($_POST['id'], PURGE);
		$PluginCv->restore($_POST);
		Html::back();

	} else if (isset($_POST["purge"])) {
		
		$query ="UPDATE `glpi_plugin_comproveedores_cvs` SET `capital_social` = NULL WHERE `glpi_plugin_comproveedores_cvs`.`id` =".$_POST['id'];

		$DB->query($query);

		//eliminamos los indice se siniestralidad de CV_ID
		$query ="SELECT id FROM glpi_plugin_comproveedores_annualbillings WHERE cv_id=".$_POST['id'];

			$result = $DB->query($query);

			if($result->num_rows!=0){

				while ($data=$DB->fetch_array($result)) {
					$PluginAnnualBilling->check($data['id'], PURGE);
					$PluginAnnualBilling->delete($data, 1);
				}
			}
		
		Html::back();

	} else {
		$PluginCv->checkGlobal(READ);

		$plugin = new Plugin();
		if ($plugin->isActivated("environment")) {
			Html::header(PluginComproveedoresFinancial::getTypeName(2),
				'',"management","pluginenvironmentdisplay","comproveedores");
		} else {
			Html::header(PluginComproveedoresFinancial::getTypeName(2), '', "management",
				"plugincomproveedorescvmenu");	
		}

		if(empty($_GET['id'])){
			Search::show('PluginComproveedoresFinancial');
		}else{			
			$options['id']=$_GET['id'];
			$PluginFinancial->display($options);
		}

		Html::footer();
	} 

	function InsertAndUpdateAnnualBilling($DB, $PluginAnnualBilling){

		for($i=0; $i<3; $i++){
			$query ="SELECT * FROM glpi_plugin_comproveedores_annualbillings WHERE cv_id=".$_POST['id']." and anio='".$_POST['anio'.$i]."-00-00'";

			$result = $DB->query($query);

			if($result->num_rows!=0){

				while ($data=$DB->fetch_array($result)) {

					$data['anio']=$_POST['anio'.$i]."-00-00";
					$data['facturacion']=str_replace('.', '', $_POST['facturacion'.$i].'000');
					$data['beneficios_impuestos']=str_replace('.', '', $_POST['beneficios_impuestos'.$i].'000');
					$data['resultado']=str_replace('.', '', $_POST['resultado'.$i].'000');
					$data['total_activo']=str_replace('.', '', $_POST['total_activo'.$i].'000');
					$data['activo_circulante']=str_replace('.', '', $_POST['activo_circulante'.$i].'000');
					$data['pasivo_circulante']=str_replace('.', '', $_POST['pasivo_circulante'.$i].'000');
					$data['cash_flow']=str_replace('.', '', $_POST['cash_flow'.$i].'000');
					$data['fondos_propios']=str_replace('.', '', $_POST['fondos_propios'.$i].'000');
					$data['recursos_ajenos']=str_replace('.', '', $_POST['recursos_ajenos'.$i].'000');
					$data['cv_id']=$_POST['id'];

					$PluginAnnualBilling->check($data['id'], UPDATE);
					$PluginAnnualBilling->update($data);
				}
			}
			else{

					$data['anio']=$_POST['anio'.$i]."-00-00";
					$data['facturacion']=str_replace('.', '', $_POST['facturacion'.$i].'000');
					$data['beneficios_impuestos']=str_replace('.', '', $_POST['beneficios_impuestos'.$i].'000');
					$data['resultado']=str_replace('.', '', $_POST['resultado'.$i].'000');
					$data['total_activo']=str_replace('.', '', $_POST['total_activo'.$i].'000');
					$data['activo_circulante']=str_replace('.', '', $_POST['activo_circulante'.$i].'000');
					$data['pasivo_circulante']=str_replace('.', '', $_POST['pasivo_circulante'.$i].'000');
					$data['cash_flow']=str_replace('.', '', $_POST['cash_flow'.$i].'000');
					$data['fondos_propios']=str_replace('.', '', $_POST['fondos_propios'.$i].'000');
					$data['recursos_ajenos']=str_replace('.', '', $_POST['recursos_ajenos'.$i].'000');
					$data['cv_id']=$_POST['id'];


				$PluginAnnualBilling->check(-1, CREATE, $data);
				$PluginAnnualBilling->add($data);
			}
		}
	}
	?>