<?php
    //securty
    header('Access-Control-Allow-Origin: http://ec2-52-22-71-32.compute-1.amazonaws.com:8000');
    
    //sent over from controllers.js for ItemInfoCtrl
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    @$user_id = $request->user;
    @$queryOrDelete = $request->queryOrDelete;
    @$order_id = $request->order_to_delete;
    
    $mysqli = new mysqli('localhost', 'phpUser', 'cse330', 'shopping');
    if($mysqli->connect_errno) {
        printf("Connection Failed: %s\n", $mysqli->connect_error);
        exit;
    }

    if($queryOrDelete=="query") {
        $resultArray = array();//empty array
        $stmt = $mysqli->prepare("select order_id, first_name, last_name, email, credit_card_number, shipping_address, shipping_city, shipping_state, shipping_zipcode, comments, date_placed, order_price from orders where user_id=?");
        if(!$stmt){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $stmt->bind_result($order_id, $first_name, $last_name, $email, $cc_num, $shipping_address, $shipping_city, $shipping_state, $shipping_zipcode, $comments, $date_placed, $order_price);
        $counter = 0;
        while($stmt->fetch()){//add all the results to the array to be sent back.
            $counter++;
            $resultArray[] = array("counter" => $counter,
                                   "orderID" => $order_id,
                                   "firstname" => $first_name,
                                   "lastname" => $last_name,
                                   "email" => $email,
                                   "cc_num" => $cc_num,
                                   "shipping_address" => $shipping_address,
                                   "shipping_city" => $shipping_city,
                                   "shipping_state" => $shipping_state,
                                   "shipping_zipcode" => $shipping_zipcode,
                                   "comments" => $comments,
                                   "date_placed" => $date_placed,
                                   "order_total" => $order_price);
        
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
    }
    
    elseif($queryOrDelete=="delete"){
        //first delete the order
        $stmt = $mysqli->prepare("delete from orders where order_id=?");
        if(!$stmt){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt->bind_param('i', $order_id);
        $stmt->execute();
        
        //now print out updated orders list
        $resultArray = array();//empty array
        $stmt = $mysqli->prepare("select order_id, first_name, last_name, email, credit_card_number, shipping_address, shipping_city, shipping_state, shipping_zipcode, comments, date_placed, order_price from orders where user_id=?");
        if(!$stmt){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $stmt->bind_result($order_id, $first_name, $last_name, $email, $cc_num, $shipping_address, $shipping_city, $shipping_state, $shipping_zipcode, $comments, $date_placed, $order_price);
        $counter = 0;
        while($stmt->fetch()){//add all the results to the array to be sent back.
            $counter++;
            $resultArray[] = array("counter" => $counter,
                                   "orderID" => $order_id,
                                   "firstname" => $first_name,
                                   "lastname" => $last_name,
                                   "email" => $email,
                                   "cc_num" => $cc_num,
                                   "shipping_address" => $shipping_address,
                                   "shipping_city" => $shipping_city,
                                   "shipping_state" => $shipping_state,
                                   "shipping_zipcode" => $shipping_zipcode,
                                   "comments" => $comments,
                                   "date_placed" => $date_placed,
                                   "order_total" => $order_price);
        
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
    }
    
?>
