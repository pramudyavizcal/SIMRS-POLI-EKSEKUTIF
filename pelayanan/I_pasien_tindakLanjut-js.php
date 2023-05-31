<div class="row mb-4" id="pilihanTindakLanjut">
  <div class="col-12 d-flex justify-content-center mb-3">
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="radio" name="radio-lanjut" id="pulang" value="Pulang" checked>
      <label class="form-check-label" for="pulang">Pulang</label>
    </div>
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="radio" name="radio-lanjut" id="rawatInap" value="Rawat Inap">
      <label class="form-check-label" for="rawatInap">Rawat Inap</label>
    </div>
  </div>
  <div class="col-12 col-md-3 col-lg-2 mx-auto">
    <button class="btn btn-primary w-100" type="submit" id="tombolSimpan" onclick="lanjutSubmit()">Simpan</button>
  </div>
</div>

<script>
  // ===========================================================================================
  // Tindakan Lanjut
  // ===========================================================================================
  function lanjutSubmit(){
    $.ajax({
      url: 'pasienInput.php?tindakLanjut=true',
      type: 'POST',
      data: {
        kodeMutasiPasien: <?= $kodeMutasiPasien ?>,
        kodeEpisodePasien: <?= $pasien["episode"] ?>,
        kodePetugas: '<?= $user_nip ?>',
        radioLanjut:  $("input[name='radio-lanjut']:checked").val()
      },
      success: function(msg) {
        msg = JSON.parse(msg);
        if (msg["code"] == 1) {
          location.replace("I_antrian-js.php");
        } else if (msg["code"] == 0) {
          notifikasi("#tab-notifikasi", 0, msg["pesan"]);
        }
      }               
    });
  }
</script>