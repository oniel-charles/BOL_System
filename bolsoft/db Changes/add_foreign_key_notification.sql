-- MySQL Workbench Synchronization
-- Generated: 2021-06-17 15:06
-- Model: New Model
-- Version: 1.0
-- Project: Name of the project
-- Author: Oniel

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

ALTER TABLE `jacksondb`.`notification_history` 
ADD CONSTRAINT `fk_billoflading_notification`
  FOREIGN KEY (`billoflading_id`)
  REFERENCES `jacksondb`.`bill_of_lading` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
