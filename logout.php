<?php
session_start();
$_SESSION['loggedIn'] = false;
$_SESSION['email'] = "";
session_destroy();
header("Location: https://www.google.com/accounts/Logout");
?>
