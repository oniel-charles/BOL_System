-- MySQL Workbench Synchronization
-- Generated: 2021-10-01 23:18
-- Model: New Model
-- Version: 1.0
-- Project: Name of the project
-- Author: Oniel

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE TABLE IF NOT EXISTS `appointment` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `billoflading_id` INT(11) NULL DEFAULT NULL,
  `trn` CHAR(15) NULL DEFAULT NULL,
  `uba_code` CHAR(30) NULL DEFAULT NULL,
  `passport_number` CHAR(20) NULL DEFAULT NULL,
  `status` CHAR(20) NULL DEFAULT NULL,
  `appointment_date` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `fk_appointment_billoflading_idx` (`billoflading_id` ASC),
  CONSTRAINT `fk_appointment_billoflading`
    FOREIGN KEY (`billoflading_id`)
    REFERENCES `bill_of_lading` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
