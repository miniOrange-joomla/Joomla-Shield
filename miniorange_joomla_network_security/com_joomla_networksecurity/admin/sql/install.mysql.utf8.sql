CREATE TABLE IF NOT EXISTS `#__miniorange_jnsp_loginsecurity_setup` (
`id` int(11) UNSIGNED NOT NULL,
`enable_custom_admin_login` tinyint(1) DEFAULT 0,
`access_lgn_urlky` VARCHAR(255),
`after_adm_failure_response` VARCHAR(255),
`custom_failure_destination` VARCHAR(255),
`custom_message_after_fail` VARCHAR(255),
`mo_manual_ip` VARCHAR(255),
`mo_ip_lookup_values` VARCHAR(4096),
`enforce_strong_password_login` tinyint(1) DEFAULT 0,
PRIMARY KEY(`id`)
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__miniorange_jnsp_registersecurity_setup` (
`id` int(11) UNSIGNED NOT NULL,
`block_fake_emails` int NOT NULL,
`mo_email_domains` VARCHAR(1048) NOT NULL,
`enforce_strong_password_register` tinyint(1) DEFAULT 0,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__miniorange_networksecurity_customer` (
`id` int(11) UNSIGNED NOT NULL ,
`email` VARCHAR(255)  NOT NULL ,
`password` VARCHAR(255)  NOT NULL ,
`admin_phone` VARCHAR(255)  NOT NULL ,
`customer_key` VARCHAR(255)  NOT NULL ,
`customer_token` VARCHAR(255) NOT NULL,
`api_key` VARCHAR(255)  NOT NULL,
`login_status` int NOT NULL,
`registration_status` VARCHAR(255) NOT NULL,
`new_registration` int NOT NULL,
`transaction_id` VARCHAR(255) NOT NULL,
`email_count` int(11),
`sms_count` int(11),
`uninstall_feedback` int(2) NOT NULL,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__miniorange_login_transactions` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ip_address` mediumtext NOT NULL ,
`username` mediumtext NOT NULL ,
`type` mediumtext NOT NULL ,
`url` mediumtext NOT NULL ,
`status` mediumtext NOT NULL ,
`created_timestamp` int,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__miniorange_login_transactions_reports` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ip_address` mediumtext NOT NULL ,
`username` mediumtext NOT NULL ,
`type` mediumtext NOT NULL ,
`url` mediumtext NOT NULL ,
`status` mediumtext NOT NULL ,
`isadmin_user` mediumtext NOT NULL,
`country_name` mediumtext NOT NULL,
`browser_name` mediumtext NOT NULL,
`operating_system` mediumtext NOT NULL,
`created_timestamp` int,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__miniorange_jnsp_advance_blocking` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`mo_enable_browser_blocking` tinyint(1) DEFAULT 0,
`mo_medge_blocking` tinyint(1) DEFAULT 0,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

INSERT IGNORE INTO `#__miniorange_jnsp_loginsecurity_setup`(`id`) values (1);
INSERT IGNORE INTO `#__miniorange_jnsp_registersecurity_setup`(`id`) values (1);
INSERT IGNORE INTO `#__miniorange_networksecurity_customer`(`id`,`login_status`) values (1,0);
INSERT IGNORE INTO `#__miniorange_login_transactions`(`id`) values (1);
INSERT IGNORE INTO `#__miniorange_login_transactions_reports`(`id`) values (1);
INSERT IGNORE INTO `#__miniorange_jnsp_advance_blocking`(`id`) values (1);