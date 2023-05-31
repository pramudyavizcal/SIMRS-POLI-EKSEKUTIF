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


<script>
  // ===========================================================================================
  // DPJP & diagnosa
  // ===========================================================================================
  // tampilkan list diagnosa 
  function diagnosaPasien(){
    loading("#daftarDiagnosa");
    $("#kodeDiagnosa").html("");
    $("#tab-notifikasi").html("");
    var noRM = <?= $pasien["noRM"] ?>;
    var episode = <?= $pasien["episode"] ?>;
    $.get("cariData.php?cariDiagnosaPasien=true&noRM="+noRM+"&episode="+episode, tampilkanDiagnosaPasien);
  }

  function tampilkanDiagnosaPasien(data){
    var kodeDokterUser = <?= $user_kodeDokter ?>;
    data = JSON.parse(data);
    var jumlah = data.length;

    var myTable = "";
    var bodyTable = "";
    if (jumlah > 0) {
      $("#kategoriDiagnosa").html("<option value='S' selected>Penyerta</option>");
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
      $("#kategoriDiagnosa").html("<option value='U' selected>Utama</option>");
      var pesan = "Tidak ditemukan Diagnosa pasien dengan No. RM <b><?= $pasien["noRM"] ?></b>";
      notifikasi('#daftarDiagnosa', 0, pesan);
    }
  }

  // ===========================================================================================
  $("#kodeDiagnosa").select2({
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
    var dokterDiagnosa = $("#dokterDiagnosa").val();
    var kategoriDiagnosa = $("#kategoriDiagnosa").val();
    var diagnosa = $("#kodeDiagnosa").val();

    if (dokterDiagnosa!="" && kategoriDiagnosa!="" && diagnosa!="") {
      $("#tombolTambahDiagnosa").prop('disabled', false);
    } else {
      $("#tombolTambahDiagnosa").prop('disabled', true);
    }
  });

  // tambah diagnosa
  function konfirmasiTambahDiagnosa(){
    $("#passwordAksi").val("");
    $("#tombolAutentikasiAksi").attr("disabled","true");
    $("#tombolAutentikasiAksi").attr("onclick","tambahDiagnosa()");
    $("#modalKonfirmasiAksi").modal("show");
  }
  
  function tambahDiagnosa(){
    $("#modalKonfirmasiAksi").modal("hide");
    $.ajax({
      url: 'pasienInput.php?tambahDiagnosa=true',
      type: 'POST',
      data: {
        kodeMutasiPasien: <?= $kodeMutasiPasien ?>,
        dokterDiagnosa: $("#dokterDiagnosa").val(),
        perawatDiagnosa: '<?= $user_nip ?>',
        kategoriDiagnosa: $("#kategoriDiagnosa").val(),
        kodeDiagnosa: $("#kodeDiagnosa").val()
      },
      success: function(msg) {
        msg = JSON.parse(msg);
        if (msg["code"] != "") {
          diagnosaPasien();
          notifikasi("#tab-notifikasi", msg["code"], msg["pesan"]);
        }
      }               
    });
  }

  // hapus diagnosa
  function konfirmasiHapusDiagnosa(kodeDiagnosa){
    $("#passwordAksi").val("");
    $("#tombolAutentikasiAksi").attr("disabled","true");
    $("#tombolAutentikasiAksi").attr("onclick","hapusDiagnosa("+kodeDiagnosa+")");
    $("#modalKonfirmasiAksi").modal("show");
  }
  function hapusDiagnosa(kodeDiagnosa){
    $("#modalKonfirmasiAksi").modal("hide");
    $.ajax({
      url: 'pasienInput.php?hapusDiagnosa=true',
      type: 'POST',
      data: {
        kodeMutasiPasien: <?= $kodeMutasiPasien ?>,
        kodeDiagnosa: kodeDiagnosa
      },
      success: function(msg) {
        msg = JSON.parse(msg);
        if (msg["code"] != "") {
          diagnosaPasien();
          notifikasi("#tab-notifikasi", msg["code"], msg["pesan"]);
        }
      }               
    });
  }
</script>