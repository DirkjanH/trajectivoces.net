ALTER TABLE `#__allvideoshare_categories` ADD COLUMN `description` TEXT NULL AFTER `thumb`;

ALTER TABLE `#__allvideoshare_videos` ADD COLUMN `captions` TEXT NULL AFTER `thirdparty`;