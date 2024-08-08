CREATE TABLE `bovis`.`glpi_proveedortypes` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NULL DEFAULT NULL,
  `comment` TEXT NULL DEFAULT NULL,
  `date_mod` DATETIME NULL DEFAULT NULL,
  `date_creation` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;

INSERT INTO `bovis`.`glpi_proveedortypes` (`nombre`) VALUES ('Contratista');
INSERT INTO `bovis`.`glpi_proveedortypes` (`nombre`) VALUES ('Consultor');

UPDATE `bovis`.`glpi_suppliers` SET `date_mod` = '1970-01-01' WHERE (DATE_FORMAT(date_mod, '%Y-%m-%d') = '0000-00-00');

ALTER TABLE `bovis`.`glpi_suppliers` 
ADD COLUMN `proveedortypes_id` INT(11) NOT NULL DEFAULT '0' AFTER `suppliertypes_id`;


ALTER TABLE `bovis`.`glpi_proveedortypes` 
ADD INDEX `name` (`name` ASC),
ADD INDEX `date_mod` (`date_mod` ASC),
ADD INDEX `date_creation` (`date_creation` ASC);




