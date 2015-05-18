<?php
$parts = ['message', 'file', 'line', 'code'];
$data  = [];
foreach ($parts as $part) {
    $data[$part] = $error[$part];
}
echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);