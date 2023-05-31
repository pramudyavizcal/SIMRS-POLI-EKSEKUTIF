<?php 
  include_once("config.php");
  if(isset($_SESSION['simrs']['pegawai']['kodeUser'])){
    header('Location: dashboard.php');
    exit;
  }

  $status = "";
  $pesanStatus = "";
  if (isset($_SESSION['simrs']['login']['status'])) {
    $status = $_SESSION['simrs']['login']['status'];
    unset($_SESSION['simrs']['login']);
    if ($status == 0) {
      $pesanStatus = "Username Anda Salah";
    } elseif ($status == 1) {
      $pesanStatus = "Password Anda Salah";
    }
  }
  $pesanBerjalan = "kerja kerja kerja + paket hemat";
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
  <link href="<?= baseURL ?>assets/images/logo-rsud.png" rel="icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="<?= baseURL ?>assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= baseURL ?>assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="<?= baseURL ?>assets/css/style.css" rel="stylesheet">

</head>

<body style="background-image: url('assets/images/bg-login.png'); background-repeat: no-repeat; background-size: cover;">
  <main>
    <div class="fixed-top container pt-5">
      <!-- <div class="card w-100 bg-light border border-primary px-3" style="height:50px">
        <marquee class="h-100 d-flex align-items-center text-dark fw-bold"><?= $pesanBerjalan ?></marquee>
      </div> -->
    </div>

    <div class="container">
      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
              <div class="card mb-3">
                <div class="card-body">

                  
                  <div class="mt-3 text-center">
                    <img src="assets/images/logo-rsud.png" alt="">
                    <h5 class="card-title pt-3 pb-0 fs-4">Graha Wijaya Kusuma</h5>
                    <p class="small">Enter your username & password to login</p>
                  </div>
                  <hr class="my-4">

                  <?php if($pesanStatus != "") : ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                      <?= $pesanStatus ?>
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                  <?php endif; ?>


                  <form class="row g-3 needs-validation" action="autentikasi.php" method="POST" novalidate>

                    <div class="col-12">
                      <label for="yourUsername" class="form-label">Username</label>
                      <input type="text" class="form-control" id="username" name="username" required>
                      <div class="invalid-feedback">Masukan Username Anda.</div>
                    </div>

                    <div class="col-12">
                      <label for="yourPassword" class="form-label">Password</label>
                      <input type="password" class="form-control" id="password" name="password" required>
                      <div class="invalid-feedback">Masukan Password Anda!</div>
                    </div>

                    <div class="col-12">
                      <button class="btn btn-primary w-100" type="submit">Login</button>
                    </div>
                  </form>

                  <div class="mt-3 text-center">
                    <small>Designed and Develop by <b>SIMRS</b> © 2022</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </section>

    </div>
  </main><!-- End #main -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="<?= baseURL ?>assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Template Main JS File -->
  <script src="<?= baseURL ?>assets/js/main.js"></script>

</body>

</html>