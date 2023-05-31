<?php
include("../database/libsServer/adodbcon.php");
include("../database/libsFarmasi/adodbcon.php");
date_default_timezone_set('Asia/Jakarta');

if (@$_GET['cariTagihanPasien']) {
    $episode = $_GET['episode'];
    @$noRM = $_GET['noRM'];
    $result = array();



    $queryRincianTagihan = "SELECT Idx,1 AS Idz,idx AS Idm,
                            IF(Idx=6,'Penunjang Medis',(IF(Idx=7,'Tindakan Non Operatif','Administrasi'))) AS szUraian,
                            NULL AS tTanggal,NULL AS nQtt,NULL AS curTarif,NULL AS curBiaya,NULL AS nNoRegPoli FROM (SELECT IF(nKodeTindakan = 2063,1,
                            (IF(nKodeTindakan IN (1526,1527,1528,2078,690,691,692,693,694,695),2,
                            (IF(nKodeTindakan IN (2062),3,(IF(nKodeTindakan IN (2061),4,(IF(nKodeTindakan IN (2057),5,(IF(nKodeTindakan IN (2060),8,IF(nKodeKunjungan=3,6,7)))))))))))) AS Idx,
                            IF(nKodeTindakan IN (2057,2063,2060,2061,2062,1526,1527,1528,2078,690,691,692,693,694,695),szNamaTarifTindakan,szNama) AS szUraian,nKodeTindakan 
                            FROM (SELECT IFNULL(a.nKodeTindakan,c.nKodeTindakan) AS nKodeTindakan,b.nKodeKunjungan,a.szNamaTarifTindakan,b.szNama FROM _mutasi_pasien_tindakan_detil a
                            LEFT OUTER JOIN _poliklinik b ON b.nKode=a.nKodePoli
                            LEFT OUTER JOIN _tarif_tindakan c ON c.nKodeID=a.nKodeTarifTindakan
                            WHERE a.nEpisode='$episode'
                            UNION
                            SELECT '2060' AS nKodeTindakan,2 AS nKodeKunjungan,szNamaTarif,NULL AS szNama FROM _mutasi_keuangan a WHERE a.nEpisode='$episode')A)B GROUP BY Idx
                            UNION
                            SELECT Idx,Idz,idx AS Idm,szUraian,
                            IF(Idx IN (7),nKodeMutasiPasien,NULL) AS tTanggal,
                            IF(Idx IN (7),SUM(nQtt),NULL) AS nQtt,
                            IF(Idx IN (7),IFNULL(curJM,0)+IFNULL(curJS,0)+IFNULL(curBBA,0)+IFNULL(curCito,0),NULL) AS curTarif,
                            SUM(nQtt*(IFNULL(curJM,0)+IFNULL(curJS,0)+IFNULL(curBBA,0)+IFNULL(curCito,0))) AS curBiaya,nNoRegPoli FROM (SELECT Idx,2 AS Idz,
                            IF(Idx IN (1,5),CONCAT(szNama,DATE_FORMAT(tTanggal,'%d-%m-%Y %H:%i:%s'),' ~ ',DATE_FORMAT(IFNULL(tTglKRS,NOW()),'%d-%m-%Y %H:%i:%s')),
                            IF(Idx=2,szDokter,(IF(Idx=6,CONCAT(szNama,IFNULL(szNoKwitansi,'')),(IF(Idx IN (3,4),szNama,(IF(Idx IN (7),CONCAT(szNamaTarifTindakan),szNamaTarifTindakan)))))))) AS szUraian,nKodeMutasiPasien,
                            nQtt,curJM,curJS,curBBA,curCito,nNoRegPoli
                            FROM (SELECT IF(Idx=99 AND nKodeKunjungan=3,6,IF(Idx=99 AND nKodeKunjungan IN (4),7,Idx)) AS Idx,
                            tTanggal,tTglKRS,szNoKwitansi,szNama,szDokter,szNamaTarifTindakan,
                            IF(nKodeTindakan IN (2063,2057) AND tTimestamp IS NULL,(DATEDIFF(NOW(),tMRS)+1),nQtt) AS nQtt,curJM,curJS,curBBA,curCito,nNoRegPoli,
                            nKodeMutasiPasien,nKodeBayar,nKodeMTX,nKodeMT 
                            FROM (SELECT IF(nKodeTindakan=2063,1,(IF(nKodeTindakan=2057,5,(IF(nKodeTindakan IN (0,2060),8,(IF(nKodeTindakan IN (1526,1527,1528,2078,690,691,692,693,694,695),2,
                            (IF(nKodeTindakan = 2062,3,(IF(nKodeTindakan = 2061,4,99))))))))))) AS Idx,K.* FROM(SELECT a.nKode,b.nKodeKunjungan,
                            IFNULL(a.nKodeTindakan,e.nKodeTindakan) AS nKodeTindakan,d.tTanggal,IFNULL(d.tTglKRS,a.tTimestamp) AS tTglKRS,c.szNoKwitansi,b.szNama,a.szNamaTarifTindakan,a.nQtt,a.curJM,a.curJS,a.curBBA,
                            a.curCito,IF(c.nKodeBayar IN ('T','N') AND c.szNoKwitansi IS NOT NULL,c.szNoKwitansi,a.nNoRegPoli) AS nNoRegPoli,f.nama AS szDokter,a.tTimestamp,c.szCaraBayar,
                            a.tTanggal AS tMRS,d.nKode AS nKodeMutasiPasien,c.nKodeBayar,a.nKodeMutasiPasienTindakan AS nKodeMTX,c.nKode AS nKodeMT
                            FROM _mutasi_pasien_tindakan_detil a 
                            LEFT OUTER JOIN _poliklinik b ON b.nKode=a.nKodePoli
                            LEFT OUTER JOIN _mutasi_pasien_tindakan c ON c.nKode=a.nKodeMutasiPasienTindakan AND c.nKodeMutasiPasien=a.nKodeMutasiPasien
                            LEFT OUTER JOIN _mutasi_pasien d ON d.nKode=a.nKodeMutasiPasien
                            LEFT OUTER JOIN _tarif_tindakan e ON e.nKodeID=a.nKodeTarifTindakan
                            LEFT OUTER JOIN _pegawai f ON f.NIP = a.nKodeDokter 
                            LEFT OUTER JOIN _mutasi_pasien_penunjang h ON h.nKodeMutasiPasien=d.nKode
                            WHERE a.nEpisode='$episode' AND nNoRegPoli IS NOT NULL
                            UNION
                            SELECT a.nKode,3 AS nKodeKunjungan,0 AS nKodeTindakan,b.tTanggal,b.tTglKRS,b.szSJP,'Administrasi' AS szNama,a.szNamaTarif,1 AS nQtt,IFNULL(a.curJM,0) AS curJM,IFNULL(a.curJS,0) AS curJS,IFNULL(a.curBBA,0) AS curBBA,
                            0 AS curCito,b.szSJP,NULL AS szDokter,NULL AS tTimestamp,b.szCaraBayar,NULL AS tMRS,
                            NULL AS nKodeMutasiPasien,IF(b.szCaraBayar='U' AND b.szSJP != '','T','R') AS nKodeBayar, NULL AS nKodeMTX, NULL AS nKodeMT
                            FROM _mutasi_keuangan a                                          
                            LEFT OUTER JOIN _mutasi_pasien b ON b.nKode=a.nKodeMutasiPasien
                            WHERE a.nEpisode='$episode')K)H)I)J GROUP BY Idx,Idz,szUraian,nNoRegPoli
                            ORDER BY Idx,Idm,Idz,tTanggal DESC,szUraian;
                            ";

    $hasil = $conn->Execute($queryRincianTagihan) or die($conn->ErrorMsg());
    $a = 0;
    $hasil->MoveFirst();
    while (!$hasil->EOF) {
        $result[$a]["no"] = $a + 1;
        $result[$a]["Idz"] = $hasil->fields[1];
        $result[$a]["szTarif"] = $hasil->fields[3];
        $result[$a]["qty"] = $hasil->fields[5];
        $result[$a]["curTarif"] = $hasil->fields[6];
        $result[$a]["curBiaya"] = $hasil->fields[7];
       
        $a++;
        $hasil->MoveNext();
    }

    $queryRincianTagihanFarmasi = "SELECT SUM(CASE WHEN nKodeLunas=1 THEN curFarmasi ELSE 0 END) as curLunas,
    SUM(CASE WHEN nKodeLunas=0 THEN curFarmasi ELSE 0 END) as curBayar
    from (SELECT tTanggal,szNomor,curBayar-IFNULL(curRetur,0) as curFarmasi,
    if(szNoBayar IS NULL,0,1) as nKodeLunas,szNoBayar 
    from _farmasi_penjualan where nEpisodeMutasiPasien='$episode')G where curFarmasi > 0 order by tTanggal";
    $hasil = $connFarmasi->Execute($queryRincianTagihanFarmasi) or die($connFarmasi->ErrorMsg());
    $a = 0;
    $hasil->MoveFirst();
    while (!$hasil->EOF) {
        $result[$a]["no"] = $a + 1;
        $result[$a]["curLunas"] = $hasil->fields[0];
        $result[$a]["curBayar"] = $hasil->fields[1];
        $a++;
        $hasil->MoveNext();
    }

    $queryDataPembayaran = " SELECT m.szCaraBayar,m.nKode,m.nEpisode,m.nPoli,(SELECT YEAR(NOW())) AS tahun
		FROM _pasien p
		LEFT OUTER JOIN (SELECT a.szSJP,a.nNoRM,a.nPoli,a.tTanggal,a.tTglKRS,a.tSelesai,a.szTindakLanjut,a.nEpisode,a.nKode,a.szCaraBayar,
		IF(a.szCaraBayar='U','Umum',a.szAsuransi) AS szAsuransi
		FROM _mutasi_pasien a 
        LEFT OUTER JOIN _poliklinik b ON b.nKode=a.nPoli
		LEFT OUTER JOIN _mutasi_pasien_tindakan c ON c.nKodeMutasiPasien = a.nKode
		WHERE tTglKRS IS NULL AND szTindakLanjut IS NULL AND nNIKOlehLoket = 'administrasi' AND c.tTglBayar IS NULL GROUP BY nKode) m ON m.nNoRM=p.nNoRM
		LEFT OUTER JOIN _poliklinik b ON b.nKode=m.nPoli
		WHERE p.nNoRM = '$noRM';
        ";
        $hasil = $conn->Execute($queryDataPembayaran) or die($conn->ErrorMsg());
        $a = 0;
        $hasil->MoveFirst();
        while (!$hasil->EOF) {
            $result[$a]["no"] = $a + 1;
            $result[$a]["szCaraBayar"] = $hasil->fields[0];
            $result[$a]["nKode"] = $hasil->fields[1];
            $result[$a]["nEpisode"] = $hasil->fields[2];
            $result[$a]["nPoli"] = $hasil->fields[3];
            $result[$a]["tahun"] = $hasil->fields[4];
            $a++;
            $hasil->MoveNext();
    }

    echo json_encode($result);
}

// if (@$_GET['cariTagihanFarmasi']) {
//     $episode = $_GET['episode'];
//     $result = array();
//     $queryRincianTagihanFarmasi = "SELECT SUM(CASE WHEN nKodeLunas=1 THEN curFarmasi ELSE 0 END) as curLunas,
//     SUM(CASE WHEN nKodeLunas=0 THEN curFarmasi ELSE 0 END) as curBayar
//     from (SELECT tTanggal,szNomor,curBayar-IFNULL(curRetur,0) as curFarmasi,
//     if(szNoBayar IS NULL,0,1) as nKodeLunas,szNoBayar 
//     from _farmasi_penjualan where nEpisodeMutasiPasien='$episode')G where curFarmasi > 0 order by tTanggal";
//     $hasil = $connFarmasi->Execute($queryRincianTagihanFarmasi) or die($connFarmasi->ErrorMsg());
//     $a = 0;
//     $hasil->MoveFirst();
//     while (!$hasil->EOF) {
//         $result[$a]["no"] = $a + 1;
//         $result[$a]["curLunas"] = $hasil->fields[0];
//         $result[$a]["curBayar"] = $hasil->fields[1];
//         $a++;
//         $hasil->MoveNext();
//     }
//     echo json_encode($result);
// }


?>