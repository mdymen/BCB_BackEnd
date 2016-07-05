CREATE TABLE `penca`.`championship` (
  `ch_id` INT NOT NULL AUTO_INCREMENT,
  `ch_nome` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`ch_id`));

  CREATE TABLE `penca`.`new_table` (
  `tm_id` INT NOT NULL AUTO_INCREMENT,
  `tm_name` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`tm_id`));
  
  ALTER TABLE `penca`.`new_table` 
RENAME TO  `penca`.`team` ;

ALTER TABLE `penca`.`championship` 
ADD COLUMN `ch_idfixture` INT NULL AFTER `ch_nome`;

CREATE TABLE `penca`.`match` (
  `mt_id` INT NOT NULL AUTO_INCREMENT,
  `mt_idteam1` INT NOT NULL,
  `mt_idteam2` INT NOT NULL,
  `mt_date` DATETIME NULL,
  `mt_goal1` INT NULL,
  `mt_goal2` INT NULL,
  PRIMARY KEY (`mt_id`));
  
  CREATE TABLE `penca`.`fixture` (
  `fx_id` INT NOT NULL AUTO_INCREMENT,
  `fx_match` INT NOT NULL,
  PRIMARY KEY (`fx_id`));

  CREATE TABLE `penca`.`user` (
  `us_id` INT NOT NULL AUTO_INCREMENT,
  `us_username` VARCHAR(155) NOT NULL,
  PRIMARY KEY (`us_id`));

  
  CREATE TABLE `penca`.`penca` (
  `pn_id` INT NOT NULL AUTO_INCREMENT,
  `pn_name` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`pn_id`));

  CREATE TABLE `penca`.`user_penca` (
  `up_id` INT NOT NULL AUTO_INCREMENT,
  `up_idpenca` INT NOT NULL,
  `up_iduser` INT NOT NULL,
  PRIMARY KEY (`up_id`));

  ALTER TABLE `penca`.`penca` 
ADD COLUMN `pn_value` DECIMAL(3) NOT NULL AFTER `pn_name`;

ALTER TABLE `penca`.`penca` 
ADD COLUMN `pn_iduser` INT(11) NULL AFTER `pn_value`;
