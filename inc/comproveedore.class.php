<?php

/******************************************

	PLUGIN DE GESTION DE CURRICULUMS DE LOS PROVEEDORES


 ******************************************/

	class PluginComproveedoresComproveedore extends CommonDBTM{

		static $types = array('Computer');

		static $rightname="plugin_comproveedores";


		static function getTypeName($nb=0){
			return "Gestión de Proveedores";
		}

		function getSearchOptions(){

			$tab = array();

			$tab['common'] = ('CVs');

			$tab[1]['table']	=$this->getTable();
			$tab[1]['field']	='name';
			$tab[1]['name']		=__('Name');
			$tab[1]['datatype']		='itemlink';
			$tab[1]['itemlink_type']	=$this->getTable();

			return $tab;

		}

		function defineTabs($options=array()){
			$ong = array();

			$this->addDefaultFormTab($ong);
			$this->addStandardTab('PluginComproveedoresCv', $ong, $options);
			

			return $ong;
		}

		function registerType($type){
			if(!in_array($type, self::$types)){
				self::$types[]= $type;
			}		
		}

		static function getTypes($all=false) {
			if ($all) {
				return self::$types;
			}
    // Only allowed types
			$types = self::$types;
			foreach ($types as $key => $type) {
				if (!($item = getItemForItemtype($type))) {
					continue;
				}

				if (!$item->canView()) {
					unset($types[$key]);
				}
			}
			return $types;
		}

		function showForm($ID,$options=array()){

			var_dump($options);

			$this->initForm($ID, $options);
			$this->showFormHeader($options);
			echo "<tr class='tab_bg_1'>";

			echo "<td>".__('Name')."</td>";
			echo "<td>";
			Html::autocompletionTextField($this,"name");
			echo "</td>";

			echo "<td>".__('Status')."</td>";
			echo "<td>";
			State::dropdown(array('value' => $this->fields["states_id"]));
			echo "</td>";

			

			$this->showFormButtons($options);
		}

		

	}