-- ============================================================
-- MindBridge Database Schema
-- Mental Health & Wellness Portal
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

DROP DATABASE IF EXISTS `mindbridge`;
CREATE DATABASE `mindbridge` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `mindbridge`;

-- ============================================================
-- TABLE: users (base auth table for all roles)
-- ============================================================
CREATE TABLE `users` (
  `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `email`         VARCHAR(191) NOT NULL UNIQUE,
  `password`      VARCHAR(255) NOT NULL,
  `first_name`    VARCHAR(100) NOT NULL,
  `last_name`     VARCHAR(100) NOT NULL,
  `role`          ENUM('patient','therapist','admin') NOT NULL DEFAULT 'patient',
  `status`        ENUM('active','inactive','suspended','pending') NOT NULL DEFAULT 'pending',
  `avatar`        VARCHAR(255) DEFAULT NULL,
  `phone`         VARCHAR(30) DEFAULT NULL,
  `date_of_birth` DATE DEFAULT NULL,
  `gender`        ENUM('male','female','non_binary','prefer_not') DEFAULT NULL,
  `timezone`      VARCHAR(64) NOT NULL DEFAULT 'UTC',
  `last_login`    DATETIME DEFAULT NULL,
  `email_verified`TINYINT(1) NOT NULL DEFAULT 0,
  `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_role` (`role`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: patients (1:1 with users)
-- ============================================================
CREATE TABLE `patients` (
  `id`                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`             INT UNSIGNED NOT NULL UNIQUE,
  `insurance_provider`  VARCHAR(150) DEFAULT NULL,
  `insurance_number`    VARCHAR(100) DEFAULT NULL,
  `insurance_verified`  TINYINT(1) NOT NULL DEFAULT 0,
  `emergency_contact`   VARCHAR(200) DEFAULT NULL,
  `emergency_phone`     VARCHAR(30) DEFAULT NULL,
  `address`             VARCHAR(300) DEFAULT NULL,
  `city`                VARCHAR(100) DEFAULT NULL,
  `country`             VARCHAR(100) DEFAULT 'Egypt',
  `preferred_language`  VARCHAR(50) NOT NULL DEFAULT 'English',
  `onboarding_step`     TINYINT NOT NULL DEFAULT 0 COMMENT '0=registered,1=intake,2=consent,3=matched,4=complete',
  `assigned_therapist`  INT UNSIGNED DEFAULT NULL,
  `notes`               TEXT DEFAULT NULL,
  `created_at`          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_patients_user`
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_patients_therapist`
    FOREIGN KEY (`assigned_therapist`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  INDEX `idx_patient_therapist` (`assigned_therapist`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: therapists (1:1 with users)
-- ============================================================
CREATE TABLE `therapists` (
  `id`                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`           INT UNSIGNED NOT NULL UNIQUE,
  `license_number`    VARCHAR(100) NOT NULL,
  `license_verified`  TINYINT(1) NOT NULL DEFAULT 0,
  `specializations`   VARCHAR(500) DEFAULT NULL COMMENT 'comma-separated list',
  `languages`         VARCHAR(300) DEFAULT 'English' COMMENT 'comma-separated list',
  `bio`               TEXT DEFAULT NULL,
  `years_experience`  TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `session_rate`      DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `accepts_insurance` TINYINT(1) NOT NULL DEFAULT 0,
  `max_patients`      TINYINT UNSIGNED NOT NULL DEFAULT 20,
  `current_patients`  TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `rating`            DECIMAL(3,2) NOT NULL DEFAULT 0.00,
  `total_reviews`     INT UNSIGNED NOT NULL DEFAULT 0,
  `is_available`      TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_therapists_user`
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: therapist_availability
-- ============================================================
CREATE TABLE `therapist_availability` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `therapist_id`INT UNSIGNED NOT NULL,
  `day_of_week` TINYINT NOT NULL COMMENT '0=Sun,1=Mon,...,6=Sat',
  `start_time`  TIME NOT NULL,
  `end_time`    TIME NOT NULL,
  `is_active`   TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_avail_therapist`
    FOREIGN KEY (`therapist_id`) REFERENCES `therapists`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX `idx_avail_day` (`therapist_id`, `day_of_week`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: intake_forms
-- ============================================================
CREATE TABLE `intake_forms` (
  `id`                    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `patient_id`            INT UNSIGNED NOT NULL,
  `primary_concerns`      TEXT DEFAULT NULL,
  `mental_health_history` TEXT DEFAULT NULL,
  `current_medications`   TEXT DEFAULT NULL,
  `previous_therapy`      TINYINT(1) NOT NULL DEFAULT 0,
  `therapy_type_pref`     VARCHAR(200) DEFAULT NULL,
  `therapist_gender_pref` ENUM('male','female','no_preference') NOT NULL DEFAULT 'no_preference',
  `preferred_language`    VARCHAR(50) NOT NULL DEFAULT 'English',
  `session_format_pref`   ENUM('video','audio','chat','no_preference') NOT NULL DEFAULT 'no_preference',
  `availability_notes`    TEXT DEFAULT NULL,
  `urgency_level`         ENUM('low','medium','high','crisis') NOT NULL DEFAULT 'medium',
  `goals`                 TEXT DEFAULT NULL,
  `submitted_at`          DATETIME DEFAULT NULL,
  `status`                ENUM('draft','submitted','reviewed') NOT NULL DEFAULT 'draft',
  `created_at`            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_intake_patient`
    FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  UNIQUE KEY `uq_intake_patient` (`patient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: consent_forms
-- ============================================================
CREATE TABLE `consent_forms` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `patient_id`   INT UNSIGNED NOT NULL,
  `form_type`    ENUM('service_agreement','privacy_policy','telehealth_consent','hipaa') NOT NULL,
  `content`      TEXT DEFAULT NULL,
  `signature`    VARCHAR(500) DEFAULT NULL,
  `ip_address`   VARCHAR(45) DEFAULT NULL,
  `signed_at`    DATETIME DEFAULT NULL,
  `is_signed`    TINYINT(1) NOT NULL DEFAULT 0,
  `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_consent_patient`
    FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  UNIQUE KEY `uq_consent` (`patient_id`, `form_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: therapist_matches
-- ============================================================
CREATE TABLE `therapist_matches` (
  `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `patient_id`      INT UNSIGNED NOT NULL,
  `therapist_id`    INT UNSIGNED NOT NULL,
  `match_score`     DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  `match_reasons`   TEXT DEFAULT NULL COMMENT 'JSON array of matching criteria',
  `status`          ENUM('suggested','accepted','declined','expired') NOT NULL DEFAULT 'suggested',
  `suggested_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `responded_at`    DATETIME DEFAULT NULL,
  `created_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_match_patient`
    FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_match_therapist`
    FOREIGN KEY (`therapist_id`) REFERENCES `therapists`(`id`) ON DELETE CASCADE,
  INDEX `idx_match_status` (`status`),
  UNIQUE KEY `uq_match` (`patient_id`, `therapist_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: waitlists
-- ============================================================
CREATE TABLE `waitlists` (
  `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `patient_id`     INT UNSIGNED NOT NULL,
  `therapist_id`   INT UNSIGNED NOT NULL,
  `position`       INT UNSIGNED NOT NULL DEFAULT 1,
  `status`         ENUM('waiting','notified','accepted','removed') NOT NULL DEFAULT 'waiting',
  `notes`          TEXT DEFAULT NULL,
  `joined_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `notified_at`    DATETIME DEFAULT NULL,
  CONSTRAINT `fk_waitlist_patient`
    FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_waitlist_therapist`
    FOREIGN KEY (`therapist_id`) REFERENCES `therapists`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `uq_waitlist` (`patient_id`, `therapist_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: appointments
-- ============================================================
CREATE TABLE `appointments` (
  `id`               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `patient_id`       INT UNSIGNED NOT NULL,
  `therapist_id`     INT UNSIGNED NOT NULL,
  `scheduled_at`     DATETIME NOT NULL,
  `duration_minutes` SMALLINT UNSIGNED NOT NULL DEFAULT 50,
  `type`             ENUM('video','audio','chat') NOT NULL DEFAULT 'video',
  `status`           ENUM('scheduled','confirmed','in_progress','completed','cancelled','no_show','rescheduled') NOT NULL DEFAULT 'scheduled',
  `patient_notes`    TEXT DEFAULT NULL,
  `cancel_reason`    VARCHAR(500) DEFAULT NULL,
  `cancelled_by`     INT UNSIGNED DEFAULT NULL,
  `created_at`       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_appt_patient`
    FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_appt_therapist`
    FOREIGN KEY (`therapist_id`) REFERENCES `therapists`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_appt_cancelled_by`
    FOREIGN KEY (`cancelled_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  INDEX `idx_appt_scheduled` (`scheduled_at`),
  INDEX `idx_appt_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: sessions (therapy session records)
-- ============================================================
CREATE TABLE `sessions` (
  `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `appointment_id`  INT UNSIGNED NOT NULL UNIQUE,
  `therapist_notes` TEXT DEFAULT NULL,
  `patient_notes`   TEXT DEFAULT NULL,
  `mood_before`     TINYINT UNSIGNED DEFAULT NULL,
  `mood_after`      TINYINT UNSIGNED DEFAULT NULL,
  `duration_actual` SMALLINT UNSIGNED DEFAULT NULL,
  `techniques_used` VARCHAR(500) DEFAULT NULL,
  `homework`        TEXT DEFAULT NULL,
  `outcome`         ENUM('good','neutral','poor','crisis') DEFAULT NULL,
  `follow_up_date`  DATE DEFAULT NULL,
  `started_at`      DATETIME DEFAULT NULL,
  `ended_at`        DATETIME DEFAULT NULL,
  `created_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_session_appt`
    FOREIGN KEY (`appointment_id`) REFERENCES `appointments`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: payments
-- ============================================================
CREATE TABLE `payments` (
  `id`               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `appointment_id`   INT UNSIGNED NOT NULL,
  `patient_id`       INT UNSIGNED NOT NULL,
  `amount`           DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `currency`         VARCHAR(10) NOT NULL DEFAULT 'USD',
  `method`           ENUM('credit_card','insurance','bank_transfer','cash') NOT NULL DEFAULT 'credit_card',
  `status`           ENUM('pending','paid','refunded','failed') NOT NULL DEFAULT 'pending',
  `transaction_ref`  VARCHAR(200) DEFAULT NULL,
  `paid_at`          DATETIME DEFAULT NULL,
  `notes`            TEXT DEFAULT NULL,
  `created_at`       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_payment_appt`
    FOREIGN KEY (`appointment_id`) REFERENCES `appointments`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_payment_patient`
    FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
  INDEX `idx_payment_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: messages
-- ============================================================
CREATE TABLE `messages` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `sender_id`   INT UNSIGNED NOT NULL,
  `receiver_id` INT UNSIGNED NOT NULL,
  `subject`     VARCHAR(300) DEFAULT NULL,
  `content`     TEXT NOT NULL,
  `is_read`     TINYINT(1) NOT NULL DEFAULT 0,
  `read_at`     DATETIME DEFAULT NULL,
  `parent_id`   INT UNSIGNED DEFAULT NULL COMMENT 'for replies',
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_msg_sender`
    FOREIGN KEY (`sender_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_msg_receiver`
    FOREIGN KEY (`receiver_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_msg_parent`
    FOREIGN KEY (`parent_id`) REFERENCES `messages`(`id`) ON DELETE SET NULL,
  INDEX `idx_msg_receiver` (`receiver_id`, `is_read`),
  INDEX `idx_msg_sender` (`sender_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: notifications
-- ============================================================
CREATE TABLE `notifications` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`    INT UNSIGNED NOT NULL,
  `type`       VARCHAR(60) NOT NULL,
  `title`      VARCHAR(255) NOT NULL,
  `message`    TEXT NOT NULL,
  `link`       VARCHAR(500) DEFAULT NULL,
  `is_read`    TINYINT(1) NOT NULL DEFAULT 0,
  `read_at`    DATETIME DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_notif_user`
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX `idx_notif_user` (`user_id`, `is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: journals
-- ============================================================
CREATE TABLE `journals` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `patient_id` INT UNSIGNED NOT NULL,
  `title`      VARCHAR(300) NOT NULL,
  `content`    TEXT NOT NULL,
  `mood_tag`   VARCHAR(100) DEFAULT NULL,
  `is_private` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_journal_patient`
    FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
  INDEX `idx_journal_date` (`patient_id`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: mood_entries
-- ============================================================
CREATE TABLE `mood_entries` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `patient_id`  INT UNSIGNED NOT NULL,
  `mood_level`  TINYINT UNSIGNED NOT NULL COMMENT '1=very low, 10=excellent',
  `notes`       TEXT DEFAULT NULL,
  `triggers`    VARCHAR(500) DEFAULT NULL,
  `activities`  VARCHAR(500) DEFAULT NULL,
  `entry_date`  DATE NOT NULL,
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_mood_patient`
    FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `uq_mood_date` (`patient_id`, `entry_date`),
  INDEX `idx_mood_date` (`patient_id`, `entry_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: wellness_goals
-- ============================================================
CREATE TABLE `wellness_goals` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `patient_id`   INT UNSIGNED NOT NULL,
  `title`        VARCHAR(300) NOT NULL,
  `description`  TEXT DEFAULT NULL,
  `category`     ENUM('mental','physical','social','spiritual','work','other') NOT NULL DEFAULT 'mental',
  `target_date`  DATE DEFAULT NULL,
  `progress`     TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '0-100 percent',
  `status`       ENUM('active','completed','paused','abandoned') NOT NULL DEFAULT 'active',
  `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_goal_patient`
    FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: wellness_resources
-- ============================================================
CREATE TABLE `wellness_resources` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title`       VARCHAR(300) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `content`     LONGTEXT DEFAULT NULL,
  `type`        ENUM('article','video','audio','exercise','worksheet') NOT NULL DEFAULT 'article',
  `category`    ENUM('anxiety','depression','stress','sleep','mindfulness','relationships','grief','trauma','general') NOT NULL DEFAULT 'general',
  `thumbnail`   VARCHAR(255) DEFAULT NULL,
  `url`         VARCHAR(500) DEFAULT NULL,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `is_active`   TINYINT(1) NOT NULL DEFAULT 1,
  `view_count`  INT UNSIGNED NOT NULL DEFAULT 0,
  `created_by`  INT UNSIGNED DEFAULT NULL,
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_resource_creator`
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  INDEX `idx_resource_cat` (`category`),
  INDEX `idx_resource_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: forum_posts
-- ============================================================
CREATE TABLE `forum_posts` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`      INT UNSIGNED NOT NULL,
  `title`        VARCHAR(400) NOT NULL,
  `content`      TEXT NOT NULL,
  `category`     VARCHAR(100) NOT NULL DEFAULT 'general',
  `is_anonymous` TINYINT(1) NOT NULL DEFAULT 0,
  `pseudonym`    VARCHAR(100) DEFAULT NULL,
  `status`       ENUM('published','pending','removed','flagged') NOT NULL DEFAULT 'published',
  `is_pinned`    TINYINT(1) NOT NULL DEFAULT 0,
  `view_count`   INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_post_user`
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX `idx_post_status` (`status`),
  INDEX `idx_post_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: forum_comments
-- ============================================================
CREATE TABLE `forum_comments` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `post_id`      INT UNSIGNED NOT NULL,
  `user_id`      INT UNSIGNED NOT NULL,
  `content`      TEXT NOT NULL,
  `is_anonymous` TINYINT(1) NOT NULL DEFAULT 0,
  `pseudonym`    VARCHAR(100) DEFAULT NULL,
  `status`       ENUM('published','pending','removed') NOT NULL DEFAULT 'published',
  `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_comment_post`
    FOREIGN KEY (`post_id`) REFERENCES `forum_posts`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_comment_user`
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX `idx_comment_post` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: feedback
-- ============================================================
CREATE TABLE `feedback` (
  `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `patient_id`     INT UNSIGNED NOT NULL,
  `therapist_id`   INT UNSIGNED NOT NULL,
  `appointment_id` INT UNSIGNED DEFAULT NULL,
  `rating`         TINYINT UNSIGNED NOT NULL COMMENT '1-5 stars',
  `comment`        TEXT DEFAULT NULL,
  `is_public`      TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_feedback_patient`
    FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_feedback_therapist`
    FOREIGN KEY (`therapist_id`) REFERENCES `therapists`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_feedback_appt`
    FOREIGN KEY (`appointment_id`) REFERENCES `appointments`(`id`) ON DELETE SET NULL,
  UNIQUE KEY `uq_feedback` (`patient_id`, `appointment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: reports (content moderation)
-- ============================================================
CREATE TABLE `reports` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `reporter_id` INT UNSIGNED NOT NULL,
  `type`        ENUM('forum_post','forum_comment','user','message') NOT NULL,
  `target_id`   INT UNSIGNED NOT NULL,
  `reason`      ENUM('spam','harassment','inappropriate','misinformation','other') NOT NULL,
  `details`     TEXT DEFAULT NULL,
  `status`      ENUM('pending','reviewed','resolved','dismissed') NOT NULL DEFAULT 'pending',
  `reviewed_by` INT UNSIGNED DEFAULT NULL,
  `reviewed_at` DATETIME DEFAULT NULL,
  `resolution`  TEXT DEFAULT NULL,
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_report_reporter`
    FOREIGN KEY (`reporter_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_report_reviewer`
    FOREIGN KEY (`reviewed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  INDEX `idx_report_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: audit_logs
-- ============================================================
CREATE TABLE `audit_logs` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`    INT UNSIGNED DEFAULT NULL,
  `action`     VARCHAR(200) NOT NULL,
  `entity`     VARCHAR(100) DEFAULT NULL,
  `entity_id`  INT UNSIGNED DEFAULT NULL,
  `details`    TEXT DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(500) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_audit_user`
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  INDEX `idx_audit_user` (`user_id`),
  INDEX `idx_audit_action` (`action`),
  INDEX `idx_audit_date` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: crisis_alerts
-- ============================================================
CREATE TABLE `crisis_alerts` (
  `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `patient_id`    INT UNSIGNED NOT NULL,
  `trigger_text`  TEXT DEFAULT NULL,
  `source`        ENUM('journal','message','forum','mood','manual') NOT NULL DEFAULT 'manual',
  `severity`      ENUM('low','medium','high','critical') NOT NULL DEFAULT 'medium',
  `status`        ENUM('new','acknowledged','in_progress','resolved') NOT NULL DEFAULT 'new',
  `responder_id`  INT UNSIGNED DEFAULT NULL,
  `response_note` TEXT DEFAULT NULL,
  `resolved_at`   DATETIME DEFAULT NULL,
  `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_crisis_patient`
    FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_crisis_responder`
    FOREIGN KEY (`responder_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  INDEX `idx_crisis_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: disputes
-- ============================================================
CREATE TABLE `disputes` (
  `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `appointment_id` INT UNSIGNED DEFAULT NULL,
  `filed_by`       INT UNSIGNED NOT NULL,
  `against`        INT UNSIGNED NOT NULL,
  `reason`         TEXT NOT NULL,
  `status`         ENUM('open','under_review','resolved','closed') NOT NULL DEFAULT 'open',
  `resolution`     TEXT DEFAULT NULL,
  `resolved_by`    INT UNSIGNED DEFAULT NULL,
  `resolved_at`    DATETIME DEFAULT NULL,
  `created_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_dispute_appt`
    FOREIGN KEY (`appointment_id`) REFERENCES `appointments`(`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_dispute_filer`
    FOREIGN KEY (`filed_by`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_dispute_against`
    FOREIGN KEY (`against`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_dispute_resolver`
    FOREIGN KEY (`resolved_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;
