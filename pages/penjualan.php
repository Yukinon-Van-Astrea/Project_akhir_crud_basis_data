<?php
$stmt_produk = $pdo->query("SELECT id_produk, nama_produk, harga, stok FROM produk WHERE stok > 0 ORDER BY nama_produk ASC");
$list_produk = $stmt_produk->fetchAll();

$stmt_pelanggan = $pdo->query("SELECT id_pelanggan, nama_pelanggan FROM pelanggan ORDER BY nama_pelanggan ASC");
$list_pelanggan = $stmt_pelanggan->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_penjualan') {
    $id_produk = $_POST['id_produk'];
    $id_pelanggan = $_POST['id_pelanggan'];
    $jumlah = (int)$_POST['jumlah'];

    try {
        $pdo->beginTransaction();
        $stmt_check = $pdo->prepare("SELECT harga, stok FROM produk WHERE id_produk = ?");
        $stmt_check->execute([$id_produk]);
        $produk = $stmt_check->fetch();

        if (!$produk) {
            throw new Exception("Produk tidak ditemukan.");
        }
        if ($produk['stok'] < $jumlah) {
            throw new Exception("Stok tidak mencukupi. Stok saat ini: " . $produk['stok']);
        }

        $harga = $produk['harga'];
        $total_harga = $harga * $jumlah;

        $stmt_insert = $pdo->prepare("INSERT INTO penjualan (id_produk, id_pelanggan, jumlah, total_harga) VALUES (?, ?, ?, ?)");
        $stmt_insert->execute([$id_produk, $id_pelanggan, $jumlah, $total_harga]);

        $stmt_update = $pdo->prepare("UPDATE produk SET stok = stok - ? WHERE id_produk = ?");
        $stmt_update->execute([$jumlah, $id_produk]);

        $pdo->commit();

        $_SESSION['message'] = "Transaksi penjualan berhasil dicatat! Total: Rp " . number_format($total_harga, 2, ',', '.');
        header("Location: index.php?page=penjualan");
        exit();

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $_SESSION['error'] = "Gagal mencatat transaksi: " . $e->getMessage();
        header("Location: index.php?page=penjualan");
        exit();
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id_penjualan = $_GET['id'];

    try {
        $pdo->beginTransaction();
        $stmt_get = $pdo->prepare("SELECT id_produk, jumlah FROM penjualan WHERE id_penjualan = ?");
        $stmt_get->execute([$id_penjualan]);
        $transaksi = $stmt_get->fetch();

        if (!$transaksi) {
            throw new Exception("Transaksi tidak ditemukan.");
        }

        $stmt_delete = $pdo->prepare("DELETE FROM penjualan WHERE id_penjualan = ?");
        $stmt_delete->execute([$id_penjualan]);

        $stmt_restore = $pdo->prepare("UPDATE produk SET stok = stok + ? WHERE id_produk = ?");
        $stmt_restore->execute([$transaksi['jumlah'], $transaksi['id_produk']]);

        $pdo->commit();

        $_SESSION['message'] = "Transaksi berhasil dihapus dan stok produk dikembalikan.";
        header("Location: index.php?page=penjualan");
        exit();
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $_SESSION['error'] = "Gagal menghapus transaksi: " . $e->getMessage();
        header("Location: index.php?page=penjualan");
        exit();
    }
}

$sql_read = "
    SELECT 
        p.id_penjualan, 
        pd.nama_produk, 
        pg.nama_pelanggan, 
        p.tanggal, 
        p.jumlah, 
        p.total_harga
    FROM penjualan p
    JOIN produk pd ON p.id_produk = pd.id_produk
    JOIN pelanggan pg ON p.id_pelanggan = pg.id_pelanggan
    ORDER BY p.id_penjualan ASC
";
$stmt_read = $pdo->query($sql_read);
$penjualan_data = $stmt_read->fetchAll();
?>

<h2>📈 Data Penjualan (Transaksi)</h2>

<?php 
if (isset($_SESSION['message'])): 
?>
    <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
<?php 
endif; 
if (isset($_SESSION['error'])): 
?>
    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php 
endif; 
?>

<button class="btn btn-success mb-3" data-toggle="modal" data-target="#createPenjualanModal">
    + Catat Transaksi Baru
</button>

<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>ID Transaksi</th>
                <th>Tanggal</th>
                <th>Nama Produk</th>
                <th>Pelanggan</th>
                <th>Jumlah</th>
                <th>Total Harga</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($penjualan_data as $transaksi): ?>
                <tr>
                    <td><?php echo htmlspecialchars($transaksi['id_penjualan']); ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($transaksi['tanggal'])); ?></td>
                    <td><?php echo htmlspecialchars($transaksi['nama_produk']); ?></td>
                    <td><?php echo htmlspecialchars($transaksi['nama_pelanggan']); ?></td>
                    <td><?php echo htmlspecialchars($transaksi['jumlah']); ?></td>
                    <td><?php echo 'Rp ' . number_format($transaksi['total_harga'], 2, ',', '.'); ?></td>
                    <td>
                        <a href="index.php?page=penjualan&action=delete&id=<?php echo $transaksi['id_penjualan']; ?>" 
                           class="btn btn-sm btn-danger" 
                           onclick="return confirm('Yakin ingin menghapus transaksi ini? Stok akan dikembalikan!');">
                            Hapus
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="createPenjualanModal" tabindex="-1" role="dialog" aria-labelledby="createPenjualanModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createPenjualanModalLabel">Catat Transaksi Penjualan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <form action="index.php?page=penjualan" method="POST">
        <div class="modal-body">
            <input type="hidden" name="action" value="create_penjualan">
            
            <div class="form-group">
                <label>Pilih Produk</label>
                <select name="id_produk" id="select-produk" class="form-control" required>
                    <option value="" data-harga="0" data-stok="0">-- Pilih Produk --</option>
                    <?php foreach ($list_produk as $prod): ?>
                        <option value="<?php echo $prod['id_produk']; ?>" 
                                data-harga="<?php echo $prod['harga']; ?>" 
                                data-stok="<?php echo $prod['stok']; ?>">
                            <?php echo htmlspecialchars($prod['nama_produk']); ?> (Stok: <?php echo $prod['stok']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="form-text text-muted">Stok tersedia: <span id="stok-info">0</span></small>
            </div>

            <div class="form-group">
                <label>Pilih Pelanggan</label>
                <select name="id_pelanggan" class="form-control" required>
                    <option value="">-- Pilih Pelanggan --</option>
                    <?php foreach ($list_pelanggan as $pel): ?>
                        <option value="<?php echo $pel['id_pelanggan']; ?>">
                            <?php echo htmlspecialchars($pel['nama_pelanggan']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Jumlah Beli</label>
                <input type="number" name="jumlah" id="input-jumlah" class="form-control" min="1" required>
            </div>

            <h5 class="mt-3">Total Harga: <span id="total-harga-display" class="badge badge-primary">Rp 0.00</span></h5>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-success" id="btn-catat-transaksi">Catat Transaksi</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
    $(document).ready(function() {
        const $selectProduk = $('#select-produk');
        const $inputJumlah = $('#input-jumlah');
        const $stokInfo = $('#stok-info');
        const $totalDisplay = $('#total-harga-display');
        const $btnSubmit = $('#btn-catat-transaksi');

        function updateCalculation() {
            const selectedOption = $selectProduk.find(':selected');
            const harga = parseFloat(selectedOption.data('harga')) || 0;
            const stok = parseInt(selectedOption.data('stok')) || 0;
            const jumlahBeli = parseInt($inputJumlah.val()) || 0;
            
            $stokInfo.text(stok);
            
            if (selectedOption.val() === "") {
                 $totalDisplay.text('Rp 0.00');
                 $btnSubmit.prop('disabled', true);
                 return;
            }
            const total = harga * jumlahBeli;

            const formatter = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 2,
            });
            
            $totalDisplay.text(formatter.format(total));

            let isValid = true;
            if (jumlahBeli <= 0) {
                isValid = false;
            } else if (jumlahBeli > stok) {
                isValid = false;
            }

            if (isValid) {
                $btnSubmit.prop('disabled', false);
                $inputJumlah.removeClass('is-invalid').addClass('is-valid');
            } else {
                $btnSubmit.prop('disabled', true);
                if(jumlahBeli > 0) {
                   $inputJumlah.addClass('is-invalid').removeClass('is-valid');
                } else {
                   $inputJumlah.removeClass('is-invalid').removeClass('is-valid');
                }
            }
        }
        $selectProduk.on('change', updateCalculation);
        $inputJumlah.on('input', updateCalculation);

        $('#createPenjualanModal').on('shown.bs.modal', function () {
            updateCalculation();
        });

    
        updateCalculation();
    });
</script>