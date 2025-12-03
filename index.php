<?php
session_start();
require 'config/db.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'produk';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD warung madura</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .content { margin-top: 20px; }
        .dark-mode { background-color: #121212 !important; color: #e0e0e0 !important; }
        .dark-mode .navbar-dark { background-color: #212121 !important; border-bottom: 1px solid #424242; }
        .dark-mode .table { color: #e0e0e0; }
        .dark-mode .table-striped > tbody > tr:nth-of-type(odd) { background-color: #2c2c2c; }
        .dark-mode .table-striped > tbody > tr:nth-of-type(even) { background-color: #1e1e1e; }
        .dark-mode .modal-content { background-color: #333333; color: #e0e0e0; }
    </style>
</head>
<body class="<?php echo isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark' ? 'dark-mode' : ''; ?>">

<?php 
include 'includes/header.php'; 
?>

<div class="container content">
    <div class="row">
        <div class="col-md-12">
            <?php
            $filePath = 'pages/' . $page . '.php';
            if (file_exists($filePath)) {
                include $filePath;
            } else {
                echo '<div class="alert alert-danger">Halaman tidak ditemukan!</div>';
            }
            ?>
        </div>
    </div>
</div>

<?php 
include 'includes/footer.php'; 
?>