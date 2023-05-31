<?php 
  include_once("../config.php");
  include_once("../koneksi/koneksi169.php");
  include_once("../koneksi/koneksi99.php");
  $result = array();


  // ===========================================================================================
  // antrian
  // ===========================================================================================
  if (@$_GET['cariAntrean']) {
    $kodeDokter = $_GET['kodeDokter'];
    
    $result['kodeDokter'] = $kodeDokter;
    $result['antrean'] = array();
		$query = "SELECT a.nKode, a.szPrefix, a.nNoAntrean, a.nStatus, a.nNIKUser, b.szNama
              FROM eksekutif_antrean a
              LEFT OUTER JOIN eksekutif_user b ON a.nNIKUser = b.nNIK
              WHERE a.nKodePoli = $kodeDokter AND DATE(a.tJadwal) = DATE(NOW()) AND (a.nStatus IS NULL OR a.nStatus = 2)
              ORDER BY a.nStatus, a.nNoAntrean;
				    ";
    $connAntri->query($query);
		$antrian = $connAntri->resultSet();
		$a = 0;
		foreach ($antrian as $antrian) {
      $result['antrean'][$a]['no'] = $a+1;
      $result['antrean'][$a]['kode'] = $antrian['nKode'];
			$result['antrean'][$a]['nomor'] = $antrian['szPrefix'].'-'.str_pad($antrian['nNoAntrean'], 3, '0', STR_PAD_LEFT);
			$result['antrean'][$a]['status'] = $antrian['nStatus'];
			$result['antrean'][$a]['loket'] = $antrian['nNIKUser'];
			$result['antrean'][$a]['userLoket'] = $antrian['szNama'];
      $a++;
    }	
    echo json_encode($result);
  }

  // ===========================================================================================
  // daftar
  // ===========================================================================================
  if (@$_GET['cariRM']) {
    $noRM = $_POST['noRM'];
		
    $result['pesanError'] = "";
		$queryPasien = "SELECT nNoRM, CONCAT(szNama, ' ,', szTitle) AS nama, szJenisKelamin, tTglLahir, 
                    CONCAT(IF(szAlamat = '' OR szAlamat IS NULL, '', CONCAT(szAlamat, ', ')), 'RT. ', szRT, ', RW. ', szRW, ', Ds. ', szDesa, ', Kec. ', szKecamatan, ', Kab. ', szkota, ', Prop. ', szPropinsi) AS alamat,
                    (SELECT IF (_yearTahun >= (SELECT nMin FROM _umur_kode WHERE szDeskripsi = '>='  AND szJK = gender),
                    (SELECT nKodeUmur FROM _umur_kode WHERE szDeskripsi = '>=' AND szJK = gender), 
                    IF (_yearTahun = 0 AND _month = 0 AND _day <= (SELECT nMax FROM _umur_kode WHERE nMin=0 AND szJK = gender) ,
                    (SELECT nKodeUmur FROM _umur_kode WHERE nMin=0 AND szJK = gender), 
                    IF (_yearTahun = 0 AND _month = 0 AND _day BETWEEN 7 AND (SELECT nMax FROM _umur_kode WHERE nMin=7 AND szJK = gender),
                    (SELECT nKodeUmur FROM _umur_kode WHERE nMin=7 AND szJK = gender),
                    IF ((_yearTahun = 0 AND _month = 0 AND _day >=29) OR (_yearTahun = 0 AND _month > 0) ,
                    (SELECT nKodeUmur FROM _umur_kode WHERE nMin=29 AND szJK = gender),
                    (SELECT nKodeUmur FROM _umur_kode WHERE (_yearTahun BETWEEN nMin AND nMax) AND szDeskripsi='<' AND szJK = gender))))) AS kodeUmur
                    FROM (SELECT szJenisKelamin AS gender, DATE(u.tTglLahir) AS tglLahir,
                    TIMESTAMPDIFF( YEAR, DATE(tTglLahir), NOW() ) AS _yearTahun
                    , TIMESTAMPDIFF( MONTH, DATE(tTglLahir), NOW() ) % 12 AS _month
                    , FLOOR( TIMESTAMPDIFF( DAY, DATE(tTglLahir), NOW() ) % 30.4375 ) AS _day          
                    FROM _pasien u WHERE nNoRM = '$noRM') k ) AS nKodeUmur
                    FROM _pasien WHERE nNoRM = '$noRM' LIMIT 1;
                  ";
		$conn->query($queryPasien);
		$pasien = $conn->result();
    if (isset($pasien['nNoRM'])) {
      // hitung umur
      $bday = new DateTime($pasien['tTglLahir']);
      $today = new Datetime(date('Y-m-d'));
      $diff = $today->diff($bday);
      // susun result
      $result['noRM'] = $noRM;
      $result['nama']= $pasien['nama'];
      $result['jenisKelamin'] = $pasien['szJenisKelamin'];
      $result['tglLahir'] = $pasien['tTglLahir'];
      $result['umur'] = "$diff->y Tahun $diff->m Bulan $diff->d Hari";
      $result['alamatLengkap'] = $pasien['alamat'];
      $result['kodeUmur'] = $pasien['nKodeUmur'];
      
      // riwayat
      $queryRiwayat = " SELECT a.nKode, a.nPoli, b.szNama AS poli, a.tTglKRS, a.szTindakLanjut 
                        FROM _mutasi_pasien a
                        JOIN _poliklinik b on a.nPoli = b.nKode
                        WHERE a.nNoRM = $noRM
                        ORDER BY a.nKode DESC LIMIT 1
                      ";
      $conn->query($queryRiwayat);
      $riwayat = $conn->result();
      if (isset($riwayat['nKode'])) {
        if ($riwayat['tTglKRS'] == "") {
          $nama = $result['nama'];
          $poli = $riwayat['poli'];
          $result['pesanError'] = "Pasien atas nama <b>$nama</b> masih dalam perawatan <b>$poli</b>";
        } else if ($riwayat['szTindakLanjut'] == "Meninggal") {
          $nama = $result['nama'];
          $tanggal = date("d-m-Y H:i:s", strtotime($riwayat['tTglKRS']));
          $result['pesanError'] = "Pasien atas nama <b>$nama</b> telah meninggal tanggal <b>$tanggal</b>";
        }
      }

      $query = "SELECT SUM(curJM)+SUM(curJS) AS biaya 
                FROM _tarif 
                WHERE nKode IN (80, 79);
              ";
      $conn->query($query);
      $biaya = $conn->result();
      $result['biaya'] = $biaya['biaya'];
    } else {
      $result['pesanError'] = "Nomor RM <b>$noRM</b> Tidak Terdaftar";
    }
    
    echo json_encode($result);
  }
  
?>
