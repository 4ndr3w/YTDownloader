<?php
$loggedIn = $_SESSION['loggedIn'];
$email = $_SESSION['email'];

if ( !$loggedIn )
{
	header("Location: login.php");
}
?>