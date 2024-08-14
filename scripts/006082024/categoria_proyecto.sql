INSERT INTO `bovis`.`glpi_plugin_comproveedores_experiencestypes` (`name`) VALUES ('Otros');

UPDATE `bovis`.`glpi_plugin_comproveedores_experiencestypes` SET `name` = 'CPD' WHERE (`id` = '11');

UPDATE glpi_projects SET plugin_comproveedores_experiencestypes_id = (SELECT id FROM glpi_plugin_comproveedores_experiencestypes where name = 'Otros')
WHERE (id = 129 || id = 93 || id = 120)
