<?php
  include_once("config.php");
  include_once("koneksi/koneksi99.php");
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
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="<?=baseURL?>assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Template Main CSS File -->
    <link href="<?=baseURL?>assets/css/style.css" rel="stylesheet">

  </head>
  <body>
    <?php
      if (!isset($_POST['username']) || !isset($_POST['password'])) {
        header("Location: index.php");
        exit;
      }
      $username = $_POST['username'];
      $password = $_POST['password'];
      $status = "";
      
      $nNIK = "";
      $query = "SELECT a.nNIK, a.szPassword, a.nNIP, a.szNama, a.szJabatan, a.nKodeLoket, b.szNama as namaPoli
                FROM eksekutif_user a
                LEFT OUTER JOIN _poliklinik b on b.nKode = a.nKodeLoket
                WHERE a.nNIK = '$username';
              ";
      $conn->query($query);
      $user = $conn->result();
      if (isset($user["nNIK"])){
        $nNIK = $user["nNIK"];
        $szPassword = $user["szPassword"];
        $nNIP = $user["nNIP"];
        $szNama = $user["szNama"];
        $szJabatan = $user["szJabatan"];
        $szLoket = $user["nKodeLoket"];
        $namaPoli = $user["namaPoli"];
        
      }
      
      if ($nNIK == "") {
        $_SESSION['simrs']['login']['status'] = 0;
        header("Location: index.php");
        exit;
      } else if ($password != $szPassword) {
        $_SESSION['simrs']['login']['status'] = 1;
        header("Location: index.php");
        exit;
      } else {
        $_SESSION['simrs']['pegawai']['kode'] = $nNIK;
        $_SESSION['simrs']['pegawai']['nip'] = $nNIP;
        $_SESSION['simrs']['pegawai']['password'] = $szPassword;
        $_SESSION['simrs']['pegawai']['nama'] = $szNama;
        $_SESSION['simrs']['pegawai']['jabatan'] = $szJabatan;
        $_SESSION['simrs']['pegawai']['loket'] = $szLoket;
        $_SESSION['simrs']['pegawai']['namaPoli'] = $namaPoli;
        header("Location: dashboard.php");
        exit;
      }
    ?>

    <!-- Vendor JS Files -->
    <script src="<?=baseURL?>assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?=baseURL?>assets/vendor/jQuery/jquery.min.js"></script>
      
    <!-- Template Main JS File -->
    <script src="<?=baseURL?>assets/js/main.js"></script>
  </body>
</html>