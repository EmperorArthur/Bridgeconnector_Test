<?php
require_once 'helpers.php';
require_once 'crud.php';
session_start();

// var_dump( $_SESSION );

// Copy this from the created app
$_SESSION['client_id'] = '<Your Client Id Here>';

//Determine if logged in or not
//WARNING:  This does not check if the token is expired!
if( array_key_exists('access_token',$_SESSION) ){
    $log_in_status = "Logged In";
    $is_logged_in = True;
} else {
    $log_in_status = "Not logged in.  <button onclick=\"window.location.href='/oath_login.php'\">Log In</button>";
    $is_logged_in = False;
}

if( $is_logged_in and array_key_exists('instance_url',$_SESSION) ){
    $instance_url = $_SESSION['instance_url'];
} else {
    $instance_url = "You must log in to connect to an instance.";
    //Should never happen, but better safe than sorry
    $is_logged_in = False;
}

?>

<html>
<head>
</head>
<body>

<div>
    Oauth status: <?php echo $log_in_status ?>
</div>

<div>
    Salseforce Instance: <?php echo $instance_url ?>
</div>

<div>
    CRUD Test:
    <?php
    if( $is_logged_in ){
        $CRUD_test = new Salesforce_CRUD($_SESSION['instance_url'], $_SESSION['access_token']);
        echo "<div>getBetweenDates:<br>";
        var_dump ($CRUD_test->getBetweenDates('2018-01-01T17:36:58.000+0000','2019-01-01T17:36:58.000+0000') );
        echo "</div>";
        echo "<div>getAccountObjectById:<br>";
        var_dump ($CRUD_test->getAccountObjectById("0011N00001BnFyUQAV"));
        echo "</div>";
        echo "<div>create:<br>";
        $created = $CRUD_test->create( array('name' => 'test') );
        var_dump ( $created );
        echo "</div>";
        echo "<div>update:<br>";
        var_dump ( $CRUD_test->update($created->Id, array('phone' => '(000) 000-0000')) );
        echo "</div>";
    }
    ?>
</div>
</body>
</html>

