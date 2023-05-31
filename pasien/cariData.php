<?php 
  include("../database/libsServer/adodbcon.php"); 
  date_default_timezone_set('Asia/Jakarta');


  // ===========================================================================================
  // RIWAYAT
  // ===========================================================================================
  if (@$_GET['cariBiodataPasien']) {
    $noRM = $_GET['noRM'];
		
    $result = array();
    $result["noRM"] = "";
    $queryPasien = "SELECT CONCAT(p.szNama,', ',szTitle) AS szNama,szTempatLahir,tTglLahir,IF(szJenisKelamin='L','Laki-laki','Perempuan') AS szJenisKelamin,
                    CONCAT(IF(szAlamat = '' OR szAlamat IS NULL, '', CONCAT(szAlamat, ', ')), 'RT. ', szRT, ', RW. ', szRW, ', Ds. ', szDesa, ', Kec. ', szKecamatan, ', Kab. ', szkota, ', Prop. ', szPropinsi) AS Alamat,
                    b.szNama AS szRuang,m.szCaraBayar, m.szAsuransi,m.nKode,m.nEpisode,m.nPoli,m.szSJP
                    FROM _pasien p
                    LEFT OUTER JOIN (SELECT a.szSJP,a.nNoRM,a.nPoli,a.tTanggal,a.tTglKRS,a.tSelesai,a.szTindakLanjut,a.nEpisode,a.nKode,a.szCaraBayar,
                    IF(a.szCaraBayar='U','Umum',a.szAsuransi) AS szAsuransi
                    FROM _mutasi_pasien a LEFT OUTER JOIN _poliklinik b ON b.nKode=a.nPoli
                    WHERE nNoRM='$noRM' AND tTglKRS IS NULL) m ON m.nNoRM=p.nNoRM
                    LEFT OUTER JOIN _poliklinik b ON b.nKode=m.nPoli
                    WHERE p.nNoRM = '$noRM';
                  ";
    $pasien = $conn->Execute($queryPasien) or die($conn->ErrorMsg());
    $pasien->MoveFirst();
    while (!$pasien->EOF) {
      // hitung umur
      $bday = new DateTime($pasien->fields[2]);
      $today = new Datetime(date("Y-m-d"));
      $diff = $today->diff($bday);
      // susun pasien
      $result["noRM"] = $noRM;
      $result["nama"] = $pasien->fields[0];
      $result["tempatLahir"] = $pasien->fields[1];
      $result["tglLahir"] = date("d-m-Y", strtotime($pasien->fields[2]));
      $result["jenisKelamin"] = $pasien->fields[3];
      $result["umur"] = "$diff->y Tahun $diff->m Bulan $diff->d Hari";
      $result["alamatLengkap"] = $pasien->fields[4];
      $result["szRuang"] = $pasien->fields[5];
      $result["szCaraBayar"] = $pasien->fields[6];
      $result["szAsuransi"] = $pasien->fields[7];
      $result["nKode"] = $pasien->fields[8];
      $result["nEpisode"] = $pasien->fields[9];
      $result["nPoli"] = $pasien->fields[10];

      $pasien->MoveNext();
    }
		
    echo json_encode($result);
  }

  if (@$_GET['cariRiwayatPasien']) {
    $noRM = $_GET['noRM'];

    $result = array();
    $queryRiwayat = " SELECT a.tTanggal, IF(a.szCaraBayar = 'U','Umum', (IF(a.szCaraBayar='J', a.szAsuransi, CONCAT('Rencana ', a.szAsuransi)))) AS szBayar, a.szSJP, a.nStatusPoli, b.szNama, IFNULL(f.nama, ff.szNama) AS szDokter, a.szKodeDiagUtama, a.tTglKRS, a.szTindakLanjut, IFNULL(g.nama,gg.szNama) AS szPetugas
                      FROM _mutasi_pasien a 
                      LEFT OUTER JOIN _poliklinik b ON b.nKode = a.nPoli
                      LEFT OUTER JOIN _pegawai f ON f.NIP = a.nKodeDokter
                      LEFT OUTER JOIN _dokter_perawat ff ON ff.nKode = a.nKodeDokter
                      LEFT OUTER JOIN _pegawai g ON g.NIP = a.nNIKOlehPerawat
                      LEFT OUTER JOIN _user gg ON gg.nNIK = a.nNIKOlehPerawat
                      WHERE a.nNoRM = $noRM AND b.nKodeKunjungan IN (1, 2) AND a.nStatusPoli IS NULL
                      UNION
                      SELECT a.tTanggal, 'Umum' AS szBayar, a.szSJP, a.nStatusPoli, b.nama AS szNama, b.nama AS szDokter, a.szKodeDiagUtama, a.tTglKRS, a.szTindakLanjut, c.nama AS szPetugas
                      FROM _mutasi_pasien a 
                      LEFT OUTER JOIN _pegawai b ON a.nKodeDokter = b.NIP
                      LEFT OUTER JOIN _pegawai c ON a.nNIKOlehPerawat = c.NIP
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

?>
