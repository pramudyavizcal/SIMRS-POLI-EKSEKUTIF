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
        <h1>Riwayat Kunjungan Pasien</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item">Pasien</li>
            <li class="breadcrumb-item active">Riwayat</li>
          </ol>
        </nav>
      </div>

      <section class="section">
        <div class="row">
          <div class="col-12">

            <div class="card">
              <div class="card-header bg-light">
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
              </div>

              <!-- riwayat -->
              <div class="card-header bg-light border border-start-0 border-end-0 d-none" id="headerRiwayatPasien">
                <h5 class="card-title my-0 py-0">Riwayat Kunjungan Pasien</h5>
              </div>
              <div class="card-body d-none pt-4 pb-0" id="bodyRiwayatPasien">
                <div id="riwayatPasien"></div>
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
      var noRM = $("#noRM").val();
      $(document).ready(function(){
        // auto cari biodara
        if (noRM != "") {
          cariBiodata();
        }
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
    </script>

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
          $("#riwayatPasien").html("");
          $("#headerRiwayatPasien").addClass("d-none");
          $("#bodyRiwayatPasien").addClass("d-none");
        } else {
          loading("#biodataPasien");
          $.get("cariData.php?cariBiodataPasien=true&noRM="+noRM, tampilkanBiodata);
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
          biodata += "      <div class='col-8 col-lg-9'>"+data['noRM']+"</div>";
          biodata += "    </div>";
          biodata += "    <div class='row mb-3'>";
          biodata += "      <div class='col-4 col-lg-3 d-flex justify-content-between'><span>Nama</span><span>:</span></div>";
          biodata += "      <div class='col-8 col-lg-9'>"+data['nama']+"</div>";
          biodata += "    </div>";
          biodata += "    <div class='row mb-3'>";
          biodata += "      <div class='col-4 col-lg-3 d-flex justify-content-between'><span>Alamat</span><span>:</span></div>";
          biodata += "      <div class='col-8 col-lg-9'>"+data['alamatLengkap']+"</div>";
          biodata += "    </div>";
          biodata += "  </div>";
          biodata += "  <div class='col-md-6'>";
          biodata += "    <div class='row mb-3'>";
          biodata += "      <div class='col-4 d-flex justify-content-between'><span>Jenis Kelamin</span><span>:</span></div>";
          biodata += "      <div class='col-8'>"+data['jenisKelamin']+"</div>";
          biodata += "    </div>";
          biodata += "    <div class='row mb-3'>";
          biodata += "      <div class='col-4 d-flex justify-content-between'><span>Tempat, Tanggal Lahir</span><span>:</span></div>";
          biodata += "      <div class='col-8'>"+data['tempatLahir']+", "+data['tglLahir']+"</div>";
          biodata += "    </div>";
          biodata += "    <div class='row mb-3'>";
          biodata += "      <div class='col-4 d-flex justify-content-between'><span>Umur</span><span>:</span></div>";
          biodata += "      <div class='col-8'>"+data['umur']+"</div>";
          biodata += "    </div>";
          biodata += "  </div>";
          biodata += "</div>";
          
          $("#biodataPasien").html(biodata);
          cariRiwayat();
        } else {
          var pesan = "";
          pesan += "<div class='alert alert-danger fade show text-center mb-4' role='alert'>";
          pesan += "  Tidak ditemukan pasien dengan No. RM <b>"+noRM+"</b>";
          pesan += "</div>";
          $("#biodataPasien").html(pesan);
          // kosongi riwayat
          $("#headerRiwayatPasien").addClass("d-none");
          $("#bodyRiwayatPasien").addClass("d-none");
          $("#riwayatPasien").html(pesan);
        }
      }

      function cariRiwayat() {
        var noRM = $("#noRM").val();

        $("#headerRiwayatPasien").removeClass("d-none");
        $("#bodyRiwayatPasien").removeClass("d-none");
        if (noRM == "") {
          var pesan = "";
          pesan += "<div class='alert alert-danger fade show text-center mb-4' role='alert'>";
          pesan += "  Masukan No. RM dalam kolom pencarian";
          pesan += "</div>";
          $("#riwayatPasien").html(pesan);
        } else {
          loading("#riwayatPasien");
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
          myTable += "        <th class='text-center align-middle' width='15%'>unit</th>";
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

          $("#riwayatPasien").html(myTable);
          $("#dataTable").DataTable();
        } else {
          var pesan = "";
          pesan += "<div class='alert alert-danger fade show text-center mb-4' role='alert'>";
          pesan += "  Tidak ditemukan riwayat kunjungan pasien dengan No. RM <b>"+noRM+"</b>";
          pesan += "</div>";
          $("#riwayatPasien").html(pesan);
        }

        $("#headerBiodataPasien").removeClass("d-none");
        $("#bodyBiodataPasien").removeClass("d-none");
      }
    </script>
  </body>

</html>