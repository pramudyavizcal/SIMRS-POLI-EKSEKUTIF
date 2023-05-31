<?php
  include_once("config.php");
  include_once("koneksi/koneksi99.php");

  if (!isset($_SESSION['simrs']['pegawai'])) {
    header('Location: '.baseURL.'index.php');
    exit;
  }

  $user_kode = $_SESSION["simrs"]["pegawai"]["kode"];
  $user_password = $_SESSION["simrs"]["pegawai"]["password"];
  $user_nip = $_SESSION["simrs"]["pegawai"]["nip"];
  $user_nama = $_SESSION["simrs"]["pegawai"]["nama"];
  $user_jabatan = $_SESSION["simrs"]["pegawai"]["jabatan"];
  $user_kodeDokter = $_SESSION['simrs']['pegawai']['loket'];
  $user_namaDokter = $_SESSION['simrs']['pegawai']['namaPoli'];
?>

<!-- ======= Header ======= -->
<header id="header" class="header fixed-top d-flex align-items-center px-2 px-md-3">

  <div class="d-flex align-items-center">
    <i class="bi bi-list toggle-sidebar-btn "></i>
    <a href="<?= baseURL ?>dashboard.php" class="logo d-flex align-items-center ms-4">
      <img src="<?= baseURL ?>assets/images/logo-rsud.png" alt="">
      <span class="d-none d-md-block ms-2">Poliklinik Wijaya Kusuma</span>
    </a>
  </div><!-- End Logo -->

  <nav class="header-nav ms-auto">
    <ul class="d-flex align-items-center">

      <li class="nav-item dropdown pe-3">

        <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
          <span class="dropdown-toggle ps-2"><?= $_SESSION['simrs']['pegawai']['nama'] ?></span>
        </a><!-- End Profile Iamge Icon -->

        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
          <li class="dropdown-header">
            <h6><?= $_SESSION['simrs']['pegawai']['nama'] ?></h6>
            <span><?= $_SESSION['simrs']['pegawai']['jabatan'] ?></span>
          </li>
          <li>
            <hr class="dropdown-divider">
          </li>
          <li>
            <a class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#logoutModal">
              <i class="bi bi-box-arrow-right"></i><span>Keluar</span>
            </a>
          </li>

        </ul><!-- End Profile Dropdown Items -->
      </li><!-- End Profile Nav -->

    </ul>
  </nav><!-- End Icons Navigation -->

</header><!-- End Header -->

<!-- alert -->
<div class="modal fade" id="logoutModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body text-center">
        <div class="row  h-100 d-flex align-items-center">
          <div class="col-4 text-center" style="font-size: 75px;">
            <i class="bi bi-exclamation-triangle text-danger"></i>
          </div>
          <div class="col-8">
            Anda akan keluar ?
          </div>
          <div class="col-6">
            <a href="<?= baseURL ?>logout.php" type="button" class="btn btn-danger w-100">Keluar</a>
          </div>
          <div class="col-6">
            <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal" aria-label="Close">Batal</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div><!-- End Basic Modal-->







