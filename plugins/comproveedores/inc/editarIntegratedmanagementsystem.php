<?php
include ("../../../inc/includes.php");
GLOBAL $DB,$CFG_GLPI;
$objCommonDBT=new CommonDBTM;

$id = $_GET['id'];
$plan_gestion = $_GET['plan_gestion'];
$obs_plan_gestion = $_GET['obs_plan_gestion'];
$control_documentos = $_GET['control_documentos'];
$obs_control_documentos = $_GET['obs_control_documentos'];
$politica_calidad = $_GET['politica_calidad'];
$obs_politica_calidad = $_GET['obs_politica_calidad'];
$auditorias_internas = $_GET['auditorias_internas'];
$obs_auditorias_internas = $_GET['obs_auditorias_internas'];
$plan_sostenibilidad = $_GET['plan_sostenibilidad'];
$obs_plan_sostenibilidad = $_GET['obs_plan_sostenibilidad'];
$sg_medioambiental = $_GET['sg_medioambiental'];
$obs_plan_sostenibilidad = $_GET['obs_plan_sostenibilidad'];
$sg_medioambiental = $_GET['sg_medioambiental'];
$obs_sg_medioambiental = $_GET['obs_sg_medioambiental'];
$acciones_rsc = $_GET['acciones_rsc'];
$obs_acciones_rsc = $_GET['obs_acciones_rsc'];
$gestion_rsc = $_GET['gestion_rsc'];
$obs_gestion_rsc = $_GET['obs_gestion_rsc'];
$sg_seguridad_y_salud = $_GET['sg_seguridad_y_salud'];
$obs_sg_seguridad_y_salud = $_GET['obs_sg_seguridad_y_salud'];
$certificado_formacion = $_GET['certificado_formacion'];
$obs_certificado_formacion = $_GET['obs_certificado_formacion'];
$departamento_segurida_y_salud = $_GET['departamento_segurida_y_salud'];
$obs_departamento_segurida_y_salud = $_GET['obs_departamento_segurida_y_salud'];
$metodologia_segurida_y_salud = $_GET['metodologia_segurida_y_salud'];
$obs_metodologia_segurida_y_salud = $_GET['obs_metodologia_segurida_y_salud'];
$formacion_segurida_y_salud = $_GET['formacion_segurida_y_salud'];
$obs_formacion_segurida_y_salud = $_GET['obs_formacion_segurida_y_salud'];
$empleado_rp = $_GET['empleado_rp'];
$obs_empleado_rp = $_GET['obs_empleado_rp'];
$empresa_asesoramiento = $_GET['empresa_asesoramiento'];
$obs_empresa_asesoramiento = $_GET['obs_empresa_asesoramiento'];
$procedimiento_subcontratistas = $_GET['procedimiento_subcontratistas'];
$obs_procedimiento_subcontratistas = $_GET['obs_procedimiento_subcontratistas'];
$cv_id = $_GET['cvid'];

$sql = "UPDATE glpi_plugin_comproveedores_integratedmanagementsystems 
SET plan_gestion = {$plan_gestion},
obs_plan_gestion = '{$obs_plan_gestion}',
control_documentos = {$control_documentos},
obs_control_documentos = '{$obs_control_documentos}',
politica_calidad = {$politica_calidad},
obs_politica_calidad = '{$obs_politica_calidad}',
auditorias_internas = {$auditorias_internas},
obs_auditorias_internas = '{$obs_auditorias_internas}',
plan_sostenibilidad = {$plan_sostenibilidad},
obs_plan_sostenibilidad = '{$obs_plan_sostenibilidad}',
sg_medioambiental = {$sg_medioambiental},
obs_sg_medioambiental = '{$obs_sg_medioambiental}',
acciones_rsc = {$acciones_rsc},
obs_acciones_rsc = '{$obs_acciones_rsc}',
gestion_rsc = {$gestion_rsc},
obs_gestion_rsc = '{$obs_gestion_rsc}',
sg_seguridad_y_salud = {$sg_seguridad_y_salud},
obs_sg_seguridad_y_salud = '{$obs_sg_seguridad_y_salud}',
certificado_formacion = {$certificado_formacion},
obs_certificado_formacion = '{$obs_certificado_formacion}',
departamento_segurida_y_salud = {$departamento_segurida_y_salud},
obs_departamento_segurida_y_salud = '{$obs_departamento_segurida_y_salud}',
metodologia_segurida_y_salud = {$metodologia_segurida_y_salud},
obs_metodologia_segurida_y_salud = '{$obs_metodologia_segurida_y_salud}',
formacion_segurida_y_salud = {$formacion_segurida_y_salud},
obs_formacion_segurida_y_salud = '{$obs_formacion_segurida_y_salud}',
empleado_rp = {$empleado_rp},
obs_empleado_rp = '{$obs_empleado_rp}',
empresa_asesoramiento = {$empresa_asesoramiento},
obs_empresa_asesoramiento = '{$obs_empresa_asesoramiento}',
procedimiento_subcontratistas = {$procedimiento_subcontratistas},
obs_procedimiento_subcontratistas = '{$obs_procedimiento_subcontratistas}',
cv_id = {$cv_id}
WHERE id = {$id}";
        
$DB->query($sql);

//echo $sql;
?>