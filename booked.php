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
      <section class="background"  style="background-image: url('img/booked.jpg')">
        <div class="text">

          <h2>ALTA LODGE</h2>
          <h1>BOOKED EVENTS</h1>
        </div>
      </section>
      <section class="maincontent">
        <div class="blindarea">

        </div>
        <div class="scroll">
          <div class="scrollcont">
          <h1>
            EVENTS YOU BOOKED
            </h1>
              <?php
              $statement = $pdo->prepare("SELECT *, FORMAT(payed, 2, 'de_de') as payed, booked.ID as bookedid FROM `booked` INNER JOIN exkursion ON exkursion.ID = booked.excursion INNER JOIN events ON events.ID = exkursion.event");
              $result = $statement->execute(array('bid' => $room['BookingNr']));
              $booking = $statement->fetchAll();
              foreach ($booking as $key) {
                ?>
                <div class="event" style="background-image: url('img/<?php echo $key['image']; ?>')">
                    <div class="eventtext">
                   <span class="overtitle">
                    <?php echo $key['abovetitle']; ?>
                   </span><span class="title">
                    <?php echo $key['subtitle']; ?>
                       </span>
                    <span class="content"><?php echo $key['description']; ?></span>
                  <span class="content">PAYED <?php echo $key['payed']; ?>€</span></div>
                  <div class="interact">
                    <span class="title">Booked people:</span>
                  <?php
                  $statement = $pdo->prepare("SELECT * FROM `booked_clients` INNER JOIN kunden ON booked_clients.client_id = kunden.clientnr WHERE booked_clients.booked_id = :id");
                  $result = $statement->execute(array('id' => $key['bookedid']));
                  $bookuser = $statement->fetchAll();
                  foreach ($bookuser as $key) {
                    echo "<span>" . $key['name'] .  " "  . $key['surname'] .  "</span>";
                  }
                    ?>
                  </div>
                  <?php


                   ?>

                  </div>
                <?php
              }
              ?>
          </div>
        </div>
      </section>
    </main>
  </body>

</html>
