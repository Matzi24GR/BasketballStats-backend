<?php
  // Initialize the session
  session_start();
  
  // Check if the user is logged in, if not then redirect him to login page
  if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
      header("location: index.php");
      exit;
  }

  require_once "database.php";

?>

<!DOCTYPE html>
<html lang="en"></html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="style.css">
</head>
  <body>
    <h2 style="border-radius: 12px;" class="header">Hi, <?php echo htmlspecialchars($_SESSION["username"]);?>. Welcome to the Administrator Dashboard.<a style="color:lavender;" href="logout.php">Sign Out of Your Account</a></h2>
    <div class="container" style="margin-top: 20px;">
      <div class="card2">
        <h2> General Info </h2>
        <h3>
            Connected Database:
        </h3>
        <?php 
            echo $pdo->query('select version()')->fetchColumn();
        ?>
        <hr>
        <h3> Users:</h3>
        <?php 
        try {
            $users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
            echo "$users Users ";
            $users = $pdo->query("SELECT COUNT(*) FROM users WHERE isAdmin = 1")->fetchColumn();
            echo "($users Admins)";
        } catch (PDOException $e) {
            echo "<span class='error'> Table does not exist </span>";
        }
        ?>
      </div>
      <hr>
      <div class="card2">
        <h3>
        Teams: 
        </h3>
        <?php 
        try {
            $formEntries = $pdo->query("SELECT COUNT(*) FROM teams")->fetchColumn();
            echo "$formEntries Entries";
        } catch (PDOException $e) {
            echo "<span class='error'> Table does not exist </span>";
        }
        ?>
        <hr>
        <form action="./teams.php">
          <button>Manage</button>
        </form>
      </div>
      <div class="card2">
        <h3>
        Players: 
        </h3>
        <?php 
        try {
            $formEntries = $pdo->query("SELECT COUNT(*) FROM players")->fetchColumn();
            echo "$formEntries Entries";
        } catch (PDOException $e) {
            echo "<span class='error'> Table does not exist </span>";
        }
        ?>
        <hr>
        <form action="./players.php">
          <button>Manage</button>
        </form>
      </div>
      <div class="card2">
        <h3>
        Games: 
        </h3>
        <?php 
        try {
            $formEntries = $pdo->query("SELECT COUNT(*) FROM games")->fetchColumn();
            echo "$formEntries Entries";
        } catch (PDOException $e) {
            echo "<span class='error'> Table does not exist </span>";
        }
        ?>
        <hr>
        <form action="./games.php">
          <button>Manage</button>
        </form>
      </div>
      <div class="card2">
        <h3>
        Events: 
        </h3>
        <?php 
        try {
            $formEntries = $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
            echo "$formEntries Entries";
        } catch (PDOException $e) {
            echo "<span class='error'> Table does not exist </span>";
        }
        ?>
        <hr>
        <form action="./events.php">
          <button>Manage</button>
        </form>
      </div>
      <div class="card2">
        <h3>
        Championships: 
        </h3>
        <?php 
        try {
            $formEntries = $pdo->query("SELECT COUNT(*) FROM championships")->fetchColumn();
            echo "$formEntries Entries";
        } catch (PDOException $e) {
            echo "<span class='error'> Table does not exist </span>";
        }
        ?>
        <hr>
<!--    <form action="./championships.php">-->
        <form>
          <button>Manage (TODO)</button>
        </form>
      </div>
    </div>
  </body>
</html>