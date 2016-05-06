<?php
    //securty
    header('Access-Control-Allow-Origin: http://ec2-52-22-71-32.compute-1.amazonaws.com:8000');

    //sent over from controllers.js for ItemInfoCtrl
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    @$user_id = $request->user;

    $mysqli = new mysqli('localhost', 'phpUser', 'cse330', 'shopping');
    if($mysqli->connect_errno) {
    printf("Connection Failed: %s\n", $mysqli->connect_error);
    exit;
    }

    $resultArray = array();//empty array.

    $stmt = $mysqli->prepare("select first_name, last_name, city, address, state, zipcode, joined, username, profile_picture from users where id=?");
    if(!$stmt){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->bind_result($first_name, $last_name, $city, $address, $state, $zipcode, $joined, $username, $profile_picture);
    $counter = 0;
    while($stmt->fetch()){//add all the results to the array to be sent back.
        $counter++;
        $resultArray[] = array("counter" => $counter,
                               "firstname" => $first_name,
                               "lastname" => $last_name,
                               "city" => $city,
                               "address" => $address,
                               "state" => $state,
                               "zipcode" => $zipcode,
                               "joined" => $joined,
                               "username" => $username,
                               "pictureURL" =>$profile_picture);
    }
    $stmt->close();
    $mysqli->next_result();//MUST DO THIS FOR NEXT RESULT.

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
