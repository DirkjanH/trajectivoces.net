ALTER TABLE `#__allvideoshare_videos` ADD COLUMN `ratings` DECIMAL(5,2) NULL DEFAULT 0 AFTER `views`;
ALTER TABLE `#__allvideoshare_videos` ADD COLUMN `likes` INT(11) NULL DEFAULT 0 AFTER `views`;
ALTER TABLE `#__allvideoshare_videos` ADD COLUMN `dislikes` INT(11) NULL DEFAULT 0 AFTER `views`;

CREATE TABLE IF NOT EXISTS `#__allvideoshare_ratings` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,     
    `videoid` INT(11) NULL DEFAULT 0,    
    `ratings` DECIMAL(2,1) NULL DEFAULT 0,
    `userid` INT(11) NULL DEFAULT 0,
    `sessionid` VARCHAR(255) NOT NULL,
    `created_date` DATETIME NULL DEFAULT NULL,
    `updated_date` DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__allvideoshare_likes` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,    
    `videoid` INT(11) NULL DEFAULT 0,    
    `likes` INT(11) NULL DEFAULT 0,
    `dislikes` INT(11) NULL DEFAULT 0,
    `userid` INT(11) NULL DEFAULT 0,
    `sessionid` VARCHAR(255) NOT NULL,
    `created_date` DATETIME NULL DEFAULT NULL,
    `updated_date` DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;