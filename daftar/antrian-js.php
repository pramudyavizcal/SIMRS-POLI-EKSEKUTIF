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
  <!-- data table -->
  <link rel="stylesheet" href="<?=baseURL?>assets/vendor/datatables/datatables.min.css">

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
      <h1>Antrean Pasien</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item">Pendaftaran</li>
          <li class="breadcrumb-item active">Antrian</li>
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
                    $queryDokter = "SELECT a.nKode, a.szNama, COUNT(b.nKode) AS jumlahPasien 
                                    FROM eksekutif_poliklinik a 
                                    LEFT OUTER JOIN (
                                      SELECT nKode, nKodePoli, szPrefix, nNoAntrean
                                      FROM eksekutif_antrean
                                      WHERE DATE(tJadwal) = DATE(NOW()) AND (nStatus IS NULL OR nStatus = 2)
                                    ) b ON a.nKode = b.nKodePoli 
                                    GROUP BY a.nKode ORDER BY a.szNama;
                                  ";
                    $connAntri->query($queryDokter);
                    $dokter = $connAntri->resultSet();
                    foreach ($dokter as $dokter) {
                      $kodeJadwal = $dokter['nKode'];
                      $namaDokter = $dokter['szNama'];
                      $jumlahAntrean = $dokter['jumlahPasien'];
                  ?>
                      <div class="col-12 col-md-6 col-lg-3 mb-3" style="min-height:65px">
                        <button type="button" class="btn btn-outline-primary position-relative w-100 h-100 tombolDokter"  id="dokter_<?=$kodeJadwal?>" onclick="cariData(<?=$kodeJadwal?>)">
                          <b><?=$namaDokter?></b>
                          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-1 <?= ($jumlahAntrean == 0) ? 'd-none' : ''; ?>" style="font-size:12px;" id="jumlah_<?=$kodeJadwal?>"><?=$jumlahAntrean?></span>
                        </button> 
                      </div>
                  <?php
                    }
                  ?>
                </div>
              </div>

              <?php
                $nomorDiproses = "";
                $queryProses = "SELECT a.nKodePoli, a.szPrefix, a.nNoAntrean, b.szNama
                                FROM eksekutif_antrean a
                                JOIN eksekutif_poliklinik b ON a.nKodePoli = b.nKode
                                WHERE DATE(tJadwal) = DATE(NOW()) AND a.nNIKUser = '$user_kode' AND a.nStatus IS NULL
                                ORDER BY a.tPanggil DESC LIMIT 1;
                              ";
                $connAntri->query($queryProses);
                $antrianProses = $connAntri->result();
                if (isset($antrianProses['nKodePoli'])) {
                  $kodeDokter = $antrianProses['nKodePoli'];
                  $nomorDiproses = $antrianProses['szPrefix']."-".str_pad($antrianProses['nNoAntrean'], 3, "0", STR_PAD_LEFT);
                  $szNamaDokter = $antrianProses['szNama'];               
                }
              ?>
            </div>
            
            <div class="card-header bg-light border border-start-0 border-end-0 d-none" id="headerListAntrean">
              <h5 class="card-title my-0 py-0">List Antrean</h5>
            </div>
            <div class="card-body pt-4 pb-0 d-none" id="bodyListAntrean">
              <?php if ($nomorDiproses != "") : ?>
                <div class="alert alert-success fade show mb-4" role="alert">
                  Anda sekarang memanggil nomor antrean : <span class="fw-bold"><?= $nomorDiproses ?> - <?= $szNamaDokter ?></span>, Selesaikan antrean tersebut untuk memanggil antrean yang lain<br>
                </div>
              <?php endif; ?>

              <div id="listAntrean"></div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main><!-- End #main -->

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
    var nomorDiproses = "<?= $nomorDiproses ?>";
    var user = "<?= $user_kode ?>";

    $(document).ready(function() {
      // notifikasi
      $("#notificationModal").modal("show");
      // pilihan dokter
      if (nomorDiproses != "") {
        var kodeDokter = "<?= @$kodeDokter ?>";
        cariData(kodeDokter);
      }
    });

    function loading(id) {
      var pesan = "";
      pesan += "<div class='d-flex justify-content-center mb-4'>";
      pesan += "  <div class='spinner-border' role='status'>";
      pesan += "    <span class='visually-hidden'>Loading...</span>";
      pesan += "  </div>";
      pesan += "</div>";
      $(id).html(pesan);
    }

    function pesan(id, isi) {
      var pesan = "";
      pesan += "<div class='alert alert-danger fade show text-center mb-4' role='alert'>";
      pesan += isi;
      pesan += "</div>";
      $(id).html(pesan);
    }
  </script>

  <script>
    function cariData(kodeDokter){
      $("#headerListAntrean").removeClass("d-none");
      $("#bodyListAntrean").removeClass("d-none");
      $(".tombolDokter").removeClass("btn-primary");
      $(".tombolDokter").addClass("btn-outline-primary");
      $("#dokter_"+kodeDokter).removeClass("btn-outline-primary");
      $("#dokter_"+kodeDokter).addClass("btn-primary");  

      loading("#listAntrean");
      $.get("cariData.php?cariAntrean=true&kodeDokter=" + kodeDokter, tampilkanAntrean);
    }

    function tampilkanAntrean(data){
      data = JSON.parse(data);
      var jumlah = data['antrean'].length;
      var kodeDokter = data['kodeDokter'];
      
      var myTable = "";
      var bodyTable = "";
      if (jumlah > 0) {
        $("#jumlah_"+kodeDokter).html(jumlah); 
        for (var i=0; i<jumlah; i++) {
          var no = data['antrean'][i]["no"];
          var nKode = data['antrean'][i]["kode"];
          var nomorAntrian = data['antrean'][i]["nomor"];
          var status = data['antrean'][i]["status"];
          var kodeLoket = data['antrean'][i]["loket"];
          var userLoket = data['antrean'][i]["userLoket"];
          var keterangan = "";
          var tools= "";

          if (kodeLoket == null) {
            if(nomorDiproses == ""){
              tools += "<button class='btn btn-primary mx-1' data-toggle='tooltip' data-placement='top' title='Panggil' onclick='myFunctionPanggil("+nKode+")'><i class='bi bi-volume-up-fill text-white' style='font-size: 15px;'></i></button>";
            }
          } else {
            if(status == null) {
              if(kodeLoket == user) {
                tools += "<button class='btn btn-primary mx-1' data-toggle='tooltip' data-placement='top' title='Panggil Lagi' onclick='myFunctionPanggil("+nKode+")'><i class='bi bi-volume-up-fill text-white' style='font-size: 15px;'></i></button>";
                tools += "<button class='btn btn-success mx-1' data-toggle='tooltip' data-placement='top' title='Daftarkan Pasien' onclick='myFunctionDaftar("+nKode+")'><i class='bi bi-file-earmark-medical-fill text-white' style='font-size: 15px;'></i></button>";
                tools += "<button class='btn btn-dark mx-1' data-toggle='tooltip' data-placement='top' title='Pending' onclick='myFunctionPending("+nKode+")'><i class='bi bi-hourglass-split text-white' style='font-size: 15px;'></i></button>";
                tools += "<button class='btn btn-danger mx-1' data-toggle='tooltip' data-placement='top' title='Batal' onclick='myFunctionBatal("+nKode+")'><i class='bi bi-trash text-white' style='font-size: 15px;'></i></button>";
              } else {
                tools += "Telah dipanggil oleh <b>" + userLoket + "</b>, pelayanan belum selesai";
              }
            } else if (status == 2) {
              keterangan = "Pasien dipending oleh <b>" + userLoket + "</b> karena tidak datang saat dipanggil";
              tools += "<button class='btn btn-success mx-1' data-toggle='tooltip' data-placement='top' title='Daftarkan Pasien' onclick='myFunctionDaftar("+nKode+")'><i class='bi bi-file-earmark-medical-fill text-white' style='font-size: 15px;'></i></button>";
              tools += "<button class='btn btn-danger mx-1' data-toggle='tooltip' data-placement='top' title='Batal' onclick='myFunctionBatal("+nKode+")'><i class='bi bi-trash text-white' style='font-size: 15px;'></i></button>";
            }
          }

          bodyTable += "<tr>";
          bodyTable += "  <td class='text-center align-middle'>" + no + "</td>";
          bodyTable += "  <td class='text-center align-middle'>" + nomorAntrian + "</td>";
          bodyTable += "  <td class='text-center align-middle'>" + keterangan + "</td>";
          bodyTable += "  <td class='text-center align-middle'>" + tools + "</td>";
          bodyTable += "</tr>";
        }  
        myTable += "<div class='table-responsive mb-4'>";
        myTable += "  <table class='table table-bordered table-hover' id='dataTable' width='100%'>";
        myTable += "    <thead class='bg-light'>";
        myTable += "      <tr style='height: 50px;'>";
        myTable += "        <th class='text-center align-middle' width='10%'>NO</th>";
        myTable += "        <th class='text-center align-middle' width='20%'>NO. ANTREAN</th>";
        myTable += "        <th class='text-center align-middle' width='50%'>KETERANGAN</th>";
        myTable += "        <th class='text-center align-middle' width='25%'>TOOLS</th>";
        myTable += "      </tr>";
        myTable += "    </thead>";
        myTable += "    <tbody>";
        myTable += bodyTable;            
        myTable += "    </tbody>";
        myTable += "  </table>";
        myTable += "</div>";

        $("#listAntrean").html(myTable);
        $("#dataTable").DataTable();
      } else {
        $("#jumlah_"+kodeDokter).addClass("d-none"); 
        pesan("#listAntrean", "Tidak ada antrean");
      }
    }

    function myFunctionPanggil(kode) {
      $.get("antrianInput.php?panggilAntrean=true&kode="+kode, aksiLanjut);
    }
    function myFunctionPending(kode) {
      $.get("antrianInput.php?pendingAntrean=true&kode="+kode, aksiLanjut);
    }
    function myFunctionBatal(kode) {
      $.get("antrianInput.php?batalAntrean=true&kode="+kode, aksiLanjut);
    }

    function aksiLanjut(data){
      window.location.reload();
    }

    // daftarkan pasien
    function myFunctionDaftar(kode){
      var form = document.createElement("form");
      var input1 = document.createElement("input"); 
      form.method = "POST";
      form.action = "daftar-js.php";   
      input1.name = "kodeAntrean";
      input1.value = kode;
      form.appendChild(input1);  
      document.body.appendChild(form);
      form.submit();
    }

  </script>

</body>

</html>