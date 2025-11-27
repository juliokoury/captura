<?php
header('Content-Type: application/json');
require_once '../config.php';

session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid data']);
    exit;
}

$id = $data['id'];

try {
    // Fetch lead data
    $stmt = $pdo->prepare("SELECT * FROM leads WHERE id = ?");
    $stmt->execute([$id]);
    $lead = $stmt->fetch();

    if (!$lead) {
        http_response_code(404);
        echo json_encode(['error' => 'Lead not found']);
        exit;
    }

    // Gemini AI Integration - Hardcoded
    $geminiApiKey = 'AIzaSyCv77d24ByVvUmTyJ0TMhn3Wt-1OoeUuO0';

    $aiResult = [
        'urgencia' => 'baixa',
                ]
            ]
        ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $response = curl_exec($ch);

    if ($response === false) {
        $aiResult['resumo'] = 'Erro CURL: ' . curl_error($ch);
    } else {
        $responseData = json_decode($response, true);

        if (isset($responseData['error'])) {
            $aiResult['resumo'] = 'Erro API Gemini: ' . ($responseData['error']['message'] ?? 'Desconhecido');
        } elseif (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
            $aiText = $responseData['candidates'][0]['content']['parts'][0]['text'];
            $aiText = str_replace(['```json', '```'], '', $aiText);
            $aiJson = json_decode($aiText, true);

            if ($aiJson) {
                $aiResult['urgencia'] = strtolower($aiJson['urgencia'] ?? 'baixa');
                $aiResult['tags_ai'] = $aiJson['tags_ai'] ?? [];
                $aiResult['resumo'] = $aiJson['resumo'] ?? '';
            } else {
                $aiResult['resumo'] = 'Erro ao ler JSON da IA.';
            }
        } else {
            $aiResult['resumo'] = 'Resposta inesperada da API.';
        }
    }
    curl_close($ch);

    // Normalize urgency
    $validUrgency = ['baixa', 'media', 'alta'];
    if (!in_array($aiResult['urgencia'], $validUrgency)) {
        if ($aiResult['urgencia'] == 'média')
            $aiResult['urgencia'] = 'media';
        else
            $aiResult['urgencia'] = 'baixa';
    }

    $tagsString = is_array($aiResult['tags_ai']) ? implode(', ', $aiResult['tags_ai']) : $aiResult['tags_ai'];

    $stmt = $pdo->prepare("UPDATE leads SET urgencia = ?, tags_ai = ?, resumo_ai = ?, status_kanban = ? WHERE id = ?");
    $stmt->execute([
        $aiResult['urgencia'],
        $tagsString,
        $aiResult['resumo'],
        $aiResult['urgencia'],
        $id
    ]);

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>