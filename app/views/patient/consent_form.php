<?php
$baseUrl = BASE_URL;
$signedTypes = $signedTypes ?? [];
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';

$consentDocs = [
    'service_agreement' => [
        'title' => 'Service Agreement',
        'icon'  => 'file-earmark-text',
        'desc'  => 'Agreement for mental health services, including session policies, fees, and cancellation terms.',
        'content' => 'This Service Agreement ("Agreement") is entered into between MindBridge and the Client. By signing, you agree to engage in mental health services as provided. Sessions are 50 minutes. A 24-hour cancellation notice is required. MindBridge reserves the right to terminate services if safety is a concern.'
    ],
    'privacy_policy' => [
        'title' => 'Privacy Policy & HIPAA',
        'icon'  => 'shield-lock',
        'desc'  => 'How we collect, use, and protect your personal health information.',
        'content' => 'Your privacy is paramount. All information shared in therapy is confidential. We comply with HIPAA regulations. Exceptions include: imminent danger to self or others, child/elder abuse, or legal requirements. Records are kept for 7 years and stored securely.'
    ],
    'telehealth_consent' => [
        'title' => 'Telehealth Consent',
        'icon'  => 'camera-video',
        'desc'  => 'Consent for receiving mental health services via video, audio, or chat.',
        'content' => 'Telehealth services are provided via secure video/audio/chat. You understand that: (1) Technology may have limitations. (2) Confidentiality protections apply. (3) You have the right to refuse telehealth. (4) Emergency protocols will be followed if safety is a concern.'
    ],
];
?>
<div class="main-content">
  <div class="page-header fade-in-up">
    <h1><i class="bi bi-file-earmark-check me-2"></i>Consent Forms</h1>
    <p>Please read and sign all required consent forms to continue your onboarding.</p>
  </div>

  <div style="max-width:780px;margin:0 auto;">
    <?php foreach ($consentDocs as $type => $doc): ?>
      <?php $isSigned = in_array($type, $signedTypes); ?>
      <div class="card mb-4 fade-in-up <?= $isSigned ? 'border border-success' : '' ?>">
        <div class="card-header d-flex align-items-center justify-content-between">
          <span><i class="bi bi-<?= $doc['icon'] ?> me-2 text-primary"></i><?= $doc['title'] ?></span>
          <?php if ($isSigned): ?>
            <span class="badge-status confirmed"><i class="bi bi-check-circle me-1"></i>Signed</span>
          <?php else: ?>
            <span class="badge-status pending">Pending Signature</span>
          <?php endif; ?>
        </div>
        <div class="card-body">
          <p class="text-muted mb-3"><?= $doc['desc'] ?></p>

          <!-- Collapsible Content -->
          <div class="mb-3">
            <button class="btn btn-sm btn-outline-primary" type="button"
                    data-bs-toggle="collapse" data-bs-target="#content-<?= $type ?>">
              <i class="bi bi-eye me-1"></i>Read Full Document
            </button>
            <div class="collapse mt-3" id="content-<?= $type ?>">
              <div class="p-3 rounded" style="background:var(--bg);font-size:.9rem;line-height:1.8;">
                <?= htmlspecialchars($doc['content']) ?>
              </div>
            </div>
          </div>

          <?php if (!$isSigned): ?>
          <form method="POST" action="<?= $baseUrl ?>/patient/submitConsent" class="needs-validation" novalidate>
            <input type="hidden" name="form_type" value="<?= $type ?>">
            <div class="mb-3">
              <label class="form-label fw-600">
                Type your full name as your electronic signature <span class="text-danger">*</span>
              </label>
              <input type="text" name="signature" class="form-control" required
                     placeholder="<?= htmlspecialchars($patient['first_name'].' '.$patient['last_name']) ?>">
              <div class="form-text">By typing your name, you agree to the terms above.</div>
            </div>
            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" id="agree-<?= $type ?>" required>
              <label class="form-check-label" for="agree-<?= $type ?>">
                I have read and agree to the <?= $doc['title'] ?>
              </label>
            </div>
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-pen me-2"></i>Sign <?= $doc['title'] ?>
            </button>
          </form>
          <?php else: ?>
            <p class="text-success fw-600 mb-0">
              <i class="bi bi-check-circle-fill me-2"></i>
              Signed on <?= date('M j, Y', strtotime(
                $forms[array_search($type, array_column($forms,'form_type'))]['signed_at'] ?? 'now'
              )) ?>
            </p>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>

    <?php if (count($signedTypes) >= 3): ?>
      <div class="alert alert-success fade-in-up">
        <i class="bi bi-check-circle-fill me-2"></i>
        All forms signed! <a href="<?= $baseUrl ?>/patient/matching" class="alert-link">Find your therapist →</a>
      </div>
    <?php endif; ?>
  </div>
</div>
<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
