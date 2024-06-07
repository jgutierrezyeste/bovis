
/* DROP TABLE IF EXISTS*/
DROP TABLE IF EXISTS 	glpi_plugin_portada_portadas,
						glpi_plugin_portada_portada_virtualComputers,
						glpi_plugin_portada_portada_physicalComputers,
						glpi_plugin_portada_portadatypes,
						glpi_plugin_portada_config,
						glpi_plugin_portada_environments;





CREATE TABLE `glpi_plugin_portada_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL,
  `comment` varchar(255),
  `route` varchar(40),
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

