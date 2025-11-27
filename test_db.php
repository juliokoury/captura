$response['error'] = $e->getMessage();
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>