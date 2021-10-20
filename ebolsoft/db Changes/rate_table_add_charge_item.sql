-- MySQL Workbench Synchronization
-- Generated: 2021-06-19 04:13
-- Model: New Model
-- Version: 1.0
-- Project: Name of the project
-- Author: Oniel

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

ALTER TABLE `jacksondb`.`rate_table` 
ADD COLUMN `charge_item_id` INT(11) NULL DEFAULT NULL AFTER `rate`,
ADD INDEX `fk_rate_charge_item_idx` (`charge_item_id` ASC);

ALTER TABLE `jacksondb`.`rate_table` 
ADD CONSTRAINT `fk_rate_charge_item`
  FOREIGN KEY (`charge_item_id`)
  REFERENCES `jacksondb`.`charge_item` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
