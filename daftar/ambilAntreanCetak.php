<?php
    include_once("../config.php");
    include_once("../koneksi/koneksi169.php");
    if (!isset($_GET['kodePoli'])) {
      die;
    }
    $kodePoli = $_GET['kodePoli'];
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <!-- Favicon icon -->
    <link href="<?=baseURL?>assets/images/logo-rsud.png" rel="icon">
    <title>Graha Wijaya Kusuma</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?=baseURL?>/assets/vendor/bootstrap/css/bootstrap.min.css">
  </head>
  

  <body>
    <?php 
      $query = "SELECT a.nKode, a.szNama, a.szPrefix, b.nomor, b.sisaAntrean, NOW() AS waktu
                FROM eksekutif_poliklinik a
                LEFT OUTER JOIN (
                  SELECT $kodePoli AS nKodePoli, IF (MAX(x.nNoAntrean) IS NULL, 1, MAX(x.nNoAntrean)+1) AS nomor, COUNT(CASE WHEN nStatus IS NULL OR nStatus = 2 THEN 1 END) AS sisaAntrean
                  FROM (
                    SELECT * FROM eksekutif_antrean WHERE nKodePoli = $kodePoli AND DATE(tJadwal) = DATE(NOW())
                  ) X
                ) b ON b.nKodePoli = a.nKode
                WHERE a.nKode = $kodePoli;
              ";
      $connAntri->query($query);
      $hasil = $connAntri->result();
      if (isset($hasil['nKode'])){
        $namaPoli = strtoupper($hasil['szNama']);
        $prefix = $hasil['szPrefix'];
        $nomor = $hasil['nomor'];
        $nomorAntrean = $prefix."-".str_pad($nomor, 3, '0', STR_PAD_LEFT);
        $sisaAntrean = $hasil['sisaAntrean'];
        $waktuPengambilan = date("d-m-Y H:i:s", strtotime($hasil['waktu']));

        $query = "INSERT INTO eksekutif_antrean (tJadwal, nKodePoli, szPrefix, nNoAntrean) 
                  SELECT * FROM (SELECT NOW(), $kodePoli, '$prefix', $nomor) AS tmp
                  WHERE NOT EXISTS(SELECT nKode FROM eksekutif_antrean WHERE DATE(tJadwal) = DATE(NOW()) AND szPrefix = '$prefix' AND nNoAntrean = $nomor ORDER BY nKode DESC);
                ";
        $connAntri->query($query);
        if ($connAntri->execute()) { 
          echo "<script>window.print(); window.onafterprint = window.close;</script>";
        } else {
          echo "<script>alert('Gagal'); window.close;</script>";
        }
      } else {
        echo "<script>alert('Gagal'); window.close;</script>";
      }
    ?>
    <!-- <div class="d-none d-print-block"> -->
    <div>
      <div class="row text-center">
        <div class="col-12">
          <img src="../assets/images/xx-title.png" class="w-100 mb-3">
        </div>
        <div class="col-12">
          <span class="fw-bold" style="font-size: 25px;"><?= $namaPoli ?></span><br>
          Nomor Antrean Anda :<br>
          <span class="fw-bold" style="font-size: 60px;"><?= $nomorAntrean ?></span><br>
          Sisa Antrean : <span class="fw-bold"><?= $sisaAntrean ?></span><br>
          <?= $waktuPengambilan ?><br>
          ------------------------------------
        </div>
      </div>
    </div>

    <!-- script bootstrap -->
    <script src="<?=baseURL?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  </body>
</html>