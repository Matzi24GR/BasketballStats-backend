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
$startDateErr = "";
$teamA = $teamB = $startDate = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST["teamA"])) {
        if (empty($_POST["startDate"])) {
            $startDateErr = "date is required";
        } else {
            $startDate = strtotime(test_input($_POST["startDate"]));
            $endDate = $startDate + 7200;
        }

        $teamA = $_POST["teamA"];
        $teamB = $_POST["teamB"];

        if (empty($startDateErr)) {
            try {
                insertGame($pdo, $teamA, $teamB, $startDate);
            } catch(PDOException $e) {
                echo "Connection failed: " . $e->getMessage();
                $formErr = "Form is disabled. Try again later";
            }
        }
    }

    if (isset($_POST['deleteButton'])) {
        $stmt = $pdo->prepare("DELETE FROM games");
        $stmt->execute();
    }

}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
function insertGame($pdo, $teamA, $teamB, $startDate) {
    $endDate = $startDate + 7200;
    $stmt = $pdo->prepare("INSERT INTO games (teamA, teamB, timeStart, timeEnd, champID) VALUES (:teamA, :teamB, :timeStart, :timeEnd, null)");
    $stmt->bindParam(':teamA', $teamA);
    $stmt->bindParam(':teamB', $teamB);
    $stmt->bindParam(':timeStart', $startDate);
    $stmt->bindParam(':timeEnd', $endDate);
    $stmt->execute();
}
?>
<!DOCTYPE html>
<html lang="en"></html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Games</title>
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
                <th>Start</th>
                <th>End</th>
                <th>Team A</th>
                <th>Team A emblem</th>
                <th>Team B</th>
                <th>Team B emblem</th>
            </tr>
            <?php
            try {
                $sql = 'Select g.id, g.timeStart, g.timeEnd,
                         t1.teamName as teamA, t1.emblemUrl as teamAurl,
                         t2.teamName as teamB, t2.emblemUrl as teamBurl 
                        FROM games as g 
                        LEFT JOIN teams as t1 ON g.teamA=t1.id 
                        LEFT JOIN teams as t2 ON g.teamB=t2.id';

                $data = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);


                if ($data) {
                    foreach ($data as $entry) {
                        $start = date('Y-m-d H:i:s', $entry['timeStart'] );
                        $end = date('Y-m-d H:i:s', $entry['timeEnd'] );
                        echo "<tr>";
                        echo "<td>{$entry['id']}</td>";
                        echo "<td>{$start}</td>";
                        echo "<td>{$end}</td>";
                        echo "<td><img src={$entry['teamAurl']} width='30' ></td>";
                        echo "<td>{$entry['teamA']}</td>";
                        echo "<td><img src={$entry['teamBurl']} width='30' ></td>";
                        echo "<td>{$entry['teamB']}</td>";
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
        <h2>Create Game</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" enctype="multipart/form-data">
            <label>Team A</label><br>
            <select name="teamA">
                <?php
                try {
                    $sql = 'SELECT * FROM teams';

                    $data = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);


                    if ($data) {
                        foreach ($data as $entry) {
                            $selectedTag = (isset($_POST['team']) && intval($_POST['team']) == $entry["id"]) ? 'selected' : '';
                            echo "<option value={$entry['id']} {$selectedTag}>{$entry['teamName']}</option>";
                        }
                    }

                } catch(PDOException $e) {
                    echo "Connection failed: " . $e->getMessage();
                }
                ?>
            </select><br>
            <label>Team B</label><br>
            <select name="teamB">
                <?php
                try {
                    $sql = 'SELECT * FROM teams';

                    $data = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);


                    if ($data) {
                        foreach ($data as $entry) {
                            $selectedTag = (isset($_POST['team']) && intval($_POST['team']) == $entry["id"]) ? 'selected' : '';
                            echo "<option value={$entry['id']} {$selectedTag}>{$entry['teamName']}</option>";
                        }
                    }

                } catch(PDOException $e) {
                    echo "Connection failed: " . $e->getMessage();
                }
                ?>
            </select><br>
            <label>Start</label><br>
            <input type="datetime-local" name="startDate"><br><?php echo $startDateErr;?><br>
            <button>Submit</button>
            <input type="reset">
            <?php echo $formErr;?></span><br>
        </form>
    </div>

    <div class="card2">
        <form method="post">
            <br>
            <input type="submit" name="deleteButton" value="!! Delete all !!">
        </form>
    </div>

</div>
</body>
</html>