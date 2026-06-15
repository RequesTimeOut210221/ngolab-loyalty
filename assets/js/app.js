import { ApiService, SessionManager, showToast } from "./api.js";

// Global SPA Application State
const AppState = {
  activeTab: "home",
  cart: [],
  selectedCategory: "all",
  menuSearch: "",
  menus: [],
  user: null,
};

// Initialize App on DOM Load
document.addEventListener("DOMContentLoaded", async () => {
  setupNavigation();
  setupCartDrawer();

  // Check auth session
  if (!SessionManager.isLoggedIn()) {
    showLoginModal();
  } else {
    await initUserData();
  }

  // Load default catalog
  await loadCatalog();

  // Set up forms
  setupFeedbackForm();
});

// 🧭 Dynamic SPA Tab Switcher
function setupNavigation() {
  const navLinks = document.querySelectorAll("[data-tab-target]");
  navLinks.forEach((link) => {
    link.addEventListener("click", (e) => {
      e.preventDefault();

      // Prevent nav if not logged in
      if (!SessionManager.isLoggedIn()) {
        showToast("Silakan login terlebih dahulu!", "error");
        showLoginModal();
        return;
      }

      const targetTab = link.getAttribute("data-tab-target");
      switchTab(targetTab);
    });
  });

  // Handle profile click from redirect hashing
  if (window.location.hash) {
    const hash = window.location.hash.substring(1);
    if (["home", "katalog", "reward", "riwayat", "profil"].includes(hash)) {
      setTimeout(() => switchTab(hash), 100);
    }
  }
}

export function switchTab(tabId) {
  AppState.activeTab = tabId;

  // Hide all sections
  const sections = document.querySelectorAll(".tab-content");
  sections.forEach((section) => {
    section.classList.add("hidden-tab");
    section.classList.remove("active-tab");
  });

  // Show active section
  const activeSection = document.getElementById(`${tabId}-tab`);
  if (activeSection) {
    activeSection.classList.remove("hidden-tab");
    activeSection.classList.add("active-tab");
  }

  // Update Navbar Active States
  const navLinks = document.querySelectorAll("[data-tab-target]");
  navLinks.forEach((link) => {
    if (link.getAttribute("data-tab-target") === tabId) {
      link.classList.add("active-nav-link");
    } else {
      link.classList.remove("active-nav-link");
    }
  });

  // Reload data for specific tabs
  if (tabId === "home") {
    renderHomeView();
  } else if (tabId === "reward") {
    if (window.ProfileActions && window.ProfileActions.refreshRewards) {
      window.ProfileActions.refreshRewards();
    }
  } else if (tabId === "riwayat") {
    loadTransactionHistory();
  } else if (tabId === "profil") {
    renderProfileView();
  }
}

// 🔐 Unified Authentication Modal
function showLoginModal() {
  // Check if modal already exists
  let modal = document.getElementById("login-modal");
  if (!modal) {
    modal = document.createElement("div");
    modal.id = "login-modal";
    modal.className =
      "fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4 backdrop-filter backdrop-blur-sm";
    modal.innerHTML = `
      <div class="bg-white rounded-2xl w-full max-w-md p-8 shadow-2xl hover-scale">
        <div class="text-center mb-6">
          <span class="text-4xl">☕</span>
          <h3 class="text-2xl font-bold text-slate-800 mt-2">Masuk ke Ngolab</h3>
          <p class="text-sm text-gray-500 mt-1">Masukkan Nomor HP atau NIM Anda untuk memesan & mengklaim poin</p>
        </div>
        
        <div id="login-error-alert" class="hidden mb-4 p-3 bg-red-50 text-red-600 text-sm rounded-lg border border-red-100 font-medium"></div>
        <form id="login-form" class="space-y-4">
          <div>
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
              Email / Nomor HP / NIM / Username
            </label>
            <input type="text" id="login-identifier" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent text-lg font-medium text-center mb-3" placeholder="Masukkan ID Login Anda" required>
          </div>
          <div>
            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Password</label>
            <input type="password" id="login-password" placeholder="••••••••" required
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent text-lg font-medium text-center">
          </div>
          <button type="submit" id="login-submit-btn" class="w-full bg-orange-500 text-white font-bold py-3 rounded-xl hover:bg-orange-600 transition-all">
            Masuk Sekarang
          </button>
        </form>
        
        <div class="text-center mt-4 flex flex-col space-y-2">
          <button id="show-register-btn" class="text-xs text-orange-500 font-semibold hover:underline">
            Member Baru? Daftarkan Akun Anda
          </button>
        </div>
      </div>
    `;
    document.body.appendChild(modal);

    // Login Submission
    document
      .getElementById("login-form")
      .addEventListener("submit", async (e) => {
        e.preventDefault();
        const identifier = document
          .getElementById("login-identifier")
          .value.trim();
        const password = document.getElementById("login-password").value.trim();
        const alertBox = document.getElementById("login-error-alert");
        const submitBtn = document.getElementById("login-submit-btn");

        alertBox.classList.add("hidden");
        submitBtn.innerHTML = "Memproses...";
        submitBtn.disabled = true;

        try {
          const res = await ApiService.login(identifier, password);
          if (res.status === "success") {
            if (res.role === "admin" && res.redirect) {
              window.location.href = res.redirect;
              return;
            }
            modal.remove();
            await initUserData();
            switchTab("home");
          } else {
            alertBox.textContent =
              res.message || "Username atau password salah!";
            alertBox.classList.remove("hidden");
          }
        } catch (err) {
          alertBox.textContent = "Terjadi kesalahan pada server.";
          alertBox.classList.remove("hidden");
        } finally {
          submitBtn.innerHTML = "Masuk Sekarang";
          submitBtn.disabled = false;
        }
      });

    // Toggle to Register Modal
    document
      .getElementById("show-register-btn")
      .addEventListener("click", () => {
        showRegisterModal();
        modal.remove();
      });
  }
}

function showRegisterModal() {
  let modal = document.getElementById("register-modal");
  if (!modal) {
    modal = document.createElement("div");
    modal.id = "register-modal";
    modal.className =
      "fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4 backdrop-filter backdrop-blur-sm";
    modal.innerHTML = `
      <div class="bg-white rounded-2xl w-full max-w-md p-8 shadow-2xl">
        <div class="text-center mb-6">
          <span class="text-4xl">🎁</span>
          <h3 class="text-2xl font-bold text-slate-800 mt-2">Daftar Member Baru</h3>
          <p class="text-sm text-gray-500 mt-1">Dapatkan Welcome Bonus +10 Poin langsung!</p>
        </div>
        
        <form id="register-form" class="space-y-4">
          <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Nama Lengkap</label>
            <input type="text" id="reg-name" placeholder="Budi Raharjo" required
                   class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none">
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Email Kampus</label>
            <input type="email" id="reg-email" placeholder="budi@student.telkomuniversity.ac.id" required
                   class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none">
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Nomor HP / NIM</label>
            <input type="text" id="reg-phone" placeholder="1202210045" required
                   class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none">
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Password</label>
            <input type="password" id="reg-password" placeholder="Buat kata sandi..." required
                   class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none">
          </div>
          <button type="submit" class="w-full bg-orange-500 text-white font-bold py-3 rounded-xl hover:bg-orange-600 transition-all">
            Daftar & Ambil Poin Bonus
          </button>
        </form>
        
        <div class="text-center mt-4">
          <button id="show-login-btn" class="text-xs text-orange-500 font-semibold hover:underline">
            Sudah punya akun? Masuk di sini
          </button>
        </div>
      </div>
    `;
    document.body.appendChild(modal);

    document
      .getElementById("register-form")
      .addEventListener("submit", async (e) => {
        e.preventDefault();
        const username = document.getElementById("reg-name").value.trim();
        const email = document.getElementById("reg-email").value.trim();
        const phone = document.getElementById("reg-phone").value.trim();
        const password = document.getElementById("reg-password").value.trim();
        try {
          const res = await ApiService.register(
            username,
            email,
            phone,
            password,
          );
          if (res.status === "success") {
            modal.remove();
            await initUserData();
            switchTab("home");
            showToast("Registrasi berhasil! +10 Poin Bonus.", "success");
          } else {
            showToast(res.message || "Gagal mendaftar.", "error");
          }
        } catch (error) {
          showToast(
            "Gagal mendaftar. Pastikan backend database menyala.",
            "error",
          );
        }
      });

    document.getElementById("show-login-btn").addEventListener("click", () => {
      showLoginModal();
      modal.remove();
    });
  }
}

// 📦 Initialize User Data
async function initUserData() {
  try {
    const user = await ApiService.getProfile();
    AppState.user = user;

    // Set points in session storage for sync
    SessionManager.setPoints(user.saldo_poin);

    // Render views
    renderHomeView();
    updateNavbarUserInfo();

    // Start background live sync every 5 seconds
    setInterval(syncUserData, 5000);
  } catch (error) {
    console.error("Failed to load profile", error);
  }
}

// 🔄 Live Sync User Data
async function syncUserData() {
  if (!SessionManager.isLoggedIn()) return;
  try {
    const user = await ApiService.getProfile();
    if (AppState.user && AppState.user.saldo_poin !== user.saldo_poin) {
      AppState.user.saldo_poin = user.saldo_poin;
      SessionManager.setPoints(user.saldo_poin);
      updateNavbarUserInfo();

      // If user is currently viewing the profile or rewards tab, we might want to refresh it
      if (AppState.activeTab === "profil") {
        if (typeof renderProfileView === "function") renderProfileView();
      }
      if (AppState.activeTab === "reward") {
        if (window.ProfileActions && window.ProfileActions.refreshRewards)
          window.ProfileActions.refreshRewards();
      }
    }
  } catch (error) {
    // Silently fail on background sync errors
  }
}

function updateNavbarUserInfo() {
  const profileName = document.getElementById("nav-profile-name");
  const pointsBadge = document.getElementById("nav-points-badge");
  if (profileName && pointsBadge && AppState.user) {
    profileName.textContent = AppState.user.username;
    pointsBadge.textContent = `${AppState.user.saldo_poin} POIN`;
  }
}

// 🏡 Render Beranda (Home Tab)
function renderHomeView() {
  if (!AppState.user) return;

  const currentPoints = SessionManager.getPoints();

  // Set User Profile Card Details
  document.getElementById("member-name").textContent = AppState.user.username;
  document.getElementById("member-nim").textContent =
    AppState.user.nim || "N/A";
  document.getElementById("points-balance").textContent =
    `${currentPoints} POIN`;

  // Handle Tier Level Visual Styles
  const idBorder = document.getElementById("digital-id-border");
  const tierBadge = document.getElementById("member-tier-badge");
  if (idBorder && tierBadge) {
    const baseBorderClasses =
      "rounded-3xl p-1 shadow-lg hover:-translate-y-1 transition-transform duration-300 bg-gradient-to-br ";
    const baseTextClasses = "text-xs font-bold tracking-widest uppercase ";

    if (currentPoints < 20) {
      idBorder.className = baseBorderClasses + "from-orange-500 to-amber-400";
      tierBadge.className = baseTextClasses + "text-orange-400";
      tierBadge.textContent = "Bronze Member";
    } else if (currentPoints < 50) {
      idBorder.className = baseBorderClasses + "from-slate-400 to-gray-300";
      tierBadge.className = baseTextClasses + "text-slate-300";
      tierBadge.textContent = "Silver Member";
    } else {
      idBorder.className = baseBorderClasses + "from-yellow-400 to-amber-500";
      tierBadge.className = baseTextClasses + "text-yellow-400";
      tierBadge.textContent = "Gold Member";
    }
  }

  // Gamification Progress Bar Calc
  let nextReward = 15; // default target (Kopi Aren)
  let percentage = Math.min((currentPoints / nextReward) * 100, 100);
  let pointsNeeded = Math.max(nextReward - currentPoints, 0);

  const targetText = document.getElementById("target-reward-text");
  const progressBar = document.getElementById("target-progress-bar");
  if (targetText && progressBar) {
    progressBar.style.width = `${percentage}%`;
    if (pointsNeeded > 0) {
      targetText.innerHTML = `<strong>${pointsNeeded} Poin</strong> lagi untuk klaim Kopi Susu Gratis!`;
    } else {
      targetText.innerHTML = `<strong>Selamat!</strong> Poin Anda cukup untuk klaim Kopi Susu Gratis.`;
    }
  }

  // Setup QR Modal Trigger
  const qrBtn = document.getElementById("show-qr-btn");
  if (qrBtn) {
    qrBtn.onclick = () => {
      showQRModal(AppState.user.nim || "N/A");
    };
  }

  // Setup Quick Action Wifi claim
  const quickWifiBtn = document.getElementById("quick-wifi-claim-btn");
  if (quickWifiBtn) {
    quickWifiBtn.onclick = async () => {
      const confirmClaim = confirm(
        "Tukar 5 Poin dengan Voucher WiFi VIP 24 Jam?",
      );
      if (confirmClaim) {
        const res = await ApiService.redeemReward(1); // 1 = Wifi
        if (res.status === "success") {
          // Refresh details
          await initUserData();
          renderHomeView();
          alert(
            `Berhasil! Token WiFi Anda: ${res.token_wifi || "WIFI-VIP-NGLB"}\nDetail disimpan di riwayat.`,
          );
        }
      }
    };
  }

  // Medsos Share Button
  const shareBtn = document.getElementById("share-medsos-btn");
  if (shareBtn) {
    shareBtn.onclick = async () => {
      // Simulate share link popup
      window.open("https://instagram.com", "_blank");

      // API call
      const res = await ApiService.claimShareBonus();
      if (res.status === "success") {
        await initUserData();
        renderHomeView();
      }
    };
  }
}

// 🔎 Digital QR ID Modal
function showQRModal(nimValue) {
  let modal = document.createElement("div");
  modal.className =
    "fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-65 p-4 backdrop-filter backdrop-blur-sm";
  modal.innerHTML = `
    <div class="bg-white rounded-2xl p-6 text-center max-w-sm w-full relative">
      <h4 class="text-lg font-bold text-slate-800 mb-2">Scan Member ID</h4>
      <p class="text-xs text-gray-500 mb-4">Tunjukkan kode QR ini ke kasir Ngolab untuk transaksi cepat</p>
      
      <!-- Fake QR Code Generator using API -->
      <div class="bg-gray-100 p-4 rounded-xl inline-block mb-4">
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(nimValue)}" 
             alt="Member NIM QR" class="w-48 h-48 mx-auto rounded-lg">
      </div>
      
      <div class="text-sm font-bold text-slate-700 mb-4">${nimValue}</div>
      
      <button class="w-full bg-slate-800 text-white font-semibold py-2.5 rounded-xl hover:bg-slate-900 transition"
              onclick="this.parentElement.parentElement.remove()">
        Tutup
      </button>
    </div>
  `;
  document.body.appendChild(modal);
}

// 🍔 Catalog & Menu Loader
async function loadCatalog() {
  const grid = document.getElementById("catalog-grid");
  if (!grid) return;

  // Show skeleton loading
  grid.innerHTML = Array(4)
    .fill(0)
    .map(
      () => `
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
      <div class="h-40 skeleton-loading w-full"></div>
      <div class="p-4 space-y-2">
        <div class="h-4 skeleton-loading w-2/3 rounded"></div>
        <div class="h-3 skeleton-loading w-1/2 rounded"></div>
        <div class="h-8 skeleton-loading w-full rounded-lg mt-2"></div>
      </div>
    </div>
  `,
    )
    .join("");

  try {
    const menus = await ApiService.getMenus(AppState.selectedCategory);
    AppState.menus = menus;

    // Setup category switchers
    await setupCategoryFilters();

    // Render actual cards
    renderCatalogCards();
  } catch (error) {
    grid.innerHTML = `<div class="col-span-full py-8 text-center text-red-500">Gagal memuat katalog menu. Hubungkan backend.</div>`;
  }
}

async function setupCategoryFilters() {
  const selectFilter = document.getElementById("menu-category-filter");
  const searchInput = document.getElementById("menu-search-input");

  if (selectFilter && selectFilter.options.length <= 1) {
    const cats = [
      ...new Set(
        AppState.menus.flatMap((m) => (m.kategori ? [m.kategori] : [])),
      ),
    ];
    selectFilter.innerHTML =
      `<option value="all">Semua Kategori</option>` +
      cats
        .map((c) => {
          return `<option value="${c.toLowerCase()}">${c}</option>`;
        })
        .join("");

    selectFilter.addEventListener("change", (e) => {
      AppState.selectedCategory = e.target.value;
      renderCatalogCards();
    });
  }

  if (searchInput && !searchInput.dataset.listener) {
    searchInput.dataset.listener = "true";
    searchInput.addEventListener("input", (e) => {
      AppState.menuSearch = e.target.value.toLowerCase().trim();
      renderCatalogCards();
    });
  }
}

function renderCatalogCards() {
  const grid = document.getElementById("catalog-grid");
  if (!grid) return;

  const filtered = AppState.menus.filter((m) => {
    const catMatch =
      AppState.selectedCategory === "all" ||
      AppState.selectedCategory === "" ||
      m.category === AppState.selectedCategory ||
      (m.kategori || "").toLowerCase() === AppState.selectedCategory;
    const searchMatch =
      AppState.menuSearch === "" ||
      (m.nama_menu || "").toLowerCase().includes(AppState.menuSearch) ||
      (m.description || "").toLowerCase().includes(AppState.menuSearch);
    return catMatch && searchMatch;
  });

  if (filtered.length === 0) {
    grid.innerHTML = `<div class="col-span-full py-12 text-center text-gray-400">Tidak ada menu yang sesuai.</div>`;
    return;
  }

  grid.innerHTML = filtered
    .map((item) => {
      const pointsEarning = Math.floor(item.harga / 10000);
      const imageSrc =
        item.gambar_menu ||
        (item.gambar
          ? `assets/uploads/menus/${item.gambar}`
          : "https://placehold.co/400x300?text=Menu");
      return `
      <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 hover-scale flex flex-col justify-between">
        <div>
          <img src="${imageSrc}" alt="${item.nama_menu}" class="h-40 w-full object-cover">
          <div class="p-4">
            <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600">
              ${item.kategori || "General"}
            </span>
            <h4 class="font-bold text-slate-800 mt-2 text-base leading-tight">${item.nama_menu}</h4>
            <p class="text-xs text-gray-500 mt-1 line-clamp-2">${item.description || ""}</p>
          </div>
        </div>
        
        <div class="p-4 pt-0 border-t border-gray-50 mt-auto">
          <div class="flex items-center justify-between my-3">
            <span class="font-extrabold text-orange-600">Rp ${item.harga.toLocaleString("id-ID")}</span>
            <span class="text-[11px] font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">
              +${pointsEarning} Poin
            </span>
          </div>
          <button class="w-full bg-slate-800 hover:bg-slate-900 text-white font-bold py-2 rounded-lg text-sm transition-all"
                  onclick="window.AppActions.addToCart(${item.id_menu})">
            + Tambah Keranjang
          </button>
        </div>
      </div>
    `;
    })
    .join("");
}

// 🛒 Shopping Cart System
function setupCartDrawer() {
  // Checkout Button
  const checkoutForm = document.getElementById("checkout-form");
  if (checkoutForm) {
    checkoutForm.addEventListener("submit", async (e) => {
      e.preventDefault();

      if (AppState.cart.length === 0) {
        showToast("Keranjang Anda masih kosong!", "error");
        return;
      }

      const notes = document.getElementById("checkout-notes").value.trim();
      const cartItem = AppState.cart[0]; // Self-Order standard limit: single checkout process

      // Calculate total fields
      const total_harga = AppState.cart.reduce(
        (sum, item) => sum + item.harga * item.qty,
        0,
      );
      const pointsEarned = Math.floor(total_harga / 10000);

      try {
        // Send actual API request
        const res = await ApiService.checkout(
          cartItem.id_menu,
          cartItem.qty,
          total_harga,
          pointsEarned,
          notes,
        );
        if (res.status === "success") {
          // Clear cart
          AppState.cart = [];
          updateCartUI();

          // Close drawer
          drawer.classList.add("translate-x-full");
          backdrop.classList.add("opacity-0", "pointer-events-none");

          // Switch to history tab
          switchTab("riwayat");
        }
      } catch (error) {
        showToast("Koneksi ke backend gagal.", "error");
      }
    });
  }
}

function addToCart(id_menu) {
  if (!SessionManager.isLoggedIn()) {
    showToast("Harap login dahulu sebelum memesan!", "error");
    showLoginModal();
    return;
  }

  const menuItem = AppState.menus.find((m) => m.id_menu === id_menu);
  if (!menuItem) return;

  const existing = AppState.cart.find((c) => c.id_menu === id_menu);
  if (existing) {
    existing.qty += 1;
  } else {
    AppState.cart.push({
      ...menuItem,
      qty: 1,
    });
  }

  updateCartUI();
  showToast(`Ditambahkan ke keranjang: ${menuItem.nama_menu}`);
}

function updateCartUI() {
  const container = document.getElementById("cart-items-container");
  const floatingCount = document.getElementById("floating-cart-count");
  const navCount = document.getElementById("nav-cart-count");

  const totalCount = AppState.cart.reduce((sum, item) => sum + item.qty, 0);
  const totalPrice = AppState.cart.reduce(
    (sum, item) => sum + item.harga * item.qty,
    0,
  );
  const potentialPoints = Math.floor(totalPrice / 10000);

  // Update badges
  if (floatingCount) floatingCount.textContent = totalCount;
  if (navCount) navCount.textContent = totalCount;

  // Render total details in drawer
  document.getElementById("checkout-total-price").textContent =
    `Rp ${totalPrice.toLocaleString("id-ID")}`;
  document.getElementById("checkout-potential-points").textContent =
    `+${potentialPoints} Poin`;

  if (AppState.cart.length === 0) {
    container.innerHTML = `
      <div class="py-12 text-center text-gray-400">
        <span class="text-3xl block mb-2">🛒</span>
        Keranjang kosong. Tambahkan menu lezat sekarang!
      </div>
    `;
    return;
  }

  container.innerHTML = AppState.cart
    .map(
      (item) => `
    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100">
      <div class="flex items-center space-x-3">
        <img src="${item.gambar_menu}" alt="${item.nama_menu}" class="w-12 h-12 rounded-lg object-cover">
        <div>
          <h5 class="text-xs font-bold text-slate-800 leading-tight">${item.nama_menu}</h5>
          <span class="text-xs text-orange-600 font-extrabold">Rp ${item.harga.toLocaleString("id-ID")}</span>
        </div>
      </div>
      <div class="flex items-center space-x-2">
        <button class="w-6 h-6 flex items-center justify-center bg-white border border-gray-200 rounded-full font-bold text-xs"
                onclick="window.AppActions.adjustQty(${item.id_menu}, -1)">-</button>
        <span class="text-xs font-bold text-slate-700 w-4 text-center">${item.qty}</span>
        <button class="w-6 h-6 flex items-center justify-center bg-white border border-gray-200 rounded-full font-bold text-xs"
                onclick="window.AppActions.adjustQty(${item.id_menu}, 1)">+</button>
      </div>
    </div>
  `,
    )
    .join("");
}

function adjustQty(id_menu, change) {
  const item = AppState.cart.find((c) => c.id_menu === id_menu);
  if (!item) return;

  item.qty += change;
  if (item.qty <= 0) {
    AppState.cart = AppState.cart.filter((c) => c.id_menu !== id_menu);
  }

  updateCartUI();
}

// 🕒 Log & Transaction History
async function loadTransactionHistory() {
  const container = document.getElementById("history-list-container");
  if (!container) return;

  container.innerHTML = `<div class="py-8 text-center text-gray-400">Loading history...</div>`;

  try {
    const orders = await ApiService.getHistory();
    const redeems = await ApiService.getRedemptions();

    // Sort combined activities by date descending
    const activities = [
      ...orders.map((o) => ({ ...o, type: "order" })),
      ...redeems.map((r) => ({ ...r, type: "redeem" })),
    ].sort(
      (a, b) =>
        new Date(b.tanggal_pesan || b.tanggal_tukar) -
        new Date(a.tanggal_pesan || a.tanggal_tukar),
    );

    if (activities.length === 0) {
      container.innerHTML = `<div class="py-12 text-center text-gray-400">Belum ada riwayat aktivitas.</div>`;
      return;
    }

    container.innerHTML = activities
      .map((act) => {
        const isOrder = act.type === "order";
        const badgeColor =
          act.status === "selesai" || act.status === "berhasil"
            ? "bg-green-100 text-green-700"
            : act.status === "pending"
              ? "bg-yellow-100 text-yellow-700"
              : "bg-red-100 text-red-700";

        const icon = isOrder ? "🛒" : "🎁";
        const title = isOrder
          ? `Pemesanan: ${act.nama_menu} (${act.jumlah}x)`
          : `Penukaran: ${act.nama_reward}`;
        const subtitle = isOrder
          ? `Total Belanja: Rp ${act.total_harga.toLocaleString("id-ID")} | +${act.poin_didapat} Poin`
          : `Tukar ${act.poin_dibutuhkan} Poin ${act.token_wifi ? `| Token: <strong>${act.token_wifi}</strong>` : ""}`;

        const date = new Date(
          act.tanggal_pesan || act.tanggal_tukar,
        ).toLocaleDateString("id-ID", {
          day: "2-digit",
          month: "short",
          hour: "2-digit",
          minute: "2-digit",
        });

        return `
        <div class="flex items-start justify-between p-4 bg-white rounded-xl border border-gray-100 shadow-sm">
          <div class="flex items-start space-x-3">
            <span class="text-xl p-2 bg-gray-50 rounded-lg">${icon}</span>
            <div>
              <h5 class="text-sm font-bold text-slate-800 leading-tight">${title}</h5>
              <p class="text-xs text-gray-500 mt-1">${subtitle}</p>
              <span class="text-[10px] text-gray-400 block mt-1">${date}</span>
            </div>
          </div>
          <span class="text-xs font-semibold px-2 py-0.5 rounded-full ${badgeColor} capitalize">
            ${act.status}
          </span>
        </div>
      `;
      })
      .join("");
  } catch (error) {
    container.innerHTML = `<div class="py-8 text-center text-red-500">Gagal memuat riwayat.</div>`;
  }
}

// 💬 Feedback Form setup
function setupFeedbackForm() {
  const form = document.getElementById("feedback-form");
  if (!form) return;

  // Rating Star Click Listener
  const stars = form.querySelectorAll(".star-rating-btn");
  let selectedRating = 5;

  stars.forEach((star, idx) => {
    star.addEventListener("click", (e) => {
      e.preventDefault();
      selectedRating = idx + 1;
      // Color selected stars
      stars.forEach((s, i) => {
        if (i <= idx) {
          s.classList.add("text-yellow-400");
          s.classList.remove("text-gray-300");
        } else {
          s.classList.remove("text-yellow-400");
          s.classList.add("text-gray-300");
        }
      });
    });
  });

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const ulasan = document.getElementById("feedback-review").value.trim();

    try {
      const res = await ApiService.submitFeedback(selectedRating, ulasan);
      if (res.status === "success") {
        form.reset();
        // Reset stars
        stars.forEach((s, i) => {
          s.classList.add("text-yellow-400");
          s.classList.remove("text-gray-300");
        });

        // Refresh profile points
        await initUserData();
      }
    } catch (error) {
      showToast("Koneksi gagal.", "error");
    }
  });
}

// 👤 Render Profile Settings
function renderProfileView() {
  if (!AppState.user) return;

  const nameInput = document.getElementById("profile-edit-name");
  const nimLabel = document.getElementById("profile-info-nim");
  const keyLabel = document.getElementById("profile-info-key");
  const avatarImg = document.getElementById("profile-avatar-preview");

  if (nameInput) nameInput.value = AppState.user.username;
  if (nimLabel) nimLabel.textContent = AppState.user.nim || "-";
  if (keyLabel) keyLabel.textContent = SessionManager.getApiKey() || "-";
  if (avatarImg && AppState.user.avatar) avatarImg.src = AppState.user.avatar;
}

// Attach functions to window for onclick handlers in HTML
window.AppActions = {
  addToCart,
  adjustQty,
  switchTab,
  toggleCart: () => {
    const drawer = document.getElementById("cart-drawer");
    const backdrop = document.getElementById("cart-drawer-backdrop");
    if (drawer && backdrop) {
      drawer.classList.toggle("translate-x-full");
      backdrop.classList.toggle("opacity-0");
      backdrop.classList.toggle("pointer-events-none");
    }
  },
  logout: () => {
    const confirmLogout = confirm("Apakah Anda yakin ingin keluar?");
    if (confirmLogout) {
      SessionManager.clearSession();
      AppState.user = null;
      AppState.cart = [];
      location.reload();
    }
  },
};
export { AppState, initUserData };
