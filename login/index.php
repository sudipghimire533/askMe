<?php
if(!session_id()){
    session_start();
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../server/global.php';

if(getLoginStatus() == true){
    if(isset($_GET['taketo'])){
        header("Location: ".$_GET['taketo']."?apple=true");
        exit;
    }
    echo "You are already logged in..<a href='/home'>go home</a>";
    exit;
}

include_once 'fb_config.php';

$helper = $fb->getRedirectLoginHelper();
$permission = ['email'];
$loginUrl = $helper->getLoginUrl('http://localhost/login/callback.php', $permission);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help me for Homework</title>

    <link href='../global/global.css' type="text/css" rel="stylesheet" />
    <link href='./login.css' type="text/css" rel="stylesheet" />
</head>

<body>
    <div id='Login'>
        <div class='loginConatiner Container'>
            <div class='left'>
                <div class='imgContainer'>
                    <img src='./images/feature.jpg' alt='Featured Image' title='Sign in' />
                </div>
            </div>
            <div class='right'>
                <div>
                    <a href='<?php echo $loginUrl; ?>' class='facebook_login_btn login_btn'>Login With Facebook
                    </a>
                </div>
                <p class='terms'>
                    <a href='/terms'>Terms and Condition</a>
                </p>
            </div>
        </div>
    </div>
</body>

</html>
