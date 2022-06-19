<?php

require_once "../database.php";

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

if (isset($uri[2]) && $uri[2] == 'users') {
    $output = $pdo->query('SELECT * FROM users')->fetchAll(PDO::FETCH_ASSOC);
    header('Content-type: application/json');
    echo json_encode(['users'=>$output]);
    exit();
}

if (isset($uri[2]) && $uri[2] == 'teams') {
    $output = $pdo->query('SELECT id as teamId, teamName, emblemUrl as teamEmblemUrl FROM teams')->fetchAll(PDO::FETCH_ASSOC);
    header('Content-type: application/json');
    echo json_encode(['teams'=>$output]);
    exit();
}

if (isset($uri[2]) && $uri[2] == 'login') {

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $username = trim($_POST["username"]);
        $password = trim($_POST["password"]);

        if (empty($username) || empty($password)) {
            echo "FAILED";
            exit();
        }

        // Prepare a select statement
        $sql = "SELECT id, username, password, isAdmin FROM users WHERE username = :username";

        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);

            // Set parameters
            $param_username = trim($_POST["username"]);

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Check if username exists, if yes then verify password
                if($stmt->rowCount() == 1){
                    if($row = $stmt->fetch()){
                        $id = $row["id"];
                        $username = $row["username"];
                        $hashed_password = $row["password"];
                        if(password_verify($password, $hashed_password)){
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;

                            // Check if user is an Administrator
                            if ($row["isAdmin"]) {
                                echo "Admin";
                            } else {
                                echo "User";
                            }
                        } else{
                            // Password is not valid, display a generic error message
                            echo "INVALID";
                            exit();
                        }
                    }
                } else{
                    // Username doesn't exist, display a generic error message
                    echo "INVALID";
                    exit();
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
                exit();
            }

            // Close statement
            unset($stmt);
            }
    } else {
        echo "NOT POST REQUEST";
    }
}

if (isset($uri[2]) && $uri[2] == 'register') {
    if($_SERVER["REQUEST_METHOD"] == "POST"){

        $username = trim($_POST["username"]);
        $password = trim($_POST["password"]);

        if (empty($username) || empty($password)) {
            echo "FAILED";
            exit();
        }

        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = :username";

        if ($stmt = $pdo->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);

            // Set parameters
            $param_username = trim($_POST["username"]);

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    echo "EXISTS";
                    exit();
                }
            }
            // Close statement
            unset($stmt);
        }

        $sql = "INSERT INTO users (username, password, isAdmin) VALUES (:username, :password, false)";

        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);

            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Redirect to login page
                echo "SUCCESS";
                exit();
            } else{
                echo "FAILED";
                exit();
            }

            // Close statement
            unset($stmt);
        }

        // Close connection
        unset($pdo);
    } else {
        echo "NOT POST REQUEST";
    }
}

if (isset($uri[2]) && $uri[2] == 'games' && !isset($uri[3])) {
    $stmt = 'Select g.id, g.timeStart, g.timeEnd,
         t1.teamName as teamA, t1.emblemUrl as teamAurl,
         t2.teamName as teamB, t2.emblemUrl as teamBurl 
        FROM games as g 
        LEFT JOIN teams as t1 ON g.teamA=t1.id 
        LEFT JOIN teams as t2 ON g.teamB=t2.id';
    $output = $pdo->query($stmt)->fetchAll(PDO::FETCH_ASSOC);
    header('Content-type: application/json');
    echo json_encode(['games'=>$output]);
    exit();
}

if (isset($uri[2]) && $uri[2] == 'games' && isset($uri[3])) {
    $gameID = $uri[3];

    $stmtA = 'Select p.id, p.pname as name, p.prole as role, p.photoUrl
                FROM games as g
                LEFT JOIN teams as t ON g.teamA=t.id
                RIGHT JOIN players as p ON t.id=p.teamId
                WHERE g.id='."$gameID";
    $teamA = $pdo->query($stmtA)->fetchAll(PDO::FETCH_ASSOC);

    $stmtB = 'Select p.id, p.pname as name, p.prole as role, p.photoUrl
                FROM games as g
                LEFT JOIN teams as t ON g.teamB=t.id
                RIGHT JOIN players as p ON t.id=p.teamId
                WHERE g.id='."$gameID";
    $teamB = $pdo->query($stmtB)->fetchAll(PDO::FETCH_ASSOC);

    header('Content-type: application/json');
    echo json_encode(['teamAPlayers'=>$teamA]+['teamBPlayers'=>$teamB]);
    exit();
}

?>