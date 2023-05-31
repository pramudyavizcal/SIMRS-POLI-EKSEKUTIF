<script>
  // ===========================================================================================
  // riwayat
  // ===========================================================================================
  function riwayatPasien(){
    loading("#nav-riwayat");
    $("#tab-notifikasi").html("");
    var noRM = <?= $pasien["noRM"] ?>;
    $.get("../pasien/cariData.php?cariRiwayatPasien=true&noRM="+noRM, tampilkanRiwayat);
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
      myTable += "  <table class='table table-bordered table-hover' id='tabelRiwayat' width='100%'>";
      myTable += "    <thead class='bg-light'>";
      myTable += "      <tr style='height: 50px;'>";
      myTable += "        <th class='text-center align-middle' width='5%'>NO</th>";
      myTable += "        <th class='text-center align-middle' width='9%'>TANGGAL</th>";
      myTable += "        <th class='text-center align-middle' width='15%'>PEMBIAYAAN</th>";
      myTable += "        <th class='text-center align-middle' width='15%'>UNIT</th>";
      myTable += "        <th class='text-center align-middle' width='15%'>DPJP</th>";
      myTable += "        <th class='text-center align-middle' width='8%'>DIAGNOSA</th>";
      myTable += "        <th class='text-center align-middle' width='9%'>TANGGAL KRS</th>";
      myTable += "        <th class='text-center align-middle' width='8%'>TINDAK LANJUT</th>";
      myTable += "        <th class='text-center align-middle' width='15%'>PETUGAS</th>";
      myTable += "      </tr>";
      myTable += "    </thead>";
      myTable += "    <tbody>";
      myTable +=        bodyTable;            
      myTable += "    </tbody>";
      myTable += "  </table>";
      myTable += "</div>";

      $("#nav-riwayat").html(myTable);
      $("#tabelRiwayat").DataTable({"autoWidth": false});
    } else {
      var pesan =  "Tidak ditemukan riwayat kunjungan pasien dengan No. RM <b><?= $pasien["noRM"] ?></b>";
      notifikasi("#nav-riwayat", 0, pesan);;
    }
  }
</script>