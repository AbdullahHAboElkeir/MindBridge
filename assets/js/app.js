/**
 * MindBridge — Main JavaScript
 * AJAX mood tracking, crisis detection, notifications, UI helpers
 */

const MB = {
  baseUrl: document.querySelector('meta[name="base-url"]')?.content || window.BASE_URL || '',

  /* ── Init ──────────────────────────────────────────────── */
  init() {
    this.initMoodTracker();
    this.initCrisisDetection();
    this.initFlashDismiss();
    this.initSidebarToggle();
    this.initTooltips();
    this.initCharts();
    this.initGoalProgress();
    this.pollNotifications();
  },

  /* ── Mood Tracker AJAX ─────────────────────────────────── */
  initMoodTracker() {
    const form = document.getElementById('moodForm');
    if (!form) return;

    const range  = document.getElementById('moodRange');
    const label  = document.getElementById('moodLabel');
    const status = document.getElementById('moodStatus');

    const labels = {
      1:'Very Low 😞', 2:'Low 😟', 3:'Somewhat Low 😕',
      4:'Below Average 😐', 5:'Average 😶', 6:'Above Average 🙂',
      7:'Good 😊', 8:'Very Good 😄', 9:'Great 😁', 10:'Excellent 🤩'
    };

    if (range) {
      range.addEventListener('input', () => {
        if (label) label.textContent = labels[range.value] || '';
      });
      // trigger on load
      range.dispatchEvent(new Event('input'));
    }

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const btn = form.querySelector('[type="submit"]');
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving…';

      try {
        const resp = await fetch(MB.baseUrl + '/wellness/storeMood', {
          method: 'POST',
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
          body: new FormData(form)
        });
        const data = await resp.json();

        if (data.success) {
          MB.showToast('Mood logged successfully! 🎉', 'success');
          if (status) status.innerHTML =
            `<div class="alert alert-success fade-in-up mt-3">Today's mood: <strong>${labels[data.mood]}</strong></div>`;
          if (data.redirect) setTimeout(() => location.href = data.redirect, 1200);
        } else {
          MB.showToast(data.message || 'Error saving mood.', 'danger');
        }
      } catch (err) {
        MB.showToast('Network error. Please try again.', 'danger');
      } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Log My Mood';
      }
    });
  },

  /* ── Crisis Keyword Detection ──────────────────────────── */
  crisisKeywords: [
    'suicide','suicidal','kill myself','end my life','want to die',
    'self harm','self-harm','hurt myself','overdose','no reason to live',
    'hopeless','worthless','can\'t go on','don\'t want to live'
  ],

  initCrisisDetection() {
    const fields = document.querySelectorAll('textarea[data-crisis-check]');
    fields.forEach(field => {
      field.addEventListener('input', () => MB.checkCrisis(field));
    });
  },

  checkCrisis(field) {
    const text = field.value.toLowerCase();
    const found = MB.crisisKeywords.some(kw => text.includes(kw));
    let banner = document.getElementById('crisisBanner');

    if (found) {
      if (!banner) {
        banner = document.createElement('div');
        banner.id = 'crisisBanner';
        banner.className = 'alert alert-danger fade-in-up mt-2';
        banner.innerHTML = `
          <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>We're here for you.</strong>
          If you're in crisis, please reach out immediately:
          <br><strong>Crisis Line: 988</strong> (Suicide & Crisis Lifeline)
          <br>Or text <strong>HOME</strong> to 741741`;
        field.parentNode.insertBefore(banner, field.nextSibling);
      }
      // Send alert to server
      MB.reportCrisis(field.value);
    } else if (banner) {
      banner.remove();
    }
  },

  async reportCrisis(text) {
    try {
      await fetch(MB.baseUrl + '/crisis/detect', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: new URLSearchParams({ trigger_text: text.substring(0, 500) })
      });
    } catch (_) {}
  },

  /* ── Notification Polling ──────────────────────────────── */
  pollNotifications() {
    const badge = document.getElementById('notifCount');
    if (!badge) return;

    const check = async () => {
      try {
        const resp = await fetch(MB.baseUrl + '/notifications/count', {
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await resp.json();
        if (data.count > 0) {
          badge.textContent = data.count;
          badge.style.display = 'inline-block';
        } else {
          badge.style.display = 'none';
        }
      } catch (_) {}
    };

    check();
    setInterval(check, 30000); // every 30s
  },

  /* ── Flash Message Dismiss ─────────────────────────────── */
  initFlashDismiss() {
    document.querySelectorAll('.alert-dismissible').forEach(alert => {
      setTimeout(() => {
        alert.style.transition = 'opacity .5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
      }, 4000);
    });
  },

  /* ── Sidebar Toggle (mobile) ───────────────────────────── */
  initSidebarToggle() {
    const toggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    if (!toggle || !sidebar) return;

    toggle.addEventListener('click', () => sidebar.classList.toggle('open'));

    // Close on outside click
    document.addEventListener('click', (e) => {
      if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
        sidebar.classList.remove('open');
      }
    });
  },

  /* ── Bootstrap Tooltips ────────────────────────────────── */
  initTooltips() {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
      new bootstrap.Tooltip(el);
    });
  },

  /* ── Charts (Chart.js) ─────────────────────────────────── */
  initCharts() {
    // Mood chart
    const moodCtx = document.getElementById('moodChart');
    if (moodCtx && window.moodData) {
      new Chart(moodCtx, {
        type: 'line',
        data: {
          labels: window.moodData.labels,
          datasets: [{
            label: 'Mood Level',
            data: window.moodData.values,
            borderColor: '#4A90D9',
            backgroundColor: 'rgba(74,144,217,.1)',
            tension: .4,
            fill: true,
            pointBackgroundColor: '#4A90D9',
            pointRadius: 5
          }]
        },
        options: {
          responsive: true,
          plugins: { legend: { display: false } },
          scales: {
            y: { min: 1, max: 10, grid: { color: '#E2E8F0' } },
            x: { grid: { display: false } }
          }
        }
      });
    }

    // Sessions bar chart
    const sessCtx = document.getElementById('sessionsChart');
    if (sessCtx && window.sessionsData) {
      new Chart(sessCtx, {
        type: 'bar',
        data: {
          labels: window.sessionsData.labels,
          datasets: [{
            label: 'Sessions',
            data: window.sessionsData.values,
            backgroundColor: 'rgba(74,144,217,.7)',
            borderRadius: 6
          }]
        },
        options: {
          responsive: true,
          plugins: { legend: { display: false } },
          scales: { y: { beginAtZero: true, grid: { color: '#E2E8F0' } }, x: { grid: { display: false } } }
        }
      });
    }
  },

  /* ── Goal Progress AJAX ────────────────────────────────── */
  initGoalProgress() {
    document.querySelectorAll('.goal-progress-input').forEach(input => {
      input.addEventListener('change', async function () {
        const goalId = this.dataset.goalId;
        const progress = this.value;
        try {
          const resp = await fetch(MB.baseUrl + '/wellness/updateGoalProgress', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: new URLSearchParams({ goal_id: goalId, progress })
          });
          const data = await resp.json();
          if (data.success) {
            const bar = document.querySelector(`#goal-bar-${goalId}`);
            if (bar) { bar.style.width = progress + '%'; bar.textContent = progress + '%'; }
            MB.showToast('Goal progress updated!', 'success');
          }
        } catch (_) { MB.showToast('Error updating goal.', 'danger'); }
      });
    });
  },

  /* ── Toast Notification ────────────────────────────────── */
  showToast(message, type = 'success') {
    let container = document.getElementById('toastContainer');
    if (!container) {
      container = document.createElement('div');
      container.id = 'toastContainer';
      container.style.cssText = 'position:fixed;top:1rem;right:1rem;z-index:9999;';
      document.body.appendChild(container);
    }

    const icons = { success: 'check-circle-fill', danger: 'x-circle-fill', warning: 'exclamation-triangle-fill', info: 'info-circle-fill' };
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} d-flex align-items-center gap-2 shadow mb-2 fade-in-up`;
    toast.style.cssText = 'min-width:280px;max-width:360px;';
    toast.innerHTML = `<i class="bi bi-${icons[type] || 'info-circle-fill'}"></i><span>${message}</span>`;
    container.appendChild(toast);

    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transition = 'opacity .5s';
      setTimeout(() => toast.remove(), 500);
    }, 3500);
  },

  /* ── Confirm Dialog ────────────────────────────────────── */
  confirm(message, callback) {
    if (window.confirm(message)) callback();
  }
};

// Auto-init on DOM ready
document.addEventListener('DOMContentLoaded', () => MB.init());

/* ── Form validation helper ───────────────────────────────── */
document.querySelectorAll('form.needs-validation')?.forEach(form => {
  form.addEventListener('submit', e => {
    if (!form.checkValidity()) {
      e.preventDefault();
      e.stopPropagation();
    }
    form.classList.add('was-validated');
  });
});
