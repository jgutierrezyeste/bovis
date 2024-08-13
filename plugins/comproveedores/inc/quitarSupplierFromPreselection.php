<?php
include ("../../../inc/includes.php");
GLOBAL $DB,$CFG_GLPI;
$objCommonDBT=new CommonDBTM;

$archivo=fopen("quitarlicitador.txt","w+");

$idlicitador = $_GET['idlicitador'];


$sql_adjudicatario= "SELECT * FROM glpi_plugin_comproveedores_preselections where id=".$idlicitador;

$result_adjudicatario=$DB->query($sql_adjudicatario);



$data=$DB->fetch_array($result_adjudicatario);
$data_supplier= $data['suppliers_id'];
$data_projecttask=$data['projecttasks_id'];


$sql_comprobacion= "SELECT * FROM glpi_projecttaskteams where projecttasks_id=".$data_projecttask. " and items_id=".$data_supplier;
$result_comprobacion=$DB->query($sql_comprobacion);


if($result_comprobacion->num_rows == 1){
    $sql ="DELETE FROM glpi_projecttaskteams where projecttasks_id=".$data_projecttask. " and items_id=".$data_supplier;
    $result1 = $DB->query($sql);

    $sql2= "UPDATE glpi_plugin_comproveedores_preselections set is_deleted=1 where id=".$idlicitador;
    $result2 = $DB->query($sql2);

}
else
    {
        if ($idlicitador!=0) {
            //$sql = "DELETE FROM glpi_plugin_comproveedores_preselections WHERE id=".$idlicitador;
            $sql= "UPDATE glpi_plugin_comproveedores_preselections set is_deleted=1 where id=".$idlicitador;
            $result = $DB->query($sql);
        }
    }

