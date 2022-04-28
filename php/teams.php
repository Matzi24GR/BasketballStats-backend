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
        $stmt = $pdo->prepare("INSERT INTO teams (teamName, city, emblemUrl) VALUES (:teamName, :city, :emblemUrl)");
        $stmt->bindParam(':teamName', $name);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':emblemUrl', $imageUrl);

        $stmt->execute();

      } catch(PDOException $e) {
        //echo "Connection failed: " . $e->getMessage();
        $formErr = "Form is disabled. Try again later";
      }
    }

  }

  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
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
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" enctype="multipart/form-data">
      name: <input type="text" name="name" value="<?php echo $name;?>"> <span class="error">* <?php echo $nameErr;?></span><br>
      city: <input type="text" name="city" value="<?php echo $city;?>"><span class="error">* <?php echo $cityErr;?></span><br>
      emblem: <input type="text" name="imageUrl" value="<?php echo $imageUrl;?>"><span class="error">* <?php echo $imageErr;?></span>
      <input type="file" name="fileToUpload" id="fileToUpload">
      <hr>
      <button>Submit</button>
      <input type="reset">
      <?php echo $formErr;?></span><br>
    </form>  
    <hr>
    <form action="./admin.php">
      <button>Return</button>
    </form>
    <hr>
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
                echo "<td>".$entry['emblemUrl']."</td>";
                echo "</tr>";
              }
            }

        } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
        }
    ?>
  </body>
</html>