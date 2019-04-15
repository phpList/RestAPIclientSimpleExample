<?php
/**
 * A very simple example of  REST API client of phpList
 * @author Xheni Myrtaj
 **/

require __DIR__ . '/vendor/autoload.php';

$client = new \GuzzleHttp\Client();

//Please replace the following values with yours.
$loginname = 'admin';
$password = 'phplist';
$base_uri = 'http://example.com/lists/api/v2';

try {
    $response = $client->request('POST', $base_uri . '/sessions', [
        'form_params' => [
            'login_name' => $loginname,
            'password' => $password,
        ],
    ]);
} catch (\GuzzleHttp\Exception\GuzzleException $e) {
}

//get session key
if ($response->getBody()) {

    $obj = json_decode($response->getBody(), true);
    $key = $obj['key'];
    echo 'Session key is: ' . $key . '<br><br>';
}

//Use session key as password for basic auth
$credentials = base64_encode($loginname . ':' . $key);

// Get list info  where id=1
$listInfo = $client->get($base_uri . '/lists/1',
    [
        'headers' => [
            'Authorization' => 'Basic ' . $credentials,
            'Content-Type' => 'application/json',
        ],
    ]);

if ($listInfo->getBody()) {

    $listInfoResponse = json_decode($listInfo->getBody(), true);

    echo 'List Info: <br><br>';

    foreach ($listInfoResponse as $key => $value) {

        echo "$key : $value<br>";
    }
    echo '<br>';
}

//Get all subscribers where list id=1
$members = $client->get($base_uri . '/lists/1/members',
    [
        'headers' => [
            'Authorization' => 'Basic ' . $credentials,
            'Content-Type' => 'application/json',
        ],
    ]);

if ($members->getBody()) {

    $membersResponse = json_decode($members->getBody(), true);

    echo 'Subscribers of ' . $listInfoResponse['name'] . ':<br><br>';

    foreach ($membersResponse as $k => $val) {
        foreach ($val as $key => $value) {
            echo "$key : $value<br>";
        }
        echo '<br>';
    }
}

// Add a new subscriber
try {
    $subscriberRequest = $client->request('POST', $base_uri . '/subscribers',
        [
            'headers' => [
                'Authorization' => 'Basic ' . $credentials,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'email' => 'restapi@example.com',
                'confirmed' => true,
                'blacklisted' => false,
                'html_email' => true,
                'disabled' => false,
            ],
        ]
    );
} catch (\GuzzleHttp\Exception\GuzzleException $e) {
}
$subscriberRequest->getBody();
