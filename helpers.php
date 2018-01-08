<?php
// PHP helpers including things like http requests, and OAuth locations

// WARNING:  This code doesn't deal with failure well.

require_once 'HTTP/Request2.php';

//A really simple webpage that re-directs the user to the provided URL.
//Useful for Oauth.
function redirect_to_url($url){
    echo "<html><head></head><body><script>window.location='{$url}'</script><a href='{$url}'>Click here to follow redirect</a></body></html>";
}

function request(string $url, string $method = HTTP_Request2::METHOD_GET, array $post_data = array(), array $header=array(), string $data =''){
    error_log('Sending request to : ' . $url);

    try {
        $request = new HTTP_Request2($url);
        $request->setHeader($header);
        $request->setMethod($method);
        if( $method == HTTP_Request2::METHOD_POST ){
            $request->addPostParameter($post_data);
        }
        if( isset($data) and $data != '' ){
            $request->setBody($data);
        }

        $response = $request->send();

        //200 is OK, 201 is created (for put requests), 204 is everything's OK, but we don't get any data back (Successful patch requests)
        if ($response->getStatus() == 200 or $response->getStatus() == 201 or $response->getStatus() == 204){
            return $response->getBody();
        } else if ($response->getStatus() == 302) {
            //Redirect user to appropriate page
            redirect_to_url($response->getHeader()['location']);
            return "";
        } else {
            echo 'Recieved status code: ' . $response->getStatus() . "<br>";
            echo 'Headers:<br><br>';
            var_dump( $response->getHeader() );
            echo 'Body:<br><br>';
            var_dump( $response->getBody() );
            return $response->getBody();
        }

    } catch (HTTP_Request2_Exception $e) {
        echo 'Error: ' . $e->getMessage();
        return "";
    }

}

?>
