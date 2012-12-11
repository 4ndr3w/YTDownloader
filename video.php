<?php
$id = intval($_GET['id']);
if ( intval($id) == 0 )
		die("Invalid video ID");

require_once "config.php";
require_once "db.php";

$video = $database->getVideo($id);

if ( !$video )
	die("This video ID does not exist!");
else if ( $video['status'] == DOWNLOAD_FAILED )
	die("This video failed to download.");
else if ( $video['status'] == DOWNLOAD_INPROGRESS )
	die("This video is currently downloading");
else if ( $video['status'] != DOWNLOAD_FINISHED )
	die("The video is not ready yet. It is in the download queue. Please try again in 5 minutes.");
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

	#container
	{
		text-align: center;
		margin:auto;
		display:block;
	}

</style>
<body>
<center>
<div id="container">

<?php
if ( strstr($video['videofile'], ".mp4") && $preferHTML5 )
{
?>
	<video width="640" height="480" controls="controls">
		<source src="../<?php echo $video['videofile']; ?>" type="video/mp4" />
	</video>
<?php
}
else
{
?>
		<script type="text/javascript" src="../jwplayer/jwplayer.js"></script>
		<script type="text/javascript">
				jwplayer("container").setup({
						flashplayer: "../jwplayer/player.swf",
						file: "../<?php echo $video['videofile']; ?>",
						width: 640,
						height: 480
				});
		</script>
<?php
}
?>
</div>
</center>
</body>


