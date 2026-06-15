/**
 * API Wrapper and Data Controller
 * Ngolab Express Cafe - Unified Loyalty System
 *
 * This file handles all fetch requests to backend endpoints.
 */

const API_CONFIG = {
  baseUrl: window.location.origin + window.location.pathname.replace(/\/[^\/]*$/, ""),
  apiKeyHeader: "x-api-key"
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

  const iconDiv = document.createElement("div");
  iconDiv.className = `inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg ${iconColor}`;
  // iconSvg is a strict hardcoded string constant from above
  // eslint-disable-next-line react-doctor/dangerous-html-sink
  iconDiv.innerHTML = iconSvg;

  const msgDiv = document.createElement("div");
  msgDiv.className = "ms-3 text-sm font-normal";
  msgDiv.textContent = message;

  const btn = document.createElement("button");
  btn.type = "button";
  btn.className = "ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700";
  btn.setAttribute("aria-label", "Close");
  btn.onclick = function() { this.parentElement.remove(); };
  btn.innerHTML = `<span class="sr-only">Close</span><svg class="w-3 h-3" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"></path></svg>`;

  toast.appendChild(iconDiv);
  toast.appendChild(msgDiv);
  toast.appendChild(btn);

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

// Local Storage Cache for better performance
const storageCache = new Map();
window.addEventListener('storage', (e) => {
  if (e.key && storageCache.has(e.key)) {
    storageCache.set(e.key, e.newValue);
  }
});

// Eagerly cache all relevant keys to avoid repeated localStorage reads during runtime
const storageKeys = ["ngolab_api_key", "ngolab_username", "ngolab_email", "ngolab_points", "ngolab_nim"];
storageKeys.forEach(key => {
  storageCache.set(key, localStorage.getItem(key));
});

function getCachedStorage(key) {
  return storageCache.get(key);
}

function setCachedStorage(key, value) {
  storageCache.set(key, value);
  localStorage.setItem(key, value);
}

function removeCachedStorage(key) {
  storageCache.delete(key);
  localStorage.removeItem(key);
}

// Local Storage Session Helpers
const SessionManager = {
  getApiKey() {
    return getCachedStorage("ngolab_api_key");
  },
  setSession(apiKey, username, email, points = 10, nim = "") {
    setCachedStorage("ngolab_api_key", apiKey);
    setCachedStorage("ngolab_username", username);
    setCachedStorage("ngolab_email", email);
    setCachedStorage("ngolab_points", points);
    setCachedStorage("ngolab_nim", nim);
  },
  clearSession() {
    removeCachedStorage("ngolab_api_key");
    removeCachedStorage("ngolab_username");
    removeCachedStorage("ngolab_email");
    removeCachedStorage("ngolab_points");
    removeCachedStorage("ngolab_nim");
    removeCachedStorage("ngolab_is_shared");
  },
  isLoggedIn() {
    return !!this.getApiKey();
  },
  getPoints() {
    return parseInt(getCachedStorage("ngolab_points") || "0", 10);
  },
  setPoints(points) {
    setCachedStorage("ngolab_points", points);
  },
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
    console.error(`[API ERROR] Network error fetching ${url}`, error);
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

    if (res.status === "success" && res.key) {
      SessionManager.setSession(
        res.key,
        username,
        email,
        10,
        phone.substring(0, 10),
      );
    }
    return res;
  },

  // 👥 MEMBER 1: USER PROFILE & POINTS
  async getProfile() {
    const res = await request(`${API_CONFIG.baseUrl}/api/profile.php`, {
      method: "GET",
    });

    if (res.error) {
      throw new Error(res.error);
    }
    
    if (res.avatar && !res.avatar.startsWith('http') && !res.avatar.startsWith('assets/')) {
        res.avatar = `assets/uploads/profiles/${res.avatar}`;
    }

    return res;
  },

  async updateProfile(name, avatarFile) {
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

    return res;
  },

  // 👥 MEMBER 2: CATALOG & REVIEWS
  async getCategories() {
    const res = await request(`${API_CONFIG.baseUrl}/api/categories.php`, {
      method: "GET",
    });
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

    if (Array.isArray(res)) {
      return res.map((menu) => {
        let rawGambar = menu.gambar_menu || menu.gambar || "";
        let finalGambar = rawGambar;
        if (rawGambar && !rawGambar.startsWith('http') && !rawGambar.startsWith('assets/')) {
            finalGambar = `assets/uploads/menus/${rawGambar}`;
        }
        
        return {
          ...menu,
          id_menu: Number(menu.id_menu || menu.id),
          kategori: menu.kategori || menu.category || "",
          category: menu.category || menu.kategori || "",
          gambar_menu: finalGambar,
          description: menu.description || menu.deskripsi || "",
          poin_didapat: Number(menu.poin_didapat || Math.floor(Number(menu.harga || 0) / 10000)),
        };
      });
    }
    return res;
  },

  async submitFeedback(rating, ulasan) {
    const nama_user = getCachedStorage("ngolab_username") || "Pelanggan Anonim";
    const res = await request(`${API_CONFIG.baseUrl}/api/feedback.php`, {
      method: "POST",
      body: JSON.stringify({ rating, ulasan, nama_user }),
    });

    return res;
  },

  async getFeedbacks() {
    const res = await request(`${API_CONFIG.baseUrl}/api/feedback.php`, {
      method: "GET",
    });

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

    return res;
  },

  async getHistory() {
    const res = await request(`${API_CONFIG.baseUrl}/api/checkout.php`, {
      method: "GET",
    });

    return res;
  },

  // 👥 MEMBER 3: POINT REDEMPTION (REWARDS & WIFI)
  async getRewards() {
    const res = await request(`${API_CONFIG.baseUrl}/api/rewards.php`, {
      method: "GET",
    });

    if (Array.isArray(res)) {
      return res.map((reward) => {
        let rawGambar = reward.gambar || reward.image || "";
        let finalGambar = rawGambar || "https://placehold.co/300x300?text=Reward";
        if (rawGambar && !rawGambar.startsWith('http') && !rawGambar.startsWith('assets/')) {
            finalGambar = `assets/uploads/rewards/${rawGambar}`;
        }
        
        return {
          ...reward,
          id_reward: Number(reward.id_reward || reward.id),
          nama_reward: reward.nama_reward || reward.name || reward.nama || "",
          poin_dibutuhkan: Number(reward.poin_dibutuhkan || reward.cost_points || reward.points || 0),
          stok: Number(reward.stok || reward.stock || 0),
          gambar: finalGambar,
        };
      });
    }
    return res;
  },

  async redeemReward(id_reward) {
    const res = await request(`${API_CONFIG.baseUrl}/api/redemptions.php`, {
      method: "POST",
      body: JSON.stringify({ id_reward }),
    });

    return res;
  },

  async getRedemptions() {
    const res = await request(`${API_CONFIG.baseUrl}/api/redemptions.php`, {
      method: "GET",
    });

    return res;
  },

  async claimShareBonus() {
    const res = await request(`${API_CONFIG.baseUrl}/api/share_bonus.php`, {
      method: "POST",
    });

    return res;
  },
};
export { ApiService, SessionManager, showToast };
