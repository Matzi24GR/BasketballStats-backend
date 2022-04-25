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
  <div class="card">
  <h2>Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to the Administrator Dashboard.</h2>
    <p>
        <a href="logout.php">Sign Out of Your Account</a>
    </p>
    <h3>
        Connected Database:
    </h3>
    <?php 
        echo $pdo->query('select version()')->fetchColumn();
    ?>
    <hr>
    <h3>
    "data" Table Status: 
    </h3>
    <?php 
    try {
        $formEntries = $pdo->query("SELECT COUNT(*) FROM data")->fetchColumn();
        echo "$formEntries Entries";
    } catch (PDOException $e) {
        echo "<span class='error'> Table does not exist </span>";
    }
    ?>
    <hr>
    <h3>
    "users" Table Status: 
    </h3>
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
  </body>
</html>