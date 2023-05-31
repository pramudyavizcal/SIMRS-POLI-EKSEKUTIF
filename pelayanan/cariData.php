<?php 
  include("../database/libsServer/adodbcon.php"); 
  include("../database/libsFarmasi/adodbcon.php"); 
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
    $episode = $_GET['episode'];

    $result = array();
    $queryDiagnosa = "SELECT a.nKode, a.nKodePoli, a.nKodeMutasiPasien, a.tTimeStamp, IF(a.nKategoriDiagnosa = 'U', 'Utama', 'Penyerta') AS kategori, b.nama, CONCAT(a.nKodeDiagnosa, ' : ', c.penyakit) AS diagnosa, a.tTglVerif 
                      FROM _mutasi_pasien_diagnosa a
                      LEFT OUTER JOIN _pegawai b ON a.nKodeDokter = b.NIP
                      LEFT OUTER JOIN icd c ON a.nKodeDiagnosa = c.id_icd
                      WHERE a.nNoRM = $noRM AND nEpisode = $episode
                      ORDER BY a.tTimeStamp; 
                    ";
    $hasil = $conn->Execute($queryDiagnosa) or die($conn->ErrorMsg());
    $a = 0;
    $hasil->MoveFirst();
    while (!$hasil->EOF) {
      $result[$a]["no"] = $a+1;
      $result[$a]["kodeDiagnosa"] = $hasil->fields[0];
      $result[$a]["kodePoli"] = $hasil->fields[1];
      $result[$a]["kodeMutasiPasien"] = $hasil->fields[2];
      $result[$a]["tanggalDiagnosa"] = $hasil->fields[3];
      $result[$a]["kategoriDiagnosa"] = $hasil->fields[4];
      $result[$a]["dokterDiagnosa"] = $hasil->fields[5];
      $result[$a]["diagnosa"] = $hasil->fields[6];
      $result[$a]["tanggalVerif"] = ($hasil->fields[7] == "") ? "" : $hasil->fields[7];
      $a++;
      $hasil->MoveNext();
    }

    echo json_encode($result);
  }

  // ===========================================================================================
  // Tindakan
  // ==========================================================================================
  if (@$_GET['cariTindakan']) {
    $kodePoli = $_GET['search']; 
 
    // Fetch matched data from the database 
    $result = array();
    $queryICD = "SELECT a.nKodeID, b.szTindakan
                 FROM _tarif_tindakan a
                 LEFT OUTER JOIN _tindakan_master b ON a.nKodeTindakan = b.nKodeTindakan
                 WHERE a.nKodePenunjang = $kodePoli ORDER BY b.szTindakan;
                ";
    $hasil = $conn->Execute($queryICD) or die($conn->ErrorMsg());
    $a = 0;
    $hasil->MoveFirst();
    while (!$hasil->EOF) {
      $result[$a]["id"] = $hasil->fields[0];
      $result[$a]["text"] = $hasil->fields[1];
      $a++;
      $hasil->MoveNext();
    }
    if (count($result) > 0) {
      echo json_encode($result);
    } else {
      echo "Diagnosa tidak ditemukan";
    }
  }

  if (@$_GET['cariTindakanPasien']) {
    $noRM = $_POST['noRM'];
    $episode = $_POST['episode'];

    $result = array();
    $queryUnit = "SELECT a.nKodeMutasiPasien, a.nKodePoli, b.szNama AS szPoli
                  FROM _mutasi_pasien_tindakan_detil a 
                  LEFT OUTER JOIN _poliklinik b ON b.nKode = a.nKodePoli
                  WHERE a. nEpisode = $episode AND a.nNoRM = $noRM 
                  GROUP BY a.nKodeMutasiPasien
                  ORDER BY a.nKodeMutasiPasien;
                ";
    $unit = $conn->Execute($queryUnit) or die($conn->ErrorMsg());
    $a = 0;
    $unit->MoveFirst();
    while (!$unit->EOF) {
      $kodeMutasiPasien = $unit->fields[0];
      $kodeUnit = $unit->fields[1];
      $result[$a]["kodeUnit"] = $unit->fields[1];
      $result[$a]["namaUnit"] = $unit->fields[2];
      // tindakan unit
      $querytindakan = "SELECT a.nKode, a.tTanggal, a.szNamaTarifTindakan, b.nama AS dokter, a.nkodePerawat, a.nQtt, a.nNoRegPoli, a.nKodeMutasiPasien AS nKodeMP, IF(d.nKodeBayar = 'T' AND d.szNoKwitansi IS NOT NULL, 1,0) AS nKodeBayar
                        FROM _mutasi_pasien_tindakan_detil a
                        LEFT OUTER JOIN _pegawai b ON b.NIP = a.nKodeDokter
                        LEFT OUTER JOIN _mutasi_pasien_tindakan d ON d.nKode = a.nKodeMutasiPasienTindakan
                        WHERE a.nNoRM = $noRM  AND a.nEpisode = $episode AND  a.nKodePoli = $kodeUnit
                        ORDER BY tTanggal;
                      ";
      $tindakan = $conn->Execute($querytindakan) or die($conn->ErrorMsg());
      $b = 0;
      $tindakan->MoveFirst();
      while (!$tindakan->EOF) {
        $result[$a]["tindakan"][$b]["no"] = $b;
        $result[$a]["tindakan"][$b]["kodeTindakan"] = $tindakan->fields[0];
        $result[$a]["tindakan"][$b]["tanggalTindakan"] = date("d-m-Y H:i:s", strtotime($tindakan->fields[1]));
        $result[$a]["tindakan"][$b]["namaTindakan"] = $tindakan->fields[2];
        $result[$a]["tindakan"][$b]["dokterTindakan"] = ($tindakan->fields[3] == null) ? "" : $tindakan->fields[3];
        $result[$a]["tindakan"][$b]["perawatTindakan"] = "";
        if ($tindakan->fields[4] != null) {
          $nipPegawai = $tindakan->fields[4];
          $queryPerawat = "SELECT GROUP_CONCAT(nama SEPARATOR '<br>') FROM _pegawai WHERE nip IN ($nipPegawai);";
          $perawat = $conn->Execute($queryPerawat) or die($conn->ErrorMsg());
          $perawat->MoveFirst();
          while (!$perawat->EOF) {
            $result[$a]["tindakan"][$b]["perawatTindakan"] = $perawat->fields[0];
            $perawat->MoveNext();
          }
        }
        $result[$a]["tindakan"][$b]["jumlahTindakan"] = $tindakan->fields[5];
        $result[$a]["tindakan"][$b]["regPoli"] = $tindakan->fields[6];
        $b++;
        $tindakan->MoveNext();
      }
      // end tindakan unit
      $a++;
      $unit->MoveNext();
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
                      WHERE a.nEpisode='$episode' and b.szLinkURL1 IS NOT NULL group by a.nKodeMutasiPasienTindakan) f ON f.nKodeMutasiPasienTindakan=c.nKode
                      WHERE a.nEpisode='$episode' and b.nKodeKunjungan IN (3) AND a.nStatusPoli IS NULL 
                      GROUP BY a.nKode ORDER BY a.tTanggal;
                    ";

    $hasil = $conn->Execute($queryPenunjang) or die($conn->ErrorMsg());
    $a = 0;
    $hasil->MoveFirst();
    while (!$hasil->EOF) {
      $result[$a]["no"] = $a + 1;
      $result[$a]["Idx"] = $hasil->fields[0];
      $result[$a]["nKodeMutasi"] = $hasil->fields[1];
      $result[$a]["tTanggal"] = date("d-m-Y H:i:s", strtotime($hasil->fields[2]));
      $result[$a]["szPenunjang"] = $hasil->fields[3];
      $result[$a]["szKwitansi"] = ($hasil->fields[4] == null) ? "" : $hasil->fields[4];
      $result[$a]["curPenunjang"] = ($hasil->fields[5] == null) ? "" : $hasil->fields[5];
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
  // FARMASI
  // ==========================================================================================
  if (@$_GET['cariFarmasi']) {
    $noRM = $_POST['noRM']; 
    $episode = $_POST['episode']; 
    $result = array();
 
    // Fetch matched data from the database 
    $query = "SELECT a.nKode, a.tTanggal, b.szNama AS unit,a.szNomor AS nomorKwitansi
              FROM _farmasi_penjualan a 
              LEFT OUTER JOIN _poliklinik b ON b.nKode = a.nKodePoli
              WHERE a.nEpisodeMutasiPasien = $episode
              ORDER BY a.tTanggal DESC;
            "; 
    $transaksi = $connFarmasi->Execute($query) or die($conn->ErrorMsg());
    $a = 0;
    $transaksi->MoveFirst();
    while (!$transaksi->EOF) {
      $kodeTransaksi = $transaksi->fields[0];
      $result[$a]["tanggal"] = date("d-m-Y H:i:s", strtotime($transaksi->fields[1]));
      $result[$a]["unit"] = $transaksi->fields[2];
      $result[$a]["noKwitansi"] = $transaksi->fields[3];
      $result[$a]["obat"] = array();

      $query = "SELECT CONCAT(c.szNama,' ',c.szSediaan) AS namaObat,CONCAT(b.nQty-IFNULL(b.nQtyRetur,0),' ',c.szSatuanJual) AS jumlahObat,
                IF(SUBSTRING_INDEX(b.nPagi,'|',-1) != '',SUBSTRING_INDEX(b.nPagi,'|',-1),(IF(SUBSTRING_INDEX(b.nSiang,'|',-1) != '',SUBSTRING_INDEX(b.nSiang,'|',-1),IF(SUBSTRING_INDEX(b.nSore,'|',-1) != '',SUBSTRING_INDEX(b.nSore,'|',-1),SUBSTRING_INDEX(b.nMalam,'|',-1))))) AS szSatuanJual,
                SUBSTRING_INDEX(b.nPagi,'|',1) AS nPagi,SUBSTRING_INDEX(b.nSiang,'|',1) AS nSiang,SUBSTRING_INDEX(b.nSore,'|',1) AS nSore,SUBSTRING_INDEX(b.nMalam,'|',1) AS nMalam,
                SUBSTRING_INDEX(b.szAturanPakai,'|',1),SUBSTRING_INDEX(b.szAturanPakai,'|',-1)
                FROM _farmasi_penjualan a 
                LEFT OUTER JOIN _farmasi_penjualan_item b ON b.nKodePenjualan = a.nKode
                LEFT OUTER JOIN _farmasi_barang c ON c.nKode = b.nKodeBarang
                WHERE a.nEpisodeMutasiPasien = $episode AND a.nKode = $kodeTransaksi
                ORDER BY namaObat;
              ";  
      $obat = $connFarmasi->Execute($query) or die($conn->ErrorMsg());
      $b = 0;
      $obat->MoveFirst();
      while (!$obat->EOF) {
        $result[$a]["obat"][$b]['nama'] = $obat->fields[0];
        $result[$a]["obat"][$b]['jumlah'] = $obat->fields[1];
        $result[$a]["obat"][$b]['aturanPakai'] = "";
        $szSatuanJual = $obat->fields[2];
        $nPagi = $obat->fields[3];
        if($nPagi != ""){
          $nPagi = str_replace('1/2',' &frac12;',$nPagi);
          $nPagi = str_replace('0.5',' &frac12;',$nPagi);
          $nPagi = str_replace('.5',' &frac12;',$nPagi);
          $nPagi = str_replace('0,5',' &frac12;',$nPagi);
          $nPagi = str_replace(',5',' &frac12;',$nPagi);
          $nPagi = str_replace('1/4',' &frac14;',$nPagi);
          $nPagi = str_replace('0.25',' &frac14;',$nPagi);
          $nPagi = str_replace('.25',' &frac14;',$nPagi);
          $nPagi = str_replace('0,25',' &frac14;',$nPagi);
          $nPagi = str_replace(',25',' &frac14;',$nPagi);
          $nPagi = str_replace('3/4',' &frac34;',$nPagi);
          $nPagi = str_replace('0.75',' &frac34;',$nPagi);
          $nPagi = str_replace('.75',' &frac34;',$nPagi);
          $nPagi = str_replace('0,75',' &frac34;',$nPagi);
          $nPagi = str_replace(',75',' &frac34;',$nPagi);
          $nPagi = str_replace(' ',"",$nPagi);
        }
        $nSiang = $obat->fields[4];
        if($nSiang != ""){
          $nSiang = str_replace('1/2',' &frac12;',$nSiang);
          $nSiang = str_replace('0.5',' &frac12;',$nSiang);
          $nSiang = str_replace('.5',' &frac12;',$nSiang);
          $nSiang = str_replace('0,5',' &frac12;',$nSiang);
          $nSiang = str_replace(',5',' &frac12;',$nSiang);
          $nSiang = str_replace('1/4',' &frac14;',$nSiang);
          $nSiang = str_replace('0.25',' &frac14;',$nSiang);
          $nSiang = str_replace('.25',' &frac14;',$nSiang);
          $nSiang = str_replace('0,25',' &frac14;',$nSiang);
          $nSiang = str_replace(',25',' &frac14;',$nSiang);
          $nSiang = str_replace('3/4',' &frac34;',$nSiang);
          $nSiang = str_replace('0.75',' &frac34;',$nSiang);
          $nSiang = str_replace('.75',' &frac34;',$nSiang);
          $nSiang = str_replace('0,75',' &frac34;',$nSiang);
          $nSiang = str_replace(',75',' &frac34;',$nSiang);
          $nSiang = str_replace(' ',"",$nSiang);
        }
        $nSore = $obat->fields[5];
        if($nSore != ""){
          $nSore = str_replace('1/2',' &frac12;',$nSore);
          $nSore = str_replace('0.5',' &frac12;',$nSore);
          $nSore = str_replace('.5',' &frac12;',$nSore);
          $nSore = str_replace('0,5',' &frac12;',$nSore);
          $nSore = str_replace(',5',' &frac12;',$nSore);
          $nSore = str_replace('1/4',' &frac14;',$nSore);
          $nSore = str_replace('0.25',' &frac14;',$nSore);
          $nSore = str_replace('.25',' &frac14;',$nSore);
          $nSore = str_replace('0,25',' &frac14;',$nSore);
          $nSore = str_replace(',25',' &frac14;',$nSore);
          $nSore = str_replace('3/4',' &frac34;',$nSore);
          $nSore = str_replace('0.75',' &frac34;',$nSore);
          $nSore = str_replace('.75',' &frac34;',$nSore);
          $nSore = str_replace('0,75',' &frac34;',$nSore);
          $nSore = str_replace(',75',' &frac34;',$nSore);
          $nSore = str_replace(' ',"",$nSore);
        }
        $nMalam = $obat->fields[6];
        if ($nMalam != ""){
          $nMalam = str_replace('1/2',' &frac12;',$nMalam);
          $nMalam = str_replace('0.5',' &frac12;',$nMalam);
          $nMalam = str_replace('.5',' &frac12;',$nMalam);
          $nMalam = str_replace('0,5',' &frac12;',$nMalam);
          $nMalam = str_replace(',5',' &frac12;',$nMalam);
          $nMalam = str_replace('1/4',' &frac14;',$nMalam);
          $nMalam = str_replace('0.25',' &frac14;',$nMalam);
          $nMalam = str_replace('.25',' &frac14;',$nMalam);
          $nMalam = str_replace('0,25',' &frac14;',$nMalam);
          $nMalam = str_replace(',25',' &frac14;',$nMalam);
          $nMalam = str_replace('3/4',' &frac34;',$nMalam);
          $nMalam = str_replace('0.75',' &frac34;',$nMalam);
          $nMalam = str_replace('.75',' &frac34;',$nMalam);
          $nMalam = str_replace('0,75',' &frac34;',$nMalam);
          $nMalam = str_replace(',75',' &frac34;',$nMalam);
          $nMalam = str_replace(' ',"",$nMalam);
        }
        $szNote = $obat->fields[7];
        $szAturanPakai = $obat->fields[8];
        if($szAturanPakai != ""){ 
          $szAturanPakai = ", " . $szAturanPakai; 
        }
        $aturanPakai = "";
        if($nPagi != ""){
          $aturanPakai = $aturanPakai . "Pagi $nPagi " . $szSatuanJual . "" . $szAturanPakai. "<br>";
        }
        if($nSiang  != ""){
          $aturanPakai = $aturanPakai . "Siang $nSiang " . $szSatuanJual . "" . $szAturanPakai . "<br>";
        }
        if($nSore  != ""){
          $aturanPakai = $aturanPakai . "Sore $nSore " . $szSatuanJual . "" . $szAturanPakai . "<br>";
        }
        if($nMalam  != ""){
          $aturanPakai = $aturanPakai . "Malam $nMalam " . $szSatuanJual . "" . $szAturanPakai . "<br>";
        }
        if($szNote != ""){
          $aturanPakai = $aturanPakai . "[" . $szNote . "]";
        } 
        $result[$a]["obat"][$b]['aturanPakai'] = $aturanPakai;
        $b++;
        $obat->MoveNext();
      }
      $a++;
      $transaksi->MoveNext();
    }
    echo json_encode($result);
  }

  // ===========================================================================================
  // Biaya
  // ===========================================================================================
  if (@$_GET['cariBiaya']){
    $noRM = $_POST['noRM'];
    $episode = $_POST['episode'];
    $result = array();
    $result["tindakan"] = array();
    $result["farmasi"] = array();

    $query = "SELECT if(Idx=8,1,Idx) as Idx,1 as Idz,null as nKodeKunjungan,if(nKodeTindakan IN (1526,1527,1528,2078,690,691,692,693,694,695),nKodeTindakan,3) as Idy,
          if(Idx=2,nKodeTindakan,Idx) as Idm,
          if(Idx IN (1,2,3,4,5),szUraian,(if(Idx=6,'Penunjang Medis',(if(Idx=7,'Tindakan Non Operatif','Administrasi'))))) as szUraian,
          null as tTanggal,null as nQtt,null as curTarif,null as curBiaya,null as nNoRegPoli,null as szKelas,null as szLunas,null as nKodeBayar, null as nKodeMTX, null as nKodeMT from (SELECT if(nKodeTindakan = 2063,1,
          (if(nKodeTindakan IN (1526,1527,1528,2078,690,691,692,693,694,695),2,
          (if(nKodeTindakan IN (2062),3,(if(nKodeTindakan IN (2061),4,(if(nKodeTindakan IN (2057),5,(if(nKodeTindakan IN (2060),8,if(nKodeKunjungan=3,6,7)))))))))))) as Idx,
          if(nKodeTindakan IN (2057,2063,2060,2061,2062,1526,1527,1528,2078,690,691,692,693,694,695),szNamaTarifTindakan,szNama) as szUraian,nKodeTindakan 
          from (SELECT IFNULL(a.nKodeTindakan,c.nKodeTindakan) as nKodeTindakan,b.nKodeKunjungan,a.szNamaTarifTindakan,b.szNama from _mutasi_pasien_tindakan_detil a
          left outer JOIN _poliklinik b ON b.nKode=a.nKodePoli
          left outer JOIN _tarif_tindakan c ON c.nKodeID=a.nKodeTarifTindakan
          where a.nEpisode='$episode'
          UNION
          SELECT '2060' as nKodeTindakan,2 as nKodeKunjungan,szNamaTarif,null as szNama from _mutasi_keuangan a where a.nEpisode='$episode')A)B group by Idx,Idy 
          UNION
          SELECT if(Idx=8,1,Idx) as Idx,Idz,if(nKodeKunjungan=1,2,1) as nKodeKunjungan,if(nKodeKunjungan IN (1,2) and nKodeTindakan NOT IN (2057,2060,2063),nKodeTindakan,3) as Idy,
          if(Idx=2,nKodeTindakan,Idx) as Idm, szUraian,
          if(Idx IN (1,2,3,4,5,7),nKodeMutasiPasien,null) as tTanggal,
          sum(nQtt) as nQtt,
          IFNULL(curJM,0)+IFNULL(curJS,0)+IFNULL(curBBA,0)+IFNULL(curCito,0) as curTarif,
          sum(nQtt*(IFNULL(curJM,0)+IFNULL(curJS,0)+IFNULL(curBBA,0)+IFNULL(curCito,0))) as curBiaya,nNoRegPoli,szKelas,szLunas,nKodeBayar,nKodeMTX,nKodeMT from (SELECT Idx,2 as Idz,
          if(Idx IN (1,5),concat(szNama, ' [',szKelas,'] : ',DATE_FORMAT(tTanggal,'%d-%m-%Y %H:%i:%s'),' ~ ',DATE_FORMAT(IFNULL(tTglKRS,now()),'%d-%m-%Y %H:%i:%s')),
          if(Idx=2,szDokter,(if(Idx=6,concat(szNama,' : ',IFNULL(szNoKwitansi,'')),(if(Idx IN (3,4),szNama,(if(Idx IN (7),concat(szNama,' : ',szNamaTarifTindakan),szNamaTarifTindakan)))))))) as szUraian,nKodeMutasiPasien,
          nQtt,curJM,curJS,curBBA,curCito,nNoRegPoli,szLunas,nKodeTindakan,szKelas,nKodeKunjungan,nKodeBayar,nKodeMTX,nKodeMT 
          from (SELECT if(Idx=99 and nKodeKunjungan=3,6,if(Idx=99 and nKodeKunjungan IN (1,2),7,Idx)) as Idx,
          tTanggal,tTglKRS,szNoKwitansi,szNama,szDokter,szNamaTarifTindakan,
          if(nKodeTindakan IN (2063,2057) and tTimestamp IS NULL,(DATEDIFF(now(),tMRS)+1),nQtt) as nQtt,curJM,curJS,curBBA,curCito,nNoRegPoli,
          if(nKodeBayar IN ('T','N') and szNoKwitansi IS NOT NULL,1,0) as szLunas,nKodeTindakan,nama_kelas as szKelas,nKodeKunjungan,nKodeMutasiPasien,nKodeBayar,nKodeMTX,nKodeMT 
          from (SELECT if(nKodeTindakan=2063,1,(if(nKodeTindakan=2057,5,(if(nKodeTindakan IN (0,2060),8,(if(nKodeTindakan IN (1526,1527,1528,2078,690,691,692,693,694,695),2,
          (if(nKodeTindakan = 2062,3,(if(nKodeTindakan = 2061,4,99))))))))))) as Idx,K.* from(SELECT a.nKode,b.nKodeKunjungan,
          IFNULL(a.nKodeTindakan,e.nKodeTindakan) as nKodeTindakan,d.tTanggal,IFNULL(d.tTglKRS,a.tTimestamp) as tTglKRS,c.szNoKwitansi,b.szNama,a.szNamaTarifTindakan,a.nQtt,a.curJM,a.curJS,a.curBBA,
          a.curCito,if(c.nKodeBayar IN ('T','N') and c.szNoKwitansi IS NOT NULL,c.szNoKwitansi,a.nNoRegPoli) as nNoRegPoli,f.nama as szDokter,a.tTimestamp,c.szCaraBayar,if(b.nKodeKunjungan = 1 and (h.nTitip='' OR h.nTitip IS NULL),g.nama_kelas,if(b.nKodeKunjungan = 1 and h.nTitip='Y',i.nama_kelas,null)) as nama_kelas,
          a.tTanggal as tMRS,d.nKode as nKodeMutasiPasien,c.nKodeBayar,a.nKodeMutasiPasienTindakan as nKodeMTX,c.nKode as nKodeMT
          from _mutasi_pasien_tindakan_detil a 
          left outer JOIN _poliklinik b ON b.nKode=a.nKodePoli
          left outer JOIN _mutasi_pasien_tindakan c ON c.nKode=a.nKodeMutasiPasienTindakan and c.nKodeMutasiPasien=a.nKodeMutasiPasien
          left outer JOIN _mutasi_pasien d ON d.nKode=a.nKodeMutasiPasien
          left outer JOIN _tarif_tindakan e ON e.nKodeID=a.nKodeTarifTindakan
          left outer JOIN _pegawai f ON f.NIP = a.nKodeDokter 
          left outer JOIN _mutasi_pasien_penunjang h ON h.nKodeMutasiPasien=d.nKode
          left outer JOIN _ruang_kelas g ON g.id=h.nKelasRuang  
          left outer JOIN _ruang_kelas i ON i.id=h.nKodeKelasTarif
          where a.nEpisode='$episode'
          UNION
          SELECT a.nKode,3 as nKodeKunjungan,0 as nKodeTindakan,b.tTanggal,b.tTglKRS,b.szSJP,'Administrasi' as szNama,a.szNamaTarif,1 as nQtt,IFNULL(a.curJM,0) as curJM,IFNULL(a.curJS,0) as curJS,IFNULL(a.curBBA,0) as curBBA,
          0 as curCito,b.szSJP,null as szDokter,null as tTimestamp,b.szCaraBayar,null as nama_kelas,null as tMRS,
          null as nKodeMutasiPasien,if(b.szCaraBayar='U' and b.szSJP != '','T','R') as nKodeBayar, null as nKodeMTX, null as nKodeMT
          from _mutasi_keuangan a                                          
          left outer JOIN _mutasi_pasien b ON b.nKode=a.nKodeMutasiPasien
          where a.nEpisode='$episode')K)H)I)J group by Idx,Idz,Idy,szUraian,szKelas,nNoRegPoli
          order by Idx,Idm,nKodeKunjungan,Idz,tTanggal,szLunas desc,szUraian;
            ";
    $hasil = $conn->Execute($query) or die($conn->ErrorMsg());
    $a = 0; 
    $hasil->Movefirst();
    while (!$hasil->EOF){ 
      $result["tindakan"][$a]["Idx"] = $hasil->fields[0]; 
      $result["tindakan"][$a]["Idz"] = $hasil->fields[1]; 
      $result["tindakan"][$a]["Idm"] = $hasil->fields[4];
      $result["tindakan"][$a]["szTarif"] = $hasil->fields[5];
      $result["tindakan"][$a]["nKodeMPX"] = $hasil->fields[6];
      $result["tindakan"][$a]["qty"] = $hasil->fields[7];
      $result["tindakan"][$a]["curTarif"] = $hasil->fields[8];
      $result["tindakan"][$a]["curBiaya"] = $hasil->fields[9];
      $result["tindakan"][$a]["nRegister"] = $hasil->fields[10];
      $result["tindakan"][$a]["nLunas"] = $hasil->fields[11]; //sudah terbayar=1, tagihan=0
      $result["tindakan"][$a]["nKodeBayar"] = $hasil->fields[12];
      $result["tindakan"][$a]["nKodeMTX"] = $hasil->fields[13];
      $result["tindakan"][$a]["nKodeMT"] = $hasil->fields[14];
      $a++;
      $hasil->MoveNext();
    }

    $queryFRS ="SELECT * from (SELECT tTanggal,szNomor,curBayar-IFNULL(curRetur,0) as curFarmasi,
                if(szNoBayar IS NULL,0,1) as nKodeLunas,szNoBayar 
                from _farmasi_penjualan 
                where nEpisodeMutasiPasien='$episode')G where curFarmasi > 0 order by tTanggal;
              ";
      $rsFRS = $connFarmasi->Execute($queryFRS) or die($connFarmasi->ErrorMsg());
      $a= 0;
      $curBayar_F = 0; $curPending_F = 0; $xFarmasi;
      $rsFRS->MoveFirst();
      while (!$rsFRS->EOF) {
        $result["farmasi"][$a]["tFarmasi"] = date('d-m-Y H:i:s',strtotime($rsFRS->fields[0]));
        $result["farmasi"][$a]["szNomorFarmasi"] = $rsFRS->fields[1];
        $result["farmasi"][$a]["curFarmasi"] = $rsFRS->fields[2];
        $result["farmasi"][$a]["nKodeLunasFarmasi"] = $rsFRS->fields[3];
        $result["farmasi"][$a]["szNoBayar"] = $rsFRS->fields[4];
        $a++;
        $rsFRS->MoveNext();
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
