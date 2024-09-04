/*correción provinca Almería, que pertenece a comunidad de Andalucía y aparece en Comunidad Valenciana*/
UPDATE `bovis`.`glpi_plugin_comproveedores_provinces` SET `plugin_comproveedores_communities_id` = (select id from glpi_plugin_comproveedores_communities where name='Andalucía') WHERE (`name` = 'Almería');
