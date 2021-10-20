-- MySQL Workbench Synchronization
-- Generated: 2021-10-10 23:35
-- Model: New Model
-- Version: 1.0
-- Project: Name of the project
-- Author: Oniel

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

ALTER TABLE `bill_of_lading` 
ADD COLUMN `date_processed` INT(11) NULL DEFAULT NULL AFTER `customer_type`;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

INSERT INTO `menu_item` (`id`, `menu_group_id`, `title`, `url`, `menu_order`, `status`, `level`, `icon`, `description`) VALUES (NULL, '9', 'Outstanding Door-To-Door', 'rpt_door_to_door.html', '9', NULL, '2', NULL, NULL);
