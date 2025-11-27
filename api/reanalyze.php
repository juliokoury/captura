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

    // Gemini AI Integration
    $geminiApiKey = getenv('GEMINI_API_KEY') ?: 'AIzaSyCv77d24ByVvUmTyJ0TMhn3Wt-1OoeUuO0';

    $aiResult = [
        'urgencia' => 'baixa',
        'tags_ai' => [],
        'resumo' => 'Reanálise falhou ou sem chave API.'
    ];

    if ($geminiApiKey && $geminiApiKey !== 'YOUR_GEMINI_API_KEY') {
        $prompt = "Você é um assistente de triagem de pacientes para uma clínica de ortopedia e medicina intervencionista da dor. REANÁLISE. Receba as respostas abaixo e devolva um JSON contendo: urgencia (baixa, média, alta), tags_ai (lista com insights), resumo (descrição curta do quadro do paciente). Responda apenas com JSON puro.\n\nDados do paciente:\nNome: {$lead['nome']}\nIdade: {$lead['idade']}\nLocal da dor: {$lead['local_dor']}\nTempo da dor: {$lead['tempo_dor']}";

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $geminiApiKey;

        $payload = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $prompt]
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

        if ($response) {
            $responseData = json_decode($response, true);
            if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                $aiText = $responseData['candidates'][0]['content']['parts'][0]['text'];
                $aiText = str_replace(['```json', '```'], '', $aiText);
                $aiJson = json_decode($aiText, true);

                if ($aiJson) {
                    $aiResult['urgencia'] = strtolower($aiJson['urgencia'] ?? 'baixa');
                    $aiResult['tags_ai'] = $aiJson['tags_ai'] ?? [];
                    $aiResult['resumo'] = $aiJson['resumo'] ?? '';
                }
            }
        }
        curl_close($ch);
    }

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