-- Radu pērļu konsolidētais MySQL skripts
-- Izmantojams jaunos un esošos serveros, kur pastāv Laravel tabula users.
-- Skripts pievieno pērļu tabulas, noņem vecos persona_id/event_date laukus
-- un izveido reakciju tabulu.

START TRANSACTION;

CREATE TABLE IF NOT EXISTS `family_pearl_categories` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `slug` varchar(120) NOT NULL,
    `is_active` tinyint(1) NOT NULL DEFAULT 1,
    `sort_order` smallint UNSIGNED NOT NULL DEFAULT 0,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `family_pearl_categories_name_unique` (`name`),
    UNIQUE KEY `family_pearl_categories_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `family_pearls` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` bigint UNSIGNED NOT NULL,
    `category_id` bigint UNSIGNED NOT NULL,
    `title` varchar(160) NOT NULL,
    `text` text NOT NULL,
    `context` text NULL DEFAULT NULL,
    `is_anonymous` tinyint(1) NOT NULL DEFAULT 0,
    `status` varchar(20) NOT NULL DEFAULT 'published',
    `moderation_note` text NULL DEFAULT NULL,
    `published_at` timestamp NULL DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `family_pearls_status_published_at_index` (`status`, `published_at`),
    KEY `family_pearls_category_id_status_index` (`category_id`, `status`),
    KEY `family_pearls_user_id_foreign` (`user_id`),
    KEY `family_pearls_category_id_foreign` (`category_id`),
    CONSTRAINT `family_pearls_user_id_foreign`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `family_pearls_category_id_foreign`
        FOREIGN KEY (`category_id`) REFERENCES `family_pearl_categories` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `family_pearl_media` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    `pearl_id` bigint UNSIGNED NOT NULL,
    `type` varchar(20) NOT NULL,
    `path` varchar(255) NOT NULL,
    `disk` varchar(20) NOT NULL DEFAULT 'private',
    `mime_type` varchar(100) NOT NULL,
    `size` bigint UNSIGNED NOT NULL,
    `sort_order` smallint UNSIGNED NOT NULL DEFAULT 0,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `family_pearl_media_pearl_id_foreign` (`pearl_id`),
    CONSTRAINT `family_pearl_media_pearl_id_foreign`
        FOREIGN KEY (`pearl_id`) REFERENCES `family_pearls` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `family_pearl_reactions` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    `pearl_id` bigint UNSIGNED NOT NULL,
    `user_id` bigint UNSIGNED NOT NULL,
    `emoji` varchar(16) NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `family_pearl_reactions_pearl_id_user_id_unique` (`pearl_id`, `user_id`),
    KEY `family_pearl_reactions_pearl_id_emoji_index` (`pearl_id`, `emoji`),
    KEY `family_pearl_reactions_user_id_foreign` (`user_id`),
    CONSTRAINT `family_pearl_reactions_pearl_id_foreign`
        FOREIGN KEY (`pearl_id`) REFERENCES `family_pearls` (`id`) ON DELETE CASCADE,
    CONSTRAINT `family_pearl_reactions_user_id_foreign`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Vecās personas kolonnas un tabula tiek noņemtas, ja tās izveidotas iepriekšējā migrācijas versijā.
DROP PROCEDURE IF EXISTS `drop_family_pearl_legacy_columns`;

DELIMITER $$
CREATE PROCEDURE `drop_family_pearl_legacy_columns`()
BEGIN
    IF EXISTS (
        SELECT 1
        FROM information_schema.TABLE_CONSTRAINTS
        WHERE CONSTRAINT_SCHEMA = DATABASE()
          AND TABLE_NAME = 'family_pearls'
          AND CONSTRAINT_NAME = 'family_pearls_person_id_foreign'
          AND CONSTRAINT_TYPE = 'FOREIGN KEY'
    ) THEN
        ALTER TABLE `family_pearls` DROP FOREIGN KEY `family_pearls_person_id_foreign`;
    END IF;

    IF EXISTS (
        SELECT 1
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'family_pearls'
          AND COLUMN_NAME = 'person_id'
    ) THEN
        ALTER TABLE `family_pearls` DROP COLUMN `person_id`;
    END IF;

    IF EXISTS (
        SELECT 1
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'family_pearls'
          AND COLUMN_NAME = 'event_date'
    ) THEN
        ALTER TABLE `family_pearls` DROP COLUMN `event_date`;
    END IF;

    DROP TABLE IF EXISTS `family_people`;
END$$
DELIMITER ;

CALL `drop_family_pearl_legacy_columns`();
DROP PROCEDURE `drop_family_pearl_legacy_columns`;

-- Esošās gaidošās pērles kļūst publicētas.
UPDATE `family_pearls`
SET
    `status` = 'published',
    `published_at` = COALESCE(`published_at`, `created_at`, CURRENT_TIMESTAMP)
WHERE `status` = 'pending';

ALTER TABLE `family_pearls`
    MODIFY COLUMN `status` varchar(20) NOT NULL DEFAULT 'published';

INSERT INTO `family_pearl_categories`
    (`name`, `slug`, `is_active`, `sort_order`, `created_at`, `updated_at`)
VALUES
    ('Citāts', 'citats', 1, 10, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    ('Notikums', 'notikums', 1, 20, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    ('Smieklīga situācija', 'situacija', 1, 30, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    ('Atmiņa', 'atmina', 1, 40, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    ('Cits', 'cits', 1, 50, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
ON DUPLICATE KEY UPDATE
    `name` = VALUES(`name`),
    `is_active` = 1,
    `sort_order` = VALUES(`sort_order`),
    `updated_at` = CURRENT_TIMESTAMP;

COMMIT;
