
<?php 
  include_once("../config.php");
  include_once("../koneksi/koneksi169.php");
  include_once("../koneksi/koneksi99.php");

  if (@$_GET['daftarkanPasien']) {
    $user = $_SESSION['simrs']['pegawai']['kode'];
    $kodeAntrean = $_POST['kodeAntrean'];
    $nomorAntrean = $_POST['nomorAntrean'];
    $NIPDokter = $_POST['NIPDokter'];
    $noRM = $_POST['noRM'];
    $jenisKelamin = $_POST['jenisKelamin'];
    $kodeDokter = $_POST['dokter'];
    $kodeUmur = $_POST['kodeUmur'];

    // mencari nKodeMutasi dan episode
    $query = "SELECT IFNULL(MAX(nKode),0)+1 AS nKodeMutasiPasien,
              (SELECT IFNULL(MAX(nEpisode),0)+1 AS episode FROM _mutasi_pasien WHERE YEAR(tTanggal) = YEAR(NOW())) AS episode,
              (SELECT IFNULL(MAX(nKode),0)+1 AS nKodeTindakan FROM _mutasi_pasien_tindakan) AS nKodeTindakan
              FROM _mutasi_pasien;
            ";
    $conn->query($query);
		$mutasi = $conn->result();
    $kodeMutasi = $mutasi['nKodeMutasiPasien'];
    $episodeMutasi = $mutasi['episode'];
    $KodeTindakan = $mutasi['nKodeTindakan'];

    // insert ke mutasi pasien
		$query = "INSERT INTO _mutasi_pasien
                (nKode, tTanggal, nNoRM, szCaraBayar, nPoli, nNIKOlehLoket, nEpisode, nNoAntrianPoli, szAsuransi, nKodeUmur, nStatusPoli, nBerkas, szJK, nKodeDokter)
              SELECT * FROM
                (SELECT $kodeMutasi AS nKode, NOW() AS tanggal, $noRM AS noRM, 'U', $kodeDokter AS kodeDokter, '$user', $episodeMutasi AS episode, $nomorAntrean AS noUrut, 'Umum', $kodeUmur AS kodeUmur, 1, 'T', '$jenisKelamin', '$NIPDokter' AS nipDokter) AS temp
              WHERE NOT EXISTS
                (SELECT * FROM _mutasi_pasien WHERE DATE(tTanggal) = DATE(NOW()) AND nNoRM = $noRM AND nPoli = $kodeDokter);
              ";
    $conn->query($query);
    if ($conn->execute()) { 
      // insert ke mutasi keuangan
      $query ="INSERT INTO _mutasi_keuangan
                  (nKodeMutasiPasien, nEpisode, szNamaTarif, nKodeVM, curJM, nKodeVS, curJS, nKodeVB, curBBA, nKodeTarif)
                SELECT * FROM 
                  (SELECT $kodeMutasi AS kodeMutasi, $episodeMutasi AS episode, szNama, nKodeVM, curJM, nKodeVS, curJS, nKodeVB, curBBA, nKode FROM _tarif WHERE nKode IN (80,79)) AS temp
                WHERE EXISTS
                  (SELECT * FROM _mutasi_pasien WHERE nKode = $kodeMutasi AND nEpisode = $episodeMutasi);
                ";
      // insert ke mutasi tindakan
      $query .="INSERT INTO _mutasi_pasien_tindakan
                  (nKode,nKodeMutasiPasien, nEpisode, nNoRM,szCaraBayar,nKodeBayar, nKategori, nKodePoli)
                SELECT * FROM
                  (SELECT '$KodeTindakan' AS nKode,'$kodeMutasi' AS kodeMutasi,'$episodeMutasi' AS episode, '$noRM' AS noRM, 'U' AS caraBayar, 'R' AS nKodeBayar, 1 AS kategori,$kodeDokter AS kodeDokter) AS temp
                WHERE NOT EXISTS
                  (SELECT * FROM _mutasi_pasien_tindakan WHERE nKodeMutasiPasien = $kodeMutasi AND nKategori = 1 AND nKodePoli = $kodeDokter);";
      $conn->query($query);
      $conn->execute();

      // update status antrean
      $queryAntrean= "UPDATE eksekutif_antrean
                      SET nStatus = 1, tSelesai = NOW(), nNIKUser = '$user', tTimestamp = NOW()
                      WHERE nKode = $kodeAntrean;
                    ";
      $connAntri->query($queryAntrean);
      $connAntri->execute();

      $_SESSION['simrs']['alert']['code'] = 1;
      $_SESSION['simrs']['alert']['message'] = "Berhasil mendaftarkan pasien";
    } else {
      $_SESSION['simrs']['alert']['code'] = 0;
      $_SESSION['simrs']['alert']['message'] = "Gagal mendaftarkan pasien, Silahkan coba Lagi";
    }
    header('Location: antrian-js.php');
    exit;
  }

?>