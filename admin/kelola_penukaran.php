<?php
/* Author: Shaena */
 
if (!defined('IN_ADMIN')) exit('No direct script access allowed'); 
require_once '../koneksi.php';

// Handle Actions (Approve, Reject, Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $redemptionId = intval($_POST['redemption_id']);

    if ($action === 'approve') {
        $stmtUpdate = $conn->prepare("UPDATE TABEL_PENUKARAN_REWARD SET status = 'berhasil' WHERE id_penukaran = ?");
        $stmtUpdate->bind_param("i", $redemptionId);
        $stmtUpdate->execute();
    } elseif ($action === 'reject') {
        // Find redemption points to refund
        $stmt = $conn->prepare("
            SELECT pr.id_member, r.poin_dibutuhkan 
            FROM TABEL_PENUKARAN_REWARD pr
            JOIN TABEL_REWARD r ON pr.id_reward = r.id_reward
            WHERE pr.id_penukaran = ? AND pr.status = 'pending'
        ");
        $stmt->bind_param("i", $redemptionId);
        $stmt->execute();
        $pr = $stmt->get_result()->fetch_assoc();
        
        if ($pr) {
            // Update status to ditolak
            $stmtUpdate = $conn->prepare("UPDATE TABEL_PENUKARAN_REWARD SET status = 'ditolak' WHERE id_penukaran = ?");
            $stmtUpdate->bind_param("i", $redemptionId);
            $stmtUpdate->execute();
            
            // Refund points
            $stmtRefund = $conn->prepare("UPDATE TABEL_MEMBER SET saldo_poin = saldo_poin + ? WHERE id_member = ?");
            $stmtRefund->bind_param("ii", $pr['poin_dibutuhkan'], $pr['id_member']);
            $stmtRefund->execute();
        }
    } elseif ($action === 'delete') {
        $stmtDel = $conn->prepare("DELETE FROM TABEL_PENUKARAN_REWARD WHERE id_penukaran = ?");
        $stmtDel->bind_param("i", $redemptionId);
        $stmtDel->execute();
    }
    
    // Redirect to refresh
    echo "<script>window.location.href='admin_dashboard.php?page=penukaran';</script>";
    exit;
}

// Fetch all redemptions
$query = "
    SELECT pr.id_penukaran, pr.tanggal_tukar, pr.status, pr.token_wifi,
           m.username, m.nim, m.no_hp,
           r.nama_reward, r.poin_dibutuhkan 
    FROM TABEL_PENUKARAN_REWARD pr
    JOIN TABEL_MEMBER m ON pr.id_member = m.id_member
    JOIN TABEL_REWARD r ON pr.id_reward = r.id_reward
    ORDER BY pr.tanggal_tukar DESC
";
$result = $conn->query($query);
$db_redemptions = [];
while ($row = $result->fetch_assoc()) {
    $db_redemptions[] = [
        'id' => $row['id_penukaran'],
        'date' => date('d M Y H:i', strtotime($row['tanggal_tukar'])),
        'member' => htmlspecialchars($row['username']),
        'nim' => htmlspecialchars($row['nim'] ?? ''),
        'phone' => htmlspecialchars($row['no_hp']),
        'reward' => htmlspecialchars($row['nama_reward']),
        'points' => (int)$row['poin_dibutuhkan'],
        'status' => $row['status'],
        'token_wifi' => $row['token_wifi']
    ];
}
$redemptionsJson = json_encode($db_redemptions);
?>
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

    * { box-sizing: border-box; }
    body { margin: 0; min-height: 100vh; font-family: 'Plus Jakarta Sans', sans-serif; background: #E5E7EB; color: var(--text-dark); }
    .admin-shell { display: block; min-height: 100vh; }
    .content { padding: 32px; background: #E5E7EB; }
    .page-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 24px; }
    .page-title { margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.04em; }
    .page-description { margin: 8px 0 0; color: #64748B; font-size: 15px; max-width: 560px; }
    .status-chip { padding: 10px 18px; border-radius: 999px; background: #DCFCE7; color: #166534; font-size: 13px; font-weight: 700; border: 1px solid rgba(22, 163, 74, 0.14); white-space: nowrap; }

    .stats-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 18px; margin-bottom: 22px; }
    .stat-card { background: var(--panel-bg); border-radius: 24px; padding: 22px; box-shadow: var(--shadow); border: 1px solid var(--panel-border); display: flex; align-items: center; gap: 14px; }
    .stat-icon { width: 46px; height: 46px; border-radius: 14px; display: grid; place-items: center; font-size: 18px; font-weight: 700; color: white; }
    .stat-blue .stat-icon { background: #DBEAFE; color: #1D4ED8; }
    .stat-orange .stat-icon { background: #FEF3C7; color: #D97706; }
    .stat-green .stat-icon { background: #DCFCE7; color: #15803D; }
    .stat-label { color: #64748B; font-size: 12px; font-weight: 700; letter-spacing: 0.10em; text-transform: uppercase; margin-bottom: 6px; }
    .stat-value { margin: 0; font-size: 24px; font-weight: 800; color: #0F172A; }
    .stat-note { margin: 6px 0 0; color: #64748B; font-size: 13px; }

    .filter-bar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 14px; padding: 20px 24px; background: var(--panel-bg); border-radius: 24px; box-shadow: var(--shadow); border: 1px solid var(--panel-border); margin-bottom: 22px; }
    .filter-group { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; }
    .filter-input, .search-input, .filter-select { border: 1px solid #CBD5E1; border-radius: 16px; background: #F8FAFC; padding: 14px 16px; font-size: 14px; color: #0F172A; outline: none; min-width: 220px; transition: border-color 0.2s ease, box-shadow 0.2s ease; }
    .filter-input:focus, .search-input:focus, .filter-select:focus { border-color: #F97316; box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.08); }

    .table-card { background: var(--panel-bg); border-radius: 28px; padding: 20px; box-shadow: var(--shadow); border: 1px solid var(--panel-border); }
    .redemptions-table { width: 100%; border-collapse: collapse; min-width: 1000px; }
    .redemptions-table th, .redemptions-table td { padding: 18px 14px; font-size: 14px; color: #334155; vertical-align: middle; }
    .redemptions-table th { text-align: left; color: #64748B; font-size: 12px; letter-spacing: 0.12em; text-transform: uppercase; font-weight: 700; padding-bottom: 12px; }
    .redemptions-table tbody tr { border-bottom: 1px solid #E2E8F0; }
    .redemptions-table tbody tr:last-child { border-bottom: none; }
    .secondary-text { display: block; color: #94A3B8; font-size: 12px; margin-top: 6px; }

    .badge-status { display: inline-flex; align-items: center; padding: 8px 12px; border-radius: 999px; font-size: 12px; font-weight: 700; }
    .badge-pending { background: #FEF3C7; color: #92400E; }
    .badge-approved { background: #DCFCE7; color: #166534; }
    .badge-rejected { background: #FEE2E2; color: #991B1B; }

    .action-button { border: none; border-radius: 999px; padding: 10px 16px; font-size: 13px; font-weight: 700; cursor: pointer; transition: transform 0.2s ease; min-width: 90px; }
    .btn-approve { background: #16A34A; color: white; }
    .btn-reject { background: #DC2626; color: white; }
    .btn-view { background: #2563EB; color: white; }
    .btn-disabled { background: #E2E8F0; color: #475569; cursor: default; opacity: 0.75; }
    .action-button:hover:not(.btn-disabled) { transform: translateY(-1px); }

    .modal { display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.5); z-index: 1000; align-items: center; justify-content: center; }
    .modal.active { display: flex; }
    .modal-content { background: var(--panel-bg); border-radius: 28px; padding: 32px; box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25); max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto; }
    .modal-header { font-size: 20px; font-weight: 800; margin-bottom: 20px; color: var(--text-dark); }
    
    .info-box { padding: 16px; background: #F8FAFC; border-radius: 12px; border: 1px solid #E2E8F0; margin-bottom: 16px; font-size: 14px; color: #475569; }
    .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #E2E8F0; }
    .info-row:last-child { border-bottom: none; }
    .info-label { font-weight: 700; color: #0F172A; }
    
    .modal-actions { display: flex; gap: 12px; margin-top: 24px; }
    .modal-actions button { flex: 1; }
    .btn-cancel-modal { background: #E2E8F0; color: #475569; border: none; border-radius: 999px; padding: 10px 16px; font-weight: 700; cursor: pointer; transition: transform 0.2s ease; }
    .btn-cancel-modal:hover { transform: translateY(-1px); }

    .footer-note { margin-top: 24px; padding: 20px 24px; background: #F8FAFC; border-radius: 22px; border: 1px solid rgba(148, 163, 184, 0.16); display: flex; align-items: center; justify-content: space-between; gap: 16px; color: #475569; font-size: 14px; }
    .footer-note strong { color: #0F172A; font-size: 14px; }

    @media (max-width: 1100px) { .stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .redemptions-table { min-width: 100%; } }
    @media (max-width: 860px) { .filter-bar { flex-direction: column; align-items: stretch; } .filter-group { width: 100%; } .search-input, .filter-select { width: 100%; } .stats-grid { grid-template-columns: 1fr; } }
  </style>

  <div class="admin-shell">
    <main class="content">
      <div class="page-header">
        <div>
          <h1 class="page-title">Kelola Penukaran</h1>
          <p class="page-description">Lihat dan kelola klaim penukaran poin oleh member.</p>
        </div>
        <div class="status-chip">Server Online (Real DB)</div>
      </div>

      <div class="stats-grid">
        <div class="stat-card stat-blue">
          <div class="stat-icon">📋</div>
          <div><div class="stat-label">Total Klaim</div><p class="stat-value" id="stat-total">0</p><p class="stat-note">Semua Penukaran</p></div>
        </div>
        <div class="stat-card stat-orange">
          <div class="stat-icon">⏳</div>
          <div><div class="stat-label">Menunggu</div><p class="stat-value" id="stat-pending">0</p><p class="stat-note">Belum Dikonfirmasi</p></div>
        </div>
        <div class="stat-card stat-green">
          <div class="stat-icon">✅</div>
          <div><div class="stat-label">Disetujui</div><p class="stat-value" id="stat-approved">0</p><p class="stat-note">Penukaran Berhasil</p></div>
        </div>
      </div>

      <div class="filter-bar">
        <div class="filter-group" style="flex:1;">
          <input type="text" id="search-input" class="search-input" placeholder="Cari nama member, ID penukaran...">
          <select id="filter-status" class="filter-select">
            <option value="">Semua Status</option>
            <option value="pending">Menunggu</option>
            <option value="berhasil">Disetujui</option>
            <option value="ditolak">Ditolak</option>
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
          <span>Konfirmasi penukaran untuk memberikan reward kepada member. Tolak jika reward tidak tersedia (poin akan dikembalikan otomatis).</span>
        </div>
      </div>
      
      <form id="action-form" method="POST" style="display: none;">
        <input type="hidden" name="action" id="form-action">
        <input type="hidden" name="redemption_id" id="form-redemption-id">
      </form>
    </main>
  </div>

  <!-- Modal Detail & Konfirmasi -->
  <div class="modal" id="modal-redemption">
    <div class="modal-content">
      <div class="modal-header" id="modal-title">Detail Penukaran</div>
      <div class="info-box">
        <div class="info-row"><span class="info-label">ID Penukaran:</span><span id="detail-id"></span></div>
        <div class="info-row"><span class="info-label">Tanggal:</span><span id="detail-date"></span></div>
        <div class="info-row"><span class="info-label">Member:</span><span id="detail-member"></span></div>
        <div class="info-row"><span class="info-label">No. HP:</span><span id="detail-phone"></span></div>
        <div class="info-row"><span class="info-label">Reward:</span><span id="detail-reward"></span></div>
        <div class="info-row"><span class="info-label">Poin Digunakan:</span><span id="detail-points" style="color:#EA580C;font-weight:700"></span></div>
        <div class="info-row"><span class="info-label">Status:</span><span id="detail-status"></span></div>
        <div class="info-row" id="detail-token-row" style="display:none;"><span class="info-label">Token WiFi:</span><span id="detail-token" style="font-family:monospace;font-weight:bold;color:#1D4ED8;"></span></div>
      </div>
      <div id="modal-actions" class="modal-actions">
        <button type="button" class="btn-cancel-modal" onclick="closeModal()">Tutup</button>
      </div>
    </div>
  </div>

  <script>
    const redemptions = <?php echo $redemptionsJson; ?>;

    const tableBody = document.querySelector('#redemptions-table tbody');
    const modal = document.getElementById('modal-redemption');
    const searchInput = document.getElementById('search-input');
    const filterStatus = document.getElementById('filter-status');

    const actionForm = document.getElementById('action-form');
    const formAction = document.getElementById('form-action');
    const formRedemptionId = document.getElementById('form-redemption-id');

    function updateStats() {
      document.getElementById('stat-total').textContent = redemptions.length;
      document.getElementById('stat-pending').textContent = redemptions.filter(r => r.status === 'pending').length;
      document.getElementById('stat-approved').textContent = redemptions.filter(r => r.status === 'berhasil').length;
    }

    function getStatusBadge(status) {
      if (status === 'pending') return '<span class="badge-status badge-pending">Menunggu</span>';
      if (status === 'berhasil') return '<span class="badge-status badge-approved">Disetujui</span>';
      if (status === 'ditolak') return '<span class="badge-status badge-rejected">Ditolak</span>';
      return '';
    }

    function submitAction(action, id) {
      formAction.value = action;
      formRedemptionId.value = id;
      actionForm.submit();
    }

    function renderTable() {
      const q = searchInput.value.trim().toLowerCase();
      const status = filterStatus.value;
      tableBody.innerHTML = '';
      
      const list = redemptions.filter(r => {
        if (q && !(String(r.id).includes(q) || r.member.toLowerCase().includes(q) || r.phone.includes(q))) return false;
        if (status && r.status !== status) return false;
        return true;
      });

      if (!list.length) {
        tableBody.innerHTML = '<tr><td colspan="7" style="padding:22px;text-align:center;color:#64748B">Tidak ada penukaran yang sesuai filter.</td></tr>';
        return;
      }

      list.forEach(r => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td><strong>#${r.id}</strong></td>
          <td>${r.date}</td>
          <td>${r.member}<span class="secondary-text">${r.nim}</span></td>
          <td>${r.reward}</td>
          <td style="color:#EA580C;font-weight:700;">${r.points} Poin</td>
          <td>${getStatusBadge(r.status)}</td>
          <td>
            <div style="display:flex;gap:8px">
              <button class="action-button btn-view" onclick="openDetail('${r.id}')">Lihat</button>
              ${r.status === 'pending' ? `
                <button class="action-button btn-approve" onclick="if(confirm('Setujui penukaran ini?')) submitAction('approve', ${r.id})">Setujui</button>
                <button class="action-button btn-reject" onclick="if(confirm('Tolak penukaran ini dan kembalikan poin?')) submitAction('reject', ${r.id})">Tolak</button>
              ` : `
                <button class="action-button btn-reject" onclick="if(confirm('Hapus riwayat permanen?')) submitAction('delete', ${r.id})">Hapus</button>
              `}
            </div>
          </td>
        `;
        tableBody.appendChild(tr);
      });
    }

    function openDetail(id) {
      const r = redemptions.find(x => String(x.id) === String(id));
      if (!r) return;
      document.getElementById('detail-id').textContent = '#' + r.id;
      document.getElementById('detail-date').textContent = r.date;
      document.getElementById('detail-member').textContent = r.member;
      document.getElementById('detail-phone').textContent = r.phone;
      document.getElementById('detail-reward').textContent = r.reward;
      document.getElementById('detail-points').textContent = r.points + ' Poin';
      document.getElementById('detail-status').innerHTML = getStatusBadge(r.status);
      
      const tokenRow = document.getElementById('detail-token-row');
      if (r.token_wifi) {
          tokenRow.style.display = 'flex';
          document.getElementById('detail-token').textContent = r.token_wifi;
      } else {
          tokenRow.style.display = 'none';
      }

      modal.classList.add('active');
    }

    function closeModal() {
      modal.classList.remove('active');
    }

    searchInput.addEventListener('input', renderTable);
    filterStatus.addEventListener('change', renderTable);

    updateStats();
    renderTable();
  </script>
