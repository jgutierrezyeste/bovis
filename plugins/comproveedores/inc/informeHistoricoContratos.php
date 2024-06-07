<?php
include ("../../../inc/includes.php");
GLOBAL $DB,$CFG_GLPI;
$objCommonDBT=new CommonDBTM;

//$id = $_POST['id'];
include_once ("../../../vendor/fpdf181/fpdf.php");
/**
        $query = "select project.id as projectid, 
            project.name as projectname, 
            task.id as taskid, 
            task.name as taskname, 
            DATE_FORMAT(task.ini,'%d/%m/%Y') as ini, 
            DATE_FORMAT(task.fin,'%d/%m/%Y') as fin, 
            replace(concat(format(valor_contrato,0), ' €'), ',', '.') as presupuesto_objetivo,
            replace(concat(format(importe_ofertado,0), ' €'), ',', '.') as importe_ofertado, 
            replace(concat(format(importe_licitado_ganador(task.id),0), ' €'), ',', '.') as importe_ofertado_ganador, 
            calidad_oferta, 
            calidad_oferta_ganador(task.id) as calidad_oferta_ganador,
            comentarios,
            if((select count(teams.id) as num from glpi_projecttaskteams as teams where teams.projecttasks_id=task.id and teams.items_id=supplier.id)>0, 
                                   1, 
                                   if((select count(teams.id) as num from glpi_projecttaskteams as teams where teams.projecttasks_id=task.id and teams.items_id<>supplier.id)>0, 0, -1 )) as resultado            
            from glpi_suppliers as supplier
                left join glpi_plugin_comproveedores_preselections as pre on supplier.id = pre.suppliers_id 
                left join glpi_projecttasks as task on task.id = pre.projecttasks_id          
                left join glpi_projects as project on project.id = task.projects_id
             where supplier.cv_id = {$cvid} order by 1 desc";
         //echo $query;
        $result = $DB->query($query);**/

// Creación del objeto de la clase heredada
/*
class PDF extends FPDF{
    function Header() {
        // Logo
        $this->Image('../../../pics/fd_logo.png',10,8,33);
        // Arial bold 15
        $this->SetFont('Arial','B',15);
        // Movernos a la derecha
        $this->Cell(80);
        // Título
        $this->Cell(30,10,'Title',1,0,'C');
        // Salto de línea
        $this->Ln(20);
    }
}*/
//header('Content-type: application/pdf');
$pdf = new FPDF('P','mm','Letter');
$pdf->AddFont('Arial','');
$pdf->AddPage();
$pdf->SetFont('Arial','',11);
$pdf->Cell(50,10,'yo');
$pdf->Output();

//echo "yo";