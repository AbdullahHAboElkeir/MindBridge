<?php
$baseUrl      = BASE_URL;
$activeUserId = $activeUserId ?? 0;
$activeUser   = $activeUser ?? null;
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/sidebar.php';
$myId = Session::userId();
?>
<div class="main-content">
  <div class="page-header fade-in-up">
    <h1><i class="bi bi-chat-dots me-2"></i>Messages</h1>
    <p>Secure communication with your care team</p>
  </div>

  <div class="card fade-in-up" style="min-height:520px;">
    <div class="row g-0" style="min-height:520px;">

      <!-- Sidebar: conversations + contacts -->
      <div class="col-md-4" style="border-right:1px solid var(--border);">
        <!-- New message contact picker -->
        <div class="p-3 border-bottom">
          <select id="contactPicker" class="form-select form-select-sm"
                  onchange="if(this.value) window.location='<?= $baseUrl ?>/messages?with='+this.value">
            <option value="">✉ New Message — Select contact</option>
            <?php foreach ($contacts as $c): ?>
              <option value="<?= $c['id'] ?>"
                <?= $activeUserId === (int)$c['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['first_name'].' '.$c['last_name']) ?> (<?= ucfirst($c['role']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Conversations list -->
        <div style="overflow-y:auto;max-height:430px;">
          <?php if (empty($conversations)): ?>
            <p class="text-center text-muted py-4 small">No conversations yet.</p>
          <?php else: ?>
            <?php foreach ($conversations as $conv): ?>
              <?php $otherId = $conv['other_user_id']; ?>
              <a href="<?= $baseUrl ?>/messages?with=<?= $otherId ?>"
                 class="d-flex gap-3 p-3 border-bottom text-decoration-none
                        <?= $activeUserId === (int)$otherId ? 'bg-primary-light' : '' ?>"
                 style="transition:var(--transition);">
                <div class="avatar flex-shrink-0">
                  <?= strtoupper(substr($conv['first_name'],0,1)) ?>
                </div>
                <div class="flex-grow-1 overflow-hidden">
                  <div class="d-flex justify-content-between">
                    <span class="fw-600 small" style="color:var(--text);">
                      <?= htmlspecialchars($conv['first_name'].' '.$conv['last_name']) ?>
                    </span>
                    <span class="text-muted" style="font-size:.72rem;">
                      <?= date('M j', strtotime($conv['created_at'])) ?>
                    </span>
                  </div>
                  <div class="text-muted small text-truncate">
                    <?= htmlspecialchars(substr($conv['content'],0,50)) ?>
                  </div>
                </div>
                <?php if ($conv['unread_count'] > 0): ?>
                  <span class="badge bg-primary rounded-pill align-self-center"><?= $conv['unread_count'] ?></span>
                <?php endif; ?>
              </a>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

      <!-- Chat window -->
      <div class="col-md-8 d-flex flex-column">
        <?php if ($activeUser): ?>
          <!-- Header -->
          <div class="p-3 border-bottom d-flex align-items-center gap-3">
            <div class="avatar"><?= strtoupper(substr($activeUser['first_name'],0,1)) ?></div>
            <div>
              <div class="fw-700"><?= htmlspecialchars($activeUser['first_name'].' '.$activeUser['last_name']) ?></div>
              <div class="text-muted small"><?= ucfirst($activeUser['role']) ?></div>
            </div>
          </div>

          <!-- Messages -->
          <div class="chat-window flex-grow-1" id="chatWindow">
            <?php foreach ($thread as $msg): ?>
              <div class="d-flex <?= (int)$msg['sender_id'] === $myId ? 'justify-content-end' : '' ?> mb-2">
                <div class="chat-bubble <?= (int)$msg['sender_id'] === $myId ? 'sent' : 'received' ?>">
                  <?= nl2br(htmlspecialchars($msg['content'])) ?>
                  <div style="font-size:.7rem;opacity:.7;margin-top:4px;text-align:right;">
                    <?= date('g:i A', strtotime($msg['created_at'])) ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

          <!-- Send form -->
          <div class="p-3 border-top">
            <form method="POST" action="<?= $baseUrl ?>/messages/send" class="d-flex gap-2">
              <input type="hidden" name="receiver_id" value="<?= $activeUser['id'] ?>">
              <textarea name="content" class="form-control" rows="1" required
                        placeholder="Type a message…" data-crisis-check
                        style="resize:none;border-radius:20px;padding:.5rem 1rem;"></textarea>
              <button type="submit" class="btn btn-primary rounded-circle"
                      style="width:44px;height:44px;padding:0;flex-shrink:0;">
                <i class="bi bi-send-fill"></i>
              </button>
            </form>
          </div>
        <?php else: ?>
          <div class="flex-grow-1 d-flex flex-column align-items-center justify-content-center text-muted">
            <i class="bi bi-chat-dots fs-1 mb-3 opacity-40"></i>
            <h6>Select a conversation</h6>
            <p class="small">Or start a new message above</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<script>
// Scroll to bottom of chat
const cw = document.getElementById('chatWindow');
if (cw) cw.scrollTop = cw.scrollHeight;
</script>
<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
