<?php
  include_once("../config.php");
  include_once("../koneksi/koneksi99.php");
?>

<!DOCTYPE html>
<html dir="ltr" lang="en">

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
    <!-- data table -->
    <link rel="stylesheet" href="<?=baseURL?>assets/vendor/datatables/datatables.min.css">

    <!-- Template Main CSS File -->
    <link rel="stylesheet" href="<?=baseURL?>assets/css/style.css">
  </head>

  <body>
    <!-- Header -->
    <?php include("../header-menu.php") ?>
    <!-- End of Header -->

    <!-- Sidebar -->
    <?php include("../sidebar-menu.php") ?>
    <!-- End of Sidebar -->

    <main id="main" class="main">

      <div class="pagetitle">
        <h1>Daftar Pasien <?= $user_namaDokter ?></h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item">Pelayanan</li>
            <li class="breadcrumb-item active">Pasien</li>
          </ol>
        </nav>
      </div>

      <section class="section">
        <div class="row">
          <div class="col-12">

            <div class="card">
              <?php if ($user_kodeDokter == "") : ?>
                <div class="alert alert-danger fade show m-4" role="alert">
                  Anda tidak bisa mengakses menu ini
                </div>
              <?php else : ?>
                <div class="card-header bg-light">
                  <h5 class="card-title my-0 py-0">Keterangan warna nama pasien</h5>
                </div>
                <div class="card-body py-4">
                  <div class="row g-3">
                    <div class="col-12 col-md-6 col-lg-3">
                      <button class="btn btn-dark w-100 h-100 d-flex justify-content-around py-3">
                        <div><i class="bi bi-check2-square"></i> Diagnosa</div>
                        <div><i class="bi bi-check2-square"></i> Tindakan</div>
                      </button>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                      <button class="btn btn-primary w-100 h-100 d-flex justify-content-around py-3">
                        <div><i class="bi bi-check2-square"></i> Diagnosa</div>
                      </button>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                      <button class="btn btn-success w-100 h-100 d-flex justify-content-around py-3">
                        <div><i class="bi bi-check2-square"></i> Tindakan</div>
                      </button>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                      <button class="btn btn-danger w-100 h-100 d-flex justify-content-around py-3">
                        <div>Menunggu Pelayanan</div>
                      </button>
                    </div>
                  </div>
                </div>

                <?php
                  $pasien = array();
                  $query = "SELECT a.nKode as mutasiPasien, a.tTanggal, a.nNoAntrianPoli, a.nNoRM, CONCAT(b.szNama, ', ', b.szTitle) AS nama, a.szTindakLanjut,
                            CONCAT(IF(szAlamat = '' OR szAlamat IS NULL, '', CONCAT(szAlamat, ', ')), 'RT. ', szRT, ', RW. ', szRW, ', Ds. ', szDesa, ', Kec. ', szKecamatan, ', Kab. ', szkota, ', Prop. ', szPropinsi) AS alamat,
                            a.szKodeDiagUtama, c.nKode as tindakan, d.tgl_akhir, a.tDokumen, a.tStart, a.tEnd
                            FROM _mutasi_pasien a
                            LEFT OUTER JOIN _pasien b ON a.nNoRM = b.nNoRM
                            LEFT OUTER JOIN _mutasi_pasien_tindakan_detil c ON c.nKodeMutasiPasien = a.nKode
                            LEFT OUTER JOIN _mutasi_penunjang_respon d ON d.nKodeMutasiPasien = a.nKode
                            WHERE a.nPoli = $user_kodeDokter AND a.nStatusPoli = 1 AND a.tTglKRS IS NULL
                            GROUP BY a.nKode;
                          ";
                  $conn->query($query);
                  $hasil = $conn->resultSet();
                  $a = 0;
                  foreach ($hasil as $hasil) {
                    $a++;
                    // susun pasien
                    $pasien[$a]["kodeMutasiPasien"] = $hasil['mutasiPasien'];
                    $pasien[$a]["tglDaftar"] = date("d-m-Y H:i:s", strtotime($hasil['tTanggal']));
                    $pasien[$a]["noAntrean"] = $hasil['nNoAntrianPoli'];
                    $pasien[$a]["noRM"] = $hasil['nNoRM'];
                    $pasien[$a]["nama"] = $hasil['nama'];
                    $pasien[$a]["tindakLanjut"] = $hasil['szTindakLanjut'];
                    $pasien[$a]["alamatLengkap"] = $hasil['alamat'];
                    $pasien[$a]["diagnosa"] = $hasil['szKodeDiagUtama'];
                    $pasien[$a]["tindakan"] = $hasil['tindakan'];
                    $pasien[$a]["tRespon"] = $hasil['tgl_akhir'];
                    $pasien[$a]["tDokumen"] = $hasil['tDokumen'];
                    $pasien[$a]["tStart"] = $hasil['tStart'];
                    $pasien[$a]["tEnd"] = $hasil['tEnd'];
                  }
                ?>

                <div class="card-header bg-light border border-start-0 border-end-0">
                  <h5 class="card-title my-0 py-0">Daftar Pasien</h5>
                </div>
                <div class="card-body pt-4 pb-2">
                  <?php if (count($pasien) == 0) : ?>
                    <div class="alert alert-danger fade show" role="alert">
                      tidak ada pasien saat ini
                    </div>
                  <?php else : ?>
                    <div class="table-responsive mb-3">
                      <table class="table table-bordered table-hover" id="dataTable" width="100%">
                        <thead class="bg-light">
                          <tr style="min-height: 50px;">
                            <th class="text-center align-middle" width="5%">NO</th>
                            <th class="text-center align-middle" width="10%">TANGGAL</th>
                            <th class="text-center align-middle" width="7%">NO. Antrean</th>
                            <th class="text-center align-middle" width="7%">NO. RM</th>
                            <th class="text-center align-middle" width="21%">NAMA</th>
                            <th class="text-center align-middle">ALAMAT</th>
                            <th class="text-center align-middle" width="15%">TOOLS</th>
                          </tr>
                        </thead>
                        <tbody>         
                          <?php 
                            for($i=1; $i<=count($pasien); $i++) : 
                              if ($pasien[$i]["tindakan"] == "" && $pasien[$i]["diagnosa"] == "") {
                                $color = "text-danger";
                              } elseif ($pasien[$i]["tindakan"] != "" && $pasien[$i]["diagnosa"] == "") {
                                $color = "text-success";
                              } elseif ($pasien[$i]["tindakan"] == "" && $pasien[$i]["diagnosa"] != "") {
                                $color = "text-primary";
                              } else {
                                $color = "text-dark";
                              }
                          ?>
                            <tr>
                              <td class="text-center align-middle"><?= $i ?></td>
                              <td class="align-middle"><?= $pasien[$i]["tglDaftar"] ?></td>
                              <td class="text-center align-middle"><?= $pasien[$i]["noAntrean"] ?></td>
                              <td class="text-center align-middle"><?= $pasien[$i]["noRM"] ?></td>
                              <td class="align-middle fw-bold">
                                <?php if ($pasien[$i]['tindakLanjut'] == NULL) : ?>
                                  <a href="#" class="<?= $color ?>" onclick="perawatanPasien(<?= $pasien[$i]['kodeMutasiPasien'] ?>)"><?= $pasien[$i]["nama"] ?></a>
                                <?php else : ?>
                                  <?= $pasien[$i]["nama"] ?>
                                  <br>[sudah Tindak Lanjut]
                                <?php endif; ?>
                              </td>
                              <td class="align-middle"><?= $pasien[$i]["alamatLengkap"] ?></td>
                              <td class="text-center align-middle">
                                <!-- dokumen -->
                                <?php if ($pasien[$i]["tDokumen"] == "" ) : ?>
                                  <!-- <button class="btn btn-danger m-1" data-toggle="tooltip" data-placement="top" title="Waktu tunggu dokumen RM" onclick="myFunctionRM(<?= $pasien[$i]['kodeMutasiPasien'] ?>)"><i class="bi bi-clock text-white" style="font-size: 15px;"></i></button> -->
                                <?php endif; ?>
                                <!-- respon -->
                                <?php if ($pasien[$i]["tRespon"] == "" ) : ?>
                                  <!-- <button class="btn btn-dark mx-1" data-toggle="tooltip" data-placement="top" title="Waktu tunggu dokter" onclick="myFunctionDokter(<?= $pasien[$i]['kodeMutasiPasien'] ?>)"><i class="bi bi-clock text-white" style="font-size: 15px;"></i></button> -->
                                <?php endif; ?>
                                <!-- panggil -->
                                <?php if ($pasien[$i]["tEnd"] == "" ) : ?>
                                  <!-- <button class="btn btn-primary m-1" data-toggle="tooltip" data-placement="top" title="Panggil" onclick="panggilPasien(<?= $pasien[$i]['kodeMutasiPasien'] ?>)"><i class="bi bi-volume-up text-white" style="font-size: 15px;"></i></button> -->
                                <?php endif; ?>
                                <!-- selesai -->
                                <?php if ($pasien[$i]["tStart"] != "" && $pasien[$i]["tEnd"] == "" ) : ?>
                                  <!-- <button class="btn btn-success m-1" data-toggle="tooltip" data-placement="top" title="Selesai" onclick="selesaiPasien(<?= $pasien[$i]['kodeMutasiPasien'] ?>)"><i class="bi bi-check2-square text-white" style="font-size: 15px;"></i></button> -->
                                <?php endif; ?>
                              </td>
                            </tr>
                          <?php endfor; ?>
                        </tbody>
                      </table>
                    </div>
                  <?php endif; ?>

                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </section>
    </main>
    <!-- End #main -->

    <!-- Footer -->
    <?php include("../footer-menu.php") ?>
    <?php include("../notifikasi.php") ?>

    <!-- End of Footer -->
      
    <!-- bootstrap -->
    <script src="<?=baseURL?>assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- jquery -->
    <script src="<?=baseURL?>assets/vendor/jQuery/jquery.min.js"></script>
    <!-- datatable -->
    <script src="<?=baseURL?>assets/vendor/datatables/datatables.min.js"></script>

    <!-- Template Main JS File -->
    <script src="<?=baseURL?>assets/js/main.js"></script>
    <script src="<?=baseURL?>assets/js/menu-aktif.js"></script>
    
    <script>
      $(document).ready(function(){
        $("#notificationModal").modal("show");
        $("#dataTable").DataTable();
      });
    </script>

    <script>
      function myFunctionRM() {
        alert("masih dibuat");
      }
      function myFunctionDokter() {
        alert("masih dibuat");
      }

      function panggilPasien(kodeMutasiPasien){
        $.get("antrianInput.php?panggilPasien=true&kodeMutasiPasien="+kodeMutasiPasien, feedback);
      }

      function selesaiPasien(kodeMutasiPasien){
        $.get("antrianInput.php?selesaiPasien=true&kodeMutasiPasien="+kodeMutasiPasien, feedback);
      }

      function feedback(data) {
        window.location.reload();
      }

      function perawatanPasien(kodeMutasiPasien){
        var form = document.createElement("form");
        form.action = "I_pasien-js.php";   
        form.method = "POST";
        var input1 = document.createElement("input"); 
        input1.name = "kodeMutasiPasien";
        input1.value = kodeMutasiPasien;
        form.appendChild(input1);  
        document.body.appendChild(form);
        form.submit();
      }
    </script>
  </body>

</html>