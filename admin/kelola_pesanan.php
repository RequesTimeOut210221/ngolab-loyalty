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
      grid-template-columns: repeat(5, minmax(0, 1fr));
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

    .orders-table {
      width: 100%;
      border-collapse: collapse;
      min-width: 900px;
    }

    .orders-table th,
    .orders-table td {
      padding: 18px 14px;
      font-size: 14px;
      color: #334155;
      vertical-align: middle;
    }

    .orders-table th {
      text-align: left;
      color: #64748B;
      font-size: 12px;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      font-weight: 700;
      padding-bottom: 12px;
    }

    .orders-table tbody tr {
      border-bottom: 1px solid #E2E8F0;
    }

    .orders-table tbody tr:last-child {
      border-bottom: none;
    }

    .secondary-text {
      display: block;
      color: #94A3B8;
      font-size: 12px;
      margin-top: 6px;
    }

    .badge-status {
      display: inline-flex;
      align-items: center;
      padding: 8px 12px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 700;
    }

    .badge-pending-status { background: #FEF3C7; color: #92400E; }
    .badge-processing-status { background: #DBEAFE; color: #1D4ED8; }
    .badge-success-status { background: #DCFCE7; color: #166534; }
    .badge-cancel-status { background: #FEE2E2; color: #991B1B; }

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

    .btn-process { background: #2563EB; color: white; }
    .btn-done { background: #16A34A; color: white; }
    .btn-cancel { background: #DC2626; color: white; }
    .btn-disabled { background: #E2E8F0; color: #475569; cursor: default; opacity: 0.75; }

    .action-button:hover:not(.btn-disabled) {
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
      .orders-table {
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
          <h1 class="page-title">Kelola Pesanan</h1>
          <p class="page-description">Lihat dan kelola semua pesanan masuk dari konsumen.</p>
        </div>
        <div class="status-chip">Server Online (Mock)</div>
      </div>
      <div class="stats-grid">
        <div class="stat-card stat-blue">
          <div class="stat-icon">📦</div>
          <div>
            <div class="stat-label">Total Pesanan</div>
            <p class="stat-value" id="stat-total">0</p>
            <p class="stat-note">Semua Pesanan</p>
          </div>
        </div>
        <div class="stat-card stat-orange">
          <div class="stat-icon">⏳</div>
          <div>
            <div class="stat-label">Pending</div>
            <p class="stat-value" id="stat-pending">0</p>
            <p class="stat-note">Menunggu Proses</p>
          </div>
        </div>
        <div class="stat-card stat-indigo">
          <div class="stat-icon">🔄</div>
          <div>
            <div class="stat-label">Diproses</div>
            <p class="stat-value" id="stat-processing">0</p>
            <p class="stat-note">Sedang Diproses</p>
          </div>
        </div>
        <div class="stat-card stat-green">
          <div class="stat-icon">✅</div>
          <div>
            <div class="stat-label">Selesai</div>
            <p class="stat-value" id="stat-success">0</p>
            <p class="stat-note">Selesai / Lunas</p>
          </div>
        </div>
        <div class="stat-card stat-red">
          <div class="stat-icon">❌</div>
          <div>
            <div class="stat-label">Dibatalkan</div>
            <p class="stat-value" id="stat-canceled">0</p>
            <p class="stat-note">Pesanan Dibatalkan</p>
          </div>
        </div>
      </div>
      <div class="filter-bar">
        <div class="filter-group">
          <label>01 Jun 2026 - 01 Jun 2026</label>
          <input type="date" id="date-from" class="filter-input" value="">
          <input type="date" id="date-to" class="filter-input" value="">
          <select id="status-filter" class="filter-select">
            <option value="all">Semua Status</option>
            <option value="pending">Pending</option>
            <option value="processing">Diproses</option>
            <option value="success">Selesai</option>
            <option value="canceled">Dibatalkan</option>
          </select>
        </div>
        <div class="filter-group" style="flex:1; justify-content:flex-end;">
          <input type="text" id="search-input" class="search-input" placeholder="Cari nama member / nomor HP / ID pesanan...">
          <button class="btn-export" id="export-button">Export Laporan</button>
        </div>
      </div>
      <div class="table-card">
        <div style="overflow-x:auto;">
          <table class="orders-table" id="orders-table">
            <thead>
              <tr>
                <th>ID Pesanan</th>
                <th>Tanggal</th>
                <th>Member</th>
                <th>No. HP</th>
                <th>Total Belanja</th>
                <th>Poin</th>
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
          <strong>Sistem Poin Otomatis</strong>
          <span>Ketika pesanan berstatus Selesai (Lunas), poin belanja akan otomatis ditambahkan ke saldo poin member.</span>
        </div>
        <div><strong>Kelipatan Rp 10.000 = 1 Poin</strong></div>
      </div>
    </main>
  </div>
  <script>
    const orders = [
      { id: '#NGLB-015', date: '01 Jun 2026 16:21', name: 'Budi Raharjo', nim: '1202210045', phone: '0812-3456-7890', total: 50000, points: 5, status: 'pending' },
      { id: '#NGLB-014', date: '01 Jun 2026 16:10', name: 'Ani Wijaya', nim: '1202210029', phone: '0821-6543-2109', total: 22000, points: 2, status: 'processing' },
      { id: '#NGLB-013', date: '01 Jun 2026 15:45', name: 'Dika Saputra', nim: '1202210003', phone: '0813-1122-3344', total: 100000, points: 10, status: 'success' },
      { id: '#NGLB-012', date: '01 Jun 2026 15:30', name: 'Siti Nurhaliza', nim: '1202210077', phone: '0857-8877-6655', total: 40000, points: 4, status: 'canceled' },
      { id: '#NGLB-011', date: '01 Jun 2026 14:50', name: 'Rudi Hermawan', nim: '1202210022', phone: '0812-9988-7766', total: 33000, points: 3, status: 'processing' }
    ];

    const state = {
      filterStatus: 'all',
      dateFrom: '',
      dateTo: '',
      search: ''
    };

    // Members mock store (keyed by NIM if available, else phone)
    const members = {};

    function memberKeyFor(order) {
      return order.nim && order.nim.trim() !== '' ? order.nim : order.phone;
    }

    function calculatePointsFromTotal(total) {
      return Math.floor(total / 10000);
    }

    function initMembers() {
      orders.forEach(o => {
        const key = memberKeyFor(o);
        if (!members[key]) members[key] = { name: o.name, nim: o.nim, phone: o.phone, balance: 0 };
      });
      // Initialize balances from already-success orders (avoid double apply)
      orders.forEach(o => {
        if (o.status === 'success') {
          const key = memberKeyFor(o);
          const pts = typeof o.points === 'number' ? o.points : calculatePointsFromTotal(o.total);
          members[key].balance += pts;
          o._pointsApplied = true;
        }
      });
    }

    const statTotal = document.getElementById('stat-total');
    const statPending = document.getElementById('stat-pending');
    const statProcessing = document.getElementById('stat-processing');
    const statSuccess = document.getElementById('stat-success');
    const statCanceled = document.getElementById('stat-canceled');
    const ordersTableBody = document.querySelector('#orders-table tbody');
    const statusFilter = document.getElementById('status-filter');
    const dateFromInput = document.getElementById('date-from');
    const dateToInput = document.getElementById('date-to');
    const searchInput = document.getElementById('search-input');
    const exportButton = document.getElementById('export-button');

    function updateStats() {
      const total = orders.length;
      const pending = orders.filter(o => o.status === 'pending').length;
      const processing = orders.filter(o => o.status === 'processing').length;
      const success = orders.filter(o => o.status === 'success').length;
      const canceled = orders.filter(o => o.status === 'canceled').length;
      statTotal.textContent = total;
      statPending.textContent = pending;
      statProcessing.textContent = processing;
      statSuccess.textContent = success;
      statCanceled.textContent = canceled;
    }

    function formatCurrency(value) {
      return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value);
    }

    function createStatusBadge(status) {
      const badge = document.createElement('span');
      badge.className = 'badge-status ' + {
        pending: 'badge-pending-status',
        processing: 'badge-processing-status',
        success: 'badge-success-status',
        canceled: 'badge-cancel-status'
      }[status];
      badge.textContent = {
        pending: 'Pending',
        processing: 'Diproses',
        success: 'Selesai',
        canceled: 'Dibatalkan'
      }[status];
      return badge;
    }

    function button(text, styleClass, disabled, handler) {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = `action-button ${styleClass}`;
      btn.textContent = text;
      if (disabled) {
        btn.classList.add('btn-disabled');
        btn.disabled = true;
      }
      if (!disabled) btn.addEventListener('click', handler);
      return btn;
    }

    function buildActionButtons(order) {
      const wrapper = document.createElement('div');
      wrapper.style.display = 'flex';
      wrapper.style.flexWrap = 'wrap';
      wrapper.style.gap = '8px';
      if (order.status === 'pending') {
        wrapper.appendChild(button('Proses', 'btn-process', false, () => updateOrderStatus(order.id, 'processing')));
        wrapper.appendChild(button('Batal', 'btn-cancel', false, () => updateOrderStatus(order.id, 'canceled')));
      } else if (order.status === 'processing') {
        wrapper.appendChild(button('Selesai', 'btn-done', false, () => updateOrderStatus(order.id, 'success')));
        wrapper.appendChild(button('Batal', 'btn-cancel', false, () => updateOrderStatus(order.id, 'canceled')));
      } else if (order.status === 'success') {
        wrapper.appendChild(button('Selesai', 'btn-done', true, () => {}));
        // allow admin to remove completed record if desired
        wrapper.appendChild(button('Hapus', 'btn-cancel', false, () => deleteOrder(order.id)));
      } else if (order.status === 'canceled') {
        wrapper.appendChild(button('Dibatalkan', 'btn-disabled', true, () => {}));
        wrapper.appendChild(button('Hapus', 'btn-cancel', false, () => deleteOrder(order.id)));
      }
      return wrapper;
    }

    function applyPointsToMember(order) {
      if (!order) return;
      if (order._pointsApplied) return; // avoid double applying
      const key = memberKeyFor(order);
      const pts = calculatePointsFromTotal(order.total);
      order.points = pts;
      if (!members[key]) members[key] = { name: order.name, nim: order.nim, phone: order.phone, balance: 0 };
      members[key].balance += pts;
      order._pointsApplied = true;
      // visual toast
      showToast(`${pts} poin ditambahkan ke ${order.name} (Saldo: ${members[key].balance} Poin)`);
    }

    function deleteOrder(orderId) {
      const idx = orders.findIndex(o => o.id === orderId);
      if (idx === -1) return;
      if (!confirm('Hapus pesanan ini secara permanen?')) return;
      orders.splice(idx, 1);
      renderOrders();
      updateStats();
    }

    function updateOrderStatus(orderId, status) {
      const order = orders.find(item => item.id === orderId);
      if (!order) return;
      const prev = order.status;
      order.status = status;
      // when moving to success, apply points once
      if (status === 'success' && prev !== 'success') {
        applyPointsToMember(order);
      }
      renderOrders();
      updateStats();
    }

    // Robust date parsing for order.date (handles ISO or '01 Jun 2026 16:21' formats)
    function parseOrderDate(order) {
      if (!order || !order.date) return null;
      // try native parse first
      const d1 = new Date(order.date);
      if (!isNaN(d1.getTime())) return d1;

      // fallback: try dd MMM yyyy HH:mm (e.g., '01 Jun 2026 16:21')
      const parts = order.date.trim().split(' ');
      if (parts.length >= 3) {
        const day = parts[0];
        const monthStr = parts[1];
        const year = parts[2];
        const time = parts.length >= 4 ? parts[3] : '00:00';
        const months = { Jan: '01', Feb: '02', Mar: '03', Apr: '04', May: '05', Jun: '06', Jul: '07', Aug: '08', Sep: '09', Oct: '10', Nov: '11', Dec: '12' };
        const m = months[monthStr] || monthStr;
        const iso = `${year}-${m}-${day}T${time}:00`;
        const d2 = new Date(iso);
        if (!isNaN(d2.getTime())) return d2;
      }

      return null;
    }

    // Small toast for feedback
    function showToast(message, timeout = 3000) {
      const t = document.createElement('div');
      t.textContent = message;
      t.style.position = 'fixed';
      t.style.right = '20px';
      t.style.bottom = '20px';
      t.style.background = '#111827';
      t.style.color = 'white';
      t.style.padding = '12px 16px';
      t.style.borderRadius = '12px';
      t.style.boxShadow = '0 8px 30px rgba(2,6,23,0.4)';
      t.style.zIndex = 9999;
      document.body.appendChild(t);
      setTimeout(() => t.remove(), timeout);
    }

    function filterOrders() {
      const query = state.search.toLowerCase().trim();
      const from = state.dateFrom ? new Date(state.dateFrom) : null;
      const to = state.dateTo ? new Date(state.dateTo) : null;
      if (from) from.setHours(0,0,0,0);
      if (to) to.setHours(23,59,59,999);

      return orders.filter(order => {
        const orderDate = parseOrderDate(order);
        const matchStatus = state.filterStatus === 'all' || order.status === state.filterStatus;
        const matchSearch = query === '' || [order.id, (order.name||''), (order.nim||''), (order.phone||'')].some(value => String(value).toLowerCase().includes(query));
        const matchDate = (!from && !to) || !orderDate || ((from ? orderDate >= from : true) && (to ? orderDate <= to : true));
        return matchStatus && matchSearch && matchDate;
      });
    }

    function renderOrders() {
      ordersTableBody.innerHTML = '';
      const filtered = filterOrders();
      if (!filtered.length) {
        const row = document.createElement('tr');
        row.innerHTML = '<td colspan="8" style="padding:22px;text-align:center;color:#64748B;">Tidak ada pesanan yang sesuai filter.</td>';
        ordersTableBody.appendChild(row);
        return;
      }
      filtered.forEach(order => {
        const row = document.createElement('tr');
        row.className = 'order-row';
        const key = memberKeyFor(order);
        const balance = (members[key] && members[key].balance) ? members[key].balance : 0;
        row.innerHTML = `
          <td>${order.id}</td>
          <td>${order.date}</td>
          <td>${order.name}<span class="secondary-text">${order.nim} • Saldo: ${balance} Poin</span></td>
          <td>${order.phone}</td>
          <td style="color:#EA580C;font-weight:700;">${formatCurrency(order.total)}</td>
          <td style="color:#16A34A;font-weight:700;">+${order.points || calculatePointsFromTotal(order.total)} Poin</td>
          <td></td>
          <td></td>
        `;
        row.children[6].appendChild(createStatusBadge(order.status));
        row.children[7].appendChild(buildActionButtons(order));
        ordersTableBody.appendChild(row);
      });
    }

    function downloadCSV() {
      const data = filterOrders();
      const headers = ['ID Pesanan', 'Tanggal', 'Member', 'NIM', 'No. HP', 'Total Belanja', 'Poin', 'Status'];
      const csvRows = [headers.join(',')];
      data.forEach(order => {
        const row = [order.id, order.date, order.name, order.nim, order.phone, `Rp${order.total}`, order.points, order.status];
        csvRows.push(row.map(value => `"${value}"`).join(','));
      });
      const blob = new Blob([csvRows.join('\n')], { type: 'text/csv;charset=utf-8;' });
      const link = document.createElement('a');
      link.href = URL.createObjectURL(blob);
      link.download = 'laporan_pesanan.csv';
      document.body.appendChild(link);
      link.click();
      link.remove();
    }

    statusFilter.addEventListener('change', (event) => {
      state.filterStatus = event.target.value;
      renderOrders();
    });

    dateFromInput.addEventListener('change', (event) => {
      state.dateFrom = event.target.value;
      renderOrders();
    });

    dateToInput.addEventListener('change', (event) => {
      state.dateTo = event.target.value;
      renderOrders();
    });

    searchInput.addEventListener('input', (event) => {
        state.search = event.target.value;
        renderOrders();
    });

    exportButton.addEventListener('click', downloadCSV);

    initMembers();
    updateStats();
    renderOrders();
    </script>
