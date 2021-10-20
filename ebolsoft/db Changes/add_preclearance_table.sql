-- MySQL Workbench Synchronization
-- Generated: 2021-06-22 15:18
-- Model: New Model
-- Version: 1.0
-- Project: Name of the project
-- Author: Oniel

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE TABLE IF NOT EXISTS `jacksondb`.`preclearance` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `preclearance_date` INT(11) NULL DEFAULT NULL,
  `preclearance_time` INT(11) NULL DEFAULT NULL,
  `client_id` INT(11) NULL DEFAULT NULL,
  `payee` CHAR(40) NULL DEFAULT NULL,
  `preclearance_total` DECIMAL(12,2) NULL DEFAULT NULL,
  `currency_id` INT(11) NULL DEFAULT NULL,
  `local_total` DECIMAL(12,2) NULL DEFAULT NULL,
  `exchange_rate` DECIMAL(8,2) NULL DEFAULT NULL,
  `printed` SMALLINT(6) NULL DEFAULT NULL,
  `created_by` INT(11) NULL DEFAULT NULL,
  `billoflading_id` INT(11) NULL DEFAULT NULL,
  `cancel_by` INT(11) NULL DEFAULT NULL,
  `cancel_date` INT(11) NULL DEFAULT NULL,
  `cancel_time` SMALLINT(6) NULL DEFAULT NULL,
  `cancelled` SMALLINT(6) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `Unique_preclearance_by_bol` (`preclearance_date` ASC, `billoflading_id` ASC, `preclearance_time` ASC, `preclearance_total` ASC),
  INDEX `fk_preclearance_currency` (`currency_id` ASC),
  INDEX `fk_preclearancet_bol` (`billoflading_id` ASC),
  INDEX `fk_preclearance_client` (`client_id` ASC),
  INDEX `fk_preclearance_created_by` (`created_by` ASC),
  INDEX `fk_preclearance_deleted_by` (`cancel_by` ASC),
  CONSTRAINT `fk_preclearance_client0`
    FOREIGN KEY (`client_id`)
    REFERENCES `jacksondb`.`client` (`id`)
    ON DELETE SET NULL
    ON UPDATE SET NULL,
  CONSTRAINT `fk_preclearance_created_by0`
    FOREIGN KEY (`created_by`)
    REFERENCES `jacksondb`.`user_profile` (`id`)
    ON DELETE SET NULL
    ON UPDATE SET NULL,
  CONSTRAINT `fk_preclearance_currency0`
    FOREIGN KEY (`currency_id`)
    REFERENCES `jacksondb`.`currency` (`id`),
  CONSTRAINT `fk_preclearance_deleted_by0`
    FOREIGN KEY (`cancel_by`)
    REFERENCES `jacksondb`.`user_profile` (`id`)
    ON DELETE SET NULL
    ON UPDATE SET NULL)
ENGINE = InnoDB
AUTO_INCREMENT = 11
DEFAULT CHARACTER SET = latin1;

CREATE TABLE IF NOT EXISTS `jacksondb`.`preclearance_detail` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `preclearance_id` INT(11) NULL DEFAULT NULL,
  `bol_id` INT(11) NULL DEFAULT NULL,
  `charge_item_id` INT(11) NULL DEFAULT NULL,
  `amount` DECIMAL(10,2) NULL DEFAULT NULL,
  `currency_amount` DECIMAL(10,2) NOT NULL,
  `discount` DECIMAL(10,2) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_preclearance_detail_charge_item` (`charge_item_id` ASC),
  INDEX `fk_preclearance_idx` (`preclearance_id` ASC),
  CONSTRAINT `fk_preclearance0`
    FOREIGN KEY (`preclearance_id`)
    REFERENCES `jacksondb`.`preclearance` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_preclearance_detail_charge_item0`
    FOREIGN KEY (`charge_item_id`)
    REFERENCES `jacksondb`.`charge_item` (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 561855
DEFAULT CHARACTER SET = latin1;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
