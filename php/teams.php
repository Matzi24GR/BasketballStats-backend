<?php

  require_once "database.php";
  // Initialize the session
  session_start();
  
  // Check if the user is logged in, if not then redirect him to login page
  if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
      header("location: index.php");
      exit;
  }

  $formErr = "";
  $nameErr = $cityErr = $imageErr = "";
  $name = $city = $imageUrl = "";

  if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST["name"])) {
      if (empty($_POST["name"])) {
        $nameErr = "name is required";
      } else {
        $name = test_input($_POST["name"]);
      }
      if (empty($_POST["city"])) {
        $cityErr = "city is required";
      } else {
        $city = test_input($_POST["city"]);
      }
      if (empty($_POST["imageUrl"])) {
        $imageErr = "emblem is required";
      } else {
        $imageUrl = test_input($_POST["imageUrl"]);
      }

      if (empty($nameErr) && empty($cityErr) && empty($imageErr)) {
        try {
          insertTeam($pdo, $name, $city, $imageUrl);
        } catch(PDOException $e) {
          //echo "Connection failed: " . $e->getMessage();
          $formErr = "Form is disabled. Try again later";
        }
      }
    }

    if (isset($_POST['deleteButton'])) {
      $stmt = $pdo->prepare("DELETE FROM teams");
      $stmt->execute();
    }

    if (isset($_POST['fillButton'])) {
      insertTeam($pdo, "ΑΕΚ", "Αθήνα", "/resources/images/teams/aek.png");
      insertTeam($pdo, "ΑΠΟΛΛΩΝ Π. OSCAR", "Πάτρα", "/resources/images/teams/apollon_p_oscar.png");
      insertTeam($pdo, "ΑΡΗΣ", "Θεσσαλονίκη", "/resources/images/teams/aris.png");
      insertTeam($pdo, "ΗΡΑΚΛΗΣ", "Θεσσαλονίκη", "/resources/images/teams/iraklis.png");
      insertTeam($pdo, "ΙΩΝΙΚΟΣ BASKET", "Νίκαια", "/resources/images/teams/ionikos_basket.png");
      insertTeam($pdo, "ΚΟΛΟΣΣΟΣ H HOTELS", "Ρόδος", "/resources/images/teams/Kolossos_h_hotels.png");
      insertTeam($pdo, "ΛΑΡΙΣΑ", "Λάρισα", "/resources/images/teams/larisa.png");
      insertTeam($pdo, "ΛΑΥΡΙΟ MEGABOLT", "Λαύριο", "/resources/images/teams/lavrio_megabolt.png");
      insertTeam($pdo, "ΟΛΥΜΠΙΑΚΟΣ", "Πειραιάς", "/resources/images/teams/olympiakos.png");
      insertTeam($pdo, "ΠΑΝΑΘΗΝΑΪΚΟΣ ΟΠΑΠ", "Αθήνα", "/resources/images/teams/panathinaikos_opap.png");
      insertTeam($pdo, "ΠΑΟΚ mateco", "Θεσσαλονίκη", "/resources/images/teams/paok_mateco.png");
      insertTeam($pdo, "ΠΕΡΙΣΤΕΡΙ VITABIOTICS", "Αθήνα", "/resources/images/teams/peristeri_vitabiotics.png");
      insertTeam($pdo, "ΠΡΟΜΗΘΕΑΣ", " Πάτρας", "/resources/images/teams/promitheas.png");
    }

  }

  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }
  function insertTeam($pdo, $name, $city, $emblem) {
    $stmt = $pdo->prepare("INSERT INTO teams (teamName, city, emblemUrl) VALUES (:teamName, :city, :emblemUrl)");
    $stmt->bindParam(':teamName', $name);
    $stmt->bindParam(':city', $city);
    $stmt->bindParam(':emblemUrl', $emblem);
    $stmt->execute();
  }
?>
<!DOCTYPE html>
<html lang="en"></html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Teams</title>
    <link rel="stylesheet" href="style.css">
</head>
  <body>
    <h2 style="border-radius: 12px;" class="header">Teams Dashboard <a style="color:lavender;" href="admin.php">Go Back</a></h2>
    <div class="container" style="flex-wrap: wrap-reverse; align-items:flex-end">

      <div class="card2">
        <h2>Table Status</h2>
        <table>
          <tr>
            <th>Id</th>
            <th>Name</th>
            <th>City</th>
            <th>EmblemUrl</th>
          </tr>
          <?php
              try {
                  $sql = 'SELECT * FROM teams';

                  $data = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);


                  if ($data) {
                    foreach ($data as $entry) {
                      echo "<tr>";
                      echo "<td>".$entry['id']."</td>";
                      echo "<td>".$entry['teamName']."</td>";
                      echo "<td>".$entry['city']."</td>";
                      echo "<td><a href=".$entry['emblemUrl'].">".$entry['emblemUrl']."</a></td>";
                      echo "</tr>";
                    }
                  }

              } catch(PDOException $e) {
              echo "Connection failed: " . $e->getMessage();
              }
          ?>
        </table>
      </div>

      <div class="card2">
        <h2>Insert Team</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" enctype="multipart/form-data">
          <label>Name</label><br>
          <input type="text" name="name" value="<?php echo $name;?>"> <span class="error"><br><?php echo $nameErr;?></span><br>
          <label>City</label><br>
          <input type="text" name="city" value="<?php echo $city;?>"><span class="error"><br><?php echo $cityErr;?></span><br>
          <label>Image Location</label><br>
          <input type="text" name="imageUrl" value="<?php echo $imageUrl;?>"><span class="error"><br><?php echo $imageErr;?></span><br>
          <hr>
          <button>Submit</button>
          <input type="reset">
          <?php echo $formErr;?></span><br>
        </form>
      </div>

      <div class="card2">
        <form method="post">
          <h2>Mass Actions</h2>
          <input type="submit" name="fillButton" value="Load Default Teams">
          <br>
          <input type="submit" name="deleteButton" value="!! Delete all !!">
        </form>
      </div>

    </div>
  </body>
</html>