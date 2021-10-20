-- MySQL Workbench Synchronization
-- Generated: 2021-10-10 21:13
-- Model: New Model
-- Version: 1.0
-- Project: Name of the project
-- Author: Oniel

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

ALTER TABLE `bill_of_lading` 
ADD COLUMN `customer_type` CHAR(15) NULL DEFAULT NULL AFTER `voyage_id`;

CREATE TABLE IF NOT EXISTS `notification_history` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `notification_date` INT(11) NULL DEFAULT NULL,
  `notification_detail` CHAR(100) NULL DEFAULT NULL,
  `billoflading_id` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_notification_billoflading_idx` (`billoflading_id` ASC),
  CONSTRAINT `fk_notification_billoflading`
    FOREIGN KEY (`billoflading_id`)
    REFERENCES `bill_of_lading` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
