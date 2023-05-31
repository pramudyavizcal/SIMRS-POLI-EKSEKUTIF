<?php
    include('adodb.inc.php');
    $dbtype = "mysqli";
    //$conni = newADOConnection('mysqli');
    @$conn = &ADONewConnection($dbtype);  # create a connection
    /*$conni->debug=TRUE;*/

    $dbhostname = "192.168.0.99";
    $dbusername = "SidikJari";
    $dbpassword = "djatikoesoemo";
    $dbname = "sosodoro";
    $conn->PConnect($dbhostname,$dbusername,$dbpassword,$dbname);
?>