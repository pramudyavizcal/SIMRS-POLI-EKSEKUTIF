<?php 
  include_once("../config.php");
  include_once("../koneksi/koneksi99.php");
  if ( !session_id() ) {
    session_start();
  }
  $response = array();
  $response['code'] = "";

  // ===========================================================================================
  // INPUT DIAGNOSA
  // ===========================================================================================

  if (@$_GET['tambahDiagnosa']) {
    $kodeMutasiPasien = $_POST['kodeMutasiPasien'];
    $dokterDiagnosa = $_POST['dokterDiagnosa'];
    $perawatDiagnosa = $_POST['perawatDiagnosa'];
    $kategoriDiagnosa = $_POST['kategoriDiagnosa'];
    $kodeDiagnosa = $_POST['kodeDiagnosa'];
    $query = "";

    if($kategoriDiagnosa == "U") {	
      $query .= "UPDATE _mutasi_pasien 
                  SET szKodeDiagUtama = '$kodeDiagnosa', nKodeDokter = '$dokterDiagnosa', nNIKOlehPerawat = '$perawatDiagnosa' 
                  WHERE nKode = $kodeMutasiPasien;
                ";
    }
    $query .= "INSERT INTO _mutasi_pasien_diagnosa(nKodeMutasiPasien, nKodePoli, nEpisode, nNoRM, nKategoriDiagnosa, nKodeDokter, nKodeDiagnosa, tTimeStamp, nNIKOLeh)
                SELECT nKode, nPoli, nEpisode, nNoRM, '$kategoriDiagnosa', '$dokterDiagnosa', '$kodeDiagnosa', NOW(), '$perawatDiagnosa' 
                FROM _mutasi_pasien WHERE nKode = $kodeMutasiPasien;
              ";
    $conn->query($query);
    if ($conn->execute()) {
      $response['code'] = 1;
      $response['pesan'] = "Berhasil menambahkan diagnosa";
    } else {
      $response['code'] = 0;
      $response['pesan'] = "Gagal menambahkan diagnosa";
    }
    echo json_encode($response);
  }

  if (@$_GET['hapusDiagnosa']) {
    $kodeDiagnosa = $_POST['kodeDiagnosa'];
    $kodeMutasiPasien = $_POST['kodeMutasiPasien'];

    $query = "DELETE FROM _mutasi_pasien_diagnosa
              WHERE nKode = $kodeDiagnosa AND nKodeMutasiPasien = $kodeMutasiPasien;
            ";
    $conn->query($query);
    if ($conn->execute()) {
      $response['code'] = 1;
      $response['pesan'] = "Berhasil menghapus diagnosa";
    } else {
      $response['code'] = 1;
      $response['pesan'] = "Gagal menghapus diagnosa";
    }
    echo json_encode($response);

  }

  // ===========================================================================================
  // INPUT TINDAKAN
  // ===========================================================================================
  if (@$_GET['tambahTindakan']) {
    $kodeMutasiPasien = $_POST['kodeMutasiPasien'];
    $dokterTindakan = $_POST['dokterTindakan'];
    $perawatTindakan =  implode(',', $_POST['perawatTindakan']);
    $kodeTindakan = $_POST['kodeTindakan'];
    $jumlahTindakan = $_POST['jumlahTindakan'];
    $user = $_SESSION["simrs"]["pegawai"]["kode"];

    // cek mutasi tindakan
    $query = "SELECT nKode 
              FROM _mutasi_pasien_tindakan 
              WHERE nKodeMutasiPasien = $kodeMutasiPasien AND nKategori IS NULL LIMIT 1;
            ";
    $conn->query($query);
    $hasil = $conn->result();
    if (!isset($hasil['nKode'])) {
      $query = "SELECT IFNULL(MAX(nKode),0)+1 AS kodeTindakan FROM _mutasi_pasien_tindakan;";
      $conn->query($query);
      $hasil = $conn->result();
      $kodeMutasiTindakan = $hasil['kodeTindakan'];

      $query = "INSERT INTO _mutasi_pasien_tindakan(nKode,nKodeMutasiPasien, nEpisode, nNoRM, szCaraBayar, nKodeBayar, nKodePoli)
                SELECT * FROM (SELECT $kodeMutasiTindakan,nKode,nEpisode,nNoRM,szCaraBayar,'R',nPoli FROM _mutasi_pasien WHERE nKode = $kodeMutasiPasien ) AS tmp
                WHERE NOT EXISTS (SELECT * FROM _mutasi_pasien_tindakan WHERE nKodeMutasiPasien = $kodeMutasiPasien AND nKategori IS NULL);
              ";
      $conn->query($query);
      if (!$conn->execute()) {
        $response['code'] = 0;
        $response['pesan'] = "Gagal input tindakan";
        echo json_encode($response);
        die;
      }
    } else {
      $kodeMutasiTindakan = $hasil['nKode'];
    }

    // input tindakan detil
    $query = "INSERT INTO _mutasi_pasien_tindakan_detil(tTanggal, nKodeMutasiPasienTindakan, nKodeMutasiPasien, nEpisode, nKodePoli, nNoRM, nKodeBayar, nKodeTindakan, nKodeTarifTindakan, szNamaTarifTindakan, nQtt, nKodeVM, curJM, nKodeVS, curJS, nKodeVB, curBBA, nKodeDokter, nKodePerawat, nNIKOleh)
              SELECT * FROM (
                SELECT NOW(), $kodeMutasiTindakan AS kodeMutasiTindakan, m.nKode, m.nEpisode, m.nPoli, m.nNoRM, 'R', K.nKodeTindakan, $kodeTindakan, K.szTindakan, $jumlahTindakan, K.nKodeVM, K.curJM, K.nKodeVS, K.curJS, K.nKodeVB, K.curBBA, '$dokterTindakan', '$perawatTindakan', '$user' 
                FROM _mutasi_pasien m 
                LEFT OUTER JOIN (
                  SELECT $kodeMutasiPasien AS nKodeMutasiPasien, a.nKodeTindakan, a.nKodeVM, a.curJM, a.nKodeVS, a.curJS, a.nKodeVB, a.curBBA, b.szTindakan 
                  FROM _tarif_tindakan a 
                  LEFT OUTER JOIN _tindakan_master b ON b.nKodeTindakan = a.nKodeTindakan 
                  WHERE a.nKodeID = '$kodeTindakan'
                ) K ON K.nKodeMutasiPasien = m.nKode
                WHERE m.nKode = '$kodeMutasiPasien'
              ) AS tmp;
            ";
    $conn->query($query);
    if ($conn->execute()) {
      $response['code'] = 1;
      $response['pesan'] = "Berhasil input tindakan";
    } else {
      $response['code'] = 0;
      $response['pesan'] = "Gagal input tindakan";
    }
    echo json_encode($response);
  }

  if (@$_GET['hapusTindakan']) {
    $kodeTindakan = $_POST['kodeTindakan'];
    $kodeMutasiPasien = $_POST['kodeMutasiPasien'];
    // input tindakan detil
    $query = "DELETE FROM _mutasi_pasien_tindakan_detil
              WHERE nKode = $kodeTindakan AND nKodeMutasiPasien = $kodeMutasiPasien
            ";
    $conn->query($query);
    if ($conn->execute()) {
      $response['code'] = 1;
      $response['pesan'] = "Berhasil hapus tindakan";
    } else {
      $response['code'] = 0;
      $response['pesan'] = "Gagal hapus tindakan";
    }
    echo json_encode($response);
  }





  // ===========================================================================================
  // INPUT PENUNJANG
  // ===========================================================================================
  if (@$_GET['orderPenunjang']) {
    $user = $_SESSION['simrs']['pegawai']['kode'];
    $nKodeMutasiPasien = $_POST['kodeMutasiPasien'];
    $nKodePoli = $_POST['unitTujuan'];
    $nKodeDokter = $_POST['dokterPengirim'];
    $nKodeICD = $_POST['diagnosaPenunjang'];
    $nKodeTarif = $_POST['tarifPenunjang'];
    $nCito = $_POST['citoPenunjang'];

    $query = "SELECT (SELECT IFNULL(MAX(nKode)+1,1) FROM _mutasi_pasien) AS nKodeMP, 
              (SELECT IFNULL(MAX(nNoAntrianPoli)+1,1) FROM _mutasi_pasien WHERE DATE(tTanggal) = DATE(NOW()) AND nPoli = $nKodePoli) AS noUrut, 
              b.nKode, b.szNama, b.szInisial 
              FROM _mutasi_pasien a 
              LEFT OUTER JOIN _poliklinik b ON b.nKode = a.nPoli 
              WHERE a.nKode = $nKodeMutasiPasien;
            ";
    $conn->query($query);
    $hasil = $conn->result();
    $kodeMutasi = $hasil['nKodeMP'];
    $noUrut = $hasil['noUrut'];
    $kodeUnitPengirim  = $hasil['nKode'];
    $namaUnitPengirim  = $hasil['szNama'];
    $inisialUnitPengirim = $hasil['szInisial'];
    
    $query = "INSERT INTO _mutasi_pasien(nKode, tTanggal, nNoRM, szCaraBayar, nPoli, nNIKOlehLoket,nEpisode, nNoAntrianPoli, szAsuransi, szKodeFKTPRujukan, tTglRujukan, szKodeDiagAwal, nKodeUmur,szJK)
              (SELECT $kodeMutasi,NOW(),nNoRM,szCaraBayar,' $nKodePoli','$user',nEpisode,'$noUrut',szAsuransi,'$namaUnitPengirim',NOW(),'$nKodeICD',nKodeUmur,szJK FROM _mutasi_pasien WHERE nKode='$nKodeMutasiPasien');
            ";
    if ($nKodePoli == 49) {
      $query = $query . " INSERT INTO _mutasi_pasien_penunjang(nKodePoli, nKodeMutasiPasien, szJK, szJenisPasien, szPasien, szDokterPengirim, nKodeUnitPengirim,szUnitPengirim, nKodeKelasTarif, nTitip, nNIK)
              SELECT * FROM(SELECT $nKodePoli AS kodePoli,$kodeMutasi AS KodeMutasi, szJK, 'RS', '$inisialUnitPengirim', '$namaUnitPengirim' AS dokter ,nPoli,'$namaUnitPengirim' AS unit,'$nKodeTarif','$nCito','$user' FROM _mutasi_pasien WHERE nKode=$nKodeMutasiPasien)AS tmp
              WHERE NOT EXISTS (SELECT * FROM _mutasi_pasien_penunjang WHERE nKodeMutasiPasien = $kodeMutasi AND nKodePoli = $nKodePoli);
            ";
    } else {
      $query = $query . " INSERT INTO _mutasi_pasien_penunjang(nKodePoli, nKodeMutasiPasien, szJK, szJenisPasien, szPasien, tTanggalPelaksanaan,jPelaksanaan, szDokterPengirim, nKodeUnitPengirim,szUnitPengirim, nKodeKelasTarif, nNIK)
              SELECT * FROM(SELECT $nKodePoli AS kodePoli,$kodeMutasi AS KodeMutasi, szJK, 'RS', '$inisialUnitPengirim',DATE(NOW()),TIME(NOW()),'$namaUnitPengirim' AS dokter,nPoli,'$namaUnitPengirim' AS unit,'$nKodeTarif','$user' FROM _mutasi_pasien WHERE nKode='$nKodeMutasiPasien')AS tmp
              WHERE NOT EXISTS (SELECT * FROM _mutasi_pasien_penunjang WHERE nKodeMutasiPasien = $kodeMutasi AND nKodePoli = $nKodePoli);
            ";
    }

    $conn->query($query);
    if ($conn->execute()) {
      $response['code'] = 1;
      $response['pesan'] = "Berhasil order penunjang";
    } else {
      $response['code'] = 0;
      $response['pesan'] = "Gagal order penunjang";
    }
    echo json_encode($response);
  }

  // ===========================================================================================
  //  Biaya
  // ===========================================================================================
  if(@$_GET['verifBiaya']) {
    $user_nama = $_SESSION["simrs"]["pegawai"]["nama"];
    $user_nip = $_SESSION["simrs"]["pegawai"]["nip"];
    $user_kodeDokter = $_SESSION['simrs']['pegawai']['loket']; //poli
    $kodeMutasiPasien = $_POST['kodeMutasiPasien'];
    $nKodeBayar = $_POST['nKodeBayar'];
    
    $querySTR = "SELECT (SELECT IFNULL(max(nomor),0)+1 as n from (SELECT substr(nNoRegPoli,4,4) as thn,substr(nNoRegPoli,11,4) as nomor from _mutasi_pasien_tindakan_detil 
                where nKodePoli='$user_kodeDokter' and nKodeBayar='$nKodeBayar' and nNoRegPoli is not null)
                k where thn=DATE_FORMAT(now(),'%y%m')) as nomor,
                (SELECT szInisial from _poliklinik where nKode='$user_kodeDokter') as kodeK, 
                (SELECT DATE_FORMAT(now(),'%y%m')) as th,
                (SELECT nKode from _mutasi_pasien_tindakan where nKodeMutasiPasien='$kodeMutasiPasien' and nKategori IS NULL and szNoKwitansi IS NULL AND nNoKui is Null) as nKodeMT,
                (SELECT if(szCaraBayar='U',NULL,szSJP) from _mutasi_pasien where nKode='$kodeMutasiPasien') as szSJP, 
                (SELECT SUM(curBiaya) from (SELECT a.nKode,a.nKodeBayar,a.szNoKwitansi,b.nNoRegPoli,
                IF(b.nKodeTindakan IN (2057,2063),(DATEDIFF(now(),b.tTanggal)+1),b.nQtt) * (IFNULL(b.curJS,0)+IFNULL(b.curJM,0)+IFNULL(b.curBBA,0)+IFNULL(b.curCito,0)) as curBiaya,
                if(a.nKodeBayar='T' and a.szNoKwitansi IS NOT NULL,1,0) as Idm from _mutasi_pasien_tindakan a 
                left outer join	 _mutasi_pasien_tindakan_detil b ON b.nKodeMutasiPasienTindakan=a.nKode
                where a.nKodeMutasiPasien='$kodeMutasiPasien' and a.nKategori IS NULL)K where Idm = 0) as curBiaya;";   
    // echo "$querySTR";
      $conn->query($querySTR);
      $rs = $conn->result();
      $noKui = $rs['nomor'];
      $szKui = $rs['kodeK'];
      $szTh  = $rs['th'];
  
      $noKui = str_pad($noKui, 4, '0', STR_PAD_LEFT);

      $noReg = $szKui . date('dmy') . $nKodeBayar  . $noKui;				

      $query = "UPDATE _mutasi_pasien_tindakan_detil 
                SET nNoRegPoli='$noReg', nKodeBayar='$nKodeBayar' 
                WHERE nKodePoli = '$user_kodeDokter' and nKodeMutasiPasien = '$kodeMutasiPasien' and nNoRegPoli is NULL;
              ";

      $conn->query($query);
      if ($conn->execute()) {
        $response['code'] = 1;
        $response['pesan'] = "Verifikasi Biaya Berhasil";
      } else {
        $response['code'] = 0;
        $response['pesan'] = "Gagal Verifikasi Biaya";
      }
      echo json_encode($response);
    }



  // ===========================================================================================
  // tindak lanjut
  // ===========================================================================================
  if(@$_GET['tindakLanjut']){
    $user_nip = $_SESSION["simrs"]["pegawai"]["nip"];
    $kodeMutasiPasien = $_POST['kodeMutasiPasien'];
    $kodeEpisodePasien = $_POST['kodeEpisodePasien'];
    $radioLanjut = $_POST['radioLanjut'];

    // cek diagnosa
    $query = "SELECT nKodeDiagnosa FROM _mutasi_pasien_diagnosa WHERE nKategoriDiagnosa = 'U' AND nKodeMutasiPasien = '$kodeMutasiPasien' LIMIT 1;";
    $conn->query($query);
    $hasil = $conn->result();
    if (!isset($hasil['nKodeDiagnosa'])) {
      $response['code'] = 0;
      $response['pesan'] = "Diagnosa belum diisi";
      echo json_encode($response);
      die;
    }

    // cek tagihan
    $query = "SELECT COUNT(nKode) AS jumlah,COUNT(CASE WHEN nNoRegPoli IS NOT NULL THEN 1 END) AS terbayar,COUNT(CASE WHEN nNoRegPoli IS NULL THEN 1 END) AS belum_terbayar
              FROM _mutasi_pasien_tindakan_detil
              WHERE nEpisode = '$kodeEpisodePasien';
            ";
    $conn->query($query);
    $hasil = $conn->result();
    if (isset($hasil['jumlah'])) {
      if($hasil['jumlah'] > $hasil['terbayar']) {
        $response['code'] = 0;
        $response['pesan'] = "Ada tagihan yang belum dibayar";
        echo json_encode($response);
        die;
      }
    }

    //===============================PULANG=============================================
    if($radioLanjut == "Pulang"){
      // update tanggal krs
      $query = "UPDATE _mutasi_pasien 
                SET szTindakLanjut = '$radioLanjut',tTglKRS=now()
                WHERE nKode='$kodeMutasiPasien';
              ";
      $conn->query($query);
      if ($conn->execute()) {
        $_SESSION['simrs']['alert']['code'] = 1;
        $_SESSION['simrs']['alert']['message'] = "Berhasil Mempulangkan Pasien";
        $response['code'] = 1;
      } else {
        $response['code'] = 0;
        $response['pesan'] = "Gagal Mempulangkan Pasien";
      }

      //===============================Rawat Inap=============================================
    } else {
      $query = "SELECT ifnull(max(nKode),0) + 1 as kode,
                (select szNama from _poliklinik where nKode = (SELECT nPoli from _mutasi_pasien where nKode = '$kodeMutasiPasien')) as szPoliAwal from _mutasi_pasien;";
      $conn->query($query);
      $rs = $conn->result();
      $nKodeMutasiK = $rs['kode'];
      $szPoliAwal = $rs['szPoliAwal'];

      $query = "UPDATE _mutasi_pasien SET szTindakLanjut = '$radioLanjut', tTglKRS=now() where nKode='$kodeMutasiPasien';";

      $query = $query . " INSERT INTO _mutasi_pasien (nKode, tTanggal, nNoRM, szCaraBayar, nNIKOlehLoket, nEpisode, szAsuransi, szJenisPeserta, nKelas, szKodeFKTPRujukan, tTglRujukan, szKodeDiagAwal, nKodeUmur,nBerkas,szJK)
      (SELECT '$nKodeMutasiK',now(),nNoRM,szCaraBayar,'$user_nip', nEpisode,szAsuransi,szJenisPeserta, nKelas,'$szPoliAwal', now(), szKodeDiagUtama, nKodeUmur,nBerkas,szJK from _mutasi_pasien where nKode = '$kodeMutasiPasien');";
      
      $conn->query($query);
      if ($conn->execute()) {
        $_SESSION['simrs']['alert']['code'] = 1;
        $_SESSION['simrs']['alert']['message'] = "Berhasil Mendaftarkan Rawat Inap Pasien";
        $response['code'] = 1;
      } else {
        $response['code'] = 0;
        $response['pesan'] = "Gagal  Mendaftarkan Rawat Inap Pasien";
      }
    }
    echo json_encode($response);
  }

?>
