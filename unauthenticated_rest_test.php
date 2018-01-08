<?php
// A simple unauthenticated test to make sure that everything works

require_once 'helpers.php';
session_start();

$_SESSION['instance_url'] = 'https://<your instance here>.salesforce.com';


$api_url = '/services/data/';

var_dump( json_decode( request($_SESSION['instance_url'] . $api_url, False, array(), array('Authorization' => "Bearer {$_SESSION['access_token']}") ) ) );

?>

