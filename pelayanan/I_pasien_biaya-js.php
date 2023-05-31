

<script>
  // ===========================================================================================
  // Biaya
  // ===========================================================================================
  function biayaPasien(){
    var noRM = <?= $pasien["noRM"] ?>;
    var episode = <?= $pasien["episode"] ?>;
    var totalBiaya = 0;

    loading("#nav-biaya");
    $("#tab-notifikasi").html("");
    $.ajax({
      url: 'cariData.php?cariBiaya=true',
      type: 'POST',
      data: {
        noRM:  <?= $pasien["noRM"] ?>,
        episode: <?= $pasien["episode"] ?>
      },
      success: function(data) {
        data = JSON.parse(data);
        // console.log(data);
        var jumlahTindakan = data["tindakan"].length;
        var jumlahFarmasi = data["farmasi"].length;
        var statusPembayaran = 1;
        
        var bodyTable = "";
        if (jumlahTindakan + jumlahFarmasi > 0) {
          // bagian tindakan
          var totalTindakan = 0;
          for (var i = 0; i < jumlahTindakan; i++) {
            var tindakan = data["tindakan"][i];
            if(tindakan["Idz"] == 1){
              // console.log(data[i]["Idz"]);
              bodyTable += "<tr class='bg-primary bg-opacity-10 fw-bold' style='height: 50px;'>";
              bodyTable += "  <td class='align-middle' colspan='6'>"+tindakan['szTarif']+"</td>";
              bodyTable += "</tr>";
            } else {
              totalTindakan += parseInt(tindakan["curBiaya"]);
              bodyTable += "<tr>";
              bodyTable += "  <td class='align-middle'>" + tindakan["szTarif"] + "</td>";
              bodyTable += "  <td class='align-middle'>" + tindakan["qty"] + "</td>";
              bodyTable += "  <td class='align-middle'>" + tindakan["curTarif"] + "</td>";
              bodyTable += "  <td class='align-middle text-end'>" + tindakan["curBiaya"].replace(/\B(?=(\d{3})+(?!\d))/g, ",") + "</td>";
              bodyTable += "  <td class='align-middle text-center'>";
              if((tindakan["Idx"] == 1 ) && (tindakan["Idz"] != 1)){
                bodyTable += "<font color='blue'>Sudah diverifikasi</font>";
              } else {
                if((tindakan["nKodeBayar"] == "T")||(tindakan["nKodeBayar"] == "N")){
                  if(tindakan["nLunas"] == 1){ 
                    bodyTable += "Lunas [" + tindakan["nRegister"] + "]"; //nNoregPoli
                  } else {
                    if(tindakan["nRegister"]){
                      bodyTable += "<font color='blue'>Sudah diverifikasi, belum dibayar</font>";
                    } else {
                      statusPembayaran = 0;
                      bodyTable += "<font color='red'>Belum diverifikasi</font>";
                    }
                  }
                } else {
                  if(tindakan["nRegister"]){
                    bodyTable += "<font color='blue'>Sudah diverifikasi</font>";
                  } else {
                    statusPembayaran = 0;
                    bodyTable += "<font color='red'>Belum diverifikasi</font>";
                  }
                } 
              }
              bodyTable += "  </td>";
              bodyTable += "</tr>";
            } 
          }
          //Bagian Farmasi
          var totalFarmasi = 0;
          bodyTable += "<tr class='bg-primary bg-opacity-10 fw-bold' style='height: 50px;'>";
          bodyTable += "  <td class='align-middle' colspan='6'>"+"Farmasi"+"</td>";
          bodyTable += "</tr>";
          for (var i = 0; i < jumlahFarmasi; i++) {
            var farmasi = data["farmasi"][i];
            totalFarmasi += parseInt(farmasi["curFarmasi"]);
            bodyTable += "<tr>";
            bodyTable += "  <td class='align-middle' colspan='3'>" + farmasi["tFarmasi"] + ":" + farmasi["szNomorFarmasi"] + "</td>";
            bodyTable += "  <td class='align-middle text-end'>" + farmasi["curFarmasi"].replace(/\B(?=(\d{3})+(?!\d))/g, ",") + "</td>";
            bodyTable += "  <td class='align-middle text-center'>";
            if(farmasi["nKodeLunasFarmasi"] == 1){
              bodyTable += "Lunas [" + farmasi["szNoBayar"] + "]";
            } else {
              bodyTable += "<font color='blue'>Sudah diverifikasi</font>";								
            } 
            bodyTable +=  "</td>";
            bodyTable += "</tr>";
          }

          // total biaya
          totalBiaya = (totalTindakan+totalFarmasi).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
          bodyTable += "<tr class='bg-primary bg-opacity-10 fw-bold' 'pull-right m-t-30' style='height: 50px;'>";
          bodyTable += "  <td class='align-center text-center' colspan='3'>"+ "Total" +"</td>";
          bodyTable += "  <td class='align-center text-end'>";
          bodyTable +=       totalBiaya;
          bodyTable += "  </td>";
          bodyTable += "  <td class='align-center text-end'></td>";
          bodyTable += "</tr>";
            
            var myTable = "";
            myTable += "<div class='table-responsive' style='overflow-x:hidden;'>";
            myTable += "  <table class='table table-bordered table-hover'>";
            myTable += "    <thead class='bg-light'>";
            myTable += "      <tr style='height: 50px;'>";
            myTable += "        <th class='text-center align-middle' width='40%'>URAIAN</th>";
            myTable += "        <th class='text-center align-middle' width='8%'>QTY</th>";
            myTable += "        <th class='text-center align-middle' width='15%'>TARIF</th>";
            myTable += "        <th class='text-center align-middle'width='15%'>BIAYA</th>";
            myTable += "        <th class='text-center align-middle' width='30%'>STATUS</th>";
            myTable += "      </tr>";
            myTable += "    </thead>";
            myTable += "    <tbody>";
            myTable += bodyTable;
            myTable += "    </tbody>";
            myTable += "  </table>";
            myTable += "</div>";
            
            if (statusPembayaran == 0) {
              myTable += "<div class='text-end'>";
              myTable += "  <div class='input-group-btn' style='margin-bottom: 20px'>";
              myTable += "    <button class='btn btn-danger' id='tombolVerifikasi' 'data-toggle='tooltip' data-placement='top' title='verifikasi' onclick='konfirmasiBiaya()'><i class='fas fa-check'></i>Verifikasi</button>";
              myTable += "  </div>";
              myTable += "</div>";
          }
          $("#nav-biaya").html(myTable);
          $("#perkiraanBiaya").html(totalBiaya);
          totalPerkiraanBiaya();
        } else {
          var pesan =  "Tidak ditemukan daftar biaya untuk pasien dengan No. RM <b><?= $pasien["noRM"] ?></b>";
          notifikasi("#nav-biaya", 0, pesan);
        }
      }  
                   
    });
  }

// ====================================================================================================================

  function konfirmasiBiaya(){
    // $("#passwordAksi").val("");
    // $("#tombolAutentikasiAksi").attr("disabled","true");
    // $("#tombolAutentikasiAksi").attr("onclick","verifikasiBiayaPasien()");
    // $("#modalKonfirmasiAksi").modal("show");
    verifikasiBiayaPasien();
  }

  function verifikasiBiayaPasien(){
    // $("#modalKonfirmasiAksi").modal("hide");
    $('#tombolVerifikasi').prop('disabled', true);
    loading("#tab-notifikasi");
    $.ajax({
      url: 'pasienInput.php?verifBiaya=true',
      type: 'POST',
      data: {
        kodeMutasiPasien: <?= $kodeMutasiPasien ?>,
        namaPetugas: '<?= $user_nama ?>',
        kodePetugas: '<?= $user_nip ?>',
        kodePoli: <?= $user_kodeDokter ?>,
        nKodeBayar: 'R'
      },
      success: function(msg) {
        // console.log(msg); return false;
        msg = JSON.parse(msg);
        if (msg["code"] != "") {
          notifikasi("#tab-notifikasi", msg["code"], msg["pesan"]);
          biayaPasien();
        }
      }               
    });
  }

</script>  
