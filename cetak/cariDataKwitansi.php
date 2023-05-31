<?php
include("../database/libsServer/adodbcon.php");
include("../database/conString.php");
date_default_timezone_set('Asia/Jakarta');

if (@$_GET['cariKwi']) {
    @$noRM = $_GET['noRM'];
    @$tahunSekarang = $_GET['tahunSekarang'];
    $result = array();

    $queryCariKwi = "SELECT IF(nKodeKunjungan=4,e.nKode,a.nKode) AS nKode,a.tTglBayar,b.szNama, a.szNoKwitansi,b.nKodeKunjungan,e.curBayar,
                    IF(f.szCaraBayar='U','Umum',f.szAsuransi) AS szAsuransi,a.nEpisode,a.nKodeMutasiPasien
                    FROM _mutasi_pasien_tindakan a
                    LEFT OUTER JOIN _poliklinik b ON b.nKode=a.nKodePoli
                    LEFT OUTER JOIN _mutasi_pasien_tindakan_detil d ON d.nKodeMutasiPasienTindakan=a.nKode
                    LEFT OUTER JOIN _mutasi_pendapatan_$tahunSekarang e ON e.nKodeMutasiPasien=a.nKodeMutasiPasien
                    LEFT OUTER JOIN _mutasi_pasien f ON f.nKode=a.nKodeMutasiPasien
                    WHERE a.nNoRM = '$noRM' AND a.nKodeBayar IN ('T','N') AND a.tTglBayar IS NOT NULL
                    AND b.nKodeKunjungan = '4'
                    GROUP BY a.szNoKwitansi
                    ORDER BY tTglBayar DESC;
                  ";

            $hasil = $conn->Execute($queryCariKwi) or die($conn->ErrorMsg());
            $a = 0;
            $hasil->MoveFirst();
            while (!$hasil->EOF) {
                $result[$a]["no"] = $a + 1;
                $result[$a]["nKode"] = $hasil->fields[0];
                $result[$a]["tTglBayar"] = date("d-m-Y H:i:s", strtotime($hasil->fields[1]));
                $result[$a]["szNama"] = $hasil->fields[2];
                $result[$a]["szNoKwitansi"] = $hasil->fields[3];
                $result[$a]["nKodeKunjungan"] = $hasil->fields[4];
                $result[$a]["curBayar"] = $hasil->fields[5];
                $result[$a]["szAsuransi"] = $hasil->fields[6];
                $result[$a]["nEpisode"] = $hasil->fields[7];
                $result[$a]["nKodeMutasiPasien"] = $hasil->fields[8];
                $a++;
                $hasil->MoveNext();
            }

    echo json_encode($result);
}

?>