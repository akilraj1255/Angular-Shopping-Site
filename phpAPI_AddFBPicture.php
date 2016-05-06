<?php
    //securty
    header('Access-Control-Allow-Origin: http://ec2-52-22-71-32.compute-1.amazonaws.com:8000');
    
    //http://ec2-52-22-71-32.compute-1.amazonaws.com/~chiraag/RuChiShopping/app/controllers.js
    
    //sent over from controllers.js for ItemInfoCtrl
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    @$pictureURL = $request->profile_pic_URL;
    @$user_id = $request->user;
    
    $mysqli = new mysqli('localhost', 'phpUser', 'cse330', 'shopping');
    if($mysqli->connect_errno) {
        printf("Connection Failed: %s\n", $mysqli->connect_error);
        exit;
    }
    
    //first add new review
    $stmt = $mysqli->prepare("update users set profile_picture=? where id=?");
    if(!$stmt){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $stmt->bind_param('si', $pictureURL, $user_id);
    $stmt->execute();
    $stmt->close();
    $mysqli->next_result();//MUST DO THIS FOR NEXT RESULT.
?>
