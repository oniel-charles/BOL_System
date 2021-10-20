-- MySQL Workbench Synchronization
-- Generated: 2018-09-12 15:28
-- Model: New Model
-- Version: 1.0
-- Project: Name of the project
-- Author: Oniel

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE TABLE IF NOT EXISTS `dennismby`.`client_discount` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `client_id` INT(11) NOT NULL,
  `charge_item_id` INT(11) NOT NULL,
  `basis` VARCHAR(15) NULL DEFAULT NULL,
  `amount` DECIMAL(12,2) NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;

CREATE TABLE IF NOT EXISTS `dennismby`.`billoflading_discount` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `client_id` INT(11) NOT NULL,
  `charge_item_id` INT(11) NOT NULL,
  `basis` VARCHAR(15) NULL DEFAULT NULL,
  `amount` DECIMAL(12,2) NULL DEFAULT NULL,
  `billoflading_id` INT(11) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;

ALTER TABLE `dennismby`.`billoflading_discount` 
ADD INDEX `fk_client_idx` (`client_id` ASC),
ADD INDEX `fk_charge_item_idx` (`charge_item_id` ASC),
ADD UNIQUE INDEX `by_client_item_bol` (`client_id` ASC, `charge_item_id` ASC, `billoflading_id` ASC),
ADD INDEX `fk_billoflading_idx` (`billoflading_id` ASC);

ALTER TABLE `dennismby`.`client_discount` 
ADD INDEX `fk_charge_item_idx` (`charge_item_id` ASC),
ADD UNIQUE INDEX `by_client_item` (`client_id` ASC, `charge_item_id` ASC);

ALTER TABLE `dennismby`.`billoflading_discount` 
ADD CONSTRAINT `fk_client`
  FOREIGN KEY (`client_id`)
  REFERENCES `dennismby`.`client` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_charge_item`
  FOREIGN KEY (`charge_item_id`)
  REFERENCES `dennismby`.`charge_item` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_billoflading`
  FOREIGN KEY (`billoflading_id`)
  REFERENCES `dennismby`.`bill_of_lading` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `dennismby`.`client_discount` 
ADD CONSTRAINT `fk_charge_item`
  FOREIGN KEY (`charge_item_id`)
  REFERENCES `dennismby`.`charge_item` (`id`)
  ON DELETE RESTRICT
  ON UPDATE NO ACTION;

  ALTER TABLE `dennismby`.`billoflading_discount` 
DROP FOREIGN KEY `fk_client`;

ALTER TABLE `dennismby`.`billoflading_discount` 
DROP COLUMN `client_id`,
DROP INDEX `fk_client_idx` ,
DROP INDEX `by_client_item_bol` ;

ALTER TABLE `dennismby`.`billoflading_discount` 
ADD UNIQUE INDEX `bol_charge_item` (`charge_item_id` ASC, `billoflading_id` ASC);


INSERT INTO `menu_item` (`id`, `menu_group_id`, `title`, `url`, `menu_order`, `status`, `level`, `icon`, `description`) VALUES (NULL, '2', 'Customers', 'maintain_client.html', '8', NULL, '2', NULL, NULL);
INSERT INTO `menu_item` (`id`, `menu_group_id`, `title`, `url`, `menu_order`, `status`, `level`, `icon`, `description`) VALUES (NULL, '6', 'Bill of Laing Discount', 'billoflading_discount.html', '7', NULL, '2', NULL, NULL);

ALTER TABLE `voyage` ADD `mby_arrival_date` INT NULL AFTER `stripped_date`, ADD `mby_vessel_id` INT NULL AFTER `mby_arrival_date`;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

INSERT INTO `system_values` (`id`, `description`, `data_type`, `data_value`, `code`) VALUES (NULL, 'Company Branch', 'char', 'kgn', 'branchcode');
