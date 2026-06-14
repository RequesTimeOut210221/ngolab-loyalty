<?php
/* Author: Shaena */
 
if (!defined('IN_ADMIN')) exit('No direct script access allowed'); 
require_once '../koneksi.php';

// Handle Actions (Update Status, Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $orderId = intval($_POST['order_id']);

    if ($action === 'update_status') {
        $status = $_POST['status'];
        
        // Cek status saat ini
        $stmt = $conn->prepare("SELECT status, total_harga, id_member FROM TABEL_PESANAN WHERE id_pesanan = ?");
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $pesanan = $stmt->get_result()->fetch_assoc();
        
        if ($pesanan && $pesanan['status'] !== $status) {
            $stmtUpdate = $conn->prepare("UPDATE TABEL_PESANAN SET status = ? WHERE id_pesanan = ?");
            $stmtUpdate->bind_param("si", $status, $orderId);
            $stmtUpdate->execute();

            // Jika status berubah menjadi selesai, tambahkan poin ke member
            if ($status === 'selesai' && $pesanan['status'] !== 'selesai') {
                $poin_didapat = floor($pesanan['total_harga'] / 10000);
                $stmtPoin = $conn->prepare("UPDATE TABEL_MEMBER SET saldo_poin = saldo_poin + ? WHERE id_member = ?");
                $stmtPoin->bind_param("ii", $poin_didapat, $pesanan['id_member']);
                $stmtPoin->execute();
            }
        }
    } elseif ($action === 'delete') {
        $stmtDel = $conn->prepare("DELETE FROM TABEL_PESANAN WHERE id_pesanan = ?");
        $stmtDel->bind_param("i", $orderId);
        $stmtDel->execute();
    }
    
    // Redirect to refresh
    echo "<script>window.location.href='admin_dashboard.php?page=pesanan';</script>";
    exit;
}

// Fetch all orders
$query = "
    SELECT p.id_pesanan, p.tanggal_pesan, p.total_harga, p.poin_didapat, p.status, 
           m.username, m.nim, m.no_hp, m.saldo_poin 
    FROM TABEL_PESANAN p
    JOIN TABEL_MEMBER m ON p.id_member = m.id_member
    ORDER BY p.tanggal_pesan DESC
";
$result = $conn->query($query);
$db_orders = [];
while ($row = $result->fetch_assoc()) {
    $db_orders[] = [
        'id' => $row['id_pesanan'],
        'date' => date('d M Y H:i', strtotime($row['tanggal_pesan'])),
        'name' => htmlspecialchars($row['username']),
        'nim' => htmlspecialchars($row['nim'] ?? ''),
        'phone' => htmlspecialchars($row['no_hp']),
        'total' => (int)$row['total_harga'],
        'points' => (int)$row['poin_didapat'],
        'status' => $row['status'],
        'balance' => (int)$row['saldo_poin']
    ];
}
$ordersJson = json_encode($db_orders);
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
    .status-chip { padding: 10px 18px; border-radius: 999px; background: #DCFCE7; color: #166534; font-size: 13px; font-weight: 700; border: 1px solid rgba(22, 163, 74, 0.14); }
    
    .stats-grid { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 18px; margin-bottom: 22px; }
    .stat-card { background: var(--panel-bg); border-radius: 24px; padding: 22px; box-shadow: var(--shadow); border: 1px solid var(--panel-border); display: flex; align-items: center; gap: 14px; }
    .stat-icon { width: 46px; height: 46px; border-radius: 14px; display: grid; place-items: center; font-size: 18px; font-weight: 700; color: white; }
    .stat-blue .stat-icon { background: #DBEAFE; color: #1D4ED8; }
    .stat-orange .stat-icon { background: #FEF3C7; color: #D97706; }
    .stat-indigo .stat-icon { background: #E0E7FF; color: #3730A3; }
    .stat-green .stat-icon { background: #DCFCE7; color: #15803D; }
    .stat-red .stat-icon { background: #FEE2E2; color: #B91C1C; }
    .stat-label { color: #64748B; font-size: 12px; font-weight: 700; letter-spacing: 0.10em; text-transform: uppercase; margin-bottom: 6px; }
    .stat-value { margin: 0; font-size: 24px; font-weight: 800; color: #0F172A; }
    .stat-note { margin: 6px 0 0; color: #64748B; font-size: 13px; }

    .filter-bar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 14px; padding: 20px 24px; background: var(--panel-bg); border-radius: 24px; box-shadow: var(--shadow); border: 1px solid var(--panel-border); margin-bottom: 22px; }
    .filter-group { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; }
    .filter-group label { color: #475569; font-size: 14px; font-weight: 700; white-space: nowrap; }
    .filter-input, .search-input, .filter-select { border: 1px solid #CBD5E1; border-radius: 16px; background: #F8FAFC; padding: 14px 16px; font-size: 14px; color: #0F172A; outline: none; min-width: 220px; transition: border-color 0.2s ease, box-shadow 0.2s ease; }
    .filter-input:focus, .search-input:focus, .filter-select:focus { border-color: #F97316; box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.08); }
    
    .table-card { background: var(--panel-bg); border-radius: 28px; padding: 20px; box-shadow: var(--shadow); border: 1px solid var(--panel-border); }
    .orders-table { width: 100%; border-collapse: collapse; min-width: 900px; }
    .orders-table th, .orders-table td { padding: 18px 14px; font-size: 14px; color: #334155; vertical-align: middle; }
    .orders-table th { text-align: left; color: #64748B; font-size: 12px; letter-spacing: 0.12em; text-transform: uppercase; font-weight: 700; padding-bottom: 12px; }
    .orders-table tbody tr { border-bottom: 1px solid #E2E8F0; }
    .orders-table tbody tr:last-child { border-bottom: none; }
    .secondary-text { display: block; color: #94A3B8; font-size: 12px; margin-top: 6px; }

    .badge-status { display: inline-flex; align-items: center; padding: 8px 12px; border-radius: 999px; font-size: 12px; font-weight: 700; }
    .badge-pending-status { background: #FEF3C7; color: #92400E; }
    .badge-processing-status { background: #DBEAFE; color: #1D4ED8; }
    .badge-success-status { background: #DCFCE7; color: #166534; }
    .badge-cancel-status { background: #FEE2E2; color: #991B1B; }

    .action-button { border: none; border-radius: 999px; padding: 10px 16px; font-size: 13px; font-weight: 700; cursor: pointer; transition: transform 0.2s ease; min-width: 90px; }
    .btn-process { background: #2563EB; color: white; }
    .btn-done { background: #16A34A; color: white; }
    .btn-cancel { background: #DC2626; color: white; }
    .btn-disabled { background: #E2E8F0; color: #475569; cursor: default; opacity: 0.75; }
    .action-button:hover:not(.btn-disabled) { transform: translateY(-1px); }

    .footer-note { margin-top: 24px; padding: 20px 24px; background: #F8FAFC; border-radius: 22px; border: 1px solid rgba(148, 163, 184, 0.16); display: flex; align-items: center; justify-content: space-between; gap: 16px; color: #475569; font-size: 14px; }
    .footer-note strong { color: #0F172A; font-size: 14px; }

    @media (max-width: 1100px) { .stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .orders-table { min-width: 100%; } }
    @media (max-width: 860px) { .filter-bar { flex-direction: column; align-items: stretch; } .filter-group { width: 100%; } .filter-input, .search-input, .filter-select { width: 100%; } .stats-grid { grid-template-columns: 1fr; } }
  </style>

  <div class="admin-shell">
    <main class="content">
      <div class="page-header">
        <div>
          <h1 class="page-title">Kelola Pesanan</h1>
          <p class="page-description">Lihat dan kelola semua pesanan masuk dari konsumen.</p>
        </div>
        <div class="status-chip">Server Online (Real DB)</div>
      </div>

      <div class="stats-grid">
        <div class="stat-card stat-blue">
          <div class="stat-icon">📦</div>
          <div><div class="stat-label">Total Pesanan</div><p class="stat-value" id="stat-total">0</p><p class="stat-note">Semua Pesanan</p></div>
        </div>
        <div class="stat-card stat-orange">
          <div class="stat-icon">⏳</div>
          <div><div class="stat-label">Pending</div><p class="stat-value" id="stat-pending">0</p><p class="stat-note">Menunggu Proses</p></div>
        </div>
        <div class="stat-card stat-indigo">
          <div class="stat-icon">🔄</div>
          <div><div class="stat-label">Diproses</div><p class="stat-value" id="stat-processing">0</p><p class="stat-note">Sedang Diproses</p></div>
        </div>
        <div class="stat-card stat-green">
          <div class="stat-icon">✅</div>
          <div><div class="stat-label">Selesai</div><p class="stat-value" id="stat-success">0</p><p class="stat-note">Selesai / Lunas</p></div>
        </div>
        <div class="stat-card stat-red">
          <div class="stat-icon">❌</div>
          <div><div class="stat-label">Dibatalkan</div><p class="stat-value" id="stat-canceled">0</p><p class="stat-note">Pesanan Dibatalkan</p></div>
        </div>
      </div>

      <div class="filter-bar">
        <div class="filter-group">
          <select id="status-filter" class="filter-select">
            <option value="all">Semua Status</option>
            <option value="pending">Pending</option>
            <option value="diproses">Diproses</option>
            <option value="selesai">Selesai</option>
            <option value="canceled">Dibatalkan</option>
          </select>
        </div>
        <div class="filter-group" style="flex:1; justify-content:flex-end;">
          <input type="text" id="search-input" class="search-input" placeholder="Cari nama member / nomor HP / ID pesanan...">
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

      <form id="action-form" method="POST" style="display: none;">
        <input type="hidden" name="action" id="form-action">
        <input type="hidden" name="order_id" id="form-order-id">
        <input type="hidden" name="status" id="form-status">
      </form>
    </main>
  </div>

  <script>
    const orders = <?php echo $ordersJson; ?>;
    const state = { filterStatus: 'all', search: '' };

    const statTotal = document.getElementById('stat-total');
    const statPending = document.getElementById('stat-pending');
    const statProcessing = document.getElementById('stat-processing');
    const statSuccess = document.getElementById('stat-success');
    const statCanceled = document.getElementById('stat-canceled');
    const ordersTableBody = document.querySelector('#orders-table tbody');
    const statusFilter = document.getElementById('status-filter');
    const searchInput = document.getElementById('search-input');
    const actionForm = document.getElementById('action-form');
    const formAction = document.getElementById('form-action');
    const formOrderId = document.getElementById('form-order-id');
    const formStatus = document.getElementById('form-status');

    function updateStats() {
      statTotal.textContent = orders.length;
      statPending.textContent = orders.filter(o => o.status === 'pending').length;
      statProcessing.textContent = orders.filter(o => o.status === 'diproses').length;
      statSuccess.textContent = orders.filter(o => o.status === 'selesai').length;
      statCanceled.textContent = orders.filter(o => o.status === 'canceled').length;
    }

    function formatCurrency(value) {
      return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value);
    }

    function createStatusBadge(status) {
      const badge = document.createElement('span');
      let classMap = { 'pending': 'badge-pending-status', 'diproses': 'badge-processing-status', 'selesai': 'badge-success-status', 'canceled': 'badge-cancel-status' };
      let textMap = { 'pending': 'Pending', 'diproses': 'Diproses', 'selesai': 'Selesai', 'canceled': 'Dibatalkan' };
      badge.className = 'badge-status ' + (classMap[status] || 'badge-pending-status');
      badge.textContent = textMap[status] || status;
      return badge;
    }

    function button(text, styleClass, disabled, handler) {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = `action-button ${styleClass}`;
      btn.textContent = text;
      if (disabled) { btn.classList.add('btn-disabled'); btn.disabled = true; }
      else btn.addEventListener('click', handler);
      return btn;
    }

    function submitAction(action, orderId, status = '') {
      formAction.value = action;
      formOrderId.value = orderId;
      formStatus.value = status;
      actionForm.submit();
    }

    function buildActionButtons(order) {
      const wrapper = document.createElement('div');
      wrapper.style.display = 'flex';
      wrapper.style.flexWrap = 'wrap';
      wrapper.style.gap = '8px';
      
      if (order.status === 'pending') {
        wrapper.appendChild(button('Proses', 'btn-process', false, () => submitAction('update_status', order.id, 'diproses')));
        wrapper.appendChild(button('Batal', 'btn-cancel', false, () => submitAction('update_status', order.id, 'canceled')));
      } else if (order.status === 'diproses') {
        wrapper.appendChild(button('Selesai', 'btn-done', false, () => submitAction('update_status', order.id, 'selesai')));
        wrapper.appendChild(button('Batal', 'btn-cancel', false, () => submitAction('update_status', order.id, 'canceled')));
      } else if (order.status === 'selesai' || order.status === 'canceled') {
        wrapper.appendChild(button(order.status === 'selesai' ? 'Selesai' : 'Dibatalkan', 'btn-disabled', true, () => {}));
        wrapper.appendChild(button('Hapus', 'btn-cancel', false, () => {
            if (confirm('Hapus pesanan ini permanen?')) submitAction('delete', order.id);
        }));
      }
      return wrapper;
    }

    function renderOrders() {
      ordersTableBody.innerHTML = '';
      const query = state.search.toLowerCase().trim();
      
      const filtered = orders.filter(o => {
        const matchStatus = state.filterStatus === 'all' || o.status === state.filterStatus;
        const matchSearch = query === '' || [o.id, (o.name||''), (o.nim||''), (o.phone||'')].some(val => String(val).toLowerCase().includes(query));
        return matchStatus && matchSearch;
      });

      if (!filtered.length) {
        ordersTableBody.innerHTML = '<tr><td colspan="8" style="padding:22px;text-align:center;color:#64748B;">Tidak ada pesanan yang sesuai filter.</td></tr>';
        return;
      }

      filtered.forEach(order => {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>#${order.id}</td>
          <td>${order.date}</td>
          <td>${order.name}<span class="secondary-text">${order.nim} • Saldo: ${order.balance} Poin</span></td>
          <td>${order.phone}</td>
          <td style="color:#EA580C;font-weight:700;">${formatCurrency(order.total)}</td>
          <td style="color:#16A34A;font-weight:700;">+${order.points} Poin</td>
          <td></td>
          <td></td>
        `;
        row.children[6].appendChild(createStatusBadge(order.status));
        row.children[7].appendChild(buildActionButtons(order));
        ordersTableBody.appendChild(row);
      });
    }

    searchInput.addEventListener('input', (e) => { state.search = e.target.value; renderOrders(); });
    statusFilter.addEventListener('change', (e) => { state.filterStatus = e.target.value; renderOrders(); });

    updateStats();
    renderOrders();
  </script>
