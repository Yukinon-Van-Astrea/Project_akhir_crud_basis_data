<?php
// Logika CREATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_pelanggan') {
    $nama_pelanggan = $_POST['nama_pelanggan'];
    $no_hp = $_POST['no_hp'];
    try {
        $stmt = $pdo->prepare("INSERT INTO pelanggan (nama_pelanggan, no_hp) VALUES (?, ?)");
        $stmt->execute([$nama_pelanggan, $no_hp]);
        $_SESSION['message'] = "Pelanggan berhasil ditambahkan!";
    } catch (Exception $e) { $_SESSION['error'] = "Gagal menambah pelanggan: " . $e->getMessage(); }
    header("Location: index.php?page=pelanggan"); exit();
}

// Logika UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_pelanggan') {
    $id_pelanggan = $_POST['id_pelanggan'];
    $nama_pelanggan = $_POST['nama_pelanggan'];
    $no_hp = $_POST['no_hp'];
    try {
        $stmt = $pdo->prepare("UPDATE pelanggan SET nama_pelanggan = ?, no_hp = ? WHERE id_pelanggan = ?");
        $stmt->execute([$nama_pelanggan, $no_hp, $id_pelanggan]);
        $_SESSION['message'] = "Data pelanggan berhasil diubah!";
    } catch (Exception $e) { $_SESSION['error'] = "Gagal mengubah pelanggan: " . $e->getMessage(); }
    header("Location: index.php?page=pelanggan"); exit();
}

// Logika DELETE
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id_pelanggan = $_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM pelanggan WHERE id_pelanggan = ?");
        $stmt->execute([$id_pelanggan]);
        $_SESSION['message'] = "Pelanggan berhasil dihapus!";
    } catch (Exception $e) { $_SESSION['error'] = "Gagal menghapus pelanggan. Pastikan pelanggan tidak memiliki riwayat penjualan."; }
    header("Location: index.php?page=pelanggan"); exit();
}

// Logika READ (ID terkecil ke terbesar)
$stmt = $pdo->query("SELECT * FROM pelanggan ORDER BY id_pelanggan ASC"); 
$pelanggan_data = $stmt->fetchAll();
?>

<h2>👥 Data Pelanggan</h2>

<?php if (isset($_SESSION['message'])): ?> <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div> <?php endif; ?>
<?php if (isset($_SESSION['error'])): ?> <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div> <?php endif; ?>

<button class="btn btn-primary mb-3" data-toggle="modal" data-target="#createPelangganModal">+ Tambah Pelanggan Baru</button>

<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead>
            <tr><th>ID Pelanggan</th><th>Nama Pelanggan</th><th>Nomor HP</th><th>Aksi</th></tr>
        </thead>
        <tbody>
            <?php foreach ($pelanggan_data as $pelanggan): ?>
                <tr>
                    <td><?php echo htmlspecialchars($pelanggan['id_pelanggan']); ?></td>
                    <td><?php echo htmlspecialchars($pelanggan['nama_pelanggan']); ?></td>
                    <td><?php echo htmlspecialchars($pelanggan['no_hp']); ?></td>
                    <td>
                        <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editPelangganModal" 
                                data-id="<?php echo $pelanggan['id_pelanggan']; ?>" 
                                data-nama="<?php echo htmlspecialchars($pelanggan['nama_pelanggan']); ?>"
                                data-hp="<?php echo htmlspecialchars($pelanggan['no_hp']); ?>">Edit</button>
                        <a href="index.php?page=pelanggan&action=delete&id=<?php echo $pelanggan['id_pelanggan']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus pelanggan ini?');">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="createPelangganModal" tabindex="-1" role="dialog"><div class="modal-dialog" role="document"><div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Tambah Pelanggan Baru</h5><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button></div>
      <form action="index.php?page=pelanggan" method="POST"><div class="modal-body">
            <input type="hidden" name="action" value="create_pelanggan">
            <div class="form-group"><label>Nama Pelanggan</label><input type="text" name="nama_pelanggan" class="form-control" required></div>
            <div class="form-group"><label>Nomor HP (Opsional)</label><input type="text" name="no_hp" class="form-control"></div>
        </div><div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary">Simpan Pelanggan</button>
        </div></form>
    </div></div></div>

<div class="modal fade" id="editPelangganModal" tabindex="-1" role="dialog"><div class="modal-dialog" role="document"><div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Ubah Data Pelanggan</h5><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button></div>
      <form action="index.php?page=pelanggan" method="POST"><div class="modal-body">
            <input type="hidden" name="action" value="update_pelanggan">
            <input type="hidden" name="id_pelanggan" id="edit-id-pelanggan">
            <div class="form-group"><label>Nama Pelanggan</label><input type="text" name="nama_pelanggan" id="edit-nama-pelanggan" class="form-control" required></div>
            <div class="form-group"><label>Nomor HP (Opsional)</label><input type="text" name="no_hp" id="edit-no-hp" class="form-control"></div>
        </div><div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div></form>
    </div></div></div>

<script>
    $('#editPelangganModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); 
        var id = button.data('id');
        var nama = button.data('nama');
        var hp = button.data('hp');
        var modal = $(this);
        modal.find('#edit-id-pelanggan').val(id);
        modal.find('#edit-nama-pelanggan').val(nama);
        modal.find('#edit-no-hp').val(hp);
    });
</script>