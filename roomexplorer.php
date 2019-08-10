<?php
session_start();
require_once('konfiguration.php');
if(!isset($_SESSION['room'])){
  exit();
}
$pdo = new PDO('mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DATENBANK, MYSQL_BENUTZER,MYSQL_KENNWORT);
$statement = $pdo->prepare("SELECT *, DATE_FORMAT(`from`, '%d.%m.%Y') as fromd, DATE_FORMAT(`to`, '%d.%m.%Y') as tod FROM `rooms` INNER JOIN booking ON booking.BookingNR = rooms.booking WHERE RoomCode = :rc ");
$result = $statement->execute(array('rc' => $_SESSION['room']));
$room = $statement->fetch();
if(!$room) {
  exit();
}

 ?>
<!DOCTYPE html>
<html lang="de" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
      rel="stylesheet">
    <title>Welcome to ALTA</title>
<link rel="stylesheet" type="text/css" href="./design.css">

  </head>
  <body>
    <?php require('header.php'); ?>
    <main>
      <section class="background"  style="background-image: url('img/plan.jpg')">
        <div class="text">

          <h2>ALTA LODGE</h2>
          <h1>ROOM EXPLORER</h1>
        </div>
      </section>
      <section class="maincontent" id="maincontent">
        <div class="blindarea" id=blindarea>

        </div>
        <div class="scroll">
          <div class="scrollcont">
          <h1>
            Alta's room explorer
            </h1>
            <p>
              Click the button below to start, then select the strongest Bluetooth beacon.
            </p>
            <a class="button inv" onclick="startExplore()">START</a>
            <h1>Found:</h1>
            <div id="main">
              Nothing found yet.
            </div>
          </div>
        </div>
      </section>
    </main>
  </body>
  <script>
  function startExplore(){
    maincontent.scrollTop = blindarea.offsetHeight;
      main.innerHTML = "Let the search begin...";
      if(!navigator.bluetooth){
        main.innerHTML = "Sadly, your device doesnt support this function.";
      }
    let options = {
    filters: [
      {namePrefix: 'NOI0082'},
      {namePrefix: 'NOI0080'}
    ]
    }

        navigator.bluetooth.requestDevice(options).then(function(device) {
          main.innerHTML = "<h2>You are at Alta's " + beacons[device.name][0] + "</h2>";
        main.innerHTML += beacons[device.name][1];
      }).catch(function(error) {
        main.innerHTML = "Something didnt work. Please try again.";
      });
  }
  <?php
  $statement = $pdo->prepare("SELECT * FROM `meetingpoints`");
  $result = $statement->execute(array('bid' => $room['BookingNr']));
  $rooms = $statement->fetchAll();
  $all = array();
  foreach ($rooms as $key) {
    $all[$key["BeaconName"]] = array($key["RoomName"], $key["Descr"]);
  }
  echo "var beacons = " . json_encode($all);

   ?>

</script>

</html>
