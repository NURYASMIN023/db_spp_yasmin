<h3>Transaksi Pembayaran SPP</h3>
<form method="get" action="">
    nisn: <input type="text" name="nisn" value="<?php echo isset($_GET['nisn']) ? htmlspecialchars($_GET['nisn']) : ''; ?>" />
    <input type="hidden" id="open" name="open" value="data_pembayaran"/>
    <input type="submit" name="cari" value="Cari Siswa" />
</form>

<?php
include_once "../src/koneksi.php";

if (isset($_GET['nisn']) && trim($_GET['nisn']) != '') {
    $nisn_input = mysqli_real_escape_string($koneksi, trim($_GET['nisn']));

    // Query biodata siswa
    $sqlSiswa = mysqli_query($koneksi, "SELECT siswa.*, kelas.nama_kelas, spp.nominal 
                                         FROM siswa 
                                         JOIN kelas ON kelas.id_kelas = siswa.id_kelas 
                                         JOIN spp ON siswa.id_spp = spp.id_spp 
                                         WHERE siswa.nisn = '$nisn_input'");
    
    $dataSiswa = mysqli_fetch_array($sqlSiswa);

    if ($dataSiswa) {
        $nisn = $dataSiswa['nisn'];
?>

<h3>Biodata Siswa</h3>
<table>
    <tr>
        <td>NISN</td>
        <td>:</td>
        <td><?php echo htmlspecialchars($dataSiswa['nisn']); ?></td>
    </tr>
    <tr>
        <td>Nama Siswa</td>
        <td>:</td>
        <td><?php echo htmlspecialchars($dataSiswa['nama']); ?></td>
    </tr>
    <tr>
        <td>Kelas</td>
        <td>:</td>
        <td><?php echo htmlspecialchars($dataSiswa['nama_kelas']); ?></td>
    </tr>
</table>

<h3>Tagihan SPP Siswa</h3>
<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>No</th>
            <th>Bulan</th>
            <th>Tahun</th>
            <th>No. Bayar</th>
            <th>Tgl. Bayar</th>
            <th>Jumlah</th>
            <th>Keterangan</th>
            <th>Bayar</th>
        </tr>
    </thead>
    <tbody>
    <?php
    // Pilih kolom secara spesifik agar id_pembayaran tidak tertimpa
    $sqlPembayaran = mysqli_query($koneksi, "SELECT pembayaran.id_pembayaran, pembayaran.bulan_tagihan, 
                                                    pembayaran.tahun_tagihan, pembayaran.no_bayar, 
                                                    pembayaran.tgl_pembayaran, pembayaran.keterangan, spp.nominal 
                                             FROM pembayaran 
                                             JOIN spp ON pembayaran.id_spp = spp.id_spp 
                                             WHERE pembayaran.nisn = '$nisn'");
    $no = 1;
    while ($row = mysqli_fetch_array($sqlPembayaran)) {
        echo "<tr>
            <td align='center'>$no</td>
            <td>{$row['bulan_tagihan']}</td>
            <td align='center'>{$row['tahun_tagihan']}</td>
            <td align='center'>{$row['no_bayar']}</td>
            <td align='center'>{$row['tgl_pembayaran']}</td>
            <td>Rp. " . number_format($row['nominal'], 0, ',', '.') . "</td>
            <td align='center'>{$row['keterangan']}</td>
            <td align='center'>";
                
                if (empty($row['no_bayar'])) {
                    echo "<a href='proses_pembayaran.php?nisn={$nisn}&act=bayar&id_pembayaran={$row['id_pembayaran']}'>Bayar</a>";
                } else {
                    echo "-";
                }

        echo "</td>
        </tr>";
        $no++;
    }
    ?>
    </tbody>
</table>

<?php
    } else {
        echo "<p style='color:red;'>Data siswa dengan NISN <b>" . htmlspecialchars($nisn_input) . "</b> tidak ditemukan.</p>";
    }
}
?>

<p>Pembayaran SPP dilakukan dengan cara mencari tagihan siswa dengan NISN melalui form di atas, kemudian proses pembayaran</p>