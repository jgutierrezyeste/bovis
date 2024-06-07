<?php

GLOBAL $DB,$CFG_GLPI;
$objCommonDBT=new CommonDBTM;

	//Comunidades
	/**$opt3['comments']= false;
	$opt3['addicon']= false;
	$opt3['width']='203px';
	$opt3['specific_tags']=array('onchange' => 'cambiarProvincia(value, false)');**/
	
	echo"<div style='position:relative; width:100%; margin: 0px auto 30px auto;'>";
        echo "";
	echo "<div class='cuadro_busqueda'>";				
	echo "<div style='font-weight: bold; padding: 10px;border-radius: 4px 4px 0px 0px;position:relative;margin:0px;background-color:#0e52a0;color:#FFF;'>BÚSQUEDA DE PROYECTOS</div>";
	echo "<div class='campo_busqueda' style='width: 45%;'>";
	echo "<label for='ccaa' style='width: 50px;'>Ubicación (CCAA)</label>";
	//Dropdown::show('PluginComproveedoresCommunity',$opt3);
	
	echo "<label for='prv' style='width: 50px;'>Ubicación (PRV)</label>
            		<select id='prv' value=''/>
		</select>					
	</div>
	<div class='campo_busqueda' style='width: 45%;'>
		<label for='estado' style='width: 50px;'>Estado </label>
		<select id='estado' value=''/>
		</select>
	</div>
	<div class='campo_busqueda'>Fecha de Comienzo: (inicio)<input type='text' id='comienzoINI' value='' style='width:80px;'/> (fin)<input type='text' id='comienzoFIN' value='' style='width:80px;'/></div>
	<div class='campo_busqueda'>Duración: <input type='text' id='duracion' value='' /></div>
	<div class='campo_busqueda'>Tipo de Servicio: <input type='text' id='tservicio' value='' /></div>
	<div class='campo_busqueda'>Cliente: <input type='text' id='cliente' value='' /></div>
	<div class='campo_busqueda'>
		Coste: (mínimo)<input type='text' id='minima' readonly style='width:100px;'/> (máximo)<input type='text' id='maxima' readonly style='width:100px;'/>
		<div id='slider-facturacion' style='margin-top: 4px; margin-bottom: 4px;'></div>				
	</div>
	<input type='submit' name='search' value='BUSCAR' class='submit' style='margin: 5px; position:relative;' />";
	echo "</div></div>";
   
	
	echo "<script type='text/javascript'>
                var maxi = $('#maxima').val();
                $('#maxima').val(currency(maxi));			

		function currency(value, decimals, separators) {
			decimals = decimals >= 0 ? parseInt(decimals, 0) : 2;
			separators = separators || ['.', '.', ','];
			var number = (parseFloat(value) || 0).toFixed(decimals);
			if (number.length <= (4 + decimals))
				return number.replace('.', separators[separators.length - 1]);
			var parts = number.split(/[-.]/);
			value = parts[parts.length > 1 ? parts.length - 2 : 0];
			var result = value.substr(value.length - 3, 3) + (parts.length > 1 ?
				separators[separators.length - 1] + parts[parts.length - 1] : '');
			var start = value.length - 6;
			var idx = 0;
			while (start > -3) {
				result = (start > 0 ? value.substr(start, 3) : value.substr(0, 3 + start))
					+ separators[idx] + result;
				idx = (++idx) % 2;
				start -= 3;
			}
			return (parts.length == 3 ? '-' : '') + result + ' €';
		}    
                
		$('#slider-facturacion').slider({
		  range: true,
		  min: 0,
		  max: 999999999,
		  values: [ 75, 150000 ],
		  slide: function( event, ui ) {
			$('#minima' ).val( currency(ui.values[ 0 ]));
			$('#maxima' ).val( currency(ui.values[ 1 ]));
		  }
		});   
                
		$('#minima' ).val( currency(0));
		$('#maxima' ).val( currency(150000));
                
		$('#comienzoINI').datepicker( {
                    dateFormat: 'mm/yy',
                    changeMonth: true,
                    changeYear: true,
                    onClose: function() {
                        var iMonth = $('#ui-datepicker-div .ui-datepicker-month :selected').val();
                        var iYear = $('#ui-datepicker-div .ui-datepicker-year :selected').val();
                        $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
                    },
                    beforeShow: function() {
                        if ((selDate = $(this).val()).length > 0){
                            iYear = selDate.substring(selDate.length - 4, selDate.length);
                            iMonth = jQuery.inArray(selDate.substring(0, selDate.length - 5),$(this).datepicker('option', 'monthNames'));
                            $(this).datepicker('option', 'defaultDate', new Date(iYear, iMonth, 1));
                            $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
                        }
                    }
                });
		
                $('#comienzoFIN').datepicker( {
                    dateFormat: 'mm/yy',
                    changeMonth: true,
                    changeYear: true,
                    onClose: function() {
                        var iMonth = $('#ui-datepicker-div .ui-datepicker-month :selected').val();
                        var iYear = $('#ui-datepicker-div .ui-datepicker-year :selected').val();
                        $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
                    },
                    beforeShow: function() {
                    if ((selDate = $(this).val()).length > 0){
                        iYear = selDate.substring(selDate.length - 4, selDate.length);
                        iMonth = jQuery.inArray(selDate.substring(0, selDate.length - 5),$(this).datepicker('option', 'monthNames'));
                        $(this).datepicker('option', 'defaultDate', new Date(iYear, iMonth, 1));
                        $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
                    }
                    }
                });

	
		$(document).ready(function() {
                        var maxi = $('#maxima').val();
                        $('#maxima').val(currency(maxi));
                        
			$('[id*=searchrowProject]').find('.pointer').hide();
			$('[id*=s2id_dropdown_criteria_1__link_]').html('&nbsp;&nbsp');
			$('[id*=s2id_dropdown_criteria_0__searchtype_]').hide();
			$('[id*=s2id_dropdown_criteria_1__searchtype_]').hide();
	
			$('[id*=spansearchtypecriteriaProject] input').attr('size','35');
			
			$('[id*=dropdown_criteria_0__field_').attr('disabled','true');
			$('[id*=dropdown_criteria_1__field_').attr('disabled','true');
                        
		 });	
		
		
	</script>
	
	";


