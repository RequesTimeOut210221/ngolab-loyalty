<?php
/* Author: Ayesha */

if (!defined('IN_ADMIN')) exit('No direct script access allowed');

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'create') {
            $nama = $_POST['nama_kategori'];
            $deskripsi = $_POST['deskripsi'];
            
            $stmt = $conn->prepare("INSERT INTO TABEL_KATEGORI (nama_kategori, deskripsi) VALUES (?, ?)");
            $stmt->bind_param("ss", $nama, $deskripsi);
            if ($stmt->execute()) {
                $message = "Kategori baru berhasil ditambahkan.";
            } else {
                $message = "Gagal menambah kategori: " . $stmt->error;
            }
        } elseif ($action === 'delete') {
            $id = $_POST['id_kategori'];
            $stmt = $conn->prepare("DELETE FROM TABEL_KATEGORI WHERE id_kategori=?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $message = "Kategori berhasil dihapus.";
            } else {
                $message = "Gagal menghapus kategori: " . $stmt->error;
            }
        } elseif ($action === 'edit') {
            $id = $_POST['id_kategori'];
            $nama = $_POST['nama_kategori'];
            $deskripsi = $_POST['deskripsi'];
            
            $stmt = $conn->prepare("UPDATE TABEL_KATEGORI SET nama_kategori=?, deskripsi=? WHERE id_kategori=?");
            $stmt->bind_param("ssi", $nama, $deskripsi, $id);
            if ($stmt->execute()) {
                $message = "Kategori berhasil diupdate.";
            } else {
                $message = "Gagal mengupdate kategori: " . $stmt->error;
            }
        }
    }
}

$kategori = mysqli_query($conn, "SELECT * FROM TABEL_KATEGORI ORDER BY id_kategori DESC");
?>

<script>
    function openCreateModal() {
        document.getElementById('createKategoriModal').classList.remove('hidden');
    }
    function closeCreateModal() {
        document.getElementById('createKategoriModal').classList.add('hidden');
    }
    function openEditModal(id, nama, deskripsi) {
        document.getElementById('edit_id_kategori').value = id;
        document.getElementById('edit_nama_kategori').value = nama;
        document.getElementById('edit_deskripsi').value = deskripsi;
        document.getElementById('editKategoriModal').classList.remove('hidden');
    }
    function closeEditModal() {
        document.getElementById('editKategoriModal').classList.add('hidden');
    }
</script>

<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-slate-800">🏷️ Kelola Kategori Menu</h1>
        <button onclick="openCreateModal()" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded shadow-md transition">
            + Tambah Kategori Baru
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
                    <th class="py-3 px-6 text-left">ID</th>
                    <th class="py-3 px-6 text-left">Nama Kategori</th>
                    <th class="py-3 px-6 text-left">Deskripsi</th>
                    <th class="py-3 px-6 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
                <?php while ($row = mysqli_fetch_assoc($kategori)): ?>
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="py-3 px-6 text-left whitespace-nowrap">
                        <span class="font-medium"><?= $row['id_kategori'] ?></span>
                    </td>
                    <td class="py-3 px-6 text-left">
                        <span class="font-bold capitalize block text-slate-800"><?= htmlspecialchars($row['nama_kategori']) ?></span>
                    </td>
                    <td class="py-3 px-6 text-left">
                        <span class="text-gray-600"><?= htmlspecialchars($row['deskripsi']) ?></span>
                    </td>
                    <td class="py-3 px-6 text-center">
                        <div class="flex item-center justify-center space-x-3">
                            <button onclick="openEditModal(<?= $row['id_kategori'] ?>, '<?= htmlspecialchars(addslashes($row['nama_kategori'])) ?>', '<?= htmlspecialchars(addslashes($row['deskripsi'])) ?>')" class="transform hover:text-blue-500 hover:scale-110">
                                ✏️ Edit
                            </button>
                            <form method="POST" action="" class="inline" onsubmit="return confirm('Hapus kategori ini beserta menu di dalamnya?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id_kategori" value="<?= $row['id_kategori'] ?>">
                                <button type="submit" class="transform hover:text-red-500 hover:scale-110">🗑️ Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if (mysqli_num_rows($kategori) == 0): ?>
                <tr>
                    <td colspan="4" class="py-4 text-center text-gray-500">Belum ada kategori yang ditambahkan.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Kategori -->
<div id="createKategoriModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 p-4">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-xl font-bold mb-4 text-slate-800">Tambah Kategori Baru</h2>
        <form method="POST" action="">
            <input type="hidden" name="action" value="create">
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Kategori</label>
                <input type="text" name="nama_kategori" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Deskripsi</label>
                <textarea name="deskripsi" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
            </div>
            
            <div class="flex items-center justify-between mt-6">
                <button type="button" onclick="closeCreateModal()" class="text-gray-500 hover:text-gray-700 font-bold">Batal</button>
                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Simpan Kategori</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Kategori -->
<div id="editKategoriModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 p-4">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-xl font-bold mb-4 text-slate-800">Edit Kategori</h2>
        <form method="POST" action="">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id_kategori" id="edit_id_kategori">
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Kategori</label>
                <input type="text" name="nama_kategori" id="edit_nama_kategori" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Deskripsi</label>
                <textarea name="deskripsi" id="edit_deskripsi" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
            </div>
            
            <div class="flex items-center justify-between mt-6">
                <button type="button" onclick="closeEditModal()" class="text-gray-500 hover:text-gray-700 font-bold">Batal</button>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Update Kategori</button>
            </div>
        </form>
    </div>
</div>