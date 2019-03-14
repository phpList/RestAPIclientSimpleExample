<?php
/**
 * A very simple example of  Rest Api client of phpList
 * @author Xheni Myrtaj
 **/

require __DIR__ . '/vendor/autoload.php';

$client = new \GuzzleHttp\Client();

//Please replace the following values with yours.
$loginname = 'admin';
$password = 'admin1234';
$base_uri = 'http://10.211.55.4:1994/app.php/api/v2';

try {
    $response = $client->request('POST', $base_uri.'/sessions', [
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
    echo 'Session key is: ' . $key.'<br><br>';
}

//Use session key as password for basic auth
$credentials = base64_encode($loginname . ':' . $key);

$listInfo = $client->get($base_uri.'/lists/1',
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

$members = $client->get($base_uri.'/lists/1/members',
    [
        'headers' => [
            'Authorization' => 'Basic ' . $credentials,
            'Content-Type' => 'application/json',
        ],
    ]);

if ($members->getBody()) {
    $membersResponse = json_decode($members->getBody(), true);

    echo 'Subcsribers data of '.$listInfoResponse['name'].':<br><br>';

    foreach ($membersResponse as $k => $val) {
        foreach ($val as $key => $value) {
            echo "$key : $value<br>";
        }
        echo '<br>';
    }
}
