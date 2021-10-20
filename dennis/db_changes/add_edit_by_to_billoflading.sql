-- MySQL Workbench Synchronization
-- Generated: 2021-03-06 23:39
-- Model: New Model
-- Version: 1.0
-- Project: Name of the project
-- Author: Oniel

USE dennisxx;
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

ALTER TABLE `bill_of_lading` 
ADD COLUMN `edited_by` INT(11) NULL DEFAULT NULL AFTER `receipt_processed`,
ADD COLUMN `edit_type` CHAR(25) NULL DEFAULT NULL AFTER `edited_by`,
ADD INDEX `fk_edit_user_idx` (`edited_by` ASC);

ALTER TABLE `bill_of_lading` 
ADD CONSTRAINT `fk_edit_user`
  FOREIGN KEY (`edited_by`)
  REFERENCES `user_profile` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
