ALTER TABLE `wifi_comm_dev`.`device_problems`
CHANGE COLUMN `device_id` `device_id` INT(11) UNSIGNED NOT NULL ;

ALTER TABLE `wifi_comm_dev`.`device_settings`
CHANGE COLUMN `device_id` `device_id` INT(11) UNSIGNED NOT NULL ;

ALTER TABLE `wifi_comm_dev`.`device_settings_pending`
CHANGE COLUMN `device_id` `device_id` INT(11) UNSIGNED NOT NULL ;


ALTER TABLE `wifi_comm_dev`.`devices`
CHANGE COLUMN `id` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ;


ALTER TABLE `wifi_comm_dev`.`groups_devices`
CHANGE COLUMN `group_id` `group_id` INT(11) UNSIGNED NOT NULL ,
CHANGE COLUMN `device_id` `device_id` INT(11) UNSIGNED NOT NULL ;

ALTER TABLE `wifi_comm_dev`.`groups_users`
CHANGE COLUMN `group_id` `group_id` INT(11) UNSIGNED NOT NULL ,
CHANGE COLUMN `user_id` `user_id` INT(11) UNSIGNED NOT NULL ,
CHANGE COLUMN `role` `role` INT(11) UNSIGNED NOT NULL ;

ALTER TABLE `wifi_comm_dev`.`options`
CHANGE COLUMN `group_id` `group_id` INT(11) UNSIGNED NOT NULL DEFAULT '1' ;


ALTER TABLE `wifi_comm_dev`.`options_groups_roles`
CHANGE COLUMN `role_id` `role_id` INT(11) UNSIGNED NOT NULL ;

ALTER TABLE `wifi_comm_dev`.`teams_users`
CHANGE COLUMN `team_id` `team_id` INT(11) UNSIGNED NOT NULL ,
CHANGE COLUMN `user_id` `user_id` INT(11) UNSIGNED NOT NULL ;


ALTER TABLE `wifi_comm_dev`.`users_roles`
CHANGE COLUMN `user_id` `user_id` INT(11) UNSIGNED NOT NULL ;

ALTER TABLE `wifi_comm_dev`.`sessions` 
CHANGE COLUMN `user_id` `user_id` INT(11) UNSIGNED NULL DEFAULT NULL ;


TRUNCATE `wifi_comm_dev`.`migrations`;

INSERT INTO `wifi_comm_dev`.`migrations` (`migration`, `batch`) VALUES ('2020_05_01_220223_create_device_groups_table', '0');
INSERT INTO `wifi_comm_dev`.`migrations` (`migration`, `batch`) VALUES ('2020_05_01_220223_create_device_problems_table', '0');
INSERT INTO `wifi_comm_dev`.`migrations` (`migration`, `batch`) VALUES ('2020_05_01_220223_create_device_settings_history_table', '0');
INSERT INTO `wifi_comm_dev`.`migrations` (`migration`, `batch`) VALUES ('2020_05_01_220223_create_device_settings_pending_table', '0');
INSERT INTO `wifi_comm_dev`.`migrations` (`migration`, `batch`) VALUES ('2020_05_01_220223_create_device_settings_table', '0');
INSERT INTO `wifi_comm_dev`.`migrations` (`migration`, `batch`) VALUES ('2020_05_01_220223_create_devices_table', '0');
INSERT INTO `wifi_comm_dev`.`migrations` (`migration`, `batch`) VALUES ('2020_05_01_220223_create_groups_devices_table', '0');
INSERT INTO `wifi_comm_dev`.`migrations` (`migration`, `batch`) VALUES ('2020_05_01_220223_create_groups_roles_table', '0');
INSERT INTO `wifi_comm_dev`.`migrations` (`migration`, `batch`) VALUES ('2020_05_01_220223_create_groups_table', '0');
INSERT INTO `wifi_comm_dev`.`migrations` (`migration`, `batch`) VALUES ('2020_05_01_220223_create_groups_users_table', '0');
INSERT INTO `wifi_comm_dev`.`migrations` (`migration`, `batch`) VALUES ('2020_05_01_220223_create_modems_table', '0');

INSERT INTO `wifi_comm_dev`.`migrations` (`migration`, `batch`) VALUES ('2020_05_01_220223_create_options_groups_roles_table', '0');
INSERT INTO `wifi_comm_dev`.`migrations` (`migration`, `batch`) VALUES ('2020_05_01_220223_create_options_groups_table', '0');
INSERT INTO `wifi_comm_dev`.`migrations` (`migration`, `batch`) VALUES ('2020_05_01_220223_create_options_table', '0');
INSERT INTO `wifi_comm_dev`.`migrations` (`migration`, `batch`) VALUES ('2020_05_01_220223_create_options_types_table', '0');
INSERT INTO `wifi_comm_dev`.`migrations` (`migration`, `batch`) VALUES ('2020_05_01_220223_create_password_resets_table', '0');
INSERT INTO `wifi_comm_dev`.`migrations` (`migration`, `batch`) VALUES ('2020_05_01_220223_create_sessions_table', '0');
INSERT INTO `wifi_comm_dev`.`migrations` (`migration`, `batch`) VALUES ('2020_05_01_220223_create_teams_table', '0');

INSERT INTO `wifi_comm_dev`.`migrations` (`migration`, `batch`) VALUES ('2020_05_01_220223_create_teams_users_table', '0');
INSERT INTO `wifi_comm_dev`.`migrations` (`migration`, `batch`) VALUES ('2020_05_01_220223_create_users_roles_table', '0');
INSERT INTO `wifi_comm_dev`.`migrations` (`migration`, `batch`) VALUES ('2020_05_01_220223_create_users_table', '0');

