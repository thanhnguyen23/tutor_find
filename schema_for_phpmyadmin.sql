-- MySQL schema export generated from Laravel migrations
-- Compatible with phpMyAdmin import

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;

-- Drop tables if they exist (order matters for FKs)
DROP TABLE IF EXISTS `websockets_statistics_entries`;
DROP TABLE IF EXISTS `failed_jobs`;
DROP TABLE IF EXISTS `personal_access_tokens`;
DROP TABLE IF EXISTS `password_reset_tokens`;
DROP TABLE IF EXISTS `notification_logs`;
DROP TABLE IF EXISTS `notification_type`;
DROP TABLE IF EXISTS `message`;
DROP TABLE IF EXISTS `conversation`;
DROP TABLE IF EXISTS `class_room`;
DROP TABLE IF EXISTS `user_booking_complaints`;
DROP TABLE IF EXISTS `complaint_type`;
DROP TABLE IF EXISTS `user_booking_logs`;
DROP TABLE IF EXISTS `payments`;
DROP TABLE IF EXISTS `payment_methods`;
DROP TABLE IF EXISTS `user_bookings`;
DROP TABLE IF EXISTS `user_package_features`;
DROP TABLE IF EXISTS `user_study_locations`;
DROP TABLE IF EXISTS `user_languages`;
DROP TABLE IF EXISTS `online_learning_platform`;
DROP TABLE IF EXISTS `user_online_learning_platforms`;
DROP TABLE IF EXISTS `languages`;
DROP TABLE IF EXISTS `time_slots`;
DROP TABLE IF EXISTS `study_locations`;
DROP TABLE IF EXISTS `user_subject_levels`;
DROP TABLE IF EXISTS `user_education_levels`;
DROP TABLE IF EXISTS `user_achievements`;
DROP TABLE IF EXISTS `user_experiences`;
DROP TABLE IF EXISTS `user_educations`;
DROP TABLE IF EXISTS `user_weekly_time_slots`;
DROP TABLE IF EXISTS `user_time_slots`;
DROP TABLE IF EXISTS `user_subjects`;
DROP TABLE IF EXISTS `subjects`;
DROP TABLE IF EXISTS `education_levels`;
DROP TABLE IF EXISTS `category_subjects`;
DROP TABLE IF EXISTS `level_languages`;
DROP TABLE IF EXISTS `day_of_week`;
DROP TABLE IF EXISTS `user_packages`;
DROP TABLE IF EXISTS `packages`;
DROP TABLE IF EXISTS `locations`;
DROP TABLE IF EXISTS `authentication_otp`;
DROP TABLE IF EXISTS `ref_location_wards`;
DROP TABLE IF EXISTS `ref_location_districts`;
DROP TABLE IF EXISTS `ref_location_provinces`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uid` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `about_you` varchar(255) DEFAULT NULL,
  `student_number` int DEFAULT NULL,
  `lesson_number` int DEFAULT NULL,
  `rating_number` int NOT NULL DEFAULT 0,
  `price` int DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `role` int NOT NULL DEFAULT 0 COMMENT '0: student, 1: tutor',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `password` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `cccd` varchar(255) DEFAULT NULL,
  `cccd_front` varchar(255) DEFAULT NULL,
  `cccd_back` varchar(255) DEFAULT NULL,
  `sex` varchar(50) DEFAULT NULL,
  `districts_id` bigint unsigned DEFAULT NULL,
  `provinces_id` bigint unsigned DEFAULT NULL,
  `wards_id` bigint unsigned DEFAULT NULL,
  `title_ads` varchar(255) DEFAULT NULL,
  `education_level_id` bigint unsigned DEFAULT NULL,
  `reminder_emails_sent` int NOT NULL DEFAULT 0,
  `last_reminder_sent_at` timestamp NULL DEFAULT NULL,
  `profile_completed` tinyint(1) NOT NULL DEFAULT 0,
  `referral_link` varchar(255) DEFAULT NULL,
  `is_free_study` tinyint(1) NOT NULL DEFAULT 0,
  `free_study_time` int DEFAULT NULL,
  `last_activity` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_uid_unique` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- study_locations
CREATE TABLE `study_locations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `home_tutor` tinyint(1) NOT NULL DEFAULT 0,
  `home_user` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- time_slots
CREATE TABLE `time_slots` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `time` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- languages
CREATE TABLE `languages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `emoji` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- user_languages
CREATE TABLE `user_languages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uid` varchar(255) NOT NULL,
  `language_id` bigint unsigned NOT NULL,
  `level_language_id` bigint unsigned DEFAULT NULL,
  `is_native` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_languages_language_id_foreign` (`language_id`),
  KEY `user_languages_level_language_id_foreign` (`level_language_id`),
  KEY `user_languages_uid_index` (`uid`),
  CONSTRAINT `user_languages_language_id_foreign` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`),
  CONSTRAINT `user_languages_level_language_id_foreign` FOREIGN KEY (`level_language_id`) REFERENCES `level_languages` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- user_study_locations
CREATE TABLE `user_study_locations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uid` varchar(255) NOT NULL,
  `study_location_id` bigint unsigned NOT NULL,
  `max_distance` int DEFAULT NULL,
  `transportation_fee` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_study_locations_study_location_id_foreign` (`study_location_id`),
  KEY `user_study_locations_uid_index` (`uid`),
  CONSTRAINT `user_study_locations_study_location_id_foreign` FOREIGN KEY (`study_location_id`) REFERENCES `study_locations` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- online_learning_platform
CREATE TABLE `online_learning_platform` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- user_online_learning_platforms
CREATE TABLE `user_online_learning_platforms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uid` varchar(255) NOT NULL,
  `id_online_learning_platform` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_olp_platform_id_foreign` (`id_online_learning_platform`),
  KEY `user_olp_uid_index` (`uid`),
  CONSTRAINT `user_olp_platform_id_foreign` FOREIGN KEY (`id_online_learning_platform`) REFERENCES `online_learning_platform` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- notification_type
CREATE TABLE `notification_type` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- notification_logs
CREATE TABLE `notification_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `notification_type` varchar(255) DEFAULT NULL,
  `uid` varchar(255) DEFAULT NULL,
  `user_uid` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- conversation
CREATE TABLE `conversation` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user1_uid` varchar(255) DEFAULT NULL,
  `user2_uid` varchar(255) DEFAULT NULL,
  `last_message_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- message
CREATE TABLE `message` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `conversation_id` bigint unsigned NOT NULL,
  `sender_id` varchar(255) NOT NULL,
  `receiver_id` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `message_conversation_id_foreign` (`conversation_id`),
  CONSTRAINT `message_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `conversation` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- class_room
CREATE TABLE `class_room` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint unsigned DEFAULT NULL,
  `tutor_uid` varchar(255) DEFAULT NULL,
  `student_uid` varchar(255) DEFAULT NULL,
  `zoom_meeting_id` varchar(255) DEFAULT NULL,
  `zoom_join_url` text DEFAULT NULL,
  `zoom_start_url` text DEFAULT NULL,
  `zoom_password` varchar(255) DEFAULT NULL,
  `zoom_host_id` varchar(255) DEFAULT NULL,
  `scheduled_start_time` datetime DEFAULT NULL,
  `scheduled_end_time` datetime DEFAULT NULL,
  `scheduled_duration` int DEFAULT NULL,
  `actual_start_time` datetime DEFAULT NULL,
  `actual_end_time` datetime DEFAULT NULL,
  `actual_duration` int DEFAULT NULL,
  `participants_count` int DEFAULT NULL,
  `max_participants` int DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `topic` varchar(255) DEFAULT NULL,
  `agenda` text DEFAULT NULL,
  `zoom_created_at` datetime DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- complaint_type
CREATE TABLE `complaint_type` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- user_bookings
CREATE TABLE `user_bookings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uid` varchar(255) NOT NULL,
  `tutor_uid` varchar(255) NOT NULL,
  `request_code` varchar(255) NOT NULL,
  `subject_id` bigint unsigned NOT NULL,
  `education_level_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `start_time_id` bigint unsigned NOT NULL,
  `end_time_id` bigint unsigned NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `study_location_id` bigint unsigned DEFAULT NULL,
  `note` text DEFAULT NULL,
  `hourly_rate` decimal(10,2) DEFAULT NULL,
  `duration` int DEFAULT NULL,
  `package_id` bigint unsigned DEFAULT NULL,
  `num_sessions` int DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `status` varchar(50) NOT NULL,
  `is_package` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- user_booking_logs
CREATE TABLE `user_booking_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uid` varchar(255) NOT NULL,
  `user_booking_id` bigint unsigned NOT NULL,
  `status` varchar(50) NOT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- payments
CREATE TABLE `payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint unsigned NOT NULL,
  `payment_method_id` bigint unsigned NOT NULL,
  `user_uid` varchar(255) NOT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `reference_code` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `fee` decimal(10,2) DEFAULT NULL,
  `refunded_amount` decimal(10,2) DEFAULT NULL,
  `currency` varchar(10) DEFAULT NULL,
  `gateway` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `expired_at` datetime DEFAULT NULL,
  `note` text DEFAULT NULL,
  `raw` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- payment_methods
CREATE TABLE `payment_methods` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(50) DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- user_booking_complaints
CREATE TABLE `user_booking_complaints` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint unsigned NOT NULL,
  `complaint_type_id` bigint unsigned NOT NULL,
  `uid` varchar(255) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(50) NOT NULL,
  `evidence` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- authentication_otp
CREATE TABLE `authentication_otp` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `otp` varchar(255) NOT NULL,
  `expired_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- locations
CREATE TABLE `locations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- packages
CREATE TABLE `packages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `package_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` int DEFAULT NULL,
  `discount_percent` int DEFAULT NULL,
  `max_contacts` int DEFAULT NULL,
  `months` int DEFAULT NULL,
  `years` int DEFAULT NULL,
  `is_popular` tinyint(1) NOT NULL DEFAULT 0,
  `icon` varchar(255) NOT NULL,
  `features` json NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `user_packages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `subject_id` bigint unsigned NOT NULL,
  `level_id` bigint unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `number_of_lessons` int NOT NULL,
  `duration` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_packages_user_id_foreign` (`user_id`),
  KEY `user_packages_subject_id_foreign` (`subject_id`),
  KEY `user_packages_level_id_foreign` (`level_id`),
  CONSTRAINT `user_packages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_packages_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_packages_level_id_foreign` FOREIGN KEY (`level_id`) REFERENCES `education_levels` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- user_package_features
CREATE TABLE `user_package_features` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_package_id` bigint unsigned NOT NULL,
  `feature` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_package_features_user_package_id_foreign` (`user_package_id`),
  CONSTRAINT `user_package_features_user_package_id_foreign` FOREIGN KEY (`user_package_id`) REFERENCES `user_packages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- category_subjects
CREATE TABLE `category_subjects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- education_levels
CREATE TABLE `education_levels` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- subjects
CREATE TABLE `subjects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `category_subject_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subjects_category_subject_id_foreign` (`category_subject_id`),
  CONSTRAINT `subjects_category_subject_id_foreign` FOREIGN KEY (`category_subject_id`) REFERENCES `category_subjects` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ref_location_provinces
CREATE TABLE `ref_location_provinces` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ref_location_districts
CREATE TABLE `ref_location_districts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `province_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ref_location_districts_province_id_foreign` (`province_id`),
  CONSTRAINT `ref_location_districts_province_id_foreign` FOREIGN KEY (`province_id`) REFERENCES `ref_location_provinces` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ref_location_wards
CREATE TABLE `ref_location_wards` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `district_id` bigint unsigned NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ref_location_wards_district_id_foreign` (`district_id`),
  CONSTRAINT `ref_location_wards_district_id_foreign` FOREIGN KEY (`district_id`) REFERENCES `ref_location_districts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `user_subjects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `subject_id` bigint unsigned NOT NULL,
  `level_id` bigint unsigned DEFAULT NULL,
  `years_of_experience` int NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_subjects_user_id_foreign` (`user_id`),
  KEY `user_subjects_subject_id_foreign` (`subject_id`),
  CONSTRAINT `user_subjects_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `user_subjects_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- user_time_slots
CREATE TABLE `user_time_slots` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_time_slots_user_id_foreign` (`user_id`),
  CONSTRAINT `user_time_slots_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `user_weekly_time_slots` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `day_of_week_id` bigint unsigned NOT NULL,
  `time_slot_id_start` bigint unsigned NOT NULL,
  `time_slot_id_end` bigint unsigned NOT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 0,
  `teaching_mode` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- user_educations
CREATE TABLE `user_educations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `school_name` varchar(255) NOT NULL,
  `major` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `certificate` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_educations_user_id_foreign` (`user_id`),
  CONSTRAINT `user_educations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `user_experiences` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_experiences_user_id_foreign` (`user_id`),
  CONSTRAINT `user_experiences_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- user_achievements
CREATE TABLE `user_achievements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `achievement` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_achievements_user_id_foreign` (`user_id`),
  CONSTRAINT `user_achievements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- user_education_levels
CREATE TABLE `user_education_levels` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `education_level_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_education_levels_user_id_foreign` (`user_id`),
  KEY `user_education_levels_education_level_id_foreign` (`education_level_id`),
  CONSTRAINT `user_education_levels_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `user_education_levels_education_level_id_foreign` FOREIGN KEY (`education_level_id`) REFERENCES `education_levels` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `user_subject_levels` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_subject_id` bigint unsigned NOT NULL,
  `education_level_id` bigint unsigned NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_subject_levels_user_subject_id_foreign` (`user_subject_id`),
  KEY `user_subject_levels_education_level_id_foreign` (`education_level_id`),
  CONSTRAINT `user_subject_levels_user_subject_id_foreign` FOREIGN KEY (`user_subject_id`) REFERENCES `user_subjects` (`id`),
  CONSTRAINT `user_subject_levels_education_level_id_foreign` FOREIGN KEY (`education_level_id`) REFERENCES `education_levels` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- level_languages
CREATE TABLE `level_languages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- day_of_week
CREATE TABLE `day_of_week` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- password_reset_tokens
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- personal_access_tokens
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- failed_jobs
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- websockets_statistics_entries
CREATE TABLE `websockets_statistics_entries` (
  `id` int NOT NULL AUTO_INCREMENT,
  `app_id` varchar(255) NOT NULL,
  `peak_connection_count` int NOT NULL,
  `websocket_message_count` int NOT NULL,
  `api_message_count` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default seed data

-- day_of_week
INSERT INTO `day_of_week` (`name`, `slug`, `created_at`, `updated_at`) VALUES
('Thứ Hai', 'monday', NOW(), NOW()),
('Thứ Ba', 'tuesday', NOW(), NOW()),
('Thứ Tư', 'wednesday', NOW(), NOW()),
('Thứ Năm', 'thursday', NOW(), NOW()),
('Thứ Sáu', 'friday', NOW(), NOW()),
('Thứ Bảy', 'saturday', NOW(), NOW()),
('Chủ Nhật', 'sunday', NOW(), NOW());

-- level_languages
INSERT INTO `level_languages` (`name`, `created_at`, `updated_at`) VALUES
('Beginner', NOW(), NOW()),
('Elementary', NOW(), NOW()),
('Intermediate', NOW(), NOW()),
('Upper-Intermediate', NOW(), NOW()),
('Advanced', NOW(), NOW()),
('Proficient', NOW(), NOW());

-- payment_methods
INSERT INTO `payment_methods` (`name`, `description`, `is_default`, `status`, `code`, `created_at`, `updated_at`) VALUES
('Chuyển khoản ngân hàng', 'Thanh toán qua tài khoản ngân hàng', 1, 'active', 'atm', NOW(), NOW()),
('VNPay', 'Thanh toán qua VNPay', 0, 'active', 'vnpay', NOW(), NOW()),
('Tiền mặt', 'Thanh toán bằng tiền mặt', 0, 'active', 'cash', NOW(), NOW()),
('Momo', 'Thanh toán qua ví Momo', 0, 'active', 'momo', NOW(), NOW());

-- notification_type
INSERT INTO `notification_type` (`name`, `icon`, `type`, `created_at`, `updated_at`) VALUES
('Lịch học', NULL, 'schedule', NOW(), NOW()),
('Tin nhắn', NULL, 'message', NOW(), NOW()),
('Đánh giá', NULL, 'review', NOW(), NOW()),
('Hồ sơ', NULL, 'profile', NOW(), NOW());

-- languages
INSERT INTO `languages` (`name`, `emoji`, `created_at`, `updated_at`) VALUES
('Tiếng Việt', '🇻🇳', NOW(), NOW()),
('English', '🇬🇧', NOW(), NOW());

-- study_locations
INSERT INTO `study_locations` (`name`, `description`, `icon`, `home_tutor`, `home_user`, `created_at`, `updated_at`) VALUES
('Tại nhà gia sư', 'Học tại địa chỉ của gia sư', NULL, 1, 0, NOW(), NOW()),
('Tại nhà học sinh', 'Gia sư đến nhà học sinh', NULL, 0, 1, NOW(), NOW()),
('Online', 'Học trực tuyến qua nền tảng video', NULL, 0, 0, NOW(), NOW());

-- time_slots (giờ chẵn từ 06:00 đến 22:00)
INSERT INTO `time_slots` (`time`, `name`, `created_at`, `updated_at`) VALUES
('06:00', '06:00', NOW(), NOW()),
('07:00', '07:00', NOW(), NOW()),
('08:00', '08:00', NOW(), NOW()),
('09:00', '09:00', NOW(), NOW()),
('10:00', '10:00', NOW(), NOW()),
('11:00', '11:00', NOW(), NOW()),
('12:00', '12:00', NOW(), NOW()),
('13:00', '13:00', NOW(), NOW()),
('14:00', '14:00', NOW(), NOW()),
('15:00', '15:00', NOW(), NOW()),
('16:00', '16:00', NOW(), NOW()),
('17:00', '17:00', NOW(), NOW()),
('18:00', '18:00', NOW(), NOW()),
('19:00', '19:00', NOW(), NOW()),
('20:00', '20:00', NOW(), NOW()),
('21:00', '21:00', NOW(), NOW()),
('22:00', '22:00', NOW(), NOW());

-- online_learning_platform
INSERT INTO `online_learning_platform` (`name`, `created_at`, `updated_at`) VALUES
('Zoom', NOW(), NOW()),
('Google Meet', NOW(), NOW()),
('Microsoft Teams', NOW(), NOW());

-- complaint_type
INSERT INTO `complaint_type` (`name`, `description`, `created_at`, `updated_at`) VALUES
('Chất lượng lớp học', 'Khiếu nại về chất lượng buổi học', NOW(), NOW()),
('Thời gian', 'Khiếu nại về lịch học hoặc trễ giờ', NOW(), NOW()),
('Thanh toán', 'Khiếu nại về phí hoặc hoàn tiền', NOW(), NOW()),
('Khác', 'Các khiếu nại khác', NOW(), NOW());

-- education_levels
INSERT INTO `education_levels` (`name`, `slug`, `image`, `created_at`, `updated_at`) VALUES
('Tiểu học', 'tieu-hoc', 'default.png', NOW(), NOW()),
('THCS', 'thcs', 'default.png', NOW(), NOW()),
('THPT', 'thpt', 'default.png', NOW(), NOW()),
('Đại học', 'dai-hoc', 'default.png', NOW(), NOW());

-- ref_location_provinces
INSERT INTO `ref_location_provinces` (`name`, `slug`, `type`, `created_at`, `updated_at`) VALUES
('Hà Nội', 'ha-noi', 'Thành phố', NOW(), NOW());

-- ref_location_districts (belongs to province id = 1)
INSERT INTO `ref_location_districts` (`name`, `slug`, `type`, `province_id`, `created_at`, `updated_at`) VALUES
('Ba Đình', 'ba-dinh', 'Quận', 1, NOW(), NOW());

-- ref_location_wards (belongs to district id = 1)
INSERT INTO `ref_location_wards` (`name`, `district_id`, `slug`, `type`, `created_at`, `updated_at`) VALUES
('Phúc Xá', 1, 'phuc-xa', 'Phường', NOW(), NOW());

-- category_subjects (seed one category for subjects FK)
INSERT INTO `category_subjects` (`name`, `slug`, `description`, `created_at`, `updated_at`) VALUES
('Môn học phổ thông', 'mon-hoc-pho-thong', 'Nhóm các môn học phổ thông', NOW(), NOW());

-- subjects (reference category_subject_id = 1)
INSERT INTO `subjects` (`name`, `slug`, `image`, `category_subject_id`, `created_at`, `updated_at`) VALUES
('Toán', 'toan', 'default.png', 1, NOW(), NOW()),
('Ngữ văn', 'ngu-van', 'default.png', 1, NOW(), NOW()),
('Tiếng Anh', 'tieng-anh', 'default.png', 1, NOW(), NOW());

SET FOREIGN_KEY_CHECKS=1;

