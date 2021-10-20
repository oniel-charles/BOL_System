-- MySQL Workbench Synchronization
-- Generated: 2018-12-13 12:18
-- Model: New Model
-- Version: 1.0
-- Project: Name of the project
-- Author: Oniel

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE TABLE IF NOT EXISTS `delivery_order` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `voyage_id` INT(11) NULL DEFAULT NULL,
  `billoflading_id` INT(11) NULL DEFAULT NULL,
  `created_by` INT(11) NULL DEFAULT NULL,
  `cancelled` SMALLINT(6) NULL DEFAULT NULL,
  `order_date` INT(11) NULL DEFAULT NULL,
  `cancel_by` INT(11) NULL DEFAULT NULL,
  `cancel_date` INT(11) NULL DEFAULT NULL,
  `cancel_time` SMALLINT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_order_bol` (`billoflading_id` ASC),
  INDEX `fk_order_created_by` (`created_by` ASC),
  INDEX `fk_order_voyage` (`voyage_id` ASC),
  CONSTRAINT `fk_order_bol0`
    FOREIGN KEY (`billoflading_id`)
    REFERENCES `bill_of_lading` (`id`),
  CONSTRAINT `fk_order_created_by0`
    FOREIGN KEY (`created_by`)
    REFERENCES `user_profile` (`id`),
  CONSTRAINT `fk_order_voyage0`
    FOREIGN KEY (`voyage_id`)
    REFERENCES `voyage` (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 71
DEFAULT CHARACTER SET = latin1;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;