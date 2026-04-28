// ============================================================
// INTRANET HMP — JavaScript Principal
// ============================================================

document.addEventListener('DOMContentLoaded', () => {
  // --- Dark Mode ---
  const saved = localStorage.getItem('hmp_dark') || document.documentElement.dataset.theme;
  if (saved === 'dark') applyDark(true);

  document.querySelectorAll('.dark-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
      const isDark = document.documentElement.dataset.theme === 'dark';
      applyDark(!isDark);
      const apiBase = window.location.pathname.includes("/admin/") ? "../api" : "api"; fetch(apiBase + '/toggle_dark.php', { method: 'POST' }).catch(() => {});
    });
  });

  function applyDark(on) {
    document.documentElement.dataset.theme = on ? 'dark' : 'light';
    localStorage.setItem('hmp_dark', on ? 'dark' : 'light');
    document.querySelectorAll('.dark-toggle').forEach(b => b.classList.toggle('on', on));
  }

  // --- Mobile Menu ---
  const mobileBtn = document.getElementById('mobileMenuBtn');
  const navbarNav = document.getElementById('navbarNav');
  if (mobileBtn && navbarNav) {
    mobileBtn.addEventListener('click', () => {
      navbarNav.classList.toggle('open');
    });
  }

  // --- Admin Sidebar Toggle ---
  const sidebarBtn = document.getElementById('sidebarToggle');
  const sidebar    = document.getElementById('adminSidebar');
  if (sidebarBtn && sidebar) {
    sidebarBtn.addEventListener('click', () => sidebar.classList.toggle('open'));
    document.addEventListener('click', e => {
      if (!sidebar.contains(e.target) && !sidebarBtn.contains(e.target))
        sidebar.classList.remove('open');
    });
  }

  // --- Dropdown ---
  document.querySelectorAll('.dropdown').forEach(d => {
    d.addEventListener('click', e => {
      e.stopPropagation();
      document.querySelectorAll('.dropdown.open').forEach(o => { if (o !== d) o.classList.remove('open'); });
      d.classList.toggle('open');
    });
  });
  document.addEventListener('click', () => {
    document.querySelectorAll('.dropdown.open').forEach(d => d.classList.remove('open'));
  });

  // --- Toast ---
  window.showToast = (msg, type = 'success') => {
    let container = document.querySelector('.toast-container');
    if (!container) {
      container = document.createElement('div');
      container.className = 'toast-container';
      document.body.appendChild(container);
    }
    const icons = { success: 'check_circle', error: 'error', warning: 'warning', info: 'info' };
    const toast = document.createElement('div');
    toast.className = `toast toast-${type === 'error' ? 'error' : 'success'}`;
    toast.innerHTML = `<span class="material-icons">${icons[type] || 'info'}</span><span>${msg}</span>`;
    container.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; toast.style.transform = 'translateX(100px)'; toast.style.transition = '.3s'; setTimeout(() => toast.remove(), 300); }, 3000);
  };

  // --- Auto-dismiss alerts ---
  document.querySelectorAll('.alert[data-auto-dismiss]').forEach(alert => {
    setTimeout(() => alert.remove(), 5000);
  });

  // --- Confirm delete ---
  document.querySelectorAll('[data-confirm]').forEach(btn => {
    btn.addEventListener('click', e => {
      if (!confirm(btn.dataset.confirm || 'Tem certeza?')) e.preventDefault();
    });
  });

  // --- Rich Text Editor (simple contenteditable) ---
  const editor = document.getElementById('content-editor');
  const contentInput = document.getElementById('content-hidden');
  if (editor && contentInput) {
    editor.addEventListener('input', () => { contentInput.value = editor.innerHTML; });
    document.querySelectorAll('.editor-btn[data-cmd]').forEach(btn => {
      btn.addEventListener('click', e => {
        e.preventDefault();
        const cmd = btn.dataset.cmd;
        const val = btn.dataset.val || null;
        document.execCommand(cmd, false, val);
        editor.focus();
        contentInput.value = editor.innerHTML;
      });
    });
  }

  // --- Image preview ---
  document.querySelectorAll('[data-preview]').forEach(input => {
    input.addEventListener('change', () => {
      const file = input.files[0];
      if (!file) return;
      const target = document.getElementById(input.dataset.preview);
      if (!target) return;
      const reader = new FileReader();
      reader.onload = e => { target.src = e.target.result; target.style.display = 'block'; };
      reader.readAsDataURL(file);
    });
  });

  // --- Module drag sort (admin) ---
  initDragSort();

  // --- Search ---
  const searchInput = document.getElementById('searchInput');
  if (searchInput) {
    searchInput.addEventListener('input', debounce(() => {
      const q = searchInput.value.trim();
      if (q.length > 2) {
        window.location.href = 'index.php?page=busca&q=' + encodeURIComponent(q);
      }
    }, 600));
  }

  // --- Stats counter animation ---
  document.querySelectorAll('.stat-value[data-count]').forEach(el => {
    const target = parseInt(el.dataset.count);
    let current = 0;
    const step = Math.max(1, Math.floor(target / 40));
    const timer = setInterval(() => {
      current = Math.min(current + step, target);
      el.textContent = current.toLocaleString('pt-BR');
      if (current >= target) clearInterval(timer);
    }, 25);
  });
});

// --- Simple drag-sort for modules ---
function initDragSort() {
  const sortable = document.getElementById('sortableList');
  if (!sortable) return;
  let dragged = null;
  sortable.querySelectorAll('[draggable]').forEach(item => {
    item.addEventListener('dragstart', () => { dragged = item; item.style.opacity = '.5'; });
    item.addEventListener('dragend', () => { item.style.opacity = '1'; updateSortOrder(); });
    item.addEventListener('dragover', e => { e.preventDefault(); const r = item.getBoundingClientRect(); if (e.clientY < r.top + r.height / 2) item.before(dragged); else item.after(dragged); });
  });
  function updateSortOrder() {
    const ids = [...sortable.querySelectorAll('[data-id]')].map(i => i.dataset.id);
    const base = window.location.pathname.includes('/admin/') ? '../api/sort_modules.php' : 'api/sort_modules.php';
    fetch(base, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ ids }) });
  }
}

// --- Debounce ---
function debounce(fn, delay) {
  let t;
  return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), delay); };
}
