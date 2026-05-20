<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'Config/api_keys.php';

$url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash:generateContent?key=' . GEMINI_API_KEY;

$requestData = [
    'contents' => [
        [
            'role' => 'user',
            'parts' => [
                ['text' => 'Hola']
            ]
        ]
    ]
];

$options = [
    'http' => [
        'header'  => "Content-type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($requestData),
        'ignore_errors' => true // To get the actual HTTP response code even if it's 400
    ],
    'ssl' => [
        'verify_peer' => false, // often needed on WAMP for HTTPS
        'verify_peer_name' => false
    ]
];

$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

echo "Resultado:\n";
var_dump($result);
if ($result === false) {
    echo "Error:\n";
    print_r(error_get_last());
}
?>
