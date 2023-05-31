<?php 
  if (isset($_SESSION['simrs']['alert'])) {
    $notificationCode = $_SESSION['simrs']['alert']['code'];
    $notificationMessage = $_SESSION['simrs']['alert']['message'];
    if ($notificationCode == 1) {
      $colorButton = "success";
      $notificationIcon = "<i class='bi bi-check2-square text-success'></i>";
    } else {
      $colorButton = "danger";
      $notificationIcon = "<i class='bi bi-exclamation-triangle text-danger'></i>";
    }
    
    echo "
      <div class='modal fade' id='notificationModal' tabindex='-1'>
        <div class='modal-dialog'>
          <div class='modal-content'>
            <div class='modal-body text-center'>
              <div class='row  h-100 d-flex align-items-center'>
                <div class='col-4' style='font-size: 75px;'>$notificationIcon</div>
                <div class='col-8'>$notificationMessage</div>
                <div class='col-12'>
                  <button type='button' class='btn btn-$colorButton w-100' data-bs-dismiss='modal' aria-label='Close'>Oke</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    ";

    unset($_SESSION['simrs']['alert']);
  }

?>