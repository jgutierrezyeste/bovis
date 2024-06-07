/* 
 * BOVIS es una aplicación desarrollada por el equipo TI de 
 FOMENTO DE TÉCNICAS EXTREMEÑAS S.L. (FOTEX)
 +34924207328
 http://www.fotex.es
 Comienzo del desarrollo: enero de 2019
 */

        function quitarFilaTabla(id, den){
            var aux = den+id;
            $(aux).remove();            
        };
                
        function IncSel(nombreLic, cifLic, strClaseIncorpora, strIdentificador, strURL) {
            var n = nombreLic;
            var c = cifLic;	
            var suppliers_id = '';
            var contrato = $(strIdentificador).val();
            var i = 0;
            $(strClaseIncorpora).each(function() {
                if(i == 0) {
                    suppliers_id = $(this).attr('id').replace('proveedor_', '');
                }else{
                    suppliers_id = suppliers_id + ',' + $(this).attr('id').replace('proveedor_', '');
                }
                i = i + 1;
            });
            if(i > 0){
                $.ajax({ 
                    async: false, 
                    type: 'GET',
                    data: {'projecttasks_id':contrato, 'nombre_lic':n, 'cif_lic':c, 'supplier_id':suppliers_id},                  
                    url: strURL,  				
                    success:function(data){
                            location.reload();
                        },
                        error: function(result) {
                            alert("Data not found!");
                        }
                });       
            }else{
                alert('No ha seleccionado ningún elemento,<br> antes de continuar debe seleccionar algún elemento<br>para acceder a la siguiente fase.');
            }
        };
   

	$(document).ready(function() {
            jQuery.extend( jQuery.fn.dataTableExt.oSort, {
                'currency-pre': function ( a ) {
                    a = (a==='-') ? 0 : a.replace( /[^\d\-\.]/g, '' );
                    return parseFloat( a );
                },

                'currency-asc': function ( a, b ) {
                    return a - b;
                },

                'currency-desc': function ( a, b ) {
                    return b - a;
                }
            }); 
        });


	   
