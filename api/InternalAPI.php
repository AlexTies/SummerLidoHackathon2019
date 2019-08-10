<?php

function outputAppointments(){
  $klassen = array();

      if(LANG == 0){
      $months = array(
        'M01' => 'Januar',
        'M02' => 'Februar',
        'M03' => 'M&auml;rz',
        'M04' => 'April',
        'M05' => 'Mai',
        'M06' => 'Juni',
        'M07' =>  'Juli ',
        'M08' => 'August',
        'M09' => 'September',
        'M10' => 'Oktober',
        'M11' => 'November',
        'M12' => 'Dezember'
      );


      $days = array(
        'D0' => 'Sonntag',
        'D1' => 'Montag',
        'D2' => 'Dienstag',
        'D3' => 'Mittwoch',
        'D4' => 'Donnerstag',
        'D5' => 'Freitag',
        'D6' => 'Samstag'
      );
      }
      if(LANG == 1){
      $months = array(
        'M01' => 'Gennaio',
        'M02' => 'Febbraio',
        'M03' => 'Marzo',
        'M04' => 'Aprile',
        'M05' => 'Maggio',
        'M06' => 'Giugno',
        'M07' => 'Luglio',
        'M08' => 'Agosto',
        'M09' => 'Settembre',
        'M10' => 'Ottobre',
        'M11' => 'Novembre',
        'M12' => 'Dicembre'
      );


      $days = array(
        'D0' => 'Domenica',
        'D1' => 'Lunedì',
        'D2' => 'Martedì',
        'D3' => 'Mercoledì',
        'D4' => 'Giovedì',
        'D5' => 'Venerdì',
        'D6' => 'Sabato'
      );
      }
      if(LANG == 2){
        $months = array(
          'M01' => 'January',
          'M02' => 'February',
          'M03' => 'March',
          'M04' => 'April',
          'M05' => 'May',
          'M06' => 'June',
          'M07' => 'July',
          'M08' => 'August',
          'M09' => 'September',
          'M10' => 'October',
          'M11' => 'November',
          'M12' => 'December'
        );


        $days = array(
          'D0' => 'Sunday',
          'D1' => 'Monday',
          'D2' => 'Tuesday',
          'D3' => 'Wednesday',
          'D4' => 'Thursday',
          'D5' => 'Friday',
          'D6' => 'Saturday'
        );
      }

  $pdo = new PDO('mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DATENBANK, MYSQL_BENUTZER,MYSQL_KENNWORT, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));$pdo->query("SET CHARACTER SET utf8mb4");

  $klassentermine = array();

  $statement = $pdo->prepare("SELECT class.*, CONCAT(users.vorname,' ', users.nachname) as name, DATE_FORMAT(founded, '%d.%m.%Y') as founded, COUNT(in_class.users_id) as schueler FROM class INNER JOIN users ON users.id = class.founder LEFT JOIN in_class ON in_class.class_id = class.ID WHERE in_class.users_id = :id GROUP BY class.ID");
  $result = $statement->execute(array("id" => $_SESSION['userid']));
  $user = $statement->fetchAll();
  foreach ($user as $row) {
    array_push($klassen, $row['extuuid']);
  }

  foreach ($klassen as $klass) {

    $pdo = new PDO('mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DATENBANK, MYSQL_BENUTZER,MYSQL_KENNWORT, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));$pdo->query("SET CHARACTER SET utf8mb4");

    $ausgabe = array();
    $statement = $pdo->prepare("SELECT appointments.*, users.username, DATE_FORMAT(block.time, '%H:%i') as time, block.dayofweek, appointments.ID as tid, CONCAT(users.vorname, ' ', users.nachname) as creatorname, subject.backcolor, subject.title as subject, subject.color, DATE_FORMAT(NOW(), 'D%w, %d. M%m %Y') as heute, block.duration, DATE_FORMAT(NOW(), 'Heute, %d. M%m %Y') as heutefor, DATE_FORMAT(DATE_ADD(DATE_ADD(DATE_ADD(MAKEDATE(appointments.year, 1), INTERVAL (appointments.weekofyear-1) WEEK), INTERVAL -(DAYOFWEEK(DATE_ADD(MAKEDATE(appointments.year, 1), INTERVAL (appointments.weekofyear-1) WEEK))-1) DAY), INTERVAL block.dayofweek DAY), 'D%w, %d. M%m %Y') as datum, important, block.ID as blockid, IF((DATE_ADD(DATE_ADD(DATE_ADD(MAKEDATE(appointments.year, 1), INTERVAL (appointments.weekofyear-1) WEEK), INTERVAL -(DAYOFWEEK(DATE_ADD(MAKEDATE(appointments.year, 1), INTERVAL (appointments.weekofyear-1) WEEK))-1) DAY), INTERVAL (block.dayofweek-1) DAY)) > DATE_ADD(NOW(), INTERVAL -2 DAY),1,0) as future, IF(appointment_complete.UserID IS NULL, 0, 1) as done, type, timetable, subject.ID as sid FROM appointments INNER JOIN block ON block.ID = appointments.block INNER JOIN users ON users.ID = appointments.creator INNER JOIN in_class ON in_class.class_id = appointments.class INNER JOIN subject ON subject.ID = block.subject LEFT JOIN appointment_complete ON appointment_complete.AppointmentID = appointments.ID AND appointment_complete.UserID = :id WHERE in_class.users_id = :id AND in_class.class_id = (SELECT ID FROM class WHERE extuuid =:classid) AND ( private = 0 OR (private = 1 AND creator = :id)) ORDER BY appointments.year ASC, appointments.weekofyear ASC, block.dayofweek ASC");
    $result = $statement->execute(array("id" => $_SESSION['userid'], 'classid' => $klass));
    $user = $statement->fetchAll();



    foreach ($user as $row) { //heute
      $datum = $row['datum'];
      if($row['datum'] == $row['heute']){
        $datum = $row['heutefor'];
      }
      foreach ($months as $key => $value) {
        $datum = str_replace($key, $value, $datum);
      }
      foreach ($days as $key => $value) {
        $datum = str_replace($key, $value, $datum);
      }
        $comments = array();

      $statement = $pdo->prepare("SELECT appointments_comments.text, CONCAT(users.vorname, ' ', users.nachname) as sender, users.username,commentEXT, IF(users.id = :me, 1, 0) as mycomment FROM `appointments_comments` INNER JOIN users ON users.id = appointments_comments.sender WHERE appointments_comments.appointment = :id ORDER BY sent ASC;");
      $result = $statement->execute(array('id' => $row['tid'],"me" => $_SESSION['userid']));
      $erg = $statement->fetchAll();
      foreach ($erg as $row2) {
        array_push($comments, array($row2['sender'],$row2['username'],$row2['text'],$row2['commentEXT'], $row2['mycomment']));
      }

      $vote = array(array(), array());

      $statement = $pdo->prepare("SELECT * FROM `appointments_vote` INNER JOIN users ON users.id = appointments_vote.UserID WHERE appointments_vote.AppointmentID = :id;");
      $result = $statement->execute(array('id' => $row['tid']));
      $erg = $statement->fetchAll();

      foreach ($erg as $row2) {
        array_push($vote[intval($row2['VoteOpinion'])], array($row2['vorname'] . " " . $row2['nachname']));
      }


      array_push($ausgabe, array($row['tid'],$row['title'], str_replace("\n","<br>", $row['description']), $datum, $row['weekofyear'], $row['dayofweek'], $row['subject'], $row['backcolor'], $row['color'], $row['important'], $row['creatorname'], $row['time'], $row['username'], $comments, $row['private'], $row['blockid'], $row['future'], $row['year'], $row['duration'], $row['done'], $row['type'], $row['timetable'], $row['sid'],$vote));
    }
    $klassentermine[$klass] = $ausgabe;

  }
  return json_encode($klassentermine);

}
function outputNotes(){
  $klassen = array();

  $pdo = new PDO('mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DATENBANK, MYSQL_BENUTZER,MYSQL_KENNWORT, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));$pdo->query("SET CHARACTER SET utf8mb4");

  $klassentermine = array();

  $statement = $pdo->prepare("SELECT class.*, CONCAT(users.vorname,' ', users.nachname) as name, DATE_FORMAT(founded, '%d.%m.%Y') as founded, COUNT(in_class.users_id) as schueler FROM class INNER JOIN users ON users.id = class.founder LEFT JOIN in_class ON in_class.class_id = class.ID WHERE in_class.users_id = :id GROUP BY class.ID");
  $result = $statement->execute(array("id" => $_SESSION['userid']));
  $user = $statement->fetchAll();
  foreach ($user as $row) {
    array_push($klassen, $row['extuuid']);
  }

  foreach ($klassen as $klass) {

    $pdo = new PDO('mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DATENBANK, MYSQL_BENUTZER,MYSQL_KENNWORT, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));$pdo->query("SET CHARACTER SET utf8mb4");

    $ausgabe = array();
    $statement = $pdo->prepare("SELECT *, DATE_FORMAT(created, '%d.%m.%Y') as created FROM notes INNER JOIN users ON users.id = notes.creator WHERE notes.class = (SELECT ID FROM class WHERE extuuid =:classid)");
    $result = $statement->execute(array('classid' => $klass));
    $user = $statement->fetchAll();



    foreach ($user as $row) { //heute


      array_push($ausgabe, array($row['ID'],$row['title'], str_replace("\n","<br>", $row['text']), $row['created'], $row['vorname'] . " " . $row['nachname'], $row['important'], $row['private'],$row['username']));
    }
    $klassentermine[$klass] = $ausgabe;

  }
  return json_encode($klassentermine);

}
function outputFaecher(){
  $klassen = array();

  $pdo = new PDO('mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DATENBANK, MYSQL_BENUTZER,MYSQL_KENNWORT, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));$pdo->query("SET CHARACTER SET utf8mb4");

  $ausgabe = array();

  $statement = $pdo->prepare("SELECT class.*, CONCAT(users.vorname,' ', users.nachname) as name, DATE_FORMAT(founded, '%d.%m.%Y') as founded, COUNT(in_class.users_id) as schueler FROM class INNER JOIN users ON users.id = class.founder LEFT JOIN in_class ON in_class.class_id = class.ID WHERE in_class.users_id = :id GROUP BY class.ID");
  $result = $statement->execute(array("id" => $_SESSION['userid']));
  $user = $statement->fetchAll();
  foreach ($user as $row) {
    array_push($klassen, $row['extuuid']);
    $ausgabe[$row['extuuid']] = array();
  }
    foreach ($klassen as $klass) {

      $sql = "SELECT * FROM `subject` WHERE class=(SELECT ID FROM class WHERE extuuid =:ext)";
      $statement = $pdo->prepare($sql);
      $result = $statement->execute(array("ext" => $klass));
      $user = $statement->fetchAll();
      foreach ($user as $row) {
        array_push($ausgabe[$klass], array($row['ID'],$row['title'], $row['backcolor'],$row['color']));

      }
    }

    return json_encode($ausgabe);
    }
    function outputCaneledBlock(){
      $klassen = array();

      $pdo = new PDO('mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DATENBANK, MYSQL_BENUTZER,MYSQL_KENNWORT, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));$pdo->query("SET CHARACTER SET utf8mb4");

      $ausgabe = array();

      $statement = $pdo->prepare("SELECT class.*, CONCAT(users.vorname,' ', users.nachname) as name, DATE_FORMAT(founded, '%d.%m.%Y') as founded, COUNT(in_class.users_id) as schueler FROM class INNER JOIN users ON users.id = class.founder LEFT JOIN in_class ON in_class.class_id = class.ID WHERE in_class.users_id = :id GROUP BY class.ID");
      $result = $statement->execute(array("id" => $_SESSION['userid']));
      $user = $statement->fetchAll();
      foreach ($user as $row) {
        array_push($klassen, $row['extuuid']);
        $ausgabe[$row['extuuid']] = array();
      }
        foreach ($klassen as $klass) {

          $sql = "SELECT *, DATE_FORMAT(block.time, '%H:%i') as time, DATE_FORMAT(DATE_ADD(DATE_ADD(DATE_ADD(MAKEDATE(canceledBlock.year, 1), INTERVAL (canceledBlock.weekofyear-1) WEEK), INTERVAL -(DAYOFWEEK(DATE_ADD(MAKEDATE(canceledBlock.year, 1), INTERVAL (canceledBlock.weekofyear-1) WEEK))-1) DAY), INTERVAL block.dayofweek DAY), '%d.%m.%Y') as datum, canceled.ID as CID FROM `canceledBlock` INNER JOIN block ON canceledBlock.BlockID = block.ID INNER JOIN canceled ON canceled.ID = canceledBlock.cancelID LEFT JOIN subject ON subject.ID = block.subject WHERE canceled.class=(SELECT ID FROM class WHERE extuuid =:ext)";
          $statement = $pdo->prepare($sql);
          $result = $statement->execute(array("ext" => $klass));
          $user = $statement->fetchAll();
          foreach ($user as $row) {
            array_push($ausgabe[$klass], array($row['BlockID'],$row['weekofyear'], $row['year'], $row['CID'], $row['time'], $row['title'], $row['datum'], $row['duration']));

          }
        }

        return json_encode($ausgabe);
        }


        function outputCanceled(){
          $klassen = array();

          $pdo = new PDO('mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DATENBANK, MYSQL_BENUTZER,MYSQL_KENNWORT, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));$pdo->query("SET CHARACTER SET utf8mb4");

          $ausgabe = array();

          $statement = $pdo->prepare("SELECT class.*, CONCAT(users.vorname,' ', users.nachname) as name, DATE_FORMAT(founded, '%d.%m.%Y') as founded, COUNT(in_class.users_id) as schueler FROM class INNER JOIN users ON users.id = class.founder LEFT JOIN in_class ON in_class.class_id = class.ID WHERE in_class.users_id = :id GROUP BY class.ID");
          $result = $statement->execute(array("id" => $_SESSION['userid']));
          $user = $statement->fetchAll();
          foreach ($user as $row) {
            array_push($klassen, $row['extuuid']);
            $ausgabe[$row['extuuid']] = array();
          }
            foreach ($klassen as $klass) {

              //$sql = "SELECT canceled.*, users.vorname, users.nachname, COUNT(canceledBlock.BlockID) as blockc FROM `canceled` LEFT JOIN canceledBlock ON canceled.ID = canceledBlock.cancelID INNER JOIN users ON users.id = canceled.creator  WHERE class=(SELECT ID FROM class WHERE extuuid =:ext) GROUP BY canceled.ID";
$sql = "SELECT canceled.*, users.vorname, users.nachname, COUNT(canceledBlock.BlockID) as blockc, DATE_FORMAT(MAX(DATE_ADD(DATE_ADD(DATE_ADD(MAKEDATE(canceledBlock.year, 1), INTERVAL (canceledBlock.weekofyear-1) WEEK), INTERVAL -(DAYOFWEEK(DATE_ADD(MAKEDATE(canceledBlock.year, 1), INTERVAL (canceledBlock.weekofyear-1) WEEK))-1) DAY), INTERVAL block.dayofweek DAY)), '%d.%m.%Y') as datummax, DATE_FORMAT(MIN(DATE_ADD(DATE_ADD(DATE_ADD(MAKEDATE(canceledBlock.year, 1), INTERVAL (canceledBlock.weekofyear-1) WEEK), INTERVAL -(DAYOFWEEK(DATE_ADD(MAKEDATE(canceledBlock.year, 1), INTERVAL (canceledBlock.weekofyear-1) WEEK))-1) DAY), INTERVAL block.dayofweek DAY)), '%d.%m.%Y') as datummin FROM `canceled` LEFT JOIN canceledBlock ON canceled.ID = canceledBlock.cancelID LEFT JOIN block ON block.ID = canceledBlock.BlockID INNER JOIN users ON users.id = canceled.creator WHERE canceled.class=(SELECT ID FROM class WHERE extuuid =:ext) GROUP BY canceled.ID";

              $statement = $pdo->prepare($sql);
              $result = $statement->execute(array("ext" => $klass));
              $user = $statement->fetchAll();
              foreach ($user as $row) {
                array_push($ausgabe[$klass], array($row['ID'],$row['reason'], $row['vorname'] . " " . $row['nachname'], $row['blockc'],$row['datummin'],$row['datummax']));

              }
            }

            return json_encode($ausgabe);
            }
            /* */


function outputKlasseSchueler(){
  $klassen = array();

  $pdo = new PDO('mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DATENBANK, MYSQL_BENUTZER,MYSQL_KENNWORT, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));$pdo->query("SET CHARACTER SET utf8mb4");

  $ausgabe = array();

  $statement = $pdo->prepare("SELECT class.*, CONCAT(users.vorname,' ', users.nachname) as name, DATE_FORMAT(founded, '%d.%m.%Y') as founded, COUNT(in_class.users_id) as schueler FROM class INNER JOIN users ON users.id = class.founder LEFT JOIN in_class ON in_class.class_id = class.ID WHERE in_class.users_id = :id GROUP BY class.ID");
  $result = $statement->execute(array("id" => $_SESSION['userid']));
  $user = $statement->fetchAll();
  foreach ($user as $row) {
    array_push($klassen, $row['extuuid']);
    $ausgabe[$row['extuuid']] = array();
  }
    foreach ($klassen as $klass) {
      $sql = "SELECT users.username, house.name as hausname, users.vorname, users.nachname,in_class.admin, (SELECT Count(*) FROM appointments WHERE appointments.class = class.ID AND appointments.creator = in_class.users_id) as termine, (SELECT Count(*) FROM discussion WHERE discussion.class = class.ID AND discussion.sender = in_class.users_id) as posts, (SELECT Count(*) FROM notes WHERE notes.class = class.ID AND notes.creator = in_class.users_id) as notes FROM class INNER JOIN in_class ON in_class.class_id = class.ID INNER JOIN users ON in_class.users_id = users.id LEFT JOIN house ON in_class.haus = house.ID  WHERE class.extuuid = :ext ORDER BY users.nachname ASC";
      $statement = $pdo->prepare($sql);
      $result = $statement->execute(array("ext" => $klass));
      $user = $statement->fetchAll();
      foreach ($user as $row) {
        array_push($ausgabe[$klass], array($row['username'],$row['vorname'], $row['nachname'],$row['admin'],$row['hausname'], $row['termine'], $row['posts'], $row['notes']) );

      }
    }

    return json_encode($ausgabe);

}
function outputKlassenProtocol(){
  $klassen = array();

  $pdo = new PDO('mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DATENBANK, MYSQL_BENUTZER,MYSQL_KENNWORT, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));$pdo->query("SET CHARACTER SET utf8mb4");

  $ausgabe = array();

  $statement = $pdo->prepare("SELECT class.*, CONCAT(users.vorname,' ', users.nachname) as name, DATE_FORMAT(founded, '%d.%m.%Y') as founded, COUNT(in_class.users_id) as schueler FROM class INNER JOIN users ON users.id = class.founder LEFT JOIN in_class ON in_class.class_id = class.ID WHERE in_class.users_id = :id GROUP BY class.ID");
  $result = $statement->execute(array("id" => $_SESSION['userid']));
  $user = $statement->fetchAll();
  foreach ($user as $row) {
    array_push($klassen, $row['extuuid']);
    $ausgabe[$row['extuuid']] = array();
  }
    foreach ($klassen as $klass) {
      $sql = "SELECT protocol.*, CONCAT(users.vorname, ' ', users.nachname) as name, DATE_FORMAT(created, '%d.%m.%Y %H:%i') as happened, users.username FROM `protocol` INNER JOIN users ON users.id = protocol.user INNER JOIN class ON class.ID = protocol.class WHERE class.extuuid = :ext ORDER BY created ASC";
      $statement = $pdo->prepare($sql);
      $result = $statement->execute(array("ext" => $klass));
      $user = $statement->fetchAll();
      foreach ($user as $row) {
        array_push($ausgabe[$klass], array($row['name'],$row['name'],$row['event'], $row['title'],$row['previously'], $row['after'], $row['username'], $row['happened'], $row['LineID']) );

      }
    }

    return json_encode($ausgabe);

}
function outputNew(){
  $klassen = array();

  $pdo = new PDO('mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DATENBANK, MYSQL_BENUTZER,MYSQL_KENNWORT, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));$pdo->query("SET CHARACTER SET utf8mb4");

  $ausgabe = array();

  $statement = $pdo->prepare("SELECT class.*, CONCAT(users.vorname,' ', users.nachname) as name, DATE_FORMAT(founded, '%d.%m.%Y') as founded, COUNT(in_class.users_id) as schueler FROM class INNER JOIN users ON users.id = class.founder LEFT JOIN in_class ON in_class.class_id = class.ID WHERE in_class.users_id = :id GROUP BY class.ID");
  $result = $statement->execute(array("id" => $_SESSION['userid']));
  $user = $statement->fetchAll();
  foreach ($user as $row) {
    array_push($klassen, $row['extuuid']);
    $ausgabe[$row['extuuid']] = array();
  }
    foreach ($klassen as $klass) {
      $sql = "SELECT class.title, COUNT(discussion.ID) as neu FROM class LEFT JOIN discussion ON discussion.class = class.ID WHERE class.extuuid = :ext AND discussion.posted >= (SELECT in_class.lastposts FROM in_class WHERE in_class.users_id = :id AND in_class.class_id = class.ID) GROUP BY class.ID";
      $statement = $pdo->prepare($sql);
      $result = $statement->execute(array("ext" => $klass, "id" => $_SESSION['userid']));
      $erg = $statement->fetch();
      array_push($ausgabe[$klass], array($erg['neu']));


    }

    return json_encode($ausgabe);

}
function outputInformationen(){
  $pdo = new PDO('mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DATENBANK, MYSQL_BENUTZER,MYSQL_KENNWORT, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));$pdo->query("SET CHARACTER SET utf8mb4");

  $informationen = array();
  $statement = $pdo->prepare("SELECT class.*, CONCAT(users.vorname,' ', users.nachname) as name, DATE_FORMAT(founded, '%d.%m.%Y') as founded, DATE_FORMAT(ended, '%Y-%m-%dT%H:%i:00') as endedex, DATE_FORMAT(founded, '%Y-%m-%dT%H:%i:00') as founded2, COUNT(in_class.users_id) as schueler, IF(plus = 1, 'true', 'false') as plusm, CONCAT(ender.vorname,' ', ender.nachname) as endedby, DATE_FORMAT(ended, '%d.%m.%Y') as ended FROM class INNER JOIN users ON users.id = class.founder LEFT JOIN in_class ON in_class.class_id = class.ID LEFT JOIN users as ender ON ender.id = endedby WHERE in_class.users_id = :id GROUP BY class.ID");
  $result = $statement->execute(array("id" => $_SESSION['userid']));
  $user = $statement->fetchAll();
  foreach ($user as $row) {
    $informationen[$row['extuuid']] = array($row['extuuid'],$row['title'],$row['klassencode'],$row['name'],$row['founded'],$row['plusm'],$row['founded2'],$row['endedby'],$row['ended'],$row['endedex']);
  }
  return json_encode($informationen);
}
function outputKlassenPosts(){
  $klassen = array();

  $pdo = new PDO('mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DATENBANK, MYSQL_BENUTZER,MYSQL_KENNWORT, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));$pdo->query("SET CHARACTER SET utf8mb4");

  $ausgabe = array();

  $statement = $pdo->prepare("SELECT class.*, CONCAT(users.vorname,' ', users.nachname) as name, DATE_FORMAT(founded, '%d.%m.%Y') as founded, COUNT(in_class.users_id) as schueler FROM class INNER JOIN users ON users.id = class.founder LEFT JOIN in_class ON in_class.class_id = class.ID WHERE in_class.users_id = :id GROUP BY class.ID");
  $result = $statement->execute(array("id" => $_SESSION['userid']));
  $user = $statement->fetchAll();
  foreach ($user as $row) {
    array_push($klassen, $row['extuuid']);
    $ausgabe[$row['extuuid']] = array();
  }
    foreach ($klassen as $klass) {
      $sql = "SELECT *, DATE_FORMAT(posted, '%d.%m.%Y %H:%i') as postedFor, discussion.ID as discID FROM discussion INNER JOIN users ON users.id = discussion.sender INNER JOIN class ON class.ID = discussion.class WHERE class.extuuid = :ext ORDER BY posted ASC";
      $statement = $pdo->prepare($sql);
      $result = $statement->execute(array("ext" => $klass));
      $user = $statement->fetchAll();

      foreach ($user as $row) {
        $comments = array();
        $statement = $pdo->prepare("SELECT discussion_comments.text, CONCAT(users.vorname, ' ', users.nachname) as sender, users.username,commentEXT, IF(users.id = :me, 1, 0) as mycomment FROM `discussion_comments` INNER JOIN users ON users.id = discussion_comments.sender WHERE discussion_comments.discussion = :id ORDER BY sent ASC;");
        $result = $statement->execute(array("id" => $row['discID'],"me" => $_SESSION['userid']));
        $erg = $statement->fetchAll();
        foreach ($erg as $row2) {
          array_push($comments, array($row2['sender'],$row2['username'],$row2['text'],$row2['commentEXT'], $row2['mycomment']));
        }
        array_push($ausgabe[$klass], array($row['extID'],$row['vorname'],$row['nachname'], $row['text'],$row['postedFor'],$row['username'],$comments));

      }
    }

    return json_encode($ausgabe);

}
function outputStundenplan(){
  $klassen = array();
  $pdo = new PDO('mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DATENBANK, MYSQL_BENUTZER,MYSQL_KENNWORT, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));$pdo->query("SET CHARACTER SET utf8mb4");
  $stundenplaene = array();
  $statement = $pdo->prepare("SELECT class.*, CONCAT(users.vorname,' ', users.nachname) as name, DATE_FORMAT(founded, '%d.%m.%Y') as founded, COUNT(in_class.users_id) as schueler FROM class INNER JOIN users ON users.id = class.founder LEFT JOIN in_class ON in_class.class_id = class.ID WHERE in_class.users_id = :id GROUP BY class.ID");
  $result = $statement->execute(array("id" => $_SESSION['userid']));
  $user = $statement->fetchAll();
  foreach ($user as $row) {
    array_push($klassen, $row['extuuid']);
  }

  foreach ($klassen as $klass) {
    $stundenplan = array();
    $statement = $pdo->prepare("SELECT *, subject.ID as subjectid, DATE_FORMAT(time,'%H:%i') as uhrzeit, block.ID as blockid FROM block INNER JOIN subject ON subject.ID = block.subject WHERE block.timetable >= 1 AND block.class = (SELECT ID FROM class WHERE extuuid =:classid) ORDER BY timetable ASC, dayofweek ASC, time ASC; ");
    $result = $statement->execute(array( 'classid' => $klass));
    $erg = $statement->fetchAll();
    $old = "";
    $oldplan = "";
    $plan = array();
    $stunden = array();
    //var_dump($erg);

    foreach ($erg as $key) {
      if($old != $key['dayofweek']){
        if($old != ""){
          array_push($plan, $stunden);

        }
        if($oldplan != $key['timetable']){
          if($oldplan != ""){
            array_push($stundenplan, $plan);
          }
          $plan = array();
          $oldplan = $key['timetable'];
        }
        $stunden = array();
        $old = $key['dayofweek'];
      }

      //echo "1";
      array_push($stunden, array($key['title'], $key['uhrzeit'], $key['duration'], $key['backcolor'], $key['color'], $key['dayofweek'], $key['blockid'], $key['subjectid']));
    }
      array_push($plan, $stunden);
    array_push($stundenplan, $plan);

    $stundenplaene[$klass] = $stundenplan;
  }
  //var_dump($stundenplaene);
  return json_encode( $stundenplaene );
}


function generateRandomString($length = 10,$prefix = "") {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $prefix . $randomString;
}
?>
