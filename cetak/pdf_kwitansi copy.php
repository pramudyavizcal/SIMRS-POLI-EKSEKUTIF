<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

include("../database/conString.php"); 
include("../database/fungsiTerbilang.php");
define('FPDF_FONTPATH','../assets/vendor/fpdf184/font/');
require('../assets/vendor/fpdf184/fpdf.php');

$nKodeUser = $_GET['n'];	
$nKode = $_GET['nKode'];	
$szKwitansi = $_GET['szKwitansi'];
$nEpisode = $_GET['nEpisode'];

// Create connection
	$conni = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conni->connect_error) {
		die("Connection failed: " . $conni->connect_error);
	}		
	
	$querySTR = "SELECT a.nKodeMutasiPasien,b.nKode, a.nNoRM,d.szNama,d.szTitle,IF(szAlamat IN ('','-'),
					CONCAT('RT. ',szRT,' RW. ',szRW,', ',szDesa),CONCAT(szAlamat,' RT. ',szRT,' RW. ',szRW,', ',szDesa)) AS szAlamat,
					IF(szKecamatan=szKota,szKota,CONCAT(szKecamatan,', ',szKota)) AS szKota,szPropinsi,curBayar,a.tTanggal,a.nNIKOleh,a.szAsuransi,c.szNama AS szPoli,
					(SELECT MIN(DATE(d.tTanggal)) FROM _mutasi_pasien d 
					LEFT OUTER JOIN _poliklinik c ON c.nKode=d.nPoli WHERE nEpisode='$nEpisode' AND c.nKodeKunjungan=4) AS tglMRS,
					(SELECT MAX(DATE(d.tTimestamp)) FROM _mutasi_pasien_tindakan_detil d 
					LEFT OUTER JOIN _poliklinik c ON c.nKode=d.nKodePoli WHERE nEpisode='$nEpisode' AND c.nKodeKunjungan=4 AND nKodeTindakan=2063) AS tglKRS,
					f.nama_kelas,e.nNoBed,g.szNama AS szPetugas, g.nNIP,
					a.szNoKwitansi,IF(h.status IN ('CPNS','PNS'),'NIP. ','NR. BLUD ') AS STATUS,
					a.tTanggal AS tTglBayar, (SELECT nKodeBayar FROM _mutasi_pasien_tindakan i WHERE i.nKodeMutasiPasien = a.nKodeMutasiPasien) AS kodeBayar
					FROM _mutasi_pendapatan_2022 a 
					LEFT OUTER JOIN _mutasi_pasien b ON b.nEpisode=a.nEpisode 
					LEFT OUTER JOIN _poliklinik c ON c.nKode=b.nPoli
					LEFT OUTER JOIN _pasien d ON d.nNoRM=a.nNoRM
					LEFT OUTER JOIN _mutasi_pasien_penunjang e ON e.nKodeMutasiPasien=b.nKode
					LEFT OUTER JOIN _ruang_kelas f ON f.id=e.nKelasRuang
					LEFT OUTER JOIN eksekutif_user g ON g.nNIK=a.nNIKOleh    
					LEFT OUTER JOIN _pegawai h ON h.NIP=g.nNIP
					WHERE a.nKode='$nKode' AND c.nKodeKunjungan=4 ORDER BY b.nKode DESC LIMIT 1;";
		if (!$result = $conni->query($querySTR)) {
			exit;
		}
		$user = $result->fetch_assoc();
			$nNoRM = $user['nNoRM'] ;
			$szNama = $user['szNama'] ;
			$szTitle = $user['szTitle'] ;
			$szAlamat = $user['szAlamat'] ;
			$szKota = $user['szKota'] ;
			$szPropinsi = $user['szPropinsi'] ;
			$szPoli = $user['szPoli'] ;
			$szPetugas = $user['szPetugas'] ;
			$nNIP = $user['nNIP'] ;
			$szNoKwitansi = $user['szNoKwitansi'];
			//$szJab = $user['status'] ;
			$szBayar = $user['kodeBayar'] ;
			$nKodeMutasiPasien = $user['nKodeMutasiPasien'] ;
			$tMRS = $user['tglMRS'] ;
			$tKRS = $user['tglKRS'] ;
			if($tMRS == $tKRS){
				$tInap = date('d-m-Y',strtotime($tMRS));
			} else {
				//$tInap = date('d-m-Y',strtotime($tMRS)) . ' s/d ' . date('d-m-Y',strtotime($tKRS)) ;
			}
			$szKelas = $user['nama_kelas'] . ' / ' .  $user['nNoBed'];
			$tTglBayar = $user['tTglBayar'] ; $tBayar = date('d-m-Y H:i:s',strtotime($tTglBayar));
			$szCatatan = ""; //$user['szCatatan'] ;
			$tPoli = $user['tTanggal'] ;
			$szAsuransi = $user['szAsuransi'] ;
			$curBayar = $user['curBayar'] ;
			// $szNoBayar = $user['szNoBayar'] ;
			if($szBayar == "T"){ 
				$szBayar = "Tunai"; 
			}
			if($szBayar == "N"){ 
				$szBayar = "Non Tunai"; 
			}
			else { 
			$szBayar = $szBayar .", " . $szCatatan; 
			}
			$curTotal = number_format($curBayar);
			$curBilang = Terbilang($curBayar);
		$result->free();
	
class PDF extends FPDF {
var $col=0;
var $y0;
var $lebar=19;
var $total=12;
var $kiri=2;
	function Header() {
		global $szNoKwitansi;
	
		$this->Image('../images/logo_pemkab.jpg',1,1,1);
		$this->SetFont('Arial','',8);
		$this->SetFillColor(224,235,255);
		$this->Cell(1.2,0,'',0,0);
		$this->Cell(10.5,0.8,'PEMERINTAH KABUPATEN BOJONEGORO',0,0,'L');
		$this->SetFont('Arial','BU',10);
		$this->Cell(7.5,0.8,'KWITANSI PEMBAYARAN',0,0,'R');
		$this->Ln(0.37);
		$this->Cell(1.2,0,'',0,0);
		$this->SetFont('Arial','',9);
		$this->Cell(10.5,0.8,'RSUD Dr. R. SOSODORO DJATIKOESOEMO',0,0,'L');
		$this->SetFont('Arial','B',10);
		$this->Cell(7.5,0.8, 'No. '.$szNoKwitansi,0,0,'R');
		$this->Ln(0.35);
		$this->Cell(1.2,0,'',0,0);
		$this->SetFont('Arial','',9);
		$this->Cell(14.5,0.8,'Jl. Veteran No. 36 Telepon (0353) 3412133 Fax (0353) 3412133',0,0,'L');
		$this->Ln(0.35);
		$this->Cell(0.1,0,'',0,0);
		$this->SetFont('Arial','',9);
		$this->Cell(19,0.4,'','B',0,'L');
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

$pdf=new PDF('P','cm','sjp');
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

	// ----------- CETAK DATA --------------	
	
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(0.5,0,'',0,0);
	$pdf->Cell(18.5,0.8,'Telah terima dari :',0,0,'L');
	$pdf->Ln(0.5);
	$pdf->Cell(0.5,0,'',0,0);
	$pdf->Cell(3,0.8,'No. RM / Nama',0,0,'L');
	$pdf->Cell(0.5,0.8,':',0,0,'C');
	$pdf->Cell(13,0.8,$nNoRM . " / " .$szNama . ", " . $szTitle,0,0,'L');
	$pdf->Ln(0.5);
	$pdf->Cell(0.5,0,'',0,0);
	$pdf->Cell(3,0.8,'Alamat',0,0,'L');
	$pdf->Cell(0.5,0.8,':',0,0,'C');
	$pdf->Cell(13,0.8,$szAlamat . ", " . $szKota . ", " . $szPropinsi,0,0,'L');
	$pdf->Ln(0.5);
	$pdf->Cell(0.5,0,'',0,0);
	$pdf->Cell(3,0.8,'Jenis Pembayaran',0,0,'L');
	$pdf->Cell(0.5,0.8,':',0,0,'C');
	$pdf->Cell(13,0.8,$szBayar,0,0,'L');
	$pdf->Ln(0.5);
	$pdf->Cell(0.5,0,'',0,0);
	$pdf->Cell(3,0.8,'Sejumlah',0,0,'L');	  
	$pdf->Cell(0.5,0.8,':',0,0,'C');  
	$pdf->SetFont('Arial','BI',10) ;
    $xRowA = $pdf->GetStringWidth($curBilang);
	$zRowA = ceil($xRowA/13);
	$x = $pdf->GetX();
	$y = $pdf->GetY();
	if($zRowA == 1) {
	$pdf->Cell(13,0.8,$curBilang . ' Rupiah','',0,'L');
	$pdf->Ln(0.7);	
	} else {
	$pdf->MultiCell(13,0.7,$curBilang . ' Rupiah',0,'L',false);
	$pdf->SetXY($x + 0.3, $y);
	$pdf->Ln(1.4);	
	}    
	$pdf->SetFont('Arial','',9) ;
	$pdf->Cell(0.5,0,'',0,0);
	$pdf->Cell(18.5,0.8,'Untuk Pembayaran Biaya Poli Eksekutif :',0,0,'L');
	$pdf->Ln(0.5);
	$pdf->Cell(1.5,0,'',0,0);
	$pdf->Cell(2,0.8,'Ruang','',0,'L');
	$pdf->Cell(0.5,0.8,':',0,0,'C');
	$pdf->Cell(13,0.8,$szPoli,'',0,'L');
	$pdf->Ln(0.5);

	$pdf->Cell(1.5,0,'',0,0);
	$pdf->Cell(2,0.8,'Pembiayaan','',0,'L');
	$pdf->Cell(0.5,0.8,':',0,0,'C');
	$pdf->Cell(13,0.8,$szAsuransi,'',0,'L');
	$pdf->Ln();
    
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(10.5,0,'',0,0);
	$pdf->Cell(10,0.5,'Bojonegoro, '.$tBayar,'',0,'C');
	$pdf->Ln();
	$pdf->Cell(0.5,0,'',0,0);
	$pdf->Cell(10,0.5,'','',0,'C');
	$pdf->Cell(10,0.5,'Petugas','',0,'C');
	$pdf->Ln(0.5);
	$pdf->Cell(0.7,0,'',0,0);      
	$pdf->SetFont('Arial','B',11);
	$pdf->Cell(1,1.3,'Rp. ','LTB',0,'C'); 
	$pdf->Cell(3,1.3,$curTotal.',-','RTB',0,'R'); 
	$pdf->Ln(1);
	$pdf->Cell(10.5,0,'',0,0);       	
	$pdf->SetFont('Arial','BU',8);
	$pdf->Cell(10,0.5,$szPetugas,'',0,'C');
	$pdf->Ln(0.5);
	$pdf->SetFont('Arial','',8);	
	$pdf->Cell(0.5,0,'',0,0);
	$pdf->Cell(10,0.5,'','',0,'C');
	$pdf->Cell(10,0.4, $nKodeUser . '' . $nNIP,'',0,'C');
	
	$pdf->Ln();		 
	 
$pdf->Output();
