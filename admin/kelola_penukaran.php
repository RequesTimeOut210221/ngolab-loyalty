<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Penukaran | Ngolab POS Admin</title>
  <link rel="stylesheet" href="../assets/css/style.css">
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
      display: grid;
      grid-template-columns: 280px 1fr;
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
    .stat-green .stat-icon { background: #DCFCE7; color: #15803D; }

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

    .table-card {
      background: var(--panel-bg);
      border-radius: 28px;
      padding: 20px;
      box-shadow: var(--shadow);
      border: 1px solid var(--panel-border);
    }

    .redemptions-table {
      width: 100%;
      border-collapse: collapse;
      min-width: 1000px;
    }

    .redemptions-table th,
    .redemptions-table td {
      padding: 18px 14px;
      font-size: 14px;
      color: #334155;
      vertical-align: middle;
    }

    .redemptions-table th {
      text-align: left;
      color: #64748B;
      font-size: 12px;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      font-weight: 700;
      padding-bottom: 12px;
    }

    .redemptions-table tbody tr {
      border-bottom: 1px solid #E2E8F0;
    }

    .redemptions-table tbody tr:last-child {
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

    .badge-pending { background: #FEF3C7; color: #92400E; }
    .badge-approved { background: #DCFCE7; color: #166534; }
    .badge-rejected { background: #FEE2E2; color: #991B1B; }

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

    .btn-approve { background: #16A34A; color: white; }
    .btn-reject { background: #DC2626; color: white; }
    .btn-view { background: #2563EB; color: white; }
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

    .btn-cancel {
      background: #E2E8F0;
      color: #475569;
      border: none;
      border-radius: 999px;
      padding: 10px 16px;
      font-weight: 700;
      cursor: pointer;
      transition: transform 0.2s ease;
    }

    .btn-cancel:hover {
      transform: translateY(-1px);
    }

    .info-box {
      padding: 16px;
      background: #F8FAFC;
      border-radius: 12px;
      border: 1px solid #E2E8F0;
      margin-bottom: 16px;
      font-size: 14px;
      color: #475569;
    }

    .info-row {
      display: flex;
      justify-content: space-between;
      padding: 8px 0;
      border-bottom: 1px solid #E2E8F0;
    }

    .info-row:last-child {
      border-bottom: none;
    }

    .info-label {
      font-weight: 700;
      color: #0F172A;
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
      .redemptions-table {
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
      .filter-group {
        width: 100%;
        justify-content: flex-start;
      }
      .filter-input,
      .search-input,
      .filter-select {
        width: 100%;
        min-width: 0;
      }
      .stats-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <div class="admin-shell">
    <aside class="sidebar">
      <div class="brand">
        <div class="brand-logo">NG</div>
        <div class="brand-text">
          <span>NGOLAB POS ADMIN</span>
          <span>Admin Kelola Penukaran</span>
        </div>
      </div>
      <nav class="nav-menu">
        <a href="#" class="nav-link"><strong>Dashboard POS</strong></a>
        <a href="#" class="nav-link">Kelola Member</a>
        <a href="#" class="nav-link">Kelola Staff</a>
        <a href="#" class="nav-link">Kelola Menu</a>
        <a href="#" class="nav-link">Kelola Kategori</a>
        <a href="#" class="nav-link">Kelola Reward</a>
        <a href="#" class="nav-link">Kelola Pesanan</a>
        <a href="#" class="nav-link active"><strong>Kelola Penukaran</strong></a>
        <a href="#" class="nav-link">Kelola Feedback</a>
      </nav>
      <div class="sidebar-footer">
        <div class="footer-avatar">A</div>
        <div class="footer-info">
          <div class="footer-title">Admin Kelola Penukaran</div>
          <div class="footer-action">Keluar</div>
        </div>
      </div>
    </aside>
    <main class="content">
      <div class="page-header">
        <div>
          <h1 class="page-title">Kelola Penukaran</h1>
          <p class="page-description">Lihat dan kelola klaim penukaran poin oleh member.</p>
        </div>
        <div class="status-chip">Server Online (Mock)</div>
      </div>
      <div class="stats-grid">
        <div class="stat-card stat-blue">
          <div class="stat-icon">📋</div>
          <div>
            <div class="stat-label">Total Klaim</div>
            <p class="stat-value" id="stat-total">0</p>
            <p class="stat-note">Semua Penukaran</p>
          </div>
        </div>
        <div class="stat-card stat-orange">
          <div class="stat-icon">⏳</div>
          <div>
            <div class="stat-label">Menunggu</div>
            <p class="stat-value" id="stat-pending">0</p>
            <p class="stat-note">Belum Dikonfirmasi</p>
          </div>
        </div>
        <div class="stat-card stat-green">
          <div class="stat-icon">✅</div>
          <div>
            <div class="stat-label">Disetujui</div>
            <p class="stat-value" id="stat-approved">0</p>
            <p class="stat-note">Penukaran Berhasil</p>
          </div>
        </div>
      </div>
      <div class="filter-bar">
        <div class="filter-group">
          <input type="text" id="search-input" class="search-input" placeholder="Cari nama member, ID penukaran...">
          <select id="filter-status" class="filter-select">
            <option value="">Semua Status</option>
            <option value="pending">Menunggu</option>
            <option value="approved">Disetujui</option>
            <option value="rejected">Ditolak</option>
          </select>
        </div>
      </div>
      <div class="table-card">
        <div style="overflow-x:auto;">
          <table class="redemptions-table" id="redemptions-table">
            <thead>
              <tr>
                <th>ID Penukaran</th>
                <th>Tanggal</th>
                <th>Member</th>
                <th>Reward</th>
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
          <strong>Catatan Penukaran</strong>
          <span>Konfirmasi penukaran untuk memberikan reward kepada member. Tolak jika reward tidak tersedia.</span>
        </div>
      </div>
    </main>
  </div>

  <!-- Modal Detail & Konfirmasi -->
  <div class="modal" id="modal-redemption">
    <div class="modal-content">
      <div class="modal-header" id="modal-title">Detail Penukaran</div>
      <div class="info-box">
        <div class="info-row">
          <span class="info-label">ID Penukaran:</span>
          <span id="detail-id"></span>
        </div>
        <div class="info-row">
          <span class="info-label">Tanggal:</span>
          <span id="detail-date"></span>
        </div>
        <div class="info-row">
          <span class="info-label">Member:</span>
          <span id="detail-member"></span>
        </div>
        <div class="info-row">
          <span class="info-label">No. HP:</span>
          <span id="detail-phone"></span>
        </div>
        <div class="info-row">
          <span class="info-label">Reward:</span>
          <span id="detail-reward"></span>
        </div>
        <div class="info-row">
          <span class="info-label">Poin Digunakan:</span>
          <span id="detail-points" style="color:#EA580C;font-weight:700"></span>
        </div>
        <div class="info-row">
          <span class="info-label">Status:</span>
          <span id="detail-status"></span>
        </div>
      </div>
      <div id="modal-actions" class="modal-actions">
        <button type="button" class="btn-cancel" onclick="closeModal()">Tutup</button>
      </div>
    </div>
  </div>

  <script>
    let redemptions = [
      { id: 'RDM-001', date: '04 Jun 2026 10:30', member: 'Budi Raharjo', phone: '0812-3456-7890', nim: '1202210045', reward: 'Voucher Makan 50rb', points: 50, status: 'pending' },
      { id: 'RDM-002', date: '04 Jun 2026 09:15', member: 'Ani Wijaya', phone: '0821-6543-2109', nim: '1202210029', reward: 'Diskon 10%', points: 100, status: 'approved' },
      { id: 'RDM-003', date: '03 Jun 2026 16:45', member: 'Dika Saputra', phone: '0813-1122-3344', nim: '1202210003', reward: 'Tumbler Eksklusif', points: 300, status: 'approved' },
      { id: 'RDM-004', date: '03 Jun 2026 14:20', member: 'Siti Nurhaliza', phone: '0857-8877-6655', nim: '1202210077', reward: 'Voucher Makan 50rb', points: 50, status: 'rejected' },
      { id: 'RDM-005', date: '02 Jun 2026 11:00', member: 'Rudi Hermawan', phone: '0812-9988-7766', nim: '1202210022', reward: 'Diskon 10%', points: 100, status: 'pending' }
    ];

    const tableBody = document.querySelector('#redemptions-table tbody');
    const modal = document.getElementById('modal-redemption');
    const modalTitle = document.getElementById('modal-title');
    const modalActions = document.getElementById('modal-actions');
    const searchInput = document.getElementById('search-input');
    const filterStatus = document.getElementById('filter-status');

    function updateStats() {
      const total = redemptions.length;
      const pending = redemptions.filter(r => r.status === 'pending').length;
      const approved = redemptions.filter(r => r.status === 'approved').length;
      document.getElementById('stat-total').textContent = total;
      document.getElementById('stat-pending').textContent = pending;
      document.getElementById('stat-approved').textContent = approved;
    }

    function getStatusBadge(status) {
      if (status === 'pending') return '<span class="badge-status badge-pending">Menunggu</span>';
      if (status === 'approved') return '<span class="badge-status badge-approved">Disetujui</span>';
      if (status === 'rejected') return '<span class="badge-status badge-rejected">Ditolak</span>';
      return '';
    }

    function renderTable() {
      const q = searchInput.value.trim().toLowerCase();
      const status = filterStatus.value;
      tableBody.innerHTML = '';
      const list = redemptions.filter(r => {
        if (q && !(r.id.toLowerCase().includes(q) || r.member.toLowerCase().includes(q) || r.phone.includes(q))) return false;
        if (status && r.status !== status) return false;
        return true;
      });
      if (!list.length) {
        const tr = document.createElement('tr');
        tr.innerHTML = '<td colspan="7" style="padding:22px;text-align:center;color:#64748B">Tidak ada penukaran yang sesuai filter.</td>';
        tableBody.appendChild(tr);
        return;
      }
      list.forEach(r => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td><strong>${r.id}</strong></td>
          <td>${r.date}</td>
          <td>${r.member}<span class="secondary-text">${r.nim}</span></td>
          <td>${r.reward}</td>
          <td style="color:#EA580C;font-weight:700;">${r.points} Poin</td>
          <td>${getStatusBadge(r.status)}</td>
          <td>
            <div style="display:flex;gap:8px">
              <button class="action-button btn-view" onclick="openDetail('${r.id}')">Lihat</button>
              ${r.status === 'pending' ? `
                <button class="action-button btn-approve" onclick="approveRedemption('${r.id}')">Setujui</button>
                <button class="action-button btn-reject" onclick="rejectRedemption('${r.id}')">Tolak</button>
              ` : `
                <button class="action-button btn-disabled" disabled>-</button>
              `}
            </div>
          </td>
        `;
        tableBody.appendChild(tr);
      });
    }

    function openDetail(id) {
      const r = redemptions.find(x => x.id === id);
      if (!r) return;
      modalTitle.textContent = 'Detail Penukaran';
      document.getElementById('detail-id').textContent = r.id;
      document.getElementById('detail-date').textContent = r.date;
      document.getElementById('detail-member').textContent = r.member;
      document.getElementById('detail-phone').textContent = r.phone;
      document.getElementById('detail-reward').textContent = r.reward;
      document.getElementById('detail-points').textContent = r.points + ' Poin';
      document.getElementById('detail-status').innerHTML = getStatusBadge(r.status);
      modalActions.innerHTML = '<button type="button" class="btn-cancel" onclick="closeModal()">Tutup</button>';
      modal.classList.add('active');
    }

    function approveRedemption(id) {
      const r = redemptions.find(x => x.id === id);
      if (!r) return;
      if (!confirm(`Setujui penukaran ${r.reward} untuk ${r.member}?`)) return;
      r.status = 'approved';
      renderTable();
      updateStats();
      showToast(`Penukaran ${r.id} disetujui!`);
      closeModal();
    }

    function rejectRedemption(id) {
      const r = redemptions.find(x => x.id === id);
      if (!r) return;
      if (!confirm(`Tolak penukaran ${r.reward} untuk ${r.member}?`)) return;
      r.status = 'rejected';
      renderTable();
      updateStats();
      showToast(`Penukaran ${r.id} ditolak.`);
      closeModal();
    }

    function closeModal() {
      modal.classList.remove('active');
    }

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

    searchInput.addEventListener('input', renderTable);
    filterStatus.addEventListener('change', renderTable);

    updateStats();
    renderTable();
  </script>
</body>
</html>
