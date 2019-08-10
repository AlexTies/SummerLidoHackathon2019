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
      <section class="background"  style="background-image: url('img/rooms.jpg')">
        <div class="text">

          <h2>ALTA LODGE</h2>
          <h1>ROOM SERVICE</h1>
        </div>
      </section>
      <section class="maincontent">
        <div class="blindarea">

        </div>
        <div class="scroll" style="display: -webkit-box;">
          <div class="scrollcont">
          <h1>
            Alta's Room service
            </h1>
            <p>
              Our room service is available 06:00 - 22:00
            </p>
            <h1>
              Drinks
              </h1>
              <?php
              $statement = $pdo->prepare("SELECT *, FORMAT(ItemPrice, 2, 'de_de') as ItemPrice FROM `roomservice` WHERE Category = 1;");
              $result = $statement->execute(array('bid' => $room['BookingNr']));
              $booking = $statement->fetchAll();
              foreach ($booking as $key) {
                ?>
                <div class="article" onclick="upArticles(this)"  style="background-image: url('img/<?php echo $key['Image']; ?>')">
                    <div class="articletext">
                      <span class="amount"><b>0</b> Ordered</span>
                   <span class="artname">
                    <?php echo $key['ItemName']; ?>
                  </span><span class="price">
                    <?php echo $key['ItemPrice']; ?>€
                       </span>

                    </div>
                  </div>
                <?php
              }
              ?>

              <h1>
                Snacks
                </h1>
              <?php
              $statement = $pdo->prepare("SELECT *, FORMAT(ItemPrice, 2, 'de_de') as ItemPrice FROM `roomservice` WHERE Category = 2;");
              $result = $statement->execute(array('bid' => $room['BookingNr']));
              $booking = $statement->fetchAll();
              foreach ($booking as $key) {
                ?>
                <div class="article" onclick="upArticles(this)"  style="background-image: url('img/<?php echo $key['Image']; ?>')">
                    <div class="articletext">
                      <span class="amount"><b>0</b> Ordered</span>
                   <span class="artname">
                    <?php echo $key['ItemName']; ?>
                  </span><span class="price">
                    <?php echo $key['ItemPrice']; ?>€
                       </span>

                    </div>
                  </div>
                <?php
              }
              ?>

              <h1>
                Food
                </h1>
              <?php
              $statement = $pdo->prepare("SELECT *, FORMAT(ItemPrice, 2, 'de_de') as ItemPrice FROM `roomservice` WHERE Category = 3;");
              $result = $statement->execute(array('bid' => $room['BookingNr']));
              $booking = $statement->fetchAll();
              foreach ($booking as $key) {
                ?>
                <div class="article" onclick="upArticles(this)"  style="background-image: url('img/<?php echo $key['Image']; ?>')">
                    <div class="articletext">
                      <span class="amount"><b>0</b> Ordered</span>
                   <span class="artname">
                    <?php echo $key['ItemName']; ?>
                  </span><span class="price">
                    <?php echo $key['ItemPrice']; ?>€
                       </span>

                    </div>
                  </div>
                <?php
              }
              ?>
              <a class="button inv mrg">BESTELLEN</a>
          </div>
        </div>
      </section>
    </main>
  </body>
  <script>
  function upArticles(item){
    item.classList.add('ordered');
    	let amount = parseInt(item.children[0].children[0].children[0].innerHTML);
      item.children[0].children[0].children[0].innerHTML = amount+1;
  }
  </script>

</html>
