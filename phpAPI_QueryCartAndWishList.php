<?php
//securty
header('Access-Control-Allow-Origin: http://ec2-52-22-71-32.compute-1.amazonaws.com:8000');

//http://ec2-52-22-71-32.compute-1.amazonaws.com/~chiraag/RuChiShopping/app/controllers.js

//sent over from controllers.js for ItemInfoCtrl
$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
@$userID = $request->user;
@$cartOrWishList = $request->cartOrWishList;

$mysqli = new mysqli('localhost', 'phpUser', 'cse330', 'shopping');
if($mysqli->connect_errno) {
printf("Connection Failed: %s\n", $mysqli->connect_error);
exit;
}

if($cartOrWishList == "cart"){
        $resultArray = array();//empty array.
            //now send back all the events.
        //get all the events. 
                    
        $stmt = $mysqli->prepare("select products.product_id, products.name, products.price, products.image_url from shopping_cart inner join products on shopping_cart.product_id=products.product_id where shopping_cart.user = ?");
        if(!$stmt){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt->bind_param('i', $userID);
        $stmt->execute();
        $stmt->bind_result($prod_id, $prod_name, $prod_price, $prod_image);
        $counter = 0;
        while($stmt->fetch()){//add all the results to the array to be sent back.
            $counter++;
            $resultArray[] = array("counter" => $counter,
                                   "product_id" => $prod_id,
                                   "product_name" => $prod_name,
                                   "price"=>$prod_price,
                                   "imageURL"=>$prod_image);
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
}

elseif($cartOrWishList =="wishlist"){
    $resultArray = array();//empty array.
            //now send back all the events.
        //get all the events. 
                    
        $stmt = $mysqli->prepare("select products.product_id, products.name, products.price, products.image_url from wish_list inner join products on wish_list.product_id=products.product_id where user =?");
        if(!$stmt){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt->bind_param('i', $userID);
        $stmt->execute();
        $stmt->bind_result($prod_id, $prod_name, $prod_price, $prod_image);
        $counter = 0;
        while($stmt->fetch()){//add all the results to the array to be sent back.
            $counter++;
            $resultArray[] = array("counter" => $counter,
                                   "product_id" => $prod_id,
                                   "product_name" => $prod_name,
                                   "price"=>$prod_price,
                                   "imageURL"=>$prod_image);
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
}


?>
