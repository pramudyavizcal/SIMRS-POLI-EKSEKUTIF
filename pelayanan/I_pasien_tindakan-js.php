<div class="row g-3 mb-4">
  <div class="col-md-6 col-lg-3">
    <label for="dokterTindakan" class="form-label">Dokter</label>
    <select class="form-select formTindakan" id="dokterTindakan" name="dokterTindakan">
      <option value="<?=$NIPDPJP?>" selected><?=$namaDPJP?></option>
    </select>
  </div>
  <div class="col-md-6 col-lg-3">
    <label for="perawatTindakan" class="form-label">Perawat</label>
    <select class="form-select formTindakan selectpicker" id="perawatTindakan" name="perawatTindakan"  multiple placeholder="Pilih Perawat ...">
      <?php  
        $query = "SELECT nip, nama FROM _pegawai WHERE nKodeFungsional IN (10,11,12,15,20,22,23,24,25) AND status_aktif = 1  ORDER BY nama;";
        $conn->query($query);
        $perawat = $conn->resultSet();
        foreach ($perawat as $perawat) {
          $nip = $perawat['nip'];
          $nama = $perawat['nama'];
          echo "<option value='$nip'>$nama</option>";
        }
      ?>
    </select>
  </div>
  <div class="col-md-8 col-lg-4">
    <label for="kodeTindakan" class="form-label">Tindakan</label>
    <select class="form-select formTindakan" id="kodeTindakan" name="kodeTindakan">
      option 
    </select>
  </div>
  <div class="col-md-2 col-lg-1">
    <label for="jumlahTindakan" class="form-label">Jumlah</label>
    <input type="number" class="form-control formTindakan" name="jumlahTindakan" id="jumlahTindakan" min="1">                      
  </div>
  <div class="col-md-2 col-lg-1 mt-md-5">
    <button class="btn btn-primary w-100" type="submit" id="tombolTambahTindakan" onclick="konfirmasiTambahTindakan()" disabled>Tambah</button>
  </div>
</div>

<div id="daftarTindakan" class="mb-4"></div>

<script>
  // ===========================================================================================
  // Tindakan
  // ===========================================================================================
  $("#perawatTindakan").select2({
    theme: "bootstrap-5",
    placeholder: "Pilih Perawat..."
  });
  // tampilkan list tindakan 
  function tindakanPasien(){
    var kodeDokterUser = <?= $user_kodeDokter ?>;
    loading("#daftarTindakan");
    $("#tab-notifikasi").html("");
    $("#kodeTindakan").html("");
    $("#jumlahTindakan").val("");

    $.ajax({
      url: 'cariData.php?cariTindakanPasien=true',
      type: 'POST',
      data: {
        noRM:  <?= $pasien["noRM"] ?>,
        episode: <?= $pasien["episode"] ?>
      },
      success: function(msg) {
        msg = JSON.parse(msg);
        var jumlah = msg.length;

        if (jumlah > 0) {
          bodyTable = "";
          for (var i=0; i<jumlah; i++) {
            bodyTable += "<tr class='bg-primary bg-opacity-10 fw-bold' style='height: 50px;'>";
            bodyTable += "  <td class='align-middle text-center' colspan='7'>"+msg[i]["namaUnit"]+"</td>";
            bodyTable += "</tr>";
            var tindakan = msg[i]["tindakan"];
            for (var j=0; j<tindakan.length; j++) {
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
              bodyTable += "  <td class='align-middle text-center py-0'>";
              if (regPoli == null && kodeDokterUser == msg[i]["kodeUnit"]) {
                bodyTable += "  <button class='btn btn-danger mx-1 my-0' data-toggle='tooltip' data-placement='top' title='Hapus' onclick='konfirmasiHapusTindakan("+kodeTindakan+")'><i class='bi bi-trash text-white' style='font-size: 15px;'></i></button>";
              }
              bodyTable += "  </td>";
              bodyTable += "</tr>";
            }  
          }  
          // set tabel
          myTable = "";
          myTable += "<div class='table-responsive' style='overflow-x:hidden;'>";
          myTable += "  <table class='table table-bordered table-hover tabelTindakan' width='100%'>";
          myTable += "    <thead class='bg-light'>";
          myTable += "      <tr style='height: 50px;'>";
          myTable += "        <th class='text-center align-middle' width='5%'>NO</th>";
          myTable += "        <th class='text-center align-middle' width='10%'>TANGGAL</th>";
          myTable += "        <th class='text-center align-middle' width='30%'>TINDAKAN</th>";
          myTable += "        <th class='text-center align-middle' width='20%'>DOKTER</th>";
          myTable += "        <th class='text-center align-middle' width='20%'>PERAWAT</th>";
          myTable += "        <th class='text-center align-middle' width='5%'>JUMLAH</th>";
          myTable += "        <th class='text-center align-middle' width='10%'>TOOLS</th>";
          myTable += "      </tr>";
          myTable += "    </thead>";
          myTable += "    <tbody>";
          myTable += bodyTable;            
          myTable += "    </tbody>";
          myTable += "  </table>";
          myTable += "</div>";
          $("#daftarTindakan").html(myTable);
        } else {
          var pesan =  "Tidak ditemukan tindakan pasien dengan No. RM <b><?= $pasien["noRM"] ?></b>";
          notifikasi("#daftarTindakan", 0, pesan);
        }
      }               
    });
  }

  // ===========================================================================================
  // list tindakan
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

  // tambah tindakan
  function konfirmasiTambahTindakan(){
    // $("#passwordAksi").val("");
    // $("#tombolAutentikasiAksi").attr("disabled","true");
    // $("#tombolAutentikasiAksi").attr("onclick","tambahTindakan()");
    // $("#modalKonfirmasiAksi").modal("show");
    tambahTindakan();
  }

  function tambahTindakan(){
    $("#modalKonfirmasiAksi").modal("hide");
    $.ajax({
      url: 'pasienInput.php?tambahTindakan=true',
      type: 'POST',
      data: {
        kodeMutasiPasien: <?= $kodeMutasiPasien ?>,
        dokterTindakan: $("#dokterTindakan").val(),
        perawatTindakan: $("#perawatTindakan").val(),
        kodeTindakan: $("#kodeTindakan").val(),
        jumlahTindakan: $("#jumlahTindakan").val()
      },
      success: function(msg) {
        msg = JSON.parse(msg);
        if (msg["code"] != "") {
          tindakanPasien();
          notifikasi("#tab-notifikasi", msg["code"], msg["pesan"]);
        }
      }               
    });
  }

  // hapus tindakan
  function konfirmasiHapusTindakan(kodeTindakan){
    // $("#passwordAksi").val("");
    // $("#tombolAutentikasiAksi").attr("disabled","true");
    // $("#tombolAutentikasiAksi").attr("onclick","hapusTindakan("+kodeTindakan+")");
    // $("#modalKonfirmasiAksi").modal("show");
    hapusTindakan(kodeTindakan);
  }

  function hapusTindakan(kodeTindakan){
    $("#modalKonfirmasiAksi").modal("hide");
    $.ajax({
      url: 'pasienInput.php?hapusTindakan=true',
      type: 'POST',
      data: {
        kodeMutasiPasien: <?= $kodeMutasiPasien ?>,
        kodeTindakan: kodeTindakan
      },
      success: function(msg) {
        msg = JSON.parse(msg);
        if (msg["code"] != "") {
          tindakanPasien();
          notifikasi("#tab-notifikasi", msg["code"], msg["pesan"]);
        }
      }               
    });
  }

</script>