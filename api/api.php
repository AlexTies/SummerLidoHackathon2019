<?php
require_once ('../konfiguration.php');
require_once ('InternalAPI.php');
error_reporting(E_ALL);
session_start();
//require_once('../../security.php');
header('Content-Type: application/json');

//$headers = apache_request_headers();
$pdo = new PDO('mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DATENBANK, MYSQL_BENUTZER,MYSQL_KENNWORT, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));$pdo->query("SET CHARACTER SET utf8mb4");
/*
$contenttype = "Content-type";
if(isset($headers["Content-Type"])){
 $contenttype = "Content-Type";
}*/
$data = json_decode(file_get_contents('php://input'), true);
//GATEWAY

//if (strpos($headers[$contenttype], "application/json") === 0) {

       if($data[0] == "roomcheck"){
         login($data[1]);
       }

/*} else {
 header('Location: ../errors/400.php');
}*/
function login($roomid){
  $pdo = new PDO('mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DATENBANK, MYSQL_BENUTZER,MYSQL_KENNWORT);
  $statement = $pdo->prepare("SELECT * FROM `rooms` WHERE RoomCode = :rc");
  $result = $statement->execute(array('rc' => $roomid));
  $erg = $statement->fetch();
  if($erg){
    echo '[1]';
    $_SESSION['room'] = $roomid;

  } else {
    echo '[0]';
  }
}
?>
