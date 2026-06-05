import { ApiService, showToast } from "./api.js";

// Global App Actions (Dipanggil oleh atribut onclick di HTML)
window.AppActions = {
  switchTab: (tabName) => {
    const btn = document.querySelector(`[data-tab-target="${tabName}"]`);
    if (btn) btn.click();
  },
  logout: () => {
    if (confirm("Apakah Anda yakin ingin keluar dari akun?")) {
      localStorage.clear();
      window.location.reload();
    }
  },
};

document.addEventListener("DOMContentLoaded", () => {
  // ==========================================
  // 0. LOGIKA NAVIGASI TAB (SPA SWITCHER)
  // ==========================================
  const tabBtns = document.querySelectorAll("[data-tab-target]");
  tabBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      const targetId = btn.getAttribute("data-tab-target") + "-tab";
      document.querySelectorAll(".tab-content").forEach((c) => {
        c.classList.add("hidden-tab");
        c.classList.remove("active-tab");
      });
      document.getElementById(targetId)?.classList.remove("hidden-tab");
      document.getElementById(targetId)?.classList.add("active-tab");

      // Update efek warna (Highlight) pada tombol navigasi yang sedang aktif
      document.querySelectorAll("[data-tab-target]").forEach((navBtn) => {
        navBtn.classList.remove("text-white", "font-extrabold");
      });
      const activeTarget = btn.getAttribute("data-tab-target");
      document
        .querySelectorAll(`[data-tab-target="${activeTarget}"]`)
        .forEach((activeBtn) => {
          activeBtn.classList.add("text-white", "font-extrabold");
        });
    });
  });

  // ==========================================
  // 1. RENDER KATALOG MENU (API GET)
  // ==========================================
  const loadMenus = async (kategori = "cafe") => {
    const grid = document.getElementById("catalog-grid");
    if (!grid) return;

    grid.innerHTML =
      '<p class="col-span-full text-center text-gray-500 font-medium animate-pulse">Memuat menu...</p>';

    try {
      const data = await ApiService.getMenus(kategori);

      grid.innerHTML = "";
      if (data.length === 0) {
        grid.innerHTML =
          '<p class="col-span-full text-center text-gray-500">Menu belum tersedia di kategori ini.</p>';
        return;
      }

      data.forEach((menu) => {
        const poinValue = menu.poin_didapat || Math.floor(menu.harga / 10000);
        const imageSrc = menu.gambar
          ? `uploads/menus/${menu.gambar}`
          : "https://placehold.co/400x300?text=Menu";

        grid.innerHTML += `
            <div class="group bg-white rounded-2xl shadow-sm hover:shadow-lg border border-gray-100 overflow-hidden flex flex-col transition-all duration-300">
              <div class="relative overflow-hidden">
                <img src="${imageSrc}" alt="${menu.nama_menu}" class="h-32 sm:h-40 w-full object-cover bg-gray-100 group-hover:scale-110 transition-transform duration-500">
                <div class="absolute top-2 right-2 bg-white/95 backdrop-blur-sm px-2.5 py-1 rounded-lg shadow-sm">
                  <span class="text-[10px] font-black text-orange-600 uppercase tracking-wider">${menu.kategori}</span>
                </div>
              </div>
              <div class="p-5 flex-1 flex flex-col justify-between">
                <div>
                  <h3 class="font-bold text-slate-800 text-sm sm:text-base leading-tight group-hover:text-orange-600 transition-colors">${menu.nama_menu}</h3>
                  <div class="flex items-center justify-between mt-2">
                    <p class="text-slate-800 font-black text-sm sm:text-lg">Rp ${parseInt(menu.harga).toLocaleString("id-ID")}</p>
                    <span class="bg-orange-50 border border-orange-100 text-orange-600 text-[10px] font-bold px-2 py-1 rounded-md">+${poinValue} Pts</span>
                  </div>
                </div>
                <button class="w-full mt-4 bg-slate-50 hover:bg-orange-500 hover:text-white border border-slate-200 hover:border-orange-500 text-slate-700 font-bold py-2.5 rounded-xl text-xs transition-all duration-300 flex items-center justify-center space-x-2">
                  <span>🛒</span> <span>Tambah</span>
                </button>
              </div>
            </div>
        `;
      });
    } catch (error) {
      grid.innerHTML =
        '<p class="col-span-full text-center text-red-500 font-medium">Koneksi Backend Gagal.</p>';
    }
  };

  // Load Kategori Dinamis
  const loadCategories = async () => {
    const container = document.getElementById("category-filter-container");
    if (!container) return;

    try {
      const categories = await ApiService.getCategories();
      container.innerHTML = ""; // Bersihkan tombol bawaan

      // Tambahkan tombol default "Semua Menu"
      container.innerHTML += `<button data-cat="all" class="cat-filter-btn shrink-0 px-5 py-2.5 text-sm font-bold rounded-xl transition bg-white text-orange-600 shadow-sm snap-start border border-gray-100">Semua Menu</button>`;

      categories.forEach((cat) => {
        const catValue = cat.nama_kategori.toLowerCase();
        container.innerHTML += `<button data-cat="${catValue}" class="cat-filter-btn shrink-0 px-5 py-2.5 text-sm font-bold rounded-xl transition text-gray-500 hover:text-slate-800 hover:bg-gray-300/40 snap-start border border-transparent">${cat.nama_kategori}</button>`;
      });

      // Event listener untuk tombol filter yang baru dibuat
      document.querySelectorAll(".cat-filter-btn").forEach((btn) => {
        btn.addEventListener("click", (e) => {
          // Reset warna semua tombol
          document.querySelectorAll(".cat-filter-btn").forEach((b) => {
            b.className =
              "cat-filter-btn shrink-0 px-5 py-2.5 text-sm font-bold rounded-xl transition text-gray-500 hover:text-slate-800 hover:bg-gray-300/40 snap-start border border-transparent";
          });
          // Warnai putih tombol yang sedang aktif
          e.target.className =
            "cat-filter-btn shrink-0 px-5 py-2.5 text-sm font-bold rounded-xl transition bg-white text-orange-600 shadow-sm snap-start border border-gray-100";

          // Load menu berdasarkan kategori yang dipilih
          const selectedCat = e.target.getAttribute("data-cat");
          loadMenus(selectedCat === "all" ? "" : selectedCat);
        });
      });
    } catch (error) {
      console.warn("Gagal memuat kategori:", error);
    }
  };

  // Load pertama kali
  loadCategories().then(() => loadMenus(""));

  // ==========================================
  // 2. KIRIM ULASAN & KLAIM POIN (API POST)
  // ==========================================
  const loadFeedbacks = async () => {
    const container = document.getElementById("feedback-list-container");
    if (!container) return;

    try {
      const feedbacks = await ApiService.getFeedbacks();
      container.innerHTML = "";

      if (!feedbacks || feedbacks.length === 0) {
        container.innerHTML =
          '<p class="text-center text-xs text-gray-500">Belum ada ulasan. Jadilah yang pertama!</p>';
        return;
      }

      feedbacks.forEach((fb) => {
        const stars = "★".repeat(fb.rating) + "☆".repeat(5 - fb.rating);
        container.innerHTML += `
          <div class="bg-gray-50/80 p-4 rounded-2xl border border-gray-100 relative hover:shadow-md transition-shadow">
            <!-- Dekorasi Tanda Kutip -->
            <div class="absolute top-4 right-4 text-gray-200/60">
              <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h4v10h-10z"/></svg>
            </div>
            <div class="flex flex-col mb-3">
              <span class="font-extrabold text-sm text-slate-800">${fb.nama_user}</span>
              <span class="text-orange-400 text-[11px] tracking-widest mt-0.5">${stars}</span>
            </div>
            <p class="text-xs text-gray-600 leading-relaxed pr-6 italic relative z-10">"${fb.ulasan}"</p>
          </div>
        `;
      });
    } catch (error) {
      container.innerHTML =
        '<p class="text-center text-xs text-red-500">Gagal memuat ulasan.</p>';
    }
  };

  loadFeedbacks();

  const feedbackForm = document.getElementById("feedback-form");

  if (feedbackForm) {
    feedbackForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      const ulasan = document.getElementById("feedback-review").value;
      const ratingEl = document.getElementById("feedback-rating");
      const rating = ratingEl ? parseInt(ratingEl.value) : 5;

      // Tembak POST API Feedback
      const res = await ApiService.submitFeedback(rating, ulasan);

      if (res.status === "success") {
        if (!res._mock) showToast(res.message, "success");
        feedbackForm.reset();
        loadFeedbacks(); // Refresh daftar ulasan seketika setelah ulasan baru dikirim
      } else {
        if (!res._mock)
          showToast(res.message || "Gagal mengirim ulasan.", "error");
      }
    });
  }
});
