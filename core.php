<?php 
/**
* Class contains all the 'core' processing
* @author University of Lincoln CS2 Group Project 02/2015
*/ 
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
set_include_path('/var/www/html/');
require_once('Managers/twitterHelper.php');
require_once('Managers/dbHelper.php');
class core
{
    public function __construct()
    {
        set_time_limit(0);
        $dbHelperObj = new dbHelper();
        $twitterHelperObj = new twitterHelper();
        //retrieve the next ID for processing
        $intHandleID = $dbHelperObj->getNextHandleID();
        echo '>>>Next Handle ID = '.$intHandleID.'<br />';
        //get the latest tweet/follower count
        $objHandleStats = $twitterHelperObj->getHandleIDObject($intHandleID);

        $intTweetCount = 0;
        $intFollowerCount = 0;

        try
        {
            $intTweetCount = $objHandleStats['statuses_count'];
            echo '>>>Tweet Count = '.$intTweetCount.'<br />';
            $intFollowerCount = $objHandleStats['followers_count'];
            echo '>>>Follower Count = '.$intFollowerCount.'<br />';
        }
        catch(Exception $e)
        {
            echo '>>>Exception: core.php ln: 35 - '.$e;
            die();
        }
        //update DB
        $dbHelperObj->updateStats($intHandleID, $intTweetCount, $intFollowerCount);
        echo '>>>tblHandles updated with user object<br />';
        //add followers to db
        $followerObj = $twitterHelperObj->getFollowerObjects($intHandleID, -1);
        if(isset($followerObj['ids']))
        {
          for ($i = 0; $i < count($followerObj['ids']); ++$i)
          {
              $intFollowerID = $followerObj['ids'][$i];
              $dbHelperObj->insertSingleTwitterID($intFollowerID, 0 ,0);
          }
          echo '>>>New rows inserted into tblHandles<br />';
        } 
        else
        {
            echo '>>>Exception: core.php ln: 55 - likely Twitter rate limit reached';
            die();
        }
        //process tweets
        $tweetObj = $twitterHelperObj->getTweetsArray($intHandleID, -1);
        if(isset($tweetObj))
        {
          $strPhonePattern = "#[0-9][0-9][0-9][0-9][0-9]\s*[0-9][0-9][0-9][0-9][0-9][0-9]#";
          $strUKPostcodePattern = '#^[A-Z]{1,2}[0-9R][0-9A-Z]? [0-9][ABD-HJLNP-UW-Z]{2}$#';
          $strEmailAddressPattern = '/[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+\.[a-zA-Z]{2,4}/';
          for ($i = 0; $i < count($tweetObj); ++$i)
          {
            $strTweet = $tweetObj[$i]['text'];
            $intTweetID = $tweetObj[$i]['id'];
            if(preg_match($strUKPostcodePattern, $strTweet))
            {
                 echo '>>>UK Postcode Found:<br />';
                 echo '>>>'.$strTweet.'<br />';
                 $dbHelperObj->insertTweetLocation($intHandleID, $intTweetID, 0);
            }
            if(preg_match($strPhonePattern, $strTweet))
            {
                 echo '>>>Phone Number Found:<br />';
                 echo '>>>'.$strTweet.'<br />';
                 $dbHelperObj->insertTweetLocation($intHandleID, $intTweetID, 1);
            }
            if(preg_match($strEmailAddressPattern, $strTweet))
            {
                 echo '>>>Email Address Found:<br />';
                 echo '>>>'.$strTweet.'<br />';
                 $dbHelperObj->insertTweetLocation($intHandleID, $intTweetID, 2);
            }
          }
        } else echo '>>>Exception: core.php ln: 75 - likely Twitter rate limit reached';

    }

}

new core();

?>