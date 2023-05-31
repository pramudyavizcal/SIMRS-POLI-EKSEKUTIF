<?php 
  include_once("config.php");

  if(isset($_SESSION['simrs'])){
    unset($_SESSION['simrs']);
    session_destroy();
  }
  header('Location: '.baseURL.'index.php');
  exit;
?>