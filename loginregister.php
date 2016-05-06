<?php
//securty
header('Access-Control-Allow-Origin: http://ec2-52-22-71-32.compute-1.amazonaws.com:8000');

//http://ec2-52-22-71-32.compute-1.amazonaws.com/~chiraag/RuChiShopping/app/controllers.js

//sent over from controllers.js for LoginCtrl
$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
@$user = $request->username;
@$pass = $request->password;
@$task = $request->task;

$mysqli = new mysqli('localhost', 'phpUser', 'cse330', 'shopping');
if($mysqli->connect_errno) {
  printf("Connection Failed: %s\n", $mysqli->connect_error);
  exit;
}

//
$resultArray = array();
if( !preg_match('/^[\w_\-]+$/', $user) ){//filter input.
  $resultArray["result"]="failure";
  $resultArray["message"]="Invalid username";
  echo json_encode(utf8ize($resultArray));
  exit;//exit the script.
}
if($task=="login"){

  //check if the username and pass match.
  // Use a prepared statement
  //santize input.
  $username = $mysqli->real_escape_string($user);
  $stmt = $mysqli->prepare("SELECT COUNT(*),id, password FROM users WHERE username=?");
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
  $pwd_guess = $pass;
  if( $cnt == 1 && crypt($pwd_guess, $pwd_hash)==$pwd_hash){
    // Login succeeded!
    $resultArray['result']="success";
    $resultArray['username']=$username;
    $resultArray['user_id'] = $user_id;
    //$_SESSION['token'] = substr(md5(rand()), 0, 10); // generate a 10-character random string
  }else{
    $resultArray['result']="failure";
    $resultArray['message']="Username and password do not match.";

  }
} else if($task == "register"){
  @$firstnameP = $request->firstname;
  @$lastnameP = $request->lastname;
  @$addressP = $request->address;
  @$cityP = $request->city;
  @$stateP = $request->state;

  $newusername = $mysqli->real_escape_string($user);//santize
  //does the username already exist?
  // Use a prepared statement
  $stmt = $mysqli->prepare("SELECT COUNT(*) FROM users WHERE username=?");
  if(!$stmt){
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
  }
  // Bind the parameter
  $stmt->bind_param('s', $newusername);
  $stmt->execute();

  // Bind the results
  $stmt->bind_result($cnt);
  $stmt->fetch();
  $stmt->close();//MUST DO THIS FOR NEXT RESULT.
  $mysqli->next_result();//MUST DO THIS FOR NEXT RESULT.
  //if the user does not exist!
  if(!$cnt == 0){
    $resultArray["result"]="failure";
    $resultArray["message"]="Username already exists. Please log in.";
    echo json_encode(utf8ize($resultArray));
    exit;

  }

  //encrypt password and add to users database.
    $saltedPassword= crypt($pass);

    $stmt = $mysqli->prepare("INSERT INTO users (username, password,first_name,last_name,address,city,state) VALUES (?,?,?,?,?,?,?)");
    if(!$stmt){
      printf("Query Prep Failed: %s\n", $mysqli->error);
      exit;
    }
    $stmt->bind_param('sssssss',$newusername, $saltedPassword,$firstnameP,$lastnameP,$addressP,$cityP,$stateP);
    $stmt->execute();
    $stmt->close();
    $mysqli->next_result();//MUST DO THIS FOR NEXT RESULT.

    $resultArray['result']="success";

}else{
  $resultArray['result']="failure";
  $resultArray["message"]="Task was not parsed properly.";


}

  //send it back.
  function utf8ize($d) {
    if (is_array($d)) {
      foreach ($d as $k => $v) {
        $d[$k] = utf8ize($v);
      }
    } else if (is_string ($d)) {
      return utf8_encode($d);
    }
    return $d;
  }
  echo json_encode(utf8ize($resultArray));


  ?>
