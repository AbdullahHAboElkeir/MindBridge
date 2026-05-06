-- ============================================================
-- MindBridge Seed Data
-- Run AFTER mindbridge_schema.sql
-- Import in phpMyAdmin or run: mysql -u root mindbridge < mindbridge_seed.sql
-- ============================================================

USE `mindbridge`;

SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- USERS (passwords hashed with password_hash() in setup.php)
-- Use setup.php?action=reset_admin to set correct hashes
-- ============================================================

-- Admin user (password will be set by setup.php?action=reset_admin)
-- Temporary placeholder hash — MUST run setup.php?action=reset_admin after import
INSERT IGNORE INTO `users`
    (id, email, password, name, first_name, last_name, role, status, email_verified, timezone, created_at)
VALUES
-- Admin (password = Admin123@ — set via setup.php)
(1, 'admin@mindbridge.com',
 '$2y$10$JoM4JIDEVZ7d9BXTgGtPveWFH7uTgPTdll1k25b1yxhayFcRB.dY6',
 'System Admin', 'System', 'Admin', 'admin', 'active', 1, 'UTC', NOW()),

-- Therapist 1 (password = password)
(2, 'dr.sarah@mindbridge.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'Dr. Sarah Johnson', 'Sarah', 'Johnson', 'therapist', 'active', 1, 'UTC', NOW()),

-- Therapist 2 (password = password)
(3, 'dr.michael@mindbridge.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'Dr. Michael Chen', 'Michael', 'Chen', 'therapist', 'active', 1, 'UTC', NOW()),

-- Patient 1 (password = password)
(4, 'patient1@example.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'Alex Rivera', 'Alex', 'Rivera', 'patient', 'active', 1, 'UTC', NOW()),

-- Patient 2 (password = password)
(5, 'patient2@example.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'Jordan Lee', 'Jordan', 'Lee', 'patient', 'active', 1, 'UTC', NOW());

-- ============================================================
-- THERAPISTS
-- ============================================================
INSERT IGNORE INTO `therapists`
    (id, user_id, license_number, license_verified, specializations, languages, bio, years_experience, session_rate, accepts_insurance, max_patients, current_patients, rating, total_reviews, is_available)
VALUES
(1, 2, 'LIC-2024-001', 1,
 'Anxiety,Depression,Trauma,CBT',
 'English,Arabic',
 'Dr. Sarah Johnson is a licensed clinical psychologist specializing in Cognitive Behavioral Therapy (CBT) and trauma-informed care. With over 8 years of experience, she helps clients navigate anxiety, depression, and life transitions.',
 8, 120.00, 1, 20, 3, 4.80, 47, 1),

(2, 3, 'LIC-2024-002', 1,
 'Stress,Relationships,Mindfulness,Grief',
 'English,French',
 'Dr. Michael Chen brings a holistic approach to mental wellness, integrating mindfulness-based cognitive therapy with evidence-based practices. Specializes in relationship issues, grief counseling, and stress management.',
 6, 100.00, 1, 20, 2, 4.65, 32, 1);

-- ============================================================
-- PATIENTS
-- ============================================================
INSERT IGNORE INTO `patients`
    (id, user_id, preferred_language, onboarding_step, assigned_therapist, country, created_at)
VALUES
(1, 4, 'English', 4, 2, 'Egypt', NOW()),
(2, 5, 'English', 2, NULL, 'Egypt', NOW());

-- ============================================================
-- THERAPIST AVAILABILITY (Mon-Fri 9am-5pm, Sat 10am-2pm)
-- day_of_week: 0=Sun,1=Mon,2=Tue,3=Wed,4=Thu,5=Fri,6=Sat
-- ============================================================

-- Dr. Sarah (therapist_id=1)
INSERT IGNORE INTO `therapist_availability` (therapist_id, day_of_week, start_time, end_time, is_active) VALUES
(1, 1, '09:00:00', '17:00:00', 1),  -- Monday
(1, 2, '09:00:00', '17:00:00', 1),  -- Tuesday
(1, 3, '09:00:00', '17:00:00', 1),  -- Wednesday
(1, 4, '09:00:00', '17:00:00', 1),  -- Thursday
(1, 5, '09:00:00', '15:00:00', 1),  -- Friday
(1, 6, '10:00:00', '14:00:00', 1);  -- Saturday

-- Dr. Michael (therapist_id=2)
INSERT IGNORE INTO `therapist_availability` (therapist_id, day_of_week, start_time, end_time, is_active) VALUES
(2, 1, '10:00:00', '18:00:00', 1),  -- Monday
(2, 2, '10:00:00', '18:00:00', 1),  -- Tuesday
(2, 3, '10:00:00', '18:00:00', 1),  -- Wednesday
(2, 4, '10:00:00', '18:00:00', 1),  -- Thursday
(2, 5, '10:00:00', '16:00:00', 1);  -- Friday

-- ============================================================
-- WELLNESS RESOURCES
-- ============================================================
INSERT IGNORE INTO `wellness_resources`
    (id, title, description, content, type, category, is_featured, is_active, created_by, created_at)
VALUES
(1, 'Understanding Anxiety: A Beginner''s Guide',
 'Learn what anxiety is, how it affects the body and mind, and evidence-based strategies to manage it.',
 'Anxiety is one of the most common mental health experiences. This guide walks you through the science of anxiety, common triggers, and proven coping strategies including deep breathing, grounding exercises, and cognitive reframing.',
 'article', 'anxiety', 1, 1, 1, NOW()),

(2, 'Mindfulness Meditation: 5-Minute Daily Practice',
 'A simple daily mindfulness routine you can do anywhere in just 5 minutes.',
 'This guided mindfulness exercise helps you anchor to the present moment, reduce stress, and build emotional resilience. Find a quiet place, close your eyes, and follow along with this simple breath-focused meditation.',
 'exercise', 'mindfulness', 1, 1, 1, NOW()),

(3, 'Overcoming Depression: Steps Toward Recovery',
 'Practical, evidence-based steps for managing depression and rebuilding a life worth living.',
 'Depression is treatable. This resource covers behavioral activation, thought records, social connection strategies, and when to seek professional help. Remember: you don''t have to face this alone.',
 'article', 'depression', 1, 1, 1, NOW()),

(4, 'Sleep Hygiene for Mental Health',
 'How quality sleep impacts your mental wellness and 10 science-backed tips to sleep better.',
 'Sleep and mental health are deeply intertwined. Poor sleep can worsen anxiety and depression, while good sleep hygiene can dramatically improve mood and resilience. Explore our evidence-based sleep improvement strategies.',
 'article', 'sleep', 0, 1, 1, NOW()),

(5, 'Stress Management Toolkit',
 'A collection of tools and techniques for managing stress in everyday life.',
 'From time management strategies to relaxation techniques, this toolkit provides practical tools to help you identify your stress triggers, build healthy coping mechanisms, and find balance in a demanding world.',
 'worksheet', 'stress', 1, 1, 1, NOW()),

(6, 'Grief and Loss: Finding Your Way Through',
 'A compassionate guide for those navigating grief, loss, and the path toward healing.',
 'Grief is a natural response to loss. This resource explores the non-linear nature of grief, common reactions, and gentle strategies for moving forward while honoring what you''ve lost.',
 'article', 'grief', 0, 1, 1, NOW());

-- ============================================================
-- FORUM POSTS (sample community posts)
-- ============================================================
INSERT IGNORE INTO `forum_posts`
    (id, user_id, title, content, category, is_anonymous, pseudonym, status, is_pinned, view_count, created_at)
VALUES
(1, 4,
 'Feeling overwhelmed by anxiety — anyone else?',
 'Hey everyone, I''ve been struggling with anxiety a lot lately. Simple things like going to the grocery store feel impossible. My therapist is helping but I wanted to connect with others who understand. Does anyone have tips for managing anxiety in public spaces?',
 'anxiety', 0, NULL, 'published', 0, 45, DATE_SUB(NOW(), INTERVAL 3 DAY)),

(2, 5,
 'Welcome to the MindBridge Community Forum 🌱',
 'This is a safe space for all of us on our wellness journey. Please be kind, supportive, and remember that everyone here is doing their best. Share your experiences, ask questions, and know that you are not alone. The MindBridge team is here to support you.',
 'general', 0, NULL, 'published', 1, 120, DATE_SUB(NOW(), INTERVAL 7 DAY)),

(3, 4,
 'Mindfulness has been life-changing for me',
 'I was skeptical about mindfulness at first — it seemed too simple to help with serious depression. But after three months of daily practice, even just 10 minutes a day, I can genuinely say it''s made a significant difference. If you''re on the fence, I encourage you to give it a real try.',
 'mindfulness', 1, 'HopefulHeart', 'published', 0, 67, DATE_SUB(NOW(), INTERVAL 1 DAY));

-- ============================================================
-- FORUM COMMENTS
-- ============================================================
INSERT IGNORE INTO `forum_comments`
    (post_id, user_id, content, is_anonymous, status, created_at)
VALUES
(1, 5, 'Thank you for sharing this. You are definitely not alone! One thing that helps me is the 5-4-3-2-1 grounding technique — name 5 things you can see, 4 you can touch, 3 you can hear, 2 you can smell, 1 you can taste. It brings you back to the present moment immediately.', 0, 'published', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(2, 4, 'Thank you for creating this space! Already feeling supported just reading through the posts here.', 0, 'published', DATE_SUB(NOW(), INTERVAL 6 DAY)),
(3, 5, 'Completely agree! I started with just 5 minutes a day and now I can''t imagine my morning without it. The Headspace app helped me get started if anyone needs guidance.', 1, 'published', DATE_SUB(NOW(), INTERVAL 12 HOUR));

-- ============================================================
-- MOOD ENTRIES (sample data for patient 1)
-- ============================================================
INSERT IGNORE INTO `mood_entries` (patient_id, mood_level, notes, entry_date, created_at) VALUES
(1, 6, 'Feeling okay today, had therapy session', DATE_SUB(CURDATE(), INTERVAL 6 DAY), NOW()),
(1, 5, 'A bit anxious but managing', DATE_SUB(CURDATE(), INTERVAL 5 DAY), NOW()),
(1, 7, 'Good day! Went for a walk', DATE_SUB(CURDATE(), INTERVAL 4 DAY), NOW()),
(1, 4, 'Rough day at work', DATE_SUB(CURDATE(), INTERVAL 3 DAY), NOW()),
(1, 6, 'Better after talking to my therapist', DATE_SUB(CURDATE(), INTERVAL 2 DAY), NOW()),
(1, 8, 'Great day! Connected with family', DATE_SUB(CURDATE(), INTERVAL 1 DAY), NOW()),
(1, 7, 'Feeling hopeful', CURDATE(), NOW());

-- ============================================================
-- NOTIFICATIONS (welcome notification for patient)
-- ============================================================
INSERT IGNORE INTO `notifications` (user_id, type, title, message, link, is_read, created_at) VALUES
(4, 'welcome', 'Welcome to MindBridge! 🌱', 'Your account is all set. Start by completing your intake form to get matched with the right therapist.', '/patient/intake', 0, NOW()),
(4, 'match', 'Therapist Match Ready!', 'Dr. Sarah Johnson has been suggested as your therapist. View your match and book your first session.', '/patient/matching', 0, DATE_SUB(NOW(), INTERVAL 1 DAY));

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- IMPORTANT: After importing this file, visit:
-- http://localhost/MindBridge/MindBridge/setup.php?action=reset_admin
-- This will set the correct bcrypt hash for Admin123@
-- and also fix therapist/patient passwords to 'password'
-- ============================================================
