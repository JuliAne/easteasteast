<?php
//import and some variables

require_once $modulePath.'/fb_sdk/autoload.php';


use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;

$appIDWatte = '1551776271736439';
$appSecretWatte = '356a031074a7cd524e85dafab4b5ea83';
$appIDMatthias = '1525312674403751';
$appSecretMatthias = '62345dfec3e154f7ffdeb43d92ce62fd';

$servername = $_SERVER['HTTP_HOST'];

if(strpos($servername, "www.")!=0) {
    $redirectURL = "http://www.".$servername.$_SERVER['REQUEST_URI'];
}
else {
    $redirectURL = "http://".$servername.$_SERVER['REQUEST_URI'];
}

//FB seems to have a problem with normal slashes. change them to '%2F' But at first after drupal/?.... >_> bullshit
$redirectURL = "http://localhost/?q=kuenstler%2Fmenu%2FStatistics";


//FB kram
if(version_compare(phpversion(), "5.4.0") != -1){
    if (session_status() == PHP_SESSION_NONE) {
      session_start();
    }
} else {
    if(session_id() == '') {
      session_start();
    }
}
//FB with AppID and AppSecret. Uhuuuuu, now i'm a hacker, right? Cause i know a passwort.. y'know?
FacebookSession::setDefaultApplication($appIDMatthias,$appSecretMatthias);

// login helper with redirect_uri
$helper = new FacebookRedirectLoginHelper( $redirectURL );
 
try {
  $session = $helper->getSessionFromRedirect();
} catch( FacebookRequestException $ex ) {
  // When Facebook returns an error
    echo "Error on FBSide: <br>" . $ex;
} catch( Exception $ex ) {
  // When validation fails or other local issues
    echo "Error on our side:<br>" . $ex;
}
 
// see if we have a session
if ( isset( $session ) ) {
    
// Logged in    
    
    $request = new FacebookRequest($session, 'GET', '/395972600559743'); // page id
    $response = $request->execute();
    $graphObject = $response->getGraphObject();
    $amountLikes = $graphObject->getProperty("likes");
    include __DIR__ . "/statistics/db/saveLikes.php";
    
    //save like-amount in database
    echo "Amount Likes: " . $graphObject->getProperty("likes");
    
    
    
} else {
  // show login url
  echo '<a href="' . $helper->getLoginUrl() . '">Login</a>';
}
