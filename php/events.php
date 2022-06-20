<?php

require_once "database.php";
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: index.php");
    exit;
}

$gameId = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {


    if (isset($_POST['gameId'])) {
        $gameId = test_input($_POST["gameId"]);
    }

    if (isset($_POST['deleteButton'])) {
        $stmt = $pdo->prepare("DELETE FROM events");
        $stmt->execute();
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
    <title>Game Events</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h2 style="border-radius: 12px;" class="header">Events Dashboard <a style="color:lavender;" href="admin.php">Go Back</a></h2>
<div class="container" style="flex-wrap: wrap-reverse; align-items:flex-end">

    <div class="card2">
        <h2>Table Status</h2>
        <table>
            <tr>
                <th>Id</th>
                <th>playerId</th>
                <th>Player</th>
                <th>GameId</th>
                <th>Type</th>
                <th>Time</th>
            </tr>
            <?php
            try {
                $sql = 'Select e.*, p.pname
                            FROM events as e 
                            JOIN players as p ON e.playerId=p.id
                            WHERE gameId='."$gameId";

                $data = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);


                if ($data) {
                    foreach ($data as $entry) {
                        $time = date('Y-m-d H:i:s', $entry['etime'] );
                        echo "<tr>";
                        echo "<td>{$entry['id']}</td>";
                        echo "<td>{$entry['playerId']}</td>";
                        echo "<td>{$entry['pname']}</td>";
                        echo "<td>{$entry['gameId']}</td>";
                        echo "<td>{$entry['etype']}</td>";
                        echo "<td>{$time}</td>";
                        echo "</tr>";
                    }
                }

            } catch(PDOException $e) {
                echo "Select Game first";
            }
            ?>
        </table>
    </div>

    <div class="card2">
        <h2>Game Filter</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" enctype="multipart/form-data">
            <select name="gameId">
                <?php
                try {
                    $sql = 'SELECT g.id, t1.teamName as teamA, t2.teamName as teamB FROM games as g LEFT JOIN teams t1 on t1.id = g.teamA LEFT JOIN teams t2 on t2.id = g.teamB';

                    $data = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

                    if ($data) {
                        foreach ($data as $entry) {
                            $selectedTag = (isset($_POST['gameId']) && intval($_POST['gameId']) == $entry["id"]) ? 'selected' : '';
                            $gameString = $entry['id'].": ".$entry['teamA']." - ".$entry['teamB'];
                            echo "<option value={$entry['id']} {$selectedTag}>{$gameString}</option>";
                        }
                    }

                } catch(PDOException $e) {
                    echo "Connection failed: " . $e->getMessage();
                }
                ?>
            </select><br>
            <button>Submit</button>
        </form>
    </div>

    <div class="card2">
        <form method="post">
            <input type="submit" name="deleteButton" value="!! Delete all !!">
        </form>
    </div>

</div>
</body>
</html>