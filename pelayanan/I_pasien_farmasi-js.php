<script>
  // ===========================================================================================
  // farmasi
  // ===========================================================================================
  function farmasiPasien(){
    var noRM = <?= $pasien["noRM"] ?>;
    var episode = <?= $pasien["episode"] ?>;
    loading("#nav-farmasi");
    $.ajax({
      url: 'cariData.php?cariFarmasi=true',
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
            bodyTable += "  <td class='align-middle' colspan='4'>";
            bodyTable += "    <div class='row text-center'>";
            bodyTable += "      <div class='col'>"+msg[i]['tanggal']+"</div>";
            bodyTable += "      <div class='col'>"+msg[i]['unit']+"</div>";
            bodyTable += "      <div class='col'>"+msg[i]['noKwitansi']+"</div>";
            bodyTable += "    </div>";
            bodyTable += "  </td>";
            bodyTable += "</tr>";
            var obat = msg[i]["obat"];
            for (var j=0; j<obat.length; j++) {
              bodyTable += "<tr>";
              bodyTable += "  <td class='align-middle text-center'>" + (j+1) + "</td>";
              bodyTable += "  <td class='align-middle'>" + obat[j]["nama"] + "</td>";
              bodyTable += "  <td class='align-middle'>" + obat[j]["jumlah"] + "</td>";
              bodyTable += "  <td class='align-middle'>" + obat[j]["aturanPakai"] + "</td>";
              bodyTable += "</tr>";
            }  
          }  
          // set tabel
          myTable = "";
          myTable += "<div class='table-responsive' style='overflow-x:hidden;'>";
          myTable += "  <table class='table table-bordered table-hover'>";
          myTable += "    <thead class='bg-light'>";
          myTable += "      <tr style='height: 50px;'>";
          myTable += "        <th class='text-center align-middle' width='5%'>NO</th>";
          myTable += "        <th class='text-center align-middle' width='40%'>NAMA OBAT</th>";
          myTable += "        <th class='text-center align-middle' width='10%'>JUMLAH</th>";
          myTable += "        <th class='text-center align-middle' width='45%'>ATURAN PAKAI</th>";
          myTable += "      </tr>";
          myTable += "    </thead>";
          myTable += "    <tbody>";
          myTable += bodyTable;            
          myTable += "    </tbody>";
          myTable += "  </table>";
          myTable += "</div>";
          $("#nav-farmasi").html(myTable);
        } else {
          var pesan =  "Tidak ditemukan daftar obat untuk pasien dengan No. RM <b><?= $pasien["noRM"] ?></b>";
          notifikasi("#nav-farmasi", 0, pesan);
        }
      }               
    });
  }
</script>