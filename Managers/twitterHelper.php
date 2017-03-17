<?php
/**
* Class contains twitter api helper functions
* @author University of Lincoln CS2 Group Project 02/2015
*/
require_once('TwitterAPIExchange.php'); //get it from https://github.com/J7mbo/twitter-api-php
class twitterHelper 
{
	private static $intFollowerLimit;
	private static $intTweetLimit;
	private static $arrSettings;
	public function __construct()
	{
		self::$arrSettings = array(
			'oauth_access_token' => "CODE",
			'oauth_access_token_secret' => "CODE",
			'consumer_key' => "CODE",
			'consumer_secret' => "CODE"
		);
		//r = request, m = minutes
		//maximum 5000, limit 15 r/15 m
		self::$intFollowerLimit = 500;
		//maximum 200, limit 180 r/15 m
		self::$intTweetLimit = 200;
	}
	/**
	* retrieves the twitter user objects of a given Handle ID's followers
	* limited by twitter to 5000, limited by us to 500
	* @param $intHandleID = the numeric ID of the handle to retrieve the results for
	* @param $intCursor = an integer value of which page of [5000 or less] followers to start processing at
	* @return $objFollowers = an object of handle IDS
	**/
	public function getFollowerObjects($intHandleID, $intCursor)
	{
		$followerObj = self::accessAPI('https://api.twitter.com/1.1/followers/ids.json', '?user_id='.$intHandleID.'&cursor='.$intCursor.'&count='.self::$intFollowerLimit);
		return $followerObj;
	}
	/**
	* retrieves the tweets of a given Handle ID
	* limited by twitter to 200 tweets/request
	* @param $intHandleID = the numeric ID of the handle to retrieve the results for
	* @param $intCursor = an integer value of which page of [200] tweets to start processing at
	* @return $tweetObj = an array of tweets
	**/
	public function getTweetsArray($intHandleID, $intCursor)
	{
		$tweetObj = self::accessAPI('https://api.twitter.com/1.1/statuses/user_timeline.json', '?user_id='.$intHandleID.'&count='.self::$intTweetLimit.'&cursor='.$intCursor.'');
		return $tweetObj;
	}
	/**
	* retrieves the user object (from Twitter) for a given handle id
	* @param $intHandleID = the handle ID to return data for
	* @return $handleObj = the twitter handle object
	**/
	public function getHandleIDObject($intHandleID)
	{
		$tweetObj = self::accessAPI('https://api.twitter.com/1.1/users/show.json', '?user_id='.$intHandleID);
		return $tweetObj;
	}
	/**
	* fetches the data and returns a json decoded object
	* @param $strUrl = the twitter api url to call
	* @param $strGetField = the parameters to append to the url
	* @return $dataObj = the json decoded object
	**/
	private function accessAPI($strUrl, $strGetField)
	{
		$twitterObj = new TwitterAPIExchange(self::$arrSettings);
		$responseObj = $twitterObj->setGetfield($strGetField)
		    ->buildOauth($strUrl, 'GET')
		    ->performRequest();

		$dataObj = json_decode($responseObj, true);
		return $dataObj;
	}
}
?>