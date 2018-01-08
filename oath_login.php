<?php
// Send the request to authenticate via Oauth

require_once 'helpers.php';
session_start();

$login_url = 'https://login.salesforce.com/services/oauth2/authorize';
$oath_data = array(
    'response_type' => 'token',
    'client_id' => $_SESSION['client_id'],
    'redirect_uri' => 'http://localhost:8080/oath_callback.html'
    );

echo request($login_url, True, $oath_data);

?>
