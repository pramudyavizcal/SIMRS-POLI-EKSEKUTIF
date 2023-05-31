<?php 
  include("../database/conString.php"); 
  include("../database/libsServer/adodbcon.php");

  if ( !session_id() ) {
    session_start();
  }

  if (@$_GET['tambahDiagnosa']) {
    $NIPPerawat = $_GET['NIPPerawat'];
    $kodeMutasiPasien = $_GET['kodeMutasiPasien'];
    $NIPDokter = $_GET['NIPDokter'];
    $kategoriDiagnosa = $_GET['kategoriDiagnosa'];
    $kodeDiagnosa = $_GET['kodeDiagnosa'];
    $query = "";

    if($kategoriDiagnosa == "U") {	
      $query .= "UPDATE _mutasi_pasien 
                  SET szKodeDiagUtama = '$kodeDiagnosa', nKodeDokter = '$NIPDokter', nNIKOlehPerawat = '$NIPPerawat' 
                  WHERE nKode = $kodeMutasiPasien;
                ";
    }
    $query .= "INSERT INTO _mutasi_pasien_diagnosa
                (nKodeMutasiPasien, nKodePoli, nEpisode, nNoRM, nKategoriDiagnosa, nKodeDokter, nKodeDiagnosa, tTimeStamp, nNIKOLeh)
                (
                  SELECT nKode, nPoli, nEpisode, nNoRM, '$kategoriDiagnosa', '$NIPDokter', '$kodeDiagnosa', NOW(), '$NIPPerawat' 
                  FROM _mutasi_pasien WHERE nKode = $kodeMutasiPasien
                );
              ";
    mysqli_report(MYSQLI_REPORT_OFF);
    if ($conni->multi_query($query) === TRUE) {
      $_SESSION['simrs']['alert']['code'] = 1;
      $_SESSION['simrs']['alert']['message'] = "Berhasil menambahkan Diagnosa";
  
    } else {
      $_SESSION['simrs']['alert']['code'] = 0;
      $_SESSION['simrs']['alert']['message'] = "Gagal menambahkan Diagnosa, Silahkan coba Lagi";
    }
  }

  if (@$_GET['hapusDiagnosa']) {
    $kodeDiagnosa = $_GET['kodeDiagnosa'];
    $kodeMutasiPasien = $_GET['kodeMutasiPasien'];
    $query = "";

    $query .= "DELETE FROM _mutasi_pasien_diagnosa
                WHERE nKode = $kodeDiagnosa;
              ";
    mysqli_report(MYSQLI_REPORT_OFF);
    if ($conni->multi_query($query) === TRUE) {
      $_SESSION['simrs']['alert']['code'] = 1;
      $_SESSION['simrs']['alert']['message'] = "Berhasil menghapus Diagnosa";
  
    } else {
      $_SESSION['simrs']['alert']['code'] = 0;
      $_SESSION['simrs']['alert']['message'] = "Gagal menghapus Diagnosa, Silahkan coba Lagi";
    }
    var_dump($_SESSION['simrs']['alert']);
  }


  // ===========================================================================================
  // INPUT PENUNJANG
  // ===========================================================================================

  if (@$_GET['orderPenunjang']) {
    $n = $_GET['n'];
    $nKodeMutasiPasien = $_GET['nKodeMutasi'];
    $nKodePoli = $_GET['nKodePoli'];
    $nKodeDokter = $_GET['nKodeDokter'];
    $nKodeTarif = $_GET['nKodeTarif'];
    $nKodeICD = $_GET['nKodeDx'];
    $nCito = $_GET['nCito'];

    $queryOrder = "SELECT (SELECT MAX(IFNULL(nKode,0))+1 FROM _mutasi_pasien) AS nKodeMP,
    (SELECT IF(b.nKodeKunjungan=2,b.szNama,CONCAT(b.szNama,' / ',IF(c.nTitip='Y',d.nama_kelas,dd.nama_kelas),' / Bed ',c.nNoBed)) FROM _mutasi_pasien a LEFT OUTER JOIN _poliklinik b ON b.nKode=a.nPoli 
    LEFT OUTER JOIN _mutasi_pasien_penunjang c ON c.nKodeMutasiPasien=a.nKode
    LEFT OUTER JOIN _ruang_kelas d ON d.id=c.nKodeKelasTarif
    LEFT OUTER JOIN _ruang_kelas dd ON dd.id=c.nKelasRuang
    WHERE a.nKode='$nKodeMutasiPasien') AS szUnitPengirim,
    (SELECT MAX(IFNULL(nNoAntrianPoli,0)) + 1 FROM _mutasi_pasien 
    WHERE DATE(tTanggal)=DATE(NOW()) AND nPoli = '$nKodePoli') AS urutPoli,
    (SELECT b.szNama FROM _mutasi_pasien a LEFT OUTER JOIN _poliklinik b ON b.nKode=a.nPoli 
    WHERE a.nKode='$nKodeMutasiPasien') AS szPoliPengirim,
    (SELECT szNama FROM _pegawai WHERE nNIP='$nKodeDokter') AS szDokterPengirim,
    (SELECT IF(b.nKodeKunjungan = 2 AND a.nPoli IN (10,20),'IGD',IF(b.nKodeKunjungan = 2 AND a.nPoli NOT IN (10,20),'RJ','RI'))
    FROM _mutasi_pasien a LEFT OUTER JOIN _poliklinik b ON b.nKode=a.nPoli 
    WHERE a.nKode='$nKodeMutasiPasien') AS szPasien;";

      $hasil = $conn->Execute($queryOrder) or die($conn->ErrorMsg());
      $a = 0;
      $hasil->MoveFirst();
      while (!$hasil->EOF) {
        $kodeMutasi = $hasil->fields[0];
        $szUnitPengirim  = $hasil->fields[1];
        $antriPoliMutasi = $hasil->fields[2];
        $szPoliPengirim = $hasil->fields[3];
        $szDokterPengirim = $hasil->fields[4];
        $szPasien = $hasil->fields[5];
        $hasil->MoveNext();
      }

      $query = "INSERT INTO _mutasi_pasien(nKode, tTanggal, nNoRM, szCaraBayar, nPoli, nNIKOlehLoket, 
      nEpisode, nNoAntrianPoli, szAsuransi, szKodeFKTPRujukan, tTglRujukan, szKodeDiagAwal, nKodeUmur,szJK)
      (SELECT '$kodeMutasi',NOW(),nNoRM,szCaraBayar,' $nKodePoli','$n',nEpisode,
      '$antriPoliMutasi',szAsuransi,'$szPoliPengirim',NOW(),'$nKodeICD',nKodeUmur,szJK
      FROM _mutasi_pasien WHERE nKode='$nKodeMutasiPasien');";

      if ($nKodePoli == 49) {
        $query = $query . " INSERT INTO _mutasi_pasien_penunjang(nKodePoli, nKodeMutasiPasien, szJK, 
        szJenisPasien, szPasien, szDokterPengirim, nKodeUnitPengirim,szUnitPengirim, nKodeKelasTarif, 
        nTitip, nNIK)(SELECT '$nKodePoli','$kodeMutasi', szJK, 'RS', '$szPasien',
        '$szDokterPengirim',nPoli,'$szUnitPengirim','$nKodeTarif','$nCito','$n' 
        from _mutasi_pasien where nKode='$nKodeMutasiPasien');";
      } else {
        $query = $query . " INSERT INTO _mutasi_pasien_penunjang(nKodePoli, nKodeMutasiPasien, szJK, 
        szJenisPasien, szPasien, tTanggalPelaksanaan,jPelaksanaan, szDokterPengirim, nKodeUnitPengirim,szUnitPengirim, nKodeKelasTarif, 
        nNIK)(SELECT '$nKodePoli','$kodeMutasi', szJK, 'RS', '$szPasien',DATE(NOW()),TIME(NOW()),
        '$szDokterPengirim',nPoli,'$szUnitPengirim','$nKodeTarif','$n' 
        FROM _mutasi_pasien WHERE nKode='$nKodeMutasiPasien');";
      }

      mysqli_report(MYSQLI_REPORT_OFF);
      if ($conni->multi_query($query) === TRUE) {
        $_SESSION['simrs']['alert']['code'] = 1;
        $_SESSION['simrs']['alert']['message'] = "Berhasil order Penunjang";
      } else {
        $_SESSION['simrs']['alert']['code'] = 0;
        $_SESSION['simrs']['alert']['message'] = "Gagal order Penunjang, Silahkan coba Lagi";
      }

    }
    
    $conni->close();

  ?>
