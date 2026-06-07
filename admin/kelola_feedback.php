<?php
if (!defined('IN_ADMIN')) exit('No direct script access allowed');

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = $_POST['id_feedback'];
        $stmt = $conn->prepare("DELETE FROM TABEL_FEEDBACK WHERE id_feedback=?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $message = "Feedback berhasil dihapus.";
        } else {
            $message = "Gagal menghapus feedback: " . $stmt->error;
        }
    }
}

$feedbacks = mysqli_query($conn, "SELECT * FROM TABEL_FEEDBACK ORDER BY id_feedback DESC");
?>

<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-slate-800">💬 Kelola Ulasan (Feedback)</h1>
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
                    <th class="py-3 px-6 text-left w-1/4">Pelanggan</th>
                    <th class="py-3 px-6 text-center w-1/6">Rating</th>
                    <th class="py-3 px-6 text-left w-2/5">Ulasan</th>
                    <th class="py-3 px-6 text-center">Waktu</th>
                    <th class="py-3 px-6 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
                <?php while ($row = mysqli_fetch_assoc($feedbacks)): ?>
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="py-3 px-6 text-left">
                        <span class="font-bold block text-slate-800"><?= htmlspecialchars($row['nama_user']) ?></span>
                        <span class="text-xs text-gray-400">ID Member: <?= $row['id_member'] ? $row['id_member'] : 'Anonim' ?></span>
                    </td>
                    <td class="py-3 px-6 text-center">
                        <span class="text-yellow-500 text-lg"><?= str_repeat('★', $row['rating']) ?></span>
                        <span class="text-gray-300 text-lg"><?= str_repeat('★', 5 - $row['rating']) ?></span>
                    </td>
                    <td class="py-3 px-6 text-left">
                        <p class="text-gray-700 italic">"<?= htmlspecialchars($row['ulasan']) ?>"</p>
                    </td>
                    <td class="py-3 px-6 text-center whitespace-nowrap text-xs">
                        <?= date('d M Y, H:i', strtotime($row['created_at'])) ?>
                    </td>
                    <td class="py-3 px-6 text-center">
                        <div class="flex item-center justify-center">
                            <form method="POST" action="" class="inline" onsubmit="return confirm('Hapus ulasan ini secara permanen?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id_feedback" value="<?= $row['id_feedback'] ?>">
                                <button type="submit" class="transform hover:text-red-500 hover:scale-110">🗑️ Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if (mysqli_num_rows($feedbacks) == 0): ?>
                <tr>
                    <td colspan="5" class="py-8 text-center text-gray-500 font-medium">Belum ada ulasan yang masuk.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>