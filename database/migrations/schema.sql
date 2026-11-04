-- ==========================================================
-- Bayan AI Quran Teacher Database Schema (MySQL 8 / MariaDB)
-- Character Set: utf8mb4 / utf8mb4_unicode_ci
-- ==========================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Roles Table
CREATE TABLE IF NOT EXISTS `roles` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL UNIQUE,
  `display_name` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Users Table
CREATE TABLE IF NOT EXISTS `users` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(191) NOT NULL,
  `email` VARCHAR(191) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'student') NOT NULL DEFAULT 'student',
  `status` ENUM('active', 'inactive', 'suspended') NOT NULL DEFAULT 'active',
  `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
  `avatar` VARCHAR(255) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_users_role` (`role`),
  INDEX `idx_users_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. User Profiles Table
CREATE TABLE IF NOT EXISTS `user_profiles` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NOT NULL UNIQUE,
  `phone` VARCHAR(50) NULL,
  `country` VARCHAR(100) NULL,
  `preferred_qari_id` INT NULL DEFAULT 1,
  `preferred_translation` VARCHAR(50) NULL DEFAULT 'ur.jalandhry',
  `daily_goal_minutes` INT NOT NULL DEFAULT 15,
  `daily_ayah_target` INT NOT NULL DEFAULT 5,
  `arabic_font_size` INT NOT NULL DEFAULT 28,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_profile_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Surahs Table (114 Surahs Canonical Metadata)
CREATE TABLE IF NOT EXISTS `surahs` (
  `id` INT NOT NULL PRIMARY KEY,
  `name_arabic` VARCHAR(100) NOT NULL,
  `name_english` VARCHAR(100) NOT NULL,
  `name_translation` VARCHAR(150) NOT NULL,
  `revelation_type` ENUM('Meccan', 'Medinan') NOT NULL,
  `total_ayahs` INT NOT NULL,
  `page_number` INT NOT NULL DEFAULT 1,
  `juz_number` INT NOT NULL DEFAULT 1,
  INDEX `idx_surahs_revelation` (`revelation_type`),
  INDEX `idx_surahs_juz` (`juz_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Qari Profiles Table (Authentic Reciters)
CREATE TABLE IF NOT EXISTS `qari_profiles` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `identifier` VARCHAR(100) NOT NULL UNIQUE,
  `name_arabic` VARCHAR(150) NOT NULL,
  `name_english` VARCHAR(150) NOT NULL,
  `style` VARCHAR(100) NOT NULL DEFAULT 'Murattal',
  `server_url` VARCHAR(255) NOT NULL,
  `format` VARCHAR(20) NOT NULL DEFAULT 'mp3',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Reading Progress Table
CREATE TABLE IF NOT EXISTS `reading_progress` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `surah_id` INT NOT NULL,
  `ayah_number` INT NOT NULL DEFAULT 1,
  `last_read_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uniq_user_surah` (`user_id`, `surah_id`),
  CONSTRAINT `fk_reading_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Bookmarks Table
CREATE TABLE IF NOT EXISTS `bookmarks` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `surah_id` INT NOT NULL,
  `ayah_number` INT NOT NULL,
  `note` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uniq_user_bookmark` (`user_id`, `surah_id`, `ayah_number`),
  CONSTRAINT `fk_bookmarks_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Practice Sessions (AI Quran Teacher Recitation Practice)
CREATE TABLE IF NOT EXISTS `practice_sessions` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `surah_id` INT NOT NULL,
  `ayah_number` INT NOT NULL,
  `recognized_text` TEXT NULL,
  `target_text` TEXT NOT NULL,
  `accuracy_score` DECIMAL(5,2) NULL,
  `status` ENUM('passed', 'needs_practice', 'unverified') NOT NULL DEFAULT 'unverified',
  `feedback_json` JSON NULL,
  `ai_provider` VARCHAR(50) NOT NULL DEFAULT 'gemini',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_practice_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Hifz Progress Table (Memorization & Revision Tracker)
CREATE TABLE IF NOT EXISTS `hifz_progress` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `surah_id` INT NOT NULL,
  `start_ayah` INT NOT NULL,
  `end_ayah` INT NOT NULL,
  `status` ENUM('learning', 'memorized', 'needs_revision') NOT NULL DEFAULT 'learning',
  `memorized_date` DATE NULL,
  `last_reviewed_at` TIMESTAMP NULL DEFAULT NULL,
  `next_revision_date` DATE NULL,
  `revision_count` INT NOT NULL DEFAULT 0,
  `confidence_level` INT NOT NULL DEFAULT 3,
  `notes` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_hifz_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Site Settings Table
CREATE TABLE IF NOT EXISTS `site_settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` LONGTEXT NULL,
  `setting_group` VARCHAR(50) NOT NULL DEFAULT 'general',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. SEO Metadata Table
CREATE TABLE IF NOT EXISTS `seo_metadata` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `route_path` VARCHAR(191) NOT NULL UNIQUE,
  `meta_title` VARCHAR(191) NOT NULL,
  `meta_description` TEXT NOT NULL,
  `meta_keywords` VARCHAR(255) NULL,
  `og_image` VARCHAR(255) NULL,
  `robots` VARCHAR(50) NOT NULL DEFAULT 'index, follow',
  `schema_type` VARCHAR(50) NOT NULL DEFAULT 'WebSite',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Analytics Events Table
CREATE TABLE IF NOT EXISTS `analytics_events` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NULL,
  `event_type` VARCHAR(100) NOT NULL,
  `page_url` VARCHAR(255) NOT NULL,
  `referrer` VARCHAR(255) NULL,
  `user_agent` VARCHAR(255) NULL,
  `ip_hash` VARCHAR(64) NULL,
  `metadata_json` JSON NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_events_type` (`event_type`),
  INDEX `idx_events_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. Admin Audit Logs
CREATE TABLE IF NOT EXISTS `admin_audit_logs` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `admin_id` BIGINT UNSIGNED NOT NULL,
  `action` VARCHAR(100) NOT NULL,
  `details` TEXT NULL,
  `ip_address` VARCHAR(45) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_audit_admin` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
