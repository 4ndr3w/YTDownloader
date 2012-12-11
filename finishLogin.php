<?php
include "config.php";
include "authCommon.php";
$response = $openID->complete($baseURL.$basePath);

$identifier = $response->getDisplayIdentifier();

if ( $response->status == Auth_OpenID_SUCCESS )
{
	$axFetcher = new Auth_OpenID_AX_FetchResponse();
	$axList = $axFetcher->fromSuccessResponse($response);
	$emailSplit = explode("@", $axList->data["http://axschema.org/contact/email"][0]);
	if ( $emailSplit[1] != $requiredEmailDomain )
		die("You must login using your ".$requiredEmailDomain." account.<br><a href='https://www.google.com/accounts/Logout'>Logout</a> before trying again.");
	if ( intval($emailSplit[0]) != 0 )
		die("Students may not access this system! <a href='https://www.google.com/accounts/Logout'>Logout</a>");
	$_SESSION['loggedIn'] = true;
	$_SESSION['email'] = $axList->data["http://axschema.org/contact/email"][0];
	header("Location: index.php");
}
else
{
	echo "OpenID auth failed!<br><br>";
	print_r($response);
}
?>
