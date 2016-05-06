<?php
//securty
header('Access-Control-Allow-Origin: http://ec2-52-22-71-32.compute-1.amazonaws.com:8000');

//http://ec2-52-22-71-32.compute-1.amazonaws.com/~chiraag/RuChiShopping/app/controllers.js

//sent over from controllers.js for ItemInfoCtrl
$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
@$userID = $request->user;

$mysqli = new mysqli('localhost', 'phpUser', 'cse330', 'shopping');
if($mysqli->connect_errno) {
printf("Connection Failed: %s\n", $mysqli->connect_error);
exit;
}
$resultDouble =0.00;
//$resultArray = array();//empty array.
        //now send back all the events.
    //get all the events. 
                
    $stmt = $mysqli->prepare("select sum(products.price) from products inner join shopping_cart on products.product_id=shopping_cart.product_id where shopping_cart.user=?");
    if(!$stmt){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $stmt->bind_param('i', $userID);
    $stmt->execute();
    $stmt->bind_result($price);
    while($stmt->fetch()){//add all the results to the array to be sent back.
        $resultDouble=$price;
    }
    $stmt->close();
    $mysqli->next_result();//MUST DO THIS FOR NEXT RESULT.
    //
    //print_r(array_values($resultArray));

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
    echo $resultDouble;

?>
