<?php
include_once "../src/koneksi.php";

# SKRIP TOMBOL SIMPAN DIKLIK
if (isset($_POST['btnSimpan'])) {
    // Sanitasi Input untuk Mencegah SQL Injection
    $nisn     = mysqli_real_escape_string($koneksi, trim($_POST['nisn']));
    $nis      = mysqli_real_escape_string($koneksi, trim($_POST['nis']));
    $nama     = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
    $id_kelas = mysqli_real_escape_string($koneksi, trim($_POST['id_kelas']));
    $alamat   = mysqli_real_escape_string($koneksi, trim($_POST['alamat']));
    $no_telp  = mysqli_real_escape_string($koneksi, trim($_POST['no_telp']));
    $id_spp   = mysqli_real_escape_string($koneksi, trim($_POST['id_spp']));

    $arraybulan = array(
        "Juli", "Agustus", "September", "Oktober", "Nopember", "Desember",
        "Januari", "Februari", "Maret", "April", "Mei", "Juni"
    );

    // Validasi Form Inputs
    $pesanError = array();
    if (empty($nisn)) {
        $pesanError[] = "Data <b>NISN</b> tidak boleh kosong !";
    }
    if (empty($nis)) {
        $pesanError[] = "Data <b>NIS</b> tidak boleh kosong !";
    }
    if (empty($nama)) {
        $pesanError[] = "Data <b>NAMA</b> tidak boleh kosong !";
    }
    if (empty($id_kelas) || $id_kelas == "Kosong") {
        $pesanError[] = "Data <b>KELAS</b> belum dipilih !";
    }
    if (empty($alamat)) {
        $pesanError[] = "Data <b>ALAMAT</b> tidak boleh kosong !";
    }
    if (empty($no_telp)) {
        $pesanError[] = "Data <b>NOMOR TELEPON</b> tidak boleh kosong !";
    }
    if (empty($id_spp) || $id_spp == "Kosong") {
        $pesanError[] = "Data <b>NOMINAL SPP</b> belum dipilih !";
    }

    // PENGECEKAN DUPLIKASI NISN / NIS DI DATABASE
    if (!empty($nisn)) {
        $cekNisn = mysqli_query($koneksi, "SELECT nisn FROM siswa WHERE nisn = '$nisn'");
        if (mysqli_num_rows($cekNisn) >= 1) {
            $pesanError[] = "NISN <b>$nisn</b> sudah terdaftar! Gunakan NISN lain.";
        }
    }

    // Menampilkan Pesan Error dari Form
    if (count($pesanError) >= 1) {
        $noPesan = 0;
        foreach ($pesanError as $pesan_tampil) {
            $noPesan++;
            echo "&nbsp; $noPesan. $pesan_tampil<br>";
        }
        echo "<br>";
    } else {
        // Skrip Simpan data ke Database
        $sql = mysqli_query($koneksi, "INSERT INTO siswa (nisn, nis, nama, id_kelas, alamat, no_telp, id_spp)
                                       VALUES ('$nisn', '$nis', '$nama', '$id_kelas', '$alamat', '$no_telp', '$id_spp')");

        if ($sql) {
            // Ambil ID Petugas dari session (jika ada), atau beri nilai default misalnya '1'/ 'ADM01'
            $id_petugas = isset($_SESSION['id_petugas']) ? $_SESSION['id_petugas'] : '1';
            $tahunsekarang = date('Y');

            for ($i = 0; $i < 12; $i++) {
                $bulan = $arraybulan[$i];

                if ($i == 6) {
                    $tahunsekarang++;
                }

                // QUERY SAMA PERSIS DENGAN STRUKTUR PHPMYADMIN ANDA (5 Kolom = 5 Nilai)
                $simpan_tagihan = mysqli_query($koneksi, "INSERT INTO pembayaran (id_petugas, nisn, bulan_tagihan, tahun_tagihan, id_spp)
                                                          VALUES ('$id_petugas', '$nisn', '$bulan', '$tahunsekarang', '$id_spp')");
                
                // Cek jika ada error saat insert ke DB
                if (!$simpan_tagihan) {
                    die("Gagal insert tagihan bulan $bulan: " . mysqli_error($koneksi));
                }
            }

            echo "<center><b><font color='green' size='4'><p>Data Berhasil disimpan</p></font></b></center><br>
                  <meta http-equiv='refresh' content='2; url=?open=data_siswa'>";
        } else {
            echo "<center><b><font color='red' size='4'><p>Data Gagal Disimpan!</p></font></b></center><br>
                  <meta http-equiv='refresh' content='2; url=?open=data_siswa'>";
        }
        exit;
    }
}
?>

<form action="" method="post">
<table cellspacing="1" cellpadding="3">
    <tr>
        <td colspan="3" bgcolor="#CCCCCC"><strong>TAMBAH DATA SISWA</strong></td>
    </tr>
    <tr>
        <td>NISN</td>
        <td>:</td>
        <td><input name="nisn" type="text" size="10" maxlength="10" value="<?php echo isset($_POST['nisn']) ? htmlspecialchars($_POST['nisn']) : ''; ?>" /></td>
    </tr>
    <tr>
        <td>NIS</td>
        <td>:</td>
        <td><input name="nis" type="text" size="20" maxlength="10" value="<?php echo isset($_POST['nis']) ? htmlspecialchars($_POST['nis']) : ''; ?>" /></td>
    </tr>
    <tr>
        <td>NAMA</td>
        <td>:</td>
        <td><input name="nama" type="text" size="50" maxlength="50" value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>" /></td>
    </tr>
    <tr>
        <td>KELAS</td>
        <td>:</td>
        <td>
            <select name="id_kelas">
                <option value="Kosong"> ..... </option>
                <?php
                    $tampilkanKelas = mysqli_query($koneksi, "SELECT * FROM kelas");
                    while ($bacaData = mysqli_fetch_array($tampilkanKelas)) {
                        $cek = (isset($_POST['id_kelas']) && $_POST['id_kelas'] == $bacaData['id_kelas']) ? "selected" : "";
                        echo "<option value='{$bacaData['id_kelas']}' $cek>{$bacaData['nama_kelas']}</option>";
                    }
                ?>
            </select>
        </td>
    </tr>
    <tr>
        <td>Alamat</td>
        <td>:</td>
        <td><input name="alamat" type="text" size="50" maxlength="50" value="<?php echo isset($_POST['alamat']) ? htmlspecialchars($_POST['alamat']) : ''; ?>" /></td>
    </tr>
    <tr>
        <td>No. Telepon</td>
        <td>:</td>
        <td><input name="no_telp" type="text" size="50" maxlength="50" value="<?php echo isset($_POST['no_telp']) ? htmlspecialchars($_POST['no_telp']) : ''; ?>" /></td>
    </tr>
    <tr>
        <td>Nominal SPP</td>
        <td>:</td>
        <td>
            <select name="id_spp">
                <option value="Kosong"> ..... </option>
                <?php
                    $tampilkanSPP = mysqli_query($koneksi, "SELECT * FROM spp");
                    while ($bacaData = mysqli_fetch_array($tampilkanSPP)) {
                        $cek = (isset($_POST['id_spp']) && $_POST['id_spp'] == $bacaData['id_spp']) ? "selected" : "";
                        echo "<option value='{$bacaData['id_spp']}' $cek>{$bacaData['nominal']}</option>";
                    }
                ?>
            </select>
        </td>
    </tr>
    <tr>
        <td>&nbsp;</td> 
        <td>&nbsp;</td>
        <td><input name="btnSimpan" type="submit" value="Simpan" /></td>
    </tr>
</table>
</form>