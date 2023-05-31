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
    <!-- select2 -->
    <link rel="stylesheet" href="../assets/vendor/select2/css/select2.min.css">
    <link rel="stylesheet" href="../assets/vendor/select2/css/select2-bootstrap-5-theme.min.css">
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

    <?php
      $kodeMutasiPasien = $_POST["kodeMutasiPasien"];
    ?>

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
                  $hasil = $conn->Execute($queryPasien) or die($conn->ErrorMsg());
                  $hasil->MoveFirst();
                  while (!$hasil->EOF) {
                    // hitung umur
                    $bday = new DateTime($hasil->fields[7]);
                    $today = new Datetime(date("Y-m-d"));
                    $diff = $today->diff($bday);
                    // susun pasien
                    $pasien["tanggal"] =  date("d-m-Y H:i:s", strtotime($hasil->fields[0]));
                    $pasien["episode"] = $hasil->fields[1];
                    $pasien["SJP"] = $hasil->fields[2];
                    $pasien["noRM"] = $hasil->fields[3];
                    $pasien["nama"] = $hasil->fields[4];
                    $pasien["NIK"] = $hasil->fields[5];
                    $pasien["tempatLahir"] = $hasil->fields[6];
                    $pasien["tglLahir"] = date("d-m-Y", strtotime($hasil->fields[7]));
                    $pasien["jenisKelamin"] = $hasil->fields[8];
                    $pasien["umur"] = "$diff->y Tahun $diff->m Bulan $diff->d Hari";
                    $pasien["alamatLengkap"] = $hasil->fields[9];
                    $hasil->MoveNext();
                  }

                  $queryDashboard = "SELECT b.nNIP, b.szNama, a.szKodeDiagUtama, c.penyakit, a.tTanggal, a.tDokumen, a.tPanggil
                                      FROM _mutasi_pasien a
                                      LEFT OUTER JOIN dokter b ON a.nPoli = b.nKode
                                      LEFT OUTER JOIN icd c ON a.szKodeDiagUtama = c.id_icd
                                      WHERE a.nKode = $kodeMutasiPasien;
                                    ";
                  $hasil = $conn->Execute($queryDashboard) or die($conn->ErrorMsg());
                  $hasil->MoveFirst();
                  while (!$hasil->EOF) {
                    $kodeDPJP = $hasil->fields[0];
                    $namaDPJP = $hasil->fields[1];
                    $kodeDiagnosa = $hasil->fields[2];
                    $namaDiagnosa = $hasil->fields[3];
                    $biaya = "Rp. ". number_format(500000000);
                    $tDaftar = $hasil->fields[4];
                    $tDokumen = ($hasil->fields[5] == "") ? date("Y-m-d H:i:s") : $hasil->fields[5];
                    $tPelayanan = ($hasil->fields[6] == "") ? date("Y-m-d H:i:s") : $hasil->fields[6];
                    // hitung waktu
                    $waktuDaftar = new DateTime($tDaftar);
                    // waktu dokumen
                    $waktuDokumen = new DateTime($tDokumen);
                    $tungguDokumen = $waktuDaftar->diff($waktuDokumen);
                    $totalTungguDokumen = "$tungguDokumen->h Jam $tungguDokumen->i Menit $tungguDokumen->s Detik";
                    // waktu pelayanan
                    $waktuPelayanan = new DateTime($tPelayanan);
                    $tungguPelayanan = $waktuDaftar->diff($waktuPelayanan);
                    $totalTungguPelayanan = "$tungguPelayanan->h Jam $tungguPelayanan->i Menit $tungguPelayanan->s Detik";
                    
                    $hasil->MoveNext();
                  }
                ?>
                <div class="row d-flex align-items-center justify-content-between mb-3">
                  <div class="col-12 col-md-6 mb-3">
                    <h2 class="fw-bold text-danger m-0"><?=  $pasien["noRM"] ?> - <?= $pasien["nama"] ?></h2>
                  </div>
                  <div class="col-12 col-md-6 col-lg-3 mb-3 text-end">
                    <button type="button" class="btn btn-outline-primary btn-lg h-100 w-100 py-3">
                        <h4 class="fw-bold m-0">Biaya - <?=$biaya?></h4>
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
                    <button class="nav-link rounded rounded-0 fw-semibold py-3" id="nav-tindakan-tab" data-bs-toggle="tab" data-bs-target="#nav-tindakan" type="button" role="tab" aria-controls="nav-tindakan" aria-selected="false">Tindakan</button>
                    <button class="nav-link rounded rounded-0 fw-semibold py-3" id="nav-penunjangMedis-tab" data-bs-toggle="tab" data-bs-target="#nav-penunjangMedis" type="button" role="tab" aria-controls="nav-penunjangMedis" aria-selected="false" onclick="PenunjangPasien()">Penunjang Medis</button>
                    <button class="nav-link rounded rounded-0 fw-semibold py-3" id="nav-farmasi-tab" data-bs-toggle="tab" data-bs-target="#nav-farmasi" type="button" role="tab" aria-controls="nav-farmasi" aria-selected="false">Farmasi</button>
                    <button class="nav-link rounded rounded-0 fw-semibold py-3" id="nav-biaya-tab" data-bs-toggle="tab" data-bs-target="#nav-biaya" type="button" role="tab" aria-controls="nav-biaya" aria-selected="false">Biaya</button>
                    <button class="nav-link rounded rounded-0 fw-semibold py-3" id="nav-tindakLanjut-tab" data-bs-toggle="tab" data-bs-target="#nav-tindakLanjut" type="button" role="tab" aria-controls="nav-tindakLanjut" aria-selected="false">Tindak Lanjut</button>
                    <button class="nav-link rounded rounded-0 fw-semibold py-3" id="nav-riwayat-tab" data-bs-toggle="tab" data-bs-target="#nav-riwayat" type="button" role="tab" aria-controls="nav-riwayat" aria-selected="false" onclick="riwayatPasien()">Riwayat</button>
                    <button class="nav-link rounded rounded-0 fw-semibold py-3" id="nav-dataInduk-tab" data-bs-toggle="tab" data-bs-target="#nav-dataInduk" type="button" role="tab" aria-controls="nav-dataInduk" aria-selected="false" onclick="dataIndukPasien()">Data Induk</button>
                  </div>
                </nav>
              </div>
              <div class="card-body pt-4 pb-0">
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

                  <!-- DPJP & diagnosa -->
                  <div class="tab-pane fade" id="nav-dpjp-diagnosa" role="tabpanel" aria-labelledby="nav-dpjp-diagnosa-tab" tabindex="0">
                    <div class="row g-3 mb-4">
                      <div class="col-md-6 col-lg-4">
                        <label for="DPJP" class="form-label">DPJP</label>
                        <select class="form-select formDiagnosa" id="DPJP" name="DPJP">
                          <option value="<?=$kodeDPJP?>" selected><?=$namaDPJP?></option>
                        </select>
                      </div>
                      <div class="col-md-6 col-lg-3">
                        <label for="kategoriDiagnosa" class="form-label">Kategori Diagnosa</label>
                        <select class="form-select formDiagnosa" id="kategoriDiagnosa" name="kategoriDiagnosa">
                          <?php if($kodeDiagnosa == "") : ?>
                            <option value="U" selected>Utama</option>
                          <?php else : ?>
                            <option value="S" selected>Penyerta</option>
                          <?php endif; ?>
                        </select>
                      </div>
                      <div class="col-md-9 col-lg-4">
                        <label for="diagnosa" class="form-label">Diagnosa</label>
                        <select class="form-select formDiagnosa" name="diagnosa" id="diagnosa"></select>                       
                      </div>
                      <div class="col-md-3 col-lg-1 align-self-end">
                        <button class="btn btn-primary w-100" type="submit" id="tombolTambahDiagnosa" onclick="konfirmasiTambahDiagnosa()" disabled>Tambah</button>
                      </div>
                    </div>
                    <!-- modal password -->
                    <div class="modal fade" id="modalKonfirmasiAksi" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalKonfirmasiAksiLabel" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                          <div class="modal-body">
                            <div class="row g-3">
                              <div class="col-12 d-none">
                                <input type="text" class="form-control" id="kodeDiagnosa">
                              </div>
                              <div class="col-12">
                                <label for="passwordUser" class="form-label">Masukan Password</label>
                                <input type="password" class="form-control" id="passwordUser">
                              </div>
                              <div class="col-12 d-none" id="pesanAutentikasiDiagnosa"></div>
                              <div class="col-6">
                                <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Batal</button>
                              </div>
                              <div class="col-6">
                                <button type="button" class="btn btn-primary w-100" id="tombolAutentikasiDiagnosa" onclick="cekInputDiagnosa()" disabled>Simpan</button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div id="daftarDiagnosa" class="mb-4"></div>
                  </div>


                  <!-- tindakan -->
                  <div class="tab-pane fade" id="nav-tindakan" role="tabpanel" aria-labelledby="nav-tindakan-tab" tabindex="0">
                    Tindakan
                  </div>
                  <div class="tab-pane fade" id="nav-penunjangMedis" role="tabpanel" aria-labelledby="nav-penunjangMedis-tab" tabindex="0">
                  <div class="row g-3 mb-4">
                    <div class="col-md-1 col-lg-1 align-self-end">
                      <button class='btn btn-success mx-1' data-bs-toggle="modal" data-bs-target="#modalOrderPenunjang" title='Order'><i class='bi bi-plus text-white' style='font-size: 15px;'></i></button>
                    </div>
                  </div>
                  <div class="modal fade" id="modalOrderPenunjang" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="modalOrderPenunjangLabel" aria-hidden="true" style="overflow:hidden;">
                    <div class="modal-dialog modal-dialog-centered">
                      <div class="modal-content">
                        <div class="modal-body">
                          <div class="row g-3">
                            <div class="col-12">
                              <label class="form-label">
                                <font color="blue">Form Order Penunjang Medis</font>
                              </label>

                              <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Unit</label>
                                <div class="col-sm-8">
                                  <select class="form-select formPenunjang" name="selectUnit_modal" id="selectUnit_modal">
                                    <option value="">Pilih Unit Penunjang Medis</option>
                                    <?php
                                    $SQLPoli = "SELECT nKode,szNama from _poliklinik where nKodeKunjungan='3' order by szNama;";
                                    $restK = $conni->query($SQLPoli);
                                    while ($szKK = $restK->fetch_assoc()) {
                                      $nKodePetugas = $szKK['nKode'];
                                      $szPetugas = $szKK['szNama'];
                                      echo "
														        <option value='$nKodePetugas'>$szPetugas</option>";
                                    } ?>
                                  </select>
                                </div>
                              </div>

                              <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Dokter Pengirim</label>
                                <div class="col-sm-8">
                                  <select class="form-select formPenunjang" name="selectDokter_modal" id="selectDokter_modal">
                                    <option value="">Pilih Dokter</option>
                                    <?php
                                    $SQLPoli = "SELECT a.nNIP, a.szNama FROM dokter a
                                      LEFT OUTER JOIN dokter_jadwal b ON b.nKodeDokter = a.nKode
                                      WHERE b.nAktif ='1';";
                                    $restK = $conni->query($SQLPoli);
                                    while ($szKK = $restK->fetch_assoc()) {
                                      $nKodeDM = $szKK['nNIP'];
                                      $szDM = $szKK['szNama'];
                                      echo "
                                    <option value='$nKodeDM'>$szDM</option>";
                                    } ?>
                                  </select>
                                </div>
                              </div>

                              <div class="row mb-3">
                                <label for="diagnosa" class="col-sm-4 col-form-label">Diagnosa</label>
                                <div class="col-sm-8">
                                  <select class="form-select formPenunjang" name="selectdiagnosa" id="selectdiagnosa"></select>
                                </div>
                              </div>

                              <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Kelas Tarif</label>
                                <div class="col-sm-8">
                                  <select class="form-select formPenunjang" name="selectTarif_modal" id="selectTarif_modal">
                                    <option value="">Pilih Kelas Tarif</option>
                                    <option value="1">VVIP</option>
                                    <option value="2">VIP</option>
                                    <option value="3">Kelas 1</option>
                                    <option value="4">Kelas 2</option>
                                    <option value="5">Kelas 3</option>
                                  </select>
                                </div>
                              </div>

                              <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Cito</label>
                                <div class="col-sm-8">
                                  <div class="col-md-4">
                                    <div class="custom-control custom-radio">
                                      <input type="radio" class="custom-control-input" id="citoVal1" name="radio-cito" value="Y">
                                      <label class="custom-control-label" for="citoVal1">Ya</label>
                                    </div>
                                  </div>
                                  <div class="col-md-5">
                                    <div class="custom-control custom-radio">
                                      <input type="radio" class="custom-control-input" id="citoVal2" name="radio-cito" value="" checked>
                                      <label class="custom-control-label" for="citoVal2">Tidak</label>
                                    </div>
                                  </div>
                                </div>
                              </div>


                            </div>

                            <div class="col-6">
                              <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Batal</button>
                            </div>
                            <div class="col-6">
                              <button type="button" class="btn btn-primary w-100" id="tombolAutentikasiPenunjang" onclick="tambahPenunjang()" disabled>Simpan</button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>


                  <div id="daftarPenunjang"></div>
                </div>
                  <div class="tab-pane fade" id="nav-farmasi" role="tabpanel" aria-labelledby="nav-farmasi-tab" tabindex="0">
                    Farmasi
                  </div>
                  <div class="tab-pane fade" id="nav-biaya" role="tabpanel" aria-labelledby="nav-biaya-tab" tabindex="0">
                    Biaya
                  </div>

                  <!-- tindak lanjut -->
                  <div class="tab-pane fade" id="nav-tindakLanjut" role="tabpanel" aria-labelledby="nav-tindakLanjut-tab" tabindex="0">
                    <?php $statusTindakLanjut = 0 ?>
                    <?php if ($statusTindakLanjut == 0) : ?>
                      <div class="row mx-1" id="errorTindakLanjut">
                        <div class='alert alert-danger fade show text-center mb-4' role='alert'>
                          Tidak ada Antrian Pasien
                        </div>
                      </div>
                    <?php else : ?>
                      <div class="row mb-4" id="pilihanTindakLanjut">
                        <div class="col-12 d-flex justify-content-center mb-3">
                          <div class="form-check">
                            <input class="form-check-input" type="radio" name="pulang" id="pulang" value="option1" checked>
                            <label class="form-check-label" for="pulang">
                              Pulang
                            </label>
                          </div>
                        </div>
                        <div class="col-12 col-md-3 col-lg-2 mx-auto">
                          <button class="btn btn-primary w-100" type="submit" id="tombolSimpan">Simpan</button>
                        </div>
                      </div>
                    <?php endif; ?>
                  </div>

                  <!-- riwayat kunjungan -->
                  <div class="tab-pane fade" id="nav-riwayat" role="tabpanel" aria-labelledby="nav-riwayat-tab" tabindex="0"></div>
                  
                  <!-- data induk -->
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
      </section>
    </main>
    <!-- End #main -->

    <!-- Footer -->
    <?php include("../footer-menu.php") ?>
    <!-- End of Footer -->
    <div id="notifikasi">
      <?php include("../notifikasi.php") ?>
    </div>
      
    <!-- bootstrap -->
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- jquery -->
    <script src="../assets/vendor/jQuery/jquery.min.js"></script>
    <!-- datatable -->
    <script src="../assets/vendor/datatables/datatables.min.js"></script>
    <!-- select 2 -->
    <script src="../assets/vendor/select2/js/select2.min.js"></script>


    <!-- Template Main JS File -->
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/menu-aktif.js"></script>

    <script>
      function loading(element){
        var pesan = "";
        pesan += "<div class='d-flex justify-content-center mb-4'>";
        pesan += "<div class='spinner-grow text-danger mx-1' role='status'></div>";
        pesan += "<div class='spinner-grow text-warning mx-1' role='status'></div>";
        pesan += "<div class='spinner-grow text-success mx-1' role='status'></div>";
        pesan += "<div class='spinner-grow text-primary mx-1' role='status'></div>";
        pesan += "<div class='spinner-grow text-secondary mx-1' role='status'></div>";
        pesan += "</div>";
        $("#"+element).html(pesan);
      }
      function alert(element, isiPesan){
        var pesan = "";
        pesan += "<div class='alert alert-danger alert-dismissible mb-0 fade show text-center' role='alert'>";
        pesan += isiPesan;
        pesan += "  <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>";
        pesan += "</div>";
        $(element).html(pesan);
      }

    </script>

    <script>
      // ===========================================================================================
      // DPJP & diagnosa
      // ===========================================================================================
      
      $("#diagnosa").select2({
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
            processResults: function (data) {
            return {
              results: data
            };
          },
        }
      });
      // cek isi form tambah diagnosa
      $(".formDiagnosa").change(function(){
        var DPJP = $("#DPJP").val();
        var kategoriDiagnosa = $("#kategoriDiagnosa").val();
        var diagnosa = $("#diagnosa").val();

        if (DPJP!="" && kategoriDiagnosa!="" && diagnosa!="") {
          $("#tombolTambahDiagnosa").prop('disabled', false);
        } else {
          $("#tombolTambahDiagnosa").prop('disabled', true);
        }
      });
      // cek password
      $("#passwordUser").change(function(){
        var password = '<?= $passwordUser ?>';
        var passwordInput = $("#passwordUser").val();
        if (passwordInput == "") {
          alert("#pesanAutentikasiDiagnosa", "Masukan password");
          $("#pesanAutentikasiDiagnosa").removeClass("d-none");
          $("#tombolAutentikasiDiagnosa").prop("disabled", true);
        } else if (passwordInput != password) {
          alert("#pesanAutentikasiDiagnosa", "Password salah");
          $("#pesanAutentikasiDiagnosa").removeClass("d-none");
          $("#tombolAutentikasiDiagnosa").prop("disabled", true);
        } else {
          $("#pesanAutentikasiDiagnosa").html("");
          $("#tombolAutentikasiDiagnosa").prop("disabled", false);
        }
      });

      // tampilkan list diagnosa 
      function diagnosaPasien(){
        var noRM = <?= $pasien["noRM"] ?>;
        var eposide = <?= $pasien["episode"] ?>;
        loading("daftarDiagnosa");
        $.get("cariData.php?cariDiagnosaPasien=true&noRM="+noRM+"&eposide="+eposide, tampilkanDiagnosaPasien);
      }

      function tampilkanDiagnosaPasien(data){
        data = JSON.parse(data);
        var jumlah = data.length;

        var myTable = "";
        var bodyTable = "";
        if (jumlah > 0) {
          for (var i=0; i<jumlah; i++) {
            var no = data[i]["no"];
            var kodeDiagnosa = data[i]["kodeDiagnosa"];
            var kodeMutasiPasien = data[i]["kodeMutasiPasien"];
            var tanggalDiagnosa = data[i]["tanggalDiagnosa"];
            var kategoriDiagnosa = data[i]["kategoriDiagnosa"];
            var DPJP = data[i]["DPJP"];
            var diagnosa = data[i]["diagnosa"];
            var tanggalVerif = data[i]["tanggalVerif"];
            

            bodyTable += "<tr>";
            bodyTable += "  <td class='text-center align-middle'>" + no + "</td>";
            bodyTable += "  <td class='align-middle'>" + tanggalDiagnosa + "</td>";
            bodyTable += "  <td class='align-middle text-center'>" + kategoriDiagnosa + "</td>";
            bodyTable += "  <td class='align-middle'>" + DPJP + "</td>";
            bodyTable += "  <td class='align-middle'>" + diagnosa + "</td>";
            console.log(tanggalVerif);
            if (tanggalVerif == "") {
              bodyTable += "  <td class='align-middle text-center'><button class='btn btn-danger mx-1' data-toggle='tooltip' data-placement='top' title='Hapus' onclick='konfirmasiHapusDiagnosa("+kodeDiagnosa+")'><i class='bi bi-trash text-white' style='font-size: 15px;'></i></button></td>";
            } else {
              bodyTable += "  <td class='align-middle text-center'></td>";
            }
            bodyTable += "</tr>";
          }  
          myTable += "<div class='table-responsive mt-5 mb-4'>";
          myTable += "  <table class='table table-bordered table-hover' id='dataTable' width='100%'>";
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
          $("#dataTable").DataTable();
        } else {
          var noRM = <?= $pasien["noRM"] ?>;
          var pesan =  "Tidak ditemukan Diagnosa pasien dengan No. RM <b>"+noRM+"</b>";
          alert("#daftarDiagnosa", pesan);
        }
      }

      // ===========================================================================================
      function konfirmasiTambahDiagnosa(kodeDiagnosa){
        $("#kodeDiagnosa").val();
        $("#modalKonfirmasiAksi").modal("show");
      }

      function konfirmasiHapusDiagnosa(kodeDiagnosa){
        $("#kodeDiagnosa").val(kodeDiagnosa);
        $("#modalKonfirmasiAksi").modal("show");
      }

      // tambah diagnosa
      function cekInputDiagnosa(){
        var kodeDiagnosa = $("#kodeDiagnosa").val();
        if (kodeDiagnosa == "") {
          tambahDiagnosa();
        } else {
          hapusDiagnosa();
        }
      }

      // tambah diagnosa
      function tambahDiagnosa(){
        $("#modalKonfirmasiAksi").modal("hide");
        loading("daftarDiagnosa");
        var NIPPerawat = '<?= $NIPUser ?>';						
        var kodeMutasiPasien = <?= $kodeMutasiPasien ?>;
        var NIPDokter = $("#DPJP").val();		
        var kategoriDiagnosa = $("#kategoriDiagnosa").val();
        var kodeDiagnosa = $("#diagnosa").val();

        $.get("pasienInput.php?tambahDiagnosa=true&NIPPerawat="+NIPPerawat+"&kodeMutasiPasien="+kodeMutasiPasien+"&NIPDokter="+NIPDokter+"&kategoriDiagnosa="+kategoriDiagnosa+"&kodeDiagnosa="+kodeDiagnosa, responseAksi);
      }

      // tambah diagnosa
      function hapusDiagnosa(){
        $("#modalKonfirmasiAksi").modal("hide");
        loading("daftarDiagnosa");
        var kodeDiagnosa = $("#kodeDiagnosa").val();
        var kodeMutasiPasien = <?= $kodeMutasiPasien ?>;
        $.get("pasienInput.php?hapusDiagnosa=true&kodeDiagnosa="+kodeDiagnosa+"&kodeMutasiPasien="+kodeMutasiPasien, responseAksi);
      }
      function responseAksi(data){
        $("#diagnosa").val("");
        $("#passwordUser").val("");
        $("#tombolAutentikasiDiagnosa").prop("disabled", true);
        $("#notifikasi").load(location.href + " #notifikasi");
        $("#notificationModal").modal("show");
        diagnosaPasien();
      }

  // ===========================================================================================
  // PENUNJANG MEDIS
  // ===========================================================================================
  
  $("#selectdiagnosa").select2({
      dropdownParent: "#modalOrderPenunjang",
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
      var user = '<?= $nKodeUser ?>';
      var kodeMutasiPasien = <?= $kodeMutasiPasien ?>;
      var unitPenunjang = $("#selectUnit_modal").val();
      var dokterPengirim = $("#selectDokter_modal").val();
      var diagnosaPenunjang = $("#selectdiagnosa").val();
      var tarifPenunjang = $("#selectTarif_modal").val();
      var citoPenunjang = document.querySelector('input[name="radio-cito"]:checked').value;

      //console.log(user + '|' + kodeMutasiPasien + '|' + unitPenunjang + '|' + dokterPengirim + '|' + diagnosaPenunjang + '|' + tarifPenunjang + '|' + citoPenunjang, );
      $.get('pasienInput.php?orderPenunjang=true&n=' + user + '&nKodeMutasi=' + kodeMutasiPasien +
        '&nKodePoli=' + unitPenunjang + '&nKodeDokter=' + dokterPengirim + '&nKodeTarif=' + tarifPenunjang +
        '&nKodeDx=' + diagnosaPenunjang + '&nCito=' + citoPenunjang);
      window.location.reload();

    }

    // tampilkan list penunjang
    function PenunjangPasien() {
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
          // var szHasilLab = str_replace('/','-',kwitansiPenunjang);
		      // var szFileX = "../hasilPenunjang/"+namaFile;

          bodyTable += "<tr>";
          bodyTable += "  <td class='text-center align-middle'>" + no + "</td>";
          bodyTable += "  <td class='align-middle'>" + tanggalPenunjang + "</td>";
          bodyTable += "  <td class='align-middle'>" + unitPenunjang + "</td>";
          bodyTable += "  <td class='align-middle'>" + kwitansiPenunjang + "</td>";
          bodyTable += "  <td class='align-middle'>" + bp + "</td>";

          //console.log(tanggalVerif);
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

      // ===========================================================================================
      // riwayat
      // ===========================================================================================
      function riwayatPasien(){
        var noRM = <?= $pasien["noRM"] ?>;
        var riwayat = $("#nav-riwayat").html();
        if (riwayat == "") {
          loading("nav-riwayat");
          $.get("cariData.php?cariRiwayatPasien=true&noRM="+noRM, tampilkanRiwayat);
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
          myTable += "  <table class='table table-bordered table-hover' id='dataTable' width='100%'>";
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
          myTable += bodyTable;            
          myTable += "    </tbody>";
          myTable += "  </table>";
          myTable += "</div>";

          $("#nav-riwayat").html(myTable);
          $("#dataTable").DataTable();
        } else {
          var noRM = <?= $pasien["noRM"] ?>;
          var pesan =  "Tidak ditemukan riwayat kunjungan pasien dengan No. RM <b>"+noRM+"</b>";
          alert("#nav-riwayat", pesan);;
        }
      }


      // ===========================================================================================
      // data induk
      // ===========================================================================================
      function dataIndukPasien(){
        var noRM = <?= $pasien["noRM"] ?>;
        var nik = $("#RM_NIK").html();
        if (nik == "") {
          loading("loadingDataInduk");
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