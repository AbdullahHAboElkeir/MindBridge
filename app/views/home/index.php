<?php
$pageTitle = $pageTitle ?? 'MindBridge — Mental Health & Wellness Portal';
$baseUrl   = BASE_URL;
require_once BASE_PATH . '/app/views/layouts/header.php';
?>

<!-- Hero -->
<section class="hero-section">
  <div class="hero-bg-shapes"></div>
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6" data-aos="fade-right">
        <span class="hero-pill">
          <i class="bi bi-heart-pulse-fill me-2" style="color:var(--accent);"></i>
          Professional Mental Health Care
        </span>
        <h1 class="hero-title">Your Journey to <span class="text-gradient">Mental Wellness</span> Starts Here</h1>
        <p class="hero-subtitle">
          Connect with licensed therapists, track your mood, set wellness goals, and join a supportive community —
          all in one secure, compassionate platform.
        </p>
        <div class="d-flex gap-3 flex-wrap">
          <a href="<?= $baseUrl ?>/auth/register" class="btn btn-primary btn-lg px-5">
            <i class="bi bi-person-plus me-2"></i>Get Started Free
          </a>
          <a href="<?= $baseUrl ?>/auth/login" class="btn btn-outline-primary btn-lg px-5">
            <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
          </a>
        </div>
        <div class="d-flex gap-4 mt-4 flex-wrap">
          <?php foreach ([['500+','Licensed Therapists'],['10K+','Happy Patients'],['98%','Satisfaction Rate']] as [$num,$lbl]): ?>
            <div>
              <div class="fw-800" style="font-size:1.4rem;color:var(--primary);"><?= $num ?></div>
              <div class="text-muted small"><?= $lbl ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="col-lg-6 text-center" data-aos="fade-left">
        <div class="hero-illustration">
          <?php
          $cards = [
            ['bi-emoji-smile','Mood Tracker','Feeling great today! 😊','#dbeafe','#4A90E2'],
            ['bi-calendar-check','Session Booked','Dr. Sarah · Tomorrow 3PM','#d1fae5','#2AC0B5'],
            ['bi-trophy','Goal Achieved','Mindfulness streak: 7 days 🏆','#ede9fe','#7B6CF6'],
          ];
          foreach ($cards as $i => [$icon,$title,$sub,$bg,$color]):
          ?>
            <div class="hero-card"
                 style="background:<?= $bg ?>;--delay:<?= $i * 150 ?>ms;
                        transform:rotate(<?= [-3,2,-2][$i] ?>deg);">
              <i class="bi bi-<?= $icon ?>" style="color:<?= $color ?>;font-size:1.5rem;"></i>
              <div>
                <div class="fw-700 small" style="color:<?= $color ?>;"><?= $title ?></div>
                <div class="text-muted" style="font-size:.78rem;"><?= $sub ?></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Features -->
<section class="section-features">
  <div class="container">
    <div class="section-header">
      <h2>Everything You Need for <span class="text-gradient">Holistic Wellness</span></h2>
      <p>A comprehensive platform that grows with your mental health journey</p>
    </div>
    <div class="row g-4">
      <?php
      $features = [
        ['people','Therapist Matching','Our smart algorithm matches you with the perfect therapist based on your needs, preferences, and availability.','primary'],
        ['calendar-check','Session Scheduling','Book, reschedule, or cancel sessions with ease. Video, audio, or chat — you choose the format.','secondary'],
        ['emoji-smile','Mood Tracker','Log daily moods, identify patterns, and gain insights into your emotional wellbeing over time.','accent'],
        ['journal-text','Private Journal','A secure, private space to reflect on your thoughts and feelings between therapy sessions.','primary'],
        ['trophy','Wellness Goals','Set meaningful goals, track progress, and celebrate milestones on your path to better mental health.','secondary'],
        ['people-fill','Community Forum','Connect anonymously with others, share experiences, and find support in our moderated community.','accent'],
      ];
      foreach ($features as [$icon,$title,$desc,$color]):
      ?>
        <div class="col-md-6 col-xl-4">
          <div class="feature-card">
            <div class="feature-icon" style="background:var(--<?= $color ?>-light);">
              <i class="bi bi-<?= $icon ?>" style="color:var(--<?= $color ?>);font-size:1.6rem;"></i>
            </div>
            <h5 class="fw-700 mb-2"><?= $title ?></h5>
            <p class="text-muted mb-0"><?= $desc ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- How it works -->
<section style="background:var(--bg);padding:5rem 0;">
  <div class="container">
    <div class="section-header">
      <h2>How <span class="text-gradient">MindBridge</span> Works</h2>
      <p>Simple steps to start your wellness journey</p>
    </div>
    <div class="row g-4">
      <?php
      $steps = [
        ['person-plus','Create Account','Sign up and complete your intake assessment to help us understand your needs.','01'],
        ['people','Get Matched','Our algorithm suggests the best-fit therapists based on your preferences and goals.','02'],
        ['calendar-check','Book a Session','Choose your preferred therapist and schedule your first session.','03'],
        ['heart-pulse','Start Healing','Attend sessions, track your mood, journal, and grow with your support community.','04'],
      ];
      foreach ($steps as [$icon,$title,$desc,$num]):
      ?>
        <div class="col-md-6 col-xl-3 text-center">
          <div class="p-4">
            <div style="position:relative;display:inline-block;margin-bottom:1.5rem;">
              <div class="avatar avatar-xl" style="font-size:1.5rem;margin:auto;background:linear-gradient(135deg,var(--primary),var(--secondary));">
                <i class="bi bi-<?= $icon ?>"></i>
              </div>
              <span style="position:absolute;top:-8px;right:-8px;background:var(--accent);color:#fff;
                           width:24px;height:24px;border-radius:50%;font-size:.7rem;font-weight:800;
                           display:flex;align-items:center;justify-content:center;">
                <?= $num ?>
              </span>
            </div>
            <h6 class="fw-700 mb-2"><?= $title ?></h6>
            <p class="text-muted small"><?= $desc ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- CTA -->
<section style="background:linear-gradient(135deg,var(--primary),var(--secondary));padding:5rem 0;">
  <div class="container text-center">
    <h2 class="fw-800 mb-3" style="color:#fff;font-size:2.2rem;">
      Ready to Start Your Wellness Journey?
    </h2>
    <p style="color:rgba(255,255,255,.85);font-size:1.1rem;margin-bottom:2.5rem;">
      Join thousands who have already taken the first step toward better mental health.
    </p>
    <div class="d-flex gap-3 justify-content-center flex-wrap">
      <a href="<?= $baseUrl ?>/auth/register" class="btn btn-lg px-5"
         style="background:#fff;color:var(--primary);font-weight:700;border-radius:50px;">
        <i class="bi bi-person-plus me-2"></i>Get Started — It's Free
      </a>
      <a href="<?= $baseUrl ?>/auth/login" class="btn btn-lg px-5"
         style="background:rgba(255,255,255,.15);color:#fff;border:2px solid rgba(255,255,255,.5);border-radius:50px;">
        <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
      </a>
    </div>
  </div>
</section>

<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
