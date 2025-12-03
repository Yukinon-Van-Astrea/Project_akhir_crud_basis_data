<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $nama_produk = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    try {
        $stmt = $pdo->prepare("INSERT INTO produk (nama_produk, harga, stok) VALUES (?, ?, ?)");
        $stmt->execute([$nama_produk, $harga, $stok]);
        $_SESSION['message'] = "Produk berhasil ditambahkan!";
    } catch (Exception $e) { $_SESSION['error'] = "Gagal menambah produk: " . $e->getMessage(); }
    header("Location: index.php?page=produk"); exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id_produk = $_POST['id_produk'];
    $nama_produk = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    try {
        $stmt = $pdo->prepare("UPDATE produk SET nama_produk = ?, harga = ?, stok = ? WHERE id_produk = ?");
        $stmt->execute([$nama_produk, $harga, $stok, $id_produk]);
        $_SESSION['message'] = "Produk berhasil diubah!";
    } catch (Exception $e) { $_SESSION['error'] = "Gagal mengubah produk: " . $e->getMessage(); }
    header("Location: index.php?page=produk"); exit();
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id_produk = $_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM produk WHERE id_produk = ?");
        $stmt->execute([$id_produk]);
        $_SESSION['message'] = "Produk berhasil dihapus!";
    } catch (Exception $e) { $_SESSION['error'] = "Gagal menghapus produk: Mungkin ada data penjualan yang terkait."; }
    header("Location: index.php?page=produk"); exit();
}

$stmt = $pdo->query("SELECT * FROM produk ORDER BY id_produk ASC"); 
$produk_data = $stmt->fetchAll();
?>

<h2>🛍️ Data Produk</h2>

<?php if (isset($_SESSION['message'])): ?> <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div> <?php endif; ?>
<?php if (isset($_SESSION['error'])): ?> <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div> <?php endif; ?>

<button class="btn btn-primary mb-3" data-toggle="modal" data-target="#createModal">+ Tambah Produk Baru</button>

<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead>
            <tr><th>ID Produk</th><th>Nama Produk</th><th>Harga</th><th>Stok</th><th>Aksi</th></tr>
        </thead>
        <tbody>
            <?php foreach ($produk_data as $produk): ?>
                <tr>
                    <td><?php echo htmlspecialchars($produk['id_produk']); ?></td>
                    <td><?php echo htmlspecialchars($produk['nama_produk']); ?></td>
                    <td><?php echo 'Rp ' . number_format($produk['harga'], 2, ',', '.'); ?></td>
                    <td><?php echo htmlspecialchars($produk['stok']); ?></td>
                    <td>
                        <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editModal" 
                                data-id="<?php echo $produk['id_produk']; ?>" 
                                data-nama="<?php echo htmlspecialchars($produk['nama_produk']); ?>"
                                data-harga="<?php echo htmlspecialchars($produk['harga']); ?>"
                                data-stok="<?php echo htmlspecialchars($produk['stok']); ?>">Edit</button>
                        <a href="index.php?page=produk&action=delete&id=<?php echo $produk['id_produk']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus produk ini?');">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="createModal" tabindex="-1" role="dialog"><div class="modal-dialog" role="document"><div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Tambah Produk Baru</h5><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button></div>
      <form action="index.php?page=produk" method="POST"><div class="modal-body">
            <input type="hidden" name="action" value="create">
            <div class="form-group"><label>Nama Produk</label><input type="text" name="nama_produk" class="form-control" required></div>
            <div class="form-group"><label>Harga (Rp)</label><input type="number" step="0.01" name="harga" class="form-control" required></div>
            <div class="form-group"><label>Stok</label><input type="number" name="stok" class="form-control" required></div>
        </div><div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary">Simpan Produk</button>
        </div></form>
    </div></div></div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog"><div class="modal-dialog" role="document"><div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Ubah Data Produk</h5><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button></div>
      <form action="index.php?page=produk" method="POST"><div class="modal-body">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id_produk" id="edit-id-produk">
            <div class="form-group"><label>Nama Produk</label><input type="text" name="nama_produk" id="edit-nama-produk" class="form-control" required></div>
            <div class="form-group"><label>Harga (Rp)</label><input type="number" step="0.01" name="harga" id="edit-harga" class="form-control" required></div>
            <div class="form-group"><label>Stok</label><input type="number" name="stok" id="edit-stok" class="form-control" required></div>
        </div><div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div></form>
    </div></div></div>

<script>
    $('#editModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); 
        var id = button.data('id');
        var nama = button.data('nama');
        var harga = button.data('harga');
        var stok = button.data('stok');
        var modal = $(this);
        modal.find('#edit-id-produk').val(id);
        modal.find('#edit-nama-produk').val(nama);
        modal.find('#edit-harga').val(harga);
        modal.find('#edit-stok').val(stok);
    });
</script>