<?php
global $page;
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="index.php">Dashboard Warung Madura</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item <?php echo $page == 'produk' ? 'active' : ''; ?>">
        <a class="nav-link" href="index.php?page=produk">🛍️ Produk</a>
      </li>
      <li class="nav-item <?php echo $page == 'pelanggan' ? 'active' : ''; ?>">
        <a class="nav-link" href="index.php?page=pelanggan">👥 Pelanggan</a>
      </li>
      <li class="nav-item <?php echo $page == 'penjualan' ? 'active' : ''; ?>">
        <a class="nav-link" href="index.php?page=penjualan">📈 Penjualan</a>
      </li>
    </ul>
    <div class="custom-control custom-switch mr-3">
        <input type="checkbox" class="custom-control-input" id="themeSwitch" <?php echo isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark' ? 'checked' : ''; ?>>
        <label class="custom-control-label text-light" for="themeSwitch">
            Mode Gelap
        </label>
    </div>
    <span class="navbar-text">
      Status: Koneksi OK
    </span>
  </div>
</nav>