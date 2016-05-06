<?php
    //securty
    header('Access-Control-Allow-Origin: http://ec2-52-22-71-32.compute-1.amazonaws.com:8000');
    
    //sent over from controllers.js for ItemInfoCtrl
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    @$first_name = $request->first_name;
    @$last_name = $request->last_name;
    @$email = $request->email;
    @$cc_name = $request->credit_card_name;
    @$cc_num = $request->credit_card_num;
    @$cc_expiration = $request->credit_card_expiration;
    @$cc_security = $request->credit_card_security_num;
    @$billing_address = $request->billing_address;
    @$billing_city = $request->billing_city;
    @$billing_state = $request->billing_state;
    @$billing_zipcode = $request->billing_zipcode;
    @$shipping_address = $request->shipping_address;
    @$shipping_city = $request->shipping_city;
    @$shipping_state = $request->shipping_state;
    @$shipping_zipcode = $request->shipping_zipcode;
    @$comments = $request->comments;
    @$user_id = $request->user;
    @$order_total = $request->price;
    
    $mysqli = new mysqli('localhost', 'phpUser', 'cse330', 'shopping');
    if($mysqli->connect_errno) {
    printf("Connection Failed: %s\n", $mysqli->connect_error);
    exit;
    }

    $resultArray = array();//empty array.
  
    $stmt = $mysqli->prepare("insert into orders(first_name, last_name, email, credit_card_name, credit_card_number, credit_card_expiration, credit_card_security_number, billing_address, billing_city, billing_state, billing_zipcode, shipping_address, shipping_city, shipping_state, shipping_zipcode, comments, user_id, order_price) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    if(!$stmt){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $stmt->bind_param('ssssisisssisssisid', $first_name, $last_name, $email, $cc_name, $cc_num, $cc_expiration, $cc_security, $billing_address, $billing_city, $billing_state, $billing_zipcode, $shipping_address, $shipping_city, $shipping_state, $shipping_zipcode, $comments, $user_id, $order_total);
    $stmt->execute();
?>
