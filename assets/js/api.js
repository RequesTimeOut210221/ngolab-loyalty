/**
 * API Wrapper and Data Controller
 * Ngolab Express Cafe - Unified Loyalty System
 *
 * This file handles all fetch requests to backend endpoints.
 * It features automatic fallback to mock data if backend endpoints are not yet implemented (404/500/network error),
 * allowing developers to test the frontend independently.
 */

const API_CONFIG = {
  baseUrl:
    window.location.origin + window.location.pathname.replace(/\/[^\/]*$/, ""),
  apiKeyHeader: "x-api-key",
  // Auto-detected mock mode when backend is missing
  useMockFallback: true,
};

// Toast notification helper
function showToast(message, type = "success") {
  const container = document.getElementById("toast-container");
  if (!container) return;

  const toast = document.createElement("div");
  toast.className = `flex items-center w-full max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-lg shadow dark:text-gray-400 dark:bg-gray-800 transition-all duration-300 transform translate-y-2 opacity-0`;

  let iconColor = "text-green-500 bg-green-100";
  let iconSvg = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>`;

  if (type === "error") {
    iconColor = "text-red-500 bg-red-100";
    iconSvg = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>`;
  } else if (type === "warning") {
    iconColor = "text-yellow-500 bg-yellow-100";
    iconSvg = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>`;
  }

  toast.innerHTML = `
    <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg ${iconColor}">
      ${iconSvg}
    </div>
    <div class="ms-3 text-sm font-normal">${message}</div>
    <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700" data-dismiss-target="#toast-success" aria-label="Close" onclick="this.parentElement.remove()">
      <span class="sr-only">Close</span>
      <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"></path></svg>
    </button>
  `;

  container.appendChild(toast);

  // Animate in
  setTimeout(() => {
    toast.classList.remove("translate-y-2", "opacity-0");
  }, 10);

  // Auto-remove after 4 seconds
  setTimeout(() => {
    toast.classList.add("opacity-0", "translate-y-2");
    setTimeout(() => toast.remove(), 300);
  }, 4000);
}

// Local Storage Session Helpers
const SessionManager = {
  getApiKey() {
    return localStorage.getItem("ngolab_api_key");
  },
  setSession(apiKey, username, email, points = 10, nim = "") {
    localStorage.setItem("ngolab_api_key", apiKey);
    localStorage.setItem("ngolab_username", username);
    localStorage.setItem("ngolab_email", email);
    localStorage.setItem("ngolab_points", points);
    localStorage.setItem("ngolab_nim", nim);
  },
  clearSession() {
    localStorage.removeItem("ngolab_api_key");
    localStorage.removeItem("ngolab_username");
    localStorage.removeItem("ngolab_email");
    localStorage.removeItem("ngolab_points");
    localStorage.removeItem("ngolab_nim");
    localStorage.removeItem("ngolab_is_shared");
  },
  isLoggedIn() {
    return !!this.getApiKey();
  },
  getPoints() {
    return parseInt(localStorage.getItem("ngolab_points") || "0", 10);
  },
  setPoints(points) {
    localStorage.setItem("ngolab_points", points);
  },
};

// Mock database simulation for fallback
const MOCK_DB = {
  profile: {
    username: "Budi Raharjo",
    email: "budi.raharjo@student.telkomuniversity.ac.id",
    nim: "1202210045",
    saldo_poin: 45,
    is_shared_sosmed: false,
    avatar:
      "https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=150&q=80",
  },
  menus: [
    // Cafe Category
    {
      id_menu: 1,
      nama_menu: "Kopi Susu Gula Aren",
      harga: 15000,
      category: "cafe",
      gambar_menu:
        "https://images.unsplash.com/photo-1541167760496-1628856ab772?auto=format&fit=crop&w=300&q=80",
      description:
        "Espresso blend khas Ngolab dicampur susu segar dan sirup aren organik.",
    },
    {
      id_menu: 2,
      nama_menu: "Classic Espresso",
      harga: 10000,
      category: "cafe",
      gambar_menu:
        "https://images.unsplash.com/photo-1510707572775-d36a137b899d?auto=format&fit=crop&w=300&q=80",
      description:
        "Ekstraksi kopi murni konsentrat tinggi dari biji kopi arabika robusta pilihan.",
    },
    {
      id_menu: 3,
      nama_menu: "Caramel Macchiato",
      harga: 22000,
      category: "cafe",
      gambar_menu:
        "https://images.unsplash.com/photo-1485808191679-5f86510681a2?auto=format&fit=crop&w=300&q=80",
      description:
        "Espresso dengan steamed milk lembut dan karamel saus melimpah.",
    },
    {
      id_menu: 4,
      nama_menu: "Ice Americano",
      harga: 12000,
      category: "cafe",
      gambar_menu:
        "https://images.unsplash.com/photo-1517701604599-bb29b565090c?auto=format&fit=crop&w=300&q=80",
      description:
        "Espresso shot dingin dengan air jernih segar, pilihan terbaik penahan kantuk.",
    },
    {
      id_menu: 5,
      nama_menu: "Matcha Latte Green Tea",
      harga: 20000,
      category: "cafe",
      gambar_menu:
        "https://images.unsplash.com/photo-1536256263959-770b48d82b0a?auto=format&fit=crop&w=300&q=80",
      description:
        "Bubuk matcha Uji Jepang premium diseduh dengan susu segar hangat/dingin.",
    },

    // Bakso Category
    {
      id_menu: 6,
      nama_menu: "Bakso Mas Yanto Spesial Urat",
      harga: 22000,
      category: "bakso",
      gambar_menu:
        "https://images.unsplash.com/photo-1529042410759-befb1204b468?auto=format&fit=crop&w=300&q=80",
      description:
        "Bakso urat jumbo berdaging tebal disajikan dengan kuah kaldu sapi pekat, mie, dan seledri.",
    },
    {
      id_menu: 7,
      nama_menu: "Bakso Halus Kuah Kaldu",
      harga: 18000,
      category: "bakso",
      gambar_menu:
        "https://images.unsplash.com/photo-1608897013039-887f21d8c804?auto=format&fit=crop&w=300&q=80",
      description:
        "5 butir bakso halus lembut yang memanjakan lidah bersama kaldu sapi segar.",
    },
    {
      id_menu: 8,
      nama_menu: "Bakso Telur Rebus",
      harga: 20000,
      category: "bakso",
      gambar_menu:
        "https://images.unsplash.com/photo-1596797038530-2c107229654b?auto=format&fit=crop&w=300&q=80",
      description:
        "Bakso daging sapi isi telur rebus utuh disiram kaldu panas gurih.",
    },
  ],
  rewards: [
    {
      id_reward: 1,
      nama_reward: "WiFi VIP Voucher 24 Jam",
      poin_dibutuhkan: 5,
      stok: 99,
      category: "internet",
      gambar:
        "https://images.unsplash.com/photo-1563986768609-322da13575f3?auto=format&fit=crop&w=300&q=80",
    },
    {
      id_reward: 2,
      nama_reward: "Kopi Susu Aren Gratis",
      poin_dibutuhkan: 15,
      stok: 12,
      category: "cafe",
      gambar:
        "https://images.unsplash.com/photo-1541167760496-1628856ab772?auto=format&fit=crop&w=300&q=80",
    },
    {
      id_reward: 3,
      nama_reward: "Bakso Urat Jumbo Gratis",
      poin_dibutuhkan: 25,
      stok: 8,
      category: "bakso",
      gambar:
        "https://images.unsplash.com/photo-1529042410759-befb1204b468?auto=format&fit=crop&w=300&q=80",
    },
  ],
  history: [
    {
      id_pesanan: 101,
      id_menu: 1,
      nama_menu: "Kopi Susu Gula Aren",
      jumlah: 2,
      total_harga: 30000,
      poin_didapat: 3,
      status: "selesai",
      tanggal_pesan: "2026-06-01 10:20:00",
    },
    {
      id_pesanan: 102,
      id_menu: 7,
      nama_menu: "Bakso Halus Kuah Kaldu",
      jumlah: 1,
      total_harga: 18000,
      poin_didapat: 1,
      status: "selesai",
      tanggal_pesan: "2026-05-30 18:45:00",
    },
  ],
  redemptions: [
    {
      id_penukaran: 201,
      nama_reward: "WiFi VIP Voucher 24 Jam",
      poin_dibutuhkan: 5,
      tanggal_tukar: "2026-05-31 15:00:00",
      token_wifi: "WIFI-VIP-NGLB45",
      status: "berhasil",
    },
  ],
};

// Generic safe fetch handler
async function request(url, options = {}) {
  const headers = {
    "Content-Type": "application/json",
    ...(options.headers || {}),
  };

  const apiKey = SessionManager.getApiKey();
  if (apiKey) {
    headers[API_CONFIG.apiKeyHeader] = apiKey;
  }

  const fetchOptions = {
    ...options,
    headers,
  };

  try {
    const response = await fetch(url, fetchOptions);
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    return await response.json();
  } catch (error) {
    console.warn(
      `[API WARNING] Network error fetching ${url}. Falling back to client-side mock data.`,
      error,
    );
    if (API_CONFIG.useMockFallback) {
      return { _mock: true }; // Flags calling function to trigger mock routine
    }
    throw error;
  }
}

// API functions
const ApiService = {
  // 👥 MEMBER 1: AUTHENTICATION
  async login(phone, password = "") {
    const res = await request(
      `${API_CONFIG.baseUrl}/api/auth.php?action=login`,
      {
        method: "POST",
        body: JSON.stringify({ phone, password }),
      },
    );

    if (res._mock) {
      // Mock Login behavior
      if ((phone === "1202210045" || phone === "08123456789") && (!password || password === "123456")) {
        const mockKey = "ng-mock-apikey-budi-1202210045";
        SessionManager.setSession(
          mockKey,
          MOCK_DB.profile.username,
          MOCK_DB.profile.email,
          MOCK_DB.profile.saldo_poin,
          MOCK_DB.profile.nim,
        );
        showToast("Login Berhasil! Selamat datang di Ngolab Cafe.");
        return { status: "success", key: mockKey, user: MOCK_DB.profile };
      } else {
        showToast("Nomor HP/NIM tidak terdaftar! Silakan registrasi.", "error");
        return { status: "error", message: "User not found" };
      }
    }
    if (res.status === "success" && res.role === "member" && res.key && res.user) {
      SessionManager.setSession(
        res.key,
        res.user.username,
        res.user.email,
        res.user.saldo_poin,
        res.user.nim || res.user.no_hp || "",
      );
    }
    return res;
  },

  async register(username, email, phone, password = "") {
    const res = await request(
      `${API_CONFIG.baseUrl}/api/auth.php?action=register`,
      {
        method: "POST",
        body: JSON.stringify({ username, email, phone, password }),
      },
    );

    if (res._mock) {
      const mockKey = `ng-mock-apikey-${Date.now()}`;
      SessionManager.setSession(
        mockKey,
        username,
        email,
        10,
        phone.substring(0, 10),
      ); // 10 points Welcome Bonus
      showToast("Registrasi Berhasil! Selamat datang (+10 Poin Welcome Bonus)");
      return { status: "success", key: mockKey };
    }
    if (res.status === "success" && res.key) {
      SessionManager.setSession(res.key, username, email, 10, phone.substring(0, 10));
    }
    return res;
  },

  // 👥 MEMBER 1: USER PROFILE & POINTS
  async getProfile() {
    const res = await request(`${API_CONFIG.baseUrl}/api/profile.php`, {
      method: "GET",
    });

    if (res._mock) {
      // Sync mock points from state
      MOCK_DB.profile.saldo_poin = SessionManager.getPoints();
      MOCK_DB.profile.username =
        localStorage.getItem("ngolab_username") || MOCK_DB.profile.username;
      MOCK_DB.profile.email =
        localStorage.getItem("ngolab_email") || MOCK_DB.profile.email;
      MOCK_DB.profile.nim =
        localStorage.getItem("ngolab_nim") || MOCK_DB.profile.nim;
      return MOCK_DB.profile;
    }
    return res.map((menu) => ({
      ...menu,
      id_menu: Number(menu.id_menu || menu.id),
      kategori: menu.kategori || menu.category || "",
      category: menu.category || menu.kategori || "",
      gambar_menu: menu.gambar_menu || (menu.gambar ? `assets/uploads/menus/${menu.gambar}` : ""),
      description: menu.description || menu.deskripsi || "",
      poin_didapat: Number(menu.poin_didapat || Math.floor(Number(menu.harga || 0) / 10000)),
    }));
  },

  async updateProfile(name, avatarFile) {
    // If we have an avatar, use FormData, else send JSON
    let body;
    let headers = {};

    if (avatarFile) {
      body = new FormData();
      body.append("action", "update");
      body.append("username", name);
      body.append("avatar", avatarFile);
      // Let browser set multipart boundary
    } else {
      body = JSON.stringify({ username: name });
      headers["Content-Type"] = "application/json";
    }

    const res = await request(
      `${API_CONFIG.baseUrl}/api/profile.php?action=update`,
      {
        method: "POST", // standard endpoint for multipart
        headers,
        body,
      },
    );

    if (res._mock) {
      localStorage.setItem("ngolab_username", name);
      if (avatarFile) {
        // Mock image preview URL
        const previewUrl = URL.createObjectURL(avatarFile);
        MOCK_DB.profile.avatar = previewUrl;
      }
      showToast("Profil berhasil diperbarui!");
      return { status: "success" };
    }
    return res.map((reward) => ({
      ...reward,
      id_reward: Number(reward.id_reward || reward.id),
      nama_reward: reward.nama_reward || reward.name || reward.nama || "",
      poin_dibutuhkan: Number(reward.poin_dibutuhkan || reward.cost_points || reward.points || 0),
      stok: Number(reward.stok || reward.stock || 0),
      gambar: reward.gambar || reward.image || "https://placehold.co/300x300?text=Reward",
    }));
  },

  // 👥 MEMBER 2: CATALOG & REVIEWS
  async getCategories() {
    const res = await request(`${API_CONFIG.baseUrl}/api/categories.php`, {
      method: "GET",
    });
    if (res._mock) {
      return [{nama_kategori: "Cafe"}, {nama_kategori: "Bakso"}];
    }
    return res;
  },

  async getMenus(category = "all") {
    const url =
      category === "all" || category === ""
        ? `${API_CONFIG.baseUrl}/api/menus.php`
        : `${API_CONFIG.baseUrl}/api/menus.php?kategori=${category}`;

    const res = await request(url, {
      method: "GET",
    });

    if (res._mock) {
      if (category === "all") return MOCK_DB.menus;
      return MOCK_DB.menus.filter((m) => m.category === category);
    }
    return res;
  },

  async submitFeedback(rating, ulasan) {
    const nama_user = localStorage.getItem("ngolab_username") || "Pelanggan Anonim";
    const res = await request(`${API_CONFIG.baseUrl}/api/feedback.php`, {
      method: "POST",
      body: JSON.stringify({ rating, ulasan, nama_user }),
    });

    if (res._mock) {
      // Award +5 points
      const newPoints = SessionManager.getPoints() + 5;
      SessionManager.setPoints(newPoints);
      showToast("Feedback Terkirim! Terima kasih ulasannya (+5 Poin bonus)");
      return { status: "success", current_points: newPoints };
    }
    return res;
  },

  async getFeedbacks() {
    const res = await request(`${API_CONFIG.baseUrl}/api/feedback.php`, {
      method: "GET",
    });

    if (res._mock) {
      return []; // Return empty array if backend is missing
    }
    return res;
  },

  // 👥 MEMBER 3: TRANSACTIONS & LOYALTY (CHECKOUT / MOCK POS)
  async checkout(id_menu, jumlah, total_harga, poin_didapat, catatan = "") {
    const res = await request(`${API_CONFIG.baseUrl}/api/checkout.php`, {
      method: "POST",
      body: JSON.stringify({
        id_menu,
        jumlah_pesanan: jumlah,
        total_harga,
        poin_didapat,
        catatan,
      }),
    });

    if (res._mock) {
      // Add to mock history
      const selectedMenu = MOCK_DB.menus.find((m) => m.id_menu === id_menu);
      const newOrder = {
        id_pesanan: Date.now(),
        id_menu,
        nama_menu: selectedMenu ? selectedMenu.nama_menu : "Pesanan Kopi/Bakso",
        jumlah,
        total_harga,
        poin_didapat,
        status: "pending", // Starts pending, Admin completed it to award points
        tanggal_pesan: new Date()
          .toISOString()
          .replace("T", " ")
          .substring(0, 19),
      };

      MOCK_DB.history.unshift(newOrder);
      showToast(
        "Pesanan berhasil dibuat! Menunggu Kasir/Admin konfirmasi selesai untuk mendapatkan poin.",
      );
      return { status: "success", message: "Pesanan pending dibuat." };
    }
    return res;
  },

  async getHistory() {
    const res = await request(`${API_CONFIG.baseUrl}/api/checkout.php`, {
      method: "GET",
    });

    if (res._mock) {
      return MOCK_DB.history;
    }
    return res;
  },

  // 👥 MEMBER 3: POINT REDEMPTION (REWARDS & WIFI)
  async getRewards() {
    const res = await request(`${API_CONFIG.baseUrl}/api/rewards.php`, {
      method: "GET",
    });

    if (res._mock) {
      return MOCK_DB.rewards;
    }
    return res;
  },

  async redeemReward(id_reward) {
    const res = await request(`${API_CONFIG.baseUrl}/api/redemptions.php`, {
      method: "POST",
      body: JSON.stringify({ id_reward }),
    });

    if (res._mock) {
      const reward = MOCK_DB.rewards.find((r) => r.id_reward === id_reward);
      if (!reward) {
        showToast("Item reward tidak ditemukan!", "error");
        return { status: "error" };
      }

      const currentPoints = SessionManager.getPoints();
      if (currentPoints < reward.poin_dibutuhkan) {
        showToast("Poin Anda tidak mencukupi untuk klaim reward ini!", "error");
        return { status: "error", message: "Insufficient points" };
      }

      const newPoints = currentPoints - reward.poin_dibutuhkan;
      SessionManager.setPoints(newPoints);

      const isWifi = reward.id_reward === 1;
      const wifiToken = isWifi
        ? `WIFI-VIP-${Math.random().toString(36).substr(2, 6).toUpperCase()}`
        : null;

      const newRedemption = {
        id_penukaran: Date.now(),
        nama_reward: reward.nama_reward,
        poin_dibutuhkan: reward.poin_dibutuhkan,
        tanggal_tukar: new Date()
          .toISOString()
          .replace("T", " ")
          .substring(0, 19),
        token_wifi: wifiToken,
        status: isWifi ? "berhasil" : "pending", // Wifi auto-success, others pending verification
      };

      MOCK_DB.redemptions.unshift(newRedemption);

      if (isWifi) {
        showToast(`Berhasil menukar 5 Poin dengan Token WiFi VIP!`);
        return {
          status: "success",
          token_wifi: wifiToken,
          current_points: newPoints,
        };
      } else {
        showToast(
          `Request penukaran ${reward.nama_reward} diajukan! Menunggu verifikasi admin.`,
        );
        return { status: "success", current_points: newPoints };
      }
    }
    return res;
  },

  async getRedemptions() {
    const res = await request(`${API_CONFIG.baseUrl}/api/redemptions.php`, {
      method: "GET",
    });

    if (res._mock) {
      return MOCK_DB.redemptions;
    }
    return res;
  },

  async claimShareBonus() {
    const res = await request(`${API_CONFIG.baseUrl}/api/share_bonus.php`, {
      method: "POST",
    });

    if (res._mock) {
      if (localStorage.getItem("ngolab_is_shared") === "true") {
        showToast("Anda sudah mengklaim bonus share medsos!", "warning");
        return { status: "error", message: "Already claimed" };
      }

      const newPoints = SessionManager.getPoints() + 10;
      SessionManager.setPoints(newPoints);
      localStorage.setItem("ngolab_is_shared", "true");
      MOCK_DB.profile.is_shared_sosmed = true;
      showToast("Klaim Bonus Berhasil! +10 Poin ditambahkan.");
      return { status: "success", current_points: newPoints };
    }
    return res;
  },
};
export { ApiService, SessionManager, showToast };
