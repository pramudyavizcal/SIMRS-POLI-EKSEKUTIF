<?php
  include_once("../config.php");
  include_once("../koneksi/koneksi99.php");
  include_once("../database/fungsiTerbilang.php");
  define('FPDF_FONTPATH','../assets/vendor/fpdf184/font/');
  require('../assets/vendor/fpdf184/fpdf.php');


	$nKodeUser = $_SESSION['simrs']['pegawai']['kode'];	
	$nomor = $_GET['nomor'];
  @$tahunSekarang = $_GET['tahunSekarang'];

	
	$query = "SELECT a.szNoKwitansi, a.nNoRM, CONCAT(b.szNama,', ', szTitle) AS nama, CONCAT(IF(szAlamat = '' OR szAlamat IS NULL, '', CONCAT(szAlamat, ', ')), 'RT. ', szRT, ', RW. ', szRW, ', ', szDesa, ', ', szKecamatan, IF(szkota = szKecamatan, '', CONCAT(', ', szkota)), ', ', szPropinsi) AS alamat,
            d.szNama AS dokter, a.curBayar, a.szAsuransi, IF(a.nKodeBayar = 'T', 'Tunai', 'Non Tunai') AS caraBayar, a.tTanggal
            FROM _mutasi_pendapatan_$tahunSekarang a 
            JOIN _pasien b ON b.nNoRM = a.nNoRM
            JOIN _mutasi_pasien c ON c.nKode = a.nKodeMutasiPasien
            JOIN _poliklinik d ON c.nPoli = d.nKode
            WHERE a.szNoKwitansi = '$nomor' AND d.nKodeKunjungan = 4;
          ";
  $conn->query($query);
  $hasil = $conn->result();
  if (!isset($hasil['nNoRM'])) {
    die("Tidak ada hasil");
  }
  $szNoKwitansi = $hasil['szNoKwitansi'];
  $noRM = $hasil['nNoRM'];
  $nama = $hasil['nama'];
  $alamat = $hasil['alamat'];
  $dokter = $hasil['dokter'];
  $biaya = $hasil['curBayar'];
  $asuransi = $hasil['szAsuransi'];
  $caraBayar = $hasil['caraBayar'];
  $tanggal = date("d-m-Y H:i:s", strtotime($hasil['tTanggal']));

  $query = "SELECT szNama, nNIP FROM eksekutif_user WHERE nNIK = '$nKodeUser'";
  $conn->query($query);
  $hasil = $conn->result();
  $petugas = $hasil['szNama'];
  $nip = $hasil['nNIP'];



  class PDF extends FPDF {
    function Header() {
      global $szNoKwitansi;
    
      $this->Image('../images/logo_pemkab.jpg',1,1.1,1);
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
      $this->Ln(1);	
    }

    function Footer() {
      $this->SetY(-1.3);
      $this->SetFont('Arial','',8);
      $this->Cell(0,1,'Halaman '.$this->PageNo().'/{nb}',0,0,'L');
      $tanggal = date("d M Y H:i:s",time());
      $this->Cell(0,1,"Dicetak melalui Aplikasi SIMRS tanggal $tanggal",0,0,'R');
    } 
  }

  $pdf = new PDF('P','cm','folio');
  //$pdf->Open();
  $pdf->AliasNbPages();
  $pdf->AddPage();
  $pdf->SetFont('Arial','',8);
  $pdf->SetFillColor(214,235,255);
  $pdf->SetTextColor(0);

  $pdf->SetFont('Arial','',9);
	$pdf->Cell(19,0.8,'Telah terima dari :',0,0,'L');
	$pdf->Ln(0.5);
	$pdf->Cell(3,0.8,'No. RM / Nama',0,0,'L');
	$pdf->Cell(0.5,0.8,':',0,0,'C');
	$pdf->Cell(15.5,0.8,$noRM . " / " .$nama,0,0,'L');
	$pdf->Ln(0.5);
  $pdf->Cell(3,0.8,'Alamat',0,0,'L');
	$pdf->Cell(0.5,0.8,':',0,0,'C');
	$pdf->Cell(15.5,0.8,$alamat,0,0,'L');
	$pdf->Ln(0.5);
  $pdf->Cell(3,0.8,'Jenis Pembayaran',0,0,'L');
	$pdf->Cell(0.5,0.8,':',0,0,'C');
	$pdf->Cell(15.5,0.8,$caraBayar,0,0,'L');
	$pdf->Ln(0.5);
  $pdf->Cell(3,0.8,'Sejumlah',0,0,'L');
	$pdf->Cell(0.5,0.8,':',0,0,'C');
	$pdf->Cell(15.5,0.8,Terbilang($biaya),0,0,'L');
	$pdf->Ln(1);
  $pdf->Cell(19,0.8,'Untuk Pembayaran Biaya Poli Eksekutif :',0,0,'L');
	$pdf->Ln(0.5);
  $pdf->Cell(3,0.8,'Ruang',0,0,'L');
	$pdf->Cell(0.5,0.8,':',0,0,'C');
	$pdf->Cell(14.5,0.8,$dokter,0,0,'L');
  $pdf->Ln(0.5);
  $pdf->Cell(3,0.8,'Pembiayaan',0,0,'L');
	$pdf->Cell(0.5,0.8,':',0,0,'C');
	$pdf->Cell(14.5,0.8,$asuransi,0,0,'L');
  $pdf->Ln(2);


  $pdf->SetFont('Arial','',8);
	$pdf->Cell(10,0,'',0,0);
	$pdf->Cell(9,0.5,'Bojonegoro, '.$tanggal,'',0,'C');
	$pdf->Ln(0.5);
	$pdf->Cell(10,0.5,'','',0,'C');
	$pdf->Cell(9,0.5,'Petugas','',0,'C');
	$pdf->Ln(0.5);    
	$pdf->SetFont('Arial','B',11);
	$pdf->Cell(1,1.3,'Rp. ','LTB',0,'C'); 
	$pdf->Cell(3,1.3,number_format($biaya).',-','RTB',0,'R'); 
	$pdf->Ln(1);
	$pdf->SetFont('Arial','BU',8);
	$pdf->Cell(10,0.5,'','',0,'C');
	$pdf->Cell(9,0.5,$petugas,'',0,'C');
	$pdf->Ln(0.5);
	$pdf->SetFont('Arial','',8);	
	$pdf->Cell(10,0.5,'','',0,'C');
	$pdf->Cell(9,0.5, $nip,'',0,'C');




  $pdf->Output();	
?>