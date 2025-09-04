ALTER TABLE `#__allvideoshare_categories` ENGINE=INNODB;
ALTER TABLE `#__allvideoshare_categories` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__allvideoshare_categories` CHANGE published state TINYINT(1);
ALTER TABLE `#__allvideoshare_categories` ADD COLUMN `checked_out` INT(11) UNSIGNED AFTER `ordering`;
ALTER TABLE `#__allvideoshare_categories` ADD COLUMN `checked_out_time` DATETIME NULL DEFAULT NULL AFTER `ordering`;
ALTER TABLE `#__allvideoshare_categories` ADD COLUMN `created_by` INT(11) NULL DEFAULT 0 AFTER `ordering`;
ALTER TABLE `#__allvideoshare_categories` ADD COLUMN `modified_by` INT(11) NULL DEFAULT 0 AFTER `ordering`;

ALTER TABLE `#__allvideoshare_videos` ENGINE=INNODB;
ALTER TABLE `#__allvideoshare_videos` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__allvideoshare_videos` CHANGE published state TINYINT(1);
ALTER TABLE `#__allvideoshare_videos` ADD COLUMN `youtube` VARCHAR(255) NULL DEFAULT "" AFTER `hd`;
ALTER TABLE `#__allvideoshare_videos` ADD COLUMN `vimeo` VARCHAR(255) NULL DEFAULT "" AFTER `hd`;
ALTER TABLE `#__allvideoshare_videos` ADD COLUMN `checked_out` INT(11) UNSIGNED AFTER `ordering`;
ALTER TABLE `#__allvideoshare_videos` ADD COLUMN `checked_out_time` DATETIME NULL DEFAULT NULL AFTER `ordering`;
ALTER TABLE `#__allvideoshare_videos` ADD COLUMN `created_by` INT(11) NULL DEFAULT 0 AFTER `ordering`;
ALTER TABLE `#__allvideoshare_videos` ADD COLUMN `modified_by` INT(11) NULL DEFAULT 0 AFTER `ordering`;
ALTER TABLE `#__allvideoshare_videos` ADD COLUMN `updated_date` DATETIME NULL DEFAULT NULL AFTER `ordering`;

ALTER TABLE `#__allvideoshare_adverts` ENGINE=INNODB;
ALTER TABLE `#__allvideoshare_adverts` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__allvideoshare_adverts` CHANGE published state TINYINT(1);
ALTER TABLE `#__allvideoshare_adverts` ADD COLUMN `ordering` INT(11) NULL DEFAULT 0 AFTER `clicks`;
ALTER TABLE `#__allvideoshare_adverts` ADD COLUMN `checked_out` INT(11) UNSIGNED AFTER `clicks`;
ALTER TABLE `#__allvideoshare_adverts` ADD COLUMN `checked_out_time` DATETIME NULL DEFAULT NULL AFTER `clicks`;
ALTER TABLE `#__allvideoshare_adverts` ADD COLUMN `created_by` INT(11) NULL DEFAULT 0 AFTER `clicks`;
ALTER TABLE `#__allvideoshare_adverts` ADD COLUMN `modified_by` INT(11) NULL DEFAULT 0 AFTER `clicks`;