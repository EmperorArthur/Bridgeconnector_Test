<?php


// Operates on the Accounts sObject
interface CRUD {
    /**
     * @param string $start
     * @param string $end
     * @return array
     */
    public function getBetweenDates(string $start, string $end);

    /**
     * @param array $attributes
     * @return array
     */
    public function create(array $attributes);

    /**
     * @param string $id
     * @param array $attributes
     * @return mixed
     */
    public function update(string $id, array $attributes);
}

class Salesforce_CRUD implements CRUD {

    // See __construct for what these two do
    private $instance_url;
    private $access_token;

    /**
     * @param string $instance_url A URL pointing to the Salesforce instance to access/modify
     * @param string $access_token A valid Oauth Access token.  So Salesforce knows who we are, and that we are authorized.
     */
    public function __construct(string $instance_url, string $access_token){
        $this->instance_url = $instance_url . '/services/data/v41.0/';
        $this->access_token = $access_token;
    }

    private function salesforce_get(string $api_url){
        return json_decode( request($this->instance_url . $api_url, HTTP_Request2::METHOD_GET, '', array('Authorization' => "Bearer {$this->access_token}") ) );
    }

    function salesforce_put($api_url, $data){
        return json_decode( request($this->instance_url . $api_url, HTTP_Request2::METHOD_POST, $data, array('Authorization' => "Bearer {$this->access_token}", 'Content-Type' => 'application/json') ) );
    }

    function salesforce_patch($api_url, $data){
        return json_decode( request($this->instance_url . $api_url, 'PATCH', $data, array('Authorization' => "Bearer {$this->access_token}", 'Content-Type' => 'application/json') ) );
    }


    function salesforce_query(string $query){
        return $this->salesforce_get( 'query?q=' . str_replace(' ', '+', $query) );
    }

    // Unfortunately, the SOQL language used for Salesforce REST API doesn't allow the `*` selector.
    // This function is used instead to get a full list of all field names for a given object
    private function getSobjectFields(string $objectName){
        $describe_object = $this->salesforce_get( "sobjects/{$objectName}/describe" );

        //For use with array_reduce.
        function _get_field_name( $initial, $field){
            if ( isset($initial) and $initial != '' ){
                $initial = $initial . ", " . $field->name;
            } else {
                $initial = $field->name;
            }
            return $initial;
        }

        //Something went wrong.
        if ( !isset($describe_object->fields) ){
//             throw new Exception("Unable to obtain field names!");
            return "";
        }

        $fields = array_reduce($describe_object->fields, '_get_field_name');
        return $fields;
    }

    public function getAccountObjectById(string $id){
        return $this->salesforce_get( "sobjects/Account/{$id}" );
    }

    public function getBetweenDates(string $start, string $end){
        $start = urlencode($start);
        $end = urlencode($end);

        $fields = $this->getSobjectFields('Account');
        $result = $this->salesforce_query( "SELECT {$fields} from Account WHERE CreatedDate >= {$start}  AND CreatedDate <= {$end}" );

        if ( isset($result->records) ){
            $records = $result->records;
        } else {
            //Something went wrong, so just give the full result
            return $result;
        }

        // If we didn't get all the data
        while ( isset($result->nextRecordsUrl) ){
            $result = $this->salesforce_get ( $result->nextRecordsUrl );
            // TODO:  Handle failure at this point
            $records = array_merge($records, $result->records);
        }

        return $records;
    }

    public function create(array $attributes){
        $data= json_encode( $attributes );
        $result = $this->salesforce_put("sobjects/Account/", $data);
        if (isset($result->success) ){
            return $this->getAccountObjectById($result->id);
        }else{
            return $result;
        }
    }
    public function update(string $id, array $attributes){
        $data= json_encode( $attributes );
        $result = $this->salesforce_patch("sobjects/Account/{$id}", $data);
        // We don't actually get any data back on success (technically it sends a 204, but we can't see that here)
        // The server only sends data back when things go wrong
        if ( isset($result) ){
            return $result;
        } else {
            return $this->getAccountObjectById($id);
        }
    }
}

?>
