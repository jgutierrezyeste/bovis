<?php
include ("../../../inc/includes.php");
GLOBAL $DB,$CFG_GLPI;
$objCommonDBT=new CommonDBTM;

$id = $_GET['id'];

        if ($id>0) {
            $idusu = 0;
            $sql = "SELECT * FROM glpi_plugin_comproveedores_contacts WHERE id=".$id;
            $contact = $DB->query($sql);
            while ($data=$DB->fetch_array($contact)) {
                $idusu = $data['fkusuario'];
            }             
            if($idusu>0){
                $sql = "UPDATE glpi_users SET is_active = 0 WHERE id=".idusu;
                $desactiva = $DB->query($sql);
            }
            $sql = "DELETE FROM glpi_plugin_comproveedores_contacts WHERE id=".$id;
            $result = $DB->query($sql);
        }
