<!DOCTYPE html>
<html>
<head>
    <title>SPP ONLINE</title>
    <link rel="stylesheet" type="text/css" href="../src/style.css">
</head>
<body>

<?php
session_start();

// Cek apakah sudah login
if (!isset($_SESSION['level']) || $_SESSION['level'] == "") {
    header("Location: index.php?pesan=gagal");
    exit;
}
?>

<br><br>

<!-- Header -->
<header>
    <h1 class="judul">PEMBAYARAN SPP ONLINE SMK NEGERI 3 BANJARBARU</h1>
</header>

<div class="wrap">

    <!-- Menu -->
    <nav class="menu">
        <?php include "menu.php"; ?>
    </nav>

    <!-- Isi -->
    <div class="blog">
        <div class="conteudo">
            <?php include "buka_file.php"; ?>
        </div>
    </div>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="widget">
            <h2>Selamat Datang, <?php echo $_SESSION['username']; ?></h2>
            <p>Selamat datang di aplikasi pembayaran SPP SMK Negeri 3 Banjarbaru.</p>
        </div>
    </aside>

</div>

</body>
</html>