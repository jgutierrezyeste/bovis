<?php


use Glpi\Event;


include ("../../../inc/includes.php");




GLOBAL $DB,$CFG_GLPI;


			$query ="SELECT * FROM glpi_plugin_comproveedores_experiences WHERE id=".$_GET['idExperiencia'];

			$objCommonDBT=new CommonDBTM;
			$objExperiencia=new PluginComproveedoresExperience;

			$result = $DB->query($query);


			while ($data=$DB->fetch_array($result)) {

			$opt['comments']= false;
			$opt['addicon']= false;


			$opt3['comments']= false;
			$opt3['addicon']= false;
			$opt3['value']=  $data["plugin_comproveedores_communities_id"];

			$opt2['comments']= false;
			$opt2['addicon']= false;
			$opt2['value']=  $data["plugin_comproveedores_experiencestypes_id"];


			echo"<table class='tab_cadre_fixe'><tbody>";
			echo"<tr class='headerRow'>";

			echo Html::hidden('idExperiencia', ['value' => $_GET['idExperiencia']]);

			echo"<th colspan='9' style='background-color:#BDBDBD; border-top: 2px solid #BDBDBD; border-left: 2px solid #BDBDBD; border-right: 2px solid #BDBDBD;'>Experiencia</th></tr>";
			echo"<tr class='tab_bg_1 center'  style='background-color:#D8D8D8; border: 20px solid #BDBDDB;'>";

			echo "<td style='font-weight:bold; background-color:#E6E6E6; border-top: 2px solid #BDBDBD; border-left: 2px solid #BDBDBD; border-right: 2px solid #BDBDBD;'>" . __('Proy') . "</td>";

			echo "<td style='font-weight:bold; background-color:#E6E6E6; border-top: 2px solid #BDBDBD; border-right: 2px solid #BDBDBD;'>" . __('Estado') . "</td>";

			echo  "<td style='font-weight:bold; background-color:#E6E6E6; border-top: 2px solid #BDBDBD; border-right: 2px solid #BDBDBD;' class='tipos_experiencias'>" . __('Exper.') . "</td>";

			echo "<td style='width:100px; font-weight:bold; background-color:#E6E6E6; border-top: 2px solid #BDBDBD; border-right: 2px solid #BDBDBD;'>" . __('Meses') . "</td>";

			echo "<td style='width:10%;font-weight:bold; background-color:#E6E6E6; border-top: 2px solid #BDBDBD; border-right: 2px solid #BDBDBD;'>" . __('BOVIS') . "</td>";
			
			echo "<td style='width:10%;font-weight:bold; background-color:#E6E6E6; border-top: 2px solid #BDBDBD; border-right: 2px solid #BDBDBD;'>" . __('BIM') . "</td>";
			
			echo "<td style='width:10%;font-weight:bold; background-color:#E6E6E6; border-top: 2px solid #BDBDBD; border-right: 2px solid #BDBDBD;'>" . __('Breeam') . "</td>";
			
			echo "<td style='width:10%;font-weight:bold; background-color:#E6E6E6; border-top: 2px solid #BDBDBD; border-right: 2px solid #BDBDBD;'>" . __('Leed') . "</td>";
			
			echo "<td style='width:10%; font-weight:bold; background-color:#E6E6E6; border-top: 2px solid #BDBDBD; border-right: 2px solid #BDBDBD;'>" . __('Otros') . "</td>";


			echo "</tr>";

			echo"<tr class='tab_bg_1' style='background-color:#D8D8D8; border: 20px;'>";

			echo "<td id='nombreExperiencia' style='border-left: 2px solid #BDBDBD; border-right: 2px solid #BDBDBD;'>";
			echo "<textarea style='padding:7px; resize: none; display:block; margin-left:auto; margin-right:auto;' cols='20' rows='3' name='name'>".$data['name']."</textarea>";
			echo "</td>";

			echo "<td style='text-align: center; border-right: 2px solid #BDBDBD;'>";
			Dropdown::showFromArray('estado',array(1 =>'En curso' , 0 =>'Finalizado'), array('value' => $data['estado']));
			echo "</td>";

			echo "<td class='tipos_experiencias' style='text-align: center; border-right: 2px solid #BDBDBD;'>";
			Dropdown::show('PluginComproveedoresExperiencestype', $opt2);

			echo "<td style='text-align-last: center; border-right: 2px solid #BDBDBD;'>";
			Html::autocompletionTextField($objCommonDBT, "duracion", array('value' => $data['duracion']));
			echo "</td>";

			echo "<td id='intervencionBovis' style='width:10%; text-align-last: center; border-right: 2px solid #BDBDBD;'>";
			//Dropdown::showYesNo('intervencion_bovis');
			if($data['intervencion_bovis']==1){
				echo "<input type='checkbox' name='intervencion_bovis' value='1' checked>";
			}
			else{
				echo "<input type='checkbox' name='intervencion_bovis' value='1'>";
			}
			echo "</td>";

			echo "<td style='width:10%; text-align-last: center; border-right: 2px solid #BDBDBD;'>";
			if($data['bim']==1){
				echo "<input type='checkbox' name='bim' value='1' checked>";
			}
			else{
				echo "<input type='checkbox' name='bim' value='1'>";
			}
			//Dropdown::showFromArray('bim', array(-1 =>'------', 1=>'Sí' , 0 =>'No'));
			echo "</td>";

			echo "<td style='width:10%; text-align-last: center; border-right: 2px solid #BDBDBD;'>";
			if($data['breeam']==1){
				echo "<input type='checkbox' name='breeam' value='1' checked>";
			}
			else{
				echo "<input type='checkbox' name='breeam' value='1'>";
			}
			//Dropdown::showFromArray('breeam', array(-1 =>'------', 1=>'Sí' , 0 =>'No'));
			echo "</td>";

			echo "<td style='width:10%; text-align-last: center; border-right: 2px solid #BDBDBD;'>";
			if($data['leed']==1){
				echo "<input type='checkbox' name='leed' value='1' checked>";
			}
			else{
				echo "<input type='checkbox' name='leed' value='1'>";
			}
			//Dropdown::showFromArray('leed', array(-1 =>'------', 1=>'Sí' , 0 =>'No'));
			echo "</td>";

			echo "<td style='width:10%; text-align-last: center; border-right: 2px solid #BDBDBD;'>";
			if($data['otros_certificados']==1){
				echo "<input type='checkbox' name='otros_certificados' value='1' checked>";
			}
			else{
				echo "<input type='checkbox' name='otros_certificados' value='1'>";
			}
			//Dropdown::showFromArray('otros_certificados', array(-1 =>'------', 1=>'Sí' , 0 =>'No'));
			echo "</td>";

			echo "</tr>";

			echo"<tr class='tab_bg_1 center' style='background-color:#d8d8d8;'>";
			
			echo "<td style='font-weight:bold; background-color:#E6E6E6; border-top: 2px solid #BDBDBD; border-left: 2px solid #BDBDBD; border-right: 2px solid #BDBDBD;'>" . __('Cliente') . "</td>";
			
			echo "<td style='font-weight:bold; background-color:#E6E6E6; border-top: 2px solid #BDBDBD; border-right: 2px solid #BDBDBD;'>" . __('Año') . "</td>";
			
			echo "<td style='font-weight:bold; background-color:#E6E6E6; border-top: 2px solid #BDBDBD; border-right: 2px solid #BDBDBD;'>" . __('CCAA') . "</td>";

			echo "<td style='font-weight:bold; background-color:#E6E6E6; border-top: 2px solid #BDBDBD; border-right: 2px solid #BDBDBD;'>" . __('Importe') . "</td>";

			echo Html::hidden("<td>" . __('Cpd Tier') . "</td>");
			
			echo "<td colspan='5' style='font-weight:bold; background-color:#E6E6E6; border-top: 2px solid #BDBDBD; border-right: 2px solid #BDBDBD;'>" . __('Observaciones') . "</td>";

			echo "</tr>";

			echo"<tr class='tab_bg_1 center' style='background-color:#D8D8D8;'>";

			echo Html::hidden("<td>");
			echo Html::hidden("<input type='checkbox' name='cpd_tier' value='1' style='text-align-last: center'>");
			//Dropdown::showFromArray('cpd_tier', array(-1 =>'------', 1=>'Sí' , 0 =>'No'));
			echo Html::hidden("</td>");

			echo "<td style='text-align: center; border-bottom: 2px solid #BDBDBD; border-left: 2px solid #BDBDBD; border-right: 2px solid #BDBDBD;'>";
			echo "<textarea style='padding:7px; resize: none;' cols='20' rows='3' name='cliente'>".$data['cliente']."</textarea>";
			echo "</td>";

			echo "<td style='border-bottom: 2px solid #BDBDBD; border-right: 2px solid #BDBDBD;'>";
			$keyAnio=0;
			foreach ($objExperiencia->getYears() as $key => $value) {
				if($value==$data['anio'])
					$keyAnio=$key;

			}
			Dropdown::showFromArray('anio', $objExperiencia->getYears(), array('value' => (int)$keyAnio));
			echo "</td>";


			echo "<td style='text-align: center; border-bottom: 2px solid #BDBDBD; border-right: 2px solid #BDBDBD;'>";
			Dropdown::show('PluginComproveedoresCommunity',$opt3);
			echo "</td>";

			echo "<td  style='text-align-last: center; border-bottom: 2px solid #BDBDBD; border-right: 2px solid #BDBDBD;'>";
			$importe=number_format($data['importe'], 2, ',', '.');
			Html::autocompletionTextField($objCommonDBT, "importe", array('value' => $importe));
			echo "</td>";

			echo "<td colspan='5' style='border-bottom: 2px solid #BDBDBD; border-right: 2px solid #BDBDBD;'>";
			echo "<textarea style='padding:7px; width:95%; resize: none;' cols='20' rows='3' name='observaciones'>".$data['observaciones']."</textarea>";
			//Html::autocompletionTextField($this, "observaciones");
			echo "</td>";

			echo"</tr>";

			echo"<tr>";
					
			echo"</tbody>";
			echo"</table>";

			echo "<div style='margin-bottom: 15px; margin-top: 15px;'>";
			/*echo "<div style='display: inline-block;'><input type='submit' class='submit' name='add' value='AÑADIR' style='margin-right: 15px;'/></div>";*/
			echo "<div style='display: inline-block;'><span class='vsubmit' onclick='AñadirNormal();' name='addNoDelete' style='margin-right: 15px;'>AÑADIR</span></div>";
			echo "<div style='display: inline-block;'><span class='vsubmit' onclick='añadirSinBorrar();' name='addNoDelete' style='margin-right: 15px;'>AÑADIR SIN BORRAR </span></div>";

			//pasamos la tabla que se está modificando para actualizarla
			$tabla_modificada='';
			switch ($data["intervencion_bovis"]) {
				case 1:
					$tabla_modificada='intervencion_bovis';
					break;
				case 0:
					$tabla_modificada='sin_experiencia';
					break;
				default:
					$tabla_modificada=$data["plugin_comproveedores_experiencestypes_id"];
					break;
			}

			echo "<div style='display: inline-block;' id='guardar_modificacion'><span class='vsubmit' onclick='guardarModificacion(\"$tabla_modificada\");' name='Update' style='margin-right:15px;'>GUARDAR MODIFICACIÓN</span></div>";

			echo "<div style='display: inline-block;'><span class='vsubmit' onclick='limpiarFormulario();' name='addNoDelete' style='margin-right: 15px;'>LIMPIAR</span></div>";

			echo "</div>";
		}


