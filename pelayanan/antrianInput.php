<?php 
  include("../database/conString.php"); 

  if (@$_GET['panggilPasien']) {
    $kodeMutasiPasien = $_GET['kodeMutasiPasien'];

    $query = "UPDATE _mutasi_pasien
              SET tStart = NOW()
              WHERE nKode = $kodeMutasiPasien;
				    ";
    if ($conni->multi_query($query) === TRUE) {
      echo "1|Berhasil Input Data";
    } else {
      echo "0|Gagal Input Data";
    }
  }

  // ==========================================================
  // ==========================================================

  if (@$_GET['selesaiPasien']) {
    $kodeMutasiPasien = $_GET['kodeMutasiPasien'];

    $query = "UPDATE _mutasi_pasien
              SET tEnd = NOW()
              WHERE nKode = $kodeMutasiPasien;
				    ";
    if ($conni->multi_query($query) === TRUE) {
      echo "1|Berhasil Input Data";
    } else {
      echo "0|Gagal Input Data";
    }
  }

?>
