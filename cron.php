<?php
require_once "config.php";
require_once "db.php";

if ( !is_dir("videos") )
	mkdir("videos");


function _log($text)
{
	echo $text."\n";
	//file_put_contents("YouTubeDownload.log", $text."\n", FILE_APPEND);
}

function getFilenameFromOutput($output)
{
	$lines = explode("\n", $output);
	foreach ( $lines as $line )
	{
		$words = explode(" ", $line);
		if ( count($words) == 3 && $words[0] == "[download]")
			return $words[2];
	}
	return "";
}

_log("Starting YouTube Downloader...");

if ( file_exists("youtube.lock") )
{
	_log("A downloader process is already running! Remove youtube.lock if the previous process crashed.");
	die();
}
else 
	touch("youtube.lock");

$videos = $database->getVideos();
foreach ( $videos as $video )
{
	if ( $video['status'] == DOWNLOAD_QUEUE )
	{
		$database->setVideoStatus($video['id'], DOWNLOAD_INPROGRESS);
		_log("Starting download of video ".$video['id']);
		$mp4FormatID = -1;
		
		$video['videourl'] = str_replace("\"", "", $video['videourl']);
		$video['videourl'] = str_replace("\\", "", $video['videourl']);
		if ( strstr($video['videourl'], "youtube.com") )
		{
			$formats = shell_exec("./youtube-dl/youtube-dl --list-formats \"".$video['videourl']."\"");
			$formats = explode("\n", $formats);
			$mp4FormatID = -1;
			foreach ( $formats as $format )
			{
				$thisformat = explode(":", $format);
				if ( array_key_exists(1, $thisformat) )
				{
					$thisID = trim($thisformat[0]);
					if ( strstr($thisformat[1], "mp4") && $thisID < 22 )
					{
						$mp4FormatID = $thisID;
						_log("Using mp4 for video ".$video['id']);
						break;
					}
				}
			}
		}
		else
			$mp4FormatID = -2;
		sleep(1); // small break between requests
		$result = "";
		if ( $mp4FormatID == -1) // YT flv download, medium quality
		{
			_log("Using flv for video ".$video['id']);
			$result = shell_exec("./youtube-dl/youtube-dl --max-quality 35 -o \"videos/".$video['id'].".%(ext)s\" \"".$video['videourl']."\"");
			
		}
		else if ( $mp4FormatID == -2 ) // Everything but YT
		{
			_log("Using default for video ".$video['id']);
			$result = shell_exec("./youtube-dl/youtube-dl -o \"videos/".$video['id'].".%(ext)s\" \"".$video['videourl']."\"");
		}
		else // YT mp4 download
			$result = shell_exec("./youtube-dl/youtube-dl --format ".$mp4FormatID." -o \"videos/".$video['id'].".%(ext)s\" \"".$video['videourl']."\"");

		$filename = getFilenameFromOutput($result);
		if ( file_exists($filename) )
		{
			$database->setVideoFilename($video['id'], $filename);	
			$database->setVideoStatus($video['id'], DOWNLOAD_FINISHED);
		}
		else
			$database->setVideoStatus($video['id'], DOWNLOAD_FAILED);
			
			
		_log("Finished download of video ".$video['id']);
	}
	else if ( $video['status'] == DOWNLOAD_FINISHED )
	{
		if ( $video['expires'] < time() )
		{
			$database->removeVideo($video['id']);
			_log("Video ".$video['id']." has expired. Removing.");
		}
	}
}
unlink("youtube.lock");
_log("Exiting");
?>

