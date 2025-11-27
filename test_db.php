<?php
header('Content-Type: application/json');

$response = [
    'status' => 'checking',
    'env_vars' => [
        'DB_HOST' => getenv('DB_HOST'),
        'DB_NAME' => getenv('DB_NAME'),
        'DB_USER' => getenv('DB_USER'),
        'DB_PASS' => getenv('DB_PASS') ? '******' : '(empty)',
    ],
    'connection' => 'pending',
    'error' => null
];

try {
    require_once 'config.php';
    $response['connection'] = 'success';
    $response['status'] = 'ok';
    $response['message'] = 'Conexão com banco de dados realizada com sucesso!';
} catch (Exception $e) {
    $response['connection'] = 'failed';
    $response['status'] = 'error';
    $response['error'] = $e->getMessage();
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>