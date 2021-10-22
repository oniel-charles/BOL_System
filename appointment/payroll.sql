-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema payroll
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema payroll
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `payroll` DEFAULT CHARACTER SET latin1 ;
USE `payroll` ;

-- -----------------------------------------------------
-- Table `payroll`.`allowance`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `payroll`.`allowance` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `code` CHAR(20) NULL DEFAULT NULL,
  `description` CHAR(30) NULL DEFAULT NULL,
  `amount` DECIMAL(12,2) NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `payroll`.`currency`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `payroll`.`currency` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `currency_code` CHAR(10) NULL DEFAULT NULL,
  `currency_name` CHAR(50) NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `payroll`.`currency_rate`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `payroll`.`currency_rate` (
  `currency_id` INT(11) NOT NULL,
  `effective_date` INT(11) NULL DEFAULT NULL,
  `exchange_rate` DECIMAL(7,2) NULL DEFAULT NULL,
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `Index_date` (`currency_id` ASC, `effective_date` ASC),
  CONSTRAINT `fk_currency_rate_currency`
    FOREIGN KEY (`currency_id`)
    REFERENCES `payroll`.`currency` (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `payroll`.`deduction`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `payroll`.`deduction` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `code` CHAR(20) NOT NULL,
  `description` CHAR(30) NOT NULL,
  `basis` CHAR(20) NOT NULL,
  `amount` DECIMAL(12,2) NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `payroll`.`menu_group`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `payroll`.`menu_group` (
  `id` BIGINT(20) NOT NULL,
  `title` CHAR(100) NULL DEFAULT NULL,
  `menu_order` INT(11) NULL DEFAULT NULL,
  `status` SMALLINT(6) NULL DEFAULT NULL,
  `level` SMALLINT(6) NULL DEFAULT NULL,
  `icon` CHAR(100) NULL DEFAULT NULL,
  `description` CHAR(100) NULL DEFAULT NULL)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `payroll`.`menu_item`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `payroll`.`menu_item` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `menu_group_id` BIGINT(20) NULL DEFAULT NULL,
  `title` VARCHAR(100) NULL DEFAULT NULL,
  `url` VARCHAR(100) NULL DEFAULT NULL,
  `menu_order` INT(11) NULL DEFAULT NULL,
  `status` INT(11) NULL DEFAULT NULL,
  `level` SMALLINT(6) NULL DEFAULT NULL,
  `icon` CHAR(100) NULL DEFAULT NULL,
  `description` CHAR(150) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_menu_item_` (`menu_group_id` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 47
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `payroll`.`user_profile`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `payroll`.`user_profile` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_name` CHAR(60) NULL DEFAULT NULL,
  `password` CHAR(255) NULL DEFAULT NULL,
  `full_name` CHAR(70) NULL DEFAULT NULL,
  `status` CHAR(10) NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 38
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `payroll`.`user_option`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `payroll`.`user_option` (
  `menu_item_id` INT(11) NOT NULL,
  `user_id` INT(11) NOT NULL,
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `fk_user_option_user_profile` USING BTREE (`user_id` ASC, `menu_item_id` ASC),
  INDEX `menu_item_id` USING BTREE (`menu_item_id` ASC, `user_id` ASC),
  CONSTRAINT `fk_user_option_menu_item`
    FOREIGN KEY (`menu_item_id`)
    REFERENCES `payroll`.`menu_item` (`id`),
  CONSTRAINT `fk_user_option_user_profile`
    FOREIGN KEY (`user_id`)
    REFERENCES `payroll`.`user_profile` (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 1053
DEFAULT CHARACTER SET = latin1;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
