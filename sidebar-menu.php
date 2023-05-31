

<?php
  // daftar menu
  // if (!isset($_SESSION["simrs"]["menu"])){
    $_SESSION["simrs"]["menu"] = array();
    $_SESSION["simrs"]["file"] = array();
    if ($_SESSION["simrs"]['pegawai']["jabatan"] == "Administrator") {
      $queryMenu = "SELECT szNomor, szKategori, szClass 
                    FROM eksekutif_menu 
                    GROUP BY szNomor ORDER BY szNomor;
                  ";
    } else {
      $queryMenu = "SELECT a.szNomor, a.szKategori, a.szClass 
                    FROM eksekutif_menu a 
                    LEFT OUTER JOIN eksekutif_user_menu b ON b.nKode_menu = a.nKode
                    WHERE b.nNIK_user ='$user_kode'
                    GROUP BY szNomor ORDER BY szNomor;
                  ";
    }
    $a = 0;
    $x = 0;
    $conn->query($queryMenu);
    $menu = $conn->resultSet();
    foreach ($menu as $menu) {
      $nomor = $menu['szNomor'];
      $kategori = $menu['szKategori'];
      $icon = $menu['szClass'];
      $_SESSION["simrs"]["menu"][$a]["iconMenu"] = $icon;
      $_SESSION["simrs"]["menu"][$a]["namaMenu"] = $kategori;

      if ($user_jabatan == "Administrator") {
        $querySub = "SELECT szNama, szLink 
                    FROM eksekutif_menu 
                    WHERE szNomor = '$nomor'
                    ORDER BY szNomor, szNama;
                  ";
      } else {
        $querySub = "SELECT szNama, szLink 
                    FROM eksekutif_menu a 
                    LEFT OUTER JOIN eksekutif_user_menu b ON b.nKode_menu = a.nKode
                    WHERE szNomor = '$nomor' AND b.nNIK_user = '$user_kode'
                    ORDER BY szNomor, a.szNama;
                  ";
      }
      $b = 0;
      $conn->query($querySub);
      $subMenu = $conn->resultSet();
      foreach ($subMenu as $subMenu ) {
        $namaMenu = $subMenu['szNama'];
        $link = $subMenu['szLink'];
        $_SESSION["simrs"]["menu"][$a]["subMenu"][$b]["namaSubMenu"] = $namaMenu;
        $_SESSION["simrs"]["menu"][$a]["subMenu"][$b]["linkSubMenu"] = $link;
        $_SESSION["simrs"]["file"][$x]["namaMenu"] = $a+2;
        $_SESSION["simrs"]["file"][$x]["subMenu"] = $b+1;
        $namaFile = explode('/', $link);
        $namaFile = end($namaFile);
        $_SESSION["simrs"]["file"][$x]["namaFile"] = $namaFile;
        $b++;
        $x++;
      }
      $a++;
    }
  // }
    
  $listMenuUser = $_SESSION["simrs"]["menu"];
  $listFileUser = $_SESSION["simrs"]["file"];
  
  // menu aktif
  $menuAktif = 1;
  $subMenuAktif = 0;
  $namaFileSekarang = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1); 
  // replace nama file yang tidak bisa di akses langsung dari sidebar
  $listMenuDiganti = array("I_pasien-js.php");
  $listMenuPengganti = array("I_antrian-js.php");
  for ($i=0; $i<count($listMenuDiganti); $i++){
    if($namaFileSekarang == $listMenuDiganti[$i]){
      $namaFileSekarang = $listMenuPengganti[$i];
      break;
    }
  }
  //set kode menu aktif
  for ($i=0; $i<count($listFileUser); $i++){
    if($listFileUser[$i]["namaFile"] == $namaFileSekarang){
      $menuAktif = $listFileUser[$i]["namaMenu"];
      $subMenuAktif = $listFileUser[$i]["subMenu"];
      break;
    }
  }

?>


<aside id="sidebar" class="sidebar">
  <input type="hidden" class="form-control" id="menuAktifUser" value="<?= $menuAktif ?>">
  <input type="hidden" class="form-control" id="subMenuAktifUser" value="<?= $subMenuAktif ?>">
  <ul class="sidebar-nav" id="sidebar-nav">
    <li class="nav-item">
      <a class="nav-link collapsed" id="menu-1" onclick="pilihMenu('<?=baseURL?>dashboard.php')">
        <i class="bi bi-grid"></i><span>Dashboard</span>
      </a>
    </li>

    <!-- menu dari database -->
    <?php 
      for($i=0; $i<count($listMenuUser); $i++) :
        $icon = $listMenuUser[$i]["iconMenu"];
        $namaMenu = $listMenuUser[$i]["namaMenu"];
    ?>
      <li class="nav-item">
        <a class="nav-link collapsed" id="menu-<?=$i+2?>"data-bs-target="#menu-content-<?=$i+2?>" data-bs-toggle="collapse" href="#"> <!-- collapsed -->
          <i class="<?= $icon ?>"></i><span><?= $namaMenu ?></span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        
        <ul id="menu-content-<?=$i+2?>" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <?php 
            for ($j=0; $j<count($listMenuUser[$i]["subMenu"]) ; $j++) : 
              $namaSubMenu = $listMenuUser[$i]["subMenu"][$j]["namaSubMenu"];
              $linkSubMenu = $listMenuUser[$i]["subMenu"][$j]["linkSubMenu"];
          ?>
          <li>
            <a href="#" class="sub-menu" id="sub-menu-<?=$i+2?>-<?=$j+1?>" onclick="pilihMenu('<?= baseURL.$linkSubMenu ?>')">
              <i class="bi bi-circle"></i><span><?= $namaSubMenu ?></span>
            </a>
          </li>
          <?php endfor; ?>
        </ul>
      </li>
    <?php endfor; ?>
  </ul>
</aside>

<script>
  function pilihMenu(link){
    window.location.replace(link);
  }
</script>
