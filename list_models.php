<?php
header('Content-Type: application/json');

// Hardcoded key as requested
$key = 'AIzaSyCv77d24ByVvUmTyJ0TMhn3Wt-1OoeUuO0';
$url = "https://generativelanguage.googleapis.com/v1beta/models?key=" . $key;

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL check just in case
$response = curl_exec($ch);

if ($response === false) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Erro CURL: ' . curl_error($ch)
    ]);
} else {
    // Decode to check if it's an error from Google
    $json = json_decode($response, true);
    if (isset($json['error'])) {
        echo json_encode([
            'status' => 'api_error',
            'message' => $json['error']['message']
        ]);
    } else {
        echo $response;
    }
}
curl_close($ch);
?>