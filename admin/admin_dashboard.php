<?php
session_start();
require_once '../koneksi.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../index.html');
    exit;
}

define('IN_ADMIN', true);

$page = $_GET['page'] ?? 'pos';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ngolab POS & Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .active-tab-btn { background-color: #F97316; color: white; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); }
        .inactive-tab-btn { color: #9CA3AF; }
        .inactive-tab-btn:hover { background-color: #1E293B; color: white; }
        
        /* Mobile sidebar toggle */
        @media (max-width: 768px) {
            #sidebar { transform: translateX(-100%); transition: transform 0.3s ease-in-out; position: fixed; z-index: 40; height: 100vh; }
            #sidebar.open { transform: translateX(0); }
            #overlay.open { display: block; }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen text-slate-800 flex flex-col md:flex-row font-sans">

    <!-- Mobile Overlay -->
    <div id="overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden md:hidden"></div>

    <!-- 📂 Admin Sidebar Navigation -->
    <aside id="sidebar" class="w-64 bg-slate-900 text-gray-300 flex flex-col justify-between shrink-0 border-r border-slate-800 overflow-y-auto hidden md:flex">
      <div>
        <div class="h-16 flex items-center px-6 border-b border-slate-800 bg-slate-950">
          <span class="text-xl mr-2 animate-pulse">☕</span>
          <span class="font-extrabold text-sm tracking-wider text-orange-500">NGOLAB ADMIN</span>
        </div>

        <nav class="p-4 space-y-1">
          <a href="admin_dashboard.php?page=pos" class="w-full flex items-center space-x-3 px-4 py-2.5 rounded-xl text-xs font-bold transition <?= $page == 'pos' ? 'active-tab-btn' : 'inactive-tab-btn' ?>">
            <span>📂</span> <span>Dashboard POS</span>
          </a>
          <a href="admin_dashboard.php?page=member" class="w-full flex items-center space-x-3 px-4 py-2.5 rounded-xl text-xs font-bold transition <?= $page == 'member' ? 'active-tab-btn' : 'inactive-tab-btn' ?>">
            <span>👥</span> <span>Kelola Member</span>
          </a>
          <a href="admin_dashboard.php?page=staff" class="w-full flex items-center space-x-3 px-4 py-2.5 rounded-xl text-xs font-bold transition <?= $page == 'staff' ? 'active-tab-btn' : 'inactive-tab-btn' ?>">
            <span>👤</span> <span>Kelola Staff</span>
          </a>
          <a href="admin_dashboard.php?page=menu" class="w-full flex items-center space-x-3 px-4 py-2.5 rounded-xl text-xs font-bold transition <?= $page == 'menu' ? 'active-tab-btn' : 'inactive-tab-btn' ?>">
            <span>🍔</span> <span>Kelola Menu</span>
          </a>
          <a href="admin_dashboard.php?page=kategori" class="w-full flex items-center space-x-3 px-4 py-2.5 rounded-xl text-xs font-bold transition <?= $page == 'kategori' ? 'active-tab-btn' : 'inactive-tab-btn' ?>">
            <span>🏷️</span> <span>Kelola Kategori</span>
          </a>
          <a href="admin_dashboard.php?page=pesanan" class="w-full flex items-center space-x-3 px-4 py-2.5 rounded-xl text-xs font-bold transition <?= $page == 'pesanan' ? 'active-tab-btn' : 'inactive-tab-btn' ?>">
            <span>🛒</span> <span>Kelola Pesanan</span>
          </a>
          <a href="admin_dashboard.php?page=reward" class="w-full flex items-center space-x-3 px-4 py-2.5 rounded-xl text-xs font-bold transition <?= $page == 'reward' ? 'active-tab-btn' : 'inactive-tab-btn' ?>">
            <span>🎁</span> <span>Kelola Reward</span>
          </a>
          <a href="admin_dashboard.php?page=penukaran" class="w-full flex items-center space-x-3 px-4 py-2.5 rounded-xl text-xs font-bold transition <?= $page == 'penukaran' ? 'active-tab-btn' : 'inactive-tab-btn' ?>">
            <span>🎟️</span> <span>Kelola Penukaran</span>
          </a>
          <a href="admin_dashboard.php?page=feedback" class="w-full flex items-center space-x-3 px-4 py-2.5 rounded-xl text-xs font-bold transition <?= $page == 'feedback' ? 'active-tab-btn' : 'inactive-tab-btn' ?>">
            <span>💬</span> <span>Kelola Feedback</span>
          </a>
        </nav>
      </div>

      <div class="p-4 border-t border-slate-800 bg-slate-950 flex items-center justify-between">
        <div class="flex items-center space-x-2">
          <div class="w-8 h-8 rounded-full bg-orange-500 text-white font-bold flex items-center justify-center text-xs uppercase">
            <?= substr($_SESSION['admin_username'], 0, 1) ?>
          </div>
          <div class="text-left">
            <p class="text-[10px] font-bold text-white leading-tight capitalize"><?= $_SESSION['admin_role'] ?></p>
            <p class="text-[9px] text-gray-400 leading-none"><?= htmlspecialchars($_SESSION['admin_username']) ?></p>
          </div>
        </div>
        <a href="logout.php" class="text-xs text-red-400 font-bold hover:underline">Keluar</a>
      </div>
    </aside>

    <!-- 💻 Main Content Wrapper -->
    <div class="flex-1 flex flex-col min-w-0">
      
      <!-- Topbar Header -->
      <header class="h-16 bg-white border-b border-gray-200 px-6 flex items-center justify-between shrink-0 shadow-sm sticky top-0 z-20">
        <div class="flex items-center space-x-4">
          <button onclick="toggleSidebar()" class="md:hidden text-gray-500 hover:text-orange-500 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
          </button>
          <h2 id="admin-current-title" class="text-lg font-bold text-slate-800 capitalize">
            <?= $page === 'pos' ? 'Dashboard Kasir POS' : 'Modul ' . htmlspecialchars($page, ENT_QUOTES, 'UTF-8') ?>
          </h2>
        </div>
        <div class="flex items-center space-x-4">
          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 hidden sm:inline-flex">
            <span class="w-2 h-2 rounded-full bg-green-500 mr-1.5 animate-ping"></span>
            Server Online
          </span>
          <a href="../index.html" target="_blank" class="text-xs font-bold text-blue-500 hover:underline">Buka Aplikasi</a>
        </div>
      </header>

      <!-- Main Working Panel -->
      <main class="flex-grow p-4 md:p-6 overflow-y-auto space-y-6">
        
        <?php if ($page === 'pos'): ?>
        <!-- ================= TAB: POS ADMIN ================= -->
        <section class="space-y-6">
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-100 space-y-6">
              <div class="border-b border-gray-100 pb-4">
                <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">Kasir Manual (Earning Poin)</h3>
                <p class="text-xs text-gray-400 mt-0.5">Input belanja pelanggan offline untuk memberikan poin loyalty.</p>
              </div>

              <form id="pos-earn-form" class="space-y-4" onsubmit="alert('Fitur Suntik Poin Manual sedang dalam pengembangan.'); return false;">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Nomor HP / NIM Member</label>
                    <input type="text" placeholder="Contoh: 1202210045" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none font-medium"/>
                  </div>
                  <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Total Belanja (Rupiah)</label>
                    <input type="number" id="pos-amount" placeholder="Contoh: 50000" required oninput="document.getElementById('pos-points-preview').innerText = '+' + Math.floor(this.value/10000) + ' Poin'" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none font-medium"/>
                  </div>
                </div>

                <div class="flex items-center justify-between p-3 bg-orange-50 rounded-xl border border-orange-100">
                  <span class="text-xs text-orange-800 font-medium">Preview Perolehan Poin (Kelipatan Rp10.000):</span>
                  <span id="pos-points-preview" class="text-sm font-extrabold text-orange-600">+0 Poin</span>
                </div>

                <button type="submit" class="w-full bg-orange-500 text-white font-extrabold py-3 rounded-xl hover:bg-orange-600 transition shadow-md">
                  🔥 Proses Transaksi (Suntik Poin)
                </button>
              </form>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between">
              <div>
                <div class="border-b border-gray-100 pb-4 mb-4">
                  <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">Live Log Transaksi</h3>
                  <p class="text-xs text-gray-400 mt-0.5">Audit log poin sukses hari ini.</p>
                </div>
                <div class="space-y-3 max-h-[220px] overflow-y-auto pr-1">
                  <!-- Placeholder Log -->
                  <div class="flex items-center justify-between p-2.5 bg-gray-50 rounded-lg border border-gray-100 text-xs text-center text-gray-500">
                    Belum ada transaksi hari ini.
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <?php else: ?>
            <!-- Dynamic Include for Other Pages -->
            <?php
            switch ($page) {
                case 'member':
                    include 'kelola_member.php';
                    break;
                case 'staff':
                    include 'kelola_staff.php';
                    break;
                case 'menu':
                    include 'kelola_menu.php';
                    break;
                case 'kategori':
                    include 'kelola_kategori.php';
                    break;
                case 'pesanan':
                    include 'kelola_pesanan.php';
                    break;
                case 'reward':
                    include 'kelola_reward.php';
                    break;
                case 'penukaran':
                    include 'kelola_penukaran.php';
                    break;
                case 'feedback':
                    include 'kelola_feedback.php';
                    break;
                default:
                    echo "<p class='text-red-500'>Halaman tidak ditemukan.</p>";
                    break;
            }
            ?>
        <?php endif; ?>

      </main>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('hidden');
            document.getElementById('sidebar').classList.toggle('open');
            document.getElementById('overlay').classList.toggle('open');
        }
    </script>
</body>
</html>
