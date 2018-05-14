<?php
require "db_connect.php";
session_start();

// initializing variables
$uid = "";
$email    = "";
$errors = array();

// connect to the database
$db = mysqli_connect('localhost', 'root', 'root', 'sqli');

// REGISTER USER
if (isset($_POST['reg_user'])) {
  // receive all input values from the form
  $uid = mysqli_real_escape_string($db, $_POST['uid']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $pwd_1 = mysqli_real_escape_string($db, $_POST['pwd_1']);
  $pwd_2 = mysqli_real_escape_string($db, $_POST['pwd_2']);

  // form validation: ensure that the form is correctly filled ...
  // by adding (array_push()) corresponding error unto $errors array
  if (empty($uid)) { array_push($errors, "Username is required"); }
  if (empty($email)) { array_push($errors, "Email is required"); }
  if (empty($pwd_1)) { array_push($errors, "Password is required"); }
  if ($pwd_1 != $pwd_2) {
	array_push($errors, "The two pwds do not match");
  }

  // first check the database to make sure
  // a user does not already exist with the same uid and/or email
  $user_check_query = "SELECT * FROM user WHERE uid='$uid' OR email='$email' LIMIT 1";
  $result = mysqli_query($db, $user_check_query);
  $user = mysqli_fetch_assoc($result);

  if ($user) { // if user exists
    if ($user['uid'] === $uid) {
      array_push($errors, "User already exists");
    }

    if ($user['email'] === $email) {
      array_push($errors, "Email already exists");
    }
  }

  // Finally, register user if there are no errors in the form
  if (count($errors) == 0) {
  	$pwd = hash('sha512', $pwd_1);//encrypt the pwd before saving in the database

  	$query = "INSERT INTO user (uid, email, pwd)
  			  VALUES('$uid', '$email', '$pwd')";
  	mysqli_query($db, $query);
  	$_SESSION['uid'] = $uid;
  	$_SESSION['success'] = "You are now logged in";
  	header('location: index.php');
  }
}
