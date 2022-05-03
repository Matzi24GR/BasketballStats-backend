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
$nameErr = $photoErr = "";
$name = $photo = $teamId = $role = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST["name"])) {
        if (empty($_POST["name"])) {
            $nameErr = "name is required";
        } else {
            $name = test_input($_POST["name"]);
        }
        if (empty($_POST["photo"])) {
            $photoErr = "photo is required";
        } else {
            $photo = test_input($_POST["photo"]);
        }

        $teamId = test_input($_POST["team"]);
        $role = test_input($_POST["role"]);


        if (empty($nameErr) && empty($photoErr)) {
            try {
                insertPlayer($pdo, $name, $photo, intval($teamId), $role);
            } catch(PDOException $e) {
                //echo "Connection failed: " . $e->getMessage();
                $formErr = "Form is disabled. Try again later";
            }
        }
    }

    if (isset($_POST['deleteButton'])) {
        $stmt = $pdo->prepare("DELETE FROM players");
        $stmt->execute();
    }

    if (isset($_POST['fillButton'])) {
        //TODO implement this
    }

}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
function insertPlayer($pdo, $name, $photo, $teamId, $role) {
    $stmt = $pdo->prepare("INSERT INTO players (pname, photoUrl, teamId, prole) VALUES (:name, :photo, :teamId, :role)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':photo', $photo);
    $stmt->bindParam(':teamId', $teamId);
    $stmt->bindParam(':role', $role);
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
        <h2>Insert Player</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" enctype="multipart/form-data">
            <label>Name</label><br>
            <input type="text" name="name" value="<?php echo $name;?>"> <span class="error"><br><?php echo $nameErr;?></span><br>
            <label>Image Location</label><br>
            <input type="text" name="photo" value="<?php echo $photo;?>"><span class="error"><br><?php echo $photoErr;?></span><br>
            <label>Team</label><br>
            <select name="team">
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
            <label>Role</label><br>
            <select name="role">
                <option value="1" <?php echo (isset($_POST['role']) && $_POST['role'] === '1') ? 'selected' : ''; ?> >Point Guard</option>
                <option value="2" <?php echo (isset($_POST['role']) && $_POST['role'] === '2') ? 'selected' : ''; ?> >Shooting Guard</option>
                <option value="3" <?php echo (isset($_POST['role']) && $_POST['role'] === '3') ? 'selected' : ''; ?> >Small Forward</option>
                <option value="4" <?php echo (isset($_POST['role']) && $_POST['role'] === '4') ? 'selected' : ''; ?> >Power Forward</option>
                <option value="5" <?php echo (isset($_POST['role']) && $_POST['role'] === '5') ? 'selected' : ''; ?> >Center</option>
            </select>
            <hr>
            <button>Submit</button>
            <input type="reset">
            <?php echo $formErr;?></span><br>
        </form>
    </div>

    <div class="card2">
        <form method="post">
            <h2>Mass Actions</h2>
            <input type="submit" name="fillButton" value="Load Default Players (TODO)">
            <br>
            <input type="submit" name="deleteButton" value="!! Delete all !!">
        </form>
    </div>

</div>
<h1>Teams</h1>
<hr>
<div class="container">
    <?php
    try {
        $sql = 'SELECT * FROM teams';

        $data = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);


        if ($data) {
            foreach ($data as $entry) {
                $players = $pdo->query("SELECT * FROM players WHERE teamId = ({$entry['id']})")->fetchAll(PDO::FETCH_ASSOC);
                echo "<div class='card2'>
                        <h2><img src={$entry['emblemUrl']} height='20'> {$entry['teamName']}</h2>";
                echo "<table>
                        <tr>
                            <th>Id</th>
                            <th>Name</th>
                            <th>Role</th>
                            <th>PhotoUrl</th>
                        </tr>";
                foreach ($players as $player) {
                    echo "<tr>";
                    echo "<td>{$player['id']}</td>";
                    echo "<td>{$player['pname']}</td>";
                    echo "<td>{$player['prole']}</td>";
                    echo "<td>{$player['photoUrl']}</td>";
                    echo "</tr>";
                }
                echo "    </table>
                    </div>";
            }
        }

    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
    ?>
</div>
</body>
</html>