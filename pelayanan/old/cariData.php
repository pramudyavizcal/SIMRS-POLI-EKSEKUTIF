<?php 
  include("../database/libsServer/adodbcon.php"); 
  date_default_timezone_set('Asia/Jakarta');

  // ===========================================================================================
  // DPJP & diagnosa
  // ===========================================================================================
  if (@$_GET['cariICD']) {
    $kataKunci = $_GET['search']; 
 
    // Fetch matched data from the database 
    $result = array();
    $queryICD = "SELECT id_icd, penyakit 
              FROM icd 
              WHERE nKodeDTD IS NOT NULL AND nKodeDTD > 0 AND id_icd LIKE '%$kataKunci%' OR penyakit LIKE '%$kataKunci%' 
              ORDER BY id_icd;
            ";
    $hasil = $conn->Execute($queryICD) or die($conn->ErrorMsg());
    $a = 0;
    $hasil->MoveFirst();
    while (!$hasil->EOF) {
      $result[$a]["id"] = $hasil->fields[0];
      $result[$a]["text"] = $hasil->fields[0]." : ".$hasil->fields[1];
      $a++;
      $hasil->MoveNext();
    }
    if (count($result) > 0) {
      echo json_encode($result);
    } else {
      echo "Diagnosa tidak ditemukan";
    }
  }

  if (@$_GET['cariDiagnosaPasien']) {
    $noRM = $_GET['noRM'];
    $episode = $_GET['eposide'];

    $result = array();
    $queryDiagnosa = "SELECT a.nKode, a.nKodeMutasiPasien, a.tTimeStamp, IF(a.nKategoriDiagnosa = 'U', 'Utama', 'Penyerta') AS kategori, b.szNama, CONCAT(a.nKodeDiagnosa, ' : ', c.penyakit) AS diagnosa, a.tTglVerif 
                      FROM _mutasi_pasien_diagnosa a
                      LEFT OUTER JOIN dokter b ON a.nKodeDokter = b.nNIP
                      LEFT OUTER JOIN icd c ON a.nKodeDiagnosa = c.id_icd
                      WHERE a.nNoRM = $noRM AND nEpisode = $episode; 
                    ";
    $hasil = $conn->Execute($queryDiagnosa) or die($conn->ErrorMsg());
    $a = 0;
    $hasil->MoveFirst();
    while (!$hasil->EOF) {
      $result[$a]["no"] = $a+1;
      $result[$a]["kodeDiagnosa"] = $hasil->fields[0];
      $result[$a]["kodeMutasiPasien"] = $hasil->fields[1];
      $result[$a]["tanggalDiagnosa"] = $hasil->fields[2];
      $result[$a]["kategoriDiagnosa"] = $hasil->fields[3];
      $result[$a]["DPJP"] = $hasil->fields[4];
      $result[$a]["diagnosa"] = $hasil->fields[5];
      $result[$a]["tanggalVerif"] = ($hasil->fields[6] == "") ? "" : $hasil->fields[6];
      $a++;
      $hasil->MoveNext();
    }

    echo json_encode($result);
  }

// ===========================================================================================
  // PENUNJANG
  // ===========================================================================================

  if (@$_GET['cariPenunjangPasien']){
    $episode = $_GET['episode'];
    $result = array();
    $queryPenunjang = "SELECT if(a.nPoli=38,1,2) as Idx,a.nKode,a.tTanggal,b.szNama as szPenunjang,c.szNoKwitansi,
      SUM(d.nQtt*(IFNULL(d.curJM,0)+IFNULL(d.curJS,0)+IFNULL(d.curBBA,0)+IFNULL(d.curCito,0))) as curTarif, a.nPoli,c.nKode as nKodeMT,
      e.nKode,e.szFile,f.X1
      from _mutasi_pasien a 
      left outer JOIN _poliklinik b ON b.nKode=a.nPoli
      left outer JOIN _mutasi_pasien_tindakan c ON c.nKodeMutasiPasien=a.nKode
      left outer JOIN _mutasi_pasien_tindakan_detil d ON d.nKodeMutasiPasien=a.nKode
      left outer JOIN _mutasi_pasien_hasil_diagnostik e ON e.nKodeMutasiPasien=a.nKode
      left outer JOIN (SELECT a.nKodeMutasiPasien,a.nKodeMutasiPasienTindakan,count(b.szLinkUrl1) as X1 from _mutasi_pasien_tindakan_detil a 
      left outer join _ordertoris b ON b.nKodeMutasiOrder=a.nKode
      where a.nEpisode='$episode' and b.szLinkURL1 IS NOT NULL group by a.nKodeMutasiPasienTindakan) f ON f.nKodeMutasiPasienTindakan=c.nKode
      where a.nEpisode='$episode' and b.nKodeKunjungan IN (3) AND a.nStatusPoli IS NULL group by c.nKode order by a.tTanggal;";

    $hasil = $conn->Execute($queryPenunjang) or die($conn->ErrorMsg());
    $a = 0;
    $hasil->MoveFirst();
    while (!$hasil->EOF) {
      $result[$a]["no"] = $a + 1;
      $result[$a]["Idx"] = $hasil->fields[0];
      $result[$a]["nKodeMutasi"] = $hasil->fields[1];
      $result[$a]["tTanggal"] = $hasil->fields[2];
      $result[$a]["szPenunjang"] = $hasil->fields[3];
      $result[$a]["szKwitansi"] = $hasil->fields[4];
      $result[$a]["curPenunjang"] = $hasil->fields[5];
      $result[$a]["nKodePenunjang"] = $hasil->fields[6];
      $result[$a]["nKodeMT"] = $hasil->fields[7];
      $result[$a]["nKodeHasil"] = $hasil->fields[8];
      $result[$a]["szFile"] = $hasil->fields[9];
      $result[$a]["xRIS"] = $hasil->fields[10];
      $a++;
      $hasil->MoveNext();
  }

  echo json_encode($result);
  }

  // ===========================================================================================
  // RIWAYAT
  // ===========================================================================================
  if (@$_GET['cariRiwayatPasien']) {
    $noRM = $_GET['noRM'];

    $result = array();
    $queryRiwayat = "SELECT a.tTanggal, IF(a.szCaraBayar = 'U','Umum', (IF(a.szCaraBayar='J', a.szAsuransi, CONCAT('Rencana ', a.szAsuransi)))) AS szBayar, a.szSJP, a.nStatusPoli, b.szNama, IFNULL(f.szNama, ff.szNama) AS szDokter, a.szKodeDiagUtama, a.tTglKRS, a.szTindakLanjut, IFNULL(g.szNama,gg.szNama) AS szPetugas
                      FROM _mutasi_pasien a 
                      LEFT OUTER JOIN _poliklinik b ON b.nKode = a.nPoli
                      LEFT OUTER JOIN _pegawai f ON f.nNIP = a.nKodeDokter
                      LEFT OUTER JOIN _dokter_perawat ff ON ff.nKode = a.nKodeDokter
                      LEFT OUTER JOIN _pegawai g ON g.nNIP = a.nNIKOlehPerawat
                      LEFT OUTER JOIN _user gg ON gg.nNIK = a.nNIKOlehPerawat
                      WHERE a.nNoRM = $noRM AND b.nKodeKunjungan IN (1, 2) AND a.nStatusPoli IS NULL
                      union
                      select a.tTanggal, 'Umum' as szBayar, a.szSJP, a.nStatusPoli,  UPPER(b.szNama) AS szNama, UPPER(b.szNama) AS szDokter, a.szKodeDiagUtama, a.tTglKRS, a.szTindakLanjut, IFNULL(g.szNama, gg.szNama) AS szPetugas
                      FROM _mutasi_pasien a 
                      LEFT OUTER JOIN dokter b ON b.nKode = a.nPoli
                      LEFT OUTER JOIN _pegawai g ON g.nNIP = a.nNIKOlehPerawat
                      LEFT OUTER JOIN _user gg ON gg.nNIK = a.nNIKOlehPerawat
                      WHERE a.nNoRM = $noRM AND a.nStatusPoli = 1
                      ORDER BY tTanggal;
                    ";
    $hasil = $conn->Execute($queryRiwayat) or die($conn->ErrorMsg());
    $a = 0;
    $hasil->MoveFirst();
    while (!$hasil->EOF) {
      $result[$a]["no"] = $a+1;
      $result[$a]["tanggal"] = date("d-m-Y H:i:s", strtotime($hasil->fields[0]));
      $result[$a]["penjamin"] = $hasil->fields[1];
      $result[$a]["sjp"] = ($hasil->fields[2] == "") ? "" : $hasil->fields[2];
      $result[$a]["statusUnit"] = ($hasil->fields[3] == "") ? "" : $hasil->fields[3];
      $result[$a]["unit"] = $hasil->fields[4];
      $result[$a]["dokter"] = $hasil->fields[5];
      $result[$a]["diagnosa"] = ($hasil->fields[6] == "") ? "" : $hasil->fields[6];
      $result[$a]["tglKRS"] = ($hasil->fields[7] == "") ? "" : date("d-m-Y H:i:s", strtotime($hasil->fields[7]));
      $result[$a]["tindakLanjut"] = ($hasil->fields[8] == "") ? "" : $hasil->fields[8];
      $result[$a]["petugas"] = ($hasil->fields[9] == "") ? "" : $hasil->fields[9];
      $a++;
      $hasil->MoveNext();
    }

    echo json_encode($result);
  }

  // ===========================================================================================
  // DATA INDUK
  // ===========================================================================================
  if (@$_GET['cariDataIndukPasien']) {
    $noRM = $_GET['noRM'];
		
    $result = array();
    $result["noRM"] = "";
    $queryPasien = "SELECT a.nID, CONCAT(a.szNama,', ',a.szTitle) AS szNama, a.szTempatLahir, a.tTglLahir, IF(a.szJenisKelamin='L','Laki-laki','Perempuan') AS szJenisKelamin, 
                    CONCAT(IF(a.szAlamat = '' OR a.szAlamat IS NULL, '', CONCAT(a.szAlamat, ', ')), 'RT. ', a.szRT, ', RW. ', a.szRW, ', Ds. ', a.szDesa, ', Kec. ', a.szKecamatan, ', Kab. ', a.szkota, ', Prop. ', a.szPropinsi) AS Alamat,
                    a.szNegara, a.szTelp, a.szAgama, a.szGolDarah, a.szAsuransi, a.nNoAsuransi, a.nKelasAsuransi, a.szKepesertaan, a.szJenisPeserta, a.szFKTP, a.szKawin, CONCAT(a.szNamaKawin, ', ', a.szTitleKawin) AS namaWali, a.szWali, a.szTelpKawin,
                    b.szPendidikan, b.szPekerjaan, b.szEtnis, b.szBahasa, b.szJenisBahasa, b.szHambatanKom, b.szAlergi
                    FROM _pasien a
                    LEFT OUTER JOIN _pasien_data_tambahan b ON b.nNoRM = a.nNoRM
                    WHERE a.nNoRM = $noRM;
                  ";
    $pasien = $conn->Execute($queryPasien) or die($conn->ErrorMsg());
    $pasien->MoveFirst();
    while (!$pasien->EOF) {
      // hitung umur
      $bday = new DateTime($pasien->fields[3]);
      $today = new Datetime(date("Y-m-d"));
      $diff = $today->diff($bday);
      // susun pasien
      $result["noRM"] = $noRM;
      $result["NIK"] = $pasien->fields[0];
      $result["nama"] = $pasien->fields[1];
      $result["tempatLahir"] = $pasien->fields[2];
      $result["tglLahir"] = date("d-m-Y", strtotime($pasien->fields[3]));
      $result["umur"] = "$diff->y Tahun $diff->m Bulan $diff->d Hari";
      $result["jenisKelamin"] = $pasien->fields[4];
      $result["alamatLengkap"] = $pasien->fields[5];
      $result["wargaNegara"] = $pasien->fields[6]." / ".$pasien->fields[22];
      $result["noTelp"] = $pasien->fields[7];
      $result["agama"] = $pasien->fields[8];
      $result["golDarah"] = $pasien->fields[9];
      $noAsuransi = ($pasien->fields[11] == "") ? "" : " - No. ". $pasien->fields[11];
      $result["asuransi"] = $pasien->fields[10]. $noAsuransi;
      $result["kelasAsuransi"] = "Kelas ".$pasien->fields[12];
      $result["kepesertaanAsuransi"] = $pasien->fields[13]." / ".$pasien->fields[14];
      $result["FKTP"] = $pasien->fields[15];
      $result["statusPernikahan"] = "";
      if ($pasien->fields[16] == "B") {
        $result["statusPernikahan"] = "Belum Menikah";
      } elseif ($pasien->fields[16] == "K") {
        $result["statusPernikahan"] = "Menikah";
      } elseif ($pasien->fields[16] == "D") {
        $result["statusPernikahan"] = "Duda";
      } elseif ($pasien->fields[16] == "J") {
        $result["statusPernikahan"] = "Janda";
      }
      $result["namaWali"] = $pasien->fields[17];
      $result["hubunganWali"] = $pasien->fields[18];
      $result["noTelpWali"] = $pasien->fields[19];
      $result["pendidikan"] = $pasien->fields[20];
      $result["pekerjaan"] = $pasien->fields[21];
      $result["bahasa"] = $pasien->fields[23];
      $result["jenisBahasa"] = $pasien->fields[24];
      $result["hambatanKomunikasi"] = $pasien->fields[25];
      $result["alergi"] = ($pasien->fields[26] == null) ? "" : $pasien->fields[26];

      $pasien->MoveNext();
    }
		
    echo json_encode($result);
  }


?>
