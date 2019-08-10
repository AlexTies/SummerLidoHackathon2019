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
      <section class="background"  style="background-image: url('img/pool.jpg')">
        <div class="text">

          <h2>ALTA LODGE</h2>
          <h1>THE POOL</h1>
        </div>
      </section>
      <section class="maincontent">
        <div class="blindarea">

        </div>
        <div class="scroll">
          <div class="scrollcont">
          <h1>
            ALTA LODGE's heated outdoor pool
          </h1>
            <h4>OPENING HOURS TODAY</h4>
            <span class="thin">08:00 - 18:00</span>
            <h4>CURRENT TEMPERATURE</h4>
            <span class="thin">24° CELSIUS</span>

            <h1>
              Events @thePool
            </h1>
              <?php
              $statement = $pdo->prepare("SELECT * FROM `events` WHERE kat=3");
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
