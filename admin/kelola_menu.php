<?php
include '../koneksi.php';
/** @var mysqli $conn */
if (!is_dir('../uploads/menus')) {
    mkdir('../uploads/menus', 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['tambah'])) {
        $nama = $_POST['nama_menu'];
        $harga = $_POST['harga'];
        $kategori = $_POST['kategori'];
        $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
        $gambar = $_FILES['gambar']['name'];
        $poin = floor($harga / 10000); // Hitung poin otomatis
        if ($gambar) {
            move_uploaded_file($_FILES['gambar']['tmp_name'], '../uploads/menus/' . $gambar);
        }
        $query = mysqli_query($conn, "INSERT INTO menus (nama_menu, harga, kategori, deskripsi, gambar, poin_didapat) VALUES ('$nama', '$harga', '$kategori', '$deskripsi', '$gambar', '$poin')");
        if (!$query) die("Gagal Tambah Menu: " . mysqli_error($conn));
    } elseif (isset($_POST['hapus'])) {
        $id = $_POST['id'];
        $query = mysqli_query($conn, "DELETE FROM menus WHERE id='$id'");
        if (!$query) die("Gagal Hapus Menu: " . mysqli_error($conn));
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $nama = $_POST['nama_menu'];
        $harga = $_POST['harga'];
        $kategori = $_POST['kategori'];
        $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
        $poin = floor($harga / 10000);

        $update_query = "UPDATE menus SET nama_menu='$nama', harga='$harga', kategori='$kategori', deskripsi='$deskripsi', poin_didapat='$poin'";
        if (!empty($_FILES['gambar']['name'])) {
            $gambar = $_FILES['gambar']['name'];
            move_uploaded_file($_FILES['gambar']['tmp_name'], '../uploads/menus/' . $gambar);
            $update_query .= ", gambar='$gambar'";
        }
        $update_query .= " WHERE id='$id'";
        $query = mysqli_query($conn, $update_query);
        if (!$query) die("Gagal Edit Menu: " . mysqli_error($conn));
    }
    header("Location: kelola_menu.php");
    exit;
}

$menus = mysqli_query($conn, "SELECT * FROM menus");
$kategori_list = mysqli_query($conn, "SELECT * FROM kategori_menu");

$data_edit = null;
if (isset($_GET['edit'])) {
    $id_edit = mysqli_real_escape_string($conn, $_GET['edit']);
    $result_edit = mysqli_query($conn, "SELECT * FROM menus WHERE id='$id_edit'");
    $data_edit = mysqli_fetch_assoc($result_edit);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <title>Kelola Menu - Admin</title>
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
                <a href="kelola_menu.php" class="w-full flex items-center space-x-3 px-4 py-2.5 rounded-xl text-xs font-bold transition bg-orange-500 text-white shadow-sm">
                    <span>🍔</span> <span>Kelola Menu</span>
                </a>
                <a href="kelola_kategori.php" class="w-full flex items-center space-x-3 px-4 py-2.5 rounded-xl text-xs font-bold transition text-gray-400 hover:bg-slate-800 hover:text-white">
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
            <h2 class="text-lg font-bold text-slate-800">Kelola Menu</h2>
        </header>

        <main class="flex-grow p-6 overflow-y-auto">
            <div class="container" style="max-width: 100%; margin: 0; padding: 0;">
                <h1>🍔 Kelola Menu (Katalog)</h1>

                <?php if ($data_edit): ?>
                    <!-- Form Edit -->
                    <div class="card">
                        <h2>Edit Menu</h2>
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="edit" value="1">
                            <input type="hidden" name="id" value="<?= $data_edit['id'] ?>">
                            <div class="form-group">
                                <label>Nama Menu</label>
                                <input type="text" name="nama_menu" value="<?= $data_edit['nama_menu'] ?>" required class="form-control">
                            </div>
                            <div class="grid-2">
                                <div class="form-group">
                                    <label>Harga (Rp)</label>
                                    <input type="number" name="harga" value="<?= $data_edit['harga'] ?>" required class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Kategori</label>
                                    <select name="kategori" required class="form-control">
                                        <?php
                                        mysqli_data_seek($kategori_list, 0);
                                        while ($kat = mysqli_fetch_assoc($kategori_list)):
                                        ?>
                                            <option value="<?= strtolower($kat['nama_kategori']) ?>" <?= $data_edit['kategori'] == strtolower($kat['nama_kategori']) ? 'selected' : '' ?>><?= $kat['nama_kategori'] ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Deskripsi Menu</label>
                                <textarea name="deskripsi" rows="2" required class="form-control"><?= $data_edit['deskripsi'] ?? '' ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>Gambar Menu (Opsional - Biarkan kosong jika tidak diubah)</label>
                                <input type="file" name="gambar" accept="image/*" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-primary">Update Menu</button>
                            <a href="kelola_menu.php" class="btn btn-link" style="margin-left:15px;">Batal</a>
                        </form>
                    </div>
                <?php else: ?>
                    <!-- Form Tambah -->
                    <div class="card">
                        <h2>Tambah Menu Baru</h2>
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="tambah" value="1">
                            <div class="form-group">
                                <label>Nama Menu</label>
                                <input type="text" name="nama_menu" required class="form-control">
                            </div>
                            <div class="grid-2">
                                <div class="form-group">
                                    <label>Harga (Rp)</label>
                                    <input type="number" name="harga" required class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Kategori</label>
                                    <select name="kategori" required class="form-control">
                                        <?php
                                        mysqli_data_seek($kategori_list, 0);
                                        while ($kat = mysqli_fetch_assoc($kategori_list)):
                                        ?>
                                            <option value="<?= strtolower($kat['nama_kategori']) ?>"><?= $kat['nama_kategori'] ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Deskripsi Menu</label>
                                <textarea name="deskripsi" rows="2" required class="form-control"></textarea>
                            </div>
                            <div class="form-group">
                                <label>Gambar Menu (Opsional)</label>
                                <input type="file" name="gambar" accept="image/*" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-primary">Simpan Menu</button>
                        </form>
                    </div>
                <?php endif; ?>

                <!-- Daftar Menu -->
                <div class="card">
                    <h2>Daftar Menu</h2>
                    <div class="grid-2">
                        <?php while ($row = mysqli_fetch_assoc($menus)): ?>
                            <div class="list-item">
                                <div class="list-item-content">
                                    <h3><?= $row['nama_menu'] ?> (Rp <?= number_format($row['harga'], 0, ',', '.') ?>)</h3>
                                    <span class="badge"><?= ucfirst($row['kategori']) ?></span>
                                </div>
                                <div style="display: flex; gap: 8px; align-items: flex-start;">
                                    <a href="?edit=<?= $row['id'] ?>" class="btn btn-primary btn-sm" style="text-decoration:none;">Edit</a>
                                    <form method="POST" onsubmit="return confirm('Hapus menu?');">
                                        <input type="hidden" name="hapus" value="1">
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
</body>

</html>