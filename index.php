<?php
require_once "config.php";
require_once "db.php";
require_once "authcheck.php";
$id = 0;
$urlInfo = "";
if ( array_key_exists("videourl", $_POST) && !empty($_POST['videourl']) && ($urlInfo = parse_url($_POST['videourl'])) && (substr($_POST['videourl'], 0, 7) == "http://" || substr($_POST['videourl'], 0, 8) == "https://") )
{
	$videourl = $_POST['videourl'];
	if ( ($urlInfo['host'] == "youtube.com" || $urlInfo['host'] == "www.youtube.com") ) // Attempt to clean up YouTube urls
	{
		$queryParams = explode("&", $urlInfo['query']);
		$videoid = "";
		foreach ( $queryParams as $param )
		{
			$paramData = explode("=", $param);
			if ( $paramData[0] == "v" )
				$videoid = $paramData[1];
		}
		$videourl = "http://www.youtube.com/watch?v=".$videoid;
	}
	
	global $id;
	global $success;
	
	$id = 0;
	if ( ($id = $database->isVideoInDatabase($videourl)) != false )
		$database->resetVideoExpire($videourl);
	else
		$id = $database->addVideoToQueue($videourl, $_SESSION['email']);
		
	$success = ($id != -1);
	
	
	if ( !$success )
		die("Failed!");
}
?>
<html>
<head>
	<title>YouTube Downloader</title>
</head>

<style type="text/css">
	body
	{
		background-color: #dddddd;
	}
	#spacer
	{
		height: 40%;
	}

	#formbox
	{
		border: solid 1px;
		width: 50%;
		margin: auto;
		background-color: white;
		-webkit-border-radius: 10px;
		-moz-border-radius: 10px;
		border-radius: 10px;
	}

	#container
	{
		padding: 10px;
		text-align: center;

	}

	#inputBox
	{
		font-size: 20px;
		width: 100%;

	}

	#result
	{
		text-align: center;
		border: solid 1px;

		margin: auto;
		margin-bottom: 10px;
		padding: 9px;
		background-color: white;

		width: 70%;
	}

	.success
	{
		background-color: green;
	}

	.fail
	{
		background-color: red;
	}

</style>
<body>
	<a href="logout.php">Logout</a>
	<div id="spacer"></div>
	<div id="formbox">
		<div id="container">
				<form method="post" action="">
					<input id="inputBox" name="videourl" type="text" />
					<br>
					<input type="submit" value="Queue Download" />
				</form>
		</div>
		<?php
		if ( array_key_exists("videourl", $_POST) )
		{
			global $id;
		?>
	
			<div id="result">
				<?php
				if ( $success )
					echo "Your video is now in the queue to be downloaded. <br>Watch it at: <a href='watch/".$id."'>".$baseURL."watch/".$id."</a>";
				else
					echo "Something went wrong while adding your video to the queue. Check the URL.";
				?>
			</div>
		<?php
		}
		?>
	</div>

</body>

