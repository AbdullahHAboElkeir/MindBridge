-- ============================================================
-- MindBridge Seed Data — Corrected & Complete
-- Run AFTER mindbridge_schema.sql
-- ============================================================
USE `mindbridge`;

-- Disable FK checks for clean inserts
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- USERS
-- Admin password: Admin123@
-- All others password: password (hash below)
-- ============================================================
INSERT INTO `users`
    (`id`,`email`,`password`,`name`,`first_name`,`last_name`,`role`,`status`,`gender`,`phone`,`timezone`,`email_verified`)
VALUES
-- Admin: password = Admin123@
(1,'admin@mindbridge.com',
 '$2y$10$44kjhC6IB7RnEwwbjsBV8./ySF9Ps1v5UH59.ziuDgEaQyg3i8kVS',
 'System Admin','System','Admin','admin','active','prefer_not','+1-000-000-0001','UTC',1),
-- Therapists: password = password
(2,'dr.sarah@mindbridge.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'Sarah Mitchell','Sarah','Mitchell','therapist','active','female','+1-555-101-0001','America/New_York',1),
(3,'dr.james@mindbridge.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'James Carter','James','Carter','therapist','active','male','+1-555-101-0002','America/Chicago',1),
(4,'dr.aisha@mindbridge.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'Aisha Rahman','Aisha','Rahman','therapist','active','female','+1-555-101-0003','America/Los_Angeles',1),
(5,'dr.mark@mindbridge.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'Mark Johnson','Mark','Johnson','therapist','active','male','+1-555-101-0004','Europe/London',1),
(6,'dr.lena@mindbridge.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'Lena Hoffmann','Lena','Hoffmann','therapist','active','female','+1-555-101-0005','Europe/Berlin',1),
-- Patients: password = password
(7,'patient1@example.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'Alex Johnson','Alex','Johnson','patient','active','male','+1-555-200-0001','America/New_York',1),
(8,'patient2@example.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'Maria Garcia','Maria','Garcia','patient','active','female','+1-555-200-0002','America/Chicago',1),
(9,'patient3@example.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'David Lee','David','Lee','patient','active','male','+1-555-200-0003','America/Los_Angeles',1),
(10,'patient4@example.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'Emma Williams','Emma','Williams','patient','active','female','+1-555-200-0004','Europe/London',1),
(11,'patient5@example.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'Omar Hassan','Omar','Hassan','patient','active','male','+1-555-200-0005','Africa/Cairo',1)
ON DUPLICATE KEY UPDATE
    password = VALUES(password),
    status   = VALUES(status),
    role     = VALUES(role);

-- ============================================================
-- THERAPISTS
-- ============================================================
INSERT IGNORE INTO `therapists`
    (`id`,`user_id`,`license_number`,`license_verified`,`specializations`,`languages`,
     `bio`,`years_experience`,`session_rate`,`accepts_insurance`,`max_patients`,
     `current_patients`,`rating`,`total_reviews`,`is_available`)
VALUES
(1,2,'LIC-2001-NY',1,'Anxiety,Depression,Trauma,CBT','English,French',
 'Dr. Sarah Mitchell is a licensed psychologist specializing in CBT with over 10 years of experience.',
 10,120.00,1,20,3,4.80,24,1),
(2,3,'LIC-2002-IL',1,'Depression,Grief,Relationships,DBT','English',
 'Dr. James Carter brings warmth and evidence-based approaches to help clients navigate depression.',
 8,100.00,1,20,2,4.60,18,1),
(3,4,'LIC-2003-CA',1,'Trauma,PTSD,Mindfulness,ACT','English,Arabic,Urdu',
 'Dr. Aisha Rahman specializes in trauma recovery and mindfulness-based therapies.',
 12,130.00,0,15,2,4.90,31,1),
(4,5,'LIC-2004-UK',1,'Anxiety,OCD,Phobias,ERP','English',
 'Dr. Mark Johnson is an expert in exposure and response prevention therapy.',
 9,110.00,1,20,1,4.70,15,1),
(5,6,'LIC-2005-DE',1,'Stress,Burnout,Work-Life,MBSR','English,German',
 'Dr. Lena Hoffmann specializes in mindfulness-based stress reduction and burnout recovery.',
 7,95.00,0,20,2,4.50,12,1);

-- ============================================================
-- THERAPIST AVAILABILITY
-- day_of_week: 0=Sun, 1=Mon, 2=Tue, 3=Wed, 4=Thu, 5=Fri, 6=Sat
-- ============================================================
INSERT IGNORE INTO `therapist_availability`
    (`therapist_id`,`day_of_week`,`start_time`,`end_time`,`is_active`)
VALUES
-- Dr. Sarah: Mon–Thu 9am–5pm
(1,1,'09:00:00','17:00:00',1),(1,2,'09:00:00','17:00:00',1),
(1,3,'09:00:00','17:00:00',1),(1,4,'09:00:00','17:00:00',1),
-- Dr. James: Mon, Tue, Thu, Fri
(2,1,'10:00:00','18:00:00',1),(2,2,'10:00:00','18:00:00',1),
(2,4,'10:00:00','18:00:00',1),(2,5,'10:00:00','14:00:00',1),
-- Dr. Aisha: Mon, Wed, Fri
(3,1,'08:00:00','16:00:00',1),(3,3,'08:00:00','16:00:00',1),(3,5,'08:00:00','16:00:00',1),
-- Dr. Mark: Tue, Wed, Thu
(4,2,'11:00:00','19:00:00',1),(4,3,'11:00:00','19:00:00',1),(4,4,'11:00:00','19:00:00',1),
-- Dr. Lena: Mon, Tue, Fri
(5,1,'09:00:00','15:00:00',1),(5,2,'09:00:00','15:00:00',1),(5,5,'09:00:00','13:00:00',1);

-- ============================================================
-- PATIENTS
-- ============================================================
INSERT IGNORE INTO `patients`
    (`id`,`user_id`,`insurance_provider`,`insurance_number`,`insurance_verified`,
     `emergency_contact`,`emergency_phone`,`preferred_language`,`onboarding_step`,`assigned_therapist`)
VALUES
(1,7,'BlueCross','BC-123456',1,'Jane Johnson','+1-555-200-9001','English',4,2),
(2,8,'Aetna','AE-789012',1,'Carlos Garcia','+1-555-200-9002','English,Spanish',4,3),
(3,9,NULL,NULL,0,NULL,NULL,'English',2,NULL),
(4,10,'NHS','NHS-345678',1,'Tom Williams','+1-555-200-9004','English',4,4),
(5,11,NULL,NULL,0,NULL,NULL,'Arabic,English',1,NULL);

-- ============================================================
-- INTAKE FORMS
-- ============================================================
INSERT IGNORE INTO `intake_forms`
    (`patient_id`,`primary_concerns`,`mental_health_history`,`previous_therapy`,
     `therapist_gender_pref`,`preferred_language`,`session_format_pref`,
     `urgency_level`,`goals`,`status`,`submitted_at`)
VALUES
(1,'Anxiety, work stress, panic attacks','Family history of anxiety disorders',1,
 'female','English','video','medium',
 'Manage anxiety, improve work-life balance, develop coping strategies',
 'reviewed','2026-01-10 10:00:00'),
(2,'Depression, low energy, sleep issues','Diagnosed with MDD in 2022',1,
 'no_preference','English','video','high',
 'Reduce depressive symptoms, improve sleep, reconnect with life',
 'reviewed','2026-01-15 14:00:00'),
(3,'Grief after loss, loneliness','No prior mental health history',0,
 'male','English','video','medium',
 'Process grief, build connections, find meaning',
 'submitted','2026-03-01 09:00:00'),
(4,'Social anxiety, fear of judgment','Mild anxiety in university',0,
 'female','English','audio','low',
 'Build confidence, manage social situations, reduce avoidance',
 'reviewed','2026-02-01 11:00:00');

-- ============================================================
-- CONSENT FORMS
-- ============================================================
INSERT IGNORE INTO `consent_forms`
    (`patient_id`,`form_type`,`is_signed`,`signature`,`ip_address`,`signed_at`)
VALUES
(1,'service_agreement',1,'Alex Johnson','127.0.0.1','2026-01-10 10:05:00'),
(1,'privacy_policy',1,'Alex Johnson','127.0.0.1','2026-01-10 10:06:00'),
(1,'telehealth_consent',1,'Alex Johnson','127.0.0.1','2026-01-10 10:07:00'),
(2,'service_agreement',1,'Maria Garcia','127.0.0.1','2026-01-15 14:05:00'),
(2,'privacy_policy',1,'Maria Garcia','127.0.0.1','2026-01-15 14:06:00'),
(2,'telehealth_consent',1,'Maria Garcia','127.0.0.1','2026-01-15 14:07:00'),
(4,'service_agreement',1,'Emma Williams','127.0.0.1','2026-02-01 11:05:00'),
(4,'privacy_policy',1,'Emma Williams','127.0.0.1','2026-02-01 11:06:00'),
(4,'telehealth_consent',1,'Emma Williams','127.0.0.1','2026-02-01 11:07:00');

-- ============================================================
-- THERAPIST MATCHES
-- ============================================================
INSERT IGNORE INTO `therapist_matches`
    (`patient_id`,`therapist_id`,`match_score`,`match_reasons`,`status`,`suggested_at`,`responded_at`)
VALUES
(1,1,92.50,'["specialization_match","language_match","gender_preference","availability_match"]',
 'accepted','2026-01-11 09:00:00','2026-01-11 12:00:00'),
(1,3,85.00,'["specialization_match","language_match","availability_match"]',
 'suggested','2026-01-11 09:00:00',NULL),
(2,2,88.00,'["specialization_match","language_match","availability_match"]',
 'accepted','2026-01-16 09:00:00','2026-01-16 15:00:00'),
(4,4,91.00,'["specialization_match","availability_match","session_format_match"]',
 'accepted','2026-02-02 09:00:00','2026-02-02 14:00:00');

-- ============================================================
-- APPOINTMENTS
-- ============================================================
INSERT IGNORE INTO `appointments`
    (`id`,`patient_id`,`therapist_id`,`scheduled_at`,`duration_minutes`,`type`,`status`)
VALUES
(1,1,1,'2026-04-01 10:00:00',50,'video','completed'),
(2,1,1,'2026-04-08 10:00:00',50,'video','completed'),
(3,1,1,'2026-04-15 10:00:00',50,'video','completed'),
(4,2,2,'2026-04-02 11:00:00',50,'video','completed'),
(5,2,2,'2026-04-09 11:00:00',50,'video','completed'),
(6,4,4,'2026-04-03 14:00:00',50,'audio','completed'),
(7,1,1,'2026-05-20 10:00:00',50,'video','scheduled'),
(8,2,2,'2026-05-21 11:00:00',50,'video','scheduled'),
(9,4,4,'2026-05-22 14:00:00',50,'audio','scheduled');

-- ============================================================
-- SESSIONS
-- ============================================================
INSERT IGNORE INTO `sessions`
    (`appointment_id`,`therapist_notes`,`mood_before`,`mood_after`,
     `duration_actual`,`outcome`,`started_at`,`ended_at`)
VALUES
(1,'Patient showed significant anxiety. Introduced breathing techniques. Good progress.',
 4,6,52,'good','2026-04-01 10:00:00','2026-04-01 10:52:00'),
(2,'Continued CBT exercises. Patient reports improved sleep. Assigned thought journal.',
 5,7,50,'good','2026-04-08 10:00:00','2026-04-08 10:50:00'),
(3,'Patient managing panic attacks better. Discussed workplace boundaries.',
 6,8,49,'good','2026-04-15 10:00:00','2026-04-15 10:49:00'),
(4,'Patient presenting with severe depressive episodes. Started BAS.',
 3,5,53,'neutral','2026-04-02 11:00:00','2026-04-02 11:53:00'),
(5,'Noticeable improvement in mood. Sleep is better. Continuing DBT skills.',
 5,6,50,'good','2026-04-09 11:00:00','2026-04-09 11:50:00'),
(6,'Social anxiety explored. Gradual exposure plan created.',
 4,6,51,'good','2026-04-03 14:00:00','2026-04-03 14:51:00');

-- ============================================================
-- MOOD ENTRIES
-- ============================================================
INSERT IGNORE INTO `mood_entries`
    (`patient_id`,`mood_level`,`notes`,`triggers`,`entry_date`)
VALUES
(1,4,'Feeling anxious about work deadline','Work pressure','2026-04-28'),
(1,5,'A bit better after morning walk','Exercise helped','2026-04-29'),
(1,6,'Had a good therapy session','Therapy session','2026-04-30'),
(1,7,'Productive day, feeling positive','Good sleep','2026-05-01'),
(1,6,'Some anxiety but manageable','Social event','2026-05-02'),
(2,3,'Very low today, hard to get out of bed','Poor sleep','2026-04-28'),
(2,4,'Slightly better, took medication','Medication','2026-04-29'),
(2,5,'Talked to a friend, helped a lot','Social support','2026-04-30'),
(2,4,'Anxious about appointment','Upcoming appointment','2026-05-01'),
(2,6,'Good session with Dr. Carter','Therapy session','2026-05-02');

-- ============================================================
-- JOURNALS
-- ============================================================
INSERT IGNORE INTO `journals`
    (`patient_id`,`title`,`content`,`mood_tag`,`is_private`)
VALUES
(1,'My anxiety journey',
 'Today I realized that my anxiety peaks in the morning before work. I have been practicing the breathing exercises Dr. Mitchell taught me and they really help. I feel more in control now.',
 'hopeful',1),
(1,'Progress update',
 'Week 3 of therapy and I can already feel improvements. The CBT techniques are helping me challenge my negative thoughts. Still struggling with panic attacks but they are less frequent.',
 'grateful',1),
(2,'A hard day',
 'Today was very difficult. I could not get out of bed until noon. Depression feels like a heavy blanket. My therapist says these bad days are part of the process. Trying to hold on.',
 'struggling',1),
(2,'Small victories',
 'I cooked a meal today. It sounds small but when you have depression, making yourself eat is an achievement. Dr. Carter would be proud. Taking it one day at a time.',
 'hopeful',1);

-- ============================================================
-- WELLNESS GOALS
-- ============================================================
INSERT IGNORE INTO `wellness_goals`
    (`patient_id`,`title`,`description`,`category`,`target_date`,`progress`,`status`)
VALUES
(1,'Practice daily mindfulness','Meditate for 10 minutes every morning','mental','2026-06-30',60,'active'),
(1,'Improve sleep schedule','Go to bed by 11pm and wake up at 7am consistently','physical','2026-05-31',40,'active'),
(1,'Reduce caffeine intake','Cut down to 1 cup of coffee per day','physical','2026-05-15',80,'active'),
(2,'Exercise three times a week','Walk or gym 3x per week to boost mood','physical','2026-06-30',30,'active'),
(2,'Journaling practice','Write in journal every evening','mental','2026-12-31',70,'active');

-- ============================================================
-- WELLNESS RESOURCES
-- ============================================================
INSERT IGNORE INTO `wellness_resources`
    (`title`,`description`,`type`,`category`,`is_featured`,`is_active`,`created_by`)
VALUES
('Understanding Anxiety: A Guide',
 'Learn about anxiety disorders, their causes, and evidence-based treatments.',
 'article','anxiety',1,1,1),
('5-Minute Breathing Exercise',
 'A simple diaphragmatic breathing technique to calm anxiety in minutes.',
 'exercise','anxiety',1,1,1),
('Managing Depression Daily',
 'Practical strategies for managing depression including activity scheduling and self-care.',
 'article','depression',1,1,1),
('Mindfulness for Beginners',
 'Start your mindfulness journey with this guided introduction.',
 'audio','mindfulness',0,1,1),
('Sleep Hygiene Worksheet',
 'Track and improve your sleep patterns with this evidence-based assessment.',
 'worksheet','sleep',0,1,1),
('Stress Less: Understanding Your Triggers',
 'Identify your personal stress triggers and develop customized coping strategies.',
 'article','stress',1,1,1),
('Grief and Loss: Finding Your Way',
 'A compassionate guide through the stages of grief with practical coping tools.',
 'article','grief',0,1,1),
('Progressive Muscle Relaxation',
 'A guided audio exercise for releasing physical tension associated with stress.',
 'audio','stress',0,1,1),
('Relationships and Mental Health',
 'How to maintain healthy relationships while managing your mental health journey.',
 'article','relationships',0,1,1),
('Trauma-Informed Self-Care',
 'Understanding trauma responses and building a personalized self-care toolkit.',
 'article','trauma',0,1,1);

-- ============================================================
-- FORUM POSTS
-- ============================================================
INSERT IGNORE INTO `forum_posts`
    (`user_id`,`title`,`content`,`category`,`is_anonymous`,`pseudonym`,`status`,`is_pinned`)
VALUES
(7,'Does anyone else struggle with morning anxiety?',
 'Every morning I wake up with this overwhelming sense of dread before I even remember what I have to do that day. My therapist says it is called morning anxiety and it is very common. Anyone else experience this? What helps you?',
 'anxiety',0,NULL,'published',0),
(8,'How therapy changed my relationship with depression',
 'I was skeptical about therapy at first. I thought talking about my problems could not possibly help me feel better. But after 3 months of working with my therapist, I can honestly say it is the best decision I ever made.',
 'depression',0,NULL,'published',1),
(9,'Scared to start therapy',
 'I have been struggling for months but I am terrified to start therapy. What if the therapist judges me? What if sharing makes it worse? Has anyone felt this way before starting?',
 'general',1,'WorriedHeart','published',0),
(10,'Coping with social anxiety at work',
 'My social anxiety makes professional settings really hard. I struggle with presentations, one-on-one meetings, and even casual conversations in the break room. Looking for tips.',
 'anxiety',0,NULL,'published',0),
(7,'30 days of mindfulness - my experience',
 'I committed to 30 days of daily mindfulness meditation and here is what happened. Days 1-7 were hard. By day 14 I noticed I was less reactive to stress. Day 30: I feel genuinely calmer.',
 'mindfulness',0,NULL,'published',0);

-- ============================================================
-- FORUM COMMENTS
-- ============================================================
INSERT IGNORE INTO `forum_comments`
    (`post_id`,`user_id`,`content`,`is_anonymous`,`pseudonym`,`status`)
VALUES
(1,8,'Yes! Morning anxiety is so real. What helps me is NOT checking my phone first thing. I do 5 minutes of breathing before I even get out of bed.',0,NULL,'published'),
(1,9,'I struggle with this too. My therapist suggested keeping a notepad by the bed to write down worries so my brain does not have to hold onto them overnight.',0,NULL,'published'),
(2,7,'This gives me so much hope. I have been avoiding therapy for years. Reading your story makes me want to try.',0,NULL,'published'),
(3,8,'I felt the exact same way before my first session. My therapist was so non-judgmental. The fear is normal but it gets so much better once you start.',0,NULL,'published'),
(4,7,'Workplace anxiety is tough. I find that preparing talking points before meetings really helps me feel less anxious.',0,NULL,'published');

-- ============================================================
-- FEEDBACK
-- ============================================================
INSERT IGNORE INTO `feedback`
    (`patient_id`,`therapist_id`,`appointment_id`,`rating`,`comment`,`is_public`)
VALUES
(1,1,1,5,'Dr. Mitchell is absolutely wonderful. She creates such a safe space and her CBT techniques have genuinely changed my life.',1),
(1,1,2,5,'Another great session. I always leave feeling more empowered and equipped to handle my anxiety.',1),
(2,2,4,4,'Dr. Carter is very empathetic and professional. Still early days but I feel heard and understood.',1),
(4,4,6,5,'Dr. Johnson really understands social anxiety. The gradual exposure approach is working so well for me.',1);

-- ============================================================
-- PAYMENTS
-- ============================================================
INSERT IGNORE INTO `payments`
    (`appointment_id`,`patient_id`,`amount`,`currency`,`method`,`status`,`transaction_ref`,`paid_at`)
VALUES
(1,1,120.00,'USD','credit_card','paid','TXN-2026-0401-001','2026-04-01 09:45:00'),
(2,1,120.00,'USD','credit_card','paid','TXN-2026-0408-001','2026-04-08 09:45:00'),
(3,1,120.00,'USD','credit_card','paid','TXN-2026-0415-001','2026-04-15 09:45:00'),
(4,2,100.00,'USD','insurance','paid','INS-2026-0402-001','2026-04-02 10:45:00'),
(5,2,100.00,'USD','insurance','paid','INS-2026-0409-001','2026-04-09 10:45:00'),
(6,4,110.00,'USD','credit_card','paid','TXN-2026-0403-001','2026-04-03 13:45:00'),
(7,1,120.00,'USD','credit_card','pending',NULL,NULL),
(8,2,100.00,'USD','insurance','pending',NULL,NULL),
(9,4,110.00,'USD','credit_card','pending',NULL,NULL);

-- ============================================================
-- NOTIFICATIONS
-- ============================================================
INSERT IGNORE INTO `notifications`
    (`user_id`,`type`,`title`,`message`,`link`,`is_read`)
VALUES
(7,'appointment_reminder','Upcoming Session Tomorrow',
 'Your session with Dr. Sarah Mitchell is soon.','/appointments','0'),
(7,'payment_due','Payment Pending',
 'Your payment for the upcoming session is pending.','/payments','0'),
(8,'appointment_reminder','Upcoming Session',
 'Your session with Dr. James Carter is coming up.','/appointments','1'),
(2,'new_patient','New Patient Assigned',
 'You have a new patient: Alex Johnson.','/therapist/patients','1'),
(1,'new_report','New Content Report',
 'A forum post has been reported and requires review.','/admin/reports','0');

-- ============================================================
-- MESSAGES
-- ============================================================
INSERT IGNORE INTO `messages`
    (`sender_id`,`receiver_id`,`subject`,`content`,`is_read`,`read_at`)
VALUES
(7,2,'Question about homework',
 'Hi Dr. Mitchell, I wanted to ask about the thought journal exercise. Should I do it before or after breakfast?',
 1,'2026-04-16 09:00:00'),
(2,7,'Re: Question about homework',
 'Hi Alex! Great that you are engaging with the exercises. I recommend right after breakfast when you are settled. The key is consistency rather than timing.',
 0,NULL),
(8,3,'Feeling worried',
 'Hi Dr. Carter, I have been having a rough week. More low days than usual. Should I be concerned?',
 1,'2026-04-10 11:00:00'),
(3,8,'Re: Feeling worried',
 'Hi Maria, thank you for reaching out. It is completely normal to have rough patches. Let us discuss this at our next session. In the meantime, try the mood tracker.',
 0,NULL);

-- ============================================================
-- CRISIS ALERTS (sample)
-- ============================================================
INSERT IGNORE INTO `crisis_alerts`
    (`patient_id`,`trigger_text`,`source`,`severity`,`status`,`created_at`)
VALUES
(2,'Sample detected phrase','journal','medium','resolved','2026-04-05 08:00:00');

-- ============================================================
-- AUDIT LOGS
-- ============================================================
INSERT IGNORE INTO `audit_logs`
    (`user_id`,`action`,`entity`,`entity_id`,`details`,`ip_address`)
VALUES
(1,'login','users',1,'Admin login successful','127.0.0.1'),
(7,'login','users',7,'Patient login successful','127.0.0.1'),
(7,'submit_intake','intake_forms',1,'Intake form submitted','127.0.0.1'),
(7,'sign_consent','consent_forms',1,'Service agreement signed','127.0.0.1'),
(7,'book_appointment','appointments',7,'Appointment booked with Dr. Mitchell','127.0.0.1');

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- NOTE ON PASSWORDS
-- Admin    (admin@mindbridge.com)     → Admin123@
-- All others                          → password
--
-- If admin login fails, visit:
--   http://localhost/MindBridge/MindBridge/setup.php?action=reset_admin
-- ============================================================
