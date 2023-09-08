-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema wifi_comm_dev
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema wifi_comm_dev
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `wifi_comm_dev` DEFAULT CHARACTER SET latin1 ;
USE `wifi_comm_dev` ;

-- -----------------------------------------------------
-- Table `wifi_comm_dev`.`device_groups`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `wifi_comm_dev`.`device_groups` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL DEFAULT '0',
  `name` VARCHAR(64) NULL DEFAULT NULL,
  `group_hash` VARCHAR(10) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `wifi_comm_dev`.`modems`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `wifi_comm_dev`.`modems` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `serial_number` VARCHAR(20) NOT NULL,
  `firmware_version` INT(2) NULL DEFAULT NULL,
  `rssi` DECIMAL(4,1) NULL DEFAULT NULL,
  `device_group_id` INT(11) NOT NULL,
  `net_name` VARCHAR(45) NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `serial_UNIQUE` (`serial_number` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `wifi_comm_dev`.`devices`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `wifi_comm_dev`.`devices` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `serial_number` VARCHAR(12) NOT NULL COMMENT '---MOD\\\\ndevice serial number,\\\\n32 bit unsigned integer\\\\n',
  `challenge_response` BINARY(16) NOT NULL DEFAULT '0000000000000000' COMMENT 'challenge reply, when modem connects we should check it against what it sends us',
  `device_group_id` INT(11) NOT NULL DEFAULT '0',
  `mode_id` TINYINT(2) NOT NULL DEFAULT '0' COMMENT '0-not configured, 1-slave, 2-master, 3- master with wifi connection(master+slave mode)',
  `type_id` INT(2) NOT NULL DEFAULT '0' COMMENT '0-not configured, 1-slave, 2-master, 3- master with wifi connection(master+slave mode)',
  `aes_key` BINARY(16) NOT NULL DEFAULT '0000000000000000',
  `name` VARCHAR(64) NULL DEFAULT NULL COMMENT 'device name',
  `last_seen` TIMESTAMP NULL DEFAULT NULL,
  `modem_id` INT(11) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_devices_modems_idx` (`modem_id` ASC),
  CONSTRAINT `fk_devices_modems`
    FOREIGN KEY (`modem_id`)
    REFERENCES `wifi_comm_dev`.`modems` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COMMENT = 'main devices table,\\\\nmodule server saves new received devices here.';


-- -----------------------------------------------------
-- Table `wifi_comm_dev`.`device_problems`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `wifi_comm_dev`.`device_problems` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `device_id` INT(11) UNSIGNED NOT NULL,
  `title` VARCHAR(80) NOT NULL,
  `description` VARCHAR(300) NULL DEFAULT NULL,
  `date` DATETIME NOT NULL,
  `displayed` TINYINT(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  INDEX `fk_device_problems_devices1_idx` (`device_id` ASC),
  CONSTRAINT `fk_device_problems_devices1`
    FOREIGN KEY (`device_id`)
    REFERENCES `wifi_comm_dev`.`devices` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `wifi_comm_dev`.`device_settings`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `wifi_comm_dev`.`device_settings` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `device_id` INT(11) UNSIGNED NOT NULL,
  `option_id` INT(11) NOT NULL,
  `value` VARCHAR(30) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_device_settings_devices1_idx` (`device_id` ASC),
  CONSTRAINT `fk_device_settings_devices1`
    FOREIGN KEY (`device_id`)
    REFERENCES `wifi_comm_dev`.`devices` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `wifi_comm_dev`.`device_settings_history`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `wifi_comm_dev`.`device_settings_history` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `device_setting_id` INT(11) NOT NULL,
  `value` INT(11) NULL DEFAULT NULL,
  `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `deviceSettingId` (`device_setting_id` ASC),
  CONSTRAINT `fk_device_settings_history_device_settings1`
    FOREIGN KEY (`device_setting_id`)
    REFERENCES `wifi_comm_dev`.`device_settings` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `wifi_comm_dev`.`options_groups`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `wifi_comm_dev`.`options_groups` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `order` INT(11) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `wifi_comm_dev`.`options`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `wifi_comm_dev`.`options` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(90) NOT NULL,
  `dev_rep` VARCHAR(10) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL,
  `field_length` VARCHAR(45) NOT NULL DEFAULT '2',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `group_id` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  `order` INT(11) NULL DEFAULT '0',
  `type_id` INT(11) NOT NULL DEFAULT '1',
  `level` INT(11) NULL DEFAULT '128',
  PRIMARY KEY (`id`),
  INDEX `fk_options_options_groups1_idx` (`group_id` ASC),
  CONSTRAINT `fk_options_options_groups1`
    FOREIGN KEY (`group_id`)
    REFERENCES `wifi_comm_dev`.`options_groups` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `wifi_comm_dev`.`device_settings_pending`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `wifi_comm_dev`.`device_settings_pending` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `device_id` INT(11) UNSIGNED NOT NULL,
  `option_id` INT(11) NOT NULL,
  `value` VARCHAR(30) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `sent_to_mqtt` INT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  INDEX `fk_device_settings_pending_devices1_idx` (`device_id` ASC),
  INDEX `fk_device_settings_pending_options1_idx` (`option_id` ASC),
  CONSTRAINT `fk_device_settings_pending_devices1`
    FOREIGN KEY (`device_id`)
    REFERENCES `wifi_comm_dev`.`devices` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_device_settings_pending_options1`
    FOREIGN KEY (`option_id`)
    REFERENCES `wifi_comm_dev`.`options` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `wifi_comm_dev`.`teams`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `wifi_comm_dev`.`teams` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `user_id` INT(11) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `teams_name_index` (`name` ASC),
  INDEX `teams_user_id_index` (`user_id` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `wifi_comm_dev`.`groups`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `wifi_comm_dev`.`groups` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `team_id` INT(11) NOT NULL,
  `name` VARCHAR(255) CHARACTER SET 'utf8' NOT NULL,
  PRIMARY KEY (`id`, `team_id`),
  INDEX `fk_groups_teams1_idx` (`team_id` ASC),
  CONSTRAINT `fk_groups_teams1`
    FOREIGN KEY (`team_id`)
    REFERENCES `wifi_comm_dev`.`teams` (`user_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `wifi_comm_dev`.`groups_devices`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `wifi_comm_dev`.`groups_devices` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_id` INT(11) UNSIGNED NOT NULL,
  `device_id` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_groups_devices_groups1_idx` (`group_id` ASC),
  INDEX `fk_groups_devices_devices1_idx` (`device_id` ASC),
  CONSTRAINT `fk_groups_devices_devices1`
    FOREIGN KEY (`device_id`)
    REFERENCES `wifi_comm_dev`.`devices` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_groups_devices_groups1`
    FOREIGN KEY (`group_id`)
    REFERENCES `wifi_comm_dev`.`groups` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `wifi_comm_dev`.`groups_roles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `wifi_comm_dev`.`groups_roles` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `role` ENUM('STAFF', 'TENANTRY', 'TECHNICIAN') NOT NULL,
  `sort` TINYINT(4) NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `wifi_comm_dev`.`groups_users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `wifi_comm_dev`.`groups_users` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_id` INT(11) UNSIGNED NOT NULL,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `role` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_groups_users_groups_roles1_idx` (`role` ASC),
  INDEX `fk_groups_users_groups1_idx` (`group_id` ASC),
  CONSTRAINT `fk_groups_users_groups1`
    FOREIGN KEY (`group_id`)
    REFERENCES `wifi_comm_dev`.`groups` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_groups_users_groups_roles1`
    FOREIGN KEY (`role`)
    REFERENCES `wifi_comm_dev`.`groups_roles` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `wifi_comm_dev`.`migrations`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `wifi_comm_dev`.`migrations` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` VARCHAR(255) NOT NULL,
  `batch` INT(11) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `wifi_comm_dev`.`options_groups_roles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `wifi_comm_dev`.`options_groups_roles` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `option_id` INT(11) NOT NULL,
  `role_id` INT(11) UNSIGNED NOT NULL,
  `can_see` TINYINT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  INDEX `fk_options_groups_roles_options1_idx` (`option_id` ASC),
  INDEX `fk_options_groups_roles_groups_roles1_idx` (`role_id` ASC),
  CONSTRAINT `fk_options_groups_roles_groups_roles1`
    FOREIGN KEY (`role_id`)
    REFERENCES `wifi_comm_dev`.`groups_roles` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_options_groups_roles_options1`
    FOREIGN KEY (`option_id`)
    REFERENCES `wifi_comm_dev`.`options` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `wifi_comm_dev`.`options_types`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `wifi_comm_dev`.`options_types` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL DEFAULT 'string',
  `type` ENUM('string', 'integer', 'hide|show', 'on|off', 'date', 'pin', 'lang', 'readonly', 'select', 'counter') CHARACTER SET 'utf8' NOT NULL,
  `min` INT(11) NOT NULL,
  `max` INT(11) NOT NULL,
  `values` VARCHAR(255) CHARACTER SET 'utf8' NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `wifi_comm_dev`.`password_resets`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `wifi_comm_dev`.`password_resets` (
  `email` VARCHAR(255) NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  INDEX `password_resets_email_index` (`email` ASC),
  INDEX `password_resets_token_index` (`token` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `wifi_comm_dev`.`users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `wifi_comm_dev`.`users` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `remember_token` VARCHAR(100) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `origin` VARCHAR(255) NOT NULL DEFAULT 'register',
  `username` VARCHAR(30) NOT NULL DEFAULT '',
  `password_hash` CHAR(60) CHARACTER SET 'utf8' NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `users_email_unique` (`email` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `wifi_comm_dev`.`sessions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `wifi_comm_dev`.`sessions` (
  `id` VARCHAR(255) NOT NULL,
  `user_id` INT(11) UNSIGNED NULL DEFAULT NULL,
  `ip_address` VARCHAR(45) NULL DEFAULT NULL,
  `user_agent` TEXT NULL DEFAULT NULL,
  `payload` TEXT NOT NULL,
  `last_activity` INT(11) NOT NULL,
  UNIQUE INDEX `sessions_id_unique` (`id` ASC),
  INDEX `fk_sessions_users1_idx` (`user_id` ASC),
  CONSTRAINT `fk_sessions_users1`
    FOREIGN KEY (`user_id`)
    REFERENCES `wifi_comm_dev`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `wifi_comm_dev`.`teams_users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `wifi_comm_dev`.`teams_users` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `team_id` INT(11) UNSIGNED NOT NULL,
  `user_id` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`, `user_id`, `team_id`),
  INDEX `teams_users_team_id_index` (`team_id` ASC),
  INDEX `teams_users_user_id_index` (`user_id` ASC),
  CONSTRAINT `fk_teams_users_teams1`
    FOREIGN KEY (`team_id`)
    REFERENCES `wifi_comm_dev`.`teams` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_teams_users_users1`
    FOREIGN KEY (`user_id`)
    REFERENCES `wifi_comm_dev`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `wifi_comm_dev`.`users_roles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `wifi_comm_dev`.`users_roles` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `role` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_users_roles_users1_idx` (`user_id` ASC),
  CONSTRAINT `fk_users_roles_users1`
    FOREIGN KEY (`user_id`)
    REFERENCES `wifi_comm_dev`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
