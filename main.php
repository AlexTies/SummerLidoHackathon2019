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
      <section class="background">
        <div class="text">

          <h2>Welcome to Alta</h2>
          <h1><?php echo $room['RoomName']; ?></h1>
          <h3>ROOM NR. <?php echo $room['RoomNR']; ?></h3>
        </div>
      </section>
      <section class="maincontent">
        <div class="blindarea">

        </div>
        <div class="scroll">
          <div class="scrollcont">
                  <h1>Whats up at ALTA</h1>
        <!--<div class="event">
            <div class="eventtext">
           <span class="overtitle">KIDS
           </span><span class="title">
            ENTERTANMENT
               </span>
            <span class="content">See what your Kids can do at ALTA LAKE</span></div>

        </div>
        <div class="event" style="background-image: url('img/pool.jpg'">
            <div class="eventtext">
           <span class="overtitle">POOLS
           </span><span class="title">
            SWIMMING
               </span>
            <span class="content">Discover all of our pools</span></div>

          </div>-->
          <?php
          $add = "";
          if($room['HaveKids'] == 0){
            $add = " AND kids = 0";
          }
          $statement = $pdo->prepare("SELECT * FROM `events` WHERE kat=1 $add");
          $result = $statement->execute(array('bid' => $room['BookingNr']));
          $booking = $statement->fetchAll();
          foreach ($booking as $key) {
            //echo "<tr><td>" . $key['name'] . " " . $key['surname'] . "</td><td>" . $key['RoomNR'] . "</td></tr>";
            if($key['LinkTo'] != ""){
              echo '<a href="' . $key['LinkTo'] . '">';
            }
            ?>

              <div class="event" style="background-image: url('img/<?php echo $key['image']; ?>')">
                  <div class="eventtext">
                 <span class="overtitle">
                  <?php echo $key['abovetitle']; ?>
                 </span><span class="title">
                  <?php echo $key['subtitle']; ?>
                     </span>
                  <span class="content"><?php echo $key['description']; ?></span></div>

                </div>
              </a>
            <?php
          }
          ?>
        </div>
        </div>
      </section>
    </main>
  </body>

</html>
