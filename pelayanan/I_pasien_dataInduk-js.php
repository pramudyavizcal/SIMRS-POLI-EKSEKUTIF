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

<script>
  // ===========================================================================================
  // data induk
  // ===========================================================================================
  function dataIndukPasien(){
    loading("#loadingDataInduk");
    $("#tab-notifikasi").html("");
    var noRM = <?= $pasien["noRM"] ?>;
    $("#biodataPasien").addClass("d-none");
    $.get("cariData.php?cariDataIndukPasien=true&noRM="+noRM, tampilkanDataInduk);
  }
  function tampilkanDataInduk(data) {
    $("#loadingDataInduk").html("");
    $("#biodataPasien").removeClass("d-none");
    
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
  }
</script>