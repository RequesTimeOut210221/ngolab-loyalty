<?php
if (!defined('IN_ADMIN')) exit('No direct script access allowed');
if (!is_dir('../assets/uploads/menus')) {
    mkdir('../assets/uploads/menus', 0777, true);
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'create') {
            $nama = $_POST['nama_menu'];
            $harga = $_POST['harga'];
            $kategori = $_POST['kategori'];
            $deskripsi = $_POST['deskripsi'];
            $gambar = $_FILES['gambar']['name'] ?? '';
            $poin = floor($harga / 10000); 
            
            if ($gambar) {
                move_uploaded_file($_FILES['gambar']['tmp_name'], '../assets/uploads/menus/' . $gambar);
            }
            
            $stmt = $conn->prepare("INSERT INTO TABEL_MENU (nama_menu, harga, kategori, deskripsi, gambar, poin_didapat) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sisssi", $nama, $harga, $kategori, $deskripsi, $gambar, $poin);
            if ($stmt->execute()) {
                $message = "Menu baru berhasil ditambahkan.";
            } else {
                $message = "Gagal menambah menu: " . $stmt->error;
            }
        } elseif ($action === 'delete') {
            $id = $_POST['id_menu'];
            $stmt = $conn->prepare("DELETE FROM TABEL_MENU WHERE id_menu=?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $message = "Menu berhasil dihapus.";
            } else {
                $message = "Gagal menghapus menu: " . $stmt->error;
            }
        } elseif ($action === 'edit') {
            $id = $_POST['id_menu'];
            $nama = $_POST['nama_menu'];
            $harga = $_POST['harga'];
            $kategori = $_POST['kategori'];
            $deskripsi = $_POST['deskripsi'];
            $poin = floor($harga / 10000);

            if (!empty($_FILES['gambar']['name'])) {
                $gambar = $_FILES['gambar']['name'];
                move_uploaded_file($_FILES['gambar']['tmp_name'], '../assets/uploads/menus/' . $gambar);
                $stmt = $conn->prepare("UPDATE TABEL_MENU SET nama_menu=?, harga=?, kategori=?, deskripsi=?, poin_didapat=?, gambar=? WHERE id_menu=?");
                $stmt->bind_param("sisssii", $nama, $harga, $kategori, $deskripsi, $poin, $gambar, $id);
            } else {
                $stmt = $conn->prepare("UPDATE TABEL_MENU SET nama_menu=?, harga=?, kategori=?, deskripsi=?, poin_didapat=? WHERE id_menu=?");
                $stmt->bind_param("sisssi", $nama, $harga, $kategori, $deskripsi, $poin, $id);
            }
            
            if ($stmt->execute()) {
                $message = "Menu berhasil diupdate.";
            } else {
                $message = "Gagal mengupdate menu: " . $stmt->error;
            }
        }
    }
}

$menus = mysqli_query($conn, "SELECT * FROM TABEL_MENU ORDER BY id_menu DESC");
$kategori_list = mysqli_query($conn, "SELECT * FROM TABEL_KATEGORI");
$kategori_options = [];
while ($k = mysqli_fetch_assoc($kategori_list)) {
    $kategori_options[] = $k;
}
?>

<script>
    function openCreateModal() {
        document.getElementById('createMenuModal').classList.remove('hidden');
    }
    function closeCreateModal() {
        document.getElementById('createMenuModal').classList.add('hidden');
    }
    function openEditModal(id, nama, harga, kategori, deskripsi) {
        document.getElementById('edit_id_menu').value = id;
        document.getElementById('edit_nama_menu').value = nama;
        document.getElementById('edit_harga').value = harga;
        document.getElementById('edit_kategori').value = kategori.toLowerCase();
        document.getElementById('edit_deskripsi').value = deskripsi;
        document.getElementById('editMenuModal').classList.remove('hidden');
    }
    function closeEditModal() {
        document.getElementById('editMenuModal').classList.add('hidden');
    }
</script>

<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-slate-800">🍔 Kelola Menu (Katalog)</h1>
        <button onclick="openCreateModal()" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded shadow-md transition">
            + Tambah Menu Baru
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
                    <th class="py-3 px-6 text-left">Menu</th>
                    <th class="py-3 px-6 text-left">Kategori</th>
                    <th class="py-3 px-6 text-center">Harga / Poin</th>
                    <th class="py-3 px-6 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
                <?php while ($row = mysqli_fetch_assoc($menus)): ?>
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="py-3 px-6 text-left whitespace-nowrap">
                        <img src="<?= !empty($row['gambar']) ? '../assets/uploads/menus/' . htmlspecialchars($row['gambar']) : 'https://placehold.co/100x100?text=No+Image' ?>" alt="<?= htmlspecialchars($row['nama_menu']) ?>" class="w-12 h-12 rounded object-cover shadow">
                    </td>
                    <td class="py-3 px-6 text-left">
                        <span class="font-bold block"><?= htmlspecialchars($row['nama_menu']) ?></span>
                        <span class="text-xs text-gray-500 line-clamp-1"><?= htmlspecialchars($row['deskripsi']) ?></span>
                    </td>
                    <td class="py-3 px-6 text-left">
                        <span class="bg-gray-200 text-gray-700 py-1 px-3 rounded-full text-xs font-bold capitalize"><?= htmlspecialchars($row['kategori']) ?></span>
                    </td>
                    <td class="py-3 px-6 text-center">
                        <span class="block text-slate-800 font-medium">Rp <?= number_format($row['harga'], 0, ',', '.') ?></span>
                        <span class="text-xs text-orange-500 font-bold">+<?= $row['poin_didapat'] ?> Poin</span>
                    </td>
                    <td class="py-3 px-6 text-center">
                        <div class="flex item-center justify-center space-x-3">
                            <button onclick="openEditModal(<?= $row['id_menu'] ?>, '<?= htmlspecialchars(addslashes($row['nama_menu'])) ?>', <?= $row['harga'] ?>, '<?= htmlspecialchars(addslashes($row['kategori'])) ?>', '<?= htmlspecialchars(addslashes($row['deskripsi'])) ?>')" class="transform hover:text-blue-500 hover:scale-110">
                                ✏️ Edit
                            </button>
                            <form method="POST" action="" class="inline" onsubmit="return confirm('Hapus menu ini?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id_menu" value="<?= $row['id_menu'] ?>">
                                <button type="submit" class="transform hover:text-red-500 hover:scale-110">🗑️ Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Menu -->
<div id="createMenuModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 p-4">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md max-h-[90vh] overflow-y-auto">
        <h2 class="text-xl font-bold mb-4 text-slate-800">Tambah Menu Baru</h2>
        <form method="POST" enctype="multipart/form-data" action="">
            <input type="hidden" name="action" value="create">
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Menu</label>
                <input type="text" name="nama_menu" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            
            <div class="flex space-x-4 mb-4">
                <div class="w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Harga (Rp)</label>
                    <input type="number" name="harga" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Kategori</label>
                    <select name="kategori" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline capitalize">
                        <?php foreach($kategori_options as $kat): ?>
                            <option value="<?= strtolower($kat['nama_kategori']) ?>"><?= htmlspecialchars($kat['nama_kategori']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Deskripsi</label>
                <textarea name="deskripsi" required rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Gambar Menu (Opsional)</label>
                <input type="file" name="gambar" accept="image/*" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            
            <div class="flex items-center justify-between mt-6">
                <button type="button" onclick="closeCreateModal()" class="text-gray-500 hover:text-gray-700 font-bold">Batal</button>
                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Simpan Menu</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Menu -->
<div id="editMenuModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 p-4">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md max-h-[90vh] overflow-y-auto">
        <h2 class="text-xl font-bold mb-4 text-slate-800">Edit Menu</h2>
        <form method="POST" enctype="multipart/form-data" action="">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id_menu" id="edit_id_menu">
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Menu</label>
                <input type="text" name="nama_menu" id="edit_nama_menu" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            
            <div class="flex space-x-4 mb-4">
                <div class="w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Harga (Rp)</label>
                    <input type="number" name="harga" id="edit_harga" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Kategori</label>
                    <select name="kategori" id="edit_kategori" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline capitalize">
                        <?php foreach($kategori_options as $kat): ?>
                            <option value="<?= strtolower($kat['nama_kategori']) ?>"><?= htmlspecialchars($kat['nama_kategori']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Deskripsi</label>
                <textarea name="deskripsi" id="edit_deskripsi" required rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Ganti Gambar (Biarkan kosong jika tidak diubah)</label>
                <input type="file" name="gambar" accept="image/*" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            
            <div class="flex items-center justify-between mt-6">
                <button type="button" onclick="closeEditModal()" class="text-gray-500 hover:text-gray-700 font-bold">Batal</button>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Update Menu</button>
            </div>
        </form>
    </div>
</div>