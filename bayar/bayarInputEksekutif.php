<?php
include("../database/libsServer/adodbcon.php");
include("../database/conString.php");
include_once("../config.php");

// Create connection
$conni = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conni->connect_error) {
	die("Connection failed: " . $conni->connect_error);
} 

$n = $_SESSION['simrs']['pegawai']['kode'];
$nip = $_SESSION['simrs']['pegawai']['nip'];

//==================== Pasien Bayar Tunai ====================//
if (@$_GET['Bayar']) {
	$nKodeMutasiPasien = $_GET['nKodeMutasiPasien'];
	$nKodeBayar = $_GET['szBayar'];
	$nEpisode = $_GET['nEpisode'];
	@$szEDC = $_GET['szEDC'];
	@$buktiTransaksi = $_GET['szCatatan'];
	$nKodePoli = $_GET['nKodePoli'];
	$curBayar = $_GET['curBayar'];
	$tahunSekarang = $_GET['tahunSekarang'];

	$querySTR = "SELECT (SELECT IFNULL(MAX(nNoBayar),0)+1 FROM _mutasi_pendapatan_$tahunSekarang WHERE nKodeBayar='$nKodeBayar' AND YEAR(tTanggal)=YEAR(NOW())) AS nNoBayar, 
		(SELECT szAlias FROM eksekutif_user WHERE nNIK='$n') AS initial,
		a.szAsuransi,a.nNoRM FROM _mutasi_pasien a
		WHERE a.nKode='$nKodeMutasiPasien'; ";

	$rs = $conn->Execute($querySTR) or die($conn->ErrorMsg());
	$rs->MoveFirst();
	while (!$rs->EOF) {
		$nNoBayar = $rs->fields[0];
		$szInit = $rs->fields[1];
		$szAsuransi = $rs->fields[2];
		$noRM = $rs->fields[3];
		$rs->MoveNext();
	}
	if (strlen($nNoBayar) == 1) {
		$noKuiSHow = "000" . $nNoBayar;
	} elseif (strlen($nNoBayar) == 2) {
		$noKuiSHow = "00" . $nNoBayar;
	} elseif (strlen($nNoBayar) == 3) {
		$noKuiSHow = "0" . $nNoBayar;
	} else {
		$noKuiSHow = $nNoBayar;
	}
	$tCetak =  date("dmy");
	$data = "GWK/" . strtoupper($szInit) . "/" . $nKodeBayar . "/" . $tCetak . $noKuiSHow;
	$noKw = $nNoBayar;

	if($nKodeBayar == "T"){
		$query = "INSERT INTO _mutasi_pendapatan_$tahunSekarang(nKodeMutasiPasien,nEpisode,nNoRM,szAsuransi,szNoKwitansi,
	curBayar,nNIKOleh,tTanggal,nKodeBayar,nNoBayar,nKodePoli)VALUES('" . $nKodeMutasiPasien . "','" . $nEpisode . "','" . $noRM . "',
	'" . $szAsuransi . "','" . $data . "','" . $curBayar . "','$nip',now(),'" . $nKodeBayar . "','" . $noKw . "','" . $nKodePoli . "');";

		$query = $query . " UPDATE _mutasi_pasien SET nNIKOlehKasir='$nip', szSJP = '$data' where nEpisode = '" . $nEpisode . "';";
		$query = $query . " UPDATE pasien SET aktif = 'T',proses='', tujuan='' where nostatus = '" . $noRM . "';";
		$query = $query . " UPDATE _mutasi_pasien_tindakan SET szNoKwitansi = '" . $data . "', nNoKui='" . $noKw . "', tTglBayar=now(), 
		nNIKBayar = '" . $nip . "', nKodeBayar = '" . $nKodeBayar . "' where nEpisode = '$nEpisode';";
		$query = $query . " UPDATE _mutasi_pasien_tindakan SET curBayar = '$curBayar' where nKodeMutasiPasien = '$nKodeMutasiPasien';";
	}else{
		$query = "INSERT INTO _mutasi_pendapatan_$tahunSekarang(nKodeMutasiPasien,nEpisode,nNoRM,szAsuransi,szNoKwitansi,
	curBayar,nNIKOleh,tTanggal,nKodeBayar,nNoBayar,nKodePoli)VALUES('" . $nKodeMutasiPasien . "','" . $nEpisode . "','" . $noRM . "',
	'" . $szAsuransi . "','" . $data . "','" . $curBayar . "','" . $nip . "',now(),'" . $nKodeBayar . "','" . $noKw . "','" . $nKodePoli . "');";

		$query = $query . " UPDATE _mutasi_pasien SET nNIKOlehKasir='$nip', szSJP = '$data' where nEpisode = '" . $nEpisode . "';";
		$query = $query . " UPDATE pasien SET aktif = 'T',proses='', tujuan='' where nostatus = '" . $noRM . "';";
		$query = $query . " UPDATE _mutasi_pasien_tindakan SET szNoKwitansi = '" . $data . "', nNoKui='" . $noKw . "', tTglBayar=now(), 
		nNIKBayar = '" . $n . "', nKodeBayar = '" . $nKodeBayar . "',szCatatan = '$szEDC,  No. Transaksi $buktiTransaksi' where nEpisode = '$nEpisode';";
		$query = $query . " UPDATE _mutasi_pasien_tindakan SET curBayar = '$curBayar' where nKodeMutasiPasien = '$nKodeMutasiPasien';";
		
	}
		//echo $query;

	if ($conni->multi_query($query) === TRUE) {
		echo "Berhasil Input Data";
			// $conni->close();
			// echo $n . "|1|Berhasil Input Data";
		} else {
			echo "Gagal Input Data";
			// $conni->close();
			//echo $n . "|0|ERROR - Gagal Input Data";
		}

}
//====================== end of Tunai Eksekutif =========================//


$conn->close();
$conni->close();
