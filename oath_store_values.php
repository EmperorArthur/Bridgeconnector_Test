<?php
// Store the access_token and instance_url for REST API calls

require_once 'helpers.php';
session_start();

// var_dump( $_GET );

// If something goes wrong
if( array_key_exists('error',$_GET) ){
    echo $_GET['error'];
} else
// Correct path
if( array_key_exists('access_token',$_GET) and array_key_exists('instance_url',$_GET) ) {
    $_SESSION['access_token'] = $_GET['access_token'];
    $_SESSION['instance_url'] = $_GET['instance_url'];
    redirect_to_url('/index.php');
} else
// Something has seriously gone wrong
{
    echo "WARNING:  Unexpected or no reply recieved!";
}
?>
