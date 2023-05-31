<?php
  include_once("../config.php");
  include_once("../koneksi/koneksi169.php");

  if (!isset($_POST["kodeAntrean"])) {
    header("Location: antrian-js.php");
    exit;
  }
  $kodeAntrean = $_POST["kodeAntrean"];
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
  <?php include("../header-menu.php"); ?>
  <!-- End of Header -->

  <!-- Sidebar -->
  <?php include("../sidebar-menu.php"); ?>
  <!-- End of Sidebar -->
  
  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Pendaftaran Pasien</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item">Pendaftaran</li>
          <li class="breadcrumb-item">Antrian</li>
          <li class="breadcrumb-item active">Daftar</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <?php 
      $query = "SELECT a.szPrefix, a.nNoAntrean, b.nKode, b.nNIP, b.szNama
                FROM eksekutif_antrean a
                JOIN eksekutif_poliklinik b ON a.nKodePoli = b.nKode
                WHERE a.nKode = $kodeAntrean;
              ";
      $connAntri->query($query);
      $antrian = $connAntri->result();
      $prefixAntrean = $antrian['szPrefix'];
      $nomorAntrean = $antrian['nNoAntrean'];
      $kodeDokter = $antrian['nKode'];
      $NIPDokter = $antrian['nNIP'];
      $namaDokter = $antrian['szNama'];
    ?>

    <section class="section">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-light">
              <h5 class="card-title my-0 py-0">Form Pendaftaran Pasien</h5>
            </div>
            <div class="card-body py-0">
              <div class="row mt-3 mb-5">
                <div class="col-12 d-block d-md-none text-center align-self-end text-danger" style="font-size:40px;">
                  <h1 class="fw-bold"><?= $prefixAntrean." - ".str_pad($nomorAntrean, 3, '0', STR_PAD_LEFT) ?></h1>
                </div>
                <div class="col-12 col-md-4 col-lg-3 mb-3 mb-md-0">
                  <label for="nNoRM" class="form-label">No RM</label>
                  <input type="text" class="form-control" id="nNoRM" name="nNoRM">
                </div>
                <div class="col-12 col-md-3 col-lg-2 align-self-end">
                  <button class="btn btn-primary w-100" type="submit" onclick="cariPasien()">Cari</button>
                </div>
                <div class="col-12 col-md-5 col-lg-7 d-none d-md-block text-center fw-bold align-self-end text-danger" style="font-size:40px;">
                  <?= $prefixAntrean." - ".str_pad($nomorAntrean, 3, '0', STR_PAD_LEFT) ?>
                </div>
                <div class="col-12" id="errorMessage"></div>
              </div>

              <form class="g-3 needs-validation mb-4" action="daftarInput.php?daftarkanPasien=true" method="POST" novalidate>
                <!-- data hidden -->
                <div class="row">
                  <div class="col-12 d-none">
                    <input type="text" class="form-control" id="kodeAntrean" name="kodeAntrean" value="<?= $kodeAntrean ?>" readonly>
                    <input type="text" class="form-control" id="nomorAntrean" name="nomorAntrean" value="<?= $nomorAntrean ?>" readonly>
                    <input type="text" class="form-control" id="NIPDokter" name="NIPDokter" value="<?= $NIPDokter ?>" readonly>
                    <input type="text" class="form-control" id="noRM" name="noRM" readonly>
                    <input type="text" class="form-control" id="kodeUmur" name="kodeUmur" readonly>
                  </div>
                </div>
                <!-- data show -->
                <div class="row">
                  <label for="nama" class="col-3 col-lg-1 col-form-label">Nama</label>
                  <div class="col-9 col-lg-5 mb-3">
                    <input type="text" class="form-control" id="nama" name="nama" readonly>
                  </div>
                  <label for="jenisKelamin" class="col-3 col-lg-1 col-form-label">Jenis kelamin</label>
                  <div class="col-9 col-lg-5 mb-3">
                    <select class="form-select" id="jenisKelamin" name="jenisKelamin" readonly>
                      <option value="" selected>jenis Kelamin...</option>
                      <option value="L">Laki-Laki</option>
                      <option value="P">Perempuan</option>
                    </select>
                  </div>
                </div>
                <div class="row">
                  <label for="tglLahir" class="col-3 col-lg-1 col-form-label">Tanggal lahir</label>
                  <div class="col-9 col-lg-5 mb-3">
                    <input type="date" class="form-control" id="tglLahir" name="tglLahir" readonly>
                  </div>
                  <label for="umur" class="col-3 col-lg-1 col-form-label">Umur</label>
                  <div class="col-9 col-lg-5 mb-3">
                    <input type="text" class="form-control" id="umur" name="umur" readonly>
                  </div>
                </div>
                <div class="row">
                  <label for="alamatLengkap" class="col-3 col-lg-1 col-form-label">Alamat</label>
                  <div class="col-9 col-lg-5 mb-3">
                    <textarea class="form-control" style="height: 100px" id="alamatLengkap" name="alamatLengkap" readonly></textarea>
                  </div>
                </div>
                <div class="row">
                  <label for="dokter" class="col-3 col-lg-1 col-form-label">Dokter</label>
                  <div class="col-9 col-lg-5 mb-3">
                    <select class="form-select" id="dokter" name="dokter" readonly>
                      <option selected value="<?=$kodeDokter?>"><?=$namaDokter?></option>
                    </select>
                  </div>
                  <label for="biaya" class="col-3 col-lg-1 col-form-label">Biaya</label>
                  <div class="col-9 col-lg-5 mb-3">
                    <input type="text" class="form-control" id="biaya" name="biaya" readonly>
                  </div>
                </div>
                <div class="row">
                  <div class="col-12 col-md-3 col-lg-2 mt-4 ms-auto">
                    <button class="btn btn-primary w-100" type="submit" id="tombolDaftar" disabled>Daftar</button>
                  </div>
                </div>
              </form>
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

    function notifikasi(id, pesan) {
      var error = "";
      error += "<div class='alert alert-danger alert-dismissible fade show mt-3 mb-0' role='alert'>";
      error += pesan;
      error += "  <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>";
      error += "</div>";
      $(id).html(error);
    }

    function loading(element){
      var pesan = "";
      pesan += "<div class='d-flex justify-content-center mt-3'>";
      pesan += "<div class='spinner-grow text-danger mx-1' role='status'></div>";
      pesan += "<div class='spinner-grow text-warning mx-1' role='status'></div>";
      pesan += "<div class='spinner-grow text-success mx-1' role='status'></div>";
      pesan += "<div class='spinner-grow text-primary mx-1' role='status'></div>";
      pesan += "<div class='spinner-grow text-secondary mx-1' role='status'></div>";
      pesan += "</div>";
      $(element).html(pesan);
    }

    function cariPasien(){
      loading("#errorMessage");
      if ($("#nNoRM").val() == "") {
        notifikasi("#errorMessage", "Masukan No RM terlebih dulu");
        kosongiForm();
      } else {
        $.ajax({
          url: 'cariData.php?cariRM=true',
          type: 'POST',
          data: {
            noRM: $("#nNoRM").val()
          },
          success: function(data) {
            $("#errorMessage").html("");
            data = JSON.parse(data);
            if (data["pesanError"] != "") {
              notifikasi("#errorMessage", data['pesanError']);
              kosongiForm();
            } else {
              $("#errorMessage").html('');
              $("#noRM").val(data["noRM"]);
              $("#nama").val(data["nama"]);
              $("#jenisKelamin").val(data["jenisKelamin"]);
              $("#tglLahir").val(data["tglLahir"]);
              $("#umur").val(data["umur"]);
              $("#alamatLengkap").val(data["alamatLengkap"]);
              $("#kodeUmur").val(data["kodeUmur"]);
              data["biaya"] = data["biaya"].toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
              $("#biaya").val(data["biaya"]);
              validasiForm();
            }
          }               
        });
      }
    }

    function kosongiForm(){
      $("#nNoRM").val('');
      $("#noRM").val('');
      $("#kodeUMur").val('');
      $("#nama").val('');
      $("#jenisKelamin").val('');
      $("#tglLahir").val('');
      $("#umur").val('');
      $("#alamatLengkap").val('');
      $("#kodeUmur").val('');
      $("#biaya").val('');
      validasiForm();
    }

    function validasiForm(){
      var noRM = $("#noRM").val();
      var nama = $("#nama").val();
      var jenisKelamin = $("#jenisKelamin").val();
      var tglLahir = $("#tglLahir").val();
      var umur = $("#umur").val();
      var alamatLengkap = $("#alamatLengkap").val();
      var dokter = $("#dokter").val();
      var biaya = $("#biaya").val();

      if (nama!="" && jenisKelamin!="" && tglLahir!="" && umur!="" && alamatLengkap!="" && dokter!="" && biaya!="") {
        $("#tombolDaftar").prop('disabled', false);
      } else {
        $("#tombolDaftar").prop('disabled', true);
      }
    }
    
  </script>

</body>

</html>