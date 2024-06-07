

CREATE TABLE IF NOT EXISTS `glpi_plugin_comproveedores_comproveedores` (
	`id` int(11) NOT NULL auto_increment,
	`user_id` int(11) NOT NULL,
	`name` varchar(255) NOT NULL default '',


	`entities_id` int(11) NOT NULL default '0',
	`is_recursive` tinyint(1) NOT NULL default '0',

	`states_id` int(11) NOT NULL default '0',
	`comment` text,
	`template_name` varchar(255) collate utf8_unicode_ci default NULL,
	`is_deleted` tinyint(1) NOT NULL default '0', 
	`is_template` tinyint(1) NOT NULL default '0',
	`is_helpdesk_visible` 								int(11) NOT NULL default '1',
	`externalid` varchar(255) NULL,

	PRIMARY KEY  (`id`),
	KEY `entities_id` (`entities_id`),
	KEY `is_deleted` (`is_deleted`),
	KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE IF NOT EXISTS `glpi_plugin_comproveedores_cvs` (
	`id` int(11) NOT NULL auto_increment,
	`name` varchar(255) NOT NULL default '',
	`supplier_id` int(11) NOT NULL default '0',

	`empresa_matriz_nombre` varchar(255),
	`empresa_matriz_direccion` varchar(255),
	`empresa_matriz_pais` varchar(255),
	`empresa_matriz_provincia` varchar(255),
	`empresa_matriz_ciudad` varchar(255),
	`empresa_matriz_CP` varchar(255),
	`titulacion_superior` INT(11) NOT NULL default '0',
	`titulacion_grado_medio` INT(11) NOT NULL default '0',
	`tecnicos_no_universitarios` INT(11) NOT NULL default '0',
	`personal` INT(11) NOT NULL default '0',
	`otros_categoria_numeros_empleados` INT(11) NOT NULL default '0',
	`capital_social` decimal(20,2) NULL,
	`states_id` int(11) NOT NULL default '0',
	`entities_id` int(11) NOT NULL default '0',
	`is_recursive` tinyint(1) NOT NULL default '0',
	`comment` text,
	`externalid` varchar(255) NULL,
	PRIMARY KEY (`id`),	
	KEY `entities_id` (`entities_id`),
	UNIQUE (`externalid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE IF NOT EXISTS `glpi_plugin_comproveedores_experiences` (
	`id` int(11) NOT NULL auto_increment,
	`name`varchar(255),
	`estado` tinyint(1),
	`intervencion_bovis`tinyint(1) not null default '0',
	`plugin_comproveedores_experiencestypes_id` int(11) ,
	`plugin_comproveedores_communities_id` int(11) ,
	`cliente` varchar(255) ,
	`anio` date,
	`importe` decimal(20,2) ,
	`duracion` int(11) ,
	`bim` tinyint(1),
	`breeam` tinyint(1) ,
	`leed` tinyint(1) ,
	`otros_certificados` tinyint(1) ,
	`observaciones` varchar(255) ,
	`cv_id` int(11) ,
	`is_deleted` tinyint(1) NOT NULL default '0',
	`externalid` varchar(255) NULL,
	`is_recursive` tinyint(1) NOT NULL default '0',
	`entities_id` int(11) NOT NULL default '0',
	PRIMARY KEY (`id`),
	KEY `name` (`name`),
	KEY `entities_id` (`entities_id`)

	
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `glpi_plugin_comproveedores_communities` (
	`id` int(11) NOT NULL auto_increment,
	`name`varchar(255),
	`is_deleted` tinyint(1) NOT NULL default '0',
	`externalid` varchar(255) NULL,
	`is_recursive` tinyint(1) NOT NULL default '0',
	`entities_id` int(11) NOT NULL default '0',
	PRIMARY KEY (`id`),
	KEY `name` (`name`),
	KEY `entities_id` (`entities_id`)

) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `glpi_plugin_comproveedores_provinces` (
	`id` int(11) NOT NULL auto_increment,
	`name`varchar(255),
                `plugin_comproveedores_communities_id` int(11) NOT NULL,
	`is_deleted` tinyint(1) NOT NULL default '0',
	`externalid` varchar(255) NULL,
	`is_recursive` tinyint(1) NOT NULL default '0',
	`entities_id` int(11) NOT NULL default '0',
	PRIMARY KEY (`id`),
	KEY `name` (`name`),
	KEY `entities_id` (`entities_id`)

) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `glpi_plugin_comproveedores_provinces` (`id`, `name`, `plugin_comproveedores_communities_id`, `is_deleted`, `externalid`, `is_recursive`, `entities_id`) 
VALUES 
('1', 'La Coruña', '12', '0', NULL, '0', '0'),
('2', 'Álava', '16', '0', NULL, '0', '0'),
('3', 'Albacete', '8', '0', NULL, '0', '0'),
('4', 'Alicante', '10', '0', NULL, '0', '0'),
('5', 'Almería', '10', '0', NULL, '0', '0'),
('6', 'Asturias', '3', '0', NULL, '0', '0'),
('7', 'Ávila', '7', '0', NULL, '0', '0'),
('8', 'Badajoz', '11', '0', NULL, '0', '0'),
('9', 'Islas Baleares', '4', '0', NULL, '0', '0'),
('10', 'Barcelona', '9', '0', NULL, '0', '0'),
('11', 'Burgos', '7', '0', NULL, '0', '0'),
('12', 'Cáceres', '11', '0', NULL, '0', '0'),
('13', 'Cádiz', '1', '0', NULL, '0', '0'),
('14', 'Cantabria', '6', '0', NULL, '0', '0'),
('15', 'Castellón', '10', '0', NULL, '0', '0'),
('16', 'Ciudad Real', '8', '0', NULL, '0', '0'),
('17', 'Córdoba', '1', '0', NULL, '0', '0'),
('18', 'Cuenca', '8', '0', NULL, '0', '0'),
('19', 'Girona', '9', '0', NULL, '0', '0'),
('20', 'Granada', '1', '0', NULL, '0', '0'),
('21', 'Guadalajara', '8', '0', NULL, '0', '0'),
('22', 'Guipúzcoa', '16', '0', NULL, '0', '0'),
('23', 'Huelva', '1', '0', NULL, '0', '0'),
('24', 'Huesca', '2', '0', NULL, '0', '0'),
('25', 'Jaén', '1', '0', NULL, '0', '0'),
('26', 'La Rioja', '17', '0', NULL, '0', '0'),
('27', 'Las Palmas', '5', '0', NULL, '0', '0'),
('28', 'León', '7', '0', NULL, '0', '0'),
('29', 'Lleida', '9', '0', NULL, '0', '0'),
('30', 'Lugo', '12', '0', NULL, '0', '0'),
('31', 'Madrid', '13', '0', NULL, '0', '0'),
('32', 'Málaga', '1', '0', NULL, '0', '0'),
('33', 'Murcia', '14', '0', NULL, '0', '0'),
('34', 'Navarra', '15', '0', NULL, '0', '0'),
('35', 'Orense', '12', '0', NULL, '0', '0'),
('36', 'Palencia', '7', '0', NULL, '0', '0'),
('37', 'Pontevedra', '12', '0', NULL, '0', '0'),
('38', 'Salamanca', '7', '0', NULL, '0', '0'),
('39', 'Segovia', '7', '0', NULL, '0', '0'),
('40', 'Sevilla', '1', '0', NULL, '0', '0'),
('41', 'Soria', '7', '0', NULL, '0', '0'),
('42', 'Tarragona', '9', '0', NULL, '0', '0'),
('43', 'Santa Cruz de Tenerife', '5', '0', NULL, '0', '0'),
('44', 'Teruel', '2', '0', NULL, '0', '0'),
('45', 'Toledo', '8', '0', NULL, '0', '0'),
('46', 'Valencia', '10', '0', NULL, '0', '0'),
('47', 'Valladolid', '7', '0', NULL, '0', '0'),
('48', 'Vizcaya', '16', '0', NULL, '0', '0'),
('49', 'Zamora', '7', '0', NULL, '0', '0'),
('50', 'Zaragoza', '2', '0', NULL, '0', '0'),
('51', 'Ceuta', '18', '0', NULL, '0', '0'),
('52', 'Melilla', '19', '0', NULL, '0', '0');


CREATE TABLE IF NOT EXISTS `glpi_plugin_comproveedores_listspecialties` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `plugin_comproveedores_roltypes_id` int(11) DEFAULT NULL,
  `plugin_comproveedores_categories_id` int(11) NOT NULL,
  `plugin_comproveedores_specialties_id` int(11) DEFAULT NULL,
  `cv_id` int(11) DEFAULT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `glpi_plugin_comproveedores_empleados` (
	`id` int(11) NOT NULL auto_increment,
	`empleados_eventuales` varchar(255),
	`empleados_fijos` varchar(255),
	`anio` int(11) ,
	`cv_id` int(11) ,


	`is_deleted` tinyint(1) NOT NULL default '0',
	`externalid` varchar(255) NULL,
	`is_recursive` tinyint(1) NOT NULL default '0',
	`entities_id` int(11) NOT NULL default '0',
	PRIMARY KEY (`id`),
	KEY `entities_id` (`entities_id`)

	
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `glpi_plugin_comproveedores_communities` (`id`, `name`) VALUES
(1, 'Andalucía'),
(2,	'Aragón'),
(3, 'Principado de Asturias'),
(4, 'Illes Balears'),
(5, 'Canarias'),
(6, 'Cantabria'),
(7, 'Castilla y León'),
(8, 'Castilla - La Mancha'),
(9, 'Cataluña'),
(10, 'Comunitat Valenciana'),
(11, 'Extremadura'),
(12, 'Galicia'),
(13, 'Comunidad de Madrid '),
(14, 'Región de Murcia'),
(15, 'Comunidad Foral de Navarra'),
(16, 'País Vasco'),
(17, 'La Rioja'),
(18, 'Ceuta'),
(19, 'Melilla');

CREATE TABLE IF NOT EXISTS `glpi_plugin_comproveedores_insurances` (
	`id` int(11) NOT NULL auto_increment,
	`name` varchar(255) NOT NULL default '',
	`cia_aseguradora` varchar(255) NOT NULL default '',
	`cuantia` int(11) NOT NULL default '0',
	`fecha_caducidad` date,
	`numero_empleados_asegurados` int(11) NOT NULL default '0',
	`cv_id` int(11) NOT NULL default '0',

	`is_deleted` tinyint(1) NOT NULL default '0',
	`externalid` varchar(255) NULL,
	`is_recursive` tinyint(1) NOT NULL default '0',
	`entities_id` int(11) NOT NULL default '0',
	PRIMARY KEY (`id`),
	KEY `entities_id` (`entities_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `glpi_plugin_comproveedores_experiencestypes` (
	`id` int(11) NOT NULL auto_increment,
	`name`varchar(255),
	`descripcion`varchar(255),
	`is_deleted` tinyint(1) NOT NULL default '0',
	`externalid` varchar(255) NULL,
	`is_recursive` tinyint(1) NOT NULL default '0',
	`entities_id` int(11) NOT NULL default '0',
	PRIMARY KEY (`id`),
	KEY `name` (`name`),
	KEY `entities_id` (`entities_id`)

) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `glpi_plugin_comproveedores_experiencestypes` (`id`, `name`, `descripcion`) VALUES
(1, 'Oficinas', 'Edificios de oficinas'),
(2,	'Comerciales', 'Centros comerciales/locales comerciales'),
(3, 'Hospitales', 'Proyectos de hospitales/Centros sanitarios'),
(4, 'Hoteles', 'Proyectos de hoteles/Residencias 3ª edad/Residencias estudiantes'),
(5, 'Culturales', 'Proyectos de equipamiento-museos, Centros culturales, Auditorios, Centros de convenciones, palacios congresos'),
(6, 'Docentes', 'Centros docentes(Universidades,Institutos de enseñanza, Guarderías infatiles,etc)'),
(7, 'Deportes', 'Complejos deportivos(Estadios de fútbol, Pabellones deportivos, Polideportivos, etc)'),
(8, 'Industriales', 'Proyectos industriales/Logísticos'),
(9, 'Viviendas', 'Proyectos de vivienda residenciales'),
(10, 'Rehabilitacion', 'Obras de rehabilitación de edificios'),
(11, 'CPD', 'Centro de procesos de datos(CPD) y otros proyectos'),
(12, 'Otros', 'Otros CPD');

CREATE TABLE IF NOT EXISTS `glpi_plugin_comproveedores_integratedmanagementsystems` (
	`id` int(11) NOT NULL auto_increment,

	`plan_gestion` tinyint(1) NOT NULL default '0',
	`obs_plan_gestion` varchar(255) NULL,
	`control_documentos` tinyint(1) NOT NULL default '0',
	`obs_control_documentos` varchar(255) NULL,
	`politica_calidad` tinyint(1) NOT NULL default '0',
	`obs_politica_calidad` varchar(255) NULL,
	`auditorias_internas` tinyint(1) NOT NULL default '0',
	`obs_auditorias_internas` varchar(255) NULL,
	`plan_sostenibilidad` tinyint(1) NOT NULL default '0',
	`obs_plan_sostenibilidad` varchar(255) NULL,
	`sg_medioambiental` tinyint(1) NOT NULL default '0',
	`obs_sg_medioambiental` varchar(255) NULL,
	`acciones_rsc` tinyint(1) NOT NULL default '0',
	`obs_acciones_rsc` varchar(255) NULL,
	`gestion_rsc` tinyint(1) NOT NULL default '0',
	`obs_gestion_rsc` varchar(255) NULL,
	`sg_seguridad_y_salud` tinyint(1) NOT NULL default '0',
	`obs_sg_seguridad_y_salud` varchar(255) NULL,
	`certificado_formacion` tinyint(1) NOT NULL default '0',
	`obs_certificado_formacion` varchar(255) NULL,
	`departamento_segurida_y_salud` tinyint(1) NOT NULL default '0',
	`obs_departamento_segurida_y_salud` varchar(255) NULL,
	`metodologia_segurida_y_salud` tinyint(1) NOT NULL default '0',
	`obs_metodologia_segurida_y_salud` varchar(255) NULL,
	`formacion_segurida_y_salud` tinyint(1) NOT NULL default '0',
	`obs_formacion_segurida_y_salud` varchar(255) NULL,
	`empleado_rp` tinyint(1) NOT NULL default '0',
	`obs_empleado_rp` varchar(255) NULL,
	`empresa_asesoramiento` tinyint(1) NOT NULL default '0',
	`obs_empresa_asesoramiento` varchar(255) NULL,
	`procedimiento_subcontratistas` tinyint(1) NOT NULL default '0',
	`obs_procedimiento_subcontratistas` varchar(255) NULL,

	`cv_id` int(11) NOT NULL default '0',
	`entities_id` int(11) NOT NULL default '0',
	`is_recursive` tinyint(1) NOT NULL default '0',
	`comment` text,
	`externalid` varchar(255) NULL,
	PRIMARY KEY (`id`),	
	KEY `entities_id` (`entities_id`),
	UNIQUE (`externalid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `glpi_plugin_comproveedores_lossratios` (
	`id` int(11) NOT NULL auto_increment,

	`anio` date NULL,
	`incidencia` decimal(4,2) NULL,
	`frecuencia` decimal(4,2) NULL,
	`gravedad` decimal(4,2) NULL,

	`cv_id` int(11) NOT NULL default '0',
	`entities_id` int(11) NOT NULL default '0',
	`is_recursive` tinyint(1) NOT NULL default '0',
	`comment` text,
	`externalid` varchar(255) NULL,
	PRIMARY KEY (`id`),	
	KEY `entities_id` (`entities_id`),
	UNIQUE (`externalid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `glpi_plugin_comproveedores_annualbillings` (
	`id` int(11) NOT NULL auto_increment,

	`anio` date NULL,
	`facturacion` decimal(12,0),
	`beneficios_impuestos` decimal(12,0) NULL,
	`resultado` decimal(12,0) NULL,
	`total_activo` decimal(12,0) NULL,
	`activo_circulante` decimal(12,0) NULL,
	`pasivo_circulante` decimal(12,0) NULL,
	`cash_flow` decimal(12,0) NULL,
	`fondos_propios` decimal(12,0) NULL,
	`recursos_ajenos` decimal(12,0) NULL,
	
	`cv_id` int(11) NOT NULL default '0',
	`entities_id` int(11) NOT NULL default '0',
	`is_recursive` tinyint(1) NOT NULL default '0',
	`comment` text,
	`externalid` varchar(255) NULL,
	PRIMARY KEY (`id`),	
	KEY `entities_id` (`entities_id`),
	UNIQUE (`externalid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `glpi_plugin_comproveedores_featuredcompanies` (
	`id` int(11) NOT NULL auto_increment,

	`nombre_empresa_destacada` varchar(255) NULL,
	`puesto` int(11) NOT NULL default '0',
		
	`cv_id` int(11) NOT NULL default '0',
	`entities_id` int(11) NOT NULL default '0',
	`is_recursive` tinyint(1) NOT NULL default '0',
	`comment` text,
	`externalid` varchar(255) NULL,
	PRIMARY KEY (`id`),	
	KEY `entities_id` (`entities_id`),
	UNIQUE (`externalid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `glpi_plugin_comproveedores_subcontractingcompanies` (
	`id` int(11) NOT NULL auto_increment,

	`nombre_empresa_subcontratista` varchar(255) NULL,
	`puesto` int(11) NOT NULL default '0',
		
	`cv_id` int(11) NOT NULL default '0',
	`entities_id` int(11) NOT NULL default '0',
	`is_recursive` tinyint(1) NOT NULL default '0',
	`comment` text,
	`externalid` varchar(255) NULL,
	PRIMARY KEY (`id`),	
	KEY `entities_id` (`entities_id`),
	UNIQUE (`externalid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `glpi_plugin_comproveedores_previousnamescompanies` (
	`id` int(11) NOT NULL auto_increment,

	`nombre` varchar(255) NULL,
	`fecha_cambio` datetime(6) NULL,
		
	`cv_id` int(11) NOT NULL default '0',
	`entities_id` int(11) NOT NULL default '0',
	`is_recursive` tinyint(1) NOT NULL default '0',
	`comment` text,
	`externalid` varchar(255) NULL,
	PRIMARY KEY (`id`),	
	KEY `entities_id` (`entities_id`),
	UNIQUE (`externalid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `glpi_plugin_comproveedores_valuations` (
        `id` int(11) NOT NULL auto_increment,
        `projecttasks_id` int(11) NOT NULL default '0',
        `cv_id` int(11) NOT NULL default '0',
        `calidad` DECIMAL(3,2) NOT NULL DEFAULT '0',
        `planificacion` DECIMAL(3,2) NOT NULL DEFAULT '0',
        `costes` DECIMAL(3,2) NOT NULL DEFAULT '0',
        `cultura_empresarial` DECIMAL(3,2) NOT NULL DEFAULT '0',
        `gestion_de_suministros_y_subcontratistas` DECIMAL(3,2) NOT NULL DEFAULT '0',
        `seguridad_y_salud_y_medioambiente` DECIMAL(3,2) NOT NULL DEFAULT '0',
        `bim` DECIMAL(3,2) NOT NULL DEFAULT '0',
        `certificacion_medioambiental` DECIMAL(3,2) NOT NULL DEFAULT '0',
        `proyecto_basico` DECIMAL(3,2) NOT NULL DEFAULT '0',
        `proyecto_de_ejecucion` DECIMAL(3,2) NOT NULL DEFAULT '0',
        `capacidad_de_la_empresa` DECIMAL(3,2) NOT NULL DEFAULT '0',
        `colaboradores` DECIMAL(3,2) NOT NULL DEFAULT '0',
        `capacidad` DECIMAL(3,2) NOT NULL DEFAULT '0',
        `actitud` DECIMAL(3,2) NOT NULL DEFAULT '0',

        `fecha` DATETIME NULL,
        `evaluacion_final` TINYINT(1) NOT NULL DEFAULT '0',
        `comentario` VARCHAR(255) NULL,

        `is_deleted` tinyint(1) NOT NULL default '0',
        `externalid` varchar(255) NULL,
        `is_recursive` tinyint(1) NOT NULL default '0',
        `entities_id` int(11) NOT NULL default '0',
        PRIMARY KEY (`id`),
        KEY `entities_id` (`entities_id`)

) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `glpi_plugin_comproveedores_subpaquetes` (
	`id` int(11) NOT NULL auto_increment,
	`name` varchar(255) NULL,
                `projecttasks_id` int(11) NOT NULL default '0',
                `suppliers_id` int(11) NOT NULL default '0',
                `valoracion`varchar(255) NULL,
 	`is_deleted` tinyint(1) NOT NULL default '0',
	`externalid` varchar(255) NULL,
	`is_recursive` tinyint(1) NOT NULL default '0',
	`entities_id` int(11) NOT NULL default '0',
	PRIMARY KEY (`id`),
	KEY `entities_id` (`entities_id`)

) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `glpi_plugin_comproveedores_subvaluations` (
        `id` int(11) NOT NULL auto_increment,
        `valuation_id` int(11) NOT NULL default '0',
        `criterio_id` int(11) NOT NULL default '0',
        `valor` int(1) NOT NULL default '0',
        `comentario` VARCHAR(255) NULL DEFAULT NULL,

        `is_deleted` tinyint(1) NOT NULL default '0',
        `externalid` varchar(255) NULL,
        `is_recursive` tinyint(1) NOT NULL default '0',
        `entities_id` int(11) NOT NULL default '0',
        PRIMARY KEY (`id`),
        KEY `entities_id` (`entities_id`)

) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `glpi_plugin_comproveedores_criterios` (
	`id` int(11) NOT NULL auto_increment,
	`criterio_padre` varchar(255) NULL,
                `criterio_hijo` varchar(255) NULL,
                `ponderacion` int(3) NOT NULL default '0',
                `denom_Mala` longtext NULL,
                `denom_Excelente` longtext NULL,
                `tipo_especialidad` tinyint(1) NOT NULL default '0',

 	`is_deleted` tinyint(1) NOT NULL default '0',
	`externalid` varchar(255) NULL,
	`is_recursive` tinyint(1) NOT NULL default '0',
	`entities_id` int(11) NOT NULL default '0',
	PRIMARY KEY (`id`),
	KEY `entities_id` (`entities_id`)

) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `glpi_plugin_comproveedores_criterios` (`id`, `criterio_padre`, `criterio_hijo`, `ponderacion`, `denom_Mala`, `denom_Excelente`, `tipo_especialidad`, `is_deleted`, `externalid`, `is_recursive`, `entities_id`) 
VALUES('1', 'calidad', 'Gestión documentación. Planos e información', '15', 
'No existe ni se respeta un protocolo para la emisión de documentación. Es insuficiente y se entrega tarde y con muchos errores. BOVIS tiene que reclamarla constantemente.',
'Procedimientos empleados para la entrega de la documentación en plazo, forma y calidad, excelentes.',
 '2', '0', NULL, '0', '0'),
 ('2', 'calidad', 'Calidad de la ejecución', '45', 
'Calidad general inaceptable. No alineada con las prioridades del cliente. Nivel de control bajo. Necesidad significativa de rehacer trabajos. No atiende diligentemente a las instrucciones de la DF, deja muchos pendientes para el periodo de repasos. ',
'Calidad de la ejecución excelente, mejor de lo esperado. La actitud es hacerlo bien a la primera. Se cumple eficazmente con el plan de calidad. La intervención requerida de la DF o de BOVIS para conseguirlo es mínima. Comprensión de las necesidades del cliente.',
 '2', '0', NULL, '0', '0'),
 ('3', 'calidad', 'PPIs', '10', 
'',
'',
'2', '0', NULL, '0', '0'),
('4', 'calidad', 'Defectos a la entrega', '30', 
'Defectos numerosos/significativos con gran impacto en la operatividad. Gestión deficiente de los repasos, incumplimiento de fechas de resolución.',
'Muy pocos defectos y poco significativos. Gestión muy eficaz de la resolución de los mismos.',
 '2', '0', NULL, '0', '0'),
('5', 'planificacion', 'Exactitud planificación', '25', 
' Incumplimiento sistemático de la planificación presentada.',
'Planificación realista. Capacidad y recursos suficientes para cumplir la planificación en todas sus fases. Se cumple al 100 % la planificación presentada.',
'2', '0', NULL, '0', '0'),
('6', 'planificacion', 'Hitos clave', '50', 
'Incumplimiento de hitos clave. Acciones de mitigación inexistentes.',
'Actitud proactiva para el cumplimiento o mejora de los hitos clave. Se avisa con tiempo suficiente de posibles retrasos para establecer y aplicar planes de mitigación.',
'2', '0', NULL, '0', '0'),
('7', 'planificacion', 'Calidad planificación e informes de progreso', '25', 
'Planificación irreal y excesivamente genérica. Información pobre y con errores, niveles mínimos de detalle. No se entregan actualizaciones.',
'Información proporcionada fiable, detallada. Actualizaciones regulares de la planificación según calendario.',
'2', '0', NULL, '0', '0'),
('8', 'costes', 'Presupuesto contrato vs Coste real', '40', 
'Las reclamaciones exceden el 2% del importe del contrato. El periodo de cierre de la liquidación es de más de 12 semanas.',
'No se presentan reclamaciones no ligadas a cambios no generados por el propio contratista. El periodo de cierre de la liquidación es de menos de 4 semanas.',
'2', '0', NULL, '0', '0'),
('9', 'costes', 'Actitud', '30', 
'No comprometidos con los objetivos de coste del proyecto. Reclamaciones constantes sin fundamento y/o fuera de plazo. Incapacidad de negociación.  Insistencia en cambiar los productos especificados sin beneficio claro para el proyecto.',
'Actitud proactiva en el cumplimiento de presupuestos. Alertas tempranas de posibles sobrecostes. Excelente disposición para llegar a soluciones satisfactorias para ambas partes. Proactividad en la identificación de productos/materiales y técnicas que mejoren plazos y coste sin reducir calidad.',
'2', '0', NULL, '0', '0'),
('10', 'costes', 'Respuesta ante cambios e identificación de sobrecostes', '30', 
'Plazo de respuesta a peticiones de valoración duplica al establecido. No se respetan los precios de contrato. La presentación de posibles sobrecostes se efectúa pasados dos meses del periodo en el que se originan.',
'Plazo de respuesta a peticiones de valoración más corto que el establecido. Se respetan los precios de contrato. Se informa de posibles sobrecostes a los quince días de que se originen debido a instrucciones en obra o 15 días antes de iniciarse los trabajos afectados.',
'2', '0', NULL, '0', '0'),
('11', 'cultura_empresarial', 'Innovación y oficina técnica', '10', 
'Soluciones técnicas muy pobres, no promueve ni emplea nuevas técnicas.',
'Oficina técnica potente. Soluciones constructivas que mejoran el rendimiento del proyecto.',
'2', '0', NULL, '0', '0'),
('12', 'cultura_empresarial', 'Cultura', '35', 
'Empresa conflictiva. Enfocada en maximizar su beneficio a costa de los objetivos del proyecto. Incumple reiteradamente los procedimientos de gestión de proyecto.',
'Empresa enfocada a trabajar en colaboración con el resto de agentes. Alineada con los objetivos del proyecto. Proactiva. Se adapta y cumple los procedimientos marcados por BOVIS y propone mejoras en la gestión del proyecto.',
'2', '0', NULL, '0', '0'),
('13', 'cultura_empresarial', 'Apoyo corporativo', '30', 
'Apoyo inadecuado e insuficiente. Desinterés por parte del equipo directivo.',
'Excelente apoyo. El equipo directivo de la empresa se involucra activamente en el desarrollo y éxito del proyecto.',
'2', '0', NULL, '0', '0'),
('14', 'cultura_empresarial', 'Equipo de obra', '25', 
'Equipo escaso, con poca experiencia, competencia y motivación a nivel general. Descoordinación entre los miembros del equipo.',
'Equipo con recursos suficientes, motivado, colaborador y muy competente. Contribución significativa al desarrollo del proyecto.',
'2', '0', NULL, '0', '0'), 
('15', 'gestion_de_suministros_y_subcontratistas', 'Gestión de suministros', '50', 
'Nulo control sobre el calendario de compras y plazos de entrega. Tendencia a trabajar con una filosofía "just in time" y poca previsión.',
'Control exhaustivo del calendario de contratación de suministros y proveedores. Gestión coordinada de entregas.',
'2', '0', NULL, '0', '0'),
('16', 'gestion_de_suministros_y_subcontratistas', 'Gestión de subcontratistas', '50', 
'Elevada subcontratación, subcontratistas no habituales, actitud no cooperadora. Poco control de la calidad de ejecución y escasa coordinación en obra.',
'Sistema de subcontratación excelente. Sistemas de control de calidad de la cadena de suministro y subcontratación. Filosofía de colaboración y confianza con subcontratistas. Gestión coordinada de diseño (planos de taller, etc.), ejecución, calendarios de pruebas.',
'2', '0', NULL, '0', '0'),
('17', 'seguridad_y_salud_y_medioambiente', 'Indicadores de SysS', '25', 
'Los indicadores de accidentalidad y de siniestralidad no cumplen con los mínimos de BOVIS.',
'Los indicadores de accidentalidad y de siniestralidad cumplen al 100% con los mínimos de BOVIS',
'2', '0', NULL, '0', '0'),
('18', 'seguridad_y_salud_y_medioambiente', 'Compromiso con la SyS', '25', 
'Medios de prevención y protección inadecuados y/o escasos. Ningún compromiso por parte del equipo de obra. Se incumplen sistemáticamente la normativa y las instrucciones dadas.',
'Personal concienciado. La empresa suministra los medios adecuados de protección y el equipo de obra planifica los trabajos. Reuniones diarias de coordinación. Cumplen los RMGs.',
'2', '0', NULL, '0', '0'),
('19', 'seguridad_y_salud_y_medioambiente', 'Logística', '25', 
'Resistencia a cumplir con el plan logístico. Desorden y suciedad generalizados a lo largo de toda la ejecución.',
'Muy buen plan logístico y actualizaciones regulares. Cumplimiento del mismo a lo largo de toda la ejecución. Mantienen orden y limpieza en la obra.',
'2', '0', NULL, '0', '0'),
('20', 'seguridad_y_salud_y_medioambiente', 'Medioambiente', '25', 
'No tiene una política medioambiental. No han presentado un plan de gestión medioambiental de la obra. No cumple con los requisitos exigidos por BOVIS o el cliente.',
'Conciencia activa para minimizar el impacto medioambiental de sus trabajos. Cumple todos los requerimientos legales y los solicitados por BOVIS y el cliente. Monitoriza el cumplimiemto del plan de gestión medioambiental.',
'2', '0', NULL, '0', '0'),
('21', 'bim', 'Manejo BIM', '100', 
'No ha logrado trabajar en entorno BIM.',
'Excelente nivel de manejo de BIM. Personal propio capacitado. Voluntad de colaborar con el resto de participantes en el proyecto.',
'2', '0', NULL, '0', '0'),
('22', 'certificacion_medioambiental', 'Manejo certificación medioambiental', '100', 
'Graves dificultades en la consecución de la certificación por desinterés del contratista y falta total de colaboración.',
'Elevado grado de compromiso y comprensión de los objetivos del proyecto. Cumple todos los requisitos exigidos por la certificación y entrega toda la documentación en plazo y forma.',
'2', '0', NULL, '0', '0'),
('23', 'proyecto_basico', 'Calidad documentación', '40', 
'Calidad general inaceptable. Entregas incompletas y con muchos errores. No alineada con las prioridades y necesidades del cliente. Necesidad significativa de corregir el trabajo. BOVIS acaba participando más de lo necesario para obtener un nivel mínimamente aceptable.',
'Calidad del proyecto excelente, mejor de lo esperado. Comprensión y cumplimiento de las necesidades del cliente. Memoria, planos y mediciones muy completos. Gestión de la documentación sobresaliente.',
'1', '0', NULL, '0', '0'),
('24', 'proyecto_basico', 'Cumplimiento del presupuesto objetivo', '40', 
'Rechazo a a aceptar restricciones presupuestarias y desarrollo de diseños que sobrepasan significativamente estos límites.',
'Compromiso absoluto con los objetivos de coste del cliente. Colaboración continua con el equipo de BOVIS y actitud proactiva de implantación de técnicas de ingeniería de valor.',
'1', '0', NULL, '0', '0'),
('25', 'proyecto_basico', 'Entregas', '20', 
'Incumplimiento reiterado de plazos de entrega.',
'Actitud proactiva para el cumplimiento de las entregas. Se cumple al 100 % con la planificación.',
'1', '0', NULL, '0', '0'),
('26', 'proyecto_de_ejecucion', 'Calidad documentación', '40', 
'Calidad general inaceptable. Desconocimiento de cómo se construye. Soluciones técnicas muy pobres e indefinidas. Necesidad significativa de corregir el trabajo. BOVIS acaba participando más de lo necesario para obtener un nivel mínimamente aceptable.',
'Gestión de la documentación sobresaliente. Excelentes soluciones técnicas, conocimiento de las necesidades en obra. Memoria, planos y mediciones muy completos.',
'1', '0', NULL, '0', '0'),
('27', 'proyecto_de_ejecucion', 'Cumplimiento del presupuesto objetivo', '40', 
'Rechazo a a aceptar restricciones presupuestarias y desarrollo de diseños que sobrepasan significativamente estos límites.',
'Compromiso absoluto con los objetivos de coste del cliente. Colaboración continua con el equipo de BOVIS y actitud proactiva de implantación de técnicas de ingeniería de valor.',
'1', '0', NULL, '0', '0'),
('28', 'proyecto_de_ejecucion', 'Entregas', '20', 
'Incumplimiento reiterado de plazos de entrega.',
'Actitud proactiva para el cumplimiento de las entregas. Se cumple al 100 % con la planificación.',
'1', '0', NULL, '0', '0'),
('29', 'capacidad_de_la_empresa', 'Cultura y apoyo corporativo', '35', 
'Se ve al gerente de proyecto como adversario. Poca implicación del equipo directivo en el proyecto.',
'Empresa enfocada a trabajar en colaboración con el resto de agentes. Alineada con los objetivos del proyecto. El equipo directivo de la empresa se involucra activamente.',
'1', '0', NULL, '0', '0'),
('30', 'capacidad_de_la_empresa', 'Equipo redactor', '65', 
'Equipo escaso, con poca experiencia, competencia y motivación a nivel general. Descoordinación entre sus miembros. Incumple reiteradamente los procedimientos de gestión de proyecto.',
'Equipo con recursos suficientes, motivado, colaborador y muy competente. Se adapta y cumple los procedimientos marcados por BOVIS y propone mejoras en la gestión del proyecto.',
'1', '0', NULL, '0', '0'),
('31', 'colaboradores', 'Capacidad del equipo de obra', '50', 
'Equipo escaso, con poca experiencia, competencia y motivación a nivel general.',
'Equipo competente y experimentado.',
'1', '0', NULL, '0', '0'),
('32', 'colaboradores', 'Calidad trabajo', '50', 
'Colaboradores con poca experiencia. Calidad de trabajo deficiente.',
'Colaboradores con elevado grado de experiencia y excelente nivel.',
'1', '0', NULL, '0', '0'),
('33', 'capacidad', 'Capacidad del equipo de obra', '50', 
'Equipo escaso, con poca experiencia, competencia y motivación a nivel general.',
'Equipo competente y experimentado.',
'1', '0', NULL, '0', '0'),
('34', 'capacidad', 'Respuestas ante dudas, reclamaciones y propuestas de cambios', '50', 
'Deriva a los contratistas la resolución de cualquier cuestión técnica. Tiempo de respuesta muy dilatado, descoordinación en las respuestas.',
'Respuestas a preguntas y/o solicitudes de cambio rápidas, efectivas y bien documentadas.',
'1', '0', NULL, '0', '0'),
('35', 'actitud', 'Actitud del equipo de obra', '40', 
'No respeta los procedimientos establecidos. Dedicación mínima a la obra. Plantea cambios de forma arbitraria y continua.',
'Respeta escrupulosamente los procedimientos establecidos. Comprometido con la obra, cumple con la dedicación requerida. No plantea cambios de forma arbitraria.',
'1', '0', NULL, '0', '0'),
('36', 'actitud', 'Cumplimiento del presupuesto objetivo', '40', 
'Desprecio hacia las restricciones presupuestarias. Introduce cambios sin considerar los impactos económicos o de plazos.',
'Compromiso absoluto con los objetivos de coste del cliente. Colaboración continua con el equipo de BOVIS y actitud proactiva de implantación de técnicas de ingeniería de valor.',
'1', '0', NULL, '0', '0'),
('37', 'actitud', 'Compromiso con la SyS', '20', 
'Desinterés absoluto.',
'Alineada con la filosofía de BOVIS. Comprometidos y proactivos.',
'1', '0', NULL, '0', '0'),
('38', 'bim', 'Manejo BIM', '100', 
'Conocimientos escasos, reducidos a diseñar en 3D. Incomprensión de la filosofía BIM. Necesidad de subcontratar personal capacitado.',
'Excelente nivel de manejo de BIM. Personal propio capacitado. Voluntad de colaborar con el resto de participantes en el proyecto.',
'1', '0', NULL, '0', '0');

CREATE TABLE IF NOT EXISTS `glpi_plugin_comproveedores_servicetypes` (
	`id` int(11) NOT NULL auto_increment,
	`name` varchar(255) NULL,
              
 	`is_deleted` tinyint(1) NOT NULL default '0',
	`externalid` varchar(255) NULL,
	`is_recursive` tinyint(1) NOT NULL default '0',
	`entities_id` int(11) NOT NULL default '0',
	PRIMARY KEY (`id`),
	KEY `entities_id` (`entities_id`)

) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `glpi_plugin_comproveedores_servicetypes` (`id`, `name`, `is_deleted`, `externalid`, `is_recursive`, `entities_id`) 
VALUES ('1', 'PM', '0', NULL, '0', '0'),
('2', 'CM', '0', NULL, '0', '0'),
('3', 'PMonit', '0', NULL, '0', '0'),
('4', 'D&B', '0', NULL, '0', '0'),
('5', 'PMG', '0', NULL, '0', '0'),
('6', 'Precio cerrado', '0', NULL, '0', '0');

CREATE TABLE IF NOT EXISTS `glpi_plugin_comproveedores_preselections` (
	`id` int(11) NOT NULL auto_increment,
	`suppliers_id` int(11) NOT NULL default '0',
                `projecttasks_id` int(11) NOT NULL default '0',
              
 	`is_deleted` tinyint(1) NOT NULL default '0',
	`externalid` varchar(255) NULL,
	`is_recursive` tinyint(1) NOT NULL default '0',
	`entities_id` int(11) NOT NULL default '0',
	PRIMARY KEY (`id`),
	KEY `entities_id` (`entities_id`)

) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
