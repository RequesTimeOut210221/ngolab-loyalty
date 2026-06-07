<?php if (!defined('IN_ADMIN')) exit('No direct script access allowed'); ?>
  <style>
    :root {
      --sidebar-bg: #0F172A;
      --sidebar-text: #E2E8F0;
      --panel-bg: #FFFFFF;
      --panel-border: rgba(148, 163, 184, 0.16);
      --shadow: 0 18px 55px rgba(15, 23, 42, 0.08);
      --text-dark: #0F172A;
      --text-muted: #64748B;
    }

    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      min-height: 100vh;
      font-family: 'Plus Jakarta Sans', sans-serif;
      background: #E5E7EB;
      color: var(--text-dark);
    }

    .admin-shell {
      display: block;
      min-height: 100vh;
    }

    .sidebar {
      background: linear-gradient(180deg, #0F172A 0%, #111827 100%);
      color: var(--sidebar-text);
      padding: 32px 24px;
      display: flex;
      flex-direction: column;
      gap: 32px;
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .brand-logo {
      width: 44px;
      height: 44px;
      border-radius: 14px;
      background: radial-gradient(circle at top left, #F97316 0%, #FB923C 82%);
      display: grid;
      place-items: center;
      color: white;
      font-size: 18px;
      font-weight: 800;
    }

    .brand-text span:first-child {
      display: block;
      font-size: 16px;
      font-weight: 700;
    }

    .brand-text span:last-child {
      display: block;
      font-size: 12px;
      color: #94A3B8;
      margin-top: 2px;
    }

    .nav-menu {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .nav-link {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 14px 16px;
      border-radius: 16px;
      text-decoration: none;
      color: var(--sidebar-text);
      font-size: 14px;
      transition: background 0.2s ease, color 0.2s ease;
    }

    .nav-link.active {
      background: #F97316;
      color: white;
    }

    .nav-link:hover {
      background: rgba(249, 115, 22, 0.16);
    }

    .sidebar-footer {
      margin-top: auto;
      padding: 18px 16px;
      border-radius: 20px;
      background: rgba(255, 255, 255, 0.06);
      border: 1px solid rgba(249, 115, 22, 0.18);
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
    }

    .footer-avatar {
      width: 42px;
      height: 42px;
      border-radius: 14px;
      display: grid;
      place-items: center;
      font-weight: 700;
      color: white;
      background: radial-gradient(circle at top left, #F97316 0%, #FB923C 85%);
      box-shadow: 0 14px 30px rgba(249, 115, 22, 0.18);
      flex-shrink: 0;
    }

    .footer-info {
      display: flex;
      flex-direction: column;
      gap: 2px;
      color: #E2E8F0;
      min-width: 0;
    }

    .footer-title {
      font-size: 14px;
      font-weight: 700;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .footer-action {
      font-size: 12px;
      color: #F97316;
      font-weight: 700;
      white-space: nowrap;
    }

    .content {
      padding: 32px;
      background: #E5E7EB;
    }

    .page-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 16px;
      margin-bottom: 24px;
    }

    .page-title {
      margin: 0;
      font-size: 32px;
      font-weight: 800;
      letter-spacing: -0.04em;
    }

    .page-description {
      margin: 8px 0 0;
      color: #64748B;
      font-size: 15px;
      max-width: 560px;
    }

    .status-chip {
      padding: 10px 18px;
      border-radius: 999px;
      background: #ECFDF5;
      color: #166534;
      font-size: 13px;
      font-weight: 700;
      border: 1px solid rgba(22, 163, 74, 0.14);
      white-space: nowrap;
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 18px;
      margin-bottom: 22px;
    }

    .stat-card {
      background: var(--panel-bg);
      border-radius: 24px;
      padding: 22px;
      box-shadow: var(--shadow);
      border: 1px solid var(--panel-border);
      display: flex;
      align-items: center;
      gap: 14px;
    }

    .stat-icon {
      width: 46px;
      height: 46px;
      border-radius: 14px;
      display: grid;
      place-items: center;
      font-size: 18px;
      font-weight: 700;
      color: white;
    }

    .stat-blue .stat-icon { background: #DBEAFE; color: #1D4ED8; }
    .stat-orange .stat-icon { background: #FEF3C7; color: #D97706; }
    .stat-indigo .stat-icon { background: #E0E7FF; color: #3730A3; }
    .stat-green .stat-icon { background: #DCFCE7; color: #15803D; }
    .stat-red .stat-icon { background: #FEE2E2; color: #B91C1C; }

    .stat-label {
      color: #64748B;
      font-size: 12px;
      font-weight: 700;
      letter-spacing: 0.10em;
      text-transform: uppercase;
      margin-bottom: 6px;
    }

    .stat-value {
      margin: 0;
      font-size: 24px;
      font-weight: 800;
      color: #0F172A;
    }

    .stat-note {
      margin: 6px 0 0;
      color: #64748B;
      font-size: 13px;
    }

    .filter-bar {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      gap: 14px;
      padding: 20px 24px;
      background: var(--panel-bg);
      border-radius: 24px;
      box-shadow: var(--shadow);
      border: 1px solid var(--panel-border);
      margin-bottom: 22px;
    }

    .filter-group {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      align-items: center;
    }

    .filter-group label {
      color: #475569;
      font-size: 14px;
      font-weight: 700;
      white-space: nowrap;
    }

    .filter-input,
    .search-input,
    .filter-select {
      border: 1px solid #CBD5E1;
      border-radius: 16px;
      background: #F8FAFC;
      padding: 14px 16px;
      font-size: 14px;
      color: #0F172A;
      outline: none;
      min-width: 220px;
      transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .filter-input:focus,
    .search-input:focus,
    .filter-select:focus {
      border-color: #F97316;
      box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.08);
    }

    .btn-export {
      border: none;
      border-radius: 16px;
      background: #F97316;
      color: white;
      padding: 14px 20px;
      font-size: 14px;
      font-weight: 700;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      box-shadow: 0 12px 25px rgba(249, 115, 22, 0.18);
      transition: transform 0.2s ease;
      white-space: nowrap;
    }

    .btn-export:hover {
      transform: translateY(-1px);
    }

    .table-card {
      background: var(--panel-bg);
      border-radius: 28px;
      padding: 20px;
      box-shadow: var(--shadow);
      border: 1px solid var(--panel-border);
    }

    .rewards-table {
      width: 100%;
      border-collapse: collapse;
      min-width: 900px;
    }

    .rewards-table th,
    .rewards-table td {
      padding: 18px 14px;
      font-size: 14px;
      color: #334155;
      vertical-align: middle;
    }

    .rewards-table th {
      text-align: left;
      color: #64748B;
      font-size: 12px;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      font-weight: 700;
      padding-bottom: 12px;
    }

    .rewards-table tbody tr {
      border-bottom: 1px solid #E2E8F0;
    }

    .rewards-table tbody tr:last-child {
      border-bottom: none;
    }

    .badge-status {
      display: inline-flex;
      align-items: center;
      padding: 8px 12px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 700;
    }

    .badge-available { background: #DCFCE7; color: #166534; }
    .badge-limited { background: #FEF3C7; color: #92400E; }
    .badge-outofstock { background: #FEE2E2; color: #991B1B; }

    .action-button {
      border: none;
      border-radius: 999px;
      padding: 10px 16px;
      font-size: 13px;
      font-weight: 700;
      cursor: pointer;
      transition: transform 0.2s ease, opacity 0.2s ease;
      min-width: 90px;
    }

    .btn-edit { background: #2563EB; color: white; }
    .btn-delete { background: #DC2626; color: white; }
    .btn-add { background: #F97316; color: white; box-shadow: 0 12px 25px rgba(249, 115, 22, 0.18); }
    .btn-disabled { background: #E2E8F0; color: #475569; cursor: default; opacity: 0.75; }

    .action-button:hover:not(.btn-disabled) {
      transform: translateY(-1px);
    }

    .modal {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.5);
      z-index: 1000;
      align-items: center;
      justify-content: center;
    }

    .modal.active {
      display: flex;
    }

    .modal-content {
      background: var(--panel-bg);
      border-radius: 28px;
      padding: 32px;
      box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
      max-width: 500px;
      width: 90%;
      max-height: 90vh;
      overflow-y: auto;
    }

    .modal-header {
      font-size: 20px;
      font-weight: 800;
      margin-bottom: 20px;
      color: var(--text-dark);
    }

    .form-group {
      margin-bottom: 16px;
    }

    .form-group label {
      display: block;
      margin-bottom: 6px;
      font-size: 13px;
      font-weight: 700;
      color: #475569;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
      width: 100%;
      padding: 12px 14px;
      border: 1px solid #CBD5E1;
      border-radius: 12px;
      background: #F8FAFC;
      font-size: 14px;
      color: var(--text-dark);
      outline: none;
      transition: border-color 0.2s ease;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
      border-color: #F97316;
      box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.08);
    }

    .modal-actions {
      display: flex;
      gap: 12px;
      margin-top: 24px;
    }

    .modal-actions button {
      flex: 1;
    }

    .btn-secondary {
      background: #E2E8F0;
      color: #475569;
    }

    .btn-secondary:hover {
      transform: translateY(-1px);
    }

    .footer-note {
      margin-top: 24px;
      padding: 20px 24px;
      background: #F8FAFC;
      border-radius: 22px;
      border: 1px solid rgba(148, 163, 184, 0.16);
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      color: #475569;
      font-size: 14px;
    }

    .footer-note strong {
      color: #0F172A;
      font-size: 14px;
    }

    @media (max-width: 1100px) {
      .stats-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
      .rewards-table {
        min-width: 100%;
      }
    }

    @media (max-width: 860px) {
      .admin-shell {
        grid-template-columns: 1fr;
      }
      .sidebar {
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: space-between;
      }
      .page-header {
        flex-direction: column;
        align-items: flex-start;
      }
      .filter-bar {
        flex-direction: column;
        align-items: stretch;
      }
      .filter-group,
      .filter-right {
        width: 100%;
        justify-content: flex-start;
      }
      .filter-input,
      .search-input,
      .filter-select,
      .btn-export {
        width: 100%;
        min-width: 0;
      }
      .stats-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
  <div class="admin-shell">
    <main class="content">
      <div class="page-header">
        <div>
          <h1 class="page-title">Kelola Reward</h1>
          <p class="page-description">Buat, lihat, ubah, dan hapus reward yang bisa ditukar oleh member.</p>
        </div>
        <div class="status-chip">Server Online (Mock)</div>
      </div>
      <div class="stats-grid">
        <div class="stat-card stat-blue">
          <div class="stat-icon">🎁</div>
          <div>
            <div class="stat-label">Total Reward</div>
            <p class="stat-value" id="stat-total">0</p>
            <p class="stat-note">Semua Reward</p>
          </div>
        </div>
        <div class="stat-card stat-green">
          <div class="stat-icon">✅</div>
          <div>
            <div class="stat-label">Tersedia</div>
            <p class="stat-value" id="stat-available">0</p>
            <p class="stat-note">Stok Tersedia</p>
          </div>
        </div>
        <div class="stat-card stat-orange">
          <div class="stat-icon">⚠️</div>
          <div>
            <div class="stat-label">Terbatas</div>
            <p class="stat-value" id="stat-limited">0</p>
            <p class="stat-note">Stok Menipis</p>
          </div>
        </div>
      </div>
      <div class="filter-bar">
        <div class="filter-group">
          <input type="text" id="search-input" class="search-input" placeholder="Cari reward, kategori, atau tier...">
          <select id="filter-category" class="filter-select">
            <option value="">Semua Kategori</option>
          </select>
          <select id="filter-tier" class="filter-select">
            <option value="">Semua Tier</option>
            <option value="basic">Basic</option>
            <option value="silver">Silver</option>
            <option value="gold">Gold</option>
          </select>
        </div>
        <div class="filter-group" style="flex:1; justify-content:flex-end;">
          <button class="btn-export btn-add" id="btn-add-reward">+ Tambah Reward</button>
        </div>
      </div>
      <div class="table-card">
        <div style="overflow-x:auto;">
          <table class="rewards-table" id="rewards-table">
            <thead>
              <tr>
                <th>Nama Reward</th>
                <th>Kategori</th>
                <th>Harga Poin</th>
                <th>Stok</th>
                <th>Tier</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
      <div class="footer-note">
        <div>
          <strong>Panduan Tier</strong>
          <span>Basic = Semua member, Silver = Member dengan poin ≥500, Gold = Member dengan poin ≥1000</span>
        </div>
      </div>
    </main>
  </div>

  <!-- Modal Add/Edit Reward -->
  <div class="modal" id="modal-reward">
    <div class="modal-content">
      <div class="modal-header" id="modal-title">Tambah Reward Baru</div>
      <form id="form-reward" onsubmit="return false;">
        <input type="hidden" id="input-id">
        <div class="form-group">
          <label>Nama Reward</label>
          <input type="text" id="input-name" required>
        </div>
        <div class="form-group">
          <label>Harga Poin</label>
          <input type="number" id="input-points" min="0" required>
        </div>
        <div class="form-group">
          <label>Kategori</label>
          <input type="text" id="input-category" placeholder="Contoh: Food, Merch, Promo">
        </div>
        <div class="form-group">
          <label>Stok</label>
          <input type="number" id="input-stock" min="0" value="1">
        </div>
        <div class="form-group">
          <label>Batasan Tier</label>
          <select id="input-tier">
            <option value="basic">Basic (Semua Member)</option>
            <option value="silver">Silver (Poin ≥ 500)</option>
            <option value="gold">Gold (Poin ≥ 1000)</option>
          </select>
        </div>
        <div class="modal-actions">
          <button type="button" class="action-button btn-secondary" onclick="closeModal()">Batalkan</button>
          <button type="button" class="action-button btn-add" onclick="saveReward()">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    let rewards = [
      { id: 'RWD-001', name: 'Voucher Makan 50rb', points: 50, category: 'Food', stock: 20, tier: 'basic' },
      { id: 'RWD-002', name: 'Diskon 10% Transaksi', points: 100, category: 'Promo', stock: 10, tier: 'silver' },
      { id: 'RWD-003', name: 'Tumbler Eksklusif', points: 300, category: 'Merch', stock: 5, tier: 'gold' },
      { id: 'RWD-004', name: 'Voucher Minum 30rb', points: 30, category: 'Food', stock: 0, tier: 'basic' }
    ];

    const tableBody = document.querySelector('#rewards-table tbody');
    const modal = document.getElementById('modal-reward');
    const modalTitle = document.getElementById('modal-title');
    const formReward = document.getElementById('form-reward');
    const inputId = document.getElementById('input-id');
    const inputName = document.getElementById('input-name');
    const inputPoints = document.getElementById('input-points');
    const inputCategory = document.getElementById('input-category');
    const inputStock = document.getElementById('input-stock');
    const inputTier = document.getElementById('input-tier');
    const searchInput = document.getElementById('search-input');
    const filterCategory = document.getElementById('filter-category');
    const filterTier = document.getElementById('filter-tier');
    const btnAddReward = document.getElementById('btn-add-reward');

    function uid(prefix = 'RWD') {
      const n = Math.floor(Math.random() * 900) + 100;
      return `${prefix}-${n}`;
    }

    function updateStats() {
      const total = rewards.length;
      const available = rewards.filter(r => r.stock > 0).length;
      const limited = rewards.filter(r => r.stock > 0 && r.stock <= 5).length;
      document.getElementById('stat-total').textContent = total;
      document.getElementById('stat-available').textContent = available;
      document.getElementById('stat-limited').textContent = limited;
    }

    function renderCategories() {
      const cats = Array.from(new Set(rewards.map(r => r.category).filter(Boolean)));
      filterCategory.innerHTML = '<option value="">Semua Kategori</option>' + cats.map(c => `<option value="${c}">${c}</option>`).join('');
    }

    function getStockBadge(stock) {
      if (stock === 0) return '<span class="badge-status badge-outofstock">Habis</span>';
      if (stock <= 5) return '<span class="badge-status badge-limited">Menipis</span>';
      return '<span class="badge-status badge-available">Tersedia</span>';
    }

    function renderTable() {
      const q = searchInput.value.trim().toLowerCase();
      const cat = filterCategory.value;
      const tier = filterTier.value;
      tableBody.innerHTML = '';
      const list = rewards.filter(r => {
        if (q && !(r.name.toLowerCase().includes(q) || (r.category || '').toLowerCase().includes(q))) return false;
        if (cat && r.category !== cat) return false;
        if (tier && r.tier !== tier) return false;
        return true;
      });
      if (!list.length) {
        const tr = document.createElement('tr');
        tr.innerHTML = '<td colspan="7" style="padding:22px;text-align:center;color:#64748B">Tidak ada reward yang sesuai filter.</td>';
        tableBody.appendChild(tr);
        return;
      }
      list.forEach(r => {
        const tr = document.createElement('tr');
        const tierLabel = { basic: 'Basic', silver: 'Silver', gold: 'Gold' }[r.tier] || r.tier;
        tr.innerHTML = `
          <td><strong>${r.name}</strong></td>
          <td>${r.category || '-'}</td>
          <td style="color:#EA580C;font-weight:700;">${r.points} Poin</td>
          <td>${r.stock}</td>
          <td><span style="font-size:12px;font-weight:700">${tierLabel}</span></td>
          <td>${getStockBadge(r.stock)}</td>
          <td>
            <div style="display:flex;gap:8px">
              <button class="action-button btn-edit" onclick="openEdit('${r.id}')">Edit</button>
              <button class="action-button btn-delete" onclick="removeReward('${r.id}')">Hapus</button>
            </div>
          </td>
        `;
        tableBody.appendChild(tr);
      });
    }

    function openCreate() {
      modalTitle.textContent = 'Tambah Reward Baru';
      inputId.value = '';
      inputName.value = '';
      inputPoints.value = '';
      inputCategory.value = '';
      inputStock.value = 1;
      inputTier.value = 'basic';
      modal.classList.add('active');
    }

    function openEdit(id) {
      const r = rewards.find(x => x.id === id);
      if (!r) return;
      modalTitle.textContent = 'Edit Reward';
      inputId.value = r.id;
      inputName.value = r.name;
      inputPoints.value = r.points;
      inputCategory.value = r.category || '';
      inputStock.value = r.stock;
      inputTier.value = r.tier || 'basic';
      modal.classList.add('active');
    }

    function closeModal() {
      modal.classList.remove('active');
    }

    function saveReward() {
      const id = inputId.value || uid();
      const name = inputName.value.trim();
      const points = parseInt(inputPoints.value, 10) || 0;
      const category = inputCategory.value.trim();
      const stock = parseInt(inputStock.value, 10) || 0;
      const tier = inputTier.value || 'basic';
      if (!name) return alert('Nama reward wajib diisi');
      const existing = rewards.find(r => r.id === id);
      if (existing) {
        existing.name = name;
        existing.points = points;
        existing.category = category;
        existing.stock = stock;
        existing.tier = tier;
      } else {
        rewards.unshift({ id, name, points, category, stock, tier });
      }
      renderCategories();
      renderTable();
      updateStats();
      closeModal();
    }

    function removeReward(id) {
      if (!confirm('Hapus reward ini secara permanen?')) return;
      rewards = rewards.filter(r => r.id !== id);
      renderCategories();
      renderTable();
      updateStats();
    }

    searchInput.addEventListener('input', renderTable);
    filterCategory.addEventListener('change', renderTable);
    filterTier.addEventListener('change', renderTable);
    btnAddReward.addEventListener('click', openCreate);

    renderCategories();
    renderTable();
    updateStats();
  </script>
