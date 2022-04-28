<?php
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: admin.php");
}

require_once "database.php";
 
// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
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
                                header("location: admin.php");
                            } else {
                                header("location: form.php");
                            }
                        } else{
                            // Password is not valid, display a generic error message
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else{
                    // Username doesn't exist, display a generic error message
                    $login_err = "Invalid username or password.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            unset($stmt);
        }
    }
    
    // Close connection
    unset($pdo);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="card" style="background-color:darkorange;">
        <h1>Mock Website. Not related to <a href="https://www.esake.gr">esake.gr</a></h1>
    </div>
    <div class="container">
        <div class="card2" style="flex-grow: 1;">
            <h2>Information</h2>
            <p>This website was created as part of an assignment</p>
            <p>Android Development course at University of Macedonia 2022</p>
            <h3>Links</h3>
            <li><a href="https://github.com/UoM2022AndroidDevTeam11/BasketballStats-backend">Github</a></li>
            <li><a href="https://uom2022team11.stoplight.io/docs/basketballstats-backend/YXBpOjU1MDgzMDg3-esake-mock-api">Api Documentation</a></li>
            <h3> Made by Team 11</h3>
        </div>
        <div class="card2">
            <h2>Administrator Panel Login</h2>
            <?php 
            if(!empty($login_err)){
                echo '<div>' . $login_err . '</div>';
            }        
            ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div>
                    <label>Username</label><br>
                    <input type="text" name="username" value="<?php echo $username; ?>">
                    <span class="error"><br><?php echo $username_err; ?></span>
                </div>    
                <div>
                    <label>Password</label><br>
                    <input type="password" name="password">
                    <span class="error"><br><?php echo $password_err; ?></span>
                </div>
                <div>
                    <input type="submit" value="Login">
                </div>
                <!-- <p>Don't have an account? <a href="register.php">Sign up now</a>.</p> -->
            </form>
        </div>
    </div>
</body>
</html>