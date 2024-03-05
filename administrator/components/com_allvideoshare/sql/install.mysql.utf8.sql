CREATE TABLE IF NOT EXISTS `#__allvideoshare_categories` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,    
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) COLLATE utf8_bin NULL,
    `parent` INT(11) NULL DEFAULT 0,
    `thumb` VARCHAR(255) NULL DEFAULT "",
    `description` TEXT NULL, 
    `access` VARCHAR(25) NULL DEFAULT 0,    
    `metakeywords` TEXT NULL,
    `metadescription` TEXT NULL,
    `state` TINYINT(1) NULL DEFAULT 1,
    `ordering` INT(11) NULL DEFAULT 0,
    `checked_out` INT(11) UNSIGNED,
    `checked_out_time` DATETIME NULL DEFAULT NULL,
    `created_by` INT(11) NULL DEFAULT 0,
    `modified_by` INT(11) NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__allvideoshare_videos` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,    
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) COLLATE utf8_bin NULL,
    `catid` INT(11) UNSIGNED NOT NULL,
    `catids` TEXT NULL,
    `type` VARCHAR(25) NULL DEFAULT "general",
    `video` VARCHAR(255) NULL DEFAULT "",
    `hd` VARCHAR(255) NULL DEFAULT "",
    `youtube` VARCHAR(255) NULL DEFAULT "",
    `vimeo` VARCHAR(255) NULL DEFAULT "",
    `hls` VARCHAR(255) NULL DEFAULT "",
    `thirdparty` TEXT NULL,
    `captions` TEXT NULL, 
    `thumb` VARCHAR(255) NULL DEFAULT "",
    `description` TEXT NULL,    
    `access` VARCHAR(25) NULL DEFAULT 0,
    `featured` TINYINT(1) NULL DEFAULT 0,
    `views` INT(11) NULL DEFAULT 0,
    `ratings` DECIMAL(5,2) NULL DEFAULT 0,   
    `likes` INT(11) NULL DEFAULT 0, 
    `dislikes` INT(11) NULL DEFAULT 0, 
    `user` VARCHAR(255) NULL DEFAULT "",
    `tags` TEXT NULL,
    `metadescription` TEXT NULL,
    `state` TINYINT(1) NULL DEFAULT 1,
    `ordering` INT(11) NULL DEFAULT 0,
    `checked_out` INT(11) UNSIGNED,
    `checked_out_time` DATETIME NULL DEFAULT NULL,
    `created_by` INT(11) NULL DEFAULT 0,
    `modified_by` INT(11) NULL DEFAULT 0,
    `created_date` DATETIME NULL DEFAULT NULL,
    `updated_date` DATETIME NULL DEFAULT NULL,    
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__allvideoshare_adverts` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,    
    `title` VARCHAR(255) NOT NULL,
    `type` VARCHAR(255) NULL DEFAULT "both",
    `video` VARCHAR(255) NOT NULL DEFAULT "",
    `link` VARCHAR(255) NULL DEFAULT "",
    `impressions` INT(11) NULL DEFAULT 0,
    `clicks` INT(11) NULL DEFAULT 0,
    `state` TINYINT(1) NULL DEFAULT 1,
    `ordering` INT(11) NULL DEFAULT 0,
    `checked_out` INT(11) UNSIGNED,
    `checked_out_time` DATETIME NULL DEFAULT NULL,
    `created_by` INT(11) NULL DEFAULT 0,
    `modified_by` INT(11) NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE IF NOT EXISTS `#__allvideoshare_cache` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,    
    `name` VARCHAR(255) NOT NULL,
	`value` TEXT NULL,
    `expiry_date` DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;