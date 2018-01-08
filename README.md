# Bridgeconnector CRUD Test

This is a programming test for Bridgeconnector.
It implements an extremely basic CRUD interface for Salesforce.com.

Copyright Arthur Moore 2018, BSD 2 clause license


# Assumptions

* The user has the HTTP_Request2 PHP module installed
* Errors are very loud.  Though the response body is returned, the code also echoes the error code and message.
* `getBetweenDates` uses Salesforce's [DateTime format](https://developer.salesforce.com/docs/atlas.en-us.soql_sosl.meta/soql_sosl/sforce_api_calls_soql_select_dateformats.htm)
* `getBetweenDates` returns all account sObject fields
* `create` returns the created object on success, or an array containing the error code and message on failure
* 'update` returns the updated object on success on success, or an array containing the error code and message on failure


# Running the example

## Configuration

Before the example can be run, an app must be created within a salesforce instance.
See the [REST Tutorial][1] for how to do so.

Next, the index.php must be edited. Optionally unauthenticated_rest_test.php can also be edited.
The variable `$_SESSION['client_id']` must be set to the app's client id.


# Starting the server

The simplest method to run the example is to use PHP's built in web server.
`php -S localhost:8080`

Any server can be used as long as the callback URL for the Salesforce App points to "oath_callback.html".

# Oauth flow

User agent Oauth allows the app to access and change information without ever knowing the user's password.
See [here][2] for more information on how Salesforce does things.

* index.php: sets `client_id` and checks if `access_token` is set.  "Log In" button directs to ->
* oath_login.php: Sends an HTTP push request, asking for access using `client_id` callback url goes to ->
* oath_callback.html:  Takes the oath details in the URL (given as HTML anchors) and passes them as an HTTP get request to ->
* oath_store_values.php: Stores `access_token` or displays an error message.  Then passes back to "index.php"

# Useful Information:

[Salesforce REST Tutorial][1]

[Salesforce Quickstart Code](https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/quickstart_code.htm)

[Salesforce User Agent Oauth Flow][2]



[1]: (https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/intro_what_is_rest_api.htm)
[2]: (https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/intro_understanding_user_agent_oauth_flow.htm)
