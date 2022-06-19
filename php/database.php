<?php
define('DB_HOST', $_ENV["DB_HOST"]);
define('DB_NAME', $_ENV["DB_NAME"]);
define('DB_USERNAME', $_ENV["DB_USERNAME"]);
define('DB_PASSWORD', $_ENV["DB_PASSWORD"]);
 
/* Attempt to connect to MySQL database */
try{
    $pdo = new PDO("mysql:host=".DB_HOST, DB_USERNAME, DB_PASSWORD);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create Database
    $sql = "CREATE DATABASE IF NOT EXISTS ".DB_NAME;
    $pdo->query($sql);

    // Use Database
    $pdo->query("use ".DB_NAME);

    // Create users Table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        isAdmin BOOLEAN NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->query($sql);

    $sql = 'SELECT COUNT(*) FROM users WHERE isAdmin=TRUE';
    $adminCount = $pdo->query($sql)->fetchColumn();
    
    if ($adminCount == 0) {
        // Create Admin Users
        $sql = "INSERT INTO users (username, password, isAdmin) VALUES (:username, :password, true)";
         
        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":username", $param_username);
            $stmt->bindParam(":password", $param_password);
            
            // Set parameters
            $param_username = "admin";
            $param_password = password_hash("admin", PASSWORD_DEFAULT);
            
            $stmt->execute();
        }
    }

    createSchema($pdo);

} catch(PDOException $e){
    die("ERROR: Could not connect. " . $e->getMessage());
}

function createSchema($pdo) {
    $sql = "CREATE TABLE IF NOT EXISTS teams (
        id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
        teamName VARCHAR(255) NOT NULL,
        city VARCHAR(255),
        emblemUrl VARCHAR(255)
      );
      
      CREATE TABLE IF NOT EXISTS championships (
        id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
        winningTeamId INT,
        cname VARCHAR(255),
        FOREIGN KEY (winningTeamId) REFERENCES teams(id)
      );
      
      CREATE TABLE IF NOT EXISTS players (
        id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
        teamId INT,
        pname VARCHAR(255) NOT NULL,
        prole VARCHAR(255),
        photoUrl VARCHAR(255),
        FOREIGN KEY (teamId) REFERENCES teams(id)
      );
      
      CREATE TABLE IF NOT EXISTS events (
        id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
        playerId INT,
        gameId INT,
        etype VARCHAR(255),
        etime INT,
        FOREIGN KEY (playerId) REFERENCES players(id),
        FOREIGN KEY (gameId) REFERENCES games(id)
      );
      
      CREATE TABLE IF NOT EXISTS games (
        id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
        teamA INT,
        teamB INT,
        timeStart INT,
        timeEnd INT,
        champID INT,
        FOREIGN KEY (teamA) REFERENCES teams(id),
        FOREIGN KEY (teamB) REFERENCES teams(id),
        FOREIGN KEY (champId) REFERENCES championships(id)
      );
      ";
    $pdo->query($sql);
}

?>