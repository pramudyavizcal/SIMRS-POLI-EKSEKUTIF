<?php

  define('baseURL', 'http://192.168.0.244/grahaWK/');
  define('baseurl_doc', $_SERVER['DOCUMENT_ROOT'].'/grahaWK/');

  define('copyright', 'Designed and Develop by <b>SIMRS</b> © 2022');

  date_default_timezone_set('Asia/Jakarta');
  if ( !session_id() ) {
    session_start();
  }

?>