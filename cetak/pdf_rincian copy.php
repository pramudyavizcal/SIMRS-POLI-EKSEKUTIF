<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

include("../database/conStringFarmasi.php"); 
include("../database/conString.php");
include("../database/fungsiTerbilang.php");
include("../database/libsServer/adodbcon.php");
define('FPDF_FONTPATH','../assets/vendor/fpdf184/font/');
require('../assets/vendor/fpdf184/fpdf.php');

$szAsuransi = $_GET['szAsuransi'];
$szSJP = $_GET['szSJP'];

class PDF extends FPDF {
var $col=0;
var $y0;
var $lebar=19;
var $total=12;
var $kiri=2;
	function Header() {
	global $szSJP;
	
		$this->Image('../images/logo_pemkab.jpg',1,1,1);
		$this->SetFont('Arial','',9);
		$this->SetFillColor(224,235,255);
		$this->Cell(1.2,0,'',0,0);
		$this->Cell(10.5,0.8,'PEMERINTAH KABUPATEN BOJONEGORO',0,0,'L');
		$this->SetFont('Arial','BU',10);
		$this->Cell(7.5,0.8,'RINCIAN PEMBAYARAN',0,0,'R');
		$this->Ln(0.35);
		$this->Cell(1.2,0,'',0,0);
		$this->SetFont('Arial','',9);
		$this->Cell(10.5,0.8,'RSUD Dr. R. SOSODORO DJATIKOESOEMO',0,0,'L');
		$this->SetFont('Arial','B',10);
		$this->Cell(7.5,0.8,'No. ' . $szSJP,0,0,'R');
		$this->Ln(0.35);
		$this->Cell(1.2,0,'',0,0);
		$this->SetFont('Arial','',9);
		$this->Cell(14.5,0.8,'Jl. Veteran No. 36 Telepon (0353) 3412133 Fax (0353) 3412133',0,0,'L');
		$this->Ln();		
	}

	function Footer() {
		$this->SetY(-1.3);
		$this->SetFont('Arial','',8);
		$this->Cell(0,1,'Halaman '.$this->PageNo().'/{nb}',0,0,'L');
		$tanggal=date("d M Y H:i:s",time());
		$this->Cell(0,1,'Dicetak melalui Aplikasi SIMRS tanggal '.date('d-m-Y H:i:s'),0,0,'R');
	} 
}

$pdf=new PDF('P','cm','folio');
//$pdf->Open();
$pdf->AliasNbPages();
$pdf->AddPage();
$lebar=19;
$total=12;
$kiri=1;
$x=$pdf->GetY();
$pdf->SetY($x);
$pdf->SetFont('Arial','B',8);
$pdf->SetFillColor(214,235,255);
$pdf->SetTextColor(0);

// Create connection
	$conni = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conni->connect_error) {
		die("Connection failed: " . $conni->connect_error);
	}	
	$conniFarmasi = new mysqli($servernameFarmasi, $usernameFarmasi, $passwordFarmasi, $dbnameFarmasi);
	// Check connection
	if ($conniFarmasi->connect_error) {
		die("Connection failed: " . $conniFarmasi->connect_error);
	}	

$nKodeUser = $_GET['n'];	
$nKode = $_GET['nKode'];

$nEpisode = $_GET['nEpisode'];
$umur_P = '';
	
	$querySTR = "SELECT a.nNoRM,concat(p.szNama,', ',p.szTitle) as szNama,if(szAlamat IN ('','-'),
	concat('RT. ',szRT,' RW. ',szRW,', ',szDesa),concat(szAlamat,' RT. ',szRT,' RW. ',szRW,', ',szDesa)) as szAlamat,
	if(szKecamatan=szKota,szKota,concat(szKecamatan,', ',szKota)) as szKota, szPropinsi, 
	DATE_FORMAT(p.tTglLahir,'%d-%m-%Y') as tTglLahir,
	DATE_FORMAT(a.tTanggal,'%d-%m-%Y %H:%i:%s') as tTanggal,R.szNama as szPoli, R.szDokter,R.szTindakLanjut,R.szKaRu,
	R.nNIP,R.szJab,if(p.szJenisKelamin='L','Laki-laki','Perempuan') as szJK,c.nama_kelas,R.tTglKRS,p.szJenisPeserta 
	from _mutasi_pasien a 
	left outer join _pasien p ON p.nNoRM=a.nNoRM
	left outer JOIN (SELECT a.nKode,a.nPoli,a.nEpisode,b.szNama,a.szTindakLanjut,c.nama as szDokter,b.szKaRu,b.nNIP, 
	if(length(b.nNIP)=18,'NIP. ','NR. BLUD. ') as szJab,date_format(a.tTglKRS,'%d-%m-%Y %H:%i:%s') as tTglKRS from _mutasi_pasien a 
	left outer JOIN _poliklinik b ON b.nKode=a.nPoli left outer JOIN _pegawai c ON c.NIP=a.nKodeDokter
		where a.nEpisode='".$nEpisode."' and b.nKodeKunjungan IN (1,2) order by a.nKode desc limit 1)R ON R.nEpisode=a.nEpisode
		left outer join _mutasi_pasien_penunjang b ON b.nKodeMutasiPasien=R.nKode
		left outer join _ruang_kelas c ON c.id=b.nKelasRuang
	where a.nKode='".$nKode."';";
	if (!$result = $conni->query($querySTR)) {
		exit;
	}
	$user = $result->fetch_assoc();
		$nNoRM = $user['nNoRM'] ;
		$szNama = $user['szNama'] ;
		$szAlamat = $user['szAlamat'] ; 
		$szKota = $user['szKota'] ; 
		$szPropinsi = $user['szPropinsi'] ; 
		$alamat = $szAlamat . ', ' . $szKota . ', ' . $szPropinsi;
		$tTglLahir = $user['tTglLahir'] ; 
		$tTglSEP = $user['tTanggal'] ; 
		$szPoli = $user['szPoli'] ;
		$szDokter = $user['szDokter'] ;
		$szTindakLanjut = $user['szTindakLanjut'] ;
		$szPetugas = $user['szKaRu'] ;
		$nNIP = $user['nNIP'] ;
		$szJab = $user['szJab'] ;
		$szJK = $user['szJK'] ;
		$szKelas = $user['nama_kelas'] ;
		$tTglKRS = $user['tTglKRS'] ;
		$szJP = $user['szJenisPeserta'] ;
		
	$result->free();
	// ----------- CETAK DATA --------------	
	$pdf->Ln(0.5);		
	$pdf->SetFont('Arial','',10);
	$pdf->SetFillColor(224,235,255);
	$pdf->SetTextColor(0);
	$pdf->Cell(0.1,0,'',0,0);
	$pdf->Cell(2.5,0.6,'Nama','',0,'L');
	$pdf->Cell(9.5,0.6,': '.$szNama,'',0,'L');
	$pdf->Cell(2.5,0.6,'No. RM','',0,'L');
	$pdf->Cell(5,0.6,': '.$nNoRM,'',0,'L');
	$pdf->Ln(0.5);		
	$pdf->Cell(0.1,0,'',0,0);
	$pdf->Cell(2.5,0.6,'Tgl Lahir / JK','',0,'L');
	$pdf->Cell(9.5,0.6,': '.$tTglLahir . ' / ' . $szJK,'',0,'L');
	$pdf->Cell(2.5,0.6,'Pembiayaan','',0,'L');
	$pdf->Cell(5,0.6,': '.$szAsuransi,'',0,'L');
	$pdf->Ln(0.5);						
	$pdf->Cell(0.1,0,'',0,0);
	$pdf->Cell(2.5,0.6,'Alamat','',0,'L');
	$pdf->Cell(17,0.6,': '.$alamat,'',0,'L');
	$pdf->Ln(0.5);		
	$pdf->Ln(1);	
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(0.1,0,'',0,0);
	$pdf->Cell(1,0.6,'NO','LRTB',0,'C');
	$pdf->Cell(11.9,0.6,'URAIAN','LRTB',0,'C');
	$pdf->Cell(1,0.6,'QTY','LRTB',0,'C');
	$pdf->Cell(2.5,0.6,'TARIF','LRTB',0,'C');
	$pdf->Cell(2.5,0.6,'BIAYA','LRTB',0,'C');
	$pdf->Ln();
	$pdf->Cell(0.1,0,'',0,0);
	
	$pdf->Ln();
	
	//diisi penunjang medis
	$queryPNJ = "SELECT -1 AS Idx,'PENUNJANG MEDIS' AS szNama, NULL AS curBayar FROM _mutasi_pasien_tindakan a 
		LEFT OUTER JOIN _poliklinik b ON b.nKode=a.nKodePoli
		WHERE a.nEpisode='$nEpisode' AND ((a.nKodeBayar IN ('R','K') AND a.szNoKwitansi IS NOT NULL) 
		OR (a.nKodeBayar IN ('T','N') AND a.szNoKwitansi = '$szSJP')) AND a.nKategori IS NULL AND b.nKodeKunjungan='3'
		UNION
		SELECT Idx,szNama,SUM(nQtt*curTarif) AS curBayar FROM (SELECT a.nKodePoli AS Idx,b.szNama,c.nQtt,
		(IFNULL(c.curJM,0)+IFNULL(c.curJS,0)+IFNULL(c.curBBA,0)+IFNULL(c.curCito,0)) AS curTarif
		 FROM _mutasi_pasien_tindakan a
		LEFT OUTER JOIN _poliklinik b ON b.nKode=a.nKodePoli
		LEFT OUTER JOIN _mutasi_pasien_tindakan_detil c ON c.nKodeMutasiPasienTindakan=a.nKode
		WHERE a.nEpisode='$nEpisode' AND ((a.nKodeBayar IN ('R','K') AND a.szNoKwitansi IS NOT NULL) 
		OR (a.nKodeBayar IN ('T','N') AND a.szNoKwitansi = '$szSJP')) AND a.nKategori IS NULL AND b.nKodeKunjungan='3')L
		GROUP BY Idx ORDER BY Idx;";
	if (!$resPNJ = $conni->query($queryPNJ)) {
		exit;
	}
	$a = 0; $curTotal = 0;
	while($resRPNJ = $resPNJ->fetch_assoc()){
		$Idx_P = $resRPNJ['Idx'] ;
		$szNama_P = $resRPNJ['szNama'] ;
		$curBayar_P = $resRPNJ['curBayar'] ;
		if($curBayar_P){
			$szBea_P = number_format($curBayar_P);
		} else {
			$szBea_P = '';
		}
		$curTotal = $curTotal + $curBayar_P;
	$pdf->Cell(0.1,0,'',0,0);	
	if($Idx_P == -1){
		$a = $a + 1;
		$pdf->Cell(1,0.6,$a,'LRTB',0,'C');
		$pdf->Cell(15.4,0.6,$szNama_P,'LRTB',0,'L');
	} else {
		$a = $a;		
		$pdf->Cell(1,0.6,'','LRTB',0,'C');
		$pdf->Cell(15.4,0.6,'- '.$szNama_P,'LRTB',0,'L');
	}
	$pdf->Cell(2.5,0.6,$szBea_P,'LRTB',0,'R');
	$pdf->Ln();
	}
	//diisi tindakan non op
	$queryNOP = "SELECT -1 AS Idx,'TINDAKAN NON OPERATIF' AS szNama,NULL AS curBayar
		FROM (SELECT IF(e.nKodeKunjungan=3,9,(IF(e.nKodeKunjungan IN (4) AND c.nKodeKategori IS NULL,7,c.nKodeKategori))) AS nKategori
		 FROM _mutasi_pasien_tindakan a
		LEFT OUTER JOIN _mutasi_pasien_tindakan_detil d ON d.nKodeMutasiPasienTindakan=a.nKode 
		LEFT OUTER JOIN _tarif_tindakan b ON b.nKodeID=d.nKodeTarifTindakan
		LEFT OUTER JOIN _tindakan_master c ON c.nKodeTindakan=b.nKodeTindakan
		LEFT OUTER JOIN _poliklinik e ON e.nKode=a.nKodePoli
		WHERE a.nEpisode='$nEpisode'  AND a.nKodeBayar IN ('T','N','R') AND a.szNoKwitansi = '$szSJP' AND a.nKategori IS NULL)K 
		WHERE K.nKategori IN (7)
		UNION
		SELECT nKodePoli AS Idx,szNamaTarifTindakan,SUM(nQtt*curBea) AS curBayar FROM (SELECT IF(e.nKodeKunjungan=3,9,(IF(e.nKodeKunjungan IN (4) AND c.nKodeKategori IS NULL,7,c.nKodeKategori))) AS nKategori,d.szNamaTarifTindakan,d.nQtt,
		(IFNULL(d.curJM,0)+IFNULL(d.curJS,0)+IFNULL(d.curBBA,0)+IFNULL(d.curCito,0)) AS curBea,a.nKodePoli
		 FROM _mutasi_pasien_tindakan a
		LEFT OUTER JOIN _mutasi_pasien_tindakan_detil d ON d.nKodeMutasiPasienTindakan=a.nKode 
		LEFT OUTER JOIN _tarif_tindakan b ON b.nKodeID=d.nKodeTarifTindakan
		LEFT OUTER JOIN _tindakan_master c ON c.nKodeTindakan=b.nKodeTindakan
		LEFT OUTER JOIN _poliklinik e ON e.nKode=a.nKodePoli
		WHERE a.nEpisode='$nEpisode' AND a.nKodeBayar IN ('T','N','R') AND a.szNoKwitansi = '$szSJP' AND a.nKategori IS NULL AND e.nKodeKunjungan IN (4))K 
		WHERE K.nKategori IN (7) GROUP BY nKodePoli ORDER BY Idx;";
	if (!$resNOP = $conni->query($queryNOP)) {
		exit;
	}
	while($resRNOP = $resNOP->fetch_assoc()){
		$Idx_O = $resRNOP['Idx'] ;
		$szNama_O = $resRNOP['szNama'] ;
		$curBayar_O = $resRNOP['curBayar'] ;
		if($curBayar_O){
			$szBea_O = number_format($curBayar_O);
		} else {
			$szBea_O = '';
		}
		$curTotal = $curTotal + $curBayar_O;
	$pdf->Cell(0.1,0,'',0,0);	
	if($Idx_O == -1){
		$a = $a + 1;
		$pdf->Cell(1,0.6,$a,'LRTB',0,'C');
		$pdf->Cell(15.4,0.6,$szNama_O,'LRTB',0,'L');
	} else {
		$a = $a;		
		$pdf->Cell(1,0.6,'','LRTB',0,'C');
		$pdf->Cell(15.4,0.6,'- '.$szNama_O,'LRTB',0,'L');
	}
	$pdf->Cell(2.5,0.6,$szBea_O,'LRTB',0,'R');
	$pdf->Ln();
	}
	
	//diisi Farmasi
	$queryFRM = "SELECT -1 as Idx,'FARMASI' as szNamaTarif,null as qty,SUM(curBayar-IFNULL(curRetur,0)) as curTarif 
		from _farmasi_penjualan where nEpisodeMutasiPasien='".$nEpisode."' and nKodeKategoriPembayaran != 'T' group by nEpisodeMutasiPasien order by Idx;";
	if (!$resF = $conniFarmasi->query($queryFRM)) {
		exit;
	}
	$totalFRM = 0;
	while($resFx = $resF->fetch_assoc()){
		$IdxF = $resFx['Idx'] ;
		$szNamaF = $resFx['szNamaTarif'] ;
		$nQtyF = $resFx['qty'] ;
		$curTarifF = $resFx['curTarif'] ;
			$a = $a + 1; 
			$Fx = $a;
			if($curTarifF) { $szTarifF = number_format($curTarifF); } else { $szTarifF = '';}
		$totalFRM = $totalFRM + $curTarifF;
	$pdf->Cell(0.1,0,'',0,0);
	$pdf->Cell(1,0.6,$Fx,'LRTB',0,'C');
	$pdf->Cell(15.4,0.6,$szNamaF,'LRTB',0,'L');
	$pdf->Cell(2.5,0.6,$szTarifF,'LRTB',0,'R');
	$pdf->Ln();
	}
	//diisi administrasi
	$queryADM = "SELECT -1 AS Idx,'ADMINISTRASI' AS szNama,NULL AS curBayar 
		FROM _mutasi_keuangan WHERE nKodeMutasiPasien='$nKode'
		UNION
		SELECT a.nKode, a.szNamaTarif, SUM(IFNULL(a.curJM,0)+IFNULL(a.curJS,0)+IFNULL(a.curBBA,0)) AS curBayar
		FROM _mutasi_keuangan a
		WHERE a.nKodeMutasiPasien='$nKode';";
	if (!$resADM = $conni->query($queryADM)) {
		exit;
	}
	while($resRADM = $resADM->fetch_assoc()){
		$Idx_A = $resRADM['Idx'] ;
		$szNama_A = $resRADM['szNama'] ;
		$curBayar_A = $resRADM['curBayar'] ;
		if($curBayar_A){
			$szBea_A = number_format($curBayar_A);
		} else {
			$szBea_A = '';
		}
		$curTotal = $curTotal + $curBayar_A;
	$pdf->Cell(0.1,0,'',0,0);	
	if($Idx_A == -1){
		$a = $a + 1;
		$pdf->Cell(1,0.6,$a,'LRTB',0,'C');
		$pdf->Cell(15.4,0.6,$szNama_A,'LRTB',0,'L');
	} else {
		$a = $a;		
		$pdf->Cell(1,0.6,'','LRTB',0,'C');
		$pdf->Cell(15.4,0.6,'- '.$szNama_A .' dan Pemeriksaan Dokter','LRTB',0,'L');
	}
	$pdf->Cell(2.5,0.6,$szBea_A,'LRTB',0,'R');
	$pdf->Ln();
	}

	//Query Petugas
	$queryBPJS = "SELECT a.curBayar,c.szNama,c.nNIP,a.tTanggal,IF(LENGTH(c.nNIP) > 13,'NIP. ','NR. BLUD ') AS szJabKasir 
			FROM _mutasi_pendapatan_2022 a
			LEFT OUTER JOIN eksekutif_user c ON c.nNIK=a.nNIKOLeh WHERE a.nEpisode='$nEpisode' LIMIT 1;";
	if (!$resR = $conni->query($queryBPJS)) {
		exit;
	}
	$curTunai = 0;
	while ($resRR = $resR->fetch_assoc()) {
		$curTunaiX = $resRR['curBayar'];
		$curTunai = $curTunai + $curTunaiX;
		$szKasir = $resRR['szNama'];
		$nipKasir = $resRR['nNIP'];
		$tKasir = date('d-m-Y H:i:s', strtotime($resRR['tTanggal']));
		$jabKasir = $resRR['szJabKasir'];
	}

	//$curSumTotal = $curTotal + $totalFRM;
	$curSumTotal = $curTotal + $totalFRM;
	$szTotalFRM = number_format($totalFRM);
	$szTotal = number_format($curSumTotal);
	
	// if($curTunai > 0){
	// 	$szCurSJP = number_format($curSJP);
	// 	$szCurTunai = number_format($curTunai);
	// }
    $curBilang = Terbilang($curSumTotal);
	
	$pdf->SetFont('Arial','B',10);
	$pdf->Cell(0.1,0,'',0,0);
	$pdf->Cell(16.4,0.6,'TOTAL TARIF RUMAH SAKIT','LRTB',0,'R');
	$pdf->Cell(2.5,0.6,$szTotal,'LRTB',0,'R');
	$pdf->Ln();
	$pdf->SetFont('Arial','BI',10);
	$pdf->Cell(0.1,0,'',0,0);
	$pdf->Cell(18.9,0.6,'Terbilang : '.$curBilang.' Rupiah','LRTB',0,'L');
	$pdf->Ln();
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(1.1,0,'',0,0);		
	$pdf->Cell(12,0,'',0,0);
	$pdf->Cell(5,0.6,'Bojonegoro, '.$tKasir,'',0,'C');
	$pdf->Ln(0.5);
	$pdf->Cell(1.1,0,'',0,0);		
	$pdf->Cell(12,0,'',0,0);
	$pdf->Cell(5,0.6,'Petugas Poli Eksekutif','',0,'C');
	$pdf->Ln(1);
	$pdf->SetFont('Arial','U',9);	
	$pdf->Cell(13.1,0,'',0,0);
	$pdf->Cell(5,0.6,$szKasir,'',0,'C');
	$pdf->Ln(0.4);
	$pdf->SetFont('Arial','',9);	
	$pdf->Cell(13.1,0,'',0,0);
	$pdf->Cell(5, 0.6, $jabKasir . ' ' . $nipKasir, '', 0, 'C');

	
	$pdf->Ln();
$pdf->SetAutoPageBreak(false);	
	 
$pdf->Output();
$conni->close();
$conniFarmasi->close();	
?> 