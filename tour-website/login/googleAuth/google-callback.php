<?php

require_once '../../../vendor/autoload.php';
session_start();

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$client = new Google_Client();
$client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
$client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
$client->setRedirectUri($_ENV['GOOGLE_REDIRECT']);
$client->addScope("email");
$client->addScope("profile");

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (!isset($token['error'])) {
        $client->setAccessToken($token['access_token']);

        $oauth2 = new Google_Service_Oauth2($client);
        $userInfo = $oauth2->userinfo->get();

        $_SESSION['user_type'] = 'google';
        $_SESSION['user_name'] = $userInfo->name;
        $_SESSION['user_email'] = $userInfo->email;
        $_SESSION['user_image'] = $userInfo->picture;

        // Database connection
        require_once '../../../includes/db.php';

        // Check if user already exists
        $email = $conn->real_escape_string($userInfo->email);
        $name = $conn->real_escape_string($userInfo->name);

        $result = $conn->query("SELECT * FROM clients WHERE email = '$email'");

        if ($result && $result->num_rows > 0) {
            // User exists, set the session with their client_id
            $client = $result->fetch_assoc();
            $_SESSION['client_id'] = $client['id'];
        } else {
            // User doesn't exist, insert new user
            $conn->query("INSERT INTO clients (name, email) VALUES ('$name', '$email')");
            $_SESSION['client_id'] = $conn->insert_id;
        }

        // Check if we should redirect to a specific page (like book now)
        $redirect = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : '../../index.php';
        unset($_SESSION['redirect_after_login']);

        header("Location: $redirect");
        exit();

    } else {
        $_SESSION['error'] = 'Login Failed!';
        header('Location: ../login.php');
        exit();
    }
} else {
    $_SESSION['error'] = 'Invalid login!';
    header('Location: ../login.php');
    exit();
}