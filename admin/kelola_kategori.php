<?php
include '../koneksi.php';
/** @var mysqli $conn */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['tambah'])) {
        $nama = $_POST['nama_kategori'];
        $deskripsi = $_POST['deskripsi'];
        $query = mysqli_query($conn, "INSERT INTO kategori_menu (nama_kategori, deskripsi) VALUES ('$nama', '$deskripsi')");
        if (!$query) die("Gagal Tambah Kategori: " . mysqli_error($conn));
    } elseif (isset($_POST['hapus'])) {
        $id = $_POST['id_kategori'];
        $query = mysqli_query($conn, "DELETE FROM kategori_menu WHERE id_kategori='$id'");
        if (!$query) die("Gagal Hapus Kategori: " . mysqli_error($conn));
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id_kategori'];
        $nama = $_POST['nama_kategori'];
        $deskripsi = $_POST['deskripsi'];
        $query = mysqli_query($conn, "UPDATE kategori_menu SET nama_kategori='$nama', deskripsi='$deskripsi' WHERE id_kategori='$id'");
        if (!$query) die("Gagal Edit Kategori: " . mysqli_error($conn));
    }
    header("Location: kelola_kategori.php");
    exit;
}

$kategori = mysqli_query($conn, "SELECT * FROM kategori_menu");

$data_edit = null;
if (isset($_GET['edit'])) {
    $id_edit = mysqli_real_escape_string($conn, $_GET['edit']);
    $result_edit = mysqli_query($conn, "SELECT * FROM kategori_menu WHERE id_kategori='$id_edit'");
    $data_edit = mysqli_fetch_assoc($result_edit);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <title>Kelola Kategori - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="bg-gray-100 min-h-screen text-slate-800 flex flex-col md:flex-row">

    <!-- 📂 Admin Sidebar Navigation -->
    <aside class="w-full md:w-64 bg-slate-900 text-gray-300 flex flex-col justify-between shrink-0 border-r border-slate-800">
        <div>
            <div class="h-16 flex items-center px-6 border-b border-slate-800 bg-slate-950">
                <span class="text-xl mr-2 animate-pulse">☕</span>
                <span class="font-extrabold text-sm tracking-wider text-orange-500">NGOLAB POS ADMIN</span>
            </div>
            <nav class="p-4 space-y-1">
                <a href="../showcase_admin.html" class="w-full flex items-center space-x-3 px-4 py-2.5 rounded-xl text-xs font-bold transition text-gray-400 hover:bg-slate-800 hover:text-white">
                    <span>📂</span> <span>Dashboard POS</span>
                </a>
                <a href="kelola_menu.php" class="w-full flex items-center space-x-3 px-4 py-2.5 rounded-xl text-xs font-bold transition text-gray-400 hover:bg-slate-800 hover:text-white">
                    <span>🍔</span> <span>Kelola Menu</span>
                </a>
                <a href="kelola_kategori.php" class="w-full flex items-center space-x-3 px-4 py-2.5 rounded-xl text-xs font-bold transition bg-orange-500 text-white shadow-sm">
                    <span>🏷️</span> <span>Kelola Kategori</span>
                </a>
                <a href="kelola_feedback.php" class="w-full flex items-center space-x-3 px-4 py-2.5 rounded-xl text-xs font-bold transition text-gray-400 hover:bg-slate-800 hover:text-white">
                    <span>💬</span> <span>Kelola Feedback</span>
                </a>
            </nav>
        </div>
        <div class="p-4 border-t border-slate-800 bg-slate-950 flex items-center justify-between">
            <a href="../index.html" class="text-xs text-red-400 font-bold hover:underline w-full text-center">Keluar ke Aplikasi</a>
        </div>
    </aside>

    <!-- 💻 Main Content Wrapper -->
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-16 bg-white border-b border-gray-200 px-6 flex items-center justify-between shrink-0 shadow-sm">
            <h2 class="text-lg font-bold text-slate-800">Kelola Kategori</h2>
        </header>

        <main class="flex-grow p-6 overflow-y-auto">
            <div class="container" style="max-width: 100%; margin: 0; padding: 0;">
                <h1>🏷️ Kelola Kategori Menu/Reward</h1>

                <?php if ($data_edit): ?>
                    <!-- Form Edit -->
                    <div class="card">
                        <h2>Edit Kategori</h2>
                        <form method="POST">
                            <input type="hidden" name="edit" value="1">
                            <input type="hidden" name="id_kategori" value="<?= $data_edit['id_kategori'] ?>">
                            <div class="form-group">
                                <label>Nama Kategori</label>
                                <input type="text" name="nama_kategori" value="<?= $data_edit['nama_kategori'] ?>" required class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Deskripsi</label>
                                <textarea name="deskripsi" rows="2" class="form-control"><?= $data_edit['deskripsi'] ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Kategori</button>
                            <a href="kelola_kategori.php" class="btn btn-link" style="margin-left: 15px;">Batal</a>
                        </form>
                    </div>
                <?php else: ?>
                    <!-- Form Tambah -->
                    <div class="card">
                        <h2>Tambah Kategori Baru</h2>
                        <form method="POST">
                            <input type="hidden" name="tambah" value="1">
                            <div class="form-group">
                                <label>Nama Kategori</label>
                                <input type="text" name="nama_kategori" required class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Deskripsi</label>
                                <textarea name="deskripsi" rows="2" class="form-control"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Simpan Kategori</button>
                        </form>
                    </div>
                <?php endif; ?>

                <!-- Daftar Kategori -->
                <div class="card">
                    <h2>Daftar Kategori</h2>
                    <div class="list-container">
                        <?php while ($row = mysqli_fetch_assoc($kategori)): ?>
                            <div class="list-item">
                                <div class="list-item-content">
                                    <h3><?= $row['nama_kategori'] ?></h3>
                                    <p><?= $row['deskripsi'] ?></p>
                                </div>
                                <div style="display: flex; gap: 8px; align-items: flex-start;">
                                    <a href="?edit=<?= $row['id_kategori'] ?>" class="btn btn-primary btn-sm" style="text-decoration:none;">Edit</a>
                                    <form method="POST" onsubmit="return confirm('Hapus kategori ini?');">
                                        <input type="hidden" name="hapus" value="1">
                                        <input type="hidden" name="id_kategori" value="<?= $row['id_kategori'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        <?php endwhile; ?>

                        <?php if (mysqli_num_rows($kategori) == 0) echo "<p class='text-gray'>Belum ada kategori.</p>"; ?>
                    </div>
                </div>
            </div>
</body>

</html>