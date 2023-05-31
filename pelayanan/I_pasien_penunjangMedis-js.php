<div class="row g-3 mb-4">
  <div class="col-md-6 col-lg-2">
    <label for="unitTujuan" class="form-label">Unit Tujuan</label>
    <select class="form-select formPenunjang" name="unitTujuan" id="unitTujuan">
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
    <label for="dokterPengirim" class="form-label">Dokter Pengirim</label>
    <select class="form-select formPenunjang" name="dokterPengirim" id="dokterPengirim">
      <option value='<?=$NIPDPJP?>'><?=$namaDPJP?></option>
    </select>
  </div>
  <div class="col-md-6 col-lg-3">
    <label for="diagnosaPenunjang" class="form-label">Diagnosa</label>
    <select class="form-select formPenunjang" name="diagnosaPenunjang" id="diagnosaPenunjang"></select>
  </div>
  <div class="col-md-6 col-lg-2">
    <label for="tarifPenunjang" class="form-label">Tarif</label>
    <select class="form-select formPenunjang" name="tarifPenunjang" id="tarifPenunjang">
      <option value="2" selected>VIP</option>
    </select>                      
  </div>
  <div class="col-md-6 col-lg-2">
    <label class="form-label">Cito</label><br>
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="radio" name="radio-cito" id="citoVal1" value="Y">
      <label class="form-check-label" for="citoVal1">Ya</label>
    </div>
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="radio" name="radio-cito" id="citoVal2" value="" checked>
      <label class="form-check-label" for="citoVal2">Tidak</label>
    </div>                  
  </div>
  <div class="col-md-6 col-lg-1 align-self-end">
    <button class="btn btn-primary w-100" type="submit" id="tombolAutentikasiPenunjang" onclick="konfirmasiTambahPenunjang()" disabled>Simpan</button>
  </div>
</div>

<div id="daftarPenunjang"></div>

<script>
  // ===========================================================================================
  // penunjang medis
  // ===========================================================================================
  // tampilkan list penunjang
  function penunjangPasien() {
    $("#diagnosaPenunjang").html("");
    $("#tab-notifikasi").html("");
    loading("#daftarPenunjang");
    var episode = <?= $pasien["episode"] ?>;
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
      var pesan = "Tidak ditemukan Penunjang pasien dengan No. RM <b> <?= $pasien["noRM"] ?></b>";
      notifikasi("#daftarPenunjang", 0, pesan);
    }
  }
  
  // ===========================================================================================
  $("#diagnosaPenunjang").select2({
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
    var unitTujuan = $("#unitTujuan").val();
    var dokterPengirim = $("#dokterPengirim").val();
    var diagnosaPenunjang = $("#diagnosaPenunjang").val();
    var tarifPenunjang = $("#tarifPenunjang").val();
    var cito = $("#radio-cito").val();
    if (unitTujuan != "" && dokterPengirim != "" && diagnosaPenunjang != "" && tarifPenunjang != "") {
      $("#tombolAutentikasiPenunjang").prop('disabled', false);
    } else {
      $("#tombolAutentikasiPenunjang").prop('disabled', true);
    }
  });

  // tambah penunjang
  function konfirmasiTambahPenunjang(){
    // $("#passwordAksi").val("");
    // $("#tombolAutentikasiAksi").attr("disabled","true");
    // $("#tombolAutentikasiAksi").attr("onclick","tambahPenunjang()");
    // $("#modalKonfirmasiAksi").modal("show");
    tambahPenunjang();
  }
  function tambahPenunjang() {
    $("#modalKonfirmasiAksi").modal("hide");
    $.ajax({
      url: 'pasienInput.php?orderPenunjang=true',
      type: 'POST',
      data: {
        kodeMutasiPasien: <?= $kodeMutasiPasien ?>,
        unitTujuan: $("#unitTujuan").val(),
        dokterPengirim: $("#dokterPengirim").val(),
        diagnosaPenunjang: $("#diagnosaPenunjang").val(),
        tarifPenunjang: $("#tarifPenunjang").val(),
        citoPenunjang: $("input[name='radio-cito']:checked").val()
      },
      success: function(msg) {
        msg = JSON.parse(msg);
        if (msg["code"] != "") {
          penunjangPasien();
          notifikasi("#tab-notifikasi", msg["code"], msg["pesan"]);
        }
      }               
    });
  }
</script>