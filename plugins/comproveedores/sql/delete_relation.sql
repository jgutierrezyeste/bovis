DROP TABLE IF EXISTS `glpi_plugin_comproveedores_categories`;
DROP TABLE IF EXISTS `glpi_plugin_comproveedores_communities`;
DROP TABLE IF EXISTS `glpi_plugin_comproveedores_comproveedores`;
DROP TABLE IF EXISTS `glpi_plugin_comproveedores_cvs`;
DROP TABLE IF EXISTS `glpi_plugin_comproveedores_experiences`;
DROP TABLE IF EXISTS `glpi_plugin_comproveedores_listspecialties`;
DROP TABLE IF EXISTS `glpi_plugin_comproveedores_roltypes`;
DROP TABLE IF EXISTS `glpi_plugin_comproveedores_specialties`;
DROP TABLE IF EXISTS `glpi_plugin_comproveedores_insurances`;
DROP TABLE IF EXISTS `glpi_plugin_comproveedores_empleados`;

ALTER TABLE `glpi_users` DROP COLUMN  `supplier_id`;

ALTER TABLE `glpi_profiles` DROP COLUMN  `create_cv_on_login`;


ALTER TABLE `glpi_suppliers` DROP COLUMN  `cv_id`;
ALTER TABLE `glpi_suppliers` DROP COLUMN  `cif`;
ALTER TABLE `glpi_suppliers` DROP COLUMN `forma_juridica`;
ALTER TABLE `glpi_suppliers` DROP COLUMN `locations_id`;

