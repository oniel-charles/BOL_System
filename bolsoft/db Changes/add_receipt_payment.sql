-- MySQL Workbench Synchronization
-- Generated: 2021-07-06 01:47
-- Model: New Model
-- Version: 1.0
-- Project: Name of the project
-- Author: Oniel

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

ALTER TABLE `jacksondb`.`bill_of_lading` 
ADD COLUMN `status` CHAR(10) NULL DEFAULT NULL AFTER `customer_type`;

update  `bill_of_lading` set status='release';

CREATE TABLE IF NOT EXISTS `jacksondb`.`receipt_payment` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `receipt_id` INT(11) NULL DEFAULT NULL,
  `payment_type` CHAR(15) NULL DEFAULT NULL,
  `amount` DECIMAL(12,2) NULL DEFAULT NULL,
  `currency_amount` DECIMAL(12,2) NULL DEFAULT NULL,
  `currency_id` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_receipt_payment_receipt_idx` (`receipt_id` ASC),
  UNIQUE INDEX `by_receipt` (`receipt_id` ASC, `payment_type` ASC),
  CONSTRAINT `fk_receipt_payment_receipt`
    FOREIGN KEY (`receipt_id`)
    REFERENCES `jacksondb`.`receipt` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
