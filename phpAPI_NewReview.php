<?php
    //securty
    header('Access-Control-Allow-Origin: http://ec2-52-22-71-32.compute-1.amazonaws.com:8000');
    
    //http://ec2-52-22-71-32.compute-1.amazonaws.com/~chiraag/RuChiShopping/app/controllers.js
    
    //sent over from controllers.js for ItemInfoCtrl
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    @$productID = $request->product;
    @$userID = $request->user;
    @$rating = $request->user_rating;
    @$review = $request->user_review;
    
    $mysqli = new mysqli('localhost', 'phpUser', 'cse330', 'shopping');
    if($mysqli->connect_errno) {
        printf("Connection Failed: %s\n", $mysqli->connect_error);
        exit;
    }
    
    //first add new review
    $stmt = $mysqli->prepare("insert into reviews(product_id, reviewer, rating, review) values (?, ?, ?, ?)");
    if(!$stmt){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $stmt->bind_param('iiis', $productID, $userID, $rating, $review);
    $stmt->execute();
    $stmt->close();
    $mysqli->next_result();//MUST DO THIS FOR NEXT RESULT.

    $resultArray = array();//empty array.
    
    //now query reviews to print updated version of reviews                
    $stmt = $mysqli->prepare("select reviews.product_id, users.first_name, users.last_name, reviews.rating, reviews.review from reviews inner join users on reviews.reviewer=users.id where product_id=?");
    if(!$stmt){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $stmt->bind_param('i', $productID);
    $stmt->execute();
    $stmt->bind_result($prod_id, $prod_reviewer_first_name, $prod_reviewer_last_name, $prod_rating, $prod_review);
    $counter = 0;
    while($stmt->fetch()){//add all the results to the array to be sent back.
        $counter++;
        $resultArray[] = array("counter" => $counter,
                               "product_id" => $prod_id,
                               "firstname" => $prod_reviewer_first_name,
                               "lastname" => $prod_reviewer_last_name,
                               "rating" => $prod_rating,
                               "review" => $prod_review);
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
