<?php
if (!defined('IN_ADMIN')) exit('No direct script access allowed');

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action === 'create') {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = hash('sha256', $_POST['password']);
            $role = $_POST['role'];
            $no_hp = !empty($_POST['no_hp']) ? $_POST['no_hp'] : null;
            $nim = !empty($_POST['nim']) ? $_POST['nim'] : null;
            
            $stmt = $conn->prepare("INSERT INTO TABEL_STAFF (username, email, password, role, no_hp, nim) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $username, $email, $password, $role, $no_hp, $nim);
            if ($stmt->execute()) {
                $message = "Staff baru berhasil ditambahkan.";
            } else {
                $message = "Gagal menambah staff.";
            }
        } elseif ($action === 'edit') {
            $id_staff = $_POST['id_staff'];
            $username = $_POST['username'];
            $email = $_POST['email'];
            $role = $_POST['role'];
            $no_hp = !empty($_POST['no_hp']) ? $_POST['no_hp'] : null;
            $nim = !empty($_POST['nim']) ? $_POST['nim'] : null;
            
            if (!empty($_POST['password'])) {
                $password = hash('sha256', $_POST['password']);
                $stmt = $conn->prepare("UPDATE TABEL_STAFF SET username=?, email=?, password=?, role=?, no_hp=?, nim=? WHERE id_staff=?");
                $stmt->bind_param("ssssssi", $username, $email, $password, $role, $no_hp, $nim, $id_staff);
            } else {
                $stmt = $conn->prepare("UPDATE TABEL_STAFF SET username=?, email=?, role=?, no_hp=?, nim=? WHERE id_staff=?");
                $stmt->bind_param("sssssi", $username, $email, $role, $no_hp, $nim, $id_staff);
            }
            if ($stmt->execute()) {
                $message = "Data staff berhasil diupdate.";
            }
        } elseif ($action === 'delete') {
            $id_staff = $_POST['id_staff'];
            $stmt = $conn->prepare("DELETE FROM TABEL_STAFF WHERE id_staff = ?");
            $stmt->bind_param("i", $id_staff);
            if ($stmt->execute()) {
                $message = "Staff berhasil dihapus.";
            }
        }
    }
}

$staff_list = $conn->query("SELECT * FROM TABEL_STAFF ORDER BY id_staff DESC");
?>
<script>
    function openCreateModal() {
        document.getElementById('createModal').classList.remove('hidden');
    }
    function closeCreateModal() {
        document.getElementById('createModal').classList.add('hidden');
    }
    function openEditModal(id, username, email, role, no_hp, nim) {
        document.getElementById('edit_id_staff').value = id;
        document.getElementById('edit_username').value = username;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_role').value = role;
        document.getElementById('edit_no_hp').value = no_hp || '';
        document.getElementById('edit_nim').value = nim || '';
        document.getElementById('editModal').classList.remove('hidden');
    }
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }
</script>

<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-slate-800">Daftar Staff Admin</h1>
        <button onclick="openCreateModal()" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded shadow-md">
            + Tambah Staff Baru
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
                    <th class="py-3 px-6 text-left">Username</th>
                    <th class="py-3 px-6 text-left">Email</th>
                    <th class="py-3 px-6 text-center">Role</th>
                    <th class="py-3 px-6 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
                <?php while($row = $staff_list->fetch_assoc()): ?>
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="py-3 px-6 text-left whitespace-nowrap">
                        <span class="font-medium"><?= $row['id_staff'] ?></span>
                    </td>
                    <td class="py-3 px-6 text-left">
                        <span><?= htmlspecialchars($row['username']) ?></span>
                    </td>
                    <td class="py-3 px-6 text-left">
                        <span><?= htmlspecialchars($row['email']) ?></span>
                    </td>
                    <td class="py-3 px-6 text-center">
                        <?php if($row['role'] === 'superadmin'): ?>
                            <span class="bg-purple-200 text-purple-700 py-1 px-3 rounded-full text-xs font-bold">Superadmin</span>
                        <?php else: ?>
                            <span class="bg-blue-200 text-blue-700 py-1 px-3 rounded-full text-xs font-bold capitalize"><?= htmlspecialchars($row['role']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="py-3 px-6 text-center">
                        <div class="flex item-center justify-center space-x-3">
                            <button onclick="openEditModal(<?= $row['id_staff'] ?>, '<?= addslashes($row['username']) ?>', '<?= addslashes($row['email']) ?>', '<?= $row['role'] ?>', '<?= addslashes($row['no_hp'] ?? '') ?>', '<?= addslashes($row['nim'] ?? '') ?>')" class="transform hover:text-blue-500 hover:scale-110">
                                ✏️ Edit
                            </button>
                            <?php if($row['role'] !== 'superadmin' || $_SESSION['admin_role'] === 'superadmin'): ?>
                            <form method="POST" action="" class="inline" onsubmit="return confirm('Hapus staff ini?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id_staff" value="<?= $row['id_staff'] ?>">
                                <button type="submit" class="transform hover:text-red-500 hover:scale-110">🗑️ Hapus</button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Staff -->
<div id="createModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-xl font-bold mb-4 text-slate-800">Tambah Staff Baru</h2>
        <form method="POST" action="">
            <input type="hidden" name="action" value="create">
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                <input type="text" name="username" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <input type="email" name="email" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <input type="password" name="password" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">No HP (Opsional)</label>
                <input type="text" name="no_hp" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">NIM (Opsional)</label>
                <input type="text" name="nim" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Role</label>
                <select name="role" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="admin">Admin</option>
                    <option value="kasir">Kasir</option>
                </select>
            </div>
            
            <div class="flex items-center justify-between">
                <button type="button" onclick="closeCreateModal()" class="text-gray-500 hover:text-gray-700 font-bold">Batal</button>
                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Tambahkan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Staff -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-xl font-bold mb-4 text-slate-800">Edit Staff</h2>
        <form method="POST" action="">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id_staff" id="edit_id_staff">
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                <input type="text" name="username" id="edit_username" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <input type="email" name="email" id="edit_email" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Password Baru (Biarkan kosong jika tidak diubah)</label>
                <input type="password" name="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">No HP (Opsional)</label>
                <input type="text" name="no_hp" id="edit_no_hp" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">NIM (Opsional)</label>
                <input type="text" name="nim" id="edit_nim" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Role</label>
                <select name="role" id="edit_role" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="admin">Admin</option>
                    <option value="kasir">Kasir</option>
                    <option value="superadmin">Superadmin</option>
                </select>
            </div>
            
            <div class="flex items-center justify-between">
                <button type="button" onclick="closeEditModal()" class="text-gray-500 hover:text-gray-700 font-bold">Batal</button>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Simpan</button>
            </div>
        </form>
    </div>
</div>

</div>
