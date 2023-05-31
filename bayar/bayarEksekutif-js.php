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

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Pembayaran Tagihan Pasien</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item">Pasien</li>
          <li class="breadcrumb-item active">Poli Eksekutif</li>
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
            </div>

            <!-- Rincian Biaya -->
            <div class="card-header bg-light border border-start-0 border-end-0 d-none" id="headerTagihanPasien">
              <h5 class="card-title my-0 py-0">Rincian Biaya Pasien</h5>
            </div>
            <div class="card-body d-none pt-4 pb-0" id="bodyTagihanPasien">
              <div id="TagihanPasien"></div>
            </div>
            <div class="card-body d-none pt-0 pb-4" id="bodyTombolBayar">
              <div id="tombolBayar"></div>
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
        $("#TagihanPasien").html("");
        $("#headerTagihanPasien").addClass("d-none");
        $("#bodyTagihanPasien").addClass("d-none");
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
                cariTagihan(data['nEpisode']);
            } else {
                var pesan = "";
                pesan += "<div class='alert alert-danger fade show text-center mb-4' role='alert'>";
                pesan += "  Tidak ditemukan pasien dengan No. RM <b>" + noRM + "</b>";
                pesan += "</div>";
                $("#biodataPasien").html(pesan);
                // kosongi riwayat
                $("#headerTagihanPasien").addClass("d-none");
                $("#bodyTagihanPasien").addClass("d-none");
                $("#TagihanPasien").html(pesan);
            }
        }

        function cariTagihan(episode) {
            var noRM = $("#noRM").val();

            $("#headerTagihanPasien").removeClass("d-none");
            $("#bodyTagihanPasien").removeClass("d-none");
            $("#bodyTombolBayar").removeClass("d-none");
            $("#modalNonTunai").removeClass("d-none");


            if (noRM == "") {
                var pesan = "";
                pesan += "<div class='alert alert-danger fade show text-center mb-4' role='alert'>";
                pesan += "  Masukan No. RM dalam kolom pencarian";
                pesan += "</div>";
                $("#TagihanPasien").html(pesan);
            } else {
                var pesan = "";
                pesan += "<div class='d-flex justify-content-center mb-4'>";
                pesan += "  <div class='spinner-border' role='status'>";
                pesan += "    <span class='visually-hidden'>Loading...</span>";
                pesan += "  </div>";
                pesan += "</div>";
                $("#TagihanPasien").html(pesan);

                $.get("cariDataPembayaran.php?cariTagihanPasien=true&episode=" + episode + '&noRM=' + noRM, tampilkanTagihan);
            }
        }

        function tampilkanTagihan(data) {

            data = JSON.parse(data);
            var jumlah = data.length;
            console.log(data);

            var myTable = "";
            var bodyTable = "";
            var tombol = "";
            let yNonTunai = $("#selectNonTunai").val();
				            let yEDC = $("#noTransaksi").val();

            if (jumlah > 0) {
                var totBiaya = 0;
                for (var i = 0; i < jumlah; i++) {
                    var user = '<?= $user_kode ?>';
                    var kodeIdz = data[i]["Idz"];
                    var namaTarif = data[i]["szTarif"];
                    var qtyTindakan = data[i]["qty"];
                    var tarifTindakan = data[i]["curTarif"];
                    var bpTarif = String(tarifTindakan).replace(/(.)(?=(\d{3})+$)/g, '$1,');
                    var totalBiaya = data[i]["curBiaya"];
                    var bpBiaya = String(totalBiaya).replace(/(.)(?=(\d{3})+$)/g, '$1,');

                    if (totalBiaya != null && totalBiaya > 0) {
                        totBiaya += parseInt(totalBiaya);
                    }

                    bodyTable += "<tr>";
                    if (kodeIdz == 1) {
                        bodyTable += "      <tr class='table-info' style='height: 50px;'>";
                        bodyTable += "        <td class='text-left align-middle' colspan='6'><b><font color='blue'>" + namaTarif + "</font></b></td>";
                        bodyTable += "      </tr>";
                    } else {
                        bodyTable += "  <td class='text-left align-middle'>" + namaTarif + "</td>";
                        if (qtyTindakan == null) {
                            bodyTable += "  <td class='text-center align-middle'></td>";
                        } else {
                            bodyTable += "  <td class='text-center align-middle'>" + qtyTindakan + "</td>";
                        }
                        if (tarifTindakan == null) {
                            bodyTable += "  <td class='text-center align-middle'></td>";
                        } else {
                            bodyTable += "  <td class='text-end align-middle'>" + bpTarif + "</td>";
                        }
                        bodyTable += "  <td class='text-end  align-middle'>" + bpBiaya + "</td>";
                    }

                    bodyTable += "</tr>";

                }

                for (var i = 0; i < 1; i++) {
                    var curBayar = data[i]["curBayar"];
                    var bpBayar = String(curBayar).replace(/(.)(?=(\d{3})+$)/g, '$1,');

                    if (curBayar == null) {
                        bodyTable += "</tr>";
                        bodyTable += "      <tr class='table-info' style='height: 50px;'>";
                        bodyTable += "        <td class='text-left align-middle' colspan='6'><div style='float:left;'><b><font color='blue'>Farmasi</font></b></div><div style='float:right;'>" + 0 + "</div>";
                        bodyTable += "</td>"
                        bodyTable += "      </tr>"
                    } else {
                        bodyTable += "</tr>";
                        bodyTable += "      <tr class='table-info' style='height: 50px;'>";
                        bodyTable += "        <td class='text-left align-middle' colspan='6'><div style='float:left;'><b><font color='blue'>Farmasi</font></b></div><div style='float:right;'>" + bpBayar + "</div>";
                        bodyTable += "</td>"
                        bodyTable += "      </tr>"
                    }
                }

                for (var i = 0; i < 1; i++) {
                    var caraBayar = data[i]["szCaraBayar"];
                    var nKodeMutasiPasien = data[i]["nKode"];
                    var episode = data[i]["nEpisode"];
                    var nKodePoli = data[i]["nPoli"];
                    var tahunSekarang = data[i]["tahun"];
                }
                if (nKodeMutasiPasien == null) {
                    var pesan = "";
                    pesan += "<div class='alert alert-danger fade show text-center mb-4' role='alert'>";
                    pesan += "  Tidak ditemukan tagihan pasien dengan No. RM <b>" + noRM + "</b>";
                    pesan += "</div>";
                    $("#TagihanPasien").html(pesan);

                } else {

                    var total = "";
                    if (curBayar == null) {
                        total = parseInt(totBiaya);
                    } else {
                        total = parseInt(totBiaya) + parseInt(curBayar);
                    }
                    var bpTotal = String(total).replace(/(.)(?=(\d{3})+$)/g, '$1,');

                    bodyTable += "</tr>";
                    bodyTable += "      <tr class='table-light' style='height: 50px;'>";
                    bodyTable += "        <td class='text-left align-middle' colspan='6'><div style='float:right;'><b><font color='blue'>TOTAL : " + bpTotal + "</font></b></div>";
                    bodyTable += "</td>"
                    bodyTable += "      </tr>"

                    myTable += "<div class='table-responsive mt-2 mb-0'>";
                    myTable += "  <table class='table table-bordered' id='dataTable' width='100%'>";
                    myTable += "    <thead class='bg-light'>";
                    myTable += "      <tr class='tab' style='height: 50px;'>";
                    myTable += "        <th class='text-center align-middle' width='20%'>URAIAN</th>";
                    myTable += "        <th class='text-center align-middle' width='5%'>QTY</th>";
                    myTable += "        <th class='text-center align-middle' width='10%'>TARIF</th>";
                    myTable += "        <th class='text-center align-middle' width='10%'>BIAYA</th>";
                    myTable += "      </tr>";
                    myTable += "    </thead>";
                    myTable += "    <tbody>";
                    myTable += bodyTable;
                    myTable += "    </tbody>";
                    myTable += "  </table>";
                    myTable += "</div>";

                  
                    $("#TagihanPasien").html(myTable);


                    tombol += "<div class='table-responsive mt-2 mb-2 float-end align-items-center' id='tombolBayarPasien'>";
                    tombol += "<button type='button' class='btn btn-danger' id='submitTunai' onclick='submitTunai(" + nKodeMutasiPasien + ",`" + caraBayar + "`," + episode + "," + nKodePoli + "," + total + "," + tahunSekarang + ")'> Tunai </button> &nbsp;&nbsp;";
                    tombol += "<button type='button' class='btn btn-success' id='submitEDC' data-bs-toggle='modal' data-bs-target='#basicModal' > Non Tunai </button>";
                    tombol += "<div class='modal fade' id='basicModal' tabindex='-1'>";
                    tombol += "<div class='modal-dialog'>";
                    tombol += "<div class='modal-content'>";
                    tombol += "<div class='modal-header'>";
                    tombol += "<h5 class='modal-title'>Pilih Jenis Pembayaran</h5>";
                    tombol += "<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>";
                    tombol += "</div>"; 
                    tombol += "<div class='modal-body'>";
                    tombol += "<div class='form-group row'>";
							      tombol += "<div class='col-12'><select class='form-select' id='selectNonTunai'>"
                    tombol += "<option value='' selected>Pilih Jenis Pembayaran...</option>";
                    tombol += "<option value='EDC'>EDC Bank Jatim</option>";
                    tombol += "<option value='Transfer Bank Jatim'>Transfer Bank Jatim</option>";
                    tombol += "</select></div>";
                    tombol += "</div>";
                    
                    tombol += "<div class='form-group row mt-2' id='rowSelect'>";
							      tombol += "<div class='col-12'>";
								    tombol += "<input type='text' class='form-control' id='noTransaksi' placeholder='Ketik No. Transaksi disini...'>";
							      tombol += "</div>";
						        tombol += "</div>";


                    tombol += "</div>";//end modal body
                    
                    tombol += "<div class='modal-footer'>";
                    tombol += "<button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>";
                    tombol += "<button type='button' class='btn btn-primary' onclick='submitNonTunai(" + nKodeMutasiPasien + ",`" + caraBayar + "`," + episode + "," + nKodePoli + "," + total + "," + tahunSekarang + ")'>Simpan</button>";
                    tombol += "</div>";
                    tombol += "</div>";
                    tombol += "</div>";
                    tombol += "</div>";
                    tombol += "</div>";

                    $("#tombolBayar").html(tombol);
                    
                }

            }
            $("#headerBiodataPasien").removeClass("d-none");
            $("#bodyBiodataPasien").removeClass("d-none");

        }

        function submitTunai(nKodeMutasiPasien, szBayar, nEpisode, nKodePoli, curBayar, tahunSekarang) {
            $.get('bayarInputEksekutif.php?Bayar=true&nKodeMutasiPasien=' + nKodeMutasiPasien + '&szBayar=T' +
                '&nEpisode=' + nEpisode + '&curBayar=' + curBayar +
                '&tahunSekarang=' + tahunSekarang + '&nKodePoli=' + nKodePoli, doSomethingWithTunai);
            //console.log(n + "|" + nKodeMutasiPasien + "|" + szBayar + "|" + nEpisode + "|" + nKodePoli + "|" + curBayar + "|" + tahunSekarang);
        }

        function doSomethingWithTunai(data) {
            //console.log(data);
            alert(data);
            window.location.reload();
        }

        function submitNonTunai(nKodeMutasiPasien, szBayar, nEpisode, nKodePoli, curBayar, tahunSekarang) {
          var caraBayar = $("#selectNonTunai").val();
          var szCatatan = $("#noTransaksi").val();
          $.get('bayarInputEksekutif.php?Bayar=true&nKodeMutasiPasien=' + nKodeMutasiPasien + '&szBayar=N' +
                '&nEpisode=' + nEpisode + '&curBayar=' + curBayar +
                '&tahunSekarang=' + tahunSekarang + '&nKodePoli=' + nKodePoli + '&szEDC=' + caraBayar + '&szCatatan=' + szCatatan, doSomethingWithTunai);
            //console.log("its me pram");
        }

    </script>
</body>

</html>