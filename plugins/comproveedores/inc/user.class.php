<?php

/******************************************

	PLUGIN DE GESTION DE CURRICULUMS DE LOS PROVEEDORES


 ******************************************/

	class PluginComproveedoresUser extends CommonDBRelation{

		static $rightname	= "plugin_comproveedores";

		static function getTypeName($nb=0){
			return _n('Personas de contacto','Personas de contacto',1,'comproveedores');
		}

		function getTabNameForItem(CommonGLPI $item, $tabnum=1,$withtemplate=0){
			if($item-> getType()=="Supplier"){
				return self::createTabEntry('Personas de contacto');
			}
			return 'Personas de contacto';
		}


		static function displayTabContentForItem(CommonGLPI $item,$tabnum=1,$withtemplate=0){

			global $CFG_GLPI;
			$self = new self();
			if($item->getType()=='Supplier'){	
				$self->showFormItem($item, $withtemplate);
			}else if($item->getType()=='PluginComproveedoresCv'){
				$self->showFormItemCv($item, $withtemplate);
			}
		}
                
		static function getProfileByUserID($UsuarioID){
			global $DB;

			$query ="SELECT profiles_id as profile FROM glpi_users WHERE id=$UsuarioID";

			$result=$DB->query($query);
			$id=$DB->fetch_array($result);

			if($id['profile']<>''){
				$options['profile']=$id['profile'];
			}
			return $options['profile'];
		}
                
                function fechaCaducidad($idcv) {
                    global $DB;
                    $query = "SELECT ADDDATE(if(fecha_ultima_modificacion is null, fecha_alta, fecha_ultima_modificacion), INTERVAL 1 YEAR) as fecha_caducidad
                              FROM glpi_plugin_comproveedores_cvs
                              WHERE id = {$idcv} and is_deleted=0 ";
                    $r = $DB->query($query);
                    $c = $DB->fetch_array($r);
                    $fecha = new Datetime($c['fecha_caducidad']);

                    return ($fecha->format('d-m-Y'));
                }
                
                function contactos($item, $withtemplate=''){
                    GLOBAL $DB, $CFG_GLPI;
                    
                    //AÑADIR USUARIOS AL PROVEEDOR
                    $self = new self();
                                        
                    $dropdown = new Dropdown();
                    $fkcv = 0;
                    $SupplierID = 0;
                    
                    if(isset($item->fields['cv_id'])){
                        $fkcv = $item->fields['cv_id'];
                        if(isset($item->fields['id'])){
                            $SupplierID = $item->fields['id'];
                        }
                    }else{
                        //$profile_Id = $item->getProfileByUserID($user_Id);
                        if(isset($item->fields['id'])){
                            $fkcv = $item->fields['id'];
                        }
                        if(isset($item->fields['supplier_id'])){
                            $SupplierID = $item->fields['supplier_id'];
                        }
                    }
                    
                    echo "<style>
                         .checkboxUsuario{
                            height: 20px;
                            width: 20px;
                            cursor: pointer;
                         }
                    </style>";

                    //DATOS DEL PERFIL Y USUARIO
                    $user_Id             = $_SESSION['glpiID'];
                    $profile_Id          = $self->getProfileByUserID($user_Id);
                    $ver                 = true;
                    if(in_array($profile_Id, array(3,4,16))){    
                        $ver = true;
                        echo "<input id='verBotonesContacto' type='hidden' value='1' />";
                    }else{
                        $ver = false;
                        echo "<input id='verBotonesContacto' type='hidden' value='0' />";
                    }                     
                    
                    if($ver === false){
                        echo "<script type='text/javascript'>"
                        . "$('#c_recherche').css('display', 'none'); "
                        . "$('#language_link').css('display', 'none'); "
                        . "$('#help_link').css('display', 'none'); "
                        . "$('#bookmark_link').css('display', 'none'); "
                        . "$('#debug_mode').css('display', 'none'); "
                        . "$('#page').css('margin', 'auto'); "
                        . "$('#c_ssmenu2').css('display', 'none');"
                        . "$('#goToList').css('display', 'none');"
                        . "$('#preferences_link').css('display', 'none');"
                        . "</script>";
                    }                       
                    
                    $strbus = "select id, nombre from glpi_plugin_comproveedores_contacttypes order by nombre";
                    $resulcontacttypes = $DB->query($strbus);

                    //CCAA
                    $opt3['comments']       = false;
                    $opt3['addicon']        = false;
                    $opt3['width']          = '203px';
                    //$opt3['value']          = $this->fields["plugin_comproveedores_communities_id"];
                    
                        //echo Html::hidden('supplier_id', array('value' => $SupplierID));
                        echo "<input type='hidden' id='supplier_id' value='{$SupplierID}'>";
                        echo "<input type='hidden' id='cv_id' value='{$fkcv}'>";
                        echo "<input type='hidden' id='claveUsu' value='' />";
                        echo "<input type='hidden' id='profile_id' value='{$profile_Id}' />";
                        
                        echo "<div id='general' class='center' style='float: left; width: 98%; border-radius: 4px; padding: 8px; background-color: #e9ecf3; height: 440px; margin-bottom: 10px; overflow-y: auto;'>";
                        //if($profile_Id != 14){                        
                        echo "<div class='center'>";
                        echo "<table class='tab_cadre_fixe' style='width: 100%; margin-top: 4px; padding: 5px; border-radius:4px; background-color:#cacaca; '><tbody>";
                        echo "<tr>";
                            echo "<td style='padding: 0px;'>nombre:(*)</td>";
                            echo "<td style='padding: 0px;'>teléfono:</td>";
                            echo "<td style='padding: 0px;'>email:(*)</td>";
                            echo "<td style='padding: 0px;'>cargo:</td>";
                            echo "<td style='padding: 0px;'>delegación:</td>";
                        echo "</tr>";
                        echo "<tr>";
                            echo "<td style='padding: 0px;'>";
                            echo "<input id='nombreContacto' type='text' value='' />";
                            echo "</td>";
                            echo "<td style='padding: 0px;'>";
                            echo "<input id='telefonoContacto' type='text' value='' />";
                            echo "</td>";
                            echo "<td style='padding: 0px;'>";
                            echo "<input id='emailContacto' type='text' value='' />";
                            echo "</td>";     
                            echo "<td style='padding: 0px;'>";
                            echo "<select id='cargoContacto' style='height: 25px; width: 20em; border-radius: 4px; margin-right: 2px;'>";
                                echo "<option value='0'> ----- </option>";
                                while ($datos=$DB->fetch_array($resulcontacttypes)) {
                                    echo "<option value='{$datos["id"]}'>{$datos["nombre"]}</option>";
                                }                            
                            echo "</select>";
                            echo "</td>";
                            echo "<td style='padding: 0px;'>";
                            echo "<input id='delegacionContacto' type='text' value='' />";
                            echo "</td>";
                        echo "</tr>";
                        echo "<tr>";
                            echo "<td colspan='4'>";
                            echo "</td>";
                            echo "<td style='text-align: right;'>";
                            echo "<input type='hidden' id='identContacto' value='' />";
                            echo "<input type='submit' class='boton_grabar' value='' style='margin-right: 10px;' title='Grabar cambios'/>";
                            echo "<input type='submit' class='boton_limpieza' value='' title='Limpiar campos' />";
                            echo "</td>";                            
                        echo "</tr>";
                        echo "</tbody></table></div>";
                       // }
                        
                        if($fkcv == 0){$fkcv = -1;}
                        
                        $query3 = "select c.id as ident, if(fkusuario is null, 0, fkusuario) as fkusuario, c.clave,
                                    c.nombre, c.telefono as telefono, c.email, t.id as cargoid, 
                                    t.nombre as cargo, if(c.editor is null,0, c.editor) as gestor, u.name as nombreusuario, c.delegacion
                                    from glpi_plugin_comproveedores_contacts as c 
                                            left join glpi_plugin_comproveedores_contacttypes as t on c.fkcontacttype = t.id
                                            left join glpi_users as u on u.id = c.fkusuario
                                    where c.fkcv = {$fkcv}
                                    order by c.nombre asc";
                        $result3 = $DB->query($query3);                  
                        $fecha = $self->fechaCaducidad($fkcv);
                        
                        echo "<div align='center' style='width: 100%; float: left;'><table id='tablaContactos' class='display' style='width: 100%;'>";
                        echo "<thead>";
                        echo "<tr>";
                            echo "<th class='center' style='border-right: 1px solid #eee;'>nombre</th>";
                            echo "<th class='center' style='border-right: 1px solid #eee;'>teléfono</th>";
                            echo "<th class='center' style='border-right: 1px solid #eee;'>email</th>";
                            echo "<th class='center' style='border-right: 1px solid #eee;'>cargo</th>";
                            echo "<th class='center' style='border-right: 1px solid #eee;'>delegación</th>";
                            echo "<th class='center' style='border-right: 1px solid #eee;'>gestor</th>";
                            echo "<th class='center' style='border-right: 1px solid #eee;'>mail</th>";
                            echo "<th class='center' style='border-right: 1px solid #eee;'>generar nueva clave</th>";
                            if($profile_Id!=14){
                            echo "<th class='center'>editar</th>";                            
                            echo "<th class='center'>quitar</th>";}
                        echo "</tr>";
                        echo "</thead><tbody>";
                        while ($data=$DB->fetch_array($result3)) {
                            echo "<tr id='contactoid_{$data["ident"]}'>";
                                echo "<td id='nombre_{$data["ident"]}' style='border-right: 1px solid #eee;'>".$data["nombre"]."</td>";
                                echo "<td id='telefono_{$data["ident"]}' style='border-right: 1px solid #eee;'>".$data["telefono"]."</td>";
                                echo "<td id='email_{$data["ident"]}' style='border-right: 1px solid #eee;'>".$data["email"]."</td>";
                                echo "<td id='cargo_{$data["ident"]}' style='border-right: 1px solid #eee;'><input id='cargoOculto_{$data['ident']}' type='hidden' value='{$data['cargoid']}' />".$data["cargo"]."</td>";
                                echo "<td id='delegacion_{$data["ident"]}' style='border-right: 1px solid #eee;'>".$data["delegacion"]."</td>";
                                echo "<td id='check_{$data["ident"]}' class='center' style='border-right: 1px solid #eee;'>";
                                    if($data['gestor'] == null || $data['gestor']<1){
                                        echo "<input id='gestor_{$data["ident"]}' class='checkboxUsuario' type='checkbox' value='0' />";
                                    }else{
                                        echo "<input id='gestor_{$data["ident"]}' class='checkboxUsuario' type='checkbox' value='1' checked/>";
                                    }
                                    
                                    echo "<input id='txtUsuario_{$data["ident"]}' type='hidden' value='{$data["fkusuario"]}' />";
                                    echo "<input id='ocl_{$data["ident"]}' type='hidden' value='{$data["clave"]}' />";
                                echo "</td>";
                                echo "<td class='center' >";
                                    echo "<input type='submit' title='Enviar EMAIL de recordatorio al contacto' id='email_{$data["ident"]}' class='boton_email' value=' ' style='width:30px;height:30px; background-size: 20px;'/>";                                    
                                echo "</td>";
                                echo "<td class='center' >";
                                    echo "<input type='submit' title='Generar nueva clave y enviar' id='clave_{$data["ident"]}' class='boton_clave' value=' ' />";
                                echo "</td>";              
                                if($profile_Id!=14){
                                echo "<td class='center' >";
                                    echo "<input type='submit' title='Editar contacto' id='editar_{$data["ident"]}' class='boton_editar' value=' ' style='width:30px;height:30px; background-size: 20px;'/>";                                    
                                echo "</td>";
                                echo "<td class='center' >";
                                    echo "<input type='submit' title='Quitar contacto' id='quitar_{$data["ident"]}' class='boton_borrar' value=' ' />";
                                echo "</td>";
                                }
                            echo "</tr>";
                        }
                        echo "</tbody></table>";
                        echo "</div>";
                                                
                        echo "<script type='text/javascript'>

                            var profileid = $('#profile_id').val();
                            var valor     = $('#verBotonesContacto').val();
                            if( valor == '0'){
                                $('#tablaContactos').DataTable({
                                        'searching':      true,
                                        'scrollY':        '400px',
                                        'scrollCollapse': true,
                                        'ordering':       true,
                                        'paging':         false,
                                        'language': {'url': '".$CFG_GLPI["root_doc"]."/lib/jqueryplugins/Spanish.json'}                    
                                }); 
                            }else{
                                $('#tablaContactos').DataTable({
                                        'searching':      true,
                                        'scrollY':        '400px',
                                        'scrollCollapse': true,
                                        'ordering':       true,
                                        'paging':         false,
                                        'language': {'url': '".$CFG_GLPI["root_doc"]."/lib/jqueryplugins/Spanish.json'},
                                        'dom': 'Bfrtip',
                                        'buttons': [
                                            'copyHtml5',
                                            'excelHtml5',
                                            'pdfHtml5'
                                        ]                          
                                });                            
                            }
                            

                            function generatePassword(length,type) {
                                switch(type){
                                    case 'num':
                                        characters = '0123456789';
                                        break;
                                    case 'alf':
                                        characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                                        break;
                                    case 'rand':
                                        break;
                                    case 'alfanum':
                                        characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                                        break;
                                }
                                var pass = '';
                                for (i=0; i < length; i++){
                                    if(type == 'rand'){
                                        pass += String.fromCharCode((Math.floor((Math.random() * 100)) % 94) + 33);
                                    }else{
                                        pass += characters.charAt(Math.floor(Math.random()*characters.length));   
                                    }
                                }
                                return pass;
                            }
                    

                            function enviar_correo(origen, destino, asunto, mensaje, notificacion){
                                var resp = confirm(notificacion, 'EMAIL');
                                if(resp){
                                    var parametros = {fromEmail: origen,
                                                        toEmail: destino,
                                                        subjectEmail: asunto,
                                                        messageEmail: mensaje};
                                    var strurl = '".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/email.php';
                                    $.ajax({
                                        url: strurl,
                                        data: parametros,
                                        async: false,
                                        globl: false,
                                        type: 'GET',
                                        cache: false,
                                        success: function (data) {
                                           alert(data);
                                        },
                                        error: function () { 
                                           alert('Error de conexión');
                                        }
                                    });                                                  
                                }
                            }

                            function cliquea_gestor(ident){
                                var idgestor = '#gestor_'+ident;
                                var g = 0;
                                if($(idgestor).prop('checked')){
                                    g = 1;
                                }else{
                                    g = 0;
                                }
                                var idemail = '#email_'+ident;
                                var idnombre = '#nombre_'+ident;
                                var email = $(idemail).text();
                                var idtelefono = '#telefono_'+ident;
                                var telefono = $(idtelefono).text();
                                var nombre = $(idnombre).text();
                                var fkcv = $('#cv_id').val();
                                var usuariooculto = '#txtUsuario_'+ident;
                                var usuarioid = 0;                     
                                var clave = generatePassword(8,'alfanum');
                                var proveedorid = $('#supplier_id').val();

                                if($(usuariooculto).length>0){
                                    usuarioid = $(usuariooculto).val();
                                }
                                
                                //alert('ident:'+ident+' email:'+email+' telefono:'+telefono+' nombre:'+nombre+' proveedorid:'+fkcv+' usuarioid: '+usuarioid+' clave:'+clave+' gestor:'+g+' proveedorid:'+proveedorid);
                                
                                var parametros = {ident: ident,
                                                email: email,
                                                telefono: telefono,
                                                nombre: nombre,
                                                proveedorid: fkcv,
                                                usuarioid: usuarioid,
                                                clave: clave,
                                                gestor: g,
                                                proveedorid: proveedorid};
                                var strURL = '".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/insertUserFromSupplier.php';
                                $.ajax({
                                    url: strURL,
                                    data: parametros,
                                    async: false,
                                    globl: false,
                                    type: 'GET',
                                    cache: false,
                                    success: function (html) {
                                        
                                        if(html!=''){                                        
                                            var res = confirm(html, 'Usuario gestor');
                                            if(res){
                                                if(gestor==1){
                                                    var asunto = 'NO REPLEY';
                                                    var origen = 'info@fotex.es';
                                                    var destino = email;
                                                    var mensaje = 'Texto del correo electrónico';
                                                    var texto = '¿Desea enviar al gestor un correo con sus datos?';
                                                    
                                                    enviar_correo(origen, destino, asunto, mensaje, texto);
                                                    location.reload();
                                                }
                                            }
                                        }else{
                                            location.reload();
                                        }
                                    },
                                    error: function () { 
                                        alert('Error de conexión');
                                    }
                                });                                 
                            }

                            $('.checkboxUsuario').on('click', function() {
                                //var valor = '#'+$(this).attr('id');
                                var ident = $(this).attr('id').replace('gestor_', '');
                                var n = 0;
                                var e = $('.checkboxUsuario');
  
                                for(i=0; i<e.length; i++){
                                    if(e[i].checked){ n++; }
                                }
                                if(n<=2){                            
                                    cliquea_gestor(ident);                                   
                                }else{
                                    alert('No pueden existir mas de 2 gestores/editores');
                                }                                
                            });

                            function wait(miliseconds){

                              const gen = function * (){
                                 const end = Date.now() + miliseconds;
                                 while(Date.now() < end){yield};
                                 return;
                              }

                              const iter = gen();
                              while(iter.next().done === false);
                            }
                            
                            $('.boton_editar').on('click', function() {
                                
                                var ident = $(this).attr('id').replace('editar_','');
                                var idtelefono = '#telefono_'+ident;
                                var telefono = $(idtelefono).text().trim();
                                var idemail = '#email_'+ident;
                                var email = $(idemail).text().trim();
                                var idnombre = '#nombre_'+ident;
                                var nombre = $(idnombre).text().trim();
                                var iddelegacion = '#delegacion_'+ident;
                                var delegacion = $(iddelegacion).text().trim();
                                var cargo = '#cargoOculto_'+ident;
                                var idcargo = $(cargo).val();

                                $('#identContacto').val(ident);
                                $('#emailContacto').val(email);
                                $('#nombreContacto').val(nombre);
                                $('#telefonoContacto').val(telefono);
                                $('#delegacionContacto').val(delegacion);
                                $('#cargoContacto').val(idcargo);
                                
                            });

                            $('.boton_limpieza').on('click', function() {
                                $('#nombreContacto').val('');
                                $('#telefonoContacto').val('');
                                $('#emailContacto').val('');
                                $('#cargoContacto').val('0');     
                                $('#delegacionContacto').val('');
                                $('#identContacto').val('');
                            });

                            $('.boton_grabar').on('click', function() {

                                var nombre = $('#nombreContacto').val();
                                var telefono = $('#telefonoContacto').val();
                                var email = $('#emailContacto').val();
                                var cargo = $('#cargoContacto').val();
                                var proveedorid = {$SupplierID};
                                var delegacion = $('#delegacionContacto').val();
                                var ident = $('#identContacto').val();
                                var fkcv = {$fkcv};

                                if(nombre=='' || email==''){
                                    alert('Debe indicar al menos el nombre y el correo electrónico.');
                                }else{
                                    var parametros = {  
                                            identContacto: ident,
                                            nombreContacto: nombre,
                                            telefonoContacto: telefono,
                                            emailContacto: email,
                                            cargoContacto: cargo,
                                            proveedorid: proveedorid,
                                            fkcv: fkcv,
                                            delegacionContacto: delegacion};
                                    var strURL = '".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/insertContactSupplier.php';
                                    $.ajax({
                                        url: strURL,
                                        data: parametros,
                                        async: false,
                                        globl: false,
                                        type: 'GET',
                                        cache: false,
                                        success: function (data) {
                                           location.reload();
                                        },
                                        error: function () { 
                                            alert('Error de conexión');
                                        }
                                    });   
                                    $('#identContacto').val(0);
                                    $('#emailContacto').val('');
                                    $('#nombreContacto').val('');
                                    $('#telefonoContacto').val('');
                                    $('#delegacionContacto').val('');
                                    $('#cargoContacto').val(0);
                                }
                            });
                            
                            $('.boton_borrar').on('click', function(){
                                var resp = confirm('¿Realmente desea eliminar este contacto?','Confirmación');
                                if(resp == true){
                                    var strURL = '".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/quitarContactoDeProveedor.php';
                                    var ident = $(this).attr('id').replace('quitar_','');
                                    $.ajax({
                                        url: strURL,
                                        data: {id: ident},
                                        async: false,
                                        globl: false,
                                        type: 'GET',
                                        cache: false,
                                        success: function (data) {
                                            location.reload();
                                        },
                                        error: function () { 
                                            alert('Error de conexión');
                                        }
                                    });   
                                }
                            });

                            $('.boton_email').on('click', function() { 
                                var ident = $(this).attr('id').replace('email_','');
                                var idtelefono = '#telefono_'+ident;
                                var telefono = $(idtelefono).text().trim();                               
                                var idemail = '#email_'+ident;
                                var email = $(idemail).text().trim();
                                var idnombre = '#nombre_'+ident;
                                var nombre = $(idnombre).text().trim();
                                var iddelegacion = '#delegacion_'+ident;
                                var delegacion = $(iddelegacion).text().trim();
                                var cargo = '#cargoOculto_'+ident;
                                var idcargo = $(cargo).val();
                                var idgestor = '#gestor_'+ident;
                                var isGestorChecked =  $(idgestor).prop('checked');
                                var idclv = '#ocl_'+ident;
                                var clv = $(idclv).val();
                                var mensaje = '';
                                var asunto = 'NO REPLY';
                                
                                if(isGestorChecked==false){
                                        mensaje  = 'Estimado <b>'+nombre+'</b>, <br>';
                                        mensaje += 'Le recordamos la necesidad de mantener actualizados los datos de su empresa ';
                                        mensaje += 'a través de la persona de contacto asignada para ello así como el enlace ';
                                        mensaje += 'para acceder a los mismos: <br><br>';
                                        mensaje += '<a href=\"http://www.gestioninteligente.eu/bovis\">http://www.gestioninteligente.eu/bovis</a> <br><br>';
                                        mensaje += 'Actualmente la información de su empresa en nuestra base de datos ';
                                        mensaje += 'es válida hasta el <b>{$fecha}</b> <br>';
                                        mensaje += 'Si necesita ayuda diríjase, por favor, a la siguiente dirección de correo electrónico: <br><br>';
                                        mensaje += '<a href=\"mailto:Plataformaproveedores@bovis.es\">Plataformaproveedores@bovis.es</a>';
                                }else{
                                        mensaje  = 'Estimado <b>'+nombre+'</b>, <br>';
                                        mensaje += 'Le recordamos la necesidad de mantener actualizados los datos de su empresa ';
                                        mensaje += 'a través de la persona de contacto asignada para ello así como el enlace ';
                                        mensaje += 'para acceder a los mismos: <br><br>';
                                        mensaje += '<a href=\"http://www.gestioninteligente.eu/bovis\">http://www.gestioninteligente.eu/bovis</a> <br><br>';
                                        mensaje += 'Actualmente la información de su empresa en nuestra base de datos ';
                                        mensaje += 'es válida hasta el <b>{$fecha}</b> <br><br>';
                                        mensaje += 'USUARIO: '+email+'<br>';
                                        mensaje += 'CLAVE: '+clv+'<br><br>';
                                        mensaje += 'Si necesita ayuda diríjase, por favor, a la siguiente dirección de correo electrónico: <br><br>';
                                        mensaje += '<a href=\"mailto:Plataformaproveedores@bovis.es\">Plataformaproveedores@bovis.es</a>';
                                }   
                                var destino = email;
                                var origen = 'info@fotex.es';
                                var notificacion = '¿Desea enviar por email la notificación de vigencia al usuario?';
                                enviar_correo(origen, destino, asunto, mensaje, notificacion);      
                            });



                            $('.boton_clave').on('click', function() {
                                
                                var ident = $(this).attr('id').replace('clave_','');
                                var idtelefono = '#telefono_'+ident;
                                var telefono = $(idtelefono).text().trim();                               
                                var idemail = '#email_'+ident;
                                var email = $(idemail).text().trim();
                                var idnombre = '#nombre_'+ident;
                                var nombre = $(idnombre).text().trim();
                                var iddelegacion = '#delegacion_'+ident;
                                var delegacion = $(iddelegacion).text().trim();
                                var cargo = '#cargoOculto_'+ident;
                                var idcargo = $(cargo).val();                                
                                var usuario = '#txtUsuario_'+ident;
                                var idusuario = $(usuario).val();
                                var destino = email;
                                var origen = 'info@fotex.es';
                                var mensaje = '';
                                var asunto = 'NO REPLY';                                
                                var idgestor = '#gestor_'+ident;
                                var isChecked = $(idgestor).prop('checked');

                                if(isChecked){
                                    var resp = confirm('¿Realmente desea cambiar la clave de este usuario?','Confirmación');
                                    if(resp){   

                                        var clave = generatePassword(8,'alfanum');

                                        mensaje  = 'Estimado <b>'+nombre+'</b>, <br>';
                                        mensaje += 'Le recordamos la necesidad de mantener actualizados los datos de su empresa ';
                                        mensaje += 'a través de la persona de contacto asignada para ello así como el enlace ';
                                        mensaje += 'para acceder a los mismos: <br><br>';
                                        mensaje += '<a href=\"http://www.gestioninteligente.eu/bovis\">http://www.gestioninteligente.eu/bovis</a> <br><br>';
                                        mensaje += 'Actualmente la información de su empresa en nuestra base de datos ';
                                        mensaje += 'es válida hasta el <b>{$fecha}</b> <br><br>';
                                        mensaje += 'USUARIO: '+email+'<br>';
                                        mensaje += 'CLAVE: '+clave+'<br><br>';
                                        mensaje += 'Si necesita ayuda diríjase, por favor, a la siguiente dirección de correo electrónico: <br><br>';
                                        mensaje += '<a href=\"mailto:Plataformaproveedores@bovis.es\">Plataformaproveedores@bovis.es</a>';

                                        var parametros = {  idcontacto: ident,
                                                            nombreContacto: nombre,
                                                            telefonoContacto: telefono,
                                                            emailContacto: email,
                                                            cargoContacto: cargo,
                                                            idusuario: idusuario,
                                                            clave: clave,
                                                            destino: destino,
                                                            origen: origen,
                                                            mensaje: mensaje,
                                                            asunto: asunto};
                                        var strURL = '".$CFG_GLPI["root_doc"]."/plugins/comproveedores/inc/reiniciaEmail.php';
                                        $.ajax({
                                            url: strURL,
                                            data: parametros,
                                            async: false,
                                            globl: false,
                                            type: 'GET',
                                            cache: false,
                                            success: function (data) {
                                                var r = confirm(data,'Email');
                                                if(r){
                                                    location.reload();
                                                }
                                            },
                                            error: function () { 
                                                alert('Error de conexión');
                                            }
                                        });                                     
                                    }
                                }
                                else{
                                    alert('Para cambiar la clave, ese usuario debe ser gestor');
                                }
                            });    
                            
                        </script>";                       
                        echo "</div>";                           
                    
                    
                }

		function showFormItem($item, $withtemplate='') {	
                /******************************************************************
                 * DESDE LA GESTIÓN DEL ADMINISTRADOR O TÉCNICO GESTOR DE BOVIS
                *******************************************************************/
                    
                    $this->contactos($item, $withtemplate);
                }
                    
                function showFormItemCv($item, $withtemplate='') {	
                /******************************************************************
                 * DESDE LA GESTIÓN DEL PROPIO PROVEEDOR
                *******************************************************************/
                    $this->contactos($item, $withtemplate);
                }

								
                function showForm($ID, $options=[]) {
                        echo " "; 
                }

    }