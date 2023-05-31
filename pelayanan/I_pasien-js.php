<?php
  include_once("../config.php");
  include_once("../koneksi/koneksi99.php");
  if (!isset($_POST["kodeMutasiPasien"])) {
    header("Location: I_antrian-js.php");
    exit;
  }
  $kodeMutasiPasien = $_POST["kodeMutasiPasien"];
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
    <!-- select2 -->
    <link rel="stylesheet" href="<?=baseURL?>assets/vendor/select2/css/select2.min.css">
    <link rel="stylesheet" href="<?=baseURL?>assets/vendor/select2/css/select2-bootstrap-5-theme.min.css">
    <!-- multi select -->
    <link rel="stylesheet" href="<?=baseURL?>assets/vendor/selectPicker/css/bootstrap-select.min.css" integrity="sha512-mR/b5Y7FRsKqrYZou7uysnOdCIJib/7r5QeJMFvLNHNhtye3xJp1TdJVPLtetkukFn227nKpXD9OjUc09lx97Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />    
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
    
    <!-- bootstrap -->
    <script src="<?=baseURL?>assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- jquery -->
    <script src="<?=baseURL?>assets/vendor/jQuery/jquery.min.js"></script>
    <!-- datatable -->
    <script src="<?=baseURL?>assets/vendor/datatables/datatables.min.js"></script>
    <!-- select 2 -->
    <script src="<?=baseURL?>assets/vendor/select2/js/select2.min.js"></script>
    <!-- multi select -->
    <script src="<?=baseURL?>assets/vendor/selectPicker/js/bootstrap-select.min.js"></script>
    <!-- Template Main JS File -->
    <script src="<?=baseURL?>assets/js/main.js"></script>
    <script src="<?=baseURL?>assets/js/menu-aktif.js"></script>

    <main id="main" class="main">

      <div class="pagetitle">
        <h1>Detail Perawatan Pasien <?= $user_namaDokter ?></h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item">Pelayanan</li>
            <li class="breadcrumb-item">Pasien</li>
            <li class="breadcrumb-item active">Detail Perawatan</li>
          </ol>
        </nav>
      </div>

      <section class="section">
        <div class="row">
          <div class="col-12">

            <div class="card">
              <div class="card-header bg-light">
                <h5 class="card-title my-0 py-0">Biodata Pasien</h5>
              </div>
              <div class="card-body pt-4 pb-2">
                <?php
                  $pasien = array();
                  $queryPasien = "SELECT a.tTanggal, a.nEpisode, a.szSJP, a.nNoRM, CONCAT(b.szNama,', ',b.szTitle) AS szNama, b.nID, b.szTempatLahir, b.tTglLahir, IF(b.szJenisKelamin='L','Laki-laki','Perempuan') AS szJenisKelamin,
                                  CONCAT(IF(b.szAlamat = '' OR b.szAlamat IS NULL, '', CONCAT(b.szAlamat, ', ')), 'RT. ', b.szRT, ', RW. ', b.szRW, ', Ds. ', b.szDesa, ', Kec. ', b.szKecamatan, ', Kab. ', b.szkota, ', Prop. ', b.szPropinsi) AS Alamat
                                  FROM _mutasi_pasien a
                                  LEFT OUTER JOIN _pasien b ON a.nNoRM = b.nNoRM
                                  WHERE a.nKode = $kodeMutasiPasien;
                                ";
                  $conn->query($queryPasien);
                  $hasil = $conn->resultSet();
                  foreach ($hasil as $hasil) {
                    // hitung umur
                    $bday = new DateTime($hasil['tTglLahir']);
                    $today = new Datetime(date("Y-m-d"));
                    $diff = $today->diff($bday);
                    // susun pasien
                    $pasien["tanggal"] =  date("d-m-Y H:i:s", strtotime($hasil['tTanggal']));
                    $pasien["episode"] = $hasil['nEpisode'];
                    $pasien["SJP"] = $hasil['szSJP'];
                    $pasien["noRM"] = $hasil['nNoRM'];
                    $pasien["nama"] = $hasil['szNama'];
                    $pasien["NIK"] = $hasil['nID'];
                    $pasien["tempatLahir"] = $hasil['szTempatLahir'];
                    $pasien["tglLahir"] = date("d-m-Y", strtotime($hasil['tTglLahir']));
                    $pasien["jenisKelamin"] = $hasil['szJenisKelamin'];
                    $pasien["umur"] = "$diff->y Tahun $diff->m Bulan $diff->d Hari";
                    $pasien["alamatLengkap"] = $hasil['Alamat'];
                  }

                  $queryDashboard = "SELECT b.NIP, b.nama, a.szKodeDiagUtama, c.penyakit, a.tTanggal, a.tDokumen, a.tPanggil
                                      FROM _mutasi_pasien a
                                      LEFT OUTER JOIN _pegawai b ON a.nKodeDokter = b.NIP
                                      LEFT OUTER JOIN icd c ON a.szKodeDiagUtama = c.id_icd
                                      WHERE a.nKode = $kodeMutasiPasien;
                                    ";
                  $conn->query($queryDashboard);
                  $hasil = $conn->resultSet();
                  foreach ($hasil as $hasil) {
                    $NIPDPJP = $hasil['NIP'];
                    $namaDPJP = $hasil['nama'];
                    $kodeDiagnosa = $hasil['szKodeDiagUtama'];
                    $namaDiagnosa = $hasil['penyakit'];
                    $tDaftar = $hasil['tTanggal'];
                    $tDokumen = ($hasil['tDokumen'] == "") ? date("Y-m-d H:i:s") : $hasil['tDokumen'];
                    $tPelayanan = ($hasil['tPanggil'] == "") ? date("Y-m-d H:i:s") : $hasil['tPanggil'];
                    // hitung waktu
                    $waktuDaftar = new DateTime($tDaftar);
                    // waktu dokumen
                    $waktuDokumen = new DateTime($tDokumen);
                    $tungguDokumen = $waktuDaftar->diff($waktuDokumen);
                    $totalTungguDokumen = "$tungguDokumen->h Jam, $tungguDokumen->i Menit, $tungguDokumen->s Detik";
                    // waktu pelayanan
                    $waktuPelayanan = new DateTime($tPelayanan);
                    $tungguPelayanan = $waktuDaftar->diff($waktuPelayanan);
                    $totalTungguPelayanan = "$tungguPelayanan->h Jam, $tungguPelayanan->i Menit, $tungguPelayanan->s Detik";
                  }
                ?>
                <div class="row d-flex align-items-center justify-content-between mb-3">
                  <div class="col-12 col-md-6 mb-3">
                    <h2 class="fw-bold text-danger m-0"><?=  $pasien["noRM"] ?> - <?= $pasien["nama"] ?></h2>
                  </div>
                  <div class="col-12 col-md-6 col-lg-4 mb-3 text-end">
                    <button type="button" class="btn btn-outline-primary btn-lg h-100 w-100 py-3">
                        <h4 class="fw-bold m-0" >Perkiraan Biaya : Rp. <span id="perkiraanBiaya"></span></h4>
                    </button> 
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="row mb-3">
                      <div class="col-4 col-lg-3 d-flex justify-content-between"><span>Tanggal Masuk</span><span>:</span></div>
                      <div class="col-8 col-lg-9"><?= $pasien["tanggal"] ?></div>
                    </div>
                    <div class="row mb-3">
                      <div class="col-4 col-lg-3 d-flex justify-content-between"><span>NIK</span><span>:</span></div>
                      <div class="col-8 col-lg-9"><?= $pasien["NIK"] ?></div>
                    </div>
                    <div class="row mb-3">
                      <div class="col-4 col-lg-3 d-flex justify-content-between"><span>Pembiayaan</span><span>:</span></div>
                      <div class="col-8 col-lg-9">Umum</div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="row mb-3">
                      <div class="col-4 d-flex justify-content-between"><span>Tempat, Tanggal Lahir</span><span>:</span></div>
                      <div class="col-8"><?= $pasien["tempatLahir"] ?>, <?= $pasien["tglLahir"] ?></div>
                    </div>
                    <div class="row mb-3">
                      <div class="col-4 d-flex justify-content-between"><span>Jenis Kelamin / Umur</span><span>:</span></div>
                      <div class="col-8"><?= $pasien["jenisKelamin"] ?> / <?= $pasien["umur"] ?></div>
                    </div>
                    <div class="row mb-3">
                      <div class="col-4 d-flex justify-content-between"><span>Alamat</span><span>:</span></div>
                      <div class="col-8"><?= $pasien["alamatLengkap"] ?></div>
                    </div>
                  </div>
                </div>
              </div>


              <div class="card-header bg-light border border-start-0 border-end-0 p-0">
                <nav>
                  <div class="nav nav-pills nav-fill" id="nav-tab" role="tablist">
                    <button class="nav-link rounded rounded-0 fw-semibold py-3 active" id="nav-dashboard-tab" data-bs-toggle="tab" data-bs-target="#nav-dashboard" type="button" role="tab" aria-controls="nav-dashboard" aria-selected="true" onclick="dashboardPasien()">Dashboard</button>
                    <button class="nav-link rounded rounded-0 fw-semibold py-3" id="nav-dpjp-diagnosa-tab" data-bs-toggle="tab" data-bs-target="#nav-dpjp-diagnosa" type="button" role="tab" aria-controls="nav-dpjp-diagnosa" aria-selected="false" onclick="diagnosaPasien()">DPJP & Diagnosa</button>
                    <button class="nav-link rounded rounded-0 fw-semibold py-3" id="nav-tindakan-tab" data-bs-toggle="tab" data-bs-target="#nav-tindakan" type="button" role="tab" aria-controls="nav-tindakan" aria-selected="false" onclick="tindakanPasien()">Tindakan</button>
                    <button class="nav-link rounded rounded-0 fw-semibold py-3" id="nav-penunjangMedis-tab" data-bs-toggle="tab" data-bs-target="#nav-penunjangMedis" type="button" role="tab" aria-controls="nav-penunjangMedis" aria-selected="false" onclick="penunjangPasien()">Penunjang Medis</button>
                    <button class="nav-link rounded rounded-0 fw-semibold py-3" id="nav-farmasi-tab" data-bs-toggle="tab" data-bs-target="#nav-farmasi" type="button" role="tab" aria-controls="nav-farmasi" aria-selected="false" onclick="farmasiPasien()">Farmasi</button>
                    <button class="nav-link rounded rounded-0 fw-semibold py-3" id="nav-biaya-tab" data-bs-toggle="tab" data-bs-target="#nav-biaya" type="button" role="tab" aria-controls="nav-biaya" aria-selected="false" onclick="biayaPasien()">Biaya</button>
                    <button class="nav-link rounded rounded-0 fw-semibold py-3" id="nav-tindakLanjut-tab" data-bs-toggle="tab" data-bs-target="#nav-tindakLanjut" type="button" role="tab" aria-controls="nav-tindakLanjut" aria-selected="false">Tindak Lanjut</button>
                    <button class="nav-link rounded rounded-0 fw-semibold py-3" id="nav-riwayat-tab" data-bs-toggle="tab" data-bs-target="#nav-riwayat" type="button" role="tab" aria-controls="nav-riwayat" aria-selected="false" onclick="riwayatPasien()">Riwayat</button>
                    <button class="nav-link rounded rounded-0 fw-semibold py-3" id="nav-dataInduk-tab" data-bs-toggle="tab" data-bs-target="#nav-dataInduk" type="button" role="tab" aria-controls="nav-dataInduk" aria-selected="false" onclick="dataIndukPasien()">Data Induk</button>
                  </div>
                </nav>
              </div>
              <div class="card-body pt-4 pb-0">
                <div id="tab-notifikasi"></div>
                <div class="tab-content" id="nav-tabContent">
                  <!-- dashboard -->
                  <div class="tab-pane fade show active" id="nav-dashboard" role="tabpanel" aria-labelledby="nav-dashboard-tab" tabindex="0">
                    
                    <div class="row">
                      <div class="col-12 col-md-6 col-lg-4">
                        <div class="card border border-2 rounded rounded-3 border-primary">
                          <div class="card-header bg-primary fw-bold text-center text-white">
                            DPJP Utama
                          </div>
                          <div class="card-body p-0 d-flex align-items-center">
                            <button type="button" class="btn btn-outline-primary w-100 h-100 border border-0 rounded-0" style="min-height:150px;">
                              <span class="fw-bold" style="font-size:30px;"><?= $namaDPJP ?></span>
                            </button> 
                          </div>
                        </div>
                      </div>
                      <div class="col-12 col-md-6 col-lg-4">
                        <div class="card border border-2 rounded rounded-3 border-success">
                          <div class="card-header bg-success fw-bold text-center text-white">
                            Diagnosa Utama
                          </div>
                          <div class="card-body p-0 d-flex align-items-center">
                            <button type="button" class="btn btn-outline-success w-100 h-100 border border-0 rounded-0" style="min-height:150px;">
                              <span class="fw-bold" style="font-size:30px;"><?= ($kodeDiagnosa == "") ? "" : $kodeDiagnosa.' : '.$namaDiagnosa ?></span>
                            </button> 
                          </div>
                        </div>
                      </div>
                      <div class="col-12 col-md-6 col-lg-4">
                        <div class="card border border-2 rounded rounded-3 border-info">
                          <div class="card-header bg-info fw-bold text-center text-white">
                            Biaya
                          </div>
                          <div class="card-body p-0 d-flex align-items-center">
                            <button type="button" class="btn btn-outline-info w-100 h-100 border border-0 rounded-0" style="min-height:150px;">
                              <span class="fw-bold" style="font-size:30px;"><span id="biaya"></span></span>
                            </button> 
                          </div>
                        </div>
                      </div>
                      <div class="col-12 col-md-6 col-lg-4">
                        <div class="card border border-2 rounded rounded-3 border-danger">
                          <div class="card-header bg-danger fw-bold text-center text-white">
                            Alergi
                          </div>
                          <div class="card-body p-0 d-flex align-items-center">
                            <button type="button" class="btn btn-outline-danger w-100 h-100 border border-0 rounded-0" style="min-height:150px;">
                              <span class="fw-bold" style="font-size:30px;"></span>
                            </button> 
                          </div>
                        </div>
                      </div>
                      <div class="col-12 col-md-6 col-lg-4">
                        <div class="card border border-2 rounded rounded-3 border-secondary">
                          <div class="card-header bg-secondary fw-bold text-center text-white">
                            Waktu Tunggu Dokumen
                          </div>
                          <div class="card-body p-0 d-flex align-items-center">
                            <button type="button" class="btn btn-outline-secondary w-100 h-100 border border-0 rounded-0" style="min-height:150px;">
                              <span class="fw-bold" style="font-size:30px;"><?= $totalTungguDokumen ?></span>
                            </button> 
                          </div>
                        </div>
                      </div>
                      <div class="col-12 col-md-6 col-lg-4">
                        <div class="card border border-2 rounded rounded-3 border-dark">
                          <div class="card-header bg-dark fw-bold text-center text-white">
                            Waktu Tunggu Pelayanan
                          </div>
                          <div class="card-body p-0 d-flex align-items-center">
                            <button type="button" class="btn btn-outline-dark w-100 h-100 border border-0 rounded-0" style="min-height:150px;">
                              <span class="fw-bold" style="font-size:30px;"><?= $totalTungguPelayanan ?></span>
                            </button> 
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- =========================================================================================== -->
                  <!-- DPJP & diagnosa -->
                  <!-- =========================================================================================== -->
                  <div class="tab-pane fade" id="nav-dpjp-diagnosa" role="tabpanel" aria-labelledby="nav-dpjp-diagnosa-tab" tabindex="0">
                    <?php include_once("I_pasien_diagnosa-js.php"); ?>
                  </div>


                  <!-- =========================================================================================== -->
                  <!-- Tindakan -->
                  <!-- =========================================================================================== -->
                  <div class="tab-pane fade" id="nav-tindakan" role="tabpanel" aria-labelledby="nav-tindakan-tab" tabindex="0">
                    <?php include_once("I_pasien_tindakan-js.php"); ?>
                  </div>

                  <!-- =========================================================================================== -->
                  <!-- Penunjang Medis -->
                  <!-- =========================================================================================== -->
                  <div class="tab-pane fade" id="nav-penunjangMedis" role="tabpanel" aria-labelledby="nav-penunjangMedis-tab" tabindex="0">
                    <?php include_once("I_pasien_penunjangMedis-js.php"); ?>
                  </div>

                  <!-- =========================================================================================== -->
                  <!-- Farmasi -->
                  <!-- =========================================================================================== -->
                  <div class="tab-pane fade" id="nav-farmasi" role="tabpanel" aria-labelledby="nav-farmasi-tab" tabindex="0">
                    <?php include_once("I_pasien_farmasi-js.php"); ?>
                  </div>

                  <!-- =========================================================================================== -->
                  <!-- Biaya -->
                  <!-- =========================================================================================== -->
                  <div class="tab-pane fade" id="nav-biaya" role="tabpanel" aria-labelledby="nav-biaya-tab" tabindex="0">
                    <?php include_once("I_pasien_biaya-js.php"); ?>
                  </div>

                  <!-- =========================================================================================== -->
                  <!-- Tindak Lanjut -->
                  <!-- =========================================================================================== -->
                  <div class="tab-pane fade" id="nav-tindakLanjut" role="tabpanel" aria-labelledby="nav-tindakLanjut-tab" tabindex="0">
                    <?php include_once("I_pasien_tindakLanjut-js.php"); ?>
                  </div>
                  <!-- =========================================================================================== -->
                  <!-- Riwayat Kunjungan -->
                  <!-- =========================================================================================== -->
                  <div class="tab-pane fade" id="nav-riwayat" role="tabpanel" aria-labelledby="nav-riwayat-tab" tabindex="0">
                    <?php include_once("I_pasien_riwayat-js.php"); ?>
                  </div>
                  
                  <!-- =========================================================================================== -->
                  <!-- Data Induk -->
                  <!-- =========================================================================================== -->
                  <div class="tab-pane fade" id="nav-dataInduk" role="tabpanel" aria-labelledby="nav-dataInduk-tab" tabindex="0">
                    <?php include_once("I_pasien_dataInduk-js.php"); ?>
                  </div>
                </div>
              </div>
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
    
    <!-- modal password -->
    <div class="modal fade" id="modalKonfirmasiAksi" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalKonfirmasiAksiLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-12">
                <label for="passwordAksi" class="form-label">Masukkan Password</label>
                <input type="password" class="form-control" id="passwordAksi">
                <span style="cursor:pointer;" onclick="showPassword('passwordAksi')"><input type="checkbox" id="passwordAksiCheck" class="mt-3 me-2">Tampilkan password</span>
              </div>
              <div class="col-12" id="pesanAutentikasiAksi"></div>
              <div class="col-6">
                <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Batal</button>
              </div>
              <div class="col-6">
                <button type="button" class="btn btn-primary w-100" id="tombolAutentikasiAksi" disabled>Simpan</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script>
      $(document).ready(function(){
        $("#notificationModal").modal("show");
        totalPerkiraanBiaya();
      });

      $(document).on('select2:open', () => {
        document.querySelector('.select2-search__field').focus();
      });

      function loading(element){
        var pesan = "";
        pesan += "<div class='d-flex justify-content-center mb-4'>";
        pesan += "<div class='spinner-grow text-danger mx-1' role='status'></div>";
        pesan += "<div class='spinner-grow text-warning mx-1' role='status'></div>";
        pesan += "<div class='spinner-grow text-success mx-1' role='status'></div>";
        pesan += "<div class='spinner-grow text-primary mx-1' role='status'></div>";
        pesan += "<div class='spinner-grow text-secondary mx-1' role='status'></div>";
        pesan += "</div>";
        $(element).html(pesan);
      }

      function notifikasi(element, kode, isiPesan) {
        var bg = "danger";
        if (kode == 1) {
          bg = "success";
        }
        var pesan = "";
        pesan += "<div class='alert alert-"+bg+" alert-dismissible mb-4 fade show text-center' role='alert'>";
        pesan += isiPesan;
        if (element == "#tab-notifikasi") {
          pesan += "  <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>";
        }
        pesan += "</div>";
        $(element).html(pesan);
      }

      // cek password
      $("#passwordAksi").change(function(){
        var password = '<?= $user_password ?>';
        var passwordInput = $("#passwordAksi").val();
        if (passwordInput == "") {
          notifikasi("#pesanAutentikasiAksi", 0, "Masukkan password");
          $("#tombolAutentikasiAksi").prop("disabled", true);
        } else if (passwordInput != password) {
          notifikasi("#pesanAutentikasiAksi", 0, "Password salah");
          $("#tombolAutentikasiAksi").prop("disabled", true);
        }else {
          $("#pesanAutentikasiAksi").html("");
          $("#tombolAutentikasiAksi").prop("disabled", false);
        }
      });

      function showPassword(element){
        if ($("#"+element+"Check").prop('checked')) {
          $("#"+element+"Check").prop('checked', false);
          $("#"+element).prop("type", "password");
        } else {
          $("#"+element+"Check").prop('checked', true);
          $("#"+element).prop("type", "text");
        }
      }

      function dashboardPasien(){
        $("#tab-notifikasi").html("");
      }
      function totalPerkiraanBiaya(){
        var totalBiaya = 0;
        $.ajax({
          url: 'cariData.php?cariBiaya=true',
          type: 'POST',
          data: {
            noRM:  <?= $pasien["noRM"] ?>,
            episode: <?= $pasien["episode"] ?>
          },
          success: function(data) {
            data = JSON.parse(data);
            var jumlahTindakan = data["tindakan"].length;
            var jumlahFarmasi = data["farmasi"].length;
            
          
            if (jumlahTindakan + jumlahFarmasi > 0) {
              // bagian tindakan
              var totalTindakan = 0;
              for (var i = 0; i < jumlahTindakan; i++) {
                var tindakan = data["tindakan"][i];
                if(tindakan["curBiaya"] == null){
                  tindakan["curBiaya"] = 0;
                }
                totalTindakan += parseInt(tindakan["curBiaya"]);
              }
              //Bagian Farmasi
              var totalFarmasi = 0;
              for (var i = 0; i < jumlahFarmasi; i++) {
                var farmasi = data["farmasi"][i];
                if(farmasi["curFarmasi"] == null){
                  farmasi["curFarmasi"] = 0;
                }
                totalFarmasi += parseInt(farmasi["curFarmasi"]);
              }

              // total biaya
              $("#perkiraanBiaya").html((totalTindakan+totalFarmasi).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
              $("#biaya").html("Rp. "+(totalTindakan+totalFarmasi).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
            }  
          }         
        }); 
      }
      
    </script>

  </body>

</html>