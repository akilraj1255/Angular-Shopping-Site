'use strict';

/* Controllers */

var shoppingControllers = angular.module('shoppingControllers', []);
var usernameG = "-1";//initalized null to mean not logged in.
var userIDG = -1;//initalized null to mean not logged in.
//import from MYSQL here.
var userFBPic = "";

shoppingControllers.controller('ProductsCtrl', function ($scope, $http) {
  $http.get('http://ec2-52-22-71-32.compute-1.amazonaws.com/~chiraag/CreativePHPDB/phpAPI_Products.php').success(function(data){
    $scope.products = data;
  });
  $scope.orderProp = 'brand';
  $scope.uname = usernameG;
  $scope.uid = userIDG;

  $scope.add_to_cart = function(item_to_add) {
    if(userIDG==-1){alert("Please log in to use this function.");}else{
      var request = $http({
        method: "POST",
        url: "http://ec2-52-22-71-32.compute-1.amazonaws.com/~chiraag/CreativePHPDB/phpAPI_UpdateCartAndWishList.php",
        data:{
          user: userIDG, //arbitrary number for now (user_id 1 is Ruth)
          product: item_to_add,
          addOrDelete: "add", //we're passing in this bc I'm handling 4 functions in UpdateCartAndWishList.php (add cart, add wishlist, remove cart, remove wishlist)
          cartOrWishList: "cart"
        },
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      });
      request.success(function(){
        alert("Successfully added item to your cart!");
      });

    }}

    $scope.add_to_wishlist = function(item_to_add) {
      if(userIDG==-1){alert("Please log in to use this function.");}else{
        var request = $http({
          method: "POST",
          url: "http://ec2-52-22-71-32.compute-1.amazonaws.com/~chiraag/CreativePHPDB/phpAPI_UpdateCartAndWishList.php",
          data:{
            user: userIDG, //arbitrary number for now (user_id 1 is Ruth)
            product: item_to_add,
            addOrDelete: "add",
            cartOrWishList: "wishlist"
          },
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        });
        request.success(function(){
          alert("Successfully added item to your wishlist!");
        });
      }}
    });

    shoppingControllers.controller('LoginCtrl', ['$scope', '$http', '$window',
    function($scope, $http, $window) {
      $scope.login = function(usr,pwd) {
        var request = $http({
          method: "POST",
          url: "http://ec2-52-22-71-32.compute-1.amazonaws.com/~chiraag/CreativePHPDB/loginregister.php",
          data:{
            username: usr,
            password: pwd,
            task: "login"
          },
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        });

        request.success(function(response){
          if(response['result']=='success'){
            usernameG = response["username"];
            userIDG = parseInt(response["user_id"]);
            $window.location.href='#/products';
          }
          else {
            alert("Error: "+response['message']);
          }

        });

      }

      $scope.register = function(usr,pwd,first,last,addres,cty,stt) {
        var request = $http({
          method: "POST",
          url: "http://ec2-52-22-71-32.compute-1.amazonaws.com/~chiraag/CreativePHPDB/loginregister.php",
          data:{
            username: usr,
            password: pwd,
            task: "register",
            firstname: first,
            lastname: last,
            address: addres,
            city: cty,
            state: stt,
          },
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        });

        request.success(function(response){
          if(response['result']=='success'){
            alert("You have successfully registered. Please log in.");
            $window.location.href='#/products';
          }else{
            alert("Error: "+response['message']);

          }
        });
      }
    }
  ]);


  shoppingControllers.controller('ProductInfoCtrl', ['$scope', '$routeParams', '$http',
  function($scope, $routeParams, $http) {

    //sending over what product we want to query info for
    //this is taken from http://stackoverflow.com/questions/31637243/pass-angularjs-value-to-php-variable

    //this is to query product info
    var request = $http({
      method: "POST",
      url: "http://ec2-52-22-71-32.compute-1.amazonaws.com/~chiraag/CreativePHPDB/phpAPI_ProductInfo.php",
      data:{
        chosenProduct: $routeParams.productId
      },
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    });

    request.success(function(response){
      $scope.products = response;
    });

    //this is to query reviews
    var request2 = $http({
      method: "POST",
      url: "http://ec2-52-22-71-32.compute-1.amazonaws.com/~chiraag/CreativePHPDB/phpAPI_Reviews.php",
      data:{
        chosen: $routeParams.productId
      },
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    });

    request2.success(function(response){
      $scope.reviews = response;
    });

    $scope.new_review = function(){
      if(userIDG==-1){alert("Please log in to use this function.");}else{
        var request = $http({
          method: "POST",
          url: "http://ec2-52-22-71-32.compute-1.amazonaws.com/~chiraag/CreativePHPDB/phpAPI_NewReview.php",
          data:{
            product: $routeParams.productId,
            user: userIDG,
            user_rating: $scope.rating,
            user_review: $scope.review
          },
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        });
        request.success(function(response){
          $scope.reviews = response;
        });
      }}
    }
  ]);

  shoppingControllers.controller('CartAndWishListCtrl', ['$scope', '$routeParams', '$http', '$window',
  function($scope, $routeParams, $http, $window) {

    //this is to query shopping cart for user id
    if(userIDG==-1){
      alert("Please log in to use this function.");
      $window.location.href='#/products';
    }else{
      var request = $http({
        method: "POST",
        url: "http://ec2-52-22-71-32.compute-1.amazonaws.com/~chiraag/CreativePHPDB/phpAPI_QueryCartAndWishList.php",
        data:{
          user: userIDG, //arbitrary number for now (user_id 1 is Ruth)
          cartOrWishList: "cart" //sending this over so we can use an if statement to determine if it's shopping cart or wishlist to query (saves us an extra PHP file)
        },
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      });

      request.success(function(response){
        $scope.cart = response;
      });

      //this is to query wish list for user id
      var request2 = $http({
        method: "POST",
        url: "http://ec2-52-22-71-32.compute-1.amazonaws.com/~chiraag/CreativePHPDB/phpAPI_QueryCartAndWishList.php",
        data:{
          user: userIDG, //arbitrary number for now (user_id 1 is Ruth)
          cartOrWishList: "wishlist" //sending this over so we can use an if statement to determine if it's shopping cart or wishlist to query (saves us an extra PHP file)
        },
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      });

      request2.success(function(response){
        $scope.wishlist = response;
      });

      //this is to get the cart total
      var request3 = $http({
        method: "POST",
        url: "http://ec2-52-22-71-32.compute-1.amazonaws.com/~chiraag/CreativePHPDB/phpAPI_CartTotal.php",
        data:{
          user: userIDG
        },
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      });

      request3.success(function(response){
        $scope.cartTotal = response;
      });

    }
    $scope.remove_from_cart = function(item_to_remove){
      if(userIDG==-1){
            alert("Please log in to use this function.");
      }else{
        var request = $http({
          method: "POST",
          url: "http://ec2-52-22-71-32.compute-1.amazonaws.com/~chiraag/CreativePHPDB/phpAPI_UpdateCartAndWishList.php",
          data:{
            user: userIDG, //arbitrary number for now (user_id 1 is Ruth)
            product: item_to_remove,
            addOrDelete: "delete",
            cartOrWishList: "cart"
          },
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        });
        request.success(function(response){
          $scope.cart = response;
        });

        //this is to get the cart total
            var request2 = $http({
              method: "POST",
              url: "http://ec2-52-22-71-32.compute-1.amazonaws.com/~chiraag/CreativePHPDB/phpAPI_CartTotal.php",
              data:{
                user: userIDG
              },
              headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            });
            request2.success(function(response){
              $scope.cartTotal = response;
            });
      }}

      $scope.remove_from_wishlist = function(item_to_remove){
        if(userIDG==-1){alert("Please log in to use this function.");}else{
          var request = $http({
            method: "POST",
            url: "http://ec2-52-22-71-32.compute-1.amazonaws.com/~chiraag/CreativePHPDB/phpAPI_UpdateCartAndWishList.php",
            data:{
              user: userIDG, //arbitrary number for now (user_id 1 is Ruth)
              product: item_to_remove,
              addOrDelete: "delete",
              cartOrWishList: "wishlist"
            },
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
          });
          request.success(function(response){
            $scope.wishlist = response;
          });
        }}

        $scope.proceed_to_checkout = function(){
          $window.location.href='#/checkout';
        }
      }
    ]);

    shoppingControllers.controller('CheckoutCtrl', ['$scope', '$http', '$window',
    function($scope, $http, $window){

      var requestToGetCartTotal = $http({
            method: "POST",
            url: "http://ec2-52-22-71-32.compute-1.amazonaws.com/~chiraag/CreativePHPDB/phpAPI_CartTotal.php",
            data:{
              //these are all passed in from the form on checkout.html that calls ng-submit="checkout()"
              user: userIDG
            },
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
          });
      requestToGetCartTotal.success(function(response){
            $scope.cartTotal = response;
      });

      $scope.checkout = function(cartTotal){ //called in checkout.html via ng-submit="checkout()"
            if(userIDG==-1){
                  alert("Please log in to use this function.");
                  $window.location.href='#/products';
            }else{
                  if ($scope.promo_code=='CSE330') {
                        cartTotal=cartTotal*0.70;
                        cartTotal=cartTotal.toFixed(2);
                        alert("Promo code CSE330 for 30% was successfully applied! Your total is now $" + cartTotal);
                  }
              var request = $http({
                method: "POST",
                url: "http://ec2-52-22-71-32.compute-1.amazonaws.com/~chiraag/CreativePHPDB/phpAPI_Checkout.php", //we'll insert all these values into our orders table here
                data:{
                  //these are all passed in from the form on checkout.html that calls ng-submit="checkout()"
                  first_name: $scope.first_name,
                  last_name: $scope.last_name,
                  email: $scope.email,
                  credit_card_name: $scope.credit_card_name,
                  credit_card_number: $scope.credit_card_num,
                  credit_card_expiration: $scope.credit_card_expiration,
                  credit_card_security_num: $scope.credit_card_security_num,
                  billing_address: $scope.billing_address,
                  billing_city: $scope.billing_city,
                  billing_state: $scope.billing_state,
                  billing_zipcode: $scope.billing_zipcode,
                  shipping_address: $scope.shipping_address,
                  shipping_city: $scope.shipping_city,
                  shipping_state: $scope.shipping_state,
                  shipping_zipcode: $scope.shipping_zipcode,
                  comments: $scope.comments,
                  user: userIDG,
                  price: cartTotal
                },
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
              });
              request.success(function(response){
                //after we successfully add a new order to our orders table, let's go to the "thank you for your order" page using $window
                $window.location.href='#/thankyou';
              });
            }
      }
    }
  ]);

  shoppingControllers.controller('ProfileCtrl', ['$scope', '$routeParams', '$http', '$window',
  function($scope, $routeParams, $http, $window) {
    if(userIDG==-1){
      alert("Please log in to use this function.");
      $window.location.href='#/products';
    }else{
      //querying users table
      var request = $http({
        method: "POST",
        url: "http://ec2-52-22-71-32.compute-1.amazonaws.com/~chiraag/CreativePHPDB/phpAPI_Profile.php",
        data:{
          user: userIDG
        },
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      });
      request.success(function(response){
        $scope.userInfo = response;
      });

      //querying orders table
      var request2 = $http({
        method: "POST",
        url: "http://ec2-52-22-71-32.compute-1.amazonaws.com/~chiraag/CreativePHPDB/phpAPI_Orders.php",
        data:{
          user: userIDG,
          queryOrDelete: "query",
          order_to_delete: 0
        },
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      });
      request2.success(function(response){
        $scope.orders = response;
      });
    }
    //deleting an order
    $scope.delete_order = function(order){
      if(userIDG==-1){alert("Please log in to use this function.");}else{
        var request = $http({
          method: "POST",
          url: "http://ec2-52-22-71-32.compute-1.amazonaws.com/~chiraag/CreativePHPDB/phpAPI_Orders.php",
          data:{
            user: userIDG,
            queryOrDelete: "delete",
            order_to_delete: order
          },
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        });
        request.success(function(response){
          alert("Your order has been canceled!");
          $scope.orders = response; //display the updated orders list
        });
      }
      }

      $scope.set_fb_pic = function(){
            //first change picture URL in users table
            var request = $http({
            method: "POST",
            url: "http://ec2-52-22-71-32.compute-1.amazonaws.com/~chiraag/CreativePHPDB/phpAPI_AddFBPicture.php",
            data:{
              profile_pic_URL: userFBPic,
              user: userIDG
            },
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            });
            request.success(function(){
              alert("Your profile picture has been successfully saved!");
            });

            //now update page to show new picture
            var request2 = $http({
                  method: "POST",
                  url: "http://ec2-52-22-71-32.compute-1.amazonaws.com/~chiraag/CreativePHPDB/phpAPI_Profile.php",
                  data:{
                    user: userIDG
                  },
                  headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            });
            request2.success(function(response){
                  $scope.userInfo = response;
            });
      }

    }
  ]);

  shoppingControllers.controller('GMapsCtrl', ['$scope', '$routeParams', '$http', '$window',
  function($scope, $routeParams, $http, $window) {
    //display directions div
    $scope.directionsDisplay;
$scope.directionsService = new google.maps.DirectionsService();
$scope.map;

  $scope.directionsDisplay = new google.maps.DirectionsRenderer();
  $scope.chicago = new google.maps.LatLng(41.850033, -87.6500523);
  $scope.mapOptions = {
    zoom:7,
    center: $scope.chicago
  }
  $scope.map = new google.maps.Map(document.getElementById("map"), $scope.mapOptions);
  $scope.directionsDisplay.setMap($scope.map);
  $scope.directionsDisplay.setPanel(document.getElementById("directionsPanel"));

//
var orginAdd = "";
if(userIDG==-1){orginAdd = "6515 Wydown Blvd., St. Louis, MO";
$scope.ends = "1 Brookings Dr., St. Louis, MO";
$scope.requestm = {
  origin: orginAdd,
  destination: $scope.ends,
  travelMode: google.maps.TravelMode.DRIVING
};
$scope.directionsService.route($scope.requestm, function(response, status) {
  if (status == google.maps.DirectionsStatus.OK) {
    $scope.directionsDisplay.setDirections(response);
  }
});

}
else{//if logged in.
  var request = $http({
    method: "POST",
    url: "http://ec2-52-22-71-32.compute-1.amazonaws.com/~chiraag/CreativePHPDB/getAddress.php",
    data:{
      user: userIDG
    },
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
  });
  request.success(function(response){
    //get address info.
    orginAdd = String(response['address']+", "+response['city']+", "+response['state']);
    $scope.ends = "1 Brookings Dr., St. Louis, MO";
    $scope.requestm = {
      origin: orginAdd,
      destination: $scope.ends,
      travelMode: google.maps.TravelMode.DRIVING
    };
    $scope.directionsService.route($scope.requestm, function(response, status) {
      if (status == google.maps.DirectionsStatus.OK) {
        $scope.directionsDisplay.setDirections(response);
      }
    });
    //alert(response['address']);
  });

}
//
  //$scope.starts = "1 Euclid Ave., St. Louis, MO";

  }]);


  shoppingControllers.controller('LogoutCtrl', function($scope, $window){
    if(userIDG==-1){
      alert("Logging out failed because you are not currently logged in.");
      $window.location.href='#/products';
    }else{
      userIDG = -1;
      alert("You've been logged out. See you next time!");
      $window.location.href='#/products';
    }
  });

  shoppingControllers.controller('FBCtrl', function($scope, $http, ezfb, $window, $location) {
      updateLoginStatus(updateApiMe);

      $scope.login = function () {
        /**
         * Calling FB.login with required permissions specified
         * https://developers.facebook.com/docs/reference/javascript/FB.login/v2.0
         */
        ezfb.login(function (res) {
          /**
           * no manual $scope.$apply, I got that handled
           */
          if (res.authResponse) {
            updateLoginStatus(updateApiMe);
            alert("You have successfully logged into Facebook!");
          }
        }, {scope: 'email'});
      };

      $scope.logout = function () {

        /**
         * Calling FB.logout
         * https://developers.facebook.com/docs/reference/javascript/FB.logout
         */
        ezfb.logout(function () {
          updateLoginStatus(updateApiMe);
          alert("You have successfully logged out of Facebook!");
        });

      };

      /**
       * For generating better looking JSON results
       */
      var autoToJSON = ['loginStatus', 'apiMe'];
      angular.forEach(autoToJSON, function (varName) {
        $scope.$watch(varName, function (val) {
          $scope[varName + 'JSON'] = JSON.stringify(val, null, 2);
        }, true);
      });

      /**
      * Update loginStatus result
      */
     function updateLoginStatus (more) {
       ezfb.getLoginStatus(function (res) {
         $scope.loginStatus = res;

         (more || angular.noop)();
       });
     }

      /**
       * Update api('/me') result
       */
      function updateApiMe() {
        ezfb.api('/me?fields=picture.height(961)', function (res) {
          $scope.apiMe = res;
          userFBPic = $scope.apiMe.picture.data.url;
        });
      }
});
