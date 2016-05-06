<?php
    require 'database.php';
    session_start();
    if(isset($_SESSION['logged_in'])){
        header("Location: stories.php");
    }
?>
<!DOCTYPE html>
<!--Authors: Ruth Chen-423268 and Chiraag Kapadia-430947-->
<html>
    <head>
        <title>R and C News -- Login!</title>
        <link rel="stylesheet" type="text/css" href="login.css">
    </head>
    <body>
        <div>
            <h1>Welcome to Ruth and Chiraag's News Site!</h1>
            <div id="login">
                <h1>Log In</h1>
                <form method="POST">
                    <p>
                        <label for="username">Username:</label>
                        <input type="text" name="username" id="username"/>
                    </p>
                    <p>
                        <label for="passwordinput">Password:</label>
                        <input type="password" name="passwordinput" id="passwordinput"/>
                    </p>
                    <p>
                        <input type="submit" value="Log in"/>
                        <input type="reset"/>
                    </p>
                 </form>
            </div>
            <div id="register">
                <h1>Create an account</h1>
                <form method="POST" action="newaccount.php">
                    <p>
                        <label for="newUser">Choose a username:</label>
                        <input type="text" name="newUser" id="newUser"/>
                    </p>
                    <p>
                        <label for="newPass1">Choose a password:</label>
                        <input type="password" name="newPass1" id="newPass1"/>
                    </p>
                    <p>
                        <label for="newPass2">Confirm password:</label>
                        <input type="password" name="newPass2" id="newPass2"/>
                    </p>
                    <p>
                        <input type="submit" value="Register"/>
                        <input type="reset"/>
                    </p>
                </form>
            </div>
            <?php
                if(isset($_POST['username'])){//if a username had been entered.
                    
                    $username = $_POST['username'];
                    if( !preg_match('/^[\w_\-]+$/', $username) ){//filter input.
                        ?>
                        <div id="invalidusername">
                        <br>
                        <?php
                            echo "Invalid username. Usernames cannot have spaces.";
                        ?>
                        </div>
                        <?php
                        exit;//exit the script.
                    }
                    //check if the username and pass match.
                    // Use a prepared statement
                    //santize input.
                  $username = $mysqli->real_escape_string($username);
                    $stmt = $mysqli->prepare("SELECT COUNT(*),user_id, password FROM users WHERE username=?");
                     if(!$stmt){
                            printf("Query Prep Failed: %s\n", $mysqli->error);
                            exit;
                        }
                    // Bind the parameter
                    $stmt->bind_param('s', $username);
                    $stmt->execute();
                     
                    // Bind the results
                    $stmt->bind_result($cnt, $user_id, $pwd_hash);
                    $stmt->fetch();
                    $stmt->close();//MUST DO THIS FOR NEXT RESULT.
                    $mysqli->next_result();//MUST DO THIS FOR NEXT RESULT.
                   
                    
                   //compare password here.
                    $pwd_guess = $_POST['passwordinput'];
                   if( $cnt == 1 && crypt($pwd_guess, $pwd_hash)==$pwd_hash){
                        // Login succeeded!
                        $_SESSION['username']=$username;
                        $_SESSION['user_id'] = $user_id;
                        $_SESSION['logged_in'] = "yes";
                        $_SESSION['token'] = substr(md5(rand()), 0, 10); // generate a 10-character random string

                        header("Location: stories.php");
                        // Redirect to your target page
                   }
                   else {
                        // Login failed; redirect back to the login screen?
                        //bad username and password?
                        ?>
                        <div id="error">
                            <br>
                            <?php echo "This username and password combination does not exist.";
                            ?>
                        </div>
                        <?php
                        //then redirect.
                    }
                }
            ?>
        </div>
    </body>
</html>