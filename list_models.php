<?php
$json = file_get_contents('models.json');
$data = json_decode($json, true);
if (isset($data['models'])) {
    foreach ($data['models'] as $model) {
        if (in_array('generateContent', $model['supportedGenerationMethods'])) {
            echo $model['name'] . "\n";
        }
    }
}
?>
