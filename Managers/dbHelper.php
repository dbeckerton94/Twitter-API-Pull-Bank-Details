<?php
/**
* Class contains database helper functions
* inherits dbConnectionManager
* @author University of Lincoln CS2 Group Project 02/2015
*/
require_once('dbConnectionManager.php');
class dbHelper extends dbConnectionManager
{
	private static $dbConn = null;
    private static $dbObj = null;
	public function __construct()
	{
		try 
		{
			self::$dbConn = parent::getConnection();
			self::$dbObj = new pdo(self::$dbConn);
			self::$dbObj->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			die();
		}
	}
	/**
	* updates the follower count and tweet count for a given handle ID
	* @param $intHandleID = (primary key) of the row to update
	* @param $intFollowerCount = the number of followers to update
	* @param $intTweetCount = the number of tweets to update
	**/
	public function updateStats($intHandleID, $intFollowerCount, $intTweetCount)
	{
		try
		{
			$query = self::$dbObj->prepare('UPDATE "tblHandles" SET "intTweetCount" = :intTweetCount, "intFollowerCount" = :intFollowerCount, "tsLastUpdated" = now() WHERE "intHandleID" = :intHandleID');
			$query->bindParam(':intTweetCount', $intTweetCount);
			$query->bindParam(':intFollowerCount', $intFollowerCount);
			$query->bindParam(':intHandleID', $intHandleID);
			$query->execute();
		}
		catch (PDOException $e) 
		{
			echo $e;
		}
	}
	/**
	* Inserts a single row into tblHandles
	* @param $intHandleID = the numeric id of the handle being inserted
	* @param $intTweetCount = the number of tweets the handle has at time of insert
	*/
	public function insertSingleTwitterID($intHandleID, $intTweetCount, $intFollowerCount)
	{
		if(!self::checkIDExists($intHandleID))
		{
			$query = self::$dbObj->prepare('INSERT INTO "tblHandles"("intHandleID","intTweetCount","intFollowerCount") VALUES(:intHandleID,:intTweetCount,:intFollowerCount)');
			$query->bindParam(':intHandleID', $intHandleID);
			$query->bindParam(':intTweetCount', $intTweetCount);
			$query->bindParam(':intFollowerCount', $intFollowerCount);
			$query->execute();
		}
	}
	/**
	* Returns true or false if a handle already exists in the DB
	* @param $intHandleID = the handle ID to check
	* @return boolFound = a boolean value if he handle exists already
	**/
	public function checkIDExists($intHandleID)
	{
		$query = self::$dbObj->prepare('SELECT "intHandleID" FROM "tblHandles" WHERE "intHandleID" = '.$intHandleID);
		$query->execute();
		$result = $query->fetchAll();
		if(isset($result[0][0])) return true;
		else return false;
	}
	/**
	* Returns the next handle ID to process based on last updated date.
	* @return intHandleID = a single integer of the handle ID
	**/
	public function getNextHandleID()
	{
		$query = self::$dbObj->prepare('SELECT "intHandleID" FROM "tblHandles" ORDER BY "tsLastUpdated" ASC LIMIT 1');
		$query->execute();
		$result = $query->fetchAll();
		if(isset($result[0][0])) return $result[0][0];
		else return null;
	}
	/**
	* Inserts a single row into tblTweets
	* this table keeps a reference to the tweet and user containing personal infomation
	* @param $intHandleID = the numeric ID of the handle being inserted
	* @param $intTweetID = the numeric ID of the tweet being inserted
	* @param $intInfoTypeID = the numeric ID pointing to a row in tblInfoFound - this translates IDS (1,2,3 etc) into strings (address, phone number etc)
	*/
	public function insertTweetLocation($intHandleID, $intTweetID, $intInfoTypeID)
	{
		/** $intInfoTypeID:
		* 0 = Postcode
		* 1 = Phone Number
		* 2 = email address
		**/
		$query = self::$dbObj->prepare('INSERT INTO "tblTweets"("intHandleID","intTweetID","intInfoTypeID") VALUES(:intHandleID,:intTweetID,:intInfoTypeID)');
		$query->bindParam(':intHandleID', $intHandleID);
		$query->bindParam(':intTweetID', $intTweetID);
		$query->bindParam(':intInfoTypeID', $intInfoTypeID);
		$query->execute();
	}
}
?>