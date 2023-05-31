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
    <link href="<?=baseURL?>/assets/images/logo-rsud.png" rel="icon">

    <!-- Google Fonts -->
    <link rel="preconnect"href="https://fonts.gstatic.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i">

    <!-- Vendor CSS Files -->
    <link rel="stylesheet" href="<?=baseURL?>/assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?=baseURL?>/assets/vendor/bootstrap-icons/bootstrap-icons.css">
    <!-- data table -->
    <link rel="stylesheet" href="<?=baseURL?>/assets/vendor/datatables/datatables.min.css">
    <!-- select2 -->
    <link rel="stylesheet" href="<?=baseURL?>/assets/vendor/select2/css/select2.min.css">
    <link rel="stylesheet" href="<?=baseURL?>/assets/vendor/select2/css/select2-bootstrap-5-theme.min.css">
    <!-- Template Main CSS File -->
    <link rel="stylesheet" href="<?=baseURL?>/assets/css/style.css">
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
        <h1>Detail Perawatan Pasien</h1>
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

                  $queryDashboard = "SELECT b.nNIP, b.szNama, a.szKodeDiagUtama, c.penyakit, a.tTanggal, a.tDokumen, a.tPanggil
                                      FROM _mutasi_pasien a
                                      LEFT OUTER JOIN _dokter_perawat b ON a.nKodeDokter = b.nNIP
                                      LEFT OUTER JOIN icd c ON a.szKodeDiagUtama = c.id_icd
                                      WHERE a.nKode = $kodeMutasiPasien;
                                    ";
                  $conn->query($queryDashboard);
                  $hasil = $conn->resultSet();
                  foreach ($hasil as $hasil) {
                    $NIPDPJP = $hasil['nNIP'];
                    $namaDPJP = $hasil['szNama'];
                    $kodeDiagnosa = $hasil['szKodeDiagUtama'];
                    $namaDiagnosa = $hasil['penyakit'];
                    $biaya = "Rp. ". number_format(50000);
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
                        <h4 class="fw-bold m-0">Perkiraan Biaya : <?=$biaya?></h4>
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
                      <div class="col-8 col-lg-9">Umum [ SEP. <?= ($pasien["SJP"] == "") ? "-" : $pasien["SJP"] ?> ]</div>
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
                    <button class="nav-link rounded rounded-0 fw-semibold py-3 active" id="nav-dashboard-tab" data-bs-toggle="tab" data-bs-target="#nav-dashboard" type="button" role="tab" aria-controls="nav-dashboard" aria-selected="true">Dashboard</button>
                    <button class="nav-link rounded rounded-0 fw-semibold py-3" id="nav-dpjp-diagnosa-tab" data-bs-toggle="tab" data-bs-target="#nav-dpjp-diagnosa" type="button" role="tab" aria-controls="nav-dpjp-diagnosa" aria-selected="false" onclick="diagnosaPasien()">DPJP & Diagnosa</button>
                    <button class="nav-link rounded rounded-0 fw-semibold py-3" id="nav-tindakan-tab" data-bs-toggle="tab" data-bs-target="#nav-tindakan" type="button" role="tab" aria-controls="nav-tindakan" aria-selected="false" onclick="tindakanPasien()">Tindakan</button>
                    <button class="nav-link rounded rounded-0 fw-semibold py-3" id="nav-penunjangMedis-tab" data-bs-toggle="tab" data-bs-target="#nav-penunjangMedis" type="button" role="tab" aria-controls="nav-penunjangMedis" aria-selected="false" onclick="penunjangPasien()">Penunjang Medis</button>
                    <button class="nav-link rounded rounded-0 fw-semibold py-3" id="nav-farmasi-tab" data-bs-toggle="tab" data-bs-target="#nav-farmasi" type="button" role="tab" aria-controls="nav-farmasi" aria-selected="false">Farmasi</button>
                    <button class="nav-link rounded rounded-0 fw-semibold py-3" id="nav-biaya-tab" data-bs-toggle="tab" data-bs-target="#nav-biaya" type="button" role="tab" aria-controls="nav-biaya" aria-selected="false">Biaya</button>
                    <button class="nav-link rounded rounded-0 fw-semibold py-3" id="nav-tindakLanjut-tab" data-bs-toggle="tab" data-bs-target="#nav-tindakLanjut" type="button" role="tab" aria-controls="nav-tindakLanjut" aria-selected="false">Tindak Lanjut</button>
                    <button class="nav-link rounded rounded-0 fw-semibold py-3" id="nav-riwayat-tab" data-bs-toggle="tab" data-bs-target="#nav-riwayat" type="button" role="tab" aria-controls="nav-riwayat" aria-selected="false" onclick="riwayatPasien()">Riwayat</button>
                    <button class="nav-link rounded rounded-0 fw-semibold py-3" id="nav-dataInduk-tab" data-bs-toggle="tab" data-bs-target="#nav-dataInduk" type="button" role="tab" aria-controls="nav-dataInduk" aria-selected="false" onclick="dataIndukPasien()">Data Induk</button>
                  </div>
                </nav>
              </div>
              <div class="card-body pt-4 pb-0">
                <div class="tab-notifikasi">

                </div>
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
                              <span class="fw-bold" style="font-size:30px;"><?=$biaya?></span>
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
                    <div class="row g-3 mb-4">
                      <div class="col-md-6 col-lg-4">
                        <label for="dokterDiagnosa" class="form-label">DPJP</label>
                        <select class="form-select formDiagnosa" id="dokterDiagnosa" name="dokterDiagnosa">
                          <option value="<?=$NIPDPJP?>" selected><?=$namaDPJP?></option>
                        </select>
                      </div>
                      <div class="col-md-6 col-lg-3">
                        <label for="kategoriDiagnosa" class="form-label">Kategori Diagnosa</label>
                        <select class="form-select formDiagnosa" id="kategoriDiagnosa" name="kategoriDiagnosa"></select>
                      </div>
                      <div class="col-md-9 col-lg-4">
                        <label for="kodeDiagnosa" class="form-label">Diagnosa</label>
                        <select class="form-select formDiagnosa" name="kodeDiagnosa" id="kodeDiagnosa"></select>                       
                      </div>
                      <div class="col-md-3 col-lg-1 align-self-end">
                        <button class="btn btn-primary w-100" type="submit" id="tombolTambahDiagnosa" onclick="konfirmasiTambahDiagnosa()" disabled>Tambah</button>
                      </div>
                    </div>

                    <div id="daftarDiagnosa" class="mb-4"></div>
                  </div>


                  <!-- =========================================================================================== -->
                  <!-- Tindakan -->
                  <!-- =========================================================================================== -->
                  <div class="tab-pane fade" id="nav-tindakan" role="tabpanel" aria-labelledby="nav-tindakan-tab" tabindex="0">
                  <div class="row g-3 mb-4">
                      <div class="col-md-6 col-lg-3">
                        <label for="dokterTindakan" class="form-label">Dokter</label>
                        <select class="form-select formTindakan" id="dokterTindakan" name="dokterTindakan">
                          <option value="<?=$NIPDPJP?>" selected><?=$namaDPJP?></option>
                        </select>
                      </div>
                      <div class="col-md-6 col-lg-3">
                        <label for="perawatTindakan" class="form-label">Perawat</label>
                        <select class="form-select formTindakan" id="perawatTindakan" name="perawatTindakan">
                          <option value="<?=$user_nip?>" selected><?=$user_nama?></option>
                        </select>
                      </div>
                      <div class="col-md-8 col-lg-4">
                        <label for="kodeTindakan" class="form-label">Tindakan</label>
                        <select class="form-select formTindakan" id="kodeTindakan" name="kodeTindakan"></select>
                      </div>
                      <div class="col-md-2 col-lg-1">
                        <label for="jumlahTindakan" class="form-label">Jumlah</label>
                        <input type="number" class="form-control formTindakan" name="jumlahTindakan" id="jumlahTindakan" min="1">                      
                      </div>
                      <div class="col-md-2 col-lg-1 align-self-end">
                        <button class="btn btn-primary w-100" type="submit" id="tombolTambahTindakan" onclick="konfirmasiTambahTindakan()" disabled>Tambah</button>
                      </div>
                    </div>

                    <div id="daftarTindakan" class="mb-4"></div>
                  </div>

                  <!-- =========================================================================================== -->
                  <!-- Penunjang Medis -->
                  <!-- =========================================================================================== -->
                  <div class="tab-pane fade" id="nav-penunjangMedis" role="tabpanel" aria-labelledby="nav-penunjangMedis-tab" tabindex="0">
                    <div class="row g-3 mb-4">
                      <div class="col-md-6 col-lg-2">
                        <label for="selectUnit_modal" class="form-label">Unit Tujuan</label>
                        <select class="form-select formPenunjang" name="selectUnit_modal" id="selectUnit_modal">
                          <option value="">Pilih Unit Penunjang Medis</option>
                          <?php
                            $query = "SELECT nKode, szNama FROM _poliklinik WHERE nKodeKunjungan = '3' ORDER BY szNama;";
                            $conn->query($query);
                            $penunjang = $conn->resultSet();
                            foreach ($penunjang as $penunjang) {
                              $kode = $penunjang['nKode'];
                              $nama = $penunjang['szNama'];
                              echo "<option value='$kode'>$nama</option>";
                            } 
                          ?>
                        </select>
                      </div>
                      <div class="col-md-6 col-lg-2">
                        <label for="selectDokter_modal" class="form-label">Dokter Pengirim</label>
                        <select class="form-select formPenunjang" name="selectDokter_modal" id="selectDokter_modal">
                          <option value='<?=$NIPDPJP?>'><?=$namaDPJP?></option>
                        </select>
                      </div>
                      <div class="col-md-6 col-lg-2">
                        <label for="selectdiagnosa" class="form-label">Diagnosa</label>
                        <select class="form-select formPenunjang" name="selectdiagnosa" id="selectdiagnosa"></select>
                      </div>
                      <div class="col-md-6 col-lg-2">
                        <label for="selectTarif_modal" class="form-label">Tarif</label>
                        <select class="form-select formPenunjang" name="selectTarif_modal" id="selectTarif_modal">
                          <option value="">Pilih Kelas Tarif</option>
                          <option value="1">VVIP</option>
                          <option value="2" selected>VIP</option>
                          <option value="3">Kelas 1</option>
                          <option value="4">Kelas 2</option>
                          <option value="5">Kelas 3</option>
                        </select>                      
                      </div>
                      <div class="col-md-6 col-lg-2">
                        <label class="col-sm-4 col-form-label">Cito</label><br>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="radio-cito" id="citoVal1" value="Y">
                          <label class="form-check-label" for="citoVal1">Ya</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="radio-cito" id="citoVal2" value="" checked>
                          <label class="form-check-label" for="citoVal2">Tidak</label>
                        </div>                  
                      </div>
                      <div class="col-md-3 col-lg-1 align-self-end">
                        <button class="btn btn-primary w-100" type="submit" id="tombolAutentikasiPenunjang" onclick="tambahPenunjang()" disabled>Simpan</button>
                      </div>
                    </div>
                    
                    <div id="daftarPenunjang"></div>
                  </div>

                  <!-- =========================================================================================== -->
                  <!-- Farmasi -->
                  <!-- =========================================================================================== -->
                  <div class="tab-pane fade" id="nav-farmasi" role="tabpanel" aria-labelledby="nav-farmasi-tab" tabindex="0">
                    Farmasi
                  </div>

                  <!-- =========================================================================================== -->
                  <!-- Biaya -->
                  <!-- =========================================================================================== -->
                  <div class="tab-pane fade" id="nav-biaya" role="tabpanel" aria-labelledby="nav-biaya-tab" tabindex="0">
                    Biaya
                  </div>

                  <!-- =========================================================================================== -->
                  <!-- Tindak Lanjut -->
                  <!-- =========================================================================================== -->
                  <div class="tab-pane fade" id="nav-tindakLanjut" role="tabpanel" aria-labelledby="nav-tindakLanjut-tab" tabindex="0">
                    <div class="row mb-4" id="pilihanTindakLanjut">
                      <div class="col-12 d-flex justify-content-center mb-3">
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="radio-lanjut" id="pulang" value="Pulang" checked>
                          <label class="form-check-label" for="pulang">
                            Pulang
                          </label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="radio-lanjut" id="rawatInap" value="Rawat Inap">
                          <label class="form-check-label" for="rawatInap">
                            Rawat Inap
                          </label>
                        </div>
                      </div>
                      <div class="col-12 col-md-3 col-lg-2 mx-auto">
                        <button class="btn btn-primary w-100" type="submit" id="tombolSimpan" onclick="lanjutSubmit()">Simpan</button>
                      </div>
                    </div>
                  </div>
                  <!-- =========================================================================================== -->
                  <!-- Riwayat Kunjungan -->
                  <!-- =========================================================================================== -->
                  <div class="tab-pane fade" id="nav-riwayat" role="tabpanel" aria-labelledby="nav-riwayat-tab" tabindex="0"></div>
                  
                  <!-- =========================================================================================== -->
                  <!-- Data Induk -->
                  <!-- =========================================================================================== -->
                  <div class="tab-pane fade" id="nav-dataInduk" role="tabpanel" aria-labelledby="nav-dataInduk-tab" tabindex="0">
                    <div id="loadingDataInduk"></div>
                    <div class="row mb-2 d-none" id="biodataPasien">
                      <div class="col-md-6">
                        <div class="row mb-3">
                          <div class="col-4 d-flex justify-content-between"><span>No RM / NIK</span><span>:</span></div>
                          <div class="col-8" id="RM_NIK"></div>
                        </div>
                        <div class="row mb-3">
                          <div class="col-4 d-flex justify-content-between"><span>Nama</span><span>:</span></div>
                          <div class="col-8" id="nama"></div>
                        </div>
                        <div class="row mb-3">
                          <div class="col-4 d-flex justify-content-between"><span>Tempat, Tanggal Lahir</span><span>:</span></div>
                          <div class="col-8" id="TTL"></div>
                        </div>
                        <div class="row mb-3">
                          <div class="col-4 d-flex justify-content-between"><span>Jenis Kelamin / Umur</span><span>:</span></div>
                          <div class="col-8" id="JK_umur"></div>
                        </div>
                        <div class="row mb-3">
                          <div class="col-4 d-flex justify-content-between"><span>Status Pernikahan</span><span>:</span></div>
                          <div class="col-8" id="statusPernikahan"></div>
                        </div>
                        <div class="row mb-3">
                          <div class="col-4 d-flex justify-content-between"><span>Alamat</span><span>:</span></div>
                          <div class="col-8" id="alamat"></div>
                        </div>
                        <div class="row mb-3">
                          <div class="col-4 d-flex justify-content-between"><span>No.HP</span><span>:</span></div>
                          <div class="col-8" id="noHP"></div>
                        </div>
                        <div class="row mb-3">
                          <div class="col-4 d-flex justify-content-between"><span>Agama</span><span>:</span></div>
                          <div class="col-8" id="agama"></div>
                        </div>
                        <div class="row mb-3">
                          <div class="col-4 d-flex justify-content-between"><span>golongan Darah</span><span>:</span></div>
                          <div class="col-8" id="darah"></div>
                        </div>
                        <div class="row mb-3">
                          <div class="col-4 d-flex justify-content-between"><span>Warga Negara / Etnis</span><span>:</span></div>
                          <div class="col-8" id="wargaNegara_etnis"></div>
                        </div>
                        <div class="row mb-3">
                          <div class="col-4 d-flex justify-content-between"><span>Pendidikan / Pekerjaan</span><span>:</span></div>
                          <div class="col-8" id="pendidikan_pekerjaan"></div>
                        </div>
                        <div class="row mb-3">
                          <div class="col-4 d-flex justify-content-between"><span>Bahasa / Hambatan Bahasa</span><span>:</span></div>
                          <div class="col-8" id="bahsa_hambatan"></div>
                        </div>
                        <div class="row mb-3">
                          <div class="col-4 d-flex justify-content-between"><span>Alergi</span><span>:</span></div>
                          <div class="col-8" id="alergi"></div>
                        </div>
                      </div>

                      <div class="col-md-6">
                        <div class="row mb-3">
                          <div class="col-4 d-flex justify-content-between"><span>Asuransi / Kelas</span><span>:</span></div>
                          <div class="col-8" id="asuransi_kelas"></div>
                        </div>
                        <div class="row mb-3">
                          <div class="col-4 d-flex justify-content-between"><span>Kepesertaan</span><span>:</span></div>
                          <div class="col-8" id="kepesertaan"></div>
                        </div>
                        <div class="row mb-3">
                          <div class="col-4 d-flex justify-content-between"><span>FKTP</span><span>:</span></div>
                          <div class="col-8" id="FKTP"></div>
                        </div>
                        <div class="row mb-3">
                          <div class="col-4 d-flex justify-content-between"><span>Nama Wali</span><span>:</span></div>
                          <div class="col-8" id="namaWali"></div>
                        </div>
                        <div class="row mb-3">
                          <div class="col-4 d-flex justify-content-between"><span>Hubungan Wali</span><span>:</span></div>
                          <div class="col-8" id="hubunganWali"></div>
                        </div>
                        <div class="row mb-3">
                          <div class="col-4 d-flex justify-content-between"><span>No.HP Wali</span><span>:</span></div>
                          <div class="col-8" id="noHPWali"></div>
                        </div>
                      
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

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

      </section>
    </main>
    <!-- End #main -->

    <!-- Footer -->
    <?php include("../footer-menu.php") ?>
    <?php include("../notifikasi.php") ?>
    <!-- End of Footer -->
      
    <!-- bootstrap -->
    <script src="<?=baseURL?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- jquery -->
    <script src="<?=baseURL?>/assets/vendor/jQuery/jquery.min.js"></script>
    <!-- datatable -->
    <script src="<?=baseURL?>/assets/vendor/datatables/datatables.min.js"></script>
    <!-- select 2 -->
    <script src="<?=baseURL?>/assets/vendor/select2/js/select2.min.js"></script>


    <!-- Template Main JS File -->
    <script src="<?=baseURL?>/assets/js/main.js"></script>
    <script src="<?=baseURL?>/assets/js/menu-aktif.js"></script>

    <script>
      $(document).ready(function(){
        $("#notificationModal").modal("show");
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

      // cek password
      $("#passwordAksi").change(function(){
        var password = '<?= $user_password ?>';
        var passwordInput = $("#passwordAksi").val();
        if (passwordInput == "") {
          alert("#pesanAutentikasiAksi", "Masukkan password");
          $("#tombolAutentikasiAksi").prop("disabled", true);
        } else if (passwordInput != password) {
          alert("#pesanAutentikasiAksi", "Password salah");
          $("#tombolAutentikasiAksi").prop("disabled", true);
        }else {
          $("#pesanAutentikasiAksi").html("");
          $("#tombolAutentikasiAksi").prop("disabled", false);
        }
      });

      function alert(element, isiPesan){
        var pesan = "";
        pesan += "<div class='alert alert-danger alert-dismissible mb-0 fade show text-center' role='alert'>";
        pesan += isiPesan;
        pesan += "  <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>";
        pesan += "</div>";
        $(element).html(pesan);
      }

      function showPassword(element){
        if ($("#"+element+"Check").prop('checked')) {
          $("#"+element+"Check").prop('checked', false);
          $("#"+element).prop("type", "password");
        } else {
          $("#"+element+"Check").prop('checked', true);
          $("#"+element).prop("type", "text");
        }
      }

    </script>

    <script>
      // ===========================================================================================
      // DPJP & diagnosa
      // ===========================================================================================

      // tampilkan list diagnosa 
      function diagnosaPasien(){
        var noRM = <?= $pasien["noRM"] ?>;
        var episode = <?= $pasien["episode"] ?>;
        loading("#daftarDiagnosa");
        $.get("cariData.php?cariDiagnosaPasien=true&noRM="+noRM+"&episode="+episode, tampilkanDiagnosaPasien);
      }

      function tampilkanDiagnosaPasien(data){
        var kodeDokterUser = <?= $user_kodeDokter ?>;
        data = JSON.parse(data);
        var jumlah = data.length;

        var myTable = "";
        var bodyTable = "";
        if (jumlah > 0) {
          // create select kategoti item
          $("#kategoriDiagnosa").html("<option value='S' selected>Penyerta</option>");
          // display list diagnosa
          for (var i=0; i<jumlah; i++) {
            var no = data[i]["no"];
            var kodeDiagnosa = data[i]["kodeDiagnosa"];
            var kodeDokter = data[i]["kodePoli"];
            var kodeMutasiPasien = data[i]["kodeMutasiPasien"];
            var tanggalDiagnosa = data[i]["tanggalDiagnosa"];
            var kategoriDiagnosa = data[i]["kategoriDiagnosa"];
            var dokterDiagnosa = data[i]["dokterDiagnosa"];
            var diagnosa = data[i]["diagnosa"];
            var tanggalVerif = data[i]["tanggalVerif"];
            

            bodyTable += "<tr>";
            bodyTable += "  <td class='text-center align-middle'>" + no + "</td>";
            bodyTable += "  <td class='align-middle'>" + tanggalDiagnosa + "</td>";
            bodyTable += "  <td class='align-middle text-center'>" + kategoriDiagnosa + "</td>";
            bodyTable += "  <td class='align-middle'>" + dokterDiagnosa + "</td>";
            bodyTable += "  <td class='align-middle'>" + diagnosa + "</td>";
            if (tanggalVerif == "" && kodeDokter == kodeDokterUser) {
              bodyTable += "  <td class='align-middle text-center'><button class='btn btn-danger mx-1' data-toggle='tooltip' data-placement='top' title='Hapus' onclick='konfirmasiHapusDiagnosa("+kodeDiagnosa+")'><i class='bi bi-trash text-white' style='font-size: 15px;'></i></button></td>";
            } else {
              bodyTable += "  <td class='align-middle text-center'></td>";
            }
            bodyTable += "</tr>";
          }  
          myTable += "<div class='table-responsive mt-5 mb-4'>";
          myTable += "  <table class='table table-bordered table-hover' id='tabelDiagnosa' width='100%'>";
          myTable += "    <thead class='bg-light'>";
          myTable += "      <tr style='height: 50px;'>";
          myTable += "        <th class='text-center align-middle' width='5%'>NO</th>";
          myTable += "        <th class='text-center align-middle' width='10%'>TANGGAL</th>";
          myTable += "        <th class='text-center align-middle' width='10%'>KATEGORI DIAGNOSA</th>";
          myTable += "        <th class='text-center align-middle'>DOKTER</th>";
          myTable += "        <th class='text-center align-middle'>DIAGNOSA</th>";
          myTable += "        <th class='text-center align-middle' width='5%'>TOOLS</th>";
          myTable += "      </tr>";
          myTable += "    </thead>";
          myTable += "    <tbody>";
          myTable += bodyTable;            
          myTable += "    </tbody>";
          myTable += "  </table>";
          myTable += "</div>";

          $("#daftarDiagnosa").html(myTable);
          $("#tabelDiagnosa").DataTable({"autoWidth": false});
        } else {
          // create select kategoti item
          $("#kategoriDiagnosa").html("<option value='U' selected>Utama</option>");
          // create alert
          var noRM = <?= $pasien["noRM"] ?>;
          var pesan =  "Tidak ditemukan Diagnosa pasien dengan No. RM <b>"+noRM+"</b>";
          alert("#daftarDiagnosa", pesan);
        }
      }

      // ===========================================================================================
      // tambah diagnosa
      function konfirmasiTambahDiagnosa(){
        $("#passwordAksi").val("");
        $("#tombolAutentikasiAksi").attr("onclick","tambahDiagnosa()");
        $("#modalKonfirmasiAksi").modal("show");
      }
      function tambahDiagnosa(){
        $("#modalKonfirmasiAksi").modal("hide");
        var kodeMutasiPasien = <?= $kodeMutasiPasien ?>;
        var dokterDiagnosa = $("#dokterDiagnosa").val();		
        var perawatDiagnosa = '<?= $user_nip ?>';						
        var kategoriDiagnosa = $("#kategoriDiagnosa").val();
        var kodeDiagnosa = $("#kodeDiagnosa").val();
        $.get("pasienInput.php?tambahDiagnosa=true&kodeMutasiPasien="+kodeMutasiPasien+"&dokterDiagnosa="+dokterDiagnosa+"&perawatDiagnosa="+perawatDiagnosa+"&kategoriDiagnosa="+kategoriDiagnosa+"&kodeDiagnosa="+kodeDiagnosa, responseAksi);
      }

      // hapus diagnosa
      function konfirmasiHapusDiagnosa(kodeDiagnosa){
        $("#passwordAksi").val("");
        $("#tombolAutentikasiAksi").attr("onclick","hapusDiagnosa("+kodeDiagnosa+")");
        $("#modalKonfirmasiAksi").modal("show");
      }
      function hapusDiagnosa(kodeDiagnosa){
        $("#modalKonfirmasiAksi").modal("hide");
        var kodeMutasiPasien = <?= $kodeMutasiPasien ?>;
        $.get("pasienInput.php?hapusDiagnosa=true&kodeDiagnosa="+kodeDiagnosa+"&kodeMutasiPasien="+kodeMutasiPasien, responseAksi);
      }

      function responseAksi(data){
        var kodeMutasiPasien = <?= $kodeMutasiPasien ?>;
        perawatanPasien(kodeMutasiPasien);
      }
    </script>

    <script>
      // ===========================================================================================
      // Tindakan
      // ===========================================================================================
      $("#kodeTindakan").select2({
        theme: "bootstrap-5",
        minimumInputLength: 0,
        allowClear: true,
        placeholder: "Pilih Tindakan...",
        ajax: {
          dataType: "json",
          url: "cariData.php?cariTindakan=true",
          delay: 800,
          data: function(params) {
            return {
              search: <?= $user_kodeDokter ?>
            }
          },
          processResults: function (data) {
            return {
              results: data
            };
          },
        }
      });
      // cek isi form tambah diagnosa
      $(".formTindakan").change(function(){
        var dokterTindakan = $("#dokterTindakan").val();
        var perawatTindakan = $("#perawatTindakan").val();
        var kodeTindakan = $("#kodeTindakan").val();
        var jumlahTindakan = $("#jumlahTindakan").val();
        if (jumlahTindakan == 0 ) {
          $("#jumlahTindakan").val("");
          jumlahTindakan = "";
        }

        if (dokterTindakan!="" && perawatTindakan!="" && kodeTindakan!="" && jumlahTindakan!="") {
          $("#tombolTambahTindakan").prop('disabled', false);
        } else {
          $("#tombolTambahTindakan").prop('disabled', true);
        }
      });
      // cek password
      $("#passwordTindakan").change(function(){
        var passwordInput = $("#passwordTindakan").val();
        if (passwordInput == "") {
          $("#tombolAutentikasiTindakan").prop("disabled", true);
        } else {
          $("#tombolAutentikasiTindakan").prop("disabled", false);
        }
      });

      // tampilkan list tindakan 
      function tindakanPasien(){
        var noRM = <?= $pasien["noRM"] ?>;
        var episode = <?= $pasien["episode"] ?>;
        loading("#daftarTindakan");
        $.get("cariData.php?cariTindakanPasien=true&noRM="+noRM+"&episode="+episode, tampilkanTIndakanPasien);
      }

      function tampilkanTIndakanPasien(data){
        var kodeDokterUser = <?= $user_kodeDokter ?>;
        data = JSON.parse(data);
        var jumlah = data.length;

        var myTable = "";
        var bodyTable = "";
        var section = "";
        if (jumlah > 0) {
          section += "<div class='accordion mt-5'>";
          for (var i=0; i<jumlah; i++) {
            // set isi tabel
            bodyTable = "";
            var tindakan = data[i]["tindakan"];
            for (var j=0; j<data[i]["tindakan"].length; j++) {
              var no = tindakan[j]["no"]+1;
              var kodeTindakan = tindakan[j]["kodeTindakan"];
              var tanggalTindakan = tindakan[j]["tanggalTindakan"];
              var namaTindakan = tindakan[j]["namaTindakan"];
              var dokterTindakan = tindakan[j]["dokterTindakan"];
              var perawatTindakan = tindakan[j]["perawatTindakan"];
              var jumlahTindakan = tindakan[j]["jumlahTindakan"];
              var regPoli = tindakan[j]["regPoli"];
              
              bodyTable += "<tr>";
              bodyTable += "  <td class='align-middle text-center'>" + no + "</td>";
              bodyTable += "  <td class='align-middle'>" + tanggalTindakan + "</td>";
              bodyTable += "  <td class='align-middle'>" + namaTindakan + "</td>";
              bodyTable += "  <td class='align-middle'>" + dokterTindakan + "</td>";
              bodyTable += "  <td class='align-middle'>" + perawatTindakan + "</td>";
              bodyTable += "  <td class='align-middle text-center'>" + jumlahTindakan + "</td>";
              bodyTable += "  <td class='align-middle text-center'>";
              if (regPoli == null && kodeDokterUser == data[i]["kodeUnit"]) {
                bodyTable += "  <button class='btn btn-danger mx-1' data-toggle='tooltip' data-placement='top' title='Hapus' onclick='konfirmasiHapusTindakan("+kodeTindakan+")'><i class='bi bi-trash text-white' style='font-size: 15px;'></i></button>";
              }
              bodyTable += "  </td>";
              bodyTable += "</tr>";
            }  
            // set tabel
            myTable = "";
            myTable += "<div class='table-responsive'>";
            myTable += "  <table class='table table-bordered table-hover tabelTindakan' width='100%'>";
            myTable += "    <thead class='bg-light'>";
            myTable += "      <tr style='height: 50px;'>";
            myTable += "        <th class='text-center align-middle' width='5%'>NO</th>";
            myTable += "        <th class='text-center align-middle' width='10%'>TANGGAL</th>";
            myTable += "        <th class='text-center align-middle' width='30%'>TINDAKAN</th>";
            myTable += "        <th class='text-center align-middle' width='20%'>DOKTER</th>";
            myTable += "        <th class='text-center align-middle' width='20%'>PERAWAT</th>";
            myTable += "        <th class='text-center align-middle' WIFTH='5%'>JUMLAH</th>";
            myTable += "        <th class='text-center align-middle' width='10%'>TOOLS</th>";
            myTable += "      </tr>";
            myTable += "    </thead>";
            myTable += "    <tbody>";
            myTable += bodyTable;            
            myTable += "    </tbody>";
            myTable += "  </table>";
            myTable += "</div>";

            // set acordion
            var classHeader = "collapsed";
            var classBody = "";
            if (kodeDokterUser == data[i]['kodeUnit']) {
              classHeader = "";
              classBody = "show";
            }

            section += "  <div class='accordion-item'>";
            section += "    <div class='accordion-header' id='panelHeading_"+i+"'>";
            section += "      <button class='accordion-button bg-primary bg-opacity-10 "+classHeader+"' type='button' data-bs-toggle='collapse' data-bs-target='#panelcollapse_"+i+"' aria-expanded='true' aria-controls='panelHeading_"+i+"'>";
            section += "       <h4 class='fw-bold m-0'>"+data[i]['namaUnit']+"</h4>";
            section += "      </button>";
            section += "    </div>";
            section += "    <div class='accordion-collapse collapse "+classBody+"' id='panelcollapse_"+i+"' aria-labelledby='panelHeading_"+i+"'>";
            section += "      <div class='accordion-body'>";
            section +=          myTable;
            section += "      </div>";
            section += "    </div>";
            section += "  </div>";
          }  
          section += "</div>";

          $("#daftarTindakan").html(section);
          $(".tabelTindakan").DataTable({"autoWidth": false});
        } else {
          // create alert
          var noRM = <?= $pasien["noRM"] ?>;
          var pesan =  "Tidak ditemukan tindakan pasien dengan No. RM <b>"+noRM+"</b>";
          alert("#daftarTindakan", pesan);
        }
      }

      // ===========================================================================================
      // tambah tindakan
      function konfirmasiTambahTindakan(){
        $("#passwordAksi").val("");
        $("#tombolAutentikasiAksi").attr("onclick","tambahTindakan()");
        $("#modalKonfirmasiAksi").modal("show");
      }

      function tambahTindakan(){
        $("#modalKonfirmasiAksi").modal("hide");
        var kodeMutasiPasien = <?= $kodeMutasiPasien ?>;
        var dokterTindakan = $("#dokterTindakan").val();
        var perawatTindakan = $("#perawatTindakan").val();
        var kodeTindakan = $("#kodeTindakan").val();
        var jumlahTindakan = $("#jumlahTindakan").val();

        $.get("pasienInput.php?tambahTindakan=true&kodeMutasiPasien="+kodeMutasiPasien+"&dokterTindakan="+dokterTindakan+"&perawatTindakan="+perawatTindakan+"&kodeTindakan="+kodeTindakan+"&jumlahTindakan="+jumlahTindakan, responseAksiTindakan);
      }

      // hapus tindakan
      function konfirmasiHapusTindakan(kodeTindakan){
        $("#passwordAksi").val("");
        $("#tombolAutentikasiAksi").attr("onclick","hapusTindakan("+kodeTindakan+")");
        $("#modalKonfirmasiAksi").modal("show");
      }

      function hapusTindakan(kodeTindakan){
        $("#modalKonfirmasiAksi").modal("hide");
        var kodeMutasiPasien = <?= $kodeMutasiPasien ?>;
        $.get("pasienInput.php?hapusTindakan=true&kodeTindakan="+kodeTindakan+"&kodeMutasiPasien="+kodeMutasiPasien, responseAksiTindakan);
      }

      function responseAksiTindakan(data){
        console.log(data);
        var kodeMutasiPasien = <?= $kodeMutasiPasien ?>;
        perawatanPasien(kodeMutasiPasien);
      }
    </script>

    <script>
      // ===========================================================================================
      // penunjang medis
      // ===========================================================================================
      $("#selectdiagnosa").select2({
        theme: "bootstrap-5",
        minimumInputLength: 3,
        allowClear: true,
        placeholder: "Pilih Diagnosa...",
        ajax: {
          dataType: "json",
          url: "cariData.php?cariICD=true",
          delay: 800,
          data: function(params) {
            return {
              search: params.term
            }
          },
          processResults: function(data) {
            return {
              results: data
            };
          },
        }
      });

      //cek isi form order penunjang
      $(".formPenunjang").change(function() {
        var selectUnit_modal = $("#selectUnit_modal").val();
        var selectDokter_modal = $("#selectDokter_modal").val();
        var selectdiagnosa = $("#selectdiagnosa").val();
        var selectTarif_modal = $("#selectTarif_modal").val();
        var cito = $("#radio-cito").val();
        if (selectUnit_modal != "" && selectDokter_modal != "" && selectTarif_modal != "" && selectdiagnosa != "") {
          $("#tombolAutentikasiPenunjang").prop('disabled', false);
        } else {
          $("#tombolAutentikasiPenunjang").prop('disabled', true);
        }
      });

      // tambah penunjang
      function tambahPenunjang() {
        var user = '<?= $user_kode ?>';
        var kodeMutasiPasien = <?= $kodeMutasiPasien ?>;
        var unitPenunjang = $("#selectUnit_modal").val();
        var dokterPengirim = $("#selectDokter_modal").val();
        var diagnosaPenunjang = $("#selectdiagnosa").val();
        var tarifPenunjang = $("#selectTarif_modal").val();
        var citoPenunjang = document.querySelector('input[name="radio-cito"]:checked').value;

        $.get('pasienInput.php?orderPenunjang=true&n=' + user + '&nKodeMutasi=' + kodeMutasiPasien +
          '&nKodePoli=' + unitPenunjang + '&nKodeDokter=' + dokterPengirim + '&nKodeTarif=' + tarifPenunjang +
          '&nKodeDx=' + diagnosaPenunjang + '&nCito=' + citoPenunjang, test);
      }

      // tampilkan list penunjang
      function penunjangPasien() {
        var episode = <?= $pasien["episode"] ?>;
        loading("daftarPenunjang");
        $.get("cariData.php?cariPenunjangPasien=true&episode=" + episode, tampilkanPenunjangPasien);
      }

      function tampilkanPenunjangPasien(data) {
        data = JSON.parse(data);
        var jumlah = data.length;

        var myTable = "";
        var bodyTable = "";
        if (jumlah > 0) {
          for (var i = 0; i < jumlah; i++) {
            var no = data[i]["no"];
            var kodeIDX = data[i]["Idx"];
            var kodeMutasiPasien = data[i]["nKodeMutasi"];
            var tanggalPenunjang = data[i]["tTanggal"];
            var unitPenunjang = data[i]["szPenunjang"];
            var kwitansiPenunjang = data[i]["szKwitansi"];
            var biayaPenunjang = data[i]["curPenunjang"];var bp = String(biayaPenunjang).replace(/(.)(?=(\d{3})+$)/g, '$1,');
            var kodePenunjang = data[i]["nKodePenunjang"];
            var kodeMutasiTindakan = data[i]["nKodeMT"];
            var kodeHasilPenunjang = data[i]["nKodeHasil"];
            var namaFile = data[i]["szFile"];
            var kodeRIS = data[i]["xRIS"];

            bodyTable += "<tr>";
            bodyTable += "  <td class='text-center align-middle'>" + no + "</td>";
            bodyTable += "  <td class='align-middle'>" + tanggalPenunjang + "</td>";
            bodyTable += "  <td class='align-middle'>" + unitPenunjang + "</td>";
            bodyTable += "  <td class='align-middle'>" + kwitansiPenunjang + "</td>";
            bodyTable += "  <td class='align-middle'>" + bp + "</td>";

            if (biayaPenunjang) {
              bodyTable += "  <td class='align-middle'><button class='btn btn-danger mx-1' data-toggle='tooltip' data-placement='top' title='Hasil Bacaan' onclick='myFunctionBacaHasil(" + no + ")'><i class='bi bi-book text-white' style='font-size: 15px;'></i></button></td>";
            } else {
              bodyTable += "  <td class='align-middle'></td>";
            }


            bodyTable += "</tr>";
          }
          myTable += "<div class='table-responsive mt-5 mb-4'>";
          myTable += "  <table class='table table-bordered table-hover' id='dataTable' width='100%'>";
          myTable += "    <thead class='bg-light'>";
          myTable += "      <tr style='height: 50px;'>";
          myTable += "        <th class='text-center align-middle' width='5%'>NO</th>";
          myTable += "        <th class='text-center align-middle' width='10%'>TANGGAL</th>";
          myTable += "        <th class='text-center align-middle' width='30%'>UNIT PENUNJANG</th>";
          myTable += "        <th class='text-center align-middle'>NO. REGISTER</th>";
          myTable += "        <th class='text-center align-middle'>BIAYA</th>";
          myTable += "        <th class='text-center align-middle' width='10%'>TOOLS</th>";
          myTable += "      </tr>";
          myTable += "    </thead>";
          myTable += "    <tbody>";
          myTable += bodyTable;
          myTable += "    </tbody>";
          myTable += "  </table>";
          myTable += "</div>";

          $("#daftarPenunjang").html(myTable);
          $("#dataTable").DataTable();
        } else {
          var noRM = <?= $pasien["noRM"] ?>;
          var pesan = "";
          pesan += "<div class='alert alert-danger fade show text-center mb-4' role='alert'>";
          pesan += "  Tidak ditemukan Penunjang pasien dengan No. RM <b>" + noRM + "</b>";
          pesan += "</div>";
          $("#daftarPenunjang").html(pesan);
        }
      }
    </script>
    <script>
      // ===========================================================================================
      // Tindakan Lanjut
      // ===========================================================================================
      function lanjutSubmit(){
        var mutasiPasien = <?= $kodeMutasiPasien ?>;
        var episodePasien = <?= $pasien["episode"] ?>;
        var tindakLanjut = document.querySelector('input[name="radio-lanjut"]:checked').value;
        // console.log(yKode + "|" + yLanjut);
			  $.get('pasienInput.php?cariTindakLanjut=true&kodeMutasiPasien='+mutasiPasien+'&kodeEpisode='+episodePasien+'&radioLanjut='+tindakLanjut, doSomethingWithLanjut);
      }

      function doSomethingWithLanjut(data){
        console.log(data);
        // alert(data);
        // return false;
        // alert(resRx[1]);
        //alert(yUser);
        // location.replace("I_pasien-js.php?n=" + res[0]);
      } 
      //====================end of Simpan Tindak Lanjut===================================
    </script>

    <script>
      // ===========================================================================================
      // riwayat
      // ===========================================================================================
      function riwayatPasien(){
        var noRM = <?= $pasien["noRM"] ?>;
        var riwayat = $("#nav-riwayat").html();
        if (riwayat == "") {
          loading("#nav-riwayat");
          $.get("../pasien/cariData.php?cariRiwayatPasien=true&noRM="+noRM, tampilkanRiwayat);
        }
      }

      function tampilkanRiwayat(data) {
        data = JSON.parse(data);
        var jumlah = data.length;

        var myTable = "";
        var bodyTable = "";
        if (jumlah > 0) {
          for (var i=0; i<jumlah; i++) {
            var no = data[i]["no"];
            var tanggal = data[i]["tanggal"];
            var penjamin = data[i]["penjamin"];
            var sjp = data[i]["sjp"];
            var statusUnit = data[i]["statusUnit"];
            var unit = data[i]["unit"];
            var dokter = data[i]["dokter"];
            var diagnosa = data[i]["diagnosa"];
            var tglKRS= data[i]["tglKRS"];
            var tindakLanjut= data[i]["tindakLanjut"];
            var petugas= data[i]["petugas"];

            bodyTable += "<tr>";
            bodyTable += "  <td class='text-center align-middle'>" + no + "</td>";
            bodyTable += "  <td class='align-middle'>" + tanggal + "</td>";
            bodyTable += "  <td class='align-middle'>" + penjamin + "<br>[ "+ sjp + " ]</td>";
            if (statusUnit == "") {
              bodyTable += "  <td class='align-middle'>" + unit + "</td>";
            } else {
              bodyTable += "  <td class='align-middle'><span class='text-primary'>Poli Eksekutif</span><br>"+ unit + "</td>";
            }
            bodyTable += "  <td class='align-middle'>" + dokter + "</td>";
            bodyTable += "  <td class='align-middle'>" + diagnosa + "</td>";
            bodyTable += "  <td class='align-middle'>" + tglKRS + "</td>";
            bodyTable += "  <td class='align-middle'>" + tindakLanjut + "</td>";
            bodyTable += "  <td class='align-middle'>" + petugas + "</td>";
            bodyTable += "</tr>";
          }  
          myTable += "<div class='table-responsive mb-4'>";
          myTable += "  <table class='table table-bordered table-hover' id='tabelRiwayat' width='100%'>";
          myTable += "    <thead class='bg-light'>";
          myTable += "      <tr style='height: 50px;'>";
          myTable += "        <th class='text-center align-middle' width='5%'>NO</th>";
          myTable += "        <th class='text-center align-middle' width='9%'>TANGGAL</th>";
          myTable += "        <th class='text-center align-middle' width='15%'>PEMBIAYAAN</th>";
          myTable += "        <th class='text-center align-middle' width='15%'>UNIT</th>";
          myTable += "        <th class='text-center align-middle' width='15%'>DPJP</th>";
          myTable += "        <th class='text-center align-middle' width='8%'>DIAGNOSA</th>";
          myTable += "        <th class='text-center align-middle' width='9%'>TANGGAL KRS</th>";
          myTable += "        <th class='text-center align-middle' width='8%'>TINDAK LANJUT</th>";
          myTable += "        <th class='text-center align-middle' width='15%'>PETUGAS</th>";
          myTable += "      </tr>";
          myTable += "    </thead>";
          myTable += "    <tbody>";
          myTable +=        bodyTable;            
          myTable += "    </tbody>";
          myTable += "  </table>";
          myTable += "</div>";

          $("#nav-riwayat").html(myTable);
          $("#tabelRiwayat").DataTable({"autoWidth": false});
        } else {
          var noRM = <?= $pasien["noRM"] ?>;
          var pesan =  "Tidak ditemukan riwayat kunjungan pasien dengan No. RM <b>"+noRM+"</b>";
          alert("#nav-riwayat", pesan);;
        }
      }
    </script>

    <script>
      // ===========================================================================================
      // data induk
      // ===========================================================================================
      function dataIndukPasien(){
        var noRM = <?= $pasien["noRM"] ?>;
        var nik = $("#RM_NIK").html();
        if (nik == "") {
          loading("#loadingDataInduk");
          $("#biodataPasien").addClass("d-none");
  
          $.get("cariData.php?cariDataIndukPasien=true&noRM="+noRM, tampilkanDataInduk);
        }
      }
      function tampilkanDataInduk(data) {
        $("#loadingDataInduk").html("");
        data = JSON.parse(data);
        $("#RM_NIK").html(data["noRM"]+" / "+data["NIK"]);
        $("#nama").html(data["nama"]);
        $("#TTL").html(data["tempatLahir"]+", "+data["tglLahir"]);
        $("#JK_umur").html(data["jenisKelamin"]+" / "+data["umur"]);
        $("#statusPernikahan").html(data["statusPernikahan"]);
        $("#alamat").html(data["alamatLengkap"]);
        $("#noHP").html(data["noTelp"]);
        $("#agama").html(data["agama"]);
        $("#darah").html(data["golDarah"]);
        $("#wargaNegara_etnis").html(data["wargaNegara"]);
        $("#pendidikan_pekerjaan").html(data["pendidikan"]+" / "+data["pekerjaan"]);
        $("#bahsa_hambatan").html(data["bahasa"]+" / "+data["hambatanKomunikasi"]);
        $("#alergi").html(data["alergi"]);
        $("#asuransi_kelas").html(data["asuransi"]+" / "+data["kelasAsuransi"]);
        $("#kepesertaan").html(data["kepesertaanAsuransi"]);
        $("#FKTP").html(data["FKTP"]);
        $("#namaWali").html(data["namaWali"]);
        $("#hubunganWali").html(data["hubunganWali"]);
        $("#noHPWali").html(data["noTelpWali"]);

        $("#loading").html("");
        $("#loading").addClass("d-none");
        $("#biodataPasien").removeClass("d-none");
      }
    </script>

    <script>
      // ===========================================================================================
      // tambahan
      // ===========================================================================================
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

      function test(data){
        console.log(data);
      }
    </script>

  </body>

</html>