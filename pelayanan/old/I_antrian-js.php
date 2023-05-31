<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Graha Wijaya Kusuma</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="../assets/images/logo-rsud.png" rel="icon">

    <!-- Google Fonts -->
    <link rel="preconnect"href="https://fonts.gstatic.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i">

    <!-- Vendor CSS Files -->
    <link rel="stylesheet" href="../assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/vendor/bootstrap-icons/bootstrap-icons.css">
    <!-- data table -->
    <link rel="stylesheet" href="../assets/vendor/datatables/datatables.min.css">

    <!-- Template Main CSS File -->
    <link rel="stylesheet" href="../assets/css/style.css">
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
        <h1>Daftar Pasien <?= $namaDokterUser ?></h1>
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
              <?php if ($kodeDokterUser == "") : ?>
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
                  $query = "SELECT a.nKode, a.tTanggal, a.nNoAntrianPoli, a.nNoRM, CONCAT(b.szNama, ', ', b.szTitle) AS nama, b.szAlamat, b.szRT, b.szRW, b.szDesa, b.szKecamatan, b.szkota, b.szPropinsi, 
                            a.szKodeDiagUtama, c.nKode, d.tgl_akhir, a.tDokumen, a.tStart, a.tEnd
                            FROM _mutasi_pasien a
                            LEFT OUTER JOIN _pasien b ON a.nNoRM = b.nNoRM
                            LEFT OUTER JOIN _mutasi_pasien_tindakan_detil c ON c.nKodeMutasiPasien = a.nKode
                            LEFT OUTER JOIN _mutasi_penunjang_respon d ON d.nKodeMutasiPasien = a.nKode
                            WHERE a.nPoli = $kodeDokterUser AND a.nStatusPoli = 1;
                          ";
                  $hasil = $conn->Execute($query) or die($conn->ErrorMsg());
                  $a = 0;
                  $hasil->MoveFirst();
                  while (!$hasil->EOF) {
                    $a++;
                    // susun alamat
                    $alamat = $hasil->fields[5];
                    $rt = str_pad($hasil->fields[6], 3, "0", STR_PAD_LEFT);
                    $rw = str_pad($hasil->fields[7], 3, "0", STR_PAD_LEFT);
                    $desa = $hasil->fields[8];
                    $kecamatan = $hasil->fields[9];
                    $kabupaten = $hasil->fields[10];
                    $propinsi = $hasil->fields[11];
                    $alamatLengkap = "";
                    if ($alamat != "") {
                      $alamatLengkap .= "$alamat, ";
                    }
                    $alamatLengkap .= "Ds. $desa, RT. $rt, RW. $rw, Kec. $kecamatan, Kab. $kabupaten, Prop. $propinsi";
                    // susun pasien
                    $pasien[$a]["kodeMutasiPasien"] = $hasil->fields[0];
                    $pasien[$a]["tglDaftar"] = date("d-m-Y H:i:s", strtotime($hasil->fields[1]));
                    $pasien[$a]["noAntrean"] = $hasil->fields[2];
                    $pasien[$a]["noRM"] = $hasil->fields[3];
                    $pasien[$a]["nama"] = $hasil->fields[4];
                    $pasien[$a]["alamatLengkap"] = $alamatLengkap;
                    $pasien[$a]["diagnosa"] = $hasil->fields[12];
                    $pasien[$a]["tindakan"] = $hasil->fields[13];
                    $pasien[$a]["tRespon"] = $hasil->fields[14];
                    $pasien[$a]["tDokumen"] = $hasil->fields[15];
                    $pasien[$a]["tStart"] = $hasil->fields[16];
                    $pasien[$a]["tEnd"] = $hasil->fields[17];
                    $hasil->MoveNext();
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
                              <td class="align-middle fw-bold"><a href="#" class="<?= $color ?>" onclick="perawatanPasien(<?= $pasien[$i]['kodeMutasiPasien'] ?>)"><?= $pasien[$i]["nama"] ?></a></td>
                              <td class="align-middle"><?= $pasien[$i]["alamatLengkap"] ?></td>
                              <td class="text-center align-middle">
                                <!-- dokumen -->
                                <?php if ($pasien[$i]["tDokumen"] == "" ) : ?>
                                  <button class="btn btn-danger mx-1" data-toggle="tooltip" data-placement="top" title="Waktu tunggu dokumen RM" onclick="myFunctionRM(<?= $pasien[$i]['kodeMutasiPasien'] ?>)"><i class="bi bi-clock text-white" style="font-size: 15px;"></i></button>
                                <?php endif; ?>
                                <!-- respon -->
                                <?php if ($pasien[$i]["tRespon"] == "" ) : ?>
                                  <button class="btn btn-dark mx-1" data-toggle="tooltip" data-placement="top" title="Waktu tunggu dokter" onclick="myFunctionDokter(<?= $pasien[$i]['kodeMutasiPasien'] ?>)"><i class="bi bi-clock text-white" style="font-size: 15px;"></i></button>
                                <?php endif; ?>
                                <!-- panggil -->
                                <?php if ($pasien[$i]["tEnd"] == "" ) : ?>
                                  <button class="btn btn-primary mx-1" data-toggle="tooltip" data-placement="top" title="Panggil" onclick="panggilPasien(<?= $pasien[$i]['kodeMutasiPasien'] ?>)"><i class="bi bi-volume-up text-white" style="font-size: 15px;"></i></button>
                                <?php endif; ?>
                                <!-- selesai -->
                                <?php if ($pasien[$i]["tStart"] != "" && $pasien[$i]["tEnd"] == "" ) : ?>
                                  <button class="btn btn-success mx-1" data-toggle="tooltip" data-placement="top" title="Selesai" onclick="selesaiPasien(<?= $pasien[$i]['kodeMutasiPasien'] ?>)"><i class="bi bi-check2-square text-white" style="font-size: 15px;"></i></button>
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
    <!-- End of Footer -->
      
    <!-- bootstrap -->
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- jquery -->
    <script src="../assets/vendor/jQuery/jquery.min.js"></script>
    <!-- datatable -->
    <script src="../assets/vendor/datatables/datatables.min.js"></script>

    <!-- Template Main JS File -->
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/menu-aktif.js"></script>

    <script>
      $("#dataTable").DataTable();
    </script>

    <script>
      function panggilPasien(kodeMutasiPasien){
        $.get("antrianInput.php?panggilPasien=true&kodeMutasiPasien="+kodeMutasiPasien);
        window.location.reload();
      }
      function selesaiPasien(kodeMutasiPasien){
        $.get("antrianInput.php?selesaiPasien=true&kodeMutasiPasien="+kodeMutasiPasien);
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