
import { ApiService, SessionManager, showToast } from './api.js';
import { initUserData, switchTab } from './app.js';

document.addEventListener('DOMContentLoaded', () => {
  // Setup Profile Actions only if elements exist
  setupProfileForm();
  setupRedeemCatalog();
});

// Global State for Rewards
let stateRewards = [];
let rewardSearch = '';
let rewardCategory = 'all';

// 👤 Setup Profile Details & Avatar Photo Editor
function setupProfileForm() {
  const profileForm = document.getElementById('profile-edit-form');
  const avatarInput = document.getElementById('profile-avatar-input');
  const avatarPreview = document.getElementById('profile-avatar-preview');
  
  if (avatarInput && avatarPreview) {
    // Show instant preview on file selection
    avatarInput.addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (file) {
        if (file.size > 2 * 1024 * 1024) { // 2MB Limit
          showToast('Ukuran file maksimal adalah 2MB!', 'error');
          avatarInput.value = '';
          return;
        }
        
        const reader = new FileReader();
        reader.onload = (event) => {
          avatarPreview.src = event.target.result;
        };
        reader.readAsDataURL(file);
      }
    });
  }

  if (profileForm) {
    profileForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      
      const name = document.getElementById('profile-edit-name').value.trim();
      const fileInput = document.getElementById('profile-avatar-input');
      const avatarFile = fileInput && fileInput.files[0] ? fileInput.files[0] : null;
      
      if (!name) {
        showToast('Nama tidak boleh kosong!', 'error');
        return;
      }
      
      // Show saving indicator
      const saveBtn = profileForm.querySelector('button[type="submit"]');
      const originalText = saveBtn.innerHTML;
      saveBtn.disabled = true;
      saveBtn.innerHTML = `<span>Menyimpan...</span>`;
      
      try {
        const res = await ApiService.updateProfile(name, avatarFile);
        if (res.status === 'success') {
          await initUserData(); // reload state
          // Update profile page view
          document.getElementById('profile-edit-name').value = name;
        }
      } catch (error) {
        showToast('Gagal menghubungkan ke backend.', 'error');
      } finally {
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalText;
      }
    });
  }
}

async function setupRedeemCatalog() {
  const grid = document.getElementById('rewards-catalog-grid');
  if (!grid) return;

  const searchInput = document.getElementById('reward-search-input');
  const catFilter = document.getElementById('reward-category-filter');

  grid.innerHTML = Array(3).fill(0).map(() => `
    <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100 flex items-center space-x-4">
      <div class="w-16 h-16 skeleton-loading rounded-lg"></div>
      <div class="flex-1 space-y-2">
        <div class="h-4 skeleton-loading w-2/3 rounded"></div>
        <div class="h-3 skeleton-loading w-1/3 rounded"></div>
      </div>
    </div>
  `).join('');

  try {
    const rewards = await ApiService.getRewards();
    stateRewards = rewards;

    if (catFilter && catFilter.options.length <= 1) {
      const cats = [...new Set(rewards.flatMap(r => r.nama_kategori ? [r.nama_kategori] : []))];
      catFilter.innerHTML = `<option value="all">Semua Kategori</option>` +
        cats.map(c => `<option value="${c.toLowerCase()}">${c}</option>`).join('');
        
      catFilter.addEventListener('change', (e) => {
        rewardCategory = e.target.value;
        renderRewards();
      });
      
      if (searchInput) {
        searchInput.addEventListener('input', (e) => {
          rewardSearch = e.target.value.toLowerCase().trim();
          renderRewards();
        });
      }
    }

    renderRewards();
  } catch (error) {
    grid.innerHTML = `<div class="py-4 text-center text-red-500">Gagal memuat katalog reward.</div>`;
  }
}

function renderRewards() {
  const grid = document.getElementById('rewards-catalog-grid');
  const currentPoints = SessionManager.getPoints();

  const filtered = stateRewards.filter(r => {
    const catMatch = rewardCategory === 'all' || (r.nama_kategori && r.nama_kategori.toLowerCase() === rewardCategory);
    const searchMatch = rewardSearch === '' || (r.nama_reward || '').toLowerCase().includes(rewardSearch);
    return catMatch && searchMatch;
  });

  if (filtered.length === 0) {
    grid.innerHTML = `<div class="col-span-full py-4 text-center text-gray-400">Tidak ada item reward yang sesuai filter.</div>`;
    return;
  }

  grid.innerHTML = filtered.map(reward => {
    const isAffordable = currentPoints >= reward.poin_dibutuhkan;
    const btnColor = isAffordable 
      ? 'bg-orange-500 hover:bg-orange-600 text-white shadow-sm' 
      : 'bg-gray-100 text-gray-400 cursor-not-allowed';
    
    return `
      <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 hover-scale flex flex-col justify-between">
        <div>
          <img src="${reward.gambar}" alt="${reward.nama_reward}" class="h-40 w-full object-cover">
          <div class="p-4">
            <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600">
              ${reward.nama_kategori || 'General'}
            </span>
            <h4 class="font-bold text-slate-800 mt-2 text-base leading-tight">${reward.nama_reward}</h4>
            <p class="text-xs text-gray-500 mt-1">Stok: ${reward.stok}</p>
          </div>
        </div>
        
        <div class="p-4 pt-0 border-t border-gray-50 mt-auto">
          <div class="flex items-center justify-between my-3">
            <span class="font-extrabold text-orange-600">${reward.poin_dibutuhkan} Poin</span>
          </div>
          <button class="w-full font-bold py-2 rounded-lg text-sm transition-all ${isAffordable ? 'bg-slate-800 hover:bg-slate-900 text-white' : 'bg-gray-100 text-gray-400 cursor-not-allowed'}"
                  onclick="window.ProfileActions.redeemItem(${reward.id_reward})"
                  ${!isAffordable ? 'disabled' : ''}>
            Tukar Poin
          </button>
        </div>
      </div>
    `;
  }).join('');
}

async function redeemItem(id_reward) {
  const confirmRedeem = confirm('Anda yakin ingin menukar poin dengan reward ini?');
  if (!confirmRedeem) return;
  
  try {
    const res = await ApiService.redeemReward(id_reward);
    if (res.status === 'success') {
      // Reload profile & catalog view
      await initUserData();
      await setupRedeemCatalog();
      
      if (res.token_wifi) {
        alert(`Penukaran Sukses!\nToken WiFi VIP Anda: ${res.token_wifi}\nGunakan ini untuk login internet.`);
      }
      
      // Auto switch to history tab to see redemption
      switchTab('riwayat');
    }
  } catch (error) {
    showToast('Koneksi backend gagal.', 'error');
  }
}

// Hook up functions globally
window.ProfileActions = {
  redeemItem,
  refreshRewards: setupRedeemCatalog,
  claimShareBonus
};

// 🎁 Claim Share Bonus +10 Poin
async function claimShareBonus() {

  const confirmShare = confirm(
    'Claim bonus share Instagram/X +10 poin?'
  );

  if (!confirmShare) return;

  try {

    const response = await fetch('api/share_bonus.php', {
      method: 'POST'
    });

    const res = await response.json();

    if (res.status === 'success') {

      showToast(
        '+10 poin berhasil ditambahkan!',
        'success'
      );

      await initUserData();

    } else {

      showToast(
        res.message || 'Bonus sudah pernah diklaim',
        'error'
      );

    }

  } catch (error) {

    showToast(
      'Gagal terhubung ke server.',
      'error'
    );

  }
}
