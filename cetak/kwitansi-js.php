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
    <link rel="preconnect" href="https://fonts.gstatic.com">
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

    
    <?php
                  $queryTahun = "SELECT YEAR(NOW()) AS tahun;
                                ";
                  $conn->query($queryTahun);
                  $hasil = $conn->resultSet();
                  foreach ($hasil as $hasil) {
                    $tahunSekarang = $hasil['tahun'];
                  }
                ?>
                

    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Cetak Kwitansi Pasien</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">Pasien</li>
                    <li class="breadcrumb-item active">Cetak Kwitansi</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-12">

                    <div class="card">
                        <div class="card-header bg-light" id="headerCariPasien">
                            <h5 class="card-title my-0 py-0">Kata Kunci Pencarian</h5>
                        </div>
                        <div class="card-body pt-4 pb-0">
                            <div class="row g-3 mb-4">
                                <div class="col-12 col-md-4 col-lg-2">
                                    <label for="noRM" class="form-label">No RM</label>
                                    <input type="text" class="form-control" id="noRM" name="noRM" value="<?= @$_POST['noRM'] ?>">
                                </div>
                                <div class="col-12 col-md-2 col-lg-1 align-self-end">
                                    <button type="submit" class="btn btn-primary w-100" onclick="cariBiodata()">Cari</button>
                                </div>
                            </div>
                        </div>

                        <!-- biodata -->
                        <div class="card-header bg-light border border-start-0 border-end-0 d-none" id="headerBiodataPasien">
                            <h5 class="card-title my-0 py-0">Biodata Pasien</h5>
                        </div>
                        <div class="card-body d-none pt-4 pb-0" id="bodyBiodataPasien">
                            <div id="biodataPasien"></div>
                            <input type="hidden" id="tahunSekarang" value="<?php echo $tahunSekarang ?>">
                        </div>

                        <!-- List Kwitansi -->
                        <div class="card-header bg-light border border-start-0 border-end-0 d-none" id="headerKwitansiPasien">
                            <h5 class="card-title my-0 py-0">Kwitansi Pasien</h5>
                        </div>
                        <div class="card-body d-none pt-0 pb-0" id="bodyKwitansiPasien">

                            <div id="KwitansiPasien"></div>
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
    <script src="<?= baseURL ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- jquery -->
    <script src="<?= baseURL ?>/assets/vendor/jQuery/jquery.min.js"></script>
    <!-- datatable -->
    <script src="<?= baseURL ?>/assets/vendor/datatables/datatables.min.js"></script>
    <!-- select 2 -->
    <script src="<?= baseURL ?>/assets/vendor/select2/js/select2.min.js"></script>


    <!-- Template Main JS File -->
    <script src="<?= baseURL ?>/assets/js/main.js"></script>
    <script src="<?= baseURL ?>/assets/js/menu-aktif.js"></script>



    <script>
        function cariBiodata() {
            noRM = $("#noRM").val();
            $("#headerBiodataPasien").removeClass("d-none");
            $("#bodyBiodataPasien").removeClass("d-none");
            if (noRM == "") {
                var pesan = "";
                pesan += "<div class='alert alert-danger fade show text-center mb-4' role='alert'>";
                pesan += "  Masukan No. RM dalam kolom pencarian";
                pesan += "</div>";
                $("#biodataPasien").html(pesan);
            } else {
                var pesan = "";
                pesan += "<div class='d-flex justify-content-center mb-4'>";
                pesan += "  <div class='spinner-border' role='status'>";
                pesan += "    <span class='visually-hidden'>Loading...</span>";
                pesan += "  </div>";
                pesan += "</div>";
                $("#biodataPasien").html(pesan);

                $.get("../pasien/cariData.php?cariBiodataPasien=true&noRM=" + noRM, tampilkanBiodata);
            }
        }

        function tampilkanBiodata(data) {
            data = JSON.parse(data);
            var biodata = "";
            if (data["noRM"] != "") {
                biodata += "<div class='row mb-2'>";
                biodata += "  <div class='col-md-6'>";
                biodata += "    <div class='row mb-3'>";
                biodata += "      <div class='col-4 col-lg-3 d-flex justify-content-between'><span>No RM</span><span>:</span></div>";
                biodata += "      <div class='col-8 col-lg-9'>" + data['noRM'] + "</div>";
                biodata += "    </div>";
                biodata += "    <div class='row mb-3'>";
                biodata += "      <div class='col-4 col-lg-3 d-flex justify-content-between'><span>Nama</span><span>:</span></div>";
                biodata += "      <div class='col-8 col-lg-9'>" + data['nama'] + "</div>";
                biodata += "    </div>";
                biodata += "    <div class='row mb-3'>";
                biodata += "      <div class='col-4 col-lg-3 d-flex justify-content-between'><span>Alamat</span><span>:</span></div>";
                biodata += "      <div class='col-8 col-lg-9'>" + data['alamatLengkap'] + "</div>";
                biodata += "    </div>";
                biodata += "  </div>";
                biodata += "  <div class='col-md-6'>";
                biodata += "    <div class='row mb-3'>";
                biodata += "      <div class='col-4 d-flex justify-content-between'><span>Jenis Kelamin</span><span>:</span></div>";
                biodata += "      <div class='col-8'>" + data['jenisKelamin'] + "</div>";
                biodata += "    </div>";
                biodata += "    <div class='row mb-3'>";
                biodata += "      <div class='col-4 d-flex justify-content-between'><span>Tempat, Tanggal Lahir</span><span>:</span></div>";
                biodata += "      <div class='col-8'>" + data['tempatLahir'] + ", " + data['tglLahir'] + "</div>";
                biodata += "    </div>";
                biodata += "    <div class='row mb-3'>";
                biodata += "      <div class='col-4 d-flex justify-content-between'><span>Umur</span><span>:</span></div>";
                biodata += "      <div class='col-8'>" + data['umur'] + "</div>";
                biodata += "    </div>";
                biodata += "  </div>";
                biodata += "</div>";

                $("#biodataPasien").html(biodata);
                cariKwitansi();
            } else {
                var pesan = "";
                pesan += "<div class='alert alert-danger fade show text-center mb-4' role='alert'>";
                pesan += "  Tidak ditemukan pasien dengan No. RM <b>" + noRM + "</b>";
                pesan += "</div>";
                $("#biodataPasien").html(pesan);
                $("#headerKwitansiPasien").addClass("d-none");
                $("#bodyKwitansiPasien").addClass("d-none");
                $("#KwitansiPasien").html(pesan);
            }
        }

        function cariKwitansi() {
            var noRM = $("#noRM").val();
            var tahunSekarang = $("#tahunSekarang").val();

            $("#headerKwitansiPasien").removeClass("d-none");
            $("#bodyKwitansiPasien").removeClass("d-none");

            if (noRM == "") {
                var pesan = "";
                pesan += "<div class='alert alert-danger fade show text-center mb-4' role='alert'>";
                pesan += "  Masukan No. RM dalam kolom pencarian";
                pesan += "</div>";
                $("#KwitansiPasien").html(pesan);
            } else {
                var pesan = "";
                pesan += "<div class='d-flex justify-content-center mb-4'>";
                pesan += "  <div class='spinner-border' role='status'>";
                pesan += "    <span class='visually-hidden'>Loading...</span>";
                pesan += "  </div>";
                pesan += "</div>";
                $("#KwitansiPasien").html(pesan);
                $.get("cariDataKwitansi.php?cariKwi=true&noRM=" + noRM +
                '&tahunSekarang=' + tahunSekarang, tampilkanKwitansi);
            }
        }

        function tampilkanKwitansi(data) {

            data = JSON.parse(data);
            var jumlah = data.length;
            console.log(data);

            var myTable = "";
            var bodyTable = "";


            if (jumlah > 0) {

                for (var i = 0; i < jumlah; i++) {
                    var user = '<?= $user_kode ?>';
                    var no = data[i]["no"];
                    var nkode = data[i]["nKode"];
                    console.log(nkode);
                    var tBayar = data[i]["tTglBayar"];
                    var namaUnit = data[i]["szNama"];
                    var noKwitansi = data[i]["szNoKwitansi"];
                    var kodeKunjungan = data[i]["nKodeKunjungan"];
                    var totalBiaya = data[i]["curBayar"];
                    var bpBiaya = String(totalBiaya).replace(/(.)(?=(\d{3})+$)/g, '$1,');
                    var szAsuransi = data[i]["szAsuransi"];
                    var episode = data[i]["nEpisode"];
                    var kodeMutasiPasien = data[i]["nKodeMutasiPasien"];

                    bodyTable += "<tr>";

                    bodyTable += "  <td class='text-center align-middle'>" + no + "</td>";

                    bodyTable += "  <td class='text-left align-middle'>" + tBayar + "</td>";

                    bodyTable += "  <td class='text-left align-middle'>POLI EKSEKUTIF</td>";


                    bodyTable += "  <td class='text-left align-middle'>" + namaUnit + "</td>";

                    bodyTable += "  <td class='text-left align-middle'>" + noKwitansi + "</td>";

                    bodyTable += "  <td class='text-end  align-middle'>" + bpBiaya + "</td>";

                    bodyTable += "  <td class='align-middle'> ";
                    bodyTable += "    <button class='btn btn-danger' data-toggle='tooltip' data-placement='top' title='Cetak Kwitansi' onclick='myFunctionTampilkanKwitansi(`" + noKwitansi + "`)' > <i class='bi bi-printer-fill text-white'> </i></button>";
                    bodyTable += "    <button class='btn btn-success' data-toggle='tooltip' data-placement='top' title='Cetak Rincian' onclick='myFunctionTampilkanRincian(`" + noKwitansi + "`)' > <i class='bi bi-filter-square text-white'> </i></button>";
                    bodyTable += "</td>";

                    bodyTable += "</tr>";
                }

                myTable += "<div class='table-responsive mt-2 mb-0'>";
                myTable += "  <table class='table table-bordered' id='dataTable' width='100%'>";
                myTable += "    <thead class='bg-light'>";
                myTable += "      <tr class='tab' style='height: 50px;'>";
                myTable += "        <th class='text-center align-middle' width='3%'>NO</th>";
                myTable += "        <th class='text-center align-middle' width='10%'>TANGGAL</th>";
                myTable += "        <th class='text-center align-middle' width='15%'>PEMBAYARAN</th>";
                myTable += "        <th class='text-center align-middle' width='25%'>DOKTER</th>";
                myTable += "        <th class='text-center align-middle' width='10%'>NO KWITANSI</th>";
                myTable += "        <th class='text-center align-middle' width='10%'>NOMINAL</th>";
                myTable += "        <th class='text-center align-middle' width='8%'>TOOLS</th>";
                myTable += "      </tr>";
                myTable += "    </thead>";
                myTable += "    <tbody>";
                myTable += bodyTable;
                myTable += "    </tbody>";
                myTable += "  </table>";
                myTable += "</div>";

                $("#KwitansiPasien").html(myTable);

            } else {
                var pesan = "";
                pesan += "<div class='alert alert-danger fade show text-center mb-4' role='alert'>";
                pesan += "  Tidak ditemukan data kwitansi pasien dengan No. RM <b>" + noRM + "</b>";
                pesan += "</div>";
                $("#KwitansiPasien").html(pesan);
            }
            $("#headerBiodataPasien").removeClass("d-none");
            $("#bodyBiodataPasien").removeClass("d-none");

        }

        function myFunctionTampilkanKwitansi(kwitansi) {
            var tahunSekarang = $("#tahunSekarang").val();
            window.open("pdf_kwitansi.php?nomor=" + kwitansi + '&tahunSekarang=' + tahunSekarang, "_BLANK");
        }

        function myFunctionTampilkanRincian(kwitansi) {
            var tahunSekarang = $("#tahunSekarang").val();
            window.open("pdf_rincian.php?nomor=" + kwitansi  + '&tahunSekarang=' + tahunSekarang, "_BLANK");

        }
    </script>
</body>

</html>