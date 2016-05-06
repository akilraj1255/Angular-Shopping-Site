<?php
//securty
header('Access-Control-Allow-Origin: http://ec2-52-22-71-32.compute-1.amazonaws.com:8000');

//http://ec2-52-22-71-32.compute-1.amazonaws.com/~chiraag/RuChiShopping/app/controllers.js

//sent over from controllers.js for ItemInfoCtrl
$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
@$productID = $request->chosenProduct;

$mysqli = new mysqli('localhost', 'phpUser', 'cse330', 'shopping');
if($mysqli->connect_errno) {
printf("Connection Failed: %s\n", $mysqli->connect_error);
exit;
}

//
$resultArray = array();//empty array.
        //now send back all the events.
    //get all the events.
    $stmt = $mysqli->prepare("select product_id, name, price,description,quantity,image_url from products where product_id=?");
    if(!$stmt){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $stmt->bind_param('i', $productID);
    $stmt->execute();
    $stmt->bind_result($prod_id, $prod_name, $prod_price, $prod_desc,$prod_quan, $prod_url);
    $counter = 0;
    while($stmt->fetch()){//add all the results to the array to be sent back.
        $counter++;
        $resultArray[] = array("counter" => $counter,
                               "product_id" => $prod_id,
                               "name" => $prod_name,
                               "price" => $prod_price,
                               "description" => $prod_desc,
                               "quantity" => $prod_quan,
                               "image_url" => $prod_url);

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
    echo json_encode(utf8ize($resultArray));


?>
