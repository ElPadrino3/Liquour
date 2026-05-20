<?php
require_once 'Config/api_keys.php';
$url = 'https://generativelanguage.googleapis.com/v1beta/models?key=' . GEMINI_API_KEY;
$options = [
    'http' => ['ignore_errors' => true],
    'ssl' => ['verify_peer' => false, 'verify_peer_name' => false]
];
$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);
file_put_contents('models.json', $result);
?>
