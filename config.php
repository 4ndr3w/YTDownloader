<?php

$baseURL = "http://127.0.0.1/youtube/";
$basePath = "finishLogin.php";
$siteName = "Penn Manor YouTube Downloader";

// Download States
define("DOWNLOAD_QUEUE", 0);
define("DOWNLOAD_INPROGRESS", 1);
define("DOWNLOAD_FINISHED", 2);
define("DOWNLOAD_FAILED", 3);

$databaseInfo = array(
	"host" => "127.0.0.1",
	"user" => "root",
	"password" => "",
	"database" => "youtube"
);

// Everyone
$providerURL = "https://www.google.com/accounts/o8/id";

$requiredEmailDomain = "lobos.me";

$expireTime = "+5 days"; // Video expire time

$preferHTML5 = true; // Display videos using HTML5 <video> when possible

session_start();
?>
