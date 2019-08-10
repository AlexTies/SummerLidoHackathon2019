<?php
session_start();
require_once('konfiguration.php');
if(!isset($_SESSION['room'])){
  exit();
}
if(!isset($_GET['excursion'])){
  exit();
}
$pdo = new PDO('mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DATENBANK, MYSQL_BENUTZER,MYSQL_KENNWORT);
$statement = $pdo->prepare("SELECT *, FORMAT(price, 2, 'de_de') as price, FORMAT(price, 2) as priceprint FROM `exkursion` INNER JOIN events ON events.ID = exkursion.event LEFT JOIN meetingpoints ON meetingpoints.ID = exkursion.StartRoom WHERE exkursion.ID = :rc ");
$result = $statement->execute(array('rc' => $_GET['excursion']));
$excurs = $statement->fetch();
if(!$excurs) {
  exit();
}
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
      <section class="background"  style="background-image: url('img/<?php echo $excurs['image']; ?>')">
        <div class="text">

          <h2><?php echo $excurs['abovetitle']; ?></h2>
          <h1><?php echo $excurs['subtitle']; ?></h1>
        </div>
      </section>
      <section class="maincontent">
        <div class="blindarea">

        </div>
        <div class="scroll">
          <div class="scrollcont">
          <h1>What will you do?</h1>
          <p>
            <?php echo $excurs['what']; ?>
          </p>
          <?php
          if($excurs['price'] != 0.00){
          ?>

          <span class="price">Price: <?php echo $excurs['price']; ?>€
            <?php
            if($excurs['PerPerson'] == 1)
            echo "per Person";
             ?>
            </span>
          <?php
        } else {
            ?>

            <span class="price">Price: Free reservation</span>
            <?php
          }
          if($excurs['StartRoom'] != 0){
            ?>
            <h1>Where does the Exkursion start?</h1>
            <?php
            echo "<p>Excursion starts at the " . $excurs['RoomName'] . ".</p>";
          }
           ?>

          <h1>Who do you want to book for?</h1>
          <table class="staymembers">
            <tbody>
            <tr>
              <th>Name</th>
              <th>Room</th>
            </tr>
            </tbody>
            <tbody id=members>
              <?php
              $add = "";
              if($excurs['age'] == 1){
                $add = " AND kunden.kid = 0";
              }
              if($excurs['age'] == 2){
                $add = " AND kunden.kid = 1";
              }
              $statement = $pdo->prepare("SELECT * FROM `kunden` INNER JOIN rooms ON rooms.RoomCode = kunden.room WHERE kunden.booking = :bid " . $add);
              $result = $statement->execute(array('bid' => $room['BookingNr']));
              $booking = $statement->fetchAll();
              foreach ($booking as $key) {
                echo '<tr onclick="bookToggle(this)"><td>' . $key['name'] . " " . $key['surname'] . "</td><td>" . $key['RoomNR'] . "</td></tr>";
              }
              ?>
            </tbody>
          </table>
          <?php

          if($excurs['age'] == 1){
            echo "<p>This excursion is for adults only.</p>";
          }
          if($excurs['age'] == 2){
            echo "<p>This excursion is for children only.</p>";
          }
           ?>
          <span class="price">Cost: <b id=total>0,00</b>€ in total.</span>
          <a class="book">BOOK</a>
          </div>
        </div>
      </section>
    </main>
  </body>
  <script>
  var excursionPrice = <?php echo $excurs['priceprint']; ?>;
  function bookToggle(item){
    item.classList.toggle('selected');
    let price = 0.00;
    for (let i = 0; i < members.children.length; i++) {
      if(members.children[i].classList.contains('selected'))
      price += excursionPrice;
    }
    total.innerHTML = parseFloat(price).toFixed(2).replace('.', ',');
  }
  </script>

</html>
