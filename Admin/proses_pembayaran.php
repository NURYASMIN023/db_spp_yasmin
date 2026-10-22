<?php
session_start();
include "../src/koneksi.php";

if (isset($_GET['act']) && $_GET['act'] == 'bayar') {

    $id_pembayaran = $_GET['id_pembayaran'];
    $nisn          = $_GET['nisn'];

    // Membuat nomor pembayaran
    $today = date("ymd");
    $query = mysqli_query($koneksi, "SELECT max(no_bayar) AS last FROM pembayaran WHERE no_bayar LIKE '$today%'");
    $data  = mysqli_fetch_array($query);

    $lastNoBayar   = $data['last'];
    // Gunakan (int) agar jika $lastNoBayar kosong, nilainya otomatis jadi 0
    $lastNoUrut    = (int) substr($lastNoBayar, 6, 4);
    $nextNoUrut    = $lastNoUrut + 1;
    $nextNoBayar   = $today . sprintf('%04s', $nextNoUrut);

    // Tanggal Bayar
    $tgl_pembayaran = date('Y-m-d');

    // ID admin
    $admin = isset($_SESSION['id_petugas']) ? $_SESSION['id_petugas'] : '';

    mysqli_query($koneksi, "UPDATE pembayaran SET no_bayar='$nextNoBayar',
                                                  tgl_pembayaran='$tgl_pembayaran',
                                                  keterangan='LUNAS',
                                                  id_petugas='$admin'
                                            WHERE id_pembayaran='$id_pembayaran'");

    header('location:halaman_admin.php?open=data_pembayaran&nisn=' . $nisn);
    exit();
}
?>