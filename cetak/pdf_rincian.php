<?php
  include_once("../config.php");
  include_once("../koneksi/koneksi99.php");
  include_once("../koneksi/koneksi48.php");
  include_once("../database/fungsiTerbilang.php");
  define('FPDF_FONTPATH','../assets/vendor/fpdf184/font/');
  require('../assets/vendor/fpdf184/fpdf.php');


	$nKodeUser = $_SESSION['simrs']['pegawai']['kode'];	
	$nomorkwitansi = $_GET['nomor'];

  class PDF extends FPDF {
    function Header() {
      global $nomorkwitansi;
    
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
      $this->Cell(7.5,0.8, 'No. '.$nomorkwitansi,0,0,'R');
      $this->Ln(0.35);
      $this->Cell(1.2,0,'',0,0);
      $this->SetFont('Arial','',9);
      $this->Cell(14.5,0.8,'Jl. Veteran No. 36 Telepon (0353) 3412133 Fax (0353) 3412133',0,0,'L');
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
	$pdf->SetFont('Arial','',9);
  $pdf->SetFillColor(214,235,255);
  $pdf->SetTextColor(0);

  // biodata
	$query = "SELECT a.nNoRM, a.tTglBayar, CONCAT(b.szNama,', ', szTitle) AS nama, CONCAT(IF(szAlamat = '' OR szAlamat IS NULL, '', CONCAT(szAlamat, ', ')), 'RT. ', szRT, ', RW. ', szRW, ', ', szDesa, ', ', szKecamatan, IF(szkota = szKecamatan, '', CONCAT(', ', szkota)), ', ', szPropinsi) AS alamat,
            b.tTglLahir, IF(b.szJenisKelamin = 'L', 'Laki-Laki', 'Perempuan') AS jenisKelamin, a.nEpisode, c.szNamaAlias
            FROM _mutasi_pasien_tindakan a 
            JOIN _pasien b ON b.nNoRM = a.nNoRM
            JOIN _poliklinik c ON c.nKode = a.nKodePoli
            WHERE a.szNoKwitansi = '$nomorkwitansi' LIMIT 1;
          ";
  $conn->query($query);
  $hasil = $conn->result();
  if (!isset($hasil['nNoRM'])) {
    die("Tidak ada hasil");
  }
  $episode = $hasil['nEpisode'];
  $noRM = $hasil['nNoRM'];
  $tanggal = date("d-m-Y", strtotime($hasil['tTglBayar']));
  $nama = $hasil['nama'];
  $alamat = $hasil['alamat'];
  $tanggalLahir = date("d-m-Y", strtotime($hasil['tTglLahir']));
  $jenisKelamin = $hasil['jenisKelamin'];
  $dpjp = $hasil['szNamaAlias'];

	$pdf->Cell(2,0.8,'Nama',0,0,'L');
	$pdf->Cell(0.5,0.8,':',0,0,'C');
	$pdf->Cell(7,0.8,$nama,0,0,'L');
  $pdf->Cell(2,0.8,'No. RM',0,0,'L');
	$pdf->Cell(0.5,0.8,':',0,0,'C');
	$pdf->Cell(7,0.8,$noRM,0,0,'L');
	$pdf->Ln(0.5);
  $pdf->Cell(2,0.8,'Tgl Lahir / JK',0,0,'L');
	$pdf->Cell(0.5,0.8,':',0,0,'C');
	$pdf->Cell(7,0.8,$tanggalLahir . " / " .$jenisKelamin,0,0,'L');
  $pdf->Cell(2,0.8,'Pembiayaan',0,0,'L');
	$pdf->Cell(0.5,0.8,':',0,0,'C');
	$pdf->Cell(7,0.8,'Umum',0,0,'L');
	$pdf->Ln(0.5);
  $pdf->Cell(2,0.8,'DPJP',0,0,'L');
	$pdf->Cell(0.5,0.8,':',0,0,'C');
	$pdf->Cell(7,0.8,$dpjp,0,0,'L');
	$pdf->Ln(0.5);
  $pdf->Cell(2,0.8,'Alamat',0,0,'L');
	$pdf->Cell(0.5,0.8,':',0,0,'C');
	$pdf->Cell(16.5,0.8,$alamat,0,0,'L');
	$pdf->Ln(1);


	$pdf->Cell(1,0.6,'NO','LRTB',0,'C');
	$pdf->Cell(12,0.6,'URAIAN','LRTB',0,'C');
	$pdf->Cell(1,0.6,'QTY','LRTB',0,'C');
	$pdf->Cell(2.5,0.6,'TARIF','LRTB',0,'C');
	$pdf->Cell(2.5,0.6,'BIAYA','LRTB',0,'C');
	$pdf->Ln(0.6);
  $a = 0;
  $totalBiaya = 0;
  // penunjang
  $query = "SELECT 1 AS id, 'PENUNJANG MEDIS' AS nama, '' AS biaya
            UNION
            SELECT 2 AS id, b.szNama AS nama, SUM(IFNULL(a.curJM,0)+IFNULL(a.curJS,0)+IFNULL(a.curBBA,0)+IFNULL(a.curCito,0)) AS biaya
            FROM _mutasi_pasien_tindakan_detil a
            LEFT OUTER JOIN _poliklinik b ON b.nKode = a.nKodePoli
            WHERE a.nEpisode = $episode AND b.nKodeKunjungan = 3
            GROUP BY a.nKode;
          ";
  $conn->query($query);
  $penunjang = $conn->resultSet();
  if(isset($penunjang[1])) {
    foreach ($penunjang as $penunjang) {
      if ($penunjang['nama'] != null){
        if ($penunjang['id'] == 1) {
          $a++;
          $pdf->Cell(1,0.6,$a,'LRTB',0,'C');
          $pdf->Cell(15.5,0.6,$penunjang['nama'],'LRTB',0,'L');
          $pdf->Cell(2.5,0.6,'','LRTB',0,'L');
          $pdf->Ln(0.6);
        } else {
          $totalBiaya += $penunjang['biaya'];
          $pdf->Cell(1,0.6,'','LRTB',0,'C');
          $pdf->Cell(0.5,0.6,'','TB',0,'C');
          $pdf->Cell(15,0.6,$penunjang['nama'],'RTB',0,'L');
          $pdf->Cell(2.5,0.6,number_format($penunjang['biaya']),'LRTB',0,'R');
          $pdf->Ln(0.6);
        }
      }
    }
  }
  // tindakan
  $query = "SELECT 1 AS id, 'TINDAKAN NON OPERATIF' AS nama, '' AS nQtt, '' AS biaya
            UNION
            SELECT 2 AS id, a.szNamaTarifTindakan AS nama, a.nQtt, SUM(IFNULL(a.curJM,0)+IFNULL(a.curJS,0)+IFNULL(a.curBBA,0)+IFNULL(a.curCito,0)) AS biaya
            FROM _mutasi_pasien_tindakan_detil a
            LEFT OUTER JOIN _poliklinik b ON b.nKode = a.nKodePoli
            WHERE a.nEpisode = $episode AND b.nKodeKunjungan = 4
            GROUP BY a.nKode;
          ";
  $conn->query($query);
  $tindakan = $conn->resultSet();
  if(isset($tindakan[1])) {
    foreach ($tindakan as $tindakan) {
      if ($tindakan['nama'] != null){
        if ($tindakan['id'] == 1) {
          $a++;
          $pdf->Cell(1,0.6,$a,'LRTB',0,'C');
          $pdf->Cell(15.5,0.6,$tindakan['nama'],'LRTB',0,'L');
          $pdf->Cell(2.5,0.6,'','LRTB',0,'L');
          $pdf->Ln(0.6);
        } else {
          $totalBiaya += ($tindakan['biaya']*$tindakan['nQtt']);
          $pdf->Cell(1,0.6,'','LRTB',0,'C');
          $pdf->Cell(0.5,0.6,'','TB',0,'C');
          $pdf->Cell(11.5,0.6,$tindakan['nama'],'RTB',0,'L');
          $pdf->Cell(1,0.6,$tindakan['nQtt'],'LRTB',0,'C');
          $pdf->Cell(2.5,0.6,number_format($tindakan['biaya']),'LRTB',0,'R');
          $pdf->Cell(2.5,0.6,number_format($tindakan['biaya'] * $tindakan['nQtt']),'LRTB',0,'R');
          $pdf->Ln(0.6);
        }
      }
    }
  }
  // retribusi
  $query = "SELECT 1 AS id, 'RETRIBUSI' AS nama, '' AS biaya
            UNION
            SELECT 2 AS id, a.szNamaTarif AS nama, SUM(IFNULL(a.curJM,0)+IFNULL(a.curJS,0)+IFNULL(a.curBBA,0)) AS biaya
            FROM _mutasi_keuangan a WHERE a.nEpisode = $episode
            GROUP BY a.nKode;
          ";
  $conn->query($query);
  $retribusi = $conn->resultSet();
  if(isset($retribusi[1])) {
    foreach ($retribusi as $retribusi) {
      if ($retribusi['nama'] != null){
        if ($retribusi['id'] == 1) {
          $a++;
          $pdf->Cell(1,0.6,$a,'LRTB',0,'C');
          $pdf->Cell(15.5,0.6,$retribusi['nama'],'LRTB',0,'L');
          $pdf->Cell(2.5,0.6,'','LRTB',0,'L');
          $pdf->Ln(0.6);
        } else {
          $totalBiaya += $retribusi['biaya'];
          $pdf->Cell(1,0.6,'','LRTB',0,'C');
          $pdf->Cell(0.5,0.6,'','TB',0,'C');
          $pdf->Cell(15,0.6,$retribusi['nama'],'RTB',0,'L');
          $pdf->Cell(2.5,0.6,number_format($retribusi['biaya']),'LRTB',0,'R');
          $pdf->Ln(0.6);
        }
      }
    }
  }
  // farmasi
  $query = "SELECT 'FARMASI' AS nama, SUM(curBayar-IFNULL(curRetur,0)) AS biaya 
            FROM _farmasi_penjualan 
            WHERE nEpisodeMutasiPasien = $episode AND nKodeKategoriPembayaran != 'T' 
            GROUP BY nEpisodeMutasiPasien;
          ";
  $connFarmasi->query($query);
  $farmasi = $connFarmasi->result();
  if (isset($farmasi['nama'])) {
    $a++;
    $totalBiaya += $farmasi['biaya'];
    $pdf->Cell(1,0.6,$a,'LRTB',0,'C');
    $pdf->Cell(15.5,0.6,$farmasi['nama'],'LRTB',0,'L');
    $pdf->Cell(2.5,0.6,number_format($farmasi['biaya']),'LRTB',0,'R');
    $pdf->Ln(0.6);
  }
  
  // total
	$pdf->SetFont('Arial','B',9);
  $pdf->Cell(16.5,0.6,"TOTAL TARIF RUMAH SAKIT",'LRTB',0,'R');
  $pdf->Cell(2.5,0.6,number_format($totalBiaya),'LRTB',0,'R');
  $pdf->Ln(0.6);
  $pdf->Cell(19,0.6,"Terbilang : ".Terbilang($totalBiaya),'LRTB',0,'L');
	$pdf->SetFont('Arial','',8);

          
  // petugas
  $query = "SELECT szNama, nNIP FROM eksekutif_user WHERE nNIK = '$nKodeUser'";
  $conn->query($query);
  $hasil = $conn->result();
  $petugas = $hasil['szNama'];
  $nip = $hasil['nNIP'];
  $pdf->Ln(1);
	$pdf->Cell(10,0,'',0,0);
	$pdf->Cell(9,0.5,'Bojonegoro, '.$tanggal,'',0,'C');
	$pdf->Ln(0.5);
	$pdf->Cell(10,0.5,'','',0,'C');
	$pdf->Cell(9,0.5,'Petugas','',0,'C');
	$pdf->Ln(2);    
	$pdf->SetFont('Arial','BU',8);
	$pdf->Cell(10,0.5,'','',0,'C');
	$pdf->Cell(9,0.5,$petugas,'',0,'C');
	$pdf->Ln(0.5);
	$pdf->SetFont('Arial','',8);	
	$pdf->Cell(10,0.5,'','',0,'C');
	$pdf->Cell(9,0.5, $nip,'',0,'C');

  $pdf->Output();	
?>