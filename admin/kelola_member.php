<?php
if (!defined('IN_ADMIN')) {
    exit('No direct script access allowed');
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $id_member = $_POST['id_member'] ?? 0;
        
        if ($_POST['action'] === 'delete') {
            $stmt = $conn->prepare("DELETE FROM TABEL_MEMBER WHERE id_member = ?");
            $stmt->bind_param("i", $id_member);
            $stmt->execute();
            $message = "Member berhasil dihapus.";
        } elseif ($_POST['action'] === 'edit') {
            $username = $_POST['username'];
            $no_hp = $_POST['no_hp'];
            $nim = $_POST['nim'];
            $saldo_poin = $_POST['saldo_poin'];
            
            $stmt = $conn->prepare("UPDATE TABEL_MEMBER SET username=?, no_hp=?, nim=?, saldo_poin=? WHERE id_member=?");
            $stmt->bind_param("sssii", $username, $no_hp, $nim, $saldo_poin, $id_member);
            $stmt->execute();
            $message = "Data member berhasil diupdate.";
        } elseif ($_POST['action'] === 'promote') {
            if ($_SESSION['admin_role'] === 'superadmin') {
                $stmt = $conn->prepare("SELECT * FROM TABEL_MEMBER WHERE id_member = ?");
                $stmt->bind_param("i", $id_member);
                $stmt->execute();
                $user = $stmt->get_result()->fetch_assoc();
                
                if ($user) {
                    $stmt_insert = $conn->prepare("INSERT INTO TABEL_STAFF (username, email, password, role) VALUES (?, ?, ?, 'admin')");
                    $stmt_insert->bind_param("sss", $user['username'], $user['email'], $user['password']);
                    if ($stmt_insert->execute()) {
                        $message = "Member berhasil di-promote menjadi Admin.";
                    }
                }
            } else {
                $message = "Hanya Superadmin yang bisa melakukan promote.";
            }
        }
    }
}

$members = $conn->query("SELECT * FROM TABEL_MEMBER ORDER BY id_member DESC");
?>
<script>
    function openEditModal(id, username, no_hp, nim, saldo_poin) {
        document.getElementById('edit_id_member').value = id;
        document.getElementById('edit_username').value = username;
        document.getElementById('edit_no_hp').value = no_hp;
        document.getElementById('edit_nim').value = nim;
        document.getElementById('edit_saldo_poin').value = saldo_poin;
        document.getElementById('editModal').classList.remove('hidden');
    }
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }
</script>

<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-slate-800">Daftar Member Loyalty</h1>
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
                    <th class="py-3 px-6 text-center">NIM / HP</th>
                    <th class="py-3 px-6 text-center">Saldo Poin</th>
                    <th class="py-3 px-6 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
                <?php while($row = $members->fetch_assoc()): ?>
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="py-3 px-6 text-left whitespace-nowrap">
                        <span class="font-medium"><?= $row['id_member'] ?></span>
                    </td>
                    <td class="py-3 px-6 text-left">
                        <div class="flex items-center">
                            <?php if($row['avatar']): ?>
                                <img src="../<?= htmlspecialchars($row['avatar']) ?>" class="w-8 h-8 rounded-full mr-2 object-cover">
                            <?php else: ?>
                                <div class="w-8 h-8 rounded-full mr-2 bg-gray-300"></div>
                            <?php endif; ?>
                            <span><?= htmlspecialchars($row['username']) ?></span>
                        </div>
                    </td>
                    <td class="py-3 px-6 text-left">
                        <span><?= htmlspecialchars($row['email']) ?></span>
                    </td>
                    <td class="py-3 px-6 text-center">
                        <span><?= htmlspecialchars($row['nim']) ?> / <?= htmlspecialchars($row['no_hp']) ?></span>
                    </td>
                    <td class="py-3 px-6 text-center">
                        <span class="bg-orange-200 text-orange-700 py-1 px-3 rounded-full text-xs font-bold"><?= $row['saldo_poin'] ?> Poin</span>
                    </td>
                    <td class="py-3 px-6 text-center">
                        <div class="flex item-center justify-center space-x-3">
                            <button onclick="openEditModal(<?= $row['id_member'] ?>, '<?= addslashes($row['username']) ?>', '<?= addslashes($row['no_hp']) ?>', '<?= addslashes($row['nim']) ?>', <?= $row['saldo_poin'] ?>)" class="transform hover:text-blue-500 hover:scale-110">
                                ✏️ Edit
                            </button>
                            <form method="POST" action="" class="inline" onsubmit="return confirm('Hapus member ini?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id_member" value="<?= $row['id_member'] ?>">
                                <button type="submit" class="transform hover:text-red-500 hover:scale-110">🗑️ Hapus</button>
                            </form>
                            <?php if($_SESSION['admin_role'] === 'superadmin'): ?>
                            <form method="POST" action="" class="inline" onsubmit="return confirm('Promote member ini menjadi Admin?');">
                                <input type="hidden" name="action" value="promote">
                                <input type="hidden" name="id_member" value="<?= $row['id_member'] ?>">
                                <button type="submit" class="transform hover:text-green-500 hover:scale-110" title="Promote to Admin">⭐ Promote</button>
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

<!-- Modal Edit -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-xl font-bold mb-4 text-slate-800">Edit Member</h2>
        <form method="POST" action="">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id_member" id="edit_id_member">
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                <input type="text" name="username" id="edit_username" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">No HP</label>
                <input type="text" name="no_hp" id="edit_no_hp" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">NIM</label>
                <input type="text" name="nim" id="edit_nim" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Saldo Poin</label>
                <input type="number" name="saldo_poin" id="edit_saldo_poin" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            
            <div class="flex items-center justify-between">
                <button type="button" onclick="closeEditModal()" class="text-gray-500 hover:text-gray-700 font-bold">Batal</button>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Simpan</button>
            </div>
        </form>
    </div>
</div>

</div>
