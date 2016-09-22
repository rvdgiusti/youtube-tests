<?php

// Defining

require_once __DIR__.'/../../gplus-lib/vendor/autoload.php';
require_once __DIR__.'/config.php';

session_start();

// Initialization

$client = new Google_Client();
$client->setClientId(CLIENT_ID);
$client->setClientSecret(CLIENT_SECRET);
$client->setRedirectUri(REDIRECT_URI);
$client->setScopes(array(
     'https://www.googleapis.com/auth/plus.login',
     'profile',
     'email',
     'openid',
));

$plus = new Google_Service_Plus($client);

// Actual process

if(isset($_REQUEST['logout'])) {
    session_unset();
}

if(isset($_GET['code'])) {
    $client->authenticate($_GET['code']);
    $_SESSION['access_token'] = $client->getAccessToken();
    $redirect = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
    header('Location:'.filter_var($redirect,FILTER_SANITIZE_URL));
}


if(isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $client->setAccessToken($_SESSION['access_token']);
    $me = $plus->people->get('me');

    $id = $me['id'];
    $name = $me['displayName'];
    $email = $me['emails'][0]['value'];
    $profile_image_url = $me['image']['url'];
    $cover_image_url = $me['cover']['coverPhoto']['url'];
    $profile_url = $me['url'];
    print_r($_SESSION);

} else {
    $authUrl = $client->createAuthUrl();
}
?>

<div>
    <?php
    if(isset($authUrl)) {
	echo "<a class='login' href='" . $authUrl . "'><img src='../../gplus-lib/signin_button.png' height='50px'/>";
    } else {
	print "ID: {$id} <br>";
	print "Name: {$name} <br>";
	print "Email: {$email} <br>";
	print "Image: <img src='{$profile_image_url}' alt='photo'/><br>";
	echo "<a href='index.php'>Voltar</a><br>";
	echo "<a class='logout' href='?logout'><button>Logout</button></a>";
    }
    ?>
</div>	
