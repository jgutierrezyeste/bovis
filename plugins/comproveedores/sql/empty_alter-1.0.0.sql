


ALTER TABLE `glpi_profiles` ADD `create_cv_on_login` TINYINT NOT NULL DEFAULT '0';
ALTER TABLE `glpi_users` ADD `supplier_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `glpi_suppliers` ADD `cv_id` INT(11);
ALTER TABLE `glpi_suppliers` ADD `cif` varchar(255);
ALTER TABLE `glpi_suppliers` ADD `forma_juridica` varchar(255);
ALTER TABLE `glpi_suppliers` ADD `locations_id` int(11) NOT NULL default '0';
ALTER TABLE `glpi_projecttasks` ADD `valor_contrato` DECIMAL(12,0) NOT NULL DEFAULT '0';
ALTER TABLE `glpi_projects` ADD `plugin_comproveedores_servicetypes_id` int(11) NOT NULL default '0';
ALTER TABLE `glpi_projects` ADD `plugin_comproveedores_experiencestypes_id` int(11) NOT NULL default '0';
ALTER TABLE `glpi_projects` ADD `plugin_comproveedores_communities_id` int(11) NOT NULL default '0';
ALTER TABLE `glpi_projects` ADD `plugin_comproveedores_provinces_id` int(11) NOT NULL default '0';
ALTER TABLE `glpi_projecttasks` CHANGE `plan_start_date` `plan_start_date` DATE NULL DEFAULT NULL;
ALTER TABLE `glpi_projecttasks` CHANGE `plan_end_date` `plan_end_date` DATE NULL DEFAULT NULL;
ALTER TABLE `glpi_projects` CHANGE `plan_start_date` `plan_start_date` DATE NULL DEFAULT NULL;
ALTER TABLE `glpi_projects` CHANGE `plan_end_date` `plan_end_date` DATE NULL DEFAULT NULL;
ALTER TABLE `glpi_projecttasks` ADD `tipo_especialidad` TINYINT(1) NOT NULL;
ALTER TABLE `glpi_projecttasks` ADD `is_delete` TINYINT(1) NOT NULL;
ALTER TABLE `glpi_projectteams` ADD `gerente` TINYINT(1) NOT NULL DEFAULT '0';


INSERT INTO `glpi_configs` (`id`, `context`, `name`, `value`) VALUES (NULL, 'core', 'meses_valoraciones', '4');
INSERT INTO `glpi_configs` (`id`, `context`, `name`, `value`) VALUES (NULL, 'core', 'asunto_correo', 'Evaluación');
INSERT INTO `glpi_configs` (`id`, `context`, `name`, `value`) VALUES (NULL, 'core', 'cuerpo_correo', 'Se tienen que realizar las evaluaciones para:');
INSERT INTO `glpi_configs` (`id`, `context`, `name`, `value`) VALUES (NULL, 'core', 'firma_correo', 'Bovis');
INSERT INTO `glpi_configs` (`id`, `context`, `name`, `value`) VALUES (NULL, 'core', 'remitente_correo', 'prueba@gmail.com');
INSERT INTO `glpi_configs` (`id`, `context`, `name`, `value`) VALUES (NULL, 'core', 'remitente_nombre', 'BOVIS');

INSERT INTO `glpi_crontasks` (`itemtype`, `name`, `frequency`, `param`, `state`, `mode`, `allowmode`, `hourmin`, `hourmax`, `logs_lifetime`, `lastrun`, `lastcode`, `comment`, `date_mod`, `date_creation`) VALUES
('PluginComproveedoresValuation', 'EvaluacionesRecordatorio', 86400, NULL, 1, 1, 3, 0, 24, 30, '2018-07-09 14:19:00', NULL, NULL, '2018-07-09 11:30:38', NULL);