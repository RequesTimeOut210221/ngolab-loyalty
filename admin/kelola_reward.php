<?php
/* Author: Shaena */
if (!defined('IN_ADMIN')) exit('No direct script access allowed');

// Ensure upload directory exists
if (!is_dir('../assets/uploads/rewards')) {
    @mkdir('../assets/uploads/rewards', 0777, true);
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'create') {
            $nama = $_POST['nama_reward'];
            $poin = $_POST['poin_dibutuhkan'];
            $id_kategori = $_POST['kategori'];
            $stok = $_POST['stok'];
            $tier = $_POST['tier'];
            
            $gambar = '';
            if (!empty($_FILES['gambar']['name'])) {
                $webp_filename = uploadAndConvertToWebP($_FILES['gambar'], '../assets/uploads/rewards', 'reward');
                if ($webp_filename) $gambar = $webp_filename;
            }
            
            $stmt = $conn->prepare("INSERT INTO TABEL_REWARD (nama_reward, poin_dibutuhkan, id_kategori, stok, tier, gambar) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("siiiss", $nama, $poin, $id_kategori, $stok, $tier, $gambar);
            if ($stmt->execute()) {
                $message = "Reward baru berhasil ditambahkan.";
            } else {
                $message = "Gagal menambah reward: " . $stmt->error;
            }
        } elseif ($action === 'delete') {
            $id = $_POST['id_reward'];
            $stmt = $conn->prepare("DELETE FROM TABEL_REWARD WHERE id_reward=?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $message = "Reward berhasil dihapus.";
            } else {
                $message = "Gagal menghapus reward: " . $stmt->error;
            }
        } elseif ($action === 'edit') {
            $id = $_POST['id_reward'];
            $nama = $_POST['nama_reward'];
            $poin = $_POST['poin_dibutuhkan'];
            $id_kategori = $_POST['kategori'];
            $stok = $_POST['stok'];
            $tier = $_POST['tier'];

            if (!empty($_FILES['gambar']['name'])) {
                $webp_filename = uploadAndConvertToWebP($_FILES['gambar'], '../assets/uploads/rewards', 'reward');
                if ($webp_filename) {
                    $gambar = $webp_filename;
                    $stmt = $conn->prepare("UPDATE TABEL_REWARD SET nama_reward=?, poin_dibutuhkan=?, id_kategori=?, stok=?, tier=?, gambar=? WHERE id_reward=?");
                    $stmt->bind_param("siiissi", $nama, $poin, $id_kategori, $stok, $tier, $gambar, $id);
                } else {
                    $stmt = $conn->prepare("UPDATE TABEL_REWARD SET nama_reward=?, poin_dibutuhkan=?, id_kategori=?, stok=?, tier=? WHERE id_reward=?");
                    $stmt->bind_param("siiisi", $nama, $poin, $id_kategori, $stok, $tier, $id);
                }
            } else {
                $stmt = $conn->prepare("UPDATE TABEL_REWARD SET nama_reward=?, poin_dibutuhkan=?, id_kategori=?, stok=?, tier=? WHERE id_reward=?");
                $stmt->bind_param("siiisi", $nama, $poin, $id_kategori, $stok, $tier, $id);
            }
            
            if ($stmt->execute()) {
                $message = "Reward berhasil diupdate.";
            } else {
                $message = "Gagal mengupdate reward: " . $stmt->error;
            }
        }
    }
}

$rewards_query = mysqli_query($conn, "
    SELECT r.*, k.nama_kategori AS kategori 
    FROM TABEL_REWARD r 
    LEFT JOIN TABEL_KATEGORI_REWARD k ON r.id_kategori = k.id_kategori 
    ORDER BY r.id_reward DESC
");
$rewards_data = [];
while ($row = mysqli_fetch_assoc($rewards_query)) {
    $rewards_data[] = $row;
}

$kategori_list = mysqli_query($conn, "SELECT * FROM TABEL_KATEGORI_REWARD ORDER BY nama_kategori ASC");
$kategori_options = [];
while ($k = mysqli_fetch_assoc($kategori_list)) {
    $kategori_options[] = $k;
}
?>

<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-slate-800">🎁 Kelola Reward</h1>
        <button onclick="openCreateModal()" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded shadow-md transition">
            + Tambah Reward Baru
        </button>
    </div>

    <?php if($message): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
        <span class="block sm:inline"><?= htmlspecialchars($message) ?></span>
    </div>
    <?php endif; ?>

    <div class="bg-white shadow-md rounded my-6 overflow-x-auto">
        <table class="min-w-full w-full table-auto">
            <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">Gambar</th>
                    <th class="py-3 px-6 text-left">Nama Reward</th>
                    <th class="py-3 px-6 text-left">Kategori</th>
                    <th class="py-3 px-6 text-center">Poin / Stok</th>
                    <th class="py-3 px-6 text-center">Tier</th>
                    <th class="py-3 px-6 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
                <?php foreach ($rewards_data as $row): ?>
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="py-3 px-6 text-left whitespace-nowrap">
                        <img src="<?= !empty($row['gambar']) ? '../assets/uploads/rewards/' . htmlspecialchars($row['gambar']) : 'https://placehold.co/100x100?text=No+Image' ?>" alt="<?= htmlspecialchars($row['nama_reward']) ?>" class="w-12 h-12 rounded object-cover shadow">
                    </td>
                    <td class="py-3 px-6 text-left">
                        <span class="font-bold block"><?= htmlspecialchars($row['nama_reward']) ?></span>
                    </td>
                    <td class="py-3 px-6 text-left">
                        <span class="bg-gray-200 text-gray-700 py-1 px-3 rounded-full text-xs font-bold capitalize"><?= htmlspecialchars($row['kategori']) ?></span>
                    </td>
                    <td class="py-3 px-6 text-center">
                        <span class="block text-slate-800 font-medium"><?= number_format($row['poin_dibutuhkan'], 0, ',', '.') ?> Poin</span>
                        <span class="text-xs font-bold <?= $row['stok'] > 0 ? 'text-green-500' : 'text-red-500' ?>">Stok: <?= $row['stok'] ?></span>
                    </td>
                    <td class="py-3 px-6 text-center">
                        <span class="bg-blue-100 text-blue-700 py-1 px-3 rounded-full text-xs font-bold capitalize"><?= htmlspecialchars($row['tier']) ?></span>
                    </td>
                    <td class="py-3 px-6 text-center">
                        <div class="flex item-center justify-center space-x-3">
                            <button onclick="openEditModal(<?= $row['id_reward'] ?>, '<?= htmlspecialchars(addslashes($row['nama_reward'])) ?>', <?= $row['poin_dibutuhkan'] ?>, <?= $row['id_kategori'] ?: '""' ?>, <?= $row['stok'] ?>, '<?= htmlspecialchars(addslashes($row['tier'])) ?>')" class="transform hover:text-blue-500 hover:scale-110">
                                ✏️ Edit
                            </button>
                            <form method="POST" action="" class="inline" onsubmit="return confirm('Hapus reward ini?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id_reward" value="<?= $row['id_reward'] ?>">
                                <button type="submit" class="transform hover:text-red-500 hover:scale-110">🗑️ Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Reward -->
<div id="createRewardModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 p-4">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md max-h-[90vh] overflow-y-auto">
        <h2 class="text-xl font-bold mb-4 text-slate-800">Tambah Reward Baru</h2>
        <form method="POST" enctype="multipart/form-data" action="">
            <input type="hidden" name="action" value="create">
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Reward</label>
                <input type="text" name="nama_reward" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            
            <div class="flex space-x-4 mb-4">
                <div class="w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Poin Dibutuhkan</label>
                    <input type="number" name="poin_dibutuhkan" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Stok</label>
                    <input type="number" name="stok" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
            </div>
            
            <div class="flex space-x-4 mb-4">
                <div class="w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Kategori</label>
                    <select name="kategori" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($kategori_options as $k): ?>
                            <option value="<?= $k['id_kategori'] ?>"><?= htmlspecialchars($k['nama_kategori']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Tier</label>
                    <select name="tier" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="basic">Basic</option>
                        <option value="silver">Silver</option>
                        <option value="gold">Gold</option>
                    </select>
                </div>
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Gambar Reward (Opsional)</label>
                <input type="file" name="gambar" accept="image/*" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            
            <div class="flex items-center justify-between mt-6">
                <button type="button" onclick="closeCreateModal()" class="text-gray-500 hover:text-gray-700 font-bold">Batal</button>
                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Simpan Reward</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Reward -->
<div id="editRewardModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 p-4">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md max-h-[90vh] overflow-y-auto">
        <h2 class="text-xl font-bold mb-4 text-slate-800">Edit Reward</h2>
        <form method="POST" enctype="multipart/form-data" action="">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id_reward" id="edit_id_reward">
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Reward</label>
                <input type="text" name="nama_reward" id="edit_nama_reward" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            
            <div class="flex space-x-4 mb-4">
                <div class="w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Poin Dibutuhkan</label>
                    <input type="number" name="poin_dibutuhkan" id="edit_poin_dibutuhkan" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Stok</label>
                    <input type="number" name="stok" id="edit_stok" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
            </div>
            
            <div class="flex space-x-4 mb-4">
                <div class="w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Kategori</label>
                    <select name="kategori" id="edit_kategori" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($kategori_options as $k): ?>
                            <option value="<?= $k['id_kategori'] ?>"><?= htmlspecialchars($k['nama_kategori']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Tier</label>
                    <select name="tier" id="edit_tier" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="basic">Basic</option>
                        <option value="silver">Silver</option>
                        <option value="gold">Gold</option>
                    </select>
                </div>
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Ganti Gambar (Biarkan kosong jika tidak diubah)</label>
                <input type="file" name="gambar" accept="image/*" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            
            <div class="flex items-center justify-between mt-6">
                <button type="button" onclick="closeEditModal()" class="text-gray-500 hover:text-gray-700 font-bold">Batal</button>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Update Reward</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateModal() {
        document.getElementById('createRewardModal').classList.remove('hidden');
    }
    function closeCreateModal() {
        document.getElementById('createRewardModal').classList.add('hidden');
    }
    function openEditModal(id, nama, poin, id_kategori, stok, tier) {
        document.getElementById('edit_id_reward').value = id;
        document.getElementById('edit_nama_reward').value = nama;
        document.getElementById('edit_poin_dibutuhkan').value = poin;
        document.getElementById('edit_kategori').value = id_kategori;
        document.getElementById('edit_stok').value = stok;
        document.getElementById('edit_tier').value = tier;
        document.getElementById('editRewardModal').classList.remove('hidden');
    }
    function closeEditModal() {
        document.getElementById('editRewardModal').classList.add('hidden');
    }
</script>
