<?php 
  include_once("../config.php");
  include_once("../koneksi/koneksi169.php");

  if (@$_GET['panggilAntrean']) {
    $kode = $_GET['kode'];
    $user = $_SESSION['simrs']['pegawai']['kode'];
    $query = "UPDATE eksekutif_antrean
              SET tPanggil = NOW(), tSoundPlay = NULL, tTimeStamp = NOW(), nNIKUser = '$user'
              WHERE nKode = $kode;
				    ";
    $connAntri->query($query);
    $connAntri->execute();
  }

  // ==========================================================
  // ==========================================================

  if (@$_GET['pendingAntrean']) {
    $kode = $_GET['kode'];
    $user = $_SESSION['simrs']['pegawai']['kode'];

    $query = "UPDATE eksekutif_antrean
              SET nStatus = 2, tTimeStamp = NOW()
              WHERE nKode = $kode;
				    ";
    $connAntri->query($query);
    $connAntri->execute();
  }

  // ==========================================================
  // ==========================================================

  if (@$_GET['batalAntrean']) {
    $kode = $_GET['kode'];
    $user = $_SESSION['simrs']['pegawai']['kode'];

    $query = "UPDATE eksekutif_antrean
              SET nStatus = 0, tSelesai = NOW(), tTimeStamp = NOW(), nNIKUser = '$user'
              WHERE nKode = $kode;
				    ";
    $connAntri->query($query);
    $connAntri->execute();
  }
?>
