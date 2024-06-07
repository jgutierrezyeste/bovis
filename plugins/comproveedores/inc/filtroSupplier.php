<?php

GLOBAL $DB,$CFG_GLPI;
$objCommonDBT=new CommonDBTM;

echo "<form name='searchformSupplier' method='get' action=".$CFG_GLPI["root_doc"]."'/front/supplier.php'>";
echo"<table class='tab_cadre_fixe'><tbody>";
			


			echo"<th colspan='6'>Filtro Proveedores</th></tr>";

			echo"<tr class='tab_bg_1 center'>";
				echo "<td>" . __('Nombre') . "</td>";
				echo "<td>";
				Html::autocompletionTextField($objCommonDBT,'name');
				echo "</td>";

				echo "<td>" . __('Intervención de BOVIS') . "</td>";
				echo "<td id='intervencionBovis'>";
				Dropdown::showFromArray('intervencion_bovis', array('' =>'Todos',1=>'Sí' , 0 =>'No'));
				echo "</td>";
			echo "</tr>";

			echo"<tr class='tab_bg_1 center'>";
				echo "<td>" . __('BIM') . "</td>";
				echo "<td>";
				Dropdown::showFromArray('bim', array('' =>'Todos',-1 =>'------', 1=>'Sí' , 0 =>'No'));
				echo "</td>";

				echo "<td>" . __('LEED') . "</td>";
				echo "<td>";
				Dropdown::showFromArray('leed', array('' =>'Todos',-1 =>'------', 1=>'Sí' , 0 =>'No'));
				echo "</td>";
			echo "</tr>";

			echo"<tr class='tab_bg_1 center '>";
				echo "<td>" . __('BREEAM') . "</td>";
				echo "<td>";
				Dropdown::showFromArray('breeam', array('' =>'Todos',-1 =>'------', 1=>'Sí' , 0 =>'No'));
				echo "</td>";

				echo "<td>" . __('Otros certificados') . "</td>";
				echo "<td>";
				Dropdown::showFromArray('otros_certificados', array('' =>'Todos',-1 =>'------', 1=>'Sí' , 0 =>'No'));
				echo "</td>";
			echo "</tr>";

			echo"<tr class='tab_bg_1 top'>";
				echo "<td class='center'>" . __('Cpd Tier') . "</td>";
				echo "<td class='center'>";
				Dropdown::showFromArray('cpd_tier', array('' =>'Todos',-1 =>'------', 1=>'Sí' , 0 =>'No'));
				echo "</td>";

				echo "<td class='center'>" . __('Tipos de experiencias') . "</td>";
				$lista=getTiposExperiencias();
				echo "<td colspan='".count($lista)."'>";
						echo "<div style='width: 250px; height: 100px; overflow-y: scroll; border: 1px solid #BDBDBD'>";
						foreach ($lista as $key => $value) {
						
							echo "<input type='checkbox' name='tipos_experiencias[]' value='".$key."' />".$value."<br />";
						}
						echo "</div>";
				echo "</td>";
			echo "</tr>";

			echo"<tr class='tab_bg_1 center'>";
				echo "<td colspan='6'><input type='submit' name='search' value='Filtrar' class='submit'/></td>";
			echo "</tr>";


			
echo "</table>";
echo "</form>";

function getTiposExperiencias(){
	GLOBAL $DB,$CFG_GLPI;


	$query ="SELECT name FROM `glpi_plugin_comproveedores_experiencestypes` order by id";

	$result = $DB->query($query);
	$i=1;
	while ($data=$DB->fetch_array($result)) {
		$lista[$i]=$data['name'];
		$i++;
	}

	return $lista;
}