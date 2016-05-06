'use strict';
//Lets shop! - CK. Modified.

/* App Module */

var shoppingApp = angular.module('RuChiShoppingApp', [
  'ngRoute',
  'shoppingControllers',
  'ezfb', //for fb login
  'hljs' //for fb login

]);

shoppingApp.config(function (ezfbProvider) {
  /**
   * Basic setup
   *
   * https://github.com/pc035860/angular-easyfb#configuration
   */
  ezfbProvider.setInitParams({
    appId: '169768563376788'
  });  
})

shoppingApp.config(['$routeProvider',
  function($routeProvider) {
    $routeProvider.
      when('/products', {
        templateUrl: 'partials/products.html',
        controller: 'ProductsCtrl'
      }).
      when('/login', {
        templateUrl: 'partials/login.html',
        controller: 'LoginCtrl'
      }).
      when('/products/:productId', {
        templateUrl: 'partials/productInfo.html',
        controller: 'ProductInfoCtrl'
      }).
      when('/map', {
        templateUrl: 'partials/GMapsHelloWorld.html',
        controller: 'GMapsCtrl'
      }).
      when('/cart', {
        templateUrl: 'partials/cart.html',
        controller: "CartAndWishListCtrl"
      }).
      when('/wishlist',{
        templateUrl: 'partials/wishlist.html',
        controller: "CartAndWishListCtrl"
      }).
      when('/profile',{
        templateUrl: 'partials/profile.html',
        controller: "ProfileCtrl"
      }).
      when('/checkout',{
        templateUrl: 'partials/checkout.html',
        controller: "CheckoutCtrl"
      }).
      when('/thankyou',{
        templateUrl: 'partials/orderSuccessAndThanks.html',
        controller: "OrderSuccessAndThanksCtrl"
      }).
      when('/logout',{
        templateUrl: 'partials/logout.html',
        controller: 'LogoutCtrl'
      }).
      when('/signup',{
        templateUrl: 'partials/signup.html',
        controller: 'SignupCtrl'
      }).
      otherwise({
        redirectTo: '/products'
      });
  }]);
