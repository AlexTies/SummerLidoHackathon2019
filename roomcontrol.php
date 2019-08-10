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
      <section class="background"  >
        <div class="text">

          <h2>ALTA LODGE</h2>
          <h1>CONTROL YOUR ROOM</h1>
        </div>
      </section>
      <section class="maincontent">
        <div class="blindarea">

        </div>
        <div class="scroll">
          <div class="scrollcont">
          <h1>
            YOUR ROOM CONTROL
          </h1>
            <h4>LAST CLEANED BY STAFF</h4>
            <span class="thin">Today 15:34</span>
            <div class="setting">
              <a>REQUEST CLEANING</a>
            </div>
            <h4>ROOM TEMPERATURE</h4>
            <span class="thin">22° CELSIUS</span>
            <div class="setting">
              <a>+</a>
              <a>-</a>
            </div>
              <h4>ROLLER SHUTTERS</h4>
              <span class="thin">CLOSED</span>
              <div class="setting">
                <a>OPEN</a>
              </div>
            <h4>DISABLE AIR CONDITIONING</h4>
            <span class="thin">23:00 O' CLOCK</span>
            <div class="setting">
              <a>+</a>
              <a>-</a>
            </div>
            <h4>ALARM</h4>
            <span class="thin">07:00 O' CLOCK</span>
            <div class="setting">
              <a>+</a>
              <a>-</a>
            </div>

            <h1>
              Book additional services
            </h1>
              <?php
              $statement = $pdo->prepare("SELECT * FROM `events` WHERE kat=6");
              $result = $statement->execute(array('bid' => $room['BookingNr']));
              $booking = $statement->fetchAll();
              foreach ($booking as $key) {
                echo "<tr><td>" . $key['name'] . " " . $key['surname'] . "</td><td>" . $key['RoomNR'] . "</td></tr>";
                ?>
                <div class="event" style="background-image: url('img/<?php echo $key['image']; ?>')">
                    <div class="eventtext">
                   <span class="overtitle">
                    <?php echo $key['abovetitle']; ?>
                   </span><span class="title">
                    <?php echo $key['subtitle']; ?>
                       </span>
                    <span class="content"><?php echo $key['description']; ?></span></div>
                    <?php
                    if($key['bookable'] > 0){
                    ?>
                    <div class="interact">
                      <a href="./book.php?excursion=<?php echo $key['bookable']; ?>">BOOK NOW</a>
                    </div>
                    <?php

                    }

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
  <script>
  var excursionPrice = <?php echo $excurs['priceprint']; ?>;
  function bookToggle(item){
    item.classList.toggle('selected');
    let price = 0.0;
    for (let i = 0; i < members.children.length; i++) {
      if(members.children[i].classList.contains('selected'))
      price += excursionPrice;
    }
    total.innerHTML = parseFloat(price).toFixed(2).replace('.', ',');
  }
  </script>

</html>
