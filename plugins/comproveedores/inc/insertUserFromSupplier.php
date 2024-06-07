<?php

/* 
 * RSU es una aplicación desarrollada por el equipo TI de 
FOMENTO DE TÉCNICAS EXTREMEÑAS S.L. (FOTEX)
+34924207328
http://www.fotex.es
Comienzo del desarrollo: enero de 2019
 */

include ("../../../inc/includes.php");
GLOBAL $DB,$CFG_GLPI;
$objCommonDBT=new CommonDBTM;

$ident = $_GET['ident'];
$email = $_GET['email'];
$telefono = $_GET['telefono'];
$nombre = $_GET['nombre'];
$proveedorid = $_GET['proveedorid'];
$gestor = $_GET['gestor'];
$usuarioid = $_GET['usuarioid'];
$clave_sincod = $_GET['clave'];

//OBTENGO EL NOMBRE Y APELLIDOS
$nom = '';
$apellidos = '';
if(strpos($nombre,' ')>=0){
    $pos = strpos($nombre,' ');
    $nom = substr($nombre, 0, $pos);
    $apellidos = substr($nombre, $pos+1, strlen($nombre)-($pos+1));    
}else{
    $nom = $nombre;
    $apellidos = '';
}


//CODIFICO LA CLAVE, POR DEFECTO SERÁ UNA CADENA ALEATORIA DE 6 CARACTERES.
/**    $cons = array('b','c','d','f','g','h','j','k','l','m','n','p','r','s','t','v','w','x','y','z');
    $voca = array('a','e','i','o','u');
    srand((double)microtime()*1000000);
    $max = 6/2;
    $clave_sincod = '';
    for($i=1;$i<=$max;$i++){
        $clave_sincod .= $cons[rand(0,count($cons)-1)];
        $clave_sincod .= $voca[rand(0,count($voca)-1)];
    }
     if((6 % 2) == 1) $clave_sincod .= $cons[rand(0,count($cons)-1)];**/

$clave = auth::getPasswordHash($clave_sincod);
//PREGUNTO SI HAY QUE INCLUIRLO COMO GESTOR
if($gestor==1){
    //SI TIENE QUE SER GESTOR, HAY QUE VER SI EXISTE COMO USUARIO Y SI ESTÁ ENLAZADO CON LOS CONTACTOS
    //DEL PROVEEDOR 
    if($usuarioid>0){
        //COMPRUEBO QUE EXISTA EL USUARIO ENLAZADO Y QUE EL NOMBRE COINCIDA CON EL MAIL INDICADO
        $sql = "SELECT *
                FROM glpi_users 
                WHERE ID={$usuarioid} AND name='{$email}'";
        $existe = $DB->query($sql);
        $numUsuarios = $existe->num_rows;
        if($numUsuarios==1){
            //EL USUARIO YA EXISTE Y ADEMÁS ESTÁ IDENTIFICADO POR EL EMAIL INDICADO
            //COMPRUEBO QUE EL CORREO ELECTRÓNICO COINCIDE CON EL REGISTRADO
            $sql="SELECT * FROM glpi_useremails WHERE users_id = {$usuarioid} AND email = '{$email}'";
            $resulemail = $DB->query($sql);
            $numEmails = $resulemail->num_rows;
            if($numEmails>0){
                //Si es mayor que cero, significa 
                //que el usuario ya tiene registrado al menos un email 
                //igual que el indicado. En este caso no hay que hacer nada, solo por precaución activamos el usuario por si no
                //estuviese activado.         
                echo "El usuario ya se encontraba dado de alta en el sistema";
            }else{
                //si no hay ningún email igual para ese usuario,
                //hay que dar de alta ese email para ese usuario.
                $sql = "INSERT INTO glpi_useremails (users_id, is_default, is_dynamic, email) VALUES ({$usuarioid},1,1,'{$email}')";
                $insertaMail = $DB->query($sql);
                $nuevoUserEmailId = $DB->insert_id();
            }    
            $sql = "update glpi_users
                    set is_active = 1, profiles_id = 9
                    where id = {$usuarioid}";
            $activateUser = $DB->query($sql);         
            //Hay que enlazar el usuario obtenido con el perfil que debería tener si no lo tiene ya.
            $sql = "select * from glpi_profiles_users where users_id={$usuarioid} AND profiles_id=9";
            $profiles = $DB->query($sql);
            if($profiles->num_rows==0){
                $sql = "insert into glpi_profiles_users (users_id, profiles_id, entities_id, is_recursive, is_dynamic) 
                         values ({$usuarioid}, 9, 0, 0, 0)";
                $autorizacion = $DB->query($sql);
            }

            //HAY QUE ACTUALIZAR LOS CONTACTOS PARA REGISTRAR EL ID DE USUARIO Y GESTOR DEL CONTACTO
            $sql = "update glpi_plugin_comproveedores_contacts
                    set fkusuario = {$usuarioid}, editor = {$gestor}, clave='{$clave_sincod}'
                    where id = {$ident}";
            $updateContacts = $DB->query($sql);
        }else{
            //ESTE CASO NO PUEDE DARSE YA QUE IMPLICARÍA QUE HAY VARIOS USUARIOS CON EL MISMO ID
            echo "Error en la tabla glpi_users para el ID {$usuarioid}";
        }
    }else{
        //SI NO HAY IDUSUARIO HAY QUE BUSCAR EL USUARIO
        //CUYO NOMBRE = EMAIL
        $sql = "SELECT *
        FROM glpi_users
        WHERE name = '{$email}'";        
        $numUsuarios = $DB->query($sql);
        $num = $numUsuarios->num_rows;
        if($num > 1){
            //Se supone que ya existe un usuario de nombre = al email
            //lo enlazaremos con el contacto recogiendo su id
            while ($data=$DB->fetch_array($numUsuarios)) {
                $idusu = $data['id'];
            }       
            //En el hipotético caso de que tengamos un email genérico al estilo contratacion@asd.es,
            //que antes o ahora pertenezcan a otro usuario, el sistema debería utilizar este mismo usuario
            //pero alterando el nombre y apellidos, clave por defecto, activo si, teléfono para adaptarlo al nuevo contacto.
            //MODIFICO LOS DATOS DEL ANTIGUO USUARIO CON LOS NUEVOS DATOS RECOGIDOS.
            $sql = "select * from glpi_profiles_users where users_id={$usuarioid} AND profiles_id=9";            
            if($profiles->num_rows==0){
                $sql = "insert into glpi_profiles_users (users_id, profiles_id, entities_id, is_recursive, is_dynamic) 
                         values ({$usuarioid}, 9, 0, 0, 0)";
                $autorizacion = $DB->query($sql);
            }            
            $sql = "UPDATE glpi_users
                        SET name = '{$email}',
                        password = '{$clave}',
                        phone = '{$telefono}',
                        realname = '{$nom}',
                        firstname = '{$apellidos}',
                        is_active = 1,
                        auths_id = 0,
                        authtype = 1,
                        date_mod = now(),
                        is_deleted = 0,
                        profiles_id = 9,
                        entities_id = 0,
                        usertitles_id = 0,
                        usercategories_id = 0,
                        supplier_id = {$proveedorid}
                        where id = {$idusu}";
            $resultUpdate = $DB->query($sql);
            
            //Compruebo si ya existe el email registrado para ese usuario, si no existe lo inserto
            $sql = "SELECT * FROM glpi_useremails WHERE users_id=[$idusu} And email='{$email}'";
            $resultEmails = $DB->query($sql);
            if($resultEmails->num_rows==0){
                $sql = "INSERT INTO glpi_useremails (users_id,is_default,is_dynamic,email)
                        VALUES ({$idusu}, 1, 1, '{$email}')";
                $insertEmail = $DB->query($sql);
            }else{
                //En este caso, el usuario existe ya madificado anteriormente
                //y dispone de un correo electrónico igual al introducido por el usuario gestor.
                //Solo debemos indicar que el proceso es correcto.
            }
            //HAY QUE ACTUALIZAR EL CONTACTO PARA REGISTRAR EL ID DE USUARIO Y GESTOR DEL CONTACTO
            $sql = "update glpi_plugin_comproveedores_contacts
                    set fkusuario = {$idusu}, editor = 1, clave='{$clave_sincod}'
                    where id = {$ident}";
            $updateContacts = $DB->query($sql);
            echo "El usuario {$nombre} ha sido enlazado con éxito y activado como gestor.";
            
        }else{
            if($num == 0){              
                //En este caso se entiende que no existe el usuario especificado y
                //tampoco existe ese email entre los registrados.
                //Hay que proceder al alta del usuario y al alta del email.
                //ALTA DEL USUARIO
                $sql = "INSERT INTO glpi_users (name, password, phone, realname, firstname, is_active, auths_id, authtype, date_mod, is_deleted, profiles_id, entities_id, usertitles_id, usercategories_id, supplier_id)
                        VALUES ('{$email}', '{$clave}', '{$telefono}', '{$nom}', '{$apellidos}', 1, 0, 1, now(), 0, 9, 0, 0, 0, {$proveedorid})";
                $insertUser = $DB->query($sql);
                //inserto el nuevo usuario en los enlaces con los perfiles de usuario.
                $nuevoUsuarioId = $DB->insert_id();
                $sql = "insert into glpi_profiles_users (users_id, profiles_id, entities_id, is_recursive, is_dynamic) 
                         values ({$nuevoUsuarioId}, 9, 0, 0, 0)";
                $autorizacion = $DB->query($sql);
                //inserto el nuevo email enlazado con el nuevo usuario.
                $sql = "INSERT INTO glpi_useremails (users_id, is_default,is_dynamic,email)
                        VALUES ({$nuevoUsuarioId}, 1, 1, '{$email}')";
                $insertEmail = $DB->query($sql);      
                //HAY QUE ACTUALIZAR EL CONTACTO PARA REGISTRAR EL ID DE USUARIO Y GESTOR DEL CONTACTO
                $sql = "update glpi_plugin_comproveedores_contacts
                        set fkusuario = {$nuevoUsuarioId}, editor = 1,  clave='{$clave_sincod}'
                        where id = {$ident}";
                $updateContacts = $DB->query($sql);
                echo "El usuario {$nombre} ha sido dado de alta, enlazado y activado como gestor, con éxito.";                                       
                
            }else{
                //Este caso es muy extraño ya que supone la existencia de mas de un usuario con el mismo 
                //nombre, coincidente ademas con el email indicado.
                //En tal caso debemos indicar el error para que el administrador del sistema arregle
                //el fallo.
                echo "Existen {$numUsuarios->num_rows} con nombre {$email}. Comuniquelo al administrador del sistema para su solución.";
            }
        }
    } 
}else{
    $sql = "update glpi_plugin_comproveedores_contacts
            set editor = 0
            where id = {$ident}";
    $deactivateGestor = $DB->query($sql);      
    
}







