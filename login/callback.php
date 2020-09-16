<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(!session_id()){
	session_start();
}

include_once 'fb_config.php';

$helper = $fb->getRedirectLoginHelper();

try {
  $accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  // When Graph returns an error
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  // When validation fails or other local issues
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

if (! isset($accessToken)) {
  if ($helper->getError()) {
    header('HTTP/1.0 401 Unauthorized');
    echo "Error: " . $helper->getError() . "\n";
    echo "Error Code: " . $helper->getErrorCode() . "\n";
    echo "Error Reason: " . $helper->getErrorReason() . "\n";
    echo "Error Description: " . $helper->getErrorDescription() . "\n";
  } else {
    header('HTTP/1.0 400 Bad Request');
    echo 'Bad request';
  }
  exit;
}

// Logged in
// echo '<h3>Access Token</h3>';
// var_dump($accessToken->getValue());

// The OAuth 2.0 client handler helps us manage access tokens
$oAuth2Client = $fb->getOAuth2Client();

// Get the access token metadata from /debug_token
$tokenMetadata = $oAuth2Client->debugToken($accessToken);
// echo '<h3>Metadata</h3>';
// var_dump($tokenMetadata);

// Validation (these will throw FacebookSDKException's when they fail)
try{
	$tokenMetadata->validateAppId($config['app_id']);
} catch(Facebook\FacebookSDKException $e){
	echo "<i style='color: red'>$e->getMessage()</i>";
}
// If you know the user ID this access token belongs to, you can validate it here
// $tokenMetadata->validateUserId('123');

$tokenMetadata->validateExpiration();

if (! $accessToken->isLongLived()) {
  // Exchanges a short-lived access token for a long-lived one
  try {
    echo "Short: ".$accessToken;
    $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
    echo "<hr>Long: ".$accessToken;
    exit;
  } catch (Facebook\Exceptions\FacebookSDKException $e) {
    echo "<p>Error getting long-lived access token: " . $e->getMessage() . "</p>\n\n";
    exit;
  }
}

try {
  // Returns a `Facebook\FacebookResponse` object
  $response = $fb->get('/me?fields=id, first_name, email, last_name, middle_name, address, picture',
      $accessToken);
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}
$user = $response->getGraphUser();

require_once('../server/global.php');
$conn = get_connection();

$id = $conn->real_escape_string($user['id'] );

$res = $conn->query("SELECT LocalId FROM UserLogin WHERE RemoteId='$id';") or die($conn->error);

$userId = -1;

if($res->num_rows == 0){
  $first_name = $conn->real_escape_string((string)$user['first_name']);
  $last_name = !isset($user['middle_name'])?'':(string)$user['middle_name'];
  $last_name = $last_name.(!isset($user['last_name']))?'':(string)$user['last_name'];
  $last_name = $conn->real_escape_string($last_name);
  $location = !isset($user['address'])?'':(string)$user['address'];
  $location = $conn->real_escape_string($location);
  $email = isset($user['email'])? $user['email'] : 'inalid@localhost';
  $email = $conn->real_escape_string($email);
  $userName = str_replace(" ", "-", strtolower($first_name.$last_name));
  $userName  = preg_replace("/[^A-Za-z0-9\-\.]/", '', $userName);
  $userName = $conn->real_escape_string($userName);
  $userProfileUrl = $user['picture']->getUrl();

  $conn->autocommit(false);
  
  $conn->query("INSERT INTO UserLogin (RemoteId) VALUES($id);") or die($conn->error);
  $userId = $conn->insert_id;

  $localPathUrl = "../resource/profileImages/".base64_encode($userId).'.jpeg';
  if(!file_exists($localPathUrl)){
    touch($localPathUrl);
  }
  $localPath = fopen($localPathUrl, "w") or die("Unable to open file at file");
  fwrite($localPath, file_get_contents($userProfileUrl));
  $localPathUrl = substr($localPathUrl, 2);
  $localPathUrl = $conn->real_escape_string($localPathUrl);

  $conn->query("INSERT INTO
          User (Id, FirstName, LastName, Location, UserName, Email, Picture)
          VALUES('$userId', '$first_name', '$last_name', '$location', '$userName', '$email', '$localPathUrl');
    ;") or die($conn->error);
  $conn->query("INSERT INTO UserTag (User, Tag) VALUES ($userId, (SELECT Id FROM Tags WHERE Name='askme' LIMIT 1))") or die($conn->error);

  $conn->commit();
  $conn->autocommit(true);
} else {
  $userId = $res->fetch_array()[0];
}

$_SESSION['userId'] = $userId;
$_SESSION['token'] = $accessToken->getValue();
$fb->setDefaultAccessToken($accessToken);

if(!getLoginStatus()){ // will set the more session variable;
  echo "Login Failed...";
  exit;
}
header("Location: /profile/".$_SESSION['userName']);

$conn->close();