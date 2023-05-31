<?php
  include_once("../config.php");
  include_once("../koneksi/koneksi169.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Graha Wijaya Kusuma</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="<?=baseURL?>assets/images/logo-rsud.png" rel="icon">

  <!-- Google Fonts -->
  <link rel="preconnect"href="https://fonts.gstatic.com">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i">

  <!-- Vendor CSS Files -->
  <link rel="stylesheet" href="<?=baseURL?>assets/vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?=baseURL?>assets/vendor/bootstrap-icons/bootstrap-icons.css">

  <!-- Template Main CSS File -->
  <link rel="stylesheet" href="<?=baseURL?>assets/css/style.css">
</head>

<body>

  <!-- Header -->
  <?php  include_once("../header-menu.php"); ?>
  <!-- End of Header -->

  <!-- Sidebar -->
  <?php include("../sidebar-menu.php"); ?>
  <!-- End of Sidebar -->
  
  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Ambil Antrean Pasien</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item">Pendaftaran</li>
          <li class="breadcrumb-item active">Ambil Antrean</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-12">

          <div class="card">
            <div class="card-header bg-light">
              <h5 class="card-title my-0 py-0">List Dokter</h5>
            </div>
            <div class="card-body pt-4 pb-2">
              <div id="listDokter">
                <div class="row">
                  <?php
                    $queryDokter = "SELECT nKode, szNama FROM eksekutif_poliklinik ORDER BY szNama;";
                    $connAntri->query($queryDokter);
                    $dokter = $connAntri->resultSet();
                    foreach ($dokter as $dokter) {
                      $kodePoli = $dokter['nKode'];
                      $namaDokter = $dokter['szNama'];
                  ?>
                      <div class="col-12 col-md-6 col-lg-3 mb-3" style="min-height:65px">
                        <button type="button" class="btn btn-outline-primary position-relative w-100 h-100 tombolDokter"  id="dokter_<?=$kodePoli?>" onclick="cetakNomor(<?=$kodePoli?>)">
                          <b><?=$namaDokter?></b>
                        </button> 
                      </div>
                  <?php
                    }
                  ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main><!-- End #main -->

  <!-- Footer -->
  <?php include("../footer-menu.php") ?>
  <!-- End of Footer -->

  <!-- bootstrap -->
  <script src="<?=baseURL?>assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- jquery -->
  <script src="<?=baseURL?>assets/vendor/jQuery/jquery.min.js"></script>

  <!-- Template Main JS File -->
  <script src="<?=baseURL?>assets/js/main.js"></script>
  <script src="<?=baseURL?>assets/js/menu-aktif.js"></script>

  <script>
    function cetakNomor(kodePoli) {
      window.open("ambilAntreanCetak.php?kodePoli="+ kodePoli, "_blank");
    }
  </script>
</body>

</html>