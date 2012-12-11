<?php
require_once "config.php";

class Database
{
	private $dbLink;
	function __construct()
	{
		global $databaseInfo;
		
		$this->dbLink = mysql_connect($databaseInfo['host'], $databaseInfo['user'], $databaseInfo['password']);
		mysql_select_db($databaseInfo['database'], $this->dbLink);
	}
	
	function __destruct()
	{
		mysql_close($this->dbLink);
	}
	
	function addVideoToQueue($videourl, $owner)
	{
		global $expireTime;
		$expiresAt = strtotime($expireTime);
		$query = mysql_query("INSERT INTO `videos` (videourl, expires, owner, status) VALUES('".mysql_real_escape_string($videourl)."', ".$expiresAt.", '".mysql_real_escape_string($owner)."', ".DOWNLOAD_QUEUE.")", $this->dbLink);
		if ( !$query )
			return -1;
		return mysql_insert_id($this->dbLink);
	}
	
	function resetVideoExpire($videourl)
	{
		global $expireTime;
		$expiresAt = strtotime($expireTime);
		
		return mysql_query("UPDATE `videos` SET `expires` = ".$expiresAt." WHERE `videourl` = '".mysql_real_escape_string($videourl)."'");
	}
	
	function isVideoInDatabase($videourl)
	{
		$result = mysql_query("SELECT * FROM `videos` WHERE `videourl` = '".mysql_real_escape_string($videourl)."'", $this->dbLink);
		if ( mysql_num_rows($result) != 1 )
			return false;
		return mysql_result($result, 0, "id");
	}
	
	function setVideoStatus($id, $status)
	{
		$id = intval($id);
		if ( $id == 0 )
			return false;
			
		return mysql_query("UPDATE `videos` SET `status` = ".$status." WHERE `id` = ".$id."");
	}
	
	function removeVideo($id)
	{
		$id = intval($id);
		if ( $id == 0 )
			return false;
		unlink("./".$this->getVideoFilename($id));
		mysql_query("DELETE FROM `videos` WHERE `id` = ".$id);
		return true;
	}
	
	function getVideos()
	{
		$result = mysql_query("SELECT * FROM `videos`");
		$output = array();
		for ( $i = 0; $i < mysql_num_rows($result); $i++ )
		{
			$output[] = mysql_fetch_array($result, MYSQL_ASSOC);
		}
		return $output;
	}
	
	function getVideo($id)
	{
		$id = intval($id);
		if ( $id == 0 )
			return false;
		$result = mysql_query("SELECT * FROM `videos` WHERE `id` = ".$id);
		return mysql_fetch_array($result, MYSQL_ASSOC);
	}
	
	function getVideoFilename($id)
	{
		$id = intval($id);
		if ( $id == 0 )
			return false;
		$result = mysql_query("SELECT * FROM `videos` WHERE `id` = ".$id);
		$result = mysql_fetch_array($result, MYSQL_ASSOC);
		return $result['videofile'];
	}
	
	function setVideoFilename($id, $filename)
	{
		$id = intval($id);
		if ( $id == 0 )
			return false;
			
		return mysql_query("UPDATE `videos` SET `videofile` = '".mysql_real_escape_string($filename)."' WHERE `id` = ".$id."");
	}
}

$database = new Database();

